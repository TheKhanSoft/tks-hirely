<div>
    <!-- Console Control Header -->
    <div class="card mb-6" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; position: relative; overflow: hidden;">
        <div style="position: absolute; right: -2rem; top: -2rem; width: 140px; height: 140px; background: radial-gradient(circle, var(--primary-glow) 0%, transparent 70%); pointer-events: none;"></div>
        <div>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.35rem;">
                <div class="brand-icon-box" style="width: 38px; height: 38px; border-radius: 10px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="card-title" style="margin: 0; font-size: 1.35rem;">System & Workflow Settings</h2>
                    <p class="card-subtitle" style="margin: 0;">Configure mandatory candidate document requests, workflow pipeline toggles, candidate profile rules, and organization profile</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session()->has('message'))
        <div class="alert alert-success mb-6" style="display: flex; align-items: center; gap: 0.75rem; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3); color: var(--success); padding: 0.85rem 1.25rem; border-radius: var(--radius-md);">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <span style="font-weight: 600;">{{ session('message') }}</span>
        </div>
    @endif

    <!-- Settings Navigation Tabs -->
    <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; flex-wrap: wrap;">
        <button type="button" 
                wire:click="setTab('pipeline')" 
                class="btn {{ $activeTab === 'pipeline' ? 'btn-primary' : 'btn-secondary' }}" 
                style="display: inline-flex; align-items: center; gap: 0.45rem; font-size: 0.88rem; padding: 0.55rem 1rem;">
            <span>⚡ Workflow Pipeline Stages</span>
        </button>

        <button type="button" 
                wire:click="setTab('documents')" 
                class="btn {{ $activeTab === 'documents' ? 'btn-primary' : 'btn-secondary' }}" 
                style="display: inline-flex; align-items: center; gap: 0.45rem; font-size: 0.88rem; padding: 0.55rem 1rem;">
            <span>📑 Document Requests & Proofs</span>
        </button>

        <button type="button" 
                wire:click="setTab('candidate_rules')" 
                class="btn {{ $activeTab === 'candidate_rules' ? 'btn-primary' : 'btn-secondary' }}" 
                style="display: inline-flex; align-items: center; gap: 0.45rem; font-size: 0.88rem; padding: 0.55rem 1rem;">
            <span>👥 Candidate & Application Rules</span>
        </button>

        <button type="button" 
                wire:click="setTab('organization')" 
                class="btn {{ $activeTab === 'organization' ? 'btn-primary' : 'btn-secondary' }}" 
                style="display: inline-flex; align-items: center; gap: 0.45rem; font-size: 0.88rem; padding: 0.55rem 1rem;">
            <span>🏢 Organization & Sector Profile</span>
        </button>

        <button type="button" 
                wire:click="setTab('academic_criteria')" 
                class="btn {{ $activeTab === 'academic_criteria' ? 'btn-primary' : 'btn-secondary' }}" 
                style="display: inline-flex; align-items: center; gap: 0.45rem; font-size: 0.88rem; padding: 0.55rem 1rem;">
            <span>🎓 Academic Division & Marks Benchmarks</span>
        </button>
    </div>

    <!-- ============================================================== -->
    <!-- TAB 1: WORKFLOW PIPELINE STAGES -->
    <!-- ============================================================== -->
    @if($activeTab === 'pipeline')
        <div class="card mb-6">
            <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                <h3 class="card-title" style="margin: 0 0 0.25rem 0; font-size: 1.15rem;">Workflow Pipeline Stages & Milestone Control</h3>
                <p class="card-subtitle" style="margin: 0;">Toggle major recruitment milestones on or off according to institutional governance policies</p>
            </div>

            <form wire:submit.prevent="savePipelineSettings">
                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <!-- Peer Review Toggle -->
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        <div style="max-width: 80%;">
                            <div style="font-size: 0.95rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.25rem;">
                                External Subject & Academic Peer Review Stage
                            </div>
                            <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.4;">
                                Enables sending candidate research dossiers to external empanelled experts for independent blind peer evaluation and quantitative scoring.
                            </div>
                        </div>
                        <div>
                            <label style="position: relative; display: inline-block; width: 50px; height: 26px; cursor: pointer;">
                                <input type="checkbox" wire:model="enable_peer_review_step" style="opacity: 0; width: 0; height: 0;">
                                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: {{ $enable_peer_review_step ? 'var(--primary)' : '#4b5563' }}; transition: .3s; border-radius: 34px;">
                                    <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $enable_peer_review_step ? '28px' : '4px' }}; bottom: 4px; background-color: white; transition: .3s; border-radius: 50%;"></span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Scrutiny Verification Toggle -->
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        <div style="max-width: 80%;">
                            <div style="font-size: 0.95rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.25rem;">
                                Preliminary Scrutiny & Document Verification Desk
                            </div>
                            <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.4;">
                                Requires the Scrutiny Committee to audit applicant degrees, transcripts, and mark candidates as Eligible or Ineligible with disqualification reasons.
                            </div>
                        </div>
                        <div>
                            <label style="position: relative; display: inline-block; width: 50px; height: 26px; cursor: pointer;">
                                <input type="checkbox" wire:model="enable_scrutiny_step" style="opacity: 0; width: 0; height: 0;">
                                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: {{ $enable_scrutiny_step ? 'var(--primary)' : '#4b5563' }}; transition: .3s; border-radius: 34px;">
                                    <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $enable_scrutiny_step ? '28px' : '4px' }}; bottom: 4px; background-color: white; transition: .3s; border-radius: 50%;"></span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Appellate Redressal Toggle -->
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        <div style="max-width: 80%;">
                            <div style="font-size: 0.95rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.25rem;">
                                Candidate Grievance & Appellate Review Mechanism
                            </div>
                            <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.4;">
                                Provides a statutory window allowing rejected candidates to file grievance appeals reviewed by an independent Appellate Committee.
                            </div>
                        </div>
                        <div>
                            <label style="position: relative; display: inline-block; width: 50px; height: 26px; cursor: pointer;">
                                <input type="checkbox" wire:model="enable_appellate_step" style="opacity: 0; width: 0; height: 0;">
                                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: {{ $enable_appellate_step ? 'var(--primary)' : '#4b5563' }}; transition: .3s; border-radius: 34px;">
                                    <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $enable_appellate_step ? '28px' : '4px' }}; bottom: 4px; background-color: white; transition: .3s; border-radius: 50%;"></span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Live Board Scoring Toggle -->
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        <div style="max-width: 80%;">
                            <div style="font-size: 0.95rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.25rem;">
                                Live Interview & Selection Board Synchronous Scoring Panel
                            </div>
                            <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.4;">
                                Enables real-time digital grading by individual Selection Board members during in-person or virtual interview sessions.
                            </div>
                        </div>
                        <div>
                            <label style="position: relative; display: inline-block; width: 50px; height: 26px; cursor: pointer;">
                                <input type="checkbox" wire:model="enable_board_interview_step" style="opacity: 0; width: 0; height: 0;">
                                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: {{ $enable_board_interview_step ? 'var(--primary)' : '#4b5563' }}; transition: .3s; border-radius: 34px;">
                                    <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $enable_board_interview_step ? '28px' : '4px' }}; bottom: 4px; background-color: white; transition: .3s; border-radius: 50%;"></span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Quorum Enforcements -->
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        <div style="max-width: 80%;">
                            <div style="font-size: 0.95rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.25rem;">
                                Strict Committee & Board Quorum Validation
                            </div>
                            <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.4;">
                                Strictly blocks proceeding if appointed board members do not match the exact statutory constitution requirements (Internal, External, International).
                            </div>
                        </div>
                        <div>
                            <label style="position: relative; display: inline-block; width: 50px; height: 26px; cursor: pointer;">
                                <input type="checkbox" wire:model="enforce_strict_committee_quorum" style="opacity: 0; width: 0; height: 0;">
                                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: {{ $enforce_strict_committee_quorum ? 'var(--primary)' : '#4b5563' }}; transition: .3s; border-radius: 34px;">
                                    <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $enforce_strict_committee_quorum ? '28px' : '4px' }}; bottom: 4px; background-color: white; transition: .3s; border-radius: 50%;"></span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Strict Peer Review Quorum -->
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        <div style="max-width: 80%;">
                            <div style="font-size: 0.95rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.25rem;">
                                Strict External Peer Review Quota Enforcement
                            </div>
                            <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.4;">
                                Requires the exact statutory number of external experts (e.g. 3 experts for Assistant Professor) to be selected simultaneously before dispatching.
                            </div>
                        </div>
                        <div>
                            <label style="position: relative; display: inline-block; width: 50px; height: 26px; cursor: pointer;">
                                <input type="checkbox" wire:model="enforce_strict_peer_review_quorum" style="opacity: 0; width: 0; height: 0;">
                                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: {{ $enforce_strict_peer_review_quorum ? 'var(--primary)' : '#4b5563' }}; transition: .3s; border-radius: 34px;">
                                    <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $enforce_strict_peer_review_quorum ? '28px' : '4px' }}; bottom: 4px; background-color: white; transition: .3s; border-radius: 50%;"></span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 mt-6" style="border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                    <button type="submit" class="btn btn-primary">Save Pipeline Stage Settings</button>
                </div>
            </form>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- TAB 2: DOCUMENT REQUESTS & CANDIDATE PROOFS -->
    <!-- ============================================================== -->
    @if($activeTab === 'documents')
        <div class="card mb-6">
            <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                <h3 class="card-title" style="margin: 0 0 0.25rem 0; font-size: 1.15rem;">Candidate Document Requests & Mandatory Uploads</h3>
                <p class="card-subtitle" style="margin: 0;">Specify which evidentiary proofs candidates are strictly required to upload during application submission</p>
            </div>

            <form wire:submit.prevent="saveDocumentSettings">
                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <!-- Transcript Proof Toggle -->
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        <div style="max-width: 80%;">
                            <div style="font-size: 0.95rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.25rem;">
                                Official Degree Transcripts / Detailed Marks Certificates (DMC)
                            </div>
                            <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.4;">
                                If enabled, candidates cannot submit without uploading scanned transcript proofs for each entered academic qualification.
                            </div>
                        </div>
                        <div>
                            <label style="position: relative; display: inline-block; width: 50px; height: 26px; cursor: pointer;">
                                <input type="checkbox" wire:model="require_transcript_proof" style="opacity: 0; width: 0; height: 0;">
                                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: {{ $require_transcript_proof ? 'var(--primary)' : '#4b5563' }}; transition: .3s; border-radius: 34px;">
                                    <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $require_transcript_proof ? '28px' : '4px' }}; bottom: 4px; background-color: white; transition: .3s; border-radius: 50%;"></span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Experience Proof Toggle -->
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        <div style="max-width: 80%;">
                            <div style="font-size: 0.95rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.25rem;">
                                Employment Service & Experience Certificates
                            </div>
                            <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.4;">
                                Requires applicants claiming professional or teaching experience to attach scanned experience certificates or appointment orders.
                            </div>
                        </div>
                        <div>
                            <label style="position: relative; display: inline-block; width: 50px; height: 26px; cursor: pointer;">
                                <input type="checkbox" wire:model="require_experience_proof" style="opacity: 0; width: 0; height: 0;">
                                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: {{ $require_experience_proof ? 'var(--primary)' : '#4b5563' }}; transition: .3s; border-radius: 34px;">
                                    <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $require_experience_proof ? '28px' : '4px' }}; bottom: 4px; background-color: white; transition: .3s; border-radius: 50%;"></span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- CNIC / National ID -->
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        <div style="max-width: 80%;">
                            <div style="font-size: 0.95rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.25rem;">
                                National Identity Card (CNIC / Passport) Verification Upload
                            </div>
                            <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.4;">
                                Requires applicants to upload front/back image of their CNIC or biographical passport page during initial profile registration.
                            </div>
                        </div>
                        <div>
                            <label style="position: relative; display: inline-block; width: 50px; height: 26px; cursor: pointer;">
                                <input type="checkbox" wire:model="require_cnic_upload" style="opacity: 0; width: 0; height: 0;">
                                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: {{ $require_cnic_upload ? 'var(--primary)' : '#4b5563' }}; transition: .3s; border-radius: 34px;">
                                    <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $require_cnic_upload ? '28px' : '4px' }}; bottom: 4px; background-color: white; transition: .3s; border-radius: 50%;"></span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Employer NOC -->
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        <div style="max-width: 80%;">
                            <div style="font-size: 0.95rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.25rem;">
                                Employer No Objection Certificate (NOC) for Currently Employed Candidates
                            </div>
                            <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.4;">
                                Requires government, autonomous, or private sector employees to attach a valid departmental NOC.
                            </div>
                        </div>
                        <div>
                            <label style="position: relative; display: inline-block; width: 50px; height: 26px; cursor: pointer;">
                                <input type="checkbox" wire:model="require_noc_employed" style="opacity: 0; width: 0; height: 0;">
                                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: {{ $require_noc_employed ? 'var(--primary)' : '#4b5563' }}; transition: .3s; border-radius: 34px;">
                                    <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $require_noc_employed ? '28px' : '4px' }}; bottom: 4px; background-color: white; transition: .3s; border-radius: 50%;"></span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Research Publications Dossier -->
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        <div style="max-width: 80%;">
                            <div style="font-size: 0.95rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.25rem;">
                                Research Publications & DOI Proofs for Academic / Research Cadres
                            </div>
                            <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.4;">
                                Enforces publication entry and PDF/DOI proof attachment for academic and scientific researcher positions.
                            </div>
                        </div>
                        <div>
                            <label style="position: relative; display: inline-block; width: 50px; height: 26px; cursor: pointer;">
                                <input type="checkbox" wire:model="require_publications_academic" style="opacity: 0; width: 0; height: 0;">
                                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: {{ $require_publications_academic ? 'var(--primary)' : '#4b5563' }}; transition: .3s; border-radius: 34px;">
                                    <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $require_publications_academic ? '28px' : '4px' }}; bottom: 4px; background-color: white; transition: .3s; border-radius: 50%;"></span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Challan Receipt Upload -->
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        <div style="max-width: 80%;">
                            <div style="font-size: 0.95rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.25rem;">
                                Scanned Bank-Stamped Challan Receipt Upload
                            </div>
                            <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.4;">
                                Requires candidates who pay via manual bank deposit counters to upload a scanned bank-stamped receipt.
                            </div>
                        </div>
                        <div>
                            <label style="position: relative; display: inline-block; width: 50px; height: 26px; cursor: pointer;">
                                <input type="checkbox" wire:model="require_challan_receipt_upload" style="opacity: 0; width: 0; height: 0;">
                                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: {{ $require_challan_receipt_upload ? 'var(--primary)' : '#4b5563' }}; transition: .3s; border-radius: 34px;">
                                    <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $require_challan_receipt_upload ? '28px' : '4px' }}; bottom: 4px; background-color: white; transition: .3s; border-radius: 50%;"></span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 mt-6" style="border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                    <button type="submit" class="btn btn-primary">Save Document Rules</button>
                </div>
            </form>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- TAB 3: CANDIDATE & APPLICATION RULES -->
    <!-- ============================================================== -->
    @if($activeTab === 'candidate_rules')
        <div class="card mb-6">
            <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                <h3 class="card-title" style="margin: 0 0 0.25rem 0; font-size: 1.15rem;">Candidate Application & Multi-Post Applying Governance</h3>
                <p class="card-subtitle" style="margin: 0;">Control duplicate application limits, fee voucher settlement grace days, and profile lock timings</p>
            </div>

            <form wire:submit.prevent="saveCandidateRules">
                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <!-- Allow Multiple Applications Toggle -->
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        <div style="max-width: 80%;">
                            <div style="font-size: 0.95rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.25rem;">
                                Allow Candidates to Apply for Multiple Distinct Positions
                            </div>
                            <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.4;">
                                Allows candidates to submit separate applications for different campaigns (e.g. apply for both Assistant Professor and Research Fellow).
                            </div>
                        </div>
                        <div>
                            <label style="position: relative; display: inline-block; width: 50px; height: 26px; cursor: pointer;">
                                <input type="checkbox" wire:model="allow_multiple_applications" style="opacity: 0; width: 0; height: 0;">
                                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: {{ $allow_multiple_applications ? 'var(--primary)' : '#4b5563' }}; transition: .3s; border-radius: 34px;">
                                    <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $allow_multiple_applications ? '28px' : '4px' }}; bottom: 4px; background-color: white; transition: .3s; border-radius: 50%;"></span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Strictly Prevent Duplicate Applications for Same Campaign -->
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        <div style="max-width: 80%;">
                            <div style="font-size: 0.95rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.25rem;">
                                Strictly Prevent Duplicate Applications for the Same Position
                            </div>
                            <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.4;">
                                Blocks a candidate from submitting more than one application to the exact same recruitment campaign.
                            </div>
                        </div>
                        <div>
                            <label style="position: relative; display: inline-block; width: 50px; height: 26px; cursor: pointer;">
                                <input type="checkbox" wire:model="prevent_duplicate_campaign_applications" style="opacity: 0; width: 0; height: 0;">
                                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: {{ $prevent_duplicate_campaign_applications ? 'var(--primary)' : '#4b5563' }}; transition: .3s; border-radius: 34px;">
                                    <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $prevent_duplicate_campaign_applications ? '28px' : '4px' }}; bottom: 4px; background-color: white; transition: .3s; border-radius: 50%;"></span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Auto-Lock Profile Upon Voucher -->
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        <div style="max-width: 80%;">
                            <div style="font-size: 0.95rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.25rem;">
                                Auto-Lock Candidate Profile Upon Fee Voucher Issuance
                            </div>
                            <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.4;">
                                Locks candidate demographics (name, CNIC, date of birth) to prevent post-voucher alterations.
                            </div>
                        </div>
                        <div>
                            <label style="position: relative; display: inline-block; width: 50px; height: 26px; cursor: pointer;">
                                <input type="checkbox" wire:model="auto_lock_profile_on_voucher" style="opacity: 0; width: 0; height: 0;">
                                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: {{ $auto_lock_profile_on_voucher ? 'var(--primary)' : '#4b5563' }}; transition: .3s; border-radius: 34px;">
                                    <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $auto_lock_profile_on_voucher ? '28px' : '4px' }}; bottom: 4px; background-color: white; transition: .3s; border-radius: 50%;"></span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Grace Days -->
                    <div class="grid grid-cols-2 gap-4 mt-2">
                        <x-input label="Challan / Voucher Payment Grace Days *" :error="$errors->first('challan_grace_days')">
                            <input type="number" min="1" max="90" class="form-control" wire:model="challan_grace_days">
                        </x-input>
                    </div>
                </div>

                <div class="flex gap-4 mt-6" style="border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                    <button type="submit" class="btn btn-primary">Save Candidate & Application Rules</button>
                </div>
            </form>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- TAB 5: ORGANIZATION & SECTOR PROFILE -->
    <!-- ============================================================== -->
    @if($activeTab === 'organization')
        <div class="card mb-6">
            <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                <h3 class="card-title" style="margin: 0 0 0.25rem 0; font-size: 1.15rem;">Organization Profile & Sector Generalization</h3>
                <p class="card-subtitle" style="margin: 0;">Hirely is sector-agnostic: Configure whether the hiring entity is a University, Corporate Enterprise, Government Agency, or Research Institute</p>
            </div>

            <form wire:submit.prevent="saveOrganizationProfile">
                <div class="grid grid-cols-2 gap-4">
                    <x-input label="Organization / Hiring Entity Name *" :error="$errors->first('org_name')">
                        <input type="text" class="form-control" wire:model="org_name" placeholder="e.g. National Institute of Technology or Apex Global Tech">
                    </x-input>

                    <x-select label="Organization Sector / Entity Type *" :error="$errors->first('org_type')">
                        <select class="form-control form-select" wire:model="org_type">
                            <option value="HIGHER_EDUCATION">Higher Education / University / College</option>
                            <option value="CORPORATE_ENTERPRISE">Corporate Enterprise / Private Company</option>
                            <option value="PUBLIC_SECTOR_GOVT">Public Sector / Government Commission / Civil Service</option>
                            <option value="RESEARCH_INSTITUTE">Research & Development Institute / Think Tank</option>
                            <option value="HEALTHCARE_HOSPITAL">Healthcare Network / Hospital / Medical Center</option>
                        </select>
                    </x-select>
                </div>

                <div class="grid grid-cols-3 gap-4 mt-4">
                    <x-input label="Administrative Unit Label *" :error="$errors->first('sector_label')">
                        <input type="text" class="form-control" wire:model="sector_label" placeholder="e.g. Department / Faculty or Division / Business Unit">
                    </x-input>

                    <x-input label="Voucher Currency Symbol *" :error="$errors->first('currency_symbol')">
                        <input type="text" class="form-control" wire:model="currency_symbol" placeholder="PKR">
                    </x-input>

                    <x-input label="Default Campaign Fee (Currency Units) *" :error="$errors->first('default_application_fee')">
                        <input type="number" min="0" class="form-control" wire:model="default_application_fee">
                    </x-input>
                </div>

                <div class="flex gap-4 mt-6" style="border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                    <button type="submit" class="btn btn-primary">Save Organization Profile</button>
                </div>
            </form>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- TAB 5: GLOBAL ACADEMIC DIVISION & MARKS BENCHMARKS            -->
    <!-- ============================================================== -->
    @if($activeTab === 'academic_criteria')
        <div class="card mb-6">
            <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                <h3 class="card-title" style="margin: 0 0 0.25rem 0; font-size: 1.15rem; display: flex; align-items: center; gap: 0.5rem;">
                    <span>🎓 Global Academic Division & Marks Benchmarks</span>
                    <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; font-size: 0.74rem;">ALL POSITIONS DEFAULT</span>
                </h3>
                <p class="card-subtitle" style="margin: 0;">Configure statutory qualification criteria applied across all positions unless overridden by a specific campaign: 3rd division prohibitions, 2nd division allowances, and marks cutoffs ("xx out of nn marks not allowed").</p>
            </div>

            <form wire:submit.prevent="saveAcademicCriteriaSettings">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- 3rd Division Rules -->
                    <div class="card" style="background: var(--bg-surface); border-color: var(--border-color); padding: 1.25rem;">
                        <h4 style="font-size: 0.92rem; font-weight: 800; color: var(--secondary); text-transform: uppercase; margin: 0 0 0.85rem 0; display: flex; align-items: center; gap: 0.4rem;">
                            <span>Third (3rd) Division Policies</span>
                        </h4>

                        <div class="flex-col gap-3">
                            <label style="display: flex; align-items: center; gap: 0.65rem; cursor: pointer; color: var(--color-text-primary); font-size: 0.85rem;">
                                <input type="checkbox" wire:model="global_disallow_third_division" style="width: 1.15rem; height: 1.15rem; accent-color: #ef4444;">
                                <div>
                                    <strong style="color: var(--color-text-primary);">3rd Division strictly prohibited across all qualifications</strong>
                                    <p style="margin: 0.15rem 0 0 0; font-size: 0.76rem; color: var(--color-text-muted);">
                                        Candidates with any 3rd division in Matric, Inter, Bachelors, or Masters will be flagged as Ineligible.
                                    </p>
                                </div>
                            </label>

                            <div style="margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px dashed var(--border-color);">
                                <label style="font-size: 0.78rem; font-weight: 700; color: var(--color-text-secondary);">
                                    Or Maximum Permitted 3rd Divisions (e.g. 1 allowed in career):
                                </label>
                                <input type="number" min="0" max="5" class="form-control mt-1" wire:model="global_max_allowed_third_divisions" placeholder="e.g. 1 (leaves blank if completely unrestricted)" style="max-width: 220px;">
                                <small style="display: block; margin-top: 0.25rem; font-size: 0.72rem; color: var(--color-text-muted);">
                                    Allows e.g. 1 third division in early schooling provided terminal degrees meet division criteria.
                                </small>
                            </div>

                            <label style="display: flex; align-items: center; gap: 0.65rem; cursor: pointer; color: var(--color-text-primary); font-size: 0.85rem; margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px dashed var(--border-color);">
                                <input type="checkbox" wire:model="global_disallow_third_in_terminal" style="width: 1.15rem; height: 1.15rem; accent-color: #ef4444;">
                                <div>
                                    <strong style="color: var(--color-text-primary);">No 3rd Division in Terminal Degree</strong>
                                    <p style="margin: 0.15rem 0 0 0; font-size: 0.76rem; color: var(--color-text-muted);">
                                        The highest qualification (PhD / Masters / BS) must not be a third division under any circumstances.
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- 2nd Division Rules -->
                    <div class="card" style="background: var(--bg-surface); border-color: var(--border-color); padding: 1.25rem;">
                        <h4 style="font-size: 0.92rem; font-weight: 800; color: var(--primary); text-transform: uppercase; margin: 0 0 0.85rem 0; display: flex; align-items: center; gap: 0.4rem;">
                            <span>Second (2nd) Division Policies</span>
                        </h4>

                        <div class="flex-col gap-3">
                            <label style="display: flex; align-items: center; gap: 0.65rem; cursor: pointer; color: var(--color-text-primary); font-size: 0.85rem;">
                                <input type="checkbox" wire:model="global_disallow_two_second_divisions" style="width: 1.15rem; height: 1.15rem; accent-color: #f59e0b;">
                                <div>
                                    <strong style="color: var(--color-text-primary);">Two or more 2nd Divisions NOT allowed (Max 1 permitted)</strong>
                                    <p style="margin: 0.15rem 0 0 0; font-size: 0.76rem; color: var(--color-text-muted);">
                                        Statutory requirement: Candidate cannot possess 2 or more second divisions in entire academic record.
                                    </p>
                                </div>
                            </label>

                            <div style="margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px dashed var(--border-color);">
                                <label style="font-size: 0.78rem; font-weight: 700; color: var(--color-text-secondary);">
                                    Or Maximum Permitted 2nd Divisions:
                                </label>
                                <input type="number" min="0" max="5" class="form-control mt-1" wire:model="global_max_allowed_second_divisions" placeholder="e.g. 1" style="max-width: 220px;">
                                <small style="display: block; margin-top: 0.25rem; font-size: 0.72rem; color: var(--color-text-muted);">
                                    Sets specific cap on 2nd divisions.
                                </small>
                            </div>

                            <label style="display: flex; align-items: center; gap: 0.65rem; cursor: pointer; color: var(--color-text-primary); font-size: 0.85rem; margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px dashed var(--border-color);">
                                <input type="checkbox" wire:model="global_disallow_second_in_terminal" style="width: 1.15rem; height: 1.15rem; accent-color: #f59e0b;">
                                <div>
                                    <strong style="color: var(--color-text-primary);">No 2nd Division in Terminal Degree</strong>
                                    <p style="margin: 0.15rem 0 0 0; font-size: 0.76rem; color: var(--color-text-muted);">
                                        Terminal degree must be First Division or minimum equivalent CGPA (e.g. 3.0 / 4.0).
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Marks Cutoffs ("xx out of nn marks not allowed") -->
                    <div class="card" style="grid-column: 1 / -1; background: var(--bg-surface); border-color: var(--border-color); padding: 1.25rem;">
                        <h4 style="font-size: 0.92rem; font-weight: 800; color: #f59e0b; text-transform: uppercase; margin: 0 0 0.85rem 0; display: flex; align-items: center; gap: 0.4rem;">
                            <span>Minimum Marks / Score Cutoffs ("xx out of nn marks not allowed")</span>
                        </h4>

                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label style="font-size: 0.78rem; font-weight: 700; color: var(--color-text-secondary);">
                                    Minimum Percentage Across All Degrees (%)
                                </label>
                                <input type="number" step="0.1" class="form-control mt-1" wire:model="global_min_marks_percentage" placeholder="e.g. 60.0 (less than 60% disqualified)">
                                <small style="display: block; margin-top: 0.25rem; font-size: 0.72rem; color: var(--color-text-muted);">
                                    Disqualifies any degree scoring below this percentage threshold.
                                </small>
                            </div>

                            <div>
                                <label style="font-size: 0.78rem; font-weight: 700; color: var(--color-text-secondary);">
                                    Minimum Terminal Degree Percentage (%)
                                </label>
                                <input type="number" step="0.1" class="form-control mt-1" wire:model="global_min_terminal_percentage" placeholder="e.g. 60.0">
                                <small style="display: block; margin-top: 0.25rem; font-size: 0.72rem; color: var(--color-text-muted);">
                                    Applies to the highest degree in candidate profile.
                                </small>
                            </div>

                            <div>
                                <label style="font-size: 0.78rem; font-weight: 700; color: var(--color-text-secondary);">
                                    Minimum Terminal CGPA (on 4.0 Scale)
                                </label>
                                <input type="number" step="0.05" class="form-control mt-1" wire:model="global_min_terminal_cgpa" placeholder="e.g. 3.0">
                                <small style="display: block; margin-top: 0.25rem; font-size: 0.72rem; color: var(--color-text-muted);">
                                    Minimum CGPA for semester system degrees.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 mt-6" style="border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                    <button type="submit" class="btn btn-primary">Save Global Academic Benchmarks</button>
                </div>
            </form>
        </div>
    @endif
</div>
