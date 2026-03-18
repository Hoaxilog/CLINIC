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
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-key-square-icon lucide-key-square">
                    <path
                        d="M12.4 2.7a2.5 2.5 0 0 1 3.4 0l5.5 5.5a2.5 2.5 0 0 1 0 3.4l-3.7 3.7a2.5 2.5 0 0 1-3.4 0L8.7 9.8a2.5 2.5 0 0 1 0-3.4z" />
                    <path d="m14 7 3 3" />
                    <path
                        d="m9.4 10.6-6.814 6.814A2 2 0 0 0 2 18.828V21a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h1a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h.172a2 2 0 0 0 1.414-.586l.814-.814" />
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
                <label class="mb-2 block text-sm font-bold uppercase tracking-wide text-gray-700">One-Time
                    Password</label>
                <input type="hidden" id="otp" name="otp" value="">
                <div id="otp-group" class="grid grid-cols-6 gap-3">
                    <input type="text" inputmode="numeric" maxlength="1" autocomplete="one-time-code"
                        class="otp-digit w-full rounded-lg border-2 border-gray-300 py-3 text-center text-2xl font-bold font-mono text-gray-800 focus:border-[#0086DA] focus:outline-none">
                    <input type="text" inputmode="numeric" maxlength="1"
                        class="otp-digit w-full rounded-lg border-2 border-gray-300 py-3 text-center text-2xl font-bold font-mono text-gray-800 focus:border-[#0086DA] focus:outline-none">
                    <input type="text" inputmode="numeric" maxlength="1"
                        class="otp-digit w-full rounded-lg border-2 border-gray-300 py-3 text-center text-2xl font-bold font-mono text-gray-800 focus:border-[#0086DA] focus:outline-none">
                    <input type="text" inputmode="numeric" maxlength="1"
                        class="otp-digit w-full rounded-lg border-2 border-gray-300 py-3 text-center text-2xl font-bold font-mono text-gray-800 focus:border-[#0086DA] focus:outline-none">
                    <input type="text" inputmode="numeric" maxlength="1"
                        class="otp-digit w-full rounded-lg border-2 border-gray-300 py-3 text-center text-2xl font-bold font-mono text-gray-800 focus:border-[#0086DA] focus:outline-none">
                    <input type="text" inputmode="numeric" maxlength="1"
                        class="otp-digit w-full rounded-lg border-2 border-gray-300 py-3 text-center text-2xl font-bold font-mono text-gray-800 focus:border-[#0086DA] focus:outline-none">
                </div>
                @error('otp')
                    <p class="mt-2 text-xs font-bold text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full rounded-lg bg-[#0086DA] px-4 py-3 text-sm font-bold tracking-wide text-white transition hover:bg-[#0073A8]">
                VERIFY OTP
            </button>
        </form>

        <form method="POST" action="{{ route('login.otp.resend') }}" class="mt-4">
            @csrf
            <button id="resendOtpButton" type="submit" @disabled(($otpSendCount ?? 1) >= ($otpMaxSends ?? 3) || ($resendCooldownRemaining ?? 0) > 0)
                data-cooldown="{{ (int) ($resendCooldownRemaining ?? 0) }}"
                data-max-reached="{{ (int) (($otpSendCount ?? 1) >= ($otpMaxSends ?? 3)) }}"
                class="w-full rounded-lg border-2 border-gray-300 px-4 py-3 text-sm font-bold text-gray-700 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-60">
                RESEND CODE
            </button>
            <p id="resendOtpHint" class="mt-2 text-center text-xs text-gray-500">
                OTP send {{ (int) ($otpSendCount ?? 1) }} of {{ (int) ($otpMaxSends ?? 3) }}
                @if (($otpSendCount ?? 1) >= ($otpMaxSends ?? 3))
                    - limit reached for this login session.
                @elseif(($resendCooldownRemaining ?? 0) > 0)
                    - resend available in {{ (int) ($resendCooldownRemaining ?? 0) }}s.
                @endif
            </p>
        </form>

        <a href="{{ route('login') }}"
            class="mt-4 inline-flex w-full items-center justify-center text-sm font-semibold text-gray-500 hover:text-gray-700">
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
            fillDigits(@json(old('otp', '')));

            otpDigits.forEach((input, index) => {
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/\D/g, '').slice(0, 1);
                    syncOtpValue();

                    if (this.value && index < otpDigits.length - 1) {
                        otpDigits[index + 1].focus();
                    }
                });

                input.addEventListener('keydown', function(event) {
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

                input.addEventListener('paste', function(event) {
                    handlePaste(event);
                });
            });

            otpGroup.addEventListener('paste', handlePaste);
            const firstEmptyIndex = otpDigits.findIndex((input) => input.value === '');
            otpDigits[firstEmptyIndex === -1 ? otpDigits.length - 1 : firstEmptyIndex].focus();
        }

        const resendButton = document.getElementById('resendOtpButton');
        const resendHint = document.getElementById('resendOtpHint');

        if (resendButton && resendHint) {
            let remaining = Number(resendButton.dataset.cooldown || 0);
            const maxReached = resendButton.dataset.maxReached === '1';

            if (maxReached) {
                resendHint.textContent = 'OTP send limit reached for this login session.';
            } else if (remaining > 0) {
                resendButton.disabled = true;

                const tick = () => {
                    if (remaining <= 0) {
                        resendButton.disabled = false;
                        resendHint.textContent = 'You can resend a new OTP now.';
                        return;
                    }

                    resendHint.textContent = `You can resend a new OTP in ${remaining}s.`;
                    remaining -= 1;
                    window.setTimeout(tick, 1000);
                };

                tick();
            }
        }
    </script>
</body>

</html>
