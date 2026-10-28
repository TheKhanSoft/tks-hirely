<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Academic Reviewer Empanelment & Login Credentials</title>
</head>
<body style="font-family: 'Segoe UI', Arial, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 24px; line-height: 1.6;">
    <div style="max-width: 640px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.06);">
        <!-- Header Banner -->
        <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 28px 32px; color: #ffffff; border-bottom: 3px solid #6366f1;">
            <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: #06b6d4; font-weight: 800;">
                OFFICE OF THE REGISTRAR • UNIVERSITY RECRUITMENT SECRETARIAT
            </div>
            <h1 style="margin: 8px 0 0; font-size: 22px; font-weight: 800; color: #ffffff; letter-spacing: -0.02em;">
                Empanelment as Academic Subject Reviewer
            </h1>
        </div>

        <!-- Body Content -->
        <div style="padding: 32px;">
            <p style="font-size: 16px; font-weight: 600; margin-top: 0; color: #0f172a;">
                Respected {{ $academicRank }} {{ $name }},
            </p>

            <p style="font-size: 14px; color: #475569;">
                The University is pleased to invite you to serve as an <strong>External Subject Expert & Peer Reviewer</strong> for faculty recruitment evaluations in your specialized discipline of <strong>{{ $subjectField }}</strong>.
            </p>

            <p style="font-size: 14px; color: #475569;">
                An institutional evaluation account has been configured for you in the Hirely University Recruitment OS based on your standing at <strong>{{ $university }}</strong>.
            </p>

            <!-- Credentials Callout Box -->
            <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-left: 4px solid #6366f1; border-radius: 8px; padding: 20px 24px; margin: 24px 0;">
                <h3 style="margin: 0 0 12px; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; color: #334155; font-weight: 800;">
                    🔐 Your Confidential Portal Login Credentials
                </h3>
                <table style="width: 100%; font-size: 14px; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 6px 0; color: #64748b; width: 140px;">Portal URL:</td>
                        <td style="padding: 6px 0;"><a href="{{ $loginUrl }}" style="color: #4f46e5; font-weight: 700; text-decoration: none;">{{ $loginUrl }}</a></td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b;">Login Email:</td>
                        <td style="padding: 6px 0; font-weight: 700; color: #0f172a;">{{ $email }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b;">Temporary Password:</td>
                        <td style="padding: 6px 0; font-family: 'Courier New', monospace; font-size: 16px; font-weight: 800; color: #0284c7; letter-spacing: 1px;">
                            {{ $plainPassword }}
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Reviewer Guidelines / Instructions -->
            <h3 style="font-size: 15px; color: #0f172a; margin: 24px 0 12px;">
                📋 Overview of Reviewer Responsibilities & Guidance:
            </h3>

            <ol style="font-size: 13.5px; color: #475569; padding-left: 20px; line-height: 1.7; margin: 0 0 24px;">
                <li><strong>Confidential Dossier Inspection:</strong> When academic candidates (Lecturer, Assistant Professor, Associate Professor, Professor) in <strong>{{ $subjectField }}</strong> are assigned to you, their full research publications, teaching portfolio, patents, and qualifications will appear on your Evaluation Desk.</li>
                <li><strong>HEC & Academic Scrutiny:</strong> Please evaluate candidate publications against HEC journal indexing criteria (W, X, Y categories) and verify substantive contributions to the discipline.</li>
                <li><strong>Qualitative Evaluation & Score:</strong> Submit your qualitative evaluation comments, overall suitability score, and definitive recommendation (<em>Strongly Recommended, Recommended, or Not Recommended</em>).</li>
                <li><strong>Confidentiality Notice:</strong> In accordance with university statutory appointment rules, all candidate dossiers and evaluation reports remain strictly confidential.</li>
            </ol>

            <div style="text-align: center; margin: 28px 0 16px;">
                <a href="{{ $loginUrl }}" style="display: inline-block; background: #4f46e5; color: #ffffff; font-weight: 700; font-size: 14px; padding: 12px 28px; border-radius: 6px; text-decoration: none; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);">
                    Sign In to Reviewer Portal
                </a>
            </div>

            <p style="font-size: 12.5px; color: #64748b; margin-top: 24px; border-top: 1px solid #e2e8f0; padding-top: 16px;">
                If you have questions regarding candidate portfolios or require technical assistance, please contact the Recruitment Secretariat at the Office of the Registrar.
            </p>
        </div>

        <!-- Footer -->
        <div style="background: #f1f5f9; padding: 16px 32px; font-size: 11px; color: #64748b; text-align: center;">
            Hirely University Recruitment Operating System • Confidential University Records
        </div>
    </div>
</body>
</html>
