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

    <!-- Filter & Search Bar -->
    <div class="card mb-6" style="padding: 1.15rem 1.35rem;">
        <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
            <div style="position: relative; flex: 2; min-width: 280px;">
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

            <div style="flex: 1; min-width: 220px;">
                <select class="form-control form-select" wire:model.live="campaignFilter" style="background: var(--bg-card); color: var(--color-text-primary);">
                    <option value="">All Campaigns</option>
                    @foreach($campaigns as $c)
                        <option value="{{ $c->id }}">{{ $c->title }} ({{ $c->job_code }})</option>
                    @endforeach
                </select>
            </div>

            <div style="width: 180px;">
                <select class="form-control form-select" wire:model.live="trackFilter" style="background: var(--bg-card); color: var(--color-text-primary);">
                    <option value="">All Tracks</option>
                    <option value="ACADEMIC">Academic Faculty</option>
                    <option value="ADMINISTRATIVE">Administrative</option>
                    <option value="SUPPORT">Support Staff</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Candidates Table -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Candidate Dossier</th>
                        <th>Position Applied</th>
                        <th>Cadre Track</th>
                        <th>Current Status</th>
                        <th>Board Panel Feedback</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($candidates as $cand)
                        @php
                            $myEval = $cand->evaluations->firstWhere('evaluator_id', auth()->id());
                        @endphp
                        <tr wire:key="board-cand-{{ $cand->id }}">
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.85rem;">
                                    @if($cand->user->basicInfo?->profile_picture_url)
                                        <div class="user-avatar-circle" style="width: 38px; height: 38px; border-radius: 12px; overflow: hidden; padding: 0; flex-shrink: 0; border: 1px solid var(--border-color);">
                                            <img src="{{ $cand->user->basicInfo->profile_picture_url }}" alt="Candidate" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                    @else
                                        <div class="user-avatar-circle" style="width: 38px; height: 38px; font-size: 0.85rem; font-weight: 800; background: linear-gradient(135deg, rgba(6,182,212,0.25), rgba(99,102,241,0.25)); color: var(--secondary); border: 1px solid rgba(6,182,212,0.3);">
                                            {{ strtoupper(substr($cand->user->basicInfo?->first_name ?: $cand->user->email, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <strong style="color: var(--color-text-primary); font-size: 0.95rem;">
                                            {{ $cand->user->basicInfo ? trim($cand->user->basicInfo->first_name . ' ' . $cand->user->basicInfo->last_name) : $cand->user->email }}
                                        </strong>
                                        <div style="font-size: 0.76rem; color: var(--color-text-muted); font-family: 'JetBrains Mono', monospace; margin-top: 1px;">
                                            CNIC: {{ $cand->user->basicInfo?->cnic_number ?? 'N/A' }} • Ref: #{{ $cand->tracking_number }}
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
                                <span class="badge badge-paid" style="font-size: 0.75rem; font-weight: 700;">
                                    {{ $cand->status }}
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
                                <div style="font-size: 0.72rem; color: var(--color-text-muted); margin-top: 2px;">
                                    {{ $cand->evaluations->count() }} member(s) scored
                                </div>
                            </td>

                            <td style="text-align: right;">
                                <a href="/board/evaluate/{{ $cand->id }}" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.4rem 0.85rem; font-size: 0.82rem;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                    </svg>
                                    <span>{{ $myEval ? 'Update Evaluation' : 'Score Candidate' }}</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center" style="padding: 3rem; color: var(--color-text-muted);">
                                <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(6,182,212,0.1); color: var(--secondary); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"/>
                                    </svg>
                                </div>
                                <h4 style="color: var(--color-text-primary); font-size: 1rem; margin: 0 0 0.35rem 0;">No Candidates Ready for Evaluation</h4>
                                <p style="font-size: 0.82rem; margin: 0;">Once candidates are cleared through scrutiny, they will appear here for Selection Board grading.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($candidates->hasPages())
            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-color);">
                {{ $candidates->links() }}
            </div>
        @endif
    </div>
</div>
