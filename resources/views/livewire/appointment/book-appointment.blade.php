<div>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <section class="bg-[#FCFCFC] py-10 my-25">
        <div class="max-w-5xl mx-auto px-4">
            @if (session()->has('success'))
                <div
                    class="bg-green-500 text-white p-4 mb-4 border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                    {{ session('success') }}
                </div>
            @endif

            <form wire:submit.prevent="bookAppointment" class="grid grid-cols-1 gap-8" id="bookingForm">
                <div class="bg-white border-2 border-black p-8">
                    <h3 class="text-2xl font-black mb-6 flex items-center gap-3">
                        <span
                            class="bg-[#0789da] text-white w-8 h-8 flex items-center justify-center text-sm border-2 border-black">1</span>
                        Patient Details
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <input type="text" wire:model="first_name" placeholder="First Name"
                                class="w-full border-2 border-black p-3 font-bold">
                            @error('first_name')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <input type="text" wire:model="last_name" placeholder="Last Name"
                                class="w-full border-2 border-black p-3 font-bold">
                            @error('last_name')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <input type="text" wire:model="age" placeholder="Age"
                                class="w-full border-2 border-black p-3 font-bold">
                            @error('age')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <input type="text" wire:model="contact_number" placeholder="Contact number"
                                class="w-full border-2 border-black p-3 font-bold">
                            @error('contact_number')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <input type="email" wire:model="email" placeholder="Email"
                                class="w-full border-2 border-black p-3 font-bold">
                            @error('email')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-white border-2 border-black p-8">
                    <h3 class="text-2xl font-black mb-6 flex items-center gap-3">
                        <span
                            class="bg-[#0789da] text-white w-8 h-8 flex items-center justify-center text-sm border-2 border-black">2</span>
                        Select Appointment
                    </h3>
                    <select wire:model="service_id" class="w-full border-2 border-black p-3 mb-6 font-bold bg-white">
                        <option value="">Select Service</option>
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}">{{ $service->service_name }}</option>
                        @endforeach
                    </select>
                    @error('service_id')
                        <div class="text-sm text-red-600 mb-4">{{ $message }}</div>
                    @enderror

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="border-2 border-black p-4 bg-white" wire:ignore>
                            <p class="text-xs font-bold uppercase text-gray-500 mb-3">Pick a Date</p>
                            <div class="flex justify-between items-center mb-4">
                                <button type="button" id="prevMonth" class="font-bold">‹</button>
                                <h4 id="monthYear" class="font-black uppercase"></h4>
                                <button type="button" id="nextMonth" class="font-bold">›</button>
                            </div>
                            <div id="calendarDays" class="grid grid-cols-7 gap-1"></div>
                            @error('selectedDate')
                                <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <p class="text-xs font-bold uppercase text-gray-500 mb-3">Select a Time</p>
                            <div class="grid grid-cols-2 gap-2">
                                @forelse ($availableSlots as $slot)
                                    <label class="cursor-pointer">
                                        <input type="radio" wire:model="selectedSlot" value="{{ $slot['value'] }}"
                                            class="peer sr-only">
                                        <div
                                            class="text-center py-2 border-2 border-black font-bold peer-checked:bg-[#0789da] peer-checked:text-white transition-all">
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
                                            class="text-center py-2 border-2 border-black font-bold text-gray-300 bg-gray-50 cursor-not-allowed">
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

                    <button type="submit" onclick="setRecaptchaToken()"
                        class="w-full mt-8 py-4 bg-[#0789da] text-white font-black border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] uppercase">
                        Confirm Appointment
                    </button>
                </div>
            </form>
        </div>
    </section>

    <script>
        let currentDate = new Date();
        let selectedDate = null;

        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
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
                const dateStr = date.toISOString().split('T')[0];
                const dayButton = document.createElement('button');
                dayButton.type = 'button';
                dayButton.textContent = day;
                dayButton.className =
                    `p-2 text-xs font-bold border-2 border-black ${selectedDate === dateStr ? 'bg-[#0789da] text-white' : 'bg-white'}`;

                dayButton.addEventListener('click', () => {
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

        document.addEventListener('DOMContentLoaded', () => {
            renderCalendar();
            ensureRecaptcha();
        });

        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('message.processed', () => {
                ensureRecaptcha();
            });
        });
    </script>
</div>
