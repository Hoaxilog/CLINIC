<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Reminder</title>
</head>
<body style="margin:0;padding:0;background:#f6fafd;font-family:Arial,sans-serif;color:#1a2e3b;">
    <div style="max-width:640px;margin:0 auto;padding:32px 20px;">
        <div style="background:#0086da;padding:28px 32px;color:#ffffff;">
            <div style="font-size:12px;font-weight:700;letter-spacing:.22em;text-transform:uppercase;opacity:.75;">Tejada Clinic</div>
            <h1 style="margin:12px 0 0;font-size:28px;line-height:1.15;">Appointment Reminder</h1>
        </div>

        <div style="background:#ffffff;border:1px solid #d7ebf8;padding:32px;">
            <p style="margin:0 0 16px;font-size:16px;line-height:1.8;">
                Hello {{ $patientName }},
            </p>
            <p style="margin:0 0 20px;font-size:15px;line-height:1.8;color:#587189;">
                This is your {{ strtolower($metaLabel) }} reminder for your upcoming appointment with Tejada Clinic.
            </p>

            <div style="border:1px solid #e4eff8;background:#f8fbfe;padding:20px 22px;margin:0 0 22px;">
                <p style="margin:0 0 10px;font-size:12px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:#0086da;">Appointment Details</p>
                <p style="margin:0 0 8px;font-size:15px;"><strong>Service:</strong> {{ $serviceName }}</p>
                <p style="margin:0 0 8px;font-size:15px;"><strong>Date:</strong> {{ $appointmentAt->format('F d, Y') }}</p>
                <p style="margin:0 0 8px;font-size:15px;"><strong>Time:</strong> {{ $appointmentAt->format('h:i A') }}</p>
                <p style="margin:0;font-size:15px;"><strong>Status:</strong> {{ $status }}</p>
            </div>

            <p style="margin:0 0 20px;font-size:15px;line-height:1.8;color:#587189;">
                If you need to review your appointment details, please log in to your patient account.
            </p>

            <a href="{{ route('patient.dashboard') }}"
                style="display:inline-block;background:#0086da;color:#ffffff;text-decoration:none;padding:14px 24px;font-size:12px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;">
                View Appointment
            </a>
        </div>
    </div>
</body>
</html>
