@php
    $letterhead = \App\Models\SystemSetting::getLetterheadDetails();
    $letterFooter = \App\Models\SystemSetting::getLetterFooter();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consensus Peer Review Report — {{ $application->tracking_number }} — {{ $campaign->title }}</title>
    
    <!-- Modern Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Times New Roman', Times, Georgia, serif;
            color: #0f172a;
            background: #f8fafc;
            line-height: 1.45;
            font-size: 9.8pt;
        }

        .no-print-toolbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: #0f172a;
            color: #ffffff;
            padding: 0.75rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 14px rgba(0,0,0,0.25);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .btn-toolbar {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
        }

        .btn-toolbar-primary {
            background: #4f46e5;
            color: #ffffff;
        }
        .btn-toolbar-secondary {
            background: rgba(255,255,255,0.12);
            color: #ffffff;
            border-color: rgba(255,255,255,0.2);
        }

        .document-wrapper {
            max-width: 900px;
            margin: 1.5rem auto 3rem;
            background: #ffffff;
            padding: 2.25rem 2.5rem;
            box-shadow: 0 4px 25px rgba(0,0,0,0.08);
            border: 1px solid #cbd5e1;
            border-radius: 4px;
        }

        .official-header {
            text-align: center;
            border-bottom: 2.5px solid #0f172a;
            padding-bottom: 1rem;
            margin-bottom: 1.25rem;
        }

        .official-header .org-title {
            font-size: 1.35rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #0f172a;
        }

        .official-header .doc-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            margin-top: 0.35rem;
        }

        .official-header .statutory-sub {
            font-size: 0.85rem;
            color: #64748b;
            font-style: italic;
            margin-top: 0.2rem;
        }

        .particulars-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.5rem;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 0.75rem 1rem;
            margin-bottom: 1.25rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.82rem;
        }

        .particular-item span {
            display: block;
            font-size: 0.68rem;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 700;
        }
        .particular-item strong {
            font-size: 0.88rem;
            color: #0f172a;
        }

        /* Executive Consensus Seal Banner */
        .consensus-seal-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
            border: 2px solid #4f46e5;
            border-radius: 8px;
            padding: 1.15rem 1.5rem;
            margin-bottom: 1.5rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .section-heading {
            font-size: 0.95rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-bottom: 1.5px solid #0f172a;
            padding-bottom: 0.35rem;
            margin: 1.5rem 0 0.85rem 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .reviewer-dossier-card {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.25rem;
            background: #ffffff;
            page-break-inside: avoid;
        }

        .reviewer-meta {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.65rem;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 0.5rem;
        }

        .quote-block {
            background: #f8fafc;
            border-left: 3px solid #4f46e5;
            padding: 0.65rem 0.9rem;
            font-style: italic;
            color: #1e293b;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        .signatures-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-top: 3rem;
            text-align: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
            page-break-inside: avoid;
        }

        .sig-line {
            border-top: 1.5px solid #0f172a;
            padding-top: 0.4rem;
            font-size: 0.8rem;
            font-weight: 700;
        }

        @media print {
            .no-print-toolbar {
                display: none !important;
            }
            body {
                background: #ffffff;
            }
            .document-wrapper {
                box-shadow: none;
                border: none;
                padding: 0;
                margin: 0;
                max-width: 100%;
            }
            @page {
                size: A4 portrait;
                margin: 12mm 15mm;
            }
        }
    </style>
</head>
<body>

    <!-- On-screen Action Toolbar -->
    <div class="no-print-toolbar">
        <div>
            <strong>External Peer Review Consensus Certificate</strong> — {{ $application->tracking_number }}
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <button type="button" onclick="window.print()" class="btn-toolbar btn-toolbar-primary">
                🖨️ Print Official Certificate
            </button>
            <button type="button" onclick="window.close()" class="btn-toolbar btn-toolbar-secondary">
                ✕ Close
            </button>
        </div>
    </div>

    <div class="document-wrapper">
        
        <!-- Official University Letterhead -->
        <div class="official-header">
            @if(!empty($letterhead['logo']))
                <div style="margin-bottom: 0.5rem;">
                    <img src="{{ $letterhead['logo'] }}" alt="{{ $letterhead['heading'] }}" style="max-height: 52px; max-width: 52px; object-fit: contain;">
                </div>
            @endif
            <div class="org-title">{{ $letterhead['heading'] }}</div>
            @if(!empty($letterhead['subheading']))
                <div style="font-size: 0.85rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 0.15rem;">{{ $letterhead['subheading'] }}</div>
            @endif
            @if(!empty($letterhead['office']))
                <div style="font-size: 0.82rem; font-weight: 800; color: #2563eb; text-transform: uppercase; letter-spacing: 0.04em; margin-top: 0.15rem;">{{ $letterhead['office'] }}</div>
            @endif
            <div class="doc-title">CONFIDENTIAL EXTERNAL PEER REVIEW EVALUATION CERTIFICATE</div>
            <div class="statutory-sub">Issued pursuant to HEC Faculty Appointment Criteria & Statutory Selection Framework</div>
            @if(!empty($letterhead['custom_details']))
                <div style="font-size: 0.76rem; color: #64748b; margin-top: 0.25rem;">
                    {!! nl2br(e($letterhead['custom_details'])) !!}
                </div>
            @endif
        </div>

        <!-- Candidate & Position Meta Grid -->
        <div class="particulars-grid">
            <div class="particular-item">
                <span>Tracking ID</span>
                <strong style="font-family: 'JetBrains Mono', monospace;">{{ $application->tracking_number }}</strong>
            </div>
            <div class="particular-item">
                <span>Candidate Name</span>
                <strong>{{ $application->user->basicInfo ? trim($application->user->basicInfo->first_name . ' ' . $application->user->basicInfo->last_name) : $application->user->email }}</strong>
            </div>
            <div class="particular-item">
                <span>CNIC / Passport</span>
                <strong style="font-family: 'JetBrains Mono', monospace;">{{ $application->user->basicInfo?->cnic_number ?? 'N/A' }}</strong>
            </div>
            <div class="particular-item">
                <span>Recruitment Track</span>
                <strong>{{ $campaign->track_type }}</strong>
            </div>

            <div class="particular-item">
                <span>Position Applied For</span>
                <strong>{{ $campaign->title }}</strong>
            </div>
            <div class="particular-item">
                <span>Job Code & Scale</span>
                <strong>{{ $campaign->job_code }}</strong>
            </div>
            <div class="particular-item">
                <span>Department / Faculty</span>
                <strong>{{ $campaign->department }}</strong>
            </div>
            <div class="particular-item">
                <span>Advertisement Reference</span>
                <strong>{{ $advertisement ? "Ad #{$advertisement->advertisement_no} ({$advertisement->year})" : 'Statutory Drive' }}</strong>
            </div>
        </div>

        <!-- Consensus Dossier Assessment Banner -->
        @php
            $passed = $verdict['passed'] ?? ($avgScore >= 60);
        @endphp
        <div class="consensus-seal-card">
            <div>
                <div style="font-size: 0.72rem; text-transform: uppercase; color: #4f46e5; font-weight: 800; letter-spacing: 0.05em;">
                    CONSENSUS EVALUATION VERDICT
                </div>
                <div style="font-size: 1.25rem; font-weight: 900; color: #0f172a; margin-top: 0.2rem;">
                    {{ $submittedReviews->count() }} of {{ $reviews->count() }} External Peer Evaluation Reports Received
                </div>
                <div style="font-size: 0.82rem; color: #475569; margin-top: 0.25rem;">
                    Statutory Turnout: <strong>{{ $reviews->count() > 0 ? round(($submittedReviews->count() / $reviews->count()) * 100) : 0 }}%</strong> • Required Quota: <strong>{{ $campaign->constitution?->required_peer_reviewers ?? 3 }} Referees</strong>
                </div>
            </div>

            <div style="text-align: right;">
                <div style="font-size: 0.72rem; text-transform: uppercase; color: #64748b; font-weight: 800;">Average Peer Score</div>
                <div style="font-size: 2.1rem; font-weight: 900; color: {{ $avgScore >= 80 ? '#059669' : ($avgScore >= 60 ? '#d97706' : '#dc2626') }}; line-height: 1;">
                    {{ number_format($avgScore, 1) }}<span style="font-size: 1rem; color: #64748b; font-weight: 700;"> / 100</span>
                </div>
                <div style="margin-top: 0.35rem; display: inline-block; padding: 0.25rem 0.65rem; border-radius: 4px; font-size: 0.75rem; font-weight: 800; background: {{ $passed ? 'rgba(16,185,129,0.15)' : 'rgba(239,68,68,0.15)' }}; color: {{ $passed ? '#047857' : '#b91c1c' }};">
                    {{ $passed ? '✓ CLEARED FOR SELECTION BOARD' : '✗ BENCHMARK NOT MET' }}
                </div>
            </div>
        </div>

        <!-- Section: Individual Empanelled Referees Evaluation Reports -->
        <div class="section-heading">
            <span>Independent External Peer Evaluation Ledger</span>
            <span style="font-size: 0.78rem; font-weight: 600; text-transform: none; color: #64748b;">(Confidential Blind Review Protocol)</span>
        </div>

        @forelse($reviews as $idx => $r)
            @php
                $prof = $r->reviewer?->reviewerProfile;
            @endphp
            <div class="reviewer-dossier-card">
                <div class="reviewer-meta">
                    <div>
                        <div style="font-size: 0.95rem; font-weight: 800; color: #0f172a;">
                            Referee #{{ $idx + 1 }}: {{ $prof?->name ?? 'External Referee' }}
                        </div>
                        <div style="font-size: 0.8rem; color: #475569;">
                            {{ $prof?->academic_rank }} • {{ $prof?->university }} • {{ $prof?->country }}
                        </div>
                        <div style="font-size: 0.74rem; color: #64748b;">
                            Discipline: <strong>{{ $prof?->subject }}</strong> ({{ $prof?->specialization ?: 'Specialist' }})
                        </div>
                    </div>

                    <div style="text-align: right;">
                        @if($r->status === 'SUBMITTED')
                            <span style="display: inline-block; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.72rem; font-weight: 800; background: rgba(16,185,129,0.12); color: #047857;">
                                ✓ REPORT SUBMITTED
                            </span>
                            <div style="font-size: 1.15rem; font-weight: 900; color: {{ $r->overall_score >= 80 ? '#059669' : ($r->overall_score >= 60 ? '#d97706' : '#dc2626') }}; margin-top: 0.2rem;">
                                {{ number_format($r->overall_score, 1) }} / 100
                            </div>
                        @else
                            <span style="display: inline-block; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.72rem; font-weight: 800; background: rgba(245,158,11,0.12); color: #b45309;">
                                ⏳ PENDING EVALUATION
                            </span>
                            <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">
                                Due: {{ $r->due_date?->format('d M Y') ?: 'In 14 days' }}
                            </div>
                        @endif
                    </div>
                </div>

                @if($r->status === 'SUBMITTED')
                    <div style="display: flex; justify-content: space-between; align-items: center; margin: 0.4rem 0;">
                        <span style="font-size: 0.78rem; text-transform: uppercase; font-weight: 700; color: #64748b;">Statutory Recommendation:</span>
                        <strong style="font-size: 0.82rem; color: {{ in_array($r->recommendation, ['STRONGLY_RECOMMENDED', 'RECOMMENDED']) ? '#059669' : '#dc2626' }};">
                            {{ str_replace('_', ' ', $r->recommendation) }}
                        </strong>
                    </div>

                    <div style="font-size: 0.78rem; font-weight: 700; color: #475569; text-transform: uppercase; margin-top: 0.5rem;">
                        Qualitative Expert Evaluation Remarks:
                    </div>
                    <div class="quote-block">
                        "{{ $r->report_text ?: 'Evaluation submitted in accordance with statutory rubric.' }}"
                    </div>

                    <div style="margin-top: 0.5rem; font-size: 0.72rem; color: #64748b; display: flex; justify-content: space-between;">
                        <span>Assigned: {{ $r->assigned_at?->format('d M Y') }}</span>
                        <span>Evaluation Certified on: <strong>{{ $r->submitted_at?->format('d M Y, h:i A') }}</strong></span>
                    </div>
                @else
                    <div style="font-size: 0.8rem; color: #64748b; font-style: italic; padding: 0.5rem 0;">
                        Evaluation link dispatched to referee. Formal submission pending.
                    </div>
                @endif
            </div>
        @empty
            <p style="text-align: center; color: #64748b; padding: 2rem;">No external reviewers empanelled for this application dossier.</p>
        @endforelse

        <!-- Statutory Committee Signatures Block -->
        <div class="signatures-grid">
            <div>
                <div class="sig-line">Convenor, Scrutiny Committee</div>
                <div style="font-size: 0.72rem; color: #64748b;">Seal & Signature</div>
            </div>
            <div>
                <div class="sig-line">Dean / Academic Head</div>
                <div style="font-size: 0.72rem; color: #64748b;">Seal & Signature</div>
            </div>
            <div>
                <div class="sig-line">Registrar / Secretary Selection Board</div>
                <div style="font-size: 0.72rem; color: #64748b;">Statutory Verification Seal</div>
            </div>
        </div>

        <!-- Official Statutory Endorsement Footer -->
        <div style="margin-top: 1.5rem; padding-top: 0.75rem; border-top: 1px dashed #94a3b8; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 8pt; color: #475569; display: flex; flex-direction: column; gap: 0.4rem; page-break-inside: avoid;">
            @if(!empty($letterFooter['disclaimer']))
                <div style="font-size: 7.5pt; color: #64748b; line-height: 1.4; border-bottom: 1px dotted #cbd5e1; padding-bottom: 0.35rem;">
                    <strong>Statutory Verification & Legal Endorsement:</strong> {{ $letterFooter['disclaimer'] }}
                </div>
            @endif
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                <div>
                    <strong>Peer Evaluation Dossier:</strong> #{{ $application->tracking_number }} • Referees Empanelled: {{ $reviews->count() }} ({{ $submittedReviews->count() }} Completed)
                </div>
                <div>
                    <strong>Evaluation Record Date:</strong> {{ now()->format('d M, Y \a\t h:i A') }}
                </div>
            </div>
            @if(!empty($letterFooter['contact']))
                <div style="font-size: 7.2pt; color: #64748b; text-align: center; margin-top: 0.15rem;">
                    {{ $letterFooter['contact'] }}
                </div>
            @endif
        </div>

    </div>

</body>
</html>
