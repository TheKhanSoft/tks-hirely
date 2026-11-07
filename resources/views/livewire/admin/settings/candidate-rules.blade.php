<div class="card mb-6" style="padding: 1.75rem; border-color: var(--primary); box-shadow: var(--shadow-md);">
    <!-- Card Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                <span class="badge badge-purple" style="font-size: 0.72rem; font-weight: 800;">APPLICANT LIFECYCLE</span>
                <span class="badge badge-slate" style="font-size: 0.72rem;">INTAKE POLICIES</span>
                <span class="badge badge-amber" style="font-size: 0.72rem; font-weight: 700;">FRAUD PREVENTION</span>
            </div>
            <h2 class="card-title" style="margin: 0; font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary);">
                Candidate Eligibility, Submission & Voucher Rules
            </h2>
            <p class="card-subtitle" style="margin: 0.25rem 0 0 0; font-size: 0.85rem; color: var(--color-text-secondary);">
                Configure cross-campaign limits, duplicate dossier prevention, payment settlement windows, and profile modification freeze locks.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            @can('manage-settings')
            <button type="button" 
                    wire:click="resetCandidateRulesToDefault" 
                    wire:confirm="Reset candidate policies to system defaults (14 days grace, multi-app allowed, duplicate blocked, auto-lock on)?"
                    class="btn btn-secondary btn-sm" 
                    style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.55rem 0.95rem; font-weight: 700; font-size: 0.82rem;"
                    title="Restore candidate rules to standard defaults">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>
                <span>Reset Candidate Rules</span>
            </button>

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
            @endcan
        </div>
    </div>

    {{-- System Help Callout --}}
    <x-help-hint type="callout" variant="info" title="Applicant Governance Architecture">
        These policies safeguard application integrity, prevent spam or unauthorized duplicate candidate submissions, and ensure statutory fee settlement timelines align with bank clearing cycles.
    </x-help-hint>

    {{-- INTERACTIVE CHALLAN TIMELINE PREVIEW STRIP --}}
    <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem 1.5rem; margin-top: 1.25rem; margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem; flex-wrap: wrap; gap: 0.5rem;">
            <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--secondary); letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.4rem;">
                <span>⏱️ Live Challan Payment Grace Window Calculator</span>
            </div>
            <span class="badge badge-amber" style="font-size: 0.7rem; font-weight: 800;">{{ $challan_grace_days ?? 14 }} Calendar Days Allowed</span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: center;">
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem 1rem;">
                <div style="font-size: 0.72rem; color: var(--color-text-muted); font-weight: 700; text-transform: uppercase;">Step 1: Voucher Generated</div>
                <div style="font-size: 0.95rem; font-weight: 800; color: var(--color-text-primary); margin-top: 0.2rem;">Day 0 (Initial Intake)</div>
                <div style="font-size: 0.72rem; color: var(--color-text-secondary); margin-top: 0.15rem;">1Link / Kuickpay consumer ID issued</div>
            </div>

            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem 1rem; border-left: 3px solid var(--primary);">
                <div style="font-size: 0.72rem; color: var(--primary); font-weight: 800; text-transform: uppercase;">Step 2: Settlement Window</div>
                <div style="font-size: 0.95rem; font-weight: 800; color: var(--color-text-primary); margin-top: 0.2rem;">{{ $challan_grace_days ?? 14 }} Days Valid</div>
                <div style="font-size: 0.72rem; color: var(--color-text-secondary); margin-top: 0.15rem;">Payable via online app, ATM, or bank counter</div>
            </div>

            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem 1rem; border-left: 3px solid var(--danger);">
                <div style="font-size: 0.72rem; color: var(--danger); font-weight: 800; text-transform: uppercase;">Step 3: Expiry & Void</div>
                <div style="font-size: 0.95rem; font-weight: 800; color: var(--color-text-primary); margin-top: 0.2rem;">Day {{ ($challan_grace_days ?? 14) + 1 }} Midnight</div>
                <div style="font-size: 0.72rem; color: var(--color-text-secondary); margin-top: 0.15rem;">Unpaid voucher cancelled automatically</div>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="saveCandidateRules" style="margin-top: 1.5rem;">
        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
            
            <!-- 1. Multiple Applications Across Campaigns -->
            <div id="setting-candidate-multi-app" style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; background: var(--bg-surface); border: 1px solid {{ $allow_multiple_applications ? 'var(--primary-glow)' : 'var(--border-color)' }}; border-radius: var(--radius-md); transition: var(--transition-fast);">
                <div style="max-width: 80%;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem; flex-wrap: wrap;">
                        <span style="font-size: 1.15rem;">🌐</span>
                        <span style="font-size: 0.98rem; font-weight: 800; color: var(--color-text-primary);">
                            Allow Multiple Active Applications Across Separate Positions
                        </span>
                        <span class="badge {{ $allow_multiple_applications ? 'badge-emerald' : 'badge-slate' }}" style="font-size: 0.65rem; font-weight: 800;">
                            {{ $allow_multiple_applications ? 'PERMITTED' : 'RESTRICTED TO 1' }}
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
                        <input type="checkbox" wire:model.live="allow_multiple_applications" style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $allow_multiple_applications ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $allow_multiple_applications ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                            <span style="position: absolute; content: ''; height: 20px; width: 20px; left: {{ $allow_multiple_applications ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- 2. Prevent Duplicate Applications for Same Position -->
            <div id="setting-candidate-duplicate-block" style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; background: var(--bg-surface); border: 1px solid {{ $prevent_duplicate_campaign_applications ? 'var(--primary-glow)' : 'var(--border-color)' }}; border-radius: var(--radius-md); transition: var(--transition-fast);">
                <div style="max-width: 80%;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem; flex-wrap: wrap;">
                        <span style="font-size: 1.15rem;">🛡️</span>
                        <span style="font-size: 0.98rem; font-weight: 800; color: var(--color-text-primary);">
                            Strict Duplicate Application Blocking (Same Position)
                        </span>
                        <span class="badge {{ $prevent_duplicate_campaign_applications ? 'badge-indigo' : 'badge-amber' }}" style="font-size: 0.65rem; font-weight: 800;">
                            {{ $prevent_duplicate_campaign_applications ? 'STRICT BLOCK ON' : 'DUPLICATES ALLOWED' }}
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
                        <input type="checkbox" wire:model.live="prevent_duplicate_campaign_applications" style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $prevent_duplicate_campaign_applications ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $prevent_duplicate_campaign_applications ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                            <span style="position: absolute; content: ''; height: 20px; width: 20px; left: {{ $prevent_duplicate_campaign_applications ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- 3. Profile Auto-Lock On Voucher Generation -->
            <div id="setting-candidate-profile-lock" style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; background: var(--bg-surface); border: 1px solid {{ $auto_lock_profile_on_voucher ? 'var(--primary-glow)' : 'var(--border-color)' }}; border-radius: var(--radius-md); transition: var(--transition-fast);">
                <div style="max-width: 80%;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem; flex-wrap: wrap;">
                        <span style="font-size: 1.15rem;">🔒</span>
                        <span style="font-size: 0.98rem; font-weight: 800; color: var(--color-text-primary);">
                            Auto-Lock Candidate Profile Upon Challan / Voucher Generation
                        </span>
                        <span class="badge {{ $auto_lock_profile_on_voucher ? 'badge-emerald' : 'badge-slate' }}" style="font-size: 0.65rem; font-weight: 800;">
                            {{ $auto_lock_profile_on_voucher ? 'TAMPER LOCK ACTIVE' : 'UNLOCKED' }}
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
                        <input type="checkbox" wire:model.live="auto_lock_profile_on_voucher" style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $auto_lock_profile_on_voucher ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $auto_lock_profile_on_voucher ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                            <span style="position: absolute; content: ''; height: 20px; width: 20px; left: {{ $auto_lock_profile_on_voucher ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- 4. Bank Challan Grace Period -->
            <div id="setting-candidate-grace-period" style="padding: 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap;">
                    <div style="max-width: 70%;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                            <span style="font-size: 1.15rem;">⏳</span>
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
                                   wire:model.live="challan_grace_days" 
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

            <!-- 5. Application Withdrawal Policy -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; background: var(--bg-surface); border: 1px solid {{ $candidate_allow_application_withdrawal ? 'var(--primary-glow)' : 'var(--border-color)' }}; border-radius: var(--radius-md); transition: var(--transition-fast);">
                <div style="max-width: 80%;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem; flex-wrap: wrap;">
                        <span style="font-size: 1.15rem;">↩️</span>
                        <span style="font-size: 0.98rem; font-weight: 800; color: var(--color-text-primary);">
                            Allow Candidates to Withdraw Applications (Pre-Scrutiny)
                        </span>
                        <span class="badge {{ $candidate_allow_application_withdrawal ? 'badge-amber' : 'badge-slate' }}" style="font-size: 0.65rem; font-weight: 800;">
                            {{ $candidate_allow_application_withdrawal ? 'WITHDRAWAL ENABLED' : 'NON-WITHDRAWABLE' }}
                        </span>
                        <x-help-hint type="inline" 
                                     variant="info"
                                     title="Candidate Withdrawal"
                                     text="Permits candidates to withdraw their submitted application from their dashboard before scrutiny vetting begins." />
                    </div>
                    <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.5;">
                        Allows applicants to voluntarily withdraw their submission before the statutory scrutiny committee initiates formal file evaluation.
                    </div>
                </div>
                <div>
                    <label style="position: relative; display: inline-block; width: 52px; height: 28px; cursor: pointer;">
                        <input type="checkbox" wire:model.live="candidate_allow_application_withdrawal" style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $candidate_allow_application_withdrawal ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $candidate_allow_application_withdrawal ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                            <span style="position: absolute; content: ''; height: 20px; width: 20px; left: {{ $candidate_allow_application_withdrawal ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- 6. Intake Limits & Document Upload Quotas -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                <div style="padding: 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    <div style="display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.25rem;">
                        <span style="font-size: 1.15rem;">📊</span>
                        <span style="font-size: 0.95rem; font-weight: 800; color: var(--color-text-primary);">Max Active Applications / Candidate</span>
                    </div>
                    <div style="font-size: 0.78rem; color: var(--color-text-secondary); margin-bottom: 0.75rem;">
                        Maximum number of active concurrent applications an applicant can hold at once.
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="number" 
                               wire:model.live="max_active_applications_per_candidate" 
                               min="1" 
                               max="20" 
                               class="form-control" 
                               style="width: 90px; text-align: center; font-weight: 800; font-size: 1.05rem;">
                        <span style="font-size: 0.82rem; font-weight: 700; color: var(--color-text-secondary);">Applications</span>
                    </div>
                    @error('max_active_applications_per_candidate')
                        <div style="color: var(--danger); font-size: 0.75rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="padding: 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    <div style="display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.25rem;">
                        <span style="font-size: 1.15rem;">📁</span>
                        <span style="font-size: 0.95rem; font-weight: 800; color: var(--color-text-primary);">Proof Upload Max File Size (MB)</span>
                    </div>
                    <div style="font-size: 0.78rem; color: var(--color-text-secondary); margin-bottom: 0.75rem;">
                        Maximum file size allowed for degree scans, CNIC, and employment certificates.
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="number" 
                               wire:model.live="max_upload_file_size_mb" 
                               min="1" 
                               max="50" 
                               class="form-control" 
                               style="width: 90px; text-align: center; font-weight: 800; font-size: 1.05rem;">
                        <span style="font-size: 0.82rem; font-weight: 700; color: var(--color-text-secondary);">MB per file</span>
                    </div>
                    @error('max_upload_file_size_mb')
                        <div style="color: var(--danger); font-size: 0.75rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- 7. Candidate Online Challan Auto-Verification -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; background: var(--bg-surface); border: 1px solid {{ $enable_online_challan_verification ? 'var(--primary-glow)' : 'var(--border-color)' }}; border-radius: var(--radius-md); transition: var(--transition-fast);">
                <div style="max-width: 80%;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem; flex-wrap: wrap;">
                        <span style="font-size: 1.15rem;">⚡</span>
                        <span style="font-size: 0.98rem; font-weight: 800; color: var(--color-text-primary);">
                            Candidate Self-Service 1Link / 1Bill Voucher Verification
                        </span>
                        <span class="badge {{ $enable_online_challan_verification ? 'badge-emerald' : 'badge-slate' }}" style="font-size: 0.65rem; font-weight: 800;">
                            {{ $enable_online_challan_verification ? 'ACTIVE DESK' : 'ADMIN ONLY' }}
                        </span>
                        <x-help-hint type="inline" 
                                     variant="tip"
                                     title="Payment Verification Trigger"
                                     text="Allows candidates to click 'Verify Payment Status' from their portal to fetch live 1Link/Kuickpay settlement." />
                    </div>
                    <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.5;">
                        Allows applicants to initiate instant real-time bank ledger reconciliation for their paid challans directly from the applicant dashboard.
                    </div>
                </div>
                <div>
                    <label style="position: relative; display: inline-block; width: 52px; height: 28px; cursor: pointer;">
                        <input type="checkbox" wire:model.live="enable_online_challan_verification" style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $enable_online_challan_verification ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $enable_online_challan_verification ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                            <span style="position: absolute; content: ''; height: 20px; width: 20px; left: {{ $enable_online_challan_verification ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>
                        </span>
                    </label>
                </div>
            </div>

        </div>

        <div style="margin-top: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
            @can('manage-settings')
            <button type="button" 
                    wire:click="resetCandidateRulesToDefault" 
                    wire:confirm="Reset candidate policies to system defaults (14 days grace, multi-app allowed, duplicate blocked, auto-lock on)?"
                    class="btn btn-secondary btn-sm" 
                    style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/></svg>
                <span>Reset Candidate Rules</span>
            </button>

            <button type="submit" 
                    class="btn btn-primary" 
                    style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.75rem; font-weight: 800; font-size: 0.92rem; box-shadow: 0 4px 14px var(--primary-glow);">
                <span wire:loading.remove wire:target="saveCandidateRules">Save Candidate Rules</span>
                <span wire:loading wire:target="saveCandidateRules">Saving Candidate Rules...</span>
            </button>
            @endcan
        </div>
    </form>
</div>
