<div>
    <!-- Console Control Header -->
    <div class="card mb-6" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; position: relative; overflow: hidden;">
        <div style="position: absolute; right: -2rem; top: -2rem; width: 140px; height: 140px; background: radial-gradient(circle, var(--primary-glow) 0%, transparent 70%); pointer-events: none;"></div>
        <div>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.35rem;">
                <div class="brand-icon-box" style="width: 38px; height: 38px; border-radius: 10px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                </div>
                <div>
                    <h2 class="card-title" style="margin: 0; font-size: 1.35rem;">Applications Desk</h2>
                    <p class="card-subtitle" style="margin: 0;">Comprehensive candidate application registry, processing stages, and dossier milestones</p>
                </div>
            </div>
        </div>
        <div>
            @if(!$isFormOpen && !$isDetailsOpen)
                @if(auth()->user()->can('create-application') || auth()->user()->hasAnyRole(['super-admin', 'registrar']))
                    <button type="button" class="btn btn-primary" wire:click="openCreateApplicationForm" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>Add Application</span>
                    </button>
                @endif
            @endif
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session()->has('message'))
        <div class="alert alert-success mb-6" style="display: flex; align-items: center; gap: 0.75rem; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3); color: var(--success); padding: 0.85rem 1.25rem; border-radius: var(--radius-md);">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger mb-6" style="display: flex; align-items: center; gap: 0.75rem; background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.3); color: var(--danger); padding: 0.85rem 1.25rem; border-radius: var(--radius-md);">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Form Drawer -->
    @if ($isFormOpen)
        <div class="card mb-6" style="border-color: var(--primary-glow); box-shadow: var(--shadow-md);">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                <h3 class="card-title" style="margin: 0; font-size: 1.2rem;">
                    {{ $isEditMode ? 'Manage Application Status & Progression' : 'Register Application Record' }}
                </h3>
                <button type="button" class="btn btn-secondary" wire:click="resetForm" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;">Cancel</button>
            </div>
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
                    <button type="submit" class="btn btn-primary">Save Application</button>
                    <button type="button" class="btn btn-secondary" wire:click="resetForm">Cancel</button>
                </div>
            </form>
        </div>
    @else
        <!-- Filter Bar -->
        <div class="card mb-4" style="padding: 1rem 1.25rem;">
            <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 250px;">
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Search applicant by name, email, CNIC, or job code...">
                </div>
                <div style="width: 200px;">
                    <select class="form-control form-select" wire:model.live="statusFilter">
                        <option value="">All Processing Stages</option>
                        <option value="DRAFT">Draft</option>
                        <option value="SUBMITTED">Submitted</option>
                        <option value="PAID">Paid / In Scrutiny</option>
                        <option value="ELIGIBLE">Eligible</option>
                        <option value="INELIGIBLE">Ineligible</option>
                        <option value="APPEALED">Appealed Grievance</option>
                        <option value="UNDER_REVIEW">Under Review</option>
                        <option value="APPROVED">Approved for Evaluation</option>
                        <option value="REJECTED">Rejected</option>
                    </select>
                </div>
                <div>
                    <span style="font-size: 0.85rem; color: var(--color-text-secondary); font-weight: 600;">
                        Showing {{ count($applications) }} record{{ count($applications) === 1 ? '' : 's' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Applications Table -->
        <x-table :headers="['Candidate details', 'Campaign Code', 'Voucher status', 'Processing stage', 'Submitted Timestamp', 'Actions']">
            @forelse($applications as $app)
                <tr>
                    <td>
                        <strong style="color: var(--color-text-primary)">{{ $app->user->basicInfo?->first_name ?? 'N/A' }} {{ $app->user->basicInfo?->last_name ?? 'N/A' }}</strong><br>
                        <small style="color: var(--color-text-secondary)">{{ $app->user->email }}</small>
                    </td>
                    <td><code>{{ $app->campaign->job_code }}</code></td>
                    <td>
                        <span class="badge" style="background: {{ $app->billingLedger?->status === 'SETTLED' ? 'rgba(16,185,129,0.15)' : 'rgba(245,158,11,0.15)' }}; color: {{ $app->billingLedger?->status === 'SETTLED' ? 'var(--success)' : 'var(--warning)' }};">
                            {{ $app->billingLedger?->status ?: 'No Voucher' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary); border: 1px solid rgba(99,102,241,0.25);">
                            {{ $app->status }}
                        </span>
                        @if($app->disqualification_reason)
                            <br><small style="color: var(--danger); font-size: 0.75rem;">{{ Str::limit($app->disqualification_reason, 25) }}</small>
                        @endif
                    </td>
                    <td><span style="font-size: 0.85rem; color: var(--color-text-secondary); font-family: monospace;">{{ $app->submission_timestamp ?: 'In draft' }}</span></td>
                    <td>
                        <div class="flex gap-2">
                            <button type="button" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="viewApplicationDetails({{ $app->id }})">Details</button>
                            <button type="button" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="openEditApplicationForm({{ $app->id }})">Status</button>
                            <a href="/admin/verification/{{ $app->id }}" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; text-decoration: none;" title="Open Verification Desk">Verify</a>
                            <button type="button" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="deleteApplication({{ $app->id }})" onclick="confirm('Are you sure you want to delete this application record?') || event.stopImmediatePropagation()">Delete</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center" style="padding: 2.5rem; color: var(--color-text-secondary);">No recruitment applications found.</td></tr>
            @endforelse
        </x-table>
    @endif

    <!-- Application Details Modal -->
    <x-modal :show="$isDetailsOpen" title="Application Profile Details Summary" closeAction="$set('isDetailsOpen', false)" style="max-width: 800px;">
        @if($selectedRecordDetails)
            <div style="max-height: 500px; overflow-y: auto; padding-right: 0.5rem;">
                <div class="grid grid-cols-2 gap-4 border-bottom pb-4 mb-4">
                    <div>
                        <p style="color: var(--color-text-secondary); margin-bottom: 0.2rem;">Candidate Full Name:</p>
                        <strong>{{ $selectedRecordDetails->user->basicInfo?->first_name }} {{ $selectedRecordDetails->user->basicInfo?->last_name }}</strong>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary); margin-bottom: 0.2rem;">Email Reference:</p>
                        <code>{{ $selectedRecordDetails->user->email }}</code>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4 border-bottom pb-4 mb-4">
                    <div>
                        <p style="color: var(--color-text-secondary); margin-bottom: 0.2rem;">Job Position:</p>
                        <strong>{{ $selectedRecordDetails->campaign->title }}</strong>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary); margin-bottom: 0.2rem;">Job Code:</p>
                        <code style="color: var(--secondary)">{{ $selectedRecordDetails->campaign->job_code }}</code>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary); margin-bottom: 0.2rem;">Track Classification:</p>
                        <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary);">{{ $selectedRecordDetails->campaign->track_type }}</span>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4 border-bottom pb-4 mb-4">
                    <div>
                        <p style="color: var(--color-text-secondary); margin-bottom: 0.2rem;">Challan Voucher Status:</p>
                        <span class="badge" style="background: {{ $selectedRecordDetails->billingLedger?->status === 'SETTLED' ? 'rgba(16,185,129,0.15)' : 'rgba(245,158,11,0.15)' }}; color: {{ $selectedRecordDetails->billingLedger?->status === 'SETTLED' ? 'var(--success)' : 'var(--warning)' }};">
                            {{ $selectedRecordDetails->billingLedger?->status ?: 'No Voucher' }}
                        </span>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary); margin-bottom: 0.2rem;">Consumer Reference ID:</p>
                        <code>{{ $selectedRecordDetails->billingLedger?->consumer_id ?: 'N/A' }}</code>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary); margin-bottom: 0.2rem;">Voucher Amount Paid:</p>
                        <strong style="color: var(--success)">PKR {{ number_format($selectedRecordDetails->billingLedger?->amount ?? 0, 2) }}</strong>
                    </div>
                </div>

                <!-- Academic Peer Review Status Card -->
                @if($selectedRecordDetails->campaign->track_type === 'ACADEMIC')
                    <div class="card mb-4 mt-4" style="background: rgba(6, 182, 212, 0.05); border-color: var(--secondary-glow); padding: 1.25rem;">
                        <div class="flex justify-between align-center mb-2" style="flex-wrap: wrap; gap: 0.5rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--secondary)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                <h4 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: var(--color-text-primary);">
                                    External Academic Peer Reviews ({{ count($selectedRecordDetails->reviews ?? []) }} Assigned)
                                </h4>
                            </div>
                            <a href="/admin/reviewers" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; text-decoration: none;">
                                Reviewers Desk →
                            </a>
                        </div>

                        @if(!empty($selectedRecordDetails->reviews) && count($selectedRecordDetails->reviews) > 0)
                            <div class="flex-col gap-2 mt-3">
                                @foreach($selectedRecordDetails->reviews as $rev)
                                    <div style="background: var(--bg-card); padding: 0.65rem 0.85rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                                        <div>
                                            <strong style="font-size: 0.88rem; color: var(--color-text-primary);">
                                                {{ $rev->reviewer->reviewerProfile?->name ?? 'External Referee' }}
                                            </strong>
                                            <span style="font-size: 0.75rem; color: var(--color-text-muted); margin-left: 0.35rem;">
                                                ({{ $rev->reviewer->reviewerProfile?->university }})
                                            </span>
                                        </div>
                                        <div>
                                            @if($rev->status === 'SUBMITTED')
                                                <span class="badge" style="background: rgba(16,185,129,0.15); color: var(--success); font-weight: 700; font-size: 0.75rem;">
                                                    ✓ {{ $rev->overall_score }}/100 • {{ str_replace('_', ' ', $rev->recommendation) }}
                                                </span>
                                            @else
                                                <span class="badge" style="background: rgba(245,158,11,0.15); color: var(--warning); font-size: 0.75rem;">
                                                    ⏳ Pending Evaluation
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p style="font-size: 0.82rem; color: var(--color-text-muted); margin: 0.5rem 0 0;">
                                Dossier has not yet been dispatched to external academic reviewers.
                            </p>
                        @endif
                    </div>
                @endif

                @if(!empty($selectedRecordDetails->evaluations) && count($selectedRecordDetails->evaluations) > 0)
                    <h4 class="mb-2 mt-4" style="color: var(--secondary)">Selection Board Evaluation Results</h4>
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
                                        <span class="badge" style="background: rgba(239,68,68,0.15); color: var(--danger);">Outlier Alert</span><br>
                                        <small style="color: var(--color-text-muted)">Justify: {{ $eval->outlier_justification }}</small>
                                    @else
                                        <span class="badge" style="background: rgba(16,185,129,0.15); color: var(--success);">Normal</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center">No board grades submitted yet.</td></tr>
                        @endforelse
                    </x-table>
                @endif

                <div class="mt-6 flex justify-between align-center" style="border-top: 1px solid var(--border-color); padding-top: 1rem;">
                    <a href="/admin/verification/{{ $selectedRecordDetails->id }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        <span>Open Scrutiny Verification Desk</span>
                    </a>
                    <button type="button" class="btn btn-secondary" wire:click="$set('isDetailsOpen', false)">Close</button>
                </div>
            </div>
        @endif
    </x-modal>
</div>

