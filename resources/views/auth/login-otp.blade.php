<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter OTP - Tejadent</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
    <div class="w-full max-w-lg rounded-2xl bg-white p-8 shadow-xl">
        <div class="mb-6 text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blue-100">
                <svg class="h-8 w-8 text-[#0086DA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 .967-.784 1.75-1.75 1.75S8.5 11.967 8.5 11s.784-1.75 1.75-1.75S12 10.033 12 11zm0 0V9m0 2h.01M5 20h14a2 2 0 002-2v-5a2 2 0 00-2-2h-1V9a6 6 0 10-12 0v2H5a2 2 0 00-2 2v5a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-extrabold text-gray-800">Enter Your OTP</h1>
            <p class="mt-3 text-sm text-gray-600">
                We sent a 6-digit code to <span class="font-semibold text-gray-800">{{ $email }}</span>.
                The code expires at {{ \Carbon\Carbon::parse($expiresAt)->format('g:i A') }}.
            </p>
        </div>

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

        <form method="POST" action="{{ route('login.otp.verify') }}" class="space-y-5">
            @csrf
            <div>
                <label class="mb-2 block text-sm font-bold uppercase tracking-wide text-gray-700">One-Time Password</label>
                <input type="hidden" id="otp" name="otp" value="">
                <div id="otp-group" class="grid grid-cols-6 gap-3">
                    <input type="text" inputmode="numeric" maxlength="1" autocomplete="one-time-code" class="otp-digit w-full rounded-lg border-2 border-gray-300 py-3 text-center text-2xl font-bold font-mono text-gray-800 focus:border-[#0086DA] focus:outline-none">
                    <input type="text" inputmode="numeric" maxlength="1" class="otp-digit w-full rounded-lg border-2 border-gray-300 py-3 text-center text-2xl font-bold font-mono text-gray-800 focus:border-[#0086DA] focus:outline-none">
                    <input type="text" inputmode="numeric" maxlength="1" class="otp-digit w-full rounded-lg border-2 border-gray-300 py-3 text-center text-2xl font-bold font-mono text-gray-800 focus:border-[#0086DA] focus:outline-none">
                    <input type="text" inputmode="numeric" maxlength="1" class="otp-digit w-full rounded-lg border-2 border-gray-300 py-3 text-center text-2xl font-bold font-mono text-gray-800 focus:border-[#0086DA] focus:outline-none">
                    <input type="text" inputmode="numeric" maxlength="1" class="otp-digit w-full rounded-lg border-2 border-gray-300 py-3 text-center text-2xl font-bold font-mono text-gray-800 focus:border-[#0086DA] focus:outline-none">
                    <input type="text" inputmode="numeric" maxlength="1" class="otp-digit w-full rounded-lg border-2 border-gray-300 py-3 text-center text-2xl font-bold font-mono text-gray-800 focus:border-[#0086DA] focus:outline-none">
                </div>
                @error('otp')
                    <p class="mt-2 text-xs font-bold text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full rounded-lg bg-[#0086DA] px-4 py-3 text-sm font-bold tracking-wide text-white transition hover:bg-[#0073A8]">
                VERIFY OTP
            </button>
        </form>

        <form method="POST" action="{{ route('login.otp.resend') }}" class="mt-4">
            @csrf
            <button type="submit" class="w-full rounded-lg border-2 border-gray-300 px-4 py-3 text-sm font-bold text-gray-700 transition hover:bg-gray-50">
                RESEND CODE
            </button>
        </form>

        <a href="{{ route('login') }}" class="mt-4 inline-flex w-full items-center justify-center text-sm font-semibold text-gray-500 hover:text-gray-700">
            Back to login
        </a>
    </div>
    <script>
        const otpInput = document.getElementById('otp');
        const otpGroup = document.getElementById('otp-group');
        const otpDigits = Array.from(document.querySelectorAll('.otp-digit'));

        function syncOtpValue() {
            otpInput.value = otpDigits.map((input) => input.value).join('');
        }

        function fillDigits(value) {
            const digits = value.replace(/\D/g, '').slice(0, otpDigits.length).split('');
            otpDigits.forEach((input, index) => {
                input.value = digits[index] ?? '';
            });
            syncOtpValue();
        }

        function handlePaste(event) {
            event.preventDefault();
            const pasted = (event.clipboardData || window.clipboardData).getData('text');
            fillDigits(pasted);
            const nextIndex = Math.min(
                Math.max(pasted.replace(/\D/g, '').length - 1, 0),
                otpDigits.length - 1
            );
            otpDigits[nextIndex].focus();
        }

        if (otpInput && otpDigits.length) {
            otpDigits.forEach((input, index) => {
                input.addEventListener('input', function () {
                    this.value = this.value.replace(/\D/g, '').slice(0, 1);
                    syncOtpValue();

                    if (this.value && index < otpDigits.length - 1) {
                        otpDigits[index + 1].focus();
                    }
                });

                input.addEventListener('keydown', function (event) {
                    if (event.key === 'Backspace' && !this.value && index > 0) {
                        otpDigits[index - 1].focus();
                    }

                    if (event.key === 'ArrowLeft' && index > 0) {
                        otpDigits[index - 1].focus();
                    }

                    if (event.key === 'ArrowRight' && index < otpDigits.length - 1) {
                        otpDigits[index + 1].focus();
                    }
                });

                input.addEventListener('paste', function (event) {
                    handlePaste(event);
                });
            });

            otpGroup.addEventListener('paste', handlePaste);
            otpDigits[0].focus();
        }
    </script>
</body>
</html>
