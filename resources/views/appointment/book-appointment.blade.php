<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Document</title>

    @vite('resources/css/app.css')
</head>

<body>
    @include('components.homepage.header-section')
    <section class="bg-[#FCFCFC] py-16 my-25">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-12">
                <div
                    class="inline-block border-2 border-black px-3 py-1 mb-4 bg-white shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                    <span class="text-xs font-bold uppercase tracking-widest text-black">Online Booking</span>
                </div>
                <h1 class="text-4xl md:text-5xl font-black text-black uppercase tracking-tighter">
                    Schedule Your <span class="text-[#0789da]">Visit</span>
                </h1>
            </div>

            <form action="" method="POST" class="space-y-8 grid grid-cols-2" id="bookingForm">
                @csrf

                <div class="bg-white border-2 border-black p-8 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] relative z-20">
                    <h3 class="text-2xl font-black uppercase mb-6 flex items-center gap-3">
                        <span
                            class="bg-[#0789da] text-white w-8 h-8 flex items-center justify-center text-sm border-2 border-black">1</span>
                        Patient Details
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest mb-2">First Name</label>
                            <input type="text" name="first_name" id="firstName"
                                class="required-field w-full border-2 border-black p-3 font-bold focus:outline-none focus:shadow-[4px_4px_0px_0px_rgba(7,137,218,1)] transition-all"
                                placeholder="JUAN" value="{{ Auth::user()->first_name ?? '' }}">
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest mb-2">Middle Name
                                (Optional)</label>
                            <input type="text" name="middle_name"
                                class="w-full border-2 border-black p-3 font-bold focus:outline-none focus:shadow-[4px_4px_0px_0px_rgba(7,137,218,1)] transition-all"
                                placeholder="D.">
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest mb-2">Last Name</label>
                            <input type="text" name="last_name" id="lastName"
                                class="required-field w-full border-2 border-black p-3 font-bold focus:outline-none focus:shadow-[4px_4px_0px_0px_rgba(7,137,218,1)] transition-all"
                                placeholder="DELA CRUZ" value="{{ Auth::user()->last_name ?? '' }}">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest mb-2">Age</label>
                                <input type="number" name="age" id="age"
                                    class="required-field w-full border-2 border-black p-3 font-bold focus:outline-none focus:shadow-[4px_4px_0px_0px_rgba(7,137,218,1)] transition-all"
                                    placeholder="24">
                            </div>
                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest mb-2">Contact
                                    No.</label>
                                <input type="text" name="contact_number" id="contact"
                                    class="required-field w-full border-2 border-black p-3 font-bold focus:outline-none focus:shadow-[4px_4px_0px_0px_rgba(7,137,218,1)] transition-all"
                                    placeholder="0912...">
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-black uppercase tracking-widest mb-2">Email Address</label>
                            <input type="email" name="email" id="email"
                                class="required-field w-full border-2 border-black p-3 font-bold focus:outline-none focus:shadow-[4px_4px_0px_0px_rgba(7,137,218,1)] transition-all"
                                placeholder="email@example.com" value="{{ Auth::user()->email ?? '' }}"
                                {{ Auth::check() ? 'readonly' : '' }}>
                        </div>
                    </div>
                </div>

                <div id="sectionDate"
                    class="bg-white border-2 border-black p-8 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] transition-all duration-500 ease-in-out">

                    <h3 class="text-2xl font-black uppercase mb-6 flex items-center gap-3">
                        <span
                            class="bg-[#0789da] text-white w-8 h-8 flex items-center justify-center text-sm border-2 border-black">2</span>
                        Select Appointment
                    </h3>

                    <div class="mb-6">
                        <label class="block text-xs font-black uppercase tracking-widest mb-2">Service Required</label>
                        <select name="service_id"
                            class="w-full border-2 border-black p-3 font-bold focus:outline-none focus:shadow-[4px_4px_0px_0px_rgba(7,137,218,1)] transition-all bg-white">
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}">{{ $service->service_name }}
                                    ({{ $service->duration }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest mb-2">Pick a Date</label>
                            <div class="border-2 border-black p-4 bg-white">
                                <div class="flex justify-between items-center mb-4">
                                    <button type="button" id="prevMonth" class="font-bold text-lg">‹</button>
                                    <h4 id="monthYear" class="font-black text-lg uppercase"></h4>
                                    <button type="button" id="nextMonth" class="font-bold text-lg">›</button>
                                </div>
                                <div class="grid grid-cols-7 gap-1 mb-4">
                                    <div class="text-center text-xs font-black">Sun</div>
                                    <div class="text-center text-xs font-black">Mon</div>
                                    <div class="text-center text-xs font-black">Tue</div>
                                    <div class="text-center text-xs font-black">Wed</div>
                                    <div class="text-center text-xs font-black">Thu</div>
                                    <div class="text-center text-xs font-black">Fri</div>
                                    <div class="text-center text-xs font-black">Sat</div>
                                </div>
                                <div id="calendarDays" class="grid grid-cols-7 gap-1"></div>
                            </div>
                            <input type="hidden" name="appointment_date" id="appointmentDate">
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest mb-2">Pick a Time</label>
                            <div class="grid grid-cols-3 gap-2">
                                @for ($i = 8; $i <= 19; $i++)
                                    @php
                                        $timeVal = sprintf('%02d:00', $i);
                                        $display = date('h:00 A', strtotime("$i:00"));
                                    @endphp
                                    <label class="cursor-pointer relative">
                                        <input type="radio" name="appointment_time" value="{{ $timeVal }}"
                                            class="peer sr-only" onchange="enableSubmit()">
                                        <div
                                            class="text-center py-2 border-2 border-black text-xs font-bold hover:bg-gray-100 peer-checked:bg-[#0789da] peer-checked:text-white transition-all">
                                            {{ $display }}
                                        </div>
                                    </label>
                                @endfor
                            </div>
                        </div>
                    </div>

                    @guest
                        <div class="mt-8 flex justify-center">
                            <div class="border-2 border-black p-1 bg-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                                <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                            </div>
                        </div>
                    @endguest

                    <div class="mt-8">
                        <button type="submit" id="submitBtn" disabled
                            class="w-full py-4 bg-[#0789da] text-white font-black text-lg border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[3px] hover:translate-y-[3px] transition-all uppercase tracking-widest disabled:opacity-50 disabled:cursor-not-allowed">
                            Confirm Appointment
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </section>

    @include('components.homepage.footer-section')

    <script>
        let currentDate = new Date();
        let selectedDate = null;

        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();

            // Update header
            document.getElementById('monthYear').textContent = currentDate.toLocaleDateString('en-US', {
                month: 'long',
                year: 'numeric'
            }).toUpperCase();

            // Get first day of month and number of days
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const today = new Date();

            // Clear calendar
            const calendarDays = document.getElementById('calendarDays');
            calendarDays.innerHTML = '';

            // Add empty cells for days before month starts
            for (let i = 0; i < firstDay; i++) {
                calendarDays.innerHTML += '<div></div>';
            }

            // Add day buttons
            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month, day);
                const dateStr = date.toISOString().split('T')[0];
                const isToday = date.toDateString() === today.toDateString();
                const isPast = date < today && !isToday;
                const isSelected = selectedDate === dateStr;

                const dayButton = document.createElement('button');
                dayButton.type = 'button';
                dayButton.textContent = day;
                dayButton.className =
                    `p-2 text-xs font-bold border-2 border-black ${isPast ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer hover:bg-gray-100'} ${isSelected ? 'bg-[#0789da] text-white' : 'bg-white'} transition-all`;
                dayButton.disabled = isPast;

                dayButton.addEventListener('click', function() {
                    if (!isPast) {
                        selectedDate = dateStr;
                        document.getElementById('appointmentDate').value = dateStr;
                        renderCalendar(); // Re-render to show selection
                        enableSubmit();
                    }
                });

                calendarDays.appendChild(dayButton);
            }
        }

        document.getElementById('prevMonth').addEventListener('click', function(e) {
            e.preventDefault();
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        });

        document.getElementById('nextMonth').addEventListener('click', function(e) {
            e.preventDefault();
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        });

        function enableSubmit() {
            const dateSelected = document.getElementById('appointmentDate').value;
            const timeSelected = document.querySelector('input[name="appointment_time"]:checked');

            if (dateSelected && timeSelected) {
                document.getElementById('submitBtn').disabled = false;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const menuBtn = document.getElementById('menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');
            const menuIcon = document.getElementById('menu-icon');
            const menuPanel = document.getElementById('mobile-menu-panel');
            const menuBackdrop = document.getElementById('mobile-menu-backdrop');
            const menuClose = document.getElementById('menu-close');

            function openMenu() {
                mobileMenu.classList.remove('hidden');
                mobileMenu.setAttribute('aria-hidden', 'false');
                menuBtn.setAttribute('aria-expanded', 'true');
                menuIcon.innerHTML =
                    `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />`;
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        menuBackdrop.classList.remove('opacity-0');
                        menuBackdrop.classList.add('opacity-100');
                        menuPanel.classList.remove('translate-x-full');
                    });
                });
            }

            function closeMenu() {
                menuPanel.classList.add('translate-x-full');
                menuBackdrop.classList.remove('opacity-100');
                menuBackdrop.classList.add('opacity-0');
                menuBtn.setAttribute('aria-expanded', 'false');
                menuIcon.innerHTML =
                    `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />`;
                setTimeout(() => {
                    mobileMenu.classList.add('hidden');
                    mobileMenu.setAttribute('aria-hidden', 'true');
                }, 300);
            }
            menuBtn.addEventListener('click', function() {
                if (mobileMenu.classList.contains('hidden')) {
                    openMenu();
                } else {
                    closeMenu();
                }
            });
            menuBackdrop.addEventListener('click', closeMenu);
            menuClose.addEventListener('click', closeMenu);
        });
    </script>


</body>

</html>
