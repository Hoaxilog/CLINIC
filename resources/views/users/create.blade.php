@extends('index')

@section('page_shell_class', 'bg-[#f6fafd]')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400&display=swap');
    #create-user-wrap * { font-family: 'Montserrat', sans-serif; }
    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear { display: none; }
</style>

<script>
    function toggleAdminCreatePassword(fieldId, eyeOpenId, eyeClosedId) {
        const passwordField = document.getElementById(fieldId);
        const eyeOpen = document.getElementById(eyeOpenId);
        const eyeClosed = document.getElementById(eyeClosedId);
        const isHidden = passwordField.type === 'password';
        passwordField.type = isHidden ? 'text' : 'password';
        eyeClosed.classList.toggle('hidden', isHidden);
        eyeOpen.classList.toggle('hidden', !isHidden);
    }
</script>

<div id="create-user-wrap" style="font-family:'Montserrat',sans-serif; -webkit-font-smoothing:antialiased;">

    {{-- Page Banner --}}
    <div class="mb-6 border border-[#e4eff8] bg-white">
        <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-6 md:px-8">
            <div>
                <h1 class="text-[1.35rem] font-extrabold leading-[1.1] tracking-[-.02em] text-[#1a2e3b]">Create User Account</h1>
                <p class="mt-1 text-[.8rem] text-[#7a9db5]">Add a new admin, dentist, or staff member to the system.</p>
            </div>
            <a href="{{ route('users.index') }}"
                class="inline-flex items-center gap-2 border border-[#e4eff8] bg-white hover:bg-[#f0f8fe] px-5 py-[9px] text-[.72rem] font-bold uppercase tracking-[.1em] text-[#3d5a6e] transition hover:-translate-y-px">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="square">
                    <path d="M19 12H5M12 5l-7 7 7 7"/>
                </svg>
                Back to Users
            </a>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="border border-[#e4eff8] bg-white shadow-[0_20px_48px_rgba(0,134,218,.07)]">

        {{-- Card Header --}}
        <div class="flex items-center gap-3 border-b border-[#e4eff8] px-6 py-5 md:px-8">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center bg-[#0086da]">
                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/>
                    <path d="M6.376 18.91a6 6 0 0 1 11.249.003"/>
                    <circle cx="12" cy="11" r="4"/>
                </svg>
            </div>
            <div>
                <div class="text-[.95rem] font-extrabold tracking-[-0.01em] text-[#1a2e3b]">Account Details</div>
                <p class="text-[.72rem] text-[#7a9db5]">Fill in the fields below to register a new user.</p>
            </div>
        </div>

        <div class="p-6 md:p-8">

            {{-- Error Messages --}}
            @if(session('error'))
                <div class="mb-6 border-l-4 border-red-500 bg-red-50 p-4 text-[.8rem] text-red-700 flex items-start gap-3">
                    <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <span class="font-bold block">Error Encountered</span>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 border-l-4 border-red-500 bg-red-50 p-4 text-[.8rem] text-red-700">
                    <span class="font-bold block">Please fix the following:</span>
                    <ul class="mt-2 list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('users.store') }}">
                @csrf

                {{-- Section Label --}}
                <p class="mb-5 text-[.7rem] font-bold uppercase tracking-[.14em] text-[#7a9db5]">Personal & Account Information</p>

                {{-- Row 1: First + Last Name --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    {{-- First Name --}}
                    <div>
                        <label class="block text-[.7rem] font-bold uppercase tracking-[.12em] text-[#3d5a6e] mb-1.5">First Name</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-[#7a9db5]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </span>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" placeholder="Enter first name" required maxlength="100" pattern="[A-Za-zÀ-ÿ\s'\-]+"
                                title="First name may only contain letters, spaces, hyphens, and apostrophes."
                                class="w-full pl-9 pr-4 py-2.5 border border-[#d4e8f5] bg-white text-[.85rem] text-[#1a2e3b] font-medium placeholder-[#a8c5d8] focus:outline-none focus:border-[#0086da] transition">
                        </div>
                        @error('first_name') <p class="text-red-500 text-[.72rem] mt-1 font-semibold">{{ $message }}</p> @enderror
                    </div>

                    {{-- Last Name --}}
                    <div>
                        <label class="block text-[.7rem] font-bold uppercase tracking-[.12em] text-[#3d5a6e] mb-1.5">Last Name</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-[#7a9db5]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </span>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Enter last name" required maxlength="100" pattern="[A-Za-zÀ-ÿ\s'\-]+"
                                title="Last name may only contain letters, spaces, hyphens, and apostrophes."
                                class="w-full pl-9 pr-4 py-2.5 border border-[#d4e8f5] bg-white text-[.85rem] text-[#1a2e3b] font-medium placeholder-[#a8c5d8] focus:outline-none focus:border-[#0086da] transition">
                        </div>
                        @error('last_name') <p class="text-red-500 text-[.72rem] mt-1 font-semibold">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Row 2: Email + Mobile --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    {{-- Email --}}
                    <div>
                        <label class="block text-[.7rem] font-bold uppercase tracking-[.12em] text-[#3d5a6e] mb-1.5">Email Address</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-[#7a9db5]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </span>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="e.g. staff@tejadent.com" required maxlength="255"
                                class="w-full pl-9 pr-4 py-2.5 border border-[#d4e8f5] bg-white text-[.85rem] text-[#1a2e3b] font-medium placeholder-[#a8c5d8] focus:outline-none focus:border-[#0086da] transition">
                        </div>
                        @error('email') <p class="text-red-500 text-[.72rem] mt-1 font-semibold">{{ $message }}</p> @enderror
                    </div>

                    {{-- Mobile Number --}}
                    <div>
                        <label class="block text-[.7rem] font-bold uppercase tracking-[.12em] text-[#3d5a6e] mb-1.5">Mobile Number</label>
                        <div class="flex min-w-0">
                            <span class="inline-flex shrink-0 items-center px-3 border border-r-0 border-[#d4e8f5] bg-[#f0f8fe] text-[.8rem] font-bold text-[#3d5a6e] select-none">+63</span>
                            <input type="text" inputmode="numeric" maxlength="10" name="mobile_number" value="{{ old('mobile_number') }}" placeholder="9171234567" pattern="[0-9]{10}"
                                title="Mobile number must be exactly 10 digits after +63." oninput="this.value=this.value.replace(/[^0-9]/g,'').replace(/^0+/,'').slice(0,10)"
                                class="min-w-0 w-full pr-4 py-2.5 border border-[#d4e8f5] bg-white text-[.85rem] text-[#1a2e3b] font-medium placeholder-[#a8c5d8] focus:outline-none focus:border-[#0086da] transition">
                        </div>
                        @error('mobile_number') <p class="text-red-500 text-[.72rem] mt-1 font-semibold">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Row 3: Role (single col, half width) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
                    {{-- Role --}}
                    <div>
                        <label class="block text-[.7rem] font-bold uppercase tracking-[.12em] text-[#3d5a6e] mb-1.5">Role</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-[#7a9db5]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </span>
                            <select name="role" required class="w-full pl-9 pr-8 py-2.5 border border-[#d4e8f5] bg-white text-[.85rem] text-[#1a2e3b] font-medium appearance-none focus:outline-none focus:border-[#0086da] transition">
                                <option value="" selected disabled>Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role') == $role->id ? 'selected' : '' }}>{{ $role->label }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-[#7a9db5]">
                                <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                            </div>
                        </div>
                        @error('role') <p class="text-red-500 text-[.72rem] mt-1 font-semibold">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Divider --}}
                <div class="border-t border-[#e4eff8] mb-7"></div>
                <p class="mb-5 text-[.7rem] font-bold uppercase tracking-[.14em] text-[#7a9db5]">Set Password</p>

                {{-- Row 4: Password + Confirm --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
                    {{-- Password --}}
                    <div>
                        <label class="block text-[.7rem] font-bold uppercase tracking-[.12em] text-[#3d5a6e] mb-1.5">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-[#7a9db5]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </span>
                            <input type="password" id="admin-create-password" name="password" placeholder="Min. 8 characters" required minlength="8" autocomplete="new-password"
                                class="w-full pl-9 pr-10 py-2.5 border border-[#d4e8f5] bg-white text-[.85rem] text-[#1a2e3b] font-medium placeholder-[#a8c5d8] focus:outline-none focus:border-[#0086da] transition">
                            <button type="button"
                                onclick="toggleAdminCreatePassword('admin-create-password','admin-create-eye-open','admin-create-eye-closed')"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-[#7a9db5] hover:text-[#3d5a6e] transition"
                                aria-label="Toggle password visibility">
                                <svg id="admin-create-eye-closed" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg id="admin-create-eye-open" class="h-4 w-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.58 10.58a2 2 0 102.83 2.83"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.88 5.09A10.94 10.94 0 0112 5c5 0 9.27 3.11 11 7a12.62 12.62 0 01-3.04 4.19M6.1 6.1A12.84 12.84 0 001 12c1.73 3.89 6 7 11 7 1.65 0 3.23-.34 4.66-.95"/>
                                </svg>
                            </button>
                        </div>
                        @error('password') <p class="text-red-500 text-[.72rem] mt-1 font-semibold">{{ $message }}</p> @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label class="block text-[.7rem] font-bold uppercase tracking-[.12em] text-[#3d5a6e] mb-1.5">Confirm Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-[#7a9db5]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </span>
                            <input type="password" id="admin-create-password-confirmation" name="password_confirmation" placeholder="Re-enter password" required minlength="8" autocomplete="new-password"
                                class="w-full pl-9 pr-10 py-2.5 border border-[#d4e8f5] bg-white text-[.85rem] text-[#1a2e3b] font-medium placeholder-[#a8c5d8] focus:outline-none focus:border-[#0086da] transition">
                            <button type="button"
                                onclick="toggleAdminCreatePassword('admin-create-password-confirmation','admin-create-confirm-eye-open','admin-create-confirm-eye-closed')"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-[#7a9db5] hover:text-[#3d5a6e] transition"
                                aria-label="Toggle confirm password visibility">
                                <svg id="admin-create-confirm-eye-closed" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg id="admin-create-confirm-eye-open" class="h-4 w-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.58 10.58a2 2 0 102.83 2.83"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.88 5.09A10.94 10.94 0 0112 5c5 0 9.27 3.11 11 7a12.62 12.62 0 01-3.04 4.19M6.1 6.1A12.84 12.84 0 001 12c1.73 3.89 6 7 11 7 1.65 0 3.23-.34 4.66-.95"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div class="flex items-center justify-end gap-4 border-t border-[#e4eff8] pt-6">
                    <a href="{{ route('users.index') }}"
                        class="px-5 py-2.5 text-[.75rem] font-bold uppercase tracking-[.1em] text-[#7a9db5] hover:text-[#3d5a6e] transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 bg-[#0086da] hover:bg-[#006ab0] px-7 py-2.5 text-[.75rem] font-bold uppercase tracking-[.1em] text-white transition hover:-translate-y-px">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="square">
                            <path d="M5 12h14M12 5v14"/>
                        </svg>
                        Create Account
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection
