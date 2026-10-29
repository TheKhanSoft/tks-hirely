<div>
    <!-- Executive Control Header -->
    <div class="page-hero-header">
        <div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div class="brand-icon-box" style="background: linear-gradient(135deg, rgba(99,102,241,0.25), rgba(79,70,229,0.4)); box-shadow: 0 4px 14px rgba(99,102,241,0.25);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" style="color: #ffffff;">
                        <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z"></path>
                        <path d="M12 6a4 4 0 1 0 4 4 4 4 0 0 0-4-4z"></path>
                    </svg>
                </div>
                <div>
                    <h1>System Entity & Operations Console</h1>
                    <p>Unified management desk for administrative control, roles registry, candidate applications, vouchers, and audit logs.</p>
                </div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            @if(!$isFormOpen && !$isDetailsOpen)
                @if($activeTab === 'users' && auth()->user()->can('create-user'))
                    <button type="button" class="btn btn-primary" wire:click="openCreateUserForm" style="display: inline-flex; align-items: center; gap: 0.5rem; border-radius: 10px; font-weight: 700; padding: 0.65rem 1.35rem; box-shadow: 0 4px 18px -2px rgba(99,102,241,0.5);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>+ Register User</span>
                    </button>
                @elseif($activeTab === 'roles' && auth()->user()->can('create-role'))
                    <button type="button" class="btn btn-primary" wire:click="openCreateRoleForm" style="display: inline-flex; align-items: center; gap: 0.5rem; border-radius: 10px; font-weight: 700; padding: 0.65rem 1.35rem; box-shadow: 0 4px 18px -2px rgba(99,102,241,0.5);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>+ Add Role</span>
                    </button>
                @elseif($activeTab === 'permissions' && auth()->user()->can('create-permission'))
                    <button type="button" class="btn btn-primary" wire:click="openCreatePermissionForm" style="display: inline-flex; align-items: center; gap: 0.5rem; border-radius: 10px; font-weight: 700; padding: 0.65rem 1.35rem; box-shadow: 0 4px 18px -2px rgba(99,102,241,0.5);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>+ Add Permission</span>
                    </button>
                @elseif($activeTab === 'campaigns' && auth()->user()->can('create-campaign'))
                    <button type="button" class="btn btn-primary" wire:click="openCreateCampaignForm" style="display: inline-flex; align-items: center; gap: 0.5rem; border-radius: 10px; font-weight: 700; padding: 0.65rem 1.35rem; box-shadow: 0 4px 18px -2px rgba(99,102,241,0.5);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>+ Create Campaign</span>
                    </button>
                @elseif($activeTab === 'applications' && auth()->user()->can('create-application'))
                    <button type="button" class="btn btn-primary" wire:click="openCreateApplicationForm" style="display: inline-flex; align-items: center; gap: 0.5rem; border-radius: 10px; font-weight: 700; padding: 0.65rem 1.35rem; box-shadow: 0 4px 18px -2px rgba(99,102,241,0.5);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>+ Add Application</span>
                    </button>
                @elseif($activeTab === 'basic_infos' && auth()->user()->can('create-basic-info'))
                    <button type="button" class="btn btn-primary" wire:click="openCreateBasicInfoForm" style="display: inline-flex; align-items: center; gap: 0.5rem; border-radius: 10px; font-weight: 700; padding: 0.65rem 1.35rem; box-shadow: 0 4px 18px -2px rgba(99,102,241,0.5);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>+ Create Profile</span>
                    </button>
                @elseif($activeTab === 'payments' && auth()->user()->can('create-payment'))
                    <button type="button" class="btn btn-primary" wire:click="openCreatePaymentForm" style="display: inline-flex; align-items: center; gap: 0.5rem; border-radius: 10px; font-weight: 700; padding: 0.65rem 1.35rem; box-shadow: 0 4px 18px -2px rgba(99,102,241,0.5);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>+ Log Payment</span>
                    </button>
                @elseif($activeTab === 'logs' && auth()->user()->can('delete-audit-log') && count($logs ?? []) > 0)
                    <button type="button" class="btn btn-danger" wire:click="clearAuditLogs" onclick="confirm('Are you sure you want to permanently purge all security logs?') || event.stopImmediatePropagation()" style="display: inline-flex; align-items: center; gap: 0.5rem; border-radius: 10px; font-weight: 700; padding: 0.65rem 1.35rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        <span>Purge Logs</span>
                    </button>
                @endif
            @endif
        </div>
    </div>

    <!-- Segmented Navigation Tabs -->
    <div style="display: flex; gap: 0.35rem; background: var(--bg-tertiary); padding: 0.35rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color); width: 100%; margin-bottom: 1.5rem; overflow-x: auto; white-space: nowrap;">
        @can('view-user')
            <button type="button" class="btn" wire:click="setTab('users')"
                    style="padding: 0.45rem 1rem; font-size: 0.85rem; border-radius: var(--radius-md); font-weight: 600; display: inline-flex; align-items: center; gap: 0.45rem; transition: var(--transition-base); {{ $activeTab === 'users' ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                <span>Users</span>
            </button>
        @endcan
        @can('view-role')
            <button type="button" class="btn" wire:click="setTab('roles')"
                    style="padding: 0.45rem 1rem; font-size: 0.85rem; border-radius: var(--radius-md); font-weight: 600; display: inline-flex; align-items: center; gap: 0.45rem; transition: var(--transition-base); {{ $activeTab === 'roles' ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                <span>Roles</span>
            </button>
        @endcan
        @can('view-permission')
            <button type="button" class="btn" wire:click="setTab('permissions')"
                    style="padding: 0.45rem 1rem; font-size: 0.85rem; border-radius: var(--radius-md); font-weight: 600; display: inline-flex; align-items: center; gap: 0.45rem; transition: var(--transition-base); {{ $activeTab === 'permissions' ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="7.5" cy="15.5" r="5.5"></circle><path d="m21 2-9.6 9.6"></path><path d="m15.5 7.5 3 3L22 7l-3-3"></path></svg>
                <span>Permissions</span>
            </button>
        @endcan
        @can('view-campaign')
            <button type="button" class="btn" wire:click="setTab('campaigns')"
                    style="padding: 0.45rem 1rem; font-size: 0.85rem; border-radius: var(--radius-md); font-weight: 600; display: inline-flex; align-items: center; gap: 0.45rem; transition: var(--transition-base); {{ $activeTab === 'campaigns' ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                <span>Campaigns</span>
            </button>
        @endcan
        @can('tune-constitution')
            <a href="/admin/criteria" class="btn"
               style="padding: 0.45rem 1rem; font-size: 0.85rem; border-radius: var(--radius-md); font-weight: 600; display: inline-flex; align-items: center; gap: 0.45rem; transition: var(--transition-base); background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="M7 21h10"/><path d="M12 3v18"/><path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"/></svg>
                <span>Criteria</span>
            </a>
        @endcan
        @can('view-application')
            <button type="button" class="btn" wire:click="setTab('applications')"
                    style="padding: 0.45rem 1rem; font-size: 0.85rem; border-radius: var(--radius-md); font-weight: 600; display: inline-flex; align-items: center; gap: 0.45rem; transition: var(--transition-base); {{ $activeTab === 'applications' ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                <span>Applications</span>
            </button>
        @endcan
        @can('view-basic-info')
            <button type="button" class="btn" wire:click="setTab('basic_infos')"
                    style="padding: 0.45rem 1rem; font-size: 0.85rem; border-radius: var(--radius-md); font-weight: 600; display: inline-flex; align-items: center; gap: 0.45rem; transition: var(--transition-base); {{ $activeTab === 'basic_infos' ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                <span>Candidate Profiles</span>
            </button>
        @endcan
        @can('view-payment')
            <button type="button" class="btn" wire:click="setTab('payments')"
                    style="padding: 0.45rem 1rem; font-size: 0.85rem; border-radius: var(--radius-md); font-weight: 600; display: inline-flex; align-items: center; gap: 0.45rem; transition: var(--transition-base); {{ $activeTab === 'payments' ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                <span>Vouchers & Payments</span>
            </button>
        @endcan
        @can('view-audit-log')
            <button type="button" class="btn" wire:click="setTab('logs')"
                    style="padding: 0.45rem 1rem; font-size: 0.85rem; border-radius: var(--radius-md); font-weight: 600; display: inline-flex; align-items: center; gap: 0.45rem; transition: var(--transition-base); {{ $activeTab === 'logs' ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                <span>Security Audit Logs</span>
            </button>
        @endcan
    </div>

    <!-- ========================================== -->
    <!-- TAB 1: USERS -->
    <!-- ========================================== -->
    @if ($activeTab === 'users')
        @if ($isFormOpen)
            <div class="card" style="border-color: var(--primary-glow); box-shadow: var(--shadow-md);">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h3 class="card-title" style="margin: 0; font-size: 1.2rem;">
                        {{ $isEditMode ? 'Edit User Account' : 'Register User Account' }}
                    </h3>
                    <button type="button" class="btn btn-secondary" wire:click="$set('isFormOpen', false)" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;">Cancel</button>
                </div>
                <form wire:submit.prevent="saveUser">
                    <div class="grid grid-cols-2 gap-6">
                        <x-input label="Email Address *" :error="$errors->first('email')">
                            <input type="email" class="form-control" wire:model="email" placeholder="email@university.edu">
                        </x-input>
                        <x-input label="Password {{ $isEditMode ? '(Leave blank to keep current)' : '*' }}" :error="$errors->first('password')">
                            <input type="password" class="form-control" wire:model="password" placeholder="••••••••">
                        </x-input>
                    </div>
                    <div class="grid grid-cols-2 gap-6 mt-4">
                        <x-select label="Assign System Role *" :error="$errors->first('role_name')">
                            <select class="form-control form-select" wire:model="role_name">
                                @foreach($roles as $r)
                                    <option value="{{ $r->name }}">{{ str_replace('-', ' ', $r->name) }}</option>
                                @endforeach
                            </select>
                        </x-select>
                        <div class="form-group" style="margin-top: 1.75rem;">
                            <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                                <input type="checkbox" wire:model="is_active" style="width: 1.25rem; height: 1.25rem; accent-color: var(--primary);">
                                <span style="font-weight: 600;">Allow User Login (Active Status)</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex gap-4 mt-6" style="border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                        <button type="submit" class="btn btn-primary">Save User</button>
                        <button type="button" class="btn btn-secondary" wire:click="$set('isFormOpen', false)">Cancel</button>
                    </div>
                </form>
            </div>
        @else
            <x-table :headers="['Email Address Reference', 'Mapped Role', 'Active Status', 'Date Created', 'Actions']">
                @forelse($users as $u)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.6rem;">
                                <div style="width: 28px; height: 28px; border-radius: 50%; background: rgba(99,102,241,0.15); color: var(--primary); font-weight: 700; font-size: 0.75rem; display: flex; align-items: center; justify-content: center;">
                                    {{ strtoupper(substr($u->email, 0, 1)) }}
                                </div>
                                <strong style="color: var(--color-text-primary)">{{ $u->email }}</strong>
                            </div>
                        </td>
                        <td>
                            <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary); border: 1px solid rgba(99,102,241,0.25);">
                                {{ str_replace('-', ' ', $u->roles->first()?->name ?? 'None') }}
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn {{ $u->is_active ? 'btn-success' : 'btn-danger' }}" style="padding: 0.2rem 0.5rem; font-size: 0.75rem; border-radius: 999px;" wire:click="toggleUserStatus({{ $u->id }})" :disabled="auth()->id() == $u->id">
                                {{ $u->is_active ? 'Active' : 'Blocked' }}
                            </button>
                        </td>
                        <td><span style="font-size: 0.85rem; color: var(--color-text-secondary); font-family: monospace;">{{ $u->created_at?->format('Y-m-d H:i') ?: 'System default' }}</span></td>
                        <td>
                            <div class="action-btn-group">
                                @if(auth()->user()->hasRole('super-admin') && auth()->id() !== $u->id && !$u->hasRole('super-admin'))
                                    <a href="{{ route('impersonate.start', $u->id) }}" 
                                       class="action-btn action-btn-impersonate" 
                                       data-tooltip="Impersonate {{ $u->email }}"
                                       aria-label="Impersonate User">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                            <circle cx="9" cy="7" r="4"/>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                        </svg>
                                    </a>
                                @endif
                                <button type="button" 
                                        class="action-btn action-btn-edit" 
                                        wire:click="openEditUserForm({{ $u->id }})"
                                        data-tooltip="Edit User Account"
                                        aria-label="Edit User">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </button>
                                <button type="button" 
                                        class="action-btn action-btn-delete" 
                                        wire:click="deleteUser({{ $u->id }})" 
                                        onclick="confirm('Are you sure you want to delete this user?') || event.stopImmediatePropagation()" 
                                        :disabled="auth()->id() == $u->id"
                                        data-tooltip="Delete User Account"
                                        aria-label="Delete User">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        <line x1="10" y1="11" x2="10" y2="17"/>
                                        <line x1="14" y1="11" x2="14" y2="17"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center" style="padding: 2rem;">No user accounts found.</td></tr>
                @endforelse
            </x-table>
        @endif

    <!-- ========================================== -->
    <!-- TAB 2: ROLES -->
    <!-- ========================================== -->
    @elseif ($activeTab === 'roles')
        @if ($isFormOpen)
            <div class="card" style="border-color: var(--primary-glow); box-shadow: var(--shadow-md);">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h3 class="card-title" style="margin: 0; font-size: 1.2rem;">
                        {{ $isEditMode ? 'Sync Role Permissions' : 'Register New Role' }}
                    </h3>
                    <button type="button" class="btn btn-secondary" wire:click="$set('isFormOpen', false)" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;">Cancel</button>
                </div>
                <form wire:submit.prevent="saveRole">
                    <x-input label="Role Name Reference *" :error="$errors->first('role_name_input')">
                        <input type="text" class="form-control" wire:model="role_name_input" placeholder="e.g. support-technician">
                    </x-input>
                    
                    <div style="border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem; background: var(--bg-tertiary);" class="mt-4 mb-4">
                        <h4 style="color: var(--secondary); margin-bottom: 1rem; font-size: 1rem; font-weight: 700;">Mapped Permissions Grid</h4>
                        <div class="grid grid-cols-3 gap-3" style="max-height: 250px; overflow-y: auto;">
                            @foreach($permissions as $perm)
                                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 0.5rem 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-card);">
                                    <input type="checkbox" wire:model="selected_permissions" value="{{ $perm->name }}" style="width: 1.15rem; height: 1.15rem; accent-color: var(--primary);">
                                    <span style="font-size: 0.85rem;">{{ str_replace('-', ' ', $perm->name) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex gap-4 mt-6" style="border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                        <button type="submit" class="btn btn-primary">Save Role</button>
                        <button type="button" class="btn btn-secondary" wire:click="$set('isFormOpen', false)">Cancel</button>
                    </div>
                </form>
            </div>
        @else
            <x-table :headers="['Role Name Reference', 'Synced Permissions Count', 'Active Accounts Scope', 'Action']">
                @foreach($roles as $role)
                    <tr>
                        <td><strong style="color: var(--secondary); text-transform: uppercase;">{{ str_replace('-', ' ', $role->name) }}</strong></td>
                        <td>
                            <span class="badge" style="background: rgba(6,182,212,0.12); color: var(--secondary); border: 1px solid rgba(6,182,212,0.25);">
                                {{ $role->name === 'super-admin' ? 'All (Wildcard *)' : $role->permissions->count() . ' permissions' }}
                            </span>
                        </td>
                        <td>{{ $role->users->count() }} Accounts</td>
                        <td>
                            <div class="action-btn-group">
                                <button type="button" 
                                        class="action-btn action-btn-edit" 
                                        wire:click="openEditRoleForm({{ $role->id }})"
                                        data-tooltip="Edit & Sync Permissions"
                                        aria-label="Edit Role">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </button>
                                @if(!in_array($role->name, ['super-admin', 'registrar', 'applicant']))
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
                                @else
                                    <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--color-text-muted); font-size: 0.72rem;">Locked</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-table>
        @endif

    <!-- ========================================== -->
    <!-- TAB 3: PERMISSIONS -->
    <!-- ========================================== -->
    @elseif ($activeTab === 'permissions')
        @if ($isFormOpen)
            <div class="card" style="border-color: var(--primary-glow); box-shadow: var(--shadow-md);">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h3 class="card-title" style="margin: 0; font-size: 1.2rem;">
                        {{ $isEditMode ? 'Edit System Permission' : 'Register Permission Action' }}
                    </h3>
                    <button type="button" class="btn btn-secondary" wire:click="$set('isFormOpen', false)" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;">Cancel</button>
                </div>
                <form wire:submit.prevent="savePermission">
                    <x-input label="Permission Name Slug *" :error="$errors->first('permission_name_input')">
                        <input type="text" class="form-control" wire:model="permission_name_input" placeholder="e.g. view-reports">
                    </x-input>
                    <div class="flex gap-4 mt-6" style="border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                        <button type="submit" class="btn btn-primary">Save Permission</button>
                        <button type="button" class="btn btn-secondary" wire:click="$set('isFormOpen', false)">Cancel</button>
                    </div>
                </form>
            </div>
        @else
            <x-table :headers="['Permission Slug Name', 'Guard Engine Reference', 'Actions']">
                @forelse($permissions as $perm)
                    <tr>
                        <td><code style="background: rgba(99,102,241,0.1); color: var(--primary); padding: 0.2rem 0.5rem; border-radius: 4px;">{{ $perm->name }}</code></td>
                        <td><code>{{ $perm->guard_name }}</code></td>
                        <td>
                            <div class="action-btn-group">
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
                                <button type="button" 
                                        class="action-btn action-btn-delete" 
                                        wire:click="deletePermission({{ $perm->id }})" 
                                        onclick="confirm('Are you sure you want to delete this permission? This will detach it from all roles.') || event.stopImmediatePropagation()"
                                        data-tooltip="Delete Permission"
                                        aria-label="Delete Permission">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        <line x1="10" y1="11" x2="10" y2="17"/>
                                        <line x1="14" y1="11" x2="14" y2="17"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center" style="padding: 2rem;">No system permissions registered.</td></tr>
                @endforelse
            </x-table>
        @endif

    <!-- ========================================== -->
    <!-- TAB 4: CAMPAIGNS -->
    <!-- ========================================== -->
    @elseif ($activeTab === 'campaigns')
        @if ($isFormOpen)
            <div class="card" style="border-color: var(--primary-glow); box-shadow: var(--shadow-md);">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h3 class="card-title" style="margin: 0; font-size: 1.2rem;">
                        {{ $isEditMode ? 'Modify Recruitment Campaign' : 'Create Recruitment Campaign' }}
                    </h3>
                    <button type="button" class="btn btn-secondary" wire:click="$set('isFormOpen', false)" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;">Cancel</button>
                </div>
                <form wire:submit.prevent="saveCampaign">
                    <div class="grid grid-cols-2 gap-4">
                        <x-input label="Job Code *" :error="$errors->first('job_code')">
                            <input type="text" class="form-control" wire:model="job_code" placeholder="CS-FAC-2026">
                        </x-input>
                        <x-input label="Job Position Title *" :error="$errors->first('title')">
                            <input type="text" class="form-control" wire:model="title" placeholder="Assistant Professor">
                        </x-input>
                    </div>
                    <div class="grid grid-cols-3 gap-4 mt-4">
                        <x-select label="Job Track Type *" :error="$errors->first('track_type')">
                            <select class="form-control form-select" wire:model.live="track_type">
                                <option value="ACADEMIC">Academic Faculty Track</option>
                                <option value="ADMINISTRATIVE">Administrative Officer Track</option>
                                <option value="SUPPORT">Support Staff Track</option>
                            </select>
                        </x-select>
                        <x-input label="Department Scope *" :error="$errors->first('department')">
                            <input type="text" class="form-control" wire:model="department" placeholder="Department of IT">
                        </x-input>
                        <x-input label="Processing Fee (PKR) *" :error="$errors->first('application_fee')">
                            <input type="number" class="form-control" wire:model="application_fee">
                        </x-input>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <x-input label="Opening Date *" :error="$errors->first('opening_date')">
                            <input type="datetime-local" class="form-control" wire:model="opening_date">
                        </x-input>
                        <x-input label="Closing Date *" :error="$errors->first('closing_date')">
                            <input type="datetime-local" class="form-control" wire:model="closing_date">
                        </x-input>
                    </div>

                    <div style="border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1rem 1.25rem; background: var(--bg-tertiary); margin-top: 1rem; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="color: var(--color-text-primary); font-size: 0.95rem; display: block;">Academic Transcript Proof Requirement</strong>
                            <small style="color: var(--color-text-secondary);">Enforce mandatory document uploads during candidate profile completion</small>
                        </div>
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-weight: 600; color: var(--color-text-primary);">
                            <input type="checkbox" wire:model="campaign_require_transcript_proof" style="width: 1.15rem; height: 1.15rem; accent-color: var(--primary);">
                            <span>{{ $campaign_require_transcript_proof ? 'Mandatory (Required *)' : 'Optional' }}</span>
                        </label>
                    </div>

                    @if($track_type === 'ACADEMIC')
                        <div style="border: 1px solid var(--secondary-glow); border-radius: var(--radius-md); padding: 1.5rem; background: rgba(6,182,212,0.03);" class="mt-6 mb-6">
                            <h4 style="color: var(--secondary); font-size: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;" class="mb-4">Academic Scoring Weights</h4>
                            <div class="grid grid-cols-3 gap-4">
                                <x-input label="Gold Medal Weight"><input type="number" step="0.1" class="form-control" wire:model="gold_medal_weight"></x-input>
                                <x-input label="Silver Medal Weight"><input type="number" step="0.1" class="form-control" wire:model="silver_medal_weight"></x-input>
                                <x-input label="Bronze Medal Weight"><input type="number" step="0.1" class="form-control" wire:model="bronze_medal_weight"></x-input>
                            </div>
                            <div class="grid grid-cols-4 gap-4 mt-4">
                                <x-input label="HJRS W weight"><input type="number" step="0.05" class="form-control" wire:model="w_category_weight"></x-input>
                                <x-input label="HJRS X weight"><input type="number" step="0.05" class="form-control" wire:model="x_category_weight"></x-input>
                                <x-input label="HJRS Y weight"><input type="number" step="0.05" class="form-control" wire:model="y_category_weight"></x-input>
                                <x-input label="HJRS Z weight"><input type="number" step="0.05" class="form-control" wire:model="z_category_weight"></x-input>
                            </div>
                        </div>
                    @endif

                    <div class="flex gap-4 mt-6" style="border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                        <button type="submit" class="btn btn-primary">Save Campaign</button>
                        <button type="button" class="btn btn-secondary" wire:click="$set('isFormOpen', false)">Cancel</button>
                    </div>
                </form>
            </div>
        @else
            <!-- Campaign List & Committee Assignment Panel -->
            <div class="grid grid-cols-3 gap-6">
                <div class="card" style="grid-column: span 2;">
                    <h3 class="card-title mb-4">Job Openings & Campaigns</h3>
                    <x-table :headers="['Code', 'Position / Dept', 'Track Class', 'Voucher Fee', 'Dates Range', 'Actions']">
                        @foreach($campaigns as $camp)
                            <tr>
                                <td><code style="color: var(--secondary)">{{ $camp->job_code }}</code></td>
                                <td><strong>{{ $camp->title }}</strong><br><small style="color: var(--color-text-secondary)">{{ $camp->department }}</small></td>
                                <td>
                                    <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary); border: 1px solid rgba(99,102,241,0.25);">
                                        {{ $camp->track_type }}
                                    </span>
                                </td>
                                <td><strong>PKR {{ number_format($camp->application_fee, 2) }}</strong></td>
                                <td>
                                    <small style="display: block;">Open: {{ $camp->opening_date?->format('Y-m-d') }}</small>
                                    <small style="color: var(--color-text-secondary)">Close: {{ $camp->closing_date?->format('Y-m-d') }}</small>
                                </td>
                                <td>
                                    <div class="action-btn-group">
                                        <button type="button" 
                                                class="action-btn action-btn-edit" 
                                                wire:click="openEditCampaignForm({{ $camp->id }})"
                                                data-tooltip="Edit Campaign"
                                                aria-label="Edit Campaign">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                        </button>
                                        <button type="button" 
                                                class="action-btn action-btn-delete" 
                                                wire:click="deleteCampaign({{ $camp->id }})" 
                                                onclick="confirm('Are you sure you want to delete this campaign?') || event.stopImmediatePropagation()"
                                                data-tooltip="Delete Campaign"
                                                aria-label="Delete Campaign">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                <line x1="10" y1="11" x2="10" y2="17"/>
                                                <line x1="14" y1="11" x2="14" y2="17"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </x-table>
                </div>
                
                <!-- Committee Assignments sub-panel -->
                <div class="card flex-col gap-4">
                    <h3 class="card-title">Allocate Committee Members</h3>
                    <form wire:submit.prevent="assignCommitteeMember">
                        <x-select label="Select Job Campaign *" :error="$errors->first('selected_campaign_id')">
                            <select class="form-control form-select" wire:model="selected_campaign_id">
                                <option value="">-- Choose Position --</option>
                                @foreach($campaigns as $camp)
                                    <option value="{{ $camp->id }}">{{ $camp->job_code }} - {{ $camp->title }}</option>
                                @endforeach
                            </select>
                        </x-select>

                        <x-select label="Select Committee Member *" :error="$errors->first('selected_user_id')">
                            <select class="form-control form-select" wire:model="selected_user_id">
                                <option value="">-- Select Member --</option>
                                @foreach($committee_users as $cu)
                                    <option value="{{ $cu->id }}">{{ $cu->email }} (Role: {{ str_replace('-', ' ', $cu->roles->first()?->name) }})</option>
                                @endforeach
                            </select>
                        </x-select>

                        <x-select label="Committee Type *" :error="$errors->first('is_appellate_panel')">
                            <select class="form-control form-select" wire:model="is_appellate_panel">
                                <option value="0">Document Scrutiny Panel</option>
                                <option value="1">Appellate Grievance Panel</option>
                            </select>
                        </x-select>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary" style="width: 100%;">Allocate Member</button>
                        </div>
                    </form>

                    <h4 class="mt-4 border-bottom pb-2" style="font-size: 0.95rem; font-weight: 700;">Active Panel Allocations</h4>
                    <div style="max-height: 200px; overflow-y: auto;">
                        @forelse($committee_assignments as $asg)
                            <div style="background: var(--bg-tertiary); border: 1px solid var(--border-color); border-radius: 6px; padding: 0.75rem; margin-bottom: 0.75rem;" class="flex justify-between align-center">
                                <div>
                                    <code style="color: var(--secondary)">{{ $asg->campaign->job_code }}</code><br>
                                    <small>{{ $asg->user->email }}</small><br>
                                    <span class="badge" style="background: {{ $asg->is_appellate_panel ? 'rgba(245,158,11,0.15)' : 'rgba(16,185,129,0.15)' }}; color: {{ $asg->is_appellate_panel ? 'var(--warning)' : 'var(--success)' }}; font-size: 0.75rem;">
                                        {{ $asg->is_appellate_panel ? 'Appellate Panel' : 'Scrutiny Panel' }}
                                    </span>
                                </div>
                                <button type="button" class="btn btn-danger" style="padding: 0.2rem 0.4rem; font-size: 0.75rem;" wire:click="removeCommitteeAssignment({{ $asg->id }})">×</button>
                            </div>
                        @empty
                            <p style="color: var(--color-text-muted); font-size: 0.85rem;" class="text-center">No panel allocations recorded.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

    <!-- ========================================== -->
    <!-- TAB 5: APPLICATIONS -->
    <!-- ========================================== -->
    @elseif ($activeTab === 'applications')
        @if ($isFormOpen)
            <div class="card" style="border-color: var(--primary-glow); box-shadow: var(--shadow-md);">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h3 class="card-title" style="margin: 0; font-size: 1.2rem;">
                        {{ $isEditMode ? 'Manage Application Status' : 'Register Application Record' }}
                    </h3>
                    <button type="button" class="btn btn-secondary" wire:click="$set('isFormOpen', false)" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;">Cancel</button>
                </div>
                <form wire:submit.prevent="saveApplication">
                    @if(!$isEditMode)
                        <div class="grid grid-cols-2 gap-4">
                            <x-select label="Select Applicant Account *" :error="$errors->first('app_user_id')">
                                <select class="form-control form-select" wire:model="app_user_id">
                                    <option value="">-- Choose Account --</option>
                                    @foreach($users as $au)
                                        <option value="{{ $au->id }}">{{ $au->email }}</option>
                                    @endforeach
                                </select>
                            </x-select>
                            <x-select label="Select Recruitment Campaign *" :error="$errors->first('app_campaign_id')">
                                <select class="form-control form-select" wire:model="app_campaign_id">
                                    <option value="">-- Choose Campaign --</option>
                                    @foreach($campaigns as $ac)
                                        <option value="{{ $ac->id }}">{{ $ac->job_code }} - {{ $ac->title }}</option>
                                    @endforeach
                                </select>
                            </x-select>
                        </div>
                    @endif
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <x-select label="Application Processing Status *" :error="$errors->first('app_status')">
                            <select class="form-control form-select" wire:model="app_status">
                                <option value="DRAFT">Draft</option>
                                <option value="SUBMITTED">Submitted</option>
                                <option value="PAID">Paid / In Scrutiny</option>
                                <option value="ELIGIBLE">Eligible</option>
                                <option value="INELIGIBLE">Ineligible</option>
                                <option value="APPEALED">Appealed Grievance</option>
                                <option value="UNDER_REVIEW">Under Appellate Review</option>
                                <option value="APPROVED">Approved for Evaluation</option>
                                <option value="REJECTED">Appellate Rejected</option>
                            </select>
                        </x-select>
                        <x-input label="Disqualification Reason (If ineligible)" :error="$errors->first('app_disqualification_reason')">
                            <input type="text" class="form-control" wire:model="app_disqualification_reason" placeholder="e.g. Over-age / Incomplete transcript proof">
                        </x-input>
                    </div>
                    <div class="flex gap-4 mt-6" style="border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                        <button type="submit" class="btn btn-primary">Save Application</button>
                        <button type="button" class="btn btn-secondary" wire:click="$set('isFormOpen', false)">Cancel</button>
                    </div>
                </form>
            </div>
        @else
            <x-table :headers="['Candidate details', 'Campaign Code', 'Voucher status', 'Processing stage', 'Submitted Timestamp', 'Actions']">
                @forelse($applications as $app)
                    <tr>
                        <td>
                            <strong style="color: var(--color-text-primary)">{{ $app->user->basicInfo?->first_name ?? 'N/A' }} {{ $app->user->basicInfo?->last_name ?? 'N/A' }}</strong><br>
                            <small style="color: var(--color-text-secondary)">{{ $app->user->email }}</small>
                        </td>
                        <td><code>{{ $app->campaign->job_code }}</code></td>
                        <td>
                            <span class="badge" style="background: {{ $app->billingLedger?->status === 'SETTLED' ? 'rgba(16,185,129,0.15)' : 'rgba(245,158,11,0.15)' }}; color: {{ $app->billingLedger?->status === 'SETTLED' ? 'var(--success)' : 'var(--warning)' }};">
                                {{ $app->billingLedger?->status ?: 'No Voucher' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary); border: 1px solid rgba(99,102,241,0.25);">
                                {{ $app->status }}
                            </span>
                        </td>
                        <td><span style="font-size: 0.85rem; color: var(--color-text-secondary); font-family: monospace;">{{ $app->submission_timestamp ?: 'In draft' }}</span></td>
                        <td>
                            <div class="action-btn-group">
                                @if(auth()->user()->hasRole('super-admin') && $app->user && auth()->id() !== $app->user->id && !$app->user->hasRole('super-admin'))
                                    <a href="{{ route('impersonate.start', $app->user->id) }}" 
                                       class="action-btn action-btn-impersonate" 
                                       data-tooltip="Impersonate Applicant ({{ $app->user->email }})"
                                       aria-label="Impersonate Applicant">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                            <circle cx="9" cy="7" r="4"/>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                        </svg>
                                    </a>
                                @endif
                                <button type="button" 
                                        class="action-btn action-btn-view" 
                                        wire:click="viewApplicationDetails({{ $app->id }})"
                                        data-tooltip="View Application Dossier"
                                        aria-label="View Details">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </button>
                                <button type="button" 
                                        class="action-btn action-btn-edit" 
                                        wire:click="openEditApplicationForm({{ $app->id }})"
                                        data-tooltip="Manage Application Status"
                                        aria-label="Edit Status">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </button>
                                <button type="button" 
                                        class="action-btn action-btn-delete" 
                                        wire:click="deleteApplication({{ $app->id }})" 
                                        onclick="confirm('Are you sure you want to delete this application record?') || event.stopImmediatePropagation()"
                                        data-tooltip="Delete Application Record"
                                        aria-label="Delete Application">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        <line x1="10" y1="11" x2="10" y2="17"/>
                                        <line x1="14" y1="11" x2="14" y2="17"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center" style="padding: 2rem;">No recruitment applications logged.</td></tr>
                @endforelse
            </x-table>
        @endif

    <!-- ========================================== -->
    <!-- TAB 6: basic_infos -->
    <!-- ========================================== -->
    @elseif ($activeTab === 'basic_infos')
        @if ($isFormOpen)
            <div class="card" style="border-color: var(--primary-glow); box-shadow: var(--shadow-md);">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h3 class="card-title" style="margin: 0; font-size: 1.2rem;">
                        {{ $isEditMode ? 'Modify Profile Details' : 'Create Profile Record' }}
                    </h3>
                    <button type="button" class="btn btn-secondary" wire:click="$set('isFormOpen', false)" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;">Cancel</button>
                </div>
                <form wire:submit.prevent="saveBasicInfo">
                    @if(!$isEditMode)
                        <x-select label="Select Applicant Account *" :error="$errors->first('bi_user_id')">
                            <select class="form-control form-select" wire:model="bi_user_id">
                                <option value="">-- Choose Account --</option>
                                @foreach($users as $pu)
                                    <option value="{{ $pu->id }}">{{ $pu->email }}</option>
                                @endforeach
                            </select>
                        </x-select>
                    @endif
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <x-input label="First Name *" :error="$errors->first('bi_first_name')">
                            <input type="text" class="form-control" wire:model="bi_first_name">
                        </x-input>
                        <x-input label="Last Name *" :error="$errors->first('bi_last_name')">
                            <input type="text" class="form-control" wire:model="bi_last_name">
                        </x-input>
                    </div>
                    <div class="grid grid-cols-3 gap-4 mt-4 align-center">
                        <x-input label="CNIC Reference *" :error="$errors->first('bi_cnic_number')">
                            <input type="text" class="form-control" wire:model="bi_cnic_number" placeholder="xxxxx-xxxxxxx-x">
                        </x-input>
                        <x-input label="Date of Birth *" :error="$errors->first('bi_dob')">
                            <input type="date" class="form-control" wire:model="bi_dob">
                        </x-input>
                        <div class="form-group" style="margin-top: 1.75rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" wire:model="bi_is_locked" style="width: 1.25rem; height: 1.25rem; accent-color: var(--primary);">
                                <span style="font-weight: 600;">Lock Profile (Voucher generated)</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex gap-4 mt-6" style="border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                        <button type="submit" class="btn btn-primary">Save Profile</button>
                        <button type="button" class="btn btn-secondary" wire:click="$set('isFormOpen', false)">Cancel</button>
                    </div>
                </form>
            </div>
        @else
            <x-table :headers="['Candidate Reference', 'Track Type', 'CNIC Number Reference', 'Lock Status', 'Action']">
                @forelse($basic_infos as $basicInfo)
                    <tr>
                        <td>
                            <strong style="color: var(--color-text-primary)">{{ $basicInfo->first_name }} {{ $basicInfo->last_name }}</strong><br>
                            <small style="color: var(--color-text-secondary)">{{ $basicInfo->user->email }}</small>
                        </td>
                        <td>
                            <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary); border: 1px solid rgba(99,102,241,0.25);">
                                {{ $basicInfo->track_type }}
                            </span>
                        </td>
                        <td><code>{{ $basicInfo->cnic_number }}</code></td>
                        <td>
                            <span class="badge" style="background: {{ $basicInfo->is_locked ? 'rgba(239,68,68,0.15)' : 'rgba(16,185,129,0.15)' }}; color: {{ $basicInfo->is_locked ? 'var(--danger)' : 'var(--success)' }};">
                                {{ $basicInfo->is_locked ? 'Locked' : 'Editing' }}
                            </span>
                        </td>
                        <td>
                            <div class="action-btn-group">
                                @if(auth()->user()->hasRole('super-admin') && $basicInfo->user && auth()->id() !== $basicInfo->user->id && !$basicInfo->user->hasRole('super-admin'))
                                    <a href="{{ route('impersonate.start', $basicInfo->user->id) }}" 
                                       class="action-btn action-btn-impersonate" 
                                       data-tooltip="Impersonate {{ $basicInfo->first_name ?: $basicInfo->user->email }}"
                                       aria-label="Impersonate Candidate">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                            <circle cx="9" cy="7" r="4"/>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                        </svg>
                                    </a>
                                @endif
                                <button type="button" 
                                        class="action-btn action-btn-view" 
                                        wire:click="viewBasicInfoDetails({{ $basicInfo->id }})"
                                        data-tooltip="View Full Dossier"
                                        aria-label="View Full Dossier">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </button>
                                <button type="button" 
                                        class="action-btn action-btn-edit" 
                                        wire:click="openEditBasicInfoForm({{ $basicInfo->id }})"
                                        data-tooltip="Edit Profile Particulars"
                                        aria-label="Edit Profile">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </button>
                                <button type="button" 
                                        class="action-btn action-btn-delete" 
                                        wire:click="deleteBasicInfo({{ $basicInfo->id }})" 
                                        onclick="confirm('Are you sure you want to delete this profile?') || event.stopImmediatePropagation()"
                                        data-tooltip="Delete Profile"
                                        aria-label="Delete Profile">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        <line x1="10" y1="11" x2="10" y2="17"/>
                                        <line x1="14" y1="11" x2="14" y2="17"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center" style="padding: 2rem;">No candidate profiles registered in system.</td></tr>
                @endforelse
            </x-table>
        @endif

    <!-- ========================================== -->
    <!-- TAB 7: PAYMENTS -->
    <!-- ========================================== -->
    @elseif ($activeTab === 'payments')
        @if ($isFormOpen)
            <div class="card" style="border-color: var(--primary-glow); box-shadow: var(--shadow-md);">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h3 class="card-title" style="margin: 0; font-size: 1.2rem;">
                        {{ $isEditMode ? 'Edit Processing Voucher Details' : 'Log Processing Voucher' }}
                    </h3>
                    <button type="button" class="btn btn-secondary" wire:click="$set('isFormOpen', false)" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;">Cancel</button>
                </div>
                <form wire:submit.prevent="savePayment">
                    @if(!$isEditMode)
                        <div class="grid grid-cols-2 gap-4">
                            <x-select label="Select Active Application *" :error="$errors->first('pay_app_id')">
                                <select class="form-control form-select" wire:model="pay_app_id">
                                    <option value="">-- Choose Application --</option>
                                    @foreach($applications as $app)
                                        <option value="{{ $app->id }}">{{ $app->user->email }} (Job Code: {{ $app->campaign->job_code }})</option>
                                    @endforeach
                                </select>
                            </x-select>
                            <x-input label="12-digit Consumer ID Reference *" :error="$errors->first('pay_consumer_id')">
                                <input type="text" class="form-control" wire:model="pay_consumer_id" placeholder="e.g. 100012345678">
                            </x-input>
                        </div>
                    @endif
                    <div class="grid grid-cols-3 gap-4 mt-4 align-center">
                        <x-input label="Total Amount (PKR) *" :error="$errors->first('pay_amount')">
                            <input type="number" class="form-control" wire:model="pay_amount">
                        </x-input>
                        <x-select label="Voucher Status *" :error="$errors->first('pay_status')">
                            <select class="form-control form-select" wire:model="pay_status">
                                <option value="PENDING">Pending Payment</option>
                                <option value="AUTHORIZED">Authorized</option>
                                <option value="SETTLED">Settled / PAID</option>
                                <option value="EXPIRED">Expired</option>
                            </select>
                        </x-select>
                        <x-input label="Bank Transaction Reference ID" :error="$errors->first('pay_transaction_id')">
                            <input type="text" class="form-control" wire:model="pay_transaction_id" placeholder="TID-123456">
                        </x-input>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <x-input label="Reconciled Timestamp" :error="$errors->first('pay_timestamp')">
                            <input type="datetime-local" class="form-control" wire:model="pay_timestamp">
                        </x-input>
                    </div>
                    <div class="flex gap-4 mt-6" style="border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                        <button type="submit" class="btn btn-primary">Save Payment</button>
                        <button type="button" class="btn btn-secondary" wire:click="$set('isFormOpen', false)">Cancel</button>
                    </div>
                </form>
            </div>
        @else
            <x-table :headers="['Consumer ID', 'Applicant Account', 'Campaign Scope', 'Amount PKR', 'Voucher status', 'Transaction ID', 'Paid Date', 'Action']">
                @forelse($payments as $pay)
                    <tr>
                        <td><code style="color: var(--secondary)">{{ $pay->consumer_id }}</code></td>
                        <td>{{ $pay->application->user->email }}</td>
                        <td><code>{{ $pay->application->campaign->job_code }}</code></td>
                        <td><strong>PKR {{ number_format($pay->amount, 2) }}</strong></td>
                        <td>
                            <span class="badge" style="background: {{ $pay->status === 'SETTLED' ? 'rgba(16,185,129,0.15)' : 'rgba(245,158,11,0.15)' }}; color: {{ $pay->status === 'SETTLED' ? 'var(--success)' : 'var(--warning)' }};">
                                {{ $pay->status }}
                            </span>
                        </td>
                        <td><code>{{ $pay->transaction_id ?: 'N/A' }}</code></td>
                        <td><span style="font-size: 0.85rem; color: var(--color-text-secondary); font-family: monospace;">{{ $pay->payment_timestamp ?: 'Unpaid' }}</span></td>
                        <td>
                            <div class="action-btn-group">
                                <button type="button" 
                                        class="action-btn action-btn-edit" 
                                        wire:click="openEditPaymentForm({{ $pay->id }})"
                                        data-tooltip="Reconcile / Edit Voucher"
                                        aria-label="Edit Voucher">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </button>
                                <button type="button" 
                                        class="action-btn action-btn-delete" 
                                        wire:click="deletePayment({{ $pay->id }})" 
                                        onclick="confirm('Are you sure you want to delete this payment record?') || event.stopImmediatePropagation()"
                                        data-tooltip="Delete Voucher Record"
                                        aria-label="Delete Voucher">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        <line x1="10" y1="11" x2="10" y2="17"/>
                                        <line x1="14" y1="11" x2="14" y2="17"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center" style="padding: 2rem;">No processing billing vouchers logged.</td></tr>
                @endforelse
            </x-table>
        @endif

    <!-- ========================================== -->
    <!-- TAB 8: AUDIT LOGS -->
    <!-- ========================================== -->
    @elseif ($activeTab === 'logs')
        <div class="card" style="padding: 0; overflow: hidden;">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <h3 class="card-title" style="margin: 0; font-size: 1.15rem; display: flex; align-items: center; gap: 0.5rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                    <span>Security Audit Log Ledger</span>
                </h3>
            </div>
            <x-table :headers="['Action User', 'Access IP', 'Action Mode', 'Target Database Table', 'Record reference ID', 'Timestamp Logged', 'Action']">
                @forelse($logs as $log)
                    <tr>
                        <td><strong>{{ $log->user?->email ?: 'Anonymous / Guest' }}</strong></td>
                        <td><code>{{ $log->ip_address }}</code></td>
                        <td>
                            <span class="badge" style="background: {{ $log->action_type === 'DELETED' ? 'rgba(239,68,68,0.15)' : ($log->action_type === 'CREATED' ? 'rgba(16,185,129,0.15)' : 'rgba(99,102,241,0.15)') }}; color: {{ $log->action_type === 'DELETED' ? 'var(--danger)' : ($log->action_type === 'CREATED' ? 'var(--success)' : 'var(--primary)') }};">
                                {{ $log->action_type }}
                            </span>
                        </td>
                        <td><code>{{ $log->table_name }}</code></td>
                        <td><code>ID #{{ $log->record_id }}</code></td>
                        <td><small style="color: var(--color-text-secondary); font-family: monospace;">{{ $log->logged_at }}</small></td>
                        <td>
                            <button type="button" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="viewLogDetails({{ $log->id }})">Details Diff</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center" style="padding: 2rem;">No security audit records logged.</td></tr>
                @endforelse
            </x-table>
        </div>
    @endif

    <!-- ========================================== -->
    <!-- MODAL POPUP DRAWERS FOR DETAILED DETAILS -->
    <!-- ========================================== -->
    
    <!-- 1. Application Details Modal -->
    <x-modal :show="$isDetailsOpen && $activeTab === 'applications'" title="Application Profile Details Summary" closeAction="$set('isDetailsOpen', false)" style="max-width: 800px;">
        @if($selectedRecordDetails)
            <div style="max-height: 450px; overflow-y: auto; padding-right: 0.5rem;">
                <div class="grid grid-cols-2 gap-4 border-bottom pb-4 mb-4">
                    <div>
                        <p style="color: var(--color-text-secondary)">Candidate Full Name:</p>
                        <strong>{{ $selectedRecordDetails->user->basicInfo?->first_name }} {{ $selectedRecordDetails->user->basicInfo?->last_name }}</strong>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary)">Email Reference:</p>
                        <code>{{ $selectedRecordDetails->user->email }}</code>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4 border-bottom pb-4 mb-4">
                    <div>
                        <p style="color: var(--color-text-secondary)">Job Position:</p>
                        <strong>{{ $selectedRecordDetails->campaign->title }}</strong>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary)">Job Code:</p>
                        <code style="color: var(--secondary)">{{ $selectedRecordDetails->campaign->job_code }}</code>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary)">Track Classification:</p>
                        <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary);">{{ $selectedRecordDetails->campaign->track_type }}</span>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4 border-bottom pb-4 mb-4">
                    <div>
                        <p style="color: var(--color-text-secondary)">Challan Voucher Status:</p>
                        <span class="badge" style="background: {{ $selectedRecordDetails->billingLedger?->status === 'SETTLED' ? 'rgba(16,185,129,0.15)' : 'rgba(245,158,11,0.15)' }}; color: {{ $selectedRecordDetails->billingLedger?->status === 'SETTLED' ? 'var(--success)' : 'var(--warning)' }};">
                            {{ $selectedRecordDetails->billingLedger?->status ?: 'No Voucher' }}
                        </span>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary)">Consumer Reference ID:</p>
                        <code>{{ $selectedRecordDetails->billingLedger?->consumer_id ?: 'N/A' }}</code>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary)">Voucher Amount Paid:</p>
                        <strong style="color: var(--success)">PKR {{ number_format($selectedRecordDetails->billingLedger?->amount ?? 0, 2) }}</strong>
                    </div>
                </div>

                @if(!empty($selectedRecordDetails->evaluations))
                    <h4 class="mb-2 mt-4" style="color: var(--secondary)">Board Members Grading Results</h4>
                    <x-table :headers="['Board Member Account', 'Academic', 'Medals', 'Publications', 'Events', 'Projects', 'Interview', 'Outlier?']">
                        @forelse($selectedRecordDetails->evaluations as $eval)
                            <tr>
                                <td>{{ $eval->boardMember->email }}</td>
                                <td>{{ $eval->academic_score }}</td>
                                <td>{{ $eval->medals_score }}</td>
                                <td>{{ $eval->publications_score }}</td>
                                <td>{{ $eval->events_score }}</td>
                                <td>{{ $eval->project_score }}</td>
                                <td><strong style="color: var(--success)">{{ $eval->interview_score }}</strong></td>
                                <td>
                                    @if($eval->is_outlier)
                                        <span class="badge" style="background: rgba(239,68,68,0.15); color: var(--danger);">Outlier Alert</span><br>
                                        <small style="color: var(--color-text-muted)">Justify: {{ $eval->outlier_justification }}</small>
                                    @else
                                        <span class="badge" style="background: rgba(16,185,129,0.15); color: var(--success);">Normal</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center">No board grades submitted yet.</td></tr>
                        @endforelse
                    </x-table>
                @endif
            </div>
        @endif
    </x-modal>

    <!-- 2. Profile Details Modal -->
    <x-modal :show="$isDetailsOpen && $activeTab === 'basic_infos'" title="Complete Candidate Wizard Profile Details" closeAction="$set('isDetailsOpen', false)" style="max-width: 900px; width: 95%;">
        @if($selectedRecordDetails)
            <div style="max-height: 550px; overflow-y: auto; padding-right: 0.5rem;">
                <div class="grid grid-cols-3 gap-4 border-bottom pb-4 mb-4">
                    <div>
                        <p style="color: var(--color-text-secondary)">First & Last Name:</p>
                        <strong>{{ $selectedRecordDetails->first_name }} {{ $selectedRecordDetails->last_name }}</strong>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary)">CNIC Reference:</p>
                        <code>{{ $selectedRecordDetails->cnic_number }}</code>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary)">Date of Birth:</p>
                        <strong>{{ $selectedRecordDetails->date_of_birth?->format('Y-m-d') }}</strong>
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-4 border-bottom pb-4 mb-4">
                    <div>
                        <p style="color: var(--color-text-secondary)">Gender:</p>
                        <strong>{{ $selectedRecordDetails->gender ?? 'N/A' }}</strong>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary)">Father's Name:</p>
                        <strong>{{ $selectedRecordDetails->father_name ?? 'N/A' }}</strong>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary)">Contact Phone:</p>
                        <strong>{{ $selectedRecordDetails->phone_number ?? 'N/A' }}</strong>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary)">Domicile Scope:</p>
                        <strong>{{ $selectedRecordDetails->domicile ?? 'N/A' }}</strong>
                    </div>
                </div>

                <!-- Academic History (Ascending sorted) -->
                @if($selectedRecordDetails->degrees->isNotEmpty())
                    <h4 style="color: var(--secondary); font-weight: 700;" class="mt-4 mb-2">Chronological Academic Qualifications</h4>
                    <x-table :headers="['Degree', 'Major Specialization', 'Institution / Board', 'Passing Year', 'Marks Obtd / Scale', 'Division/Grade', 'Transcript Proof']">
                        @foreach($selectedRecordDetails->degrees as $deg)
                            <tr>
                                <td><strong>{{ $deg['degree_title'] }}</strong></td>
                                <td>{{ $deg['major'] }}</td>
                                <td>{{ $deg['institution'] }} ({{ $deg['board'] }})</td>
                                <td>{{ $deg['year'] }}</td>
                                <td>{{ $deg['gpa'] }} / {{ $deg['scale'] }}</td>
                                <td>{{ $deg['division'] }}</td>
                                <td>
                                    @if(isset($deg['proof_path']))
                                        <a href="/storage/{{ $deg['proof_path'] }}" target="_blank" style="color: var(--secondary)">View File</a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </x-table>
                @endif

                <!-- Job Experiences -->
                @if($selectedRecordDetails->experiences->isNotEmpty())
                    <h4 style="color: var(--secondary); font-weight: 700;" class="mt-6 mb-2">Employment History Log</h4>
                    <x-table :headers="['Designation', 'Company Name', 'Tenure Dates Range', 'Sector', 'Gross Salary', 'Description', 'Letter File']">
                        @foreach($selectedRecordDetails->experiences as $exp)
                            <tr>
                                <td><strong>{{ $exp->job_title }}</strong></td>
                                <td>{{ $exp->organization }}</td>
                                <td><code>{{ $exp->start_date?->format('Y-m-d') }}</code> to <code>{{ $exp->end_date?->format('Y-m-d') ?: 'Present' }}</code></td>
                                <td>{{ $exp->sector }}</td>
                                <td>PKR {{ number_format($exp->salary ?? 0, 2) }}</td>
                                <td>{{ $exp->description }}</td>
                                <td>
                                    @if(isset($exp['proof_path']))
                                        <a href="/storage/{{ $exp['proof_path'] }}" target="_blank" style="color: var(--secondary)">Open NOC</a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </x-table>
                @endif
            </div>
        @endif
    </x-modal>

    <!-- 3. Security Audit Log Details Modal -->
    <x-modal :show="$isDetailsOpen && $activeTab === 'logs'" title="Security Audit State Details Diff" closeAction="$set('isDetailsOpen', false)" style="max-width: 800px;">
        @if($selectedRecordDetails)
            <div style="max-height: 450px; overflow-y: auto;">
                <div class="grid grid-cols-3 gap-4 border-bottom pb-4 mb-4">
                    <div>
                        <p style="color: var(--color-text-secondary)">Action Class:</p>
                        <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary);">{{ $selectedRecordDetails->action_type }}</span>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary)">Database Table:</p>
                        <code>{{ $selectedRecordDetails->table_name }}</code>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary)">Record Reference ID:</p>
                        <code>ID #{{ $selectedRecordDetails->record_id }}</code>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6 mt-4">
                    <div>
                        <h4 style="color: var(--danger); font-size: 0.95rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
                            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--danger);"></span>
                            State Before Change:
                        </h4>
                        <pre style="background: rgba(0,0,0,0.5); padding: 1rem; border-radius: 6px; font-size: 0.8rem; overflow-x: auto; color: #fca5a5; font-family: 'JetBrains Mono', monospace;">{{ json_encode($selectedRecordDetails->state_before, JSON_PRETTY_PRINT) ?: 'No previous state (NEW INSERT)' }}</pre>
                    </div>
                    <div>
                        <h4 style="color: var(--success); font-size: 0.95rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
                            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--success);"></span>
                            State After Change:
                        </h4>
                        <pre style="background: rgba(0,0,0,0.5); padding: 1rem; border-radius: 6px; font-size: 0.8rem; overflow-x: auto; color: #a7f3d0; font-family: 'JetBrains Mono', monospace;">{{ json_encode($selectedRecordDetails->state_after, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            </div>
        @endif
    </x-modal>
</div>
