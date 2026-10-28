<div class="flex-col gap-6">
    <!-- Header Banner -->
    <div class="applicant-hero">
        <div class="flex justify-between align-center" style="flex-wrap: wrap; gap: 1.5rem;">
            <div class="flex align-center gap-4">
                <div class="applicant-avatar" style="background: linear-gradient(135deg, #0891b2, #4f46e5);">
                    {{ strtoupper(substr($reviewerProfile?->name ?: auth()->user()->email, 0, 1)) }}
                </div>
                <div>
                    <div class="flex align-center gap-2">
                        <h1 style="font-size: 1.6rem; font-weight: 800; letter-spacing: -0.03em; margin: 0; color: var(--color-text-primary);">
                            {{ $reviewerProfile?->name ?? auth()->user()->email }}
                        </h1>
                        <span class="badge badge-appealed" style="font-size: 0.72rem; font-weight: 800;">
                            {{ $reviewerProfile?->subject ?? 'ACADEMIC' }} EXPERT
                        </span>
                    </div>

                    <div class="flex align-center gap-3 mt-1" style="font-size: 0.85rem; color: var(--color-text-secondary); flex-wrap: wrap;">
                        <span>{{ $reviewerProfile?->academic_rank }}</span>
                        <span>•</span>
                        <span>{{ $reviewerProfile?->university }}</span>
                        <span>•</span>
                        <code style="color: var(--secondary);">{{ auth()->user()->email }}</code>
                    </div>
                </div>
            </div>

            <!-- Stats Badge -->
            <div class="flex gap-3">
                <div style="background: var(--bg-card-solid); border: 1px solid var(--border-color); padding: 0.75rem 1.25rem; border-radius: var(--radius-sm); text-align: center;">
                    <span style="font-size: 0.72rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">Pending</span>
                    <div style="font-size: 1.4rem; font-weight: 800; color: var(--warning);">{{ $pendingCount }}</div>
                </div>

                <div style="background: var(--bg-card-solid); border: 1px solid var(--border-color); padding: 0.75rem 1.25rem; border-radius: var(--radius-sm); text-align: center;">
                    <span style="font-size: 0.72rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">Completed</span>
                    <div style="font-size: 1.4rem; font-weight: 800; color: var(--success);">{{ $completedCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Message Notification -->
    @if (session()->has('message'))
        <div class="card" style="background: rgba(16, 185, 129, 0.1); border-color: var(--success); padding: 1rem 1.25rem;">
            <div class="flex align-center gap-3">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <div style="flex: 1;">
                    <strong style="color: #6ee7b7; font-size: 0.95rem;">Review Dispatched</strong>
                    <p style="color: var(--color-text-secondary); font-size: 0.88rem; margin: 0.15rem 0 0;">{{ session('message') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Segmented Filter Tabs -->
    <div class="filter-pills">
        <button type="button" class="filter-pill {{ $activeTab === 'pending' ? 'active' : '' }}" wire:click="$set('activeTab', 'pending')">
            <span class="badge-dot" style="background: var(--warning);"></span>
            <span>Awaiting Evaluation</span>
            <span class="filter-pill-count">{{ $pendingCount }}</span>
        </button>

        <button type="button" class="filter-pill {{ $activeTab === 'completed' ? 'active' : '' }}" wire:click="$set('activeTab', 'completed')">
            <span class="badge-dot" style="background: var(--success);"></span>
            <span>Completed Reviews</span>
            <span class="filter-pill-count">{{ $completedCount }}</span>
        </button>
    </div>

    <!-- Assigned Dossiers Table -->
    <div class="card">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Candidate Dossier</th>
                        <th>Target Position</th>
                        <th>Discipline</th>
                        <th>Assigned Date</th>
                        <th>Deadline</th>
                        <th>Review State</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $rev)
                        <tr wire:key="eval-row-{{ $rev->id }}">
                            <td>
                                <strong style="color: var(--color-text-primary); font-size: 0.95rem;">
                                    {{ $rev->application->user->basicInfo ? trim($rev->application->user->basicInfo->first_name . ' ' . $rev->application->user->basicInfo->last_name) : 'Academic Candidate' }}
                                </strong>
                            </td>
                            <td>
                                <div class="flex-col">
                                    <strong style="color: var(--color-text-primary); font-size: 0.92rem;">{{ $rev->application->campaign->title }}</strong>
                                    <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: var(--primary);">
                                        {{ $rev->application->campaign->job_code }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-appealed">{{ $rev->application->campaign->department }}</span>
                            </td>
                            <td>
                                <span style="font-size: 0.84rem; color: var(--color-text-muted);">
                                    {{ $rev->assigned_at?->format('d M Y') }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size: 0.84rem; font-weight: 700; color: {{ $rev->due_date?->isPast() ? 'var(--danger)' : 'var(--warning)' }};">
                                    {{ $rev->due_date?->format('d M Y') ?: 'In 14 days' }}
                                </span>
                            </td>
                            <td>
                                @if($rev->status === 'SUBMITTED')
                                    <span class="badge badge-paid">
                                        <span class="badge-dot" style="background: var(--success);"></span>
                                        Report Submitted ({{ $rev->overall_score }}/100)
                                    </span>
                                @else
                                    <span class="badge badge-pending">
                                        <span class="badge-dot" style="background: var(--warning);"></span>
                                        Pending Evaluation
                                    </span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary btn-sm" wire:click="openEvalModal({{ $rev->id }})">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                    </svg>
                                    <span>{{ $rev->status === 'SUBMITTED' ? 'View Evaluation' : 'Evaluate Dossier' }}</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center" style="padding: 3rem 1.5rem; color: var(--color-text-muted);">
                                No candidacies currently in your evaluation queue.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- EVALUATION DRAWER / MODAL                                      -->
    <!-- ============================================================== -->
    @if($showEvalModal && $activeReview && $candidateApplication)
        <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 1.5rem;">
            <div class="card" style="width: 100%; max-width: 960px; max-height: 92vh; display: flex; flex-direction: column; border-color: var(--border-color); box-shadow: var(--shadow-lg); background: var(--bg-card-solid); overflow: hidden;">
                <!-- Header -->
                <div class="flex justify-between align-center" style="padding: 1.25rem 1.75rem; border-bottom: 1px solid var(--border-color);">
                    <div>
                        <h3 class="card-title" style="margin: 0; font-size: 1.2rem;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--secondary)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                            Peer Review Dossier: {{ $candidateApplication->campaign->title }}
                        </h3>
                        <p class="card-subtitle" style="margin: 0.2rem 0 0;">
                            Candidate: <strong>{{ $candidateApplication->user->basicInfo ? trim($candidateApplication->user->basicInfo->first_name . ' ' . $candidateApplication->user->basicInfo->last_name) : 'Candidate' }}</strong> • Department: <strong>{{ $candidateApplication->campaign->department }}</strong>
                        </p>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="closeEvalModal">✕</button>
                </div>

                <!-- Body Scrollable Content -->
                <div style="padding: 1.5rem; overflow-y: auto; flex: 1;">
                    <!-- Candidate Credentials Summary -->
                    <div class="card mb-4" style="background: var(--bg-surface); border-color: var(--border-color); padding: 1.25rem;">
                        <h4 style="color: var(--secondary); font-size: 0.95rem; font-weight: 700; margin-bottom: 0.75rem;">
                            🎓 Academic Qualifications & Degrees
                        </h4>
                        <div class="flex-col gap-2">
                            @forelse($candidateApplication->user->basicInfo?->degrees ?? [] as $deg)
                                <div style="background: var(--bg-card-solid); padding: 0.65rem 0.85rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);" class="flex justify-between align-center">
                                    <div>
                                        <strong style="color: var(--color-text-primary); font-size: 0.88rem;">{{ $deg->degree_level }}: {{ $deg->degree_title }}</strong>
                                        <div style="font-size: 0.78rem; color: var(--color-text-secondary);">
                                            {{ $deg->institution }} ({{ $deg->passing_year }}) • Division/CGPA: {{ $deg->division_cgpa }}
                                        </div>
                                    </div>
                                    @if($deg->proof_path)
                                        <a href="/storage/{{ $deg->proof_path }}" target="_blank" class="btn btn-secondary btn-sm" style="font-size: 0.72rem;">View Degree PDF</a>
                                    @endif
                                </div>
                            @empty
                                <p style="font-size: 0.82rem; color: var(--color-text-muted); margin: 0;">No degrees registered.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Peer-Reviewed Publications (Core Academic Milestone) -->
                    <div class="card mb-4" style="background: var(--bg-surface); border-color: var(--border-color); padding: 1.25rem;">
                        <div class="flex justify-between align-center mb-2">
                            <h4 style="color: var(--primary); font-size: 0.95rem; font-weight: 700; margin: 0;">
                                📚 Peer-Reviewed Research Publications ({{ count($candidateApplication->user->basicInfo?->publications ?? []) }})
                            </h4>
                            <span style="font-size: 0.75rem; color: var(--color-text-muted);">HEC W/X/Y Categories</span>
                        </div>

                        <div class="flex-col gap-2">
                            @forelse($candidateApplication->user->basicInfo?->publications ?? [] as $pub)
                                <div style="background: var(--bg-card-solid); padding: 0.75rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                                    <div class="flex justify-between align-center mb-1">
                                        <strong style="color: var(--color-text-primary); font-size: 0.9rem;">{{ $pub->title }}</strong>
                                        <span class="badge badge-appealed" style="font-size: 0.68rem;">{{ $pub->hec_category }}</span>
                                    </div>
                                    <div style="font-size: 0.8rem; color: var(--color-text-secondary); margin-bottom: 0.35rem;">
                                        Journal: <em>{{ $pub->journal_name }}</em> • ISSN: {{ $pub->issn }} • Impact Factor: {{ $pub->impact_factor ?? 'N/A' }} • Year: {{ $pub->publication_year }}
                                    </div>
                                    @if($pub->url || $pub->proof_path)
                                        <div class="flex gap-2 mt-2">
                                            @if($pub->url)
                                                <a href="{{ $pub->url }}" target="_blank" class="btn btn-secondary btn-sm" style="font-size: 0.72rem;">DOI / Online URL</a>
                                            @endif
                                            @if($pub->proof_path)
                                                <a href="/storage/{{ $pub->proof_path }}" target="_blank" class="btn btn-secondary btn-sm" style="font-size: 0.72rem;">Paper PDF</a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <p style="font-size: 0.82rem; color: var(--color-text-muted); margin: 0;">No peer-reviewed publications listed.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Reviewer Evaluation Form -->
                    <div class="card" style="border-color: var(--secondary); background: rgba(6, 182, 212, 0.04); padding: 1.5rem;">
                        <h4 style="color: var(--color-text-primary); font-size: 1.05rem; font-weight: 800; margin-bottom: 1rem;">
                            📝 Your Confidential Peer Review & Recommendation
                        </h4>

                        <form wire:submit.prevent="submitEvaluation">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <x-input label="Overall Academic Suitability Score (0 - 100) *" :error="$errors->first('overall_score')">
                                    <input type="number" step="0.1" min="0" max="100" class="form-control" wire:model="overall_score" placeholder="e.g. 85.0">
                                </x-input>

                                <x-select label="Final Expert Recommendation *" :error="$errors->first('recommendation')">
                                    <select class="form-control form-select" wire:model="recommendation">
                                        <option value="">-- Choose Recommendation --</option>
                                        <option value="STRONGLY_RECOMMENDED">Strongly Recommended (Exceptional Credentials)</option>
                                        <option value="RECOMMENDED">Recommended (Meets all Criteria)</option>
                                        <option value="CONDITIONALLY_RECOMMENDED">Conditionally Recommended</option>
                                        <option value="NOT_RECOMMENDED">Not Recommended (Criteria Deficit)</option>
                                    </select>
                                </x-select>
                            </div>

                            <div class="form-group mb-4">
                                <label class="form-label">Qualitative Expert Evaluation Report * (Min 30 characters)</label>
                                <textarea class="form-control" rows="5" wire:model="report_text" placeholder="Detail your evaluation of the candidate's research papers, citations, HEC journal compliance, originality, and overall suitability for the academic post..."></textarea>
                                @error('report_text') <span style="color: var(--danger); font-size: 0.82rem; margin-top: 0.35rem; display: block; font-weight: 500;">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label class="form-label">Upload Signed Evaluation Sheet / Formal Letter (Optional, PDF max 5MB)</label>
                                <input type="file" class="form-control" wire:model="evaluation_file">
                                @error('evaluation_file') <span style="color: var(--danger); font-size: 0.82rem; margin-top: 0.35rem; display: block; font-weight: 500;">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex justify-between align-center" style="border-top: 1px solid var(--border-color); padding-top: 1rem;">
                                <button type="button" class="btn btn-secondary" wire:click="closeEvalModal">Cancel</button>
                                <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="submitEvaluation">Submit Confidential Evaluation Report</span>
                                    <span wire:loading wire:target="submitEvaluation">Transmitting Report...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
