<div>
    <!-- Dynamic Header -->
    <div class="card mb-6" style="background: var(--bg-card); border-color: var(--border-color); box-shadow: var(--shadow-md);">
        <div class="flex justify-between align-center" style="flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1 class="card-title" style="font-size: 1.6rem; font-weight: 800; letter-spacing: -0.03em;">
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; background: var(--primary-light); color: var(--primary); margin-right: 0.5rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 3v18h18"/>
                            <path d="m19 9-5 5-4-4-3 3"/>
                        </svg>
                    </span>
                    Administrative Operations Control
                </h1>
                <p class="card-subtitle">Real-time metrics, scrutiny pipelines, and selection board candidate evaluation desks.</p>
            </div>
            
            <!-- Quick Actions & Session Pill -->
            <div class="flex align-center gap-3">
                <a href="/admin/management?tab=campaigns" class="btn btn-primary btn-sm">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>Manage Campaigns</span>
                </a>

                <div style="background: var(--bg-btn-secondary); border: 1px solid var(--border-color); padding: 0.45rem 0.9rem; border-radius: var(--radius-sm);" class="flex align-center gap-2">
                    <span class="badge-dot" style="background: var(--success); box-shadow: 0 0 6px var(--success);"></span>
                    <span style="font-size: 0.78rem; font-weight: 700; color: var(--color-text-secondary); text-transform: uppercase; letter-spacing: 0.04em;">
                        {{ str_replace('-', ' ', auth()->user()->roles->first()?->name ?? 'Admin') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Interactive KPI Metrics Summary Cards -->
    <div class="grid grid-cols-4 gap-6 mb-6">
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
            <div class="metric-subtext">Waiting for document verification</div>
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
            <div class="metric-subtext">Cleared verification checks</div>
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
            <div class="metric-subtext">Marked failed in scrutiny</div>
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
            <div class="metric-subtext">Grievances in 7-day window</div>
        </div>
    </div>

    <!-- Graphics and Track Details Panel -->
    <div class="grid grid-cols-3 gap-6 mb-6">
        
        <!-- Track Breakdown & Graphical SVG Progress Indicators -->
        <div class="card flex-col gap-4">
            <h3 class="card-title" style="font-size: 1.05rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/>
                    <path d="M2 12h20"/>
                </svg>
                Merit Pipeline Progress
            </h3>
            
            <div class="flex justify-between align-center mt-2 mb-2" style="padding: 0 0.5rem;">
                <!-- SVG Ring 1: Scrutiny -->
                <div class="flex-col align-center text-center">
                    <div style="position: relative; width: 68px; height: 68px;">
                        <svg width="68" height="68" viewBox="0 0 36 36">
                            <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="var(--border-color)" stroke-width="3.5" />
                            <path stroke-dasharray="75, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="var(--secondary)" stroke-width="3.5" stroke-linecap="round" />
                        </svg>
                        <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 800; color: var(--color-text-primary);">
                            75%
                        </div>
                    </div>
                    <span style="font-size: 0.72rem; color: var(--color-text-secondary); margin-top: 0.4rem; font-weight: 600;">Scrutinized</span>
                </div>
                
                <!-- SVG Ring 2: Paid -->
                <div class="flex-col align-center text-center">
                    <div style="position: relative; width: 68px; height: 68px;">
                        <svg width="68" height="68" viewBox="0 0 36 36">
                            <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="var(--border-color)" stroke-width="3.5" />
                            <path stroke-dasharray="90, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="var(--success)" stroke-width="3.5" stroke-linecap="round" />
                        </svg>
                        <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 800; color: var(--color-text-primary);">
                            90%
                        </div>
                    </div>
                    <span style="font-size: 0.72rem; color: var(--color-text-secondary); margin-top: 0.4rem; font-weight: 600;">Settled</span>
                </div>

                <!-- SVG Ring 3: Evaluated -->
                <div class="flex-col align-center text-center">
                    <div style="position: relative; width: 68px; height: 68px;">
                        <svg width="68" height="68" viewBox="0 0 36 36">
                            <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="var(--border-color)" stroke-width="3.5" />
                            <path stroke-dasharray="45, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="var(--warning)" stroke-width="3.5" stroke-linecap="round" />
                        </svg>
                        <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 800; color: var(--color-text-primary);">
                            45%
                        </div>
                    </div>
                    <span style="font-size: 0.72rem; color: var(--color-text-secondary); margin-top: 0.4rem; font-weight: 600;">Graded</span>
                </div>
            </div>

            <!-- Tracks metrics -->
            <div class="flex-col gap-2" style="border-top: 1px solid var(--border-color); padding-top: 1rem;">
                <div class="flex justify-between align-center" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                    <span style="color: var(--color-text-secondary); font-size: 0.85rem;">Academic Faculty</span>
                    <strong style="color: var(--secondary); font-size: 0.95rem;">{{ $tracks['ACADEMIC'] }}</strong>
                </div>
                <div class="flex justify-between align-center" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-top: 0.25rem;">
                    <span style="color: var(--color-text-secondary); font-size: 0.85rem;">Administrative Officers</span>
                    <strong style="color: var(--secondary); font-size: 0.95rem;">{{ $tracks['ADMINISTRATIVE'] }}</strong>
                </div>
                <div class="flex justify-between align-center" style="padding-bottom: 0.25rem; margin-top: 0.25rem;">
                    <span style="color: var(--color-text-secondary); font-size: 0.85rem;">Support Staff</span>
                    <strong style="color: var(--secondary); font-size: 0.95rem;">{{ $tracks['SUPPORT'] }}</strong>
                </div>
            </div>
        </div>

        <!-- Active Campaign Monitors -->
        <div class="card" style="grid-column: span 2;">
            <div class="flex justify-between align-center mb-4">
                <h3 class="card-title" style="font-size: 1.05rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--secondary)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m3 11 18-5v12L3 14v-3z"/>
                        <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>
                    </svg>
                    Active Campaigns Monitor
                </h3>
                <a href="/admin/management?tab=campaigns" class="btn btn-secondary btn-sm" style="font-size: 0.78rem;">View All</a>
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
                        @forelse($campaigns as $camp)
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

    <!-- Scrutiny Pipeline Section -->
    @if(!empty($pendingScrutiny))
        <div class="card mb-6" style="border-color: var(--border-color);">
            <div class="flex justify-between align-center mb-4">
                <h3 class="card-title" style="font-size: 1.05rem; color: var(--color-text-primary);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.3-4.3"/>
                    </svg>
                    Document Scrutiny Desk (Action Required)
                </h3>
                <span class="badge badge-pending">{{ count($pendingScrutiny) }} Pending Dossiers</span>
            </div>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Candidate Dossier</th>
                            <th>Track Scope</th>
                            <th>Campaign Code</th>
                            <th>Voucher Fee</th>
                            <th>Scrutiny Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingScrutiny as $app)
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div class="user-avatar-circle" style="width: 34px; height: 34px; font-size: 0.75rem;">
                                            {{ strtoupper(substr($app->user->basicInfo?->first_name ?: $app->user->email, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong style="color: var(--color-text-primary); font-size: 0.92rem;">
                                                {{ $app->user->basicInfo ? trim($app->user->basicInfo->first_name . ' ' . $app->user->basicInfo->last_name) : $app->user->email }}
                                            </strong><br>
                                            <span style="color: var(--color-text-muted); font-family: 'JetBrains Mono', monospace; font-size: 0.78rem;">
                                                CNIC: {{ $app->user->basicInfo?->cnic_number ?? 'Pending' }}
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
                                    <strong style="color: var(--warning); font-size: 0.9rem;">PKR {{ number_format($app->billingLedger?->amount, 2) }}</strong>
                                </td>
                                <td>
                                    <a href="/admin/verification/{{ $app->id }}" class="btn btn-primary btn-sm">
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

    <!-- Selection Board Evaluation Section -->
    @if(!empty($pendingEvaluations))
        <div class="card mb-6" style="border-color: var(--border-color);">
            <div class="flex justify-between align-center mb-4">
                <h3 class="card-title" style="font-size: 1.05rem; color: var(--color-text-primary);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                    Selection Board & VC Evaluation Desk
                </h3>
                <span class="badge badge-eligible">{{ count($pendingEvaluations) }} Verified Candidates</span>
            </div>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Candidate Account</th>
                            <th>Campaign Position</th>
                            <th>CNIC Reference</th>
                            <th>Verification Gate</th>
                            <th>Grading Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingEvaluations as $app)
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div class="user-avatar-circle" style="width: 34px; height: 34px; font-size: 0.75rem; background: linear-gradient(135deg, var(--success), #059669);">
                                            {{ strtoupper(substr($app->user->basicInfo?->first_name ?: $app->user->email, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong style="color: var(--color-text-primary); font-size: 0.92rem;">
                                                {{ $app->user->basicInfo ? trim($app->user->basicInfo->first_name . ' ' . $app->user->basicInfo->last_name) : $app->user->email }}
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
                                        {{ $app->user->basicInfo?->cnic_number }}
                                    </code>
                                </td>
                                <td>
                                    <span class="badge badge-paid">
                                        <span class="badge-dot" style="background: var(--success);"></span>
                                        CLEARED (ELIGIBLE)
                                    </span>
                                </td>
                                <td>
                                    <a href="/board/evaluate/{{ $app->id }}" class="btn btn-success btn-sm">
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
