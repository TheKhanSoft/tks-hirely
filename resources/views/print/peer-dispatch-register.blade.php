@php
    $letterhead = \App\Models\SystemSetting::getLetterheadDetails();
    $letterFooter = \App\Models\SystemSetting::getLetterFooter();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Peer Review Dispatch Register — {{ $orgName }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Times New Roman', Times, Georgia, serif; color: #0f172a; background: #f8fafc; font-size: 9pt; }
        .no-print-toolbar { position: sticky; top: 0; z-index: 1000; background: #0f172a; color: #ffffff; padding: 0.75rem 1.5rem; display: flex; justify-content: space-between; align-items: center; font-family: 'Plus Jakarta Sans', sans-serif; }
        .btn-toolbar { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.85rem; font-weight: 700; text-decoration: none; cursor: pointer; border: 1px solid transparent; }
        .btn-toolbar-primary { background: #4f46e5; color: #ffffff; }
        .btn-toolbar-secondary { background: rgba(255,255,255,0.12); color: #ffffff; }
        .document-wrapper { max-width: 1100px; margin: 1.5rem auto 3rem; background: #ffffff; padding: 2rem 2.5rem; box-shadow: 0 4px 25px rgba(0,0,0,0.08); border: 1px solid #cbd5e1; }
        .official-header { text-align: center; border-bottom: 2.5px solid #0f172a; padding-bottom: 1rem; margin-bottom: 1rem; }
        .org-title { font-size: 1.3rem; font-weight: 800; text-transform: uppercase; color: #0f172a; }
        .doc-title { font-size: 1.1rem; font-weight: 800; text-transform: uppercase; margin-top: 0.25rem; }
        .meta-strip { display: flex; justify-content: space-between; background: #f1f5f9; padding: 0.5rem 0.75rem; border: 1px solid #cbd5e1; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.8rem; margin-bottom: 1rem; }
        table { width: 100%; border-collapse: collapse; margin-top: 0.5rem; }
        th, td { border: 1px solid #94a3b8; padding: 0.45rem 0.6rem; text-align: left; }
        th { background: #e2e8f0; font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; }
        .signatures-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem; margin-top: 3rem; text-align: center; font-family: 'Plus Jakarta Sans', sans-serif; page-break-inside: avoid; }
        .sig-line { border-top: 1.5px solid #0f172a; padding-top: 0.4rem; font-size: 0.8rem; font-weight: 700; }
        @media print {
            .no-print-toolbar { display: none !important; }
            body { background: #ffffff; }
            .document-wrapper { border: none; box-shadow: none; padding: 0; margin: 0; max-width: 100%; }
            @page { size: A4 landscape; margin: 10mm; }
        }
    </style>
</head>
<body>

    <div class="no-print-toolbar">
        <div><strong>Academic Peer Review Dispatch Register</strong></div>
        <div style="display: flex; gap: 0.5rem;">
            <button type="button" onclick="window.print()" class="btn-toolbar btn-toolbar-primary">🖨️ Print Register</button>
            <button type="button" onclick="window.close()" class="btn-toolbar btn-toolbar-secondary">✕ Close</button>
        </div>
    </div>

    <div class="document-wrapper">
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
            <div class="doc-title">CONFIDENTIAL EXTERNAL ACADEMIC PEER REVIEW DISPATCH REGISTER</div>
            <div style="font-size: 0.8rem; color: #64748b; font-style: italic;">Official Institutional Record for Academic Referee Evaluations</div>
            @if(!empty($letterhead['custom_details']))
                <div style="font-size: 0.76rem; color: #64748b; margin-top: 0.25rem;">
                    {!! nl2br(e($letterhead['custom_details'])) !!}
                </div>
            @endif
        </div>

        <div class="meta-strip">
            <div>Drive: <strong>{{ $advertisement ? "Ad #{$advertisement->advertisement_no} ({$advertisement->year})" : 'All Advertisements' }}</strong></div>
            <div>Position: <strong>{{ $campaign ? $campaign->title . " ({$campaign->job_code})" : 'All Academic Positions' }}</strong></div>
            <div>Total Candidacies: <strong>{{ $candidacies->count() }}</strong></div>
            <div>Generated: <strong>{{ now()->format('d M Y, h:i A') }}</strong></div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 35px; text-align: center;">#</th>
                    <th style="width: 95px;">Tracking ID</th>
                    <th>Candidate Particulars</th>
                    <th>Position & Dept</th>
                    <th>Assigned External Referees</th>
                    <th style="text-align: center; width: 65px;">Turnout</th>
                    <th style="text-align: center; width: 75px;">Avg Score</th>
                    <th style="text-align: center; width: 110px;">Consensus Verdict</th>
                </tr>
            </thead>
            <tbody>
                @forelse($candidacies as $idx => $app)
                    @php
                        $submitted = $app->reviews->where('status', 'SUBMITTED');
                        $avg = $submitted->count() > 0 ? round((float)$submitted->avg('overall_score'), 1) : null;
                        $verdict = $app->evaluatePeerReviewVerdict();
                    @endphp
                    <tr>
                        <td style="text-align: center; font-weight: 700;">{{ $idx + 1 }}</td>
                        <td style="font-family: 'JetBrains Mono', monospace; font-weight: 700;">{{ $app->tracking_number }}</td>
                        <td>
                            <strong>{{ $app->user->basicInfo ? trim($app->user->basicInfo->first_name . ' ' . $app->user->basicInfo->last_name) : $app->user->email }}</strong>
                            <div style="font-size: 0.72rem; color: #64748b;">CNIC: {{ $app->user->basicInfo?->cnic_number ?? '—' }} • {{ $app->user->basicInfo?->quota ?? 'Open Merit' }}</div>
                        </td>
                        <td>
                            <strong>{{ $app->campaign->title }}</strong>
                            <div style="font-size: 0.72rem; color: #64748b;">{{ $app->campaign->department }}</div>
                        </td>
                        <td style="font-size: 0.78rem;">
                            @if($app->reviews->isNotEmpty())
                                @foreach($app->reviews as $r)
                                    <div>
                                        • {{ $r->reviewer?->reviewerProfile?->name ?? 'Referee' }}
                                        <span style="font-size: 0.7rem; color: {{ $r->status === 'SUBMITTED' ? '#047857' : '#b45309' }};">
                                            ({{ $r->status === 'SUBMITTED' ? ($r->overall_score . '/100') : 'Pending' }})
                                        </span>
                                    </div>
                                @endforeach
                            @else
                                <span style="color: #dc2626; font-style: italic;">No Referees Assigned</span>
                            @endif
                        </td>
                        <td style="text-align: center; font-weight: 700;">
                            {{ $submitted->count() }} / {{ $app->reviews->count() }}
                        </td>
                        <td style="text-align: center; font-weight: 800; color: {{ $avg >= 80 ? '#059669' : ($avg >= 60 ? '#d97706' : '#64748b') }};">
                            {{ $avg !== null ? ($avg . '%') : '—' }}
                        </td>
                        <td style="text-align: center; font-size: 0.75rem; font-weight: 800;">
                            @if($verdict['is_complete'])
                                <span style="color: {{ $verdict['passed'] ? '#059669' : '#dc2626' }};">
                                    {{ $verdict['passed'] ? 'CLEARED' : 'FAILED' }}
                                </span>
                            @else
                                <span style="color: #d97706;">AWAITING</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 2rem; color: #64748b;">No academic candidacies found matching criteria.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="signatures-grid">
            <div>
                <div class="sig-line">In-Charge, Peer Review Section</div>
            </div>
            <div>
                <div class="sig-line">Convener, Scrutiny Committee</div>
            </div>
            <div>
                <div class="sig-line">Registrar / Selection Secretariat</div>
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
                    <strong>Register Audit Record:</strong> Total Candidacies Empanelled: {{ $candidacies->count() }}
                </div>
                <div>
                    <strong>Dispatch Issued on:</strong> {{ now()->format('d M, Y \a\t h:i A') }}
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
