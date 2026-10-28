<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Candidate Dossier Assigned for Academic Review</title>
</head>
<body style="font-family: 'Segoe UI', Arial, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 24px; line-height: 1.6;">
    <div style="max-width: 640px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.06);">
        <!-- Header Banner -->
        <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 28px 32px; color: #ffffff; border-bottom: 3px solid #06b6d4;">
            <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: #06b6d4; font-weight: 800;">
                OFFICE OF THE REGISTRAR • ACADEMIC SELECTION SECRETARIAT
            </div>
            <h1 style="margin: 8px 0 0; font-size: 21px; font-weight: 800; color: #ffffff; letter-spacing: -0.02em;">
                Academic Dossier Dispatched for Peer Review
            </h1>
        </div>

        <!-- Body Content -->
        <div style="padding: 32px;">
            <p style="font-size: 16px; font-weight: 600; margin-top: 0; color: #0f172a;">
                Dear {{ $reviewerName }},
            </p>

            <p style="font-size: 14px; color: #475569;">
                The Registrar's Office has assigned an academic candidacy dossier in your discipline for your expert evaluation.
            </p>

            <!-- Case Particulars Table -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px 24px; margin: 20px 0;">
                <table style="width: 100%; font-size: 13.5px; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 6px 0; color: #64748b; width: 140px;">Candidate Name:</td>
                        <td style="padding: 6px 0; font-weight: 700; color: #0f172a;">{{ $candidateName }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b;">Target Position:</td>
                        <td style="padding: 6px 0; font-weight: 700; color: #4f46e5;">{{ $positionTitle }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b;">Department / Field:</td>
                        <td style="padding: 6px 0; font-weight: 600; color: #0f172a;">{{ $department }}</td>
                    </tr>
                    @if($dueDate)
                        <tr>
                            <td style="padding: 6px 0; color: #64748b;">Review Deadline:</td>
                            <td style="padding: 6px 0; font-weight: 700; color: #d97706;">{{ $dueDate }}</td>
                        </tr>
                    @endif
                </table>

                @if($confidentialNotes)
                    <div style="margin-top: 14px; padding-top: 12px; border-top: 1px dashed #cbd5e1; font-size: 13px; color: #334155;">
                        <strong style="color: #64748b; text-transform: uppercase; font-size: 11px;">Registrar's Confidential Instructions:</strong>
                        <p style="margin: 4px 0 0; font-style: italic;">{{ $confidentialNotes }}</p>
                    </div>
                @endif
            </div>

            <div style="text-align: center; margin: 28px 0 16px;">
                <a href="{{ $evalUrl }}" style="display: inline-block; background: #0891b2; color: #ffffff; font-weight: 700; font-size: 14px; padding: 12px 28px; border-radius: 6px; text-decoration: none; box-shadow: 0 4px 12px rgba(8, 145, 178, 0.3);">
                    Open Candidate Evaluation Desk
                </a>
            </div>

            <p style="font-size: 12.5px; color: #64748b; margin-top: 24px; border-top: 1px solid #e2e8f0; padding-top: 16px;">
                Please note that external peer reviews are statutory confidential documents under university recruitment regulations.
            </p>
        </div>

        <!-- Footer -->
        <div style="background: #f1f5f9; padding: 16px 32px; font-size: 11px; color: #64748b; text-align: center;">
            Hirely University Recruitment Operating System • Confidential University Records
        </div>
    </div>
</body>
</html>
