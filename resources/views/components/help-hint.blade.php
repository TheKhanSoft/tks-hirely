@props([
    'type' => 'inline',          // 'inline', 'callout', 'drawer', 'badge'
    'variant' => 'info',         // 'info', 'statutory', 'warning', 'tip', 'danger'
    'title' => null,             // Headline / title
    'text' => null,              // Detailed description
    'statute' => null,           // E.g. "HEC Faculty Policy 2024", "Statutory Act Sec. 14(2)"
    'impact' => null,            // E.g. "Prevents voucher generation if unpaid"
    'example' => null,           // E.g. "e.g. 14 days from advertisement closing"
    'placement' => 'top',        // Tooltip placement: 'top', 'bottom', 'left', 'right'
])

@php
    // Theme palette mappings based on variant
    $themeStyles = match($variant) {
        'statutory' => [
            'icon_bg' => 'rgba(147, 51, 234, 0.15)',
            'icon_border' => 'rgba(147, 51, 234, 0.3)',
            'icon_color' => '#a855f7',
            'border' => 'rgba(147, 51, 234, 0.28)',
            'bg' => 'rgba(147, 51, 234, 0.05)',
            'badge_bg' => 'rgba(147, 51, 234, 0.15)',
            'badge_color' => '#c084fc',
            'badge_label' => '⚖️ STATUTORY MANDATE',
        ],
        'warning' => [
            'icon_bg' => 'rgba(245, 158, 11, 0.15)',
            'icon_border' => 'rgba(245, 158, 11, 0.3)',
            'icon_color' => 'var(--warning)',
            'border' => 'rgba(245, 158, 11, 0.28)',
            'bg' => 'rgba(245, 158, 11, 0.05)',
            'badge_bg' => 'rgba(245, 158, 11, 0.15)',
            'badge_color' => '#fbbf24',
            'badge_label' => '⚠️ STRICT RESTRICTION',
        ],
        'tip' => [
            'icon_bg' => 'rgba(16, 185, 129, 0.15)',
            'icon_border' => 'rgba(16, 185, 129, 0.3)',
            'icon_color' => 'var(--success)',
            'border' => 'rgba(16, 185, 129, 0.28)',
            'bg' => 'rgba(16, 185, 129, 0.05)',
            'badge_bg' => 'rgba(16, 185, 129, 0.15)',
            'badge_color' => '#34d399',
            'badge_label' => '💡 BEST PRACTICE',
        ],
        'danger' => [
            'icon_bg' => 'rgba(239, 68, 68, 0.15)',
            'icon_border' => 'rgba(239, 68, 68, 0.3)',
            'icon_color' => 'var(--danger)',
            'border' => 'rgba(239, 68, 68, 0.28)',
            'bg' => 'rgba(239, 68, 68, 0.05)',
            'badge_bg' => 'rgba(239, 68, 68, 0.15)',
            'badge_color' => '#f87171',
            'badge_label' => '🚫 DISQUALIFYING POLICY',
        ],
        default => [
            'icon_bg' => 'rgba(99, 102, 241, 0.15)',
            'icon_border' => 'rgba(99, 102, 241, 0.3)',
            'icon_color' => 'var(--primary)',
            'border' => 'rgba(99, 102, 241, 0.25)',
            'bg' => 'rgba(99, 102, 241, 0.04)',
            'badge_bg' => 'rgba(99, 102, 241, 0.15)',
            'badge_color' => '#818cf8',
            'badge_label' => 'ℹ️ OPERATIONAL GUIDE',
        ],
    };
@endphp

