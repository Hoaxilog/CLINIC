<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Login extends Controller
{   
    public function index() {   
        return view("login");
    }

    public function login(Request $request) {
        
        $request->validate([
            'username' => 'required|',
            'password' => 'required',
        ]);

        $user = DB::table('users')
                    ->where('username', $request->username)
                    ->first();

        if($user && Hash::check($request->password, $user->password)) {
            Auth::loginUsingId($user->id);
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        return back()->with('failed', "Wrong credentials!")->withInput($request->only('username'));
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }   
    
}
