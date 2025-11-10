<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Login extends Controller
{   
    public function index() {   
        return view("login");
    }

    public function login(Request $request) {

        $validation = $request->validate([
            'username' => 'required|',
            'password' => 'required',
        ]);

        $users = DB::table('users')
                    ->where('username', $request->input('username'))
                    ->first();

        if(!$users) {
            return back()->with('failed', "Wrong credentials!")
                        ->withInput($request->only('username'));
        }

        if(Auth::attempt($validation)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

    }
    
}
