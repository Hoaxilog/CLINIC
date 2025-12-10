<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Show lists grouped by role: admins, staffs, and normal users.
     * Each group has its own paginator.
     */
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
        $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:users'],
            'contact'  => ['nullable', 'string', 'max:225'],
            'password' => ['required', 'confirmed', 'min:8'],
            'role'     => ['required', 'integer', 'exists:roles,id'],
        ]);

        DB::table('users')->insert([
            'username'   => $request->username,
            'contact'    => $request->contact,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $user = DB::table('users')->where('id', $id)->first();

        if (!$user) {
            abort(404);
        }

        $roles = DB::table('roles')->get();
        
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = DB::table('users')->where('id', $id)->first();

        if (!$user) {
            abort(404);
        }

        $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:users,username,' . $id . ',id'],
            'contact'  => ['nullable', 'string', 'max:225'],
            'role'     => ['required', 'integer', 'exists:roles,id'],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        $updateData = [
            'username'   => $request->username,
            'contact'    => $request->contact,
            'role'       => $request->role,
            'updated_at' => now(),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        DB::table('users')->where('id', $id)->update($updateData);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = DB::table('users')->where('id', $id)->first();

        if (!$user) {
            abort(404);
        }

        // Check if user is trying to delete their own account
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        DB::table('users')->where('id', $id)->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}