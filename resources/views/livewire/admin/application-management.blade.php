<div>
    <!-- Executive Control Header -->
    <div class="page-hero-header">
        <div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div class="brand-icon-box">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" style="color: #ffffff;">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                </div>
                <div>
                    <h1>Applications & Candidacies Operations Desk</h1>
                    <p>Dispatch peer reviews, evaluate referee verdicts, scrutinize eligibility, and progress lifecycle transitions.</p>
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            @if(!$isFormOpen && !$isDetailsOpen)
                @can('create-application')
                    <button type="button" class="btn btn-primary" wire:click="openCreateApplicationForm" style="display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 18px -2px rgba(99,102,241,0.5); padding: 0.65rem 1.35rem; font-weight: 700; border-radius: 10px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>+ Register Application</span>
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
                    <span class="metric-label">Total Applications</span>
                    <div class="metric-icon-wrap">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
                        </svg>
                    </div>
                </div>
                <div class="metric-value" style="color: var(--primary);">{{ $stats['total'] }}</div>
                <div class="metric-subtext">Across all open campaigns</div>
            </div>

            <div class="metric-card" style="--card-accent: var(--success); --metric-icon-bg: var(--success-light); --metric-icon-color: var(--success);">
                <div class="metric-header">
                    <span class="metric-label">Paid & Settled</span>
                    <div class="metric-icon-wrap">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/>
                        </svg>
                    </div>
                </div>
                <div class="metric-value" style="color: var(--success);">{{ $stats['paid'] }}</div>
                <div class="metric-subtext">Kuickpay / 1Link settled</div>
            </div>

            <div class="metric-card" style="--card-accent: #3b82f6; --metric-icon-bg: rgba(59,130,246,0.12); --metric-icon-color: #3b82f6;">
                <div class="metric-header">
                    <span class="metric-label">Eligible Candidates</span>
                    <div class="metric-icon-wrap">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                    </div>
                </div>
                <div class="metric-value" style="color: #3b82f6;">{{ $stats['eligible'] }}</div>
                <div class="metric-subtext">Passed scrutiny verification</div>
            </div>

            <div class="metric-card" style="--card-accent: var(--secondary); --metric-icon-bg: var(--secondary-light); --metric-icon-color: var(--secondary);">
                <div class="metric-header">
                    <span class="metric-label">In Peer / Appeal</span>
                    <div class="metric-icon-wrap">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                </div>
                <div class="metric-value" style="color: var(--secondary);">{{ $stats['under_review'] }}</div>
                <div class="metric-subtext">Referee evaluation in progress</div>
            </div>

            <div class="metric-card" style="--card-accent: #8b5cf6; --metric-icon-bg: rgba(139,92,246,0.12); --metric-icon-color: #8b5cf6;">
                <div class="metric-header">
                    <span class="metric-label">Approved Selected</span>
                    <div class="metric-icon-wrap">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                    </div>
                </div>
                <div class="metric-value" style="color: #8b5cf6;">{{ $stats['approved'] }}</div>
                <div class="metric-subtext">Final selection board confirmed</div>
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
                        {{ $isEditMode ? 'Modify Application Status & Progression Stage' : 'Register New Application Record' }}
                    </h3>
                    <p class="card-subtitle" style="margin: 0.2rem 0 0;">
                        Assign candidate linkage, recruitment campaign, and transition operational status.
                    </p>
                </div>
                <button type="button" class="btn btn-secondary btn-sm" wire:click="resetForm">Cancel</button>
            </div>

            <form wire:submit.prevent="saveApplication">
                @if(!$isEditMode)
                    <div class="grid grid-cols-2 gap-5">
                        <x-select label="Select Applicant Account *" :error="$errors->first('app_user_id')">
                            <select class="form-control form-select" wire:model="app_user_id">
                                <option value="">-- Choose Account --</option>
                                @foreach($users as $au)
                                    <option value="{{ $au->id }}">{{ $au->email }} ({{ $au->basicInfo ? ($au->basicInfo->first_name . ' ' . $au->basicInfo->last_name) : 'No Profile' }})</option>
                                @endforeach
                            </select>
                        </x-select>
                        <x-select label="Select Recruitment Campaign *" :error="$errors->first('app_campaign_id')">
                            <select class="form-control form-select" wire:model="app_campaign_id">
                                <option value="">-- Choose Campaign --</option>
                                @foreach($campaigns as $ac)
                                    <option value="{{ $ac->id }}">{{ $ac->job_code }} - {{ $ac->title }} ({{ $ac->department }})</option>
                                @endforeach
                            </select>
                        </x-select>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-5 mt-4">
                    <x-select label="Application Processing Status *" :error="$errors->first('app_status')">
                        <select class="form-control form-select" wire:model="app_status">
                            @foreach($workflowStatuses as $ws)
                                <option value="{{ $ws->code }}">{{ $ws->label }} ({{ $ws->code }})</option>
                            @endforeach
                            <option value="DRAFT">Draft State</option>
                            <option value="SUBMITTED">Submitted</option>
                            <option value="PAID">Paid / Settled</option>
                            <option value="INCOMPLETE_DOSSIER">Incomplete Dossier</option>
                            <option value="ELIGIBLE">Eligible (Scrutiny Verified)</option>
                            <option value="INELIGIBLE">Ineligible (Disqualified)</option>
                            <option value="APPEALED">Appealed (Grievance Review)</option>
                            <option value="UNDER_REVIEW">Under External Peer Review</option>
                            <option value="APPROVED">Approved by Selection Board</option>
                            <option value="REJECTED">Rejected by Selection Board</option>
                        </select>
                    </x-select>

                    <x-input label="Disqualification / Ineligibility Justification" :error="$errors->first('app_disqualification_reason')">
                        <input type="text" class="form-control" wire:model="app_disqualification_reason" placeholder="e.g. Deficient post-PhD experience or missing equivalence certificate">
                    </x-input>
                </div>

                <div class="flex gap-4 mt-6" style="border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                    <button type="submit" class="btn btn-primary" style="padding: 0.55rem 1.35rem; font-weight: 700;">Save Application</button>
                    <button type="button" class="btn btn-secondary" wire:click="resetForm">Cancel</button>
                </div>
            </form>
        </div>
    @else
        <!-- Quick Status Segmented Tabs -->
        <div style="display: flex; gap: 0.5rem; background: var(--bg-tertiary); padding: 0.35rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color); width: fit-content; margin-bottom: 1.25rem; flex-wrap: wrap;">
            <button type="button" 
                    class="btn" 
                    wire:click="$set('statusFilter', '')"
                    style="padding: 0.45rem 1.15rem; font-size: 0.85rem; border-radius: var(--radius-md); font-weight: 700; display: inline-flex; align-items: center; gap: 0.45rem; transition: var(--transition-base); {{ empty($statusFilter) ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
                <span>All Applications</span>
                <span style="font-size: 0.72rem; padding: 0.1rem 0.45rem; border-radius: 999px; background: rgba(99,102,241,0.15); color: var(--primary); font-weight: 800;">{{ $stats['total'] }}</span>
            </button>

            <button type="button" 
                    class="btn" 
                    wire:click="$set('statusFilter', 'PAID')"
                    style="padding: 0.45rem 1.15rem; font-size: 0.85rem; border-radius: var(--radius-md); font-weight: 700; display: inline-flex; align-items: center; gap: 0.45rem; transition: var(--transition-base); {{ $statusFilter === 'PAID' ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
                <span>Paid / Scrutiny</span>
                <span style="font-size: 0.72rem; padding: 0.1rem 0.45rem; border-radius: 999px; background: rgba(16,185,129,0.15); color: var(--success); font-weight: 800;">{{ $stats['paid'] }}</span>
            </button>

            <button type="button" 
                    class="btn" 
                    wire:click="$set('statusFilter', 'ELIGIBLE')"
                    style="padding: 0.45rem 1.15rem; font-size: 0.85rem; border-radius: var(--radius-md); font-weight: 700; display: inline-flex; align-items: center; gap: 0.45rem; transition: var(--transition-base); {{ $statusFilter === 'ELIGIBLE' ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
                <span>Eligible</span>
                <span style="font-size: 0.72rem; padding: 0.1rem 0.45rem; border-radius: 999px; background: rgba(59,130,246,0.15); color: #3b82f6; font-weight: 800;">{{ $stats['eligible'] }}</span>
            </button>

            <button type="button" 
                    class="btn" 
                    wire:click="$set('statusFilter', 'UNDER_REVIEW')"
                    style="padding: 0.45rem 1.15rem; font-size: 0.85rem; border-radius: var(--radius-md); font-weight: 700; display: inline-flex; align-items: center; gap: 0.45rem; transition: var(--transition-base); {{ $statusFilter === 'UNDER_REVIEW' ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
                <span>In Peer Review</span>
                <span style="font-size: 0.72rem; padding: 0.1rem 0.45rem; border-radius: 999px; background: rgba(6,182,212,0.15); color: var(--secondary); font-weight: 800;">{{ $stats['under_review'] }}</span>
            </button>

            <button type="button" 
                    class="btn" 
                    wire:click="$set('statusFilter', 'APPROVED')"
                    style="padding: 0.45rem 1.15rem; font-size: 0.85rem; border-radius: var(--radius-md); font-weight: 700; display: inline-flex; align-items: center; gap: 0.45rem; transition: var(--transition-base); {{ $statusFilter === 'APPROVED' ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
                <span>Approved Board</span>
                <span style="font-size: 0.72rem; padding: 0.1rem 0.45rem; border-radius: 999px; background: rgba(139,92,246,0.15); color: #8b5cf6; font-weight: 800;">{{ $stats['approved'] }}</span>
            </button>
        </div>

        <!-- Reusable Dynamic Entity Listing Component: Responsive Card Grid & Table -->
        <x-entity-card-grid 
            :items="$applications" 
            entityName="Job Applications" 
            searchPlaceholder="Search by tracking number, applicant name, email, or CNIC..."
            :perPage="$perPage" 
            :viewMode="$viewMode" 
            exportAction="exportCsv" 
            importAction="openImportModal">
            
            <!-- Filters Slot -->
            <x-slot:filters>
                <div style="flex: 1; min-width: 200px;">
                    <select class="form-control form-select" wire:model.live="campaignFilter">
                        <option value="">All Job Campaigns</option>
                        @foreach($campaigns as $camp)
                            <option value="{{ $camp->id }}">{{ $camp->job_code }} - {{ $camp->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="flex: 1; min-width: 170px;">
                    <select class="form-control form-select" wire:model.live="statusFilter">
                        <option value="">All Statuses</option>
                        @foreach($workflowStatuses as $ws)
                            <option value="{{ $ws->code }}">{{ $ws->label }}</option>
                        @endforeach
                        <option value="DRAFT">Draft</option>
                        <option value="SUBMITTED">Submitted</option>
                        <option value="PAID">Paid / In Scrutiny</option>
                        <option value="INCOMPLETE_DOSSIER">Incomplete Dossier</option>
                        <option value="ELIGIBLE">Eligible</option>
                        <option value="INELIGIBLE">Ineligible</option>
                        <option value="APPEALED">Appealed</option>
                        <option value="UNDER_REVIEW">Under Review</option>
                        <option value="APPROVED">Approved</option>
                        <option value="REJECTED">Rejected</option>
                    </select>
                </div>

                @if(!empty($search) || !empty($campaignFilter) || !empty($statusFilter))
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="$set('search', ''); $set('campaignFilter', ''); $set('statusFilter', '');" style="display: inline-flex; align-items: center; gap: 0.35rem;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        <span>Reset Filters</span>
                    </button>
                @endif
            </x-slot:filters>

            <!-- Cards Slot (3 to 5 Cards Per Row) -->
            <x-slot:cards>
                @foreach($applications as $app)
                    @php 
                        $bStyle = $app->status_badge_style;
                        $reviewsCount = $app->reviews->count();
                        $reqReferees = $app->campaign->getRequiredPeerReviewersCount();
                        $displayName = $app->user->basicInfo ? ($app->user->basicInfo->first_name . ' ' . $app->user->basicInfo->last_name) : $app->user->email;
                        $isPaid = $app->billingLedger?->status === 'SETTLED';
                        $avatarTheme = $isPaid ? 'avatar-theme-emerald' : 'avatar-theme-amber';
                        $isClosedOrCompleted = $app->campaign?->is_closed || $app->campaign?->is_completed;
                        $isSuperAdmin = auth()->user()?->hasRole('super-admin');
                        $canModifyApp = $isSuperAdmin || !$isClosedOrCompleted;
                        $isFinalized = in_array($app->status, ['APPOINTED', 'OFFERED', 'SELECTED']);
                        $canDeleteApp = $isSuperAdmin || (!$isClosedOrCompleted && !$isFinalized);
                    @endphp

                    <x-entity-card 
                        :id="$app->id" 
                        :title="$displayName" 
                        :subtitle="$app->campaign->title"
                        :avatar="strtoupper(substr($app->user->basicInfo?->first_name ?: $app->user->email, 0, 1))"
                        :avatar-image="$app->user->basicInfo?->profile_picture_url"
                        :avatarClass="$avatarTheme">
                        
                        <!-- Badge Slot: Lifecycle Stage -->
                        <x-slot:badge>
                            <span class="badge" style="background: {{ $bStyle['bg'] }}; color: {{ $bStyle['text'] }}; border: 1px solid {{ $bStyle['border'] }}; font-weight: 800; font-size: 0.72rem;">
                                {{ $app->status_label }}
                            </span>
                        </x-slot:badge>

                        <!-- Tag Slot: Challan / Payment Status & Job Code -->
                        <x-slot:tag>
                            <div style="display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;">
                                @if($isPaid)
                                    <span class="badge badge-emerald" style="font-size: 0.68rem; font-weight: 700; padding: 0.15rem 0.45rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                                        <span>✓ PAID (PKR {{ number_format($app->billingLedger->amount, 0) }})</span>
                                    </span>
                                @else
                                    <span class="badge badge-amber" style="font-size: 0.68rem; font-weight: 700; padding: 0.15rem 0.45rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                                        <span>⏳ PENDING FEE</span>
                                    </span>
                                @endif
                                <span class="mono-pill" style="font-size: 0.68rem; padding: 0.15rem 0.45rem;">
                                    {{ $app->campaign->job_code }}
                                </span>
                            </div>
                        </x-slot:tag>

                        <!-- Card Body Metrics -->
                        <div class="entity-card-metric-row">
                            <span class="entity-card-metric-label">Tracking:</span>
                            <span class="mono-pill" style="color: var(--secondary); font-size: 0.74rem;">
                                {{ $app->tracking_number }}
                            </span>
                        </div>

                        <div class="entity-card-metric-row">
                            <span class="entity-card-metric-label">CNIC:</span>
                            <span class="entity-card-metric-value" style="font-family: 'JetBrains Mono', monospace; font-size: 0.76rem;">
                                {{ $app->user->basicInfo?->cnic_number ?: 'N/A' }}
                            </span>
                        </div>

                        @if($reqReferees > 0)
                            <div class="entity-card-metric-row">
                                <span class="entity-card-metric-label">Peer Experts:</span>
                                <span class="entity-card-metric-value">
                                    <span class="badge" style="background: {{ $reviewsCount >= $reqReferees ? 'rgba(16,185,129,0.15)' : 'rgba(236,72,153,0.15)' }}; color: {{ $reviewsCount >= $reqReferees ? '#10b981' : '#ec4899' }}; font-size: 0.68rem; font-weight: 700;">
                                        {{ $reviewsCount }}/{{ $reqReferees }} Empanelled
                                    </span>
                                </span>
                            </div>
                        @endif

                        <div class="entity-card-metric-row">
                            <span class="entity-card-metric-label">Submitted:</span>
                            <span class="entity-card-metric-value" style="color: var(--color-text-muted);">
                                {{ $app->created_at->format('M d, Y') }}
                            </span>
                        </div>

                        @if($app->disqualification_reason)
                            <div style="color: var(--danger); font-size: 0.72rem; margin-top: 0.35rem; padding: 0.25rem 0.45rem; background: rgba(239,68,68,0.08); border-radius: 4px;" title="{{ $app->disqualification_reason }}">
                                ⚠️ {{ Str::limit($app->disqualification_reason, 45) }}
                            </div>
                        @endif

                        <!-- Quick Actions (Hover Reveal Icons) -->
                        <x-slot:quickActions>
                            @if(auth()->user()->hasRole('super-admin') && $app->user && auth()->id() !== $app->user->id && !$app->user->hasRole('super-admin'))
                                <a href="{{ route('impersonate.start', $app->user->id) }}" 
                                   class="action-btn action-btn-impersonate" 
                                   data-tooltip="Impersonate Applicant" 
                                   aria-label="Impersonate Applicant">
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
                                    wire:click="viewApplicationDetails({{ $app->id }})" 
                                    data-tooltip="View Application Dossier" 
                                    aria-label="View Details">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>

                            @if($reqReferees > 0 || $app->campaign->track_type === 'ACADEMIC')
                                @if($canModifyApp)
                                    <button type="button" 
                                            class="action-btn" 
                                            style="color: #ec4899; border-color: rgba(236,72,153,0.3); background: rgba(236,72,153,0.08);"
                                            wire:click="openDispatchModal({{ $app->id }})" 
                                            data-tooltip="Dispatch Reviewers">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                            <path d="M10 2v7.31"/><path d="M14 9.3V1.99"/><path d="M8.5 2h7"/><path d="M14 9.3a6.5 6.5 0 1 1-4 0"/><path d="M5.52 16h12.96"/>
                                        </svg>
                                    </button>
                                @else
                                    <button type="button" 
                                            class="action-btn disabled" 
                                            disabled 
                                            data-tooltip="Locked: Campaign completed or closed" 
                                            style="opacity: 0.45; cursor: not-allowed;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                        </svg>
                                    </button>
                                @endif
                            @endif

                            @if($reviewsCount >= 1 && ($app->status === 'UNDER_REVIEW' || $reviewsCount >= 2))
                                @if($canModifyApp)
                                    <button type="button" 
                                            class="action-btn" 
                                            style="color: #10b981; border-color: rgba(16,185,129,0.35); background: rgba(16,185,129,0.1);"
                                            wire:click="evaluatePeerReviewResults({{ $app->id }})" 
                                            data-tooltip="Evaluate Peer Review Results (Passing Criteria)">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                                    </button>
                                @else
                                    <button type="button" 
                                            class="action-btn disabled" 
                                            disabled 
                                            data-tooltip="Locked: Campaign completed or closed" 
                                            style="opacity: 0.45; cursor: not-allowed;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                        </svg>
                                    </button>
                                @endif
                            @endif

                            @if($canModifyApp)
                                <button type="button" 
                                        class="action-btn action-btn-perms" 
                                        wire:click="openTransitionModal({{ $app->id }})" 
                                        data-tooltip="Advance Workflow Stage">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                        <path d="M21.5 2v6h-6"/><path d="M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/>
                                    </svg>
                                </button>
                            @else
                                <button type="button" 
                                        class="action-btn disabled" 
                                        disabled 
                                        data-tooltip="Workflow Locked: Position completed or closed" 
                                        style="opacity: 0.45; cursor: not-allowed;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                </button>
                            @endif
                        </x-slot:quickActions>

                        <!-- Kebab Dropdown Options Menu [ICON + TEXT] -->
                        <x-slot:menuActions>
                            @if($reviewsCount >= 1)
                                @if($canModifyApp)
                                    <x-entity-card-menu-item 
                                        label="Evaluate Peer Review Verdict" 
                                        click="evaluatePeerReviewResults({{ $app->id }})">
                                        <x-slot:icon>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                                        </x-slot:icon>
                                    </x-entity-card-menu-item>
                                @else
                                    <x-entity-card-menu-item 
                                        :disabled="true" 
                                        label="Evaluate Peer Review (Locked)" 
                                        tooltip="Locked: Campaign completed or closed">
                                        <x-slot:icon>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                        </x-slot:icon>
                                    </x-entity-card-menu-item>
                                @endif
                            @endif
                            <x-entity-card-menu-item 
                                label="View Application Dossier" 
                                click="viewApplicationDetails({{ $app->id }})">
                                <x-slot:icon>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                </x-slot:icon>
                            </x-entity-card-menu-item>

                            @if($reqReferees > 0 || $app->campaign->track_type === 'ACADEMIC')
                                @if($canModifyApp)
                                    <x-entity-card-menu-item 
                                        label="Dispatch Peer Reviewers" 
                                        click="openDispatchModal({{ $app->id }})">
                                        <x-slot:icon>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 2v7.31"/><path d="M14 9.3V1.99"/><path d="M8.5 2h7"/><path d="M14 9.3a6.5 6.5 0 1 1-4 0"/><path d="M5.52 16h12.96"/></svg>
                                        </x-slot:icon>
                                    </x-entity-card-menu-item>
                                @else
                                    <x-entity-card-menu-item 
                                        :disabled="true" 
                                        label="Dispatch Peer Reviewers (Locked)" 
                                        tooltip="Locked: Campaign completed or closed">
                                        <x-slot:icon>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                        </x-slot:icon>
                                    </x-entity-card-menu-item>
                                @endif
                            @endif

                            @if($canModifyApp)
                                <x-entity-card-menu-item 
                                    label="Advance Workflow Transition" 
                                    click="openTransitionModal({{ $app->id }})">
                                    <x-slot:icon>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.5 2v6h-6"/><path d="M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                                    </x-slot:icon>
                                </x-entity-card-menu-item>
                            @else
                                <x-entity-card-menu-item 
                                    :disabled="true" 
                                    label="Advance Workflow Transition (Locked)" 
                                    tooltip="Locked: Campaign completed or closed">
                                    <x-slot:icon>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                    </x-slot:icon>
                                </x-entity-card-menu-item>
                            @endif

                            @if($app->status !== 'ELIGIBLE' && $app->status !== 'APPROVED')
                                @if($canModifyApp)
                                    <x-entity-card-menu-item 
                                        label="Mark as Eligible for Board" 
                                        click="markAsEligible({{ $app->id }})">
                                        <x-slot:icon>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                        </x-slot:icon>
                                    </x-entity-card-menu-item>
                                @else
                                    <x-entity-card-menu-item 
                                        :disabled="true" 
                                        label="Mark as Eligible (Locked)" 
                                        tooltip="Locked: Campaign completed or closed">
                                        <x-slot:icon>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                        </x-slot:icon>
                                    </x-entity-card-menu-item>
                                @endif
                            @endif

                            @if($canModifyApp)
                                <x-entity-card-menu-item 
                                    label="Mark Incomplete Dossier" 
                                    click="openIncompleteModal({{ $app->id }})">
                                    <x-slot:icon>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                    </x-slot:icon>
                                </x-entity-card-menu-item>
                            @else
                                <x-entity-card-menu-item 
                                    :disabled="true" 
                                    label="Mark Incomplete Dossier (Locked)" 
                                    tooltip="Locked: Campaign completed or closed">
                                    <x-slot:icon>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                    </x-slot:icon>
                                </x-entity-card-menu-item>
                            @endif

                            @if($canModifyApp)
                                <x-entity-card-menu-item 
                                    label="Reject / Disqualify Application" 
                                    click="openRejectModal({{ $app->id }})">
                                    <x-slot:icon>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                    </x-slot:icon>
                                </x-entity-card-menu-item>
                            @else
                                <x-entity-card-menu-item 
                                    :disabled="true" 
                                    label="Reject / Disqualify (Locked)" 
                                    tooltip="Locked: Campaign completed or closed">
                                    <x-slot:icon>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                    </x-slot:icon>
                                </x-entity-card-menu-item>
                            @endif

                            @if($canModifyApp)
                                <x-entity-card-menu-item 
                                    label="Edit Application Record" 
                                    click="openEditApplicationForm({{ $app->id }})">
                                    <x-slot:icon>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </x-slot:icon>
                                </x-entity-card-menu-item>
                            @else
                                <x-entity-card-menu-item 
                                    :disabled="true" 
                                    label="Edit Application Record (Locked)" 
                                    tooltip="Locked: Campaign completed or closed">
                                    <x-slot:icon>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                    </x-slot:icon>
                                </x-entity-card-menu-item>
                            @endif

                            @if(auth()->user()->hasRole('super-admin') && $app->user && auth()->id() !== $app->user->id && !$app->user->hasRole('super-admin'))
                                <div class="entity-card-menu-divider"></div>
                                <x-entity-card-menu-item 
                                    label="Impersonate Applicant" 
                                    href="{{ route('impersonate.start', $app->user->id) }}" 
                                    :impersonate="true">
                                    <x-slot:icon>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                    </x-slot:icon>
                                </x-entity-card-menu-item>
                            @endif

                            @can('delete-application')
                                <div class="entity-card-menu-divider"></div>
                                @if($canDeleteApp)
                                    <x-entity-card-menu-item 
                                        label="Delete Application" 
                                        click="deleteApplication({{ $app->id }})" 
                                        :danger="true"
                                        onclick="confirm('Are you sure you want to permanently delete this application record?') || event.stopImmediatePropagation()">
                                        <x-slot:icon>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                        </x-slot:icon>
                                    </x-entity-card-menu-item>
                                @else
                                    <x-entity-card-menu-item 
                                        :disabled="true" 
                                        label="Delete Application (Locked)" 
                                        tooltip="Locked: Finalized or closed campaign applications cannot be deleted without Super Admin privileges">
                                        <x-slot:icon>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                        </x-slot:icon>
                                    </x-entity-card-menu-item>
                                @endif
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
                            <th style="width: 24%;">Candidate Reference</th>
                            <th style="width: 22%;">Target Vacancy & Quotas</th>
                            <th style="width: 14%;">Fee Voucher</th>
                            <th style="width: 15%;">Lifecycle Stage</th>
                            <th style="width: 25%; text-align: right;">Direct Operational Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applications as $app)
                            @php 
                                $bStyle = $app->status_badge_style;
                                $reviewsCount = $app->reviews->count();
                                $reqReferees = $app->campaign->getRequiredPeerReviewersCount();
                                $isClosedOrCompleted = $app->campaign?->is_closed || $app->campaign?->is_completed;
                                $isSuperAdmin = auth()->user()?->hasRole('super-admin');
                                $canModifyApp = $isSuperAdmin || !$isClosedOrCompleted;
                                $isFinalized = in_array($app->status, ['APPOINTED', 'OFFERED', 'SELECTED']);
                                $canDeleteApp = $isSuperAdmin || (!$isClosedOrCompleted && !$isFinalized);
                            @endphp
                            <tr wire:key="app-row-{{ $app->id }}">
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.85rem;">
                                        @if($app->user->basicInfo?->profile_picture_url)
                                            <div class="user-avatar-circle" style="width: 40px; height: 40px; border-radius: 12px; overflow: hidden; padding: 0; flex-shrink: 0; border: 1.5px solid var(--border-color); box-shadow: var(--shadow-sm);">
                                                <img src="{{ $app->user->basicInfo->profile_picture_url }}" alt="{{ $app->user->email }}" style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                        @else
                                            <div class="user-avatar-circle" style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, rgba(99,102,241,0.25), rgba(6,182,212,0.25)); color: var(--primary); font-weight: 800; font-size: 1rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid rgba(99,102,241,0.25);">
                                                {{ strtoupper(substr($app->user->basicInfo?->first_name ?: $app->user->email, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div style="font-weight: 800; color: var(--color-text-primary); font-size: 0.96rem;">
                                                {{ $app->user->basicInfo ? ($app->user->basicInfo->first_name . ' ' . $app->user->basicInfo->last_name) : $app->user->email }}
                                            </div>
                                            <div style="font-size: 0.78rem; color: var(--color-text-secondary); display: flex; align-items: center; gap: 0.4rem; margin-top: 2px;">
                                                <code style="font-family: 'JetBrains Mono', monospace; color: var(--secondary); font-size: 0.75rem;">{{ $app->tracking_number }}</code>
                                                @if($app->user->basicInfo?->cnic_number)
                                                    <span style="opacity: 0.4;">•</span>
                                                    <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.74rem;">{{ $app->user->basicInfo->cnic_number }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong style="color: var(--color-text-primary); font-size: 0.94rem;">{{ $app->campaign->title }}</strong>
                                        <div style="display: flex; gap: 0.4rem; align-items: center; margin-top: 3px; flex-wrap: wrap;">
                                            <span style="font-size: 0.74rem; font-family: 'JetBrains Mono', monospace; font-weight: 700; color: var(--primary);">
                                                {{ $app->campaign->job_code }}
                                            </span>
                                            <span class="badge" style="background: rgba(99,102,241,0.1); color: var(--primary); font-size: 0.68rem; padding: 0.12rem 0.45rem; font-weight: 700;">
                                                {{ $app->campaign->track_type }}
                                            </span>
                                            @if($reqReferees > 0)
                                                <span class="badge" style="background: {{ $reviewsCount >= $reqReferees ? 'rgba(16,185,129,0.15)' : 'rgba(236,72,153,0.15)' }}; color: {{ $reviewsCount >= $reqReferees ? '#10b981' : '#ec4899' }}; font-size: 0.68rem; font-weight: 700;">
                                                    Referees: {{ $reviewsCount }}/{{ $reqReferees }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($app->billingLedger?->status === 'SETTLED')
                                        <span class="badge badge-paid" style="font-size: 0.76rem; font-weight: 700;">
                                            ✓ SETTLED
                                        </span>
                                        <div style="font-size: 0.72rem; color: var(--success); font-weight: 700; margin-top: 2px;">
                                            PKR {{ number_format($app->billingLedger->amount, 0) }}
                                        </div>
                                    @else
                                        <span class="badge" style="background: rgba(245,158,11,0.15); color: var(--warning); border: 1px solid rgba(245,158,11,0.3); font-size: 0.75rem; font-weight: 700;">
                                            ⏳ PENDING
                                        </span>
                                        @if($app->billingLedger)
                                            <div style="font-size: 0.7rem; color: var(--color-text-muted); font-family: 'JetBrains Mono', monospace; margin-top: 2px;">
                                                {{ $app->billingLedger->consumer_id }}
                                            </div>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <span class="badge" style="background: {{ $bStyle['bg'] }}; color: {{ $bStyle['text'] }}; border: 1px solid {{ $bStyle['border'] }}; font-weight: 800; font-size: 0.76rem;">
                                        {{ $app->status_label }}
                                    </span>
                                    @if($app->disqualification_reason)
                                        <div style="color: var(--danger); font-size: 0.72rem; margin-top: 3px; max-width: 170px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $app->disqualification_reason }}">
                                            ⚠️ {{ $app->disqualification_reason }}
                                        </div>
                                    @endif
                                </td>
                                <td style="text-align: right; vertical-align: middle;">
                                    <div class="action-btn-group justify-end" x-data="{ openMenu: false }">
                                        <button type="button" class="action-btn action-btn-view" wire:click="viewApplicationDetails({{ $app->id }})" data-tooltip="View Application Dossier">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </button>
                                        @if($canModifyApp)
                                            <button type="button" class="action-btn action-btn-perms" wire:click="openTransitionModal({{ $app->id }})" data-tooltip="Advance Workflow Transition">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21.5 2v6h-6"/><path d="M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                                            </button>
                                        @else
                                            <button type="button" class="action-btn disabled" disabled data-tooltip="Workflow Locked: Position completed or closed" style="opacity: 0.45; cursor: not-allowed;">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
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
                                                @if($reqReferees > 0 || $app->campaign->track_type === 'ACADEMIC')
                                                    @if($canModifyApp)
                                                        <button type="button" class="table-action-menu-item" wire:click="openDispatchModal({{ $app->id }})">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="color: #ec4899;"><path d="M10 2v7.31"/><path d="M14 9.3V1.99"/><path d="M8.5 2h7"/><path d="M14 9.3a6.5 6.5 0 1 1-4 0"/><path d="M5.52 16h12.96"/></svg>
                                                            <span>Dispatch to Reviewers</span>
                                                        </button>
                                                    @else
                                                        <button type="button" class="table-action-menu-item disabled" disabled title="Locked: Position completed or closed">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                                            <span>Dispatch Reviewers (Locked)</span>
                                                        </button>
                                                    @endif
                                                @endif

                                                @if($reviewsCount >= 1 && ($app->status === 'UNDER_REVIEW' || $reviewsCount >= 2))
                                                    @if($canModifyApp)
                                                        <button type="button" class="table-action-menu-item success" wire:click="evaluatePeerReviewResults({{ $app->id }})">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                                                            <span>Evaluate Peer Review</span>
                                                        </button>
                                                    @else
                                                        <button type="button" class="table-action-menu-item disabled" disabled title="Locked: Position completed or closed">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                                            <span>Evaluate Review (Locked)</span>
                                                        </button>
                                                    @endif
                                                @endif

                                                @if($app->status !== 'ELIGIBLE' && $app->status !== 'APPROVED')
                                                    @if($canModifyApp)
                                                        <button type="button" class="table-action-menu-item success" wire:click="markAsEligible({{ $app->id }})">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                                            <span>Mark as Eligible</span>
                                                        </button>
                                                    @else
                                                        <button type="button" class="table-action-menu-item disabled" disabled title="Locked: Position completed or closed">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                                            <span>Mark Eligible (Locked)</span>
                                                        </button>
                                                    @endif
                                                @endif

                                                @if($canModifyApp)
                                                    <button type="button" class="table-action-menu-item" wire:click="openIncompleteModal({{ $app->id }})">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="color: var(--warning);"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                                        <span>Mark Incomplete</span>
                                                    </button>
                                                    <button type="button" class="table-action-menu-item danger" wire:click="openRejectModal({{ $app->id }})">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                                        <span>Reject / Disqualify</span>
                                                    </button>
                                                    <button type="button" class="table-action-menu-item" wire:click="openEditApplicationForm({{ $app->id }})">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                        <span>Edit Application</span>
                                                    </button>
                                                @else
                                                    <button type="button" class="table-action-menu-item disabled" disabled title="Locked: Position completed or closed">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                                        <span>Edit Application (Locked)</span>
                                                    </button>
                                                @endif

                                                @if(auth()->user()->hasRole('super-admin') && $app->user && auth()->id() !== $app->user->id && !$app->user->hasRole('super-admin'))
                                                    <a href="{{ route('impersonate.start', $app->user->id) }}" class="table-action-menu-item impersonate">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                                        <span>Impersonate Applicant</span>
                                                    </a>
                                                @endif

                                                @can('delete-application')
                                                    <div class="table-action-menu-divider"></div>
                                                    @if($canDeleteApp)
                                                        <button type="button" class="table-action-menu-item danger" wire:click="deleteApplication({{ $app->id }})" wire:confirm="Are you sure you want to permanently delete this application record?">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                                            <span>Delete Application</span>
                                                        </button>
                                                    @else
                                                        <button type="button" class="table-action-menu-item disabled" disabled title="Locked: Finalized or closed campaign applications cannot be deleted">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                                            <span>Delete Application (Locked)</span>
                                                        </button>
                                                    @endif
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

        <!-- Universal Import Modal for Applications -->
        <x-entity-import-modal 
            :isOpen="$isImportModalOpen" 
            entityName="Job Applications" 
            :sampleColumns="['tracking_number', 'user_email', 'campaign_code', 'status']" />
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 1: DIRECT DISPATCH TO PEER REVIEWERS (FROM /APPLICATIONS ROUTE)     -->
    <!-- ========================================================================= -->
    @if($showDispatchModal && $dispatchApplication)
        <div style="position: fixed; inset: 0; background: rgba(0, 0, 0, 0.75); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 1.5rem;">
            <div class="card" style="width: 100%; max-width: 900px; max-height: 90vh; overflow-y: auto; background: #0f172a; border-color: rgba(236, 72, 153, 0.35); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); padding: 1.75rem;">
                
                <!-- Modal Header -->
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.3rem;">
                            <span class="badge" style="background: rgba(236, 72, 153, 0.2); color: #ec4899; font-size: 0.72rem; font-weight: 800;">
                                EXTERNAL PEER REVIEW DISPATCH
                            </span>
                            <span class="badge" style="background: rgba(255, 255, 255, 0.06); color: var(--color-text-secondary); font-size: 0.72rem;">
                                {{ $dispatchApplication->tracking_number }}
                            </span>
                        </div>
                        <h3 style="font-size: 1.35rem; font-weight: 800; color: var(--color-text-primary); margin: 0;">
                            Dispatch Candidacy for External Academic Peer Review
                        </h3>
                        <p style="font-size: 0.85rem; color: var(--color-text-secondary); margin: 0.25rem 0 0 0;">
                            Candidate: <strong style="color: var(--color-text-primary);">{{ $dispatchApplication->user->basicInfo ? ($dispatchApplication->user->basicInfo->first_name . ' ' . $dispatchApplication->user->basicInfo->last_name) : $dispatchApplication->user->email }}</strong> &bull; {{ $dispatchApplication->campaign->title }}
                        </p>
                    </div>
                    <button type="button" wire:click="closeDispatchModal" style="background: none; border: none; color: var(--color-text-muted); cursor: pointer; font-size: 1.25rem;">✕</button>
                </div>

                <!-- Statutory Quorum Callout -->
                <div style="background: rgba(236, 72, 153, 0.08); border: 1px solid rgba(236, 72, 153, 0.25); border-radius: var(--radius-sm); padding: 0.85rem 1.25rem; margin-bottom: 1.25rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
                    <div>
                        <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 800; color: #ec4899; letter-spacing: 0.05em;">
                            STATUTORY REFEREE QUOTA
                        </div>
                        <div style="font-size: 0.9rem; font-weight: 700; color: var(--color-text-primary);">
                            {{ $dispatchTargetRequiredExperts }} Experts Required &bull; {{ $dispatchAlreadyAssignedCount }} Currently Assigned &bull; <span style="color: #ec4899;">{{ count($selectedReviewerIds) }} Selected Now</span>
                        </div>
                    </div>

                    <button type="button" class="btn btn-secondary btn-sm" wire:click="openEmpanelModal" style="font-size: 0.8rem; font-weight: 700; color: var(--primary); border-color: rgba(99,102,241,0.4); display: inline-flex; align-items: center; gap: 0.35rem;">
                        <span>+ Empanel New Expert</span>
                    </button>
                </div>

                <!-- Reviewer Filtering & Search -->
                <div style="display: flex; gap: 0.75rem; align-items: center; margin-bottom: 1rem; flex-wrap: wrap;">
                    <div style="flex: 2; min-width: 220px; position: relative;">
                        <input type="text" class="form-control" wire:model.live.debounce.250ms="dispatchReviewerSearch" placeholder="Search experts by name, university, or country..." style="font-size: 0.85rem; padding-left: 2rem;" />
                        <span style="position: absolute; left: 0.7rem; top: 50%; transform: translateY(-50%); font-size: 0.8rem; color: var(--color-text-muted);">🔍</span>
                    </div>

                    <div style="flex: 1; min-width: 180px;">
                        <select class="form-control form-select" wire:model.live="dispatchDisciplineFilter" style="font-size: 0.85rem;">
                            <option value="">All Allied Disciplines</option>
                            @foreach($availableDisciplines as $disc)
                                <option value="{{ $disc }}">{{ $disc }}</option>
                            @endforeach
                        </select>
                    </div>

                    <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.8rem; cursor: pointer; color: var(--color-text-secondary);">
                        <input type="checkbox" wire:model.live="filterByFieldOnly" style="width: 15px; height: 15px;" />
                        <span>Allied Field Match Only</span>
                    </label>
                </div>

                <!-- Reviewers Selection List -->
                <div style="max-height: 240px; overflow-y: auto; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: var(--radius-sm); margin-bottom: 1.25rem;">
                    @forelse($fieldMatchedReviewers as $revProfile)
                        @php $isSelected = in_array($revProfile->user_id, $selectedReviewerIds); @endphp
                        <div wire:click="toggleReviewerSelection({{ $revProfile->user_id }})" 
                            style="padding: 0.75rem 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.04); display: flex; justify-content: space-between; align-items: center; cursor: pointer; background: {{ $isSelected ? 'rgba(236, 72, 153, 0.12)' : 'transparent' }}; transition: background 0.15s ease;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <input type="checkbox" checked="{{ $isSelected ? 'checked' : '' }}" style="width: 17px; height: 17px; pointer-events: none;" />
                                <div>
                                    <div style="font-weight: 700; font-size: 0.9rem; color: var(--color-text-primary);">
                                        {{ $revProfile->name }}
                                        <span class="badge" style="background: rgba(99, 102, 241, 0.15); color: var(--primary); font-size: 0.68rem; margin-left: 0.35rem;">
                                            {{ $revProfile->academic_rank }}
                                        </span>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--color-text-muted); margin-top: 2px;">
                                        {{ $revProfile->university }} &bull; {{ $revProfile->country }} &bull; <strong style="color: var(--secondary);">{{ $revProfile->subject }}</strong>
                                    </div>
                                </div>
                            </div>

                            <span class="badge" style="background: {{ $isSelected ? '#ec4899' : 'rgba(255,255,255,0.06)' }}; color: {{ $isSelected ? '#ffffff' : 'var(--color-text-muted)' }}; font-size: 0.72rem; font-weight: 700;">
                                {{ $isSelected ? 'Selected' : 'Click to Select' }}
                            </span>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 2rem; color: var(--color-text-muted); font-size: 0.85rem;">
                            No matching experts found. Try unchecking "Allied Field Match Only" or click "+ Empanel New Expert" to register a new referee.
                        </div>
                    @endforelse
                </div>

                @error('selectedReviewerIds')
                    <div class="alert alert-danger mb-4" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 0.75rem 1rem; border-radius: var(--radius-sm); font-size: 0.85rem;">
                        {{ $message }}
                    </div>
                @enderror

                <!-- Review Instructions & Due Date -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="md:col-span-2">
                        <label class="form-label" style="font-size: 0.76rem; font-weight: 700;">CONFIDENTIAL EVALUATION GUIDANCE NOTES</label>
                        <textarea class="form-control" rows="2" wire:model.defer="dispatchReviewNotes" style="font-size: 0.82rem;"></textarea>
                    </div>
                    <div>
                        <label class="form-label" style="font-size: 0.76rem; font-weight: 700;">REVIEW REPORT DUE DATE</label>
                        <input type="date" class="form-control" wire:model.defer="dispatchReviewDueDate" style="font-size: 0.85rem;" />
                    </div>
                </div>

                <!-- Modal Actions -->
                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 1rem; border-top: 1px solid rgba(255, 255, 255, 0.08);">
                    <button type="button" class="btn btn-secondary" wire:click="closeDispatchModal" style="padding: 0.6rem 1.25rem; font-size: 0.85rem;">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="dispatchApplicationToReviewers" style="padding: 0.6rem 1.75rem; font-size: 0.88rem; font-weight: 700; background: #ec4899; border-color: #ec4899;">
                        Dispatched to {{ count($selectedReviewerIds) }} Expert(s)
                    </button>
                </div>

            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- SUB-MODAL: EMPANEL NEW EXPERT ON THE FLY                                  -->
    <!-- ========================================================================= -->
    @if($showEmpanelModal)
        <div style="position: fixed; inset: 0; background: rgba(0, 0, 0, 0.85); backdrop-filter: blur(10px); display: flex; align-items: center; justify-content: center; z-index: 10000; padding: 1.5rem;">
            <div class="card" style="width: 100%; max-width: 650px; background: #0f172a; border-color: rgba(99, 102, 241, 0.4); padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                    <h4 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: var(--color-text-primary);">
                        + Empanel & Assign Academic Subject Expert
                    </h4>
                    <button type="button" wire:click="closeEmpanelModal" style="background: none; border: none; color: var(--color-text-muted); cursor: pointer; font-size: 1.2rem;">✕</button>
                </div>

                <form wire:submit.prevent="createAndAssignReviewer">
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="form-label" style="font-size: 0.75rem; font-weight: 700;">FULL NAME & TITLE *</label>
                            <input type="text" class="form-control" wire:model.defer="empanelName" placeholder="Prof. Dr. Jane Doe" required />
                            @error('empanelName') <span class="text-danger" style="font-size: 0.72rem;">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="form-label" style="font-size: 0.75rem; font-weight: 700;">ACADEMIC EMAIL *</label>
                            <input type="email" class="form-control" wire:model.defer="empanelEmail" placeholder="referee@university.edu" required />
                            @error('empanelEmail') <span class="text-danger" style="font-size: 0.72rem;">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="form-label" style="font-size: 0.75rem; font-weight: 700;">AFFILIATED INSTITUTION *</label>
                            <input type="text" class="form-control" wire:model.defer="empanelUniversity" placeholder="Oxford / MIT / NUST" required />
                            @error('empanelUniversity') <span class="text-danger" style="font-size: 0.72rem;">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="form-label" style="font-size: 0.75rem; font-weight: 700;">SUBJECT / DISCIPLINE *</label>
                            <input type="text" class="form-control" wire:model.defer="empanelSubject" placeholder="Computer Science" required />
                            @error('empanelSubject') <span class="text-danger" style="font-size: 0.72rem;">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div>
                            <label class="form-label" style="font-size: 0.75rem; font-weight: 700;">ACADEMIC RANK *</label>
                            <select class="form-control form-select" wire:model.defer="empanelRank">
                                <option value="Distinguished Professor">Distinguished Professor</option>
                                <option value="Professor">Tenured Professor</option>
                                <option value="Associate Professor">Associate Professor</option>
                                <option value="Industry Fellow">Industry Research Fellow</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" style="font-size: 0.75rem; font-weight: 700;">COUNTRY *</label>
                            <select class="form-control form-select" wire:model.defer="empanelCountry">
                                @foreach($countries as $cntry)
                                    <option value="{{ $cntry }}">{{ $cntry }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; padding-top: 1rem; border-top: 1px solid rgba(255, 255, 255, 0.08);">
                        <button type="button" class="btn btn-secondary" wire:click="closeEmpanelModal" style="padding: 0.5rem 1rem; font-size: 0.82rem;">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1.4rem; font-size: 0.85rem; font-weight: 700;">Empanel & Assign</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 2: MARK AS INCOMPLETE DOSSIER DIRECT ACTION                         -->
    <!-- ========================================================================= -->
    @if($showIncompleteModal)
        <div style="position: fixed; inset: 0; background: rgba(0, 0, 0, 0.75); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 1.5rem;">
            <div class="card" style="width: 100%; max-width: 580px; background: #0f172a; border-color: rgba(245, 158, 11, 0.4); padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                    <div>
                        <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b; font-size: 0.72rem; font-weight: 800; margin-bottom: 0.25rem;">
                            SCRUTINY DEFICIENCY NOTICE
                        </span>
                        <h3 style="margin: 0; font-size: 1.2rem; font-weight: 800; color: var(--color-text-primary);">
                            Mark Application as Incomplete Dossier
                        </h3>
                    </div>
                    <button type="button" wire:click="closeIncompleteModal" style="background: none; border: none; color: var(--color-text-muted); cursor: pointer; font-size: 1.2rem;">✕</button>
                </div>

                <p style="font-size: 0.82rem; color: var(--color-text-secondary); margin-bottom: 1rem;">
                    Record the specific credential deficiencies or missing documents. The candidate will see this justification and be alerted to provide required documents.
                </p>

                <div class="mb-4">
                    <label class="form-label" style="font-size: 0.76rem; font-weight: 700;">DEFICIENCY JUSTIFICATION & MISSING DOCUMENTS *</label>
                    <textarea class="form-control" rows="4" wire:model.defer="incompleteDeficiencyNotes" placeholder="e.g. Official MSc degree transcript not certified; Missing 3 years industrial experience letter..." required></textarea>
                    @error('incompleteDeficiencyNotes') <span class="text-danger" style="font-size: 0.72rem;">{{ $message }}</span> @enderror
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" class="btn btn-secondary" wire:click="closeIncompleteModal" style="padding: 0.55rem 1.15rem; font-size: 0.82rem;">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="markAsIncompleteDossier" style="background: #f59e0b; border-color: #f59e0b; padding: 0.55rem 1.5rem; font-size: 0.85rem; font-weight: 700;">
                        Mark as Incomplete
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 3: REJECT / DISQUALIFY APPLICATION DIRECT ACTION                    -->
    <!-- ========================================================================= -->
    @if($showRejectModal)
        <div style="position: fixed; inset: 0; background: rgba(0, 0, 0, 0.75); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 1.5rem;">
            <div class="card" style="width: 100%; max-width: 580px; background: #0f172a; border-color: rgba(239, 68, 68, 0.4); padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                    <div>
                        <span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #ef4444; font-size: 0.72rem; font-weight: 800; margin-bottom: 0.25rem;">
                            FORMAL REJECTION / DISQUALIFICATION
                        </span>
                        <h3 style="margin: 0; font-size: 1.2rem; font-weight: 800; color: var(--color-text-primary);">
                            Reject / Disqualify Candidacy
                        </h3>
                    </div>
                    <button type="button" wire:click="closeRejectModal" style="background: none; border: none; color: var(--color-text-muted); cursor: pointer; font-size: 1.2rem;">✕</button>
                </div>

                <p style="font-size: 0.82rem; color: var(--color-text-secondary); margin-bottom: 1rem;">
                    Specify formal statutory justification for ineligibility or board rejection. This justification is permanently recorded for audit logs and appellate grievance review.
                </p>

                <div class="mb-4">
                    <label class="form-label" style="font-size: 0.76rem; font-weight: 700;">FORMAL DISQUALIFICATION / REJECTION REASON *</label>
                    <textarea class="form-control" rows="4" wire:model.defer="rejectReason" placeholder="e.g. Does not fulfill statutory criteria: Less than 5 years required post-PhD experience..." required></textarea>
                    @error('rejectReason') <span class="text-danger" style="font-size: 0.72rem;">{{ $message }}</span> @enderror
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <button type="button" class="btn btn-secondary" wire:click="closeRejectModal" style="padding: 0.55rem 1.15rem; font-size: 0.82rem;">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="rejectApplication" style="background: #ef4444; border-color: #ef4444; padding: 0.55rem 1.5rem; font-size: 0.85rem; font-weight: 700;">
                        Confirm Rejection
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 4: DYNAMIC LIFECYCLE STATUS TRANSITION DIRECT ACTION                -->
    <!-- ========================================================================= -->
    @if($showTransitionModal && $transitionApplication)
        <div style="position: fixed; inset: 0; background: rgba(0, 0, 0, 0.75); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 1.5rem;">
            <div class="card" style="width: 100%; max-width: 620px; background: #0f172a; border-color: rgba(99, 102, 241, 0.4); padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                    <div>
                        <span class="badge" style="background: rgba(99, 102, 241, 0.15); color: var(--primary); font-size: 0.72rem; font-weight: 800; margin-bottom: 0.25rem;">
                            DYNAMIC STATUS TRANSITION ENGINE
                        </span>
                        <h3 style="margin: 0; font-size: 1.2rem; font-weight: 800; color: var(--color-text-primary);">
                            Transition Lifecycle Stage
                        </h3>
                    </div>
                    <button type="button" wire:click="closeTransitionModal" style="background: none; border: none; color: var(--color-text-muted); cursor: pointer; font-size: 1.2rem;">✕</button>
                </div>

                <div style="background: rgba(15, 23, 42, 0.6); padding: 0.75rem 1rem; border-radius: var(--radius-sm); border: 1px solid rgba(255, 255, 255, 0.06); margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <div style="font-size: 0.72rem; text-transform: uppercase; color: var(--color-text-muted);">CURRENT STATUS</div>
                        <strong style="color: var(--color-text-primary); font-size: 0.95rem;">{{ $transitionApplication->status_label }}</strong>
                    </div>
                    <span class="badge" style="background: {{ $transitionApplication->status_badge_style['bg'] }}; color: {{ $transitionApplication->status_badge_style['text'] }}; border: 1px solid {{ $transitionApplication->status_badge_style['border'] }}; font-weight: 800; font-size: 0.78rem;">
                        {{ $transitionApplication->status }}
                    </span>
                </div>

                <!-- Gate Validation Errors Alert -->
                @if(!empty($transitionValidationErrors))
                    <div class="alert alert-danger mb-4" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 0.75rem 1rem; border-radius: var(--radius-sm); font-size: 0.82rem;">
                        <strong style="display: block; margin-bottom: 0.25rem;">⚠️ Transition Blocked by Lifecycle Policy:</strong>
                        <ul style="margin: 0; padding-left: 1.25rem;">
                            @foreach($transitionValidationErrors as $vErr)
                                <li>{{ $vErr }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-4">
                    <label class="form-label" style="font-size: 0.76rem; font-weight: 700;">SELECT TARGET TRANSITION STAGE *</label>
                    <select class="form-control form-select" wire:model.defer="transitionTargetStatus" style="font-size: 0.88rem; font-weight: 600;">
                        <option value="">-- Choose Allowed Destination Status --</option>
                        @foreach($transitionApplication->getAllowedNextTransitions() as $targetState)
                            <option value="{{ $targetState['code'] }}">
                                ➔ {{ $targetState['label'] }} ({{ $targetState['code'] }})
                            </option>
                        @endforeach
                    </select>
                    @error('transitionTargetStatus') <span class="text-danger" style="font-size: 0.72rem;">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label" style="font-size: 0.76rem; font-weight: 700;">TRANSITION AUDIT NOTES / JUSTIFICATION</label>
                    <textarea class="form-control" rows="2" wire:model.defer="transitionNotes" placeholder="Reason, remarks, or scrutiny audit notes..."></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.5rem; padding-top: 1rem; border-top: 1px solid rgba(255, 255, 255, 0.08);">
                    <button type="button" class="btn btn-secondary" wire:click="closeTransitionModal" style="padding: 0.55rem 1.15rem; font-size: 0.82rem;">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="executeStatusTransition" style="padding: 0.55rem 1.5rem; font-size: 0.85rem; font-weight: 700;">
                        Execute Transition
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Application Details Dossier Modal -->
    <x-modal :show="$isDetailsOpen" title="Complete Application Dossier Summary" closeAction="$set('isDetailsOpen', false)" style="max-width: 880px; width: 95%;">
        @if($selectedRecordDetails)
            <div style="max-height: 75vh; overflow-y: auto; padding-right: 0.25rem;">
                <!-- Header Banner -->
                <div style="background: linear-gradient(135deg, rgba(99,102,241,0.12), rgba(6,182,212,0.08)); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem; margin-bottom: 1.25rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            @if($selectedRecordDetails->user->basicInfo?->profile_picture_url)
                                <div class="user-avatar-circle" style="width: 52px; height: 52px; border-radius: 14px; overflow: hidden; padding: 0; flex-shrink: 0; border: 2px solid var(--primary); box-shadow: var(--shadow-sm);">
                                    <img src="{{ $selectedRecordDetails->user->basicInfo->profile_picture_url }}" alt="{{ $selectedRecordDetails->user->email }}" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            @else
                                <div class="user-avatar-circle" style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, var(--primary), var(--secondary)); font-size: 1.25rem; font-weight: 800; color: #ffffff; display: flex; align-items: center; justify-content: center;">
                                    {{ strtoupper(substr($selectedRecordDetails->user->basicInfo?->first_name ?: $selectedRecordDetails->user->email, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <h3 style="margin: 0; font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary);">
                                    {{ $selectedRecordDetails->user->basicInfo ? ($selectedRecordDetails->user->basicInfo->first_name . ' ' . $selectedRecordDetails->user->basicInfo->last_name) : $selectedRecordDetails->user->email }}
                                </h3>
                                <div style="font-size: 0.82rem; color: var(--color-text-secondary); margin-top: 2px;">
                                    Tracking ID: <code style="color: var(--secondary); font-weight: 700;">{{ $selectedRecordDetails->tracking_number }}</code> &bull; {{ $selectedRecordDetails->user->email }}
                                </div>
                            </div>
                        </div>
                        <div>
                            <span class="badge" style="padding: 0.35rem 0.85rem; font-size: 0.8rem; border-radius: 999px; font-weight: 800; background: rgba(99,102,241,0.15); color: var(--primary); border: 1px solid rgba(99,102,241,0.3);">
                                {{ $selectedRecordDetails->status_label }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Position & Vacancy Card -->
                <div class="card mb-4" style="padding: 1.25rem; background: var(--bg-card);">
                    <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 800; color: var(--secondary); letter-spacing: 0.05em; margin-bottom: 0.85rem;">
                        Target Job Vacancy Particulars
                    </div>
                    <div class="grid grid-cols-3 gap-4" style="font-size: 0.88rem;">
                        <div>
                            <div style="color: var(--color-text-muted); font-size: 0.75rem; text-transform: uppercase;">Job Code</div>
                            <strong style="color: var(--primary); font-family: 'JetBrains Mono', monospace;">{{ $selectedRecordDetails->campaign->job_code }}</strong>
                        </div>
                        <div>
                            <div style="color: var(--color-text-muted); font-size: 0.75rem; text-transform: uppercase;">Position Title</div>
                            <strong style="color: var(--color-text-primary);">{{ $selectedRecordDetails->campaign->title }}</strong>
                        </div>
                        <div>
                            <div style="color: var(--color-text-muted); font-size: 0.75rem; text-transform: uppercase;">Career Track</div>
                            <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary); font-weight: 700;">{{ $selectedRecordDetails->campaign->track_type }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payments & Settlement Card -->
                <div class="card mb-4" style="padding: 1.25rem; background: var(--bg-card);">
                    <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 800; color: var(--secondary); letter-spacing: 0.05em; margin-bottom: 0.85rem;">
                        Payment & Fee Voucher Details
                    </div>
                    <div class="grid grid-cols-3 gap-4" style="font-size: 0.88rem;">
                        <div>
                            <div style="color: var(--color-text-muted); font-size: 0.75rem; text-transform: uppercase;">Voucher Status</div>
                            <span class="badge" style="background: {{ $selectedRecordDetails->billingLedger?->status === 'SETTLED' ? 'rgba(16,185,129,0.15)' : 'rgba(245,158,11,0.15)' }}; color: {{ $selectedRecordDetails->billingLedger?->status === 'SETTLED' ? 'var(--success)' : 'var(--warning)' }}; font-weight: 700;">
                                {{ $selectedRecordDetails->billingLedger?->status ?: 'No Voucher Generated' }}
                            </span>
                        </div>
                        <div>
                            <div style="color: var(--color-text-muted); font-size: 0.75rem; text-transform: uppercase;">1Link Consumer Number</div>
                            <code style="font-family: 'JetBrains Mono', monospace; font-size: 0.88rem; color: var(--color-text-primary);">{{ $selectedRecordDetails->billingLedger?->consumer_id ?: 'N/A' }}</code>
                        </div>
                        <div>
                            <div style="color: var(--color-text-muted); font-size: 0.75rem; text-transform: uppercase;">Fee Amount</div>
                            <strong style="color: var(--success); font-size: 1rem;">PKR {{ number_format($selectedRecordDetails->billingLedger?->amount ?? 0, 2) }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Academic Peer Reviews Card -->
                @if($selectedRecordDetails->campaign->track_type === 'ACADEMIC' || $selectedRecordDetails->reviews->count() > 0)
                    <div class="card mb-4" style="padding: 1.25rem; background: rgba(6, 182, 212, 0.04); border-color: rgba(6, 182, 212, 0.25);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem; flex-wrap: wrap; gap: 0.5rem;">
                            <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 800; color: var(--secondary); letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.4rem;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/></svg>
                                <span>External Peer Review Referees ({{ count($selectedRecordDetails->reviews ?? []) }} Assigned)</span>
                            </div>
                            <button type="button" class="btn btn-secondary btn-sm" wire:click="openDispatchModal({{ $selectedRecordDetails->id }})" style="font-size: 0.75rem; color: #ec4899; font-weight: 700;">
                                + Dispatch More Experts
                            </button>
                        </div>

                        @if(!empty($selectedRecordDetails->reviews) && count($selectedRecordDetails->reviews) > 0)
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                @foreach($selectedRecordDetails->reviews as $rev)
                                    <div style="background: var(--bg-card); padding: 0.75rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                                        <div>
                                            <strong style="font-size: 0.9rem; color: var(--color-text-primary);">
                                                {{ $rev->reviewer->reviewerProfile?->name ?? 'External Referee' }}
                                            </strong>
                                            <span style="font-size: 0.78rem; color: var(--color-text-muted); margin-left: 0.4rem;">
                                                ({{ $rev->reviewer->reviewerProfile?->university }})
                                            </span>
                                        </div>
                                        <div>
                                            @if($rev->status === 'SUBMITTED' || $rev->status === 'COMPLETED')
                                                <span class="badge" style="background: rgba(16,185,129,0.15); color: var(--success); font-weight: 800; font-size: 0.76rem;">
                                                    ✓ {{ $rev->overall_score }}/100 • {{ str_replace('_', ' ', $rev->recommendation ?? 'Completed') }}
                                                </span>
                                            @else
                                                <span class="badge" style="background: rgba(245,158,11,0.15); color: var(--warning); font-size: 0.75rem; font-weight: 700;">
                                                    ⏳ Pending Evaluation
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p style="font-size: 0.84rem; color: var(--color-text-muted); margin: 0;">
                                Dossier has not yet been dispatched to external academic referees.
                            </p>
                        @endif
                    </div>
                @endif

                <div class="mt-5 flex justify-between align-center" style="border-top: 1px solid var(--border-color); padding-top: 1rem;">
                    <div style="display: flex; gap: 0.75rem; align-items: center;">
                        @if(auth()->user()->hasRole('super-admin') && $selectedRecordDetails->user && auth()->id() !== $selectedRecordDetails->user->id && !$selectedRecordDetails->user->hasRole('super-admin'))
                            <a href="{{ route('impersonate.start', $selectedRecordDetails->user->id) }}" class="btn btn-warning" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; font-weight: 700;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                <span>🎭 Impersonate Applicant</span>
                            </a>
                        @endif

                        @if(auth()->user()->can('scrutinize-application') || auth()->user()->can('verify-documents'))
                            <a href="/admin/verification/{{ $selectedRecordDetails->id }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; font-weight: 700;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                <span>Open Scrutiny Verification Desk</span>
                            </a>
                        @endif
                    </div>
                    <button type="button" class="btn btn-secondary" wire:click="$set('isDetailsOpen', false)" style="padding: 0.45rem 1.25rem;">Close Dossier</button>
                </div>
            </div>
        @endif
    </x-modal>
</div>
