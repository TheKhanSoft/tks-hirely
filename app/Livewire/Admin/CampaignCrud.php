<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\Campaign;
use App\Models\Advertisement;
use App\Models\User;
use App\Models\CampaignCommittee;
use App\Models\CommitteeConstitution;
use App\Models\RecruitmentProcessFlow;
use App\Rules\SeparatedCommitteeConflictRule;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Traits\WithDynamicListing;

class CampaignCrud extends Component
{
    use WithDynamicListing;

    public $campaigns;
    public $campaign_id;
    
    // Campaign Form fields
    public $job_code;
    public $title;
    public $track_type = 'ACADEMIC';
    public $department;
    public $opening_date;
    public $closing_date;
    public $application_fee = 1000.00;
    public $require_transcript_proof = true;
    public $constitution_id = null;
    public $process_flow_id = null;
    public $advertisement_id = null;

    #[Url(as: 'ad')]
    public $advertisementFilter = '';

    #[Url(as: 'year')]
    public $yearFilter = '';

    #[Url(as: 'track')]
    public $trackFilter = 'ALL';

    #[Url(as: 'status')]
    public $statusFilter = 'ALL';

    // Flexible Academic Division & Marks Criteria Properties
    public $use_custom_academic_criteria = false;
    public $disallow_third_division = false;
    public $max_allowed_third_divisions = '';
    public $disallow_third_in_terminal = true;
    public $disallow_two_second_divisions = false;
    public $max_allowed_second_divisions = '';
    public $disallow_second_in_terminal = false;
    public $min_marks_percentage = '';
    public $min_terminal_percentage = '';
    public $min_terminal_cgpa = '';

    // Quick Link Board & Flow Modal
    public $showLinkModal = false;
    public $linkCampaignId = null;
    public $link_constitution_id = null;
    public $link_process_flow_id = null;

    // View Details Modal
    public $isDetailsOpen = false;
    public $selectedCampaign = null;
    
    // Dynamic rule mapper fields (Academic track weights)
    public $gold_medal_weight = 5.0;
    public $silver_medal_weight = 3.0;
    public $bronze_medal_weight = 2.0;
    
    public $organizer_leader_weight = 3.0;
    public $organizer_associate_weight = 1.5;
    public $paper_international_weight = 1.0;
    public $paper_national_weight = 0.5;
    public $event_attendee_weight = 0.1;
    
    public $w_category_weight = 1.00;
    public $x_category_weight = 0.75;
    public $y_category_weight = 0.50;
    public $z_category_weight = 0.15;
    
    public $project_million_rate = 1.0;
    public $project_max_marks = 13.0;
    
    public $patent_granted_weight = 1.0;
    public $patent_licensed_weight = 2.0;

    // Committee Assignment Form fields
    public $selected_campaign_id;
    public $selected_user_id;
    public $is_appellate_panel = false;
    public $member_category = 'INTERNAL';
    public $role_designation = 'Internal Member / Department Head';
    public $affiliation = '';
    public $country = '';

    // Candidate Pool Filtering
    public $candidateSearch = '';
    public $candidateFilterSection = 'ALL'; // ALL, INTERNAL, EXTERNAL, INTERNATIONAL, INDEPENDENT

    // Statutory Constitution Modal & Presets
    public $isConstitutionModalOpen = false;
    public $constitution_required_total = 5;
    public $constitution_required_internal = 2;
    public $constitution_required_external = 2;
    public $constitution_required_international = 1;
    public $constitution_required_independent = 0;
    public $constitution_required_peer_reviewers = 3;
    public $constitution_title = '';
    public $constitution_description = '';
    public $constitution_rules = [];
    
    // Lists
    public $committee_users = [];
    public $assignments;

    // View states
    public $isFormOpen = false;
    public $isEditMode = false;
    public $activeTab = 'campaigns'; // 'campaigns' or 'committees'

    public function mount()
    {
        abort_unless(Auth::check() && (Auth::user()->can('view-campaign') || Auth::user()->can('view-committee')), 403);

        // Pre-fill filters from incoming query parameters if supplied
        if (request()->has('advertisement_id') && empty($this->advertisementFilter)) {
            $this->advertisementFilter = (string)request()->query('advertisement_id');
        }
        if (request()->has('year') && empty($this->yearFilter)) {
            $this->yearFilter = (string)request()->query('year');
        }
        if (!empty($this->advertisementFilter) && empty($this->yearFilter) && $this->advertisementFilter !== 'NONE') {
            $ad = Advertisement::find($this->advertisementFilter);
            if ($ad) {
                $this->yearFilter = (string)$ad->year;
            }
        }

        $this->loadCampaigns();
        if ($this->campaigns->isNotEmpty()) {
            $this->selected_campaign_id = $this->campaigns->first()->id;
            $this->syncConstitutionFromCampaign($this->selected_campaign_id);
        }
        $this->loadCommitteeData();
    }

    public function updatingAdvertisementFilter()
    {
        $this->resetPage();
    }

    public function updatingYearFilter($newYear)
    {
        $this->resetPage();
        if (!empty($newYear) && !empty($this->advertisementFilter) && $this->advertisementFilter !== 'NONE') {
            $ad = Advertisement::find($this->advertisementFilter);
            if ($ad && (int)$ad->year !== (int)$newYear) {
                $this->advertisementFilter = '';
            }
        }
    }

