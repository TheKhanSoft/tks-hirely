<div class="card mb-6" style="padding: 1.75rem; border-color: var(--primary); box-shadow: var(--shadow-md);">
    <!-- Card Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                <span class="badge badge-purple" style="font-size: 0.72rem; font-weight: 800;">APPLICANT LIFECYCLE</span>
                <span class="badge badge-slate" style="font-size: 0.72rem;">INTAKE POLICIES</span>
            </div>
            <h2 class="card-title" style="margin: 0; font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary);">
                Candidate Eligibility, Submission & Voucher Rules
            </h2>
            <p class="card-subtitle" style="margin: 0.25rem 0 0 0; font-size: 0.85rem; color: var(--color-text-secondary);">
                Configure cross-campaign limits, duplicate dossier prevention, payment settlement windows, and profile modification freeze locks.
            </p>
        </div>

        <button type="button" 
                wire:click="saveCandidateRules" 
                class="btn btn-primary" 
                style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.4rem; font-weight: 800; font-size: 0.88rem; box-shadow: 0 4px 14px var(--primary-glow);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                <polyline points="17 21 17 13 7 13 7 21"/>
                <polyline points="7 3 7 8 15 8"/>
            </svg>
            <span wire:loading.remove wire:target="saveCandidateRules">Save Candidate Rules</span>
            <span wire:loading wire:target="saveCandidateRules">Saving...</span>
        </button>
    </div>

    {{-- System Help Callout --}}
    <x-help-hint type="callout" variant="info" title="Applicant Governance Architecture">
        These policies safeguard application integrity, prevent spam or unauthorized duplicate candidate submissions, and ensure statutory fee settlement timelines align with bank clearing cycles.
    </x-help-hint>

    <form wire:submit.prevent="saveCandidateRules" style="margin-top: 1.5rem;">
        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
            
            <!-- 1. Multiple Applications Across Campaigns -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                <div style="max-width: 80%;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                        <span style="font-size: 1.1rem;">🌐</span>
                        <span style="font-size: 0.98rem; font-weight: 800; color: var(--color-text-primary);">
                            Allow Multiple Active Applications Across Separate Positions
                        </span>
                        <x-help-hint type="inline" 
                                     variant="info"
                                     title="Multi-Post Intake"
                                     text="Permits an applicant to apply to multiple distinct posts (e.g. Lecturer in CS and Assistant Professor in SE) within the same advertisement drive." />
                    </div>
                    <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.5;">
                        When enabled, a single applicant profile can submit separate dossiers and fee settlements for different advertised job campaigns simultaneously.
                    </div>
                </div>
                <div>
                    <label style="position: relative; display: inline-block; width: 52px; height: 28px; cursor: pointer;">
                        <input type="checkbox" wire:model="allow_multiple_applications" style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $allow_multiple_applications ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $allow_multiple_applications ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                            <span style="position: absolute; content: ''; height: 20px; width: 20px; left: {{ $allow_multiple_applications ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- 2. Prevent Duplicate Applications for Same Position -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                <div style="max-width: 80%;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                        <span style="font-size: 1.1rem;">🛡️</span>
                        <span style="font-size: 0.98rem; font-weight: 800; color: var(--color-text-primary);">
                            Strict Duplicate Application Blocking (Same Position)
                        </span>
                        <x-help-hint type="inline" 
                                     variant="warning"
                                     title="Duplicate Elimination"
                                     text="Hard database constraint preventing duplicate applications for the same job campaign using the same CNIC or user ID." />
                    </div>
                    <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.5;">
                        Strictly forbids an applicant from submitting more than one dossier for the exact same job post or campaign code.
                    </div>
                </div>
                <div>
                    <label style="position: relative; display: inline-block; width: 52px; height: 28px; cursor: pointer;">
                        <input type="checkbox" wire:model="prevent_duplicate_campaign_applications" style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $prevent_duplicate_campaign_applications ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $prevent_duplicate_campaign_applications ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                            <span style="position: absolute; content: ''; height: 20px; width: 20px; left: {{ $prevent_duplicate_campaign_applications ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- 3. Profile Auto-Lock On Voucher Generation -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                <div style="max-width: 80%;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                        <span style="font-size: 1.1rem;">🔒</span>
                        <span style="font-size: 0.98rem; font-weight: 800; color: var(--color-text-primary);">
                            Auto-Lock Candidate Profile Upon Challan / Voucher Generation
                        </span>
                        <x-help-hint type="inline" 
                                     variant="statutory"
                                     title="Tamper Prevention Lock"
                                     text="Freezes basic info, degrees, and publications once the voucher is generated to prevent post-submission credential tampering." />
                    </div>
                    <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.5;">
                        Automatically freezes biographical data, academic records, and claimed experience once the candidate generates an official intake fee challan.
                    </div>
                </div>
                <div>
                    <label style="position: relative; display: inline-block; width: 52px; height: 28px; cursor: pointer;">
                        <input type="checkbox" wire:model="auto_lock_profile_on_voucher" style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $auto_lock_profile_on_voucher ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $auto_lock_profile_on_voucher ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                            <span style="position: absolute; content: ''; height: 20px; width: 20px; left: {{ $auto_lock_profile_on_voucher ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- 4. Bank Challan Grace Period -->
            <div style="padding: 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap;">
                    <div style="max-width: 70%;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                            <span style="font-size: 1.1rem;">⏳</span>
                            <span style="font-size: 0.98rem; font-weight: 800; color: var(--color-text-primary);">
                                Bank Fee Settlement Grace Period (Calendar Days)
                            </span>
                            <x-help-hint type="inline" 
                                         variant="tip"
                                         title="Challan Expiration Window"
                                         text="Sets the number of calendar days a candidate has to deposit payment at the bank before the voucher code expires."
                                         example="Standard: 14 days" />
                        </div>
                        <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.5;">
                            Number of calendar days granted to candidates after generating a fee challan to complete payment at 1Link, Kuickpay, or branch counters before expiry.
                        </div>
                    </div>

                    <div style="min-width: 160px;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <input type="number" 
                                   wire:model="challan_grace_days" 
                                   min="1" 
                                   max="90" 
                                   class="form-control" 
                                   style="width: 90px; text-align: center; font-weight: 800; font-size: 1.1rem; padding: 0.45rem 0.5rem;">
                            <span style="font-weight: 700; color: var(--color-text-secondary); font-size: 0.88rem;">Days</span>
                        </div>
                        @error('challan_grace_days')
                            <div style="color: var(--danger); font-size: 0.75rem; font-weight: 700; margin-top: 0.25rem;">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

        </div>

        <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
            <button type="submit" 
                    class="btn btn-primary" 
                    style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.75rem; font-weight: 800; font-size: 0.92rem; box-shadow: 0 4px 14px var(--primary-glow);">
                <span wire:loading.remove wire:target="saveCandidateRules">Save Candidate Intake Policies</span>
                <span wire:loading wire:target="saveCandidateRules">Saving Policies...</span>
            </button>
        </div>
    </form>
</div>
