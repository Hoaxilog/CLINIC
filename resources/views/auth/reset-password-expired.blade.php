<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Link Expired - Tejadent</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md text-center">

        {{-- Icon --}}
        <div class="flex justify-center mb-5">
            <div class="bg-red-100 rounded-full p-4">
                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
        </div>

        <h2 class="text-2xl font-extrabold mb-2 text-gray-800">Link Expired</h2>
        <p class="text-gray-500 text-sm mb-6">
            This password reset link has already been used or has expired.<br>
            Please request a new one.
        </p>

        <a href="{{ route('password.forgot') }}"
           class="inline-block w-full bg-[#0086DA] text-white font-bold py-3 rounded-lg hover:bg-[#0073A8] transition shadow-md">
            Request New Reset Link
        </a>

        @auth
        <p class="text-xs text-gray-400 mt-4">
            You are currently logged in. You can also change your password from your
            <a href="{{ route('profile.index') }}" class="text-[#0086DA] hover:underline">Profile</a>.
        </p>
        @endauth
    </div>
</body>
</html>
