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
                @can('create-application')
                    <button type="button" class="btn btn-primary" wire:click="openCreateApplicationForm" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>Add Application</span>
                    </button>
                @endcan
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

    <!-- Executive Metric Cards -->
    @if(!$isFormOpen && !$isDetailsOpen)
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
            <div class="card" style="padding: 1.15rem 1.25rem; display: flex; align-items: center; gap: 1rem; border-left: 4px solid var(--primary);">
                <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(99,102,241,0.12); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: var(--color-text-secondary); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Total Applications</div>
                    <div style="font-size: 1.6rem; font-weight: 800; color: var(--color-text-primary); line-height: 1.2;">{{ $stats['total'] }}</div>
                </div>
            </div>

            <div class="card" style="padding: 1.15rem 1.25rem; display: flex; align-items: center; gap: 1rem; border-left: 4px solid var(--success);">
                <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(16,185,129,0.12); display: flex; align-items: center; justify-content: center; color: var(--success);">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: var(--color-text-secondary); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Paid / Settled</div>
                    <div style="font-size: 1.6rem; font-weight: 800; color: var(--success); line-height: 1.2;">{{ $stats['paid'] }}</div>
                </div>
            </div>

            <div class="card" style="padding: 1.15rem 1.25rem; display: flex; align-items: center; gap: 1rem; border-left: 4px solid #3b82f6;">
                <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(59,130,246,0.12); display: flex; align-items: center; justify-content: center; color: #3b82f6;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: var(--color-text-secondary); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Eligible Dossiers</div>
                    <div style="font-size: 1.6rem; font-weight: 800; color: #3b82f6; line-height: 1.2;">{{ $stats['eligible'] }}</div>
                </div>
            </div>

            <div class="card" style="padding: 1.15rem 1.25rem; display: flex; align-items: center; gap: 1rem; border-left: 4px solid var(--secondary);">
                <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(6,182,212,0.12); display: flex; align-items: center; justify-content: center; color: var(--secondary);">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: var(--color-text-secondary); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">In Review / Appeal</div>
                    <div style="font-size: 1.6rem; font-weight: 800; color: var(--secondary); line-height: 1.2;">{{ $stats['under_review'] }}</div>
                </div>
            </div>

            <div class="card" style="padding: 1.15rem 1.25rem; display: flex; align-items: center; gap: 1rem; border-left: 4px solid #8b5cf6;">
                <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(139,92,246,0.12); display: flex; align-items: center; justify-content: center; color: #8b5cf6;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: var(--color-text-secondary); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Approved Selection</div>
                    <div style="font-size: 1.6rem; font-weight: 800; color: #8b5cf6; line-height: 1.2;">{{ $stats['approved'] }}</div>
                </div>
            </div>
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
        <div class="card mb-4" style="padding: 1.15rem 1.25rem;">
            <div style="display: flex; gap: 0.85rem; align-items: center; flex-wrap: wrap;">
                <div style="flex: 2; min-width: 260px; position: relative;">
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Search applicant by name, email, CNIC, or job code..." style="padding-left: 2.25rem;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: var(--color-text-muted); pointer-events: none;">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.3-4.3"/>
                    </svg>
                </div>

                <div style="flex: 1.5; min-width: 200px;">
                    <select class="form-control form-select" wire:model.live="campaignFilter">
                        <option value="">All Campaigns</option>
                        @foreach($campaigns as $camp)
                            <option value="{{ $camp->id }}">{{ $camp->job_code }} - {{ Str::limit($camp->title, 28) }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="flex: 1; min-width: 170px;">
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

                <div style="flex: 1; min-width: 160px;">
                    <select class="form-control form-select" wire:model.live="voucherFilter">
                        <option value="">All Voucher Status</option>
                        <option value="SETTLED">Settled / Paid</option>
                        <option value="PENDING">Pending / Unpaid</option>
                    </select>
                </div>

                <div>
                    <span style="font-size: 0.85rem; color: var(--color-text-secondary); font-weight: 600; white-space: nowrap;">
                        Showing {{ count($applications) }} dossier{{ count($applications) === 1 ? '' : 's' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Applications Table -->
        <div class="card" style="padding: 0; overflow: hidden;">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Candidate Particulars</th>
                            <th>Applied Vacancy</th>
                            <th>Voucher Ledger</th>
                            <th>Processing Stage</th>
                            <th>Submitted Date</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $app)
                            <tr wire:key="app-row-{{ $app->id }}">
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.85rem;">
                                        <div class="user-avatar-circle" style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, rgba(99,102,241,0.22), rgba(6,182,212,0.22)); color: var(--primary); font-weight: 800; font-size: 0.95rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid rgba(99,102,241,0.2);">
                                            {{ strtoupper(substr($app->user->basicInfo?->first_name ?: $app->user->email, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong style="color: var(--color-text-primary); font-size: 0.94rem;">
                                                {{ $app->user->basicInfo ? trim($app->user->basicInfo->first_name . ' ' . $app->user->basicInfo->last_name) : 'No Name Profile' }}
                                            </strong>
                                            <div style="font-size: 0.78rem; color: var(--color-text-muted); font-family: 'JetBrains Mono', monospace; margin-top: 2px;">
                                                {{ $app->user->email }}
                                            </div>
                                            @if($app->user->basicInfo?->cnic_number)
                                                <div style="font-size: 0.72rem; color: var(--color-text-muted); margin-top: 1px;">
                                                    CNIC: <code>{{ $app->user->basicInfo->cnic_number }}</code>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong style="color: var(--color-text-primary); font-size: 0.92rem;">{{ $app->campaign->title }}</strong>
                                        <div style="display: flex; gap: 0.4rem; align-items: center; margin-top: 3px;">
                                            <span style="font-size: 0.74rem; font-family: 'JetBrains Mono', monospace; font-weight: 700; color: var(--primary);">
                                                {{ $app->campaign->job_code }}
                                            </span>
                                            <span class="badge" style="background: rgba(99,102,241,0.1); color: var(--primary); font-size: 0.68rem; padding: 0.15rem 0.45rem;">
                                                {{ $app->campaign->track_type }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($app->billingLedger?->status === 'SETTLED')
                                        <span class="badge badge-paid" style="font-size: 0.78rem; font-weight: 700;">
                                            ✓ SETTLED
                                        </span>
                                        <div style="font-size: 0.72rem; color: var(--success); font-weight: 600; margin-top: 2px;">
                                            PKR {{ number_format($app->billingLedger->amount, 0) }}
                                        </div>
                                    @else
                                        <span class="badge" style="background: rgba(245,158,11,0.15); color: var(--warning); border: 1px solid rgba(245,158,11,0.3); font-size: 0.75rem; font-weight: 700;">
                                            ⏳ PENDING
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($app->status === 'APPROVED')
                                        <span class="badge" style="background: rgba(16,185,129,0.15); color: var(--success); border: 1px solid rgba(16,185,129,0.3); font-weight: 700;">
                                            ✓ Approved
                                        </span>
                                    @elseif($app->status === 'UNDER_REVIEW')
                                        <span class="badge" style="background: rgba(6,182,212,0.15); color: var(--secondary); border: 1px solid rgba(6,182,212,0.3); font-weight: 700;">
                                            ⏳ Under Review
                                        </span>
                                    @elseif($app->status === 'ELIGIBLE')
                                        <span class="badge badge-paid" style="font-weight: 700;">
                                            ✓ Eligible
                                        </span>
                                    @elseif($app->status === 'INELIGIBLE' || $app->status === 'REJECTED')
                                        <span class="badge" style="background: rgba(239,68,68,0.15); color: var(--danger); border: 1px solid rgba(239,68,68,0.3); font-weight: 700;">
                                            ✕ {{ $app->status }}
                                        </span>
                                        @if($app->disqualification_reason)
                                            <div style="color: var(--danger); font-size: 0.72rem; margin-top: 2px; max-width: 150px;" title="{{ $app->disqualification_reason }}">
                                                {{ Str::limit($app->disqualification_reason, 22) }}
                                            </div>
                                        @endif
                                    @elseif($app->status === 'APPEALED')
                                        <span class="badge" style="background: rgba(245,158,11,0.15); color: var(--warning); border: 1px solid rgba(245,158,11,0.3); font-weight: 700;">
                                            ⚠ Grievance Appealed
                                        </span>
                                    @else
                                        <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary); border: 1px solid rgba(99,102,241,0.25); font-weight: 700;">
                                            {{ $app->status }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-size: 0.82rem; color: var(--color-text-secondary); font-family: 'JetBrains Mono', monospace;">
                                        {{ $app->submission_timestamp ? \Carbon\Carbon::parse($app->submission_timestamp)->format('Y-m-d H:i') : 'Draft state' }}
                                    </div>
                                    @if($app->submission_timestamp)
                                        <div style="font-size: 0.72rem; color: var(--color-text-muted);">
                                            {{ \Carbon\Carbon::parse($app->submission_timestamp)->diffForHumans() }}
                                        </div>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <div class="flex gap-2 justify-end align-center">
                                        @can('view-application')
                                            <button type="button" class="btn btn-secondary btn-sm" style="padding: 0.35rem 0.65rem; font-size: 0.78rem;" wire:click="viewApplicationDetails({{ $app->id }})">Details</button>
                                        @endcan
                                        @if(auth()->user()->can('change-application-status') || auth()->user()->can('edit-application'))
                                            <button type="button" class="btn btn-secondary btn-sm" style="padding: 0.35rem 0.65rem; font-size: 0.78rem;" wire:click="openEditApplicationForm({{ $app->id }})">Status</button>
                                        @endif
                                        @if(auth()->user()->can('scrutinize-application') || auth()->user()->can('verify-documents'))
                                            <a href="/admin/verification/{{ $app->id }}" class="btn btn-secondary btn-sm" style="padding: 0.35rem 0.65rem; font-size: 0.78rem; text-decoration: none;" title="Open Verification Desk">Verify</a>
                                        @endif
                                        @can('delete-application')
                                            <button type="button" class="btn btn-danger btn-sm" style="padding: 0.35rem 0.65rem; font-size: 0.78rem;" wire:click="deleteApplication({{ $app->id }})" onclick="confirm('Are you sure you want to delete this application record?') || event.stopImmediatePropagation()">Delete</button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center" style="padding: 3.5rem; color: var(--color-text-muted);">
                                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(99,102,241,0.1); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    </div>
                                    <h4 style="font-size: 1rem; font-weight: 700; color: var(--color-text-primary); margin: 0 0 0.35rem 0;">No Candidate Applications Found</h4>
                                    <p style="font-size: 0.82rem; margin: 0;">No records match your active search and filter parameters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
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

