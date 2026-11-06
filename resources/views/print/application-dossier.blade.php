@php
    $letterhead = $letterhead ?? \App\Models\SystemSetting::getLetterheadDetails();
    $letterFooter = $letterFooter ?? \App\Models\SystemSetting::getLetterFooter();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $application->tracking_number }} — {{ $basicInfo ? trim(($basicInfo->first_name ?? '') . ' ' . ($basicInfo->last_name ?? '')) : $application->user->email }} — {{ $viewMode === 'admin' ? 'Master Dossier' : 'Application Form' }}</title>
    
    <!-- Modern Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #4f46e5;
            --slate-900: #0f172a;
            --slate-800: #1e293b;
            --slate-700: #334155;
            --slate-600: #475569;
            --slate-500: #64748b;
            --slate-200: #e2e8f0;
            --slate-100: #f1f5f9;
        }

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
            font-size: 10pt;
        }

        /* Screen Floating Action Bar */
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

        .no-print-toolbar .toolbar-left {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            flex-wrap: wrap;
        }

        .no-print-toolbar .toolbar-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
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
            transition: all 0.2s;
        }

        .btn-toolbar-primary {
            background: #4f46e5;
            color: #ffffff;
        }
        .btn-toolbar-primary:hover {
            background: #4338ca;
        }

        .btn-toolbar-secondary {
            background: rgba(255,255,255,0.12);
            color: #ffffff;
            border-color: rgba(255,255,255,0.2);
        }
        .btn-toolbar-secondary:hover {
            background: rgba(255,255,255,0.2);
        }

        /* Printable Document Container */
        .document-wrapper {
            max-width: 920px;
            margin: 1.5rem auto 3rem;
            background: #ffffff;
            padding: 2.25rem 2.5rem;
            box-shadow: 0 4px 25px rgba(0,0,0,0.08);
            border: 1px solid #cbd5e1;
            border-radius: 4px;
        }

        /* Formal University Letterhead */
        .official-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2.5px solid #0f172a;
            padding-bottom: 1rem;
            margin-bottom: 1.25rem;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .emblem-box {
            width: 72px;
            height: 72px;
            border: 2px solid #0f172a;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 900;
            font-size: 1.4rem;
            color: #0f172a;
            background: #f8fafc;
            letter-spacing: -0.05em;
        }

        .header-title-box h1 {
            font-size: 1.35rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #0f172a;
            margin: 0;
            line-height: 1.2;
        }

        .header-title-box h2 {
            font-size: 1.05rem;
            font-weight: 700;
            color: #334155;
            margin-top: 0.25rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .header-title-box .header-subtitle {
            font-size: 0.85rem;
            color: #64748b;
            font-style: italic;
            margin-top: 0.2rem;
        }

        .candidate-photo-box {
            width: 110px;
            height: 130px;
            border: 2px solid #0f172a;
            background: #f1f5f9;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            text-align: center;
            padding: 2px;
            flex-shrink: 0;
        }

        .candidate-photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Tracking & Particulars Bar */
        .meta-strip {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.5rem;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 0.65rem 0.85rem;
            margin-bottom: 0.75rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.82rem;
        }

        .meta-strip div strong {
            display: block;
            font-size: 0.72rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .meta-strip div span {
            font-weight: 700;
            color: #0f172a;
        }

        /* User Details & Document Audit Strip */
        .audit-strip {
            display: grid;
            grid-template-columns: 2.2fr 1.3fr 1.5fr;
            gap: 0.65rem;
            background: #eff6ff;
            border: 1.5px solid #bfdbfe;
            border-left: 4px solid #2563eb;
            padding: 0.6rem 0.85rem;
            margin-bottom: 1.25rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.82rem;
        }

        .audit-strip div strong {
            display: block;
            font-size: 0.70rem;
            color: #1e40af;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.15rem;
        }

        .audit-strip div span {
            font-weight: 700;
            color: #1e3a8a;
        }

        /* Section Headings */
        .section-heading {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.92rem;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
            background: #e2e8f0;
            padding: 0.35rem 0.75rem;
            margin: 1.25rem 0 0.55rem 0;
            border-left: 4px solid #0f172a;
            display: flex;
            justify-content: space-between;
            align-items: center;
            page-break-after: avoid;
        }

        .section-heading span.badge {
            font-size: 0.7rem;
            padding: 0.15rem 0.5rem;
            border-radius: 3px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            font-weight: 700;
        }

        /* Bio Data Grid */
        .bio-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0.75rem;
            font-size: 9.5pt;
        }

        .bio-table td {
            padding: 0.35rem 0.6rem;
            border: 1px solid #cbd5e1;
            vertical-align: top;
        }

        .bio-table td.label-col {
            width: 22%;
            font-weight: 700;
            background: #f8fafc;
            color: #334155;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 8.2pt;
            text-transform: uppercase;
        }

        .bio-table td.val-col {
            width: 28%;
            color: #0f172a;
        }

        /* Standard Data Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0.75rem;
            font-size: 9pt;
        }

        .data-table th, .data-table td {
            border: 1px solid #cbd5e1;
            padding: 0.38rem 0.5rem;
            text-align: left;
            vertical-align: middle;
        }

        .data-table th {
            background: #f1f5f9;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 8pt;
            text-transform: uppercase;
            font-weight: 800;
            color: #334155;
        }

        .data-table tr:nth-child(even) td {
            background: #fafafa;
        }

        /* Badges */
        .badge-pill {
            display: inline-block;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 7.2pt;
            font-weight: 700;
            padding: 0.12rem 0.45rem;
            border-radius: 9999px;
            border: 1px solid #cbd5e1;
            text-transform: uppercase;
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

        /* Admin Highlight Panels */
        .admin-box {
            border: 2px dashed #64748b;
            border-radius: 4px;
            padding: 0.85rem;
            margin-bottom: 1rem;
            background: #f8fafc;
            page-break-inside: avoid;
        }

        /* Declaration Box */
        .declaration-box {
            border: 1px solid #94a3b8;
            padding: 0.85rem 1rem;
            background: #fcfcfc;
            margin-top: 1.25rem;
            font-size: 9pt;
            line-height: 1.5;
            page-break-inside: avoid;
        }

        .declaration-box p {
            margin-bottom: 0.75rem;
            text-align: justify;
        }

        .signatures-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-top: 2rem;
            padding-top: 0.5rem;
            text-align: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 8.5pt;
            page-break-inside: avoid;
        }

        .signature-line {
            border-top: 1.5px solid #0f172a;
            padding-top: 0.4rem;
            margin-top: 2.5rem;
        }

        .official-seal-box {
            border: 1px dashed #cbd5e1;
            width: 90px;
            height: 90px;
            margin: 0 auto 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 7.5pt;
            color: #94a3b8;
            text-transform: uppercase;
        }

        /* Running print footer (hidden on screen, active on print) */
        .document-print-footer {
            display: none;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
                font-size: 9.5pt;
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
                size: A4 portrait;
                margin: 12mm 14mm 14mm 14mm;
            }

            .page-break-before {
                page-break-before: always;
            }

            .data-table, .bio-table, .declaration-box, .signatures-grid, .admin-box, .audit-strip {
                page-break-inside: avoid;
            }

            .document-print-footer {
                display: flex !important;
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                height: 18px;
                border-top: 1px solid #94a3b8;
                padding-top: 2px;
                font-size: 7.2pt;
                font-family: 'Plus Jakarta Sans', sans-serif;
                color: #475569;
                justify-content: space-between;
                align-items: center;
                background: #ffffff;
            }
        }
    </style>
