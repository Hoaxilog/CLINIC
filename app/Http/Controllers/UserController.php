<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
        $roles = DB::table('roles')->get();

        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        // 1. Validation (Keep your existing validation)
        $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:users'],
            'contact'  => ['nullable', 'string', 'max:225'],
            'password' => ['required', 'confirmed', 'min:8'],
            'role'     => ['required', 'integer', 'exists:roles,id'],
        ]);

        // 2. Prepare Data
        $insertData = [
            'username'   => $request->username,
            'contact'    => $request->contact,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // 3. Insert and Get ID (We need the ID for the log)
        DB::table('users')->insert($insertData);
        $newUser = DB::table('users')->where('username', $request->username)->first();

        // 4. === LOGGING (Create) ===
        // No "Diff Check" needed because everything is new.
        $subject = new \App\Models\User();
        $subject->id = $newUser->id;

        activity()
            ->causedBy(Auth::user())
            ->performedOn($subject)
            ->event('user_created') // Specific Event
            ->withProperties([
                'attributes' => $insertData // Log all the new info
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

        $roles = DB::table('roles')->get();

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
            'username' => ['required', 'string', 'max:50', 'unique:users,username,' . $id . ',id'],
            'contact'  => ['nullable', 'string', 'max:225'],
            'role'     => ['required', 'integer', 'exists:roles,id'],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        // 3. Prepare the New Data
        $updateData = [
            'username'   => $request->username,
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
                    'old' => $oldAttributes,       // e.g. "Staff"
                    'attributes' => $changedAttributes // e.g. "Admin"
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
                'attributes' => (array) $user // Save the backup here
            ])
            ->log('Deleted User Account'); // Specific Description
        // ===========================

        // 3. Now it is safe to delete
        DB::table('users')->where('id', $id)->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
