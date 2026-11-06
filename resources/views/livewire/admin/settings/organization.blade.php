<div class="card mb-6" style="padding: 1.75rem; border-color: var(--primary); box-shadow: var(--shadow-md);">
    <!-- Card Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                <span class="badge badge-indigo" style="font-size: 0.72rem; font-weight: 800;">INSTITUTIONAL IDENTITY</span>
                <span class="badge badge-slate" style="font-size: 0.72rem;">FULL & SHORT NOMENCLATURE</span>
                <span class="badge badge-cyan" style="font-size: 0.72rem; font-weight: 700;">LETTERHEAD STUDIO</span>
            </div>
            <h2 class="card-title" style="margin: 0; font-size: 1.28rem; font-weight: 800; color: var(--color-text-primary);">
                Organization Profile, Institutional Sector, Short Names &amp; Official Letterhead
            </h2>
            <p class="card-subtitle" style="margin: 0.25rem 0 0 0; font-size: 0.85rem; color: var(--color-text-secondary);">
                Configure full legal names for documents, compact short abbreviations for sidebars/menus, and custom official letterheads for printed candidate dossiers.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            @can('manage-settings')
            <button type="button" 
                    wire:click="resetOrganizationToDefault" 
                    wire:confirm="Reset organization profile to standard defaults (Abdul Wali Khan University Mardan, AWKUM, PKR)?"
                    class="btn btn-secondary btn-sm" 
                    style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.55rem 0.95rem; font-weight: 700; font-size: 0.82rem;"
                    title="Restore organization profile to standard institutional defaults">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>
                <span>Reset Organization Defaults</span>
            </button>

            <button type="button" 
                    wire:click="saveOrganizationProfile" 
                    class="btn btn-primary" 
                    style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.4rem; font-weight: 800; font-size: 0.88rem; box-shadow: 0 4px 14px var(--primary-glow);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                <span wire:loading.remove wire:target="saveOrganizationProfile">Save Organization Profile</span>
                <span wire:loading wire:target="saveOrganizationProfile">Saving...</span>
            </button>
            @endcan
        </div>
    </div>

    {{-- System Help Callout --}}
    <x-help-hint type="callout" variant="info" title="Dual Nomenclature System: Full vs. Short Name">
        <strong>Full Name:</strong> Displayed where ample horizontal space is available, such as login banners, document titles, candidate portal headers, and official merit lists (e.g. <em>Abdul Wali Khan University Mardan</em>).<br>
        <strong>Short Name:</strong> Displayed in width-constrained areas like the navigation sidebar drawer, mobile header bars, and compact topbar breadcrumb pills (e.g. <em>AWKUM</em>).
    </x-help-hint>

    {{-- LIVE INSTITUTIONAL IDENTITY PREVIEW CARD --}}
    <div style="background: var(--bg-surface); border: 1.5px dashed var(--border-color); border-radius: var(--radius-md); padding: 1.25rem 1.5rem; margin-top: 1.25rem; margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem; flex-wrap: wrap; gap: 0.5rem;">
            <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--secondary); letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.4rem;">
                <span>🏛️ Live Candidate Dossier & Dual-Name Preview</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span class="badge badge-indigo" style="font-size: 0.7rem; font-weight: 800;">
                    Sidebar: {{ strtoupper($org_short_name ?: 'AWKUM') }}
                </span>
                <span class="badge badge-slate" style="font-size: 0.7rem; font-weight: 800;">
                    {{ $currency_symbol ?: 'PKR' }} • {{ $sector_label ?: 'Department' }} Model
                </span>
            </div>
        </div>

        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.65rem;">
                    <div style="font-size: 1.2rem; font-weight: 900; color: var(--color-text-primary);">
                        {{ $org_name ?: 'Abdul Wali Khan University Mardan' }}
                    </div>
                    <span class="badge badge-cyan" style="font-size: 0.75rem; font-weight: 800;">
                        {{ $org_short_name ?: 'AWKUM' }}
                    </span>
                </div>
                <div style="font-size: 0.82rem; color: var(--color-text-secondary); margin-top: 0.35rem; display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                    <span>📍 {{ $org_location ?: 'Khyber Pakhtunkhwa, Pakistan' }}</span>
                    <span>•</span>
                    <span>🏢 {{ $org_office_title ?: 'Office of the Registrar' }}</span>
                    <span>•</span>
                    <span style="color: var(--secondary); font-weight: 700;">Taxonomy: {{ $sector_label ?: 'Department / Faculty' }}</span>
                </div>
            </div>

            <div style="text-align: right; background: var(--bg-surface); padding: 0.65rem 1rem; border-radius: 6px; border: 1px solid var(--border-color);">
                <div style="font-size: 0.7rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">Baseline Intake Fee</div>
                <div style="font-size: 1.2rem; font-weight: 900; color: var(--success); margin-top: 0.15rem;">
                    {{ $currency_symbol ?: 'PKR' }} {{ number_format((float)($default_application_fee ?? 3000)) }}
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 1: ORGANIZATION CORE IDENTITY & NOMENCLATURE                      -->
    <!-- ========================================================================= -->
    <form wire:submit.prevent="saveOrganizationProfile">
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            <!-- Full Name & Short Name Grid -->
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.25rem;">
                <!-- 1. Full Legal Name -->
                <div>
                    <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                        <span>Organization / University Full Legal Name</span>
                        <span style="color: var(--danger);">*</span>
                        <x-help-hint type="inline" 
                                     variant="info" 
                                     title="Full Legal Name" 
                                     text="Used in wide areas: login banners, document titles, official evaluation dossiers, and candidate portal headers." 
                                     example="e.g. Abdul Wali Khan University Mardan" />
                    </label>
                    <input type="text" 
                           wire:model.live="org_name" 
                           class="form-control" 
                           placeholder="e.g. Abdul Wali Khan University Mardan" 
                           style="font-size: 0.95rem; font-weight: 600; padding: 0.65rem 1rem;">
                    <small style="color: var(--color-text-secondary); font-size: 0.76rem; display: block; margin-top: 0.25rem;">Displayed where ample space is available.</small>
                    @error('org_name')
                        <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 2. Short Name / Acronym -->
                <div>
                    <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                        <span>Short Name / Acronym</span>
                        <span style="color: var(--danger);">*</span>
                        <x-help-hint type="inline" 
                                     variant="tip" 
                                     title="Short Name / Acronym" 
                                     text="Used in narrow areas: sidebar navigation drawer, compact menu bars, mobile header bars, and topbar badges." 
                                     example="e.g. AWKUM" />
                    </label>
                    <input type="text" 
                           wire:model.live="org_short_name" 
                           class="form-control" 
                           placeholder="e.g. AWKUM" 
                           style="font-size: 0.95rem; font-weight: 800; letter-spacing: 0.05em; padding: 0.65rem 1rem;">
                    <small style="color: var(--primary); font-weight: 700; font-size: 0.76rem; display: block; margin-top: 0.25rem;">Directly powers the sidebar brand mark, topbar workspace pills &amp; compact spaces.</small>
                    @error('org_short_name')
                        <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Two-Column Grid: Location & Office Title -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.25rem;">
                <!-- 3. Location / Jurisdiction -->
                <div>
                    <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                        <span>Institutional Jurisdiction / Location</span>
                        <x-help-hint type="inline" 
                                     variant="info" 
                                     title="Location / Province" 
                                     text="Displayed below the main organization title on official letters, certificates, and print headers." 
                                     example="e.g. Khyber Pakhtunkhwa, Pakistan" />
                    </label>
                    <input type="text" 
                           wire:model.live="org_location" 
                           class="form-control" 
                           placeholder="e.g. Khyber Pakhtunkhwa, Pakistan" 
                           style="font-size: 0.92rem; padding: 0.65rem 1rem;">
                    @error('org_location')
                        <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 4. Directorate / Office Title -->
                <div>
                    <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                        <span>Executing Directorate / Office Name</span>
                        <x-help-hint type="inline" 
                                     variant="info" 
                                     title="Executing Office" 
                                     text="The administrative wing issuing recruitment drives and verifying candidate dossiers." 
                                     example="e.g. Office of the Registrar" />
                    </label>
                    <input type="text" 
                           wire:model.live="org_office_title" 
                           class="form-control" 
                           placeholder="e.g. Office of the Registrar" 
                           style="font-size: 0.92rem; padding: 0.65rem 1rem;">
                    @error('org_office_title')
                        <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Two-Column Grid: Org Type & Sector Label -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.25rem;">
                <!-- 5. Organizational Sector Structure -->
                <div>
                    <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                        <span>Organizational Sector / Governance Model</span>
                        <span style="color: var(--danger);">*</span>
                        <x-help-hint type="inline" 
                                     variant="statutory" 
                                     title="Governance Structure" 
                                     text="Adjusts default statutory pipeline requirements and review committee nomenclatures across the system." />
                    </label>
                    <select wire:model.live="org_type" class="form-control form-select" style="font-size: 0.92rem; padding: 0.65rem 1rem;">
                        <option value="HIGHER_EDUCATION">🏛️ Public Sector University / Higher Education (HEC Rules)</option>
                        <option value="AUTONOMOUS_INSTITUTE">🔬 Autonomous Degree Awarding Institute / Research Centre</option>
                        <option value="PUBLIC_SECTOR">🏛️ Statutory Civil Service / Public Service Commission</option>
                        <option value="CORPORATE_ENTERPRISE">🏢 Corporate Enterprise / Semi-Government Authority</option>
                    </select>
                    @error('org_type')
                        <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 6. Sector / Department Taxonomy Label -->
                <div>
                    <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                        <span>Academic / Departmental Taxonomy Label</span>
                        <span style="color: var(--danger);">*</span>
                        <x-help-hint type="inline" 
                                     variant="tip" 
                                     title="Nomenclature Taxonomy" 
                                     text="Defines the organizational unit terminology used on job campaigns and filter dropdowns (e.g. Department, Faculty, Division, Directorate)." 
                                     example="Standard: Department / Faculty" />
                    </label>
                    <input type="text" 
                           wire:model.live="sector_label" 
                           class="form-control" 
                           placeholder="e.g. Department / Faculty" 
                           style="font-size: 0.92rem; padding: 0.65rem 1rem;">
                    @error('sector_label')
                        <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Two-Column Grid: Currency Symbol & Default Fee -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.25rem;">
                <!-- 7. Currency Symbol -->
                <div>
                    <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                        <span>System Currency Symbol & Code</span>
                        <span style="color: var(--danger);">*</span>
                        <x-help-hint type="inline" 
                                     variant="info" 
                                     title="Payment Currency" 
                                     text="Standard monetary symbol prefixed to fee vouchers, Kuickpay checkout bills, and accounting ledgers." 
                                     example="PKR, USD, AED, SAR, GBP" />
                    </label>
                    <input type="text" 
                           wire:model.live="currency_symbol" 
                           class="form-control" 
                           placeholder="e.g. PKR" 
                           style="font-size: 0.92rem; font-weight: 700; padding: 0.65rem 1rem;">
                    @error('currency_symbol')
                        <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 8. Default Baseline Application Fee -->
                <div>
                    <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                        <span>Default Baseline Application Intake Fee</span>
                        <span style="color: var(--danger);">*</span>
                        <x-help-hint type="inline" 
                                     variant="info" 
                                     title="Intake Fee" 
                                     text="Pre-populates new job campaigns when launched. Individual campaigns may override this amount." />
                    </label>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="number" 
                               wire:model.live="default_application_fee" 
                               min="0" 
                               step="100" 
                               class="form-control" 
                               style="font-size: 1rem; font-weight: 800; padding: 0.65rem 1rem;">
                        <span style="font-weight: 700; color: var(--color-text-secondary); font-size: 0.9rem;">
                            {{ $currency_symbol ?: 'PKR' }}
                        </span>
                    </div>
                    @error('default_application_fee')
                        <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Save Bar for Organization -->
            <div style="display: flex; justify-content: flex-end; padding-top: 0.75rem; border-top: 1px solid var(--border-color);">
                @can('manage-settings')
                <button type="submit" 
                        class="btn btn-primary" 
                        style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.6rem; font-weight: 800; font-size: 0.92rem; box-shadow: 0 4px 14px var(--primary-glow);">
                    <span wire:loading.remove wire:target="saveOrganizationProfile">Save Organization Profile</span>
                    <span wire:loading wire:target="saveOrganizationProfile">Saving Profile...</span>
                </button>
                @endcan
            </div>

        </div>
    </form>

    <!-- ========================================================================= -->
    <!-- SECTION 2: OFFICIAL PRINTABLE LETTERHEAD & DOCUMENT HEADER STUDIO         -->
    <!-- ========================================================================= -->
    <div style="margin-top: 3rem; padding-top: 2rem; border-top: 2px solid var(--border-color);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                    <span class="badge badge-indigo" style="font-size: 0.72rem; font-weight: 800;">OFFICIAL LETTERHEAD STUDIO</span>
                    <span class="badge badge-purple" style="font-size: 0.72rem;">PRINT & DOSSIER ATTACHMENTS</span>
                </div>
                <h3 style="margin: 0; font-size: 1.22rem; font-weight: 800; color: var(--color-text-primary);">
                    Printable Document Letterhead (Heading, Sub-Heading, Logo & Details)
                </h3>
                <p style="margin: 0.25rem 0 0 0; font-size: 0.85rem; color: var(--color-text-secondary);">
                    Controls the official letterhead printed on candidate application dossiers, merit list gazettes, and appointment letters. The exact styling, font hierarchy, and size are maintained.
                </p>
            </div>

            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                @can('manage-settings')
                <button type="button" 
                        wire:click="resetLetterheadToDefault" 
                        wire:confirm="Reset official letterhead to institutional standards (Abdul Wali Khan University Mardan, Khyber Pakhtunkhwa, Office of the Registrar)?"
                        class="btn btn-secondary btn-sm" 
                        style="display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 700; padding: 0.55rem 0.95rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>
                    <span>Reset Letterhead</span>
                </button>

                <button type="button" 
                        wire:click="saveLetterheadSettings" 
                        class="btn btn-primary" 
                        style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.4rem; font-weight: 800; font-size: 0.88rem; box-shadow: 0 4px 14px var(--primary-glow);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    <span wire:loading.remove wire:target="saveLetterheadSettings">Save Letterhead Settings</span>
                    <span wire:loading wire:target="saveLetterheadSettings">Saving Letterhead...</span>
                </button>
                @endcan
            </div>
        </div>

        {{-- LIVE OFFICIAL LETTERHEAD INTERACTIVE PREVIEW --}}
        <div style="background: #ffffff; color: #0f172a; border: 2px solid #0f172a; border-radius: var(--radius-sm); padding: 1.5rem 1.75rem; margin-bottom: 2rem; box-shadow: 0 4px 20px rgba(0,0,0,0.06); font-family: 'Plus Jakarta Sans', sans-serif;">
            <div style="font-size: 0.72rem; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; margin-bottom: 1rem; border-bottom: 1px dashed #cbd5e1; padding-bottom: 0.4rem; display: flex; justify-content: space-between; align-items: center;">
                <span>📜 Exact Printable Dossier Header Preview</span>
                <span style="color: #2563eb;">Maintains Official Document Standard</span>
            </div>

            <!-- Header Preview Replica -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2.5px solid #0f172a; padding-bottom: 1rem;">
                <div style="display: flex; align-items: center; gap: 1.25rem;">
                    <!-- Letterhead Emblem / Logo Box -->
                    <div style="width: 72px; height: 72px; border: 2px solid #0f172a; border-radius: 6px; display: flex; align-items: center; justify-content: center; background: #f8fafc; flex-shrink: 0; overflow: hidden; padding: 4px;">
                        @if($letterheadLogoUpload)
                            <img src="{{ $letterheadLogoUpload->temporaryUrl() }}" alt="Letterhead Logo Preview" style="max-height: 52px; max-width: 52px; object-fit: contain;">
                        @elseif(!empty($letterhead_logo))
                            <img src="{{ $letterhead_logo }}" alt="Letterhead Logo" style="max-height: 52px; max-width: 52px; object-fit: contain;" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                            <span style="display: none; font-size: 1.4rem; font-weight: 900;">{{ substr($letterhead_heading ?: $org_name, 0, 1) }}</span>
                        @else
                            <span style="font-size: 1.4rem; font-weight: 900;">{{ substr($letterhead_heading ?: $org_name, 0, 1) }}</span>
                        @endif
                    </div>

                    <!-- Titles Box -->
                    <div>
                        <h1 style="font-size: 1.35rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em; color: #0f172a; margin: 0; line-height: 1.2;">
                            {{ $letterhead_heading ?: ($org_name ?: 'Abdul Wali Khan University Mardan') }}
                        </h1>
                        @if(!empty($letterhead_subheading ?: $org_location))
                            <div style="font-size: 0.92rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 0.15rem;">
                                {{ $letterhead_subheading ?: $org_location }}
                            </div>
                        @endif
                        @if(!empty($letterhead_office ?: $org_office_title))
                            @php
                                $officeFontSize = match($letterhead_office_font_size ?? 'standard') {
                                    'large' => '1.40rem',
                                    'compact' => '1.10rem',
                                    default => '1.25rem'
                                };
                            @endphp
                            <div style="font-size: {{ $officeFontSize }}; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 0.04em; margin-top: 0.25rem; margin-bottom: 0.2rem; line-height: 1.25;">
                                {{ $letterhead_office ?: $org_office_title }}
                            </div>
                        @endif
                        @if($letterhead_show_document_title)
                            @php
                                $rawPreviewTitle = $letterhead_dossier_title_admin ?: 'Official Master Application Dossier & Scrutiny Record';
                                $cleanPreviewTitle = trim(str_ireplace('Administrative ', '', $rawPreviewTitle));
                            @endphp
                            @if(!empty($cleanPreviewTitle))
                                <h2 style="font-size: 1.05rem; font-weight: 700; color: #334155; margin-top: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                    {{ $cleanPreviewTitle }}
                                </h2>
                            @endif
                        @endif
                        @if(!empty($letterhead_custom_details))
                            <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.2rem; line-height: 1.35;">
                                {!! nl2br(e($letterhead_custom_details)) !!}
                            </div>
                        @elseif($letterhead_show_statutory_note)
                            <div style="font-size: 0.85rem; color: #64748b; font-style: italic; margin-top: 0.2rem;">
                                Prescribed under Statutory University Employment Code & Service Statutes
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Candidate Photo Placeholder Box -->
                @if($dossier_show_photo)
                <div style="width: 110px; height: 130px; border: 2px solid #0f172a; background: #f1f5f9; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 4px; flex-shrink: 0;">
                    <div style="font-size: 1.5rem; opacity: 0.4;">👤</div>
                    <div style="font-size: 7.5pt; color: #64748b; margin-top: 0.25rem; font-weight: 600;">Candidate Photo Attached Here</div>
                </div>
                @endif
            </div>
        </div>

        <form wire:submit.prevent="saveLetterheadSettings">
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                
                <!-- Row 1: Heading & Subheading -->
                <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 1.25rem;">
                    <!-- Letterhead Heading -->
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                            <span>Letterhead Heading / Institution Name</span>
                            <span style="color: var(--danger);">*</span>
                            <x-help-hint type="inline" 
                                         variant="info" 
                                         title="Primary Letterhead Heading" 
                                         text="Prominently rendered in uppercase at the top of candidate dossiers, merit gazettes, and appointment orders." 
                                         example="e.g. Abdul Wali Khan University Mardan" />
                        </label>
                        <input type="text" 
                               wire:model.live="letterhead_heading" 
                               class="form-control" 
                               placeholder="e.g. Abdul Wali Khan University Mardan" 
                               style="font-size: 0.95rem; font-weight: 700; padding: 0.65rem 1rem;">
                        @error('letterhead_heading')
                            <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Letterhead Subheading -->
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                            <span>Letterhead Sub-Heading / Region</span>
                            <x-help-hint type="inline" 
                                         variant="info" 
                                         title="Sub-Heading" 
                                         text="Jurisdiction or territorial state placed right beneath the main institution header." 
                                         example="e.g. Khyber Pakhtunkhwa, Pakistan" />
                        </label>
                        <input type="text" 
                               wire:model.live="letterhead_subheading" 
                               class="form-control" 
                               placeholder="e.g. Khyber Pakhtunkhwa, Pakistan" 
                               style="font-size: 0.95rem; padding: 0.65rem 1rem;">
                        @error('letterhead_subheading')
                            <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Row 2: Office Title & Dedicated Logo Upload -->
                <div style="display: grid; grid-template-columns: 1.2fr 0.8fr 1fr; gap: 1.25rem;">
                    <!-- Office Title -->
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                            <span>Executing Office / Directorate on Letterhead</span>
                            <x-help-hint type="inline" 
                                         variant="info" 
                                         title="Executing Directorate" 
                                         text="The administrative secretariat issuing the document (e.g. Office of the Registrar)." 
                                         example="e.g. Office of the Registrar" />
                        </label>
                        <input type="text" 
                               wire:model.live="letterhead_office" 
                               class="form-control" 
                               placeholder="e.g. Office of the Registrar" 
                               style="font-size: 0.92rem; font-weight: 600; padding: 0.65rem 1rem;">
                        @error('letterhead_office')
                            <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Office Title Font Size -->
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                            <span>Office Text Size</span>
                            <x-help-hint type="inline" 
                                         variant="tip" 
                                         title="Office Heading Size" 
                                         text="Select the font size of the Office line on printed letterheads." />
                        </label>
                        <select wire:model.live="letterhead_office_font_size" class="form-control" style="font-size: 0.88rem; font-weight: 700; padding: 0.65rem 0.85rem;">
                            <option value="standard">Standard Prominent (1.25rem / 14pt)</option>
                            <option value="large">Extra Large (1.40rem / 16pt)</option>
                            <option value="compact">Compact (1.10rem / 12pt)</option>
                        </select>
                        @error('letterhead_office_font_size')
                            <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Dedicated Letterhead Emblem Upload -->
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                            <span>Letterhead Crest / Emblem</span>
                            <x-help-hint type="inline" 
                                         variant="tip" 
                                         title="Official Document Emblem" 
                                         text="Upload the official high-resolution university crest or institutional emblem for document printing." />
                        </label>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <input type="file" 
                                   wire:model="letterheadLogoUpload" 
                                   accept="image/*" 
                                   class="form-control" 
                                   style="font-size: 0.85rem; padding: 0.5rem 0.75rem;">
                            @if(!empty($letterhead_logo) && $letterhead_logo !== '/images/logo-icon.svg')
                                <button type="button" 
                                        wire:click="$set('letterhead_logo', '/images/logo-icon.svg')" 
                                        class="btn btn-secondary btn-sm" 
                                        style="white-space: nowrap; font-size: 0.75rem;" 
                                        title="Reset to standard brand logo">
                                    Use App Logo
                                </button>
                            @endif
                        </div>
                        <small style="color: var(--color-text-secondary); font-size: 0.76rem; display: block; margin-top: 0.25rem;">Supports PNG, SVG, JPG (Max 2MB). Fits inside 72×72px official emblem box.</small>
                        @error('letterheadLogoUpload')
                            <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Row 3: Rich Textarea / Multi-Line Details -->
                <div>
                    <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                        <span>Custom Letterhead Details / Contact & Statutory References</span>
                        <x-help-hint type="inline" 
                                     variant="info" 
                                     title="Rich Letterhead Details" 
                                     text="Multi-line contact details, statutory act citations, physical address, and telephone lines rendered under the letterhead titles." />
                    </label>
                    <textarea wire:model.live="letterhead_custom_details" 
                              rows="3" 
                              class="form-control" 
                              placeholder="e.g. Main Campus, Toru Road, Mardan, Khyber Pakhtunkhwa&#10;Phone: +92-937-9230611 • Fax: +92-937-9230612 • Web: www.awkum.edu.pk" 
                              style="font-size: 0.88rem; font-family: monospace; padding: 0.75rem 1rem;"></textarea>
                    <small style="color: var(--color-text-secondary); font-size: 0.76rem; display: block; margin-top: 0.25rem;">Line breaks are preserved on printed letterheads.</small>
                    @error('letterhead_custom_details')
                        <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Row 4: Document Titles for Admin Dossier & Candidate Form -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                            <span>Master Dossier Document Title (Staff &amp; Admin View)</span>
                            <x-help-hint type="inline" 
                                         variant="info" 
                                         title="Master Dossier Document Title" 
                                         text="Title printed beneath letterhead when downloaded by Staff, Registrar, or Scrutiny Officers." 
                                         example="Default: Official Master Application Dossier & Scrutiny Record" />
                        </label>
                        <input type="text" 
                               wire:model.live="letterhead_dossier_title_admin" 
                               class="form-control" 
                               placeholder="Official Master Application Dossier & Scrutiny Record" 
                               style="font-size: 0.88rem; font-weight: 600; padding: 0.65rem 1rem;">
                        @error('letterhead_dossier_title_admin')
                            <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                            <span>Candidate Application Form Title (Applicant View)</span>
                            <x-help-hint type="inline" 
                                         variant="info" 
                                         title="Candidate Application Form Title" 
                                         text="Title printed beneath letterhead when downloaded directly by the candidate from their applicant portal." 
                                         example="Default: Official Application Form for Employment" />
                        </label>
                        <input type="text" 
                               wire:model.live="letterhead_dossier_title_candidate" 
                               class="form-control" 
                               placeholder="Official Application Form for Employment" 
                               style="font-size: 0.88rem; font-weight: 600; padding: 0.65rem 1rem;">
                        @error('letterhead_dossier_title_candidate')
                            <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Row 5: Document Print & Letterhead Display Controls -->
                <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem;">
                    <div style="font-size: 0.85rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.85rem; display: flex; align-items: center; gap: 0.4rem;">
                        <span>🖨️ Dossier &amp; Printed Document Layout Toggles</span>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem;">
                        <label style="display: flex; align-items: center; gap: 0.65rem; cursor: pointer; font-size: 0.85rem; font-weight: 700; color: var(--color-text-primary);">
                            <input type="checkbox" wire:model.live="letterhead_show_document_title" style="width: 17px; height: 17px; accent-color: var(--primary);">
                            <span>Show Document Title on Letterhead</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 0.65rem; cursor: pointer; font-size: 0.85rem; font-weight: 700; color: var(--color-text-primary);">
                            <input type="checkbox" wire:model.live="letterhead_show_statutory_note" style="width: 17px; height: 17px; accent-color: var(--primary);">
                            <span>Show Statutory Reference Subtitle</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 0.65rem; cursor: pointer; font-size: 0.85rem; font-weight: 700; color: var(--color-text-primary);">
                            <input type="checkbox" wire:model.live="dossier_show_photo" style="width: 17px; height: 17px; accent-color: var(--primary);">
                            <span>Include Candidate Photo Box</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 0.65rem; cursor: pointer; font-size: 0.85rem; font-weight: 700; color: var(--color-text-primary);">
                            <input type="checkbox" wire:model.live="dossier_show_draft_watermark" style="width: 17px; height: 17px; accent-color: var(--primary);">
                            <span>Watermark Unsubmitted Drafts</span>
                        </label>
                    </div>
                </div>

                <!-- Save Bar for Letterhead -->
                <div style="display: flex; justify-content: flex-end; padding-top: 0.75rem; border-top: 1px solid var(--border-color);">
                    @can('manage-settings')
                    <button type="submit" 
                            class="btn btn-primary" 
                            style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.6rem; font-weight: 800; font-size: 0.92rem; box-shadow: 0 4px 14px var(--primary-glow);">
                        <span wire:loading.remove wire:target="saveLetterheadSettings">Save Letterhead Settings</span>
                        <span wire:loading wire:target="saveLetterheadSettings">Saving Letterhead...</span>
                    </button>
                    @endcan
                </div>

            </div>
        </form>
    </div>

</div>
