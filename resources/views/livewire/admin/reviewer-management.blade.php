<div>
    <!-- Executive Control Header -->
    <div class="page-hero-header">
        <div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div class="brand-icon-box" style="background: linear-gradient(135deg, rgba(6,182,212,0.25), rgba(14,165,233,0.4)); box-shadow: 0 4px 14px rgba(6,182,212,0.25);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color: #ffffff;">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div>
                    <h1>Academic Reviewers & Expert Referees Desk</h1>
                    <p>Empanel external academic experts, match referees dynamically by discipline, and orchestrate confidential peer evaluations.</p>
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            @if(auth()->user()->can('create-reviewer') || auth()->user()->can('manage-reviewers'))
                <button type="button" class="btn btn-primary" wire:click="openCreateModal" style="display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 18px -2px rgba(6,182,212,0.5); padding: 0.65rem 1.35rem; font-weight: 700; border-radius: 10px; background: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%); border-color: #0284c7;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>+ Empanel Academic Expert</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Live Executive KPI Summary Cards -->
    <div class="stat-kpi-grid">
        <div class="metric-card" style="--card-accent: var(--secondary); --metric-icon-bg: var(--secondary-light); --metric-icon-color: var(--secondary);">
            <div class="metric-header">
                <span class="metric-label">Empanelled Experts</span>
                <div class="metric-icon-wrap">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
            </div>
            <div class="metric-value" style="color: var(--secondary);">{{ $stats['totalReviewers'] }}</div>
            <div class="metric-subtext">{{ $stats['activeReviewers'] }} active for assignment</div>
        </div>

        <div class="metric-card" style="--card-accent: var(--primary); --metric-icon-bg: var(--primary-light); --metric-icon-color: var(--primary);">
            <div class="metric-header">
                <span class="metric-label">Academic Positions</span>
                <div class="metric-icon-wrap">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
                    </svg>
                </div>
            </div>
            <div class="metric-value" style="color: var(--primary);">{{ $stats['totalAcademicCampaigns'] }}</div>
            <div class="metric-subtext">{{ $stats['totalAcademicApplications'] }} total faculty candidacies</div>
        </div>

        <div class="metric-card" style="--card-accent: var(--warning); --metric-icon-bg: var(--warning-light); --metric-icon-color: var(--warning);">
            <div class="metric-header">
                <span class="metric-label">In Peer Review</span>
                <div class="metric-icon-wrap">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
            </div>
            <div class="metric-value" style="color: var(--warning);">{{ $stats['inReviewApplications'] }}</div>
            <div class="metric-subtext">Awaiting referee evaluations</div>
        </div>

        <div class="metric-card" style="--card-accent: var(--success); --metric-icon-bg: var(--success-light); --metric-icon-color: var(--success);">
            <div class="metric-header">
                <span class="metric-label">Completed Reports</span>
                <div class="metric-icon-wrap">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
            </div>
            <div class="metric-value" style="color: var(--success);">{{ $stats['completedReviewsCount'] }}</div>
            <div class="metric-subtext">Signed confidential dossiers</div>
        </div>
    </div>

    <!-- Flash Message Notification -->
    @if (session()->has('message'))
        <div class="alert alert-success mb-6" style="display: flex; align-items: center; gap: 0.75rem; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3); color: var(--success); padding: 0.95rem 1.35rem; border-radius: var(--radius-md);">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <div style="flex: 1; font-weight: 600;">
                <span>{{ session('message') }}</span>
            </div>
        </div>
    @endif

    <!-- Segmented Tab Navigation -->
    <div style="display: flex; gap: 0.5rem; background: var(--bg-tertiary); padding: 0.35rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color); width: fit-content; margin-bottom: 1.5rem;">
        <button type="button" 
                class="btn" 
                wire:click="setTab('reviewers')"
                style="padding: 0.55rem 1.35rem; font-size: 0.88rem; border-radius: var(--radius-md); font-weight: 700; display: inline-flex; align-items: center; gap: 0.55rem; transition: var(--transition-base); {{ $activeTab === 'reviewers' ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            <span>Empanelled Subject Experts</span>
            <span style="font-size: 0.75rem; padding: 0.15rem 0.55rem; border-radius: 999px; background: rgba(99,102,241,0.15); color: var(--primary); font-weight: 800;">{{ $reviewers->total() }}</span>
        </button>

        <button type="button" 
                class="btn" 
                wire:click="setTab('dispatch')"
                style="padding: 0.55rem 1.35rem; font-size: 0.88rem; border-radius: var(--radius-md); font-weight: 700; display: inline-flex; align-items: center; gap: 0.55rem; transition: var(--transition-base); {{ $activeTab === 'dispatch' ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="20" height="14" x="2" y="7" rx="2" ry="2"/>
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
            </svg>
            <span>Academic Peer Review Dispatch</span>
            <span style="font-size: 0.75rem; padding: 0.15rem 0.55rem; border-radius: 999px; background: rgba(6,182,212,0.15); color: var(--secondary); font-weight: 800;">{{ $academicApplications->count() }}</span>
        </button>
    </div>

    <!-- ============================================================== -->
    <!-- TAB 1: EMPANELLED REVIEWERS DIRECTORY                          -->
    <!-- ============================================================== -->
    @if($activeTab === 'reviewers')
        <x-entity-card-grid
            :items="$reviewers"
            :per-page="$perPage"
            :view-mode="$viewMode"
            :search="$search"
            search-placeholder="Search expert by name, university, email, or specialization..."
            entity-name="Empanelled Reviewers"
            :can-export="true"
            :can-import="true"
            grid-class="grid-3-cols"
        >
            <x-slot:filters>
                @if(!empty($availableSubjects))
                    <div style="flex: 1; min-width: 180px;">
                        <select class="form-control form-select" wire:model.live="subjectFilter" style="background: var(--bg-card); color: var(--color-text-primary); font-size: 0.82rem;">
                            <option value="">All Academic Disciplines</option>
                            @foreach($availableSubjects as $subj)
                                <option value="{{ $subj }}">{{ $subj }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if(!empty($availableRanks))
                    <div style="width: 175px;">
                        <select class="form-control form-select" wire:model.live="rankFilter" style="background: var(--bg-card); color: var(--color-text-primary); font-size: 0.82rem;">
                            <option value="">All Academic Ranks</option>
                            @foreach($availableRanks as $rk)
                                <option value="{{ $rk }}">{{ $rk }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div style="width: 160px;">
                    <select class="form-control form-select" wire:model.live="countryFilter" style="background: var(--bg-card); color: var(--color-text-primary); font-size: 0.82rem;">
                        <option value="">All Countries</option>
                        @foreach($countries as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>

                @if(!empty($search) || !empty($subjectFilter) || !empty($rankFilter) || !empty($countryFilter))
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="resetFilters" style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.78rem;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        <span>Reset Filters</span>
                    </button>
                @endif
            </x-slot:filters>

            <x-slot:headerActions>
                <button type="button" class="btn btn-primary" wire:click="openCreateModal" style="padding: 0.45rem 0.9rem; font-size: 0.82rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    <span>Empanel Expert</span>
                </button>
            </x-slot:headerActions>

            <!-- Responsive Card Grid (3 to 5 per row) -->
            @foreach($reviewers as $rev)
                @php
                    $parsed = $rev->parsed_name;
                    $rankLower = strtolower($rev->academic_rank ?? '');
                    if (str_contains($rankLower, 'assoc')) {
                        $rankClass = 'badge-indigo';
                    } elseif (str_contains($rankLower, 'asst') || str_contains($rankLower, 'assistant')) {
                        $rankClass = 'badge-cyan';
                    } else {
                        $rankClass = 'badge-amber';
                    }
                @endphp
                <x-entity-card 
                    :key="'reviewer-card-'.$rev->id"
                    :avatar-initial="strtoupper(substr($parsed['full_name'] ?: $rev->name, 0, 1))"
                    avatar-class="avatar-theme-purple"
                    :avatar-badge="($rev->country ?? '') === 'Pakistan' ? 'National' : 'Intl'"
                    :subtitle="$rev->university"
                    :is-active="$rev->is_active"
                >
                    <x-slot:titleSlot>
                        <div style="display: flex; flex-direction: column; min-width: 0;">
                            @if(!empty($parsed['prefix']))
                                <span style="font-size: 0.74rem; font-weight: 800; color: var(--secondary); text-transform: uppercase; letter-spacing: 0.05em; line-height: 1.2;">
                                    {{ $parsed['prefix'] }}
                                </span>
                            @endif
                            <div style="font-size: 1.08rem; font-weight: 800; color: var(--color-text-primary); line-height: 1.28; word-break: break-word;" title="{{ $rev->name }}">
                                {{ $parsed['full_name'] }}
                            </div>
                        </div>
                    </x-slot:titleSlot>

                    <!-- Interactive Status Strip (Positioned above the action footer) -->
                    <x-slot:badge>
                        @if(auth()->user()->can('edit-reviewer') || auth()->user()->can('manage-reviewers'))
                            <button type="button" 
                                    class="entity-card-status-toggle-btn"
                                    wire:click="toggleReviewerStatus({{ $rev->id }})"
                                    title="Status: {{ $rev->is_active ? 'Active' : 'Inactive' }} (Click to toggle)"
                                    aria-label="Toggle status for {{ $rev->name }}">
                                <span style="display: inline-flex; align-items: center; gap: 0.4rem;">
                                    <span style="display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: {{ $rev->is_active ? '#10b981' : '#ef4444' }}; box-shadow: 0 0 8px {{ $rev->is_active ? 'rgba(16, 185, 129, 0.9)' : 'rgba(239, 68, 68, 0.9)' }};"></span>
                                    <span>{{ $rev->is_active ? 'ACTIVE' : 'INACTIVE' }}</span>
                                </span>
                                <span style="font-size: 0.68rem; opacity: 0.75; font-weight: 700; text-transform: uppercase;">Click to toggle ↻</span>
                            </button>
                        @else
                            <div style="display: inline-flex; align-items: center; gap: 0.4rem;">
                                <span style="display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: {{ $rev->is_active ? '#10b981' : '#ef4444' }};"></span>
                                <span>{{ $rev->is_active ? 'ACTIVE' : 'INACTIVE' }}</span>
                            </div>
                        @endif
                    </x-slot:badge>

                    <x-slot:tag>
                        <div class="reviewer-rank-badge {{ $rankClass }}" title="{{ $rev->academic_rank }}">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" style="flex-shrink: 0;">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                                <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                            </svg>
                            <span class="reviewer-rank-text">{{ $rev->academic_rank }}</span>
                        </div>
                    </x-slot:tag>

                    <x-slot:body>
                        <div class="entity-card-metric-row" style="align-items: flex-start;">
                            <span class="entity-card-metric-label" style="padding-top: 1px;">Discipline</span>
                            <span class="reviewer-discipline-text" title="{{ $rev->subject }}">
                                {{ $rev->subject }}
                            </span>
                        </div>
                        <div class="entity-card-metric-row">
                            <span class="entity-card-metric-label">Country</span>
                            <span class="entity-card-metric-value" style="display: inline-flex; align-items: center; gap: 0.35rem;">
                                <span>{{ ($rev->country ?? '') === 'Pakistan' ? '🇵🇰' : '🌐' }}</span>
                                <span>{{ $rev->country }}</span>
                                <span class="badge {{ ($rev->country ?? '') === 'Pakistan' ? 'badge-emerald' : 'badge-cyan' }}" style="font-size: 0.62rem; padding: 0.1rem 0.35rem; font-weight: 800;">
                                    {{ ($rev->country ?? '') === 'Pakistan' ? 'National' : 'Intl' }}
                                </span>
                            </span>
                        </div>
                        <div class="entity-card-metric-row" x-data="{ copied: false }">
                            <span class="entity-card-metric-label">Email</span>
                            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.35rem; max-width: 72%; min-width: 0;">
                                @if($rev->user?->email)
                                    <a href="mailto:{{ $rev->user->email }}" 
                                       class="reviewer-email-link" 
                                       title="{{ $rev->user->email }}">
                                        {{ $rev->user->email }}
                                    </a>
                                    <button type="button" 
                                            class="copy-email-btn"
                                            @click.stop="navigator.clipboard.writeText('{{ $rev->user->email }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                                            :title="copied ? 'Copied to clipboard!' : 'Copy email address'"
                                            aria-label="Copy email">
                                        <svg x-show="!copied" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                                            <rect width="14" height="14" x="8" y="8" rx="2" ry="2"/>
                                            <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>
                                        </svg>
                                        <svg x-show="copied" style="display: none;" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.8">
                                            <polyline points="20 6 9 17 4 12"/>
                                        </svg>
                                    </button>
                                @else
                                    <span style="color: var(--color-text-muted); font-size: 0.74rem;">N/A</span>
                                @endif
                            </div>
                        </div>
                        <div class="entity-card-metric-row">
                            <span class="entity-card-metric-label">Dossiers</span>
                            <span class="badge badge-cyan" style="font-weight: 800; font-size: 0.72rem; padding: 0.15rem 0.45rem;" title="{{ $rev->completed_reviews_count ?? 0 }} submitted out of {{ $rev->reviews_count }} assigned">
                                {{ $rev->completed_reviews_count ?? 0 }} / {{ $rev->reviews_count }} Done
                            </span>
                        </div>
                        @if($rev->specialization)
                            <div class="entity-card-metric-row" style="margin-top: 0.15rem; align-items: flex-start;">
                                <span class="entity-card-metric-label" style="padding-top: 1px;">Focus</span>
                                <span class="reviewer-focus-text" title="{{ $rev->specialization }}">
                                    {{ $rev->specialization }}
                                </span>
                            </div>
                        @endif
                    </x-slot:body>

                    <!-- Quick Hover Actions -->
                    <x-slot:quickActions>
                        @if(auth()->user()->hasRole('super-admin') && $rev->user && auth()->id() !== $rev->user->id && !$rev->user->hasRole('super-admin'))
                            <a href="{{ route('impersonate.start', $rev->user->id) }}" 
                               class="action-btn action-btn-impersonate" 
                               data-tooltip="Impersonate ({{ $rev->name }})"
                               aria-label="Impersonate Reviewer">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                            </a>
                        @endif

                        <button type="button" 
                                class="action-btn action-btn-view" 
                                wire:click="openDetailsModal({{ $rev->id }})" 
                                data-tooltip="View Reviewer Profile & Dossiers" 
                                aria-label="View Reviewer Details">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>

                        @if(auth()->user()->can('edit-reviewer') || auth()->user()->can('manage-reviewers'))
                            <button type="button" 
                                    class="action-btn action-btn-edit" 
                                    wire:click="openEditModal({{ $rev->id }})" 
                                    data-tooltip="Edit Reviewer Profile" 
                                    aria-label="Edit Reviewer">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>

                            <button type="button" 
                                    class="action-btn {{ $rev->is_active ? 'action-btn-success' : 'action-btn-delete' }}" 
                                    data-tooltip="{{ $rev->is_active ? 'Active (Click to Deactivate)' : 'Inactive (Click to Activate)' }}" 
                                    wire:click="toggleReviewerStatus({{ $rev->id }})" 
                                    aria-label="Toggle Reviewer Status">
                                @if($rev->is_active)
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                @else
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                @endif
                            </button>
                        @endif

                        @if($rev->user_id && (auth()->user()->hasAnyRole(['super-admin', 'registrar']) || auth()->user()->can('reset-user-password')) && auth()->id() !== $rev->user_id && (!$rev->user?->hasRole('super-admin') || auth()->user()->hasRole('super-admin')))
                            <button type="button" 
                                    class="action-btn" 
                                    style="color: var(--secondary);"
                                    wire:click="openPasswordResetModal({{ $rev->user_id }})" 
                                    data-tooltip="Reset Password & Credentials" 
                                    aria-label="Reset Password">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            </button>
                        @endif
                    </x-slot:quickActions>

                    <!-- Dropdown Options Menu [ICON + Text] -->
                    <x-slot:menuItems>
                        <x-entity-card-menu-item 
                            wire-click="openDetailsModal({{ $rev->id }})"
                            label="View Profile & Dossiers"
                        >
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        </x-entity-card-menu-item>

                        <x-entity-card-menu-item 
                            :href="route('reviewer-profile.print', $rev->id)"
                            target="_blank"
                            label="Print Official Dossier"
                        >
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                        </x-entity-card-menu-item>

                        @if(auth()->user()->can('edit-reviewer') || auth()->user()->can('manage-reviewers'))
                            <x-entity-card-menu-item 
                                wire-click="openEditModal({{ $rev->id }})"
                                label="Edit Reviewer Profile"
                            >
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </x-entity-card-menu-item>

                            <x-entity-card-menu-item 
                                wire-click="toggleReviewerStatus({{ $rev->id }})"
                                label="{{ $rev->is_active ? 'Deactivate Empanelment' : 'Activate Empanelment' }}"
                            >
                                @if($rev->is_active)
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                @else
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                @endif
                            </x-entity-card-menu-item>
                        @endif

                        @if($rev->user_id && (auth()->user()->hasAnyRole(['super-admin', 'registrar']) || auth()->user()->can('reset-user-password')) && auth()->id() !== $rev->user_id && (!$rev->user?->hasRole('super-admin') || auth()->user()->hasRole('super-admin')))
                            <x-entity-card-menu-item 
                                wire-click="openPasswordResetModal({{ $rev->user_id }})"
                                label="Reset Password & Credentials"
                            >
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </x-entity-card-menu-item>
                        @endif

                        @if(auth()->user()->hasRole('super-admin') && $rev->user && auth()->id() !== $rev->user->id && !$rev->user->hasRole('super-admin'))
                            <x-entity-card-menu-item 
                                :href="route('impersonate.start', $rev->user->id)"
                                label="Impersonate Reviewer"
                                theme="impersonate"
                            >
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            </x-entity-card-menu-item>
                        @endif
                    </x-slot:menuItems>
                </x-entity-card>
            @endforeach

            <!-- Table View Alternative Slot -->
            <x-slot:table>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr style="user-select: none;">
                                <th style="cursor: pointer;" wire:click="sortBy('name')">
                                    Expert Referee & Rank @if($sortBy === 'name') <span style="color: var(--secondary);">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span> @endif
                                </th>
                                <th style="cursor: pointer;" wire:click="sortBy('subject')">
                                    Academic Discipline @if($sortBy === 'subject') <span style="color: var(--secondary);">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span> @endif
                                </th>
                                <th style="cursor: pointer;" wire:click="sortBy('university')">
                                    Institution & Country @if($sortBy === 'university') <span style="color: var(--secondary);">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span> @endif
                                </th>
                                <th>Contact Particulars</th>
                                <th>Research Focus</th>
                                <th style="cursor: pointer;" wire:click="sortBy('reviews_count')">
                                    Assigned Dossiers @if($sortBy === 'reviews_count') <span style="color: var(--secondary);">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span> @endif
                                </th>
                                <th style="cursor: pointer;" wire:click="sortBy('is_active')">
                                    Status @if($sortBy === 'is_active') <span style="color: var(--secondary);">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span> @endif
                                </th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reviewers as $rev)
                                @php
                                    $parsed = $rev->parsed_name;
                                @endphp
                                <tr wire:key="reviewer-table-row-{{ $rev->id }}">
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                                            <div class="user-avatar-circle" style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, rgba(6,182,212,0.2), rgba(99,102,241,0.2)); border: 1px solid rgba(6,182,212,0.3); color: var(--secondary); font-weight: 800; font-size: 0.95rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                {{ strtoupper(substr($parsed['full_name'] ?: $rev->name, 0, 1)) }}
                                            </div>
                                            <div style="min-width: 0;">
                                                @if(!empty($parsed['prefix']))
                                                    <span style="display: block; font-size: 0.72rem; font-weight: 800; color: var(--secondary); text-transform: uppercase; letter-spacing: 0.04em; line-height: 1.1;">
                                                        {{ $parsed['prefix'] }}
                                                    </span>
                                                @endif
                                                <strong style="color: var(--color-text-primary); font-size: 0.98rem; font-weight: 800; display: block; line-height: 1.25;">
                                                    {{ $parsed['full_name'] }}
                                                </strong>
                                                <div style="font-size: 0.78rem; color: var(--color-text-secondary); font-weight: 600; margin-top: 2px;">
                                                    {{ $rev->academic_rank }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary); border: 1px solid rgba(99,102,241,0.25); font-weight: 700; font-size: 0.78rem;">
                                            {{ $rev->subject }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong style="color: var(--color-text-primary); font-size: 0.88rem;">{{ $rev->university }}</strong>
                                            <div style="color: var(--color-text-muted); font-size: 0.76rem; margin-top: 2px;">
                                                📍 {{ $rev->country }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 0.35rem;" x-data="{ copied: false }">
                                            @if($rev->user?->email)
                                                <a href="mailto:{{ $rev->user->email }}" class="reviewer-email-link" title="{{ $rev->user->email }}">
                                                    {{ $rev->user->email }}
                                                </a>
                                                <button type="button" 
                                                        class="copy-email-btn"
                                                        @click.stop="navigator.clipboard.writeText('{{ $rev->user->email }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                                                        :title="copied ? 'Copied to clipboard!' : 'Copy email address'"
                                                        aria-label="Copy email">
                                                    <svg x-show="!copied" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                                                    <svg x-show="copied" style="display: none;" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.8"><polyline points="20 6 9 17 4 12"/></svg>
                                                </button>
                                            @else
                                                <span style="color: var(--color-text-muted); font-size: 0.76rem;">N/A</span>
                                            @endif
                                        </div>
                                        @if($rev->phone)
                                            <div style="color: var(--color-text-muted); font-size: 0.74rem; margin-top: 2px;">{{ $rev->phone }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span style="font-size: 0.82rem; color: var(--color-text-secondary); max-width: 240px; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $rev->specialization }}">
                                            {{ $rev->specialization ?: 'General ' . $rev->subject }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge" style="background: rgba(6,182,212,0.12); color: var(--secondary); font-weight: 800; border: 1px solid rgba(6,182,212,0.25); font-size: 0.74rem;" title="{{ $rev->completed_reviews_count ?? 0 }} submitted out of {{ $rev->reviews_count }} assigned">
                                            {{ $rev->completed_reviews_count ?? 0 }} / {{ $rev->reviews_count }} Done
                                        </span>
                                    </td>
                                    <td>
                                        @if(auth()->user()->can('edit-reviewer') || auth()->user()->can('manage-reviewers'))
                                            <button type="button" 
                                                    wire:click="toggleReviewerStatus({{ $rev->id }})"
                                                    style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.28rem 0.65rem; font-size: 0.72rem; font-weight: 800; border-radius: 9999px; cursor: pointer; transition: all 0.2s ease; border: 1px solid; {{ $rev->is_active ? 'background: rgba(16, 185, 129, 0.15); color: #10b981; border-color: rgba(16, 185, 129, 0.35); box-shadow: 0 0 8px rgba(16, 185, 129, 0.15);' : 'background: rgba(239, 68, 68, 0.15); color: #ef4444; border-color: rgba(239, 68, 68, 0.35); box-shadow: 0 0 8px rgba(239, 68, 68, 0.15);' }}"
                                                    title="Status: {{ $rev->is_active ? 'Active' : 'Inactive' }} (Click to toggle)"
                                                    aria-label="Toggle status for {{ $rev->name }}">
                                                <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: {{ $rev->is_active ? '#10b981' : '#ef4444' }}; box-shadow: 0 0 6px {{ $rev->is_active ? 'rgba(16, 185, 129, 0.8)' : 'rgba(239, 68, 68, 0.8)' }};"></span>
                                                <span>{{ $rev->is_active ? 'ACTIVE' : 'INACTIVE' }}</span>
                                            </button>
                                        @else
                                            <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.28rem 0.65rem; font-size: 0.72rem; font-weight: 800; border-radius: 9999px; border: 1px solid; {{ $rev->is_active ? 'background: rgba(16, 185, 129, 0.15); color: #10b981; border-color: rgba(16, 185, 129, 0.35);' : 'background: rgba(239, 68, 68, 0.15); color: #ef4444; border-color: rgba(239, 68, 68, 0.35);' }}">
                                                <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: {{ $rev->is_active ? '#10b981' : '#ef4444' }};"></span>
                                                <span>{{ $rev->is_active ? 'ACTIVE' : 'INACTIVE' }}</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td style="text-align: right;">
                                        <div class="action-btn-group justify-end" x-data="{ openMenu: false }">
                                            <button type="button" 
                                                    class="action-btn action-btn-view" 
                                                    wire:click="openDetailsModal({{ $rev->id }})" 
                                                    data-tooltip="View Reviewer Profile & Dossiers" 
                                                    aria-label="View Reviewer Details">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                            </button>

                                            <a href="{{ route('reviewer-profile.print', $rev->id) }}" 
                                               target="_blank"
                                               class="action-btn" 
                                               style="color: var(--primary);"
                                               data-tooltip="Print Official Dossier" 
                                               aria-label="Print Dossier">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                                    <rect x="6" y="14" width="12" height="8"></rect>
                                                </svg>
                                            </a>

                                            @if(auth()->user()->can('edit-reviewer') || auth()->user()->can('manage-reviewers'))
                                                <button type="button" 
                                                        class="action-btn action-btn-edit" 
                                                        wire:click="openEditModal({{ $rev->id }})" 
                                                        data-tooltip="Edit Reviewer Profile" 
                                                        aria-label="Edit Reviewer">
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                    </svg>
                                                </button>
                                            @endif

                                            @if($rev->user_id && (auth()->user()->hasAnyRole(['super-admin', 'registrar']) || auth()->user()->can('reset-user-password')) && auth()->id() !== $rev->user_id && (!$rev->user?->hasRole('super-admin') || auth()->user()->hasRole('super-admin')))
                                                <button type="button" 
                                                        class="action-btn" 
                                                        style="color: var(--secondary);"
                                                        wire:click="openPasswordResetModal({{ $rev->user_id }})" 
                                                        data-tooltip="Reset Password & Credentials" 
                                                        aria-label="Reset Password">
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                                    </svg>
                                                </button>
                                            @endif

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
                                                    @if(auth()->user()->can('edit-reviewer') || auth()->user()->can('manage-reviewers'))
                                                        <button type="button" 
                                                                class="table-action-menu-item {{ $rev->is_active ? 'danger' : 'success' }}" 
                                                                wire:click="toggleReviewerStatus({{ $rev->id }})">
                                                            @if($rev->is_active)
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                                                <span>Deactivate Reviewer</span>
                                                            @else
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                                                <span>Activate Reviewer</span>
                                                            @endif
                                                        </button>
                                                    @endif
                                                    @if($rev->user_id && (auth()->user()->hasAnyRole(['super-admin', 'registrar']) || auth()->user()->can('reset-user-password')) && auth()->id() !== $rev->user_id && (!$rev->user?->hasRole('super-admin') || auth()->user()->hasRole('super-admin')))
                                                        <button type="button" 
                                                                class="table-action-menu-item" 
                                                                wire:click="openPasswordResetModal({{ $rev->user_id }})">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                                            <span>Reset Password & Credentials</span>
                                                        </button>
                                                    @endif

                                                    @if(auth()->user()->hasRole('super-admin') && $rev->user && auth()->id() !== $rev->user->id && !$rev->user->hasRole('super-admin'))
                                                        <a href="{{ route('impersonate.start', $rev->user->id) }}" class="table-action-menu-item impersonate">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                                            <span>Impersonate Reviewer</span>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center" style="padding: 3rem 1.5rem; color: var(--color-text-muted);">
                                        No empanelled reviewers found matching criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-slot:table>
        </x-entity-card-grid>
    @endif

    <!-- ============================================================== -->
    <!-- TAB 2: ACADEMIC CANDIDACIES & REVIEW DISPATCH                 -->
    <!-- ============================================================== -->
    @if($activeTab === 'dispatch')
        <!-- Header & Live Filters -->
        <div class="card mb-4" style="padding: 1.25rem 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
                <div>
                    <h3 class="card-title" style="margin: 0; font-size: 1.25rem; font-weight: 800; display: flex; align-items: center; gap: 0.6rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--secondary)" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="14" x="2" y="7" rx="2" ry="2"/>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                        </svg>
                        <span>Academic Peer Review Dispatch & Referees Desk</span>
                    </h3>
                    <p class="card-subtitle" style="margin: 0.2rem 0 0;">
                        Assign external peer reviewers calibrated to candidate department, track evaluations, manage deadlines, and inspect confidential referee reports.
                    </p>
                </div>

                <!-- Export & Print Actions -->
                <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
                    <button type="button" 
                            wire:click="exportDispatchCsv" 
                            class="btn btn-secondary btn-sm" 
                            style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.45rem 0.85rem; font-weight: 700;"
                            title="Export academic candidacies and referee assignments to CSV">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        <span>Export Register CSV</span>
                    </button>

                    <a href="{{ route('peer-dispatch-register.print', ['ad' => $advertisementFilter, 'camp' => $candidacyCampaignFilter, 'status' => $candidacyStatusFilter]) }}" 
                       target="_blank" 
                       class="btn btn-secondary btn-sm" 
                       style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.45rem 0.85rem; font-weight: 700;"
                       title="Print Official Confidential Peer Dispatch Register (A4 Landscape)">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                        <span>Print Dispatch Register</span>
                    </a>
                </div>
            </div>

            <!-- Review Status Filter Tabs -->
            <div style="display: flex; gap: 0.35rem; background: var(--bg-surface); padding: 0.3rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); margin-bottom: 1rem; overflow-x: auto;">
                <button type="button" class="btn btn-sm" wire:click="$set('candidacyStatusFilter', 'ALL')" style="{{ $candidacyStatusFilter === 'ALL' ? 'background: var(--bg-card); color: var(--color-text-primary); font-weight: 700;' : 'background: transparent; color: var(--color-text-secondary);' }}">
                    All Dossiers
                </button>
                <button type="button" class="btn btn-sm" wire:click="$set('candidacyStatusFilter', 'NEEDS_DISPATCH')" style="{{ $candidacyStatusFilter === 'NEEDS_DISPATCH' ? 'background: var(--bg-card); color: var(--warning); font-weight: 700;' : 'background: transparent; color: var(--color-text-secondary);' }}">
                    Needs Dispatch
                </button>
                <button type="button" class="btn btn-sm" wire:click="$set('candidacyStatusFilter', 'UNDER_REVIEW')" style="{{ $candidacyStatusFilter === 'UNDER_REVIEW' ? 'background: var(--bg-card); color: var(--secondary); font-weight: 700;' : 'background: transparent; color: var(--color-text-secondary);' }}">
                    Under Review
                </button>
                <button type="button" class="btn btn-sm" wire:click="$set('candidacyStatusFilter', 'COMPLETED')" style="{{ $candidacyStatusFilter === 'COMPLETED' ? 'background: var(--bg-card); color: var(--success); font-weight: 700;' : 'background: transparent; color: var(--color-text-secondary);' }}">
                    Reports In
                </button>
                <button type="button" class="btn btn-sm" wire:click="$set('candidacyStatusFilter', 'STRONGLY_RECOMMENDED')" style="{{ $candidacyStatusFilter === 'STRONGLY_RECOMMENDED' ? 'background: var(--bg-card); color: #10b981; font-weight: 700;' : 'background: transparent; color: var(--color-text-secondary);' }}">
                    Strongly Recommended
                </button>
                <button type="button" class="btn btn-sm" wire:click="$set('candidacyStatusFilter', 'NOT_RECOMMENDED')" style="{{ $candidacyStatusFilter === 'NOT_RECOMMENDED' ? 'background: var(--bg-card); color: #ef4444; font-weight: 700;' : 'background: transparent; color: var(--color-text-secondary);' }}">
                    Not Recommended
                </button>
            </div>

            <!-- Search and Dynamic Dropdown Filters -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 0.85rem; align-items: flex-end;">
                <!-- Search -->
                <div style="position: relative;">
                    <label style="display: block; font-size: 0.74rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-secondary); margin-bottom: 0.3rem;">
                        Search Dossiers
                    </label>
                    <div style="position: relative;">
                        <input type="text" 
                               class="form-control" 
                               wire:model.live.debounce.250ms="candidacySearch" 
                               placeholder="Candidate, CNIC, or #APP..." 
                               style="padding-left: 2.2rem; background: var(--bg-card); color: var(--color-text-primary); font-size: 0.85rem;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--color-text-muted)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); pointer-events: none;">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.3-4.3"/>
                        </svg>
                    </div>
                </div>

                <!-- Advertisement Drive -->
                <div>
                    <label style="display: block; font-size: 0.74rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-secondary); margin-bottom: 0.3rem;">
                        Advertisement Drive
                    </label>
                    <select class="form-control form-select" wire:model.live="advertisementFilter" style="background: var(--bg-card); color: var(--color-text-primary); font-size: 0.85rem;">
                        <option value="ALL">All Advertisements</option>
                        @foreach($availableAdvertisements as $adv)
                            <option value="{{ $adv->id }}">📢 {{ $adv->advertisement_no }} ({{ $adv->year }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Position Select -->
                <div>
                    <label style="display: block; font-size: 0.74rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-secondary); margin-bottom: 0.3rem;">
                        Academic Position / Post
                    </label>
                    <select class="form-control form-select" wire:model.live="candidacyCampaignFilter" style="background: var(--bg-card); color: var(--color-text-primary); font-size: 0.85rem;">
                        <option value="">All Academic Positions</option>
                        @foreach($availableAcademicCampaigns as $c)
                            <option value="{{ $c->id }}">{{ $c->title }} ({{ $c->job_code }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Application Status -->
                <div>
                    <label style="display: block; font-size: 0.74rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-secondary); margin-bottom: 0.3rem;">
                        Dossier Application Status
                    </label>
                    <select class="form-control form-select" wire:model.live="candidacyAppStatusFilter" style="background: var(--bg-card); color: var(--color-text-primary); font-size: 0.85rem;">
                        <option value="ALL">All Overall Statuses</option>
                        <option value="UNDER_REVIEW">In Peer Review (UNDER_REVIEW)</option>
                        <option value="PAID">Paid / Scrutiny Cleared (PAID)</option>
                        <option value="ELIGIBLE">Peer Cleared (ELIGIBLE)</option>
                        <option value="REJECTED">Peer Failed (REJECTED)</option>
                        <option value="SHORTLISTED">Shortlisted for Board</option>
                    </select>
                </div>

                <!-- Reset Filters Button -->
                <div>
                    @if(!empty($candidacySearch) || $advertisementFilter !== 'ALL' || !empty($candidacyCampaignFilter) || $candidacyStatusFilter !== 'ALL' || $candidacyAppStatusFilter !== 'ALL')
                        <button type="button" class="btn btn-secondary btn-sm" wire:click="resetCandidacyFilters" style="width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 0.35rem; font-size: 0.82rem; padding: 0.55rem;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            <span>Reset Filters</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="card" style="padding: 0; overflow: hidden;">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr style="user-select: none;">
                            <th style="cursor: pointer;" wire:click="sortCandidacies('candidate_name')">
                                Candidate Particulars @if($candidacySortBy === 'candidate_name') <span style="color: var(--secondary);">{{ $candidacySortDir === 'asc' ? '▲' : '▼' }}</span> @endif
                            </th>
                            <th style="cursor: pointer;" wire:click="sortCandidacies('tracking_number')">
                                Academic Position @if($candidacySortBy === 'tracking_number') <span style="color: var(--secondary);">{{ $candidacySortDir === 'asc' ? '▲' : '▼' }}</span> @endif
                            </th>
                            <th>Department & Discipline</th>
                            <th style="cursor: pointer;" wire:click="sortCandidacies('status')">
                                Current State @if($candidacySortBy === 'status') <span style="color: var(--secondary);">{{ $candidacySortDir === 'asc' ? '▲' : '▼' }}</span> @endif
                            </th>
                            <th style="cursor: pointer;" wire:click="sortCandidacies('reviews_count')">
                                Peer Review Progress & Referees @if($candidacySortBy === 'reviews_count') <span style="color: var(--secondary);">{{ $candidacySortDir === 'asc' ? '▲' : '▼' }}</span> @endif
                            </th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($academicApplications as $app)
                            @php
                                $reviewCount = $app->reviews->count();
                                $submittedReviews = $app->reviews->where('status', 'SUBMITTED')->count();
                                $pct = $reviewCount > 0 ? round(($submittedReviews / $reviewCount) * 100) : 0;
                            @endphp
                            <tr wire:key="academic-app-{{ $app->id }}">
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.85rem;">
                                        <div class="user-avatar-circle" style="width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, rgba(99,102,241,0.25), rgba(6,182,212,0.25)); color: var(--primary); font-weight: 800; font-size: 0.88rem; display: flex; align-items: center; justify-content: center;">
                                            {{ strtoupper(substr($app->user->basicInfo?->first_name ?: $app->user->email, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong style="color: var(--color-text-primary); font-size: 0.95rem;">
                                                {{ $app->user->basicInfo ? trim($app->user->basicInfo->first_name . ' ' . $app->user->basicInfo->last_name) : $app->user->email }}
                                            </strong>
                                            <div style="font-size: 0.76rem; color: var(--color-text-muted); font-family: 'JetBrains Mono', monospace; margin-top: 1px;">
                                                CNIC: {{ $app->user->basicInfo?->cnic_number ?: 'Pending' }} • #{{ $app->tracking_number }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong style="color: var(--color-text-primary); font-size: 0.92rem;">{{ $app->campaign->title }}</strong>
                                        <div style="font-size: 0.76rem; color: var(--primary); font-weight: 700; font-family: 'JetBrains Mono', monospace; margin-top: 1px;">
                                            {{ $app->campaign->job_code }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary); border: 1px solid rgba(99,102,241,0.25); font-weight: 700; font-size: 0.78rem;">
                                        {{ $app->campaign->department }}
                                    </span>
                                </td>
                                <td>
                                    @if($app->status === 'UNDER_REVIEW')
                                        <span class="badge" style="background: rgba(6,182,212,0.15); color: var(--secondary); border: 1px solid rgba(6,182,212,0.3); font-weight: 700; font-size: 0.76rem;">
                                            ⏳ In Peer Review
                                        </span>
                                    @elseif($app->status === 'PAID')
                                        <span class="badge badge-paid" style="font-weight: 700; font-size: 0.76rem;">
                                            ✓ Paid / Scrutiny
                                        </span>
                                    @elseif($app->status === 'ELIGIBLE')
                                        <span class="badge badge-paid" style="font-weight: 700; font-size: 0.76rem;">
                                            ✓ Eligible
                                        </span>
                                    @else
                                        <span class="badge" style="font-size: 0.76rem;">{{ $app->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($reviewCount > 0)
                                        <div style="min-width: 220px;">
                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem; font-size: 0.75rem;">
                                                <strong style="color: {{ $submittedReviews === $reviewCount ? 'var(--success)' : 'var(--warning)' }};">
                                                    {{ $submittedReviews }} of {{ $reviewCount }} Reports ({{ $pct }}%)
                                                </strong>
                                            </div>
                                            <div class="review-progress-track" style="margin-bottom: 0.45rem;">
                                                <div class="review-progress-fill" style="width: {{ $pct }}%; background: {{ $submittedReviews === $reviewCount ? 'var(--success)' : 'linear-gradient(90deg, var(--secondary), var(--primary))' }};"></div>
                                            </div>
                                            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                                @foreach($app->reviews as $r)
                                                    <div>
                                                        @if($r->status === 'SUBMITTED')
                                                            <span class="review-chip review-chip-submitted" title="Score: {{ $r->overall_score }}/100 • Recommendation: {{ $r->recommendation }}">
                                                                ✓ {{ $r->reviewer->reviewerProfile?->name ?? 'Reviewer' }} ({{ $r->overall_score }}/100)
                                                            </span>
                                                        @else
                                                            <span class="review-chip review-chip-pending" title="Assigned on: {{ $r->assigned_at?->format('Y-m-d') }} • Due: {{ $r->due_date?->format('Y-m-d') }}">
                                                                ⏳ {{ $r->reviewer->reviewerProfile?->name ?? 'Reviewer' }} (Pending)
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <span style="color: var(--color-text-muted); font-size: 0.8rem; font-style: italic;">
                                            Awaiting Dispatch to Experts
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <div class="flex gap-2 justify-end align-center">
                                        @if(auth()->user()->can('assign-reviewer') || auth()->user()->can('manage-reviewers'))
                                            <button type="button" class="btn btn-primary btn-sm" wire:click="openDispatchModal({{ $app->id }})" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.4rem 0.85rem; font-size: 0.82rem;">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                                                    <line x1="22" y1="2" x2="11" y2="13"/>
                                                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                                </svg>
                                                <span>{{ $reviewCount > 0 ? '+ Add Referees' : 'Dispatch for Review' }}</span>
                                            </button>
                                        @endif

                                        @if($submittedReviews >= 1 && ($app->status === 'UNDER_REVIEW' || $submittedReviews >= 2))
                                            <button type="button" class="btn btn-sm" wire:click="evaluatePeerReviewResults({{ $app->id }})" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.4rem 0.85rem; font-size: 0.82rem;" title="Evaluate Peer Review Results under attached criteria (e.g. 2/3 majority requirement)">
                                                ⚡ Evaluate Verdict
                                            </button>
                                        @endif

                                        @if($reviewCount > 0)
                                            @if(auth()->user()->can('view-review-reports') || auth()->user()->can('manage-reviewers'))
                                                <button type="button" class="btn btn-secondary btn-sm" wire:click="openReportsModal({{ $app->id }})" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.4rem 0.85rem; font-size: 0.82rem;">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                                    <span>Reports ({{ $submittedReviews }})</span>
                                                </button>
                                            @endif
                                        @endif

                                        <!-- 3-Dot More Actions Menu -->
                                        <div class="table-action-dropdown-wrap" x-data="{ openMenu: false }" @click.outside="openMenu = false">
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
                                                @if($reviewCount > 0)
                                                    <a href="{{ route('peer-reports.print', $app->id) }}" target="_blank" class="table-action-menu-item">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                                                        <span>Print Consensus Report</span>
                                                    </a>
                                                    <button type="button" class="table-action-menu-item" wire:click="exportApplicationPeerReportsCsv({{ $app->id }})">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                                        <span>Export Reports CSV</span>
                                                    </button>
                                                    @if($submittedReviews < $reviewCount)
                                                        <button type="button" class="table-action-menu-item" wire:click="remindAllPendingReviewers({{ $app->id }})">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                                                            <span>Remind Pending Reviewers</span>
                                                        </button>
                                                    @endif
                                                @endif
                                                <a href="/admin/applications/{{ $app->id }}/print" target="_blank" class="table-action-menu-item">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                                    <span>Print Candidate Dossier</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center" style="padding: 3.5rem; color: var(--color-text-muted);">
                                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(99,102,241,0.1); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                    </div>
                                    <h4 style="font-size: 1rem; font-weight: 700; color: var(--color-text-primary); margin: 0 0 0.35rem 0;">No Academic Candidacies Found</h4>
                                    <p style="font-size: 0.82rem; margin: 0;">No applicant files match the selected position or search filter.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($academicApplications, 'hasPages') && $academicApplications->hasPages())
                <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-color); background: var(--bg-surface);">
                    {{ $academicApplications->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- MODAL 2: SEND APPLICATION FOR REVIEW (FIELD-RELATED MATCHING)   -->
    <!-- ============================================================== -->
    @if($showDispatchModal && $targetApplication)
        <div class="modal-overlay-premium" style="z-index: 1050;">
            <div class="modal-dialog-premium" style="width: 100%; max-width: 900px; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; padding: 0;">
                <!-- Header -->
                <div class="flex justify-between align-center" style="padding: 1.25rem 1.75rem; border-bottom: 1px solid var(--border-color); background: var(--bg-surface); flex-shrink: 0;">
                    <div style="display: flex; align-items: center; gap: 0.85rem;">
                        <div class="brand-icon-box" style="width: 42px; height: 42px; border-radius: 10px; background: rgba(6,182,212,0.15); color: var(--secondary);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="22" y1="2" x2="11" y2="13"/>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="card-title" style="margin: 0; font-size: 1.25rem; font-weight: 800; letter-spacing: -0.02em;">
                                Dispatch Candidacy for External Academic Peer Review
                            </h3>
                            <p class="card-subtitle" style="margin: 0.2rem 0 0; font-size: 0.84rem;">
                                Position: <strong style="color: var(--color-text-primary);">{{ $targetApplication->campaign->title }}</strong> • Department: <strong style="color: var(--secondary);">{{ $targetApplication->campaign->department }}</strong>
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="closeDispatchModal" style="padding: 0.35rem 0.65rem;">✕</button>
                </div>

                <!-- Case Details Header Banner -->
                <div style="padding: 1rem 1.75rem; background: rgba(0,0,0,0.2); border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; flex-shrink: 0;">
                    <div style="display: flex; align-items: center; gap: 0.85rem; min-width: 260px;">
                        <div class="user-avatar-circle" style="width: 40px; height: 40px; font-size: 0.85rem; font-weight: 800; background: linear-gradient(135deg, var(--secondary), var(--primary)); flex-shrink: 0;">
                            {{ strtoupper(substr($targetApplication->user->basicInfo?->first_name ?: $targetApplication->user->email, 0, 1)) }}{{ strtoupper(substr($targetApplication->user->basicInfo?->last_name ?: '', 0, 1)) }}
                        </div>
                        <div>
                            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                <strong style="color: var(--color-text-primary); font-size: 1rem; white-space: nowrap;">
                                    {{ $targetApplication->user->basicInfo ? trim($targetApplication->user->basicInfo->first_name . ' ' . $targetApplication->user->basicInfo->last_name) : $targetApplication->user->email }}
                                </strong>
                                <span class="badge badge-appealed" style="font-size: 0.7rem; text-transform: uppercase;">{{ $targetApplication->campaign->department }}</span>
                            </div>
                            <div style="font-size: 0.76rem; color: var(--color-text-muted); font-family: 'JetBrains Mono', monospace; margin-top: 1px;">
                                CNIC: {{ $targetApplication->user->basicInfo?->cnic_number ?? 'Pending' }} • Dossier Ref: #{{ $targetApplication->tracking_number }}
                            </div>
                        </div>
                    </div>

                    <!-- Filter Pill Toggle -->
                    <label style="cursor: pointer; display: inline-flex; align-items: center; gap: 0.55rem; background: var(--bg-card); padding: 0.45rem 0.95rem; border-radius: 9999px; border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); user-select: none;">
                        <input type="checkbox" wire:model.live="filterByFieldOnly" style="width: 1.1rem; height: 1.1rem; accent-color: var(--secondary); cursor: pointer;">
                        <span style="font-size: 0.82rem; font-weight: 700; color: var(--color-text-primary);">
                            Smart Discipline Match ({{ \App\Services\AcademicDisciplineMatcher::normalizeDepartment($targetApplication->campaign->department) }})
                        </span>
                    </label>
                </div>

                <!-- Field Matched Reviewers Selection Area -->
                <div style="padding: 1.25rem 1.75rem 3rem 1.75rem; overflow-y: auto; flex: 1;">
                    <!-- Statutory Constitution Quorum Banner -->
                    <div style="margin-bottom: 1.25rem; padding: 0.85rem 1.25rem; background: rgba(99,102,241,0.06); border: 1.5px solid rgba(99,102,241,0.2); border-radius: var(--radius-md); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(99,102,241,0.18); color: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem;">
                                ⚖
                            </div>
                            <div>
                                <div style="font-size: 0.82rem; font-weight: 800; color: var(--color-text-primary);">
                                    Statutory Criteria Quorum: Exactly {{ $targetRequiredExperts }} External Subject Referees Required
                                </div>
                                <div style="font-size: 0.74rem; color: var(--color-text-muted);">
                                    {{ $alreadyAssignedCount }} previously dispatched • {{ count($selectedReviewerIds) }} currently picked for dispatch
                                </div>
                            </div>
                        </div>

                        <div>
                            @php
                                $totalDispatchedOrPicked = count($selectedReviewerIds) + $alreadyAssignedCount;
                            @endphp
                            @if($totalDispatchedOrPicked === $targetRequiredExperts)
                                <span class="badge" style="background: rgba(16,185,129,0.18); color: var(--success); font-weight: 800; font-size: 0.78rem; padding: 0.4rem 0.8rem; border: 1px solid rgba(16,185,129,0.3);">
                                    ✓ Quorum Satisfied ({{ $totalDispatchedOrPicked }} of {{ $targetRequiredExperts }})
                                </span>
                            @elseif($totalDispatchedOrPicked < $targetRequiredExperts)
                                <span class="badge" style="background: rgba(245,158,11,0.15); color: var(--warning); font-weight: 800; font-size: 0.78rem; padding: 0.4rem 0.8rem; border: 1px solid rgba(245,158,11,0.3);">
                                    ⏳ {{ $totalDispatchedOrPicked }}/{{ $targetRequiredExperts }} (Pick {{ $targetRequiredExperts - $totalDispatchedOrPicked }} more)
                                </span>
                            @else
                                <span class="badge" style="background: rgba(239,68,68,0.15); color: var(--danger); font-weight: 800; font-size: 0.78rem; padding: 0.4rem 0.8rem; border: 1px solid rgba(239,68,68,0.3);">
                                    ⚠ Over Quota ({{ $totalDispatchedOrPicked }}/{{ $targetRequiredExperts }})
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($errors->has('selectedReviewerIds'))
                        <div class="alert alert-danger mb-4" style="padding: 0.75rem 1rem; font-size: 0.82rem; display: flex; align-items: center; gap: 0.5rem; background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.3); color: var(--danger); border-radius: var(--radius-sm);">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span>{{ $errors->first('selectedReviewerIds') }}</span>
                        </div>
                    @endif
                    <!-- Filter Toolbar inside modal -->
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1rem; flex-wrap: wrap;">
                        <div style="display: flex; align-items: center; gap: 0.6rem;">
                            <div style="width: 28px; height: 28px; border-radius: 8px; background: rgba(99,102,241,0.15); color: var(--primary); display: flex; align-items: center; justify-content: center;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            </div>
                            <h4 style="font-size: 0.98rem; font-weight: 800; color: var(--color-text-primary); margin: 0;">
                                @if($filterByFieldOnly)
                                    Field-Matched Referees in {{ \App\Services\AcademicDisciplineMatcher::normalizeDepartment($targetApplication->campaign->department) }} & Allied Fields
                                @else
                                    All Empanelled Academic Experts
                                @endif
                            </h4>
                            <span class="badge" style="background: rgba(99,102,241,0.15); color: var(--primary); font-size: 0.75rem; font-weight: 800;">
                                {{ $fieldMatchedReviewers->count() }} Found
                            </span>
                        </div>

                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <button type="button" class="btn btn-primary btn-sm" wire:click="openEmpanelAndAssignModal('{{ addslashes($targetApplication->campaign->department) }}')" style="font-size: 0.8rem; padding: 0.4rem 0.85rem; display: inline-flex; align-items: center; gap: 0.4rem; box-shadow: var(--shadow-sm); font-weight: 700;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                <span>+ Empanel New Expert</span>
                            </button>

                            <span class="badge {{ count($selectedReviewerIds) > 0 ? 'badge-paid' : 'badge-draft' }}" style="font-size: 0.78rem; font-weight: 800; padding: 0.4rem 0.85rem;">
                                {{ count($selectedReviewerIds) }} Selected
                            </span>
                        </div>
                    </div>

                    <!-- Inner Search & Discipline Filter Bar -->
                    <div style="display: flex; gap: 0.75rem; margin-bottom: 1rem; align-items: center;">
                        <div style="position: relative; flex: 2;">
                            <input type="text" 
                                   class="form-control" 
                                   wire:model.live.debounce.200ms="dispatchReviewerSearch" 
                                   placeholder="Filter experts by name, subfield, or university..." 
                                   style="padding-left: 2.2rem; font-size: 0.85rem; background: var(--bg-card); color: var(--color-text-primary);">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--color-text-muted)" stroke-width="2" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); pointer-events: none;">
                                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                            </svg>
                        </div>

                        @if(!empty($availableSubjects))
                            <div style="flex: 1;">
                                <select class="form-control form-select" wire:model.live="dispatchDisciplineFilter" style="font-size: 0.85rem; background: var(--bg-card); color: var(--color-text-primary);">
                                    <option value="">All Field Disciplines</option>
                                    @foreach($availableSubjects as $subj)
                                        <option value="{{ $subj }}">{{ $subj }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>

                    @error('selectedReviewerIds')
                        <div class="card mb-3" style="background: rgba(239,68,68,0.12); border-color: rgba(239,68,68,0.3); padding: 0.75rem 1rem;">
                            <span style="color: #fca5a5; font-size: 0.82rem; font-weight: 600;">{{ $message }}</span>
                        </div>
                    @enderror

                    <!-- Reviewers Grid -->
                    <div class="grid grid-cols-2 gap-3 mb-6" style="max-height: 290px; overflow-y: auto;">
                        @forelse($fieldMatchedReviewers as $rev)
                            @php
                                $isAlreadyAssigned = $targetApplication->reviews->contains('reviewer_id', $rev->user_id);
                                $isSelected = in_array($rev->user_id, $selectedReviewerIds);
                            @endphp
                            <div class="expert-card-selectable {{ $isAlreadyAssigned ? 'disabled' : ($isSelected ? 'selected' : '') }}"
                                 wire:key="rev-select-{{ $rev->id }}"
                                 @if(!$isAlreadyAssigned) wire:click="toggleReviewerSelection({{ $rev->user_id }})" @endif>
                                <div>
                                    <div class="flex justify-between align-center mb-1">
                                        <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary); font-size: 0.7rem; font-weight: 700;">{{ $rev->subject }}</span>
                                        <span style="font-size: 0.72rem; color: var(--color-text-muted);">📍 {{ $rev->country }}</span>
                                    </div>

                                    <strong style="color: var(--color-text-primary); font-size: 0.94rem; display: block; margin-bottom: 2px;">
                                        {{ $rev->name }}
                                    </strong>
                                    <span style="font-size: 0.8rem; color: var(--secondary); font-weight: 700; display: block;">
                                        {{ $rev->academic_rank }}
                                    </span>
                                    <span style="font-size: 0.76rem; color: var(--color-text-secondary); display: block;">
                                        {{ $rev->university }}
                                    </span>
                                    @if($rev->specialization)
                                        <div style="font-size: 0.72rem; color: var(--color-text-muted); margin-top: 3px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            Scope: {{ $rev->specialization }}
                                        </div>
                                    @endif
                                </div>

                                <div style="margin-top: 0.75rem; border-top: 1px solid var(--border-color); padding-top: 0.5rem;" class="flex justify-between align-center">
                                    <span style="font-size: 0.72rem; color: var(--color-text-muted);">
                                        Load: {{ $rev->reviews->count() }} dossiers
                                    </span>

                                    @if($isAlreadyAssigned)
                                        <span class="badge" style="background: rgba(16,185,129,0.15); color: var(--success); font-size: 0.68rem; font-weight: 700;">Already Assigned</span>
                                    @elseif($isSelected)
                                        <span class="badge" style="background: rgba(6,182,212,0.15); color: var(--secondary); font-size: 0.68rem; font-weight: 800;">✓ Selected</span>
                                    @else
                                        <span class="btn btn-secondary btn-sm" style="font-size: 0.7rem; padding: 0.2rem 0.55rem;">Select</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div style="grid-column: 1 / -1; text-align: center; color: var(--color-text-muted); padding: 2.5rem 1.5rem; background: rgba(0,0,0,0.15); border: 1.5px dashed var(--border-color); border-radius: var(--radius-md);">
                                <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(6,182,212,0.1); color: var(--secondary); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/>
                                    </svg>
                                </div>
                                <h5 style="color: var(--color-text-primary); font-size: 1rem; font-weight: 700; margin: 0 0 0.35rem 0;">
                                    No External Reviewers Found Matching Search Criteria
                                </h5>
                                <p style="font-size: 0.82rem; color: var(--color-text-secondary); max-width: 460px; margin: 0 auto 1.25rem auto; line-height: 1.5;">
                                    Statutory faculty appointments require external subject reviews. Click below to empanel a new expert or toggle all faculties.
                                </p>
                                <div class="flex gap-2 justify-center flex-wrap">
                                    <button type="button" class="btn btn-primary btn-sm" wire:click="openEmpanelAndAssignModal('{{ addslashes($targetApplication->campaign->department) }}')" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.45rem 0.9rem;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                        <span>+ Empanel New Expert for {{ \App\Services\AcademicDisciplineMatcher::normalizeDepartment($targetApplication->campaign->department) }}</span>
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm" wire:click="$set('filterByFieldOnly', false)" style="padding: 0.45rem 0.9rem;">
                                        <span>Show All Disciplines</span>
                                    </button>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Evaluation Parameters & Instructions Header -->
                    <div style="border-top: 1px solid var(--border-color); padding-top: 1.25rem; margin-top: 0.5rem;">
                        <h5 style="font-size: 0.88rem; font-weight: 700; color: var(--color-text-secondary); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.85rem; display: flex; align-items: center; gap: 0.4rem;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <span>Evaluation Parameters & Instructions</span>
                        </h5>

                        <div class="grid grid-cols-2 gap-4">
                            <x-input label="Evaluation Due Date *" :error="$errors->first('reviewDueDate')">
                                <input type="date" class="form-control" wire:model="reviewDueDate" style="background: var(--bg-card); color: var(--color-text-primary); border-color: var(--border-color);">
                            </x-input>

                            <x-input label="Confidential Instructions to Reviewers" :error="$errors->first('reviewNotes')">
                                <input type="text" class="form-control" wire:model="reviewNotes" placeholder="e.g. Focus on post-PhD HEC category publications..." style="background: var(--bg-card); color: var(--color-text-primary); border-color: var(--border-color);">
                            </x-input>
                        </div>
                    </div>
                </div>

                <!-- Sticky Footer -->
                <div class="flex justify-between align-center" style="padding: 1.25rem 1.75rem; border-top: 1px solid var(--border-color); background: var(--bg-surface); flex-shrink: 0;">
                    <button type="button" class="btn btn-secondary" wire:click="closeDispatchModal">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="dispatchApplicationToReviewers" wire:loading.attr="disabled" :disabled="empty($selectedReviewerIds)">
                        <span wire:loading.remove wire:target="dispatchApplicationToReviewers">Dispatch to {{ count($selectedReviewerIds) }} Expert(s) & Send Official Emails</span>
                        <span wire:loading wire:target="dispatchApplicationToReviewers">Dispatching to Reviewers...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL 1: ADD NEW ACADEMIC REVIEWER (LAYERED ON TOP: Z-INDEX 1600)-->
    <!-- ============================================================== -->
    @if($showCreateModal)
        <div class="modal-overlay-premium" style="z-index: 1600;">
            <div class="modal-dialog-premium" style="width: 100%; max-width: 720px; max-height: 92vh; overflow-y: auto; border-color: var(--secondary-glow); padding: 1.75rem;">
                <div class="flex justify-between align-center mb-4" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div class="brand-icon-box" style="width: 38px; height: 38px; border-radius: 10px; background: rgba(99,102,241,0.12); color: var(--primary);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <line x1="19" y1="8" x2="19" y2="14"/>
                                <line x1="22" y1="11" x2="16" y2="11"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="card-title" style="margin: 0; font-size: 1.25rem; font-weight: 800;">
                                Empanel Academic Subject Expert
                            </h3>
                            <p class="card-subtitle" style="margin: 0.2rem 0 0;">Add external referee for faculty appointment evaluations.</p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="closeCreateModal" style="padding: 0.35rem 0.65rem;">✕</button>
                </div>

                <div class="card mb-4" style="background: rgba(6, 182, 212, 0.08); border-color: var(--secondary-glow); padding: 0.85rem 1rem;">
                    <p style="color: #67e8f9; font-size: 0.82rem; margin: 0; line-height: 1.5;">
                        <strong>Automatic Credential Dispatch:</strong> Once created, Hirely will generate secure portal login credentials and dispatch an official invitation email with evaluation instructions.
                    </p>
                </div>

                <form wire:submit.prevent="createReviewer">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <x-input label="Full Name & Title *" :error="$errors->first('name')">
                            <input type="text" class="form-control" wire:model="name" placeholder="e.g. Prof. Dr. Tariq Mahmood">
                        </x-input>

                        <x-input label="Institutional Email Address *" :error="$errors->first('email')">
                            <input type="email" class="form-control" wire:model="email" placeholder="e.g. tariq.mahmood@nust.edu.pk">
                        </x-input>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <x-input label="Subject / Academic Discipline *" :error="$errors->first('subject')">
                            <input type="text" class="form-control" wire:model="subject" placeholder="e.g. Computer Science, Biotechnology">
                        </x-input>

                        <x-select label="Current Academic Rank / Position *" :error="$errors->first('academic_rank')">
                            <select class="form-control form-select" wire:model="academic_rank">
                                <option value="Professor">Professor</option>
                                <option value="Associate Professor">Associate Professor</option>
                                <option value="Assistant Professor">Assistant Professor</option>
                                <option value="Tenured Professor">Tenured Professor</option>
                                <option value="Distinguished Professor">Distinguished Professor</option>
                                <option value="Senior Scientist">Senior Scientist / Principal Investigator</option>
                                <option value="Industry Fellow">Industry Fellow / Technical Advisor</option>
                                <option value="Principal Consultant">Principal Consultant / Lead Specialist</option>
                                <option value="Executive Director">Executive Director / Senior Director</option>
                            </select>
                        </x-select>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <x-input label="Institutional Affiliation / Organization / University *" :error="$errors->first('university')">
                            <input type="text" class="form-control" wire:model="university" placeholder="e.g. Oxford University, Microsoft Research, NUST">
                        </x-input>

                        <!-- Searchable & Selectable Country Component -->
                        <x-searchable-country-select wire:model="country" label="Affiliated Country *" :error="$errors->first('country')" placeholder="Search or select country..." />
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <x-input label="Contact Number (Optional)" :error="$errors->first('phone')">
                            <input type="text" class="form-control" wire:model="phone" placeholder="e.g. +92 300 1234567">
                        </x-input>

                        <x-input label="Specialization / Sub-discipline (Optional)" :error="$errors->first('specialization')">
                            <input type="text" class="form-control" wire:model="specialization" placeholder="e.g. Artificial Intelligence, NLP, Distributed Systems">
                        </x-input>
                    </div>

                    <div class="flex justify-between align-center" style="border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                        <button type="button" class="btn btn-secondary" wire:click="closeCreateModal">Cancel</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="createReviewer">Create Reviewer & Send Credentials Email</span>
                            <span wire:loading wire:target="createReviewer">Dispatching Credentials...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL 4: EMPANEL & ASSIGN NEW SUBJECT EXPERT (LAYERED AT 1700) -->
    <!-- ============================================================== -->
    @if($showEmpanelAndAssignModal)
        <div class="modal-overlay-premium" style="z-index: 1700;">
            <div class="modal-dialog-premium" style="width: 100%; max-width: 720px; max-height: 92vh; overflow-y: auto; border: 1.5px solid var(--secondary); box-shadow: 0 30px 70px rgba(0,0,0,0.95); padding: 1.75rem;">
                <div class="flex justify-between align-center mb-4" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div class="brand-icon-box" style="width: 40px; height: 40px; border-radius: 10px; background: rgba(6,182,212,0.15); color: var(--secondary);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <line x1="19" y1="8" x2="19" y2="14"/>
                                <line x1="22" y1="11" x2="16" y2="11"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="card-title" style="margin: 0; font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary);">
                                Empanel & Assign New Subject Expert
                            </h3>
                            <p class="card-subtitle" style="margin: 0.2rem 0 0; font-size: 0.82rem;">
                                Pre-configured for <strong style="color: var(--secondary);">{{ \App\Services\AcademicDisciplineMatcher::normalizeDepartment($targetApplication?->campaign?->department ?? '') }}</strong>. Automatically assigned to this candidate upon saving.
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="closeEmpanelAndAssignModal" style="padding: 0.35rem 0.65rem;">✕</button>
                </div>

                <div class="card mb-4" style="background: rgba(6, 182, 212, 0.08); border-color: rgba(6, 182, 212, 0.3); padding: 0.85rem 1rem;">
                    <p style="color: #67e8f9; font-size: 0.82rem; margin: 0; line-height: 1.5;">
                        <strong>Automatic Credential Dispatch & Selection:</strong> The expert will receive secure portal credentials and peer review guidance via email, and will be automatically selected in your current review dispatch.
                    </p>
                </div>

                <form wire:submit.prevent="createReviewer">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <x-input label="Full Name & Title *" :error="$errors->first('name')">
                            <input type="text" class="form-control" wire:model="name" placeholder="e.g. Prof. Dr. Tariq Mahmood">
                        </x-input>

                        <x-input label="Institutional Email Address *" :error="$errors->first('email')">
                            <input type="email" class="form-control" wire:model="email" placeholder="e.g. tariq.mahmood@nust.edu.pk">
                        </x-input>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <x-input label="Subject / Academic Discipline *" :error="$errors->first('subject')">
                            <input type="text" class="form-control" wire:model="subject" placeholder="e.g. Computer Science">
                        </x-input>

                        <x-select label="Current Academic Rank / Position *" :error="$errors->first('academic_rank')">
                            <select class="form-control form-select" wire:model="academic_rank">
                                <option value="Professor">Professor</option>
                                <option value="Associate Professor">Associate Professor</option>
                                <option value="Assistant Professor">Assistant Professor</option>
                                <option value="Tenured Professor">Tenured Professor</option>
                                <option value="Distinguished Professor">Distinguished Professor</option>
                                <option value="Senior Scientist">Senior Scientist / Principal Investigator</option>
                                <option value="Industry Fellow">Industry Fellow / Technical Advisor</option>
                                <option value="Principal Consultant">Principal Consultant / Lead Specialist</option>
                                <option value="Executive Director">Executive Director / Senior Director</option>
                            </select>
                        </x-select>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <x-input label="Institutional Affiliation / Organization / University *" :error="$errors->first('university')">
                            <input type="text" class="form-control" wire:model="university" placeholder="e.g. Oxford University, Microsoft Research, NUST">
                        </x-input>

                        <x-searchable-country-select wire:model="country" label="Affiliated Country *" :error="$errors->first('country')" placeholder="Search or select country..." />
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <x-input label="Contact Phone (Optional)" :error="$errors->first('phone')">
                            <input type="text" class="form-control" wire:model="phone" placeholder="e.g. +92 300 1234567">
                        </x-input>

                        <x-input label="Specialization / Research Focus (Optional)" :error="$errors->first('specialization')">
                            <input type="text" class="form-control" wire:model="specialization" placeholder="e.g. AI, Distributed Systems">
                        </x-input>
                    </div>

                    <div class="flex justify-between align-center" style="border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                        <button type="button" class="btn btn-secondary" wire:click="closeEmpanelAndAssignModal">Cancel</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="createReviewer">✓ Empanel & Select for Review</span>
                            <span wire:loading wire:target="createReviewer">Empanelling Expert...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL 3: VIEW ACADEMIC REVIEW REPORTS                          -->
    <!-- ============================================================== -->
    @if($showReportsModal && $viewingApplication)
        @php
            $submitted = $applicationReviews->where('status', 'SUBMITTED');
            $pendingReviews = $applicationReviews->where('status', '!=', 'SUBMITTED');
            $avgScore = $submitted->count() > 0 ? $submitted->avg('overall_score') : null;
            $stronglyRecCount = $submitted->where('recommendation', 'STRONGLY_RECOMMENDED')->count();
            $recCount = $submitted->whereIn('recommendation', ['STRONGLY_RECOMMENDED', 'RECOMMENDED'])->count();
        @endphp
        <div class="modal-overlay-premium" style="z-index: 1050;">
            <div class="modal-dialog-premium" style="width: 100%; max-width: 900px; max-height: 92vh; overflow-y: auto; padding: 1.75rem; background: var(--bg-card); border: 1.5px solid var(--border-color); box-shadow: 0 25px 60px rgba(0,0,0,0.4); border-radius: var(--radius-lg);">
                
                <!-- Modal Header -->
                <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.35rem;">
                            <span class="badge badge-indigo" style="font-size: 0.72rem; font-weight: 800;">CONFIDENTIAL PEER DOSSIER</span>
                            <span class="badge badge-slate" style="font-size: 0.72rem; font-family: 'JetBrains Mono', monospace;">#{{ $viewingApplication->tracking_number }}</span>
                        </div>
                        <h3 class="card-title" style="margin: 0; font-size: 1.3rem; font-weight: 800; color: var(--color-text-primary);">
                            External Peer Review Reports: {{ $viewingApplication->campaign->title }}
                        </h3>
                        <p class="card-subtitle" style="margin: 0.3rem 0 0; font-size: 0.85rem; color: var(--color-text-secondary);">
                            Candidate: <strong style="color: var(--color-text-primary);">{{ $viewingApplication->user->basicInfo ? trim($viewingApplication->user->basicInfo->first_name . ' ' . $viewingApplication->user->basicInfo->last_name) : $viewingApplication->user->email }}</strong> • Department: <strong style="color: var(--secondary);">{{ $viewingApplication->campaign->department }}</strong>
                        </p>
                    </div>

                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <a href="{{ route('peer-reports.print', $viewingApplication->id) }}" 
                           target="_blank" 
                           class="btn btn-primary btn-sm" 
                           style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.45rem 0.85rem; font-weight: 800; box-shadow: 0 4px 12px var(--primary-glow);"
                           title="Print Official Certified External Peer Consensus Evaluation Certificate (A4 Portrait)">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                            <span>Print Consensus Report</span>
                        </a>

                        <button type="button" 
                                wire:click="exportApplicationPeerReportsCsv({{ $viewingApplication->id }})" 
                                class="btn btn-secondary btn-sm" 
                                style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.45rem 0.8rem; font-weight: 700;"
                                title="Download all peer reports for this candidate as CSV">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            <span>Export CSV</span>
                        </button>

                        <button type="button" class="btn btn-secondary btn-sm" wire:click="closeReportsModal" style="padding: 0.45rem 0.75rem;">✕</button>
                    </div>
                </div>

                <!-- Live Status Notifications -->
                @if(session()->has('message'))
                    <div style="background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3); border-radius: var(--radius-sm); padding: 0.75rem 1rem; margin-bottom: 1.25rem; color: var(--success); font-weight: 700; font-size: 0.85rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span>✓</span>
                        <span>{{ session('message') }}</span>
                    </div>
                @endif
                @if(session()->has('warning'))
                    <div style="background: rgba(245,158,11,0.12); border: 1px solid rgba(245,158,11,0.3); border-radius: var(--radius-sm); padding: 0.75rem 1rem; margin-bottom: 1.25rem; color: var(--warning); font-weight: 700; font-size: 0.85rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span>⚠</span>
                        <span>{{ session('warning') }}</span>
                    </div>
                @endif
                @if(session()->has('error'))
                    <div style="background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.3); border-radius: var(--radius-sm); padding: 0.75rem 1rem; margin-bottom: 1.25rem; color: var(--danger); font-weight: 700; font-size: 0.85rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span>✕</span>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Executive Consensus Assessment 4-Stat Banner -->
                <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; box-shadow: var(--shadow-sm);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                        <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--secondary); font-weight: 800; letter-spacing: 0.05em;">
                            Consensus Dossier Assessment Summary
                        </span>
                        <span class="badge {{ $submitted->count() === $applicationReviews->count() ? 'badge-emerald' : 'badge-amber' }}" style="font-size: 0.74rem; font-weight: 800;">
                            {{ $submitted->count() === $applicationReviews->count() ? 'Quorum Completed' : 'Peer Quorum Incomplete' }}
                        </span>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 1rem;">
                        <!-- Metric 1: Assigned Referees -->
                        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem 1rem;">
                            <div style="font-size: 0.72rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">Referees Dispatched</div>
                            <div style="font-size: 1.35rem; font-weight: 900; color: var(--color-text-primary); margin-top: 0.2rem;">
                                {{ $applicationReviews->count() }} <span style="font-size: 0.75rem; color: var(--color-text-secondary); font-weight: 600;">Experts</span>
                            </div>
                        </div>

                        <!-- Metric 2: Reports Submitted -->
                        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem 1rem;">
                            <div style="font-size: 0.72rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">Reports Submitted</div>
                            <div style="font-size: 1.35rem; font-weight: 900; color: {{ $submitted->count() > 0 ? 'var(--secondary)' : 'var(--color-text-muted)' }}; margin-top: 0.2rem;">
                                {{ $submitted->count() }} <span style="font-size: 0.75rem; color: var(--color-text-secondary); font-weight: 600;">of {{ $applicationReviews->count() }}</span>
                            </div>
                        </div>

                        <!-- Metric 3: Average Peer Score -->
                        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem 1rem;">
                            <div style="font-size: 0.72rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">Average Peer Score</div>
                            <div style="font-size: 1.35rem; font-weight: 900; color: {{ $avgScore >= 80 ? 'var(--success)' : ($avgScore >= 60 ? 'var(--warning)' : ($avgScore ? 'var(--danger)' : 'var(--color-text-muted)')) }}; margin-top: 0.2rem;">
                                {{ $avgScore !== null ? number_format($avgScore, 1) : 'Pending' }} 
                                @if($avgScore !== null)<span style="font-size: 0.75rem; color: var(--color-text-secondary); font-weight: 600;">/ 100</span>@endif
                            </div>
                        </div>

                        <!-- Metric 4: Statutory Recommendation -->
                        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem 1rem;">
                            <div style="font-size: 0.72rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">Statutory Endorsement</div>
                            <div style="font-size: 1.15rem; font-weight: 900; color: {{ $recCount >= 2 ? 'var(--success)' : 'var(--warning)' }}; margin-top: 0.2rem;">
                                {{ $recCount }}/{{ $applicationReviews->count() }} Recommended
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Individual Referee Evaluation Cards -->
                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    @forelse($applicationReviews as $ar)
                        <div class="card" style="background: var(--bg-surface); border: 1.5px solid var(--border-color); padding: 1.35rem; border-radius: var(--radius-md); box-shadow: var(--shadow-sm);">
                            <!-- Referee Header -->
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem; flex-wrap: wrap; gap: 0.5rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div class="user-avatar-circle" style="width: 42px; height: 42px; border-radius: 50%; background: linear-gradient(135deg, rgba(6,182,212,0.2), rgba(99,102,241,0.2)); color: var(--secondary); font-weight: 800; font-size: 0.95rem; display: flex; align-items: center; justify-content: center;">
                                        {{ strtoupper(substr($ar->reviewer->reviewerProfile?->name ?? 'R', 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong style="font-size: 1.05rem; color: var(--color-text-primary);">
                                            {{ $ar->reviewer->reviewerProfile?->name ?? 'External Reviewer' }}
                                        </strong>
                                        <div style="font-size: 0.8rem; color: var(--color-text-secondary); margin-top: 1px;">
                                            {{ $ar->reviewer->reviewerProfile?->academic_rank }} • {{ $ar->reviewer->reviewerProfile?->university }} ({{ $ar->reviewer->reviewerProfile?->country }}) • <span style="color: var(--secondary); font-weight: 700;">{{ $ar->reviewer->reviewerProfile?->subject }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    @if($ar->status === 'SUBMITTED')
                                        <span class="badge" style="background: rgba(16,185,129,0.15); color: var(--success); font-weight: 800; font-size: 0.8rem; padding: 0.35rem 0.75rem; border: 1px solid rgba(16,185,129,0.3);">
                                            ✓ Report Submitted
                                        </span>
                                    @else
                                        <span class="badge" style="background: rgba(245,158,11,0.15); color: var(--warning); font-weight: 800; font-size: 0.8rem; padding: 0.35rem 0.75rem; border: 1px solid rgba(245,158,11,0.3);">
                                            ⏳ Pending Evaluation
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($ar->status === 'SUBMITTED')
                                <!-- Score & Recommendation Block -->
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; background: var(--bg-card); padding: 1rem 1.25rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); margin-bottom: 1rem;">
                                    <div>
                                        <span style="font-size: 0.72rem; color: var(--color-text-muted); text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em;">Quantitative Peer Score</span><br>
                                        <strong style="color: {{ $ar->overall_score >= 80 ? 'var(--success)' : ($ar->overall_score >= 60 ? 'var(--warning)' : 'var(--danger)') }}; font-size: 1.5rem; font-weight: 900;">
                                            {{ $ar->overall_score }} <span style="font-size: 0.85rem; color: var(--color-text-secondary); font-weight: 600;">/ 100</span>
                                        </strong>
                                    </div>
                                    <div>
                                        <span style="font-size: 0.72rem; color: var(--color-text-muted); text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em;">Statutory Recommendation</span><br>
                                        <span class="badge" style="margin-top: 0.3rem; font-size: 0.85rem; font-weight: 800; padding: 0.35rem 0.75rem; background: {{ in_array($ar->recommendation, ['STRONGLY_RECOMMENDED', 'RECOMMENDED']) ? 'rgba(16,185,129,0.15)' : 'rgba(239,68,68,0.15)' }}; color: {{ in_array($ar->recommendation, ['STRONGLY_RECOMMENDED', 'RECOMMENDED']) ? 'var(--success)' : 'var(--danger)' }}; border: 1px solid {{ in_array($ar->recommendation, ['STRONGLY_RECOMMENDED', 'RECOMMENDED']) ? 'rgba(16,185,129,0.3)' : 'rgba(239,68,68,0.3)' }};">
                                            {{ str_replace('_', ' ', $ar->recommendation) }}
                                        </span>
                                    </div>
                                    <div>
                                        <span style="font-size: 0.72rem; color: var(--color-text-muted); text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em;">Submitted Timestamp</span><br>
                                        <div style="font-size: 0.85rem; font-weight: 700; color: var(--color-text-primary); margin-top: 0.35rem;">
                                            {{ $ar->submitted_at?->format('d M Y, h:i A') ?: 'Recorded' }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Qualitative Remarks -->
                                <div style="margin-bottom: 1rem;">
                                    <strong style="font-size: 0.75rem; color: var(--color-text-secondary); text-transform: uppercase; letter-spacing: 0.05em;">Qualitative Expert Evaluation Remarks:</strong>
                                    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-left: 4px solid var(--secondary); padding: 0.95rem 1.25rem; border-radius: 0 var(--radius-sm) var(--radius-sm) 0; color: var(--color-text-primary); font-size: 0.9rem; line-height: 1.6; font-style: italic; margin-top: 0.35rem;">
                                        "{{ $ar->report_text ?: 'Evaluation submitted without narrative remarks.' }}"
                                    </div>
                                </div>

                                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; padding-top: 0.75rem; border-top: 1px solid var(--border-color); margin-top: 0.75rem;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                        <a href="{{ route('peer-reports.print', $viewingApplication->id) }}#reviewer-{{ $ar->id }}" target="_blank" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.78rem; font-weight: 700;">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                                            <span>Print Referee Report</span>
                                        </a>

                                        @if($ar->evaluation_file_path)
                                            <a href="/storage/{{ $ar->evaluation_file_path }}" target="_blank" class="btn btn-secondary btn-sm" style="font-size: 0.78rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 700;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                                <span>Download Signed PDF</span>
                                            </a>
                                        @endif
                                    </div>

                                    @if(auth()->user()->hasAnyRole(['super-admin', 'registrar']) || auth()->user()->can('manage-reviewers'))
                                        <button type="button" 
                                                wire:click="reopenReviewerEvaluation({{ $ar->id }})" 
                                                wire:confirm="Re-open this peer review evaluation? This will clear the score and permit the reviewer to re-submit their evaluation."
                                                class="btn btn-sm" 
                                                style="background: rgba(245, 158, 11, 0.12); color: var(--warning); border: 1px solid rgba(245, 158, 11, 0.25); display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.78rem; font-weight: 700;"
                                                title="Re-open evaluation for revisions">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>
                                            <span>Re-open for Revision</span>
                                        </button>
                                    @endif
                                </div>
                            @else
                                <!-- Pending Evaluation Details & Actions -->
                                <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1rem 1.25rem; margin-bottom: 1rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem; font-size: 0.85rem;">
                                        <span style="color: var(--color-text-secondary);">
                                            Assignment Dispatched: <strong style="color: var(--color-text-primary);">{{ $ar->assigned_at?->format('d M Y') ?: 'Recently' }}</strong>
                                        </span>
                                        <span style="color: var(--color-text-secondary);">
                                            Statutory Due Date: <strong style="color: var(--color-text-primary);">{{ $ar->due_date?->format('d M Y') ?: 'In 14 days' }}</strong>
                                            @if($ar->due_date && now()->greaterThan($ar->due_date))
                                                <span class="badge badge-rose" style="font-size: 0.7rem; font-weight: 800; margin-left: 0.35rem;">OVERDUE</span>
                                            @endif
                                        </span>
                                    </div>
                                    @if($ar->confidential_notes)
                                        <div style="margin-top: 0.5rem; font-size: 0.8rem; color: var(--color-text-muted); font-style: italic;">
                                            Dispatch Notes: "{{ $ar->confidential_notes }}"
                                        </div>
                                    @endif
                                </div>

                                <!-- Reviewer Actions Row -->
                                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; padding-top: 0.5rem; border-top: 1px solid var(--border-color);">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                        <button type="button" 
                                                wire:click="sendReviewerReminder({{ $ar->id }})" 
                                                class="btn btn-secondary btn-sm" 
                                                style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.8rem; font-weight: 700;"
                                                title="Send confidential reminder email to this referee">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                                            <span>Send Reminder Email</span>
                                        </button>

                                        <button type="button" 
                                                wire:click="extendReviewDeadline({{ $ar->id }}, 7)" 
                                                class="btn btn-secondary btn-sm" 
                                                style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.8rem; font-weight: 700;"
                                                title="Grant 7 additional calendar days to evaluate dossier">
                                            <span>+7 Days Deadline</span>
                                        </button>

                                        <button type="button" 
                                                wire:click="extendReviewDeadline({{ $ar->id }}, 14)" 
                                                class="btn btn-secondary btn-sm" 
                                                style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.8rem; font-weight: 700;"
                                                title="Grant 14 additional calendar days to evaluate dossier">
                                            <span>+14 Days Deadline</span>
                                        </button>
                                    </div>

                                    <div>
                                        <button type="button" 
                                                wire:click="removeReviewerAssignment({{ $ar->id }})" 
                                                wire:confirm="Revoke this referee assignment? The applicant will have 1 fewer assigned referee." 
                                                class="btn btn-sm" 
                                                style="background: rgba(239,68,68,0.12); color: var(--danger); border: 1px solid rgba(239,68,68,0.25); display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.8rem; font-weight: 700;"
                                                title="Revoke and remove this external referee assignment">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                            <span>Revoke Assignment</span>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="card text-center" style="padding: 2.5rem; color: var(--color-text-muted);">
                            <p style="font-size: 0.9rem; margin: 0;">No external peer review assignments registered for this application yet.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Modal Footer -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                    <div style="font-size: 0.8rem; color: var(--color-text-muted);">
                        Confidential proceedings under Statutory Recruitment & Peer Review Ordinance.
                    </div>
                    <button type="button" class="btn btn-secondary" wire:click="closeReportsModal">
                        Close Reports
                    </button>
                </div>

            </div>
        </div>
    @endif
    <!-- MODAL 5: REVIEWER PROFILE & DOSSIERS LEDGER MODAL             -->
    <!-- ============================================================== -->
    @if($showDetailsModal && $selectedReviewerForDetails)
        @php
            $detParsed = $selectedReviewerForDetails->parsed_name;
            $detReviews = $selectedReviewerForDetails->reviews ?? collect();
            $completedCount = $detReviews->where('status', 'SUBMITTED')->count();
            $pendingCount = $detReviews->where('status', '!=', 'SUBMITTED')->count();
            $avgScore = $completedCount > 0 ? $detReviews->where('status', 'SUBMITTED')->avg('overall_score') : null;
        @endphp
        <div class="modal-overlay-premium" style="z-index: 1650;">
            <div class="modal-dialog-premium" style="width: 100%; max-width: 900px; max-height: 92vh; overflow-y: auto; padding: 2rem; border-color: rgba(6,182,212,0.4); box-shadow: 0 30px 80px rgba(0,0,0,0.85);">
                <!-- Modal Header -->
                <div class="flex justify-between align-center mb-4" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
                    <div style="display: flex; align-items: center; gap: 1rem; min-width: 0;">
                        <div class="user-avatar-circle" style="width: 54px; height: 54px; border-radius: 14px; background: linear-gradient(135deg, rgba(6,182,212,0.25), rgba(99,102,241,0.25)); border: 1.5px solid rgba(6,182,212,0.4); color: var(--secondary); font-weight: 800; font-size: 1.4rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            {{ strtoupper(substr($detParsed['full_name'] ?: $selectedReviewerForDetails->name, 0, 1)) }}
                        </div>
                        <div style="min-width: 0;">
                            @if(!empty($detParsed['prefix']))
                                <span style="display: block; font-size: 0.74rem; font-weight: 800; color: var(--secondary); text-transform: uppercase; letter-spacing: 0.05em; line-height: 1.2;">
                                    {{ $detParsed['prefix'] }}
                                </span>
                            @endif
                            <h3 class="card-title" style="margin: 0; font-size: 1.35rem; font-weight: 800; color: var(--color-text-primary); line-height: 1.2; word-break: break-word;">
                                {{ $detParsed['full_name'] }}
                            </h3>
                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.35rem; flex-wrap: wrap;">
                                <span class="reviewer-rank-badge badge-amber" style="font-size: 0.72rem; padding: 0.15rem 0.5rem;">
                                    {{ $selectedReviewerForDetails->academic_rank }}
                                </span>
                                <span class="badge badge-indigo" style="font-size: 0.72rem; padding: 0.15rem 0.5rem;">
                                    {{ $selectedReviewerForDetails->subject }}
                                </span>
                                <span style="font-size: 0.8rem; color: var(--color-text-muted);">
                                    📍 {{ $selectedReviewerForDetails->university }} ({{ $selectedReviewerForDetails->country }})
                                </span>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
                        <a href="{{ route('reviewer-profile.print', $selectedReviewerForDetails->id) }}" 
                           target="_blank" 
                           class="btn btn-secondary btn-sm"
                           style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.38rem 0.75rem; font-size: 0.8rem; font-weight: 700;"
                           data-tooltip="Print Official Profile & Dossier (A4)">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                <rect x="6" y="14" width="12" height="8"></rect>
                            </svg>
                            <span>Print Dossier</span>
                        </a>

                        <button type="button" 
                                class="btn btn-secondary btn-sm"
                                wire:click="exportReviewerDossiersCsv({{ $selectedReviewerForDetails->id }})"
                                style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.38rem 0.75rem; font-size: 0.8rem; font-weight: 700;"
                                data-tooltip="Download Dossiers as CSV">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="7 10 12 15 17 10"/>
                                <line x1="12" x2="12" y1="15" y2="3"/>
                            </svg>
                            <span>Export CSV</span>
                        </button>

                        @if(auth()->user()->can('edit-reviewer') || auth()->user()->can('manage-reviewers'))
                            <button type="button" 
                                    wire:click="toggleReviewerStatus({{ $selectedReviewerForDetails->id }})"
                                    style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.75rem; font-size: 0.76rem; font-weight: 800; border-radius: 9999px; cursor: pointer; transition: all 0.2s ease; border: 1px solid; {{ $selectedReviewerForDetails->is_active ? 'background: rgba(16, 185, 129, 0.15); color: #10b981; border-color: rgba(16, 185, 129, 0.35); box-shadow: 0 0 10px rgba(16, 185, 129, 0.2);' : 'background: rgba(239, 68, 68, 0.15); color: #ef4444; border-color: rgba(239, 68, 68, 0.35); box-shadow: 0 0 10px rgba(239, 68, 68, 0.2);' }}"
                                    title="Click to toggle status">
                                <span style="display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: {{ $selectedReviewerForDetails->is_active ? '#10b981' : '#ef4444' }};"></span>
                                <span>{{ $selectedReviewerForDetails->is_active ? 'ACTIVE' : 'INACTIVE' }}</span>
                            </button>
                        @endif

                        <button type="button" class="btn btn-secondary btn-sm" wire:click="closeDetailsModal" style="padding: 0.4rem 0.75rem; font-size: 0.85rem;">✕</button>
                    </div>
                </div>

                <!-- Referee KPI Metrics Strip (Theme Adaptive) -->
                <div class="referee-kpi-grid">
                    <div class="referee-kpi-card">
                        <span class="referee-kpi-label">Assigned Dossiers</span>
                        <div class="referee-kpi-val" style="color: var(--secondary);">
                            {{ $detReviews->count() }}
                        </div>
                    </div>
                    <div class="referee-kpi-card">
                        <span class="referee-kpi-label">Completed / Submitted</span>
                        <div class="referee-kpi-val" style="color: #10b981;">
                            {{ $completedCount }}
                        </div>
                    </div>
                    <div class="referee-kpi-card">
                        <span class="referee-kpi-label">Pending Evaluation</span>
                        <div class="referee-kpi-val" style="color: #f59e0b;">
                            {{ $pendingCount }}
                        </div>
                    </div>
                    <div class="referee-kpi-card">
                        <span class="referee-kpi-label">Mean Rating Score</span>
                        <div class="referee-kpi-val" style="color: var(--primary);">
                            {{ $avgScore !== null ? number_format($avgScore, 1) . ' / 100' : 'N/A' }}
                        </div>
                    </div>
                </div>

                <!-- Referee Particulars Grid -->
                <div class="card mb-4" style="background: var(--bg-surface); border-color: var(--border-color); padding: 1.25rem;">
                    <h5 style="font-size: 0.82rem; font-weight: 800; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 1rem 0;">
                        Institutional Affiliation & Contact Details
                    </h5>
                    <div class="grid grid-cols-2 gap-4">
                        <div x-data="{ copied: false }">
                            <span style="font-size: 0.74rem; color: var(--color-text-muted); text-transform: uppercase; font-weight: 700;">Institutional Email</span>
                            <div style="display: flex; align-items: center; gap: 0.45rem; margin-top: 3px;">
                                @if($selectedReviewerForDetails->user?->email)
                                    <a href="mailto:{{ $selectedReviewerForDetails->user->email }}" 
                                       style="color: var(--color-text-primary); font-size: 0.88rem; font-weight: 600; text-decoration: none; font-family: 'JetBrains Mono', monospace;"
                                       class="reviewer-email-link">
                                        {{ $selectedReviewerForDetails->user->email }}
                                    </a>
                                    <button type="button" 
                                            class="copy-email-btn"
                                            @click.stop="navigator.clipboard.writeText('{{ $selectedReviewerForDetails->user->email }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                                            :title="copied ? 'Copied!' : 'Copy email address'">
                                        <svg x-show="!copied" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                                            <rect width="14" height="14" x="8" y="8" rx="2" ry="2"/>
                                            <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>
                                        </svg>
                                        <svg x-show="copied" style="display: none;" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.8">
                                            <polyline points="20 6 9 17 4 12"/>
                                        </svg>
                                    </button>
                                @else
                                    <span style="color: var(--color-text-muted); font-size: 0.85rem;">Not linked</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <span style="font-size: 0.74rem; color: var(--color-text-muted); text-transform: uppercase; font-weight: 700;">Direct Phone</span>
                            <div style="color: var(--color-text-primary); font-size: 0.88rem; font-weight: 600; margin-top: 3px;">
                                @if($selectedReviewerForDetails->phone)
                                    <a href="tel:{{ $selectedReviewerForDetails->phone }}" style="color: var(--color-text-primary); text-decoration: none;">
                                        {{ $selectedReviewerForDetails->phone }}
                                    </a>
                                @else
                                    <span style="color: var(--color-text-muted);">Not provided</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <span style="font-size: 0.74rem; color: var(--color-text-muted); text-transform: uppercase; font-weight: 700;">Institution / University</span>
                            <div style="color: var(--color-text-primary); font-size: 0.88rem; font-weight: 600; margin-top: 3px;">
                                {{ $selectedReviewerForDetails->university }}
                            </div>
                        </div>
                        <div>
                            <span style="font-size: 0.74rem; color: var(--color-text-muted); text-transform: uppercase; font-weight: 700;">Jurisdiction & Country</span>
                            <div style="color: var(--color-text-primary); font-size: 0.88rem; font-weight: 600; margin-top: 3px; display: flex; align-items: center; gap: 0.35rem;">
                                <span>{{ ($selectedReviewerForDetails->country ?? '') === 'Pakistan' ? '🇵🇰' : '🌐' }}</span>
                                <span>{{ $selectedReviewerForDetails->country }}</span>
                                <span class="badge {{ ($selectedReviewerForDetails->country ?? '') === 'Pakistan' ? 'badge-emerald' : 'badge-cyan' }}" style="font-size: 0.65rem; padding: 0.1rem 0.4rem; font-weight: 800;">
                                    {{ ($selectedReviewerForDetails->country ?? '') === 'Pakistan' ? 'National Referee' : 'International Referee' }}
                                </span>
                            </div>
                        </div>
                        <div style="grid-column: 1 / -1;">
                            <span style="font-size: 0.74rem; color: var(--color-text-muted); text-transform: uppercase; font-weight: 700;">Research Focus & Domain Expertise</span>
                            <div style="color: var(--color-text-primary); font-size: 0.85rem; margin-top: 5px; line-height: 1.5; background: var(--bg-card); padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                                {{ $selectedReviewerForDetails->specialization ?: 'General ' . $selectedReviewerForDetails->subject }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assigned Candidacies & Reviews Ledger (with Tab Filtering) -->
                <div class="mb-4" x-data="{ dossierTab: 'all' }">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem; flex-wrap: wrap; gap: 0.5rem;">
                        <h5 style="font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--secondary)" stroke-width="2.3"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                            <span>Assigned Candidacy Dossiers ({{ $detReviews->count() }})</span>
                        </h5>

                        <!-- Status Filter Pills -->
                        @if($detReviews->isNotEmpty())
                            <div class="dossier-filter-pills">
                                <button type="button" class="dossier-tab-btn" :class="{ 'active': dossierTab === 'all' }" @click="dossierTab = 'all'">
                                    All Dossiers ({{ $detReviews->count() }})
                                </button>
                                <button type="button" class="dossier-tab-btn" :class="{ 'active': dossierTab === 'completed' }" @click="dossierTab = 'completed'">
                                    ✓ Submitted ({{ $completedCount }})
                                </button>
                                <button type="button" class="dossier-tab-btn" :class="{ 'active': dossierTab === 'pending' }" @click="dossierTab = 'pending'">
                                    ⏳ In Progress ({{ $pendingCount }})
                                </button>
                            </div>
                        @endif
                    </div>

                    @if($detReviews->isNotEmpty())
                        <div class="table-container" style="max-height: 320px; overflow-y: auto;">
                            <table class="table" style="font-size: 0.82rem;">
                                <thead>
                                    <tr>
                                        <th>Applicant Name & Tracking</th>
                                        <th>Position / Campaign</th>
                                        <th>Assigned / Due</th>
                                        <th>Status</th>
                                        <th>Score & Verdict</th>
                                        <th style="text-align: right;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($detReviews as $revAssignment)
                                        <tr x-show="dossierTab === 'all' || (dossierTab === 'completed' && '{{ $revAssignment->status }}' === 'SUBMITTED') || (dossierTab === 'pending' && '{{ $revAssignment->status }}' !== 'SUBMITTED')">
                                            <td>
                                                <strong style="color: var(--color-text-primary); font-size: 0.88rem;">
                                                    {{ $revAssignment->application?->user?->basicInfo?->full_name ?: ($revAssignment->application?->user?->name ?: 'Applicant') }}
                                                </strong>
                                                <div style="font-size: 0.75rem; color: var(--color-text-muted); font-family: 'JetBrains Mono', monospace;">
                                                    {{ $revAssignment->application?->tracking_number }}
                                                </div>
                                            </td>
                                            <td>
                                                <span style="font-weight: 600; color: var(--color-text-primary);">
                                                    {{ $revAssignment->application?->campaign?->title ?? 'N/A' }}
                                                </span>
                                                <div style="font-size: 0.74rem; color: var(--color-text-muted);">
                                                    {{ $revAssignment->application?->campaign?->department ?? '' }}
                                                </div>
                                            </td>
                                            <td>
                                                <span style="font-size: 0.76rem; color: var(--color-text-secondary);">
                                                    {{ $revAssignment->assigned_at?->format('d M Y') }}
                                                </span>
                                                <div style="font-size: 0.72rem; color: var(--color-text-muted);">
                                                    Due: {{ $revAssignment->due_date?->format('d M Y') ?: '14 days' }}
                                                </div>
                                            </td>
                                            <td>
                                                @if($revAssignment->status === 'SUBMITTED')
                                                    <span class="badge" style="background: rgba(16,185,129,0.15); color: #10b981; font-weight: 800; font-size: 0.72rem;">✓ Submitted</span>
                                                    @if($revAssignment->submitted_at)
                                                        <div style="font-size: 0.7rem; color: var(--color-text-muted); margin-top: 2px;">
                                                            {{ $revAssignment->submitted_at->format('d M Y') }}
                                                        </div>
                                                    @endif
                                                @else
                                                    <span class="badge" style="background: rgba(245,158,11,0.15); color: #f59e0b; font-weight: 800; font-size: 0.72rem;">⏳ In Progress</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($revAssignment->status === 'SUBMITTED')
                                                    <strong style="color: {{ $revAssignment->overall_score >= 80 ? '#10b981' : ($revAssignment->overall_score >= 60 ? '#f59e0b' : '#ef4444') }}; font-size: 0.95rem; font-weight: 800;">
                                                        {{ $revAssignment->overall_score }} / 100
                                                    </strong>
                                                    <div style="font-size: 0.72rem; color: var(--color-text-muted); margin-top: 1px;">
                                                        {{ str_replace('_', ' ', $revAssignment->recommendation ?? 'Completed') }}
                                                    </div>
                                                @else
                                                    <span style="color: var(--color-text-muted); font-size: 0.76rem;">Pending</span>
                                                @endif
                                            </td>
                                            <td style="text-align: right;">
                                                <div style="display: inline-flex; align-items: center; justify-content: flex-end; gap: 0.4rem;">
                                                    @if($revAssignment->status === 'SUBMITTED')
                                                        <a href="{{ route('peer-reports.print', $revAssignment->application_id) }}" 
                                                           target="_blank" 
                                                           class="action-btn action-btn-view" 
                                                           data-tooltip="View Consensus Peer Report" 
                                                           aria-label="View Peer Report">
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                                <circle cx="12" cy="12" r="3"/>
                                                            </svg>
                                                        </a>
                                                    @else
                                                        @if(auth()->user()->can('assign-reviewer') || auth()->user()->can('manage-reviewers'))
                                                            <button type="button" 
                                                                    class="action-btn" 
                                                                    style="color: var(--secondary);"
                                                                    wire:click="sendReviewerReminder({{ $revAssignment->id }})" 
                                                                    wire:loading.attr="disabled"
                                                                    data-tooltip="Send Official Reminder Email" 
                                                                    aria-label="Send Reminder">
                                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                                                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                                                                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                                                                </svg>
                                                            </button>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div style="text-align: center; padding: 2rem 1rem; background: var(--bg-card); border: 1.5px dashed var(--border-color); border-radius: var(--radius-md); color: var(--color-text-muted); font-size: 0.85rem;">
                            No candidacies or dossiers have been assigned to this expert referee yet.
                        </div>
                    @endif
                </div>

                <!-- Modal Actions Footer -->
                <div class="flex justify-between align-center" style="border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                    <button type="button" class="btn btn-secondary" wire:click="closeDetailsModal">Close</button>
                    @if(auth()->user()->can('edit-reviewer') || auth()->user()->can('manage-reviewers'))
                        <button type="button" class="btn btn-primary" wire:click="openEditModal({{ $selectedReviewerForDetails->id }})" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            <span>Edit Reviewer Particulars</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL 6: EDIT REVIEWER PROFILE                                -->
    <!-- ============================================================== -->
    @if($showEditModal)
        <div class="modal-overlay-premium" style="z-index: 1750;">
            <div class="modal-dialog-premium" style="width: 100%; max-width: 720px; max-height: 92vh; overflow-y: auto; padding: 1.75rem; border-color: var(--secondary-glow); box-shadow: 0 30px 80px rgba(0,0,0,0.9);">
                <div class="flex justify-between align-center mb-4" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div class="brand-icon-box" style="width: 40px; height: 40px; border-radius: 10px; background: rgba(6,182,212,0.15); color: var(--secondary);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="card-title" style="margin: 0; font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary);">
                                Edit Reviewer Profile
                            </h3>
                            <p class="card-subtitle" style="margin: 0.2rem 0 0; font-size: 0.82rem;">
                                Update expert particulars, discipline specialization, and institutional contact details.
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="closeEditModal" style="padding: 0.35rem 0.65rem;">✕</button>
                </div>

                <form wire:submit.prevent="updateReviewer">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <x-input label="Full Name & Academic Title *" :error="$errors->first('edit_name')">
                            <input type="text" class="form-control" wire:model="edit_name" placeholder="e.g. Prof. Dr. Tariq Mahmood">
                        </x-input>

                        <x-input label="Institutional Email Address *" :error="$errors->first('edit_email')">
                            <input type="email" class="form-control" wire:model="edit_email" placeholder="e.g. tariq.mahmood@nust.edu.pk">
                        </x-input>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <x-input label="Subject / Academic Discipline *" :error="$errors->first('edit_subject')">
                            <input type="text" class="form-control" wire:model="edit_subject" placeholder="e.g. Computer Science, Biotechnology">
                        </x-input>

                        <x-select label="Current Academic Rank / Position *" :error="$errors->first('edit_academic_rank')">
                            <select class="form-control form-select" wire:model="edit_academic_rank">
                                <option value="Professor">Professor</option>
                                <option value="Associate Professor">Associate Professor</option>
                                <option value="Assistant Professor">Assistant Professor</option>
                                <option value="Tenured Professor">Tenured Professor</option>
                                <option value="Distinguished Professor">Distinguished Professor</option>
                                <option value="Senior Scientist">Senior Scientist / Principal Investigator</option>
                                <option value="Industry Fellow">Industry Fellow / Technical Advisor</option>
                                <option value="Principal Consultant">Principal Consultant / Lead Specialist</option>
                                <option value="Executive Director">Executive Director / Senior Director</option>
                            </select>
                        </x-select>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <x-input label="Institutional Affiliation / Organization / University *" :error="$errors->first('edit_university')">
                            <input type="text" class="form-control" wire:model="edit_university" placeholder="e.g. Oxford University, NUST">
                        </x-input>

                        <!-- Searchable Country Select -->
                        <x-searchable-country-select wire:model="edit_country" label="Affiliated Country *" :error="$errors->first('edit_country')" placeholder="Search or select country..." />
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <x-input label="Contact Number (Optional)" :error="$errors->first('edit_phone')">
                            <input type="text" class="form-control" wire:model="edit_phone" placeholder="e.g. +92 300 1234567">
                        </x-input>

                        <x-input label="Specialization / Sub-discipline (Optional)" :error="$errors->first('edit_specialization')">
                            <input type="text" class="form-control" wire:model="edit_specialization" placeholder="e.g. Artificial Intelligence, NLP, Cyber Security">
                        </x-input>
                    </div>

                    <div class="flex justify-between align-center" style="border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                        <button type="button" class="btn btn-secondary" wire:click="closeEditModal">Cancel</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="updateReviewer">Save Changes</span>
                            <span wire:loading wire:target="updateReviewer">Saving Changes...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Universal Reviewers Batch Import Modal -->
    <x-entity-import-modal 
        entity-name="Empanelled Reviewers"
        sample-columns="Full Name, Email, University / Institution, Academic Rank, Academic Discipline / Subject, Country, Specialization, Phone"
    />

    <!-- Administrative Password Reset Modal -->
    <x-password-reset-modal />
</div>
