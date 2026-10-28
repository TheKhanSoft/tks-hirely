@props(['title' => null, 'subtitle' => null, 'style' => null])

<div {{ $attributes->merge(['class' => 'card']) }} style="{{ $style }}">
    @if($title || $subtitle)
        <div class="mb-4">
            @if($title)
                <h3 class="card-title">{{ $title }}</h3>
            @endif
            @if($subtitle)
                <p class="card-subtitle" style="margin-bottom: 0;">{{ $subtitle }}</p>
            @endif
        </div>
    @endif
    {{ $slot }}
</div>
