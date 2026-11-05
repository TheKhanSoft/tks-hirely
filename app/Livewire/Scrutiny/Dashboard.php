<?php

namespace App\Livewire\Scrutiny;

use Livewire\Component;
use App\Livewire\Traits\WithDynamicListing;
use App\Models\Application;
use App\Models\Advertisement;
use App\Models\Campaign;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    use WithDynamicListing;

    public $search = '';
    public $advertisementFilter = 'ALL';
    public $trackFilter = '';
    public $campaignFilter = '';
    public $quotaFilter = '';
    public $statusFilter = 'PAID'; // Default to PAID (awaiting scrutiny)
    public $viewMode = 'grid';
    public $perPage = 12;

    public $sortBy = 'created_at';
    public $sortDir = 'desc';

    public $isCertificateModalOpen = false;
    public $selectedAppForCertificate = null;

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }
        $this->resetPage();
    }

    public function updatingAdvertisementFilter()
    {
        $this->resetPage();
    }

    public function updatingQuotaFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->advertisementFilter = 'ALL';
        $this->campaignFilter = '';
        $this->trackFilter = '';
        $this->quotaFilter = '';
        $this->statusFilter = 'PAID';
        $this->sortBy = 'created_at';
        $this->sortDir = 'desc';
        $this->resetPage();
    }

    public function exportCsv()
    {
        abort_unless(Auth::check() && (
            Auth::user()->can('export-application') ||
            Auth::user()->can('scrutinize-application') ||
            Auth::user()->can('view-application') ||
            Auth::user()->hasAnyRole(['super-admin', 'registrar', 'scrutiny-committee', 'scrutiny'])
        ), 403);

        $query = Application::with(['user.basicInfo', 'campaign.advertisement', 'billingLedger']);

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->advertisementFilter !== 'ALL') {
            $query->whereHas('campaign', fn($q) => $q->where('advertisement_id', (int)$this->advertisementFilter));
        }

        if ($this->campaignFilter) {
            $query->where('campaign_id', $this->campaignFilter);
        }

        if ($this->trackFilter) {
            $query->whereHas('campaign', fn($q) => $q->where('track_type', $this->trackFilter));
        }

        if ($this->quotaFilter) {
            $query->whereHas('user.basicInfo', fn($q) => $q->where('quota', $this->quotaFilter));
        }

        if (!empty($this->search)) {
            $s = '%' . trim($this->search) . '%';
            $rawId = preg_replace('/[^0-9]/', '', $this->search);
            $query->where(function ($q) use ($s, $rawId) {
                if (!empty($rawId)) {
                    $q->where('applications.id', (int)$rawId);
                }
                $q->orWhereHas('user', function ($uq) use ($s) {
                    $uq->where('email', 'like', $s)
                       ->orWhereHas('basicInfo', function ($bq) use ($s) {
                           $bq->where('first_name', 'like', $s)
                              ->orWhere('last_name', 'like', $s)
                              ->orWhere('cnic_number', 'like', $s);
                       });
                })->orWhereHas('campaign', function ($cq) use ($s) {
                    $cq->where('title', 'like', $s)->orWhere('job_code', 'like', $s);
                });
            });
        }

        $records = $query->latest()->get();

        $headers = [
            'Dossier Ref',
            'Candidate Name',
            'Father Name',
            'CNIC Number',
            'Candidate Email',
            'Position Title',
            'Job Code',
            'Department',
            'Cadre Track',
            'Statutory Quota',
            'Fee Status',
            'Scrutiny Status',
            'Disqualification Reason',
            'Submission Date',
        ];

        $rows = [];
        foreach ($records as $app) {
            $bi = $app->user?->basicInfo;
            $name = $bi ? trim($bi->first_name . ' ' . $bi->last_name) : ($app->user?->email ?? 'N/A');
            $rows[] = [
                $app->tracking_number,
                $name,
                $bi?->father_name ?? 'N/A',
                $bi?->cnic_number ?? 'N/A',
                $app->user?->email ?? '',
                $app->campaign?->title ?? '',
                $app->campaign?->job_code ?? '',
                $app->campaign?->department ?? '',
                $app->campaign?->track_type ?? '',
                $bi?->quota ?? 'Open Merit',
                $app->billingLedger?->status === 'SETTLED' ? 'Settled / Paid' : 'Pending',
                $app->status,
                $app->disqualification_reason ?? '',
                $app->submission_timestamp ? $app->submission_timestamp->format('d M Y, h:i A') : $app->created_at->format('d M Y, h:i A'),
            ];
        }

        return $this->streamCsvDownload('scrutiny-roster-' . date('Y-m-d') . '.csv', $headers, $rows);
    }

    public function setViewMode($mode)
    {
        $this->viewMode = in_array($mode, ['grid', 'table']) ? $mode : 'grid';
    }

    public function openCertificateModal($appId)
    {
        abort_unless(Auth::check() && (
            Auth::user()->can('generate-scrutiny-certificate') ||
            Auth::user()->can('scrutinize-application') ||
            Auth::user()->can('view-application') ||
            Auth::user()->hasAnyRole(['super-admin', 'registrar', 'scrutiny-committee', 'scrutiny'])
        ), 403);

        $this->selectedAppForCertificate = Application::with([
            'user.basicInfo.degrees',
            'user.basicInfo.experiences',
            'user.basicInfo.publications',
            'campaign.advertisement',
            'reviews'
        ])->findOrFail($appId);
        $this->isCertificateModalOpen = true;
    }

    public function closeCertificateModal()
    {
        $this->isCertificateModalOpen = false;
        $this->selectedAppForCertificate = null;
    }

    public function mount()
    {
        abort_unless(Auth::check() && (
            Auth::user()->can('scrutinize-application') ||
            Auth::user()->can('view-application') ||
            Auth::user()->hasAnyRole(['super-admin', 'registrar', 'scrutiny-committee', 'scrutiny'])
        ), 403);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTrackFilter()
    {
        $this->resetPage();
    }

    public function updatingCampaignFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        // 1. Compute summary stats scoped to current advertisement filter if applied
        $statsQuery = Application::query();
        if ($this->advertisementFilter !== 'ALL') {
            $statsQuery->whereHas('campaign', fn($q) => $q->where('advertisement_id', (int)$this->advertisementFilter));
        }

        $stats = [
            'pending' => (clone $statsQuery)->where('status', 'PAID')->count(),
            'eligible' => (clone $statsQuery)->where('status', 'ELIGIBLE')->count(),
            'ineligible' => (clone $statsQuery)->where('status', 'INELIGIBLE')->count(),
            'appealed' => (clone $statsQuery)->where('status', 'APPEALED')->count(),
        ];

        // 2. Query applications for scrutiny queue
        $query = Application::with(['user.basicInfo', 'campaign.advertisement', 'billingLedger']);

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->advertisementFilter !== 'ALL') {
            $query->whereHas('campaign', fn($q) => $q->where('advertisement_id', (int)$this->advertisementFilter));
        }

        if ($this->trackFilter) {
            $query->whereHas('campaign', fn($q) => $q->where('track_type', $this->trackFilter));
        }

        if ($this->campaignFilter) {
            $query->where('campaign_id', $this->campaignFilter);
        }

        if ($this->quotaFilter) {
            $query->whereHas('user.basicInfo', fn($q) => $q->where('quota', $this->quotaFilter));
        }

        if (!empty($this->search)) {
            $s = '%' . trim($this->search) . '%';
            $rawId = preg_replace('/[^0-9]/', '', $this->search);
            $query->where(function ($q) use ($s, $rawId) {
                if (!empty($rawId)) {
                    $q->where('applications.id', (int)$rawId);
                }
                $q->orWhereHas('user', function ($uq) use ($s) {
                    $uq->where('email', 'like', $s)
                       ->orWhereHas('basicInfo', function ($bq) use ($s) {
                           $bq->where('first_name', 'like', $s)
                              ->orWhere('last_name', 'like', $s)
                              ->orWhere('cnic_number', 'like', $s);
                       });
                })->orWhereHas('campaign', function ($cq) use ($s) {
                    $cq->where('title', 'like', $s)->orWhere('job_code', 'like', $s);
                });
            });
        }

        // 3. Dynamic Sorting
        if ($this->sortBy === 'candidate_name') {
            $query->join('users', 'applications.user_id', '=', 'users.id')
                ->leftJoin('basic_infos', 'users.id', '=', 'basic_infos.user_id')
                ->orderBy('basic_infos.first_name', $this->sortDir)
                ->select('applications.*');
        } elseif ($this->sortBy === 'tracking_number') {
            $query->orderBy('applications.id', $this->sortDir);
        } elseif ($this->sortBy === 'campaign_title') {
            $query->join('campaigns', 'applications.campaign_id', '=', 'campaigns.id')
                ->orderBy('campaigns.title', $this->sortDir)
                ->select('applications.*');
        } elseif ($this->sortBy === 'status') {
            $query->orderBy('applications.status', $this->sortDir);
        } else {
            $query->orderBy('applications.created_at', $this->sortDir);
        }

        $applications = $query->paginate($this->perPage);

        $campaignsQuery = Campaign::select('id', 'title', 'job_code', 'department', 'advertisement_id')->orderBy('title');
        if ($this->advertisementFilter !== 'ALL') {
            $campaignsQuery->where('advertisement_id', (int)$this->advertisementFilter);
        }
        $campaigns = $campaignsQuery->get();

        $advertisements = Advertisement::chronologicalDesc()->get();

        return view('livewire.scrutiny.dashboard', [
            'stats' => $stats,
            'applications' => $applications,
            'campaigns' => $campaigns,
            'advertisements' => $advertisements,
        ])->layout('layouts.app', ['title' => 'Scrutiny Committee Desk - Hirely']);
    }
}
