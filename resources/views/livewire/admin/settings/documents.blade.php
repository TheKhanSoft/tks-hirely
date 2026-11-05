<div class="card mb-6" style="padding: 1.75rem; border-color: var(--primary); box-shadow: var(--shadow-md);">
    <!-- Card Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                <span class="badge badge-cyan" style="font-size: 0.72rem; font-weight: 800;">VERIFICATION GATES</span>
                <span class="badge badge-slate" style="font-size: 0.72rem;">CANDIDATE DOSSIER</span>
            </div>
            <h2 class="card-title" style="margin: 0; font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary);">
                Mandatory Candidate Document Proofs & Dossier Verification
            </h2>
            <p class="card-subtitle" style="margin: 0.25rem 0 0 0; font-size: 0.85rem; color: var(--color-text-secondary);">
                Enforce mandatory file upload requirements that applicants must submit during profile creation and application intake.
            </p>
        </div>

        @can('manage-settings')
        <button type="button" 
                wire:click="saveDocumentSettings" 
                class="btn btn-primary" 
                style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.4rem; font-weight: 800; font-size: 0.88rem; box-shadow: 0 4px 14px var(--primary-glow);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                <polyline points="17 21 17 13 7 13 7 21"/>
                <polyline points="7 3 7 8 15 8"/>
            </svg>
            <span wire:loading.remove wire:target="saveDocumentSettings">Save Document Rules</span>
            <span wire:loading wire:target="saveDocumentSettings">Saving...</span>
        </button>
        @endcan
    </div>

    {{-- System Help Callout --}}
    <x-help-hint type="callout" variant="info" title="Application Submission Enforcement">
        When an upload rule is activated, candidates cannot finalize application voucher generation or submit their dossier until valid digital copies (PDF/JPG/PNG) are attached and verified by the system's file validator.
    </x-help-hint>

    <form wire:submit.prevent="saveDocumentSettings" style="margin-top: 1.5rem;">
        
        <!-- Category 1: Academic Credentials & Research -->
        <div style="margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                <span style="font-size: 1.1rem;">🎓</span>
                <h4 style="margin: 0; font-size: 0.95rem; font-weight: 800; color: var(--color-text-primary);">
                    Academic Credentials & Research Evidence
                </h4>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1rem;">
                
                <!-- 1. Degree & Transcript Upload -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.15rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    <div style="max-width: 80%;">
                        <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem;">
                            <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                                Degree Certificates & Official Transcripts
                            </span>
                            <x-help-hint type="inline" 
                                         variant="statutory"
                                         statute="HEC Regulation 4.1"
                                         title="Degree & Mark Sheet Proof"
                                         text="Mandates uploading clear scans of Matric, Inter, Bachelor, Master, MPhil, and PhD degrees with mark sheets/DMC." />
                        </div>
                        <div style="font-size: 0.8rem; color: var(--color-text-secondary); line-height: 1.45;">
                            Enforces uploading certified transcript scans for every educational record added to candidate profiles.
                        </div>
                    </div>
                    <div>
                        <label style="position: relative; display: inline-block; width: 50px; height: 26px; cursor: pointer;">
                            <input type="checkbox" wire:model="require_transcript_proof" style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $require_transcript_proof ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $require_transcript_proof ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                                <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $require_transcript_proof ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%;"></span>
                            </span>
                        </label>
                    </div>
                </div>

                <!-- 2. Academic Publications -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.15rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    <div style="max-width: 80%;">
                        <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem;">
                            <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                                Journal Publications & Research Papers
                            </span>
                            <x-help-hint type="inline" 
                                         variant="statutory"
                                         statute="HEC HJRS Mandate"
                                         title="HEC Journal Proofs"
                                         text="Requires uploading first-page or full-text PDFs with visible DOI, ISSN, and HEC Journal Category (W, X, Y)." />
                        </div>
                        <div style="font-size: 0.8rem; color: var(--color-text-secondary); line-height: 1.45;">
                            Requires PDF reprints or DOI publication proofs for all academic research faculty applications.
                        </div>
                    </div>
                    <div>
                        <label style="position: relative; display: inline-block; width: 50px; height: 26px; cursor: pointer;">
                            <input type="checkbox" wire:model="require_publications_academic" style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $require_publications_academic ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $require_publications_academic ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                                <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $require_publications_academic ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%;"></span>
                            </span>
                        </label>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 2: Civil Identity & Legal Clearance -->
        <div style="margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                <span style="font-size: 1.1rem;">🪪</span>
                <h4 style="margin: 0; font-size: 0.95rem; font-weight: 800; color: var(--color-text-primary);">
                    Civil Identity, Nationality & In-Service Clearance
                </h4>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1rem;">
                
                <!-- 3. CNIC / ID Card -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.15rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    <div style="max-width: 80%;">
                        <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem;">
                            <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                                CNIC / National ID Card Front & Back Scan
                            </span>
                            <x-help-hint type="inline" 
                                         variant="info"
                                         title="Identity Verification"
                                         text="Validates candidate citizenship, age verification against NADRA database, and provincial domicile." />
                        </div>
                        <div style="font-size: 0.8rem; color: var(--color-text-secondary); line-height: 1.45;">
                            Mandates clear digital copy of computerized national identity card or international passport for overseas applicants.
                        </div>
                    </div>
                    <div>
                        <label style="position: relative; display: inline-block; width: 50px; height: 26px; cursor: pointer;">
                            <input type="checkbox" wire:model="require_cnic_upload" style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $require_cnic_upload ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $require_cnic_upload ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                                <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $require_cnic_upload ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%;"></span>
                            </span>
                        </label>
                    </div>
                </div>

                <!-- 4. Employer NOC -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.15rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    <div style="max-width: 80%;">
                        <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem;">
                            <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                                Employer NOC (No Objection Certificate)
                            </span>
                            <x-help-hint type="inline" 
                                         variant="statutory"
                                         statute="Civil Service Rules Sec. 18"
                                         title="Government / Semi-Gov Clearance"
                                         text="Candidates currently employed in government, autonomous bodies, or statutory universities must attach a department clearance NOC." />
                        </div>
                        <div style="font-size: 0.8rem; color: var(--color-text-secondary); line-height: 1.45;">
                            Restricts government/public servants from final submission without certified departmental permission.
                        </div>
                    </div>
                    <div>
                        <label style="position: relative; display: inline-block; width: 50px; height: 26px; cursor: pointer;">
                            <input type="checkbox" wire:model="require_noc_employed" style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $require_noc_employed ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $require_noc_employed ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                                <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $require_noc_employed ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%;"></span>
                            </span>
                        </label>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 3: Experience & Financial Settlements -->
        <div style="margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                <span style="font-size: 1.1rem;">💼</span>
                <h4 style="margin: 0; font-size: 0.95rem; font-weight: 800; color: var(--color-text-primary);">
                    Service Experience & Fee Settlement Receipts
                </h4>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1rem;">
                
                <!-- 5. Experience Certificate Proof -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.15rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    <div style="max-width: 80%;">
                        <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem;">
                            <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                                Professional Experience & Service Certificates
                            </span>
                            <x-help-hint type="inline" 
                                         variant="info"
                                         title="Experience Verification"
                                         text="Mandates experience letter with joining/relieving dates and designation for calculating post-qualification service years." />
                        </div>
                        <div style="font-size: 0.8rem; color: var(--color-text-secondary); line-height: 1.45;">
                            Enforces attaching signed appointment or relieving letters for all claiming post-qualification experience years.
                        </div>
                    </div>
                    <div>
                        <label style="position: relative; display: inline-block; width: 50px; height: 26px; cursor: pointer;">
                            <input type="checkbox" wire:model="require_experience_proof" style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $require_experience_proof ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $require_experience_proof ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                                <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $require_experience_proof ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%;"></span>
                            </span>
                        </label>
                    </div>
                </div>

                <!-- 6. Bank Challan Deposit Slip Scan -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.15rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    <div style="max-width: 80%;">
                        <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem;">
                            <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                                Bank Challan Stamped Deposit Receipt Scan
                            </span>
                            <x-help-hint type="inline" 
                                         variant="tip"
                                         title="Challan Stamped Slip"
                                         text="Useful when online API auto-reconciliation is unavailable and applicants pay at physical bank branches." />
                        </div>
                        <div style="font-size: 0.8rem; color: var(--color-text-secondary); line-height: 1.45;">
                            Demands uploading stamped bank deposit receipt if paid via manual branch counter rather than Kuickpay API.
                        </div>
                    </div>
                    <div>
                        <label style="position: relative; display: inline-block; width: 50px; height: 26px; cursor: pointer;">
                            <input type="checkbox" wire:model="require_challan_receipt_upload" style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $require_challan_receipt_upload ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $require_challan_receipt_upload ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                                <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $require_challan_receipt_upload ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%;"></span>
                            </span>
                        </label>
                    </div>
                </div>

            </div>
        </div>

        <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
            <button type="submit" 
                    class="btn btn-primary" 
                    style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.75rem; font-weight: 800; font-size: 0.92rem; box-shadow: 0 4px 14px var(--primary-glow);">
                <span wire:loading.remove wire:target="saveDocumentSettings">Save Document Verification Rules</span>
                <span wire:loading wire:target="saveDocumentSettings">Saving Document Rules...</span>
            </button>
        </div>
    </form>
</div>
