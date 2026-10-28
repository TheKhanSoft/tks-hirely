<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Campaign;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Auth;

class ConstitutionCriteriaManagement extends Component
{
    public $selected_campaign_id = null;
    public $constitution_title = '';
    public $constitution_description = '';
    public $required_peer_reviewers = 3;
    public $enforce_strict_quota = true;

    /**
     * Dynamic flexible constitution rules repeater.
     * Each item: [
     *   'id' => string,
     *   'name' => string,
     *   'count' => int,
     *   'category' => 'INTERNAL'|'EXTERNAL'|'INTERNATIONAL'|'INDEPENDENT',
     *   'cadre' => 'FACULTY'|'OFFICER'|'EXPERT'|'EXECUTIVE'|'LEGAL'|'ANY',
     *   'role_nature' => 'CONVENER'|'MEMBER'|'SECRETARY'|'EXPERT'|'ARBITER',
     *   'is_mandatory' => bool,
     *   'notes' => string
     * ]
     */
    public $rules = [];

    // Global settings / preset save state
    public $saveAsDefaultType = '';
    public $showPresetNotice = false;

    public function mount($campaign_id = null)
    {
        abort_unless(Auth::user()->can('tune-constitution'), 403);

        $requestedCampaignId = $campaign_id ?: request()->query('campaign_id');
        if ($requestedCampaignId && Campaign::where('id', $requestedCampaignId)->exists()) {
            $this->selected_campaign_id = (int)$requestedCampaignId;
        } else {
            $firstActive = Campaign::where('is_active', true)->first();
            $this->selected_campaign_id = $firstActive ? $firstActive->id : Campaign::first()?->id;
        }

        $this->loadConstitution();
    }

    public function updatedSelectedCampaignId($val)
    {
        $this->loadConstitution();
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
            // Build initial dynamic rules from numeric quotas if rules array is missing
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
     * Add a new dynamic constitution rule / slot.
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

    /**
     * Remove a rule row by index.
     */
    public function removeRule($index)
    {
        if (isset($this->rules[$index])) {
            unset($this->rules[$index]);
            $this->rules = array_values($this->rules);
        }
    }

    /**
     * Increment or decrement count for a rule row.
     */
    public function updateRuleCount($index, $delta)
    {
        if (isset($this->rules[$index])) {
            $current = (int)($this->rules[$index]['count'] ?? 1);
            $this->rules[$index]['count'] = max(1, min(50, $current + $delta));
        }
    }

    /**
     * Toggle mandatory / ex-officio badge for a rule.
     */
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
     * Save the dynamic constitution specification to the selected campaign.
     */
    public function saveConstitution()
    {
        abort_unless(Auth::user()->can('tune-constitution'), 403);

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

    /**
     * Save the tuned criteria as the system-wide default preset.
     */
    public function saveAsSystemDefault()
    {
        abort_unless(Auth::user()->can('tune-constitution'), 403);

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

    public function render()
    {
        $campaigns = Campaign::orderBy('created_at', 'desc')->get();

        return view('livewire.admin.constitution-criteria-management', [
            'campaigns' => $campaigns,
            'currentCampaign' => $this->currentCampaign,
            'metrics' => $this->campaignMetrics,
        ])->layout('layouts.app', ['title' => 'Statutory Committee Criteria - Hirely']);
    }
}

