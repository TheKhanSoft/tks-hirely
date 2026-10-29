<div class="split-screen">
    <!-- Left Pane: Verification Panel & Checklist -->
    <div class="split-left">
        <!-- Header Card -->
        <div class="card mb-4" style="border-color: var(--secondary-glow); position: relative; overflow: hidden;">
            <div style="position: absolute; right: -2rem; top: -2rem; width: 120px; height: 120px; background: radial-gradient(circle, var(--secondary-glow) 0%, transparent 70%); pointer-events: none;"></div>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                <div class="brand-icon-box" style="width: 36px; height: 36px; border-radius: 8px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--secondary);">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <path d="m9 15 2 2 4-4"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="card-title" style="margin: 0; font-size: 1.25rem;">Candidate Scrutiny Checklist</h2>
                    <p class="card-subtitle" style="margin: 0;">
                        Dossier for <strong style="color: var(--color-text-primary);">{{ trim(($application->user->basicInfo?->first_name ?? '') . ' ' . ($application->user->basicInfo?->last_name ?? '')) }}</strong>
                    </p>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 0.75rem; font-size: 0.85rem; color: var(--color-text-secondary); padding-top: 0.5rem; border-top: 1px solid var(--border-color); flex-wrap: wrap;">
                <span>Campaign: <strong style="color: var(--color-text-primary);">{{ $application->campaign->title }}</strong></span>
                <span>•</span>
                <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary);">{{ $application->campaign->track_type }} Track</span>
                @if($application->campaign->advertisement)
                    <span>•</span>
                    <span class="badge" style="background: rgba(6,182,212,0.12); color: var(--secondary); font-weight: 800;">
                        📢 {{ $application->campaign->advertisement->advertisement_no }} (Year {{ $application->campaign->advertisement->year }})
                    </span>
                @endif
            </div>
            @if($application->campaign->track_type === 'ACADEMIC' && (auth()->user()->can('manage-reviewers') || auth()->user()->hasAnyRole(['super-admin', 'registrar'])))
                <div style="margin-top: 0.75rem; padding: 0.5rem 0.75rem; background: rgba(6,182,212,0.08); border: 1px solid var(--secondary-glow); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; color: var(--secondary);">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        <span>Academic Position • Requires Peer Review</span>
                    </div>
                    <a href="/admin/reviewers" class="btn btn-secondary btn-sm" style="padding: 0.2rem 0.6rem; font-size: 0.75rem;">
                        <span>Reviewer Desk</span>
                    </a>
                </div>
            @endif
        </div>

        <!-- 1. Bio-Data Checklist -->
        <div class="card mb-4">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                <span style="width: 22px; height: 22px; border-radius: 50%; background: rgba(6,182,212,0.15); color: var(--secondary); font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; justify-content: center;">1</span>
                <h3 class="card-title" style="margin: 0; font-size: 1.05rem; color: var(--secondary);">Bio-Data & CNIC Verification</h3>
            </div>
            
            <div class="grid grid-cols-2 gap-3 mb-4" style="font-size: 0.9rem; background: var(--bg-tertiary); padding: 0.85rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                <div><span style="color: var(--color-text-secondary);">Full Name:</span> <strong style="color: var(--color-text-primary);">{{ trim(($application->user->basicInfo?->first_name ?? '') . ' ' . ($application->user->basicInfo?->last_name ?? '')) }}</strong></div>
                <div><span style="color: var(--color-text-secondary);">CNIC Number:</span> <code style="color: var(--secondary); font-weight: 700;">{{ $application->user->basicInfo?->cnic_number }}</code></div>
                <div><span style="color: var(--color-text-secondary);">DOB:</span> <strong style="color: var(--color-text-primary);">{{ $application->user->basicInfo?->date_of_birth?->format('Y-m-d') }}</strong></div>
                <div><span style="color: var(--color-text-secondary);">Quota:</span> <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary);">{{ $application->user->basicInfo?->quota ?? 'General Merit' }}</span></div>
            </div>
            
            @if(isset($application->user->basicInfo?->dynamic_attributes['disability_cert_path']))
                <button type="button" class="btn btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.4rem; margin-bottom: 0.75rem;" 
                    wire:click="viewDocument('Disability Certificate', '{{ $application->user->basicInfo->dynamic_attributes['disability_cert_path'] }}', 'CERTIFICATE')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                    <span>Inspect Disability Certificate</span>
                </button>
            @endif

            <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer; padding: 0.5rem; background: var(--bg-card); border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                <input type="checkbox" wire:model="verified_biodata" style="width: 1.15rem; height: 1.15rem; accent-color: var(--primary);">
                <span style="font-size: 0.85rem; font-weight: 600; color: var(--color-text-primary);">Confirm Bio-data & CNIC format match official government records.</span>
            </label>
        </div>

        <!-- 2. Academic Degrees -->
        <div class="card mb-4">
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                <span style="width: 22px; height: 22px; border-radius: 50%; background: rgba(6,182,212,0.15); color: var(--secondary); font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; justify-content: center;">2</span>
                <h3 class="card-title" style="margin: 0; font-size: 1.05rem; color: var(--secondary);">Degrees & Transcripts Checklist</h3>
            </div>

            <!-- Academic Division & Marks Eligibility Audit Card -->
            @if(isset($academicEvaluation))
                <div class="card mb-3" style="background: {{ $academicEvaluation['is_eligible'] ? 'rgba(16, 185, 129, 0.08)' : 'rgba(239, 68, 68, 0.08)' }}; border: 1.5px solid {{ $academicEvaluation['is_eligible'] ? 'rgba(16, 185, 129, 0.35)' : 'rgba(239, 68, 68, 0.35)' }}; padding: 0.85rem 1rem;">
                    <div class="flex justify-between align-center flex-wrap gap-2">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-size: 1.1rem;">{{ $academicEvaluation['is_eligible'] ? '✅' : '⚠️' }}</span>
                            <div>
                                <strong style="color: {{ $academicEvaluation['is_eligible'] ? '#10b981' : '#ef4444' }}; font-size: 0.88rem;">
                                    {{ $academicEvaluation['badge']['label'] }}
                                </strong>
                                <div style="font-size: 0.76rem; color: var(--color-text-secondary);">
                                    Division breakdown: <strong>{{ $academicEvaluation['first_division_count'] }}</strong> First, <strong>{{ $academicEvaluation['second_division_count'] }}</strong> Second, <strong>{{ $academicEvaluation['third_division_count'] }}</strong> Third
                                </div>
                            </div>
                        </div>

                        <div class="flex align-center gap-2">
                            @if(!$academicEvaluation['is_eligible'])
                                <button type="button" class="btn btn-sm btn-danger" wire:click="autoFillAcademicDisqualification" style="padding: 0.3rem 0.65rem; font-size: 0.75rem; font-weight: 700;">
                                    Auto-fill Disqualification Reason
                                </button>
                            @endif
                        </div>
                    </div>

                    @if(!$academicEvaluation['is_eligible'] && !empty($academicEvaluation['violations']))
                        <div style="margin-top: 0.65rem; padding-top: 0.65rem; border-top: 1px dashed rgba(239, 68, 68, 0.25); font-size: 0.78rem; color: #ef4444;">
                            <strong style="text-transform: uppercase; font-size: 0.72rem; letter-spacing: 0.03em;">Statutory Criteria Violations:</strong>
                            <ul style="margin: 0.25rem 0 0 1.25rem; padding: 0;">
                                @foreach($academicEvaluation['violations'] as $v)
                                    <li>{{ $v }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif

            <div class="table-container mb-3">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Level</th>
                            <th>Degree Title</th>
                            <th>Institution</th>
                            <th>GPA / Scale</th>
                            <th style="text-align: right;">Proof</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($application->user->basicInfo?->degrees ?? [] as $deg)
                            <tr>
                                <td>
                                    <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary); font-size: 0.75rem; text-transform: uppercase;">
                                        {{ str_replace('_', ' ', $deg['degree_level'] ?? 'N/A') }}
                                    </span>
                                </td>
                                <td><strong>{{ $deg['degree_title'] }}</strong> <small style="color: var(--color-text-secondary)">({{ $deg['year'] }})</small></td>
                                <td>{{ $deg['institution'] }}</td>
                                <td><strong>{{ $deg['gpa'] }}</strong> / {{ $deg['scale'] ?? '4.0' }}</td>
                                <td style="text-align: right;">
                                    @if(isset($deg['proof_path']))
                                        <button type="button" class="btn btn-secondary" style="padding: 0.25rem 0.55rem; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.3rem;"
                                            wire:click="viewDocument('Degree Transcript - {{ $deg['degree_title'] }}', '{{ $deg['proof_path'] }}', 'TRANSCRIPT')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                            <span>Inspect</span>
                                        </button>
                                    @else
                                        <span style="color: var(--color-text-muted); font-size: 0.8rem;">No File</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center" style="padding: 1.5rem;">No degrees uploaded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer; padding: 0.5rem; background: var(--bg-card); border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                <input type="checkbox" wire:model="verified_degrees" style="width: 1.15rem; height: 1.15rem; accent-color: var(--primary);">
                <span style="font-size: 0.85rem; font-weight: 600; color: var(--color-text-primary);">Confirm all uploaded degree certificates and transcripts align with requirements.</span>
            </label>
        </div>

        <!-- 3. Academic Medals Ledger -->
        @if($application->campaign->track_type !== 'SUPPORT')
            <div class="card mb-4">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                    <span style="width: 22px; height: 22px; border-radius: 50%; background: rgba(6,182,212,0.15); color: var(--secondary); font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; justify-content: center;">3</span>
                    <h3 class="card-title" style="margin: 0; font-size: 1.05rem; color: var(--secondary);">Academic Medals & Distinctions</h3>
                </div>

                <div class="table-container mb-2">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Degree</th>
                                <th>Medal Distinction</th>
                                <th>Proof Document</th>
                                <th style="text-align: right;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($application->user->basicInfo?->medals ?? [] as $medal)
                                <tr>
                                    <td><strong>{{ $medal->degree_title }}</strong> <small>({{ $medal->year_awarded }})</small></td>
                                    <td>
                                        <span class="badge" style="background: {{ $medal->medal_type === 'GOLD' ? 'rgba(234,179,8,0.15)' : 'rgba(148,163,184,0.15)' }}; color: {{ $medal->medal_type === 'GOLD' ? '#eab308' : '#94a3b8' }};">
                                            {{ $medal->medal_type }} Medal
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-secondary" style="padding: 0.2rem 0.5rem; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.3rem;"
                                            wire:click="viewDocument('Academic Medal Proof - {{ $medal->medal_type }}', '{{ $medal->transcription_proof_path }}', 'MEDAL')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                            <span>Proof</span>
                                        </button>
                                    </td>
                                    <td style="text-align: right;">
                                        <button type="button" class="btn {{ $medal->is_verified ? 'btn-success' : 'btn-secondary' }}" style="padding: 0.25rem 0.55rem; font-size: 0.75rem;"
                                            wire:click="toggleVerifyMedal({{ $medal->id }})">
                                            {{ $medal->is_verified ? '✓ Verified' : 'Verify Medal' }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center" style="padding: 1.5rem;">No medals logged.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- 4. Academic Publications -->
        @if($application->campaign->track_type === 'ACADEMIC')
            <div class="card mb-4">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                    <span style="width: 22px; height: 22px; border-radius: 50%; background: rgba(6,182,212,0.15); color: var(--secondary); font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; justify-content: center;">4</span>
                    <h3 class="card-title" style="margin: 0; font-size: 1.05rem; color: var(--secondary);">Research Publications Verification</h3>
                </div>

                <div class="table-container mb-2">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Journal / ISSN</th>
                                <th>HJRS Ranking</th>
                                <th style="text-align: right;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($application->user->basicInfo?->publications ?? [] as $pub)
                                <tr>
                                    <td><strong>{{ $pub->title }}</strong></td>
                                    <td>{{ $pub->journal_name }} <br><small style="color: var(--color-text-secondary)">ISSN: {{ $pub->issn_print }}</small></td>
                                    <td>
                                        <span class="badge" style="background: rgba(6,182,212,0.12); color: var(--secondary);">
                                            {{ $pub->hjrs_category }} (IF: {{ $pub->impact_factor }})
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        <button type="button" class="btn {{ $pub->is_verified ? 'btn-success' : 'btn-secondary' }}" style="padding: 0.25rem 0.55rem; font-size: 0.75rem;"
                                            wire:click="toggleVerifyPublication({{ $pub->id }})">
                                            {{ $pub->is_verified ? '✓ Verified' : 'Verify' }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center" style="padding: 1.5rem;">No publications logged.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- 5. Research Projects -->
        @if($application->campaign->track_type === 'ACADEMIC')
            <div class="card mb-4">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                    <span style="width: 22px; height: 22px; border-radius: 50%; background: rgba(6,182,212,0.15); color: var(--secondary); font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; justify-content: center;">5</span>
                    <h3 class="card-title" style="margin: 0; font-size: 1.05rem; color: var(--secondary);">Funded Grants & Turnitin Similarity</h3>
                </div>

                <div class="table-container mb-2">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Project Title</th>
                                <th>Grant Amount</th>
                                <th>Turnitin Similarity</th>
                                <th style="text-align: right;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($application->user->basicInfo?->researchProjects ?? [] as $proj)
                                <tr>
                                    <td><strong>{{ $proj->project_title }}</strong> <br><small style="color: var(--color-text-secondary)">Agency: {{ $proj->funding_agency }}</small></td>
                                    <td><strong>PKR {{ number_format($proj->grant_amount, 2) }} M</strong></td>
                                    <td>
                                        <span style="font-weight: 700; font-family: monospace; color: {{ $proj->turnitin_similarity_percentage < 19 ? 'var(--success)' : 'var(--danger)' }};">
                                            {{ $proj->turnitin_similarity_percentage }}%
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        <button type="button" class="btn {{ $proj->is_verified ? 'btn-success' : 'btn-secondary' }}" style="padding: 0.25rem 0.55rem; font-size: 0.75rem;"
                                            wire:click="toggleVerifyProject({{ $proj->id }})">
                                            {{ $proj->is_verified ? '✓ Verified' : 'Verify' }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center" style="padding: 1.5rem;">No research projects logged.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- 6. Professional Events -->
        @if($application->campaign->track_type !== 'SUPPORT')
            <div class="card mb-4">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                    <span style="width: 22px; height: 22px; border-radius: 50%; background: rgba(6,182,212,0.15); color: var(--secondary); font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; justify-content: center;">6</span>
                    <h3 class="card-title" style="margin: 0; font-size: 1.05rem; color: var(--secondary);">Conferences & Professional Events</h3>
                </div>

                <div class="table-container mb-2">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Event Name</th>
                                <th>Role</th>
                                <th>Certificate</th>
                                <th style="text-align: right;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($application->user->basicInfo?->events ?? [] as $event)
                                <tr>
                                    <td><strong>{{ $event->event_name }}</strong> <small>({{ $event->event_type }})</small></td>
                                    <td>
                                        <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary);">
                                            {{ str_replace('_', ' ', $event->event_role) }}
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-secondary" style="padding: 0.2rem 0.5rem; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.3rem;"
                                            wire:click="viewDocument('Event Certificate - ' . $event->event_name, '{{ $event->certificate_proof_path }}', 'CERTIFICATE')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                            <span>View</span>
                                        </button>
                                    </td>
                                    <td style="text-align: right;">
                                        <button type="button" class="btn {{ $event->is_verified ? 'btn-success' : 'btn-secondary' }}" style="padding: 0.25rem 0.55rem; font-size: 0.75rem;"
                                            wire:click="toggleVerifyEvent({{ $event->id }})">
                                            {{ $event->is_verified ? '✓ Verified' : 'Verify' }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center" style="padding: 1.5rem;">No professional events activity logged.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Main Scrutiny Actions -->
        <div class="card" style="border-top: 1px solid var(--border-color); margin-top: 2rem; display: flex; gap: 1rem; box-shadow: var(--shadow-md);">
            <button type="button" class="btn btn-success" wire:click="markEligible" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem 1rem; font-weight: 700;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Candidate is Eligible</span>
            </button>
            <button type="button" class="btn btn-danger" wire:click="openDisqualificationForm" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem 1rem; font-weight: 700;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                <span>Mark Ineligible (Disqualify)</span>
            </button>
        </div>
    </div>

    <!-- Right Pane: Dynamic Sandbox Document Viewer -->
    <div class="split-right">
        <div class="card" style="height: 100%; min-height: 550px; display: flex; flex-direction: column; justify-content: space-between; border-color: var(--secondary-glow); position: sticky; top: 5rem;">
            <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;" class="mb-4">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                    <div style="width: 28px; height: 28px; border-radius: 6px; background: rgba(6,182,212,0.15); display: flex; align-items: center; justify-content: center; color: var(--secondary);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    </div>
                    <h3 style="color: var(--secondary); font-size: 1.15rem; margin: 0;">Dynamic Document Sandbox Viewer</h3>
                </div>
                <p style="font-size: 0.85rem; color: var(--color-text-secondary); margin: 0;">Currently displaying: <strong style="color: var(--color-text-primary);">{{ $viewingDocName }}</strong></p>
            </div>

            <!-- Virtual Certificate Renderer -->
            <div style="flex: 1; background: var(--bg-tertiary); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 2rem; display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative;">
                @if($viewingDocPath)
                    <div style="text-align: center; border: 2px solid var(--secondary-glow); padding: 2.5rem; border-radius: var(--radius-md); width: 92%; background: var(--bg-card); box-shadow: 0 10px 30px rgba(6,182,212,0.08); position: relative;">
                        <div style="text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.15em; font-weight: 700; color: var(--secondary); margin-bottom: 0.5rem;">University Recruitment System</div>
                        <h2 style="font-family: 'Plus Jakarta Sans', sans-serif; color: var(--color-text-primary); font-size: 1.65rem; font-weight: 800; letter-spacing: -0.02em; margin-bottom: 0.25rem;">OFFICIAL ATTESTATION PROOF</h2>
                        <p style="font-size: 0.8rem; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.1em;">Verified Document Record</p>
                        
                        <div style="border-top: 1.5px solid var(--border-color); border-bottom: 1.5px solid var(--border-color); padding: 1.5rem 0; margin: 1.5rem 0;">
                            <p style="font-style: italic; color: var(--color-text-secondary); font-size: 0.95rem; margin-bottom: 0.5rem;">This official dossier entry confirms the digital record validation of:</p>
                            <h3 style="font-size: 1.45rem; font-weight: 800; color: var(--color-text-primary); margin: 0.25rem 0;">
                                {{ $application->user->basicInfo?->first_name }} {{ $application->user->basicInfo?->last_name }}
                            </h3>
                            <p style="font-size: 0.9rem; color: var(--secondary); font-family: 'JetBrains Mono', monospace; font-weight: 700; margin: 0.25rem 0;">CNIC: {{ $application->user->basicInfo?->cnic_number }}</p>
                        </div>

                        <div style="display: flex; justify-content: center; gap: 1.5rem; font-size: 0.8rem; color: var(--color-text-secondary);">
                            <span>Doc Ref: <code style="color: var(--secondary); font-family: 'JetBrains Mono', monospace;">{{ strtoupper(substr(md5($viewingDocName), 0, 12)) }}</code></span>
                            <span>•</span>
                            <span style="font-family: monospace;">{{ $viewingDocType ?? 'CERTIFICATE' }}</span>
                        </div>
                        <p style="font-size: 0.75rem; color: var(--color-text-muted); margin-top: 0.5rem; font-family: monospace; word-break: break-all;">File: {{ $viewingDocPath }}</p>
                        
                        <!-- Watermark Seal -->
                        <div style="position: absolute; bottom: 1.5rem; right: 1.5rem; border: 2px dashed var(--secondary); padding: 0.4rem 0.8rem; border-radius: 999px; color: var(--secondary); transform: rotate(-12deg); font-weight: 800; font-size: 0.7rem; letter-spacing: 0.1em; background: rgba(6,182,212,0.05);">
                            VERIFIED DOSSIER
                        </div>
                    </div>
                @else
                    <div style="text-align: center; color: var(--color-text-muted); max-width: 320px;">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 3.5rem; height: 3.5rem; margin: 0 auto 1rem auto; opacity: 0.5;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h4 style="color: var(--color-text-primary); font-size: 1rem; margin-bottom: 0.35rem;">No Document Selected</h4>
                        <p style="font-size: 0.85rem; margin: 0;">Click "Inspect" or "View" on any certificate or transcript on the left panel to examine its credentials.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Disqualification Reason Modal -->
    @if($showDisqualificationModal)
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(12px); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 1rem;">
            <div class="card" style="width: 100%; max-width: 550px; border-color: var(--danger); box-shadow: 0 20px 40px rgba(239,68,68,0.2);">
                <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 1rem;">
                    <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(239,68,68,0.15); display: flex; align-items: center; justify-content: center; color: var(--danger);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                    </div>
                    <div>
                        <h3 class="card-title" style="margin: 0; font-size: 1.2rem; color: var(--danger);">Confirm Candidate Disqualification</h3>
                        <p class="card-subtitle" style="margin: 0;">This opens a 7-day appellate window for the applicant to lodge a formal grievance</p>
                    </div>
                </div>

                <form wire:submit.prevent="markIneligible">
                    <div class="form-group mb-4">
                        <label class="form-label">Disqualification Reason / Reference Code *</label>
                        <textarea class="form-control" rows="4" wire:model="disqualification_reason" placeholder="e.g. HEC Equivalence certificate missing or Turnitin similarity index exceeds 19% threshold.">{{ $disqualification_reason }}</textarea>
                        @error('disqualification_reason') <span style="color: var(--danger); font-size: 0.85rem; margin-top: 0.25rem; display: block;">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex gap-4 mt-6">
                        <button type="submit" class="btn btn-danger" style="flex: 1;">
                            Confirm Ineligible
                        </button>
                        <button type="button" class="btn btn-secondary" style="flex: 1;" wire:click="$set('showDisqualificationModal', false)">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
