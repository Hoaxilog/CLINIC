<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TEJADENT</title>
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

<body class="min-h-screen bg-gray-100">
    <header class="sticky top-0 z-[100] border-b border-[#e4eff8] bg-white px-6 md:px-12 xl:px-20">
        <div class="relative mx-auto flex h-[70px] w-full max-w-[1400px] items-center justify-between">
            <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-3 no-underline">
                <div class="flex h-[38px] w-[38px] shrink-0 items-center justify-center">
                    <svg width="56" height="45" viewBox="0 0 56 45" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <mask id="register-nav-mask" mask-type="alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                            width="56" height="45">
                            <path
                                d="M11.783 0.465134C6.04622 2.04593 1.64903 6.81758 0.396845 12.7602C-0.127324 15.307 -0.127324 16.9171 0.367724 19.3468C1.70727 25.6993 7.88082 33.5154 18.5972 42.444L21.0724 44.5225L21.3927 43.1173C22.1499 39.8972 23.402 37.9944 25.6152 36.7941C27.2751 35.8574 28.3525 35.9159 30.158 36.9698C31.5849 37.8187 33.2739 40.5412 33.7398 42.7367C33.9437 43.7321 34.1766 44.5225 34.264 44.5225C34.5261 44.5225 40.8161 39.0775 43.5243 36.5307C51.7363 28.7438 56.0461 20.9862 55.4637 15.0436C55.0269 10.711 53.2797 7.05178 50.2511 4.24147C44.2814 -1.37913 35.4579 -1.4084 29.5756 4.12438L27.7701 5.82227L25.9646 4.15365C22.9361 1.34335 19.4708 -0.00325012 15.3939 0.0552979C14.2 0.0552979 12.5692 0.260216 11.783 0.465134ZM32.7206 9.36442C38.7486 12.3504 41.1947 19.4932 38.1953 25.4066C37.2634 27.2801 34.7008 29.8269 32.808 30.7344C27.0712 33.4862 20.0532 31.0857 17.2867 25.4066C16.0054 22.7134 15.6851 20.6934 16.151 17.9417C16.7626 14.341 19.1796 11.0916 22.5284 9.42297C24.596 8.39838 25.4405 8.22274 28.2943 8.33983C30.4492 8.42765 31.119 8.57402 32.7206 9.36442Z"
                                fill="black" />
                            <path
                                d="M24.0136 9.97903C21.0142 11.15 18.9757 13.2577 17.7235 16.4193C16.7917 18.7612 16.9664 22.3619 18.1021 24.616C19.2378 26.8116 21.2471 28.8022 23.3729 29.7975C24.9163 30.5001 25.4987 30.6172 27.7701 30.6172C29.9833 30.6172 30.653 30.5001 32.0217 29.8561C39.1271 26.4896 40.5831 17.5024 34.8755 12.2039C32.6624 10.1547 30.7113 9.39355 27.6828 9.39355C26.1976 9.42283 24.9745 9.59847 24.0136 9.97903ZM31.0316 13.4334C30.9151 14.0188 30.9151 15.0142 31.0025 15.5996L31.1772 16.6828L33.2739 16.7706L35.3414 16.8584V20.0493V23.2694L33.3613 23.2987C32.2838 23.328 31.3228 23.4451 31.2063 23.5329C31.119 23.65 31.0025 24.616 31.0025 25.6992L30.9734 27.6898L27.6828 27.7776L24.4213 27.8654V25.6699V23.5036L23.0526 23.328C22.2663 23.2401 21.3345 23.2109 20.9268 23.2401L20.1988 23.2987L20.1114 19.9907L20.0241 16.712H22.2372H24.4213V14.5165V12.321H27.7992H31.2063L31.0316 13.4334Z"
                                fill="black" />
                        </mask>
                        <g mask="url(#register-nav-mask)">
                            <rect x="-25.5311" y="-23.4609" width="106.265" height="91.7739" fill="#0086DA" />
                        </g>
                    </svg>
                </div>
                <div class="leading-[1.25]">
                    <div class="text-[.92rem] font-extrabold tracking-[.04em] text-[#1a2e3b]">TEJADA CLINIC</div>
                    <div class="text-[.57rem] font-semibold uppercase tracking-[.2em] text-[#0086da]">Dental Care</div>
                </div>
            </a>

            <nav class="hidden items-center gap-9 lg:flex">
                @foreach (['Services' => 'services', 'About' => 'about', 'Why Us' => 'why-us', 'Hours' => 'hours', 'Contact' => 'contact'] as $label => $id)
                    <a href="{{ route('home') }}#{{ $id }}"
                        class="text-[.72rem] font-semibold uppercase tracking-[.07em] text-[#1a2e3b] transition-colors duration-200 hover:text-[#0086da]">{{ $label }}</a>
                @endforeach
            </nav>

            <div class="hidden items-center gap-3 lg:flex">
                <a href="{{ route('login') }}"
                    class="inline-flex items-center gap-[9px] whitespace-nowrap border border-[#0086da] px-6 py-3 text-[.7rem] font-bold uppercase tracking-[.1em] text-[#0086da] transition duration-200 hover:bg-[#0086da] hover:text-white">
                    Login
                </a>
                <a href="{{ route('book') }}"
                    class="inline-flex items-center gap-[9px] whitespace-nowrap bg-[#0086da] px-6 py-3 text-[.7rem] font-bold uppercase tracking-[.1em] text-white transition duration-200 hover:bg-[#006ab0]">
                    Book Now
                </a>
            </div>

            <button id="register-menu-btn" aria-label="Toggle menu"
                class="flex flex-col items-end gap-[5px] border-none bg-transparent p-2 lg:hidden">
                <span class="h-[2px] w-[22px] bg-[#1a2e3b]"></span>
                <span class="h-[2px] w-[22px] bg-[#1a2e3b]"></span>
                <span class="h-[2px] w-[14px] bg-[#0086da]"></span>
            </button>

            <div id="register-mob-menu"
                class="absolute top-full right-0 left-0 z-[200] hidden border-t border-[#e4eff8] bg-white shadow-[0_8px_32px_rgba(0,0,0,.08)]">
                @foreach (['Services' => 'services', 'About' => 'about', 'Why Us' => 'why-us', 'Hours' => 'hours', 'Contact' => 'contact'] as $label => $id)
                    <a href="{{ route('home') }}#{{ $id }}"
                        class="block border-b border-[#e4eff8] px-7 py-[17px] text-[.75rem] font-semibold uppercase tracking-[.08em] text-[#1a2e3b] no-underline transition hover:bg-[#f0f8fe] hover:text-[#0086da]">{{ $label }}</a>
                @endforeach
                <a href="{{ route('login') }}"
                    class="block border-b border-[#e4eff8] px-7 py-[17px] text-[.75rem] font-semibold uppercase tracking-[.08em] text-[#1a2e3b] no-underline transition hover:bg-[#f0f8fe] hover:text-[#0086da]">Login</a>
                <div class="px-7 pt-5 pb-6">
                    <a href="{{ route('book') }}"
                        class="inline-flex w-full items-center justify-center gap-[9px] whitespace-nowrap bg-[#0086da] px-8 py-[15px] text-[.72rem] font-bold uppercase tracking-[.1em] text-white transition duration-200 hover:bg-[#006ab0]">
                        Book Appointment
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl flex max-w-4xl w-full overflow-hidden">
                <div class="w-1/2 relative bg-cover bg-center hidden lg:block">
                    <img src="{{ asset('login-image.png') }}" alt="Dental Clinic" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-blue-900 bg-opacity-20"></div>
                </div>

                <div class="w-full lg:w-1/2 p-10 md:p-16 flex flex-col justify-center">
                    <div class="text-center mb-10">
                        <h1 class="text-3xl font-extrabold text-gray-800 tracking-wide">
                            TEJA<span class="text-[#0086DA]">DENT</span>
                        </h1>
                        <p class="text-gray-500 mt-2 text-sm">Create your patient account</p>
                    </div>

                    <form method="POST" action="{{ route('register.submit') }}" class="space-y-6">
                        @csrf

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                @class([
                                    'w-full border-2 rounded-lg px-4 py-3 focus:outline-none focus:border-[#0086DA] transition bg-gray-50',
                                    'border-red-500' => $errors->has('email'),
                                    'border-gray-200' => !$errors->has('email'),
                                ]) placeholder="john@example.com" required>
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                            <div class="relative">
                                <input type="password" id="register-password" name="password"
                                    @class([
                                        'w-full border-2 rounded-lg px-4 pr-10 py-3 focus:outline-none focus:border-[#0086DA] transition bg-gray-50',
                                        'border-red-500' => $errors->has('password'),
                                        'border-gray-200' => !$errors->has('password'),
                                    ]) required minlength="8"
                                    placeholder="At least 8 characters">
                                <button type="button"
                                    onclick="toggleRegisterPassword('register-password','register-eye-open','register-eye-closed')"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 transition"
                                    aria-label="Toggle password visibility">
                                    <svg id="register-eye-closed" class="h-5 w-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                    <svg id="register-eye-open" class="h-5 w-5 hidden" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 3l18 18"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10.58 10.58a2 2 0 102.83 2.83"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9.88 5.09A10.94 10.94 0 0112 5c5 0 9.27 3.11 11 7a12.62 12.62 0 01-3.04 4.19M6.1 6.1A12.84 12.84 0 001 12c1.73 3.89 6 7 11 7 1.65 0 3.23-.34 4.66-.95">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Confirm Password</label>
                            <div class="relative">
                                <input type="password" id="register-password-confirmation"
                                    name="password_confirmation"
                                    class="w-full border-2 border-gray-200 rounded-lg px-4 pr-10 py-3 focus:outline-none focus:border-[#0086DA] transition bg-gray-50"
                                    required placeholder="Retype your password">
                                <button type="button"
                                    onclick="toggleRegisterPassword('register-password-confirmation','register-confirm-eye-open','register-confirm-eye-closed')"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 transition"
                                    aria-label="Toggle confirm password visibility">
                                    <svg id="register-confirm-eye-closed" class="h-5 w-5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                    <svg id="register-confirm-eye-open" class="h-5 w-5 hidden" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 3l18 18"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10.58 10.58a2 2 0 102.83 2.83"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9.88 5.09A10.94 10.94 0 0112 5c5 0 9.27 3.11 11 7a12.62 12.62 0 01-3.04 4.19M6.1 6.1A12.84 12.84 0 001 12c1.73 3.89 6 7 11 7 1.65 0 3.23-.34 4.66-.95">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex flex-col lg:items-center">
                            <div class="g-recaptcha " data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                            @error('g-recaptcha-response')
                                <p class="text-red-500 text-xs self-start mt-2 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="w-full bg-[#0086DA] hover:bg-[#0073A8] text-white font-bold py-3 px-6 rounded-lg text-lg transition duration-200 shadow-lg mt-4 transform hover:-translate-y-0.5">
                            CREATE ACCOUNT
                        </button>
                    </form>

                    <div class="mt-10 text-center">
                        <p class="text-gray-600 text-sm">Already have an account? <a href="{{ route('login') }}"
                                class="text-[#0086DA] font-bold hover:underline ml-1">Sign In</a></p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="border-t-[3px] border-t-[#006ab0] bg-[#0086da] px-6 py-10 md:px-12 xl:px-20">
        <div class="mx-auto flex w-full max-w-[1400px] flex-col items-center justify-between gap-4 md:flex-row">
            <p class="text-[.72rem] text-white/75">&copy; {{ date('Y') }} Tejada Clinic. All rights reserved.</p>
            <div class="flex items-center gap-4 text-[.72rem] text-white/75">
                <a href="{{ route('home') }}#services" class="transition hover:text-white">Services</a>
                <a href="{{ route('home') }}#contact" class="transition hover:text-white">Contact</a>
                <a href="{{ route('privacy-policy') }}" class="transition hover:text-white">Privacy Policy</a>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const menuBtn = document.getElementById('register-menu-btn');
            const mobMenu = document.getElementById('register-mob-menu');
            if (!menuBtn || !mobMenu) return;

            menuBtn.addEventListener('click', () => {
                const open = mobMenu.classList.toggle('hidden') === false;
                menuBtn.setAttribute('aria-expanded', String(open));
            });

            mobMenu.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    mobMenu.classList.add('hidden');
                    menuBtn.setAttribute('aria-expanded', 'false');
                });
            });
        });
    </script>
</body>

</html>
