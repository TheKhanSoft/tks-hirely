<div>
    <!-- Executive Control Header -->
    <div class="page-hero-header">
        <div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div class="brand-icon-box">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" style="color: #ffffff;">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div>
                    <h1>User Accounts & Personnel Directory</h1>
                    <p>Institutional user administration, security role assignments, direct permissions, and activation governance.</p>
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            @if(!$isFormOpen)
                @can('create-user')
                    <button type="button" class="btn btn-primary" wire:click="openCreateUserForm" style="display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 18px -2px rgba(99,102,241,0.5); padding: 0.65rem 1.35rem; font-weight: 700; border-radius: 10px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>+ Register User Account</span>
                    </button>
                @endcan
            @endif
        </div>
    </div>

    <!-- Live Executive KPI Summary Cards -->
    @if(!$isFormOpen)
        <div class="stat-kpi-grid">
            <div class="metric-card" style="--card-accent: var(--primary); --metric-icon-bg: var(--primary-light); --metric-icon-color: var(--primary);">
                <div class="metric-header">
                    <span class="metric-label">Total Accounts</span>
                    <div class="metric-icon-wrap">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                </div>
                <div class="metric-value" style="color: var(--primary);">{{ $totalCount }}</div>
                <div class="metric-subtext">All active directory users</div>
            </div>

            <div class="metric-card" style="--card-accent: var(--secondary); --metric-icon-bg: var(--secondary-light); --metric-icon-color: var(--secondary);">
                <div class="metric-header">
                    <span class="metric-label">Staff & Leadership</span>
                    <div class="metric-icon-wrap">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M3 21h18M3 7v14M21 7v14M6 11h4M6 15h4M14 11h4M14 15h4M12 3l9 4H3l9-4z"/>
                        </svg>
                    </div>
                </div>
                <div class="metric-value" style="color: var(--secondary);">{{ $staffCount }}</div>
                <div class="metric-subtext">Registrar & administration</div>
            </div>

            <div class="metric-card" style="--card-accent: #3b82f6; --metric-icon-bg: rgba(59,130,246,0.12); --metric-icon-color: #3b82f6;">
                <div class="metric-header">
                    <span class="metric-label">Committees</span>
                    <div class="metric-icon-wrap">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        </svg>
                    </div>
                </div>
                <div class="metric-value" style="color: #3b82f6;">{{ $committeeCount }}</div>
                <div class="metric-subtext">Scrutiny, Board, Appellate</div>
            </div>

            <div class="metric-card" style="--card-accent: #8b5cf6; --metric-icon-bg: rgba(139,92,246,0.12); --metric-icon-color: #8b5cf6;">
                <div class="metric-header">
                    <span class="metric-label">Reviewers</span>
                    <div class="metric-icon-wrap">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                        </svg>
                    </div>
                </div>
                <div class="metric-value" style="color: #8b5cf6;">{{ $reviewerCount }}</div>
                <div class="metric-subtext">Empanelled peer experts</div>
            </div>

            <div class="metric-card" style="--card-accent: var(--success); --metric-icon-bg: var(--success-light); --metric-icon-color: var(--success);">
                <div class="metric-header">
                    <span class="metric-label">Applicants</span>
                    <div class="metric-icon-wrap">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
                        </svg>
                    </div>
                </div>
                <div class="metric-value" style="color: var(--success);">{{ $applicantCount }}</div>
                <div class="metric-subtext">Registered candidates</div>
            </div>
        </div>
    @endif

    <!-- Flash Message Notification -->
    @if(session()->has('message'))
        <div class="alert alert-success mb-6" style="display: flex; align-items: center; gap: 0.75rem; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3); color: var(--success); padding: 0.95rem 1.35rem; border-radius: var(--radius-md);">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <div style="flex: 1; font-weight: 600;">
                <span>{{ session('message') }}</span>
            </div>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger mb-6" style="display: flex; align-items: center; gap: 0.75rem; background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.3); color: var(--danger); padding: 0.95rem 1.35rem; border-radius: var(--radius-md);">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
            <div style="flex: 1; font-weight: 600;">
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Form Drawer -->
    @if ($isFormOpen)
        <div class="card mb-6 card-ambient-mesh" style="border-color: var(--primary); box-shadow: var(--shadow-lg); padding: 1.75rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <h3 class="card-title" style="margin: 0; font-size: 1.25rem; font-weight: 800;">
                        {{ $isEditMode ? 'Modify User Account & Role Privileges' : 'Register New User Account' }}
                    </h3>
                    <p class="card-subtitle" style="margin: 0.2rem 0 0;">
                        Set authentication credentials, primary security role, and login accessibility status.
                    </p>
                </div>
                <button type="button" class="btn btn-secondary btn-sm" wire:click="resetForm">Cancel</button>
            </div>

            <form wire:submit.prevent="saveUser">
                <div class="grid grid-cols-2 gap-5">
                    <x-input label="Official Email Address *" :error="$errors->first('email')">
                        <input type="email" class="form-control" wire:model="email" placeholder="user@university.edu">
                    </x-input>
                    <x-input label="Password {{ $isEditMode ? '(Leave blank to retain current)' : '*' }}" :error="$errors->first('password')">
                        <input type="password" class="form-control" wire:model="password" placeholder="••••••••">
                    </x-input>
                </div>

                <div class="grid grid-cols-2 gap-5 mt-4">
                    <x-select label="Assign Primary System Role *" :error="$errors->first('role_name')">
                        <select class="form-control form-select" wire:model="role_name">
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}">{{ str_replace('-', ' ', $r->name) }}</option>
                            @endforeach
                        </select>
                    </x-select>

                    <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.85rem 1rem; display: flex; align-items: center;">
                        <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; margin: 0;">
                            <input type="checkbox" wire:model="is_active" style="width: 1.25rem; height: 1.25rem; accent-color: var(--primary);">
                            <div>
                                <span style="font-weight: 700; color: var(--color-text-primary);">Allow User Login (Active Account)</span>
                                <div style="font-size: 0.75rem; color: var(--color-text-muted);">Uncheck to temporarily suspend login credentials.</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="flex gap-4 mt-6" style="border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                    <button type="submit" class="btn btn-primary" style="padding: 0.55rem 1.35rem; font-weight: 700;">Save Account</button>
                    <button type="button" class="btn btn-secondary" wire:click="resetForm">Cancel</button>
                </div>
            </form>
        </div>
    @else
        <!-- Directory Classification Segmented Tabs -->
        <div style="display: flex; gap: 0.5rem; background: var(--bg-tertiary); padding: 0.35rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color); width: fit-content; margin-bottom: 1.25rem; flex-wrap: wrap;">
            <button type="button" 
                    class="btn" 
                    wire:click="filterCategory('ALL')"
                    style="padding: 0.45rem 1.15rem; font-size: 0.85rem; border-radius: var(--radius-md); font-weight: 700; display: inline-flex; align-items: center; gap: 0.45rem; transition: var(--transition-base); {{ $categorySection === 'ALL' ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
                <span>All Accounts</span>
                <span style="font-size: 0.72rem; padding: 0.1rem 0.45rem; border-radius: 999px; background: rgba(99,102,241,0.15); color: var(--primary); font-weight: 800;">{{ $totalCount }}</span>
            </button>

            <button type="button" 
                    class="btn" 
                    wire:click="filterCategory('STAFF')"
                    style="padding: 0.45rem 1.15rem; font-size: 0.85rem; border-radius: var(--radius-md); font-weight: 700; display: inline-flex; align-items: center; gap: 0.45rem; transition: var(--transition-base); {{ $categorySection === 'STAFF' ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
                <span>🏛️ Staff & Leadership</span>
                <span style="font-size: 0.72rem; padding: 0.1rem 0.45rem; border-radius: 999px; background: rgba(6,182,212,0.15); color: var(--secondary); font-weight: 800;">{{ $staffCount }}</span>
            </button>

            <button type="button" 
                    class="btn" 
                    wire:click="filterCategory('COMMITTEES')"
                    style="padding: 0.45rem 1.15rem; font-size: 0.85rem; border-radius: var(--radius-md); font-weight: 700; display: inline-flex; align-items: center; gap: 0.45rem; transition: var(--transition-base); {{ $categorySection === 'COMMITTEES' ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
                <span>👥 Committees</span>
                <span style="font-size: 0.72rem; padding: 0.1rem 0.45rem; border-radius: 999px; background: rgba(59,130,246,0.15); color: #3b82f6; font-weight: 800;">{{ $committeeCount }}</span>
            </button>

            <button type="button" 
                    class="btn" 
                    wire:click="filterCategory('REVIEWERS')"
                    style="padding: 0.45rem 1.15rem; font-size: 0.85rem; border-radius: var(--radius-md); font-weight: 700; display: inline-flex; align-items: center; gap: 0.45rem; transition: var(--transition-base); {{ $categorySection === 'REVIEWERS' ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
                <span>🌐 Peer Reviewers</span>
                <span style="font-size: 0.72rem; padding: 0.1rem 0.45rem; border-radius: 999px; background: rgba(139,92,246,0.15); color: #8b5cf6; font-weight: 800;">{{ $reviewerCount }}</span>
            </button>

            <button type="button" 
                    class="btn" 
                    wire:click="filterCategory('APPLICANTS')"
                    style="padding: 0.45rem 1.15rem; font-size: 0.85rem; border-radius: var(--radius-md); font-weight: 700; display: inline-flex; align-items: center; gap: 0.45rem; transition: var(--transition-base); {{ $categorySection === 'APPLICANTS' ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
                <span>🎓 Applicants</span>
                <span style="font-size: 0.72rem; padding: 0.1rem 0.45rem; border-radius: 999px; background: rgba(16,185,129,0.15); color: var(--success); font-weight: 800;">{{ $applicantCount }}</span>
            </button>
        </div>

        <!-- Reusable Dynamic Entity Listing Component: Responsive Card Grid & Table -->
        <x-entity-card-grid 
            :items="$users" 
            entityName="Personnel Accounts" 
            searchPlaceholder="Search personnel name, email, department, or CNIC..."
            :perPage="$perPage" 
            :viewMode="$viewMode" 
            exportAction="exportCsv" 
            importAction="openImportModal">
            
            <!-- Filters Slot -->
            <x-slot:filters>
                <div style="flex: 1; min-width: 180px;">
                    <select class="form-control form-select" wire:model.live="roleFilter">
                        <option value="">All Security Roles</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}">{{ str_replace('-', ' ', $r->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="flex: 1; min-width: 170px;">
                    <select class="form-control form-select" wire:model.live="statusFilter">
                        <option value="">All Account Statuses</option>
                        <option value="1">Active Login Allowed</option>
                        <option value="0">Blocked / Suspended</option>
                    </select>
                </div>

                @if(!empty($search) || !empty($roleFilter) || $statusFilter !== '')
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="$set('search', ''); $set('roleFilter', ''); $set('statusFilter', '');" style="display: inline-flex; align-items: center; gap: 0.35rem;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        <span>Reset Filters</span>
                    </button>
                @endif
            </x-slot:filters>

            <!-- Cards Slot (3 to 5 Cards Per Row) -->
            <x-slot:cards>
                @foreach($users as $u)
                    @php
                        $isSuperAdmin = $u->hasRole('super-admin');
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

                        if ($isSuperAdmin) {
                            $avatarTheme = 'avatar-theme-amber';
                            $roleBadgeClass = 'badge-amber';
                            $roleLabel = '👑 Super Admin';
                        } elseif ($isStaff) {
                            $avatarTheme = 'avatar-theme-indigo';
                            $roleBadgeClass = 'badge-indigo';
                            $roleLabel = '🏛️ Staff & Leadership';
                        } elseif ($isCommittee) {
                            $avatarTheme = 'avatar-theme-cyan';
                            $roleBadgeClass = 'badge-cyan';
                            $roleLabel = '👥 Committee Panelist';
                        } elseif ($isReviewer) {
                            $avatarTheme = 'avatar-theme-purple';
                            $roleBadgeClass = 'badge-purple';
                            $roleLabel = '🌐 External Reviewer';
                        } else {
                            $avatarTheme = 'avatar-theme-emerald';
                            $roleBadgeClass = 'badge-emerald';
                            $roleLabel = '🎓 Job Applicant';
                        }
                    @endphp

                    <x-entity-card 
                        :id="$u->id" 
                        :title="$displayName ?: $u->email" 
                        :subtitle="$displayName ? $u->email : 'Registered User'"
                        :avatar="strtoupper(substr($displayName ?: $u->email, 0, 1))"
                        :avatarClass="$avatarTheme"
                        :is-active="$u->is_active">
                        
                        <!-- Badge Slot: Active / Blocked Status (Above action strip) -->
                        <x-slot:badge>
                            @can('toggle-user-status')
                                <button type="button" 
                                        class="entity-card-status-toggle-btn"
                                        style="font-size: 0.74rem; font-weight: 800; cursor: pointer;" 
                                        wire:click="toggleUserStatus({{ $u->id }})" 
                                        :disabled="auth()->id() == $u->id"
                                        title="{{ auth()->id() == $u->id ? 'Cannot deactivate yourself' : 'Click to toggle user status' }}">
                                    <span style="display: inline-flex; align-items: center; gap: 0.4rem;">
                                        <span class="pulse-dot {{ $u->is_active ? 'online' : 'offline' }}"></span>
                                        <span>{{ $u->is_active ? 'ACTIVE' : 'BLOCKED' }}</span>
                                    </span>
                                    @if(auth()->id() != $u->id)
                                        <span style="font-size: 0.68rem; opacity: 0.75; font-weight: 700; text-transform: uppercase;">Click to toggle ↻</span>
                                    @endif
                                </button>
                            @else
                                <span class="badge" style="background: {{ $u->is_active ? 'rgba(16,185,129,0.15)' : 'rgba(239,68,68,0.15)' }}; color: {{ $u->is_active ? 'var(--success)' : 'var(--danger)' }}; font-size: 0.72rem; border-radius: 999px; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
                                    <span class="pulse-dot {{ $u->is_active ? 'online' : 'offline' }}"></span>
                                    <span>{{ $u->is_active ? 'Active' : 'Blocked' }}</span>
                                </span>
                            @endcan
                        </x-slot:badge>

                        <!-- Tag Slot: Classification -->
                        <x-slot:tag>
                            <span class="badge {{ $roleBadgeClass }}" style="font-weight: 700; font-size: 0.72rem;">
                                {{ $roleLabel }}
                            </span>

                            @if(auth()->id() === $u->id)
                                <span class="badge badge-cyan" style="font-size: 0.68rem; padding: 0.1rem 0.35rem; font-weight: 700; margin-left: 0.25rem;">You</span>
                            @endif
                        </x-slot:tag>

                        <!-- Card Body Metrics -->
                        <div class="entity-card-metric-row">
                            <span class="entity-card-metric-label">Assigned Role:</span>
                            <span class="entity-card-metric-value" style="color: var(--primary); text-transform: capitalize;">
                                {{ str_replace('-', ' ', $u->roles->first()?->name ?? 'No Role') }}
                            </span>
                        </div>

                        <div class="entity-card-metric-row">
                            <span class="entity-card-metric-label">Institution:</span>
                            <span class="entity-card-metric-value" title="{{ $affiliation ?: 'Independent' }}">
                                {{ $affiliation ?: 'Independent Account' }}
                            </span>
                        </div>

                        @if($u->permissions->isNotEmpty())
                            <div class="entity-card-metric-row">
                                <span class="entity-card-metric-label">Granular Keys:</span>
                                <span class="badge" style="background: rgba(6,182,212,0.15); color: var(--secondary); font-size: 0.68rem; font-weight: 700;">
                                    +{{ $u->permissions->count() }} Direct Permissions
                                </span>
                            </div>
                        @endif

                        <!-- Quick Actions (Hover Reveal Icons) -->
                        <x-slot:quickActions>
                            {{-- Impersonate Quick Action --}}
                            @if(auth()->user()->hasRole('super-admin') && auth()->id() !== $u->id && !$u->hasRole('super-admin'))
                                <a href="{{ route('impersonate.start', $u->id) }}" 
                                   class="action-btn action-btn-impersonate" 
                                   data-tooltip="Impersonate {{ $displayName ?: $u->email }}"
                                   aria-label="Impersonate User">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                </a>
                            @endif

                            {{-- View Dossier Quick Action --}}
                            @can('view-user-dossier')
                                <button type="button" 
                                        class="action-btn action-btn-view" 
                                        wire:click="viewPersonnelDetails({{ $u->id }})" 
                                        data-tooltip="View Full Dossier">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                        <line x1="16" y1="13" x2="8" y2="13"/>
                                        <line x1="16" y1="17" x2="8" y2="17"/>
                                    </svg>
                                </button>
                            @endcan

                            {{-- Edit Account Quick Action --}}
                            @can('edit-user')
                                <button type="button" 
                                        class="action-btn action-btn-edit" 
                                        wire:click="openEditUserForm({{ $u->id }})" 
                                        data-tooltip="Edit Account Info">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </button>
                            @endcan
                        </x-slot:quickActions>

                        <!-- Kebab Dropdown Options Menu [ICON + TEXT] -->
                        <x-slot:menuActions>
                            @can('view-user-dossier')
                                <x-entity-card-menu-item 
                                    label="View Full Dossier" 
                                    click="viewPersonnelDetails({{ $u->id }})">
                                    <x-slot:icon>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                    </x-slot:icon>
                                </x-entity-card-menu-item>
                            @endcan

                            @can('edit-user')
                                <x-entity-card-menu-item 
                                    label="Edit User Account" 
                                    click="openEditUserForm({{ $u->id }})">
                                    <x-slot:icon>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </x-slot:icon>
                                </x-entity-card-menu-item>
                            @endcan

                            @can('assign-user-permissions')
                                <x-entity-card-menu-item 
                                    label="Direct Extra Permissions" 
                                    click="openDirectPermissionsModal({{ $u->id }})">
                                    <x-slot:icon>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="7.5" cy="15.5" r="5.5"/><path d="m21 2-9.6 9.6"/><path d="m15.5 7.5 3 3L22 7l-3-3"/></svg>
                                    </x-slot:icon>
                                </x-entity-card-menu-item>
                            @endcan

                            @can('toggle-user-status')
                                <x-entity-card-menu-item 
                                    label="{{ $u->is_active ? 'Suspend Account' : 'Activate Account' }}" 
                                    click="toggleUserStatus({{ $u->id }})">
                                    <x-slot:icon>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                    </x-slot:icon>
                                </x-entity-card-menu-item>
                            @endcan

                            @if(auth()->user()->hasRole('super-admin') && auth()->id() !== $u->id && !$u->hasRole('super-admin'))
                                <div class="entity-card-menu-divider"></div>
                                <x-entity-card-menu-item 
                                    label="Impersonate User" 
                                    href="{{ route('impersonate.start', $u->id) }}" 
                                    :impersonate="true">
                                    <x-slot:icon>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                    </x-slot:icon>
                                </x-entity-card-menu-item>
                            @endif

                            @if(auth()->id() !== $u->id)
                                @can('delete-user')
                                    <div class="entity-card-menu-divider"></div>
                                    <x-entity-card-menu-item 
                                        label="Delete Account" 
                                        click="deleteUser({{ $u->id }})" 
                                        :danger="true"
                                        onclick="confirm('Are you sure you want to permanently delete this user account?') || event.stopImmediatePropagation()">
                                        <x-slot:icon>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        </x-slot:icon>
                                    </x-entity-card-menu-item>
                                @endcan
                            @endif
                        </x-slot:menuActions>
                    </x-entity-card>
                @endforeach
            </x-slot:cards>

            <!-- Table Slot (For Seamless View Toggle) -->
            <x-slot:table>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Personnel / User Reference</th>
                            <th>Classification</th>
                            <th>Department / Institution</th>
                            <th>Role & Permissions</th>
                            <th>Status</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $u)
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
                            <tr wire:key="user-row-{{ $u->id }}">
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.85rem;">
                                        <div class="user-avatar-circle" style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, rgba(99,102,241,0.22), rgba(6,182,212,0.22)); color: var(--primary); font-weight: 800; font-size: 1rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid rgba(99,102,241,0.25);">
                                            {{ strtoupper(substr($displayName ?: $u->email, 0, 1)) }}
                                        </div>
                                        <div style="overflow: hidden;">
                                            @if($displayName)
                                                <div style="font-weight: 800; color: var(--color-text-primary); font-size: 0.94rem;">
                                                    {{ $displayName }}
                                                </div>
                                            @endif
                                            <div style="font-size: 0.78rem; color: var(--color-text-secondary); display: flex; align-items: center; gap: 0.35rem; margin-top: 2px;">
                                                <span>{{ $u->email }}</span>
                                                @if(auth()->id() === $u->id)
                                                    <span class="badge" style="background: rgba(6,182,212,0.15); color: var(--secondary); font-size: 0.68rem; padding: 0.1rem 0.35rem; font-weight: 700;">You</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($isStaff)
                                        <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary); border: 1px solid rgba(99,102,241,0.25); font-weight: 700; font-size: 0.74rem;">
                                            🏛️ Leadership & Staff
                                        </span>
                                    @elseif($isCommittee)
                                        <span class="badge" style="background: rgba(59,130,246,0.12); color: #3b82f6; border: 1px solid rgba(59,130,246,0.25); font-weight: 700; font-size: 0.74rem;">
                                            👥 Committee Panelist
                                        </span>
                                    @elseif($isReviewer)
                                        <span class="badge" style="background: rgba(139,92,246,0.12); color: #8b5cf6; border: 1px solid rgba(139,92,246,0.25); font-weight: 700; font-size: 0.74rem;">
                                            🌐 External Reviewer
                                        </span>
                                    @else
                                        <span class="badge" style="background: rgba(16,185,129,0.12); color: var(--success); border: 1px solid rgba(16,185,129,0.25); font-weight: 700; font-size: 0.74rem;">
                                            🎓 Job Applicant
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($affiliation)
                                        <div style="font-size: 0.86rem; font-weight: 700; color: var(--color-text-primary);">{{ $affiliation }}</div>
                                        <div style="font-size: 0.74rem; color: var(--color-text-muted); margin-top: 2px;">{{ $country }}</div>
                                    @else
                                        <span style="font-size: 0.8rem; color: var(--color-text-muted); font-style: italic;">Independent Account</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--color-text-primary); border: 1px solid var(--border-color); text-transform: capitalize; font-size: 0.76rem; font-weight: 700;">
                                        {{ str_replace('-', ' ', $u->roles->first()?->name ?? 'No Role') }}
                                    </span>
                                    @if($u->permissions->isNotEmpty())
                                        <div style="margin-top: 0.25rem;">
                                            <span class="badge" style="background: rgba(6,182,212,0.15); color: var(--secondary); font-size: 0.68rem; border: 1px solid rgba(6,182,212,0.3); font-weight: 700;">
                                                +{{ $u->permissions->count() }} Direct 🔑
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @can('toggle-user-status')
                                        <button type="button" 
                                                class="badge {{ $u->is_active ? 'badge-success' : 'badge-danger' }}" 
                                                style="cursor: pointer; border: 1px solid currentColor; background: {{ $u->is_active ? 'rgba(16,185,129,0.12)' : 'rgba(239,68,68,0.12)' }};" 
                                                wire:click="toggleUserStatus({{ $u->id }})" 
                                                :disabled="auth()->id() == $u->id"
                                                title="{{ auth()->id() == $u->id ? 'Cannot deactivate yourself' : 'Click to toggle account status' }}">
                                            <span class="badge-dot" style="background: currentColor;"></span> {{ $u->is_active ? 'Active' : 'Blocked' }}
                                        </button>
                                    @else
                                        <span class="badge {{ $u->is_active ? 'badge-success' : 'badge-danger' }}">
                                            <span class="badge-dot" style="background: currentColor;"></span> {{ $u->is_active ? 'Active' : 'Blocked' }}
                                        </span>
                                    @endcan
                                </td>
                                <td style="text-align: right;">
                                    <div class="action-btn-group justify-end" x-data="{ openMenu: false }">
                                        @can('view-user-dossier')
                                            <button type="button" class="action-btn action-btn-view" wire:click="viewPersonnelDetails({{ $u->id }})" data-tooltip="View Personnel Dossier">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                            </button>
                                        @endcan
                                        @can('edit-user')
                                            <button type="button" class="action-btn action-btn-edit" wire:click="openEditUserForm({{ $u->id }})" data-tooltip="Edit User Account">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                            </button>
                                        @endcan
                                        
                                        <!-- 3-Dot More Actions Dropdown -->
                                        <div class="table-action-dropdown-wrap" @click.outside="openMenu = false">
                                            <button type="button" 
                                                    class="action-btn" 
                                                    @click="openMenu = !openMenu" 
                                                    data-tooltip="More Actions" 
                                                    aria-label="More Actions">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                                                    <circle cx="12" cy="5" r="1.5"/>
                                                    <circle cx="12" cy="12" r="1.5"/>
                                                    <circle cx="12" cy="19" r="1.5"/>
                                                </svg>
                                            </button>
                                            <div class="table-action-dropdown" x-show="openMenu" x-cloak style="display: none;" @click="openMenu = false">
                                                @if(auth()->user()->hasRole('super-admin') && auth()->id() !== $u->id && !$u->hasRole('super-admin'))
                                                    <a href="{{ route('impersonate.start', $u->id) }}" class="table-action-menu-item impersonate">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                                        <span>Impersonate User</span>
                                                    </a>
                                                @endif
                                                @can('assign-user-permissions')
                                                    <button type="button" class="table-action-menu-item" wire:click="openDirectPermissionsModal({{ $u->id }})">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="7.5" cy="15.5" r="5.5"/><path d="m21 2-9.6 9.6"/><path d="m15.5 7.5 3 3L22 7l-3-3"/></svg>
                                                        <span>Direct Extra Permissions</span>
                                                    </button>
                                                @endcan
                                                @if(auth()->id() !== $u->id)
                                                    @can('delete-user')
                                                        <div class="table-action-menu-divider"></div>
                                                        <button type="button" class="table-action-menu-item danger" wire:click="deleteUser({{ $u->id }})" onclick="confirm('Are you sure you want to permanently delete this user account?') || event.stopImmediatePropagation()">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                            <span>Delete User Account</span>
                                                        </button>
                                                    @endcan
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-slot:table>
        </x-entity-card-grid>

        <!-- Universal Import Modal for Users -->
        <x-entity-import-modal 
            :isOpen="$isImportModalOpen" 
            entityName="Personnel Accounts" 
            :sampleColumns="['email', 'password', 'role_name', 'is_active']" />
    @endif

    <!-- Personnel Profile Dossier Modal -->
    <x-modal :show="$isPersonnelModalOpen" title="Complete Personnel Reference & Committee Ledger" closeAction="closePersonnelModal" style="max-width: 720px; width: 95%;">
        @if($selectedPersonnel)
            @php
                $pName = $selectedPersonnel->reviewerProfile?->name 
                    ?? ($selectedPersonnel->basicInfo?->first_name ? ($selectedPersonnel->basicInfo->first_name . ' ' . $selectedPersonnel->basicInfo->last_name) : $selectedPersonnel->email);
                $pAffil = $selectedPersonnel->reviewerProfile?->university 
                    ?? ($selectedPersonnel->hasAnyRole(['registrar', 'super-admin', 'scrutiny-committee', 'selection-board-member', 'appellate-committee']) ? 'Main Campus / Head Office' : 'Independent');
                $pCountry = $selectedPersonnel->reviewerProfile?->country 
                    ?? ($selectedPersonnel->basicInfo?->nationality ?: 'Domestic / Local');
            @endphp
            <div style="max-height: 75vh; overflow-y: auto; padding-right: 0.25rem;">
                <!-- Personnel Banner -->
                <div style="display: flex; align-items: center; gap: 1rem; background: linear-gradient(135deg, rgba(99,102,241,0.12), rgba(6,182,212,0.08)); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem; margin-bottom: 1.25rem;">
                    <div class="user-avatar-circle" style="width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg, var(--primary), var(--secondary)); font-size: 1.35rem; font-weight: 800; color: #ffffff; display: flex; align-items: center; justify-content: center;">
                        {{ strtoupper(substr($pName, 0, 1)) }}
                    </div>
                    <div style="flex: 1;">
                        <h3 style="margin: 0; font-size: 1.3rem; font-weight: 800; color: var(--color-text-primary);">
                            {{ $pName }}
                        </h3>
                        <div style="font-size: 0.84rem; color: var(--color-text-secondary); margin-top: 2px;">
                            {{ $selectedPersonnel->email }} &bull; Account Reference #{{ $selectedPersonnel->id }}
                        </div>
                    </div>
                    <div>
                        <span class="badge" style="background: {{ $selectedPersonnel->is_active ? 'rgba(16,185,129,0.15)' : 'rgba(239,68,68,0.15)' }}; color: {{ $selectedPersonnel->is_active ? 'var(--success)' : 'var(--danger)' }}; font-size: 0.78rem; font-weight: 700; border-radius: 999px; padding: 0.35rem 0.75rem;">
                            {{ $selectedPersonnel->is_active ? '● Active Login' : '✕ Suspended' }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1rem;">
                        <div style="font-size: 0.72rem; text-transform: uppercase; font-weight: 800; color: var(--color-text-muted); letter-spacing: 0.04em;">Affiliation & Office</div>
                        <div style="font-weight: 800; color: var(--color-text-primary); margin-top: 0.25rem; font-size: 0.95rem;">{{ $pAffil }}</div>
                        <div style="font-size: 0.78rem; color: var(--color-text-secondary); margin-top: 0.2rem;">{{ $pCountry }}</div>
                    </div>

                    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1rem;">
                        <div style="font-size: 0.72rem; text-transform: uppercase; font-weight: 800; color: var(--color-text-muted); letter-spacing: 0.04em;">Assigned System Role</div>
                        <div style="font-weight: 800; color: var(--primary); margin-top: 0.25rem; text-transform: capitalize; font-size: 0.95rem;">
                            {{ str_replace('-', ' ', $selectedPersonnel->roles->pluck('name')->join(', ') ?: 'No Role') }}
                        </div>
                        <div style="font-size: 0.78rem; color: var(--color-text-secondary); margin-top: 0.2rem;">
                            Direct extra permissions: <strong>{{ $selectedPersonnel->permissions->count() }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Contact Particulars -->
                <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1rem; margin-bottom: 1.25rem;">
                    <div style="font-size: 0.72rem; text-transform: uppercase; font-weight: 800; color: var(--secondary); letter-spacing: 0.05em; margin-bottom: 0.6rem;">Contact & Verification Reference</div>
                    <div class="grid grid-cols-2 gap-3" style="font-size: 0.88rem;">
                        <div>
                            <span style="color: var(--color-text-muted); font-size: 0.76rem; text-transform: uppercase;">Official Phone:</span> 
                            <div style="font-weight: 700; color: var(--color-text-primary); margin-top: 2px;">{{ $selectedPersonnel->basicInfo?->phone_number ?? ($selectedPersonnel->reviewerProfile?->phone ?? 'N/A') }}</div>
                        </div>
                        <div>
                            <span style="color: var(--color-text-muted); font-size: 0.76rem; text-transform: uppercase;">CNIC / Passport:</span> 
                            <div style="font-weight: 700; color: var(--color-text-primary); font-family: 'JetBrains Mono', monospace; margin-top: 2px;">{{ $selectedPersonnel->basicInfo?->cnic_number ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Committee Panels Assignment Ledger -->
                @if($selectedPersonnel->committees && $selectedPersonnel->committees->isNotEmpty())
                    <div style="background: rgba(6,182,212,0.05); border: 1px solid rgba(6,182,212,0.25); border-radius: var(--radius-sm); padding: 1rem; margin-bottom: 1.25rem;">
                        <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 800; color: var(--secondary); margin-bottom: 0.65rem; display: flex; align-items: center; gap: 0.4rem;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                            <span>Active Committee Panel Assignments ({{ $selectedPersonnel->committees->count() }})</span>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 0.45rem;">
                            @foreach($selectedPersonnel->committees as $comm)
                                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; background: var(--bg-card); padding: 0.6rem 0.85rem; border-radius: 6px; border: 1px solid var(--border-color);">
                                    <div>
                                        <strong style="color: var(--color-text-primary);">{{ $comm->campaign?->title ?? 'Campaign #'.$comm->campaign_id }}</strong>
                                        <span style="font-size: 0.75rem; color: var(--color-text-muted); margin-left: 0.4rem; font-family: 'JetBrains Mono', monospace;">({{ $comm->campaign?->job_code }})</span>
                                    </div>
                                    <span class="badge" style="background: {{ $comm->is_appellate_panel ? 'rgba(245,158,11,0.15)' : 'rgba(16,185,129,0.15)' }}; color: {{ $comm->is_appellate_panel ? 'var(--warning)' : 'var(--success)' }}; font-size: 0.72rem; font-weight: 700;">
                                        {{ $comm->is_appellate_panel ? 'Appellate Panel' : 'Scrutiny Panel' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Reviewer Dossier if applicable -->
                @if($selectedPersonnel->reviewerProfile)
                    <div style="background: rgba(139,92,246,0.06); border: 1px solid rgba(139,92,246,0.25); border-radius: var(--radius-sm); padding: 1rem; margin-bottom: 1.25rem;">
                        <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 800; color: #8b5cf6; margin-bottom: 0.5rem;">
                            Empanelled Academic Reviewer Dossier
                        </div>
                        <div style="font-size: 0.85rem; color: var(--color-text-secondary); line-height: 1.6;">
                            <div>Academic Rank: <strong style="color: var(--color-text-primary);">{{ $selectedPersonnel->reviewerProfile->academic_rank }}</strong></div>
                            <div>Field / Subject: <strong style="color: var(--color-text-primary);">{{ $selectedPersonnel->reviewerProfile->subject }}</strong></div>
                            <div>Specialization: <strong style="color: var(--color-text-primary);">{{ $selectedPersonnel->reviewerProfile->specialization ?: 'Generalist' }}</strong></div>
                        </div>
                    </div>
                @endif

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; border-top: 1px solid var(--border-color); padding-top: 1rem;">
                    <div>
                        @if(auth()->user()->hasRole('super-admin') && auth()->id() !== $selectedPersonnel->id && !$selectedPersonnel->hasRole('super-admin'))
                            <a href="{{ route('impersonate.start', $selectedPersonnel->id) }}" class="btn btn-warning" style="display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 700; text-decoration: none;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                <span>🎭 Impersonate User</span>
                            </a>
                        @endif
                    </div>
                    <button type="button" class="btn btn-secondary" wire:click="closePersonnelModal" style="padding: 0.45rem 1.25rem;">
                        Close Dossier
                    </button>
                </div>
            </div>
        @endif
    </x-modal>

    <!-- Direct User Permissions Modal -->
    <x-modal :show="$isDirectPermissionsModalOpen" title="Manage Direct / Extra User Permissions" closeAction="closeDirectPermissionsModal" style="max-width: 860px; width: 95%;">
        @if($targetUserIdForPermissions)
            @php
                $targetUser = \App\Models\User::find($targetUserIdForPermissions);
            @endphp
            <div style="max-height: 75vh; overflow-y: auto; padding-right: 0.25rem;">
                <div style="background: linear-gradient(135deg, rgba(6,182,212,0.1), rgba(99,102,241,0.08)); border: 1px solid rgba(6,182,212,0.25); border-radius: var(--radius-md); padding: 1.15rem; margin-bottom: 1.25rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
                        <div>
                            <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 800; color: var(--secondary); letter-spacing: 0.04em;">Target User Account</div>
                            <strong style="font-size: 1.05rem; color: var(--color-text-primary);">{{ $targetUser?->email }}</strong>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div>
                                <span style="font-size: 0.75rem; color: var(--color-text-muted);">Primary Role:</span>
                                <span class="badge" style="background: rgba(99,102,241,0.15); color: var(--primary); font-weight: 700;">{{ $targetUser?->roles->first()?->name }}</span>
                            </div>
                            <span class="badge" style="background: rgba(6,182,212,0.18); color: var(--secondary); font-weight: 800; padding: 0.35rem 0.75rem;">
                                {{ count($targetUserDirectPermissions) }} Extra Assigned
                            </span>
                        </div>
                    </div>
                </div>

                <div style="font-size: 0.82rem; color: var(--color-text-secondary); margin-bottom: 1.25rem; line-height: 1.5;">
                    💡 <strong>Granular Capability Control:</strong> Permissions can be inherited automatically from the user's primary security role (marked <span class="badge" style="background: rgba(99,102,241,0.2); color: var(--primary); font-size: 0.68rem;">Role</span>), or granted directly as individual authority overrides (marked <span class="badge" style="background: rgba(6,182,212,0.2); color: var(--secondary); font-size: 0.68rem;">★ Extra</span>).
                </div>

                <!-- Grouped Permissions Matrix -->
                <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.5rem;">
                    @foreach($allGroupedPermissions as $groupName => $perms)
                        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1rem 1.15rem;">
                            <div style="font-size: 0.85rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.04em; display: flex; align-items: center; justify-content: space-between;">
                                <span>{{ $groupName }}</span>
                                <span style="font-size: 0.72rem; color: var(--color-text-muted); font-weight: 600;">{{ count($perms) }} action capabilities</span>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach($perms as $permKey => $permMeta)
                                    @php
                                        $isInherited = in_array($permKey, $targetUserRolePermissions);
                                        $isDirect = in_array($permKey, $targetUserDirectPermissions);
                                    @endphp
                                    <div style="display: flex; align-items: flex-start; gap: 0.65rem; padding: 0.6rem 0.75rem; border-radius: 8px; background: {{ $isDirect ? 'rgba(6,182,212,0.1)' : ($isInherited ? 'rgba(99,102,241,0.06)' : 'rgba(255,255,255,0.015)') }}; border: 1px solid {{ $isDirect ? 'var(--secondary)' : ($isInherited ? 'rgba(99,102,241,0.25)' : 'var(--border-color)') }}; transition: all 0.2s ease;">
                                        <input type="checkbox" 
                                               style="margin-top: 0.25rem; width: 1.15rem; height: 1.15rem; accent-color: var(--secondary); cursor: pointer;"
                                               wire:click="toggleDirectPermission('{{ $permKey }}')"
                                               {{ $isDirect ? 'checked' : '' }}>
                                        <div style="flex: 1; cursor: pointer;" wire:click="toggleDirectPermission('{{ $permKey }}')">
                                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.4rem;">
                                                <strong style="font-size: 0.84rem; color: var(--color-text-primary);">{{ $permMeta['label'] }}</strong>
                                                <div style="display: flex; gap: 0.25rem;">
                                                    @if($isInherited)
                                                        <span class="badge" style="background: rgba(99,102,241,0.2); color: var(--primary); font-size: 0.65rem; padding: 0.1rem 0.35rem;">Role</span>
                                                    @endif
                                                    @if($isDirect)
                                                        <span class="badge" style="background: rgba(6,182,212,0.2); color: var(--secondary); font-size: 0.65rem; padding: 0.1rem 0.35rem;">★ Extra</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <code style="font-size: 0.72rem; color: var(--color-text-muted); display: block; margin-top: 0.15rem; font-family: 'JetBrains Mono', monospace;">{{ $permKey }}</code>
                                            <div style="font-size: 0.74rem; color: var(--color-text-secondary); margin-top: 0.25rem; line-height: 1.35;">
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
                    <button type="button" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.5rem; font-weight: 700;" wire:click="saveDirectPermissions">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        <span>Save Direct Extra Permissions</span>
                    </button>
                </div>
            </div>
        @endif
    </x-modal>
</div>
