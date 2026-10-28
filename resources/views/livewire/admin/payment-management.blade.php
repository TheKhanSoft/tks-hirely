<div>
    <!-- Console Control Header -->
    <div class="card mb-6" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; position: relative; overflow: hidden;">
        <div style="position: absolute; right: -2rem; top: -2rem; width: 140px; height: 140px; background: radial-gradient(circle, var(--primary-glow) 0%, transparent 70%); pointer-events: none;"></div>
        <div>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.35rem;">
                <div class="brand-icon-box" style="width: 38px; height: 38px; border-radius: 10px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                        <line x1="1" y1="10" x2="23" y2="10"></line>
                    </svg>
                </div>
                <div>
                    <h2 class="card-title" style="margin: 0; font-size: 1.35rem;">Vouchers & Fee Reconciliation</h2>
                    <p class="card-subtitle" style="margin: 0;">Monitor Kuickpay/1Link consumer challans, bank transaction IDs, and settlement ledger</p>
                </div>
            </div>
        </div>
        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <a href="/bank/simulator" target="_blank" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.4rem; text-decoration: none; border-color: var(--secondary-glow); color: var(--secondary);" title="Open Sandbox Bank Simulator">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                <span>1Link Simulator</span>
            </a>
            @if(!$isFormOpen)
                @can('create-payment')
                    <button type="button" class="btn btn-primary" wire:click="openCreatePaymentForm" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>Log Voucher / Payment</span>
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

    <!-- Form Drawer -->
    @if ($isFormOpen)
        <div class="card mb-6" style="border-color: var(--primary-glow); box-shadow: var(--shadow-md);">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                <h3 class="card-title" style="margin: 0; font-size: 1.2rem;">
                    {{ $isEditMode ? 'Edit Processing Voucher & Reconciliation Details' : 'Log Processing Voucher' }}
                </h3>
                <button type="button" class="btn btn-secondary" wire:click="resetForm" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;">Cancel</button>
            </div>
            <form wire:submit.prevent="savePayment">
                @if(!$isEditMode)
                    <div class="grid grid-cols-2 gap-4">
                        <x-select label="Select Active Application *" :error="$errors->first('pay_app_id')">
                            <select class="form-control form-select" wire:model="pay_app_id">
                                <option value="">-- Choose Application --</option>
                                @foreach($availableApplications as $app)
                                    <option value="{{ $app->id }}">{{ $app->user->email }} (Job: {{ $app->campaign->job_code }} - {{ $app->campaign->title }})</option>
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
                        <input type="number" step="0.01" class="form-control" wire:model="pay_amount">
                    </x-input>
                    <x-select label="Voucher Status *" :error="$errors->first('pay_status')">
                        <select class="form-control form-select" wire:model="pay_status">
                            <option value="PENDING">Pending Payment</option>
                            <option value="AUTHORIZED">Authorized</option>
                            <option value="SETTLED">Settled / PAID</option>
                            <option value="EXPIRED">Expired</option>
                        </select>
                    </x-select>
                    <x-input label="Bank Transaction Reference ID" :error="$errors->first('pay_transaction_id')">
                        <input type="text" class="form-control" wire:model="pay_transaction_id" placeholder="TID-123456">
                    </x-input>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <x-input label="Reconciled Timestamp" :error="$errors->first('pay_timestamp')">
                        <input type="datetime-local" class="form-control" wire:model="pay_timestamp">
                    </x-input>
                </div>
                <div class="flex gap-4 mt-6" style="border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                    <button type="submit" class="btn btn-primary">Save Voucher</button>
                    <button type="button" class="btn btn-secondary" wire:click="resetForm">Cancel</button>
                </div>
            </form>
        </div>
    @else
        <!-- Filter Bar -->
        <div class="card mb-4" style="padding: 1rem 1.25rem;">
            <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 250px;">
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Search by Consumer ID, TID, applicant email, or job code...">
                </div>
                <div style="width: 200px;">
                    <select class="form-control form-select" wire:model.live="statusFilter">
                        <option value="">All Voucher Statuses</option>
                        <option value="PENDING">Pending</option>
                        <option value="AUTHORIZED">Authorized</option>
                        <option value="SETTLED">Settled / Paid</option>
                        <option value="EXPIRED">Expired</option>
                    </select>
                </div>
                <div>
                    <span style="font-size: 0.85rem; color: var(--color-text-secondary); font-weight: 600;">
                        Showing {{ count($payments) }} record{{ count($payments) === 1 ? '' : 's' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Ledger Table -->
        <x-table :headers="['Consumer ID', 'Applicant Account', 'Campaign Scope', 'Amount PKR', 'Voucher status', 'Transaction ID', 'Paid Date', 'Action']">
            @forelse($payments as $pay)
                <tr>
                    <td><code style="color: var(--secondary); font-weight: 700;">{{ $pay->consumer_id }}</code></td>
                    <td>
                        <strong style="color: var(--color-text-primary)">{{ $pay->application->user->basicInfo?->first_name ?? '' }} {{ $pay->application->user->basicInfo?->last_name ?? '' }}</strong><br>
                        <small style="color: var(--color-text-secondary)">{{ $pay->application->user->email }}</small>
                    </td>
                    <td><code>{{ $pay->application->campaign->job_code }}</code></td>
                    <td><strong>PKR {{ number_format($pay->amount, 2) }}</strong></td>
                    <td>
                        <span class="badge" style="background: {{ $pay->status === 'SETTLED' ? 'rgba(16,185,129,0.15)' : ($pay->status === 'PENDING' ? 'rgba(245,158,11,0.15)' : 'rgba(239,68,68,0.15)') }}; color: {{ $pay->status === 'SETTLED' ? 'var(--success)' : ($pay->status === 'PENDING' ? 'var(--warning)' : 'var(--danger)') }};">
                            {{ $pay->status }}
                        </span>
                    </td>
                    <td><code>{{ $pay->transaction_id ?: 'N/A' }}</code></td>
                    <td><span style="font-size: 0.85rem; color: var(--color-text-secondary); font-family: monospace;">{{ $pay->payment_timestamp ?: 'Unpaid' }}</span></td>
                    <td>
                        <div class="flex gap-2">
                            @if($pay->status !== 'SETTLED')
                                @if(auth()->user()->can('reconcile-payment') || auth()->user()->can('edit-payment'))
                                    <button type="button" class="btn btn-success" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="markSettled({{ $pay->id }})" title="Quick reconcile this voucher to SETTLED">Reconcile Paid</button>
                                @endif
                            @endif
                            @can('edit-payment')
                                <button type="button" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="openEditPaymentForm({{ $pay->id }})">Edit</button>
                            @endcan
                            @can('delete-payment')
                                <button type="button" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="deletePayment({{ $pay->id }})" onclick="confirm('Are you sure you want to delete this payment record?') || event.stopImmediatePropagation()">Delete</button>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center" style="padding: 2.5rem; color: var(--color-text-secondary);">No billing vouchers found.</td></tr>
            @endforelse
        </x-table>
    @endif
</div>

