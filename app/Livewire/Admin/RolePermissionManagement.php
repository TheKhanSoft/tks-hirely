<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

class RolePermissionManagement extends Component
{
    // Active sub-tab
    public $activeSubTab = 'roles'; // 'roles' or 'permissions'

    // View state
    public $isFormOpen = false;
    public $isEditMode = false;

    // Collections
    public $roles = [];
    public $permissions = [];

    // Role form fields
    public $role_id;
    public $role_name_input;
    public $selected_permissions = [];

    // Permission form fields
    public $permission_id;
    public $permission_name_input;

    public function mount()
    {
        abort_unless(Auth::check() && Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
        
        $tab = request()->query('tab');
        if (in_array($tab, ['roles', 'permissions'])) {
            $this->activeSubTab = $tab;
        }

        $this->loadData();
    }

    public function setSubTab($tab)
    {
        $this->activeSubTab = $tab;
        $this->resetForm();
    }

    public function loadData()
    {
        $this->roles = Role::with('permissions', 'users')->get();
        $this->permissions = Permission::orderBy('name')->get();
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

    // Role operations
    public function openCreateRoleForm()
    {
        abort_unless(Auth::user()->can('create-role') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
        $this->resetForm();
        $this->isEditMode = false;
        $this->isFormOpen = true;
    }

    public function openEditRoleForm($id)
    {
        abort_unless(Auth::user()->can('edit-role') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
        $this->resetForm();
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
            abort_unless(Auth::user()->can('edit-role') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
            $this->validate([
                'role_name_input' => 'required|string|unique:roles,name,' . $this->role_id,
            ]);
            $role = Role::findOrFail($this->role_id);
            $role->update(['name' => $this->role_name_input]);
            $role->syncPermissions($this->selected_permissions);
            session()->flash('message', 'Role permissions updated successfully.');
        } else {
            abort_unless(Auth::user()->can('create-role') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
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
        abort_unless(Auth::user()->can('delete-role') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
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
        abort_unless(Auth::user()->can('create-permission') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
        $this->resetForm();
        $this->isEditMode = false;
        $this->isFormOpen = true;
    }

    public function openEditPermissionForm($id)
    {
        abort_unless(Auth::user()->can('edit-permission') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
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
            abort_unless(Auth::user()->can('edit-permission') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
            $this->validate([
                'permission_name_input' => 'required|string|unique:permissions,name,' . $this->permission_id,
            ]);
            $perm = Permission::findOrFail($this->permission_id);
            $perm->update(['name' => $this->permission_name_input]);
            session()->flash('message', 'Permission updated successfully.');
        } else {
            abort_unless(Auth::user()->can('create-permission') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
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
        abort_unless(Auth::user()->can('delete-permission') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
        Permission::destroy($id);
        session()->flash('message', 'Permission deleted and revoked.');
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.admin.role-permission-management')
            ->layout('layouts.app', ['title' => 'Roles & Permissions - Hirely']);
    }
}
