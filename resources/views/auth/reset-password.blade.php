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
                <div class="relative">
                    <input type="password" id="reset-password" name="password" class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 pr-12 focus:outline-none focus:border-[#0086DA]" placeholder="Min. 8 characters" required minlength="8">
                    <button type="button" data-password-toggle="reset-password" class="absolute inset-y-0 right-3 inline-flex items-center text-gray-400 transition hover:text-gray-600" aria-label="Toggle new password visibility">
                        <span data-password-icon="show">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </span>
                        <span data-password-icon="hide" class="hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 3 18 18" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.58 10.58A2 2 0 0 0 12 14a2 2 0 0 0 1.42-.58" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.88 5.09A9.77 9.77 0 0 1 12 5c4.48 0 8.27 2.94 9.54 7a9.96 9.96 0 0 1-4.04 5.02" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6.23 6.23A9.96 9.96 0 0 0 2.46 12A9.97 9.97 0 0 0 12 19c1.61 0 3.13-.38 4.46-1.06" />
                            </svg>
                        </span>
                    </button>
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Confirm Password</label>
                <div class="relative">
                    <input type="password" id="reset-password-confirmation" name="password_confirmation" class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 pr-12 focus:outline-none focus:border-[#0086DA]" placeholder="Retype password" required>
                    <button type="button" data-password-toggle="reset-password-confirmation" class="absolute inset-y-0 right-3 inline-flex items-center text-gray-400 transition hover:text-gray-600" aria-label="Toggle password confirmation visibility">
                        <span data-password-icon="show">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </span>
                        <span data-password-icon="hide" class="hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 3 18 18" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.58 10.58A2 2 0 0 0 12 14a2 2 0 0 0 1.42-.58" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.88 5.09A9.77 9.77 0 0 1 12 5c4.48 0 8.27 2.94 9.54 7a9.96 9.96 0 0 1-4.04 5.02" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6.23 6.23A9.96 9.96 0 0 0 2.46 12A9.97 9.97 0 0 0 12 19c1.61 0 3.13-.38 4.46-1.06" />
                            </svg>
                        </span>
                    </button>
                </div>
                @error('password_confirmation')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-[#0086DA] text-white font-bold py-3 rounded-lg hover:bg-[#0073A8] transition shadow-md">Reset Password</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordToggleButtons = document.querySelectorAll('[data-password-toggle]');

            passwordToggleButtons.forEach((button) => {
                button.addEventListener('click', function() {
                    const input = document.getElementById(this.dataset.passwordToggle);
                    if (!input) {
                        return;
                    }

                    const isPassword = input.type === 'password';
                    input.type = isPassword ? 'text' : 'password';

                    this.querySelector('[data-password-icon="show"]')?.classList.toggle('hidden', isPassword);
                    this.querySelector('[data-password-icon="hide"]')?.classList.toggle('hidden', !isPassword);
                });
            });
        });
    </script>
</body>
</html>
