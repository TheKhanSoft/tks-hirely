<div>
    <!-- Executive Control Header -->
    <div class="card mb-6" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.25rem; position: relative; overflow: hidden;">
        <div style="position: absolute; right: -2rem; top: -2rem; width: 160px; height: 160px; background: radial-gradient(circle, var(--secondary-glow) 0%, transparent 70%); pointer-events: none;"></div>
        <div>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.35rem;">
                <div class="brand-icon-box" style="width: 42px; height: 42px; border-radius: 12px; background: rgba(6,182,212,0.15); border: 1px solid rgba(6,182,212,0.3);">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--secondary);">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div>
                    <h2 class="card-title" style="margin: 0; font-size: 1.45rem;">Academic Reviewers & Expert Referees Desk</h2>
                    <p class="card-subtitle" style="margin: 0;">Empanel external academic experts, match referees by discipline, and monitor confidential peer evaluations</p>
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <button type="button" class="btn btn-primary" wire:click="openCreateModal" style="display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: var(--shadow-sm);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Add New Academic Reviewer</span>
            </button>
        </div>
    </div>

    <!-- Flash Message Notification -->
    @if (session()->has('message'))
        <div class="alert alert-success mb-6" style="display: flex; align-items: center; gap: 0.75rem; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3); color: var(--success); padding: 0.85rem 1.25rem; border-radius: var(--radius-md);">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <div style="flex: 1;">
                <span>{{ session('message') }}</span>
            </div>
        </div>
    @endif

    <!-- Segmented Tab Navigation -->
    <div style="display: flex; gap: 0.5rem; background: var(--bg-tertiary); padding: 0.35rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color); width: fit-content; margin-bottom: 1.5rem;">
        <button type="button" 
                class="btn" 
                wire:click="setTab('reviewers')"
                style="padding: 0.5rem 1.25rem; font-size: 0.875rem; border-radius: var(--radius-md); font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; transition: var(--transition-base); {{ $activeTab === 'reviewers' ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            <span>Empanelled Reviewers Register</span>
            <span style="font-size: 0.75rem; padding: 0.15rem 0.5rem; border-radius: 999px; background: rgba(99,102,241,0.15); color: var(--primary);">{{ $reviewers->total() }}</span>
        </button>

        <button type="button" 
                class="btn" 
                wire:click="setTab('dispatch')"
                style="padding: 0.5rem 1.25rem; font-size: 0.875rem; border-radius: var(--radius-md); font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; transition: var(--transition-base); {{ $activeTab === 'dispatch' ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="20" height="14" x="2" y="7" rx="2" ry="2"/>
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
            </svg>
            <span>Academic Candidacies & Dispatch</span>
            <span style="font-size: 0.75rem; padding: 0.15rem 0.5rem; border-radius: 999px; background: rgba(6,182,212,0.15); color: var(--secondary);">{{ $academicApplications->count() }}</span>
        </button>
    </div>

    <!-- ============================================================== -->
    <!-- TAB 1: EMPANELLED REVIEWERS REGISTER                           -->
    <!-- ============================================================== -->
    @if($activeTab === 'reviewers')
        <div class="card mb-4" style="padding: 1rem 1.25rem;">
            <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                <div style="position: relative; flex: 1; min-width: 260px;">
                    <input type="text" class="form-control" wire:model.live.debounce.250ms="search" placeholder="Search by reviewer name, university, or specialization..." style="padding-left: 2.5rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-text-muted)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); pointer-events: none;">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.3-4.3"/>
                    </svg>
                </div>

                @if(!empty($availableSubjects))
                    <div style="width: 240px;">
                        <select class="form-control form-select" wire:model.live="subjectFilter">
                            <option value="">All Disciplines / Subjects</option>
                            @foreach($availableSubjects as $subj)
                                <option value="{{ $subj }}">{{ $subj }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div>
                    <span style="font-size: 0.85rem; color: var(--color-text-secondary); font-weight: 600;">
                        Showing {{ $reviewers->total() }} expert{{ $reviewers->total() === 1 ? '' : 's' }}
                    </span>
                </div>
            </div>
        </div>

        <x-table :headers="['Expert Name & Rank', 'Discipline / Field', 'Institution & Country', 'Contact Info', 'Specialization Scope', 'Active Dossiers', 'Status & Actions']">
            @forelse($reviewers as $rev)
                <tr wire:key="reviewer-row-{{ $rev->id }}">
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, rgba(6,182,212,0.2), rgba(99,102,241,0.2)); border: 1px solid rgba(6,182,212,0.3); color: var(--secondary); font-weight: 800; font-size: 0.85rem; display: flex; align-items: center; justify-content: center;">
                                {{ strtoupper(substr($rev->name, 0, 1)) }}
                            </div>
                            <div>
                                <strong style="color: var(--color-text-primary); font-size: 0.95rem;">{{ $rev->name }}</strong><br>
                                <span style="font-size: 0.78rem; color: var(--secondary); font-weight: 700;">{{ $rev->academic_rank }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary); border: 1px solid rgba(99,102,241,0.25); font-weight: 700;">
                            {{ $rev->subject }}
                        </span>
                    </td>
                    <td>
                        <div>
                            <span style="color: var(--color-text-primary); font-size: 0.88rem; font-weight: 600;">{{ $rev->university }}</span><br>
                            <small style="color: var(--color-text-muted);">📍 {{ $rev->country }}</small>
                        </div>
                    </td>
                    <td>
                        <div>
                            <code style="color: var(--color-text-primary); font-size: 0.82rem;">{{ $rev->user->email }}</code>
                            @if($rev->phone)
                                <br><small style="color: var(--color-text-muted); font-size: 0.78rem;">{{ $rev->phone }}</small>
                            @endif
                        </div>
                    </td>
                    <td>
                        <span style="font-size: 0.82rem; color: var(--color-text-secondary); max-width: 220px; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $rev->specialization }}">
                            {{ $rev->specialization ?: 'General ' . $rev->subject }}
                        </span>
                    </td>
                    <td>
                        <span class="badge" style="background: rgba(6,182,212,0.12); color: var(--secondary); font-weight: 800; border: 1px solid rgba(6,182,212,0.25);">
                            {{ $rev->reviews_count }} Assigned
                        </span>
                    </td>
                    <td>
                        <button type="button" 
                                class="btn {{ $rev->is_active ? 'btn-success' : 'btn-danger' }}" 
                                style="padding: 0.2rem 0.6rem; font-size: 0.75rem; border-radius: 999px; font-weight: 600;" 
                                wire:click="toggleReviewerStatus({{ $rev->id }})" 
                                title="Click to toggle reviewer availability status">
                            {{ $rev->is_active ? 'Active' : 'Inactive' }}
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 3rem 1.5rem; color: var(--color-text-muted);">
                        <p style="font-size: 0.95rem; font-weight: 600; color: var(--color-text-primary); margin-bottom: 0.25rem;">No Academic Reviewers Found</p>
                        <p style="font-size: 0.82rem; color: var(--color-text-secondary); margin-bottom: 1rem;">Empanel your first external academic subject expert.</p>
                        <button type="button" class="btn btn-primary btn-sm" wire:click="openCreateModal">+ Add New Academic Reviewer</button>
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div style="margin-top: 1.25rem;">
            {{ $reviewers->links() }}
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- TAB 2: ACADEMIC APPLICATIONS & REVIEW DISPATCH                 -->
    <!-- ============================================================== -->
    @if($activeTab === 'dispatch')
        <div class="card mb-4" style="padding: 1.25rem 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <h3 class="card-title" style="margin: 0; font-size: 1.15rem; display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="14" x="2" y="7" rx="2" ry="2"/>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                        </svg>
                        <span>Academic Position Candidacies (Lecturer, Assistant Prof, Associate Prof, Prof)</span>
                    </h3>
                    <p class="card-subtitle" style="margin: 0.2rem 0 0;">Dispatch candidate dossiers to external reviewers calibrated specifically to the academic department</p>
                </div>
            </div>
        </div>

        <x-table :headers="['Candidate Particulars', 'Academic Position', 'Department / Field', 'Application Status', 'Peer Review Progress & Referees', 'Actions']">
            @forelse($academicApplications as $app)
                @php
                    $reviewCount = $app->reviews->count();
                    $submittedReviews = $app->reviews->where('status', 'SUBMITTED')->count();
                    $pct = $reviewCount > 0 ? round(($submittedReviews / $reviewCount) * 100) : 0;
                @endphp
                <tr wire:key="academic-app-{{ $app->id }}">
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: rgba(99,102,241,0.15); color: var(--primary); font-weight: 800; font-size: 0.85rem; display: flex; align-items: center; justify-content: center;">
                                {{ strtoupper(substr($app->user->basicInfo?->first_name ?: $app->user->email, 0, 1)) }}
                            </div>
                            <div>
                                <strong style="color: var(--color-text-primary); font-size: 0.95rem;">
                                    {{ $app->user->basicInfo ? trim($app->user->basicInfo->first_name . ' ' . $app->user->basicInfo->last_name) : $app->user->email }}
                                </strong><br>
                                <code style="font-size: 0.78rem; color: var(--color-text-secondary);">
                                    {{ $app->user->basicInfo?->cnic_number ?: $app->user->email }}
                                </code>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div>
                            <strong style="color: var(--color-text-primary); font-size: 0.92rem;">{{ $app->campaign->title }}</strong><br>
                            <code style="font-size: 0.75rem; color: var(--primary);">
                                {{ $app->campaign->job_code }}
                            </code>
                        </div>
                    </td>
                    <td>
                        <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary); border: 1px solid rgba(99,102,241,0.25); font-weight: 700;">
                            {{ $app->campaign->department }}
                        </span>
                    </td>
                    <td>
                        @if($app->status === 'UNDER_REVIEW')
                            <span class="badge" style="background: rgba(6,182,212,0.15); color: var(--secondary); border: 1px solid rgba(6,182,212,0.3);">
                                <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: var(--secondary); margin-right: 0.35rem;"></span>
                                In Peer Review
                            </span>
                        @elseif($app->status === 'PAID')
                            <span class="badge" style="background: rgba(16,185,129,0.15); color: var(--success); border: 1px solid rgba(16,185,129,0.3);">
                                Paid / In Scrutiny
                            </span>
                        @elseif($app->status === 'ELIGIBLE')
                            <span class="badge" style="background: rgba(16,185,129,0.15); color: var(--success); border: 1px solid rgba(16,185,129,0.3);">
                                Eligible
                            </span>
                        @else
                            <span class="badge" style="background: rgba(255,255,255,0.08); color: var(--color-text-secondary);">{{ $app->status }}</span>
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
                                <div class="review-progress-track" style="margin-bottom: 0.5rem;">
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
                            <span style="color: var(--color-text-muted); font-size: 0.82rem; font-style: italic;">Not Dispatched</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex gap-2 align-center">
                            <button type="button" class="btn btn-primary btn-sm" wire:click="openDispatchModal({{ $app->id }})" style="display: inline-flex; align-items: center; gap: 0.35rem;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="22" y1="2" x2="11" y2="13"/>
                                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                </svg>
                                <span>{{ $reviewCount > 0 ? 'Add Reviewers' : 'Send for Review' }}</span>
                            </button>

                            @if($reviewCount > 0)
                                <button type="button" class="btn btn-secondary btn-sm" wire:click="openReportsModal({{ $app->id }})" style="display: inline-flex; align-items: center; gap: 0.35rem;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                    <span>Reports ({{ $submittedReviews }})</span>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 3rem; color: var(--color-text-muted);">
                        No academic position candidacies registered in the system.
                    </td>
                </tr>
            @endforelse
        </x-table>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL 2: SEND APPLICATION FOR REVIEW (FIELD-RELATED MATCHING)   -->
    <!-- ============================================================== -->
    @if($showDispatchModal && $targetApplication)
        <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); display: flex; align-items: center; justify-content: center; z-index: 1050; padding: 1.5rem;">
            <div class="card" style="width: 100%; max-width: 860px; max-height: 90vh; display: flex; flex-direction: column; border-color: var(--border-color); box-shadow: var(--shadow-lg); background: var(--bg-card-solid); border-radius: var(--radius-lg); overflow: hidden;">
                <!-- Header -->
                <div class="flex justify-between align-center" style="padding: 1.25rem 1.75rem; border-bottom: 1px solid var(--border-color); background: var(--bg-surface); flex-shrink: 0;">
                    <div style="display: flex; align-items: center; gap: 0.85rem;">
                        <div class="brand-icon-box" style="width: 40px; height: 40px; border-radius: 10px; background: rgba(6,182,212,0.12); color: var(--secondary);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="22" y1="2" x2="11" y2="13"/>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="card-title" style="margin: 0; font-size: 1.22rem; font-weight: 800; letter-spacing: -0.02em;">
                                Dispatch Candidacy for External Academic Peer Review
                            </h3>
                            <p class="card-subtitle" style="margin: 0.2rem 0 0; font-size: 0.84rem;">
                                Position: <strong style="color: var(--color-text-primary);">{{ $targetApplication->campaign->title }}</strong> • Department: <strong style="color: var(--secondary);">{{ $targetApplication->campaign->department }}</strong>
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="closeDispatchModal" style="padding: 0.35rem 0.65rem;">✕</button>
                </div>

                <!-- Case Details & Smart Filter Header -->
                <div style="padding: 1rem 1.75rem; background: rgba(0,0,0,0.2); border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; flex-shrink: 0;">
                    <div style="display: flex; align-items: center; gap: 0.85rem; min-width: 260px;">
                        <div class="user-avatar-circle" style="width: 38px; height: 38px; font-size: 0.82rem; font-weight: 800; background: linear-gradient(135deg, var(--secondary), var(--primary)); flex-shrink: 0;">
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
                                CNIC: {{ $targetApplication->user->basicInfo?->cnic_number ?? 'Pending' }}
                            </div>
                        </div>
                    </div>

                    <!-- Filter Pill Toggle -->
                    <label style="cursor: pointer; display: inline-flex; align-items: center; gap: 0.55rem; background: var(--bg-card); padding: 0.45rem 0.95rem; border-radius: 9999px; border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); user-select: none;">
                        <input type="checkbox" wire:model.live="filterByFieldOnly" style="width: 1.1rem; height: 1.1rem; accent-color: var(--secondary); cursor: pointer;">
                        <span style="font-size: 0.82rem; font-weight: 600; color: var(--color-text-primary);">
                            Only show field-related reviewers ({{ $targetApplication->campaign->department }})
                        </span>
                    </label>
                </div>

                <!-- Field Matched Reviewers Selection Area -->
                <div style="padding: 1.5rem 1.75rem 3rem 1.75rem; overflow-y: auto; flex: 1;">
                    <!-- Inline Empanelment Form (Opens smoothly right inside the dispatch window) -->
                    @if($showInlineEmpanel)
                        <div style="background: rgba(13, 20, 36, 0.98); border: 1.5px solid var(--secondary); border-radius: var(--radius-md); padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 10px 30px rgba(0,0,0,0.6);">
                            <div class="flex justify-between align-center mb-3">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <div style="width: 28px; height: 28px; border-radius: 6px; background: rgba(6,182,212,0.15); color: var(--secondary); display: flex; align-items: center; justify-content: center;">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                    </div>
                                    <div>
                                        <strong style="color: var(--color-text-primary); font-size: 0.98rem;">Empanel & Assign New Subject Expert</strong>
                                        <span style="font-size: 0.75rem; color: var(--secondary); display: block;">For {{ $targetApplication->campaign->department }} (Automatically selected upon saving)</span>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-secondary btn-sm" wire:click="$set('showInlineEmpanel', false)" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;">✕ Close Form</button>
                            </div>

                            <form wire:submit.prevent="createReviewer">
                                <div class="grid grid-cols-2 gap-3 mb-3">
                                    <x-input label="Full Name & Title *" :error="$errors->first('name')">
                                        <input type="text" class="form-control" wire:model="name" placeholder="e.g. Prof. Dr. Tariq Mahmood">
                                    </x-input>
                                    <x-input label="Institutional Email Address *" :error="$errors->first('email')">
                                        <input type="email" class="form-control" wire:model="email" placeholder="e.g. tariq.mahmood@nust.edu.pk">
                                    </x-input>
                                </div>

                                <div class="grid grid-cols-2 gap-3 mb-3">
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
                                        </select>
                                    </x-select>
                                </div>

                                <div class="grid grid-cols-2 gap-3 mb-3">
                                    <x-input label="Affiliated University / Institution *" :error="$errors->first('university')">
                                        <input type="text" class="form-control" wire:model="university" placeholder="e.g. National University of Sciences & Technology">
                                    </x-input>
                                    <x-searchable-country-select wire:model="country" label="Affiliated Country *" :error="$errors->first('country')" placeholder="Search or select country..." />
                                </div>

                                <div class="grid grid-cols-2 gap-3 mb-3">
                                    <x-input label="Contact Phone (Optional)" :error="$errors->first('phone')">
                                        <input type="text" class="form-control" wire:model="phone" placeholder="e.g. +92 300 1234567">
                                    </x-input>
                                    <x-input label="Specialization / Research Focus (Optional)" :error="$errors->first('specialization')">
                                        <input type="text" class="form-control" wire:model="specialization" placeholder="e.g. AI, Distributed Systems">
                                    </x-input>
                                </div>

                                <div class="flex justify-between align-center mt-3 pt-3" style="border-top: 1px solid var(--border-color);">
                                    <button type="button" class="btn btn-secondary btn-sm" wire:click="$set('showInlineEmpanel', false)">Cancel</button>
                                    <button type="submit" class="btn btn-primary btn-sm" wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="createReviewer">✓ Empanel & Select for Review</span>
                                        <span wire:loading wire:target="createReviewer">Saving Expert...</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1rem; flex-wrap: wrap;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-size: 1.1rem;">🎯</span>
                            <h4 style="font-size: 0.98rem; font-weight: 700; color: var(--color-text-primary); margin: 0;">
                                @if($filterByFieldOnly)
                                    Field-Matched Subject Experts in {{ $targetApplication->campaign->department }}
                                @else
                                    All Empanelled Academic Reviewers
                                @endif
                            </h4>
                            <span class="badge" style="background: rgba(99,102,241,0.14); color: var(--primary); font-size: 0.75rem; font-weight: 800;">
                                {{ $fieldMatchedReviewers->count() }} Found
                            </span>
                        </div>

                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <button type="button" class="btn btn-secondary btn-sm" wire:click="toggleInlineEmpanel('{{ addslashes($targetApplication->campaign->department) }}')" style="font-size: 0.78rem; padding: 0.35rem 0.75rem; display: inline-flex; align-items: center; gap: 0.35rem;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                <span>{{ $showInlineEmpanel ? 'Hide Empanel Form' : '+ Empanel New Expert' }}</span>
                            </button>

                            <span class="badge {{ count($selectedReviewerIds) > 0 ? 'badge-paid' : 'badge-draft' }}" style="font-size: 0.78rem; font-weight: 800; padding: 0.35rem 0.75rem;">
                                {{ count($selectedReviewerIds) }} Expert(s) Selected
                            </span>
                        </div>
                    </div>

                    @error('selectedReviewerIds')
                        <div class="card mb-3" style="background: rgba(239,68,68,0.12); border-color: rgba(239,68,68,0.3); padding: 0.75rem 1rem;">
                            <span style="color: #fca5a5; font-size: 0.82rem; font-weight: 600;">{{ $message }}</span>
                        </div>
                    @enderror

                    <div class="grid grid-cols-2 gap-3 mb-6" style="max-height: 280px; overflow-y: auto;">
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
                                        <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary); font-size: 0.68rem; font-weight: 700;">{{ $rev->subject }}</span>
                                        <span style="font-size: 0.72rem; color: var(--color-text-muted);">📍 {{ $rev->country }}</span>
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
                                        <span class="badge" style="background: rgba(16,185,129,0.15); color: var(--success); font-size: 0.65rem;">Already Assigned</span>
                                    @elseif($isSelected)
                                        <span class="badge" style="background: rgba(6,182,212,0.15); color: var(--secondary); font-size: 0.65rem; font-weight: 700;">✓ Selected</span>
                                    @else
                                        <span class="btn btn-secondary btn-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">Select</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div style="grid-column: 1 / -1; text-align: center; color: var(--color-text-muted); padding: 2.5rem 1.5rem; background: rgba(0,0,0,0.15); border: 1.5px dashed var(--border-color); border-radius: var(--radius-md);">
                                <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(6,182,212,0.1); color: var(--secondary); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <line x1="19" y1="8" x2="19" y2="14"/>
                                        <line x1="22" y1="11" x2="16" y2="11"/>
                                    </svg>
                                </div>
                                <h5 style="color: var(--color-text-primary); font-size: 1rem; font-weight: 700; margin: 0 0 0.35rem 0;">
                                    No External Reviewers Empanelled for {{ $targetApplication->campaign->department }}
                                </h5>
                                <p style="font-size: 0.82rem; color: var(--color-text-secondary); max-width: 460px; margin: 0 auto 1.25rem auto; line-height: 1.5;">
                                    Statutory faculty appointments require external subject reviews. Click below to empanel an expert right here or browse all faculties.
                                </p>
                                <div class="flex gap-2 justify-center flex-wrap">
                                    <button type="button" class="btn btn-primary btn-sm" wire:click="toggleInlineEmpanel('{{ addslashes($targetApplication->campaign->department) }}')" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.45rem 0.9rem;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                        <span>+ Empanel New Expert for {{ $targetApplication->campaign->department }}</span>
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
        <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.88); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); display: flex; align-items: center; justify-content: center; z-index: 1600; padding: 1.5rem;">
            <div class="card" style="width: 100%; max-width: 720px; max-height: 92vh; overflow-y: auto; border-color: var(--secondary-glow); box-shadow: 0 25px 60px rgba(0,0,0,0.9); background: var(--bg-card-solid); border-radius: var(--radius-lg);">
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
                            </select>
                        </x-select>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <x-input label="Affiliated University / Institution *" :error="$errors->first('university')">
                            <input type="text" class="form-control" wire:model="university" placeholder="e.g. National University of Sciences & Technology">
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
    <!-- MODAL 3: VIEW ACADEMIC REVIEW REPORTS                          -->
    <!-- ============================================================== -->
    @if($showReportsModal && $viewingApplication)
        @php
            $submitted = $applicationReviews->where('status', 'SUBMITTED');
            $avgScore = $submitted->count() > 0 ? $submitted->avg('overall_score') : null;
        @endphp
        <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 1.5rem;">
            <div class="card" style="width: 100%; max-width: 860px; max-height: 90vh; overflow-y: auto; border-color: var(--border-color); box-shadow: var(--shadow-lg); background: var(--bg-card-solid);">
                <div class="flex justify-between align-center mb-4" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                    <div>
                        <h3 class="card-title" style="margin: 0; font-size: 1.25rem;">
                            External Peer Review Reports: {{ $viewingApplication->campaign->title }}
                        </h3>
                        <p class="card-subtitle" style="margin: 0.2rem 0 0;">
                            Candidate: <strong>{{ $viewingApplication->user->basicInfo ? trim($viewingApplication->user->basicInfo->first_name . ' ' . $viewingApplication->user->basicInfo->last_name) : $viewingApplication->user->email }}</strong> • Department: <strong>{{ $viewingApplication->campaign->department }}</strong>
                        </p>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="closeReportsModal">✕</button>
                </div>

                @if($submitted->count() > 0)
                    <!-- Executive Consensus Summary Banner -->
                    <div class="executive-summary-banner mb-6">
                        <div class="flex justify-between align-center" style="flex-wrap: wrap; gap: 1rem;">
                            <div>
                                <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--secondary); font-weight: 700; letter-spacing: 0.05em;">Consensus Dossier Assessment</span>
                                <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary); margin: 0.25rem 0 0;">
                                    {{ $submitted->count() }} of {{ $applicationReviews->count() }} Evaluation Reports Submitted
                                </h3>
                            </div>
                            <div style="display: flex; align-items: center; gap: 1.25rem;">
                                <div style="text-align: right;">
                                    <span style="font-size: 0.72rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">Average Peer Score</span>
                                    <div style="font-size: 1.6rem; font-weight: 900; color: {{ $avgScore >= 80 ? 'var(--success)' : ($avgScore >= 60 ? 'var(--warning)' : 'var(--danger)') }};">
                                        {{ number_format($avgScore, 1) }} <span style="font-size: 0.85rem; color: var(--color-text-secondary); font-weight: 600;">/ 100</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="flex-col gap-4">
                    @forelse($applicationReviews as $ar)
                        <div class="card" style="background: var(--bg-surface); border-color: var(--border-color); padding: 1.25rem;">
                            <div class="flex justify-between align-center mb-3" style="flex-wrap: wrap; gap: 0.5rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, rgba(6,182,212,0.2), rgba(99,102,241,0.2)); color: var(--secondary); font-weight: 800; font-size: 0.9rem; display: flex; align-items: center; justify-content: center;">
                                        {{ strtoupper(substr($ar->reviewer->reviewerProfile?->name ?? 'R', 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong style="font-size: 1rem; color: var(--color-text-primary);">
                                            {{ $ar->reviewer->reviewerProfile?->name ?? 'External Reviewer' }}
                                        </strong>
                                        <div style="font-size: 0.78rem; color: var(--color-text-secondary);">
                                            {{ $ar->reviewer->reviewerProfile?->academic_rank }} • {{ $ar->reviewer->reviewerProfile?->university }} ({{ $ar->reviewer->reviewerProfile?->subject }})
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    @if($ar->status === 'SUBMITTED')
                                        <span class="badge" style="background: rgba(16,185,129,0.15); color: var(--success); font-weight: 700;">✓ Report Submitted</span>
                                    @else
                                        <span class="badge" style="background: rgba(245,158,11,0.15); color: var(--warning); font-weight: 700;">⏳ Pending Evaluation</span>
                                    @endif
                                </div>
                            </div>

                            @if($ar->status === 'SUBMITTED')
                                <div class="grid grid-cols-2 gap-4 mb-3" style="background: rgba(0,0,0,0.3); padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                                    <div>
                                        <span style="font-size: 0.75rem; color: var(--color-text-muted); text-transform: uppercase; font-weight: 700;">Overall Score</span><br>
                                        <strong style="color: {{ $ar->overall_score >= 80 ? 'var(--success)' : ($ar->overall_score >= 60 ? 'var(--warning)' : 'var(--danger)') }}; font-size: 1.35rem; font-weight: 900;">
                                            {{ $ar->overall_score }} <span style="font-size: 0.8rem; color: var(--color-text-secondary); font-weight: 600;">/ 100</span>
                                        </strong>
                                    </div>
                                    <div>
                                        <span style="font-size: 0.75rem; color: var(--color-text-muted); text-transform: uppercase; font-weight: 700;">Recommendation</span><br>
                                        <span class="badge" style="margin-top: 0.25rem; font-size: 0.85rem; font-weight: 700; background: {{ in_array($ar->recommendation, ['STRONGLY_RECOMMENDED', 'RECOMMENDED']) ? 'rgba(16,185,129,0.15)' : 'rgba(239,68,68,0.15)' }}; color: {{ in_array($ar->recommendation, ['STRONGLY_RECOMMENDED', 'RECOMMENDED']) ? 'var(--success)' : 'var(--danger)' }};">
                                            {{ str_replace('_', ' ', $ar->recommendation) }}
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <strong style="font-size: 0.8rem; color: var(--color-text-secondary); text-transform: uppercase; letter-spacing: 0.05em;">Qualitative Expert Evaluation Remarks:</strong>
                                    <div class="reviewer-quote-box mt-2">
                                        "{{ $ar->report_text }}"
                                    </div>
                                </div>

                                <div class="flex justify-between align-center mt-3" style="font-size: 0.75rem; color: var(--color-text-muted);">
                                    <span>Submitted on: <strong>{{ $ar->submitted_at?->format('d M Y, h:i A') }}</strong></span>
                                    @if($ar->evaluation_file_path)
                                        <a href="/storage/{{ $ar->evaluation_file_path }}" target="_blank" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; text-decoration: none;">
                                            📥 Download Signed Evaluation PDF
                                        </a>
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
                    <button type="button" class="btn btn-secondary" wire:click="closeReportsModal">Close Reports</button>
                </div>
            </div>
        </div>
    @endif
</div>
