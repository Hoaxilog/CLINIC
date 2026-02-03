<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VerificationController extends Controller
{
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
        if (!$user) {
            return redirect()->route('login')->with('failed', 'Invalid verification link or account already verified.');
        }

        if ($user->email_verified_at) {
            return redirect()->route('login')->with('success', 'Your account is already verified. Please login.');
        }

        // 3. Verify
        DB::table('users')
            ->where('id', $id)
            ->update([
                'email_verified_at' => now(),
                'verification_token' => null,
                'updated_at' => now()
            ]);

        // 4. NO AUTO-LOGIN. Redirect to Success Page instead.
        // We pass the name so we can personalize the success message.
        return redirect()->route('verification.success')->with('first_name', $user->first_name);
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

        $newToken = \Illuminate\Support\Str::random(64);

        DB::table('users')
            ->where('email', $request->email)
            ->update(['verification_token' => $newToken, 'updated_at' => now()]);

        \Illuminate\Support\Facades\Mail::send('auth.emails.verify-email', 
            ['token' => $newToken, 'id' => $user->id, 'name' => $user->name], 
            function($message) use($request) {
                $message->to($request->email);
                $message->subject('Verify Your Email Address - Tejadent');
            }
        );

        return back()->with('success', 'A fresh verification link has been sent.');
    }
}