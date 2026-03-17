@extends('layouts.app')

@section('content')
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

    <main id="profile-wrap" class="min-h-screen bg-[#f6fafd] px-6 py-8 md:px-12 xl:px-20">
        <div class="mx-auto flex w-full max-w-[1400px] flex-col gap-7">

            @if (session('success'))
                <div class="border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('failed'))
                <div class="border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    {{ session('failed') }}
                </div>
            @endif

            {{-- Page heading --}}
            <div class="border-b border-[#e4eff8] pb-6">
                <a href="{{ route('patient.dashboard') }}"
                    class="mb-4 inline-flex items-center gap-[7px] text-[.68rem] font-bold uppercase tracking-[.12em] text-[#7a9db5] no-underline transition hover:text-[#0086da]">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="square">
                        <path d="M19 12H5M12 5l-7 7 7 7" />
                    </svg>
                    Back to Dashboard
                </a>
                <h1 class="text-[1.7rem] font-extrabold leading-[1.1] tracking-[-.02em] text-[#1a2e3b]">My Account</h1>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1.6fr_.9fr]">

                {{-- ── Left: forms ── --}}
                <div class="space-y-6">

                    {{-- Account info tiles --}}
                    <article class="border border-[#e4eff8] bg-white">
                        <div class="border-b border-[#e4eff8] px-6 py-6 sm:px-8">
                            <div class="mb-3 inline-flex items-center gap-[10px] text-[.63rem] font-bold uppercase tracking-[.22em] text-[#0086da]">
                                <span class="block h-[2px] w-[22px] bg-[#0086da]"></span>Overview
                            </div>
                            <h2 class="text-[1.5rem] leading-[1.15] font-extrabold tracking-[-.02em] text-[#1a2e3b]">Account Information</h2>
                        </div>
                        <div class="grid grid-cols-2 gap-[2px] bg-[#e4eff8] sm:grid-cols-4 px-0 py-0">
                            <div class="bg-white px-6 py-5">
                                <div class="text-[.6rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Name</div>
                                <div class="mt-2 text-sm font-semibold text-[#1a2e3b]">{{ $accountName }}</div>
                            </div>
                            <div class="bg-white px-6 py-5">
                                <div class="text-[.6rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Contact</div>
                                <div class="mt-2 text-sm font-semibold text-[#1a2e3b]">{{ data_get($user, 'contact') ?? 'N/A' }}</div>
                            </div>
                            <div class="bg-white px-6 py-5 col-span-2">
                                <div class="text-[.6rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Email</div>
                                <div class="mt-2 truncate text-sm font-semibold text-[#1a2e3b]">{{ $user->email ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="border-t border-[#e4eff8] px-6 py-4 sm:px-8">
                            <p class="text-[.82rem] leading-[1.7] text-[#7a9db5]">Member since <span class="font-semibold text-[#587189]">{{ $memberSince }}</span>. Clinic-side patient records are managed separately by staff.</p>
                        </div>
                    </article>

                    {{-- Edit account settings --}}
                    <article class="border border-[#e4eff8] bg-white">
                        <div class="border-b border-[#e4eff8] px-6 py-6 sm:px-8">
                            <div class="mb-3 inline-flex items-center gap-[10px] text-[.63rem] font-bold uppercase tracking-[.22em] text-[#0086da]">
                                <span class="block h-[2px] w-[22px] bg-[#0086da]"></span>Account Settings
                            </div>
                            <h2 class="text-[1.5rem] leading-[1.15] font-extrabold tracking-[-.02em] text-[#1a2e3b]">Update Details</h2>
                        </div>

                        <form action="{{ route('profile.update') }}" method="POST" class="px-6 py-6 sm:px-8">
                            @csrf
                            @method('PATCH')

                            <div class="grid gap-5 sm:grid-cols-2">
                                <div class="space-y-2">
                                    <label class="text-[.72rem] font-bold uppercase tracking-[.12em] text-[#587189]">Name</label>
                                    <input type="text" value="{{ $accountName }}" readonly
                                        class="w-full border border-[#e4eff8] bg-[#f6fafd] px-4 py-3 text-sm text-[#1a2e3b] outline-none">
                                    <p class="text-[.72rem] leading-[1.6] text-[#7a9db5]">Shown from your latest appointment request details.</p>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-[.72rem] font-bold uppercase tracking-[.12em] text-[#587189]">Email Address</label>
                                    <input type="text" value="{{ $user->email ?? 'N/A' }}" readonly
                                        class="w-full border border-[#e4eff8] bg-[#f6fafd] px-4 py-3 text-sm text-[#1a2e3b] outline-none">
                                </div>

                                <div class="space-y-2 sm:col-span-2">
                                    <label class="text-[.72rem] font-bold uppercase tracking-[.12em] text-[#587189]">Contact Number</label>
                                    <input type="text" name="contact" value="{{ old('contact', data_get($user, 'contact')) }}"
                                        class="w-full border border-[#e4eff8] bg-white px-4 py-3 text-sm text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#0086da]/10 @error('contact') border-rose-400 @enderror">
                                    @error('contact')
                                        <span class="text-xs text-rose-600">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit"
                                    class="inline-flex items-center gap-[9px] whitespace-nowrap bg-[#0086da] px-8 py-[15px] text-[.72rem] font-bold uppercase tracking-[.1em] text-white transition hover:bg-[#006ab0]">
                                    Save Contact Number
                                </button>
                            </div>
                        </form>
                    </article>
                </div>

                {{-- ── Right: security ── --}}
                <aside>
                    <article class="border border-[#e4eff8] bg-white">
                        <div class="border-b border-[#e4eff8] px-6 py-6">
                            <div class="mb-3 inline-flex items-center gap-[10px] text-[.63rem] font-bold uppercase tracking-[.22em] text-[#0086da]">
                                <span class="block h-[2px] w-[22px] bg-[#0086da]"></span>Security
                            </div>
                            <h2 class="text-[1.3rem] leading-[1.15] font-extrabold tracking-[-.02em] text-[#1a2e3b]">Password</h2>
                        </div>

                        <div class="px-6 py-6">
                            @if (!empty($isGoogleUser))
                                <p class="text-[.88rem] leading-[1.75] text-[#587189]">Your account uses Google Login. We'll email you a secure link to set a password.</p>
                                <form action="{{ route('profile.password.reset-link') }}" method="POST" class="mt-5">
                                    @csrf
                                    <button type="button"
                                        onclick="this.disabled=true; this.classList.add('opacity-60','cursor-not-allowed'); this.form.submit();"
                                        class="inline-flex items-center gap-[9px] whitespace-nowrap border border-[#1a2e3b] px-6 py-[11px] text-[.7rem] font-bold uppercase tracking-[.1em] text-[#1a2e3b] transition hover:bg-[#1a2e3b] hover:text-white">
                                        Send Reset Link
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                                    @csrf
                                    @method('PUT')

                                    <div class="space-y-2">
                                        <label class="text-[.72rem] font-bold uppercase tracking-[.12em] text-[#587189]">Current Password</label>
                                        <input type="password" name="current_password" placeholder="Current password"
                                            class="w-full border border-[#e4eff8] bg-white px-4 py-3 text-sm text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#0086da]/10 @error('current_password') border-rose-400 @enderror">
                                        @error('current_password')
                                            <span class="text-xs text-rose-600">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[.72rem] font-bold uppercase tracking-[.12em] text-[#587189]">New Password</label>
                                        <input type="password" name="password" placeholder="New password"
                                            class="w-full border border-[#e4eff8] bg-white px-4 py-3 text-sm text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#0086da]/10 @error('password') border-rose-400 @enderror">
                                        @error('password')
                                            <span class="text-xs text-rose-600">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[.72rem] font-bold uppercase tracking-[.12em] text-[#587189]">Confirm Password</label>
                                        <input type="password" name="password_confirmation" placeholder="Confirm new password"
                                            class="w-full border border-[#e4eff8] bg-white px-4 py-3 text-sm text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#0086da]/10">
                                    </div>

                                    <div class="pt-1">
                                        <button type="submit"
                                            class="inline-flex items-center gap-[9px] whitespace-nowrap bg-[#1a2e3b] px-6 py-[11px] text-[.7rem] font-bold uppercase tracking-[.1em] text-white transition hover:bg-[#0d1e27]">
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
@endsection
