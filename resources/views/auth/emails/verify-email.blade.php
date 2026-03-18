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
            background: #f6fafd;
            color: #1a2e3b;
            font-family: Arial, "Segoe UI", sans-serif;
        }

        table {
            border-spacing: 0;
        }

        td {
            padding: 0;
        }

        .wrapper {
            width: 100%;
            background: #f6fafd;
            padding: 40px 16px;
        }

        .container {
            width: 100%;
            max-width: 640px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e4eff8;
            border-top: 4px solid #0086da;
        }

        .hero {
            padding: 32px 40px;
            background: #0086da;
            color: #ffffff;
        }

        .brand {
            margin: 0;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: 0.06em;
        }

        .hero-copy {
            margin: 10px 0 0;
            font-size: 14px;
            line-height: 1.75;
            color: rgba(255, 255, 255, 0.85);
        }

        .content {
            padding: 36px 40px 20px;
        }

        .title {
            margin: 0 0 12px;
            font-size: 22px;
            line-height: 1.25;
            font-weight: 800;
            color: #1a2e3b;
            letter-spacing: -0.02em;
        }

        .text {
            margin: 0 0 16px;
            font-size: 14px;
            line-height: 1.8;
            color: #587189;
        }

        .info-box {
            margin: 22px 0;
            padding: 18px 20px;
            border: 1px solid #e4eff8;
            background: #f6fafd;
        }

        .button-wrap {
            padding: 8px 0 28px;
            text-align: center;
        }

        .button {
            display: inline-block;
            padding: 15px 32px;
            background: #0086da;
            color: #ffffff !important;
            text-decoration: none;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .note {
            margin: 0 0 24px;
            font-size: 12px;
            line-height: 1.7;
            color: #7a9db5;
        }

        .link-box {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e4eff8;
            font-size: 11px;
            line-height: 1.7;
            color: #7a9db5;
            word-break: break-all;
        }

        .link-box a {
            color: #0086da;
            text-decoration: none;
        }

        .footer {
            padding: 20px 40px;
            border-top: 1px solid #e4eff8;
            font-size: 11px;
            line-height: 1.7;
            color: #7a9db5;
            text-align: center;
            background: #f6fafd;
        }

        @media only screen and (max-width: 600px) {

            .hero,
            .content,
            .footer {
                padding-left: 24px !important;
                padding-right: 24px !important;
            }

            .title {
                font-size: 20px !important;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container">
            <div class="hero">
                <h1 class="brand">TEJADA CLINIC</h1>
                <p class="hero-copy">Secure your account and finish setting up your access to appointments, records,
                    and clinic updates.</p>
            </div>

            <div class="content">
                <h2 class="title">Confirm your email address</h2>
                <p class="text">Hi {{ $name }}, thanks for registering with Tejadent Clinic. Please verify your email
                    so we can activate your account and keep your patient access secure.</p>

                <div class="info-box">
                    <p class="text" style="margin: 0;">This verification link expires in 5 minutes. Once verified, you
                        can sign in normally and continue booking appointments from your dashboard.</p>
                </div>

                <div class="button-wrap">
                    <a href="{{ route('verification.verify', ['id' => $id, 'token' => $token]) }}"
                        class="button">Verify Email Address</a>
                </div>

                <p class="note">If you did not create an account, no further action is required.</p>

                <div class="link-box">
                    If the button does not work, copy and paste this link into your browser:<br>
                    <a href="{{ route('verification.verify', ['id' => $id, 'token' => $token]) }}">{{ route('verification.verify', ['id' => $id, 'token' => $token]) }}</a>
                </div>
            </div>

            <div class="footer">
                &copy; {{ date('Y') }} Tejadent Clinic &mdash; All rights reserved.
            </div>
        </div>
    </div>
</body>

</html>
