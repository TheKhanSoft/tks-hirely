<div class="flex-col gap-6">
    <!-- Flash Success Notification -->
    @if (session()->has('message'))
        <div class="card" style="background: rgba(16, 185, 129, 0.1); border-color: var(--success); padding: 1rem 1.25rem;">
            <div class="flex align-center justify-between" style="gap: 1rem;">
                <div class="flex align-center gap-3">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <div>
                        <strong style="color: #6ee7b7; font-size: 0.95rem;">Action Successful</strong>
                        <p style="color: var(--color-text-secondary); font-size: 0.88rem; margin: 0.15rem 0 0;">{{ session('message') }}</p>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary btn-sm" onclick="this.closest('.card').remove()">✕</button>
            </div>
        </div>
    @endif

    @if(!$basicInfo)
        <!-- ============================================================== -->
        <!-- ONBOARDING HERO: NEW CANDIDATE                                 -->
        <!-- ============================================================== -->
        <div class="card applicant-hero text-center" style="padding: 3.5rem 2rem;">
            <div class="applicant-avatar" style="margin: 0 auto 1.5rem; width: 76px; height: 76px; font-size: 2rem;">
                {{ strtoupper(substr(auth()->user()->email, 0, 1)) }}
            </div>
            
            <h1 style="font-size: 1.85rem; font-weight: 800; letter-spacing: -0.03em; margin-bottom: 0.5rem; color: var(--color-text-primary);">
                Welcome to Hirely Recruitment Portal
            </h1>
            <p style="color: var(--color-text-secondary); font-size: 1.05rem; max-width: 580px; margin: 0 auto 2rem; line-height: 1.6;">
                Begin your university employment application by preparing your candidate profile dossier, entering your academic and professional credentials, and generating your 1Link fee voucher.
            </p>

            <!-- 3-Step Walkthrough Preview -->
            <div class="grid grid-cols-3 gap-4 mb-6" style="max-width: 850px; margin-left: auto; margin-right: auto; text-align: left;">
                <div style="background: var(--bg-card-solid); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; background: var(--primary-light); color: var(--primary); font-weight: 800; font-size: 0.8rem;">1</span>
                        <strong style="font-size: 0.95rem; color: var(--color-text-primary);">Bio-Data & Degrees</strong>
                    </div>
                    <p style="color: var(--color-text-muted); font-size: 0.82rem; margin: 0;">Enter your personal details, academic qualifications, and verified CNIC identity.</p>
                </div>

                <div style="background: var(--bg-card-solid); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; background: var(--secondary-light); color: var(--secondary); font-weight: 800; font-size: 0.8rem;">2</span>
                        <strong style="font-size: 0.95rem; color: var(--color-text-primary);">Select Campaigns</strong>
                    </div>
                    <p style="color: var(--color-text-muted); font-size: 0.82rem; margin: 0;">Choose one or multiple vacancies, review criteria, and submit your dossier.</p>
                </div>

                <div style="background: var(--bg-card-solid); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; background: var(--success-light); color: var(--success); font-weight: 800; font-size: 0.8rem;">3</span>
                        <strong style="font-size: 0.95rem; color: var(--color-text-primary);">1Link Fee & Track</strong>
                    </div>
                    <p style="color: var(--color-text-muted); font-size: 0.82rem; margin: 0;">Settle processing fee via mobile banking or simulator and track committee scrutiny.</p>
                </div>
            </div>

            <div class="flex justify-center gap-3">
                <a href="/applicant/wizard" class="btn btn-primary btn-lg">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                        <path d="M15 2H9a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1Z"/>
                        <path d="m9 14 2 2 4-4"/>
                    </svg>
                    <span>Launch Application Dossier Wizard</span>
                </a>
            </div>
        </div>
    @else
        <!-- ============================================================== -->
        <!-- EXECUTIVE APPLICANT PROFILE HERO                               -->
        <!-- ============================================================== -->
        <div class="applicant-hero">
            <div class="flex justify-between align-center" style="flex-wrap: wrap; gap: 1.75rem;">
                <!-- Candidate Identity Block -->
                <div class="flex align-center gap-4" style="flex: 1; min-width: 280px;">
                    <div class="applicant-avatar">
                        {{ strtoupper(substr($basicInfo->first_name ?: auth()->user()->email, 0, 1)) }}
                        <div class="applicant-avatar-verified" title="Verified Identity Record">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </div>
                    </div>

                    <div style="flex: 1; min-width: 0;">
                        <div class="flex align-center gap-2" style="flex-wrap: wrap;">
                            <h1 style="font-size: 1.65rem; font-weight: 800; letter-spacing: -0.03em; margin: 0; color: var(--color-text-primary);">
                                {{ trim($basicInfo->first_name . ' ' . $basicInfo->last_name) }}
                            </h1>
                            <span class="badge badge-appealed" style="font-size: 0.72rem; font-weight: 800;">
                                {{ $basicInfo->track_type }} TRACK
                            </span>
                        </div>

                        <div class="flex align-center gap-3 mt-2" style="flex-wrap: wrap; font-size: 0.85rem; color: var(--color-text-secondary);">
                            <span class="flex align-center gap-1">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="20" height="16" x="2" y="4" rx="2"/>
                                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                                </svg>
                                {{ auth()->user()->email }}
                            </span>
                            
                            <span style="color: var(--border-color);">•</span>

                            <span class="flex align-center gap-1">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="20" height="14" x="2" y="5" rx="2"/>
                                    <circle cx="8" cy="12" r="2"/>
                                    <path d="M14 10h4"/>
                                    <path d="M14 14h2"/>
                                </svg>
                                CNIC: <strong style="font-family: 'JetBrains Mono', monospace; color: var(--color-text-primary);">{{ $basicInfo->cnic_number }}</strong>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Profile Completeness Gauge & Primary Action -->
                <div style="min-width: 260px; max-width: 340px; width: 100%;">
                    <div class="flex justify-between align-center mb-2">
                        <span style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-secondary);">
                            Dossier Completeness
                        </span>
                        <strong style="color: {{ $completion === 100 ? 'var(--success)' : 'var(--secondary)' }}; font-size: 0.95rem; font-weight: 800;">
                            {{ $completion }}%
                        </strong>
                    </div>

                    <div class="progress-track-container mb-2">
                        <div class="progress-track-bar" style="width: {{ $completion }}%;"></div>
                    </div>

                    <p style="font-size: 0.75rem; color: var(--color-text-muted); margin: 0 0 0.85rem;">
                        @if($basicInfo->is_locked)
                            ✓ Dossier locked & submitted for administrative review.
                        @else
                            Pending completion. Finalize details and submit to lock.
                        @endif
                    </p>

                    <div class="flex gap-2">
                        <button type="button" class="btn btn-primary btn-sm" wire:click="openApplyModal" style="flex: 1;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            <span>Apply for Position</span>
                        </button>

                        <a href="/applicant/wizard" class="btn btn-secondary btn-sm" title="Open Dossier Wizard">
                            <span>Dossier</span>
                        </a>

                        <a href="/bank/simulator" class="btn btn-secondary btn-sm" title="Open 1Link Sandbox Switch">
                            <span>1Link Sim</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- EXECUTIVE BENTO KPI METRICS                                    -->
        <!-- ============================================================== -->
        <div class="grid grid-cols-4 gap-6">
            <!-- Dossier Gate Status -->
            <div class="metric-card" style="--card-accent: {{ $basicInfo->is_locked ? 'var(--success)' : 'var(--warning)' }};">
                <div class="metric-header">
                    <span class="metric-label">Dossier Gate</span>
                    <div class="metric-icon-wrap" style="background: {{ $basicInfo->is_locked ? 'var(--success-light)' : 'var(--warning-light)' }}; color: {{ $basicInfo->is_locked ? 'var(--success)' : 'var(--warning)' }};">
                        @if($basicInfo->is_locked)
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        @else
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 9.9-1"/>
                            </svg>
                        @endif
                    </div>
                </div>
                <div>
                    @if($basicInfo->is_locked)
                        <span class="badge badge-paid">
                            <span class="badge-dot" style="background: var(--success);"></span>
                            Frozen & Submitted
                        </span>
                        <p class="metric-subtext" style="margin-top: 0.5rem;">Records secured for scrutiny committee verification.</p>
                    @else
                        <span class="badge badge-pending">
                            <span class="badge-dot" style="background: var(--warning);"></span>
                            Draft / Editable
                        </span>
                        <p class="metric-subtext" style="margin-top: 0.5rem;">Free to update education, tools, and publications.</p>
                    @endif
                </div>
            </div>

            <!-- Recruitment Track -->
            <div class="metric-card" style="--card-accent: var(--secondary);">
                <div class="metric-header">
                    <span class="metric-label">Evaluation Track</span>
                    <div class="metric-icon-wrap" style="background: var(--secondary-light); color: var(--secondary);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="metric-value" style="font-size: 1.45rem; color: var(--secondary); margin-bottom: 0.35rem;">
                        {{ $basicInfo->track_type }}
                    </div>
                    <p class="metric-subtext">Calibrated against official HEC & university appointment statutes.</p>
                </div>
            </div>

            <!-- Application Pipeline -->
            <div class="metric-card" style="--card-accent: var(--primary);">
                <div class="metric-header">
                    <span class="metric-label">Applications Submitted</span>
                    <div class="metric-icon-wrap" style="background: var(--primary-light); color: var(--primary);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="14" x="2" y="7" rx="2" ry="2"/>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="metric-value">{{ $totalApplications }}</div>
                    <p class="metric-subtext">{{ $paidCount }} Paid & Verified • {{ $pendingCount }} Awaiting Payment</p>
                </div>
            </div>

            <!-- Fee & Scrutiny State -->
            <div class="metric-card" style="--card-accent: {{ $pendingCount > 0 ? 'var(--warning)' : 'var(--success)' }};">
                <div class="metric-header">
                    <span class="metric-label">Voucher Settlement</span>
                    <div class="metric-icon-wrap" style="background: {{ $pendingCount > 0 ? 'var(--warning-light)' : 'var(--success-light)' }}; color: {{ $pendingCount > 0 ? 'var(--warning)' : 'var(--success)' }};">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="14" x="2" y="5" rx="2"/>
                            <line x1="2" x2="22" y1="10" y2="10"/>
                        </svg>
                    </div>
                </div>
                <div>
                    @if($pendingCount > 0)
                        <span class="badge badge-pending">
                            <span class="badge-dot" style="background: var(--warning);"></span>
                            {{ $pendingCount }} Pending Payment
                        </span>
                        <p class="metric-subtext" style="margin-top: 0.5rem;">Settle fee to queue dossier for committee scrutiny.</p>
                    @else
                        <span class="badge badge-paid">
                            <span class="badge-dot" style="background: var(--success);"></span>
                            All Fees Reconciled
                        </span>
                        <p class="metric-subtext" style="margin-top: 0.5rem;">Zero pending vouchers. Invoices settled.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- RECRUITMENT LIFECYCLE ROADMAP (APPLICATION JOURNEY)             -->
        <!-- ============================================================== -->
        @php
            $hasSubmitted = $allApplications->count() > 0;
            $hasPaid = $allApplications->whereIn('status', ['PAID', 'ELIGIBLE', 'APPROVED'])->count() > 0;
            $hasEligible = $allApplications->whereIn('status', ['ELIGIBLE', 'APPROVED'])->count() > 0;
            $hasApproved = $allApplications->where('status', 'APPROVED')->count() > 0;
        @endphp
        <div class="card">
            <div class="flex justify-between align-center mb-2">
                <div>
                    <h3 class="card-title" style="font-size: 1.05rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--secondary)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        University Recruitment Lifecycle Pipeline
                    </h3>
                    <p class="card-subtitle">Transparent tracking of your candidacy through institutional scrutiny gates.</p>
                </div>

                <div class="flex align-center gap-2">
                    <span class="status-beacon">
                        <span class="beacon-dot"></span>
                        Live Portal Status
                    </span>
                </div>
            </div>

            <div class="lifecycle-tracker">
                <!-- Step 1: Dossier Setup -->
                <div class="lifecycle-step completed">
                    <div class="lifecycle-node">✓</div>
                    <span class="lifecycle-label">1. Candidate Dossier</span>
                </div>
                <div class="lifecycle-connector filled"></div>

                <!-- Step 2: Submission & Challan -->
                <div class="lifecycle-step {{ $hasSubmitted ? 'completed' : 'active' }}">
                    <div class="lifecycle-node">
                        @if($hasSubmitted) ✓ @else 2 @endif
                    </div>
                    <span class="lifecycle-label">2. Campaign Voucher</span>
                </div>
                <div class="lifecycle-connector {{ $hasPaid ? 'filled' : '' }}"></div>

                <!-- Step 3: 1Link Fee Settle -->
                <div class="lifecycle-step {{ $hasPaid ? 'completed' : ($hasSubmitted ? 'active' : '') }}">
                    <div class="lifecycle-node">
                        @if($hasPaid) ✓ @else 3 @endif
                    </div>
                    <span class="lifecycle-label">3. 1Link Settlement</span>
                </div>
                <div class="lifecycle-connector {{ $hasEligible ? 'filled' : '' }}"></div>

                <!-- Step 4: Scrutiny Committee -->
                <div class="lifecycle-step {{ $hasEligible ? 'completed' : ($hasPaid ? 'active' : '') }}">
                    <div class="lifecycle-node">
                        @if($hasEligible) ✓ @else 4 @endif
                    </div>
                    <span class="lifecycle-label">4. Scrutiny Desk</span>
                </div>
                <div class="lifecycle-connector {{ $hasApproved ? 'filled' : '' }}"></div>

                <!-- Step 5: Selection Board -->
                <div class="lifecycle-step {{ $hasApproved ? 'completed' : ($hasEligible ? 'active' : '') }}">
                    <div class="lifecycle-node">
                        @if($hasApproved) ✓ @else 5 @endif
                    </div>
                    <span class="lifecycle-label">5. Selection Board</span>
                </div>
                <div class="lifecycle-connector {{ $hasApproved ? 'filled' : '' }}"></div>

                <!-- Step 6: Final Merit & Appointment -->
                <div class="lifecycle-step {{ $hasApproved ? 'active' : '' }}">
                    <div class="lifecycle-node">6</div>
                    <span class="lifecycle-label">6. Appointment</span>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- ACTIVE APPLICATIONS & VOUCHERS TABLE                           -->
        <!-- ============================================================== -->
        <div class="card">
            <div class="flex justify-between align-center mb-4" style="flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h3 class="card-title" style="font-size: 1.15rem;">
                        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="14" x="2" y="7" rx="2" ry="2"/>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                        </svg>
                        Submitted Job Applications & Invoices
                    </h3>
                    <p class="card-subtitle">Manage all positions applied for, track individual 1Link payment switch reconciliations, and review eligibility.</p>
                </div>

                <div class="flex align-center gap-2">
                    <button type="button" class="btn btn-primary btn-sm" wire:click="openApplyModal">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        <span>Apply for Another Position</span>
                    </button>
                </div>
            </div>

            <!-- Segmented Status Filter Tabs -->
            <div class="filter-pills mb-4">
                <button type="button" class="filter-pill {{ $statusFilter === 'ALL' ? 'active' : '' }}" wire:click="setStatusFilter('ALL')">
                    <span>All Applications</span>
                    <span class="filter-pill-count">{{ $totalApplications }}</span>
                </button>

                <button type="button" class="filter-pill {{ $statusFilter === 'PENDING' ? 'active' : '' }}" wire:click="setStatusFilter('PENDING')">
                    <span class="badge-dot" style="background: var(--warning);"></span>
                    <span>Pending Payment</span>
                    <span class="filter-pill-count">{{ $pendingCount }}</span>
                </button>

                <button type="button" class="filter-pill {{ $statusFilter === 'PAID' ? 'active' : '' }}" wire:click="setStatusFilter('PAID')">
                    <span class="badge-dot" style="background: var(--success);"></span>
                    <span>Paid / In Scrutiny</span>
                    <span class="filter-pill-count">{{ $paidCount }}</span>
                </button>

                <button type="button" class="filter-pill {{ $statusFilter === 'ELIGIBLE' ? 'active' : '' }}" wire:click="setStatusFilter('ELIGIBLE')">
                    <span class="badge-dot" style="background: var(--secondary);"></span>
                    <span>Board Eligible</span>
                    <span class="filter-pill-count">{{ $eligibleCount }}</span>
                </button>

                @if($ineligibleCount > 0)
                    <button type="button" class="filter-pill {{ $statusFilter === 'INELIGIBLE' ? 'active' : '' }}" wire:click="setStatusFilter('INELIGIBLE')">
                        <span class="badge-dot" style="background: var(--danger);"></span>
                        <span>Appeals / Disqualified</span>
                        <span class="filter-pill-count">{{ $ineligibleCount }}</span>
                    </button>
                @endif
            </div>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Recruitment Campaign</th>
                            <th>Department</th>
                            <th>Submission Date</th>
                            <th>1Link Consumer ID</th>
                            <th>Processing Fee</th>
                            <th>Current Status</th>
                            <th>Official Slip & Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $app)
                            <tr wire:key="app-row-{{ $app->id }}">
                                <td>
                                    <div class="flex-col">
                                        <strong style="color: var(--color-text-primary); font-size: 0.95rem;">{{ $app->campaign->title }}</strong>
                                        <div class="flex align-center gap-2 mt-1">
                                            <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; background: var(--primary-light); color: var(--primary); padding: 0.15rem 0.45rem; border-radius: 4px; font-weight: 700;">
                                                {{ $app->campaign->job_code }}
                                            </span>
                                            <span style="font-size: 0.72rem; color: var(--color-text-muted); text-transform: uppercase;">
                                                {{ $app->campaign->track_type }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span style="color: var(--color-text-secondary); font-size: 0.88rem; font-weight: 500;">
                                        {{ $app->campaign->department }}
                                    </span>
                                </td>
                                <td>
                                    <span style="font-size: 0.85rem; color: var(--color-text-muted);">
                                        {{ $app->submission_timestamp?->format('d M Y, h:i A') ?: 'Draft' }}
                                    </span>
                                </td>
                                <td>
                                    @if($app->billingLedger)
                                        <div class="copyable-chip" onclick="copyConsumerId('{{ $app->billingLedger->consumer_id }}', this)" title="Click to copy Consumer ID to clipboard">
                                            <span>{{ $app->billingLedger->consumer_id }}</span>
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect width="14" height="14" x="8" y="8" rx="2" ry="2"/>
                                                <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>
                                            </svg>
                                        </div>
                                    @else
                                        <span style="color: var(--color-text-muted); font-size: 0.85rem;">Not Generated</span>
                                    @endif
                                </td>
                                <td>
                                    <strong style="color: var(--color-text-primary); font-size: 0.95rem;">
                                        PKR {{ number_format($app->billingLedger?->amount ?? $app->campaign->application_fee, 2) }}
                                    </strong>
                                </td>
                                <td>
                                    @if($app->status === 'DRAFT')
                                        <span class="badge badge-draft">Draft</span>
                                    @elseif($app->status === 'SUBMITTED')
                                        <span class="badge badge-pending">
                                            <span class="badge-dot" style="background: var(--warning);"></span>
                                            Pending Payment
                                        </span>
                                    @elseif($app->status === 'PAID')
                                        <span class="badge badge-paid">
                                            <span class="badge-dot" style="background: var(--success);"></span>
                                            Paid / In Scrutiny
                                        </span>
                                    @elseif($app->status === 'ELIGIBLE')
                                        <span class="badge badge-eligible">
                                            <span class="badge-dot" style="background: var(--success);"></span>
                                            Eligible for Board
                                        </span>
                                    @elseif($app->status === 'INELIGIBLE')
                                        <span class="badge badge-ineligible">
                                            <span class="badge-dot" style="background: var(--danger);"></span>
                                            Disqualified
                                        </span>
                                    @elseif($app->status === 'APPEALED')
                                        <span class="badge badge-appealed">
                                            <span class="badge-dot" style="background: var(--secondary);"></span>
                                            Appeal Under Review
                                        </span>
                                    @elseif($app->status === 'APPROVED')
                                        <span class="badge badge-approved">
                                            <span class="badge-dot" style="background: var(--success);"></span>
                                            Appointment Approved
                                        </span>
                                    @elseif($app->status === 'REJECTED')
                                        <span class="badge badge-rejected">Appeal Dismissed</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex gap-2 align-center">
                                        <!-- View Official Slip Button -->
                                        <button type="button" class="btn btn-secondary btn-sm" wire:click="openSlipModal({{ $app->id }})" title="View / Print Official 3-Part Bank Slip">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M6 9V2h12v7"/>
                                                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                                                <path d="M6 14h12v8H6z"/>
                                            </svg>
                                            <span>Official Slip</span>
                                        </button>

                                        @if($app->status === 'SUBMITTED' && $app->billingLedger)
                                            <a href="/bank/simulator?consumer_id={{ $app->billingLedger->consumer_id }}" class="btn btn-primary btn-sm" style="font-size: 0.78rem;">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect width="20" height="14" x="2" y="5" rx="2"/>
                                                    <line x1="2" x2="22" y1="10" y2="10"/>
                                                </svg>
                                                <span>Pay Sim</span>
                                            </a>
                                        @elseif($app->status === 'INELIGIBLE')
                                            <button type="button" class="btn btn-danger btn-sm" wire:click="openAppealForm({{ $app->id }})">
                                                <span>Appeal</span>
                                            </button>
                                        @endif
                                    </div>

                                    @if($app->status === 'INELIGIBLE' && $app->disqualification_reason)
                                        <small style="color: #fca5a5; display: block; margin-top: 0.35rem; font-size: 0.75rem;">
                                            Reason: {{ $app->disqualification_reason }}
                                        </small>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center" style="color: var(--color-text-muted); padding: 3rem 1.5rem;">
                                    <div style="max-width: 380px; margin: 0 auto;">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--color-text-dim)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 0.75rem;">
                                            <rect width="20" height="14" x="2" y="7" rx="2" ry="2"/>
                                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                                        </svg>
                                        <p style="font-size: 0.95rem; font-weight: 600; color: var(--color-text-primary); margin-bottom: 0.25rem;">No Applications Found</p>
                                        <p style="font-size: 0.82rem; color: var(--color-text-secondary); margin-bottom: 1rem;">No applications match the current filter selection.</p>
                                        <button type="button" class="btn btn-primary btn-sm" wire:click="openApplyModal">Apply for Position</button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL 1: APPLY FOR ANOTHER POSITION                            -->
    <!-- ============================================================== -->
    @if($showApplyModal)
        <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.82); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 1.5rem;">
            <div class="card" style="width: 100%; max-width: 820px; max-height: 90vh; display: flex; flex-direction: column; border-color: var(--border-color); box-shadow: var(--shadow-lg); background: var(--bg-card-solid); overflow: hidden;">
                <!-- Modal Header -->
                <div class="flex justify-between align-center" style="padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
                    <div>
                        <h3 class="card-title" style="margin: 0; font-size: 1.25rem;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--secondary)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="16"/>
                                <line x1="8" y1="12" x2="16" y2="12"/>
                            </svg>
                            Apply for University Recruitment Position
                        </h3>
                        <p class="card-subtitle" style="margin: 0.25rem 0 0;">
                            Select an available campaign. Positions you have already applied for are locked to prevent duplicate submissions.
                        </p>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="closeApplyModal">✕</button>
                </div>

                <!-- Search Filter & Notice -->
                <div style="padding: 1rem 1.5rem; background: var(--bg-surface); border-bottom: 1px solid var(--border-color);">
                    <div style="position: relative;">
                        <input type="text" class="form-control" wire:model.live.debounce.250ms="campaignSearch" placeholder="Filter positions by title, job code, or department..." style="padding-left: 2.5rem; font-size: 0.88rem;">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="var(--color-text-muted)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); pointer-events: none;">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.3-4.3"/>
                        </svg>
                    </div>
                </div>

                <!-- Vacancies Selection Grid -->
                <div style="padding: 1.5rem; overflow-y: auto; flex: 1;">
                    @error('selectedCampaignIdForModal')
                        <div class="card mb-3" style="background: var(--danger-light); border-color: var(--danger-glow); padding: 0.75rem 1rem;">
                            <span style="color: #fca5a5; font-size: 0.84rem; font-weight: 600;">{{ $message }}</span>
                        </div>
                    @enderror

                    <div class="vacancy-grid">
                        @forelse($availableCampaigns as $camp)
                            @php
                                $alreadyApplied = in_array($camp->id, $appliedCampaignIds);
                                $isSelected = $selectedCampaignIdForModal == $camp->id;
                            @endphp
                            <div class="vacancy-card {{ $alreadyApplied ? 'disabled' : ($isSelected ? 'selected' : '') }}"
                                 wire:key="modal-camp-{{ $camp->id }}"
                                 @if(!$alreadyApplied) wire:click="selectCampaignForModal({{ $camp->id }})" @endif>
                                <div>
                                    <div class="flex justify-between align-center mb-2">
                                        <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.72rem; background: var(--primary-light); color: var(--primary); padding: 0.15rem 0.45rem; border-radius: 4px; font-weight: 700;">
                                            {{ $camp->job_code }}
                                        </span>
                                        <x-badge type="appealed" style="font-size: 0.65rem;">{{ $camp->track_type }}</x-badge>
                                    </div>

                                    <strong style="color: var(--color-text-primary); font-size: 0.95rem; display: block; margin-bottom: 0.35rem; line-height: 1.35;">
                                        {{ $camp->title }}
                                    </strong>
                                    <span style="color: var(--color-text-secondary); font-size: 0.8rem; display: block;">
                                        {{ $camp->department }}
                                    </span>
                                </div>

                                <div style="margin-top: 1rem; border-top: 1px solid var(--border-color); padding-top: 0.75rem;" class="flex justify-between align-center">
                                    <div>
                                        <small style="color: var(--color-text-muted); font-size: 0.7rem; text-transform: uppercase;">Processing Fee</small><br>
                                        <strong style="color: var(--warning); font-size: 0.95rem;">PKR {{ number_format($camp->application_fee) }}</strong>
                                    </div>

                                    <div>
                                        @if($alreadyApplied)
                                            <span class="badge badge-paid" style="font-size: 0.65rem;">
                                                <span class="badge-dot" style="background: var(--success);"></span>
                                                Already Applied
                                            </span>
                                        @elseif($isSelected)
                                            <span class="badge badge-eligible" style="font-size: 0.65rem;">
                                                ✓ Selected
                                            </span>
                                        @else
                                            <span class="btn btn-secondary btn-sm" style="font-size: 0.72rem; padding: 0.25rem 0.6rem;">
                                                Select
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div style="grid-column: 1 / -1; text-align: center; color: var(--color-text-muted); padding: 2rem;">
                                No open positions match your search query.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-between align-center" style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--border-color); background: var(--bg-surface);">
                    <div>
                        @if($selectedCampaignIdForModal)
                            @php
                                $chosenCampaign = $availableCampaigns->firstWhere('id', $selectedCampaignIdForModal);
                            @endphp
                            <span style="font-size: 0.85rem; color: var(--color-text-secondary);">Selected Position:</span>
                            <strong style="color: var(--color-text-primary); font-size: 0.95rem; margin-left: 0.35rem;">
                                {{ $chosenCampaign?->title }}
                            </strong>
                            <span style="color: var(--warning); font-weight: 700; margin-left: 0.5rem;">
                                (PKR {{ number_format($chosenCampaign?->application_fee ?? 0) }})
                            </span>
                        @else
                            <span style="font-size: 0.82rem; color: var(--color-text-muted);">
                                Click a vacancy card above to select it for application.
                            </span>
                        @endif
                    </div>

                    <div class="flex gap-2">
                        <button type="button" class="btn btn-secondary" wire:click="closeApplyModal">Cancel</button>
                        <button type="button" class="btn btn-primary" wire:click="submitNewApplication" wire:loading.attr="disabled" :disabled="!$selectedCampaignIdForModal">
                            <span wire:loading.remove wire:target="submitNewApplication">Submit Application & Generate Voucher</span>
                            <span wire:loading wire:target="submitNewApplication">Generating 1Link Slip...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL 2: PRINT-READY OFFICIAL CHALLAN / PAYMENT RECEIPT        -->
    <!-- ============================================================== -->
    @if($showSlipModal && $selectedSlipApp)
        @php
            $isPaid = ($selectedSlipApp->billingLedger?->status === 'SETTLED');
        @endphp
        <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); display: flex; align-items: center; justify-content: center; z-index: 1050; padding: 1.5rem; overflow-y: auto;">
            <div class="card print-area" style="width: 100%; max-width: {{ $isPaid ? '780px' : '960px' }}; max-height: 92vh; display: flex; flex-direction: column; background: #ffffff; color: #0f172a; border-radius: var(--radius-md); box-shadow: var(--shadow-lg); overflow: hidden; padding: 0;">
                <!-- Top Toolbar (Hidden on print) -->
                <div class="no-print flex justify-between align-center" style="padding: 1rem 1.5rem; background: var(--bg-surface); border-bottom: 1px solid var(--border-color); color: var(--color-text-primary);">
                    <div class="flex align-center gap-2">
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; background: {{ $isPaid ? 'rgba(16,185,129,0.15)' : 'var(--secondary-light)' }}; color: {{ $isPaid ? 'var(--success)' : 'var(--secondary)' }};">
                            @if($isPaid)
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                            @else
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/>
                                    <path d="M6 6h10"/>
                                    <path d="M6 10h10"/>
                                </svg>
                            @endif
                        </span>
                        <strong style="font-size: 1.05rem;">
                            {{ $isPaid ? 'Official Fee Payment Receipt & Deposit Acknowledgement' : 'Official Bank Challan / Deposit Slip' }}
                        </strong>
                    </div>

                    <div class="flex align-center gap-2">
                        <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 6 2 18 2 18 9"/>
                                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                                <rect width="12" height="8" x="6" y="14"/>
                            </svg>
                            <span>{{ $isPaid ? 'Print Official Receipt' : 'Print Slip' }}</span>
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" wire:click="closeSlipModal">✕ Close</button>
                    </div>
                </div>

                <!-- Body Area -->
                <div style="padding: 1.5rem; overflow-y: auto; flex: 1;">
                    @if($isPaid)
                        <!-- ============================================================== -->
                        <!-- PAID STATE: SINGLE OFFICIAL PAYMENT RECEIPT                    -->
                        <!-- ============================================================== -->
                        <div style="border: 2px solid #0f172a; border-radius: 8px; padding: 2rem; position: relative; background: #ffffff; color: #0f172a;">
                            <!-- Watermark Seal -->
                            <div style="position: absolute; right: 2rem; top: 7rem; opacity: 0.08; pointer-events: none; font-size: 5rem; font-weight: 900; transform: rotate(-25deg); color: #059669; border: 6px dashed #059669; padding: 0.5rem 1.5rem; border-radius: 12px;">
                                PAID
                            </div>

                            <!-- Institutional Header -->
                            <div style="text-align: center; border-bottom: 2px solid #0f172a; padding-bottom: 1.25rem; margin-bottom: 1.5rem;">
                                <div style="display: inline-block; font-size: 0.72rem; text-transform: uppercase; font-weight: 800; letter-spacing: 0.15em; color: #475569; margin-bottom: 0.25rem;">
                                    Office of the Treasurer & Registrar Secretariat
                                </div>
                                <h2 style="font-size: 1.6rem; font-weight: 900; letter-spacing: -0.02em; color: #0f172a; margin: 0;">
                                    OFFICIAL APPLICATION FEE PAYMENT RECEIPT
                                </h2>
                                <p style="font-size: 0.82rem; color: #64748b; margin: 0.35rem 0 0;">
                                    Electronic Fee Settlement & Clearance Certificate (1Link / Kuickpay Switch)
                                </p>
                            </div>

                            <!-- Reference Numbers Bar -->
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 6px; padding: 0.85rem 1rem; margin-bottom: 1.5rem; font-size: 0.82rem;">
                                <div>
                                    <span style="font-size: 0.7rem; text-transform: uppercase; font-weight: 700; color: #64748b; display: block;">1Link Consumer ID</span>
                                    <strong style="font-family: 'JetBrains Mono', monospace; font-size: 1rem; color: #0284c7;">
                                        {{ $selectedSlipApp->billingLedger?->consumer_id }}
                                    </strong>
                                </div>
                                <div>
                                    <span style="font-size: 0.7rem; text-transform: uppercase; font-weight: 700; color: #64748b; display: block;">Bank Transaction Ref</span>
                                    <strong style="font-family: 'JetBrains Mono', monospace; font-size: 0.95rem; color: #0f172a;">
                                        {{ $selectedSlipApp->billingLedger?->transaction_id ?? 'N/A' }}
                                    </strong>
                                </div>
                                <div>
                                    <span style="font-size: 0.7rem; text-transform: uppercase; font-weight: 700; color: #64748b; display: block;">Settlement Date / Time</span>
                                    <strong style="font-size: 0.9rem; color: #0f172a;">
                                        {{ $selectedSlipApp->billingLedger?->updated_at?->format('d M Y, h:i A') ?? now()->format('d M Y') }}
                                    </strong>
                                </div>
                            </div>

                            <!-- Candidate & Position Data Grid -->
                            <div style="border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden; margin-bottom: 1.5rem; font-size: 0.85rem;">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; border-bottom: 1px solid #e2e8f0;">
                                    <div style="padding: 0.65rem 1rem; border-right: 1px solid #e2e8f0; background: #fafafa;">
                                        <span style="color: #64748b; font-size: 0.75rem; display: block;">Candidate Full Name:</span>
                                        <strong style="color: #0f172a; font-size: 0.95rem;">
                                            {{ $selectedSlipApp->user->basicInfo ? trim($selectedSlipApp->user->basicInfo->first_name . ' ' . $selectedSlipApp->user->basicInfo->last_name) : $selectedSlipApp->user->email }}
                                        </strong>
                                    </div>
                                    <div style="padding: 0.65rem 1rem; background: #fafafa;">
                                        <span style="color: #64748b; font-size: 0.75rem; display: block;">CNIC / National Identity:</span>
                                        <strong style="font-family: 'JetBrains Mono', monospace; color: #0f172a; font-size: 0.95rem;">
                                            {{ $selectedSlipApp->user->basicInfo?->cnic_number ?? 'N/A' }}
                                        </strong>
                                    </div>
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 1fr; border-bottom: 1px solid #e2e8f0;">
                                    <div style="padding: 0.65rem 1rem; border-right: 1px solid #e2e8f0;">
                                        <span style="color: #64748b; font-size: 0.75rem; display: block;">Position Applied For:</span>
                                        <strong style="color: #0f172a;">{{ $selectedSlipApp->campaign->title }}</strong>
                                    </div>
                                    <div style="padding: 0.65rem 1rem;">
                                        <span style="color: #64748b; font-size: 0.75rem; display: block;">Job Code & Track:</span>
                                        <strong style="font-family: 'JetBrains Mono', monospace; color: #0284c7;">
                                            {{ $selectedSlipApp->campaign->job_code }} ({{ $selectedSlipApp->campaign->track_type }} Track)
                                        </strong>
                                    </div>
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 1fr;">
                                    <div style="padding: 0.65rem 1rem; border-right: 1px solid #e2e8f0;">
                                        <span style="color: #64748b; font-size: 0.75rem; display: block;">Academic Discipline / Department:</span>
                                        <strong style="color: #0f172a;">{{ $selectedSlipApp->campaign->department }}</strong>
                                    </div>
                                    <div style="padding: 0.65rem 1rem;">
                                        <span style="color: #64748b; font-size: 0.75rem; display: block;">Application Dossier Status:</span>
                                        <span style="color: #059669; font-weight: 700; text-transform: uppercase;">
                                            Fee Settled ({{ $selectedSlipApp->status }})
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Financial Breakdown -->
                            <div style="background: #f0fdf4; border: 1.5px solid #86efac; border-radius: 6px; padding: 1rem 1.25rem; display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
                                <div>
                                    <span style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: #166534; display: block;">
                                        Application Processing Fee (Total Paid)
                                    </span>
                                    <strong style="font-family: 'JetBrains Mono', monospace; font-size: 1.45rem; color: #15803d;">
                                        PKR {{ number_format($selectedSlipApp->billingLedger?->amount ?? $selectedSlipApp->campaign->application_fee, 2) }}
                                    </strong>
                                </div>

                                <div style="text-align: right;">
                                    <div style="display: inline-flex; align-items: center; gap: 0.4rem; background: #15803d; color: #ffffff; padding: 0.45rem 0.9rem; border-radius: 9999px; font-weight: 800; font-size: 0.82rem; letter-spacing: 0.05em;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"/>
                                        </svg>
                                        <span>PAID & CLEARED</span>
                                    </div>
                                    <div style="font-size: 0.72rem; color: #166534; margin-top: 0.35rem; font-weight: 600;">
                                        Method: 1Link / Kuickpay Switch
                                    </div>
                                </div>
                            </div>

                            <!-- Institutional Attestation & Signatures -->
                            <div style="border-top: 1.5px solid #cbd5e1; padding-top: 1.25rem; margin-top: 1.5rem; display: flex; justify-content: space-between; align-items: flex-end; font-size: 0.75rem; color: #64748b;">
                                <div style="max-width: 440px; line-height: 1.5;">
                                    <strong style="color: #0f172a; display: block; margin-bottom: 0.2rem;">Official Electronic Acknowledgement:</strong>
                                    This computer-generated receipt confirms the successful clearance of recruitment examination and processing fees. It is valid without physical treasury signatures. Please retain for scrutiny and interview records.
                                </div>
                                <div style="text-align: center; border-top: 1px solid #94a3b8; padding-top: 0.4rem; min-width: 170px;">
                                    <strong style="color: #0f172a; font-size: 0.82rem; display: block;">Treasurer Secretariat</strong>
                                    <span style="font-size: 0.7rem; color: #64748b;">University Recruitment OS</span>
                                </div>
                            </div>
                        </div>

                    @else
                        <!-- ============================================================== -->
                        <!-- UNPAID STATE: 3-PART PERFORATED BANK CHALLAN SLIP             -->
                        <!-- ============================================================== -->
                        <div class="slip-triplicate">
                            @php
                                $copies = [
                                    1 => '1. CANDIDATE COPY',
                                    2 => '2. BANK BRANCH COPY',
                                    3 => '3. UNIVERSITY TREASURY COPY'
                                ];
                            @endphp

                            @foreach($copies as $copyNum => $copyTitle)
                                <div class="slip-part">
                                    <div class="slip-part-watermark">HIRELY</div>

                                    <!-- Slip Header -->
                                    <div style="text-align: center; border-bottom: 2px solid #0f172a; padding-bottom: 0.65rem; margin-bottom: 0.75rem;">
                                        <div style="font-weight: 800; font-size: 0.85rem; letter-spacing: -0.02em; color: #0f172a;">HIRELY RECRUITMENT</div>
                                        <div style="font-size: 0.68rem; text-transform: uppercase; font-weight: 700; color: #475569;">1Link / Kuickpay Switch</div>
                                        <div style="background: #0f172a; color: #ffffff; font-weight: 800; font-size: 0.65rem; padding: 0.15rem 0.4rem; border-radius: 3px; display: inline-block; margin-top: 0.35rem; letter-spacing: 0.05em;">
                                            {{ $copyTitle }}
                                        </div>
                                    </div>

                                    <!-- Monospace Reference Box -->
                                    <div style="background: #f1f5f9; border: 1px dashed #94a3b8; border-radius: 4px; padding: 0.5rem; text-align: center; margin-bottom: 0.75rem;">
                                        <span style="font-size: 0.62rem; text-transform: uppercase; font-weight: 700; color: #64748b; letter-spacing: 0.05em;">1Link Consumer ID</span>
                                        <div style="font-family: 'JetBrains Mono', monospace; font-size: 1.15rem; font-weight: 800; color: #0284c7; letter-spacing: 1px;">
                                            {{ $selectedSlipApp->billingLedger?->consumer_id }}
                                        </div>
                                        <div style="font-size: 0.62rem; color: #475569; margin-top: 2px;">Payable at any Bank App / ATM</div>
                                    </div>

                                    <!-- Data Grid -->
                                    <div style="display: flex; flex-direction: column; gap: 0.4rem; font-size: 0.74rem;">
                                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #e2e8f0; padding-bottom: 2px;">
                                            <span style="color: #64748b;">Candidate:</span>
                                            <strong style="color: #0f172a; text-align: right;">{{ $selectedSlipApp->user->basicInfo?->first_name }} {{ $selectedSlipApp->user->basicInfo?->last_name }}</strong>
                                        </div>

                                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #e2e8f0; padding-bottom: 2px;">
                                            <span style="color: #64748b;">CNIC:</span>
                                            <strong style="font-family: 'JetBrains Mono', monospace; color: #0f172a;">{{ $selectedSlipApp->user->basicInfo?->cnic_number }}</strong>
                                        </div>

                                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #e2e8f0; padding-bottom: 2px;">
                                            <span style="color: #64748b;">Position:</span>
                                            <strong style="color: #0f172a; text-align: right; max-width: 140px;">{{ $selectedSlipApp->campaign->title }}</strong>
                                        </div>

                                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #e2e8f0; padding-bottom: 2px;">
                                            <span style="color: #64748b;">Job Code:</span>
                                            <strong style="font-family: 'JetBrains Mono', monospace; color: #0f172a;">{{ $selectedSlipApp->campaign->job_code }}</strong>
                                        </div>

                                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #e2e8f0; padding-bottom: 2px;">
                                            <span style="color: #64748b;">Department:</span>
                                            <span style="color: #0f172a; text-align: right;">{{ $selectedSlipApp->campaign->department }}</span>
                                        </div>

                                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #e2e8f0; padding-bottom: 2px;">
                                            <span style="color: #64748b;">Due Date:</span>
                                            <span style="color: #0f172a;">{{ $selectedSlipApp->campaign->closing_date?->format('d M Y') }}</span>
                                        </div>

                                        <div style="display: flex; justify-content: space-between; background: #fef3c7; padding: 0.4rem; border-radius: 4px; margin-top: 0.35rem;">
                                            <span style="font-weight: 700; color: #92400e;">Fee Amount:</span>
                                            <strong style="font-family: 'JetBrains Mono', monospace; font-size: 0.95rem; color: #b45309;">
                                                PKR {{ number_format($selectedSlipApp->billingLedger?->amount ?? $selectedSlipApp->campaign->application_fee) }}
                                            </strong>
                                        </div>

                                        <!-- Differentiated Show vs Print Badge -->
                                        <!-- ON SCREEN (SHOW): Web badge with reminder -->
                                        <div class="screen-only" style="text-align: center; margin-top: 0.5rem;">
                                            <span style="background: #fef3c7; color: #b45309; font-weight: 800; font-size: 0.68rem; padding: 0.2rem 0.5rem; border-radius: 4px; border: 1px solid #fde68a; display: inline-block;">
                                                ⚡ PAYMENT PENDING
                                            </span>
                                            <span style="display: block; font-size: 0.65rem; color: #64748b; margin-top: 2px;">Pay via ATM / App or Bank Counter</span>
                                        </div>

                                        <!-- ON PRINT: Official deposit instructions without the web emoji badge -->
                                        <div class="print-only" style="border: 1px dashed #cbd5e1; border-radius: 4px; padding: 0.35rem; margin-top: 0.4rem; text-align: center; font-size: 0.65rem; color: #475569;">
                                            <strong style="color: #0f172a; text-transform: uppercase;">Amount Due: PKR {{ number_format($selectedSlipApp->billingLedger?->amount ?? $selectedSlipApp->campaign->application_fee) }}</strong>
                                            <div style="font-size: 0.6rem; color: #64748b;">Depositor / Cashier Validation Required</div>
                                        </div>
                                    </div>

                                    <!-- Signature Lines -->
                                    <div style="display: flex; justify-content: space-between; margin-top: 1.5rem; font-size: 0.65rem; color: #64748b; border-top: 1px solid #cbd5e1; padding-top: 0.5rem;">
                                        <span>Depositor Signature</span>
                                        <span>Bank Stamp & Signature</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL 3: GRIEVANCE APPEAL DRAWER                               -->
    <!-- ============================================================== -->
    @if($showAppealForm)
        <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 1.5rem;">
            <div class="card" style="width: 100%; max-width: 620px; border-color: var(--border-color); box-shadow: var(--shadow-lg); background: var(--bg-card-solid);">
                <div class="flex justify-between align-center mb-4" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                    <div>
                        <h3 class="card-title" style="color: var(--warning); margin: 0; font-size: 1.2rem;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                                <path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                                <path d="M7 21h10"/>
                                <path d="M12 3v18"/>
                            </svg>
                            Formal Grievance Redressal Appeal
                        </h3>
                        <p class="card-subtitle">Statutory appeal within 7 calendar days under University Recruitment Regulations.</p>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="$set('showAppealForm', false)">✕</button>
                </div>

                <div class="card mb-4" style="background: rgba(245, 158, 11, 0.08); border-color: var(--warning-glow); padding: 0.85rem 1rem;">
                    <p style="color: #fde68a; font-size: 0.82rem; margin: 0; line-height: 1.45;">
                        <strong>Statutory Requirement:</strong> Please explain the grounds for your appeal (e.g. HEC degree equivalence, attested experience certificate, publication citation). You must attach supporting documentary evidence.
                    </p>
                </div>
                
                <form wire:submit.prevent="submitAppeal">
                    <div class="form-group mb-4">
                        <label class="form-label">Grounds of Appeal / Justification Narrative * (Min 20 characters)</label>
                        <textarea class="form-control" rows="5" wire:model="appealText" placeholder="Specify equivalence details, missing document clarification, or credentials reference logs..."></textarea>
                        @error('appealText') <span style="color: var(--danger); font-size: 0.82rem; margin-top: 0.35rem; display: block; font-weight: 500;">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group mb-6">
                        <label class="form-label">Supporting Proof / Equivalence Certificate * (PDF or Image, max 2MB)</label>
                        <input type="file" class="form-control" wire:model="appealProof">
                        <div wire:loading wire:target="appealProof" style="color: var(--secondary); font-size: 0.8rem; margin-top: 0.35rem;">
                            Uploading file, please wait...
                        </div>
                        @error('appealProof') <span style="color: var(--danger); font-size: 0.82rem; margin-top: 0.35rem; display: block; font-weight: 500;">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-between align-center" style="border-top: 1px solid var(--border-color); padding-top: 1rem;">
                        <button type="button" class="btn btn-secondary" wire:click="$set('showAppealForm', false)">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-danger" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="submitAppeal">Submit Formal Appeal</span>
                            <span wire:loading wire:target="submitAppeal">Submitting...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

<script>
    function copyConsumerId(consumerId, el) {
        if (!navigator.clipboard) {
            const input = document.createElement('input');
            input.value = consumerId;
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);
        } else {
            navigator.clipboard.writeText(consumerId);
        }

        const originalText = el.innerHTML;
        el.innerHTML = '<span>✓ Copied!</span>';
        setTimeout(() => {
            el.innerHTML = originalText;
        }, 1800);
    }
</script>
