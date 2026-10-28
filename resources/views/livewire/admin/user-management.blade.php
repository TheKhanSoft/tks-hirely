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
                @can('create-user')
                    <button type="button" class="btn btn-primary" wire:click="openCreateUserForm" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>Register User</span>
                    </button>
                @endcan
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
        <!-- Directory Category Tabs -->
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1rem;">
            <button type="button" class="btn {{ $categorySection === 'ALL' ? 'btn-primary' : 'btn-secondary' }}" 
                    style="padding: 0.45rem 0.85rem; font-size: 0.82rem; border-radius: var(--radius-sm);"
                    wire:click="filterCategory('ALL')">
                All User Accounts ({{ $totalCount }})
            </button>
            <button type="button" class="btn {{ $categorySection === 'STAFF' ? 'btn-primary' : 'btn-secondary' }}" 
                    style="padding: 0.45rem 0.85rem; font-size: 0.82rem; border-radius: var(--radius-sm);"
                    wire:click="filterCategory('STAFF')">
                🏛️ Institutional Staff & Leadership ({{ $staffCount }})
            </button>
            <button type="button" class="btn {{ $categorySection === 'COMMITTEES' ? 'btn-primary' : 'btn-secondary' }}" 
                    style="padding: 0.45rem 0.85rem; font-size: 0.82rem; border-radius: var(--radius-sm);"
                    wire:click="filterCategory('COMMITTEES')">
                👥 Committee Panelists ({{ $committeeCount }})
            </button>
            <button type="button" class="btn {{ $categorySection === 'REVIEWERS' ? 'btn-primary' : 'btn-secondary' }}" 
                    style="padding: 0.45rem 0.85rem; font-size: 0.82rem; border-radius: var(--radius-sm);"
                    wire:click="filterCategory('REVIEWERS')">
                🌐 Empanelled Reviewers ({{ $reviewerCount }})
            </button>
            <button type="button" class="btn {{ $categorySection === 'APPLICANTS' ? 'btn-primary' : 'btn-secondary' }}" 
                    style="padding: 0.45rem 0.85rem; font-size: 0.82rem; border-radius: var(--radius-sm);"
                    wire:click="filterCategory('APPLICANTS')">
                🎓 Job Applicants ({{ $applicantCount }})
            </button>
        </div>

        <!-- Filter Bar -->
        <div class="card mb-4" style="padding: 1rem 1.25rem;">
            <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 250px;">
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Search by name, email, department, or CNIC...">
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
                        Showing {{ count($users) }} record{{ count($users) === 1 ? '' : 's' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <x-table :headers="['Personnel / User Reference', 'Classification', 'Department / Institution', 'Role', 'Status', 'Actions']">
            @forelse($users as $u)
                @php
                    $isCommittee = $u->hasAnyRole(['scrutiny-committee', 'scrutiny', 'appellate-committee', 'appellate', 'selection-board-member', 'board-member', 'board']) || $u->committees->isNotEmpty();
                    $isReviewer = $u->hasAnyRole(['academic-reviewer', 'reviewer']) || $u->reviewerProfile;
                    $isApplicant = $u->hasRole('applicant') && !$u->hasAnyRoleOtherThan('applicant');
                    $isStaff = !$isApplicant && !$isCommittee && !$isReviewer;

                    $displayName = $u->reviewerProfile?->name 
                        ?? ($u->basicInfo?->first_name ? ($u->basicInfo->first_name . ' ' . $u->basicInfo->last_name) : null);
                    
                    $affiliation = $u->reviewerProfile?->university 
                        ?? ($isStaff || $isCommittee ? 'Main Campus / Head Office' : null);

                    $country = $u->reviewerProfile?->country 
                        ?? ($u->basicInfo?->nationality ?: 'Domestic / Local');
                @endphp
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.65rem;">
                            <div style="width: 34px; height: 34px; border-radius: 50%; background: rgba(99,102,241,0.15); color: var(--primary); font-weight: 800; font-size: 0.85rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                {{ strtoupper(substr($displayName ?: $u->email, 0, 1)) }}
                            </div>
                            <div style="overflow: hidden;">
                                @if($displayName)
                                    <div style="font-weight: 700; color: var(--color-text-primary); font-size: 0.92rem;">
                                        {{ $displayName }}
                                    </div>
                                @endif
                                <div style="font-size: 0.8rem; color: var(--color-text-secondary); display: flex; align-items: center; gap: 0.35rem;">
                                    <span>{{ $u->email }}</span>
                                    @if(auth()->id() === $u->id)
                                        <span class="badge" style="background: rgba(6,182,212,0.15); color: var(--secondary); font-size: 0.68rem; padding: 0.1rem 0.35rem;">You</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($isStaff)
                            <span class="member-category-badge internal" style="font-size: 0.72rem;">
                                🏛️ Leadership & Staff
                            </span>
                        @elseif($isCommittee)
                            <span class="member-category-badge internal" style="font-size: 0.72rem;">
                                👥 Committee Panelist
                            </span>
                        @elseif($isReviewer)
                            <span class="member-category-badge external" style="font-size: 0.72rem;">
                                🌐 External Reviewer
                            </span>
                        @else
                            <span class="badge" style="background: rgba(255,255,255,0.06); color: var(--color-text-secondary); font-size: 0.72rem; border: 1px solid var(--border-color);">
                                🎓 Job Applicant
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($affiliation)
                            <div style="font-size: 0.85rem; font-weight: 600; color: var(--color-text-primary);">{{ $affiliation }}</div>
                            <span class="country-pill" style="font-size: 0.7rem; margin-top: 0.15rem;">{{ $country }}</span>
                        @else
                            <span style="font-size: 0.8rem; color: var(--color-text-muted);">Independent / Direct Account</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary); border: 1px solid rgba(99,102,241,0.25); text-transform: capitalize; font-size: 0.75rem;">
                            {{ str_replace('-', ' ', $u->roles->first()?->name ?? 'None') }}
                        </span>
                        @if($u->permissions->isNotEmpty())
                            <div style="margin-top: 0.25rem;">
                                <span class="badge" style="background: rgba(6,182,212,0.15); color: var(--secondary); font-size: 0.68rem; border: 1px solid rgba(6,182,212,0.3);">
                                    +{{ $u->permissions->count() }} Direct 🔑
                                </span>
                            </div>
                        @endif
                    </td>
                    <td>
                        @can('toggle-user-status')
                            <button type="button" 
                                    class="btn {{ $u->is_active ? 'btn-success' : 'btn-danger' }}" 
                                    style="padding: 0.2rem 0.6rem; font-size: 0.75rem; border-radius: 999px;" 
                                    wire:click="toggleUserStatus({{ $u->id }})" 
                                    :disabled="auth()->id() == $u->id"
                                    title="{{ auth()->id() == $u->id ? 'Cannot deactivate yourself' : 'Click to toggle active status' }}">
                                {{ $u->is_active ? 'Active' : 'Blocked' }}
                            </button>
                        @else
                            <span class="badge" style="background: {{ $u->is_active ? 'rgba(16,185,129,0.15)' : 'rgba(239,68,68,0.15)' }}; color: {{ $u->is_active ? 'var(--success)' : 'var(--danger)' }}; font-size: 0.75rem; border-radius: 999px;">
                                {{ $u->is_active ? 'Active' : 'Blocked' }}
                            </span>
                        @endcan
                    </td>
                    <td>
                        <div class="flex gap-2">
                            @can('view-user-dossier')
                                <button type="button" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 0.3rem;" wire:click="viewPersonnelDetails({{ $u->id }})">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <span>Dossier</span>
                                </button>
                            @endcan
                            @can('assign-user-permissions')
                                <button type="button" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 0.25rem;" wire:click="openDirectPermissionsModal({{ $u->id }})" title="Grant extra direct permissions on top of role">
                                    <span>🔑</span>
                                    <span>Permissions</span>
                                </button>
                            @endcan
                            @can('edit-user')
                                <button type="button" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.78rem;" wire:click="openEditUserForm({{ $u->id }})">Edit</button>
                            @endcan
                            @if(auth()->id() !== $u->id)
                                @can('delete-user')
                                    <button type="button" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.78rem;" wire:click="deleteUser({{ $u->id }})" onclick="confirm('Are you sure you want to delete this user?') || event.stopImmediatePropagation()">Delete</button>
                                @endcan
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center" style="padding: 2.5rem; color: var(--color-text-secondary);">No user accounts found matching your filter criteria.</td></tr>
            @endforelse
        </x-table>
    @endif

    <!-- Personnel Profile Dossier Modal -->
    @if($isPersonnelModalOpen && $selectedPersonnel)
        @php
            $pName = $selectedPersonnel->reviewerProfile?->name 
                ?? ($selectedPersonnel->basicInfo?->first_name ? ($selectedPersonnel->basicInfo->first_name . ' ' . $selectedPersonnel->basicInfo->last_name) : $selectedPersonnel->email);
            $pAffil = $selectedPersonnel->reviewerProfile?->university 
                ?? ($selectedPersonnel->hasAnyRole(['registrar', 'super-admin', 'scrutiny-committee', 'selection-board-member', 'appellate-committee']) ? 'Main Campus / Head Office' : 'Independent');
            $pCountry = $selectedPersonnel->reviewerProfile?->country 
                ?? ($selectedPersonnel->basicInfo?->nationality ?: 'Domestic / Local');
        @endphp
        <div style="position: fixed; inset: 0; background: rgba(0, 0, 0, 0.8); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 1.5rem; backdrop-filter: blur(8px);">
            <div class="card" style="width: 100%; max-width: 650px; max-height: 90vh; overflow-y: auto; border-color: var(--primary); box-shadow: 0 20px 48px -10px rgba(0, 0, 0, 0.8), 0 0 20px var(--primary-glow);">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 44px; height: 44px; border-radius: 50%; background: rgba(99,102,241,0.2); color: var(--primary); font-weight: 800; font-size: 1.15rem; display: flex; align-items: center; justify-content: center;">
                            {{ strtoupper(substr($pName, 0, 1)) }}
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 1.2rem; font-weight: 800; color: var(--color-text-primary);">
                                {{ $pName }}
                            </h3>
                            <div style="font-size: 0.82rem; color: var(--color-text-secondary);">
                                {{ $selectedPersonnel->email }} &bull; Account #{{ $selectedPersonnel->id }}
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary" style="padding: 0.35rem 0.65rem;" wire:click="closePersonnelModal">
                        ✕
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem;">
                        <div style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: var(--color-text-muted);">Affiliation & Office</div>
                        <div style="font-weight: 700; color: var(--color-text-primary); margin-top: 0.2rem;">{{ $pAffil }}</div>
                        <span class="country-pill" style="margin-top: 0.35rem;">{{ $pCountry }}</span>
                    </div>

                    <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem;">
                        <div style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: var(--color-text-muted);">Assigned System Role</div>
                        <div style="font-weight: 700; color: var(--primary); margin-top: 0.2rem; text-transform: capitalize;">
                            {{ str_replace('-', ' ', $selectedPersonnel->roles->pluck('name')->join(', ') ?: 'No Role') }}
                        </div>
                        <div style="font-size: 0.78rem; color: var(--color-text-secondary); margin-top: 0.25rem;">
                            Status: <strong style="color: {{ $selectedPersonnel->is_active ? 'var(--success)' : 'var(--danger)' }}">{{ $selectedPersonnel->is_active ? 'Active' : 'Blocked' }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Contact Details -->
                <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem; margin-bottom: 1rem;">
                    <div style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: var(--color-text-muted); margin-bottom: 0.5rem;">Contact & Identification Reference</div>
                    <div class="grid grid-cols-2 gap-3" style="font-size: 0.84rem;">
                        <div>
                            <span style="color: var(--color-text-muted);">Phone:</span> 
                            <strong style="color: var(--color-text-primary);">{{ $selectedPersonnel->basicInfo?->phone_number ?? ($selectedPersonnel->reviewerProfile?->phone ?? 'N/A') }}</strong>
                        </div>
                        <div>
                            <span style="color: var(--color-text-muted);">CNIC / Passport:</span> 
                            <strong style="color: var(--color-text-primary);">{{ $selectedPersonnel->basicInfo?->cnic_number ?? 'N/A' }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Committee Panels Assignment Ledger -->
                @if($selectedPersonnel->committees && $selectedPersonnel->committees->isNotEmpty())
                    <div style="background: rgba(6,182,212,0.05); border: 1px solid rgba(6,182,212,0.25); border-radius: var(--radius-sm); padding: 0.85rem; margin-bottom: 1rem;">
                        <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: var(--secondary); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                            <span>Active Committee Panel Assignments</span>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                            @foreach($selectedPersonnel->committees as $comm)
                                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.82rem; background: rgba(255,255,255,0.03); padding: 0.4rem 0.6rem; border-radius: 4px;">
                                    <div>
                                        <strong style="color: var(--color-text-primary);">{{ $comm->campaign?->title ?? 'Campaign #'.$comm->campaign_id }}</strong>
                                        <span style="font-size: 0.72rem; color: var(--color-text-muted); margin-left: 0.3rem;">({{ $comm->campaign?->job_code }})</span>
                                    </div>
                                    <span class="badge" style="background: {{ $comm->is_appellate_panel ? 'rgba(245,158,11,0.15)' : 'rgba(16,185,129,0.15)' }}; color: {{ $comm->is_appellate_panel ? 'var(--warning)' : 'var(--success)' }}; font-size: 0.7rem;">
                                        {{ $comm->is_appellate_panel ? 'Appellate Panel' : 'Scrutiny Panel' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Reviewer Dossier if applicable -->
                @if($selectedPersonnel->reviewerProfile)
                    <div style="background: rgba(139,92,246,0.05); border: 1px solid rgba(139,92,246,0.25); border-radius: var(--radius-sm); padding: 0.85rem; margin-bottom: 1rem;">
                        <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: #c084fc; margin-bottom: 0.4rem;">
                            Empanelled Academic Reviewer Dossier
                        </div>
                        <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.5;">
                            <div>Rank: <strong style="color: var(--color-text-primary);">{{ $selectedPersonnel->reviewerProfile->academic_rank }}</strong></div>
                            <div>Field / Subject: <strong style="color: var(--color-text-primary);">{{ $selectedPersonnel->reviewerProfile->subject }}</strong></div>
                            <div>Specialization: <strong style="color: var(--color-text-primary);">{{ $selectedPersonnel->reviewerProfile->specialization ?: 'Generalist' }}</strong></div>
                        </div>
                    </div>
                @endif

                <div style="display: flex; justify-content: flex-end; margin-top: 1.25rem;">
                    <button type="button" class="btn btn-secondary" wire:click="closePersonnelModal">
                        Close Dossier
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Direct / Extra User Permissions Modal -->
    @if($isDirectPermissionsModalOpen && $targetUserIdForPermissions)
        @php
            $targetUser = \App\Models\User::find($targetUserIdForPermissions);
        @endphp
        <div style="position: fixed; inset: 0; background: rgba(0, 0, 0, 0.82); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 1.5rem; backdrop-filter: blur(8px);">
            <div class="card" style="width: 100%; max-width: 820px; max-height: 90vh; overflow-y: auto; border-color: var(--secondary); box-shadow: 0 20px 48px -10px rgba(0, 0, 0, 0.8), 0 0 25px var(--secondary-glow);">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.25rem;">
                    <div>
                        <h3 style="margin: 0; font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary); display: flex; align-items: center; gap: 0.5rem;">
                            <span>🔑 Manage Direct / Extra User Permissions</span>
                        </h3>
                        <p style="margin: 0.25rem 0 0 0; font-size: 0.85rem; color: var(--color-text-secondary);">
                            Target: <strong style="color: var(--color-text-primary);">{{ $targetUser?->email }}</strong> &bull; Primary Role: <span class="badge" style="background: rgba(99,102,241,0.15); color: var(--primary);">{{ $targetUser?->roles->first()?->name }}</span>
                        </p>
                    </div>
                    <button type="button" class="btn btn-secondary" style="padding: 0.35rem 0.65rem;" wire:click="closeDirectPermissionsModal">
                        ✕
                    </button>
                </div>

                <div style="background: rgba(6,182,212,0.06); border: 1px solid rgba(6,182,212,0.25); border-radius: var(--radius-sm); padding: 0.85rem 1rem; margin-bottom: 1.25rem; font-size: 0.84rem; color: var(--color-text-secondary); line-height: 1.5;">
                    💡 <strong>Granular Access Control:</strong> Permissions can be granted to security roles, or granted directly to this specific user on top of their role. If a permission is already inherited from their role, it is marked with <span class="badge" style="background: rgba(99,102,241,0.2); color: var(--primary); font-size: 0.7rem;">Role Inherited</span>. You can toggle any extra permissions below.
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <span style="font-size: 0.84rem; font-weight: 700; color: var(--color-text-primary);">
                        Active Direct Permissions Selected: <span style="color: var(--secondary); font-size: 0.95rem;">{{ count($targetUserDirectPermissions) }}</span>
                    </span>
                </div>

                <!-- Grouped Permissions List -->
                <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.5rem;">
                    @foreach($allGroupedPermissions as $groupName => $perms)
                        <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem 1rem;">
                            <div style="font-size: 0.85rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.6rem; text-transform: uppercase; letter-spacing: 0.04em;">
                                {{ $groupName }}
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach($perms as $permKey => $permMeta)
                                    @php
                                        $isInherited = in_array($permKey, $targetUserRolePermissions);
                                        $isDirect = in_array($permKey, $targetUserDirectPermissions);
                                    @endphp
                                    <div style="display: flex; align-items: flex-start; gap: 0.6rem; padding: 0.5rem 0.65rem; border-radius: 6px; background: {{ $isDirect ? 'rgba(6,182,212,0.1)' : ($isInherited ? 'rgba(99,102,241,0.06)' : 'rgba(255,255,255,0.015)') }}; border: 1px solid {{ $isDirect ? 'var(--secondary)' : ($isInherited ? 'rgba(99,102,241,0.25)' : 'var(--border-color)') }};">
                                        <input type="checkbox" 
                                               style="margin-top: 0.2rem; width: 1.15rem; height: 1.15rem; accent-color: var(--secondary); cursor: pointer;"
                                               wire:click="toggleDirectPermission('{{ $permKey }}')"
                                               {{ $isDirect ? 'checked' : '' }}>
                                        <div style="flex: 1; cursor: pointer;" wire:click="toggleDirectPermission('{{ $permKey }}')">
                                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.4rem;">
                                                <strong style="font-size: 0.82rem; color: var(--color-text-primary);">{{ $permMeta['label'] }}</strong>
                                                @if($isInherited)
                                                    <span class="badge" style="background: rgba(99,102,241,0.2); color: var(--primary); font-size: 0.65rem; padding: 0.1rem 0.35rem;">Role</span>
                                                @endif
                                                @if($isDirect)
                                                    <span class="badge" style="background: rgba(6,182,212,0.2); color: var(--secondary); font-size: 0.65rem; padding: 0.1rem 0.35rem;">★ Extra</span>
                                                @endif
                                            </div>
                                            <code style="font-size: 0.72rem; color: var(--color-text-muted); display: block; margin-top: 0.1rem;">{{ $permKey }}</code>
                                            <div style="font-size: 0.74rem; color: var(--color-text-secondary); margin-top: 0.2rem; line-height: 1.35;">
                                                {{ $permMeta['description'] }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding-top: 1rem;">
                    <button type="button" class="btn btn-secondary" wire:click="closeDirectPermissionsModal">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.5rem;" wire:click="saveDirectPermissions">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        <span>Save Direct Extra Permissions</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

