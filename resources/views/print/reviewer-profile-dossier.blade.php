@php
    $letterhead = \App\Models\SystemSetting::getLetterheadDetails();
    $letterFooter = \App\Models\SystemSetting::getLetterFooter();
    $parsed = $reviewer->parsed_name;
    $completedCount = $submittedReviews->count();
    $pendingCount = $pendingReviews->count();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expert Referee Profile Dossier — {{ $reviewer->name }} — {{ $orgName }}</title>
    
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

        .particulars-label {
            font-size: 0.68rem;
            text-transform: uppercase;
            font-weight: 800;
            color: #64748b;
            display: block;
            margin-bottom: 2px;
        }

        .particulars-value {
            font-size: 0.88rem;
            font-weight: 700;
            color: #0f172a;
            word-break: break-word;
        }

        .kpi-strip {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.75rem;
            margin-bottom: 1.25rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .kpi-card {
            border: 1.5px solid #cbd5e1;
            border-radius: 6px;
            padding: 0.75rem;
            text-align: center;
            background: #fafafa;
        }

        .kpi-title {
            font-size: 0.68rem;
            text-transform: uppercase;
            font-weight: 800;
            color: #475569;
            margin-bottom: 3px;
        }

        .kpi-val {
            font-size: 1.35rem;
            font-weight: 900;
            color: #0f172a;
        }

        .section-heading {
            font-size: 0.95rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #0f172a;
            border-bottom: 1.5px solid #0f172a;
            padding-bottom: 0.3rem;
            margin: 1.25rem 0 0.75rem 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 1.25rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .data-table th, .data-table td {
            border: 1px solid #cbd5e1;
            padding: 0.5rem 0.65rem;
            text-align: left;
            vertical-align: top;
        }

        .data-table th {
            background: #f1f5f9;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            font-size: 0.72rem;
            letter-spacing: 0.03em;
        }

        .badge-print {
            display: inline-block;
            padding: 0.15rem 0.45rem;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            border: 1px solid #cbd5e1;
        }

        .badge-submitted {
            background: #ecfdf5;
            color: #047857;
            border-color: #a7f3d0;
        }

        .badge-pending {
            background: #fffbeb;
            color: #b45309;
            border-color: #fde68a;
        }

        .signature-block {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 2.5rem;
            padding-top: 1.5rem;
            border-top: 1.5px solid #cbd5e1;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .sig-item {
            text-align: center;
            width: 220px;
        }

        .sig-line {
            border-bottom: 1px solid #0f172a;
            height: 45px;
            margin-bottom: 0.4rem;
        }

        .sig-title {
            font-size: 0.75rem;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
        }

        .sig-sub {
            font-size: 0.68rem;
            color: #64748b;
        }

        .official-footer {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 7.5pt;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            padding-top: 0.5rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        @media print {
            .no-print-toolbar {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                color: #000000 !important;
                font-size: 9pt;
            }
            .document-wrapper {
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
                max-width: 100% !important;
            }
            .data-table th, .data-table td {
                border-color: #64748b !important;
            }
            .kpi-card {
                border-color: #64748b !important;
            }
            @page {
                size: A4 portrait;
                margin: 1.2cm;
            }
        }
    </style>
</head>
<body>

    <!-- On-Screen Navigation Toolbar -->
    <div class="no-print-toolbar">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <a href="javascript:window.history.back()" class="btn-toolbar btn-toolbar-secondary">
                ← Return to Portal
            </a>
            <span style="font-size: 0.85rem; font-weight: 600; opacity: 0.85;">
                Official Academic Referee Profile Dossier • #{{ str_pad($reviewer->id, 5, '0', STR_PAD_LEFT) }}
            </span>
        </div>
        <div style="display: flex; align-items: center; gap: 0.6rem;">
            <button onclick="window.print()" class="btn-toolbar btn-toolbar-primary">
                🖶 Print Official Dossier
            </button>
            <button onclick="window.close()" class="btn-toolbar btn-toolbar-secondary">
                ✕ Close
            </button>
        </div>
    </div>

    <!-- Printable Formal Document Wrapper -->
    <div class="document-wrapper">
        <!-- Official Letterhead Header -->
        <div class="official-header">
            @if(!empty($letterhead['logo_url']))
                <img src="{{ $letterhead['logo_url'] }}" alt="Official Seal" style="max-height: 60px; margin-bottom: 0.4rem;">
            @endif
            <div class="org-title">{{ $letterhead['institution_name'] ?? $orgName }}</div>
            <div style="font-size: 0.85rem; font-weight: 600; color: #475569; font-family: 'Plus Jakarta Sans', sans-serif;">
                {{ $letterhead['tagline'] ?? 'Office of the Registrar & Academic Personnel Governance Directorate' }}
            </div>
            <div class="doc-title">
                Official Expert Referee Profile & Evaluation Portfolio
            </div>
            <div class="statutory-sub">
                Empanelled Subject-Matter Expert Record for Statutory Academic Appointments & Peer Review
            </div>
        </div>

        <!-- Referee Key Particulars Grid -->
        <div class="particulars-grid">
            <div style="grid-column: span 2;">
                <span class="particulars-label">Expert Referee Full Name</span>
                <div class="particulars-value" style="font-size: 1rem; color: #1e1b4b;">
                    @if(!empty($parsed['prefix']))
                        <span style="font-weight: 800; color: #4f46e5;">{{ $parsed['prefix'] }}</span>
                    @endif
                    {{ $parsed['full_name'] }}
                </div>
            </div>
            <div>
                <span class="particulars-label">Current Academic Rank</span>
                <div class="particulars-value" style="color: #0f172a;">
                    {{ $reviewer->academic_rank }}
                </div>
            </div>
            <div>
                <span class="particulars-label">Empanelment Status</span>
                <div class="particulars-value">
                    <span class="badge-print {{ $reviewer->is_active ? 'badge-submitted' : 'badge-pending' }}">
                        {{ $reviewer->is_active ? 'Active / Empanelled' : 'Inactive / In Quarantine' }}
                    </span>
                </div>
            </div>

            <div style="grid-column: span 2;">
                <span class="particulars-label">Institutional Affiliation / University</span>
                <div class="particulars-value">{{ $reviewer->university }}</div>
            </div>
            <div>
                <span class="particulars-label">Academic Discipline</span>
                <div class="particulars-value">{{ $reviewer->subject }}</div>
            </div>
            <div>
                <span class="particulars-label">Jurisdiction & Country</span>
                <div class="particulars-value">
                    {{ ($reviewer->country ?? '') === 'Pakistan' ? '🇵🇰 Pakistan (National)' : '🌐 ' . $reviewer->country . ' (International)' }}
                </div>
            </div>

            <div>
                <span class="particulars-label">Official Institutional Email</span>
                <div class="particulars-value" style="font-family: 'JetBrains Mono', monospace; font-size: 0.78rem;">
                    {{ $reviewer->user?->email ?? 'Not linked' }}
                </div>
            </div>
            <div>
                <span class="particulars-label">Direct Contact Phone</span>
                <div class="particulars-value">{{ $reviewer->phone ?: 'Not provided' }}</div>
            </div>
            <div>
                <span class="particulars-label">Empanelled Date</span>
                <div class="particulars-value">{{ $reviewer->created_at ? $reviewer->created_at->format('d M Y') : 'N/A' }}</div>
            </div>
            <div>
                <span class="particulars-label">Empanelled By / Registrar</span>
                <div class="particulars-value">{{ $reviewer->creator?->name ?? 'System Authority' }}</div>
            </div>

            @if($reviewer->specialization)
                <div style="grid-column: 1 / -1; margin-top: 0.35rem; padding-top: 0.45rem; border-top: 1px dashed #cbd5e1;">
                    <span class="particulars-label">Research Focus & Specialized Domain Expertise</span>
                    <div class="particulars-value" style="font-weight: 500; font-size: 0.85rem; line-height: 1.4;">
                        {{ $reviewer->specialization }}
                    </div>
                </div>
            @endif
        </div>

        <!-- Caseload & Metric Summary Strip -->
        <div class="kpi-strip">
            <div class="kpi-card">
                <div class="kpi-title">Assigned Dossiers</div>
                <div class="kpi-val" style="color: #4f46e5;">{{ $reviews->count() }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-title">Completed Evaluations</div>
                <div class="kpi-val" style="color: #059669;">{{ $completedCount }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-title">Pending Evaluations</div>
                <div class="kpi-val" style="color: #d97706;">{{ $pendingCount }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-title">Mean Consensus Score</div>
                <div class="kpi-val" style="color: #0284c7;">
                    {{ $avgScore > 0 ? number_format($avgScore, 1) . ' / 100' : 'N/A' }}
                </div>
            </div>
        </div>

        <!-- Section 2: Statutory Peer Review Ledger -->
        <div class="section-heading">
            <span>Statutory Peer Review Caseload & Evaluated Dossiers</span>
            <span style="font-size: 0.72rem; font-weight: 600; text-transform: none; color: #64748b;">Total: {{ $reviews->count() }} Record{{ $reviews->count() === 1 ? '' : 's' }}</span>
        </div>

        @if($reviews->isNotEmpty())
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 14%;">Tracking #</th>
                        <th style="width: 20%;">Candidate Particulars</th>
                        <th style="width: 22%;">Position & Department</th>
                        <th style="width: 14%;">Assigned / Due</th>
                        <th style="width: 14%;">Status</th>
                        <th style="width: 16%;">Score & Verdict</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reviews as $idx => $r)
                        @php
                            $candidate = $r->application?->user?->basicInfo;
                            $candidateName = $candidate ? trim($candidate->first_name . ' ' . $candidate->last_name) : ($r->application?->user?->name ?? 'Candidate');
                        @endphp
                        <tr>
                            <td>
                                <strong style="font-family: 'JetBrains Mono', monospace; font-size: 0.78rem;">
                                    {{ $r->application?->tracking_number ?? 'N/A' }}
                                </strong>
                                <div style="font-size: 0.68rem; color: #64748b; margin-top: 1px;">
                                    {{ $r->application?->campaign?->advertisement?->advertisement_no ?? 'Intake' }}
                                </div>
                            </td>
                            <td>
                                <strong style="color: #0f172a; font-size: 0.82rem;">{{ $candidateName }}</strong>
                                @if($candidate?->father_name)
                                    <div style="font-size: 0.68rem; color: #64748b;">S/D/O {{ $candidate->father_name }}</div>
                                @endif
                                @if($candidate?->cnic_number)
                                    <div style="font-size: 0.68rem; color: #64748b; font-family: 'JetBrains Mono', monospace;">{{ $candidate->cnic_number }}</div>
                                @endif
                            </td>
                            <td>
                                <div style="font-weight: 700; color: #0f172a;">{{ $r->application?->campaign?->title ?? 'N/A' }}</div>
                                <div style="font-size: 0.7rem; color: #475569;">{{ $r->application?->campaign?->department ?? '' }}</div>
                            </td>
                            <td>
                                <div>{{ $r->assigned_at ? $r->assigned_at->format('d M Y') : 'N/A' }}</div>
                                <div style="font-size: 0.68rem; color: #64748b;">
                                    Due: {{ $r->due_date ? $r->due_date->format('d M Y') : '14 days' }}
                                </div>
                            </td>
                            <td>
                                @if($r->status === 'SUBMITTED')
                                    <span class="badge-print badge-submitted">✓ Submitted</span>
                                    @if($r->submitted_at)
                                        <div style="font-size: 0.68rem; color: #059669; margin-top: 2px;">
                                            {{ $r->submitted_at->format('d M Y') }}
                                        </div>
                                    @endif
                                @else
                                    <span class="badge-print badge-pending">⏳ In Progress</span>
                                @endif
                            </td>
                            <td>
                                @if($r->status === 'SUBMITTED')
                                    <strong style="font-size: 0.88rem; color: {{ $r->overall_score >= 80 ? '#059669' : ($r->overall_score >= 60 ? '#d97706' : '#dc2626') }};">
                                        {{ $r->overall_score }} / 100
                                    </strong>
                                    <div style="font-size: 0.68rem; color: #475569; margin-top: 1px; font-weight: 600;">
                                        {{ str_replace('_', ' ', $r->recommendation ?? 'Completed') }}
                                    </div>
                                @else
                                    <span style="font-size: 0.72rem; color: #94a3b8; font-style: italic;">Evaluation Pending</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="padding: 1.5rem; text-align: center; border: 1px dashed #cbd5e1; border-radius: 4px; color: #64748b; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.85rem; margin-bottom: 1.25rem;">
                No academic candidacy dossiers have been assigned to this expert referee yet.
            </div>
        @endif

        <!-- Statutory Confidentiality Clause & Sign-off Block -->
        <div style="margin-top: 1.5rem; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; font-size: 7.8pt; color: #475569; line-height: 1.45; font-family: 'Plus Jakarta Sans', sans-serif;">
            <strong>STATUTORY CONFIDENTIALITY & INTEGRITY DECLARATION:</strong> This portfolio constitutes an official record maintained by the Directorate of Academic Personnel Governance and Selection Board Secretariat under the statutory University Recruitment Bylaws. External referee evaluations, scoring metrics, and qualitative remarks remain strictly privileged and confidential, intended solely for official statutory selection and scrutiny proceedings.
        </div>

        <div class="signature-block">
            <div class="sig-item">
                <div class="sig-line"></div>
                <div class="sig-title">Prepared & Checked By</div>
                <div class="sig-sub">Academic Peer Review Coordinator</div>
            </div>
            <div class="sig-item">
                <div class="sig-line"></div>
                <div class="sig-title">Verified & Recorded</div>
                <div class="sig-sub">Director / Secretary Selection Board</div>
            </div>
            <div class="sig-item">
                <div class="sig-line"></div>
                <div class="sig-title">Attested & Sealed</div>
                <div class="sig-sub">Registrar / Competent Authority</div>
            </div>
        </div>

        <!-- Official Letter Footer -->
        <div class="official-footer">
            <div>{{ $letterFooter['contact_line'] ?? 'Direct inquiries to: registrar@hirely.edu.pk • Official Verification Portal: https://hirely.edu.pk/verify' }}</div>
            <div style="margin-top: 2px;">
                Generated on {{ now()->format('d M Y \a\t H:i:s T') }} • System Ref: HRY-REF-{{ str_pad($reviewer->id, 6, '0', STR_PAD_LEFT) }}-{{ date('Ymd') }} • Page 1 of 1
            </div>
        </div>
    </div>

</body>
</html>
