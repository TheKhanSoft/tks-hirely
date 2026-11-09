<div id="projector-merit-board-root" 
     @if($autoRefresh && $isFeatureEnabled) wire:poll.10s @endif
     style="{{ $themeMode === 'dark' 
        ? '--p-bg: #090e17; --p-card: #111827; --p-card-header: #1e293b; --p-border: #243248; --p-text: #f8fafc; --p-text-muted: #94a3b8; --p-accent: #38bdf8; --p-gold: #fbbf24; --p-emerald: #10b981; --p-rose: #f43f5e;' 
        : '--p-bg: #f8fafc; --p-card: #ffffff; --p-card-header: #f1f5f9; --p-border: #e2e8f0; --p-text: #0f172a; --p-text-muted: #64748b; --p-accent: #0284c7; --p-gold: #d97706; --p-emerald: #059669; --p-rose: #e11d48;' }} 
        background: var(--p-bg); color: var(--p-text); min-height: 100vh; padding: 1.5rem; transition: background 0.3s ease, color 0.3s ease; font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;">

    @if(!$isFeatureEnabled)
        <!-- Decommissioned / Disabled State -->
        <div style="max-width: 800px; margin: 4rem auto; text-align: center; background: var(--p-card); border: 2px dashed var(--p-border); padding: 3rem; border-radius: 16px;">
            <div style="font-size: 3.5rem; margin-bottom: 1rem;">📽️</div>
            <h2 style="font-size: 1.75rem; font-weight: 800; margin-bottom: 0.75rem; color: var(--p-text);">
                Selection Board Big-Screen Projector Merit Board is Disabled
            </h2>
            <p style="font-size: 1rem; color: var(--p-text-muted); line-height: 1.6; margin-bottom: 1.5rem;">
                The Live Projector Merit Board module is currently toggled OFF under the institutional statutory workflow settings.
                The Registrar Secretariat or Super Administrator can enable this module from the Admin Settings panel.
            </p>
            @can('manage-settings')
                <a href="/admin/settings/workflows" class="btn btn-primary" style="padding: 0.75rem 1.75rem; font-weight: 700;">
                    ⚙️ Configure in System Settings
                </a>
            @else
                <span class="badge badge-slate" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                    Institutional Policy Active
                </span>
            @endcan
        </div>
    @else
        <!-- Top Projector Control & Institutional Header Bar -->
        <div style="background: var(--p-card); border: 1px solid var(--p-border); border-radius: 14px; padding: 1.25rem 1.75rem; margin-bottom: 1.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.15); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            
            <!-- Left: Campaign & Meeting Context -->
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 52px; height: 52px; border-radius: 12px; background: linear-gradient(135deg, var(--p-gold), #ea580c); display: flex; align-items: center; justify-content: center; font-size: 1.8rem; box-shadow: 0 4px 12px rgba(251, 191, 36, 0.3);">
                    🏛️
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                        <span style="font-size: 0.75rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; color: var(--p-gold);">
                            STATUTORY SELECTION BOARD DELIBERATION
                        </span>
                        @if($campaign)
                            <span class="badge" style="background: rgba(56, 189, 248, 0.15); color: var(--p-accent); border: 1px solid var(--p-accent); font-weight: 800; font-size: 0.75rem;">
                                ADV REF: {{ $campaign->advertisement?->advertisement_no ?? 'Cycle 2026' }}
                            </span>
                        @endif
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.25rem;">
                        <select wire:model.live="campaignId" style="background: var(--p-card-header); color: var(--p-text); border: 1px solid var(--p-border); border-radius: 8px; font-weight: 700; font-size: 1.15rem; padding: 0.35rem 0.75rem; cursor: pointer; max-width: 480px;">
                            @foreach($campaigns as $camp)
                                <option value="{{ $camp->id }}" @selected($camp->id == $campaignId)>
                                    {{ $camp->title }} ({{ $camp->job_code ?? 'BPS/TTS' }}) - {{ $camp->applications_count }} Candidates
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Center: Quorum Counter -->
            <div style="background: var(--p-card-header); border: 1px solid var(--p-border); border-radius: 10px; padding: 0.6rem 1.25rem; display: flex; align-items: center; gap: 1.25rem;">
                <div style="text-align: center;">
                    <div style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: var(--p-text-muted);">
                        Board Quorum
                    </div>
                    <div style="font-size: 1.1rem; font-weight: 800; color: {{ $committeeQuorum['quorum_met'] ? 'var(--p-emerald)' : 'var(--p-gold)' }};">
                        {{ $committeeQuorum['quorum_met'] ? '🟢 Quorum Met' : '🟡 In Deliberation' }}
                    </div>
                </div>
                <div style="height: 28px; width: 1px; background: var(--p-border);"></div>
                <div style="text-align: center;">
                    <div style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: var(--p-text-muted);">
                        Evaluations Cast
                    </div>
                    <div style="font-size: 1.1rem; font-weight: 800; color: var(--p-text);">
                        {{ $committeeQuorum['members_submitted'] }} / {{ $committeeQuorum['total_members'] }} Members
                    </div>
                </div>
            </div>

            <!-- Right: Projector Controls Toolbar -->
            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                
                <!-- Anonymize Toggle -->
                <button type="button" 
                        wire:click="toggleAnonymization"
                        class="btn" 
                        style="background: {{ $isAnonymized ? 'rgba(244, 63, 94, 0.2)' : 'var(--p-card-header)' }}; color: {{ $isAnonymized ? 'var(--p-rose)' : 'var(--p-text)' }}; border: 1px solid {{ $isAnonymized ? 'var(--p-rose)' : 'var(--p-border)' }}; font-weight: 700; font-size: 0.85rem; padding: 0.5rem 0.9rem; border-radius: 8px;">
                    🛡️ {{ $isAnonymized ? 'Anonymized (Active)' : 'Anonymize Candidates' }}
                </button>

                <!-- High-contrast Dark/Light Mode -->
                <button type="button" 
                        wire:click="toggleTheme" 
                        class="btn" 
                        style="background: var(--p-card-header); color: var(--p-text); border: 1px solid var(--p-border); font-weight: 700; font-size: 0.85rem; padding: 0.5rem 0.9rem; border-radius: 8px;">
                    {{ $themeMode === 'dark' ? '☀️ Light' : '🌙 Dark Projector' }}
                </button>

                <!-- Auto-Sync Toggle -->
                <button type="button" 
                        wire:click="toggleAutoRefresh" 
                        class="btn" 
                        style="background: {{ $autoRefresh ? 'rgba(16, 185, 129, 0.15)' : 'var(--p-card-header)' }}; color: {{ $autoRefresh ? 'var(--p-emerald)' : 'var(--p-text-muted)' }}; border: 1px solid {{ $autoRefresh ? 'var(--p-emerald)' : 'var(--p-border)' }}; font-weight: 700; font-size: 0.85rem; padding: 0.5rem 0.9rem; border-radius: 8px;">
                    🔄 {{ $autoRefresh ? 'Live Sync (10s)' : 'Sync Paused' }}
                </button>

                <!-- Native Fullscreen Toggle Button -->
                <button type="button" 
                        onclick="toggleFullScreenProjector()" 
                        class="btn" 
                        style="background: var(--p-card-header); color: var(--p-text); border: 1px solid var(--p-border); font-weight: 700; font-size: 0.85rem; padding: 0.5rem 0.9rem; border-radius: 8px;">
                    ⛶ Fullscreen
                </button>
            </div>
        </div>

        @if(!$campaign)
            <div style="text-align: center; padding: 4rem 1rem; color: var(--p-text-muted);">
                <h3>No Recruitment Campaign Selected</h3>
                <p>Please select an active campaign from the dropdown above to project the live merit board.</p>
            </div>
        @else
            <!-- Main Grid: Spotlight on Current Candidate & Live Merit Leaderboard -->
            <div style="display: grid; grid-template-columns: 460px 1fr; gap: 1.5rem; align-items: start;">
                
                <!-- Left Column: Spotlight Candidate Card -->
                <div style="background: var(--p-card); border: 1px solid var(--p-border); border-radius: 14px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
                    <div style="background: linear-gradient(135deg, rgba(56, 189, 248, 0.15), rgba(251, 191, 36, 0.1)); padding: 1.25rem; border-bottom: 1px solid var(--p-border); display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 0.75rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; color: var(--p-accent); display: flex; align-items: center; gap: 0.5rem;">
                            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--p-rose); box-shadow: 0 0 8px var(--p-rose); animation: pulse 1.5s infinite;"></span>
                            ACTIVE INTERVIEW SPOTLIGHT
                        </span>
                        @if($activeCandidate)
                            <span class="badge" style="background: var(--p-card); color: var(--p-gold); border: 1px solid var(--p-gold); font-weight: 800; font-size: 0.75rem;">
                                RANK #{{ $activeCandidate['rank'] ?? '—' }}
                            </span>
                        @endif
                    </div>

                    @if($activeCandidate)
                        <div style="padding: 1.5rem;">
                            <!-- Candidate Identity (Respects Anonymization) -->
                            <div style="display: flex; align-items: center; gap: 1.25rem; margin-bottom: 1.5rem;">
                                @if(!$isAnonymized && $activeCandidate['app']->user?->basicInfo?->profile_picture_url)
                                    <img src="{{ $activeCandidate['app']->user->basicInfo->profile_picture_url }}" 
                                         alt="Candidate" 
                                         style="width: 72px; height: 72px; border-radius: 16px; object-fit: cover; border: 2px solid var(--p-accent);">
                                @else
                                    <div style="width: 72px; height: 72px; border-radius: 16px; background: linear-gradient(135deg, var(--p-accent), #6366f1); display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 800; color: white;">
                                        {{ $isAnonymized ? '🔒' : strtoupper(substr($activeCandidate['candidate_name'] ?? 'C', 0, 1)) }}
                                    </div>
                                @endif

                                <div>
                                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 0.82rem; font-weight: 800; color: var(--p-accent);">
                                        {{ $activeCandidate['app']->tracking_number }}
                                    </div>
                                    <h2 style="font-size: 1.35rem; font-weight: 800; margin: 0.2rem 0; color: var(--p-text);">
                                        @if($isAnonymized)
                                            Candidate #{{ $activeCandidate['app']->tracking_number }} (Masked)
                                        @else
                                            {{ $activeCandidate['candidate_name'] }}
                                        @endif
                                    </h2>
                                    <div style="font-size: 0.8rem; color: var(--p-text-muted);">
                                        Quota: <strong style="color: var(--p-text);">{{ $activeCandidate['quota'] ?? 'Open Merit' }}</strong> &bull; Domicile: <strong style="color: var(--p-text);">{{ $activeCandidate['app']->user?->basicInfo?->domicile ?? 'N/A' }}</strong>
                                    </div>
                                </div>
                            </div>

                            <!-- 4 Objective Composite Metric Cards -->
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.5rem;">
                                <div style="background: var(--p-card-header); border: 1px solid var(--p-border); border-radius: 10px; padding: 0.85rem; text-align: center;">
                                    <div style="font-size: 0.7rem; font-weight: 800; color: var(--p-text-muted); text-transform: uppercase;">
                                        Academic (40)
                                    </div>
                                    <div style="font-size: 1.4rem; font-weight: 900; color: var(--p-accent); margin-top: 0.2rem;">
                                        {{ number_format($activeCandidate['academic_score'] ?? 0, 1) }}
                                    </div>
                                </div>

                                <div style="background: var(--p-card-header); border: 1px solid var(--p-border); border-radius: 10px; padding: 0.85rem; text-align: center;">
                                    <div style="font-size: 0.7rem; font-weight: 800; color: var(--p-text-muted); text-transform: uppercase;">
                                        Peer Review (15)
                                    </div>
                                    <div style="font-size: 1.4rem; font-weight: 900; color: var(--p-gold); margin-top: 0.2rem;">
                                        {{ number_format($activeCandidate['peer_review_scaled_score'] ?? 0, 1) }}
                                    </div>
                                </div>

                                <div style="background: var(--p-card-header); border: 1px solid var(--p-border); border-radius: 10px; padding: 0.85rem; text-align: center;">
                                    <div style="font-size: 0.7rem; font-weight: 800; color: var(--p-text-muted); text-transform: uppercase;">
                                        Board Viva (30)
                                    </div>
                                    <div style="font-size: 1.4rem; font-weight: 900; color: var(--p-rose); margin-top: 0.2rem;">
                                        {{ number_format($activeCandidate['interview_score'] ?? 0, 1) }}
                                    </div>
                                </div>

                                <div style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(6, 182, 212, 0.1)); border: 1px solid var(--p-emerald); border-radius: 10px; padding: 0.85rem; text-align: center;">
                                    <div style="font-size: 0.7rem; font-weight: 800; color: var(--p-emerald); text-transform: uppercase;">
                                        Aggregate (100)
                                    </div>
                                    <div style="font-size: 1.5rem; font-weight: 900; color: var(--p-emerald); margin-top: 0.2rem;">
                                        {{ number_format($activeCandidate['aggregate_score'] ?? 0, 1) }}
                                    </div>
                                </div>
                            </div>

                            <!-- Board Member Live Scoring Breakdown -->
                            <div style="margin-bottom: 1.5rem;">
                                <div style="font-size: 0.75rem; font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; color: var(--p-text-muted); margin-bottom: 0.5rem; display: flex; justify-content: space-between;">
                                    <span>Board Member Marks Rollup</span>
                                    <span>{{ $activeEvaluations->count() }} Submissions</span>
                                </div>

                                @if($activeEvaluations->isEmpty())
                                    <div style="background: var(--p-card-header); border: 1px dashed var(--p-border); border-radius: 8px; padding: 1.25rem; text-align: center; color: var(--p-text-muted); font-size: 0.85rem;">
                                        Awaiting committee member scoring submissions for this candidate.
                                    </div>
                                @else
                                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                        @foreach($activeEvaluations as $eval)
                                            @php
                                                $evaluatorName = $eval->boardMember?->basicInfo?->full_name ?: ($eval->boardMember?->email ?? 'Board Member');
                                            @endphp
                                            <div style="background: var(--p-card-header); border: 1px solid var(--p-border); border-radius: 8px; padding: 0.6rem 0.85rem; display: flex; justify-content: space-between; align-items: center;">
                                                <div>
                                                    <div style="font-weight: 700; font-size: 0.88rem; color: var(--p-text);">
                                                        {{ $evaluatorName }}
                                                    </div>
                                                    <div style="font-size: 0.72rem; color: var(--p-text-muted);">
                                                        Evaluated {{ $eval->created_at?->diffForHumans() }}
                                                    </div>
                                                </div>
                                                <div style="text-align: right;">
                                                    <span style="font-size: 1.1rem; font-weight: 800; color: var(--p-accent);">
                                                        {{ number_format($eval->interview_score, 1) }}
                                                    </span>
                                                    <span style="font-size: 0.75rem; color: var(--p-text-muted);">/ 50</span>
                                                    @if($eval->is_outlier)
                                                        <span class="badge" style="background: rgba(244, 63, 94, 0.2); color: var(--p-rose); font-size: 0.65rem; margin-left: 0.35rem;">
                                                            ⚠️ Outlier
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <!-- Consensus Verdict Badge -->
                            <div style="background: var(--p-card-header); border: 1px solid var(--p-border); border-radius: 10px; padding: 0.9rem; display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 0.82rem; font-weight: 700; color: var(--p-text-muted);">Consensus Recommendation:</span>
                                <span class="badge {{ $activeCandidate['board_status_badge'] ?? 'badge-slate' }}" style="font-size: 0.85rem; font-weight: 800; padding: 0.35rem 0.75rem;">
                                    {{ $activeCandidate['board_status'] ?? 'AWAITING EVALUATION' }}
                                </span>
                            </div>
                        </div>
                    @else
                        <div style="padding: 2.5rem; text-align: center; color: var(--p-text-muted);">
                            Select a candidate from the merit roster on the right to spotlight their details.
                        </div>
                    @endif
                </div>

                <!-- Right Column: Full Merit Leaderboard Roster -->
                <div style="background: var(--p-card); border: 1px solid var(--p-border); border-radius: 14px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
                    <div style="background: var(--p-card-header); padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--p-border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
                        <div>
                            <h3 style="font-size: 1.15rem; font-weight: 800; margin: 0; color: var(--p-text);">
                                Live Merit Roster & Board Standings
                            </h3>
                            <div style="font-size: 0.8rem; color: var(--p-text-muted); margin-top: 0.2rem;">
                                Ranked by Statutory Aggregate Marks (Academic + Research + Peer Review + Interview)
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <!-- Quota Filter -->
                            <select wire:model.live="quotaFilter" style="background: var(--p-card); color: var(--p-text); border: 1px solid var(--p-border); border-radius: 6px; padding: 0.35rem 0.65rem; font-size: 0.8rem; font-weight: 700;">
                                <option value="ALL">All Quotas</option>
                                <option value="OPEN_MERIT">Open Merit</option>
                                <option value="PUNJAB">Punjab</option>
                                <option value="SINDH_RURAL">Sindh Rural</option>
                                <option value="SINDH_URBAN">Sindh Urban</option>
                                <option value="KPK">KP</option>
                                <option value="BALOCHISTAN">Balochistan</option>
                            </select>

                            <!-- Quick Search -->
                            <input type="text" 
                                   wire:model.live.debounce.300ms="search" 
                                   placeholder="Filter tracking / name..." 
                                   style="background: var(--p-card); color: var(--p-text); border: 1px solid var(--p-border); border-radius: 6px; padding: 0.35rem 0.65rem; font-size: 0.8rem; width: 170px;">
                        </div>
                    </div>

                    <!-- Leaderboard Table -->
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.88rem;">
                            <thead>
                                <tr style="background: var(--p-card-header); color: var(--p-text-muted); font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--p-border);">
                                    <th style="padding: 0.85rem 1rem;">Rank</th>
                                    <th style="padding: 0.85rem 1rem;">Tracking #</th>
                                    <th style="padding: 0.85rem 1rem;">Candidate</th>
                                    <th style="padding: 0.85rem 1rem; text-align: center;">Academic (40)</th>
                                    <th style="padding: 0.85rem 1rem; text-align: center;">Peer (15)</th>
                                    <th style="padding: 0.85rem 1rem; text-align: center;">Viva (30)</th>
                                    <th style="padding: 0.85rem 1rem; text-align: center;">Aggregate (100)</th>
                                    <th style="padding: 0.85rem 1rem; text-align: right;">Verdict</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($candidates as $item)
                                    @php
                                        $isSpotlighted = $item['app']->id == $activeCandidateId;
                                        $isTopTier = is_numeric($item['rank']) && intval($item['rank']) <= ($campaign->vacancies_count ?? 1);
                                    @endphp
                                    <tr wire:click="spotlightCandidate({{ $item['app']->id }})" 
                                        style="border-bottom: 1px solid var(--p-border); cursor: pointer; transition: background 0.15s ease; 
                                               background: {{ $isSpotlighted ? 'rgba(56, 189, 248, 0.12)' : ($isTopTier ? 'rgba(16, 185, 129, 0.05)' : 'transparent') }};"
                                        onmouseover="this.style.background='var(--p-card-header)'"
                                        onmouseout="this.style.background='{{ $isSpotlighted ? 'rgba(56, 189, 248, 0.12)' : ($isTopTier ? 'rgba(16, 185, 129, 0.05)' : 'transparent') }}'">
                                        
                                        <!-- Rank -->
                                        <td style="padding: 0.85rem 1rem; font-weight: 800;">
                                            @if($isTopTier)
                                                <span style="display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; background: var(--p-emerald); color: white; font-size: 0.8rem;">
                                                    {{ $item['rank'] }}
                                                </span>
                                            @else
                                                <span style="color: var(--p-text-muted); padding-left: 0.4rem;">
                                                    {{ $item['rank'] }}
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Tracking # -->
                                        <td style="padding: 0.85rem 1rem; font-family: 'JetBrains Mono', monospace; font-size: 0.82rem; font-weight: 700; color: var(--p-accent);">
                                            {{ $item['app']->tracking_number }}
                                        </td>

                                        <!-- Candidate Name / Mask -->
                                        <td style="padding: 0.85rem 1rem;">
                                            <div style="font-weight: 700; color: var(--p-text);">
                                                @if($isAnonymized)
                                                    Candidate #{{ $item['app']->tracking_number }}
                                                @else
                                                    {{ $item['candidate_name'] }}
                                                @endif
                                            </div>
                                            <div style="font-size: 0.72rem; color: var(--p-text-muted);">
                                                {{ $item['quota'] ?? 'Open Merit' }} &bull; {{ $item['app']->user?->basicInfo?->domicile ?? 'Pakistan' }}
                                            </div>
                                        </td>

                                        <!-- Scores -->
                                        <td style="padding: 0.85rem 1rem; text-align: center; font-weight: 700; color: var(--p-text);">
                                            {{ number_format($item['academic_score'] ?? 0, 1) }}
                                        </td>
                                        <td style="padding: 0.85rem 1rem; text-align: center; font-weight: 700; color: var(--p-gold);">
                                            {{ number_format($item['peer_review_scaled_score'] ?? 0, 1) }}
                                        </td>
                                        <td style="padding: 0.85rem 1rem; text-align: center; font-weight: 700; color: var(--p-rose);">
                                            {{ number_format($item['interview_score'] ?? 0, 1) }}
                                        </td>
                                        <td style="padding: 0.85rem 1rem; text-align: center; font-weight: 900; font-size: 1.05rem; color: var(--p-emerald);">
                                            {{ number_format($item['aggregate_score'] ?? 0, 1) }}
                                        </td>

                                        <!-- Verdict -->
                                        <td style="padding: 0.85rem 1rem; text-align: right;">
                                            <span class="badge {{ $item['board_status_badge'] ?? 'badge-slate' }}" style="font-size: 0.75rem; font-weight: 800;">
                                                {{ $item['board_status'] ?? 'PENDING' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" style="padding: 3rem 1rem; text-align: center; color: var(--p-text-muted);">
                                            No candidates found matching the active campaign and filter criteria.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        @endif
    @endif

    <!-- Native Fullscreen Script -->
    <script>
        function toggleFullScreenProjector() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.warn(`Error attempting to enable full-screen mode: ${err.message}`);
                });
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }
    </script>
</div>
