<div>
@if($isFeatureDisabled)
    <div class="card mb-6" style="padding: 2.5rem; text-align: center; border-left: 5px solid var(--warning); background: var(--bg-card); box-shadow: var(--shadow-lg); margin: 1.5rem auto; max-width: 900px;">
        <div style="font-size: 3rem; margin-bottom: 1rem;">⚖️</div>
        <h2 style="font-size: 1.4rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.5rem;">
            Grievance Appellate Adjudication Workbench is Currently Disabled
        </h2>
        <p style="color: var(--color-text-secondary); max-width: 650px; margin: 0 auto; line-height: 1.6; font-size: 0.95rem;">
            The digital adjudication desk for reviewing candidate appeals and issuing statutory decrees has been toggled off by university administrators in Workflows Settings.
        </p>
        @can('manage-settings')
            <div style="margin-top: 1.5rem;">
                <a href="/admin/settings/workflows" class="btn btn-primary" style="font-weight: 700;">Enable Appellate Workbench in Workflows Settings</a>
            </div>
        @endcan
    </div>
@endif

<div class="split-screen" style="{{ $isFeatureDisabled ? 'opacity: 0.5; pointer-events: none;' : '' }}">
    <!-- Left Pane: Appealed Applications List & Selected Details -->
    <div class="split-left">
        <div class="card mb-4" style="border-color: var(--border-color); box-shadow: var(--shadow-sm);">
            <div class="flex justify-between align-center" style="flex-wrap: wrap; gap: 0.75rem;">
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
                    <p class="card-subtitle">Review candidate formal grievances, examine evidence, and issue statutory appellate decrees.</p>
                </div>
                
                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                    <button type="button" 
                            wire:click="exportCsv" 
                            class="btn btn-secondary btn-sm" 
                            style="display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 700;"
                            title="Export appellate docket to CSV">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/>
                            <line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                        <span>Export CSV</span>
                    </button>

                    <button type="button" 
                            onclick="window.print()" 
                            class="btn btn-secondary btn-sm" 
                            style="display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 700;"
                            title="Print Appellate Tribunal Docket">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <polyline points="6 9 6 2 18 2 18 9"></polyline>
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                            <rect x="6" y="14" width="12" height="8"></rect>
                        </svg>
                        <span>Print Docket</span>
                    </button>

                    <span class="badge badge-appealed">{{ count($appeals) }} In Tribunal Queue</span>
                </div>
            </div>

            <!-- Docket Filter Bar -->
            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color); display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.75rem; align-items: flex-end;">
                <!-- Search -->
                <div style="position: relative;">
                    <label style="display: block; font-size: 0.72rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-secondary); margin-bottom: 0.25rem;">Search Appellant</label>
                    <input type="text" 
                           class="form-control" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Name, CNIC, or #APP..." 
                           style="background: var(--bg-surface); color: var(--color-text-primary); font-size: 0.82rem;">
                </div>

                <!-- Advertisement Filter -->
                <div>
                    <label style="display: block; font-size: 0.72rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-secondary); margin-bottom: 0.25rem;">Advertisement</label>
                    <select class="form-control form-select" wire:model.live="advertisementFilter" style="background: var(--bg-surface); color: var(--color-text-primary); font-size: 0.82rem;">
                        <option value="ALL">All Advertisements</option>
                        @foreach($advertisements as $adv)
                            <option value="{{ $adv->id }}">📢 {{ $adv->advertisement_no }} ({{ $adv->year }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Campaign Filter -->
                <div>
                    <label style="display: block; font-size: 0.72rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-secondary); margin-bottom: 0.25rem;">Position / Campaign</label>
                    <select class="form-control form-select" wire:model.live="campaignFilter" style="background: var(--bg-surface); color: var(--color-text-primary); font-size: 0.82rem;">
                        <option value="">All Positions</option>
                        @foreach($campaigns as $c)
                            <option value="{{ $c->id }}">{{ $c->title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label style="display: block; font-size: 0.72rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-secondary); margin-bottom: 0.25rem;">Appeal Status</label>
                    <select class="form-control form-select" wire:model.live="statusFilter" style="background: var(--bg-surface); color: var(--color-text-primary); font-size: 0.82rem;">
                        <option value="APPEALED">⏳ Pending Hearing (APPEALED)</option>
                        <option value="ELIGIBLE">✓ Restored to Eligible</option>
                        <option value="PAID">↺ Remanded for Re-Scrutiny</option>
                        <option value="REJECTED">✕ Dismissed (REJECTED)</option>
                        <option value="ALL">All Appeal Outcomes</option>
                    </select>
                </div>

                @if(!empty($search) || $advertisementFilter !== 'ALL' || !empty($campaignFilter) || $statusFilter !== 'APPEALED')
                    <div>
                        <button type="button" wire:click="resetFilters" class="btn btn-secondary btn-sm" style="width: 100%; font-size: 0.8rem; padding: 0.5rem;">
                            Reset Filters
                        </button>
                    </div>
                @endif
            </div>
        </div>

        @if(session()->has('message'))
            <div class="alert alert-success mb-4" style="display: flex; align-items: center; gap: 0.75rem; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3); color: var(--success); padding: 0.85rem 1.25rem; border-radius: var(--radius-md);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span style="font-weight: 700; font-size: 0.88rem;">{{ session('message') }}</span>
            </div>
        @endif

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
                    No pending candidate appeals found in the system at this time. All statutory 7-day appeal windows have been addressed.
                </p>
            </div>
        @else
            <!-- Appeals List -->
            <div class="card mb-4" style="padding: 1.15rem;">
                <h3 class="card-title mb-3" style="font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-muted);">
                    Appeals Docket ({{ count($appeals) }})
                </h3>
                <div class="flex-col gap-2">
                    @foreach($appeals as $app)
                        @php
                            $bi = $app->user->basicInfo;
                            $candidateName = $bi ? trim($bi->first_name . ' ' . $bi->last_name) : $app->user->email;
                            $avatar = $bi?->profile_picture_url;
                            $isSelected = ($selectedAppealId == $app->id);
                        @endphp
                        <div wire:click="selectAppeal({{ $app->id }})" 
                             style="padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 1.5px solid {{ $isSelected ? 'var(--warning)' : 'var(--border-color)' }}; background: {{ $isSelected ? 'rgba(245, 158, 11, 0.08)' : 'var(--bg-card)' }}; cursor: pointer; transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);"
                             class="flex justify-between align-center">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                @if($avatar)
                                    <div class="user-avatar-circle" style="width: 38px; height: 38px; border-radius: 10px; overflow: hidden; padding: 0; flex-shrink: 0; border: 1px solid var(--border-color);">
                                        <img src="{{ $avatar }}" alt="{{ $candidateName }}" onerror="this.style.display='none';" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                @else
                                    <div class="user-avatar-circle" style="width: 38px; height: 38px; font-size: 0.8rem; font-weight: 800; border-radius: 10px; background: linear-gradient(135deg, var(--warning), #d97706); color: #fff;">
                                        {{ strtoupper(substr($candidateName, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <strong style="color: var(--color-text-primary); font-size: 0.92rem;">
                                        {{ $candidateName }}
                                    </strong><br>
                                    <span style="font-size: 0.76rem; color: var(--color-text-muted); font-family: 'JetBrains Mono', monospace;">
                                        CNIC: {{ $bi?->cnic_number ?? 'N/A' }} &bull; {{ $app->campaign->job_code }}
                                    </span>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <span class="badge" style="background: rgba(245,158,11,0.12); color: var(--warning); font-size: 0.72rem; font-weight: 700;">
                                    ⚖️ Appeal #{{ $app->id }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Selected Appeal Detail -->
            @if($selectedAppeal)
                @php
                    $selBi = $selectedAppeal->user->basicInfo;
                    $selName = $selBi ? trim($selBi->first_name . ' ' . $selBi->last_name) : $selectedAppeal->user->email;
                @endphp
                <div class="card" style="border-color: var(--border-color); box-shadow: var(--shadow-md);">
                    <div class="flex justify-between align-center mb-4" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                        <h3 class="card-title" style="color: var(--warning); font-size: 1.1rem; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                                <path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                                <path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                                <path d="M7 21h10"/>
                                <path d="M12 3v18"/>
                            </svg>
                            Grievance Dossier: {{ $selName }}
                        </h3>
                        <span class="badge badge-appealed">Tracking #{{ $selectedAppeal->tracking_number }}</span>
                    </div>
                    
                    <div class="flex-col gap-4">
                        <!-- Disqualification Note -->
                        <div style="background: rgba(239, 68, 68, 0.08); border: 1.5px solid rgba(239, 68, 68, 0.3); padding: 1rem 1.25rem; border-radius: var(--radius-sm);">
                            <div style="display: flex; align-items: center; gap: 0.45rem; margin-bottom: 0.4rem;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--danger)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="8" x2="12" y2="12"/>
                                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                                </svg>
                                <h4 style="color: var(--danger); font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 800; margin: 0;">
                                    Original Scrutiny Committee Disqualification Grounds:
                                </h4>
                            </div>
                            <p style="font-size: 0.92rem; color: #fca5a5; margin: 0; line-height: 1.5; font-weight: 600;">
                                {{ $selectedAppeal->disqualification_reason ?: 'Candidate failed statutory qualification or equivalence criteria under scrutiny guidelines.' }}
                            </p>
                        </div>

                        <!-- Candidate Appeal Statement -->
                        <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-color); padding: 1.25rem; border-radius: var(--radius-sm);">
                            <div class="flex justify-between align-center mb-2">
                                <h4 style="color: var(--secondary); font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; margin: 0;">
                                    Formal Candidate Appeal & Grounds for Review:
                                </h4>
                                <span style="font-size: 0.74rem; color: var(--color-text-muted);">
                                    Filed within 7-Day Window
                                </span>
                            </div>
                            <p style="font-size: 0.92rem; color: var(--color-text-primary); line-height: 1.6; white-space: pre-line; margin: 0; font-style: italic;">
                                "{{ $selectedAppeal->appeal_letter ?: 'I hereby submit this statutory appeal requesting reconsideration of my credentials and attached equivalence certificate.' }}"
                            </p>
                        </div>

                        <!-- Recorded Decree Details (if decided) -->
                        @if($selectedAppeal->appeal_decision_remarks)
                            <div style="background: rgba(16, 185, 129, 0.08); border: 1.5px solid rgba(16, 185, 129, 0.3); padding: 1rem 1.25rem; border-radius: var(--radius-sm);">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem; flex-wrap: wrap; gap: 0.5rem;">
                                    <strong style="color: var(--success); font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.04em;">
                                        ⚖️ Recorded Appellate Tribunal Decree
                                    </strong>
                                    @if($selectedAppeal->appeal_decided_at)
                                        <span style="font-size: 0.75rem; color: var(--color-text-muted); font-family: 'JetBrains Mono', monospace;">
                                            Decided: {{ $selectedAppeal->appeal_decided_at->format('d M Y, h:i A') }}
                                        </span>
                                    @endif
                                </div>
                                <p style="font-size: 0.9rem; color: var(--color-text-primary); margin: 0; line-height: 1.5;">
                                    "{{ $selectedAppeal->appeal_decision_remarks }}"
                                </p>
                            </div>
                        @endif

                        <!-- Committee Decision Actions -->
                        <div class="flex-col gap-2 mt-2" style="border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                            <div class="flex justify-between align-center mb-2">
                                <h4 style="font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin: 0;">
                                    Appellate Tribunal Verdict Decisions:
                                </h4>
                                <span style="font-size: 0.75rem; color: var(--color-text-muted);">Statutory Authority: Senate / Appellate Bench</span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-3">
                                <button type="button" class="btn btn-success" wire:click="openResolutionModal('ELIGIBLE')" style="padding: 0.75rem; font-weight: 700;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"/>
                                    </svg>
                                    <span>Condon Deficiency (Mark Eligible)</span>
                                </button>
                                
                                <button type="button" class="btn btn-secondary" wire:click="openResolutionModal('PAID')" style="padding: 0.75rem; font-weight: 700;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/>
                                        <path d="M21 3v5h-5"/>
                                        <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/>
                                        <path d="M8 16H3v5"/>
                                    </svg>
                                    <span>Remand for Re-Scrutiny</span>
                                </button>
                            </div>
                            
                            <button type="button" class="btn btn-danger mt-2" wire:click="openResolutionModal('REJECTED')" style="width: 100%; padding: 0.75rem; font-weight: 700;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18"/>
                                    <line x1="6" y1="6" x2="18" y2="18"/>
                                </svg>
                                <span>Dismiss Appeal (Affirm Rejection)</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>

    <!-- Right Pane: Dynamic Sandbox Appeal Document & Evidence Dossier Viewer -->
    <div class="split-right">
        <div class="card" style="height: 100%; min-height: 520px; display: flex; flex-direction: column; justify-content: space-between; border-color: var(--border-color); box-shadow: var(--shadow-md);">
            <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;" class="mb-4">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--color-text-primary); display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--warning)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                        </svg>
                        Appellate Evidence & Statutory Dossier Vault
                    </h3>
                    <span class="badge badge-appealed">Tribunal Sandbox</span>
                </div>
                <p style="font-size: 0.82rem; color: var(--color-text-secondary); margin-top: 0.25rem;">
                    Active Evidence: <strong>{{ $viewingDocName }}</strong>
                </p>
            </div>

            <!-- Virtual Certificate & Evidence Renderer -->
            <div style="flex: 1; background: var(--bg-card-solid); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.75rem; display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative;">
                @if($selectedAppeal)
                    @php
                        $rBi = $selectedAppeal->user->basicInfo;
                        $rName = $rBi ? trim($rBi->first_name . ' ' . $rBi->last_name) : $selectedAppeal->user->email;
                    @endphp
                    <div style="text-align: center; border: 2px dashed var(--warning); padding: 2.25rem 2rem; border-radius: var(--radius-md); width: 95%; background: var(--bg-primary); box-shadow: var(--shadow-glow); position: relative; overflow: hidden;">
                        <div style="color: var(--warning); font-size: 0.78rem; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 0.5rem;">
                            OFFICIAL APPELLATE EVIDENCE DOCKET
                        </div>
                        <h2 style="font-family: 'Outfit', sans-serif; color: var(--color-text-primary); font-size: 1.6rem; font-weight: 800; letter-spacing: -0.5px; margin: 0.25rem 0;">
                            STATUTORY APPELLATE TRIBUNAL RECORD
                        </h2>
                        
                        <div style="border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color); padding: 1.25rem 0; margin: 1.25rem 0; text-align: left;">
                            <div class="grid grid-cols-2 gap-3" style="font-size: 0.85rem;">
                                <div>
                                    <span style="color: var(--color-text-muted);">Appellant:</span>
                                    <strong style="color: var(--color-text-primary); display: block;">{{ $rName }}</strong>
                                </div>
                                <div>
                                    <span style="color: var(--color-text-muted);">CNIC Reference:</span>
                                    <code style="color: var(--secondary); font-family: 'JetBrains Mono', monospace; display: block;">{{ $rBi?->cnic_number ?? 'Pending' }}</code>
                                </div>
                                <div>
                                    <span style="color: var(--color-text-muted);">Contested Campaign:</span>
                                    <strong style="color: var(--color-text-primary); display: block;">{{ $selectedAppeal->campaign->title }}</strong>
                                </div>
                                <div>
                                    <span style="color: var(--color-text-muted);">Regional Quota:</span>
                                    <strong style="color: var(--success); display: block;">{{ $rBi?->quota ?? 'Open Merit' }}</strong>
                                </div>
                            </div>
                        </div>

                        <div style="background: rgba(245, 158, 11, 0.05); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; padding: 0.85rem; text-align: left; margin-bottom: 1rem;">
                            <span style="font-size: 0.75rem; color: var(--warning); font-weight: 800; text-transform: uppercase;">
                                Evidence Authenticity Verification
                            </span>
                            <p style="font-size: 0.82rem; color: var(--color-text-secondary); margin: 0.25rem 0 0; line-height: 1.5;">
                                Candidate has submitted original equivalence / credential justification. Verified against Higher Education Commission qualification frameworks.
                            </p>
                        </div>

                        <p style="font-size: 0.78rem; color: var(--color-text-muted); font-family: 'JetBrains Mono', monospace; word-break: break-all; margin: 0;">
                            Docket Reference: APP-TRB-{{ str_pad($selectedAppeal->id, 5, '0', STR_PAD_LEFT) }}-{{ date('Y') }}
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

    <!-- Statutory Condonation & Appellate Resolution Modal -->
    <x-modal :show="$isResolutionModalOpen" title="Issue Statutory Appellate Tribunal Decree" closeAction="closeResolutionModal" style="max-width: 650px; width: 95%;">
        @if($selectedAppeal)
            <div>
                <div style="margin-bottom: 1.25rem;">
                    <div style="font-size: 0.82rem; color: var(--color-text-muted); text-transform: uppercase; font-weight: 700;">
                        Appellate Action Selected
                    </div>
                    <div style="font-size: 1.2rem; font-weight: 800; margin: 0.2rem 0; color: {{ $resolutionType === 'ELIGIBLE' ? 'var(--success)' : ($resolutionType === 'PAID' ? 'var(--secondary)' : 'var(--danger)') }};">
                        @if($resolutionType === 'ELIGIBLE')
                            ✓ CONDON DEFICIENCY & UPGRADE TO ELIGIBLE
                        @elseif($resolutionType === 'PAID')
                            🔄 REMAND BACK TO SCRUTINY COMMITTEE (PAID)
                        @else
                            ✕ PERMANENTLY DISMISS APPEAL (REJECTED)
                        @endif
                    </div>
                    <p style="font-size: 0.85rem; color: var(--color-text-secondary); margin: 0;">
                        Candidate: <strong>{{ $selectedAppeal->user->basicInfo?->first_name }} {{ $selectedAppeal->user->basicInfo?->last_name }}</strong> &bull; Campaign: {{ $selectedAppeal->campaign->title }}
                    </p>
                </div>

                <div class="form-group mb-4">
                    <label class="form-label" style="font-weight: 700; font-size: 0.85rem;">
                        Official Statutory Minutes & Judicial Grounds *
                    </label>
                    <textarea class="form-control" rows="4" wire:model="statutoryOrderNotes" style="font-size: 0.88rem; line-height: 1.5;"></textarea>
                    <small style="color: var(--color-text-muted); font-size: 0.76rem; display: block; margin-top: 0.35rem;">
                        This formal verdict will be recorded in the permanent institutional recruitment minutes.
                    </small>
                </div>

                <div class="flex justify-end gap-3" style="border-top: 1px solid var(--border-color); padding-top: 1rem;">
                    <button type="button" class="btn btn-secondary" wire:click="closeResolutionModal">
                        Cancel
                    </button>
                    <button type="button" 
                            class="btn {{ $resolutionType === 'ELIGIBLE' ? 'btn-success' : ($resolutionType === 'PAID' ? 'btn-primary' : 'btn-danger') }}" 
                            wire:click="executeAppellateDecree" 
                            style="font-weight: 700;">
                        Sign & Execute Decree
                    </button>
                </div>
            </div>
        @endif
    </x-modal>
</div>
</div>
