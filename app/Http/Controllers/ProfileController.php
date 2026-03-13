<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Throwable;

class ProfileController extends Controller
{
    protected ?array $userTableColumns = null;
    protected ?array $patientTableColumns = null;

    public function index()
    {
        $user = DB::table('users')->where('id', Auth::id())->first();
        $isGoogleUser = $user && !empty($user->google_id);
        if ($user && (int) $user->role === 3) {
            $patient = $this->resolvePatientForUser($user);
            return view('patient.profile', compact('user', 'patient', 'isGoogleUser'));
        }

        // Fetch role name for display
        $roleName = DB::table('roles')->where('id', $user->role)->value('role_name');

        return view('profile', compact('user', 'roleName', 'isGoogleUser'));
    }

    public function update(Request $request)
    {
        $userId = Auth::id();
        $user = DB::table('users')->where('id', $userId)->first();

        if (!$user) {
            return back()->with('failed', 'Unable to find your account.');
        }

        $rules = [
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($userId)],
            'contact'  => ['nullable', 'regex:/^[0-9]+$/', 'max:20'],
        ];

        if ((int) $user->role === 3) {
            $rules = array_merge($rules, [
                'birth_date' => ['nullable', 'date'],
                'gender' => ['nullable', Rule::in(['Male', 'Female', 'Other'])],
                'home_address' => ['nullable', 'string', 'max:255'],
                'emergency_contact_name' => ['nullable', 'string', 'max:255'],
                'emergency_contact_number' => ['nullable', 'regex:/^[0-9]+$/', 'max:20'],
                'relationship' => ['nullable', 'string', 'max:100'],
            ]);
        } else {
            $rules['position'] = ['nullable', 'string', 'max:100'];
        }

        $validated = $request->validate($rules, [
            'contact.regex' => 'The contact number must contain digits only.',
            'emergency_contact_number.regex' => 'The emergency contact number must contain digits only.',
        ]);

        $updates = [
            'updated_at' => now(),
        ];

        if ($this->userHasColumn('username')) {
            $updates['username'] = $validated['username'];
        }

        if ($this->userHasColumn('contact')) {
            $updates['contact'] = $validated['contact'] ?? data_get($user, 'contact');
        }

        if ((int) $user->role !== 3 && $this->userHasColumn('position')) {
            $updates['position'] = $validated['position'] ?? data_get($user, 'position');
        }

        DB::table('users')->where('id', $userId)->update($updates);

        if ((int) $user->role === 3) {
            $patient = $this->resolvePatientForUser($user);

            if ($patient) {
                $patientUpdates = ['updated_at' => now()];

                if ($this->patientHasColumn('mobile_number')) {
                    $patientUpdates['mobile_number'] = $validated['contact'] ?: null;
                }

                if ($this->patientHasColumn('birth_date')) {
                    $patientUpdates['birth_date'] = $validated['birth_date'] ?? data_get($patient, 'birth_date');
                }

                if ($this->patientHasColumn('gender')) {
                    $patientUpdates['gender'] = $validated['gender'] ?? data_get($patient, 'gender');
                }

                if ($this->patientHasColumn('home_address')) {
                    $patientUpdates['home_address'] = $validated['home_address'] ?? data_get($patient, 'home_address');
                }

                if ($this->patientHasColumn('emergency_contact_name')) {
                    $patientUpdates['emergency_contact_name'] = $validated['emergency_contact_name'] ?? data_get($patient, 'emergency_contact_name');
                }

                if ($this->patientHasColumn('emergency_contact_number')) {
                    $patientUpdates['emergency_contact_number'] = $validated['emergency_contact_number'] ?? data_get($patient, 'emergency_contact_number');
                }

                if ($this->patientHasColumn('relationship')) {
                    $patientUpdates['relationship'] = $validated['relationship'] ?? data_get($patient, 'relationship');
                }

                DB::table('patients')
                    ->where('id', $patient->id)
                    ->update($patientUpdates);
            }
        }

