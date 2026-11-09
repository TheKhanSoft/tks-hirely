@php
    $letterhead = \App\Models\SystemSetting::getLetterheadDetails();
    $letterFooter = \App\Models\SystemSetting::getLetterFooter();
    $basicInfo = $application->user?->basicInfo;
    $campaign = $application->campaign;
    $refNumber = 'RO/SB/' . date('Y') . '/' . ($application->tracking_number ?: ('APP-' . str_pad($application->id, 5, '0', STR_PAD_LEFT)));
    $issuedDate = $application->interview_call_letter_issued_at ? \Carbon\Carbon::parse($application->interview_call_letter_issued_at) : now();
    $interviewDate = $application->interview_date ? \Carbon\Carbon::parse($application->interview_date) : null;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statutory Interview Call Letter — {{ $application->tracking_number }} — {{ $orgName }}</title>
    
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
            line-height: 1.5;
            font-size: 10.5pt;
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
            padding: 0.5rem 1.1rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }

        .btn-toolbar-primary {
            background: #2563eb;
            color: #ffffff;
        }
        .btn-toolbar-primary:hover {
            background: #1d4ed8;
        }

        .btn-toolbar-secondary {
            background: rgba(255,255,255,0.12);
            color: #ffffff;
            border-color: rgba(255,255,255,0.2);
        }
        .btn-toolbar-secondary:hover {
            background: rgba(255,255,255,0.2);
        }

        .document-wrapper {
            max-width: 860px;
            margin: 1.5rem auto 3rem;
            background: #ffffff;
            padding: 2.75rem 3rem;
            box-shadow: 0 4px 25px rgba(0,0,0,0.08);
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            position: relative;
        }

        /* Letterhead */
        .official-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            border-bottom: 2.5px solid #0f172a;
            padding-bottom: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .header-logo {
            width: 88px;
            height: 88px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .header-title-box {
            flex: 1;
            text-align: center;
        }

        .header-title-box h1 {
            font-size: 1.45rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #0f172a;
            margin: 0;
            line-height: 1.2;
        }

        .header-title-box .header-subheading {
            font-size: 0.95rem;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 0.15rem;
        }

        .header-title-box .header-office {
            font-size: 1.30rem;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-top: 0.25rem;
            line-height: 1.25;
        }

        .header-title-box .header-tagline {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 0.2rem;
            font-style: italic;
        }

        .confidential-stamp {
            border: 2px solid #dc2626;
            color: #dc2626;
            padding: 0.35rem 0.65rem;
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            border-radius: 4px;
            text-align: center;
            flex-shrink: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Dispatch strip */
        .dispatch-strip {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.88rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 0.75rem;
        }

        .dispatch-ref {
            font-weight: 700;
            color: #0f172a;
            font-family: 'JetBrains Mono', monospace;
        }

        /* Candidate Details */
        .address-box {
            margin-bottom: 1.5rem;
            font-size: 10.5pt;
            line-height: 1.6;
        }

        .subject-box {
            background: #f1f5f9;
            border-left: 4px solid #2563eb;
            padding: 0.75rem 1rem;
            margin-bottom: 1.5rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.95rem;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: 0.02em;
        }

        .body-text {
            font-size: 11pt;
            text-align: justify;
            margin-bottom: 1.5rem;
            line-height: 1.7;
        }

        /* Interview Schedule Box */
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.25rem 0 1.75rem 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.9rem;
            border: 1.5px solid #0f172a;
        }

        .schedule-table th {
            background: #0f172a;
            color: #ffffff;
            padding: 0.65rem 0.85rem;
            text-align: left;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .schedule-table td {
            padding: 0.75rem 0.85rem;
            border-bottom: 1px solid #cbd5e1;
            color: #1e293b;
        }

        .schedule-table tr:last-child td {
            border-bottom: none;
        }

        .schedule-table .param-name {
            width: 28%;
            font-weight: 700;
            background: #f8fafc;
            color: #334155;
            border-right: 1px solid #cbd5e1;
        }

        .schedule-table .param-val {
            font-weight: 800;
            color: #0f172a;
        }

        /* Instructions List */
        .instructions-heading {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.95rem;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
            margin-bottom: 0.65rem;
        }

        .instructions-list {
            margin-left: 1.5rem;
            margin-bottom: 1.5rem;
            line-height: 1.65;
            font-size: 10pt;
        }

        .instructions-list li {
            margin-bottom: 0.45rem;
        }

        .instructions-list strong {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 9.5pt;
        }

        /* Signature block */
        .signatory-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 3rem;
            padding-top: 1.5rem;
        }

        .barcode-box {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.72rem;
            color: #475569;
            border: 1px solid #cbd5e1;
            padding: 0.5rem 0.75rem;
            background: #f8fafc;
            border-radius: 4px;
        }

        .signature-box {
            text-align: right;
            min-width: 240px;
        }

        .signature-line {
            width: 200px;
            border-bottom: 1.5px solid #0f172a;
            margin-left: auto;
            margin-bottom: 0.5rem;
            height: 40px;
        }

        .signatory-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 0.92rem;
            color: #0f172a;
        }

        .signatory-sub {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.8rem;
            color: #64748b;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #ffffff !important;
                font-size: 10.5pt !important;
            }
            .no-print-toolbar {
                display: none !important;
            }
            .document-wrapper {
                margin: 0 !important;
                padding: 1.5rem 2rem !important;
                box-shadow: none !important;
                border: none !important;
                max-width: 100% !important;
            }
            @page {
                size: A4;
                margin: 1.2cm;
            }
        }
    </style>
