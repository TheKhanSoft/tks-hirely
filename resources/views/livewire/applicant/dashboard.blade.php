<div>
    <!-- Welcome Header Card -->
    <div class="card mb-6" style="background: var(--bg-card); border-color: var(--secondary-glow); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);">
        <div class="flex justify-between align-center">
            <div>
                <h2 class="card-title" style="color: var(--color-text-primary); font-size: 1.75rem; font-weight: 800; letter-spacing: -0.5px;">
                    👤 Candidate Portal
                </h2>
                <p class="card-subtitle" style="margin-bottom: 0;">Welcome, <strong>{{ $basicInfo ? trim($basicInfo->first_name . ' ' . $basicInfo->last_name) : auth()->user()->email }}</strong>. Track applications and invoices here.</p>
            </div>
            
            <div style="background: var(--bg-btn-secondary); border: 1px solid var(--border-color); padding: 0.5rem 1rem; border-radius: 8px;" class="flex align-center gap-2">
                <span class="badge badge-paid" style="width: 8px; height: 8px; padding: 0; border-radius: 50%; display: inline-block;"></span>
                <span style="font-size: 0.85rem; font-weight: 700; color: var(--color-text-secondary); text-transform: uppercase;">
                    Candidate Session
                </span>
            </div>
        </div>
    </div>

    @if(!$basicInfo)
        <div class="card text-center mb-6" style="border: 2px dashed var(--border-color); padding: 4rem;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🚀</div>
            <h3 class="card-title mb-2">Build Your Candidate basicInfo</h3>
            <p style="color: var(--color-text-secondary); font-size: 1rem;" class="mb-6">
                You haven't completed your basicInfo or applied for any positions yet. Complete the wizard to generate your bill.
            </p>
            <a href="/applicant/wizard" class="btn btn-primary btn-lg" style="border-radius: var(--radius-sm);">
                📋 Launch basicInfo Wizard
            </a>
        </div>
    @else
        <!-- Grid basicInfo Status -->
        <div class="grid grid-cols-3 gap-6 mb-6">
            <div class="card">
                <h4 style="color: var(--secondary); font-weight: 700; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px;" class="mb-2">basicInfo Locking Status</h4>
                @if($basicInfo->is_locked)
                    <span class="badge badge-paid" style="font-size: 0.8rem; font-weight: 700;">🔒 LOCKED & SUBMITTED</span>
                    <p style="font-size: 0.85rem; color: var(--color-text-secondary); margin-top: 0.75rem;">Credential fields frozen for administrative scrutiny.</p>
                @else
                    <span class="badge badge-pending" style="font-size: 0.8rem; font-weight: 700;">🔓 OPEN FOR EDITS</span>
                    <p style="font-size: 0.85rem; color: var(--color-text-secondary); margin-top: 0.75rem;">You can update your degrees and experience logs.</p>
                @endif
            </div>
            
            <div class="card">
                <h4 style="color: var(--secondary); font-weight: 700; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px;" class="mb-2">Assigned Evaluation Track</h4>
                <span class="badge badge-appealed" style="font-size: 0.85rem; font-weight: 700;">{{ $basicInfo->track_type }} TRACK</span>
                <p style="font-size: 0.85rem; color: var(--color-text-secondary); margin-top: 0.75rem;">Form checklist customized to this career track.</p>
            </div>

            <div class="card">
                <h4 style="color: var(--secondary); font-weight: 700; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px;" class="mb-2">CNIC Verification Reference</h4>
                <p style="font-family: monospace; font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary); margin-top: 0.25rem; letter-spacing: 1px;">
                    {{ $basicInfo->cnic_number }}
                </p>
                <p style="font-size: 0.85rem; color: var(--color-text-secondary); margin-top: 0.75rem;">Verified database unique key.</p>
            </div>
        </div>

        <!-- Application table -->
        <div class="card" style="border-color: var(--border-color);">
            <h3 class="card-title mb-4" style="color: var(--color-text-primary);">💼 Active Applications & Invoices</h3>
            
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Job Campaign</th>
                            <th>Department Scope</th>
                            <th>Applied Date</th>
                            <th>1Link Reference</th>
                            <th>Voucher Status</th>
                            <th>Scrutiny Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $app)
                            <tr>
                                <td>
                                    <strong style="color: var(--color-text-primary); font-size: 1rem;">{{ $app->campaign->title }}</strong> <br>
                                    <small style="color: var(--color-text-secondary);">Campaign Job Code: <code style="color: var(--secondary);">{{ $app->campaign->job_code }}</code></small>
                                </td>
                                <td>{{ $app->campaign->department }}</td>
                                <td>{{ $app->submission_timestamp?->format('Y-m-d H:i') ?: 'Draft' }}</td>
                                <td>
                                    @if($app->billingLedger)
                                        <code style="color: var(--secondary); font-weight: 800; font-size: 1.05rem;">{{ $app->billingLedger->consumer_id }}</code>
                                    @else
                                        <span style="color: var(--color-text-muted);">Not Generated</span>
                                    @endif
                                </td>
                                <td>
                                    @if($app->status === 'DRAFT')
                                        <span class="badge badge-draft">Draft</span>
                                    @elseif($app->status === 'SUBMITTED')
                                        <span class="badge badge-pending">PENDING PAYMENT</span>
                                    @elseif($app->status === 'PAID')
                                        <span class="badge badge-paid">PAID / IN SCRUTINY</span>
                                    @elseif($app->status === 'ELIGIBLE')
                                        <span class="badge badge-eligible">✓ ELIGIBLE</span>
                                    @elseif($app->status === 'INELIGIBLE')
                                        <span class="badge badge-ineligible">✗ DISQUALIFIED</span>
                                    @elseif($app->status === 'APPEALED')
                                        <span class="badge badge-appealed">APPEAL LODGED</span>
                                    @elseif($app->status === 'REJECTED')
                                        <span class="badge badge-rejected">APPEAL DISMISSED</span>
                                    @elseif($app->status === 'APPROVED')
                                        <span class="badge badge-paid">APPROVED</span>
                                    @endif
                                </td>
                                <td>
                                    @if($app->status === 'SUBMITTED')
                                        <a href="/applicant/wizard" class="btn btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; border-radius: 4px;">View Challan</a>
                                    @elseif($app->status === 'INELIGIBLE')
                                        <div class="flex-col gap-2" style="background: rgba(239,68,68,0.05); padding: 0.75rem; border-radius: 6px; border: 1px solid var(--danger-glow);">
                                            <p style="color: #fca5a5; font-size: 0.85rem; max-width: 250px;">
                                                <strong>Reason:</strong> {{ $app->disqualification_reason }}
                                            </p>
                                            <button type="button" class="btn btn-danger" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; border-radius: 4px;" wire:click="openAppealForm({{ $app->id }})">
                                                ⚖️ Lodge Appeal (7 Days)
                                            </button>
                                        </div>
                                    @elseif($app->status === 'APPEALED')
                                        <div style="background: rgba(6,182,212,0.05); padding: 0.75rem; border-radius: 6px; border: 1px solid var(--secondary-glow);">
                                            <p style="color: #8be5f2; font-size: 0.85rem;">Grievance under review by Appellate Committee.</p>
                                        </div>
                                    @else
                                        <span style="color: var(--color-text-muted); font-size: 0.85rem;">No action required.</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center" style="color: var(--color-text-muted);">No applications registered. <a href="/applicant/wizard" style="color: var(--secondary);">Launch basicInfo wizard</a>.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Appeal Form Modal Drawer -->
    @if($showAppealForm)
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 1000;">
            <div class="card" style="width: 100%; max-width: 600px; border-color: var(--danger); box-shadow: 0 0 35px var(--danger-glow);">
                <div class="flex justify-between align-center mb-4" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                    <h3 class="card-title" style="color: var(--danger); margin: 0;">⚖️ Lodge Formal Appeal</h3>
                    <button type="button" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="$set('showAppealForm', false)">Close</button>
                </div>
                
                <form wire:submit.prevent="submitAppeal">
                    <div class="form-group mb-4">
                        <label class="form-label">Explain your case / Provide justification (Min 20 chars)</label>
                        <textarea class="form-control" rows="5" wire:model="appealText" placeholder="Specify equivalence or credentials reference logs..."></textarea>
                        @error('appealText') <span style="color: var(--danger); font-size: 0.85rem;">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group mb-6">
                        <label class="form-label">Upload Equivalence / Proof Document (PDF/Image)</label>
                        <input type="file" class="form-control" wire:model="appealProof">
                        @error('appealProof') <span style="color: var(--danger); font-size: 0.85rem;">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="btn btn-danger" style="width: 100%; padding: 0.75rem; border-radius: var(--radius-sm); font-size: 1rem;">
                        ⚖️ Submit Formal Appeal
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>
