<div>
    <!-- Executive Control Header -->
    <div class="page-hero-header" style="margin-bottom: 1.5rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div class="brand-icon-box" style="background: linear-gradient(135deg, rgba(99,102,241,0.25), rgba(168,85,247,0.35)); box-shadow: 0 4px 14px rgba(99,102,241,0.25);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        <polyline points="14 11 16 13 22 7"/>
                    </svg>
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                        <h1 style="margin: 0; font-size: 1.4rem; font-weight: 800; color: var(--color-text-primary);">
                            Statutory Recruitment Merit Lists & Proceedings
                        </h1>
                        <span class="badge badge-indigo" style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase;">
                            v2.4 Statutory Engine
                        </span>
                    </div>
                    <p style="margin: 0.2rem 0 0 0; font-size: 0.85rem; color: var(--color-text-secondary);">
                        Multi-stage merit rankings, scrutiny clearance rosters, expert referee scores, selection board viva marks, and gazetted appointment orders.
                    </p>
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            @if($selectedCampaign)
                <a href="{{ route('merit-list.print', ['campaignId' => $selectedCampaign->id, 'stage' => $activeStage, 'quota' => $quotaFilter, 'search' => $search]) }}" 
                   target="_blank" 
                   class="btn btn-primary" 
                   style="display: inline-flex; align-items: center; gap: 0.45rem; padding: 0.6rem 1.25rem; font-weight: 800; box-shadow: 0 4px 14px var(--primary-glow);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    <span>Print Official Gazette Order</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Multi-Stage Process / Step Navigation Bar -->
    <div style="margin-bottom: 1.5rem;">
        <div class="segmented-tabs-bar" style="width: 100%; display: flex; flex-wrap: wrap; gap: 0.35rem; padding: 0.35rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); box-shadow: var(--shadow-sm);">
            
            <button type="button" 
                    wire:click="setStage('intake')" 
                    class="segmented-tab-item {{ $activeStage === 'intake' ? 'active' : '' }}" 
                    style="flex: 1; min-width: 170px; justify-content: center; padding: 0.65rem 0.85rem;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                <span>Step 1: Intake & Fee Roster</span>
            </button>

            <button type="button" 
                    wire:click="setStage('scrutiny')" 
                    class="segmented-tab-item {{ $activeStage === 'scrutiny' ? 'active' : '' }}" 
                    style="flex: 1; min-width: 170px; justify-content: center; padding: 0.65rem 0.85rem;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m9 11 3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                <span>Step 2: Scrutiny Shortlist</span>
            </button>

            <button type="button" 
                    wire:click="setStage('peer_review')" 
                    class="segmented-tab-item {{ $activeStage === 'peer_review' ? 'active' : '' }}" 
                    style="flex: 1; min-width: 170px; justify-content: center; padding: 0.65rem 0.85rem;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
                <span>Step 3: Peer Evaluation</span>
            </button>

            <button type="button" 
                    wire:click="setStage('selection_board')" 
                    class="segmented-tab-item {{ $activeStage === 'selection_board' ? 'active' : '' }}" 
                    style="flex: 1; min-width: 170px; justify-content: center; padding: 0.65rem 0.85rem;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                <span>Step 4: Selection Board Viva</span>
            </button>

            <button type="button" 
                    wire:click="setStage('consolidated')" 
                    class="segmented-tab-item {{ $activeStage === 'consolidated' ? 'active' : '' }}" 
                    style="flex: 1; min-width: 190px; justify-content: center; padding: 0.65rem 0.85rem; border-color: var(--primary);">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
                <span>Step 5: Final Gazette Order</span>
            </button>
        </div>
    </div>

    <!-- Filter Control Card -->
    <div class="card mb-6" style="padding: 1.25rem; background: var(--bg-card); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); border-radius: var(--radius-md);">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; align-items: flex-end;">
            
            <!-- Campaign Select -->
            <div>
                <label style="display: block; font-size: 0.78rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-secondary); margin-bottom: 0.35rem;">
                    Target Recruitment Campaign
                </label>
                <select wire:model.live="campaignId" 
                        class="form-control" 
                        style="width: 100%; padding: 0.55rem 0.85rem; border-radius: 8px; font-size: 0.88rem; font-weight: 700; background: var(--bg-surface); color: var(--color-text-primary); border: 1px solid var(--border-color);">
                    @foreach($campaigns as $camp)
                        <option value="{{ $camp->id }}">
                            {{ $camp->advertisement ? "Ad #{$camp->advertisement->advertisement_no} — " : '' }}{{ $camp->title }} ({{ $camp->job_code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Search Input -->
            <div>
                <label style="display: block; font-size: 0.78rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-secondary); margin-bottom: 0.35rem;">
                    Search Candidate / CNIC / Tracking ID
                </label>
                <div style="position: relative;">
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Filter by name, CNIC or APP-XXXXX..." 
                           class="form-control" 
                           style="width: 100%; padding: 0.55rem 0.85rem; border-radius: 8px; font-size: 0.88rem; background: var(--bg-surface); color: var(--color-text-primary); border: 1px solid var(--border-color);">
                </div>
            </div>

            <!-- Quota Filter -->
            <div>
                <label style="display: block; font-size: 0.78rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-secondary); margin-bottom: 0.35rem;">
                    Statutory Quota
                </label>
                <select wire:model.live="quotaFilter" 
                        class="form-control" 
                        style="width: 100%; padding: 0.55rem 0.85rem; border-radius: 8px; font-size: 0.88rem; background: var(--bg-surface); color: var(--color-text-primary); border: 1px solid var(--border-color);">
                    <option value="ALL">All Statutory Quotas</option>
                    <option value="Open Merit">Open Merit</option>
                    <option value="Women Quota">Women Quota (15%)</option>
                    <option value="Minority Quota">Minority Quota (5%)</option>
                    <option value="Disabled Quota">Disabled Persons Quota (2%)</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label style="display: block; font-size: 0.78rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-secondary); margin-bottom: 0.35rem;">
                    Eligibility Status
                </label>
                <select wire:model.live="statusFilter" 
                        class="form-control" 
                        style="width: 100%; padding: 0.55rem 0.85rem; border-radius: 8px; font-size: 0.88rem; background: var(--bg-surface); color: var(--color-text-primary); border: 1px solid var(--border-color);">
                    <option value="ALL">All Statuses</option>
                    <option value="ELIGIBLE">Eligible / Cleared</option>
                    <option value="APPROVED">Approved / Selected</option>
                    <option value="UNDER_REVIEW">Under Review</option>
                    <option value="INCOMPLETE_DOSSIER">Deficient Dossier</option>
                    <option value="INELIGIBLE">Ineligible / Disqualified</option>
                </select>
            </div>

        </div>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="stat-kpi-grid mb-6" style="margin-bottom: 1.5rem;">
        <div class="metric-card" style="--card-accent: var(--primary);">
            <div class="metric-header">
                <span class="metric-label">Total Pool</span>
                <div class="metric-icon-wrap">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                </div>
            </div>
            <div class="metric-value">{{ $metrics['total_applications'] }}</div>
            <div class="metric-subtext">Total submissions on file</div>
        </div>

        <div class="metric-card" style="--card-accent: #10b981;">
            <div class="metric-header">
                <span class="metric-label">Eligible / Cleared</span>
                <div class="metric-icon-wrap" style="color: #10b981; background: rgba(16,185,129,0.12);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
            </div>
            <div class="metric-value" style="color: #10b981;">{{ $metrics['eligible_count'] }}</div>
            <div class="metric-subtext">Passed statutory benchmarks</div>
        </div>

        <div class="metric-card" style="--card-accent: #ef4444;">
            <div class="metric-header">
                <span class="metric-label">Ineligible / Disqualified</span>
                <div class="metric-icon-wrap" style="color: #ef4444; background: rgba(239,68,68,0.12);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                </div>
            </div>
            <div class="metric-value" style="color: #ef4444;">{{ $metrics['ineligible_count'] }}</div>
            <div class="metric-subtext">Failed statutory criteria</div>
        </div>

        <div class="metric-card" style="--card-accent: #6366f1;">
            <div class="metric-header">
                <span class="metric-label">Average Score</span>
                <div class="metric-icon-wrap" style="color: #6366f1; background: rgba(99,102,241,0.12);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
            </div>
            <div class="metric-value">{{ $metrics['average_aggregate'] }}%</div>
            <div class="metric-subtext">Stage aggregate average</div>
        </div>

        <div class="metric-card" style="--card-accent: #06b6d4;">
            <div class="metric-header">
                <span class="metric-label">Top Benchmark Score</span>
                <div class="metric-icon-wrap" style="color: #06b6d4; background: rgba(6,182,212,0.12);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
            </div>
            <div class="metric-value" style="color: #06b6d4;">{{ $metrics['top_score'] }}%</div>
            <div class="metric-subtext">Leading candidate score</div>
        </div>
    </div>

    <!-- Active Stage Banner -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
        <div style="display: flex; align-items: center; gap: 0.65rem;">
            <span style="font-size: 1.15rem; font-weight: 800; color: var(--color-text-primary);">
                {{ $stageTitle }}
            </span>
            <span class="badge badge-slate" style="font-size: 0.75rem;">
                {{ $applications->count() }} Records Found
            </span>
        </div>

        @if($selectedCampaign)
            <div style="font-size: 0.85rem; color: var(--color-text-secondary);">
                Active Campaign: <strong style="color: var(--color-text-primary);">{{ $selectedCampaign->title }}</strong> ({{ $selectedCampaign->job_code }})
            </div>
        @endif
    </div>

    <!-- Master Roster Data Table -->
    <div class="table-container" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); overflow-x: auto; box-shadow: var(--shadow-sm);">
        <table class="data-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-color); background: var(--bg-surface);">
                    @if($activeStage === 'intake')
                        <th style="padding: 0.85rem 1rem; text-align: center; width: 60px;">#</th>
                        <th style="padding: 0.85rem 1rem;">Tracking ID</th>
                        <th style="padding: 0.85rem 1rem;">Candidate Particulars</th>
                        <th style="padding: 0.85rem 1rem;">Father / Domicile</th>
                        <th style="padding: 0.85rem 1rem; text-align: center;">Fee Status</th>
                        <th style="padding: 0.85rem 1rem;">1Link Reference</th>
                        <th style="padding: 0.85rem 1rem; text-align: right;">Dossier Actions</th>
                    @elseif($activeStage === 'scrutiny')
                        <th style="padding: 0.85rem 1rem; text-align: center; width: 70px;">Rank</th>
                        <th style="padding: 0.85rem 1rem;">Tracking ID</th>
                        <th style="padding: 0.85rem 1rem;">Candidate Particulars</th>
                        <th style="padding: 0.85rem 1rem;">Terminal Qualification</th>
                        <th style="padding: 0.85rem 1rem; text-align: center;">Acad (/40)</th>
                        <th style="padding: 0.85rem 1rem; text-align: center;">Scrutiny Determination</th>
                        <th style="padding: 0.85rem 1rem;">Deficiency Grounds</th>
                        <th style="padding: 0.85rem 1rem; text-align: right;">Dossier Actions</th>
                    @elseif($activeStage === 'peer_review')
                        <th style="padding: 0.85rem 1rem; text-align: center; width: 70px;">Rank</th>
                        <th style="padding: 0.85rem 1rem;">Tracking ID</th>
                        <th style="padding: 0.85rem 1rem;">Candidate Particulars</th>
                        <th style="padding: 0.85rem 1rem; text-align: center;">Referees Assigned</th>
                        <th style="padding: 0.85rem 1rem; text-align: center;">Peer Score Avg</th>
                        <th style="padding: 0.85rem 1rem; text-align: center;">Benchmark Verdict</th>
                        <th style="padding: 0.85rem 1rem; text-align: center;">Publications</th>
                        <th style="padding: 0.85rem 1rem; text-align: right;">Dossier Actions</th>
                    @elseif($activeStage === 'selection_board')
                        <th style="padding: 0.85rem 1rem; text-align: center; width: 70px;">Rank</th>
                        <th style="padding: 0.85rem 1rem;">Tracking ID</th>
                        <th style="padding: 0.85rem 1rem;">Candidate Particulars</th>
                        <th style="padding: 0.85rem 1rem; text-align: center;">Acad (/40)</th>
                        <th style="padding: 0.85rem 1rem; text-align: center;">Research (/20)</th>
                        <th style="padding: 0.85rem 1rem; text-align: center;">Board Viva (/40)</th>
                        <th style="padding: 0.85rem 1rem; text-align: center;">Total Score</th>
                        <th style="padding: 0.85rem 1rem; text-align: center;">Board Status</th>
                        <th style="padding: 0.85rem 1rem; text-align: right;">Dossier Actions</th>
                    @else
                        {{-- Consolidated Final Gazette --}}
                        <th style="padding: 0.85rem 1rem; text-align: center; width: 75px;">Rank</th>
                        <th style="padding: 0.85rem 1rem;">Tracking ID</th>
                        <th style="padding: 0.85rem 1rem;">Candidate Particulars</th>
                        <th style="padding: 0.85rem 1rem; text-align: center;">Acad (/40)</th>
                        <th style="padding: 0.85rem 1rem; text-align: center;">Exp/Pub (/15)</th>
                        <th style="padding: 0.85rem 1rem; text-align: center;">Peer (/15)</th>
                        <th style="padding: 0.85rem 1rem; text-align: center;">Board (/30)</th>
                        <th style="padding: 0.85rem 1rem; text-align: center;">Aggregate %</th>
                        <th style="padding: 0.85rem 1rem; text-align: center;">Final Selection Order</th>
                        <th style="padding: 0.85rem 1rem; text-align: right;">Dossier Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $item)
                    @php
                        $isDisq = in_array($item['app']->status, ['INELIGIBLE', 'DISQUALIFIED']);
                        $isRank1 = ($item['rank'] === 1 && !$isDisq);
                    @endphp
                    <tr style="border-bottom: 1px solid var(--border-color); background: {{ $isRank1 ? 'rgba(16,185,129,0.06)' : ($isDisq ? 'rgba(239,68,68,0.04)' : 'transparent') }}; transition: background 0.15s;">
                        
                        @if($activeStage === 'intake')
                            <td style="padding: 0.85rem 1rem; text-align: center; font-weight: 700;">{{ $loop->iteration }}</td>
                            <td style="padding: 0.85rem 1rem; font-family: 'JetBrains Mono', monospace; font-weight: 700;">
                                {{ $item['tracking_number'] }}
                            </td>
                            <td style="padding: 0.85rem 1rem;">
                                <div style="font-weight: 800; color: var(--color-text-primary);">{{ $item['candidate_name'] }}</div>
                                <div style="font-size: 0.78rem; color: var(--color-text-secondary); font-family: 'JetBrains Mono', monospace;">CNIC: {{ $item['cnic'] }}</div>
                            </td>
                            <td style="padding: 0.85rem 1rem; font-size: 0.85rem;">
                                <div>{{ $item['father_name'] }}</div>
                                <div style="font-size: 0.78rem; color: var(--color-text-muted);">{{ $item['quota'] }} • {{ $item['domicile'] }}</div>
                            </td>
                            <td style="padding: 0.85rem 1rem; text-align: center;">
                                <span class="badge {{ $item['is_paid'] ? 'badge-emerald' : 'badge-amber' }}">
                                    {{ $item['is_paid'] ? 'PAID / SETTLED' : 'PENDING' }}
                                </span>
                            </td>
                            <td style="padding: 0.85rem 1rem; font-family: 'JetBrains Mono', monospace; font-size: 0.82rem;">
                                {{ $item['challan_id'] }}
                            </td>

                        @elseif($activeStage === 'scrutiny')
                            <td style="padding: 0.85rem 1rem; text-align: center; font-weight: 800; font-size: 1.05rem;">
                                {{ $item['rank'] }}
                            </td>
                            <td style="padding: 0.85rem 1rem; font-family: 'JetBrains Mono', monospace; font-weight: 700;">
                                {{ $item['tracking_number'] }}
                            </td>
                            <td style="padding: 0.85rem 1rem;">
                                <div style="font-weight: 800; color: var(--color-text-primary);">{{ $item['candidate_name'] }}</div>
                                <div style="font-size: 0.78rem; color: var(--color-text-secondary); font-family: 'JetBrains Mono', monospace;">CNIC: {{ $item['cnic'] }}</div>
                            </td>
                            <td style="padding: 0.85rem 1rem; font-size: 0.85rem;">
                                <div style="font-weight: 700;">{{ $item['highest_degree'] }}</div>
                                <div style="font-size: 0.78rem; color: var(--color-text-muted);">{{ $item['terminal_division'] }} ({{ $item['terminal_gpa'] }})</div>
                            </td>
                            <td style="padding: 0.85rem 1rem; text-align: center; font-family: 'JetBrains Mono', monospace; font-weight: 700;">
                                {{ number_format($item['academic_score'], 1) }}
                            </td>
                            <td style="padding: 0.85rem 1rem; text-align: center;">
                                <span class="badge {{ in_array($item['app']->status, ['ELIGIBLE', 'UNDER_REVIEW', 'APPROVED']) ? 'badge-emerald' : (in_array($item['app']->status, ['INELIGIBLE', 'DISQUALIFIED']) ? 'badge-rose' : 'badge-amber') }}">
                                    {{ $item['status_label'] }}
                                </span>
                            </td>
                            <td style="padding: 0.85rem 1rem; font-size: 0.82rem; color: {{ $isDisq ? 'var(--danger)' : 'var(--color-text-secondary)' }};">
                                {{ $item['disqualification_reason'] ?: 'All statutory minimum criteria verified and satisfied.' }}
                            </td>

                        @elseif($activeStage === 'peer_review')
                            <td style="padding: 0.85rem 1rem; text-align: center; font-weight: 800;">{{ $item['rank'] }}</td>
                            <td style="padding: 0.85rem 1rem; font-family: 'JetBrains Mono', monospace; font-weight: 700;">{{ $item['tracking_number'] }}</td>
                            <td style="padding: 0.85rem 1rem;">
                                <div style="font-weight: 800; color: var(--color-text-primary);">{{ $item['candidate_name'] }}</div>
                                <div style="font-size: 0.78rem; color: var(--color-text-secondary);">{{ $item['highest_degree'] }}</div>
                            </td>
                            <td style="padding: 0.85rem 1rem; text-align: center; font-size: 0.85rem;">
                                {{ $item['app']->reviews->where('status', 'SUBMITTED')->count() }} / {{ $item['app']->reviews->count() }} Referees
                            </td>
                            <td style="padding: 0.85rem 1rem; text-align: center; font-family: 'JetBrains Mono', monospace; font-weight: 800; color: var(--primary);">
                                {{ $item['peer_score_avg'] > 0 ? number_format($item['peer_score_avg'], 1) . '%' : '—' }}
                            </td>
                            <td style="padding: 0.85rem 1rem; text-align: center;">
                                <span class="badge {{ $item['peer_verdict_badge'] }}">
                                    {{ $item['peer_verdict'] }}
                                </span>
                            </td>
                            <td style="padding: 0.85rem 1rem; text-align: center; font-family: 'JetBrains Mono', monospace;">
                                {{ $item['app']->user?->basicInfo?->publications->count() ?? 0 }} Papers
                            </td>

                        @elseif($activeStage === 'selection_board')
                            <td style="padding: 0.85rem 1rem; text-align: center; font-weight: 800;">{{ $item['rank'] }}</td>
                            <td style="padding: 0.85rem 1rem; font-family: 'JetBrains Mono', monospace; font-weight: 700;">{{ $item['tracking_number'] }}</td>
                            <td style="padding: 0.85rem 1rem;">
                                <div style="font-weight: 800; color: var(--color-text-primary);">{{ $item['candidate_name'] }}</div>
                                <div style="font-size: 0.78rem; color: var(--color-text-secondary);">CNIC: {{ $item['cnic'] }}</div>
                            </td>
                            <td style="padding: 0.85rem 1rem; text-align: center; font-family: 'JetBrains Mono', monospace;">{{ number_format($item['academic_score'], 1) }}</td>
                            <td style="padding: 0.85rem 1rem; text-align: center; font-family: 'JetBrains Mono', monospace;">{{ number_format($item['research_exp_score'], 1) }}</td>
                            <td style="padding: 0.85rem 1rem; text-align: center; font-family: 'JetBrains Mono', monospace; font-weight: 700;">{{ number_format($item['interview_score'], 1) }}</td>
                            <td style="padding: 0.85rem 1rem; text-align: center; font-family: 'JetBrains Mono', monospace; font-weight: 800; color: var(--primary);">
                                {{ number_format($item['academic_score'] + $item['research_exp_score'] + $item['interview_score'], 1) }}
                            </td>
                            <td style="padding: 0.85rem 1rem; text-align: center;">
                                <span class="badge {{ $item['board_status_badge'] }}">
                                    {{ $item['board_status'] }}
                                </span>
                            </td>

                        @else
                            {{-- Step 5: Consolidated Gazette --}}
                            <td style="padding: 0.85rem 1rem; text-align: center; font-weight: 900; font-size: 1.15rem;">
                                @if($isRank1)
                                    <span style="color: #10b981;">🥇 1</span>
                                @elseif($item['rank'] === 2 && !$isDisq)
                                    <span style="color: #6366f1;">🥈 2</span>
                                @elseif($item['rank'] === 3 && !$isDisq)
                                    <span style="color: #06b6d4;">🥉 3</span>
                                @else
                                    <span style="color: var(--color-text-secondary);">{{ $item['rank'] }}</span>
                                @endif
                            </td>
                            <td style="padding: 0.85rem 1rem; font-family: 'JetBrains Mono', monospace; font-weight: 700;">
                                {{ $item['tracking_number'] }}
                            </td>
                            <td style="padding: 0.85rem 1rem;">
                                <div style="font-weight: 800; color: var(--color-text-primary); font-size: 0.95rem;">
                                    {{ $item['candidate_name'] }}
                                </div>
                                <div style="font-size: 0.78rem; color: var(--color-text-muted);">
                                    {{ $item['father_name'] }} • <span style="font-family: 'JetBrains Mono', monospace;">{{ $item['cnic'] }}</span>
                                </div>
                            </td>
                            <td style="padding: 0.85rem 1rem; text-align: center; font-family: 'JetBrains Mono', monospace;">{{ number_format($item['academic_score'], 1) }}</td>
                            <td style="padding: 0.85rem 1rem; text-align: center; font-family: 'JetBrains Mono', monospace;">{{ number_format($item['research_exp_score'], 1) }}</td>
                            <td style="padding: 0.85rem 1rem; text-align: center; font-family: 'JetBrains Mono', monospace;">{{ number_format($item['peer_score_scaled'], 1) }}</td>
                            <td style="padding: 0.85rem 1rem; text-align: center; font-family: 'JetBrains Mono', monospace;">{{ number_format($item['interview_score'], 1) }}</td>
                            <td style="padding: 0.85rem 1rem; text-align: center; font-family: 'JetBrains Mono', monospace; font-weight: 900; font-size: 1.1rem; color: var(--primary);">
                                {{ number_format($item['aggregate_score'], 1) }}%
                            </td>
                            <td style="padding: 0.85rem 1rem; text-align: center;">
                                <span class="badge {{ $item['recommendation_badge'] }}" style="font-size: 0.72rem; font-weight: 800;">
                                    {{ $item['recommendation'] }}
                                </span>
                            </td>
                        @endif

                        <!-- Common Dossier Actions -->
                        <td style="padding: 0.85rem 1rem; text-align: right; white-space: nowrap;">
                            <div style="display: inline-flex; align-items: center; gap: 0.35rem;">
                                <button type="button" 
                                        wire:click="openDetailModal({{ $item['app']->id }})" 
                                        class="btn btn-secondary btn-sm" 
                                        title="Quick Dossier Preview">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <span>Preview</span>
                                </button>
                                <a href="{{ route('application.print', ['id' => $item['app']->id, 'view' => 'admin']) }}" 
                                   target="_blank" 
                                   class="btn btn-secondary btn-sm" 
                                   title="Print Full Master Dossier (Admin View)">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                                    <span>Print Dossier</span>
                                </a>
                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="padding: 3rem 1rem; text-align: center; color: var(--color-text-muted);">
                            <div style="font-size: 2rem; margin-bottom: 0.5rem;">📋</div>
                            <div style="font-weight: 700; font-size: 1rem; color: var(--color-text-primary);">No Applications Found</div>
                            <div style="font-size: 0.85rem;">No candidates matched your filter criteria for this recruitment stage.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Quick Dossier Preview Modal -->
    @if($showDetailModal && $selectedApplication)
        <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.75); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 1.5rem;"
             wire:keydown.escape="closeDetailModal">
            <div class="card" style="width: 100%; max-width: 820px; max-height: 90vh; overflow-y: auto; background: var(--bg-card); border-color: var(--primary); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); padding: 1.75rem;"
                 @click.outside="$wire.closeDetailModal()">
                
                <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.25rem;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span class="badge badge-indigo">{{ $selectedApplication->tracking_number }}</span>
                            <span class="badge {{ in_array($selectedApplication->status, ['ELIGIBLE', 'UNDER_REVIEW', 'APPROVED']) ? 'badge-emerald' : 'badge-slate' }}">{{ $selectedApplication->status_label }}</span>
                        </div>
                        <h3 style="margin: 0.4rem 0 0; font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary);">
                            {{ $selectedApplication->user?->basicInfo ? trim($selectedApplication->user->basicInfo->first_name . ' ' . $selectedApplication->user->basicInfo->last_name) : $selectedApplication->user?->email }}
                        </h3>
                        <p style="margin: 0.2rem 0 0; font-size: 0.85rem; color: var(--color-text-secondary);">
                            Applying for: {{ $selectedApplication->campaign?->title }} ({{ $selectedApplication->campaign?->job_code }})
                        </p>
                    </div>

                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <a href="{{ route('application.print', ['id' => $selectedApplication->id, 'view' => 'admin']) }}" 
                           target="_blank" 
                           class="btn btn-primary btn-sm">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                            <span>Open Print Dossier</span>
                        </a>
                        <button type="button" wire:click="closeDetailModal" class="btn btn-secondary btn-sm">✕</button>
                    </div>
                </div>

                <!-- Academic Degrees List -->
                <div style="margin-bottom: 1.25rem;">
                    <h4 style="font-size: 0.9rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-secondary); margin-bottom: 0.5rem;">
                        Verified Academic Degrees
                    </h4>
                    @if($selectedApplication->user?->basicInfo?->degrees->isNotEmpty())
                        <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                            @foreach($selectedApplication->user->basicInfo->degrees as $deg)
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0.75rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.85rem;">
                                    <div>
                                        <strong>{{ $deg->degree_level }}</strong>: {{ $deg->degree_title }} ({{ $deg->institution }})
                                    </div>
                                    <div>
                                        <span class="badge badge-slate">{{ $deg->division }}</span>
                                        <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; margin-left: 0.35rem;">{{ $deg->gpa }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="font-size: 0.85rem; color: var(--color-text-muted); font-style: italic;">No degrees recorded.</div>
                    @endif
                </div>

                <!-- Peer Review Evaluations -->
                @if($selectedApplication->reviews->isNotEmpty())
                    <div style="margin-bottom: 1.25rem;">
                        <h4 style="font-size: 0.9rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-secondary); margin-bottom: 0.5rem;">
                            Peer Review Evaluations ({{ $selectedApplication->reviews->count() }} Assigned)
                        </h4>
                        <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                            @foreach($selectedApplication->reviews as $rev)
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0.75rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.85rem;">
                                    <div>
                                        <strong>{{ $rev->reviewer?->reviewerProfile?->name ?? 'External Referee' }}</strong> ({{ $rev->reviewer?->reviewerProfile?->university ?? 'University' }})
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <span class="badge badge-indigo">{{ $rev->recommendation }}</span>
                                        <strong style="font-family: 'JetBrains Mono', monospace; color: var(--primary);">{{ $rev->overall_score ? number_format($rev->overall_score, 1) . '%' : 'Pending' }}</strong>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem; gap: 0.5rem;">
                    <button type="button" wire:click="closeDetailModal" class="btn btn-secondary">
                        Close Preview
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>
