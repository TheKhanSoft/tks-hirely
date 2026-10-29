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

    <!-- Page Header & Segmented Engine Switcher -->
    <div class="card mb-6" style="background: linear-gradient(135deg, rgba(30, 41, 59, 0.8) 0%, rgba(15, 23, 42, 0.9) 100%); border-color: rgba(99, 102, 241, 0.25);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1.5rem; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 280px;">
                <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.4rem;">
                    <span class="badge" style="background: rgba(99, 102, 241, 0.2); color: var(--primary); font-weight: 800; font-size: 0.75rem; letter-spacing: 0.05em;">
                        GOVERNANCE & STATUTE ENGINE
                    </span>
                    <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; font-size: 0.75rem; font-weight: 700;">
                        MULTI-SECTOR DYNAMIC
                    </span>
                </div>
                <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--color-text-primary); margin: 0 0 0.35rem 0; display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                        <path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                        <path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                        <path d="M7 21h10"/>
                        <path d="M12 3v18"/>
                        <path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"/>
                    </svg>
                    <span>Statutory Criteria & Committee Constitutions Engine</span>
                </h2>
                <p style="font-size: 0.88rem; color: var(--color-text-secondary); margin: 0; line-height: 1.5;">
                    Dynamic governance engine for creating and managing custom statutory selection boards, routing flows, eligibility criteria, and member quotas across Higher Education, Corporate Enterprise, Healthcare, and Civil Service.
                </p>
            </div>

            <!-- Segmented Top Tabs -->
            <div style="display: flex; gap: 0.5rem; background: rgba(15, 23, 42, 0.6); padding: 0.35rem; border-radius: var(--radius-md); border: 1px solid rgba(255, 255, 255, 0.08);">
                <button type="button" 
                    wire:click="setTab('campaign_constitution')" 
                    class="btn {{ $activeTab === 'campaign_constitution' ? 'btn-primary' : 'btn-secondary' }}"
                    style="padding: 0.55rem 1rem; font-size: 0.82rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.45rem;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <circle cx="12" cy="12" r="6"/>
                        <circle cx="12" cy="12" r="2"/>
                    </svg>
                    <span>Campaign Quotas</span>
                </button>
                <button type="button" 
                    wire:click="setTab('board_registry')" 
                    class="btn {{ $activeTab === 'board_registry' ? 'btn-primary' : 'btn-secondary' }}"
                    style="padding: 0.55rem 1rem; font-size: 0.82rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.45rem;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 21h18"/>
                        <path d="M5 21V7l8-4v18"/>
                        <path d="M19 21V11l-6-4"/>
                    </svg>
                    <span>Board & Flow Registry ({{ $registeredBoards->count() }})</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- VIEW 1: CAMPAIGN CONSTITUTION & QUOTA TUNING                               -->
    <!-- ========================================================================= -->
    @if($activeTab === 'campaign_constitution')
        <!-- Campaign Selector & Master Board Loader -->
        <div class="card mb-6" style="padding: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 280px;">
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

                <div style="flex: 1; min-width: 280px;">
                    <label style="display: block; font-size: 0.74rem; text-transform: uppercase; font-weight: 700; color: var(--color-text-muted); margin-bottom: 0.35rem; letter-spacing: 0.05em;">
                        APPLY REGISTERED STATUTORY BOARD CONSTITUTION
                    </label>
                    <select class="form-control form-select" wire:change="applyBoardConstitutionToCampaign($event.target.value)" style="font-weight: 600; font-size: 0.9rem;">
                        <option value="">-- Choose Registered Statutory Board --</option>
                        @foreach($registeredBoards as $b)
                            <option value="{{ $b->id }}">{{ $b->name }} [{{ $b->organization_sector }} - {{ $b->required_total }} Members]</option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; gap: 0.5rem; align-items: flex-end;">
                    <button type="button" class="btn btn-primary" style="padding: 0.65rem 1.4rem; font-size: 0.88rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.45rem;" wire:click="saveConstitution">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/>
                            <polyline points="7 3 7 8 15 8"/>
                        </svg>
                        <span>Save Criteria</span>
                    </button>
                    @if($currentCampaign)
                        <a href="/admin/campaigns" class="btn btn-secondary" style="padding: 0.65rem 1rem; font-size: 0.85rem;" title="Go to Campaign Allocations">
                            <span>Panel Allocation ↗</span>
                        </a>
                    @endif
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
                    <span>📚 Lecturer Panel (5 Members • 4 Internal)</span>
                </button>
                <button type="button" class="btn btn-secondary" style="padding: 0.45rem 0.85rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.4rem;" wire:click="applyPreset('corporate_board')">
                    <span>🏢 Corporate Panel (5 Members • 2 Industry)</span>
                </button>
                <button type="button" class="btn btn-secondary" style="padding: 0.45rem 0.85rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.4rem;" wire:click="applyPreset('public_sector')">
                    <span>🏛️ Public Sector Board (4 Members • 1 Legal Arbiter)</span>
                </button>
                <button type="button" class="btn btn-secondary" style="padding: 0.45rem 0.85rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.4rem;" wire:click="applyPreset('blank')">
                    <span>➕ Blank Custom Board</span>
                </button>
            </div>
        </div>

        <!-- Live Quota Summary Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
            <div class="metric-card" style="border-left: 4px solid var(--primary);">
                <div class="metric-label">TOTAL QUORUM</div>
                <div class="metric-value" style="color: var(--primary);">{{ $this->totalQuorum }}</div>
                <div class="metric-subtext">Statutory required members</div>
            </div>
            <div class="metric-card" style="border-left: 4px solid #3b82f6;">
                <div class="metric-label">INTERNAL SLOTS</div>
                <div class="metric-value" style="color: #3b82f6;">{{ $this->internalCount }}</div>
                <div class="metric-subtext">Home institution members</div>
            </div>
            <div class="metric-card" style="border-left: 4px solid #10b981;">
                <div class="metric-label">EXTERNAL SLOTS</div>
                <div class="metric-value" style="color: #10b981;">{{ $this->externalCount }}</div>
                <div class="metric-subtext">Other institutions / industry</div>
            </div>
            <div class="metric-card" style="border-left: 4px solid #8b5cf6;">
                <div class="metric-label">INTERNATIONAL SLOTS</div>
                <div class="metric-value" style="color: #8b5cf6;">{{ $this->internationalCount }}</div>
                <div class="metric-subtext">Overseas specialists</div>
            </div>
            <div class="metric-card" style="border-left: 4px solid #f59e0b;">
                <div class="metric-label">INDEPENDENT / ARBITERS</div>
                <div class="metric-value" style="color: #f59e0b;">{{ $this->independentCount }}</div>
                <div class="metric-subtext">Legal / governance arbiters</div>
            </div>
        </div>

        <!-- Committee Constitution Form & Dynamic Rules Table -->
        <div class="card mb-6" style="padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h3 style="font-size: 1.15rem; font-weight: 700; margin: 0 0 0.25rem 0; color: var(--color-text-primary);">
                        Constitutional Quorum Slots & Rules
                    </h3>
                    <p style="font-size: 0.82rem; color: var(--color-text-secondary); margin: 0;">
                        Specify each role, exact seat quota, category origin, cadre type, and whether attendance is mandatory.
                    </p>
                </div>
                <button type="button" class="btn btn-secondary" wire:click="addRule" style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.82rem; font-weight: 700;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    <span>Add Role Slot</span>
                </button>
            </div>

            <!-- Title & Description -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="form-label" style="font-size: 0.78rem; font-weight: 700;">CONSTITUTION TITLE</label>
                    <input type="text" class="form-control" wire:model.defer="constitution_title" placeholder="e.g. Statutory Selection Committee for Assistant Professor" />
                    @error('constitution_title') <span class="text-danger" style="font-size: 0.75rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="form-label" style="font-size: 0.78rem; font-weight: 700;">EXTERNAL PEER REVIEWERS REQUIRED</label>
                    <input type="number" min="0" max="20" class="form-control" wire:model.defer="required_peer_reviewers" />
                    <span style="font-size: 0.72rem; color: var(--color-text-muted);">Number of subject referees for external research peer review (e.g. 3 for Asst Prof, 0 for Corporate).</span>
                </div>
            </div>

            <div class="mb-5">
                <label class="form-label" style="font-size: 0.78rem; font-weight: 700;">STATUTORY DESCRIPTION & CITATION</label>
                <textarea class="form-control" rows="2" wire:model.defer="constitution_description" placeholder="Statute citation or justification notes..."></textarea>
            </div>

            <!-- Rules Table -->
            <div class="table-responsive" style="border: 1px solid rgba(255, 255, 255, 0.08); border-radius: var(--radius-sm); overflow: hidden;">
                <table class="table" style="margin: 0;">
                    <thead style="background: rgba(15, 23, 42, 0.6); font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.05em;">
                        <tr>
                            <th style="width: 32%;">ROLE TITLE / DESIGNATION</th>
                            <th style="width: 14%; text-align: center;">QUOTA COUNT</th>
                            <th style="width: 18%;">ORIGIN CATEGORY</th>
                            <th style="width: 16%;">CADRE & NATURE</th>
                            <th style="width: 10%; text-align: center;">MANDATORY</th>
                            <th style="width: 10%; text-align: right;">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rules as $idx => $rule)
                            <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.04);">
                                <td style="vertical-align: middle;">
                                    <input type="text" class="form-control" wire:model.defer="rules.{{ $idx }}.name" placeholder="e.g. Dean of Faculty, External Expert..." style="font-size: 0.85rem; font-weight: 600;" />
                                    @error("rules.{$idx}.name") <span class="text-danger" style="font-size: 0.72rem;">{{ $message }}</span> @enderror
                                </td>
                                <td style="vertical-align: middle; text-align: center;">
                                    <div style="display: inline-flex; align-items: center; gap: 0.35rem;">
                                        <button type="button" class="btn btn-secondary" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;" wire:click="updateRuleCount({{ $idx }}, -1)">-</button>
                                        <span style="font-weight: 800; min-width: 24px; text-align: center; color: var(--color-text-primary);">{{ $rule['count'] ?? 1 }}</span>
                                        <button type="button" class="btn btn-secondary" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;" wire:click="updateRuleCount({{ $idx }}, 1)">+</button>
                                    </div>
                                </td>
                                <td style="vertical-align: middle;">
                                    <select class="form-control form-select" wire:model.defer="rules.{{ $idx }}.category" style="font-size: 0.82rem; font-weight: 600;">
                                        <option value="INTERNAL">INTERNAL (Home Inst.)</option>
                                        <option value="EXTERNAL">EXTERNAL (Other Inst.)</option>
                                        <option value="INTERNATIONAL">INTERNATIONAL (Overseas)</option>
                                        <option value="INDEPENDENT">INDEPENDENT (Arbiter)</option>
                                    </select>
                                </td>
                                <td style="vertical-align: middle;">
                                    <div style="display: flex; gap: 0.35rem;">
                                        <select class="form-control form-select" wire:model.defer="rules.{{ $idx }}.cadre" style="font-size: 0.78rem; flex: 1;">
                                            <option value="FACULTY">Faculty</option>
                                            <option value="OFFICER">Officer</option>
                                            <option value="EXPERT">Expert</option>
                                            <option value="EXECUTIVE">Executive</option>
                                            <option value="LEGAL">Legal</option>
                                        </select>
                                        <select class="form-control form-select" wire:model.defer="rules.{{ $idx }}.role_nature" style="font-size: 0.78rem; flex: 1;">
                                            <option value="CONVENER">Convener</option>
                                            <option value="MEMBER">Member</option>
                                            <option value="SECRETARY">Secretary</option>
                                            <option value="EXPERT">Expert</option>
                                            <option value="ARBITER">Arbiter</option>
                                        </select>
                                    </div>
                                </td>
                                <td style="vertical-align: middle; text-align: center;">
                                    <button type="button" 
                                        wire:click="toggleMandatory({{ $idx }})" 
                                        class="badge" 
                                        style="background: {{ !empty($rule['is_mandatory']) ? 'rgba(16, 185, 129, 0.2)' : 'rgba(148, 163, 184, 0.1)' }}; color: {{ !empty($rule['is_mandatory']) ? '#10b981' : '#94a3b8' }}; border: 1px solid {{ !empty($rule['is_mandatory']) ? 'rgba(16, 185, 129, 0.4)' : 'rgba(148, 163, 184, 0.2)' }}; cursor: pointer; padding: 0.3rem 0.6rem;">
                                        {{ !empty($rule['is_mandatory']) ? 'Mandatory' : 'Optional' }}
                                    </button>
                                </td>
                                <td style="vertical-align: middle; text-align: right;">
                                    <button type="button" class="btn btn-secondary" style="padding: 0.3rem 0.6rem; color: #ef4444;" wire:click="removeRule({{ $idx }})" title="Remove Slot">
                                        ✕
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 2rem; color: var(--color-text-muted);">
                                    No dynamic rules configured yet. Click "Add Role Slot" or choose a Preset above.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Save Buttons Footer -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                    <input type="checkbox" id="enforce_strict" wire:model.defer="enforce_strict_quota" style="width: 16px; height: 16px;" />
                    <label for="enforce_strict" style="font-size: 0.85rem; font-weight: 600; cursor: pointer; color: var(--color-text-primary);">
                        Strict Quota Enforcement: Prevent committee approval if slot quotas are under or over allocated
                    </label>
                </div>

                <div style="display: flex; gap: 0.5rem;">
                    <button type="button" class="btn btn-secondary" wire:click="saveAsSystemDefault" style="padding: 0.6rem 1rem; font-size: 0.82rem;">
                        Save as System Default
                    </button>
                    <button type="button" class="btn btn-primary" wire:click="saveConstitution" style="padding: 0.6rem 1.5rem; font-size: 0.88rem; font-weight: 700;">
                        Save & Apply to Campaign
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- VIEW 2: MASTER STATUTORY BOARDS & COMMITTEES REGISTRY (MULTI-SECTOR)       -->
    <!-- ========================================================================= -->
    @if($activeTab === 'board_registry')
        <!-- Sector Filter Bar & Action Header -->
        <div class="card mb-6" style="padding: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                    <span style="font-size: 0.74rem; text-transform: uppercase; font-weight: 700; color: var(--color-text-muted); margin-right: 0.25rem;">
                        SECTOR FILTER:
                    </span>
                    @foreach(['ALL' => 'All Sectors', 'ACADEMIC' => '🎓 Higher Ed', 'CORPORATE' => '🏢 Corporate', 'HEALTHCARE' => '🩺 Healthcare', 'PUBLIC_SECTOR' => '🏛️ Public Sector', 'GENERAL' => '🌐 General'] as $secKey => $secLabel)
                        <button type="button" 
                            wire:click="$set('sectorFilter', '{{ $secKey }}')" 
                            class="btn {{ $sectorFilter === $secKey ? 'btn-primary' : 'btn-secondary' }}"
                            style="padding: 0.4rem 0.8rem; font-size: 0.78rem; font-weight: 600;">
                            {{ $secLabel }}
                        </button>
                    @endforeach
                </div>

                <button type="button" class="btn btn-primary" wire:click="openCreateBoardModal" style="display: inline-flex; align-items: center; gap: 0.45rem; font-size: 0.85rem; font-weight: 700; padding: 0.6rem 1.2rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    <span>+ New Statutory Board / Committee</span>
                </button>
            </div>
        </div>

        <!-- Registered Boards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @forelse($registeredBoards as $board)
                @php $badge = $board->sector_badge; @endphp
                <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; border-color: rgba(255, 255, 255, 0.08); transition: transform 0.15s ease, border-color 0.15s ease;">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                            <span class="badge" style="background: {{ $badge['bg'] }}; color: {{ $badge['color'] }}; font-weight: 800; font-size: 0.72rem; letter-spacing: 0.04em;">
                                {{ $badge['icon'] }} {{ $badge['label'] }}
                            </span>
                            <span class="badge" style="background: rgba(255, 255, 255, 0.06); color: var(--color-text-secondary); font-size: 0.72rem; font-weight: 700;">
                                Quorum: {{ $board->required_total }} Members
                            </span>
                        </div>

                        <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--color-text-primary); margin: 0 0 0.4rem 0; line-height: 1.35;">
                            {{ $board->name }}
                        </h3>

                        <p style="font-size: 0.82rem; color: var(--color-text-secondary); margin: 0 0 1rem 0; line-height: 1.5; min-height: 48px;">
                            {{ Str::limit($board->description ?: 'Statutory board constitution and routing pipeline criteria.', 120) }}
                        </p>

                        <!-- Breakdown pills -->
                        <div style="display: flex; flex-wrap: wrap; gap: 0.35rem; margin-bottom: 1rem;">
                            <span class="badge" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6; font-size: 0.7rem;">
                                {{ $board->required_internal }} Internal
                            </span>
                            <span class="badge" style="background: rgba(16, 185, 129, 0.12); color: #10b981; font-size: 0.7rem;">
                                {{ $board->required_external }} External
                            </span>
                            @if($board->required_international > 0)
                                <span class="badge" style="background: rgba(139, 92, 246, 0.12); color: #8b5cf6; font-size: 0.7rem;">
                                    {{ $board->required_international }} Overseas
                                </span>
                            @endif
                            @if($board->required_independent > 0)
                                <span class="badge" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b; font-size: 0.7rem;">
                                    {{ $board->required_independent }} Arbiter
                                </span>
                            @endif
                            @if($board->required_peer_reviewers > 0)
                                <span class="badge" style="background: rgba(236, 72, 153, 0.12); color: #ec4899; font-size: 0.7rem;">
                                    {{ $board->required_peer_reviewers }} Peer Referees
                                </span>
                            @endif
                        </div>

                        <!-- Routing pipeline summary preview -->
                        <div style="background: rgba(15, 23, 42, 0.5); border-radius: var(--radius-sm); padding: 0.65rem 0.85rem; margin-bottom: 1.25rem;">
                            <div style="font-size: 0.7rem; text-transform: uppercase; font-weight: 700; color: var(--color-text-muted); margin-bottom: 0.35rem; letter-spacing: 0.05em;">
                                PIPELINE ROUTING FLOW
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                @foreach(array_slice($board->getEffectiveRoutingFlow(), 0, 3) as $flowStep)
                                    <div style="font-size: 0.75rem; color: var(--color-text-secondary); display: flex; align-items: center; gap: 0.35rem;">
                                        <span style="color: var(--primary); font-weight: 800;">{{ $flowStep['step'] }}.</span>
                                        <span>{{ $flowStep['name'] }}</span>
                                        <span style="margin-left: auto; font-size: 0.68rem; color: var(--color-text-muted);">({{ $flowStep['sla_days'] ?? 7 }}d)</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Actions Bar -->
                    <div style="border-top: 1px solid rgba(255, 255, 255, 0.06); padding-top: 0.85rem; display: flex; justify-content: space-between; align-items: center; gap: 0.5rem;">
                        <button type="button" class="btn btn-secondary" wire:click="applyBoardConstitutionToCampaign({{ $board->id }})" style="font-size: 0.78rem; font-weight: 700; padding: 0.4rem 0.75rem; color: var(--primary);" title="Load into Active Campaign">
                            ⚡ Apply to Campaign
                        </button>

                        <div class="action-btn-group">
                            <button type="button" 
                                    class="action-btn action-btn-edit" 
                                    wire:click="openEditBoardModal({{ $board->id }})" 
                                    data-tooltip="Edit Statutory Board Constitution"
                                    aria-label="Edit Constitution">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>
                            <button type="button" 
                                    class="action-btn action-btn-view" 
                                    wire:click="cloneBoardModel({{ $board->id }})" 
                                    data-tooltip="Clone / Duplicate Board Definition"
                                    aria-label="Clone Board">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="14" height="14" x="8" y="8" rx="2" ry="2"/>
                                    <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>
                                </svg>
                            </button>
                            <button type="button" 
                                    class="action-btn action-btn-delete" 
                                    wire:click="deleteBoardModel({{ $board->id }})" 
                                    wire:confirm="Are you sure you want to delete this Statutory Board Constitution?" 
                                    data-tooltip="Delete Board Definition"
                                    aria-label="Delete Board">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                    <line x1="10" y1="11" x2="10" y2="17"/>
                                    <line x1="14" y1="11" x2="14" y2="17"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 3rem 1.5rem;">
                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🏛️</div>
                    <h4 style="font-size: 1.1rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                        No Statutory Boards Found for Selected Sector
                    </h4>
                    <p style="font-size: 0.85rem; color: var(--color-text-secondary); margin-bottom: 1.25rem;">
                        Create your first custom statutory selection board or reset the sector filter.
                    </p>
                    <button type="button" class="btn btn-primary" wire:click="openCreateBoardModal" style="padding: 0.6rem 1.25rem; font-size: 0.85rem; font-weight: 700;">
                        + Create Statutory Board
                    </button>
                </div>
            @endforelse
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODAL: ADD / EDIT STATUTORY BOARD & COMMITTEE CONSTITUTION                 -->
    <!-- ========================================================================= -->
    @if($showBoardModal)
        <div style="position: fixed; inset: 0; background: rgba(0, 0, 0, 0.75); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 1.5rem;">
            <div class="card" style="width: 100%; max-width: 900px; max-height: 90vh; overflow-y: auto; background: #0f172a; border-color: rgba(99, 102, 241, 0.35); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); padding: 1.75rem;">
                
                <!-- Modal Header -->
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.3rem;">
                            <span class="badge" style="background: rgba(99, 102, 241, 0.2); color: var(--primary); font-size: 0.72rem; font-weight: 800;">
                                STATUTORY GOVERNANCE BUILDER
                            </span>
                            <span class="badge" style="background: rgba(255, 255, 255, 0.06); color: var(--color-text-secondary); font-size: 0.72rem;">
                                {{ $isEditBoard ? 'EDITING CONSTITUTION' : 'NEW CONSTITUTION' }}
                            </span>
                        </div>
                        <h3 style="font-size: 1.35rem; font-weight: 800; color: var(--color-text-primary); margin: 0;">
                            {{ $isEditBoard ? 'Configure Statutory Board Constitution' : 'Create New Statutory Board Constitution' }}
                        </h3>
                    </div>
                    <button type="button" wire:click="closeBoardModal" style="background: none; border: none; color: var(--color-text-muted); cursor: pointer; font-size: 1.25rem;">✕</button>
                </div>

                <!-- Form Fields -->
                <form wire:submit.prevent="saveBoardModel">
                    <!-- General Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="form-label" style="font-size: 0.78rem; font-weight: 700;">BOARD / COMMITTEE NAME</label>
                            <input type="text" class="form-control" wire:model.defer="board_name" placeholder="e.g. Higher Education Selection Board, Executive Panel..." required />
                            @error('board_name') <span class="text-danger" style="font-size: 0.72rem;">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="form-label" style="font-size: 0.78rem; font-weight: 700;">ORGANIZATION SECTOR</label>
                            <select class="form-control form-select" wire:model.defer="board_sector">
                                <option value="ACADEMIC">🎓 Higher Education / Academic</option>
                                <option value="CORPORATE">🏢 Corporate & Enterprise</option>
                                <option value="HEALTHCARE">🩺 Healthcare & Medical</option>
                                <option value="PUBLIC_SECTOR">🏛️ Public Sector & Civil Service</option>
                                <option value="JUDICIAL">⚖️ Judicial & Legal</option>
                                <option value="DEFENSE">🛡️ Security & Defense</option>
                                <option value="GENERAL">🌐 General / Cross-Sector</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-size: 0.78rem; font-weight: 700;">DESCRIPTION & LEGAL STATUTE CITATION</label>
                        <textarea class="form-control" rows="2" wire:model.defer="board_description" placeholder="Statute citation, quorum constitution terms, or governing bylaws..."></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                        <div>
                            <label class="form-label" style="font-size: 0.78rem; font-weight: 700;">EXTERNAL PEER REVIEWERS REQUIRED</label>
                            <input type="number" min="0" max="20" class="form-control" wire:model.defer="board_required_peer_reviewers" />
                            <span style="font-size: 0.72rem; color: var(--color-text-muted);">Set to 0 if this board/sector does not mandate external research peer reviews.</span>
                        </div>
                        <div style="display: flex; align-items: center; padding-top: 1.5rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.82rem; font-weight: 600;">
                                <input type="checkbox" wire:model.defer="board_enforce_strict_quota" style="width: 16px; height: 16px;" />
                                <span>Strict Quorum Enforcement (Lock panel if quorum violated)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Quorum Roles Breakdown Repeater -->
                    <div style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: var(--radius-sm); padding: 1.25rem; margin-bottom: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem;">
                            <div>
                                <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--color-text-primary); margin: 0;">
                                    Statutory Quorum Members & Cadres
                                </h4>
                                <span style="font-size: 0.72rem; color: var(--color-text-muted);">Define member seats, categories, and mandatory roles.</span>
                            </div>
                            <button type="button" class="btn btn-secondary" wire:click="addBoardRole" style="padding: 0.35rem 0.75rem; font-size: 0.78rem; font-weight: 700;">
                                + Add Member Slot
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table" style="margin: 0; font-size: 0.82rem;">
                                <thead style="font-size: 0.7rem; text-transform: uppercase;">
                                    <tr>
                                        <th style="width: 35%;">ROLE / TITLE</th>
                                        <th style="width: 15%;">SEATS</th>
                                        <th style="width: 25%;">ORIGIN CATEGORY</th>
                                        <th style="width: 15%;">CADRE</th>
                                        <th style="width: 10%; text-align: right;">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($board_roles as $rIdx => $bRole)
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control" wire:model.defer="board_roles.{{ $rIdx }}.name" placeholder="e.g. Panel Convener, External Expert" style="font-size: 0.82rem;" required />
                                            </td>
                                            <td>
                                                <input type="number" min="1" max="50" class="form-control" wire:model.defer="board_roles.{{ $rIdx }}.count" style="font-size: 0.82rem;" required />
                                            </td>
                                            <td>
                                                <select class="form-control form-select" wire:model.defer="board_roles.{{ $rIdx }}.category" style="font-size: 0.8rem;">
                                                    <option value="INTERNAL">INTERNAL (Home Inst.)</option>
                                                    <option value="EXTERNAL">EXTERNAL (Outside Inst.)</option>
                                                    <option value="INTERNATIONAL">INTERNATIONAL (Overseas)</option>
                                                    <option value="INDEPENDENT">INDEPENDENT (Arbiter)</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control form-select" wire:model.defer="board_roles.{{ $rIdx }}.cadre" style="font-size: 0.8rem;">
                                                    <option value="FACULTY">Faculty</option>
                                                    <option value="OFFICER">Officer</option>
                                                    <option value="EXPERT">Expert</option>
                                                    <option value="EXECUTIVE">Executive</option>
                                                    <option value="LEGAL">Legal</option>
                                                </select>
                                            </td>
                                            <td style="text-align: right;">
                                                <button type="button" class="btn btn-secondary" wire:click="removeBoardRole({{ $rIdx }})" style="padding: 0.25rem 0.5rem; color: #ef4444;">✕</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pipeline Routing Flow Repeater -->
                    <div style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: var(--radius-sm); padding: 1.25rem; margin-bottom: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem;">
                            <div>
                                <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--color-text-primary); margin: 0;">
                                    Application Routing & Pipeline Flow
                                </h4>
                                <span style="font-size: 0.72rem; color: var(--color-text-muted);">Stages through which applications progress under this board.</span>
                            </div>
                            <button type="button" class="btn btn-secondary" wire:click="addRoutingStep" style="padding: 0.35rem 0.75rem; font-size: 0.78rem; font-weight: 700;">
                                + Add Flow Stage
                            </button>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            @foreach($board_routing_flow as $sIdx => $step)
                                <div style="display: flex; align-items: center; gap: 0.5rem; background: rgba(30, 41, 59, 0.4); padding: 0.5rem 0.75rem; border-radius: var(--radius-sm);">
                                    <span style="font-weight: 800; color: var(--primary); font-size: 0.85rem; min-width: 24px;">{{ $step['step'] ?? ($sIdx + 1) }}.</span>
                                    <input type="text" class="form-control" wire:model.defer="board_routing_flow.{{ $sIdx }}.name" placeholder="Stage Name" style="font-size: 0.82rem; flex: 2;" required />
                                    <select class="form-control form-select" wire:model.defer="board_routing_flow.{{ $sIdx }}.cadre" style="font-size: 0.8rem; flex: 1;">
                                        <option value="OFFICER">Officer / Scrutiny</option>
                                        <option value="EXPERT">Peer Expert</option>
                                        <option value="FACULTY">Faculty Board</option>
                                        <option value="EXECUTIVE">Executive / Syndicate</option>
                                    </select>
                                    <div style="display: flex; align-items: center; gap: 0.25rem;">
                                        <input type="number" min="1" max="60" class="form-control" wire:model.defer="board_routing_flow.{{ $sIdx }}.sla_days" style="width: 70px; font-size: 0.8rem;" title="SLA Days" />
                                        <span style="font-size: 0.72rem; color: var(--color-text-muted);">days</span>
                                    </div>
                                    <button type="button" class="btn btn-secondary" wire:click="removeRoutingStep({{ $sIdx }})" style="padding: 0.25rem 0.5rem; color: #ef4444;">✕</button>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 1rem; border-top: 1px solid rgba(255, 255, 255, 0.08);">
                        <button type="button" class="btn btn-secondary" wire:click="closeBoardModal" style="padding: 0.6rem 1.25rem; font-size: 0.85rem;">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.75rem; font-size: 0.88rem; font-weight: 700;">
                            {{ $isEditBoard ? 'Update Statutory Board' : 'Save & Empanel Board' }}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif
</div>
