<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 12;

        // Admins
        $admins = DB::table('users')
            ->join('roles', 'users.role', '=', 'roles.id')
            ->where('roles.role_name', 'admin')
            ->orderBy('users.created_at', 'desc')
            ->select('users.*', 'roles.role_name')
            ->paginate($perPage, ['*'], 'admins_page');

        // Staff
        $staffs = DB::table('users')
            ->join('roles', 'users.role', '=', 'roles.id')
            ->where('roles.role_name', 'staff')
            ->orderBy('users.created_at', 'desc')
            ->select('users.*', 'roles.role_name')
            ->paginate($perPage, ['*'], 'staffs_page');

        // Normal users
        $normalUsers = DB::table('users')
            ->leftJoin('roles', 'users.role', '=', 'roles.id')
            ->where(function ($query) {
                $query->whereNotIn('roles.role_name', ['admin', 'staff'])
                    ->orWhereNull('roles.role_name');
            })
            ->orderBy('users.created_at', 'desc')
            ->select('users.*', 'roles.role_name')
            ->paginate($perPage, ['*'], 'users_page');

        return view('users.index', compact('admins', 'staffs', 'normalUsers'));
    }

    public function create()
    {
        $roles = DB::table('roles')
            ->whereIn('role_name', ['admin', 'staff'])
            ->orderBy('id')
            ->get();

        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $allowedRoleIds = $this->allowedStaffRoleIds();

        if (empty($allowedRoleIds)) {
            return redirect()->route('users.index')->with('error', 'Admin/Staff roles are not configured.');
        }

        $request->validate([
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email'), Rule::unique('users', 'username')],
            'contact'  => ['nullable', 'string', 'max:225'],
            'password' => ['required', 'confirmed', 'min:12', 'regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).+$/'],
            'role'     => ['required', 'integer', Rule::in($allowedRoleIds)],
        ], [
            'password.regex' => 'Password must include at least one letter, one number, and one symbol.',
        ]);

        $token = Str::random(64);
        $insertData = [
            'username'   => $request->email,
            'email'      => $request->email,
            'contact'    => $request->contact,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
            'verification_token' => $token,
            'email_verified_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $newUserId = DB::table('users')->insertGetId($insertData);
        $newUser = DB::table('users')->where('id', $newUserId)->first();

        Mail::send('auth.emails.verify-email', ['token' => $token, 'id' => $newUserId, 'name' => 'Team Member'], function($message) use($request) {
            $message->to($request->email);
            $message->subject('Verify Your Email Address - Tejadent');
        });

        // 4. === LOGGING (Create) ===
        // No "Diff Check" needed because everything is new.
        $subject = new \App\Models\User();
        $subject->id = $newUser->id;

        activity()
            ->causedBy(Auth::user())
            ->performedOn($subject)
            ->event('user_created') // Specific Event
            ->withProperties([
                'attributes' => $this->sanitizeAuditAttributes($insertData)
            ])
            ->log('Created User Account'); // Specific Description
        // ===========================

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }
    public function edit($id)
    {
        $user = DB::table('users')->where('id', $id)->first();

        if (! $user) {
            abort(404);
        }

        $roles = DB::table('roles')
            ->whereIn('role_name', ['admin', 'staff'])
            ->orderBy('id')
            ->get();

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        // 1. Fetch the Old Data (Before we change anything)
        $user = DB::table('users')->where('id', $id)->first();
        $oldDataArray = (array) $user; // Convert object to array for easy checking

        if (!$user) {
            abort(404);
        }

        // 2. Validate Inputs
        $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($id),
                Rule::unique('users', 'username')->ignore($id),
            ],
            'contact'  => ['nullable', 'string', 'max:225'],
            'role'     => ['required', 'integer', Rule::in($this->allowedStaffRoleIds())],
            'password' => ['nullable', 'confirmed', 'min:12', 'regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).+$/'],
        ], [
            'password.regex' => 'Password must include at least one letter, one number, and one symbol.',
        ]);

        // 3. Prepare the New Data
        $updateData = [
            'username'   => $request->email,
            'email'      => $request->email,
            'contact'    => $request->contact,
            'role'       => $request->role,
            'updated_at' => now(),
        ];

        // Handle Password (only if provided)
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        // 4. === SMART DIFF CHECK (The Fix) ===
        // We calculate what actually changed before running the update
        $changedAttributes = [];
        $oldAttributes = [];

        foreach ($updateData as $key => $newValue) {
            // Skip technical fields
            if ($key === 'updated_at') continue;

            // Check if value changed
            // Note: Passwords will always look "different" because of hashing, which is correct.
            if (array_key_exists($key, $oldDataArray) && $oldDataArray[$key] != $newValue) {
                $changedAttributes[$key] = $newValue;       
                $oldAttributes[$key] = $oldDataArray[$key]; 
            }
            // Special case: If we added a new field (like setting a security question for the first time)
            elseif (!array_key_exists($key, $oldDataArray)) {
                $changedAttributes[$key] = $newValue;
            }
        }

        // 5. Update the Database
        DB::table('users')->where('id', $id)->update($updateData);

        // 6. Log ONLY if something changed
        if (!empty($changedAttributes)) {
            $subject = new \App\Models\User();
            $subject->id = $id;

            activity()
                ->causedBy(Auth::user())
                ->performedOn($subject)
                ->event('user_updated')
                ->withProperties([
                    'old' => $this->sanitizeAuditAttributes($oldAttributes),
                    'attributes' => $this->sanitizeAuditAttributes($changedAttributes)
                ])
                ->log('Updated User Account');
        }

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        // 1. Fetch the user BEFORE deleting (So we have a backup)
        $user = DB::table('users')->where('id', $id)->first();

        if (!$user) {
            abort(404);
        }

        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        // 2. === LOGGING (Delete) ===
        // We log the snapshot of the user before they are removed.
        $subject = new \App\Models\User();
        $subject->id = $id;

        activity()
            ->causedBy(Auth::user())
            ->performedOn($subject)
            ->event('user_deleted') // Specific Event
            ->withProperties([
                'attributes' => $this->sanitizeAuditAttributes((array) $user)
            ])
            ->log('Deleted User Account'); // Specific Description
        // ===========================

        // 3. Now it is safe to delete
        DB::table('users')->where('id', $id)->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    private function allowedStaffRoleIds(): array
    {
        return DB::table('roles')
            ->whereIn('role_name', ['admin', 'staff'])
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    private function sanitizeAuditAttributes(array $attributes): array
    {
        unset(
            $attributes['password'],
            $attributes['remember_token'],
            $attributes['verification_token']
        );

        return $attributes;
    }
}
