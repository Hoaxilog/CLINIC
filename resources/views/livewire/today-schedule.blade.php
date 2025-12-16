<div>
    <div class="flex items-center justify-between p-4">
        <h1 class="text-2xl font-semibold text-gray-800"> Today's Schedule</h1>
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0086da" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock"><path d="M12 6v6l4 2"/><circle cx="12" cy="12" r="10"/></svg>
    </div>
                    
    <div class="space-y-3 p-4 h-96 overflow-y-auto scrollbar-thin scrollbar-thumb-rounded-full scrollbar-track-[#ccebff] scrollbar-thumb-[#0086da] scrollbar-color-[#0086da]">
        @if(count($todayAppointments) > 0)
            @foreach($todayAppointments as $app)
                <div wire:click="viewAppointment({{ $app->id }})" class="bg-[#ccebff] p-3 rounded-lg cursor-pointer hover:bg-blue-200 transition duration-150">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 mr-4">
                            <h2 class="text-lg font-semibold text-gray-900 uppercase">{{ $app->first_name }} {{ $app->last_name }}</h2>
                            <p class="text-sm font-medium text-gray-700">
                                {{ $app->service_name }}
                            </p>
                        </div>
                        <div class="text-right min-w-[140px]">
                            
                            <div class="text-sm font-bold text-[#0086da] bg-white border border-[#0086da] px-3 py-1 rounded-lg whitespace-nowrap w-fit ml-auto">
                                {{ \Carbon\Carbon::parse($app->appointment_date)->format('M d, h:i A') }}
                            </div>

                            <div class="mt-1">
                                <span class="text-xs font-bold 
                                    @if($app->status == 'Completed') text-green-600 
                                    @elseif($app->status == 'Cancelled') text-red-600 
                                    @elseif($app->status == 'Ongoing') text-yellow-600 
                                    @else text-gray-500 @endif">
                                    {{ $app->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="flex flex-col items-center justify-center h-full text-gray-500">
                <p>No appointments found.</p>
            </div>
        @endif

    </div>
    @if($showAppointmentModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="absolute inset-0 bg-black opacity-60"></div>
                <div class="relative bg-white rounded-lg shadow-xl w-full max-w-4xl mx-4 z-10 overflow-hidden">
                    <div class="px-6 py-4 flex items-center justify-between bg-white border-b">
                        <h3 class="text-2xl font-semibold text-gray-900 ">Appointment Details</h3>
                        <button class="active:outline-2 active:outline-offset-3 active:outline-dashed active:outline-black text-[#0086da] text-5xl flex items-center justify-center px-3 py-1 rounded-full hover:bg-[#e6f4ff] transition" wire:click="closeAppointmentModal" aria-label="Close">×</button>
                    </div>

                    <div class="p-6 overflow-y-auto" style="max-height: 80vh;">
                        
                        <div class="mb-4 bg-gray-100 rounded-lg p-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Date</label>
                                    <input type="text" value="{{ \Carbon\Carbon::parse($selectedDate)->format('F j, Y') }}" class="w-full border-0 bg-transparent p-0 text-lg font-bold text-gray-900" readonly />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Start Time</label>
                                    <input type="text" value="{{ $selectedTime }}" class="w-full border-0 bg-transparent p-0 text-lg font-bold text-gray-900" readonly />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">End Time</label>
                                    <input type="text" value="{{ $endTime }}" class="w-full border-0 bg-transparent p-0 text-lg font-bold text-gray-900" readonly />
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-2">First Name</label>
                                <input wire:model="firstName" type="text" class="w-full border rounded px-4 py-3 text-base" readonly/>
                            </div>
                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-2">Middle Name</label>
                                <input wire:model="middleName" type="text" class="w-full border rounded px-4 py-3 text-base" readonly/>
                            </div>
                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-2">Last Name</label>
                                <input wire:model="lastName" type="text" class="w-full border rounded px-4 py-3 text-base" readonly/>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-lg font-medium text-gray-700 mb-2">Contact Number</label>
                            <input wire:model="contactNumber" type="text" class="w-full border rounded px-4 py-3 text-base" readonly/>
                        </div>

                        <div class="mb-4">
                            <label class="block text-lg font-medium text-gray-700 mb-2">Service</label>
                            <select wire:model="selectedService" class="w-full border rounded px-4 py-3 text-base bg-white" disabled>
                                @foreach($servicesList as $service)
                                    <option value="{{ $service->id }}">
                                        {{ $service->service_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            @if(!in_array($appointmentStatus, ['Cancelled', 'Completed']))
                                <button type="button" wire:click="updateStatus('Cancelled')" wire:confirm="Cancel this appointment?" class="px-5 py-3 rounded bg-red-100 hover:bg-red-200 text-red-700 font-semibold mr-auto">
                                    Cancel Appointment
                                </button>
                            @endif

                            @if($appointmentStatus === 'Scheduled')
                                <button type="button" wire:click="updateStatus('Ongoing')" class="px-5 py-3 rounded bg-blue-600 hover:bg-blue-700 text-white font-semibold">
                                    Mark as Ongoing
                                </button>
                            @elseif($appointmentStatus === 'Ongoing')
                                <button type="button" wire:click="updateStatus('Completed')" class="px-5 py-3 rounded bg-green-600 hover:bg-green-700 text-white font-semibold">
                                    Mark as Completed
                                </button>
                            @elseif($appointmentStatus === 'Completed')
                                <span class="px-5 py-3 rounded bg-green-100 text-green-800 font-bold border border-green-200">Completed</span>
                            @elseif($appointmentStatus === 'Cancelled')
                                <span class="px-5 py-3 rounded bg-red-100 text-red-800 font-bold border border-red-200">Cancelled</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
</div>