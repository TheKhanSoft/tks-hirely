<div>
    <!-- Console Control Header -->
    <div class="card mb-6" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; position: relative; overflow: hidden;">
        <div style="position: absolute; right: -2rem; top: -2rem; width: 140px; height: 140px; background: radial-gradient(circle, var(--primary-glow) 0%, transparent 70%); pointer-events: none;"></div>
        <div>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.35rem;">
                <div class="brand-icon-box" style="width: 38px; height: 38px; border-radius: 10px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="card-title" style="margin: 0; font-size: 1.35rem;">User Accounts & Directory</h2>
                    <p class="card-subtitle" style="margin: 0;">Institutional user administration, security role assignments, and account activation controls</p>
                </div>
            </div>
        </div>
        <div>
            @if(!$isFormOpen)
                @if(auth()->user()->can('create-user') || auth()->user()->hasAnyRole(['super-admin', 'registrar']))
                    <button type="button" class="btn btn-primary" wire:click="openCreateUserForm" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>Register User</span>
                    </button>
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

    <!-- Form Drawer -->
    @if ($isFormOpen)
        <div class="card mb-6" style="border-color: var(--primary-glow); box-shadow: var(--shadow-md);">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                <h3 class="card-title" style="margin: 0; font-size: 1.2rem;">
                    {{ $isEditMode ? 'Edit User Account & Role' : 'Register User Account' }}
                </h3>
                <button type="button" class="btn btn-secondary" wire:click="resetForm" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;">Cancel</button>
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
                            <span style="font-weight: 600;">Allow User Login (Active Account)</span>
                        </label>
                    </div>
                </div>
                <div class="flex gap-4 mt-6" style="border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                    <button type="submit" class="btn btn-primary">Save User</button>
                    <button type="button" class="btn btn-secondary" wire:click="resetForm">Cancel</button>
                </div>
            </form>
        </div>
    @else
        <!-- Filter Bar -->
        <div class="card mb-4" style="padding: 1rem 1.25rem;">
            <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 250px;">
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Search users by email address...">
                </div>
                <div style="width: 200px;">
                    <select class="form-control form-select" wire:model.live="roleFilter">
                        <option value="">All Roles</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}">{{ str_replace('-', ' ', $r->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="width: 180px;">
                    <select class="form-control form-select" wire:model.live="statusFilter">
                        <option value="">All Account Statuses</option>
                        <option value="1">Active</option>
                        <option value="0">Blocked</option>
                    </select>
                </div>
                <div>
                    <span style="font-size: 0.85rem; color: var(--color-text-secondary); font-weight: 600;">
                        Showing {{ count($users) }} user{{ count($users) === 1 ? '' : 's' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <x-table :headers="['Email Address Reference', 'Mapped Role', 'Active Status', 'Date Created', 'Actions']">
            @forelse($users as $u)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.6rem;">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: rgba(99,102,241,0.15); color: var(--primary); font-weight: 700; font-size: 0.8rem; display: flex; align-items: center; justify-content: center;">
                                {{ strtoupper(substr($u->email, 0, 1)) }}
                            </div>
                            <strong style="color: var(--color-text-primary)">{{ $u->email }}</strong>
                            @if(auth()->id() === $u->id)
                                <span class="badge" style="background: rgba(6,182,212,0.15); color: var(--secondary); font-size: 0.7rem;">You</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary); border: 1px solid rgba(99,102,241,0.25); text-transform: capitalize;">
                            {{ str_replace('-', ' ', $u->roles->first()?->name ?? 'None') }}
                        </span>
                    </td>
                    <td>
                        <button type="button" 
                                class="btn {{ $u->is_active ? 'btn-success' : 'btn-danger' }}" 
                                style="padding: 0.2rem 0.6rem; font-size: 0.75rem; border-radius: 999px;" 
                                wire:click="toggleUserStatus({{ $u->id }})" 
                                :disabled="auth()->id() == $u->id"
                                title="{{ auth()->id() == $u->id ? 'Cannot deactivate yourself' : 'Click to toggle active status' }}">
                            {{ $u->is_active ? 'Active' : 'Blocked' }}
                        </button>
                    </td>
                    <td><span style="font-size: 0.85rem; color: var(--color-text-secondary); font-family: monospace;">{{ $u->created_at?->format('Y-m-d H:i') ?: 'System default' }}</span></td>
                    <td>
                        <div class="flex gap-2">
                            <button type="button" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="openEditUserForm({{ $u->id }})">Edit</button>
                            @if(auth()->id() !== $u->id)
                                <button type="button" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="deleteUser({{ $u->id }})" onclick="confirm('Are you sure you want to delete this user?') || event.stopImmediatePropagation()">Delete</button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center" style="padding: 2.5rem; color: var(--color-text-secondary);">No user accounts found.</td></tr>
            @endforelse
        </x-table>
    @endif
</div>

