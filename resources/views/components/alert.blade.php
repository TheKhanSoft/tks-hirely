@props(['type' => 'success', 'message' => null])

@php
    $type = strtolower($type);
    $isSuccess = $type === 'success';
    $isDanger = in_array($type, ['danger', 'error']);
    $isWarning = $type === 'warning';
    
    $accentColor = $isSuccess ? 'var(--success)' : ($isDanger ? 'var(--danger)' : ($isWarning ? 'var(--warning)' : 'var(--primary)'));
    $bgColor = $isSuccess ? 'var(--success-light)' : ($isDanger ? 'var(--danger-light)' : ($isWarning ? 'var(--warning-light)' : 'var(--primary-light)'));
    $borderColor = $isSuccess ? 'var(--success-glow)' : ($isDanger ? 'var(--danger-glow)' : ($isWarning ? 'var(--warning-glow)' : 'var(--primary-glow)'));
@endphp

<div {{ $attributes->merge(['class' => 'card']) }} style="border-color: {{ $borderColor }}; background: {{ $bgColor }}; margin-bottom: 1.5rem; padding: 1rem 1.25rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
    <div style="display: flex; align-items: center; gap: 0.75rem;">
        @if($isSuccess)
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $accentColor }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
        @elseif($isDanger)
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $accentColor }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        @elseif($isWarning)
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $accentColor }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
        @else
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $accentColor }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="16" x2="12" y2="12"/>
                <line x1="12" y1="8" x2="12.01" y2="8"/>
            </svg>
        @endif
        <div style="color: {{ $accentColor }}; font-weight: 600; font-size: 0.92rem;">
            {{ $message ?? $slot }}
        </div>
    </div>
    <button type="button" style="background: none; border: none; color: {{ $accentColor }}; font-size: 1.3rem; line-height: 1; cursor: pointer; opacity: 0.75; transition: opacity 0.15s;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.75" onclick="this.closest('.card').remove()">×</button>
</div>
