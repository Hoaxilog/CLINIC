<div class="relative" @if (!$showAppointmentModal) wire:poll.5s="loadAppointments" @endif>
    <h1 class="text-3xl lg:text-4xl font-bold text-gray-800">Appointments</h1>
    <div class="w-full max-w-9xl mx-auto px-2 py-6 lg:px-8 overflow-x-auto bg-white mt-6">
        <div class="flex items-center gap-2 mb-6">
            @if (auth()->user()->role !== 3)
                <button type="button" wire:click="setActiveTab('pending')"
                    class="px-4 py-2 text-sm font-semibold rounded-lg border border-gray-200 transition
                    {{ $activeTab === 'pending' ? 'bg-[#0789da] text-white border-[#0789da] shadow-sm' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                    Appointment Request
                </button>
            @endif
            <button type="button" wire:click="setActiveTab('calendar')"
                class="px-4 py-2 text-sm font-semibold rounded-lg border border-gray-200 transition
                {{ $activeTab === 'calendar' ? 'bg-[#0789da] text-white border-[#0789da] shadow-sm' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                Appointment Calendar
            </button>
        </div>

        @if ($prefillPatientId && $prefillPatientLabel)
            <div class="mb-5 flex flex-wrap items-center gap-3 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                <span class="font-semibold">Adding appointment for:</span>
                <span class="font-medium text-blue-900">{{ $prefillPatientLabel }}</span>
                <button type="button" wire:click="clearPrefill"
                    class="ml-auto rounded-lg border border-blue-200 bg-white px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100">
                    Clear
                </button>
            </div>
        @endif

        @if ($activeTab === 'pending' && auth()->user()->role !== 3)
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-[#f7fbff] to-white">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Pending Approvals</h2>
                            <p class="text-xs text-gray-500">Review and approve appointment requests.</p>
                        </div>
                    </div>
                </div>

                <div
                    class="hidden md:grid grid-cols-5 gap-2 px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 bg-gray-50 border-b border-gray-100">
                    <div>Date & Time</div>
                    <div>Patient</div>
                    <div>Service</div>
                    <div>Contact</div>
                    <div class="text-right">Actions</div>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse($this->getPendingApprovals() as $pending)
                        <div
                            class="grid grid-cols-1 md:grid-cols-5 gap-3 px-5 py-4 text-sm items-center hover:bg-gray-50 transition">
                            <div>
                                <div class="font-semibold text-gray-900">
                                    {{ \Carbon\Carbon::parse($pending->appointment_date)->format('M d, Y') }}</div>
                                <div class="text-gray-500">
                                    {{ \Carbon\Carbon::parse($pending->appointment_date)->format('h:i A') }}</div>
                            </div>
                            <div class="font-medium text-gray-900">{{ $pending->last_name }}, {{ $pending->first_name }}
                            </div>
                            <div class="text-gray-700">{{ $pending->service_name }}</div>
                            <div class="text-gray-600">
                                <div>{{ $pending->mobile_number ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-400">{{ $pending->email_address ?? 'N/A' }}</div>
                            </div>
                            <div class="flex md:justify-end gap-2">
                                <button type="button" wire:click="approveAppointment({{ $pending->id }})"
                                    class="px-3.5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition">
                                    Approve
                                </button>
                                <button type="button" wire:click="rejectAppointment({{ $pending->id }})"
                                    class="px-3.5 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-sm transition">
                                    Reject
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-sm text-gray-500">No pending approvals.</div>
                    @endforelse
                </div>
            </div>
        @endif

        @if ($activeTab === 'calendar')
            <div class="flex flex-col md:flex-row max-md:gap-3 items-center justify-between mb-5">
                <div class="flex items-center gap-4">
                    <input type="date" wire:model.live="selectedDate" wire:change="goToDate"
                        min="{{ now()->subYear()->format('Y-m-d') }}" max="{{ now()->addYears(3)->format('Y-m-d') }}"
                        class="border rounded px-3 py-2 text-sm">

                    <button type="button" wire:click="goToToday"
                        class="px-3 py-2 border rounded text-sm hover:bg-gray-100">
                        Today
                    </button>

                    <div class="flex gap-2">
                        <button wire:click="previousWeek" class="p-2 hover:bg-gray-200 rounded">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button wire:click="nextWeek" class="p-2 hover:bg-gray-200 rounded">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="relative">
                <div
                    class="grid grid-cols-[120px_repeat(7,1fr)] border-t border-gray-200 sticky top-0 left-0 w-full bg-white z-[1]">
                    <div class="p-3.5 flex items-center justify-center text-sm font-medium text-gray-900">
                    </div>
                    @foreach ($weekDates as $date)
                        <div
                            class="p-3.5 flex flex-col items-center justify-center border-r border-b border-gray-200  {{ $date->isToday() ? 'bg-[#0086da] text-white' : '' }}">
                            <span
                                class="text-md font-medium {{ $date->isToday() ? ' text-white' : 'text-gray-500' }} mb-1">{{ $date->format('D') }}</span>
                            <span class="text-lg font-medium ">
                                {{ $date->format('M j') }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="hidden sm:grid grid-cols-[120px_repeat(7,1fr)] w-full overflow-x-auto relative pt-4 z-10">
                    @foreach ($timeSlots as $time)
                        <div class="relative h-16 lg:h-16 border-t border-r border-gray-200">
                            <span
                                class="absolute top-0 left-2 -mt-2.5 bg-white px-1 text-sm font-semibold text-gray-500">
                                {{ Carbon\Carbon::parse($time)->format('h:i A') }}
                            </span>
                        </div>
                        @foreach ($weekDates as $date)
                            @php
                                $isOccupied = $this->isSlotOccupied($date->toDateString(), $time);
                            @endphp

                            <div @if (!$isOccupied) wire:click="openAppointmentModal('{{ $date->toDateString() }}', '{{ $time }}')" @endif
                                class="h-16 lg:h-16 p-0.5 md:p-3.5 border-t border-r border-gray-200 transition-all 
                                @if (!$isOccupied) hover:bg-stone-100 cursor-default @endif
                                ">
                            </div>
                        @endforeach
                    @endforeach

                    <div
                        class="absolute inset-x-0 bottom-0 top-4 grid grid-cols-[120px_repeat(7,1fr)] w-full pointer-events-none">
                        <div class="h-full"></div>

                        @foreach ($weekDates as $date)
                            <div class="relative h-full border-r border-gray-200">
                                @php
                                    $dayAppointments = $this->getAppointmentsForDay($date);
                                    $dayStartHour = 9;
                                    $slotHeightRem = 4;
                                    $groupedByTime = $dayAppointments->groupBy('start_time');
                                @endphp

                                @foreach ($groupedByTime as $timeKey => $appointmentsAtTime)
                                    @php
                                        $firstAppt = $appointmentsAtTime->first();
                                        $startCarbon = Carbon\Carbon::parse($firstAppt->start_time);
                                        $topInMinutes =
                                            ($startCarbon->hour - $dayStartHour) * 60 + $startCarbon->minute;
                                        $topPositionRem = ($topInMinutes / 30) * $slotHeightRem;
                                        $heightInRem = ($firstAppt->duration_in_minutes / 30) * $slotHeightRem;
                                        $countAtTime = $appointmentsAtTime->count();
                                    @endphp

                                    <div class="absolute w-full px-1 py-0.5 "
                                        style="top: {{ $topPositionRem }}rem; height: {{ $heightInRem }}rem; z-index: 10;">

                                        @if ($countAtTime === 1)
                                            <div wire:click="viewAppointment({{ $firstAppt->id }})"
                                                class="rounded p-1.5 border-l-4 border-t-4 border-blue-600 bg-blue-50 h-full overflow-hidden pointer-events-auto cursor-pointer">
                                                <p class="text-md font-semibold text-gray-900 mb-px">
                                                    {{ $firstAppt->last_name }}, {{ $firstAppt->first_name }}
                                                </p>
                                                <p
                                                    class="text-md font-medium text-gray-700 leading-tight mb-1 truncate">
                                                    {{ $firstAppt->service_name }}
                                                </p>
                                                <p class="text-md font-normal text-blue-600">
                                                    {{ $firstAppt->start_time }} - {{ $firstAppt->end_time }}
                                                </p>
                                                <p
                                                    class="text-md font-normal 
                                                @if ($firstAppt->status == 'Ongoing') text-yellow-600
                                                @elseif($firstAppt->status == 'Scheduled') text-blue-600
                                                @elseif($firstAppt->status == 'Cancelled') text-red-600
                                                @elseif($firstAppt->status == 'Waiting') text-orange-600
                                                @elseif($firstAppt->status == 'Completed') text-green-600
                                                @else text-gray-600 @endif">
                                                    {{ $firstAppt->status === 'Waiting' ? 'Ready' : $firstAppt->status }}
                                                </p>
                                            </div>
                                        @else
                                            <div x-data="{ open: false }" class="h-full pointer-events-auto relative">
                                                <button type="button" @click="open = !open"
                                                    class="rounded p-1.5 border-l-4 border-t-4 border-blue-600 bg-blue-50 h-full w-full overflow-hidden pointer-events-auto cursor-pointer text-left">
                                                    <div class="flex items-center justify-between">
                                                        <p class="text-md font-semibold text-gray-900 mb-px">
                                                            {{ $countAtTime }} Appointments
                                                        </p>
                                                        <span
                                                            class="text-xs font-bold bg-black text-white px-2 py-0.5 rounded-full">x{{ $countAtTime }}</span>
                                                    </div>
                                                    <p class="text-md font-normal text-blue-600">
                                                        {{ $firstAppt->start_time }} - {{ $firstAppt->end_time }}
                                                    </p>
                                                </button>
                                                <div x-show="open" @click.away="open = false"
                                                    class="absolute mt-2 w-64 bg-white border border-gray-200 rounded-lg shadow-lg z-30 pointer-events-auto">
                                                    @foreach ($appointmentsAtTime as $apptItem)
                                                        <button type="button"
                                                            wire:click="viewAppointment({{ $apptItem->id }})"
                                                            class="w-full text-left px-4 py-2 hover:bg-gray-50 border-b last:border-b-0">
                                                            <div class="text-sm font-semibold">
                                                                {{ $apptItem->last_name }},
                                                                {{ $apptItem->first_name }}
                                                            </div>
                                                            <div class="text-xs text-gray-600">
                                                                {{ $apptItem->service_name }}
                                                            </div>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- --- APPOINTMENT MODAL --- --}}
    @if ($showAppointmentModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">

            <div class="absolute inset-0 bg-black opacity-60" wire:click="closeAppointmentModal"></div>
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-4xl mx-4 z-10 overflow-hidden">

                <div class="px-6 py-4 flex items-center justify-between bg-white border-b">
                    <h3 class="text-2xl font-semibold text-gray-900 ">Appointment Details</h3>
                    <button
                        class="text-[#0086da] text-4xl flex items-center justify-center px-2 rounded-full hover:bg-[#e6f4ff] transition"
                        wire:click="closeAppointmentModal">×</button>
                </div>

                @if (session()->has('error'))
                    <div class="bg-red-100 text-red-700 px-6 py-3 text-sm font-bold border-b border-red-200">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- [FIX] Changed from DIV to FORM so the submit button works --}}
                <form class="p-6 overflow-y-auto max-h-[85vh]" wire:submit.prevent="saveAppointment">

                    {{-- DATE & TIME HEADER --}}
                    <div class="mb-6 bg-gray-50 rounded-xl p-5 border border-gray-100">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Date</label>
                                <input type="text"
                                    value="{{ \Carbon\Carbon::parse($selectedDate)->format('F j, Y') }}"
                                    class="w-full border-0 bg-transparent p-0 text-xl font-bold text-gray-800"
                                    readonly />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Start Time</label>
                                <input type="text" value="{{ $selectedTime }}"
                                    class="w-full border-0 bg-transparent p-0 text-xl font-bold text-gray-800"
                                    readonly />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">End Time</label>
                                <input type="text" value="{{ $endTime }}"
                                    class="w-full border-0 bg-transparent p-0 text-xl font-bold text-gray-800"
                                    readonly />
                            </div>
                        </div>
                    </div>

                    {{-- SEARCH EXISTING PATIENT --}}
                    @if (!$isViewing)
                        <div class="mb-6 relative">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search Existing Patient</label>
                            <div class="relative">
                                <input type="text" wire:model.live.debounce.300ms="searchQuery"
                                    class="w-full border-black border
                                     rounded-lg px-4 py-2 pl-10 text-base focus:ring-2 focus:ring-[#0086da] focus:border-[#0086da]"
                                    placeholder="Search by name or phone number..." />
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24"
                                        height="24" color="currentColor" fill="none" stroke="currentColor"
                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path
                                            d="M2.5 12C2.5 7.52166 2.5 5.28249 3.89124 3.89124C5.28249 2.5 7.52166 2.5 12 2.5C16.4783 2.5 18.7175 2.5 20.1088 3.89124C21.5 5.28249 21.5 7.52166 21.5 12C21.5 16.4783 21.5 18.7175 20.1088 20.1088C18.7175 21.5 16.4783 21.5 12 21.5C7.52166 21.5 5.28249 21.5 3.89124 20.1088C2.5 18.7175 2.5 16.4783 2.5 12Z" />
                                        <path
                                            d="M14.8284 14.8284L17 17M16 12C16 9.79086 14.2091 8 12 8C9.79086 8 8 9.79086 8 12C8 14.2091 9.79086 16 12 16C14.2091 16 16 14.2091 16 12Z" />
                                    </svg>
                                </div>

                                @if (!empty($searchQuery) && count($patientSearchResults) > 0)
                                    <div
                                        class="absolute z-50 mt-1 w-full bg-white shadow-xl max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                        @foreach ($patientSearchResults as $result)
                                            <button type="button" wire:click="selectPatient({{ $result->id }})"
                                                class="w-full text-left cursor-pointer select-none relative py-3 pl-3 pr-9 hover:bg-blue-50 transition text-gray-900 group border-b border-gray-100 last:border-0">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-semibold block truncate">
                                                        {{ $result->last_name }}, {{ $result->first_name }}
                                                        <span class="font-normal text-gray-500 text-xs ml-1">
                                                            ({{ $result->birth_date ? \Carbon\Carbon::parse($result->birth_date)->format('M d, Y') : 'No Bday' }})
                                                        </span>
                                                    </span>
                                                    <span
                                                        class="text-gray-500 text-xs bg-gray-100 px-2 py-1 rounded-full group-hover:bg-white">
                                                        {{ $result->mobile_number }}
                                                    </span>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                @elseif(!empty($searchQuery) && strlen($searchQuery) >= 2)
                                    <div
                                        class="absolute z-50 mt-1 w-full bg-white shadow-lg rounded-md py-2 px-4 text-sm text-gray-500 border border-gray-200">
                                        No patient found. Please fill in the details below.
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- FORM INPUTS --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-600">*</span></label>
                            <input wire:model="firstName" type="text"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 bg-gray-50 text-gray-800 font-medium focus:ring-blue-500 focus:border-blue-500"
                                @if ($isViewing) readonly class="w-full border rounded px-4 py-3 text-base bg-gray-100 cursor-not-allowed" @endif />
                            @error('firstName')
                                <span class="text-xs text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                            <input wire:model="middleName" type="text"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 bg-gray-50 text-gray-800 font-medium focus:ring-blue-500 focus:border-blue-500"
                                @if ($isViewing) readonly class="w-full border rounded px-4 py-3 text-base bg-gray-100 cursor-not-allowed" @endif />
                            @error('middleName')
                                <span class="text-xs text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-600">*</span></label>
                            <input wire:model="lastName" type="text"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 bg-gray-50 text-gray-800 font-medium focus:ring-blue-500 focus:border-blue-500"
                                @if ($isViewing) readonly class="w-full border rounded px-4 py-3 text-base bg-gray-100 cursor-not-allowed" @endif />
                            @error('lastName')
                                <span class="text-xs text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number <span class="text-red-600">*</span></label>
                            <input wire:model="contactNumber" type="number"
                                class="border w-full border-gray-300 rounded-lg px-4 py-2.5 bg-gray-50 text-gray-800 font-medium"
                                @if ($isViewing) readonly class="w-full border rounded px-4 py-3 text-base bg-gray-100 cursor-not-allowed" @endif />
                            @error('contactNumber')
                                <span class="text-xs text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- BIRTH DATE (Required for Saving) --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Birth Date <span class="text-red-600">*</span></label>
                            <input wire:model="birthDate" type="date"
                                class="border w-full border-gray-300 rounded-lg px-4 py-2.5 bg-gray-50 text-gray-800 font-medium"
                                @if ($isViewing) readonly class="w-full border rounded px-4 py-3 text-base bg-gray-100 cursor-not-allowed" @endif />
                            @error('birthDate')
                                <span class="text-xs text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Service Required <span class="text-red-600">*</span></label>
                            <select wire:model.live="selectedService"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-gray-800 font-medium focus:ring-blue-500 focus:border-blue-500"
                                {{ $isViewing && $appointmentStatus != 'Waiting' ? 'disabled' : '' }}>
                                <option value="" disabled>Select service</option>
                                @foreach ($servicesList as $service)
                                    <option value="{{ $service->id }}">
                                        {{ $service->service_name }}
                                        ({{ \Carbon\Carbon::parse($service->duration)->format('H:i') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('selectedService')
                                <span class="text-xs text-red-600">{{ $message }}</span>
                            @enderror

                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                        @error('conflict')
                            <span class="text-red-600 text-sm mr-auto">{{ $message }}</span>
                        @enderror

                        @if ($isViewing)
                            {{-- === VIEWING MODE (Flow Logic) === --}}
                            @if (!in_array($appointmentStatus, ['Cancelled', 'Completed']))
                                <button type="button" wire:click="updateStatus('Cancelled')"
                                    wire:confirm="Are you sure you want to cancel this appointment?"
                                    class="px-5 py-2.5 rounded-lg text-red-600 font-medium hover:bg-red-50 border border-transparent hover:border-red-100 mr-auto transition">
                                    Cancel Appointment
                                </button>
                            @endif

                            @if ($appointmentStatus === 'Pending')
                                @if (auth()->user()->role !== 3)
                                    <button type="button" wire:click="updateStatus('Scheduled')"
                                        wire:confirm="Approve this appointment request?"
                                        class="px-6 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-md hover:shadow-lg transition">
                                        Approve
                                    </button>
                                    <button type="button" wire:click="updateStatus('Cancelled')"
                                        wire:confirm="Reject this appointment request?"
                                        class="px-6 py-2.5 rounded-lg bg-red-600 hover:bg-red-700 text-white font-bold shadow-md hover:shadow-lg transition">
                                        Reject
                                    </button>
                                @endif
                            @elseif($appointmentStatus === 'Scheduled')
                                <button type="button" wire:click="processPatient"
                                    class="px-6 py-2.5 rounded-lg bg-white border-2 border-blue-600 text-blue-700 font-bold hover:bg-blue-50 transition">
                                    Update Patient Info
                                </button>
                                <button type="button" wire:click="updateStatus('Waiting')"
                                    wire:confirm="Confirm patient is ready?"
                                    class="px-6 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-md hover:shadow-lg transition flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Mark Ready
                                </button>
                            @elseif($appointmentStatus === 'Waiting')
                                <button type="button" wire:click="processPatient"
                                    class="px-6 py-2.5 rounded-lg bg-white border-2 border-gray-300 text-gray-600 font-bold hover:bg-gray-50 transition">
                                    View Patient Info
                                </button>

                                @if (auth()->user()->role === 1)
                                    <button type="button" wire:click="admitPatient"
                                        wire:confirm="Admit this patient to the chair now?"
                                        class="px-6 py-2.5 rounded-lg bg-red-500 hover:bg-red-600 text-white font-bold shadow-md hover:shadow-lg transition flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                        ADMIT PATIENT
                                    </button>
                                @endif
                            @elseif($appointmentStatus === 'Ongoing')
                                @if (auth()->user()->role === 1)
                                    <button type="button" wire:click="openPatientChart"
                                        class="px-6 py-2.5 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-bold shadow-md hover:shadow-lg transition flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                            </path>
                                        </svg>
                                        View Dental Chart
                                    </button>
                                @endif
                                <button type="button" wire:click="updateStatus('Completed')"
                                    wire:confirm="Mark this appointment as completed?"
                                    class="px-6 py-2.5 rounded-lg bg-green-600 hover:bg-green-700 text-white font-bold shadow-md hover:shadow-lg transition flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Finish & Complete
                                </button>
                            @elseif($appointmentStatus === 'Completed')
                                <span
                                    class="px-6 py-2.5 rounded-lg bg-green-100 text-green-800 font-bold border border-green-200">
                                    ✅ Completed
                                </span>
                            @elseif($appointmentStatus === 'Cancelled')
                                <span
                                    class="px-6 py-2.5 rounded-lg bg-red-100 text-red-800 font-bold border border-red-200">
                                    ❌ Cancelled
                                </span>
                            @endif
                        @else
                            {{-- === BOOKING MODE === --}}
                            <button type="button" wire:click="closeAppointmentModal"
                                class="px-5 py-3 rounded bg-gray-200 hover:bg-gray-300 font-medium">Cancel</button>
                            <button type="submit"
                                onclick="return confirm('Save this appointment and patient details?')"
                                class="px-6 py-3 rounded bg-[#0086da] text-white text-lg font-bold shadow-md hover:bg-blue-600 transition">
                                Save Appointment
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    @endif
    <livewire:patient-form-controller.patient-form-modal />

    @include('components.flash-toast')
</div>


