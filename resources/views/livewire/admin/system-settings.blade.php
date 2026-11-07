<div x-data="{ 
    changeUrl(url) {
        window.history.pushState({}, '', url);
    },
    settingsFilter: '',
    viewMode: localStorage.getItem('hirely_settings_view_mode') || 'bento',
    setViewMode(mode) {
        this.viewMode = mode;
        localStorage.setItem('hirely_settings_view_mode', mode);
    }
}" x-on:url-changed.window="changeUrl($event.detail.url)">

    {{-- Executive Header, Breadcrumbs & Feedback Alerts --}}
    @include('livewire.admin.settings.navigation-tabs')

    {{-- View Switcher Button Styling & Bento Canvas Extensions --}}
    <style>
        .view-mode-pill-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.38rem;
            padding: 0.32rem 0.72rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            line-height: 1.2;
        }
        .view-mode-pill-btn.active-view-btn {
            background: var(--primary, #4f46e5);
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.28);
        }
        .view-mode-pill-btn.inactive-view-btn {
            background: transparent;
            color: var(--color-text-secondary, #64748b);
        }
        .view-mode-pill-btn.inactive-view-btn:hover {
            color: var(--color-text-primary, #0f172a);
            background: rgba(0, 0, 0, 0.04);
        }
        html.dark .view-mode-pill-btn.inactive-view-btn:hover {
            background: rgba(255, 255, 255, 0.06);
            color: #f8fafc;
        }
        .settings-canvas-fullwidth {
            width: 100% !important;
            max-width: 100% !important;
            flex: 1 1 100% !important;
        }
        .bento-view-enter-transition {
            transition: opacity 0.25s ease-in-out, transform 0.25s ease-in-out;
        }
    </style>

    {{-- VIEW MODE 1: Bento Hub Grid View (Selected & Persisted) --}}
    <div x-show="viewMode === 'bento'" x-cloak class="bento-view-enter-transition">
        {{-- Bento Hub 11-Card Responsive Grid --}}
        @include('livewire.admin.settings.bento-grid')

        {{-- Active Settings Canvas Below Bento Hub --}}
        <main class="settings-detail-canvas settings-canvas-fullwidth" id="settingsCanvasBento">
            @include('livewire.admin.settings.canvas-router')
        </main>
    </div>

    {{-- VIEW MODE 2: Master-Detail Two-Column Sidebar View --}}
    <div x-show="viewMode === 'sidebar'" x-cloak class="settings-master-shell bento-view-enter-transition">
        {{-- Left Column: Master Settings Sidebar --}}
        @include('livewire.admin.settings.sidebar')

        {{-- Right Column: Active Settings Detail Canvas --}}
        <main class="settings-detail-canvas" id="settingsCanvasSidebar">
            @include('livewire.admin.settings.canvas-router')
        </main>
    </div>

    {{-- Hash Anchor Deep-Link Scroll & Highlight Handler --}}
    <script>
        (function() {
            function checkAndHighlightSettingTarget() {
                if (window.location.hash) {
                    const targetId = window.location.hash.substring(1);
                    const el = document.getElementById(targetId);
                    if (el) {
                        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        el.classList.add('setting-focus-pulse');
                        setTimeout(() => el.classList.remove('setting-focus-pulse'), 2500);
                    }
                }
            }
            if (document.readyState === 'complete' || document.readyState === 'interactive') {
                setTimeout(checkAndHighlightSettingTarget, 300);
            } else {
                window.addEventListener('DOMContentLoaded', () => setTimeout(checkAndHighlightSettingTarget, 350));
            }
            window.addEventListener('hashchange', checkAndHighlightSettingTarget);
        })();
    </script>
</div>
