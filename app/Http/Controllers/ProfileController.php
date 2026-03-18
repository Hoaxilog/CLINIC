<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function index()
    {
        $user = DB::table('users')->where('id', Auth::id())->first();
        $isGoogleUser = $user && ! empty($user->google_id);
        if ($user && (int) $user->role === 3) {
            $latestRequestIdentity = DB::table('appointments')
                ->where(function ($query) use ($user) {
                    $query->where('requester_user_id', $user->id);

                    if (! empty($user->email)) {
                        $query->orWhere('requester_email', $user->email);
                    }
                })
                ->select('requester_first_name', 'requester_last_name')
                ->orderByDesc('updated_at')
                ->orderByDesc('appointment_date')
                ->first();

            $requesterDisplayName = trim(
                (string) ($latestRequestIdentity->requester_first_name ?? '').' '.
                (string) ($latestRequestIdentity->requester_last_name ?? '')
            );

            if ($requesterDisplayName === '') {
                $requesterDisplayName = 'Patient';
            }

            return view('patient.profile', compact('user', 'isGoogleUser', 'requesterDisplayName'));
        }

        // Fetch role name for display
        $roleName = DB::table('roles')->where('id', $user->role)->value('role_name');

        return view('profile', compact('user', 'roleName', 'isGoogleUser'));
    }

    public function update(Request $request)
    {
        $userId = Auth::id();
        $user = DB::table('users')->where('id', $userId)->first();

        if (! $user) {
            return back()->with('failed', 'We could not find your account details.');
        }

        return back()->with('success', 'Account details are managed by the clinic.');
    }

    public function updatePassword(Request $request)
    {
        $user = DB::table('users')->where('id', Auth::id())->first();
        $isGoogleUser = $user && ! empty($user->google_id);

        $rules = [
            'password' => ['required', 'confirmed', 'min:8'],
        ];

        if ($isGoogleUser) {
            if ($request->filled('current_password')) {
                $rules['current_password'] = ['current_password'];
            }
        } else {
            $rules['current_password'] = ['required', 'current_password'];
        }

        $request->validate($rules);

        DB::table('users')->where('id', Auth::id())->update([
            'password' => Hash::make($request->password),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Password changed successfully.');
    }

    public function sendPasswordResetLink(Request $request)
    {
        $user = DB::table('users')->where('id', Auth::id())->first();

        if (! $user || empty($user->email)) {
            return back()->with('failed', 'We could not find an email address for your account.');
        }

        $key = 'profile-reset:'.Str::lower($user->email).'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = max(1, ceil($seconds / 60));

            return back()->with('failed', "Too many requests. Try again in {$minutes} minute(s).");
        }

        RateLimiter::hit($key, 300);

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $token,
                'created_at' => Carbon::now(),
            ]
        );

        Mail::send('auth.emails.password-reset', ['token' => $token, 'email' => $user->email], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Reset Password Request - Tejadent');
        });

        return back()->with('success', 'We sent a password reset link to your email.');
    }
}
