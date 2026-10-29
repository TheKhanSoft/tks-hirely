<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Auth;

class SystemSettings extends Component
{
    public $activeTab = 'pipeline'; // 'pipeline', 'documents', 'candidate_rules', 'organization'

    // ⚡ Pipeline Toggles
    public $enable_peer_review_step;
    public $enable_scrutiny_step;
    public $enable_appellate_step;
    public $enable_board_interview_step;
    public $enforce_strict_committee_quorum;
    public $enforce_strict_peer_review_quorum;

    // 📑 Document Request Toggles
    public $require_transcript_proof;
    public $require_experience_proof;
    public $require_cnic_upload;
    public $require_noc_employed;
    public $require_publications_academic;
    public $require_challan_receipt_upload;

    // 👥 Candidate & Application Rules
    public $allow_multiple_applications;
    public $prevent_duplicate_campaign_applications;
    public $challan_grace_days;
    public $auto_lock_profile_on_voucher;

    // 🏢 Organization Profile
    public $org_name;
    public $org_type;
    public $sector_label;
    public $currency_symbol;
    public $default_application_fee;

    // ⚖️ Statutory Quota Defaults
    public $asst_prof_required_peer_reviewers;
    public $prof_required_peer_reviewers;
    public $lecturer_required_peer_reviewers;

