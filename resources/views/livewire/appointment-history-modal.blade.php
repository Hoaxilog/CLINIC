<div>
    @if($showModal && $patient)
        {{-- Backdrop --}}
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70" x-data="{}">
            {{-- Modal Container --}}
            <div class="bg-white rounded-lg shadow-xl w-full max-w-7xl mx-auto m-8 flex flex-col min-h-[90vh] max-h-[90vh]">
            
                <div class="flex-none p-8 pb-4 border-b border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <!-- Patient Name -->
                        <h2 class="text-3xl font-bold text-gray-800">
                            {{ $patient->first_name }} {{ $patient->last_name }}
                        </h2>

                        <!-- Go Back Button -->
                        <button 
                            wire:click="closeModal" 
                            class="active:outline-2 active:outline-offset-3 active:outline-dashed active:outline-black bg-[#F06565] hover:outline-2 hover:outline-[#F06565] text-white px-6 py-2 rounded shadow flex items-center gap-2 transition"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/>
                            </svg>
                            Go back
                        </button>
                    </div>

                    <h3 class="text-xl font-semibold text-gray-800">Appointment history</h3>
                </div>

                {{-- 2. Scrollable List Section --}}
                <div class="flex-1 overflow-y-auto px-8 pb-10">
                    
                    @if(count($appointmentHistory) > 0)
                        <div class="flex flex-col">
                            @foreach($appointmentHistory as $appt)
                                <div class="flex flex-col md:flex-row items-start md:items-start justify-between py-6 border-b border-gray-300 gap-4 md:gap-0 hover:bg-gray-50 transition px-2 rounded-lg">
                                    {{-- Date --}}
                                    <div class="flex items-center gap-3 w-full md:w-1/4">
                                        <svg class="text-gray-500 h-6 w-6 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/>
                                        </svg>
                                        <span class="text-gray-500 text-lg">
                                            {{ \Carbon\Carbon::parse($appt->appointment_date)->format('F j, Y') }}
                                        </span>
                                    </div>

                                    {{-- Time & Duration --}}
                                    <div class="flex items-center gap-3 w-full md:w-1/4">
                                        <svg class="text-gray-500 h-6 w-6 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                        </svg>
                                        <span class="text-gray-500 text-lg">
                                            {{ \Carbon\Carbon::parse($appt->appointment_date)->format('g:i A') }} 
                                            ({{ $appt->duration_minutes ?? 0 }} min)
                                        </span>
                                    </div>

                                    {{-- Service Name --}}
                                    <div class="flex items-center gap-3 w-full md:w-1/3">
                                        <svg class="text-gray-500 h-6 w-6 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><line x1="10" x2="8" y1="9" y2="9"/>
                                        </svg>
                                        <span class="text-gray-500 text-lg truncate pr-4" title="{{ $appt->service_name }}">
                                            {{ $appt->service_name }}
                                        </span>
                                    </div>

                                    {{-- Status --}}
                                    <div class="w-full md:w-auto text-right md:pr-8">
                                        @php
                                            $status = $appt->status;
                                            $colorClass = match($status) {
                                                'Completed' => 'text-[#3B82F6]', 
                                                'Scheduled' => 'text-[#22C55E]', 
                                                'Cancelled' => 'text-[#EF4444]', 
                                                'Upcoming'  => 'text-[#22C55E]',  
                                                default => 'text-gray-500',
                                            };
                                            
                                            $displayStatus = ($status === 'Scheduled') ? 'Upcoming' : $status;
                                        @endphp
                                        <span class="text-lg font-medium {{ $colorClass }}">
                                            {{ $displayStatus }}
                                        </span>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center h-64 text-gray-400">
                            <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            <p class="text-xl">No appointment history found.</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    @endif
</div>