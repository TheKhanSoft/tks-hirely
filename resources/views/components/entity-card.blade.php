@props([
    'id' => null,
    'title' => '',
    'subtitle' => null,
    'avatar' => null,
    'avatarInitial' => null,
    'avatarImage' => null,
    'avatarBadge' => null,
    'avatarBg' => null,
    'avatarClass' => null,
    'badge' => null,
    'headerBadge' => null,
    'statusBadge' => null,
    'statusClass' => 'badge-paid',
    'tag' => null,
    'status' => null,
    'isActive' => null,
])

@php
    $cardStatusClass = '';
    if ($isActive === false || $status === 'inactive') {
        $cardStatusClass = 'status-inactive';
    } elseif ($isActive === true || $status === 'active') {
        $cardStatusClass = 'status-active';
    } elseif ($status === 'warning' || $status === 'pending') {
        $cardStatusClass = 'status-warning';
    }
@endphp

<div class="entity-card {{ $cardStatusClass }}" 
     @if($id) wire:key="card-{{ $id }}" @endif
     x-data="{ menuOpen: false }"
     @click.outside="menuOpen = false">
    
    <!-- Top Header (Full width for Avatar + Title & Subtitle) -->
    <div>
        <div class="entity-card-header">
            <div style="display: flex; align-items: center; gap: 0.75rem; min-width: 0; flex: 1;">
                @if(isset($avatarSlot))
                    {{ $avatarSlot }}
                @elseif($avatarImage)
                    <div class="entity-card-avatar {{ $avatarClass }}" style="overflow: hidden; padding: 0; border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; {{ $avatarBg }}">
                        <img src="{{ $avatarImage }}" alt="{{ $title }}" onerror="this.style.display='none'; this.parentElement.innerText='{{ substr($title, 0, 1) }}';" style="width: 100%; height: 100%; object-fit: cover; border-radius: inherit;">
                    </div>
                @elseif($avatar || $avatarInitial)
                    <div class="entity-card-avatar {{ $avatarClass }}" @if($avatarBg) style="{{ $avatarBg }}" @endif>
                        {{ $avatar ?: $avatarInitial }}
                    </div>
                @endif

                <div class="entity-card-info" style="flex: 1; min-width: 0;">
                    @if(isset($titleSlot))
                        {{ $titleSlot }}
                    @else
                        <div class="entity-card-title" title="{{ $title }}">{{ $title }}</div>
                    @endif
                    @if($subtitle)
                        <div class="entity-card-subtitle" title="{{ $subtitle }}">{{ $subtitle }}</div>
                    @endif
                </div>
            </div>

            @if(isset($headerBadgeSlot))
                <div style="flex-shrink: 0;">
                    {{ $headerBadgeSlot }}
                </div>
            @elseif($headerBadge)
                <div style="flex-shrink: 0;">
                    {!! $headerBadge !!}
                </div>
            @endif
        </div>

        @if(isset($tagSlot))
            <div style="margin-bottom: 0.5rem;">
                {{ $tagSlot }}
            </div>
        @elseif($tag)
            <div style="margin-bottom: 0.5rem;">
                @if(str_starts_with($tag, '<'))
                    {!! $tag !!}
                @else
                    <span class="badge" style="background: rgba(255,255,255,0.06); color: var(--color-text-secondary); font-size: 0.72rem; font-weight: 700;">
                        {{ $tag }}
                    </span>
                @endif
            </div>
        @endif
    </div>

    <!-- Body / Metadata -->
    <div class="entity-card-body">
        {{ $body ?? $slot }}
    </div>

    <!-- Dedicated Status Strip Above Action Footer -->
    @if(isset($statusStrip))
        <div class="entity-card-status-strip {{ ($isActive === false || $status === 'inactive') ? 'is-inactive' : 'is-active' }}">
            {{ $statusStrip }}
        </div>
    @elseif(isset($badgeSlot))
        <div class="entity-card-status-strip {{ ($isActive === false || $status === 'inactive') ? 'is-inactive' : 'is-active' }}">
            {{ $badgeSlot }}
        </div>
    @elseif(isset($badge))
        <div class="entity-card-status-strip {{ ($isActive === false || $status === 'inactive') ? 'is-inactive' : 'is-active' }}">
            {!! $badge !!}
        </div>
    @elseif($statusBadge)
        <div class="entity-card-status-strip {{ ($isActive === false || $status === 'inactive') ? 'is-inactive' : 'is-active' }}">
            <span class="badge {{ $statusClass }}" style="font-size: 0.74rem; font-weight: 800; width: 100%; text-align: center;">
                {{ $statusBadge }}
            </span>
        </div>
    @endif

    <!-- Footer / Action Row -->
    <div class="entity-card-footer">
        <!-- Quick Action Icons -->
        <div class="entity-card-quick-actions">
            @if(isset($quickActions))
                {{ $quickActions }}
            @endif
        </div>

        <!-- 3-Dot More Actions Dropdown -->
        @if(isset($menuActions) || isset($menuItems))
            <div style="position: relative;">
                <button type="button" 
                        class="entity-card-menu-btn" 
                        @click="menuOpen = !menuOpen" 
                        aria-label="More Options"
                        data-tooltip="More actions">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="1.5"></circle>
                        <circle cx="6" cy="12" r="1.5"></circle>
                        <circle cx="18" cy="12" r="1.5"></circle>
                    </svg>
                </button>

                <!-- Floating Dropdown Menu [ICON + TEXT] -->
                <div class="entity-card-dropdown" 
                     x-show="menuOpen" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95"
                     @click="menuOpen = false"
                     style="display: none;">
                    {{ $menuActions ?? $menuItems }}
                </div>
            </div>
        @endif
    </div>
</div>
