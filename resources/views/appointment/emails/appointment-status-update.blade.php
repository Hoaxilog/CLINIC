<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Status Update</title>
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

        .hero-copy {
            margin: 12px 0 0;
            max-width: 450px;
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

        .details {
            margin: 26px 0;
            padding: 12px 20px;
            border: 1px solid #d7e7f3;
            border-radius: 20px;
            background: linear-gradient(180deg, #f7fbfe 0%, #eef8fd 100%);
        }

        .row {
            padding: 14px 0;
            border-bottom: 1px solid #deedf7;
        }

        .row.last {
            border-bottom: none;
        }

        .label {
            display: block;
            margin-bottom: 6px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #7e97aa;
        }

        .value {
            display: block;
            font-size: 16px;
            line-height: 1.6;
            font-weight: 700;
            color: #10283b;
        }

        .status-pill {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 999px;
            background: #e8f4fd;
            color: #0d7cc2;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
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
                <p class="hero-copy">There is a new update for your appointment. Please review the latest schedule status
                    below.</p>
            </div>

            <div class="content">
                <h2 class="title">Appointment status updated</h2>
                <p class="text">Hi {{ $name }}, your appointment details have been updated by the clinic.</p>

                <div class="details">
                    <div class="row">
                        <span class="label">Service</span>
                        <span class="value">{{ $service_name }}</span>
                    </div>
                    <div class="row">
                        <span class="label">Date and Time</span>
                        <span class="value">{{ $appointment_date }}</span>
                    </div>
                    <div class="row last">
                        <span class="label">Status</span>
                        <span class="status-pill">{{ $status }}</span>
                    </div>
                </div>

                <p class="text">If you have questions about this update, please contact the clinic directly.</p>
                <p class="text" style="margin-bottom: 0;">This is an automated message. Please do not reply.</p>

                <div class="details" style="margin-top: 20px;">
                    <div class="row">
                        <span class="label">Clinic Address</span>
                        <span class="value">251 Commonwealth Ave, Diliman, Quezon City</span>
                    </div>
                    <div class="row">
                        <span class="label">Phone</span>
                        <span class="value">+63 912 345 6789</span>
                    </div>
                    <div class="row last">
                        <span class="label">Facebook</span>
                        <span class="value">facebook.com/TejaDentClinic</span>
                    </div>
                </div>
            </div>

            <div class="footer">
                &copy; {{ date('Y') }} Tejadent Clinic. All rights reserved.
            </div>
        </div>
    </div>
</body>

</html>
