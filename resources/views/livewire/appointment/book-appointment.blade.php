<div>
    @php
        $isPatientUser = auth()->check() && auth()->user()->role === 3;
        $patientName = $isPatientUser ? auth()->user()->username ?? 'Patient' : null;
        $sectionClass = $isPatientUser
            ? 'bg-slate-50 py-8 md:py-10'
            : 'bg-gradient-to-b from-[#F7F3EF] via-white to-[#F1F7F6] py-12 md:py-16';
        $cardClass = $isPatientUser
            ? 'rounded-2xl border border-slate-200 bg-white p-6 md:p-8 shadow-sm'
            : 'bg-white/90 backdrop-blur border border-[#E5E7EB] p-6 md:p-8 rounded-2xl shadow-sm';
        $headingClass = $isPatientUser
            ? 'text-xl md:text-2xl font-semibold mb-6 flex items-center gap-3 text-slate-900'
            : 'text-xl md:text-2xl font-semibold mb-6 flex items-center gap-3 text-[#111827]';
        $badgeClass = $isPatientUser ? 'bg-sky-600' : 'bg-[#0F766E]';
        $inputClass = $isPatientUser
            ? 'w-full border border-slate-200 rounded-lg p-3 text-sm md:text-base focus:ring-2 focus:ring-sky-500 focus:border-sky-500'
            : 'w-full border border-[#D1D5DB] rounded-lg p-3 text-sm md:text-base focus:ring-2 focus:ring-[#0F766E] focus:border-[#0F766E]';
        $selectClass = $isPatientUser
            ? 'w-full border border-slate-200 rounded-lg p-3 mb-6 text-sm md:text-base bg-white focus:ring-2 focus:ring-sky-500 focus:border-sky-500'
            : 'w-full border border-[#D1D5DB] rounded-lg p-3 mb-6 text-sm md:text-base bg-white focus:ring-2 focus:ring-[#0F766E] focus:border-[#0F766E]';
        $mutedLabelClass = $isPatientUser
            ? 'text-xs uppercase tracking-[0.2em] text-slate-500 mb-3'
            : 'text-xs uppercase tracking-[0.2em] text-[#6B7280] mb-3';
        $primaryButtonClass = $isPatientUser
            ? 'w-full mt-8 py-3.5 bg-sky-600 text-white text-sm md:text-base font-semibold rounded-lg hover:bg-sky-700 transition'
            : 'w-full mt-8 py-3.5 bg-[#0F766E] text-white text-sm md:text-base font-semibold rounded-lg hover:bg-[#0B5F59] transition';
        $successRingClass = $isPatientUser ? 'bg-sky-50 text-sky-700' : 'bg-emerald-50 text-emerald-700';
        $successBorderClass = $isPatientUser ? 'border-sky-100' : 'border-emerald-100';
        $successButtonClass = $isPatientUser
            ? 'mt-6 inline-flex items-center justify-center px-4 py-2 rounded-lg bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700 transition'
            : 'mt-6 inline-flex items-center justify-center px-4 py-2 rounded-lg bg-[#0F766E] text-white text-sm font-semibold hover:bg-[#0B5F59] transition';
    @endphp
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <section class="{{ $sectionClass }}">
        <div class="max-w-6xl mx-auto px-4">
            @if ($isPatientUser)
                <div class="mb-8">
                    <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Patient Portal</p>
                    <h2 class="mt-2 text-2xl font-bold text-slate-900 sm:text-3xl">Book an appointment, {{ $patientName }}.</h2>
                    <p class="mt-2 max-w-2xl text-sm text-slate-600">
                        Choose a service and time that works best for you.
                    </p>
                </div>
            @else
                <div class="mb-10">
                    <p class="text-xs uppercase tracking-[0.3em] text-[#6B6B6B]">Patient Appointment</p>
                    <h2 class="text-3xl md:text-4xl font-semibold text-[#1F2937] mt-2">Book Your Visit</h2>
                    <p class="text-sm md:text-base text-[#4B5563] mt-2 max-w-2xl">
                        Provide patient details, then pick a service, date, and time.
                    </p>
                </div>
            @endif
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

            <form wire:submit.prevent="bookAppointment" class="grid grid-cols-1 gap-8" id="bookingForm">
                <div class="{{ $cardClass }}">
                    <h3 class="{{ $headingClass }}">
                        <span
                            class="{{ $badgeClass }} text-white w-8 h-8 rounded-full flex items-center justify-center text-sm">1</span>
                        Patient Details
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <input type="text" wire:model="first_name" placeholder="First Name"
                                class="{{ $inputClass }}">
                            @error('first_name')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <input type="text" wire:model="last_name" placeholder="Last Name"
                                class="{{ $inputClass }}">
                            @error('last_name')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <input type="text" wire:model="age" placeholder="Age" class="{{ $inputClass }}">
                            @error('age')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <input type="text" wire:model="contact_number" placeholder="Contact number"
                                class="{{ $inputClass }}">
                            @error('contact_number')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <input type="email" wire:model="email" placeholder="Email" class="{{ $inputClass }}">
                            @error('email')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
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
                    <select wire:model="service_id" class="{{ $selectClass }}">
                        <option value="">Select Service</option>
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}">{{ $service->service_name }}</option>
                        @endforeach
                    </select>
                    @error('service_id')
                        <div class="text-sm text-red-600 mb-4">{{ $message }}</div>
                    @enderror

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <p class="{{ $mutedLabelClass }}">Pick a Date</p>
                            <div class="border border-[#E5E7EB] p-4 rounded-xl bg-white" wire:ignore>
                            <div class="flex justify-between items-center mb-4">
                                <button type="button" id="prevMonth"
                                    class="w-8 h-8 rounded-full border border-[#E5E7EB] text-[#374151] hover:bg-[#F3F4F6] transition">
                                    &lsaquo;
                                </button>
                                <h4 id="monthYear"
                                    class="text-sm font-semibold uppercase tracking-[0.2em] text-[#111827]"></h4>
                                <button type="button" id="nextMonth"
                                    class="w-8 h-8 rounded-full border border-[#E5E7EB] text-[#374151] hover:bg-[#F3F4F6] transition">
                                    &rsaquo;
                                </button>
                            </div>
                            <div id="calendarDays" class="grid grid-cols-7 gap-1.5"></div>
                            @error('selectedDate')
                                <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
                            @enderror
                            </div>
                        </div>

                        <div>
                            <p class="{{ $mutedLabelClass }}">Select a Time</p>
                            <div class="grid grid-cols-2 gap-2">
                                @forelse ($availableSlots as $slot)
                                    <label class="cursor-pointer">
                                        <input type="radio" wire:model="selectedSlot" value="{{ $slot['value'] }}"
                                            class="peer sr-only">
                                        <div
                                            class="text-center py-2 rounded-lg border border-[#E5E7EB] text-sm font-medium text-[#374151] {{ $isPatientUser ? 'peer-checked:bg-sky-600' : 'peer-checked:bg-[#0F766E]' }} peer-checked:text-white transition-all">
                                            {{ $slot['time'] }}
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
                                            '01:00 PM',
                                            '03:00 PM',
                                            '04:00 PM',
                                            '05:00 PM',
                                            '06:00 PM',
                                            '07:00 PM',
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
                            @error('selectedSlot')
                                <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
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
                            <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
                        @enderror
                    @endguest

                    <button type="submit" onclick="setRecaptchaToken()" class="{{ $primaryButtonClass }}">
                        Confirm Appointment
                    </button>
                </div>
            </form>
        </div>
    </section>

    <script>
        let currentDate = new Date();
        let selectedDate = null;
        const selectedDayClass = @json($isPatientUser ? 'bg-sky-600 text-white' : 'bg-[#0F766E] text-white');
        const activeDayClass = @json($isPatientUser ? 'bg-white text-slate-700 hover:bg-slate-100' : 'bg-white text-[#374151] hover:bg-[#F3F4F6]');
        const disabledDayClass = @json($isPatientUser ? 'bg-slate-50 text-slate-300 cursor-not-allowed' : 'bg-[#F9FAFB] text-[#C7CCD1] cursor-not-allowed');

        function formatDateLocal(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            const today = new Date();
            const todayStr = formatDateLocal(today);
            const viewingIsPastMonth =
                year < today.getFullYear() || (year === today.getFullYear() && month < today.getMonth());
            const viewingIsFutureMonth =
                year > today.getFullYear() || (year === today.getFullYear() && month > today.getMonth());
            document.getElementById('monthYear').textContent = currentDate.toLocaleDateString('en-US', {
                month: 'long',
                year: 'numeric'
            }).toUpperCase();

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const calendarDays = document.getElementById('calendarDays');
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
                    renderCalendar();
                });
                calendarDays.appendChild(dayButton);
            }
        }

        document.getElementById('prevMonth').addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        });
        document.getElementById('nextMonth').addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        });

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
            }
        }

        function selectTodayIfEmpty() {
            if (selectedDate) return;
            selectedDate = formatDateLocal(new Date());
            if (typeof @this !== 'undefined' && @this.set) {
                @this.set('selectedDate', selectedDate);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            selectTodayIfEmpty();
            renderCalendar();
            ensureRecaptcha();
        });

        document.addEventListener('livewire:initialized', () => {
            selectTodayIfEmpty();
            Livewire.hook('message.processed', () => {
                ensureRecaptcha();
            });
        });
    </script>
</div>
