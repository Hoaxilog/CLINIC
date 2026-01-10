<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = DB::table('users')->where('id', Auth::id())->first();
        // Fetch role name for display
        $roleName = DB::table('roles')->where('id', $user->role)->value('role_name');
        
        return view('profile', compact('user', 'roleName'));
    }

    public function update(Request $request)
    {
        $userId = Auth::id();

        // Validate strictly against your existing columns
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($userId)],
            'contact'  => ['required', 'string', 'max:20'],
        ]);

        // Update strictly your existing columns
        DB::table('users')->where('id', $userId)->update([
            'username' => $validated['username'],
            'contact'  => $validated['contact'],
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        DB::table('users')->where('id', Auth::id())->update([
            'password' => Hash::make($request->password),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Password changed successfully.');
    }
}