@php
    $isPatientUser = auth()->check() && auth()->user()->role === 3;
@endphp
<div class="h-full flex flex-col" wire:poll.5s>

    <!-- Header (No change) -->
    <div class="flex flex-col gap-4 mb-6">
        @if (!$showProfile)
            <div class="rounded-none border border-gray-200 bg-white p-4 shadow-sm lg:p-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center">
                        @if (!$isPatientUser)
                            <button wire:click="$dispatch('openAddPatientModal')" type="button"
                                class="inline-flex items-center justify-center gap-2 rounded-none bg-[#0086da] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#0073a8] active:outline-2 active:outline-dashed active:outline-offset-2 active:outline-black">
                                <span class="text-base leading-none">+</span>
                                <span>Add Patient</span>
                            </button>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-2">
                        @if (!$isPatientUser)
                            <div class="relative w-full min-w-[16rem] bg-white md:w-[22rem]">
                                <input type="text" placeholder="Search patient name..."
                                    class="w-full rounded-none border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-700 shadow-sm focus:border-[#0086da] focus:outline-none focus:ring-2 focus:ring-[#0086da]"
                                    wire:model.live.debounce.300ms="search">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400">
                                    <circle cx="11" cy="11" r="8" />
                                    <path d="m21 21-4.3-4.3" />
                                </svg>
                            </div>

                            <div class="relative" id="sortDropdown">
                                <button onclick="document.getElementById('sortMenu').classList.toggle('hidden')"
                                    class="inline-flex items-center gap-2 rounded-none border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                                    <span>
                                        @switch($sortOption)
                                            @case('oldest')
                                                Oldest
                                            @break

                                            @case('a_z')
                                                Name (A-Z)
                                            @break

                                            @case('z_a')
                                                Name (Z-A)
                                            @break

                                            @default
                                                Recent
                                        @endswitch
                                    </span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div id="sortMenu"
                                    class="absolute right-0 z-50 mt-2 hidden w-52 rounded-none border border-gray-200 bg-white py-1 shadow-lg">
                                    <button wire:click.stop="setSort('recent')"
                                        class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 {{ $sortOption === 'recent' ? 'bg-blue-50 font-semibold text-blue-700' : '' }}">
                                        Recent (Newest)
                                    </button>
                                    <button wire:click.stop="setSort('oldest')"
                                        class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 {{ $sortOption === 'oldest' ? 'bg-blue-50 font-semibold text-blue-700' : '' }}">
                                        Oldest First
                                    </button>
                                    <button wire:click.stop="setSort('a_z')"
                                        class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 {{ $sortOption === 'a_z' ? 'bg-blue-50 font-semibold text-blue-700' : '' }}">
                                        Name (A-Z)
                                    </button>
                                    <button wire:click.stop="setSort('z_a')"
                                        class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 {{ $sortOption === 'z_a' ? 'bg-blue-50 font-semibold text-blue-700' : '' }}">
                                        Name (Z-A)
                                    </button>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center rounded-none border border-gray-200 bg-white p-1 shadow-sm">
                            <button type="button" wire:click="setViewMode('table')"
                                class="rounded-none p-2 transition {{ $viewMode === 'table' ? 'bg-[#0086da] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}"
                                title="Table view" aria-label="Table view">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="h-4 w-4 lucide lucide-table-icon lucide-table">
                                    <path d="M12 3v18" />
                                    <rect width="18" height="18" x="3" y="3" rx="2" />
                                    <path d="M3 9h18" />
                                    <path d="M3 15h18" />
                                </svg>
                            </button>
                            <button type="button" wire:click="setViewMode('cards')"
                                class="rounded-none p-2 transition {{ $viewMode === 'cards' ? 'bg-[#0086da] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}"
                                title="Card view" aria-label="Card view">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="h-4 w-4 lucide lucide-grid3x3-icon lucide-grid-3x3">
                                    <rect width="18" height="18" x="3" y="3" rx="2" />
                                    <path d="M3 9h18" />
                                    <path d="M3 15h18" />
                                    <path d="M9 3v18" />
                                    <path d="M15 3v18" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if ($showProfile && $selectedPatient)
        @php
            $patientCode = sprintf('PT%04d', $selectedPatient->id);
            $patientFullName = trim(($selectedPatient->first_name ?? '') . ' ' . ($selectedPatient->last_name ?? ''));
            $patientInitials = strtoupper(
                substr($selectedPatient->first_name ?? 'P', 0, 1) . substr($selectedPatient->last_name ?? '', 0, 1),
            );
            $patientType = $selectedPatient->patient_type ?? 'Inactive';
            $lastVisitLabel = $selectedPatient->last_completed_at
                ? \Carbon\Carbon::parse($selectedPatient->last_completed_at)->format('M d, Y')
                : 'No visits yet';
            $patientAge = $selectedPatient->birth_date
                ? \Carbon\Carbon::parse($selectedPatient->birth_date)->age
                : null;
        @endphp

        <div class="flex flex-col flex-1 overflow-hidden">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Patient Details</h2>
                    <p class="text-xs text-gray-500">Patient Records / Profile</p>
                </div>
                <button type="button" wire:click="backToList"
                    class="rounded-none border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    Back to Patient Records
                </button>
            </div>

            <div
                class="flex-1 overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-rounded-full scrollbar-track-[#ccebff] scrollbar-thumb-[#0086da]">
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <div class="space-y-6">
                        <div class="rounded-none border border-gray-200 bg-white p-6 shadow-sm">
                            <div class="flex items-center gap-4">
                                <div
                                    class="h-16 w-16 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xl font-bold">
                                    {{ $patientInitials }}
                                </div>
                                <div>
                                    <span
                                        class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide text-blue-700">
                                        #{{ $patientCode }}
                                    </span>
                                    <h3 class="mt-2 text-lg font-semibold text-gray-900">
                                        {{ $patientFullName ?: 'Unnamed patient' }}</h3>
                                    <p class="text-xs text-gray-500">Last visited: {{ $lastVisitLabel }}</p>
                                </div>
                            </div>

                            <div class="mt-5 flex items-center gap-2">
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide
                            {{ $patientType === 'Active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $patientType }}
                                </span>
                            </div>
                        </div>

                        <div class="rounded-none border border-gray-200 bg-white p-6 shadow-sm">
                            <div class="bg-blue-100 border-l-4 border-blue-500 p-4 mb-6">
                                <h2 class="text-xl font-bold text-black">Patient Information</h2>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6 text-sm">
                                <div>
                                    <div class="text-xs font-semibold uppercase text-gray-400">Last Name</div>
                                    <div class="font-semibold text-gray-900">{{ $selectedPatient->last_name ?? 'N/A' }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-xs font-semibold uppercase text-gray-400">First Name</div>
                                    <div class="font-semibold text-gray-900">
                                        {{ $selectedPatient->first_name ?? 'N/A' }}</div>
                                </div>
                                <div>
                                    <div class="text-xs font-semibold uppercase text-gray-400">Middle Name</div>
                                    <div class="font-semibold text-gray-900">
                                        {{ $selectedPatient->middle_name ?? 'N/A' }}</div>
                                </div>
                                <div>
                                    <div class="text-xs font-semibold uppercase text-gray-400">Nickname</div>
                                    <div class="font-semibold text-gray-900">{{ $selectedPatient->nickname ?? 'N/A' }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-xs font-semibold uppercase text-gray-400">Occupation</div>
                                    <div class="font-semibold text-gray-900">
                                        {{ $selectedPatient->occupation ?? 'N/A' }}</div>
                                </div>
                                <div>
                                    <div class="text-xs font-semibold uppercase text-gray-400">Date of Birth</div>
                                    <div class="font-semibold text-gray-900">
                                        {{ $selectedPatient->birth_date ? \Carbon\Carbon::parse($selectedPatient->birth_date)->format('M d, Y') : 'N/A' }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-xs font-semibold uppercase text-gray-400">Age</div>
                                    <div class="font-semibold text-gray-900">{{ $patientAge ?? 'N/A' }}</div>
                                </div>
                                <div>
                                    <div class="text-xs font-semibold uppercase text-gray-400">Sex</div>
                                    <div class="font-semibold text-gray-900">{{ $selectedPatient->gender ?? 'N/A' }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-xs font-semibold uppercase text-gray-400">Civil Status</div>
                                    <div class="font-semibold text-gray-900">
                                        {{ $selectedPatient->civil_status ?? 'N/A' }}</div>
                                </div>
                                <div class="md:col-span-2">
                                    <div class="text-xs font-semibold uppercase text-gray-400">Home Address</div>
                                    <div class="font-semibold text-gray-900">
                                        {{ $selectedPatient->home_address ?? 'N/A' }}</div>
                                </div>
                                <div>
                                    <div class="text-xs font-semibold uppercase text-gray-400">Home Phone Number</div>
                                    <div class="font-semibold text-gray-900">
                                        {{ $selectedPatient->home_number ?? 'N/A' }}</div>
                                </div>
                                <div class="md:col-span-2">
                                    <div class="text-xs font-semibold uppercase text-gray-400">Office Address</div>
                                    <div class="font-semibold text-gray-900">
                                        {{ $selectedPatient->office_address ?? 'N/A' }}</div>
                                </div>
                                <div>
                                    <div class="text-xs font-semibold uppercase text-gray-400">Office Phone Number
                                    </div>
                                    <div class="font-semibold text-gray-900">
                                        {{ $selectedPatient->office_number ?? 'N/A' }}</div>
                                </div>
                                <div>
                                    <div class="text-xs font-semibold uppercase text-gray-400">Mobile Number</div>
                                    <div class="font-semibold text-gray-900">
                                        {{ $selectedPatient->mobile_number ?? 'N/A' }}</div>
                                </div>
                                <div>
                                    <div class="text-xs font-semibold uppercase text-gray-400">E-mail Address</div>
                                    <div class="font-semibold text-gray-900">
                                        {{ $selectedPatient->email_address ?? 'N/A' }}</div>
                                </div>
                                <div class="md:col-span-3">
                                    <div class="text-xs font-semibold uppercase text-gray-400">Referral</div>
                                    <div class="font-semibold text-gray-900">{{ $selectedPatient->referral ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>

                            <div class="bg-blue-100 border-l-4 border-blue-500 p-4 mb-6">
                                <h2 class="text-xl font-bold text-black">Person to Contact in Case of Emergency</h2>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6 text-sm">
                                <div>
                                    <div class="text-xs font-semibold uppercase text-gray-400">Name</div>
                                    <div class="font-semibold text-gray-900">
                                        {{ $selectedPatient->emergency_contact_name ?? 'N/A' }}</div>
                                </div>
                                <div>
                                    <div class="text-xs font-semibold uppercase text-gray-400">Contact Number</div>
                                    <div class="font-semibold text-gray-900">
                                        {{ $selectedPatient->emergency_contact_number ?? 'N/A' }}</div>
                                </div>
                                <div>
                                    <div class="text-xs font-semibold uppercase text-gray-400">Relationship to Patient
                                    </div>
                                    <div class="font-semibold text-gray-900">
                                        {{ $selectedPatient->relationship ?? 'N/A' }}</div>
                                </div>
                            </div>

                            @if ($patientAge !== null && $patientAge < 18)
                                <div class="bg-blue-100 border-l-4 border-blue-500 p-4 mb-6">
                                    <h2 class="text-xl font-bold text-black">For Patient's Below 18 Years Old</h2>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                                    <div>
                                        <div class="text-xs font-semibold uppercase text-gray-400">Who is Answering
                                        </div>
                                        <div class="font-semibold text-gray-900">
                                            {{ $selectedPatient->who_answering ?? 'N/A' }}</div>
                                    </div>
                                    <div>
                                        <div class="text-xs font-semibold uppercase text-gray-400">Relationship to
                                            Patient</div>
                                        <div class="font-semibold text-gray-900">
                                            {{ $selectedPatient->relationship_to_patient ?? 'N/A' }}</div>
                                    </div>
                                    <div>
                                        <div class="text-xs font-semibold uppercase text-gray-400">Father's Name</div>
                                        <div class="font-semibold text-gray-900">
                                            {{ $selectedPatient->father_name ?? 'N/A' }}</div>
                                    </div>
                                    <div>
                                        <div class="text-xs font-semibold uppercase text-gray-400">Father's Contact
                                            Number</div>
                                        <div class="font-semibold text-gray-900">
                                            {{ $selectedPatient->father_number ?? 'N/A' }}</div>
                                    </div>
                                    <div>
                                        <div class="text-xs font-semibold uppercase text-gray-400">Mother's Name</div>
                                        <div class="font-semibold text-gray-900">
                                            {{ $selectedPatient->mother_name ?? 'N/A' }}</div>
                                    </div>
                                    <div>
                                        <div class="text-xs font-semibold uppercase text-gray-400">Mother's Contact
                                            Number</div>
                                        <div class="font-semibold text-gray-900">
                                            {{ $selectedPatient->mother_number ?? 'N/A' }}</div>
                                    </div>
                                    <div>
                                        <div class="text-xs font-semibold uppercase text-gray-400">Guardian's Name
                                        </div>
                                        <div class="font-semibold text-gray-900">
                                            {{ $selectedPatient->guardian_name ?? 'N/A' }}</div>
                                    </div>
                                    <div>
                                        <div class="text-xs font-semibold uppercase text-gray-400">Guardian's Contact
                                            Number</div>
                                        <div class="font-semibold text-gray-900">
                                            {{ $selectedPatient->guardian_number ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="lg:col-span-2 space-y-6">
                        <div class="rounded-none border border-gray-200 bg-white p-6 shadow-sm">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Appointments</h3>
                                    <p class="text-xs text-gray-500">Manage upcoming and past visits.</p>
                                </div>
                                <a href="{{ route('appointment', ['patient_id' => $selectedPatient->id]) }}"
                                    class="rounded-none bg-[#0086da] px-4 py-2 text-xs font-semibold text-white hover:bg-[#0073a8]">
                                    Add Appointment
                                </a>
                            </div>

                            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="rounded-none border border-gray-200 bg-gray-50 p-4">
                                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Last Visit
                                    </div>
                                    <div class="mt-2 text-lg font-bold text-gray-900">{{ $lastVisitLabel }}</div>
                                </div>
                                <div class="rounded-none border border-gray-200 bg-gray-50 p-4">
                                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Latest
                                        Status</div>
                                    <div class="mt-2 text-lg font-bold text-gray-900">
                                        {{ $selectedPatient->latest_status ?? 'N/A' }}</div>
                                </div>
                            </div>

                            <div class="mt-5 flex flex-wrap gap-3">
                                <button type="button"
                                    wire:click="$dispatch('open-history-modal', { patientId: {{ $selectedPatient->id }} })"
                                    class="rounded-none border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                                    Appointment History
                                </button>
                                <button type="button"
                                    wire:click="$dispatch('editPatient', { id: {{ $selectedPatient->id }}, startStep: 1 })"
                                    class="rounded-none border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                                    View Full Record
                                </button>
                                <button type="button"
                                    wire:click="$dispatch('editPatient', { id: {{ $selectedPatient->id }}, startStep: 3 })"
                                    class="rounded-none border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                                    Dental Chart
                                </button>
                            </div>
                        </div>

                        <div class="rounded-none border border-gray-200 bg-white p-6 shadow-sm">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Treatment Records</h3>
                                    <p class="text-xs text-gray-500">Latest treatment history and notes.</p>
                                </div>
                            </div>

                            @if (!empty($treatmentRecords) && count($treatmentRecords) > 0)
                                <div class="mt-5 divide-y divide-gray-100">
                                    @foreach ($treatmentRecords as $record)
                                        <div class="py-4">
                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                <div class="text-sm font-semibold text-gray-900">
                                                    {{ $record->treatment ?? 'Treatment' }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $record->created_at ? \Carbon\Carbon::parse($record->created_at)->format('M d, Y') : 'N/A' }}
                                                </div>
                                            </div>
                                            <div
                                                class="mt-2 grid grid-cols-1 gap-2 text-xs text-gray-600 sm:grid-cols-3">
                                                <div>
                                                    <span class="font-semibold text-gray-500">DMD:</span>
                                                    <span class="text-gray-900">{{ $record->dmd ?? 'N/A' }}</span>
                                                </div>
                                                <div>
                                                    <span class="font-semibold text-gray-500">Cost:</span>
                                                    <span class="text-gray-900">
                                                        {{ isset($record->cost_of_treatment) ? number_format($record->cost_of_treatment, 2) : 'N/A' }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="font-semibold text-gray-500">Amount Charged:</span>
                                                    <span class="text-gray-900">
                                                        {{ isset($record->amount_charged) ? number_format($record->amount_charged, 2) : 'N/A' }}
                                                    </span>
                                                </div>
                                            </div>
                                            @if (!empty($record->remarks))
                                                <div
                                                    class="mt-3 rounded-none bg-gray-50 px-3 py-2 text-xs text-gray-700">
                                                    Notes:
                                                    {{ $record->remarks }}
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div
                                    class="mt-6 rounded-none border border-dashed border-gray-200 bg-gray-50 p-6 text-center text-sm text-gray-500">
                                    No treatment records found for this patient.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Patient List (Full Width) -->
            <div class="flex flex-col overflow-hidden">
                <!-- List Container -->
                @if ($viewMode === 'table')
                    <div class="flex-1 overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-rounded-full scrollbar-track-[#f3f4f6] scrollbar-thumb-[#cbd5e1]">
                        <div class="overflow-hidden rounded-none border border-gray-200 bg-white shadow-sm">
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left text-gray-600">
                                    <thead class="border-b border-gray-200 bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        <tr>
                                            <th class="px-5 py-4">Patient</th>
                                            <th class="px-5 py-4">Contact</th>
                                            <th class="px-5 py-4">Email</th>
                                            <th class="px-5 py-4">Address</th>
                                            <th class="px-5 py-4">Age</th>
                                            <th class="px-5 py-4 text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @forelse($patients as $patient)
                                            <tr
                                                wire:click="selectPatient({{ $patient->id }})"
                                                class="cursor-pointer transition hover:bg-gray-50 @if ($patient->id == $selectedPatient?->id) bg-gray-100 @endif">
                                                <td class="px-5 py-4">
                                                    <div class="flex items-center gap-3">
                                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-xs font-bold text-gray-700">
                                                            {{ strtoupper(substr($patient->first_name ?? 'P', 0, 1) . substr($patient->last_name ?? '', 0, 1)) }}
                                                        </div>
                                                        <div>
                                                            <div class="font-semibold text-gray-900">
                                                                {{ $patient->last_name }}, {{ $patient->first_name }}
                                                            </div>
                                                            <div class="text-[11px] font-medium uppercase text-gray-400">
                                                                #{{ sprintf('PT%04d', $patient->id) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-5 py-4 text-sm">
                                                    {{ $patient->mobile_number ?? 'N/A' }}
                                                </td>
                                                <td class="px-5 py-4 text-xs text-gray-500">
                                                    {{ $patient->email_address ?? 'N/A' }}
                                                    @if (!$isPatientUser && !empty($patient->pending_recovery_request_id))
                                                        <div class="mt-1">
                                                            <span
                                                                class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-amber-800">
                                                                Pending Recovery
                                                            </span>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="max-w-[240px] truncate px-5 py-4 text-xs text-gray-500"
                                                    title="{{ $patient->home_address ?? 'N/A' }}">
                                                    {{ $patient->home_address ?? 'N/A' }}
                                                </td>
                                                <td class="px-5 py-4 text-sm font-semibold text-gray-700">{{ $patient->age ?? 'N/A' }}</td>
                                                <td class="px-5 py-4 text-right">
                                                    <div class="relative inline-block text-left"
                                                        x-data="{ open: false }"
                                                        @close-patient-menus.window="open = false">
                                                        <button type="button"
                                                            class="rounded-full p-2 hover:bg-gray-100"
                                                            @click.stop="$dispatch('close-patient-menus'); open = !open">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                viewBox="0 0 24 24" width="20" height="20"
                                                                fill="none" stroke="currentColor"
                                                                stroke-width="2">
                                                                <circle cx="12" cy="5" r="1.5" />
                                                                <circle cx="12" cy="12" r="1.5" />
                                                                <circle cx="12" cy="19" r="1.5" />
                                                            </svg>
                                                        </button>
                                                        <div x-show="open" @click.away="open = false"
                                                            class="absolute right-0 z-20 mt-2 w-44 rounded-none border border-gray-200 bg-white shadow-lg"
                                                            style="display: none;">
                                                            <button type="button"
                                                                class="w-full px-4 py-2 text-left text-sm hover:bg-gray-50"
                                                                wire:click="openProfile({{ $patient->id }})"
                                                                onclick="event.stopPropagation();">
                                                                View Profile
                                                            </button>
                                                            <button type="button"
                                                                class="w-full px-4 py-2 text-left text-sm hover:bg-gray-50"
                                                                wire:click="$dispatch('editPatient', { id: {{ $patient->id }}, startStep: 1 })"
                                                                onclick="event.stopPropagation();">
                                                                View Full Record
                                                            </button>
                                                            @if (!$isPatientUser)
                                                                <button type="button"
                                                                    class="w-full px-4 py-2 text-left text-sm text-red-700 hover:bg-red-50"
                                                                    onclick="event.stopPropagation(); if (confirm('Delete this patient? This cannot be undone.')) { @this.deletePatient({{ $patient->id }}) }">
                                                                    Delete
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="p-6 text-center text-gray-500">
                                                    No patients found for "{{ $search }}".
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="grid flex-1 grid-cols-1 gap-4 overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-rounded-full scrollbar-track-[#f3f4f6] scrollbar-thumb-[#cbd5e1] md:grid-cols-2 xl:grid-cols-3">
                        @forelse($patients as $patient)
                            @php
                                $patientName = trim(($patient->first_name ?? '') . ' ' . ($patient->last_name ?? ''));
                                $patientCode = sprintf('PT%04d', $patient->id);
                                $patientInitials = strtoupper(
                                    substr($patient->first_name ?? 'P', 0, 1) . substr($patient->last_name ?? '', 0, 1),
                                );
                                $lastVisit = $patient->last_completed_at
                                    ? \Carbon\Carbon::parse($patient->last_completed_at)->format('d M Y')
                                    : 'No visits';
                                $genderLabel = $patient->gender ?? 'N/A';
                                $locationLabel = $patient->home_address
                                    ? \Illuminate\Support\Str::limit($patient->home_address, 20)
                                    : 'N/A';
                            @endphp

                            <div wire:click="selectPatient({{ $patient->id }})"
                                class="group rounded-none border bg-white p-5 shadow-sm transition @if ($patient->id == $selectedPatient?->id) border-[#0789da] ring-1 ring-[#0789da] @else border-gray-200 hover:shadow-md @endif">
                                <div class="flex items-start justify-between">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide
                                    {{ $patient->patient_type === 'Active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $patient->patient_type ?? 'Inactive' }}
                                    </span>
                                    @if (!$isPatientUser)
                                        <button type="button"
                                            class="rounded-full p-2 transition-colors hover:bg-red-50"
                                            title="Delete Patient"
                                            onclick="event.stopPropagation(); if (confirm('Delete this patient? This cannot be undone.')) { @this.deletePatient({{ $patient->id }}) }">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                width="20" height="20" color="#f56e6e" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                                <path
                                                    d="M19.5 5.5L18.8803 15.5251C18.7219 18.0864 18.6428 19.3671 18.0008 20.2879C17.6833 20.7431 17.2747 21.1273 16.8007 21.416C15.8421 22 14.559 22 11.9927 22C9.42312 22 8.1383 22 7.17905 21.4149C6.7048 21.1257 6.296 20.7408 5.97868 20.2848C5.33688 19.3626 5.25945 18.0801 5.10461 15.5152L4.5 5.5" />
                                                <path
                                                    d="M3 5.5H21M16.0557 5.5L15.3731 4.09173C14.9196 3.15626 14.6928 2.68852 14.3017 2.39681C14.215 2.3321 14.1231 2.27454 14.027 2.2247C13.5939 2 13.0741 2 12.0345 2C10.9688 2 10.436 2 9.99568 2.23412C9.8981 2.28601 9.80498 2.3459 9.71729 2.41317C9.32164 2.7167 9.10063 3.20155 8.65861 4.17126L8.05292 5.5" />
                                                <path d="M9.5 16.5L9.5 10.5" />
                                                <path d="M14.5 16.5L14.5 10.5" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>

                                <div class="mt-4 flex flex-col items-center text-center">
                                    <div
                                        class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-700 text-lg font-bold">
                                        {{ $patientInitials }}
                                    </div>
                                    <div class="mt-3 text-xs font-semibold text-gray-500">#{{ $patientCode }}</div>
                                    <div class="text-lg font-bold text-gray-900">
                                        {{ $patientName ?: 'Unnamed patient' }}
                                    </div>
                                </div>

                                <div class="mt-4 grid grid-cols-3 divide-x divide-gray-100 rounded-none border border-gray-200 bg-gray-50">
                                    <div class="px-3 py-2 text-center">
                                        <div class="text-[10px] font-semibold uppercase text-gray-400">Last Visit</div>
                                        <div class="text-xs font-semibold text-gray-700">{{ $lastVisit }}</div>
                                    </div>
                                    <div class="px-3 py-2 text-center">
                                        <div class="text-[10px] font-semibold uppercase text-gray-400">Gender</div>
                                        <div class="text-xs font-semibold text-gray-700">{{ $genderLabel }}</div>
                                    </div>
                                    <div class="px-3 py-2 text-center">
                                        <div class="text-[10px] font-semibold uppercase text-gray-400">Location</div>
                                        <div class="text-xs font-semibold text-gray-700"
                                            title="{{ $patient->home_address ?? 'N/A' }}">{{ $locationLabel }}</div>
                                    </div>
                                </div>

                                <div class="mt-4 flex items-center gap-2">
                                    <a href="{{ route('appointment', ['patient_id' => $patient->id]) }}"
                                        class="flex-1 rounded-none bg-[#0086da] px-3 py-2 text-center text-xs font-semibold text-white hover:bg-[#0073a8]">
                                        Add Appointment
                                    </a>
                                    <button type="button" wire:click="openProfile({{ $patient->id }})"
                                        class="flex-1 rounded-none border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                                        View Record
                                    </button>
                                </div>
                            </div>

                        @empty
                            <div class="rounded-none border border-dashed border-gray-300 bg-gray-50 p-6 text-center text-gray-500">
                                No patients found for "{{ $search }}".
                            </div>
                        @endforelse
                    </div>
                @endif
                <!-- Pagination links -->
                <div class="mt-4">
                    {{ $patients->links() }}
                </div>
            </div>

        </div>

    @endif

    @include('components.flash-toast')

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