{{-- ========================================================================= --}}
{{-- 1. INLINE MODE: Sleek question mark / info icon with popover tooltip --}}
{{-- ========================================================================= --}}
@if($type === 'inline')
    <span class="help-hint-inline-wrapper" 
          x-data="{ 
              show: false,
              placeBottom: false,
              checkPlacement() {
                  const rect = this.$el.getBoundingClientRect();
                  this.placeBottom = (rect.top < 240);
              }
          }" 
          @mouseleave="show = false" 
          style="position: relative; display: inline-flex; align-items: center; vertical-align: middle; margin-left: 0.35rem;">
        
        <button type="button" 
                @mouseenter="checkPlacement(); show = true" 
                @focus="checkPlacement(); show = true" 
                @blur="show = false" 
                @click.prevent="checkPlacement(); show = !show"
                aria-label="View help information"
                class="help-hint-trigger"
                style="background: {{ $themeStyles['icon_bg'] }}; border: 1px solid {{ $themeStyles['icon_border'] }}; color: {{ $themeStyles['icon_color'] }}; width: 18px; height: 18px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 0.68rem; font-weight: 800; cursor: pointer; transition: all 0.2s ease; padding: 0; outline: none; line-height: 1;">
            ?
        </button>

        {{-- Floating Popover (CRITICAL: Never use .card here to prevent max-width: 100% squashing) --}}
        <div x-show="show" 
             x-cloak 
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="help-hint-popover"
             :style="placeBottom 
                 ? 'top: calc(100% + 8px); bottom: auto;' 
                 : 'bottom: calc(100% + 8px); top: auto;'"
             style="position: absolute; left: 50%; transform: translateX(-50%); width: 310px; min-width: 270px; max-width: min(90vw, 340px); z-index: 9999; padding: 0.85rem 1rem; background: var(--bg-card-solid); border: 1px solid var(--border-color); box-shadow: 0 12px 30px -4px rgba(0,0,0,0.35), 0 2px 8px rgba(0,0,0,0.15); border-radius: 10px; font-size: 0.8rem; pointer-events: none; text-align: left; white-space: normal; word-break: normal; overflow-wrap: break-word; box-sizing: border-box;">
            
            @if($statute)
                <div style="font-size: 0.68rem; font-weight: 800; text-transform: uppercase; color: {{ $themeStyles['badge_color'] }}; letter-spacing: 0.05em; margin-bottom: 0.35rem; display: flex; align-items: center; gap: 0.3rem;">
                    <span>📜</span>
                    <span>{{ $statute }}</span>
                </div>
            @endif

            @if($title)
                <div style="font-weight: 800; color: var(--color-text-primary); font-size: 0.84rem; margin-bottom: 0.25rem; line-height: 1.3;">
                    {{ $title }}
                </div>
            @endif

            <div style="color: var(--color-text-secondary); line-height: 1.48; font-size: 0.79rem;">
                {{ $text ?? $slot }}
            </div>

            @if($impact)
                <div style="margin-top: 0.5rem; padding-top: 0.45rem; border-top: 1px solid var(--border-color); font-size: 0.74rem; color: var(--warning); display: flex; align-items: flex-start; gap: 0.35rem; line-height: 1.35;">
                    <span style="font-weight: 800; flex-shrink: 0;">Impact:</span>
                    <span style="color: var(--color-text-primary);">{{ $impact }}</span>
                </div>
            @endif

            {{-- Pointer arrow --}}
            <div :style="placeBottom
                    ? 'bottom: 100%; top: auto; border-left: 6px solid transparent; border-right: 6px solid transparent; border-bottom: 6px solid var(--border-color);'
                    : 'top: 100%; bottom: auto; border-left: 6px solid transparent; border-right: 6px solid transparent; border-top: 6px solid var(--border-color);'"
                 style="position: absolute; left: 50%; transform: translateX(-50%); width: 0; height: 0;"></div>
        </div>
    </span>

