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

        .details {
            margin: 22px 0;
            border: 1px solid #e4eff8;
            background: #f6fafd;
        }

        .row {
            padding: 14px 20px;
            border-bottom: 1px solid #e4eff8;
        }

        .row.last {
            border-bottom: none;
        }

        .label {
            display: block;
            margin-bottom: 5px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #7a9db5;
        }

        .value {
            display: block;
            font-size: 14px;
            line-height: 1.6;
            font-weight: 700;
            color: #1a2e3b;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border: 1px solid #bae6fd;
            background: #f0f9ff;
            color: #0369a1;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.14em;
        }

        .divider {
            height: 1px;
            background: #e4eff8;
            margin: 4px 0 20px;
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
                <p class="hero-copy">There is a new update for your appointment. Please review the latest schedule
                    status below.</p>
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
                        <span class="status-badge">{{ $status }}</span>
                    </div>
                </div>

                <p class="text">If you have questions about this update, please contact the clinic directly.</p>
                <p class="text" style="margin-bottom: 0;">This is an automated message. Please do not reply.</p>

                <div class="divider"></div>

                <div class="details" style="margin-top: 0;">
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
                &copy; {{ date('Y') }} Tejadent Clinic &mdash; All rights reserved.
            </div>
        </div>
    </div>
</body>

</html>
