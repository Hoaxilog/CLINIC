<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Password - Tejadent</title>
    @vite('resources/css/app.css')
    <style>
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear {
            display: none;
        }
    </style>
    <script>
        function toggleResetPassword(fieldId, eyeOpenId, eyeClosedId) {
            const passwordField = document.getElementById(fieldId);
            const eyeOpen = document.getElementById(eyeOpenId);
            const eyeClosed = document.getElementById(eyeClosedId);

            if (!passwordField || !eyeOpen || !eyeClosed) {
                return;
            }

            const isHidden = passwordField.type === 'password';
            passwordField.type = isHidden ? 'text' : 'password';

            eyeClosed.classList.toggle('hidden', isHidden);
            eyeOpen.classList.toggle('hidden', !isHidden);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const fieldConfigs = [
                {
                    inputId: 'reset-password',
                    errorId: 'reset-password-error',
                },
                {
                    inputId: 'reset-password-confirmation',
                    errorId: 'reset-password-confirmation-error',
                },
            ];

            fieldConfigs.forEach(({ inputId, errorId }) => {
                const input = document.getElementById(inputId);
                const error = document.getElementById(errorId);

                if (!input) {
                    return;
                }

                const clearErrorState = () => {
                    input.classList.remove('border-red-500');
                    input.classList.add('border-gray-200');

                    if (error) {
                        error.classList.add('hidden');
                    }
                };

                input.addEventListener('input', clearErrorState);
                input.addEventListener('focus', clearErrorState);
            });
        });
    </script>
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
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                <input type="email" name="email" value="{{ old('email', request()->email) }}" class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 bg-gray-50" readonly required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">New Password</label>
                <div class="relative">
                    <input
                        type="password"
                        id="reset-password"
                        name="password"
                        autocomplete="new-password"
                        @class([
                            'w-full border-2 rounded-lg px-4 pr-10 py-3 focus:outline-none transition',
                            'border-red-500 focus:border-red-500' => $errors->has('password'),
                            'border-gray-200 focus:border-[#0086DA]' => ! $errors->has('password'),
                        ])
                        placeholder="Min. 8 characters"
                        required
                        minlength="8"
                    >
                    <button
                        type="button"
                        onclick="toggleResetPassword('reset-password','reset-password-eye-open','reset-password-eye-closed')"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 transition"
                        aria-label="Toggle password visibility"
                    >
                        <svg id="reset-password-eye-closed" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <svg id="reset-password-eye-open" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.58 10.58a2 2 0 102.83 2.83"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.88 5.09A10.94 10.94 0 0112 5c5 0 9.27 3.11 11 7a12.62 12.62 0 01-3.04 4.19M6.1 6.1A12.84 12.84 0 001 12c1.73 3.89 6 7 11 7 1.65 0 3.23-.34 4.66-.95"></path>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p id="reset-password-error" class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Confirm Password</label>
                <div class="relative">
                    <input
                        type="password"
                        id="reset-password-confirmation"
                        name="password_confirmation"
                        autocomplete="new-password"
                        @class([
                            'w-full border-2 rounded-lg px-4 pr-10 py-3 focus:outline-none transition',
                            'border-red-500 focus:border-red-500' => $errors->has('password') || $errors->has('password_confirmation'),
                            'border-gray-200 focus:border-[#0086DA]' => ! $errors->has('password') && ! $errors->has('password_confirmation'),
                        ])
                        placeholder="Retype password"
                        required
                    >
                    <button
                        type="button"
                        onclick="toggleResetPassword('reset-password-confirmation','reset-password-confirm-eye-open','reset-password-confirm-eye-closed')"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 transition"
                        aria-label="Toggle confirm password visibility"
                    >
                        <svg id="reset-password-confirm-eye-closed" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <svg id="reset-password-confirm-eye-open" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.58 10.58a2 2 0 102.83 2.83"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.88 5.09A10.94 10.94 0 0112 5c5 0 9.27 3.11 11 7a12.62 12.62 0 01-3.04 4.19M6.1 6.1A12.84 12.84 0 001 12c1.73 3.89 6 7 11 7 1.65 0 3.23-.34 4.66-.95"></path>
                        </svg>
                    </button>
                </div>
                @if ($errors->has('password') || $errors->has('password_confirmation'))
                    <p id="reset-password-confirmation-error" class="text-red-500 text-xs mt-1">
                        {{ $errors->first('password_confirmation') ?: $errors->first('password') }}
                    </p>
                @endif
            </div>

            <button type="submit" class="w-full bg-[#0086DA] text-white font-bold py-3 rounded-lg hover:bg-[#0073A8] transition shadow-md">Reset Password</button>
        </form>
    </div>
</body>
</html>
