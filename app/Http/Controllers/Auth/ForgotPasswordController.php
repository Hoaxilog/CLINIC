<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\SendPasswordResetLinkRequest;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{

    public function showLinkRequestForm(): View
    {
        return view('auth.forgot-password-smtp');
    }

    public function sendResetLinkEmail(SendPasswordResetLinkRequest $request): RedirectResponse
    {

        $email = Str::lower($request->input('email'));
        $state = $this->resetRequestState($email, (string) $request->ip());
        $remaining = $this->resetCooldownRemaining($state);
        if ($remaining > 0) {

            return back()->withErrors([
                'email' => "Please wait {$remaining} second(s) before requesting a new reset email.",
            ])->withInput();
        }

        if ($this->resetRequestCount($state) >= $this->maxResends()) {
            return back()->withErrors([
                'email' => 'You have reached the maximum of 3 resend attempts for this reset flow.',
            ])->withInput();
        }

        $recaptchaToken = $request->input('g-recaptcha-response');

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $recaptchaToken,
            'remoteip' => $request->ip(),
        ]);

        if (! $response->ok() || $response->json('success') !== true) {
            return back()->withErrors([
                'g-recaptcha-response' => 'CAPTCHA verification failed.',
            ])->withInput();
        }

        $user = DB::table('users')->where('email', $request->email)->first();

        if (! $user) {
            return back()->with('failed', 'We can\'t find a user with that email address.');
        }

        // Generate a random secure token
        $token = Str::random(64);

        // Save token to 'password_reset_tokens' table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $token,
                'created_at' => Carbon::now(),
            ],
        );

        Mail::send('auth.emails.password-reset', ['token' => $token, 'email' => $request->email], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Reset Password Request - Tejadent');
        });

        $nextCount = max(0, $this->resetRequestCount($state) + 1);
        $this->storeResetRequestState($email, (string) $request->ip(), $nextCount);

        return back()->with('reset_email', $request->email);
    }

    public function showResetForm(string $token): View|RedirectResponse
    {
        $resetRecord = DB::table('password_reset_tokens')
            ->where('token', $token)
            ->first();

        if (! $resetRecord) {
            return redirect()->route('password.expired');
        }

        if (Carbon::parse($resetRecord->created_at)->addMinutes($this->resetLinkExpiresInMinutes())->isPast()) {
            DB::table('password_reset_tokens')->where('email', $resetRecord->email)->delete();

            return redirect()->route('password.expired');
        }

        return view('auth.reset-password', ['token' => $token]);
    }

    public function reset(ResetPasswordRequest $request): RedirectResponse
    {
        // Verify the token matches the email
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (! $resetRecord) {
            return redirect()->route('password.expired');
        }

        // Check if token is older than the allowed reset window
        if (Carbon::parse($resetRecord->created_at)->addMinutes($this->resetLinkExpiresInMinutes())->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return redirect()->route('password.expired');
        }

        // Update the user's password
        DB::table('users')
            ->where('email', $request->email)
            ->update([
                'password' => Hash::make($request->password),
                'updated_at' => now(),
            ]);

        // Delete the used token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect('/login')->with('success', 'Your password has been changed. Please log in again.');
    }

    private function resetLinkExpiresInMinutes(): int
    {
        return max(1, (int) config('verification.link_expires_in_minutes', 3));
    }

    private function resendCooldownSeconds(): int
    {
        return max(1, (int) config('verification.resend_cooldown_seconds', 60));
    }

    private function maxResends(): int
    {
        return max(0, (int) config('verification.max_resends', 3));
    }

    private function resetRequestState(string $email, string $ip): array
    {
        return cache()->get($this->resetRequestStateKey($email, $ip), [
            'count' => -1,
            'cooldown_until' => null,
        ]);
    }

    private function resetCooldownRemaining(array $state): int
    {
        $cooldownUntil = $state['cooldown_until'] ?? null;

        if (! $cooldownUntil) {
            return 0;
        }

        return max(0, now()->diffInSeconds(Carbon::parse($cooldownUntil), false));
    }

    private function resetRequestCount(array $state): int
    {
        return (int) ($state['count'] ?? -1);
    }

    private function storeResetRequestState(string $email, string $ip, int $count): void
    {
        cache()->put(
            $this->resetRequestStateKey($email, $ip),
            [
                'count' => $count,
                'cooldown_until' => now()->addSeconds($this->resendCooldownSeconds())->toDateTimeString(),
            ],
            now()->addMinutes($this->resetLinkExpiresInMinutes())
        );
    }

    private function resetRequestStateKey(string $email, string $ip): string
    {
        return 'password-reset-request-state:'.sha1($email.'|'.$ip);
    }
}
