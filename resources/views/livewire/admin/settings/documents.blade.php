<div class="card mb-6" style="padding: 1.75rem; border-color: var(--primary); box-shadow: var(--shadow-md);">
    <!-- Card Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                <span class="badge badge-cyan" style="font-size: 0.72rem; font-weight: 800;">VERIFICATION GATES</span>
                <span class="badge badge-slate" style="font-size: 0.72rem;">CANDIDATE DOSSIER</span>
                <span class="badge badge-indigo" style="font-size: 0.72rem; font-weight: 700;">DIGITAL EVIDENCE</span>
            </div>
            <h2 class="card-title" style="margin: 0; font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary);">
                Mandatory Candidate Document Proofs & Dossier Verification
            </h2>
            <p class="card-subtitle" style="margin: 0.25rem 0 0 0; font-size: 0.85rem; color: var(--color-text-secondary);">
                Enforce mandatory file upload requirements that applicants must submit during profile creation and application intake.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            @can('manage-settings')
            <button type="button" 
                    wire:click="resetDocumentsToDefault" 
                    wire:confirm="Reset all document verification rules to institutional defaults (transcripts, CNIC, and Challan mandatory)?"
                    class="btn btn-secondary btn-sm" 
                    style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.55rem 0.95rem; font-weight: 700; font-size: 0.82rem;"
                    title="Restore document rules to standard university defaults">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>
                <span>Reset Document Rules</span>
            </button>

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
    </div>

    {{-- System Help Callout --}}
    <x-help-hint type="callout" variant="info" title="Application Submission Enforcement">
        When an upload rule is activated, candidates cannot finalize application voucher generation or submit their dossier until valid digital copies (PDF/JPG/PNG) are attached and verified by the system's file validator.
    </x-help-hint>

    {{-- VISUAL DOCUMENT CHECKLIST MATRIX PREVIEW --}}
    <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem 1.5rem; margin-top: 1.25rem; margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem; flex-wrap: wrap; gap: 0.5rem;">
            <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--secondary); letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.4rem;">
                <span>📋 Candidate Submission Enforcement Matrix</span>
            </div>
            <span class="badge badge-slate" style="font-size: 0.7rem;">Live Portal Gating Status</span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.75rem;">
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.75rem 0.85rem; display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 0.82rem; font-weight: 700; color: var(--color-text-primary);">🎓 Transcripts</span>
                <span class="badge {{ $require_transcript_proof ? 'badge-emerald' : 'badge-slate' }}" style="font-size: 0.65rem; font-weight: 800;">
                    {{ $require_transcript_proof ? 'MANDATORY' : 'OPTIONAL' }}
                </span>
            </div>

            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.75rem 0.85rem; display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 0.82rem; font-weight: 700; color: var(--color-text-primary);">📚 Research Papers</span>
                <span class="badge {{ $require_publications_academic ? 'badge-emerald' : 'badge-slate' }}" style="font-size: 0.65rem; font-weight: 800;">
                    {{ $require_publications_academic ? 'MANDATORY' : 'OPTIONAL' }}
                </span>
            </div>

            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.75rem 0.85rem; display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 0.82rem; font-weight: 700; color: var(--color-text-primary);">🪪 CNIC / Passport</span>
                <span class="badge {{ $require_cnic_upload ? 'badge-emerald' : 'badge-slate' }}" style="font-size: 0.65rem; font-weight: 800;">
                    {{ $require_cnic_upload ? 'MANDATORY' : 'OPTIONAL' }}
                </span>
            </div>

            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.75rem 0.85rem; display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 0.82rem; font-weight: 700; color: var(--color-text-primary);">🏛️ Employer NOC</span>
                <span class="badge {{ $require_noc_employed ? 'badge-emerald' : 'badge-slate' }}" style="font-size: 0.65rem; font-weight: 800;">
                    {{ $require_noc_employed ? 'MANDATORY' : 'OPTIONAL' }}
                </span>
            </div>

            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.75rem 0.85rem; display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 0.82rem; font-weight: 700; color: var(--color-text-primary);">💼 Experience Letters</span>
                <span class="badge {{ $require_experience_proof ? 'badge-emerald' : 'badge-slate' }}" style="font-size: 0.65rem; font-weight: 800;">
                    {{ $require_experience_proof ? 'MANDATORY' : 'OPTIONAL' }}
                </span>
            </div>

            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.75rem 0.85rem; display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 0.82rem; font-weight: 700; color: var(--color-text-primary);">🧾 Challan Receipt</span>
                <span class="badge {{ $require_challan_receipt_upload ? 'badge-emerald' : 'badge-slate' }}" style="font-size: 0.65rem; font-weight: 800;">
                    {{ $require_challan_receipt_upload ? 'MANDATORY' : 'OPTIONAL' }}
                </span>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="saveDocumentSettings" style="margin-top: 1.5rem;">
        
        <!-- Category 1: Academic Credentials & Research -->
        <div style="margin-bottom: 1.75rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                <span style="font-size: 1.15rem;">🎓</span>
                <h4 style="margin: 0; font-size: 0.95rem; font-weight: 800; color: var(--color-text-primary);">
                    Academic Credentials & Research Evidence
                </h4>
                <span class="badge badge-slate" style="font-size: 0.65rem;">ACADEMIC TRACK</span>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1rem;">
                
                <!-- 1. Degree & Transcript Upload -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.15rem; background: var(--bg-surface); border: 1px solid {{ $require_transcript_proof ? 'var(--primary-glow)' : 'var(--border-color)' }}; border-radius: var(--radius-md); transition: var(--transition-fast);">
                    <div style="max-width: 80%;">
                        <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem; flex-wrap: wrap;">
                            <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                                Degree Certificates & Official Transcripts
                            </span>
                            <span class="badge {{ $require_transcript_proof ? 'badge-emerald' : 'badge-slate' }}" style="font-size: 0.65rem; font-weight: 800;">
                                {{ $require_transcript_proof ? 'ENFORCED' : 'WAIVED' }}
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
                            <input type="checkbox" wire:model.live="require_transcript_proof" style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $require_transcript_proof ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $require_transcript_proof ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                                <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $require_transcript_proof ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%;"></span>
                            </span>
                        </label>
                    </div>
                </div>

                <!-- 2. Academic Publications -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.15rem; background: var(--bg-surface); border: 1px solid {{ $require_publications_academic ? 'var(--primary-glow)' : 'var(--border-color)' }}; border-radius: var(--radius-md); transition: var(--transition-fast);">
                    <div style="max-width: 80%;">
                        <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem; flex-wrap: wrap;">
                            <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                                Journal Publications & Research Papers
                            </span>
                            <span class="badge {{ $require_publications_academic ? 'badge-emerald' : 'badge-slate' }}" style="font-size: 0.65rem; font-weight: 800;">
                                {{ $require_publications_academic ? 'ENFORCED' : 'WAIVED' }}
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
                            <input type="checkbox" wire:model.live="require_publications_academic" style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $require_publications_academic ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $require_publications_academic ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                                <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $require_publications_academic ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%;"></span>
                            </span>
                        </label>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 2: Civil Identity & Legal Clearance -->
        <div style="margin-bottom: 1.75rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                <span style="font-size: 1.15rem;">🪪</span>
                <h4 style="margin: 0; font-size: 0.95rem; font-weight: 800; color: var(--color-text-primary);">
                    Civil Identity, Nationality & In-Service Clearance
                </h4>
                <span class="badge badge-slate" style="font-size: 0.65rem;">LEGAL VERIFICATION</span>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1rem;">
                
                <!-- 3. CNIC / ID Card -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.15rem; background: var(--bg-surface); border: 1px solid {{ $require_cnic_upload ? 'var(--primary-glow)' : 'var(--border-color)' }}; border-radius: var(--radius-md); transition: var(--transition-fast);">
                    <div style="max-width: 80%;">
                        <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem; flex-wrap: wrap;">
                            <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                                CNIC / National ID Card Front & Back Scan
                            </span>
                            <span class="badge {{ $require_cnic_upload ? 'badge-emerald' : 'badge-slate' }}" style="font-size: 0.65rem; font-weight: 800;">
                                {{ $require_cnic_upload ? 'ENFORCED' : 'WAIVED' }}
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
                            <input type="checkbox" wire:model.live="require_cnic_upload" style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $require_cnic_upload ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $require_cnic_upload ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                                <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $require_cnic_upload ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%;"></span>
                            </span>
                        </label>
                    </div>
                </div>

                <!-- 4. Employer NOC -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.15rem; background: var(--bg-surface); border: 1px solid {{ $require_noc_employed ? 'var(--primary-glow)' : 'var(--border-color)' }}; border-radius: var(--radius-md); transition: var(--transition-fast);">
                    <div style="max-width: 80%;">
                        <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem; flex-wrap: wrap;">
                            <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                                Employer NOC (No Objection Certificate)
                            </span>
                            <span class="badge {{ $require_noc_employed ? 'badge-emerald' : 'badge-slate' }}" style="font-size: 0.65rem; font-weight: 800;">
                                {{ $require_noc_employed ? 'ENFORCED' : 'WAIVED' }}
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
                            <input type="checkbox" wire:model.live="require_noc_employed" style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $require_noc_employed ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $require_noc_employed ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                                <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $require_noc_employed ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%;"></span>
                            </span>
                        </label>
                    </div>
                </div>

            </div>
        </div>

        <!-- Category 3: Experience & Financial Settlements -->
        <div style="margin-bottom: 1.75rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                <span style="font-size: 1.15rem;">💼</span>
                <h4 style="margin: 0; font-size: 0.95rem; font-weight: 800; color: var(--color-text-primary);">
                    Service Experience & Fee Settlement Receipts
                </h4>
                <span class="badge badge-slate" style="font-size: 0.65rem;">SETTLEMENT & SERVICE</span>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1rem;">
                
                <!-- 5. Experience Certificate Proof -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.15rem; background: var(--bg-surface); border: 1px solid {{ $require_experience_proof ? 'var(--primary-glow)' : 'var(--border-color)' }}; border-radius: var(--radius-md); transition: var(--transition-fast);">
                    <div style="max-width: 80%;">
                        <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem; flex-wrap: wrap;">
                            <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                                Professional Experience & Service Certificates
                            </span>
                            <span class="badge {{ $require_experience_proof ? 'badge-emerald' : 'badge-slate' }}" style="font-size: 0.65rem; font-weight: 800;">
                                {{ $require_experience_proof ? 'ENFORCED' : 'WAIVED' }}
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
                            <input type="checkbox" wire:model.live="require_experience_proof" style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $require_experience_proof ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $require_experience_proof ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                                <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $require_experience_proof ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%;"></span>
                            </span>
                        </label>
                    </div>
                </div>

                <!-- 6. Bank Challan Deposit Slip Scan -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.15rem; background: var(--bg-surface); border: 1px solid {{ $require_challan_receipt_upload ? 'var(--primary-glow)' : 'var(--border-color)' }}; border-radius: var(--radius-md); transition: var(--transition-fast);">
                    <div style="max-width: 80%;">
                        <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem; flex-wrap: wrap;">
                            <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                                Bank Challan Stamped Deposit Receipt Scan
                            </span>
                            <span class="badge {{ $require_challan_receipt_upload ? 'badge-emerald' : 'badge-slate' }}" style="font-size: 0.65rem; font-weight: 800;">
                                {{ $require_challan_receipt_upload ? 'ENFORCED' : 'WAIVED' }}
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
                            <input type="checkbox" wire:model.live="require_challan_receipt_upload" style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $require_challan_receipt_upload ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $require_challan_receipt_upload ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                                <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $require_challan_receipt_upload ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%;"></span>
                            </span>
                        </label>
                    </div>
                </div>

            </div>
        </div>

        <div style="margin-top: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
            @can('manage-settings')
            <button type="button" 
                    wire:click="resetDocumentsToDefault" 
                    wire:confirm="Reset all document verification rules to institutional defaults (transcripts, CNIC, and Challan mandatory)?"
                    class="btn btn-secondary btn-sm" 
                    style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/></svg>
                <span>Reset Document Rules</span>
            </button>

            <button type="submit" 
                    class="btn btn-primary" 
                    style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.75rem; font-weight: 800; font-size: 0.92rem; box-shadow: 0 4px 14px var(--primary-glow);">
                <span wire:loading.remove wire:target="saveDocumentSettings">Save Document Verification Rules</span>
                <span wire:loading wire:target="saveDocumentSettings">Saving Document Rules...</span>
            </button>
            @endcan
        </div>
    </form>
</div>
