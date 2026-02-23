<div @if(!$showAppointmentModal && !$isPatientFormOpen) wire:poll.5s="loadDashboardData" @endif class="h-full">

    @php
        $completedAppointments = $todayAppointments->where('status', 'Completed');
        $scheduledAppointments = $todayAppointments->whereIn('status', ['Scheduled', 'Cancelled']);
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-[calc(100vh-220px)] min-h-[600px]">

        <div class="bg-gray-50/50 rounded-2xl border border-gray-200 flex flex-col overflow-hidden shadow-sm relative">
            <div class="absolute top-0 left-0 right-0 h-1 bg-blue-500"></div>
            <div class="flex items-center justify-between p-5 border-b border-gray-100 bg-white/50">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-blue-500"></div>
                    <h1 class="text-base font-bold text-gray-900">Today Schedule</h1>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-blue-700 bg-blue-100 px-2.5 py-1 rounded-full">{{ count($scheduledAppointments) }}</span>
                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2.5 py-1 rounded-full uppercase tracking-wider">
                        Completed Today: {{ count($completedAppointments) }}
                    </span>
                </div>
            </div>
            
            <div class="flex-1 overflow-y-auto p-4 space-y-3 scrollbar-thin scrollbar-track-transparent scrollbar-thumb-gray-200">
                @if(count($scheduledAppointments) > 0)
                    @foreach($scheduledAppointments as $app)
                        <div wire:click="viewAppointment({{ $app->id }})" class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm cursor-pointer hover:border-blue-300 hover:shadow-md hover:-translate-y-0.5 transition-all group">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1 min-w-0 mr-3">
                                    <h2 class="text-sm font-bold text-gray-900 truncate">{{ $app->first_name }} {{ $app->last_name }}</h2>
                                    <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $app->service_name }}</p>
                                </div>
                                <div class="text-xs font-bold text-blue-700 bg-blue-50 border border-blue-100 px-2 py-1 rounded-md shrink-0 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($app->appointment_date)->format('h:i A') }}
                                </div>
                            </div>
                            <div class="pt-3 mt-3 border-t border-gray-50 flex justify-between items-center">
                                <div class="text-[10px] font-bold uppercase tracking-wider {{ $app->status === 'Cancelled' ? 'text-rose-600' : 'text-gray-400' }}">
                                    {{ $app->status }}
                                </div>
                                <div class="text-xs font-semibold text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity">View →</div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="h-full flex flex-col items-center justify-center text-gray-400 text-sm">
                        <svg class="w-10 h-10 mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        No scheduled items
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-gray-50/50 rounded-2xl border border-gray-200 flex flex-col overflow-hidden shadow-sm relative">
            <div class="absolute top-0 left-0 right-0 h-1 bg-amber-500"></div>
            <div class="flex items-center justify-between p-5 border-b border-gray-100 bg-white/50">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></div>
                    <h1 class="text-base font-bold text-gray-900">Ready in Lobby</h1>
                    <span class="text-xs font-bold text-amber-700 bg-amber-100 px-2.5 py-1 rounded-full">{{ count($waitingQueue) }}</span>
                </div>
                @if(auth()->user()?->role === 1)
                    <button type="button" wire:click="callNextPatient" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-gray-900 text-white hover:bg-gray-800 shadow-sm transition">
                        Call Next
                    </button>
                @endif
            </div>
            
            <div class="flex-1 overflow-y-auto p-4 space-y-3 scrollbar-thin scrollbar-track-transparent scrollbar-thumb-gray-200">
                @if(count($waitingQueue) > 0)
                    @foreach($waitingQueue as $wait)
                        <div wire:click="viewAppointment({{ $wait->id }})" class="bg-white p-4 rounded-xl border {{ $loop->first ? 'border-amber-300 shadow-md ring-1 ring-amber-50' : 'border-gray-200 shadow-sm' }} cursor-pointer hover:border-amber-400 hover:-translate-y-0.5 transition-all group">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h2 class="text-sm font-bold text-gray-900">{{ $wait->first_name }} {{ $wait->last_name }}</h2>
                                        @if($loop->first)
                                            <span class="text-[9px] font-bold uppercase tracking-wider bg-amber-100 text-amber-700 px-2 py-0.5 rounded-md">Up Next</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Waiting: <span class="font-semibold text-gray-700">{{ \Carbon\Carbon::parse($wait->waited_at)->diffForHumans(null, true) }}</span></p>
                                </div>
                            </div>
                            <div class="pt-3 mt-3 border-t border-gray-50 flex justify-between items-center">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-500">Ready</span>
                                <div class="text-xs font-semibold text-amber-600 opacity-0 group-hover:opacity-100 transition-opacity">Open Chart →</div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="h-full flex flex-col items-center justify-center text-gray-400 text-sm">
                        <svg class="w-10 h-10 mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Lobby is empty
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-gray-50/50 rounded-2xl border border-gray-200 flex flex-col overflow-hidden shadow-sm relative">
            <div class="absolute top-0 left-0 right-0 h-1 bg-emerald-500"></div>
            <div class="flex items-center justify-between p-5 border-b border-gray-100 bg-white/50">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <h1 class="text-base font-bold text-gray-900">In Session</h1>
                </div>
            </div>
            
            <div class="flex-1 overflow-y-auto p-4 space-y-3 scrollbar-thin scrollbar-track-transparent scrollbar-thumb-gray-200">
                @if(count($ongoingAppointments) > 0)
                    @foreach($ongoingAppointments as $ongoing)
                        <div wire:click="viewAppointment({{ $ongoing->id }})" class="bg-white p-4 rounded-xl border-2 border-emerald-400 shadow-sm cursor-pointer hover:shadow-md hover:-translate-y-0.5 transition-all group">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h2 class="text-sm font-bold text-gray-900">{{ $ongoing->first_name }} {{ $ongoing->last_name }}</h2>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Dentist:
                                        <span class="font-semibold text-gray-700">
                                            {{ $ongoing->dentist_name ? 'Dr. ' . $ongoing->dentist_name : 'Unassigned' }}
                                        </span>
                                    </p>
                                </div>
                                <span class="bg-emerald-50 border border-emerald-100 text-emerald-700 text-[9px] font-bold px-2 py-1 rounded-md uppercase tracking-wider flex items-center gap-1.5 shrink-0">
                                    <svg class="w-3 h-3 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    In Chair
                                </span>
                            </div>
                            <div class="text-xs text-gray-600 mb-4 bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                                <span class="font-semibold text-gray-900 block mb-0.5">Procedure:</span> 
                                {{ $ongoing->service_name }}
                            </div>
                            <div class="pt-3 border-t border-gray-50 flex justify-between items-center">
                                <span class="text-xs text-gray-500">Started: {{ \Carbon\Carbon::parse($ongoing->appointment_date)->format('h:i A') }}</span>
                                <span class="text-xs font-bold text-emerald-600 group-hover:underline">Open Chart →</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="h-full flex flex-col items-center justify-center text-gray-400 text-sm">
                        <svg class="w-10 h-10 mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Chair is Empty
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if($showAppointmentModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" wire:click="closeAppointmentModal"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl mx-4 z-10 overflow-hidden border border-gray-100">
                
                <div class="px-6 py-5 flex items-center justify-between bg-white border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900">Appointment Details</h3>
                    <button class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-full transition" wire:click="closeAppointmentModal">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                @if (session()->has('error'))
                    <div class="bg-red-50 text-red-600 px-6 py-3 text-sm font-semibold border-b border-red-100 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ session('error') }}
                    </div>
                @endif

                <div class="p-6 overflow-y-auto max-h-[80vh]">
                    
                    <div class="mb-6 bg-blue-50/50 rounded-xl p-5 border border-blue-100">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-[10px] font-bold text-blue-500 uppercase tracking-wider mb-1">Date</label>
                                <input type="text" value="{{ \Carbon\Carbon::parse($selectedDate)->format('F j, Y') }}" class="w-full border-0 bg-transparent p-0 text-lg font-bold text-gray-900 focus:ring-0" readonly />
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-blue-500 uppercase tracking-wider mb-1">Start Time</label>
                                <input type="text" value="{{ $selectedTime }}" class="w-full border-0 bg-transparent p-0 text-lg font-bold text-gray-900 focus:ring-0" readonly />
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-blue-500 uppercase tracking-wider mb-1">End Time</label>
                                <input type="text" value="{{ $endTime }}" class="w-full border-0 bg-transparent p-0 text-lg font-bold text-gray-900 focus:ring-0" readonly />
                            </div>
                        </div>
                    </div>

                    @if($dentistName && ($appointmentStatus == 'Ongoing' || $appointmentStatus == 'Completed'))
                        <div class="mb-6 bg-emerald-50 border border-emerald-100 p-4 rounded-xl flex items-center gap-4">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode('Dr ' . $dentistName) }}&background=10b981&color=fff" class="w-10 h-10 rounded-full border-2 border-white shadow-sm">
                            <div>
                                <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">Assigned Dentist</p>
                                <p class="text-base font-bold text-gray-900">Dr. {{ $dentistName }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">First Name</label>
                            <input wire:model="firstName" type="text" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 bg-gray-50/50 text-gray-900 font-medium outline-none" readonly/>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Middle Name</label>
                            <input wire:model="middleName" type="text" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 bg-gray-50/50 text-gray-900 font-medium outline-none" readonly/>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Last Name</label>
                            <input wire:model="lastName" type="text" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 bg-gray-50/50 text-gray-900 font-medium outline-none" readonly/>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Contact Number</label>
                            <input wire:model="contactNumber" type="text" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 bg-gray-50/50 text-gray-900 font-medium outline-none" readonly/>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Service Required</label>
                            <select wire:model="selectedService" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 bg-white text-gray-900 font-medium focus:ring-2 focus:ring-blue-100 outline-none" {{ ($appointmentStatus != 'Waiting') ? 'disabled' : '' }}>
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
                            <button type="button" wire:click="updateStatus('Cancelled')" wire:confirm="Are you sure you want to cancel this appointment?" class="px-5 py-2.5 rounded-lg text-rose-600 font-semibold hover:bg-rose-50 mr-auto transition">
                                Cancel Appointment
                            </button>
                        @endif

                        @if($appointmentStatus === 'Scheduled')
                            <button type="button" wire:click="processPatient" class="px-5 py-2.5 rounded-lg bg-white border border-gray-200 text-gray-700 font-semibold hover:bg-gray-50 transition">
                                Update Info
                            </button>
                            <button type="button" wire:click="updateStatus('Waiting')" class="px-5 py-2.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-semibold shadow-sm transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Mark as Arrived (Lobby)
                            </button>

                        @elseif($appointmentStatus === 'Waiting')
                            <button type="button" wire:click="processPatient" class="px-5 py-2.5 rounded-lg bg-white border border-gray-200 text-gray-700 font-semibold hover:bg-gray-50 transition">
                                View Patient Info
                            </button>
                            <button type="button" wire:click="admitPatient" class="px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold shadow-sm transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                Call to Chair
                            </button>

                        @elseif($appointmentStatus === 'Ongoing')
                            <button type="button" wire:click="openPatientChart" class="px-5 py-2.5 rounded-lg bg-gray-900 hover:bg-gray-800 text-white font-semibold shadow-sm transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Open Chart
                            </button>
                            <button type="button" wire:click="updateStatus('Completed')" class="px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold shadow-sm transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Finish Session
                            </button>

                        @elseif($appointmentStatus === 'Completed')
                            <span class="px-5 py-2.5 rounded-lg bg-emerald-50 text-emerald-700 font-bold border border-emerald-200 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Completed
                            </span>
                        @elseif($appointmentStatus === 'Cancelled')
                            <span class="px-5 py-2.5 rounded-lg bg-rose-50 text-rose-700 font-bold border border-rose-200 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Cancelled
                            </span>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('success') || session()->has('error') || session()->has('info'))
    <div 
        id="schedule-toast"
        class="fixed bottom-5 right-5 z-[70] flex items-center gap-3 px-6 py-4 rounded-xl shadow-lg border transform transition-all duration-300 ease-in-out translate-y-0 opacity-100
        @if(session('success')) bg-emerald-50 border-emerald-200 text-emerald-800 
        @elseif(session('error')) bg-rose-50 border-rose-200 text-rose-800 
        @else bg-blue-50 border-blue-200 text-blue-800 @endif"
    >
        @if(session('success'))
            <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        @elseif(session('error'))
            <svg class="h-5 w-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        @else
            <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        @endif

        <div class="font-semibold text-sm">
            {{ session('success') ?? session('error') ?? session('info') }}
        </div>

        <button onclick="document.getElementById('schedule-toast').remove()" class="ml-4 text-gray-400 hover:text-gray-700 focus:outline-none">
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
            }, 3000);
        </script>
    </div>
    @endif
</div>
