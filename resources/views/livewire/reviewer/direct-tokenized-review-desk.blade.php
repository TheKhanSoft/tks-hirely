@php
    $app = $review?->application;
    $basic = $app?->user?->basicInfo;
    $campaign = $app?->campaign;
    $degrees = $basic?->degrees ?? collect();
    $publications = $basic?->publications ?? collect();
    $projects = $basic?->researchProjects ?? collect();
    $patents = $basic?->patents ?? collect();
@endphp

<div class="direct-review-wrapper" style="max-width: 1100px; margin: 1.5rem auto 3rem; padding: 0 1rem;">

    {{-- 1. FEATURE DISABLED BANNER --}}
    @if($isFeatureDisabled)
        <div class="card" style="padding: 2.5rem; text-align: center; border-left: 5px solid var(--warning); background: var(--bg-card); box-shadow: var(--shadow-lg);">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🔒</div>
            <h2 style="font-size: 1.4rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.5rem;">
                External Referee Direct Desk Currently Disabled
            </h2>
            <p style="color: var(--color-text-secondary); max-width: 650px; margin: 0 auto; line-height: 1.6; font-size: 0.95rem;">
                The passwordless 1-click referee desk is currently disabled in institutional settings. Please log into the institutional referee portal or contact the Office of the Registrar.
            </p>
            <div style="margin-top: 1.5rem;">
                <a href="/login" class="btn btn-primary" style="font-weight: 700;">Go to Portal Login</a>
            </div>
        </div>

    {{-- 2. INVALID OR EXPIRED TOKEN --}}
    @elseif($isInvalidToken)
        <div class="card" style="padding: 2.5rem; text-align: center; border-left: 5px solid var(--danger); background: var(--bg-card); box-shadow: var(--shadow-lg);">
            <div style="font-size: 3rem; margin-bottom: 1rem;">⚠️</div>
            <h2 style="font-size: 1.4rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.5rem;">
                Invalid or Expired Evaluation Link
            </h2>
            <p style="color: var(--color-text-secondary); max-width: 650px; margin: 0 auto; line-height: 1.6; font-size: 0.95rem;">
                This cryptographic evaluation link has expired or is invalid. To protect candidate portfolio confidentiality, links remain valid for a limited period. Please contact the Registrar Secretariat for a refreshed link.
            </p>
            <div style="margin-top: 1.5rem;">
                <span class="badge badge-slate" style="font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; padding: 0.4rem 0.8rem;">
                    TOKEN: {{ substr($token, 0, 10) }}...
                </span>
            </div>
        </div>

    {{-- 3. ACTIVE EVALUATION DESK --}}
    @else
        <!-- Institutional Header Banner -->
        <div class="card mb-6" style="padding: 1.75rem 2rem; background: var(--bg-card); border-top: 4px solid var(--primary); box-shadow: var(--shadow-md);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.4rem; flex-wrap: wrap;">
                        <span class="badge badge-indigo" style="font-weight: 800; font-size: 0.72rem;">CONFIDENTIAL PEER REVIEW</span>
                        <span class="badge badge-slate" style="font-size: 0.72rem; font-weight: 700;">EXTERNAL REFEREE DESK</span>
                        @if($isSubmitted)
                            <span class="badge badge-emerald" style="font-weight: 800; font-size: 0.72rem;">✓ SUBMITTED &amp; LOCKED</span>
                        @else
                            <span class="badge badge-amber" style="font-weight: 800; font-size: 0.72rem;">PENDING EVALUATION</span>
                        @endif
                    </div>

                    <h1 style="font-size: 1.45rem; font-weight: 800; color: var(--color-text-primary); margin: 0; letter-spacing: -0.02em;">
                        Academic Portfolio Evaluation: {{ $campaign->title ?? 'Advertised Post' }}
                    </h1>
                    <div style="color: var(--color-text-secondary); font-size: 0.88rem; margin-top: 0.35rem;">
                        <span>Institution: <strong>{{ $orgName }}</strong></span> • 
                        <span>Department: <strong>{{ $campaign->department?->name ?? 'Academic Faculty' }}</strong></span> •
                        <span>Candidate Tracking: <strong style="font-family: 'JetBrains Mono', monospace;">{{ $app->tracking_number }}</strong></span>
                    </div>
                </div>

                <div style="text-align: right;">
                    <div style="font-size: 0.78rem; color: var(--color-text-muted); text-transform: uppercase; font-weight: 700;">Evaluation Due Date</div>
                    <div style="font-size: 0.95rem; font-weight: 800; color: var(--color-text-primary); font-family: 'JetBrains Mono', monospace;">
                        {{ $review->due_date ? $review->due_date->format('d M, Y') : 'Statutory Timetable' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session()->has('message'))
            <div class="card mb-6" style="padding: 1.25rem 1.5rem; background: rgba(16, 185, 129, 0.1); border: 1.5px solid #10b981; border-radius: var(--radius-md); display: flex; align-items: center; gap: 0.75rem;">
                <span style="font-size: 1.5rem;">🎉</span>
                <div>
                    <strong style="color: #10b981; font-size: 0.95rem;">Evaluation Successfully Submitted!</strong>
                    <div style="color: var(--color-text-primary); font-size: 0.85rem; margin-top: 0.15rem;">
                        {{ session('message') }}
                    </div>
                </div>
            </div>
        @endif

        @if(session()->has('error'))
            <div class="card mb-6" style="padding: 1.25rem 1.5rem; background: rgba(239, 68, 68, 0.1); border: 1.5px solid #ef4444; border-radius: var(--radius-md); display: flex; align-items: center; gap: 0.75rem;">
                <span style="font-size: 1.5rem;">⚠️</span>
                <div style="color: #ef4444; font-size: 0.9rem; font-weight: 700;">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">

            <!-- CANDIDATE RESEARCH DOSSIER SECTION -->
            <div class="card" style="padding: 1.75rem; background: var(--bg-card); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm);">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.25rem;">
                    <div>
                        <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--color-text-primary); margin: 0;">
                            📚 Research Portfolio &amp; Scholarly Publications ({{ $publications->count() }})
                        </h3>
                        <p style="font-size: 0.82rem; color: var(--color-text-secondary); margin: 0.2rem 0 0 0;">
                            List of peer-reviewed journal papers, books, and conference proceedings submitted by the candidate.
                        </p>
                    </div>
                    <span class="badge badge-slate" style="font-size: 0.75rem; font-weight: 700;">CONFIDENTIAL DOSSIER</span>
                </div>

                @if($publications->isEmpty())
                    <div style="padding: 2rem; text-align: center; color: var(--color-text-muted); font-size: 0.9rem;">
                        No research publications recorded for this candidate profile.
                    </div>
                @else
                    <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                        @foreach($publications as $index => $pub)
                            @php
                                $verification = \App\Services\HecHjrsJournalService::verifyPublication($pub);
                            @endphp
                            <div style="padding: 1rem 1.25rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm);">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.75rem; flex-wrap: wrap;">
                                    <div style="flex: 1; min-width: 260px;">
                                        <div style="display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.25rem; flex-wrap: wrap;">
                                            <span style="font-weight: 800; font-size: 0.85rem; color: var(--primary);">#{{ $index + 1 }}</span>
                                            <span class="badge {{ $verification['is_predatory'] ? 'badge-rose' : ($verification['category'] === 'W' ? 'badge-emerald' : ($verification['category'] === 'X' ? 'badge-indigo' : 'badge-slate')) }}" style="font-size: 0.68rem; font-weight: 800;">
                                                {{ $verification['badge_text'] ?? ('CAT ' . ($pub->category ?: 'UNVERIFIED')) }}
                                            </span>
                                            @if($pub->publication_year)
                                                <span class="badge badge-slate" style="font-size: 0.68rem;">{{ $pub->publication_year }}</span>
                                            @endif
                                            @if($pub->author_role)
                                                <span class="badge badge-slate" style="font-size: 0.68rem;">{{ $pub->author_role }}</span>
                                            @endif
                                        </div>

                                        <h4 style="font-size: 0.98rem; font-weight: 800; color: var(--color-text-primary); margin: 0 0 0.35rem 0;">
                                            {{ $pub->title }}
                                        </h4>

                                        <div style="font-size: 0.83rem; color: var(--color-text-secondary); line-height: 1.45;">
                                            <span><strong>Journal:</strong> {{ $pub->journal_name }}</span>
                                            @if($pub->issn_print)
                                                • <span style="font-family: 'JetBrains Mono', monospace;">ISSN: {{ $pub->issn_print }}</span>
                                            @endif
                                            @if($pub->impact_factor)
                                                • <span>Impact Factor: <strong>{{ $pub->impact_factor }}</strong></span>
                                            @endif
                                        </div>

                                        @if($verification['warning'])
                                            <div style="margin-top: 0.35rem; font-size: 0.78rem; color: #ef4444; font-weight: 700;">
                                                ⚠️ {{ $verification['warning'] }}
                                            </div>
                                        @endif
                                    </div>

                                    @if($pub->doi_or_url)
                                        <a href="{{ str_starts_with($pub->doi_or_url, 'http') ? $pub->doi_or_url : 'https://doi.org/' . $pub->doi_or_url }}" 
                                           target="_blank" 
                                           class="btn btn-secondary btn-sm"
                                           style="font-size: 0.75rem; font-weight: 700; white-space: nowrap;">
                                            <span>View Paper / DOI ↗</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- CANDIDATE QUALIFICATIONS STRIP -->
            @if($degrees->isNotEmpty())
                <div class="card" style="padding: 1.5rem 1.75rem; background: var(--bg-card); border: 1px solid var(--border-color);">
                    <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--color-text-primary); margin: 0 0 0.85rem 0;">
                        🎓 Academic Degrees &amp; Qualifications
                    </h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 0.75rem;">
                        @foreach($degrees as $deg)
                            <div style="padding: 0.85rem 1rem; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm);">
                                <div style="font-weight: 800; font-size: 0.88rem; color: var(--color-text-primary);">
                                    {{ $deg->degree_level }} {{ $deg->degree_title ? '— ' . $deg->degree_title : '' }}
                                </div>
                                <div style="font-size: 0.78rem; color: var(--color-text-secondary); margin-top: 0.2rem;">
                                    {{ $deg->institution_name ?: 'Institution Recorded' }} ({{ $deg->passing_year }})
                                </div>
                                <div style="font-size: 0.75rem; font-weight: 700; color: var(--primary); margin-top: 0.25rem;">
                                    {{ $deg->cgpa_percentage ? 'CGPA/Marks: ' . $deg->cgpa_percentage : '' }} {{ $deg->division ? '• ' . $deg->division : '' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- STATUTORY REFEREE EVALUATION FORM -->
            <div class="card" style="padding: 2rem; background: var(--bg-card); border: 2px solid {{ $isSubmitted ? 'var(--border-color)' : 'var(--primary)' }}; box-shadow: var(--shadow-md);">
                <div style="border-bottom: 1.5px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                        <div>
                            <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary); margin: 0;">
                                ✍️ External Referee Assessment &amp; Quantitative Scoring
                            </h3>
                            <p style="font-size: 0.82rem; color: var(--color-text-secondary); margin: 0.2rem 0 0 0;">
                                Please review the candidate's publications and provide your quantitative grade and qualitative recommendation.
                            </p>
                        </div>
                        @if($isSubmitted)
                            <span class="badge badge-emerald" style="font-size: 0.8rem; font-weight: 800; padding: 0.4rem 0.8rem;">
                                ✓ VERDICT RECORDED ON {{ $review->submitted_at?->format('d M, Y') }}
                            </span>
                        @endif
                    </div>
                </div>

                <form wire:submit.prevent="submitEvaluation">
                    <!-- 1. Quantitative Score (0-100) -->
                    <div style="margin-bottom: 1.75rem;">
                        <label style="display: block; font-weight: 800; font-size: 0.95rem; color: var(--color-text-primary); margin-bottom: 0.4rem;">
                            1. Overall Academic &amp; Research Score (/100) <span style="color: #ef4444;">*</span>
                        </label>
                        <div style="font-size: 0.8rem; color: var(--color-text-secondary); margin-bottom: 0.65rem;">
                            Score based on original research contribution, publication quality, journal impact factors, and subject relevance.
                        </div>

                        <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                            <input type="number" 
                                   step="0.5" 
                                   min="0" 
                                   max="100" 
                                   wire:model="overall_score" 
                                   class="form-control" 
                                   placeholder="0 - 100"
                                   {{ $isSubmitted ? 'disabled' : '' }}
                                   style="width: 140px; font-size: 1.35rem; font-weight: 800; text-align: center; font-family: 'JetBrains Mono', monospace; padding: 0.5rem;">

                            @if(!$isSubmitted)
                                <div style="display: flex; gap: 0.35rem; flex-wrap: wrap; align-items: center;">
                                    <span style="font-size: 0.75rem; color: var(--color-text-muted); font-weight: 700; margin-right: 0.25rem;">Quick Chips:</span>
                                    @foreach([50, 60, 70, 75, 80, 85, 90, 95] as $chip)
                                        <button type="button" 
                                                wire:click="setQuickScore({{ $chip }})"
                                                class="btn btn-secondary btn-sm"
                                                style="font-size: 0.78rem; font-weight: 700; padding: 0.3rem 0.6rem; font-family: 'JetBrains Mono', monospace;">
                                            {{ $chip }}
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        @error('overall_score') <span style="color: #ef4444; font-size: 0.8rem; font-weight: 700; margin-top: 0.3rem; display: block;">{{ $message }}</span> @enderror
                    </div>

                    <!-- 2. Statutory Recommendation Decision -->
                    <div style="margin-bottom: 1.75rem;">
                        <label style="display: block; font-weight: 800; font-size: 0.95rem; color: var(--color-text-primary); margin-bottom: 0.4rem;">
                            2. Statutory Recommendation to the Selection Board <span style="color: #ef4444;">*</span>
                        </label>
                        <div style="font-size: 0.8rem; color: var(--color-text-secondary); margin-bottom: 0.65rem;">
                            Formal opinion regarding fitness for appointment to the post.
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0.75rem;">
                            <!-- Strongly Recommended -->
                            <button type="button" 
                                    wire:click="setQuickRecommendation('STRONGLY_RECOMMENDED')"
                                    {{ $isSubmitted ? 'disabled' : '' }}
                                    style="padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 2px solid {{ $recommendation === 'STRONGLY_RECOMMENDED' ? '#10b981' : 'var(--border-color)' }}; background: {{ $recommendation === 'STRONGLY_RECOMMENDED' ? 'rgba(16, 185, 129, 0.12)' : 'var(--bg-surface)' }}; text-align: left; cursor: pointer; transition: all 0.2s ease;">
                                <div style="font-weight: 800; font-size: 0.88rem; color: {{ $recommendation === 'STRONGLY_RECOMMENDED' ? '#10b981' : 'var(--color-text-primary)' }};">
                                    🌟 Strongly Recommended
                                </div>
                                <div style="font-size: 0.72rem; color: var(--color-text-secondary); margin-top: 0.2rem;">
                                    Outstanding academic dossier, exceptional publications.
                                </div>
                            </button>

                            <!-- Recommended -->
                            <button type="button" 
                                    wire:click="setQuickRecommendation('RECOMMENDED')"
                                    {{ $isSubmitted ? 'disabled' : '' }}
                                    style="padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 2px solid {{ $recommendation === 'RECOMMENDED' ? 'var(--primary)' : 'var(--border-color)' }}; background: {{ $recommendation === 'RECOMMENDED' ? 'rgba(99, 102, 241, 0.12)' : 'var(--bg-surface)' }}; text-align: left; cursor: pointer; transition: all 0.2s ease;">
                                <div style="font-weight: 800; font-size: 0.88rem; color: {{ $recommendation === 'RECOMMENDED' ? 'var(--primary)' : 'var(--color-text-primary)' }};">
                                    ✅ Recommended
                                </div>
                                <div style="font-size: 0.72rem; color: var(--color-text-secondary); margin-top: 0.2rem;">
                                    Fulfills all statutory standards with competent work.
                                </div>
                            </button>

                            <!-- Conditionally Recommended -->
                            <button type="button" 
                                    wire:click="setQuickRecommendation('CONDITIONALLY_RECOMMENDED')"
                                    {{ $isSubmitted ? 'disabled' : '' }}
                                    style="padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 2px solid {{ $recommendation === 'CONDITIONALLY_RECOMMENDED' ? '#f59e0b' : 'var(--border-color)' }}; background: {{ $recommendation === 'CONDITIONALLY_RECOMMENDED' ? 'rgba(245, 158, 11, 0.12)' : 'var(--bg-surface)' }}; text-align: left; cursor: pointer; transition: all 0.2s ease;">
                                <div style="font-weight: 800; font-size: 0.88rem; color: {{ $recommendation === 'CONDITIONALLY_RECOMMENDED' ? '#f59e0b' : 'var(--color-text-primary)' }};">
                                    ⚠️ With Reservations
                                </div>
                                <div style="font-size: 0.72rem; color: var(--color-text-secondary); margin-top: 0.2rem;">
                                    Marginal suitability; subject to board clarification.
                                </div>
                            </button>

                            <!-- Not Recommended -->
                            <button type="button" 
                                    wire:click="setQuickRecommendation('NOT_RECOMMENDED')"
                                    {{ $isSubmitted ? 'disabled' : '' }}
                                    style="padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 2px solid {{ $recommendation === 'NOT_RECOMMENDED' ? '#ef4444' : 'var(--border-color)' }}; background: {{ $recommendation === 'NOT_RECOMMENDED' ? 'rgba(239, 68, 68, 0.12)' : 'var(--bg-surface)' }}; text-align: left; cursor: pointer; transition: all 0.2s ease;">
                                <div style="font-weight: 800; font-size: 0.88rem; color: {{ $recommendation === 'NOT_RECOMMENDED' ? '#ef4444' : 'var(--color-text-primary)' }};">
                                    ❌ Not Recommended
                                </div>
                                <div style="font-size: 0.72rem; color: var(--color-text-secondary); margin-top: 0.2rem;">
                                    Does not meet statutory academic requirements.
                                </div>
                            </button>
                        </div>
                        @error('recommendation') <span style="color: #ef4444; font-size: 0.8rem; font-weight: 700; margin-top: 0.3rem; display: block;">{{ $message }}</span> @enderror
                    </div>

                    <!-- 3. Qualitative Evaluation Report -->
                    <div style="margin-bottom: 1.75rem;">
                        <label style="display: block; font-weight: 800; font-size: 0.95rem; color: var(--color-text-primary); margin-bottom: 0.4rem;">
                            3. Comprehensive Qualitative Evaluation Report <span style="color: #ef4444;">*</span>
                        </label>
                        <div style="font-size: 0.8rem; color: var(--color-text-secondary); margin-bottom: 0.65rem;">
                            Detailed critical appraisal of the candidate's research focus, methodology, originality, and impact.
                        </div>

                        <textarea wire:model="report_text" 
                                  rows="6" 
                                  class="form-control" 
                                  placeholder="Please provide your professional academic assessment of the publications and research standing of the candidate..."
                                  {{ $isSubmitted ? 'disabled' : '' }}
                                  style="width: 100%; font-size: 0.92rem; line-height: 1.6; padding: 0.85rem; border-radius: var(--radius-sm);"></textarea>
                        @error('report_text') <span style="color: #ef4444; font-size: 0.8rem; font-weight: 700; margin-top: 0.3rem; display: block;">{{ $message }}</span> @enderror
                    </div>

                    <!-- 4. Optional Signed Report Attachment -->
                    @if(!$isSubmitted)
                        <div style="margin-bottom: 1.75rem;">
                            <label style="display: block; font-weight: 800; font-size: 0.9rem; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                                4. Signed Official Referee Report Document (Optional PDF / DOC)
                            </label>
                            <div style="font-size: 0.78rem; color: var(--color-text-secondary); margin-bottom: 0.5rem;">
                                You may optionally attach your signed formal review on university letterhead (Max 10 MB).
                            </div>
                            <input type="file" wire:model="evaluation_file" accept=".pdf,.doc,.docx" class="form-control" style="font-size: 0.85rem;">
                            @error('evaluation_file') <span style="color: #ef4444; font-size: 0.8rem; font-weight: 700; margin-top: 0.3rem; display: block;">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <!-- Submit / Locked Strip -->
                    <div style="border-top: 1px solid var(--border-color); padding-top: 1.25rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                        <div style="font-size: 0.78rem; color: var(--color-text-muted); display: flex; align-items: center; gap: 0.4rem;">
                            <span>🔒 Encrypted TLS Transmission</span> • <span>Statutory Confidentiality Enforced</span>
                        </div>

                        @if(!$isSubmitted)
                            <button type="submit" 
                                    class="btn btn-primary"
                                    style="padding: 0.75rem 2rem; font-weight: 800; font-size: 0.95rem; box-shadow: 0 4px 14px var(--primary-glow); display: inline-flex; align-items: center; gap: 0.5rem;">
                                <span wire:loading.remove wire:target="submitEvaluation">Submit Confidential Evaluation Report</span>
                                <span wire:loading wire:target="submitEvaluation">Securing &amp; Transmitting...</span>
                            </button>
                        @else
                            <div style="display: inline-flex; align-items: center; gap: 0.5rem; color: #10b981; font-weight: 800; font-size: 0.95rem;">
                                <span>✓ Formal Report Submitted &amp; Locked</span>
                            </div>
                        @endif
                    </div>
                </form>
            </div>

        </div>
    @endif

</div>
