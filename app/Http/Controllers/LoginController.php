<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{   
    // Show Login Page
    public function index() {   
        if (Auth::check()) {
            $role = Auth::user()?->role;
            if ($role === 3) {
                return redirect()->route('patient.dashboard');
            }
            return redirect()->route('dashboard');
        }
        // Show captcha only after 3 failed attempts
        $showCaptcha = session()->get('login_failed_attempts', 0) >= 3;
        return view("login", compact('showCaptcha')); 
    }

    // Handle Login Logic
    public function login(Request $request) 
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $failedAttempts = session()->get('login_failed_attempts', 0);
        $showCaptcha = $failedAttempts >= 3;

        // RECAPTCHA CHECK (only after 3 failed attempts)
        if ($showCaptcha) {
            $recaptchaToken = $request->input('g-recaptcha-response');
            if (empty($recaptchaToken)) {
                session()->put('login_failed_attempts', $failedAttempts + 1);
                return back()->with('failed', 'Please complete the captcha below');
            }

            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => env('RECAPTCHA_SECRET_KEY'),
                'response' => $recaptchaToken,
                'remoteip' => $request->ip(),
            ]);

            if (!$response->json()['success']) {
                session()->put('login_failed_attempts', $failedAttempts + 1);
                return back()->with('failed', 'CAPTCHA verification failed.');
            }
        }

        // LOGIN ATTEMPT
        $user = DB::table('users')
            ->where('username', $request->username)
            ->orWhere('email', $request->username)
            ->first();
        
        
        if ($user && Hash::check($request->password, $user->password)) {
            session()->forget('login_failed_attempts');
            
            // --- RELAXED VERIFICATION CHECK ---
            // Allow login even if unverified, but check status
            $isUnverified = ($user->email_verified_at === null && $user->google_id === null);

            if ($isUnverified) {
                // Flash warning for dashboard
                session()->flash('warning', 'Your email is not verified. You must verify it before booking an appointment.');
                // Store email for resend functionality
                session()->put('unverified_email', $user->email);
            }

            Auth::loginUsingId($user->id);
            
            $role = $user->role;

            if (in_array($role, [1, 2], true)) {
                if ($role === 1) {
                    return redirect()->intended('/dashboard');
                }
                return $isUnverified ? redirect('/dashboard') : redirect()->intended('/appointment');
            }

            // Patients should only access booking (or their own dashboard when built)
            if ($role === 3) {
                return redirect()->intended('/patient/dashboard');
            }

            return redirect()->intended('/dashboard');
        }

        session()->put('login_failed_attempts', $failedAttempts + 1);
        return back()->with('failed', 'Invalid username or password.');
    }

    // Handle Logout
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }   
}
