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
                    'badge' => '36 Templates',
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

<!-- Master-Detail Navigation Sidebar with Deep Settings Search Engine -->
<aside class="settings-master-sidebar"
       x-data="{
           searchQuery: '',
           isDropdownOpen: false,
           selectedIndex: 0,
           deepSettings: [
               // 1. Institution & Identity
               { title: 'Organization / University Full Legal Name', tab: 'organization', tabLabel: 'Institution Profile', section: 'Nomenclature', target_id: 'setting-org-legal-name', icon: '🏛️', kw: 'name legal title university awkum mardan abdul wali khan full' },
               { title: 'Short Name / Acronym (AWKUM)', tab: 'organization', tabLabel: 'Institution Profile', section: 'Nomenclature', target_id: 'setting-org-short-name', icon: '🏛️', kw: 'acronym short name abbreviation awkum branding' },
               { title: 'Institutional Jurisdiction & Location', tab: 'organization', tabLabel: 'Institution Profile', section: 'Jurisdiction', target_id: 'setting-org-jurisdiction', icon: '📍', kw: 'location province jurisdiction khyber pakhtunkhwa pakistan' },
               { title: 'Executing Directorate / Office Name', tab: 'organization', tabLabel: 'Institution Profile', section: 'Directorate', target_id: 'setting-org-directorate', icon: '🏢', kw: 'office directorate registrar hr administration executive' },
               { title: 'Organizational Sector / Governance Model', tab: 'organization', tabLabel: 'Institution Profile', section: 'Sector Model', target_id: 'setting-org-sector-model', icon: '🏛️', kw: 'sector governance taxonomy model hec faculty department' },
               { title: 'Academic / Departmental Taxonomy Label', tab: 'organization', tabLabel: 'Institution Profile', section: 'Taxonomy', target_id: 'setting-org-taxonomy-label', icon: '🏷️', kw: 'taxonomy label department faculty division directorate' },
               { title: 'System Currency Symbol & Code', tab: 'organization', tabLabel: 'Institution Profile', section: 'Currency', target_id: 'setting-org-currency', icon: '💰', kw: 'currency pkr rs symbol code fee money transaction' },
               { title: 'Default Baseline Application Intake Fee', tab: 'organization', tabLabel: 'Institution Profile', section: 'Fee Structure', target_id: 'setting-org-baseline-fee', icon: '💳', kw: 'fee baseline intake 3000 challan payment price voucher' },
               { title: 'Printable Letterhead Studio & Seal', tab: 'organization', tabLabel: 'Institution Profile', section: 'Letterhead Studio', target_id: 'setting-org-letterhead-studio', icon: '📜', kw: 'letterhead seal logo header address watermark candidate dossier print' },

               // 2. Academic Division & Benchmarks
               { title: 'Zero 3rd Division Allowed in Career', tab: 'academic_criteria', tabLabel: 'Academic Benchmarks', section: 'Division Rules', target_id: 'setting-academic-disallow-3rd-career', icon: '⚖️', kw: '3rd division third career zero tolerance matric fsc bs bar disqualify' },
               { title: 'Disallow 3rd Division in Terminal Degree', tab: 'academic_criteria', tabLabel: 'Academic Benchmarks', section: 'Division Rules', target_id: 'setting-academic-disallow-3rd-terminal', icon: '🎓', kw: 'terminal highest degree third 3rd division disqualify' },
               { title: 'Maximum Permitted 3rd Divisions Cap', tab: 'academic_criteria', tabLabel: 'Academic Benchmarks', section: 'Division Rules', target_id: 'setting-academic-max-3rd-divisions', icon: '⚖️', kw: 'cap max 3rd third division numerical limit' },
               { title: 'Disallow Multiple 2nd Divisions (Max 1)', tab: 'academic_criteria', tabLabel: 'Academic Benchmarks', section: 'Division Rules', target_id: 'setting-academic-disallow-two-2nd', icon: '⚖️', kw: '2nd division second multiple one allowed only second divisions' },
               { title: 'Global Minimum Academic Aggregate %', tab: 'academic_criteria', tabLabel: 'Academic Benchmarks', section: 'Marks Thresholds', target_id: 'setting-academic-aggregate-pct', icon: '🎯', kw: 'aggregate marks percentage 60 cutoff career qualification' },
               { title: 'Terminal Degree Minimum Marks %', tab: 'academic_criteria', tabLabel: 'Academic Benchmarks', section: 'Marks Thresholds', target_id: 'setting-academic-terminal-pct', icon: '🎓', kw: 'terminal percentage marks 65 score highest degree' },
               { title: 'Terminal Degree Minimum CGPA (4.00 Scale)', tab: 'academic_criteria', tabLabel: 'Academic Benchmarks', section: 'CGPA Thresholds', target_id: 'setting-academic-cgpa', icon: '📊', kw: 'cgpa 3.00 4.00 terminal gpa semester transcript grade point hec' },
               { title: 'Statutory Peer Referee Quotas by Rank', tab: 'academic_criteria', tabLabel: 'Academic Benchmarks', section: 'Peer Review Quota', target_id: 'setting-academic-peer-quotas', icon: '👨‍🏫', kw: 'peer referee quota 3 experts assistant professor lecturer reports' },
               { title: 'HEC Mandatory Terminal PhD Faculty Standard', tab: 'academic_criteria', tabLabel: 'Academic Benchmarks', section: 'HEC Standards', target_id: 'setting-academic-hec-hjrs', icon: '🎓', kw: 'phd terminal 18 years hec faculty associate professor professor qualification' },
               { title: 'Mandatory Foreign Degree Equivalence Certificate', tab: 'academic_criteria', tabLabel: 'Academic Benchmarks', section: 'HEC Standards', target_id: 'setting-academic-hec-hjrs', icon: '📜', kw: 'foreign degree equivalence hec ibcc recognition abroad overseas' },
               { title: 'HJRS Journal Category W/X/Y Scoring Weights', tab: 'academic_criteria', tabLabel: 'Academic Benchmarks', section: 'HJRS Publications', target_id: 'setting-academic-hec-hjrs', icon: '📑', kw: 'hjrs category w x y publication marks weight impact factor journal' },

               // 3. Candidate Policies & Intake
               { title: 'Allow Multiple Active Applications Across Posts', tab: 'candidate_rules', tabLabel: 'Candidate Policies', section: 'Intake Controls', target_id: 'setting-candidate-multi-app', icon: '🌐', kw: 'multiple applications multi-post active positions intake' },
               { title: 'Strict Duplicate Application Blocking', tab: 'candidate_rules', tabLabel: 'Candidate Policies', section: 'Fraud Prevention', target_id: 'setting-candidate-duplicate-block', icon: '🛡️', kw: 'duplicate block applications same position cnic fraud' },
               { title: 'Auto-Lock Profile on Voucher Generation', tab: 'candidate_rules', tabLabel: 'Candidate Policies', section: 'Tamper Protection', target_id: 'setting-candidate-profile-lock', icon: '🔒', kw: 'auto lock profile voucher tamper freeze degrees' },
               { title: 'Bank Fee Settlement Grace Window (Days)', tab: 'candidate_rules', tabLabel: 'Candidate Policies', section: 'Settlement Window', target_id: 'setting-candidate-grace-period', icon: '⏳', kw: 'grace period challan days 14 bank expiration window' },
               { title: 'Daily Application Closing Cutoff Time (PKT)', tab: 'candidate_rules', tabLabel: 'Candidate Policies', section: 'Intake Controls', target_id: 'setting-candidate-cutoff-time', icon: '⏰', kw: 'cutoff time closing deadline midnight 23:59:59 pkt application hours' },
               { title: 'Late Application Submissions with Surcharge', tab: 'candidate_rules', tabLabel: 'Candidate Policies', section: 'Late Fee & Grace', target_id: 'setting-candidate-late-fee', icon: '⏳', kw: 'late fee submission grace days surcharge amount penalty deadline extended' },
               { title: 'Aggregate Dossier Upload Ceiling (MB)', tab: 'candidate_rules', tabLabel: 'Candidate Policies', section: 'Storage Quota', target_id: 'setting-candidate-dossier-ceiling', icon: '📦', kw: 'dossier upload limit ceiling aggregate megabytes mb storage size' },
               { title: 'Strict Profile Lock Upon Final Submission', tab: 'candidate_rules', tabLabel: 'Candidate Policies', section: 'Tamper Protection', target_id: 'setting-candidate-lock-dossier', icon: '🔒', kw: 'lock freeze profile submission non repudiable tamper immutable' },

               // 4. Workflow Pipelines & Gating
               { title: 'Scrutiny Committee Dossier Vetting Gate', tab: 'pipeline', tabLabel: 'Workflow Pipelines', section: 'Stage Gating', target_id: 'setting-pipeline-scrutiny-gate', icon: '📑', kw: 'scrutiny gate stage committee verification pre-qualification' },
               { title: 'External Peer Review Evaluation Milestone', tab: 'pipeline', tabLabel: 'Workflow Pipelines', section: 'Stage Gating', target_id: 'setting-pipeline-peer-step', icon: '🔬', kw: 'peer review external referee stage gating quota evaluation' },

               // 5. Email & SMTP Delivery
               { title: 'Outgoing Sender Identity & Header Labels', tab: 'email', tabLabel: 'Email Delivery', section: 'Sender Identity', target_id: 'setting-email-from', icon: '👤', kw: 'sender from email name address reply-to' },
               { title: 'Mail Protocol & Driver (SMTP / Mailpit)', tab: 'email', tabLabel: 'Email Delivery', section: 'SMTP Server', target_id: 'setting-email-mailer', icon: '⚡', kw: 'mailer driver smtp mailpit sendmail protocol' },
               { title: 'SMTP Server Host Server', tab: 'email', tabLabel: 'Email Delivery', section: 'SMTP Server', target_id: 'setting-email-host', icon: '🖥️', kw: 'host smtp mailpit localhost mail server 127.0.0.1' },
               { title: 'SMTP Port (1025 Mailpit / 587 TLS / 465 SSL)', tab: 'email', tabLabel: 'Email Delivery', section: 'SMTP Server', target_id: 'setting-email-port', icon: '🔌', kw: 'port 1025 587 465 mailpit smtp network tls ssl' },
               { title: 'SMTP Security Encryption (TLS / SSL / None)', tab: 'email', tabLabel: 'Email Delivery', section: 'SMTP Server', target_id: 'setting-email-encryption', icon: '🔒', kw: 'encryption tls ssl none starttls handshake' },
               { title: 'WhatsApp Business Automated Alert Gateway', tab: 'email', tabLabel: 'Email Delivery', section: 'Multi-Channel', target_id: 'setting-email-whatsapp', icon: '💬', kw: 'whatsapp business alerts phone notifications credentials password dispatch' },

               // 6. Security Governance
               { title: 'Two-Factor Authentication (2FA) Policy', tab: 'security', tabLabel: 'Security & Access', section: '2FA Policy', target_id: 'setting-security-2fa', icon: '🔐', kw: '2fa two factor authentication otp mfa mandatory staff' },
               { title: 'Idle Session Inactivity Timeout (Minutes)', tab: 'security', tabLabel: 'Security & Access', section: 'Session Policy', target_id: 'setting-security-session-timeout', icon: '⏱️', kw: 'session timeout inactivity idle minutes logout' },
               { title: 'Max Consecutive Failed Login Attempts', tab: 'security', tabLabel: 'Security & Access', section: 'Brute Force', target_id: 'setting-security-failed-attempts', icon: '🛡️', kw: 'failed login attempts rate limit lockout brute force' },
               { title: 'Account Lockout Suspension Duration', tab: 'security', tabLabel: 'Security & Access', section: 'Lockout', target_id: 'setting-security-lockout-duration', icon: '⏳', kw: 'lockout duration suspension cooldown minutes' },
               { title: 'Session IP Binding & Anti-Hijacking', tab: 'security', tabLabel: 'Security & Access', section: 'Hardening', target_id: 'setting-security-advanced-hardening', icon: '🛡️', kw: 'ip binding session hijacking cookie lock security network' },
               { title: 'SHA-256 Tamper-Evident Dossier Fingerprinting', tab: 'security', tabLabel: 'Security & Access', section: 'Digital Integrity', target_id: 'setting-security-advanced-hardening', icon: '🔒', kw: 'sha 256 hash fingerprint tamper evident digital proof cryptographic' },
               { title: 'Official Security Watermark Text', tab: 'security', tabLabel: 'Security & Access', section: 'Digital Integrity', target_id: 'setting-security-advanced-hardening', icon: '📄', kw: 'watermark print dossier security confidential official record' },
           ],
           get searchMatches() {
               if (!this.searchQuery || this.searchQuery.trim().length < 2) return [];
               const q = this.searchQuery.toLowerCase().trim();
               return this.deepSettings.filter(item => {
                   return item.title.toLowerCase().includes(q) ||
                          item.tabLabel.toLowerCase().includes(q) ||
                          item.section.toLowerCase().includes(q) ||
                          item.kw.toLowerCase().includes(q);
               }).slice(0, 8);
           },
           navigateToSetting(item) {
               const targetTab = item.tab;
               const targetId = item.target_id;
               this.searchQuery = '';
               this.isDropdownOpen = false;

               if (window.Livewire) {
                   $wire.setTab(targetTab);
               }

               setTimeout(() => {
                   const el = document.getElementById(targetId);
                   if (el) {
                       el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                       el.classList.add('setting-focus-pulse');
                       setTimeout(() => el.classList.remove('setting-focus-pulse'), 2500);
                   }
               }, 320);
           }
       }">

    <!-- Live Instant Filter Input with Autocomplete Dropdown -->
    <div class="settings-sidebar-search-box" style="position: relative;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--color-text-muted);">
            <circle cx="11" cy="11" r="8"/>
            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" 
               x-model="searchQuery" 
               x-on:input="isDropdownOpen = searchQuery.trim().length >= 2; selectedIndex = 0;"
               x-on:focus="isDropdownOpen = searchQuery.trim().length >= 2;"
               x-on:keydown.arrow-down.prevent="if (searchMatches.length) selectedIndex = (selectedIndex + 1) % searchMatches.length"
               x-on:keydown.arrow-up.prevent="if (searchMatches.length) selectedIndex = (selectedIndex - 1 + searchMatches.length) % searchMatches.length"
               x-on:keydown.enter.prevent="if (searchMatches.length && searchMatches[selectedIndex]) navigateToSetting(searchMatches[selectedIndex])"
               x-on:keydown.escape="isDropdownOpen = false"
               placeholder="Search settings, CGPA, AWKUM, fees..." 
               class="settings-sidebar-search-input"
               aria-label="Search Entire Settings Engine">
        <button type="button" 
                x-show="searchQuery" 
                x-on:click="searchQuery = ''; isDropdownOpen = false;" 
                style="background: none; border: none; color: var(--color-text-muted); cursor: pointer; font-size: 0.75rem; padding: 0 0.2rem;"
                title="Clear Search">
            ✕
        </button>

        <!-- Deep Autocomplete Dropdown -->
        <div x-show="isDropdownOpen && searchMatches.length > 0" 
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="settings-search-dropdown"
             x-on:click.outside="isDropdownOpen = false"
             style="display: none;">
            <div style="padding: 0.35rem 0.6rem 0.45rem; font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-muted); border-bottom: 1px solid var(--border-color); margin-bottom: 0.35rem; display: flex; align-items: center; justify-content: space-between;">
                <span>Deep Matches (<span x-text="searchMatches.length"></span>)</span>
                <span style="font-size: 0.62rem; text-transform: none; opacity: 0.8;">Enter ↵ to jump</span>
            </div>
            <template x-for="(match, index) in searchMatches" :key="match.target_id + match.title">
                <div class="settings-search-item" 
                     :style="selectedIndex === index ? 'background: var(--bg-surface); border-color: var(--primary);' : ''"
                     x-on:click="navigateToSetting(match)"
                     x-on:mouseenter="selectedIndex = index">
                    <div style="display: flex; align-items: center; gap: 0.55rem; min-width: 0;">
                        <span x-text="match.icon" style="font-size: 1.05rem; flex-shrink: 0;"></span>
                        <div style="min-width: 0;">
                            <div style="font-weight: 700; font-size: 0.82rem; color: var(--color-text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" x-text="match.title"></div>
                            <div style="font-size: 0.68rem; color: var(--color-text-muted); display: flex; align-items: center; gap: 0.35rem;">
                                <span style="color: var(--primary); font-weight: 700;" x-text="match.tabLabel"></span>
                                <span>•</span>
                                <span x-text="match.section"></span>
                            </div>
                        </div>
                    </div>
                    <span class="badge badge-slate" style="font-size: 0.65rem; font-weight: 700; flex-shrink: 0;">Jump ↵</span>
                </div>
            </template>
        </div>
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
                           x-show="!searchQuery || ('{{ strtolower($tab['label'] . ' ' . $tab['subtitle'] . ' ' . $tab['badge']) }}').includes(searchQuery.toLowerCase())"
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
                            
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</aside>
