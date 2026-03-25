<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 12;

        // Admins
        $admins = DB::table('users')
            ->where('users.role', User::ROLE_ADMIN)
            ->orderBy('users.created_at', 'desc')
            ->select('users.*')
            ->paginate($perPage, ['*'], 'admins_page');

        // Dentists
        $dentists = DB::table('users')
            ->where('users.role', User::ROLE_DENTIST)
            ->orderBy('users.created_at', 'desc')
            ->select('users.*')
            ->paginate($perPage, ['*'], 'dentists_page');

        // Staff
        $staffs = DB::table('users')
            ->where('users.role', User::ROLE_STAFF)
            ->orderBy('users.created_at', 'desc')
            ->select('users.*')
            ->paginate($perPage, ['*'], 'staffs_page');

        // Patient and other non-internal users
        $normalUsers = DB::table('users')
            ->whereNotIn('users.role', array_keys(User::internalRoleOptions()))
            ->orderBy('users.created_at', 'desc')
            ->select('users.*')
            ->paginate($perPage, ['*'], 'users_page');

        return view('users.index', compact('admins', 'dentists', 'staffs', 'normalUsers'));
    }

    public function create()
    {
        $roles = $this->manageableRoles();

        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $allowedRoleIds = $this->allowedStaffRoleIds();

        if (empty($allowedRoleIds)) {
            return redirect()->route('users.index')->with('error', 'Internal roles are not configured.');
        }

        $validated = $this->validateUserRequest($request, $allowedRoleIds, true);

        $token = Str::random(64);
        $firstName = $validated['first_name'];
        $lastName = $validated['last_name'];
        $mobileNumber = $validated['mobile_number'];
        $recipientName = trim(implode(' ', array_filter([$firstName, $lastName]))) ?: 'Team Member';

        $insertData = [
            'username' => $validated['email'],
            'email' => $validated['email'],
            'first_name' => $firstName,
            'last_name' => $lastName,
            'mobile_number' => $mobileNumber,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'verification_token' => $token,
            'email_verified_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $newUserId = DB::table('users')->insertGetId($insertData);
        $newUser = DB::table('users')->where('id', $newUserId)->first();

        Mail::send('auth.emails.verify-email', ['token' => $token, 'id' => $newUserId, 'name' => $recipientName], function ($message) use ($validated) {
            $message->to($validated['email']);
            $message->subject('Verify Your Email Address - Tejadent');
        });

        // 4. === LOGGING (Create) ===
        // No "Diff Check" needed because everything is new.
        $subject = new User;
        $subject->id = $newUser->id;

        activity()
            ->causedBy(Auth::user())
            ->performedOn($subject)
            ->event('user_created') // Specific Event
            ->withProperties([
                'attributes' => $this->sanitizeAuditAttributes($insertData),
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

        $roles = $this->editableRoles();

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        // 1. Fetch the Old Data (Before we change anything)
        $user = DB::table('users')->where('id', $id)->first();
        $oldDataArray = (array) $user; // Convert object to array for easy checking

        if (! $user) {
            abort(404);
        }

        // 2. Validate Inputs
        $validated = $this->validateUserRequest($request, $this->editableRoleIds(), false, $id);

        // 3. Prepare the New Data
        $updateData = [
            'username' => $validated['email'],
            'email' => $validated['email'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'mobile_number' => $validated['mobile_number'],
            'role' => $validated['role'],
            'updated_at' => now(),
        ];

        // Handle Password (only if provided)
        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        // 4. === SMART DIFF CHECK (The Fix) ===
        // We calculate what actually changed before running the update
        $changedAttributes = [];
        $oldAttributes = [];

        foreach ($updateData as $key => $newValue) {
            // Skip technical fields
            if ($key === 'updated_at') {
                continue;
            }

            // Check if value changed
            // Note: Passwords will always look "different" because of hashing, which is correct.
            if (array_key_exists($key, $oldDataArray) && $oldDataArray[$key] != $newValue) {
                $changedAttributes[$key] = $newValue;
                $oldAttributes[$key] = $oldDataArray[$key];
            }
            // Special case: If we added a new field (like setting a security question for the first time)
            elseif (! array_key_exists($key, $oldDataArray)) {
                $changedAttributes[$key] = $newValue;
            }
        }

        // 5. Update the Database
        DB::table('users')->where('id', $id)->update($updateData);

        // 6. Log ONLY if something changed
        if (! empty($changedAttributes)) {
            $subject = new User;
            $subject->id = $id;

            activity()
                ->causedBy(Auth::user())
                ->performedOn($subject)
                ->event('user_updated')
                ->withProperties([
                    'old' => $this->sanitizeAuditAttributes($oldAttributes),
                    'attributes' => $this->sanitizeAuditAttributes($changedAttributes),
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

        if (! $user) {
            abort(404);
        }

        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        // 2. === LOGGING (Delete) ===
        // We log the snapshot of the user before they are removed.
        $subject = new User;
        $subject->id = $id;

        activity()
            ->causedBy(Auth::user())
            ->performedOn($subject)
            ->event('user_deleted') // Specific Event
            ->withProperties([
                'attributes' => $this->sanitizeAuditAttributes((array) $user),
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
        return array_keys(User::internalRoleOptions());
    }

    /**
     * @return Collection<int, object>
     */
    private function manageableRoles()
    {
        return collect(User::internalRoleOptions())
            ->map(fn (string $label, int $id) => (object) [
                'id' => $id,
                'label' => $label,
            ])
            ->values();
    }

    /**
     * @return Collection<int, object>
     */
    private function editableRoles()
    {
        return collect([
            User::ROLE_ADMIN => 'Admin',
            User::ROLE_DENTIST => 'Dentist',
            User::ROLE_STAFF => 'Staff',
            User::ROLE_PATIENT => 'Patient',
        ])->map(fn (string $label, int $id) => (object) [
            'id' => $id,
            'label' => $label,
        ])->values();
    }

    private function editableRoleIds(): array
    {
        return [
            User::ROLE_ADMIN,
            User::ROLE_DENTIST,
            User::ROLE_STAFF,
            User::ROLE_PATIENT,
        ];
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

    private function userValidationRules(array $allowedRoleIds, bool $passwordRequired, ?int $userId = null): array
    {
        $passwordRules = [$passwordRequired ? 'required' : 'nullable', 'confirmed', 'min:8'];

        return [
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
                Rule::unique('users', 'username')->ignore($userId),
            ],
            'first_name' => ['required', 'string', 'min:2', 'max:100', "regex:/^[\\pL\\s'\\-]+$/u"],
            'last_name' => ['required', 'string', 'min:2', 'max:100', "regex:/^[\\pL\\s'\\-]+$/u"],
            'mobile_number' => ['nullable', 'regex:/^09\\d{9}$/'],
            'role' => ['required', 'integer', Rule::in($allowedRoleIds)],
            'password' => $passwordRules,
        ];
    }

    private function userValidationMessages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'first_name.min' => 'First name must be at least 2 characters.',
            'first_name.regex' => 'First name may only contain letters, spaces, hyphens, and apostrophes.',
            'last_name.required' => 'Last name is required.',
            'last_name.min' => 'Last name must be at least 2 characters.',
            'last_name.regex' => 'Last name may only contain letters, spaces, hyphens, and apostrophes.',
            'email.required' => 'Email is required.',
            'email.email' => 'Enter a valid email address.',
            'email.unique' => 'That email is already in use.',
            'mobile_number.regex' => 'Mobile number must be in 09XXXXXXXXX format.',
            'role.required' => 'Please select a role.',
            'role.in' => 'The selected role is invalid.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }

    private function validateUserRequest(Request $request, array $allowedRoleIds, bool $passwordRequired, ?int $userId = null): array
    {
        $input = [
            'first_name' => trim((string) $request->input('first_name')),
            'last_name' => trim((string) $request->input('last_name')),
            'email' => strtolower(trim((string) $request->input('email'))),
            'mobile_number' => trim((string) $request->input('mobile_number')),
            'role' => $request->input('role'),
            'password' => (string) $request->input('password', ''),
            'password_confirmation' => (string) $request->input('password_confirmation', ''),
        ];

        if ($input['mobile_number'] === '') {
            $input['mobile_number'] = null;
        }

        if (! $passwordRequired && $input['password'] === '') {
            $input['password'] = null;
            $input['password_confirmation'] = null;
        }

        $validator = Validator::make(
            $input,
            $this->userValidationRules($allowedRoleIds, $passwordRequired, $userId),
            $this->userValidationMessages()
        );

        return $validator->validate();
    }
}
