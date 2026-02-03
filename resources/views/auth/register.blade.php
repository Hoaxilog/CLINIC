<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TEJADENT</title>
    @vite('resources/css/app.css')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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
                    <input type="password" name="password" 
                        class="@error('password') border-red-500 @else border-gray-200 @enderror w-full border-2 rounded-lg px-4 py-3 focus:outline-none focus:border-[#0086DA] transition bg-gray-50" 
                        required minlength="8" placeholder="At least 8 characters">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Confirm Password</label>
                    <input type="password" name="password_confirmation" 
                        class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:border-[#0086DA] transition bg-gray-50" 
                        required placeholder="Retype your password">
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