@php
    $sysAppName = \App\Models\SystemSetting::getAppName();
    $sysAppLogo = \App\Models\SystemSetting::getAppLogo();
    $sysAppFavicon = \App\Models\SystemSetting::getAppFavicon();
    $sysTagline = \App\Models\SystemSetting::get('app_tagline', 'Unified Faculty & Officer Recruitment Portal');
    $sysAccentColor = \App\Models\SystemSetting::get('theme_accent_color', '#4f46e5');
    $orgName = \App\Models\SystemSetting::get('org_name', $sysAppName . ' Recruitment Management System');
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
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>Sign In - {{ $sysAppName }} University Recruitment OS</title>
    
    <!-- Modern Favicons & Brand Assets -->
    <link rel="icon" href="{{ $sysAppFavicon }}">
    <link rel="shortcut icon" href="{{ $sysAppFavicon }}">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="{{ $sysAccentColor }}">
    @if($sysAccentColor && $sysAccentColor !== '#4f46e5')
    <style>
        :root {
            --primary: {{ $sysAccentColor }};
            --primary-hover: {{ $sysAccentColor }}dd;
            --primary-glow: {{ $sysAccentColor }}33;
        }
    </style>
    @endif

    <!-- Modern Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    
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
<body style="display: flex; align-items: center; justify-content: center; min-height: 100vh; position: relative;">
    <!-- Ambient Backdrop Mesh -->
    <div style="position: absolute; width: 500px; height: 500px; border-radius: 50%; background: radial-gradient(circle, var(--primary-glow) 0%, transparent 70%); top: 10%; left: 15%; pointer-events: none; filter: blur(60px); opacity: 0.5;"></div>
    <div style="position: absolute; width: 450px; height: 450px; border-radius: 50%; background: radial-gradient(circle, var(--secondary-glow) 0%, transparent 70%); bottom: 10%; right: 15%; pointer-events: none; filter: blur(60px); opacity: 0.5;"></div>

    <div class="container auth-wrapper" style="position: relative; z-index: 10;">
        @if($announcementActive && !empty($announcementMsg))
            @php
                $bannerMeta = match($announcementType) {
                    'warning' => ['bg' => 'rgba(245, 158, 11, 0.14)', 'border' => 'rgba(245, 158, 11, 0.45)', 'text' => '#fbbf24', 'badge' => 'Attention', 'icon' => '⚠️'],
                    'danger' => ['bg' => 'rgba(239, 68, 68, 0.14)', 'border' => 'rgba(239, 68, 68, 0.45)', 'text' => '#f87171', 'badge' => 'Urgent Notice', 'icon' => '🚨'],
                    'success' => ['bg' => 'rgba(16, 185, 129, 0.14)', 'border' => 'rgba(16, 185, 129, 0.45)', 'text' => '#34d399', 'badge' => 'Update', 'icon' => '✅'],
                    default => ['bg' => 'rgba(79, 70, 229, 0.14)', 'border' => 'rgba(79, 70, 229, 0.45)', 'text' => '#a5b4fc', 'badge' => 'Announcement', 'icon' => '📢'],
                };
            @endphp
            <div class="card" style="border-color: {{ $bannerMeta['border'] }}; background: {{ $bannerMeta['bg'] }}; margin-bottom: 1.25rem; padding: 0.85rem 1.15rem; display: flex; align-items: center; justify-content: space-between; gap: 0.85rem; border-radius: var(--radius-sm);">
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

        <div class="card auth-card">
            <!-- Brand Logo Header -->
            <div class="text-center mb-6">
                <div class="auth-header-brand">
                    <div class="brand-icon-box" style="width: 52px; height: 52px; padding: 0; background: transparent; box-shadow: none; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                        <img src="{{ $sysAppLogo }}" alt="{{ $sysAppName }}" width="52" height="52" style="border-radius: 14px; box-shadow: 0 8px 24px var(--primary-glow); display: block; object-fit: contain;">
                    </div>
                </div>
                <h1 style="font-family: 'Outfit', sans-serif; font-size: 1.85rem; font-weight: 800; letter-spacing: -0.5px; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                    Sign in to <span style="background: linear-gradient(135deg, var(--primary), var(--secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ strtoupper($sysAppName) }}</span>
                </h1>
                <p class="card-subtitle" style="font-size: 0.9rem;">{{ $sysTagline }}</p>
            </div>

            <!-- Login Form -->
            <form action="/login" method="POST" id="loginForm" class="mb-4">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="emailInput">Work or Candidate Email</label>
                    <div style="position: relative;">
                        <input type="email" id="emailInput" class="form-control" name="email" required placeholder="e.g. registrar@university.edu" value="{{ old('email') }}" style="padding-left: 2.5rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-text-muted)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); pointer-events: none;">
                            <rect width="20" height="16" x="2" y="4" rx="2"/>
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                        </svg>
                    </div>
                    @error('email') 
                        <span style="color: var(--danger); font-size: 0.82rem; margin-top: 0.35rem; display: block; font-weight: 500;">{{ $message }}</span> 
                    @enderror
                </div>

                <div class="form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                        <label class="form-label" for="passwordInput" style="margin-bottom: 0;">Password</label>
                        <span style="font-size: 0.75rem; color: var(--color-text-muted);">Default demo: <code>password</code></span>
                    </div>
                    <div style="position: relative;">
                        <input type="password" id="passwordInput" class="form-control" name="password" required value="password" style="padding-left: 2.5rem; padding-right: 2.5rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-text-muted)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); pointer-events: none;">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <button type="button" id="togglePasswordBtn" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--color-text-muted); cursor: pointer; padding: 0.25rem;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.8rem; font-size: 0.95rem; margin-top: 0.5rem; border-radius: var(--radius-sm);">
                    <span>Sign In to Console</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" x2="19" y1="12" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </button>
            </form>

            <!-- 1-Click Sandbox Fast Switch -->
            <div style="border-top: 1px solid var(--border-color); padding-top: 1.25rem; margin-top: 1.5rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.65rem;">
                    <div style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--secondary);">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                        </svg>
                        <span>1-Click Sandbox Fast Switch</span>
                    </div>
                    <span style="font-size: 0.7rem; color: var(--color-text-muted);">Click to Autofill</span>
                </div>

                <div class="quick-sandbox-grid">
                    <button type="button" class="quick-sandbox-chip" onclick="selectDemoAccount('registrar@university.edu', 'Registrar')">
                        <strong>🏛️ Registrar</strong>
                        <span>registrar@university.edu</span>
                    </button>
                    <button type="button" class="quick-sandbox-chip" onclick="selectDemoAccount('applicant1@gmail.com', 'Applicant')">
                        <strong>👤 Candidate 1</strong>
                        <span>applicant1@gmail.com</span>
                    </button>
                    <button type="button" class="quick-sandbox-chip" onclick="selectDemoAccount('scrutiny1@university.edu', 'Scrutiny Officer')">
                        <strong>🔍 Scrutiny Officer</strong>
                        <span>scrutiny1@university.edu</span>
                    </button>
                    <button type="button" class="quick-sandbox-chip" onclick="selectDemoAccount('appellate1@university.edu', 'Appellate Panel')">
                        <strong>⚖️ Appellate Panel</strong>
                        <span>appellate1@university.edu</span>
                    </button>
                    <button type="button" class="quick-sandbox-chip" onclick="selectDemoAccount('board1@university.edu', 'Selection Board')">
                        <strong>⭐ Selection Board</strong>
                        <span>board1@university.edu</span>
                    </button>
                    <button type="button" class="quick-sandbox-chip" onclick="selectDemoAccount('superadmin@university.edu', 'Super Admin')">
                        <strong>⚡ Super Admin</strong>
                        <span>superadmin@university.edu</span>
                    </button>
                </div>
            </div>

            <!-- Helpdesk & Candidate Facilitation Channel -->
            @if(!empty($supportEmail) || !empty($supportPhone) || !empty($supportWhatsapp) || !empty($helpdeskLocation))
            <div style="margin-top: 1.5rem; padding: 0.9rem 1rem; border-radius: var(--radius-sm); background: var(--bg-surface); border: 1px solid var(--border-color); font-size: 0.8rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.45rem;">
                    <div style="font-weight: 700; color: var(--color-text-primary); display: flex; align-items: center; gap: 0.4rem;">
                        <span>🎧</span>
                        <span>Candidate Helpdesk & Inquiries</span>
                    </div>
                    @if(!empty($supportHours))
                        <span style="font-size: 0.72rem; color: var(--color-text-muted);">{{ $supportHours }}</span>
                    @endif
                </div>

                <div style="display: flex; flex-wrap: wrap; gap: 0.75rem 1.25rem; color: var(--color-text-secondary); margin-top: 0.4rem;">
                    @if(!empty($supportEmail))
                        <a href="mailto:{{ $supportEmail }}" style="color: var(--primary); text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
                            <span>📧</span> <span>{{ $supportEmail }}</span>
                        </a>
                    @endif
                    @if(!empty($supportPhone))
                        <a href="tel:{{ $supportPhone }}" style="color: var(--color-text-secondary); text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
                            <span>📞</span> <span>{{ $supportPhone }}</span>
                        </a>
                    @endif
                    @if(!empty($supportWhatsapp))
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $supportWhatsapp) }}" target="_blank" style="color: #10b981; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 600;">
                            <span>💬</span> <span>WhatsApp Helpline</span>
                        </a>
                    @endif
                </div>

                @if(!empty($helpdeskLocation))
                    <div style="margin-top: 0.4rem; font-size: 0.74rem; color: var(--color-text-muted); display: flex; align-items: center; gap: 0.35rem;">
                        <span>📍</span>
                        <span>{{ $helpdeskLocation }}</span>
                    </div>
                @endif
            </div>
            @endif

            <!-- Footer note -->
            <div style="margin-top: 1.5rem; text-align: center;">
                <p style="font-size: 0.75rem; color: var(--color-text-muted); margin-bottom: 0.25rem;">
                    {{ $orgName }} &bull; {{ $footerCopyright }}
                </p>
                @if(!empty($orgWebsite))
                    <a href="{{ $orgWebsite }}" target="_blank" style="font-size: 0.72rem; color: var(--primary); text-decoration: none;">
                        Visit Institutional Portal &rarr;
                    </a>
                @endif
            </div>
        </div>
    </div>

    <script>
        function selectDemoAccount(email, roleName) {
            const emailInput = document.getElementById('emailInput');
            const passwordInput = document.getElementById('passwordInput');
            emailInput.value = email;
            passwordInput.value = 'password';
            
            // Visual feedback
            emailInput.style.borderColor = 'var(--secondary)';
            emailInput.style.boxShadow = '0 0 0 3px var(--secondary-glow)';
            setTimeout(() => {
                emailInput.style.borderColor = '';
                emailInput.style.boxShadow = '';
            }, 600);
        }

        const toggleBtn = document.getElementById('togglePasswordBtn');
        const passInput = document.getElementById('passwordInput');
        if (toggleBtn && passInput) {
            toggleBtn.addEventListener('click', function() {
                const isPassword = passInput.type === 'password';
                passInput.type = isPassword ? 'text' : 'password';
            });
        }
    </script>
</body>
</html>
