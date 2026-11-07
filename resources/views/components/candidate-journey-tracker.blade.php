@props(['application', 'allApplications' => collect(), 'currencySymbol' => 'PKR'])

@php
    if (!$application) {
        return;
    }
    $milestones = $application->journey_milestones;
    $progress = $application->journey_progress;
    $status = strtoupper($application->status ?? 'DRAFT');
    $ledger = $application->billingLedger;
    $percent = $progress['percent'] ?? 0;
    $appellateEnabled = \App\Models\SystemSetting::isPipelineStepEnabled('appellate');
@endphp

<div class="card journey-tracker-card mb-6" style="background: var(--bg-card); border: 1.5px solid var(--border-color); border-radius: var(--radius-lg); box-shadow: var(--shadow-md); overflow: hidden; padding: 0;">
    <!-- Tracker Header -->
    <div style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.08), rgba(6, 182, 212, 0.05)); border-bottom: 1px solid var(--border-color); padding: 1.25rem 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.65rem; margin-bottom: 0.35rem; flex-wrap: wrap;">
                    <span class="badge" style="background: var(--primary-light); color: var(--primary); font-weight: 800; font-size: 0.76rem; letter-spacing: 0.05em; font-family: 'JetBrains Mono', monospace;">
                        {{ $application->tracking_number }}
                    </span>
                    <span class="badge" style="background: var(--bg-btn-secondary); color: var(--color-text-secondary); font-size: 0.74rem;">
                        {{ $application->campaign->department }}
                    </span>
                    <span class="badge" style="background: var(--bg-btn-secondary); color: var(--color-text-secondary); font-size: 0.74rem;">
                        {{ $application->campaign->track_type }}
                    </span>
                </div>
                <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--color-text-primary); margin: 0; letter-spacing: -0.02em;">
                    {{ $application->campaign->title }}
                </h2>
            </div>

            <!-- Application Switcher for Multiple Vacancies -->
            @if($allApplications && $allApplications->count() > 1)
                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                    <span style="font-size: 0.75rem; color: var(--color-text-muted); font-weight: 700; text-transform: uppercase;">Switch Application:</span>
                    <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                        @foreach($allApplications as $optApp)
                            <button type="button" 
                                    wire:click="selectJourneyApp({{ $optApp->id }})"
                                    class="btn btn-sm"
                                    style="font-size: 0.75rem; font-weight: 700; padding: 0.3rem 0.65rem; border-radius: var(--radius-sm); {{ $optApp->id === $application->id ? 'background: var(--primary); color: #fff; border: 1px solid var(--primary); box-shadow: 0 2px 6px rgba(99,102,241,0.3);' : 'background: var(--bg-surface); color: var(--color-text-secondary); border: 1px solid var(--border-color);' }}">
                                {{ $optApp->tracking_number }} ({{ $optApp->campaign->job_code }})
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Progress Overview Bar -->
        <div style="margin-top: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; font-size: 0.82rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span class="journey-pulse-dot" style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--primary); box-shadow: 0 0 8px var(--primary);"></span>
                    <strong style="color: var(--color-text-primary);">Current Milestone:</strong>
                    <span style="color: var(--primary); font-weight: 700;">{{ $progress['label'] }}</span>
                </div>
                <span style="font-family: 'JetBrains Mono', monospace; font-weight: 800; color: var(--color-text-secondary);">
                    {{ $percent }}% Complete
                </span>
            </div>

            <div style="width: 100%; height: 7px; background: rgba(255, 255, 255, 0.08); border-radius: 999px; overflow: hidden; position: relative;">
                <div style="width: {{ $percent }}%; height: 100%; background: linear-gradient(90deg, #10b981, #06b6d4, #6366f1); border-radius: 999px; transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);"></div>
            </div>
        </div>
    </div>

    <!-- Connected 6-Stage Timeline Stepper (Pizza-Tracker UX) -->
    <div class="journey-stepper-container" style="padding: 1.75rem 1.5rem 1.5rem;">
        <div class="journey-track-grid">
            @foreach($milestones as $step)
                @php
                    $isCompleted = ($step['state'] === 'completed');
                    $isActive = ($step['state'] === 'active');
                    $isAlert = ($step['state'] === 'alert');
                    $isPending = ($step['state'] === 'pending');
                @endphp

                <div class="journey-step-item {{ $isCompleted ? 'step-completed' : '' }} {{ $isActive ? 'step-active' : '' }} {{ $isAlert ? 'step-alert' : '' }} {{ $isPending ? 'step-pending' : '' }}">
                    <!-- Step Node Indicator -->
                    <div class="journey-node-wrapper">
                        <div class="journey-node-circle">
                            @if($isCompleted)
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            @elseif($isAlert)
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="8" x2="12" y2="12"/>
                                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                                </svg>
                            @elseif($isActive)
                                <span class="journey-active-inner-dot"></span>
                            @else
                                <span style="font-size: 0.8rem; font-weight: 700; color: var(--color-text-dim);">{{ $step['index'] }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Step Content Block -->
                    <div class="journey-step-content">
                        <div class="journey-step-header">
                            <span class="journey-step-num">STAGE 0{{ $step['index'] }}</span>
                            <span class="journey-badge badge-{{ $step['color'] }}">
                                {{ $step['badge'] }}
                            </span>
                        </div>
                        <h4 class="journey-step-title">{{ $step['title'] }}</h4>
                        <div class="journey-step-subtitle">{{ $step['subtitle'] }}</div>
                        <p class="journey-step-desc">{{ $step['description'] }}</p>
                        @if($step['timestamp'])
                            <div class="journey-step-time">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
                                <span>{{ $step['timestamp'] }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Contextual Direct Action Footer -->
    <div style="background: var(--bg-surface); border-top: 1px solid var(--border-color); padding: 1.15rem 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            @if($ledger)
                <div class="copyable-chip" onclick="copyConsumerId('{{ $ledger->consumer_id }}', this)" title="Click to copy Consumer ID">
                    <span style="font-size: 0.72rem; color: var(--color-text-muted); text-transform: uppercase;">1Link ID:</span>
                    <strong style="color: var(--color-text-primary);">{{ $ledger->consumer_id }}</strong>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="14" height="14" x="8" y="8" rx="2" ry="2"/>
                        <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>
                    </svg>
                </div>
            @endif

            <span style="font-size: 0.82rem; color: var(--color-text-secondary);">
                Fee: <strong style="color: var(--color-text-primary);">{{ $currencySymbol }} {{ number_format($ledger?->amount ?? $application->campaign->application_fee, 2) }}</strong>
            </span>
        </div>

        <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
            <!-- Print Official Dossier Button -->
            <a href="{{ route('application.print', ['id' => $application->id, 'view' => 'candidate']) }}" 
               target="_blank" 
               class="btn btn-secondary btn-sm"
               style="font-size: 0.8rem; font-weight: 700;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
                <span>Print Dossier</span>
            </a>

            <!-- View Bank Slip Button -->
            <button type="button" 
                    wire:click="openSlipModal({{ $application->id }})" 
                    class="btn btn-secondary btn-sm"
                    style="font-size: 0.8rem; font-weight: 700;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 9V2h12v7"/>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                    <path d="M6 14h12v8H6z"/>
                </svg>
                <span>1Link Slip</span>
            </button>

            <!-- Merit Gazette Link -->
            <a href="{{ route('merit-list.print', ['campaignId' => $application->campaign_id, 'stage' => 'consolidated']) }}" 
               target="_blank" 
               class="btn btn-secondary btn-sm"
               style="font-size: 0.8rem; font-weight: 700;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
                <span>Merit Gazette</span>
            </a>

            <!-- Stage-Specific Call To Action -->
            @if($status === 'SUBMITTED' && $ledger)
                <a href="/bank/simulator?consumer_id={{ $ledger->consumer_id }}" 
                   class="btn btn-primary btn-sm"
                   style="font-size: 0.82rem; font-weight: 800; background: linear-gradient(135deg, #10b981, #059669); border: none; box-shadow: 0 2px 8px rgba(16,185,129,0.35);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="14" x="2" y="5" rx="2"/>
                        <line x1="2" x2="22" y1="10" y2="10"/>
                    </svg>
                    <span>Pay 1Link Voucher Online</span>
                </a>
            @elseif($status === 'INELIGIBLE')
                @if($appellateEnabled)
                    <button type="button" 
                            wire:click="openAppealForm({{ $application->id }})" 
                            class="btn btn-danger btn-sm"
                            style="font-size: 0.82rem; font-weight: 800;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/>
                            <path d="m9 12 2 2 4-4"/>
                        </svg>
                        <span>Submit Formal Appellate Petition</span>
                    </button>
                @endif
            @endif
        </div>
    </div>
</div>

<style>
/* ==============================================================
   CANDIDATE APPLICATION JOURNEY TRACKER (PIZZA-TRACKER UX)
   ============================================================== */
.journey-track-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 1rem;
    position: relative;
}

/* Horizontal connecting line across grid */
.journey-track-grid::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 20px;
    right: 20px;
    height: 3px;
    background: rgba(255, 255, 255, 0.08);
    z-index: 1;
}

