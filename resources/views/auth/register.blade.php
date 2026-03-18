<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TEJADENT</title>
    <x-brand.meta />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400&display=swap"
        rel="stylesheet">
    @vite('resources/css/app.css')
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }
    </style>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        function toggleRegisterPassword(fieldId, eyeOpenId, eyeClosedId) {
            const passwordField = document.getElementById(fieldId);
            const eyeOpen = document.getElementById(eyeOpenId);
            const eyeClosed = document.getElementById(eyeClosedId);

            const isHidden = passwordField.type === 'password';
            passwordField.type = isHidden ? 'text' : 'password';

            eyeClosed.classList.toggle('hidden', isHidden);
            eyeOpen.classList.toggle('hidden', !isHidden);
        }
    </script>
</head>

<body class="min-h-screen bg-[#f6fafd] text-[#1a2e3b] antialiased">
    @php($guestActionMode = 'login')
    @include('components.homepage.header-section')

    <main class="px-6 py-8 md:px-12 xl:px-20">
        <div class="mx-auto w-full max-w-[1200px]">
            <div class="mb-6 border-b border-[#e4eff8] pb-6">
                <div class="mb-4 inline-flex items-center gap-[10px] text-[.63rem] font-bold uppercase tracking-[.22em] text-[#0086da]">
                    <span class="block h-[2px] w-[22px] bg-[#0086da]"></span>Patient Registration
                </div>
                <h1 class="text-[clamp(1.8rem,3.2vw,2.5rem)] font-extrabold leading-[1.1] tracking-[-.02em] text-[#1a2e3b]">Create your account</h1>
                <p class="mt-3 max-w-3xl text-[.9rem] leading-[1.8] text-[#587189]">
                    Use the same profile details you use for appointments so staff can verify and link your records faster.
                </p>
            </div>

            <div class="grid border border-[#e4eff8] bg-[#e4eff8] lg:grid-cols-[.9fr_1.1fr]">
                <section class="border-r border-[#e4eff8] bg-white px-6 py-7 sm:px-8 md:px-10">
                    <div class="mb-7 flex items-center gap-3 border-b border-[#e4eff8] pb-6">
                        <x-brand.logo href="{{ route('home') }}" class="pointer-events-none" iconClass="flex h-[50px] w-[50px] shrink-0 items-center justify-center" />
                        <div>
                            <p class="text-[.68rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Tejada Clinic</p>
                            <p class="text-[1.05rem] font-extrabold text-[#1a2e3b]">Account Access</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="border border-[#e4eff8] bg-[#f6fafd] p-5">
                            <p class="text-[.7rem] font-bold uppercase tracking-[.18em] text-[#0086da]">Faster booking</p>
                            <p class="mt-2 text-[.9rem] leading-[1.75] text-[#587189]">Your next requests can prefill from your account details.</p>
                        </div>
                        <div class="border border-[#e4eff8] bg-[#f6fafd] p-5">
                            <p class="text-[.7rem] font-bold uppercase tracking-[.18em] text-[#0086da]">Safer linking</p>
                            <p class="mt-2 text-[.9rem] leading-[1.75] text-[#587189]">Staff still verifies records before permanent patient linking.</p>
                        </div>
                        <div class="border border-[#e4eff8] bg-[#f6fafd] p-5">
                            <p class="text-[.7rem] font-bold uppercase tracking-[.18em] text-[#0086da]">One profile</p>
                            <p class="mt-2 text-[.9rem] leading-[1.75] text-[#587189]">Keep your account name and mobile consistent across dashboard and profile.</p>
                        </div>
                    </div>
                </section>

                <section class="bg-white px-6 py-7 sm:px-8 md:px-10">
                    <form method="POST" action="{{ route('register.submit') }}" class="space-y-5">
                    @csrf

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-[.72rem] font-bold uppercase tracking-[.16em] text-[#1a2e3b]">First Name</label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}"
                                @class([
                                    'w-full rounded-xl border bg-[#f7fbfe] px-4 py-3.5 text-[.95rem] text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:bg-white focus:ring-4 focus:ring-[#0086da]/10',
                                    'border-red-500' => $errors->has('first_name'),
                                    'border-[#cfe3f2]' => !$errors->has('first_name'),
                                ]) placeholder="Juan" required>
                            @error('first_name')
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-[.72rem] font-bold uppercase tracking-[.16em] text-[#1a2e3b]">Last Name</label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}"
                                @class([
                                    'w-full rounded-xl border bg-[#f7fbfe] px-4 py-3.5 text-[.95rem] text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:bg-white focus:ring-4 focus:ring-[#0086da]/10',
                                    'border-red-500' => $errors->has('last_name'),
                                    'border-[#cfe3f2]' => !$errors->has('last_name'),
                                ]) placeholder="Dela Cruz" required>
                            @error('last_name')
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-[.72rem] font-bold uppercase tracking-[.16em] text-[#1a2e3b]">Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            @class([
                                'w-full rounded-xl border bg-[#f7fbfe] px-4 py-3.5 text-[.95rem] text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:bg-white focus:ring-4 focus:ring-[#0086da]/10',
                                'border-red-500' => $errors->has('email'),
                                'border-[#cfe3f2]' => !$errors->has('email'),
                            ]) placeholder="john@example.com" required>
                        @error('email')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-[.72rem] font-bold uppercase tracking-[.16em] text-[#1a2e3b]">Mobile Number (Optional)</label>
                        <input type="text" name="mobile_number" value="{{ old('mobile_number') }}"
                            @class([
                                'w-full rounded-xl border bg-[#f7fbfe] px-4 py-3.5 text-[.95rem] text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:bg-white focus:ring-4 focus:ring-[#0086da]/10',
                                'border-red-500' => $errors->has('mobile_number'),
                                'border-[#cfe3f2]' => !$errors->has('mobile_number'),
                            ]) placeholder="09XXXXXXXXX">
                        @error('mobile_number')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-[.72rem] font-bold uppercase tracking-[.16em] text-[#1a2e3b]">Password</label>
                        <div class="relative">
                            <input type="password" id="register-password" name="password"
                                @class([
                                    'w-full rounded-xl border bg-[#f7fbfe] px-4 py-3.5 pr-12 text-[.95rem] text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:bg-white focus:ring-4 focus:ring-[#0086da]/10',
                                    'border-red-500' => $errors->has('password'),
                                    'border-[#cfe3f2]' => !$errors->has('password'),
                                ]) required minlength="8" placeholder="At least 8 characters">
                            <button type="button"
                                onclick="toggleRegisterPassword('register-password','register-eye-open','register-eye-closed')"
                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-[#6d8798] transition hover:text-[#1a2e3b]"
                                aria-label="Toggle password visibility">
                                <svg id="register-eye-closed" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg id="register-eye-open" class="hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.58 10.58a2 2 0 102.83 2.83"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.88 5.09A10.94 10.94 0 0112 5c5 0 9.27 3.11 11 7a12.62 12.62 0 01-3.04 4.19M6.1 6.1A12.84 12.84 0 001 12c1.73 3.89 6 7 11 7 1.65 0 3.23-.34 4.66-.95"></path>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-[.72rem] font-bold uppercase tracking-[.16em] text-[#1a2e3b]">Confirm Password</label>
                        <div class="relative">
                            <input type="password" id="register-password-confirmation" name="password_confirmation"
                                class="w-full rounded-xl border border-[#cfe3f2] bg-[#f7fbfe] px-4 py-3.5 pr-12 text-[.95rem] text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:bg-white focus:ring-4 focus:ring-[#0086da]/10"
                                required placeholder="Retype your password">
                            <button type="button"
                                onclick="toggleRegisterPassword('register-password-confirmation','register-confirm-eye-open','register-confirm-eye-closed')"
                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-[#6d8798] transition hover:text-[#1a2e3b]"
                                aria-label="Toggle confirm password visibility">
                                <svg id="register-confirm-eye-closed" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg id="register-confirm-eye-open" class="hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.58 10.58a2 2 0 102.83 2.83"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.88 5.09A10.94 10.94 0 0112 5c5 0 9.27 3.11 11 7a12.62 12.62 0 01-3.04 4.19M6.1 6.1A12.84 12.84 0 001 12c1.73 3.89 6 7 11 7 1.65 0 3.23-.34 4.66-.95"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="pt-2">
                        <div class="flex flex-col lg:items-start">
                            <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                            @error('g-recaptcha-response')
                                <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <button type="submit"
                        class="inline-flex w-full items-center justify-center rounded-sm bg-[#0086da] px-6 py-[13px] text-[.72rem] font-bold uppercase tracking-[.1em] text-white transition hover:bg-[#006ab0] focus:outline-none focus:ring-2 focus:ring-[#0086da]/20">
                        Create Account
                    </button>
                    </form>

                    <div class="mt-6 border-t border-[#e4eff8] pt-5 text-[.9rem] text-[#587189]">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-semibold text-[#0086da] transition hover:text-[#006ab0] hover:underline">Sign In</a>
                    </div>
                </section>
            </div>
        </div>
    </main>

    @include('components.homepage.footer-section')
</body>

</html>
