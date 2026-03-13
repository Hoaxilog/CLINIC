@extends('index')

@section('content')
@php
    $patientFirstName = data_get($patient, 'first_name', '');
    $patientLastName = data_get($patient, 'last_name', '');
    $patientName = trim($patientFirstName . ' ' . $patientLastName);
    $patientCode = $patient ? sprintf('PT%04d', $patient->id) : null;
    $profilePictureUrl = !empty($user->profile_picture)
        ? asset('storage/' . $user->profile_picture) . '?v=' . urlencode((string) strtotime((string) $user->updated_at))
        : null;
    $displayName = $patientName ?: ($user->username ?: 'Patient');
@endphp

<main id="mainContent" class="min-h-screen overflow-hidden bg-[#f4f8fb] p-4 sm:p-6 lg:p-8">
    <div class="relative mb-8 flex flex-col gap-2">
        <span class="inline-flex w-fit rounded-full border border-[#0086DA]/15 bg-white/70 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-[#0086DA] shadow-sm backdrop-blur">
            Patient Portal
        </span>
        <h1 class="text-3xl lg:text-4xl font-bold text-slate-900">My Profile</h1>
        <p class="max-w-2xl text-sm text-slate-600">Manage your account, personal profile, emergency contact, avatar, and password from one page.</p>
    </div>

    @if (session('success'))
        <div class="relative mb-6 rounded-2xl border border-emerald-200/80 bg-white/80 px-4 py-3 text-sm font-medium text-emerald-700 shadow-sm backdrop-blur">
            {{ session('success') }}
        </div>
    @endif

    @if (session('failed'))
        <div class="relative mb-6 rounded-2xl border border-rose-200/80 bg-white/80 px-4 py-3 text-sm font-medium text-rose-700 shadow-sm backdrop-blur">
            {{ session('failed') }}
        </div>
    @endif

    @if ($errors->has('profile_picture'))
        <div class="relative mb-6 rounded-2xl border border-rose-200/80 bg-white/80 px-4 py-3 text-sm font-medium text-rose-700 shadow-sm backdrop-blur">
            {{ $errors->first('profile_picture') }}
        </div>
    @endif

    <section class="relative mb-6 overflow-hidden rounded-[28px] border border-[#0086DA]/20 bg-[linear-gradient(135deg,#0086DA_0%,#1493ea_38%,#57b8ff_100%)] p-6 shadow-[0_24px_70px_-30px_rgba(0,134,218,0.65)] xl:p-8">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(255,255,255,0.22),transparent_34%),radial-gradient(circle_at_bottom_right,_rgba(255,255,255,0.16),transparent_30%)]"></div>
        <div class="flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                <form id="patient-avatar-form" action="{{ route('profile.picture.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" id="patient-avatar-input" name="profile_picture" accept="image/jpeg,image/png,image/gif,image/webp" class="hidden">
                    <button type="button" id="patient-avatar-trigger"
                        class="group relative flex h-24 w-24 items-center justify-center overflow-hidden rounded-[22px] bg-[linear-gradient(135deg,#0086DA,#5ab2ff)] text-2xl font-bold text-white shadow-lg shadow-[#0086DA]/25 ring-4 ring-white/90 transition hover:scale-[1.02]">
                        @if ($profilePictureUrl)
                            <img id="patient-avatar-preview" src="{{ $profilePictureUrl }}" alt="{{ $displayName }} profile picture"
                                class="h-full w-full object-cover transition duration-200 group-hover:scale-105">
                        @else
                            <span id="patient-avatar-fallback">{{ strtoupper(substr($displayName, 0, 1)) }}</span>
                        @endif
                    </button>
                </form>

                <div class="relative min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex rounded-full border border-white/30 bg-white/20 px-3 py-1 text-xs font-semibold text-white backdrop-blur">Patient Account</span>
                        @if ($patientCode)
                            <span class="inline-flex rounded-full border border-white/30 bg-white/20 px-3 py-1 text-xs font-semibold text-white backdrop-blur">{{ $patientCode }}</span>
                        @endif
                        @if (!empty($user->google_id))
                            <span class="inline-flex rounded-full border border-emerald-200/70 bg-emerald-50/95 px-3 py-1 text-xs font-semibold text-emerald-700">Google Login</span>
                        @endif
                    </div>
                    <h2 class="mt-3 text-2xl font-bold text-white lg:text-3xl">{{ $displayName }}</h2>
                    <p class="mt-1 text-sm text-blue-50/90">{{ $user->email }}</p>
                    <p class="mt-3 text-xs font-medium uppercase tracking-[0.18em] text-blue-100/90">Click the avatar to update your photo</p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3 xl:min-w-[460px]">
                <div class="rounded-2xl border border-white/20 bg-white/16 p-4 shadow-sm backdrop-blur-md">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-100">Member Since</p>
                    <p class="mt-2 text-lg font-bold text-white">{{ \Carbon\Carbon::parse($user->created_at)->format('M Y') }}</p>
                </div>
                <div class="rounded-2xl border border-white/20 bg-white/16 p-4 shadow-sm backdrop-blur-md">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-100">Birth Date</p>
                    <p class="mt-2 text-lg font-bold text-white">{{ data_get($patient, 'birth_date') ? \Carbon\Carbon::parse($patient->birth_date)->format('M d, Y') : 'N/A' }}</p>
                </div>
                <div class="rounded-2xl border border-white/20 bg-white/16 p-4 shadow-sm backdrop-blur-md">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-100">Mobile</p>
                    <p class="mt-2 text-lg font-bold text-white">{{ data_get($patient, 'mobile_number') ?: ($user->contact ?? 'N/A') }}</p>
                </div>
            </div>
        </div>
    </section>

    @if (!$patient)
        <section class="mb-6 rounded-[26px] border border-white/70 bg-white/88 p-6 shadow-[0_16px_50px_-30px_rgba(15,23,42,0.35)] backdrop-blur">
            <h2 class="text-lg font-bold text-slate-900">Patient profile not linked</h2>
            <p class="mt-2 text-sm text-slate-600">We could not find a patient record linked to your account. You can still update your avatar and login details here.</p>
        </section>
    @endif

    <div class="relative grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <section class="rounded-[26px] border border-white/70 bg-white/88 p-6 shadow-[0_16px_50px_-30px_rgba(15,23,42,0.35)] backdrop-blur">
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-slate-900">Profile Information</h2>
                    <p class="mt-1 text-sm text-slate-600">View and edit your patient details on the same page.</p>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label for="username" class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Username</label>
                            <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}"
                                class="w-full rounded-2xl border border-sky-100 bg-sky-50/70 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#0086DA] focus:bg-white focus:ring-4 focus:ring-sky-100">
                            @error('username')
                                <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="patient_contact" class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Contact Number</label>
                            <input type="text" id="patient_contact" name="contact" value="{{ old('contact', data_get($patient, 'mobile_number', $user->contact)) }}"
                                inputmode="numeric" pattern="[0-9]*" autocomplete="tel"
                                class="w-full rounded-2xl border border-sky-100 bg-sky-50/70 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#0086DA] focus:bg-white focus:ring-4 focus:ring-sky-100">
                            @error('contact')
                                <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label for="birth_date" class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Birth Date</label>
                            <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', data_get($patient, 'birth_date')) }}"
                                class="w-full rounded-2xl border border-sky-100 bg-sky-50/70 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#0086DA] focus:bg-white focus:ring-4 focus:ring-sky-100">
                            @error('birth_date')
                                <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="gender" class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Gender</label>
                            <select id="gender" name="gender"
                                class="w-full rounded-2xl border border-sky-100 bg-sky-50/70 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#0086DA] focus:bg-white focus:ring-4 focus:ring-sky-100">
                                <option value="">Select gender</option>
                                <option value="Male" @selected(old('gender', data_get($patient, 'gender')) === 'Male')>Male</option>
                                <option value="Female" @selected(old('gender', data_get($patient, 'gender')) === 'Female')>Female</option>
                                <option value="Other" @selected(old('gender', data_get($patient, 'gender')) === 'Other')>Other</option>
                            </select>
                            @error('gender')
                                <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="home_address" class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Home Address</label>
                        <textarea id="home_address" name="home_address" rows="3"
                            class="w-full rounded-2xl border border-sky-100 bg-sky-50/70 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#0086DA] focus:bg-white focus:ring-4 focus:ring-sky-100">{{ old('home_address', data_get($patient, 'home_address')) }}</textarea>
                        @error('home_address')
                            <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="rounded-3xl border border-sky-100 bg-sky-50/40 p-5">
                        <div class="mb-5">
                            <h3 class="text-sm font-bold uppercase tracking-[0.18em] text-slate-700">Emergency Contact</h3>
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <label for="emergency_contact_name" class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Contact Name</label>
                                <input type="text" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name', data_get($patient, 'emergency_contact_name')) }}"
                                    class="w-full rounded-2xl border border-sky-100 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#0086DA] focus:ring-4 focus:ring-sky-100">
                                @error('emergency_contact_name')
                                    <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="emergency_contact_number" class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Contact Number</label>
                                <input type="text" id="emergency_contact_number" name="emergency_contact_number" value="{{ old('emergency_contact_number', data_get($patient, 'emergency_contact_number')) }}"
                                    inputmode="numeric" pattern="[0-9]*" autocomplete="tel"
                                    class="w-full rounded-2xl border border-sky-100 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#0086DA] focus:ring-4 focus:ring-sky-100">
                                @error('emergency_contact_number')
                                    <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="relationship" class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Relationship</label>
                            <input type="text" id="relationship" name="relationship" value="{{ old('relationship', data_get($patient, 'relationship')) }}"
                                class="w-full rounded-2xl border border-sky-100 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#0086DA] focus:ring-4 focus:ring-sky-100">
                            @error('relationship')
                                <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-sky-100 pt-6 sm:flex-row sm:justify-end">
                        <button type="reset" class="inline-flex items-center justify-center rounded-2xl border border-sky-100 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:border-sky-200 hover:bg-sky-50">
                            Reset
                        </button>
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-[#0086DA] px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-[#0086DA]/25 transition hover:bg-[#0077c2]">
                            Save Changes
                        </button>
                    </div>
                </form>
            </section>

            <section class="rounded-[26px] border border-white/70 bg-white/88 p-6 shadow-[0_16px_50px_-30px_rgba(15,23,42,0.35)] backdrop-blur">
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-slate-900">Security &amp; Password</h2>
                    <p class="mt-1 text-sm text-slate-600">Manage your account security and password settings.</p>
                </div>

                @if (!empty($isGoogleUser))
                    <div class="rounded-2xl border border-sky-100 bg-sky-50/80 p-4">
                        <p class="text-sm font-semibold text-blue-900">Google Sign-In Active</p>
                        <p class="mt-1 text-sm text-blue-700">Your account is linked to Google Login. Use the action below to set a password.</p>
                        <form action="{{ route('profile.password.reset-link') }}" method="POST" class="mt-4">
                            @csrf
                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-[#0086DA] px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-[#0086DA]/25 transition hover:bg-[#0077c2]">
                                Send Password Reset Link
                            </button>
                        </form>
                    </div>
                @else
                    <form action="{{ route('profile.password') }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="patient_current_password" class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Current Password</label>
                            <div class="relative">
                                <input type="password" id="patient_current_password" name="current_password" class="w-full rounded-2xl border border-sky-100 bg-sky-50/70 px-4 py-3 pr-12 text-sm text-slate-900 outline-none transition focus:border-[#0086DA] focus:bg-white focus:ring-4 focus:ring-sky-100">
                                <button type="button" data-password-toggle="patient_current_password" class="absolute inset-y-0 right-3 inline-flex items-center text-slate-400 transition hover:text-slate-600" aria-label="Toggle current password visibility">
                                    <span data-password-icon="show">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </span>
                                    <span data-password-icon="hide" class="hidden">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 3 18 18" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.58 10.58A2 2 0 0 0 12 14a2 2 0 0 0 1.42-.58" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.88 5.09A9.77 9.77 0 0 1 12 5c4.48 0 8.27 2.94 9.54 7a9.96 9.96 0 0 1-4.04 5.02" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6.23 6.23A9.96 9.96 0 0 0 2.46 12A9.97 9.97 0 0 0 12 19c1.61 0 3.13-.38 4.46-1.06" />
                                        </svg>
                                    </span>
                                </button>
                            </div>
                            @error('current_password')
                                <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="patient_password" class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">New Password</label>
                            <div class="relative">
                                <input type="password" id="patient_password" name="password" class="w-full rounded-2xl border border-sky-100 bg-sky-50/70 px-4 py-3 pr-12 text-sm text-slate-900 outline-none transition focus:border-[#0086DA] focus:bg-white focus:ring-4 focus:ring-sky-100">
                                <button type="button" data-password-toggle="patient_password" class="absolute inset-y-0 right-3 inline-flex items-center text-slate-400 transition hover:text-slate-600" aria-label="Toggle new password visibility">
                                    <span data-password-icon="show">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </span>
                                    <span data-password-icon="hide" class="hidden">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 3 18 18" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.58 10.58A2 2 0 0 0 12 14a2 2 0 0 0 1.42-.58" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.88 5.09A9.77 9.77 0 0 1 12 5c4.48 0 8.27 2.94 9.54 7a9.96 9.96 0 0 1-4.04 5.02" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6.23 6.23A9.96 9.96 0 0 0 2.46 12A9.97 9.97 0 0 0 12 19c1.61 0 3.13-.38 4.46-1.06" />
                                        </svg>
                                    </span>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="patient_password_confirmation" class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Confirm Password</label>
                            <div class="relative">
                                <input type="password" id="patient_password_confirmation" name="password_confirmation" class="w-full rounded-2xl border border-sky-100 bg-sky-50/70 px-4 py-3 pr-12 text-sm text-slate-900 outline-none transition focus:border-[#0086DA] focus:bg-white focus:ring-4 focus:ring-sky-100">
                                <button type="button" data-password-toggle="patient_password_confirmation" class="absolute inset-y-0 right-3 inline-flex items-center text-slate-400 transition hover:text-slate-600" aria-label="Toggle password confirmation visibility">
                                    <span data-password-icon="show">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </span>
                                    <span data-password-icon="hide" class="hidden">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 3 18 18" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.58 10.58A2 2 0 0 0 12 14a2 2 0 0 0 1.42-.58" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.88 5.09A9.77 9.77 0 0 1 12 5c4.48 0 8.27 2.94 9.54 7a9.96 9.96 0 0 1-4.04 5.02" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6.23 6.23A9.96 9.96 0 0 0 2.46 12A9.97 9.97 0 0 0 12 19c1.61 0 3.13-.38 4.46-1.06" />
                                        </svg>
                                    </span>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end border-t border-sky-100 pt-6">
                            <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-[#0086DA] px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-[#0086DA]/25 transition hover:bg-[#0077c2]">
                                Update Password
                            </button>
                        </div>
                    </form>
                @endif
            </section>
        </div>

        <aside class="space-y-6">
            <section class="rounded-[26px] border border-white/70 bg-white/88 p-6 shadow-[0_16px_50px_-30px_rgba(15,23,42,0.35)] backdrop-blur">
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-slate-900">Account Overview</h2>
                    <p class="mt-1 text-sm text-slate-600">Reference details for your account.</p>
                </div>

                <div class="space-y-3">
                    <div class="rounded-2xl border border-sky-100 bg-[linear-gradient(135deg,rgba(239,246,255,0.92),rgba(255,255,255,0.9))] p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Patient ID</p>
                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ $patientCode ?? 'Not linked' }}</p>
                    </div>
                    <div class="rounded-2xl border border-sky-100 bg-[linear-gradient(135deg,rgba(239,246,255,0.92),rgba(255,255,255,0.9))] p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Full Name</p>
                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ $displayName }}</p>
                    </div>
                    <div class="rounded-2xl border border-sky-100 bg-[linear-gradient(135deg,rgba(239,246,255,0.92),rgba(255,255,255,0.9))] p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Email</p>
                        <p class="mt-2 break-all text-sm font-semibold text-slate-900">{{ data_get($patient, 'email_address', $user->email ?? 'N/A') }}</p>
                    </div>
                    <div class="rounded-2xl border border-sky-100 bg-[linear-gradient(135deg,rgba(239,246,255,0.92),rgba(255,255,255,0.9))] p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Gender</p>
                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ data_get($patient, 'gender', 'N/A') }}</p>
                    </div>
                    <div class="rounded-2xl border border-sky-100 bg-[linear-gradient(135deg,rgba(239,246,255,0.92),rgba(255,255,255,0.9))] p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Emergency Contact</p>
                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ data_get($patient, 'emergency_contact_name', 'N/A') }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ data_get($patient, 'relationship', 'No relationship set') }}</p>
                    </div>
                </div>
            </section>

            <section class="rounded-[26px] border border-red-200/80 bg-white/88 p-6 shadow-[0_16px_50px_-30px_rgba(15,23,42,0.35)] backdrop-blur">
                <div class="mb-5">
                    <h2 class="text-lg font-bold text-slate-900">Delete Account</h2>
                    <p class="mt-1 text-sm text-slate-600">This permanently removes your portal access.</p>
                </div>

                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    This action cannot be undone.
                </div>

                <div class="mt-5 space-y-4">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" id="accept-terms-checkbox" class="mt-1 h-4 w-4 rounded border-red-300 text-red-600 focus:ring-red-500">
                        <span class="text-sm leading-6 text-slate-700">I understand that deleting my account is permanent.</span>
                    </label>

                    <div>
                        <label for="delete-confirmation-input" class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-600">Final Confirmation</label>
                        <input type="text" id="delete-confirmation-input" placeholder="Type DELETE ACCOUNT to confirm"
                            class="mt-3 w-full rounded-xl border border-red-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition">
                    </div>

                    <button id="delete-account-btn" type="button" disabled
                        class="inline-flex w-full items-center justify-center rounded-xl bg-red-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:bg-slate-300"
                        onclick="openDeleteModal()">
                        Delete Account Permanently
                    </button>
                </div>
            </section>
        </aside>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const avatarInput = document.getElementById('patient-avatar-input');
        const avatarTrigger = document.getElementById('patient-avatar-trigger');
        const avatarForm = document.getElementById('patient-avatar-form');
        const avatarPreview = document.getElementById('patient-avatar-preview');
        const avatarFallback = document.getElementById('patient-avatar-fallback');
        const patientContactInput = document.getElementById('patient_contact');
        const emergencyContactInput = document.getElementById('emergency_contact_number');
        const passwordToggleButtons = document.querySelectorAll('[data-password-toggle]');
        const acceptCheckbox = document.getElementById('accept-terms-checkbox');
        const deleteInput = document.getElementById('delete-confirmation-input');
        const deleteBtn = document.getElementById('delete-account-btn');

        if (avatarTrigger && avatarInput) {
            avatarTrigger.addEventListener('click', function() {
                avatarInput.click();
            });

            avatarInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file || !file.type.startsWith('image/')) {
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(event) {
                    if (avatarPreview) {
                        avatarPreview.src = event.target.result;
                    } else if (avatarFallback) {
                        avatarFallback.outerHTML = '<img id="patient-avatar-preview" src="' + event.target.result + '" alt="Profile picture preview" class="h-full w-full object-cover transition duration-200 group-hover:scale-105">';
                    }

                    avatarForm.submit();
                };

                reader.readAsDataURL(file);
            });
        }

        [patientContactInput, emergencyContactInput].forEach((input) => {
            if (!input) {
                return;
            }

            input.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '');
            });
        });

        passwordToggleButtons.forEach((button) => {
            button.addEventListener('click', function() {
                const input = document.getElementById(this.dataset.passwordToggle);
                if (!input) {
                    return;
                }

                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                this.querySelector('[data-password-icon="show"]')?.classList.toggle('hidden', isPassword);
                this.querySelector('[data-password-icon="hide"]')?.classList.toggle('hidden', !isPassword);
            });
        });

        function validateDeleteButton() {
            if (!acceptCheckbox || !deleteInput || !deleteBtn) {
                return;
            }

            deleteBtn.disabled = !(acceptCheckbox.checked && deleteInput.value === 'DELETE ACCOUNT');
        }

        acceptCheckbox?.addEventListener('change', validateDeleteButton);
        deleteInput?.addEventListener('input', validateDeleteButton);
    });

    function deleteAccount() {
        document.getElementById('delete-account-form')?.submit();
    }

    function openDeleteModal() {
        document.getElementById('delete-account-modal')?.showModal();
    }

    function closeDeleteModal() {
        document.getElementById('delete-account-modal')?.close();
    }

    document.addEventListener('click', function(e) {
        const deleteModal = document.getElementById('delete-account-modal');
        if (e.target === deleteModal) {
            deleteModal.close();
        }
    });
