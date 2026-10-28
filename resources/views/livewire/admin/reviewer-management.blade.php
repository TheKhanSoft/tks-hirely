<div>
    <!-- Dynamic Header -->
    <div class="card mb-6" style="background: var(--bg-card); border-color: var(--border-color); box-shadow: var(--shadow-md);">
        <div class="flex justify-between align-center" style="flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1 class="card-title" style="font-size: 1.55rem; font-weight: 800; letter-spacing: -0.03em;">
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; background: var(--secondary-light); color: var(--secondary); margin-right: 0.5rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </span>
                    Academic Reviewers & Expert Referees Desk
                </h1>
                <p class="card-subtitle">
                    Empanel external academic experts, automatically match reviewers by discipline, and dispatch academic candidacies for peer evaluation.
                </p>
            </div>
            
            <div class="flex align-center gap-3">
                <button type="button" class="btn btn-primary btn-sm" wire:click="openCreateModal">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>Add New Academic Reviewer</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Flash Message Notification -->
    @if (session()->has('message'))
        <div class="card mb-6" style="background: rgba(16, 185, 129, 0.1); border-color: var(--success); padding: 1rem 1.25rem;">
            <div class="flex align-center gap-3">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <div style="flex: 1;">
                    <strong style="color: #6ee7b7; font-size: 0.95rem;">Action Successful</strong>
                    <p style="color: var(--color-text-secondary); font-size: 0.88rem; margin: 0.15rem 0 0;">{{ session('message') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Segmented Tab Navigation -->
    <div class="filter-pills mb-6">
        <button type="button" class="filter-pill {{ $activeTab === 'reviewers' ? 'active' : '' }}" wire:click="setTab('reviewers')">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            <span>Empanelled Reviewers Register</span>
            <span class="filter-pill-count">{{ $reviewers->total() }}</span>
        </button>

        <button type="button" class="filter-pill {{ $activeTab === 'dispatch' ? 'active' : '' }}" wire:click="setTab('dispatch')">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="20" height="14" x="2" y="7" rx="2" ry="2"/>
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
            </svg>
            <span>Academic Applications & Review Dispatch</span>
            <span class="filter-pill-count">{{ $academicApplications->count() }}</span>
        </button>
    </div>

    <!-- ============================================================== -->
    <!-- TAB 1: EMPANELLED REVIEWERS REGISTER                           -->
    <!-- ============================================================== -->
    @if($activeTab === 'reviewers')
        <div class="card mb-6">
            <div class="flex justify-between align-center mb-4" style="flex-wrap: wrap; gap: 1rem;">
                <div class="flex align-center gap-3" style="flex: 1; max-width: 600px;">
                    <div style="position: relative; flex: 1;">
                        <input type="text" class="form-control" wire:model.live.debounce.250ms="search" placeholder="Search by reviewer name, university, or specialization..." style="padding-left: 2.5rem; font-size: 0.88rem;">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="var(--color-text-muted)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); pointer-events: none;">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.3-4.3"/>
                        </svg>
                    </div>

                    @if(!empty($availableSubjects))
                        <select class="form-control form-select" wire:model.live="subjectFilter" style="width: 220px; font-size: 0.88rem;">
                            <option value="">All Disciplines / Subjects</option>
                            @foreach($availableSubjects as $subj)
                                <option value="{{ $subj }}">{{ $subj }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <button type="button" class="btn btn-primary btn-sm" wire:click="openCreateModal">
                    <span>+ Add Reviewer</span>
                </button>
            </div>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Expert Name & Rank</th>
                            <th>Subject / Field</th>
                            <th>Affiliated University & Country</th>
                            <th>Email & Contact</th>
                            <th>Specialization</th>
                            <th>Reviews</th>
                            <th>Status & Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviewers as $rev)
                            <tr wire:key="reviewer-row-{{ $rev->id }}">
                                <td>
                                    <div class="flex-col">
                                        <strong style="color: var(--color-text-primary); font-size: 0.95rem;">{{ $rev->name }}</strong>
                                        <span style="font-size: 0.78rem; color: var(--secondary); font-weight: 700;">{{ $rev->academic_rank }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-appealed" style="font-size: 0.75rem; font-weight: 700;">
                                        {{ $rev->subject }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex-col">
                                        <span style="color: var(--color-text-primary); font-size: 0.88rem; font-weight: 500;">{{ $rev->university }}</span>
                                        <small style="color: var(--color-text-muted);">{{ $rev->country }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex-col" style="font-size: 0.82rem;">
                                        <code style="color: var(--color-text-primary);">{{ $rev->user->email }}</code>
                                        @if($rev->phone)
                                            <span style="color: var(--color-text-muted); font-size: 0.78rem;">{{ $rev->phone }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span style="font-size: 0.82rem; color: var(--color-text-secondary); max-width: 220px; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $rev->specialization }}">
                                        {{ $rev->specialization ?: 'General ' . $rev->subject }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-draft" style="font-weight: 800;">
                                        {{ $rev->reviews_count }} Assigned
                                    </span>
                                </td>
                                <td>
                                    <div class="flex align-center gap-2">
                                        <button type="button" class="btn {{ $rev->is_active ? 'btn-secondary' : 'btn-danger' }} btn-sm" wire:click="toggleReviewerStatus({{ $rev->id }})" title="Toggle Active Status">
                                            <span>{{ $rev->is_active ? 'Active' : 'Inactive' }}</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center" style="padding: 3rem 1.5rem; color: var(--color-text-muted);">
                                    <div style="max-width: 360px; margin: 0 auto;">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--color-text-dim)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 0.75rem;">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                            <circle cx="9" cy="7" r="4"/>
                                        </svg>
                                        <p style="font-size: 0.95rem; font-weight: 600; color: var(--color-text-primary); margin-bottom: 0.25rem;">No Academic Reviewers Found</p>
                                        <p style="font-size: 0.82rem; color: var(--color-text-secondary); margin-bottom: 1rem;">Click below to empanel your first external academic subject expert.</p>
                                        <button type="button" class="btn btn-primary btn-sm" wire:click="openCreateModal">+ Add New Academic Reviewer</button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 1.25rem;">
                {{ $reviewers->links() }}
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- TAB 2: ACADEMIC APPLICATIONS & REVIEW DISPATCH                 -->
    <!-- ============================================================== -->
    @if($activeTab === 'dispatch')
        <div class="card mb-6">
            <div class="flex justify-between align-center mb-4">
                <div>
                    <h3 class="card-title" style="font-size: 1.15rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="14" x="2" y="7" rx="2" ry="2"/>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                        </svg>
                        Academic Position Candidacies (Lecturer, AP, Associate Prof, Prof)
                    </h3>
                    <p class="card-subtitle">Dispatch candidate dossiers to external reviewers calibrated specifically to the academic department.</p>
                </div>
            </div>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Candidate Particulars</th>
                            <th>Academic Position</th>
                            <th>Department / Field</th>
                            <th>Application Status</th>
                            <th>Peer Review State</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($academicApplications as $app)
                            @php
                                $reviewCount = $app->reviews->count();
                                $submittedReviews = $app->reviews->where('status', 'SUBMITTED')->count();
                            @endphp
                            <tr wire:key="academic-app-{{ $app->id }}">
                                <td>
                                    <div class="flex-col">
                                        <strong style="color: var(--color-text-primary); font-size: 0.95rem;">
                                            {{ $app->user->basicInfo ? trim($app->user->basicInfo->first_name . ' ' . $app->user->basicInfo->last_name) : $app->user->email }}
                                        </strong>
                                        <small style="font-family: 'JetBrains Mono', monospace; color: var(--color-text-muted);">
                                            {{ $app->user->basicInfo?->cnic_number }}
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex-col">
                                        <strong style="color: var(--color-text-primary); font-size: 0.92rem;">{{ $app->campaign->title }}</strong>
                                        <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: var(--primary);">
                                            {{ $app->campaign->job_code }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-appealed" style="font-weight: 700; font-size: 0.78rem;">
                                        {{ $app->campaign->department }}
                                    </span>
                                </td>
                                <td>
                                    @if($app->status === 'UNDER_REVIEW')
                                        <span class="badge badge-appealed">
                                            <span class="badge-dot" style="background: var(--secondary);"></span>
                                            In External Review
                                        </span>
                                    @elseif($app->status === 'PAID')
                                        <span class="badge badge-paid">
                                            <span class="badge-dot" style="background: var(--success);"></span>
                                            Paid / Scrutiny
                                        </span>
                                    @elseif($app->status === 'ELIGIBLE')
                                        <span class="badge badge-eligible">
                                            <span class="badge-dot" style="background: var(--success);"></span>
                                            Eligible
                                        </span>
                                    @else
                                        <span class="badge badge-draft">{{ $app->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($reviewCount > 0)
                                        <div class="flex-col gap-1">
                                            <span class="badge {{ $submittedReviews === $reviewCount ? 'badge-paid' : 'badge-pending' }}" style="font-size: 0.72rem;">
                                                {{ $submittedReviews }} of {{ $reviewCount }} Reports Received
                                            </span>
                                            <div style="font-size: 0.75rem; color: var(--color-text-muted);">
                                                @foreach($app->reviews as $r)
                                                    <span style="display: block;">• {{ $r->reviewer->reviewerProfile?->name ?? 'Reviewer' }} ({{ $r->status }})</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <span style="color: var(--color-text-muted); font-size: 0.82rem;">Not Dispatched</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex gap-2 align-center">
                                        <button type="button" class="btn btn-primary btn-sm" wire:click="openDispatchModal({{ $app->id }})" title="Assign Field-Related Reviewers">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                <line x1="22" y1="2" x2="11" y2="13"/>
                                                <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                            </svg>
                                            <span>{{ $reviewCount > 0 ? 'Add Reviewers' : 'Send for Review' }}</span>
                                        </button>

                                        @if($reviewCount > 0)
                                            <button type="button" class="btn btn-secondary btn-sm" wire:click="openReportsModal({{ $app->id }})" title="View Evaluation Reports">
                                                <span>Reports ({{ $submittedReviews }})</span>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center" style="padding: 2.5rem; color: var(--color-text-muted);">
                                    No academic position candidacies registered in the system.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL 1: ADD NEW ACADEMIC REVIEWER                             -->
    <!-- ============================================================== -->
    @if($showCreateModal)
        <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.82); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 1.5rem;">
            <div class="card" style="width: 100%; max-width: 680px; max-height: 90vh; overflow-y: auto; border-color: var(--border-color); box-shadow: var(--shadow-lg); background: var(--bg-card-solid);">
                <div class="flex justify-between align-center mb-4" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                    <div>
                        <h3 class="card-title" style="margin: 0; font-size: 1.25rem;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <line x1="19" y1="8" x2="19" y2="14"/>
                                <line x1="22" y1="11" x2="16" y2="11"/>
                            </svg>
                            Add New Academic Reviewer / Subject Expert
                        </h3>
                        <p class="card-subtitle">Empanel an external referee for university faculty appointment reviews.</p>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="closeCreateModal">✕</button>
                </div>

                <div class="card mb-4" style="background: rgba(6, 182, 212, 0.08); border-color: var(--secondary-glow); padding: 0.85rem 1rem;">
                    <p style="color: #67e8f9; font-size: 0.82rem; margin: 0; line-height: 1.5;">
                        <strong>Automatic Credential Dispatch:</strong> Once added, Hirely will generate secure login credentials and dispatch an official invitation email with complete evaluation guidance and portal access instructions.
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
                            </select>
                        </x-select>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <x-input label="Affiliated University / Institution *" :error="$errors->first('university')">
                            <input type="text" class="form-control" wire:model="university" placeholder="e.g. National University of Sciences & Technology">
                        </x-input>

                        <x-input label="Country *" :error="$errors->first('country')">
                            <input type="text" class="form-control" wire:model="country" placeholder="e.g. Pakistan, United Kingdom">
                        </x-input>
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
    <!-- MODAL 2: SEND APPLICATION FOR REVIEW (FIELD-RELATED MATCHING)   -->
    <!-- ============================================================== -->
    @if($showDispatchModal && $targetApplication)
        <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 1.5rem;">
            <div class="card" style="width: 100%; max-width: 780px; max-height: 90vh; display: flex; flex-direction: column; border-color: var(--border-color); box-shadow: var(--shadow-lg); background: var(--bg-card-solid); overflow: hidden;">
                <!-- Header -->
                <div class="flex justify-between align-center" style="padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
                    <div>
                        <h3 class="card-title" style="margin: 0; font-size: 1.25rem;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--secondary)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="22" y1="2" x2="11" y2="13"/>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                            </svg>
                            Dispatch Candidacy for External Academic Peer Review
                        </h3>
                        <p class="card-subtitle" style="margin: 0.25rem 0 0;">
                            Position: <strong>{{ $targetApplication->campaign->title }}</strong> • Department: <strong style="color: var(--secondary);">{{ $targetApplication->campaign->department }}</strong>
                        </p>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="closeDispatchModal">✕</button>
                </div>

                <!-- Case Details & Smart Filter Header -->
                <div style="padding: 1rem 1.5rem; background: var(--bg-surface); border-bottom: 1px solid var(--border-color);" class="flex justify-between align-center flex-wrap gap-2">
                    <div class="flex align-center gap-2">
                        <span style="font-size: 0.85rem; color: var(--color-text-secondary);">Candidate:</span>
                        <strong style="color: var(--color-text-primary);">
                            {{ $targetApplication->user->basicInfo ? trim($targetApplication->user->basicInfo->first_name . ' ' . $targetApplication->user->basicInfo->last_name) : $targetApplication->user->email }}
                        </strong>
                        <span class="badge badge-appealed" style="font-size: 0.7rem;">{{ $targetApplication->campaign->department }}</span>
                    </div>

                    <!-- Field-Related Filter Toggle -->
                    <label class="flex align-center gap-2" style="cursor: pointer; font-size: 0.84rem; color: var(--color-text-primary); font-weight: 600;">
                        <input type="checkbox" wire:model.live="filterByFieldOnly" style="accent-color: var(--primary);">
                        <span>Only show field-related reviewers ({{ $targetApplication->campaign->department }})</span>
                    </label>
                </div>

                <!-- Field Matched Reviewers Selection Area -->
                <div style="padding: 1.5rem; overflow-y: auto; flex: 1;">
                    <div class="flex justify-between align-center mb-3">
                        <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--color-text-primary); margin: 0;">
                            @if($filterByFieldOnly)
                                🎯 Field-Matched Subject Experts in {{ $targetApplication->campaign->department }} ({{ $fieldMatchedReviewers->count() }} Found)
                            @else
                                All Empanelled Academic Reviewers ({{ $fieldMatchedReviewers->count() }} Available)
                            @endif
                        </h4>
                        <span style="font-size: 0.78rem; color: var(--secondary); font-weight: 700;">
                            {{ count($selectedReviewerIds) }} Reviewer(s) Selected
                        </span>
                    </div>

                    @error('selectedReviewerIds')
                        <div class="card mb-3" style="background: var(--danger-light); border-color: var(--danger-glow); padding: 0.75rem 1rem;">
                            <span style="color: #fca5a5; font-size: 0.82rem; font-weight: 600;">{{ $message }}</span>
                        </div>
                    @enderror

                    <div class="vacancy-grid" style="max-height: 280px;">
                        @forelse($fieldMatchedReviewers as $rev)
                            @php
                                $isAlreadyAssigned = $targetApplication->reviews->contains('reviewer_id', $rev->user_id);
                                $isSelected = in_array($rev->user_id, $selectedReviewerIds);
                            @endphp
                            <div class="vacancy-card {{ $isAlreadyAssigned ? 'disabled' : ($isSelected ? 'selected' : '') }}"
                                 wire:key="rev-select-{{ $rev->id }}"
                                 @if(!$isAlreadyAssigned) wire:click="toggleReviewerSelection({{ $rev->user_id }})" @endif>
                                <div>
                                    <div class="flex justify-between align-center mb-1">
                                        <span class="badge badge-appealed" style="font-size: 0.68rem;">{{ $rev->subject }}</span>
                                        <span style="font-size: 0.72rem; color: var(--color-text-muted);">{{ $rev->country }}</span>
                                    </div>

                                    <strong style="color: var(--color-text-primary); font-size: 0.92rem; display: block; margin-bottom: 2px;">
                                        {{ $rev->name }}
                                    </strong>
                                    <span style="font-size: 0.8rem; color: var(--secondary); font-weight: 600; display: block;">
                                        {{ $rev->academic_rank }}
                                    </span>
                                    <span style="font-size: 0.75rem; color: var(--color-text-secondary); display: block;">
                                        {{ $rev->university }}
                                    </span>
                                </div>

                                <div style="margin-top: 0.75rem; border-top: 1px solid var(--border-color); padding-top: 0.5rem;" class="flex justify-between align-center">
                                    <span style="font-size: 0.72rem; color: var(--color-text-muted);">
                                        Load: {{ $rev->reviews->count() }} dossiers
                                    </span>

                                    @if($isAlreadyAssigned)
                                        <span class="badge badge-paid" style="font-size: 0.65rem;">Already Assigned</span>
                                    @elseif($isSelected)
                                        <span class="badge badge-eligible" style="font-size: 0.65rem;">✓ Selected</span>
                                    @else
                                        <span class="btn btn-secondary btn-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">Select</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div style="grid-column: 1 / -1; text-align: center; color: var(--color-text-muted); padding: 2rem;">
                                No external reviewers found matching <strong>{{ $targetApplication->campaign->department }}</strong>.
                                <br>
                                <button type="button" class="btn btn-secondary btn-sm mt-2" wire:click="$set('filterByFieldOnly', false)">Show All Disciplines</button>
                                <button type="button" class="btn btn-primary btn-sm mt-2" wire:click="openCreateModal">+ Empanel New Expert</button>
                            </div>
                        @endforelse
                    </div>

                    <!-- Due Date & Confidential Notes -->
                    <div class="grid grid-cols-2 gap-4 mt-4" style="border-top: 1px solid var(--border-color); padding-top: 1rem;">
                        <x-input label="Evaluation Due Date *" :error="$errors->first('reviewDueDate')">
                            <input type="date" class="form-control" wire:model="reviewDueDate">
                        </x-input>

                        <x-input label="Confidential Instructions to Reviewers" :error="$errors->first('reviewNotes')">
                            <input type="text" class="form-control" wire:model="reviewNotes" placeholder="e.g. Focus on post-PhD HEC category publications...">
                        </x-input>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex justify-between align-center" style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--border-color); background: var(--bg-surface);">
                    <button type="button" class="btn btn-secondary" wire:click="closeDispatchModal">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="dispatchApplicationToReviewers" wire:loading.attr="disabled" :disabled="empty($selectedReviewerIds)">
                        <span wire:loading.remove wire:target="dispatchApplicationToReviewers">Dispatch Application & Send Emails</span>
                        <span wire:loading wire:target="dispatchApplicationToReviewers">Dispatching to Reviewers...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL 3: VIEW ACADEMIC REVIEW REPORTS                          -->
    <!-- ============================================================== -->
    @if($showReportsModal && $viewingApplication)
        <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 1.5rem;">
            <div class="card" style="width: 100%; max-width: 840px; max-height: 90vh; overflow-y: auto; border-color: var(--border-color); box-shadow: var(--shadow-lg); background: var(--bg-card-solid);">
                <div class="flex justify-between align-center mb-4" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                    <div>
                        <h3 class="card-title" style="margin: 0; font-size: 1.25rem;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                            External Peer Review Reports: {{ $viewingApplication->campaign->title }}
                        </h3>
                        <p class="card-subtitle">
                            Candidate: <strong>{{ $viewingApplication->user->basicInfo ? trim($viewingApplication->user->basicInfo->first_name . ' ' . $viewingApplication->user->basicInfo->last_name) : $viewingApplication->user->email }}</strong>
                        </p>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="closeReportsModal">✕</button>
                </div>

                <div class="flex-col gap-4">
                    @forelse($applicationReviews as $ar)
                        <div class="card" style="background: var(--bg-surface); border-color: var(--border-color); padding: 1.25rem;">
                            <div class="flex justify-between align-center mb-3" style="flex-wrap: wrap; gap: 0.5rem;">
                                <div>
                                    <strong style="font-size: 1rem; color: var(--color-text-primary);">
                                        {{ $ar->reviewer->reviewerProfile?->name ?? 'External Reviewer' }}
                                    </strong>
                                    <div style="font-size: 0.78rem; color: var(--color-text-secondary);">
                                        {{ $ar->reviewer->reviewerProfile?->academic_rank }} • {{ $ar->reviewer->reviewerProfile?->university }} ({{ $ar->reviewer->reviewerProfile?->subject }})
                                    </div>
                                </div>

                                <div>
                                    @if($ar->status === 'SUBMITTED')
                                        <span class="badge badge-paid">✓ Report Submitted</span>
                                    @else
                                        <span class="badge badge-pending">⚡ Pending Evaluation</span>
                                    @endif
                                </div>
                            </div>

                            @if($ar->status === 'SUBMITTED')
                                <div class="grid grid-cols-2 gap-4 mb-3" style="background: rgba(0,0,0,0.2); padding: 0.85rem; border-radius: var(--radius-sm);">
                                    <div>
                                        <span style="font-size: 0.75rem; color: var(--color-text-muted); text-transform: uppercase;">Overall Score</span><br>
                                        <strong style="color: var(--warning); font-size: 1.25rem;">{{ $ar->overall_score }} / 100</strong>
                                    </div>
                                    <div>
                                        <span style="font-size: 0.75rem; color: var(--color-text-muted); text-transform: uppercase;">Recommendation</span><br>
                                        <strong style="color: {{ in_array($ar->recommendation, ['STRONGLY_RECOMMENDED', 'RECOMMENDED']) ? 'var(--success)' : 'var(--danger)' }}; font-size: 1rem;">
                                            {{ str_replace('_', ' ', $ar->recommendation) }}
                                        </strong>
                                    </div>
                                </div>

                                <div>
                                    <strong style="font-size: 0.82rem; color: var(--color-text-secondary); text-transform: uppercase;">Qualitative Expert Remarks:</strong>
                                    <p style="color: var(--color-text-primary); font-size: 0.88rem; margin: 0.35rem 0 0; line-height: 1.6; background: var(--bg-card-solid); padding: 0.75rem; border-radius: var(--radius-sm);">
                                        {{ $ar->report_text }}
                                    </p>
                                </div>

                                <div class="flex justify-between align-center mt-3" style="font-size: 0.75rem; color: var(--color-text-muted);">
                                    <span>Submitted on: {{ $ar->submitted_at?->format('d M Y, h:i A') }}</span>
                                    @if($ar->evaluation_file_path)
                                        <a href="/storage/{{ $ar->evaluation_file_path }}" target="_blank" class="btn btn-secondary btn-sm" style="font-size: 0.72rem;">Download Evaluation PDF</a>
                                    @endif
                                </div>
                            @else
                                <p style="color: var(--color-text-muted); font-size: 0.82rem; margin: 0;">
                                    Review assigned on {{ $ar->assigned_at?->format('d M Y') }}. Review deadline: <strong>{{ $ar->due_date?->format('d M Y') ?: 'In 14 days' }}</strong>.
                                </p>
                            @endif
                        </div>
                    @empty
                        <p style="color: var(--color-text-muted); text-align: center; padding: 2rem;">No review assignments registered for this application.</p>
                    @endforelse
                </div>

                <div class="flex justify-end mt-4" style="border-top: 1px solid var(--border-color); padding-top: 1rem;">
                    <button type="button" class="btn btn-secondary" wire:click="closeReportsModal">Close</button>
                </div>
            </div>
        </div>
    @endif
</div>
