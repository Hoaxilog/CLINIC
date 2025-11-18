{{-- 
    MODIFIED: Added 'h-full flex flex-col'
    This makes the component fill its parent and become a flex container
--}}
<div class="h-full flex flex-col">
        
    <!-- Header (No change) -->
    <div class="flex flex-col gap-4 mb-6">
        <!-- Title -->
        <h1 class="text-3xl font-bold text-gray-800">Patient Records</h1>
        
        <!-- Search and Actions Wrapper -->
        <div class="flex flex-col sm:flex-row items-center justify-start sm:justify-end gap-3">
            <!-- Search Input - WIRED to $search -->
            <div class="relative w-full sm:w-auto">
                <input 
                    type="text" 
                    placeholder="Search by name" 
                    class="w-full sm:w-56 pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                    wire:model.live.debounce.300ms="search" 
                >
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                </svg>
            </div>
            
            <!-- Recent Button -->
            <button class="flex shrink-0 items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 w-full sm:w-auto justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-down h-4 w-4 text-gray-600">
                    <path d="m21 16-4 4-4-4"/><path d="M17 20V4"/><path d="m3 8 4-4 4 4"/><path d="M7 4v16"/>
                </svg>
                Recent
            </button>

            <!-- Add Patient Button -->
            {{-- MODIFIED: Added wire:click to dispatch the event --}}
            <button 
                wire:click="$dispatch('openAddPatientModal')"
                type="button"
                class="flex shrink-0 items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg shadow-sm text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 w-full sm:w-auto justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus h-4 w-4">
                    <path d="M5 12h14"/><path d="M12 5v14"/>
                </svg>
                Add new patient
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 flex-1 overflow-hidden">
        <!-- Left Column: Patient List -->
        <div class="flex flex-col overflow-hidden">
            <!-- List Container -->
            <div class="space-y-3 flex-1 overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-rounded-full scrollbar-track-[#ccebff] scrollbar-thumb-[#0086da]">
                @forelse($patients as $patient)
                    <button 
                        wire:click="selectPatient({{ $patient->id }})"
                        class="w-full text-left p-4 bg-white rounded-lg shadow-sm flex items-center gap-4 transition-all
                               @if($patient->id == $selectedPatient?->id) 
                                   border-l-4 border-blue-500 
                               @else 
                                   hover:bg-gray-50 
                               @endif" 
                    >
                        <!-- User-provided SVG Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                             class="lucide lucide-file-user-icon lucide-file-user flex-shrink-0 h-8 w-8 
                                    @if($patient->id == $selectedPatient?->id) text-blue-600 @else text-gray-500 @endif">
                            <path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z"/><path d="M14 2v5a1 1 0 0 0 1 1h5"/><path d="M16 22a4 4 0 0 0-8 0"/><circle cx="12" cy="15" r="3"/>
                        </svg>
                        <div>
                            <div class="font-semibold text-gray-800">{{ $patient->first_name }} {{ $patient->last_name }}</div>
                            <div class="text-sm text-gray-600">{{ $patient->mobile_number }}</div>
                            <div class="text-sm text-gray-500">{{ $patient->home_address }}</div>
                        </div>
                    </button>
                @empty
                    <div class="p-4 text-center text-gray-500">
                        No patients found for "{{ $search }}".
                    </div>
                @endforelse
               
            </div>
            <!-- Pagination links -->
            <div class="mt-4">
                {{ $patients->links() }}
            </div>
        </div>

        <!-- Right Column: Patient Details -->
        <div class="overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-rounded-full scrollbar-track-[#ccebff] scrollbar-thumb-[#0086da]">
            @if ($selectedPatient)
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <!-- Details Header -->
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-800">Patient Details</h2>
                            <h3 class="text-2xl font-semibold text-gray-700 mt-2">
                                {{ $selectedPatient->first_name }} {{ $selectedPatient->last_name }}
                            </h3>
                        </div>
                        <span class="mt-1 bg-green-100 text-green-700 text-sm font-medium px-4 py-1.5 rounded-full">
                            {{-- Your DB schema didn't show a 'status'. Using 'active' as a placeholder. --}}
                            {{ $selectedPatient->status ?? 'active' }}
                        </span>
                    </div>

                    <!-- Contact Info -->
                    <div class="space-y-5">
                        <div class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail flex-shrink-0 h-5 w-5 text-gray-500 mt-1">
                                <rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                            </svg>
                            <div class="ml-4">
                                <div class="font-semibold text-gray-700">Email:</div>
                                <div class="text-gray-600">{{ $selectedPatient->email ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone flex-shrink-0 h-5 w-5 text-gray-500 mt-1">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2-19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.63A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                            <div class="ml-4">
                                <div class="font-semibold text-gray-700">Contact:</div>
                                <div class="text-gray-600">{{ $selectedPatient->mobile_number ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin flex-shrink-0 h-5 w-5 text-gray-500 mt-1">
                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                            </svg>
                            <div class="ml-4">
                                <div class="font-semibold text-gray-700">Address:</div>
                                <div class="text-gray-600">
                                    {{ $selectedPatient->home_address ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <hr class="my-8 border-gray-200">

                    <!-- Appointment Record - WIRED to $lastVisit -->
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Appointment Record</h3>
                        <div class="text-gray-600 mb-6">
                            <span class="font-medium">Last visit:</span> 
                            @if ($lastVisit)
                                {{ \Carbon\Carbon::parse($lastVisit->appointment_date)->format('M d, Y') }}
                            @else
                                No completed visits found.
                            @endif
                        </div>
                        
                        <!-- Tabs/Buttons -->
                        <div class="flex space-x-2">
                            <button class="px-5 py-2.5 rounded-lg text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200">
                                Appointment History
                            </button>
                            <button class="px-5 py-2.5 rounded-lg text-sm font-medium text-white bg-blue-600 shadow-sm">
                                Patient Records
                            </button>
                        </div>
                    </div>

                </div>
            @else
                <div class="bg-white rounded-2xl shadow-lg p-8 flex items-center justify-center h-full">
                    <p class="text-gray-500">Please select a patient to view details.</p>
                </div>
            @endif
        </div>
    </div>
</div>