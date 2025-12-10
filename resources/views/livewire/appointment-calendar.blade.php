<div class="relative ">
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
            <div class="grid grid-cols-[120px_repeat(7,1fr)] border-t border-gray-200 sticky top-0 left-0 w-full bg-white z-10">
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

            {{-- This grid contains both the background cells AND the absolute-positioned appointments --}}
            <div class="hidden sm:grid grid-cols-[120px_repeat(7,1fr)] w-full overflow-x-auto relative pt-4">                
                {{-- Loop 1: Render the background grid cells (with click handlers) --}}
                {{-- Loop 1: Render the background grid cells (with click handlers) --}}
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

                {{-- Loop 2: Render the appointments OVER the grid --}}
                {{-- UPDATED: Added pointer-events-none to this overlay --}}
                <div class="absolute inset-x-0 bottom-0 top-4 grid grid-cols-[120px_repeat(7,1fr)] w-full pointer-events-none">                    
                    <div class="h-full">
                        {{-- Empty spacer for the 120px time column --}}
                    </div>
                    
                    @foreach($weekDates as $date)
                        {{-- This is a 'day' column, it must be relative --}}
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
                                <div class="absolute w-full px-1 py-0.5" 
                                     style="top: {{ $topPositionRem }}rem; height: {{ $heightInRem }}rem; z-index: 10;">
                                     
                                     {{-- UPDATED: Added pointer-events-auto so this block is "solid" --}}
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
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black opacity-60"></div>
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-4xl mx-4 z-10 overflow-hidden">
                <div class="px-6 py-4 flex items-center justify-between bg-white border-b">
                    <h3 class="text-2xl font-semibold text-gray-900 ">{{ $isViewing ? 'Appointment Details' : 'Book Appointment' }}</h3>
                    <button class="active:outline-2 active:outline-offset-3 active:outline-dashed active:outline-black text-[#0086da] text-5xl flex items-center justify-center px-3 py-1 rounded-full hover:bg-[#e6f4ff] transition" wire:click="closeAppointmentModal" aria-label="Close">×</button>
                </div>

                <form class="p-6 overflow-y-auto" style="max-height: 80vh;" wire:submit.prevent="saveAppointment">
                    
                    {{-- DATE & TIME (Read-only) --}}
                    <div class="mb-4 bg-gray-100 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Date</label>
                                <input type="text" value="{{ $appointmentDate ? Carbon\Carbon::parse($appointmentDate)->format('F j, Y') : '' }}" class="w-full border-0 bg-transparent p-0 text-lg font-bold text-gray-900" readonly />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Start Time</label>
                                <input type="text" value="{{ $selectedTime ? Carbon\Carbon::parse($selectedTime)->format('h:i A') : '' }}" class="w-full border-0 bg-transparent p-0 text-lg font-bold text-gray-900" readonly />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">End Time</label>
                                <input wire:model="endTime" type="text" class="w-full border-0 bg-transparent p-0 text-lg font-bold text-gray-900" readonly placeholder="--:-- --" />
                                @error('endTime') <span class="text-red-600 text-sm">Please select a service.</span> @enderror
                            </div>
                        </div>
                    </div>

                    @if(!$isViewing)
                        <div class="mb-6 relative">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search Existing Patient</label>
                            <div class="relative">
                                <input 
                                    type="text" 
                                    wire:model.live.debounce.300ms="searchQuery"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 pl-10 text-base focus:ring-2 focus:ring-[#0086da] focus:border-[#0086da]" 
                                    placeholder="Search by name or phone number..." 
                                />
                                {{-- Search Icon --}}
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>

                                {{-- Search Results Dropdown --}}
                                @if(!empty($searchQuery) && count($patientSearchResults) > 0)
                                    <div class="absolute z-50 mt-1 w-full bg-white shadow-xl max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                        @foreach($patientSearchResults as $result)
                                            <button 
                                                type="button"
                                                wire:click="selectPatient({{ $result->id }})"
                                                class="w-full text-left cursor-pointer select-none relative py-3 pl-3 pr-9 hover:bg-blue-50 transition text-gray-900 group border-b border-gray-100 last:border-0"
                                            >
                                                <div class="flex justify-between items-center">
                                                    <span class="font-semibold block truncate">
                                                        {{ $result->last_name }}, {{ $result->first_name }} 
                                                        {{-- Add Birthdate here for instant verification --}}
                                                        <span class="font-normal text-gray-500 text-xs ml-1">
                                                            ({{ $result->birth_date ? \Carbon\Carbon::parse($result->birth_date)->format('M d, Y') : 'No Bday' }})
                                                        </span>
                                                    </span>
                                                    <span class="text-gray-500 text-xs bg-gray-100 px-2 py-1 rounded-full group-hover:bg-white">
                                                        {{ $result->mobile_number }}
                                                    </span>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                @elseif(!empty($searchQuery) && strlen($searchQuery) >= 2)
                                    <div class="absolute z-50 mt-1 w-full bg-white shadow-lg rounded-md py-2 px-4 text-sm text-gray-500 border border-gray-200">
                                        No patient found. Please fill in the details below.
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                    {{-- NAME FIELDS --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-2">First Name</label>
                            <input wire:model.defer="firstName" type="text" class="w-full border rounded px-4 py-3 text-base" placeholder="Renz" />
                            @error('firstName') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-2">Middle Name</label>
                            <input wire:model.defer="middleName" type="text" class="w-full border rounded px-4 py-3 text-base" placeholder="S" />
                            @error('middleName') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-2">Last Name</label>
                            <input wire:model.defer="lastName" type="text" class="w-full border rounded px-4 py-3 text-base" placeholder="Rosales" />
                            @error('lastName') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- CONTACT & RECORD NUMBER --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-2">Contact Number</label>
                            <input wire:model.defer="contactNumber" type="text" class="w-full border rounded px-4 py-3 text-base" placeholder="09..." />
                            @error('contactNumber') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-lg font-medium text-gray-700 mb-2">Birth Date</label>
                            <input 
                                wire:model.defer="birthDate" 
                                type="date" 
                                class="w-full border rounded px-4 py-3 text-base" 
                                @if($isViewing) readonly @endif 
                            />
                            {{-- FIXED: Changed error key from 'contactNumber' to 'birthDate' --}}
                            @error('birthDate') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-lg font-medium text-gray-700 mb-2">Patient Number (Optional)</label>
                        <input wire:model.defer="recordNumber" type="text" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., P-00123" />
                        @error('recordNumber') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    {{-- SERVICES --}}
                    <div class="mb-4">
                        <label class="block text-lg font-medium text-gray-700 mb-2">Service</label>
                        <select wire:model.live="selectedService" class="w-full border rounded px-4 py-3 text-base bg-white">
                            <option value="">-- Select a service --</option>
                            @foreach($servicesList as $service)
                                <option value="{{ $service->id }}">
                                    {{ $service->service_name }} ({{ Carbon\Carbon::parse($service->duration)->format('H:i') }} duration)
                                </option>
                            @endforeach
                        </select>
                        @error('selectedService') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- FORM BUTTONS --}}
                    <div class="flex justify-end gap-3 mt-6">
                        @error('conflict') 
                            <span class="text-red-600 text-sm mr-auto">{{ $message }}</span> 
                        @enderror
                        @if($isViewing)
                            {{-- --- VIEW MODE BUTTONS --- --}}
                            
                            {{-- 1. Cancel Button (Visible if not already cancelled or completed) --}}
                            @if(!in_array($appointmentStatus, ['Cancelled', 'Completed']))
                                <button type="button" 
                                    wire:click="updateStatus('Cancelled')"
                                    wire:confirm="Are you sure you want to cancel this appointment?"
                                    class="px-5 py-3 rounded bg-red-100 hover:bg-red-200 text-red-700 font-semibold mr-auto"
                                >
                                    Cancel Appointment
                                </button>
                            @endif

                            {{-- 2. Status Progression Buttons --}}
                            <div class="flex gap-2">
                                <button type="button" wire:click="closeAppointmentModal" class="px-5 py-3 rounded bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold">
                                    Close
                                </button>

                                @if($appointmentStatus === 'Scheduled')
                                    <button type="button" wire:click="updateStatus('Ongoing')" class="px-5 py-3 rounded bg-blue-600 hover:bg-blue-700 text-white font-semibold">
                                        Mark as Ongoing
                                    </button>
                                @elseif($appointmentStatus === 'Ongoing')
                                    <button type="button" wire:click="updateStatus('Completed')" class="px-5 py-3 rounded bg-green-600 hover:bg-green-700 text-white font-semibold">
                                        Mark as Completed
                                    </button>
                                @elseif($appointmentStatus === 'Completed')
                                    <span class="px-5 py-3 rounded bg-green-100 text-green-800 font-bold border border-green-200">
                                        Completed
                                    </span>
                                @elseif($appointmentStatus === 'Cancelled')
                                    <span class="px-5 py-3 rounded bg-red-100 text-red-800 font-bold border border-red-200">
                                        Cancelled
                                    </span>
                                @endif
                            </div>

                        @else
                            {{-- --- BOOKING MODE BUTTONS (Unchanged) --- --}}
                            <button type="button" wire:click="closeAppointmentModal" class="px-5 py-3 rounded bg-gray-200">Cancel</button>
                            <button type="submit" class="px-5 py-3 rounded bg-[#0086da] text-white text-lg">
                                Save Appointment
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>