<div class="card mb-6" style="padding: 1.75rem; border-color: var(--primary); box-shadow: var(--shadow-md);">
    <!-- Card Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                <span class="badge badge-indigo" style="font-size: 0.72rem; font-weight: 800;">STATUTORY ENGINE</span>
                <span class="badge badge-slate" style="font-size: 0.72rem;">PIPELINE GATING</span>
                <span class="badge badge-cyan" style="font-size: 0.72rem; font-weight: 700;">6-STAGE LIFECYCLE</span>
            </div>
            <h2 class="card-title" style="margin: 0; font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary);">
                Workflow Pipeline Stages & Milestone Control
            </h2>
            <p class="card-subtitle" style="margin: 0.25rem 0 0 0; font-size: 0.85rem; color: var(--color-text-secondary);">
                Enable, sequence, or bypass recruitment evaluation milestones according to institutional governance policies and statutory acts.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            @can('manage-settings')
            <button type="button" 
                    wire:click="resetPipelineToDefault" 
                    wire:confirm="Reset recruitment workflow pipeline stages to system defaults (all stages enabled)?"
                    class="btn btn-secondary btn-sm" 
                    style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.55rem 0.95rem; font-weight: 700; font-size: 0.82rem;"
                    title="Restore pipeline settings to standard university defaults">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>
                <span>Reset to Defaults</span>
            </button>

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
            @endcan
        </div>
    </div>

    {{-- System Help Callout Banner --}}
    <x-help-hint type="callout" variant="info" title="Recruitment Flow Lifecycle Logic">
        These settings govern the mandatory sequence of candidate progression. Turning off a stage removes that gating requirement system-wide, permitting applications to automatically route to the subsequent statutory body without holding for committee clearance.
    </x-help-hint>

    {{-- VISUAL PIPELINE PROGRESSION STEPPER --}}
    <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem 1.5rem; margin-top: 1.25rem; margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
            <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--secondary); letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.4rem;">
                <span>🔄 Visual Candidate Lifecycle Pipeline Flow</span>
            </div>
            <span class="badge badge-slate" style="font-size: 0.7rem;">Automated Decision Progression</span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 0.75rem; position: relative;">
            <!-- Step 1: Intake -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem; text-align: center; border-top: 3px solid var(--primary);">
                <div style="font-size: 0.65rem; font-weight: 800; color: var(--primary); text-transform: uppercase;">Step 1</div>
                <div style="font-size: 0.85rem; font-weight: 800; color: var(--color-text-primary); margin: 0.2rem 0;">Intake & Fee</div>
                <span class="badge badge-emerald" style="font-size: 0.65rem; font-weight: 800;">ACTIVE</span>
            </div>

            <!-- Step 2: Scrutiny -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem; text-align: center; border-top: 3px solid {{ $enable_scrutiny_step ? 'var(--primary)' : 'var(--color-text-dim)' }}; opacity: {{ $enable_scrutiny_step ? '1' : '0.6' }};">
                <div style="font-size: 0.65rem; font-weight: 800; color: {{ $enable_scrutiny_step ? 'var(--primary)' : 'var(--color-text-muted)' }}; text-transform: uppercase;">Step 2</div>
                <div style="font-size: 0.85rem; font-weight: 800; color: var(--color-text-primary); margin: 0.2rem 0;">Scrutiny Vetting</div>
                <span class="badge {{ $enable_scrutiny_step ? 'badge-emerald' : 'badge-slate' }}" style="font-size: 0.65rem; font-weight: 800;">
                    {{ $enable_scrutiny_step ? 'ACTIVE' : 'BYPASSED' }}
                </span>
            </div>

            <!-- Step 3: Peer Review -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem; text-align: center; border-top: 3px solid {{ $enable_peer_review_step ? 'var(--secondary)' : 'var(--color-text-dim)' }}; opacity: {{ $enable_peer_review_step ? '1' : '0.6' }};">
                <div style="font-size: 0.65rem; font-weight: 800; color: {{ $enable_peer_review_step ? 'var(--secondary)' : 'var(--color-text-muted)' }}; text-transform: uppercase;">Step 3</div>
                <div style="font-size: 0.85rem; font-weight: 800; color: var(--color-text-primary); margin: 0.2rem 0;">Peer Review</div>
                <span class="badge {{ $enable_peer_review_step ? 'badge-emerald' : 'badge-slate' }}" style="font-size: 0.65rem; font-weight: 800;">
                    {{ $enable_peer_review_step ? 'ACTIVE' : 'BYPASSED' }}
                </span>
            </div>

            <!-- Step 4: Selection Board -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem; text-align: center; border-top: 3px solid {{ $enable_board_interview_step ? 'var(--accent)' : 'var(--color-text-dim)' }}; opacity: {{ $enable_board_interview_step ? '1' : '0.6' }};">
                <div style="font-size: 0.65rem; font-weight: 800; color: {{ $enable_board_interview_step ? 'var(--accent)' : 'var(--color-text-muted)' }}; text-transform: uppercase;">Step 4</div>
                <div style="font-size: 0.85rem; font-weight: 800; color: var(--color-text-primary); margin: 0.2rem 0;">Selection Board</div>
                <span class="badge {{ $enable_board_interview_step ? 'badge-emerald' : 'badge-slate' }}" style="font-size: 0.65rem; font-weight: 800;">
                    {{ $enable_board_interview_step ? 'ACTIVE' : 'BYPASSED' }}
                </span>
            </div>

            <!-- Step 5: Syndicate -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem; text-align: center; border-top: 3px solid var(--success);">
                <div style="font-size: 0.65rem; font-weight: 800; color: var(--success); text-transform: uppercase;">Step 5</div>
                <div style="font-size: 0.85rem; font-weight: 800; color: var(--color-text-primary); margin: 0.2rem 0;">Syndicate Final</div>
                <span class="badge badge-emerald" style="font-size: 0.65rem; font-weight: 800;">ACTIVE</span>
            </div>

            <!-- Step 6: Appellate -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem; text-align: center; border-top: 3px solid {{ $enable_appellate_step ? 'var(--warning)' : 'var(--color-text-dim)' }}; opacity: {{ $enable_appellate_step ? '1' : '0.6' }};">
                <div style="font-size: 0.65rem; font-weight: 800; color: {{ $enable_appellate_step ? 'var(--warning)' : 'var(--color-text-muted)' }}; text-transform: uppercase;">Step 6</div>
                <div style="font-size: 0.85rem; font-weight: 800; color: var(--color-text-primary); margin: 0.2rem 0;">Appellate Tribunal</div>
                <span class="badge {{ $enable_appellate_step ? 'badge-amber' : 'badge-slate' }}" style="font-size: 0.65rem; font-weight: 800;">
                    {{ $enable_appellate_step ? 'ACTIVE' : 'BYPASSED' }}
                </span>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="savePipelineSettings" style="margin-top: 1rem;">
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            
            <!-- 1. Scrutiny Committee Verification Milestone -->
            <div id="setting-pipeline-scrutiny-gate" style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; background: var(--bg-surface); border: 1px solid {{ $enable_scrutiny_step ? 'var(--primary-glow)' : 'var(--border-color)' }}; border-radius: var(--radius-md); transition: var(--transition-fast);">
                <div style="max-width: 80%;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem; flex-wrap: wrap;">
                        <span style="font-size: 1.15rem;">📑</span>
                        <span style="font-size: 0.98rem; font-weight: 800; color: var(--color-text-primary);">
                            Scrutiny Committee Dossier Vetting Stage
                        </span>
                        <span class="badge {{ $enable_scrutiny_step ? 'badge-indigo' : 'badge-slate' }}" style="font-size: 0.68rem; font-weight: 800;">
                            {{ $enable_scrutiny_step ? 'STAGE 2: ACTIVE' : 'STAGE 2: BYPASSED' }}
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
                        <input type="checkbox" wire:model.live="enable_scrutiny_step" style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $enable_scrutiny_step ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $enable_scrutiny_step ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                            <span style="position: absolute; content: ''; height: 20px; width: 20px; left: {{ $enable_scrutiny_step ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- 2. Academic Peer Review Milestone -->
            <div id="setting-pipeline-peer-step" style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; background: var(--bg-surface); border: 1px solid {{ $enable_peer_review_step ? 'var(--primary-glow)' : 'var(--border-color)' }}; border-radius: var(--radius-md); transition: var(--transition-fast);">
                <div style="max-width: 80%;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem; flex-wrap: wrap;">
                        <span style="font-size: 1.15rem;">🔬</span>
                        <span style="font-size: 0.98rem; font-weight: 800; color: var(--color-text-primary);">
                            External Subject & Academic Peer Review Stage
                        </span>
                        <span class="badge {{ $enable_peer_review_step ? 'badge-purple' : 'badge-slate' }}" style="font-size: 0.68rem; font-weight: 800;">
                            {{ $enable_peer_review_step ? 'STAGE 3: ACTIVE' : 'STAGE 3: BYPASSED' }}
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
                        <input type="checkbox" wire:model.live="enable_peer_review_step" style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $enable_peer_review_step ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $enable_peer_review_step ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                            <span style="position: absolute; content: ''; height: 20px; width: 20px; left: {{ $enable_peer_review_step ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- 3. Selection Board Milestone -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; background: var(--bg-surface); border: 1px solid {{ $enable_board_interview_step ? 'var(--primary-glow)' : 'var(--border-color)' }}; border-radius: var(--radius-md); transition: var(--transition-fast);">
                <div style="max-width: 80%;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem; flex-wrap: wrap;">
                        <span style="font-size: 1.15rem;">👥</span>
                        <span style="font-size: 0.98rem; font-weight: 800; color: var(--color-text-primary);">
                            Statutory Selection Board Interview & Viva Stage
                        </span>
                        <span class="badge {{ $enable_board_interview_step ? 'badge-cyan' : 'badge-slate' }}" style="font-size: 0.68rem; font-weight: 800;">
                            {{ $enable_board_interview_step ? 'STAGE 4: ACTIVE' : 'STAGE 4: BYPASSED' }}
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
                        <input type="checkbox" wire:model.live="enable_board_interview_step" style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $enable_board_interview_step ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $enable_board_interview_step ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                            <span style="position: absolute; content: ''; height: 20px; width: 20px; left: {{ $enable_board_interview_step ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- 4. Appellate Committee Milestone -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; background: var(--bg-surface); border: 1px solid {{ $enable_appellate_step ? 'var(--primary-glow)' : 'var(--border-color)' }}; border-radius: var(--radius-md); transition: var(--transition-fast);">
                <div style="max-width: 80%;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem; flex-wrap: wrap;">
                        <span style="font-size: 1.15rem;">⚖️</span>
                        <span style="font-size: 0.98rem; font-weight: 800; color: var(--color-text-primary);">
                            Appellate Grievance & Representation Tribunal
                        </span>
                        <span class="badge {{ $enable_appellate_step ? 'badge-amber' : 'badge-slate' }}" style="font-size: 0.68rem; font-weight: 800;">
                            {{ $enable_appellate_step ? 'STAGE 6: ACTIVE' : 'STAGE 6: BYPASSED' }}
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
                        <input type="checkbox" wire:model.live="enable_appellate_step" style="opacity: 0; width: 0; height: 0;">
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
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem; flex-wrap: wrap;">
                        <span style="font-size: 1.15rem;">🏛️</span>
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
                        <input type="checkbox" wire:model.live="enforce_strict_committee_quorum" style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $enforce_strict_committee_quorum ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $enforce_strict_committee_quorum ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                            <span style="position: absolute; content: ''; height: 20px; width: 20px; left: {{ $enforce_strict_committee_quorum ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- 6. Peer Review Strict Quorum Toggle -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                <div style="max-width: 80%;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem; flex-wrap: wrap;">
                        <span style="font-size: 1.15rem;">📊</span>
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
                        <input type="checkbox" wire:model.live="enforce_strict_peer_review_quorum" style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $enforce_strict_peer_review_quorum ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $enforce_strict_peer_review_quorum ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                            <span style="position: absolute; content: ''; height: 20px; width: 20px; left: {{ $enforce_strict_peer_review_quorum ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- Section Divider: Statutory Modules & Secretariat Operations -->
            <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-color);">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                    <h4 style="margin: 0; font-size: 1rem; font-weight: 800; color: var(--color-text-primary);">
                        Statutory Operational Modules & Specialized Features
                    </h4>
                    <span class="badge badge-indigo" style="font-size: 0.68rem; font-weight: 800;">STATUTORY SUITE</span>
                </div>
                <p style="margin: 0 0 1rem 0; font-size: 0.8rem; color: var(--color-text-secondary);">
                    Toggle institutional recruitment capabilities, candidate summons generators, external reviewer links, and scrutiny intelligence features.
                </p>
            </div>

            <!-- 7. Statutory Interview Call Letter Generator Toggle -->
            <div id="setting-feature-call-letters" style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; background: var(--bg-surface); border: 1px solid {{ $enable_interview_call_letters ? 'var(--primary-glow)' : 'var(--border-color)' }}; border-radius: var(--radius-md); transition: var(--transition-fast);">
                <div style="max-width: 80%;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem; flex-wrap: wrap;">
                        <span style="font-size: 1.15rem;">✉️</span>
                        <span style="font-size: 0.98rem; font-weight: 800; color: var(--color-text-primary);">
                            Statutory Interview Call Letter Generator & Multi-Channel Dispatch (PDF + Email + SMS)
                        </span>
                        <span class="badge {{ $enable_interview_call_letters ? 'badge-emerald' : 'badge-slate' }}" style="font-size: 0.68rem; font-weight: 800;">
                            {{ $enable_interview_call_letters ? 'ENABLED' : 'DISABLED' }}
                        </span>
                    </div>
                    <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.5;">
                        Empowers the Registrar Secretariat to generate official printable Selection Board interview summons on university letterhead, schedule batches (date, time, venue, committee room), and dispatch multi-channel summons notifications with downloadable PDF access.
                    </div>
                </div>
                <div>
                    <label style="position: relative; display: inline-block; width: 52px; height: 28px; cursor: pointer;">
                        <input type="checkbox" wire:model.live="enable_interview_call_letters" style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $enable_interview_call_letters ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $enable_interview_call_letters ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                            <span style="position: absolute; content: ''; height: 20px; width: 20px; left: {{ $enable_interview_call_letters ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- 8. External Academic Referee Tokenized Magic Link Desk Toggle -->
            <div id="setting-feature-referee-magic-links" style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; background: var(--bg-surface); border: 1px solid {{ $enable_referee_magic_links ? 'var(--primary-glow)' : 'var(--border-color)' }}; border-radius: var(--radius-md); transition: var(--transition-fast);">
                <div style="max-width: 80%;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem; flex-wrap: wrap;">
                        <span style="font-size: 1.15rem;">🔗</span>
                        <span style="font-size: 0.98rem; font-weight: 800; color: var(--color-text-primary);">
                            External Academic Referee Tokenized Magic Links (Passwordless 1-Click Desk)
                        </span>
                        <span class="badge {{ $enable_referee_magic_links ? 'badge-emerald' : 'badge-slate' }}" style="font-size: 0.68rem; font-weight: 800;">
                            {{ $enable_referee_magic_links ? 'ENABLED' : 'DISABLED' }}
                        </span>
                    </div>
                    <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.5;">
                        Allows foreign and national external subject referees to securely access candidate dossiers and submit quantitative marks (0-100) and qualitative reports using time-limited cryptographic URL tokens without friction or password login hurdles.
                    </div>
                </div>
                <div>
                    <label style="position: relative; display: inline-block; width: 52px; height: 28px; cursor: pointer;">
                        <input type="checkbox" wire:model.live="enable_referee_magic_links" style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $enable_referee_magic_links ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $enable_referee_magic_links ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                            <span style="position: absolute; content: ''; height: 20px; width: 20px; left: {{ $enable_referee_magic_links ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- 9. HEC HJRS Journal Lookup & Predatory Filter Toggle -->
            <div id="setting-feature-hjrs-lookup" style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; background: var(--bg-surface); border: 1px solid {{ $enable_hjrs_journal_lookup ? 'var(--primary-glow)' : 'var(--border-color)' }}; border-radius: var(--radius-md); transition: var(--transition-fast);">
                <div style="max-width: 80%;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem; flex-wrap: wrap;">
                        <span style="font-size: 1.15rem;">📚</span>
                        <span style="font-size: 0.98rem; font-weight: 800; color: var(--color-text-primary);">
                            HEC HJRS Journal ISSN Lookup & Predatory Journal Detector
                        </span>
                        <span class="badge {{ $enable_hjrs_journal_lookup ? 'badge-emerald' : 'badge-slate' }}" style="font-size: 0.68rem; font-weight: 800;">
                            {{ $enable_hjrs_journal_lookup ? 'ENABLED' : 'DISABLED' }}
                        </span>
                    </div>
                    <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.5;">
                        Validates candidate research publications against official HEC HJRS journal categories (W, X, Y) and real-time REST API lookup, flagging predatory, blacklisted, or cloned journals with alert warnings in Scrutiny Committee verification.
                    </div>
                </div>
                <div>
                    <label style="position: relative; display: inline-block; width: 52px; height: 28px; cursor: pointer;">
                        <input type="checkbox" wire:model.live="enable_hjrs_journal_lookup" style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $enable_hjrs_journal_lookup ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $enable_hjrs_journal_lookup ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                            <span style="position: absolute; content: ''; height: 20px; width: 20px; left: {{ $enable_hjrs_journal_lookup ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- 10. Grievance Appellate Adjudication Workbench Toggle -->
            <div id="setting-feature-appellate-workbench" style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; background: var(--bg-surface); border: 1px solid {{ $enable_appellate_workbench ? 'var(--primary-glow)' : 'var(--border-color)' }}; border-radius: var(--radius-md); transition: var(--transition-fast);">
                <div style="max-width: 80%;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem; flex-wrap: wrap;">
                        <span style="font-size: 1.15rem;">⚖️</span>
                        <span style="font-size: 0.98rem; font-weight: 800; color: var(--color-text-primary);">
                            Grievance Appellate Committee Digital Adjudication Workbench
                        </span>
                        <span class="badge {{ $enable_appellate_workbench ? 'badge-emerald' : 'badge-slate' }}" style="font-size: 0.68rem; font-weight: 800;">
                            {{ $enable_appellate_workbench ? 'ENABLED' : 'DISABLED' }}
                        </span>
                    </div>
                    <div style="font-size: 0.82rem; color: var(--color-text-secondary); line-height: 1.5;">
                        Provides the Appellate Committee with a dedicated side-by-side adjudication desk (Scrutiny Finding vs Candidate Rebuttal Proof) and statutory decree execution (Overrule & Reinstate, Dismiss Appeal, or Remand for Re-scrutiny) with automated SMS and email alerts.
                    </div>
                </div>
                <div>
                    <label style="position: relative; display: inline-block; width: 52px; height: 28px; cursor: pointer;">
                        <input type="checkbox" wire:model.live="enable_appellate_workbench" style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $enable_appellate_workbench ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $enable_appellate_workbench ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                            <span style="position: absolute; content: ''; height: 20px; width: 20px; left: {{ $enable_appellate_workbench ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>
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

        <div style="margin-top: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
            @can('manage-settings')
            <button type="button" 
                    wire:click="resetPipelineToDefault" 
                    wire:confirm="Reset recruitment workflow pipeline stages to system defaults (all stages enabled)?"
                    class="btn btn-secondary btn-sm" 
                    style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/></svg>
                <span>Reset Pipeline to Defaults</span>
            </button>

            <button type="submit" 
                    class="btn btn-primary" 
                    style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.75rem; font-weight: 800; font-size: 0.92rem; box-shadow: 0 4px 14px var(--primary-glow);">
                <span wire:loading.remove wire:target="savePipelineSettings">Save Workflow Pipeline Settings</span>
                <span wire:loading wire:target="savePipelineSettings">Saving Settings...</span>
            </button>
            @endcan
        </div>
    </form>
</div>
