@props(['type' => 'draft'])

<span {{ $attributes->merge(['class' => 'badge badge-' . strtolower($type)]) }}>
    {{ $slot }}
</span>
