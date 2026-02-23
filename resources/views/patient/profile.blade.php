@extends('index')

@section('content')
@php
    $patientFirstName = data_get($patient, 'first_name', '');
    $patientLastName = data_get($patient, 'last_name', '');
    $patientName = trim($patientFirstName . ' ' . $patientLastName);
    $patientCode = $patient ? sprintf('PT%04d', $patient->id) : null;
    $patientInitials = $patient
        ? strtoupper(substr($patientFirstName ?: 'P', 0, 1) . substr($patientLastName ?: '', 0, 1))
        : strtoupper(substr($user->username ?? 'P', 0, 1));
    $birthDate = data_get($patient, 'birth_date') ? \Carbon\Carbon::parse($patient->birth_date)->format('M d, Y') : 'N/A';
@endphp

<main id="mainContent" class="min-h-screen bg-slate-50 p-4 pb-10 sm:p-6 lg:p-8">
    <div class="mb-6">
        <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Patient Portal</p>
        <h1 class="mt-2 text-2xl font-bold text-slate-900 sm:text-3xl">My Profile</h1>
        <p class="mt-2 text-sm text-slate-600">
            Review your personal details and keep your contact information up to date.
        </p>
    </div>

    @if (!$patient)
        <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Patient profile not linked</h2>
            <p class="mt-2 text-sm text-slate-600">
                We couldn't find a patient record associated with your account. Please contact the clinic so we can link your profile.
            </p>
        </section>
    @endif

    <section class="mt-6 grid gap-6 lg:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
            <div class="flex items-center gap-4">
                <div class="h-14 w-14 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-lg font-bold">
                    {{ $patientInitials }}
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-400">Patient ID</p>
                    <p class="text-lg font-semibold text-slate-900">{{ $patientCode ?? 'Not linked' }}</p>
                    <p class="text-sm text-slate-600">{{ $patientName ?: $user->username }}</p>
                </div>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-400">Birth Date</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $birthDate }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-400">Gender</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $patient?->gender ?? 'N/A' }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-400">Mobile</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $patient?->mobile_number ?? 'N/A' }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-400">Email</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $patient?->email_address ?? $user->email ?? 'N/A' }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4 sm:col-span-2">
                    <p class="text-xs uppercase tracking-wide text-slate-400">Home Address</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $patient?->home_address ?? 'N/A' }}</p>
                </div>
            </div>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Emergency Contact</h2>
            <div class="mt-4 space-y-3">
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-400">Contact Name</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $patient?->emergency_contact_name ?? 'N/A' }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-400">Contact Number</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $patient?->emergency_contact_number ?? 'N/A' }}</p>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-400">Relationship</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $patient?->relationship ?? 'N/A' }}</p>
                </div>
            </div>
        </article>
    </section>

    <section class="mt-6 grid gap-6 lg:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900">Account Settings</h2>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" class="mt-5 grid gap-4 sm:grid-cols-2">
                @csrf
                @method('PATCH')

                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Username</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                    @error('username')
                        <span class="text-xs text-rose-600">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Contact Number</label>
                    <input type="text" name="contact" value="{{ old('contact', data_get($user, 'contact')) }}"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                    @error('contact')
                        <span class="text-xs text-rose-600">{{ $message }}</span>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">
                        Save Changes
                    </button>
                </div>
            </form>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Security</h2>
            <form action="{{ route('profile.password') }}" method="POST" class="mt-4 space-y-3">
                @csrf
                @method('PUT')

                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Current Password</label>
                    <input type="password" name="current_password"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                        placeholder="Enter current password">
                    @error('current_password')
                        <span class="text-xs text-rose-600">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">New Password</label>
                    <input type="password" name="password"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                        placeholder="Enter new password">
                    @error('password')
                        <span class="text-xs text-rose-600">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Confirm Password</label>
                    <input type="password" name="password_confirmation"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                        placeholder="Confirm new password">
                </div>

                <div class="pt-1">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                        Update Password
                    </button>
                </div>
            </form>
        </article>
    </section>
</main>
@endsection
