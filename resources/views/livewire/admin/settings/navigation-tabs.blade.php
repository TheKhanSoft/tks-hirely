@php
    $basePrefix = request()->is('registrar*') ? '/registrar' : '/admin';
    
    $settingsTabs = [
        [
            'id' => 'pipeline',
            'slug' => 'workflows',
            'url' => $basePrefix . '/settings/workflows',
            'label' => 'Workflow Pipelines',
            'badge' => 'Gates & Quorum',
            'subtitle' => 'Milestones, quorums & stage gating',
            'color' => '#6366f1',
            'bg' => 'rgba(99, 102, 241, 0.12)',
            'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>',
        ],
        [
            'id' => 'documents',
            'slug' => 'documents',
            'url' => $basePrefix . '/settings/documents',
            'label' => 'Document Proofs',
            'badge' => 'Compliance',
            'subtitle' => 'Dossier uploads & verification rules',
            'color' => '#0ea5e9',
            'bg' => 'rgba(14, 165, 233, 0.12)',
            'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>',
        ],
        [
            'id' => 'candidate_rules',
            'slug' => 'candidate-rules',
            'url' => $basePrefix . '/settings/candidate-rules',
            'label' => 'Candidate Policies',
            'badge' => 'Intake Controls',
            'subtitle' => 'Limits, age cutoffs & draft quotas',
            'color' => '#8b5cf6',
            'bg' => 'rgba(139, 92, 246, 0.12)',
            'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
        ],
        [
            'id' => 'organization',
            'slug' => 'organization',
            'url' => $basePrefix . '/settings/organization',
            'label' => 'Institution & Sector',
            'badge' => 'Entity Profile',
            'subtitle' => 'Entity structure, faculties & profile',
            'color' => '#10b981',
            'bg' => 'rgba(16, 185, 129, 0.12)',
            'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2" ry="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M12 6h.01"/><path d="M12 10h.01"/><path d="M12 14h.01"/><path d="M16 10h.01"/><path d="M16 14h.01"/><path d="M8 10h.01"/><path d="M8 14h.01"/></svg>',
        ],
        [
            'id' => 'academic_criteria',
            'slug' => 'academic-criteria',
            'url' => $basePrefix . '/settings/academic-criteria',
            'label' => 'Academic Benchmarks',
            'badge' => 'HEC Standards',
            'subtitle' => 'HEC thresholds, CGPA & division rules',
            'color' => '#f59e0b',
            'bg' => 'rgba(245, 158, 11, 0.12)',
            'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m4 6 8-4 8 4-8 4-8-4Z"/><path d="m18 10 4 2v6a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-6l4-2"/><path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5"/></svg>',
        ],
        [
            'id' => 'branding',
            'slug' => 'branding',
            'url' => $basePrefix . '/settings/branding',
            'label' => 'App Branding & Logos',
            'badge' => 'Visual Identity',
            'subtitle' => 'Logos, favicon, colors & meta',
            'color' => '#ec4899',
            'bg' => 'rgba(236, 72, 153, 0.12)',
            'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="13.5" cy="6.5" r=".5" fill="currentColor"/><circle cx="17.5" cy="10.5" r=".5" fill="currentColor"/><circle cx="8.5" cy="7.5" r=".5" fill="currentColor"/><circle cx="6.5" cy="12.5" r=".5" fill="currentColor"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z"/></svg>',
        ],
        [
            'id' => 'contact',
            'slug' => 'contact',
            'url' => $basePrefix . '/settings/contact',
            'label' => 'Contact & Helpdesk',
            'badge' => 'Communications',
            'subtitle' => 'Official hotlines, desk & address',
            'color' => '#14b8a6',
            'bg' => 'rgba(20, 184, 166, 0.12)',
            'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
        ],
        [
            'id' => 'email',
            'slug' => 'email',
            'url' => $basePrefix . '/settings/email',
            'label' => 'Email & Delivery',
            'badge' => 'SMTP Protocol',
            'subtitle' => 'SMTP gateway, TLS & live tester',
            'color' => '#3b82f6',
            'bg' => 'rgba(59, 130, 246, 0.12)',
            'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>',
        ],
    ];

    $currentTabInfo = collect($settingsTabs)->firstWhere('id', $activeTab) ?? $settingsTabs[0];
