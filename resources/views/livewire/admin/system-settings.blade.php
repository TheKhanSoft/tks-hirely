<div x-data="{ 
    changeUrl(url) {
        window.history.pushState({}, '', url);
    },
    settingsFilter: ''
}" x-on:url-changed.window="changeUrl($event.detail.url)">

    {{-- Executive Header, Breadcrumbs & Feedback Alerts --}}
    @include('livewire.admin.settings.navigation-tabs')

    {{-- Master-Detail Two-Column Layout Shell --}}
    <div class="settings-master-shell">
        
        {{-- Left Column: Master Settings Sidebar --}}
        @include('livewire.admin.settings.sidebar')

        {{-- Right Column: Active Settings Detail Canvas --}}
        <main class="settings-detail-canvas" id="settingsCanvas">
            @if($activeTab === 'organization')
                @include('livewire.admin.settings.organization')
            @elseif($activeTab === 'branding')
                @include('livewire.admin.settings.branding')
            @elseif($activeTab === 'pipeline')
                @include('livewire.admin.settings.workflows')
            @elseif($activeTab === 'academic_criteria')
                @include('livewire.admin.settings.academic-criteria')
            @elseif($activeTab === 'candidate_rules')
                @include('livewire.admin.settings.candidate-rules')
            @elseif($activeTab === 'documents')
                @include('livewire.admin.settings.documents')
            @elseif($activeTab === 'email_templates')
                @include('livewire.admin.settings.email-templates')
            @elseif($activeTab === 'email')
                @include('livewire.admin.settings.email')
            @elseif($activeTab === 'contact')
                @include('livewire.admin.settings.contact')
            @elseif($activeTab === 'security')
                @include('livewire.admin.settings.security')
            @elseif($activeTab === 'system')
                @include('livewire.admin.settings.system')
            @else
                @include('livewire.admin.settings.workflows')
            @endif
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
