@php
    $basePrefix = request()->is('registrar*') ? '/registrar' : '/admin';

    $navGroups = [
        [
            'title' => 'Institution & Identity',
            'icon' => '🏛️',
            'tag' => 'Primary Brand',
            'tabs' => [
                [
                    'id' => 'organization',
                    'slug' => 'organization',
                    'url' => $basePrefix . '/settings/organization',
                    'label' => 'Institution Profile & Acronym',
                    'badge' => 'AWKUM',
                    'subtitle' => 'Nomenclature, letterhead & fee',
                    'color' => '#10b981',
                    'bg' => 'rgba(16, 185, 129, 0.12)',
                    'icon' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2" ry="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M12 6h.01"/><path d="M12 10h.01"/><path d="M12 14h.01"/><path d="M16 10h.01"/><path d="M16 14h.01"/><path d="M8 10h.01"/><path d="M8 14h.01"/></svg>',
                ],
                [
                    'id' => 'branding',
                    'slug' => 'branding',
                    'url' => $basePrefix . '/settings/branding',
                    'label' => 'Theme Studio & Design System',
                    'badge' => 'Visual Identity',
                    'subtitle' => 'Palettes, presets, radius & layout',
                    'color' => '#ec4899',
                    'bg' => 'rgba(236, 72, 153, 0.12)',
                    'icon' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="13.5" cy="6.5" r=".5" fill="currentColor"/><circle cx="17.5" cy="10.5" r=".5" fill="currentColor"/><circle cx="8.5" cy="7.5" r=".5" fill="currentColor"/><circle cx="6.5" cy="12.5" r=".5" fill="currentColor"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z"/></svg>',
                ],
            ]
        ],
        [
            'title' => 'Recruitment & Academic Governance',
            'icon' => '📋',
            'tag' => 'Business Logic',
            'tabs' => [
                [
                    'id' => 'pipeline',
                    'slug' => 'workflows',
                    'url' => $basePrefix . '/settings/workflows',
                    'label' => 'Workflow Pipelines',
                    'badge' => 'Gates & Quorum',
                    'subtitle' => 'Milestones, quorums & stage gating',
                    'color' => '#6366f1',
                    'bg' => 'rgba(99, 102, 241, 0.12)',
                    'icon' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>',
                ],
                [
                    'id' => 'academic_criteria',
                    'slug' => 'academic-criteria',
                    'url' => $basePrefix . '/settings/academic-criteria',
                    'label' => 'Academic Benchmarks & CGPA',
                    'badge' => 'HEC Standards',
                    'subtitle' => 'HEC thresholds, CGPA & division rules',
                    'color' => '#f59e0b',
                    'bg' => 'rgba(245, 158, 11, 0.12)',
                    'icon' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m4 6 8-4 8 4-8 4-8-4Z"/><path d="m18 10 4 2v6a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-6l4-2"/><path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5"/></svg>',
                ],
                [
                    'id' => 'candidate_rules',
                    'slug' => 'candidate-rules',
                    'url' => $basePrefix . '/settings/candidate-rules',
                    'label' => 'Candidate Policies & Limits',
                    'badge' => 'Intake Controls',
                    'subtitle' => 'Limits, age cutoffs & draft quotas',
                    'color' => '#8b5cf6',
                    'bg' => 'rgba(139, 92, 246, 0.12)',
                    'icon' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
                ],
                [
                    'id' => 'documents',
                    'slug' => 'documents',
                    'url' => $basePrefix . '/settings/documents',
                    'label' => 'Document Proofs & Compliance',
                    'badge' => 'Compliance',
                    'subtitle' => 'Dossier uploads & verification rules',
                    'color' => '#0ea5e9',
                    'bg' => 'rgba(14, 165, 233, 0.12)',
                    'icon' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>',
                ],
            ]
        ],
        [
            'title' => 'Communications & Messaging',
            'icon' => '✉️',
            'tag' => 'Notifications',
            'tabs' => [
                [
                    'id' => 'email_templates',
                    'slug' => 'email-templates',
                    'url' => $basePrefix . '/settings/email-templates',
                    'label' => 'Email Templates & Triggers',
                    'badge' => '32 Templates',
                    'subtitle' => 'Email templates & custom desk',
                    'color' => '#8b5cf6',
                    'bg' => 'rgba(139, 92, 246, 0.12)',
                    'icon' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>',
                ],
                [
                    'id' => 'email',
                    'slug' => 'email',
                    'url' => $basePrefix . '/settings/email',
                    'label' => 'Email Delivery & SMTP Gateway',
                    'badge' => 'SMTP Protocol',
                    'subtitle' => 'SMTP gateway, TLS & live tester',
                    'color' => '#3b82f6',
                    'bg' => 'rgba(59, 130, 246, 0.12)',
                    'icon' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>',
                ],
                [
                    'id' => 'contact',
                    'slug' => 'contact',
                    'url' => $basePrefix . '/settings/contact',
                    'label' => 'Contact & Helpdesk Desks',
                    'badge' => 'Support',
                    'subtitle' => 'Official hotlines, desk & address',
                    'color' => '#14b8a6',
                    'bg' => 'rgba(20, 184, 166, 0.12)',
                    'icon' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
                ],
            ]
        ],
        [
            'title' => 'Platform & Governance',
            'icon' => '🛡️',
            'tag' => 'System',
            'tabs' => [
                [
                    'id' => 'security',
                    'slug' => 'security',
                    'url' => $basePrefix . '/settings/security',
                    'label' => 'Security & Access Policies',
                    'badge' => '2FA & Sessions',
                    'subtitle' => 'Auth policies, lockout & uploads',
                    'color' => '#e11d48',
                    'bg' => 'rgba(225, 29, 72, 0.12)',
                    'icon' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/><circle cx="12" cy="16" r="1"/></svg>',
                ],
                [
                    'id' => 'system',
                    'slug' => 'system',
                    'url' => $basePrefix . '/settings/system',
                    'label' => 'System Diagnostics & Health',
                    'badge' => 'Diagnostics',
                    'subtitle' => 'Server runtime, cache & maintenance',
                    'color' => '#64748b',
                    'bg' => 'rgba(100, 116, 139, 0.12)',
                    'icon' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4"/><path d="m4.93 4.93 2.83 2.83"/><path d="M2 12h4"/><path d="m4.93 19.07 2.83-2.83"/><path d="M12 18v4"/><path d="m19.07 19.07-2.83-2.83"/><path d="M18 12h4"/><path d="m19.07 4.93-2.83 2.83"/><circle cx="12" cy="12" r="3"/></svg>',
                ],
            ]
        ],
    ];
