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
                    <p>Verify candidate qualifications, degrees, publications, medals, and statutory quota eligibility.</p>
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

    <!-- Smart Filter & Search Bar with View Mode Switcher -->
    <div class="card mb-6" style="padding: 1.15rem 1.35rem;">
        <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
            <!-- Search -->
            <div style="position: relative; flex: 2; min-width: 260px;">
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
            <div style="flex: 1; min-width: 180px;">
                <select class="form-control form-select" wire:model.live="campaignFilter" style="background: var(--bg-card); color: var(--color-text-primary);">
                    <option value="">All Campaigns</option>
                    @foreach($campaigns as $c)
                        <option value="{{ $c->id }}">{{ $c->title }} ({{ $c->job_code }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Track Filter -->
            <div style="width: 160px;">
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

            <!-- View Mode Switcher -->
            <div class="view-mode-toggle" style="margin-left: auto;">
                <button type="button" 
                        class="view-mode-btn {{ $viewMode === 'grid' ? 'active' : '' }}" 
                        wire:click="setViewMode('grid')" 
                        title="Grid View">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <rect width="7" height="7" x="3" y="3" rx="1.5"/>
                        <rect width="7" height="7" x="14" y="3" rx="1.5"/>
                        <rect width="7" height="7" x="14" y="14" rx="1.5"/>
                        <rect width="7" height="7" x="3" y="14" rx="1.5"/>
                    </svg>
                </button>
                <button type="button" 
                        class="view-mode-btn {{ $viewMode === 'table' ? 'active' : '' }}" 
                        wire:click="setViewMode('table')" 
                        title="Table View">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <line x1="3" x2="21" y1="6" y2="6"/>
                        <line x1="3" x2="21" y1="12" y2="12"/>
                        <line x1="3" x2="21" y1="18" y2="18"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Active Applications Display (Grid vs Table) -->
    @if($applications->count() > 0)
        @if($viewMode === 'grid')
            <!-- Responsive Card Grid: 3 per row -->
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
                @foreach($applications as $app)
                    @php
                        $bi = $app->user->basicInfo;
                        $candidateName = $bi ? trim($bi->first_name . ' ' . $bi->last_name) : $app->user->email;
                        $avatar = $bi?->profile_picture_url;
                        $statusBadgeClass = match($app->status) {
                            'ELIGIBLE' => 'badge-paid',
                            'INELIGIBLE' => 'badge-disqualified',
                            'APPEALED' => 'badge-appealed',
                            default => 'badge-pending',
                        };
                    @endphp
                    <div class="card card-ambient-mesh" style="padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between; border-color: var(--border-color); position: relative; transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);">
                        <div>
                            <!-- Header with Avatar, Name & Status -->
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.75rem; margin-bottom: 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    @if($avatar)
                                        <div class="user-avatar-circle" style="width: 46px; height: 46px; border-radius: 12px; overflow: hidden; padding: 0; flex-shrink: 0; border: 2px solid var(--primary); box-shadow: var(--shadow-sm);">
                                            <img src="{{ $avatar }}" alt="{{ $candidateName }}" onerror="this.style.display='none';" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                    @else
                                        <div class="user-avatar-circle" style="width: 46px; height: 46px; font-size: 1rem; font-weight: 800; border-radius: 12px; background: linear-gradient(135deg, var(--primary), var(--secondary)); color: #fff;">
                                            {{ strtoupper(substr($candidateName, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--color-text-primary); margin: 0; line-height: 1.25;">
                                            {{ $candidateName }}
                                        </h3>
                                        <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.76rem; color: var(--color-text-muted); display: block; margin-top: 2px;">
                                            {{ $bi?->cnic_number ?? 'Pending CNIC' }}
                                        </span>
                                    </div>
                                </div>
                                
                                <span class="badge {{ $statusBadgeClass }}" style="font-size: 0.72rem; font-weight: 700; white-space: nowrap;">
                                    {{ $app->status === 'PAID' ? 'Awaiting Scrutiny' : $app->status }}
                                </span>
                            </div>

                            <!-- Target Campaign Details -->
                            <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.75rem 0.85rem; margin-bottom: 0.85rem;">
                                <div style="font-size: 0.74rem; color: var(--color-text-muted); text-transform: uppercase; font-weight: 700;">Target Position</div>
                                <div style="font-weight: 700; font-size: 0.92rem; color: var(--color-text-primary); margin: 0.15rem 0 0.25rem;">
                                    {{ $app->campaign->title }}
                                </div>
                                <div class="flex justify-between align-center" style="font-size: 0.76rem;">
                                    <span style="color: var(--secondary);">{{ $app->campaign->department }}</span>
                                    <code style="background: rgba(255,255,255,0.04); padding: 0.1rem 0.35rem; border-radius: 4px; color: var(--color-text-secondary); font-family: 'JetBrains Mono', monospace;">
                                        {{ $app->campaign->job_code }}
                                    </code>
                                </div>
                            </div>

                            <!-- Metadata row (Quota, Track, Tracking ID) -->
                            <div class="flex justify-between align-center" style="font-size: 0.76rem; margin-bottom: 1rem;">
                                <span class="badge" style="background: rgba(99,102,241,0.1); color: var(--primary); border: 1px solid rgba(99,102,241,0.25); font-weight: 700;">
                                    {{ $bi?->quota ?? 'Open Merit' }}
                                </span>
                                <span style="color: var(--color-text-muted); font-family: 'JetBrains Mono', monospace;">
                                    Ref #{{ $app->tracking_number }}
                                </span>
                            </div>
                        </div>

                        <!-- Card Action Buttons -->
                        <div style="border-top: 1px solid var(--border-color); padding-top: 0.85rem; display: flex; gap: 0.5rem; align-items: center;">
                            <a href="/scrutiny/verification/{{ $app->id }}" class="btn btn-primary btn-sm" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 0.35rem; padding: 0.45rem 0.85rem; font-weight: 700; font-size: 0.8rem;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"/>
                                    <path d="m21 21-4.3-4.3"/>
                                </svg>
                                <span>{{ $app->status === 'PAID' ? 'Verify Dossier' : 'Inspect Dossier' }}</span>
                            </a>

                            <button type="button" 
                                    class="btn btn-secondary btn-sm" 
                                    wire:click="openCertificateModal({{ $app->id }})" 
                                    title="View & Print Official Verification Certificate"
                                    style="padding: 0.45rem 0.65rem;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 6 2 18 2 18 9"/>
                                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                                    <rect width="12" height="8" x="6" y="14"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Responsive Table View -->
            <div class="card mb-6" style="padding: 0; overflow: hidden;">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Candidate Dossier</th>
                                <th>Position Applied</th>
                                <th>Cadre Track</th>
                                <th>Statutory Quota</th>
                                <th>Current State</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($applications as $app)
                                @php
                                    $bi = $app->user->basicInfo;
                                    $candidateName = $bi ? trim($bi->first_name . ' ' . $bi->last_name) : $app->user->email;
                                    $avatar = $bi?->profile_picture_url;
                                @endphp
                                <tr wire:key="scrutiny-app-row-{{ $app->id }}">
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                                            @if($avatar)
                                                <div class="user-avatar-circle" style="width: 38px; height: 38px; border-radius: 12px; overflow: hidden; padding: 0; flex-shrink: 0; border: 1px solid var(--border-color);">
                                                    <img src="{{ $avatar }}" alt="{{ $candidateName }}" onerror="this.style.display='none';" style="width: 100%; height: 100%; object-fit: cover;">
                                                </div>
                                            @else
                                                <div class="user-avatar-circle" style="width: 38px; height: 38px; font-size: 0.85rem; font-weight: 800; background: linear-gradient(135deg, rgba(99,102,241,0.25), rgba(6,182,212,0.25)); color: var(--primary); border: 1px solid rgba(99,102,241,0.3);">
                                                    {{ strtoupper(substr($candidateName, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <strong style="color: var(--color-text-primary); font-size: 0.95rem;">
                                                    {{ $candidateName }}
                                                </strong>
                                                <div style="font-size: 0.76rem; color: var(--color-text-muted); font-family: 'JetBrains Mono', monospace; margin-top: 1px;">
                                                    CNIC: {{ $bi?->cnic_number ?? 'N/A' }} • Ref: #{{ $app->tracking_number }}
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
                                        <span class="badge" style="background: rgba(255,255,255,0.04); font-size: 0.75rem; color: var(--color-text-primary); border: 1px solid var(--border-color);">
                                            {{ $bi?->quota ?? 'Open Merit' }}
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
                                        <div style="display: inline-flex; align-items: center; gap: 0.4rem;">
                                            <a href="/scrutiny/verification/{{ $app->id }}" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.4rem 0.85rem; font-size: 0.82rem;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                                    <polyline points="14 2 14 8 20 8"/>
                                                </svg>
                                                <span>{{ $app->status === 'PAID' ? 'Verify' : 'Inspect' }}</span>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-secondary btn-sm" 
                                                    wire:click="openCertificateModal({{ $app->id }})" 
                                                    title="View Verification Certificate"
                                                    style="padding: 0.4rem 0.6rem;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="6 9 6 2 18 2 18 9"/>
                                                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                                                    <rect width="12" height="8" x="6" y="14"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Clean Pagination Footer -->
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-top: 1.25rem; padding: 0.5rem 0.25rem;">
            <div style="font-size: 0.82rem; color: var(--color-text-muted); font-weight: 700;">
                Showing <strong style="color: var(--primary);">{{ $applications->firstItem() ?? 0 }}</strong> to <strong style="color: var(--primary);">{{ $applications->lastItem() ?? 0 }}</strong> of <strong style="color: var(--color-text-primary);">{{ $applications->total() }}</strong> candidacies
            </div>

            @if($applications->hasPages())
                <div>
                    {{ $applications->links('components.pagination') }}
                </div>
            @endif
        </div>
    @else
        <div class="card text-center" style="padding: 4rem 1.5rem; margin-bottom: 1.5rem;">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: rgba(99,102,241,0.1); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
            </div>
            <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--color-text-primary); margin: 0 0 0.4rem;">
                No Applications Found
            </h3>
            <p style="font-size: 0.85rem; color: var(--color-text-secondary); max-width: 420px; margin: 0 auto 1.25rem;">
                No candidates in the scrutiny queue match your current search query or filter criteria.
            </p>
            <button type="button" class="btn btn-secondary btn-sm" wire:click="$set('search', ''); $set('campaignFilter', ''); $set('trackFilter', ''); $set('statusFilter', '');">
                Reset Filters
            </button>
        </div>
    @endif

    <!-- Printable Official Scrutiny Verification Certificate Sheet Modal -->
    <x-modal :show="$isCertificateModalOpen" title="Official Scrutiny Committee Verification Certificate" closeAction="closeCertificateModal" style="max-width: 880px; width: 95%;">
        @if($selectedAppForCertificate)
            @php
                $certBi = $selectedAppForCertificate->user->basicInfo;
                $certName = $certBi ? trim($certBi->first_name . ' ' . $certBi->last_name) : $selectedAppForCertificate->user->email;
                $degrees = $certBi?->academicDegrees ?? collect();
                $exps = $certBi?->experiences ?? collect();
            @endphp
            <div style="max-height: 75vh; overflow-y: auto; padding-right: 0.5rem;" id="printable-scrutiny-certificate">
                <!-- Formal Document Paper Container -->
                <div style="background: #ffffff; color: #1e293b; padding: 2.5rem; border-radius: 8px; border: 2px solid #cbd5e1; box-shadow: 0 4px 20px rgba(0,0,0,0.1); font-family: 'Times New Roman', Times, serif;">
                    
                    <!-- Official Header -->
                    <div style="text-align: center; border-bottom: 2px solid #0f172a; padding-bottom: 1.25rem; margin-bottom: 1.5rem;">
                        <div style="font-size: 0.9rem; font-weight: 700; letter-spacing: 0.15em; text-transform: uppercase; color: #475569;">
                            GOVERNMENT OF PAKISTAN • HIGHER EDUCATION RECRUITMENT COMMISSION
                        </div>
                        <h2 style="font-size: 1.6rem; font-weight: 800; color: #0f172a; margin: 0.4rem 0 0.2rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            STATUTORY SCRUTINY COMMITTEE VERIFICATION CERTIFICATE
                        </h2>
                        <div style="font-size: 0.85rem; color: #64748b; font-style: italic;">
                            Official Form Schedule-IV (Prescribed under University Statutory Recruitment Code)
                        </div>
                    </div>

                    <!-- Meta / Tracking Bar -->
                    <div style="display: flex; justify-content: space-between; font-size: 0.88rem; margin-bottom: 1.5rem; border-bottom: 1px dashed #cbd5e1; padding-bottom: 0.75rem;">
                        <div>
                            <strong>Verification Certificate #:</strong> 
                            <span style="font-family: monospace; font-weight: 700;">SCR-{{ str_pad($selectedAppForCertificate->id, 5, '0', STR_PAD_LEFT) }}-{{ date('Y') }}</span>
                        </div>
                        <div>
                            <strong>Tracking Reference:</strong> 
                            <span style="font-family: monospace; font-weight: 700;">#{{ $selectedAppForCertificate->tracking_number }}</span>
                        </div>
                        <div>
                            <strong>Date of Scrutiny:</strong> 
                            <span>{{ now()->format('d F, Y') }}</span>
                        </div>
                    </div>

                    <!-- Candidate & Post Details Table -->
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; font-size: 0.92rem;">
                        <tbody>
                            <tr>
                                <td style="padding: 0.5rem; border: 1px solid #cbd5e1; background: #f8fafc; font-weight: 700; width: 28%;">Candidate Full Name:</td>
                                <td style="padding: 0.5rem; border: 1px solid #cbd5e1; font-weight: 800; width: 32%;">{{ $certName }}</td>
                                <td style="padding: 0.5rem; border: 1px solid #cbd5e1; background: #f8fafc; font-weight: 700; width: 20%;">National ID (CNIC):</td>
                                <td style="padding: 0.5rem; border: 1px solid #cbd5e1; font-family: monospace; font-weight: 700; width: 20%;">{{ $certBi?->cnic_number ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 0.5rem; border: 1px solid #cbd5e1; background: #f8fafc; font-weight: 700;">Father's Name:</td>
                                <td style="padding: 0.5rem; border: 1px solid #cbd5e1;">{{ $certBi?->father_name ?? 'N/A' }}</td>
                                <td style="padding: 0.5rem; border: 1px solid #cbd5e1; background: #f8fafc; font-weight: 700;">Domicile Quota:</td>
                                <td style="padding: 0.5rem; border: 1px solid #cbd5e1; font-weight: 700; color: #047857;">{{ $certBi?->quota ?? 'Open Merit' }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 0.5rem; border: 1px solid #cbd5e1; background: #f8fafc; font-weight: 700;">Applied Position / Post:</td>
                                <td style="padding: 0.5rem; border: 1px solid #cbd5e1; font-weight: 700;">{{ $selectedAppForCertificate->campaign->title }}</td>
                                <td style="padding: 0.5rem; border: 1px solid #cbd5e1; background: #f8fafc; font-weight: 700;">Job Code:</td>
                                <td style="padding: 0.5rem; border: 1px solid #cbd5e1; font-family: monospace;">{{ $selectedAppForCertificate->campaign->job_code }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 0.5rem; border: 1px solid #cbd5e1; background: #f8fafc; font-weight: 700;">Department Scope:</td>
                                <td style="padding: 0.5rem; border: 1px solid #cbd5e1;">{{ $selectedAppForCertificate->campaign->department }}</td>
                                <td style="padding: 0.5rem; border: 1px solid #cbd5e1; background: #f8fafc; font-weight: 700;">Cadre Track:</td>
                                <td style="padding: 0.5rem; border: 1px solid #cbd5e1;">{{ $selectedAppForCertificate->campaign->track_type }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Verified Academic Qualifications Checklist -->
                    <div style="margin-bottom: 1.5rem;">
                        <h4 style="font-size: 0.95rem; font-weight: 800; color: #0f172a; text-transform: uppercase; margin-bottom: 0.5rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.25rem;">
                            1. Verified Educational Credentials & Degree Equivalency
                        </h4>
                        @if($degrees->isNotEmpty())
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.88rem;">
                                <thead>
                                    <tr style="background: #f1f5f9;">
                                        <th style="padding: 0.4rem; border: 1px solid #cbd5e1; text-align: left;">Degree Level</th>
                                        <th style="padding: 0.4rem; border: 1px solid #cbd5e1; text-align: left;">Degree Title & Major</th>
                                        <th style="padding: 0.4rem; border: 1px solid #cbd5e1; text-align: left;">Institution / Board</th>
                                        <th style="padding: 0.4rem; border: 1px solid #cbd5e1; text-align: center;">Year</th>
                                        <th style="padding: 0.4rem; border: 1px solid #cbd5e1; text-align: center;">Scrutiny Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($degrees as $deg)
                                        <tr>
                                            <td style="padding: 0.4rem; border: 1px solid #cbd5e1; font-weight: 700;">{{ $deg->degree_level }}</td>
                                            <td style="padding: 0.4rem; border: 1px solid #cbd5e1;">{{ $deg->degree_title }} ({{ $deg->major }})</td>
                                            <td style="padding: 0.4rem; border: 1px solid #cbd5e1;">{{ $deg->institution }}</td>
                                            <td style="padding: 0.4rem; border: 1px solid #cbd5e1; text-align: center;">{{ $deg->year }}</td>
                                            <td style="padding: 0.4rem; border: 1px solid #cbd5e1; text-align: center; color: #047857; font-weight: 700;">✓ VERIFIED</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p style="font-size: 0.85rem; color: #64748b; font-style: italic;">No degrees logged in digital dossier.</p>
                        @endif
                    </div>

                    <!-- Scrutiny Committee Determination -->
                    <div style="background: #f8fafc; border: 2px solid {{ $selectedAppForCertificate->status === 'INELIGIBLE' ? '#ef4444' : '#10b981' }}; border-radius: 6px; padding: 1rem 1.25rem; margin-bottom: 2rem;">
                        <div style="font-size: 0.85rem; font-weight: 800; text-transform: uppercase; color: {{ $selectedAppForCertificate->status === 'INELIGIBLE' ? '#b91c1c' : '#047857' }};">
                            SCRUTINY COMMITTEE STATUTORY DETERMINATION:
                        </div>
                        <div style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0.25rem 0;">
                            @if($selectedAppForCertificate->status === 'INELIGIBLE')
                                ✕ DISQUALIFIED / INELIGIBLE (STATUTORY DEFICIENCY)
                            @elseif($selectedAppForCertificate->status === 'PAID')
                                ⏳ PENDING SCRUTINY COMMITTEE BOARD VERIFICATION
                            @else
                                ✓ CLEARED AND DECLARED ELIGIBLE FOR SELECTION PROCESS
                            @endif
                        </div>
                        @if($selectedAppForCertificate->disqualification_reason)
                            <div style="font-size: 0.88rem; color: #b91c1c; margin-top: 0.5rem; font-style: italic;">
                                Deficiency Reason: "{{ $selectedAppForCertificate->disqualification_reason }}"
                            </div>
                        @endif
                    </div>

                    <!-- Signatures Block -->
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; text-align: center; margin-top: 3rem; padding-top: 1rem; border-top: 1px solid #cbd5e1;">
                        <div>
                            <div style="border-bottom: 1px solid #475569; width: 80%; margin: 0 auto 0.5rem;"></div>
                            <strong style="font-size: 0.85rem; color: #0f172a;">Member, Scrutiny Committee</strong>
                            <div style="font-size: 0.75rem; color: #64748b;">(Subject Expert / Dy. Registrar)</div>
                        </div>
                        <div>
                            <div style="border-bottom: 1px solid #475569; width: 80%; margin: 0 auto 0.5rem;"></div>
                            <strong style="font-size: 0.85rem; color: #0f172a;">Convener, Scrutiny Committee</strong>
                            <div style="font-size: 0.75rem; color: #64748b;">(Dean / Chairperson)</div>
                        </div>
                        <div>
                            <div style="border-bottom: 1px solid #475569; width: 80%; margin: 0 auto 0.5rem;"></div>
                            <strong style="font-size: 0.85rem; color: #0f172a;">Registrar / Authorized Officer</strong>
                            <div style="font-size: 0.75rem; color: #64748b;">Statutory Recruitment Secretariat</div>
                        </div>
                    </div>

                </div>

                <!-- Print & Dismiss Actions -->
                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.25rem;">
                    <button type="button" class="btn btn-secondary" wire:click="closeCertificateModal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" onclick="window.print()" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.6rem 1.25rem; font-weight: 700;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                            <polyline points="6 9 6 2 18 2 18 9"/>
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                            <rect width="12" height="8" x="6" y="14"/>
                        </svg>
                        <span>Print Verification Certificate</span>
                    </button>
                </div>
            </div>
        @endif
    </x-modal>
</div>