</head>
<body>

    <!-- On-Screen Control Toolbar -->
    <div class="no-print-toolbar">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="font-size: 1.2rem;">✉️</span>
            <div>
                <strong style="font-size: 0.95rem;">Statutory Selection Board Interview Call Letter</strong>
                <span style="opacity: 0.75; font-size: 0.8rem; margin-left: 0.5rem;">
                    Candidate: {{ $basicInfo?->full_name ?? $application->user?->name }} ({{ $application->tracking_number }})
                </span>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <a href="javascript:window.print()" class="btn-toolbar btn-toolbar-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                <span>Print Official Call Letter</span>
            </a>

            <button type="button" onclick="window.close(); if(!window.closed) history.back();" class="btn-toolbar btn-toolbar-secondary">
                <span>Close / Back</span>
            </button>
        </div>
    </div>

    <!-- Official Printable Document -->
    <div class="document-wrapper">
        
        <!-- Official Letterhead -->
        <div class="official-header">
            @if(!empty($letterhead['logo_url']))
                <img src="{{ $letterhead['logo_url'] }}" alt="University Crest" class="header-logo">
            @else
                <div class="header-logo" style="display: flex; align-items: center; justify-content: center; background: #0f172a; color: #fff; font-size: 1.8rem; font-weight: 800; border-radius: 8px;">
                    🏛️
                </div>
            @endif

            <div class="header-title-box">
                <h1>{{ $letterhead['heading'] }}</h1>
                @if(!empty($letterhead['subheading']))
                    <div class="header-subheading">{{ $letterhead['subheading'] }}</div>
                @endif
                <div class="header-office">
                    {{ $letterhead['office'] ?: 'OFFICE OF THE REGISTRAR • SELECTION BOARD SECRETARIAT' }}
                </div>
                <div class="header-tagline">
                    Statutory University Appointments &amp; Service Statutes
                </div>
            </div>

            <div class="confidential-stamp">
                STATUTORY<br>SUMMONS
            </div>
        </div>

        <!-- Dispatch Reference Strip -->
        <div class="dispatch-strip">
            <div>
                <span>Ref. No: </span>
                <span class="dispatch-ref">{{ $refNumber }}</span>
            </div>
            <div>
                <span>Dated: </span>
                <strong style="color: #0f172a;">{{ $issuedDate->format('d F, Y') }}</strong>
            </div>
        </div>

        <!-- Candidate Address Box -->
        <div class="address-box">
            <strong>To:</strong><br>
            <strong style="font-size: 11pt; color: #0f172a; text-transform: uppercase;">
                {{ $basicInfo?->full_name ?? $application->user?->name }}
            </strong><br>
            @if($basicInfo?->father_name)
                <span>S/O, D/O, W/O: <strong>{{ $basicInfo->father_name }}</strong></span><br>
            @endif
            @if($basicInfo?->cnic_passport)
                <span>CNIC / Passport: <strong style="font-family: 'JetBrains Mono', monospace;">{{ $basicInfo->cnic_passport }}</strong></span><br>
            @endif
            <span>Tracking Number: <strong style="font-family: 'JetBrains Mono', monospace;">{{ $application->tracking_number }}</strong></span><br>
            @if($basicInfo?->postal_address)
                <span>Mailing Address: {{ $basicInfo->postal_address }}, {{ $basicInfo->city }}</span><br>
            @endif
            @if($basicInfo?->mobile_number)
                <span>Contact: {{ $basicInfo->mobile_number }} | Email: {{ $application->user?->email }}</span>
            @endif
        </div>

        <!-- Subject Line -->
        <div class="subject-box">
            Subject: Call Letter for Interview Before the Statutory Selection Board for the Post of 
            "{{ $campaign?->title ?? 'Advertised Post' }}" 
            @if($campaign?->job_code)
                (Job Code: {{ $campaign->job_code }})
            @endif
        </div>

        <!-- Summons Body Text -->
        <div class="body-text">
            <p>Dear Candidate,</p>
            <p style="margin-top: 0.5rem;">
                With reference to your application for the post of <strong>{{ $campaign?->title ?? 'Advertised Position' }}</strong> in the <strong>{{ $campaign?->department?->name ?? 'University Academic / Administrative Cadre' }}</strong>, you are hereby informed that after formal scrutiny and statutory preliminary screening, you have been shortlisted to appear for an interview before the <strong>Statutory Selection Board</strong>.
            </p>
            <p style="margin-top: 0.5rem;">
                You are accordingly advised to appear in person before the Selection Board strictly according to the following schedule:
            </p>
        </div>

        <!-- Interview Schedule Table -->
        <table class="schedule-table">
            <thead>
                <tr>
                    <th colspan="2">Statutory Interview Schedule &amp; Reporting Parameters</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="param-name">Date of Interview</td>
                    <td class="param-val">
                        {{ $interviewDate ? $interviewDate->format('l, d F Y') : 'To be notified by Secretariat' }}
                    </td>
                </tr>
                <tr>
                    <td class="param-name">Reporting Time</td>
                    <td class="param-val" style="color: #2563eb;">
                        {{ $application->interview_reporting_time ?: '09:00 AM Sharp' }}
                    </td>
                </tr>
                <tr>
                    <td class="param-name">Interview Commences At</td>
                    <td class="param-val">
                        {{ $application->interview_time ?: '10:00 AM PST' }}
                    </td>
                </tr>
                <tr>
                    <td class="param-name">Interview Venue</td>
                    <td class="param-val">
                        {{ $application->interview_venue ?: 'Main Administration Complex, University Secretariat' }}
                    </td>
                </tr>
                <tr>
                    <td class="param-name">Committee Room</td>
                    <td class="param-val">
                        {{ $application->interview_committee_room ?: 'Statutory Syndicate & Selection Board Conference Room' }}
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Mandatory Requisites & Instructions -->
        <div class="instructions-heading">
            Mandatory Instructions &amp; Original Documents to Produce:
        </div>
        <ol class="instructions-list">
            <li>
                <strong>Original CNIC:</strong> You must produce your original Computerized National Identity Card (CNIC) or valid Passport at the reception desk for biometrical/identity verification.
            </li>
            <li>
                <strong>Original Academic Credentials:</strong> You are required to produce all original degrees, diplomas, certificates, and Detailed Marks Certificates (DMCs) from Matriculation up to the terminal degree (PhD / MS / MPhil / Master's / Bachelor's).
            </li>
            <li>
                <strong>HEC / IBCC Equivalence:</strong> In case of foreign degrees, you must present the official original Equivalence Certificate issued by the Higher Education Commission (HEC) or Inter Board Coordination Commission (IBCC).
            </li>
            <li>
                <strong>Departmental Permission / NOC:</strong> Candidates currently serving in Government, Autonomous, or Semi-Autonomous bodies must produce the original Departmental Permission Certificate (NOC) from their competent authority.
            </li>
            <li>
                <strong>Research Publications &amp; Reprints:</strong> For academic and research faculty chairs, you must bring original copies/reprints of your HEC-recognized journal publications.
            </li>
            <li>
                <strong>Attested Dossier Copy:</strong> Bring one complete set of attested photocopies of all educational certificates, experience credentials, and research portfolio.
            </li>
            @if(!empty($application->interview_instructions))
                <li>
                    <strong>Specific Secretariat Directive:</strong> {{ $application->interview_instructions }}
                </li>
            @endif
        </ol>

        <!-- Statutory Notice / Disclaimers -->
        <div style="background: #f8fafc; border: 1px solid #cbd5e1; padding: 0.85rem 1rem; font-size: 8.8pt; color: #475569; margin-bottom: 2rem; font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.55;">
            <strong>Statutory Terms &amp; Conditions:</strong><br>
            1. No Travelling Allowance (TA) or Daily Allowance (DA) will be admissible for appearing before the Selection Board.<br>
            2. Failure to appear at the specified date and time, or failure to produce the required original documents, shall result in immediate forfeiture of candidacy.<br>
            3. The University reserves the statutory right to increase/decrease the number of positions or postpone/cancel the selection proceedings without assigning any reason.
        </div>

        <!-- Signatory Block -->
        <div class="signatory-row">
            <div class="barcode-box">
                <div><strong>TRACKING:</strong> {{ $application->tracking_number }}</div>
                <div><strong>SECURITY HASH:</strong> {{ substr(hash('sha256', $application->id . '-' . $application->tracking_number), 0, 16) }}</div>
                <div><strong>DISPATCH:</strong> {{ $issuedDate->format('d-M-Y H:i:s') }}</div>
                <div style="margin-top: 4px; font-size: 0.65rem; color: #64748b;">
                    Verify authenticity at: {{ url('/verify') }}
                </div>
            </div>

            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signatory-title">Registrar / Secretary</div>
                <div class="signatory-sub">Statutory Selection Board</div>
                <div class="signatory-sub">{{ $orgName }}</div>
            </div>
        </div>

        <!-- Footer Notice -->
        <div style="margin-top: 2rem; padding-top: 0.75rem; border-top: 1px solid #cbd5e1; font-size: 7.5pt; color: #64748b; text-align: center; font-family: 'Plus Jakarta Sans', sans-serif;">
            {{ $letterFooter['text'] ?? 'Official computer-generated Selection Board Call Letter issued under statutory university recruitment authority.' }}
        </div>
    </div>

</body>
</html>