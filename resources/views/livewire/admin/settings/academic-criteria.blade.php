<div class="card mb-6" style="padding: 1.75rem; border-color: var(--primary); box-shadow: var(--shadow-md);">
    <!-- Card Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                <span class="badge badge-indigo" style="font-size: 0.72rem; font-weight: 800;">STATUTORY BENCHMARKS</span>
                <span class="badge badge-purple" style="font-size: 0.72rem; font-weight: 800;">HEC CRITERIA COMPLIANT</span>
            </div>
            <h2 class="card-title" style="margin: 0; font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary);">
                Academic Division Criteria, Minimum CGPA & Peer Review Quotas
            </h2>
            <p class="card-subtitle" style="margin: 0.25rem 0 0 0; font-size: 0.85rem; color: var(--color-text-secondary);">
                Enforce institutional academic gating thresholds, statutory degree division limits, and mandatory external expert referee quotas.
            </p>
        </div>

        @can('manage-settings')
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

    {{-- Statutory Callout --}}
    <x-help-hint type="callout" variant="statutory" statute="HEC Statutory Minimum Criteria" title="Automated Academic Dossier Eligibility Vetting">
        These rules run automatically inside the Scrutiny Engine when screening candidate transcripts. Candidates failing to satisfy these criteria are automatically flagged as ineligible with precise statutory justifications documented in the candidate summary dossier.
    </x-help-hint>

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
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.15rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    <div style="max-width: 80%;">
                        <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem;">
                            <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                                Zero 3rd Division Allowed in Academic Career
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
                            <input type="checkbox" wire:model="global_disallow_third_division" style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $global_disallow_third_division ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $global_disallow_third_division ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                                <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $global_disallow_third_division ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%;"></span>
                            </span>
                        </label>
                    </div>
                </div>

                <!-- 2. Disallow Third Division in Terminal / Highest Degree -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.15rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    <div style="max-width: 80%;">
                        <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem;">
                            <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                                Disallow 3rd Division in Terminal Degree
                            </span>
                            <x-help-hint type="inline" 
                                         variant="statutory" 
                                         statute="Standard Statutory Rule" 
                                         title="Terminal Degree 3rd Division Bar" 
                                         text="Requires the highest qualifying degree (e.g. Master, MS/MPhil, or PhD) to be 1st or 2nd Division." />
                        </div>
                        <div style="font-size: 0.8rem; color: var(--color-text-secondary); line-height: 1.45;">
                            Enforces that the highest required degree for the applied position cannot be in the 3rd Division.
                        </div>
                    </div>
                    <div>
                        <label style="position: relative; display: inline-block; width: 50px; height: 26px; cursor: pointer;">
                            <input type="checkbox" wire:model="global_disallow_third_in_terminal" style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; inset: 0; background-color: {{ $global_disallow_third_in_terminal ? 'var(--primary)' : 'rgba(148, 163, 184, 0.2)' }}; border: 1px solid {{ $global_disallow_third_in_terminal ? 'var(--primary)' : 'var(--border-color)' }}; transition: .25s; border-radius: 34px;">
                                <span style="position: absolute; content: ''; height: 18px; width: 18px; left: {{ $global_disallow_third_in_terminal ? '26px' : '4px' }}; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%;"></span>
                            </span>
                        </label>
                    </div>
                </div>

                <!-- 3. Maximum Allowed 3rd Divisions -->
                <div style="padding: 1.15rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                        <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                            Maximum Permitted 3rd Divisions
                        </span>
                        <x-help-hint type="inline" 
                                     variant="tip" 
                                     title="Allowed 3rd Division Cap" 
                                     text="Sets a numerical cap on 3rd divisions (e.g. at most 1 allowed in Matric/FSc). Leave blank for unconstrained unless zero-tolerance is on." />
                    </div>
                    <div style="font-size: 0.8rem; color: var(--color-text-secondary); margin-bottom: 0.65rem;">
                        Upper limit on total third divisions permitted in an applicant's academic trajectory.
                    </div>
                    <input type="number" 
                           wire:model="global_max_allowed_third_divisions" 
                           min="0" 
                           max="5" 
                           placeholder="Leave empty for unconstrained" 
                           class="form-control" 
                           style="font-size: 0.9rem; padding: 0.45rem 0.75rem;">
                </div>

                <!-- 4. Disallow Multiple 2nd Divisions -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.15rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    <div style="max-width: 80%;">
                        <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem;">
                            <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                                Disallow Two or More 2nd Divisions
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
                            <input type="checkbox" wire:model="global_disallow_two_second_divisions" style="opacity: 0; width: 0; height: 0;">
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
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                <span style="font-size: 1.15rem;">🎯</span>
                <h4 style="margin: 0; font-size: 1rem; font-weight: 800; color: var(--color-text-primary);">
                    Minimum Aggregate Percentage & Terminal CGPA Benchmarks
                </h4>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
                
                <!-- Min Aggregate Percentage -->
                <div style="padding: 1.15rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem;">
                        <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                            Global Minimum Academic Aggregate %
                        </span>
                        <x-help-hint type="inline" 
                                     variant="info" 
                                     title="Minimum Marks Cutoff" 
                                     text="Global minimum threshold for marks percentage across qualifying examinations." 
                                     example="e.g. 60% for 1st Division" />
                    </div>
                    <div style="font-size: 0.8rem; color: var(--color-text-secondary); margin-bottom: 0.65rem;">
                        Baseline marks threshold applied to career qualification records.
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="number" 
                               step="0.1" 
                               wire:model="global_min_marks_percentage" 
                               placeholder="e.g. 60.0" 
                               class="form-control" 
                               style="font-size: 0.92rem; padding: 0.45rem 0.75rem;">
                        <span style="font-weight: 700; color: var(--color-text-secondary);">%</span>
                    </div>
                </div>

                <!-- Min Terminal Degree Marks % -->
                <div style="padding: 1.15rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem;">
                        <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                            Terminal Degree Minimum Marks %
                        </span>
                        <x-help-hint type="inline" 
                                     variant="info" 
                                     title="Terminal Degree Marks" 
                                     text="Threshold specifically enforced on the highest required qualification." 
                                     example="e.g. 65% in MS/MPhil" />
                    </div>
                    <div style="font-size: 0.8rem; color: var(--color-text-secondary); margin-bottom: 0.65rem;">
                        Minimum required score in candidate's highest qualifying degree.
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="number" 
                               step="0.1" 
                               wire:model="global_min_terminal_percentage" 
                               placeholder="e.g. 65.0" 
                               class="form-control" 
                               style="font-size: 0.92rem; padding: 0.45rem 0.75rem;">
                        <span style="font-weight: 700; color: var(--color-text-secondary);">%</span>
                    </div>
                </div>

                <!-- Min Terminal CGPA -->
                <div style="padding: 1.15rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem;">
                        <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                            Terminal Degree Minimum CGPA (4.00 Scale)
                        </span>
                        <x-help-hint type="inline" 
                                     variant="statutory" 
                                     statute="HEC Semester Guidelines" 
                                     title="Terminal Degree CGPA Cutoff" 
                                     text="Minimum CGPA in semester system (e.g. 3.00/4.00 or 3.50/4.00 for PhD entry/faculty eligibility)." 
                                     example="Standard: 3.00" />
                    </div>
                    <div style="font-size: 0.8rem; color: var(--color-text-secondary); margin-bottom: 0.65rem;">
                        Minimum grade point average on semester transcripts.
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="number" 
                               step="0.01" 
                               wire:model="global_min_terminal_cgpa" 
                               placeholder="e.g. 3.00" 
                               class="form-control" 
                               style="font-size: 0.92rem; padding: 0.45rem 0.75rem;">
                        <span style="font-weight: 700; color: var(--color-text-secondary);">/ 4.00</span>
                    </div>
                </div>

            </div>
        </div>

        <!-- SECTION 3: External Peer Reviewer Quotas by Rank -->
        <div style="margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                <span style="font-size: 1.15rem;">👨‍🏫</span>
                <h4 style="margin: 0; font-size: 1rem; font-weight: 800; color: var(--color-text-primary);">
                    Statutory External Peer Referee Quotas by Academic Rank
                </h4>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
                
                <!-- Assistant Professor Quota -->
                <div style="padding: 1.15rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem;">
                        <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                            Assistant Professor (TTS / BPS)
                        </span>
                        <x-help-hint type="inline" 
                                     variant="statutory" 
                                     statute="TTS Statutes Sec. 2.4" 
                                     title="Assistant Professor Peer Evaluation" 
                                     text="HEC requires minimum 3 independent external evaluation reports from technologically advanced countries for Tenure Track (TTS) Assistant Professors." 
                                     example="Standard: 3 referees" />
                    </div>
                    <div style="font-size: 0.8rem; color: var(--color-text-secondary); margin-bottom: 0.65rem;">
                        Minimum mandatory external peer review dossiers to dispatch.
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="number" 
                               wire:model="asst_prof_required_peer_reviewers" 
                               min="1" 
                               max="10" 
                               class="form-control" 
                               style="width: 90px; text-align: center; font-weight: 800; font-size: 1rem;">
                        <span style="font-weight: 700; color: var(--color-text-secondary);">Experts</span>
                    </div>
                </div>

                <!-- Professor & Associate Professor Quota -->
                <div style="padding: 1.15rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem;">
                        <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                            Professor & Associate Professor
                        </span>
                        <x-help-hint type="inline" 
                                     variant="statutory" 
                                     statute="HEC Senior Chair Criteria" 
                                     title="Senior Chair Peer Quota" 
                                     text="Senior academic chairs must receive positive recommendations from independent foreign referees before appearing at the Selection Board." 
                                     example="Standard: 3 referees" />
                    </div>
                    <div style="font-size: 0.8rem; color: var(--color-text-secondary); margin-bottom: 0.65rem;">
                        Mandatory external foreign and national referee reports.
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="number" 
                               wire:model="prof_required_peer_reviewers" 
                               min="1" 
                               max="10" 
                               class="form-control" 
                               style="width: 90px; text-align: center; font-weight: 800; font-size: 1rem;">
                        <span style="font-weight: 700; color: var(--color-text-secondary);">Experts</span>
                    </div>
                </div>

                <!-- Lecturer Quota -->
                <div style="padding: 1.15rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem;">
                        <span style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary);">
                            Lecturer / Junior Teaching Cadre
                        </span>
                        <x-help-hint type="inline" 
                                     variant="tip" 
                                     title="Lecturer Evaluation" 
                                     text="Junior academic positions generally rely on scrutiny screening and written tests without external referee dispatch (0 means skipped)." 
                                     example="Standard: 0 (Skipped)" />
                    </div>
                    <div style="font-size: 0.8rem; color: var(--color-text-secondary); margin-bottom: 0.65rem;">
                        External referees required (Set 0 to skip peer review for lecturers).
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="number" 
                               wire:model="lecturer_required_peer_reviewers" 
                               min="0" 
                               max="10" 
                               class="form-control" 
                               style="width: 90px; text-align: center; font-weight: 800; font-size: 1rem;">
                        <span style="font-weight: 700; color: var(--color-text-secondary);">Experts</span>
                    </div>
                </div>

            </div>
        </div>

        <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
            <button type="submit" 
                    class="btn btn-primary" 
                    style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.75rem; font-weight: 800; font-size: 0.92rem; box-shadow: 0 4px 14px var(--primary-glow);">
                <span wire:loading.remove wire:target="saveAcademicCriteriaSettings">Save Statutory Academic Benchmarks</span>
                <span wire:loading wire:target="saveAcademicCriteriaSettings">Saving Academic Benchmarks...</span>
            </button>
        </div>
    </form>
</div>
