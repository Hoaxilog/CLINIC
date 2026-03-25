<?php

namespace App\Http\Controllers;

use App\Support\InputSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PatientOnboardingController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        if (! $user || ! $user->isPatient()) {
            abort(403);
        }

        if (! $user->requiresAccountSetupCompletion()) {
            return redirect()->route('patient.dashboard');
        }

        return view('patient.complete-profile', [
            'user' => $user,
            'hasMiddleNameColumn' => Schema::hasColumn('users', 'middle_name'),
            'hasBirthDateColumn' => Schema::hasColumn('users', 'birth_date'),
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (! $user || ! $user->isPatient()) {
            abort(403);
        }

        if (! $user->requiresAccountSetupCompletion()) {
            return redirect()->route('patient.dashboard');
        }

        $sanitized = [
            'first_name' => InputSanitizer::sanitizeTitleCase($request->input('first_name')),
            'last_name' => InputSanitizer::sanitizeTitleCase($request->input('last_name')),
            'mobile_number' => InputSanitizer::sanitizeCountryCodeLocalNumber($request->input('mobile_number')),
        ];

        $hasMiddleNameColumn = Schema::hasColumn('users', 'middle_name');
        $hasBirthDateColumn = Schema::hasColumn('users', 'birth_date');

        if ($hasMiddleNameColumn) {
            $sanitized['middle_name'] = InputSanitizer::sanitizeTitleCase($request->input('middle_name'));
        }

        if ($hasBirthDateColumn) {
            $sanitized['birth_date'] = trim((string) $request->input('birth_date'));
        }

        $rules = [
            'first_name' => ['required', 'string', 'min:2', 'max:100', "regex:/^[\\pL\\pM\\s'\\-]+$/u"],
            'last_name' => ['required', 'string', 'min:2', 'max:100', "regex:/^[\\pL\\pM\\s'\\-]+$/u"],
            'mobile_number' => ['required', 'digits:10'],
        ];

        if ($hasMiddleNameColumn) {
            $rules['middle_name'] = ['nullable', 'string', 'max:100', "regex:/^[\\pL\\pM\\s'\\-]+$/u"];
        }

        if ($hasBirthDateColumn) {
            $rules['birth_date'] = ['required', 'date', 'before:today'];
        }

        $validated = validator($sanitized, $rules, [
            'mobile_number.digits' => 'Contact number must be exactly 10 digits after +63.',
            'birth_date.before' => 'Birth date must be a past date.',
        ])->validate();

        $updatePayload = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'mobile_number' => $validated['mobile_number'],
            'updated_at' => now(),
        ];

        if ($hasMiddleNameColumn) {
            $updatePayload['middle_name'] = ($validated['middle_name'] ?? '') !== '' ? $validated['middle_name'] : null;
        }

        if ($hasBirthDateColumn) {
            $updatePayload['birth_date'] = $validated['birth_date'];
        }

        DB::table('users')
            ->where('id', $user->id)
            ->update($updatePayload);

        return redirect()
            ->route('patient.dashboard')
            ->with('success', 'Your account setup is complete.');
    }
}
