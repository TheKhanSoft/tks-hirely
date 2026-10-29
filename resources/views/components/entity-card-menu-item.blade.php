@props([
    'label',
    'href' => null,
    'click' => null,
    'wireClick' => null,
    'danger' => false,
    'impersonate' => false,
    'disabled' => false,
    'tooltip' => null,
    'icon' => null,
    'theme' => null,
    'confirmMessage' => null,
])

@php
    $actionClick = $click ?? $wireClick ?? $attributes->get('wire:click') ?? $attributes->get('wire-click');
    $cleanAttributes = $attributes->except(['wire-click', 'confirm-message']);

    $isDanger = $danger || $theme === 'danger';
    $isImpersonate = $impersonate || $theme === 'impersonate';

    $classes = 'entity-card-menu-item';
    if ($isDanger) {
        $classes .= ' danger';
    } elseif ($isImpersonate) {
        $classes .= ' impersonate';
    }
    if ($disabled) {
        $classes .= ' disabled';
    }

    $hasIcon = isset($icon);
    $hasSlot = isset($slot) && trim($slot->toHtml()) !== '';
@endphp

@if($href && !$disabled)
    <a href="{{ $href }}" 
       class="{{ $classes }}" 
       @if($tooltip) data-tooltip="{{ $tooltip }}" @endif
       {{ $cleanAttributes }}>
        @if($hasIcon)
            {{ $icon }}
        @elseif($hasSlot)
            {{ $slot }}
        @elseif($isDanger)
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            </svg>
        @else
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        @endif
        <span>{{ $label }}</span>
    </a>
@else
    <button type="button" 
            @if($actionClick && !$disabled) wire:click="{{ $actionClick }}" @endif
            @if($confirmMessage && !$disabled) wire:confirm="{{ $confirmMessage }}" @endif
            @if($disabled) disabled @endif
            class="{{ $classes }}" 
            @if($tooltip) data-tooltip="{{ $tooltip }}" @endif
            {{ $cleanAttributes }}>
        @if($hasIcon)
            {{ $icon }}
        @elseif($hasSlot)
            {{ $slot }}
        @elseif($isDanger)
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            </svg>
        @else
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        @endif
        <span>{{ $label }}</span>
    </button>
@endif
