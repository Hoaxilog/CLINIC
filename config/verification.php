<?php

return [
    'link_expires_in_minutes' => (int) env('VERIFICATION_LINK_EXPIRES_IN_MINUTES', 3),
    'otp_expires_in_minutes' => (int) env('OTP_EXPIRES_IN_MINUTES', 3),
    'resend_cooldown_seconds' => (int) env('VERIFICATION_RESEND_COOLDOWN_SECONDS', 60),
    'max_resends' => (int) env('VERIFICATION_MAX_RESENDS', 3),
    'otp_resend_lock_seconds' => (int) env('OTP_RESEND_LOCK_SECONDS', 600),
    'otp_verify_max_attempts' => (int) env('OTP_VERIFY_MAX_ATTEMPTS', 5),
    'otp_verify_block_seconds' => (int) env('OTP_VERIFY_BLOCK_SECONDS', 300),
];
