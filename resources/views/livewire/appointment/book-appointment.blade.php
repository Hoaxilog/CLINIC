<div>
    @php
        $isPatientUser = auth()->check() && auth()->user()->role === 3;
        $patientName = $isPatientUser ? auth()->user()->username ?? 'Patient' : null;
        $sectionClass = $isPatientUser ? 'bg-white py-10 md:py-14' : 'bg-white py-10 md:py-14';
        $cardClass = $isPatientUser
            ? 'rounded-3xl border border-slate-200/80 bg-white/95 p-6 md:p-8 shadow-[0_22px_50px_-32px_rgba(2,132,199,0.45)]'
            : 'rounded-3xl border border-slate-200/80 bg-white/95 p-6 md:p-8 shadow-[0_22px_50px_-32px_rgba(2,132,199,0.45)]';
        $headingClass = $isPatientUser
            ? 'text-xl md:text-2xl font-semibold mb-6 flex items-center gap-3 text-slate-900'
            : 'text-xl md:text-2xl font-semibold mb-6 flex items-center gap-3 text-[#111827]';
        $badgeClass = 'bg-sky-600';
        $inputClass = $isPatientUser
            ? 'w-full border border-slate-200 rounded-lg p-3 text-sm md:text-base focus:ring-2 focus:ring-sky-500 focus:border-sky-500'
            : 'w-full border border-[#D1D5DB] rounded-lg p-3 text-sm md:text-base focus:ring-2 focus:ring-sky-500 focus:border-sky-500';
        $selectClass = $isPatientUser
            ? 'w-full border border-slate-200 rounded-lg p-3 mb-6 text-sm md:text-base bg-white focus:ring-2 focus:ring-sky-500 focus:border-sky-500'
            : 'w-full border border-[#D1D5DB] rounded-lg p-3 mb-6 text-sm md:text-base bg-white focus:ring-2 focus:ring-sky-500 focus:border-sky-500';
        $mutedLabelClass = $isPatientUser
            ? 'text-xs uppercase tracking-[0.2em] text-slate-500 mb-3'
            : 'text-xs uppercase tracking-[0.2em] text-[#6B7280] mb-3';
        $primaryButtonClass = $isPatientUser
            ? 'w-full mt-8 py-3.5 bg-sky-600 text-white text-sm md:text-base font-semibold rounded-lg hover:bg-sky-700 transition'
            : 'w-full mt-8 py-3.5 bg-sky-600 text-white text-sm md:text-base font-semibold rounded-lg hover:bg-sky-700 transition';
        $desktopDatePickerClass = $errors->has('selectedDate')
            ? 'hidden md:block border border-red-500 p-4 rounded-xl bg-white'
            : 'hidden md:block border border-[#E5E7EB] p-4 rounded-xl bg-white';
        $slotGridClass = $errors->has('selectedSlot')
            ? 'grid grid-cols-2 gap-2 rounded-xl p-1 border border-red-500'
            : 'grid grid-cols-2 gap-2 rounded-xl p-1';
        $successRingClass = $isPatientUser ? 'bg-sky-50 text-sky-700' : 'bg-sky-50 text-sky-700';
        $successBorderClass = $isPatientUser ? 'border-sky-100' : 'border-sky-100';
        $successButtonClass = $isPatientUser
            ? 'mt-6 inline-flex items-center justify-center px-4 py-2 rounded-lg bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700 transition'
            : 'mt-6 inline-flex items-center justify-center px-4 py-2 rounded-lg bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700 transition';
    @endphp
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <section class="{{ $sectionClass }} min-h-screen {{ $guestOtpStepActive ? 'flex items-center' : '' }}">
        <div class="mx-auto max-w-7xl px-4 w-full {{ $guestOtpStepActive ? 'py-8 md:py-12' : '' }}">

            @if (session()->has('success'))
                <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
                    <div
                        class="bg-white rounded-2xl shadow-xl border border-emerald-100 w-full max-w-md p-6 text-center">
                        <div
                            class="mx-auto mb-4 h-12 w-12 rounded-full {{ $successRingClass }} flex items-center justify-center text-2xl">
                            &#10003;
                        </div>
                        <h3 class="text-lg font-semibold text-[#111827]">Appointment booked</h3>
                        <p class="text-sm text-[#4B5563] mt-2">{{ session('success') }}</p>
                        <button type="button" class="{{ $successButtonClass }}"
                            onclick="this.closest('.fixed')?.remove()">
                            Ok
                        </button>
                    </div>
                </div>
            @endif

            <form wire:submit.prevent="bookAppointment"
                class="grid grid-cols-1 {{ $guestOtpStepActive ? 'gap-10' : 'gap-8' }}" id="bookingForm">
                @if (!$guestOtpStepActive)
                    <div class="{{ $cardClass }}">
                        <h3 class="{{ $headingClass }}">
                            <span
                                class="{{ $badgeClass }} text-white w-8 h-8 rounded-full flex items-center justify-center text-sm">1</span>
                            Patient Details
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">
                                    First Name <span class="text-red-600">*</span>
                                </label>
                                <input type="text" wire:model="first_name" data-validate-field="first_name"
                                    placeholder="First Name"
                                    class="{{ $inputClass }} @error('first_name') border-red-500 focus:border-red-500 focus:ring-red-200 @enderror">
                                @error('first_name')
                                    <div class="text-sm text-red-600 mt-1 validation-error" data-error-for="first_name">
                                        {{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">
                                    Last Name <span class="text-red-600">*</span>
                                </label>
                                <input type="text" wire:model="last_name" data-validate-field="last_name"
                                    placeholder="Last Name"
                                    class="{{ $inputClass }} @error('last_name') border-red-500 focus:border-red-500 focus:ring-red-200 @enderror">
                                @error('last_name')
                                    <div class="text-sm text-red-600 mt-1 validation-error" data-error-for="last_name">
                                        {{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">
                                    Age
                                </label>
                                <input type="number" wire:model="age" data-validate-field="age" placeholder="Age"
                                    class="{{ $inputClass }} @error('age') border-red-500 focus:border-red-500 focus:ring-red-200 @enderror">
                                @error('age')
                                    <div class="text-sm text-red-600 mt-1 validation-error" data-error-for="age">
                                        {{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">
                                    Contact Number <span class="text-red-600">*</span>
                                </label>
                                <input type="number" wire:model="contact_number" data-validate-field="contact_number"
                                    placeholder="Contact number"
                                    class="{{ $inputClass }} @error('contact_number') border-red-500 focus:border-red-500 focus:ring-red-200 @enderror">
                                @error('contact_number')
                                    <div class="text-sm text-red-600 mt-1 validation-error" data-error-for="contact_number">
                                        {{ $message }}</div>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="mb-1 block text-sm font-medium text-slate-700">
                                    Email <span class="text-red-600">*</span>
                                </label>
                                <input type="email" wire:model="email" data-validate-field="email" placeholder="Email"
                                    class="{{ $inputClass }} @error('email') border-red-500 focus:border-red-500 focus:ring-red-200 @enderror">
                                @error('email')
                                    <div class="text-sm text-red-600 mt-1 validation-error" data-error-for="email">
                                        {{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="{{ $cardClass }}">
                        <h3 class="{{ $headingClass }}">
                            <span
                                class="{{ $badgeClass }} text-white w-8 h-8 rounded-full flex items-center justify-center text-sm">2</span>
                            Select Appointment
                        </h3>
                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            Service <span class="text-red-600">*</span>
                        </label>
                        <select wire:model="service_id" data-validate-field="service_id"
                            class="{{ $selectClass }} @error('service_id') border-red-500 focus:border-red-500 focus:ring-red-200 @enderror">
                            <option value="">Select Service</option>
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}">{{ $service->service_name }}</option>
                            @endforeach
                        </select>
                        @error('service_id')
                            <div class="text-sm text-red-600 mb-4 validation-error" data-error-for="service_id">
                                {{ $message }}</div>
                        @enderror

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <p class="{{ $mutedLabelClass }}">Pick a Date <span class="text-red-600">*</span></p>
                                <div class="md:hidden">
                                    <input type="date" wire:model.live="selectedDate"
                                        data-validate-field="selectedDate" min="{{ now()->toDateString() }}"
                                        aria-label="Pick a date"
                                        class="{{ $inputClass }} @error('selectedDate') border-red-500 focus:border-red-500 focus:ring-red-200 @enderror">
                                    <p class="mt-1 text-xs text-slate-500">Tap to select a date.</p>
                                </div>
                                <div class="{{ $desktopDatePickerClass }}" data-validate-field="selectedDate"
                                    wire:ignore>
                                    <div class="flex justify-between items-center mb-4">
                                        <button type="button" id="prevMonth"
                                            class="w-8 h-8 rounded-full border border-[#E5E7EB] text-[#374151] hover:bg-[#F3F4F6] transition">
                                            &lsaquo;
                                        </button>
                                        <h4 id="monthYear"
                                            class="text-sm font-semibold uppercase tracking-[0.2em] text-[#111827]">
                                        </h4>
                                        <button type="button" id="nextMonth"
                                            class="w-8 h-8 rounded-full border border-[#E5E7EB] text-[#374151] hover:bg-[#F3F4F6] transition">
                                            &rsaquo;
                                        </button>
                                    </div>
                                    <div
                                        class="grid grid-cols-7 gap-1.5 mb-2 text-center text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                                        <div>Sun</div>
                                        <div>Mon</div>
                                        <div>Tue</div>
                                        <div>Wed</div>
                                        <div>Thu</div>
                                        <div>Fri</div>
                                        <div>Sat</div>
                                    </div>
                                    <div id="calendarDays" class="grid grid-cols-7 gap-1.5"></div>
                                </div>
                                @error('selectedDate')
                                    <div class="text-sm text-red-600 mt-2 validation-error" data-error-for="selectedDate">
                                        {{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <p class="{{ $mutedLabelClass }}">Select a Time <span class="text-red-600">*</span>
                                </p>
                                <div class="{{ $slotGridClass }}"
                                    data-validate-field="selectedSlot" wire:loading.remove wire:target="selectedDate">
                                    @forelse ($availableSlots as $slot)
                                        @php
                                            $isFull = !empty($slot['is_full']);
                                            $isPastSlot = !empty($slot['is_past']);
                                            $isBlocked = !empty($slot['is_blocked']);
                                            $isDisabled = $isFull || $isPastSlot || $isBlocked;
                                        @endphp
                                        <label class="{{ $isDisabled ? 'cursor-not-allowed' : 'cursor-pointer' }}">
                                            <input type="radio" name="selectedSlot" wire:model="selectedSlot"
                                                data-validate-field="selectedSlot"
                                                value="{{ $slot['value'] }}" @disabled($isDisabled)
                                                class="peer sr-only">
                                            <div
                                                class="text-center py-2 rounded-lg border text-sm font-medium transition-all {{ $isDisabled ? 'border-[#E5E7EB] text-[#C7CCD1] bg-[#F9FAFB]' : 'border-[#E5E7EB] text-[#374151] peer-checked:bg-sky-600 peer-checked:text-white' }}">
                                                {{ $slot['time'] }}{{ $isFull ? ' (Full)' : '' }}
                                            </div>
                                        </label>
                                    @empty
                                        @php
                                            $placeholderSlots = [
                                                '09:00 AM',
                                                '10:00 AM',
                                                '11:00 AM',
                                                '12:00 PM',
                                                '01:00 PM',
                                                '02:00 PM',
                                                '03:00 PM',
                                                '04:00 PM',
                                                '05:00 PM',
                                                '06:00 PM',
                                                '07:00 PM',
                                                '08:00 PM',
                                            ];
                                        @endphp
                                        @foreach ($placeholderSlots as $placeholder)
                                            <div
                                                class="text-center py-2 rounded-lg border border-[#E5E7EB] text-sm font-medium text-[#C7CCD1] bg-[#F9FAFB] cursor-not-allowed">
                                                {{ $placeholder }}
                                            </div>
                                        @endforeach
                                    @endforelse
                                </div>
                                <div wire:loading.flex wire:target="selectedDate"
                                    class="mt-3 min-h-[170px] items-center justify-center">
                                    <svg class="h-6 w-6 animate-spin text-sky-700" viewBox="0 0 24 24" fill="none"
                                        aria-hidden="true">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-opacity="0.2" stroke-width="4"></circle>
                                        <path d="M22 12a10 10 0 0 1-10 10" stroke="currentColor" stroke-width="4"
                                            stroke-linecap="round"></path>
                                    </svg>
                                </div>
                                @error('selectedSlot')
                                    <div class="text-sm text-red-600 mt-2 validation-error" data-error-for="selectedSlot">
                                        {{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        @guest
                            <div class="mt-8" wire:ignore>
                                <div id="recaptcha-container" class="g-recaptcha"
                                    data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                            </div>
                            <input type="hidden" id="recaptchaToken" wire:model.defer="recaptchaToken">
                            @error('recaptcha')
                                <div class="text-sm text-red-600 mt-2 validation-error" data-error-for="recaptcha">
                                    {{ $message }}</div>
                            @enderror
                        @endguest

                        <button type="submit" onclick="setRecaptchaToken()"
                            class="{{ $primaryButtonClass }} flex items-center justify-center"
                            wire:loading.attr="disabled" wire:target="bookAppointment">
                            <span class="text-center" wire:loading.remove wire:target="bookAppointment">Confirm
                                Appointment</span>
                            <span wire:loading wire:target="bookAppointment"
                                class="inline-flex items-center justify-center">
                                <svg class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none"
                                    aria-hidden="true">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-opacity="0.2" stroke-width="4"></circle>
                                    <path d="M22 12a10 10 0 0 1-10 10" stroke="currentColor" stroke-width="4"
                                        stroke-linecap="round"></path>
                                </svg>
                            </span>
                        </button>
                    </div>
                @endif

                @guest
                    @if ($guestOtpStepActive)
                        <div class="max-w-2xl mx-auto rounded-xl border border-slate-200 bg-white p-7 md:p-10 shadow-none">
                            <h3 class="{{ $headingClass }} mb-4">
                                <span
                                    class="{{ $badgeClass }} text-white w-8 h-8 rounded-full flex items-center justify-center text-sm">3</span>
                                Verify Your Email
                            </h3>
                            <p class="text-base leading-relaxed text-slate-600">
                                We sent a 6-digit code to <strong>{{ $email }}</strong>. Enter it below to finish
                                booking.
                            </p>
                            @if (session()->has('otp_success'))
                                <p class="mt-4 text-sm font-semibold text-emerald-700">{{ session('otp_success') }}</p>
                            @endif

                            <div class="mt-8 flex flex-col sm:flex-row gap-4 sm:items-end">
                                <input type="text" inputmode="numeric" maxlength="6"
                                    wire:model.defer="guestEmailOtp" data-validate-field="guestEmailOtp"
                                    placeholder="OTP"
                                    class="{{ $inputClass }} h-12 rounded-md text-base tracking-[0.35em] text-center sm:max-w-[260px] @error('guestEmailOtp') border-red-500 focus:border-red-500 focus:ring-red-200 @enderror">
                                <button type="button" wire:click="verifyGuestEmailOtp" wire:loading.attr="disabled"
                                    wire:target="verifyGuestEmailOtp"
                                    class="h-12 px-7 rounded-md bg-sky-600 text-white text-sm md:text-base font-semibold hover:bg-sky-700 transition cursor-pointer disabled:opacity-70 disabled:cursor-not-allowed inline-flex items-center justify-center">
                                    <span wire:loading.remove wire:target="verifyGuestEmailOtp">Verify & Book</span>
                                    <span wire:loading wire:target="verifyGuestEmailOtp"
                                        class="inline-flex items-center gap-2">
                                        <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"
                                            aria-hidden="true">
                                            <circle cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-opacity="0.2" stroke-width="4"></circle>
                                            <path d="M22 12a10 10 0 0 1-10 10" stroke="currentColor" stroke-width="4"
                                                stroke-linecap="round"></path>
                                        </svg>
                                        Verifying...
                                    </span>
                                </button>
                            </div>

                            @error('guestEmailOtp')
                                <div class="text-sm text-red-600 mt-3 validation-error" data-error-for="guestEmailOtp">
                                    {{ $message }}</div>
                            @enderror

                            <div class="mt-8 flex flex-wrap gap-3">
                                <button type="button" wire:click="sendGuestEmailOtp" wire:loading.attr="disabled"
                                    wire:target="sendGuestEmailOtp"
                                    class="h-11 px-5 rounded-md border border-sky-200 bg-white text-sky-700 text-sm md:text-base font-semibold hover:bg-sky-50 transition cursor-pointer disabled:opacity-70 disabled:cursor-not-allowed inline-flex items-center justify-center">
                                    <span wire:loading.remove wire:target="sendGuestEmailOtp">Resend OTP</span>
                                    <span wire:loading wire:target="sendGuestEmailOtp">Sending...</span>
                                </button>
                                <button type="button" wire:click="cancelGuestOtpStep"
                                    class="h-11 px-5 rounded-md border border-slate-200 bg-white text-slate-700 text-sm md:text-base font-semibold hover:bg-slate-50 transition cursor-pointer inline-flex items-center justify-center">
                                    Back to form
                                </button>
                            </div>
                        </div>
                    @endif
                @endguest
            </form>
        </div>
    </section>

    <script>
        let currentDate = new Date();
        let selectedDate = @js($selectedDate);
        const selectedDayClass = @json($isPatientUser ? 'bg-sky-600 text-white' : 'bg-sky-600 text-white');
        const activeDayClass = @json($isPatientUser ? 'bg-white text-slate-700 hover:bg-slate-100' : 'bg-white text-[#374151] hover:bg-[#F3F4F6]');
        const disabledDayClass = @json($isPatientUser ? 'bg-slate-50 text-slate-300 cursor-not-allowed' : 'bg-[#F9FAFB] text-[#C7CCD1] cursor-not-allowed');

        function formatDateLocal(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function syncCalendarToSelectedDate() {
            if (!selectedDate) return;
            const parsed = new Date(`${selectedDate}T00:00:00`);
            if (!Number.isNaN(parsed.getTime())) {
                currentDate = new Date(parsed.getFullYear(), parsed.getMonth(), 1);
            }
        }

        function renderCalendar() {
            const monthYearEl = document.getElementById('monthYear');
            const calendarDays = document.getElementById('calendarDays');
            if (!monthYearEl || !calendarDays) return;

            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            const today = new Date();
            const todayStr = formatDateLocal(today);
            const viewingIsPastMonth =
                year < today.getFullYear() || (year === today.getFullYear() && month < today.getMonth());
            const viewingIsFutureMonth =
                year > today.getFullYear() || (year === today.getFullYear() && month > today.getMonth());
            monthYearEl.textContent = currentDate.toLocaleDateString('en-US', {
                month: 'long',
                year: 'numeric'
            }).toUpperCase();

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            calendarDays.innerHTML = '';

            for (let i = 0; i < firstDay; i++) {
                calendarDays.innerHTML += '<div></div>';
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month, day);
                const dateStr = formatDateLocal(date);
                const dayButton = document.createElement('button');
                dayButton.type = 'button';
                dayButton.textContent = day;
                const isPastDate =
                    viewingIsPastMonth ||
                    (!viewingIsFutureMonth && dateStr < todayStr);
                dayButton.disabled = isPastDate;
                dayButton.className =
                    `p-2 text-xs font-semibold rounded-md border border-[#E5E7EB] ${
                        isPastDate
                            ? disabledDayClass
                            : selectedDate === dateStr
                                ? selectedDayClass
                                : activeDayClass
                    }`;

                dayButton.addEventListener('click', () => {
                    if (isPastDate) return;
                    selectedDate = dateStr;
                    @this.set('selectedDate', dateStr); // Sync with Livewire
                    dismissValidationFor('selectedDate');
                    renderCalendar();
                });
                calendarDays.appendChild(dayButton);
            }
        }

        function bindCalendarNavigation() {
            const prevMonthBtn = document.getElementById('prevMonth');
            const nextMonthBtn = document.getElementById('nextMonth');

            if (prevMonthBtn && !prevMonthBtn.dataset.bound) {
                prevMonthBtn.dataset.bound = '1';
                prevMonthBtn.addEventListener('click', () => {
                    currentDate.setMonth(currentDate.getMonth() - 1);
                    renderCalendar();
                });
            }

            if (nextMonthBtn && !nextMonthBtn.dataset.bound) {
                nextMonthBtn.dataset.bound = '1';
                nextMonthBtn.addEventListener('click', () => {
                    currentDate.setMonth(currentDate.getMonth() + 1);
                    renderCalendar();
                });
            }
        }

        function ensureRecaptcha() {
            if (typeof grecaptcha === 'undefined' || typeof grecaptcha.render !== 'function') return;
            const container = document.getElementById('recaptcha-container');
            if (!container) return;
            const hasIframe = container.querySelector('iframe');
            if (!hasIframe && !container.getAttribute('data-rendered')) {
                grecaptcha.render(container, {
                    sitekey: container.getAttribute('data-sitekey')
                });
                container.setAttribute('data-rendered', 'true');
            }
        }

        function setRecaptchaToken() {
            if (typeof grecaptcha === 'undefined' || typeof grecaptcha.getResponse !== 'function') return;
            const token = grecaptcha.getResponse();
            const input = document.getElementById('recaptchaToken');
            if (input) {
                input.value = token;
                input.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
                dismissValidationFor('recaptcha');
            }
        }

        function dismissValidationFor(fieldKey) {
            const errorEl = document.querySelector(`.validation-error[data-error-for="${fieldKey}"]`);
            if (errorEl) {
                errorEl.classList.add('hidden');
            }

            const fieldEls = document.querySelectorAll(`[data-validate-field="${fieldKey}"]`);
            fieldEls.forEach((fieldEl) => {
                fieldEl.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-200');
            });
        }

        function bindValidationDismissal() {
            const bookingForm = document.getElementById('bookingForm');
            if (!bookingForm || bookingForm.dataset.validationBound === '1') return;
            bookingForm.dataset.validationBound = '1';

            bookingForm.addEventListener('input', (event) => {
                const target = event.target;
                if (!(target instanceof HTMLElement)) return;

                const fieldKey = target.getAttribute('data-validate-field');
                if (fieldKey) {
                    dismissValidationFor(fieldKey);
                }
            });

            bookingForm.addEventListener('change', (event) => {
                const target = event.target;
                if (!(target instanceof HTMLElement)) return;

                if (target instanceof HTMLInputElement && target.type === 'radio' && target.name ===
                    'selectedSlot') {
                    dismissValidationFor('selectedSlot');
                    return;
                }

                const fieldKey = target.getAttribute('data-validate-field');
                if (fieldKey) {
                    dismissValidationFor(fieldKey);
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            syncCalendarToSelectedDate();
            bindCalendarNavigation();
            renderCalendar();
            ensureRecaptcha();
            bindValidationDismissal();
        });

        document.addEventListener('book-calendar-refresh', (event) => {
            selectedDate = event.detail?.selectedDate || null;
            syncCalendarToSelectedDate();
            bindCalendarNavigation();
            renderCalendar();
            ensureRecaptcha();
            bindValidationDismissal();
        });
    </script>
</div>
