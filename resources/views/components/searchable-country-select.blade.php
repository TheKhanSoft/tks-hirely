@props([
    'label' => 'Country *',
    'error' => null,
    'placeholder' => 'Search or select country...',
    'disabled' => false,
    'id' => null,
])

@php
    $id = $id ?? 'country_select_' . uniqid();
    $wireModel = $attributes->wire('model')->value();
    $countries = \App\Services\CountryService::all();
    $priority = \App\Services\CountryService::priority();
@endphp

<div class="form-group mb-3" style="position: relative;"
     x-data="{
        open: false,
        search: '',
        selected: @if($wireModel) @entangle($attributes->wire('model')) @else '' @endif,
        countries: @js($countries),
        priority: @js($priority),
        init() {
            if (this.selected) {
                this.search = this.selected;
            }
            this.$watch('selected', value => {
                if (value) this.search = value;
            });
        },
        get filtered() {
            if (!this.search || this.search === this.selected) {
                return this.countries;
            }
            const q = this.search.toLowerCase().trim();
            return this.countries.filter(c => c.toLowerCase().includes(q));
        },
        select(c) {
            this.selected = c;
            this.search = c;
            this.open = false;
        },
        clear() {
            this.selected = '';
            this.search = '';
            this.open = true;
            this.$refs.searchInput.focus();
        }
     }"
     @click.outside="open = false; if (!selected) { search = ''; } else { search = selected; }"
>
    @if($label)
        <label for="{{ $id }}" class="form-label" style="display: flex; align-items: center; justify-content: space-between; font-size: 0.86rem; font-weight: 700; color: var(--color-text-secondary); margin-bottom: 0.4rem;">
            <span>{{ $label }}</span>
        </label>
    @endif

    <div style="position: relative;">
        <!-- Left Globe Icon -->
        <div style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); pointer-events: none; color: var(--color-text-muted); display: flex; align-items: center;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="2" y1="12" x2="22" y2="12"></line>
                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
            </svg>
        </div>

        <!-- Searchable Input -->
        <input
            x-ref="searchInput"
            id="{{ $id }}"
            type="text"
            class="form-control"
            style="padding-left: 2.4rem; padding-right: 4.5rem; font-size: 0.9rem; background: var(--bg-card); border-color: var(--border-color); color: var(--color-text-primary);"
            placeholder="{{ $placeholder }}"
            x-model="search"
            @focus="open = true"
            @input="open = true"
            @keydown.escape="open = false"
            @keydown.arrow-down.prevent="$refs.list ? $refs.list.firstElementChild?.focus() : null"
            {{ $disabled ? 'disabled' : '' }}
            autocomplete="off"
        />

        <!-- Right Action Buttons (Clear & Dropdown Toggle) -->
        <div style="position: absolute; right: 0.65rem; top: 50%; transform: translateY(-50%); display: flex; align-items: center; gap: 0.25rem;">
            <button
                type="button"
                x-show="search.length > 0 && !{{ $disabled ? 'true' : 'false' }}"
                @click.stop="clear()"
                title="Clear selection"
                style="background: none; border: none; padding: 0.2rem; cursor: pointer; color: var(--color-text-muted); display: flex; align-items: center; border-radius: 4px;"
                class="hover-text-primary"
            >
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>

            <button
                type="button"
                @click="open = !open"
                style="background: none; border: none; padding: 0.2rem; cursor: pointer; color: var(--color-text-muted); display: flex; align-items: center;"
                tabindex="-1"
            >
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" :style="open ? 'transform: rotate(180deg);' : ''" style="transition: transform 0.2s;">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
        </div>

        <!-- Floating Dropdown Results -->
        <div
            x-ref="list"
            x-show="open"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 transform scale-95 -translate-y-1"
            x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 transform scale-95 -translate-y-1"
            style="position: absolute; top: calc(100% + 4px); left: 0; right: 0; max-height: 240px; overflow-y: auto; z-index: 2000; background: var(--bg-card-solid); border: 1px solid var(--border-hover); border-radius: var(--radius-sm); box-shadow: var(--shadow-lg); padding: 0.4rem 0;"
        >
            <template x-for="(country, idx) in filtered" :key="country">
                <div
                    @click="select(country)"
                    class="country-option"
                    style="padding: 0.55rem 0.9rem; font-size: 0.88rem; cursor: pointer; display: flex; align-items: center; justify-content: space-between; transition: background 0.15s;"
                    :style="selected === country ? 'background: var(--primary-light); color: var(--primary); font-weight: 700;' : 'color: var(--color-text-primary);'"
                    @mouseenter="$el.style.background = 'var(--bg-table-hover)'"
                    @mouseleave="$el.style.background = selected === country ? 'var(--primary-light)' : 'transparent'"
                >
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span x-text="country"></span>
                    </div>

                    <template x-if="selected === country">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </template>
                </div>
            </template>

            <div x-show="filtered.length === 0" style="padding: 1rem; text-align: center; color: var(--color-text-muted); font-size: 0.84rem;">
                No countries matching "<span x-text="search" style="color: var(--color-text-primary); font-weight: 600;"></span>"
            </div>
        </div>
    </div>

    @if($error)
        <span style="color: var(--danger); font-size: 0.82rem; margin-top: 0.35rem; display: block; font-weight: 500;">
            {{ $error }}
        </span>
    @endif
</div>
