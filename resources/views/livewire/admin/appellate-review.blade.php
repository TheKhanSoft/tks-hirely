<div class="split-screen">
    <!-- Left Pane: Appealed Applications List & Selected Details -->
    <div class="split-left">
        <div class="card mb-4" style="border-color: var(--warning-glow);">
            <h2 class="card-title">⚖️ Appellate Review Workspace</h2>
            <p class="card-subtitle">Review formal grievances and candidate-submitted appeals.</p>
        </div>

        @if($appeals->isEmpty())
            <div class="card text-center" style="padding: 3rem;">
                <p style="color: var(--color-text-secondary); font-size: 1.1rem;">
                    No pending candidate appeals found in the system.
                </p>
            </div>
        @else
            <!-- Appeals List -->
            <div class="card mb-4">
                <h3 class="card-title mb-4" style="font-size: 1rem; text-transform: uppercase; color: var(--warning);">Appeals Queue</h3>
                <div class="flex-col gap-2">
                    @foreach($appeals as $app)
                        <div wire:click="selectAppeal({{ $app->id }})" 
                             style="padding: 1rem; border-radius: 8px; border: 1px solid {{ $selectedAppealId == $app->id ? 'var(--warning)' : 'var(--border-color)' }}; background: {{ $selectedAppealId == $app->id ? 'rgba(245, 158, 11, 0.05)' : 'rgba(255,255,255,0.01)' }}; cursor: pointer; transition: var(--transition);"
                             class="flex justify-between align-center">
                            <div>
                                <strong>{{ $app->user->basicInfo?->first_name }} {{ $app->user->basicInfo?->last_name }}</strong><br>
                                <small style="color: var(--color-text-secondary);">Campaign: {{ $app->campaign->title }} ({{ $app->campaign->job_code }})</small>
                            </div>
                            <span class="badge badge-appealed">{{ $app->campaign->track_type }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Selected Appeal Detail -->
            @if($selectedAppeal)
                <div class="card">
                    <h3 class="card-title mb-4" style="color: var(--warning);">Grievance Details</h3>
                    
                    <div class="flex-col gap-4">
                        <!-- Disqualification Note -->
                        <div style="background: rgba(239, 68, 68, 0.08); border: 1px solid var(--danger-glow); padding: 1rem; border-radius: 8px;">
                            <h4 style="color: var(--danger); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;" class="mb-2">Original Disqualification Reason</h4>
                            <p style="font-size: 0.95rem; color: #fca5a5;">
                                {{ $selectedAppeal->disqualification_reason }}
                            </p>
                        </div>

                        <!-- Appeal Statement -->
                        <div style="background: var(--bg-table-hover); border: 1px solid var(--border-color); padding: 1rem; border-radius: 8px;">
                            <h4 style="color: var(--secondary); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;" class="mb-2">Candidate Appeal Justification</h4>
                            <p style="font-size: 0.95rem; color: var(--color-text-primary); line-height: 1.6; white-space: pre-line;">
                                "{{ $selectedAppeal->appeal_letter }}"
                            </p>
                        </div>

                        <!-- Appeal Proof Attachment Info -->
                        @if($selectedAppeal->appeal_proof_path)
                            <div class="flex align-center gap-2">
                                <span style="font-size: 0.9rem; color: var(--color-text-secondary);">Attached Proof:</span>
                                <button type="button" class="btn btn-secondary" style="padding: 0.25rem 0.75rem; font-size: 0.85rem;"
                                    wire:click="selectAppeal({{ $selectedAppeal->id }})">
                                    📄 View Proof Document
                                </button>
                            </div>
                        @endif

                        <!-- Committee Decision Actions -->
                        <div class="flex-col gap-2 mt-6" style="border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                            <h4 style="font-size: 0.95rem; font-weight: 700;" class="mb-2">Appellate Panel Verdict Actions:</h4>
                            
                            <div class="flex gap-2">
                                <button type="button" class="btn btn-success" wire:click="approveAppealToEligible" style="flex: 1;">
                                    ✓ Approve & Mark Eligible
                                </button>
                                <button type="button" class="btn btn-secondary" wire:click="approveAppealToPaid" style="flex: 1;">
                                    ♻ Approve & Reset to Scrutiny
                                </button>
                            </div>
                            
                            <button type="button" class="btn btn-danger mt-2" wire:click="rejectAppeal" style="width: 100%;">
                                ✗ Reject Appeal (Permanent Dismissal)
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>

    <!-- Right Pane: Dynamic Sandbox Appeal Document Viewer -->
    <div class="split-right">
        <div class="card" style="height: 100%; min-height: 500px; display: flex; flex-col; justify-between; border-color: var(--warning-glow);">
            <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;" class="mb-4">
                <h3 style="color: var(--warning); font-size: 1.25rem;">⚖️ Appeal Proof Document Viewer</h3>
                <p style="font-size: 0.85rem; color: var(--color-text-secondary);">Currently displaying: <strong>{{ $viewingDocName }}</strong></p>
            </div>

            <!-- Virtual Certificate Renderer -->
            <div style="flex: 1; background: rgba(15,23,42,0.6); border: 1px solid var(--border-color); border-radius: 8px; padding: 2rem; display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative;">
                @if($viewingDocPath)
                    <div style="text-align: center; border: 2px dashed var(--warning-glow); padding: 2rem; border-radius: 8px; width: 90%; background: var(--bg-card); box-shadow: 0 0 20px rgba(245,158,11,0.08);">
                        <h2 style="font-family: serif; color: var(--warning); font-size: 2rem; font-weight: 400; letter-spacing: 1px;">APPELLATE OFFICE</h2>
                        <h4 style="text-transform: uppercase; letter-spacing: 3px; font-size: 0.8rem; margin: 1rem 0; color: var(--color-text-secondary);">Corrective Evidence Proof</h4>
                        
                        <div style="border-top: 1.5px solid var(--border-color); border-bottom: 1.5px solid var(--border-color); padding: 1.5rem 0; margin: 1.5rem 0;">
                            <p style="font-style: italic; color: var(--color-text-secondary); font-size: 0.95rem;">This documentation is formally submitted as correction proof by:</p>
                            <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--color-text-primary); margin: 0.5rem 0;">
                                {{ $selectedAppeal?->user->basicInfo?->first_name }} {{ $selectedAppeal?->user->basicInfo?->last_name }}
                            </h3>
                            <p style="font-size: 0.9rem; color: var(--color-text-secondary); font-family: monospace;">Candidate Reference Application ID: #{{ $selectedAppeal?->id }}</p>
                        </div>

                        <p style="font-size: 0.85rem; color: var(--color-text-muted);">Uploaded File Path: {{ $viewingDocPath }}</p>
                        
                        <!-- Watermark Seal -->
                        <div style="position: absolute; bottom: 3rem; right: 3rem; border: 2px double var(--warning-glow); padding: 0.5rem 1rem; border-radius: 50%; color: var(--warning-glow); transform: rotate(-15deg); font-weight: 800; font-size: 0.75rem; letter-spacing: 1px;">
                            APPELLATE EVIDENCE
                        </div>
                    </div>
                @else
                    <div style="text-align: center; color: var(--color-text-muted);">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 4rem; height: 4rem; margin: 0 auto 1rem auto; display: block;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <p style="font-size: 1rem;">Select an appeal from the queue to view its proof document attachment.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
