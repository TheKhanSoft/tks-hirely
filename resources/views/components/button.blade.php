@props(['type' => 'button', 'variant' => 'primary', 'style' => null])

<button type="{{ $type }}" {{ $attributes->merge(['class' => 'btn btn-' . $variant]) }} style="{{ $style }}">
    {{ $slot }}
</button>
