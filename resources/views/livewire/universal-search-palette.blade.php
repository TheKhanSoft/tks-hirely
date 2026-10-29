<div
    x-data="{
        open: @entangle('isOpen'),
        activeIndex: @entangle('selectedIndex'),
        init() {
            this.$watch('open', value => {
                if (value) {
                    this.$nextTick(() => {
                        const input = document.getElementById('universal-search-input');
                        if (input) {
                            input.focus();
                            input.select();
                        }
                    });
                }
            });
        },
        navigateDown() {
            const items = document.querySelectorAll('.palette-result-item');
            if (items.length > 0) {
                this.activeIndex = (this.activeIndex + 1) % items.length;
                this.scrollIntoView();
            }
        },
        navigateUp() {
            const items = document.querySelectorAll('.palette-result-item');
            if (items.length > 0) {
                this.activeIndex = (this.activeIndex - 1 + items.length) % items.length;
                this.scrollIntoView();
            }
        },
        selectItem() {
            const activeEl = document.querySelector('.palette-result-item.active-selection');
            if (activeEl) {
                const link = activeEl.getAttribute('href');
                if (link) {
                    window.location.href = link;
                }
            }
        },
        scrollIntoView() {
            this.$nextTick(() => {
                const activeEl = document.querySelector('.palette-result-item.active-selection');
                if (activeEl) {
                    activeEl.scrollIntoView({ block: 'nearest' });
                }
            });
        }
    }"
    @keydown.window.prevent.ctrl.k="$wire.openPalette()"
    @keydown.window.prevent.cmd.k="$wire.openPalette()"
    @keydown.escape.window="$wire.closePalette()"
    @open-search-palette.window="$wire.openPalette()"
    x-show="open"
    x-cloak
    class="palette-overlay"
    style="display: none;"
    role="dialog"
    aria-modal="true"
    aria-label="Universal Search & Command Palette"
