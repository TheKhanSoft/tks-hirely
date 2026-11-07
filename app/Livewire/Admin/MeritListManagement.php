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
use Illuminate\Support\Str;

class MeritListManagement extends Component
{
    use WithPagination;

    #[Url(as: 'ad', except: 'ALL')]
    public $advertisementFilter = 'ALL';

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

    #[Url(as: 'fee', except: 'ALL')]
    public $feeFilter = 'ALL';

    #[Url(as: 'gender', except: 'ALL')]
    public $genderFilter = 'ALL';

    #[Url(as: 'min_score', except: '')]
    public $minScoreFilter = '';

    #[Url(as: 'rec', except: 'ALL')]
    public $recommendationFilter = 'ALL';

    #[Url(as: 'sort', except: 'rank')]
    public $sortBy = 'rank';

    #[Url(as: 'dir', except: 'asc')]
    public $sortDir = 'asc';

    #[Url(as: 'per_page', except: 25)]
    public $perPage = 25;

    #[Url(as: 'view', except: 'table')]
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

        if ($this->campaignId && $this->advertisementFilter === 'ALL') {
            $camp = Campaign::find($this->campaignId);
            if ($camp && $camp->advertisement_id) {
                // Keep 'ALL' or sync with current campaign's ad if preferred
            }
        }
    }

    public function updatedAdvertisementFilter()
    {
        $campaignsQuery = Campaign::query();
        if ($this->advertisementFilter !== 'ALL') {
            $campaignsQuery->where('advertisement_id', (int)$this->advertisementFilter);
        }
        $first = $campaignsQuery->first();
        $this->campaignId = $first?->id;
        $this->resetPage();
    }

    public function setStage($stage)
    {
        $validStages = ['intake', 'scrutiny', 'peer_review', 'selection_board', 'consolidated'];
        if (in_array($stage, $validStages)) {
            $this->activeStage = $stage;
            $this->sortBy = 'rank';
            $this->sortDir = 'asc';
            $this->resetPage();
        }
    }

    public function selectCampaign($id)
    {
        $this->campaignId = $id;
        $this->resetFilters();
        $this->resetPage();
    }

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = in_array($column, [
                'aggregate_score', 'academic_score', 'peer_score', 'peer_score_avg', 
                'interview_score', 'total_score', 'research_score', 'research_exp_score'
            ]) ? 'desc' : 'asc';
        }
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->quotaFilter = 'ALL';
        $this->statusFilter = 'ALL';
        $this->feeFilter = 'ALL';
        $this->genderFilter = 'ALL';
        $this->minScoreFilter = '';
        $this->recommendationFilter = 'ALL';
        $this->sortBy = 'rank';
        $this->sortDir = 'asc';
        $this->resetPage();
    }

    public function openDetailModal($appId)
    {
        abort_unless(Auth::check() && (
            Auth::user()->can('view-board-roster') ||
            Auth::user()->can('view-application') ||
            Auth::user()->can('scrutinize-application') ||
            Auth::user()->can('view-merit-list') ||
            Auth::user()->hasAnyRole(['super-admin', 'registrar', 'selection-board-member'])
        ), 403);

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

    public function exportCsv(MeritListService $meritService)
    {
        abort_unless(Auth::check() && (
            Auth::user()->can('view-board-roster') ||
            Auth::user()->can('view-application') ||
            Auth::user()->hasAnyRole(['super-admin', 'registrar', 'selection-board-member'])
        ), 403);

        $campaign = Campaign::with(['advertisement', 'constitution', 'processFlow'])->find($this->campaignId);
        if (!$campaign) {
            session()->flash('error', 'Please select a campaign to export merit list.');
            return;
        }

        $stageData = $meritService->getStageData($campaign, $this->activeStage, [
            'search' => $this->search,
            'quota' => $this->quotaFilter,
            'status' => $this->statusFilter,
            'fee' => $this->feeFilter,
            'gender' => $this->genderFilter,
            'minScore' => $this->minScoreFilter,
            'recommendation' => $this->recommendationFilter,
            'sortBy' => $this->sortBy,
            'sortDir' => $this->sortDir,
        ]);

        $applications = $stageData['applications'];
        $stage = $this->activeStage;
        $campaignSlug = Str::slug($campaign->job_code ?: 'campaign-' . $campaign->id);
        $filename = "merit-list-{$stage}-{$campaignSlug}-" . now()->format('Ymd-His') . ".csv";

        return response()->streamDownload(function () use ($applications, $stage, $campaign, $stageData) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Metadata Block
            fputcsv($handle, ["HIRELY RECRUITMENT MANAGEMENT SYSTEM - STATUTORY PROCEEDINGS RECORD"]);
            fputcsv($handle, ["Official Stage:", $stageData['stage_title']]);
            fputcsv($handle, ["Campaign / Position:", $campaign->title . " (" . $campaign->job_code . ")"]);
            fputcsv($handle, ["Advertisement:", $campaign->advertisement ? "Ad #{$campaign->advertisement->advertisement_no} ({$campaign->advertisement->year})" : "N/A"]);
            fputcsv($handle, ["Total Records Exported:", $applications->count()]);
            fputcsv($handle, ["Average Stage Aggregate:", ($stageData['metrics']['average_aggregate'] ?? 0) . "%"]);
            fputcsv($handle, ["Top Benchmark Score:", ($stageData['metrics']['top_score'] ?? 0) . "%"]);
            fputcsv($handle, ["Export Timestamp:", now()->toDateTimeString()]);
            fputcsv($handle, []); // Blank separator

            // Column Headers according to Stage
            if ($stage === 'intake') {
                fputcsv($handle, [
                    'Sr #', 'Tracking ID', 'Candidate Name', 'Father Name', 'CNIC', 
                    'Gender', 'Quota', 'Domicile', 'Fee Status', 'Challan ID / Reference', 
                    'Application Status', 'Submission Timestamp'
                ]);
                foreach ($applications as $idx => $item) {
                    fputcsv($handle, [
                        $idx + 1,
                        $item['tracking_number'],
                        $item['candidate_name'],
                        $item['father_name'],
                        $item['cnic'],
                        $item['gender'],
                        $item['quota'],
                        $item['domicile'],
                        $item['is_paid'] ? 'PAID / SETTLED' : 'PENDING',
                        $item['challan_id'],
                        $item['status_label'],
                        $item['app']->submission_timestamp ? $item['app']->submission_timestamp->toDateTimeString() : $item['app']->created_at->toDateTimeString(),
                    ]);
                }
            } elseif ($stage === 'scrutiny') {
                fputcsv($handle, [
                    'Rank', 'Tracking ID', 'Candidate Name', 'CNIC', 'Quota', 
                    'Highest Degree', 'Terminal Division / GPA', 'Academic Score (/40)', 
                    'Scrutiny Determination', 'Deficiency Grounds', 'Fee Status'
                ]);
                foreach ($applications as $item) {
                    fputcsv($handle, [
                        $item['rank'],
                        $item['tracking_number'],
                        $item['candidate_name'],
                        $item['cnic'],
                        $item['quota'],
                        $item['highest_degree'],
                        $item['terminal_division'] . ' (' . $item['terminal_gpa'] . ')',
                        $item['academic_score'],
                        $item['status_label'],
                        $item['disqualification_reason'] ?: 'None (Cleared)',
                        $item['is_paid'] ? 'PAID' : 'PENDING',
                    ]);
                }
            } elseif ($stage === 'peer_review') {
                fputcsv($handle, [
                    'Rank', 'Tracking ID', 'Candidate Name', 'CNIC', 'Quota', 
                    'Referees Assigned', 'Referee Reviews Received', 'Peer Score Average (%)', 
                    'Scaled Score (/15)', 'Peer Benchmark Verdict', 'Status'
                ]);
                foreach ($applications as $item) {
                    $reviewsCount = $item['app']->reviews->count();
                    $completedReviews = $item['app']->reviews->whereNotNull('total_score')->count();
                    fputcsv($handle, [
                        $item['rank'],
                        $item['tracking_number'],
                        $item['candidate_name'],
                        $item['cnic'],
                        $item['quota'],
                        $reviewsCount,
                        $completedReviews,
                        $item['peer_score_avg'] . '%',
                        $item['peer_score_scaled'],
                        $item['peer_verdict'],
                        $item['status_label'],
                    ]);
                }
            } elseif ($stage === 'selection_board') {
                fputcsv($handle, [
                    'Rank', 'Tracking ID', 'Candidate Name', 'CNIC', 'Quota', 
                    'Academic Score (/40)', 'Research & Experience (/20)', 'Selection Board Viva (/40)', 
                    'Total Score (/100)', 'Selection Board Status', 'Recommendation'
                ]);
                foreach ($applications as $item) {
                    fputcsv($handle, [
                        $item['rank'],
                        $item['tracking_number'],
                        $item['candidate_name'],
                        $item['cnic'],
                        $item['quota'],
                        $item['academic_score'],
                        $item['research_exp_score'],
                        $item['interview_score'],
                        $item['aggregate_score'],
                        $item['board_status'],
                        $item['recommendation'] ?? 'N/A',
                    ]);
                }
            } else {
                // Consolidated Gazette Order
                fputcsv($handle, [
                    'Merit Rank', 'Tracking ID', 'Candidate Name', 'Father Name', 'CNIC', 
                    'Quota', 'Academic Credentials (/40)', 'Research & Experience (/15)', 
                    'Peer Review (/15)', 'Selection Board Viva (/30)', 'Cumulative Aggregate %', 
                    'Gazetted Statutory Recommendation', 'Status'
                ]);
                foreach ($applications as $item) {
                    fputcsv($handle, [
                        $item['rank'],
                        $item['tracking_number'],
                        $item['candidate_name'],
                        $item['father_name'],
                        $item['cnic'],
                        $item['quota'],
                        $item['academic_score'],
                        $item['research_exp_score'],
                        $item['peer_score_scaled'],
                        $item['interview_score'],
                        $item['aggregate_score'] . '%',
                        $item['recommendation'] ?? 'N/A',
                        $item['status_label'],
                    ]);
                }
            }

            fputcsv($handle, []);
            fputcsv($handle, ["Official Gazette Certificate - Certified by Registrar & Statutory Selection Secretariat"]);
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function render(MeritListService $meritService)
    {
        $advertisements = Advertisement::chronologicalDesc()->get();

        $campaignsQuery = Campaign::with('advertisement')->orderBy('id', 'desc');
        if ($this->advertisementFilter !== 'ALL') {
            $campaignsQuery->where('advertisement_id', (int)$this->advertisementFilter);
        }
        $campaigns = $campaignsQuery->get();

        // If selected campaign is not in filtered list, pick first
        if ($this->campaignId && !$campaigns->contains('id', (int)$this->campaignId)) {
            $this->campaignId = $campaigns->first()?->id;
        } elseif (!$this->campaignId && $campaigns->isNotEmpty()) {
            $this->campaignId = $campaigns->first()->id;
        }

        $selectedCampaign = $this->campaignId 
            ? Campaign::with(['advertisement', 'constitution', 'processFlow'])->find($this->campaignId) 
            : $campaigns->first();

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
                'fee' => $this->feeFilter,
                'gender' => $this->genderFilter,
                'minScore' => $this->minScoreFilter,
                'recommendation' => $this->recommendationFilter,
                'sortBy' => $this->sortBy,
                'sortDir' => $this->sortDir,
            ]);
        }

        $allApplications = $stageData['applications'];
        $totalRecords = $allApplications->count();

        // Manual Pagination for Collection
        $page = $this->getPage();
        $perPageInt = (int)$this->perPage;
        if ($perPageInt > 0) {
            $displayApplications = $allApplications->forPage($page, $perPageInt)->values();
        } else {
            $displayApplications = $allApplications;
        }

        return view('livewire.admin.merit-list-management', [
            'advertisements' => $advertisements,
            'campaigns' => $campaigns,
            'selectedCampaign' => $selectedCampaign,
            'applications' => $displayApplications,
            'totalRecords' => $totalRecords,
            'metrics' => $stageData['metrics'],
            'stageTitle' => $stageData['stage_title'],
        ])->layout('layouts.app', ['title' => 'Statutory Merit Lists & Proceedings - Hirely']);
    }
}
