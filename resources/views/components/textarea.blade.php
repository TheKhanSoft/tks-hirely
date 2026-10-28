@props(['label' => null, 'error' => null])

<div class="form-group">
    @if($label)
        <label class="form-label">{{ $label }}</label>
    @endif
    <div style="position: relative;">
        {{ $slot }}
    </div>
    @if($error)
        <span style="color: var(--danger); font-size: 0.85rem; display: block; margin-top: 0.25rem;">{{ $error }}</span>
    @endif
</div>
