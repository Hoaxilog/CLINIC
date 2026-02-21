<?php

namespace App\Http\Controllers\Auth;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class GoogleLoginController extends Controller
{
    public function redirectToGoogle() {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback() {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = DB::table('users')->where('email', $googleUser->email)->first();

            if ($user) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'google_id' => $googleUser->id, 
                        'email_verified_at' => now(), // Auto-verify
                    ]);

                Auth::loginUsingId($user->id);
                $role = $user->role;
                if ($role === 3) {
                    return redirect()->route('patient.dashboard');
                }
                return redirect()->route('dashboard');
                
            } else {
                $newUserId = DB::table('users')->insertGetId([
                    'username' => $googleUser->email, 
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'role' => 3, // Role 3 = Patient
                    'password' => Hash::make(Str::random(40)), 
                    'email_verified_at' => now(), 
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                Auth::loginUsingId($newUserId);
                return redirect()->route('patient.dashboard');
            }
        } catch (Exception $th) {
            return redirect('/login')->with('failed', 'Google Login failed. Please try again.');     
        }
    }
}
