<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;

class ForgotPasswordController extends Controller
{
    /**
     * 1. Show the form where user enters their email
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password-smtp');
    }

    /**
     * 2. Process the email submission and send the link
     */
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'g-recaptcha-response' => 'required',
        ], [
            'g-recaptcha-response.required' => 'Please complete the captcha.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // LIMITTER for anti spam

        $email = Str::lower($request->input('email'));
        $key = 'pwd-reset:' . $email . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = max(1,  ceil($seconds / 60));
            return back()->withErrors([
                'email' => "Too many requests. Try again in {$minutes} minute(s)."
            ])->withInput();
        }

        RateLimiter::hit($key, 300);

        $recaptchaToken = $request->input('g-recaptcha-response');

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $recaptchaToken,
            'remoteip' => $request->ip(),
        ]);

        if (!$response->ok() || $response->json('success') !== true) {
            return back()->withErrors([
                'g-recaptcha-response' => 'CAPTCHA verification failed.',
            ])->withInput();
        }

        // Check if user exists using DB Builder
        $user = DB::table('users')->where('email', $request->email)->first();

        if (!$user) {
            return back()->with('failed', 'We can\'t find a user with that email address.');
        }

        // EDGE CASE: If user registered via Google (no password), block them
        if ($user->google_id && is_null($user->password)) {
            return back()->with('failed', 'This account uses Google Login. Please sign in with Google.');
        }

        // Generate a random secure token
        $token = Str::random(64);

        // Save token to 'password_reset_tokens' table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        // Send the email using Mailtrap
        Mail::send('auth.emails.password-reset', ['token' => $token, 'email' => $request->email], function($message) use($request){
            $message->to($request->email);
            $message->subject('Reset Password Request - Tejadent');
        });

        return back()->with('reset_email', $request->email);
    }

    /**
     * 3. Show the "New Password" form (User clicked the email link)
     */
    public function showResetForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * 4. Update the password in the database
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|min:8|confirmed',
            'token' => 'required'
        ]);

        // Verify the token matches the email
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetRecord) {
            return back()->with('failed', 'Invalid token!');
        }

        // Check if token is older than 60 minutes
        if (Carbon::parse($resetRecord->created_at)->addMinutes(60)->isPast()) {
             DB::table('password_reset_tokens')->where('email', $request->email)->delete();
             return back()->with('failed', 'This reset link has expired.');
        }

        // Update the user's password
        DB::table('users')
            ->where('email', $request->email)
            ->update([
                'password' => Hash::make($request->password),
                'updated_at' => now()
            ]);

        // Delete the used token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect('/login')->with('success', 'Your password has been changed! You can now login.');
    }
}