<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Campaign;
use App\Models\Advertisement;
use App\Models\Application;
use App\Services\MeritListService;
use Illuminate\Support\Facades\Auth;

class MeritListManagement extends Component
{
    #[Url(as: 'camp', except: null)]
    public $campaignId = null;

    #[Url(as: 'step', except: 'consolidated')]
    public $activeStage = 'consolidated'; // 'intake', 'scrutiny', 'peer_review', 'selection_board', 'consolidated'

    #[Url(as: 'q', except: '')]
    public $search = '';

    #[Url(as: 'quota', except: 'ALL')]
    public $quotaFilter = 'ALL';

    #[Url(as: 'status', except: 'ALL')]
    public $statusFilter = 'ALL';

    public $viewMode = 'table';
    public $showDetailModal = false;
    public $selectedApplication = null;

    public function mount()
    {
        abort_unless(Auth::check() && (
            Auth::user()->can('view-board-roster') ||
            Auth::user()->can('view-application') ||
            Auth::user()->can('scrutinize-application') ||
            Auth::user()->can('access-board-grading') ||
            Auth::user()->hasAnyRole(['super-admin', 'registrar', 'selection-board-member', 'scrutiny-committee'])
        ), 403);

        if (!$this->campaignId) {
            $firstCampaign = Campaign::where('is_active', true)->first() ?: Campaign::first();
            $this->campaignId = $firstCampaign?->id;
        }
    }

    public function setStage($stage)
    {
        $validStages = ['intake', 'scrutiny', 'peer_review', 'selection_board', 'consolidated'];
        if (in_array($stage, $validStages)) {
            $this->activeStage = $stage;
        }
    }

    public function selectCampaign($id)
    {
        $this->campaignId = $id;
        $this->resetFilters();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->quotaFilter = 'ALL';
        $this->statusFilter = 'ALL';
    }

    public function openDetailModal($appId)
    {
        $this->selectedApplication = Application::with([
            'user.basicInfo.degrees',
            'user.basicInfo.experiences',
            'user.basicInfo.publications',
            'user.basicInfo.medals',
            'campaign.advertisement',
            'billingLedger',
            'evaluations.boardMember',
            'reviews.reviewer.reviewerProfile',
        ])->find($appId);

        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedApplication = null;
    }

    public function render(MeritListService $meritService)
    {
        $campaigns = Campaign::with('advertisement')->orderBy('id', 'desc')->get();
        $selectedCampaign = $this->campaignId ? Campaign::with(['advertisement', 'constitution', 'processFlow'])->find($this->campaignId) : $campaigns->first();

        $stageData = [
            'applications' => collect(),
            'metrics' => [
                'total_applications' => 0,
                'eligible_count' => 0,
                'ineligible_count' => 0,
                'deficient_count' => 0,
                'fee_paid_count' => 0,
                'average_aggregate' => 0,
                'top_score' => 0,
            ],
            'stage_title' => 'Consolidated Merit Roster',
        ];

        if ($selectedCampaign) {
            $stageData = $meritService->getStageData($selectedCampaign, $this->activeStage, [
                'search' => $this->search,
                'quota' => $this->quotaFilter,
                'status' => $this->statusFilter,
            ]);
        }

        return view('livewire.admin.merit-list-management', [
            'campaigns' => $campaigns,
            'selectedCampaign' => $selectedCampaign,
            'applications' => $stageData['applications'],
            'metrics' => $stageData['metrics'],
            'stageTitle' => $stageData['stage_title'],
        ])->layout('layouts.app', ['title' => 'Statutory Merit Lists & Proceedings - Hirely']);
    }
}
