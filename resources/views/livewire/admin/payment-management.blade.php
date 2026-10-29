<div>
    <!-- Executive Control Header -->
    <div class="card mb-6 card-ambient-mesh" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.25rem; position: relative; overflow: hidden; padding: 1.5rem 1.75rem;">
        <div style="position: absolute; right: -2rem; top: -2rem; width: 180px; height: 180px; background: radial-gradient(circle, var(--success-glow) 0%, transparent 70%); pointer-events: none;"></div>
        <div>
            <div style="display: flex; align-items: center; gap: 0.85rem; margin-bottom: 0.35rem;">
                <div class="brand-icon-box" style="width: 44px; height: 44px; border-radius: 12px; background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3);">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--success);">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                        <line x1="1" y1="10" x2="23" y2="10"/>
                    </svg>
                </div>
                <div>
                    <h1 class="card-title" style="margin: 0; font-size: 1.5rem; font-weight: 800; letter-spacing: -0.02em;">
                        Financial Ledger & Voucher Gateway
                    </h1>
                    <p class="card-subtitle" style="margin: 0.2rem 0 0;">
                        Monitor Kuickpay / 1Link consumer challans, bank settlement logs, and instant fee reconciliations.
                    </p>
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <a href="/bank/simulator" target="_blank" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.4rem; text-decoration: none; border-color: var(--secondary-glow); color: var(--secondary); font-weight: 700; padding: 0.55rem 1rem;" title="Open Fintech Sandbox">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                <span>1Link Simulator</span>
            </a>
            @if(!$isFormOpen)
                @can('create-payment')
                    <button type="button" class="btn btn-primary" wire:click="openCreatePaymentForm" style="display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: var(--shadow-sm); padding: 0.55rem 1.15rem; font-weight: 700;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>+ Log Voucher / Fee</span>
                    </button>
                @endcan
            @endif
        </div>
    </div>

    <!-- Live Executive Financial KPI Cards -->
    @if(!$isFormOpen)
        <div class="grid grid-cols-4 gap-5 mb-6">
            <div class="metric-card" style="--card-accent: var(--primary); --metric-icon-bg: var(--primary-light); --metric-icon-color: var(--primary);">
                <div class="metric-header">
                    <span class="metric-label">Total Billed Vouchers</span>
                    <div class="metric-icon-wrap">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" x2="22" y2="10"/>
                        </svg>
                    </div>
                </div>
                <div class="metric-value" style="color: var(--primary);">{{ $stats['total'] }}</div>
                <div class="metric-subtext">Consumer bills generated</div>
            </div>

            <div class="metric-card" style="--card-accent: var(--success); --metric-icon-bg: var(--success-light); --metric-icon-color: var(--success);">
                <div class="metric-header">
                    <span class="metric-label">Settled / Paid</span>
                    <div class="metric-icon-wrap">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                    </div>
                </div>
                <div class="metric-value" style="color: var(--success);">{{ $stats['settled'] }}</div>
                <div class="metric-subtext">Confirmed bank payments</div>
            </div>

            <div class="metric-card" style="--card-accent: var(--warning); --metric-icon-bg: var(--warning-light); --metric-icon-color: var(--warning);">
                <div class="metric-header">
                    <span class="metric-label">Pending Payment</span>
                    <div class="metric-icon-wrap">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                </div>
                <div class="metric-value" style="color: var(--warning);">{{ $stats['pending'] }}</div>
                <div class="metric-subtext">Awaiting candidate deposit</div>
            </div>

            <div class="metric-card" style="--card-accent: var(--secondary); --metric-icon-bg: var(--secondary-light); --metric-icon-color: var(--secondary);">
                <div class="metric-header">
                    <span class="metric-label">Total Realized Revenue</span>
                    <div class="metric-icon-wrap">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        </svg>
                    </div>
                </div>
                <div class="metric-value" style="color: var(--secondary); font-size: 1.35rem;">PKR {{ number_format($stats['total_revenue'], 0) }}</div>
                <div class="metric-subtext">Reconciled to institutional account</div>
            </div>
        </div>
    @endif

    <!-- Flash Message Notification -->
    @if(session()->has('message'))
        <div class="alert alert-success mb-6" style="display: flex; align-items: center; gap: 0.75rem; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3); color: var(--success); padding: 0.95rem 1.35rem; border-radius: var(--radius-md);">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <div style="flex: 1; font-weight: 600;">
                <span>{{ session('message') }}</span>
            </div>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger mb-6" style="display: flex; align-items: center; gap: 0.75rem; background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.3); color: var(--danger); padding: 0.95rem 1.35rem; border-radius: var(--radius-md);">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
            <div style="flex: 1; font-weight: 600;">
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Form Drawer -->
    @if ($isFormOpen)
        <div class="card mb-6 card-ambient-mesh" style="border-color: var(--primary); box-shadow: var(--shadow-lg); padding: 1.75rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <h3 class="card-title" style="margin: 0; font-size: 1.25rem; font-weight: 800;">
                        {{ $isEditMode ? 'Modify Processing Voucher & Bank Reconciliation' : 'Log Processing Voucher' }}
                    </h3>
                    <p class="card-subtitle" style="margin: 0.2rem 0 0;">
                        Manage 1Link consumer reference number, fee amount, and settlement status.
                    </p>
                </div>
                <button type="button" class="btn btn-secondary btn-sm" wire:click="resetForm">Cancel</button>
            </div>

            <form wire:submit.prevent="savePayment">
                @if(!$isEditMode)
                    <div class="grid grid-cols-2 gap-5 mb-4">
                        <x-select label="Select Active Candidate Application *" :error="$errors->first('pay_app_id')">
                            <select class="form-control form-select" wire:model="pay_app_id">
                                <option value="">-- Choose Application --</option>
                                @foreach($availableApplications as $app)
                                    <option value="{{ $app->id }}">{{ $app->user->email }} (Job: {{ $app->campaign->job_code }} - {{ $app->campaign->title }})</option>
                                @endforeach
                            </select>
                        </x-select>
                        <x-input label="12-digit 1Link Consumer ID *" :error="$errors->first('pay_consumer_id')">
                            <input type="text" class="form-control" wire:model="pay_consumer_id" placeholder="e.g. 100012345678">
                        </x-input>
                    </div>
                @endif

                <div class="grid grid-cols-3 gap-5 mt-4 align-center">
                    <x-input label="Total Processing Fee (PKR) *" :error="$errors->first('pay_amount')">
                        <input type="number" step="0.01" class="form-control" wire:model="pay_amount">
                    </x-input>
                    <x-select label="Voucher Settlement Status *" :error="$errors->first('pay_status')">
                        <select class="form-control form-select" wire:model="pay_status">
                            <option value="PENDING">Pending Payment Deposit</option>
                            <option value="AUTHORIZED">Authorized in Switch</option>
                            <option value="SETTLED">Settled / PAID</option>
                            <option value="EXPIRED">Expired Voucher</option>
                        </select>
                    </x-select>
                    <x-input label="Bank Transaction Reference ID (TID)" :error="$errors->first('pay_transaction_id')">
                        <input type="text" class="form-control" wire:model="pay_transaction_id" placeholder="TID-123456">
                    </x-input>
                </div>

                <div class="grid grid-cols-2 gap-5 mt-4">
                    <x-input label="Reconciled Timestamp" :error="$errors->first('pay_timestamp')">
                        <input type="datetime-local" class="form-control" wire:model="pay_timestamp">
                    </x-input>
                </div>

                <div class="flex gap-4 mt-6" style="border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                    <button type="submit" class="btn btn-primary" style="padding: 0.55rem 1.35rem; font-weight: 700;">Save Voucher</button>
                    <button type="button" class="btn btn-secondary" wire:click="resetForm">Cancel</button>
                </div>
            </form>
        </div>
    @else
        <!-- Quick Status Segmented Tabs -->
        <div style="display: flex; gap: 0.5rem; background: var(--bg-tertiary); padding: 0.35rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color); width: fit-content; margin-bottom: 1.25rem; flex-wrap: wrap;">
            <button type="button" 
                    class="btn" 
                    wire:click="$set('statusFilter', '')"
                    style="padding: 0.45rem 1.15rem; font-size: 0.85rem; border-radius: var(--radius-md); font-weight: 700; display: inline-flex; align-items: center; gap: 0.45rem; transition: var(--transition-base); {{ empty($statusFilter) ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
                <span>All Vouchers</span>
                <span style="font-size: 0.72rem; padding: 0.1rem 0.45rem; border-radius: 999px; background: rgba(99,102,241,0.15); color: var(--primary); font-weight: 800;">{{ $stats['total'] }}</span>
            </button>

            <button type="button" 
                    class="btn" 
                    wire:click="$set('statusFilter', 'SETTLED')"
                    style="padding: 0.45rem 1.15rem; font-size: 0.85rem; border-radius: var(--radius-md); font-weight: 700; display: inline-flex; align-items: center; gap: 0.45rem; transition: var(--transition-base); {{ $statusFilter === 'SETTLED' ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
                <span>Settled / Paid</span>
                <span style="font-size: 0.72rem; padding: 0.1rem 0.45rem; border-radius: 999px; background: rgba(16,185,129,0.15); color: var(--success); font-weight: 800;">{{ $stats['settled'] }}</span>
            </button>

            <button type="button" 
                    class="btn" 
                    wire:click="$set('statusFilter', 'PENDING')"
                    style="padding: 0.45rem 1.15rem; font-size: 0.85rem; border-radius: var(--radius-md); font-weight: 700; display: inline-flex; align-items: center; gap: 0.45rem; transition: var(--transition-base); {{ $statusFilter === 'PENDING' ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
                <span>Pending Payment</span>
                <span style="font-size: 0.72rem; padding: 0.1rem 0.45rem; border-radius: 999px; background: rgba(245,158,11,0.15); color: var(--warning); font-weight: 800;">{{ $stats['pending'] }}</span>
            </button>
        </div>

        <!-- Filter Ribbon & Search Bar -->
        <div class="card mb-5 card-ambient-mesh" style="padding: 1.25rem 1.5rem;">
            <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                <div style="flex: 2; min-width: 280px; position: relative;">
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Search by Consumer ID, TID, applicant email, or campaign..." style="padding-left: 2.35rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: var(--color-text-muted); pointer-events: none;">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.3-4.3"/>
                    </svg>
                </div>

                <div style="flex: 1; min-width: 180px;">
                    <select class="form-control form-select" wire:model.live="statusFilter">
                        <option value="">All Settlement Statuses</option>
                        <option value="SETTLED">Settled / Paid</option>
                        <option value="PENDING">Pending Payment</option>
                        <option value="AUTHORIZED">Authorized</option>
                        <option value="EXPIRED">Expired</option>
                    </select>
                </div>

                @if(!empty($search) || !empty($statusFilter))
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="$set('search', ''); $set('statusFilter', '');" style="display: inline-flex; align-items: center; gap: 0.35rem;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        <span>Reset</span>
                    </button>
                @endif

                <div style="margin-left: auto;">
                    <span style="font-size: 0.82rem; color: var(--color-text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;">
                        Showing <strong style="color: var(--primary);">{{ count($payments) }}</strong> Record{{ count($payments) === 1 ? '' : 's' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Modern Ledger Table -->
        <div class="card" style="padding: 0; overflow: hidden; border-color: var(--border-color); box-shadow: var(--shadow-sm);">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>1Link Consumer ID</th>
                            <th>Applicant Candidate</th>
                            <th>Target Vacancy</th>
                            <th>Amount (PKR)</th>
                            <th>Voucher Status</th>
                            <th>Bank Transaction ID</th>
                            <th>Settlement Timestamp</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $pay)
                            <tr wire:key="payment-row-{{ $pay->id }}">
                                <td>
                                    <code style="color: var(--secondary); font-weight: 800; font-family: 'JetBrains Mono', monospace; font-size: 0.88rem; background: rgba(6,182,212,0.1); padding: 0.2rem 0.55rem; border-radius: 4px; border: 1px solid rgba(6,182,212,0.25);">
                                        {{ $pay->consumer_id }}
                                    </code>
                                </td>
                                <td>
                                    <div style="font-weight: 800; color: var(--color-text-primary); font-size: 0.94rem;">
                                        {{ $pay->application->user->basicInfo?->first_name ?? '' }} {{ $pay->application->user->basicInfo?->last_name ?? '' }}
                                    </div>
                                    <div style="font-size: 0.78rem; color: var(--color-text-secondary); margin-top: 2px;">
                                        {{ $pay->application->user->email }}
                                    </div>
                                </td>
                                <td>
                                    <strong style="color: var(--color-text-primary); font-size: 0.88rem;">{{ $pay->application->campaign->title }}</strong>
                                    <div style="font-size: 0.74rem; font-family: 'JetBrains Mono', monospace; color: var(--primary); margin-top: 2px;">
                                        {{ $pay->application->campaign->job_code }}
                                    </div>
                                </td>
                                <td>
                                    <strong style="font-size: 0.95rem; color: {{ $pay->status === 'SETTLED' ? 'var(--success)' : 'var(--color-text-primary)' }};">
                                        PKR {{ number_format($pay->amount, 2) }}
                                    </strong>
                                </td>
                                <td>
                                    @if($pay->status === 'SETTLED')
                                        <span class="badge" style="background: rgba(16,185,129,0.15); color: var(--success); border: 1px solid rgba(16,185,129,0.3); font-weight: 800; font-size: 0.76rem;">
                                            ✓ SETTLED
                                        </span>
                                    @elseif($pay->status === 'PENDING')
                                        <span class="badge" style="background: rgba(245,158,11,0.15); color: var(--warning); border: 1px solid rgba(245,158,11,0.3); font-weight: 800; font-size: 0.76rem;">
                                            ⏳ PENDING
                                        </span>
                                    @elseif($pay->status === 'AUTHORIZED')
                                        <span class="badge" style="background: rgba(6,182,212,0.15); color: var(--secondary); border: 1px solid rgba(6,182,212,0.3); font-weight: 800; font-size: 0.76rem;">
                                            AUTHORIZED
                                        </span>
                                    @else
                                        <span class="badge" style="background: rgba(239,68,68,0.15); color: var(--danger); border: 1px solid rgba(239,68,68,0.3); font-weight: 800; font-size: 0.76rem;">
                                            {{ $pay->status }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <code style="font-size: 0.82rem; font-family: 'JetBrains Mono', monospace; color: var(--color-text-secondary);">
                                        {{ $pay->transaction_id ?: 'N/A' }}
                                    </code>
                                </td>
                                <td>
                                    <span style="font-size: 0.82rem; color: var(--color-text-secondary); font-family: 'JetBrains Mono', monospace;">
                                        {{ $pay->payment_timestamp ? \Carbon\Carbon::parse($pay->payment_timestamp)->format('Y-m-d H:i') : 'Unpaid' }}
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <div class="action-btn-group justify-end">
                                        @if($pay->status !== 'SETTLED')
                                            @if(auth()->user()->can('reconcile-payment') || auth()->user()->can('edit-payment'))
                                                <button type="button" 
                                                        class="action-btn action-btn-success" 
                                                        wire:click="markSettled({{ $pay->id }})" 
                                                        data-tooltip="Quick Reconcile Paid (Mark Settled)"
                                                        aria-label="Reconcile Payment">
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="20 6 9 17 4 12"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        @endif
                                        @can('edit-payment')
                                            <button type="button" 
                                                    class="action-btn action-btn-edit" 
                                                    wire:click="openEditPaymentForm({{ $pay->id }})"
                                                    data-tooltip="Edit Voucher Particulars"
                                                    aria-label="Edit Voucher">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                            </button>
                                        @endcan
                                        @can('delete-payment')
                                            <button type="button" 
                                                    class="action-btn action-btn-delete" 
                                                    wire:click="deletePayment({{ $pay->id }})" 
                                                    onclick="confirm('Are you sure you want to permanently delete this billing voucher record?') || event.stopImmediatePropagation()"
                                                    data-tooltip="Delete Voucher Record"
                                                    aria-label="Delete Voucher">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6"/>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                    <line x1="10" y1="11" x2="10" y2="17"/>
                                                    <line x1="14" y1="11" x2="14" y2="17"/>
                                                </svg>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center" style="padding: 4rem 2rem; color: var(--color-text-muted);">
                                    <div style="width: 54px; height: 54px; border-radius: 50%; background: rgba(16,185,129,0.1); color: var(--success); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 0.85rem;">
                                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                                    </div>
                                    <h4 style="font-size: 1.1rem; font-weight: 800; color: var(--color-text-primary); margin: 0 0 0.35rem 0;">No Billing Vouchers Found</h4>
                                    <p style="font-size: 0.85rem; margin: 0;">No financial transactions match your filter criteria.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
