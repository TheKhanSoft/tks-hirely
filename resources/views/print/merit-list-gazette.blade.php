<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Merit Gazette — {{ $campaign->title }} ({{ $campaign->job_code }}) — {{ $stageTitle }}</title>
    
    <!-- Modern Fonts -->
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
            line-height: 1.4;
            font-size: 9.8pt;
        }

        /* Screen Toolbar */
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

        /* Document Wrapper */
        .document-wrapper {
            max-width: 1020px;
            margin: 1.5rem auto 3rem;
            background: #ffffff;
            padding: 2.25rem 2.5rem;
            box-shadow: 0 4px 25px rgba(0,0,0,0.08);
            border: 1px solid #cbd5e1;
            border-radius: 4px;
        }

        /* Official Letterhead */
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
            letter-spacing: 0.05em;
            color: #0f172a;
        }

        .official-header .gazette-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-top: 0.35rem;
        }

        .official-header .statutory-sub {
            font-size: 0.85rem;
            color: #64748b;
            font-style: italic;
            margin-top: 0.2rem;
        }

        /* Meta Particulars Grid */
        .meta-strip {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.5rem;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 0.65rem 0.85rem;
            margin-bottom: 1.25rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.82rem;
        }

        .meta-strip div strong {
            display: block;
            font-size: 0.7rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .meta-strip div span {
            font-weight: 700;
            color: #0f172a;
        }

        /* KPI summary strip */
        .kpi-strip {
            display: flex;
            justify-content: space-around;
            background: #f8fafc;
            border: 1px dashed #94a3b8;
            padding: 0.65rem 1rem;
            margin-bottom: 1.25rem;
            text-align: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .kpi-strip .kpi-item .kpi-num {
            font-size: 1.25rem;
            font-weight: 800;
            color: #4f46e5;
            line-height: 1.1;
        }

        .kpi-strip .kpi-item .kpi-lbl {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            margin-top: 0.15rem;
        }

        /* Roster Table */
        .roster-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
            font-size: 9pt;
        }

        .roster-table th, .roster-table td {
            border: 1px solid #cbd5e1;
            padding: 0.45rem 0.5rem;
            text-align: left;
            vertical-align: middle;
        }

        .roster-table th {
            background: #f1f5f9;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 7.8pt;
            text-transform: uppercase;
            font-weight: 800;
            color: #334155;
            letter-spacing: 0.03em;
        }

        .roster-table tr:nth-child(even) td {
            background: #fafafa;
        }

        .roster-table tr.selected-row td {
            background: #f0fdf4 !important;
        }

        .roster-table tr.disq-row td {
            background: #fef2f2 !important;
            color: #991b1b;
        }

        /* Badges */
        .badge-pill {
            display: inline-block;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 7.2pt;
            font-weight: 700;
            padding: 0.15rem 0.45rem;
            border-radius: 9999px;
            border: 1px solid #cbd5e1;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .badge-pill.status-ok {
            background: #ecfdf5;
            color: #065f46;
            border-color: #a7f3d0;
        }

        .badge-pill.status-warn {
            background: #fffbeb;
            color: #92400e;
            border-color: #fde68a;
        }

        .badge-pill.status-danger {
            background: #fef2f2;
            color: #991b1b;
            border-color: #fecaca;
        }

        /* Statutory Committee Signatures */
        .committee-sign-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1rem;
            margin-top: 3rem;
            padding-top: 0.75rem;
            text-align: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 7.8pt;
            page-break-inside: avoid;
        }

        .sign-col {
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }

        .sign-line {
            border-top: 1.5px solid #0f172a;
            padding-top: 0.35rem;
            margin-top: 2.5rem;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
                font-size: 8.8pt;
            }

            .no-print, .no-print-toolbar {
                display: none !important;
            }

            .document-wrapper {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
            }

            @page {
                size: A4 landscape;
                margin: 10mm 12mm 12mm 12mm;
            }

            .roster-table, .committee-sign-grid, .meta-strip, .kpi-strip {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <!-- Screen Toolbar -->
    <div class="no-print-toolbar">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <span style="font-weight: 800; font-size: 1rem;">HIRELY MERIT ENGINE</span>
            <span style="background: rgba(255,255,255,0.15); padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.75rem; text-transform: uppercase; font-weight: 700;">
                {{ $stageTitle }}
            </span>
            <span style="font-size: 0.82rem; opacity: 0.75;">
                {{ $campaign->title }} ({{ $campaign->job_code }})
            </span>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            <button type="button" onclick="window.print()" class="btn-toolbar btn-toolbar-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                <span>Print Official Gazette (Ctrl + P)</span>
            </button>

            <button type="button" onclick="window.close()" class="btn-toolbar btn-toolbar-secondary">
                <span>Close</span>
            </button>
        </div>
    </div>

    <!-- Official Document Container -->
    <div class="document-wrapper">
        
        <!-- Header -->
        <div class="official-header">
            <div class="org-title">{{ $orgName }}</div>
            <div class="gazette-title">{{ $stageTitle }}</div>
            <div class="statutory-sub">
                Official Proceedings & Consolidated Order of Statutory Selection Board
            </div>
        </div>

        <!-- Meta Particulars Strip -->
        <div class="meta-strip">
            <div>
                <strong>Gazette Order Ref #</strong>
                <span style="font-family: 'JetBrains Mono', monospace;">GAZ-{{ date('Y') }}/{{ $campaign->job_code }}</span>
            </div>
            <div>
                <strong>Advertisement Reference</strong>
                <span>{{ $advertisement?->advertisement_no ?? 'Standing Recruitment' }} ({{ $advertisement?->year ?? date('Y') }})</span>
            </div>
            <div>
                <strong>Cadre & Department</strong>
                <span>{{ $campaign->title }} • {{ $campaign->department }}</span>
            </div>
            <div>
                <strong>Date of Notification</strong>
                <span>{{ now()->format('d F, Y') }}</span>
            </div>
        </div>

        <!-- Summary KPIs -->
        <div class="kpi-strip">
            <div class="kpi-item">
                <div class="kpi-num">{{ $metrics['total_applications'] }}</div>
                <div class="kpi-lbl">Total Candidates</div>
            </div>
            <div class="kpi-item">
                <div class="kpi-num" style="color: #059669;">{{ $metrics['eligible_count'] }}</div>
                <div class="kpi-lbl">Eligible / Cleared</div>
            </div>
            <div class="kpi-item">
                <div class="kpi-num" style="color: #dc2626;">{{ $metrics['ineligible_count'] }}</div>
                <div class="kpi-lbl">Ineligible / Disqualified</div>
            </div>
            <div class="kpi-item">
                <div class="kpi-num" style="color: #4f46e5;">{{ $metrics['average_aggregate'] }}%</div>
                <div class="kpi-lbl">Average Score</div>
            </div>
            <div class="kpi-item">
                <div class="kpi-num" style="color: #0891b2;">{{ $metrics['top_score'] }}%</div>
                <div class="kpi-lbl">Top Aggregate Score</div>
            </div>
        </div>

        <!-- Roster Table by Stage -->
        <table class="roster-table">
            <thead>
                @if($stage === 'intake')
                    <tr>
                        <th style="width: 5%; text-align: center;">#</th>
                        <th style="width: 12%;">Tracking #</th>
                        <th style="width: 22%;">Candidate Name</th>
                        <th style="width: 18%;">Father Name</th>
                        <th style="width: 14%;">CNIC Number</th>
                        <th style="width: 10%;">Quota / Domicile</th>
                        <th style="width: 9%; text-align: center;">Fee Status</th>
                        <th style="width: 10%;">Consumer ID</th>
                    </tr>
                @elseif($stage === 'scrutiny')
                    <tr>
                        <th style="width: 5%; text-align: center;">Rank</th>
                        <th style="width: 10%;">Tracking #</th>
                        <th style="width: 20%;">Candidate Name</th>
                        <th style="width: 16%;">Father / CNIC</th>
                        <th style="width: 16%;">Terminal Qualification</th>
                        <th style="width: 8%; text-align: center;">Acad Score</th>
                        <th style="width: 11%; text-align: center;">Scrutiny Verdict</th>
                        <th style="width: 14%;">Deficiency / Disqualification Grounds</th>
                    </tr>
                @elseif($stage === 'peer_review')
                    <tr>
                        <th style="width: 5%; text-align: center;">Rank</th>
                        <th style="width: 11%;">Tracking #</th>
                        <th style="width: 22%;">Candidate Name</th>
                        <th style="width: 15%;">Highest Degree</th>
                        <th style="width: 14%; text-align: center;">Referees Assigned</th>
                        <th style="width: 11%; text-align: center;">Avg Peer Score</th>
                        <th style="width: 12%; text-align: center;">Benchmark Verdict</th>
                        <th style="width: 10%; text-align: center;">Publications</th>
                    </tr>
                @elseif($stage === 'selection_board')
                    <tr>
                        <th style="width: 5%; text-align: center;">Rank</th>
                        <th style="width: 10%;">Tracking #</th>
                        <th style="width: 20%;">Candidate Name</th>
                        <th style="width: 15%;">Father Name</th>
                        <th style="width: 10%; text-align: center;">Academic (/40)</th>
                        <th style="width: 10%; text-align: center;">Research (/20)</th>
                        <th style="width: 10%; text-align: center;">Board Viva (/40)</th>
                        <th style="width: 10%; text-align: center;">Total Score</th>
                        <th style="width: 10%; text-align: center;">Board Verdict</th>
                    </tr>
                @else
                    {{-- Default: Consolidated Final Gazette --}}
                    <tr>
                        <th style="width: 5%; text-align: center;">Rank</th>
                        <th style="width: 9%;">Tracking #</th>
                        <th style="width: 17%;">Candidate Name</th>
                        <th style="width: 13%;">Father Name / CNIC</th>
                        <th style="width: 8%; text-align: center;">Acad (/40)</th>
                        <th style="width: 8%; text-align: center;">Exp/Pub (/15)</th>
                        <th style="width: 8%; text-align: center;">Peer/Test (/15)</th>
                        <th style="width: 8%; text-align: center;">Board (/30)</th>
                        <th style="width: 9%; text-align: center;">Aggregate %</th>
                        <th style="width: 15%; text-align: center;">Final Selection Order</th>
                    </tr>
                @endif
            </thead>
            <tbody>
                @forelse($applications as $item)
                    @php
                        $isDisq = in_array($item['app']->status, ['INELIGIBLE', 'DISQUALIFIED']);
                        $isRank1 = ($item['rank'] === 1 && !$isDisq);
                    @endphp
                    <tr class="{{ $isRank1 ? 'selected-row' : ($isDisq ? 'disq-row' : '') }}">
                        
                        @if($stage === 'intake')
                            <td style="text-align: center; font-weight: 700;">{{ $loop->iteration }}</td>
                            <td style="font-family: 'JetBrains Mono', monospace; font-weight: 700;">{{ $item['tracking_number'] }}</td>
                            <td><strong>{{ $item['candidate_name'] }}</strong></td>
                            <td>{{ $item['father_name'] }}</td>
                            <td style="font-family: 'JetBrains Mono', monospace;">{{ $item['cnic'] }}</td>
                            <td>{{ $item['quota'] }} ({{ $item['domicile'] }})</td>
                            <td style="text-align: center;">
                                <span class="badge-pill {{ $item['is_paid'] ? 'status-ok' : 'status-warn' }}">
                                    {{ $item['is_paid'] ? 'PAID' : 'PENDING' }}
                                </span>
                            </td>
                            <td style="font-family: 'JetBrains Mono', monospace; font-size: 8pt;">{{ $item['challan_id'] }}</td>

                        @elseif($stage === 'scrutiny')
                            <td style="text-align: center; font-weight: 800; font-size: 10pt;">
                                {{ $item['rank'] }}
                            </td>
                            <td style="font-family: 'JetBrains Mono', monospace; font-weight: 700;">{{ $item['tracking_number'] }}</td>
                            <td><strong>{{ $item['candidate_name'] }}</strong></td>
                            <td>
                                {{ $item['father_name'] }}<br>
                                <small style="color: #64748b; font-family: 'JetBrains Mono', monospace;">{{ $item['cnic'] }}</small>
                            </td>
                            <td>
                                {{ $item['highest_degree'] }}<br>
                                <small style="color: #64748b;">{{ $item['terminal_division'] }} ({{ $item['terminal_gpa'] }})</small>
                            </td>
                            <td style="text-align: center; font-family: 'JetBrains Mono', monospace; font-weight: 700;">
                                {{ number_format($item['academic_score'], 1) }}
                            </td>
                            <td style="text-align: center;">
                                <span class="badge-pill {{ in_array($item['app']->status, ['ELIGIBLE', 'UNDER_REVIEW', 'APPROVED']) ? 'status-ok' : (in_array($item['app']->status, ['INELIGIBLE', 'DISQUALIFIED']) ? 'status-danger' : 'status-warn') }}">
                                    {{ $item['status_label'] }}
                                </span>
                            </td>
                            <td style="font-size: 8pt; color: {{ $isDisq ? '#991b1b' : '#64748b' }};">
                                {{ $item['disqualification_reason'] ?: 'All statutory minimum criteria verified and satisfied.' }}
                            </td>

                        @elseif($stage === 'peer_review')
                            <td style="text-align: center; font-weight: 800;">{{ $item['rank'] }}</td>
                            <td style="font-family: 'JetBrains Mono', monospace; font-weight: 700;">{{ $item['tracking_number'] }}</td>
                            <td><strong>{{ $item['candidate_name'] }}</strong></td>
                            <td>{{ $item['highest_degree'] }}</td>
                            <td style="text-align: center;">
                                {{ $item['app']->reviews->where('status', 'SUBMITTED')->count() }} / {{ $item['app']->reviews->count() }} Submissions
                            </td>
                            <td style="text-align: center; font-family: 'JetBrains Mono', monospace; font-weight: 800; color: #4f46e5;">
                                {{ $item['peer_score_avg'] > 0 ? number_format($item['peer_score_avg'], 1) . '%' : '—' }}
                            </td>
                            <td style="text-align: center;">
                                <span class="badge-pill {{ $item['peer_score_avg'] >= 60.0 ? 'status-ok' : ($item['peer_score_avg'] > 0 ? 'status-danger' : 'status-warn') }}">
                                    {{ $item['peer_verdict'] }}
                                </span>
                            </td>
                            <td style="text-align: center; font-family: 'JetBrains Mono', monospace;">
                                {{ $item['app']->user?->basicInfo?->publications->count() ?? 0 }} Papers
                            </td>

                        @elseif($stage === 'selection_board')
                            <td style="text-align: center; font-weight: 800;">{{ $item['rank'] }}</td>
                            <td style="font-family: 'JetBrains Mono', monospace; font-weight: 700;">{{ $item['tracking_number'] }}</td>
                            <td><strong>{{ $item['candidate_name'] }}</strong></td>
                            <td>{{ $item['father_name'] }}</td>
                            <td style="text-align: center; font-family: 'JetBrains Mono', monospace;">{{ number_format($item['academic_score'], 1) }}</td>
                            <td style="text-align: center; font-family: 'JetBrains Mono', monospace;">{{ number_format($item['research_exp_score'], 1) }}</td>
                            <td style="text-align: center; font-family: 'JetBrains Mono', monospace; font-weight: 700;">{{ number_format($item['interview_score'], 1) }}</td>
                            <td style="text-align: center; font-family: 'JetBrains Mono', monospace; font-weight: 800; color: #4f46e5;">
                                {{ number_format($item['academic_score'] + $item['research_exp_score'] + $item['interview_score'], 1) }}
                            </td>
                            <td style="text-align: center;">
                                <span class="badge-pill {{ $item['board_status'] === 'RECOMMENDED' ? 'status-ok' : ($item['board_status'] === 'NOT RECOMMENDED' ? 'status-danger' : 'status-warn') }}">
                                    {{ $item['board_status'] }}
                                </span>
                            </td>

                        @else
                            {{-- Consolidated Final Gazette --}}
                            <td style="text-align: center; font-weight: 800; font-size: 10pt;">
                                @if($isRank1)
                                    🏆 1
                                @else
                                    {{ $item['rank'] }}
                                @endif
                            </td>
                            <td style="font-family: 'JetBrains Mono', monospace; font-weight: 700;">{{ $item['tracking_number'] }}</td>
                            <td><strong>{{ $item['candidate_name'] }}</strong></td>
                            <td>
                                {{ $item['father_name'] }}<br>
                                <small style="color: #64748b; font-family: 'JetBrains Mono', monospace;">{{ $item['cnic'] }}</small>
                            </td>
                            <td style="text-align: center; font-family: 'JetBrains Mono', monospace;">{{ number_format($item['academic_score'], 1) }}</td>
                            <td style="text-align: center; font-family: 'JetBrains Mono', monospace;">{{ number_format($item['research_exp_score'], 1) }}</td>
                            <td style="text-align: center; font-family: 'JetBrains Mono', monospace;">{{ number_format($item['peer_score_scaled'], 1) }}</td>
                            <td style="text-align: center; font-family: 'JetBrains Mono', monospace;">{{ number_format($item['interview_score'], 1) }}</td>
                            <td style="text-align: center; font-family: 'JetBrains Mono', monospace; font-weight: 900; font-size: 10.5pt; color: #4f46e5;">
                                {{ number_format($item['aggregate_score'], 1) }}%
                            </td>
                            <td style="text-align: center;">
                                <span class="badge-pill {{ $isRank1 ? 'status-ok' : ($isDisq ? 'status-danger' : 'status-warn') }}">
                                    {{ $item['recommendation'] }}
                                </span>
                            </td>
                        @endif

                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 2rem; color: #64748b; font-style: italic;">
                            No candidate records found for the selected campaign and pipeline stage.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Statutory Committee 5-Member Signatures -->
        <div class="committee-sign-grid">
            <div class="sign-col">
                <div class="sign-line">
                    <strong>Member Incharge</strong><br>
                    <span>Scrutiny Committee</span>
                </div>
            </div>
            <div class="sign-col">
                <div class="sign-line">
                    <strong>External Expert</strong><br>
                    <span>Subject Specialist</span>
                </div>
            </div>
            <div class="sign-col">
                <div class="sign-line">
                    <strong>Dean / Head</strong><br>
                    <span>Concerned Faculty</span>
                </div>
            </div>
            <div class="sign-col">
                <div class="sign-line">
                    <strong>Additional Registrar</strong><br>
                    <span>Recruitment & HR</span>
                </div>
            </div>
            <div class="sign-col">
                <div class="sign-line">
                    <strong>Vice Chancellor</strong><br>
                    <span>Chairman Selection Board</span>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
