<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Reminder</title>
</head>
<body style="margin:0;padding:0;background:#f6fafd;color:#1a2e3b;font-family:Arial,'Segoe UI',sans-serif;">
    <div style="width:100%;background:#f6fafd;padding:40px 16px;">
        <div style="max-width:640px;margin:0 auto;background:#ffffff;border:1px solid #e4eff8;border-top:4px solid #0086da;">

            <div style="padding:32px 40px;background:#0086da;color:#ffffff;">
                <h1 style="margin:0;font-size:26px;font-weight:800;letter-spacing:.06em;">TEJADA CLINIC</h1>
                <p style="margin:10px 0 0;font-size:14px;line-height:1.75;color:rgba(255,255,255,0.85);">
                    This is your {{ strtolower($metaLabel) }} reminder for your upcoming appointment.
                </p>
            </div>

            <div style="padding:36px 40px 20px;">
                <h2 style="margin:0 0 12px;font-size:22px;font-weight:800;color:#1a2e3b;letter-spacing:-.02em;">Appointment Reminder</h2>
                <p style="margin:0 0 16px;font-size:14px;line-height:1.8;color:#587189;">
                    Hello {{ $patientName }}, this is your reminder for your upcoming appointment with Tejada Clinic.
                </p>

                <div style="margin:22px 0;border:1px solid #e4eff8;background:#f6fafd;">
                    <div style="padding:14px 20px;border-bottom:1px solid #e4eff8;">
                        <span style="display:block;margin-bottom:5px;font-size:10px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:#7a9db5;">Service</span>
                        <span style="display:block;font-size:14px;font-weight:700;color:#1a2e3b;">{{ $serviceName }}</span>
                    </div>
                    <div style="padding:14px 20px;border-bottom:1px solid #e4eff8;">
                        <span style="display:block;margin-bottom:5px;font-size:10px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:#7a9db5;">Date</span>
                        <span style="display:block;font-size:14px;font-weight:700;color:#1a2e3b;">{{ $appointmentAt->format('F d, Y') }}</span>
                    </div>
                    <div style="padding:14px 20px;border-bottom:1px solid #e4eff8;">
                        <span style="display:block;margin-bottom:5px;font-size:10px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:#7a9db5;">Time</span>
                        <span style="display:block;font-size:14px;font-weight:700;color:#1a2e3b;">{{ $appointmentAt->format('h:i A') }}</span>
                    </div>
                    <div style="padding:14px 20px;">
                        <span style="display:block;margin-bottom:5px;font-size:10px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:#7a9db5;">Status</span>
                        <span style="display:inline-block;padding:4px 12px;border:1px solid #bae6fd;background:#f0f9ff;color:#0369a1;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.14em;">{{ $status }}</span>
                    </div>
                </div>

                <p style="margin:0 0 24px;font-size:14px;line-height:1.8;color:#587189;">
                    If you need to review your appointment details, please log in to your patient account.
                </p>

                <a href="{{ route('patient.dashboard') }}"
                    style="display:inline-block;background:#0086da;color:#ffffff;text-decoration:none;padding:14px 28px;font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;">
                    View Appointment
                </a>
            </div>

            <div style="padding:20px 40px;border-top:1px solid #e4eff8;font-size:11px;line-height:1.7;color:#7a9db5;text-align:center;background:#f6fafd;">
                &copy; {{ date('Y') }} Tejadent Clinic &mdash; All rights reserved.
            </div>

        </div>
    </div>
</body>
</html>
