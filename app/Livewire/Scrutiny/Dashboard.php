<?php

namespace App\Livewire\Scrutiny;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Application;
use App\Models\Campaign;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    use WithPagination;

    public $search = '';
    public $trackFilter = '';
    public $campaignFilter = '';
    public $statusFilter = 'PAID'; // Default to PAID (awaiting scrutiny)

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
        // 1. Compute summary stats
        $stats = [
            'pending' => Application::where('status', 'PAID')->count(),
            'eligible' => Application::where('status', 'ELIGIBLE')->count(),
            'ineligible' => Application::where('status', 'INELIGIBLE')->count(),
            'appealed' => Application::where('status', 'APPEALED')->count(),
        ];

        // 2. Query applications for scrutiny queue
        $query = Application::with(['user.basicInfo', 'campaign'])
            ->latest();

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->trackFilter) {
            $query->whereHas('campaign', fn($q) => $q->where('track_type', $this->trackFilter));
        }

        if ($this->campaignFilter) {
            $query->where('campaign_id', $this->campaignFilter);
        }

        if (!empty($this->search)) {
            $s = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('tracking_number', 'like', $s)
                  ->orWhereHas('user', function ($uq) use ($s) {
                      $uq->where('email', 'like', $s)
                         ->orWhereHas('basicInfo', function ($bq) use ($s) {
                             $bq->where('first_name', 'like', $s)
                                ->orWhere('last_name', 'like', $s)
                                ->orWhere('cnic_number', 'like', $s);
                         });
                  });
            });
        }

        $applications = $query->paginate(15);
        $campaigns = Campaign::select('id', 'title', 'job_code', 'department')->orderBy('title')->get();

        return view('livewire.scrutiny.dashboard', [
            'stats' => $stats,
            'applications' => $applications,
            'campaigns' => $campaigns,
        ])->layout('layouts.app', ['title' => 'Scrutiny Committee Desk - Hirely']);
    }
}
