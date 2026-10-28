@props(['show' => false, 'title' => null, 'closeAction' => null, 'style' => null])

@if($show)
    <div style="position: fixed; inset: 0; background: rgba(0, 0, 0, 0.82); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 1.5rem;">
        <div class="card" style="width: 100%; max-width: 620px; border-color: var(--border-color); box-shadow: var(--shadow-lg); background: var(--bg-card-solid); {{ $style }}">
            @if($title || $closeAction)
                <div class="flex justify-between align-center mb-4" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.85rem; margin-bottom: 1.25rem;">
                    @if($title)
                        <h3 class="card-title" style="margin: 0; font-size: 1.15rem;">{{ $title }}</h3>
                    @endif
                    @if($closeAction)
                        <button type="button" class="btn btn-secondary btn-sm" wire:click="{{ $closeAction }}">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                            <span>Close</span>
                        </button>
                    @endif
                </div>
            @endif
            {{ $slot }}
        </div>
    </div>
@endif
