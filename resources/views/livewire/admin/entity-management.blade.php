<div>
    <!-- Console Control Header -->
    <x-card title="⚙️ System Entity Console" subtitle="Unified BREAD desk for administrative control, roles registry, candidate applications, vouchers, and audit logs.">
        <div style="position: absolute; top: 1.5rem; right: 1.5rem;" class="flex gap-2">
            @if(!$isFormOpen && !$isDetailsOpen)
                @if($activeTab === 'users' && auth()->user()->can('create-user'))
                    <x-button variant="primary" wire:click="openCreateUserForm">➕ Register User</x-button>
                @elseif($activeTab === 'roles' && auth()->user()->can('create-role'))
                    <x-button variant="primary" wire:click="openCreateRoleForm">➕ Add Role</x-button>
                @elseif($activeTab === 'permissions' && auth()->user()->can('create-permission'))
                    <x-button variant="primary" wire:click="openCreatePermissionForm">➕ Add Permission</x-button>
                @elseif($activeTab === 'campaigns' && auth()->user()->can('create-campaign'))
                    <x-button variant="primary" wire:click="openCreateCampaignForm">➕ Create Campaign</x-button>
                @elseif($activeTab === 'applications' && auth()->user()->can('create-application'))
                    <x-button variant="primary" wire:click="openCreateApplicationForm">➕ Add Application</x-button>
                @elseif($activeTab === 'basic_infos' && auth()->user()->can('create-basic-info'))
                    <x-button variant="primary" wire:click="openCreateBasicInfoForm">➕ Create basicInfo</x-button>
                @elseif($activeTab === 'payments' && auth()->user()->can('create-payment'))
                    <x-button variant="primary" wire:click="openCreatePaymentForm">➕ Log Payment</x-button>
                @elseif($activeTab === 'logs' && auth()->user()->can('delete-audit-log') && count($logs ?? []) > 0)
                    <x-button variant="danger" wire:click="clearAuditLogs" onclick="confirm('Are you sure you want to permanently purge all security logs?') || event.stopImmediatePropagation()">🗑️ Purge Logs</x-button>
                @endif
            @endif
        </div>
    </x-card>

    <!-- Glassmorphic Navigation Tabs based on Granular Permissions -->
    <div class="flex gap-2 mb-6" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-top: 1.5rem; overflow-x: auto; white-space: nowrap;">
        @can('view-user')
            <x-button :variant="$activeTab === 'users' ? 'primary' : 'secondary'" wire:click="setTab('users')">👥 Users</x-button>
        @endcan
        @can('view-role')
            <x-button :variant="$activeTab === 'roles' ? 'primary' : 'secondary'" wire:click="setTab('roles')">🛡️ Roles</x-button>
        @endcan
        @can('view-permission')
            <x-button :variant="$activeTab === 'permissions' ? 'primary' : 'secondary'" wire:click="setTab('permissions')">🔑 Perms</x-button>
        @endcan
        @can('view-campaign')
            <x-button :variant="$activeTab === 'campaigns' ? 'primary' : 'secondary'" wire:click="setTab('campaigns')">📢 Campaigns</x-button>
        @endcan
        @can('view-application')
            <x-button :variant="$activeTab === 'applications' ? 'primary' : 'secondary'" wire:click="setTab('applications')">📋 Applications</x-button>
        @endcan
        @can('view-basic-info')
            <x-button :variant="$activeTab === 'basic_infos' ? 'primary' : 'secondary'" wire:click="setTab('basic_infos')">👤 basic_infos</x-button>
        @endcan
        @can('view-payment')
            <x-button :variant="$activeTab === 'payments' ? 'primary' : 'secondary'" wire:click="setTab('payments')">💳 Vouchers</x-button>
        @endcan
        @can('view-audit-log')
            <x-button :variant="$activeTab === 'logs' ? 'primary' : 'secondary'" wire:click="setTab('logs')">🔍 Audit Logs</x-button>
        @endcan
    </div>

    <!-- ========================================== -->
    <!-- TAB 1: USERS -->
    <!-- ========================================== -->
    @if ($activeTab === 'users')
        @if ($isFormOpen)
            <x-card :title="$isEditMode ? '✍️ Edit User Account' : '➕ Register User Account'">
                <form wire:submit.prevent="saveUser">
                    <div class="grid grid-cols-2 gap-6">
                        <x-input label="Email Address *" :error="$errors->first('email')">
                            <input type="email" class="form-control" wire:model="email" placeholder="email@university.edu">
                        </x-input>
                        <x-input label="Password {{ $isEditMode ? '(Leave blank to keep current)' : '*' }}" :error="$errors->first('password')">
                            <input type="password" class="form-control" wire:model="password" placeholder="••••••••">
                        </x-input>
                    </div>
                    <div class="grid grid-cols-2 gap-6 mt-4">
                        <x-select label="Assign System Role *" :error="$errors->first('role_name')">
                            <select class="form-control form-select" wire:model="role_name">
                                @foreach($roles as $r)
                                    <option value="{{ $r->name }}">{{ str_replace('-', ' ', $r->name) }}</option>
                                @endforeach
                            </select>
                        </x-select>
                        <div class="form-group" style="margin-top: 1.75rem;">
                            <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                                <input type="checkbox" wire:model="is_active" style="width: 1.3rem; height: 1.3rem;">
                                <span style="font-weight: 600;">Allow User Login (Active Status)</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex gap-4 mt-6" style="border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                        <x-button variant="primary" type="submit">💾 Save User</x-button>
                        <x-button variant="secondary" wire:click="$set('isFormOpen', false)">Cancel</x-button>
                    </div>
                </form>
            </x-card>
        @else
            <x-table :headers="['Email Address Reference', 'Mapped Role', 'Active Status', 'Date Created', 'Actions']">
                @forelse($users as $u)
                    <tr>
                        <td><strong style="color: var(--color-text-primary)">{{ $u->email }}</strong></td>
                        <td><x-badge type="appealed">{{ str_replace('-', ' ', $u->roles->first()?->name ?? 'None') }}</x-badge></td>
                        <td>
                            <x-button :variant="$u->is_active ? 'success' : 'danger'" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="toggleUserStatus({{ $u->id }})" :disabled="auth()->id() == $u->id">
                                {{ $u->is_active ? '✓ Active' : '✗ Blocked' }}
                            </x-button>
                        </td>
                        <td>{{ $u->created_at?->format('Y-m-d H:i') ?: 'System default' }}</td>
                        <td>
                            <div class="flex gap-2">
                                <x-button variant="secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="openEditUserForm({{ $u->id }})">Edit</x-button>
                                <x-button variant="danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="deleteUser({{ $u->id }})" onclick="confirm('Are you sure you want to delete this user?') || event.stopImmediatePropagation()" :disabled="auth()->id() == $u->id">Delete</x-button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">No user accounts found.</td></tr>
                @endforelse
            </x-table>
        @endif

    <!-- ========================================== -->
    <!-- TAB 2: ROLES -->
    <!-- ========================================== -->
    @elseif ($activeTab === 'roles')
        @if ($isFormOpen)
            <x-card :title="$isEditMode ? '🛡️ Sync Role Permissions' : '🛡️ Register New Role'">
                <form wire:submit.prevent="saveRole">
                    <x-input label="Role Name Reference *" :error="$errors->first('role_name_input')">
                        <input type="text" class="form-control" wire:model="role_name_input" placeholder="e.g. support-technician">
                    </x-input>
                    
                    <div style="border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1.5rem; background: var(--bg-secondary);" class="mt-4 mb-4">
                        <h4 style="color: var(--secondary); margin-bottom: 1rem;">🔑 Mapped Permissions Grid</h4>
                        <div class="grid grid-cols-3 gap-4" style="max-height: 250px; overflow-y: auto;">
                            @foreach($permissions as $perm)
                                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 4px;">
                                    <input type="checkbox" wire:model="selected_permissions" value="{{ $perm->name }}" style="width: 1.15rem; height: 1.15rem;">
                                    <span style="font-size: 0.85rem;">{{ str_replace('-', ' ', $perm->name) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex gap-4 mt-6" style="border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                        <x-button variant="primary" type="submit">💾 Save Role</x-button>
                        <x-button variant="secondary" wire:click="$set('isFormOpen', false)">Cancel</x-button>
                    </div>
                </form>
            </x-card>
        @else
            <x-table :headers="['Role Name Reference', 'Sycned Permissions Count', 'Active Accounts Scope', 'Action']">
                @foreach($roles as $role)
                    <tr>
                        <td><strong style="color: var(--secondary); text-transform: uppercase;">{{ str_replace('-', ' ', $role->name) }}</strong></td>
                        <td><x-badge type="appealed">{{ $role->name === 'super-admin' ? 'All (Wildcard *)' : $role->permissions->count() . ' perms' }}</x-badge></td>
                        <td>{{ $role->users->count() }} Accounts</td>
                        <td>
                            <div class="flex gap-2">
                                <x-button variant="secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="openEditRoleForm({{ $role->id }})">Edit & Sync</x-button>
                                @if(!in_array($role->name, ['super-admin', 'registrar', 'applicant']))
                                    <x-button variant="danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="deleteRole({{ $role->id }})" onclick="confirm('Are you sure you want to delete this role?') || event.stopImmediatePropagation()">Delete</x-button>
                                @else
                                    <span style="color: var(--color-text-muted); font-size: 0.8rem;">System Locked</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-table>
        @endif

    <!-- ========================================== -->
    <!-- TAB 3: PERMISSIONS -->
    <!-- ========================================== -->
    @elseif ($activeTab === 'permissions')
        @if ($isFormOpen)
            <x-card :title="$isEditMode ? '🔑 Edit System Permission' : '🔑 Register Permission Action'">
                <form wire:submit.prevent="savePermission">
                    <x-input label="Permission Name Slug *" :error="$errors->first('permission_name_input')">
                        <input type="text" class="form-control" wire:model="permission_name_input" placeholder="e.g. view-reports">
                    </x-input>
                    <div class="flex gap-4 mt-6" style="border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                        <x-button variant="primary" type="submit">💾 Save Permission</x-button>
                        <x-button variant="secondary" wire:click="$set('isFormOpen', false)">Cancel</x-button>
                    </div>
                </form>
            </x-card>
        @else
            <x-table :headers="['Permission Slug Name', 'Guard Engine Reference', 'Actions']">
                @forelse($permissions as $perm)
                    <tr>
                        <td><code>{{ $perm->name }}</code></td>
                        <td><code>{{ $perm->guard_name }}</code></td>
                        <td>
                            <div class="flex gap-2">
                                <x-button variant="secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="openEditPermissionForm({{ $perm->id }})">Edit</x-button>
                                <x-button variant="danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="deletePermission({{ $perm->id }})" onclick="confirm('Are you sure you want to delete this permission? This will detach it from all roles.') || event.stopImmediatePropagation()">Delete</x-button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center">No system permissions registered.</td></tr>
                @endforelse
            </x-table>
        @endif

    <!-- ========================================== -->
    <!-- TAB 4: CAMPAIGNS -->
    <!-- ========================================== -->
    @elseif ($activeTab === 'campaigns')
        @if ($isFormOpen)
            <x-card :title="$isEditMode ? '📢 Modify Recruitment Campaign' : '📢 Create Recruitment Campaign'">
                <form wire:submit.prevent="saveCampaign">
                    <div class="grid grid-cols-2 gap-4">
                        <x-input label="Job Code *" :error="$errors->first('job_code')">
                            <input type="text" class="form-control" wire:model="job_code" placeholder="CS-FAC-2026">
                        </x-input>
                        <x-input label="Job Position Title *" :error="$errors->first('title')">
                            <input type="text" class="form-control" wire:model="title" placeholder="Assistant Professor">
                        </x-input>
                    </div>
                    <div class="grid grid-cols-3 gap-4 mt-4">
                        <x-select label="Job Track Type *" :error="$errors->first('track_type')">
                            <select class="form-control form-select" wire:model.live="track_type">
                                <option value="ACADEMIC">Academic Faculty Track</option>
                                <option value="ADMINISTRATIVE">Administrative Officer Track</option>
                                <option value="SUPPORT">Support Staff Track</option>
                            </select>
                        </x-select>
                        <x-input label="Department Scope *" :error="$errors->first('department')">
                            <input type="text" class="form-control" wire:model="department" placeholder="Department of IT">
                        </x-input>
                        <x-input label="Processing Fee (PKR) *" :error="$errors->first('application_fee')">
                            <input type="number" class="form-control" wire:model="application_fee">
                        </x-input>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <x-input label="Opening Date *" :error="$errors->first('opening_date')">
                            <input type="datetime-local" class="form-control" wire:model="opening_date">
                        </x-input>
                        <x-input label="Closing Date *" :error="$errors->first('closing_date')">
                            <input type="datetime-local" class="form-control" wire:model="closing_date">
                        </x-input>
                    </div>

                    <div style="border: 1px solid var(--border-color); border-radius: 8px; padding: 1rem 1.25rem; background: var(--bg-secondary); margin-top: 1rem;" class="flex justify-between align-center">
                        <div>
                            <strong style="color: var(--color-text-primary); font-size: 0.95rem; display: block;">📜 Academic Transcript Proof Requirement</strong>
                            <small style="color: var(--color-text-secondary);">Choose whether applicants must upload degree transcripts/certificates or if uploads are optional.</small>
                        </div>
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-weight: 700; color: var(--color-text-primary);">
                            <input type="checkbox" wire:model="campaign_require_transcript_proof" style="width: 1.25rem; height: 1.25rem;">
                            <span>{{ $campaign_require_transcript_proof ? 'Mandatory (Required *)' : 'Optional' }}</span>
                        </label>
                    </div>

                    @if($track_type === 'ACADEMIC')
                        <div style="border: 1px solid var(--secondary-glow); border-radius: 8px; padding: 1.5rem; background: rgba(6,182,212,0.02);" class="mt-6 mb-6">
                            <h4 style="color: var(--secondary); font-size: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;" class="mb-4">⚡ Academic Scoring Weights</h4>
                            <div class="grid grid-cols-3 gap-4">
                                <x-input label="Gold Medal Weight"><input type="number" step="0.1" class="form-control" wire:model="gold_medal_weight"></x-input>
                                <x-input label="Silver Medal Weight"><input type="number" step="0.1" class="form-control" wire:model="silver_medal_weight"></x-input>
                                <x-input label="Bronze Medal Weight"><input type="number" step="0.1" class="form-control" wire:model="bronze_medal_weight"></x-input>
                            </div>
                            <div class="grid grid-cols-4 gap-4 mt-4">
                                <x-input label="HJRS W weight"><input type="number" step="0.05" class="form-control" wire:model="w_category_weight"></x-input>
                                <x-input label="HJRS X weight"><input type="number" step="0.05" class="form-control" wire:model="x_category_weight"></x-input>
                                <x-input label="HJRS Y weight"><input type="number" step="0.05" class="form-control" wire:model="y_category_weight"></x-input>
                                <x-input label="HJRS Z weight"><input type="number" step="0.05" class="form-control" wire:model="z_category_weight"></x-input>
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
            <!-- Campaign List & Committee Assignment Panel -->
            <div class="grid grid-cols-3 gap-6">
                <div class="card" style="grid-column: span 2;">
                    <h3 class="card-title mb-4">Job Openings & Campaigns</h3>
                    <x-table :headers="['Code', 'Position / Dept', 'Track Class', 'Voucher Fee', 'Dates Range', 'Actions']">
                        @foreach($campaigns as $camp)
                            <tr>
                                <td><code style="color: var(--secondary)">{{ $camp->job_code }}</code></td>
                                <td><strong>{{ $camp->title }}</strong><br><small>{{ $camp->department }}</small></td>
                                <td><x-badge type="appealed">{{ $camp->track_type }}</x-badge></td>
                                <td>PKR {{ number_format($camp->application_fee, 2) }}</td>
                                <td>
                                    <small>Open: {{ $camp->opening_date?->format('Y-m-d') }}</small><br>
                                    <small style="color: var(--color-text-secondary)">Close: {{ $camp->closing_date?->format('Y-m-d') }}</small>
                                </td>
                                <td>
                                    <div class="flex gap-2">
                                        <x-button variant="secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="openEditCampaignForm({{ $camp->id }})">Edit</x-button>
                                        <x-button variant="danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="deleteCampaign({{ $camp->id }})" onclick="confirm('Are you sure you want to delete this campaign?') || event.stopImmediatePropagation()">Delete</x-button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </x-table>
                </div>
                
                <!-- Committee Assignments sub-panel -->
                <div class="card flex-col gap-4">
                    <h3 class="card-title">👥 Allocate Committee Members</h3>
                    <form wire:submit.prevent="assignCommitteeMember">
                        <x-select label="Select Job Campaign *" :error="$errors->first('selected_campaign_id')">
                            <select class="form-control form-select" wire:model="selected_campaign_id">
                                <option value="">-- Choose Position --</option>
                                @foreach($campaigns as $camp)
                                    <option value="{{ $camp->id }}">{{ $camp->job_code }} - {{ $camp->title }}</option>
                                @endforeach
                            </select>
                        </x-select>

                        <x-select label="Select Committee Member *" :error="$errors->first('selected_user_id')">
                            <select class="form-control form-select" wire:model="selected_user_id">
                                <option value="">-- Select Member --</option>
                                @foreach($committee_users as $cu)
                                    <option value="{{ $cu->id }}">{{ $cu->email }} (Role: {{ str_replace('-', ' ', $cu->roles->first()?->name) }})</option>
                                @endforeach
                            </select>
                        </x-select>

                        <x-select label="Committee Type *" :error="$errors->first('is_appellate_panel')">
                            <select class="form-control form-select" wire:model="is_appellate_panel">
                                <option value="0">Document Scrutiny Panel</option>
                                <option value="1">Appellate Grievance Panel</option>
                            </select>
                        </x-select>

                        <div class="mt-4">
                            <x-button variant="primary" style="width: 100%;" type="submit">👥 Allocate Member</x-button>
                        </div>
                    </form>

                    <h4 class="mt-4 border-bottom pb-2">Active Panel Allocations</h4>
                    <div style="max-height: 200px; overflow-y: auto;">
                        @forelse($committee_assignments as $asg)
                            <div style="background: var(--bg-table-hover); border: 1px solid var(--border-color); border-radius: 6px; padding: 0.75rem; margin-bottom: 0.75rem;" class="flex justify-between align-center">
                                <div>
                                    <code style="color: var(--secondary)">{{ $asg->campaign->job_code }}</code><br>
                                    <small>{{ $asg->user->email }}</small><br>
                                    <x-badge :type="$asg->is_appellate_panel ? 'appealed' : 'paid'">
                                        {{ $asg->is_appellate_panel ? 'Appellate Panel' : 'Scrutiny Panel' }}
                                    </x-badge>
                                </div>
                                <x-button variant="danger" style="padding: 0.2rem 0.4rem; font-size: 0.75rem;" wire:click="removeCommitteeAssignment({{ $asg->id }})">×</x-button>
                            </div>
                        @empty
                            <p style="color: var(--color-text-muted); font-size: 0.85rem;" class="text-center">No panel allocations recorded.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

    <!-- ========================================== -->
    <!-- TAB 5: APPLICATIONS -->
    <!-- ========================================== -->
    @elseif ($activeTab === 'applications')
        @if ($isFormOpen)
            <x-card :title="$isEditMode ? '✍️ Manage Application Status' : '➕ Register Application record'">
                <form wire:submit.prevent="saveApplication">
                    @if(!$isEditMode)
                        <div class="grid grid-cols-2 gap-4">
                            <x-select label="Select Applicant Account *" :error="$errors->first('app_user_id')">
                                <select class="form-control form-select" wire:model="app_user_id">
                                    <option value="">-- Choose Account --</option>
                                    @foreach($users as $au)
                                        <option value="{{ $au->id }}">{{ $au->email }}</option>
                                    @endforeach
                                </select>
                            </x-select>
                            <x-select label="Select Recruitment Campaign *" :error="$errors->first('app_campaign_id')">
                                <select class="form-control form-select" wire:model="app_campaign_id">
                                    <option value="">-- Choose Campaign --</option>
                                    @foreach($campaigns as $ac)
                                        <option value="{{ $ac->id }}">{{ $ac->job_code }} - {{ $ac->title }}</option>
                                    @endforeach
                                </select>
                            </x-select>
                        </div>
                    @endif
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <x-select label="Application Processing Status *" :error="$errors->first('app_status')">
                            <select class="form-control form-select" wire:model="app_status">
                                <option value="DRAFT">Draft</option>
                                <option value="SUBMITTED">Submitted</option>
                                <option value="PAID">Paid / In Scrutiny</option>
                                <option value="ELIGIBLE">Eligible</option>
                                <option value="INELIGIBLE">Ineligible</option>
                                <option value="APPEALED">Appealed Grievance</option>
                                <option value="UNDER_REVIEW">Under Appellate Review</option>
                                <option value="APPROVED">Approved for Evaluation</option>
                                <option value="REJECTED">Appellate Rejected</option>
                            </select>
                        </x-select>
                        <x-input label="Disqualification Reason (If ineligible)" :error="$errors->first('app_disqualification_reason')">
                            <input type="text" class="form-control" wire:model="app_disqualification_reason" placeholder="e.g. Over-age / Incomplete transcript proof">
                        </x-input>
                    </div>
                    <div class="flex gap-4 mt-6" style="border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                        <x-button variant="primary" type="submit">💾 Save Application</x-button>
                        <x-button variant="secondary" wire:click="$set('isFormOpen', false)">Cancel</x-button>
                    </div>
                </form>
            </x-card>
        @else
            <x-table :headers="['Candidate details', 'Campaign Code', 'Voucher status', 'Processing stage', 'Submitted Timestamp', 'Actions']">
                @forelse($applications as $app)
                    <tr>
                        <td>
                            <strong style="color: var(--color-text-primary)">{{ $app->user->basicInfo?->first_name ?? 'N/A' }} {{ $app->user->basicInfo?->last_name ?? 'N/A' }}</strong><br>
                            <small>{{ $app->user->email }}</small>
                        </td>
                        <td><code>{{ $app->campaign->job_code }}</code></td>
                        <td>
                            <x-badge :type="$app->billingLedger?->status === 'SETTLED' ? 'paid' : 'pending'">
                                {{ $app->billingLedger?->status ?: 'No Voucher' }}
                            </x-badge>
                        </td>
                        <td><x-badge :type="strtolower($app->status)">{{ $app->status }}</x-badge></td>
                        <td>{{ $app->submission_timestamp ?: 'In draft' }}</td>
                        <td>
                            <div class="flex gap-2">
                                <x-button variant="secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="viewApplicationDetails({{ $app->id }})">Details</x-button>
                                <x-button variant="secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="openEditApplicationForm({{ $app->id }})">Status</x-button>
                                <x-button variant="danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="deleteApplication({{ $app->id }})" onclick="confirm('Are you sure you want to delete this application record?') || event.stopImmediatePropagation()">Delete</x-button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">No recruitment applications logged.</td></tr>
                @endforelse
            </x-table>
        @endif

    <!-- ========================================== -->
    <!-- TAB 6: basic_infos -->
    <!-- ========================================== -->
    @elseif ($activeTab === 'basic_infos')
        @if ($isFormOpen)
            <x-card :title="$isEditMode ? '✍️ Modify basicInfo details' : '➕ Create Basic basicInfo Record'">
                <form wire:submit.prevent="saveBasicInfo">
                    @if(!$isEditMode)
                        <x-select label="Select Applicant Account *" :error="$errors->first('bi_user_id')">
                            <select class="form-control form-select" wire:model="bi_user_id">
                                <option value="">-- Choose Account --</option>
                                @foreach($users as $pu)
                                    <option value="{{ $pu->id }}">{{ $pu->email }}</option>
                                @endforeach
                            </select>
                        </x-select>
                    @endif
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <x-input label="First Name *" :error="$errors->first('bi_first_name')">
                            <input type="text" class="form-control" wire:model="bi_first_name">
                        </x-input>
                        <x-input label="Last Name *" :error="$errors->first('bi_last_name')">
                            <input type="text" class="form-control" wire:model="bi_last_name">
                        </x-input>
                    </div>
                    <div class="grid grid-cols-3 gap-4 mt-4 align-center">
                        <x-input label="CNIC Reference *" :error="$errors->first('bi_cnic_number')">
                            <input type="text" class="form-control" wire:model="bi_cnic_number" placeholder="xxxxx-xxxxxxx-x">
                        </x-input>
                        <x-input label="Date of Birth *" :error="$errors->first('bi_dob')">
                            <input type="date" class="form-control" wire:model="bi_dob">
                        </x-input>
                        <div class="form-group" style="margin-top: 1.75rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" wire:model="bi_is_locked" style="width: 1.25rem; height: 1.25rem;">
                                <span style="font-weight: 600;">Lock basicInfo (Voucher generated)</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex gap-4 mt-6" style="border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                        <x-button variant="primary" type="submit">💾 Save basicInfo</x-button>
                        <x-button variant="secondary" wire:click="$set('isFormOpen', false)">Cancel</x-button>
                    </div>
                </form>
            </x-card>
        @else
            <x-table :headers="['Candidate Reference', 'Track Type', 'CNIC Number Reference', 'Lock Status', 'Action']">
                @forelse($basic_infos as $basicInfo)
                    <tr>
                        <td>
                            <strong style="color: var(--color-text-primary)">{{ $basicInfo->first_name }} {{ $basicInfo->last_name }}</strong><br>
                            <small>{{ $basicInfo->user->email }}</small>
                        </td>
                        <td><x-badge type="appealed">{{ $basicInfo->track_type }}</x-badge></td>
                        <td><code>{{ $basicInfo->cnic_number }}</code></td>
                        <td>
                            <x-badge :type="$basicInfo->is_locked ? 'ineligible' : 'paid'">
                                {{ $basicInfo->is_locked ? '🔒 Locked' : '🔓 Editing' }}
                            </x-badge>
                        </td>
                        <td>
                            <div class="flex gap-2">
                                <x-button variant="secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="viewBasicInfoDetails({{ $basicInfo->id }})">Full basicInfo Details</x-button>
                                <x-button variant="secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="openEditBasicInfoForm({{ $basicInfo->id }})">Basic Details</x-button>
                                <x-button variant="danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="deleteBasicInfo({{ $basicInfo->id }})" onclick="confirm('Are you sure you want to delete this basicInfo?') || event.stopImmediatePropagation()">Delete</x-button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">No candidate basic_infos registered in system.</td></tr>
                @endforelse
            </x-table>
        @endif

    <!-- ========================================== -->
    <!-- TAB 7: PAYMENTS -->
    <!-- ========================================== -->
    @elseif ($activeTab === 'payments')
        @if ($isFormOpen)
            <x-card :title="$isEditMode ? '✍️ Edit Processing Voucher details' : '➕ Log Processing Voucher'">
                <form wire:submit.prevent="savePayment">
                    @if(!$isEditMode)
                        <div class="grid grid-cols-2 gap-4">
                            <x-select label="Select Active Application *" :error="$errors->first('pay_app_id')">
                                <select class="form-control form-select" wire:model="pay_app_id">
                                    <option value="">-- Choose Application --</option>
                                    @foreach($applications as $app)
                                        <option value="{{ $app->id }}">{{ $app->user->email }} (Job Code: {{ $app->campaign->job_code }})</option>
                                    @endforeach
                                </select>
                            </x-select>
                            <x-input label="12-digit Consumer ID Reference *" :error="$errors->first('pay_consumer_id')">
                                <input type="text" class="form-control" wire:model="pay_consumer_id" placeholder="e.g. 100012345678">
                            </x-input>
                        </div>
                    @endif
                    <div class="grid grid-cols-3 gap-4 mt-4 align-center">
                        <x-input label="Total Amount (PKR) *" :error="$errors->first('pay_amount')">
                            <input type="number" class="form-control" wire:model="pay_amount">
                        </x-input>
                        <x-select label="Voucher status *" :error="$errors->first('pay_status')">
                            <select class="form-control form-select" wire:model="pay_status">
                                <option value="PENDING">Pending Payment</option>
                                <option value="AUTHORIZED">Authorized</option>
                                <option value="SETTLED">Settled / PAID</option>
                                <option value="EXPIRED">Expired</option>
                            </select>
                        </x-select>
                        <x-input label="Bank Transaction reference ID" :error="$errors->first('pay_transaction_id')">
                            <input type="text" class="form-control" wire:model="pay_transaction_id" placeholder="TID-123456">
                        </x-input>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <x-input label="Reconciled Timestamp" :error="$errors->first('pay_timestamp')">
                            <input type="datetime-local" class="form-control" wire:model="pay_timestamp">
                        </x-input>
                    </div>
                    <div class="flex gap-4 mt-6" style="border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                        <x-button variant="primary" type="submit">💾 Save Payment</x-button>
                        <x-button variant="secondary" wire:click="$set('isFormOpen', false)">Cancel</x-button>
                    </div>
                </form>
            </x-card>
        @else
            <x-table :headers="['Consumer ID', 'Applicant Account', 'Campaign Scope', 'Amount PKR', 'Voucher status', 'Transaction ID', 'Paid Date', 'Action']">
                @forelse($payments as $pay)
                    <tr>
                        <td><code style="color: var(--secondary)">{{ $pay->consumer_id }}</code></td>
                        <td>{{ $pay->application->user->email }}</td>
                        <td><code>{{ $pay->application->campaign->job_code }}</code></td>
                        <td><strong>PKR {{ number_format($pay->amount, 2) }}</strong></td>
                        <td><x-badge :type="strtolower($pay->status)">{{ $pay->status }}</x-badge></td>
                        <td><code>{{ $pay->transaction_id ?: 'N/A' }}</code></td>
                        <td>{{ $pay->payment_timestamp ?: 'Unpaid' }}</td>
                        <td>
                            <div class="flex gap-2">
                                <x-button variant="secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="openEditPaymentForm({{ $pay->id }})">Reconcile / Edit</x-button>
                                <x-button variant="danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="deletePayment({{ $pay->id }})" onclick="confirm('Are you sure you want to delete this payment record?') || event.stopImmediatePropagation()">Delete</x-button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center">No processing billing vouchers logged.</td></tr>
                @endforelse
            </x-table>
        @endif

    <!-- ========================================== -->
    <!-- TAB 8: AUDIT LOGS -->
    <!-- ========================================== -->
    @elseif ($activeTab === 'logs')
        <x-card title="📜 Security Audit Log Ledger">
            <x-table :headers="['Action User', 'Access IP', 'Action Mode', 'Target Database Table', 'Record reference ID', 'Timestamp Logged', 'Action']">
                @forelse($logs as $log)
                    <tr>
                        <td><strong>{{ $log->user?->email ?: 'Anonymous / Guest' }}</strong></td>
                        <td><code>{{ $log->ip_address }}</code></td>
                        <td>
                            <x-badge :type="$log->action_type === 'DELETED' ? 'ineligible' : ($log->action_type === 'CREATED' ? 'eligible' : 'appealed')">
                                {{ $log->action_type }}
                            </x-badge>
                        </td>
                        <td><code>{{ $log->table_name }}</code></td>
                        <td><code>ID #{{ $log->record_id }}</code></td>
                        <td><small style="color: var(--color-text-secondary)">{{ $log->logged_at }}</small></td>
                        <td>
                            <x-button variant="secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="viewLogDetails({{ $log->id }})">Details Diff</x-button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">No security audit records logged.</td></tr>
                @endforelse
            </x-table>
        </x-card>
    @endif

    <!-- ========================================== -->
    <!-- MODAL POPUP DRAWERS FOR DETAILED DETAILS -->
    <!-- ========================================== -->
    
    <!-- 1. Application Details Modal -->
    <x-modal :show="$isDetailsOpen && $activeTab === 'applications'" title="📋 Application basicInfo Details Summary" closeAction="$set('isDetailsOpen', false)" style="max-width: 800px;">
        @if($selectedRecordDetails)
            <div style="max-height: 450px; overflow-y: auto; padding-right: 0.5rem;">
                <div class="grid grid-cols-2 gap-4 border-bottom pb-4 mb-4">
                    <div>
                        <p style="color: var(--color-text-secondary)">Candidate Full Name:</p>
                        <strong>{{ $selectedRecordDetails->user->basicInfo?->first_name }} {{ $selectedRecordDetails->user->basicInfo?->last_name }}</strong>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary)">Email Reference:</p>
                        <code>{{ $selectedRecordDetails->user->email }}</code>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4 border-bottom pb-4 mb-4">
                    <div>
                        <p style="color: var(--color-text-secondary)">Job Position:</p>
                        <strong>{{ $selectedRecordDetails->campaign->title }}</strong>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary)">Job Code:</p>
                        <code style="color: var(--secondary)">{{ $selectedRecordDetails->campaign->job_code }}</code>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary)">Track Classification:</p>
                        <x-badge type="appealed">{{ $selectedRecordDetails->campaign->track_type }}</x-badge>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4 border-bottom pb-4 mb-4">
                    <div>
                        <p style="color: var(--color-text-secondary)">Challan Voucher status:</p>
                        <x-badge :type="$selectedRecordDetails->billingLedger?->status === 'SETTLED' ? 'paid' : 'pending'">
                            {{ $selectedRecordDetails->billingLedger?->status ?: 'No Voucher' }}
                        </x-badge>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary)">Consumer Reference ID:</p>
                        <code>{{ $selectedRecordDetails->billingLedger?->consumer_id ?: 'N/A' }}</code>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary)">Voucher Amount Paid:</p>
                        <strong style="color: var(--success)">PKR {{ number_format($selectedRecordDetails->billingLedger?->amount ?? 0, 2) }}</strong>
                    </div>
                </div>

                @if(!empty($selectedRecordDetails->evaluations))
                    <h4 class="mb-2 mt-4" style="color: var(--secondary)">Board Members Grading Results</h4>
                    <x-table :headers="['Board Member Account', 'Academic', 'Medals', 'Publications', 'Events', 'Projects', 'Interview', 'Outlier?']">
                        @forelse($selectedRecordDetails->evaluations as $eval)
                            <tr>
                                <td>{{ $eval->boardMember->email }}</td>
                                <td>{{ $eval->academic_score }}</td>
                                <td>{{ $eval->medals_score }}</td>
                                <td>{{ $eval->publications_score }}</td>
                                <td>{{ $eval->events_score }}</td>
                                <td>{{ $eval->project_score }}</td>
                                <td><strong style="color: var(--success)">{{ $eval->interview_score }}</strong></td>
                                <td>
                                    @if($eval->is_outlier)
                                        <x-badge type="ineligible">Outlier Alert</x-badge><br>
                                        <small style="color: var(--color-text-muted)">Justify: {{ $eval->outlier_justification }}</small>
                                    @else
                                        <x-badge type="paid">Normal</x-badge>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center">No board grades submitted yet.</td></tr>
                        @endforelse
                    </x-table>
                @endif
            </div>
        @endif
    </x-modal>

    <!-- 2. basicInfo Details Modal -->
    <x-modal :show="$isDetailsOpen && $activeTab === 'basic_infos'" title="👤 Complete Candidate Wizard basicInfo details" closeAction="$set('isDetailsOpen', false)" style="max-width: 900px; width: 95%;">
        @if($selectedRecordDetails)
            <div style="max-height: 550px; overflow-y: auto; padding-right: 0.5rem;">
                <div class="grid grid-cols-3 gap-4 border-bottom pb-4 mb-4">
                    <div>
                        <p style="color: var(--color-text-secondary)">First & Last Name:</p>
                        <strong>{{ $selectedRecordDetails->first_name }} {{ $selectedRecordDetails->last_name }}</strong>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary)">CNIC Reference:</p>
                        <code>{{ $selectedRecordDetails->cnic_number }}</code>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary)">Date of Birth:</p>
                        <strong>{{ $selectedRecordDetails->date_of_birth?->format('Y-m-d') }}</strong>
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-4 border-bottom pb-4 mb-4">
                    <div>
                        <p style="color: var(--color-text-secondary)">Gender:</p>
                        <strong>{{ $selectedRecordDetails->gender ?? 'N/A' }}</strong>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary)">Father's Name:</p>
                        <strong>{{ $selectedRecordDetails->father_name ?? 'N/A' }}</strong>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary)">Contact Phone:</p>
                        <strong>{{ $selectedRecordDetails->phone_number ?? 'N/A' }}</strong>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary)">Domicile Scope:</p>
                        <strong>{{ $selectedRecordDetails->domicile ?? 'N/A' }}</strong>
                    </div>
                </div>

                <!-- Academic History (Ascending sorted) -->
                @if($selectedRecordDetails->degrees->isNotEmpty())
                    <h4 style="color: var(--secondary); font-weight: 700;" class="mt-4 mb-2">🎓 Chronological Academic Qualifications</h4>
                    <x-table :headers="['Degree', 'Major Specialization', 'Institution / Board', 'Passing Year', 'Marks Obtd / Scale', 'Division/Grade', 'Transcript Proof']">
                        @foreach($selectedRecordDetails->degrees as $deg)
                            <tr>
                                <td><strong>{{ $deg['degree_title'] }}</strong></td>
                                <td>{{ $deg['major'] }}</td>
                                <td>{{ $deg['institution'] }} ({{ $deg['board'] }})</td>
                                <td>{{ $deg['year'] }}</td>
                                <td>{{ $deg['gpa'] }} / {{ $deg['scale'] }}</td>
                                <td>{{ $deg['division'] }}</td>
                                <td>
                                    @if(isset($deg['proof_path']))
                                        <a href="/storage/{{ $deg['proof_path'] }}" target="_blank" style="color: var(--secondary)">View File</a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </x-table>
                @endif

                <!-- Job Experiences -->
                @if($selectedRecordDetails->experiences->isNotEmpty())
                    <h4 style="color: var(--secondary); font-weight: 700;" class="mt-6 mb-2">💼 Employment History Log</h4>
                    <x-table :headers="['Designation', 'Company Name', 'Tenure Dates Range', 'Sector', 'Gross Salary', 'Description', 'Letter File']">
                        @foreach($selectedRecordDetails->experiences as $exp)
                            <tr>
                                <td><strong>{{ $exp->job_title }}</strong></td>
                                <td>{{ $exp->organization }}</td>
                                <td><code>{{ $exp->start_date?->format('Y-m-d') }}</code> to <code>{{ $exp->end_date?->format('Y-m-d') ?: 'Present' }}</code></td>
                                <td>{{ $exp->sector }}</td>
                                <td>PKR {{ number_format($exp->salary ?? 0, 2) }}</td>
                                <td>{{ $exp->description }}</td>
                                <td>
                                    @if(isset($exp['proof_path']))
                                        <a href="/storage/{{ $exp['proof_path'] }}" target="_blank" style="color: var(--secondary)">Open NOC</a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </x-table>
                @endif
            </div>
        @endif
    </x-modal>

    <!-- 3. Security Audit Log Details Modal -->
    <x-modal :show="$isDetailsOpen && $activeTab === 'logs'" title="🔍 Security Audit State Details Diff" closeAction="$set('isDetailsOpen', false)" style="max-width: 800px;">
        @if($selectedRecordDetails)
            <div style="max-height: 450px; overflow-y: auto;">
                <div class="grid grid-cols-3 gap-4 border-bottom pb-4 mb-4">
                    <div>
                        <p style="color: var(--color-text-secondary)">Action Class:</p>
                        <x-badge type="appealed">{{ $selectedRecordDetails->action_type }}</x-badge>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary)">Database Table:</p>
                        <code>{{ $selectedRecordDetails->table_name }}</code>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary)">Record reference ID:</p>
                        <code>ID #{{ $selectedRecordDetails->record_id }}</code>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6 mt-4">
                    <div>
                        <h4 style="color: var(--danger); font-size: 0.95rem; margin-bottom: 0.5rem;">🔴 State Before Change:</h4>
                        <pre style="background: rgba(0,0,0,0.5); padding: 1rem; border-radius: 6px; font-size: 0.8rem; overflow-x: auto; color: #fca5a5;">{{ json_encode($selectedRecordDetails->state_before, JSON_PRETTY_PRINT) ?: 'No previous state (NEW INSERT)' }}</pre>
                    </div>
                    <div>
                        <h4 style="color: var(--success); font-size: 0.95rem; margin-bottom: 0.5rem;">🟢 State After Change:</h4>
                        <pre style="background: rgba(0,0,0,0.5); padding: 1rem; border-radius: 6px; font-size: 0.8rem; overflow-x: auto; color: #a7f3d0;">{{ json_encode($selectedRecordDetails->state_after, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            </div>
        @endif
    </x-modal>
</div>
