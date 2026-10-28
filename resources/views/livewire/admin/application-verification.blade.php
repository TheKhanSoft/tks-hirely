<div class="split-screen">
    <!-- Left Pane: Verification Panel & Checklist -->
    <div class="split-left">
        <div class="card mb-4" style="border-color: var(--secondary-glow);">
            <h2 class="card-title">🔍 Scrutiny Checklist</h2>
            <p class="card-subtitle">Verify details for <strong>{{ trim(($application->user->basicInfo?->first_name ?? '') . ' ' . ($application->user->basicInfo?->last_name ?? '')) }}</strong></p>
            <p style="font-size: 0.9rem; color: var(--color-text-secondary);">
                Campaign: <strong>{{ $application->campaign->title }}</strong> (Track: {{ $application->campaign->track_type }})
            </p>
        </div>

        <!-- 1. Bio-Data Checklist -->
        <div class="card mb-4">
            <h3 class="card-title mb-2" style="font-size: 1.1rem; color: var(--secondary);">1. Bio-Data & CNIC Verification</h3>
            <div class="flex-col gap-2 mb-4" style="font-size: 0.95rem;">
                <div><span style="color: var(--color-text-secondary);">Full Name:</span> {{ trim(($application->user->basicInfo?->first_name ?? '') . ' ' . ($application->user->basicInfo?->last_name ?? '')) }}</div>
                <div><span style="color: var(--color-text-secondary);">CNIC Number:</span> <code style="color: var(--color-text-primary);">{{ $application->user->basicInfo?->cnic_number }}</code></div>
                <div><span style="color: var(--color-text-secondary);">DOB:</span> {{ $application->user->basicInfo?->date_of_birth?->format('Y-m-d') }}</div>
                <div><span style="color: var(--color-text-secondary);">Quota Selected:</span> <span class="badge badge-appealed">{{ $application->user->basicInfo?->quota ?? 'General' }}</span></div>
            </div>
            
            @if(isset($application->user->basicInfo?->dynamic_attributes['disability_cert_path']))
                <button type="button" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;" 
                    wire:click="viewDocument('Disability Certificate', '{{ $application->user->basicInfo->dynamic_attributes['disability_cert_path'] }}', 'CERTIFICATE')">
                    📄 View Disability Certificate
                </button>
            @endif

            <div style="margin-top: 1rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" wire:model="verified_biodata" style="width: 1.2rem; height: 1.2rem;">
                    <span style="font-size: 0.95rem; font-weight: 600;">Confirm Bio-data & CNIC format match.</span>
                </label>
            </div>
        </div>

        <!-- 2. Academic Degrees -->
        <div class="card mb-4">
            <h3 class="card-title mb-2" style="font-size: 1.1rem; color: var(--secondary);">2. Degrees Checklist</h3>
            <div class="table-container mb-4">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Level</th>
                            <th>Degree</th>
                            <th>Country</th>
                            <th>Institution</th>
                            <th>GPA / Scale</th>
                            <th>Proof</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($application->user->basicInfo?->degrees ?? [] as $deg)
                            <tr>
                                <td><span class="badge badge-appealed" style="font-size: 0.75rem; text-transform: uppercase;">{{ str_replace('_', ' ', $deg['degree_level'] ?? 'N/A') }}</span></td>
                                <td><strong>{{ $deg['degree_title'] }}</strong> ({{ $deg['year'] }})</td>
                                <td>{{ $deg['country'] ?? 'Pakistan' }}</td>
                                <td>{{ $deg['institution'] }}</td>
                                <td><strong>{{ $deg['gpa'] }}</strong> / {{ $deg['scale'] ?? '4.0' }}</td>
                                <td>
                                    @if(isset($deg['proof_path']))
                                        <button type="button" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;"
                                            wire:click="viewDocument('Degree Transcript - {{ $deg['degree_title'] }}', '{{ $deg['proof_path'] }}', 'TRANSCRIPT')">
                                            📄 View
                                        </button>
                                    @else
                                        <span style="color: var(--color-text-muted);">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">No degrees uploaded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" wire:model="verified_degrees" style="width: 1.2rem; height: 1.2rem;">
                <span style="font-size: 0.95rem; font-weight: 600;">Confirm all uploaded transcripts align.</span>
            </label>
        </div>

        <!-- 3. Academic Medals Ledger -->
        @if($application->campaign->track_type !== 'SUPPORT')
            <div class="card mb-4">
                <h3 class="card-title mb-2" style="font-size: 1.1rem; color: var(--secondary);">3. Academic Medals Ledger</h3>
                <div class="table-container mb-4">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Degree</th>
                                <th>Type</th>
                                <th>Proof</th>
                                <th>Verify Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($application->user->basicInfo?->medals ?? [] as $medal)
                                <tr>
                                    <td>{{ $medal->degree_title }} ({{ $medal->year_awarded }})</td>
                                    <td><span class="badge badge-appealed">{{ $medal->medal_type }}</span></td>
                                    <td>
                                        <button type="button" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;"
                                            wire:click="viewDocument('Academic Medal Proof - {{ $medal->medal_type }}', '{{ $medal->transcription_proof_path }}', 'MEDAL')">
                                            📄 View Proof
                                        </button>
                                    </td>
                                    <td>
                                        <button type="button" class="btn {{ $medal->is_verified ? 'btn-success' : 'btn-secondary' }}" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;"
                                            wire:click="toggleVerifyMedal({{ $medal->id }})">
                                            {{ $medal->is_verified ? '✓ Verified' : 'Verify Medal' }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center">No medals logged.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- 4. Academic Publications -->
        @if($application->campaign->track_type === 'ACADEMIC')
            <div class="card mb-4">
                <h3 class="card-title mb-2" style="font-size: 1.1rem; color: var(--secondary);">4. Publications Verification</h3>
                <div class="table-container mb-4">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Journal / ISSN</th>
                                <th>HJRS</th>
                                <th>Verify Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($application->user->basicInfo?->publications ?? [] as $pub)
                                <tr>
                                    <td><strong>{{ $pub->title }}</strong></td>
                                    <td>{{ $pub->journal_name }} <br><small>{{ $pub->issn_print }}</small></td>
                                    <td><span class="badge badge-appealed">{{ $pub->hjrs_category }} (IF: {{ $pub->impact_factor }})</span></td>
                                    <td>
                                        <button type="button" class="btn {{ $pub->is_verified ? 'btn-success' : 'btn-secondary' }}" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;"
                                            wire:click="toggleVerifyPublication({{ $pub->id }})">
                                            {{ $pub->is_verified ? '✓ Verified' : 'Verify' }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center">No publications logged.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- 5. Research Projects -->
        @if($application->campaign->track_type === 'ACADEMIC')
            <div class="card mb-4">
                <h3 class="card-title mb-2" style="font-size: 1.1rem; color: var(--secondary);">5. Research Projects & Turnitin Index</h3>
                <div class="table-container mb-4">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Project Title</th>
                                <th>Amount</th>
                                <th>Turnitin Similarity</th>
                                <th>Verify Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($application->user->basicInfo?->researchProjects ?? [] as $proj)
                                <tr>
                                    <td>{{ $proj->project_title }} <br><small>Agency: {{ $proj->funding_agency }}</small></td>
                                    <td>PKR {{ number_format($proj->grant_amount, 2) }} M</td>
                                    <td>
                                        <span style="font-weight: 700; color: {{ $proj->turnitin_similarity_percentage < 19 ? 'var(--success)' : 'var(--danger)' }}">
                                            {{ $proj->turnitin_similarity_percentage }}%
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn {{ $proj->is_verified ? 'btn-success' : 'btn-secondary' }}" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;"
                                            wire:click="toggleVerifyProject({{ $proj->id }})">
                                            {{ $proj->is_verified ? '✓ Verified' : 'Verify' }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center">No projects logged.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- 6. Professional Events -->
        @if($application->campaign->track_type !== 'SUPPORT')
            <div class="card mb-4">
                <h3 class="card-title mb-2" style="font-size: 1.1rem; color: var(--secondary);">6. Seminars, Conferences & Workshops</h3>
                <div class="table-container mb-4">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Event Name</th>
                                <th>Role</th>
                                <th>Proof</th>
                                <th>Verify Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($application->user->basicInfo?->events ?? [] as $event)
                                <tr>
                                    <td>{{ $event->event_name }} ({{ $event->event_type }})</td>
                                    <td><span class="badge badge-appealed">{{ str_replace('_', ' ', $event->event_role) }}</span></td>
                                    <td>
                                        <button type="button" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;"
                                            wire:click="viewDocument('Event Certificate - ' . $event->event_name, '{{ $event->certificate_proof_path }}', 'CERTIFICATE')">
                                            📄 View
                                        </button>
                                    </td>
                                    <td>
                                        <button type="button" class="btn {{ $event->is_verified ? 'btn-success' : 'btn-secondary' }}" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;"
                                            wire:click="toggleVerifyEvent({{ $event->id }})">
                                            {{ $event->is_verified ? '✓ Verified' : 'Verify' }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center">No professional events activity logged.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Main Scrutiny Actions -->
        <div class="card flex gap-4" style="border-top: 1px solid var(--border-color); margin-top: 2rem;">
            <button type="button" class="btn btn-success" wire:click="markEligible" style="flex: 1;">
                ✓ Candidate is Eligible
            </button>
            <button type="button" class="btn btn-danger" wire:click="openDisqualificationForm" style="flex: 1;">
                ✗ Mark Ineligible (Disqualify)
            </button>
        </div>
    </div>

    <!-- Right Pane: Dynamic Sandbox Document Viewer -->
    <div class="split-right">
        <div class="card" style="height: 100%; min-height: 500px; display: flex; flex-col; justify-between; border-color: var(--secondary-glow);">
            <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;" class="mb-4">
                <h3 style="color: var(--secondary); font-size: 1.25rem;">📄 Dynamic Document Sandbox Viewer</h3>
                <p style="font-size: 0.85rem; color: var(--color-text-secondary);">Currently displaying: <strong>{{ $viewingDocName }}</strong></p>
            </div>

            <!-- Virtual Certificate Renderer -->
            <div style="flex: 1; background: rgba(15,23,42,0.6); border: 1px solid var(--border-color); border-radius: 8px; padding: 2rem; display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative;">
                @if($viewingDocPath)
                    <div style="text-align: center; border: 2px solid var(--secondary-glow); padding: 2rem; border-radius: 8px; width: 90%; background: var(--bg-card); box-shadow: 0 0 20px rgba(6,182,212,0.1);">
                        <h2 style="font-family: serif; color: var(--secondary); font-size: 2rem; font-weight: 400; letter-spacing: 1px;">UNIVERSITY RECRUITMENT</h2>
                        <h4 style="text-transform: uppercase; letter-spacing: 3px; font-size: 0.8rem; margin: 1rem 0; color: var(--color-text-secondary);">Official Attestation Proof</h4>
                        
                        <div style="border-top: 1.5px solid var(--border-color); border-bottom: 1.5px solid var(--border-color); padding: 1.5rem 0; margin: 1.5rem 0;">
                            <p style="font-style: italic; color: var(--color-text-secondary); font-size: 1rem;">This document confirms the verification validation of:</p>
                            <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--color-text-primary); margin: 0.5rem 0;">
                                {{ $application->user->basicInfo?->first_name }} {{ $application->user->basicInfo?->last_name }}
                            </h3>
                            <p style="font-size: 0.95rem; color: var(--color-text-secondary); font-family: monospace;">CNIC: {{ $application->user->basicInfo?->cnic_number }}</p>
                        </div>

                        <p style="font-size: 0.9rem; color: var(--color-text-secondary);">Document Reference ID: <code style="color: var(--secondary);">{{ strtoupper(substr(md5($viewingDocName), 0, 12)) }}</code></p>
                        <p style="font-size: 0.85rem; color: var(--color-text-muted); margin-top: 0.5rem;">File Path: {{ $viewingDocPath }}</p>
                        
                        <!-- Watermark Seal -->
                        <div style="position: absolute; bottom: 3rem; right: 3rem; border: 2px double var(--secondary-glow); padding: 0.5rem 1rem; border-radius: 50%; color: var(--secondary-glow); transform: rotate(-15deg); font-weight: 800; font-size: 0.75rem; letter-spacing: 1px;">
                            OFFICIAL VERIFICATION
                        </div>
                    </div>
                @else
                    <div style="text-align: center; color: var(--color-text-muted);">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 4rem; height: 4rem; margin: 0 auto 1rem auto; display: block;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p style="font-size: 1rem;">Select any document proof on the left panel to inspect it inside this viewer.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Disqualification Reason Modal -->
    @if($showDisqualificationModal)
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 1000;">
            <div class="card" style="width: 100%; max-width: 550px; border-color: var(--danger); box-shadow: 0 0 35px var(--danger-glow);">
                <h3 class="card-title mb-4" style="color: var(--danger);">✗ Confirm Candidate Disqualification</h3>
                <p style="color: var(--color-text-secondary); font-size: 0.9rem;" class="mb-4">
                    Please enter the official disqualification reason. This will open a 7-day appellate window for the candidate, letting them lodge an appeal.
                </p>

                <form wire:submit.prevent="markIneligible">
                    <div class="form-group">
                        <label class="form-label">Disqualification Reason / Reference Code *</label>
                        <textarea class="form-control" rows="4" wire:model="disqualification_reason" placeholder="e.g. HEC Equivalence certificate missing or Turnitin similarity index exceeds 19% limit."></textarea>
                        @error('disqualification_reason') <span style="color: var(--danger); font-size: 0.85rem;">{{ $message }}</span> @enderror
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
