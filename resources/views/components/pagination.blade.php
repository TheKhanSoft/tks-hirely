@if ($paginator->hasPages())
    @php
        $isLivewire = isset($this) && method_exists($this, 'previousPage');
        $pageName = $paginator->getPageName();
    @endphp

    <nav role="navigation" aria-label="Pagination Navigation" style="display: inline-flex; align-items: center; gap: 0.35rem; user-select: none;">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--color-text-muted); opacity: 0.4; cursor: not-allowed;" aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </span>
        @else
            <button type="button"
                @if($isLivewire)
                    wire:click="previousPage('{{ $pageName }}')"
                    wire:loading.attr="disabled"
                @else
                    onclick="window.location.href='{{ $paginator->previousPageUrl() }}'"
                @endif
                style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--color-text-secondary); cursor: pointer; transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);"
                onmouseover="this.style.borderColor='var(--primary)'; this.style.color='var(--primary)'; this.style.background='rgba(99, 102, 241, 0.08)';"
                onmouseout="this.style.borderColor='var(--border-color)'; this.style.color='var(--color-text-secondary)'; this.style.background='var(--bg-card)';"
                aria-label="{{ __('pagination.previous') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 34px; color: var(--color-text-muted); font-weight: 700; font-size: 0.9rem;" aria-disabled="true">
                    {{ $element }}
                </span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 34px; height: 34px; padding: 0 0.5rem; border-radius: 8px; background: linear-gradient(135deg, #6366f1, #4f46e5); color: #ffffff; font-weight: 800; font-size: 0.82rem; border: 1px solid rgba(99, 102, 241, 0.6); box-shadow: 0 2px 10px rgba(99, 102, 241, 0.35);" aria-current="page">
                            {{ $page }}
                        </span>
                    @else
                        <button type="button"
                            @if($isLivewire)
                                wire:click="gotoPage({{ $page }}, '{{ $pageName }}')"
                                wire:loading.attr="disabled"
                            @else
                                onclick="window.location.href='{{ $url }}'"
                            @endif
                            style="display: inline-flex; align-items: center; justify-content: center; min-width: 34px; height: 34px; padding: 0 0.5rem; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--color-text-secondary); font-weight: 700; font-size: 0.82rem; cursor: pointer; transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);"
                            onmouseover="this.style.borderColor='var(--primary)'; this.style.color='var(--primary)'; this.style.background='rgba(99, 102, 241, 0.08)';"
                            onmouseout="this.style.borderColor='var(--border-color)'; this.style.color='var(--color-text-secondary)'; this.style.background='var(--bg-card)';"
                            aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                            {{ $page }}
                        </button>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <button type="button"
                @if($isLivewire)
                    wire:click="nextPage('{{ $pageName }}')"
                    wire:loading.attr="disabled"
                @else
                    onclick="window.location.href='{{ $paginator->nextPageUrl() }}'"
                @endif
                style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--color-text-secondary); cursor: pointer; transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);"
                onmouseover="this.style.borderColor='var(--primary)'; this.style.color='var(--primary)'; this.style.background='rgba(99, 102, 241, 0.08)';"
                onmouseout="this.style.borderColor='var(--border-color)'; this.style.color='var(--color-text-secondary)'; this.style.background='var(--bg-card)';"
                aria-label="{{ __('pagination.next') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>
        @else
            <span style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-card); color: var(--color-text-muted); opacity: 0.4; cursor: not-allowed;" aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </span>
        @endif
    </nav>
@endif