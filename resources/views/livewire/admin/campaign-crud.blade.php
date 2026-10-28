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
                <button type="button" class="btn btn-primary" wire:click="openCreateForm" style="display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: var(--shadow-sm);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>Create Campaign</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Segmented Tab Navigation -->
    <div style="display: flex; gap: 0.5rem; background: var(--bg-tertiary); padding: 0.35rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color); width: fit-content; margin-bottom: 1.5rem;">
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
                                    <div style="display: inline-flex; gap: 0.35rem;">
                                        <button type="button" class="btn btn-secondary" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.3rem;" wire:click="openEditForm({{ $camp->id }})">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                            <span>Edit</span>
                                        </button>
                                        <button type="button" class="btn btn-danger" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.3rem;" wire:click="deleteCampaign({{ $camp->id }})" onclick="confirm('Are you sure you want to delete this campaign? This will cascade delete applications and committees!') || event.stopImmediatePropagation()">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            </svg>
                                            <span>Delete</span>
                                        </button>
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
        <!-- Allocation form -->
        <div class="card mb-6" style="border-color: var(--secondary-glow); box-shadow: var(--shadow-sm);">
            <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                <div style="width: 34px; height: 34px; border-radius: 8px; background: rgba(6,182,212,0.15); display: flex; align-items: center; justify-content: center; color: var(--secondary);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="card-title" style="margin: 0; font-size: 1.15rem;">Allocate Operational Committee Member</h3>
                    <p class="card-subtitle" style="margin: 0;">Map authorized staff to scrutiny evaluation desks or appellate grievance benches</p>
                </div>
            </div>

            <form wire:submit.prevent="assignCommitteeMember">
                <div class="grid grid-cols-3 gap-6">
                    <x-input label="Target Job Campaign *" :error="$errors->first('selected_campaign_id')">
                        <select class="form-control form-select" wire:model="selected_campaign_id">
                            <option value="">-- Select Campaign --</option>
                            @foreach($campaigns as $c)
                                <option value="{{ $c->id }}">{{ $c->title }} ({{ $c->job_code }})</option>
                            @endforeach
                        </select>
                    </x-input>

                    <x-input label="Select User Account *" :error="$errors->first('selected_user_id')">
                        <select class="form-control form-select" wire:model="selected_user_id">
                            <option value="">-- Select Member --</option>
                            @foreach($committee_users as $u)
                                <option value="{{ $u->id }}">{{ $u->email }} (Role: {{ str_replace('-', ' ', $u->roles->first()?->name) }})</option>
                            @endforeach
                        </select>
                    </x-input>

                    <x-input label="Committee Panel Type *" :error="$errors->first('is_appellate_panel')">
                        <select class="form-control form-select" wire:model="is_appellate_panel">
                            <option value="0">Scrutiny / Selection Committee</option>
                            <option value="1">Appellate Panel (Grievance Review)</option>
                        </select>
                    </x-input>
                </div>

                <div style="margin-top: 1.25rem;">
                    <button type="submit" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <line x1="19" y1="8" x2="19" y2="14"></line>
                            <line x1="22" y1="11" x2="16" y2="11"></line>
                        </svg>
                        <span>Establish Assignment</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Assignments Table -->
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
                    {{ count($assignments ?? []) }} Assignments
                </span>
            </div>

            <div class="table-container" style="border: none; border-radius: 0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Job Campaign</th>
                            <th>Committee Member</th>
                            <th>System Role</th>
                            <th>Assigned Panel Type</th>
                            <th>Assigned Date</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $asm)
                            <tr>
                                <td>
                                    <strong>{{ $asm->campaign->title }}</strong>
                                    <div style="margin-top: 0.2rem;">
                                        <code style="color: var(--secondary); font-size: 0.8rem; background: rgba(6,182,212,0.1); padding: 0.15rem 0.4rem; border-radius: 4px;">{{ $asm->campaign->job_code }}</code>
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.6rem;">
                                        <div style="width: 28px; height: 28px; border-radius: 50%; background: rgba(99,102,241,0.15); color: var(--primary); font-weight: 700; font-size: 0.75rem; display: flex; align-items: center; justify-content: center;">
                                            {{ strtoupper(substr($asm->user->email, 0, 1)) }}
                                        </div>
                                        <strong>{{ $asm->user->email }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary); border: 1px solid rgba(99,102,241,0.25);">
                                        {{ str_replace('-', ' ', $asm->user->roles->first()?->name ?? 'No Role') }}
                                    </span>
                                </td>
                                <td>
                                    @if($asm->is_appellate_panel)
                                        <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: var(--warning); border: 1px solid rgba(245, 158, 11, 0.3);">
                                            <span class="badge-dot" style="background: var(--warning);"></span>
                                            Appellate Panel
                                        </span>
                                    @else
                                        <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: var(--success); border: 1px solid rgba(16, 185, 129, 0.3);">
                                            <span class="badge-dot" style="background: var(--success);"></span>
                                            Scrutiny Panel
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span style="font-size: 0.85rem; color: var(--color-text-secondary); font-family: monospace;">
                                        {{ $asm->assigned_at?->format('Y-m-d H:i') ?: 'N/A' }}
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <button type="button" class="btn btn-danger" style="padding: 0.25rem 0.6rem; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.3rem;" 
                                            wire:click="removeCommitteeAssignment({{ $asm->id }})" 
                                            onclick="confirm('Are you sure you want to de-assign this member?') || event.stopImmediatePropagation()">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                        <span>De-assign</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center" style="padding: 3rem 1rem;">
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
    @endif
</div>
