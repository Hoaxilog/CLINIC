<div class="h-full flex flex-col"  wire:poll.5s>
        
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
            <div class="relative" id="sortDropdown">
                <button 
                    onclick="document.getElementById('sortMenu').classList.toggle('hidden')"
                    class="flex shrink-0 items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 w-full sm:w-40 justify-center transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none" stroke="#141B34" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8.85746 12.5061C6.36901 10.6456 4.59564 8.59915 3.62734 7.44867C3.3276 7.09253 3.22938 6.8319 3.17033 6.3728C2.96811 4.8008 2.86701 4.0148 3.32795 3.5074C3.7889 3 4.60404 3 6.23433 3H17.7657C19.396 3 20.2111 3 20.672 3.5074C21.133 4.0148 21.0319 4.8008 20.8297 6.37281C20.7706 6.83191 20.6724 7.09254 20.3726 7.44867C19.403 8.60062 17.6261 10.6507 15.1326 12.5135C14.907 12.6821 14.7583 12.9567 14.7307 13.2614C14.4837 15.992 14.2559 17.4876 14.1141 18.2442C13.8853 19.4657 12.1532 20.2006 11.226 20.8563C10.6741 21.2466 10.0043 20.782 9.93278 20.1778C9.79643 19.0261 9.53961 16.6864 9.25927 13.2614C9.23409 12.9539 9.08486 12.6761 8.85746 12.5061Z" />
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
                    id="sortMenu"
                    class="hidden absolute right-1 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-50"
                >
                    <button wire:click.stop="setSort('recent')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ $sortOption === 'recent' ? 'bg-blue-50 text-blue-600 font-medium' : '' }}">
                        Recent (Newest)
                    </button>
                    <button wire:click.stop="setSort('oldest')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ $sortOption === 'oldest' ? 'bg-blue-50 text-blue-600 font-medium' : '' }}">
                        Oldest First
                    </button>
                    <button wire:click.stop="setSort('a_z')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ $sortOption === 'a_z' ? 'bg-blue-50 text-blue-600 font-medium' : '' }}">
                        Name (A-Z)
                    </button>
                    <button wire:click.stop="setSort('z_a')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ $sortOption === 'z_a' ? 'bg-blue-50 text-blue-600 font-medium' : '' }}">
                        Name (Z-A)
                    </button>
                </div>
            </div>

            <button 
                wire:click="$dispatch('openAddPatientModal')"
                type="button"
                class="active:outline-2 active:outline-offset-3 active:outline-dashed active:outline-black flex shrink-0 items-center gap-2 px-4 py-2.5 bg-[#0086da] text-white rounded-lg shadow-sm text-sm font-medium hover:bg-blue-00 w-full sm:w-auto justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2.5 12.0001C2.5 7.52171 2.5 5.28254 3.89124 3.8913C5.28249 2.50005 7.52166 2.50005 12 2.50005C16.4783 2.50005 18.7175 2.50005 20.1088 3.8913C21.5 5.28254 21.5 7.52171 21.5 12.0001C21.5 16.4784 21.5 18.7176 20.1088 20.1088C18.7175 21.5001 16.4783 21.5001 12 21.5001C7.52166 21.5001 5.28249 21.5001 3.89124 20.1088C2.5 18.7176 2.5 16.4784 2.5 12.0001Z" />
                    <path d="M12 8.00005V16.0001M16 12.0001L8 12.0001" />
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
                    <div 
                        wire:click="selectPatient({{ $patient->id }})"
                        class="w-full px-5 p-8 bg-white rounded-lg shadow-sm flex items-center justify-between transition-all cursor-pointer relative group
                        @if($patient->id == $selectedPatient?->id) border-l-4 border-[#0086da] @else hover:bg-gray-50 @endif"
                    >

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
                        
                        <button 
                            type="button"
                            wire:click.stop="deletePatient({{ $patient->id }})"
                            wire:confirm="Are you sure you want to delete this patient? All associated records will be removed permanently."
                            class="p-2 rounded-full hover:bg-red-50 transition-colors"
                            title="Delete Patient"
                        >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="#f56e6e" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M19.5 5.5L18.8803 15.5251C18.7219 18.0864 18.6428 19.3671 18.0008 20.2879C17.6833 20.7431 17.2747 21.1273 16.8007 21.416C15.8421 22 14.559 22 11.9927 22C9.42312 22 8.1383 22 7.17905 21.4149C6.7048 21.1257 6.296 20.7408 5.97868 20.2848C5.33688 19.3626 5.25945 18.0801 5.10461 15.5152L4.5 5.5" />
                            <path d="M3 5.5H21M16.0557 5.5L15.3731 4.09173C14.9196 3.15626 14.6928 2.68852 14.3017 2.39681C14.215 2.3321 14.1231 2.27454 14.027 2.2247C13.5939 2 13.0741 2 12.0345 2C10.9688 2 10.436 2 9.99568 2.23412C9.8981 2.28601 9.80498 2.3459 9.71729 2.41317C9.32164 2.7167 9.10063 3.20155 8.65861 4.17126L8.05292 5.5" />
                            <path d="M9.5 16.5L9.5 10.5" />
                            <path d="M14.5 16.5L14.5 10.5" />
                        </svg>
                        </button>

                    </div>

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
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-800">Patient Details</h2>
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

    {{-- ========================================== --}}
    {{-- GLOBAL NOTIFICATION TOAST (Add this block) --}}
    {{-- ========================================== --}}
    @if (session()->has('success') || session()->has('error') || session()->has('info'))
    <div 
        id="notification-toast"
        class="fixed bottom-5 right-5 z-[60] flex items-center gap-3 px-6 py-4 rounded-lg shadow-xl border transform transition-all duration-300 ease-in-out translate-y-0 opacity-100
        @if(session('success')) bg-green-50 border-green-200 text-green-800 
        @elseif(session('error')) bg-red-50 border-red-200 text-red-800 
        @else bg-blue-50 border-blue-200 text-blue-800 @endif"
    >
        {{-- Icons --}}
        @if(session('success'))
            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        @elseif(session('error'))
            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        @else
            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        @endif

        {{-- Message --}}
        <div class="font-medium text-sm">
            {{ session('success') ?? session('error') ?? session('info') }}
        </div>

        {{-- Close Button --}}
        <button onclick="document.getElementById('notification-toast').remove()" class="ml-4 text-gray-400 hover:text-gray-600 focus:outline-none">
            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
        </button>

        {{-- Auto-Hide Script --}}
        <script>
            setTimeout(function() {
                var toast = document.getElementById('notification-toast');
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

@push('script')
    <script>
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('sortDropdown');
            if (dropdown && !dropdown.contains(event.target)) {
                document.getElementById('sortMenu').classList.add('hidden');
            }
        });

        // Listen for Livewire dispatch to close dropdown
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('closeSortDropdown', () => {
                document.getElementById('sortMenu').classList.add('hidden');
            });
        });
    </script>
@endpush


