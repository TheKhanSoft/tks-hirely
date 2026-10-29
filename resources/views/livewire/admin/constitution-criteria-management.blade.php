<div>
    <!-- Top Alert Messages -->
    @if(session()->has('message'))
        <div class="mb-4" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: var(--radius-sm); padding: 0.85rem 1.25rem; display: flex; align-items: center; justify-content: space-between; color: var(--success);">
            <div style="display: flex; align-items: center; gap: 0.6rem; font-size: 0.9rem; font-weight: 600;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <span>{{ session('message') }}</span>
            </div>
            <button type="button" wire:click="$refresh" style="background: none; border: none; color: inherit; cursor: pointer; font-size: 1rem;">✕</button>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="mb-4" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: var(--radius-sm); padding: 0.85rem 1.25rem; display: flex; align-items: center; justify-content: space-between; color: #ef4444;">
            <div style="display: flex; align-items: center; gap: 0.6rem; font-size: 0.9rem; font-weight: 600;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" wire:click="$refresh" style="background: none; border: none; color: inherit; cursor: pointer; font-size: 1rem;">✕</button>
        </div>
    @endif

    <!-- Executive Control Header -->
    <div class="page-hero-header">
        <div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div class="brand-icon-box" style="background: linear-gradient(135deg, rgba(99,102,241,0.25), rgba(168,85,247,0.4)); box-shadow: 0 4px 14px rgba(99,102,241,0.25);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" style="color: #ffffff;">
                        <path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                        <path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                        <path d="M7 21h10"/>
                        <path d="M12 3v18"/>
                        <path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"/>
                    </svg>
                </div>
                <div>
                    <h1>Statutory Criteria, Committees & Process Flow Engine</h1>
                    <p>Standing statutory committees, dynamic recruitment process flows, and candidate routing with automated decision logic.</p>
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <!-- Segmented Top Tabs -->
            <div style="display: flex; gap: 0.35rem; background: rgba(15, 23, 42, 0.6); padding: 0.3rem; border-radius: var(--radius-md); border: 1px solid rgba(255, 255, 255, 0.08); flex-wrap: wrap;">
                <button type="button" 
                    wire:click="setTab('board_registry')" 
                    class="btn {{ $activeTab === 'board_registry' ? 'btn-primary' : 'btn-secondary' }}"
                    style="padding: 0.5rem 0.85rem; font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.45rem; border-radius: 8px;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 21h18"/>
                        <path d="M5 21V7l8-4v18"/>
                        <path d="M19 21V11l-6-4"/>
                    </svg>
                    <span>Standing Committees & Boards ({{ $registeredBoards->count() }})</span>
                </button>
                <button type="button" 
                    wire:click="setTab('process_flow')" 
                    class="btn {{ $activeTab === 'process_flow' ? 'btn-primary' : 'btn-secondary' }}"
                    style="padding: 0.5rem 0.85rem; font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.45rem; border-radius: 8px;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                    </svg>
                    <span>Recruitment Pipelines ({{ $processFlows->count() }})</span>
                </button>
                <button type="button" 
                    wire:click="setTab('campaign_constitution')" 
                    class="btn {{ $activeTab === 'campaign_constitution' ? 'btn-primary' : 'btn-secondary' }}"
                    style="padding: 0.5rem 0.85rem; font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.45rem; border-radius: 8px;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <circle cx="12" cy="12" r="6"/>
                        <circle cx="12" cy="12" r="2"/>
                    </svg>
                    <span>Campaign Quotas</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 1: STANDING GENERAL COMMITTEES & STATUTORY BOARDS (BASIC DETAILS LIST) -->
    <!-- ========================================================================= -->
    @if($activeTab === 'board_registry')
        <div class="stat-kpi-grid">
            <div class="metric-card" style="--card-accent: var(--primary); --metric-icon-bg: var(--primary-light); --metric-icon-color: var(--primary);">
                <div class="metric-header">
                    <span class="metric-label">Standing Bodies</span>
                    <div class="metric-icon-wrap">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4"/>
                        </svg>
                    </div>
                </div>
                <div class="metric-value" style="color: var(--primary);">{{ $registeredBoards->count() }}</div>
                <div class="metric-subtext">Statutory councils & committees</div>
            </div>

            <div class="metric-card" style="--card-accent: var(--secondary); --metric-icon-bg: var(--secondary-light); --metric-icon-color: var(--secondary);">
                <div class="metric-header">
                    <span class="metric-label">Selection Boards</span>
                    <div class="metric-icon-wrap">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        </svg>
                    </div>
                </div>
                <div class="metric-value" style="color: var(--secondary);">{{ $registeredBoards->where('committee_type', 'SELECTION_BOARD')->count() }}</div>
                <div class="metric-subtext">Interview & sanction panels</div>
            </div>

            <div class="metric-card" style="--card-accent: #8b5cf6; --metric-icon-bg: rgba(139,92,246,0.12); --metric-icon-color: #8b5cf6;">
                <div class="metric-header">
                    <span class="metric-label">Scrutiny & Review</span>
                    <div class="metric-icon-wrap">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/>
                        </svg>
                    </div>
                </div>
                <div class="metric-value" style="color: #8b5cf6;">{{ $registeredBoards->whereIn('committee_type', ['SCRUTINY_COMMITTEE', 'APPELLATE_COMMITTEE'])->count() }}</div>
                <div class="metric-subtext">Vetting & appeals bodies</div>
            </div>

            <div class="metric-card" style="--card-accent: #10b981; --metric-icon-bg: rgba(16,185,129,0.12); --metric-icon-color: #10b981;">
                <div class="metric-header">
                    <span class="metric-label">Strict Quorum Policy</span>
                    <div class="metric-icon-wrap">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </div>
                </div>
                <div class="metric-value" style="color: #10b981;">{{ $registeredBoards->where('enforce_strict_quota', true)->count() }}</div>
                <div class="metric-subtext">Statutory seats enforced</div>
            </div>
        </div>

        <x-entity-card-grid
            :items="$registeredBoards"
            :per-page="$perPage"
            :view-mode="$viewMode"
            :search="$search"
            search-placeholder="Search boards or committees by name, slug, or type..."
            entity-name="Statutory Committees & Boards"
            :can-export="true"
            :can-import="false"
        >
            <x-slot:filters>
                <div style="width: 170px;">
                    <select class="form-control form-select" wire:model.live="sectorFilter" style="background: var(--bg-card); color: var(--color-text-primary); font-size: 0.82rem;">
                        <option value="ALL">All Sectors</option>
                        <option value="ACADEMIC">Academic</option>
                        <option value="CORPORATE">Corporate</option>
                        <option value="PUBLIC_SECTOR">Public Sector</option>
                        <option value="HEALTHCARE">Healthcare</option>
                        <option value="JUDICIAL">Judicial</option>
                    </select>
                </div>
            </x-slot:filters>

            <x-slot:headerActions>
                <button type="button" class="btn btn-primary" wire:click="openCreateBoardModal" style="display: inline-flex; align-items: center; gap: 0.45rem; font-size: 0.82rem; font-weight: 700; padding: 0.45rem 1rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    <span>+ Empanel Committee</span>
                </button>
            </x-slot:headerActions>

            <!-- Cards (3 to 5 per row) -->
            @foreach($registeredBoards as $board)
                @php 
                    $typeBadge = $board->type_badge; 
                    $sectorBadge = $board->sector_badge;
                    $boardTheme = match($board->committee_type) {
                        'SELECTION_BOARD' => 'avatar-theme-indigo',
                        'SCRUTINY_COMMITTEE' => 'avatar-theme-amber',
                        'APPELLATE_COMMITTEE' => 'avatar-theme-purple',
                        'SYNDICATE' => 'avatar-theme-emerald',
                        default => 'avatar-theme-cyan',
                    };
                @endphp
                <x-entity-card
                    :key="'board-card-'.$board->id"
                    :avatar-initial="substr($board->name, 0, 1)"
                    :avatar-class="$boardTheme"
                    :avatar-badge="strtoupper(substr($board->organization_sector ?? 'GEN', 0, 3))"
                    :title="$board->name"
                    :subtitle="'#' . ($board->slug ?: 'board-'.$board->id)"
                    :tag="$board->required_total . ' Members'"
                    :status-badge="$board->enforce_strict_quota ? 'Strict Quota' : 'Flexible Quota'"
                    :status-class="$board->enforce_strict_quota ? 'badge-primary' : 'badge-paid'"
                >
                    <x-slot:body>
                        <div class="entity-card-metric-row">
                            <span class="entity-card-metric-label">Committee Type</span>
                            <span class="badge" style="background: {{ $typeBadge['bg'] }}; color: {{ $typeBadge['color'] }}; font-weight: 700; font-size: 0.72rem; padding: 0.15rem 0.4rem;">
                                {{ $typeBadge['icon'] }} {{ $typeBadge['label'] }}
                            </span>
                        </div>
                        <div class="entity-card-metric-row">
                            <span class="entity-card-metric-label">Quorum Quota</span>
                            <span class="entity-card-metric-value" style="font-weight: 700; color: var(--color-text-primary);">
                                {{ $board->required_internal }} Internal • {{ $board->required_external }} External
                            </span>
                        </div>
                        <div class="entity-card-metric-row">
                            <span class="entity-card-metric-label">Responsible Stage</span>
                            <span class="badge" style="background: rgba(15, 23, 42, 0.7); border: 1px solid rgba(255, 255, 255, 0.1); color: var(--color-text-secondary); font-size: 0.7rem;">
                                @if($board->responsible_stage === 'stage_scrutiny')
                                    📑 Step 2: Scrutiny Vetting
                                @elseif($board->responsible_stage === 'stage_peer_review')
                                    🔬 Step 3: Peer Review
                                @elseif($board->responsible_stage === 'stage_selection_board')
                                    👥 Step 4: Selection Board
                                @elseif($board->responsible_stage === 'stage_syndicate')
                                    🏛️ Step 5: Syndicate Council
                                @elseif($board->responsible_stage === 'stage_appellate')
                                    ⚖️ Appellate Tribunal
                                @else
                                    ⚙️ General Board
                                @endif
                            </span>
                        </div>
                        @if($board->required_peer_reviewers > 0)
                            <div class="entity-card-metric-row" style="margin-top: 0.2rem;">
                                <span class="entity-card-metric-label">Peer Experts</span>
                                <span class="badge" style="background: rgba(6,182,212,0.12); color: var(--secondary); font-size: 0.7rem; padding: 0.1rem 0.35rem;">
                                    {{ $board->required_peer_reviewers }} Referees
                                </span>
                            </div>
                        @endif
                    </x-slot:body>

                    <x-slot:quickActions>
                        <button type="button" 
                                class="action-btn action-btn-view" 
                                wire:click="viewBoardQuorum({{ $board->id }})" 
                                data-tooltip="View Quorum Breakdown"
                                aria-label="View Quorum">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                        <button type="button" 
                                class="action-btn action-btn-edit" 
                                wire:click="openEditBoardModal({{ $board->id }})" 
                                data-tooltip="Configure Board Details & Rules"
                                aria-label="Edit Board">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </button>
                        <button type="button" 
                                class="action-btn action-btn-view" 
                                wire:click="cloneBoardModel({{ $board->id }})" 
                                data-tooltip="Clone / Duplicate Board"
                                aria-label="Clone Board">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="14" height="14" x="8" y="8" rx="2" ry="2"/>
                                <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>
                            </svg>
                        </button>
                        <button type="button" 
                                class="action-btn action-btn-delete" 
                                wire:click="deleteBoardModel({{ $board->id }})" 
                                wire:confirm="Are you sure you want to delete this Statutory Board Constitution?" 
                                data-tooltip="Delete Board"
                                aria-label="Delete Board">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                            </svg>
                        </button>
                    </x-slot:quickActions>

                    <x-slot:menuItems>
                        <x-entity-card-menu-item 
                            wire-click="viewBoardQuorum({{ $board->id }})"
                            label="View Quorum Breakdown"
                        >
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        </x-entity-card-menu-item>

                        <x-entity-card-menu-item 
                            wire-click="applyBoardConstitutionToCampaign({{ $board->id }})"
                            label="Load into Active Campaign"
                        >
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                        </x-entity-card-menu-item>

                        <x-entity-card-menu-item 
                            wire-click="openEditBoardModal({{ $board->id }})"
                            label="Edit Board Details & Rules"
                        >
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </x-entity-card-menu-item>

                        <x-entity-card-menu-item 
                            wire-click="cloneBoardModel({{ $board->id }})"
                            label="Clone / Duplicate Board"
                        >
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                        </x-entity-card-menu-item>

                        <x-entity-card-menu-item 
                            wire-click="deleteBoardModel({{ $board->id }})"
                            label="Delete Board Constitution"
                            theme="danger"
                            confirm-message="Are you sure you want to delete this Statutory Board Constitution?"
                        >
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        </x-entity-card-menu-item>
                    </x-slot:menuItems>
                </x-entity-card>
            @endforeach

            <!-- Preserved Table View Alternative -->
            <x-slot:table>
                <div class="table-responsive">
                    <table class="table" style="margin: 0; font-size: 0.85rem; vertical-align: middle;">
                        <thead style="background: rgba(15, 23, 42, 0.3); font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-muted);">
                            <tr>
                                <th style="padding: 0.85rem 1.25rem;">Committee / Board Name</th>
                                <th style="padding: 0.85rem 1rem;">Type</th>
                                <th style="padding: 0.85rem 1rem;">Sector</th>
                                <th style="padding: 0.85rem 1rem;">Quorum Summary</th>
                                <th style="padding: 0.85rem 1rem;">Responsible Stage</th>
                                <th style="padding: 0.85rem 1rem; text-align: center;">Status</th>
                                <th style="padding: 0.85rem 1.25rem; text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody style="divide-y: 1px solid rgba(255, 255, 255, 0.05);">
                            @forelse($registeredBoards as $board)
                                @php 
                                    $typeBadge = $board->type_badge;
                                    $sectorBadge = $board->sector_badge;
                                @endphp
                                <tr style="transition: background 0.15s ease;">
                                    <td style="padding: 0.9rem 1.25rem;">
                                        <div style="font-weight: 700; color: var(--color-text-primary); font-size: 0.92rem; margin-bottom: 0.2rem;">
                                            {{ $board->name }}
                                        </div>
                                        @if($board->slug)
                                            <div style="font-size: 0.72rem; color: var(--color-text-muted); font-family: monospace;">
                                                #{{ $board->slug }}
                                            </div>
                                        @endif
                                    </td>
                                    <td style="padding: 0.9rem 1rem; white-space: nowrap;">
                                        <span class="badge" style="background: {{ $typeBadge['bg'] }}; color: {{ $typeBadge['color'] }}; font-weight: 700; font-size: 0.74rem;">
                                            {{ $typeBadge['icon'] }} {{ $typeBadge['label'] }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.9rem 1rem; white-space: nowrap;">
                                        <span class="badge" style="background: {{ $sectorBadge['bg'] }}; color: {{ $sectorBadge['color'] }}; font-weight: 600; font-size: 0.72rem;">
                                            {{ $sectorBadge['icon'] }} {{ $sectorBadge['label'] }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.9rem 1rem;">
                                        <div style="display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;">
                                            <span style="font-weight: 800; color: var(--color-text-primary); font-size: 0.88rem;">
                                                {{ $board->required_total }} Members
                                            </span>
                                            <span style="font-size: 0.72rem; color: var(--color-text-muted);">
                                                ({{ $board->required_internal }} Internal • {{ $board->required_external }} External{{ $board->required_peer_reviewers > 0 ? ' • ' . $board->required_peer_reviewers . ' Referees' : '' }})
                                            </span>
                                        </div>
                                        @if($board->enforce_strict_quota)
                                            <span style="font-size: 0.68rem; color: #f59e0b; display: inline-flex; align-items: center; gap: 0.2rem; margin-top: 0.15rem;">
                                                🔒 Strict Quorum Quota
                                            </span>
                                        @else
                                            <span style="font-size: 0.68rem; color: #10b981; display: inline-flex; align-items: center; gap: 0.2rem; margin-top: 0.15rem;">
                                                ⚡ Flexible Quota
                                            </span>
                                        @endif
                                    </td>
                                    <td style="padding: 0.9rem 1rem; white-space: nowrap;">
                                        <span class="badge" style="background: rgba(15, 23, 42, 0.7); border: 1px solid rgba(255, 255, 255, 0.1); color: var(--color-text-secondary); font-size: 0.73rem; font-weight: 600;">
                                            @if($board->responsible_stage === 'stage_scrutiny')
                                                📑 Step 2: Scrutiny Vetting
                                            @elseif($board->responsible_stage === 'stage_peer_review')
                                                🔬 Step 3: Peer Review
                                            @elseif($board->responsible_stage === 'stage_selection_board')
                                                👥 Step 4: Selection Board
                                            @elseif($board->responsible_stage === 'stage_syndicate')
                                                🏛️ Step 5: Syndicate Council
                                            @elseif($board->responsible_stage === 'stage_appellate')
                                                ⚖️ Appellate Tribunal
                                            @else
                                                ⚙️ General Board
                                            @endif
                                        </span>
                                    </td>
                                    <td style="padding: 0.9rem 1rem; text-align: center; white-space: nowrap;">
                                        <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; font-weight: 700; font-size: 0.7rem;">
                                            ● ACTIVE
                                        </span>
                                    </td>
                                    <td style="padding: 0.9rem 1.25rem; text-align: right; white-space: nowrap;">
                                        <div class="action-btn-group justify-end" x-data="{ openMenu: false }">
                                            <button type="button" 
                                                class="action-btn action-btn-view" 
                                                wire:click="viewBoardQuorum({{ $board->id }})" 
                                                data-tooltip="View Quorum & Rule Breakdown"
                                                aria-label="View Quorum Details">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                            </button>
                                            <button type="button" 
                                                class="action-btn action-btn-edit" 
                                                wire:click="openEditBoardModal({{ $board->id }})" 
                                                data-tooltip="Configure Board Details & Rules"
                                                aria-label="Edit Board">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                            </button>

                                            <!-- 3-Dot More Actions Dropdown -->
                                            <div class="table-action-dropdown-wrap" @click.outside="openMenu = false">
                                                <button type="button" 
                                                        class="action-btn" 
                                                        @click="openMenu = !openMenu" 
                                                        data-tooltip="More Actions" 
                                                        aria-label="More Actions">
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                                                        <circle cx="12" cy="5" r="1.5"/>
                                                        <circle cx="12" cy="12" r="1.5"/>
                                                        <circle cx="12" cy="19" r="1.5"/>
                                                    </svg>
                                                </button>
                                                <div class="table-action-dropdown" x-show="openMenu" x-cloak style="display: none;" @click="openMenu = false">
                                                    <button type="button" 
                                                        class="table-action-menu-item success" 
                                                        wire:click="applyBoardConstitutionToCampaign({{ $board->id }})">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                                                        </svg>
                                                        <span>Apply to Campaign</span>
                                                    </button>
                                                    <button type="button" 
                                                        class="table-action-menu-item" 
                                                        wire:click="cloneBoardModel({{ $board->id }})">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                            <rect width="14" height="14" x="8" y="8" rx="2" ry="2"/>
                                                            <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>
                                                        </svg>
                                                        <span>Clone Board</span>
                                                    </button>
                                                    <div class="table-action-menu-divider"></div>
                                                    <button type="button" 
                                                        class="table-action-menu-item danger" 
                                                        wire:click="deleteBoardModel({{ $board->id }})" 
                                                        wire:confirm="Are you sure you want to delete this Statutory Board Constitution?">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                            <polyline points="3 6 5 6 21 6"/>
                                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                        </svg>
                                                        <span>Delete Board</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 3rem 1.5rem; color: var(--color-text-muted);">
                                        No Statutory Committees Found matching criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-slot:table>
        </x-entity-card-grid>
    @endif

    <!-- ========================================================================= -->
    <!-- TAB 2: END-TO-END DYNAMIC RECRUITMENT PROCESS FLOW ENGINE                  -->
    <!-- ========================================================================= -->
    @if($activeTab === 'process_flow')
        @if($pipelineViewMode === 'list')
            <!-- Pipeline Directory Header Bar -->
            <div class="card mb-6" style="padding: 1.25rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--color-text-primary); margin: 0 0 0.35rem 0; line-height: 1.2;">
                            Recruitment Pipeline Flows
                        </h2>
                        <p style="font-size: 0.82rem; color: var(--color-text-secondary); margin: 0;">
                            Configure end-to-end multi-stage recruitment pipelines, statutory gating criteria, reviewer thresholds, and committee handoffs.
                        </p>
                    </div>

                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                        <button type="button" class="btn btn-primary" wire:click="createNewFlowPipeline" style="font-size: 0.82rem; font-weight: 700; padding: 0.55rem 1.15rem; display: inline-flex; align-items: center; gap: 0.4rem;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            <span>+ New Pipeline Flow</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Pipelines Card Grid -->
            <div class="entity-cards-grid">
                @foreach($processFlows as $pFlow)
                    @php
                        $sectorBadge = match($pFlow->sector) {
                            'ACADEMIC' => ['bg' => 'rgba(99, 102, 241, 0.15)', 'color' => 'var(--primary)', 'icon' => '🎓'],
                            'CORPORATE' => ['bg' => 'rgba(6, 182, 212, 0.15)', 'color' => 'var(--secondary)', 'icon' => '🏢'],
                            'HEALTHCARE' => ['bg' => 'rgba(16, 185, 129, 0.15)', 'color' => '#10b981', 'icon' => '🏥'],
                            'PUBLIC_SECTOR' => ['bg' => 'rgba(245, 158, 11, 0.15)', 'color' => '#f59e0b', 'icon' => '🏛️'],
                            default => ['bg' => 'rgba(148, 163, 184, 0.15)', 'color' => '#cbd5e1', 'icon' => '⚙️'],
                        };
                        $stagesCount = count($pFlow->stages ?? []);
                        $campaignsCount = $pFlow->campaigns()->count();
                    @endphp

                    <x-entity-card
                        :key="'pipeline-flow-card-'.$pFlow->id"
                        :avatar-initial="$sectorBadge['icon']"
                        avatar-class="avatar-theme-indigo"
                        :title="$pFlow->name"
                        :subtitle="'Sector: ' . $pFlow->sector . ' • ID: ' . ($pFlow->public_id ?? $pFlow->slug)"
                        :is-active="$pFlow->is_active"
                    >
                        <x-slot:titleSlot>
                            <div style="display: flex; flex-direction: column; min-width: 0;">
                                <div style="display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap; margin-bottom: 0.2rem;">
                                    <span class="badge" style="background: {{ $sectorBadge['bg'] }}; color: {{ $sectorBadge['color'] }}; font-weight: 800; font-size: 0.7rem;">
                                        {{ $sectorBadge['icon'] }} {{ $pFlow->sector }}
                                    </span>
                                    @if($pFlow->is_default)
                                        <span class="badge badge-amber" style="font-weight: 800; font-size: 0.68rem;">
                                            ★ SYSTEM DEFAULT
                                        </span>
                                    @endif
                                </div>
                                <div style="font-size: 1.05rem; font-weight: 800; color: var(--color-text-primary); line-height: 1.3; word-break: break-word;">
                                    {{ $pFlow->name }}
                                </div>
                            </div>
                        </x-slot:titleSlot>

                        <!-- Status Strip Above Action Footer (Interactive Click-to-Toggle) -->
                        <x-slot:badge>
                            <button type="button" 
                                    class="entity-card-status-toggle-btn"
                                    wire:click="toggleFlowActive({{ $pFlow->id }})"
                                    style="font-size: 0.74rem; font-weight: 800; cursor: pointer; color: {{ $pFlow->is_active ? '#10b981' : '#ef4444' }};"
                                    title="Click to toggle pipeline status">
                                <span style="display: inline-flex; align-items: center; gap: 0.4rem;">
                                    <span style="display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: {{ $pFlow->is_active ? '#10b981' : '#ef4444' }}; box-shadow: 0 0 8px {{ $pFlow->is_active ? 'rgba(16, 185, 129, 0.9)' : 'rgba(239, 68, 68, 0.9)' }};"></span>
                                    <span>{{ $pFlow->is_active ? 'ACTIVE PIPELINE' : 'INACTIVE PIPELINE' }}</span>
                                </span>
                                <span style="font-size: 0.68rem; opacity: 0.75; font-weight: 700; text-transform: uppercase;">Click to toggle ↻</span>
                            </button>
                        </x-slot:badge>

                        <x-slot:body>
                            <div class="entity-card-metric-row">
                                <span class="entity-card-metric-label">Pipeline Depth</span>
                                <span class="badge badge-cyan" style="font-weight: 800; font-size: 0.72rem;">
                                    {{ $stagesCount }} Sequential Stages
                                </span>
                            </div>

                            <!-- Visual Stage Progression Strip -->
                            <div style="margin: 0.4rem 0;">
                                <div style="display: flex; flex-wrap: wrap; gap: 0.28rem;">
                                    @foreach($pFlow->stages ?? [] as $s)
                                        <span class="badge" style="background: rgba(99, 102, 241, 0.12); color: var(--primary); font-size: 0.68rem; font-weight: 700; line-height: 1.2;">
                                            {{ $loop->iteration }}. {{ $s['name'] ?? 'Stage' }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="entity-card-metric-row">
                                <span class="entity-card-metric-label">Linked Job Positions</span>
                                @if($campaignsCount > 0)
                                    <span class="badge badge-indigo" style="font-weight: 800; font-size: 0.72rem;">
                                        💼 {{ $campaignsCount }} Campaign{{ $campaignsCount === 1 ? '' : 's' }}
                                    </span>
                                @else
                                    <span style="font-size: 0.72rem; color: var(--color-text-muted);">
                                        Unassigned
                                    </span>
                                @endif
                            </div>

                            @if($pFlow->description)
                                <div class="entity-card-metric-row" style="margin-top: 0.2rem;">
                                    <span class="entity-card-metric-label">Blueprint</span>
                                    <span class="entity-card-metric-value" style="font-size: 0.72rem; color: var(--color-text-secondary); max-width: 155px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $pFlow->description }}">
                                        {{ $pFlow->description }}
                                    </span>
                                </div>
                            @endif
                        </x-slot:body>

                        <!-- Quick Hover Actions -->
                        <x-slot:quickActions>
                            <button type="button" 
                                    class="action-btn action-btn-view" 
                                    wire:click="viewPipelineDesign({{ $pFlow->id }})" 
                                    data-tooltip="Open Visual Stage Designer Canvas" 
                                    aria-label="Design Pipeline Flow">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                            </button>

                            <button type="button" 
                                    class="action-btn action-btn-edit" 
                                    wire:click="openEditPipelineModal({{ $pFlow->id }})" 
                                    data-tooltip="Rename & Edit Pipeline Details" 
                                    aria-label="Edit Pipeline Info">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                        </x-slot:quickActions>

                        <!-- 3-Dot Options Menu -->
                        <x-slot:menuItems>
                            <x-entity-card-menu-item 
                                wire-click="viewPipelineDesign({{ $pFlow->id }})"
                                label="Open Stage Designer Canvas"
                            >
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                            </x-entity-card-menu-item>

                            <x-entity-card-menu-item 
                                wire-click="openEditPipelineModal({{ $pFlow->id }})"
                                label="Rename & Edit Pipeline Info"
                            >
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </x-entity-card-menu-item>

                            <x-entity-card-menu-item 
                                wire-click="cloneProcessFlow({{ $pFlow->id }})"
                                label="Duplicate Pipeline Flow"
                            >
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                            </x-entity-card-menu-item>

                            @if(!$pFlow->is_default)
                                <x-entity-card-menu-item 
                                    wire-click="setFlowAsDefault({{ $pFlow->id }})"
                                    label="Make System Default Flow"
                                >
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                </x-entity-card-menu-item>
                            @endif

                            <div class="entity-card-menu-divider"></div>

                            <x-entity-card-menu-item 
                                wire-click="requestDeleteProcessFlow({{ $pFlow->id }})"
                                label="Delete Pipeline"
                                theme="danger"
                            >
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                            </x-entity-card-menu-item>
                        </x-slot:menuItems>
                    </x-entity-card>
                @endforeach
            </div>
        @else
            <!-- Pipeline Design Header Bar -->
            <div class="card mb-6" style="padding: 1.25rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <!-- Back Button & Pipeline Title -->
                    <div style="flex: 1; min-width: 300px;">
                        <button type="button" 
                                class="btn btn-secondary btn-sm" 
                                wire:click="showPipelineList" 
                                style="margin-bottom: 0.5rem; display: inline-flex; align-items: center; gap: 0.4rem; font-weight: 700; font-size: 0.78rem;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                            <span>Back to All Pipelines</span>
                        </button>

                        <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
                            <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--color-text-primary); margin: 0; line-height: 1.25;">
                                {{ $flow_name }}
                            </h2>
                            <button type="button" 
                                    class="btn btn-secondary btn-sm" 
                                    wire:click="openEditPipelineModal({{ $selected_flow_id }})" 
                                    style="padding: 0.3rem 0.65rem; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;"
                                    title="Rename or configure pipeline details">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                <span>Rename / Edit Info</span>
                            </button>
                            <span class="badge" style="background: rgba(99, 102, 241, 0.15); color: var(--primary); font-weight: 800; font-size: 0.72rem;">
                                {{ $flow_sector }}
                            </span>
                            @php
                                $currentFlowRecord = $processFlows->firstWhere('id', $selected_flow_id);
                            @endphp
                            @if($currentFlowRecord?->is_default)
                                <span class="badge badge-amber" style="font-weight: 800; font-size: 0.72rem;">
                                    ★ SYSTEM DEFAULT
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Pipeline Action Buttons -->
                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                        <button type="button" class="btn btn-primary" wire:click="addNewStage" style="font-size: 0.82rem; font-weight: 700; padding: 0.55rem 1.15rem; display: inline-flex; align-items: center; gap: 0.4rem;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            <span>+ Add Pipeline Stage</span>
                        </button>
                        <button type="button" class="btn btn-secondary" wire:click="saveProcessFlow" style="font-size: 0.82rem; font-weight: 700; padding: 0.55rem 1rem; color: #10b981; border-color: rgba(16, 185, 129, 0.3);">
                            💾 Save Pipeline
                        </button>
                        @if($selected_flow_id)
                            <button type="button" class="btn btn-secondary" wire:click="cloneProcessFlow({{ $selected_flow_id }})" style="font-size: 0.8rem; font-weight: 600; padding: 0.55rem 0.85rem;" title="Duplicate Pipeline">
                                📋 Duplicate
                            </button>
                            <button type="button" class="btn btn-secondary" wire:click="requestDeleteProcessFlow({{ $selected_flow_id }})" style="font-size: 0.8rem; font-weight: 600; padding: 0.55rem 0.85rem; color: #ef4444; border-color: rgba(239, 68, 68, 0.3);" title="Delete Pipeline">
                                🗑️ Delete
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Pipeline Description Strip -->
                @if($flow_description)
                    <div style="margin-top: 0.85rem; padding-top: 0.85rem; border-top: 1px solid rgba(255, 255, 255, 0.06); font-size: 0.82rem; color: var(--color-text-secondary); display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: var(--primary); font-weight: 800;">ℹ️ PIPELINE BLUEPRINT:</span>
                        <span>{{ $flow_description }}</span>
                    </div>
                @endif
            </div>

            <!-- Sequential Visual Flow Canvas -->
            <div style="display: flex; flex-direction: column; gap: 1.25rem; margin-bottom: 2.5rem;">
                @forelse($flow_stages as $sIdx => $stage)
                <div class="card" style="border-left: 4px solid {{ !empty($stage['is_enabled']) ? 'var(--primary)' : 'rgba(255, 255, 255, 0.2)' }}; padding: 1.35rem; background: {{ !empty($stage['is_enabled']) ? 'linear-gradient(135deg, rgba(30, 41, 59, 0.6) 0%, rgba(15, 23, 42, 0.8) 100%)' : 'rgba(15, 23, 42, 0.4)' }}; position: relative; transition: border-color 0.2s ease;">
                    
                    <!-- Top Stage Header Row -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap; margin-bottom: 0.75rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                            <!-- Step Number Pill -->
                            <div style="width: 34px; height: 34px; border-radius: 50%; background: {{ !empty($stage['is_enabled']) ? 'var(--primary)' : 'rgba(255, 255, 255, 0.15)' }}; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 0.95rem; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);">
                                {{ $stage['step'] ?? ($sIdx + 1) }}
                            </div>

                            <!-- Stage Title -->
                            <div>
                                <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--color-text-primary); margin: 0; line-height: 1.3;">
                                    {{ $stage['name'] ?? 'Stage ' . ($sIdx + 1) }}
                                </h3>
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.25rem; flex-wrap: wrap;">
                                    <!-- Actor Badge -->
                                    <span class="badge" style="background: rgba(99, 102, 241, 0.18); color: var(--primary); font-weight: 700; font-size: 0.72rem;">
                                        👤 {{ $stage['governing_role'] ?? $stage['actor_type'] ?? 'Authority' }}
                                    </span>

                                    <!-- Committee Type Badge -->
                                    @if(!empty($stage['committee_type']))
                                        <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; font-weight: 700; font-size: 0.7rem;">
                                            🏛️ {{ $stage['committee_type'] }}
                                        </span>
                                    @endif

                                    <!-- SLA Days Badge -->
                                    <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b; font-weight: 700; font-size: 0.7rem;">
                                        ⏱️ {{ $stage['sla_days'] ?? 7 }} Days SLA
                                    </span>

                                    <!-- Enabled/Disabled Badge -->
                                    @if(empty($stage['is_enabled']))
                                        <span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #ef4444; font-size: 0.7rem;">
                                            DISABLED
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Stage Action Buttons -->
                        <div style="display: flex; align-items: center; gap: 0.35rem;">
                            <!-- Move Up -->
                            <button type="button" 
                                class="action-btn action-btn-view" 
                                wire:click="moveStageUp({{ $sIdx }})" 
                                {{ $sIdx === 0 ? 'disabled' : '' }} 
                                data-tooltip="Move Stage Up" 
                                aria-label="Move Up">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="18 15 12 9 6 15"/>
                                </svg>
                            </button>

                            <!-- Move Down -->
                            <button type="button" 
                                class="action-btn action-btn-view" 
                                wire:click="moveStageDown({{ $sIdx }})" 
                                {{ $sIdx === count($flow_stages) - 1 ? 'disabled' : '' }} 
                                data-tooltip="Move Stage Down" 
                                aria-label="Move Down">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"/>
                                </svg>
                            </button>

                            <!-- Toggle Enabled -->
                            <button type="button" 
                                class="action-btn action-btn-edit" 
                                wire:click="toggleStageEnabled({{ $sIdx }})" 
                                data-tooltip="{{ !empty($stage['is_enabled']) ? 'Disable Stage' : 'Enable Stage' }}" 
                                aria-label="Toggle Stage">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>

                            <!-- Configure Modal Trigger -->
                            <button type="button" 
                                class="btn btn-secondary" 
                                wire:click="openEditStageModal({{ $sIdx }})" 
                                style="font-size: 0.78rem; font-weight: 700; padding: 0.35rem 0.8rem; display: inline-flex; align-items: center; gap: 0.3rem;">
                                ✏️ Tune Logic
                            </button>

                            <!-- Remove Step -->
                            <button type="button" 
                                class="action-btn action-btn-delete" 
                                wire:click="removeStage({{ $sIdx }})" 
                                wire:confirm="Are you sure you want to remove this process stage from the pipeline?" 
                                data-tooltip="Delete Stage" 
                                aria-label="Delete Stage">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Rule / Criterion Description -->
                    @if(!empty($stage['rule_description']))
                        <div style="font-size: 0.84rem; color: var(--color-text-secondary); margin-bottom: 1rem; line-height: 1.5; background: rgba(15, 23, 42, 0.4); padding: 0.65rem 0.95rem; border-radius: var(--radius-sm); border: 1px solid rgba(255, 255, 255, 0.04);">
                            <span style="font-weight: 700; color: var(--color-text-primary);">Operational Rule:</span> {{ $stage['rule_description'] }}
                        </div>
                    @endif

                    <!-- Color-Coded Dynamic Logic Grid (Pass / Incomplete / Fail) -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <!-- 🟢 On Pass -->
                        <div style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.25); border-radius: var(--radius-sm); padding: 0.75rem 1rem;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.35rem;">
                                <span style="font-size: 0.72rem; text-transform: uppercase; font-weight: 800; color: #10b981; letter-spacing: 0.04em;">
                                    🟢 ON PASS / QUALIFIED
                                </span>
                                @if(!empty($stage['on_pass']['next_step']))
                                    <span class="badge" style="background: rgba(16, 185, 129, 0.2); color: #10b981; font-size: 0.68rem; font-weight: 700;">
                                        ➔ Step {{ $stage['on_pass']['next_step'] }}
                                    </span>
                                @endif
                            </div>
                            <div style="font-size: 0.8rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.2rem;">
                                Status: <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; font-size: 0.72rem;">{{ $stage['on_pass']['target_status'] ?? 'APPROVED' }}</span>
                            </div>
                            <div style="font-size: 0.75rem; color: var(--color-text-secondary); line-height: 1.4;">
                                {{ $stage['on_pass']['label'] ?? 'Candidate passes verification and advances to next stage.' }}
                            </div>
                        </div>

                        <!-- 🟡 On Incomplete (Deficiency Alert) -->
                        <div style="background: {{ !empty($stage['on_incomplete']) ? 'rgba(245, 158, 11, 0.08)' : 'rgba(255, 255, 255, 0.02)' }}; border: 1px solid {{ !empty($stage['on_incomplete']) ? 'rgba(245, 158, 11, 0.25)' : 'rgba(255, 255, 255, 0.05)' }}; border-radius: var(--radius-sm); padding: 0.75rem 1rem;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.35rem;">
                                <span style="font-size: 0.72rem; text-transform: uppercase; font-weight: 800; color: {{ !empty($stage['on_incomplete']) ? '#f59e0b' : 'var(--color-text-muted)' }}; letter-spacing: 0.04em;">
                                    🟡 ON INCOMPLETE / DEFICIENCY
                                </span>
                                @if(!empty($stage['on_incomplete']['grace_days']))
                                    <span class="badge" style="background: rgba(245, 158, 11, 0.2); color: #f59e0b; font-size: 0.68rem; font-weight: 700;">
                                        ⏱️ {{ $stage['on_incomplete']['grace_days'] }}d Grace
                                    </span>
                                @endif
                            </div>
                            @if(!empty($stage['on_incomplete']))
                                <div style="font-size: 0.8rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.2rem;">
                                    Status: <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b; font-size: 0.72rem;">{{ $stage['on_incomplete']['target_status'] ?? 'INCOMPLETE_DOSSIER' }}</span>
                                </div>
                                <div style="font-size: 0.75rem; color: var(--color-text-secondary); line-height: 1.4;">
                                    {{ $stage['on_incomplete']['label'] ?? 'Deficiency alerted to candidate for upload.' }}
                                </div>
                            @else
                                <div style="font-size: 0.75rem; color: var(--color-text-muted); font-style: italic; padding-top: 0.5rem;">
                                    No intermediate deficiency branch (Direct Pass / Fail).
                                </div>
                            @endif
                        </div>

                        <!-- 🔴 On Fail / Rejection -->
                        <div style="background: {{ !empty($stage['on_fail']) ? 'rgba(239, 68, 68, 0.08)' : 'rgba(255, 255, 255, 0.02)' }}; border: 1px solid {{ !empty($stage['on_fail']) ? 'rgba(239, 68, 68, 0.25)' : 'rgba(255, 255, 255, 0.05)' }}; border-radius: var(--radius-sm); padding: 0.75rem 1rem;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.35rem;">
                                <span style="font-size: 0.72rem; text-transform: uppercase; font-weight: 800; color: {{ !empty($stage['on_fail']) ? '#ef4444' : 'var(--color-text-muted)' }}; letter-spacing: 0.04em;">
                                    🔴 ON FAIL / DISQUALIFIED
                                </span>
                                @if(!empty($stage['on_fail']['allow_appeal']))
                                    <span class="badge" style="background: rgba(139, 92, 246, 0.2); color: #8b5cf6; font-size: 0.68rem; font-weight: 700;">
                                        ⚖️ Appellate Right
                                    </span>
                                @endif
                            </div>
                            @if(!empty($stage['on_fail']))
                                <div style="font-size: 0.8rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.2rem;">
                                    Status: <span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #ef4444; font-size: 0.72rem;">{{ $stage['on_fail']['target_status'] ?? 'DISQUALIFIED' }}</span>
                                </div>
                                <div style="font-size: 0.75rem; color: var(--color-text-secondary); line-height: 1.4;">
                                    {{ $stage['on_fail']['label'] ?? 'Candidate is marked disqualified or not selected.' }}
                                </div>
                            @else
                                <div style="font-size: 0.75rem; color: var(--color-text-muted); font-style: italic; padding-top: 0.5rem;">
                                    Final stage or no failure outcome specified.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Downward Arrow Between Stages -->
                @if($sIdx < count($flow_stages) - 1)
                    <div style="display: flex; justify-content: center; align-items: center; margin: -0.5rem 0;">
                        <div style="width: 28px; height: 28px; border-radius: 50%; background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.3); color: var(--primary); display: flex; align-items: center; justify-content: center;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <polyline points="19 12 12 19 5 12"/>
                            </svg>
                        </div>
                    </div>
                @endif
            @empty
                <div class="card" style="text-align: center; padding: 3rem 1.5rem;">
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🔄</div>
                    <h4 style="font-size: 1.1rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                        No Process Stages Configured
                    </h4>
                    <p style="font-size: 0.85rem; color: var(--color-text-secondary); margin-bottom: 1.25rem;">
                        Build your recruitment process pipeline stages from applicant submission to final appointment.
                    </p>
                    <button type="button" class="btn btn-primary" wire:click="addNewStage" style="padding: 0.6rem 1.25rem; font-size: 0.85rem; font-weight: 700;">
                        + Add First Stage
                    </button>
                </div>
            @endforelse
        </div>
        @endif
    @endif

    <!-- ========================================================================= -->
    <!-- TAB 3: CAMPAIGN CONSTITUTION & QUOTA TUNING                               -->
    <!-- ========================================================================= -->
    @if($activeTab === 'campaign_constitution')
        <!-- Campaign Selector & Master Board Loader -->
        <div class="card mb-6" style="padding: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 280px;">
                    <label style="display: block; font-size: 0.74rem; text-transform: uppercase; font-weight: 700; color: var(--color-text-muted); margin-bottom: 0.35rem; letter-spacing: 0.05em;">
                        TARGET JOB CAMPAIGN
                    </label>
                    <select class="form-control form-select" wire:model.live="selected_campaign_id" style="font-weight: 600; font-size: 0.9rem;">
                        @foreach($campaigns as $camp)
                            <option value="{{ $camp->id }}">
                                {{ $camp->title }} ({{ $camp->job_code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="flex: 1; min-width: 280px;">
                    <label style="display: block; font-size: 0.74rem; text-transform: uppercase; font-weight: 700; color: var(--color-text-muted); margin-bottom: 0.35rem; letter-spacing: 0.05em;">
                        APPLY REGISTERED STATUTORY BOARD CONSTITUTION
                    </label>
                    <select class="form-control form-select" wire:change="applyBoardConstitutionToCampaign($event.target.value)" style="font-weight: 600; font-size: 0.9rem;">
                        <option value="">-- Choose Registered Statutory Board --</option>
                        @foreach($registeredBoards as $b)
                            <option value="{{ $b->id }}">{{ $b->name }} [{{ $b->organization_sector }} - {{ $b->required_total }} Members]</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Quick Statutory Presets Bar -->
        <div class="card mb-6" style="padding: 1rem 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                <span style="font-size: 0.75rem; text-transform: uppercase; font-weight: 800; color: var(--color-text-muted); letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.4rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                    </svg>
                    <span>ONE-CLICK STATUTORY PRESET TEMPLATES</span>
                </span>
                <span style="font-size: 0.75rem; color: var(--color-text-muted);">
                    Clicking a preset will populate dynamic criteria rows below
                </span>
            </div>

            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                <button type="button" class="btn btn-secondary" style="padding: 0.45rem 0.85rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.4rem; border-color: rgba(99, 102, 241, 0.3);" wire:click="applyPreset('asst_prof')">
                    <span>🎓 Assistant Professor (6 Members • 3 Reviewers)</span>
                </button>
                <button type="button" class="btn btn-secondary" style="padding: 0.45rem 0.85rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.4rem; border-color: rgba(139, 92, 246, 0.3);" wire:click="applyPreset('academic_prof')">
                    <span>🏛️ Professorial Board (6 Members • 1 Overseas)</span>
                </button>
                <button type="button" class="btn btn-secondary" style="padding: 0.45rem 0.85rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.4rem;" wire:click="applyPreset('lecturer')">
                    <span>📖 Lecturer / Teaching (5 Members)</span>
                </button>
                <button type="button" class="btn btn-secondary" style="padding: 0.45rem 0.85rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.4rem;" wire:click="applyPreset('corporate_board')">
                    <span>🏢 Corporate Executive (5 Members)</span>
                </button>
                <button type="button" class="btn btn-secondary" style="padding: 0.45rem 0.85rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.4rem;" wire:click="applyPreset('public_sector')">
                    <span>🏛️ Public Sector / Civil Service (4 Members)</span>
                </button>
                <button type="button" class="btn btn-secondary" style="padding: 0.45rem 0.85rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.4rem;" wire:click="applyPreset('expert_heavy')">
                    <span>🔬 External-Led Expert (5 Members)</span>
                </button>
                <button type="button" class="btn btn-secondary" style="padding: 0.45rem 0.85rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.4rem; border-style: dashed;" wire:click="applyPreset('blank')">
                    <span>⚙️ Blank Custom</span>
                </button>
            </div>
        </div>

        <!-- Campaign Quota Customization Card -->
        <div class="card mb-6" style="padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--color-text-primary); margin: 0 0 0.25rem 0;">
                        {{ $constitution_title }}
                    </h3>
                    <p style="font-size: 0.82rem; color: var(--color-text-secondary); margin: 0;">
                        {{ $constitution_description ?: 'Statutory member quota breakdown for live committee vetting and viva.' }}
                    </p>
                </div>

                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <button type="button" class="btn btn-secondary" wire:click="addRule" style="font-size: 0.8rem; font-weight: 700; padding: 0.45rem 0.9rem;">
                        + Add Member Seat
                    </button>
                </div>
            </div>

            <!-- Rules Table -->
            <div class="table-responsive">
                <table class="table" style="font-size: 0.84rem; margin: 0;">
                    <thead style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.04em;">
                        <tr>
                            <th style="width: 35%;">MEMBER SEAT / ROLE</th>
                            <th style="width: 15%;">SEATS</th>
                            <th style="width: 25%;">ORIGIN CATEGORY</th>
                            <th style="width: 15%;">CADRE</th>
                            <th style="width: 10%; text-align: right;">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rules as $rIdx => $rule)
                            <tr>
                                <td>
                                    <input type="text" class="form-control" wire:model.defer="rules.{{ $rIdx }}.name" placeholder="Role Title" style="font-size: 0.84rem;" required />
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.35rem;">
                                        <button type="button" class="btn btn-secondary" wire:click="updateRuleCount({{ $rIdx }}, -1)" style="padding: 0.2rem 0.45rem; font-size: 0.75rem;">-</button>
                                        <input type="number" min="1" max="50" class="form-control" wire:model.defer="rules.{{ $rIdx }}.count" style="width: 55px; text-align: center; font-size: 0.84rem;" required />
                                        <button type="button" class="btn btn-secondary" wire:click="updateRuleCount({{ $rIdx }}, 1)" style="padding: 0.2rem 0.45rem; font-size: 0.75rem;">+</button>
                                    </div>
                                </td>
                                <td>
                                    <select class="form-control form-select" wire:model.defer="rules.{{ $rIdx }}.category" style="font-size: 0.8rem;">
                                        <option value="INTERNAL">INTERNAL (Home Inst.)</option>
                                        <option value="EXTERNAL">EXTERNAL (Outside Inst.)</option>
                                        <option value="INTERNATIONAL">INTERNATIONAL (Overseas)</option>
                                        <option value="INDEPENDENT">INDEPENDENT (Arbiter)</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control form-select" wire:model.defer="rules.{{ $rIdx }}.cadre" style="font-size: 0.8rem;">
                                        <option value="FACULTY">Faculty</option>
                                        <option value="OFFICER">Officer</option>
                                        <option value="EXPERT">Expert</option>
                                        <option value="EXECUTIVE">Executive</option>
                                        <option value="LEGAL">Legal</option>
                                    </select>
                                </td>
                                <td style="text-align: right;">
                                    <button type="button" class="btn btn-secondary" wire:click="removeRule({{ $rIdx }})" style="padding: 0.25rem 0.5rem; color: #ef4444;">✕</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Footer Quorum Enforcement & Save -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; flex-wrap: wrap; gap: 1rem; border-top: 1px solid rgba(255, 255, 255, 0.06); padding-top: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                    <input type="checkbox" id="enforce_strict" wire:model.defer="enforce_strict_quota" style="width: 16px; height: 16px;" />
                    <label for="enforce_strict" style="font-size: 0.85rem; font-weight: 600; cursor: pointer; color: var(--color-text-primary);">
                        Strict Quota Enforcement: Prevent committee approval if slot quotas are under or over allocated
                    </label>
                </div>

                <div style="display: flex; gap: 0.5rem;">
                    <button type="button" class="btn btn-secondary" wire:click="saveAsSystemDefault" style="padding: 0.6rem 1rem; font-size: 0.82rem;">
                        Save as System Default
                    </button>
                    <button type="button" class="btn btn-primary" wire:click="saveConstitution" style="padding: 0.6rem 1.5rem; font-size: 0.88rem; font-weight: 700;">
                        Save & Apply to Campaign
                    </button>
                </div>
            </div>
        </div>

        <!-- Active Campaign Live Panel Quorum Inspection -->
        @if($currentCampaign && $metrics)
            <div class="card mt-6" style="padding: 1.25rem;">
                <div style="border-bottom: 1px solid rgba(255, 255, 255, 0.06); padding-bottom: 0.75rem; margin-bottom: 1.25rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                    <div>
                        <h4 style="font-size: 1rem; font-weight: 800; color: var(--color-text-primary); margin: 0 0 0.15rem 0;">
                            Live Committee Quorum Status on {{ $currentCampaign->title }}
                        </h4>
                        <p style="font-size: 0.8rem; color: var(--color-text-secondary); margin: 0;">
                            Tracks actual appointed operational committee members against the prescribed criteria
                        </p>
                    </div>

                    @if(!empty($metrics['is_complete']))
                        <span class="badge" style="background: rgba(16, 185, 129, 0.2); color: var(--success); border: 1px solid rgba(16, 185, 129, 0.4); padding: 0.4rem 0.8rem; font-size: 0.82rem; font-weight: 700;">
                            ✓ Statutory Quorum Satisfied ({{ $metrics['total_assigned'] ?? 0 }} / {{ $metrics['required_total'] ?? 0 }})
                        </span>
                    @else
                        <span class="badge" style="background: rgba(245, 158, 11, 0.2); color: var(--warning); border: 1px solid rgba(245, 158, 11, 0.4); padding: 0.4rem 0.8rem; font-size: 0.82rem; font-weight: 700;">
                            ⏳ In Progress: {{ $metrics['total_assigned'] ?? 0 }} of {{ $metrics['required_total'] ?? 0 }} Members Assigned
                        </span>
                    @endif
                </div>

                <!-- Categories Tally Grid -->
                @if(!empty($metrics['categories']))
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0.85rem;">
                        @foreach($metrics['categories'] as $catKey => $catData)
                            <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.06); border-radius: var(--radius-sm); padding: 0.85rem 1rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--color-text-secondary);">
                                        {{ $catData['label'] }}
                                    </span>
                                    @if(!empty($catData['satisfied']))
                                        <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: var(--success); font-size: 0.7rem; font-weight: 700;">✓ Quota Met</span>
                                    @else
                                        <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: var(--warning); font-size: 0.7rem; font-weight: 700;">{{ $catData['available'] ?? 0 }} Needed</span>
                                    @endif
                                </div>
                                <div style="display: flex; align-items: baseline; gap: 0.4rem;">
                                    <span style="font-size: 1.35rem; font-weight: 800; font-family: monospace; color: var(--color-text-primary);">
                                        {{ $catData['assigned'] }} / {{ $catData['required'] }}
                                    </span>
                                    <span style="font-size: 0.76rem; color: var(--color-text-muted);">Allocated</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 1: ADD / EDIT STATUTORY BOARD & GENERAL COMMITTEE                   -->
    <!-- ========================================================================= -->
    @if($showBoardModal)
        <div style="position: fixed; inset: 0; background: rgba(0, 0, 0, 0.8); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 1.5rem;">
            <div class="card" style="width: 100%; max-width: 900px; max-height: 90vh; overflow-y: auto; background: #0f172a; border-color: rgba(99, 102, 241, 0.35); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); padding: 1.75rem;">
                
                <!-- Modal Header -->
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.3rem;">
                            <span class="badge" style="background: rgba(99, 102, 241, 0.2); color: var(--primary); font-size: 0.72rem; font-weight: 800;">
                                STATUTORY GOVERNANCE ENGINE
                            </span>
                            <span class="badge" style="background: rgba(255, 255, 255, 0.06); color: var(--color-text-secondary); font-size: 0.72rem;">
                                {{ $isEditBoard ? 'EDITING COMMITTEE' : 'NEW STANDING COMMITTEE' }}
                            </span>
                        </div>
                        <h3 style="font-size: 1.35rem; font-weight: 800; color: var(--color-text-primary); margin: 0;">
                            {{ $isEditBoard ? 'Configure Statutory Board & Committee' : 'Empanel General Standing Committee / Board' }}
                        </h3>
                    </div>
                    <button type="button" wire:click="closeBoardModal" style="background: none; border: none; color: var(--color-text-muted); cursor: pointer; font-size: 1.25rem;">✕</button>
                </div>

                <!-- Form Fields -->
                <form wire:submit.prevent="saveBoardModel">
                    <!-- General Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="form-label" style="font-size: 0.78rem; font-weight: 700;">COMMITTEE / BOARD NAME</label>
                            <input type="text" class="form-control" wire:model.defer="board_name" placeholder="e.g. Standing Scrutiny Committee, Selection Board..." required />
                            @error('board_name') <span class="text-danger" style="font-size: 0.72rem;">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="form-label" style="font-size: 0.78rem; font-weight: 700;">COMMITTEE STATUTORY TYPE</label>
                            <select class="form-control form-select" wire:model.defer="board_type">
                                <option value="SCRUTINY">📑 Scrutiny Committee (Dossier Vetting)</option>
                                <option value="EXPERT_REVIEW">🔬 Expert Referees Panel (Academic Peer Review)</option>
                                <option value="SELECTION_BOARD">👥 Selection Board (Live Interview & Viva)</option>
                                <option value="APPELLATE">⚖️ Appellate & Grievance Review Committee</option>
                                <option value="EXECUTIVE">🏛️ Syndicate / Executive Governing Council</option>
                                <option value="CUSTOM">⚙️ Custom Standing Board / Panel</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="form-label" style="font-size: 0.78rem; font-weight: 700;">ORGANIZATION SECTOR</label>
                            <select class="form-control form-select" wire:model.defer="board_sector">
                                <option value="ACADEMIC">🎓 Higher Education / Academic</option>
                                <option value="CORPORATE">🏢 Corporate & Enterprise</option>
                                <option value="HEALTHCARE">🩺 Healthcare & Medical</option>
                                <option value="PUBLIC_SECTOR">🏛️ Public Sector & Civil Service</option>
                                <option value="JUDICIAL">⚖️ Judicial & Legal</option>
                                <option value="DEFENSE">🛡️ Security & Defense</option>
                                <option value="GENERAL">🌐 General / Cross-Sector</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" style="font-size: 0.78rem; font-weight: 700;">RESPONSIBLE RECRUITMENT STAGE</label>
                            <select class="form-control form-select" wire:model.defer="board_responsible_stage">
                                <option value="stage_scrutiny">Step 2: Scrutiny Dossier Vetting</option>
                                <option value="stage_peer_review">Step 3: Academic Peer Review</option>
                                <option value="stage_selection_board">Step 4: Statutory Selection Board Interview</option>
                                <option value="stage_syndicate">Step 5: Syndicate / Council Sanction</option>
                                <option value="stage_appellate">Appellate / Grievance Branch</option>
                                <option value="stage_custom">Custom Operational Stage</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-size: 0.78rem; font-weight: 700;">DESCRIPTION & STATUTORY MANDATE</label>
                        <textarea class="form-control" rows="2" wire:model.defer="board_description" placeholder="Statutory citation, terms of reference, and legal mandate..."></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                        <div>
                            <label class="form-label" style="font-size: 0.78rem; font-weight: 700;">EXTERNAL PEER REVIEWERS REQUIRED</label>
                            <input type="number" min="0" max="20" class="form-control" wire:model.defer="board_required_peer_reviewers" />
                            <span style="font-size: 0.72rem; color: var(--color-text-muted);">Set to 0 if this committee does not require external referee review.</span>
                        </div>
                        <div style="display: flex; align-items: center; padding-top: 1.5rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.82rem; font-weight: 600;">
                                <input type="checkbox" wire:model.defer="board_enforce_strict_quota" style="width: 16px; height: 16px;" />
                                <span>Strict Quorum Enforcement (Lock meeting if statutory quorum unmet)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Quorum Roles Breakdown Repeater -->
                    <div style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: var(--radius-sm); padding: 1.25rem; margin-bottom: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem;">
                            <div>
                                <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--color-text-primary); margin: 0;">
                                    Statutory Quorum Members & Cadres
                                </h4>
                                <span style="font-size: 0.72rem; color: var(--color-text-muted);">Define member seats, categories, and mandatory roles.</span>
                            </div>
                            <button type="button" class="btn btn-secondary" wire:click="addBoardRole" style="padding: 0.35rem 0.75rem; font-size: 0.78rem; font-weight: 700;">
                                + Add Member Slot
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table" style="margin: 0; font-size: 0.82rem;">
                                <thead style="font-size: 0.7rem; text-transform: uppercase;">
                                    <tr>
                                        <th style="width: 35%;">ROLE / TITLE</th>
                                        <th style="width: 15%;">SEATS</th>
                                        <th style="width: 25%;">ORIGIN CATEGORY</th>
                                        <th style="width: 15%;">CADRE</th>
                                        <th style="width: 10%; text-align: right;">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($board_roles as $rIdx => $bRole)
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control" wire:model.defer="board_roles.{{ $rIdx }}.name" placeholder="e.g. Panel Convener, External Expert" style="font-size: 0.82rem;" required />
                                            </td>
                                            <td>
                                                <input type="number" min="1" max="50" class="form-control" wire:model.defer="board_roles.{{ $rIdx }}.count" style="font-size: 0.82rem;" required />
                                            </td>
                                            <td>
                                                <select class="form-control form-select" wire:model.defer="board_roles.{{ $rIdx }}.category" style="font-size: 0.8rem;">
                                                    <option value="INTERNAL">INTERNAL (Home Inst.)</option>
                                                    <option value="EXTERNAL">EXTERNAL (Outside Inst.)</option>
                                                    <option value="INTERNATIONAL">INTERNATIONAL (Overseas)</option>
                                                    <option value="INDEPENDENT">INDEPENDENT (Arbiter)</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control form-select" wire:model.defer="board_roles.{{ $rIdx }}.cadre" style="font-size: 0.8rem;">
                                                    <option value="FACULTY">Faculty</option>
                                                    <option value="OFFICER">Officer</option>
                                                    <option value="EXPERT">Expert</option>
                                                    <option value="EXECUTIVE">Executive</option>
                                                    <option value="LEGAL">Legal</option>
                                                </select>
                                            </td>
                                            <td style="text-align: right;">
                                                <button type="button" class="btn btn-secondary" wire:click="removeBoardRole({{ $rIdx }})" style="padding: 0.25rem 0.5rem; color: #ef4444;">✕</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 1rem; border-top: 1px solid rgba(255, 255, 255, 0.08);">
                        <button type="button" class="btn btn-secondary" wire:click="closeBoardModal" style="padding: 0.6rem 1.25rem; font-size: 0.85rem;">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.75rem; font-size: 0.88rem; font-weight: 700;">
                            {{ $isEditBoard ? 'Update Statutory Board' : 'Save & Empanel Board' }}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 2: STAGE CONFIGURATION (DYNAMIC RECRUITMENT FLOW)                   -->
    <!-- ========================================================================= -->
    @if($showStageModal)
        <div style="position: fixed; inset: 0; background: rgba(0, 0, 0, 0.8); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 1.5rem;">
            <div class="card" style="width: 100%; max-width: 850px; max-height: 90vh; overflow-y: auto; background: #0f172a; border-color: rgba(99, 102, 241, 0.35); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); padding: 1.75rem;">
                
                <!-- Modal Header -->
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.3rem;">
                            <span class="badge" style="background: rgba(99, 102, 241, 0.2); color: var(--primary); font-size: 0.72rem; font-weight: 800;">
                                DYNAMIC PROCESS FLOW BUILDER
                            </span>
                            <span class="badge" style="background: rgba(255, 255, 255, 0.06); color: var(--color-text-secondary); font-size: 0.72rem;">
                                {{ $isNewStage ? 'NEW PIPELINE STAGE' : 'STAGE ' . (($editingStageIndex ?? 0) + 1) }}
                            </span>
                        </div>
                        <h3 style="font-size: 1.35rem; font-weight: 800; color: var(--color-text-primary); margin: 0;">
                            Configure Recruitment Stage & Decision Logic
                        </h3>
                    </div>
                    <button type="button" wire:click="closeStageModal" style="background: none; border: none; color: var(--color-text-muted); cursor: pointer; font-size: 1.25rem;">✕</button>
                </div>

                <!-- Form -->
                <form wire:submit.prevent="saveStage">
                    <!-- Stage Identity -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="form-label" style="font-size: 0.78rem; font-weight: 700;">STAGE NAME</label>
                            <input type="text" class="form-control" wire:model.defer="stage_name" placeholder="e.g. Scrutiny Dossier Vetting, Peer Review..." required />
                        </div>
                        <div>
                            <label class="form-label" style="font-size: 0.78rem; font-weight: 700;">RESPONSIBLE ROLE / ENTITY</label>
                            <input type="text" class="form-control" wire:model.defer="stage_governing_role" placeholder="e.g. Scrutiny Committee, External Referees..." required />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="form-label" style="font-size: 0.78rem; font-weight: 700;">COMMITTEE TYPE</label>
                            <select class="form-control form-select" wire:model.defer="stage_committee_type">
                                <option value="SCRUTINY">Scrutiny Committee</option>
                                <option value="EXPERT_REVIEW">Expert Referees Panel</option>
                                <option value="SELECTION_BOARD">Selection Board</option>
                                <option value="EXECUTIVE">Executive / Syndicate</option>
                                <option value="APPELLATE">Appellate Tribunal</option>
                                <option value="CUSTOM">Custom Entity</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" style="font-size: 0.78rem; font-weight: 700;">SLA DURATION (DAYS)</label>
                            <input type="number" min="1" max="120" class="form-control" wire:model.defer="stage_sla_days" required />
                        </div>
                        <div style="display: flex; align-items: center; padding-top: 1.5rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.82rem; font-weight: 600;">
                                <input type="checkbox" wire:model.defer="stage_is_enabled" style="width: 16px; height: 16px;" />
                                <span>Stage Active in Pipeline</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-size: 0.78rem; font-weight: 700;">CRITERIA & VERIFICATION RULE DESCRIPTION</label>
                        <textarea class="form-control" rows="2" wire:model.defer="stage_rule_description" placeholder="Instructions, criteria, and requirements evaluated in this stage..."></textarea>
                    </div>

                    <!-- Decision Logic Sections -->
                    <div style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: var(--radius-sm); padding: 1.25rem; margin-bottom: 1.5rem;">
                        <h4 style="font-size: 0.95rem; font-weight: 800; color: var(--color-text-primary); margin: 0 0 1rem 0;">
                            Dynamic Decision Logic & Transition Outcomes
                        </h4>

                        <!-- 🟢 On Pass -->
                        <div style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.25); border-radius: var(--radius-sm); padding: 1rem; margin-bottom: 1rem;">
                            <div style="font-weight: 800; color: #10b981; font-size: 0.82rem; margin-bottom: 0.65rem;">
                                🟢 ON PASS OUTCOME (CANDIDATE QUALIFIED)
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-2">
                                <div>
                                    <label class="form-label" style="font-size: 0.74rem;">TARGET WORKFLOW STATUS</label>
                                    <select class="form-control form-select" wire:model.defer="stage_on_pass_target_status" style="font-size: 0.82rem;">
                                        @foreach($workflowStatuses as $wStatus)
                                            <option value="{{ $wStatus->code }}">{{ $wStatus->label }} ({{ $wStatus->code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label" style="font-size: 0.74rem;">NEXT PIPELINE STEP</label>
                                    <input type="number" min="1" max="20" class="form-control" wire:model.defer="stage_on_pass_next_step" placeholder="Next step number" style="font-size: 0.82rem;" />
                                </div>
                            </div>
                            <div>
                                <label class="form-label" style="font-size: 0.74rem;">TRANSITION LABEL / NOTIFICATION</label>
                                <input type="text" class="form-control" wire:model.defer="stage_on_pass_label" placeholder="e.g. Scrutiny Cleared ➔ Advance to Peer Review" style="font-size: 0.82rem;" />
                            </div>
                        </div>

                        <!-- 🟡 On Incomplete -->
                        <div style="background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.25); border-radius: var(--radius-sm); padding: 1rem; margin-bottom: 1rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.65rem;">
                                <div style="font-weight: 800; color: #f59e0b; font-size: 0.82rem;">
                                    🟡 ON INCOMPLETE OUTCOME (DEFICIENCY / MISSING DOCUMENTS)
                                </div>
                                <label style="display: flex; align-items: center; gap: 0.4rem; cursor: pointer; font-size: 0.78rem; font-weight: 700;">
                                    <input type="checkbox" wire:model.live="stage_has_incomplete" style="width: 15px; height: 15px;" />
                                    <span>Enable Deficiency Branch</span>
                                </label>
                            </div>

                            @if($stage_has_incomplete)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-2">
                                    <div>
                                        <label class="form-label" style="font-size: 0.74rem;">TARGET WORKFLOW STATUS</label>
                                        <select class="form-control form-select" wire:model.defer="stage_on_incomplete_target_status" style="font-size: 0.82rem;">
                                            @foreach($workflowStatuses as $wStatus)
                                                <option value="{{ $wStatus->code }}">{{ $wStatus->label }} ({{ $wStatus->code }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="form-label" style="font-size: 0.74rem;">DEFICIENCY GRACE PERIOD (DAYS)</label>
                                        <input type="number" min="1" max="30" class="form-control" wire:model.defer="stage_on_incomplete_grace_days" style="font-size: 0.82rem;" />
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label" style="font-size: 0.74rem;">DEFICIENCY NOTIFICATION MESSAGE</label>
                                    <input type="text" class="form-control" wire:model.defer="stage_on_incomplete_label" placeholder="e.g. Missing HEC Equivalence ➔ 5-day grace to re-upload" style="font-size: 0.82rem;" />
                                </div>
                            @endif
                        </div>

                        <!-- 🔴 On Fail -->
                        <div style="background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.25); border-radius: var(--radius-sm); padding: 1rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.65rem;">
                                <div style="font-weight: 800; color: #ef4444; font-size: 0.82rem;">
                                    🔴 ON FAIL OUTCOME (DISQUALIFICATION / REJECTION)
                                </div>
                                <label style="display: flex; align-items: center; gap: 0.4rem; cursor: pointer; font-size: 0.78rem; font-weight: 700;">
                                    <input type="checkbox" wire:model.live="stage_has_fail" style="width: 15px; height: 15px;" />
                                    <span>Enable Fail Branch</span>
                                </label>
                            </div>

                            @if($stage_has_fail)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-2">
                                    <div>
                                        <label class="form-label" style="font-size: 0.74rem;">TARGET WORKFLOW STATUS</label>
                                        <select class="form-control form-select" wire:model.defer="stage_on_fail_target_status" style="font-size: 0.82rem;">
                                            @foreach($workflowStatuses as $wStatus)
                                                <option value="{{ $wStatus->code }}">{{ $wStatus->label }} ({{ $wStatus->code }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div style="display: flex; flex-direction: column; gap: 0.4rem; justify-content: center; padding-top: 0.5rem;">
                                        <label style="display: flex; align-items: center; gap: 0.45rem; cursor: pointer; font-size: 0.78rem; font-weight: 600;">
                                            <input type="checkbox" wire:model.defer="stage_on_fail_allow_appeal" style="width: 15px; height: 15px;" />
                                            <span>Allow Candidate Right to Appeal</span>
                                        </label>
                                        <label style="display: flex; align-items: center; gap: 0.45rem; cursor: pointer; font-size: 0.78rem; font-weight: 600;">
                                            <input type="checkbox" wire:model.defer="stage_on_fail_require_justification" style="width: 15px; height: 15px;" />
                                            <span>Require Written Justification</span>
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label" style="font-size: 0.74rem;">FAIL NOTICE / JUSTIFICATION SUMMARY</label>
                                    <input type="text" class="form-control" wire:model.defer="stage_on_fail_label" placeholder="e.g. Ineligible Criteria ➔ Disqualified (Appellate Right Open)" style="font-size: 0.82rem;" />
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 1rem; border-top: 1px solid rgba(255, 255, 255, 0.08);">
                        <button type="button" class="btn btn-secondary" wire:click="closeStageModal" style="padding: 0.6rem 1.25rem; font-size: 0.85rem;">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.75rem; font-size: 0.88rem; font-weight: 700;">
                            Save Process Stage
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL 3: QUICK QUORUM VIEWER MODAL                                        -->
    <!-- ========================================================================= -->
    @if($showQuorumModal && $viewingBoard)
        <div style="position: fixed; inset: 0; background: rgba(0, 0, 0, 0.8); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 1.5rem;">
            <div class="card" style="width: 100%; max-width: 700px; max-height: 90vh; overflow-y: auto; background: #0f172a; border-color: rgba(99, 102, 241, 0.35); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); padding: 1.75rem;">
                
                <!-- Modal Header -->
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.3rem;">
                            <span class="badge" style="background: rgba(99, 102, 241, 0.2); color: var(--primary); font-size: 0.72rem; font-weight: 800;">
                                STATUTORY QUORUM SPECIFICATION
                            </span>
                            <span class="badge" style="background: rgba(255, 255, 255, 0.06); color: var(--color-text-secondary); font-size: 0.72rem;">
                                {{ $viewingBoard->organization_sector }}
                            </span>
                        </div>
                        <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary); margin: 0;">
                            {{ $viewingBoard->name }}
                        </h3>
                    </div>
                    <button type="button" wire:click="closeQuorumModal" style="background: none; border: none; color: var(--color-text-muted); cursor: pointer; font-size: 1.25rem;">✕</button>
                </div>

                <!-- Total Quorum Banner -->
                <div style="display: flex; justify-content: space-around; background: rgba(15, 23, 42, 0.6); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid rgba(255, 255, 255, 0.06); margin-bottom: 1.25rem; text-align: center;">
                    <div>
                        <div style="font-size: 1.4rem; font-weight: 900; color: var(--primary);">{{ $viewingBoard->required_total }}</div>
                        <div style="font-size: 0.72rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">Total Quorum</div>
                    </div>
                    <div>
                        <div style="font-size: 1.4rem; font-weight: 900; color: #3b82f6;">{{ $viewingBoard->required_internal }}</div>
                        <div style="font-size: 0.72rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">Internal</div>
                    </div>
                    <div>
                        <div style="font-size: 1.4rem; font-weight: 900; color: #10b981;">{{ $viewingBoard->required_external }}</div>
                        <div style="font-size: 0.72rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">External</div>
                    </div>
                    <div>
                        <div style="font-size: 1.4rem; font-weight: 900; color: #ec4899;">{{ $viewingBoard->required_peer_reviewers }}</div>
                        <div style="font-size: 0.72rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">Peer Referees</div>
                    </div>
                </div>

                <!-- Members Breakdown Table -->
                <div class="table-responsive mb-4">
                    <table class="table" style="font-size: 0.82rem; margin: 0;">
                        <thead style="font-size: 0.7rem; text-transform: uppercase;">
                            <tr>
                                <th>Role / Seat Title</th>
                                <th style="text-align: center;">Seats</th>
                                <th>Origin</th>
                                <th>Cadre</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($viewingBoard->roles_breakdown ?? [] as $vRole)
                                <tr>
                                    <td style="font-weight: 700; color: var(--color-text-primary);">
                                        {{ $vRole['name'] ?? 'Committee Member' }}
                                        @if(!empty($vRole['is_mandatory']))
                                            <span style="font-size: 0.68rem; color: #f59e0b; margin-left: 0.3rem;">★ Mandatory</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center; font-weight: 800; color: var(--primary);">
                                        {{ $vRole['count'] ?? 1 }}
                                    </td>
                                    <td>
                                        <span class="badge" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6; font-size: 0.7rem;">
                                            {{ $vRole['category'] ?? 'INTERNAL' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge" style="background: rgba(255, 255, 255, 0.08); color: var(--color-text-secondary); font-size: 0.7rem;">
                                            {{ $vRole['cadre'] ?? 'OFFICER' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align: center; color: var(--color-text-muted); padding: 1.5rem;">
                                        No explicit member seats configured.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Close Button -->
                <div style="display: flex; justify-content: flex-end; padding-top: 0.85rem; border-top: 1px solid rgba(255, 255, 255, 0.06);">
                    <button type="button" class="btn btn-secondary" wire:click="closeQuorumModal" style="padding: 0.5rem 1.25rem; font-size: 0.82rem;">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL: EDIT RECRUITMENT PIPELINE INFO & RENAME                 -->
    <!-- ============================================================== -->
    @if($showEditPipelineModal)
        <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.75); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 1.5rem;"
             wire:keydown.escape="closeEditPipelineModal">
            <div class="card" style="width: 100%; max-width: 580px; max-height: 90vh; overflow-y: auto; padding: 1.75rem; border: 1px solid rgba(255, 255, 255, 0.12); box-shadow: 0 20px 40px rgba(0,0,0,0.6);"
                 @click.outside="$wire.closeEditPipelineModal()">
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                    <div>
                        <span style="font-size: 0.72rem; text-transform: uppercase; font-weight: 800; color: var(--primary); letter-spacing: 0.05em;">
                            PIPELINE CONFIGURATION
                        </span>
                        <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary); margin: 0.2rem 0 0 0;">
                            Edit Recruitment Pipeline
                        </h3>
                    </div>
                    <button type="button" wire:click="closeEditPipelineModal" style="background: transparent; border: none; color: var(--color-text-muted); cursor: pointer; padding: 0.25rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="savePipelineInfo">
                    <!-- Pipeline Name -->
                    <div class="form-group mb-4">
                        <label class="form-label" style="font-weight: 700; font-size: 0.85rem;">
                            Pipeline Name <span style="color: var(--danger);">*</span>
                        </label>
                        <input type="text" class="form-control" wire:model="edit_flow_name" placeholder="e.g. Tenured Faculty Recruitment Pipeline" style="font-weight: 600;">
                        @error('edit_flow_name') <span style="color: var(--danger); font-size: 0.75rem;">{{ $message }}</span> @enderror
                    </div>

                    <!-- Sector -->
                    <div class="form-group mb-4">
                        <label class="form-label" style="font-weight: 700; font-size: 0.85rem;">
                            Organizational Sector <span style="color: var(--danger);">*</span>
                        </label>
                        <select class="form-control form-select" wire:model="edit_flow_sector" style="font-weight: 600;">
                            <option value="ACADEMIC">🎓 Academic & Faculty Track</option>
                            <option value="CORPORATE">🏢 Corporate & Commercial</option>
                            <option value="HEALTHCARE">🏥 Healthcare & Clinical</option>
                            <option value="PUBLIC_SECTOR">🏛️ Public Sector / Civil Service</option>
                            <option value="JUDICIAL">⚖️ Judicial & Legal</option>
                            <option value="DEFENSE">🛡️ Defense & Security</option>
                            <option value="GENERAL">⚙️ General Recruitment Track</option>
                        </select>
                        @error('edit_flow_sector') <span style="color: var(--danger); font-size: 0.75rem;">{{ $message }}</span> @enderror
                    </div>

                    <!-- Blueprint Description -->
                    <div class="form-group mb-4">
                        <label class="form-label" style="font-weight: 700; font-size: 0.85rem;">
                            Pipeline Blueprint Description
                        </label>
                        <textarea class="form-control" wire:model="edit_flow_description" rows="3" placeholder="Describe the workflow requirements, governing bodies, and milestones..."></textarea>
                        @error('edit_flow_description') <span style="color: var(--danger); font-size: 0.75rem;">{{ $message }}</span> @enderror
                    </div>

                    <!-- Settings Switches -->
                    <div style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: var(--radius-md); padding: 1rem; margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 0.75rem;">
                        <label style="display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                            <div>
                                <span style="font-weight: 700; font-size: 0.85rem; color: var(--color-text-primary); display: block;">Pipeline Active Status</span>
                                <small style="color: var(--color-text-secondary); font-size: 0.74rem;">When inactive, campaigns cannot attach to this workflow.</small>
                            </div>
                            <input type="checkbox" wire:model="edit_flow_is_active" style="width: 1.2rem; height: 1.2rem; accent-color: var(--primary);">
                        </label>

                        <div style="height: 1px; background: rgba(255, 255, 255, 0.06);"></div>

                        <label style="display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                            <div>
                                <span style="font-weight: 700; font-size: 0.85rem; color: var(--color-text-primary); display: block;">System Default Pipeline</span>
                                <small style="color: var(--color-text-secondary); font-size: 0.74rem;">Newly created job campaigns will link to this pipeline by default.</small>
                            </div>
                            <input type="checkbox" wire:model="edit_flow_is_default" style="width: 1.2rem; height: 1.2rem; accent-color: var(--primary);">
                        </label>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                        <button type="button" class="btn btn-secondary" wire:click="closeEditPipelineModal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" style="font-weight: 700;">
                            💾 Save Pipeline Details
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL: PIPELINE DELETION WARNING & CAMPAIGN DETACHMENT         -->
    <!-- ============================================================== -->
    @if($showDeleteWarningModal)
        <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.8); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 1.5rem;"
             wire:keydown.escape="cancelDeleteProcessFlow">
            <div class="card" style="width: 100%; max-width: 520px; padding: 1.75rem; border: 1px solid rgba(239, 68, 68, 0.35); box-shadow: 0 20px 40px rgba(0,0,0,0.7); background: #0f172a;"
                 @click.outside="$wire.cancelDeleteProcessFlow()">
                
                <div style="display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 1.25rem;">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.4rem;">
                        ⚠️
                    </div>
                    <div>
                        <h3 style="font-size: 1.2rem; font-weight: 800; color: #ef4444; margin: 0 0 0.35rem 0;">
                            @if(count($deleteWarningCampaigns) > 0)
                                Active Job Campaign Attachments Warning
                            @else
                                Delete Recruitment Pipeline
                            @endif
                        </h3>
                        <p style="font-size: 0.85rem; color: var(--color-text-secondary); margin: 0; line-height: 1.4;">
                            @if(count($deleteWarningCampaigns) > 0)
                                This pipeline cannot be deleted silently. It is currently linked to <strong>{{ count($deleteWarningCampaigns) }} active job position(s)</strong>:
                            @else
                                Are you sure you want to permanently delete this recruitment pipeline? This action cannot be undone.
                            @endif
                        </p>
                    </div>
                </div>

                @if(count($deleteWarningCampaigns) > 0)
                    <!-- List of Linked Job Campaigns -->
                    <div style="background: rgba(239, 68, 68, 0.06); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: var(--radius-md); padding: 0.85rem; margin-bottom: 1.25rem; max-height: 180px; overflow-y: auto;">
                        <ul style="margin: 0; padding-left: 1.2rem; font-size: 0.82rem; color: var(--color-text-primary);">
                            @foreach($deleteWarningCampaigns as $c)
                                <li style="margin-bottom: 0.35rem;">
                                    <strong>[{{ $c['job_code'] ?? 'JOB' }}]</strong> {{ $c['title'] ?? 'Untitled Campaign' }}
                                    @if(!empty($c['department']))
                                        <span style="color: var(--color-text-muted);">({{ $c['department'] }})</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div style="font-size: 0.8rem; color: #f59e0b; margin-bottom: 1.25rem; line-height: 1.35;">
                        ⚡ Proceeding will detach this pipeline from all linked positions. Candidates in these campaigns will retain existing progress.
                    </div>
                @endif

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" class="btn btn-secondary" wire:click="cancelDeleteProcessFlow" style="font-weight: 600;">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-danger" wire:click="confirmDeleteProcessFlow" style="font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        <span>{{ count($deleteWarningCampaigns) > 0 ? 'Detach & Delete Pipeline' : 'Delete Pipeline' }}</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
