<?php

namespace App\Livewire\Board;

use App\Models\Application;
use App\Models\Campaign;
use App\Models\CampaignCommittee;
use App\Models\LiveEvaluation;
use App\Models\SystemSetting;
use App\Services\MeritListService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProjectorMeritBoard extends Component
{
    public $campaignId;
    public $activeCandidateId = null;
    public bool $isAnonymized = false;
    public string $themeMode = 'dark'; // 'dark' (projector-optimized) or 'light'
    public bool $autoRefresh = true;
    public string $search = '';
    public string $quotaFilter = 'ALL';
    public string $statusFilter = 'ALL';

    public function mount($campaignId = null): void
    {
        abort_unless(
            Auth::user()->can('view-projector-merit-board') ||
            Auth::user()->hasAnyRole(['super-admin', 'registrar', 'selection-board-member', 'board-member', 'board']),
            403,
            'Unauthorized access to Selection Board Projector Merit Board.'
        );

        if ($campaignId) {
            $this->campaignId = $campaignId;
        } else {
            // Default to first active campaign with applications
            $defaultCampaign = Campaign::has('applications')->latest()->first() ?: Campaign::latest()->first();
            $this->campaignId = $defaultCampaign?->id;
        }
    }

    public function selectCampaign($id): void
    {
        $this->campaignId = $id;
        $this->activeCandidateId = null;
    }

    public function spotlightCandidate($appId): void
    {
        $this->activeCandidateId = $appId;
    }

    public function toggleAnonymization(): void
    {
        $this->isAnonymized = !$this->isAnonymized;
    }

    public function toggleTheme(): void
    {
        $this->themeMode = $this->themeMode === 'dark' ? 'light' : 'dark';
    }

    public function toggleAutoRefresh(): void
    {
        $this->autoRefresh = !$this->autoRefresh;
    }

    public function render()
    {
        $isFeatureEnabled = SystemSetting::isFeatureEnabled('projector_merit_board');

        if (!$isFeatureEnabled) {
            return view('livewire.board.projector-merit-board', [
                'isFeatureEnabled' => false,
                'campaign' => null,
                'campaigns' => collect(),
                'candidates' => collect(),
                'activeCandidate' => null,
                'activeEvaluations' => collect(),
                'committeeQuorum' => [],
            ])->layout('layouts.app');
        }

        $campaigns = Campaign::withCount('applications')
            ->orderByDesc('id')
            ->get();

        $campaign = null;
        if ($this->campaignId) {
            $campaign = Campaign::with(['advertisement', 'committees.user.basicInfo'])->find($this->campaignId);
        }

        $candidates = collect();
        $metrics = [];
        $activeCandidate = null;
        $activeEvaluations = collect();
        $committeeQuorum = [
            'total_members' => 0,
            'internal_members' => 0,
            'external_members' => 0,
            'members_submitted' => 0,
            'quorum_met' => false,
        ];

        if ($campaign) {
            $meritService = app(MeritListService::class);
            $stageData = $meritService->getStageData($campaign, 'selection_board', [
                'search' => $this->search,
                'quota' => $this->quotaFilter,
                'status' => $this->statusFilter,
            ]);

            $candidates = $stageData['applications'] ?? collect();
            $metrics = $stageData['metrics'] ?? [];

            // If no active candidate selected, spotlight the first one
            if (!$this->activeCandidateId && $candidates->isNotEmpty()) {
                $this->activeCandidateId = $candidates->first()['app']->id;
            }

            if ($this->activeCandidateId) {
                $activeCandidate = $candidates->firstWhere('app.id', $this->activeCandidateId);
                if ($activeCandidate) {
                    $activeEvaluations = LiveEvaluation::where('application_id', $this->activeCandidateId)
                        ->with('boardMember.basicInfo')
                        ->get();
                }
            }

            // Committee Quorum Computation
            $totalCommittees = $campaign->committees;
            $totalCount = $totalCommittees->count();
            $internalCount = $totalCommittees->where('member_category', 'INTERNAL')->count();
            $externalCount = $totalCommittees->whereIn('member_category', ['EXTERNAL', 'INTERNATIONAL'])->count();

            $submittedCount = $activeEvaluations->count();
            // Statutory quorum rule: at least 3 members or 50% whichever is greater, including at least 1 external
            $requiredQuorum = max(3, ceil($totalCount * 0.5));
            $quorumMet = $submittedCount >= $requiredQuorum && ($externalCount === 0 || $activeEvaluations->whereIn('boardMember.id', $totalCommittees->whereIn('member_category', ['EXTERNAL', 'INTERNATIONAL'])->pluck('user_id'))->isNotEmpty());

            $committeeQuorum = [
                'total_members' => $totalCount,
                'internal_members' => $internalCount,
                'external_members' => $externalCount,
                'required_quorum' => $requiredQuorum,
                'members_submitted' => $submittedCount,
                'quorum_met' => $quorumMet,
            ];
        }

        return view('livewire.board.projector-merit-board', [
            'isFeatureEnabled' => true,
            'campaign' => $campaign,
            'campaigns' => $campaigns,
            'candidates' => $candidates,
            'metrics' => $metrics,
            'activeCandidate' => $activeCandidate,
            'activeEvaluations' => $activeEvaluations,
            'committeeQuorum' => $committeeQuorum,
        ])->layout('layouts.app');
    }
}
