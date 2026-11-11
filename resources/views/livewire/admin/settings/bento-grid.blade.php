@php
    $basePrefix = request()->is('registrar*') ? '/registrar' : '/admin';

    $bentoCards = [
        [
            'id' => 'pipeline',
            'slug' => 'workflows',
            'url' => $basePrefix . '/settings/workflows',
            'label' => 'Workflow Pipelines',
            'badge' => 'GATES & QUORUM',
            'subtitle' => 'Milestones, quorums & stage gating',
            'color' => '#6366f1',
            'bg' => 'rgba(99, 102, 241, 0.12)',
            'icon' => '<svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>',
        ],
        [
            'id' => 'documents',
            'slug' => 'documents',
            'url' => $basePrefix . '/settings/documents',
            'label' => 'Document Proofs',
            'badge' => 'COMPLIANCE',
            'subtitle' => 'Dossier uploads & verification rules',
            'color' => '#06b6d4',
            'bg' => 'rgba(6, 182, 212, 0.12)',
            'icon' => '<svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>',
        ],
        [
            'id' => 'candidate_rules',
            'slug' => 'candidate-rules',
            'url' => $basePrefix . '/settings/candidate-rules',
            'label' => 'Candidate Policies',
            'badge' => 'INTAKE CONTROLS',
            'subtitle' => 'Limits, age cutoffs & draft quotas',
            'color' => '#8b5cf6',
            'bg' => 'rgba(139, 92, 246, 0.12)',
            'icon' => '<svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
        ],
        [
            'id' => 'academic_criteria',
            'slug' => 'academic-criteria',
            'url' => $basePrefix . '/settings/academic-criteria',
            'label' => 'Academic Benchmarks',
            'badge' => 'HEC STANDARDS',
            'subtitle' => 'HEC thresholds, CGPA & division rules',
            'color' => '#f59e0b',
            'bg' => 'rgba(245, 158, 11, 0.12)',
            'icon' => '<svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m4 6 8-4 8 4-8 4-8-4Z"/><path d="m18 10 4 2v6a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-6l4-2"/><path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5"/></svg>',
        ],
        [
            'id' => 'organization',
            'slug' => 'organization',
            'url' => $basePrefix . '/settings/organization',
            'label' => 'Institution Profile & Acronym',
            'badge' => 'LEGAL ENTITY',
            'subtitle' => 'Org. title, acronym, letterhead & fee',
            'color' => '#10b981',
            'bg' => 'rgba(16, 185, 129, 0.12)',
            'icon' => '<svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2" ry="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M12 6h.01"/><path d="M12 10h.01"/><path d="M12 14h.01"/><path d="M16 10h.01"/><path d="M16 14h.01"/><path d="M8 10h.01"/><path d="M8 14h.01"/></svg>',
        ],
        [
            'id' => 'branding',
            'slug' => 'branding',
            'url' => $basePrefix . '/settings/branding',
            'label' => 'Theme & Design System',
            'badge' => 'VISUAL IDENTITY',
            'subtitle' => 'Palettes, presets, radius & layout',
            'color' => '#ec4899',
            'bg' => 'rgba(236, 72, 153, 0.12)',
            'icon' => '<svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="13.5" cy="6.5" r=".5" fill="currentColor"/><circle cx="17.5" cy="10.5" r=".5" fill="currentColor"/><circle cx="8.5" cy="7.5" r=".5" fill="currentColor"/><circle cx="6.5" cy="12.5" r=".5" fill="currentColor"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z"/></svg>',
        ],
        [
            'id' => 'contact',
            'slug' => 'contact',
            'url' => $basePrefix . '/settings/contact',
            'label' => 'Contact & Helpdesk',
            'badge' => 'COMMUNICATIONS',
            'subtitle' => 'Official hotlines, desk & address',
            'color' => '#14b8a6',
            'bg' => 'rgba(20, 184, 166, 0.12)',
            'icon' => '<svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
        ],
        [
            'id' => 'email',
            'slug' => 'email',
            'url' => $basePrefix . '/settings/email',
            'label' => 'Email & Delivery',
            'badge' => 'SMTP PROTOCOL',
            'subtitle' => 'SMTP gateway, TLS & live tester',
            'color' => '#3b82f6',
            'bg' => 'rgba(59, 130, 246, 0.12)',
            'icon' => '<svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>',
        ],
        [
            'id' => 'email_templates',
            'slug' => 'email-templates',
            'url' => $basePrefix . '/settings/email-templates',
            'label' => 'Email Templates & Triggers',
            'badge' => 'COMMUNICATIONS',
            'subtitle' => 'Email templates & custom desk',
            'color' => '#8b5cf6',
            'bg' => 'rgba(139, 92, 246, 0.12)',
            'icon' => '<svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>',
        ],
        [
            'id' => 'security',
            'slug' => 'security',
            'url' => $basePrefix . '/settings/security',
            'label' => 'Security & Access',
            'badge' => '2FA & SESSIONS',
            'subtitle' => 'Auth policies, lockout & uploads',
            'color' => '#ef4444',
            'bg' => 'rgba(239, 68, 68, 0.12)',
            'icon' => '<svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/><circle cx="12" cy="16" r="1"/></svg>',
        ],
        [
            'id' => 'system',
            'slug' => 'system',
            'url' => $basePrefix . '/settings/system',
            'label' => 'System & Health',
            'badge' => 'DIAGNOSTICS',
            'subtitle' => 'Server runtime, cache & maintenance',
            'color' => '#64748b',
            'bg' => 'rgba(100, 116, 139, 0.12)',
            'icon' => '<svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>',
        ],
    ];

    // 🔒 System & Health diagnostics/maintenance is strictly Super-Admin tier
    if (!auth()->user()->hasRole('super-admin') && !auth()->user()->can('view-system-health')) {
        $bentoCards = array_filter($bentoCards, fn($c) => $c['id'] !== 'system');
    }
@endphp

<!-- Bento Hub Card Grid (Modern 4-Column Layout) -->
<div class="settings-hub-grid" id="settingsBentoHub">
    @foreach($bentoCards as $card)
        @php
            $isActive = ($activeTab === $card['id']);
        @endphp
        <a href="{{ $card['url'] }}" 
           wire:click.prevent="setTab('{{ $card['id'] }}')" 
           class="settings-tab-card {{ $isActive ? 'active' : '' }}"
           role="button"
           aria-pressed="{{ $isActive ? 'true' : 'false' }}">
            
            @if($isActive)
                <span class="settings-tab-corner-dot" aria-label="Active Tab" title="Active Tab"></span>
            @endif

            <div class="settings-tab-card-header">
                <div class="settings-tab-icon-box" style="background: {{ $card['bg'] }}; color: {{ $card['color'] }};">
                    {!! $card['icon'] !!}
                </div>
                <div class="settings-tab-card-text">
                    <div class="settings-tab-title">
                        {{ $card['label'] }}
                    </div>
                    <div class="settings-tab-subtitle">
                        {{ $card['subtitle'] }}
                    </div>
                </div>
            </div>
        </a>
    @endforeach
</div>
