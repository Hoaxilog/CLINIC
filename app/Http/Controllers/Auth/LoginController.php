<?php

namespace App\Http\Controllers\Auth;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    // Show Login Page
    public function index()
    {
        if (Auth::check()) {
            $role = Auth::user()?->role;
            if ($role === 3) {
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

            if (!$response->json()['success']) {
                session()->put('login_failed_attempts', $failedAttempts + 1);
                RateLimiter::hit($throttleKey, 300);
                return back()->with('failed', 'CAPTCHA verification failed.');
            }
        }

        $user = DB::table('users')->where('email', $email)->first();

        if ($user && !empty($user->password) && Hash::check($request->password, $user->password)) {
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

            Auth::loginUsingId($user->id);
            $request->session()->regenerate();

            return $this->redirectByRole($user->role);
        }

        if ($user && empty($user->password) && !empty($user->google_id)) {
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
                if (!empty($user->google_id)) {
                    if ($user->google_id !== $googleUser->id) {
                        return redirect('/login')->with('failed', 'This email is already linked to a different Google account.');
                    }

                    if ($this->requiresOtpChallenge($user->role)) {
                        return $this->startOtpChallenge($request, $user);
                    }

                    Auth::loginUsingId($user->id);
                    $request->session()->regenerate();
                    return $this->redirectByRole($user->role);
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

                Auth::loginUsingId($user->id);
                $request->session()->regenerate();
                return $this->redirectByRole($user->role);
            }

            $patientRoleId = DB::table('roles')
                ->where('role_name', 'patient')
                ->value('id');

            if (!$patientRoleId) {
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

            Auth::loginUsingId($newUserId);
            $request->session()->regenerate();
            return redirect()->route('patient.dashboard');
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

        if (!$pendingOtp) {
            return redirect()->route('login')->with('failed', 'Your OTP session has expired. Please log in again.');
        }

        return view('auth.login-otp', [
            'email' => $pendingOtp['email'],
            'expiresAt' => $pendingOtp['expires_at'],
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

        if (!$pendingOtp) {
            return redirect()->route('login')->with('failed', 'Your OTP session has expired. Please log in again.');
        }

        $verifyKey = $this->otpVerifyThrottleKey((int) $pendingOtp['user_id'], (string) $request->ip());
        if (RateLimiter::tooManyAttempts($verifyKey, 5)) {
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

        if (!Hash::check($request->otp, $pendingOtp['code_hash'])) {
            RateLimiter::hit($verifyKey, 300);
            return back()->withErrors([
                'otp' => 'Invalid OTP code.',
            ])->withInput($request->only('otp'));
        }

        $user = DB::table('users')->where('id', $pendingOtp['user_id'])->first();

        if (!$user) {
            $request->session()->forget('pending_otp_login');
            return redirect()->route('login')->with('failed', 'We could not complete the login. Please try again.');
        }

        RateLimiter::clear($verifyKey);
        $request->session()->forget('pending_otp_login');
        Auth::loginUsingId($user->id);
        $request->session()->regenerate();
        $this->rememberTrustedOtpDevice($user->id);

        return $this->redirectByRole($user->role);
    }

    public function resendOtp(Request $request)
    {
        $pendingOtp = $request->session()->get('pending_otp_login');

        if (!$pendingOtp) {
            return redirect()->route('login')->with('failed', 'Your OTP session has expired. Please log in again.');
        }

        $resendKey = $this->otpResendThrottleKey((int) $pendingOtp['user_id'], (string) $request->ip());
        if (RateLimiter::tooManyAttempts($resendKey, 3)) {
            $seconds = RateLimiter::availableIn($resendKey);
            $minutes = max(1, (int) ceil($seconds / 60));
            return back()->with('failed', "Too many OTP resends. Try again in {$minutes} minute(s).");
        }

        $user = DB::table('users')->where('id', $pendingOtp['user_id'])->first();

        if (!$user) {
            $request->session()->forget('pending_otp_login');
            return redirect()->route('login')->with('failed', 'We could not complete the login. Please try again.');
        }

        RateLimiter::hit($resendKey, 600);
        $this->issueOtpChallenge($request, $user);

        return back()->with('success', 'A new OTP was sent to your email.');
    }

    private function redirectByRole($role, bool $isUnverified = false)
    {
        if (in_array($role, [1, 2], true)) {
            if ($role === 1) {
                return redirect()->intended('/dashboard');
            }

            return $isUnverified ? redirect('/dashboard') : redirect()->intended('/appointment');
        }

        if ($role === 3) {
            return redirect()->intended('/patient/dashboard');
        }

        return redirect()->intended('/dashboard');
    }

    private function loginThrottleKey(string $email, string $ip): string
    {
        return 'login:' . $email . '|' . $ip;
    }

    private function requiresOtpChallenge($role): bool
    {
        return in_array((int) $role, [1, 2], true);
    }

    private function startOtpChallenge(Request $request, $user)
    {
        if ($this->hasTrustedOtpDevice($request, (int) $user->id)) {
            Auth::loginUsingId($user->id);
            $request->session()->regenerate();

            return $this->redirectByRole($user->role);
        }

        $this->issueOtpChallenge($request, $user);

        return redirect()
            ->route('login.otp')
            ->with('success', 'We sent a one-time code to your email.');
    }

    private function issueOtpChallenge(Request $request, $user): void
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(10);

        $request->session()->put('pending_otp_login', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'code_hash' => Hash::make($code),
            'expires_at' => $expiresAt,
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
        return 'otp-verify:' . $userId . '|' . $ip;
    }

    private function otpResendThrottleKey(int $userId, string $ip): string
    {
        return 'otp-resend:' . $userId . '|' . $ip;
    }

    private function hasTrustedOtpDevice(Request $request, int $userId): bool
    {
        $trustedValue = (string) $request->cookie('trusted_otp_device', '');

        if (!str_contains($trustedValue, '|')) {
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
            $userId . '|' . $token,
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
        return 'trusted-otp:' . $userId . ':' . hash('sha256', $token);
    }

}
