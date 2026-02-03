<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Sent - Tejadent</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-8 text-center">
        <div class="mx-auto mb-4 h-14 w-14 rounded-full bg-green-100 flex items-center justify-center">
            <svg class="h-7 w-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <h1 class="text-2xl font-extrabold text-gray-800">Reset Link Sent</h1>
        <p class="text-gray-500 mt-2 text-sm">
            We sent a password reset link to
            <span class="font-semibold text-gray-700">{{ $email }}</span>.
            Please check your inbox and follow the instructions.
        </p>
        <div class="mt-6">
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center w-full bg-[#0086DA] text-white font-bold py-3 rounded-lg hover:bg-[#0073A8] transition shadow-md">
                Back to Login
            </a>
        </div>
    </div>
</body>
</html>
