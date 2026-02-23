<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ProfileController extends Controller
{
    public function index()
    {
        $user = DB::table('users')->where('id', Auth::id())->first();
        if ($user && (int) $user->role === 3) {
            $patient = $this->resolvePatientForUser($user);
            return view('patient.profile', compact('user', 'patient'));
        }

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

    private function resolvePatientForUser($user)
    {
        $patient = null;
        $usesUserId = false;

        try {
            $usesUserId = Schema::hasColumn('patients', 'user_id');
        } catch (Throwable $e) {
            $usesUserId = false;
        }

        if ($usesUserId) {
            $patient = DB::table('patients')->where('user_id', $user->id)->first();
        }

        if (!$patient && !empty($user->email)) {
            $patient = DB::table('patients')->where('email_address', $user->email)->first();

            if ($patient && $usesUserId && empty($patient->user_id)) {
                DB::table('patients')
                    ->where('id', $patient->id)
                    ->update([
                        'user_id' => $user->id,
                        'updated_at' => now(),
                    ]);
                $patient->user_id = $user->id;
            }
        }

        return $patient;
    }
}
