<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{

    // Show Login Page
    public function index()
    {
        if (Auth::check()) {
            if (Auth::user()?->isPatient()) {
                return redirect()->route('patient.dashboard');
            }

            return redirect()->route('dashboard');
        }

        // Show captcha only after 3 failed attempts
        $showCaptcha = session()->get('login_failed_attempts', 0) >= 3;

        return view('login', compact('showCaptcha'));
    }

    // Handle Login Logic
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = Str::lower(trim((string) $request->email));
        $throttleKey = $this->loginThrottleKey($email, (string) $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $minutes = max(1, (int) ceil($seconds / 60));

            return back()
                ->with('failed', "Too many login attempts. Try again in {$minutes} minute(s).")
                ->withInput($request->only('email'));
        }

        $failedAttempts = session()->get('login_failed_attempts', 0);
        $showCaptcha = $failedAttempts >= 3;

        // RECAPTCHA CHECK (only after 3 failed attempts)
        if ($showCaptcha) {
            $recaptchaToken = $request->input('g-recaptcha-response');
            if (empty($recaptchaToken)) {
                session()->put('login_failed_attempts', $failedAttempts + 1);
                RateLimiter::hit($throttleKey, 300);

                return back()->with('failed', 'Please complete the captcha');
            }

            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => env('RECAPTCHA_SECRET_KEY'),
                'response' => $recaptchaToken,
                'remoteip' => $request->ip(),
            ]);

            if (! $response->json()['success']) {
                session()->put('login_failed_attempts', $failedAttempts + 1);
                RateLimiter::hit($throttleKey, 300);

                return back()->with('failed', 'CAPTCHA verification failed.');
            }
        }

        $user = DB::table('users')->where('email', $email)->first();

        if ($user && ! empty($user->password) && Hash::check($request->password, $user->password)) {
            $isUnverified = ($user->email_verified_at === null && $user->google_id === null);

            if ($isUnverified) {
                RateLimiter::hit($throttleKey, 300);

                return redirect()
                    ->route('verification.notice')
                    ->with('email', $user->email)
                    ->with('failed', 'Please verify your email before logging in.');
            }

            session()->forget('login_failed_attempts');
            RateLimiter::clear($throttleKey);

            if ($this->requiresOtpChallenge($user->role)) {
                return $this->startOtpChallenge($request, $user);
            }

            return $this->completeLogin($request, $user);
        }

        if ($user && empty($user->password) && ! empty($user->google_id)) {
            session()->put('login_failed_attempts', $failedAttempts + 1);
            RateLimiter::hit($throttleKey, 300);

            return back()->with('failed', 'This account uses Google Sign-In. Please continue with Google.');
        }

        session()->put('login_failed_attempts', $failedAttempts + 1);
        RateLimiter::hit($throttleKey, 300);

        return back()->with('failed', 'Invalid email or password.');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = DB::table('users')->where('email', $googleUser->email)->first();

            if ($user) {
                if (! empty($user->google_id)) {
                    if ($user->google_id !== $googleUser->id) {
                        return redirect('/login')->with('failed', 'This email is already linked to a different Google account.');
                    }

                    if ($this->requiresOtpChallenge($user->role)) {
                        return $this->startOtpChallenge($request, $user);
                    }

                    return $this->completeLogin($request, $user);
                }

                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'google_id' => $googleUser->id,
                        'email_verified_at' => now(),
                        'updated_at' => now(),
                    ]);

                if ($this->requiresOtpChallenge($user->role)) {
                    return $this->startOtpChallenge($request, $user);
                }

                return $this->completeLogin($request, $user);
            }

            $patientRoleId = DB::table('roles')
                ->where('role_name', 'patient')
                ->value('id');

            if (! $patientRoleId) {
                return redirect('/login')->with('failed', 'Patient role is not configured. Please contact administrator.');
            }

            $newUserId = DB::table('users')->insertGetId([
                'username' => $googleUser->email,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'role' => (int) $patientRoleId,
                'password' => Hash::make(Str::random(40)),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $newUser = DB::table('users')->where('id', $newUserId)->first();

            return $this->completeLogin($request, $newUser);
        } catch (Exception $th) {
            return redirect('/login')->with('failed', 'Google Login failed. Please try again.');
        }
    }

    // Handle Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showOtpForm(Request $request)
    {
        $pendingOtp = $request->session()->get('pending_otp_login');

        if (! $pendingOtp) {
            return redirect()->route('login')->with('failed', 'Your OTP session has expired. Please log in again.');
        }

        $lastSentAt = ! empty($pendingOtp['last_sent_at'])
            ? Carbon::parse($pendingOtp['last_sent_at'])
            : null;
        $remainingCooldown = 0;

        if ($lastSentAt) {
            $elapsed = $lastSentAt->diffInSeconds(now(), false);
            $remainingCooldown = max(0, $this->otpResendCooldownSeconds() - $elapsed);
        }

        $resendState = $this->otpResendState((string) $pendingOtp['email']);
        $lockRemaining = $this->otpResendLockRemaining($resendState);
        $resendCount = max(
            (int) ($pendingOtp['resend_count'] ?? 0),
            $this->otpResendCount($resendState)
        );

        return view('auth.login-otp', [
            'email' => $pendingOtp['email'],
            'expiresAt' => $pendingOtp['expires_at'],
            'otpResendCount' => $resendCount,
            'otpMaxResends' => $this->otpMaxResends(),
            'resendCooldownRemaining' => $remainingCooldown,
            'resendLockRemaining' => $lockRemaining,
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ], [
            'otp.required' => 'Invalid OTP.',
            'otp.digits' => 'Invalid OTP.',
        ]);

        $pendingOtp = $request->session()->get('pending_otp_login');

        if (! $pendingOtp) {
            return redirect()->route('login')->with('failed', 'Your OTP session has expired. Please log in again.');
        }

        $verifyKey = $this->otpVerifyThrottleKey((int) $pendingOtp['user_id'], (string) $request->ip());
        if (RateLimiter::tooManyAttempts($verifyKey, $this->otpVerifyMaxAttempts())) {
            $seconds = RateLimiter::availableIn($verifyKey);
            $minutes = max(1, (int) ceil($seconds / 60));

            return back()->withErrors([
                'otp' => "Too many OTP attempts. Try again in {$minutes} minute(s).",
            ])->withInput($request->only('otp'));
        }

        if (now()->greaterThan($pendingOtp['expires_at'])) {
            $request->session()->forget('pending_otp_login');

            return redirect()->route('login')->with('failed', 'Your OTP has expired. Please log in again.');
        }

        if (! Hash::check($request->otp, $pendingOtp['code_hash'])) {
            RateLimiter::hit($verifyKey, $this->otpVerifyBlockSeconds());

            return back()->withErrors([
                'otp' => 'Invalid OTP code.',
            ])->withInput($request->only('otp'));
        }

        $user = DB::table('users')->where('id', $pendingOtp['user_id'])->first();

        if (! $user) {
            $request->session()->forget('pending_otp_login');

            return redirect()->route('login')->with('failed', 'We could not complete the login. Please try again.');
        }

        RateLimiter::clear($verifyKey);
        $request->session()->forget('pending_otp_login');
        $this->completeLogin($request, $user, false);
        $this->rememberTrustedOtpDevice($user->id);

        return $this->redirectByRole($user->role);
    }

    public function resendOtp(Request $request)
    {
        $pendingOtp = $request->session()->get('pending_otp_login');

        if (! $pendingOtp) {
            return redirect()->route('login')->with('failed', 'Your OTP session has expired. Please log in again.');
        }

        $resendState = $this->otpResendState((string) $pendingOtp['email']);
        $lockRemaining = $this->otpResendLockRemaining($resendState);
        if ($lockRemaining > 0) {
            $minutes = max(1, (int) ceil($lockRemaining / 60));

            return back()->with('failed', "OTP resend limit reached. Try again in {$minutes} minute(s).");
        }

        $resendCount = max(
            (int) ($pendingOtp['resend_count'] ?? 0),
            $this->otpResendCount($resendState)
        );
        if ($resendCount >= $this->otpMaxResends()) {
            return back()->with('failed', 'OTP resend limit reached. Please wait for the lock window to expire.');
        }

        if (! empty($pendingOtp['last_sent_at'])) {
            $lastSentAt = Carbon::parse($pendingOtp['last_sent_at']);
            $elapsed = $lastSentAt->diffInSeconds(now(), false);
            if ($elapsed < $this->otpResendCooldownSeconds()) {
                $remaining = $this->otpResendCooldownSeconds() - $elapsed;

                return back()->with('failed', "Please wait {$remaining} second(s) before resending OTP.");
            }
        }

        $resendKey = $this->otpResendThrottleKey((int) $pendingOtp['user_id'], (string) $request->ip());
        if (RateLimiter::tooManyAttempts($resendKey, 3)) {
            $seconds = RateLimiter::availableIn($resendKey);
            $minutes = max(1, (int) ceil($seconds / 60));

            return back()->with('failed', "Too many OTP resends. Try again in {$minutes} minute(s).");
        }

        $user = DB::table('users')->where('id', $pendingOtp['user_id'])->first();

        if (! $user) {
            $request->session()->forget('pending_otp_login');

            return redirect()->route('login')->with('failed', 'We could not complete the login. Please try again.');
        }

        RateLimiter::hit($resendKey, $this->otpResendLockSeconds());
        $newResendCount = $this->recordOtpResend((string) $pendingOtp['email']);
        $this->issueOtpChallenge($request, $user, $pendingOtp, true, $newResendCount);

        return back()->with('success', 'A new OTP was sent to your email.');
    }

    private function redirectByRole($role, bool $isUnverified = false)
    {
        $user = Auth::user();

        if (in_array((int) $role, [User::ROLE_ADMIN, User::ROLE_DENTIST], true)) {
            return redirect()->intended('/dashboard');
        }

        if ((int) $role === User::ROLE_STAFF) {
            return $isUnverified ? redirect('/dashboard') : redirect()->intended('/appointment');
        } elseif ((int) $role === User::ROLE_PATIENT) {
            if ($user && $user->requiresAccountSetupCompletion()) {
                return redirect()->route('patient.complete-profile.show');
            }

            return redirect()->intended('/patient/dashboard');
        }

        return redirect()->intended('/dashboard');
    }

    private function loginThrottleKey(string $email, string $ip): string
    {
        return 'login:'.$email.'|'.$ip;
    }

    private function requiresOtpChallenge($role): bool
    {
        return in_array((int) $role, [
            User::ROLE_ADMIN,
            User::ROLE_DENTIST,
            User::ROLE_STAFF,
        ], true);
    }

    private function startOtpChallenge(Request $request, $user)
    {
        if ($this->hasTrustedOtpDevice($request, (int) $user->id)) {
            return $this->completeLogin($request, $user);
        }

        $lockRemaining = $this->otpResendLockRemaining($this->otpResendState((string) $user->email));
        if ($lockRemaining > 0) {
            $minutes = max(1, (int) ceil($lockRemaining / 60));

            return redirect()
                ->route('login')
                ->with('failed', "OTP resend limit reached. Try again in {$minutes} minute(s).")
                ->withInput($request->only('email'));
        }

        $this->issueOtpChallenge($request, $user);

        return redirect()
            ->route('login.otp')
            ->with('success', 'We sent a one-time code to your email.');
    }

    private function issueOtpChallenge(Request $request, $user, ?array $previousOtpData = null, bool $isResend = false, ?int $resendCountOverride = null): void
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes($this->otpExpiresInMinutes());
        $newResendCount = $resendCountOverride ?? ($isResend
            ? ((int) ($previousOtpData['resend_count'] ?? 0)) + 1
            : 0);
        $sentAt = now();

        $request->session()->put('pending_otp_login', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'code_hash' => Hash::make($code),
            'expires_at' => $expiresAt,
            'resend_count' => $newResendCount,
            'last_sent_at' => $sentAt->toDateTimeString(),
        ]);

        Mail::send('auth.emails.login-otp', [
            'otp' => $code,
            'email' => $user->email,
            'expiresAt' => $expiresAt,
        ], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Your Tejadent Login OTP');
        });
    }

    private function otpVerifyThrottleKey(int $userId, string $ip): string
    {
        return 'otp-verify:'.$userId.'|'.$ip;
    }

    private function otpResendThrottleKey(int $userId, string $ip): string
    {
        return 'otp-resend:'.$userId.'|'.$ip;
    }

    private function otpExpiresInMinutes(): int
    {
        return max(1, (int) config('verification.otp_expires_in_minutes', 3));
    }

    private function otpResendCooldownSeconds(): int
    {
        return max(1, (int) config('verification.resend_cooldown_seconds', 60));
    }

    private function otpMaxResends(): int
    {
        return max(0, (int) config('verification.max_resends', 3));
    }

    private function otpResendLockSeconds(): int
    {
        return max(1, (int) config('verification.otp_resend_lock_seconds', 600));
    }

    private function otpVerifyMaxAttempts(): int
    {
        return max(1, (int) config('verification.otp_verify_max_attempts', 5));
    }

    private function otpVerifyBlockSeconds(): int
    {
        return max(1, (int) config('verification.otp_verify_block_seconds', 300));
    }

    private function otpResendState(string $email): array
    {
        return cache()->get($this->otpResendStateKey($email), [
            'count' => 0,
            'locked_until' => null,
        ]);
    }

    private function otpResendCount(array $state): int
    {
        return (int) ($state['count'] ?? 0);
    }

    private function otpResendLockRemaining(array $state): int
    {
        $lockedUntil = $state['locked_until'] ?? null;

        if (! $lockedUntil) {
            return 0;
        }

        return max(0, now()->diffInSeconds(Carbon::parse($lockedUntil), false));
    }

    private function recordOtpResend(string $email): int
    {
        $state = $this->otpResendState($email);
        $count = min($this->otpMaxResends(), $this->otpResendCount($state) + 1);
        $lockedUntil = $count >= $this->otpMaxResends()
            ? now()->addSeconds($this->otpResendLockSeconds())->toDateTimeString()
            : null;

        cache()->put(
            $this->otpResendStateKey($email),
            [
                'count' => $count,
                'locked_until' => $lockedUntil,
            ],
            now()->addSeconds($this->otpResendLockSeconds())
        );

        return $count;
    }

    private function otpResendStateKey(string $email): string
    {
        return 'login-otp-resend-state:'.sha1(Str::lower(trim($email)));
    }

    private function hasTrustedOtpDevice(Request $request, int $userId): bool
    {
        $trustedValue = (string) $request->cookie('trusted_otp_device', '');

        if (! str_contains($trustedValue, '|')) {
            return false;
        }

        [$trustedUserId, $token] = explode('|', $trustedValue, 2);

        if ((int) $trustedUserId !== $userId || $token === '') {
            return false;
        }

        return Cache::has($this->trustedOtpCacheKey($userId, $token));
    }

    private function rememberTrustedOtpDevice(int $userId): void
    {
        $token = Str::random(64);
        $minutes = 60 * 24;

        Cache::put($this->trustedOtpCacheKey($userId, $token), true, now()->addMinutes($minutes));
        Cookie::queue(Cookie::make(
            'trusted_otp_device',
            $userId.'|'.$token,
            $minutes,
            null,
            null,
            request()->isSecure(),
            true,
            false,
            'lax'
        ));
    }

    private function trustedOtpCacheKey(int $userId, string $token): string
    {
        return 'trusted-otp:'.$userId.':'.hash('sha256', $token);
    }

    private function completeLogin(Request $request, object $user, bool $redirect = true)
    {
        Auth::loginUsingId($user->id);
        $request->session()->regenerate();
        $this->logSuccessfulLogin($request, $user);

        if (! $redirect) {
            return null;
        }

        return $this->redirectByRole($user->role);
    }

    private function logSuccessfulLogin(Request $request, object $user): void
    {
        if (! Schema::hasTable('activity_log')) {
            return;
        }

        $loggedAt = now();
        $userAgent = Str::limit(trim((string) $request->userAgent()), 500, '');

        $alreadyLoggedToday = DB::table('activity_log')
            ->where('event', 'user_logged_in')
            ->where('subject_type', User::class)
            ->where('subject_id', $user->id)
            ->whereDate('created_at', $loggedAt->toDateString())
            ->exists();

        if ($alreadyLoggedToday) {
            return;
        }

        DB::table('activity_log')->insert([
            'log_name' => 'default',
            'description' => 'Logged In',
            'subject_id' => $user->id,
            'subject_type' => User::class,
            'causer_id' => $user->id,
            'causer_type' => User::class,
            'properties' => json_encode([
                'attributes' => [
                    'ip_address' => (string) $request->ip(),
                    'login_at' => $loggedAt->toDateTimeString(),
                    'user_agent' => $userAgent,
                    'browser' => $this->detectBrowser($userAgent),
                    'platform' => $this->detectPlatform($userAgent),
                    'device' => $this->detectDevice($userAgent),
                ],
            ]),
            'batch_uuid' => null,
            'event' => 'user_logged_in',
            'created_at' => $loggedAt,
            'updated_at' => $loggedAt,
        ]);
    }

    private function detectBrowser(string $userAgent): string
    {
        $browserSignatures = [
            'Edge' => ['Edg/', 'Edge/'],
            'Opera' => ['OPR/', 'Opera/'],
            'Chrome' => ['Chrome/'],
            'Firefox' => ['Firefox/'],
            'Safari' => ['Safari/'],
        ];

        foreach ($browserSignatures as $label => $needles) {
            foreach ($needles as $needle) {
                if ($needle === 'Safari/' && str_contains($userAgent, 'Chrome/')) {
                    continue;
                }

                if (str_contains($userAgent, $needle)) {
                    return $label;
                }
            }
        }

        return 'Unknown Browser';
    }

    private function detectPlatform(string $userAgent): string
    {
        $platformSignatures = [
            'Windows' => ['Windows NT'],
            'Android' => ['Android'],
            'iPhone' => ['iPhone'],
            'iPad' => ['iPad'],
            'macOS' => ['Macintosh', 'Mac OS X'],
            'Linux' => ['Linux'],
        ];

        foreach ($platformSignatures as $label => $needles) {
            foreach ($needles as $needle) {
                if (str_contains($userAgent, $needle)) {
                    return $label;
                }
            }
        }

        return 'Unknown Platform';
    }

    private function detectDevice(string $userAgent): string
    {
        $normalized = strtolower($userAgent);

        if ($normalized === '') {
            return 'Unknown Device';
        }

        if (str_contains($normalized, 'ipad')) {
            return 'Tablet';
        }

        if (str_contains($normalized, 'mobile') || str_contains($normalized, 'iphone') || str_contains($normalized, 'android')) {
            return 'Mobile';
        }

        if (str_contains($normalized, 'tablet')) {
            return 'Tablet';
        }

        return 'Desktop';
    }
}
