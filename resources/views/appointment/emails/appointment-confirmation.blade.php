<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Confirmation</title>
</head>
<body style="margin:0;padding:0;background:#f7f7f7;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f7f7f7;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;">
                    <tr>
                        <td style="background:#0789da;color:#ffffff;padding:20px 24px;">
                            <h1 style="margin:0;font-size:20px;">Appointment Confirmation</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;color:#111827;">
                            <p style="margin:0 0 12px 0;">Hi {{ $name }},</p>
                            <p style="margin:0 0 16px 0;">
                                Your appointment request has been received. Here are the details:
                            </p>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:16px 0;border-collapse:collapse;">
                                <tr>
                                    <td style="padding:8px 0;color:#6b7280;width:140px;">Service</td>
                                    <td style="padding:8px 0;font-weight:bold;color:#111827;">{{ $service_name }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;color:#6b7280;">Date & Time</td>
                                    <td style="padding:8px 0;font-weight:bold;color:#111827;">{{ $appointment_date }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;color:#6b7280;">Status</td>
                                    <td style="padding:8px 0;font-weight:bold;color:#111827;">Pending</td>
                                </tr>
                            </table>
                            <p style="margin:16px 0 0 0;">
                                We will notify you once the appointment is approved.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 24px;background:#f9fafb;color:#6b7280;font-size:12px;">
                            If you did not request this appointment, you can ignore this email.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
