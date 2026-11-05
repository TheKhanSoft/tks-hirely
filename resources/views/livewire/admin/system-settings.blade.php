<div x-data="{ 
    changeUrl(url) {
        window.history.pushState({}, '', url);
    }
}" x-on:url-changed.window="changeUrl($event.detail.url)">

    {{-- Universal Navigation Tabs for Settings --}}
    @include('livewire.admin.settings.navigation-tabs')

    {{-- Dynamic Active Settings View --}}
    @if($activeTab === 'pipeline')
        @include('livewire.admin.settings.workflows')
    @elseif($activeTab === 'documents')
        @include('livewire.admin.settings.documents')
    @elseif($activeTab === 'candidate_rules')
        @include('livewire.admin.settings.candidate-rules')
    @elseif($activeTab === 'organization')
        @include('livewire.admin.settings.organization')
    @elseif($activeTab === 'academic_criteria')
        @include('livewire.admin.settings.academic-criteria')
    @elseif($activeTab === 'branding')
        @include('livewire.admin.settings.branding')
    @elseif($activeTab === 'contact')
        @include('livewire.admin.settings.contact')
    @elseif($activeTab === 'email')
        @include('livewire.admin.settings.email')
    @else
        @include('livewire.admin.settings.workflows')
    @endif

</div>
