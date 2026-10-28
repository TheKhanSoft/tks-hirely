<div style="max-width: 800px; margin: 0 auto;">
    <div class="card mb-6">
        <h2 class="card-title">⭐ Live Selection Board Grading</h2>
        <p class="card-subtitle">Evaluate candidate qualifications and record interview scores in real time.</p>
    </div>

    <!-- Candidate Summary Card -->
    <div class="card mb-6" style="background: rgba(15, 23, 42, 0.4); border-color: var(--secondary-glow);">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <span style="color: var(--color-text-secondary); font-size: 0.9rem;">Candidate Name:</span>
                <h3 style="color: var(--color-text-primary); font-size: 1.5rem; font-weight: 700;">
                    {{ $application->user->basicInfo?->first_name }} {{ $application->user->basicInfo?->last_name }}
                </h3>
            </div>
            <div>
                <span style="color: var(--color-text-secondary); font-size: 0.9rem;">Position Applied:</span>
                <h3 style="color: var(--secondary); font-size: 1.25rem; font-weight: 600;">
                    {{ $application->campaign->title }}
                </h3>
                <small style="color: var(--color-text-secondary);">Campaign Job Code: {{ $application->campaign->job_code }} (Track: {{ $application->campaign->track_type }})</small>
            </div>
        </div>
    </div>

    <!-- Grading Sheet Form -->
    <div class="card">
        <h3 class="card-title mb-6" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">Score Evaluation Sheet (Total: 100 Marks)</h3>
        
        <form wire:submit.prevent="saveEvaluation">
            
            <!-- 1. Academic Score (Subjective adjustment base) -->
            <div class="form-group mb-6">
                <label class="form-label" style="display: flex; justify-between; align-items: center;">
                    <span>1. Academic Score (Base Credentials)</span>
                    <span style="color: var(--color-text-muted); font-size: 0.8rem;">Max: 100 Marks</span>
                </label>
                <input type="number" step="0.1" class="form-control" wire:model.live="academic_score">
                @error('academic_score') <span style="color: var(--danger); font-size: 0.85rem;">{{ $message }}</span> @enderror
            </div>

            <!-- Pre-calculated Ledger Marks (Objective) -->
            <div style="background: rgba(255,255,255,0.02); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border-color);" class="mb-6">
                <h4 style="color: var(--secondary); font-size: 0.95rem; font-weight: 700;" class="mb-4">2. Verified Objective Ledger Merit Scores (Calculated Automatically)</h4>
                
                <div class="flex-col gap-4">
                    <!-- Medals Score -->
                    <div class="flex justify-between align-center" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                        <div>
                            <span style="font-weight: 600;">Academic Medals Score</span><br>
                            <small style="color: var(--color-text-secondary);">Gold = 5, Silver = 3, Bronze = 2 (Summed for verified awards)</small>
                        </div>
                        <h4 style="color: var(--color-text-primary); font-size: 1.25rem;">+ {{ number_format($medals_score, 2) }} Marks</h4>
                    </div>

                    <!-- Publications Score -->
                    <div class="flex justify-between align-center" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                        <div>
                            <span style="font-weight: 600;">Research Publications Score</span><br>
                            <small style="color: var(--color-text-secondary);">HEC HJRS Categorized: W = 1.00, X = 0.75, Y = 0.50, Z = 0.15 weights</small>
                        </div>
                        <h4 style="color: var(--color-text-primary); font-size: 1.25rem;">+ {{ number_format($publications_score, 2) }} Marks</h4>
                    </div>

                    <!-- Projects Score -->
                    <div class="flex justify-between align-center" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                        <div>
                            <span style="font-weight: 600;">Research Projects & Grants Score</span><br>
                            <small style="color: var(--color-text-secondary);">1 Mark per 1.0M PKR as Principal Investigator (Max cap: 13.00 Marks)</small>
                        </div>
                        <h4 style="color: var(--color-text-primary); font-size: 1.25rem;">+ {{ number_format($project_score, 2) }} Marks</h4>
                    </div>

                    <!-- Events Score -->
                    <div class="flex justify-between align-center">
                        <div>
                            <span style="font-weight: 600;">Seminars & Workshops Organizing Score</span><br>
                            <small style="color: var(--color-text-secondary);">Organizer Leader = 3.00, Associate = 1.50, Presenter = 1.00/0.50, Attendee = 0.10</small>
                        </div>
                        <h4 style="color: var(--color-text-primary); font-size: 1.25rem;">+ {{ number_format($events_score, 2) }} Marks</h4>
                    </div>
                </div>
            </div>

            <!-- 3. Interview Score (subjective Board grading) -->
            <div class="form-group mb-6">
                <label class="form-label" style="display: flex; justify-between; align-items: center;">
                    <span>3. Selection Board Interview Score</span>
                    <span style="color: var(--color-text-muted); font-size: 0.8rem;">Max: 50 Marks</span>
                </label>
                <input type="number" step="0.1" class="form-control" wire:model.live="interview_score" style="font-size: 1.5rem; font-weight: 700; color: var(--secondary);">
                @error('interview_score') <span style="color: var(--danger); font-size: 0.85rem;">{{ $message }}</span> @enderror
            </div>

            <!-- REAL-TIME OUTLIER ALERT SYSTEM -->
            @if($isOutlier)
                <div class="outlier-alert mb-6">
                    <h4 style="font-weight: 700; color: var(--danger);" class="mb-2">⚠️ Real-Time Outlier Alert Warning</h4>
                    <p style="font-size: 0.9rem;" class="mb-4">
                        Your entered interview score of <strong>{{ $interview_score }}</strong> deviates by more than <strong>25%</strong> from the other board panel members' running average (average mean: <strong>{{ $otherAverage }}</strong>).
                    </p>
                    
                    <div class="form-group">
                        <label class="form-label" style="color: #fca5a5;">Mandatory Outlier Written Justification *</label>
                        <textarea class="form-control" rows="3" wire:model="outlier_justification" placeholder="State your administrative reasoning for this exceptional grading score..."></textarea>
                        @error('outlier_justification') <span style="color: var(--danger); font-size: 0.85rem;">{{ $message }}</span> @enderror
                    </div>
                </div>
            @endif

            <!-- Form Actions -->
            <div class="flex gap-4" style="border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                <button type="submit" class="btn btn-success" style="flex: 1;">
                    ⭐ Submit Candidate Evaluation
                </button>
                <a href="/admin/dashboard" class="btn btn-secondary" style="flex: 1;">
                    Cancel
                </a>
            </div>

        </form>
    </div>
</div>
