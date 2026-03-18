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
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:users',
            'mobile_number' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'g-recaptcha-response' => 'required',
        ], [
            'g-recaptcha-response.required' => 'Please complete the captcha.',
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

        $patientRoleId = DB::table('roles')
            ->where('role_name', 'patient')
            ->value('id');

        if (!$patientRoleId) {
            return back()->withErrors([
                'email' => 'Patient role is not configured. Please contact administrator.',
            ])->withInput();
        }

        // 3. Proceed with account creation ONLY if validation above passed
        $token = \Illuminate\Support\Str::random(64);
        $firstName = trim((string) $request->input('first_name'));
        $lastName = trim((string) $request->input('last_name'));
        $mobileNumber = trim((string) $request->input('mobile_number'));
        
        $userId = DB::table('users')->insertGetId([
            'username' => $request->email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $request->email,
            'mobile_number' => $mobileNumber,
            'password' => Hash::make($request->password),
            'role' => (int) $patientRoleId,
            'verification_token' => $token,
            'email_verified_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send Verification Email
        Mail::send('auth.emails.verify-email', ['token' => $token, 'id' => $userId, 'name' => trim($firstName.' '.$lastName)], function($message) use($request) {
            $message->to($request->email);
            $message->subject('Verify Your Email Address - Tejadent');
        });

        return redirect()->route('verification.notice')->with('email', $request->email);
    }

}