    public function updatingTrackFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->advertisementFilter = '';
        $this->yearFilter = '';
        $this->trackFilter = 'ALL';
        $this->statusFilter = 'ALL';
        $this->resetPage();
    }

    public function loadCampaigns()
    {
        $this->campaigns = Campaign::with(['advertisement', 'constitution', 'processFlow'])->get();
    }

    public function loadCommitteeData()
    {
        // 🔒 DYNAMIC COMMITTEE CANDIDATE POOL:
        // Super-admin is strictly barred.
        // Any user holding ANY non-applicant role (faculty, leadership, committee, dean, or any custom role)
        // OR having an empanelled ReviewerProfile is automatically eligible for committee allocation!
        $users = User::where('is_active', true)
            ->whereDoesntHave('roles', fn($q) => $q->whereIn('name', ['super-admin', 'admin']))
            ->where('email', 'not like', '%superadmin%')
            ->where(function($q) {
                $q->whereHas('roles', fn($rq) => $rq->where('name', '!=', 'applicant'))
                  ->orWhereHas('reviewerProfile');
            })
            ->with(['roles', 'reviewerProfile', 'basicInfo'])
            ->get();

        $this->committee_users = $users->map(function($user) {
            $category = 'INTERNAL';
            $country = 'Domestic / Local';
            $affiliation = 'Main Campus / Head Office';
            $rank = $user->roles->first()?->name ? ucwords(str_replace('-', ' ', $user->roles->first()->name)) : 'Staff Member';
            $name = $user->email;
            $subject = 'Operations & Administration';

            if ($user->reviewerProfile) {
                $name = $user->reviewerProfile->name ?: $user->email;
                $affiliation = $user->reviewerProfile->university ?: 'External Partner Institution / Office';
                $country = $user->reviewerProfile->country ?: 'Domestic / Local';
                $rank = $user->reviewerProfile->academic_rank ?: 'External Expert';
                $subject = $user->reviewerProfile->subject ?: ($user->reviewerProfile->specialization ?: 'Specialist');
                
                // Categorize domestic vs international
                $domesticCountries = ['pakistan', 'domestic', 'local', 'national'];
                if (!in_array(strtolower(trim($country)), $domesticCountries)) {
                    $category = 'INTERNATIONAL';
                } else {
                    $category = 'EXTERNAL';
                }
            } elseif ($user->hasRole('appellate-committee')) {
                $category = 'INDEPENDENT';
                $rank = 'Appellate Grievance Arbiter';
                $affiliation = 'Independent Appellate Cell';
                $subject = 'Statutory Appeals & Grievance Review';
                if ($user->basicInfo && $user->basicInfo->first_name) {
                    $name = $user->basicInfo->first_name . ' ' . $user->basicInfo->last_name;
                }
            } else {
                $category = 'INTERNAL';
                if ($user->basicInfo && $user->basicInfo->first_name) {
                    $name = $user->basicInfo->first_name . ' ' . $user->basicInfo->last_name;
                }
            }

            return [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $name,
                'category' => $category,
                'country' => $country,
                'affiliation' => $affiliation,
                'rank' => $rank,
                'subject' => $subject,
                'role_name' => $user->roles->pluck('name')->map(fn($r) => ucwords(str_replace('-', ' ', $r)))->join(', '),
            ];
        })->toArray();

        // Get all current campaign assignments
        $this->assignments = CampaignCommittee::with(['campaign', 'user.roles', 'user.reviewerProfile', 'user.basicInfo'])
            ->orderBy('id', 'desc')
            ->get();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->isFormOpen = false;
        $this->resetForm();
    }

    // ==========================================
    // 📢 CAMPAIGN CRUD ACTIONS
    // ==========================================
    public function viewCampaignDetails($id)
    {
        abort_unless(Auth::user()->can('view-campaign'), 403);
        $this->selectedCampaign = Campaign::with(['departments', 'creator', 'statutoryBoard', 'processFlow', 'applications', 'committeeAssignments.user'])->findOrFail($id);
        $this->isDetailsOpen = true;
    }

    public function closeDetailsModal()
    {
        $this->isDetailsOpen = false;
        $this->selectedCampaign = null;
    }

    public function openCreateForm()
    {
        abort_unless(Auth::user()->can('create-campaign'), 403);
        $this->resetForm();
        $this->isEditMode = false;
        $this->isFormOpen = true;
    }

    public function openEditForm($id)
    {
        abort_unless(Auth::user()->can('edit-campaign'), 403);
        $campaign = Campaign::findOrFail($id);

        if (($campaign->is_completed || $campaign->is_closed) && !Auth::user()->hasRole('super-admin')) {
            session()->flash('error', 'Security Policy: This recruitment campaign is completed or closed. Modifications are locked for audit integrity and restricted to Super Admin.');
            return;
        }

        $this->resetForm();
        $this->campaign_id = $id;
        
        $this->job_code = $campaign->job_code;
        $this->title = $campaign->title;
        $this->track_type = $campaign->track_type;
        $this->department = $campaign->department;
        $this->opening_date = $campaign->opening_date?->format('Y-m-d\TH:i');
        $this->closing_date = $campaign->closing_date?->format('Y-m-d\TH:i');
        $this->application_fee = $campaign->application_fee;
        $this->constitution_id = $campaign->constitution_id;
        $this->process_flow_id = $campaign->process_flow_id;
        $this->advertisement_id = $campaign->advertisement_id;

        // Load rule map
        $rules = $campaign->scoring_rules ?? [];
        $ac = $rules['academic_criteria'] ?? null;
        if ($ac) {
            $this->use_custom_academic_criteria = true;
            $this->disallow_third_division = (bool)($ac['disallow_third_division'] ?? false);
            $this->max_allowed_third_divisions = $ac['max_allowed_third_divisions'] ?? '';
            $this->disallow_third_in_terminal = (bool)($ac['disallow_third_in_terminal'] ?? true);
            $this->disallow_two_second_divisions = (bool)($ac['disallow_two_second_divisions'] ?? false);
            $this->max_allowed_second_divisions = $ac['max_allowed_second_divisions'] ?? '';
            $this->disallow_second_in_terminal = (bool)($ac['disallow_second_in_terminal'] ?? false);
            $this->min_marks_percentage = $ac['min_marks_percentage'] ?? '';
            $this->min_terminal_percentage = $ac['min_terminal_percentage'] ?? '';
            $this->min_terminal_cgpa = $ac['min_terminal_cgpa'] ?? '';
        } else {
            $this->use_custom_academic_criteria = false;
            $global = \App\Services\AcademicEligibilityEvaluator::getGlobalCriteria();
            $this->disallow_third_division = (bool)($global['disallow_third_division'] ?? false);
            $this->max_allowed_third_divisions = $global['max_allowed_third_divisions'] ?? '';
            $this->disallow_third_in_terminal = (bool)($global['disallow_third_in_terminal'] ?? true);
            $this->disallow_two_second_divisions = (bool)($global['disallow_two_second_divisions'] ?? false);
            $this->max_allowed_second_divisions = $global['max_allowed_second_divisions'] ?? '';
            $this->disallow_second_in_terminal = (bool)($global['disallow_second_in_terminal'] ?? false);
            $this->min_marks_percentage = $global['min_marks_percentage'] ?? '';
            $this->min_terminal_percentage = $global['min_terminal_percentage'] ?? '';
            $this->min_terminal_cgpa = $global['min_terminal_cgpa'] ?? '';
        }
        $this->gold_medal_weight = $rules['gold_medal_weight'] ?? 5.0;
        $this->silver_medal_weight = $rules['silver_medal_weight'] ?? 3.0;
        $this->bronze_medal_weight = $rules['bronze_medal_weight'] ?? 2.0;
        
        $this->organizer_leader_weight = $rules['organizer_leader_weight'] ?? 3.0;
        $this->organizer_associate_weight = $rules['organizer_associate_weight'] ?? 1.5;
        $this->paper_international_weight = $rules['paper_international_weight'] ?? 1.0;
        $this->paper_national_weight = $rules['paper_national_weight'] ?? 0.5;
        $this->event_attendee_weight = $rules['event_attendee_weight'] ?? 0.1;
        
        $this->w_category_weight = $rules['w_category_weight'] ?? 1.00;
        $this->x_category_weight = $rules['x_category_weight'] ?? 0.75;
        $this->y_category_weight = $rules['y_category_weight'] ?? 0.50;
        $this->z_category_weight = $rules['z_category_weight'] ?? 0.15;
        
        $this->project_million_rate = $rules['project_million_rate'] ?? 1.0;
        $this->project_max_marks = $rules['project_max_marks'] ?? 13.0;
        
        $this->patent_granted_weight = $rules['patent_granted_weight'] ?? 1.0;
        $this->patent_licensed_weight = $rules['patent_licensed_weight'] ?? 2.0;
        $this->require_transcript_proof = (bool)$campaign->requiresTranscriptProof();

        $this->isEditMode = true;
        $this->isFormOpen = true;
    }

    public function saveCampaign()
    {
        abort_unless(Auth::user()->can($this->isEditMode ? 'edit-campaign' : 'create-campaign'), 403);

        $rules = [
            'job_code' => 'required|string|max:50|unique:campaigns,job_code,' . ($this->campaign_id ?? 'NULL'),
            'title' => 'required|string|max:255',
            'track_type' => 'required|in:ACADEMIC,ADMINISTRATIVE,SUPPORT',
            'department' => 'required|string|max:150',
            'opening_date' => 'required',
            'closing_date' => 'required',
            'application_fee' => 'required|numeric|min:0',
            'constitution_id' => 'nullable|exists:committee_constitutions,id',
            'process_flow_id' => 'nullable|exists:recruitment_process_flows,id',
            'advertisement_id' => 'nullable|exists:advertisements,id',
        ];

        $this->validate($rules);

        $scoringRules = [
            'gold_medal_weight' => floatval($this->gold_medal_weight),
            'silver_medal_weight' => floatval($this->silver_medal_weight),
            'bronze_medal_weight' => floatval($this->bronze_medal_weight),
            
            'organizer_leader_weight' => floatval($this->organizer_leader_weight),
            'organizer_associate_weight' => floatval($this->organizer_associate_weight),
            'paper_international_weight' => floatval($this->paper_international_weight),
            'paper_national_weight' => floatval($this->paper_national_weight),
            'event_attendee_weight' => floatval($this->event_attendee_weight),
            
            'w_category_weight' => floatval($this->w_category_weight),
            'x_category_weight' => floatval($this->x_category_weight),
            'y_category_weight' => floatval($this->y_category_weight),
            'z_category_weight' => floatval($this->z_category_weight),
            
            'project_million_rate' => floatval($this->project_million_rate),
            'project_max_marks' => floatval($this->project_max_marks),
            
            'patent_granted_weight' => floatval($this->patent_granted_weight),
            'patent_licensed_weight' => floatval($this->patent_licensed_weight),
            'require_transcript_proof' => (bool)$this->require_transcript_proof,
        ];

        if ($this->use_custom_academic_criteria) {
            $scoringRules['academic_criteria'] = [
                'disallow_third_division' => (bool)$this->disallow_third_division,
                'max_allowed_third_divisions' => $this->max_allowed_third_divisions !== '' ? (int)$this->max_allowed_third_divisions : null,
                'disallow_third_in_terminal' => (bool)$this->disallow_third_in_terminal,
                'disallow_two_second_divisions' => (bool)$this->disallow_two_second_divisions,
                'max_allowed_second_divisions' => $this->max_allowed_second_divisions !== '' ? (int)$this->max_allowed_second_divisions : null,
                'disallow_second_in_terminal' => (bool)$this->disallow_second_in_terminal,
                'min_marks_percentage' => $this->min_marks_percentage !== '' ? (float)$this->min_marks_percentage : null,
                'min_terminal_percentage' => $this->min_terminal_percentage !== '' ? (float)$this->min_terminal_percentage : null,
                'min_terminal_cgpa' => $this->min_terminal_cgpa !== '' ? (float)$this->min_terminal_cgpa : null,
                'scope' => 'POSITION',
            ];
        } else {
            $scoringRules['academic_criteria'] = null;
        }

        $data = [
            'advertisement_id' => $this->advertisement_id ?: null,
            'job_code' => $this->job_code,
            'title' => $this->title,
            'track_type' => $this->track_type,
            'department' => $this->department,
            'opening_date' => $this->opening_date,
            'closing_date' => $this->closing_date,
            'application_fee' => $this->application_fee,
            'require_transcript_proof' => (bool)$this->require_transcript_proof,
            'constitution_id' => $this->constitution_id ?: null,
            'process_flow_id' => $this->process_flow_id ?: null,
            'scoring_rules' => $scoringRules,
        ];

        if ($this->isEditMode) {
            $campaign = Campaign::findOrFail($this->campaign_id);
            if (($campaign->is_completed || $campaign->is_closed) && !Auth::user()->hasRole('super-admin')) {
                abort(403, 'Unauthorized: Completed or closed recruitment campaigns can only be modified by Super Admin.');
            }
            $campaign->update($data);
            session()->flash('message', 'Campaign updated successfully.');
        } else {
            Campaign::create($data);
            session()->flash('message', 'Campaign created successfully.');
        }

        $this->loadCampaigns();
        $this->isFormOpen = false;
        $this->resetForm();
    }

    public function deleteCampaign($id)
    {
        abort_unless(Auth::user()->can('delete-campaign'), 403);
        $campaign = Campaign::findOrFail($id);
        if (($campaign->is_completed || $campaign->is_closed || $campaign->applications()->exists()) && !Auth::user()->hasRole('super-admin')) {
            session()->flash('error', 'Security Policy: Completed campaigns or campaigns with submitted applications cannot be deleted by non-super-admins.');
            return;
        }
        $campaign->delete();
        $this->loadCampaigns();
        $this->loadCommitteeData();
        session()->flash('message', 'Campaign deleted successfully.');
    }

    public function resetForm()
    {
        $this->campaign_id = null;
        $this->job_code = '';
        $this->title = '';
        $this->track_type = 'ACADEMIC';
        $this->department = '';
        $this->opening_date = '';
        $this->closing_date = '';
        $this->application_fee = 1000.00;
        $this->require_transcript_proof = true;
        $this->constitution_id = null;
        $this->process_flow_id = null;
        $this->advertisement_id = null;

        // Reset Academic Division & Marks Criteria
        $this->use_custom_academic_criteria = false;
        $this->disallow_third_division = false;
        $this->max_allowed_third_divisions = '';
        $this->disallow_third_in_terminal = true;
        $this->disallow_two_second_divisions = false;
        $this->max_allowed_second_divisions = '';
        $this->disallow_second_in_terminal = false;
        $this->min_marks_percentage = '';
        $this->min_terminal_percentage = '';
        $this->min_terminal_cgpa = '';
        
        $this->gold_medal_weight = 5.0;
        $this->silver_medal_weight = 3.0;
        $this->bronze_medal_weight = 2.0;
        
        $this->organizer_leader_weight = 3.0;
        $this->organizer_associate_weight = 1.5;
        $this->paper_international_weight = 1.0;
        $this->paper_national_weight = 0.5;
        $this->event_attendee_weight = 0.1;
        
        $this->w_category_weight = 1.00;
        $this->x_category_weight = 0.75;
        $this->y_category_weight = 0.50;
        $this->z_category_weight = 0.15;
        
        $this->project_million_rate = 1.0;
        $this->project_max_marks = 13.0;
        
        $this->patent_granted_weight = 1.0;
        $this->patent_licensed_weight = 2.0;

        // Reset assignment fields
        $this->selected_campaign_id = null;
        $this->selected_user_id = null;
        $this->is_appellate_panel = false;
    }

    public function updatedSelectedCampaignId($val)
    {
        if ($val) {
            $this->syncConstitutionFromCampaign($val);
        }
    }

    public function syncConstitutionFromCampaign($campaignId)
    {
        $campaign = Campaign::find($campaignId);
        if ($campaign) {
            $constitution = $campaign->getCommitteeConstitution();
            $this->constitution_required_total = (int)($constitution['required_total'] ?? 5);
            $this->constitution_required_internal = (int)($constitution['required_internal'] ?? 2);
            $this->constitution_required_external = (int)($constitution['required_external'] ?? 2);
            $this->constitution_required_international = (int)($constitution['required_international'] ?? 1);
            $this->constitution_required_independent = (int)($constitution['required_independent'] ?? 0);
            $this->constitution_required_peer_reviewers = (int)($constitution['required_peer_reviewers'] ?? 3);
            $this->constitution_title = $constitution['title'] ?? 'Statutory Operational Committee Constitution';
            $this->constitution_description = $constitution['description'] ?? 'Statutory member quota breakdown.';

            $sourceRules = $constitution['rules'] ?? $constitution['roles_breakdown'] ?? [];
            if (!empty($sourceRules)) {
                $this->constitution_rules = array_map(function($r, $idx) {
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
                $this->constitution_rules = [];
                if ($this->constitution_required_internal > 0) {
                    $this->constitution_rules[] = [
                        'id' => 'rule_' . uniqid(),
                        'name' => 'Internal Organization / Faculty Member',
                        'count' => $this->constitution_required_internal,
                        'category' => 'INTERNAL',
                        'cadre' => 'FACULTY',
                        'role_nature' => 'CONVENER',
                        'is_mandatory' => true,
                        'notes' => '',
                    ];
                }
                if ($this->constitution_required_external > 0) {
                    $this->constitution_rules[] = [
                        'id' => 'rule_' . uniqid(),
                        'name' => 'External Subject Expert (Other Institution)',
                        'count' => $this->constitution_required_external,
                        'category' => 'EXTERNAL',
                        'cadre' => 'FACULTY',
                        'role_nature' => 'MEMBER',
                        'is_mandatory' => false,
                        'notes' => '',
                    ];
                }
                if ($this->constitution_required_international > 0) {
                    $this->constitution_rules[] = [
                        'id' => 'rule_' . uniqid(),
                        'name' => 'International / Overseas Specialist',
                        'count' => $this->constitution_required_international,
                        'category' => 'INTERNATIONAL',
                        'cadre' => 'EXPERT',
                        'role_nature' => 'EXPERT',
                        'is_mandatory' => false,
                        'notes' => '',
                    ];
                }
                if ($this->constitution_required_independent > 0) {
                    $this->constitution_rules[] = [
                        'id' => 'rule_' . uniqid(),
                        'name' => 'Independent Legal Arbiter',
                        'count' => $this->constitution_required_independent,
                        'category' => 'INDEPENDENT',
                        'cadre' => 'LEGAL',
                        'role_nature' => 'ARBITER',
                        'is_mandatory' => false,
                        'notes' => '',
                    ];
                }
            }
        }
    }

    public function addConstitutionRule()
    {
        abort_unless(Auth::user()->can('tune-constitution'), 403);

        $this->constitution_rules[] = [
            'id' => 'rule_' . uniqid(),
            'name' => '',
            'count' => 1,
            'category' => 'INTERNAL',
            'cadre' => 'FACULTY',
            'role_nature' => 'MEMBER',
            'is_mandatory' => false,
            'notes' => '',
        ];
        $this->recalculateConstitutionTotals();
    }

    public function removeConstitutionRule($index)
    {
        abort_unless(Auth::user()->can('tune-constitution'), 403);

        if (isset($this->constitution_rules[$index])) {
            unset($this->constitution_rules[$index]);
            $this->constitution_rules = array_values($this->constitution_rules);
            $this->recalculateConstitutionTotals();
        }
    }

    public function updateConstitutionRuleCount($index, $delta)
    {
        abort_unless(Auth::user()->can('tune-constitution'), 403);

        if (isset($this->constitution_rules[$index])) {
            $cur = (int)($this->constitution_rules[$index]['count'] ?? 1);
            $this->constitution_rules[$index]['count'] = max(1, min(50, $cur + $delta));
            $this->recalculateConstitutionTotals();
        }
    }

    public function recalculateConstitutionTotals()
    {
        if (!empty($this->constitution_rules)) {
            $this->constitution_required_total = (int)collect($this->constitution_rules)->sum('count');
            $this->constitution_required_internal = (int)collect($this->constitution_rules)->where('category', 'INTERNAL')->sum('count');
            $this->constitution_required_external = (int)collect($this->constitution_rules)->where('category', 'EXTERNAL')->sum('count');
            $this->constitution_required_international = (int)collect($this->constitution_rules)->where('category', 'INTERNATIONAL')->sum('count');
            $this->constitution_required_independent = (int)collect($this->constitution_rules)->where('category', 'INDEPENDENT')->sum('count');
        }
    }

    public function selectCandidate($userId)
    {
        $this->selected_user_id = $userId;
        $candidate = collect($this->committee_users)->firstWhere('id', $userId);
        if ($candidate) {
            $this->member_category = $candidate['category'];
            $this->affiliation = $candidate['affiliation'];
            $this->country = $candidate['country'];
            $this->role_designation = match($candidate['category']) {
                'INTERNATIONAL' => 'International / Overseas Specialist',
                'EXTERNAL' => 'External Subject Expert',
                'INDEPENDENT' => 'Independent Arbiter / Legal Member',
                default => 'Internal Member / Department Head',
            };
        }
    }

    public function clearSelectedCandidate()
    {
        $this->selected_user_id = null;
        $this->member_category = 'INTERNAL';
        $this->role_designation = 'Internal Member / Department Head';
        $this->affiliation = '';
        $this->country = '';
    }

    public function filterCandidateSection($sec)
    {
        $this->candidateFilterSection = $sec;
    }

    public function openConstitutionModal()
    {
        abort_unless(Auth::user()->can('tune-constitution'), 403);
        if ($this->selected_campaign_id) {
            $this->syncConstitutionFromCampaign($this->selected_campaign_id);
        }
        $this->isConstitutionModalOpen = true;
    }

    public function closeConstitutionModal()
    {
        $this->isConstitutionModalOpen = false;
    }

    public function applyConstitutionPreset($preset)
    {
        abort_unless(Auth::user()->can('tune-constitution'), 403);
        if ($preset === 'asst_prof') {
            $this->constitution_required_total = 6;
            $this->constitution_required_internal = 5;
            $this->constitution_required_external = 1;
            $this->constitution_required_international = 0;
            $this->constitution_required_independent = 0;
            $this->constitution_required_peer_reviewers = 3;
            $this->constitution_title = 'Statutory Selection Committee for Assistant Professor';
            $this->constitution_description = 'Statutory Criteria: 3 External Subject Experts for peer review. Selection Committee: Dean (Convener), Concerned Dept Head, 3 Internal Faculty from same institution, 1 External Professor from other institution.';
            $this->constitution_rules = [
                ['id' => 'rule_' . uniqid(), 'name' => 'Dean of Faculty (Convener)', 'count' => 1, 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'CONVENER', 'is_mandatory' => true, 'notes' => 'Faculty Dean / Official Convener of the Selection Committee'],
                ['id' => 'rule_' . uniqid(), 'name' => 'Department Head (Concerned)', 'count' => 1, 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'CONVENER', 'is_mandatory' => true, 'notes' => 'Concerned Department Head / Ex-Officio Member'],
                ['id' => 'rule_' . uniqid(), 'name' => 'Internal Subject Faculty (Same Institution)', 'count' => 3, 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'notes' => '3 Senior Professors/Associate Professors from same institution'],
                ['id' => 'rule_' . uniqid(), 'name' => 'External Subject Professor (Other Institution)', 'count' => 1, 'category' => 'EXTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'notes' => '1 External Professor from another university/institution'],
            ];
        } elseif ($preset === 'academic_prof') {
            $this->constitution_required_total = 6;
            $this->constitution_required_internal = 4;
            $this->constitution_required_external = 2;
            $this->constitution_required_international = 1;
            $this->constitution_required_independent = 0;
            $this->constitution_required_peer_reviewers = 3;
            $this->constitution_title = 'Statutory Professorial Selection Board (Associate & Full Professor)';
            $this->constitution_description = '3 External/Overseas Referees. Selection Board: 4 Internal Senior Leaders, 2 External Specialists (including 1 Overseas Specialist).';
            $this->constitution_rules = [
                ['id' => 'rule_' . uniqid(), 'name' => 'Vice-Chancellor / Dean (Convener)', 'count' => 1, 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'CONVENER', 'is_mandatory' => true, 'notes' => 'Statutory Convener of Professorial Board'],
                ['id' => 'rule_' . uniqid(), 'name' => 'Internal Subject Specialists (Same Institution)', 'count' => 3, 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'notes' => '3 Senior Internal Professors from same university'],
                ['id' => 'rule_' . uniqid(), 'name' => 'External Subject Expert (Other Institution)', 'count' => 1, 'category' => 'EXTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'notes' => '1 External Professor from another accredited university'],
                ['id' => 'rule_' . uniqid(), 'name' => 'International / Overseas Subject Specialist', 'count' => 1, 'category' => 'INTERNATIONAL', 'cadre' => 'EXPERT', 'role_nature' => 'EXPERT', 'is_mandatory' => false, 'notes' => '1 Overseas Subject Specialist from foreign university'],
            ];
        } elseif ($preset === 'corporate_board') {
            $this->constitution_required_total = 5;
            $this->constitution_required_internal = 3;
            $this->constitution_required_external = 2;
            $this->constitution_required_international = 0;
            $this->constitution_required_independent = 0;
            $this->constitution_required_peer_reviewers = 0;
            $this->constitution_title = 'Corporate Executive Selection & Evaluation Panel';
            $this->constitution_description = '3 Internal Company Executives (MD, HR, Division Head), 2 External Industry Advisors / Board Specialists.';
            $this->constitution_rules = [
                ['id' => 'rule_' . uniqid(), 'name' => 'Managing Director / Executive Convener', 'count' => 1, 'category' => 'INTERNAL', 'cadre' => 'EXECUTIVE', 'role_nature' => 'CONVENER', 'is_mandatory' => true, 'notes' => 'Managing Director / Panel Convener'],
                ['id' => 'rule_' . uniqid(), 'name' => 'Internal Division Heads / HR Director', 'count' => 2, 'category' => 'INTERNAL', 'cadre' => 'OFFICER', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'notes' => '2 Internal Officers / Division Leaders'],
                ['id' => 'rule_' . uniqid(), 'name' => 'External Industry Advisors / Board Delegates', 'count' => 2, 'category' => 'EXTERNAL', 'cadre' => 'EXPERT', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'notes' => '2 External Industry Advisors / Specialists from other companies'],
            ];
        } elseif ($preset === 'public_sector') {
            $this->constitution_required_total = 4;
            $this->constitution_required_internal = 2;
            $this->constitution_required_external = 1;
            $this->constitution_required_international = 0;
            $this->constitution_required_independent = 1;
            $this->constitution_required_peer_reviewers = 0;
            $this->constitution_title = 'Public Sector Service Selection Board';
            $this->constitution_description = '2 Internal Departmental Officers, 1 External Public Service Delegate, 1 Independent Legal Arbiter.';
            $this->constitution_rules = [
                ['id' => 'rule_' . uniqid(), 'name' => 'Departmental Representative / Chairman', 'count' => 1, 'category' => 'INTERNAL', 'cadre' => 'OFFICER', 'role_nature' => 'CONVENER', 'is_mandatory' => true, 'notes' => 'Ex-Officio Chairman / Departmental Representative'],
                ['id' => 'rule_' . uniqid(), 'name' => 'Internal Service Member', 'count' => 1, 'category' => 'INTERNAL', 'cadre' => 'OFFICER', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'notes' => 'Internal Civil / Administrative Service Officer'],
                ['id' => 'rule_' . uniqid(), 'name' => 'External Public Service Specialist', 'count' => 1, 'category' => 'EXTERNAL', 'cadre' => 'OFFICER', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'notes' => '1 Public Service Delegate from external agency'],
                ['id' => 'rule_' . uniqid(), 'name' => 'Independent Legal / Governance Arbiter', 'count' => 1, 'category' => 'INDEPENDENT', 'cadre' => 'LEGAL', 'role_nature' => 'ARBITER', 'is_mandatory' => false, 'notes' => '1 Independent Legal or Governance Arbiter'],
            ];
        } elseif ($preset === 'executive_admin') {
            $this->constitution_required_total = 4;
            $this->constitution_required_internal = 2;
            $this->constitution_required_external = 1;
            $this->constitution_required_international = 0;
            $this->constitution_required_independent = 1;
            $this->constitution_required_peer_reviewers = 0;
            $this->constitution_title = 'Executive & Operational Selection Board (Director / Officers)';
            $this->constitution_description = '2 Internal Leadership Officers, 1 External Industry/Office Delegate, 1 Independent Governance Member.';
            $this->constitution_rules = [
                ['id' => 'rule_' . uniqid(), 'name' => 'Internal Director / Convener', 'count' => 1, 'category' => 'INTERNAL', 'cadre' => 'OFFICER', 'role_nature' => 'CONVENER', 'is_mandatory' => true, 'notes' => 'Director / Convener'],
                ['id' => 'rule_' . uniqid(), 'name' => 'Internal Executive Officer', 'count' => 1, 'category' => 'INTERNAL', 'cadre' => 'OFFICER', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'notes' => 'Internal Officer'],
                ['id' => 'rule_' . uniqid(), 'name' => 'External Public / Office Delegate', 'count' => 1, 'category' => 'EXTERNAL', 'cadre' => 'OFFICER', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'notes' => 'External Officer'],
                ['id' => 'rule_' . uniqid(), 'name' => 'Independent Governance Member', 'count' => 1, 'category' => 'INDEPENDENT', 'cadre' => 'LEGAL', 'role_nature' => 'ARBITER', 'is_mandatory' => false, 'notes' => 'Governance Member'],
            ];
        } elseif ($preset === 'expert_heavy') {
            $this->constitution_required_total = 5;
            $this->constitution_required_internal = 1;
            $this->constitution_required_external = 3;
            $this->constitution_required_international = 1;
            $this->constitution_required_independent = 0;
            $this->constitution_required_peer_reviewers = 3;
            $this->constitution_title = 'External-Led Expert Scrutiny Panel';
            $this->constitution_description = '1 Internal Convener, 3 External Subject Experts (Other Universities/Offices), 1 Overseas Specialist.';
            $this->constitution_rules = [
                ['id' => 'rule_' . uniqid(), 'name' => 'Internal Convener / Head', 'count' => 1, 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'CONVENER', 'is_mandatory' => true, 'notes' => 'Internal Convener'],
                ['id' => 'rule_' . uniqid(), 'name' => 'External Subject Experts (Other Universities)', 'count' => 3, 'category' => 'EXTERNAL', 'cadre' => 'EXPERT', 'role_nature' => 'EXPERT', 'is_mandatory' => false, 'notes' => '3 External Experts'],
                ['id' => 'rule_' . uniqid(), 'name' => 'International / Overseas Specialist', 'count' => 1, 'category' => 'INTERNATIONAL', 'cadre' => 'EXPERT', 'role_nature' => 'EXPERT', 'is_mandatory' => false, 'notes' => '1 Overseas Specialist'],
            ];
        }
        $this->recalculateConstitutionTotals();
    }

    public function saveConstitution()
    {
        abort_unless(Auth::user()->can('tune-constitution'), 403);

        $this->validate([
            'selected_campaign_id' => 'required|exists:campaigns,id',
            'constitution_required_total' => 'required|integer|min:1',
            'constitution_required_internal' => 'required|integer|min:0',
            'constitution_required_external' => 'required|integer|min:0',
            'constitution_required_international' => 'required|integer|min:0',
            'constitution_required_independent' => 'required|integer|min:0',
        ]);

        $sum = (int)$this->constitution_required_internal +
               (int)$this->constitution_required_external +
               (int)$this->constitution_required_international +
               (int)$this->constitution_required_independent;

        if ($sum !== (int)$this->constitution_required_total) {
            $this->addError('constitution_required_total', "Category slots sum ({$sum}) does not equal total required members ({$this->constitution_required_total}).");
            return;
        }

        $campaign = Campaign::findOrFail($this->selected_campaign_id);
        if (($campaign->is_completed || $campaign->is_closed) && !Auth::user()->hasRole('super-admin')) {
            $this->addError('constitution_required_total', 'Security Policy: Statutory constitution cannot be altered for a completed or closed campaign.');
            return;
        }

        $rules = $campaign->scoring_rules ?? [];
        $rules['committee_constitution'] = [
            'required_total' => (int)$this->constitution_required_total,
            'required_internal' => (int)$this->constitution_required_internal,
            'required_external' => (int)$this->constitution_required_external,
            'required_international' => (int)$this->constitution_required_international,
            'required_independent' => (int)$this->constitution_required_independent,
            'required_peer_reviewers' => (int)$this->constitution_required_peer_reviewers,
            'title' => $this->constitution_title ?: 'Statutory Committee Constitution',
            'description' => $this->constitution_description ?: 'Statutory quota specification.',
            'enforce_strict_quota' => true,
            'rules' => $this->constitution_rules,
            'roles_breakdown' => $this->constitution_rules,
        ];

        $campaign->update(['scoring_rules' => $rules]);
        $this->isConstitutionModalOpen = false;
        session()->flash('message', 'Statutory Committee Constitution updated successfully for ' . $campaign->title);
    }

    public function getFilteredCandidatesProperty()
    {
        $list = collect($this->committee_users);

        if ($this->candidateFilterSection && $this->candidateFilterSection !== 'ALL') {
            $list = $list->where('category', $this->candidateFilterSection);
        }

        if (!empty($this->candidateSearch)) {
            $term = strtolower(trim($this->candidateSearch));
            $list = $list->filter(function($c) use ($term) {
                return str_contains(strtolower($c['name']), $term) ||
                       str_contains(strtolower($c['email']), $term) ||
                       str_contains(strtolower($c['affiliation']), $term) ||
                       str_contains(strtolower($c['country']), $term) ||
                       str_contains(strtolower($c['subject']), $term) ||
                       str_contains(strtolower($c['rank']), $term);
            });
        }

        return $list->values();
    }

    // ==========================================
    // 👥 COMMITTEE PANEL ASSIGNMENT BREAD
    // ==========================================
    public function assignCommitteeMember()
    {
        abort_unless(Auth::user()->can('assign-committee'), 403);

        // 1. Run validation
        $this->validate([
            'selected_campaign_id' => 'required|exists:campaigns,id',
            'is_appellate_panel' => 'required|boolean',
            'member_category' => 'required|in:INTERNAL,EXTERNAL,INTERNATIONAL,INDEPENDENT',
            'role_designation' => 'nullable|string|max:150',
            'affiliation' => 'nullable|string|max:190',
            'country' => 'nullable|string|max:100',
            'selected_user_id' => [
                'required',
                'exists:users,id',
                new SeparatedCommitteeConflictRule($this->selected_campaign_id, (bool)$this->is_appellate_panel)
            ]
        ], [
            'selected_user_id.required' => 'Please select a committee member from the employee/expert candidate pool.'
        ]);

        // 2. Strict Super-Admin Exclusion Check
        $targetUser = User::findOrFail($this->selected_user_id);
        if ($targetUser->hasRole('super-admin') || str_contains(strtolower($targetUser->email), 'superadmin')) {
            $this->addError('selected_user_id', 'Super Administrators are strictly excluded from operational committee allocations.');
            return;
        }

        // 3. Exact Statutory Quota Check (for Scrutiny / Selection Panel)
        $campaign = Campaign::findOrFail($this->selected_campaign_id);
        if (($campaign->is_completed || $campaign->is_closed) && !Auth::user()->hasRole('super-admin')) {
            $this->addError('selected_user_id', 'Security Policy: Committee allocations are locked because this recruitment campaign is completed or closed.');
            return;
        }

        $metrics = $campaign->getCommitteeMetrics();
        $constitution = $metrics['constitution'];

        if (!$this->is_appellate_panel) {
            $requiredTotal = (int)($constitution['required_total'] ?? 5);
            $scrutinyAssigned = CampaignCommittee::where('campaign_id', $this->selected_campaign_id)
                ->where('is_appellate_panel', false)
                ->get();

            // Check if overall committee quorum is already filled
            if ($scrutinyAssigned->count() >= $requiredTotal) {
                $this->addError('selected_user_id', "Statutory quorum reached: The committee constitution specifies exactly {$requiredTotal} members. All {$requiredTotal} slot(s) are already allocated.");
                return;
            }

            // Check if category quota is exceeded
            $categoryKey = match($this->member_category) {
                'INTERNAL' => 'required_internal',
                'EXTERNAL' => 'required_external',
                'INTERNATIONAL' => 'required_international',
                'INDEPENDENT' => 'required_independent',
                default => 'required_internal',
            };
            $categoryQuota = (int)($constitution[$categoryKey] ?? 0);
            $alreadyAssignedInCategory = $scrutinyAssigned->where('member_category', $this->member_category)->count();

            if ($categoryQuota > 0 && $alreadyAssignedInCategory >= $categoryQuota) {
                $catLabel = $metrics['categories'][$this->member_category]['label'] ?? $this->member_category;
                $this->addError('member_category', "Quota limit reached: The constitution allocates exactly {$categoryQuota} slot(s) for '{$catLabel}'. All {$categoryQuota} slot(s) are already filled.");
                return;
            }
        }

        // 4. Add to table
        CampaignCommittee::create([
            'campaign_id' => $this->selected_campaign_id,
            'user_id' => $this->selected_user_id,
            'is_appellate_panel' => (bool)$this->is_appellate_panel,
            'member_category' => $this->member_category,
            'role_designation' => $this->role_designation ?: 'Committee Member',
            'affiliation' => $this->affiliation ?: 'Home Organization',
            'country' => $this->country ?: 'Domestic / Local',
            'assigned_at' => now(),
        ]);

        session()->flash('message', 'Committee member successfully assigned under statutory criteria.');
        $this->clearSelectedCandidate();
        $this->loadCommitteeData();
    }

    public function removeCommitteeAssignment($id)
    {
        abort_unless(Auth::user()->can('remove-committee'), 403);
        $assignment = CampaignCommittee::with('campaign')->findOrFail($id);
        if ($assignment->campaign && ($assignment->campaign->is_completed || $assignment->campaign->is_closed) && !Auth::user()->hasRole('super-admin')) {
            session()->flash('error', 'Security Policy: Committee allocations for completed or closed campaigns are locked.');
            return;
        }

        CampaignCommittee::destroy($id);
        session()->flash('message', 'Committee member de-assigned successfully.');
        $this->loadCommitteeData();
    }

    public function exportCsv()
    {
        abort_unless(Auth::user()->can('view-campaign') || Auth::user()->can('export-campaign'), 403);
        $camps = Campaign::orderBy('created_at', 'desc')->get();
        $headers = ['Job Code', 'Title', 'Department', 'Track Type', 'Fee (PKR)', 'Opening Date', 'Closing Date'];
        $rows = [];
        foreach ($camps as $c) {
            $rows[] = [
                $c->job_code,
                $c->title,
                $c->department,
                $c->track_type,
                $c->application_fee,
                $c->opening_date?->format('Y-m-d') ?? '',
                $c->closing_date?->format('Y-m-d') ?? '',
            ];
        }
        return $this->streamCsvDownload('recruitment-campaigns-' . date('Y-m-d') . '.csv', $headers, $rows);
    }

    public function openLinkModal($campaignId)
    {
        abort_unless(Auth::user()->can('edit-campaign') || Auth::user()->can('tune-constitution'), 403);

        $camp = Campaign::findOrFail($campaignId);
        if (($camp->is_completed || $camp->is_closed) && !Auth::user()->hasRole('super-admin')) {
            session()->flash('error', 'Security Policy: Statutory Board & Process Flow cannot be altered for a completed or closed campaign.');
            return;
        }

        $this->linkCampaignId = $camp->id;
        $this->link_constitution_id = $camp->constitution_id;
        $this->link_process_flow_id = $camp->process_flow_id;
        $this->showLinkModal = true;
    }

    public function closeLinkModal()
    {
        $this->showLinkModal = false;
        $this->linkCampaignId = null;
        $this->link_constitution_id = null;
        $this->link_process_flow_id = null;
    }

    public function saveLinkedBoardAndFlow()
    {
        abort_unless(Auth::user()->can('edit-campaign') || Auth::user()->can('tune-constitution'), 403);

        if (!$this->linkCampaignId) return;
        $camp = Campaign::findOrFail($this->linkCampaignId);
        if (($camp->is_completed || $camp->is_closed) && !Auth::user()->hasRole('super-admin')) {
            session()->flash('error', 'Security Policy: Completed or closed campaigns cannot have their Board or Flow modified.');
            $this->closeLinkModal();
            return;
        }

        $camp->update([
            'constitution_id' => $this->link_constitution_id ?: null,
            'process_flow_id' => $this->link_process_flow_id ?: null,
        ]);
        session()->flash('message', "Successfully linked Statutory Board Constitution and Process Flow to {$camp->title}.");
        $this->loadCampaigns();
        $this->closeLinkModal();
    }

    public function toggleCampaignStatus($campaignId)
    {
        abort_unless(Auth::user()->can('edit-campaign'), 403);
        $camp = Campaign::findOrFail($campaignId);

        $isDeadlineExpired = (bool)($camp->closing_date && now()->gt($camp->closing_date));
        if ($isDeadlineExpired && !$camp->is_active && !Auth::user()->hasRole('super-admin')) {
            session()->flash('error', 'Security Policy: Re-activating an expired/closed campaign requires Super Admin authorization or an extended closing date.');
            return;
        }

        $camp->is_active = !$camp->is_active;
        $camp->save();
        $this->loadCampaigns();

        session()->flash('message', "Campaign '{$camp->title}' status toggled to " . ($camp->is_active ? 'ACTIVE' : 'INACTIVE / SUSPENDED') . ".");
    }

    public function render()
    {
        $campQuery = Campaign::with(['advertisement', 'constitution', 'processFlow', 'applications']);
        if (!empty($this->search)) {
            $s = '%' . trim($this->search) . '%';
            $campQuery->where(function($q) use ($s) {
                $q->where('title', 'like', $s)
                  ->orWhere('job_code', 'like', $s)
                  ->orWhere('department', 'like', $s)
                  ->orWhereHas('advertisement', function($aq) use ($s) {
                      $aq->where('advertisement_no', 'like', $s)
                         ->orWhere('title', 'like', $s);
                  });
            });
        }

        if (!empty($this->yearFilter)) {
            $year = (int)$this->yearFilter;
            $campQuery->where(function($q) use ($year) {
                $q->whereHas('advertisement', fn($aq) => $aq->where('year', $year))
                  ->orWhere(function($oq) use ($year) {
                      $oq->whereNull('advertisement_id')->whereYear('opening_date', $year);
                  });
            });
        }

        if (!empty($this->advertisementFilter)) {
            if ($this->advertisementFilter === 'NONE') {
                $campQuery->whereNull('advertisement_id');
            } else {
                $campQuery->where('advertisement_id', $this->advertisementFilter);
            }
        }

        if (!empty($this->trackFilter) && $this->trackFilter !== 'ALL') {
            $campQuery->where('track_type', $this->trackFilter);
        }

        if ($this->statusFilter === 'ACTIVE') {
            $campQuery->where(function($q) {
                $q->where('is_active', true)->where('closing_date', '>=', now());
            });
        } elseif ($this->statusFilter === 'CLOSED') {
            $campQuery->where(function($q) {
                $q->where('is_active', false)->orWhere('closing_date', '<', now());
            });
        }

        $paginatedCampaigns = $campQuery->orderBy('created_at', 'desc')->paginate($this->perPage);

        // Distinct years from advertisements and campaigns
        $adYears = Advertisement::distinct()->whereNotNull('year')->pluck('year')->map(fn($y) => (int)$y)->all();
        $campYears = Campaign::whereNotNull('opening_date')->pluck('opening_date')->map(fn($d) => (int)\Carbon\Carbon::parse($d)->year)->all();
        $availableYears = collect(array_unique(array_merge($adYears, $campYears, [(int)date('Y')])))->sortDesc()->values();

        // Filter advertisements dropdown (filtered by year if yearFilter is active)
        $filterAdQuery = Advertisement::withCount('campaigns')->orderBy('year', 'desc')->orderBy('advertisement_no');
        if (!empty($this->yearFilter)) {
            $filterAdQuery->where('year', (int)$this->yearFilter);
        }
        $filterAdvertisements = $filterAdQuery->get();

        $selectedAdModel = (!empty($this->advertisementFilter) && $this->advertisementFilter !== 'NONE')
            ? Advertisement::withCount('campaigns')->find($this->advertisementFilter)
            : null;

        return view('livewire.admin.campaign-crud', [
            'paginatedCampaigns' => $paginatedCampaigns,
            'statutoryBoards' => CommitteeConstitution::orderBy('name')->get(),
            'processFlows' => RecruitmentProcessFlow::orderBy('name')->get(),
            'advertisements' => Advertisement::orderBy('year', 'desc')->orderBy('title')->get(),
            'filterAdvertisements' => $filterAdvertisements,
            'availableYears' => $availableYears,
            'selectedAdModel' => $selectedAdModel,
        ])->layout('layouts.app');
    }
}
