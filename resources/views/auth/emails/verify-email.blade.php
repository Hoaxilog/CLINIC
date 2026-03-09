<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
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
            max-width: 440px;
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
            background: #0086DA;
            color: #ffffff !important;
            text-decoration: none;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.01em;
            box-shadow: 0 10px 24px rgba(0, 134, 218, 0.28);
        }
        .note {
            margin: 0 0 24px;
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
                <p class="hero-copy">Secure your account and finish setting up your access to appointments, records, and clinic updates.</p>
            </div>

            <div class="content">
                <h2 class="title">Confirm your email address</h2>
                <p class="text">Hi {{ $name }}, thanks for registering with Tejadent Clinic. Please verify your email so we can activate your account and keep your patient access secure.</p>

                <div class="info-box">
                    <p class="text" style="margin: 0;">Once verified, you can sign in normally and continue booking appointments from your dashboard.</p>
                </div>

                <div class="button-wrap">
                    <a href="{{ route('verification.verify', ['id' => $id, 'token' => $token]) }}" class="button">Verify Email Address</a>
                </div>

                <p class="note">If you did not create an account, no further action is required.</p>

                <div class="link-box">
                    If the button does not work, copy and paste this link into your browser:<br>
                    <a href="{{ route('verification.verify', ['id' => $id, 'token' => $token]) }}">{{ route('verification.verify', ['id' => $id, 'token' => $token]) }}</a>
                </div>
            </div>

            <div class="footer">
                &copy; {{ date('Y') }} Tejadent Clinic. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>
