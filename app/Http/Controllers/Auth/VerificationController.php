<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class VerificationController extends Controller
{

    public function showNotice()
    {
        return view('auth.verify-email-notice');
    }

    public function showExpired()
    {
        return view('auth.verify-email-expired');
    }

    public function verify($id, $token)
    {
        // 1. Find User
        $user = DB::table('users')
            ->where('id', $id)
            ->where('verification_token', $token)
            ->first();

        // 2. Validate
        if (! $user) {
            return redirect()->route('login')->with('failed', 'Invalid verification link or account already verified.');
        }

        if ($user->email_verified_at) {
            return redirect()->route('login')->with('success', 'Your account is already verified. Please login.');
        }

        if ($this->verificationLinkHasExpired($user)) {
            DB::table('users')
                ->where('id', $id)
                ->update([
                    'verification_token' => null,
                    'updated_at' => now(),
                ]);

            return redirect()->route('verification.expired')
                ->with('expired_email', $user->email);
        }

        // 3. Verify
        DB::table('users')
            ->where('id', $id)
            ->update([
                'email_verified_at' => now(),
                'verification_token' => null,
                'updated_at' => now(),
            ]);

        // Flash verified email for success page display.
        return redirect()->route('verification.success')->with('verified_email', $user->email ?? 'your email');
    }

    /**
     * Show the success page.
     */
    public function showSuccess()
    {
        return view('auth.verify-success');
    }

    public function resend(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $resendState = $this->resendState((string) $request->email, (string) $request->ip());
        $remaining = $this->resendCooldownRemaining($resendState);
        if ($remaining > 0) {
            return back()->withErrors([
                'email' => "Please wait {$remaining} second(s) before requesting a new verification email.",
            ])->withInput();
        }

        if ($this->resendCount($resendState) >= $this->maxResends()) {
            return back()->withErrors([
                'email' => 'You have reached the maximum of 3 resend attempts for this verification flow.',
            ])->withInput();
        }

        $user = DB::table('users')->where('email', $request->email)->first();

        if ($user->email_verified_at) {
            return redirect()->route('login')->with('success', 'Your account is already verified.');
        }

        $newToken = Str::random(64);

        DB::table('users')
            ->where('email', $request->email)
            ->update(['verification_token' => $newToken, 'updated_at' => now()]);

        Mail::send('auth.emails.verify-email',
            ['token' => $newToken, 'id' => $user->id, 'name' => 'Patient'],
            function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('Verify Your Email Address - Tejadent');
            }
        );

        $this->storeResendState((string) $request->email, (string) $request->ip(), $this->resendCount($resendState) + 1);

        return back()->with('success', 'A fresh verification link has been sent.');
    }

    private function verificationLinkHasExpired(object $user): bool
    {
        $issuedAt = $user->updated_at ?? $user->created_at ?? null;

        if ($issuedAt === null) {
            return true;
        }

        return Carbon::parse($issuedAt)
            ->addMinutes($this->linkExpiresInMinutes())
            ->isPast();
    }

    private function linkExpiresInMinutes(): int
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

    private function resendState(string $email, string $ip): array
    {
        return cache()->get($this->resendStateKey($email, $ip), [
            'count' => 0,
            'cooldown_until' => null,
        ]);
    }

    private function resendCooldownRemaining(array $state): int
    {
        $cooldownUntil = $state['cooldown_until'] ?? null;

        if (! $cooldownUntil) {
            return 0;
        }

        return max(0, now()->diffInSeconds(Carbon::parse($cooldownUntil), false));
    }

    private function resendCount(array $state): int
    {
        return (int) ($state['count'] ?? 0);
    }

    private function storeResendState(string $email, string $ip, int $count): void
    {
        cache()->put(
            $this->resendStateKey($email, $ip),
            [
                'count' => $count,
                'cooldown_until' => now()->addSeconds($this->resendCooldownSeconds())->toDateTimeString(),
            ],
            now()->addMinutes($this->linkExpiresInMinutes())
        );
    }

    private function resendStateKey(string $email, string $ip): string
    {
        return 'verification-resend-state:'.sha1(Str::lower(trim($email)).'|'.$ip);
    }
}
