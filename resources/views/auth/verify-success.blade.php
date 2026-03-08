<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verified - Tejadent</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md text-center">
        <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-green-100 mb-6">
            <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <h2 class="text-3xl font-extrabold text-gray-800 mb-4">Verified!</h2>

        <p class="text-gray-600 mb-8 text-lg">
            Welcome, <span class="font-bold text-gray-800">{{ session('verified_email') ?? 'your email' }}</span>!
            <br>
            Your email address has been successfully verified. You can now access your dashboard and book appointments.
        </p>

        <a href="{{ route('login') }}"
            class="inline-block w-full bg-[#0086DA] text-white font-bold py-3 rounded-lg hover:bg-[#0073A8] transition shadow-md">
            Login to Continue
        </a>
    </div>
</body>

</html>
