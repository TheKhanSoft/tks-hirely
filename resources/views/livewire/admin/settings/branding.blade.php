<div class="card mb-6" style="padding: 1.75rem; border-color: var(--primary); box-shadow: var(--shadow-md);">
    <!-- Card Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                <span class="badge badge-indigo" style="font-size: 0.72rem; font-weight: 800;">PLATFORM BRANDING</span>
                <span class="badge badge-slate" style="font-size: 0.72rem;">LOGOS &amp; ASSETS</span>
            </div>
            <h2 class="card-title" style="margin: 0; font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary);">
                Application Identity, Logos, Favicon &amp; Portal Announcements
            </h2>
            <p class="card-subtitle" style="margin: 0.25rem 0 0 0; font-size: 0.85rem; color: var(--color-text-secondary);">
                Configure custom application naming, brand logos, browser favicons, visual theme accent colors, and candidate broadcast banners.
            </p>
        </div>

        @can('manage-settings')
        <button type="button" 
                wire:click="saveBrandingSettings" 
                class="btn btn-primary" 
                style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.4rem; font-weight: 800; font-size: 0.88rem; box-shadow: 0 4px 14px var(--primary-glow);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                <polyline points="17 21 17 13 7 13 7 21"/>
                <polyline points="7 3 7 8 15 8"/>
            </svg>
            <span wire:loading.remove wire:target="saveBrandingSettings">Save Branding Settings</span>
            <span wire:loading wire:target="saveBrandingSettings">Saving...</span>
        </button>
        @endcan
    </div>

    @if(session()->has('message'))
        <div class="alert alert-success" style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    {{-- System Help Callout --}}
    <x-help-hint type="callout" variant="info" title="Application Identity &amp; Multi-Brand Adaptation">
        Changes to application name, logos, and favicon take effect across the candidate experience, administrative sidebars, browser page titles, and outgoing transaction emails.
    </x-help-hint>

    <form wire:submit.prevent="saveBrandingSettings" style="margin-top: 1.5rem;">
        <div style="display: flex; flex-direction: column; gap: 2rem;">

            <!-- ========================================================================= -->
            <!-- 1. CORE APPLICATION IDENTIFIERS                                            -->
            <!-- ========================================================================= -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 1.5rem;">
                <h3 style="font-size: 1rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                    <span>🏷️ Application Brand Identity</span>
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
                    <!-- App Name -->
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                            <span>Application Name</span>
                            <span style="color: var(--danger);">*</span>
                        </label>
                        <input type="text" 
                               wire:model="app_name" 
                               class="form-control" 
                               placeholder="e.g. Hirely" 
                               style="font-size: 0.95rem; font-weight: 700; padding: 0.65rem 1rem;">
                        @error('app_name') <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                        <small style="color: var(--color-text-secondary); font-size: 0.78rem; display: block; margin-top: 0.25rem;">Primary brand name displayed in headers, navigation, and page titles.</small>
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
            <!-- 2. BRAND LOGOS & FAVICON ASSETS                                           -->
            <!-- ========================================================================= -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
                    <h3 style="font-size: 1rem; font-weight: 800; color: var(--color-text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <span>🖼️ Brand Logo &amp; Favicon Assets</span>
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
                    <div style="border: 1px solid var(--border-color); border-radius: 6px; padding: 1.25rem; background: var(--bg-surface);">
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
                    <div style="border: 1px solid var(--border-color); border-radius: 6px; padding: 1.25rem; background: var(--bg-surface);">
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
                    <div style="border: 1px solid var(--border-color); border-radius: 6px; padding: 1.25rem; background: var(--bg-surface);">
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
            <!-- 3. THEME ACCENT & FOOTER NOTICE                                           -->
            <!-- ========================================================================= -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 1.5rem;">
                <h3 style="font-size: 1rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                    <span>🎨 Visual Theme Accent &amp; Footer Copyright</span>
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                    <!-- Theme Accent Color -->
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                            <span>Brand Primary Accent Color</span>
                        </label>
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.65rem;">
                            <input type="color" wire:model.live="theme_accent_color" style="width: 44px; height: 44px; padding: 2px; border: 1px solid var(--border-color); border-radius: 6px; cursor: pointer; background: transparent;">
                            <input type="text" wire:model.live="theme_accent_color" class="form-control" style="font-family: 'JetBrains Mono', monospace; font-size: 0.90rem; font-weight: 700; max-width: 140px;">
                        </div>
                        <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                            <button type="button" wire:click="$set('theme_accent_color', '#4f46e5')" class="badge" style="background: #4f46e5; color: #fff; cursor: pointer; border: none; padding: 0.3rem 0.6rem;">Indigo</button>
                            <button type="button" wire:click="$set('theme_accent_color', '#2563eb')" class="badge" style="background: #2563eb; color: #fff; cursor: pointer; border: none; padding: 0.3rem 0.6rem;">Blue</button>
                            <button type="button" wire:click="$set('theme_accent_color', '#059669')" class="badge" style="background: #059669; color: #fff; cursor: pointer; border: none; padding: 0.3rem 0.6rem;">Emerald</button>
                            <button type="button" wire:click="$set('theme_accent_color', '#7c3aed')" class="badge" style="background: #7c3aed; color: #fff; cursor: pointer; border: none; padding: 0.3rem 0.6rem;">Purple</button>
                            <button type="button" wire:click="$set('theme_accent_color', '#e11d48')" class="badge" style="background: #e11d48; color: #fff; cursor: pointer; border: none; padding: 0.3rem 0.6rem;">Rose</button>
                        </div>
                    </div>

                    <!-- Footer Copyright Text -->
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                            <span>Portal Footer Copyright Text</span>
                        </label>
                        <input type="text" 
                               wire:model="footer_copyright_text" 
                               class="form-control" 
                               placeholder="e.g. &copy; 2026 Hirely OS. All rights reserved." 
                               style="font-size: 0.90rem; padding: 0.65rem 1rem;">
                        <small style="color: var(--color-text-secondary); font-size: 0.78rem; display: block; margin-top: 0.25rem;">Displayed at the bottom of public candidate and login pages.</small>
                    </div>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- 4. PORTAL BROADCAST ANNOUNCEMENT BANNER                                   -->
            <!-- ========================================================================= -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
                    <div>
                        <h3 style="font-size: 1rem; font-weight: 800; color: var(--color-text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
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
                                <option value="warning">🟡 Warning / Deadline (Amber)</option>
                                <option value="danger">🔴 Urgent Alert (Red)</option>
                                <option value="success">🟢 Good News / Clearance (Green)</option>
                            </select>
                        </div>

                        <!-- Message -->
                        <div>
                            <label style="font-size: 0.82rem; font-weight: 700; color: var(--color-text-primary); display: block; margin-bottom: 0.35rem;">
                                Announcement Message Text
                            </label>
                            <textarea wire:model.live="portal_announcement_message" 
                                      rows="2" 
                                      class="form-control" 
                                      placeholder="e.g. Mandatory Notice: Candidates must upload original HEC equivalence certificates before the statutory deadline." 
                                      style="font-size: 0.90rem; padding: 0.6rem 0.85rem;"></textarea>
                        </div>
                    </div>

                    <!-- Live Preview -->
                    @if(!empty($portal_announcement_message))
                        <div style="margin-top: 1.25rem; padding: 0.85rem 1.25rem; border-radius: 6px; font-size: 0.88rem; font-weight: 600; display: flex; align-items: center; gap: 0.75rem; 
                            @if($portal_announcement_type === 'warning') background: #fffbeb; color: #92400e; border: 1.5px solid #fde68a;
                            @elseif($portal_announcement_type === 'danger') background: #fef2f2; color: #991b1b; border: 1.5px solid #fecaca;
                            @elseif($portal_announcement_type === 'success') background: #ecfdf5; color: #065f46; border: 1.5px solid #a7f3d0;
                            @else background: #eff6ff; color: #1e40af; border: 1.5px solid #bfdbfe; @endif">
                            <span style="font-size: 1.2rem;">
                                @if($portal_announcement_type === 'warning') ⚠️
                                @elseif($portal_announcement_type === 'danger') 🚨
                                @elseif($portal_announcement_type === 'success') ✅
                                @else 📢 @endif
                            </span>
                            <div style="flex: 1;">
                                <strong style="text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; display: block; opacity: 0.85;">Live Candidate View Preview</strong>
                                {{ $portal_announcement_message }}
                            </div>
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </form>
</div>
