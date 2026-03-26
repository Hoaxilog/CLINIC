<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TEJADENT</title>
    <meta name="theme-color" content="#0086DA">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.svg') }}">
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

        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear {
            display: none;
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
                    Patient Registration
                </div>
                <h1 class="text-[clamp(1.8rem,3.2vw,2.5rem)] font-extrabold leading-[1.1] tracking-[-.02em] text-[#1a2e3b]">Create your account</h1>
                <p class="mt-3 max-w-3xl text-[.9rem] leading-[1.8] text-[#587189]">
                    Use the same profile details you use for appointments so staff can verify and link your records faster.
                </p>
            </div>

            <div class="grid border border-[#e4eff8] bg-[#e4eff8] lg:grid-cols-[.9fr_1.1fr]">
                <section class="border-r border-[#e4eff8] bg-white px-6 py-7 sm:px-8 md:px-10">
                    <div class="mb-7 flex items-center gap-3 border-b border-[#e4eff8] pb-6">
                                                <a href="{{ route('home') }}" class="pointer-events-none flex shrink-0 items-center gap-3 no-underline">
                            <div class="flex h-[50px] w-[50px] shrink-0 items-center justify-center">
                                <svg width="56" height="45" viewBox="0 0 56 45" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <mask id="brand-logo-mask-register" mask-type="alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="56" height="45">
                                        <path
                                            d="M11.783 0.465134C6.04622 2.04593 1.64903 6.81758 0.396845 12.7602C-0.127324 15.307 -0.127324 16.9171 0.367724 19.3468C1.70727 25.6993 7.88082 33.5154 18.5972 42.444L21.0724 44.5225L21.3927 43.1173C22.1499 39.8972 23.402 37.9944 25.6152 36.7941C27.2751 35.8574 28.3525 35.9159 30.158 36.9698C31.5849 37.8187 33.2739 40.5412 33.7398 42.7367C33.9437 43.7321 34.1766 44.5225 34.264 44.5225C34.5261 44.5225 40.8161 39.0775 43.5243 36.5307C51.7363 28.7438 56.0461 20.9862 55.4637 15.0436C55.0269 10.711 53.2797 7.05178 50.2511 4.24147C44.2814 -1.37913 35.4579 -1.4084 29.5756 4.12438L27.7701 5.82227L25.9646 4.15365C22.9361 1.34335 19.4708 -0.00325012 15.3939 0.0552979C14.2 0.0552979 12.5692 0.260216 11.783 0.465134ZM32.7206 9.36442C38.7486 12.3504 41.1947 19.4932 38.1953 25.4066C37.2634 27.2801 34.7008 29.8269 32.808 30.7344C27.0712 33.4862 20.0532 31.0857 17.2867 25.4066C16.0054 22.7134 15.6851 20.6934 16.151 17.9417C16.7626 14.341 19.1796 11.0916 22.5284 9.42297C24.596 8.39838 25.4405 8.22274 28.2943 8.33983C30.4492 8.42765 31.119 8.57402 32.7206 9.36442Z"
                                            fill="black" />
                                        <path
                                            d="M24.0136 9.97903C21.0142 11.15 18.9757 13.2577 17.7235 16.4193C16.7917 18.7612 16.9664 22.3619 18.1021 24.616C19.2378 26.8116 21.2471 28.8022 23.3729 29.7975C24.9163 30.5001 25.4987 30.6172 27.7701 30.6172C29.9833 30.6172 30.653 30.5001 32.0217 29.8561C39.1271 26.4896 40.5831 17.5024 34.8755 12.2039C32.6624 10.1547 30.7113 9.39355 27.6828 9.39355C26.1976 9.42283 24.9745 9.59847 24.0136 9.97903ZM31.0316 13.4334C30.9151 14.0188 30.9151 15.0142 31.0025 15.5996L31.1772 16.6828L33.2739 16.7706L35.3414 16.8584V20.0493V23.2694L33.3613 23.2987C32.2838 23.328 31.3228 23.4451 31.2063 23.5329C31.119 23.65 31.0025 24.616 31.0025 25.6992L30.9734 27.6898L27.6828 27.7776L24.4213 27.8654V25.6699V23.5036L23.0526 23.328C22.2663 23.2401 21.3345 23.2109 20.9268 23.2401L20.1988 23.2987L20.1114 19.9907L20.0241 16.712H22.2372H24.4213V14.5165V12.321H27.7992H31.2063L31.0316 13.4334Z"
                                            fill="black" />
                                    </mask>
                                    <g mask="url(#brand-logo-mask-register)">
                                        <rect x="-25.5311" y="-23.4609" width="106.265" height="91.7739" fill="#0086DA" />
                                    </g>
                                </svg>
                            </div>
                            <div class="leading-[1.25]">
                                <div class="text-[.92rem] font-extrabold tracking-[.04em] text-[#1a2e3b]">TEJADA CLINIC</div>
                                <div class="text-[.57rem] font-semibold uppercase tracking-[.2em] text-[#0086da]">Dental Care</div>
                            </div>
                        </a>
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
                        @if ($hasMiddleNameColumn ?? false)
                            <div class="sm:col-span-2">
                                <label class="mb-2 block text-[.72rem] font-bold uppercase tracking-[.16em] text-[#1a2e3b]">Middle Name</label>
                                <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                                    @class([
                                        'w-full rounded-xl border bg-[#f7fbfe] px-4 py-3.5 text-[.95rem] text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:bg-white focus:ring-4 focus:ring-[#0086da]/10',
                                        'border-red-500' => $errors->has('middle_name'),
                                        'border-[#cfe3f2]' => !$errors->has('middle_name'),
                                    ]) placeholder="Santos">
                                @error('middle_name')
                                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
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
                        <label class="mb-2 block text-[.72rem] font-bold uppercase tracking-[.16em] text-[#1a2e3b]">Mobile Number</label>
                        <div class="flex min-w-0">
                            <span class="inline-flex shrink-0 items-center px-3 border border-r-0 border-[#cfe3f2] bg-[#f0f8fe] text-[#3d5a6e] text-sm font-semibold select-none rounded-l-xl {{ $errors->has('mobile_number') ? 'border-red-500' : '' }}">+63</span>
                            <input type="text" inputmode="numeric" maxlength="10" name="mobile_number" value="{{ old('mobile_number') }}"
                                @class([
                                    'min-w-0 w-full rounded-r-xl border bg-[#f7fbfe] px-4 py-3.5 text-[.95rem] text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:bg-white focus:ring-4 focus:ring-[#0086da]/10',
                                    'border-red-500' => $errors->has('mobile_number'),
                                    'border-[#cfe3f2]' => !$errors->has('mobile_number'),
                                ]) placeholder="9171234567" oninput="this.value=this.value.replace(/[^0-9]/g,'').replace(/^0+/,'').slice(0,10)" required>
                        </div>
                        @error('mobile_number')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    @if ($hasBirthDateColumn ?? false)
                        <div>
                            <label class="mb-2 block text-[.72rem] font-bold uppercase tracking-[.16em] text-[#1a2e3b]">Birth Date</label>
                            <input type="date" name="birth_date" value="{{ old('birth_date') }}" max="{{ now()->subDay()->toDateString() }}"
                                @class([
                                    'w-full rounded-xl border bg-[#f7fbfe] px-4 py-3.5 text-[.95rem] text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:bg-white focus:ring-4 focus:ring-[#0086da]/10',
                                    'border-red-500' => $errors->has('birth_date'),
                                    'border-[#cfe3f2]' => !$errors->has('birth_date'),
                                ]) required>
                            @error('birth_date')
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <div>
                        <label class="mb-2 block text-[.72rem] font-bold uppercase tracking-[.16em] text-[#1a2e3b]">Password</label>
                        <div class="relative">
                            <input type="password" id="register-password" name="password" autocomplete="new-password"
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
                            <input type="password" id="register-password-confirmation" name="password_confirmation" autocomplete="new-password"
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
