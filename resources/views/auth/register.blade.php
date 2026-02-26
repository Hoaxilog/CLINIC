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
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">
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
                        class="@error('email') border-red-500 @else border-gray-200 @enderror w-full border-2 rounded-lg px-4 py-3 focus:outline-none focus:border-[#0086DA] transition bg-gray-50" 
                        placeholder="john@example.com" required>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <div class="relative">
                        <input type="password" id="register-password" name="password" 
                            class="@error('password') border-red-500 @else border-gray-200 @enderror w-full border-2 rounded-lg px-4 pr-10 py-3 focus:outline-none focus:border-[#0086DA] transition bg-gray-50" 
                            required minlength="8" placeholder="At least 8 characters">
                        <button type="button"
                            onclick="toggleRegisterPassword('register-password','register-eye-open','register-eye-closed')"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 transition"
                            aria-label="Toggle password visibility">
                            <svg id="register-eye-closed" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg id="register-eye-open" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.58 10.58a2 2 0 102.83 2.83"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.88 5.09A10.94 10.94 0 0112 5c5 0 9.27 3.11 11 7a12.62 12.62 0 01-3.04 4.19M6.1 6.1A12.84 12.84 0 001 12c1.73 3.89 6 7 11 7 1.65 0 3.23-.34 4.66-.95"></path>
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
                        <input type="password" id="register-password-confirmation" name="password_confirmation" 
                            class="w-full border-2 border-gray-200 rounded-lg px-4 pr-10 py-3 focus:outline-none focus:border-[#0086DA] transition bg-gray-50" 
                            required placeholder="Retype your password">
                        <button type="button"
                            onclick="toggleRegisterPassword('register-password-confirmation','register-confirm-eye-open','register-confirm-eye-closed')"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 transition"
                            aria-label="Toggle confirm password visibility">
                            <svg id="register-confirm-eye-closed" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg id="register-confirm-eye-open" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.58 10.58a2 2 0 102.83 2.83"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.88 5.09A10.94 10.94 0 0112 5c5 0 9.27 3.11 11 7a12.62 12.62 0 01-3.04 4.19M6.1 6.1A12.84 12.84 0 001 12c1.73 3.89 6 7 11 7 1.65 0 3.23-.34 4.66-.95"></path>
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

                <button type="submit" class="w-full bg-[#0086DA] hover:bg-[#0073A8] text-white font-bold py-3 px-6 rounded-lg text-lg transition duration-200 shadow-lg mt-4 transform hover:-translate-y-0.5">
                    CREATE ACCOUNT
                </button>
            </form>

            <div class="mt-10 text-center">
                <p class="text-gray-600 text-sm">Already have an account? <a href="{{ route('login') }}" class="text-[#0086DA] font-bold hover:underline ml-1">Sign In</a></p>
            </div>
        </div>
    </div>
</body>
</html>
