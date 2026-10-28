<div>
    <!-- Dynamic Header -->
    <div class="card mb-6" style="background: var(--bg-card); border-color: var(--secondary-glow); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);">
        <div class="flex justify-between align-center">
            <div>
                <h2 class="card-title" style="color: var(--color-text-primary); font-size: 1.75rem; font-weight: 800; letter-spacing: -0.5px;">
                    🏛️ Administrative Operations Control
                </h2>
                <p class="card-subtitle" style="margin-bottom: 0;">Real-time metrics, scrutiny pipelines, and selection board candidate evaluation desks.</p>
            </div>
            
            <!-- User Status Tag -->
            <div style="background: var(--bg-btn-secondary); border: 1px solid var(--border-color); padding: 0.5rem 1rem; border-radius: 8px;" class="flex align-center gap-2">
                <span class="badge badge-paid" style="width: 8px; height: 8px; padding: 0; border-radius: 50%; display: inline-block;"></span>
                <span style="font-size: 0.85rem; font-weight: 700; color: var(--color-text-secondary); text-transform: uppercase;">
                    📟 {{ str_replace('-', ' ', auth()->user()->roles->first()?->name) }} Session
                </span>
            </div>
        </div>
    </div>

    <!-- Interactive Metrics Summary Cards -->
    <div class="grid grid-cols-4 gap-6 mb-6">
        <div class="card" style="border-color: var(--secondary-glow); background: rgba(6, 182, 212, 0.05); box-shadow: 0 0 15px rgba(6, 182, 212, 0.05);">
            <div class="flex justify-between align-center">
                <p style="color: var(--color-text-secondary); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Paid / In Scrutiny</p>
                <span style="font-size: 1.25rem;">💳</span>
            </div>
            <h3 style="font-size: 2.5rem; font-weight: 800; color: var(--secondary); margin-top: 0.5rem; letter-spacing: -1px;">{{ $metrics['paid'] }}</h3>
            <p style="font-size: 0.75rem; color: var(--color-text-muted); margin-top: 0.25rem;">Waiting for document verify</p>
        </div>
        <div class="card" style="border-color: var(--success-glow); background: rgba(16, 185, 129, 0.05); box-shadow: 0 0 15px rgba(16, 185, 129, 0.05);">
            <div class="flex justify-between align-center">
                <p style="color: var(--color-text-secondary); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Eligible Candidates</p>
                <span style="font-size: 1.25rem;">✓</span>
            </div>
            <h3 style="font-size: 2.5rem; font-weight: 800; color: var(--success); margin-top: 0.5rem; letter-spacing: -1px;">{{ $metrics['eligible'] }}</h3>
            <p style="font-size: 0.75rem; color: var(--color-text-muted); margin-top: 0.25rem;">Cleared verification checks</p>
        </div>
        <div class="card" style="border-color: var(--danger-glow); background: rgba(239, 68, 68, 0.05); box-shadow: 0 0 15px rgba(239, 68, 68, 0.05);">
            <div class="flex justify-between align-center">
                <p style="color: var(--color-text-secondary); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Disqualified</p>
                <span style="font-size: 1.25rem;">✗</span>
            </div>
            <h3 style="font-size: 2.5rem; font-weight: 800; color: var(--danger); margin-top: 0.5rem; letter-spacing: -1px;">{{ $metrics['ineligible'] }}</h3>
            <p style="font-size: 0.75rem; color: var(--color-text-muted); margin-top: 0.25rem;">Marked failed in scrutiny</p>
        </div>
        <div class="card" style="border-color: var(--warning-glow); background: rgba(245, 158, 11, 0.05); box-shadow: 0 0 15px rgba(245, 158, 11, 0.05);">
            <div class="flex justify-between align-center">
                <p style="color: var(--color-text-secondary); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Appealed Grievances</p>
                <span style="font-size: 1.25rem;">⚖️</span>
            </div>
            <h3 style="font-size: 2.5rem; font-weight: 800; color: var(--warning); margin-top: 0.5rem; letter-spacing: -1px;">{{ $metrics['appealed'] }}</h3>
            <p style="font-size: 0.75rem; color: var(--color-text-muted); margin-top: 0.25rem;">Grievances in 7-day window</p>
        </div>
    </div>

    <!-- Graphics and Track Details Panel -->
    <div class="grid grid-cols-3 gap-6 mb-6">
        
        <!-- Track Breakdown & Graphical SVG Progress Indicators -->
        <div class="card flex-col gap-4">
            <h3 class="card-title" style="font-size: 1.15rem; color: var(--secondary);">📊 Merit Analytics Rings</h3>
            
            <div class="flex justify-between align-center mt-2 mb-2">
                <!-- SVG Ring 1: Scrutiny -->
                <div class="flex-col align-center text-center">
                    <svg width="70" height="70" viewBox="0 0 36 36">
                        <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="3" />
                        <path class="circle" stroke-dasharray="75, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="var(--secondary)" stroke-width="3" stroke-linecap="round" />
                        <text x="18" y="20.35" font-size="7" font-weight="700" fill="white" text-anchor="middle">75%</text>
                    </svg>
                    <span style="font-size: 0.75rem; color: var(--color-text-secondary); margin-top: 0.5rem; font-weight: 500;">Scrutinized</span>
                </div>
                
                <!-- SVG Ring 2: Paid -->
                <div class="flex-col align-center text-center">
                    <svg width="70" height="70" viewBox="0 0 36 36">
                        <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="3" />
                        <path class="circle" stroke-dasharray="90, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="var(--success)" stroke-width="3" stroke-linecap="round" />
                        <text x="18" y="20.35" font-size="7" font-weight="700" fill="white" text-anchor="middle">90%</text>
                    </svg>
                    <span style="font-size: 0.75rem; color: var(--color-text-secondary); margin-top: 0.5rem; font-weight: 500;">Settled Bills</span>
                </div>

                <!-- SVG Ring 3: Evaluated -->
                <div class="flex-col align-center text-center">
                    <svg width="70" height="70" viewBox="0 0 36 36">
                        <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="3" />
                        <path class="circle" stroke-dasharray="45, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="var(--warning)" stroke-width="3" stroke-linecap="round" />
                        <text x="18" y="20.35" font-size="7" font-weight="700" fill="white" text-anchor="middle">45%</text>
                    </svg>
                    <span style="font-size: 0.75rem; color: var(--color-text-secondary); margin-top: 0.5rem; font-weight: 500;">Graded</span>
                </div>
            </div>

            <!-- Tracks metrics -->
            <div class="flex-col gap-2" style="border-top: 1px solid var(--border-color); padding-top: 1rem;">
                <div class="flex justify-between" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                    <span style="color: var(--color-text-secondary); font-size: 0.9rem;">Academic Faculty</span>
                    <strong style="color: var(--secondary);">{{ $tracks['ACADEMIC'] }}</strong>
                </div>
                <div class="flex justify-between" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; margin-top: 0.5rem;">
                    <span style="color: var(--color-text-secondary); font-size: 0.9rem;">Administrative Officers</span>
                    <strong style="color: var(--secondary);">{{ $tracks['ADMINISTRATIVE'] }}</strong>
                </div>
                <div class="flex justify-between" style="padding-bottom: 0.5rem; margin-top: 0.5rem;">
                    <span style="color: var(--color-text-secondary); font-size: 0.9rem;">Support Staff</span>
                    <strong style="color: var(--secondary);">{{ $tracks['SUPPORT'] }}</strong>
                </div>
            </div>
        </div>

        <!-- Active Campaign Monitors -->
        <div class="card grid-cols-2" style="grid-column: span 2;">
            <h3 class="card-title" style="font-size: 1.15rem; color: var(--secondary); margin-bottom: 1rem;">📢 Active Campaigns Monitor</h3>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Job Code</th>
                            <th>Campaign Position</th>
                            <th>Department Scope</th>
                            <th>Submissions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($campaigns as $camp)
                            <tr>
                                <td><code style="color: var(--secondary); font-weight: 700;">{{ $camp->job_code }}</code></td>
                                <td><strong>{{ $camp->title }}</strong></td>
                                <td>{{ $camp->department }}</td>
                                <td><span class="badge badge-appealed" style="font-weight: 700; border-radius: 4px;">{{ $camp->applications_count }} files</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Scrutiny Pipeline Section -->
    @if(!empty($pendingScrutiny))
        <div class="card mb-6" style="border-color: var(--secondary-glow);">
            <h3 class="card-title mb-4" style="color: var(--secondary); display: flex; align-items: center; gap: 0.5rem;">
                🔍 Document Scrutiny Desk (Action Required)
            </h3>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Candidate Details</th>
                            <th>Academic Track</th>
                            <th>Campaign Code</th>
                            <th>Voucher Fee</th>
                            <th>Scrutiny Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingScrutiny as $app)
                            <tr>
                                <td>
                                    <strong style="color: var(--color-text-primary); font-size: 1rem;">{{ $app->user->basicInfo?->first_name }} {{ $app->user->basicInfo?->last_name }}</strong><br>
                                    <small style="color: var(--color-text-secondary); font-family: monospace;">CNIC: {{ $app->user->basicInfo?->cnic_number }}</small>
                                </td>
                                <td><span class="badge badge-appealed">{{ $app->campaign->track_type }}</span></td>
                                <td><code style="color: var(--secondary);">{{ $app->campaign->job_code }}</code></td>
                                <td><strong style="color: var(--warning);">PKR {{ number_format($app->billingLedger?->amount, 2) }}</strong></td>
                                <td>
                                    <a href="/admin/verification/{{ $app->id }}" class="btn btn-primary" style="padding: 0.45rem 1rem; font-size: 0.85rem; border-radius: 4px;">
                                        🔍 Scrutinize Credentials
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center" style="color: var(--color-text-muted);">No paid files in verification queue.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Selection Board Evaluation Section (For Selection Board & Vice Chancellor) -->
    @if(!empty($pendingEvaluations))
        <div class="card mb-6" style="border-color: var(--success-glow);">
            <h3 class="card-title mb-4" style="color: var(--success); display: flex; align-items: center; gap: 0.5rem;">
                ⭐ VC & Selection Board Evaluation Desk
            </h3>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Candidate Account</th>
                            <th>Campaign Position</th>
                            <th>CNIC Reference</th>
                            <th>Verification Gate</th>
                            <th>Grading Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingEvaluations as $app)
                            <tr>
                                <td>
                                    <strong style="color: var(--color-text-primary); font-size: 1rem;">{{ $app->user->basicInfo?->first_name }} {{ $app->user->basicInfo?->last_name }}</strong><br>
                                    <small style="color: var(--color-text-secondary);">{{ $app->user->email }}</small>
                                </td>
                                <td><strong>{{ $app->campaign->title }}</strong> <br><small style="color: var(--color-text-secondary);">{{ $app->campaign->job_code }}</small></td>
                                <td><code style="font-family: monospace;">{{ $app->user->basicInfo?->cnic_number }}</code></td>
                                <td>
                                    <span class="badge badge-paid">CLEARED (ELIGIBLE)</span>
                                </td>
                                <td>
                                    <a href="/board/evaluate/{{ $app->id }}" class="btn btn-success" style="padding: 0.45rem 1rem; font-size: 0.85rem; border-radius: 4px;">
                                        ⭐ Grade Candidate
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center" style="color: var(--color-text-muted);">No eligible candidates waiting for selection board evaluation.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