@endphp

<!-- Executive Control Header -->
<div class="settings-hero-header">
    <div class="settings-hero-left">
        <div class="settings-hero-icon-box">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
            </svg>
        </div>
        <div>
            <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
                <h1 style="margin: 0; font-size: 1.38rem; font-weight: 800; color: var(--color-text-primary); letter-spacing: -0.015em;">
                    Operational Governance & System Settings
                </h1>
            </div>
            <p style="margin: 0.25rem 0 0 0; font-size: 0.85rem; color: var(--color-text-secondary); line-height: 1.45;">
                Configure statutory recruitment milestones, mandatory verification proofs, intake rules, and institutional taxonomy.
            </p>
        </div>
    </div>

    <div class="settings-hero-right">
        <div class="active-workspace-pill">
            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: {{ $currentTabInfo['color'] ?? 'var(--primary)' }};"></span>
            <span style="font-size: 0.72rem; color: var(--color-text-muted); font-weight: 700; text-transform: uppercase;">Section:</span>
            <strong style="font-size: 0.82rem; font-weight: 800; color: var(--color-text-primary);">{{ $currentTabInfo['label'] }}</strong>
        </div>

        <div class="badge-os-version">
            <span class="pulse-status-dot"></span>
            <span>v2.4 Enterprise OS</span>
        </div>

        {{-- Live Dirty State Tracker --}}
        <div wire:dirty class="unsaved-badge-pill" style="display: none;">
            <span class="unsaved-dot"></span>
            <span>Unsaved Changes</span>
        </div>
    </div>
</div>

<!-- Feedback Alerts -->
@if(session()->has('message'))
    <div class="alert alert-success mb-6" style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3); color: var(--success); padding: 0.85rem 1.25rem; border-radius: var(--radius-md); margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 0.65rem;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <span style="font-weight: 700; font-size: 0.88rem;">{{ session('message') }}</span>
        </div>
        <button type="button" wire:click="$refresh" style="background: none; border: none; color: inherit; cursor: pointer; font-size: 1rem; opacity: 0.7;">✕</button>
    </div>
@endif

<!-- Compact Mobile / Tablet Quick Switcher Strip -->
<div class="settings-quick-strip">
    @foreach($settingsTabs as $tab)
        @php
            $isActive = ($activeTab === $tab['id']);
        @endphp
        <a href="{{ $tab['url'] }}" 
           wire:click.prevent="setTab('{{ $tab['id'] }}')" 
           class="settings-quick-pill {{ $isActive ? 'active' : '' }}">
            {!! $tab['icon'] !!}
            <span>{{ $tab['label'] }}</span>
        </a>
    @endforeach
</div>

<!-- Modern Balanced 4x2 Settings Hub Grid -->
<div class="settings-hub-grid">
    @foreach($settingsTabs as $tab)
        @php
            $isActive = ($activeTab === $tab['id']);
        @endphp
        <a href="{{ $tab['url'] }}" 
           wire:click.prevent="setTab('{{ $tab['id'] }}')" 
           class="settings-tab-card {{ $isActive ? 'active' : '' }}"
           role="button"
           aria-pressed="{{ $isActive ? 'true' : 'false' }}">
            <div class="settings-tab-card-top">
                <div class="settings-tab-icon-box" style="background: {{ $tab['bg'] }}; color: {{ $tab['color'] }};">
                    {!! $tab['icon'] !!}
                </div>
                @if($isActive)
                    <span class="settings-tab-active-indicator">
                        <span class="active-dot"></span>
                        <span>Active</span>
                    </span>
                @else
                    <span class="settings-tab-badge" style="background: {{ $tab['bg'] }}; color: {{ $tab['color'] }};">
                        {{ $tab['badge'] }}
                    </span>
                @endif
            </div>
            <div>
                <div class="settings-tab-title">
                    {{ $tab['label'] }}
                </div>
                <div class="settings-tab-subtitle">
                    {{ $tab['subtitle'] }}
                </div>
            </div>
        </a>
    @endforeach
</div>
