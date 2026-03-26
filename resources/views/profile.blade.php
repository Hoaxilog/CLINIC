@extends('index')

@section('style')
    @import
    url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400&display=swap');

    #staff-profile-wrap * {
    font-family: 'Montserrat', sans-serif;
    }
@endsection

@section('page_shell_class', 'bg-[#f6fafd] px-6 py-8 md:px-10 xl:px-14')

@section('content')
    @php
        $displayRole = ucfirst($roleName ?? 'Administrator');
    @endphp

    <div id="staff-profile-wrap" class="mx-auto flex w-full max-w-[1400px] flex-col gap-7">
            @if (session('success'))
                <div class="flex items-center gap-3 border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm text-emerald-800">
                    <svg class="h-4 w-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="square" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="flex items-center gap-3 border border-rose-200 bg-rose-50 px-5 py-3 text-sm text-rose-700">
                    <svg class="h-4 w-4 shrink-0 text-rose-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="square" d="M12 8v4m0 4h.01" />
                        <circle cx="12" cy="12" r="9" />
                    </svg>
                    Please check the highlighted profile fields and try again.
                </div>
            @endif

            <div class="border-b border-[#e4eff8] pb-6">
                <h1 class="text-[1.7rem] font-extrabold leading-[1.1] tracking-[-.02em] text-[#1a2e3b]">My Profile</h1>
                <p class="mt-3 max-w-3xl text-[.88rem] leading-[1.7] text-[#587189]">
                    Review your clinic account details, update your basic identity information, and manage your password from one place. Email and role access
                    are still managed by the clinic system.
                </p>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1.6fr_.9fr]">
                <div class="space-y-6">
                    <article x-data="{ editing: {{ $errors->any() ? 'true' : 'false' }} }" class="border border-[#e4eff8] bg-white">
                        <div class="border-b border-[#e4eff8] px-6 py-6 sm:px-8">
                            <h2 class="text-[1.5rem] font-extrabold leading-[1.15] tracking-[-.02em] text-[#1a2e3b]">
                                Account Identity
                            </h2>
                        </div>

                        <form x-ref="identityForm" action="{{ route('profile.update') }}" method="POST" class="px-6 py-6 sm:px-8">
                            @csrf
                            @method('PATCH')

                            <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
                                <p class="text-[.82rem] leading-[1.7] text-[#7a9db5]">
                                    Update your display name and mobile number here. Email and role stay locked.
                                </p>
                                <button type="button" x-show="!editing" x-cloak @click="editing = true"
                                    class="inline-flex items-center gap-2 border border-[#0086da] bg-white px-5 py-[10px] text-[.7rem] font-bold uppercase tracking-[.1em] text-[#0086da] transition hover:bg-[#f0f8fe]">
                                    Edit Details
                                </button>
                            </div>

                            <div class="grid gap-5 sm:grid-cols-2">
                                <div class="space-y-2">
                                    <label class="text-[.72rem] font-bold uppercase tracking-[.12em] text-[#587189]">
                                        First Name
                                    </label>
                                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}"
                                        x-bind:readonly="!editing" x-bind:class="editing ? 'bg-white' : 'bg-[#f6fafd] cursor-not-allowed'"
                                        class="w-full border border-[#e4eff8] px-4 py-3 text-sm text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#0086da]/10 @error('first_name') border-rose-400 focus:border-rose-500 focus:ring-rose-200 @enderror">
                                    @error('first_name')
                                        <span class="text-xs text-rose-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="text-[.72rem] font-bold uppercase tracking-[.12em] text-[#587189]">
                                        Last Name
                                    </label>
                                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}"
                                        x-bind:readonly="!editing" x-bind:class="editing ? 'bg-white' : 'bg-[#f6fafd] cursor-not-allowed'"
                                        class="w-full border border-[#e4eff8] px-4 py-3 text-sm text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#0086da]/10 @error('last_name') border-rose-400 focus:border-rose-500 focus:ring-rose-200 @enderror">
                                    @error('last_name')
                                        <span class="text-xs text-rose-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                @if ($hasMiddleNameColumn)
                                    <div class="space-y-2">
                                        <label class="text-[.72rem] font-bold uppercase tracking-[.12em] text-[#587189]">
                                            Middle Name
                                        </label>
                                        <input type="text" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}"
                                            x-bind:readonly="!editing" x-bind:class="editing ? 'bg-white' : 'bg-[#f6fafd] cursor-not-allowed'"
                                            class="w-full border border-[#e4eff8] px-4 py-3 text-sm text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#0086da]/10 @error('middle_name') border-rose-400 focus:border-rose-500 focus:ring-rose-200 @enderror">
                                        @error('middle_name')
                                            <span class="text-xs text-rose-600">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif

                                <div class="space-y-2 {{ $hasMiddleNameColumn ? '' : 'sm:col-span-2' }}">
                                    <label class="text-[.72rem] font-bold uppercase tracking-[.12em] text-[#587189]">
                                        Mobile Number
                                    </label>
                                    <div class="flex">
                                        <span class="inline-flex items-center border border-r-0 border-[#d4e8f5] bg-[#f0f8fe] px-4 text-sm font-semibold text-[#3d5a6e]">+63</span>
                                        <input type="text" name="mobile_number" inputmode="numeric" maxlength="10"
                                            oninput="this.value = this.value.replace(/\D/g, '').replace(/^0+/, '').slice(0, 10)"
                                            value="{{ old('mobile_number', $accountMobileNumber) }}"
                                            x-bind:readonly="!editing" x-bind:class="editing ? 'bg-white' : 'bg-[#f6fafd] cursor-not-allowed'"
                                            class="w-full min-w-0 border border-[#e4eff8] px-4 py-3 text-sm text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#0086da]/10 @error('mobile_number') border-rose-400 focus:border-rose-500 focus:ring-rose-200 @enderror">
                                    </div>
                                    @error('mobile_number')
                                        <span class="text-xs text-rose-600">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="text-[.72rem] font-bold uppercase tracking-[.12em] text-[#587189]">
                                        Email Address
                                    </label>
                                    <input type="text" value="{{ $user->email ?: 'N/A' }}" readonly
                                        class="w-full border border-[#e4eff8] bg-[#f6fafd] px-4 py-3 text-sm text-[#1a2e3b] outline-none">
                                </div>

                                <div class="space-y-2">
                                    <label class="text-[.72rem] font-bold uppercase tracking-[.12em] text-[#587189]">
                                        Access Role
                                    </label>
                                    <input type="text" value="{{ $displayRole }}" readonly
                                        class="w-full border border-[#e4eff8] bg-[#f6fafd] px-4 py-3 text-sm text-[#1a2e3b] outline-none">
                                    <p class="text-[.72rem] leading-[1.6] text-[#7a9db5]">
                                        Identity and access changes are handled from user management, not from this
                                        profile page.
                                    </p>
                                </div>
                            </div>

                            <div x-show="editing" x-cloak class="mt-6 flex flex-wrap items-center justify-between gap-4 border-t border-[#e4eff8] pt-5">
                                <p class="text-[.78rem] leading-[1.6] text-[#7a9db5]">
                                    Save your updated name and contact number here. Role and email remain locked for admin control.
                                </p>
                                <div class="flex flex-wrap items-center gap-2">
                                    <button type="button" @click="$refs.identityForm.reset(); editing = false"
                                        class="inline-flex items-center gap-2 border border-[#d4e8f5] bg-white px-5 py-[11px] text-[.7rem] font-bold uppercase tracking-[.1em] text-[#587189] transition hover:bg-[#f6fafd]">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                        class="inline-flex items-center gap-[9px] whitespace-nowrap bg-[#0086da] px-6 py-[11px] text-[.7rem] font-bold uppercase tracking-[.1em] text-white transition hover:bg-[#006ab0]">
                                        Save Changes
                                    </button>
                                </div>
                            </div>
                        </form>
                    </article>
                </div>

                <aside class="space-y-6">
                    <article class="border border-[#e4eff8] bg-white">
                        <div class="border-b border-[#e4eff8] px-6 py-6">
                            <h2 class="text-[1.3rem] font-extrabold leading-[1.15] tracking-[-.02em] text-[#1a2e3b]">
                                Password
                            </h2>
                        </div>

                        <div class="px-6 py-6">
                            @if (!empty($isGoogleUser))
                                <p class="text-[.88rem] leading-[1.75] text-[#587189]">
                                    Your account uses Google Login. We will email you a secure link so you can set a
                                    password for direct sign-in.
                                </p>
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
                                        <label class="text-[.72rem] font-bold uppercase tracking-[.12em] text-[#587189]">
                                            Current Password
                                        </label>
                                        <input type="password" name="current_password" placeholder="Current password"
                                            class="w-full border border-[#e4eff8] bg-white px-4 py-3 text-sm text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#0086da]/10 @error('current_password') border-rose-400 @enderror">
                                        @error('current_password')
                                            <span class="text-xs text-rose-600">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[.72rem] font-bold uppercase tracking-[.12em] text-[#587189]">
                                            New Password
                                        </label>
                                        <input type="password" name="password" placeholder="New password"
                                            class="w-full border border-[#e4eff8] bg-white px-4 py-3 text-sm text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#0086da]/10 @error('password') border-rose-400 @enderror">
                                        @error('password')
                                            <span class="text-xs text-rose-600">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-[.72rem] font-bold uppercase tracking-[.12em] text-[#587189]">
                                            Confirm Password
                                        </label>
                                        <input type="password" name="password_confirmation"
                                            placeholder="Confirm new password"
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
@endsection
