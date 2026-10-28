<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\User;
use App\Models\Campaign;
use App\Models\CampaignCommittee;
use App\Models\Application;
use App\Models\BasicInfo;
use App\Models\BillingLedger;
use App\Models\SecurityAuditLog;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Rules\SeparatedCommitteeConflictRule;

class EntityManagement extends Component
{
    use WithFileUploads;

    public $activeTab = 'users'; // 'users', 'roles', 'permissions', 'campaigns', 'applications', 'basic_infos', 'payments', 'logs'
    
    // View state toggles
    public $isFormOpen = false;
    public $isEditMode = false;
    public $isDetailsOpen = false;
    public $selectedRecordId;
    public $selectedRecordDetails = null;

    // Collections
    public $users;
    public $roles;
    public $permissions;
    public $campaigns;
    public $applications;
    public $basic_infos;
    public $payments;
    public $logs;
    
    // Committee Assignment Helpers
    public $committee_users;
    public $committee_assignments;
    public $selected_campaign_id;
    public $selected_user_id;
    public $is_appellate_panel = false;

    // ==========================================
    // ENTITY FIELDS
    // ==========================================
    
    // 1. User fields
    public $user_id;
    public $email;
    public $password;
    public $role_name = 'applicant';
    public $is_active = true;

    // 2. Role fields
    public $role_id;
    public $role_name_input;
    public $selected_permissions = [];

    // 3. Permission fields
    public $permission_id;
    public $permission_name_input;

    // 4. Campaign fields
    public $campaign_id;
    public $job_code;
    public $title;
    public $track_type = 'ACADEMIC';
    public $department;
    public $opening_date;
    public $closing_date;
    public $application_fee = 1000.00;
    // Campaign Weights
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

    // 5. Application fields
    public $app_id;
    public $app_user_id;
    public $app_campaign_id;
    public $app_status = 'DRAFT';
    public $app_disqualification_reason;

    // 6. basicInfo fields
    public $bi_id;
    public $bi_user_id;
    public $bi_track_type = 'ACADEMIC';
    public $bi_first_name;
    public $bi_last_name;
    public $bi_cnic_number;
    public $bi_dob;
    public $bi_is_locked = false;

    // 7. Payment (Billing Ledger) fields
    public $pay_id;
    public $pay_app_id;
    public $pay_consumer_id;
    public $pay_amount;
    public $pay_status = 'PENDING';
    public $pay_transaction_id;
    public $pay_timestamp;

