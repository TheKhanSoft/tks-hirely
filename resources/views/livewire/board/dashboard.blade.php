<div>
    <!-- Executive Selection Board Header -->
    <div class="page-hero-header">
        <div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div class="brand-icon-box" style="background: linear-gradient(135deg, rgba(6,182,212,0.25), rgba(14,165,233,0.4)); box-shadow: 0 4px 14px rgba(6,182,212,0.25);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" style="color: #ffffff;">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                </div>
                <div>
                    <h1>Selection Board Evaluation Desk</h1>
                    <p>Conduct real-time statutory interview grading, view objective merit scores, and record panel recommendations.</p>
                </div>
            </div>
        </div>

        <div class="flex align-center gap-3">
            <button type="button" 
                    class="btn btn-secondary" 
                    wire:click="openRosterModal"
                    style="display: inline-flex; align-items: center; gap: 0.45rem; padding: 0.6rem 1rem; font-weight: 700; font-size: 0.84rem;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"/>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                    <rect width="12" height="8" x="6" y="14"/>
                </svg>
                <span>Print Consolidated Merit Roster</span>
            </button>

            <div style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); padding: 0.5rem 1rem; border-radius: 9999px;" class="flex align-center gap-2">
                <span class="badge-dot" style="background: var(--secondary); box-shadow: 0 0 8px var(--secondary);"></span>
                <span style="font-size: 0.8rem; font-weight: 700; color: var(--color-text-primary); text-transform: uppercase; letter-spacing: 0.05em;">
                    Selection Board Member
                </span>
            </div>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="stat-kpi-grid">
        <div class="metric-card" style="--card-accent: var(--secondary); --metric-icon-bg: var(--secondary-light); --metric-icon-color: var(--secondary);">
            <div class="metric-header">
                <span class="metric-label">Candidates in Board Queue</span>
                <div class="metric-icon-wrap">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
            </div>
            <div class="metric-value" style="color: var(--secondary);">{{ $stats['total_eligible'] }}</div>
            <div class="metric-subtext">Scrutiny cleared, ready for grading</div>
        </div>

        <div class="metric-card" style="--card-accent: var(--primary); --metric-icon-bg: var(--primary-light); --metric-icon-color: var(--primary);">
            <div class="metric-header">
                <span class="metric-label">Academic Faculty Positions</span>
                <div class="metric-icon-wrap">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                        <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                    </svg>
                </div>
            </div>
            <div class="metric-value" style="color: var(--primary);">{{ $stats['academic_positions'] }}</div>
            <div class="metric-subtext">Subject to external peer review</div>
        </div>

        <div class="metric-card" style="--card-accent: var(--success); --metric-icon-bg: var(--success-light); --metric-icon-color: var(--success);">
            <div class="metric-header">
                <span class="metric-label">My Completed Evaluations</span>
                <div class="metric-icon-wrap">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
            </div>
            <div class="metric-value" style="color: var(--success);">{{ $stats['my_graded'] }}</div>
            <div class="metric-subtext">Individual evaluations submitted</div>
        </div>
    </div>

    <!-- Filter & Search Bar with View Mode Toggle -->
    <div class="card mb-6" style="padding: 1.15rem 1.35rem;">
        <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
            <div style="position: relative; flex: 2; min-width: 260px;">
                <input type="text" 
                       class="form-control" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Search candidate by name, CNIC, tracking ID..." 
                       style="padding-left: 2.5rem; background: var(--bg-card); color: var(--color-text-primary);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-text-muted)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); pointer-events: none;">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
            </div>

            <div style="flex: 1; min-width: 200px;">
                <select class="form-control form-select" wire:model.live="campaignFilter" style="background: var(--bg-card); color: var(--color-text-primary);">
                    <option value="">All Campaigns</option>
                    @foreach($campaigns as $c)
                        <option value="{{ $c->id }}">{{ $c->title }} ({{ $c->job_code }})</option>
                    @endforeach
                </select>
            </div>

            <div style="width: 170px;">
                <select class="form-control form-select" wire:model.live="trackFilter" style="background: var(--bg-card); color: var(--color-text-primary);">
                    <option value="">All Tracks</option>
                    <option value="ACADEMIC">Academic Faculty</option>
                    <option value="ADMINISTRATIVE">Administrative</option>
                    <option value="SUPPORT">Support Staff</option>
                </select>
            </div>

            <!-- View Switcher -->
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

    <!-- Active Candidates Display (Grid vs Table) -->
    @if($candidates->count() > 0)
        @if($viewMode === 'grid')
            <!-- Responsive Card Grid: 3 per row -->
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
                @foreach($candidates as $cand)
                    @php
                        $bi = $cand->user->basicInfo;
                        $candidateName = $bi ? trim($bi->first_name . ' ' . $bi->last_name) : $cand->user->email;
                        $avatar = $bi?->profile_picture_url;
                        $myEval = $cand->evaluations->firstWhere('evaluator_id', auth()->id());
                    @endphp
                    <div class="card card-ambient-mesh" style="padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between; border-color: var(--border-color); position: relative; transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);">
                        <div>
                            <!-- Header with Avatar & Details -->
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.75rem; margin-bottom: 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    @if($avatar)
                                        <div class="user-avatar-circle" style="width: 46px; height: 46px; border-radius: 12px; overflow: hidden; padding: 0; flex-shrink: 0; border: 2px solid var(--secondary); box-shadow: var(--shadow-sm);">
                                            <img src="{{ $avatar }}" alt="{{ $candidateName }}" onerror="this.style.display='none';" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                    @else
                                        <div class="user-avatar-circle" style="width: 46px; height: 46px; font-size: 1rem; font-weight: 800; border-radius: 12px; background: linear-gradient(135deg, var(--secondary), #0284c7); color: #fff;">
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

                                <span class="badge" style="background: rgba(16,185,129,0.12); color: var(--success); border: 1px solid rgba(16,185,129,0.3); font-weight: 700; font-size: 0.72rem;">
                                    ✓ Verified Eligible
                                </span>
                            </div>

                            <!-- Target Campaign Details -->
                            <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.75rem 0.85rem; margin-bottom: 0.85rem;">
                                <div style="font-size: 0.74rem; color: var(--color-text-muted); text-transform: uppercase; font-weight: 700;">Target Position</div>
                                <div style="font-weight: 700; font-size: 0.92rem; color: var(--color-text-primary); margin: 0.15rem 0 0.25rem;">
                                    {{ $cand->campaign->title }}
                                </div>
                                <div class="flex justify-between align-center" style="font-size: 0.76rem;">
                                    <span style="color: var(--secondary);">{{ $cand->campaign->department }}</span>
                                    <code style="background: rgba(255,255,255,0.04); padding: 0.1rem 0.35rem; border-radius: 4px; color: var(--color-text-secondary); font-family: 'JetBrains Mono', monospace;">
                                        {{ $cand->campaign->job_code }}
                                    </code>
                                </div>
                            </div>

                            <!-- Grading Status and Quota Pill -->
                            <div class="flex justify-between align-center" style="font-size: 0.78rem; margin-bottom: 1rem;">
                                <span class="badge" style="background: rgba(99,102,241,0.1); color: var(--primary); border: 1px solid rgba(99,102,241,0.25); font-weight: 700;">
                                    {{ $bi?->quota ?? 'Open Merit' }}
                                </span>

                                @if($myEval)
                                    <span class="badge badge-paid" style="font-size: 0.74rem;">
                                        ✓ Scored: {{ number_format($myEval->total_score, 1) }}/100
                                    </span>
                                @else
                                    <span class="badge badge-appealed" style="font-size: 0.74rem;">
                                        Pending Your Score
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div style="border-top: 1px solid var(--border-color); padding-top: 0.85rem;">
                            <a href="/board/evaluate/{{ $cand->id }}" class="btn btn-primary btn-sm" style="width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem; padding: 0.55rem 1rem; font-weight: 700; font-size: 0.84rem;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                </svg>
                                <span>{{ $myEval ? 'Update Candidate Evaluation' : 'Score Candidate Live' }}</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Table View -->
            <div class="card mb-6" style="padding: 0; overflow: hidden;">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Candidate Dossier</th>
                                <th>Position Applied</th>
                                <th>Cadre Track</th>
                                <th>Statutory Quota</th>
                                <th>Board Panel Status</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($candidates as $cand)
                                @php
                                    $bi = $cand->user->basicInfo;
                                    $candidateName = $bi ? trim($bi->first_name . ' ' . $bi->last_name) : $cand->user->email;
                                    $avatar = $bi?->profile_picture_url;
                                    $myEval = $cand->evaluations->firstWhere('evaluator_id', auth()->id());
                                @endphp
                                <tr wire:key="board-cand-table-{{ $cand->id }}">
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                                            @if($avatar)
                                                <div class="user-avatar-circle" style="width: 38px; height: 38px; border-radius: 12px; overflow: hidden; padding: 0; flex-shrink: 0; border: 1px solid var(--border-color);">
                                                    <img src="{{ $avatar }}" alt="{{ $candidateName }}" onerror="this.style.display='none';" style="width: 100%; height: 100%; object-fit: cover;">
                                                </div>
                                            @else
                                                <div class="user-avatar-circle" style="width: 38px; height: 38px; font-size: 0.85rem; font-weight: 800; background: linear-gradient(135deg, rgba(6,182,212,0.25), rgba(99,102,241,0.25)); color: var(--secondary); border: 1px solid rgba(6,182,212,0.3);">
                                                    {{ strtoupper(substr($candidateName, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <strong style="color: var(--color-text-primary); font-size: 0.95rem;">
                                                    {{ $candidateName }}
                                                </strong>
                                                <div style="font-size: 0.76rem; color: var(--color-text-muted); font-family: 'JetBrains Mono', monospace; margin-top: 1px;">
                                                    CNIC: {{ $bi?->cnic_number ?? 'N/A' }} • Ref: #{{ $cand->tracking_number }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <strong style="color: var(--color-text-primary); font-size: 0.9rem;">
                                            {{ $cand->campaign->title }}
                                        </strong>
                                        <span style="font-size: 0.78rem; color: var(--secondary); display: block;">
                                            {{ $cand->campaign->department }}
                                        </span>
                                    </td>

                                    <td>
                                        @if($cand->campaign->track_type === 'ACADEMIC')
                                            <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary); font-weight: 700; font-size: 0.75rem;">
                                                🎓 Academic
                                            </span>
                                        @elseif($cand->campaign->track_type === 'ADMINISTRATIVE')
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
                                        @if($myEval)
                                            <span class="badge badge-paid" style="font-size: 0.75rem;">
                                                ✓ Graded: {{ number_format($myEval->total_score, 1) }}
                                            </span>
                                        @else
                                            <span class="badge badge-appealed" style="font-size: 0.75rem;">
                                                Pending Your Score
                                            </span>
                                        @endif
                                    </td>

                                    <td style="text-align: right;">
                                        <a href="/board/evaluate/{{ $cand->id }}" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.4rem 0.85rem; font-size: 0.82rem;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                            </svg>
                                            <span>{{ $myEval ? 'Update' : 'Score' }}</span>
                                        </a>
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
                Showing <strong style="color: var(--primary);">{{ $candidates->firstItem() ?? 0 }}</strong> to <strong style="color: var(--primary);">{{ $candidates->lastItem() ?? 0 }}</strong> of <strong style="color: var(--color-text-primary);">{{ $candidates->total() }}</strong> candidates
            </div>

            @if($candidates->hasPages())
                <div>
                    {{ $candidates->links('components.pagination') }}
                </div>
            @endif
        </div>
    @else
        <div class="card text-center" style="padding: 4rem 1.5rem; margin-bottom: 1.5rem;">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: rgba(6,182,212,0.1); color: var(--secondary); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--color-text-primary); margin: 0 0 0.4rem;">
                No Candidates Ready for Evaluation
            </h3>
            <p style="font-size: 0.85rem; color: var(--color-text-secondary); max-width: 420px; margin: 0 auto 1.25rem;">
                Once candidates are cleared through scrutiny, they will appear here for Selection Board grading.
            </p>
        </div>
    @endif

    <!-- Printable Official Consolidated Selection Board Merit Roster Modal -->
    <x-modal :show="$isRosterModalOpen" title="Official Consolidated Selection Board Merit Roster & Minutes" closeAction="closeRosterModal" style="max-width: 960px; width: 95%;">
        @if($rosterCampaign)
            <div style="max-height: 75vh; overflow-y: auto; padding-right: 0.5rem;" id="printable-selection-board-roster">
                <!-- Formal Document Paper Container -->
                <div style="background: #ffffff; color: #1e293b; padding: 2.5rem; border-radius: 8px; border: 2px solid #cbd5e1; box-shadow: 0 4px 20px rgba(0,0,0,0.1); font-family: 'Times New Roman', Times, serif;">
                    
                    <!-- Official Header -->
                    <div style="text-align: center; border-bottom: 2px solid #0f172a; padding-bottom: 1.25rem; margin-bottom: 1.5rem;">
                        <div style="font-size: 0.9rem; font-weight: 700; letter-spacing: 0.15em; text-transform: uppercase; color: #475569;">
                            ISLAMIC REPUBLIC OF PAKISTAN • STATUTORY SELECTION BOARD
                        </div>
                        <h2 style="font-size: 1.6rem; font-weight: 800; color: #0f172a; margin: 0.4rem 0 0.2rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            CONSOLIDATED MERIT ROSTER & PROCEEDINGS OF SELECTION BOARD
                        </h2>
                        <div style="font-size: 0.85rem; color: #64748b; font-style: italic;">
                            Held under the Chairmanship of the Vice Chancellor / Chairman Statutory Selection Board
                        </div>
                    </div>

                    <!-- Campaign Meta Particulars -->
                    <div style="display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 1.5rem; border-bottom: 1px dashed #cbd5e1; padding-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                        <div>
                            <strong>Position / Cadre:</strong> 
                            <span style="font-weight: 700; color: #0f172a;">{{ $rosterCampaign->title }}</span>
                        </div>
                        <div>
                            <strong>Department Scope:</strong> 
                            <span>{{ $rosterCampaign->department }}</span>
                        </div>
                        <div>
                            <strong>Job Code:</strong> 
                            <span style="font-family: monospace; font-weight: 700;">{{ $rosterCampaign->job_code }}</span>
                        </div>
                        <div>
                            <strong>Date of Board:</strong> 
                            <span>{{ now()->format('d F, Y') }}</span>
                        </div>
                    </div>

                    <!-- Ranked Merit Table -->
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.88rem; margin-bottom: 1.75rem;">
                        <thead>
                            <tr style="background: #f1f5f9; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.05em;">
                                <th style="padding: 0.5rem; border: 1px solid #cbd5e1; text-align: center; width: 6%;">Rank</th>
                                <th style="padding: 0.5rem; border: 1px solid #cbd5e1; text-align: left; width: 28%;">Candidate Name & Father's Name</th>
                                <th style="padding: 0.5rem; border: 1px solid #cbd5e1; text-align: left; width: 18%;">CNIC & Quota</th>
                                <th style="padding: 0.5rem; border: 1px solid #cbd5e1; text-align: center; width: 12%;">Academic (50)</th>
                                <th style="padding: 0.5rem; border: 1px solid #cbd5e1; text-align: center; width: 12%;">Interview (50)</th>
                                <th style="padding: 0.5rem; border: 1px solid #cbd5e1; text-align: center; width: 12%;">Total (100)</th>
                                <th style="padding: 0.5rem; border: 1px solid #cbd5e1; text-align: center; width: 12%;">Verdict</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rosterApplications as $idx => $row)
                                <tr style="{{ $idx === 0 ? 'background: #f0fdf4;' : '' }}">
                                    <td style="padding: 0.45rem; border: 1px solid #cbd5e1; text-align: center; font-weight: 800; font-size: 1rem; color: {{ $idx === 0 ? '#15803d' : '#0f172a' }};">
                                        #{{ $idx + 1 }}
                                    </td>
                                    <td style="padding: 0.45rem; border: 1px solid #cbd5e1;">
                                        <strong style="color: #0f172a;">{{ $row['name'] }}</strong>
                                        <div style="font-size: 0.75rem; color: #64748b;">Ref #{{ $row['app']->tracking_number }}</div>
                                    </td>
                                    <td style="padding: 0.45rem; border: 1px solid #cbd5e1;">
                                        <code style="font-family: monospace; font-size: 0.8rem;">{{ $row['cnic'] }}</code>
                                        <div style="font-size: 0.75rem; font-weight: 700; color: #047857;">{{ $row['quota'] }}</div>
                                    </td>
                                    <td style="padding: 0.45rem; border: 1px solid #cbd5e1; text-align: center; font-family: monospace; font-weight: 700;">
                                        {{ number_format($row['academic_score'], 1) }}
                                    </td>
                                    <td style="padding: 0.45rem; border: 1px solid #cbd5e1; text-align: center; font-family: monospace; font-weight: 700;">
                                        {{ number_format($row['interview_score'], 1) }}
                                    </td>
                                    <td style="padding: 0.45rem; border: 1px solid #cbd5e1; text-align: center; font-family: monospace; font-weight: 800; font-size: 0.95rem; color: {{ $idx === 0 ? '#15803d' : '#0f172a' }};">
                                        {{ number_format($row['total_score'], 2) }}
                                    </td>
                                    <td style="padding: 0.45rem; border: 1px solid #cbd5e1; text-align: center; font-weight: 800; font-size: 0.78rem;">
                                        @if($idx === 0)
                                            <span style="color: #15803d;">RECOMMENDED (1ST)</span>
                                        @elseif($idx === 1)
                                            <span style="color: #0369a1;">ALTERNATE (2ND)</span>
                                        @else
                                            <span style="color: #64748b;">NOT SELECTED</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 2rem; color: #64748b;">No eligible candidate evaluations logged for this campaign yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Statutory Minutes Statement -->
                    <div style="font-size: 0.85rem; line-height: 1.6; color: #334155; margin-bottom: 2rem; text-align: justify;">
                        <strong>Resolution of the Selection Board:</strong> The Selection Board, after interviewing the candidates and evaluating their verified credentials, research publications, and performance, hereby unanimously recommends the candidate appearing at Merit Position #1 for formal appointment to the Syndicate for statutory confirmation and issuance of appointment letter.
                    </div>

                    <!-- Board Panel Signatures Block -->
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; text-align: center; margin-top: 3rem; padding-top: 1rem; border-top: 1px solid #cbd5e1;">
                        <div>
                            <div style="border-bottom: 1px solid #475569; width: 85%; margin: 0 auto 0.5rem;"></div>
                            <strong style="font-size: 0.82rem; color: #0f172a;">Member / Expert</strong>
                            <div style="font-size: 0.72rem; color: #64748b;">External Subject Specialist</div>
                        </div>
                        <div>
                            <div style="border-bottom: 1px solid #475569; width: 85%; margin: 0 auto 0.5rem;"></div>
                            <strong style="font-size: 0.82rem; color: #0f172a;">Dean of Faculty</strong>
                            <div style="font-size: 0.72rem; color: #64748b;">Ex-Officio Member</div>
                        </div>
                        <div>
                            <div style="border-bottom: 1px solid #475569; width: 85%; margin: 0 auto 0.5rem;"></div>
                            <strong style="font-size: 0.82rem; color: #0f172a;">Registrar (Secretary)</strong>
                            <div style="font-size: 0.72rem; color: #64748b;">Statutory Selection Board</div>
                        </div>
                        <div>
                            <div style="border-bottom: 1px solid #475569; width: 85%; margin: 0 auto 0.5rem;"></div>
                            <strong style="font-size: 0.82rem; color: #0f172a;">Vice Chancellor</strong>
                            <div style="font-size: 0.72rem; color: #64748b;">Chairman Selection Board</div>
                        </div>
                    </div>

                </div>

                <!-- Print & Dismiss Actions -->
                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.25rem;">
                    <button type="button" class="btn btn-secondary" wire:click="closeRosterModal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" onclick="window.print()" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.6rem 1.25rem; font-weight: 700;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                            <polyline points="6 9 6 2 18 2 18 9"/>
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                            <rect width="12" height="8" x="6" y="14"/>
                        </svg>
                        <span>Print Consolidated Merit Roster</span>
                    </button>
                </div>
            </div>
        @endif
    </x-modal>
</div>
