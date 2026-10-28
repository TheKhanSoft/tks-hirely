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

    <!-- Page Header & Target Campaign Selector -->
    <div class="card mb-6" style="background: linear-gradient(135deg, rgba(30, 41, 59, 0.7) 0%, rgba(15, 23, 42, 0.8) 100%); border-color: rgba(99, 102, 241, 0.25);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1.5rem; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 280px;">
                <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.4rem;">
                    <span class="badge" style="background: rgba(99, 102, 241, 0.2); color: var(--primary); font-weight: 800; font-size: 0.75rem; letter-spacing: 0.05em;">
                        GOVERNANCE & STATUTE ENGINE
                    </span>
                    @if($currentCampaign)
                        <span class="badge" style="background: rgba(255, 255, 255, 0.06); color: var(--color-text-secondary); font-size: 0.75rem;">
                            {{ $currentCampaign->job_code }}
                        </span>
                    @endif
                </div>
                <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--color-text-primary); margin: 0 0 0.35rem 0; display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                        <path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                        <path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                        <path d="M7 21h10"/>
                        <path d="M12 3v18"/>
                        <path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"/>
                    </svg>
                    <span>Statutory Criteria & Committee Constitution</span>
                </h2>
                <p style="font-size: 0.88rem; color: var(--color-text-secondary); margin: 0; line-height: 1.5;">
                    Define dynamic rules, quotas, member counts, cadre specifications (Faculty vs. Officer vs. Expert), and origin scopes (Inside vs. Outside vs. International).
                </p>
            </div>

            <!-- Campaign Selector & Save Controls -->
            <div style="display: flex; flex-direction: column; gap: 0.75rem; min-width: 280px;">
                <div>
                    <label style="display: block; font-size: 0.74rem; text-transform: uppercase; font-weight: 700; color: var(--color-text-muted); margin-bottom: 0.35rem; letter-spacing: 0.05em;">
                        TARGET JOB CAMPAIGN
                    </label>
                    <select class="form-control form-select" wire:model.live="selected_campaign_id" style="font-weight: 600; font-size: 0.9rem;">
                        @foreach($campaigns as $camp)
                            <option value="{{ $camp->id }}">
                                {{ $camp->title }} ({{ $camp->job_code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; gap: 0.5rem;">
                    <button type="button" class="btn btn-primary" style="flex: 1; padding: 0.6rem 1.25rem; font-size: 0.88rem; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; gap: 0.45rem;" wire:click="saveConstitution">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/>
                            <polyline points="7 3 7 8 15 8"/>
                        </svg>
                        <span>Save Criteria</span>
                    </button>
                    @if($currentCampaign)
                        <a href="/admin/campaigns" class="btn btn-secondary" style="padding: 0.6rem 0.9rem; font-size: 0.85rem;" title="Go to Campaign Allocations">
                            <span>Panel Allocation ↗</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Statutory Presets Bar -->
    <div class="card mb-6" style="padding: 1rem 1.25rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
            <span style="font-size: 0.75rem; text-transform: uppercase; font-weight: 800; color: var(--color-text-muted); letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.4rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                </svg>
                <span>ONE-CLICK STATUTORY PRESET TEMPLATES</span>
            </span>
            <span style="font-size: 0.75rem; color: var(--color-text-muted);">
                Clicking a preset will populate dynamic criteria rows below
            </span>
        </div>

        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
            <button type="button" class="btn btn-secondary" style="padding: 0.45rem 0.85rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.4rem; border-color: rgba(99, 102, 241, 0.3);" wire:click="applyPreset('asst_prof')">
                <span>🎓 Assistant Professor (6 Members • 3 Reviewers)</span>
            </button>
            <button type="button" class="btn btn-secondary" style="padding: 0.45rem 0.85rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.4rem; border-color: rgba(139, 92, 246, 0.3);" wire:click="applyPreset('academic_prof')">
                <span>🏛️ Professorial Board (6 Members • 1 Overseas)</span>
            </button>
            <button type="button" class="btn btn-secondary" style="padding: 0.45rem 0.85rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.4rem;" wire:click="applyPreset('lecturer')">
                <span>📚 Lecturer / Teaching (5 Members)</span>
            </button>
            <button type="button" class="btn btn-secondary" style="padding: 0.45rem 0.85rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.4rem;" wire:click="applyPreset('corporate_board')">
                <span>💼 Corporate Executive (5 Members)</span>
            </button>
            <button type="button" class="btn btn-secondary" style="padding: 0.45rem 0.85rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.4rem;" wire:click="applyPreset('public_sector')">
                <span>🛡️ Public Sector / Civil Service (4 Members)</span>
            </button>
            <button type="button" class="btn btn-secondary" style="padding: 0.45rem 0.85rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.4rem;" wire:click="applyPreset('expert_heavy')">
                <span>🔬 External-Led Expert (5 Members)</span>
            </button>
            <button type="button" class="btn btn-secondary" style="padding: 0.45rem 0.85rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.4rem; border-style: dashed;" wire:click="applyPreset('blank')">
                <span>➕ Blank Custom</span>
            </button>
        </div>
    </div>

    <!-- Live Quorum Metrics & Dynamic Cadre Summary -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <!-- Total Quorum -->
        <div class="card" style="padding: 1rem 1.25rem; background: rgba(255, 255, 255, 0.02); border-left: 3px solid var(--primary);">
            <div style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: var(--color-text-muted); letter-spacing: 0.05em;">
                TOTAL STATUTORY QUORUM
            </div>
            <div style="display: flex; align-items: baseline; gap: 0.5rem; margin-top: 0.25rem;">
                <span style="font-size: 1.85rem; font-weight: 800; color: var(--color-text-primary); font-family: monospace;">
                    {{ $this->totalQuorum }}
                </span>
                <span style="font-size: 0.8rem; color: var(--color-text-secondary);">Prescribed Members</span>
            </div>
        </div>

        <!-- Inside Organization Scope -->
        <div class="card" style="padding: 1rem 1.25rem; background: rgba(255, 255, 255, 0.02); border-left: 3px solid var(--success);">
            <div style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: var(--color-text-muted); letter-spacing: 0.05em;">
                🏢 INSIDE (INTERNAL)
            </div>
            <div style="display: flex; align-items: baseline; gap: 0.5rem; margin-top: 0.25rem;">
                <span style="font-size: 1.85rem; font-weight: 800; color: var(--success); font-family: monospace;">
                    {{ $this->internalCount }}
                </span>
                <span style="font-size: 0.8rem; color: var(--color-text-secondary);">Home Org / Faculty</span>
            </div>
        </div>

        <!-- Outside External Experts -->
        <div class="card" style="padding: 1rem 1.25rem; background: rgba(255, 255, 255, 0.02); border-left: 3px solid var(--secondary);">
            <div style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: var(--color-text-muted); letter-spacing: 0.05em;">
                🌐 OUTSIDE (EXTERNAL)
            </div>
            <div style="display: flex; align-items: baseline; gap: 0.5rem; margin-top: 0.25rem;">
                <span style="font-size: 1.85rem; font-weight: 800; color: var(--secondary); font-family: monospace;">
                    {{ $this->externalCount }}
                </span>
                <span style="font-size: 0.8rem; color: var(--color-text-secondary);">Other Univs / Offices</span>
            </div>
        </div>

        <!-- International Specialists -->
        <div class="card" style="padding: 1rem 1.25rem; background: rgba(255, 255, 255, 0.02); border-left: 3px solid #c084fc;">
            <div style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: var(--color-text-muted); letter-spacing: 0.05em;">
                ✈️ OVERSEAS / INTL
            </div>
            <div style="display: flex; align-items: baseline; gap: 0.5rem; margin-top: 0.25rem;">
                <span style="font-size: 1.85rem; font-weight: 800; color: #c084fc; font-family: monospace;">
                    {{ $this->internationalCount }}
                </span>
                <span style="font-size: 0.8rem; color: var(--color-text-secondary);">Foreign Specialists</span>
            </div>
        </div>

        <!-- Peer Review Experts -->
        <div class="card" style="padding: 1rem 1.25rem; background: rgba(255, 255, 255, 0.02); border-left: 3px solid var(--warning);">
            <div style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: var(--color-text-muted); letter-spacing: 0.05em;">
                🔬 PEER REVIEW EXPERTS
            </div>
            <div style="display: flex; align-items: baseline; gap: 0.5rem; margin-top: 0.25rem;">
                <span style="font-size: 1.85rem; font-weight: 800; color: var(--warning); font-family: monospace;">
                    {{ $required_peer_reviewers }}
                </span>
                <span style="font-size: 0.8rem; color: var(--color-text-secondary);">External Referees</span>
            </div>
        </div>
    </div>

    <!-- Main Constitution Rules Configuration Form -->
    <div class="card mb-6">
        <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.25rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
            <div>
                <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--color-text-primary); margin: 0 0 0.2rem 0;">
                    Dynamic Statutory Committee Constitution Rules
                </h3>
                <p style="font-size: 0.82rem; color: var(--color-text-secondary); margin: 0;">
                    Define each member rule line item with exact count, designation, origin scope, and cadre.
                </p>
            </div>

            <!-- Quick Add Row Button -->
            <button type="button" class="btn btn-secondary" style="padding: 0.45rem 0.95rem; font-size: 0.82rem; display: inline-flex; align-items: center; gap: 0.4rem; border-color: var(--primary);" wire:click="addRule">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                <span>Add Member Slot / Rule</span>
            </button>
        </div>

        <!-- Top Configuration Inputs -->
        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;" class="grid-responsive">
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--color-text-secondary); margin-bottom: 0.35rem;">
                    CONSTITUTION TITLE *
                </label>
                <input type="text" class="form-control" wire:model="constitution_title" placeholder="e.g. Statutory Selection Committee for Assistant Professor" style="font-weight: 600;">
                @error('constitution_title') <span style="font-size: 0.75rem; color: var(--danger);">{{ $message }}</span> @enderror
            </div>

            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--color-text-secondary); margin-bottom: 0.35rem;">
                    REQUIRED PEER REVIEWERS *
                </label>
                <div style="display: flex; align-items: center; gap: 0.35rem;">
                    <input type="number" min="0" max="20" class="form-control" wire:model.live="required_peer_reviewers" style="font-weight: 700; font-family: monospace;">
                </div>
                @error('required_peer_reviewers') <span style="font-size: 0.75rem; color: var(--danger);">{{ $message }}</span> @enderror
            </div>

            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--color-text-secondary); margin-bottom: 0.35rem;">
                    STRICT QUOTA ENFORCEMENT
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: var(--color-text-primary); margin-top: 0.55rem; cursor: pointer;">
                    <input type="checkbox" wire:model="enforce_strict_quota" style="width: 16px; height: 16px; accent-color: var(--primary);">
                    <span>Strictly Enforce Quorum</span>
                </label>
            </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--color-text-secondary); margin-bottom: 0.35rem;">
                CONSTITUTION MEMO / LEGAL STATUTE REFERENCE
            </label>
            <input type="text" class="form-control" wire:model="constitution_description" placeholder="e.g. As prescribed under University Statute Act Section 14 / Corporate Governance Manual Article 8">
        </div>

        <!-- Dynamic Rule Rows Table / Card Repeater -->
        <div class="table-responsive" style="margin-bottom: 1.5rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); overflow: hidden;">
            <table class="table" style="margin: 0; font-size: 0.86rem;">
                <thead>
                    <tr style="background: rgba(255, 255, 255, 0.03);">
                        <th style="width: 45px; text-align: center;">#</th>
                        <th style="width: 140px;">COUNT</th>
                        <th>ROLE / TITLE / DESIGNATION</th>
                        <th style="width: 200px;">ORIGIN / SCOPE</th>
                        <th style="width: 160px;">CADRE</th>
                        <th style="width: 140px; text-align: center;">STATUS</th>
                        <th style="width: 60px; text-align: right;">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rules as $index => $rule)
                        <tr style="vertical-align: middle;">
                            <!-- Index -->
                            <td style="text-align: center; color: var(--color-text-muted); font-weight: 700; font-family: monospace;">
                                {{ $index + 1 }}
                            </td>

                            <!-- Dynamic Count Field with Stepper Buttons -->
                            <td>
                                <div style="display: inline-flex; align-items: center; gap: 0.25rem;">
                                    <button type="button" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; line-height: 1;" wire:click="updateRuleCount({{ $index }}, -1)" {{ ($rule['count'] ?? 1) <= 1 ? 'disabled' : '' }}>
                                        -
                                    </button>
                                    <input type="number" min="1" max="50" class="form-control" wire:model.live="rules.{{ $index }}.count" style="width: 58px; text-align: center; padding: 0.3rem 0.2rem; font-weight: 800; font-family: monospace;">
                                    <button type="button" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; line-height: 1;" wire:click="updateRuleCount({{ $index }}, 1)">
                                        +
                                    </button>
                                </div>
                                @error("rules.{$index}.count") <div style="font-size: 0.7rem; color: var(--danger);">{{ $message }}</div> @enderror
                            </td>

                            <!-- Role / Title Field (e.g. Dean concerned, Dept Head, x Faculty, x Officer) -->
                            <td>
                                <input type="text" class="form-control" wire:model="rules.{{ $index }}.name" placeholder="e.g. Dean of Faculty, Head of Dept, Internal Faculty..." style="font-weight: 600; font-size: 0.86rem;">
                                <input type="text" class="form-control" wire:model="rules.{{ $index }}.notes" placeholder="Optional notes / qualifications (e.g. Professor rank with 10+ publications)" style="font-size: 0.76rem; color: var(--color-text-secondary); margin-top: 0.25rem; background: rgba(0,0,0,0.15);">
                                @error("rules.{$index}.name") <div style="font-size: 0.7rem; color: var(--danger); margin-top: 0.2rem;">{{ $message }}</div> @enderror
                            </td>

                            <!-- Origin / Scope Field (Inside / Outside / Overseas / Arbiter) -->
                            <td>
                                <select class="form-control form-select" wire:model.live="rules.{{ $index }}.category" style="font-size: 0.82rem; font-weight: 600;">
                                    <option value="INTERNAL">🏢 Inside (Home Org/Dept)</option>
                                    <option value="EXTERNAL">🌐 Outside (Other Univ/Office)</option>
                                    <option value="INTERNATIONAL">✈️ International (Overseas)</option>
                                    <option value="INDEPENDENT">⚖️ Independent (Arbiter/Legal)</option>
                                </select>
                            </td>

                            <!-- Cadre Field (Faculty, Officer, Expert, Executive, Legal) -->
                            <td>
                                <select class="form-control form-select" wire:model.live="rules.{{ $index }}.cadre" style="font-size: 0.82rem;">
                                    <option value="FACULTY">🎓 Faculty Member</option>
                                    <option value="OFFICER">👔 Administrative Officer</option>
                                    <option value="EXPERT">🔬 Subject Specialist</option>
                                    <option value="EXECUTIVE">🏛️ Executive / Board</option>
                                    <option value="LEGAL">⚖️ Legal / Arbiter</option>
                                    <option value="ANY">👤 Any Qualified Staff</option>
                                </select>
                            </td>

                            <!-- Convener / Ex-Officio Toggle -->
                            <td style="text-align: center;">
                                <button type="button" class="badge" style="cursor: pointer; border: none; padding: 0.35rem 0.65rem; font-size: 0.74rem; font-weight: 700; transition: all 0.2s; {{ !empty($rule['is_mandatory']) ? 'background: rgba(99, 102, 241, 0.2); color: var(--primary); border: 1px solid rgba(99, 102, 241, 0.4);' : 'background: rgba(255, 255, 255, 0.05); color: var(--color-text-muted); border: 1px solid rgba(255, 255, 255, 0.1);' }}" wire:click="toggleMandatory({{ $index }})" title="Click to toggle Mandatory/Convener flag">
                                    {{ !empty($rule['is_mandatory']) ? '★ Mandatory' : '○ Standard' }}
                                </button>
                            </td>

                            <!-- Delete Action -->
                            <td style="text-align: right;">
                                <button type="button" class="btn btn-danger" style="padding: 0.3rem 0.55rem; font-size: 0.75rem;" wire:click="removeRule({{ $index }})" title="Remove this rule slot">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="18" y1="6" x2="6" y2="18"/>
                                        <line x1="6" y1="6" x2="18" y2="18"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center" style="padding: 2.5rem 1rem;">
                                <div style="color: var(--color-text-muted);">
                                    <p style="margin: 0 0 0.5rem 0; font-size: 0.95rem;">No dynamic constitution rules defined yet.</p>
                                    <button type="button" class="btn btn-primary" style="font-size: 0.8rem; padding: 0.4rem 0.9rem;" wire:click="addRule">
                                        + Add First Rule Slot
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Bottom Action Toolbar -->
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <button type="button" class="btn btn-secondary" style="font-size: 0.82rem; padding: 0.5rem 0.95rem; display: inline-flex; align-items: center; gap: 0.4rem;" wire:click="addRule">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    <span>+ Add Member Slot</span>
                </button>

                <button type="button" class="btn btn-secondary" style="font-size: 0.82rem; padding: 0.5rem 0.95rem;" wire:click="saveAsSystemDefault">
                    <span>💾 Save Quotas to Defaults</span>
                </button>
            </div>

            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="font-size: 0.82rem; color: var(--color-text-secondary);">
                    Total Prescribed: <strong style="color: var(--color-text-primary); font-family: monospace;">{{ $this->totalQuorum }} Members</strong>
                </div>

                <button type="button" class="btn btn-primary" style="padding: 0.65rem 1.6rem; font-size: 0.9rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.45rem;" wire:click="saveConstitution">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <span>Save Constitution Criteria</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Active Campaign Live Panel Quorum Inspection -->
    @if($currentCampaign && $metrics)
        <div class="card">
            <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem; margin-bottom: 1.25rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                <div>
                    <h4 style="font-size: 1rem; font-weight: 800; color: var(--color-text-primary); margin: 0 0 0.15rem 0;">
                        Live Committee Quorum Status on {{ $currentCampaign->title }}
                    </h4>
                    <p style="font-size: 0.8rem; color: var(--color-text-secondary); margin: 0;">
                        Tracks actual appointed operational committee members against the prescribed criteria
                    </p>
                </div>

                @if($metrics['is_complete'])
                    <span class="badge" style="background: rgba(16, 185, 129, 0.2); color: var(--success); border: 1px solid rgba(16, 185, 129, 0.4); padding: 0.4rem 0.8rem; font-size: 0.82rem; font-weight: 700;">
                        ✓ Statutory Quorum Satisfied ({{ $metrics['total_assigned'] }} / {{ $metrics['required_total'] }})
                    </span>
                @else
                    <span class="badge" style="background: rgba(245, 158, 11, 0.2); color: var(--warning); border: 1px solid rgba(245, 158, 11, 0.4); padding: 0.4rem 0.8rem; font-size: 0.82rem; font-weight: 700;">
                        ⏳ In Progress: {{ $metrics['total_assigned'] }} of {{ $metrics['required_total'] }} Members Assigned
                    </span>
                @endif
            </div>

            <!-- Categories Tally Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0.85rem;">
                @foreach($metrics['categories'] as $catKey => $catData)
                    <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                            <span style="font-size: 0.75rem; font-weight: 700; color: var(--color-text-secondary);">
                                {{ $catData['label'] }}
                            </span>
                            @if($catData['satisfied'])
                                <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: var(--success); font-size: 0.7rem; font-weight: 700;">✓ Quota Met</span>
                            @else
                                <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: var(--warning); font-size: 0.7rem; font-weight: 700;">{{ $catData['available'] }} Needed</span>
                            @endif
                        </div>
                        <div style="display: flex; align-items: baseline; gap: 0.4rem;">
                            <span style="font-size: 1.35rem; font-weight: 800; font-family: monospace; color: var(--color-text-primary);">
                                {{ $catData['assigned'] }} / {{ $catData['required'] }}
                            </span>
                            <span style="font-size: 0.76rem; color: var(--color-text-muted);">Allocated</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

