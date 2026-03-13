@extends('index')

@section('content')
    @php
        $isPatient = auth()->check() && (int) auth()->user()->role === 3;
        $roleLabel = ucfirst(['Admin', 'Staff', 'Patient'][$user->role - 1] ?? 'User');
        $profilePictureUrl = !empty($user->profile_picture)
            ? asset('storage/' . $user->profile_picture) . '?v=' . urlencode((string) strtotime((string) $user->updated_at))
            : null;
        $displayName = $user->username ?: 'User';
    @endphp

    <main id="mainContent"
        class="{{ $isPatient ? 'min-h-screen overflow-hidden bg-[#f4f8fb] p-4 sm:p-6 lg:p-8' : 'min-h-screen overflow-hidden bg-[#f4f8fb] p-6 lg:p-8 ml-64 mt-14 transition-all duration-300 peer-[.collapsed]:ml-16' }}">
        <div class="relative mb-8 flex flex-col gap-2">
            <span class="inline-flex w-fit rounded-full border border-[#0086DA]/15 bg-white/70 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-[#0086DA] shadow-sm backdrop-blur">
                Account Center
            </span>
            <h1 class="text-3xl lg:text-4xl font-bold text-slate-900">Profile</h1>
            <p class="max-w-2xl text-sm text-slate-600">Manage your account details, password, and profile picture inside the Tejada Dent system.</p>
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
            <div class="pointer-events-none absolute inset-y-0 right-0 hidden w-80 bg-[radial-gradient(circle_at_center,_rgba(255,255,255,0.18),transparent_68%)] xl:block"></div>
            <div class="flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                    <form id="avatar-form" action="{{ route('profile.picture.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" id="avatar-input" name="profile_picture" accept="image/jpeg,image/png,image/gif,image/webp" class="hidden">
                        <button type="button" id="avatar-trigger"
                            class="group relative flex h-24 w-24 items-center justify-center overflow-hidden rounded-[22px] bg-[linear-gradient(135deg,#0086DA,#5ab2ff)] text-2xl font-bold text-white shadow-lg shadow-[#0086DA]/25 ring-4 ring-white/90 transition hover:scale-[1.02]">
                            @if ($profilePictureUrl)
                                <img id="avatar-preview" src="{{ $profilePictureUrl }}" alt="{{ $displayName }} profile picture"
                                    class="h-full w-full object-cover transition duration-200 group-hover:scale-105">
                            @else
                                <span id="avatar-fallback">{{ strtoupper(substr($displayName, 0, 1)) }}</span>
                            @endif
                        </button>
                    </form>

                    <div class="relative min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex rounded-full border border-white/30 bg-white/20 px-3 py-1 text-xs font-semibold text-white backdrop-blur">
                                {{ $roleLabel }}
                            </span>
                            @if (!empty($user->google_id))
                                <span class="inline-flex rounded-full border border-emerald-200/70 bg-emerald-50/95 px-3 py-1 text-xs font-semibold text-emerald-700">
                                    Google Login
                                </span>
                            @endif
                        </div>
                        <h2 class="mt-3 text-2xl font-bold text-white lg:text-3xl">{{ $displayName }}</h2>
                        <p class="mt-1 text-sm text-blue-50/90">{{ $user->email }}</p>
                        <p class="mt-3 text-xs font-medium uppercase tracking-[0.18em] text-blue-100/90">Click the avatar to update your photo</p>
                    </div>
                </div>

                <div class="relative grid gap-4 sm:grid-cols-3 xl:min-w-[420px]">
                    <div class="rounded-2xl border border-white/20 bg-white/16 p-4 shadow-sm backdrop-blur-md">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-100">Member Since</p>
                        <p class="mt-2 text-lg font-bold text-white">{{ \Carbon\Carbon::parse($user->created_at)->format('M Y') }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/20 bg-white/16 p-4 shadow-sm backdrop-blur-md">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-100">Position</p>
                        <p class="mt-2 text-lg font-bold text-white">{{ $user->position ?? 'N/A' }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/20 bg-white/16 p-4 shadow-sm backdrop-blur-md">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-100">Contact</p>
                        <p class="mt-2 text-lg font-bold text-white">{{ $user->contact ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </section>

        <div class="relative grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="space-y-6 xl:col-span-2">
                <section class="rounded-[26px] border border-white/70 bg-white/88 p-6 shadow-[0_16px_50px_-30px_rgba(15,23,42,0.35)] backdrop-blur">
                    <div class="mb-6">
                        <h2 class="text-lg font-bold text-slate-900">Profile Information</h2>
                        <p class="mt-1 text-sm text-slate-600">Update your account details and professional information.</p>
                    </div>

                    <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <label for="username" class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Username</label>
                                <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}"
                                    placeholder="Enter your username"
                                    class="w-full rounded-2xl border border-sky-100 bg-sky-50/70 px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#0086DA] focus:bg-white focus:ring-4 focus:ring-sky-100">
                                @error('username')
                                    <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="contact" class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Contact Number</label>
                                <input type="text" id="contact" name="contact" value="{{ old('contact', $user->contact ?? '') }}"
                                    placeholder="09XXXXXXXXXX"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    autocomplete="tel"
                                    class="w-full rounded-2xl border border-sky-100 bg-sky-50/70 px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#0086DA] focus:bg-white focus:ring-4 focus:ring-sky-100">
                                @error('contact')
                                    <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="position" class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Position / Title</label>
                            <input type="text" id="position" name="position" value="{{ old('position', $user->position ?? '') }}"
                                placeholder="e.g. Dentist, Administrator, Manager"
                                class="w-full rounded-2xl border border-sky-100 bg-sky-50/70 px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#0086DA] focus:bg-white focus:ring-4 focus:ring-sky-100">
                            @error('position')
                                <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-col-reverse gap-3 border-t border-sky-100 pt-6 sm:flex-row sm:justify-end">
                            <button type="reset"
                                class="inline-flex items-center justify-center rounded-2xl border border-sky-100 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:border-sky-200 hover:bg-sky-50">
                                Reset
                            </button>
                            <button type="submit"
                                class="inline-flex items-center justify-center rounded-2xl bg-[#0086DA] px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-[#0086DA]/25 transition hover:bg-[#0077c2]">
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

                    @if (!empty($user->google_id))
                        <div class="rounded-2xl border border-sky-100 bg-sky-50/80 p-4">
                            <p class="text-sm font-semibold text-blue-900">Google Sign-In Active</p>
                            <p class="mt-1 text-sm text-blue-700">Your account is linked to Google Login. Use the action below to set a password.</p>
                            <form action="{{ route('profile.password.reset-link') }}" method="POST" class="mt-4">
                                @csrf
                                <button type="submit"
                                    class="inline-flex w-full items-center justify-center rounded-2xl bg-[#0086DA] px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-[#0086DA]/25 transition hover:bg-[#0077c2]">
                                    Send Password Reset Link
                                </button>
                            </form>
                        </div>
                    @else
                        <form action="{{ route('profile.password') }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div>
                                <label for="current_password" class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Current Password</label>
                                <div class="relative">
                                    <input type="password" id="current_password" name="current_password" placeholder="Enter your current password"
                                        class="w-full rounded-2xl border border-sky-100 bg-sky-50/70 px-4 py-3 pr-12 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#0086DA] focus:bg-white focus:ring-4 focus:ring-sky-100">
                                    <button type="button" data-password-toggle="current_password" class="absolute inset-y-0 right-3 inline-flex items-center text-slate-400 transition hover:text-slate-600" aria-label="Toggle current password visibility">
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
                                <label for="password" class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">New Password</label>
                                <div class="relative">
                                    <input type="password" id="password" name="password" placeholder="Enter new password"
                                        class="w-full rounded-2xl border border-sky-100 bg-sky-50/70 px-4 py-3 pr-12 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#0086DA] focus:bg-white focus:ring-4 focus:ring-sky-100">
                                    <button type="button" data-password-toggle="password" class="absolute inset-y-0 right-3 inline-flex items-center text-slate-400 transition hover:text-slate-600" aria-label="Toggle new password visibility">
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

                                <div id="strength-container" class="mt-3 hidden h-1.5 overflow-hidden rounded-full bg-sky-100">
                                    <div id="strength-bar" class="h-full w-0 rounded-full transition-all duration-300"></div>
                                </div>
                                <p id="strength-text" class="mt-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400"></p>
                            </div>

                            <div>
                                <label for="password_confirmation" class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Confirm Password</label>
                                <div class="relative">
                                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Re-enter your new password"
                                        class="w-full rounded-2xl border border-sky-100 bg-sky-50/70 px-4 py-3 pr-12 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#0086DA] focus:bg-white focus:ring-4 focus:ring-sky-100">
                                    <button type="button" data-password-toggle="password_confirmation" class="absolute inset-y-0 right-3 inline-flex items-center text-slate-400 transition hover:text-slate-600" aria-label="Toggle password confirmation visibility">
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
                                <button type="submit"
                                    class="inline-flex items-center justify-center rounded-2xl bg-[#0086DA] px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-[#0086DA]/25 transition hover:bg-[#0077c2]">
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
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Account Role</p>
                            <p class="mt-2 text-sm font-semibold text-slate-900">{{ $roleLabel }}</p>
                        </div>
                        <div class="rounded-2xl border border-sky-100 bg-[linear-gradient(135deg,rgba(239,246,255,0.92),rgba(255,255,255,0.9))] p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Username</p>
                            <p class="mt-2 text-sm font-semibold text-slate-900">{{ $user->username ?? 'N/A' }}</p>
                        </div>
                        <div class="rounded-2xl border border-sky-100 bg-[linear-gradient(135deg,rgba(239,246,255,0.92),rgba(255,255,255,0.9))] p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Email Address</p>
                            <p class="mt-2 break-all text-sm font-semibold text-slate-900">{{ $user->email ?? 'N/A' }}</p>
                        </div>
                        <div class="rounded-2xl border border-sky-100 bg-[linear-gradient(135deg,rgba(239,246,255,0.92),rgba(255,255,255,0.9))] p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Contact Number</p>
                            <p class="mt-2 text-sm font-semibold text-slate-900">{{ $user->contact ?? 'N/A' }}</p>
                        </div>
                        <div class="rounded-2xl border border-sky-100 bg-[linear-gradient(135deg,rgba(239,246,255,0.92),rgba(255,255,255,0.9))] p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Position</p>
                            <p class="mt-2 text-sm font-semibold text-slate-900">{{ $user->position ?? 'N/A' }}</p>
                        </div>
                        <div class="rounded-2xl border border-sky-100 bg-[linear-gradient(135deg,rgba(239,246,255,0.92),rgba(255,255,255,0.9))] p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Account Created</p>
                            <p class="mt-2 text-sm font-semibold text-slate-900">{{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}</p>
                        </div>
                        <div class="rounded-2xl border border-sky-100 bg-[linear-gradient(135deg,rgba(239,246,255,0.92),rgba(255,255,255,0.9))] p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Last Updated</p>
                            <p class="mt-2 text-sm font-semibold text-slate-900">{{ \Carbon\Carbon::parse($user->updated_at)->format('M d, Y') }}</p>
                        </div>
                        <div class="rounded-2xl border border-sky-100 bg-[linear-gradient(135deg,rgba(239,246,255,0.92),rgba(255,255,255,0.9))] p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Login Method</p>
                            <p class="mt-2 text-sm font-semibold text-slate-900">{{ !empty($user->google_id) ? 'Google Login' : 'Email & Password' }}</p>
                        </div>
                        <div class="rounded-2xl border border-sky-100 bg-[linear-gradient(135deg,rgba(239,246,255,0.92),rgba(255,255,255,0.9))] p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Email Verified</p>
                            <p class="mt-2 text-sm font-semibold text-slate-900">
                                {{ $user->email_verified_at ? \Carbon\Carbon::parse($user->email_verified_at)->format('M d, Y') : 'Not Verified' }}
                            </p>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </main>
@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const avatarInput = document.getElementById('avatar-input');
            const avatarTrigger = document.getElementById('avatar-trigger');
            const avatarForm = document.getElementById('avatar-form');
            const avatarPreview = document.getElementById('avatar-preview');
            const avatarFallback = document.getElementById('avatar-fallback');
            const passwordInput = document.getElementById('password');
            const strengthContainer = document.getElementById('strength-container');
            const strengthBar = document.getElementById('strength-bar');
            const strengthText = document.getElementById('strength-text');
            const contactInput = document.getElementById('contact');
            const passwordToggleButtons = document.querySelectorAll('[data-password-toggle]');

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
                            avatarFallback.outerHTML =
                                '<img id="avatar-preview" src="' + event.target.result + '" alt="Profile picture preview" class="h-full w-full object-cover transition duration-200 group-hover:scale-105">';
                        }

                        avatarForm.submit();
                    };

                    reader.readAsDataURL(file);
                });
            }

            if (contactInput) {
                contactInput.addEventListener('input', function() {
                    this.value = this.value.replace(/\D/g, '');
                });
            }

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

            if (!passwordInput || !strengthContainer || !strengthBar || !strengthText) {
                return;
            }

            passwordInput.addEventListener('input', function() {
                const password = this.value;

                if (password.length === 0) {
                    strengthContainer.classList.add('hidden');
                    strengthBar.style.width = '0';
                    strengthBar.className = 'h-full w-0 rounded-full transition-all duration-300';
                    strengthText.textContent = '';
                    strengthText.className = 'mt-2 text-xs font-semibold uppercase tracking-[0.18em] text-gray-400';
                    return;
                }

                strengthContainer.classList.remove('hidden');

                let strength = 0;
                if (password.length >= 8) strength++;
                if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
                if (/[0-9]/.test(password)) strength++;
                if (/[^a-zA-Z0-9]/.test(password)) strength++;

                strengthBar.className = 'h-full rounded-full transition-all duration-300';

                if (strength <= 1) {
                    strengthBar.style.width = '33%';
                    strengthBar.classList.add('bg-rose-500');
                    strengthText.textContent = 'Weak';
                    strengthText.className = 'mt-2 text-xs font-semibold uppercase tracking-[0.18em] text-rose-500';
                } else if (strength <= 2) {
                    strengthBar.style.width = '66%';
                    strengthBar.classList.add('bg-amber-500');
                    strengthText.textContent = 'Fair';
                    strengthText.className = 'mt-2 text-xs font-semibold uppercase tracking-[0.18em] text-amber-500';
                } else {
                    strengthBar.style.width = '100%';
                    strengthBar.classList.add('bg-emerald-500');
                    strengthText.textContent = 'Strong';
                    strengthText.className = 'mt-2 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-500';
                }
            });
        });
    </script>
@endpush
