<div @if(!$showAppointmentModal && !$isPatientFormOpen) wire:poll.5s="loadDashboardData" @endif>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-[500px]">

        <div class="bg-white rounded-lg shadow-md flex flex-col border-t-4 border-[#0086da] overflow-hidden">
            <div class="flex items-center justify-between p-4 border-b bg-blue-50">
                <h1 class="text-lg font-bold text-gray-800">Today Schedule</h1>
                <span class="text-xs font-bold text-blue-600 bg-blue-100 px-2 py-1 rounded-full">{{ count($todayAppointments) }}</span>
            </div>
            <div class="flex-1 overflow-y-auto p-3 space-y-2 scrollbar-thin scrollbar-track-[#ccebff] scrollbar-thumb-[#0086da]">
                @if(count($todayAppointments) > 0)
                    @foreach($todayAppointments as $app)
                        <div wire:click="viewAppointment({{ $app->id }})" class="bg-[#ccebff] p-3 rounded-lg cursor-pointer hover:bg-blue-200 transition">
                            <div class="flex justify-between items-center">
                                <div class="flex-1 min-w-0 mr-2">
                                    <h2 class="text-sm font-bold text-gray-900 uppercase truncate">{{ $app->first_name }} {{ $app->last_name }}</h2>
                                    <p class="text-xs text-gray-600 truncate">{{ $app->service_name }}</p>
                                </div>
                                <div class="text-right whitespace-nowrap">
                                    <div class="text-xs font-bold text-[#0086da] bg-white px-2 py-0.5 rounded mb-1 inline-block">
                                        {{ \Carbon\Carbon::parse($app->appointment_date)->format('h:i A') }}
                                    </div>
                                    <div class="text-[10px] font-bold uppercase {{ $app->status == 'Completed' ? 'text-green-600' : 'text-gray-500' }}">
                                        {{ $app->status }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="h-full flex items-center justify-center text-gray-400 text-sm">No scheduled items</div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md flex flex-col border-t-4 border-red-400 overflow-hidden">
            <div class="flex items-center justify-between p-4 border-b bg-red-50">
                <div class="flex items-center gap-3">
                    <h1 class="text-lg font-bold text-gray-800">Ready Queue</h1>
                    <span class="text-xs font-bold text-red-600 bg-red-100 px-2 py-1 rounded-full">{{ count($waitingQueue) }}</span>
                </div>
                @if(auth()->user()?->role === 1)
                    <button type="button" wire:click="callNextPatient" class="px-3 py-1.5 text-xs font-bold rounded bg-red-500 text-white hover:bg-red-600 transition">
                        Call Next
                    </button>
                @endif
            </div>
            <div class="flex-1 overflow-y-auto p-3 space-y-2 scrollbar-thin scrollbar-track-red-100 scrollbar-thumb-red-400">
                @if(count($waitingQueue) > 0)
                    @foreach($waitingQueue as $wait)
                        <div wire:click="viewAppointment({{ $wait->id }})" class="bg-red-50 border border-red-200 p-3 rounded-lg cursor-pointer hover:bg-red-100 transition group">
                            <div class="flex justify-between items-center">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h2 class="text-sm font-bold text-gray-900 uppercase">{{ $wait->first_name }} {{ $wait->last_name }}</h2>
                                        @if($loop->first)
                                            <span class="text-[10px] font-bold uppercase bg-red-200 text-red-700 px-2 py-0.5 rounded-full">Next</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 mt-0.5">Ready: {{ \Carbon\Carbon::parse($wait->waited_at)->diffForHumans(null, true) }}</p>
                                </div>
                                <div class="text-xs font-bold text-red-500 group-hover:underline">Open →</div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="h-full flex items-center justify-center text-gray-400 text-sm">Lobby is empty</div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md flex flex-col border-t-4 border-green-500 overflow-hidden">
            <div class="flex items-center justify-between p-4 border-b bg-green-50">
                <h1 class="text-lg font-bold text-gray-800">Session</h1>
                <span class="relative flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
            </div>
            <div class="flex-1 overflow-y-auto p-3 space-y-2 scrollbar-thin scrollbar-track-green-100 scrollbar-thumb-green-500">
                @if(count($ongoingAppointments) > 0)
                    @foreach($ongoingAppointments as $ongoing)
                        <div wire:click="viewAppointment({{ $ongoing->id }})" class="bg-green-50 border border-green-200 p-4 rounded-lg cursor-pointer hover:bg-green-100 transition shadow-sm">
                            <div class="flex flex-col gap-2">
                                <div class="flex justify-between items-start">
                                    <h2 class="text-lg font-bold text-gray-900 uppercase">{{ $ongoing->first_name }} {{ $ongoing->last_name }}</h2>
                                    <span class="bg-green-200 text-green-800 text-[10px] font-bold px-2 py-0.5 rounded uppercase">In Chair</span>
                                </div>
                                <div class="text-sm text-gray-700 font-medium">Procedure: {{ $ongoing->service_name }}</div>
                                <div class="mt-2 pt-2 border-t border-green-200 flex justify-between items-center">
                                    <span class="text-xs text-gray-500">Time: {{ \Carbon\Carbon::parse($ongoing->appointment_date)->format('h:i A') }}</span>
                                    <span class="text-xs font-bold text-green-700">Open Chart →</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="h-full flex items-center justify-center text-gray-400 text-sm">Chair is Empty</div>
                @endif
            </div>
        </div>
    </div>

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

                    {{-- [ADDED] DENTIST ASSIGNED DISPLAY --}}
                @if($dentistName && ($appointmentStatus == 'Ongoing' || $appointmentStatus == 'Completed'))
                    <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded shadow-sm">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-blue-900">
                                    Assigned Dentist
                                </p>
                                <p class="text-lg font-bold text-blue-700">
                                    Dr. {{ $dentistName }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

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
                            <select wire:model="selectedService" class="w-full border-gray-300 rounded-lg px-4 py-2.5 text-gray-800 font-medium focus:ring-blue-500 focus:border-blue-500" {{ ($appointmentStatus != 'Waiting') ? 'disabled' : '' }}>
                                @foreach($servicesList as $service)
                                    <option value="{{ $service->id }}">
                                        {{ $service->service_name }} ({{ \Carbon\Carbon::parse($service->duration)->format('H:i') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-100">
                        
                        @if(!in_array($appointmentStatus, ['Cancelled', 'Completed']))
                            <button type="button" wire:click="updateStatus('Cancelled')" wire:confirm="Cancel this appointment?" class="px-5 py-2.5 rounded-lg text-red-600 font-medium hover:bg-red-50 border border-transparent hover:border-red-100 mr-auto transition">
                                Cancel Appointment
                            </button>
                        @endif

                        @if($appointmentStatus === 'Scheduled')
                            <button type="button" wire:click="processPatient" class="px-6 py-2.5 rounded-lg bg-white border-2 border-blue-600 text-blue-700 font-bold hover:bg-blue-50 transition">
                                Update Patient Info
                            </button>
                            <button type="button" wire:click="updateStatus('Waiting')" class="px-6 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-md hover:shadow-lg transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Mark Ready
                            </button>

                        @elseif($appointmentStatus === 'Waiting')
                            <button type="button" wire:click="processPatient" class="px-6 py-2.5 rounded-lg bg-white border-2 border-gray-300 text-gray-600 font-bold hover:bg-gray-50 transition">
                                View Patient Info
                            </button>
                            <button type="button" wire:click="admitPatient" class="px-6 py-2.5 rounded-lg bg-red-500 hover:bg-red-600 text-white font-bold shadow-md hover:shadow-lg transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                ADMIT PATIENT
                            </button>

                        @elseif($appointmentStatus === 'Ongoing')
                            <button type="button" wire:click="openPatientChart" class="px-6 py-2.5 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-bold shadow-md hover:shadow-lg transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                View Dental Chart
                            </button>
                            <button type="button" wire:click="updateStatus('Completed')" class="px-6 py-2.5 rounded-lg bg-green-600 hover:bg-green-700 text-white font-bold shadow-md hover:shadow-lg transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Finish & Complete
                            </button>

                        @elseif($appointmentStatus === 'Completed')
                            <span class="px-6 py-2.5 rounded-lg bg-green-100 text-green-800 font-bold border border-green-200">
                                ✅ Completed
                            </span>
                        @elseif($appointmentStatus === 'Cancelled')
                            <span class="px-6 py-2.5 rounded-lg bg-red-100 text-red-800 font-bold border border-red-200">
                                ❌ Cancelled
                            </span>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    @endif
    @if (session()->has('success') || session()->has('error') || session()->has('info'))
    <div 
        id="schedule-toast" {{-- I renamed the ID to avoid conflicts --}}
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

        <button onclick="document.getElementById('schedule-toast').remove()" class="ml-4 text-gray-400 hover:text-gray-600 focus:outline-none">
            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
        </button>

        <script>
            setTimeout(function() {
                var toast = document.getElementById('schedule-toast');
                if (toast) {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(10px)';
                    setTimeout(function() { toast.remove(); }, 500);
                }
            }, 3000); {{-- I reduced this to 3000 (3 seconds) for a snappier feel --}}
        </script>
    </div>
    @endif
</div>

