<div style="max-width: 1400px; margin: 0 auto; padding: 1.5rem;">
    
    @if(!$isFeatureEnabled)
        <!-- Feature Disabled State -->
        <div style="max-width: 800px; margin: 4rem auto; text-align: center; background: var(--bg-card); border: 2px dashed var(--border-color); padding: 3rem; border-radius: 16px;">
            <div style="font-size: 3.5rem; margin-bottom: 1rem;">📊</div>
            <h2 style="font-size: 1.75rem; font-weight: 800; margin-bottom: 0.75rem; color: var(--color-text-primary);">
                Executive Recruitment Analytics Dashboard is Disabled
            </h2>
            <p style="font-size: 1rem; color: var(--color-text-secondary); line-height: 1.6; margin-bottom: 1.5rem;">
                The Vice Chancellor & Registrar Executive Analytics module is currently toggled OFF in system workflow settings.
                Administrative leadership can activate this feature from the Admin Settings panel.
            </p>
            @can('manage-settings')
                <a href="/admin/settings/workflows" class="btn btn-primary" style="padding: 0.75rem 1.75rem; font-weight: 700;">
                    ⚙️ Configure in System Settings
                </a>
            @else
                <span class="badge badge-slate" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                    Institutional Policy Active
                </span>
            @endcan
        </div>
    @else
        <!-- Executive Intelligence Header -->
        <div class="card mb-6" style="background: linear-gradient(135deg, var(--bg-card), rgba(99, 102, 241, 0.05)); border: 1px solid var(--border-color); box-shadow: var(--shadow-md); border-radius: 14px; padding: 1.5rem 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
                <div>
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.35rem;">
                        <span style="font-size: 1.75rem;">🏛️</span>
                        <div>
                            <span style="font-size: 0.72rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; color: var(--primary);">
                                OFFICE OF THE VICE CHANCELLOR & REGISTRAR
                            </span>
                            <h1 style="font-size: 1.65rem; font-weight: 900; letter-spacing: -0.02em; margin: 0; color: var(--color-text-primary);">
                                Executive Recruitment Intelligence & Statutory Compliance
                            </h1>
                        </div>
                    </div>
                    <p style="font-size: 0.88rem; color: var(--color-text-secondary); margin: 0; max-width: 750px;">
                        Strategic oversight dashboard tracking statutory recruitment intake, scrutiny clearance yield, regional quota adherence, disqualification Pareto analysis, and milestone velocity.
                    </p>
                </div>

                <!-- Export Action -->
                @can('export-executive-analytics')
                    <div>
                        <button type="button" 
                                wire:click="exportExecutiveReport" 
                                class="btn btn-primary" 
                                style="display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; font-weight: 800; box-shadow: 0 4px 14px rgba(99, 102, 241, 0.25);">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="7 10 12 15 17 10"/>
                                <line x1="12" y1="15" x2="12" y2="3"/>
                            </svg>
                            Export Executive Docket (CSV)
                        </button>
                    </div>
                @endcan
            </div>

            <!-- Filter Strip -->
            <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-color); display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                
                <!-- Advertisement Filter -->
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="font-size: 0.75rem; font-weight: 800; color: var(--color-text-muted); text-transform: uppercase;">
                        Advertisement:
                    </span>
                    <select wire:model.live="advertisementFilter" style="background: var(--bg-surface); color: var(--color-text-primary); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.4rem 0.75rem; font-size: 0.82rem; font-weight: 700;">
                        <option value="ALL">All Advertisements</option>
                        @foreach($advertisements as $ad)
                            <option value="{{ $ad->id }}">
                                Ad #{{ $ad->advertisement_no }} ({{ $ad->year }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Year Filter -->
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="font-size: 0.75rem; font-weight: 800; color: var(--color-text-muted); text-transform: uppercase;">
                        Year:
                    </span>
                    <select wire:model.live="yearFilter" style="background: var(--bg-surface); color: var(--color-text-primary); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.4rem 0.75rem; font-size: 0.82rem; font-weight: 700;">
                        <option value="ALL">All Years</option>
                        @foreach($years as $yr)
                            <option value="{{ $yr }}">{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Cadre Filter -->
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="font-size: 0.75rem; font-weight: 800; color: var(--color-text-muted); text-transform: uppercase;">
                        Cadre:
                    </span>
                    <select wire:model.live="cadreFilter" style="background: var(--bg-surface); color: var(--color-text-primary); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.4rem 0.75rem; font-size: 0.82rem; font-weight: 700;">
                        <option value="ALL">All Cadres (Teaching & Administrative)</option>
                        <option value="TEACHING">Teaching / Academic (Prof, Assoc Prof, Asst Prof, Lecturer)</option>
                        <option value="NON_TEACHING">Non-Teaching / Administrative Staff</option>
                    </select>
                </div>

            </div>
        </div>

        <!-- Executive KPI Cards Grid (6 Metric Cards) -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
            
            <!-- 1. Total Applications Intake -->
            <div class="card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem;">
                <div style="font-size: 0.72rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); letter-spacing: 0.05em;">
                    Total Intake Applications
                </div>
                <div style="font-size: 2rem; font-weight: 900; color: var(--color-text-primary); margin: 0.35rem 0;">
                    {{ number_format($analytics['total_applications']) }}
                </div>
                <div style="font-size: 0.75rem; color: var(--color-text-muted);">
                    All active campaigns in cycle
                </div>
            </div>

            <!-- 2. Scrutiny Clearance Rate -->
            <div class="card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem;">
                <div style="font-size: 0.72rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); letter-spacing: 0.05em;">
                    Scrutiny Clearance Yield
                </div>
                <div style="font-size: 2rem; font-weight: 900; color: var(--success); margin: 0.35rem 0;">
                    {{ $analytics['clearance_rate'] }}%
                </div>
                <div style="font-size: 0.75rem; color: var(--success);">
                    <strong>{{ $analytics['cleared_count'] }}</strong> Candidates eligible
                </div>
            </div>

            <!-- 3. Scrutiny Disqualification Rate -->
            <div class="card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem;">
                <div style="font-size: 0.72rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); letter-spacing: 0.05em;">
                    Disqualification Rate
                </div>
                <div style="font-size: 2rem; font-weight: 900; color: var(--danger); margin: 0.35rem 0;">
                    {{ $analytics['disqualification_rate'] }}%
                </div>
                <div style="font-size: 0.75rem; color: var(--danger);">
                    <strong>{{ $analytics['disqualified_count'] }}</strong> Ineligible / rejected
                </div>
            </div>

            <!-- 4. Selection Board Shortlisted -->
            <div class="card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem;">
                <div style="font-size: 0.72rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); letter-spacing: 0.05em;">
                    Board Viva Shortlisted
                </div>
                <div style="font-size: 2rem; font-weight: 900; color: var(--secondary); margin: 0.35rem 0;">
                    {{ number_format($analytics['interviewed_count']) }}
                </div>
                <div style="font-size: 0.75rem; color: var(--color-text-muted);">
                    {{ $analytics['interview_yield_rate'] }}% of eligible candidates
                </div>
            </div>

            <!-- 5. Appointed / Recommended -->
            <div class="card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem;">
                <div style="font-size: 0.72rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); letter-spacing: 0.05em;">
                    Recommended / Appointed
                </div>
                <div style="font-size: 2rem; font-weight: 900; color: var(--warning); margin: 0.35rem 0;">
                    {{ number_format($analytics['recommended_count']) }}
                </div>
                <div style="font-size: 0.75rem; color: var(--color-text-muted);">
                    {{ $analytics['final_selection_rate'] }}% of interviewed finalists
                </div>
            </div>

            <!-- 6. Revenue Realized -->
            <div class="card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem;">
                <div style="font-size: 0.72rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted); letter-spacing: 0.05em;">
                    Application Fee Settled
                </div>
                <div style="font-size: 1.8rem; font-weight: 900; color: var(--color-text-primary); margin: 0.35rem 0;">
                    PKR {{ number_format($analytics['settled_revenue']) }}
                </div>
                <div style="font-size: 0.75rem; color: var(--success);">
                    Settled & Reconciled Ledger
                </div>
            </div>

        </div>

        <!-- Middle Row: Quota Compliance Matrix (Left) & Disqualification Pareto (Right) -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            
            <!-- Left: Statutory Quota Compliance Matrix -->
            <div class="card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 14px; padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                    <div>
                        <h3 style="font-size: 1.15rem; font-weight: 800; margin: 0; color: var(--color-text-primary);">
                            Statutory Quota & Regional Compliance
                        </h3>
                        <p style="font-size: 0.78rem; color: var(--color-text-secondary); margin: 0.2rem 0 0 0;">
                            Distribution across provincial domiciles and statutory inclusion quotas
                        </p>
                    </div>
                    <span class="badge badge-paid" style="font-size: 0.7rem; font-weight: 800;">
                        Statutory Ratio
                    </span>
                </div>

                <!-- Inclusion Quotas Targets (Women, Minorities, Special Persons) -->
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem; margin-bottom: 1.25rem; background: var(--bg-surface); padding: 0.9rem; border-radius: 10px; border: 1px solid var(--border-color);">
                    <div style="text-align: center;">
                        <div style="font-size: 0.68rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted);">
                            Women Quota
                        </div>
                        <div style="font-size: 1.25rem; font-weight: 900; color: {{ $analytics['female_pct'] >= 15 ? 'var(--success)' : 'var(--warning)' }}; margin: 0.15rem 0;">
                            {{ $analytics['female_pct'] }}%
                        </div>
                        <div style="font-size: 0.68rem; color: var(--color-text-muted);">
                            Target: 15% ({{ $analytics['female_count'] }})
                        </div>
                    </div>

                    <div style="text-align: center; border-left: 1px solid var(--border-color); border-right: 1px solid var(--border-color);">
                        <div style="font-size: 0.68rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted);">
                            Minorities Quota
                        </div>
                        <div style="font-size: 1.25rem; font-weight: 900; color: {{ $analytics['minority_pct'] >= 5 ? 'var(--success)' : 'var(--warning)' }}; margin: 0.15rem 0;">
                            {{ $analytics['minority_pct'] }}%
                        </div>
                        <div style="font-size: 0.68rem; color: var(--color-text-muted);">
                            Target: 5% ({{ $analytics['minority_count'] }})
                        </div>
                    </div>

                    <div style="text-align: center;">
                        <div style="font-size: 0.68rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted);">
                            Special Persons
                        </div>
                        <div style="font-size: 1.25rem; font-weight: 900; color: {{ $analytics['disabled_pct'] >= 2 ? 'var(--success)' : 'var(--warning)' }}; margin: 0.15rem 0;">
                            {{ $analytics['disabled_pct'] }}%
                        </div>
                        <div style="font-size: 0.68rem; color: var(--color-text-muted);">
                            Target: 2% ({{ $analytics['disabled_count'] }})
                        </div>
                    </div>
                </div>

                <!-- Regional Quota Progress Bars Table -->
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach($analytics['quota_buckets'] as $qb)
                        <div>
                            <div style="display: flex; justify-content: space-between; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.25rem;">
                                <span style="color: var(--color-text-primary);">{{ $qb['label'] }}</span>
                                <span style="color: var(--color-text-secondary);">
                                    <strong>{{ $qb['count'] }}</strong> ({{ $qb['percentage'] }}%) &bull; {{ $qb['cleared'] }} Cleared
                                </span>
                            </div>
                            <div style="height: 7px; width: 100%; background: var(--bg-surface); border-radius: 4px; overflow: hidden;">
                                <div style="height: 100%; width: {{ min(100, $qb['percentage']) }}%; background: linear-gradient(90deg, var(--primary), var(--secondary)); border-radius: 4px;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Right: Scrutiny Disqualification Pareto Root-Cause Breakdown -->
            <div class="card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 14px; padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                    <div>
                        <h3 style="font-size: 1.15rem; font-weight: 800; margin: 0; color: var(--color-text-primary);">
                            Scrutiny Disqualification Pareto Breakdown
                        </h3>
                        <p style="font-size: 0.78rem; color: var(--color-text-secondary); margin: 0.2rem 0 0 0;">
                            Root-cause categorization of candidate disqualifications
                        </p>
                    </div>
                    <span class="badge badge-disqualified" style="font-size: 0.7rem; font-weight: 800;">
                        {{ $analytics['disqualified_count'] }} Deficiencies
                    </span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1rem; margin-top: 1rem;">
                    @foreach($analytics['pareto_categories'] as $cat)
                        <div>
                            <div style="display: flex; justify-content: space-between; font-size: 0.82rem; font-weight: 700; margin-bottom: 0.25rem;">
                                <span style="color: var(--color-text-primary);">{{ $cat['label'] }}</span>
                                <span style="color: var(--danger); font-weight: 800;">
                                    {{ $cat['count'] }} ({{ $cat['percentage'] }}%)
                                </span>
                            </div>
                            <div style="height: 8px; width: 100%; background: var(--bg-surface); border-radius: 4px; overflow: hidden;">
                                <div style="height: 100%; width: {{ min(100, $cat['percentage']) }}%; background: linear-gradient(90deg, #f43f5e, #fb7185); border-radius: 4px;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Bottom Row: Milestone Stage Velocity (Time-to-Hire) -->
        <div class="card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 14px; padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <h3 style="font-size: 1.15rem; font-weight: 800; margin: 0; color: var(--color-text-primary);">
                        Statutory Milestone Velocity & Pipeline Turnaround (Time-to-Hire)
                    </h3>
                    <p style="font-size: 0.78rem; color: var(--color-text-secondary); margin: 0.2rem 0 0 0;">
                        Average elapsed duration across statutory stages from job notice to final Syndicate decree
                    </p>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <span class="badge badge-pending" style="font-size: 0.8rem; font-weight: 800; padding: 0.4rem 0.8rem;">
                        ⏱️ Total Average Cycle: {{ $analytics['velocities']['total_cycle']['avg_days'] }} Days
                    </span>
                </div>
            </div>

            <!-- Milestone Timeline Progression Cards -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1rem;">
                
                <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 10px; padding: 1rem; border-top: 3px solid var(--primary);">
                    <div style="font-size: 0.68rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted);">
                        Phase 1: Candidate Intake
                    </div>
                    <div style="font-size: 1.35rem; font-weight: 900; color: var(--primary); margin: 0.35rem 0;">
                        {{ $analytics['velocities']['intake']['avg_days'] }} Days
                    </div>
                    <div style="font-size: 0.75rem; color: var(--color-text-secondary);">
                        Ad notice to submission deadline
                    </div>
                </div>

                <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 10px; padding: 1rem; border-top: 3px solid var(--secondary);">
                    <div style="font-size: 0.68rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted);">
                        Phase 2: Scrutiny Desk
                    </div>
                    <div style="font-size: 1.35rem; font-weight: 900; color: var(--secondary); margin: 0.35rem 0;">
                        {{ $analytics['velocities']['scrutiny']['avg_days'] }} Days
                    </div>
                    <div style="font-size: 0.75rem; color: var(--color-text-secondary);">
                        Document verification & audit
                    </div>
                </div>

                <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 10px; padding: 1rem; border-top: 3px solid var(--warning);">
                    <div style="font-size: 0.68rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted);">
                        Phase 3: External Peer Review
                    </div>
                    <div style="font-size: 1.35rem; font-weight: 900; color: var(--warning); margin: 0.35rem 0;">
                        {{ $analytics['velocities']['peer_review']['avg_days'] }} Days
                    </div>
                    <div style="font-size: 0.75rem; color: var(--color-text-secondary);">
                        Expert dossier evaluation & score
                    </div>
                </div>

                <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 10px; padding: 1rem; border-top: 3px solid var(--success);">
                    <div style="font-size: 0.68rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted);">
                        Phase 4: Selection Board
                    </div>
                    <div style="font-size: 1.35rem; font-weight: 900; color: var(--success); margin: 0.35rem 0;">
                        {{ $analytics['velocities']['board_interview']['avg_days'] }} Days
                    </div>
                    <div style="font-size: 0.75rem; color: var(--color-text-secondary);">
                        Interview summons & live scoring
                    </div>
                </div>

                <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 10px; padding: 1rem; border-top: 3px solid #8b5cf6;">
                    <div style="font-size: 0.68rem; font-weight: 800; text-transform: uppercase; color: var(--color-text-muted);">
                        Phase 5: Syndicate Decree
                    </div>
                    <div style="font-size: 1.35rem; font-weight: 900; color: #8b5cf6; margin: 0.35rem 0;">
                        {{ $analytics['velocities']['syndicate']['avg_days'] }} Days
                    </div>
                    <div style="font-size: 0.75rem; color: var(--color-text-secondary);">
                        Final approval & offer letter
                    </div>
                </div>

            </div>
        </div>

    @endif

</div>
