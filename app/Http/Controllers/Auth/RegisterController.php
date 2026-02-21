<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }


    public function register(Request $request)
    {
        // 1. Validate standard fields + reCAPTCHA presence
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'g-recaptcha-response' => 'required',
        ], [
            'g-recaptcha-response.required' => 'Please complete the captcha below.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // 2. Verify reCAPTCHA with Google
        $recaptchaToken = $request->input('g-recaptcha-response');

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $recaptchaToken,
            'remoteip' => $request->ip(),
        ]);

        if (!$response->json('success')) { return back()->withErrors(['g-recaptcha-response' => 'CAPTCHA verification failed.',])->withInput();
        }

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // 3. Proceed with account creation ONLY if validation above passed
        $token = \Illuminate\Support\Str::random(64);
        
        $userId = DB::table('users')->insertGetId([
            'username' => $request->email,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 3, // Role 3 = Patient
            'verification_token' => $token,
            'email_verified_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send Verification Email
        Mail::send('auth.emails.verify-email', ['token' => $token, 'id' => $userId, 'name' => 'Patient'], function($message) use($request) {
            $message->to($request->email);
            $message->subject('Verify Your Email Address - Tejadent');
        });

        return redirect()->route('verification.notice')->with('email', $request->email);
    }

}