{{-- ========================================================================= --}}
{{-- 2. CALLOUT MODE: Prominent guidance box with headline and detailed text --}}
{{-- ========================================================================= --}}
@elseif($type === 'callout')
    <div class="help-hint-callout" 
         style="background: {{ $themeStyles['bg'] }}; border: 1px solid {{ $themeStyles['border'] }}; border-radius: var(--radius-md); padding: 1rem 1.25rem; margin-top: 0.75rem; margin-bottom: 0.75rem;">
        <div style="display: flex; align-items: flex-start; gap: 0.85rem;">
            <div style="width: 32px; height: 32px; border-radius: 8px; background: {{ $themeStyles['icon_bg'] }}; border: 1px solid {{ $themeStyles['icon_border'] }}; color: {{ $themeStyles['icon_color'] }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1rem;">
                @if($variant === 'statutory') ⚖️
                @elseif($variant === 'warning') ⚠️
                @elseif($variant === 'tip') 💡
                @elseif($variant === 'danger') 🚫
                @else ℹ️ @endif
            </div>

            <div style="flex: 1;">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.3rem;">
                    @if($title)
                        <h5 style="margin: 0; font-size: 0.9rem; font-weight: 800; color: var(--color-text-primary);">
                            {{ $title }}
                        </h5>
                    @endif

                    @if($statute)
                        <span class="badge" style="background: {{ $themeStyles['badge_bg'] }}; color: {{ $themeStyles['badge_color'] }}; font-size: 0.68rem; font-weight: 800; border: 1px solid {{ $themeStyles['border'] }}; padding: 0.15rem 0.5rem; border-radius: 5px;">
                            {{ $statute }}
                        </span>
                    @endif
                </div>

                <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.5;">
                    {{ $text ?? $slot }}
                </div>

                @if($impact || $example)
                    <div style="margin-top: 0.6rem; padding-top: 0.5rem; border-top: 1px solid var(--border-color); display: flex; flex-direction: column; gap: 0.25rem; font-size: 0.78rem;">
                        @if($impact)
                            <div style="color: var(--warning); display: flex; align-items: flex-start; gap: 0.35rem;">
                                <strong style="color: var(--color-text-primary);">⚡ Operational Impact:</strong>
                                <span>{{ $impact }}</span>
                            </div>
                        @endif
                        @if($example)
                            <div style="color: var(--color-text-muted); display: flex; align-items: flex-start; gap: 0.35rem;">
                                <strong style="color: var(--color-text-primary);">📌 Example Setting:</strong>
                                <span>{{ $example }}</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

{{-- ========================================================================= --}}
{{-- 3. DRAWER / ACCORDION MODE: Interactive expandable guideline drawer --}}
{{-- ========================================================================= --}}
@elseif($type === 'drawer' || $type === 'expandable')
    <div x-data="{ expanded: false }" 
         class="help-hint-drawer" 
         style="margin: 0.65rem 0; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); overflow: hidden;">
        
        <button type="button" 
                @click="expanded = !expanded" 
                style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 0.65rem 1rem; background: transparent; border: none; cursor: pointer; text-align: left; color: var(--color-text-secondary); font-size: 0.8rem; font-weight: 700;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="font-size: 0.95rem;">💡</span>
                <span style="color: var(--color-text-primary);">{{ $title ?? 'Policy Guidelines & Statutory Reference' }}</span>
                @if($statute)
                    <span class="badge" style="background: {{ $themeStyles['badge_bg'] }}; color: {{ $themeStyles['badge_color'] }}; font-size: 0.65rem; font-weight: 800; padding: 0.1rem 0.4rem;">
                        {{ $statute }}
                    </span>
                @endif
            </div>

            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" 
                 :style="expanded ? 'transform: rotate(180deg);' : ''" 
                 style="transition: transform 0.2s ease;">
                <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
        </button>

        <div x-show="expanded" x-cloak style="display: none; padding: 0.85rem 1rem; border-top: 1px solid var(--border-color); background: var(--bg-card-solid); font-size: 0.8rem; color: var(--color-text-secondary); line-height: 1.55;">
            {{ $text ?? $slot }}

            @if($impact)
                <div style="margin-top: 0.6rem; padding: 0.5rem 0.75rem; border-radius: 6px; background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.25); color: var(--warning); font-size: 0.76rem;">
                    <strong>Notice:</strong> {{ $impact }}
                </div>
            @endif
        </div>
    </div>

{{-- ========================================================================= --}}
{{-- 4. BADGE MODE: Small inline pill badge with tooltip --}}
{{-- ========================================================================= --}}
@else
    <span class="help-hint-badge badge" 
          title="{{ $text ?? ($title ?? '') }}"
          style="background: {{ $themeStyles['badge_bg'] }}; color: {{ $themeStyles['badge_color'] }}; border: 1px solid {{ $themeStyles['border'] }}; font-size: 0.72rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.2rem 0.6rem; border-radius: 9999px;">
        <span>{{ $title ?? $themeStyles['badge_label'] }}</span>
        @if($text)
            <span style="opacity: 0.7; font-size: 0.65rem;">ℹ</span>
        @endif
    </span>
@endif
