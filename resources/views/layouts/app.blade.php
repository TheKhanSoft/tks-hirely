<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Hirely - University Recruitment Portal' }}</title>
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
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <a href="/" class="sidebar-brand">HIRELY</a>
            
            <nav class="sidebar-menu">
                @auth
                    @can('view-campaign')
                        <a href="/admin/dashboard" class="sidebar-link">📊 Dashboard</a>
                        <a href="/admin/management?tab=campaigns" class="sidebar-link">📢 Campaigns</a>
                    @endcan

                    @if(auth()->user()->can('view-user') || auth()->user()->can('view-role') || auth()->user()->can('view-permission'))
                        <a href="/admin/management" class="sidebar-link">⚙️ Entity Console</a>
                    @endif

                    @if(auth()->user()->hasRole('scrutiny-committee') && auth()->user()->can('scrutinize-application'))
                        <a href="/admin/dashboard" class="sidebar-link">🔍 Scrutiny Queue</a>
                    @endif

                    @can('process-appeal')
                        <a href="/admin/appeals" class="sidebar-link">⚖️ Grievance Review</a>
                    @endcan

                    @if(auth()->user()->hasRole('selection-board-member') && auth()->user()->can('evaluate-application'))
                        <a href="/admin/dashboard" class="sidebar-link">⭐ Board Dashboard</a>
                    @endif

                    @role('applicant')
                        <a href="/applicant/dashboard" class="sidebar-link">👤 Candidate basicInfo</a>
                        <a href="/applicant/wizard" class="sidebar-link">📋 basicInfo Wizard</a>
                    @endrole

                    <!-- Sandbox Bank Simulator Utility Link -->
                    <a href="/bank/simulator" class="sidebar-link" style="border: 1px dashed var(--secondary-glow); color: var(--secondary); margin-top: auto;">💳 Bank Switch Sim</a>
                @else
                    <a href="/login" class="sidebar-link">🔑 Login</a>
                    <a href="/bank/simulator" class="sidebar-link" style="border: 1px dashed var(--secondary-glow); color: var(--secondary); margin-top: auto;">💳 Bank Switch Sim</a>
                @endauth
            </nav>

            <!-- Theme Toggle Component -->
            <div class="sidebar-theme-toggle" style="margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem; margin-bottom: 1rem;">
                <p style="font-size: 0.75rem; text-transform: uppercase; color: var(--color-text-secondary); font-weight: 700; margin-bottom: 0.75rem; letter-spacing: 0.5px;">Theme Mode</p>
                <div class="theme-toggle-group" style="display: flex; background: rgba(0,0,0,0.15); border-radius: var(--radius-sm); padding: 0.25rem; border: 1px solid var(--border-color);">
                    <button type="button" class="theme-btn" data-theme-val="light" style="flex: 1; background: none; border: none; padding: 0.4rem; color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; cursor: pointer; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; gap: 0.25rem; transition: var(--transition);">
                        ☀️ Light
                    </button>
                    <button type="button" class="theme-btn" data-theme-val="dark" style="flex: 1; background: none; border: none; padding: 0.4rem; color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; cursor: pointer; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; gap: 0.25rem; transition: var(--transition);">
                        🌙 Dark
                    </button>
                    <button type="button" class="theme-btn" data-theme-val="system" style="flex: 1; background: none; border: none; padding: 0.4rem; color: var(--color-text-secondary); font-size: 0.85rem; font-weight: 600; cursor: pointer; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; gap: 0.25rem; transition: var(--transition);">
                        💻 Auto
                    </button>
                </div>
            </div>

            @auth
                <div class="sidebar-user">
                    <p style="font-size: 0.85rem; color: var(--color-text-secondary); margin-bottom: 0.25rem; word-break: break-all; font-weight: 500;">
                        {{ auth()->user()->email }}
                    </p>
                    <p style="font-size: 0.75rem; text-transform: uppercase; color: var(--secondary); font-weight: 800; margin-bottom: 1.25rem;">
                        {{ str_replace('-', ' ', auth()->user()->roles->first()?->name) }}
                    </p>
                    <form action="/logout" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-secondary" style="width: 100%; padding: 0.5rem; font-size: 0.9rem;">Logout</button>
                    </form>
                </div>
            @endauth
        </aside>

        <!-- Main Content Area -->
        <div class="main-content">
            <main class="container">
                @if (session()->has('message'))
                    <div class="card" style="border-color: var(--success); background: rgba(16, 185, 129, 0.1); margin-bottom: 2rem;">
                        <p style="color: #6ee7b7; font-weight: 600;">{{ session('message') }}</p>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="card" style="border-color: var(--danger); background: rgba(239, 68, 68, 0.1); margin-bottom: 2rem;">
                        <p style="color: #fca5a5; font-weight: 600;">{{ session('error') }}</p>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
    <script>
        (function() {
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
        })();
    </script>
</body>
</html>