.journey-step-item {
    position: relative;
    display: flex;
    flex-direction: column;
    z-index: 2;
}

.journey-node-wrapper {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    margin-bottom: 0.85rem;
}

.journey-node-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--bg-card-solid);
    border: 2px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    box-shadow: var(--shadow-sm);
}

/* Completed Step Styling */
.step-completed .journey-node-circle {
    background: #10b981;
    border-color: #10b981;
    box-shadow: 0 0 12px rgba(16, 185, 129, 0.45);
}

/* Active Step Styling */
.step-active .journey-node-circle {
    background: var(--bg-card);
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2), 0 0 15px rgba(99, 102, 241, 0.4);
    animation: journeyPulseRing 2s infinite ease-in-out;
}

.journey-active-inner-dot {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: var(--primary);
}

/* Alert Step Styling */
.step-alert .journey-node-circle {
    background: #ef4444;
    border-color: #ef4444;
    box-shadow: 0 0 14px rgba(239, 68, 68, 0.45);
}

/* Pending Step Styling */
.step-pending .journey-node-circle {
    background: var(--bg-surface);
    border-color: rgba(255, 255, 255, 0.08);
}

/* Content block styling */
.journey-step-content {
    display: flex;
    flex-direction: column;
}

.journey-step-header {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    margin-bottom: 0.25rem;
    flex-wrap: wrap;
}

