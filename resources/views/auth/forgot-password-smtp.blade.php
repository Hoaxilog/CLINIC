<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Forgot Password - Tejadent</title>
    @vite('resources/css/app.css')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md {{ session('reset_email') ? 'hidden' : '' }}">
        <div>
            <h2 class="text-2xl font-extrabold mb-2 text-center text-gray-800">Reset Password</h2>
            <p class="text-gray-500 text-center mb-6 text-sm">Enter your email and we'll send you a link to get back into your account.</p>
        </div>
        
        @if (session('failed'))
            <div class="bg-red-50 text-red-700 p-3 rounded mb-4 text-sm border border-red-200">{{ session('failed') }}</div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
            @csrf
            <div class="">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                <input type="email" name="email" class="@error('email') border-red-500 @else border-gray-200 @enderror w-full border-2 rounded-lg px-4 py-3 focus:outline-none focus:border-[#0086DA] transition" placeholder="patient@example.com" required>
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col">
                <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                @error('g-recaptcha-response')
                    <p class="text-red-500 self-start text-xs mt-2 font-semibold">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="w-full bg-[#0086DA] text-white font-bold py-3 rounded-lg hover:bg-[#0073A8] transition shadow-md">Send Reset Link</button>
        </form>
        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-[#0086DA] font-semibold">Back to Login</a>
        </div>
    </div>

    @if (session('reset_email'))
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-gray-100 "></div>
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-8 text-center">
                <div class="mx-auto mb-4 h-14 w-14 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="h-7 w-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h1 class="text-2xl font-extrabold text-gray-800">Reset Link Sent</h1>
                <p class="text-gray-500 mt-2 text-sm">
                    We sent a password reset link to
                    <span class="font-semibold text-gray-700">{{ session('reset_email') }}</span>.
                    Please check your inbox and follow the instructions.
                </p>
                <div class="mt-6">
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center w-full bg-[#0086DA] text-white font-bold py-3 rounded-lg hover:bg-[#0073A8] transition shadow-md">
                        Back to Login
                    </a>
                </div>
            </div>
        </div>
    @endif
</body>
</html>