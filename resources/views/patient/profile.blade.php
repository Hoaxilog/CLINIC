<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account – Tejada Clinic</title>
    <meta name="theme-color" content="#0086DA">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.svg') }}">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    @livewireStyles
</head>

<body class="bg-[#f6fafd] min-h-screen">
    @include('components.homepage.header-section', ['patientMinimalHeader' => true])

    @php
        $accountName = $requesterDisplayName ?? 'Patient';
        $memberSince = !empty($user->created_at)
            ? \Carbon\Carbon::parse($user->created_at)->format('M Y')
            : 'Recently';
    @endphp

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400&display=swap');
        #profile-wrap * { font-family: 'Montserrat', sans-serif; }
    </style>

    <main id="profile-wrap" class="min-h-screen bg-[#f6fafd] px-6 py-8 md:px-12 xl:px-20"
        style="font-family:'Montserrat',sans-serif; -webkit-font-smoothing:antialiased;">
        <div class="mx-auto flex w-full max-w-[1400px] flex-col gap-7">

            {{-- Flash messages --}}
            @if (session('success'))
                <div class="flex items-center gap-3 border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm text-emerald-800">
                    <svg class="h-4 w-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="square" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if (session('failed'))
                <div class="flex items-center gap-3 border border-red-200 bg-red-50 px-5 py-3 text-sm text-red-700">
                    <svg class="h-4 w-4 shrink-0 text-red-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="square" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('failed') }}
                </div>
            @endif

            {{-- Page banner --}}
            <div class="border border-[#e4eff8] bg-white">
                <div class="px-6 py-6 md:px-8">
                    <a href="{{ route('patient.dashboard') }}"
                        class="mb-3 inline-flex items-center gap-[7px] text-[.68rem] font-bold uppercase tracking-[.12em] text-[#7a9db5] no-underline transition hover:text-[#0086da]">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="square">
                            <path d="M19 12H5M12 5l-7 7 7 7" />
                        </svg>
                        Back to Dashboard
                    </a>
                    <h1 class="text-[1.35rem] font-extrabold leading-[1.1] tracking-[-.02em] text-[#1a2e3b]">My Account</h1>
                    <p class="mt-1 text-[.8rem] text-[#7a9db5]">Manage your login credentials and account details.</p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1.6fr_.9fr]">

                {{-- ── Left: Account Info + Identity ── --}}
                <div class="space-y-6">

                    {{-- Account Identity --}}
                    <article x-data="{ editing: {{ $errors->any() ? 'true' : 'false' }} }"
                        class="border border-[#e4eff8] bg-white shadow-[0_20px_48px_rgba(0,134,218,.07)]">
                        {{-- Card header --}}
                        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-[#e4eff8] px-6 py-5 sm:px-8">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center bg-[#0086da]">
                                    <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="square" d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                                        <path stroke-linecap="square" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-[.6rem] font-bold uppercase tracking-[.2em] text-[#0086da]">Details</div>
                                    <div class="text-[.95rem] font-extrabold tracking-[-0.01em] text-[#1a2e3b]">Account Identity</div>
                                </div>
                            </div>
                            <button type="button" x-show="!editing" x-cloak @click="editing = true"
                                class="inline-flex items-center gap-2 border border-[#0086da] bg-white px-5 py-[10px] text-[.7rem] font-bold uppercase tracking-[.1em] text-[#0086da] transition hover:bg-[#f0f8fe]">
                                <svg class="h-[12px] w-[12px]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="square" d="M12 20h9" />
                                    <path stroke-linecap="square" d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4 12.5-12.5z" />
                                </svg>
                                Edit
                            </button>
                        </div>

                        <form x-ref="identityForm" action="{{ route('profile.update') }}" method="POST" class="px-6 py-6 sm:px-8">
                            @csrf
                            @method('PATCH')

                            <div class="grid gap-5 sm:grid-cols-2">
                                <div class="space-y-2">
                                    <label class="text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e]">First Name</label>
                                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}"
                                        x-bind:readonly="!editing" x-bind:class="editing ? 'bg-white' : 'bg-[#f6fafd] cursor-not-allowed'"
                                        class="w-full border border-[#d4e8f5] px-4 py-3 text-sm text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#cde8fb] @error('first_name') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror">
                                    @error('first_name')
                                        <span class="text-[.72rem] font-bold text-red-500">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e]">Last Name</label>
                                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}"
                                        x-bind:readonly="!editing" x-bind:class="editing ? 'bg-white' : 'bg-[#f6fafd] cursor-not-allowed'"
                                        class="w-full border border-[#d4e8f5] px-4 py-3 text-sm text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#cde8fb] @error('last_name') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror">
                                    @error('last_name')
                                        <span class="text-[.72rem] font-bold text-red-500">{{ $message }}</span>
                                    @enderror
                                </div>

                                @if ($hasMiddleNameColumn)
                                    <div class="space-y-2">
                                        <label class="text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e]">Middle Name</label>
                                        <input type="text" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}"
                                            x-bind:readonly="!editing" x-bind:class="editing ? 'bg-white' : 'bg-[#f6fafd] cursor-not-allowed'"
                                            class="w-full border border-[#d4e8f5] px-4 py-3 text-sm text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#cde8fb] @error('middle_name') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror">
                                        @error('middle_name')
                                            <span class="text-[.72rem] font-bold text-red-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif

                                <div class="space-y-2 {{ $hasMiddleNameColumn ? '' : 'sm:col-span-2' }}">
                                    <label class="text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e]">Mobile Number</label>
                                    <div class="flex">
                                        <span class="inline-flex items-center border border-r-0 border-[#d4e8f5] bg-[#f0f8fe] px-4 text-sm font-semibold text-[#3d5a6e]">+63</span>
                                        <input type="text" name="mobile_number" inputmode="numeric" maxlength="10"
                                            oninput="this.value = this.value.replace(/\D/g, '').replace(/^0+/, '').slice(0, 10)"
                                            value="{{ old('mobile_number', $user->mobile_number) }}"
                                            x-bind:readonly="!editing" x-bind:class="editing ? 'bg-white' : 'bg-[#f6fafd] cursor-not-allowed'"
                                            class="w-full min-w-0 border border-[#d4e8f5] px-4 py-3 text-sm text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#cde8fb] @error('mobile_number') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror">
                                    </div>
                                    @error('mobile_number')
                                        <span class="text-[.72rem] font-bold text-red-500">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="space-y-2 sm:col-span-2">
                                    <label class="text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e]">Email Address</label>
                                    <input type="text" value="{{ $user->email ?? 'N/A' }}" readonly
                                        class="w-full border border-[#e4eff8] bg-[#f6fafd] px-4 py-3 text-sm text-[#1a2e3b] outline-none cursor-not-allowed">
                                    <p class="text-[.72rem] leading-[1.6] text-[#7a9db5]">Your login email is connected to your account and cannot be edited here.</p>
                                </div>
                            </div>

                            <div x-show="editing" x-cloak class="mt-6 flex flex-wrap items-center justify-between gap-4 border-t border-[#e4eff8] pt-5">
                                <p class="text-[.78rem] leading-[1.6] text-[#7a9db5]">You can edit your name and mobile number here. Email stays locked for account access.</p>
                                <div class="flex flex-wrap items-center gap-2">
                                    <button type="button" @click="$refs.identityForm.reset(); editing = false"
                                        class="inline-flex items-center gap-2 border border-[#d4e8f5] bg-white px-5 py-[11px] text-[.7rem] font-bold uppercase tracking-[.1em] text-[#587189] transition hover:bg-[#f6fafd]">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 bg-[#0086da] px-6 py-[11px] text-[.7rem] font-bold uppercase tracking-[.1em] text-white transition hover:-translate-y-px hover:bg-[#006ab0]">
                                        <svg class="h-[12px] w-[12px]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="square" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Save Changes
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="px-6 pb-6 sm:px-8">
                            <div class="flex items-start gap-3 border border-[#d4e8f5] bg-[#f6fafd] px-5 py-4">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-[#0086da]/60" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <path stroke-linecap="square" d="M12 8v4M12 16h.01" />
                                </svg>
                                <p class="text-[.82rem] leading-[1.7] text-[#587189]">
                                    Member since <span class="font-semibold text-[#1a2e3b]">{{ $memberSince }}</span>
                                </p>
                            </div>
                        </div>
                    </article>

                </div>

                {{-- ── Right: Password ── --}}
                <aside>
                    <article class="border border-[#e4eff8] bg-white shadow-[0_20px_48px_rgba(0,134,218,.07)]">
                        {{-- Card header --}}
                        <div class="flex items-center gap-3 border-b border-[#e4eff8] px-6 py-5">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center bg-[#0086da]">
                                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <rect x="3" y="11" width="18" height="11" stroke-linecap="square" />
                                    <path stroke-linecap="square" d="M7 11V7a5 5 0 0110 0v4" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-[.6rem] font-bold uppercase tracking-[.2em] text-[#0086da]">Security</div>
                                <div class="text-[.95rem] font-extrabold tracking-[-0.01em] text-[#1a2e3b]">Password</div>
                            </div>
                        </div>

                        <div class="px-6 py-6">
                            @if (!empty($isGoogleUser))
                                <p class="text-[.88rem] leading-[1.75] text-[#587189]">Your account uses Google Login. We'll email you a secure link to set a password.</p>
                                <form action="{{ route('profile.password.reset-link') }}" method="POST" class="mt-5">
                                    @csrf
                                    <button type="button"
                                        onclick="this.disabled=true; this.classList.add('opacity-60','cursor-not-allowed'); this.form.submit();"
                                        class="inline-flex items-center gap-[9px] whitespace-nowrap border border-[#0086da] px-6 py-[11px] text-[.7rem] font-bold uppercase tracking-[.1em] text-[#0086da] transition hover:bg-[#0086da] hover:text-white">
                                        Send Reset Link
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('profile.password') }}" method="POST" class="space-y-5">
                                    @csrf
                                    @method('PUT')

                                    <div class="space-y-2">
                                        <label class="text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e]">Current Password</label>
                                        <input type="password" name="current_password" placeholder="Current password" autocomplete="current-password"
                                            class="w-full border border-[#d4e8f5] bg-white px-4 py-3 text-sm text-[#1a2e3b] placeholder:text-[#9bbdd0] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#cde8fb] @error('current_password') border-red-400 focus:ring-red-200 focus:border-red-500 @enderror">
                                        @error('current_password')
                                            <span class="text-xs text-red-500 font-bold">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e]">New Password</label>
                                        <input type="password" name="password" placeholder="New password" autocomplete="new-password"
                                            class="w-full border border-[#d4e8f5] bg-white px-4 py-3 text-sm text-[#1a2e3b] placeholder:text-[#9bbdd0] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#cde8fb] @error('password') border-red-400 focus:ring-red-200 focus:border-red-500 @enderror">
                                        @error('password')
                                            <span class="text-xs text-red-500 font-bold">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e]">Confirm Password</label>
                                        <input type="password" name="password_confirmation" placeholder="Confirm new password" autocomplete="new-password"
                                            class="w-full border border-[#d4e8f5] bg-white px-4 py-3 text-sm text-[#1a2e3b] placeholder:text-[#9bbdd0] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#cde8fb]">
                                    </div>

                                    <div class="pt-1">
                                        <button type="submit"
                                            class="inline-flex items-center gap-2 bg-[#0086da] hover:bg-[#006ab0] px-6 py-[11px] text-[.7rem] font-bold uppercase tracking-[.1em] text-white transition hover:-translate-y-px">
                                            <svg class="h-[12px] w-[12px]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="square" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Update Password
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </article>
                </aside>

            </div>
        </div>
    </main>

    @include('components.homepage.footer-section')
    @include('components.homepage.scripts-section')
    @livewireScripts
</body>

</html>
