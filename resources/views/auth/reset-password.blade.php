<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Password - Tejadent</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
        <h2 class="text-2xl font-extrabold mb-6 text-center text-gray-800">Set New Password</h2>

        @if (session('failed'))
            <div class="bg-red-50 text-red-700 p-3 rounded mb-4 text-sm border border-red-200">{{ session('failed') }}</div>
        @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            
            <!-- Email is required to verify identity -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                <input type="email" name="email" value="{{ request()->email }}" class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 bg-gray-50" readonly required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">New Password</label>
                <input type="password" name="password" class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:border-[#0086DA]" placeholder="Min. 8 characters" required minlength="8">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation" class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:border-[#0086DA]" placeholder="Retype password" required>
            </div>

            <button type="submit" class="w-full bg-[#0086DA] text-white font-bold py-3 rounded-lg hover:bg-[#0073A8] transition shadow-md">Reset Password</button>
        </form>
    </div>
</body>
</html>