.journey-step-num {
    font-size: 0.65rem;
    font-weight: 800;
    letter-spacing: 0.06em;
    color: var(--color-text-muted);
}

.journey-badge {
    font-size: 0.62rem;
    font-weight: 800;
    padding: 0.1rem 0.4rem;
    border-radius: 4px;
    text-transform: uppercase;
}

.badge-emerald { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
.badge-indigo  { background: rgba(99, 102, 241, 0.15); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.3); }
.badge-amber   { background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); }
.badge-purple  { background: rgba(168, 85, 247, 0.15); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.3); }
.badge-red     { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
.badge-slate   { background: rgba(148, 163, 184, 0.12); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.2); }

.journey-step-title {
    font-size: 0.88rem;
    font-weight: 800;
    color: var(--color-text-primary);
    margin: 0 0 0.15rem 0;
    line-height: 1.25;
}

.journey-step-subtitle {
    font-size: 0.74rem;
    font-weight: 600;
    color: var(--color-text-secondary);
    margin-bottom: 0.35rem;
}

.journey-step-desc {
    font-size: 0.72rem;
    color: var(--color-text-muted);
    margin: 0 0 0.4rem 0;
    line-height: 1.35;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.journey-step-time {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.7rem;
    color: var(--color-text-dim);
    font-weight: 600;
}

@keyframes journeyPulseRing {
    0% {
        box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.5), 0 0 10px rgba(99, 102, 241, 0.3);
    }
    70% {
        box-shadow: 0 0 0 8px rgba(99, 102, 241, 0), 0 0 15px rgba(99, 102, 241, 0.5);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(99, 102, 241, 0), 0 0 10px rgba(99, 102, 241, 0.3);
    }
}

/* ==============================================================
   MULTI-DEVICE RESPONSIVENESS: SMARTPHONES & TABLETS (< 768px)
   Converts horizontal stepper into an immaculate vertical timeline!
   ============================================================== */
@media (max-width: 768px) {
    .journey-track-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
        padding-left: 1.75rem;
    }

    /* Vertical connector line on mobile */
    .journey-track-grid::before {
        top: 15px;
        bottom: 15px;
        left: 36px;
        width: 3px;
        height: auto;
        right: auto;
    }

    .journey-step-item {
        flex-direction: row;
        align-items: flex-start;
        gap: 1.25rem;
    }

    .journey-node-wrapper {
        margin-bottom: 0;
        flex-shrink: 0;
    }

    .journey-step-desc {
        -webkit-line-clamp: unset;
    }
}
</style>
