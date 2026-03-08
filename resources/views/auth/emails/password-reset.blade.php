<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f4f8fb;
            color: #163047;
            font-family: "Trebuchet MS", "Segoe UI", Arial, sans-serif;
        }
        table {
            border-spacing: 0;
        }
        td {
            padding: 0;
        }
        .wrapper {
            width: 100%;
            background: linear-gradient(180deg, #eaf4fb 0%, #f9fcfe 100%);
            padding: 32px 16px;
        }
        .container {
            width: 100%;
            max-width: 640px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #d7e7f3;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 18px 48px rgba(15, 57, 92, 0.12);
        }
        .hero {
            padding: 36px 40px 28px;
            background: linear-gradient(135deg, #0d7cc2 0%, #0f9ad6 100%);
            color: #ffffff;
        }
        .eyebrow {
            display: inline-block;
            margin-bottom: 14px;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.16);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }
        .brand {
            margin: 0;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: 0.08em;
        }
        .hero-copy {
            margin: 12px 0 0;
            max-width: 460px;
            font-size: 15px;
            line-height: 1.7;
            color: #dff4ff;
        }
        .content {
            padding: 36px 40px 18px;
        }
        .title {
            margin: 0 0 14px;
            font-size: 28px;
            line-height: 1.25;
            color: #10283b;
        }
        .text {
            margin: 0 0 16px;
            font-size: 15px;
            line-height: 1.8;
            color: #486175;
        }
        .info-box {
            margin: 26px 0;
            padding: 18px 20px;
            border: 1px solid #d7e7f3;
            border-radius: 18px;
            background: #f7fbfe;
        }
        .button-wrap {
            padding: 8px 0 26px;
            text-align: center;
        }
        .button {
            display: inline-block;
            padding: 15px 30px;
            border-radius: 999px;
            background: #f59e0b;
            color: #ffffff !important;
            text-decoration: none;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.01em;
            box-shadow: 0 10px 24px rgba(245, 158, 11, 0.28);
        }
        .note {
            margin: 0 0 16px;
            font-size: 13px;
            line-height: 1.7;
            color: #6d8598;
        }
        .link-box {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e3eef6;
            font-size: 12px;
            line-height: 1.7;
            color: #6d8598;
            word-break: break-all;
        }
        .link-box a {
            color: #0d7cc2;
            text-decoration: none;
        }
        .footer {
            padding: 0 40px 36px;
            font-size: 12px;
            line-height: 1.7;
            color: #89a0b2;
            text-align: center;
        }
        @media only screen and (max-width: 600px) {
            .hero,
            .content,
            .footer {
                padding-left: 24px !important;
                padding-right: 24px !important;
            }
            .title {
                font-size: 24px !important;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="hero">
                <div class="eyebrow">Tejadent Clinic</div>
                <h1 class="brand">TEJADENT</h1>
                <p class="hero-copy">A password reset was requested for your account. Use the secure link below if you want to continue.</p>
            </div>

            <div class="content">
                <h2 class="title">Reset your password</h2>
                <p class="text">We received a request to reset the password for the Tejadent account linked to this email address.</p>

                <div class="info-box">
                    <p class="text" style="margin: 0;">For your security, this reset link expires in 60 minutes. If you did not request this change, you can ignore this email.</p>
                </div>

                <div class="button-wrap">
                    <a href="{{ route('password.reset', $token) }}?email={{ $email }}" class="button">Reset Password</a>
                </div>

                <p class="note">If you were not expecting this email, no action is needed and your current password will remain unchanged.</p>

                <div class="link-box">
                    If the button does not work, copy and paste this link into your browser:<br>
                    <a href="{{ route('password.reset', $token) }}?email={{ $email }}">{{ route('password.reset', $token) }}?email={{ $email }}</a>
                </div>
            </div>

            <div class="footer">
                &copy; {{ date('Y') }} Tejadent Clinic. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>
