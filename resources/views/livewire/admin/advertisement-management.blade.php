<div class="entity-page">
    <!-- Executive Control Header -->
    <div class="page-hero-header">
        <div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div class="brand-icon-box" style="background: linear-gradient(135deg, rgba(56,189,248,0.25), rgba(14,165,233,0.4)); box-shadow: 0 4px 14px rgba(56,189,248,0.25);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" style="color: #ffffff;">
                        <rect width="18" height="18" x="3" y="3" rx="2"/>
                        <path d="M3 9h18"/>
                        <path d="M9 21V9"/>
                    </svg>
                </div>
                <div>
                    <h1>Official Advertisements & Validity Desk</h1>
                    <p>Publish official recruitment advertisements, track validity timelines, issue date extension corrigenda, and manage underlying quotas.</p>
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <button type="button" class="btn btn-secondary" wire:click="exportCsv" style="font-size: 0.82rem; padding: 0.6rem 1rem; font-weight: 700; border-radius: 10px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                <span>Export CSV</span>
            </button>

            @can('create-campaign')
                <button type="button" class="btn btn-primary" wire:click="openCreateModal" style="font-size: 0.85rem; padding: 0.65rem 1.35rem; font-weight: 800; display: inline-flex; align-items: center; gap: 0.4rem; border-radius: 10px; box-shadow: 0 4px 18px -2px rgba(14,165,233,0.5); background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); border-color: #0284c7;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    <span>+ New Advertisement</span>
                </button>
            @endcan
        </div>
    </div>

    <!-- Executive KPI Strip -->
    <div class="stat-kpi-grid">
        <div class="metric-card" style="--card-accent: var(--primary); --metric-icon-bg: var(--primary-light); --metric-icon-color: var(--primary);">
            <div class="metric-header">
                <span class="metric-label">Total Advertisements</span>
                <div class="metric-icon-wrap">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/>
                    </svg>
                </div>
            </div>
            <div class="metric-value" style="color: var(--primary);">{{ $stats['total'] }}</div>
            <div class="metric-subtext">Official recruitment cycles</div>
        </div>

        <div class="metric-card" style="--card-accent: #10b981; --metric-icon-bg: rgba(16,185,129,0.12); --metric-icon-color: #10b981;">
            <div class="metric-header">
                <span class="metric-label">Currently Active</span>
                <div class="metric-icon-wrap">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981; box-shadow: 0 0 8px rgba(16,185,129,0.8);"></span>
                </div>
            </div>
            <div class="metric-value" style="color: #10b981;">{{ $stats['active'] }}</div>
            <div class="metric-subtext">Open for candidacies</div>
        </div>

        <div class="metric-card" style="--card-accent: #38bdf8; --metric-icon-bg: rgba(56,189,248,0.12); --metric-icon-color: #38bdf8;">
            <div class="metric-header">
                <span class="metric-label">Extensions Logged</span>
                <div class="metric-icon-wrap">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/>
                    </svg>
                </div>
            </div>
            <div class="metric-value" style="color: #38bdf8;">{{ $stats['extended'] }}</div>
            <div class="metric-subtext">Corrigenda issued</div>
        </div>

        <div class="metric-card" style="--card-accent: var(--secondary); --metric-icon-bg: var(--secondary-light); --metric-icon-color: var(--secondary);">
            <div class="metric-header">
                <span class="metric-label">Mapped Jobs</span>
                <div class="metric-icon-wrap">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                    </svg>
                </div>
            </div>
            <div class="metric-value" style="color: var(--secondary);">{{ $stats['totalJobs'] }}</div>
            <div class="metric-subtext">Mapped positions & cadres</div>
        </div>
    </div>

    <!-- Notification Message -->
    @if(session()->has('message'))
        <div class="card mb-4" style="background: rgba(16, 185, 129, 0.12); border-color: rgba(16, 185, 129, 0.4); padding: 0.85rem 1.25rem; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 0.6rem; color: #10b981; font-size: 0.85rem; font-weight: 700;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span>{{ session('message') }}</span>
            </div>
            <button type="button" wire:click="$refresh" style="background: none; border: none; color: #10b981; cursor: pointer; font-size: 0.9rem;">✕</button>
        </div>
    @endif

    <!-- Dynamic Card Grid & Table View Component -->
    <x-entity-card-grid 
        title="Official Advertisements & Notices"
        :total-count="$advertisements->total()"
        :paginator="$advertisements"
        :view-mode="$viewMode"
    >
        <x-slot:filters>
            <div style="width: 130px;">
                <select class="form-control form-select" wire:model.live="yearFilter" style="background: var(--bg-card); color: var(--color-text-primary); font-size: 0.82rem;">
                    <option value="">All Years</option>
                    @foreach($availableYears as $yr)
                        <option value="{{ $yr }}">{{ $yr }}</option>
                    @endforeach
                </select>
            </div>

            <div style="width: 155px;">
                <select class="form-control form-select" wire:model.live="statusFilter" style="background: var(--bg-card); color: var(--color-text-primary); font-size: 0.82rem;">
                    <option value="ALL">All Statuses</option>
                    <option value="ACTIVE">Active Only</option>
                    <option value="EXTENDED">Extended Only</option>
                    <option value="EXPIRED">Expired Only</option>
                    <option value="INACTIVE">Inactive Only</option>
                </select>
            </div>
        </x-slot:filters>

        <x-slot:headerActions>
            @can('create-campaign')
                <button type="button" class="btn btn-primary" wire:click="openCreateModal" style="padding: 0.45rem 0.9rem; font-size: 0.82rem; font-weight: 700;">
                    + New Advertisement
                </button>
            @endcan
        </x-slot:headerActions>

        <!-- Card Grid View -->
        @foreach($advertisements as $ad)
            @php
                $badge = $ad->status_badge;
                $isExpired = $ad->is_expired;
                $isExtended = $ad->is_extended;
            @endphp
            <x-entity-card
                :key="'ad-card-'.$ad->id"
                avatar-initial="📢"
                avatar-class="avatar-theme-indigo"
                :avatar-badge="(string)$ad->year"
                :subtitle="$ad->newspaper_reference ?: 'Direct Notification'"
                :is-active="$ad->is_active && !$isExpired"
            >
                <x-slot:titleSlot>
                    <div style="display: flex; flex-direction: column; min-width: 0;">
                        <span style="font-size: 0.74rem; font-weight: 800; color: var(--secondary); text-transform: uppercase; letter-spacing: 0.05em; line-height: 1.2;">
                            {{ $ad->advertisement_no }}
                        </span>
                        <div style="font-size: 1.08rem; font-weight: 800; color: var(--color-text-primary); line-height: 1.28; word-break: break-word;" title="{{ $ad->title }}">
                            {{ $ad->title }}
                        </div>
                    </div>
                </x-slot:titleSlot>

                <!-- Interactive Status Strip (Above action footer) -->
                <x-slot:badge>
                    @can('edit-campaign')
                        <button type="button" 
                                class="entity-card-status-toggle-btn"
                                wire:click="toggleAdvertisementStatus({{ $ad->id }})"
                                style="font-size: 0.74rem; font-weight: 800; cursor: pointer; color: {{ $badge['color'] }};"
                                title="Click to toggle status"
                                aria-label="Toggle status for {{ $ad->advertisement_no }}">
                            <span style="display: inline-flex; align-items: center; gap: 0.4rem;">
                                <span style="display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: {{ $badge['color'] }}; box-shadow: 0 0 8px {{ $badge['color'] }};"></span>
                                <span>{{ $badge['label'] }}</span>
                            </span>
                            <span style="font-size: 0.68rem; opacity: 0.75; font-weight: 700; text-transform: uppercase;">Click to toggle ↻</span>
                        </button>
                    @else
                        <div style="display: inline-flex; align-items: center; gap: 0.4rem; color: {{ $badge['color'] }}; font-weight: 800;">
                            <span style="display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: {{ $badge['color'] }};"></span>
                            <span>{{ $badge['label'] }}</span>
                        </div>
                    @endcan
                </x-slot:badge>

                <x-slot:tag>
                    <span class="badge badge-indigo" style="font-weight: 700; font-size: 0.72rem;">
                        Year {{ $ad->year }}
                    </span>
                </x-slot:tag>

                <x-slot:body>
                    <div class="entity-card-metric-row">
                        <span class="entity-card-metric-label">Validity Period</span>
                        <span class="entity-card-metric-value" style="font-size: 0.76rem; font-weight: 700; color: var(--color-text-primary);">
                            {{ $ad->formatted_validity }}
                        </span>
                    </div>

                    <div class="entity-card-metric-row">
                        <span class="entity-card-metric-label">Days Remaining</span>
                        @if($isExpired)
                            <span class="badge" style="background: rgba(239,68,68,0.15); color: #ef4444; font-weight: 800; font-size: 0.72rem;">EXPIRED</span>
                        @else
                            <span class="badge" style="background: rgba(16,185,129,0.15); color: #10b981; font-weight: 800; font-size: 0.72rem;">
                                {{ $ad->days_remaining }} Day{{ $ad->days_remaining === 1 ? '' : 's' }}
                            </span>
                        @endif
                    </div>

                    <div class="entity-card-metric-row" style="margin-top: 0.25rem;">
                        <span class="entity-card-metric-label">Advertised Jobs</span>
                        <button type="button" 
                                wire:click="openJobsModal({{ $ad->id }})" 
                                class="badge badge-cyan" 
                                style="font-weight: 800; font-size: 0.72rem; cursor: pointer; border: none;"
                                title="Click to view all jobs advertised">
                            💼 {{ $ad->campaigns_count }} Position{{ $ad->campaigns_count === 1 ? '' : 's' }}
                        </button>
                    </div>

                    <div class="entity-card-metric-row">
                        <span class="entity-card-metric-label">Date Extensions</span>
                        @if($ad->date_tracks_count > 0)
                            <button type="button" 
                                    wire:click="openHistoryModal({{ $ad->id }})" 
                                    class="badge" 
                                    style="background: rgba(56,189,248,0.15); color: #38bdf8; font-weight: 800; font-size: 0.72rem; cursor: pointer; border: 1px solid rgba(56,189,248,0.3);"
                                    title="Click to view complete extension history">
                                ⏱️ Extended x{{ $ad->date_tracks_count }}
                            </button>
                        @else
                            <span style="font-size: 0.74rem; color: var(--color-text-muted);">None (Original)</span>
                        @endif
                    </div>
                </x-slot:body>

                <!-- Quick Hover Actions -->
                <x-slot:quickActions>
                    @can('edit-campaign')
                        <button type="button" 
                                class="action-btn action-btn-view" 
                                wire:click="openExtendModal({{ $ad->id }})" 
                                data-tooltip="Extend Validity / Corrigendum" 
                                aria-label="Extend Validity">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="12" y1="14" x2="12" y2="18"/><line x1="10" y1="16" x2="14" y2="16"/></svg>
                        </button>
                    @endcan

                    <button type="button" 
                            class="action-btn action-btn-info" 
                            wire:click="openHistoryModal({{ $ad->id }})" 
                            data-tooltip="View Date History & Tracking" 
                            aria-label="View History">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </button>

                    <button type="button" 
                            class="action-btn action-btn-view" 
                            wire:click="openJobsModal({{ $ad->id }})" 
                            data-tooltip="View Advertised Jobs" 
                            aria-label="View Jobs">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    </button>

                    @can('edit-campaign')
                        <button type="button" 
                                class="action-btn action-btn-edit" 
                                wire:click="openEditModal({{ $ad->id }})" 
                                data-tooltip="Edit Advertisement" 
                                aria-label="Edit Advertisement">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </button>

                        <button type="button" 
                                class="action-btn {{ $ad->is_active ? 'action-btn-success' : 'action-btn-delete' }}" 
                                data-tooltip="{{ $ad->is_active ? 'Active (Click to Deactivate)' : 'Inactive (Click to Activate)' }}" 
                                wire:click="toggleAdvertisementStatus({{ $ad->id }})" 
                                aria-label="Toggle Status">
                            @if($ad->is_active)
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            @else
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            @endif
                        </button>
                    @endcan
                </x-slot:quickActions>

                <!-- Kebab Menu Items -->
                <x-slot:menuItems>
                    <x-entity-card-menu-item wire-click="openExtendModal({{ $ad->id }})" label="Extend Validity Date">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </x-entity-card-menu-item>

                    <x-entity-card-menu-item wire-click="openHistoryModal({{ $ad->id }})" label="View Date Tracks & History">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </x-entity-card-menu-item>

                    <x-entity-card-menu-item wire-click="openJobsModal({{ $ad->id }})" label="View Advertised Jobs ({{ $ad->campaigns_count }})">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    </x-entity-card-menu-item>

                    @can('edit-campaign')
                        <x-entity-card-menu-item wire-click="openEditModal({{ $ad->id }})" label="Edit Advertisement Particulars">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </x-entity-card-menu-item>

                        <x-entity-card-menu-item wire-click="toggleAdvertisementStatus({{ $ad->id }})" label="{{ $ad->is_active ? 'Deactivate Advertisement' : 'Activate Advertisement' }}">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        </x-entity-card-menu-item>
                    @endcan
                </x-slot:menuItems>
            </x-entity-card>
        @endforeach

        <!-- Table View Slot -->
        <x-slot:table>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Advertisement No & Title</th>
                            <th>Year</th>
                            <th>Validity Period</th>
                            <th>Days Left</th>
                            <th>Jobs Count</th>
                            <th>Extensions</th>
                            <th>Status (Click to Toggle)</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($advertisements as $ad)
                            @php
                                $badge = $ad->status_badge;
                                $isExpired = $ad->is_expired;
                            @endphp
                            <tr wire:key="ad-table-row-{{ $ad->id }}">
                                <td>
                                    <div>
                                        <span style="font-size: 0.72rem; font-weight: 800; color: var(--secondary); text-transform: uppercase;">
                                            {{ $ad->advertisement_no }}
                                        </span>
                                        <strong style="color: var(--color-text-primary); font-size: 0.95rem; display: block; line-height: 1.25;">
                                            {{ $ad->title }}
                                        </strong>
                                        <div style="font-size: 0.76rem; color: var(--color-text-muted); margin-top: 2px;">
                                            {{ $ad->newspaper_reference ?: 'Direct Notification' }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-indigo" style="font-weight: 700; font-size: 0.78rem;">
                                        {{ $ad->year }}
                                    </span>
                                </td>
                                <td>
                                    <div style="font-size: 0.82rem; color: var(--color-text-primary); font-weight: 600;">
                                        {{ $ad->formatted_validity }}
                                    </div>
                                </td>
                                <td>
                                    @if($isExpired)
                                        <span class="badge" style="background: rgba(239,68,68,0.15); color: #ef4444; font-weight: 800; font-size: 0.72rem;">EXPIRED</span>
                                    @else
                                        <span class="badge" style="background: rgba(16,185,129,0.15); color: #10b981; font-weight: 800; font-size: 0.72rem;">
                                            {{ $ad->days_remaining }} Day{{ $ad->days_remaining === 1 ? '' : 's' }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" wire:click="openJobsModal({{ $ad->id }})" class="badge badge-cyan" style="font-weight: 800; font-size: 0.76rem; cursor: pointer; border: none;">
                                        💼 {{ $ad->campaigns_count }} Jobs
                                    </button>
                                </td>
                                <td>
                                    @if($ad->date_tracks_count > 0)
                                        <button type="button" wire:click="openHistoryModal({{ $ad->id }})" class="badge" style="background: rgba(56,189,248,0.15); color: #38bdf8; font-weight: 800; font-size: 0.74rem; cursor: pointer; border: 1px solid rgba(56,189,248,0.3);">
                                            ⏱️ {{ $ad->date_tracks_count }} Ext
                                        </button>
                                    @else
                                        <span style="color: var(--color-text-muted); font-size: 0.76rem;">Original</span>
                                    @endif
                                </td>
                                <td>
                                    @can('edit-campaign')
                                        <button type="button" 
                                                wire:click="toggleAdvertisementStatus({{ $ad->id }})"
                                                style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.28rem 0.65rem; font-size: 0.72rem; font-weight: 800; border-radius: 9999px; cursor: pointer; transition: all 0.2s ease; border: 1px solid {{ $badge['border'] }}; background: {{ $badge['bg'] }}; color: {{ $badge['color'] }};"
                                                title="Click to toggle status"
                                                aria-label="Toggle status for {{ $ad->advertisement_no }}">
                                            <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: {{ $badge['color'] }};"></span>
                                            <span>{{ $badge['label'] }}</span>
                                        </button>
                                    @else
                                        <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.28rem 0.65rem; font-size: 0.72rem; font-weight: 800; border-radius: 9999px; border: 1px solid {{ $badge['border'] }}; background: {{ $badge['bg'] }}; color: {{ $badge['color'] }};">
                                            <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: {{ $badge['color'] }};"></span>
                                            <span>{{ $badge['label'] }}</span>
                                        </span>
                                    @endcan
                                </td>
                                <td style="text-align: right;">
                                    <div class="action-btn-group justify-end" x-data="{ openMenu: false }">
                                        @can('edit-campaign')
                                            <button type="button" 
                                                    class="action-btn action-btn-view" 
                                                    wire:click="openExtendModal({{ $ad->id }})" 
                                                    data-tooltip="Extend Validity / Corrigendum" 
                                                    aria-label="Extend Validity">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="12" y1="14" x2="12" y2="18"/><line x1="10" y1="16" x2="14" y2="16"/></svg>
                                            </button>
                                        @endcan

                                        <button type="button" 
                                                class="action-btn action-btn-view" 
                                                wire:click="openJobsModal({{ $ad->id }})" 
                                                data-tooltip="View Advertised Jobs" 
                                                aria-label="View Jobs">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
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
                                                        class="table-action-menu-item" 
                                                        wire:click="openHistoryModal({{ $ad->id }})">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                                    <span>Date History & Tracking</span>
                                                </button>
                                                @can('edit-campaign')
                                                    <button type="button" 
                                                            class="table-action-menu-item" 
                                                            wire:click="openEditModal({{ $ad->id }})">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                        <span>Edit Advertisement</span>
                                                    </button>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center" style="padding: 3rem 1.5rem; color: var(--color-text-muted);">
                                    No advertisements found matching current filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-slot:table>
    </x-entity-card-grid>

    <!-- ============================================================== -->
    <!-- MODAL 1: CREATE NEW ADVERTISEMENT                              -->
    <!-- ============================================================== -->
    @if($showCreateModal)
        <div class="modal-overlay-premium" style="z-index: 1600;">
            <div class="modal-dialog-premium" style="width: 100%; max-width: 720px; max-height: 92vh; overflow-y: auto; padding: 1.75rem; border-color: var(--secondary-glow); box-shadow: 0 30px 80px rgba(0,0,0,0.9);">
                <div class="flex justify-between align-center mb-4" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div class="brand-icon-box" style="width: 40px; height: 40px; border-radius: 10px; background: rgba(99,102,241,0.15); color: var(--primary);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                                <rect width="18" height="18" x="3" y="3" rx="2"/>
                                <path d="M3 9h18"/>
                                <path d="M9 21V9"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="card-title" style="margin: 0; font-size: 1.25rem; font-weight: 800;">
                                Publish Official Advertisement
                            </h3>
                            <p class="card-subtitle" style="margin: 0.2rem 0 0; font-size: 0.82rem;">
                                Register recruitment advertisement notice and initial validity dates.
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="closeCreateModal" style="padding: 0.35rem 0.65rem;">✕</button>
                </div>

                <form wire:submit.prevent="createAdvertisement">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <x-input label="Advertisement No. *" :error="$errors->first('advertisement_no')">
                            <input type="text" class="form-control" wire:model="advertisement_no" placeholder="e.g. ADV-01/2026 or HR/2026-F1">
                        </x-input>

                        <x-input label="Advertisement Year *" :error="$errors->first('year')">
                            <input type="number" class="form-control" wire:model="year" min="2000" max="2100">
                        </x-input>
                    </div>

                    <div class="grid grid-cols-1 gap-4 mb-4">
                        <x-input label="Advertisement Title / Name *" :error="$errors->first('title')">
                            <input type="text" class="form-control" wire:model="title" placeholder="e.g. Faculty & Officers Recruitment Autumn 2026">
                        </x-input>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <x-input label="Validity From (Opening Date) *" :error="$errors->first('valid_from')">
                            <input type="datetime-local" class="form-control" wire:model="valid_from">
                        </x-input>

                        <x-input label="Validity To (Initial Closing Date) *" :error="$errors->first('valid_to')">
                            <input type="datetime-local" class="form-control" wire:model="valid_to">
                        </x-input>
                    </div>

                    <div class="grid grid-cols-1 gap-4 mb-4">
                        <x-input label="Newspaper / Gazette / Public Reference (Optional)" :error="$errors->first('newspaper_reference')">
                            <input type="text" class="form-control" wire:model="newspaper_reference" placeholder="e.g. Daily Dawn & Daily Jang dated 15-Oct-2026 / Official Gazette No. 44">
                        </x-input>
                    </div>

                    <div class="grid grid-cols-1 gap-4 mb-4">
                        <x-input label="Detailed Instructions / Terms & Conditions (Optional)" :error="$errors->first('details')">
                            <textarea class="form-control" wire:model="details" rows="3" placeholder="Enter general guidelines, quota details, or eligibility instructions..."></textarea>
                        </x-input>
                    </div>

                    <div class="flex justify-between align-center" style="border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                        <button type="button" class="btn btn-secondary" wire:click="closeCreateModal">Cancel</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="createAdvertisement">Publish Advertisement</span>
                            <span wire:loading wire:target="createAdvertisement">Publishing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL 2: EXTEND VALIDITY DATE & ISSUE CORRIGENDUM             -->
    <!-- ============================================================== -->
    @if($showExtendModal && $selectedAdvertisementForExtend)
        <div class="modal-overlay-premium" style="z-index: 1700;">
            <div class="modal-dialog-premium" style="width: 100%; max-width: 680px; max-height: 92vh; overflow-y: auto; padding: 1.75rem; border: 1.5px solid var(--secondary); box-shadow: 0 30px 80px rgba(0,0,0,0.95);">
                <div class="flex justify-between align-center mb-4" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div class="brand-icon-box" style="width: 40px; height: 40px; border-radius: 10px; background: rgba(6,182,212,0.15); color: var(--secondary);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                                <line x1="12" y1="14" x2="12" y2="18"/>
                                <line x1="10" y1="16" x2="14" y2="16"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="card-title" style="margin: 0; font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary);">
                                Extend Validity Date & Issue Corrigendum
                            </h3>
                            <p class="card-subtitle" style="margin: 0.2rem 0 0; font-size: 0.82rem;">
                                Advertisement: <strong style="color: var(--secondary);">{{ $selectedAdvertisementForExtend->advertisement_no }}</strong> — {{ $selectedAdvertisementForExtend->title }}
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="closeExtendModal" style="padding: 0.35rem 0.65rem;">✕</button>
                </div>

                <!-- Current Timeline Pill -->
                <div class="card mb-4" style="background: rgba(0,0,0,0.25); border-color: var(--border-color); padding: 0.85rem 1rem;">
                    <div class="flex justify-between align-center" style="font-size: 0.82rem;">
                        <span style="color: var(--color-text-muted);">Current Closing Date:</span>
                        <strong style="color: #f59e0b; font-size: 0.92rem;">
                            {{ $selectedAdvertisementForExtend->valid_to ? $selectedAdvertisementForExtend->valid_to->format('d M Y, h:i A') : 'N/A' }}
                        </strong>
                    </div>
                    <div class="flex justify-between align-center mt-1" style="font-size: 0.82rem;">
                        <span style="color: var(--color-text-muted);">Total Underlying Jobs:</span>
                        <strong style="color: var(--color-text-primary);">
                            {{ $selectedAdvertisementForExtend->campaigns->count() }} Position(s)
                        </strong>
                    </div>
                </div>

                <form wire:submit.prevent="extendAdvertisementValidity">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <x-input label="New Extended Closing Date *" :error="$errors->first('extend_new_valid_to')">
                            <input type="datetime-local" class="form-control" wire:model="extend_new_valid_to">
                        </x-input>

                        <x-input label="Corrigendum / Notice Reference" :error="$errors->first('extend_corrigendum_no')">
                            <input type="text" class="form-control" wire:model="extend_corrigendum_no" placeholder="e.g. Corrigendum No. 1 / Notification # 104">
                        </x-input>
                    </div>

                    <div class="grid grid-cols-1 gap-4 mb-4">
                        <x-input label="Reason for Extension / Administrative Reference *" :error="$errors->first('extend_reason')">
                            <textarea class="form-control" wire:model="extend_reason" rows="3" placeholder="e.g. Extended on the directive of the Competent Authority due to gazetted holidays..."></textarea>
                        </x-input>
                    </div>

                    <div class="card mb-4" style="background: rgba(6, 182, 212, 0.08); border-color: rgba(6, 182, 212, 0.25); padding: 0.85rem 1rem;">
                        <label style="display: flex; align-items: flex-start; gap: 0.65rem; margin: 0; cursor: pointer;">
                            <input type="checkbox" wire:model="extend_sync_jobs" style="margin-top: 3px;">
                            <div>
                                <strong style="color: var(--color-text-primary); font-size: 0.84rem;">Automatically synchronize closing date of all underlying jobs</strong>
                                <p style="color: var(--color-text-muted); font-size: 0.76rem; margin: 0.2rem 0 0 0;">
                                    When checked, all {{ $selectedAdvertisementForExtend->campaigns->count() }} position(s) published under this advertisement will be automatically updated with the new closing date.
                                </p>
                            </div>
                        </label>
                    </div>

                    <div class="flex justify-between align-center" style="border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                        <button type="button" class="btn btn-secondary" wire:click="closeExtendModal">Cancel</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="extendAdvertisementValidity">Log Extension & Update Closing Date</span>
                            <span wire:loading wire:target="extendAdvertisementValidity">Logging Extension...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL 3: DATE EXTENSION HISTORY & TRACKING AUDIT LEDGER       -->
    <!-- ============================================================== -->
    @if($showHistoryModal && $selectedAdvertisementForHistory)
        <div class="modal-overlay-premium" style="z-index: 1750;">
            <div class="modal-dialog-premium" style="width: 100%; max-width: 800px; max-height: 90vh; overflow-y: auto; padding: 2rem; border-color: rgba(56,189,248,0.4); box-shadow: 0 30px 80px rgba(0,0,0,0.9);">
                <div class="flex justify-between align-center mb-4" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div class="brand-icon-box" style="width: 40px; height: 40px; border-radius: 10px; background: rgba(56,189,248,0.15); color: #38bdf8;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="card-title" style="margin: 0; font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary);">
                                Chronological Validity Tracking & Date History
                            </h3>
                            <p class="card-subtitle" style="margin: 0.2rem 0 0; font-size: 0.82rem;">
                                Advertisement: <strong style="color: var(--secondary);">{{ $selectedAdvertisementForHistory->advertisement_no }}</strong> — {{ $selectedAdvertisementForHistory->title }}
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="closeHistoryModal" style="padding: 0.35rem 0.65rem;">✕</button>
                </div>

                <!-- Current Active Validity Strip -->
                <div class="card mb-4" style="background: rgba(16,185,129,0.08); border-color: rgba(16,185,129,0.25); padding: 1rem 1.25rem;">
                    <div class="flex justify-between align-center flex-wrap gap-2">
                        <div>
                            <span style="font-size: 0.72rem; text-transform: uppercase; font-weight: 800; color: #10b981; letter-spacing: 0.05em;">Current Active Validity Range</span>
                            <div style="font-size: 1.15rem; font-weight: 800; color: var(--color-text-primary); margin-top: 2px;">
                                {{ $selectedAdvertisementForHistory->formatted_validity }}
                            </div>
                        </div>
                        <div>
                            <span class="badge" style="background: rgba(56,189,248,0.15); color: #38bdf8; font-weight: 800; font-size: 0.78rem;">
                                {{ count($dateTracks) }} Extension Record(s) Logged
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Timeline Audit Ledger -->
                <div style="position: relative; padding-left: 1.75rem; border-left: 2px dashed rgba(6,182,212,0.3); margin-left: 0.75rem;">
                    @forelse($dateTracks as $idx => $track)
                        <div style="position: relative; margin-bottom: 1.75rem;">
                            <!-- Timeline Dot -->
                            <span style="position: absolute; left: -2.35rem; top: 4px; width: 14px; height: 14px; border-radius: 50%; background: #38bdf8; border: 2px solid var(--bg-card); box-shadow: 0 0 10px rgba(56,189,248,0.7);"></span>

                            <div class="card" style="background: var(--bg-surface); border-color: var(--border-color); padding: 1rem 1.25rem;">
                                <div class="flex justify-between align-center flex-wrap gap-2 mb-2">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <span class="badge" style="background: rgba(56,189,248,0.15); color: #38bdf8; font-weight: 800; font-size: 0.74rem;">
                                            {{ $track->corrigendum_no ?: 'Extension #' . (count($dateTracks) - $idx) }}
                                        </span>
                                        <strong style="color: var(--color-text-primary); font-size: 0.88rem;">
                                            Date Extension Logged
                                        </strong>
                                    </div>
                                    <span style="font-size: 0.76rem; color: var(--color-text-muted);">
                                        Recorded on {{ $track->extended_at ? $track->extended_at->format('d M Y, h:i A') : 'N/A' }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-3 mb-2" style="background: rgba(0,0,0,0.2); padding: 0.65rem 0.85rem; border-radius: var(--radius-sm);">
                                    <div>
                                        <span style="font-size: 0.7rem; color: var(--color-text-muted); text-transform: uppercase; font-weight: 700;">Previous Closing Date</span>
                                        <div style="color: #ef4444; font-size: 0.85rem; font-weight: 700; text-decoration: line-through;">
                                            {{ $track->previous_valid_to ? $track->previous_valid_to->format('d M Y, h:i A') : 'N/A' }}
                                        </div>
                                    </div>
                                    <div>
                                        <span style="font-size: 0.7rem; color: var(--color-text-muted); text-transform: uppercase; font-weight: 700;">New Extended Closing Date</span>
                                        <div style="color: #10b981; font-size: 0.85rem; font-weight: 800;">
                                            {{ $track->extended_valid_to ? $track->extended_valid_to->format('d M Y, h:i A') : 'N/A' }}
                                        </div>
                                    </div>
                                </div>

                                <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.5;">
                                    <strong style="color: var(--color-text-primary);">Reason / Justification:</strong> {{ $track->reason }}
                                </div>

                                @if($track->extendedBy)
                                    <div style="margin-top: 0.5rem; font-size: 0.74rem; color: var(--color-text-muted);">
                                        Authorized by: <strong style="color: var(--color-text-secondary);">{{ $track->extendedBy->name ?? $track->extendedBy->email }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 2rem; color: var(--color-text-muted); font-size: 0.85rem;">
                            No previous date extensions recorded. This advertisement is operating on its original published schedule.
                        </div>
                    @endforelse

                    <!-- Original Publish Event -->
                    <div style="position: relative;">
                        <span style="position: absolute; left: -2.35rem; top: 4px; width: 14px; height: 14px; border-radius: 50%; background: #10b981; border: 2px solid var(--bg-card);"></span>
                        <div class="card" style="background: rgba(0,0,0,0.2); border-color: var(--border-color); padding: 0.85rem 1rem;">
                            <div class="flex justify-between align-center">
                                <strong style="color: var(--color-text-primary); font-size: 0.84rem;">Initial Advertisement Publication</strong>
                                <span style="font-size: 0.74rem; color: var(--color-text-muted);">
                                    {{ $selectedAdvertisementForHistory->valid_from ? $selectedAdvertisementForHistory->valid_from->format('d M Y') : 'N/A' }}
                                </span>
                            </div>
                            <div style="font-size: 0.78rem; color: var(--color-text-muted); margin-top: 2px;">
                                Original Validity: {{ $selectedAdvertisementForHistory->formatted_validity }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-4" style="border-top: 1px solid var(--border-color); padding-top: 1rem;">
                    <button type="button" class="btn btn-secondary" wire:click="closeHistoryModal">Close History</button>
                </div>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL 4: VIEW ADVERTISED JOBS                                 -->
    <!-- ============================================================== -->
    @if($showJobsModal && $selectedAdvertisementForJobs)
        <div class="modal-overlay-premium" style="z-index: 1700;">
            <div class="modal-dialog-premium" style="width: 100%; max-width: 820px; max-height: 90vh; overflow-y: auto; padding: 2rem; border-color: var(--secondary-glow); box-shadow: 0 30px 80px rgba(0,0,0,0.9);">
                <div class="flex justify-between align-center mb-4" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div class="brand-icon-box" style="width: 40px; height: 40px; border-radius: 10px; background: rgba(99,102,241,0.15); color: var(--primary);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                                <rect width="20" height="14" x="2" y="7" rx="2" ry="2"/>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="card-title" style="margin: 0; font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary);">
                                Jobs & Positions Under Advertisement
                            </h3>
                            <p class="card-subtitle" style="margin: 0.2rem 0 0; font-size: 0.82rem;">
                                Advertisement: <strong style="color: var(--secondary);">{{ $selectedAdvertisementForJobs->advertisement_no }}</strong> ({{ count($advertisedJobs) }} positions)
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="closeJobsModal" style="padding: 0.35rem 0.65rem;">✕</button>
                </div>

                @if(!empty($advertisedJobs) && count($advertisedJobs) > 0)
                    <div class="table-container" style="max-height: 400px; overflow-y: auto;">
                        <table class="table" style="font-size: 0.82rem;">
                            <thead>
                                <tr>
                                    <th>Job Code & Post Title</th>
                                    <th>Department</th>
                                    <th>Track</th>
                                    <th>Closing Date</th>
                                    <th>Applications</th>
                                    <th>Fee</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($advertisedJobs as $job)
                                    <tr>
                                        <td>
                                            <span style="font-size: 0.72rem; color: var(--secondary); font-weight: 800; font-family: 'JetBrains Mono', monospace;">
                                                {{ $job->job_code }}
                                            </span>
                                            <strong style="color: var(--color-text-primary); font-size: 0.9rem; display: block;">
                                                {{ $job->title }}
                                            </strong>
                                        </td>
                                        <td>
                                            <span style="color: var(--color-text-secondary);">
                                                {{ $job->department }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $job->track_type === 'ACADEMIC' ? 'badge-indigo' : 'badge-cyan' }}" style="font-size: 0.72rem; font-weight: 700;">
                                                {{ $job->track_type }}
                                            </span>
                                        </td>
                                        <td>
                                            <span style="font-size: 0.78rem; color: var(--color-text-primary); font-weight: 600;">
                                                {{ $job->closing_date ? $job->closing_date->format('d M Y') : 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge" style="background: rgba(16,185,129,0.15); color: #10b981; font-weight: 800; font-size: 0.74rem;">
                                                {{ $job->applications->count() }} Applicant(s)
                                            </span>
                                        </td>
                                        <td>
                                            <code style="font-family: 'JetBrains Mono', monospace; font-size: 0.82rem;">
                                                Rs. {{ number_format($job->application_fee, 0) }}
                                            </code>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="text-align: center; padding: 2.5rem; color: var(--color-text-muted); background: rgba(0,0,0,0.15); border: 1.5px dashed var(--border-color); border-radius: var(--radius-md);">
                        <p style="margin: 0 0 0.85rem 0; font-size: 0.88rem;">No jobs or positions mapped under this advertisement yet.</p>
                        <a href="{{ url('/admin/campaigns') }}" class="btn btn-primary btn-sm" style="text-decoration: none;">
                            Go to Job Campaigns to Map Positions
                        </a>
                    </div>
                @endif

                <div class="flex justify-between align-center mt-4" style="border-top: 1px solid var(--border-color); padding-top: 1rem;">
                    <a href="{{ url('/admin/campaigns') }}" class="btn btn-primary btn-sm" style="text-decoration: none;">
                        Manage All Job Campaigns
                    </a>
                    <button type="button" class="btn btn-secondary" wire:click="closeJobsModal">Close</button>
                </div>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL 5: EDIT ADVERTISEMENT                                   -->
    <!-- ============================================================== -->
    @if($showEditModal)
        <div class="modal-overlay-premium" style="z-index: 1600;">
            <div class="modal-dialog-premium" style="width: 100%; max-width: 720px; max-height: 92vh; overflow-y: auto; padding: 1.75rem; border-color: var(--secondary-glow); box-shadow: 0 30px 80px rgba(0,0,0,0.9);">
                <div class="flex justify-between align-center mb-4" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div class="brand-icon-box" style="width: 40px; height: 40px; border-radius: 10px; background: rgba(99,102,241,0.15); color: var(--primary);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="card-title" style="margin: 0; font-size: 1.25rem; font-weight: 800;">
                                Edit Advertisement Particulars
                            </h3>
                            <p class="card-subtitle" style="margin: 0.2rem 0 0; font-size: 0.82rem;">
                                Update advertisement title, year, guidelines, and reference.
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="closeEditModal" style="padding: 0.35rem 0.65rem;">✕</button>
                </div>

                <form wire:submit.prevent="updateAdvertisement">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <x-input label="Advertisement No. *" :error="$errors->first('advertisement_no')">
                            <input type="text" class="form-control" wire:model="advertisement_no">
                        </x-input>

                        <x-input label="Advertisement Year *" :error="$errors->first('year')">
                            <input type="number" class="form-control" wire:model="year" min="2000" max="2100">
                        </x-input>
                    </div>

                    <div class="grid grid-cols-1 gap-4 mb-4">
                        <x-input label="Advertisement Title / Name *" :error="$errors->first('title')">
                            <input type="text" class="form-control" wire:model="title">
                        </x-input>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <x-input label="Validity From (Opening Date) *" :error="$errors->first('valid_from')">
                            <input type="datetime-local" class="form-control" wire:model="valid_from">
                        </x-input>

                        <x-input label="Validity To (Current Closing Date) *" :error="$errors->first('valid_to')">
                            <input type="datetime-local" class="form-control" wire:model="valid_to">
                        </x-input>
                    </div>

                    <div class="grid grid-cols-1 gap-4 mb-4">
                        <x-input label="Newspaper / Gazette / Public Reference (Optional)" :error="$errors->first('newspaper_reference')">
                            <input type="text" class="form-control" wire:model="newspaper_reference">
                        </x-input>
                    </div>

                    <div class="grid grid-cols-1 gap-4 mb-4">
                        <x-input label="Detailed Instructions / Terms & Conditions (Optional)" :error="$errors->first('details')">
                            <textarea class="form-control" wire:model="details" rows="3"></textarea>
                        </x-input>
                    </div>

                    <div class="flex justify-between align-center" style="border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                        <button type="button" class="btn btn-secondary" wire:click="closeEditModal">Cancel</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="updateAdvertisement">Save Changes</span>
                            <span wire:loading wire:target="updateAdvertisement">Saving Changes...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
