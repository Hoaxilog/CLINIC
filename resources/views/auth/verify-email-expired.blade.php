<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Link Expired - Tejadent</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md text-center">

        {{-- Icon --}}
        <div class="flex justify-center mb-5">
            <div class="bg-red-100 rounded-full p-4">
                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
        </div>

        <h2 class="text-2xl font-extrabold mb-2 text-gray-800">Verification Link Expired</h2>
        <p class="text-gray-500 text-sm mb-6">
            This email verification link has expired or is no longer valid.<br>
            Enter your email address below to receive a new one.
        </p>

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('failed'))
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('failed') }}
            </div>
        @endif

        {{-- Resend Form --}}
        <form method="POST" action="{{ route('verification.resend') }}" class="space-y-4 text-left">
            @csrf

            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email', session('expired_email')) }}"
                    required
                    placeholder="you@example.com"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 focus:border-[#0086DA] focus:outline-none focus:ring-2 focus:ring-[#0086DA]/20 @error('email') border-red-400 @enderror"
                >
                @error('email')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full bg-[#0086DA] text-white font-bold py-3 rounded-lg hover:bg-[#0073A8] transition shadow-md"
            >
                Send New Verification Email
            </button>
        </form>

        <a href="{{ route('login') }}" class="inline-block mt-4 w-full bg-white border-2 border-gray-300 text-gray-700 font-bold py-3 rounded-lg hover:bg-gray-50 transition">
            Return to Login
        </a>
    </div>
</body>
</html>
