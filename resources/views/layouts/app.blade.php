@php
    $sysAppName = \App\Models\SystemSetting::getAppName();
    $sysOrgName = \App\Models\SystemSetting::getOrgName();
    $sysOrgShortName = \App\Models\SystemSetting::getOrgShortName();
    $sysBrandDisplay = \App\Models\SystemSetting::getBrandDisplayName();
    $sysAppLogo = \App\Models\SystemSetting::getAppLogo();
    $sysAppFavicon = \App\Models\SystemSetting::getAppFavicon();
    $sysAppVersion = \App\Models\SystemSetting::get('app_version', 'v2.4 Enterprise OS');
    $sysAccentColor = \App\Models\SystemSetting::get('theme_accent_color', '#4f46e5');
    $announcementActive = (bool) \App\Models\SystemSetting::get('portal_announcement_enabled', false);
    $announcementType = \App\Models\SystemSetting::get('portal_announcement_type', 'info');
    $announcementMsg = \App\Models\SystemSetting::get('portal_announcement_message', '');
    $footerCopyright = \App\Models\SystemSetting::get('footer_copyright_text', '© ' . date('Y') . ' ' . $sysOrgName . ' (' . $sysOrgShortName . '). All rights reserved.');
    $supportEmail = \App\Models\SystemSetting::get('support_email');
    $supportPhone = \App\Models\SystemSetting::get('support_phone');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? ($sysBrandDisplay . ' — ' . $sysOrgName) }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    
    <!-- Modern Favicons & Brand Assets -->
    <link rel="icon" href="{{ $sysAppFavicon }}">
    <link rel="shortcut icon" href="{{ $sysAppFavicon }}">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="{{ $sysAccentColor }}">
    
    {{-- Dynamic Institutional Theme Variables (Primary, Secondary, Radius, Fonts, Shadows, Density) --}}
    {!! \App\Models\SystemSetting::renderThemeCss() !!}

    <!-- Modern Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="/css/app.css?v={{ @filemtime(public_path('css/app.css')) ?: time() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'system';
            if (theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.remove('light');
            } else {
                document.documentElement.classList.add('light');
            }
            if (localStorage.getItem('hirely_sidebar_collapsed') === 'true') {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        })();
    </script>
</head>
@php
    $themeVars = \App\Models\SystemSetting::getThemeVariables();
