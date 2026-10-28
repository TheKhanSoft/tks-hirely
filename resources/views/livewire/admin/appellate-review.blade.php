<div class="split-screen">
    <!-- Left Pane: Appealed Applications List & Selected Details -->
    <div class="split-left">
        <div class="card mb-4" style="border-color: var(--border-color); box-shadow: var(--shadow-sm);">
            <div class="flex justify-between align-center">
                <div>
                    <h2 class="card-title" style="font-size: 1.4rem;">
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; background: rgba(245, 158, 11, 0.12); color: var(--warning); margin-right: 0.5rem;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                                <path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                                <path d="M7 21h10"/>
                                <path d="M12 3v18"/>
                            </svg>
                        </span>
                        Appellate Review Workspace
                    </h2>
                    <p class="card-subtitle">Review candidate formal grievances and make panel decisions.</p>
                </div>
                <span class="badge badge-pending">{{ count($appeals) }} In Queue</span>
            </div>
        </div>

        @if($appeals->isEmpty())
            <div class="card text-center" style="padding: 4rem 2rem;">
                <div style="width: 56px; height: 56px; border-radius: 50%; background: var(--success-light); color: var(--success); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
                <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.25rem;">All Grievances Resolved</h3>
                <p style="color: var(--color-text-secondary); font-size: 0.9rem;">
                    No pending candidate appeals found in the system at this time.
                </p>
            </div>
        @else
            <!-- Appeals List -->
            <div class="card mb-4">
                <h3 class="card-title mb-4" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-muted);">
                    Appeals Queue ({{ count($appeals) }})
                </h3>
                <div class="flex-col gap-2">
                    @foreach($appeals as $app)
                        <div wire:click="selectAppeal({{ $app->id }})" 
                             style="padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 1px solid {{ $selectedAppealId == $app->id ? 'var(--warning)' : 'var(--border-color)' }}; background: {{ $selectedAppealId == $app->id ? 'rgba(245, 158, 11, 0.08)' : 'var(--bg-btn-secondary)' }}; cursor: pointer; transition: var(--transition-fast);"
                             class="flex justify-between align-center">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div class="user-avatar-circle" style="width: 34px; height: 34px; font-size: 0.75rem; background: linear-gradient(135deg, var(--warning), #d97706);">
                                    {{ strtoupper(substr($app->user->basicInfo?->first_name ?: $app->user->email, 0, 1)) }}
                                </div>
                                <div>
                                    <strong style="color: var(--color-text-primary); font-size: 0.92rem;">
                                        {{ $app->user->basicInfo ? trim($app->user->basicInfo->first_name . ' ' . $app->user->basicInfo->last_name) : $app->user->email }}
                                    </strong><br>
                                    <small style="color: var(--color-text-secondary);">Campaign: {{ $app->campaign->title }} ({{ $app->campaign->job_code }})</small>
                                </div>
                            </div>
                            <span class="badge badge-appealed">{{ $app->campaign->track_type }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Selected Appeal Detail -->
            @if($selectedAppeal)
                <div class="card" style="border-color: var(--border-color); box-shadow: var(--shadow-md);">
                    <div class="flex justify-between align-center mb-4" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                        <h3 class="card-title" style="color: var(--warning); font-size: 1.1rem; margin: 0;">
                            Grievance Dossier Details
                        </h3>
                        <span class="badge badge-pending">Appeal #{{ $selectedAppeal->id }}</span>
                    </div>
                    
                    <div class="flex-col gap-4">
                        <!-- Disqualification Note -->
                        <div style="background: var(--danger-light); border: 1px solid var(--danger-glow); padding: 1rem; border-radius: var(--radius-sm);">
                            <div style="display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.4rem;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--danger)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="8" x2="12" y2="12"/>
                                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                                </svg>
                                <h4 style="color: var(--danger); font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; margin: 0;">
                                    Original Scrutiny Disqualification Reason
                                </h4>
                            </div>
                            <p style="font-size: 0.92rem; color: #fca5a5; margin: 0;">
                                {{ $selectedAppeal->disqualification_reason }}
                            </p>
                        </div>

                        <!-- Appeal Statement -->
                        <div style="background: var(--bg-table-th); border: 1px solid var(--border-color); padding: 1.25rem; border-radius: var(--radius-sm);">
                            <h4 style="color: var(--secondary); font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; margin-bottom: 0.5rem;">
                                Candidate Appeal Justification
                            </h4>
                            <p style="font-size: 0.92rem; color: var(--color-text-primary); line-height: 1.6; white-space: pre-line; margin: 0; font-style: italic;">
                                "{{ $selectedAppeal->appeal_letter }}"
                            </p>
                        </div>

                        <!-- Appeal Proof Attachment Info -->
                        @if($selectedAppeal->appeal_proof_path)
                            <div class="flex align-center gap-3" style="background: var(--bg-btn-secondary); padding: 0.75rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                                <span style="font-size: 0.85rem; color: var(--color-text-secondary); font-weight: 600;">Attached Document:</span>
                                <button type="button" class="btn btn-secondary btn-sm" wire:click="selectAppeal({{ $selectedAppeal->id }})">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                    </svg>
                                    <span>Preview Proof Document</span>
                                </button>
                            </div>
                        @endif

                        <!-- Committee Decision Actions -->
                        <div class="flex-col gap-2 mt-4" style="border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                            <h4 style="font-size: 0.88rem; font-weight: 700; color: var(--color-text-primary);" class="mb-2">
                                Appellate Panel Verdict Decisions:
                            </h4>
                            
                            <div class="flex gap-2">
                                <button type="button" class="btn btn-success" wire:click="approveAppealToEligible" style="flex: 1; padding: 0.75rem;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"/>
                                    </svg>
                                    <span>Approve (Mark Eligible)</span>
                                </button>
                                <button type="button" class="btn btn-secondary" wire:click="approveAppealToPaid" style="flex: 1; padding: 0.75rem;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/>
                                        <path d="M21 3v5h-5"/>
                                        <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/>
                                        <path d="M8 16H3v5"/>
                                    </svg>
                                    <span>Approve (Re-scrutinize)</span>
                                </button>
                            </div>
                            
                            <button type="button" class="btn btn-danger mt-2" wire:click="rejectAppeal" style="width: 100%; padding: 0.75rem;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18"/>
                                    <line x1="6" y1="6" x2="18" y2="18"/>
                                </svg>
                                <span>Reject Appeal (Permanent Dismissal)</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>

    <!-- Right Pane: Dynamic Sandbox Appeal Document Viewer -->
    <div class="split-right">
        <div class="card" style="height: 100%; min-height: 520px; display: flex; flex-direction: column; justify-content: space-between; border-color: var(--border-color); box-shadow: var(--shadow-md);">
            <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;" class="mb-4">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--color-text-primary); display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--warning)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                        </svg>
                        Appellate Proof Document Viewer
                    </h3>
                    <span class="badge badge-appealed">Evidence Viewer</span>
                </div>
                <p style="font-size: 0.82rem; color: var(--color-text-secondary); margin-top: 0.25rem;">
                    Active Document: <strong>{{ $viewingDocName }}</strong>
                </p>
            </div>

            <!-- Virtual Certificate Renderer -->
            <div style="flex: 1; background: var(--bg-card-solid); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 2rem; display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative;">
                @if($viewingDocPath)
                    <div style="text-align: center; border: 2px dashed var(--warning); padding: 2.5rem 2rem; border-radius: var(--radius-md); width: 92%; background: var(--bg-primary); box-shadow: var(--shadow-glow); position: relative; overflow: hidden;">
                        <div style="color: var(--warning); font-size: 0.8rem; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 0.5rem;">
                            OFFICIAL APPELLATE EVIDENCE
                        </div>
                        <h2 style="font-family: 'Outfit', sans-serif; color: var(--color-text-primary); font-size: 1.8rem; font-weight: 800; letter-spacing: -0.5px;">
                            APPELLATE TRIBUNAL RECORD
                        </h2>
                        
                        <div style="border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color); padding: 1.25rem 0; margin: 1.25rem 0;">
                            <p style="font-style: italic; color: var(--color-text-secondary); font-size: 0.9rem;">
                                This documentation is formally tendered as grievance rectification evidence by:
                            </p>
                            <h3 style="font-size: 1.4rem; font-weight: 800; color: var(--color-text-primary); margin: 0.5rem 0;">
                                {{ $selectedAppeal?->user->basicInfo?->first_name }} {{ $selectedAppeal?->user->basicInfo?->last_name }}
                            </h3>
                            <p style="font-size: 0.85rem; color: var(--color-text-secondary); font-family: 'JetBrains Mono', monospace;">
                                Candidate Application ID: #{{ $selectedAppeal?->id }}
                            </p>
                        </div>

                        <p style="font-size: 0.8rem; color: var(--color-text-muted); font-family: 'JetBrains Mono', monospace; word-break: break-all;">
                            Artifact Path: {{ $viewingDocPath }}
                        </p>
                    </div>
                @else
                    <div style="text-align: center; color: var(--color-text-muted); padding: 3rem 1.5rem;">
                        <div style="width: 56px; height: 56px; border-radius: 50%; background: var(--bg-btn-secondary); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: var(--color-text-muted);">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                        </div>
                        <h4 style="color: var(--color-text-primary); font-weight: 600; font-size: 1rem; margin-bottom: 0.25rem;">No Evidence Selected</h4>
                        <p style="font-size: 0.85rem; max-width: 320px; margin: 0 auto;">Select an appeal from the queue on the left to inspect its uploaded grievance proof and justification.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