    public function mount()
    {
        abort_unless(Auth::check() && Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
        
        // Grab tab parameter if set
        $tab = request()->query('tab');
        if (in_array($tab, ['users', 'roles', 'permissions', 'campaigns', 'applications', 'basic_infos', 'payments', 'logs'])) {
            $this->activeTab = $tab;
        }
        
        $this->loadTabSpecificData();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->isFormOpen = false;
        $this->isDetailsOpen = false;
        $this->selectedRecordDetails = null;
        $this->resetForms();
        $this->loadTabSpecificData();
    }

    public function loadTabSpecificData()
    {
        if ($this->activeTab === 'users') {
            abort_unless(Auth::user()->can('view-user'), 403);
            $this->users = User::with('roles')->get();
            $this->roles = Role::all();
        } elseif ($this->activeTab === 'roles') {
            abort_unless(Auth::user()->can('view-role'), 403);
            $this->roles = Role::with('permissions', 'users')->get();
            $this->permissions = Permission::all();
        } elseif ($this->activeTab === 'permissions') {
            abort_unless(Auth::user()->can('view-permission'), 403);
            $this->permissions = Permission::all();
        } elseif ($this->activeTab === 'campaigns') {
            abort_unless(Auth::user()->can('view-campaign'), 403);
            $this->campaigns = Campaign::all();
            $this->committee_users = User::role(['scrutiny-committee', 'appellate-committee', 'selection-board-member', 'super-admin'])
                ->where('is_active', true)
                ->get();
            $this->committee_assignments = CampaignCommittee::with('campaign', 'user.roles')->get();
        } elseif ($this->activeTab === 'applications') {
            abort_unless(Auth::user()->can('view-application'), 403);
            $this->applications = Application::with('user.basicInfo', 'campaign', 'billingLedger')->get();
            $this->users = User::role('applicant')->get();
            $this->campaigns = Campaign::where('is_active', true)->get();
        } elseif ($this->activeTab === 'basic_infos') {
            abort_unless(Auth::user()->can('view-basic-info'), 403);
            $this->basic_infos = BasicInfo::with('user')->get();
            $this->users = User::doesntHave('basicInfo')->get();
        } elseif ($this->activeTab === 'payments') {
            abort_unless(Auth::user()->can('view-payment'), 403);
            $this->payments = BillingLedger::with('application.user.basicInfo', 'application.campaign')->get();
            $this->applications = Application::doesntHave('billingLedger')->get();
        } elseif ($this->activeTab === 'logs') {
            abort_unless(Auth::user()->can('view-audit-log'), 403);
            $this->logs = SecurityAuditLog::with('user')->orderBy('logged_at', 'desc')->take(200)->get();
        }
    }

    public function resetForms()
    {
        $this->reset([
            'user_id', 'email', 'password', 'role_name', 'is_active',
            'role_id', 'role_name_input', 'selected_permissions',
            'permission_id', 'permission_name_input',
            'campaign_id', 'job_code', 'title', 'track_type', 'department', 'opening_date', 'closing_date', 'application_fee',
            'selected_campaign_id', 'selected_user_id', 'is_appellate_panel',
            'app_id', 'app_user_id', 'app_campaign_id', 'app_status', 'app_disqualification_reason',
            'bi_id', 'bi_user_id', 'bi_track_type', 'bi_first_name', 'bi_last_name', 'bi_cnic_number', 'bi_dob', 'bi_is_locked',
            'pay_id', 'pay_app_id', 'pay_consumer_id', 'pay_amount', 'pay_status', 'pay_transaction_id', 'pay_timestamp'
        ]);
        $this->role_name = 'applicant';
        $this->track_type = 'ACADEMIC';
        $this->bi_track_type = 'ACADEMIC';
        $this->pay_status = 'PENDING';
        $this->app_status = 'DRAFT';
        $this->is_active = true;
    }

    // ==========================================
    // 👥 USERS BREAD WORKFLOWS
    // ==========================================
    public function openCreateUserForm()
    {
        abort_unless(Auth::user()->can('create-user'), 403);
        $this->resetForms();
        $this->isEditMode = false;
        $this->isFormOpen = true;
    }

    public function openEditUserForm($id)
    {
        abort_unless(Auth::user()->can('edit-user'), 403);
        $this->resetForms();
        $this->user_id = $id;
        $user = User::findOrFail($id);
        $this->email = $user->email;
        $this->is_active = (bool)$user->is_active;
        $this->role_name = $user->roles->first()?->name ?? 'applicant';
        $this->isEditMode = true;
        $this->isFormOpen = true;
    }

    public function saveUser()
    {
        if ($this->isEditMode) {
            abort_unless(Auth::user()->can('edit-user'), 403);
            $this->validate([
                'email' => 'required|email|unique:users,email,' . $this->user_id,
                'role_name' => 'required|exists:roles,name',
                'password' => 'nullable|string|min:6',
            ]);

            $user = User::findOrFail($this->user_id);
            $data = ['email' => $this->email, 'is_active' => $this->is_active];
            if (!empty($this->password)) {
                $data['password'] = Hash::make($this->password);
            }
            $user->update($data);
            $user->syncRoles($this->role_name);
            session()->flash('message', 'User account updated successfully.');
        } else {
            abort_unless(Auth::user()->can('create-user'), 403);
            $this->validate([
                'email' => 'required|email|unique:users,email',
                'role_name' => 'required|exists:roles,name',
                'password' => 'required|string|min:6',
            ]);

            $user = User::create([
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'is_active' => $this->is_active,
            ]);
            $user->assignRole($this->role_name);
            session()->flash('message', 'User registered successfully.');
        }

        $this->isFormOpen = false;
        $this->loadTabSpecificData();
    }

    public function deleteUser($id)
    {
        abort_unless(Auth::user()->can('delete-user'), 403);
        if (Auth::id() == $id) {
            session()->flash('error', 'Safety gate: You cannot delete your logged in session.');
            return;
        }
        User::destroy($id);
        session()->flash('message', 'User deleted successfully.');
        $this->loadTabSpecificData();
    }

    public function toggleUserStatus($id)
    {
        abort_unless(Auth::user()->can('edit-user'), 403);
        if (Auth::id() == $id) {
            session()->flash('error', 'Safety gate: You cannot deactivate your active session.');
            return;
        }
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);
        session()->flash('message', 'User active status updated.');
        $this->loadTabSpecificData();
    }

