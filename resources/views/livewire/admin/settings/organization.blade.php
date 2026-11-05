<div class="card mb-6" style="padding: 1.75rem; border-color: var(--primary); box-shadow: var(--shadow-md);">
    <!-- Card Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                <span class="badge badge-indigo" style="font-size: 0.72rem; font-weight: 800;">INSTITUTIONAL IDENTITY</span>
                <span class="badge badge-slate" style="font-size: 0.72rem;">TAXONOMY & BRANDING</span>
            </div>
            <h2 class="card-title" style="margin: 0; font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary);">
                Organization Profile, Institutional Sector & Fee Standards
            </h2>
            <p class="card-subtitle" style="margin: 0.25rem 0 0 0; font-size: 0.85rem; color: var(--color-text-secondary);">
                Customize institutional nomenclature, official legal identity, currency standards, and default baseline application processing fees.
            </p>
        </div>

        @can('manage-settings')
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

    {{-- System Help Callout --}}
    <x-help-hint type="callout" variant="info" title="System-Wide Branding & Nomenclature">
        These parameters customize the entire candidate portal and administrative workspace, including printed bank challan vouchers, appointment letters, and system email notifications.
    </x-help-hint>

    <form wire:submit.prevent="saveOrganizationProfile" style="margin-top: 1.5rem;">
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            <!-- 1. Legal Entity / University Name -->
            <div>
                <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                    <span>Official University / Organization Legal Name</span>
                    <span style="color: var(--danger);">*</span>
                    <x-help-hint type="inline" 
                                 variant="info" 
                                 title="Official Legal Entity Name" 
                                 text="Displayed prominently in system headers, printable candidate receipts, and official evaluation dossiers." />
                </label>
                <input type="text" 
                       wire:model="org_name" 
                       class="form-control" 
                       placeholder="e.g. Abdul Wali Khan University Mardan" 
                       style="font-size: 0.95rem; font-weight: 600; padding: 0.65rem 1rem;">
                @error('org_name')
                    <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Two-Column Grid: Org Type & Sector Label -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.25rem;">
                
                <!-- 2. Organizational Sector Structure -->
                <div>
                    <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                        <span>Organizational Sector / Governance Model</span>
                        <span style="color: var(--danger);">*</span>
                        <x-help-hint type="inline" 
                                     variant="statutory" 
                                     title="Governance Structure" 
                                     text="Adjusts default statutory pipeline requirements and review committee nomenclatures across the system." />
                    </label>
                    <select wire:model="org_type" class="form-control form-select" style="font-size: 0.92rem; padding: 0.65rem 1rem;">
                        <option value="HIGHER_EDUCATION">🏛️ Public Sector University / Higher Education (HEC Rules)</option>
                        <option value="AUTONOMOUS_INSTITUTE">🔬 Autonomous Degree Awarding Institute / Research Centre</option>
                        <option value="PUBLIC_SECTOR">🏛️ Statutory Civil Service / Public Service Commission</option>
                        <option value="CORPORATE_ENTERPRISE">🏢 Corporate Enterprise / Semi-Government Authority</option>
                    </select>
                    @error('org_type')
                        <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 3. Sector / Department Taxonomy Label -->
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
                           wire:model="sector_label" 
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
                
                <!-- 4. Currency Symbol -->
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
                           wire:model="currency_symbol" 
                           class="form-control" 
                           placeholder="e.g. PKR" 
                           style="font-size: 0.92rem; font-weight: 700; padding: 0.65rem 1rem;">
                    @error('currency_symbol')
                        <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 5. Default Baseline Application Fee -->
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
                               wire:model="default_application_fee" 
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

        </div>

        <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
            <button type="submit" 
                    class="btn btn-primary" 
                    style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.75rem; font-weight: 800; font-size: 0.92rem; box-shadow: 0 4px 14px var(--primary-glow);">
                <span wire:loading.remove wire:target="saveOrganizationProfile">Save Institutional Profile</span>
                <span wire:loading wire:target="saveOrganizationProfile">Saving Profile...</span>
            </button>
        </div>
    </form>
</div>
