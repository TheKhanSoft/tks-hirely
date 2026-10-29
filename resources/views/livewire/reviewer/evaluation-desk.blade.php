<div class="flex-col gap-6">
    <!-- Header Banner -->
    <div class="page-hero-header">
        <div class="flex align-center gap-4">
            <div style="width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg, #0891b2, #4f46e5); color: #fff; font-weight: 800; font-size: 1.25rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 14px rgba(8, 145, 178, 0.4); border: 2px solid rgba(255,255,255,0.2);">
                {{ strtoupper(substr($reviewerProfile?->name ?: auth()->user()->email, 0, 1)) }}
            </div>
            <div>
                <div class="flex align-center gap-2">
                    <h1 style="font-size: 1.55rem; font-weight: 800; letter-spacing: -0.03em; margin: 0; color: var(--color-text-primary);">
                        {{ $reviewerProfile?->name ?? auth()->user()->email }}
                    </h1>
                    <span class="badge" style="background: rgba(6,182,212,0.15); color: var(--secondary); border: 1px solid rgba(6,182,212,0.3); font-size: 0.72rem; font-weight: 800;">
                        {{ $reviewerProfile?->subject ?? 'ACADEMIC' }} EXPERT
                    </span>
                </div>

                <div class="flex align-center gap-2 mt-1" style="font-size: 0.85rem; color: var(--color-text-secondary); flex-wrap: wrap;">
                    <span>{{ $reviewerProfile?->academic_rank }}</span>
                    <span>•</span>
                    <span>{{ $reviewerProfile?->university }}</span>
                    <span>•</span>
                    <code style="color: var(--secondary); font-size: 0.8rem;">{{ auth()->user()->email }}</code>
                </div>
            </div>
        </div>

        <!-- Quick Stats Cards -->
        <div class="flex gap-3">
            <div style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); padding: 0.65rem 1.15rem; border-radius: var(--radius-md); text-align: center; min-width: 90px;">
                <span style="font-size: 0.72rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">Awaiting</span>
                <div style="font-size: 1.45rem; font-weight: 800; color: var(--warning);">{{ $pendingCount }}</div>
            </div>

            <div style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); padding: 0.65rem 1.15rem; border-radius: var(--radius-md); text-align: center; min-width: 90px;">
                <span style="font-size: 0.72rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">Completed</span>
                <div style="font-size: 1.45rem; font-weight: 800; color: var(--success);">{{ $completedCount }}</div>
            </div>
        </div>
    </div>

    <!-- Flash Message Notification -->
    @if (session()->has('message'))
        <div class="alert alert-success mb-6" style="display: flex; align-items: center; gap: 0.75rem; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3); color: var(--success); padding: 0.85rem 1.25rem; border-radius: var(--radius-md);">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <div style="flex: 1;">
                <strong style="color: #6ee7b7; font-size: 0.95rem;">Review Dispatched:</strong>
                <span style="font-size: 0.88rem; margin-left: 0.35rem;">{{ session('message') }}</span>
            </div>
        </div>
    @endif

    <!-- Segmented Filter Tabs -->
    <div style="display: flex; gap: 0.5rem; background: var(--bg-tertiary); padding: 0.35rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color); width: fit-content; margin-bottom: 1.5rem;">
        <button type="button" 
                class="btn" 
                wire:click="$set('activeTab', 'pending')"
                style="padding: 0.5rem 1.25rem; font-size: 0.875rem; border-radius: var(--radius-md); font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; transition: var(--transition-base); {{ $activeTab === 'pending' ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--warning);"></span>
            <span>Awaiting Evaluation</span>
            <span style="font-size: 0.75rem; padding: 0.15rem 0.5rem; border-radius: 999px; background: rgba(245,158,11,0.15); color: var(--warning);">{{ $pendingCount }}</span>
        </button>

        <button type="button" 
                class="btn" 
                wire:click="$set('activeTab', 'completed')"
                style="padding: 0.5rem 1.25rem; font-size: 0.875rem; border-radius: var(--radius-md); font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; transition: var(--transition-base); {{ $activeTab === 'completed' ? 'background: var(--bg-card); color: var(--color-text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);' : 'background: transparent; color: var(--color-text-secondary); border: 1px solid transparent;' }}">
            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--success);"></span>
            <span>Completed Reviews</span>
            <span style="font-size: 0.75rem; padding: 0.15rem 0.5rem; border-radius: 999px; background: rgba(16,185,129,0.15); color: var(--success);">{{ $completedCount }}</span>
        </button>
    </div>

    <!-- Assigned Dossiers Table -->
    <x-table :headers="['Candidate Dossier', 'Target Position', 'Discipline / Field', 'Assigned Date', 'Evaluation Deadline', 'Review Status', 'Action']">
        @forelse($reviews as $rev)
            <tr wire:key="eval-row-{{ $rev->id }}">
                <td>
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        @if($rev->application->user->basicInfo?->profile_picture_url)
                            <div style="width: 34px; height: 34px; border-radius: 50%; overflow: hidden; flex-shrink: 0; border: 1px solid var(--border-color);">
                                <img src="{{ $rev->application->user->basicInfo->profile_picture_url }}" alt="Candidate" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        @else
                            <div style="width: 34px; height: 34px; border-radius: 50%; background: rgba(6,182,212,0.15); color: var(--secondary); font-weight: 800; font-size: 0.8rem; display: flex; align-items: center; justify-content: center;">
                                {{ strtoupper(substr($rev->application->user->basicInfo?->first_name ?: 'C', 0, 1)) }}
                            </div>
                        @endif
                        <strong style="color: var(--color-text-primary); font-size: 0.95rem;">
                            {{ $rev->application->user->basicInfo ? trim($rev->application->user->basicInfo->first_name . ' ' . $rev->application->user->basicInfo->last_name) : 'Academic Candidate' }}
                        </strong>
                    </div>
                </td>
                <td>
                    <div>
                        <strong style="color: var(--color-text-primary); font-size: 0.92rem;">{{ $rev->application->campaign->title }}</strong><br>
                        <code style="font-size: 0.75rem; color: var(--primary);">
                            {{ $rev->application->campaign->job_code }}
                        </code>
                    </div>
                </td>
                <td>
                    <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary); border: 1px solid rgba(99,102,241,0.25); font-weight: 700;">
                        {{ $rev->application->campaign->department }}
                    </span>
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
                        <span class="badge" style="background: rgba(16,185,129,0.15); color: var(--success); font-weight: 700;">
                            ✓ Report Submitted ({{ $rev->overall_score }}/100)
                        </span>
                    @else
                        <span class="badge" style="background: rgba(245,158,11,0.15); color: var(--warning); font-weight: 700;">
                            ⏳ Pending Evaluation
                        </span>
                    @endif
                </td>
                <td>
                    <button type="button" class="btn btn-primary btn-sm" wire:click="openEvalModal({{ $rev->id }})" style="display: inline-flex; align-items: center; gap: 0.4rem;">
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
    </x-table>

    <!-- ============================================================== -->
    <!-- EVALUATION DRAWER / MODAL                                      -->
    <!-- ============================================================== -->
    @if($showEvalModal && $activeReview && $candidateApplication)
        <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 1.5rem;">
            <div class="card" style="width: 100%; max-width: 960px; max-height: 92vh; display: flex; flex-direction: column; border-color: var(--border-color); box-shadow: var(--shadow-lg); background: var(--bg-card-solid); overflow: hidden;">
                <!-- Header -->
                <div class="flex justify-between align-center" style="padding: 1.25rem 1.75rem; border-bottom: 1px solid var(--border-color);">
                    <div>
                        <h3 class="card-title" style="margin: 0; font-size: 1.2rem;">
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
                                        <strong style="color: var(--color-text-primary); font-size: 0.88rem;">{{ $deg['degree_level'] ?? 'Degree' }}: {{ $deg['degree_title'] }}</strong>
                                        <div style="font-size: 0.78rem; color: var(--color-text-secondary);">
                                            {{ $deg['institution'] }} ({{ $deg['year'] }}) • Marks/GPA: {{ $deg['gpa'] ?? 'N/A' }} / {{ $deg['scale'] ?? 'N/A' }} • Division: {{ $deg['division'] ?? 'N/A' }}
                                        </div>
                                    </div>
                                    @if(isset($deg['proof_path']))
                                        <a href="/storage/{{ $deg['proof_path'] }}" target="_blank" class="btn btn-secondary btn-sm" style="font-size: 0.72rem; text-decoration: none;">View Degree PDF</a>
                                    @endif
                                </div>
                            @empty
                                <p style="font-size: 0.82rem; color: var(--color-text-muted); margin: 0;">No degrees registered.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Peer-Reviewed Publications -->
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
                                        <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary); font-size: 0.68rem; font-weight: 700;">{{ $pub->hec_category }}</span>
                                    </div>
                                    <div style="font-size: 0.8rem; color: var(--color-text-secondary); margin-bottom: 0.35rem;">
                                        Journal: <em>{{ $pub->journal_name }}</em> • ISSN: {{ $pub->issn }} • Impact Factor: {{ $pub->impact_factor ?? 'N/A' }} • Year: {{ $pub->publication_year }}
                                    </div>
                                    @if($pub->url || $pub->proof_path)
                                        <div class="flex gap-2 mt-2">
                                            @if($pub->url)
                                                <a href="{{ $pub->url }}" target="_blank" class="btn btn-secondary btn-sm" style="font-size: 0.72rem; text-decoration: none;">DOI / Online URL</a>
                                            @endif
                                            @if($pub->proof_path)
                                                <a href="/storage/{{ $pub->proof_path }}" target="_blank" class="btn btn-secondary btn-sm" style="font-size: 0.72rem; text-decoration: none;">Paper PDF</a>
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
                    <div class="card" style="border-color: var(--secondary-glow); background: rgba(6, 182, 212, 0.04); padding: 1.5rem;">
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
                                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
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