    // ==========================================
    // 🛡️ ROLES BREAD WORKFLOWS
    // ==========================================
    public function openCreateRoleForm()
    {
        abort_unless(Auth::user()->can('create-role'), 403);
        $this->resetForms();
        $this->isEditMode = false;
        $this->isFormOpen = true;
    }

    public function openEditRoleForm($id)
    {
        abort_unless(Auth::user()->can('edit-role'), 403);
        $this->resetForms();
        $this->role_id = $id;
        $role = Role::findOrFail($id);
        $this->role_name_input = $role->name;
        $this->selected_permissions = $role->permissions->pluck('name')->toArray();
        $this->isEditMode = true;
        $this->isFormOpen = true;
    }

    public function saveRole()
    {
        if ($this->isEditMode) {
            abort_unless(Auth::user()->can('edit-role'), 403);
            $this->validate([
                'role_name_input' => 'required|string|unique:roles,name,' . $this->role_id,
            ]);
            $role = Role::findOrFail($this->role_id);
            $role->update(['name' => $this->role_name_input]);
            $role->syncPermissions($this->selected_permissions);
            session()->flash('message', 'Role permissions updated.');
        } else {
            abort_unless(Auth::user()->can('create-role'), 403);
            $this->validate([
                'role_name_input' => 'required|string|unique:roles,name',
            ]);
            $role = Role::create(['name' => $this->role_name_input, 'guard_name' => 'web']);
            $role->syncPermissions($this->selected_permissions);
            session()->flash('message', 'Role created successfully.');
        }

        $this->isFormOpen = false;
        $this->loadTabSpecificData();
    }

    public function deleteRole($id)
    {
        abort_unless(Auth::user()->can('delete-role'), 403);
        $role = Role::findOrFail($id);
        if (in_array($role->name, ['super-admin', 'registrar', 'applicant'])) {
            session()->flash('error', 'Core roles are system locked and cannot be deleted.');
            return;
        }
        $role->delete();
        session()->flash('message', 'Role deleted successfully.');
        $this->loadTabSpecificData();
    }

    // ==========================================
    // 🔑 PERMISSIONS BREAD WORKFLOWS
    // ==========================================
    public function openCreatePermissionForm()
    {
        abort_unless(Auth::user()->can('create-permission'), 403);
        $this->resetForms();
        $this->isEditMode = false;
        $this->isFormOpen = true;
    }

    public function openEditPermissionForm($id)
    {
        abort_unless(Auth::user()->can('edit-permission'), 403);
        $this->resetForms();
        $this->permission_id = $id;
        $perm = Permission::findOrFail($id);
        $this->permission_name_input = $perm->name;
        $this->isEditMode = true;
        $this->isFormOpen = true;
    }

    public function savePermission()
    {
        if ($this->isEditMode) {
            abort_unless(Auth::user()->can('edit-permission'), 403);
            $this->validate([
                'permission_name_input' => 'required|string|unique:permissions,name,' . $this->permission_id,
            ]);
            $perm = Permission::findOrFail($this->permission_id);
            $perm->update(['name' => $this->permission_name_input]);
            session()->flash('message', 'Permission updated.');
        } else {
            abort_unless(Auth::user()->can('create-permission'), 403);
            $this->validate([
                'permission_name_input' => 'required|string|unique:permissions,name',
            ]);
            Permission::create(['name' => $this->permission_name_input, 'guard_name' => 'web']);
            session()->flash('message', 'Permission registered.');
        }

        $this->isFormOpen = false;
        $this->loadTabSpecificData();
    }

    public function deletePermission($id)
    {
        abort_unless(Auth::user()->can('delete-permission'), 403);
        Permission::destroy($id);
        session()->flash('message', 'Permission deleted.');
        $this->loadTabSpecificData();
    }

