@php
    $sysAppName = \App\Models\SystemSetting::getAppName();
    $sysAppLogo = \App\Models\SystemSetting::getAppLogo();
    $sysAppFavicon = \App\Models\SystemSetting::getAppFavicon();
    $sysTagline = \App\Models\SystemSetting::get('app_tagline', 'Unified Faculty & Officer Recruitment Portal');
    $sysAccentColor = \App\Models\SystemSetting::get('theme_accent_color', '#4f46e5');
    $orgName = \App\Models\SystemSetting::getOrgName();
    $orgShortName = \App\Models\SystemSetting::getOrgShortName();
    $announcementActive = (bool) \App\Models\SystemSetting::get('portal_announcement_enabled', false);
    $announcementType = \App\Models\SystemSetting::get('portal_announcement_type', 'info');
    $announcementMsg = \App\Models\SystemSetting::get('portal_announcement_message', '');
    $supportEmail = \App\Models\SystemSetting::get('support_email');
    $supportPhone = \App\Models\SystemSetting::get('support_phone');
    $supportWhatsapp = \App\Models\SystemSetting::get('support_whatsapp');
    $supportHours = \App\Models\SystemSetting::get('support_hours');
    $helpdeskLocation = \App\Models\SystemSetting::get('helpdesk_location');
    $orgWebsite = \App\Models\SystemSetting::get('org_website');
    $footerCopyright = \App\Models\SystemSetting::get('footer_copyright_text', '© ' . date('Y') . ' ' . $sysAppName . '. All rights reserved.');
    
    $themeVars = \App\Models\SystemSetting::getThemeVariables();
    $loginLayout = $themeVars['loginLayout'] ?? 'split-modern';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>Sign In — {{ $sysAppName }} University Recruitment OS</title>
    
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="/css/app.css?v={{ @filemtime(public_path('css/app.css')) ?: time() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
<body class="login-layout-{{ $loginLayout }} theme-density-{{ $themeVars['density'] }} theme-radius-{{ $themeVars['radiusStyle'] }}" style="display: flex; align-items: center; justify-content: center; min-height: 100vh; position: relative; padding: 1.5rem 1rem;">

    <!-- Floating Header Controls (Theme Toggle) -->
    <div style="position: absolute; top: 1.25rem; right: 1.5rem; z-index: 50; display: flex; align-items: center; gap: 0.5rem;">
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 9999px; padding: 3px 6px; display: flex; align-items: center; gap: 3px; box-shadow: var(--shadow-sm); backdrop-filter: blur(12px);">
            <button type="button" onclick="setLoginTheme('light')" id="themeBtnLight" title="Light Theme" style="background: none; border: none; border-radius: 9999px; padding: 0.35rem 0.65rem; font-size: 0.76rem; font-weight: 700; color: var(--color-text-secondary); cursor: pointer; display: flex; align-items: center; gap: 0.3rem; transition: all 0.2s;">
                <span>☀️</span> <span>Light</span>
            </button>
            <button type="button" onclick="setLoginTheme('dark')" id="themeBtnDark" title="Dark Theme" style="background: none; border: none; border-radius: 9999px; padding: 0.35rem 0.65rem; font-size: 0.76rem; font-weight: 700; color: var(--color-text-secondary); cursor: pointer; display: flex; align-items: center; gap: 0.3rem; transition: all 0.2s;">
                <span>🌙</span> <span>Dark</span>
            </button>
        </div>
    </div>

    @if($loginLayout !== 'minimalist')
        <!-- Ambient Backdrop Mesh Glows -->
        <div style="position: absolute; width: 550px; height: 550px; border-radius: 50%; background: radial-gradient(circle, var(--primary-glow) 0%, transparent 70%); top: 5%; left: 10%; pointer-events: none; filter: blur(70px); opacity: 0.45;"></div>
        <div style="position: absolute; width: 500px; height: 500px; border-radius: 50%; background: radial-gradient(circle, var(--secondary-glow) 0%, transparent 70%); bottom: 5%; right: 10%; pointer-events: none; filter: blur(70px); opacity: 0.45;"></div>
    @endif

    <div class="login-main-wrapper" style="position: relative; z-index: 10; width: 100%; {{ $loginLayout === 'split-modern' ? 'max-width: 1080px;' : 'max-width: 520px;' }}">

        <!-- Portal Announcement Alert Banner -->
        @if($announcementActive && !empty($announcementMsg))
            @php
                $bannerMeta = match($announcementType) {
                    'warning' => ['bg' => 'rgba(245, 158, 11, 0.14)', 'border' => 'rgba(245, 158, 11, 0.45)', 'text' => '#fbbf24', 'badge' => 'Attention', 'icon' => '⚠️'],
                    'danger' => ['bg' => 'rgba(239, 68, 68, 0.14)', 'border' => 'rgba(239, 68, 68, 0.45)', 'text' => '#f87171', 'badge' => 'Urgent Notice', 'icon' => '🚨'],
                    'success' => ['bg' => 'rgba(16, 185, 129, 0.14)', 'border' => 'rgba(16, 185, 129, 0.45)', 'text' => '#34d399', 'badge' => 'Update', 'icon' => '✅'],
                    default => ['bg' => 'rgba(79, 70, 229, 0.14)', 'border' => 'rgba(79, 70, 229, 0.45)', 'text' => '#a5b4fc', 'badge' => 'Announcement', 'icon' => '📢'],
                };
            @endphp
            <div class="card" style="border-color: {{ $bannerMeta['border'] }}; background: {{ $bannerMeta['bg'] }}; margin-bottom: 1.25rem; padding: 0.85rem 1.15rem; display: flex; align-items: center; justify-content: space-between; gap: 0.85rem; border-radius: var(--radius-sm); box-shadow: var(--shadow-sm);">
                <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                    <span style="font-size: 1.25rem; line-height: 1;">{{ $bannerMeta['icon'] }}</span>
                    <span style="background: {{ $bannerMeta['border'] }}; color: {{ $bannerMeta['text'] }}; padding: 0.15rem 0.55rem; border-radius: 4px; font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em;">
                        {{ $bannerMeta['badge'] }}
                    </span>
                    <span style="color: {{ $bannerMeta['text'] }}; font-weight: 600; font-size: 0.88rem; line-height: 1.4;">
                        {{ $announcementMsg }}
                    </span>
                </div>
            </div>
        @endif

        @if($loginLayout === 'split-modern')
            <!-- SPLIT MODERN DUAL-COLUMN HERO -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; align-items: stretch;">
                
                <!-- Left Showcase Column: Institutional Profile & Features (Crisp Dark Theme Card) -->
                <div class="card auth-showcase-card" style="display: flex; flex-direction: column; justify-content: space-between; padding: 2.25rem 2rem; background: linear-gradient(150deg, #0b1120 0%, #1e1b4b 60%, #090d16 100%) !important; border: 1.5px solid rgba(255, 255, 255, 0.12) !important; border-radius: var(--radius-lg); position: relative; overflow: hidden; box-shadow: 0 20px 45px -12px rgba(0, 0, 0, 0.45) !important;">
                    <!-- Ambient Inner Glow -->
                    <div style="position: absolute; width: 220px; height: 220px; border-radius: 50%; background: var(--primary-glow); top: -40px; right: -40px; pointer-events: none; filter: blur(50px); opacity: 0.35;"></div>
                    
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.85rem; margin-bottom: 1.75rem;">
                            <div class="brand-icon-box" style="width: 50px; height: 50px; padding: 0; background: transparent; box-shadow: none; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <img src="{{ $sysAppLogo }}" alt="{{ $sysAppName }}" width="50" height="50" style="border-radius: 12px; box-shadow: 0 8px 24px var(--primary-glow); display: block; object-fit: contain;">
                            </div>
                            <div>
                                <div style="font-size: 0.72rem; font-weight: 800; text-transform: uppercase; color: #38bdf8; letter-spacing: 0.08em; display: inline-flex; align-items: center; gap: 0.35rem;">
                                    <span>🛡️</span>
                                    <span>OFFICIAL INSTITUTIONAL PORTAL</span>
                                </div>
                                <h2 style="font-size: 1.5rem; font-weight: 900; color: #ffffff !important; margin: 0; line-height: 1.2; letter-spacing: -0.02em;">
                                    {{ strtoupper($sysAppName) }}
                                </h2>
                                <div style="font-size: 0.78rem; color: #cbd5e1; font-weight: 600; margin-top: 0.15rem;">
                                    {{ $orgShortName }} • {{ $orgName }}
                                </div>
                            </div>
                        </div>

                        <h3 style="font-size: 1.22rem; font-weight: 800; color: #f8fafc !important; line-height: 1.38; margin-bottom: 0.75rem;">
                            Statutory Faculty &amp; Executive Recruitment Operating System
                        </h3>
                        <p style="font-size: 0.88rem; color: #cbd5e1 !important; line-height: 1.6; margin-bottom: 1.75rem;">
                            {{ $sysTagline }} — Engineered for merit-based academic appointments, statutory scrutiny vetting, peer review evaluations, and automated selection board proceedings.
                        </p>

                        <!-- Key Institutional Governance Pillars -->
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <div style="display: flex; align-items: flex-start; gap: 0.85rem;">
                                <div style="width: 28px; height: 28px; border-radius: 7px; background: rgba(99, 102, 241, 0.25); border: 1px solid rgba(99, 102, 241, 0.4); color: #a5b4fc; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 900; flex-shrink: 0;">✓</div>
                                <div>
                                    <div style="font-size: 0.88rem; font-weight: 700; color: #ffffff !important;">HEC Statutory Criteria Alignment</div>
                                    <div style="font-size: 0.78rem; color: #94a3b8 !important; line-height: 1.45;">Division restrictions, terminal degree cutoffs, and minimum qualification rules.</div>
                                </div>
                            </div>

                            <div style="display: flex; align-items: flex-start; gap: 0.85rem;">
                                <div style="width: 28px; height: 28px; border-radius: 7px; background: rgba(6, 182, 212, 0.25); border: 1px solid rgba(6, 182, 212, 0.4); color: #67e8f9; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 900; flex-shrink: 0;">✓</div>
                                <div>
                                    <div style="font-size: 0.88rem; font-weight: 700; color: #ffffff !important;">Confidential Blind Peer Review</div>
                                    <div style="font-size: 0.78rem; color: #94a3b8 !important; line-height: 1.45;">Empanelled international &amp; national referee scoring with statutory consensus reports.</div>
                                </div>
                            </div>

                            <div style="display: flex; align-items: flex-start; gap: 0.85rem;">
                                <div style="width: 28px; height: 28px; border-radius: 7px; background: rgba(16, 185, 129, 0.25); border: 1px solid rgba(16, 185, 129, 0.4); color: #6ee7b7; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 900; flex-shrink: 0;">✓</div>
                                <div>
                                    <div style="font-size: 0.88rem; font-weight: 700; color: #ffffff !important;">Automated 1Link &amp; 1Bill Verification</div>
                                    <div style="font-size: 0.78rem; color: #94a3b8 !important; line-height: 1.45;">Real-time candidate challan settlement with grace period enforcement.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 2rem; padding-top: 1.25rem; border-top: 1px solid rgba(255, 255, 255, 0.12); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                        <span style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.3rem 0.65rem; border-radius: 9999px; background: rgba(16, 185, 129, 0.18); border: 1px solid rgba(16, 185, 129, 0.35); font-size: 0.72rem; font-weight: 800; color: #34d399;">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #34d399; box-shadow: 0 0 8px #34d399;"></span>
                            <span>Active Recruitment Drives Live</span>
                        </span>
                        @if(!empty($orgWebsite))
                            <a href="{{ $orgWebsite }}" target="_blank" style="font-size: 0.78rem; color: #38bdf8; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem;">
                                <span>Institution Web</span>
                                <span>&rarr;</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Right Form Column: Sign In Form & Fast Switcher -->
                <div class="card auth-card" style="padding: 2.25rem 2rem; border-radius: var(--radius-lg); margin: 0;">
                    <div style="margin-bottom: 1.5rem;">
                        <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--color-text-primary); margin: 0 0 0.35rem 0;">
                            Welcome Back
                        </h1>
                        <p style="font-size: 0.84rem; color: var(--color-text-secondary); margin: 0;">
                            Sign in to access your candidate dossier or statutory officer desk.
                        </p>
                    </div>

                    @include('auth.partials.login-form')
                </div>
            </div>
        @else
            <!-- CENTERED CARD LAYOUT (Glass or Minimalist) -->
            <div class="card auth-card" style="padding: 2.25rem 2rem; border-radius: var(--radius-lg); margin: 0 auto; {{ $loginLayout === 'center-glass' ? 'backdrop-filter: blur(24px); border-color: var(--primary);' : '' }}">
                <div class="text-center mb-6">
                    <div class="auth-header-brand">
                        <div class="brand-icon-box" style="width: 52px; height: 52px; padding: 0; background: transparent; box-shadow: none; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                            <img src="{{ $sysAppLogo }}" alt="{{ $sysAppName }}" width="52" height="52" style="border-radius: 14px; box-shadow: 0 8px 24px var(--primary-glow); display: block; object-fit: contain;">
                        </div>
                    </div>
                    <h1 style="font-size: 1.75rem; font-weight: 800; letter-spacing: -0.5px; color: var(--color-text-primary); margin-bottom: 0.35rem; margin-top: 0.75rem;">
                        Sign in to <span style="background: linear-gradient(135deg, var(--primary), var(--secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ strtoupper($sysAppName) }}</span>
                    </h1>
                    <p class="card-subtitle" style="font-size: 0.88rem; color: var(--color-text-secondary);">{{ $sysTagline }}</p>
                </div>

                @include('auth.partials.login-form')
            </div>
        @endif

    </div>

    <script>
        function setLoginTheme(mode) {
            localStorage.setItem('theme', mode);
            if (mode === 'dark') {
                document.documentElement.classList.remove('light');
            } else {
                document.documentElement.classList.add('light');
            }
            updateThemeButtons();
        }

        function updateThemeButtons() {
            const isLight = document.documentElement.classList.contains('light');
            const lightBtn = document.getElementById('themeBtnLight');
            const darkBtn = document.getElementById('themeBtnDark');
            if (lightBtn && darkBtn) {
                if (isLight) {
                    lightBtn.style.background = 'var(--primary)';
                    lightBtn.style.color = '#ffffff';
                    darkBtn.style.background = 'transparent';
                    darkBtn.style.color = 'var(--color-text-secondary)';
                } else {
                    darkBtn.style.background = 'var(--primary)';
                    darkBtn.style.color = '#ffffff';
                    lightBtn.style.background = 'transparent';
                    lightBtn.style.color = 'var(--color-text-secondary)';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', updateThemeButtons);
    </script>
</body>
</html>