    public function mount()
    {
        abort_unless(Auth::check() && (Auth::user()->hasRole('super-admin') || Auth::user()->can('manage-settings')), 403);
        $this->loadSettings();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function loadSettings()
    {
        // Pipeline
        $this->enable_peer_review_step = (bool) SystemSetting::get('enable_peer_review_step', true);
        $this->enable_scrutiny_step = (bool) SystemSetting::get('enable_scrutiny_step', true);
        $this->enable_appellate_step = (bool) SystemSetting::get('enable_appellate_step', true);
        $this->enable_board_interview_step = (bool) SystemSetting::get('enable_board_interview_step', true);
        $this->enforce_strict_committee_quorum = (bool) SystemSetting::get('enforce_strict_committee_quorum', true);
        $this->enforce_strict_peer_review_quorum = (bool) SystemSetting::get('enforce_strict_peer_review_quorum', false);

        // Documents
        $this->require_transcript_proof = (bool) SystemSetting::get('require_transcript_proof', true);
        $this->require_experience_proof = (bool) SystemSetting::get('require_experience_proof', true);
        $this->require_cnic_upload = (bool) SystemSetting::get('require_cnic_upload', true);
        $this->require_noc_employed = (bool) SystemSetting::get('require_noc_employed', false);
        $this->require_publications_academic = (bool) SystemSetting::get('require_publications_academic', true);
        $this->require_challan_receipt_upload = (bool) SystemSetting::get('require_challan_receipt_upload', false);

        // Candidate Rules
        $this->allow_multiple_applications = (bool) SystemSetting::get('allow_multiple_applications', true);
        $this->prevent_duplicate_campaign_applications = (bool) SystemSetting::get('prevent_duplicate_campaign_applications', true);
        $this->challan_grace_days = (int) SystemSetting::get('challan_grace_days', 14);
        $this->auto_lock_profile_on_voucher = (bool) SystemSetting::get('auto_lock_profile_on_voucher', true);

        // Organization
        $this->org_name = SystemSetting::get('org_name', 'Hirely Recruitment Management System');
        $this->org_type = SystemSetting::get('org_type', 'HIGHER_EDUCATION');
        $this->sector_label = SystemSetting::get('sector_label', 'Department / Division');
        $this->currency_symbol = SystemSetting::get('currency_symbol', 'PKR');
        $this->default_application_fee = (int) SystemSetting::get('default_application_fee', 3000);

        // Statutory Quotas
        $this->asst_prof_required_peer_reviewers = (int) SystemSetting::get('asst_prof_required_peer_reviewers', 3);
        $this->prof_required_peer_reviewers = (int) SystemSetting::get('prof_required_peer_reviewers', 3);
        $this->lecturer_required_peer_reviewers = (int) SystemSetting::get('lecturer_required_peer_reviewers', 0);
    }

    public function toggleSetting($key)
    {
        $current = (bool) SystemSetting::get($key, false);
        $new = !$current;
        SystemSetting::set($key, $new, type: 'boolean');
        $this->$key = $new;
        session()->flash('message', "Option '" . ucwords(str_replace('_', ' ', $key)) . "' updated to " . ($new ? 'Enabled (ON)' : 'Disabled (OFF)') . ".");
    }

    public function savePipelineSettings()
    {
        SystemSetting::set('enable_peer_review_step', $this->enable_peer_review_step, 'pipeline', 'boolean');
        SystemSetting::set('enable_scrutiny_step', $this->enable_scrutiny_step, 'pipeline', 'boolean');
        SystemSetting::set('enable_appellate_step', $this->enable_appellate_step, 'pipeline', 'boolean');
        SystemSetting::set('enable_board_interview_step', $this->enable_board_interview_step, 'pipeline', 'boolean');
        SystemSetting::set('enforce_strict_committee_quorum', $this->enforce_strict_committee_quorum, 'pipeline', 'boolean');
        SystemSetting::set('enforce_strict_peer_review_quorum', $this->enforce_strict_peer_review_quorum, 'pipeline', 'boolean');

        session()->flash('message', 'Workflow pipeline stage settings successfully saved.');
    }

    public function saveDocumentSettings()
    {
        SystemSetting::set('require_transcript_proof', $this->require_transcript_proof, 'documents', 'boolean');
        SystemSetting::set('require_experience_proof', $this->require_experience_proof, 'documents', 'boolean');
        SystemSetting::set('require_cnic_upload', $this->require_cnic_upload, 'documents', 'boolean');
        SystemSetting::set('require_noc_employed', $this->require_noc_employed, 'documents', 'boolean');
        SystemSetting::set('require_publications_academic', $this->require_publications_academic, 'documents', 'boolean');
        SystemSetting::set('require_challan_receipt_upload', $this->require_challan_receipt_upload, 'documents', 'boolean');

        session()->flash('message', 'Candidate document requests and upload rules successfully updated.');
    }

    public function saveCandidateRules()
    {
        $this->validate([
            'challan_grace_days' => 'required|integer|min:1|max:90',
        ]);

        SystemSetting::set('allow_multiple_applications', $this->allow_multiple_applications, 'candidate_rules', 'boolean');
        SystemSetting::set('prevent_duplicate_campaign_applications', $this->prevent_duplicate_campaign_applications, 'candidate_rules', 'boolean');
        SystemSetting::set('challan_grace_days', $this->challan_grace_days, 'candidate_rules', 'integer');
        SystemSetting::set('auto_lock_profile_on_voucher', $this->auto_lock_profile_on_voucher, 'candidate_rules', 'boolean');

        session()->flash('message', 'Candidate application and voucher settlement rules updated.');
    }

    public function saveOrganizationProfile()
    {
        $this->validate([
            'org_name' => 'required|string|max:190',
            'org_type' => 'required|string',
            'sector_label' => 'required|string|max:100',
            'currency_symbol' => 'required|string|max:10',
            'default_application_fee' => 'required|integer|min:0',
        ]);

        SystemSetting::set('org_name', $this->org_name, 'organization', 'string');
        SystemSetting::set('org_type', $this->org_type, 'organization', 'string');
        SystemSetting::set('sector_label', $this->sector_label, 'organization', 'string');
        SystemSetting::set('currency_symbol', $this->currency_symbol, 'organization', 'string');
        SystemSetting::set('default_application_fee', $this->default_application_fee, 'organization', 'integer');

        session()->flash('message', 'Organization sector profile and system nomenclature updated.');
    }

    public function saveConstitutions()
    {
        $this->validate([
            'asst_prof_required_peer_reviewers' => 'required|integer|min:1|max:10',
            'prof_required_peer_reviewers' => 'required|integer|min:1|max:10',
            'lecturer_required_peer_reviewers' => 'required|integer|min:0|max:10',
        ]);

        SystemSetting::set('asst_prof_required_peer_reviewers', $this->asst_prof_required_peer_reviewers, 'constitution_defaults', 'integer');
        SystemSetting::set('prof_required_peer_reviewers', $this->prof_required_peer_reviewers, 'constitution_defaults', 'integer');
        SystemSetting::set('lecturer_required_peer_reviewers', $this->lecturer_required_peer_reviewers, 'constitution_defaults', 'integer');

        session()->flash('message', 'Default statutory board quorums and peer review quotas successfully saved.');
    }

    public function render()
    {
        return view('livewire.admin.system-settings')
            ->layout('layouts.app', ['title' => 'System & Workflow Settings - Hirely']);
    }
}
