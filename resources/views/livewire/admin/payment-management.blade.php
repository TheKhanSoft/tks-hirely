<div>
    <!-- Executive Control Header -->
    <div class="page-hero-header">
        <div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div class="brand-icon-box" style="background: linear-gradient(135deg, rgba(16,185,129,0.25), rgba(5,150,105,0.4)); box-shadow: 0 4px 14px rgba(16,185,129,0.25);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color: #ffffff;">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                        <line x1="1" y1="10" x2="23" y2="10"/>
                    </svg>
                </div>
                <div>
                    <h1>Payments & Fee Vouchers Gateway</h1>
                    <p>Monitor Kuickpay / 1Link consumer challans, bank settlement logs, and instant fee reconciliations.</p>
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <a href="/bank/simulator" target="_blank" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.4rem; text-decoration: none; border-color: var(--secondary-glow); color: var(--secondary); font-weight: 700; padding: 0.6rem 1.15rem; border-radius: 10px;" title="Open Fintech Sandbox">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                <span>1Link Simulator</span>
            </a>
            @if(!$isFormOpen)
                @can('create-payment')
                    <button type="button" class="btn btn-primary" wire:click="openCreatePaymentForm" style="display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 18px -2px rgba(16,185,129,0.5); padding: 0.65rem 1.35rem; font-weight: 700; border-radius: 10px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-color: #059669;">
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
        <div class="stat-kpi-grid">
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

        <!-- Reusable Dynamic Entity Listing Component: Responsive Card Grid & Table -->
        <x-entity-card-grid 
            :items="$payments" 
            entityName="Billing Vouchers" 
            searchPlaceholder="Search by Consumer ID, TID, applicant email, or campaign..."
            :perPage="$perPage" 
            :viewMode="$viewMode" 
            exportAction="exportCsv" 
            importAction="openImportModal">
            
            <!-- Filters Slot -->
            <x-slot:filters>
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
                        <span>Reset Filters</span>
                    </button>
                @endif
            </x-slot:filters>

            <!-- Cards Slot (3 to 5 Cards Per Row) -->
            <x-slot:cards>
                @foreach($payments as $pay)
                    @php
                        $applicantName = ($pay->application?->user?->basicInfo?->first_name ?? '') . ' ' . ($pay->application?->user?->basicInfo?->last_name ?? '');
                        $applicantName = trim($applicantName) ?: ($pay->application?->user?->email ?? 'Applicant #'.$pay->application_id);
                        $avatarTheme = match($pay->status) {
                            'SETTLED' => 'avatar-theme-emerald',
                            'PENDING' => 'avatar-theme-amber',
                            'AUTHORIZED' => 'avatar-theme-cyan',
                            default => 'avatar-theme-rose',
                        };
                    @endphp

                    <x-entity-card 
                        :id="$pay->id" 
                        :title="$applicantName" 
                        :subtitle="$pay->application?->campaign?->title"
                        avatar="PKR"
                        :avatarClass="$avatarTheme">
                        
                        <!-- Badge Slot: Voucher Status -->
                        <x-slot:badge>
                            @if($pay->status === 'SETTLED')
                                <span class="badge badge-emerald" style="font-weight: 800; font-size: 0.72rem; display: inline-flex; align-items: center; gap: 0.35rem;">
                                    <span class="pulse-dot online"></span>
                                    <span>✓ SETTLED</span>
                                </span>
                            @elseif($pay->status === 'PENDING')
                                <span class="badge badge-amber" style="font-weight: 800; font-size: 0.72rem; display: inline-flex; align-items: center; gap: 0.35rem;">
                                    <span class="pulse-dot offline"></span>
                                    <span>⏳ PENDING</span>
                                </span>
                            @elseif($pay->status === 'AUTHORIZED')
                                <span class="badge badge-cyan" style="font-weight: 800; font-size: 0.72rem;">
                                    AUTHORIZED
                                </span>
                            @else
                                <span class="badge badge-rose" style="font-weight: 800; font-size: 0.72rem;">
                                    {{ $pay->status }}
                                </span>
                            @endif
                        </x-slot:badge>

                        <!-- Tag Slot: 1Link Consumer ID -->
                        <x-slot:tag>
                            <span class="mono-pill" style="color: var(--secondary);">
                                1Link: {{ $pay->consumer_id }}
                            </span>
                        </x-slot:tag>

                        <!-- Card Body Metrics -->
                        <div class="entity-card-metric-row">
                            <span class="entity-card-metric-label">Fee Amount:</span>
                            <span class="entity-card-metric-value" style="font-size: 0.95rem; font-weight: 800; color: {{ $pay->status === 'SETTLED' ? 'var(--success)' : 'var(--color-text-primary)' }};">
                                PKR {{ number_format($pay->amount, 2) }}
                            </span>
                        </div>

                        <div class="entity-card-metric-row">
                            <span class="entity-card-metric-label">Target Job:</span>
                            <span class="entity-card-metric-value" style="color: var(--primary); font-family: 'JetBrains Mono', monospace;">
                                {{ $pay->application?->campaign?->job_code ?? 'N/A' }}
                            </span>
                        </div>

                        <div class="entity-card-metric-row">
                            <span class="entity-card-metric-label">Transaction ID:</span>
                            <code class="entity-card-metric-value" style="font-family: 'JetBrains Mono', monospace; font-size: 0.74rem;">
                                {{ $pay->transaction_id ?: 'Unsettled' }}
                            </code>
                        </div>

                        <div class="entity-card-metric-row">
                            <span class="entity-card-metric-label">Settled Timestamp:</span>
                            <span class="entity-card-metric-value" style="font-size: 0.74rem; color: var(--color-text-muted);">
                                {{ $pay->payment_timestamp ? \Carbon\Carbon::parse($pay->payment_timestamp)->format('Y-m-d H:i') : 'Pending' }}
                            </span>
                        </div>

                        <!-- Quick Actions (Hover Reveal Icons) -->
                        <x-slot:quickActions>
                            <button type="button" 
                                    class="action-btn action-btn-view" 
                                    wire:click="viewPaymentDetails({{ $pay->id }})" 
                                    data-tooltip="View Voucher & Reconciliation Dossier" 
                                    aria-label="View Details">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                            @if($pay->status !== 'SETTLED')
                                @if(auth()->user()->can('reconcile-payment') || auth()->user()->can('edit-payment'))
                                    <button type="button" 
                                            class="action-btn action-btn-success" 
                                            wire:click="markSettled({{ $pay->id }})" 
                                            data-tooltip="Reconcile / Mark Settled" 
                                            aria-label="Reconcile Payment">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
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
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </button>
                            @endcan
                        </x-slot:quickActions>

                        <!-- Kebab Dropdown Options Menu [ICON + TEXT] -->
                        <x-slot:menuActions>
                            <x-entity-card-menu-item 
                                label="View Voucher Dossier" 
                                click="viewPaymentDetails({{ $pay->id }})">
                                <x-slot:icon>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </x-slot:icon>
                            </x-entity-card-menu-item>
                            @if($pay->status !== 'SETTLED')
                                @if(auth()->user()->can('reconcile-payment') || auth()->user()->can('edit-payment'))
                                    <x-entity-card-menu-item 
                                        label="Reconcile Payment (Mark Settled)" 
                                        click="markSettled({{ $pay->id }})">
                                        <x-slot:icon>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                        </x-slot:icon>
                                    </x-entity-card-menu-item>
                                @endif
                            @endif

                            @php
                                $isSuperAdmin = auth()->user()?->hasRole('super-admin');
                                $canModifyPayment = $isSuperAdmin || $pay->status !== 'SETTLED';
                            @endphp

                            @can('edit-payment')
                                @if($canModifyPayment)
                                    <x-entity-card-menu-item 
                                        label="Edit Voucher Particulars" 
                                        click="openEditPaymentForm({{ $pay->id }})">
                                        <x-slot:icon>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </x-slot:icon>
                                    </x-entity-card-menu-item>
                                @else
                                    <x-entity-card-menu-item 
                                        :disabled="true" 
                                        label="Edit Voucher (Locked: Settled)" 
                                        tooltip="Settled financial records cannot be modified without Super Admin privileges">
                                        <x-slot:icon>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                        </x-slot:icon>
                                    </x-entity-card-menu-item>
                                @endif
                            @endcan

                            @can('delete-payment')
                                <div class="entity-card-menu-divider"></div>
                                @if($canModifyPayment)
                                    <x-entity-card-menu-item 
                                        label="Delete Billing Record" 
                                        click="deletePayment({{ $pay->id }})" 
                                        :danger="true"
                                        onclick="confirm('Are you sure you want to permanently delete this billing voucher record?') || event.stopImmediatePropagation()">
                                        <x-slot:icon>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                        </x-slot:icon>
                                    </x-entity-card-menu-item>
                                @else
                                    <x-entity-card-menu-item 
                                        :disabled="true" 
                                        label="Delete Record (Locked: Settled)" 
                                        tooltip="Settled financial records cannot be deleted without Super Admin privileges">
                                        <x-slot:icon>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                        </x-slot:icon>
                                    </x-entity-card-menu-item>
                                @endif
                            @endcan
                        </x-slot:menuActions>
                    </x-entity-card>
                @endforeach
            </x-slot:cards>

            <!-- Table Slot (For View Switcher) -->
            <x-slot:table>
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
                                    <div class="action-btn-group justify-end" x-data="{ openMenu: false }">
                                        <button type="button" 
                                                class="action-btn action-btn-view" 
                                                wire:click="viewPaymentDetails({{ $pay->id }})"
                                                data-tooltip="View Voucher Ledger Details"
                                                aria-label="View Details">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        </button>
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

                                        <!-- 3-Dot More Actions Dropdown -->
                                        <div class="table-action-dropdown-wrap" @click.outside="openMenu = false">
                                            <button type="button" 
                                                    class="action-btn" 
                                                    @click="openMenu = !openMenu" 
                                                    data-tooltip="More Actions" 
                                                    aria-label="More Actions">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                                                    <circle cx="12" cy="5" r="1.5"/>
                                                    <circle cx="12" cy="12" r="1.5"/>
                                                    <circle cx="12" cy="19" r="1.5"/>
                                                </svg>
                                            </button>
                                            <div class="table-action-dropdown" x-show="openMenu" x-cloak style="display: none;" @click="openMenu = false">
                                                @php
                                                    $isSuperAdmin = auth()->user()?->hasRole('super-admin');
                                                    $canModifyPayment = $isSuperAdmin || $pay->status !== 'SETTLED';
                                                @endphp

                                                @can('edit-payment')
                                                    @if($canModifyPayment)
                                                        <button type="button" 
                                                                class="table-action-menu-item" 
                                                                wire:click="openEditPaymentForm({{ $pay->id }})">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                            </svg>
                                                            <span>Edit Voucher Particulars</span>
                                                        </button>
                                                    @else
                                                        <button type="button" 
                                                                class="table-action-menu-item disabled" 
                                                                disabled
                                                                title="Settled financial records cannot be modified without Super Admin privileges">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                                            </svg>
                                                            <span>Edit Voucher (Locked: Settled)</span>
                                                        </button>
                                                    @endif
                                                @endcan
                                                @can('delete-payment')
                                                    <div class="table-action-menu-divider"></div>
                                                    @if($canModifyPayment)
                                                        <button type="button" 
                                                                class="table-action-menu-item danger" 
                                                                wire:click="deletePayment({{ $pay->id }})" 
                                                                onclick="confirm('Are you sure you want to permanently delete this billing voucher record?') || event.stopImmediatePropagation()">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                                <polyline points="3 6 5 6 21 6"/>
                                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                                <line x1="10" y1="11" x2="10" y2="17"/>
                                                                <line x1="14" y1="11" x2="14" y2="17"/>
                                                            </svg>
                                                            <span>Delete Voucher Record</span>
                                                        </button>
                                                    @else
                                                        <button type="button" 
                                                                class="table-action-menu-item disabled" 
                                                                disabled
                                                                title="Settled financial records cannot be deleted without Super Admin privileges">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                                            </svg>
                                                            <span>Delete Record (Locked: Settled)</span>
                                                        </button>
                                                    @endif
                                                @endcan
                                            </div>
                                        </div>
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
            </x-slot:table>
        </x-entity-card-grid>

        <!-- Universal Import Modal for Vouchers -->
        <x-entity-import-modal 
            :isOpen="$isImportModalOpen" 
            entityName="Billing Vouchers" 
            :sampleColumns="['consumer_id', 'amount', 'status', 'transaction_id']" />

        <!-- Payment Voucher & Reconciliation Details Modal -->
        <x-modal :show="$isDetailsOpen" title="Billing Voucher & Reconciliation Ledger" closeAction="closeDetailsModal">
            @if($selectedPayment)
                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(255, 255, 255, 0.03); border: 1px solid var(--border-color); padding: 1rem 1.25rem; border-radius: var(--radius-md);">
                        <div>
                            <div style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-text-muted); font-weight: 700;">1Link Consumer ID</div>
                            <div style="font-family: 'JetBrains Mono', monospace; font-size: 1.2rem; font-weight: 800; color: var(--secondary); margin-top: 2px;">
                                {{ $selectedPayment->consumer_id }}
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-text-muted); font-weight: 700;">Settlement Status</div>
                            <div style="margin-top: 4px;">
                                @if($selectedPayment->status === 'SETTLED')
                                    <span class="badge badge-success"><span class="badge-dot" style="background: currentColor;"></span> Settled</span>
                                @elseif($selectedPayment->status === 'PENDING')
                                    <span class="badge badge-warning"><span class="badge-dot" style="background: currentColor;"></span> Pending Payment</span>
                                @else
                                    <span class="badge badge-danger"><span class="badge-dot" style="background: currentColor;"></span> {{ $selectedPayment->status }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                        <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-color); padding: 0.85rem 1rem; border-radius: var(--radius-md);">
                            <span style="font-size: 0.75rem; color: var(--color-text-muted); display: block; text-transform: uppercase; letter-spacing: 0.04em;">Applicant Candidate</span>
                            <strong style="color: var(--color-text-primary); font-size: 0.95rem; display: block; margin-top: 3px;">
                                {{ $selectedPayment->application->user->basicInfo?->first_name ?? '' }} {{ $selectedPayment->application->user->basicInfo?->last_name ?? '' }}
                            </strong>
                            <span style="font-size: 0.8rem; color: var(--color-text-secondary);">{{ $selectedPayment->application->user->email }}</span>
                        </div>

                        <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-color); padding: 0.85rem 1rem; border-radius: var(--radius-md);">
                            <span style="font-size: 0.75rem; color: var(--color-text-muted); display: block; text-transform: uppercase; letter-spacing: 0.04em;">Target Vacancy</span>
                            <strong style="color: var(--color-text-primary); font-size: 0.95rem; display: block; margin-top: 3px;">
                                {{ $selectedPayment->application->campaign->title }}
                            </strong>
                            <span style="font-size: 0.8rem; font-family: 'JetBrains Mono', monospace; color: var(--primary);">Job Code: {{ $selectedPayment->application->campaign->job_code }}</span>
                        </div>

                        <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-color); padding: 0.85rem 1rem; border-radius: var(--radius-md);">
                            <span style="font-size: 0.75rem; color: var(--color-text-muted); display: block; text-transform: uppercase; letter-spacing: 0.04em;">Challan Amount</span>
                            <strong style="color: {{ $selectedPayment->status === 'SETTLED' ? 'var(--success)' : 'var(--color-text-primary)' }}; font-size: 1.15rem; font-family: 'JetBrains Mono', monospace; display: block; margin-top: 3px;">
                                PKR {{ number_format($selectedPayment->amount, 2) }}
                            </strong>
                        </div>

                        <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-color); padding: 0.85rem 1rem; border-radius: var(--radius-md);">
                            <span style="font-size: 0.75rem; color: var(--color-text-muted); display: block; text-transform: uppercase; letter-spacing: 0.04em;">Bank Transaction ID / Ref</span>
                            <strong style="color: var(--color-text-primary); font-size: 0.95rem; font-family: 'JetBrains Mono', monospace; display: block; margin-top: 3px;">
                                {{ $selectedPayment->transaction_id ?: 'Awaiting Settlement Gateway' }}
                            </strong>
                        </div>
                    </div>

                    <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-color); padding: 0.85rem 1rem; border-radius: var(--radius-md); display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <span style="font-size: 0.75rem; color: var(--color-text-muted); display: block; text-transform: uppercase; letter-spacing: 0.04em;">Payment Settlement Timestamp</span>
                            <strong style="color: var(--color-text-primary); font-size: 0.88rem;">
                                {{ $selectedPayment->payment_timestamp ? \Carbon\Carbon::parse($selectedPayment->payment_timestamp)->format('F d, Y - h:i A') : 'Not yet reconciled' }}
                            </strong>
                        </div>
                        <div>
                            <span style="font-size: 0.75rem; color: var(--color-text-muted); display: block; text-transform: uppercase; letter-spacing: 0.04em;">Voucher Created</span>
                            <strong style="color: var(--color-text-primary); font-size: 0.88rem;">
                                {{ $selectedPayment->created_at->format('F d, Y - h:i A') }}
                            </strong>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem;">
                        @if($selectedPayment->status !== 'SETTLED')
                            @if(auth()->user()->can('reconcile-payment') || auth()->user()->can('edit-payment'))
                                <button type="button" class="btn btn-primary" wire:click="markSettled({{ $selectedPayment->id }}); closeDetailsModal();">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    <span>Reconcile (Mark Settled)</span>
                                </button>
                            @endif
                        @endif
                        <button type="button" class="btn btn-secondary" wire:click="closeDetailsModal">Close</button>
                    </div>
                </div>
            @endif
        </x-modal>
    @endif
</div>
