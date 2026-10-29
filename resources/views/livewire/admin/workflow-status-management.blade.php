<div>
    <!-- Top Alert Messages -->
    @if(session()->has('message'))
        <div class="mb-4" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: var(--radius-sm); padding: 0.85rem 1.25rem; display: flex; align-items: center; justify-content: space-between; color: var(--success);">
            <div style="display: flex; align-items: center; gap: 0.6rem; font-size: 0.9rem; font-weight: 600;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <span>{{ session('message') }}</span>
            </div>
            <button type="button" wire:click="$refresh" style="background: none; border: none; color: inherit; cursor: pointer; font-size: 1rem;">✕</button>
        </div>
    @endif

    <!-- Page Header & Segmented Scope Controls -->
    <div class="card mb-6" style="background: linear-gradient(135deg, rgba(30, 41, 59, 0.8) 0%, rgba(15, 23, 42, 0.9) 100%); border-color: rgba(99, 102, 241, 0.25);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1.5rem; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 280px;">
                <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.4rem;">
                    <span class="badge" style="background: rgba(99, 102, 241, 0.2); color: var(--primary); font-weight: 800; font-size: 0.75rem; letter-spacing: 0.05em;">
                        WORKFLOW ORCHESTRATION ENGINE
                    </span>
                    <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; font-size: 0.75rem; font-weight: 700;">
                        LIFECYCLE MATRIX
                    </span>
                </div>
                <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--color-text-primary); margin: 0 0 0.35rem 0; display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                    </svg>
                    <span>Dynamic Statuses & Lifecycle Workflow Engine</span>
                </h2>
                <p style="font-size: 0.88rem; color: var(--color-text-secondary); margin: 0; line-height: 1.5;">
                    Configure dynamic application progression states, applicant dossier locks, gate validation criteria (fees, transcripts, peer reviews, reasons), and allowed branch transitions.
                </p>
            </div>

            <!-- Scope Navigation Segmented Tabs -->
            <div style="display: flex; gap: 0.4rem; background: rgba(15, 23, 42, 0.6); padding: 0.35rem; border-radius: var(--radius-md); border: 1px solid rgba(255, 255, 255, 0.08);">
                <button type="button" 
                    wire:click="setScope('APPLICATION')" 
                    class="btn {{ $activeScope === 'APPLICATION' ? 'btn-primary' : 'btn-secondary' }}"
                    style="padding: 0.55rem 1rem; font-size: 0.82rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem;">
                    <span>📑 Application Flow</span>
                </button>
                <button type="button" 
                    wire:click="setScope('APPLICANT')" 
                    class="btn {{ $activeScope === 'APPLICANT' ? 'btn-primary' : 'btn-secondary' }}"
                    style="padding: 0.55rem 1rem; font-size: 0.82rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem;">
                    <span>👤 Applicant Dossier</span>
                </button>
                <button type="button" 
                    wire:click="setScope('SYSTEM')" 
                    class="btn {{ $activeScope === 'SYSTEM' ? 'btn-primary' : 'btn-secondary' }}"
                    style="padding: 0.55rem 1rem; font-size: 0.82rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem;">
                    <span>⚙️ System & Quorum</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Live KPI Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="metric-card" style="border-left: 4px solid var(--primary);">
            <div class="metric-label">{{ $activeScope }} STATUSES</div>
            <div class="metric-value" style="color: var(--primary);">{{ $stats['total'] }}</div>
            <div class="metric-subtext">Active lifecycle nodes</div>
        </div>
        <div class="metric-card" style="border-left: 4px solid #10b981;">
            <div class="metric-label">OPERATIONAL STATUSES</div>
            <div class="metric-value" style="color: #10b981;">{{ $stats['active'] }}</div>
            <div class="metric-subtext">Available for transition</div>
        </div>
        <div class="metric-card" style="border-left: 4px solid #8b5cf6;">
            <div class="metric-label">LOCKED STATE NODES</div>
            <div class="metric-value" style="color: #8b5cf6;">{{ $stats['locked'] }}</div>
            <div class="metric-subtext">Candidate editing frozen</div>
        </div>
        <div class="metric-card" style="border-left: 4px solid #f59e0b;">
            <div class="metric-label">TERMINAL STAGES</div>
            <div class="metric-value" style="color: #f59e0b;">{{ $stats['terminal'] }}</div>
            <div class="metric-subtext">Final pipeline resolutions</div>
        </div>
    </div>

    <!-- Search & New Status Trigger Bar -->
    <div class="card mb-6" style="padding: 1.15rem 1.25rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div style="flex: 1; min-width: 260px; max-width: 420px; position: relative;">
                <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Search statuses by code, label, or criteria..." style="font-size: 0.88rem; padding-left: 2.2rem;" />
                <span style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--color-text-muted);">🔍</span>
            </div>

            <button type="button" class="btn btn-primary" wire:click="openCreateModal" style="display: inline-flex; align-items: center; gap: 0.45rem; font-size: 0.85rem; font-weight: 700; padding: 0.6rem 1.25rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                <span>+ Define New Status Node</span>
            </button>
        </div>
    </div>

    <!-- Statuses Table & Flow Matrix -->
    <div class="card mb-8" style="padding: 0; overflow: hidden; border-color: rgba(255, 255, 255, 0.08);">
        <div style="padding: 1rem 1.25rem; background: rgba(15, 23, 42, 0.5); border-bottom: 1px solid rgba(255, 255, 255, 0.06); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1rem; font-weight: 700; color: var(--color-text-primary); margin: 0; display: flex; align-items: center; gap: 0.4rem;">
                <span>{{ $activeScope }} Lifecycle State Progression Matrix</span>
            </h3>
            <span style="font-size: 0.75rem; color: var(--color-text-muted);">
                Showing {{ $statuses->count() }} registered state nodes
            </span>
        </div>

        <div class="table-responsive">
            <table class="table" style="margin: 0;">
                <thead style="background: rgba(15, 23, 42, 0.8); font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    <tr>
                        <th style="width: 6%; text-align: center;">ORD</th>
                        <th style="width: 22%;">STATUS IDENTIFIER & PREVIEW</th>
                        <th style="width: 22%;">DESCRIPTION & REQUISITES</th>
                        <th style="width: 28%;">ALLOWED NEXT TRANSITIONS</th>
                        <th style="width: 10%; text-align: center;">FLAGS</th>
                        <th style="width: 12%; text-align: right;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($statuses as $st)
                        @php $badge = $st->getBadgeStyle(); @endphp
                        <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.04);">
                            <!-- Order Handle -->
                            <td style="vertical-align: middle; text-align: center;">
                                <span class="badge" style="background: rgba(255, 255, 255, 0.06); color: var(--color-text-secondary); font-weight: 800; font-size: 0.75rem;">
                                    #{{ $st->order }}
                                </span>
                            </td>

                            <!-- Code & Label Preview -->
                            <td style="vertical-align: middle;">
                                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                    <div style="display: flex; align-items: center; gap: 0.45rem;">
                                        <span class="badge" style="background: {{ $badge['bg'] }}; color: {{ $badge['text'] }}; border: 1px solid {{ $badge['border'] }}; font-weight: 700; font-size: 0.78rem;">
                                            {{ $st->label }}
                                        </span>
                                        @if($st->is_system_default)
                                            <span class="badge" style="background: rgba(59, 130, 246, 0.2); color: #3b82f6; font-size: 0.68rem; font-weight: 800;">
                                                DEFAULT
                                            </span>
                                        @endif
                                    </div>
                                    <span style="font-family: monospace; font-size: 0.75rem; color: var(--color-text-muted); font-weight: 600;">
                                        {{ $st->code }}
                                    </span>
                                </div>
                            </td>

                            <!-- Description & Gate Requisites -->
                            <td style="vertical-align: middle;">
                                <p style="font-size: 0.8rem; color: var(--color-text-secondary); margin: 0 0 0.35rem 0; line-height: 1.4;">
                                    {{ $st->description ?: 'No detailed description specified.' }}
                                </p>
                                <!-- Gate Requirements Badges -->
                                @php $reqs = $st->requirements ?? []; @endphp
                                <div style="display: flex; flex-wrap: wrap; gap: 0.25rem;">
                                    @if(!empty($reqs['require_fee_paid']))
                                        <span class="badge" style="background: rgba(16, 185, 129, 0.12); color: #10b981; font-size: 0.68rem;">
                                            💳 Fee Settled Required
                                        </span>
                                    @endif
                                    @if(!empty($reqs['require_documents_verified']))
                                        <span class="badge" style="background: rgba(99, 102, 241, 0.12); color: #6366f1; font-size: 0.68rem;">
                                            📑 Transcripts Verified
                                        </span>
                                    @endif
                                    @if(!empty($reqs['require_peer_reviews']))
                                        <span class="badge" style="background: rgba(236, 72, 153, 0.12); color: #ec4899; font-size: 0.68rem;">
                                            🔬 Quorum Reviews Met
                                        </span>
                                    @endif
                                    @if(!empty($reqs['require_justification_reason']))
                                        <span class="badge" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b; font-size: 0.68rem;">
                                            📝 Justification Note Required
                                        </span>
                                    @endif
                                    @if(!empty($reqs['lock_applicant_profile']))
                                        <span class="badge" style="background: rgba(139, 92, 246, 0.12); color: #8b5cf6; font-size: 0.68rem;">
                                            🔒 Freeze Candidate Edits
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Allowed Next Transitions -->
                            <td style="vertical-align: middle;">
                                @php $targets = $st->allowed_transitions ?? []; @endphp
                                @if(!empty($targets))
                                    <div style="display: flex; flex-wrap: wrap; gap: 0.3rem;">
                                        @foreach($targets as $tCode)
                                            <span class="badge" style="background: rgba(255, 255, 255, 0.05); color: var(--color-text-secondary); border: 1px solid rgba(255, 255, 255, 0.1); font-size: 0.72rem; font-family: monospace;">
                                                ➔ {{ $tCode }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span style="font-size: 0.75rem; color: var(--color-text-muted); font-style: italic;">
                                        Terminal State (No forward transitions)
                                    </span>
                                @endif
                            </td>

                            <!-- State Flags -->
                            <td style="vertical-align: middle; text-align: center;">
                                <div style="display: flex; flex-direction: column; gap: 0.25rem; align-items: center;">
                                    @if($st->is_locked_state)
                                        <span class="badge" style="background: rgba(139, 92, 246, 0.15); color: #8b5cf6; font-size: 0.68rem;" title="Applicant dossier edit freeze">
                                            🔒 Locked
                                        </span>
                                    @endif
                                    @if($st->is_terminal)
                                        <span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #ef4444; font-size: 0.68rem;" title="Terminal pipeline outcome">
                                            🏁 Terminal
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Actions -->
                            <td style="vertical-align: middle; text-align: right;">
                                <div class="action-btn-group justify-end">
                                    <button type="button" 
                                            class="action-btn action-btn-edit" 
                                            wire:click="openEditModal({{ $st->id }})" 
                                            data-tooltip="Edit Status & Requirements"
                                            aria-label="Edit Status">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                    </button>
                                    <button type="button" 
                                            class="action-btn {{ $st->is_active ? 'action-btn-success' : 'action-btn-delete' }}" 
                                            wire:click="toggleStatusActive({{ $st->id }})" 
                                            data-tooltip="{{ $st->is_active ? 'Status: Active (Click to Disable)' : 'Status: Inactive (Click to Activate)' }}"
                                            aria-label="Toggle Status">
                                        @if($st->is_active)
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                        @else
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        @endif
                                    </button>
                                    @if(!$st->is_system_default)
                                        <button type="button" 
                                                class="action-btn action-btn-delete" 
                                                wire:click="deleteStatus({{ $st->id }})" 
                                                wire:confirm="Remove this status node from workflow?" 
                                                data-tooltip="Delete Status Node"
                                                aria-label="Delete Status Node">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                <line x1="10" y1="11" x2="10" y2="17"/>
                                                <line x1="14" y1="11" x2="14" y2="17"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2.5rem; color: var(--color-text-muted);">
                                No workflow statuses found matching search in {{ $activeScope }} scope.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: ADD / EDIT WORKFLOW STATUS NODE                                     -->
    <!-- ========================================================================= -->
    @if($showModal)
        <div style="position: fixed; inset: 0; background: rgba(0, 0, 0, 0.75); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 1.5rem;">
            <div class="card" style="width: 100%; max-width: 820px; max-height: 90vh; overflow-y: auto; background: #0f172a; border-color: rgba(99, 102, 241, 0.35); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); padding: 1.75rem;">
                
                <!-- Modal Header -->
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.3rem;">
                            <span class="badge" style="background: rgba(99, 102, 241, 0.2); color: var(--primary); font-size: 0.72rem; font-weight: 800;">
                                STATUS LIFECYCLE NODE
                            </span>
                            <span class="badge" style="background: rgba(255, 255, 255, 0.06); color: var(--color-text-secondary); font-size: 0.72rem;">
                                {{ $scope }} SCOPE
                            </span>
                        </div>
                        <h3 style="font-size: 1.35rem; font-weight: 800; color: var(--color-text-primary); margin: 0;">
                            {{ $isEdit ? "Edit Status [{$code}]" : 'Define New Workflow Status' }}
                        </h3>
                    </div>
                    <button type="button" wire:click="closeModal" style="background: none; border: none; color: var(--color-text-muted); cursor: pointer; font-size: 1.25rem;">✕</button>
                </div>

                <!-- Form Fields -->
                <form wire:submit.prevent="saveStatus">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="form-label" style="font-size: 0.78rem; font-weight: 700;">UNIQUE STATUS CODE</label>
                            <input type="text" class="form-control" wire:model.defer="code" placeholder="e.g. INCOMPLETE_DOSSIER" style="text-transform: uppercase; font-family: monospace; font-weight: 700;" required />
                            @error('code') <span class="text-danger" style="font-size: 0.72rem;">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="form-label" style="font-size: 0.78rem; font-weight: 700;">DISPLAY LABEL</label>
                            <input type="text" class="form-control" wire:model.defer="label" placeholder="e.g. Incomplete Dossier" required />
                            @error('label') <span class="text-danger" style="font-size: 0.72rem;">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="form-label" style="font-size: 0.78rem; font-weight: 700;">BADGE COLOR ACCENT</label>
                            <select class="form-control form-select" wire:model.defer="badge_color">
                                <option value="primary">Indigo / Primary</option>
                                <option value="success">Green / Success</option>
                                <option value="warning">Orange / Warning</option>
                                <option value="danger">Red / Danger</option>
                                <option value="purple">Purple / Royal</option>
                                <option value="info">Cyan / Info</option>
                                <option value="neutral">Slate / Neutral</option>
                                <option value="amber">Amber / Accent</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="form-label" style="font-size: 0.78rem; font-weight: 700;">SORT ORDER INDEX</label>
                            <input type="number" min="0" max="100" class="form-control" wire:model.defer="order" required />
                            <span style="font-size: 0.72rem; color: var(--color-text-muted);">Progression sequence number in pipeline.</span>
                        </div>

                        <div>
                            <label class="form-label" style="font-size: 0.78rem; font-weight: 700;">LIFECYCLE SCOPE</label>
                            <select class="form-control form-select" wire:model.defer="scope">
                                <option value="APPLICATION">APPLICATION (Candidate Submissions)</option>
                                <option value="APPLICANT">APPLICANT (Candidate Roster & Dossiers)</option>
                                <option value="SYSTEM">SYSTEM (Campaigns & Quorums)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-size: 0.78rem; font-weight: 700;">STATUS DESCRIPTION & OPERATIONAL GUIDANCE</label>
                        <textarea class="form-control" rows="2" wire:model.defer="description" placeholder="Explain when this status is applied and what it signifies..."></textarea>
                    </div>

                    <!-- Transition Gate Criteria & Requirements -->
                    <div style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: var(--radius-sm); padding: 1.15rem; margin-bottom: 1.5rem;">
                        <h4 style="font-size: 0.88rem; font-weight: 700; color: var(--color-text-primary); margin: 0 0 0.25rem 0;">
                            Gate Requirements & Transition Constraints
                        </h4>
                        <p style="font-size: 0.74rem; color: var(--color-text-muted); margin: 0 0 0.85rem 0;">
                            Automatic checks enforced before an application or dossier can transition into this status.
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.82rem;">
                                <input type="checkbox" wire:model.defer="req_fee_paid" style="width: 16px; height: 16px;" />
                                <span>Require Settled Application Fee (Kuickpay / 1Bill)</span>
                            </label>

                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.82rem;">
                                <input type="checkbox" wire:model.defer="req_docs_verified" style="width: 16px; height: 16px;" />
                                <span>Require All Qualification Transcripts Verified</span>
                            </label>

                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.82rem;">
                                <input type="checkbox" wire:model.defer="req_peer_reviews" style="width: 16px; height: 16px;" />
                                <span>Require Minimum Statutory Peer Reviews Completed</span>
                            </label>

                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.82rem;">
                                <input type="checkbox" wire:model.defer="req_justification" style="width: 16px; height: 16px;" />
                                <span>Require Formal Justification / Deficiency Note</span>
                            </label>

                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.82rem;">
                                <input type="checkbox" wire:model.defer="req_lock_profile" style="width: 16px; height: 16px;" />
                                <span>Freeze / Lock Candidate Profile from Further Edits</span>
                            </label>
                        </div>
                    </div>

                    <!-- Allowed Next Transitions Matrix -->
                    <div style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: var(--radius-sm); padding: 1.15rem; margin-bottom: 1.5rem;">
                        <h4 style="font-size: 0.88rem; font-weight: 700; color: var(--color-text-primary); margin: 0 0 0.25rem 0;">
                            Permitted Next Progression Transitions
                        </h4>
                        <p style="font-size: 0.74rem; color: var(--color-text-muted); margin: 0 0 0.85rem 0;">
                            Select which statuses can be reached next from this status state.
                        </p>

                        <div style="display: flex; flex-wrap: wrap; gap: 0.45rem;">
                            @foreach($allScopeStatuses as $sibling)
                                @if($sibling->code !== $code)
                                    <button type="button" 
                                        wire:click="toggleAllowedTransition('{{ $sibling->code }}')"
                                        class="badge" 
                                        style="background: {{ in_array($sibling->code, $allowed_transitions) ? 'rgba(99, 102, 241, 0.25)' : 'rgba(255, 255, 255, 0.04)' }}; color: {{ in_array($sibling->code, $allowed_transitions) ? 'var(--primary)' : 'var(--color-text-secondary)' }}; border: 1px solid {{ in_array($sibling->code, $allowed_transitions) ? 'rgba(99, 102, 241, 0.5)' : 'rgba(255, 255, 255, 0.1)' }}; padding: 0.45rem 0.8rem; font-size: 0.78rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;">
                                        <span>{{ in_array($sibling->code, $allowed_transitions) ? '✓' : '+' }}</span>
                                        <span>{{ $sibling->label }} ({{ $sibling->code }})</span>
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Flags -->
                    <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; margin-bottom: 1.5rem; padding: 0.75rem 0;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.82rem; font-weight: 600;">
                            <input type="checkbox" wire:model.defer="is_locked_state" style="width: 16px; height: 16px;" />
                            <span>Locked State (Freezes dossier edits)</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.82rem; font-weight: 600;">
                            <input type="checkbox" wire:model.defer="is_terminal" style="width: 16px; height: 16px;" />
                            <span>Terminal State (Final outcome of recruitment)</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.82rem; font-weight: 600;">
                            <input type="checkbox" wire:model.defer="is_system_default" style="width: 16px; height: 16px;" />
                            <span>Initial System Default Status</span>
                        </label>
                    </div>

                    <!-- Modal Actions -->
                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 1rem; border-top: 1px solid rgba(255, 255, 255, 0.08);">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal" style="padding: 0.6rem 1.25rem; font-size: 0.85rem;">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.75rem; font-size: 0.88rem; font-weight: 700;">
                            {{ $isEdit ? 'Update Status Node' : 'Save Status Node' }}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif
</div>