@endphp

<!-- Master-Detail Navigation Sidebar -->
<aside class="settings-master-sidebar">
    <!-- Live Instant Filter Input -->
    <div class="settings-sidebar-search-box">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--color-text-muted);">
            <circle cx="11" cy="11" r="8"/>
            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" 
               x-model="settingsFilter" 
               placeholder="Filter settings..." 
               class="settings-sidebar-search-input"
               aria-label="Filter Settings Modules">
        <button type="button" 
                x-show="settingsFilter" 
                x-on:click="settingsFilter = ''" 
                style="background: none; border: none; color: var(--color-text-muted); cursor: pointer; font-size: 0.75rem; padding: 0 0.2rem;">
            ✕
        </button>
    </div>

    <!-- Navigation Groups -->
    <div class="settings-sidebar-groups-wrapper">
        @foreach($navGroups as $group)
            <div class="settings-nav-group">
                <div class="settings-nav-group-header">
                    <div style="display: flex; align-items: center; gap: 0.4rem;">
                        <span>{{ $group['icon'] }}</span>
                        <span>{{ $group['title'] }}</span>
                    </div>
                    <span class="settings-nav-group-count">{{ count($group['tabs']) }}</span>
                </div>

                <div class="settings-nav-items-list">
                    @foreach($group['tabs'] as $tab)
                        @php
                            $isActive = ($activeTab === $tab['id']);
                        @endphp
                        <a href="{{ $tab['url'] }}" 
                           wire:click.prevent="setTab('{{ $tab['id'] }}')" 
                           class="settings-nav-item {{ $isActive ? 'active' : '' }}"
                           x-show="!settingsFilter || ('{{ strtolower($tab['label'] . ' ' . $tab['subtitle'] . ' ' . $tab['badge']) }}').includes(settingsFilter.toLowerCase())"
                           role="button"
                           aria-pressed="{{ $isActive ? 'true' : 'false' }}">
                            <div class="settings-nav-item-left">
                                <div class="settings-nav-item-icon" style="background: {{ $tab['bg'] }}; color: {{ $tab['color'] }};">
                                    {!! $tab['icon'] !!}
                                </div>
                                <div class="settings-nav-item-text">
                                    <div class="settings-nav-item-title">{{ $tab['label'] }}</div>
                                    <div class="settings-nav-item-subtitle">{{ $tab['subtitle'] }}</div>
                                </div>
                            </div>
                            @if($isActive)
                                <span class="settings-nav-active-pill">
                                    <span class="active-dot"></span>
                                </span>
                            @else
                                <span class="settings-nav-badge" style="background: {{ $tab['bg'] }}; color: {{ $tab['color'] }};">
                                    {{ $tab['badge'] }}
                                </span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</aside>
