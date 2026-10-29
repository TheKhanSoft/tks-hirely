<div>
    <!-- Executive Scrutiny Control Header -->
    <div class="page-hero-header">
        <div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div class="brand-icon-box" style="background: linear-gradient(135deg, rgba(99,102,241,0.25), rgba(79,70,229,0.4)); box-shadow: 0 4px 14px rgba(99,102,241,0.25);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" style="color: #ffffff;">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.3-4.3"/>
                        <path d="M11 8v6"/>
                        <path d="M8 11h6"/>
                    </svg>
                </div>
                <div>
                    <h1>Document Scrutiny Committee Desk</h1>
                    <p>Verify candidate qualifications, degrees, publications, medals, and statutory eligibility criteria.</p>
                </div>
            </div>
        </div>

        <div class="flex align-center gap-3">
            <div style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); padding: 0.5rem 1rem; border-radius: 9999px;" class="flex align-center gap-2">
                <span class="badge-dot" style="background: var(--primary); box-shadow: 0 0 8px var(--primary);"></span>
                <span style="font-size: 0.8rem; font-weight: 700; color: var(--color-text-primary); text-transform: uppercase; letter-spacing: 0.05em;">
                    Scrutiny Committee Panel
                </span>
            </div>
        </div>
    </div>

    <!-- Scrutiny KPI Status Cards -->
    <div class="stat-kpi-grid">
        <!-- Awaiting Verification -->
        <div class="metric-card cursor-pointer" 
             wire:click="$set('statusFilter', 'PAID')" 
             style="--card-accent: var(--secondary); --metric-icon-bg: var(--secondary-light); --metric-icon-color: var(--secondary); {{ $statusFilter === 'PAID' ? 'border: 1.5px solid var(--secondary); box-shadow: var(--shadow-glow);' : '' }}">
            <div class="metric-header">
                <span class="metric-label">Awaiting Verification</span>
                <div class="metric-icon-wrap">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
            </div>
            <div class="metric-value" style="color: var(--secondary);">{{ $stats['pending'] }}</div>
            <div class="metric-subtext">Paid dossier waiting for review</div>
        </div>

        <!-- Cleared Eligible -->
        <div class="metric-card cursor-pointer" 
             wire:click="$set('statusFilter', 'ELIGIBLE')" 
             style="--card-accent: var(--success); --metric-icon-bg: var(--success-light); --metric-icon-color: var(--success); {{ $statusFilter === 'ELIGIBLE' ? 'border: 1.5px solid var(--success);' : '' }}">
            <div class="metric-header">
                <span class="metric-label">Cleared Eligible</span>
                <div class="metric-icon-wrap">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
            </div>
            <div class="metric-value" style="color: var(--success);">{{ $stats['eligible'] }}</div>
            <div class="metric-subtext">Approved for interview / tests</div>
        </div>

        <!-- Disqualified -->
        <div class="metric-card cursor-pointer" 
             wire:click="$set('statusFilter', 'INELIGIBLE')" 
             style="--card-accent: var(--danger); --metric-icon-bg: var(--danger-light); --metric-icon-color: var(--danger); {{ $statusFilter === 'INELIGIBLE' ? 'border: 1.5px solid var(--danger);' : '' }}">
            <div class="metric-header">
                <span class="metric-label">Disqualified</span>
                <div class="metric-icon-wrap">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </div>
            </div>
            <div class="metric-value" style="color: var(--danger);">{{ $stats['ineligible'] }}</div>
            <div class="metric-subtext">Statutory criteria unmet</div>
        </div>

        <!-- Appealed Grievances -->
        <div class="metric-card cursor-pointer" 
             wire:click="$set('statusFilter', 'APPEALED')" 
             style="--card-accent: var(--warning); --metric-icon-bg: var(--warning-light); --metric-icon-color: var(--warning); {{ $statusFilter === 'APPEALED' ? 'border: 1.5px solid var(--warning);' : '' }}">
            <div class="metric-header">
                <span class="metric-label">Under Appeal</span>
                <div class="metric-icon-wrap">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                        <path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                        <path d="M7 21h10"/>
                        <path d="M12 3v18"/>
                    </svg>
                </div>
            </div>
            <div class="metric-value" style="color: var(--warning);">{{ $stats['appealed'] }}</div>
            <div class="metric-subtext">Referred to appellate board</div>
        </div>
    </div>

    <!-- Smart Filter & Search Bar -->
    <div class="card mb-6" style="padding: 1.15rem 1.35rem;">
        <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
            <!-- Search -->
            <div style="position: relative; flex: 2; min-width: 280px;">
                <input type="text" 
                       class="form-control" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Search candidate name, CNIC, tracking ID, or email..." 
                       style="padding-left: 2.5rem; background: var(--bg-card); color: var(--color-text-primary);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-text-muted)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); pointer-events: none;">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
            </div>

            <!-- Campaign Filter -->
            <div style="flex: 1; min-width: 200px;">
                <select class="form-control form-select" wire:model.live="campaignFilter" style="background: var(--bg-card); color: var(--color-text-primary);">
                    <option value="">All Campaigns</option>
                    @foreach($campaigns as $c)
                        <option value="{{ $c->id }}">{{ $c->title }} ({{ $c->job_code }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Track Filter -->
            <div style="width: 170px;">
                <select class="form-control form-select" wire:model.live="trackFilter" style="background: var(--bg-card); color: var(--color-text-primary);">
                    <option value="">All Tracks</option>
                    <option value="ACADEMIC">Academic Faculty</option>
                    <option value="ADMINISTRATIVE">Administrative</option>
                    <option value="SUPPORT">Support Staff</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div style="width: 160px;">
                <select class="form-control form-select" wire:model.live="statusFilter" style="background: var(--bg-card); color: var(--color-text-primary);">
                    <option value="">All Statuses</option>
                    <option value="PAID">Awaiting Scrutiny</option>
                    <option value="ELIGIBLE">Eligible</option>
                    <option value="INELIGIBLE">Ineligible</option>
                    <option value="APPEALED">Appealed</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Scrutiny Dossier Verification Table -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Candidate Dossier</th>
                        <th>Position Applied</th>
                        <th>Cadre Track</th>
                        <th>Submission & Fee</th>
                        <th>Current State</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                        <tr wire:key="scrutiny-app-{{ $app->id }}">
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.85rem;">
                                    <div class="user-avatar-circle" style="width: 38px; height: 38px; font-size: 0.85rem; font-weight: 800; background: linear-gradient(135deg, rgba(99,102,241,0.25), rgba(6,182,212,0.25)); color: var(--primary); border: 1px solid rgba(99,102,241,0.3);">
                                        {{ strtoupper(substr($app->user->basicInfo?->first_name ?: $app->user->email, 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong style="color: var(--color-text-primary); font-size: 0.95rem;">
                                            {{ $app->user->basicInfo ? trim($app->user->basicInfo->first_name . ' ' . $app->user->basicInfo->last_name) : $app->user->email }}
                                        </strong>
                                        <div style="font-size: 0.76rem; color: var(--color-text-muted); font-family: 'JetBrains Mono', monospace; margin-top: 1px;">
                                            CNIC: {{ $app->user->basicInfo?->cnic_number ?? 'N/A' }} • Ref: #{{ $app->tracking_number }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <strong style="color: var(--color-text-primary); font-size: 0.9rem;">
                                    {{ $app->campaign->title }}
                                </strong>
                                <span style="font-size: 0.78rem; color: var(--secondary); display: block;">
                                    {{ $app->campaign->department }}
                                </span>
                            </td>

                            <td>
                                @if($app->campaign->track_type === 'ACADEMIC')
                                    <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary); font-weight: 700; font-size: 0.75rem;">
                                        🎓 Academic
                                    </span>
                                @elseif($app->campaign->track_type === 'ADMINISTRATIVE')
                                    <span class="badge" style="background: rgba(6,182,212,0.12); color: var(--secondary); font-weight: 700; font-size: 0.75rem;">
                                        🏛️ Administrative
                                    </span>
                                @else
                                    <span class="badge" style="background: rgba(148,163,184,0.12); color: var(--color-text-secondary); font-weight: 700; font-size: 0.75rem;">
                                        💼 Support
                                    </span>
                                @endif
                            </td>

                            <td>
                                <div style="font-size: 0.8rem; color: var(--color-text-secondary);">
                                    {{ $app->created_at->format('d M Y') }}
                                </div>
                                <span class="badge badge-paid" style="font-size: 0.7rem; margin-top: 2px;">
                                    ✓ Fee Paid
                                </span>
                            </td>

                            <td>
                                @if($app->status === 'PAID')
                                    <span class="badge badge-appealed" style="font-size: 0.75rem; font-weight: 700;">
                                        ⏳ Ready for Verification
                                    </span>
                                @elseif($app->status === 'ELIGIBLE')
                                    <span class="badge badge-paid" style="font-size: 0.75rem; font-weight: 700;">
                                        ✓ Verified Eligible
                                    </span>
                                @elseif($app->status === 'INELIGIBLE')
                                    <span class="badge badge-disqualified" style="font-size: 0.75rem; font-weight: 700;">
                                        ✕ Disqualified
                                    </span>
                                @elseif($app->status === 'APPEALED')
                                    <span class="badge badge-appealed" style="font-size: 0.75rem; font-weight: 700;">
                                        ⚖️ Appealed
                                    </span>
                                @else
                                    <span class="badge" style="font-size: 0.75rem;">{{ $app->status }}</span>
                                @endif
                            </td>

                            <td style="text-align: right;">
                                <a href="/scrutiny/verification/{{ $app->id }}" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.4rem 0.85rem; font-size: 0.82rem;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                        <line x1="16" y1="13" x2="8" y2="13"/>
                                        <line x1="16" y1="17" x2="8" y2="17"/>
                                    </svg>
                                    <span>{{ $app->status === 'PAID' ? 'Verify Dossier' : 'Inspect Dossier' }}</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center" style="padding: 3rem; color: var(--color-text-muted);">
                                <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(99,102,241,0.1); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"/>
                                    </svg>
                                </div>
                                <h4 style="color: var(--color-text-primary); font-size: 1rem; margin: 0 0 0.35rem 0;">No Dossiers in Queue</h4>
                                <p style="font-size: 0.82rem; margin: 0;">All candidacies matching selected criteria have been scrutinized.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($applications->hasPages())
            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-color);">
                {{ $applications->links() }}
            </div>
        @endif
    </div>
</div>
