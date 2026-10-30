<div>
    <!-- Executive Control Header -->
    <div class="page-hero-header">
        <div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div class="brand-icon-box" style="background: linear-gradient(135deg, rgba(99,102,241,0.25), rgba(79,70,229,0.4)); box-shadow: 0 4px 14px rgba(99,102,241,0.25);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" style="color: #ffffff;">
                        <path d="M3 3v18h18"/>
                        <path d="m19 9-5 5-4-4-3 3"/>
                    </svg>
                </div>
                <div>
                    <h1>Operations Desk & Executive Overview</h1>
                    <p>Real-time statutory metrics, multi-stage recruitment funnel, quota compliance, and live board desks.</p>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions & Session Pill -->
        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <a href="/admin/reviewers" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.6rem 1rem; border-radius: 10px; font-weight: 700; font-size: 0.84rem;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <span>Academic Reviewers</span>
            </a>

            <a href="/admin/management?tab=campaigns" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.65rem 1.25rem; border-radius: 10px; font-weight: 700; font-size: 0.84rem; box-shadow: 0 4px 18px -2px rgba(99,102,241,0.5);">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Manage Campaigns</span>
            </a>

            <div style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); padding: 0.5rem 0.9rem; border-radius: 10px;" class="flex align-center gap-2">
                <span class="badge-dot" style="background: var(--success); box-shadow: 0 0 6px var(--success);"></span>
                <span style="font-size: 0.78rem; font-weight: 700; color: var(--color-text-secondary); text-transform: uppercase; letter-spacing: 0.04em;">
                    {{ str_replace('-', ' ', auth()->user()->roles->first()?->name ?? 'Admin') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Interactive KPI Metrics Summary Cards -->
    <div class="stat-kpi-grid">
        <!-- Paid / In Scrutiny -->
        <div class="metric-card" style="--card-accent: var(--secondary); --metric-icon-bg: var(--secondary-light); --metric-icon-color: var(--secondary); --metric-icon-glow: var(--secondary-glow);">
            <div class="metric-header">
                <span class="metric-label">Paid / In Scrutiny</span>
                <div class="metric-icon-wrap">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="14" x="2" y="5" rx="2"/>
                        <line x1="2" x2="22" y1="10" y2="10"/>
                    </svg>
                </div>
            </div>
            <div class="metric-value" style="color: var(--secondary);">{{ $metrics['paid'] }}</div>
            <div class="metric-subtext">Waiting for committee verification</div>
        </div>

        <!-- Eligible Candidates -->
        <div class="metric-card" style="--card-accent: var(--success); --metric-icon-bg: var(--success-light); --metric-icon-color: var(--success); --metric-icon-glow: var(--success-glow);">
            <div class="metric-header">
                <span class="metric-label">Eligible Candidates</span>
                <div class="metric-icon-wrap">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
            </div>
            <div class="metric-value" style="color: var(--success);">{{ $metrics['eligible'] }}</div>
            <div class="metric-subtext">Passed initial qualification checks</div>
        </div>

        <!-- Disqualified -->
        <div class="metric-card" style="--card-accent: var(--danger); --metric-icon-bg: var(--danger-light); --metric-icon-color: var(--danger); --metric-icon-glow: var(--danger-glow);">
            <div class="metric-header">
                <span class="metric-label">Disqualified</span>
                <div class="metric-icon-wrap">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </div>
            </div>
            <div class="metric-value" style="color: var(--danger);">{{ $metrics['ineligible'] }}</div>
            <div class="metric-subtext">Marked failed with formal deficiency</div>
        </div>

        <!-- Appealed Grievances -->
        <div class="metric-card" style="--card-accent: var(--warning); --metric-icon-bg: var(--warning-light); --metric-icon-color: var(--warning); --metric-icon-glow: var(--warning-glow);">
            <div class="metric-header">
                <span class="metric-label">Appealed Grievances</span>
                <div class="metric-icon-wrap">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                        <path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                        <path d="M7 21h10"/>
                        <path d="M12 3v18"/>
                    </svg>
                </div>
            </div>
            <div class="metric-value" style="color: var(--warning);">{{ $metrics['appealed'] }}</div>
            <div class="metric-subtext">Active appeals under judicial review</div>
        </div>
    </div>

    <!-- Visual Recruitment Lifecycle Funnel & Statutory Quota Distribution -->
    <div class="grid grid-cols-3 gap-6 mb-6">
        
        <!-- Multi-Stage Recruitment Conversion Funnel (2 Columns) -->
        <div class="card flex-col gap-4" style="grid-column: span 2;">
            <div class="flex justify-between align-center">
                <div class="flex align-center gap-2">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(99,102,241,0.12); border: 1px solid rgba(99,102,241,0.25); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="card-title" style="margin: 0; font-size: 1.05rem;">Recruitment Conversion Funnel</h3>
                        <p style="margin: 0; font-size: 0.78rem; color: var(--color-text-muted);">Progression of {{ $funnel['total'] }} candidacies across institutional screening gates</p>
                    </div>
                </div>
                <span class="badge" style="background: rgba(99,102,241,0.1); color: var(--primary); border: 1px solid rgba(99,102,241,0.3); font-weight: 700; font-size: 0.76rem;">
                    Institutional Pipeline
                </span>
            </div>

            <!-- Funnel Stage Bars -->
            <div style="display: flex; flex-direction: column; gap: 0.85rem; margin-top: 0.5rem;">
                <!-- Stage 1: Applications Intake -->
                <div>
                    <div class="flex justify-between align-center mb-1">
                        <span style="font-size: 0.84rem; font-weight: 700; color: var(--color-text-primary); display: flex; align-items: center; gap: 0.45rem;">
                            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #6366f1;"></span>
                            1. Application Intake & Submissions
                        </span>
                        <div class="flex align-center gap-2">
                            <span style="font-weight: 800; font-size: 0.86rem; color: var(--color-text-primary);">{{ $funnel['total'] }}</span>
                            <span class="badge" style="background: rgba(255,255,255,0.05); font-size: 0.72rem; color: var(--color-text-muted);">100%</span>
                        </div>
                    </div>
                    <div style="width: 100%; height: 9px; background: rgba(255,255,255,0.05); border-radius: 6px; overflow: hidden; border: 1px solid var(--border-color);">
                        <div style="width: 100%; height: 100%; background: linear-gradient(90deg, #6366f1, #818cf8); border-radius: 6px;"></div>
                    </div>
                </div>

                <!-- Stage 2: 1Link Fee Settlement -->
                <div>
                    <div class="flex justify-between align-center mb-1">
                        <span style="font-size: 0.84rem; font-weight: 700; color: var(--color-text-primary); display: flex; align-items: center; gap: 0.45rem;">
                            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #06b6d4;"></span>
                            2. 1Link / Kuickpay Fee Settlement
                        </span>
                        <div class="flex align-center gap-2">
                            <span style="font-weight: 800; font-size: 0.86rem; color: var(--secondary);">{{ $funnel['fee_settled'] }}</span>
                            <span class="badge" style="background: rgba(6,182,212,0.12); font-size: 0.72rem; color: var(--secondary);">{{ $funnel['fee_settled_pct'] }}%</span>
                        </div>
                    </div>
                    <div style="width: 100%; height: 9px; background: rgba(255,255,255,0.05); border-radius: 6px; overflow: hidden; border: 1px solid var(--border-color);">
                        <div style="width: {{ $funnel['fee_settled_pct'] }}%; height: 100%; background: linear-gradient(90deg, #06b6d4, #22d3ee); border-radius: 6px; transition: width 0.6s ease;"></div>
                    </div>
                </div>

                <!-- Stage 3: Scrutiny Cleared -->
                <div>
                    <div class="flex justify-between align-center mb-1">
                        <span style="font-size: 0.84rem; font-weight: 700; color: var(--color-text-primary); display: flex; align-items: center; gap: 0.45rem;">
                            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span>
                            3. Scrutiny Committee Eligibility Clearance
                        </span>
                        <div class="flex align-center gap-2">
                            <span style="font-weight: 800; font-size: 0.86rem; color: var(--success);">{{ $funnel['scrutiny_cleared'] }}</span>
                            <span class="badge" style="background: rgba(16,185,129,0.12); font-size: 0.72rem; color: var(--success);">{{ $funnel['scrutiny_cleared_pct'] }}%</span>
                        </div>
                    </div>
                    <div style="width: 100%; height: 9px; background: rgba(255,255,255,0.05); border-radius: 6px; overflow: hidden; border: 1px solid var(--border-color);">
                        <div style="width: {{ $funnel['scrutiny_cleared_pct'] }}%; height: 100%; background: linear-gradient(90deg, #10b981, #34d399); border-radius: 6px; transition: width 0.6s ease;"></div>
                    </div>
                </div>

                <!-- Stage 4: Expert Peer Review -->
                <div>
                    <div class="flex justify-between align-center mb-1">
                        <span style="font-size: 0.84rem; font-weight: 700; color: var(--color-text-primary); display: flex; align-items: center; gap: 0.45rem;">
                            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #f59e0b;"></span>
                            4. External Referee & Peer Evaluations
                        </span>
                        <div class="flex align-center gap-2">
                            <span style="font-weight: 800; font-size: 0.86rem; color: var(--warning);">{{ $funnel['peer_reviewed'] }}</span>
                            <span class="badge" style="background: rgba(245,158,11,0.12); font-size: 0.72rem; color: var(--warning);">{{ $funnel['peer_reviewed_pct'] }}%</span>
                        </div>
                    </div>
                    <div style="width: 100%; height: 9px; background: rgba(255,255,255,0.05); border-radius: 6px; overflow: hidden; border: 1px solid var(--border-color);">
                        <div style="width: {{ $funnel['peer_reviewed_pct'] }}%; height: 100%; background: linear-gradient(90deg, #f59e0b, #fbbf24); border-radius: 6px; transition: width 0.6s ease;"></div>
                    </div>
                </div>

                <!-- Stage 5: Selection Board & Final Resolution -->
                <div>
                    <div class="flex justify-between align-center mb-1">
                        <span style="font-size: 0.84rem; font-weight: 700; color: var(--color-text-primary); display: flex; align-items: center; gap: 0.45rem;">
                            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #ec4899;"></span>
                            5. Statutory Selection Board & Syndicate Award
                        </span>
                        <div class="flex align-center gap-2">
                            <span style="font-weight: 800; font-size: 0.86rem; color: #ec4899;">{{ $funnel['board_approved'] }}</span>
                            <span class="badge" style="background: rgba(236,72,153,0.12); font-size: 0.72rem; color: #ec4899;">{{ $funnel['board_approved_pct'] }}%</span>
                        </div>
                    </div>
                    <div style="width: 100%; height: 9px; background: rgba(255,255,255,0.05); border-radius: 6px; overflow: hidden; border: 1px solid var(--border-color);">
                        <div style="width: {{ $funnel['board_approved_pct'] }}%; height: 100%; background: linear-gradient(90deg, #ec4899, #f472b6); border-radius: 6px; transition: width 0.6s ease;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statutory Regional Quota Distribution Matrix (1 Column) -->
        <div class="card flex-col gap-4">
            <div class="flex justify-between align-center">
                <div class="flex align-center gap-2">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.25); display: flex; align-items: center; justify-content: center; color: var(--success);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                            <path d="M12 2a10 10 0 1 0 10 10H12V2z"/>
                            <path d="M12 12 2.1 10a10.1 10.1 0 0 1 10-8v10z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="card-title" style="margin: 0; font-size: 1.05rem;">Statutory Quota Matrix</h3>
                        <p style="margin: 0; font-size: 0.78rem; color: var(--color-text-muted);">Regional & reserve quotas</p>
                    </div>
                </div>
                <span class="badge" style="background: rgba(16,185,129,0.1); color: var(--success); border: 1px solid rgba(16,185,129,0.3); font-weight: 700; font-size: 0.72rem;">
                    {{ $totalQuotaCount }} Candidates
                </span>
            </div>

            <!-- Quota Bars -->
            <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-top: 0.35rem;">
                @foreach($quotaStats as $q)
                    @php
                        $pct = round(($q->count / $totalQuotaCount) * 100, 1);
                        $colorClass = match($q->quota_name) {
                            'Punjab' => '#3b82f6',
                            'Sindh (Urban)', 'Sindh (Rural)' => '#06b6d4',
                            'Khyber Pakhtunkhwa' => '#10b981',
                            'Balochistan' => '#f59e0b',
                            'Women Quota' => '#ec4899',
                            default => '#6366f1',
                        };
                    @endphp
                    <div>
                        <div class="flex justify-between align-center mb-1">
                            <span style="font-size: 0.8rem; font-weight: 600; color: var(--color-text-primary);">
                                {{ $q->quota_name }}
                            </span>
                            <span style="font-size: 0.78rem; font-weight: 700; color: {{ $colorClass }};">
                                {{ $q->count }} <span style="color: var(--color-text-muted); font-weight: 500;">({{ $pct }}%)</span>
                            </span>
                        </div>
                        <div style="width: 100%; height: 7px; background: rgba(255,255,255,0.05); border-radius: 4px; overflow: hidden; border: 1px solid var(--border-color);">
                            <div style="width: {{ $pct }}%; height: 100%; background: {{ $colorClass }}; border-radius: 4px;"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Career Track Summary Pills -->
            <div style="border-top: 1px solid var(--border-color); padding-top: 0.75rem; display: flex; justify-content: space-between; align-items: center;">
                <div style="font-size: 0.75rem; color: var(--color-text-muted);">
                    Faculty: <strong style="color: var(--color-text-primary);">{{ $tracks['ACADEMIC'] }}</strong>
                </div>
                <div style="font-size: 0.75rem; color: var(--color-text-muted);">
                    Officers: <strong style="color: var(--color-text-primary);">{{ $tracks['ADMINISTRATIVE'] }}</strong>
                </div>
                <div style="font-size: 0.75rem; color: var(--color-text-muted);">
                    Staff: <strong style="color: var(--color-text-primary);">{{ $tracks['SUPPORT'] }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue & 1Link Settlement Overview & Active Campaigns Monitor -->
    <div class="grid grid-cols-3 gap-6 mb-6">
        
        <!-- Revenue & Settlement Efficiency Card (1 Column) -->
        <div class="card flex-col gap-4">
            <div class="flex justify-between align-center">
                <div class="flex align-center gap-2">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.25); display: flex; align-items: center; justify-content: center; color: var(--success);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                            <rect width="20" height="14" x="2" y="5" rx="2"/>
                            <line x1="2" x2="22" y1="10" y2="10"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="card-title" style="margin: 0; font-size: 1.05rem;">Revenue & Settlement</h3>
                        <p style="margin: 0; font-size: 0.78rem; color: var(--color-text-muted);">1Link / Kuickpay ledger</p>
                    </div>
                </div>
                <span class="badge" style="background: rgba(16,185,129,0.12); color: var(--success); border: 1px solid rgba(16,185,129,0.3); font-weight: 800; font-size: 0.74rem;">
                    {{ $revenueStats['settlement_rate'] }}% Settled
                </span>
            </div>

            <!-- Revenue Numbers -->
            <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1rem;">
                <div style="font-size: 0.75rem; color: var(--color-text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">
                    Settled Application Receipts
                </div>
                <div style="font-size: 1.6rem; font-weight: 900; color: var(--success); margin: 0.25rem 0 0.5rem; letter-spacing: -0.02em;">
                    PKR {{ number_format($revenueStats['settled']) }}
                </div>
                <div class="flex justify-between align-center" style="font-size: 0.8rem; color: var(--color-text-secondary); border-top: 1px solid var(--border-color); padding-top: 0.5rem;">
                    <span>Pending Bank Settlement:</span>
                    <strong style="color: var(--warning);">PKR {{ number_format($revenueStats['pending']) }}</strong>
                </div>
            </div>

            <div class="flex justify-between align-center" style="font-size: 0.8rem; color: var(--color-text-muted);">
                <span>Total 1Link Invoices Generated:</span>
                <span class="badge" style="background: rgba(255,255,255,0.04); font-size: 0.75rem; color: var(--color-text-primary);">
                    {{ $revenueStats['settled_txns'] }} / {{ $revenueStats['total_txns'] }} Txns
                </span>
            </div>
        </div>

        <!-- Active Campaigns Monitor (2 Columns) -->
        <div class="card" style="grid-column: span 2;">
            <div class="flex justify-between align-center mb-4">
                <div class="flex align-center gap-2">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(6,182,212,0.12); border: 1px solid rgba(6,182,212,0.25); display: flex; align-items: center; justify-content: center; color: var(--secondary);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                            <path d="m3 11 18-5v12L3 14v-3z"/>
                            <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="card-title" style="margin: 0; font-size: 1.05rem;">Active Campaigns Monitor</h3>
                        <p style="margin: 0; font-size: 0.78rem; color: var(--color-text-muted);">Positions currently open or under statutory selection</p>
                    </div>
                </div>
                <a href="/admin/management?tab=campaigns" class="btn btn-secondary btn-sm" style="font-size: 0.78rem;">View All Campaigns</a>
            </div>
            
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Job Code</th>
                            <th>Campaign Position</th>
                            <th>Department Scope</th>
                            <th>Submissions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($campaigns->take(5) as $camp)
                            <tr>
                                <td>
                                    <code style="color: var(--secondary); font-family: 'JetBrains Mono', monospace; font-size: 0.82rem; font-weight: 700; background: var(--secondary-light); padding: 0.2rem 0.5rem; border-radius: 4px;">
                                        {{ $camp->job_code }}
                                    </code>
                                </td>
                                <td>
                                    <strong style="color: var(--color-text-primary); font-size: 0.92rem;">{{ $camp->title }}</strong>
                                </td>
                                <td>
                                    <span style="color: var(--color-text-secondary); font-size: 0.85rem;">{{ $camp->department }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-appealed">
                                        {{ $camp->applications_count }} files
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center" style="color: var(--color-text-muted); padding: 2rem;">No campaigns active at present.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Scrutiny Pipeline Section (With Candidate Portraits) -->
    @if(!empty($pendingScrutiny))
        <div class="card mb-6" style="border-color: var(--border-color);">
            <div class="flex justify-between align-center mb-4">
                <div class="flex align-center gap-2">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(99,102,241,0.12); border: 1px solid rgba(99,102,241,0.25); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.3-4.3"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="card-title" style="margin: 0; font-size: 1.05rem;">Document Scrutiny Desk (Action Required)</h3>
                        <p style="margin: 0; font-size: 0.78rem; color: var(--color-text-muted);">Verified paid applications requiring credential inspection</p>
                    </div>
                </div>
                <span class="badge badge-pending">{{ count($pendingScrutiny) }} Pending Dossiers</span>
            </div>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Candidate Dossier</th>
                            <th>Track Scope</th>
                            <th>Campaign Code</th>
                            <th>Quota & Fee</th>
                            <th>Scrutiny Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingScrutiny as $app)
                            @php
                                $bi = $app->user->basicInfo;
                                $candidateName = $bi ? trim($bi->first_name . ' ' . $bi->last_name) : $app->user->email;
                                $avatar = $bi?->profile_picture_url;
                            @endphp
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        @if($avatar)
                                            <div class="user-avatar-circle" style="width: 38px; height: 38px; border-radius: 10px; overflow: hidden; padding: 0; flex-shrink: 0; border: 1px solid var(--border-color);">
                                                <img src="{{ $avatar }}" alt="{{ $candidateName }}" onerror="this.style.display='none';" style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                        @else
                                            <div class="user-avatar-circle" style="width: 38px; height: 38px; font-size: 0.8rem; border-radius: 10px;">
                                                {{ strtoupper(substr($candidateName, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <strong style="color: var(--color-text-primary); font-size: 0.92rem;">
                                                {{ $candidateName }}
                                            </strong><br>
                                            <span style="color: var(--color-text-muted); font-family: 'JetBrains Mono', monospace; font-size: 0.78rem;">
                                                CNIC: {{ $bi?->cnic_number ?? 'Pending' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge badge-appealed">{{ $app->campaign->track_type }}</span></td>
                                <td>
                                    <code style="color: var(--secondary); font-family: 'JetBrains Mono', monospace; font-size: 0.82rem; font-weight: 700;">
                                        {{ $app->campaign->job_code }}
                                    </code>
                                </td>
                                <td>
                                    <div style="display: flex; flex-direction: column; gap: 0.15rem;">
                                        <span class="badge" style="background: rgba(255,255,255,0.04); font-size: 0.72rem; color: var(--color-text-secondary); align-self: flex-start;">
                                            {{ $bi?->quota ?? 'Open Merit' }}
                                        </span>
                                        <strong style="color: var(--warning); font-size: 0.84rem;">PKR {{ number_format($app->billingLedger?->amount, 2) }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <a href="/admin/verification/{{ $app->id }}" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 0.35rem;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="11" cy="11" r="8"/>
                                            <path d="m21 21-4.3-4.3"/>
                                        </svg>
                                        <span>Scrutinize</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center" style="color: var(--color-text-muted); padding: 2rem;">No paid dossiers waiting in scrutiny queue.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Selection Board Evaluation Section (With Candidate Portraits) -->
    @if(!empty($pendingEvaluations))
        <div class="card mb-6" style="border-color: var(--border-color);">
            <div class="flex justify-between align-center mb-4">
                <div class="flex align-center gap-2">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.25); display: flex; align-items: center; justify-content: center; color: var(--success);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="card-title" style="margin: 0; font-size: 1.05rem;">Selection Board & VC Evaluation Desk</h3>
                        <p style="margin: 0; font-size: 0.78rem; color: var(--color-text-muted);">Shortlisted candidates awaiting board interview and grading</p>
                    </div>
                </div>
                <span class="badge badge-eligible">{{ count($pendingEvaluations) }} Verified Candidates</span>
            </div>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Candidate Account</th>
                            <th>Campaign Position</th>
                            <th>CNIC & Quota</th>
                            <th>Verification Gate</th>
                            <th>Grading Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingEvaluations as $app)
                            @php
                                $bi = $app->user->basicInfo;
                                $candidateName = $bi ? trim($bi->first_name . ' ' . $bi->last_name) : $app->user->email;
                                $avatar = $bi?->profile_picture_url;
                            @endphp
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        @if($avatar)
                                            <div class="user-avatar-circle" style="width: 38px; height: 38px; border-radius: 10px; overflow: hidden; padding: 0; flex-shrink: 0; border: 1px solid var(--border-color);">
                                                <img src="{{ $avatar }}" alt="{{ $candidateName }}" onerror="this.style.display='none';" style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                        @else
                                            <div class="user-avatar-circle" style="width: 38px; height: 38px; font-size: 0.8rem; border-radius: 10px; background: linear-gradient(135deg, var(--success), #059669);">
                                                {{ strtoupper(substr($candidateName, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <strong style="color: var(--color-text-primary); font-size: 0.92rem;">
                                                {{ $candidateName }}
                                            </strong><br>
                                            <span style="color: var(--color-text-secondary); font-size: 0.8rem;">{{ $app->user->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong style="color: var(--color-text-primary); font-size: 0.9rem;">{{ $app->campaign->title }}</strong><br>
                                    <code style="color: var(--color-text-muted); font-size: 0.78rem;">{{ $app->campaign->job_code }}</code>
                                </td>
                                <td>
                                    <code style="font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; color: var(--color-text-secondary);">
                                        {{ $bi?->cnic_number ?? 'Pending' }}
                                    </code><br>
                                    <span class="badge" style="background: rgba(255,255,255,0.04); font-size: 0.7rem; color: var(--color-text-muted); margin-top: 0.2rem;">
                                        {{ $bi?->quota ?? 'Open Merit' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-paid">
                                        <span class="badge-dot" style="background: var(--success);"></span>
                                        CLEARED (ELIGIBLE)
                                    </span>
                                </td>
                                <td>
                                    <a href="/board/evaluate/{{ $app->id }}" class="btn btn-success btn-sm" style="display: inline-flex; align-items: center; gap: 0.35rem;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                        </svg>
                                        <span>Grade Candidate</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center" style="color: var(--color-text-muted); padding: 2rem;">No eligible candidates waiting for selection board evaluation.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
