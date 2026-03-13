<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Tejadent - login or sign up</title>
    @vite('resources/css/app.css')

    @if (!empty($showCaptcha))
        <!-- reCAPTCHA (shown after 3 failed attempts) -->
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif

    <script>
        function toggleLoginPassword() {
            const passwordField = document.getElementById('login-password');
            const eyeOpen = document.getElementById('login-eye-open');
            const eyeClosed = document.getElementById('login-eye-closed');

            if (!passwordField || !eyeOpen || !eyeClosed) {
                return;
            }

            const isHidden = passwordField.type === 'password';
            passwordField.type = isHidden ? 'text' : 'password';

            eyeClosed.classList.toggle('hidden', isHidden);
            eyeOpen.classList.toggle('hidden', !isHidden);
        }
    </script>
    <style>
        .login-shell {
            background:
                radial-gradient(circle at 12% 10%, rgba(0, 134, 218, 0.10), transparent 32%),
                radial-gradient(circle at 90% 85%, rgba(0, 134, 218, 0.08), transparent 28%),
                #f7fbff;
        }

        .login-wrap {
            max-width: 1140px;
            width: 100%;
        }

        .login-card {
            border: 1px solid #d9e8f4;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        }

        .login-media-overlay {
            background: linear-gradient(to top, rgba(9, 38, 63, 0.45) 0%, rgba(9, 38, 63, 0.0) 45%);
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">
    @include('components.homepage.header-section')

    <main class="pt-20">
        <section class="login-shell min-h-[calc(100vh-80px)] px-4 py-10 md:px-8 md:py-12">
            <div class="login-wrap mx-auto">
                <div class="login-card grid overflow-hidden rounded-3xl bg-white md:grid-cols-2">
                    <div class="relative hidden min-h-[640px] md:block">
                        <img src="{{ asset('login-image.png') }}" alt="Dental Clinic"
                            class="h-full w-full object-cover">
                        <div class="login-media-overlay absolute inset-0"></div>
                        <div class="absolute bottom-8 left-8 right-8 rounded-2xl border border-white/30 bg-white/15 p-5 backdrop-blur-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/85">Tejada Dental Clinic</p>
                            <p class="mt-2 text-2xl font-bold text-white">Modern care, digital convenience, better smiles.</p>
                        </div>
                    </div>

                    <form method="POST" action="/login" class="flex w-full flex-col justify-center p-8 md:p-12">
                @csrf

                <div class="mb-8">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-[0.2em] text-[#0086DA]">Welcome Back</p>
                    <h1 class="font-['Outfit'] text-4xl font-bold leading-tight text-slate-900">Sign in to your patient portal</h1>
                    <p class="mt-2 text-sm text-slate-500">Manage appointments, view records, and stay updated with your care plan.</p>
                </div>

                <div class="w-full mb-4">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </span>
                        <input value="{{ old('email') }}" type="email" placeholder="Email address" name="email"
                            class="@error('email') border-red-500 @enderror w-full rounded-xl border border-slate-300 bg-slate-50 py-3 pl-10 pr-4 focus:border-[#0086DA] focus:bg-white focus:outline-none transition" />
                    </div>
                </div>

                <div class="w-full mb-6">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                        </span>
<input type="password" id="login-password" placeholder="Password" name="password"
                            class="@error('password') border-red-500 @enderror w-full rounded-xl border border-slate-300 bg-slate-50 py-3 pl-10 pr-10 focus:border-[#0086DA] focus:bg-white focus:outline-none transition" />
                        <button type="button" onclick="toggleLoginPassword()"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 transition"
                            aria-label="Toggle password visibility">
                            <svg id="login-eye-closed" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg id="login-eye-open" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.58 10.58a2 2 0 102.83 2.83"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.88 5.09A10.94 10.94 0 0112 5c5 0 9.27 3.11 11 7a12.62 12.62 0 01-3.04 4.19M6.1 6.1A12.84 12.84 0 001 12c1.73 3.89 6 7 11 7 1.65 0 3.23-.34 4.66-.95"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="flex flex-col mt-2">
                        @if (session('failed'))
                            <p class="text-red-500 text-sm mb-2 text-left">
                                {{ session('failed') }}
                            </p>
                        @endif
                        <div class="flex justify-end">
                            <a href="{{ route('password.forgot') }}" class="text-sm text-[#0086DA] hover:underline">
                                Forgot Password?
                            </a>
                        </div>
                    </div>
                </div>

                @if (!empty($showCaptcha))
                    <div class="w-full mb-4 flex justify-center">
                        <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                    </div>
                @endif

                <button type="submit"
                    class="w-full bg-[#0086DA] hover:bg-[#0073A8] text-white font-bold py-3 px-6 rounded-xl text-lg transition duration-200 shadow-md">
                    SIGN IN
                </button>

                <div class="w-full flex items-center my-6">
                    <div class="flex-grow border-t border-gray-300"></div>
                    <span class="flex-shrink mx-4 text-gray-400 text-sm font-light">OR</span>
                    <div class="flex-grow border-t border-gray-300"></div>
                </div>

                <a href="{{ route('auth.google.redirect') }}"
                    class="w-full flex items-center justify-center bg-white border border-slate-300 hover:bg-slate-50 text-gray-700 font-semibold py-3 px-6 rounded-xl text-lg transition duration-200 shadow-sm mb-3">
                    <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google Logo"
                        class="w-6 h-6 mr-3">
                    Continue with Google
                </a>

                <div class="mt-6 text-center w-full">
                    <p class="text-gray-600 text-sm">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="text-[#0086DA] font-bold hover:underline">Sign Up</a>
                    </p>
                </div>

            </form>
        </div>
            </div>
        </section>
    </main>

    @include('components.homepage.footer-section')
    @include('components.homepage.scripts-section')
</body>

</html>
