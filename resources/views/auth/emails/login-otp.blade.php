<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Login OTP</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f6fafd;
            color: #1a2e3b;
            font-family: Arial, "Segoe UI", sans-serif;
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

        .otp-box {
            margin: 24px 0;
            padding: 28px 20px;
            border: 1px solid #e4eff8;
            background: #f6fafd;
            text-align: center;
        }

        .otp-label {
            display: block;
            margin-bottom: 10px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: #7a9db5;
        }

        .otp-code {
            display: inline-block;
            font-size: 36px;
            font-weight: 800;
            letter-spacing: 0.3em;
            color: #0086da;
            text-indent: 0.3em;
        }

        .note {
            margin: 0 0 16px;
            font-size: 12px;
            line-height: 1.7;
            color: #7a9db5;
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

            .otp-code {
                font-size: 28px !important;
                letter-spacing: 0.2em !important;
                text-indent: 0.2em !important;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container">
            <div class="hero">
                <h1 class="brand">TEJADA CLINIC</h1>
                <p class="hero-copy">Use this one-time code to complete your sign-in securely. Never share this code
                    with anyone.</p>
            </div>

            <div class="content">
                <h2 class="title">Your one-time login code</h2>
                <p class="text">Use the code below to finish signing in to your Tejadent account for
                    <strong style="color: #1a2e3b;">{{ $email }}</strong>.
                </p>

                <div class="otp-box">
                    <span class="otp-label">One-Time Code</span>
                    <span class="otp-code">{{ $otp }}</span>
                </div>

                <p class="text">This code expires in 5 minutes, at
                    <strong style="color: #1a2e3b;">{{ \Carbon\Carbon::parse($expiresAt)->format('g:i A') }}</strong>.
                </p>
                <p class="note">If you did not attempt to sign in, you can ignore this email and your account will
                    remain secure.</p>
            </div>

            <div class="footer">
                &copy; {{ date('Y') }} Tejadent Clinic &mdash; All rights reserved.
            </div>
        </div>
    </div>
</body>

</html>
