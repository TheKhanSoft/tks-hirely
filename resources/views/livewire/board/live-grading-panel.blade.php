<div style="max-width: 860px; margin: 0 auto;">
    <!-- Header -->
    <div class="card mb-6" style="background: var(--bg-card); border-color: var(--border-color); box-shadow: var(--shadow-md);">
        <div class="flex justify-between align-center" style="flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1 class="card-title" style="font-size: 1.5rem; font-weight: 800; letter-spacing: -0.03em;">
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; background: rgba(245, 158, 11, 0.12); color: var(--warning); margin-right: 0.5rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                    </span>
                    Live Selection Board Grading Panel
                </h1>
                <p class="card-subtitle">Evaluate candidate qualifications, inspect verified merit marks, and record interview assessment scores.</p>
            </div>

            <div style="background: var(--bg-btn-secondary); border: 1px solid var(--border-color); padding: 0.45rem 0.9rem; border-radius: var(--radius-sm);" class="flex align-center gap-2">
                <span class="badge-dot" style="background: var(--warning); box-shadow: 0 0 6px var(--warning);"></span>
                <span style="font-size: 0.78rem; font-weight: 700; color: var(--color-text-secondary); text-transform: uppercase;">
                    Evaluation Desk Active
                </span>
            </div>
        </div>
    </div>

    <!-- Candidate Summary Dossier Banner -->
    <div class="card mb-6" style="border-color: var(--secondary-glow); background: linear-gradient(135deg, var(--bg-card), rgba(6, 182, 212, 0.04));">
        <div class="flex justify-between align-center" style="flex-wrap: wrap; gap: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                @if($application->user->basicInfo?->profile_picture_url)
                    <div class="user-avatar-circle" style="width: 52px; height: 52px; border-radius: 14px; overflow: hidden; padding: 0; flex-shrink: 0; border: 2px solid var(--secondary); box-shadow: var(--shadow-sm);">
                        <img src="{{ $application->user->basicInfo->profile_picture_url }}" alt="Candidate" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                @else
                    <div class="user-avatar-circle" style="width: 52px; height: 52px; font-size: 1.25rem; background: linear-gradient(135deg, var(--secondary), #0284c7);">
                        {{ strtoupper(substr($application->user->basicInfo?->first_name ?: $application->user->email, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <span style="color: var(--color-text-muted); font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Candidate Profile</span>
                    <h2 style="color: var(--color-text-primary); font-size: 1.4rem; font-weight: 800; letter-spacing: -0.02em; margin: 0.15rem 0;">
                        {{ $application->user->basicInfo?->first_name }} {{ $application->user->basicInfo?->last_name }}
                    </h2>
                    <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.82rem; color: var(--color-text-secondary);">
                        CNIC: {{ $application->user->basicInfo?->cnic_number }} &bull; {{ $application->user->email }}
                    </span>
                </div>
            </div>
            
            <div style="text-align: right;">
                <span style="color: var(--color-text-muted); font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Target Position</span>
                <div style="color: var(--secondary); font-size: 1.15rem; font-weight: 700; margin: 0.15rem 0;">
                    {{ $application->campaign->title }}
                </div>
                <div style="display: flex; gap: 0.5rem; justify-content: flex-end; align-items: center;">
                    <code style="font-family: 'JetBrains Mono', monospace; color: var(--color-text-secondary); font-size: 0.8rem; background: var(--bg-btn-secondary); padding: 0.15rem 0.5rem; border-radius: 4px;">
                        {{ $application->campaign->job_code }}
                    </code>
                    <span class="badge badge-appealed">{{ $application->campaign->track_type }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Grading Sheet Form -->
    <div class="card" style="border-color: var(--border-color); box-shadow: var(--shadow-md);">
        <div class="flex justify-between align-center mb-6" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
            <h3 class="card-title" style="font-size: 1.15rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
                Merit Score Evaluation Sheet (Total: 100 Marks)
            </h3>
            <span class="badge badge-paid" style="font-size: 0.78rem;">Verified Dossier</span>
        </div>
        
        <form wire:submit.prevent="saveEvaluation">
            <!-- 1. Academic Score -->
            <div class="form-group mb-6">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <label class="form-label" style="margin-bottom: 0; font-size: 0.88rem; font-weight: 700; color: var(--color-text-primary);">
                        1. Academic Base Score (Academic Credentials)
                    </label>
                    <span style="color: var(--color-text-muted); font-size: 0.78rem; font-weight: 600;">Max: 100 Marks</span>
                </div>
                <input type="number" step="0.1" class="form-control" wire:model.live="academic_score" style="font-size: 1.15rem; font-weight: 700; font-family: 'JetBrains Mono', monospace;">
                @error('academic_score') <span style="color: var(--danger); font-size: 0.82rem; margin-top: 0.25rem; display: block;">{{ $message }}</span> @enderror
            </div>

            <!-- Pre-calculated Ledger Marks (Objective) -->
            <div style="background: var(--bg-card-solid); padding: 1.5rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);" class="mb-6">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.25rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--secondary)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <h4 style="color: var(--secondary); font-size: 0.95rem; font-weight: 700; margin: 0;">
                        2. Verified Objective Ledger Merit Scores (Calculated Automatically)
                    </h4>
                </div>
                
                <div class="flex-col gap-4">
                    <!-- Medals Score -->
                    <div class="flex justify-between align-center" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                        <div>
                            <span style="font-weight: 600; color: var(--color-text-primary); font-size: 0.92rem;">Academic Medals Score</span><br>
                            <small style="color: var(--color-text-muted);">Gold = 5.0, Silver = 3.0, Bronze = 2.0 (Summed for verified awards)</small>
                        </div>
                        <div style="font-family: 'JetBrains Mono', monospace; color: var(--color-text-primary); font-size: 1.25rem; font-weight: 800;">
                            + {{ number_format($medals_score, 2) }} <span style="font-size: 0.8rem; color: var(--color-text-muted);">Marks</span>
                        </div>
                    </div>

                    <!-- Publications Score -->
                    <div class="flex justify-between align-center" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                        <div>
                            <span style="font-weight: 600; color: var(--color-text-primary); font-size: 0.92rem;">Research Publications Score</span><br>
                            <small style="color: var(--color-text-muted);">HEC HJRS Categorized: W = 1.00, X = 0.75, Y = 0.50, Z = 0.15 weights</small>
                        </div>
                        <div style="font-family: 'JetBrains Mono', monospace; color: var(--color-text-primary); font-size: 1.25rem; font-weight: 800;">
                            + {{ number_format($publications_score, 2) }} <span style="font-size: 0.8rem; color: var(--color-text-muted);">Marks</span>
                        </div>
                    </div>

                    <!-- Projects Score -->
                    <div class="flex justify-between align-center" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                        <div>
                            <span style="font-weight: 600; color: var(--color-text-primary); font-size: 0.92rem;">Research Projects & Grants Score</span><br>
                            <small style="color: var(--color-text-muted);">1 Mark per 1.0M PKR as Principal Investigator (Max cap: 13.00 Marks)</small>
                        </div>
                        <div style="font-family: 'JetBrains Mono', monospace; color: var(--color-text-primary); font-size: 1.25rem; font-weight: 800;">
                            + {{ number_format($project_score, 2) }} <span style="font-size: 0.8rem; color: var(--color-text-muted);">Marks</span>
                        </div>
                    </div>

                    <!-- Events Score -->
                    <div class="flex justify-between align-center">
                        <div>
                            <span style="font-weight: 600; color: var(--color-text-primary); font-size: 0.92rem;">Seminars & Workshops Organizing Score</span><br>
                            <small style="color: var(--color-text-muted);">Organizer Leader = 3.00, Associate = 1.50, Presenter = 1.00/0.50, Attendee = 0.10</small>
                        </div>
                        <div style="font-family: 'JetBrains Mono', monospace; color: var(--color-text-primary); font-size: 1.25rem; font-weight: 800;">
                            + {{ number_format($events_score, 2) }} <span style="font-size: 0.8rem; color: var(--color-text-muted);">Marks</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Interview Score -->
            <div class="form-group mb-6">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <label class="form-label" style="margin-bottom: 0; font-size: 0.88rem; font-weight: 700; color: var(--color-text-primary);">
                        3. Selection Board Interview Assessment Score
                    </label>
                    <span style="color: var(--color-text-muted); font-size: 0.78rem; font-weight: 600;">Max: 50 Marks</span>
                </div>
                <input type="number" step="0.1" class="form-control" wire:model.live="interview_score" style="font-size: 1.6rem; font-weight: 800; color: var(--secondary); font-family: 'JetBrains Mono', monospace;">
                @error('interview_score') <span style="color: var(--danger); font-size: 0.82rem; margin-top: 0.25rem; display: block;">{{ $message }}</span> @enderror
            </div>

            <!-- REAL-TIME OUTLIER ALERT SYSTEM -->
            @if($isOutlier)
                <div class="card mb-6" style="border-color: var(--danger); background: var(--danger-light); animation: beaconPulse 2.5s infinite;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--danger)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                            <line x1="12" y1="9" x2="12" y2="13"/>
                            <line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                        <h4 style="font-weight: 700; color: var(--danger); font-size: 1rem; margin: 0;">
                            Real-Time Outlier Alert Warning
                        </h4>
                    </div>
                    <p style="font-size: 0.9rem; color: #fca5a5; margin-bottom: 1rem;">
                        Your entered interview score of <strong>{{ $interview_score }}</strong> deviates by more than <strong>25%</strong> from the other board panel members' running average (average mean: <strong>{{ $otherAverage }}</strong>).
                    </p>
                    
                    <div class="form-group mb-0">
                        <label class="form-label" style="color: #fca5a5; font-weight: 700;">Mandatory Outlier Written Justification *</label>
                        <textarea class="form-control" rows="3" wire:model="outlier_justification" placeholder="State your academic and administrative reasoning for this exceptional grading score..."></textarea>
                        @error('outlier_justification') <span style="color: var(--danger); font-size: 0.82rem; margin-top: 0.25rem; display: block;">{{ $message }}</span> @enderror
                    </div>
                </div>
            @endif

            <!-- Form Actions -->
            <div class="flex gap-4" style="border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                <button type="submit" class="btn btn-success" style="flex: 1; padding: 0.85rem; font-size: 1rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <span>Submit Candidate Evaluation</span>
                </button>
                <a href="/admin/dashboard" class="btn btn-secondary" style="flex: 1; padding: 0.85rem;">
                    Cancel
                </a>
            </div>

        </form>
    </div>
</div>
