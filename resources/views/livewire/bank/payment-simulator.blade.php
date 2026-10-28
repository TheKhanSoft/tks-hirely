<div style="max-width: 600px; margin: 2rem auto;">
    <div class="card" style="border-color: var(--secondary); box-shadow: 0 0 20px var(--secondary-glow);">
        <div class="text-center mb-4">
            <h2 class="card-title" style="color: var(--secondary); font-size: 1.75rem;">🇵🇰 1Link / Kuickpay Switch Simulator</h2>
            <p class="card-subtitle">Local sandbox environment to simulate instant payment webhook reconciliation.</p>
        </div>

        <form wire:submit.prevent="searchVoucher" class="mb-4">
            <div class="form-group">
                <label class="form-label">Enter 12-Digit Consumer Reference ID</label>
                <div class="flex gap-2">
                    <input type="text" class="form-control" wire:model="consumer_id" placeholder="1000xxxxxxxx" style="flex: 1;">
                    <button type="submit" class="btn btn-secondary">Search</button>
                </div>
                @error('consumer_id') <span style="color: var(--danger); font-size: 0.85rem;">{{ $message }}</span> @enderror
            </div>
        </form>

        @if($statusMessage)
            <div class="card mb-4" style="background: rgba(255, 255, 255, 0.02); border-color: {{ $statusType === 'success' ? 'var(--success)' : ($statusType === 'danger' ? 'var(--danger)' : 'var(--border-color)') }};">
                <p style="color: {{ $statusType === 'success' ? '#6ee7b7' : ($statusType === 'danger' ? '#fca5a5' : 'var(--color-text-secondary)') }}; font-weight: 600;">
                    {{ $statusMessage }}
                </p>
            </div>
        @endif

        @if($foundLedger)
            <div style="background: rgba(15, 23, 42, 0.4); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border-color);" class="mb-4">
                <h4 style="color: var(--color-text-primary);" class="mb-4">Invoice details:</h4>
                
                <div class="flex-col gap-2" style="font-size: 0.95rem;">
                    <div><span style="color: var(--color-text-secondary);">Consumer ID:</span> <span style="font-weight: 600; font-family: monospace; color: var(--secondary);">{{ $foundLedger->consumer_id }}</span></div>
                    <div><span style="color: var(--color-text-secondary);">Candidate:</span> <span style="font-weight: 600;">{{ $foundLedger->application->user->basicInfo?->first_name }} {{ $foundLedger->application->user->basicInfo?->last_name }}</span></div>
                    <div><span style="color: var(--color-text-secondary);">Position:</span> <span style="font-weight: 600;">{{ $foundLedger->application->campaign->title }}</span></div>
                    <div><span style="color: var(--color-text-secondary);">Amount:</span> <span style="font-weight: 700; color: var(--warning);">PKR {{ number_format($foundLedger->amount, 2) }}</span></div>
                    <div>
                        <span style="color: var(--color-text-secondary);">Status:</span> 
                        @if($foundLedger->status === 'SETTLED')
                            <span class="badge badge-paid">PAID / SETTLED</span>
                        @else
                            <span class="badge badge-pending">PENDING</span>
                        @endif
                    </div>
                    @if($foundLedger->transaction_id)
                        <div><span style="color: var(--color-text-secondary);">Transaction ID:</span> <span style="font-weight: 600; font-family: monospace;">{{ $foundLedger->transaction_id }}</span></div>
                        <div><span style="color: var(--color-text-secondary);">Paid At:</span> <span style="font-weight: 600;">{{ $foundLedger->payment_timestamp?->format('Y-m-d H:i:s') }}</span></div>
                    @endif
                </div>

                @if($foundLedger->status !== 'SETTLED')
                    <div class="mt-6 text-center">
                        <button type="button" class="btn btn-success" wire:click="simulatePayment" style="width: 100%;">
                            💳 Settle payment (Simulate Webhook Call)
                        </button>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
