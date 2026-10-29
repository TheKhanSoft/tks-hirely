<div>
    <!-- Executive Control Header -->
    <div class="card mb-6 card-ambient-mesh" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.25rem; position: relative; overflow: hidden; padding: 1.5rem 1.75rem;">
        <div style="position: absolute; right: -2rem; top: -2rem; width: 180px; height: 180px; background: radial-gradient(circle, var(--primary-glow) 0%, transparent 70%); pointer-events: none;"></div>
        <div>
            <div style="display: flex; align-items: center; gap: 0.85rem; margin-bottom: 0.35rem;">
                <div class="brand-icon-box" style="width: 44px; height: 44px; border-radius: 12px; background: rgba(99,102,241,0.15); border: 1px solid rgba(99,102,241,0.3);">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                </div>
                <div>
                    <h1 class="card-title" style="margin: 0; font-size: 1.5rem; font-weight: 800; letter-spacing: -0.02em;">
                        Applications & Candidacies Desk
                    </h1>
                    <p class="card-subtitle" style="margin: 0.2rem 0 0;">
                        Candidate application registry, qualification dossiers, scrutiny transitions, and appointment milestones.
                    </p>
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            @if(!$isFormOpen && !$isDetailsOpen)
                @can('create-application')
                    <button type="button" class="btn btn-primary" wire:click="openCreateApplicationForm" style="display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: var(--shadow-sm); padding: 0.55rem 1.15rem; font-weight: 700;">
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
        <div class="grid grid-cols-5 gap-4 mb-6">
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
                            <option value="DRAFT">Draft State</option>
                            <option value="SUBMITTED">Submitted (Pending Payment)</option>
                            <option value="PAID">Paid / In Scrutiny Queue</option>
                            <option value="ELIGIBLE">Eligible (Scrutiny Verified)</option>
                            <option value="INELIGIBLE">Ineligible (Disqualified)</option>
                            <option value="APPEALED">Appealed (Grievance Review)</option>
                            <option value="UNDER_REVIEW">Under External Peer Review</option>
                            <option value="APPROVED">Approved by Selection Board</option>
                            <option value="REJECTED">Rejected by Selection Board</option>
                        </select>
                    </x-select>

                    <x-input label="Disqualification / Ineligibility Justification" :error="$errors->first('app_reason')">
                        <input type="text" class="form-control" wire:model="app_reason" placeholder="e.g. Deficient post-PhD experience or missing equivalence certificate">
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

        <!-- Filter Ribbon & Search Bar -->
        <div class="card mb-5 card-ambient-mesh" style="padding: 1.25rem 1.5rem;">
            <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                <div style="flex: 2; min-width: 280px; position: relative;">
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Search by tracking number, applicant name, email, or CNIC..." style="padding-left: 2.35rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: var(--color-text-muted); pointer-events: none;">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.3-4.3"/>
                    </svg>
                </div>

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
                        <option value="DRAFT">Draft</option>
                        <option value="SUBMITTED">Submitted</option>
                        <option value="PAID">Paid / In Scrutiny</option>
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
                        <span>Reset</span>
                    </button>
                @endif

                <div style="margin-left: auto;">
                    <span style="font-size: 0.82rem; color: var(--color-text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;">
                        Showing <strong style="color: var(--primary);">{{ count($applications) }}</strong> Application{{ count($applications) === 1 ? '' : 's' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Modern Applications Table -->
        <div class="card" style="padding: 0; overflow: hidden; border-color: var(--border-color); box-shadow: var(--shadow-sm);">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Candidate Reference</th>
                            <th>Target Vacancy & Track</th>
                            <th>Financial Settlement</th>
                            <th>Processing Stage</th>
                            <th>Submission Timeline</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $app)
                            <tr wire:key="app-row-{{ $app->id }}">
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.85rem;">
                                        <div class="user-avatar-circle" style="width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, rgba(99,102,241,0.25), rgba(6,182,212,0.25)); color: var(--primary); font-weight: 800; font-size: 1rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid rgba(99,102,241,0.25);">
                                            {{ strtoupper(substr($app->user->basicInfo?->first_name ?: $app->user->email, 0, 1)) }}
                                        </div>
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
                                        <div style="display: flex; gap: 0.4rem; align-items: center; margin-top: 3px;">
                                            <span style="font-size: 0.74rem; font-family: 'JetBrains Mono', monospace; font-weight: 700; color: var(--primary);">
                                                {{ $app->campaign->job_code }}
                                            </span>
                                            <span class="badge" style="background: rgba(99,102,241,0.1); color: var(--primary); font-size: 0.68rem; padding: 0.12rem 0.45rem; font-weight: 700;">
                                                {{ $app->campaign->track_type }}
                                            </span>
                                            <span style="font-size: 0.72rem; color: var(--color-text-muted);">
                                                {{ $app->campaign->department }}
                                            </span>
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
                                    @if($app->status === 'APPROVED')
                                        <span class="badge" style="background: rgba(16,185,129,0.15); color: var(--success); border: 1px solid rgba(16,185,129,0.3); font-weight: 800; font-size: 0.76rem;">
                                            ✓ Approved Selection
                                        </span>
                                    @elseif($app->status === 'UNDER_REVIEW')
                                        <span class="badge" style="background: rgba(6,182,212,0.15); color: var(--secondary); border: 1px solid rgba(6,182,212,0.3); font-weight: 800; font-size: 0.76rem;">
                                            ⏳ In Peer Review
                                        </span>
                                    @elseif($app->status === 'ELIGIBLE')
                                        <span class="badge" style="background: rgba(59,130,246,0.15); color: #3b82f6; border: 1px solid rgba(59,130,246,0.3); font-weight: 800; font-size: 0.76rem;">
                                            ✓ Eligible
                                        </span>
                                    @elseif($app->status === 'INELIGIBLE' || $app->status === 'REJECTED')
                                        <span class="badge" style="background: rgba(239,68,68,0.15); color: var(--danger); border: 1px solid rgba(239,68,68,0.3); font-weight: 800; font-size: 0.76rem;">
                                            ✕ {{ $app->status }}
                                        </span>
                                        @if($app->disqualification_reason)
                                            <div style="color: var(--danger); font-size: 0.72rem; margin-top: 2px; max-width: 170px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $app->disqualification_reason }}">
                                                {{ $app->disqualification_reason }}
                                            </div>
                                        @endif
                                    @elseif($app->status === 'APPEALED')
                                        <span class="badge" style="background: rgba(245,158,11,0.15); color: var(--warning); border: 1px solid rgba(245,158,11,0.3); font-weight: 800; font-size: 0.76rem;">
                                            ⚠ Grievance Appealed
                                        </span>
                                    @else
                                        <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary); border: 1px solid rgba(99,102,241,0.25); font-weight: 800; font-size: 0.76rem;">
                                            {{ $app->status }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-size: 0.82rem; color: var(--color-text-secondary); font-family: 'JetBrains Mono', monospace;">
                                        {{ $app->submission_timestamp ? \Carbon\Carbon::parse($app->submission_timestamp)->format('Y-m-d H:i') : 'Draft state' }}
                                    </div>
                                    @if($app->submission_timestamp)
                                        <div style="font-size: 0.72rem; color: var(--color-text-muted); margin-top: 2px;">
                                            {{ \Carbon\Carbon::parse($app->submission_timestamp)->diffForHumans() }}
                                        </div>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <div class="flex gap-2 justify-end align-center">
                                        @can('view-application')
                                            <button type="button" class="btn btn-secondary btn-sm" style="padding: 0.35rem 0.7rem; font-size: 0.78rem;" wire:click="viewApplicationDetails({{ $app->id }})">Details</button>
                                        @endcan
                                        @if(auth()->user()->can('change-application-status') || auth()->user()->can('edit-application'))
                                            <button type="button" class="btn btn-secondary btn-sm" style="padding: 0.35rem 0.7rem; font-size: 0.78rem;" wire:click="openEditApplicationForm({{ $app->id }})">Status</button>
                                        @endif
                                        @if(auth()->user()->can('scrutinize-application') || auth()->user()->can('verify-documents'))
                                            <a href="/admin/verification/{{ $app->id }}" class="btn btn-secondary btn-sm" style="padding: 0.35rem 0.7rem; font-size: 0.78rem; text-decoration: none;" title="Open Verification Desk">Verify</a>
                                        @endif
                                        @can('delete-application')
                                            <button type="button" class="btn btn-danger btn-sm" style="padding: 0.35rem 0.65rem; font-size: 0.78rem;" wire:click="deleteApplication({{ $app->id }})" onclick="confirm('Are you sure you want to permanently delete this application record?') || event.stopImmediatePropagation()">Delete</button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center" style="padding: 4rem 2rem; color: var(--color-text-muted);">
                                    <div style="width: 54px; height: 54px; border-radius: 50%; background: rgba(99,102,241,0.1); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 0.85rem;">
                                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    </div>
                                    <h4 style="font-size: 1.1rem; font-weight: 800; color: var(--color-text-primary); margin: 0 0 0.35rem 0;">No Candidate Applications Found</h4>
                                    <p style="font-size: 0.85rem; margin: 0;">No applications match your active search and filter criteria.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
                            <div class="user-avatar-circle" style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, var(--primary), var(--secondary)); font-size: 1.25rem; font-weight: 800; color: #ffffff; display: flex; align-items: center; justify-content: center;">
                                {{ strtoupper(substr($selectedRecordDetails->user->basicInfo?->first_name ?: $selectedRecordDetails->user->email, 0, 1)) }}
                            </div>
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
                                {{ $selectedRecordDetails->status }}
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

                <!-- Financial Ledger Card -->
                <div class="card mb-4" style="padding: 1.25rem; background: var(--bg-card);">
                    <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 800; color: var(--secondary); letter-spacing: 0.05em; margin-bottom: 0.85rem;">
                        Fee Voucher & Settlement Ledger
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
                @if($selectedRecordDetails->campaign->track_type === 'ACADEMIC')
                    <div class="card mb-4" style="padding: 1.25rem; background: rgba(6, 182, 212, 0.04); border-color: rgba(6, 182, 212, 0.25);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem; flex-wrap: wrap; gap: 0.5rem;">
                            <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 800; color: var(--secondary); letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.4rem;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/></svg>
                                <span>External Peer Review Referees ({{ count($selectedRecordDetails->reviews ?? []) }} Assigned)</span>
                            </div>
                            <a href="/admin/reviewers" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; text-decoration: none;">
                                Open Reviewers Desk →
                            </a>
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
                                            @if($rev->status === 'SUBMITTED')
                                                <span class="badge" style="background: rgba(16,185,129,0.15); color: var(--success); font-weight: 800; font-size: 0.76rem;">
                                                    ✓ {{ $rev->overall_score }}/100 • {{ str_replace('_', ' ', $rev->recommendation) }}
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
                    @if(auth()->user()->can('scrutinize-application') || auth()->user()->can('verify-documents'))
                        <a href="/admin/verification/{{ $selectedRecordDetails->id }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; font-weight: 700;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                            <span>Open Scrutiny Verification Desk</span>
                        </a>
                    @else
                        <div></div>
                    @endif
                    <button type="button" class="btn btn-secondary" wire:click="$set('isDetailsOpen', false)" style="padding: 0.45rem 1.25rem;">Close Dossier</button>
                </div>
            </div>
        @endif
    </x-modal>
</div>
