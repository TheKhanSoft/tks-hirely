<div>
    <!-- Console Control Header -->
    <div class="card mb-6" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; position: relative; overflow: hidden;">
        <div style="position: absolute; right: -2rem; top: -2rem; width: 140px; height: 140px; background: radial-gradient(circle, var(--primary-glow) 0%, transparent 70%); pointer-events: none;"></div>
        <div>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.35rem;">
                <div class="brand-icon-box" style="width: 38px; height: 38px; border-radius: 10px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="card-title" style="margin: 0; font-size: 1.35rem;">Roles & Permissions Desk</h2>
                    <p class="card-subtitle" style="margin: 0;">Granular access control policies, security role memberships, and system capabilities</p>
                </div>
            </div>
        </div>
        <div>
            @if(!$isFormOpen)
                @if($activeSubTab === 'roles')
                    @can('create-role')
                        <button type="button" class="btn btn-primary" wire:click="openCreateRoleForm" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            <span>Add Role</span>
                        </button>
                    @endcan
                @elseif($activeSubTab === 'permissions')
                    @can('create-permission')
                        <button type="button" class="btn btn-primary" wire:click="openCreatePermissionForm" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            <span>Add Permission</span>
                        </button>
                    @endcan
                @endif
            @endif
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session()->has('message'))
        <div class="alert alert-success mb-6" style="display: flex; align-items: center; gap: 0.75rem; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3); color: var(--success); padding: 0.85rem 1.25rem; border-radius: var(--radius-md);">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger mb-6" style="display: flex; align-items: center; gap: 0.75rem; background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.3); color: var(--danger); padding: 0.85rem 1.25rem; border-radius: var(--radius-md);">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Sub-tab Bar -->
    <div style="display: flex; gap: 0.5rem; background: var(--bg-tertiary); padding: 0.35rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color); width: fit-content; margin-bottom: 1.5rem;">
        <button type="button" class="btn" wire:click="setSubTab('roles')"
                style="padding: 0.45rem 1.25rem; font-size: 0.875rem; border-radius: var(--radius-md); font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; transition: var(--transition-base); {{ $activeSubTab === 'roles' ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            <span>Roles Registry</span>
            <span style="font-size: 0.75rem; padding: 0.15rem 0.45rem; border-radius: 999px; background: rgba(99,102,241,0.15); color: var(--primary);">{{ count($roles) }}</span>
        </button>
        <button type="button" class="btn" wire:click="setSubTab('permissions')"
                style="padding: 0.45rem 1.25rem; font-size: 0.875rem; border-radius: var(--radius-md); font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; transition: var(--transition-base); {{ $activeSubTab === 'permissions' ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="7.5" cy="15.5" r="5.5"></circle><path d="m21 2-9.6 9.6"></path><path d="m15.5 7.5 3 3L22 7l-3-3"></path></svg>
            <span>Permissions Catalog</span>
            <span style="font-size: 0.75rem; padding: 0.15rem 0.45rem; border-radius: 999px; background: rgba(6,182,212,0.15); color: var(--secondary);">{{ count($permissions) }}</span>
        </button>
    </div>

    <!-- ========================================== -->
    <!-- SUB-TAB 1: ROLES -->
    <!-- ========================================== -->
    @if ($activeSubTab === 'roles')
        @if ($isFormOpen)
            <div class="card mb-6" style="border-color: var(--primary-glow); box-shadow: var(--shadow-md);">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h3 class="card-title" style="margin: 0; font-size: 1.2rem;">
                        {{ $isEditMode ? 'Sync Role Permissions' : 'Register New Role' }}
                    </h3>
                    <button type="button" class="btn btn-secondary" wire:click="resetForm" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;">Cancel</button>
                </div>
                <form wire:submit.prevent="saveRole">
                    <x-input label="Role Name Reference *" :error="$errors->first('role_name_input')">
                        <input type="text" class="form-control" wire:model="role_name_input" placeholder="e.g. departmental-coordinator">
                    </x-input>
                    
                    <div style="border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem; background: var(--bg-tertiary);" class="mt-4 mb-4">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <h4 style="color: var(--secondary); margin: 0; font-size: 1rem; font-weight: 700;">Assign Granular Permissions Grid (Grouped by Module)</h4>
                            <span style="font-size: 0.8rem; color: var(--color-text-secondary); font-weight: 600;">
                                {{ count($selected_permissions) }} permissions assigned
                            </span>
                        </div>
                        
                        <div style="max-height: 420px; overflow-y: auto; display: flex; flex-direction: column; gap: 1rem; padding-right: 0.5rem;">
                            @foreach($groupedPermissions as $category => $categoryPerms)
                                <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem 1rem;">
                                    <h5 style="font-size: 0.82rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 0.65rem 0; display: flex; align-items: center; gap: 0.4rem;">
                                        <span>📁</span>
                                        <span>{{ $category }}</span>
                                    </h5>
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach($categoryPerms as $permSlug => $permData)
                                            <label style="display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer; padding: 0.4rem 0.5rem; border-radius: 4px; border: 1px solid var(--border-color); background: var(--bg-tertiary);">
                                                <input type="checkbox" wire:model="selected_permissions" value="{{ $permSlug }}" style="width: 1.1rem; height: 1.1rem; accent-color: var(--primary); margin-top: 2px;">
                                                <div>
                                                    <strong style="font-size: 0.82rem; color: var(--color-text-primary); display: block;">{{ $permData['label'] }}</strong>
                                                    <code style="font-size: 0.72rem; color: var(--color-text-muted);">{{ $permSlug }}</code>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex gap-4 mt-6" style="border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                        <button type="submit" class="btn btn-primary">Save Role</button>
                        <button type="button" class="btn btn-secondary" wire:click="resetForm">Cancel</button>
                    </div>
                </form>
            </div>
        @else
            <x-table :headers="['Role Name Reference', 'Synced Permissions Scope', 'Active Accounts Scope', 'Action']">
                @foreach($roles as $role)
                    <tr>
                        <td>
                            <strong style="color: var(--secondary); text-transform: uppercase;">{{ str_replace('-', ' ', $role->name) }}</strong>
                        </td>
                        <td>
                            <span class="badge" style="background: rgba(6,182,212,0.12); color: var(--secondary); border: 1px solid rgba(6,182,212,0.25);">
                                {{ $role->name === 'super-admin' ? 'All (Wildcard Bypass *)' : $role->permissions->count() . ' permissions' }}
                            </span>
                        </td>
                        <td>{{ $role->users->count() }} Accounts</td>
                        <td>
                            <div class="action-btn-group justify-end">
                                @can('edit-role')
                                    <button type="button" 
                                            class="action-btn action-btn-edit" 
                                            wire:click="openEditRoleForm({{ $role->id }})"
                                            data-tooltip="Edit & Sync Role Permissions"
                                            aria-label="Edit Role">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                    </button>
                                @endcan
                                @if(!in_array($role->name, ['super-admin', 'registrar', 'applicant']))
                                    @can('delete-role')
                                        <button type="button" 
                                                class="action-btn action-btn-delete" 
                                                wire:click="deleteRole({{ $role->id }})" 
                                                onclick="confirm('Are you sure you want to delete this role?') || event.stopImmediatePropagation()"
                                                data-tooltip="Delete Role"
                                                aria-label="Delete Role">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                <line x1="10" y1="11" x2="10" y2="17"/>
                                                <line x1="14" y1="11" x2="14" y2="17"/>
                                            </svg>
                                        </button>
                                    @endcan
                                @else
                                    <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--color-text-muted); font-size: 0.72rem;">System Core Locked</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-table>
        @endif

    <!-- ========================================== -->
    <!-- SUB-TAB 2: PERMISSIONS -->
    <!-- ========================================== -->
    @elseif ($activeSubTab === 'permissions')
        @if ($isFormOpen)
            <div class="card mb-6" style="border-color: var(--primary-glow); box-shadow: var(--shadow-md);">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h3 class="card-title" style="margin: 0; font-size: 1.2rem;">
                        {{ $isEditMode ? 'Edit System Permission' : 'Register Permission Action' }}
                    </h3>
                    <button type="button" class="btn btn-secondary" wire:click="resetForm" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;">Cancel</button>
                </div>
                <form wire:submit.prevent="savePermission">
                    <x-input label="Permission Name Slug *" :error="$errors->first('permission_name_input')">
                        <input type="text" class="form-control" wire:model="permission_name_input" placeholder="e.g. view-reports">
                    </x-input>
                    <div class="flex gap-4 mt-6" style="border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                        <button type="submit" class="btn btn-primary">Save Permission</button>
                        <button type="button" class="btn btn-secondary" wire:click="resetForm">Cancel</button>
                    </div>
                </form>
            </div>
        @else
            <x-table :headers="['Permission Slug Name', 'Guard Engine Reference', 'Actions']">
                @forelse($permissions as $perm)
                    <tr>
                        <td>
                            <code style="background: rgba(99,102,241,0.1); color: var(--primary); padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 600;">{{ $perm->name }}</code>
                        </td>
                        <td><code>{{ $perm->guard_name }}</code></td>
                        <td>
                            <div class="action-btn-group justify-end">
                                @can('edit-permission')
                                    <button type="button" 
                                            class="action-btn action-btn-edit" 
                                            wire:click="openEditPermissionForm({{ $perm->id }})"
                                            data-tooltip="Edit Permission Slug"
                                            aria-label="Edit Permission">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                    </button>
                                @endcan
                                @can('delete-permission')
                                    <button type="button" 
                                            class="action-btn action-btn-delete" 
                                            wire:click="deletePermission({{ $perm->id }})" 
                                            onclick="confirm('Are you sure you want to delete this permission? This will revoke it from all roles.') || event.stopImmediatePropagation()"
                                            data-tooltip="Delete System Permission"
                                            aria-label="Delete Permission">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                            <line x1="10" y1="11" x2="10" y2="17"/>
                                            <line x1="14" y1="11" x2="14" y2="17"/>
                                        </svg>
                                    </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center" style="padding: 2.5rem; color: var(--color-text-secondary);">No system permissions registered.</td></tr>
                @endforelse
            </x-table>
        @endif
    @endif
</div>

