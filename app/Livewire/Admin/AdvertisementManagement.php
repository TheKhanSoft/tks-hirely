<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Advertisement;
use App\Models\AdvertisementDateTrack;
use App\Models\Campaign;
use App\Livewire\Traits\WithDynamicListing;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdvertisementManagement extends Component
{
    use WithDynamicListing;

    // Filters
    public $yearFilter = '';
    public $statusFilter = 'ALL'; // ALL, ACTIVE, EXTENDED, EXPIRED, INACTIVE

    // Create / Edit Modal Properties
    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingAdvertisementId = null;

    public $advertisement_no = '';
    public $title = '';
    public $year;
    public $details = '';
    public $valid_from;
    public $valid_to;
    public $newspaper_reference = '';
    public $is_active = true;

    // Extend Validity / Issue Corrigendum Modal
    public $showExtendModal = false;
    public $selectedAdvertisementForExtend = null;
    public $extend_new_valid_to;
    public $extend_corrigendum_no = '';
    public $extend_reason = '';
    public $extend_sync_jobs = true;

    // Date Tracking History Modal
    public $showHistoryModal = false;
    public $selectedAdvertisementForHistory = null;
    public $dateTracks = [];

    // Jobs under Advertisement Modal
    public $showJobsModal = false;
    public $selectedAdvertisementForJobs = null;
    public $advertisedJobs = [];

    protected function rules()
    {
        return [
            'advertisement_no' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'year' => 'required|integer|min:2000|max:2100',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after_or_equal:valid_from',
            'newspaper_reference' => 'nullable|string|max:255',
            'details' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function mount()
    {
        abort_unless(Auth::check() && (
            Auth::user()->can('view-campaign') ||
            Auth::user()->hasAnyRole(['super-admin', 'registrar'])
        ), 403);

        $this->year = (int)date('Y');
        $this->valid_from = now()->format('Y-m-d\TH:i');
        $this->valid_to = now()->addDays(30)->format('Y-m-d\TH:i');
    }

    public function updatingYearFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->reset([
            'advertisement_no',
            'title',
            'details',
            'newspaper_reference',
            'editingAdvertisementId',
        ]);

        $this->year = (int)date('Y');
        $this->valid_from = now()->format('Y-m-d\TH:i');
        $this->valid_to = now()->addDays(30)->format('Y-m-d\TH:i');
        $this->is_active = true;
        $this->resetErrorBag();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetErrorBag();
    }

    public function createAdvertisement()
    {
        $this->validate();

        $ad = Advertisement::create([
            'advertisement_no' => trim($this->advertisement_no),
            'title' => trim($this->title),
            'year' => (int)$this->year,
            'details' => trim($this->details),
            'valid_from' => Carbon::parse($this->valid_from),
            'valid_to' => Carbon::parse($this->valid_to),
            'newspaper_reference' => trim($this->newspaper_reference),
            'is_active' => $this->is_active,
            'created_by' => Auth::id(),
        ]);

        session()->flash('message', "Advertisement '{$ad->advertisement_no}' published successfully.");
        $this->closeCreateModal();
    }

    public function openEditModal($adId)
    {
        $ad = Advertisement::findOrFail($adId);
        if (($ad->is_expired || !$ad->is_active) && !Auth::user()->hasRole('super-admin')) {
            session()->flash('error', 'Security Restriction: Expired or inactive advertisements are locked for editing. Modifications require Super Admin authorization.');
            return;
        }

        $this->editingAdvertisementId = $ad->id;
        $this->advertisement_no = $ad->advertisement_no;
        $this->title = $ad->title;
        $this->year = $ad->year;
        $this->details = $ad->details;
        $this->valid_from = $ad->valid_from ? $ad->valid_from->format('Y-m-d\TH:i') : '';
        $this->valid_to = $ad->valid_to ? $ad->valid_to->format('Y-m-d\TH:i') : '';
        $this->newspaper_reference = $ad->newspaper_reference;
        $this->is_active = (bool)$ad->is_active;

        $this->resetErrorBag();
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingAdvertisementId = null;
        $this->resetErrorBag();
    }

    public function updateAdvertisement()
    {
        $this->validate();

        $ad = Advertisement::findOrFail($this->editingAdvertisementId);
        if (($ad->is_expired || !$ad->is_active) && !Auth::user()->hasRole('super-admin')) {
            abort(403, 'Unauthorized: Expired advertisements can only be modified by Super Admin.');
        }

        $ad->update([
            'advertisement_no' => trim($this->advertisement_no),
            'title' => trim($this->title),
            'year' => (int)$this->year,
            'details' => trim($this->details),
            'valid_from' => Carbon::parse($this->valid_from),
            'valid_to' => Carbon::parse($this->valid_to),
            'newspaper_reference' => trim($this->newspaper_reference),
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', "Advertisement '{$ad->advertisement_no}' updated successfully.");
        $this->closeEditModal();
    }

    public function toggleAdvertisementStatus($adId)
    {
        $ad = Advertisement::findOrFail($adId);

        if ($ad->is_expired && !$ad->is_active && !Auth::user()->hasRole('super-admin')) {
            session()->flash('error', 'Security Policy: Cannot reactivate an expired advertisement directly. Please issue a Date Extension / Corrigendum first.');
            return;
        }

        $ad->is_active = !$ad->is_active;
        $ad->save();

        session()->flash('message', "Advertisement '{$ad->advertisement_no}' status toggled to " . ($ad->is_active ? 'ACTIVE' : 'INACTIVE') . ".");
    }

    public function openExtendModal($adId)
    {
        $this->selectedAdvertisementForExtend = Advertisement::with('campaigns')->findOrFail($adId);
        $currentValidTo = $this->selectedAdvertisementForExtend->valid_to ?: now();
        $this->extend_new_valid_to = $currentValidTo->copy()->addDays(15)->format('Y-m-d\TH:i');
        $this->extend_corrigendum_no = 'Corrigendum No. ' . ($this->selectedAdvertisementForExtend->dateTracks()->count() + 1);
        $this->extend_reason = '';
        $this->extend_sync_jobs = true;
        $this->resetErrorBag();
        $this->showExtendModal = true;
    }

    public function closeExtendModal()
    {
        $this->showExtendModal = false;
        $this->selectedAdvertisementForExtend = null;
        $this->resetErrorBag();
    }

    public function extendAdvertisementValidity()
    {
        $this->validate([
            'extend_new_valid_to' => 'required|date|after:' . ($this->selectedAdvertisementForExtend->valid_to ? $this->selectedAdvertisementForExtend->valid_to->toDateTimeString() : now()->toDateTimeString()),
            'extend_reason' => 'required|string|min:5|max:1000',
            'extend_corrigendum_no' => 'nullable|string|max:100',
        ], [
            'extend_new_valid_to.after' => 'The extended date must be later than the current validity closing date.',
            'extend_reason.required' => 'Please provide an administrative reason or official notification reference for the extension.',
        ]);

        $track = $this->selectedAdvertisementForExtend->extendValidity(
            $this->extend_new_valid_to,
            $this->extend_reason,
            $this->extend_corrigendum_no,
            Auth::id(),
            $this->extend_sync_jobs
        );

        session()->flash('message', "Validity of Advertisement '{$this->selectedAdvertisementForExtend->advertisement_no}' extended to {$track->extended_valid_to->format('d M Y, h:i A')}. Extension logged in database.");
        $this->closeExtendModal();
    }

    public function openHistoryModal($adId)
    {
        $this->selectedAdvertisementForHistory = Advertisement::with(['dateTracks.extendedBy', 'creator'])->findOrFail($adId);
        $this->dateTracks = $this->selectedAdvertisementForHistory->dateTracks;
        $this->showHistoryModal = true;
    }

    public function closeHistoryModal()
    {
        $this->showHistoryModal = false;
        $this->selectedAdvertisementForHistory = null;
        $this->dateTracks = [];
    }

    public function openJobsModal($adId)
    {
        $this->selectedAdvertisementForJobs = Advertisement::with(['campaigns.applications'])->findOrFail($adId);
        $this->advertisedJobs = $this->selectedAdvertisementForJobs->campaigns;
        $this->showJobsModal = true;
    }

    public function closeJobsModal()
    {
        $this->showJobsModal = false;
        $this->selectedAdvertisementForJobs = null;
        $this->advertisedJobs = [];
    }

    public function exportCsv()
    {
        $headers = ['Advertisement No', 'Title', 'Year', 'Valid From', 'Valid To', 'Status', 'Jobs Count', 'Extensions Count', 'Newspaper Reference'];
        $ads = Advertisement::withCount(['campaigns', 'dateTracks'])->orderBy('year', 'desc')->get();
        $rows = [];

        foreach ($ads as $ad) {
            $rows[] = [
                $ad->advertisement_no,
                $ad->title,
                $ad->year,
                $ad->valid_from?->format('Y-m-d H:i') ?: '',
                $ad->valid_to?->format('Y-m-d H:i') ?: '',
                $ad->status_badge['label'],
                $ad->campaigns_count,
                $ad->date_tracks_count,
                $ad->newspaper_reference,
            ];
        }

        return $this->streamCsvDownload('advertisements-' . date('Y-m-d') . '.csv', $headers, $rows);
    }

    public function render()
    {
        $query = Advertisement::withCount(['campaigns', 'dateTracks'])->with('creator');

        if (!empty($this->search)) {
            $s = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('advertisement_no', 'like', $s)
                  ->orWhere('title', 'like', $s)
                  ->orWhere('newspaper_reference', 'like', $s)
                  ->orWhere('details', 'like', $s);
            });
        }

        if (!empty($this->yearFilter)) {
            $query->where('year', (int)$this->yearFilter);
        }

        if ($this->statusFilter === 'ACTIVE') {
            $query->where('is_active', true)->where('valid_to', '>=', now());
        } elseif ($this->statusFilter === 'EXTENDED') {
            $query->whereHas('dateTracks');
        } elseif ($this->statusFilter === 'EXPIRED') {
            $query->where('valid_to', '<', now());
        } elseif ($this->statusFilter === 'INACTIVE') {
            $query->where('is_active', false);
        }

        $advertisements = $query->latest('valid_to')->paginate($this->perPage);

        $availableYears = Advertisement::distinct()->orderBy('year', 'desc')->pluck('year')->filter()->values();
        if ($availableYears->isEmpty()) {
            $availableYears = collect([(int)date('Y')]);
        }

        $stats = [
            'total' => Advertisement::count(),
            'active' => Advertisement::where('is_active', true)->where('valid_to', '>=', now())->count(),
            'extended' => Advertisement::whereHas('dateTracks')->count(),
            'totalJobs' => Campaign::whereNotNull('advertisement_id')->count(),
        ];

        return view('livewire.admin.advertisement-management', [
            'advertisements' => $advertisements,
            'availableYears' => $availableYears,
            'stats' => $stats,
        ])->layout('layouts.app');
    }
}