</script>

<dialog id="delete-account-modal" class="w-full max-w-lg rounded-3xl p-0 shadow-2xl backdrop:bg-slate-950/40 backdrop:backdrop-blur-sm">
    <div class="overflow-hidden rounded-3xl border border-red-200 bg-white">
        <div class="border-b border-red-100 bg-red-50 px-6 py-5">
            <h3 class="text-lg font-bold text-slate-900">Confirm Account Deletion</h3>
            <p class="mt-1 text-sm text-slate-600">This is your final confirmation before your patient portal account is permanently removed.</p>
        </div>

        <div class="space-y-4 px-6 py-5">
            <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm leading-6 text-red-900">
                By continuing, you confirm that you understand this action is irreversible and that your portal access and associated account access will be deleted.
            </div>
        </div>

        <div class="flex flex-col-reverse gap-3 border-t border-slate-200 px-6 py-5 sm:flex-row sm:justify-end">
            <button type="button" onclick="closeDeleteModal()"
                class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                Cancel
            </button>
            <form id="delete-account-form" action="{{ route('profile.delete') }}" method="POST">
                @csrf
                <button type="button" onclick="deleteAccount()"
                    class="inline-flex items-center justify-center rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700">
                    Yes, Delete My Account
                </button>
            </form>
        </div>
    </div>
</dialog>

@endsection
