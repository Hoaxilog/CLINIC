{{-- 
    MODIFIED: Added 'h-full flex flex-col'
    This makes the component fill its parent and become a flex container
--}}
<div class="h-full flex flex-col">
        
    <!-- Header (No change) -->
    <div class="flex flex-col gap-4 mb-6">
        <!-- Title -->
        <h1 class="text-3xl font-bold text-gray-800">Patient Records</h1>
        
        <div class="flex  items-center  gap-3">
            <div class="relative w-full sm:w-auto bg-white">
                <input type="text" placeholder="Search by name" class=" w-96 pl-10 pr-4 py-2.5 border border-black rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                    wire:model.live.debounce.300ms="search">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                </svg>
            </div>
            
            <!-- Recent Button -->
            <div class="relative" x-data="{ open: false }">
                <button 
                    @click="open = !open"
                    @click.away="open = false"
                    class="flex shrink-0 items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 w-full sm:w-40 justify-center transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-down h-4 w-4 text-gray-600">
                        <path d="m21 16-4 4-4-4"/><path d="M17 20V4"/><path d="m3 8 4-4 4 4"/><path d="M7 4v16"/>
                    </svg>
                    <span>
                        @switch($sortOption)
                            @case('oldest') Oldest @break
                            @case('a_z') Name (A-Z) @break
                            @case('z_a') Name (Z-A) @break
                            @default Recent
                        @endswitch
                    </span>
                </button>

                <!-- Dropdown Menu -->
                <div 
                    x-show="open"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-50"
                    style="display: none;"
                >
                    <button wire:click="setSort('recent')" @click="open = false" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ $sortOption === 'recent' ? 'bg-blue-50 text-blue-600 font-medium' : '' }}">
                        Recent (Newest)
                    </button>
                    <button wire:click="setSort('oldest')" @click="open = false" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ $sortOption === 'oldest' ? 'bg-blue-50 text-blue-600 font-medium' : '' }}">
                        Oldest First
                    </button>
                    <button wire:click="setSort('a_z')" @click="open = false" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ $sortOption === 'a_z' ? 'bg-blue-50 text-blue-600 font-medium' : '' }}">
                        Name (A-Z)
                    </button>
                    <button wire:click="setSort('z_a')" @click="open = false" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ $sortOption === 'z_a' ? 'bg-blue-50 text-blue-600 font-medium' : '' }}">
                        Name (Z-A)
                    </button>
                </div>
            </div>

            <!-- Add Patient Button -->
            <button 
                wire:click="$dispatch('openAddPatientModal')"
                type="button"
                class="active:outline-2 active:outline-offset-3 active:outline-dashed active:outline-black flex shrink-0 items-center gap-2 px-4 py-2.5 bg-[#0086da] text-white rounded-lg shadow-sm text-sm font-medium hover:bg-blue-00 w-full sm:w-auto justify-center">
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
                    <button wire:click="selectPatient({{ $patient->id }})"
                        class="w-full px-5 p-8 bg-white rounded-lg shadow-sm flex items-center justify-between transition-all
                        @if($patient->id == $selectedPatient?->id) border-l-4 border-[#0086da] @else hover:bg-gray-50 @endif">

                        <!-- LEFT SIDE -->
                        <div class="flex items-center gap-4">

                            <!-- LEFT SVG -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="@if($patient->id == $selectedPatient?->id) text-[#0086da] @else text-gray-500 @endif">
                                <path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z"/>
                                <path d="M14 2v5a1 1 0 0 0 1 1h5"/>
                                <path d="M16 22a4 4 0 0 0-8 0"/>
                                <circle cx="12" cy="15" r="3"/>
                            </svg>

                            <!-- LEFT-ALIGNED TEXT -->
                            <div class="text-left">
                                <div class="text-xl font-semibold text-black">
                                    {{ $patient->first_name }} {{ $patient->last_name }}
                                </div>
                                {{-- <div class="text-lg text-gray-600">{{ $patient->mobile_number }}</div> --}}
                                {{-- <div class="text-lg text-gray-500">{{ $patient->home_address }}</div> --}}
                            </div>

                        </div>

                        <!-- RIGHT TRASH ICON -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                            stroke="#f56e6e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/>
                            <path d="M3 6h18"/>
                            <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>

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
                <div class="bg-white rounded-2xl shadow-lg p-8 space-y-5">
                    <!-- Details Header -->
                    <h2 class="text-2xl font-semibold text-gray-800">Patient Details</h2>

                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-4xl font-bold text-black mt-2">
                                {{ $selectedPatient->first_name }} {{ $selectedPatient->last_name }}
                            </h3>
                        </div>
                        <span class="mt-1 bg-green-100 text-green-700 text-sm font-medium px-4 py-1.5 rounded-full">
                            {{ $selectedPatient->status ?? 'active' }}
                        </span>
                    </div>

                    <!-- Contact Info -->
                    <div class="space-y-5 ml-10">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail-icon lucide-mail"><path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"/><rect x="2" y="4" width="20" height="16" rx="2"/></svg>
                            <div class="ml-4">
                                <div class="text-xl font-semibold text-black">Email: <span> {{ $selectedPatient->email ?? 'N/A' }} </span></div>
                                {{-- Your DB schema didn't show 'email'. Add ?? 'N/A' if the column might be null --}}
                            </div>
                        </div>
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-icon lucide-phone"><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"/></svg>
                            <div class="ml-4">
                                <div class="text-xl font-semibold text-black">Contact: <span> {{ $selectedPatient->mobile_number ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin-icon lucide-map-pin"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
                            <div class="ml-4">
                                <div class="text-xl font-semibold text-black">Address: <span> {{ $selectedPatient->home_address ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <hr class="my-8 border-gray-200">

                    <!-- Appointment Record - WIRED to $lastVisit -->
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Appointment Record</h3>
                        <div class="text-gray-600 mb-6 ml-10">
                            <span class="text-xl font-medium text-black">Last visit:</span> 
                            @if ($lastVisit)
                                <span class="text-xl"> {{ \Carbon\Carbon::parse($lastVisit->appointment_date)->format('M d, Y') }}</span>
                            @else
                                <span class="text-xl"> No completed visits found. </span>
                            @endif
                        </div>
                        
                        <!-- Tabs/Buttons -->
                        <div class="flex space-x-2">
                            <button 
                                wire:click="$dispatch('open-history-modal', { patientId: {{ $selectedPatient->id }} })"
                                class="active:outline-2 active:outline-offset-3 active:outline-dashed active:outline-black px-5 py-3 rounded-lg text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200">
                                Appointment History
                            </button>
                            {{-- MODIFIED: Added wire:click to dispatch 'editPatient' with the ID --}}
                            <button 
                                wire:click="$dispatch('editPatient', { id: {{ $selectedPatient->id }} })"
                                class="active:outline-2 active:outline-offset-3 active:outline-dashed active:outline-black px-5 py-3 rounded-lg text-sm font-medium text-white bg-[#0086da] shadow-sm">
                                View Patient Info
                            </button>
                        </div>
                    </div>

                </div>
            @else
                {{-- MODIFIED: Removed 'sticky top-8' here as well --}}
                <div class="bg-white rounded-2xl shadow-lg p-8 flex items-center justify-center h-full">
                    <p class="text-gray-500">Please select a patient to view details.</p>
                </div>
            @endif

        </div>

    </div>
</div>