>
    <!-- Glassmorphic Backdrop -->
    <div class="palette-backdrop" @click="$wire.closePalette()"></div>

    <!-- Command Palette Container -->
    <div
        class="palette-modal"
        @keydown.arrow-down.prevent="navigateDown()"
        @keydown.arrow-up.prevent="navigateUp()"
        @keydown.enter.prevent="selectItem()"
    >
        <!-- Search Input Bar -->
        <div class="palette-search-header">
            <div class="palette-search-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
            </div>

            <input
                id="universal-search-input"
                x-ref="searchInput"
                type="text"
                class="palette-input"
                placeholder="Search across modules, applications, campaigns, candidates..."
                wire:model.live.debounce.150ms="query"
                autocomplete="off"
                spellcheck="false"
            />

            <!-- Spinner / Clear / Esc Badges -->
            <div class="palette-input-actions">
                <div wire:loading wire:target="query" class="palette-loading-spinner" title="Searching records..."></div>

                @if(!empty($query))
                    <button
                        type="button"
                        class="palette-clear-btn"
                        wire:click="$set('query', '')"
                        title="Clear search text"
                    >
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                @endif

                <kbd class="palette-kbd-badge" wire:click="closePalette" title="Press Esc to close">ESC</kbd>
            </div>
        </div>

        <!-- Filter Categories Tabs -->
        @if(count($categories) > 1)
            <div class="palette-category-tabs">
                @foreach($categories as $catKey => $catInfo)
                    <button
                        type="button"
                        class="palette-category-tab {{ $activeCategory === $catKey ? 'active' : '' }}"
                        wire:click="selectCategory('{{ $catKey }}')"
                    >
                        @if($catInfo['icon'] === 'sparkles')
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3Z"/></svg>
                        @elseif($catInfo['icon'] === 'layout')
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                        @elseif($catInfo['icon'] === 'briefcase')
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                        @elseif($catInfo['icon'] === 'calendar')
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                        @elseif($catInfo['icon'] === 'file-text')
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        @elseif($catInfo['icon'] === 'users')
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        @elseif($catInfo['icon'] === 'award')
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                        @elseif($catInfo['icon'] === 'credit-card')
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                        @elseif($catInfo['icon'] === 'sliders')
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="4" x2="4" y1="21" y2="14"/><line x1="4" x2="4" y1="10" y2="3"/><line x1="12" x2="12" y1="21" y2="12"/><line x1="12" x2="12" y1="8" y2="3"/><line x1="20" x2="20" y1="21" y2="16"/><line x1="20" x2="20" y1="12" y2="3"/><line x1="1" x2="7" y1="14" y2="14"/><line x1="9" x2="15" y1="8" y2="8"/><line x1="17" x2="23" y1="16" y2="16"/></svg>
                        @elseif($catInfo['icon'] === 'shield')
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        @endif
                        <span>{{ $catInfo['label'] }}</span>
                    </button>
                @endforeach
            </div>
        @endif

        <!-- Results List Section -->
        <div class="palette-results-container">
            @if(count($results) > 0)
                <div class="palette-results-list">
                    @php $idx = 0; @endphp
                    @foreach($results as $item)
                        <a
                            href="{{ $item['url'] }}"
                            class="palette-result-item"
                            :class="{ 'active-selection': activeIndex === {{ $idx }} }"
                            @mouseenter="activeIndex = {{ $idx }}"
                            data-index="{{ $idx }}"
                            id="palette-item-{{ $idx }}"
                        >
                            <!-- Left Category Icon Box -->
                            <div class="palette-item-icon-box {{ $item['badge_style'] ?? 'badge-slate' }}">
                                @if($item['icon'] === 'layout')
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                                @elseif($item['icon'] === 'briefcase')
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                @elseif($item['icon'] === 'calendar')
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                                @elseif($item['icon'] === 'file-text')
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                @elseif($item['icon'] === 'users')
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                @elseif($item['icon'] === 'award')
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                                @elseif($item['icon'] === 'credit-card')
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                                @elseif($item['icon'] === 'sliders')
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="4" x2="4" y1="21" y2="14"/><line x1="4" x2="4" y1="10" y2="3"/><line x1="12" x2="12" y1="21" y2="12"/><line x1="12" x2="12" y1="8" y2="3"/><line x1="20" x2="20" y1="21" y2="16"/><line x1="20" x2="20" y1="12" y2="3"/></svg>
                                @elseif($item['icon'] === 'shield')
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                @else
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                                @endif
                            </div>

                            <!-- Content Meta -->
                            <div class="palette-item-content">
                                <div class="palette-item-title-row">
                                    <span class="palette-item-title">{{ $item['title'] }}</span>
                                    @if(!empty($item['badge']))
                                        <span class="palette-item-badge {{ $item['badge_style'] ?? 'badge-slate' }}">{{ $item['badge'] }}</span>
                                    @endif
                                </div>
                                <div class="palette-item-subtitle">{{ $item['subtitle'] }}</div>
                            </div>

                            <!-- Right Arrow / Meta Indicator -->
                            <div class="palette-item-action">
                                @if(!empty($item['meta']))
                                    <span class="palette-item-meta">{{ $item['meta'] }}</span>
                                @endif
                                <span class="palette-enter-hint" title="Press Enter to open">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 18 15 12 9 6"/></svg>
                                </span>
                            </div>
                        </a>
                        @php $idx++; @endphp
                    @endforeach
                </div>
            @else
                <!-- No Results Empty State -->
                <div class="palette-empty-state">
                    <div class="palette-empty-icon">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.3-4.3"/>
                            <line x1="8" y1="11" x2="14" y2="11"/>
                        </svg>
                    </div>
                    <h4>No Matching Records Found</h4>
                    <p>
                        @if(!empty($query))
                            No records found for "<strong>{{ $query }}</strong>" in <em>{{ $categories[$activeCategory]['label'] ?? 'All Categories' }}</em>.
                        @else
                            No accessible records found under the selected category.
                        @endif
                    </p>
                    <div class="palette-empty-tips">
                        <span>Tip: Try typing a candidate name, CNIC, tracking ID, job code, or role title.</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Footer / Shortcuts Legend -->
        <div class="palette-footer">
            <div class="palette-shortcuts-hints">
                <span class="palette-hint-group">
                    <kbd>↑</kbd><kbd>↓</kbd>
                    <span>navigate</span>
                </span>
                <span class="palette-hint-group">
                    <kbd>↵</kbd>
                    <span>select</span>
                </span>
                <span class="palette-hint-group">
                    <kbd>esc</kbd>
                    <span>close</span>
                </span>
            </div>

            <div class="palette-footer-status">
                <span class="palette-dot-status"></span>
                <span>Permission-Gated Search • Spatie RBAC</span>
            </div>
        </div>
    </div>
</div>

