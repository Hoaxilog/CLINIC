<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{   
    // Show Login Page
    public function index() {   
        return view("login"); // Recommended: Keep inside 'auth' folder
    }

    // Handle Login Logic
    public function login(Request $request) {
        
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = DB::table('users')
                    ->where('username', $request->username)
                    ->first();

        if($user && Hash::check($request->password, $user->password)) {
            Auth::loginUsingId($user->id);
            $request->session()->regenerate();

            // If the user hasnt set up a question yet, send them to setup page
            // if (is_null($user->security_question)) {
            //     return redirect()->route('security.show'); 
            // }

            return redirect()->route('dashboard');
        }

        return back()->with('failed', "Wrong credentials!")->withInput($request->only('username'));
    }

    // Handle Logout
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }   

    // =========================================================
    // 🔐 FORGOT PASSWORD LOGIC
    // =========================================================

    // Show the Wizard (Starts at Step 1)
    public function forgotPassword() {
        return view('forgot-password', ['step' => 1]);
    }

    // Handle All Steps (Find -> Verify -> Reset)
    public function processForgotPassword(Request $request) {
        
        $step = $request->input('step', 1);

        // --- STEP 1: FIND USER & SHOW QUESTION ---
        if ($step == 1) {
            $request->validate(['username' => 'required']);
            
            $user = DB::table('users')->where('username', $request->username)->first();

            if (!$user) {
                return back()->with('error', 'Account not found.');
            }

            if (!$user->security_question) {
                return back()->with('error', 'This account has not set up a security question. Please directly contact the Admin.');
            }

            // Success: Move to Step 2
            return view('forgot-password', [
                'step'      => 2,
                'user_id'   => $user->id,
                'question'  => $user->security_question
            ]);
        }

        // --- STEP 2: VERIFY ANSWER ---
        if ($step == 2) {
            $request->validate([
                'user_id' => 'required',
                'answer'  => 'required'
            ]);

            $user = DB::table('users')->where('id', $request->user_id)->first();

            // Normalize input (Lowercase & Trim)
            $cleanAnswer = Str::lower(trim($request->answer));
            
            if (Hash::check($cleanAnswer, $user->security_answer)) {
                return view('forgot-password', [
                    'step'    => 3,
                    'user_id' => $user->id
                ]);
            } else {
                return view('forgot-password', [
                    'step'         => 2,
                    'user_id'      => $user->id,
                    'question'     => $user->security_question,
                    'answer_error' => 'Incorrect Answer.'
                ]);
            }
        }

        // --- STEP 3: RESET PASSWORD ---
        if ($step == 3) {
                $validator = Validator::make($request->all(), [
                    'user_id'  => 'required',
                    'password' => 'required|min:6|confirmed', // 'confirmed' checks password_confirmation
                ]);

                if ($validator->fails()) {
                    return view('forgot-password', [
                        'step'    => 3,
                        'user_id' => $request->user_id
                    ])->withErrors($validator); // Send the errors to the view
                }

                DB::table('users')
                    ->where('id', $request->user_id)
                    ->update([
                        'password'   => Hash::make($request->password),
                        'updated_at' => now()
                    ]);

                return redirect()->route('login')->with('success', 'Password reset successfully! Please login.');
        }
    }
}