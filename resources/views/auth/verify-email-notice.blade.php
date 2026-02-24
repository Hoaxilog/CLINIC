<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Your Email - Tejadent</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-lg text-center">
        <!-- Animated Icon (Optional, using SVG) -->
        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-blue-100 mb-6">
            <svg class="h-10 w-10 text-[#0086DA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"></path>
            </svg>
        </div>

        <h2 class="text-3xl font-extrabold text-gray-800 mb-4">Verify Your Email</h2>
        
        <p class="text-gray-600 mb-6 text-lg">
            Thanks for signing up! We've sent a verification link to:
            <br>
            <span class="font-bold text-gray-800">{{ session('email') ?? 'your email address' }}</span>
        </p>

        <p class="text-gray-500 text-sm mb-8">
            Please check your inbox (and spam folder) and click the link to activate your account. You won't be able to log in until you verify.
        </p>

        <div class="space-y-4">
            <a href="{{ route('login') }}" class="inline-block w-full bg-white border-2 border-gray-300 text-gray-700 font-bold py-3 rounded-lg hover:bg-gray-50 transition">
                Return to Login
            </a>
        </div>
    </div>
</body>
</html>
