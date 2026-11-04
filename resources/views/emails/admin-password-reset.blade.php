<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ ($includePasswordInEmail || $method === 'email') ? 'Your New Hirely Portal Credentials' : 'Security Notice: Administrative Password Reset' }}</title>
</head>
<body style="font-family: 'Segoe UI', Arial, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 24px; line-height: 1.6;">
    <div style="max-width: 620px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.06);">
        <!-- Header Banner -->
        <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 26px 32px; color: #ffffff; border-bottom: 3px solid #6366f1;">
            <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: #06b6d4; font-weight: 800;">
                HIRELY RECRUITMENT ENTERPRISE OS • SECURITY SERVICES
            </div>
            <h1 style="margin: 8px 0 0; font-size: 21px; font-weight: 800; color: #ffffff; letter-spacing: -0.02em;">
                {{ ($includePasswordInEmail || $method === 'email') ? 'Administrative Password Reset' : 'Account Security Notification' }}
            </h1>
        </div>

        <!-- Body Content -->
        <div style="padding: 32px;">
            <p style="font-size: 15px; font-weight: 700; margin-top: 0; color: #0f172a;">
                Hello {{ $recipientName }},
            </p>

            @if($includePasswordInEmail || $method === 'email')
                <p style="font-size: 14px; color: #475569;">
                    Your account password for the Hirely University Recruitment Portal has been administratively reset by <strong>{{ $adminName }}</strong>.
                </p>

                <!-- Credentials Callout Box -->
                <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-left: 4px solid #6366f1; border-radius: 8px; padding: 20px 24px; margin: 24px 0;">
                    <h3 style="margin: 0 0 12px; font-size: 13.5px; text-transform: uppercase; letter-spacing: 0.5px; color: #334155; font-weight: 800;">
                        🔐 Your New Temporary Login Credentials
                    </h3>
                    <table style="width: 100%; font-size: 14px; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 6px 0; color: #64748b; width: 140px;">Portal URL:</td>
                            <td style="padding: 6px 0;"><a href="{{ $loginUrl }}" style="color: #4f46e5; font-weight: 700; text-decoration: none;">{{ $loginUrl }}</a></td>
                        </tr>
                        <tr>
                            <td style="padding: 6px 0; color: #64748b;">Registered Email:</td>
                            <td style="padding: 6px 0; font-weight: 700; color: #0f172a;">{{ $recipientEmail }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 6px 0; color: #64748b;">Temporary Password:</td>
                            <td style="padding: 6px 0; font-family: 'Courier New', monospace; font-size: 16px; font-weight: 800; color: #0284c7; letter-spacing: 1px;">
                                {{ $plainPassword }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 6px 0; color: #64748b;">Reset Timestamp:</td>
                            <td style="padding: 6px 0; font-size: 12.5px; color: #64748b;">{{ $resetTimestamp }}</td>
                        </tr>
                    </table>
                </div>

                <div style="text-align: center; margin: 28px 0 20px;">
                    <a href="{{ $loginUrl }}" style="display: inline-block; background: #4f46e5; color: #ffffff; font-weight: 700; font-size: 14px; padding: 12px 28px; border-radius: 6px; text-decoration: none; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);">
                        Sign In to Your Account
                    </a>
                </div>

                <div style="background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 6px; padding: 12px 16px; margin-top: 20px;">
                    <p style="font-size: 12.5px; color: #92400e; margin: 0; line-height: 1.5;">
                        <strong>Security Reminder:</strong> For your protection, please update your temporary password to a personal secure password upon signing in.
                    </p>
                </div>
            @else
                <p style="font-size: 14px; color: #475569;">
                    This is an automated security confirmation to inform you that your Hirely portal account password was administratively reset on <strong>{{ $resetTimestamp }}</strong> by <strong>{{ $adminName }}</strong>.
                </p>

                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #06b6d4; border-radius: 8px; padding: 16px 20px; margin: 20px 0;">
                    <p style="font-size: 13.5px; color: #334155; margin: 0;">
                        <strong>Delivery Channel:</strong> Your new temporary credentials were provided via 
                        @if($method === 'whatsapp')
                            <strong>Registered WhatsApp</strong>.
                        @else
                            <strong>Direct Administrator Handover / Screen Reveal</strong>.
                        @endif
                    </p>
                </div>

                <p style="font-size: 13.5px; color: #475569;">
                    If you requested assistance from the Registrar's Office or system administration, you may use the temporary password provided to you to sign in at <a href="{{ $loginUrl }}" style="color: #4f46e5; font-weight: 700;">{{ $loginUrl }}</a>.
                </p>

                <div style="background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 6px; padding: 14px 18px; margin-top: 20px;">
                    <p style="font-size: 12.5px; color: #991b1b; margin: 0; line-height: 1.5;">
                        <strong>Important Security Alert:</strong> If you did NOT request or authorize this password reset, please notify the university recruitment helpdesk immediately to secure your account.
                    </p>
                </div>
            @endif

            <p style="font-size: 12.5px; color: #64748b; margin-top: 28px; border-top: 1px solid #e2e8f0; padding-top: 16px;">
                This is an automated system notification sent to your registered email address <strong>{{ $recipientEmail }}</strong>. Replies to this email address are not monitored.
            </p>
        </div>

        <!-- Footer -->
        <div style="background: #f1f5f9; padding: 16px 32px; font-size: 11px; color: #64748b; text-align: center;">
            Hirely University Recruitment Operating System • Office of the Registrar
        </div>
    </div>
</body>
</html>
