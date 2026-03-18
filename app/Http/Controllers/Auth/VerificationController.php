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
    private const VERIFICATION_LINK_EXPIRES_IN_MINUTES = 5;

    public function showNotice()
    {
        return view('auth.verify-email-notice');
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

            return redirect()->route('verification.notice')
                ->with('email', $user->email)
                ->with('failed', 'Your verification link has expired. Please request a new one.');
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

        return back()->with('success', 'A fresh verification link has been sent.');
    }

    private function verificationLinkHasExpired(object $user): bool
    {
        $issuedAt = $user->updated_at ?? $user->created_at ?? null;

        if ($issuedAt === null) {
            return true;
        }

        return Carbon::parse($issuedAt)
            ->addMinutes(self::VERIFICATION_LINK_EXPIRES_IN_MINUTES)
            ->isPast();
    }
}
