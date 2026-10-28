<div>
    <!-- Welcome Header Card -->
    <div class="card mb-6" style="background: var(--bg-card); border-color: var(--border-color); box-shadow: var(--shadow-md);">
        <div class="flex justify-between align-center" style="flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1 class="card-title" style="font-size: 1.6rem; font-weight: 800; letter-spacing: -0.03em;">
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; background: var(--secondary-light); color: var(--secondary); margin-right: 0.5rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </span>
                    Candidate Portal
                </h1>
                <p class="card-subtitle">
                    Welcome back, <strong style="color: var(--color-text-primary);">{{ $basicInfo ? trim($basicInfo->first_name . ' ' . $basicInfo->last_name) : auth()->user()->email }}</strong>. Track your job submissions and payment vouchers.
                </p>
            </div>
            
            <div class="flex align-center gap-3">
                <a href="/applicant/wizard" class="btn btn-primary btn-sm">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                        <path d="M15 2H9a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1Z"/>
                        <path d="m9 14 2 2 4-4"/>
                    </svg>
                    <span>Application Wizard</span>
                </a>

                <div style="background: var(--bg-btn-secondary); border: 1px solid var(--border-color); padding: 0.45rem 0.9rem; border-radius: var(--radius-sm);" class="flex align-center gap-2">
                    <span class="badge-dot" style="background: var(--success); box-shadow: 0 0 6px var(--success);"></span>
                    <span style="font-size: 0.78rem; font-weight: 700; color: var(--color-text-secondary); text-transform: uppercase; letter-spacing: 0.04em;">
                        Candidate Active
                    </span>
                </div>
            </div>
        </div>
    </div>

    @if(!$basicInfo)
        <!-- Empty State Hero -->
        <div class="card text-center mb-6" style="border: 2px dashed var(--border-hover); padding: 4.5rem 2rem;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                    <path d="M15 2H9a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1Z"/>
                    <path d="M12 11h4"/>
                    <path d="M12 16h4"/>
                    <path d="M8 11h.01"/>
                    <path d="M8 16h.01"/>
                </svg>
            </div>
            <h3 class="card-title justify-center mb-2" style="font-size: 1.4rem;">Build Your Candidate Profile</h3>
            <p style="color: var(--color-text-secondary); font-size: 0.95rem; max-width: 500px; margin: 0 auto 1.75rem;">
                You haven't completed your candidate profile or applied for any recruitment campaigns yet. Launch the wizard to prepare your dossier and generate your fee voucher.
            </p>
            <a href="/applicant/wizard" class="btn btn-primary btn-lg">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"/>
                    <path d="m12 5 7 7-7 7"/>
                </svg>
                <span>Launch Profile Wizard</span>
            </a>
        </div>
    @else
        <!-- Grid Status Bento Cards -->
        <div class="grid grid-cols-3 gap-6 mb-6">
            <!-- Profile Locking Status -->
            <div class="metric-card" style="--card-accent: {{ $basicInfo->is_locked ? 'var(--success)' : 'var(--warning)' }};">
                <div class="metric-header">
                    <span class="metric-label">Profile Lock Status</span>
                    <div class="metric-icon-wrap" style="background: {{ $basicInfo->is_locked ? 'var(--success-light)' : 'var(--warning-light)' }}; color: {{ $basicInfo->is_locked ? 'var(--success)' : 'var(--warning)' }};">
                        @if($basicInfo->is_locked)
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        @else
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 9.9-1"/>
                            </svg>
                        @endif
                    </div>
                </div>
                <div>
                    @if($basicInfo->is_locked)
                        <span class="badge badge-paid" style="font-size: 0.8rem;">
                            <span class="badge-dot" style="background: var(--success);"></span>
                            Locked & Submitted
                        </span>
                        <p class="metric-subtext" style="margin-top: 0.5rem;">Fields frozen for administrative scrutiny.</p>
                    @else
                        <span class="badge badge-pending" style="font-size: 0.8rem;">
                            <span class="badge-dot" style="background: var(--warning);"></span>
                            Open for Edits
                        </span>
                        <p class="metric-subtext" style="margin-top: 0.5rem;">You can update qualifications and records.</p>
                    @endif
                </div>
            </div>
            
            <!-- Assigned Evaluation Track -->
            <div class="metric-card" style="--card-accent: var(--secondary);">
                <div class="metric-header">
                    <span class="metric-label">Evaluation Track</span>
                    <div class="metric-icon-wrap" style="background: var(--secondary-light); color: var(--secondary);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <span class="badge badge-appealed" style="font-size: 0.85rem; font-weight: 800;">
                        {{ $basicInfo->track_type }} TRACK
                    </span>
                    <p class="metric-subtext" style="margin-top: 0.5rem;">HEC / University criteria calibrated for this track.</p>
                </div>
            </div>

            <!-- CNIC Reference -->
            <div class="metric-card" style="--card-accent: var(--primary);">
                <div class="metric-header">
                    <span class="metric-label">Verified CNIC Number</span>
                    <div class="metric-icon-wrap" style="background: var(--primary-light); color: var(--primary);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="14" x="2" y="5" rx="2"/>
                            <circle cx="8" cy="12" r="2"/>
                            <path d="M14 10h4"/>
                            <path d="M14 14h2"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary); letter-spacing: 1px;">
                        {{ $basicInfo->cnic_number }}
                    </div>
                    <p class="metric-subtext" style="margin-top: 0.35rem;">Unique identity verification key.</p>
                </div>
            </div>
        </div>

        <!-- Application table -->
        <div class="card" style="border-color: var(--border-color);">
            <div class="flex justify-between align-center mb-4">
                <h3 class="card-title" style="font-size: 1.1rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="14" x="2" y="7" rx="2" ry="2"/>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                    </svg>
                    Active Applications & Invoices
                </h3>
            </div>
            
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Job Campaign</th>
                            <th>Department</th>
                            <th>Applied Date</th>
                            <th>1Link Consumer ID</th>
                            <th>Status</th>
                            <th>Actions & Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $app)
                            <tr>
                                <td>
                                    <strong style="color: var(--color-text-primary); font-size: 0.95rem;">{{ $app->campaign->title }}</strong> <br>
                                    <small style="color: var(--color-text-secondary);">Job Code: <code style="color: var(--secondary); font-family: 'JetBrains Mono', monospace; font-weight: 700;">{{ $app->campaign->job_code }}</code></small>
                                </td>
                                <td>
                                    <span style="color: var(--color-text-secondary); font-size: 0.88rem;">{{ $app->campaign->department }}</span>
                                </td>
                                <td>
                                    <span style="font-size: 0.85rem; color: var(--color-text-muted);">
                                        {{ $app->submission_timestamp?->format('Y-m-d H:i') ?: 'Draft' }}
                                    </span>
                                </td>
                                <td>
                                    @if($app->billingLedger)
                                        <div style="display: flex; align-items: center; gap: 0.4rem;">
                                            <code style="color: var(--secondary); font-family: 'JetBrains Mono', monospace; font-weight: 800; font-size: 0.95rem; background: var(--secondary-light); padding: 0.2rem 0.5rem; border-radius: 4px;">
                                                {{ $app->billingLedger->consumer_id }}
                                            </code>
                                        </div>
                                    @else
                                        <span style="color: var(--color-text-muted); font-size: 0.85rem;">Not Generated</span>
                                    @endif
                                </td>
                                <td>
                                    @if($app->status === 'DRAFT')
                                        <span class="badge badge-draft">Draft</span>
                                    @elseif($app->status === 'SUBMITTED')
                                        <span class="badge badge-pending">
                                            <span class="badge-dot" style="background: var(--warning);"></span>
                                            Pending Payment
                                        </span>
                                    @elseif($app->status === 'PAID')
                                        <span class="badge badge-paid">
                                            <span class="badge-dot" style="background: var(--success);"></span>
                                            Paid / In Scrutiny
                                        </span>
                                    @elseif($app->status === 'ELIGIBLE')
                                        <span class="badge badge-eligible">
                                            <span class="badge-dot" style="background: var(--success);"></span>
                                            Eligible
                                        </span>
                                    @elseif($app->status === 'INELIGIBLE')
                                        <span class="badge badge-ineligible">
                                            <span class="badge-dot" style="background: var(--danger);"></span>
                                            Disqualified
                                        </span>
                                    @elseif($app->status === 'APPEALED')
                                        <span class="badge badge-appealed">Appeal Lodged</span>
                                    @elseif($app->status === 'REJECTED')
                                        <span class="badge badge-rejected">Appeal Dismissed</span>
                                    @elseif($app->status === 'APPROVED')
                                        <span class="badge badge-paid">Approved</span>
                                    @endif
                                </td>
                                <td>
                                    @if($app->status === 'SUBMITTED')
                                        <div class="flex gap-2 align-center">
                                            <a href="/applicant/wizard" class="btn btn-secondary btn-sm">View Challan</a>
                                            @if($app->billingLedger)
                                                <a href="/bank/simulator?consumer_id={{ $app->billingLedger->consumer_id }}" class="btn btn-primary btn-sm" style="font-size: 0.75rem;">Pay Sim</a>
                                            @endif
                                        </div>
                                    @elseif($app->status === 'INELIGIBLE')
                                        <div class="flex-col gap-2" style="background: var(--danger-light); padding: 0.65rem 0.85rem; border-radius: var(--radius-sm); border: 1px solid var(--danger-glow);">
                                            <p style="color: #fca5a5; font-size: 0.82rem; max-width: 250px;">
                                                <strong>Reason:</strong> {{ $app->disqualification_reason }}
                                            </p>
                                            <button type="button" class="btn btn-danger btn-sm" wire:click="openAppealForm({{ $app->id }})">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                                                    <path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                                                    <path d="M7 21h10"/>
                                                    <path d="M12 3v18"/>
                                                </svg>
                                                <span>Lodge Appeal (7 Days)</span>
                                            </button>
                                        </div>
                                    @elseif($app->status === 'APPEALED')
                                        <div style="background: var(--secondary-light); padding: 0.5rem 0.75rem; border-radius: var(--radius-sm); border: 1px solid var(--secondary-glow);">
                                            <p style="color: #67e8f9; font-size: 0.82rem;">Under Review by Appellate Panel</p>
                                        </div>
                                    @else
                                        <span style="color: var(--color-text-muted); font-size: 0.85rem;">No action required</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center" style="color: var(--color-text-muted); padding: 2.5rem;">
                                    No applications registered yet. <a href="/applicant/wizard" style="color: var(--secondary); text-decoration: underline;">Launch profile wizard</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Appeal Form Modal Drawer -->
    @if($showAppealForm)
        <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 1.5rem;">
            <div class="card" style="width: 100%; max-width: 580px; border-color: var(--border-color); box-shadow: var(--shadow-lg);">
                <div class="flex justify-between align-center mb-4" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                    <h3 class="card-title" style="color: var(--warning); margin: 0;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                            <path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/>
                            <path d="M7 21h10"/>
                            <path d="M12 3v18"/>
                        </svg>
                        Lodge Formal Grievance Appeal
                    </h3>
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="$set('showAppealForm', false)">Close</button>
                </div>
                
                <form wire:submit.prevent="submitAppeal">
                    <div class="form-group mb-4">
                        <label class="form-label">Explain your case / Provide justification (Min 20 characters)</label>
                        <textarea class="form-control" rows="5" wire:model="appealText" placeholder="Specify equivalence, missing document clarifications, or credentials reference logs..."></textarea>
                        @error('appealText') <span style="color: var(--danger); font-size: 0.82rem; margin-top: 0.25rem; display: block;">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group mb-6">
                        <label class="form-label">Upload Equivalence / Proof Document (PDF/Image)</label>
                        <input type="file" class="form-control" wire:model="appealProof">
                        @error('appealProof') <span style="color: var(--danger); font-size: 0.82rem; margin-top: 0.25rem; display: block;">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="btn btn-danger" style="width: 100%; padding: 0.75rem; border-radius: var(--radius-sm); font-size: 0.95rem;">
                        <span>Submit Formal Appeal</span>
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>
