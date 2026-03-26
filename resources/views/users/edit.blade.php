@extends('index')

@section('page_shell_class', 'bg-[#f6fafd]')

@section('content')
<script>
    function toggleAdminEditPassword(fieldId, eyeOpenId, eyeClosedId) {
        const passwordField = document.getElementById(fieldId);
        const eyeOpen = document.getElementById(eyeOpenId);
        const eyeClosed = document.getElementById(eyeClosedId);

        const isHidden = passwordField.type === 'password';
        passwordField.type = isHidden ? 'text' : 'password';

        eyeClosed.classList.toggle('hidden', isHidden);
        eyeOpen.classList.toggle('hidden', !isHidden);
    }
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400&display=swap');
    #edit-user-wrap * { font-family: 'Montserrat', sans-serif; }
    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear { display: none; }
</style>

<div id="edit-user-wrap" class="px-6 pb-16 md:px-12 xl:px-20" style="font-family:'Montserrat',sans-serif; -webkit-font-smoothing:antialiased;">
    <div class="mx-auto w-full max-w-[900px]">

        {{-- Page Banner --}}
        <div class="mb-6 border border-[#e4eff8] bg-white">
            <div class="flex items-center gap-4 px-6 py-6 md:px-8">
                <div>
                    <a href="{{ route('users.index') }}"
                        class="mb-3 inline-flex items-center gap-[7px] text-[.68rem] font-bold uppercase tracking-[.12em] text-[#7a9db5] no-underline transition hover:text-[#0086da]">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="square">
                            <path d="M19 12H5M12 5l-7 7 7 7" />
                        </svg>
                        Back to Users
                    </a>
                    <h1 class="text-[1.35rem] font-extrabold leading-[1.1] tracking-[-.02em] text-[#1a2e3b]">Edit
                        User Account</h1>
                    <p class="mt-1 text-[.8rem] text-[#7a9db5]">Update internal account details and permissions.</p>
                </div>
            </div>
        </div>

        {{-- Alerts --}}
        @if(session('error'))
            <div class="mb-6 flex items-center gap-3 border border-red-200 bg-red-50 px-5 py-4 text-[.85rem] text-red-700">
                <svg class="h-5 w-5 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="square" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <span class="font-bold block">Error Encountered</span>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 flex items-start gap-3 border border-red-200 bg-red-50 px-5 py-4 text-[.85rem] text-red-700">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="square" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <span class="font-bold block">Please fix the following:</span>
                    <ul class="mt-2 list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('users.update', $user->id) }}">
            @csrf
            @method('PUT')

            {{-- CARD 1: Account Details --}}
            <div class="bg-white border border-[#e4eff8] shadow-[0_20px_48px_rgba(0,134,218,.07)] mb-6">

                {{-- Card Header --}}
                <div class="flex items-center gap-3 border-b border-[#e4eff8] px-7 py-5 md:px-10">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center bg-[#0086da]">
                        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="square" d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-[.6rem] font-bold uppercase tracking-[.2em] text-[#0086da]">Section 1</div>
                        <div class="text-[.95rem] font-extrabold tracking-[-0.01em] text-[#1a2e3b]">Account Details</div>
                    </div>
                </div>

                <div class="p-7 md:p-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- First Name --}}
                        <div>
                            <label class="block text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e] mb-2">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}"
                                placeholder="Enter first name" required maxlength="100"
                                pattern="[A-Za-zÀ-ÿ\s'\-]+"
                                title="First name may only contain letters, spaces, hyphens, and apostrophes."
                                class="w-full border border-[#d4e8f5] bg-white px-4 py-3 text-sm text-[#1a2e3b] placeholder:text-[#9bbdd0] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#cde8fb] @error('first_name') border-red-400 focus:ring-red-200 focus:border-red-500 @enderror">
                            @error('first_name')
                                <p class="text-red-500 text-xs mt-1.5 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Last Name --}}
                        <div>
                            <label class="block text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e] mb-2">
                                Last Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}"
                                placeholder="Enter last name" required maxlength="100"
                                pattern="[A-Za-zÀ-ÿ\s'\-]+"
                                title="Last name may only contain letters, spaces, hyphens, and apostrophes."
                                class="w-full border border-[#d4e8f5] bg-white px-4 py-3 text-sm text-[#1a2e3b] placeholder:text-[#9bbdd0] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#cde8fb] @error('last_name') border-red-400 focus:ring-red-200 focus:border-red-500 @enderror">
                            @error('last_name')
                                <p class="text-red-500 text-xs mt-1.5 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e] mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email', $user->email ?? $user->username) }}"
                                placeholder="e.g. staff@tejadent.com" required maxlength="255"
                                class="w-full border border-[#d4e8f5] bg-white px-4 py-3 text-sm text-[#1a2e3b] placeholder:text-[#9bbdd0] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#cde8fb] @error('email') border-red-400 focus:ring-red-200 focus:border-red-500 @enderror">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1.5 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Mobile Number --}}
                        <div>
                            <label class="block text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e] mb-2">
                                Mobile Number
                            </label>
                            <div class="flex min-w-0">
                                <span class="inline-flex shrink-0 items-center px-3 border border-r-0 border-[#d4e8f5] bg-[#f0f8fe] text-[#3d5a6e] text-sm font-semibold select-none @error('mobile_number') border-red-400 @enderror">+63</span>
                                <input type="text" inputmode="numeric" maxlength="10" name="mobile_number"
                                    value="{{ old('mobile_number', $user->mobile_number) }}"
                                    placeholder="9171234567" pattern="[0-9]{10}"
                                    title="Mobile number must be exactly 10 digits after +63."
                                    oninput="this.value=this.value.replace(/[^0-9]/g,'').replace(/^0+/,'').slice(0,10)"
                                    class="min-w-0 w-full border border-[#d4e8f5] bg-white pr-4 py-3 text-sm text-[#1a2e3b] placeholder:text-[#9bbdd0] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#cde8fb] @error('mobile_number') border-red-400 focus:ring-red-200 focus:border-red-500 @enderror">
                            </div>
                            @error('mobile_number')
                                <p class="text-red-500 text-xs mt-1.5 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Role --}}
                        <div class="md:col-span-2">
                            <label class="block text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e] mb-2">
                                Role <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="role" required
                                    class="w-full border border-[#d4e8f5] bg-white px-4 pr-10 py-3 text-sm text-[#1a2e3b] appearance-none outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#cde8fb] cursor-pointer @error('role') border-red-400 focus:ring-red-200 focus:border-red-500 @enderror">
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role', $user->role) == $role->id ? 'selected' : '' }}>
                                            {{ $role->label }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-[#7a9db5]">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="square" d="M6 9l6 6 6-6" />
                                    </svg>
                                </div>
                            </div>
                            @error('role')
                                <p class="text-red-500 text-xs mt-1.5 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            {{-- CARD 2: Password Change --}}
            <div class="bg-white border border-[#e4eff8] shadow-[0_20px_48px_rgba(0,134,218,.07)] mb-8">

                {{-- Card Header --}}
                <div class="flex items-center gap-3 border-b border-[#e4eff8] px-7 py-5 md:px-10">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center bg-[#0086da]">
                        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="11" stroke-linecap="square" />
                            <path stroke-linecap="square" d="M7 11V7a5 5 0 0110 0v4" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-[.6rem] font-bold uppercase tracking-[.2em] text-[#0086da]">Section 2</div>
                        <div class="text-[.95rem] font-extrabold tracking-[-0.01em] text-[#1a2e3b]">Password Change</div>
                    </div>
                </div>

                <div class="p-7 md:p-10">
                    <p class="mb-6 text-[.82rem] leading-relaxed text-[#7a9db5]">Leave both fields blank to keep the current password.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- New Password --}}
                        <div>
                            <label class="block text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e] mb-2">
                                New Password
                            </label>
                            <div class="relative">
                                <input type="password" id="admin-edit-password" name="password" autocomplete="new-password"
                                    placeholder="Leave blank to keep current" minlength="8"
                                    class="w-full border border-[#d4e8f5] bg-white px-4 pr-12 py-3 text-sm text-[#1a2e3b] placeholder:text-[#9bbdd0] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#cde8fb] @error('password') border-red-400 focus:ring-red-200 focus:border-red-500 @enderror">
                                <button type="button"
                                    onclick="toggleAdminEditPassword('admin-edit-password','admin-edit-eye-open','admin-edit-eye-closed')"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-[#7a9db5] hover:text-[#0086da] transition"
                                    aria-label="Toggle password visibility">
                                    <svg id="admin-edit-eye-closed" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <svg id="admin-edit-eye-open" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.58 10.58a2 2 0 102.83 2.83"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.88 5.09A10.94 10.94 0 0112 5c5 0 9.27 3.11 11 7a12.62 12.62 0 01-3.04 4.19M6.1 6.1A12.84 12.84 0 001 12c1.73 3.89 6 7 11 7 1.65 0 3.23-.34 4.66-.95"></path>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-red-500 text-xs mt-1.5 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div>
                            <label class="block text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e] mb-2">
                                Confirm New Password
                            </label>
                            <div class="relative">
                                <input type="password" id="admin-edit-password-confirmation" name="password_confirmation" autocomplete="new-password"
                                    placeholder="Confirm new password" minlength="8"
                                    class="w-full border border-[#d4e8f5] bg-white px-4 pr-12 py-3 text-sm text-[#1a2e3b] placeholder:text-[#9bbdd0] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#cde8fb]">
                                <button type="button"
                                    onclick="toggleAdminEditPassword('admin-edit-password-confirmation','admin-edit-confirm-eye-open','admin-edit-confirm-eye-closed')"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-[#7a9db5] hover:text-[#0086da] transition"
                                    aria-label="Toggle confirm password visibility">
                                    <svg id="admin-edit-confirm-eye-closed" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <svg id="admin-edit-confirm-eye-open" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.58 10.58a2 2 0 102.83 2.83"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.88 5.09A10.94 10.94 0 0112 5c5 0 9.27 3.11 11 7a12.62 12.62 0 01-3.04 4.19M6.1 6.1A12.84 12.84 0 001 12c1.73 3.89 6 7 11 7 1.65 0 3.23-.34 4.66-.95"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Footer Buttons --}}
            <div class="flex items-center justify-between">
                <a href="{{ route('users.index') }}"
                    class="inline-flex items-center gap-[7px] text-[.72rem] font-bold uppercase tracking-[.1em] text-[#7a9db5] transition hover:text-[#1a2e3b]">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="square">
                        <path d="M19 12H5M12 5l-7 7 7 7" />
                    </svg>
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-[#0086da] hover:bg-[#006ab0] px-8 py-[14px] text-[.72rem] font-bold uppercase tracking-[.1em] text-white transition hover:-translate-y-px">
                    <svg class="h-[13px] w-[13px]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="square" d="M5 13l4 4L19 7" />
                    </svg>
                    Update Account
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
