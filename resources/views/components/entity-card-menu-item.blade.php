@props([
    'label',
    'href' => null,
    'click' => null,
    'danger' => false,
    'impersonate' => false,
    'tooltip' => null,
])

@php
    $classes = 'entity-card-menu-item';
    if ($danger) {
        $classes .= ' danger';
    } elseif ($impersonate) {
        $classes .= ' impersonate';
    }
@endphp

@if($href)
    <a href="{{ $href }}" 
       class="{{ $classes }}" 
       @if($tooltip) data-tooltip="{{ $tooltip }}" @endif
       {{ $attributes }}>
        @if(isset($icon))
            {{ $icon }}
        @else
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="1"></circle>
                <circle cx="12" cy="5" r="1"></circle>
                <circle cx="12" cy="19" r="1"></circle>
            </svg>
        @endif
        <span>{{ $label }}</span>
    </a>
@else
    <button type="button" 
            @if($click) wire:click="{{ $click }}" @endif
            class="{{ $classes }}" 
            @if($tooltip) data-tooltip="{{ $tooltip }}" @endif
            {{ $attributes }}>
        @if(isset($icon))
            {{ $icon }}
        @else
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="1"></circle>
                <circle cx="12" cy="5" r="1"></circle>
                <circle cx="12" cy="19" r="1"></circle>
            </svg>
        @endif
        <span>{{ $label }}</span>
    </button>
@endif

