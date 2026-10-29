<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Livewire\Traits\WithDynamicListing;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

class RolePermissionManagement extends Component
{
    use WithDynamicListing;

    // Active sub-tab
    public $activeSubTab = 'roles'; // 'roles' or 'permissions'

    // Listing defaults (16 cards per page = clean 4x4 or 3-5 per row)
    public $perPage = 16;

    // View state
    public $isFormOpen = false;
    public $isEditMode = false;
    public $isRoleDetailsOpen = false;
    public $selectedRole = null;

    // Collections
    public $groupedPermissions = [];

    // Role form fields
    public $role_id;
    public $role_name_input;
    public $selected_permissions = [];

    // Permission form fields
    public $permission_id;
    public $permission_name_input;

    public function mount()
    {
        abort_unless(Auth::check() && (Auth::user()->can('view-role') || Auth::user()->can('view-permission')), 403);
        
        $tab = request()->query('tab');
        if (in_array($tab, ['roles', 'permissions'])) {
            $this->activeSubTab = $tab;
        }

        $this->loadData();
    }

    public function setSubTab($tab)
    {
        $this->activeSubTab = $tab;
        $this->search = '';
        $this->resetPage();
        $this->resetForm();
    }

    public function loadData()
    {
        $this->groupedPermissions = \App\Services\PermissionRegistry::catalogue();
    }

    public function resetForm()
    {
        $this->reset([
            'role_id',
            'role_name_input',
            'selected_permissions',
            'permission_id',
            'permission_name_input',
        ]);
        $this->isFormOpen = false;
        $this->isEditMode = false;
    }

    /**
     * Export active registry (roles or permissions) as CSV
     */
    public function exportCsv()
    {
        if ($this->activeSubTab === 'roles') {
            abort_unless(Auth::user()->can('view-role'), 403);
            $rolesQuery = Role::with('permissions', 'users');
            if (!Auth::user()->hasRole('super-admin')) {
                $rolesQuery->where('name', '!=', 'super-admin');
            }
            if (!empty($this->search)) {
                $rolesQuery->where('name', 'like', '%' . $this->search . '%');
            }
            $roles = $rolesQuery->get();

            $rows = [];
            foreach ($roles as $role) {
                $rows[] = [
                    $role->id,
                    $role->name,
                    $role->name === 'super-admin' ? 'Wildcard Super Admin (*)' : $role->permissions->count(),
                    $role->users->count(),
                    $role->permissions->pluck('name')->implode('; ')
                ];
            }

            return $this->streamCsvDownload('roles_registry_' . date('Y_m_d_His') . '.csv', [
                'Role ID', 'Role Slug', 'Permissions Count', 'Active Accounts', 'Permissions Scope'
            ], $rows);
        } else {
            abort_unless(Auth::user()->can('view-permission'), 403);
            $permsQuery = Permission::query();
            if (!empty($this->search)) {
                $permsQuery->where('name', 'like', '%' . $this->search . '%');
            }
            $perms = $permsQuery->orderBy('name')->get();

            $rows = [];
            foreach ($perms as $p) {
                $rows[] = [
                    $p->id,
                    $p->name,
                    $p->guard_name
                ];
            }

            return $this->streamCsvDownload('permissions_catalog_' . date('Y_m_d_His') . '.csv', [
                'Permission ID', 'Permission Slug', 'Guard Engine'
            ], $rows);
        }
    }

    // Role operations
    public function openCreateRoleForm()
    {
        abort_unless(Auth::user()->can('create-role'), 403);
        $this->resetForm();
        $this->isEditMode = false;
        $this->isFormOpen = true;
    }

    public function viewRoleDetails($id)
    {
        abort_unless(Auth::user()->can('view-role'), 403);
        $role = Role::with(['permissions', 'users'])->findOrFail($id);
        if ($role->name === 'super-admin' && !Auth::user()->hasRole('super-admin')) {
            abort(403, 'Unauthorized access to super administrator role.');
        }
        $this->selectedRole = $role;
        $this->isRoleDetailsOpen = true;
    }

    public function closeRoleDetailsModal()
    {
        $this->isRoleDetailsOpen = false;
        $this->selectedRole = null;
    }

