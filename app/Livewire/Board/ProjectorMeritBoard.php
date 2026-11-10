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

    // Statutory Quorum Tracker & Modal
    public bool $showQuorumModal = false;

    // Consensus Ballot & Digital Sign-off Modal
    public bool $showConsensusModal = false;
    public string $consensusResolution = '';
    public array $consensusDecisions = [];
    public ?string $ballotSignedAt = null;
    public ?string $ballotSignedByName = null;
    public ?int $ballotSignedByUserId = null;

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

    public function toggleQuorumModal(): void
    {
        $this->showQuorumModal = !$this->showQuorumModal;
    }

    public function openConsensusModal(): void
    {
        $campaign = Campaign::find($this->campaignId);
        if (!$campaign) return;

        $scoringRules = $campaign->scoring_rules ?? [];
        $existingBallot = $scoringRules['consensus_ballot'] ?? null;

        if ($existingBallot) {
            $this->consensusResolution = $existingBallot['resolution'] ?? '';
            $this->consensusDecisions = $existingBallot['decisions'] ?? [];
            $this->ballotSignedAt = isset($existingBallot['signed_at']) ? \Carbon\Carbon::parse($existingBallot['signed_at'])->format('d M Y, h:i A') : null;
            $this->ballotSignedByName = $existingBallot['signed_by_name'] ?? null;
            $this->ballotSignedByUserId = $existingBallot['signed_by_user_id'] ?? null;
        } else {
            $this->consensusResolution = "The Selection Board in its statutory meeting convened on " . now()->format('F d, Y') . " evaluated the research dossiers, academic career benchmarks, and live interview performances of the shortlisted candidates. The Board hereby records its formal consensus recommendation to the Syndicate.";
            
            $meritService = app(MeritListService::class);
            $stageData = $meritService->getStageData($campaign, 'selection_board');
            $candidates = $stageData['applications'] ?? collect();

            $this->consensusDecisions = [];
            foreach ($candidates as $index => $item) {
                $appId = $item['app']->id;
                $this->consensusDecisions[$appId] = [
                    'recommendation' => $index === 0 ? 'RECOMMENDED' : ($index === 1 ? 'ALTERNATE' : 'REGRET'),
                    'remarks' => $index === 0 ? 'Highest aggregate score and unanimous endorsement.' : '',
                ];
            }
            $this->ballotSignedAt = null;
            $this->ballotSignedByName = null;
            $this->ballotSignedByUserId = null;
        }

        $this->showConsensusModal = true;
    }

    public function closeConsensusModal(): void
    {
        $this->showConsensusModal = false;
    }

    public function setCandidateDecision($appId, $recommendation, $remarks = ''): void
    {
        abort_unless(
            Auth::user()->can('manage-projector-merit-board') ||
            Auth::user()->hasAnyRole(['super-admin', 'registrar', 'selection-board-member', 'board']),
            403
        );

        $this->consensusDecisions[$appId]['recommendation'] = $recommendation;
        if ($remarks) {
            $this->consensusDecisions[$appId]['remarks'] = $remarks;
        }
    }

    public function signAndLockConsensusBallot(): void
    {
        abort_unless(
            Auth::user()->can('manage-projector-merit-board') ||
            Auth::user()->hasAnyRole(['super-admin', 'registrar', 'selection-board-member', 'board']),
            403,
            'Unauthorized to execute statutory ballot sign-off.'
        );

        $campaign = Campaign::find($this->campaignId);
        if (!$campaign) return;

        $scoringRules = $campaign->scoring_rules ?? [];
        $scoringRules['consensus_ballot'] = [
            'signed_at' => now()->toIso8601String(),
            'signed_by_user_id' => Auth::id(),
            'signed_by_name' => Auth::user()->name,
            'signed_by_role' => Auth::user()->getRoleNames()->first() ?? 'Member',
            'resolution' => $this->consensusResolution,
            'decisions' => $this->consensusDecisions,
        ];
        $campaign->scoring_rules = $scoringRules;
        $campaign->save();

        foreach ($this->consensusDecisions as $appId => $decision) {
            $app = Application::find($appId);
            if ($app && ($decision['recommendation'] ?? '') === 'RECOMMENDED') {
                $app->status = 'OFFER_EXTENDED';
                $app->save();
            }
        }

        $this->ballotSignedAt = now()->format('d M Y, h:i A');
        $this->ballotSignedByName = Auth::user()->name;
        $this->ballotSignedByUserId = Auth::id();

        session()->flash('consensus_saved', 'Statutory Consensus Ballot has been successfully signed and sealed into statutory record.');
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
            $externalSubmittedCount = $activeEvaluations->whereIn('boardMember.id', $totalCommittees->whereIn('member_category', ['EXTERNAL', 'INTERNATIONAL'])->pluck('user_id'))->count();
            $externalQuorumMet = ($externalCount === 0 || $externalSubmittedCount >= 1);
            $quorumMet = $submittedCount >= $requiredQuorum && $externalQuorumMet;

            $deficiencies = [];
            if ($submittedCount < $requiredQuorum) {
                $deficiencies[] = "Voting attendance is below the statutory threshold (at least {$requiredQuorum} members required; currently {$submittedCount} cast).";
            }
            if ($externalCount > 0 && $externalSubmittedCount === 0) {
                $deficiencies[] = "At least one external subject expert from another university must record evaluations to satisfy HEC Model Act statutes.";
            }

            $committeeQuorum = [
                'total_members' => $totalCount,
                'internal_members' => $internalCount,
                'external_members' => $externalCount,
                'required_quorum' => $requiredQuorum,
                'members_submitted' => $submittedCount,
                'external_submitted' => $externalSubmittedCount,
                'quorum_met' => $quorumMet,
                'external_quorum_met' => $externalQuorumMet,
                'deficiencies' => $deficiencies,
                'roster' => $totalCommittees->map(function ($c) use ($activeEvaluations) {
                    $hasEvaluated = $activeEvaluations->contains('board_member_id', $c->user_id);
                    return [
                        'user_id' => $c->user_id,
                        'name' => $c->user->name ?? 'Committee Member',
                        'role' => $c->role_in_committee ?? 'Member',
                        'category' => $c->member_category ?? 'INTERNAL',
                        'has_evaluated' => $hasEvaluated,
                    ];
                }),
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
