<div class="relative"  @if(!$showAppointmentModal) wire:poll.5s="loadAppointments" @endif">
    <h1 class="text-3xl lg:text-4xl font-bold text-gray-800">Appointment Calendar</h1>
    <div class="w-full max-w-9xl mx-auto px-2 py-10 lg:px-8 overflow-x-auto bg-white mt-6">
        <div class="flex flex-col md:flex-row max-md:gap-3 items-center justify-between mb-5">
            <div class="flex items-center gap-4">
                <input type="date" wire:model.live="selectedDate" wire:change="goToDate" min="{{ now()->subYear()->format('Y-m-d') }}" max="{{ now()->addYears(3)->format('Y-m-d') }}" class="border rounded px-3 py-2 text-sm">

                <button type="button" wire:click="goToToday" class="px-3 py-2 border rounded text-sm hover:bg-gray-100">
                    Today
                </button>

                <div class="flex gap-2">
                    <button wire:click="previousWeek" class="p-2 hover:bg-gray-200 rounded">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button wire:click="nextWeek" class="p-2 hover:bg-gray-200 rounded">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div> 
        </div>

        <div class="relative">
            <div class="grid grid-cols-[120px_repeat(7,1fr)] border-t border-gray-200 sticky top-0 left-0 w-full bg-white z-[1]">
                <div class="p-3.5 flex items-center justify-center text-sm font-medium text-gray-900">
                </div>
                @foreach($weekDates as $date)
                    <div class="p-3.5 flex flex-col items-center justify-center border-r border-b border-gray-200  {{ $date->isToday() ? 'bg-[#0086da] text-white' : ''}}">
                        <span class="text-md font-medium {{ $date->isToday() ? ' text-white' : 'text-gray-500'}} mb-1">{{ $date->format('D') }}</span>
                        <span class="text-lg font-medium ">
                            {{ $date->format('M j') }}
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="hidden sm:grid grid-cols-[120px_repeat(7,1fr)] w-full overflow-x-auto relative pt-4 z-10">           
                @foreach($timeSlots as $time)
                    {{-- This is the fixed time slot label cell --}}
                    <div class="relative h-16 lg:h-16 border-t border-r border-gray-200">
                        <span class="absolute top-0 left-2 -mt-2.5 bg-white px-1 text-sm font-semibold text-gray-500">
                            {{ Carbon\Carbon::parse($time)->format('h:i A') }}
                        </span>
                    </div>
                    @foreach($weekDates as $date)
                        @php
                            // --- NEW CHECK ---
                            $isOccupied = $this->isSlotOccupied($date->toDateString(), $time);
                        @endphp 

                        <div 
                            {{-- If it's NOT occupied, add the click handler --}}
                            @if(!$isOccupied)
                                wire:click="openAppointmentModal('{{ $date->toDateString() }}', '{{ $time }}')"
                            @endif
                            
                            {{-- This class logic is UPDATED --}}
                            class="h-16 lg:h-16 p-0.5 md:p-3.5 border-t border-r border-gray-200 transition-all 
                                @if(!$isOccupied)
                                    hover:bg-stone-100 cursor-default
                                @endif
                                "
                        >
                            {{-- This is the background cell --}}
                        </div>
                    @endforeach
                @endforeach

                <div class="absolute inset-x-0 bottom-0 top-4 grid grid-cols-[120px_repeat(7,1fr)] w-full pointer-events-none">                    
                    <div class="h-full">
                        {{-- SPACER --}}
                    </div>
                    
                    @foreach($weekDates as $date)
                        <div class="relative h-full border-r border-gray-200">
                            @php
                                $dayAppointments = $this->getAppointmentsForDay($date);
                                $dayStartHour = 9;   // Must match generateTimeSlots()
                                $slotHeightRem = 4;  // h-16 = 4rem
                            @endphp

                            @foreach($dayAppointments as $appointment)
                                @php
                                    // Calculate 'top' position
                                    $startCarbon = Carbon\Carbon::parse($appointment->start_time);
                                    $topInMinutes = (($startCarbon->hour - $dayStartHour) * 60) + $startCarbon->minute;
                                    $topPositionRem = ($topInMinutes / 30) * $slotHeightRem;
                                    
                                    // Calculate 'height'
                                    $heightInRem = ($appointment->duration_in_minutes / 30) * $slotHeightRem;
                                @endphp

                                {{-- This is the actual appointment block --}}
                                <div class="absolute w-full px-1 py-0.5 " 
                                     style="top: {{ $topPositionRem }}rem; height: {{ $heightInRem }}rem; z-index: 10;">
                                     
                                     <div wire:click="viewAppointment({{ $appointment->id }})" class="rounded p-1.5 border-l-4 border-t-4 border-blue-600 bg-blue-50 h-full overflow-hidden pointer-events-auto cursor-pointer">
                                         <p class="text-md font-semibold text-gray-900 mb-px">
                                             {{ $appointment->last_name }}, {{ $appointment->first_name }} 
                                         </p>
                                         <p class="text-md font-medium text-gray-700 leading-tight mb-1 truncate">
                                            {{ $appointment->service_name }}
                                        </p>
                                        <p class="text-md font-normal text-blue-600">
                                            {{ $appointment->start_time }} - {{ $appointment->end_time }}
                                        </p>
                                        <p class="text-md font-normal 
                                            @if($appointment->status == 'Ongoing') text-yellow-600
                                            @elseif($appointment->status == 'Scheduled') text-blue-600
                                            @elseif($appointment->status == 'Cancelled') text-red-600
                                            @elseif($appointment->status == 'Completed') text-green-600
                                            @else text-gray-600
                                            @endif">
                                            {{ $appointment->status }}
                                        </p>
                                     </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>

    {{-- --- APPOINTMENT MODAL --- --}}
    @if($showAppointmentModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black opacity-60" wire:click="closeAppointmentModal"></div>
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-4xl mx-4 z-10 overflow-hidden">
                
                <div class="px-6 py-4 flex items-center justify-between bg-white border-b">
                    <h3 class="text-2xl font-semibold text-gray-900 ">Appointment Details</h3>
                    <button class="text-[#0086da] text-4xl flex items-center justify-center px-2 rounded-full hover:bg-[#e6f4ff] transition" wire:click="closeAppointmentModal">×</button>
                </div>

                @if (session()->has('error'))
                    <div class="bg-red-100 text-red-700 px-6 py-3 text-sm font-bold border-b border-red-200">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="p-6 overflow-y-auto max-h-[85vh]">
                    
                    <div class="mb-6 bg-gray-50 rounded-xl p-5 border border-gray-100">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Date</label>
                                <input type="text" value="{{ \Carbon\Carbon::parse($selectedDate)->format('F j, Y') }}" class="w-full border-0 bg-transparent p-0 text-xl font-bold text-gray-800" readonly />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Start Time</label>
                                <input type="text" value="{{ $selectedTime }}" class="w-full border-0 bg-transparent p-0 text-xl font-bold text-gray-800" readonly />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">End Time</label>
                                <input type="text" value="{{ $endTime }}" class="w-full border-0 bg-transparent p-0 text-xl font-bold text-gray-800" readonly />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <input wire:model="firstName" type="text" class="w-full border-gray-300 rounded-lg px-4 py-2.5 bg-gray-50 text-gray-800 font-medium focus:ring-blue-500 focus:border-blue-500" readonly/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                            <input wire:model="middleName" type="text" class="w-full border-gray-300 rounded-lg px-4 py-2.5 bg-gray-50 text-gray-800 font-medium focus:ring-blue-500 focus:border-blue-500" readonly/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <input wire:model="lastName" type="text" class="w-full border-gray-300 rounded-lg px-4 py-2.5 bg-gray-50 text-gray-800 font-medium focus:ring-blue-500 focus:border-blue-500" readonly/>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                            <input wire:model="contactNumber" type="text" class="w-full border-gray-300 rounded-lg px-4 py-2.5 bg-gray-50 text-gray-800 font-medium" readonly/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Service Required</label>
                            <select wire:model="selectedService" class="w-full border-gray-300 rounded-lg px-4 py-2.5 text-gray-800 font-medium focus:ring-blue-500 focus:border-blue-500" {{ ($appointmentStatus != 'Waiting' && $appointmentStatus != 'Arrived') ? 'disabled' : '' }}>
                                @foreach($servicesList as $service)
                                    <option value="{{ $service->id }}">
                                        {{ $service->service_name }} ({{ \Carbon\Carbon::parse($service->duration)->format('H:i') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                        @error('conflict') 
                            <span class="text-red-600 text-sm mr-auto">{{ $message }}</span> 
                        @enderror

                        @if($isViewing)
                            {{-- === VIEWING MODE (Flow Logic) === --}}
                            
                            {{-- Cancel Button (Always visible unless finished) --}}
                            @if(!in_array($appointmentStatus, ['Cancelled', 'Completed']))
                                <button type="button" 
                                    wire:click="updateStatus('Cancelled')"
                                    wire:confirm="Are you sure you want to cancel this appointment?"
                                    class="px-5 py-2.5 rounded-lg text-red-600 font-medium hover:bg-red-50 border border-transparent hover:border-red-100 mr-auto transition"
                                >
                                    Cancel Appointment
                                </button>
                            @endif

                            {{-- STATUS SPECIFIC BUTTONS --}}
                            
                            {{-- 1. SCHEDULED --}}
                            @if($appointmentStatus === 'Scheduled')
                                <button type="button" wire:click="processPatient" class="px-6 py-2.5 rounded-lg bg-white border-2 border-blue-600 text-blue-700 font-bold hover:bg-blue-50 transition">
                                    Update Patient Info
                                </button>
                                <button type="button" wire:click="updateStatus('Arrived')" class="px-6 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-md hover:shadow-lg transition flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    Mark Arrived
                                </button>

                            {{-- 2. WAITING / ARRIVED --}}
                            @elseif($appointmentStatus === 'Waiting' || $appointmentStatus === 'Arrived')
                                <button type="button" wire:click="processPatient" class="px-6 py-2.5 rounded-lg bg-white border-2 border-gray-300 text-gray-600 font-bold hover:bg-gray-50 transition">
                                    View Patient Info
                                </button>
                                
                                @if (auth()->user()->role === 1)
                                    <button type="button" wire:click="admitPatient" class="px-6 py-2.5 rounded-lg bg-red-500 hover:bg-red-600 text-white font-bold shadow-md hover:shadow-lg transition flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        ADMIT PATIENT
                                    </button>
                                @endif

                            {{-- 3. ONGOING / IN CHAIR --}}
                            @elseif($appointmentStatus === 'Ongoing')
                                @if (auth()->user()->role === 1)
                                    <button type="button" wire:click="openPatientChart" class="px-6 py-2.5 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-bold shadow-md hover:shadow-lg transition flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                        View Dental Chart
                                    </button>
                                @endif
                                <button type="button" wire:click="updateStatus('Completed')" class="px-6 py-2.5 rounded-lg bg-green-600 hover:bg-green-700 text-white font-bold shadow-md hover:shadow-lg transition flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Finish & Complete
                                </button>

                            {{-- 4. HISTORY --}}
                            @elseif($appointmentStatus === 'Completed')
                                <span class="px-6 py-2.5 rounded-lg bg-green-100 text-green-800 font-bold border border-green-200">
                                    ✅ Completed
                                </span>
                            @elseif($appointmentStatus === 'Cancelled')
                                <span class="px-6 py-2.5 rounded-lg bg-red-100 text-red-800 font-bold border border-red-200">
                                    ❌ Cancelled
                                </span>
                            @endif

                        @else
                            {{-- === BOOKING MODE (Unchanged) === --}}
                            <button type="button" wire:click="closeAppointmentModal" class="px-5 py-3 rounded bg-gray-200 hover:bg-gray-300 font-medium">Cancel</button>
                            <button type="submit" class="px-6 py-3 rounded bg-[#0086da] text-white text-lg font-bold shadow-md hover:bg-blue-600 transition">
                                Save Appointment
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
    <livewire:patient-form-controller.patient-form-modal />

    @if (session()->has('success') || session()->has('error') || session()->has('info'))
    <div 
        id="calendar-toast"
        class="fixed bottom-5 right-5 z-[70] flex items-center gap-3 px-6 py-4 rounded-lg shadow-xl border transform transition-all duration-300 ease-in-out translate-y-0 opacity-100
        @if(session('success')) bg-green-50 border-green-200 text-green-800 
        @elseif(session('error')) bg-red-50 border-red-200 text-red-800 
        @else bg-blue-50 border-blue-200 text-blue-800 @endif"
    >
        @if(session('success'))
            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        @elseif(session('error'))
            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        @else
            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        @endif

        <div class="font-medium text-sm">
            {{ session('success') ?? session('error') ?? session('info') }}
        </div>

        <button onclick="document.getElementById('calendar-toast').remove()" class="ml-4 text-gray-400 hover:text-gray-600 focus:outline-none">
            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
        </button>

        <script>
            setTimeout(function() {
                var toast = document.getElementById('calendar-toast');p
                if (toast) {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(10px)';
                    setTimeout(function() { toast.remove(); }, 500);
                }
            }, 5000); 
        </script>
    </div>
    @endif
</div>