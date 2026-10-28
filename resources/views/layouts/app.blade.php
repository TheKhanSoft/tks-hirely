<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Hirely - University Recruitment OS' }}</title>
    
    <!-- Modern Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="/css/app.css">
    @livewireStyles
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'system';
            if (theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.remove('light');
            } else {
                document.documentElement.classList.add('light');
            }
        })();
    </script>
</head>
<body>
    <div class="app-layout">
        <!-- Sidebar Navigation Drawer -->
        <aside class="sidebar" id="appSidebar">
            <!-- Brand Logo -->
            <a href="/" class="sidebar-brand-wrapper">
                <div class="brand-icon-box">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/>
                        <path d="M6 6h10"/>
                        <path d="M6 10h10"/>
                        <path d="M6 14h6"/>
                    </svg>
                </div>
                <div>
                    <div class="brand-title">HIRELY</div>
                    <div class="brand-badge">Recruitment OS</div>
                </div>
            </a>
            
            <nav class="sidebar-menu">
                @auth
                    @if(auth()->user()->can('view-campaign') || auth()->user()->can('view-application') || auth()->user()->can('view-basic-info') || auth()->user()->can('view-payment'))
                        <div class="sidebar-section-title">Operations</div>
                        <a href="/admin/dashboard" class="sidebar-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="7" height="9" x="3" y="3" rx="1"/>
                                <rect width="7" height="5" x="14" y="3" rx="1"/>
                                <rect width="7" height="9" x="14" y="12" rx="1"/>
                                <rect width="7" height="5" x="3" y="16" rx="1"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>

                        @can('view-campaign')
                            <a href="/admin/campaigns" class="sidebar-link {{ request()->is('admin/campaigns*') ? 'active' : '' }}">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m3 11 18-5v12L3 14v-3z"/>
                                    <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>
                                </svg>
                                <span>Job Campaigns</span>
                            </a>
                        @endcan

                        @can('view-application')
                            <a href="/admin/applications" class="sidebar-link {{ request()->is('admin/applications*') ? 'active' : '' }}">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                    <line x1="16" y1="17" x2="8" y2="17"/>
                                </svg>
                                <span>Applications</span>
                            </a>
                        @endcan

                        @can('view-basic-info')
                            <a href="/admin/candidates" class="sidebar-link {{ request()->is('admin/candidates*') ? 'active' : '' }}">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                <span>Candidate Profiles</span>
                            </a>
                        @endcan

                        @can('view-payment')
                            <a href="/admin/payments" class="sidebar-link {{ request()->is('admin/payments*') ? 'active' : '' }}">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="20" height="14" x="2" y="5" rx="2"/>
                                    <line x1="2" x2="22" y1="10" y2="10"/>
                                </svg>
                                <span>Vouchers & Payments</span>
                            </a>
                        @endcan

                        @if(auth()->user()->can('manage-reviewers') || auth()->user()->can('view-campaign'))
                            <a href="/admin/reviewers" class="sidebar-link {{ request()->is('admin/reviewers*') ? 'active' : '' }}">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                <span>Academic Reviewers</span>
                            </a>
                        @endif
                    @endif

                    @if(auth()->user()->can('view-user') || auth()->user()->can('view-role') || auth()->user()->can('view-permission') || auth()->user()->can('view-audit-log'))
                        <div class="sidebar-section-title">Administration</div>
                        
                        @can('view-user')
                            <a href="/admin/users" class="sidebar-link {{ request()->is('admin/users*') ? 'active' : '' }}">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                <span>User Accounts</span>
                            </a>
                        @endcan

                        @if(auth()->user()->can('view-role') || auth()->user()->can('view-permission'))
                            <a href="/admin/roles" class="sidebar-link {{ request()->is('admin/roles*') || request()->is('admin/permissions*') ? 'active' : '' }}">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                </svg>
                                <span>Roles & Permissions</span>
                            </a>
                        @endif

                        @can('view-audit-log')
                            <a href="/admin/audit-logs" class="sidebar-link {{ request()->is('admin/audit-logs*') ? 'active' : '' }}">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                    <line x1="16" y1="17" x2="8" y2="17"/>
                                    <polyline points="10 9 9 9 8 9"/>
                                </svg>
                                <span>Security Audit Logs</span>
                            </a>
                        @endcan
                    @endif

                    @if(auth()->user()->hasRole('scrutiny-committee') && auth()->user()->can('scrutinize-application'))
                        <div class="sidebar-section-title">Committee Desks</div>
                        <a href="/admin/dashboard" class="sidebar-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="m21 21-4.3-4.3"/>
                            </svg>
                            <span>Scrutiny Queue</span>
                        </a>
                    @endif

                    @can('process-appeal')
                        <a href="/admin/appeals" class="sidebar-link {{ request()->is('admin/appeals*') ? 'active' : '' }}">
                            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                                <path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                                <path d="M7 21h10"/>
                                <path d="M12 3v18"/>
                                <path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"/>
                            </svg>
                            <span>Grievance Review</span>
                        </a>
                    @endcan

                    @if(auth()->user()->hasRole('selection-board-member') && auth()->user()->can('evaluate-application'))
                        <a href="/admin/dashboard" class="sidebar-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                            </svg>
                            <span>Board Dashboard</span>
                        </a>
                    @endif

                    @role('academic-reviewer')
                        <div class="sidebar-section-title">Peer Review</div>
                        <a href="/reviewer/dashboard" class="sidebar-link {{ request()->is('reviewer/dashboard*') ? 'active' : '' }}">
                            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <line x1="16" y1="13" x2="8" y2="13"/>
                                <line x1="16" y1="17" x2="8" y2="17"/>
                                <polyline points="10 9 9 9 8 9"/>
                            </svg>
                            <span>Evaluation Desk</span>
                        </a>
                    @endrole

                    @role('applicant')
                        <div class="sidebar-section-title">Candidate Space</div>
                        <a href="/applicant/dashboard" class="sidebar-link {{ request()->is('applicant/dashboard') ? 'active' : '' }}">
                            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            <span>Candidate Profile</span>
                        </a>
                        <a href="/applicant/wizard" class="sidebar-link {{ request()->is('applicant/wizard') ? 'active' : '' }}">
                            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                                <path d="M15 2H9a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1Z"/>
                                <path d="m9 14 2 2 4-4"/>
                            </svg>
                            <span>Application Wizard</span>
                        </a>
                    @endrole

                    <!-- Sandbox Bank Switch Utility Link -->
                    <div class="sidebar-section-title">Fintech Sandbox</div>
                    <a href="/bank/simulator" class="sidebar-link {{ request()->is('bank/simulator*') ? 'active' : '' }}" style="border: 1px dashed var(--secondary-glow); color: var(--secondary);">
                        <svg class="nav-icon" style="color: var(--secondary);" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="14" x="2" y="5" rx="2"/>
                            <line x1="2" x2="22" y1="10" y2="10"/>
                        </svg>
                        <span>1Link Simulator</span>
                        <span class="sidebar-badge badge-appealed">Test</span>
                    </a>
                @else
                    <a href="/login" class="sidebar-link">
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                            <polyline points="10 17 15 12 10 7"/>
                            <line x1="15" x2="3" y1="12" y2="12"/>
                        </svg>
                        <span>Sign In</span>
                    </a>
                    <a href="/bank/simulator" class="sidebar-link" style="border: 1px dashed var(--secondary-glow); color: var(--secondary);">
                        <svg class="nav-icon" style="color: var(--secondary);" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="14" x="2" y="5" rx="2"/>
                            <line x1="2" x2="22" y1="10" y2="10"/>
                        </svg>
                        <span>1Link Simulator</span>
                    </a>
                @endauth
            </nav>

            <!-- Modern Segmented Theme Switcher -->
            <div class="theme-switcher-box">
                <div class="theme-segmented-group">
                    <button type="button" class="theme-btn" data-theme-val="light" title="Light Mode">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="4"/>
                            <path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/>
                        </svg>
                        Light
                    </button>
                    <button type="button" class="theme-btn" data-theme-val="dark" title="Dark Mode">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>
                        </svg>
                        Dark
                    </button>
                    <button type="button" class="theme-btn" data-theme-val="system" title="System Auto">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/>
                        </svg>
                        Auto
                    </button>
                </div>
            </div>

            @auth
                <!-- User Profile Card -->
                <div class="sidebar-user-card">
                    <div class="user-avatar-circle">
                        {{ strtoupper(substr(auth()->user()->basicInfo?->first_name ?: auth()->user()->email, 0, 1)) }}
                    </div>
                    <div class="user-meta-info">
                        <div class="user-name-title" title="{{ auth()->user()->email }}">
                            {{ auth()->user()->basicInfo ? trim(auth()->user()->basicInfo->first_name . ' ' . auth()->user()->basicInfo->last_name) : auth()->user()->email }}
                        </div>
                        <div class="user-role-pill">
                            {{ str_replace('-', ' ', auth()->user()->roles->first()?->name ?? 'User') }}
                        </div>
                    </div>
                    <form action="/logout" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn-logout-icon" title="Log out session">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <polyline points="16 17 21 12 16 7"/>
                                <line x1="21" x2="9" y1="12" y2="12"/>
                            </svg>
                        </button>
                    </form>
                </div>
            @endauth
        </aside>

        <!-- Main Content Wrapper -->
        <div class="main-content">
            <!-- Modern Topbar Navigation -->
            <header class="topbar">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <!-- Mobile Hamburger -->
                    <button type="button" id="sidebarToggle" class="btn btn-secondary btn-sm" style="display: none;" title="Toggle Sidebar">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/>
                        </svg>
                    </button>

                    <div class="topbar-breadcrumbs">
                        <span>Portal</span>
                        <span style="opacity: 0.4;">/</span>
                        <strong style="color: var(--color-text-primary); font-weight: 600;">
                            @if(request()->is('admin/dashboard*')) Administrative Control
                            @elseif(request()->is('applicant/dashboard*')) Candidate Portal
                            @elseif(request()->is('applicant/wizard*')) Application Dossier Wizard
                            @elseif(request()->is('admin/appeals*')) Grievance Appeals Workspace
                            @elseif(request()->is('admin/management*')) Entity Management Console
                            @elseif(request()->is('bank/simulator*')) 1Link / Kuickpay Switch Sandbox
                            @elseif(request()->is('board/evaluate*')) Live Selection Board Grading
                            @else Recruitment System @endif
                        </strong>
                    </div>
                </div>

                <div class="topbar-actions">
                    <div class="status-beacon">
                        <span class="beacon-dot"></span>
                        <span>System Online</span>
                    </div>

                    @auth
                        <div style="background: var(--bg-card); border: 1px solid var(--border-color); padding: 0.35rem 0.85rem; border-radius: 9999px; font-size: 0.8rem; font-weight: 700; color: var(--color-text-secondary); display: flex; align-items: center; gap: 0.4rem;">
                            <span style="color: var(--secondary);">ROLE:</span>
                            <span style="color: var(--color-text-primary); text-transform: uppercase;">
                                {{ str_replace('-', ' ', auth()->user()->roles->first()?->name ?? 'Guest') }}
                            </span>
                        </div>
                    @endauth
                </div>
            </header>

            <!-- Main Container -->
            <main class="container">
                @if (session()->has('message'))
                    <div class="card" style="border-color: rgba(16, 185, 129, 0.4); background: rgba(16, 185, 129, 0.08); margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            <span style="color: #6ee7b7; font-weight: 600; font-size: 0.95rem;">{{ session('message') }}</span>
                        </div>
                        <button type="button" style="background: none; border: none; color: #6ee7b7; font-size: 1.25rem; cursor: pointer;" onclick="this.closest('.card').remove()">×</button>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="card" style="border-color: rgba(239, 68, 68, 0.4); background: rgba(239, 68, 68, 0.08); margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--danger)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" x2="12" y1="8" y2="12"/>
                                <line x1="12" x2="12.01" y1="16" y2="16"/>
                            </svg>
                            <span style="color: #fca5a5; font-weight: 600; font-size: 0.95rem;">{{ session('error') }}</span>
                        </div>
                        <button type="button" style="background: none; border: none; color: #fca5a5; font-size: 1.25rem; cursor: pointer;" onclick="this.closest('.card').remove()">×</button>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
    <script>
        (function() {
            // Theme Switching
            const themeBtns = document.querySelectorAll('.theme-btn');
            
            function applyThemeAndActiveButton(theme, save = false) {
                const root = document.documentElement;
                
                if (save) {
                    localStorage.setItem('theme', theme);
                }
                
                if (theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    root.classList.remove('light');
                } else {
                    root.classList.add('light');
                }
                
                themeBtns.forEach(btn => {
                    if (btn.getAttribute('data-theme-val') === theme) {
                        btn.classList.add('active');
                    } else {
                        btn.classList.remove('active');
                    }
                });
            }

            themeBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const selectedTheme = this.getAttribute('data-theme-val');
                    applyThemeAndActiveButton(selectedTheme, true);
                });
            });

            const currentTheme = localStorage.getItem('theme') || 'system';
            applyThemeAndActiveButton(currentTheme);

            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                if (localStorage.getItem('theme') === 'system' || !localStorage.getItem('theme')) {
                    applyThemeAndActiveButton('system');
                }
            });

            // Mobile sidebar toggle
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('appSidebar');
            if (sidebarToggle && sidebar) {
                sidebarToggle.style.display = window.innerWidth <= 768 ? 'inline-flex' : 'none';
                window.addEventListener('resize', () => {
                    sidebarToggle.style.display = window.innerWidth <= 768 ? 'inline-flex' : 'none';
                });
                sidebarToggle.addEventListener('click', () => {
                    sidebar.classList.toggle('open');
                });
            }
        })();
    </script>
</body>
</html>
