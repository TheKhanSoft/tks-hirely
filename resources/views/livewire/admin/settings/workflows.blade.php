<div class="card mb-6" style="padding: 1.75rem; border-color: var(--primary); box-shadow: var(--shadow-md);">
    <!-- Card Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                <span class="badge badge-indigo" style="font-size: 0.72rem; font-weight: 800;">STATUTORY ENGINE</span>
                <span class="badge badge-slate" style="font-size: 0.72rem;">PIPELINE GATING</span>
            </div>
            <h2 class="card-title" style="margin: 0; font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary);">
                Workflow Pipeline Stages & Milestone Control
            </h2>
            <p class="card-subtitle" style="margin: 0.25rem 0 0 0; font-size: 0.85rem; color: var(--color-text-secondary);">
                Enable or bypass recruitment evaluation milestones according to institutional governance policies and statutory acts.
            </p>
        </div>

        <button type="button" 
                wire:click="savePipelineSettings" 
                class="btn btn-primary" 
                style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.4rem; font-weight: 800; font-size: 0.88rem; box-shadow: 0 4px 14px var(--primary-glow);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                <polyline points="17 21 17 13 7 13 7 21"/>
                <polyline points="7 3 7 8 15 8"/>
            </svg>
            <span wire:loading.remove wire:target="savePipelineSettings">Save Pipeline Settings</span>
            <span wire:loading wire:target="savePipelineSettings">Saving...</span>
        </button>
    </div>

    {{-- System Help Callout Banner --}}
    <x-help-hint type="callout" variant="info" title="Recruitment Flow Lifecycle Logic">
        These settings govern the mandatory sequence of candidate progression. Turning off a stage removes that gating requirement system-wide, permitting applications to automatically route to the subsequent statutory body without holding for committee clearance.
    </x-help-hint>

    <form wire:submit.prevent="savePipelineSettings" style="margin-top: 1.5rem;">
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            
            <!-- 1. Scrutiny Committee Verification Milestone -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); transition: var(--transition-fast);">
                <div style="max-width: 80%;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                        <span style="font-size: 1.1rem;">📑</span>
                        <span style="font-size: 0.98rem; font-weight: 800; color: var(--color-text-primary);">
                            Scrutiny Committee Dossier Vetting Stage
                        </span>
                        <x-help-hint type="inline" 
                                     variant="statutory"
                                     statute="Statutory Act Sec. 12"
                                     title="Scrutiny Committee Vetting"
                                     text="Formal preliminary inspection of educational credentials, equivalence certificates, and job prerequisites. If disabled, applications directly advance to external peer review or selection board." />
                    </div>
                    <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.5;">
                        Mandates formal committee screening to verify minimum degree division, age limits, and required experience before candidate dossiers are deemed eligible.
                    </div>
                </div>
                <div>
                    <label style="position: relative; display: inline-block; width: 52px; height: 28px; cursor: pointer;">
                        <input type="checkbox" wire:model="enable_scrutiny_step" style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $enable_scrutiny_step ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $enable_scrutiny_step ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                            <span style="position: absolute; content: ''; height: 20px; width: 20px; left: {{ $enable_scrutiny_step ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- 2. Academic Peer Review Milestone -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); transition: var(--transition-fast);">
                <div style="max-width: 80%;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                        <span style="font-size: 1.1rem;">🔬</span>
                        <span style="font-size: 0.98rem; font-weight: 800; color: var(--color-text-primary);">
                            External Subject & Academic Peer Review Stage
                        </span>
                        <x-help-hint type="inline" 
                                     variant="statutory"
                                     statute="HEC Faculty Criteria 2024"
                                     title="Blind Referee Scoring"
                                     text="Enables confidential dispatch of research papers and academic portfolios to external empanelled referees for blind evaluation."
                                     impact="Required for Assistant Professor, Associate Professor, and Professor tracks." />
                    </div>
                    <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.5;">
                        Enables sending candidate research dossiers to external empanelled experts for independent blind peer evaluation and quantitative scoring.
                    </div>
                </div>
                <div>
                    <label style="position: relative; display: inline-block; width: 52px; height: 28px; cursor: pointer;">
                        <input type="checkbox" wire:model="enable_peer_review_step" style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $enable_peer_review_step ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $enable_peer_review_step ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                            <span style="position: absolute; content: ''; height: 20px; width: 20px; left: {{ $enable_peer_review_step ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- 3. Selection Board Milestone -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); transition: var(--transition-fast);">
                <div style="max-width: 80%;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                        <span style="font-size: 1.1rem;">👥</span>
                        <span style="font-size: 0.98rem; font-weight: 800; color: var(--color-text-primary);">
                            Statutory Selection Board Interview & Viva Stage
                        </span>
                        <x-help-hint type="inline" 
                                     variant="info"
                                     title="Live Board Scoring"
                                     text="Enables the high-stakes board interview panel where internal leaders and external subject experts record quantitative interview marks." />
                    </div>
                    <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.5;">
                        Controls candidate routing to formal interview panels with dynamic scorecards, real-time rubric grading, and unanimous merit list generation.
                    </div>
                </div>
                <div>
                    <label style="position: relative; display: inline-block; width: 52px; height: 28px; cursor: pointer;">
                        <input type="checkbox" wire:model="enable_board_interview_step" style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $enable_board_interview_step ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $enable_board_interview_step ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                            <span style="position: absolute; content: ''; height: 20px; width: 20px; left: {{ $enable_board_interview_step ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- 4. Appellate Committee Milestone -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); transition: var(--transition-fast);">
                <div style="max-width: 80%;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                        <span style="font-size: 1.1rem;">⚖️</span>
                        <span style="font-size: 0.98rem; font-weight: 800; color: var(--color-text-primary);">
                            Appellate Grievance & Representation Tribunal
                        </span>
                        <x-help-hint type="inline" 
                                     variant="statutory"
                                     statute="Appellate Statute 2021"
                                     title="Candidate Legal Remedy"
                                     text="Allows rejected candidates to file formal statutory appeals within 10 days of scrutiny publication. Appeals are reviewed by a separate judicial panel." />
                    </div>
                    <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.5;">
                        Provides a legal grievance mechanism allowing rejected applicants to submit clarification representations to an independent appellate body.
                    </div>
                </div>
                <div>
                    <label style="position: relative; display: inline-block; width: 52px; height: 28px; cursor: pointer;">
                        <input type="checkbox" wire:model="enable_appellate_step" style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $enable_appellate_step ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $enable_appellate_step ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                            <span style="position: absolute; content: ''; height: 20px; width: 20px; left: {{ $enable_appellate_step ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- Section Divider: Quorum Enforcements -->
            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                    <h4 style="margin: 0; font-size: 1rem; font-weight: 800; color: var(--color-text-primary);">
                        Statutory Quorum & Gating Enforcements
                    </h4>
                    <span class="badge badge-amber" style="font-size: 0.68rem; font-weight: 800;">STATUTORY GATE</span>
                </div>
                <p style="margin: 0 0 1rem 0; font-size: 0.8rem; color: var(--color-text-secondary);">
                    Enforces mathematical quorum presence before committee proceedings can legally convene or conclude.
                </p>
            </div>

            <!-- 5. Committee Strict Quorum Toggle -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                <div style="max-width: 80%;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                        <span style="font-size: 1.1rem;">🏛️</span>
                        <span style="font-size: 0.98rem; font-weight: 800; color: var(--color-text-primary);">
                            Enforce Strict Committee & Board Quorum
                        </span>
                        <x-help-hint type="inline" 
                                     variant="warning"
                                     title="Quorum Lock"
                                     text="Prevents meeting minutes and dossier verdicts from being finalized unless both internal and external member minimums are verified present." />
                    </div>
                    <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.5;">
                        Locks committee decision submission if required internal or external board members are absent according to the board's constitution.
                    </div>
                </div>
                <div>
                    <label style="position: relative; display: inline-block; width: 52px; height: 28px; cursor: pointer;">
                        <input type="checkbox" wire:model="enforce_strict_committee_quorum" style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $enforce_strict_committee_quorum ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $enforce_strict_committee_quorum ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                            <span style="position: absolute; content: ''; height: 20px; width: 20px; left: {{ $enforce_strict_committee_quorum ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- 6. Peer Review Strict Quorum Toggle -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                <div style="max-width: 80%;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                        <span style="font-size: 1.1rem;">📊</span>
                        <span style="font-size: 0.98rem; font-weight: 800; color: var(--color-text-primary);">
                            Enforce Strict External Peer Review Score Threshold
                        </span>
                        <x-help-hint type="inline" 
                                     variant="warning"
                                     title="External Referee Gate"
                                     text="Requires 100% of dispatched peer reviews to be received before candidate can appear before the statutory selection board." />
                    </div>
                    <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.5;">
                        Requires all dispatched external peer reviewer reports to be submitted before an applicant can advance to the final Selection Board interview.
                    </div>
                </div>
                <div>
                    <label style="position: relative; display: inline-block; width: 52px; height: 28px; cursor: pointer;">
                        <input type="checkbox" wire:model="enforce_strict_peer_review_quorum" style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $enforce_strict_peer_review_quorum ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $enforce_strict_peer_review_quorum ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                            <span style="position: absolute; content: ''; height: 20px; width: 20px; left: {{ $enforce_strict_peer_review_quorum ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>
                        </span>
                    </label>
                </div>
            </div>

        </div>

        {{-- Deep Expandable Policy Guidelines --}}
        <x-help-hint type="drawer" 
                     variant="statutory" 
                     statute="University Act Sec. 11-14" 
                     title="Statutory Workflow Gating & Selection Board Protocol">
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <p style="margin: 0;">
                    Under the Higher Education Statutory Act, recruitment into permanent tenure or BPS faculty posts requires successful completion of sequential milestones. Disabling any milestone acts as an administrative waiver and will be logged in the permanent <code>SecurityAuditLog</code>.
                </p>
                <ul style="margin: 0; padding-left: 1.25rem;">
                    <li><strong>Scrutiny Stage:</strong> Verifies basic statutory eligibility (division, age, and equivalence).</li>
                    <li><strong>Peer Evaluation:</strong> 3 external referee reports required for Associate Professor / Professor chairs.</li>
                    <li><strong>Quorum Integrity:</strong> Ensures legal validity against appellate challenge in court or ombudsman tribunals.</li>
                </ul>
            </div>
        </x-help-hint>

        <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end;">
            <button type="submit" 
                    class="btn btn-primary" 
                    style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.75rem; font-weight: 800; font-size: 0.92rem; box-shadow: 0 4px 14px var(--primary-glow);">
                <span wire:loading.remove wire:target="savePipelineSettings">Save Workflow Pipeline Settings</span>
                <span wire:loading wire:target="savePipelineSettings">Saving Settings...</span>
            </button>
        </div>
    </form>
</div>
