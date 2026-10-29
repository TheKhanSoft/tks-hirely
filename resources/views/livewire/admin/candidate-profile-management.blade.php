<div>
    <!-- Executive Control Header -->
    <div class="page-hero-header">
        <div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div class="brand-icon-box">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" style="color: #ffffff;">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                <div>
                    <h1>Candidate Profiles & Dossier Vault</h1>
                    <p>Applicant demographic records, verified qualification credentials, and dossier security lock controls.</p>
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            @if(!$isFormOpen && !$isDetailsOpen)
                @can('create-basic-info')
                    <button type="button" class="btn btn-primary" wire:click="openCreateBasicInfoForm" style="display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 18px -2px rgba(99,102,241,0.5); padding: 0.65rem 1.35rem; font-weight: 700; border-radius: 10px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>+ Create Candidate Profile</span>
                    </button>
                @endcan
            @endif
        </div>
    </div>

    <!-- Live Executive KPI Summary Cards -->
    @if(!$isFormOpen && !$isDetailsOpen)
        <div class="stat-kpi-grid">
            <div class="metric-card" style="--card-accent: var(--primary); --metric-icon-bg: var(--primary-light); --metric-icon-color: var(--primary);">
                <div class="metric-header">
                    <span class="metric-label">Total Candidate Pool</span>
                    <div class="metric-icon-wrap">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                </div>
                <div class="metric-value" style="color: var(--primary);">{{ $stats['total'] }}</div>
                <div class="metric-subtext">Registered applicant profiles</div>
            </div>

            <div class="metric-card" style="--card-accent: var(--secondary); --metric-icon-bg: var(--secondary-light); --metric-icon-color: var(--secondary);">
                <div class="metric-header">
                    <span class="metric-label">Active Applicants</span>
                    <div class="metric-icon-wrap">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
                        </svg>
                    </div>
                </div>
                <div class="metric-value" style="color: var(--secondary);">{{ $stats['with_applications'] }}</div>
                <div class="metric-subtext">With submitted candidacies</div>
            </div>

            <div class="metric-card" style="--card-accent: #8b5cf6; --metric-icon-bg: rgba(139,92,246,0.12); --metric-icon-color: #8b5cf6;">
                <div class="metric-header">
                    <span class="metric-label">Academic Track</span>
                    <div class="metric-icon-wrap">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
                        </svg>
                    </div>
                </div>
                <div class="metric-value" style="color: #8b5cf6;">{{ $stats['academic'] }}</div>
                <div class="metric-subtext">Faculty & research candidates</div>
            </div>

            <div class="metric-card" style="--card-accent: var(--danger); --metric-icon-bg: var(--danger-light); --metric-icon-color: var(--danger);">
                <div class="metric-header">
                    <span class="metric-label">Locked Dossiers</span>
                    <div class="metric-icon-wrap">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </div>
                </div>
                <div class="metric-value" style="color: var(--danger);">{{ $stats['locked'] }}</div>
                <div class="metric-subtext">Immutable submitted dossiers</div>
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
                        {{ $isEditMode ? 'Modify Candidate Demographics & Dossier Lock' : 'Create Candidate Profile Record' }}
                    </h3>
                    <p class="card-subtitle" style="margin: 0.2rem 0 0;">
                        Assign official career track classification, personal particulars, and lock edit permissions.
                    </p>
                </div>
                <button type="button" class="btn btn-secondary btn-sm" wire:click="resetForm">Cancel</button>
            </div>

            <form wire:submit.prevent="saveBasicInfo">
                @if(!$isEditMode)
                    <div class="mb-4">
                        <x-select label="Select Applicant Account *" :error="$errors->first('bi_user_id')">
                            <select class="form-control form-select" wire:model="bi_user_id">
                                <option value="">-- Choose Account (Users without profile) --</option>
                                @foreach($availableUsers as $pu)
                                    <option value="{{ $pu->id }}">{{ $pu->email }}</option>
                                @endforeach
                            </select>
                        </x-select>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-5">
                    <x-input label="First Name *" :error="$errors->first('bi_first_name')">
                        <input type="text" class="form-control" wire:model="bi_first_name" placeholder="Candidate given name">
                    </x-input>
                    <x-input label="Last Name *" :error="$errors->first('bi_last_name')">
                        <input type="text" class="form-control" wire:model="bi_last_name" placeholder="Candidate surname">
                    </x-input>
                </div>

                <div class="grid grid-cols-3 gap-5 mt-4 align-center">
                    <x-input label="CNIC / National Identity Reference *" :error="$errors->first('bi_cnic_number')">
                        <input type="text" class="form-control" wire:model="bi_cnic_number" placeholder="42101-1234567-1">
                    </x-input>
                    <x-input label="Date of Birth *" :error="$errors->first('bi_dob')">
                        <input type="date" class="form-control" wire:model="bi_dob">
                    </x-input>
                    <x-select label="Career Track Classification *" :error="$errors->first('bi_track_type')">
                        <select class="form-control form-select" wire:model="bi_track_type">
                            <option value="ACADEMIC">Academic / Faculty Track</option>
                            <option value="ADMINISTRATIVE">Administrative / Executive Track</option>
                            <option value="SUPPORT">Technical & Support Staff Track</option>
                        </select>
                    </x-select>
                </div>

                <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1rem 1.25rem; margin-top: 1.25rem;">
                    <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; margin: 0;">
                        <input type="checkbox" wire:model="bi_is_locked" style="width: 1.25rem; height: 1.25rem; accent-color: var(--primary);">
                        <div>
                            <span style="font-weight: 700; color: var(--color-text-primary);">Lock Profile Dossier (Immutable Submission)</span>
                            <div style="font-size: 0.8rem; color: var(--color-text-muted);">
                                When locked, the candidate cannot modify degrees, experiences, or personal details without administrator override.
                            </div>
                        </div>
                    </label>
                </div>

                <div class="flex gap-4 mt-6" style="border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                    <button type="submit" class="btn btn-primary" style="padding: 0.55rem 1.35rem; font-weight: 700;">Save Profile</button>
                    <button type="button" class="btn btn-secondary" wire:click="resetForm">Cancel</button>
                </div>
            </form>
        </div>
    @else
        <!-- Reusable Dynamic Entity Listing Component: Responsive Card Grid & Table -->
        <x-entity-card-grid 
            :items="$profiles" 
            entityName="Candidate Profiles" 
            searchPlaceholder="Search candidates by name, email, CNIC, or degree..."
            :perPage="$perPage" 
            :viewMode="$viewMode" 
            exportAction="exportCsv" 
            importAction="openImportModal">
            
            <!-- Filters Slot -->
            <x-slot:filters>
                <div style="flex: 1; min-width: 180px;">
                    <select class="form-control form-select" wire:model.live="trackFilter">
                        <option value="">All Career Tracks</option>
                        <option value="ACADEMIC">Academic / Faculty</option>
                        <option value="ADMINISTRATIVE">Administrative / Executive</option>
                        <option value="SUPPORT">Support Staff</option>
                    </select>
                </div>

                <div style="flex: 1; min-width: 170px;">
                    <select class="form-control form-select" wire:model.live="lockFilter">
                        <option value="">All Lock States</option>
                        <option value="1">🔒 Locked Dossiers</option>
                        <option value="0">🔓 Editable (Open)</option>
                    </select>
                </div>

                @if(!empty($search) || !empty($trackFilter) || $lockFilter !== '')
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="$set('search', ''); $set('trackFilter', ''); $set('lockFilter', '');" style="display: inline-flex; align-items: center; gap: 0.35rem;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        <span>Reset Filters</span>
                    </button>
                @endif
            </x-slot:filters>

            <!-- Cards Slot (3 to 5 Cards Per Row) -->
            <x-slot:cards>
                @foreach($profiles as $basicInfo)
                    @php
                        $apps = $basicInfo->user->applications ?? collect();
                        $trackTheme = match($basicInfo->track_type) {
                            'ACADEMIC' => 'avatar-theme-purple',
                            'ADMINISTRATIVE' => 'avatar-theme-cyan',
                            default => 'avatar-theme-emerald',
                        };
                    @endphp

                    <x-entity-card 
                        :id="$basicInfo->id" 
                        :title="$basicInfo->first_name . ' ' . $basicInfo->last_name" 
                        :subtitle="$basicInfo->user?->email"
                        :avatar="strtoupper(substr($basicInfo->first_name ?: 'C', 0, 1))"
                        :avatarClass="$trackTheme"
                        :is-active="!$basicInfo->is_locked">
                        
                        <!-- Badge Slot: Locked / Editable Status (Above action strip) -->
                        <x-slot:badge>
                            @can('lock-candidate-profile')
                                <button type="button" 
                                        wire:click="toggleLock({{ $basicInfo->id }})"
                                        class="entity-card-status-toggle-btn" 
                                        style="font-size: 0.74rem; font-weight: 800; cursor: pointer; color: {{ $basicInfo->is_locked ? '#ef4444' : '#10b981' }};"
                                        title="Click to toggle lock state">
                                    <span style="display: inline-flex; align-items: center; gap: 0.4rem;">
                                        <span class="pulse-dot {{ $basicInfo->is_locked ? 'offline' : 'online' }}"></span>
                                        <span>{{ $basicInfo->is_locked ? '🔒 LOCKED DOSSIER' : '🔓 OPEN / EDITABLE' }}</span>
                                    </span>
                                    <span style="font-size: 0.68rem; opacity: 0.75; font-weight: 700; text-transform: uppercase;">Click to toggle ↻</span>
                                </button>
                            @else
                                <div style="display: inline-flex; align-items: center; gap: 0.4rem; color: {{ $basicInfo->is_locked ? '#ef4444' : '#10b981' }}; font-weight: 800;">
                                    <span class="pulse-dot {{ $basicInfo->is_locked ? 'offline' : 'online' }}"></span>
                                    <span>{{ $basicInfo->is_locked ? '🔒 LOCKED' : '🔓 EDITABLE' }}</span>
                                </div>
                            @endcan
                        </x-slot:badge>

                        <!-- Tag Slot: Career Track -->
                        <x-slot:tag>
                            @if($basicInfo->track_type === 'ACADEMIC')
                                <span class="badge badge-purple" style="font-weight: 700; font-size: 0.72rem;">
                                    🎓 Academic Track
                                </span>
                            @elseif($basicInfo->track_type === 'ADMINISTRATIVE')
                                <span class="badge badge-cyan" style="font-weight: 700; font-size: 0.72rem;">
                                    🏛️ Administrative Track
                                </span>
                            @else
                                <span class="badge badge-emerald" style="font-weight: 700; font-size: 0.72rem;">
                                    ⚙️ Support Staff
                                </span>
                            @endif
                        </x-slot:tag>

                        <!-- Card Body Metrics -->
                        <div class="entity-card-metric-row">
                            <span class="entity-card-metric-label">CNIC:</span>
                            <span class="mono-pill">
                                {{ $basicInfo->cnic_number }}
                            </span>
                        </div>

                        <div class="entity-card-metric-row">
                            <span class="entity-card-metric-label">Applications:</span>
                            <span class="entity-card-metric-value">
                                @if($apps->isNotEmpty())
                                    <span style="color: var(--primary); font-weight: 800;">{{ $apps->count() }} Position{{ $apps->count() === 1 ? '' : 's' }}</span>
                                @else
                                    <span style="color: var(--color-text-muted); font-style: italic;">None yet</span>
                                @endif
                            </span>
                        </div>

                        <div class="entity-card-metric-row">
                            <span class="entity-card-metric-label">Qualifications:</span>
                            <span class="entity-card-metric-value" style="color: var(--secondary);">
                                {{ $basicInfo->degrees?->count() ?? 0 }} Degree{{ ($basicInfo->degrees?->count() ?? 0) === 1 ? '' : 's' }}
                            </span>
                        </div>

                        <!-- Quick Actions (Hover Reveal Icons) -->
                        <x-slot:quickActions>
                            @if(auth()->user()->hasRole('super-admin') && $basicInfo->user && auth()->id() !== $basicInfo->user->id && !$basicInfo->user->hasRole('super-admin'))
                                <a href="{{ route('impersonate.start', $basicInfo->user->id) }}" 
                                   class="action-btn action-btn-impersonate" 
                                   data-tooltip="Impersonate {{ $basicInfo->first_name ?: $basicInfo->user->email }}"
                                   aria-label="Impersonate Candidate">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                </a>
                            @endif

                            @if(auth()->user()->can('view-candidate-dossier') || auth()->user()->can('view-basic-info'))
                                <button type="button" 
                                        class="action-btn action-btn-view" 
                                        data-tooltip="View Dossier" 
                                        wire:click="viewBasicInfoDetails({{ $basicInfo->id }})">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </button>
                            @endif

                            @can('edit-basic-info')
                                <button type="button" 
                                        class="action-btn action-btn-edit" 
                                        data-tooltip="Edit Profile" 
                                        wire:click="openEditBasicInfoForm({{ $basicInfo->id }})">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </button>
                            @endcan
                        </x-slot:quickActions>

                        <!-- Kebab Dropdown Options Menu [ICON + TEXT] -->
                        <x-slot:menuActions>
                            @if(auth()->user()->can('view-candidate-dossier') || auth()->user()->can('view-basic-info'))
                                <x-entity-card-menu-item 
                                    label="View Candidate Dossier" 
                                    click="viewBasicInfoDetails({{ $basicInfo->id }})">
                                    <x-slot:icon>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </x-slot:icon>
                                </x-entity-card-menu-item>
                            @endif

                            @can('edit-basic-info')
                                <x-entity-card-menu-item 
                                    label="Edit Candidate Profile" 
                                    click="openEditBasicInfoForm({{ $basicInfo->id }})">
                                    <x-slot:icon>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </x-slot:icon>
                                </x-entity-card-menu-item>
                            @endcan

                            @can('lock-candidate-profile')
                                <x-entity-card-menu-item 
                                    label="{{ $basicInfo->is_locked ? 'Unlock Dossier' : 'Lock Dossier' }}" 
                                    click="toggleLock({{ $basicInfo->id }})">
                                    <x-slot:icon>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                    </x-slot:icon>
                                </x-entity-card-menu-item>
                            @endcan

                            @if(auth()->user()->hasRole('super-admin') && $basicInfo->user && auth()->id() !== $basicInfo->user->id && !$basicInfo->user->hasRole('super-admin'))
                                <div class="entity-card-menu-divider"></div>
                                <x-entity-card-menu-item 
                                    label="Impersonate Candidate" 
                                    href="{{ route('impersonate.start', $basicInfo->user->id) }}" 
                                    :impersonate="true">
                                    <x-slot:icon>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                    </x-slot:icon>
                                </x-entity-card-menu-item>
                            @endif

                            @can('delete-basic-info')
                                <div class="entity-card-menu-divider"></div>
                                <x-entity-card-menu-item 
                                    label="Delete Candidate Profile" 
                                    click="deleteBasicInfo({{ $basicInfo->id }})" 
                                    :danger="true"
                                    onclick="confirm('Are you sure you want to permanently delete this candidate profile?') || event.stopImmediatePropagation()">
                                    <x-slot:icon>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                    </x-slot:icon>
                                </x-entity-card-menu-item>
                            @endcan
                        </x-slot:menuActions>
                    </x-entity-card>
                @endforeach
            </x-slot:cards>

            <!-- Table Slot (For View Switcher) -->
            <x-slot:table>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Candidate Particulars</th>
                            <th>Track Classification</th>
                            <th>Applied Vacancies</th>
                            <th>CNIC Reference</th>
                            <th>Dossier Status</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($profiles as $basicInfo)
                            <tr wire:key="profile-row-{{ $basicInfo->id }}">
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.85rem;">
                                        <div class="user-avatar-circle" style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, rgba(99,102,241,0.25), rgba(6,182,212,0.25)); color: var(--primary); font-weight: 800; font-size: 1rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid rgba(99,102,241,0.25);">
                                            {{ strtoupper(substr($basicInfo->first_name ?: 'C', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 800; color: var(--color-text-primary); font-size: 0.96rem;">
                                                {{ $basicInfo->first_name }} {{ $basicInfo->last_name }}
                                            </div>
                                            <div style="font-size: 0.78rem; color: var(--color-text-secondary); display: flex; align-items: center; gap: 0.4rem; margin-top: 2px;">
                                                <span>{{ $basicInfo->user?->email }}</span>
                                                @if($basicInfo->degrees && $basicInfo->degrees->isNotEmpty())
                                                    <span style="opacity: 0.4;">•</span>
                                                    <span style="color: var(--secondary); font-weight: 600;">{{ $basicInfo->degrees->count() }} degree(s)</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($basicInfo->track_type === 'ACADEMIC')
                                        <span class="badge" style="background: rgba(139,92,246,0.12); color: #8b5cf6; border: 1px solid rgba(139,92,246,0.3); font-weight: 700; font-size: 0.74rem;">
                                            🎓 ACADEMIC
                                        </span>
                                    @elseif($basicInfo->track_type === 'ADMINISTRATIVE')
                                        <span class="badge" style="background: rgba(6,182,212,0.12); color: var(--secondary); border: 1px solid rgba(6,182,212,0.3); font-weight: 700; font-size: 0.74rem;">
                                            🏛️ ADMINISTRATIVE
                                        </span>
                                    @else
                                        <span class="badge" style="background: rgba(16,185,129,0.12); color: var(--success); border: 1px solid rgba(16,185,129,0.3); font-weight: 700; font-size: 0.74rem;">
                                            ⚙️ SUPPORT
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $apps = $basicInfo->user->applications ?? collect();
                                    @endphp
                                    @if($apps->isNotEmpty())
                                        @foreach($apps->take(2) as $app)
                                            <div style="margin-bottom: 0.25rem;">
                                                <span class="badge" style="background: rgba(255,255,255,0.04); color: var(--color-text-primary); font-size: 0.74rem; border: 1px solid var(--border-color); font-weight: 600;">
                                                    {{ $app->campaign?->title ?? 'Campaign #'.$app->campaign_id }}
                                                </span>
                                            </div>
                                        @endforeach
                                        @if($apps->count() > 2)
                                            <small style="color: var(--color-text-muted); font-size: 0.72rem;">+{{ $apps->count() - 2 }} more</small>
                                        @endif
                                    @else
                                        <span style="font-size: 0.78rem; color: var(--color-text-muted); font-style: italic;">No candidacies yet</span>
                                    @endif
                                </td>
                                <td>
                                    <code style="font-family: 'JetBrains Mono', monospace; font-size: 0.82rem; color: var(--color-text-primary); background: rgba(255,255,255,0.04); padding: 0.15rem 0.45rem; border-radius: 4px; border: 1px solid var(--border-color);">
                                        {{ $basicInfo->cnic_number }}
                                    </code>
                                </td>
                                <td>
                                    @can('lock-candidate-profile')
                                        <button type="button" 
                                                wire:click="toggleLock({{ $basicInfo->id }})"
                                                class="badge {{ $basicInfo->is_locked ? 'badge-danger' : 'badge-success' }}" 
                                                style="cursor: pointer; border: 1px solid currentColor; background: {{ $basicInfo->is_locked ? 'rgba(239,68,68,0.12)' : 'rgba(16,185,129,0.12)' }};"
                                                title="Click to toggle profile editing lock">
                                            <span class="badge-dot" style="background: currentColor;"></span> {{ $basicInfo->is_locked ? 'Locked' : 'Editable' }}
                                        </button>
                                    @else
                                        <span class="badge {{ $basicInfo->is_locked ? 'badge-danger' : 'badge-success' }}">
                                            <span class="badge-dot" style="background: currentColor;"></span> {{ $basicInfo->is_locked ? 'Locked' : 'Editable' }}
                                        </span>
                                    @endcan
                                </td>
                                <td style="text-align: right;">
                                    <div class="action-btn-group justify-end" x-data="{ openMenu: false }">
                                        @if(auth()->user()->can('view-candidate-dossier') || auth()->user()->can('view-basic-info'))
                                            <button type="button" class="action-btn action-btn-view" data-tooltip="View Candidate Dossier" wire:click="viewBasicInfoDetails({{ $basicInfo->id }})">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                            </button>
                                        @endif
                                        @can('edit-basic-info')
                                            <button type="button" class="action-btn action-btn-edit" data-tooltip="Edit Profile Particulars" wire:click="openEditBasicInfoForm({{ $basicInfo->id }})">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
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
                                                @if(auth()->user()->hasRole('super-admin') && $basicInfo->user && auth()->id() !== $basicInfo->user->id && !$basicInfo->user->hasRole('super-admin'))
                                                    <a href="{{ route('impersonate.start', $basicInfo->user->id) }}" class="table-action-menu-item impersonate">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                                        <span>Impersonate Candidate</span>
                                                    </a>
                                                @endif
                                                @can('delete-basic-info')
                                                    <div class="table-action-menu-divider"></div>
                                                    <button type="button" class="table-action-menu-item danger" wire:click="deleteBasicInfo({{ $basicInfo->id }})" onclick="confirm('Are you sure you want to permanently delete this candidate profile?') || event.stopImmediatePropagation()">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                                        <span>Delete Candidate Profile</span>
                                                    </button>
                                                @endcan
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

        <!-- Universal Import Modal for Candidates -->
        <x-entity-import-modal 
            :isOpen="$isImportModalOpen" 
            entityName="Candidate Profiles" 
            :sampleColumns="['first_name', 'last_name', 'email', 'cnic_number', 'track_type', 'is_locked']" />
    @endif

    <!-- Profile Details Modal (Executive Candidate Dossier Vault) -->
    <x-modal :show="$isDetailsOpen" title="Complete Candidate Wizard Profile Details" closeAction="$set('isDetailsOpen', false)" style="max-width: 950px; width: 95%;">
        @if($selectedRecordDetails)
            <div style="max-height: 75vh; overflow-y: auto; padding-right: 0.35rem;">
                <!-- Candidate Bio Header Card -->
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; background: linear-gradient(135deg, rgba(99,102,241,0.1), rgba(6,182,212,0.08)); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem; margin-bottom: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div class="user-avatar-circle" style="width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg, var(--primary), var(--secondary)); font-size: 1.35rem; font-weight: 800; color: #ffffff; display: flex; align-items: center; justify-content: center;">
                            {{ strtoupper(substr($selectedRecordDetails->first_name ?: $selectedRecordDetails->user->email, 0, 1)) }}
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 1.35rem; font-weight: 800; color: var(--color-text-primary);">
                                {{ $selectedRecordDetails->first_name }} {{ $selectedRecordDetails->last_name }}
                            </h3>
                            <div style="font-size: 0.84rem; color: var(--color-text-secondary); margin-top: 2px;">
                                {{ $selectedRecordDetails->user->email }} &bull; Profile #{{ $selectedRecordDetails->id }}
                            </div>
                        </div>
                    </div>
                    <div>
                        <span class="badge" style="padding: 0.35rem 0.85rem; font-size: 0.8rem; border-radius: 999px; font-weight: 700; {{ $selectedRecordDetails->is_locked ? 'background: rgba(239,68,68,0.15); color: var(--danger); border: 1px solid rgba(239,68,68,0.3);' : 'background: rgba(16,185,129,0.15); color: var(--success); border: 1px solid rgba(16,185,129,0.3);' }}">
                            {{ $selectedRecordDetails->is_locked ? '🔒 Dossier Locked' : '🔓 Editable Profile' }}
                        </span>
                    </div>
                </div>

                <!-- Personal Demographics Grid -->
                <div class="card mb-4" style="padding: 1.25rem; background: var(--bg-card);">
                    <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 800; color: var(--secondary); letter-spacing: 0.05em; margin-bottom: 0.85rem;">
                        Personal & Demographic Particulars
                    </div>
                    <div class="grid grid-cols-4 gap-4" style="font-size: 0.88rem;">
                        <div>
                            <div style="color: var(--color-text-muted); font-size: 0.75rem; text-transform: uppercase;">CNIC / Passport</div>
                            <strong style="color: var(--color-text-primary); font-family: 'JetBrains Mono', monospace;">{{ $selectedRecordDetails->cnic_number }}</strong>
                        </div>
                        <div>
                            <div style="color: var(--color-text-muted); font-size: 0.75rem; text-transform: uppercase;">Date of Birth</div>
                            <strong style="color: var(--color-text-primary);">{{ $selectedRecordDetails->date_of_birth?->format('d M, Y') ?: 'N/A' }}</strong>
                        </div>
                        <div>
                            <div style="color: var(--color-text-muted); font-size: 0.75rem; text-transform: uppercase;">Gender</div>
                            <strong style="color: var(--color-text-primary);">{{ $selectedRecordDetails->gender ?: 'N/A' }}</strong>
                        </div>
                        <div>
                            <div style="color: var(--color-text-muted); font-size: 0.75rem; text-transform: uppercase;">Father / Guardian</div>
                            <strong style="color: var(--color-text-primary);">{{ $selectedRecordDetails->father_name ?: 'N/A' }}</strong>
                        </div>
                        <div>
                            <div style="color: var(--color-text-muted); font-size: 0.75rem; text-transform: uppercase;">Contact Phone</div>
                            <strong style="color: var(--color-text-primary);">{{ $selectedRecordDetails->phone_number ?: 'N/A' }}</strong>
                        </div>
                        <div>
                            <div style="color: var(--color-text-muted); font-size: 0.75rem; text-transform: uppercase;">Domicile District</div>
                            <strong style="color: var(--color-text-primary);">{{ $selectedRecordDetails->domicile ?: 'N/A' }}</strong>
                        </div>
                        <div>
                            <div style="color: var(--color-text-muted); font-size: 0.75rem; text-transform: uppercase;">Nationality</div>
                            <strong style="color: var(--color-text-primary);">{{ $selectedRecordDetails->nationality ?: 'Domestic / Local' }}</strong>
                        </div>
                        <div>
                            <div style="color: var(--color-text-muted); font-size: 0.75rem; text-transform: uppercase;">Career Track</div>
                            <strong style="color: var(--primary);">{{ $selectedRecordDetails->track_type }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Academic Qualifications History -->
                @if($selectedRecordDetails->degrees && $selectedRecordDetails->degrees->isNotEmpty())
                    <div class="card mb-4" style="padding: 1.25rem; background: var(--bg-card);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem;">
                            <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 800; color: var(--secondary); letter-spacing: 0.05em;">
                                Chronological Academic Qualifications ({{ $selectedRecordDetails->degrees->count() }})
                            </div>
                        </div>
                        <x-table :headers="['Degree Title', 'Major Discipline', 'Institution / Board', 'Year', 'GPA / Marks', 'Division', 'Proof File']">
                            @foreach($selectedRecordDetails->degrees as $deg)
                                <tr>
                                    <td><strong style="color: var(--color-text-primary);">{{ $deg['degree_title'] }}</strong></td>
                                    <td>{{ $deg['major'] }}</td>
                                    <td>{{ $deg['institution'] }} ({{ $deg['board'] }})</td>
                                    <td><code style="font-family: 'JetBrains Mono', monospace;">{{ $deg['year'] }}</code></td>
                                    <td><strong style="color: var(--primary);">{{ $deg['gpa'] }}</strong> / {{ $deg['scale'] }}</td>
                                    <td><span class="badge" style="background: rgba(16,185,129,0.12); color: var(--success); font-size: 0.72rem;">{{ $deg['division'] }}</span></td>
                                    <td>
                                        @if(isset($deg['proof_path']))
                                            <a href="/storage/{{ $deg['proof_path'] }}" target="_blank" class="btn btn-secondary btn-sm" style="padding: 0.2rem 0.55rem; font-size: 0.74rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                                <span>View Proof</span>
                                            </a>
                                        @else
                                            <span style="color: var(--color-text-muted); font-size: 0.75rem;">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </x-table>
                    </div>
                @endif

                <!-- Employment History -->
                @if($selectedRecordDetails->experiences && $selectedRecordDetails->experiences->isNotEmpty())
                    <div class="card mb-4" style="padding: 1.25rem; background: var(--bg-card);">
                        <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 800; color: var(--secondary); letter-spacing: 0.05em; margin-bottom: 0.85rem;">
                            Professional Employment History ({{ $selectedRecordDetails->experiences->count() }})
                        </div>
                        <x-table :headers="['Designation', 'Organization', 'Tenure Dates', 'Sector', 'Gross Salary', 'Description', 'Letter/NOC']">
                            @foreach($selectedRecordDetails->experiences as $exp)
                                <tr>
                                    <td><strong style="color: var(--color-text-primary);">{{ $exp->job_title }}</strong></td>
                                    <td>{{ $exp->organization }}</td>
                                    <td><code style="font-size: 0.78rem;">{{ $exp->start_date?->format('Y-m-d') }}</code> to <code style="font-size: 0.78rem;">{{ $exp->end_date?->format('Y-m-d') ?: 'Present' }}</code></td>
                                    <td><span class="badge" style="background: rgba(6,182,212,0.12); color: var(--secondary); font-size: 0.72rem;">{{ $exp->sector }}</span></td>
                                    <td><strong>PKR {{ number_format($exp->salary ?? 0, 2) }}</strong></td>
                                    <td style="max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $exp->description }}">{{ $exp->description ?: 'N/A' }}</td>
                                    <td>
                                        @if(isset($exp['proof_path']))
                                            <a href="/storage/{{ $exp['proof_path'] }}" target="_blank" class="btn btn-secondary btn-sm" style="padding: 0.2rem 0.55rem; font-size: 0.74rem; text-decoration: none;">Open NOC</a>
                                        @else
                                            <span style="color: var(--color-text-muted); font-size: 0.75rem;">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </x-table>
                    </div>
                @endif

                <!-- Research Publications -->
                @if($selectedRecordDetails->publications && $selectedRecordDetails->publications->isNotEmpty())
                    <div class="card mb-4" style="padding: 1.25rem; background: var(--bg-card);">
                        <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 800; color: var(--secondary); letter-spacing: 0.05em; margin-bottom: 0.85rem;">
                            Research Publications ({{ $selectedRecordDetails->publications->count() }})
                        </div>
                        <x-table :headers="['Paper Title', 'Journal Name', 'Category', 'Impact Factor', 'DOI / ISSN', 'Year']">
                            @foreach($selectedRecordDetails->publications as $pub)
                                <tr>
                                    <td><strong style="color: var(--color-text-primary);">{{ $pub->title }}</strong></td>
                                    <td>{{ $pub->journal_name }}</td>
                                    <td><span class="badge" style="background: rgba(99,102,241,0.15); color: var(--primary); font-weight: 800;">HEC {{ $pub->category ?? 'W' }}</span></td>
                                    <td><strong>{{ $pub->impact_factor ?: 'N/A' }}</strong></td>
                                    <td><code style="font-size: 0.78rem;">{{ $pub->doi_or_issn ?: 'N/A' }}</code></td>
                                    <td>{{ $pub->year }}</td>
                                </tr>
                            @endforeach
                        </x-table>
                    </div>
                @endif

                <div class="mt-5 flex justify-between items-center" style="border-top: 1px solid var(--border-color); padding-top: 1rem;">
                    <div>
                        @if(auth()->user()->hasRole('super-admin') && $selectedRecordDetails->user && auth()->id() !== $selectedRecordDetails->user->id && !$selectedRecordDetails->user->hasRole('super-admin'))
                            <a href="{{ route('impersonate.start', $selectedRecordDetails->user->id) }}" class="btn btn-warning" style="display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 700; text-decoration: none;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                <span>🎭 Impersonate Candidate</span>
                            </a>
                        @endif
                    </div>
                    <button type="button" class="btn btn-secondary" wire:click="$set('isDetailsOpen', false)" style="padding: 0.45rem 1.25rem;">Close Dossier Vault</button>
                </div>
            </div>
        @endif
    </x-modal>
</div>
