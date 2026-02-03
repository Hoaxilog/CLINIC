<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Tejadent - login or sign up</title>
    @vite('resources/css/app.css')
    
    <!-- 1. ADD THIS SCRIPT FROM GOOGLE -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl flex max-w-4xl w-full overflow-hidden">
            <!-- Left Side: Image -->
            <div class="w-1/2 relative bg-cover bg-center rounded-l-2xl hidden md:block">
                <img src="{{ asset('login-image.png') }}" alt="Dental Clinic" class="w-full h-full object-cover rounded-l-2xl">
            </div>

            <!-- Right Side: Form -->
            <form method="POST" action="/login" class="w-full md:w-1/2 p-12 flex flex-col justify-center items-center">
                @csrf
                
                <!-- Logo/Icon Section -->
                <div class="flex items-center mb-2">
                    <div class="relative w-15 h-15 rounded-full flex items-center justify-center">
                        <svg width="56" height="45" viewBox="0 0 56 45" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <mask id="mask0_128_176" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="56" height="45">
                                <path d="M11.783 0.465134C6.04622 2.04593 1.64903 6.81758 0.396845 12.7602C-0.127324 15.307 -0.127324 16.9171 0.367724 19.3468C1.70727 25.6993 7.88082 33.5154 18.5972 42.444L21.0724 44.5225L21.3927 43.1173C22.1499 39.8972 23.402 37.9944 25.6152 36.7941C27.2751 35.8574 28.3525 35.9159 30.158 36.9698C31.5849 37.8187 33.2739 40.5412 33.7398 42.7367C33.9437 43.7321 34.1766 44.5225 34.264 44.5225C34.5261 44.5225 40.8161 39.0775 43.5243 36.5307C51.7363 28.7438 56.0461 20.9862 55.4637 15.0436C55.0269 10.711 53.2797 7.05178 50.2511 4.24147C44.2814 -1.37913 35.4579 -1.4084 29.5756 4.12438L27.7701 5.82227L25.9646 4.15365C22.9361 1.34335 19.4708 -0.00325012 15.3939 0.0552979C14.2 0.0552979 12.5692 0.260216 11.783 0.465134ZM32.7206 9.36442C38.7486 12.3504 41.1947 19.4932 38.1953 25.4066C37.2634 27.2801 34.7008 29.8269 32.808 30.7344C27.0712 33.4862 20.0532 31.0857 17.2867 25.4066C16.0054 22.7134 15.6851 20.6934 16.151 17.9417C16.7626 14.341 19.1796 11.0916 22.5284 9.42297C24.596 8.39838 25.4405 8.22274 28.2943 8.33983C30.4492 8.42765 31.119 8.57402 32.7206 9.36442Z" fill="black"/>
                                <path d="M24.0136 9.97903C21.0142 11.15 18.9757 13.2577 17.7235 16.4193C16.7917 18.7612 16.9664 22.3619 18.1021 24.616C19.2378 26.8116 21.2471 28.8022 23.3729 29.7975C24.9163 30.5001 25.4987 30.6172 27.7701 30.6172C29.9833 30.6172 30.653 30.5001 32.0217 29.8561C39.1271 26.4896 40.5831 17.5024 34.8755 12.2039C32.6624 10.1547 30.7113 9.39355 27.6828 9.39355C26.1976 9.42283 24.9745 9.59847 24.0136 9.97903ZM31.0316 13.4334C30.9151 14.0188 30.9151 15.0142 31.0025 15.5996L31.1772 16.6828L33.2739 16.7706L35.3414 16.8584V20.0493V23.2694L33.3613 23.2987C32.2838 23.328 31.3228 23.4451 31.2063 23.5329C31.119 23.65 31.0025 24.616 31.0025 25.6992L30.9734 27.6898L27.6828 27.7776L24.4213 27.8654V25.6699V23.5036L23.0526 23.328C22.2663 23.2401 21.3345 23.2109 20.9268 23.2401L20.1988 23.2987L20.1114 19.9907L20.0241 16.712H22.2372H24.4213V14.5165V12.321H27.7992H31.2063L31.0316 13.4334Z" fill="black"/>
                            </mask>
                            <g mask="url(#mask0_128_176)">
                                <rect x="-25.5311" y="-23.4609" width="106.265" height="91.7739" fill="#0086DA"/>
                            </g>
                        </svg>
                    </div>
                </div>

                <p class="text-xl text-[#0086DA] font-semibold mb-2">
                    "Your smile is our priority"
                </p>
                <h1 class="text-5xl font-extrabold text-gray-800 mb-8 tracking-wide">
                    TEJA<span class="text-[#0086DA]">DENT</span>
                </h1>

                <!-- Username Field -->
                <div class="w-full max-w-xs mb-4">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </span>
                        <input value="{{ old('username') }}" type="text" placeholder="Username" name="username" class="@error('username') border-red-500 @enderror w-full pl-10 pr-4 py-3 rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 transition duration-200"/>
                    </div>
                </div>

                <!-- Password Field -->
                <div class="w-full max-w-xs mb-6">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </span>
                        <input type="password" placeholder="Password" name="password" class="@error('password') border-red-500 @enderror w-full pl-10 pr-10 py-3 rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 transition duration-200"/>
                    </div>
                     <!-- Error & Forgot Password Group -->
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

                <!-- 2. RECAPTCHA WIDGET HERE (Before Sign In Button) -->
                <!-- Replace 'your-site-key-here' with the actual key in your .env if preferred, or hardcode for dev -->
                <div class="w-full max-w-xs mb-4 flex justify-center">
                    <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                </div>

                <!-- Manual Sign In Button -->
                <button type="submit" class="w-full max-w-xs bg-[#0086DA] hover:bg-[#0073A8] text-white font-bold py-3 px-6 rounded-lg text-lg transition duration-200 shadow-md">
                    SIGN IN
                </button>

                <!-- OR Divider -->
                <div class="w-full max-w-xs flex items-center my-6">
                    <div class="flex-grow border-t border-gray-300"></div>
                    <span class="flex-shrink mx-4 text-gray-400 text-sm font-light">OR</span>
                    <div class="flex-grow border-t border-gray-300"></div>
                </div>

                <!-- Google Login Link -->
                <a href="{{ route('auth.google.redirect') }}" class="w-full max-w-xs flex items-center justify-center bg-white border-2 border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold py-3 px-6 rounded-lg text-lg transition duration-200 shadow-sm mb-3">
                    <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google Logo" class="w-6 h-6 mr-3">
                    Continue with Google
                </a>

                <div class="mt-6 text-center w-full max-w-xs">
                    <p class="text-gray-600 text-sm">
                        Don't have an account? 
                        <a href="{{ route('register') }}" class="text-[#0086DA] font-bold hover:underline">Sign Up</a>
                    </p>
                </div>

            </form>
        </div>
    </div>
</body>
</html>