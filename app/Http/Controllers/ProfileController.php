<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Throwable;

class ProfileController extends Controller
{
    public function index()
    {
        $user = DB::table('users')->where('id', Auth::id())->first();
        $isGoogleUser = $user && !empty($user->google_id);
        if ($user && (int) $user->role === 3) {
            $patient = $this->resolvePatientForUser($user);
            return view('patient.profile', compact('user', 'patient', 'isGoogleUser'));
        }

        // Fetch role name for display
        $roleName = DB::table('roles')->where('id', $user->role)->value('role_name');

        return view('profile', compact('user', 'roleName', 'isGoogleUser'));
    }

    public function update(Request $request)
    {
        $userId = Auth::id();

        // Validate strictly against your existing columns
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($userId)],
            'contact'  => ['required', 'string', 'max:20'],
        ]);

        // Update strictly your existing columns
        DB::table('users')->where('id', $userId)->update([
            'username' => $validated['username'],
            'contact'  => $validated['contact'],
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $user = DB::table('users')->where('id', Auth::id())->first();
        $isGoogleUser = $user && !empty($user->google_id);

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

        if (!$user || empty($user->email)) {
            return back()->with('failed', 'We could not find an email address for your account.');
        }

        $key = 'profile-reset:' . Str::lower($user->email) . '|' . $request->ip();
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

    private function resolvePatientForUser($user)
    {
        $patient = null;
        $usesUserId = false;

        try {
            $usesUserId = Schema::hasColumn('patients', 'user_id');
        } catch (Throwable $e) {
            $usesUserId = false;
        }

        if ($usesUserId) {
            $patient = DB::table('patients')->where('user_id', $user->id)->first();
        }

        if (!$patient && !empty($user->email)) {
            $patient = DB::table('patients')->where('email_address', $user->email)->first();

            if ($patient && $usesUserId && empty($patient->user_id)) {
                DB::table('patients')
                    ->where('id', $patient->id)
                    ->update([
                        'user_id' => $user->id,
                        'updated_at' => now(),
                    ]);
                $patient->user_id = $user->id;
            }
        }

        return $patient;
    }
}
