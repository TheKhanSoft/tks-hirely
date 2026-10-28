<div>
    <!-- Header Card -->
    <x-card title="📢 Campaigns & Committee Panel Allocation" subtitle="Configure recruitment tracks, define dynamic scoring rules, and allocate members to evaluation panels.">
        <div style="position: absolute; top: 1.5rem; right: 1.5rem;">
            @if(!$isFormOpen && $activeTab === 'campaigns')
                <x-button variant="primary" wire:click="openCreateForm">➕ Create Campaign</x-button>
            @endif
        </div>
    </x-card>

    <!-- Tabs Navigation -->
    <div class="flex gap-4 mb-6" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-top: 1.5rem;">
        <x-button :variant="$activeTab === 'campaigns' ? 'primary' : 'secondary'" wire:click="setTab('campaigns')">
            📢 Active Job Campaigns
        </x-button>
        <x-button :variant="$activeTab === 'committees' ? 'primary' : 'secondary'" wire:click="setTab('committees')">
            👥 Committee Allocations
        </x-button>
    </div>

    <!-- ========================================== -->
    <!-- 📢 TAB 1: ACTIVE CAMPAIGNS BREAD -->
    <!-- ========================================== -->
    @if ($activeTab === 'campaigns')
        @if($isFormOpen)
            <x-card :title="$isEditMode ? '✍️ Modify Job Campaign' : '➕ Create Job Campaign'">
                <form wire:submit.prevent="saveCampaign">
                    <div class="grid grid-cols-2 gap-4">
                        <x-input label="Job Code *" :error="$errors->first('job_code')">
                            <input type="text" class="form-control" wire:model="job_code" placeholder="e.g. CS-FAC-2026">
                        </x-input>
                        <x-input label="Job Title *" :error="$errors->first('title')">
                            <input type="text" class="form-control" wire:model="title" placeholder="e.g. Assistant Professor">
                        </x-input>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <x-input label="Track Type *">
                            <select class="form-control form-select" wire:model.live="track_type">
                                <option value="ACADEMIC">Academic Faculty Track</option>
                                <option value="ADMINISTRATIVE">Administrative Officer Track</option>
                                <option value="SUPPORT">Support Staff Track</option>
                            </select>
                        </x-input>
                        <x-input label="Department *" :error="$errors->first('department')">
                            <input type="text" class="form-control" wire:model="department" placeholder="e.g. Department of Physics">
                        </x-input>
                        <x-input label="Application Fee (PKR) *" :error="$errors->first('application_fee')">
                            <input type="number" class="form-control" wire:model="application_fee">
                        </x-input>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <x-input label="Opening Date *" :error="$errors->first('opening_date')">
                            <input type="datetime-local" class="form-control" wire:model="opening_date">
                        </x-input>
                        <x-input label="Closing Date *" :error="$errors->first('closing_date')">
                            <input type="datetime-local" class="form-control" wire:model="closing_date">
                        </x-input>
                    </div>

                    <!-- Transcript Proof Requirement Configuration -->
                    <div style="border: 1px solid var(--border-color); border-radius: 8px; padding: 1rem 1.25rem; background: var(--bg-secondary); margin-top: 1rem;" class="flex justify-between align-center">
                        <div>
                            <strong style="color: var(--color-text-primary); font-size: 0.95rem; display: block;">📜 Academic Transcript Proof Requirement</strong>
                            <small style="color: var(--color-text-secondary);">Choose whether applicants must upload degree transcripts/certificates or if uploads are optional.</small>
                        </div>
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-weight: 700; color: var(--color-text-primary);">
                            <input type="checkbox" wire:model="require_transcript_proof" style="width: 1.25rem; height: 1.25rem;">
                            <span>{{ $require_transcript_proof ? 'Mandatory (Required *)' : 'Optional' }}</span>
                        </label>
                    </div>

                    <!-- DYNAMIC RULE MAPPER SECTION -->
                    @if($track_type === 'ACADEMIC')
                        <div style="border: 1px solid var(--secondary-glow); border-radius: 8px; padding: 1.5rem; background: rgba(6,182,212,0.02);" class="mt-6 mb-6">
                            <h4 style="color: var(--secondary); font-size: 1.1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;" class="mb-4">
                                ⚙️ Dynamic Rule Mapper - Academic Merit Weights
                            </h4>

                            <div class="grid grid-cols-3 gap-4 mb-4">
                                <x-input label="Gold Medal Score">
                                    <input type="number" step="0.1" class="form-control" wire:model="gold_medal_weight">
                                </x-input>
                                <x-input label="Silver Medal Score">
                                    <input type="number" step="0.1" class="form-control" wire:model="silver_medal_weight">
                                </x-input>
                                <x-input label="Bronze Medal Score">
                                    <input type="number" step="0.1" class="form-control" wire:model="bronze_medal_weight">
                                </x-input>
                            </div>

                            <div class="grid grid-cols-3 gap-4 mb-4">
                                <x-input label="HJRS W-Category weight">
                                    <input type="number" step="0.05" class="form-control" wire:model="w_category_weight">
                                </x-input>
                                <x-input label="HJRS X-Category weight">
                                    <input type="number" step="0.05" class="form-control" wire:model="x_category_weight">
                                </x-input>
                                <x-input label="HJRS Y-Category weight">
                                    <input type="number" step="0.05" class="form-control" wire:model="y_category_weight">
                                </x-input>
                            </div>

                            <div class="grid grid-cols-3 gap-4 mb-4">
                                <x-input label="HJRS Z-Category weight">
                                    <input type="number" step="0.05" class="form-control" wire:model="z_category_weight">
                                </x-input>
                                <x-input label="Leader Organizer Event Marks">
                                    <input type="number" step="0.1" class="form-control" wire:model="organizer_leader_weight">
                                </x-input>
                                <x-input label="Associate Organizer Event Marks">
                                    <input type="number" step="0.1" class="form-control" wire:model="organizer_associate_weight">
                                </x-input>
                            </div>

                            <div class="grid grid-cols-3 gap-4">
                                <x-input label="Int. Presenter Event Marks">
                                    <input type="number" step="0.1" class="form-control" wire:model="paper_international_weight">
                                </x-input>
                                <x-input label="Nat. Presenter Event Marks">
                                    <input type="number" step="0.1" class="form-control" wire:model="paper_national_weight">
                                </x-input>
                                <x-input label="Event Attendee Marks">
                                    <input type="number" step="0.05" class="form-control" wire:model="event_attendee_weight">
                                </x-input>
                            </div>

                            <div class="grid grid-cols-4 gap-4 mt-4" style="border-top: 1px solid var(--border-color); padding-top: 1rem;">
                                <x-input label="Project Rate (Marks / Million)">
                                    <input type="number" step="0.1" class="form-control" wire:model="project_million_rate">
                                </x-input>
                                <x-input label="Project Cap Marks">
                                    <input type="number" step="0.1" class="form-control" wire:model="project_max_marks">
                                </x-input>
                                <x-input label="Patent Granted Marks">
                                    <input type="number" step="0.1" class="form-control" wire:model="patent_granted_weight">
                                </x-input>
                                <x-input label="Patent Licensed Marks">
                                    <input type="number" step="0.1" class="form-control" wire:model="patent_licensed_weight">
                                </x-input>
                            </div>
                        </div>
                    @endif

                    <div class="flex gap-4 mt-6" style="border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                        <x-button variant="primary" type="submit">💾 Save Campaign</x-button>
                        <x-button variant="secondary" wire:click="$set('isFormOpen', false)">Cancel</x-button>
                    </div>
                </form>
            </x-card>
        @else
            <!-- Campaigns list Table -->
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Job Code</th>
                            <th>Title</th>
                            <th>Track</th>
                            <th>Department</th>
                            <th>Closing Date</th>
                            <th>Fee</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($campaigns as $camp)
                            <tr>
                                <td><code style="color: var(--secondary); font-weight: 700;">{{ $camp->job_code }}</code></td>
                                <td><strong>{{ $camp->title }}</strong></td>
                                <td>
                                    <x-badge type="appealed">{{ $camp->track_type }}</x-badge>
                                </td>
                                <td>{{ $camp->department }}</td>
                                <td><span style="font-size: 0.9rem; color: var(--color-text-secondary);">{{ $camp->closing_date?->format('Y-m-d') }}</span></td>
                                <td>PKR {{ number_format($camp->application_fee, 2) }}</td>
                                <td>
                                    <div class="flex gap-2">
                                        <x-button variant="secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="openEditForm({{ $camp->id }})">Edit</x-button>
                                        <x-button variant="danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="deleteCampaign({{ $camp->id }})" onclick="confirm('Are you sure you want to delete this campaign? This will cascade delete applications and committees!') || event.stopImmediatePropagation()">Delete</x-button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center">No campaigns found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

    <!-- ========================================== -->
    <!-- 👥 TAB 2: COMMITTEE ALLOCATIONS BREAD -->
    <!-- ========================================== -->
    @elseif ($activeTab === 'committees')
        <!-- Allocation form -->
        <x-card title="👥 Allocate Operational Committee Member" style="border-color: var(--secondary-glow);">
            <form wire:submit.prevent="assignCommitteeMember">
                <div class="grid grid-cols-3 gap-6">
                    <x-input label="Job Campaign *" :error="$errors->first('selected_campaign_id')">
                        <select class="form-control form-select" wire:model="selected_campaign_id">
                            <option value="">-- Select Campaign --</option>
                            @foreach($campaigns as $c)
                                <option value="{{ $c->id }}">{{ $c->title }} ({{ $c->job_code }})</option>
                            @endforeach
                        </select>
                    </x-input>

                    <x-input label="Select User Account *" :error="$errors->first('selected_user_id')">
                        <select class="form-control form-select" wire:model="selected_user_id">
                            <option value="">-- Select Member --</option>
                            @foreach($committee_users as $u)
                                <option value="{{ $u->id }}">{{ $u->email }} (Role: {{ str_replace('-', ' ', $u->roles->first()?->name) }})</option>
                            @endforeach
                        </select>
                    </x-input>

                    <x-input label="Committee Type Panel *" :error="$errors->first('is_appellate_panel')">
                        <select class="form-control form-select" wire:model="is_appellate_panel">
                            <option value="0">Scrutiny / Selection Committee</option>
                            <option value="1">Appellate Panel (Grievance Review)</option>
                        </select>
                    </x-input>
                </div>

                <x-button variant="primary" type="submit" style="margin-top: 1rem;">
                    👥 Establish Assignment
                </x-button>
            </form>
        </x-card>

        <!-- Assignments Table -->
        <x-card title="Active Committee Allocations Ledger" style="border-color: var(--border-color);">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Job Campaign</th>
                            <th>Committee Member</th>
                            <th>Role</th>
                            <th>Assigned Panel Type</th>
                            <th>Assigned Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $asm)
                            <tr>
                                <td><strong>{{ $asm->campaign->title }}</strong> <br><small style="color: var(--color-text-secondary);">Code: {{ $asm->campaign->job_code }}</small></td>
                                <td><strong>{{ $asm->user->email }}</strong></td>
                                <td>
                                    <x-badge type="appealed">
                                        {{ str_replace('-', ' ', $asm->user->roles->first()?->name ?? 'No Role') }}
                                    </x-badge>
                                </td>
                                <td>
                                    @if($asm->is_appellate_panel)
                                        <x-badge type="ineligible" style="background: rgba(245, 158, 11, 0.2); color: var(--warning);">Appellate Panel</x-badge>
                                    @else
                                        <x-badge type="paid">Scrutiny Panel</x-badge>
                                    @endif
                                </td>
                                <td><span style="font-size: 0.85rem; color: var(--color-text-secondary);">{{ $asm->assigned_at?->format('Y-m-d H:i') ?: 'N/A' }}</span></td>
                                <td>
                                    <x-button variant="danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" 
                                            wire:click="removeCommitteeAssignment({{ $asm->id }})" 
                                            onclick="confirm('Are you sure you want to de-assign this member?') || event.stopImmediatePropagation()">
                                        De-assign Member
                                    </x-button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">No active committee panel allocations found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    @endif
</div>
