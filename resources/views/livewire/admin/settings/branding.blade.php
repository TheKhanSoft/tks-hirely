<div class="card mb-6" style="padding: 1.75rem; border-color: var(--primary); box-shadow: var(--shadow-md);">
    <!-- Card Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                <span class="badge badge-indigo" style="font-size: 0.72rem; font-weight: 800;">PLATFORM THEME &amp; DESIGN SYSTEM</span>
                <span class="badge badge-slate" style="font-size: 0.72rem;">MULTI-PORTAL STYLING</span>
            </div>
            <h2 class="card-title" style="margin: 0; font-size: 1.3rem; font-weight: 900; color: var(--color-text-primary);">
                Theme Studio, Branding, Logos &amp; UI/UX Customizer
            </h2>
            <p class="card-subtitle" style="margin: 0.25rem 0 0 0; font-size: 0.85rem; color: var(--color-text-secondary);">
                Customize primary brand accents, secondary highlights, corner radius, typography, card elevation, login layout, and visual assets linked across all portals.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
            @can('manage-settings')
            <button type="button" 
                    wire:click="resetThemeToDefault" 
                    wire:confirm="Reset all theme styling, colors, typography, and layout settings to system default?"
                    class="btn btn-secondary btn-sm" 
                    style="display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 700; padding: 0.55rem 0.95rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>
                <span>Reset Theme Defaults</span>
            </button>

            <button type="button" 
                    wire:click="saveBrandingSettings" 
                    class="btn btn-primary btn-sm" 
                    style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.55rem 1.25rem; font-weight: 800; box-shadow: 0 4px 14px var(--primary-glow);">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                <span wire:loading.remove wire:target="saveBrandingSettings">Save Theme &amp; Branding</span>
                <span wire:loading wire:target="saveBrandingSettings">Saving...</span>
            </button>
            @endcan
        </div>
    </div>

    @if(session()->has('message'))
        <div class="alert alert-success" style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3); color: var(--success); padding: 0.85rem 1.25rem; border-radius: var(--radius-md);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span style="font-weight: 700;">{{ session('message') }}</span>
        </div>
    @endif

    {{-- System Help Callout --}}
    <x-help-hint type="callout" variant="info" title="Global Dynamic Design System Architecture">
        Settings modified here are dynamically rendered via CSS custom properties across the whole application. Changing the primary color, corner radius, typography, or card elevation immediately transforms the login page, candidate portal, scrutiny desk, selection board, and administrative consoles.
    </x-help-hint>

    <form wire:submit.prevent="saveBrandingSettings" style="margin-top: 1.5rem;">
        <div style="display: flex; flex-direction: column; gap: 2rem;">

            <!-- ========================================================================= -->
            <!-- 1. ONE-CLICK INSTITUTIONAL THEME PRESETS                                   -->
            <!-- ========================================================================= -->
            <div style="background: var(--bg-card); border: 1.5px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                    <div>
                        <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--color-text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                            <span>✨ Institutional Theme Presets (1-Click Palette Apply)</span>
                        </h3>
                        <p style="font-size: 0.8rem; color: var(--color-text-secondary); margin: 0.2rem 0 0 0;">
                            Select a verified institutional color and typography palette to style every portal instantly.
                        </p>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0.85rem;">
                    <!-- Preset 1: Oxford Indigo -->
                    <button type="button" 
                            wire:click="applyThemePreset('oxford_indigo')"
                            class="card" 
                            style="cursor: pointer; text-align: left; padding: 1rem; border: 1.5px solid {{ $theme_accent_color === '#4f46e5' ? 'var(--primary)' : 'var(--border-color)' }}; background: {{ $theme_accent_color === '#4f46e5' ? 'var(--primary-light)' : 'var(--bg-surface)' }}; transition: var(--transition-fast);">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.65rem;">
                            <div style="display: flex; gap: 0.35rem;">
                                <span style="display: inline-block; width: 22px; height: 22px; border-radius: 50%; background: #4f46e5; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"></span>
                                <span style="display: inline-block; width: 22px; height: 22px; border-radius: 50%; background: #06b6d4; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2); margin-left: -8px;"></span>
                            </div>
                            @if($theme_accent_color === '#4f46e5')
                                <span class="badge badge-indigo" style="font-size: 0.65rem; font-weight: 800;">ACTIVE</span>
                            @endif
                        </div>
                        <strong style="font-size: 0.88rem; color: var(--color-text-primary); display: block;">Royal Oxford Indigo</strong>
                        <span style="font-size: 0.74rem; color: var(--color-text-secondary);">Indigo Primary • Cyan Highlight • Modern Radius</span>
                    </button>

                    <!-- Preset 2: Cambridge Emerald -->
                    <button type="button" 
                            wire:click="applyThemePreset('cambridge_emerald')"
                            class="card" 
                            style="cursor: pointer; text-align: left; padding: 1rem; border: 1.5px solid {{ $theme_accent_color === '#059669' ? 'var(--primary)' : 'var(--border-color)' }}; background: {{ $theme_accent_color === '#059669' ? 'var(--primary-light)' : 'var(--bg-surface)' }}; transition: var(--transition-fast);">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.65rem;">
                            <div style="display: flex; gap: 0.35rem;">
                                <span style="display: inline-block; width: 22px; height: 22px; border-radius: 50%; background: #059669; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"></span>
                                <span style="display: inline-block; width: 22px; height: 22px; border-radius: 50%; background: #0d9488; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2); margin-left: -8px;"></span>
                            </div>
                            @if($theme_accent_color === '#059669')
                                <span class="badge badge-emerald" style="font-size: 0.65rem; font-weight: 800;">ACTIVE</span>
                            @endif
                        </div>
                        <strong style="font-size: 0.88rem; color: var(--color-text-primary); display: block;">Cambridge Emerald</strong>
                        <span style="font-size: 0.74rem; color: var(--color-text-secondary);">Emerald Primary • Teal Highlight • Floating Elevation</span>
                    </button>

                    <!-- Preset 3: Harvard Crimson -->
                    <button type="button" 
                            wire:click="applyThemePreset('harvard_crimson')"
                            class="card" 
                            style="cursor: pointer; text-align: left; padding: 1rem; border: 1.5px solid {{ $theme_accent_color === '#be123c' ? 'var(--primary)' : 'var(--border-color)' }}; background: {{ $theme_accent_color === '#be123c' ? 'var(--primary-light)' : 'var(--bg-surface)' }}; transition: var(--transition-fast);">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.65rem;">
                            <div style="display: flex; gap: 0.35rem;">
                                <span style="display: inline-block; width: 22px; height: 22px; border-radius: 50%; background: #be123c; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"></span>
                                <span style="display: inline-block; width: 22px; height: 22px; border-radius: 50%; background: #f59e0b; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2); margin-left: -8px;"></span>
                            </div>
                            @if($theme_accent_color === '#be123c')
                                <span class="badge badge-rose" style="font-size: 0.65rem; font-weight: 800;">ACTIVE</span>
                            @endif
                        </div>
                        <strong style="font-size: 0.88rem; color: var(--color-text-primary); display: block;">Harvard Crimson</strong>
                        <span style="font-size: 0.74rem; color: var(--color-text-secondary);">Crimson Primary • Amber Highlight • Academic Serif</span>
                    </button>

                    <!-- Preset 4: MIT Sapphire -->
                    <button type="button" 
                            wire:click="applyThemePreset('mit_sapphire')"
                            class="card" 
                            style="cursor: pointer; text-align: left; padding: 1rem; border: 1.5px solid {{ $theme_accent_color === '#0284c7' ? 'var(--primary)' : 'var(--border-color)' }}; background: {{ $theme_accent_color === '#0284c7' ? 'var(--primary-light)' : 'var(--bg-surface)' }}; transition: var(--transition-fast);">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.65rem;">
                            <div style="display: flex; gap: 0.35rem;">
                                <span style="display: inline-block; width: 22px; height: 22px; border-radius: 50%; background: #0284c7; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"></span>
                                <span style="display: inline-block; width: 22px; height: 22px; border-radius: 50%; background: #6366f1; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2); margin-left: -8px;"></span>
                            </div>
                            @if($theme_accent_color === '#0284c7')
                                <span class="badge badge-cyan" style="font-size: 0.65rem; font-weight: 800;">ACTIVE</span>
                            @endif
                        </div>
                        <strong style="font-size: 0.88rem; color: var(--color-text-primary); display: block;">MIT Tech Sapphire</strong>
                        <span style="font-size: 0.74rem; color: var(--color-text-secondary);">Sky Sapphire • Indigo • Outfit Geometric</span>
                    </button>

                    <!-- Preset 5: Imperial Purple -->
                    <button type="button" 
                            wire:click="applyThemePreset('imperial_purple')"
                            class="card" 
                            style="cursor: pointer; text-align: left; padding: 1rem; border: 1.5px solid {{ $theme_accent_color === '#7c3aed' ? 'var(--primary)' : 'var(--border-color)' }}; background: {{ $theme_accent_color === '#7c3aed' ? 'var(--primary-light)' : 'var(--bg-surface)' }}; transition: var(--transition-fast);">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.65rem;">
                            <div style="display: flex; gap: 0.35rem;">
                                <span style="display: inline-block; width: 22px; height: 22px; border-radius: 50%; background: #7c3aed; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"></span>
                                <span style="display: inline-block; width: 22px; height: 22px; border-radius: 50%; background: #ec4899; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2); margin-left: -8px;"></span>
                            </div>
                            @if($theme_accent_color === '#7c3aed')
                                <span class="badge badge-purple" style="font-size: 0.65rem; font-weight: 800;">ACTIVE</span>
                            @endif
                        </div>
                        <strong style="font-size: 0.88rem; color: var(--color-text-primary); display: block;">Imperial Purple</strong>
                        <span style="font-size: 0.74rem; color: var(--color-text-secondary);">Royal Violet • Pink Rose • Organic Pill</span>
                    </button>

                    <!-- Preset 6: Slate Minimalist -->
                    <button type="button" 
                            wire:click="applyThemePreset('slate_minimalist')"
                            class="card" 
                            style="cursor: pointer; text-align: left; padding: 1rem; border: 1.5px solid {{ $theme_accent_color === '#1e293b' ? 'var(--primary)' : 'var(--border-color)' }}; background: {{ $theme_accent_color === '#1e293b' ? 'var(--primary-light)' : 'var(--bg-surface)' }}; transition: var(--transition-fast);">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.65rem;">
                            <div style="display: flex; gap: 0.35rem;">
                                <span style="display: inline-block; width: 22px; height: 22px; border-radius: 50%; background: #1e293b; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"></span>
                                <span style="display: inline-block; width: 22px; height: 22px; border-radius: 50%; background: #64748b; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2); margin-left: -8px;"></span>
                            </div>
                            @if($theme_accent_color === '#1e293b')
                                <span class="badge badge-slate" style="font-size: 0.65rem; font-weight: 800;">ACTIVE</span>
                            @endif
                        </div>
                        <strong style="font-size: 0.88rem; color: var(--color-text-primary); display: block;">Executive Charcoal</strong>
                        <span style="font-size: 0.74rem; color: var(--color-text-secondary);">Obsidian Slate • Steel Gray • Compact Razor</span>
                    </button>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- 2. CUSTOM COLOR PICKERS & LIVE MOCKUP PREVIEW                             -->
            <!-- ========================================================================= -->
            <div style="background: var(--bg-card); border: 1.5px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem;">
                <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                    <span>🎨 Custom Brand Colors &amp; Live Component Preview</span>
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; align-items: start;">
                    <!-- Controls Left -->
                    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                        <!-- Primary Accent -->
                        <div>
                            <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                                <span>Primary Brand Accent Color</span>
                            </label>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <input type="color" wire:model.live="theme_accent_color" style="width: 44px; height: 44px; padding: 2px; border: 1px solid var(--border-color); border-radius: 8px; cursor: pointer; background: transparent;">
                                <input type="text" wire:model.live="theme_accent_color" class="form-control" style="font-family: 'JetBrains Mono', monospace; font-size: 0.90rem; font-weight: 700; max-width: 140px;">
                            </div>
                            <small style="color: var(--color-text-secondary); font-size: 0.76rem; display: block; margin-top: 0.3rem;">Drives main action buttons, primary badges, focused borders, and brand glows.</small>
                        </div>

                        <!-- Secondary Highlight -->
                        <div>
                            <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                                <span>Secondary Highlight Color</span>
                            </label>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <input type="color" wire:model.live="theme_secondary_color" style="width: 44px; height: 44px; padding: 2px; border: 1px solid var(--border-color); border-radius: 8px; cursor: pointer; background: transparent;">
                                <input type="text" wire:model.live="theme_secondary_color" class="form-control" style="font-family: 'JetBrains Mono', monospace; font-size: 0.90rem; font-weight: 700; max-width: 140px;">
                            </div>
                            <small style="color: var(--color-text-secondary); font-size: 0.76rem; display: block; margin-top: 0.3rem;">Used for status pills, subtle gradients, and secondary highlights.</small>
                        </div>
                    </div>

                    <!-- Live Interactive Component Preview Card -->
                    <div class="card" style="padding: 1.25rem; background: var(--bg-surface); border: 1.5px solid var(--border-color); border-radius: var(--radius-md);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem;">
                            <span style="font-size: 0.72rem; text-transform: uppercase; font-weight: 800; color: var(--color-text-muted); letter-spacing: 0.05em;">Live Preview Component Strip</span>
                            <span class="badge badge-emerald" style="font-size: 0.68rem; font-weight: 800;">REALTIME</span>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                            <!-- Sample Button Strip -->
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                <button type="button" class="btn" style="background: {{ $theme_accent_color }}; color: #ffffff; padding: 0.45rem 1rem; font-weight: 800; font-size: 0.82rem; border-radius: var(--radius-sm); border: none; box-shadow: 0 4px 12px {{ $theme_accent_color }}55;">
                                    Primary Action
                                </button>
                                <button type="button" class="btn" style="background: transparent; color: {{ $theme_accent_color }}; border: 1.5px solid {{ $theme_accent_color }}; padding: 0.45rem 0.9rem; font-weight: 700; font-size: 0.82rem; border-radius: var(--radius-sm);">
                                    Outline Button
                                </button>
                            </div>

                            <!-- Sample Badges -->
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                <span class="badge" style="background: {{ $theme_accent_color }}22; color: {{ $theme_accent_color }}; border: 1px solid {{ $theme_accent_color }}44; font-weight: 800; font-size: 0.75rem; padding: 0.2rem 0.6rem;">
                                    Primary Badge
                                </span>
                                <span class="badge" style="background: {{ $theme_secondary_color }}22; color: {{ $theme_secondary_color }}; border: 1px solid {{ $theme_secondary_color }}44; font-weight: 800; font-size: 0.75rem; padding: 0.2rem 0.6rem;">
                                    Secondary Highlight
                                </span>
                            </div>

                            <!-- Sample Input with Brand Focus -->
                            <div>
                                <input type="text" class="form-control" readonly value="Sample Focused Input Field" style="border-color: {{ $theme_accent_color }}; box-shadow: 0 0 0 3px {{ $theme_accent_color }}33; font-size: 0.82rem; padding: 0.45rem 0.75rem;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- 3. TYPOGRAPHY, CORNER RADIUS & CARD ELEVATION                              -->
            <!-- ========================================================================= -->
            <div style="background: var(--bg-card); border: 1.5px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem;">
                <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                    <span>📐 Typography, Geometry, Shadows &amp; Density</span>
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem;">
                    
                    <!-- Corner Radius Style -->
                    <div>
                        <label style="font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); display: block; margin-bottom: 0.35rem;">
                            UI Corner Radius Style
                        </label>
                        <select wire:model.live="theme_radius_style" class="form-control form-select" style="font-size: 0.88rem; font-weight: 700;">
                            <option value="modern">Rounded Modern (14px default — Balanced SaaS)</option>
                            <option value="compact">Compact Crisp (8px — Dense Technical View)</option>
                            <option value="pill">Organic Pill (24px — Ultra Soft Curves)</option>
                            <option value="sharp">Sharp Architectural (2px — Executive Formal)</option>
                        </select>
                        <small style="color: var(--color-text-secondary); font-size: 0.75rem; display: block; margin-top: 0.25rem;">Adjusts curves on buttons, cards, modals, and input fields.</small>
                    </div>

                    <!-- Typography Font Family -->
                    <div>
                        <label style="font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); display: block; margin-bottom: 0.35rem;">
                            Platform Font Family Preset
                        </label>
                        <select wire:model.live="theme_font_family" class="form-control form-select" style="font-size: 0.88rem; font-weight: 700;">
                            <option value="jakarta">Plus Jakarta Sans &amp; Outfit (Modern Tech Academic)</option>
                            <option value="inter">Inter UI (Scandinavian Clean Enterprise)</option>
                            <option value="outfit">Outfit Geometric (Bold Futuristic Authority)</option>
                            <option value="system">Native System (Segoe UI / San Francisco / Roboto)</option>
                            <option value="academic">Playfair Display &amp; Jakarta (Classical Scholarly)</option>
                        </select>
                        <small style="color: var(--color-text-secondary); font-size: 0.75rem; display: block; margin-top: 0.25rem;">Applied across headings, body copy, tables, and data values.</small>
                    </div>

                    <!-- Card Elevation & Shadow Depth -->
                    <div>
                        <label style="font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); display: block; margin-bottom: 0.35rem;">
                            Card Elevation &amp; Shadows
                        </label>
                        <select wire:model.live="theme_card_elevation" class="form-control form-select" style="font-size: 0.88rem; font-weight: 700;">
                            <option value="bordered">Crisp Bordered (Default — Clean subtle border &amp; shadow)</option>
                            <option value="floating">Floating Deep (Layered rich shadows with ambient glow)</option>
                            <option value="minimal">Minimal Flat (Hairline borders, no deep shadows)</option>
                        </select>
                        <small style="color: var(--color-text-secondary); font-size: 0.75rem; display: block; margin-top: 0.25rem;">Governs visual depth of cards, stats, and dialogs.</small>
                    </div>

                    <!-- Sidebar Visual Theme -->
                    <div>
                        <label style="font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); display: block; margin-bottom: 0.35rem;">
                            Sidebar Visual Appearance
                        </label>
                        <select wire:model.live="theme_sidebar_style" class="form-control form-select" style="font-size: 0.88rem; font-weight: 700;">
                            <option value="glass-dark">Obsidian Dark Glass (Default High-Contrast Dark)</option>
                            <option value="light-solid">Pure Solid White (In Light Mode) / Slate</option>
                            <option value="accent-tint">Subtle Brand Color Tint</option>
                        </select>
                        <small style="color: var(--color-text-secondary); font-size: 0.75rem; display: block; margin-top: 0.25rem;">Sets navigation sidebar backdrop texture and active item contrast.</small>
                    </div>

                    <!-- Login Portal Layout -->
                    <div>
                        <label style="font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); display: block; margin-bottom: 0.35rem;">
                            Public Login Portal Layout
                        </label>
                        <select wire:model.live="theme_login_layout" class="form-control form-select" style="font-size: 0.88rem; font-weight: 700;">
                            <option value="split-modern">Split Modern (Executive Left Hero + Sign-in Right)</option>
                            <option value="center-glass">Center Frosted Glass (Centered with Ambient Mesh)</option>
                            <option value="minimalist">Minimalist Focused (Distraction-Free Card)</option>
                        </select>
                        <small style="color: var(--color-text-secondary); font-size: 0.75rem; display: block; margin-top: 0.25rem;">Alters the public candidate and officer authentication screen structure.</small>
                    </div>

                    <!-- Data Table Density -->
                    <div>
                        <label style="font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); display: block; margin-bottom: 0.35rem;">
                            Listing &amp; Table Density
                        </label>
                        <select wire:model.live="ui_table_density" class="form-control form-select" style="font-size: 0.88rem; font-weight: 700;">
                            <option value="compact">Compact (High Information Density — Data Rich)</option>
                            <option value="comfortable">Comfortable (Default Balanced Spacing)</option>
                            <option value="spacious">Spacious (Relaxed Touch-Friendly Spacing)</option>
                        </select>
                        <small style="color: var(--color-text-secondary); font-size: 0.75rem; display: block; margin-top: 0.25rem;">Adjusts table row padding across candidate lists and applications.</small>
                    </div>

                </div>

                <!-- Toggles for Glassmorphism & Animations -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem; margin-top: 1.25rem; padding-top: 1.25rem; border-top: 1px solid var(--border-color);">
                    <label style="display: flex; align-items: center; justify-content: space-between; padding: 0.85rem 1rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); cursor: pointer;">
                        <div>
                            <div style="font-weight: 800; font-size: 0.85rem; color: var(--color-text-primary);">Glassmorphic Backdrop Blur</div>
                            <div style="font-size: 0.75rem; color: var(--color-text-secondary);">Enables translucent glass effects on cards and modals</div>
                        </div>
                        <input type="checkbox" wire:model.live="ui_glassmorphism_enabled" style="width: 18px; height: 18px; accent-color: var(--primary);">
                    </label>

                    <label style="display: flex; align-items: center; justify-content: space-between; padding: 0.85rem 1rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); cursor: pointer;">
                        <div>
                            <div style="font-weight: 800; font-size: 0.85rem; color: var(--color-text-primary);">Micro-Interactions &amp; Transitions</div>
                            <div style="font-size: 0.75rem; color: var(--color-text-secondary);">Enables smooth scaling, hover effects, and spring easing</div>
                        </div>
                        <input type="checkbox" wire:model.live="ui_animations_enabled" style="width: 18px; height: 18px; accent-color: var(--primary);">
                    </label>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- 4. CORE APPLICATION IDENTIFIERS                                            -->
            <!-- ========================================================================= -->
            <div style="background: var(--bg-card); border: 1.5px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.5rem;">
                    <div>
                        <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--color-text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                            <span>🏷️ Application Brand Identity &amp; Platform Title</span>
                        </h3>
                        <p style="font-size: 0.8rem; color: var(--color-text-secondary); margin: 0.2rem 0 0 0;">
                            Configure platform software title, slogans, and software edition badges.
                        </p>
                    </div>
                    @can('manage-settings')
                    <button type="button" 
                            wire:click="resetBrandingToDefault" 
                            wire:confirm="Reset application name, taglines, description, and SVG brand assets to defaults?"
                            class="btn btn-secondary btn-sm" 
                            style="font-size: 0.76rem; padding: 0.35rem 0.75rem;">
                        Reset Brand Identity Defaults
                    </button>
                    @endcan
                </div>

                <!-- Nomenclature Harmony Notice -->
                <div style="background: rgba(99,102,241,0.06); border: 1px dashed rgba(99,102,241,0.3); border-radius: var(--radius-sm); padding: 0.85rem 1.15rem; margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
                    <div style="display: flex; align-items: center; gap: 0.65rem;">
                        <span style="font-size: 1.15rem;">🏛️</span>
                        <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.45;">
                            <strong>Institutional Short Name:</strong> The sidebar drawer and portal topbars automatically display your university acronym 
                            <span class="badge badge-indigo" style="font-size: 0.72rem; font-weight: 800;">{{ strtoupper($org_short_name ?: 'AWKUM') }}</span> 
                            (configured in <a href="javascript:void(0)" wire:click="setTab('organization')" style="color: var(--primary); font-weight: 700; text-decoration: underline;">Institution Profile</a>).
                        </div>
                    </div>
                    @if(!empty($org_short_name) && $app_name !== $org_short_name)
                        <button type="button" 
                                wire:click="$set('app_name', '{{ $org_short_name }}')"
                                class="btn btn-secondary btn-sm" 
                                style="font-size: 0.72rem; font-weight: 800; padding: 0.35rem 0.65rem;">
                            Set App Name to "{{ $org_short_name }}"
                        </button>
                    @endif
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
                    <!-- App Name -->
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                            <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin: 0;">
                                <span>Platform Brand Name</span>
                                <span style="color: var(--danger);">*</span>
                            </label>
                        </div>
                        <input type="text" 
                               wire:model="app_name" 
                               class="form-control" 
                               placeholder="e.g. {{ $org_short_name ?: 'AWKUM' }}" 
                               style="font-size: 0.95rem; font-weight: 700; padding: 0.65rem 1rem;">
                        @error('app_name') <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                        <small style="color: var(--color-text-secondary); font-size: 0.78rem; display: block; margin-top: 0.25rem;">Software suite title used in browser tabs, system alerts, and notifications.</small>
                    </div>

                    <!-- App Tagline / Slogan -->
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                            <span>Portal Tagline / Slogan</span>
                        </label>
                        <input type="text" 
                               wire:model="app_tagline" 
                               class="form-control" 
                               placeholder="e.g. Statutory Recruitment &amp; Selection Operating System" 
                               style="font-size: 0.92rem; padding: 0.65rem 1rem;">
                        @error('app_tagline') <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                        <small style="color: var(--color-text-secondary); font-size: 0.78rem; display: block; margin-top: 0.25rem;">Displayed beneath the logo on login and candidate splash screens.</small>
                    </div>

                    <!-- App Version / Edition -->
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                            <span>Version &amp; Edition Badge</span>
                        </label>
                        <input type="text" 
                               wire:model="app_version" 
                               class="form-control" 
                               placeholder="e.g. v2.4 Enterprise OS" 
                               style="font-size: 0.92rem; padding: 0.65rem 1rem;">
                        @error('app_version') <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                        <small style="color: var(--color-text-secondary); font-size: 0.78rem; display: block; margin-top: 0.25rem;">Pill badge rendered beside the brand mark in the sidebar.</small>
                    </div>
                </div>

                <!-- App Meta Description -->
                <div style="margin-top: 1.25rem;">
                    <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                        <span>Application Overview &amp; Meta Description</span>
                    </label>
                    <textarea wire:model="app_description" 
                              rows="2" 
                              class="form-control" 
                              placeholder="Describe your recruitment portal and statutory purpose..." 
                              style="font-size: 0.90rem; padding: 0.65rem 1rem;"></textarea>
                    @error('app_description') <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                    <small style="color: var(--color-text-secondary); font-size: 0.78rem; display: block; margin-top: 0.25rem;">Summary text used for browser search engine indexing and footer credits.</small>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- 5. BRAND LOGOS & FAVICON ASSETS                                           -->
            <!-- ========================================================================= -->
            <div style="background: var(--bg-card); border: 1.5px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
                    <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--color-text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <span>🖼️ Application Identity, Logos, Favicon &amp; Brand Assets</span>
                    </h3>
                    @can('manage-settings')
                    <div style="display: flex; gap: 0.5rem;">
                        <button type="button" wire:click="resetLogoToDefault" class="btn btn-secondary btn-sm" style="font-size: 0.78rem; padding: 0.35rem 0.75rem;">
                            Reset Logos to Default SVG
                        </button>
                        <button type="button" wire:click="resetFaviconToDefault" class="btn btn-secondary btn-sm" style="font-size: 0.78rem; padding: 0.35rem 0.75rem;">
                            Reset Favicon
                        </button>
                    </div>
                    @endcan
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                    
                    <!-- Light Mode Logo -->
                    <div style="border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1.25rem; background: var(--bg-surface);">
                        <div style="font-size: 0.85rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.75rem;">
                            Primary Brand Logo (Light Mode)
                        </div>

                        <!-- Preview Area -->
                        <div style="height: 80px; background: #ffffff; border: 2px dashed #cbd5e1; border-radius: 6px; display: flex; align-items: center; justify-content: center; margin-bottom: 0.85rem; padding: 0.5rem; overflow: hidden;">
                            @if ($logoUpload)
                                <img src="{{ $logoUpload->temporaryUrl() }}" alt="Preview" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                            @elseif ($app_logo)
                                <img src="{{ str_starts_with($app_logo, '/') || str_starts_with($app_logo, 'http') ? $app_logo : asset('storage/' . $app_logo) }}" alt="Logo" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                            @else
                                <span style="font-size: 0.8rem; color: #94a3b8;">No Logo Selected</span>
                            @endif
                        </div>

                        <!-- File Upload -->
                        <div style="margin-bottom: 0.65rem;">
                            <label style="font-size: 0.78rem; font-weight: 700; color: var(--color-text-secondary); display: block; margin-bottom: 0.25rem;">Upload Logo File (PNG / SVG / JPG)</label>
                            <input type="file" wire:model="logoUpload" accept="image/*" class="form-control" style="font-size: 0.82rem; padding: 0.4rem;">
                            @error('logoUpload') <div style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                        </div>

                        <!-- URL or Asset Path -->
                        <div>
                            <label style="font-size: 0.78rem; font-weight: 700; color: var(--color-text-secondary); display: block; margin-bottom: 0.25rem;">Or Enter Logo Asset URL / Path</label>
                            <input type="text" wire:model="app_logo" class="form-control" placeholder="/images/logo-icon.svg" style="font-size: 0.82rem; padding: 0.45rem 0.75rem;">
                        </div>
                    </div>

                    <!-- Dark Mode Logo -->
                    <div style="border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1.25rem; background: var(--bg-surface);">
                        <div style="font-size: 0.85rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.75rem;">
                            Secondary Brand Logo (Dark Mode)
                        </div>

                        <!-- Dark Preview Area -->
                        <div style="height: 80px; background: #0f172a; border: 2px dashed #334155; border-radius: 6px; display: flex; align-items: center; justify-content: center; margin-bottom: 0.85rem; padding: 0.5rem; overflow: hidden;">
                            @if ($logoDarkUpload)
                                <img src="{{ $logoDarkUpload->temporaryUrl() }}" alt="Dark Preview" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                            @elseif ($app_logo_dark)
                                <img src="{{ str_starts_with($app_logo_dark, '/') || str_starts_with($app_logo_dark, 'http') ? $app_logo_dark : asset('storage/' . $app_logo_dark) }}" alt="Dark Logo" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                            @else
                                <span style="font-size: 0.8rem; color: #64748b;">Using Default Logo</span>
                            @endif
                        </div>

                        <!-- File Upload -->
                        <div style="margin-bottom: 0.65rem;">
                            <label style="font-size: 0.78rem; font-weight: 700; color: var(--color-text-secondary); display: block; margin-bottom: 0.25rem;">Upload Dark Logo File</label>
                            <input type="file" wire:model="logoDarkUpload" accept="image/*" class="form-control" style="font-size: 0.82rem; padding: 0.4rem;">
                            @error('logoDarkUpload') <div style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                        </div>

                        <!-- URL or Asset Path -->
                        <div>
                            <label style="font-size: 0.78rem; font-weight: 700; color: var(--color-text-secondary); display: block; margin-bottom: 0.25rem;">Or Enter Dark Logo URL / Path</label>
                            <input type="text" wire:model="app_logo_dark" class="form-control" placeholder="/images/logo-icon.svg" style="font-size: 0.82rem; padding: 0.45rem 0.75rem;">
                        </div>
                    </div>

                    <!-- Browser Favicon -->
                    <div style="border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1.25rem; background: var(--bg-surface);">
                        <div style="font-size: 0.85rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.75rem;">
                            Browser Tab Favicon
                        </div>

                        <!-- Favicon Preview Area -->
                        <div style="height: 80px; background: var(--bg-card); border: 2px dashed #cbd5e1; border-radius: 6px; display: flex; align-items: center; justify-content: center; gap: 1rem; margin-bottom: 0.85rem; padding: 0.5rem;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 0.25rem;">
                                @if ($faviconUpload)
                                    <img src="{{ $faviconUpload->temporaryUrl() }}" alt="Favicon" width="32" height="32" style="object-fit: contain;">
                                @elseif ($app_favicon)
                                    <img src="{{ str_starts_with($app_favicon, '/') || str_starts_with($app_favicon, 'http') ? $app_favicon : asset('storage/' . $app_favicon) }}" alt="Favicon" width="32" height="32" style="object-fit: contain;">
                                @else
                                    <img src="/favicon.ico" alt="Favicon" width="32" height="32">
                                @endif
                                <span style="font-size: 0.65rem; color: var(--color-text-muted);">32x32</span>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 0.25rem;">
                                @if ($faviconUpload)
                                    <img src="{{ $faviconUpload->temporaryUrl() }}" alt="Favicon" width="16" height="16" style="object-fit: contain;">
                                @elseif ($app_favicon)
                                    <img src="{{ str_starts_with($app_favicon, '/') || str_starts_with($app_favicon, 'http') ? $app_favicon : asset('storage/' . $app_favicon) }}" alt="Favicon" width="16" height="16" style="object-fit: contain;">
                                @else
                                    <img src="/favicon.ico" alt="Favicon" width="16" height="16">
                                @endif
                                <span style="font-size: 0.65rem; color: var(--color-text-muted);">16x16</span>
                            </div>
                        </div>

                        <!-- File Upload -->
                        <div style="margin-bottom: 0.65rem;">
                            <label style="font-size: 0.78rem; font-weight: 700; color: var(--color-text-secondary); display: block; margin-bottom: 0.25rem;">Upload Favicon (.ico, .svg, .png)</label>
                            <input type="file" wire:model="faviconUpload" accept=".ico,.svg,.png,.webp" class="form-control" style="font-size: 0.82rem; padding: 0.4rem;">
                            @error('faviconUpload') <div style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                        </div>

                        <!-- URL or Asset Path -->
                        <div>
                            <label style="font-size: 0.78rem; font-weight: 700; color: var(--color-text-secondary); display: block; margin-bottom: 0.25rem;">Or Enter Favicon Path</label>
                            <input type="text" wire:model="app_favicon" class="form-control" placeholder="/favicon.ico" style="font-size: 0.82rem; padding: 0.45rem 0.75rem;">
                        </div>
                    </div>

                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- 6. PORTAL BROADCAST ANNOUNCEMENT BANNER & FOOTER NOTICE                   -->
            <!-- ========================================================================= -->
            <div style="background: var(--bg-card); border: 1.5px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
                    <div>
                        <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--color-text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                            <span>📢 Candidate Portal Broadcast &amp; Announcement Banner</span>
                        </h3>
                        <p style="font-size: 0.82rem; color: var(--color-text-secondary); margin: 0.25rem 0 0 0;">
                            Display an institutional notice, fee deadline reminder, or maintenance alert at the top of candidate dashboards.
                        </p>
                    </div>

                    <!-- Banner Toggle -->
                    <label style="display: inline-flex; align-items: center; gap: 0.6rem; cursor: pointer;">
                        <input type="checkbox" wire:model.live="portal_announcement_enabled" style="width: 18px; height: 18px; accent-color: var(--primary);">
                        <span style="font-weight: 800; font-size: 0.88rem; color: var(--color-text-primary);">
                            {{ $portal_announcement_enabled ? 'Banner Active (ON)' : 'Banner Inactive (OFF)' }}
                        </span>
                    </label>
                </div>

                @if($portal_announcement_enabled)
                    <div style="display: grid; grid-template-columns: 200px 1fr; gap: 1.25rem; margin-top: 1rem;">
                        <!-- Variant -->
                        <div>
                            <label style="font-size: 0.82rem; font-weight: 700; color: var(--color-text-primary); display: block; margin-bottom: 0.35rem;">
                                Banner Severity Style
                            </label>
                            <select wire:model.live="portal_announcement_type" class="form-control form-select" style="font-size: 0.88rem;">
                                <option value="info">🔵 Informational (Blue)</option>
                                <option value="warning">🟡 Attention / Warning (Amber)</option>
                                <option value="danger">🔴 Urgent / Critical (Red)</option>
                                <option value="success">🟢 Milestone / Update (Green)</option>
                            </select>
                        </div>

                        <!-- Message -->
                        <div>
                            <label style="font-size: 0.82rem; font-weight: 700; color: var(--color-text-primary); display: block; margin-bottom: 0.35rem;">
                                Broadcast Announcement Text
                            </label>
                            <input type="text" 
                                   wire:model="portal_announcement_message" 
                                   class="form-control" 
                                   placeholder="e.g. Challan payment verification deadline for Ad No. 58/2026 is extended till 15 November 2026." 
                                   style="font-size: 0.88rem; padding: 0.65rem 1rem;">
                        </div>
                    </div>
                @endif

                <!-- Multi-Portal Footers & Letter Endorsement Customizer -->
                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                        <span class="badge badge-indigo" style="font-size: 0.7rem; font-weight: 800;">MULTI-PORTAL FOOTERS</span>
                        <span class="badge badge-cyan" style="font-size: 0.7rem; font-weight: 800;">PRINT ENDORSEMENTS</span>
                    </div>
                    <h4 style="margin: 0; font-size: 1.05rem; font-weight: 800; color: var(--color-text-primary);">
                        Portal-Specific Footers & Printed Document Disclaimers
                    </h4>
                    <p style="margin: 0.2rem 0 1.25rem 0; font-size: 0.82rem; color: var(--color-text-secondary);">
                        Configure distinct footer notices for each portal workspace, plus the legal endorsement disclaimer printed on candidate dossiers and letters.
                    </p>

                    <!-- Universal Copyright -->
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                            <span>Universal Platform Copyright Notice</span>
                        </label>
                        <input type="text" 
                               wire:model="footer_copyright_text" 
                               class="form-control" 
                               placeholder="e.g. &copy; 2026 Abdul Wali Khan University Mardan (AWKUM). All rights reserved." 
                               style="font-size: 0.90rem; padding: 0.65rem 1rem;">
                        <small style="color: var(--color-text-secondary); font-size: 0.76rem; display: block; margin-top: 0.25rem;">Displayed at the bottom of public candidate, login, and application workspaces.</small>
                    </div>

                    <!-- 2x2 Grid for Portal Footers -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
                        <!-- Candidate Portal Footer -->
                        <div>
                            <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.85rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                                <span>Candidate Portal Footer Notice</span>
                                <x-help-hint type="inline" variant="info" title="Candidate Footer" text="Helpdesk and fee verification guidelines rendered on candidate dashboards." />
                            </label>
                            <textarea wire:model="footer_candidate_text" 
                                      rows="2" 
                                      class="form-control" 
                                      style="font-size: 0.85rem; padding: 0.6rem 0.85rem;" 
                                      placeholder="e.g. Candidate Facilitation Desk: For application guidance or voucher verification, contact..."></textarea>
                        </div>

                        <!-- Admin & Registrar Portal Footer -->
                        <div>
                            <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.85rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                                <span>Administrative Portal Footer Notice</span>
                                <x-help-hint type="inline" variant="info" title="Admin Footer" text="Governance, audit, and official compliance notice displayed in administrative workspaces." />
                            </label>
                            <textarea wire:model="footer_admin_text" 
                                      rows="2" 
                                      class="form-control" 
                                      style="font-size: 0.85rem; padding: 0.6rem 0.85rem;" 
                                      placeholder="e.g. Official Administrative Desk • Abdul Wali Khan University Mardan • System Access Audited & Recorded."></textarea>
                        </div>

                        <!-- Selection Board & Scrutiny Footer -->
                        <div>
                            <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.85rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                                <span>Selection Board &amp; Scrutiny Desk Footer</span>
                                <x-help-hint type="inline" variant="info" title="Board Footer" text="Statutory confidentiality notice displayed on selection committee desks." />
                            </label>
                            <textarea wire:model="footer_board_text" 
                                      rows="2" 
                                      class="form-control" 
                                      style="font-size: 0.85rem; padding: 0.6rem 0.85rem;" 
                                      placeholder="e.g. Statutory Selection Board Secretariat • Strictly Confidential Under University Act."></textarea>
                        </div>

                        <!-- Peer Review Portal Footer -->
                        <div>
                            <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.85rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                                <span>Academic Peer Review Footer Notice</span>
                                <x-help-hint type="inline" variant="info" title="Reviewer Footer" text="Confidentiality and evaluation protocol note displayed to external academic referees." />
                            </label>
                            <textarea wire:model="footer_reviewer_text" 
                                      rows="2" 
                                      class="form-control" 
                                      style="font-size: 0.85rem; padding: 0.6rem 0.85rem;" 
                                      placeholder="e.g. Academic Peer Evaluation Desk • External Referee Integrity • Blind Review Protocols Active."></textarea>
                        </div>
                    </div>

                    <!-- Printed Document & Letter Endorsement Section -->
                    <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1.25rem;">
                        <div style="font-size: 0.82rem; font-weight: 800; text-transform: uppercase; color: var(--secondary); margin-bottom: 0.85rem; display: flex; align-items: center; gap: 0.4rem;">
                            <span>🖨️ Printed Letter &amp; Candidate Dossier Footer Endorsements</span>
                        </div>

                        <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 1.25rem; margin-bottom: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.3rem;">
                                    Printed Document Legal Endorsement &amp; Disclaimer
                                </label>
                                <textarea wire:model="letter_footer_text" 
                                          rows="3" 
                                          class="form-control" 
                                          style="font-size: 0.84rem; padding: 0.6rem 0.85rem;" 
                                          placeholder="e.g. This is an official computer-generated document issued under the seal and authority of Abdul Wali Khan University Mardan..."></textarea>
                            </div>

                            <div>
                                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.3rem;">
                                    Printed Document Official Footer Contact
                                </label>
                                <textarea wire:model="letter_footer_contact" 
                                          rows="3" 
                                          class="form-control" 
                                          style="font-size: 0.84rem; padding: 0.6rem 0.85rem;" 
                                          placeholder="e.g. Office of the Registrar, AWKUM • Tel: +92-937-9230611 • Web: www.awkum.edu.pk"></textarea>
                            </div>
                        </div>

                        <div style="display: flex; gap: 2rem; flex-wrap: wrap; border-top: 1px dashed var(--border-color); padding-top: 0.85rem;">
                            <label style="display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.82rem; font-weight: 700; color: var(--color-text-primary);">
                                <input type="checkbox" wire:model="letter_show_audit_trail" style="accent-color: var(--primary); width: 16px; height: 16px;">
                                <span>Print User Audit Trail ("Printed by Full Name (Username) - Role &amp; Date")</span>
                            </label>

                            <label style="display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.82rem; font-weight: 700; color: var(--color-text-primary);">
                                <input type="checkbox" wire:model="letter_show_verify_ref" style="accent-color: var(--primary); width: 16px; height: 16px;">
                                <span>Print Cryptographic Verification Reference (SHA-256 Hash)</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Save Bar at bottom -->
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                @can('manage-settings')
                <button type="submit" class="btn btn-primary" style="padding: 0.65rem 1.6rem; font-weight: 800; font-size: 0.92rem; box-shadow: 0 4px 14px var(--primary-glow);">
                    <span wire:loading.remove wire:target="saveBrandingSettings">Save Theme &amp; Branding</span>
                    <span wire:loading wire:target="saveBrandingSettings">Saving...</span>
                </button>
                @endcan
            </div>

        </div>
    </form>
</div>
