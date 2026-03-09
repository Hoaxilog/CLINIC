<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verified - Tejadent</title>
</head>
<body style="margin:0;padding:32px 16px;background:linear-gradient(180deg,#eaf4fb 0%,#f9fcfe 100%);font-family:'Trebuchet MS','Segoe UI',Arial,sans-serif;color:#163047;">
    <div style="max-width:640px;margin:0 auto;background:#ffffff;border:1px solid #d7e7f3;border-radius:24px;overflow:hidden;box-shadow:0 18px 48px rgba(15,57,92,0.12);">
        <div style="padding:36px 40px 28px;background:linear-gradient(135deg,#0d7cc2 0%,#0f9ad6 100%);color:#ffffff;text-align:left;">
            <div style="display:inline-block;margin-bottom:14px;padding:6px 12px;border-radius:999px;background:rgba(255,255,255,0.16);font-size:11px;font-weight:700;letter-spacing:0.16em;text-transform:uppercase;">Tejadent Clinic</div>
            <h1 style="margin:0;font-size:28px;font-weight:800;letter-spacing:0.08em;">TEJADENT</h1>
            <p style="margin:12px 0 0;max-width:460px;font-size:15px;line-height:1.7;color:#dff4ff;">Your account is ready. You can now sign in and manage appointments with the clinic.</p>
        </div>

        <div style="padding:36px 40px 40px;text-align:center;">
            <div style="width:92px;height:92px;margin:0 auto 24px;border-radius:999px;background:#e8f7ef;border:1px solid #bde6cd;display:flex;align-items:center;justify-content:center;">
                <svg style="width:42px;height:42px;color:#15965c;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h2 style="margin:0 0 14px;font-size:28px;line-height:1.25;color:#10283b;">Email verified</h2>
            <p style="margin:0 auto 24px;max-width:420px;font-size:15px;line-height:1.8;color:#486175;">
                Welcome, <strong style="color:#10283b;">{{ session('verified_email') ?? 'your email' }}</strong>. Your email address has been confirmed successfully.
            </p>
            <p style="margin:0 auto 30px;max-width:440px;font-size:14px;line-height:1.7;color:#6d8598;">You can now sign in and continue to your dashboard to book appointments and view your records.</p>

            <a href="{{ route('login') }}" style="display:inline-block;padding:15px 30px;border-radius:999px;background:#0086DA;color:#ffffff;text-decoration:none;font-size:15px;font-weight:700;letter-spacing:0.01em;box-shadow:0 10px 24px rgba(0,134,218,0.28);">Login to Continue</a>
        </div>
    </div>
</body>
</html>