        return back()->with('success', 'Profile updated successfully.');
    }

    public function uploadProfilePicture(Request $request)
    {
        $userId = Auth::id();

        // Validate only the profile picture - 10MB limit
        $validated = $request->validate([
            'profile_picture' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'], // 10MB max
        ], [
            'profile_picture.required' => 'Please select an image to upload.',
            'profile_picture.image' => 'The file must be an image.',
            'profile_picture.mimes' => 'Only JPEG, PNG, GIF, and WebP images are allowed.',
            'profile_picture.max' => 'Image size must not exceed 10MB.',
        ]);

        $user = DB::table('users')->find($userId);

        if (!$this->userHasColumn('profile_picture')) {
            return back()->with('failed', 'The profile_picture column is missing from your users table. Apply the SQL patch first.');
        }
        
        // Delete old picture if exists
        $existingPicture = data_get($user, 'profile_picture');
        if ($user && $existingPicture && file_exists(storage_path('app/public/' . $existingPicture))) {
            Storage::disk('public')->delete($existingPicture);
        }

        // Store new picture
        $file = $request->file('profile_picture');
        $filename = 'profile_' . $userId . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('profiles', $filename, 'public');

        // Update user record
        DB::table('users')->where('id', $userId)->update([
            'profile_picture' => $path,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Profile picture updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $user = DB::table('users')->where('id', Auth::id())->first();
        $isGoogleUser = $user && !empty($user->google_id);

        $rules = [
            'password' => ['required', 'confirmed', 'min:8'],
        ];

        if ($isGoogleUser) {
            if ($request->filled('current_password')) {
                $rules['current_password'] = ['current_password'];
            }
        } else {
            $rules['current_password'] = ['required', 'current_password'];
        }

        $request->validate($rules, [
            'current_password.required' => 'Your current password is required.',
            'current_password.current_password' => 'The current password is incorrect.',
            'password.required' => 'Please enter a new password.',
            'password.min' => 'The new password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        DB::table('users')->where('id', Auth::id())->update([
            'password' => Hash::make($request->password),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Password changed successfully.');
    }

    public function sendPasswordResetLink(Request $request)
    {
        $user = DB::table('users')->where('id', Auth::id())->first();

        if (!$user || empty($user->email)) {
            return back()->with('failed', 'We could not find an email address for your account.');
        }

        $key = 'profile-reset:' . Str::lower($user->email) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = max(1, ceil($seconds / 60));
            return back()->with('failed', "Too many requests. Try again in {$minutes} minute(s).");
        }

        RateLimiter::hit($key, 300);

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $token,
                'created_at' => Carbon::now(),
            ]
        );

        Mail::send('auth.emails.password-reset', ['token' => $token, 'email' => $user->email], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Reset Password Request - Tejadent');
        });

        return back()->with('success', 'We sent a password reset link to your email.');
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

    private function userHasColumn(string $column): bool
    {
        if ($this->userTableColumns === null) {
            try {
                $this->userTableColumns = Schema::getColumnListing('users');
            } catch (Throwable $e) {
                $this->userTableColumns = [];
            }
        }

        return in_array($column, $this->userTableColumns, true);
    }

    private function patientHasColumn(string $column): bool
    {
        if ($this->patientTableColumns === null) {
            try {
                $this->patientTableColumns = Schema::getColumnListing('patients');
            } catch (Throwable $e) {
                $this->patientTableColumns = [];
            }
        }

        return in_array($column, $this->patientTableColumns, true);
    }

    public function deleteAccount(Request $request)
    {
        $user = Auth::user();
        
        // Only allow patients (role 3) to delete their own account
        if ((int)$user->role !== 3) {
            return back()->with('failed', 'You do not have permission to delete this account.');
        }

        try {
            DB::transaction(function () use ($user) {
                $patient = $this->resolvePatientForUser($user);

                if ($patient) {
                    DB::table('patients')->where('id', $patient->id)->delete();
                }

                if ($user->profile_picture && file_exists(storage_path('app/public/' . $user->profile_picture))) {
                    Storage::disk('public')->delete($user->profile_picture);
                }

                DB::table('users')->where('id', $user->id)->delete();
            });
            
            // Logout user
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect('/')->with('success', 'Your account has been permanently deleted.');
        } catch (Throwable $e) {
            Log::error('Account deletion error: ' . $e->getMessage());
            return back()->with('failed', 'An error occurred while deleting your account. Please try again.');
        }
    }
}
