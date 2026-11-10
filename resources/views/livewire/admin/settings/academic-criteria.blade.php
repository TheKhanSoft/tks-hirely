<div class="card mb-6" style="padding: 1.75rem; border-color: var(--primary); box-shadow: var(--shadow-md);">
    <!-- Card Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                <span class="badge badge-indigo" style="font-size: 0.72rem; font-weight: 800;">STATUTORY BENCHMARKS</span>
                <span class="badge badge-purple" style="font-size: 0.72rem; font-weight: 800;">HEC CRITERIA COMPLIANT</span>
                <span class="badge badge-emerald" style="font-size: 0.72rem; font-weight: 700;">SCRUTINY ENGINE</span>
            </div>
            <h2 class="card-title" style="margin: 0; font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary);">
                Academic Division Criteria, Minimum CGPA & Peer Review Quotas
            </h2>
            <p class="card-subtitle" style="margin: 0.25rem 0 0 0; font-size: 0.85rem; color: var(--color-text-secondary);">
                Enforce institutional academic gating thresholds, statutory degree division limits, and mandatory external expert referee quotas.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            @can('manage-settings')
            <button type="button" 
                    wire:click="resetAcademicCriteriaToDefault" 
                    wire:confirm="Reset academic criteria and peer review quotas to HEC benchmark defaults?"
                    class="btn btn-secondary btn-sm" 
                    style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.55rem 0.95rem; font-weight: 700; font-size: 0.82rem;"
                    title="Restore academic criteria to standard HEC defaults">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>
                <span>Reset HEC Criteria</span>
            </button>

            <button type="button" 
                    wire:click="saveAcademicCriteriaSettings" 
                    class="btn btn-primary" 
                    style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.4rem; font-weight: 800; font-size: 0.88rem; box-shadow: 0 4px 14px var(--primary-glow);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                <span wire:loading.remove wire:target="saveAcademicCriteriaSettings">Save Academic Benchmarks</span>
                <span wire:loading wire:target="saveAcademicCriteriaSettings">Saving...</span>
            </button>
            @endcan
        </div>
    </div>

    {{-- Statutory Callout --}}
    <x-help-hint type="callout" variant="statutory" statute="HEC Statutory Minimum Criteria" title="Automated Academic Dossier Eligibility Vetting">
        These rules run automatically inside the Scrutiny Engine when screening candidate transcripts. Candidates failing to satisfy these criteria are automatically flagged as ineligible with precise statutory justifications documented in the candidate summary dossier.
    </x-help-hint>

    {{-- VISUAL QUALIFICATION MATRIX STRIP --}}
    <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem 1.5rem; margin-top: 1.25rem; margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem; flex-wrap: wrap; gap: 0.5rem;">
            <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--secondary); letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.4rem;">
                <span>📊 HEC Academic Division Classification & Cutoff Matrix</span>
            </div>
            <span class="badge badge-slate" style="font-size: 0.7rem;">Automated Dossier Vetting Rules</span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 0.75rem;">
            <!-- 1st Division Card -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem 1rem; border-left: 3px solid var(--success);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-weight: 800; font-size: 0.85rem; color: var(--color-text-primary);">1st Division</span>
                    <span class="badge badge-emerald" style="font-size: 0.65rem; font-weight: 800;">PASS / REQUIRED</span>
                </div>
                <div style="font-size: 0.78rem; color: var(--color-text-secondary); margin-top: 0.35rem;">
                    &ge; 60.0% Marks or &ge; 3.00 / 4.00 CGPA
                </div>
            </div>

            <!-- 2nd Division Card -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem 1rem; border-left: 3px solid var(--warning);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-weight: 800; font-size: 0.85rem; color: var(--color-text-primary);">2nd Division</span>
                    <span class="badge {{ $global_disallow_two_second_divisions ? 'badge-amber' : 'badge-slate' }}" style="font-size: 0.65rem; font-weight: 800;">
                        {{ $global_disallow_two_second_divisions ? 'MAX 1 ALLOWED' : 'PERMITTED' }}
                    </span>
                </div>
                <div style="font-size: 0.78rem; color: var(--color-text-secondary); margin-top: 0.35rem;">
                    45.0% &ndash; 59.9% Marks or 2.50 &ndash; 2.99 CGPA
                </div>
            </div>

            <!-- 3rd Division Card -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem 1rem; border-left: 3px solid var(--danger);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-weight: 800; font-size: 0.85rem; color: var(--color-text-primary);">3rd Division</span>
                    <span class="badge {{ $global_disallow_third_division ? 'badge-rose' : 'badge-slate' }}" style="font-size: 0.65rem; font-weight: 800;">
                        {{ $global_disallow_third_division ? 'ZERO TOLERANCE' : ($global_max_allowed_third_divisions !== null && $global_max_allowed_third_divisions !== '' ? 'CAP: '.$global_max_allowed_third_divisions : 'CONDITIONAL') }}
                    </span>
                </div>
                <div style="font-size: 0.78rem; color: var(--color-text-secondary); margin-top: 0.35rem;">
                    &lt; 45.0% Marks or &lt; 2.50 CGPA (Terminal Disallowed)
                </div>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="saveAcademicCriteriaSettings" style="margin-top: 1.5rem;">
        
        <!-- SECTION 1: Division Restrictions -->
        <div style="margin-bottom: 2rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                <span style="font-size: 1.15rem;">⚖️</span>
                <h4 style="margin: 0; font-size: 1rem; font-weight: 800; color: var(--color-text-primary);">
                    Statutory Degree Division Restrictions
                </h4>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1rem;">
                
                <!-- 1. Disallow Third Division Across Career -->
                <div id="setting-academic-disallow-3rd-career" style="display: flex; justify-content: space-between; align-items: center; padding: 1.15rem; background: var(--bg-surface); border: 1px solid {{ $global_disallow_third_division ? 'var(--primary-glow)' : 'var(--border-color)' }}; border-radius: var(--radius-md); transition: var(--transition-fast);">
                    <div style="max-width: 80%;">
                        <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem; flex-wrap: wrap;">
                            <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                                Zero 3rd Division Allowed in Academic Career
                            </span>
                            <span class="badge {{ $global_disallow_third_division ? 'badge-rose' : 'badge-slate' }}" style="font-size: 0.65rem; font-weight: 800;">
                                {{ $global_disallow_third_division ? 'ZERO TOLERANCE' : 'PERMITTED' }}
                            </span>
                            <x-help-hint type="inline" 
                                         variant="danger" 
                                         statute="HEC Faculty Policy" 
                                         title="Absolute 3rd Division Bar" 
                                         text="Disqualifies applicants who hold even a single 3rd Division in their academic career from Matric to PhD." />
                        </div>
                        <div style="font-size: 0.8rem; color: var(--color-text-secondary); line-height: 1.45;">
                            Strictly disqualifies candidates with any 3rd Division across their entire academic record.
                        </div>
                    </div>
                    <div>
                        <label style="position: relative; display: inline-block; width: 50px; height: 26px; cursor: pointer;">
                            <input type="checkbox" wire:model.live="global_disallow_third_division" style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $global_disallow_third_division ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $global_disallow_third_division ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                                <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $global_disallow_third_division ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%;"></span>
                            </span>
                        </label>
                    </div>
                </div>

                <!-- 2. Disallow Third Division in Terminal / Highest Degree -->
                <div id="setting-academic-disallow-3rd-terminal" style="display: flex; justify-content: space-between; align-items: center; padding: 1.15rem; background: var(--bg-surface); border: 1px solid {{ ($global_disallow_third_division || $global_disallow_third_in_terminal) ? 'var(--primary-glow)' : 'var(--border-color)' }}; border-radius: var(--radius-md); transition: var(--transition-fast); {{ $global_disallow_third_division ? 'opacity: 0.88;' : '' }}">
                    <div style="max-width: 80%;">
                        <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem; flex-wrap: wrap;">
                            <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                                Disallow 3rd Division in Terminal Degree
                            </span>
                            @if($global_disallow_third_division)
                                <span class="badge badge-purple" style="font-size: 0.65rem; font-weight: 800;">
                                    🔒 LOCKED ON (SUBSUMED)
                                </span>
                            @else
                                <span class="badge {{ $global_disallow_third_in_terminal ? 'badge-rose' : 'badge-slate' }}" style="font-size: 0.65rem; font-weight: 800;">
                                    {{ $global_disallow_third_in_terminal ? 'TERMINAL BAR ON' : 'PERMITTED' }}
                                </span>
                            @endif
                            <x-help-hint type="inline" 
                                         variant="statutory" 
                                         statute="Standard Statutory Rule" 
                                         title="Terminal Degree 3rd Division Bar" 
                                         text="Requires the highest qualifying degree (e.g. Master, MS/MPhil, or PhD) to be 1st or 2nd Division." />
                        </div>
                        @if($global_disallow_third_division)
                            <div style="font-size: 0.78rem; color: var(--primary); font-weight: 600; line-height: 1.45;">
                                🔒 Automatically locked ON because career-wide Zero 3rd Division tolerance is active.
                            </div>
                        @else
                            <div style="font-size: 0.8rem; color: var(--color-text-secondary); line-height: 1.45;">
                                Enforces that the highest required degree for the applied position cannot be in the 3rd Division.
                            </div>
                        @endif
                    </div>
                    <div>
                        <label style="position: relative; display: inline-block; width: 50px; height: 26px; {{ $global_disallow_third_division ? 'cursor: not-allowed; opacity: 0.65;' : 'cursor: pointer;' }}">
                            <input type="checkbox" 
                                   wire:model.live="global_disallow_third_in_terminal" 
                                   {{ $global_disallow_third_division ? 'disabled' : '' }}
                                   style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; {{ $global_disallow_third_division ? 'cursor: not-allowed;' : 'cursor: pointer;' }} inset: 0; background-color: {{ ($global_disallow_third_division || $global_disallow_third_in_terminal) ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ ($global_disallow_third_division || $global_disallow_third_in_terminal) ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                                <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ ($global_disallow_third_division || $global_disallow_third_in_terminal) ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%;"></span>
                            </span>
                        </label>
                    </div>
                </div>

                <!-- 3. Maximum Allowed 3rd Divisions -->
                <div id="setting-academic-max-3rd-divisions" style="padding: 1.15rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); {{ $global_disallow_third_division ? 'opacity: 0.88;' : '' }}">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                        <div style="display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap;">
                            <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                                Maximum Permitted 3rd Divisions
                            </span>
                            @if($global_disallow_third_division)
                                <span class="badge badge-purple" style="font-size: 0.65rem; font-weight: 800;">
                                    🔒 LOCKED AT 0
                                </span>
                            @endif
                        </div>
                        <x-help-hint type="inline" 
                                     variant="tip" 
                                     title="Allowed 3rd Division Cap" 
                                     text="Sets a numerical cap on 3rd divisions (e.g. at most 1 allowed in Matric/FSc). Leave blank for unconstrained unless zero-tolerance is on." />
                    </div>
                    @if($global_disallow_third_division)
                        <div style="font-size: 0.78rem; color: var(--primary); font-weight: 600; margin-bottom: 0.65rem;">
                            🔒 Fixed at 0 because career-wide Zero 3rd Division tolerance is active.
                        </div>
                    @else
                        <div style="font-size: 0.8rem; color: var(--color-text-secondary); margin-bottom: 0.65rem;">
                            Upper limit on total third divisions permitted in an applicant's academic trajectory.
                        </div>
                    @endif
                    <input type="number" 
                           wire:model.live="global_max_allowed_third_divisions" 
                           min="0" 
                           max="5" 
                           {{ $global_disallow_third_division ? 'disabled' : '' }}
                           placeholder="{{ $global_disallow_third_division ? 'Fixed at 0 (Zero Tolerance active)' : 'Leave empty for unconstrained' }}" 
                           class="form-control" 
                           style="font-size: 0.9rem; padding: 0.45rem 0.75rem; {{ $global_disallow_third_division ? 'cursor: not-allowed; background: var(--bg-card); opacity: 0.75;' : '' }}">
                </div>

                <!-- 4. Disallow Multiple 2nd Divisions -->
                <div id="setting-academic-disallow-two-2nd" style="display: flex; justify-content: space-between; align-items: center; padding: 1.15rem; background: var(--bg-surface); border: 1px solid {{ $global_disallow_two_second_divisions ? 'var(--primary-glow)' : 'var(--border-color)' }}; border-radius: var(--radius-md); transition: var(--transition-fast);">
                    <div style="max-width: 80%;">
                        <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem; flex-wrap: wrap;">
                            <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                                Disallow Two or More 2nd Divisions
                            </span>
                            <span class="badge {{ $global_disallow_two_second_divisions ? 'badge-amber' : 'badge-slate' }}" style="font-size: 0.65rem; font-weight: 800;">
                                {{ $global_disallow_two_second_divisions ? 'MAX 1 2ND DIV' : 'MULTIPLE ALLOWED' }}
                            </span>
                            <x-help-hint type="inline" 
                                         variant="warning" 
                                         statute="HEC University Model Act" 
                                         title="Only 1 Second Division Permitted" 
                                         text="Enforces the classic statutory rule: 'Only one 2nd Division in the entire academic career is permissible; all other degrees must be 1st Division.'" />
                        </div>
                        <div style="font-size: 0.8rem; color: var(--color-text-secondary); line-height: 1.45;">
                            Permits at most one 2nd Division; multiple 2nd divisions result in automated disqualification.
                        </div>
                    </div>
                    <div>
                        <label style="position: relative; display: inline-block; width: 50px; height: 26px; cursor: pointer;">
                            <input type="checkbox" wire:model.live="global_disallow_two_second_divisions" style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $global_disallow_two_second_divisions ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $global_disallow_two_second_divisions ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                                <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $global_disallow_two_second_divisions ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%;"></span>
                            </span>
                        </label>
                    </div>
                </div>

            </div>
        </div>

        <!-- SECTION 2: Percentage & CGPA Thresholds -->
        <div style="margin-bottom: 2rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem; flex-wrap: wrap; gap: 0.5rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="font-size: 1.25rem;">🎯</span>
                    <div>
                        <h4 style="margin: 0; font-size: 1.05rem; font-weight: 800; color: var(--color-text-primary);">
                            Minimum Aggregate Percentage &amp; Terminal CGPA Benchmarks
                        </h4>
                        <p style="margin: 0.15rem 0 0 0; font-size: 0.78rem; color: var(--color-text-secondary);">
                            Automated transcript scoring thresholds verified during candidate dossier scrutiny.
                        </p>
                    </div>
                </div>
                <span class="badge badge-indigo" style="font-size: 0.7rem; font-weight: 800;">HEC STANDARD SCALE</span>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(290px, 1fr)); gap: 1.15rem;">
                
                <!-- 1. Min Aggregate Percentage -->
                <div id="setting-academic-aggregate-pct" class="card" style="padding: 1.25rem; background: var(--bg-surface); border: 1.5px solid var(--border-color); border-radius: var(--radius-md); box-shadow: var(--shadow-sm); transition: all 0.2s ease;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.35rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <div style="width: 30px; height: 30px; border-radius: 8px; background: rgba(99,102,241,0.12); color: #6366f1; display: flex; align-items: center; justify-content: center; font-size: 0.95rem; font-weight: 800; flex-shrink: 0;">
                                🎯
                            </div>
                            <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                                Global Academic Aggregate
                            </span>
                        </div>
                        <x-help-hint type="inline" 
                                     variant="info" 
                                     title="Minimum Marks Cutoff" 
                                     text="Global minimum threshold for marks percentage across qualifying examinations." 
                                     example="e.g. 60% for 1st Division" />
                    </div>
                    <div style="font-size: 0.78rem; color: var(--color-text-secondary); margin-bottom: 0.75rem; min-height: 2.2em; line-height: 1.4;">
                        Baseline marks threshold applied to career qualification records across entire trajectory.
                    </div>
                    
                    <!-- Fused Modern Input Group -->
                    <div style="display: flex; align-items: stretch; border: 1.5px solid var(--border-color); border-radius: 8px; overflow: hidden; background: var(--bg-card); box-shadow: inset 0 1px 2px rgba(0,0,0,0.04); transition: border-color 0.2s;">
                        <input type="number" 
                               step="0.1" 
                               min="0"
                               max="100"
                               wire:model.live="global_min_marks_percentage" 
                               placeholder="e.g. 60.0" 
                               class="form-control" 
                               style="border: none; background: transparent; padding: 0.65rem 0.95rem; font-size: 1.05rem; font-weight: 800; color: var(--color-text-primary); flex: 1; min-width: 0; box-shadow: none;">
                        <div style="display: flex; align-items: center; justify-content: center; padding: 0 1.15rem; background: var(--bg-surface); border-left: 1.5px solid var(--border-color); font-weight: 800; font-size: 0.95rem; color: var(--primary); letter-spacing: 0.02em;">
                            %
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.65rem; font-size: 0.72rem;">
                        <span style="color: var(--color-text-muted);">Career Cutoff:</span>
                        <span class="badge badge-slate" style="font-size: 0.68rem; font-weight: 800;">
                            {{ $global_min_marks_percentage ? $global_min_marks_percentage . '% Minimum' : 'No Global Cutoff' }}
                        </span>
                    </div>
                </div>

                <!-- 2. Min Terminal Degree Marks % -->
                <div id="setting-academic-terminal-pct" class="card" style="padding: 1.25rem; background: var(--bg-surface); border: 1.5px solid var(--border-color); border-radius: var(--radius-md); box-shadow: var(--shadow-sm); transition: all 0.2s ease;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.35rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <div style="width: 30px; height: 30px; border-radius: 8px; background: rgba(14,165,233,0.12); color: #0ea5e9; display: flex; align-items: center; justify-content: center; font-size: 0.95rem; font-weight: 800; flex-shrink: 0;">
                                🎓
                            </div>
                            <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                                Terminal Degree Marks %
                            </span>
                        </div>
                        <x-help-hint type="inline" 
                                     variant="info" 
                                     title="Terminal Degree Marks" 
                                     text="Threshold specifically enforced on the highest required qualification." 
                                     example="e.g. 65% in MS/MPhil" />
                    </div>
                    <div style="font-size: 0.78rem; color: var(--color-text-secondary); margin-bottom: 0.75rem; min-height: 2.2em; line-height: 1.4;">
                        Minimum required score in candidate's highest qualifying degree for this rank.
                    </div>

                    <!-- Fused Modern Input Group -->
                    <div style="display: flex; align-items: stretch; border: 1.5px solid var(--border-color); border-radius: 8px; overflow: hidden; background: var(--bg-card); box-shadow: inset 0 1px 2px rgba(0,0,0,0.04); transition: border-color 0.2s;">
                        <input type="number" 
                               step="0.1" 
                               min="0"
                               max="100"
                               wire:model.live="global_min_terminal_percentage" 
                               placeholder="e.g. 65.0" 
                               class="form-control" 
                               style="border: none; background: transparent; padding: 0.65rem 0.95rem; font-size: 1.05rem; font-weight: 800; color: var(--color-text-primary); flex: 1; min-width: 0; box-shadow: none;">
                        <div style="display: flex; align-items: center; justify-content: center; padding: 0 1.15rem; background: var(--bg-surface); border-left: 1.5px solid var(--border-color); font-weight: 800; font-size: 0.95rem; color: #0ea5e9; letter-spacing: 0.02em;">
                            %
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.65rem; font-size: 0.72rem;">
                        <span style="color: var(--color-text-muted);">Highest Qualification:</span>
                        <span class="badge badge-cyan" style="font-size: 0.68rem; font-weight: 800;">
                            {{ $global_min_terminal_percentage ? $global_min_terminal_percentage . '% in MS/PhD' : 'Unconstrained' }}
                        </span>
                    </div>
                </div>

                <!-- 3. Min Terminal CGPA (HIGHLIGHTED SCALE / 4.00) -->
                <div id="setting-academic-cgpa" class="card" style="padding: 1.25rem; background: var(--bg-surface); border: 1.5px solid {{ (float)$global_min_terminal_cgpa >= 3.0 ? 'rgba(245,158,11,0.5)' : 'var(--border-color)' }}; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); transition: all 0.2s ease;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.35rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <div style="width: 30px; height: 30px; border-radius: 8px; background: rgba(245,158,11,0.15); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 0.95rem; font-weight: 800; flex-shrink: 0;">
                                📊
                            </div>
                            <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                                Terminal Degree Minimum CGPA
                            </span>
                        </div>
                        <x-help-hint type="inline" 
                                     variant="statutory" 
                                     statute="HEC Semester Guidelines" 
                                     title="Terminal Degree CGPA Cutoff" 
                                     text="Minimum CGPA in semester system (e.g. 3.00/4.00 or 3.50/4.00 for PhD entry/faculty eligibility)." 
                                     example="Standard: 3.00" />
                    </div>
                    <div style="font-size: 0.78rem; color: var(--color-text-secondary); margin-bottom: 0.75rem; min-height: 2.2em; line-height: 1.4;">
                        Minimum grade point average on semester transcripts (4.00 scale).
                    </div>

                    <!-- Fused Modern Input Group with Styled / 4.00 Suffix -->
                    <div style="display: flex; align-items: stretch; border: 1.5px solid rgba(245,158,11,0.45); border-radius: 8px; overflow: hidden; background: var(--bg-card); box-shadow: 0 2px 8px -2px rgba(245,158,11,0.15); transition: all 0.2s;">
                        <input type="number" 
                               step="0.01" 
                               min="0.00"
                               max="4.00"
                               wire:model.live="global_min_terminal_cgpa" 
                               placeholder="e.g. 3.00" 
                               class="form-control" 
                               style="border: none; background: transparent; padding: 0.65rem 0.95rem; font-size: 1.12rem; font-weight: 900; color: var(--color-text-primary); flex: 1; min-width: 0; box-shadow: none;">
                        <div style="display: flex; align-items: center; justify-content: center; padding: 0 1.25rem; background: linear-gradient(135deg, rgba(245,158,11,0.12), rgba(245,158,11,0.22)); border-left: 1.5px solid rgba(245,158,11,0.35); font-weight: 900; font-size: 1rem; color: #d97706; letter-spacing: 0.05em; white-space: nowrap;">
                            / 4.00
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.65rem; font-size: 0.72rem;">
                        <span style="color: var(--color-text-muted);">Standard Scale:</span>
                        @if((float)$global_min_terminal_cgpa >= 3.0)
                            <span class="badge badge-emerald" style="font-size: 0.68rem; font-weight: 800;">
                                ✓ HEC Compliant (&ge; 3.00 / 4.00)
                            </span>
                        @elseif((float)$global_min_terminal_cgpa > 0)
                            <span class="badge badge-amber" style="font-size: 0.68rem; font-weight: 800;">
                                Cutoff: {{ number_format((float)$global_min_terminal_cgpa, 2) }} / 4.00
                            </span>
                        @else
                            <span class="badge badge-slate" style="font-size: 0.68rem; font-weight: 800;">
                                No CGPA Bar
                            </span>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        <!-- SECTION 3: External Peer Reviewer Quotas by Rank -->
        <div id="setting-academic-peer-quotas" style="margin-bottom: 2rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem; flex-wrap: wrap; gap: 0.5rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="font-size: 1.25rem;">👨‍🏫</span>
                    <div>
                        <h4 style="margin: 0; font-size: 1.05rem; font-weight: 800; color: var(--color-text-primary);">
                            Statutory External Peer Referee Quotas by Academic Rank
                        </h4>
                        <p style="margin: 0.15rem 0 0 0; font-size: 0.78rem; color: var(--color-text-secondary);">
                            Mandatory external evaluation dossiers dispatched for confidential peer vetting before selection board.
                        </p>
                    </div>
                </div>
                <span class="badge badge-cyan" style="font-size: 0.7rem; font-weight: 800;">STATUTORY QUORUM</span>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(290px, 1fr)); gap: 1.15rem;">
                
                <!-- Assistant Professor Quota -->
                <div class="card" style="padding: 1.25rem; background: var(--bg-surface); border: 1.5px solid var(--border-color); border-radius: var(--radius-md); box-shadow: var(--shadow-sm); transition: all 0.2s ease;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.35rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <div style="width: 30px; height: 30px; border-radius: 8px; background: rgba(99,102,241,0.12); color: #6366f1; display: flex; align-items: center; justify-content: center; font-size: 0.95rem; font-weight: 800; flex-shrink: 0;">
                                🔬
                            </div>
                            <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                                Assistant Professor (TTS / BPS)
                            </span>
                        </div>
                        <x-help-hint type="inline" 
                                     variant="statutory" 
                                     statute="TTS Statutes Sec. 2.4" 
                                     title="Assistant Professor Peer Evaluation" 
                                     text="HEC requires minimum 3 independent external evaluation reports from technologically advanced countries for Tenure Track (TTS) Assistant Professors." 
                                     example="Standard: 3 referees" />
                    </div>
                    <div style="font-size: 0.78rem; color: var(--color-text-secondary); margin-bottom: 0.75rem; min-height: 2.2em; line-height: 1.4;">
                        Minimum mandatory external peer review dossiers to dispatch for Assistant Professor candidacies.
                    </div>

                    <!-- Fused Modern Input Group -->
                    <div style="display: flex; align-items: stretch; border: 1.5px solid var(--border-color); border-radius: 8px; overflow: hidden; background: var(--bg-card); box-shadow: inset 0 1px 2px rgba(0,0,0,0.04);">
                        <input type="number" 
                               wire:model.live="asst_prof_required_peer_reviewers" 
                               min="0" 
                               max="10" 
                               class="form-control" 
                               style="border: none; background: transparent; padding: 0.65rem 0.95rem; text-align: center; font-weight: 900; font-size: 1.15rem; color: var(--color-text-primary); width: 85px; box-shadow: none;">
                        <div style="display: flex; align-items: center; justify-content: center; padding: 0 1.15rem; background: var(--bg-surface); border-left: 1.5px solid var(--border-color); font-weight: 800; font-size: 0.85rem; color: var(--color-text-secondary); flex: 1; text-transform: uppercase; letter-spacing: 0.04em;">
                            Experts / Dossiers
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.65rem; font-size: 0.72rem;">
                        <span style="color: var(--color-text-muted);">TTS Requirement:</span>
                        @if((int)$asst_prof_required_peer_reviewers >= 3)
                            <span class="badge badge-emerald" style="font-size: 0.68rem; font-weight: 800;">
                                ✓ Statutory Quorum ({{ $asst_prof_required_peer_reviewers }} Referees)
                            </span>
                        @elseif((int)$asst_prof_required_peer_reviewers > 0)
                            <span class="badge badge-amber" style="font-size: 0.68rem; font-weight: 800;">
                                Partial Quota ({{ $asst_prof_required_peer_reviewers }} Referees)
                            </span>
                        @else
                            <span class="badge badge-slate" style="font-size: 0.68rem; font-weight: 800;">
                                Peer Review Bypassed
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Professor & Associate Professor Quota -->
                <div class="card" style="padding: 1.25rem; background: var(--bg-surface); border: 1.5px solid var(--border-color); border-radius: var(--radius-md); box-shadow: var(--shadow-sm); transition: all 0.2s ease;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.35rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <div style="width: 30px; height: 30px; border-radius: 8px; background: rgba(16,185,129,0.12); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 0.95rem; font-weight: 800; flex-shrink: 0;">
                                🏛️
                            </div>
                            <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                                Professor &amp; Associate Professor
                            </span>
                        </div>
                        <x-help-hint type="inline" 
                                     variant="statutory" 
                                     statute="HEC Senior Chair Criteria" 
                                     title="Senior Chair Peer Quota" 
                                     text="Senior academic chairs must receive positive recommendations from independent foreign referees before appearing at the Selection Board." 
                                     example="Standard: 3 referees" />
                    </div>
                    <div style="font-size: 0.78rem; color: var(--color-text-secondary); margin-bottom: 0.75rem; min-height: 2.2em; line-height: 1.4;">
                        Mandatory external foreign and national referee reports for senior academic professorships.
                    </div>

                    <!-- Fused Modern Input Group -->
                    <div style="display: flex; align-items: stretch; border: 1.5px solid var(--border-color); border-radius: 8px; overflow: hidden; background: var(--bg-card); box-shadow: inset 0 1px 2px rgba(0,0,0,0.04);">
                        <input type="number" 
                               wire:model.live="prof_required_peer_reviewers" 
                               min="0" 
                               max="10" 
                               class="form-control" 
                               style="border: none; background: transparent; padding: 0.65rem 0.95rem; text-align: center; font-weight: 900; font-size: 1.15rem; color: var(--color-text-primary); width: 85px; box-shadow: none;">
                        <div style="display: flex; align-items: center; justify-content: center; padding: 0 1.15rem; background: var(--bg-surface); border-left: 1.5px solid var(--border-color); font-weight: 800; font-size: 0.85rem; color: var(--color-text-secondary); flex: 1; text-transform: uppercase; letter-spacing: 0.04em;">
                            Experts / Dossiers
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.65rem; font-size: 0.72rem;">
                        <span style="color: var(--color-text-muted);">Senior Quorum:</span>
                        @if((int)$prof_required_peer_reviewers >= 3)
                            <span class="badge badge-emerald" style="font-size: 0.68rem; font-weight: 800;">
                                ✓ Full International Quorum ({{ $prof_required_peer_reviewers }})
                            </span>
                        @elseif((int)$prof_required_peer_reviewers > 0)
                            <span class="badge badge-amber" style="font-size: 0.68rem; font-weight: 800;">
                                Quota: {{ $prof_required_peer_reviewers }} Referees
                            </span>
                        @else
                            <span class="badge badge-slate" style="font-size: 0.68rem; font-weight: 800;">
                                Peer Review Bypassed
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Lecturer Quota -->
                <div class="card" style="padding: 1.25rem; background: var(--bg-surface); border: 1.5px solid var(--border-color); border-radius: var(--radius-md); box-shadow: var(--shadow-sm); transition: all 0.2s ease;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.35rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <div style="width: 30px; height: 30px; border-radius: 8px; background: rgba(148,163,184,0.15); color: #64748b; display: flex; align-items: center; justify-content: center; font-size: 0.95rem; font-weight: 800; flex-shrink: 0;">
                                📖
                            </div>
                            <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                                Lecturer / Teaching Cadre
                            </span>
                        </div>
                        <x-help-hint type="inline" 
                                     variant="tip" 
                                     title="Lecturer Evaluation" 
                                     text="Junior academic positions generally rely on scrutiny screening and written tests without external referee dispatch (0 means skipped)." 
                                     example="Standard: 0 (Skipped)" />
                    </div>
                    <div style="font-size: 0.78rem; color: var(--color-text-secondary); margin-bottom: 0.75rem; min-height: 2.2em; line-height: 1.4;">
                        External referees required (Set 0 to skip external referee dispatch for junior faculty).
                    </div>

                    <!-- Fused Modern Input Group -->
                    <div style="display: flex; align-items: stretch; border: 1.5px solid var(--border-color); border-radius: 8px; overflow: hidden; background: var(--bg-card); box-shadow: inset 0 1px 2px rgba(0,0,0,0.04);">
                        <input type="number" 
                               wire:model.live="lecturer_required_peer_reviewers" 
                               min="0" 
                               max="10" 
                               class="form-control" 
                               style="border: none; background: transparent; padding: 0.65rem 0.95rem; text-align: center; font-weight: 900; font-size: 1.15rem; color: var(--color-text-primary); width: 85px; box-shadow: none;">
                        <div style="display: flex; align-items: center; justify-content: center; padding: 0 1.15rem; background: var(--bg-surface); border-left: 1.5px solid var(--border-color); font-weight: 800; font-size: 0.85rem; color: var(--color-text-secondary); flex: 1; text-transform: uppercase; letter-spacing: 0.04em;">
                            Experts / Dossiers
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.65rem; font-size: 0.72rem;">
                        <span style="color: var(--color-text-muted);">Junior Cadre:</span>
                        @if((int)$lecturer_required_peer_reviewers === 0)
                            <span class="badge badge-slate" style="font-size: 0.68rem; font-weight: 800;">
                                Skipped for Lecturers (0)
                            </span>
                        @else
                            <span class="badge badge-indigo" style="font-size: 0.68rem; font-weight: 800;">
                                {{ $lecturer_required_peer_reviewers }} External Referees
                            </span>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        {{-- STATUTORY MERIT FORMULATION & SELECTION BOARD SCORING WEIGHTS --}}
        @php
            $formulaTotal = (int)$merit_academic_weight + (int)$merit_higher_qualification_weight + (int)$merit_research_weight + (int)$merit_experience_weight + (int)$merit_interview_weight;
        @endphp
        <div style="margin-top: 2.25rem; border-top: 1px solid var(--border-color); padding-top: 1.75rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                        <span class="badge badge-indigo" style="font-size: 0.72rem; font-weight: 800;">STATUTORY MERIT SCORING</span>
                        <span class="badge badge-emerald" style="font-size: 0.72rem; font-weight: 700;">SELECTION BOARD</span>
                    </div>
                    <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: var(--color-text-primary);">
                        Statutory Selection Board Merit Formulation &amp; Weightages
                    </h3>
                    <p style="margin: 0.25rem 0 0 0; font-size: 0.83rem; color: var(--color-text-secondary);">
                        Configure statutory candidate evaluation breakdown prescribed under university employment codes and HEC service statutes.
                    </p>
                </div>

                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    @if($formulaTotal === 100)
                        <span class="badge badge-emerald" style="font-size: 0.75rem; font-weight: 800; padding: 0.4rem 0.75rem;">
                            ✅ Total: 100% (Balanced Formula)
                        </span>
                    @else
                        <span class="badge badge-rose" style="font-size: 0.75rem; font-weight: 800; padding: 0.4rem 0.75rem;">
                            ⚠️ Total: {{ $formulaTotal }}% (Adjust to 100%)
                        </span>
                    @endif
                </div>
            </div>

            <!-- Visual Weight Distribution Bar -->
            <div style="height: 10px; width: 100%; background: var(--bg-surface); border-radius: 9999px; overflow: hidden; display: flex; margin-bottom: 1.25rem; border: 1px solid var(--border-color);">
                <div style="width: {{ min(100, (int)$merit_academic_weight) }}%; background: #4f46e5;" title="Academics: {{ $merit_academic_weight }}%"></div>
                <div style="width: {{ min(100, (int)$merit_higher_qualification_weight) }}%; background: #06b6d4;" title="Higher Qual: {{ $merit_higher_qualification_weight }}%"></div>
                <div style="width: {{ min(100, (int)$merit_research_weight) }}%; background: #10b981;" title="Research: {{ $merit_research_weight }}%"></div>
                <div style="width: {{ min(100, (int)$merit_experience_weight) }}%; background: #f59e0b;" title="Experience: {{ $merit_experience_weight }}%"></div>
                <div style="width: {{ min(100, (int)$merit_interview_weight) }}%; background: #ec4899;" title="Interview: {{ $merit_interview_weight }}%"></div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1rem;">
                <div style="padding: 1rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); border-top: 3px solid #4f46e5;">
                    <div style="font-weight: 800; font-size: 0.85rem; color: var(--color-text-primary);">Academics Record</div>
                    <div style="font-size: 0.72rem; color: var(--color-text-secondary); margin-bottom: 0.5rem;">Secondary to Master Degrees</div>
                    <div style="display: flex; align-items: center; gap: 0.4rem;">
                        <input type="number" wire:model.live="merit_academic_weight" min="0" max="100" class="form-control" style="font-weight: 800; font-size: 1rem; text-align: center; width: 85px;">
                        <span style="font-weight: 700;">%</span>
                    </div>
                </div>

                <div style="padding: 1rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); border-top: 3px solid #06b6d4;">
                    <div style="font-weight: 800; font-size: 0.85rem; color: var(--color-text-primary);">Higher Qualification</div>
                    <div style="font-size: 0.72rem; color: var(--color-text-secondary); margin-bottom: 0.5rem;">Extra M.Phil / Ph.D Degrees</div>
                    <div style="display: flex; align-items: center; gap: 0.4rem;">
                        <input type="number" wire:model.live="merit_higher_qualification_weight" min="0" max="100" class="form-control" style="font-weight: 800; font-size: 1rem; text-align: center; width: 85px;">
                        <span style="font-weight: 700;">%</span>
                    </div>
                </div>

                <div style="padding: 1rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); border-top: 3px solid #10b981;">
                    <div style="font-weight: 800; font-size: 0.85rem; color: var(--color-text-primary);">Research Papers</div>
                    <div style="font-size: 0.72rem; color: var(--color-text-secondary); margin-bottom: 0.5rem;">HEC W/X/Y Publications</div>
                    <div style="display: flex; align-items: center; gap: 0.4rem;">
                        <input type="number" wire:model.live="merit_research_weight" min="0" max="100" class="form-control" style="font-weight: 800; font-size: 1rem; text-align: center; width: 85px;">
                        <span style="font-weight: 700;">%</span>
                    </div>
                </div>

                <div style="padding: 1rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); border-top: 3px solid #f59e0b;">
                    <div style="font-weight: 800; font-size: 0.85rem; color: var(--color-text-primary);">Experience Tenure</div>
                    <div style="font-size: 0.72rem; color: var(--color-text-secondary); margin-bottom: 0.5rem;">Teaching &amp; Professional Service</div>
                    <div style="display: flex; align-items: center; gap: 0.4rem;">
                        <input type="number" wire:model.live="merit_experience_weight" min="0" max="100" class="form-control" style="font-weight: 800; font-size: 1rem; text-align: center; width: 85px;">
                        <span style="font-weight: 700;">%</span>
                    </div>
                </div>

                <div style="padding: 1rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); border-top: 3px solid #ec4899;">
                    <div style="font-weight: 800; font-size: 0.85rem; color: var(--color-text-primary);">Selection Board</div>
                    <div style="font-size: 0.72rem; color: var(--color-text-secondary); margin-bottom: 0.5rem;">Live Interview &amp; Presentation</div>
                    <div style="display: flex; align-items: center; gap: 0.4rem;">
                        <input type="number" wire:model.live="merit_interview_weight" min="0" max="100" class="form-control" style="font-weight: 800; font-size: 1rem; text-align: center; width: 85px;">
                        <span style="font-weight: 700;">%</span>
                    </div>
                </div>

                <div style="padding: 1rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); border-top: 3px solid #64748b;">
                    <div style="font-weight: 800; font-size: 0.85rem; color: var(--color-text-primary);">Pass Qualifying Cutoff</div>
                    <div style="font-size: 0.72rem; color: var(--color-text-secondary); margin-bottom: 0.5rem;">Minimum Passing Aggregate</div>
                    <div style="display: flex; align-items: center; gap: 0.4rem;">
                        <input type="number" wire:model.live="merit_minimum_passing_aggregate" min="1" max="100" class="form-control" style="font-weight: 800; font-size: 1rem; text-align: center; width: 85px;">
                        <span style="font-weight: 700;">%</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- HEC FACULTY RECRUITMENT STANDARDS & HJRS JOURNAL SCORING --}}
        <div id="setting-academic-hec-hjrs" style="margin-top: 2.25rem; border-top: 1px solid var(--border-color); padding-top: 1.75rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                        <span class="badge badge-amber" style="font-size: 0.72rem; font-weight: 800;">HEC STATUTES</span>
                        <span class="badge badge-cyan" style="font-size: 0.72rem; font-weight: 700;">HJRS RECOGNITION</span>
                    </div>
                    <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: var(--color-text-primary);">
                        HEC Faculty Eligibility Criteria &amp; HJRS Publication Weights
                    </h3>
                    <p style="margin: 0.25rem 0 0 0; font-size: 0.83rem; color: var(--color-text-secondary);">
                        Enforce mandatory 18-year terminal PhD standards, foreign equivalence vetting, and Higher Education Journal Recognition System (HJRS) category scoring.
                    </p>
                </div>
            </div>

            <!-- Toggles Row: PhD Standard & Foreign Equivalence -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1rem; margin-bottom: 1.25rem;">
                <!-- 1. Mandatory PhD for Faculty -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.15rem; background: var(--bg-surface); border: 1px solid {{ $hec_minimum_phd_criteria_enabled ? 'var(--primary-glow)' : 'var(--border-color)' }}; border-radius: var(--radius-sm);">
                    <div style="max-width: 75%;">
                        <div style="font-weight: 800; font-size: 0.88rem; color: var(--color-text-primary);">
                            Mandatory 18-Year Terminal PhD for Faculty
                        </div>
                        <div style="font-size: 0.75rem; color: var(--color-text-secondary); margin-top: 0.2rem;">
                            Enforces HEC statutory requirement of earned PhD for Associate Professor and Professor appointments.
                        </div>
                    </div>
                    <label style="position: relative; display: inline-block; width: 48px; height: 26px; cursor: pointer;">
                        <input type="checkbox" wire:model.live="hec_minimum_phd_criteria_enabled" style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $hec_minimum_phd_criteria_enabled ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $hec_minimum_phd_criteria_enabled ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                            <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $hec_minimum_phd_criteria_enabled ? '24px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>
                        </span>
                    </label>
                </div>

                <!-- 2. Foreign Degree Equivalence -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.15rem; background: var(--bg-surface); border: 1px solid {{ $foreign_degree_equivalence_mandatory ? 'var(--primary-glow)' : 'var(--border-color)' }}; border-radius: var(--radius-sm);">
                    <div style="max-width: 75%;">
                        <div style="font-weight: 800; font-size: 0.88rem; color: var(--color-text-primary);">
                            Mandatory HEC / IBCC Equivalence Certificate
                        </div>
                        <div style="font-size: 0.75rem; color: var(--color-text-secondary); margin-top: 0.2rem;">
                            Disqualifies candidate at scrutiny if foreign degree lacks validated HEC equivalence document.
                        </div>
                    </div>
                    <label style="position: relative; display: inline-block; width: 48px; height: 26px; cursor: pointer;">
                        <input type="checkbox" wire:model.live="foreign_degree_equivalence_mandatory" style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $foreign_degree_equivalence_mandatory ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $foreign_degree_equivalence_mandatory ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                            <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $foreign_degree_equivalence_mandatory ? '24px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>
                        </span>
                    </label>
                </div>

                <!-- 3. HEC HJRS Journal Lookup & Predatory Filter -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.15rem; background: var(--bg-surface); border: 1px solid {{ $enable_hjrs_journal_lookup ? 'var(--primary-glow)' : 'var(--border-color)' }}; border-radius: var(--radius-sm);">
                    <div style="max-width: 75%;">
                        <div style="font-weight: 800; font-size: 0.88rem; color: var(--color-text-primary);">
                            HEC HJRS Journal ISSN Lookup & Predatory Filter
                        </div>
                        <div style="font-size: 0.75rem; color: var(--color-text-secondary); margin-top: 0.2rem;">
                            Validates candidate research publications against official HEC HJRS categories (W, X, Y) and flags predatory journals.
                        </div>
                    </div>
                    <label style="position: relative; display: inline-block; width: 48px; height: 26px; cursor: pointer;">
                        <input type="checkbox" wire:model.live="enable_hjrs_journal_lookup" style="opacity: 0; width: 0; height: 0;">
                        <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $enable_hjrs_journal_lookup ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $enable_hjrs_journal_lookup ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                            <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $enable_hjrs_journal_lookup ? '24px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- HJRS Journal Category Scoring Weights -->
            <div style="margin-bottom: 1.25rem;">
                <div style="font-size: 0.82rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 0.65rem; letter-spacing: 0.05em;">
                    HJRS Recognized Journal Publication Scoring Weights (Marks per Paper)
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1rem;">
                    <div style="padding: 1rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); border-left: 3px solid #10b981;">
                        <div style="font-weight: 800; font-size: 0.85rem; color: var(--color-text-primary);">Category W (Impact Factor)</div>
                        <div style="font-size: 0.72rem; color: var(--color-text-secondary); margin-bottom: 0.5rem;">Highest Tier HEC Journal</div>
                        <div style="display: flex; align-items: center; gap: 0.4rem;">
                            <input type="number" step="0.5" min="0" max="20" wire:model="publication_category_w_weight" class="form-control" style="font-weight: 800; font-size: 1rem; text-align: center; width: 85px;">
                            <span style="font-size: 0.8rem; font-weight: 700; color: var(--color-text-secondary);">Marks/Paper</span>
                        </div>
                    </div>

                    <div style="padding: 1rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); border-left: 3px solid #3b82f6;">
                        <div style="font-weight: 800; font-size: 0.85rem; color: var(--color-text-primary);">Category X</div>
                        <div style="font-size: 0.72rem; color: var(--color-text-secondary); margin-bottom: 0.5rem;">Mid-Tier Peer Reviewed</div>
                        <div style="display: flex; align-items: center; gap: 0.4rem;">
                            <input type="number" step="0.5" min="0" max="20" wire:model="publication_category_x_weight" class="form-control" style="font-weight: 800; font-size: 1rem; text-align: center; width: 85px;">
                            <span style="font-size: 0.8rem; font-weight: 700; color: var(--color-text-secondary);">Marks/Paper</span>
                        </div>
                    </div>

                    <div style="padding: 1rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); border-left: 3px solid #f59e0b;">
                        <div style="font-weight: 800; font-size: 0.85rem; color: var(--color-text-primary);">Category Y</div>
                        <div style="font-size: 0.72rem; color: var(--color-text-secondary); margin-bottom: 0.5rem;">Standard Recognized Journal</div>
                        <div style="display: flex; align-items: center; gap: 0.4rem;">
                            <input type="number" step="0.5" min="0" max="20" wire:model="publication_category_y_weight" class="form-control" style="font-weight: 800; font-size: 1rem; text-align: center; width: 85px;">
                            <span style="font-size: 0.8rem; font-weight: 700; color: var(--color-text-secondary);">Marks/Paper</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Minimum Publication Thresholds by Cadre -->
            <div>
                <div style="font-size: 0.82rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); margin-bottom: 0.65rem; letter-spacing: 0.05em;">
                    HEC Minimum Publication Thresholds by Academic Cadre
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1rem;">
                    <div style="padding: 1rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm);">
                        <div style="font-weight: 800; font-size: 0.85rem; color: var(--color-text-primary);">Assistant Professor</div>
                        <div style="font-size: 0.72rem; color: var(--color-text-secondary); margin-bottom: 0.5rem;">HEC Minimum Threshold</div>
                        <div style="display: flex; align-items: center; gap: 0.4rem;">
                            <input type="number" min="0" max="50" wire:model="minimum_publications_asst_prof" class="form-control" style="font-weight: 800; font-size: 1rem; text-align: center; width: 85px;">
                            <span style="font-size: 0.8rem; font-weight: 700; color: var(--color-text-secondary);">Papers (0=Entry PhD)</span>
                        </div>
                    </div>

                    <div style="padding: 1rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm);">
                        <div style="font-weight: 800; font-size: 0.85rem; color: var(--color-text-primary);">Associate Professor</div>
                        <div style="font-size: 0.72rem; color: var(--color-text-secondary); margin-bottom: 0.5rem;">Statutory Minimum (HEC)</div>
                        <div style="display: flex; align-items: center; gap: 0.4rem;">
                            <input type="number" min="0" max="50" wire:model="minimum_publications_assoc_prof" class="form-control" style="font-weight: 800; font-size: 1rem; text-align: center; width: 85px;">
                            <span style="font-size: 0.8rem; font-weight: 700; color: var(--color-text-secondary);">Papers Required</span>
                        </div>
                    </div>

                    <div style="padding: 1rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm);">
                        <div style="font-weight: 800; font-size: 0.85rem; color: var(--color-text-primary);">Full Professor</div>
                        <div style="font-size: 0.72rem; color: var(--color-text-secondary); margin-bottom: 0.5rem;">Apex Chair Minimum (HEC)</div>
                        <div style="display: flex; align-items: center; gap: 0.4rem;">
                            <input type="number" min="0" max="100" wire:model="minimum_publications_prof" class="form-control" style="font-weight: 800; font-size: 1rem; text-align: center; width: 85px;">
                            <span style="font-size: 0.8rem; font-weight: 700; color: var(--color-text-secondary);">Papers Required</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: gap: 1rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
            @can('manage-settings')
            <button type="button" 
                    wire:click="resetAcademicCriteriaToDefault" 
                    wire:confirm="Reset academic criteria and peer review quotas to HEC benchmark defaults?"
                    class="btn btn-secondary btn-sm" 
                    style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/></svg>
                <span>Reset HEC Criteria</span>
            </button>

            <button type="submit" 
                    class="btn btn-primary" 
                    style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.75rem; font-weight: 800; font-size: 0.92rem; box-shadow: 0 4px 14px var(--primary-glow);">
                <span wire:loading.remove wire:target="saveAcademicCriteriaSettings">Save Statutory Academic Benchmarks</span>
                <span wire:loading wire:target="saveAcademicCriteriaSettings">Saving Academic Benchmarks...</span>
            </button>
            @endcan
        </div>
    </form>
</div>
