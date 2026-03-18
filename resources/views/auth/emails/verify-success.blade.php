<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verified - Tejadent</title>
</head>
<body style="margin:0;padding:40px 16px;background:#f6fafd;font-family:Arial,'Segoe UI',sans-serif;color:#1a2e3b;">
    <div style="max-width:640px;margin:0 auto;background:#ffffff;border:1px solid #e4eff8;border-top:4px solid #0086da;">

        <div style="padding:32px 40px;background:#0086da;color:#ffffff;">
            <h1 style="margin:0;font-size:26px;font-weight:800;letter-spacing:.06em;">TEJADA CLINIC</h1>
            <p style="margin:10px 0 0;font-size:14px;line-height:1.75;color:rgba(255,255,255,0.85);">Your account is ready. You can now sign in and manage appointments with the clinic.</p>
        </div>

        <div style="padding:36px 40px 40px;text-align:center;">
            <div style="width:80px;height:80px;margin:0 auto 24px;background:#ecfdf5;border:1px solid #a7f3d0;display:flex;align-items:center;justify-content:center;">
                <svg style="width:36px;height:36px;color:#059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="square" stroke-linejoin="miter" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h2 style="margin:0 0 14px;font-size:22px;font-weight:800;color:#1a2e3b;letter-spacing:-.02em;">Email verified</h2>
            <p style="margin:0 auto 20px;max-width:420px;font-size:14px;line-height:1.8;color:#587189;">
                Welcome, <strong style="color:#1a2e3b;">{{ session('verified_email') ?? 'your email' }}</strong>. Your email address has been confirmed successfully.
            </p>
            <p style="margin:0 auto 30px;max-width:440px;font-size:13px;line-height:1.7;color:#7a9db5;">You can now sign in and continue to your dashboard to book appointments and view your records.</p>

            <a href="{{ route('login') }}"
                style="display:inline-block;padding:14px 32px;background:#0086da;color:#ffffff;text-decoration:none;font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;">
                Login to Continue
            </a>
        </div>

        <div style="padding:20px 40px;border-top:1px solid #e4eff8;font-size:11px;line-height:1.7;color:#7a9db5;text-align:center;background:#f6fafd;">
            &copy; {{ date('Y') }} Tejadent Clinic &mdash; All rights reserved.
        </div>

    </div>
</body>
</html>
