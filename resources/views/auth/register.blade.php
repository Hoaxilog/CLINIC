<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TEJADENT</title>
    @vite('resources/css/app.css')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        function toggleSignupPassword() {
            const passwordField = document.getElementById('signup-password');
            const eyeOpen = document.getElementById('signup-eye-open');
            const eyeClosed = document.getElementById('signup-eye-closed');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeOpen.style.display = 'block';
                eyeClosed.style.display = 'none';
            } else {
                passwordField.type = 'password';
                eyeOpen.style.display = 'none';
                eyeClosed.style.display = 'block';
            }
        }
        function toggleSignupConfirmPassword() {
            const confirmPasswordField = document.getElementById('signup-confirm-password');
            const eyeOpen = document.getElementById('signup-confirm-eye-open');
            const eyeClosed = document.getElementById('signup-confirm-eye-closed');
            
            if (confirmPasswordField.type === 'password') {
                confirmPasswordField.type = 'text';
                eyeOpen.style.display = 'block';
                eyeClosed.style.display = 'none';
            } else {
                confirmPasswordField.type = 'password';
                eyeOpen.style.display = 'none';
                eyeClosed.style.display = 'block';
            }
        }
    </script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl flex max-w-7xl w-full overflow-hidden h-[600px] lg:h-[700px]">
            
            <!-- Left Side - Image (Large, Dominant) -->
            <div class="hidden lg:block lg:w-2/3 relative">
                <img src="{{ asset('images/49.png') }}" alt="Beautiful Smile - Dental Care" class="w-full h-full object-cover">
            </div>

            <!-- Right Side - Registration Form (Compact) -->
            <div class="w-full lg:w-1/3 bg-white p-8 md:p-10 flex flex-col justify-center overflow-y-auto">
                <!-- Simple Header -->
                <h2 class="text-2xl font-bold text-gray-900 mb-1">Sign Up</h2>
                <p class="text-gray-600 text-sm mb-8">Create your patient account</p>

                <!-- Form -->
                <form method="POST" action="{{ route('register.submit') }}" class="space-y-4">
                    @csrf
                    
                    <!-- Email Field -->
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" 
                            class="@error('email') border-red-500 @else border-gray-300 @enderror w-full border-2 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#0086DA] focus:ring-2 focus:ring-[#0086DA] focus:ring-opacity-20" 
                            placeholder="Email" required>
                        @error('email')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                        <div class="relative">
                            <input type="password" id="signup-password" name="password" 
                                class="@error('password') border-red-500 @else border-gray-300 @enderror w-full border-2 rounded-lg px-4 py-2.5 pr-10 text-sm focus:outline-none focus:border-[#0086DA] focus:ring-2 focus:ring-[#0086DA] focus:ring-opacity-20" 
                                required minlength="8" placeholder="Password">
                            <button type="button" onclick="toggleSignupPassword()" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 transition">
                                <svg id="signup-eye-closed" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg id="signup-eye-open" class="h-5 w-5 hidden" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49"/>
                                    <path d="M14.084 14.158a3 3 0 0 1-4.242-4.242"/>
                                    <path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143"/>
                                    <path d="m2 2 20 20"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Confirm Password Field -->
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Confirm Password</label>
                        <div class="relative">
                            <input type="password" id="signup-confirm-password" name="password_confirmation" 
                                class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 pr-10 text-sm focus:outline-none focus:border-[#0086DA] focus:ring-2 focus:ring-[#0086DA] focus:ring-opacity-20" 
                                required placeholder="Confirm Password">
                            <button type="button" onclick="toggleSignupConfirmPassword()" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 transition">
                                <svg id="signup-confirm-eye-closed" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg id="signup-confirm-eye-open" class="h-5 w-5 hidden" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49"/>
                                    <path d="M14.084 14.158a3 3 0 0 1-4.242-4.242"/>
                                    <path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143"/>
                                    <path d="m2 2 20 20"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- ReCAPTCHA -->
                    <div class="pt-2 pb-3">
                        <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                        @error('g-recaptcha-response')
                            <p class="text-red-600 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-[#0086DA] hover:bg-[#0073B8] text-white font-bold py-2.5 px-4 rounded-lg text-sm transition duration-200">
                        Sign Up
                    </button>
                </form>

                <!-- Sign In Link -->
                <div class="mt-6 text-center text-sm text-gray-600">
                    Already have an account? <a href="{{ route('login') }}" class="text-[#0086DA] font-bold hover:underline">Sign In</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>