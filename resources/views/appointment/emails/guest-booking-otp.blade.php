<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Booking OTP</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f4f8fb;
            color: #163047;
            font-family: "Trebuchet MS", "Segoe UI", Arial, sans-serif;
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

        .otp-box {
            margin: 28px 0 22px;
            padding: 20px;
            border-radius: 20px;
            background: linear-gradient(180deg, #f7fbfe 0%, #ecf7fd 100%);
            border: 1px solid #d7e7f3;
            text-align: center;
        }

        .otp-code {
            display: inline-block;
            font-size: 34px;
            font-weight: 800;
            letter-spacing: 0.34em;
            color: #0d7cc2;
            text-indent: 0.34em;
        }

        .footer {
            padding: 0 40px 36px;
            font-size: 12px;
            line-height: 1.7;
            color: #89a0b2;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container">
            <div class="hero">
                <div class="eyebrow">Tejadent Clinic</div>
                <h1 class="brand">TEJADENT</h1>
            </div>

            <div class="content">
                <h2 class="title">Your booking verification code</h2>
                <p class="text">Use this OTP to verify your email for appointment booking:
                    <strong>{{ $email }}</strong>
                </p>

                <div class="otp-box">
                    <span class="otp-code">{{ $otp }}</span>
                </div>

                <p class="text">This code expires at
                    <strong>{{ \Carbon\Carbon::parse($expiresAt)->format('g:i A') }}</strong>.
                </p>
                <p class="text">If you did not request this, you can ignore this email.</p>

                <div class="otp-box" style="text-align: left; margin-top: 20px;">
                    <p class="text" style="margin: 0 0 8px; font-weight: 700; color: #10283b;">Need help contacting
                        the clinic?</p>
                    <p class="text" style="margin: 0 0 6px;">Address: 251 Commonwealth Ave, Diliman, Quezon City</p>
                    <p class="text" style="margin: 0 0 6px;">Phone: +63 912 345 6789</p>
                    <p class="text" style="margin: 0;">Facebook: facebook.com/TejaDentClinic</p>
                </div>
            </div>

            <div class="footer">
                &copy; {{ date('Y') }} Tejadent Clinic. All rights reserved.
            </div>
        </div>
    </div>
</body>

</html>