@endphp
<body class="theme-density-{{ $themeVars['density'] }} theme-radius-{{ $themeVars['radiusStyle'] }} theme-elevation-{{ $themeVars['elevation'] }}">
    <!-- Global Floating Rail Tooltip -->
    <div id="sidebarRailTooltip" class="sidebar-rail-tooltip" role="tooltip" aria-hidden="true"></div>

    <div class="app-layout">
        <!-- Sidebar Navigation Drawer -->
        <aside class="sidebar sidebar-{{ $themeVars['sidebarStyle'] }}" id="appSidebar">
            <!-- Brand Logo & Header Rail Toggle -->
            <div class="sidebar-header-box">
                <a href="/" class="sidebar-brand-wrapper" title="{{ $sysOrgName }}" data-tooltip="{{ $sysBrandDisplay }}">
                    <div class="brand-icon-box" style="padding: 0; background: transparent; box-shadow: none; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                        <img src="{{ $sysAppLogo }}" alt="{{ $sysOrgShortName }}" width="34" height="34" style="border-radius: 9px; display: block; box-shadow: 0 4px 14px var(--primary-glow); object-fit: contain;">
                    </div>
                    <div class="brand-meta-box" style="overflow: hidden; min-width: 0;">
                        <div class="brand-title" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $sysOrgName }}">{{ strtoupper($sysBrandDisplay) }}</div>
                        <div class="brand-badge" style="white-space: nowrap;"><span class="badge-dot" style="background: currentColor;"></span> {{ $sysAppVersion }}</div>
                    </div>
                </a>

                <button type="button" 
                        class="sidebar-header-toggle-btn" 
                        id="sidebarRailToggle" 
                        title="Toggle Sidebar Rail (Ctrl + B)" 
                        aria-label="Toggle Sidebar Rail"
                        data-tooltip="Toggle Sidebar (Ctrl + B)">
                    <svg class="header-toggle-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>
            </div>
            
            <nav class="sidebar-menu">
                @auth
                    @php
                        $user = auth()->user();
                        $isSuperAdmin = $user->hasRole('super-admin');
                        $isRegistrar = $user->hasRole('registrar');
                        $isScrutiny = $user->hasRole('scrutiny-committee');
                        $isBoard = $user->hasRole('selection-board-member');
                        $isAppellate = $user->hasRole('appellate-committee');
                        $isReviewer = $user->hasRole('academic-reviewer');
                        $isApplicant = $user->hasRole('applicant');

                        $baseRoute = $isRegistrar && !$isSuperAdmin ? '/registrar' : '/admin';
                    @endphp

                    {{-- 1. CAMPAIGNS & INTAKE --}}
                    @if($isSuperAdmin || $isRegistrar || $user->can('view-campaign') || $user->can('view-advertisement'))
                        <div class="sidebar-section-title">Campaigns &amp; Intake</div>
                        @if($isSuperAdmin || $isRegistrar)
                            <a href="{{ $baseRoute }}/dashboard" 
                               class="sidebar-link {{ request()->is('admin/dashboard*') || request()->is('registrar/dashboard*') ? 'active' : '' }}"
                               data-tooltip="Operations Desk">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="7" height="9" x="3" y="3" rx="1"/>
                                    <rect width="7" height="5" x="14" y="3" rx="1"/>
                                    <rect width="7" height="9" x="14" y="12" rx="1"/>
                                    <rect width="7" height="5" x="3" y="16" rx="1"/>
                                </svg>
                                <span>Operations Desk</span>
                            </a>
                        @endif

                        @if($isSuperAdmin || $isRegistrar || $user->can('view-advertisement') || $user->can('view-campaign'))
                            <a href="{{ $baseRoute }}/advertisements" 
                               class="sidebar-link {{ request()->is('admin/advertisements*') || request()->is('registrar/advertisements*') ? 'active' : '' }}"
                               data-tooltip="Advertisements">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="18" x="3" y="3" rx="2"/>
                                    <path d="M3 9h18"/>
                                    <path d="M9 21V9"/>
                                </svg>
                                <span>Advertisements</span>
                            </a>
                        @endif

                        @can('view-campaign')
                            <a href="{{ $baseRoute }}/campaigns" 
                               class="sidebar-link {{ request()->is('admin/campaigns*') || request()->is('registrar/campaigns*') ? 'active' : '' }}"
                               data-tooltip="Job Campaigns">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m3 11 18-5v12L3 14v-3z"/>
                                    <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>
                                </svg>
                                <span>Job Campaigns</span>
                            </a>
                        @endcan
                    @endif

                    {{-- 2. CANDIDACIES & DOSSIERS --}}
                    @if($isSuperAdmin || $isRegistrar || $user->can('view-application') || $user->can('view-basic-info') || $user->can('view-payment'))
                        <div class="sidebar-section-title">Candidacies &amp; Dossiers</div>
                        @can('view-application')
                            <a href="{{ $baseRoute }}/applications" 
                               class="sidebar-link {{ request()->is('admin/applications*') || request()->is('registrar/applications*') ? 'active' : '' }}"
                               data-tooltip="Applications Dossiers">
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
                            <a href="{{ $baseRoute }}/candidates" 
                               class="sidebar-link {{ request()->is('admin/candidates*') || request()->is('registrar/candidates*') ? 'active' : '' }}"
                               data-tooltip="Candidate Profiles">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                <span>Candidate Profiles</span>
                            </a>
                        @endcan

                        @if($isSuperAdmin || $isRegistrar || $user->can('view-board-roster') || $user->can('view-application'))
                            <a href="{{ $baseRoute }}/merit-lists" 
                               class="sidebar-link {{ request()->is('admin/merit-lists*') || request()->is('registrar/merit-lists*') || request()->is('board/merit-lists*') ? 'active' : '' }}"
                               data-tooltip="Statutory Merit Lists">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    <polyline points="14 11 16 13 22 7"/>
                                </svg>
                                <span>Statutory Merit Lists</span>
                            </a>
                        @endif

                        @can('view-payment')
                            <a href="{{ $baseRoute }}/payments" 
                               class="sidebar-link {{ request()->is('admin/payments*') || request()->is('registrar/payments*') ? 'active' : '' }}"
                               data-tooltip="Fee Payments">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="20" height="14" x="2" y="5" rx="2"/>
                                    <line x1="2" x2="22" y1="10" y2="10"/>
                                </svg>
                                <span>Payments</span>
                            </a>
                        @endcan
                    @endif

                    {{-- 3. STATUTORY GOVERNANCE --}}
                    @if($isSuperAdmin || $isRegistrar || $user->can('tune-constitution') || $user->can('manage-statuses') || $user->can('manage-reviewers') || $user->can('view-reviewer'))
                        <div class="sidebar-section-title">Statutory Governance</div>
                        @can('tune-constitution')
                            <a href="{{ $baseRoute }}/criteria" 
                               class="sidebar-link {{ request()->is('admin/criteria*') || request()->is('registrar/criteria*') || request()->is('*/constitutions*') ? 'active' : '' }}"
                               data-tooltip="Boards &amp; Constitutions">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                                    <path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                                    <path d="M7 21h10"/>
                                    <path d="M12 3v18"/>
                                    <path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"/>
                                </svg>
                                <span>Boards &amp; Constitutions</span>
                            </a>

                            <a href="{{ $baseRoute }}/pipelines" 
                               class="sidebar-link {{ request()->is('*pipeline*') ? 'active' : '' }}"
                               data-tooltip="Recruitment Pipelines">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="12 2 2 7 12 12 22 7 12 2"/>
                                    <polyline points="2 17 12 22 22 17"/>
                                    <polyline points="2 12 12 17 22 12"/>
                                </svg>
                                <span>Recruitment Pipelines</span>
                            </a>
                        @endcan

                        @if($isSuperAdmin || $isRegistrar || $user->can('manage-statuses') || $user->can('manage-settings'))
                            <a href="{{ $baseRoute }}/statuses" 
                               class="sidebar-link {{ request()->is('admin/statuses*') || request()->is('registrar/statuses*') ? 'active' : '' }}"
                               data-tooltip="Status Lifecycles">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                                </svg>
                                <span>Status Lifecycles</span>
                            </a>
                        @endif

                        @if(($user->can('manage-reviewers') || $user->can('view-reviewer')) && \App\Models\SystemSetting::isPipelineStepEnabled('peer_review'))
                            <a href="{{ $baseRoute }}/reviewers" 
                               class="sidebar-link {{ request()->is('admin/reviewers*') || request()->is('registrar/reviewers*') ? 'active' : '' }}"
                               data-tooltip="Academic Reviewers">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                <span>Academic Reviewers</span>
                            </a>
                        @endif

                        @if(($isSuperAdmin || $isRegistrar || $user->can('view-executive-analytics')) && \App\Models\SystemSetting::isFeatureEnabled('executive_analytics'))
                            <a href="{{ $baseRoute }}/executive-analytics" 
                               class="sidebar-link {{ request()->is('admin/executive-analytics*') || request()->is('registrar/executive-analytics*') ? 'active' : '' }}"
                               data-tooltip="Executive Analytics">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" x2="18" y1="20" y2="10"/>
                                    <line x1="12" x2="12" y1="20" y2="4"/>
                                    <line x1="6" x2="6" y1="20" y2="14"/>
                                </svg>
                                <span>Executive Analytics</span>
                            </a>
                        @endif
                    @endif

                    {{-- 4. COMMITTEE WORKSPACES --}}
                    @php
                        $hasCommitteeDesks = (($isSuperAdmin || $isScrutiny || $user->can('scrutinize-application')) && \App\Models\SystemSetting::isPipelineStepEnabled('scrutiny'))
                            || (($isSuperAdmin || $isBoard || $user->can('access-board-grading') || $user->can('evaluate-application')) && \App\Models\SystemSetting::isPipelineStepEnabled('board_interview'))
                            || (($isSuperAdmin || $isAppellate || $user->can('view-appeal') || $user->can('process-appeal')) && \App\Models\SystemSetting::isPipelineStepEnabled('appellate'))
                            || (($isSuperAdmin || $isReviewer || $user->can('evaluate-academic-dossier')) && \App\Models\SystemSetting::isPipelineStepEnabled('peer_review'));
                    @endphp
                    @if($hasCommitteeDesks)
                        <div class="sidebar-section-title">Committee Desks</div>

                        @if(($isSuperAdmin || $isScrutiny || $user->can('scrutinize-application')) && \App\Models\SystemSetting::isPipelineStepEnabled('scrutiny'))
                            <a href="/scrutiny/dashboard" 
                               class="sidebar-link {{ request()->is('scrutiny*') ? 'active' : '' }}"
                               data-tooltip="Scrutiny Queue">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"/>
                                    <path d="m21 21-4.3-4.3"/>
                                    <path d="M11 8v6"/>
                                    <path d="M8 11h6"/>
                                </svg>
                                <span>Scrutiny Queue</span>
                            </a>
                        @endif

                        @if(($isSuperAdmin || $isBoard || $user->can('access-board-grading') || $user->can('evaluate-application')) && \App\Models\SystemSetting::isPipelineStepEnabled('board_interview'))
                            <a href="/board/dashboard" 
                               class="sidebar-link {{ request()->is('board/dashboard*') || request()->is('board/evaluate*') ? 'active' : '' }}"
                               data-tooltip="Board Grading Desk">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                </svg>
                                <span>Board Grading Desk</span>
                            </a>

                            @if(($isSuperAdmin || $isBoard || $user->can('view-projector-merit-board')) && \App\Models\SystemSetting::isFeatureEnabled('projector_merit_board'))
                                <a href="/board/projector" 
                                   class="sidebar-link {{ request()->is('*projector*') ? 'active' : '' }}"
                                   data-tooltip="Projector Merit Board">
                                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="20" height="15" x="2" y="3" rx="2"/>
                                        <line x1="12" x2="12" y1="18" y2="21"/>
                                        <line x1="8" x2="16" y1="21" y2="21"/>
                                    </svg>
                                    <span>Projector Merit Board</span>
                                </a>
                            @endif
                        @endif

                        @if(($isSuperAdmin || $isAppellate || $user->can('view-appeal') || $user->can('process-appeal')) && \App\Models\SystemSetting::isPipelineStepEnabled('appellate'))
                            <a href="/appellate/dashboard" 
                               class="sidebar-link {{ request()->is('appellate*') || request()->is('admin/appeals*') ? 'active' : '' }}"
                               data-tooltip="Grievance Appeals">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                                    <path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                                    <path d="M7 21h10"/>
                                    <path d="M12 3v18"/>
                                    <path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"/>
                                </svg>
                                <span>Grievance Appeals</span>
                            </a>
                        @endif

                        @if(($isSuperAdmin || $isReviewer || $user->can('evaluate-academic-dossier')) && \App\Models\SystemSetting::isPipelineStepEnabled('peer_review'))
                            <a href="/reviewer/dashboard" 
                               class="sidebar-link {{ request()->is('reviewer*') ? 'active' : '' }}"
                               data-tooltip="Evaluation Desk">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                    <line x1="16" y1="17" x2="8" y2="17"/>
                                    <polyline points="10 9 9 9 8 9"/>
                                </svg>
                                <span>Evaluation Desk</span>
                            </a>
                        @endif
                    @endif

                    {{-- 5. ADMINISTRATION & SECURITY --}}
                    @if($isSuperAdmin || $isRegistrar || $user->can('view-user') || $user->can('view-role') || $user->can('view-permission') || $user->can('view-audit-log') || $user->can('manage-settings'))
                        <div class="sidebar-section-title">Administration</div>
                        @can('view-user')
                            <a href="{{ $baseRoute }}/users" 
                               class="sidebar-link {{ request()->is('admin/users*') || request()->is('registrar/users*') ? 'active' : '' }}"
                               data-tooltip="User Accounts">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                <span>User Accounts</span>
                            </a>
                        @endcan

                        @if($isSuperAdmin || $user->can('view-role') || $user->can('view-permission'))
                            <a href="{{ $baseRoute }}/roles" 
                               class="sidebar-link {{ request()->is('admin/roles*') || request()->is('registrar/roles*') ? 'active' : '' }}"
                               data-tooltip="Roles &amp; Access">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                </svg>
                                <span>Roles &amp; Access</span>
                            </a>
                        @endif

                        @can('view-audit-log')
                            <a href="{{ $baseRoute }}/audit-logs" 
                               class="sidebar-link {{ request()->is('admin/audit-logs*') || request()->is('registrar/audit-logs*') ? 'active' : '' }}"
                               data-tooltip="Security Audit Logs">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                    <line x1="16" y1="17" x2="8" y2="17"/>
                                    <polyline points="10 9 9 9 8 9"/>
                                </svg>
                                <span>Security Audit Logs</span>
                            </a>
                        @endcan

                        @if($isSuperAdmin || $user->can('manage-settings'))
                            <a href="{{ $baseRoute }}/settings" 
                               class="sidebar-link {{ request()->is('admin/settings*') || request()->is('registrar/settings*') ? 'active' : '' }}"
                               data-tooltip="Settings &amp; Workflows">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3"/>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                                </svg>
                                <span>Settings &amp; Workflows</span>
                            </a>
                        @endif
                    @endif

                    {{-- 6. CANDIDATE SPACE --}}
                    @if($isApplicant)
                        <div class="sidebar-section-title">Candidate Space</div>
                        <a href="/applicant/dashboard" 
                           class="sidebar-link {{ request()->is('applicant/dashboard') ? 'active' : '' }}"
                           data-tooltip="Candidate Profile">
                            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            <span>Candidate Profile</span>
                        </a>
                        <a href="/applicant/wizard" 
                           class="sidebar-link {{ request()->is('applicant/wizard') ? 'active' : '' }}"
                           data-tooltip="Application Wizard">
                            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                                <path d="M15 2H9a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1Z"/>
                                <path d="m9 14 2 2 4-4"/>
                            </svg>
                            <span>Application Wizard</span>
                        </a>
                    @endif

                    {{-- 7. FINTECH SANDBOX (Admin & Registrar only) --}}
                    @if($isSuperAdmin || $isRegistrar)
                        <div class="sidebar-section-title">Fintech Sandbox</div>
                        <a href="/bank/simulator" 
                           class="sidebar-link {{ request()->is('bank/simulator*') ? 'active' : '' }}" 
                           style="border: 1px dashed var(--secondary-glow); color: var(--secondary);"
                           data-tooltip="1Link Simulator">
                            <svg class="nav-icon" style="color: var(--secondary);" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="20" height="14" x="2" y="5" rx="2"/>
                                <line x1="2" x2="22" y1="10" y2="10"/>
                            </svg>
                            <span>1Link Simulator</span>
                            <span class="sidebar-badge badge-appealed">Test</span>
                        </a>
                    @endif
                @else
                    <a href="/login" class="sidebar-link" data-tooltip="Sign In">
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                            <polyline points="10 17 15 12 10 7"/>
                            <line x1="15" x2="3" y1="12" y2="12"/>
                        </svg>
                        <span>Sign In</span>
                    </a>
                    <a href="/bank/simulator" class="sidebar-link" style="border: 1px dashed var(--secondary-glow); color: var(--secondary);" data-tooltip="1Link Simulator">
                        <svg class="nav-icon" style="color: var(--secondary);" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="14" x="2" y="5" rx="2"/>
                            <line x1="2" x2="22" y1="10" y2="10"/>
                        </svg>
                        <span>1Link Simulator</span>
                    </a>
                @endauth
            </nav>


        </aside>

        <!-- Main Content Wrapper -->
        <div class="main-content">
            @if(session()->has('impersonated_by'))
                <!-- Prominent Super Admin Impersonation Notice Banner -->
                <div class="impersonation-alert-banner">
                    <div class="impersonation-content">
                        <span class="impersonation-mask-icon">🎭</span>
                        <div class="impersonation-meta">
                            <span>You are impersonating <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->email }})</span>
                            <span class="impersonation-role-pill">
                                {{ auth()->user()->roles->pluck('name')->join(', ') ?: 'Applicant' }}
                            </span>
                            <span class="impersonation-initiator">
                                • Initiated by: <em>{{ session('impersonator_name') ?: session('impersonator_email') ?: 'Super Admin' }}</em>
                            </span>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('impersonate.leave') }}" class="impersonation-exit-btn">
                            <span>✕ Exit Impersonation (Esc)</span>
                        </a>
                    </div>
                </div>
            @endif

            <!-- Modern Topbar Navigation -->
            <header class="topbar">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <!-- Mobile Hamburger -->
                    <button type="button" id="sidebarToggle" class="btn btn-secondary btn-sm" style="display: none;" title="Toggle Sidebar">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/>
                        </svg>
                    </button>

                    <!-- Desktop Sidebar Rail Toggle -->
                    <button type="button" id="sidebarDesktopToggle" class="sidebar-desktop-toggle-btn" title="Toggle Sidebar Rail (Ctrl + B)" aria-label="Toggle Sidebar Rail">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="3" rx="2"/>
                            <path d="M9 3v18"/>
                            <path d="m15 9-3 3 3 3"/>
                        </svg>
                    </button>

                    <div class="topbar-breadcrumbs">
                        <div class="topbar-workspace-pill">
                            @if(request()->is('admin*') || request()->is('registrar*'))
                                <span>🏛️ {{ $sysOrgShortName }} Admin</span>
                            @elseif(request()->is('scrutiny*'))
                                <span>🔍 {{ $sysOrgShortName }} Scrutiny</span>
                            @elseif(request()->is('board*'))
                                <span>⚖️ {{ $sysOrgShortName }} Board</span>
                            @elseif(request()->is('appellate*'))
                                <span>🛡️ {{ $sysOrgShortName }} Grievance</span>
                            @elseif(request()->is('reviewer*'))
                                <span>🔬 {{ $sysOrgShortName }} Peer Review</span>
                            @elseif(request()->is('applicant*'))
                                <span>👤 {{ $sysOrgShortName }} Candidate</span>
                            @else
                                <span>⚡ {{ $sysOrgShortName }}</span>
                            @endif
                        </div>
                        <span style="opacity: 0.35;">/</span>
                        <strong style="color: var(--color-text-primary); font-weight: 700; font-size: 0.88rem;">
                            @if(request()->is('admin/dashboard*') || request()->is('registrar/dashboard*')) Administrative Operations Desk
                            @elseif(request()->is('*projector*')) Selection Board Projector Merit Board
                            @elseif(request()->is('*executive-analytics*')) Executive Recruitment Intelligence & Analytics
                            @elseif(request()->is('scrutiny*')) Scrutiny Committee Workspace
                            @elseif(request()->is('board*')) Selection Board Workspace
                            @elseif(request()->is('appellate*') || request()->is('admin/appeals*')) Grievance Appeals Workspace
                            @elseif(request()->is('reviewer*')) Peer Review Evaluation Desk
                            @elseif(request()->is('applicant/dashboard*')) Candidate Space
                            @elseif(request()->is('applicant/wizard*')) Application Dossier Wizard
                            @elseif(request()->is('*pipeline*')) Recruitment Pipeline Flows
                            @elseif(request()->is('*criteria*') || request()->is('*constitution*')) Boards & Statutory Criteria
                            @elseif(request()->is('admin/reviewers*') || request()->is('registrar/reviewers*')) Academic Reviewers Desk
                            @elseif(request()->is('admin/advertisements*') || request()->is('registrar/advertisements*')) Official Advertisements & Dates
                            @elseif(request()->is('admin/campaigns*') || request()->is('registrar/campaigns*')) Recruitment Campaigns
                            @elseif(request()->is('admin/applications*') || request()->is('registrar/applications*')) Applications Dossiers
                            @elseif(request()->is('admin/candidates*') || request()->is('registrar/candidates*')) Candidate Profiles
                            @elseif(request()->is('admin/payments*') || request()->is('registrar/payments*')) Payments & Vouchers
                            @elseif(request()->is('admin/users*') || request()->is('registrar/users*')) User Accounts Management
                            @elseif(request()->is('admin/roles*') || request()->is('registrar/roles*')) Roles & Access Governance
                            @elseif(request()->is('admin/audit-logs*') || request()->is('registrar/audit-logs*')) Security Audit Logs
                            @elseif(request()->is('admin/settings*') || request()->is('registrar/settings*')) System Settings & Workflows
                            @elseif(request()->is('bank/simulator*')) 1Link / Kuickpay Switch Sandbox
                            @else University Recruitment OS @endif
                        </strong>
                    </div>
                </div>

                <!-- Center Quick Search Trigger -->
                <div class="topbar-search-trigger" onclick="if(window.Livewire) { Livewire.dispatch('open-search-palette'); } else { window.dispatchEvent(new CustomEvent('open-search-palette')); }" title="Quick Universal Search & Command Palette (Ctrl + K)" role="button" tabindex="0">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    <span>Quick Filter & Search Records...</span>
                    <kbd>Ctrl K</kbd>
                </div>

                <div class="topbar-actions">
                    <!-- Modern Segmented Theme Switcher in Topbar -->
                    <div class="theme-segmented-group" style="padding: 2px;">
                        <button type="button" class="theme-btn" data-theme-val="light" title="Light Mode" style="padding: 0.25rem 0.55rem; font-size: 0.75rem;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="4"/>
                                <path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/>
                            </svg>
                            <span>Light</span>
                        </button>
                        <button type="button" class="theme-btn" data-theme-val="dark" title="Dark Mode" style="padding: 0.25rem 0.55rem; font-size: 0.75rem;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>
                            </svg>
                            <span>Dark</span>
                        </button>
                        <button type="button" class="theme-btn" data-theme-val="system" title="System Auto" style="padding: 0.25rem 0.55rem; font-size: 0.75rem;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/>
                            </svg>
                            <span>Auto</span>
                        </button>
                    </div>

                    @auth
                        @php
                            $topUser = auth()->user();
                            $topName = $topUser->basicInfo ? trim($topUser->basicInfo->first_name . ' ' . $topUser->basicInfo->last_name) : null;
                            $topRole = $topUser->roles->first()?->name ?? 'User';
                            $topInitial = strtoupper(substr($topUser->basicInfo?->first_name ?: $topUser->email, 0, 1));
                        @endphp
                        <!-- User Detail in Topbar -->
                        <div class="topbar-user-badge" style="display: flex; align-items: center; gap: 0.65rem; background: var(--bg-card); border: 1px solid var(--border-color); padding: 0.25rem 0.65rem 0.25rem 0.35rem; border-radius: 9999px;">
                            <div class="user-avatar-circle" style="width: 32px; height: 32px; font-size: 0.82rem; font-weight: 700; flex-shrink: 0; overflow: hidden; padding: 0; border: 1px solid var(--border-color);">
                                @if($topUser->profile_picture_url)
                                    <img src="{{ $topUser->profile_picture_url }}" alt="{{ $topName ?: $topUser->email }}" onerror="this.style.display='none';" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    {{ $topInitial }}
                                @endif
                            </div>
                            <div style="display: flex; flex-direction: column; text-align: left; line-height: 1.15; max-width: 170px;">
                                <span style="font-size: 0.8rem; font-weight: 700; color: var(--color-text-primary); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $topUser->email }}">
                                    {{ $topName ?: $topUser->email }}
                                </span>
                                <span style="font-size: 0.65rem; font-weight: 700; color: var(--secondary); text-transform: uppercase; letter-spacing: 0.03em;">
                                    {{ str_replace('-', ' ', $topRole) }}
                                </span>
                            </div>
                            <form action="/logout" method="POST" style="margin: 0; padding-left: 0.2rem;">
                                @csrf
                                <button type="submit" class="btn-logout-icon" title="Log out session" style="width: 28px; height: 28px; background: transparent; border: none; color: var(--color-text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: var(--transition-fast);">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                        <polyline points="16 17 21 12 16 7"/>
                                        <line x1="21" x2="9" y1="12" y2="12"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endauth
                </div>
            </header>

            <!-- Main Container -->
            <main class="container">
                @if($announcementActive && !empty($announcementMsg))
                    @php
                        $bannerMeta = match($announcementType) {
                            'warning' => ['bg' => 'rgba(245, 158, 11, 0.12)', 'border' => 'rgba(245, 158, 11, 0.4)', 'text' => '#fbbf24', 'badge' => 'Attention', 'icon' => '⚠️'],
                            'danger' => ['bg' => 'rgba(239, 68, 68, 0.12)', 'border' => 'rgba(239, 68, 68, 0.4)', 'text' => '#f87171', 'badge' => 'Urgent Notice', 'icon' => '🚨'],
                            'success' => ['bg' => 'rgba(16, 185, 129, 0.12)', 'border' => 'rgba(16, 185, 129, 0.4)', 'text' => '#34d399', 'badge' => 'Update', 'icon' => '✅'],
                            default => ['bg' => 'rgba(79, 70, 229, 0.12)', 'border' => 'rgba(79, 70, 229, 0.4)', 'text' => '#a5b4fc', 'badge' => 'Announcement', 'icon' => '📢'],
                        };
                    @endphp
                    <div class="card" style="border-color: {{ $bannerMeta['border'] }}; background: {{ $bannerMeta['bg'] }}; margin-bottom: 1.5rem; padding: 0.85rem 1.15rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; border-radius: var(--radius-sm);">
                        <div style="display: flex; align-items: center; gap: 0.85rem; flex-wrap: wrap;">
                            <span style="font-size: 1.25rem; line-height: 1;">{{ $bannerMeta['icon'] }}</span>
                            <span style="background: {{ $bannerMeta['border'] }}; color: {{ $bannerMeta['text'] }}; padding: 0.15rem 0.55rem; border-radius: 4px; font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em;">
                                {{ $bannerMeta['badge'] }}
                            </span>
                            <span style="color: {{ $bannerMeta['text'] }}; font-weight: 600; font-size: 0.92rem; line-height: 1.4;">
                                {{ $announcementMsg }}
                            </span>
                        </div>
                        <button type="button" style="background: none; border: none; color: {{ $bannerMeta['text'] }}; font-size: 1.35rem; cursor: pointer; line-height: 1; padding: 0 0.25rem; opacity: 0.8;" onclick="this.closest('.card').remove()" title="Dismiss announcement">×</button>
                    </div>
                @endif

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

                @php
                    $activePortal = 'global';
                    if (request()->is('applicant*')) {
                        $activePortal = 'candidate';
                    } elseif (request()->is('scrutiny*') || request()->is('board*') || request()->is('appellate*')) {
                        $activePortal = 'board';
                    } elseif (request()->is('reviewer*')) {
                        $activePortal = 'reviewer';
                    } elseif (request()->is('admin*') || request()->is('registrar*')) {
                        $activePortal = 'admin';
                    }
                    $portalFooterNote = \App\Models\SystemSetting::getPortalFooter($activePortal);
                @endphp
                <footer style="margin-top: 3.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color); display: flex; flex-direction: column; gap: 0.85rem; font-size: 0.82rem; color: var(--color-text-muted);">
                    @if(!empty($portalFooterNote))
                        <div style="font-size: 0.80rem; color: var(--color-text-secondary); line-height: 1.45; background: var(--bg-surface); padding: 0.55rem 0.95rem; border-radius: var(--radius-sm); border-left: 3px solid var(--primary);">
                            {{ $portalFooterNote }}
                        </div>
                    @endif
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                        <div>
                            {{ $footerCopyright }}
                        </div>
                        <div style="display: flex; align-items: center; gap: 1.25rem;">
                            @if(!empty($supportEmail))
                                <a href="mailto:{{ $supportEmail }}" style="color: var(--color-text-muted); text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
                                    <span>📧</span> {{ $supportEmail }}
                                </a>
                            @endif
                            @if(!empty($supportPhone))
                                <a href="tel:{{ $supportPhone }}" style="color: var(--color-text-muted); text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
                                    <span>📞</span> {{ $supportPhone }}
                                </a>
                            @endif
                        </div>
                    </div>
                </footer>
            </main>
        </div>
    </div>

    @auth
        <livewire:universal-search-palette />
    @endauth

    @livewireScripts
    <script>
        (function() {
            // Universal Ctrl+K / Cmd+K Command Palette Shortcut
            window.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K')) {
                    e.preventDefault();
                    if (window.Livewire) {
                        Livewire.dispatch('open-search-palette');
                    } else {
                        window.dispatchEvent(new CustomEvent('open-search-palette'));
                    }
                }
            });

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

            // Desktop Sidebar Rail Collapsible Mode & Persistence
            function toggleSidebarRail() {
                const isCollapsed = document.documentElement.classList.toggle('sidebar-collapsed');
                document.body.classList.toggle('sidebar-collapsed', isCollapsed);
                localStorage.setItem('hirely_sidebar_collapsed', isCollapsed ? 'true' : 'false');

                const tooltip = document.getElementById('sidebarRailTooltip');
                if (tooltip) tooltip.classList.remove('show');
            }

            // Sync body class with html class on load
            if (document.documentElement.classList.contains('sidebar-collapsed')) {
                document.body.classList.add('sidebar-collapsed');
            }

            const sidebarRailToggle = document.getElementById('sidebarRailToggle');
            if (sidebarRailToggle) {
                sidebarRailToggle.addEventListener('click', toggleSidebarRail);
            }

            const sidebarDesktopToggle = document.getElementById('sidebarDesktopToggle');
            if (sidebarDesktopToggle) {
                sidebarDesktopToggle.addEventListener('click', toggleSidebarRail);
            }

            // Keyboard Shortcut: Ctrl + B or Cmd + B to toggle sidebar rail
            window.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && (e.key === 'b' || e.key === 'B')) {
                    e.preventDefault();
                    toggleSidebarRail();
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

            // Universal Floating Tooltip System (Menubar Rail & Header Controls)
            (function initFloatingTooltips() {
                function getOrCreateTooltip() {
                    let el = document.getElementById('sidebarRailTooltip');
                    if (!el) {
                        el = document.createElement('div');
                        el.id = 'sidebarRailTooltip';
                        el.className = 'sidebar-rail-tooltip';
                        el.setAttribute('role', 'tooltip');
                        el.setAttribute('aria-hidden', 'true');
                        document.body.prepend(el);
                    }
                    return el;
                }

                let activeEl = null;

                function extractTooltipText(target) {
                    if (!target) return '';
                    let text = target.getAttribute('data-rail-tooltip') || target.getAttribute('data-tooltip');
                    if (!text && target.hasAttribute('title')) {
                        text = target.getAttribute('title');
                        target.setAttribute(target.closest('#appSidebar') ? 'data-rail-tooltip' : 'data-tooltip', text);
                    }
                    if (!text && target.classList.contains('sidebar-link')) {
                        const span = target.querySelector('span:not(.sidebar-badge)');
                        if (span) text = span.textContent.trim();
                    }
                    return text ? text.trim() : '';
                }

                function showTooltip(target) {
                    if (window.innerWidth <= 768) return;

                    const sidebar = document.getElementById('appSidebar');
                    const isInsideSidebar = sidebar && sidebar.contains(target);

                    // Requirement: In expanded mode, do NOT show tooltips on the sidebar
                    if (isInsideSidebar) {
                        const isCollapsed = document.documentElement.classList.contains('sidebar-collapsed') 
                                         || document.body.classList.contains('sidebar-collapsed');
                        if (!isCollapsed) {
                            hideTooltip();
                            return;
                        }

                        // Requirement: Never show tooltips for sections/groups
                        if (target.classList.contains('sidebar-section-title') || target.closest('.sidebar-section-title')) {
                            hideTooltip();
                            return;
                        }
                    }

                    const text = extractTooltipText(target);
                    if (!text) {
                        hideTooltip();
                        return;
                    }

                    // Strip native title to avoid native browser tooltip collision
                    if (target.hasAttribute('title')) {
                        target.setAttribute('data-native-title', target.getAttribute('title'));
                        target.removeAttribute('title');
                    }

                    const tooltip = getOrCreateTooltip();

                    // Clean decode entities
                    const temp = document.createElement('div');
                    temp.innerHTML = text;
                    tooltip.textContent = temp.textContent || text;

                    const targetRect = target.getBoundingClientRect();
                    if (targetRect.height === 0 || targetRect.width === 0) return;

                    const isInsideTopbar = target.closest('.topbar-header');

                    let left = 0;
                    let top = 0;

                    if (isInsideSidebar && sidebar) {
                        tooltip.classList.remove('topbar-tooltip');
                        const sidebarRect = sidebar.getBoundingClientRect();
                        // Position strictly to the right of the collapsed rail
                        left = Math.round(sidebarRect.right + 10);
                        top = Math.round(targetRect.top + (targetRect.height / 2));
                    } else if (isInsideTopbar) {
                        tooltip.classList.add('topbar-tooltip');
                        left = Math.round(targetRect.left + (targetRect.width / 2));
                        top = Math.round(targetRect.bottom + 8);
                    } else {
                        tooltip.classList.remove('topbar-tooltip');
                        left = Math.round(targetRect.right + 10);
                        top = Math.round(targetRect.top + (targetRect.height / 2));
                    }

                    // Clamp to viewport
                    if (!isInsideTopbar) {
                        const minTop = 15;
                        const maxTop = window.innerHeight - 30;
                        top = Math.max(minTop, Math.min(top, maxTop));
                    }

                    const wasShowing = tooltip.classList.contains('show');

                    if (!wasShowing) {
                        tooltip.style.transition = 'none';
                        tooltip.style.left = `${left}px`;
                        tooltip.style.top = `${top}px`;
                        void tooltip.offsetHeight; // force reflow
                        tooltip.style.transition = '';
                    } else {
                        tooltip.style.left = `${left}px`;
                        tooltip.style.top = `${top}px`;
                    }

                    tooltip.classList.add('show');
                    activeEl = target;
                }

                function hideTooltip() {
                    const tooltip = document.getElementById('sidebarRailTooltip');
                    if (tooltip) {
                        tooltip.classList.remove('show');
                    }
                    activeEl = null;
                }

                // Global delegate on sidebar (only when collapsed, strictly excludes sections)
                const sidebar = document.getElementById('appSidebar');
                if (sidebar) {
                    sidebar.addEventListener('mouseover', function(e) {
                        const isCollapsed = document.documentElement.classList.contains('sidebar-collapsed') 
                                         || document.body.classList.contains('sidebar-collapsed');
                        if (!isCollapsed) return;

                        const target = e.target.closest('.sidebar-link, .sidebar-brand-wrapper, .sidebar-header-toggle-btn');
                        if (target && sidebar.contains(target)) {
                            showTooltip(target);
                        }
                    });

                    sidebar.addEventListener('mouseout', function(e) {
                        if (!activeEl) return;
                        const related = e.relatedTarget;
                        if (!related || !sidebar.contains(related)) {
                            hideTooltip();
                        } else {
                            const next = related.closest('.sidebar-link, .sidebar-brand-wrapper, .sidebar-header-toggle-btn');
                            if (!next || !sidebar.contains(next)) {
                                hideTooltip();
                            }
                        }
                    });

                    sidebar.addEventListener('mouseleave', hideTooltip);

                    const menu = sidebar.querySelector('.sidebar-menu');
                    if (menu) {
                        menu.addEventListener('scroll', hideTooltip, { passive: true });
                    }
                }

                // Topbar controls delegate
                const topbar = document.querySelector('.topbar-header');
                if (topbar) {
                    topbar.addEventListener('mouseover', function(e) {
                        const target = e.target.closest('[data-tooltip], #sidebarDesktopToggle, .topbar-search-trigger, .theme-btn');
                        if (target && topbar.contains(target)) {
                            showTooltip(target);
                        }
                    });

                    topbar.addEventListener('mouseout', function(e) {
                        if (!activeEl) return;
                        const related = e.relatedTarget;
                        if (!related || !topbar.contains(related)) {
                            hideTooltip();
                        } else {
                            const next = related.closest('[data-tooltip], #sidebarDesktopToggle, .topbar-search-trigger, .theme-btn');
                            if (!next || !topbar.contains(next)) {
                                hideTooltip();
                            }
                        }
                    });

                    topbar.addEventListener('mouseleave', hideTooltip);
                }

                window.addEventListener('scroll', hideTooltip, { passive: true });
                window.addEventListener('resize', hideTooltip, { passive: true });
                window.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' || (e.ctrlKey && e.key.toLowerCase() === 'b')) {
                        hideTooltip();
                    }
                });
            })();
        })();
    </script>
</body>
</html>
