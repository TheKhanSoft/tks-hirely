<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Livewire\Traits\WithDynamicListing;
use App\Models\Campaign;
use App\Models\SystemSetting;
use App\Models\CommitteeConstitution;
use App\Models\RecruitmentProcessFlow;
use App\Models\WorkflowStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ConstitutionCriteriaManagement extends Component
{
    use WithDynamicListing;

    // View tab switching: 'board_registry', 'process_flow', or 'campaign_constitution'
    public $activeTab = 'board_registry';

    // Default perPage
    public $perPage = 16;

    // Campaign tuning state
    public $selected_campaign_id = null;
    public $constitution_title = '';
    public $constitution_description = '';
    public $required_peer_reviewers = 3;
    public $enforce_strict_quota = true;

    /**
     * Dynamic flexible constitution rules repeater.
     */
    public $rules = [];

    // Global settings / preset save state
    public $saveAsDefaultType = '';
    public $showPresetNotice = false;

    // ==========================================
    // 🏛️ STATUTORY BOARDS & COMMITTEES REGISTRY
    // ==========================================
    public $showBoardModal = false;
    public $isEditBoard = false;
    public $selectedBoardId = null;
    public $sectorFilter = 'ALL';
    public $showQuorumModal = false;
    public $viewingBoard = null;

    // Board form fields
    public $board_name = '';
    public $board_slug = '';
    public $board_sector = 'ACADEMIC';
    public $board_type = 'SELECTION_BOARD';
    public $board_responsible_stage = 'stage_selection_board';
    public $board_description = '';
    public $board_required_peer_reviewers = 3;
    public $board_enforce_strict_quota = true;
    public $board_roles = [];
    public $board_routing_flow = [];
    public $board_eligibility = [
        'min_degree_level' => 'Ph.D. / Terminal Degree',
        'min_experience_years' => 3,
        'min_publications' => 2,
        'requires_fee_settlement' => true,
        'requires_allied_discipline' => true,
        'min_evaluation_percentile' => 60,
    ];

    // ==========================================
    // 🔄 DYNAMIC RECRUITMENT PROCESS FLOW ENGINE
    // ==========================================
    public $selected_flow_id = null;
    public $flow_name = '';
    public $flow_sector = 'ACADEMIC';
    public $flow_description = '';
    public $flow_stages = [];
    public $showStageModal = false;
    public $editingStageIndex = null;
    public $isNewStage = false;

    // Stage Form Fields
    public $stage_id = '';
    public $stage_name = '';
    public $stage_actor_type = 'COMMITTEE';
    public $stage_governing_role = '';
    public $stage_committee_type = 'SCRUTINY';
    public $stage_is_enabled = true;
    public $stage_sla_days = 7;
    public $stage_required_experts = 3;
    public $stage_pass_threshold_percent = 60;
    public $stage_rule_description = '';
    public $stage_on_pass_target_status = 'SCRUTINY_PASSED';
    public $stage_on_pass_next_step = null;
    public $stage_on_pass_label = '';
    public $stage_has_incomplete = false;
    public $stage_on_incomplete_target_status = 'INCOMPLETE_DOSSIER';
    public $stage_on_incomplete_grace_days = 5;
    public $stage_on_incomplete_label = '';
    public $stage_has_fail = true;
    public $stage_on_fail_target_status = 'DISQUALIFIED';
    public $stage_on_fail_allow_appeal = true;
    public $stage_on_fail_require_justification = true;
    public $stage_on_fail_label = '';

    public function mount($campaign_id = null)
    {
        abort_unless(Auth::user()->can('tune-constitution') || Auth::user()->can('manage-constitutions') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);

        $requestedCampaignId = $campaign_id ?: request()->query('campaign_id');
        if ($requestedCampaignId && Campaign::where('id', $requestedCampaignId)->exists()) {
            $this->selected_campaign_id = (int)$requestedCampaignId;
            $this->activeTab = 'campaign_constitution';
        } else {
            $firstActive = Campaign::where('is_active', true)->first();
            $this->selected_campaign_id = $firstActive ? $firstActive->id : Campaign::first()?->id;
        }

        if (request()->query('tab') === 'registry' || request()->query('tab') === 'boards') {
            $this->activeTab = 'board_registry';
        } elseif (request()->query('tab') === 'flow' || request()->query('tab') === 'process_flow') {
            $this->activeTab = 'process_flow';
        } elseif (request()->query('tab') === 'campaign') {
            $this->activeTab = 'campaign_constitution';
        }

        $this->loadConstitution();
        $this->loadProcessFlow();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->search = '';
        $this->resetPage();
    }

    public function updatedSelectedCampaignId($val)
    {
        $this->loadConstitution();
    }

    public function updatedSelectedFlowId($val)
    {
        $this->loadProcessFlow($val);
    }

    public function loadProcessFlow($flowId = null)
    {
        $id = $flowId ?: $this->selected_flow_id;
        $flow = null;
        if ($id) {
            $flow = RecruitmentProcessFlow::find($id);
        }
        if (!$flow) {
            $flow = RecruitmentProcessFlow::where('is_default', true)->first() ?: RecruitmentProcessFlow::first();
        }

        if ($flow) {
            $this->selected_flow_id = $flow->id;
            $this->flow_name = $flow->name;
            $this->flow_sector = $flow->sector;
            $this->flow_description = $flow->description ?? '';
            $this->flow_stages = $flow->stages ?? [];
        } else {
            $this->flow_stages = RecruitmentProcessFlow::defaultAcademicPipeline();
        }
    }

    public function loadConstitution()
    {
        if (!$this->selected_campaign_id) {
            $this->rules = [];
            return;
        }

        $campaign = Campaign::find($this->selected_campaign_id);
        if (!$campaign) {
            return;
        }

        $constitution = $campaign->getCommitteeConstitution();

        $this->constitution_title = $constitution['title'] ?? ($campaign->title . ' Statutory Committee Constitution');
        $this->constitution_description = $constitution['description'] ?? 'Statutory Committee constitution criteria and quorum breakdown.';
        $this->required_peer_reviewers = (int)($constitution['required_peer_reviewers'] ?? $campaign->getRequiredPeerReviewersCount());
        $this->enforce_strict_quota = (bool)($constitution['enforce_strict_quota'] ?? true);

        $sourceRules = $constitution['rules'] ?? $constitution['roles_breakdown'] ?? [];

        if (!empty($sourceRules) && is_array($sourceRules)) {
            $this->rules = array_map(function ($r, $idx) {
                return [
                    'id' => $r['id'] ?? ('rule_' . uniqid() . '_' . $idx),
                    'name' => $r['name'] ?? $r['role'] ?? 'Committee Member',
                    'count' => max(1, (int)($r['count'] ?? 1)),
                    'category' => in_array($r['category'] ?? '', ['INTERNAL', 'EXTERNAL', 'INTERNATIONAL', 'INDEPENDENT']) ? $r['category'] : 'INTERNAL',
                    'cadre' => $r['cadre'] ?? 'FACULTY',
                    'role_nature' => $r['role_nature'] ?? 'MEMBER',
                    'is_mandatory' => !empty($r['is_mandatory']),
                    'notes' => $r['notes'] ?? '',
                ];
            }, $sourceRules, array_keys($sourceRules));
        } else {
            $internal = (int)($constitution['required_internal'] ?? 2);
            $external = (int)($constitution['required_external'] ?? 2);
            $intl = (int)($constitution['required_international'] ?? 1);
            $indep = (int)($constitution['required_independent'] ?? 0);

            $this->rules = [];
            if ($internal > 0) {
                $this->rules[] = [
                    'id' => 'rule_' . uniqid(),
                    'name' => 'Internal Organization / Faculty Member',
                    'count' => $internal,
                    'category' => 'INTERNAL',
                    'cadre' => 'FACULTY',
                    'role_nature' => 'CONVENER',
                    'is_mandatory' => true,
                    'notes' => 'Internal members from home institution/department',
                ];
            }
            if ($external > 0) {
                $this->rules[] = [
                    'id' => 'rule_' . uniqid(),
                    'name' => 'External Subject Expert (Other Institution)',
                    'count' => $external,
                    'category' => 'EXTERNAL',
                    'cadre' => 'FACULTY',
                    'role_nature' => 'MEMBER',
                    'is_mandatory' => false,
                    'notes' => 'External subject experts from other accredited institutions',
                ];
            }
            if ($intl > 0) {
                $this->rules[] = [
                    'id' => 'rule_' . uniqid(),
                    'name' => 'International / Overseas Subject Specialist',
                    'count' => $intl,
                    'category' => 'INTERNATIONAL',
                    'cadre' => 'EXPERT',
                    'role_nature' => 'EXPERT',
                    'is_mandatory' => false,
                    'notes' => 'Overseas subject specialist',
                ];
            }
            if ($indep > 0) {
                $this->rules[] = [
                    'id' => 'rule_' . uniqid(),
                    'name' => 'Independent Legal / Governance Arbiter',
                    'count' => $indep,
                    'category' => 'INDEPENDENT',
                    'cadre' => 'LEGAL',
                    'role_nature' => 'ARBITER',
                    'is_mandatory' => false,
                    'notes' => 'Independent governance arbiter',
                ];
            }
        }
    }

    /**
     * Add a new dynamic constitution rule / slot for campaign.
     */
    public function addRule()
    {
        $this->rules[] = [
            'id' => 'rule_' . uniqid(),
            'name' => '',
            'count' => 1,
            'category' => 'INTERNAL',
            'cadre' => 'FACULTY',
            'role_nature' => 'MEMBER',
            'is_mandatory' => false,
            'notes' => '',
        ];
    }

    public function removeRule($index)
    {
        if (isset($this->rules[$index])) {
            unset($this->rules[$index]);
            $this->rules = array_values($this->rules);
        }
    }

    public function updateRuleCount($index, $delta)
    {
        if (isset($this->rules[$index])) {
            $current = (int)($this->rules[$index]['count'] ?? 1);
            $this->rules[$index]['count'] = max(1, min(50, $current + $delta));
        }
    }

    public function toggleMandatory($index)
    {
        if (isset($this->rules[$index])) {
            $this->rules[$index]['is_mandatory'] = !$this->rules[$index]['is_mandatory'];
        }
    }

    /**
     * Apply a pre-configured statutory constitution preset.
     */
    public function applyPreset($preset)
    {
        if ($preset === 'asst_prof') {
            $this->constitution_title = 'Statutory Selection Committee for Assistant Professor';
            $this->constitution_description = 'Statutory Criteria: 3 External Subject Experts for peer review. Selection Committee: Dean (Convener), Concerned Dept Head, 3 Internal Faculty from same institution, 1 External Professor from other institution.';
            $this->required_peer_reviewers = 3;
            $this->rules = [
                ['id' => 'rule_' . uniqid(), 'name' => 'Dean of Faculty (Convener)', 'count' => 1, 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'CONVENER', 'is_mandatory' => true, 'notes' => 'Faculty Dean / Official Convener of the Selection Committee'],
                ['id' => 'rule_' . uniqid(), 'name' => 'Department Head (Concerned)', 'count' => 1, 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'CONVENER', 'is_mandatory' => true, 'notes' => 'Concerned Department Head / Ex-Officio Member'],
                ['id' => 'rule_' . uniqid(), 'name' => 'Internal Subject Faculty (Same Institution)', 'count' => 3, 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'notes' => '3 Senior Professors/Associate Professors from same institution'],
                ['id' => 'rule_' . uniqid(), 'name' => 'External Subject Professor (Other Institution)', 'count' => 1, 'category' => 'EXTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'notes' => '1 External Professor from another university/institution'],
            ];
        } elseif ($preset === 'academic_prof') {
            $this->constitution_title = 'Statutory Professorial Selection Board (Associate & Full Professor)';
            $this->constitution_description = 'Statutory Criteria: 3 External / Overseas Subject Referees. Selection Board: Vice-Chancellor / Dean, 3 Internal Senior Leaders, 2 External Subject Specialists (including 1 Overseas).';
            $this->required_peer_reviewers = 3;
            $this->rules = [
                ['id' => 'rule_' . uniqid(), 'name' => 'Vice-Chancellor / Dean (Convener)', 'count' => 1, 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'CONVENER', 'is_mandatory' => true, 'notes' => 'Statutory Convener of Professorial Board'],
                ['id' => 'rule_' . uniqid(), 'name' => 'Internal Subject Specialists (Same Institution)', 'count' => 3, 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'notes' => '3 Senior Internal Professors from same university'],
                ['id' => 'rule_' . uniqid(), 'name' => 'External Subject Expert (Other Institution)', 'count' => 1, 'category' => 'EXTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'notes' => '1 External Professor from another accredited university'],
                ['id' => 'rule_' . uniqid(), 'name' => 'International / Overseas Subject Specialist', 'count' => 1, 'category' => 'INTERNATIONAL', 'cadre' => 'EXPERT', 'role_nature' => 'EXPERT', 'is_mandatory' => false, 'notes' => '1 Overseas Subject Specialist from foreign university'],
            ];
        } elseif ($preset === 'lecturer') {
            $this->constitution_title = 'Academic Selection Committee for Lecturer / Faculty';
            $this->constitution_description = 'Statutory Criteria: 4 Internal Faculty/Leadership Members, 1 External Subject Expert from partner institution.';
            $this->required_peer_reviewers = 0;
            $this->rules = [
                ['id' => 'rule_' . uniqid(), 'name' => 'Department Head (Convener)', 'count' => 1, 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'CONVENER', 'is_mandatory' => true, 'notes' => 'Concerned Department Head / Convener'],
                ['id' => 'rule_' . uniqid(), 'name' => 'Internal Faculty Members (Same Institution)', 'count' => 3, 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'notes' => '3 Internal Faculty Members from same department'],
                ['id' => 'rule_' . uniqid(), 'name' => 'External Subject Expert (Other Institution)', 'count' => 1, 'category' => 'EXTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'notes' => '1 External Faculty Expert from partner institution'],
            ];
        } elseif ($preset === 'corporate_board') {
            $this->constitution_title = 'Corporate Executive Selection & Evaluation Panel';
            $this->constitution_description = 'Enterprise Criteria: Managing Director/Lead, 2 Internal Executive Officers, 2 External Industry Advisors / Technical Specialists.';
            $this->required_peer_reviewers = 0;
            $this->rules = [
                ['id' => 'rule_' . uniqid(), 'name' => 'Managing Director / Executive Convener', 'count' => 1, 'category' => 'INTERNAL', 'cadre' => 'EXECUTIVE', 'role_nature' => 'CONVENER', 'is_mandatory' => true, 'notes' => 'Managing Director / Panel Convener'],
                ['id' => 'rule_' . uniqid(), 'name' => 'Internal Division Heads / HR Director', 'count' => 2, 'category' => 'INTERNAL', 'cadre' => 'OFFICER', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'notes' => '2 Internal Officers / Division Leaders'],
                ['id' => 'rule_' . uniqid(), 'name' => 'External Industry Advisors / Board Delegates', 'count' => 2, 'category' => 'EXTERNAL', 'cadre' => 'EXPERT', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'notes' => '2 External Industry Advisors / Specialists from other companies'],
            ];
        } elseif ($preset === 'public_sector') {
            $this->constitution_title = 'Public Sector Service Selection Board';
            $this->constitution_description = 'Public Sector Criteria: 2 Departmental Members, 1 External Public Service Delegate, 1 Independent Legal Arbiter.';
            $this->required_peer_reviewers = 0;
            $this->rules = [
                ['id' => 'rule_' . uniqid(), 'name' => 'Departmental Representative / Chairman', 'count' => 1, 'category' => 'INTERNAL', 'cadre' => 'OFFICER', 'role_nature' => 'CONVENER', 'is_mandatory' => true, 'notes' => 'Ex-Officio Chairman / Departmental Representative'],
                ['id' => 'rule_' . uniqid(), 'name' => 'Internal Service Member', 'count' => 1, 'category' => 'INTERNAL', 'cadre' => 'OFFICER', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'notes' => 'Internal Civil / Administrative Service Officer'],
                ['id' => 'rule_' . uniqid(), 'name' => 'External Public Service Specialist', 'count' => 1, 'category' => 'EXTERNAL', 'cadre' => 'OFFICER', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'notes' => '1 Public Service Delegate from external agency'],
                ['id' => 'rule_' . uniqid(), 'name' => 'Independent Legal / Governance Arbiter', 'count' => 1, 'category' => 'INDEPENDENT', 'cadre' => 'LEGAL', 'role_nature' => 'ARBITER', 'is_mandatory' => false, 'notes' => '1 Independent Legal or Governance Arbiter'],
            ];
        } elseif ($preset === 'expert_heavy') {
            $this->constitution_title = 'External-Led Expert Scrutiny Panel';
            $this->constitution_description = '1 Internal Convener, 3 External Subject Experts (Other Universities/Offices), 1 Overseas Specialist.';
            $this->required_peer_reviewers = 3;
            $this->rules = [
                ['id' => 'rule_' . uniqid(), 'name' => 'Internal Convener / Head', 'count' => 1, 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'CONVENER', 'is_mandatory' => true, 'notes' => '1 Internal Convener'],
                ['id' => 'rule_' . uniqid(), 'name' => 'External Subject Experts (Other Universities)', 'count' => 3, 'category' => 'EXTERNAL', 'cadre' => 'EXPERT', 'role_nature' => 'EXPERT', 'is_mandatory' => false, 'notes' => '3 External Subject Experts from other universities/offices'],
                ['id' => 'rule_' . uniqid(), 'name' => 'International / Overseas Specialist', 'count' => 1, 'category' => 'INTERNATIONAL', 'cadre' => 'EXPERT', 'role_nature' => 'EXPERT', 'is_mandatory' => false, 'notes' => '1 Overseas Specialist'],
            ];
        } elseif ($preset === 'blank') {
            $this->constitution_title = 'Custom Selection Committee';
            $this->constitution_description = '';
            $this->rules = [
                ['id' => 'rule_' . uniqid(), 'name' => '', 'count' => 1, 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'notes' => ''],
            ];
        }
    }

    /**
     * Apply a saved master constitution from the registry to the active campaign.
     */
    public function applyBoardConstitutionToCampaign($boardId)
    {
        $board = CommitteeConstitution::findOrFail($boardId);
        $this->constitution_title = $board->name;
        $this->constitution_description = $board->description;
        $this->required_peer_reviewers = $board->required_peer_reviewers;
        $this->enforce_strict_quota = $board->enforce_strict_quota;

        $sourceRules = $board->roles_breakdown ?? [];
        $this->rules = array_map(function ($r, $idx) {
            return [
                'id' => $r['id'] ?? ('rule_' . uniqid() . '_' . $idx),
                'name' => $r['name'] ?? 'Committee Member',
                'count' => max(1, (int)($r['count'] ?? 1)),
                'category' => $r['category'] ?? 'INTERNAL',
                'cadre' => $r['cadre'] ?? 'FACULTY',
                'role_nature' => $r['role_nature'] ?? 'MEMBER',
                'is_mandatory' => !empty($r['is_mandatory']),
                'notes' => $r['notes'] ?? '',
            ];
        }, $sourceRules, array_keys($sourceRules));

        if ($this->selected_campaign_id) {
            $campaign = Campaign::find($this->selected_campaign_id);
            if ($campaign) {
                $campaign->update(['constitution_id' => $board->id]);
            }
        }

        session()->flash('message', "Board Constitution '{$board->name}' successfully loaded into active campaign!");
    }

    /**
     * Save the dynamic constitution specification to the selected campaign.
     */
    public function saveConstitution()
    {
        abort_unless(Auth::user()->can('tune-constitution') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);

        $this->validate([
            'selected_campaign_id' => 'required|exists:campaigns,id',
            'constitution_title' => 'required|string|max:200',
            'required_peer_reviewers' => 'required|integer|min:0|max:20',
            'rules' => 'required|array|min:1',
            'rules.*.name' => 'required|string|min:2|max:150',
            'rules.*.count' => 'required|integer|min:1|max:50',
            'rules.*.category' => 'required|in:INTERNAL,EXTERNAL,INTERNATIONAL,INDEPENDENT',
        ], [
            'rules.*.name.required' => 'Each rule must have a role / title (e.g. Dean, Faculty, External Expert).',
            'rules.*.name.min' => 'Role name must be at least 2 characters.',
            'rules.*.count.min' => 'Member count must be at least 1.',
        ]);

        $campaign = Campaign::findOrFail($this->selected_campaign_id);

        $totalCount = collect($this->rules)->sum('count');
        $internalCount = collect($this->rules)->where('category', 'INTERNAL')->sum('count');
        $externalCount = collect($this->rules)->where('category', 'EXTERNAL')->sum('count');
        $internationalCount = collect($this->rules)->where('category', 'INTERNATIONAL')->sum('count');
        $independentCount = collect($this->rules)->where('category', 'INDEPENDENT')->sum('count');

        $rulesPayload = [
            'title' => $this->constitution_title,
            'description' => $this->constitution_description ?: 'Statutory quota specification.',
            'required_total' => $totalCount,
            'required_internal' => $internalCount,
            'required_external' => $externalCount,
            'required_international' => $internationalCount,
            'required_independent' => $independentCount,
            'required_peer_reviewers' => (int)$this->required_peer_reviewers,
            'enforce_strict_quota' => (bool)$this->enforce_strict_quota,
            'rules' => $this->rules,
            'roles_breakdown' => $this->rules,
        ];

        $scoringRules = $campaign->scoring_rules ?? [];
        $scoringRules['committee_constitution'] = $rulesPayload;
        $scoringRules['required_peer_reviewers'] = (int)$this->required_peer_reviewers;

        $campaign->update(['scoring_rules' => $scoringRules]);

        session()->flash('message', "Statutory Committee Constitution & Criteria successfully updated for {$campaign->title} (Total Quorum: {$totalCount} members).");
    }

    public function saveAsSystemDefault()
    {
        abort_unless(Auth::user()->can('tune-constitution') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);

        $campaign = Campaign::findOrFail($this->selected_campaign_id);
        $lower = strtolower($campaign->title);

        if (str_contains($lower, 'assistant professor') || str_contains($lower, 'asst professor')) {
            SystemSetting::set('asst_prof_required_peer_reviewers', $this->required_peer_reviewers, 'constitution_defaults', 'integer');
        } elseif (str_contains($lower, 'professor') || str_contains($lower, 'associate professor')) {
            SystemSetting::set('prof_required_peer_reviewers', $this->required_peer_reviewers, 'constitution_defaults', 'integer');
        } elseif (str_contains($lower, 'lecturer')) {
            SystemSetting::set('lecturer_required_peer_reviewers', $this->required_peer_reviewers, 'constitution_defaults', 'integer');
        }

        session()->flash('message', "Statutory peer review quotas saved to system default configuration.");
    }

    // ==========================================
    // 🛠️ CUSTOM BOARD / CONSTITUTION CRUD METHODS
    // ==========================================
    public function openCreateBoardModal()
    {
        $this->resetBoardForm();
        $this->isEditBoard = false;
        $this->selectedBoardId = null;
        $this->board_name = '';
        $this->board_slug = '';
        $this->board_sector = 'ACADEMIC';
        $this->board_type = 'SELECTION_BOARD';
        $this->board_responsible_stage = 'stage_selection_board';
        $this->board_description = '';
        $this->board_required_peer_reviewers = 3;
        $this->board_enforce_strict_quota = true;

        $this->board_roles = [
            ['id' => 'r_' . uniqid(), 'name' => 'Board Convener / Head', 'count' => 1, 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'CONVENER', 'is_mandatory' => true, 'notes' => 'Institutional Convener'],
            ['id' => 'r_' . uniqid(), 'name' => 'Internal Evaluators', 'count' => 2, 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'notes' => 'Internal Subject Specialists'],
            ['id' => 'r_' . uniqid(), 'name' => 'External Subject Experts', 'count' => 2, 'category' => 'EXTERNAL', 'cadre' => 'EXPERT', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'notes' => 'External Experts from Other Institutions'],
        ];

        $this->board_routing_flow = [
            ['step' => 1, 'stage' => 'INITIAL_SCRUTINY', 'name' => 'Administrative & Document Scrutiny', 'cadre' => 'OFFICER', 'sla_days' => 5, 'mandatory' => true],
            ['step' => 2, 'stage' => 'PEER_REVIEW', 'name' => 'External Subject Expert Peer Review', 'cadre' => 'EXPERT', 'sla_days' => 14, 'mandatory' => true],
            ['step' => 3, 'stage' => 'BOARD_INTERVIEW', 'name' => 'Statutory Board Viva & Interview', 'cadre' => 'FACULTY', 'sla_days' => 7, 'mandatory' => true],
            ['step' => 4, 'stage' => 'FINAL_SANCTION', 'name' => 'Governing Authority Appointment Sanction', 'cadre' => 'EXECUTIVE', 'sla_days' => 10, 'mandatory' => true],
        ];

        $this->showBoardModal = true;
    }

    public function openEditBoardModal($id)
    {
        $board = CommitteeConstitution::findOrFail($id);
        $this->isEditBoard = true;
        $this->selectedBoardId = $board->id;
        $this->board_name = $board->name;
        $this->board_slug = $board->slug;
        $this->board_sector = $board->organization_sector;
        $this->board_type = $board->committee_type ?? 'SELECTION_BOARD';
        $this->board_responsible_stage = $board->responsible_stage ?? 'stage_selection_board';
        $this->board_description = $board->description;
        $this->board_required_peer_reviewers = $board->required_peer_reviewers;
        $this->board_enforce_strict_quota = $board->enforce_strict_quota;
        $this->board_roles = $board->roles_breakdown ?? [];
        $this->board_routing_flow = $board->getEffectiveRoutingFlow();
        $this->board_eligibility = $board->getEffectiveEligibilityCriteria();

        $this->showBoardModal = true;
    }

    public function viewBoardQuorum($id)
    {
        $this->viewingBoard = CommitteeConstitution::find($id);
        $this->showQuorumModal = true;
    }

    public function closeQuorumModal()
    {
        $this->showQuorumModal = false;
        $this->viewingBoard = null;
    }

    public function closeBoardModal()
    {
        $this->showBoardModal = false;
        $this->resetBoardForm();
    }

    public function resetBoardForm()
    {
        $this->reset([
            'board_name',
            'board_slug',
            'board_description',
            'board_roles',
            'board_routing_flow',
        ]);
        $this->board_sector = 'ACADEMIC';
        $this->board_type = 'SELECTION_BOARD';
        $this->board_responsible_stage = 'stage_selection_board';
        $this->board_required_peer_reviewers = 3;
        $this->board_enforce_strict_quota = true;
        $this->showBoardModal = false;
    }

    public function addBoardRole()
    {
        $this->board_roles[] = [
            'id' => 'r_' . uniqid(),
            'name' => '',
            'count' => 1,
            'category' => 'INTERNAL',
            'cadre' => 'FACULTY',
            'role_nature' => 'MEMBER',
            'is_mandatory' => false,
            'notes' => '',
        ];
    }

    public function removeBoardRole($index)
    {
        if (isset($this->board_roles[$index])) {
            unset($this->board_roles[$index]);
            $this->board_roles = array_values($this->board_roles);
        }
    }

    public function addRoutingStep()
    {
        $nextStep = count($this->board_routing_flow) + 1;
        $this->board_routing_flow[] = [
            'step' => $nextStep,
            'stage' => 'STAGE_' . $nextStep,
            'name' => 'Operational Review Stage ' . $nextStep,
            'cadre' => 'OFFICER',
            'sla_days' => 7,
            'mandatory' => true,
        ];
    }

    public function removeRoutingStep($index)
    {
        if (isset($this->board_routing_flow[$index])) {
            unset($this->board_routing_flow[$index]);
            $this->board_routing_flow = array_values($this->board_routing_flow);
            foreach ($this->board_routing_flow as $k => &$step) {
                $step['step'] = $k + 1;
            }
        }
    }

    public function saveBoardModel()
    {
        abort_unless(Auth::user()->can('tune-constitution') || Auth::user()->can('manage-constitutions') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);

        $this->board_sector = $this->board_sector ?: 'GENERAL';

        $this->validate([
            'board_name' => 'required|string|min:3|max:200',
            'board_required_peer_reviewers' => 'required|integer|min:0|max:20',
            'board_roles' => 'required|array|min:1',
            'board_roles.*.name' => 'required|string|min:2|max:150',
            'board_roles.*.count' => 'required|integer|min:1|max:50',
            'board_roles.*.category' => 'required|in:INTERNAL,EXTERNAL,INTERNATIONAL,INDEPENDENT',
        ]);

        $total = collect($this->board_roles)->sum('count');
        $internal = collect($this->board_roles)->where('category', 'INTERNAL')->sum('count');
        $external = collect($this->board_roles)->where('category', 'EXTERNAL')->sum('count');
        $intl = collect($this->board_roles)->where('category', 'INTERNATIONAL')->sum('count');
        $indep = collect($this->board_roles)->where('category', 'INDEPENDENT')->sum('count');

        $data = [
            'name' => $this->board_name,
            'slug' => $this->board_slug ?: Str::slug($this->board_name) . '-' . rand(100, 999),
            'organization_sector' => $this->board_sector,
            'committee_type' => $this->board_type ?: 'SELECTION_BOARD',
            'responsible_stage' => $this->board_responsible_stage ?: 'stage_selection_board',
            'description' => $this->board_description,
            'required_total' => $total,
            'required_internal' => $internal,
            'required_external' => $external,
            'required_international' => $intl,
            'required_independent' => $indep,
            'required_peer_reviewers' => (int)$this->board_required_peer_reviewers,
            'enforce_strict_quota' => (bool)$this->board_enforce_strict_quota,
            'roles_breakdown' => $this->board_roles,
            'routing_flow' => $this->board_routing_flow,
            'eligibility_criteria' => $this->board_eligibility,
            'created_by' => Auth::id(),
            'is_active' => true,
        ];

        if ($this->isEditBoard && $this->selectedBoardId) {
            $board = CommitteeConstitution::findOrFail($this->selectedBoardId);
            $board->update($data);
            session()->flash('message', "Statutory Board Constitution '{$board->name}' updated successfully.");
        } else {
            $board = CommitteeConstitution::create($data);
            session()->flash('message', "Statutory Board Constitution '{$board->name}' registered successfully with {$total} member quorum.");
        }

        $this->closeBoardModal();
    }

    public function deleteBoardModel($id)
    {
        abort_unless(Auth::user()->can('tune-constitution') || Auth::user()->can('manage-constitutions') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
        $board = CommitteeConstitution::findOrFail($id);
        $name = $board->name;
        $board->delete();
        session()->flash('message', "Statutory Board '{$name}' removed from directory.");
    }

    public function cloneBoardModel($id)
    {
        abort_unless(Auth::user()->can('tune-constitution') || Auth::user()->can('manage-constitutions') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
        $source = CommitteeConstitution::findOrFail($id);
        $clone = $source->replicate(['slug', 'created_at', 'updated_at']);
        $clone->name = $source->name . ' (Clone)';
        $clone->slug = Str::slug($clone->name) . '-' . rand(1000, 9999);
        $clone->save();
        session()->flash('message', "Board Constitution '{$source->name}' duplicated successfully as '{$clone->name}'.");
    }

    // ==========================================
    // 🔄 RECRUITMENT PROCESS FLOW ACTIONS
    // ==========================================
    public function openEditStageModal($index)
    {
        if (!isset($this->flow_stages[$index])) {
            return;
        }

        $this->editingStageIndex = $index;
        $this->isNewStage = false;
        $stage = $this->flow_stages[$index];

        $this->stage_id = $stage['id'] ?? ('stage_' . ($index + 1));
        $this->stage_name = $stage['name'] ?? '';
        $this->stage_actor_type = $stage['actor_type'] ?? 'COMMITTEE';
        $this->stage_governing_role = $stage['governing_role'] ?? '';
        $this->stage_committee_type = $stage['committee_type'] ?? 'SCRUTINY';
        $this->stage_is_enabled = !empty($stage['is_enabled']);
        $this->stage_sla_days = (int)($stage['sla_days'] ?? 7);
        $this->stage_required_experts = (int)($stage['required_experts'] ?? 3);
        $this->stage_pass_threshold_percent = (int)($stage['pass_threshold_percent'] ?? 60);
        $this->stage_rule_description = $stage['rule_description'] ?? '';

        $onPass = $stage['on_pass'] ?? [];
        $this->stage_on_pass_target_status = $onPass['target_status'] ?? 'SCRUTINY_PASSED';
        $this->stage_on_pass_next_step = $onPass['next_step'] ?? null;
        $this->stage_on_pass_label = $onPass['label'] ?? '';

        $onIncomplete = $stage['on_incomplete'] ?? null;
        $this->stage_has_incomplete = !empty($onIncomplete);
        $this->stage_on_incomplete_target_status = $onIncomplete['target_status'] ?? 'INCOMPLETE_DOSSIER';
        $this->stage_on_incomplete_grace_days = (int)($onIncomplete['grace_days'] ?? 5);
        $this->stage_on_incomplete_label = $onIncomplete['label'] ?? '';

        $onFail = $stage['on_fail'] ?? null;
        $this->stage_has_fail = !empty($onFail);
        $this->stage_on_fail_target_status = $onFail['target_status'] ?? 'DISQUALIFIED';
        $this->stage_on_fail_allow_appeal = !empty($onFail['allow_appeal']);
        $this->stage_on_fail_require_justification = !empty($onFail['require_justification']);
        $this->stage_on_fail_label = $onFail['label'] ?? '';

        $this->showStageModal = true;
    }

    public function addNewStage()
    {
        $nextStep = count($this->flow_stages) + 1;
        $this->editingStageIndex = count($this->flow_stages);
        $this->isNewStage = true;

        $this->stage_id = 'stage_custom_' . uniqid();
        $this->stage_name = 'Custom Stage ' . $nextStep . ': Evaluation Check';
        $this->stage_actor_type = 'COMMITTEE';
        $this->stage_governing_role = 'Evaluation Committee';
        $this->stage_committee_type = 'CUSTOM';
        $this->stage_is_enabled = true;
        $this->stage_sla_days = 7;
        $this->stage_required_experts = 2;
        $this->stage_pass_threshold_percent = 60;
        $this->stage_rule_description = 'Evaluation and scoring criteria configured by administrator.';
        $this->stage_on_pass_target_status = 'ELIGIBLE';
        $this->stage_on_pass_next_step = $nextStep + 1;
        $this->stage_on_pass_label = 'Criteria cleared ➔ Advance to next stage';
        $this->stage_has_incomplete = false;
        $this->stage_on_incomplete_target_status = 'INCOMPLETE_DOSSIER';
        $this->stage_on_incomplete_grace_days = 5;
        $this->stage_on_incomplete_label = '';
        $this->stage_has_fail = true;
        $this->stage_on_fail_target_status = 'REJECTED';
        $this->stage_on_fail_allow_appeal = true;
        $this->stage_on_fail_require_justification = true;
        $this->stage_on_fail_label = 'Requirements not met ➔ Not Recommended';

        $this->showStageModal = true;
    }

    public function closeStageModal()
    {
        $this->showStageModal = false;
        $this->editingStageIndex = null;
        $this->isNewStage = false;
    }

    public function saveStage()
    {
        $this->validate([
            'stage_name' => 'required|string|min:3|max:150',
            'stage_governing_role' => 'required|string|min:2|max:100',
            'stage_sla_days' => 'required|integer|min:1|max:120',
            'stage_on_pass_target_status' => 'required|string',
        ]);

        $stageData = [
            'step' => ($this->editingStageIndex !== null ? $this->editingStageIndex + 1 : count($this->flow_stages) + 1),
            'id' => $this->stage_id ?: ('stage_' . uniqid()),
            'name' => $this->stage_name,
            'actor_type' => $this->stage_actor_type,
            'governing_role' => $this->stage_governing_role,
            'committee_type' => $this->stage_committee_type,
            'is_enabled' => (bool)$this->stage_is_enabled,
            'sla_days' => (int)$this->stage_sla_days,
            'required_experts' => (int)$this->stage_required_experts,
            'pass_threshold_percent' => (int)$this->stage_pass_threshold_percent,
            'rule_description' => $this->stage_rule_description,
            'on_pass' => [
                'target_status' => $this->stage_on_pass_target_status,
                'next_step' => $this->stage_on_pass_next_step ? (int)$this->stage_on_pass_next_step : null,
                'label' => $this->stage_on_pass_label ?: 'Candidate passed evaluation step',
            ],
            'on_incomplete' => $this->stage_has_incomplete ? [
                'target_status' => $this->stage_on_incomplete_target_status ?: 'INCOMPLETE_DOSSIER',
                'allow_reupload' => true,
                'grace_days' => (int)$this->stage_on_incomplete_grace_days,
                'label' => $this->stage_on_incomplete_label ?: "Deficiencies notified with {$this->stage_on_incomplete_grace_days}-day grace period",
            ] : null,
            'on_fail' => $this->stage_has_fail ? [
                'target_status' => $this->stage_on_fail_target_status ?: 'DISQUALIFIED',
                'allow_appeal' => (bool)$this->stage_on_fail_allow_appeal,
                'require_justification' => (bool)$this->stage_on_fail_require_justification,
                'label' => $this->stage_on_fail_label ?: 'Stage failed / Not recommended',
            ] : null,
        ];

        if ($this->isNewStage || !isset($this->flow_stages[$this->editingStageIndex])) {
            $this->flow_stages[] = $stageData;
        } else {
            $this->flow_stages[$this->editingStageIndex] = $stageData;
        }

        // Re-index steps
        foreach ($this->flow_stages as $k => &$st) {
            $st['step'] = $k + 1;
        }

        $this->persistCurrentFlow();
        $this->showStageModal = false;
        session()->flash('message', "Process flow stage '{$this->stage_name}' successfully configured!");
    }

    public function removeStage($index)
    {
        if (isset($this->flow_stages[$index])) {
            $name = $this->flow_stages[$index]['name'] ?? 'Stage';
            unset($this->flow_stages[$index]);
            $this->flow_stages = array_values($this->flow_stages);
            foreach ($this->flow_stages as $k => &$st) {
                $st['step'] = $k + 1;
            }
            $this->persistCurrentFlow();
            session()->flash('message', "Stage '{$name}' removed from pipeline.");
        }
    }

    public function moveStageUp($index)
    {
        if ($index > 0 && isset($this->flow_stages[$index])) {
            $prev = $this->flow_stages[$index - 1];
            $this->flow_stages[$index - 1] = $this->flow_stages[$index];
            $this->flow_stages[$index] = $prev;

            foreach ($this->flow_stages as $k => &$st) {
                $st['step'] = $k + 1;
            }
            $this->persistCurrentFlow();
        }
    }

    public function moveStageDown($index)
    {
        if ($index < count($this->flow_stages) - 1 && isset($this->flow_stages[$index])) {
            $next = $this->flow_stages[$index + 1];
            $this->flow_stages[$index + 1] = $this->flow_stages[$index];
            $this->flow_stages[$index] = $next;

            foreach ($this->flow_stages as $k => &$st) {
                $st['step'] = $k + 1;
            }
            $this->persistCurrentFlow();
        }
    }

    public function toggleStageEnabled($index)
    {
        if (isset($this->flow_stages[$index])) {
            $this->flow_stages[$index]['is_enabled'] = empty($this->flow_stages[$index]['is_enabled']);
            $this->persistCurrentFlow();
            session()->flash('message', "Stage status updated.");
        }
    }

    public function persistCurrentFlow()
    {
        if ($this->selected_flow_id) {
            $flow = RecruitmentProcessFlow::find($this->selected_flow_id);
            if ($flow) {
                $flow->update([
                    'stages' => $this->flow_stages,
                    'name' => $this->flow_name ?: $flow->name,
                    'description' => $this->flow_description ?: $flow->description,
                    'sector' => $this->flow_sector ?: $flow->sector,
                ]);
            }
        }
    }

    public function saveProcessFlow()
    {
        $this->validate([
            'flow_name' => 'required|string|min:3|max:200',
            'flow_sector' => 'required|string|in:ACADEMIC,CORPORATE,HEALTHCARE,PUBLIC_SECTOR,JUDICIAL,DEFENSE,GENERAL',
        ]);

        $this->persistCurrentFlow();
        session()->flash('message', "Recruitment process flow '{$this->flow_name}' successfully saved!");
    }

    public function createNewFlowPipeline()
    {
        $flow = RecruitmentProcessFlow::create([
            'name' => 'Custom Recruitment Pipeline ' . rand(100, 999),
            'sector' => 'GENERAL',
            'description' => 'Custom multi-stage recruitment pipeline.',
            'stages' => RecruitmentProcessFlow::defaultAcademicPipeline(),
            'is_default' => false,
            'is_active' => true,
            'created_by' => Auth::id(),
        ]);

        $this->selected_flow_id = $flow->id;
        $this->loadProcessFlow($flow->id);
        session()->flash('message', "New recruitment pipeline '{$flow->name}' initialized.");
    }

    public function cloneProcessFlow($id)
    {
        $source = RecruitmentProcessFlow::findOrFail($id);
        $clone = $source->replicate(['slug', 'created_at', 'updated_at']);
        $clone->name = $source->name . ' (Copy)';
        $clone->is_default = false;
        $clone->save();

        $this->selected_flow_id = $clone->id;
        $this->loadProcessFlow($clone->id);
        session()->flash('message', "Recruitment pipeline duplicated successfully.");
    }

    public function deleteProcessFlow($id)
    {
        $flow = RecruitmentProcessFlow::findOrFail($id);
        if ($flow->is_default) {
            session()->flash('error', 'Cannot delete default system pipeline.');
            return;
        }
        $name = $flow->name;
        $flow->delete();

        $this->selected_flow_id = null;
        $this->loadProcessFlow();
        session()->flash('message', "Pipeline '{$name}' deleted.");
    }

    public function setFlowAsDefault($id)
    {
        RecruitmentProcessFlow::where('is_default', true)->update(['is_default' => false]);
        $flow = RecruitmentProcessFlow::findOrFail($id);
        $flow->update(['is_default' => true]);
        session()->flash('message', "'{$flow->name}' is now set as the system default recruitment process flow.");
    }

    // ==========================================
    // 📊 COMPUTED GETTERS & QUOTA SUMMARIES
    // ==========================================
    public function getTotalQuorumProperty(): int
    {
        return (int)collect($this->rules)->sum('count');
    }

    public function getInternalCountProperty(): int
    {
        return (int)collect($this->rules)->where('category', 'INTERNAL')->sum('count');
    }

    public function getExternalCountProperty(): int
    {
        return (int)collect($this->rules)->where('category', 'EXTERNAL')->sum('count');
    }

    public function getInternationalCountProperty(): int
    {
        return (int)collect($this->rules)->where('category', 'INTERNATIONAL')->sum('count');
    }

    public function getIndependentCountProperty(): int
    {
        return (int)collect($this->rules)->where('category', 'INDEPENDENT')->sum('count');
    }

    public function getFacultyCountProperty(): int
    {
        return (int)collect($this->rules)->where('cadre', 'FACULTY')->sum('count');
    }

    public function getOfficerCountProperty(): int
    {
        return (int)collect($this->rules)->where('cadre', 'OFFICER')->sum('count');
    }

    public function getExpertCountProperty(): int
    {
        return (int)collect($this->rules)->where('cadre', 'EXPERT')->sum('count');
    }

    public function getCurrentCampaignProperty()
    {
        return $this->selected_campaign_id ? Campaign::find($this->selected_campaign_id) : null;
    }

    public function getCampaignMetricsProperty()
    {
        return $this->currentCampaign?->getCommitteeMetrics();
    }

    public function exportCsv()
    {
        abort_unless(Auth::user()->can('manage-settings') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
        $constitutionsQuery = CommitteeConstitution::orderBy('name');
        if ($this->sectorFilter !== 'ALL') {
            $constitutionsQuery->where('organization_sector', $this->sectorFilter);
        }
        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $constitutionsQuery->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('slug', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm);
            });
        }
        $boards = $constitutionsQuery->get();

        $rows = [];
        foreach ($boards as $b) {
            $rows[] = [
                $b->id,
                $b->name,
                $b->slug,
                $b->type_label ?? $b->committee_type,
                $b->organization_sector,
                $b->required_total,
                $b->required_internal,
                $b->required_external,
                $b->required_peer_reviewers,
                $b->responsible_stage,
                $b->enforce_strict_quota ? 'Strict' : 'Flexible',
                $b->description
            ];
        }

        return $this->streamCsvDownload('statutory_committees_export_' . date('Y_m_d_His') . '.csv', [
            'ID', 'Committee Name', 'Slug', 'Type', 'Sector', 'Total Quorum', 'Internal Quota', 'External Quota', 'Peer Reviewers', 'Responsible Stage', 'Quota Mode', 'Description'
        ], $rows);
    }

    public function render()
    {
        $campaigns = Campaign::orderBy('created_at', 'desc')->get();

        $constitutionsQuery = CommitteeConstitution::orderBy('name');
        if ($this->sectorFilter !== 'ALL') {
            $constitutionsQuery->where('organization_sector', $this->sectorFilter);
        }
        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $constitutionsQuery->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('slug', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm);
            });
        }
        $registeredBoards = $constitutionsQuery->paginate($this->perPage);

        $processFlows = RecruitmentProcessFlow::orderBy('is_default', 'desc')->orderBy('name')->get();
        $workflowStatuses = WorkflowStatus::where('scope', 'APPLICATION')->orderBy('order')->get();

        return view('livewire.admin.constitution-criteria-management', [
            'campaigns' => $campaigns,
            'currentCampaign' => $this->currentCampaign,
            'metrics' => $this->campaignMetrics,
            'registeredBoards' => $registeredBoards,
            'processFlows' => $processFlows,
            'workflowStatuses' => $workflowStatuses,
        ])->layout('layouts.app', ['title' => 'Statutory Board & Committee Engine - Hirely']);
    }
}
