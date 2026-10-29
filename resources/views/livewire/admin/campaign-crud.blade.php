<div>
    <!-- Header Banner -->
    <div class="card mb-6" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; position: relative; overflow: hidden;">
        <div style="position: absolute; right: -2rem; top: -2rem; width: 140px; height: 140px; background: radial-gradient(circle, var(--primary-glow) 0%, transparent 70%); pointer-events: none;"></div>
        <div>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.35rem;">
                <div class="brand-icon-box" style="width: 38px; height: 38px; border-radius: 10px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <line x1="19" y1="8" x2="19" y2="14"></line>
                        <line x1="22" y1="11" x2="16" y2="11"></line>
                    </svg>
                </div>
                <div>
                    <h2 class="card-title" style="margin: 0; font-size: 1.35rem;">Campaigns & Committee Desk</h2>
                    <p class="card-subtitle" style="margin: 0;">Configure recruitment tracks, dynamic scoring matrices, and allocate evaluation panels</p>
                </div>
            </div>
        </div>
        <div>
            @if(!$isFormOpen && $activeTab === 'campaigns')
                @can('create-campaign')
                    <button type="button" class="btn btn-primary" wire:click="openCreateForm" style="display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: var(--shadow-sm);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>Create Campaign</span>
                    </button>
                @endcan
            @endif
        </div>
    </div>

    <!-- Segmented Tab Navigation -->
    <div style="display: flex; gap: 0.5rem; background: var(--bg-tertiary); padding: 0.35rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color); width: fit-content; margin-bottom: 1.5rem;">
        @can('view-campaign')
            <button type="button" 
                    class="btn" 
                    wire:click="setTab('campaigns')"
                    style="padding: 0.5rem 1.25rem; font-size: 0.875rem; border-radius: var(--radius-md); font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; transition: var(--transition-base); {{ $activeTab === 'campaigns' ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                </svg>
                <span>Active Job Campaigns</span>
                <span style="font-size: 0.75rem; padding: 0.15rem 0.45rem; border-radius: 999px; background: rgba(99,102,241,0.15); color: var(--primary);">{{ count($campaigns) }}</span>
            </button>
        @endcan

        @can('view-committee')
            <button type="button" 
                    class="btn" 
                    wire:click="setTab('committees')"
                    style="padding: 0.5rem 1.25rem; font-size: 0.875rem; border-radius: var(--radius-md); font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; transition: var(--transition-base); {{ $activeTab === 'committees' ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>Committee Allocations</span>
                <span style="font-size: 0.75rem; padding: 0.15rem 0.45rem; border-radius: 999px; background: rgba(6,182,212,0.15); color: var(--secondary);">{{ count($assignments ?? []) }}</span>
            </button>
        @endcan
    </div>

    <!-- ========================================== -->
    <!-- TAB 1: ACTIVE CAMPAIGNS BREAD -->
    <!-- ========================================== -->
    @if ($activeTab === 'campaigns')
        @if($isFormOpen)
            <div class="card" style="border-color: var(--primary-glow); box-shadow: var(--shadow-md);">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <h3 class="card-title" style="margin: 0; font-size: 1.2rem; display: flex; align-items: center; gap: 0.5rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            <span>{{ $isEditMode ? 'Modify Job Campaign' : 'Create New Job Campaign' }}</span>
                        </h3>
                        <p class="card-subtitle" style="margin: 0;">Specify title, recruitment track, application processing fee, and scoring rules</p>
                    </div>
                    <button type="button" class="btn btn-secondary" wire:click="$set('isFormOpen', false)" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;">
                        Close Form
                    </button>
                </div>

                <form wire:submit.prevent="saveCampaign">
                    <div class="grid grid-cols-2 gap-4">
                        <x-input label="Job Code *" :error="$errors->first('job_code')">
                            <input type="text" class="form-control" wire:model="job_code" placeholder="e.g. CS-FAC-2026">
                        </x-input>
                        <x-input label="Job Title *" :error="$errors->first('title')">
                            <input type="text" class="form-control" wire:model="title" placeholder="e.g. Assistant Professor of Computer Science">
                        </x-input>
                    </div>

                    <div class="grid grid-cols-3 gap-4 mt-4">
                        <x-input label="Track Type *">
                            <select class="form-control form-select" wire:model.live="track_type">
                                <option value="ACADEMIC">Academic Faculty Track</option>
                                <option value="ADMINISTRATIVE">Administrative Officer Track</option>
                                <option value="SUPPORT">Support Staff Track</option>
                            </select>
                        </x-input>
                        <x-input label="Department / Faculty *" :error="$errors->first('department')">
                            <input type="text" class="form-control" wire:model="department" placeholder="e.g. Faculty of Engineering">
                        </x-input>
                        <x-input label="Application Fee (PKR) *" :error="$errors->first('application_fee')">
                            <input type="number" class="form-control" wire:model="application_fee" placeholder="3000">
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

                    <!-- Transcript Proof Requirement Configuration -->
                    <div style="border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1rem 1.25rem; background: var(--bg-tertiary); margin-top: 1.25rem; display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(99,102,241,0.1); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                            </div>
                            <div>
                                <strong style="color: var(--color-text-primary); font-size: 0.95rem; display: block;">Academic Transcript Proof Requirement</strong>
                                <small style="color: var(--color-text-secondary);">Enforce mandatory document uploads during candidate profile completion</small>
                            </div>
                        </div>
                        <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer; font-weight: 600; color: var(--color-text-primary); background: var(--bg-card); padding: 0.4rem 0.8rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                            <input type="checkbox" wire:model="require_transcript_proof" style="width: 1.15rem; height: 1.15rem; accent-color: var(--primary);">
                            <span style="font-size: 0.85rem;">{{ $require_transcript_proof ? 'Mandatory Proofs Required' : 'Optional Uploads' }}</span>
                        </label>
                    </div>

                    <!-- DYNAMIC RULE MAPPER SECTION -->
                    @if($track_type === 'ACADEMIC')
                        <div style="border: 1px solid var(--secondary-glow); border-radius: var(--radius-md); padding: 1.5rem; background: rgba(6,182,212,0.03); margin-top: 1.5rem;">
                            <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(6,182,212,0.15); display: flex; align-items: center; justify-content: center; color: var(--secondary);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="3"></circle>
                                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 style="color: var(--secondary); font-size: 1.05rem; font-weight: 700; margin: 0;">
                                        Dynamic Rule Mapper — Academic Merit Weighting Matrix
                                    </h4>
                                    <small style="color: var(--color-text-secondary);">Calculated automatically during pre-selection candidate dossier evaluation</small>
                                </div>
                            </div>

                            <p style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; color: var(--color-text-muted); margin-bottom: 0.5rem;">Academic Distinctions</p>
                            <div class="grid grid-cols-3 gap-4 mb-4">
                                <x-input label="Gold Medal Score">
                                    <input type="number" step="0.1" class="form-control" wire:model="gold_medal_weight">
                                </x-input>
                                <x-input label="Silver Medal Score">
                                    <input type="number" step="0.1" class="form-control" wire:model="silver_medal_weight">
                                </x-input>
                                <x-input label="Bronze Medal Score">
                                    <input type="number" step="0.1" class="form-control" wire:model="bronze_medal_weight">
                                </x-input>
                            </div>

                            <p style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; color: var(--color-text-muted); margin-bottom: 0.5rem;">HJRS Journal Publications</p>
                            <div class="grid grid-cols-4 gap-4 mb-4">
                                <x-input label="HJRS W-Category">
                                    <input type="number" step="0.05" class="form-control" wire:model="w_category_weight">
                                </x-input>
                                <x-input label="HJRS X-Category">
                                    <input type="number" step="0.05" class="form-control" wire:model="x_category_weight">
                                </x-input>
                                <x-input label="HJRS Y-Category">
                                    <input type="number" step="0.05" class="form-control" wire:model="y_category_weight">
                                </x-input>
                                <x-input label="HJRS Z-Category">
                                    <input type="number" step="0.05" class="form-control" wire:model="z_category_weight">
                                </x-input>
                            </div>

                            <p style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; color: var(--color-text-muted); margin-bottom: 0.5rem;">Conferences & Scientific Events</p>
                            <div class="grid grid-cols-4 gap-4 mb-4">
                                <x-input label="Lead Organizer">
                                    <input type="number" step="0.1" class="form-control" wire:model="organizer_leader_weight">
                                </x-input>
                                <x-input label="Assoc. Organizer">
                                    <input type="number" step="0.1" class="form-control" wire:model="organizer_associate_weight">
                                </x-input>
                                <x-input label="Intl. Presenter">
                                    <input type="number" step="0.1" class="form-control" wire:model="paper_international_weight">
                                </x-input>
                                <x-input label="Natl. Presenter">
                                    <input type="number" step="0.1" class="form-control" wire:model="paper_national_weight">
                                </x-input>
                            </div>

                            <p style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; color: var(--color-text-muted); margin-bottom: 0.5rem;">Funded Grants & Patents</p>
                            <div class="grid grid-cols-4 gap-4">
                                <x-input label="Grant Rate (Marks/M)">
                                    <input type="number" step="0.1" class="form-control" wire:model="project_million_rate">
                                </x-input>
                                <x-input label="Grant Cap Max">
                                    <input type="number" step="0.1" class="form-control" wire:model="project_max_marks">
                                </x-input>
                                <x-input label="Patent Granted">
                                    <input type="number" step="0.1" class="form-control" wire:model="patent_granted_weight">
                                </x-input>
                                <x-input label="Patent Licensed">
                                    <input type="number" step="0.1" class="form-control" wire:model="patent_licensed_weight">
                                </x-input>
                            </div>
                        </div>
                    @endif

                    <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                        <button type="submit" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                <polyline points="7 3 7 8 15 8"></polyline>
                            </svg>
                            <span>Save Campaign</span>
                        </button>
                        <button type="button" class="btn btn-secondary" wire:click="$set('isFormOpen', false)">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        @else
            <!-- Campaigns List Table -->
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Job Code</th>
                            <th>Title & Department</th>
                            <th>Recruitment Track</th>
                            <th>Closing Date</th>
                            <th>Application Fee</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($campaigns as $camp)
                            <tr>
                                <td>
                                    <code style="background: rgba(99,102,241,0.1); color: var(--primary); padding: 0.25rem 0.6rem; border-radius: 6px; font-weight: 700; border: 1px solid rgba(99,102,241,0.2);">
                                        {{ $camp->job_code }}
                                    </code>
                                </td>
                                <td>
                                    <div style="font-weight: 700; color: var(--color-text-primary);">{{ $camp->title }}</div>
                                    <small style="color: var(--color-text-secondary); display: flex; align-items: center; gap: 0.25rem; margin-top: 0.15rem;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                        </svg>
                                        {{ $camp->department }}
                                    </small>
                                </td>
                                <td>
                                    @if($camp->track_type === 'ACADEMIC')
                                        <span class="badge" style="background: rgba(6,182,212,0.12); color: var(--secondary); border: 1px solid rgba(6,182,212,0.25);">
                                            <span class="badge-dot" style="background: var(--secondary);"></span>
                                            Academic Faculty
                                        </span>
                                    @elseif($camp->track_type === 'ADMINISTRATIVE')
                                        <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary); border: 1px solid rgba(99,102,241,0.25);">
                                            <span class="badge-dot" style="background: var(--primary);"></span>
                                            Admin Officer
                                        </span>
                                    @else
                                        <span class="badge" style="background: rgba(148,163,184,0.12); color: var(--color-text-secondary); border: 1px solid rgba(148,163,184,0.25);">
                                            <span class="badge-dot" style="background: var(--color-text-muted);"></span>
                                            Support Staff
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.35rem; font-size: 0.85rem; color: var(--color-text-secondary);">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                        <span>{{ $camp->closing_date?->format('Y-m-d') }}</span>
                                    </div>
                                </td>
                                <td>
                                    <strong style="color: var(--color-text-primary); font-family: monospace;">PKR {{ number_format($camp->application_fee, 2) }}</strong>
                                </td>
                                <td style="text-align: right;">
                                    <div class="action-btn-group justify-end">
                                        @can('tune-constitution')
                                            @php
                                                $criteriaUrl = (auth()->user()->hasRole('registrar') && !auth()->user()->hasRole('super-admin')) ? '/registrar/criteria' : '/admin/criteria';
                                            @endphp
                                            <a href="{{ $criteriaUrl }}?campaign_id={{ $camp->id }}" 
                                               class="action-btn action-btn-view" 
                                               data-tooltip="Manage Statutory Criteria & Quotas"
                                               aria-label="Criteria Desk">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                                                    <path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                                                    <path d="M7 21h10"/>
                                                    <path d="M12 3v18"/>
                                                </svg>
                                            </a>
                                        @endcan

                                        @can('edit-campaign')
                                            <button type="button" 
                                                    class="action-btn action-btn-edit" 
                                                    wire:click="openEditForm({{ $camp->id }})"
                                                    data-tooltip="Edit Campaign Particulars"
                                                    aria-label="Edit Campaign">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                            </button>
                                        @endcan

                                        @can('delete-campaign')
                                            <button type="button" 
                                                    class="action-btn action-btn-delete" 
                                                    wire:click="deleteCampaign({{ $camp->id }})" 
                                                    onclick="confirm('Are you sure you want to delete this campaign? This will cascade delete applications and committees!') || event.stopImmediatePropagation()"
                                                    data-tooltip="Delete Campaign"
                                                    aria-label="Delete Campaign">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                                </svg>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center" style="padding: 3rem 1rem;">
                                    <div style="color: var(--color-text-muted);">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 0.5rem auto;">
                                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                        </svg>
                                        <p style="font-size: 0.95rem; margin: 0;">No recruitment campaigns found. Click "Create Campaign" to begin.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

    <!-- ========================================== -->
    <!-- TAB 2: COMMITTEE ALLOCATIONS BREAD -->
    <!-- ========================================== -->
    @elseif ($activeTab === 'committees')
        @php
            $currentCampaign = $campaigns->firstWhere('id', $selected_campaign_id);
            $campaignMetrics = $currentCampaign ? $currentCampaign->getCommitteeMetrics() : null;
            $campaignAssignments = $assignments->where('campaign_id', $selected_campaign_id);
        @endphp

        <!-- 1. STATUTORY CONSTITUTION & QUOTA TRACKER BANNER -->
        <div class="constitution-banner mb-6">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1.5rem; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 280px;">
                    <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.5rem;">
                        <span class="badge" style="background: rgba(99, 102, 241, 0.18); color: var(--primary); border: 1px solid rgba(99, 102, 241, 0.3); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.04em;">
                            Operational Governance
                        </span>
                        @if($currentCampaign)
                            <span class="badge" style="background: rgba(6, 182, 212, 0.15); color: var(--secondary); border: 1px solid rgba(6, 182, 212, 0.3);">
                                {{ $currentCampaign->track_type }}
                            </span>
                        @endif
                    </div>
                    <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--color-text-primary); margin: 0 0 0.4rem 0;">
                        Statutory Committee Allocation & Quota Engine
                    </h2>
                    <p style="font-size: 0.88rem; color: var(--color-text-secondary); margin: 0; line-height: 1.5;">
                        Empanel balanced evaluation bodies across internal leadership, external experts from partner institutions/offices, and international specialists under statutory criteria.
                    </p>
                </div>

                <!-- Campaign Selector & Constitution Config -->
                <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                    <div style="min-width: 240px;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--color-text-secondary); margin-bottom: 0.25rem;">
                            TARGET JOB CAMPAIGN
                        </label>
                        <select class="form-control form-select" wire:model.live="selected_campaign_id" style="font-weight: 600;">
                            @foreach($campaigns as $c)
                                <option value="{{ $c->id }}">{{ $c->title }} ({{ $c->job_code }})</option>
                            @endforeach
                        </select>
                    </div>

                    @if($currentCampaign)
                        @can('tune-constitution')
                            <div style="align-self: flex-end; display: flex; gap: 0.5rem;">
                                @php
                                    $criteriaUrl = (auth()->user()->hasRole('registrar') && !auth()->user()->hasRole('super-admin')) ? '/registrar/criteria' : '/admin/criteria';
                                @endphp
                                <a href="{{ $criteriaUrl }}?campaign_id={{ $selected_campaign_id }}" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.45rem; padding: 0.65rem 1rem; font-size: 0.84rem;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                                        <path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                                        <path d="M7 21h10"/>
                                        <path d="M12 3v18"/>
                                        <path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"/>
                                    </svg>
                                    <span>Criteria Desk ↗</span>
                                </a>
                                <button type="button" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.45rem; padding: 0.65rem 1rem; font-size: 0.84rem;" wire:click="openConstitutionModal">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="3"></circle>
                                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                    </svg>
                                    <span>Tune Modal</span>
                                </button>
                            </div>
                        @endcan
                    @endif
                </div>
            </div>

            @if($campaignMetrics)
                <!-- Live Quota Status & Statutory Quorum Tracker -->
                <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid rgba(255, 255, 255, 0.08);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem; flex-wrap: wrap; gap: 0.75rem;">
                        <div>
                            <span style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: var(--color-text-muted); letter-spacing: 0.05em;">
                                ACTIVE CONSTITUTION SPECIFICATION
                            </span>
                            <div style="font-size: 0.95rem; font-weight: 700; color: var(--color-text-primary); margin-top: 0.15rem;">
                                {{ $campaignMetrics['constitution']['title'] ?? 'Statutory Operational Committee' }}
                            </div>
                        </div>

                        <!-- Overall Quorum Badge -->
                        @if($campaignMetrics['is_complete'])
                            <span class="badge" style="background: rgba(16, 185, 129, 0.2); color: var(--success); border: 1px solid rgba(16, 185, 129, 0.4); padding: 0.4rem 0.8rem; font-size: 0.82rem; font-weight: 700;">
                                <span class="badge-dot" style="background: var(--success);"></span>
                                ✓ Statutory Quorum Satisfied ({{ $campaignMetrics['total_assigned'] }} / {{ $campaignMetrics['required_total'] }} Members Assigned)
                            </span>
                        @elseif($campaignMetrics['is_over_allocated'])
                            <span class="badge" style="background: rgba(239, 68, 68, 0.2); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.4); padding: 0.4rem 0.8rem; font-size: 0.82rem; font-weight: 700;">
                                ⚠️ Quota Exceeded ({{ $campaignMetrics['total_assigned'] }} Assigned vs {{ $campaignMetrics['required_total'] }} Prescribed)
                            </span>
                        @else
                            <span class="badge" style="background: rgba(245, 158, 11, 0.2); color: var(--warning); border: 1px solid rgba(245, 158, 11, 0.4); padding: 0.4rem 0.8rem; font-size: 0.82rem; font-weight: 700;">
                                <span class="badge-dot" style="background: var(--warning);"></span>
                                Quorum Incomplete: {{ $campaignMetrics['total_assigned'] }} of {{ $campaignMetrics['required_total'] }} Members Empanelled ({{ $campaignMetrics['required_total'] - $campaignMetrics['total_assigned'] }} Slot{{ ($campaignMetrics['required_total'] - $campaignMetrics['total_assigned']) > 1 ? 's' : '' }} Remaining)
                            </span>
                        @endif
                    </div>

                    <!-- Category Breakdown Chips -->
                    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                        <!-- Internal Quota -->
                        @php $catInternal = $campaignMetrics['categories']['INTERNAL']; @endphp
                        <div class="quota-pill {{ $catInternal['satisfied'] ? 'satisfied' : 'pending' }}">
                            <span class="member-category-badge internal" style="font-size: 0.68rem; padding: 0.15rem 0.45rem;">
                                🏛️ Internal Staff
                            </span>
                            <span>{{ $catInternal['assigned'] }} / {{ $catInternal['required'] }} Assigned</span>
                            @if($catInternal['satisfied'])
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="color: var(--success);"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            @else
                                <span style="font-size: 0.72rem; opacity: 0.85;">(Need {{ $catInternal['available'] }})</span>
                            @endif
                        </div>

                        <!-- External Quota -->
                        @php $catExternal = $campaignMetrics['categories']['EXTERNAL']; @endphp
                        <div class="quota-pill {{ $catExternal['satisfied'] ? 'satisfied' : 'pending' }}">
                            <span class="member-category-badge external" style="font-size: 0.68rem; padding: 0.15rem 0.45rem;">
                                🌐 External Experts
                            </span>
                            <span>{{ $catExternal['assigned'] }} / {{ $catExternal['required'] }} Assigned</span>
                            @if($catExternal['satisfied'])
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="color: var(--success);"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            @else
                                <span style="font-size: 0.72rem; opacity: 0.85;">(Need {{ $catExternal['available'] }})</span>
                            @endif
                        </div>

                        <!-- International Quota -->
                        @php $catIntl = $campaignMetrics['categories']['INTERNATIONAL']; @endphp
                        <div class="quota-pill {{ $catIntl['satisfied'] ? 'satisfied' : 'pending' }}">
                            <span class="member-category-badge international" style="font-size: 0.68rem; padding: 0.15rem 0.45rem;">
                                ✈️ International Specialists
                            </span>
                            <span>{{ $catIntl['assigned'] }} / {{ $catIntl['required'] }} Assigned</span>
                            @if($catIntl['satisfied'])
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="color: var(--success);"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            @else
                                <span style="font-size: 0.72rem; opacity: 0.85;">(Need {{ $catIntl['available'] }})</span>
                            @endif
                        </div>

                        <!-- Independent Quota (if configured) -->
                        @php $catInd = $campaignMetrics['categories']['INDEPENDENT']; @endphp
                        @if($catInd['required'] > 0 || $catInd['assigned'] > 0)
                            <div class="quota-pill {{ $catInd['satisfied'] ? 'satisfied' : 'pending' }}">
                                <span class="member-category-badge independent" style="font-size: 0.68rem; padding: 0.15rem 0.45rem;">
                                    ⚖️ Independent Arbiter
                                </span>
                                <span>{{ $catInd['assigned'] }} / {{ $catInd['required'] }} Assigned</span>
                                @if($catInd['satisfied'])
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="color: var(--success);"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- 2. FORMAL ALLOCATION PANEL (IF CANDIDATE IS SELECTED) -->
        @if($selected_user_id)
            @can('assign-committee')
                @php
                    $selectedCand = collect($committee_users)->firstWhere('id', $selected_user_id);
                @endphp
                <div class="card mb-6" style="border-color: var(--primary); background: linear-gradient(135deg, rgba(15, 23, 42, 0.98), rgba(30, 41, 59, 0.95)); box-shadow: 0 12px 32px -6px var(--primary-glow);">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(99, 102, 241, 0.2); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <line x1="19" y1="8" x2="19" y2="14"></line>
                                <line x1="22" y1="11" x2="16" y2="11"></line>
                            </svg>
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: var(--color-text-primary);">
                                Empanel Selected Member: {{ $selectedCand['name'] ?? 'Candidate' }}
                            </h3>
                            <p style="margin: 0; font-size: 0.85rem; color: var(--color-text-secondary);">
                                {{ $selectedCand['email'] ?? '' }} &bull; Affiliation: <strong>{{ $selectedCand['affiliation'] ?? 'N/A' }}</strong> ({{ $selectedCand['country'] ?? 'Domestic' }})
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;" wire:click="clearSelectedCandidate">
                        ✕ Cancel Selection
                    </button>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger mb-4" style="padding: 0.75rem 1rem; border-radius: var(--radius-sm); font-size: 0.88rem;">
                        <ul style="margin: 0; padding-left: 1.25rem;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form wire:submit.prevent="assignCommitteeMember">
                    <div class="grid grid-cols-4 gap-6">
                        <!-- Committee Panel Type -->
                        <x-input label="Committee Panel Type *" :error="$errors->first('is_appellate_panel')">
                            <select class="form-control form-select" wire:model.live="is_appellate_panel">
                                <option value="0">Scrutiny / Selection Committee (Operational)</option>
                                <option value="1">Appellate Panel (Grievance Review)</option>
                            </select>
                        </x-input>

                        <!-- Statutory Member Category -->
                        <x-input label="Statutory Quota Category *" :error="$errors->first('member_category')">
                            <select class="form-control form-select" wire:model.live="member_category">
                                <option value="INTERNAL">🏛️ Internal Staff & Faculty (Home Org/Campus)</option>
                                <option value="EXTERNAL">🌐 External Expert (Other University/Office)</option>
                                <option value="INTERNATIONAL">✈️ International Specialist (Overseas/Country)</option>
                                <option value="INDEPENDENT">⚖️ Independent Arbiter / Legal Observer</option>
                            </select>
                        </x-input>

                        <!-- Role / Capacity -->
                        <x-input label="Statutory Role / Capacity *" :error="$errors->first('role_designation')">
                            <input type="text" class="form-control" wire:model="role_designation" placeholder="e.g. Convener, Subject Expert, Dean">
                        </x-input>

                        <!-- Affiliation / Office -->
                        <x-input label="Organization / Office / Institution" :error="$errors->first('affiliation')">
                            <input type="text" class="form-control" wire:model="affiliation" placeholder="e.g. LUMS, MIT, Main Campus">
                        </x-input>
                    </div>

                    <div style="margin-top: 1.25rem; display: flex; justify-content: space-between; align-items: center;">
                        <div style="font-size: 0.82rem; color: var(--color-text-muted);">
                            Quota slot will be registered under category: <strong style="color: var(--secondary);">{{ $member_category }}</strong> for <strong style="color: var(--color-text-primary);">{{ $currentCampaign?->title }}</strong>
                        </div>
                        <button type="submit" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.4rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <line x1="19" y1="8" x2="19" y2="14"></line>
                                <line x1="22" y1="11" x2="16" y2="11"></line>
                            </svg>
                            <span style="font-weight: 700;">Establish Statutory Assignment</span>
                        </button>
                    </div>
                </form>
            </div>
            @endcan
        @endif

        <!-- 3. EMPANELLED EMPLOYEE & EXPERT CANDIDATE POOL (MULTI-SECTION & SEARCH) -->
        <div class="card mb-6" style="border-color: var(--border-color); box-shadow: var(--shadow-sm);">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.85rem;">
                <div>
                    <h3 class="card-title" style="margin: 0; font-size: 1.15rem; display: flex; align-items: center; gap: 0.5rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <span>Empanelled Member Pool (Cross-Institutional & Multi-Section)</span>
                    </h3>
                    <p class="card-subtitle" style="margin: 0.15rem 0 0 0;">
                        Select internal faculty/officers, external institutional delegates, or overseas subject specialists to fulfill campaign constitution.
                    </p>
                </div>

                <!-- Search Input -->
                <div style="position: relative; min-width: 280px;">
                    <input type="text" class="form-control" placeholder="Search by name, institution, country, field..." 
                           wire:model.live.debounce.250ms="candidateSearch"
                           style="padding-left: 2.2rem; font-size: 0.85rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--color-text-muted);">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>
            </div>

            <!-- Dynamic Section Tabs / Filters -->
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1.25rem;">
                <button type="button" class="btn {{ $candidateFilterSection === 'ALL' ? 'btn-primary' : 'btn-secondary' }}" 
                        style="padding: 0.4rem 0.85rem; font-size: 0.82rem; border-radius: var(--radius-sm);"
                        wire:click="filterCandidateSection('ALL')">
                    All Personnel ({{ count($committee_users) }})
                </button>
                <button type="button" class="btn {{ $candidateFilterSection === 'INTERNAL' ? 'btn-primary' : 'btn-secondary' }}" 
                        style="padding: 0.4rem 0.85rem; font-size: 0.82rem; border-radius: var(--radius-sm);"
                        wire:click="filterCandidateSection('INTERNAL')">
                    🏛️ Internal Staff & Faculty ({{ count(array_filter($committee_users, fn($c) => $c['category'] === 'INTERNAL')) }})
                </button>
                <button type="button" class="btn {{ $candidateFilterSection === 'EXTERNAL' ? 'btn-primary' : 'btn-secondary' }}" 
                        style="padding: 0.4rem 0.85rem; font-size: 0.82rem; border-radius: var(--radius-sm);"
                        wire:click="filterCandidateSection('EXTERNAL')">
                    🌐 External Experts (Other Universities / Offices) ({{ count(array_filter($committee_users, fn($c) => $c['category'] === 'EXTERNAL')) }})
                </button>
                <button type="button" class="btn {{ $candidateFilterSection === 'INTERNATIONAL' ? 'btn-primary' : 'btn-secondary' }}" 
                        style="padding: 0.4rem 0.85rem; font-size: 0.82rem; border-radius: var(--radius-sm);"
                        wire:click="filterCandidateSection('INTERNATIONAL')">
                    ✈️ International Specialists ({{ count(array_filter($committee_users, fn($c) => $c['category'] === 'INTERNATIONAL')) }})
                </button>
                <button type="button" class="btn {{ $candidateFilterSection === 'INDEPENDENT' ? 'btn-primary' : 'btn-secondary' }}" 
                        style="padding: 0.4rem 0.85rem; font-size: 0.82rem; border-radius: var(--radius-sm);"
                        wire:click="filterCandidateSection('INDEPENDENT')">
                    ⚖️ Appellate Arbiters ({{ count(array_filter($committee_users, fn($c) => $c['category'] === 'INDEPENDENT')) }})
                </button>
            </div>

            <!-- Candidate Cards Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1rem;">
                @forelse($this->filteredCandidates as $cand)
                    @php
                        $assignedToCurrent = $campaignAssignments->firstWhere('user_id', $cand['id']);
                        $isSelected = ($selected_user_id === $cand['id']);
                    @endphp
                    <div class="candidate-pool-card {{ $isSelected ? 'selected' : '' }} {{ $assignedToCurrent ? 'assigned' : '' }}">
                        <div>
                            <!-- Header Badges -->
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                                @if($cand['category'] === 'INTERNAL')
                                    <span class="member-category-badge internal">🏛️ Internal Member</span>
                                @elseif($cand['category'] === 'EXTERNAL')
                                    <span class="member-category-badge external">🌐 External Expert</span>
                                @elseif($cand['category'] === 'INTERNATIONAL')
                                    <span class="member-category-badge international">✈️ International Specialist</span>
                                @else
                                    <span class="member-category-badge independent">⚖️ Appellate Arbiter</span>
                                @endif

                                <span class="country-pill">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="2" y1="12" x2="22" y2="12"></line>
                                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                                    </svg>
                                    <span>{{ $cand['country'] }}</span>
                                </span>
                            </div>

                            <!-- User Info -->
                            <div style="display: flex; align-items: flex-start; gap: 0.75rem; margin-bottom: 0.75rem;">
                                <div style="width: 38px; height: 38px; border-radius: 50%; background: rgba(99, 102, 241, 0.15); color: var(--primary); font-weight: 800; font-size: 0.95rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    {{ strtoupper(substr($cand['name'], 0, 1)) }}
                                </div>
                                <div style="overflow: hidden;">
                                    <h4 style="margin: 0; font-size: 0.98rem; font-weight: 700; color: var(--color-text-primary); text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                        {{ $cand['name'] }}
                                    </h4>
                                    <div style="font-size: 0.78rem; color: var(--color-text-muted); text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                        {{ $cand['email'] }}
                                    </div>
                                    <div style="font-size: 0.8rem; font-weight: 600; color: var(--secondary); margin-top: 0.2rem;">
                                        {{ $cand['rank'] }}
                                    </div>
                                </div>
                            </div>

                            <!-- Affiliation & Subject -->
                            <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.5rem 0.65rem; margin-bottom: 0.75rem; font-size: 0.78rem;">
                                <div style="display: flex; align-items: center; gap: 0.35rem; color: var(--color-text-secondary); margin-bottom: 0.25rem;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--color-text-muted);">
                                        <rect x="2" y="2" width="20" height="20" rx="2" ry="2"></rect>
                                        <path d="M7 2v20"></path>
                                        <path d="M17 2v20"></path>
                                        <path d="M2 12h20"></path>
                                        <path d="M2 7h5"></path>
                                        <path d="M2 17h5"></path>
                                        <path d="M17 17h5"></path>
                                        <path d="M17 7h5"></path>
                                    </svg>
                                    <span style="font-weight: 600; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">{{ $cand['affiliation'] }}</span>
                                </div>
                                <div style="color: var(--color-text-muted); text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                    Field / Domain: <strong style="color: var(--color-text-secondary);">{{ $cand['subject'] }}</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer / Actions -->
                        <div style="border-top: 1px solid var(--border-color); padding-top: 0.65rem; margin-top: 0.25rem; display: flex; justify-content: space-between; align-items: center;">
                            @if($assignedToCurrent)
                                <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: var(--success); border: 1px solid rgba(16, 185, 129, 0.3); font-size: 0.75rem;">
                                    ✓ Assigned ({{ $assignedToCurrent->is_appellate_panel ? 'Appellate' : 'Scrutiny' }})
                                </span>
                                <span style="font-size: 0.72rem; color: var(--color-text-muted);">
                                    {{ $assignedToCurrent->role_designation ?: 'Member' }}
                                </span>
                            @elseif($isSelected)
                                <span class="badge" style="background: rgba(99, 102, 241, 0.2); color: var(--primary); border: 1px solid var(--primary); font-size: 0.75rem; font-weight: 700;">
                                    ● Currently Selected
                                </span>
                                <button type="button" class="btn btn-secondary" style="padding: 0.25rem 0.6rem; font-size: 0.75rem;" wire:click="clearSelectedCandidate">
                                    Deselect
                                </button>
                            @else
                                @can('assign-committee')
                                    <button type="button" class="btn btn-secondary" style="padding: 0.35rem 0.85rem; font-size: 0.8rem; width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 0.35rem;" wire:click="selectCandidate({{ $cand['id'] }})">
                                        <span>Select Candidate</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                        </svg>
                                    </button>
                                @else
                                    <span style="font-size: 0.75rem; color: var(--color-text-muted); font-style: italic;">Empanelled Member</span>
                                @endcan
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; padding: 3rem 1rem; text-align: center; color: var(--color-text-muted);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 0.5rem auto;">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <p style="margin: 0; font-size: 0.95rem;">No empanelled candidates found matching your filter criteria.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 4. ACTIVE COMMITTEE ALLOCATIONS LEDGER -->
        <div class="card" style="border-color: var(--border-color); padding: 0; overflow: hidden;">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <h3 class="card-title" style="margin: 0; font-size: 1.1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    <span>Active Committee Allocations Ledger</span>
                </h3>
                <span class="badge" style="background: var(--bg-tertiary); color: var(--color-text-secondary); border: 1px solid var(--border-color);">
                    {{ count($assignments ?? []) }} Total Active Assignments
                </span>
            </div>

            <div class="table-container" style="border: none; border-radius: 0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Job Campaign</th>
                            <th>Committee Member</th>
                            <th>Affiliation & Country</th>
                            <th>Statutory Category</th>
                            <th>Assigned Capacity & Panel</th>
                            <th>Assigned Date</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $asm)
                            @php
                                $isThisCampaign = ($selected_campaign_id && $asm->campaign_id == $selected_campaign_id);
                                $memName = $asm->user->reviewerProfile?->name 
                                    ?? ($asm->user->basicInfo?->first_name ? ($asm->user->basicInfo->first_name . ' ' . $asm->user->basicInfo->last_name) : $asm->user->email);
                            @endphp
                            <tr style="{{ $isThisCampaign ? 'background: rgba(99, 102, 241, 0.04);' : '' }}">
                                <td>
                                    <strong>{{ $asm->campaign->title }}</strong>
                                    <div style="margin-top: 0.2rem;">
                                        <code style="color: var(--secondary); font-size: 0.8rem; background: rgba(6,182,212,0.1); padding: 0.15rem 0.4rem; border-radius: 4px;">{{ $asm->campaign->job_code }}</code>
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.6rem;">
                                        <div style="width: 32px; height: 32px; border-radius: 50%; background: rgba(99,102,241,0.15); color: var(--primary); font-weight: 700; font-size: 0.8rem; display: flex; align-items: center; justify-content: center;">
                                            {{ strtoupper(substr($memName, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong style="color: var(--color-text-primary);">{{ $memName }}</strong>
                                            <div style="font-size: 0.78rem; color: var(--color-text-muted);">{{ $asm->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div><strong>{{ $asm->affiliation ?: 'Home Organization' }}</strong></div>
                                    <div style="margin-top: 0.15rem;">
                                        <span class="country-pill" style="font-size: 0.7rem;">
                                            {{ $asm->country ?: 'Domestic / Local' }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    @if($asm->member_category === 'INTERNAL')
                                        <span class="member-category-badge internal">🏛️ Internal</span>
                                    @elseif($asm->member_category === 'EXTERNAL')
                                        <span class="member-category-badge external">🌐 External Expert</span>
                                    @elseif($asm->member_category === 'INTERNATIONAL')
                                        <span class="member-category-badge international">✈️ International</span>
                                    @else
                                        <span class="member-category-badge independent">⚖️ Independent</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-weight: 700; font-size: 0.85rem; color: var(--color-text-primary);">
                                        {{ $asm->role_designation ?: 'Committee Member' }}
                                    </div>
                                    <div style="margin-top: 0.2rem;">
                                        @if($asm->is_appellate_panel)
                                            <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: var(--warning); border: 1px solid rgba(245, 158, 11, 0.3); font-size: 0.72rem;">
                                                <span class="badge-dot" style="background: var(--warning);"></span>
                                                Appellate Panel
                                            </span>
                                        @else
                                            <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: var(--success); border: 1px solid rgba(16, 185, 129, 0.3); font-size: 0.72rem;">
                                                <span class="badge-dot" style="background: var(--success);"></span>
                                                Scrutiny / Selection Panel
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span style="font-size: 0.82rem; color: var(--color-text-secondary); font-family: monospace;">
                                        {{ $asm->assigned_at?->format('Y-m-d H:i') ?: 'N/A' }}
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <div class="action-btn-group justify-end">
                                        @can('remove-committee')
                                            <button type="button" 
                                                    class="action-btn action-btn-delete" 
                                                    wire:click="removeCommitteeAssignment({{ $asm->id }})" 
                                                    onclick="confirm('Are you sure you want to de-assign this committee member?') || event.stopImmediatePropagation()"
                                                    data-tooltip="De-assign Member from Committee"
                                                    aria-label="De-assign Member">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                                </svg>
                                            </button>
                                        @else
                                            <span style="font-size: 0.75rem; color: var(--color-text-muted); font-style: italic;">Assigned</span>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center" style="padding: 3rem 1rem;">
                                    <div style="color: var(--color-text-muted);">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 0.5rem auto;">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                        </svg>
                                        <p style="font-size: 0.95rem; margin: 0;">No active committee panel allocations found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5. TUNE STATUTORY COMMITTEE CONSTITUTION MODAL -->
        @if($isConstitutionModalOpen)
            <div style="position: fixed; inset: 0; background: rgba(0, 0, 0, 0.8); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 1.5rem; backdrop-filter: blur(8px);">
                <div class="card" style="width: 100%; max-width: 680px; max-height: 90vh; overflow-y: auto; border-color: var(--primary); box-shadow: 0 20px 48px -10px rgba(0, 0, 0, 0.8), 0 0 20px var(--primary-glow);">
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.25rem;">
                        <div>
                            <h3 style="margin: 0; font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary); display: flex; align-items: center; gap: 0.5rem;">
                                ⚙️ Statutory Committee Constitution Rules
                            </h3>
                            <p style="margin: 0.2rem 0 0 0; font-size: 0.85rem; color: var(--color-text-secondary);">
                                Define required statutory quotas for: <strong>{{ $currentCampaign?->title }}</strong>
                            </p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <a href="{{ $criteriaUrl }}?campaign_id={{ $selected_campaign_id }}" class="btn btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 0.3rem;">
                                <span>⚖️ Full Criteria Desk ↗</span>
                            </a>
                            <button type="button" class="btn btn-secondary" style="padding: 0.35rem 0.65rem;" wire:click="closeConstitutionModal">
                                ✕
                            </button>
                        </div>
                    </div>

                    <!-- Preset Buttons -->
                    <div style="margin-bottom: 1.5rem; background: rgba(255, 255, 255, 0.03); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1rem;">
                        <span style="display: block; font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: var(--color-text-muted); margin-bottom: 0.6rem; letter-spacing: 0.05em;">
                            ⚡ QUICK STATUTORY PRESETS
                        </span>
                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            <button type="button" class="btn btn-secondary" style="text-align: left; padding: 0.6rem 0.85rem; font-size: 0.82rem; display: flex; justify-content: space-between; align-items: center;" wire:click="applyConstitutionPreset('asst_prof')">
                                <div>
                                    <strong style="color: var(--primary);">Assistant Professor Selection Committee (6 Members • 3 Peer Reviewers)</strong>
                                    <div style="font-size: 0.74rem; color: var(--color-text-muted);">Dean (Convener), Dept Head, 3 Internal Faculty (Same Institution), 1 External Professor (Other Institution)</div>
                                </div>
                                <span class="badge" style="background: rgba(99, 102, 241, 0.15); color: var(--primary); font-weight: 800;">Apply</span>
                            </button>
                            <button type="button" class="btn btn-secondary" style="text-align: left; padding: 0.6rem 0.85rem; font-size: 0.82rem; display: flex; justify-content: space-between; align-items: center;" wire:click="applyConstitutionPreset('academic_prof')">
                                <div>
                                    <strong>Professorial Selection Board (6 Members • 3 Peer Reviewers)</strong>
                                    <div style="font-size: 0.74rem; color: var(--color-text-muted);">4 Internal Senior Leaders, 2 External Specialists (including 1 Overseas/International Specialist)</div>
                                </div>
                                <span class="badge" style="background: rgba(139, 92, 246, 0.15); color: #c084fc; font-weight: 800;">Apply</span>
                            </button>
                            <button type="button" class="btn btn-secondary" style="text-align: left; padding: 0.6rem 0.85rem; font-size: 0.82rem; display: flex; justify-content: space-between; align-items: center;" wire:click="applyConstitutionPreset('corporate_board')">
                                <div>
                                    <strong>Corporate Executive Selection Panel (5 Members • Non-Academic)</strong>
                                    <div style="font-size: 0.74rem; color: var(--color-text-muted);">3 Internal Company Executives (MD, HR, Lead), 2 External Industry Advisors / Board Delegates</div>
                                </div>
                                <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: var(--warning); font-weight: 800;">Apply</span>
                            </button>
                            <button type="button" class="btn btn-secondary" style="text-align: left; padding: 0.6rem 0.85rem; font-size: 0.82rem; display: flex; justify-content: space-between; align-items: center;" wire:click="applyConstitutionPreset('public_sector')">
                                <div>
                                    <strong>Public Sector Service Board (4 Members • Civil Service)</strong>
                                    <div style="font-size: 0.74rem; color: var(--color-text-muted);">2 Internal Departmental Officers, 1 External Service Delegate, 1 Independent Legal Arbiter</div>
                                </div>
                                <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: var(--success); font-weight: 800;">Apply</span>
                            </button>
                            <button type="button" class="btn btn-secondary" style="text-align: left; padding: 0.6rem 0.85rem; font-size: 0.82rem; display: flex; justify-content: space-between; align-items: center;" wire:click="applyConstitutionPreset('executive_admin')">
                                <div>
                                    <strong>Executive / Administrative Director Track (4 Members)</strong>
                                    <div style="font-size: 0.74rem; color: var(--color-text-muted);">2 Internal Directors/Officers, 1 External Office Delegate, 1 Independent Governance Arbiter</div>
                                </div>
                                <span class="badge" style="background: rgba(6, 182, 212, 0.15); color: var(--secondary);">Apply</span>
                            </button>
                            <button type="button" class="btn btn-secondary" style="text-align: left; padding: 0.6rem 0.85rem; font-size: 0.82rem; display: flex; justify-content: space-between; align-items: center;" wire:click="applyConstitutionPreset('expert_heavy')">
                                <div>
                                    <strong>External Expert-Led Panel (5 Members)</strong>
                                    <div style="font-size: 0.74rem; color: var(--color-text-muted);">1 Internal Convener, 3 External Subject Experts (Other Universities/Offices), 1 Overseas Specialist</div>
                                </div>
                                <span class="badge" style="background: rgba(139, 92, 246, 0.15); color: #c084fc;">Apply</span>
                            </button>
                        </div>
                    </div>

                    <!-- Custom Constitution Settings -->
                    <form wire:submit.prevent="saveConstitution">
                        <div class="mb-4">
                            <x-input label="Constitution Title" :error="$errors->first('constitution_title')">
                                <input type="text" class="form-control" wire:model="constitution_title" placeholder="e.g. Statutory Selection Board Constitution">
                            </x-input>
                        </div>

                        <!-- Dynamic Member Rules Repeater -->
                        <div style="margin-bottom: 1.5rem; background: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                                <span style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: var(--color-text-muted); letter-spacing: 0.05em;">
                                    DYNAMIC COMMITTEE RULE SLOTS
                                </span>
                                <button type="button" class="btn btn-secondary" style="font-size: 0.75rem; padding: 0.25rem 0.65rem;" wire:click="addConstitutionRule">
                                    + Add Slot
                                </button>
                            </div>

                            @if(!empty($constitution_rules))
                                <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 0.5rem;">
                                    @foreach($constitution_rules as $rIdx => $rItem)
                                        <div style="display: grid; grid-template-columns: 80px 1.5fr 1fr 1fr 35px; gap: 0.5rem; align-items: center; background: rgba(0,0,0,0.2); padding: 0.5rem; border-radius: var(--radius-sm);">
                                            <!-- Count Stepper -->
                                            <div style="display: flex; align-items: center; gap: 0.2rem;">
                                                <button type="button" class="btn btn-secondary" style="padding: 0.15rem 0.35rem; font-size: 0.75rem;" wire:click="updateConstitutionRuleCount({{ $rIdx }}, -1)">-</button>
                                                <input type="number" min="1" max="50" class="form-control" wire:model.live="constitution_rules.{{ $rIdx }}.count" style="width: 38px; text-align: center; padding: 0.2rem 0.1rem; font-weight: 700; font-size: 0.8rem;">
                                                <button type="button" class="btn btn-secondary" style="padding: 0.15rem 0.35rem; font-size: 0.75rem;" wire:click="updateConstitutionRuleCount({{ $rIdx }}, 1)">+</button>
                                            </div>

                                            <!-- Role / Title -->
                                            <input type="text" class="form-control" wire:model="constitution_rules.{{ $rIdx }}.name" placeholder="Role (e.g. Dean, Dept Head, Faculty)" style="font-size: 0.8rem; padding: 0.3rem 0.5rem;">

                                            <!-- Scope -->
                                            <select class="form-control form-select" wire:model.live="constitution_rules.{{ $rIdx }}.category" wire:change="recalculateConstitutionTotals" style="font-size: 0.75rem; padding: 0.3rem 0.4rem;">
                                                <option value="INTERNAL">🏢 Inside Org</option>
                                                <option value="EXTERNAL">🌐 Outside Org</option>
                                                <option value="INTERNATIONAL">✈️ Overseas</option>
                                                <option value="INDEPENDENT">⚖️ Arbiter</option>
                                            </select>

                                            <!-- Cadre -->
                                            <select class="form-control form-select" wire:model.live="constitution_rules.{{ $rIdx }}.cadre" style="font-size: 0.75rem; padding: 0.3rem 0.4rem;">
                                                <option value="FACULTY">🎓 Faculty</option>
                                                <option value="OFFICER">👔 Officer</option>
                                                <option value="EXPERT">🔬 Expert</option>
                                                <option value="LEGAL">⚖️ Legal</option>
                                                <option value="ANY">👤 Any</option>
                                            </select>

                                            <!-- Remove -->
                                            <button type="button" class="btn btn-danger" style="padding: 0.25rem 0.45rem; font-size: 0.75rem;" wire:click="removeConstitutionRule({{ $rIdx }})">
                                                ✕
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <x-input label="Total Required Members Quorum *" :error="$errors->first('constitution_required_total')">
                                <input type="number" min="1" max="25" class="form-control" wire:model.live="constitution_required_total">
                            </x-input>

                            <x-input label="Required Internal Staff / Faculty *" :error="$errors->first('constitution_required_internal')">
                                <input type="number" min="0" max="20" class="form-control" wire:model.live="constitution_required_internal">
                            </x-input>

                            <x-input label="Required External Experts (Other Offices/Universities) *" :error="$errors->first('constitution_required_external')">
                                <input type="number" min="0" max="20" class="form-control" wire:model.live="constitution_required_external">
                            </x-input>

                            <x-input label="Required International Specialists (Overseas) *" :error="$errors->first('constitution_required_international')">
                                <input type="number" min="0" max="20" class="form-control" wire:model.live="constitution_required_international">
                            </x-input>

                            <x-input label="Required Independent Arbiters / Governance *" :error="$errors->first('constitution_required_independent')">
                                <input type="number" min="0" max="20" class="form-control" wire:model.live="constitution_required_independent">
                            </x-input>
                        </div>

                        @php
                            $sumSlots = (int)$constitution_required_internal + (int)$constitution_required_external + (int)$constitution_required_international + (int)$constitution_required_independent;
                        @endphp

                        <div style="padding: 0.75rem 1rem; border-radius: var(--radius-sm); margin-bottom: 1.25rem; font-size: 0.85rem; display: flex; justify-content: space-between; align-items: center; {{ $sumSlots === (int)$constitution_required_total ? 'background: rgba(16, 185, 129, 0.12); color: var(--success); border: 1px solid rgba(16, 185, 129, 0.3);' : 'background: rgba(239, 68, 68, 0.12); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.3);' }}">
                            <span>Sum of Quota Slots: <strong>{{ $sumSlots }}</strong> / Total Prescribed: <strong>{{ $constitution_required_total }}</strong></span>
                            @if($sumSlots === (int)$constitution_required_total)
                                <span>✓ Exactly Balanced</span>
                            @else
                                <span>⚠️ Must equal total required</span>
                            @endif
                        </div>

                        <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                            <button type="button" class="btn btn-secondary" wire:click="closeConstitutionModal">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.5rem;" {{ $sumSlots !== (int)$constitution_required_total ? 'disabled' : '' }}>
                                Save Constitution Specification
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endif
</div>