    // ==========================================
    // 📢 CAMPAIGNS BREAD WORKFLOWS
    // ==========================================
    public function openCreateCampaignForm()
    {
        abort_unless(Auth::user()->can('create-campaign'), 403);
        $this->resetForms();
        $this->isEditMode = false;
        $this->isFormOpen = true;
    }

    public function openEditCampaignForm($id)
    {
        abort_unless(Auth::user()->can('edit-campaign'), 403);
        $this->resetForms();
        $this->campaign_id = $id;
        $camp = Campaign::findOrFail($id);
        $this->job_code = $camp->job_code;
        $this->title = $camp->title;
        $this->track_type = $camp->track_type;
        $this->department = $camp->department;
        $this->opening_date = $camp->opening_date?->format('Y-m-d\TH:i');
        $this->closing_date = $camp->closing_date?->format('Y-m-d\TH:i');
        $this->application_fee = $camp->application_fee;
        
        $rules = $camp->scoring_rules ?? [];
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
        ];

        $data = [
            'job_code' => $this->job_code,
            'title' => $this->title,
            'track_type' => $this->track_type,
            'department' => $this->department,
            'opening_date' => $this->opening_date,
            'closing_date' => $this->closing_date,
            'application_fee' => $this->application_fee,
            'scoring_rules' => $scoringRules,
        ];

        if ($this->isEditMode) {
            abort_unless(Auth::user()->can('edit-campaign'), 403);
            Campaign::findOrFail($this->campaign_id)->update($data);
            session()->flash('message', 'Campaign updated successfully.');
        } else {
            abort_unless(Auth::user()->can('create-campaign'), 403);
            Campaign::create($data);
            session()->flash('message', 'Campaign created successfully.');
        }

