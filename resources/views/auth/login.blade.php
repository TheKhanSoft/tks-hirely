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
                
                <!-- Left Showcase Column: Institutional Profile & Features -->
                <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; padding: 2.25rem 2rem; background: linear-gradient(145deg, rgba(15, 23, 42, 0.88), rgba(15, 23, 42, 0.72)); border: 1.5px solid var(--border-color); border-radius: var(--radius-lg); position: relative; overflow: hidden;">
                    <!-- Ambient Inner Glow -->
                    <div style="position: absolute; width: 220px; height: 220px; border-radius: 50%; background: var(--primary-glow); top: -40px; right: -40px; pointer-events: none; filter: blur(50px); opacity: 0.35;"></div>
                    
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.85rem; margin-bottom: 1.75rem;">
                            <div class="brand-icon-box" style="width: 48px; height: 48px; padding: 0; background: transparent; box-shadow: none; display: flex; align-items: center; justify-content: center;">
                                <img src="{{ $sysAppLogo }}" alt="{{ $sysAppName }}" width="48" height="48" style="border-radius: 12px; box-shadow: 0 8px 24px var(--primary-glow); display: block; object-fit: contain;">
                            </div>
                            <div>
                                <div style="font-size: 0.74rem; font-weight: 800; text-transform: uppercase; color: var(--secondary); letter-spacing: 0.06em;">Official Institutional Portal</div>
                                <h2 style="font-size: 1.45rem; font-weight: 900; color: var(--color-text-primary); margin: 0; line-height: 1.2;">{{ strtoupper($sysAppName) }}</h2>
                            </div>
                        </div>

                        <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--color-text-primary); line-height: 1.4; margin-bottom: 0.65rem;">
                            Statutory Faculty & Executive Recruitment Operating System
                        </h3>
                        <p style="font-size: 0.86rem; color: var(--color-text-secondary); line-height: 1.6; margin-bottom: 1.75rem;">
                            {{ $sysTagline }} — Engineered for merit-based academic appointments, statutory scrutiny vetting, peer review evaluations, and automated selection board proceedings.
                        </p>

                        <!-- Key Institutional Governance Pillars -->
                        <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                            <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                <div style="width: 26px; height: 26px; border-radius: 6px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 800; flex-shrink: 0;">✓</div>
                                <div>
                                    <div style="font-size: 0.84rem; font-weight: 700; color: var(--color-text-primary);">HEC Statutory Criteria Alignment</div>
                                    <div style="font-size: 0.76rem; color: var(--color-text-muted);">Division restrictions, terminal degree cutoffs, and minimum qualification rules.</div>
                                </div>
                            </div>

                            <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                <div style="width: 26px; height: 26px; border-radius: 6px; background: var(--secondary-light); color: var(--secondary); display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 800; flex-shrink: 0;">✓</div>
                                <div>
                                    <div style="font-size: 0.84rem; font-weight: 700; color: var(--color-text-primary);">Confidential Blind Peer Review</div>
                                    <div style="font-size: 0.76rem; color: var(--color-text-muted);">Empanelled international & national referee scoring with statutory consensus reports.</div>
                                </div>
                            </div>

                            <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                <div style="width: 26px; height: 26px; border-radius: 6px; background: rgba(16, 185, 129, 0.14); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 800; flex-shrink: 0;">✓</div>
                                <div>
                                    <div style="font-size: 0.84rem; font-weight: 700; color: var(--color-text-primary);">Automated 1Link & 1Bill Verification</div>
                                    <div style="font-size: 0.76rem; color: var(--color-text-muted);">Real-time candidate challan settlement with grace period enforcement.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 2rem; padding-top: 1.25rem; border-top: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                        <span class="badge badge-emerald" style="font-size: 0.72rem; font-weight: 800;">
                            ● Active Recruitment Drives Live
                        </span>
                        @if(!empty($orgWebsite))
                            <a href="{{ $orgWebsite }}" target="_blank" style="font-size: 0.76rem; color: var(--secondary); text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 0.25rem;">
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

</body>
</html>
