@props(['show' => false, 'title' => null, 'closeAction' => null, 'style' => null, 'maxWidth' => null])

@if($show)
    <div style="position: fixed; inset: 0; background: rgba(8, 12, 22, 0.85); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); display: flex; align-items: center; justify-content: center; z-index: 10000; padding: 1.5rem; overflow-y: auto;">
        <div class="card modal-dialog-box" style="width: 100%; max-width: {{ $maxWidth ?? '620px' }}; border-color: var(--border-color); box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.65), 0 0 0 1px var(--border-color); background: var(--bg-card-solid); margin: auto; max-height: calc(100vh - 3rem); display: flex; flex-direction: column; overflow: hidden; {{ $style }}">
            @if($title || $closeAction)
                <div class="modal-header-strip flex justify-between align-center" style="border-bottom: 1px solid var(--border-color); padding: 1rem 1.35rem; background: var(--bg-surface); flex-shrink: 0;">
                    @if($title)
                        <h3 class="card-title" style="margin: 0; font-size: 1.12rem; font-weight: 800; color: var(--color-text-primary); font-family: 'Outfit', sans-serif;">{{ $title }}</h3>
                    @endif
                    @if($closeAction)
                        <button type="button" class="btn btn-secondary btn-sm" wire:click="{{ $closeAction }}" style="padding: 0.35rem 0.75rem; border-radius: 7px; display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 700;" aria-label="Close modal">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                            <span>Close</span>
                        </button>
                    @endif
                </div>
            @endif
            <div class="modal-body-scrollable" style="overflow-y: auto; padding: 1.35rem; flex: 1 1 auto; -webkit-overflow-scrolling: touch;">
                {{ $slot }}
            </div>
        </div>
    </div>
@endif