        $this->isFormOpen = false;
        $this->loadTabSpecificData();
    }

    public function deleteCampaign($id)
    {
        abort_unless(Auth::user()->can('delete-campaign'), 403);
        Campaign::destroy($id);
        session()->flash('message', 'Campaign deleted.');
        $this->loadTabSpecificData();
    }

    public function assignCommitteeMember()
    {
        abort_unless(Auth::user()->can('assign-committee'), 403);
        $this->validate([
            'selected_campaign_id' => 'required|exists:campaigns,id',
            'is_appellate_panel' => 'required|boolean',
            'selected_user_id' => [
                'required',
                'exists:users,id',
                new SeparatedCommitteeConflictRule($this->selected_campaign_id, $this->is_appellate_panel)
            ]
        ]);

        CampaignCommittee::create([
            'campaign_id' => $this->selected_campaign_id,
            'user_id' => $this->selected_user_id,
            'is_appellate_panel' => (bool)$this->is_appellate_panel,
        ]);

        session()->flash('message', 'Committee member assigned successfully.');
        $this->resetForms();
        $this->loadTabSpecificData();
    }

    public function removeCommitteeAssignment($id)
    {
        abort_unless(Auth::user()->can('remove-committee'), 403);
        CampaignCommittee::destroy($id);
        session()->flash('message', 'Committee member assignment removed.');
        $this->loadTabSpecificData();
    }

    // ==========================================
    // 📁 APPLICATIONS BREAD WORKFLOWS
    // ==========================================
    public function openCreateApplicationForm()
    {
        abort_unless(Auth::user()->can('create-application'), 403);
        $this->resetForms();
        $this->isEditMode = false;
        $this->isFormOpen = true;
    }

    public function openEditApplicationForm($id)
    {
        abort_unless(Auth::user()->can('edit-application'), 403);
        $this->resetForms();
        $this->app_id = $id;
        $app = Application::findOrFail($id);
        $this->app_user_id = $app->user_id;
        $this->app_campaign_id = $app->campaign_id;
        $this->app_status = $app->status;
        $this->app_disqualification_reason = $app->disqualification_reason;
        $this->isEditMode = true;
        $this->isFormOpen = true;
    }

    public function saveApplication()
    {
        if ($this->isEditMode) {
            abort_unless(Auth::user()->can('edit-application'), 403);
            $app = Application::findOrFail($this->app_id);
            $app->update([
                'status' => $this->app_status,
                'disqualification_reason' => $this->app_disqualification_reason,
            ]);
            session()->flash('message', 'Application updated.');
        } else {
            abort_unless(Auth::user()->can('create-application'), 403);
            $this->validate([
                'app_user_id' => 'required|exists:users,id',
                'app_campaign_id' => 'required|exists:campaigns,id',
                'app_status' => 'required',
            ]);
            Application::create([
                'user_id' => $this->app_user_id,
                'campaign_id' => $this->app_campaign_id,
                'status' => $this->app_status,
            ]);
            session()->flash('message', 'Application registered successfully.');
        }

        $this->isFormOpen = false;
        $this->loadTabSpecificData();
    }

    public function deleteApplication($id)
    {
        abort_unless(Auth::user()->can('delete-application'), 403);
        Application::destroy($id);
        session()->flash('message', 'Application deleted.');
        $this->loadTabSpecificData();
    }

    public function viewApplicationDetails($id)
    {
        abort_unless(Auth::user()->can('view-application'), 403);
        $this->selectedRecordId = $id;
        $app = Application::with('user.basicInfo', 'campaign', 'billingLedger', 'evaluations.boardMember')->findOrFail($id);
        $this->selectedRecordDetails = $app;
        $this->isDetailsOpen = true;
    }

    // ==========================================
    // 👤 basic_infos BREAD WORKFLOWS
    // ==========================================
    public function openCreateBasicInfoForm()
    {
        abort_unless(Auth::user()->can('create-basic-info'), 403);
        $this->resetForms();
        $this->isEditMode = false;
        $this->isFormOpen = true;
    }

    public function openEditBasicInfoForm($id)
    {
        abort_unless(Auth::user()->can('edit-basic-info'), 403);
        $this->resetForms();
        $this->bi_id = $id;
        $basicInfo = BasicInfo::findOrFail($id);
        $this->bi_user_id = $basicInfo->user_id;
        $this->bi_track_type = $basicInfo->track_type;
        $this->bi_first_name = $basicInfo->first_name;
        $this->bi_last_name = $basicInfo->last_name;
        $this->bi_cnic_number = $basicInfo->cnic_number;
        $this->bi_dob = $basicInfo->date_of_birth?->format('Y-m-d');
        $this->bi_is_locked = (bool)$basicInfo->is_locked;
        $this->isEditMode = true;
        $this->isFormOpen = true;
    }

    public function saveBasicInfo()
    {
        if ($this->isEditMode) {
            abort_unless(Auth::user()->can('edit-basic-info'), 403);
            $this->validate([
                'bi_first_name' => 'required|string|max:100',
                'bi_last_name' => 'required|string|max:100',
                'bi_cnic_number' => 'required|string|max:15|unique:basic_infos,cnic_number,' . $this->bi_id,
                'bi_dob' => 'required|date',
            ]);

            $basicInfo = BasicInfo::findOrFail($this->bi_id);
            $basicInfo->update([
                'first_name' => $this->bi_first_name,
                'last_name' => $this->bi_last_name,
                'cnic_number' => $this->bi_cnic_number,
                'date_of_birth' => $this->bi_dob,
                'is_locked' => $this->bi_is_locked,
            ]);
            session()->flash('message', 'Candidate basicInfo updated.');
        } else {
            abort_unless(Auth::user()->can('create-basic-info'), 403);
            $this->validate([
                'bi_user_id' => 'required|exists:users,id|unique:basic_infos,user_id',
                'bi_first_name' => 'required|string|max:100',
                'bi_last_name' => 'required|string|max:100',
                'bi_cnic_number' => 'required|string|max:15|unique:basic_infos,cnic_number',
                'bi_dob' => 'required|date',
            ]);

            BasicInfo::create([
                'user_id' => $this->bi_user_id,
                'track_type' => $this->bi_track_type,
                'first_name' => $this->bi_first_name,
                'last_name' => $this->bi_last_name,
                'cnic_number' => $this->bi_cnic_number,
                'date_of_birth' => $this->bi_dob,
                'is_locked' => $this->bi_is_locked,
            ]);
            session()->flash('message', 'Candidate basicInfo created successfully.');
        }

        $this->isFormOpen = false;
        $this->loadTabSpecificData();
    }

    public function deleteBasicInfo($id)
    {
        abort_unless(Auth::user()->can('delete-basic-info'), 403);
        BasicInfo::destroy($id);
        session()->flash('message', 'basicInfo deleted.');
        $this->loadTabSpecificData();
    }

    public function viewBasicInfoDetails($id)
    {
        abort_unless(Auth::user()->can('view-basic-info'), 403);
        $this->selectedRecordId = $id;
        $basicInfo = BasicInfo::with('user', 'medals', 'publications', 'events', 'researchProjects')->findOrFail($id);
        $this->selectedRecordDetails = $basicInfo;
        $this->isDetailsOpen = true;
    }

    // ==========================================
    // 💳 PAYMENTS BREAD WORKFLOWS
    // ==========================================
    public function openCreatePaymentForm()
    {
        abort_unless(Auth::user()->can('create-payment'), 403);
        $this->resetForms();
        $this->isEditMode = false;
        $this->isFormOpen = true;
    }

    public function openEditPaymentForm($id)
    {
        abort_unless(Auth::user()->can('edit-payment'), 403);
        $this->resetForms();
        $this->pay_id = $id;
        $ledger = BillingLedger::findOrFail($id);
        $this->pay_app_id = $ledger->application_id;
        $this->pay_consumer_id = $ledger->consumer_id;
        $this->pay_amount = $ledger->amount;
        $this->pay_status = $ledger->status;
        $this->pay_transaction_id = $ledger->transaction_id;
        $this->pay_timestamp = $ledger->payment_timestamp?->format('Y-m-d\TH:i');
        $this->isEditMode = true;
        $this->isFormOpen = true;
    }

    public function savePayment()
    {
        if ($this->isEditMode) {
            abort_unless(Auth::user()->can('edit-payment'), 403);
            $this->validate([
                'pay_amount' => 'required|numeric',
                'pay_status' => 'required|in:PENDING,AUTHORIZED,SETTLED,EXPIRED',
            ]);
            $ledger = BillingLedger::findOrFail($this->pay_id);
            $ledger->update([
                'amount' => $this->pay_amount,
                'status' => $this->pay_status,
                'transaction_id' => $this->pay_transaction_id,
                'payment_timestamp' => $this->pay_timestamp,
            ]);

            // Reconcile associated application if settled
            if ($this->pay_status === 'SETTLED') {
                $ledger->application->update(['status' => 'PAID']);
            }

            session()->flash('message', 'Payment details updated.');
        } else {
            abort_unless(Auth::user()->can('create-payment'), 403);
            $this->validate([
                'pay_app_id' => 'required|exists:applications,id|unique:billing_ledger,application_id',
                'pay_consumer_id' => 'required|string|unique:billing_ledger,consumer_id',
                'pay_amount' => 'required|numeric',
                'pay_status' => 'required',
            ]);

            BillingLedger::create([
                'application_id' => $this->pay_app_id,
                'consumer_id' => $this->pay_consumer_id,
                'amount' => $this->pay_amount,
                'status' => $this->pay_status,
                'transaction_id' => $this->pay_transaction_id,
                'payment_timestamp' => $this->pay_timestamp,
            ]);
            session()->flash('message', 'Billing voucher logged successfully.');
        }

        $this->isFormOpen = false;
        $this->loadTabSpecificData();
    }

    public function deletePayment($id)
    {
        abort_unless(Auth::user()->can('delete-payment'), 403);
        BillingLedger::destroy($id);
        session()->flash('message', 'Billing record deleted.');
        $this->loadTabSpecificData();
    }

    // ==========================================
    // 🔍 AUDIT LOGS WORKFLOWS
    // ==========================================
    public function viewLogDetails($id)
    {
        abort_unless(Auth::user()->can('view-audit-log'), 403);
        $this->selectedRecordId = $id;
        $log = SecurityAuditLog::with('user')->findOrFail($id);
        $this->selectedRecordDetails = $log;
        $this->isDetailsOpen = true;
    }

    public function clearAuditLogs()
    {
        abort_unless(Auth::user()->can('delete-audit-log'), 403);
        SecurityAuditLog::truncate();
        session()->flash('message', 'All security logs cleared.');
        $this->loadTabSpecificData();
    }

    public function render()
    {
        return view('livewire.admin.entity-management')
            ->layout('layouts.app');
    }
}
