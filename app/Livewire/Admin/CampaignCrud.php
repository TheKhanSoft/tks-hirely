<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Campaign;
use App\Models\User;
use App\Models\CampaignCommittee;
use App\Rules\SeparatedCommitteeConflictRule;
use Illuminate\Support\Facades\Auth;

class CampaignCrud extends Component
{
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
    
    // Lists
    public $committee_users;
    public $assignments;

    // View states
    public $isFormOpen = false;
    public $isEditMode = false;
    public $activeTab = 'campaigns'; // 'campaigns' or 'committees'

    public function mount()
    {
        abort_unless(Auth::check() && Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
        $this->loadCampaigns();
        $this->loadCommitteeData();
    }

    public function loadCampaigns()
    {
        $this->campaigns = Campaign::all();
    }

    public function loadCommitteeData()
    {
        // Get all users who can serve on committees (scrutiny, appellate, selection board, super-admin)
        $this->committee_users = User::role(['scrutiny-committee', 'appellate-committee', 'selection-board-member', 'super-admin'])
            ->where('is_active', true)
            ->get();

        // Get all current campaign assignments
        $this->assignments = CampaignCommittee::with('campaign', 'user.roles')->get();
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
    public function openCreateForm()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->isFormOpen = true;
    }

    public function openEditForm($id)
    {
        $this->resetForm();
        $this->campaign_id = $id;
        $campaign = Campaign::findOrFail($id);
        
        $this->job_code = $campaign->job_code;
        $this->title = $campaign->title;
        $this->track_type = $campaign->track_type;
        $this->department = $campaign->department;
        $this->opening_date = $campaign->opening_date?->format('Y-m-d\TH:i');
        $this->closing_date = $campaign->closing_date?->format('Y-m-d\TH:i');
        $this->application_fee = $campaign->application_fee;

        // Load rule map
        $rules = $campaign->scoring_rules ?? [];
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
        $rules = [
            'job_code' => 'required|string|max:50|unique:campaigns,job_code,' . ($this->campaign_id ?? 'NULL'),
            'title' => 'required|string|max:255',
            'track_type' => 'required|in:ACADEMIC,ADMINISTRATIVE,SUPPORT',
            'department' => 'required|string|max:150',
            'opening_date' => 'required',
            'closing_date' => 'required',
            'application_fee' => 'required|numeric|min:0',
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

        $data = [
            'job_code' => $this->job_code,
            'title' => $this->title,
            'track_type' => $this->track_type,
            'department' => $this->department,
            'opening_date' => $this->opening_date,
            'closing_date' => $this->closing_date,
            'application_fee' => $this->application_fee,
            'require_transcript_proof' => (bool)$this->require_transcript_proof,
            'scoring_rules' => $scoringRules,
        ];

        if ($this->isEditMode) {
            Campaign::findOrFail($this->campaign_id)->update($data);
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
        Campaign::destroy($id);
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

    // ==========================================
    // 👥 COMMITTEE PANEL ASSIGNMENT BREAD
    // ==========================================
    public function assignCommitteeMember()
    {
        // 1. Run validation
        $this->validate([
            'selected_campaign_id' => 'required|exists:campaigns,id',
            'is_appellate_panel' => 'required|boolean',
            'selected_user_id' => [
                'required',
                'exists:users,id',
                // Custom operational seeder logic rule check
                new SeparatedCommitteeConflictRule($this->selected_campaign_id, $this->is_appellate_panel)
            ]
        ], [
            'selected_user_id.required' => 'Please select a committee member to assign.'
        ]);

        // 2. Add to table
        CampaignCommittee::create([
            'campaign_id' => $this->selected_campaign_id,
            'user_id' => $this->selected_user_id,
            'is_appellate_panel' => (bool)$this->is_appellate_panel,
        ]);

        session()->flash('message', 'Committee member assigned successfully.');
        $this->resetForm();
        $this->loadCommitteeData();
    }

    public function removeCommitteeAssignment($id)
    {
        CampaignCommittee::destroy($id);
        session()->flash('message', 'Committee member de-assigned successfully.');
        $this->loadCommitteeData();
    }

    public function render()
    {
        return view('livewire.admin.campaign-crud')
            ->layout('layouts.app');
    }
}
