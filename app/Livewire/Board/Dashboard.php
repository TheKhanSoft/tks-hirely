<?php

namespace App\Livewire\Board;

use Livewire\Component;
use App\Livewire\Traits\WithDynamicListing;
use App\Models\Application;
use App\Models\Advertisement;
use App\Models\Campaign;
use App\Models\LiveEvaluation;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    use WithDynamicListing;

    public $search = '';
    public $advertisementFilter = 'ALL';
    public $campaignFilter = '';
    public $trackFilter = '';
    public $quotaFilter = '';
    public $viewMode = 'grid';
    public $perPage = 12;

    public $sortBy = 'created_at';
    public $sortDir = 'desc';

    public $isRosterModalOpen = false;
    public $rosterCampaign = null;
    public $rosterApplications = [];

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
        $this->sortBy = 'created_at';
        $this->sortDir = 'desc';
        $this->resetPage();
    }

    public function exportCsv()
    {
        abort_unless(Auth::check() && (
            Auth::user()->can('export-application') ||
            Auth::user()->can('view-board-roster') ||
            Auth::user()->can('access-board-grading') ||
            Auth::user()->can('evaluate-application') ||
            Auth::user()->hasAnyRole(['super-admin', 'selection-board-member', 'board-member', 'board', 'registrar'])
        ), 403);

        $query = Application::with(['user.basicInfo', 'campaign.advertisement', 'evaluations'])
            ->whereIn('status', ['ELIGIBLE', 'UNDER_REVIEW', 'APPROVED', 'SHORTLISTED']);

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
            'Statutory Quota',
            'Academic Benchmark (40)',
            'Average Board Viva (60)',
            'Total Aggregate Merit',
            'Selection Board Status',
        ];

        $rows = [];
        foreach ($records as $app) {
            $bi = $app->user?->basicInfo;
            $name = $bi ? trim($bi->first_name . ' ' . $bi->last_name) : ($app->user?->email ?? 'N/A');
            $avgInterview = $app->evaluations->isNotEmpty() ? $app->evaluations->avg('interview_score') : 0.0;
            $academicScore = 40.0;
            $totalMerit = round($academicScore + $avgInterview, 2);

            $rows[] = [
                $app->tracking_number,
                $name,
                $bi?->father_name ?? 'N/A',
                $bi?->cnic_number ?? 'N/A',
                $app->user?->email ?? '',
                $app->campaign?->title ?? '',
                $app->campaign?->job_code ?? '',
                $app->campaign?->department ?? '',
                $bi?->quota ?? 'Open Merit',
                $academicScore,
                round($avgInterview, 2),
                $totalMerit,
                $app->status,
            ];
        }

        return $this->streamCsvDownload('selection-board-roster-' . date('Y-m-d') . '.csv', $headers, $rows);
    }

    public function setViewMode($mode)
    {
        $this->viewMode = in_array($mode, ['grid', 'table']) ? $mode : 'grid';
    }

    public function openRosterModal($campaignId = null)
    {
        abort_unless(Auth::check() && (
            Auth::user()->can('view-board-roster') ||
            Auth::user()->can('access-board-grading') ||
            Auth::user()->can('evaluate-application') ||
            Auth::user()->hasAnyRole(['super-admin', 'selection-board-member', 'board-member', 'board'])
        ), 403);

        $cid = $campaignId ?: $this->campaignFilter ?: Campaign::first()?->id;
        if (!$cid) return;

        $this->rosterCampaign = Campaign::with('advertisement')->findOrFail($cid);

        $apps = Application::with(['user.basicInfo.degrees', 'evaluations'])
            ->where('campaign_id', $cid)
            ->whereIn('status', ['ELIGIBLE', 'UNDER_REVIEW', 'APPROVED'])
            ->get();

        // Calculate merit scores and sort descending
        $this->rosterApplications = $apps->map(function ($app) {
            $avgInterview = $app->evaluations->isNotEmpty() ? $app->evaluations->avg('interview_score') : 35.0;
            $academicScore = 40.0;
            $totalMerit = round($academicScore + $avgInterview, 2);
            return [
                'app' => $app,
                'name' => $app->user->basicInfo ? trim($app->user->basicInfo->first_name . ' ' . $app->user->basicInfo->last_name) : $app->user->email,
                'cnic' => $app->user->basicInfo?->cnic_number ?? 'N/A',
                'quota' => $app->user->basicInfo?->quota ?? 'Open Merit',
                'academic_score' => $academicScore,
                'interview_score' => round($avgInterview, 2),
                'total_score' => $totalMerit,
                'status' => $app->status,
            ];
        })->sortByDesc('total_score')->values()->all();

        $this->isRosterModalOpen = true;
    }

    public function closeRosterModal()
    {
        $this->isRosterModalOpen = false;
        $this->rosterCampaign = null;
        $this->rosterApplications = [];
    }

    public function mount()
    {
        abort_unless(Auth::check() && (
            Auth::user()->can('access-board-grading') ||
            Auth::user()->can('evaluate-application') ||
            Auth::user()->hasAnyRole(['super-admin', 'selection-board-member', 'board-member', 'board'])
        ), 403);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCampaignFilter()
    {
        $this->resetPage();
    }

    public function updatingTrackFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Application::with(['user.basicInfo', 'campaign.advertisement', 'evaluations'])
            ->whereIn('status', ['ELIGIBLE', 'UNDER_REVIEW', 'APPROVED', 'SHORTLISTED']);

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

        // Dynamic Sorting
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

        $candidates = $query->paginate($this->perPage);

        $campaignsQuery = Campaign::select('id', 'title', 'job_code', 'department', 'advertisement_id')->orderBy('title');
        if ($this->advertisementFilter !== 'ALL') {
            $campaignsQuery->where('advertisement_id', (int)$this->advertisementFilter);
        }
        $campaigns = $campaignsQuery->get();

        $advertisements = Advertisement::chronologicalDesc()->get();

        $statsQuery = Application::whereIn('status', ['ELIGIBLE', 'UNDER_REVIEW', 'APPROVED', 'SHORTLISTED']);
        if ($this->advertisementFilter !== 'ALL') {
            $statsQuery->whereHas('campaign', fn($q) => $q->where('advertisement_id', (int)$this->advertisementFilter));
        }

        $stats = [
            'total_eligible' => (clone $statsQuery)->count(),
            'my_graded' => LiveEvaluation::where('evaluator_id', Auth::id())->count(),
            'academic_positions' => (clone $statsQuery)->whereHas('campaign', fn($q) => $q->where('track_type', 'ACADEMIC'))->count(),
        ];

        return view('livewire.board.dashboard', [
            'candidates' => $candidates,
            'campaigns' => $campaigns,
            'advertisements' => $advertisements,
            'stats' => $stats,
        ])->layout('layouts.app', ['title' => 'Selection Board Desk - Hirely']);
    }
}
