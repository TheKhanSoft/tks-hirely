@props(['show' => false, 'title' => null, 'closeAction' => null, 'style' => null])

@if($show)
    <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 1000;">
        <div class="card" style="width: 100%; max-width: 600px; border-color: var(--border-color); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5); {{ $style }}">
            @if($title || $closeAction)
                <div class="flex justify-between align-center mb-4" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem; margin-bottom: 1.25rem;">
                    @if($title)
                        <h3 class="card-title" style="margin: 0;">{{ $title }}</h3>
                    @endif
                    @if($closeAction)
                        <button type="button" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="{{ $closeAction }}">Close</button>
                    @endif
                </div>
            @endif
            {{ $slot }}
        </div>
    </div>
@endif
