<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TEJADENT</title>
    <x-brand.meta />
    @vite('resources/css/app.css')
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

<body class="min-h-screen bg-[#eef5fb] text-[#1a2e3b]">
    @php($guestActionMode = 'login')
    @include('components.homepage.header-section')

    <main class="px-4 py-10 md:px-8 md:py-14 xl:py-16">
        <div class="mx-auto grid max-w-5xl overflow-hidden rounded-[28px] border border-[#d9e9f5] bg-white shadow-[0_30px_80px_rgba(13,60,91,.12)] lg:grid-cols-[1.02fr_.98fr]">
            <div class="hidden min-h-[760px] bg-gradient-to-br from-[#0086da] via-[#1593e5] to-[#0f6fb8] px-10 py-12 text-white lg:flex lg:flex-col lg:justify-between">
                <div>
                    <p class="text-[.68rem] font-bold uppercase tracking-[.22em] text-white/70">Create Account</p>
                    <h2 class="mt-4 max-w-md text-[2.2rem] font-extrabold leading-[1.08] tracking-[-.03em]">
                        Start your Tejada Clinic patient access in a few quick steps.
                    </h2>
                    <p class="mt-4 max-w-md text-[.94rem] leading-[1.9] text-white/80">
                        Register once to manage future bookings faster and keep your clinic information in one secure place.
                    </p>
                </div>

                <div class="space-y-4">
                    <div class="rounded-2xl border border-white/20 bg-white/10 px-5 py-5">
                        <p class="text-[.7rem] font-bold uppercase tracking-[.18em] text-white/65">Faster booking</p>
                        <p class="mt-2 text-[.95rem] leading-[1.7] text-white">Save time on future appointments with your account details ready to go.</p>
                    </div>
                    <div class="rounded-2xl border border-white/20 bg-white/10 px-5 py-5">
                        <p class="text-[.7rem] font-bold uppercase tracking-[.18em] text-white/65">Easy updates</p>
                        <p class="mt-2 text-[.95rem] leading-[1.7] text-white">Keep your contact information current and stay informed with clinic updates.</p>
                    </div>
                </div>
            </div>

            <div class="px-6 py-10 sm:px-10 md:px-14 lg:px-12 xl:px-14">
                <div class="mb-8 flex items-center justify-center lg:justify-start">
                    <x-brand.logo href="{{ route('home') }}" class="pointer-events-none" iconClass="flex h-[52px] w-[52px] shrink-0 items-center justify-center" />
                </div>

                <div class="mb-8 text-center lg:text-left">
                    <p class="text-[.72rem] font-bold uppercase tracking-[.22em] text-[#0086da]">Patient Registration</p>
                    <h1 class="mt-3 text-[clamp(2rem,4vw,3rem)] font-extrabold tracking-[-.03em] text-[#1a2e3b]">
                        Create your account
                    </h1>
                    <p class="mt-3 text-[.95rem] leading-[1.8] text-[#5a7689]">
                        Enter your details below to set up your Tejada Clinic login.
                    </p>
                </div>

                <form method="POST" action="{{ route('register.submit') }}" class="space-y-5">
                    @csrf

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
                        class="inline-flex w-full items-center justify-center rounded-xl bg-[#0086DA] px-6 py-3.5 text-[.95rem] font-bold uppercase tracking-[.12em] text-white transition hover:bg-[#0073A8] focus:outline-none focus:ring-4 focus:ring-[#0086da]/20">
                        Create Account
                    </button>
                </form>

                <p class="mt-8 text-center text-[.92rem] text-[#5a7689] lg:text-left">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-semibold text-[#0086da] transition hover:text-[#006ab0] hover:underline">Sign In</a>
                </p>
            </div>
        </div>
    </main>

    @include('components.homepage.footer-section')
</body>

</html>

