<?php

namespace App\Http\Controllers\Auth;

use App\Support\InputSanitizer;
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
        $input = [
            'first_name' => InputSanitizer::sanitizeTitleCase($request->input('first_name')),
            'last_name' => InputSanitizer::sanitizeTitleCase($request->input('last_name')),
            'email' => InputSanitizer::sanitizeEmail($request->input('email')),
            'mobile_number' => InputSanitizer::sanitizeCountryCodeLocalNumber($request->input('mobile_number')),
            'password' => (string) $request->input('password', ''),
            'password_confirmation' => (string) $request->input('password_confirmation', ''),
            'g-recaptcha-response' => $request->input('g-recaptcha-response'),
        ];

        // 1. Validate standard fields + reCAPTCHA presence
        $validator = Validator::make($input, [
            'first_name' => ['required', 'string', 'max:100', "regex:/^[\\pL\\pM\\s'\\-]+$/u"],
            'last_name' => ['required', 'string', 'max:100', "regex:/^[\\pL\\pM\\s'\\-]+$/u"],
            'email' => 'required|string|email|max:255|unique:users',
            'mobile_number' => ['required', 'regex:/^\\d{10}$/'],
            'password' => 'required|string|min:8|confirmed',
            'g-recaptcha-response' => 'required',
        ], [
            'g-recaptcha-response.required' => 'Please complete the captcha.',
            'mobile_number.regex' => 'Mobile number must be exactly 10 digits after +63.',
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
        $firstName = $input['first_name'];
        $lastName = $input['last_name'];
        $email = $input['email'];
        $mobileNumber = $input['mobile_number'];
        
        $userId = DB::table('users')->insertGetId([
            'username' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'mobile_number' => $mobileNumber,
            'password' => Hash::make($input['password']),
            'role' => (int) $patientRoleId,
            'verification_token' => $token,
            'email_verified_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send Verification Email
        Mail::send('auth.emails.verify-email', ['token' => $token, 'id' => $userId, 'name' => trim($firstName.' '.$lastName)], function($message) use($email) {
            $message->to($email);
            $message->subject('Verify Your Email Address - Tejadent');
        });

        return redirect()->route('verification.notice')->with('email', $email);
    }

}