</head>
<body>

    <!-- Screen Toolbar (Hidden on print) -->
    <div class="no-print-toolbar">
        <div class="toolbar-left">
            <span style="font-weight: 800; font-size: 1rem; letter-spacing: -0.02em;">
                {{ strtoupper($appName ?? 'Hirely') }} RECRUITMENT OS
            </span>
            <span style="background: rgba(255,255,255,0.15); padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.75rem; text-transform: uppercase; font-weight: 700;">
                {{ $viewMode === 'admin' ? 'Master Admin Dossier' : 'Candidate Application Form' }}
            </span>
            <span style="font-size: 0.82rem; opacity: 0.75;">
                Tracking ID: <strong>{{ $application->tracking_number }}</strong>
            </span>
            <span style="background: rgba(99,102,241,0.25); border: 1px solid rgba(165,180,252,0.3); padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.78rem;">
                👤 <strong>{{ $printedByName }} ({{ $printedByUsername }})</strong> - {{ $printedByRole }} &bull; {{ $printedAt->format('d M, Y h:i A') }}
            </span>
        </div>

        <div class="toolbar-actions">
            @if($isStaff)
                @if($viewMode === 'admin')
                    <a href="{{ request()->fullUrlWithQuery(['view' => 'candidate']) }}" class="btn-toolbar btn-toolbar-secondary">
                        <span>👁️ View As Candidate</span>
                    </a>
                @else
                    <a href="{{ request()->fullUrlWithQuery(['view' => 'admin']) }}" class="btn-toolbar btn-toolbar-secondary">
                        <span>🛡️ View Master Admin Dossier</span>
                    </a>
                @endif
            @endif

            <button type="button" onclick="window.print()" class="btn-toolbar btn-toolbar-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                <span>Print Document (Ctrl + P)</span>
            </button>

            <button type="button" onclick="window.close()" class="btn-toolbar btn-toolbar-secondary">
                <span>Close Window</span>
            </button>
        </div>
    </div>

    <!-- Official Printable Document -->
    <div class="document-wrapper">
        
        <!-- Official Letterhead Header -->
        <div class="official-header">
            <div class="header-left">
                <div class="emblem-box">
                    @if(!empty($letterhead['logo']))
                        <img src="{{ $letterhead['logo'] }}" alt="{{ $letterhead['heading'] }}" style="max-height: 52px; max-width: 52px; object-fit: contain;" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                        <span style="display: none;">{{ substr($letterhead['heading'], 0, 1) }}</span>
                    @else
                        <span>{{ substr($letterhead['heading'], 0, 1) }}</span>
                    @endif
                </div>
                <div class="header-title-box">
                    <h1>{{ $letterhead['heading'] }}</h1>
                    @if(!empty($letterhead['subheading']))
                        <div style="font-size: 0.88rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 0.15rem;">
                            {{ $letterhead['subheading'] }}
                        </div>
                    @endif
                    @if(!empty($letterhead['office']))
                        <div style="font-size: 0.82rem; font-weight: 800; color: #2563eb; text-transform: uppercase; letter-spacing: 0.04em; margin-top: 0.15rem;">
                            {{ $letterhead['office'] }}
                        </div>
                    @endif
                    <h2>
                        @if(empty($campaign))
                            Official Master Candidate Profile Dossier &amp; Research Portfolio
                        @else
                            {{ $viewMode === 'admin' 
                                ? ($letterhead['dossier_title_admin'] ?? 'Official Master Administrative Application Dossier & Scrutiny Record') 
                                : ($letterhead['dossier_title_candidate'] ?? 'Official Application Form for Employment') }}
                        @endif
                    </h2>
                    @if(!empty($letterhead['custom_details']))
                        <div class="header-subtitle" style="font-size: 0.78rem; color: #64748b; font-style: normal; margin-top: 0.2rem; line-height: 1.35;">
                            {!! nl2br(e($letterhead['custom_details'])) !!}
                        </div>
                    @else
                        <div class="header-subtitle">
                            Prescribed under Statutory University Employment Code & Service Statutes
                        </div>
                    @endif
                </div>
            </div>

            <div class="candidate-photo-box">
                @if($basicInfo?->profile_picture_url)
                    <img src="{{ $basicInfo->profile_picture_url }}" alt="Candidate Photograph">
                @else
                    <div style="font-size: 7.5pt; color: #64748b; font-family: 'Plus Jakarta Sans', sans-serif;">
                        Affix Passport Photograph Here
                    </div>
                @endif
            </div>
        </div>

        <!-- Tracking Particulars Strip -->
        <div class="meta-strip">
            <div>
                <strong>Application Tracking #</strong>
                <span style="font-family: 'JetBrains Mono', monospace;">{{ $application->tracking_number }}</span>
            </div>
            <div>
                <strong>Advertisement Reference</strong>
                <span>{{ $advertisement?->advertisement_no ?? 'Direct Intake' }} ({{ $advertisement?->year ?? date('Y') }})</span>
            </div>
            <div>
                <strong>Position / Job Code</strong>
                <span>{{ $campaign?->title ?? 'General Candidate Registry' }} ({{ $campaign?->job_code ?? 'ROSTER' }})</span>
            </div>
            <div>
                <strong>Submission Timestamp</strong>
                <span>{{ $application->submission_timestamp ? $application->submission_timestamp->format('d M, Y h:i A') : $application->created_at->format('d M, Y') }}</span>
            </div>
        </div>

        <!-- User Details & Print Audit Trail Strip -->
        <div class="audit-strip">
            <div>
                <strong>Printed / Downloaded By (User)</strong>
                <span>{{ $printedByName }} ({{ $printedByUsername }})</span>
            </div>
            <div>
                <strong>Designated Role / Authorization</strong>
                <span class="badge-pill status-ok" style="font-size: 7.5pt;">{{ $printedByRole }}</span>
            </div>
            <div>
                <strong>Printed / Downloaded on Date Time</strong>
                <span style="font-family: 'JetBrains Mono', monospace;">{{ $printedAt->format('d M, Y h:i:s A') }}</span>
            </div>
        </div>

        <!-- Section 1: Candidate Bio-Data -->
        <div class="section-heading">
            <span>1. Candidate Personal Particulars & Bio-Data</span>
            <span class="badge">{{ $basicInfo?->quota ?? 'Open Merit' }}</span>
        </div>
        <table class="bio-table">
            <tr>
                <td class="label-col">Full Name</td>
                <td class="val-col"><strong>{{ $basicInfo ? trim(($basicInfo->first_name ?? '') . ' ' . ($basicInfo->last_name ?? '')) : $application->user->email }}</strong></td>
                <td class="label-col">Father / Spouse Name</td>
                <td class="val-col">{{ $basicInfo?->father_name ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label-col">CNIC / Passport Number</td>
                <td class="val-col" style="font-family: 'JetBrains Mono', monospace;">{{ $basicInfo?->cnic_number ?? 'N/A' }}</td>
                <td class="label-col">Date of Birth / Age</td>
                <td class="val-col">
                    {{ $basicInfo?->date_of_birth ? $basicInfo->date_of_birth->format('d F, Y') : '—' }}
                    @if($basicInfo?->date_of_birth)
                        ({{ \Carbon\Carbon::parse($basicInfo->date_of_birth)->age }} Years)
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label-col">Gender</td>
                <td class="val-col">{{ $basicInfo?->gender ?? '—' }}</td>
                <td class="label-col">Domicile (District/Province)</td>
                <td class="val-col">{{ $basicInfo?->domicile ?? 'Punjab, Pakistan' }}</td>
            </tr>
            <tr>
                <td class="label-col">Nationality & Religion</td>
                <td class="val-col">{{ $basicInfo?->nationality ?? 'Pakistani' }} • {{ $basicInfo?->religion ?? 'Islam' }}</td>
                <td class="label-col">Marital Status</td>
                <td class="val-col">{{ $basicInfo?->marital_status ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label-col">Primary Mobile Contact</td>
                <td class="val-col" style="font-family: 'JetBrains Mono', monospace;">{{ $basicInfo?->phone_number ?? '—' }}</td>
                <td class="label-col">Official Email Address</td>
                <td class="val-col" style="font-family: 'JetBrains Mono', monospace;">{{ $application->user->email }}</td>
            </tr>
            @if($basicInfo?->secondary_phone || $basicInfo?->secondary_email)
            <tr>
                <td class="label-col">Secondary Mobile</td>
                <td class="val-col" style="font-family: 'JetBrains Mono', monospace;">{{ $basicInfo?->secondary_phone ?? '—' }}</td>
                <td class="label-col">Secondary Email</td>
                <td class="val-col" style="font-family: 'JetBrains Mono', monospace;">{{ $basicInfo?->secondary_email ?? '—' }}</td>
            </tr>
            @endif
            @if($basicInfo?->place_of_birth || $basicInfo?->track_type)
            <tr>
                <td class="label-col">Place of Birth</td>
                <td class="val-col">{{ $basicInfo?->place_of_birth ?? '—' }}</td>
                <td class="label-col">Recruitment Cadre Track</td>
                <td class="val-col"><strong>{{ $basicInfo?->track_type ?? 'ACADEMIC' }} TRACK</strong></td>
            </tr>
            @endif
            <tr>
                <td class="label-col">Permanent Address</td>
                <td class="val-col" colspan="3">{{ $basicInfo?->permanent_address ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label-col">Mailing / Postal Address</td>
                <td class="val-col" colspan="3">{{ $basicInfo?->mailing_address ?? ($basicInfo?->permanent_address ?? '—') }}</td>
            </tr>
        </table>

        <!-- Section 2: Fee Settlement Particulars -->
        <div class="section-heading">
            <span>2. Application Processing Fee Settlement Particulars</span>
            @php
                $isPaid = $billing?->status === 'SETTLED' || in_array($application->status, ['PAID', 'ELIGIBLE', 'UNDER_REVIEW', 'APPROVED']);
            @endphp
            <span class="badge {{ $isPaid ? 'badge-pill status-ok' : 'badge-pill status-warn' }}">
                {{ $isPaid ? 'FEE SETTLED / VERIFIED' : 'PAYMENT PENDING' }}
            </span>
        </div>
        <table class="bio-table">
            <tr>
                <td class="label-col">1Link / Kuickpay Consumer ID</td>
                <td class="val-col" style="font-family: 'JetBrains Mono', monospace; font-weight: 700;">
                    {{ $billing?->consumer_id ?? ('1000' . str_pad($application->id, 8, '0', STR_PAD_LEFT)) }}
                </td>
                <td class="label-col">Prescribed Fee Amount</td>
                <td class="val-col">
                    <strong>{{ $currency }} {{ number_format((float) ($billing?->amount ?? $campaign->application_fee ?? 0), 2) }}</strong>
                </td>
            </tr>
            <tr>
                <td class="label-col">Settlement Transaction ID</td>
                <td class="val-col" style="font-family: 'JetBrains Mono', monospace;">{{ $billing?->transaction_id ?? ($isPaid ? 'ONLINE-AUTO-VERIFIED' : 'Awaiting Settlement') }}</td>
                <td class="label-col">Payment Settlement Timestamp</td>
                <td class="val-col">
                    {{ $billing?->payment_timestamp ? $billing->payment_timestamp->format('d M, Y h:i A') : ($isPaid ? now()->format('d M, Y') : 'Pending Bank Clearance') }}
                </td>
            </tr>
        </table>

        <!-- Section 3: Academic Qualifications -->
        <div class="section-heading">
            <span>3. Academic Qualifications & Educational Record</span>
            <span class="badge">{{ $basicInfo?->degrees->count() ?? 0 }} Qualifications Recorded</span>
        </div>
        @if($basicInfo && $basicInfo->degrees->isNotEmpty())
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 4%;">#</th>
                        <th style="width: 17%;">Level / Degree</th>
                        <th style="width: 25%;">Degree Title & Major</th>
                        <th style="width: 25%;">Board / University & Country</th>
                        <th style="width: 8%; text-align: center;">Year</th>
                        <th style="width: 11%; text-align: center;">Marks / CGPA</th>
                        <th style="width: 10%; text-align: center;">Division</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($basicInfo->degrees as $idx => $deg)
                        <tr>
                            <td style="text-align: center;">{{ $idx + 1 }}</td>
                            <td><strong>{{ $deg->degree_level }}</strong></td>
                            <td>
                                {{ $deg->degree_title }}
                                @if($deg->major)
                                    <br><small style="color: #64748b;">Major: {{ $deg->major }}</small>
                                @endif
                            </td>
                            <td>{{ $deg->institution }} ({{ $deg->country ?? 'Pakistan' }})</td>
                            <td style="text-align: center;">{{ $deg->year }}</td>
                            <td style="text-align: center; font-family: 'JetBrains Mono', monospace;">{{ $deg->gpa }}</td>
                            <td style="text-align: center;"><strong>{{ $deg->division }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="padding: 0.75rem; border: 1px dashed #cbd5e1; font-size: 9pt; color: #64748b; font-style: italic; margin-bottom: 0.75rem;">
                No academic qualification records recorded on file.
            </div>
        @endif

        <!-- Section 4: Work Experience -->
        <div class="section-heading">
            <span>4. Professional & Employment Experience</span>
            <span class="badge">{{ $basicInfo?->experiences->count() ?? 0 }} Positions Recorded</span>
        </div>
        @if($basicInfo && $basicInfo->experiences->isNotEmpty())
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 4%;">#</th>
                        <th style="width: 24%;">Designation / Post</th>
                        <th style="width: 28%;">Employer / Organization</th>
                        <th style="width: 15%;">Sector / Cadre</th>
                        <th style="width: 17%; text-align: center;">Duration</th>
                        <th style="width: 12%; text-align: center;">Verified</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($basicInfo->experiences as $idx => $exp)
                        <tr>
                            <td style="text-align: center;">{{ $idx + 1 }}</td>
                            <td>
                                <strong>{{ $exp->job_title }}</strong>
                                @if($exp->experience_type)
                                    <br><small style="color: #64748b;">Type: {{ $exp->experience_type }}</small>
                                @endif
                            </td>
                            <td>
                                {{ $exp->organization }}
                                @if($exp->description)
                                    <br><small style="color: #475569; font-style: italic;">{{ Str::limit($exp->description, 75) }}</small>
                                @endif
                            </td>
                            <td>{{ $exp->sector ?? 'Public Sector' }}</td>
                            <td style="text-align: center; font-size: 8.5pt;">
                                {{ $exp->start_date ? \Carbon\Carbon::parse($exp->start_date)->format('M Y') : '—' }} - 
                                {{ $exp->end_date ? \Carbon\Carbon::parse($exp->end_date)->format('M Y') : 'Present' }}
                            </td>
                            <td style="text-align: center;">
                                <span class="badge-pill {{ $exp->is_verified ? 'status-ok' : 'status-warn' }}">
                                    {{ $exp->is_verified ? 'Verified' : 'Declared' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="padding: 0.75rem; border: 1px dashed #cbd5e1; font-size: 9pt; color: #64748b; font-style: italic; margin-bottom: 0.75rem;">
                No professional work experience recorded on file.
            </div>
        @endif

        <!-- Section 5: Research Publications & Scholarly Output -->
        @if($basicInfo && $basicInfo->publications->isNotEmpty())
            <div class="section-heading">
                <span>5. Research Publications &amp; Scholarly Output</span>
                <span class="badge">{{ $basicInfo->publications->count() }} Publications</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 4%;">#</th>
                        <th style="width: 40%;">Paper Title & Authors</th>
                        <th style="width: 24%;">Journal & ISSN</th>
                        <th style="width: 10%; text-align: center;">HJRS / IF</th>
                        <th style="width: 11%; text-align: center;">Authorship</th>
                        <th style="width: 11%; text-align: center;">Year / Issue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($basicInfo->publications as $idx => $pub)
                        <tr>
                            <td style="text-align: center;">{{ $idx + 1 }}</td>
                            <td>
                                <strong>{{ $pub->title }}</strong>
                                @if($pub->authors)
                                    <br><small style="color: #475569;">Authors: {{ $pub->authors }}</small>
                                @endif
                                @if($pub->doi)
                                    <br><small style="color: #64748b; font-family: 'JetBrains Mono', monospace; font-size: 7.5pt;">DOI: {{ $pub->doi }}</small>
                                @endif
                            </td>
                            <td>
                                {{ $pub->journal_name }}
                                @if($pub->issn_print)
                                    <br><small style="color: #64748b;">ISSN: {{ $pub->issn_print }}</small>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <span class="badge-pill status-ok">{{ $pub->hjrs_category ?? 'Indexed' }}</span>
                                @if($pub->impact_factor)
                                    <br><small style="color: #0f172a; font-weight: 700;">IF: {{ $pub->impact_factor }}</small>
                                @endif
                            </td>
                            <td style="text-align: center; font-size: 8.5pt;">
                                @if($pub->author_order)
                                    <span>{{ $pub->author_order }}{{ in_array($pub->author_order, [1, '1']) ? 'st' : (in_array($pub->author_order, [2, '2']) ? 'nd' : (in_array($pub->author_order, [3, '3']) ? 'rd' : 'th')) }} Author</span>
                                @endif
                                @if($pub->is_corresponding_author)
                                    <br><span class="badge-pill status-ok" style="font-size: 6.8pt;">Corresponding</span>
                                @endif
                            </td>
                            <td style="text-align: center; font-size: 8.5pt;">
                                <strong>{{ $pub->publication_year }}</strong>
                                @if($pub->volume_issue)
                                    <br><small style="color: #64748b;">{{ $pub->volume_issue }}</small>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @elseif($viewMode === 'admin')
            <div class="section-heading">
                <span>5. Research Publications & Scholarly Output</span>
                <span class="badge">0 Records</span>
            </div>
            <div style="padding: 0.65rem 0.85rem; border: 1px dashed #cbd5e1; font-size: 8.8pt; color: #64748b; font-style: italic; margin-bottom: 0.75rem;">
                No scholarly research publications declared or recorded on file.
            </div>
        @endif

        <!-- Section 6: Research Projects, Sponsored Grants & Scientific Investigations -->
        @if($basicInfo && $basicInfo->researchProjects->isNotEmpty())
            <div class="section-heading">
                <span>6. Research Projects, Sponsored Grants & Scientific Investigations</span>
                <span class="badge">{{ $basicInfo->researchProjects->count() }} Projects</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 4%;">#</th>
                        <th style="width: 35%;">Project Title & Scope</th>
                        <th style="width: 22%;">Funding Agency / Sponsor</th>
                        <th style="width: 14%; text-align: right;">Grant Outlay</th>
                        <th style="width: 12%; text-align: center;">Role in Project</th>
                        <th style="width: 13%; text-align: center;">Status & Period</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($basicInfo->researchProjects as $idx => $proj)
                        <tr>
                            <td style="text-align: center;">{{ $idx + 1 }}</td>
                            <td>
                                <strong>{{ $proj->project_title }}</strong>
                                @if($proj->co_pis)
                                    <br><small style="color: #475569;">Co-PI(s): {{ $proj->co_pis }}</small>
                                @endif
                                @if($proj->turnitin_similarity_percentage)
                                    <br><small style="color: #64748b;">Similarity Index: {{ $proj->turnitin_similarity_percentage }}%</small>
                                @endif
                            </td>
                            <td>{{ $proj->funding_agency ?? 'National / International Sponsor' }}</td>
                            <td style="text-align: right; font-family: 'JetBrains Mono', monospace; font-weight: 700;">
                                {{ is_numeric($proj->grant_amount ?? null) ? $currency . ' ' . number_format((float)$proj->grant_amount, 2) : ($proj->grant_amount ?: '—') }}
                            </td>
                            <td style="text-align: center;">
                                <span class="badge-pill status-ok">{{ $proj->role_in_project ?? 'PI' }}</span>
                            </td>
                            <td style="text-align: center; font-size: 8.5pt;">
                                <strong>{{ $proj->status ?? 'Completed' }}</strong>
                                @if($proj->start_date)
                                    <br><small style="color: #64748b;">Started {{ $proj->start_date->format('M Y') }}</small>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @elseif($viewMode === 'admin')
            <div class="section-heading">
                <span>6. Research Projects, Sponsored Grants & Investigations</span>
                <span class="badge">0 Records</span>
            </div>
            <div style="padding: 0.65rem 0.85rem; border: 1px dashed #cbd5e1; font-size: 8.8pt; color: #64748b; font-style: italic; margin-bottom: 0.75rem;">
                No funded research grants or sponsored investigations declared on file.
            </div>
        @endif

        <!-- Section 7: Patents, Inventions & Intellectual Property Rights -->
        @if($basicInfo && $basicInfo->patents->isNotEmpty())
            <div class="section-heading">
                <span>7. Patents, Inventions & Intellectual Property Rights</span>
                <span class="badge">{{ $basicInfo->patents->count() }} Patents</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 4%;">#</th>
                        <th style="width: 42%;">Patent / Invention Title</th>
                        <th style="width: 20%;">Registry / Patent ID</th>
                        <th style="width: 14%;">Patent Office / Country</th>
                        <th style="width: 10%; text-align: center;">Registered</th>
                        <th style="width: 10%; text-align: center;">Commercialized</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($basicInfo->patents as $idx => $pat)
                        <tr>
                            <td style="text-align: center;">{{ $idx + 1 }}</td>
                            <td><strong>{{ $pat->title }}</strong></td>
                            <td style="font-family: 'JetBrains Mono', monospace;">{{ $pat->registry_id }}</td>
                            <td>{{ $pat->country }}</td>
                            <td style="text-align: center;">{{ $pat->date_registered ? $pat->date_registered->format('d M, Y') : '—' }}</td>
                            <td style="text-align: center;">
                                <span class="badge-pill {{ $pat->is_licensed ? 'status-ok' : 'status-warn' }}">
                                    {{ $pat->is_licensed ? 'Licensed' : 'Filed' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- Section 8: Academic Distinctions, Honors & Medals -->
        @if($basicInfo && $basicInfo->medals->isNotEmpty())
            <div class="section-heading">
                <span>8. Academic Distinctions, Honors & Medals</span>
                <span class="badge">{{ $basicInfo->medals->count() }} Medals / Distinctions</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 4%;">#</th>
                        <th style="width: 24%;">Medal / Distinction Type</th>
                        <th style="width: 46%;">Degree Title & Academic Distinction Particulars</th>
                        <th style="width: 13%; text-align: center;">Year Awarded</th>
                        <th style="width: 13%; text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($basicInfo->medals as $idx => $medal)
                        <tr>
                            <td style="text-align: center;">{{ $idx + 1 }}</td>
                            <td>
                                <strong>{{ $medal->medal_type }} Medal</strong>
                            </td>
                            <td>{{ $medal->degree_title }}</td>
                            <td style="text-align: center;">{{ $medal->year_awarded }}</td>
                            <td style="text-align: center;">
                                <span class="badge-pill {{ $medal->is_verified ? 'status-ok' : 'status-warn' }}">
                                    {{ $medal->is_verified ? 'Verified' : 'Declared' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- Section 9: Professional Trainings, Certifications & Courses -->
        @if($basicInfo && $basicInfo->trainings->isNotEmpty())
            <div class="section-heading">
                <span>9. Professional Trainings, Certifications & Specialized Courses</span>
                <span class="badge">{{ $basicInfo->trainings->count() }} Certifications</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 4%;">#</th>
                        <th style="width: 46%;">Course / Certification Title</th>
                        <th style="width: 32%;">Certifying Authority / Institution</th>
                        <th style="width: 18%; text-align: center;">Validity / Expiry</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($basicInfo->trainings as $idx => $train)
                        <tr>
                            <td style="text-align: center;">{{ $idx + 1 }}</td>
                            <td><strong>{{ $train->title }}</strong></td>
                            <td>{{ $train->body }}</td>
                            <td style="text-align: center;">
                                {{ $train->expiry_date ? $train->expiry_date->format('d M, Y') : 'Lifetime / Non-expiring' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- Section 10: Conferences, Seminars, Symposia & Workshops -->
        @if($basicInfo && $basicInfo->events->isNotEmpty())
            <div class="section-heading">
                <span>10. Conferences, Seminars, Symposia & Professional Workshops</span>
                <span class="badge">{{ $basicInfo->events->count() }} Events</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 4%;">#</th>
                        <th style="width: 32%;">Event Name & Type</th>
                        <th style="width: 16%;">Role</th>
                        <th style="width: 28%;">Paper / Topic Presented</th>
                        <th style="width: 20%;">Organizer & Location</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($basicInfo->events as $idx => $ev)
                        <tr>
                            <td style="text-align: center;">{{ $idx + 1 }}</td>
                            <td>
                                <strong>{{ $ev->event_name }}</strong>
                                @if($ev->event_type)
                                    <br><small style="color: #64748b;">Type: {{ $ev->event_type }}</small>
                                @endif
                            </td>
                            <td><span class="badge-pill status-ok">{{ $ev->event_role ?? 'Participant' }}</span></td>
                            <td>{{ $ev->paper_title ?? '—' }}</td>
                            <td>
                                {{ $ev->organizer ?? 'N/A' }}
                                @if($ev->location)
                                    <br><small style="color: #64748b;">{{ $ev->location }}</small>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- Section 11: Administrative Milestones & Development Projects -->
        @if($basicInfo && $basicInfo->milestones->isNotEmpty())
            <div class="section-heading">
                <span>11. Administrative Milestones & Institutional Development</span>
                <span class="badge">{{ $basicInfo->milestones->count() }} Milestones</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 4%;">#</th>
                        <th style="width: 35%;">Milestone / Initiative Title</th>
                        <th style="width: 14%; text-align: center;">Year</th>
                        <th style="width: 18%; text-align: right;">Financial Outlay</th>
                        <th style="width: 29%;">Description / Institutional Impact</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($basicInfo->milestones as $idx => $mil)
                        <tr>
                            <td style="text-align: center;">{{ $idx + 1 }}</td>
                            <td><strong>{{ $mil->title }}</strong></td>
                            <td style="text-align: center;">{{ $mil->year }}</td>
                            <td style="text-align: right; font-family: 'JetBrains Mono', monospace;">
                                {{ is_numeric($mil->budget ?? null) ? $currency . ' ' . number_format((float)$mil->budget, 2) : ($mil->budget ?: '—') }}
                            </td>
                            <td>{{ $mil->description ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- Section 12: Technical Skills & Support Tools -->
        @if($basicInfo && $basicInfo->tools->isNotEmpty())
            <div class="section-heading">
                <span>12. Technical Skills, Laboratory Instruments & Support Tools</span>
                <span class="badge">{{ $basicInfo->tools->count() }} Tools / Instruments</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 4%;">#</th>
                        <th style="width: 45%;">Tool / Instrument / Software Name</th>
                        <th style="width: 26%;">Proficiency Level</th>
                        <th style="width: 25%; text-align: center;">Hands-on Experience</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($basicInfo->tools as $idx => $t)
                        <tr>
                            <td style="text-align: center;">{{ $idx + 1 }}</td>
                            <td><strong>{{ $t->tool_name }}</strong></td>
                            <td><span class="badge-pill status-ok">{{ $t->proficiency_level }}</span></td>
                            <td style="text-align: center;">{{ $t->experience_years ? $t->experience_years . ' Years' : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- Section 13: Creative Works & Professional Portfolio -->
        @if($basicInfo && $basicInfo->portfolioItems->isNotEmpty())
            <div class="section-heading">
                <span>13. Creative Works & Professional Portfolio Items</span>
                <span class="badge">{{ $basicInfo->portfolioItems->count() }} Items</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 4%;">#</th>
                        <th style="width: 32%;">Project / Work Title</th>
                        <th style="width: 14%; text-align: center;">Completed</th>
                        <th style="width: 32%;">Description</th>
                        <th style="width: 18%;">Link / Repository</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($basicInfo->portfolioItems as $idx => $item)
                        <tr>
                            <td style="text-align: center;">{{ $idx + 1 }}</td>
                            <td><strong>{{ $item->title }}</strong></td>
                            <td style="text-align: center;">{{ $item->date_completed ? $item->date_completed->format('M Y') : '—' }}</td>
                            <td>{{ $item->description ?? '—' }}</td>
                            <td style="font-size: 8pt; word-break: break-all;">{{ $item->url ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- Section 14: Academic & Professional Referees -->
        @if($basicInfo && $basicInfo->referees->isNotEmpty())
            <div class="section-heading">
                <span>14. Academic & Professional Referees</span>
                <span class="badge">{{ $basicInfo->referees->count() }} Referees Recorded</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 4%;">#</th>
                        <th style="width: 28%;">Referee Name</th>
                        <th style="width: 32%;">Designation & Institution</th>
                        <th style="width: 16%;">Relationship</th>
                        <th style="width: 20%;">Contact Info</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($basicInfo->referees as $idx => $ref)
                        <tr>
                            <td style="text-align: center;">{{ $idx + 1 }}</td>
                            <td><strong>{{ $ref->name }}</strong></td>
                            <td>{{ $ref->designation }}<br><small style="color: #64748b;">{{ $ref->organization }}</small></td>
                            <td>{{ $ref->relation ?? 'Academic / Professional' }}</td>
                            <td style="font-size: 8.5pt;">
                                @if($ref->email)
                                    <span>✉ {{ $ref->email }}</span><br>
                                @endif
                                @if($ref->phone)
                                    <span>📞 {{ $ref->phone }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- Section 15: Candidate Solemn Undertaking -->
        <div class="declaration-box">
            <h4 style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 9.5pt; font-weight: 800; text-transform: uppercase; margin-bottom: 0.4rem; color: #0f172a;">
                Solemn Statutory Undertaking & Declaration by Applicant
            </h4>
            <p>
                I hereby solemnly declare that all information, qualifications, employment records, research papers, grants, and documents submitted with this application are genuine, true, complete, and accurate to the best of my knowledge and belief. I have not concealed any material fact or criminal conviction. I fully understand that any misstatement, forged credential, false declaration, or omission found at any stage of the recruitment process or even after appointment shall result in summary disqualification, cancellation of candidature/appointment, forfeiture of benefits, and initiation of statutory legal proceedings under the relevant penal statutes and service regulations.
            </p>
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 1.5rem; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 8.5pt;">
                <div>
                    <strong>Date:</strong> <u>{{ $application->submission_timestamp ? $application->submission_timestamp->format('d F, Y') : date('d F, Y') }}</u><br>
                    <strong>Place:</strong> <u>{{ $basicInfo?->domicile ?? 'Pakistan' }}</u>
                </div>
                <div style="text-align: center;">
                    <div style="border-bottom: 1.5px solid #0f172a; width: 220px; margin-bottom: 0.35rem;"></div>
                    <strong>Signature of Applicant</strong>
                </div>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- MASTER ADMINISTRATIVE DOSSIER SECTIONS (ONLY IN ADMIN PRINT VIEW)          --}}
        {{-- ========================================================================= --}}
        @if($viewMode === 'admin')
            <div class="page-break-before"></div>

            <div style="border: 2px solid #0f172a; padding: 0.5rem 1rem; background: #0f172a; color: #ffffff; font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; font-size: 1.05rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1.25rem; display: flex; justify-content: space-between; align-items: center;">
                <span>CONFIDENTIAL STATUTORY SCRUTINY & ASSESSMENT RECORD</span>
                <span style="font-size: 0.75rem; background: rgba(255,255,255,0.2); padding: 0.2rem 0.5rem; border-radius: 3px;">INTERNAL INSTITUTIONAL AUDIT</span>
            </div>

            <!-- Section 16: Scrutiny Committee Verification & Clearance -->
            <div class="section-heading">
                <span>16. Statutory Scrutiny Committee Verification & Clearance Certificate</span>
                @php
                    $isEligible = in_array($application->status, ['ELIGIBLE', 'UNDER_REVIEW', 'APPROVED']);
                    $isDeficient = $application->status === 'INCOMPLETE_DOSSIER';
                    $isDisqualified = in_array($application->status, ['INELIGIBLE', 'DISQUALIFIED', 'REJECTED']);
                @endphp
                <span class="badge {{ $isEligible ? 'badge-pill status-ok' : ($isDeficient ? 'badge-pill status-warn' : 'badge-pill status-danger') }}">
                    {{ $application->status_label }}
                </span>
            </div>
            <table class="bio-table">
                <tr>
                    <td class="label-col">Scrutiny Determination</td>
                    <td class="val-col" colspan="3">
                        <strong style="color: {{ $isEligible ? '#059669' : ($isDeficient ? '#d97706' : '#dc2626') }}; font-size: 1.05rem;">
                            {{ $isEligible ? 'ELIGIBLE / SHORTLISTED FOR NEXT STAGE' : ($isDeficient ? 'PROVISIONALLY ELIGIBLE / INCOMPLETE DOSSIER' : 'INELIGIBLE / DISQUALIFIED') }}
                        </strong>
                    </td>
                </tr>
                @if($application->disqualification_reason)
                    <tr>
                        <td class="label-col" style="color: #dc2626;">Deficiency / Ineligibility Grounds</td>
                        <td class="val-col" colspan="3" style="color: #dc2626; font-weight: 700;">
                            {{ $application->disqualification_reason }}
                        </td>
                    </tr>
                @endif
                <tr>
                    <td class="label-col">Terminal Degree Verification</td>
                    <td class="val-col">
                        <span class="badge-pill {{ $basicInfo?->degrees->isNotEmpty() ? 'status-ok' : 'status-warn' }}">
                            {{ $basicInfo?->degrees->isNotEmpty() ? 'Verified & On File' : 'Pending Verification' }}
                        </span>
                    </td>
                    <td class="label-col">Fee Settlement Verification</td>
                    <td class="val-col">
                        <span class="badge-pill {{ $isPaid ? 'status-ok' : 'status-danger' }}">
                            {{ $isPaid ? 'Settled & Reconciled' : 'Voucher Unpaid' }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="label-col">Statutory Quota Clearance</td>
                    <td class="val-col">{{ $basicInfo?->quota ?? 'Open Merit' }} (Domicile: {{ $basicInfo?->domicile ?? 'General' }})</td>
                    <td class="label-col">Scrutiny Verification Date</td>
                    <td class="val-col">{{ $application->updated_at ? $application->updated_at->format('d F, Y') : now()->format('d F, Y') }}</td>
                </tr>
            </table>

            <!-- Section 17: Academic Subject Expert Referee Peer Review Evaluation -->
            @if($application->reviews->isNotEmpty())
                <div class="section-heading">
                    <span>17. External Academic Subject Expert Referee Peer Reviews</span>
                    <span class="badge">{{ $application->reviews->count() }} Referees Assigned</span>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 4%;">#</th>
                            <th style="width: 25%;">Academic Referee</th>
                            <th style="width: 25%;">University & Country</th>
                            <th style="width: 15%; text-align: center;">Score %</th>
                            <th style="width: 16%; text-align: center;">Recommendation</th>
                            <th style="width: 15%; text-align: center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($application->reviews as $idx => $rev)
                            <tr>
                                <td style="text-align: center;">{{ $idx + 1 }}</td>
                                <td>
                                    <strong>{{ $rev->reviewer?->reviewerProfile?->name ?? ($rev->reviewer?->name ?? 'External Referee ' . ($idx + 1)) }}</strong><br>
                                    <small style="color: #64748b;">{{ $rev->reviewer?->reviewerProfile?->academic_rank ?? 'Expert Evaluator' }}</small>
                                </td>
                                <td>
                                    {{ $rev->reviewer?->reviewerProfile?->university ?? 'Partner Institution' }}<br>
                                    <small style="color: #64748b;">{{ $rev->reviewer?->reviewerProfile?->country ?? 'Overseas' }}</small>
                                </td>
                                <td style="text-align: center; font-family: 'JetBrains Mono', monospace; font-weight: 700;">
                                    {{ $rev->overall_score ? number_format($rev->overall_score, 1) . '%' : 'Pending' }}
                                </td>
                                <td style="text-align: center;">
                                    @php
                                        $rec = strtoupper(trim($rev->recommendation ?? ''));
                                        $isPositive = in_array($rec, ['RECOMMENDED', 'STRONGLY_RECOMMENDED', 'APPROVE']);
                                    @endphp
                                    <span class="badge-pill {{ $isPositive ? 'status-ok' : ($rec ? 'status-danger' : 'status-warn') }}">
                                        {{ $rec ?: 'Awaiting' }}
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="badge-pill {{ $rev->status === 'SUBMITTED' ? 'status-ok' : 'status-warn' }}">
                                        {{ $rev->status }}
                                    </span>
                                </td>
                            </tr>
                            @if($rev->report_text)
                                <tr>
                                    <td colspan="6" style="background: #f8fafc; font-size: 8.5pt; padding: 0.4rem 0.75rem; color: #334155; font-style: italic;">
                                        <strong>Referee Evaluation Excerpt:</strong> "{{ $rev->report_text }}"
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @endif

            <!-- Section 18: Statutory Selection Board Interview & Viva Grading Sheet -->
            @if($application->evaluations->isNotEmpty())
                <div class="section-heading">
                    <span>18. Statutory Selection Board Member Grading & Viva Scores</span>
                    <span class="badge">{{ $application->evaluations->count() }} Board Members Graded</span>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 4%;">#</th>
                            <th style="width: 25%;">Board Member / Evaluator</th>
                            <th style="width: 14%; text-align: center;">Academic (/40)</th>
                            <th style="width: 14%; text-align: center;">Research (/20)</th>
                            <th style="width: 14%; text-align: center;">Viva (/40)</th>
                            <th style="width: 14%; text-align: center;">Total (/100)</th>
                            <th style="width: 15%; text-align: center;">Outlier Flag</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($application->evaluations as $idx => $ev)
                            @php
                                $total = ($ev->academic_score ?? 0) + ($ev->medals_score ?? 0) + ($ev->publications_score ?? 0) + ($ev->interview_score ?? 0);
                            @endphp
                            <tr>
                                <td style="text-align: center;">{{ $idx + 1 }}</td>
                                <td>
                                    <strong>{{ $ev->boardMember?->name ?? 'Selection Board Member ' . ($idx + 1) }}</strong><br>
                                    <small style="color: #64748b;">Member Statutory Selection Board</small>
                                </td>
                                <td style="text-align: center; font-family: 'JetBrains Mono', monospace;">{{ number_format($ev->academic_score ?? 0, 1) }}</td>
                                <td style="text-align: center; font-family: 'JetBrains Mono', monospace;">{{ number_format(($ev->medals_score ?? 0) + ($ev->publications_score ?? 0), 1) }}</td>
                                <td style="text-align: center; font-family: 'JetBrains Mono', monospace; font-weight: 700;">{{ number_format($ev->interview_score ?? 0, 1) }}</td>
                                <td style="text-align: center; font-family: 'JetBrains Mono', monospace; font-weight: 800; color: #4f46e5;">{{ number_format($total, 1) }}</td>
                                <td style="text-align: center;">
                                    <span class="badge-pill {{ $ev->is_outlier ? 'status-danger' : 'status-ok' }}">
                                        {{ $ev->is_outlier ? 'Outlier' : 'Standard' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <!-- Section 19: Grievance & Appeal Record (If Any) -->
            @if($application->appeal_letter)
                <div class="section-heading">
                    <span>19. Statutory Appellate Representation & Grievance History</span>
                    <span class="badge badge-pill status-warn">Grievance Lodged</span>
                </div>
                <div class="admin-box">
                    <p style="margin-bottom: 0.5rem; font-size: 9.5pt;">
                        <strong>Grounds of Candidate Appeal:</strong><br>
                        <em>"{{ $application->appeal_letter }}"</em>
                    </p>
                    <div style="font-size: 8.5pt; color: #64748b;">
                        Appeal Verification Document: <strong>{{ $application->appeal_proof_path ? 'Attached on File' : 'No Annexure' }}</strong>
                    </div>
                </div>
            @endif

            <!-- Section 20: Official Institutional Sign-Off Block -->
            <div class="section-heading">
                <span>20. Official Institutional Statutory Sign-Off & Verification Endorsement</span>
            </div>
            <div class="signatures-grid">
                <div>
                    <div class="official-seal-box">Institutional Seal</div>
                    <div class="signature-line">
                        <strong>Member / Incharge</strong><br>
                        <span>Statutory Scrutiny Committee</span>
                    </div>
                </div>
                <div>
                    <div class="official-seal-box">Office Seal</div>
                    <div class="signature-line">
                        <strong>Additional Registrar</strong><br>
                        <span>Academics & Recruitment Division</span>
                    </div>
                </div>
                <div>
                    <div class="official-seal-box">Vice Chancellor Seal</div>
                    <div class="signature-line">
                        <strong>Vice Chancellor / Chairman</strong><br>
                        <span>Statutory Selection Board</span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Formal Document Provenance & Verification Endorsement Footer -->
        <div style="margin-top: 1.5rem; padding-top: 0.75rem; border-top: 1px dashed #94a3b8; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 8pt; color: #475569; display: flex; flex-direction: column; gap: 0.45rem; page-break-inside: avoid;">
            @if(!empty($letterFooter['disclaimer']))
                <div style="font-size: 7.5pt; color: #64748b; line-height: 1.4; border-bottom: 1px dotted #cbd5e1; padding-bottom: 0.35rem;">
                    <strong>Statutory Verification & Legal Endorsement:</strong> {{ $letterFooter['disclaimer'] }}
                </div>
            @endif
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                @if($letterFooter['show_audit'] ?? true)
                    <div>
                        <strong>Document Audit Trail:</strong> Printed / Downloaded by <strong>{{ $printedByName }} ({{ $printedByUsername }})</strong> - <em>{{ $printedByRole }}</em>
                    </div>
                    <div>
                        <strong>Printed / Downloaded on:</strong> {{ $printedAt->format('d M, Y \a\t h:i:s A') }}
                    </div>
                @endif
                @if($letterFooter['show_verify_ref'] ?? true)
                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 7.5pt; color: #64748b;">
                        VERIFY-REF: #{{ $application->tracking_number }}-{{ strtoupper(substr(hash('sha256', $application->id . $application->tracking_number . $printedAt->timestamp), 0, 10)) }}
                    </div>
                @endif
            </div>
            @if(!empty($letterFooter['contact']))
                <div style="font-size: 7.2pt; color: #64748b; text-align: center; margin-top: 0.15rem;">
                    {{ $letterFooter['contact'] }}
                </div>
            @endif
        </div>

    </div>

    <!-- Running Print Footer (Active on printed pages) -->
    <div class="document-print-footer">
        <span><strong>Official Dossier:</strong> {{ $application->tracking_number }} • Candidate: {{ $basicInfo ? trim(($basicInfo->first_name ?? '') . ' ' . ($basicInfo->last_name ?? '')) : $application->user->email }}</span>
        <span><strong>Printed by:</strong> {{ $printedByName }} ({{ $printedByUsername }}) - {{ $printedByRole }}</span>
        <span><strong>Printed on:</strong> {{ $printedAt->format('d/m/Y H:i:s') }}</span>
    </div>

</body>
</html>
