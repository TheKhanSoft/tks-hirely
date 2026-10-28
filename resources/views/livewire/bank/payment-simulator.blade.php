<div style="max-width: 680px; margin: 2rem auto;">
    <div class="card" style="border-color: var(--secondary); box-shadow: 0 10px 40px -10px var(--secondary-glow); overflow: hidden; position: relative;">
        <!-- Top Tech Pattern Banner -->
        <div style="background: linear-gradient(135deg, var(--bg-surface), rgba(6, 182, 212, 0.15)); padding: 1.75rem 2rem; border-bottom: 1px solid var(--border-color); text-align: center; position: relative;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 44px; height: 44px; border-radius: 12px; background: var(--secondary-light); color: var(--secondary); margin-bottom: 0.75rem; box-shadow: 0 4px 16px var(--secondary-glow);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="20" height="14" x="2" y="5" rx="2"/>
                    <line x1="2" x2="22" y1="10" y2="10"/>
                </svg>
            </div>
            <h2 class="card-title justify-center" style="font-size: 1.5rem; color: var(--color-text-primary); margin-bottom: 0.25rem;">
                1Link / Kuickpay Switch Sandbox
            </h2>
            <p class="card-subtitle" style="font-size: 0.85rem; max-width: 440px; margin: 0 auto;">
                Bank payment switch simulator to verify instant billing ledger settlement and webhook reconciliation.
            </p>
            <div style="margin-top: 0.75rem;">
                <span class="badge badge-appealed" style="font-size: 0.68rem; font-weight: 800;">
                    🇵🇰 State Bank of Pakistan 1Link Switch Protocol
                </span>
            </div>
        </div>

        <div style="padding: 2rem;">
            <!-- Search Form -->
            <form wire:submit.prevent="searchVoucher" class="mb-4">
                <div class="form-group">
                    <label class="form-label" style="font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.04em;">
                        Enter 12-Digit Consumer Reference Number
                    </label>
                    <div class="flex gap-2">
                        <div style="position: relative; flex: 1;">
                            <input type="text" class="form-control" wire:model="consumer_id" placeholder="e.g. 1000xxxxxxxx" style="font-family: 'JetBrains Mono', monospace; font-size: 1.05rem; font-weight: 700; letter-spacing: 1px; padding-left: 2.5rem;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-text-muted)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); pointer-events: none;">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="m21 21-4.3-4.3"/>
                            </svg>
                        </div>
                        <button type="submit" class="btn btn-primary" style="padding: 0.7rem 1.5rem;">
                            <span>Search</span>
                        </button>
                    </div>
                    @error('consumer_id') <span style="color: var(--danger); font-size: 0.82rem; margin-top: 0.35rem; display: block; font-weight: 500;">{{ $message }}</span> @enderror
                </div>
            </form>

            @if($statusMessage)
                <div class="card mb-4" style="background: rgba(255, 255, 255, 0.02); border-color: {{ $statusType === 'success' ? 'var(--success)' : ($statusType === 'danger' ? 'var(--danger)' : 'var(--border-color)') }};">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        @if($statusType === 'success')
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                        @else
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--danger)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                        @endif
                        <p style="color: {{ $statusType === 'success' ? '#6ee7b7' : ($statusType === 'danger' ? '#fca5a5' : 'var(--color-text-secondary)') }}; font-weight: 600; margin: 0; font-size: 0.92rem;">
                            {{ $statusMessage }}
                        </p>
                    </div>
                </div>
            @endif

            @if($foundLedger)
                <!-- Digital Receipt Slip Preview -->
                <div style="background: var(--bg-card-solid); border: 1px solid var(--border-color); border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-md);">
                    <div style="padding: 1rem 1.5rem; background: var(--bg-table-th); border-bottom: 1px dashed var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-muted);">
                            Voucher Dossier Found
                        </span>
                        @if($foundLedger->status === 'SETTLED')
                            <span class="badge badge-paid">
                                <span class="badge-dot" style="background: var(--success);"></span>
                                SETTLED / PAID
                            </span>
                        @else
                            <span class="badge badge-pending">
                                <span class="badge-dot" style="background: var(--warning);"></span>
                                PENDING PAYMENT
                            </span>
                        @endif
                    </div>
                    
                    <div style="padding: 1.5rem;" class="flex-col gap-3">
                        <div class="flex justify-between align-center" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.6rem;">
                            <span style="color: var(--color-text-secondary); font-size: 0.88rem;">Consumer ID:</span>
                            <span style="font-weight: 800; font-family: 'JetBrains Mono', monospace; color: var(--secondary); font-size: 1.1rem;">
                                {{ $foundLedger->consumer_id }}
                            </span>
                        </div>
                        <div class="flex justify-between align-center" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.6rem;">
                            <span style="color: var(--color-text-secondary); font-size: 0.88rem;">Candidate:</span>
                            <span style="font-weight: 700; color: var(--color-text-primary);">
                                {{ $foundLedger->application->user->basicInfo?->first_name }} {{ $foundLedger->application->user->basicInfo?->last_name }}
                            </span>
                        </div>
                        <div class="flex justify-between align-center" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.6rem;">
                            <span style="color: var(--color-text-secondary); font-size: 0.88rem;">Position:</span>
                            <span style="font-weight: 600; color: var(--color-text-primary); text-align: right; max-width: 320px;">
                                {{ $foundLedger->application->campaign->title }}
                            </span>
                        </div>
                        <div class="flex justify-between align-center" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.6rem;">
                            <span style="color: var(--color-text-secondary); font-size: 0.88rem;">Voucher Fee:</span>
                            <span style="font-weight: 800; color: var(--warning); font-size: 1.25rem;">
                                PKR {{ number_format($foundLedger->amount, 2) }}
                            </span>
                        </div>

                        @if($foundLedger->transaction_id)
                            <div class="flex justify-between align-center" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.6rem;">
                                <span style="color: var(--color-text-secondary); font-size: 0.88rem;">Transaction ID:</span>
                                <span style="font-weight: 600; font-family: 'JetBrains Mono', monospace; font-size: 0.88rem; color: var(--color-text-primary);">
                                    {{ $foundLedger->transaction_id }}
                                </span>
                            </div>
                            <div class="flex justify-between align-center">
                                <span style="color: var(--color-text-secondary); font-size: 0.88rem;">Reconciled At:</span>
                                <span style="font-weight: 600; color: var(--color-text-primary); font-size: 0.88rem;">
                                    {{ $foundLedger->payment_timestamp?->format('Y-m-d H:i:s') }}
                                </span>
                            </div>
                        @endif
                    </div>

                    @if($foundLedger->status !== 'SETTLED')
                        <div style="padding: 1.25rem 1.5rem; background: var(--bg-table-th); border-top: 1px solid var(--border-color);">
                            <button type="button" class="btn btn-success" wire:click="simulatePayment" wire:loading.attr="disabled" style="width: 100%; padding: 0.85rem; font-size: 1rem; border-radius: var(--radius-sm);">
                                <span wire:loading.remove wire:target="simulatePayment" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"/>
                                    </svg>
                                    <span>Execute 1Link Settle Webhook (Simulate Payment)</span>
                                </span>
                                <span wire:loading wire:target="simulatePayment" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="animation: spin 1s linear infinite;">
                                        <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                                    </svg>
                                    <span>Reconciling with Bank Switch...</span>
                                </span>
                            </button>
                        </div>
                    @else
                        <div style="padding: 1.25rem 1.5rem; background: var(--success-light); border-top: 1px solid var(--success-glow); text-align: center;">
                            <span style="color: #6ee7b7; font-weight: 700; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 0.5rem;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                Challan Fully Settled & Reconciled
                            </span>
                        </div>
                    @endif
                </div>

                <div class="flex justify-between align-center mt-4">
                    <a href="/applicant/dashboard" class="btn btn-secondary btn-sm">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="19" x2="5" y1="12" y2="12"/>
                            <polyline points="12 19 5 12 12 5"/>
                        </svg>
                        <span>Return to Candidate Dashboard</span>
                    </a>

                    <a href="/applicant/wizard" class="btn btn-secondary btn-sm">
                        <span>Go to Dossier Wizard</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" x2="19" y1="12" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
