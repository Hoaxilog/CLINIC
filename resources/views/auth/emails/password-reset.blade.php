<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        /* Base styles for email clients that strip classes */
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 0; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f3f4f6; padding-bottom: 40px; }
        .content { max-width: 600px; background-color: #ffffff; margin: 0 auto; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background-color: #0086DA; padding: 30px; text-align: center; }
        .body { padding: 40px 30px; color: #374151; line-height: 1.6; }
        .button-container { text-align: center; margin: 30px 0; }
        .btn { background-color: #0086DA; color: #ffffff; padding: 14px 28px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px; display: inline-block; }
        .footer { padding: 20px; text-align: center; color: #9ca3af; font-size: 12px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <br>
        <div class="content">
            <!-- Header with Logo/Title -->
            <div class="header">
                <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 800; letter-spacing: 1px;">
                    TEJA<span style="color: #e5e7eb;">DENT</span>
                </h1>
            </div>

            <!-- Email Body -->
            <div class="body">
                <h2 style="color: #111827; font-size: 20px; margin-top: 0;">Password Reset Request</h2>
                <p>Hello,</p>
                <p>We received a request to reset the password for your account associated with this email address.</p>
                
                <div class="button-container">
                    <a href="{{ route('password.reset', $token) }}?email={{ $email }}" class="btn">
                        Reset Password
                    </a>
                </div>

                <p style="font-size: 14px; color: #6b7280;">This password reset link will expire in 60 minutes.</p>
                <p style="font-size: 14px; color: #6b7280;">If you did not request a password reset, please ignore this email.</p>
                
                <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">
                
                <p style="font-size: 12px; color: #9ca3af;">
                    If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:
                    <br>
                    <a href="{{ route('password.reset', $token) }}?email={{ $email }}" style="color: #0086DA; word-break: break-all;">
                        {{ route('password.reset', $token) }}?email={{ $email }}
                    </a>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} Tejadent Clinic. All rights reserved.</p>
            {{-- <p>Clinic Address, City, Country</p> --}}
        </div>
    </div>
</body>
</html>