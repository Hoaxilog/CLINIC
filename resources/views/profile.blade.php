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
        $memberSince = !empty($user->created_at) ? \Carbon\Carbon::parse($user->created_at)->format('M Y') : 'Recently';
        $createdDate = !empty($user->created_at) ? \Carbon\Carbon::parse($user->created_at)->format('M d, Y') : 'N/A';
        $updatedDate = !empty($user->updated_at) ? \Carbon\Carbon::parse($user->updated_at)->format('M d, Y') : 'N/A';
        $authMethod = !empty($isGoogleUser) ? 'Google Login' : 'Password Login';
        $resolvedAccountName = trim((string) ($accountDisplayName ?? ''));
        $resolvedAccountName = $resolvedAccountName !== '' ? $resolvedAccountName : 'N/A';
    @endphp

    <div id="staff-profile-wrap" class="mx-auto flex w-full max-w-[1400px] flex-col gap-7">


            <div class="border-b border-[#e4eff8] pb-6">
                <h1 class="text-[1.7rem] font-extrabold leading-[1.1] tracking-[-.02em] text-[#1a2e3b]">My Profile</h1>
                <p class="mt-3 max-w-3xl text-[.88rem] leading-[1.7] text-[#587189]">
                    Review your clinic account details and manage your password from one place. Email and role access
                    are managed by the clinic system.
                </p>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1.6fr_.9fr]">
                <div class="space-y-6">
                    <article class="border border-[#e4eff8] bg-white">
                        <div class="border-b border-[#e4eff8] px-6 py-6 sm:px-8">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                                <p class="max-w-3xl text-[.82rem] leading-[1.7] text-[#7a9db5]">
                                    Member since <span class="font-semibold text-[#587189]">{{ $memberSince }}</span>.
                                    Your role and account access are managed inside the clinic system.
                                </p>
                            </div>
                        </div>

                        <div class="grid gap-[2px] bg-[#e4eff8]">
                            <div class="bg-white px-6 py-5">
                                <div class="text-[.6rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Created</div>
                                <div class="mt-2 text-sm font-semibold text-[#1a2e3b]">{{ $createdDate }}</div>
                            </div>
                            <div class="bg-white px-6 py-5">
                                <div class="text-[.6rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Updated</div>
                                <div class="mt-2 text-sm font-semibold text-[#1a2e3b]">{{ $updatedDate }}</div>
                            </div>
                            <div class="bg-white px-6 py-5">
                                <div class="text-[.6rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Role</div>
                                <div class="mt-2 text-sm font-semibold text-[#1a2e3b]">{{ $displayRole }}</div>
                            </div>
                            <div class="bg-white px-6 py-5">
                                <div class="text-[.6rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Name</div>
                                <div class="mt-2 break-all text-sm font-semibold text-[#1a2e3b]">{{ $resolvedAccountName }}
                                </div>
                            </div>
                            <div class="bg-white px-6 py-5">
                                <div class="text-[.6rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Mobile</div>
                                <div class="mt-2 break-all text-sm font-semibold text-[#1a2e3b]">{{ $accountMobileNumber ?: 'N/A' }}
                                </div>
                            </div>
                            <div class="bg-white px-6 py-5">
                                <div class="text-[.6rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Email</div>
                                <div class="mt-2 break-all text-sm font-semibold text-[#1a2e3b]">{{ $user->email ?: 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </article>

                    <article class="border border-[#e4eff8] bg-white">
                        <div class="border-b border-[#e4eff8] px-6 py-6 sm:px-8">
                            <h2 class="text-[1.5rem] font-extrabold leading-[1.15] tracking-[-.02em] text-[#1a2e3b]">
                                Account Identity
                            </h2>
                        </div>

                        <div class="px-6 py-6 sm:px-8">
                            <div class="grid gap-5">
                                <div class="space-y-2">
                                    <label class="text-[.72rem] font-bold uppercase tracking-[.12em] text-[#587189]">
                                        Full Name
                                    </label>
                                    <input type="text" value="{{ $resolvedAccountName }}" readonly
                                        class="w-full border border-[#e4eff8] bg-[#f6fafd] px-4 py-3 text-sm text-[#1a2e3b] outline-none">
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
                                        Mobile Number
                                    </label>
                                    <input type="text" value="{{ $accountMobileNumber ?: 'N/A' }}" readonly
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
                            <div class="mt-6 rounded-sm border border-[#d4e8f5] bg-[#f6fafd] px-4 py-4 text-[.82rem] leading-[1.7] text-[#587189]">
                                Use this page to review account identity details and manage password access. If your
                                email or role needs to change, update it from the clinic's user management area.
                            </div>
                        </div>
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
