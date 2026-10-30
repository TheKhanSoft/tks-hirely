<?php

namespace App\Livewire\Board;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Application;
use App\Models\Campaign;
use App\Models\LiveEvaluation;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    use WithPagination;

    public $search = '';
    public $campaignFilter = '';
    public $trackFilter = '';
    public $viewMode = 'grid';
    public $perPage = 12;

    public $isRosterModalOpen = false;
    public $rosterCampaign = null;
    public $rosterApplications = [];

    public function setViewMode($mode)
    {
        $this->viewMode = in_array($mode, ['grid', 'table']) ? $mode : 'grid';
    }

    public function openRosterModal($campaignId = null)
    {
        $cid = $campaignId ?: $this->campaignFilter ?: Campaign::first()?->id;
        if (!$cid) return;

        $this->rosterCampaign = Campaign::with('advertisement')->findOrFail($cid);

        $apps = Application::with(['user.basicInfo.academicDegrees', 'evaluations'])
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
        $query = Application::with(['user.basicInfo', 'campaign', 'evaluations'])
            ->whereIn('status', ['ELIGIBLE', 'UNDER_REVIEW', 'APPROVED'])
            ->latest();

        if ($this->campaignFilter) {
            $query->where('campaign_id', $this->campaignFilter);
        }

        if ($this->trackFilter) {
            $query->whereHas('campaign', fn($q) => $q->where('track_type', $this->trackFilter));
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

        $candidates = $query->paginate($this->perPage);
        $campaigns = Campaign::select('id', 'title', 'job_code', 'department')->orderBy('title')->get();

        $stats = [
            'total_eligible' => Application::whereIn('status', ['ELIGIBLE', 'UNDER_REVIEW', 'APPROVED'])->count(),
            'my_graded' => LiveEvaluation::where('evaluator_id', Auth::id())->count(),
            'academic_positions' => Application::whereIn('status', ['ELIGIBLE', 'UNDER_REVIEW', 'APPROVED'])
                ->whereHas('campaign', fn($q) => $q->where('track_type', 'ACADEMIC'))->count(),
        ];

        return view('livewire.board.dashboard', [
            'candidates' => $candidates,
            'campaigns' => $campaigns,
            'stats' => $stats,
        ])->layout('layouts.app', ['title' => 'Selection Board Desk - Hirely']);
    }
}
