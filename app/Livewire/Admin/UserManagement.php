<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserManagement extends Component
{
    // View state
    public $isFormOpen = false;
    public $isEditMode = false;

    // Filters & Search
    public $search = '';
    public $roleFilter = '';
    public $statusFilter = '';
    public $categorySection = 'ALL'; // 'ALL', 'STAFF', 'COMMITTEES', 'REVIEWERS', 'APPLICANTS'

    // Personnel Details Modal
    public $isPersonnelModalOpen = false;
    public $selectedPersonnel = null;

    // Direct User Permissions Modal
    public $isDirectPermissionsModalOpen = false;
    public $targetUserIdForPermissions = null;
    public $targetUserDirectPermissions = [];
    public $targetUserRolePermissions = [];
    public $allGroupedPermissions = [];

    // Collections
    public $roles = [];

    // Form fields
    public $user_id;
    public $email;
    public $password;
    public $role_name = 'applicant';
    public $is_active = true;

    public function mount()
    {
        abort_unless(Auth::check() && Auth::user()->can('view-user'), 403);
        $this->loadAuxiliaryData();
    }

    public function loadAuxiliaryData()
    {
        $this->roles = Role::all();
    }

    public function resetForm()
    {
        $this->reset([
            'user_id',
            'email',
            'password',
        ]);
        $this->role_name = 'applicant';
        $this->is_active = true;
        $this->isFormOpen = false;
        $this->isEditMode = false;
    }

    public function openCreateUserForm()
    {
        abort_unless(Auth::user()->can('create-user'), 403);
        $this->resetForm();
        $this->isEditMode = false;
        $this->isFormOpen = true;
    }

    public function openEditUserForm($id)
    {
        abort_unless(Auth::user()->can('edit-user'), 403);
        $this->resetForm();
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

        $this->resetForm();
    }

    public function deleteUser($id)
    {
        abort_unless(Auth::user()->can('delete-user'), 403);
        if (Auth::id() == $id) {
            session()->flash('error', 'Safety gate: You cannot delete your logged in session.');
            return;
        }
        User::destroy($id);
        session()->flash('message', 'User account deleted successfully.');
    }

    public function toggleUserStatus($id)
    {
        abort_unless(Auth::user()->can('toggle-user-status') || Auth::user()->can('edit-user'), 403);
        if (Auth::id() == $id) {
            session()->flash('error', 'Safety gate: You cannot deactivate your active session.');
            return;
        }
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);
        session()->flash('message', 'User active status updated.');
    }

    public function filterCategory($section)
    {
        $this->categorySection = $section;
    }

    public function viewPersonnelDetails($id)
    {
        abort_unless(Auth::user()->can('view-user-dossier') || Auth::user()->can('view-user'), 403);
        $this->selectedPersonnel = User::with([
            'roles',
            'basicInfo',
            'reviewerProfile',
            'committees.campaign',
            'applications.campaign',
        ])->findOrFail($id);
        $this->isPersonnelModalOpen = true;
    }

    public function closePersonnelModal()
    {
        $this->isPersonnelModalOpen = false;
        $this->selectedPersonnel = null;
    }

    public function openDirectPermissionsModal($userId)
    {
        abort_unless(Auth::user()->can('assign-user-permissions') || Auth::user()->can('edit-user'), 403);
        $user = User::with(['roles.permissions', 'permissions'])->findOrFail($userId);
        $this->targetUserIdForPermissions = $userId;
        $this->targetUserDirectPermissions = $user->getDirectPermissions()->pluck('name')->toArray();
        $this->targetUserRolePermissions = $user->getPermissionsViaRoles()->pluck('name')->toArray();
        $this->allGroupedPermissions = \App\Services\PermissionRegistry::catalogue();
        $this->isDirectPermissionsModalOpen = true;
    }

    public function toggleDirectPermission($permName)
    {
        if (in_array($permName, $this->targetUserDirectPermissions)) {
            $this->targetUserDirectPermissions = array_values(array_diff($this->targetUserDirectPermissions, [$permName]));
        } else {
            $this->targetUserDirectPermissions[] = $permName;
        }
    }

    public function saveDirectPermissions()
    {
        abort_unless(Auth::user()->can('assign-user-permissions') || Auth::user()->can('edit-user'), 403);
        $user = User::findOrFail($this->targetUserIdForPermissions);
        $user->syncPermissions($this->targetUserDirectPermissions);
        $this->isDirectPermissionsModalOpen = false;
        session()->flash('message', 'Direct extra permissions updated successfully for ' . $user->email);
    }

    public function closeDirectPermissionsModal()
    {
        $this->isDirectPermissionsModalOpen = false;
        $this->targetUserIdForPermissions = null;
        $this->targetUserDirectPermissions = [];
    }

    public function render()
    {
        $query = User::with(['roles', 'basicInfo', 'reviewerProfile', 'committees.campaign', 'permissions']);

        // Personnel Classification Filter
        if ($this->categorySection === 'STAFF') {
            $query->whereHas('roles', function($q) {
                $q->whereNotIn('name', [
                    'applicant',
                    'scrutiny-committee', 'scrutiny',
                    'appellate-committee', 'appellate',
                    'selection-board-member', 'board-member', 'board',
                    'academic-reviewer', 'reviewer',
                ]);
            });
        } elseif ($this->categorySection === 'COMMITTEES') {
            $query->where(function($q) {
                $q->whereHas('roles', fn($rq) => $rq->whereIn('name', [
                    'scrutiny-committee', 'scrutiny',
                    'appellate-committee', 'appellate',
                    'selection-board-member', 'board-member', 'board',
                ]))->orWhereHas('committees');
            });
        } elseif ($this->categorySection === 'REVIEWERS') {
            $query->where(function($q) {
                $q->whereHas('roles', fn($rq) => $rq->whereIn('name', ['academic-reviewer', 'reviewer']))
                  ->orWhereHas('reviewerProfile');
            });
        } elseif ($this->categorySection === 'APPLICANTS') {
            $query->whereHas('roles', fn($q) => $q->where('name', 'applicant'))
                  ->whereDoesntHave('roles', fn($q) => $q->where('name', '!=', 'applicant'));
        }

        if (!empty($this->roleFilter)) {
            $query->role($this->roleFilter);
        }

        if ($this->statusFilter !== '') {
            $query->where('is_active', (bool)$this->statusFilter);
        }

        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('email', 'like', $searchTerm)
                  ->orWhereHas('basicInfo', function($bq) use ($searchTerm) {
                      $bq->where('first_name', 'like', $searchTerm)
                         ->orWhere('last_name', 'like', $searchTerm)
                         ->orWhere('cnic_number', 'like', $searchTerm);
                  })
                  ->orWhereHas('reviewerProfile', function($rq) use ($searchTerm) {
                      $rq->where('name', 'like', $searchTerm)
                         ->orWhere('university', 'like', $searchTerm)
                         ->orWhere('country', 'like', $searchTerm);
                  });
            });
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        // Categorized Counts for directory pills (Dynamic, supports any new roles)
        $staffCount = User::whereHas('roles', function($q) {
            $q->whereNotIn('name', [
                'applicant',
                'scrutiny-committee', 'scrutiny',
                'appellate-committee', 'appellate',
                'selection-board-member', 'board-member', 'board',
                'academic-reviewer', 'reviewer',
            ]);
        })->count();

        $committeeCount = User::whereHas('roles', fn($q) => $q->whereIn('name', [
            'scrutiny-committee', 'scrutiny',
            'appellate-committee', 'appellate',
            'selection-board-member', 'board-member', 'board',
        ]))->orWhereHas('committees')->count();

        $reviewerCount = User::whereHas('roles', fn($q) => $q->whereIn('name', ['academic-reviewer', 'reviewer']))
            ->orWhereHas('reviewerProfile')->count();

        $applicantCount = User::whereHas('roles', fn($q) => $q->where('name', 'applicant'))
            ->whereDoesntHave('roles', fn($q) => $q->where('name', '!=', 'applicant'))
            ->count();

        $totalCount = User::count();

        return view('livewire.admin.user-management', [
            'users' => $users,
            'staffCount' => $staffCount,
            'committeeCount' => $committeeCount,
            'reviewerCount' => $reviewerCount,
            'applicantCount' => $applicantCount,
            'totalCount' => $totalCount,
        ])->layout('layouts.app', ['title' => 'User & Personnel Directory - Hirely']);
    }
}
