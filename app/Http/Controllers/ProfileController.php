<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\InputSanitizer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function index()
    {
        $user = DB::table('users')->where('id', Auth::id())->first();
        $isGoogleUser = $user && ! empty($user->google_id);
        $accountDisplayName = trim((string) ($user->first_name ?? '').' '.(string) ($user->last_name ?? ''));
        $accountMobileNumber = trim((string) ($user->mobile_number ?? ''));
        $hasMiddleNameColumn = Schema::hasColumn('users', 'middle_name');

        if ($user && (int) $user->role === User::ROLE_PATIENT) {
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

            $requesterDisplayName = $accountDisplayName !== '' ? $accountDisplayName : trim(
                (string) ($latestRequestIdentity->requester_first_name ?? '').' '.
                (string) ($latestRequestIdentity->requester_last_name ?? '')
            );

            if ($requesterDisplayName === '') {
                $requesterDisplayName = 'Patient';
            }

            return view('patient.profile', compact('user', 'isGoogleUser', 'requesterDisplayName', 'accountMobileNumber', 'hasMiddleNameColumn'));
        }

        // Fetch role name for display
        $roleName = User::roleLabelFromId($user?->role !== null ? (int) $user->role : null);

        return view('profile', compact('user', 'roleName', 'isGoogleUser', 'accountDisplayName', 'accountMobileNumber'));
    }

    public function update(Request $request)
    {
        $userId = Auth::id();
        $user = DB::table('users')->where('id', $userId)->first();

        if (! $user) {
            return back()->with('error', 'We could not find your account details.');
        }

        if ((int) $user->role === User::ROLE_PATIENT) {
            $hasMiddleNameColumn = Schema::hasColumn('users', 'middle_name');

            $sanitized = [
                'first_name' => InputSanitizer::sanitizeTitleCase($request->input('first_name')),
                'last_name' => InputSanitizer::sanitizeTitleCase($request->input('last_name')),
                'mobile_number' => InputSanitizer::sanitizeCountryCodeLocalNumber($request->input('mobile_number')),
            ];

            if ($hasMiddleNameColumn) {
                $sanitized['middle_name'] = InputSanitizer::sanitizeTitleCase($request->input('middle_name'));
            }

            $rules = [
                'first_name' => ['required', 'string', 'min:2', 'max:100', "regex:/^[\\pL\\pM\\s'\\-]+$/u"],
                'last_name' => ['required', 'string', 'min:2', 'max:100', "regex:/^[\\pL\\pM\\s'\\-]+$/u"],
                'mobile_number' => ['required', 'digits:10'],
            ];

            if ($hasMiddleNameColumn) {
                $rules['middle_name'] = ['nullable', 'string', 'max:100', "regex:/^[\\pL\\pM\\s'\\-]+$/u"];
            }

            $validated = validator($sanitized, $rules, [
                'mobile_number.digits' => 'Contact number must be exactly 10 digits after +63.',
            ])->validate();

            $updatePayload = [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'mobile_number' => $validated['mobile_number'],
                'updated_at' => now(),
            ];

            if ($hasMiddleNameColumn) {
                $updatePayload['middle_name'] = ($validated['middle_name'] ?? '') !== '' ? $validated['middle_name'] : null;
            }

            DB::table('users')->where('id', $userId)->update($updatePayload);

            return back()->with('success', 'Your account details were updated successfully.');
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
            return back()->with('error', 'We could not find an email address for your account.');
        }

        $key = 'profile-reset:'.Str::lower($user->email).'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = max(1, ceil($seconds / 60));

            return back()->with('error', "Too many requests. Try again in {$minutes} minute(s).");
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