    public function openEditRoleForm($id)
    {
        abort_unless(Auth::user()->can('edit-role') || Auth::user()->can('assign-role-permissions'), 403);
        $role = Role::findOrFail($id);
        if ($role->name === 'super-admin' && !Auth::user()->hasRole('super-admin')) {
            abort(403, 'Unauthorized access to super administrator role.');
        }

        $this->resetForm();
        $this->role_id = $id;
        $this->role_name_input = $role->name;
        $this->selected_permissions = $role->permissions->pluck('name')->toArray();
        $this->isEditMode = true;
        $this->isFormOpen = true;
    }

    public function saveRole()
    {
        if ($this->role_name_input === 'super-admin' && !Auth::user()->hasRole('super-admin')) {
            abort(403, 'Unauthorized modification to super-admin role.');
        }

        if ($this->isEditMode) {
            abort_unless(Auth::user()->can('edit-role') || Auth::user()->can('assign-role-permissions'), 403);
            $this->validate([
                'role_name_input' => 'required|string|unique:roles,name,' . $this->role_id,
            ]);
            $role = Role::findOrFail($this->role_id);
            $role->update(['name' => $this->role_name_input]);
            $role->syncPermissions($this->selected_permissions);
            session()->flash('message', 'Role permissions updated successfully.');
        } else {
            abort_unless(Auth::user()->can('create-role'), 403);
            $this->validate([
                'role_name_input' => 'required|string|unique:roles,name',
            ]);
            $role = Role::create(['name' => $this->role_name_input, 'guard_name' => 'web']);
            $role->syncPermissions($this->selected_permissions);
            session()->flash('message', 'New role created successfully.');
        }

        $this->resetForm();
        $this->loadData();
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
        $this->loadData();
    }

    // Permission operations
    public function openCreatePermissionForm()
    {
        abort_unless(Auth::user()->can('create-permission'), 403);
        $this->resetForm();
        $this->isEditMode = false;
        $this->isFormOpen = true;
    }

    public function openEditPermissionForm($id)
    {
        abort_unless(Auth::user()->can('edit-permission'), 403);
        $this->resetForm();
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
            session()->flash('message', 'Permission updated successfully.');
        } else {
            abort_unless(Auth::user()->can('create-permission'), 403);
            $this->validate([
                'permission_name_input' => 'required|string|unique:permissions,name',
            ]);
            Permission::create(['name' => $this->permission_name_input, 'guard_name' => 'web']);
            session()->flash('message', 'New permission registered successfully.');
        }

        $this->resetForm();
        $this->loadData();
    }

    public function deletePermission($id)
    {
        abort_unless(Auth::user()->can('delete-permission'), 403);
        Permission::destroy($id);
        session()->flash('message', 'Permission deleted and revoked.');
        $this->loadData();
    }

    public function render()
    {
        $rolesQuery = Role::with('permissions', 'users');
        if (!Auth::user()->hasRole('super-admin')) {
            $rolesQuery->where('name', '!=', 'super-admin');
        }
        if ($this->activeSubTab === 'roles' && !empty($this->search)) {
            $rolesQuery->where('name', 'like', '%' . $this->search . '%');
        }
        $roles = $rolesQuery->paginate($this->perPage, ['*'], 'roles_page');

        $permsQuery = Permission::query();
        if ($this->activeSubTab === 'permissions' && !empty($this->search)) {
            $permsQuery->where('name', 'like', '%' . $this->search . '%');
        }
        $permissions = $permsQuery->orderBy('name')->paginate($this->perPage, ['*'], 'perms_page');

        $totalRolesCount = Role::when(!Auth::user()->hasRole('super-admin'), fn($q) => $q->where('name', '!=', 'super-admin'))->count();
        $totalPermsCount = Permission::count();

        return view('livewire.admin.role-permission-management', [
            'roles' => $roles,
            'permissions' => $permissions,
            'totalRolesCount' => $totalRolesCount,
            'totalPermsCount' => $totalPermsCount,
        ])->layout('layouts.app', ['title' => 'Roles & Permissions - Hirely']);
    }
}
