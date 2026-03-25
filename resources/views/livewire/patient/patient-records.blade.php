@php
    $isPatientUser = auth()->check() && auth()->user()->role === 3;
@endphp
<div class="h-full flex flex-col" style="font-family:'Montserrat',sans-serif;">

    {{-- ── TOOLBAR ── --}}
    <div class="flex flex-col gap-4 mb-6">
        @if (!$showProfile)
            <div class="border border-[#e4eff8] bg-white">
                <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div>
                            <div class="text-[.95rem] font-extrabold text-[#1a2e3b] tracking-[-0.01em]">Patient Records</div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-2">
                        @if (!$isPatientUser)
                            <button wire:click="$dispatch('openAddPatientModal')" type="button"
                                class="inline-flex items-center justify-center gap-2 bg-[#0086da] px-4 py-[9px] text-[.68rem] font-bold uppercase tracking-[.1em] text-white transition hover:bg-[#006ab0]">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-plus-icon lucide-user-plus"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
                                Add Patient
                            </button>

                            <div class="relative w-full min-w-[14rem] bg-white md:w-[20rem]">
                                <input type="text" placeholder="Search patients..."
                                    class="w-full border border-[#d4e8f5] bg-white py-[9px] pl-9 pr-4 text-[.8rem] text-[#1a2e3b] placeholder-[#9bbdd0] focus:border-[#0086da] focus:outline-none focus:ring-2 focus:ring-[#cce9f8]"
                                    wire:model.live.debounce.300ms="search">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#9bbdd0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search absolute left-3 top-1/2 -translate-y-1/2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            </div>

                            <div class="relative" id="sortDropdown">
                                <button onclick="document.getElementById('sortMenu').classList.toggle('hidden')"
                                    class="inline-flex items-center gap-2 border border-[#d4e8f5] bg-white px-4 py-[9px] text-[.72rem] font-bold uppercase tracking-[.08em] text-[#3d5a6e] transition hover:border-[#0086da] hover:text-[#0086da]">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-down"><path d="m21 16-4 4-4-4"/><path d="M17 20V4"/><path d="m3 8 4-4 4 4"/><path d="M7 4v16"/></svg>
                                    @switch($sortOption)
                                        @case('oldest') Oldest @break
                                        @case('a_z') A–Z @break
                                        @case('z_a') Z–A @break
                                        @default Recent @break
                                    @endswitch
                                </button>
                                <div id="sortMenu"
                                    class="absolute right-0 z-50 mt-1 hidden w-48 border border-[#e4eff8] bg-white shadow-[0_8px_24px_rgba(0,134,218,.1)]">
                                    @foreach([['recent','Recent (Newest)'],['oldest','Oldest First'],['a_z','Name (A–Z)'],['z_a','Name (Z–A)']] as [$val,$label])
                                        <button wire:click.stop="setSort('{{ $val }}')"
                                            class="block w-full px-4 py-2.5 text-left text-[.78rem] font-semibold text-[#3d5a6e] transition hover:bg-[#f0f8fe] hover:text-[#0086da] {{ $sortOption === $val ? 'bg-[#f0f8fe] text-[#0086da]' : '' }}">
                                            {{ $label }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center border border-[#d4e8f5] bg-white p-0.5">
                            <button type="button" wire:click="setViewMode('table')"
                                class="p-2 transition {{ $viewMode === 'table' ? 'bg-[#0086da] text-white' : 'text-[#7a9db5] hover:bg-[#f0f8fe]' }}"
                                title="Table view">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-table-icon lucide-table"><path d="M12 3v18"/><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M3 15h18"/></svg>
                            </button>
                            <button type="button" wire:click="setViewMode('cards')"
                                class="p-2 transition {{ $viewMode === 'cards' ? 'bg-[#0086da] text-white' : 'text-[#7a9db5] hover:bg-[#f0f8fe]' }}"
                                title="Card view">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-grid-3x3"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M3 15h18"/><path d="M9 3v18"/><path d="M15 3v18"/></svg>
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

        {{-- ── Montserrat font ── --}}
        <style>
            .profile-wrap * { font-family: 'Montserrat', sans-serif; }
        </style>

        <div class="profile-wrap flex flex-1 flex-col overflow-hidden"
             style="font-family:'Montserrat',sans-serif;"
             x-data="{ lightboxOpen: false, lightboxSrc: '', lightboxLabel: '' }">

            {{-- ══ HERO HEADER (matches book-appointment style) ══ --}}
            <div class="border border-[#e4eff8] bg-white mb-6">
                <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-5 md:px-8">
                    <div>
                        <button type="button" wire:click="backToList"
                            class="mb-3 inline-flex items-center gap-[7px] text-[.68rem] font-bold uppercase tracking-[.12em] text-[#7a9db5] transition hover:text-[#0086da]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left-icon lucide-arrow-left"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                            Back to Patient Records
                        </button>
                        <div class="flex items-center gap-3 flex-wrap">
                            <div class="w-10 h-10 bg-[#0086da] flex items-center justify-center flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-icon lucide-user"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-[.6rem] font-bold uppercase tracking-[.2em] text-[#0086da]">#{{ $patientCode }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 text-[.6rem] font-bold uppercase tracking-[.14em]
                                        {{ $patientType === 'Active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-[#7a9db5]' }}">
                                        {{ $patientType }}
                                    </span>
                                </div>
                                <h2 class="text-[1.15rem] font-extrabold tracking-[-.02em] text-[#1a2e3b] leading-tight">
                                    {{ $patientFullName ?: 'Unnamed Patient' }}
                                </h2>
                                <p class="text-[.76rem] text-[#7a9db5]">Last visit: {{ $lastVisitLabel }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="button"
                            wire:click="$dispatch('editPatient', { id: {{ $selectedPatient->id }}, startStep: 1 })"
                            class="inline-flex items-center gap-2 bg-[#0086da] px-5 py-[10px] text-[.7rem] font-bold uppercase tracking-[.1em] text-white transition hover:bg-[#006ab0]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-folder-open-icon lucide-folder-open"><path d="m6 14 1.5-2.9A2 2 0 0 1 9.24 10H20a2 2 0 0 1 1.94 2.5l-1.54 6a2 2 0 0 1-1.95 1.5H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3.9a2 2 0 0 1 1.69.9l.81 1.2a2 2 0 0 0 1.67.9H18a2 2 0 0 1 2 2v2"/></svg>
                            View Full Records
                        </button>
                        @if (!$isPatientUser)
                            <a href="{{ route('appointment', ['patient_id' => $selectedPatient->id]) }}"
                                class="inline-flex items-center gap-2 border border-[#0086da] bg-white px-5 py-[10px] text-[.7rem] font-bold uppercase tracking-[.1em] text-[#0086da] transition hover:bg-[#f0f8fe]">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-icon lucide-calendar"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
                                Add Appointment
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ══ MAIN CONTENT ══ --}}
            <div class="flex-1 overflow-y-auto pr-1 scrollbar-thin scrollbar-thumb-rounded-full scrollbar-track-[#ccebff] scrollbar-thumb-[#0086da]">
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                    {{-- ── LEFT: Patient Info ── --}}
                    <div class="space-y-6">

                        {{-- Patient Information card --}}
                        <div class="bg-white border border-[#e4eff8] shadow-[0_4px_20px_rgba(0,134,218,.06)]">
                            {{-- Card header --}}
                            <div class="flex items-center gap-3 px-5 py-4 border-b border-[#e4eff8]">
                                <div class="w-7 h-7 bg-[#e8f4fc] flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0086da" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-icon lucide-user"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                </div>
                                <div>
                                    <div class="text-[.55rem] font-bold uppercase tracking-[.2em] text-[#0086da]">Personal</div>
                                    <div class="text-[.85rem] font-extrabold text-[#1a2e3b] tracking-[-0.01em]">Patient Information</div>
                                </div>
                            </div>

                            <div class="px-5 py-4">
                                @php
                                    $infoFields = [
                                        ['label' => 'Last Name',     'value' => $selectedPatient->last_name],
                                        ['label' => 'First Name',    'value' => $selectedPatient->first_name],
                                        ['label' => 'Middle Name',   'value' => $selectedPatient->middle_name],
                                        ['label' => 'Nickname',      'value' => $selectedPatient->nickname],
                                        ['label' => 'Date of Birth', 'value' => $selectedPatient->birth_date ? \Carbon\Carbon::parse($selectedPatient->birth_date)->format('M d, Y') : null],
                                        ['label' => 'Age',           'value' => $patientAge],
                                        ['label' => 'Sex',           'value' => $selectedPatient->gender],
                                        ['label' => 'Civil Status',  'value' => $selectedPatient->civil_status],
                                        ['label' => 'Occupation',    'value' => $selectedPatient->occupation],
                                        ['label' => 'Mobile',        'value' => $selectedPatient->mobile_number],
                                        ['label' => 'Home Phone',    'value' => $selectedPatient->home_number],
                                        ['label' => 'Office Phone',  'value' => $selectedPatient->office_number],
                                        ['label' => 'Email',         'value' => $selectedPatient->email_address],
                                    ];
                                @endphp
                                <div class="grid grid-cols-2 gap-x-4 gap-y-3">
                                @foreach ($infoFields as $field)
                                    <div class="min-w-0">
                                        <div class="text-[.53rem] font-bold uppercase tracking-[.16em] text-[#7a9db5] truncate">{{ $field['label'] }}</div>
                                        <div class="mt-0.5 text-[.78rem] font-semibold text-[#1a2e3b] truncate">{{ $field['value'] ?? '—' }}</div>
                                    </div>
                                @endforeach
                                </div>

                                @if ($selectedPatient->home_address || $selectedPatient->office_address || $selectedPatient->referral)
                                    <div class="pt-3 border-t border-[#e4eff8]"></div>
                                @endif
                                @if ($selectedPatient->home_address)
                                    <div>
                                        <div class="text-[.58rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Home Address</div>
                                        <div class="mt-0.5 text-[.85rem] font-semibold text-[#1a2e3b]">{{ $selectedPatient->home_address }}</div>
                                    </div>
                                @endif
                                @if ($selectedPatient->office_address)
                                    <div>
                                        <div class="text-[.58rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Office Address</div>
                                        <div class="mt-0.5 text-[.85rem] font-semibold text-[#1a2e3b]">{{ $selectedPatient->office_address }}</div>
                                    </div>
                                @endif
                                @if ($selectedPatient->referral)
                                    <div>
                                        <div class="text-[.58rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Referral</div>
                                        <div class="mt-0.5 text-[.85rem] font-semibold text-[#1a2e3b]">{{ $selectedPatient->referral }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Emergency Contact card --}}
                        <div class="bg-white border border-[#e4eff8] shadow-[0_4px_20px_rgba(0,134,218,.06)]">
                            <div class="flex items-center gap-3 px-5 py-4 border-b border-[#e4eff8]">
                                <div class="w-7 h-7 bg-[#e8f4fc] flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0086da" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-call-icon lucide-phone-call"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/><path d="M14.05 2a9 9 0 0 1 8 7.94"/><path d="M14.05 6A5 5 0 0 1 18 10"/></svg>
                                </div>
                                <div>
                                    <div class="text-[.55rem] font-bold uppercase tracking-[.2em] text-[#0086da]">Emergency</div>
                                    <div class="text-[.85rem] font-extrabold text-[#1a2e3b] tracking-[-0.01em]">Emergency Contact</div>
                                </div>
                            </div>
                            <div class="px-5 py-4 grid grid-cols-2 gap-x-4 gap-y-3">
                                <div class="col-span-2">
                                    <div class="text-[.53rem] font-bold uppercase tracking-[.16em] text-[#7a9db5]">Name</div>
                                    <div class="mt-0.5 text-[.78rem] font-semibold text-[#1a2e3b]">{{ $selectedPatient->emergency_contact_name ?? '—' }}</div>
                                </div>
                                <div>
                                    <div class="text-[.53rem] font-bold uppercase tracking-[.16em] text-[#7a9db5]">Contact No.</div>
                                    <div class="mt-0.5 text-[.78rem] font-semibold text-[#1a2e3b]">{{ $selectedPatient->emergency_contact_number ?? '—' }}</div>
                                </div>
                                <div>
                                    <div class="text-[.53rem] font-bold uppercase tracking-[.16em] text-[#7a9db5]">Relationship</div>
                                    <div class="mt-0.5 text-[.78rem] font-semibold text-[#1a2e3b]">{{ $selectedPatient->relationship ?? '—' }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Under-18 card --}}
                        @if ($patientAge !== null && $patientAge < 18)
                            <div class="bg-white border border-[#e4eff8] shadow-[0_4px_20px_rgba(0,134,218,.06)]">
                                <div class="flex items-center gap-3 px-6 py-5 border-b border-[#e4eff8]">
                                    <div class="w-7 h-7 bg-[#e8f4fc] flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3.5 h-3.5 text-[#0086da]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="square" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                                            <circle cx="9" cy="7" r="4"/>
                                            <path stroke-linecap="square" d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-[.58rem] font-bold uppercase tracking-[.2em] text-[#7a9db5]">Guardian Info</div>
                                        <div class="text-[.88rem] font-extrabold text-[#1a2e3b] tracking-[-0.01em]">Patient Below 18</div>
                                    </div>
                                </div>
                                <div class="px-6 py-5 space-y-4">
                                    @foreach ([
                                        ['Who is Answering', $selectedPatient->who_answering],
                                        ['Relationship', $selectedPatient->relationship_to_patient],
                                        ["Father's Name", $selectedPatient->father_name],
                                        ["Father's Contact", $selectedPatient->father_number],
                                        ["Mother's Name", $selectedPatient->mother_name],
                                        ["Mother's Contact", $selectedPatient->mother_number],
                                        ["Guardian's Name", $selectedPatient->guardian_name],
                                        ["Guardian's Contact", $selectedPatient->guardian_number],
                                    ] as [$lbl, $val])
                                        <div>
                                            <div class="text-[.58rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">{{ $lbl }}</div>
                                            <div class="mt-0.5 text-[.85rem] font-semibold text-[#1a2e3b]">{{ $val ?? '—' }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- ── RIGHT: Appointments + Treatment Records ── --}}
                    <div class="lg:col-span-2 space-y-6">

                        {{-- Appointments card --}}
                        <div class="bg-white border border-[#e4eff8] shadow-[0_4px_20px_rgba(0,134,218,.06)]">
                            <div class="flex items-center justify-between gap-4 px-6 py-5 border-b border-[#e4eff8] flex-wrap">
                                <div class="flex items-center gap-3">
                                     <div class="w-7 h-7 bg-[#e8f4fc] flex items-center justify-center flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0086da" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-icon lucide-calendar"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
                                    </div>
                                    <div>
                                        <div class="text-[.58rem] font-bold uppercase tracking-[.2em] text-[#7a9db5]">Overview</div>
                                        <div class="text-[.88rem] font-extrabold text-[#1a2e3b] tracking-[-0.01em]">Appointments</div>
                                    </div>
                                </div>
                                @if (!$isPatientUser)
                                    <button type="button"
                                        wire:click="$dispatch('open-history-modal', { patientId: {{ $selectedPatient->id }} })"
                                        class="inline-flex items-center gap-2 border border-[#d4e8f5] bg-[#f8fbfe] px-4 py-[8px] text-[.68rem] font-bold uppercase tracking-[.1em] text-[#3d5a6e] transition hover:border-[#0086da] hover:text-[#0086da]">
                                        View Appointment History
                                    </button>
                                @endif
                            </div>

                            <div class="px-6 py-5">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                                    <div class="border border-[#e4eff8] bg-[#f8fbfe] p-4">
                                        <div class="text-[.58rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Last Visit</div>
                                        <div class="mt-2 text-[1rem] font-extrabold text-[#1a2e3b]">{{ $lastVisitLabel }}</div>
                                    </div>
                                    <div class="border border-[#e4eff8] bg-[#f8fbfe] p-4">
                                        <div class="text-[.58rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Latest Status</div>
                                        <div class="mt-2 text-[1rem] font-extrabold text-[#1a2e3b]">{{ $selectedPatient->latest_status ?? '—' }}</div>
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <button type="button"
                                        wire:click="$dispatch('editPatient', { id: {{ $selectedPatient->id }}, startStep: 1 })"
                                        class="inline-flex items-center gap-2 bg-[#0086da] px-5 py-[10px] text-[.68rem] font-bold uppercase tracking-[.1em] text-white transition hover:bg-[#006ab0]">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-folder-open-icon lucide-folder-open"><path d="m6 14 1.5-2.9A2 2 0 0 1 9.24 10H20a2 2 0 0 1 1.94 2.5l-1.54 6a2 2 0 0 1-1.95 1.5H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3.9a2 2 0 0 1 1.69.9l.81 1.2a2 2 0 0 0 1.67.9H18a2 2 0 0 1 2 2v2"/></svg>
                                        View Full Records
                                    </button>
                                    @if (!$isPatientUser)
                                        <button type="button"
                                            wire:click="$dispatch('editPatient', { id: {{ $selectedPatient->id }}, startStep: 2 })"
                                            class="inline-flex items-center gap-2 border border-[#d4e8f5] bg-white px-4 py-[10px] text-[.68rem] font-bold uppercase tracking-[.1em] text-[#3d5a6e] transition hover:border-[#0086da] hover:text-[#0086da]">
                                            Add Health History
                                        </button>
                                        <button type="button"
                                            wire:click="$dispatch('editPatient', { id: {{ $selectedPatient->id }}, startStep: 3 })"
                                            class="inline-flex items-center gap-2 border border-[#d4e8f5] bg-white px-4 py-[10px] text-[.68rem] font-bold uppercase tracking-[.1em] text-[#3d5a6e] transition hover:border-[#0086da] hover:text-[#0086da]">
                                            Dental Chart
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Treatment Records card --}}
                        <div class="bg-white border border-[#e4eff8] shadow-[0_4px_20px_rgba(0,134,218,.06)]">
                            <div class="flex items-center gap-3 px-6 py-5 border-b border-[#e4eff8]">
                                <div class="w-7 h-7 bg-[#e8f4fc] flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0086da" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clipboard-check-icon lucide-clipboard-check"><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="m9 14 2 2 4-4"/></svg>
                                </div>
                                <div>
                                    <div class="text-[.58rem] font-bold uppercase tracking-[.2em] text-[#7a9db5]">History</div>
                                    <div class="text-[.88rem] font-extrabold text-[#1a2e3b] tracking-[-0.01em]">Treatment Records</div>
                                </div>
                            </div>

                            <div class="px-6 py-5">
                                @if (!empty($treatmentRecords) && count($treatmentRecords) > 0)
                                    <div class="space-y-6">
                                        @foreach ($treatmentRecords as $record)
                                            @php
                                                $beforeImgs = collect($record->image_list ?? [])->filter(fn($i) => ($i['image_type'] ?? '') === 'before')->values();
                                                $afterImgs  = collect($record->image_list ?? [])->filter(fn($i) => ($i['image_type'] ?? '') === 'after')->values();
                                            @endphp
                                            <div class="border border-[#e4eff8] bg-[#f8fbfe]">
                                                {{-- Record header row --}}
                                                <div class="flex flex-wrap items-center justify-between gap-2 px-4 py-3 border-b border-[#e4eff8]">
                                                    <div class="text-[.85rem] font-bold text-[#1a2e3b]">{{ $record->treatment ?? 'Treatment' }}</div>
                                                    <div class="text-[.68rem] font-semibold text-[#7a9db5]">
                                                        {{ $record->created_at ? \Carbon\Carbon::parse($record->created_at)->format('M d, Y') : 'N/A' }}
                                                    </div>
                                                </div>

                                                {{-- Record details --}}
                                                <div class="grid grid-cols-1 gap-3 px-4 py-3 sm:grid-cols-3">
                                                    <div>
                                                        <div class="text-[.56rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">DMD</div>
                                                        <div class="mt-0.5 text-[.82rem] font-semibold text-[#1a2e3b]">{{ $record->dmd ?? '—' }}</div>
                                                    </div>
                                                    <div>
                                                        <div class="text-[.56rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Cost</div>
                                                        <div class="mt-0.5 text-[.82rem] font-semibold text-[#1a2e3b]">
                                                            {{ isset($record->cost_of_treatment) ? '₱ ' . number_format($record->cost_of_treatment, 2) : '—' }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="text-[.56rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Payment</div>
                                                        <div class="mt-0.5 text-[.82rem] font-semibold text-[#1a2e3b]">
                                                            {{ isset($record->amount_charged) ? '₱ ' . number_format($record->amount_charged, 2) : '—' }}
                                                        </div>
                                                    </div>
                                                </div>

                                                @if (!empty($record->remarks))
                                                    <div class="px-4 pb-3">
                                                        <div class="border-l-2 border-[#0086da] bg-white px-3 py-2">
                                                            <div class="text-[.56rem] font-bold uppercase tracking-[.18em] text-[#7a9db5] mb-1">Remarks</div>
                                                            <div class="text-[.8rem] text-[#3d5a6e] leading-relaxed">{{ $record->remarks }}</div>
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- Before / After images --}}
                                                @if ($beforeImgs->isNotEmpty() || $afterImgs->isNotEmpty())
                                                    <div class="border-t border-[#e4eff8] px-4 py-4 space-y-4">
                                                        @if ($beforeImgs->isNotEmpty())
                                                            <div>
                                                                <div class="mb-2 text-[.56rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Before Treatment</div>
                                                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                                                    @foreach ($beforeImgs as $img)
                                                                        <button type="button"
                                                                            @click="lightboxSrc = '{{ \Illuminate\Support\Facades\Storage::url($img['image_path']) }}'; lightboxLabel = 'Before Treatment'; lightboxOpen = true"
                                                                            class="group relative overflow-hidden border border-[#e4eff8] bg-white transition hover:border-[#0086da]">
                                                                            <img class="h-24 w-full object-cover transition group-hover:scale-105 duration-300"
                                                                                src="{{ \Illuminate\Support\Facades\Storage::url($img['image_path']) }}"
                                                                                alt="Before treatment photo">
                                                                            <div class="absolute inset-0 bg-[#0086da]/0 group-hover:bg-[#0086da]/10 transition duration-200 flex items-center justify-center">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0086da" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-maximize2-icon lucide-maximize-2 opacity-0 group-hover:opacity-100 transition"><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" x2="14" y1="3" y2="10"/><line x1="3" x2="10" y1="21" y2="14"/></svg>
                                                                            </div>
                                                                        </button>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if ($afterImgs->isNotEmpty())
                                                            <div>
                                                                <div class="mb-2 text-[.56rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">After Treatment</div>
                                                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                                                    @foreach ($afterImgs as $img)
                                                                        <button type="button"
                                                                            @click="lightboxSrc = '{{ \Illuminate\Support\Facades\Storage::url($img['image_path']) }}'; lightboxLabel = 'After Treatment'; lightboxOpen = true"
                                                                            class="group relative overflow-hidden border border-[#e4eff8] bg-white transition hover:border-[#0086da]">
                                                                            <img class="h-24 w-full object-cover transition group-hover:scale-105 duration-300"
                                                                                src="{{ \Illuminate\Support\Facades\Storage::url($img['image_path']) }}"
                                                                                alt="After treatment photo">
                                                                            <div class="absolute inset-0 bg-[#0086da]/0 group-hover:bg-[#0086da]/10 transition duration-200 flex items-center justify-center">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0086da" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-maximize2-icon lucide-maximize-2 opacity-0 group-hover:opacity-100 transition"><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" x2="14" y1="3" y2="10"/><line x1="3" x2="10" y1="21" y2="14"/></svg>
                                                                            </div>
                                                                        </button>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="border border-dashed border-[#d4e8f5] bg-[#f8fbfe] p-8 text-center">
                                        <div class="mx-auto mb-3 flex h-10 w-10 items-center justify-center bg-[#e8f4fc]">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0086da" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clipboard-check-icon lucide-clipboard-check"><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="m9 14 2 2 4-4"/></svg>
                                        </div>
                                        <p class="text-[.78rem] font-semibold text-[#7a9db5]">No treatment records yet.</p>
                                        <p class="text-[.72rem] text-[#9bbdd0] mt-1">Records will appear here once added.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- ══ IMAGE LIGHTBOX ══ --}}
            <div x-show="lightboxOpen" x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/75 p-4"
                @click.self="lightboxOpen = false"
                @keydown.escape.window="lightboxOpen = false">
                <div class="w-full max-w-4xl bg-white shadow-[0_32px_80px_rgba(0,0,0,.4)]">
                    <div class="flex items-center justify-between border-b border-[#e4eff8] px-5 py-3">
                        <div class="text-[.62rem] font-bold uppercase tracking-[.2em] text-[#0086da]" x-text="lightboxLabel"></div>
                        <button type="button" @click="lightboxOpen = false"
                            class="text-[.72rem] font-bold uppercase tracking-[.12em] text-[#7a9db5] hover:text-[#0086da] transition">
                            Close ✕
                        </button>
                    </div>
                    <div class="p-4">
                        <img class="max-h-[75vh] w-full object-contain" :src="lightboxSrc" alt="Treatment photo">
                    </div>
                </div>
            </div>

        </div>

    @else
        <div class="flex flex-1 flex-col" style="min-height:0;">
            <!-- Patient List (Full Width) -->
            <div class="flex flex-1 flex-col overflow-hidden">
                <!-- List Container -->
                @if ($viewMode === 'table')
                    <div class="flex-1 overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-rounded-full scrollbar-track-[#f3f4f6] scrollbar-thumb-[#cbd5e1]">
                        <div class="border border-[#e4eff8] bg-white">
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left" style="font-family:'Montserrat',sans-serif;">
                                    <thead class="border-b border-[#e4eff8] bg-[#f6fafd]">
                                        <tr>
                                            <th class="px-5 py-3.5 text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Patient</th>
                                            <th class="px-5 py-3.5 text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Contact</th>
                                            <th class="px-5 py-3.5 text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Email</th>
                                            <th class="px-5 py-3.5 text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Address</th>
                                            <th class="px-5 py-3.5 text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Age</th>
                                            <th class="px-5 py-3.5 text-right text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[#f0f6fb]">
                                        @forelse($patients as $patient)
                                            @php
                                                $rowAge = $patient->birth_date
                                                    ? \Carbon\Carbon::parse($patient->birth_date)->age
                                                    : null;
                                            @endphp
                                            <tr
                                                class="transition hover:bg-[#f8fbfe]">
                                                <td class="px-5 py-3.5">
                                                    <div class="flex items-center gap-3">
                                                        <div class="flex h-9 w-9 items-center justify-center bg-[#e8f4fc] text-gray-800 flex-shrink-0">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                                        </div>
                                                        <div>
                                                            <div class="text-[.82rem] font-bold text-[#1a2e3b]">
                                                                {{ $patient->last_name }}, {{ $patient->first_name }}
                                                            </div>
                                                            <div class="text-[.6rem] font-bold uppercase tracking-[.18em] text-[#0086da]">
                                                                #{{ sprintf('PT%04d', $patient->id) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-5 py-3.5 text-[.8rem] text-[#3d5a6e]">
                                                    {{ $patient->mobile_number ?? '—' }}
                                                </td>
                                                <td class="px-5 py-3.5">
                                                    <div class="text-[.78rem] text-[#3d5a6e]">
                                                        {{ $patient->email_address ?? '—' }}
                                                    </div>
                                                    @if (!$isPatientUser && !empty($patient->pending_recovery_request_id))
                                                        <span class="mt-1 inline-flex items-center bg-amber-100 px-2 py-0.5 text-[.58rem] font-bold uppercase tracking-[.14em] text-amber-800">
                                                            Pending Recovery
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="max-w-[200px] truncate px-5 py-3.5 text-[.78rem] text-[#7a9db5]"
                                                    title="{{ $patient->home_address ?? '' }}">
                                                    {{ $patient->home_address ?? '—' }}
                                                </td>
                                                <td class="px-5 py-3.5 text-[.82rem] text-[#1a2e3b]">
                                                    {{ $rowAge ?? '—' }}
                                                </td>
                                                <td class="px-5 py-3.5 text-right">
                                                    <div class="relative inline-block text-left"
                                                        x-data="{ open: false }"
                                                        @close-patient-menus.window="open = false">
                                                        <button type="button"
                                                            class="inline-flex items-center justify-center w-8 h-8 border border-[#e4eff8] bg-white text-[#7a9db5] transition hover:border-[#0086da] hover:text-[#0086da]"
                                                            @click.stop="$dispatch('close-patient-menus'); open = !open">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-ellipsis-vertical-icon lucide-ellipsis-vertical"><circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                                                        </button>
                                                        <div x-show="open" @click.away="open = false"
                                                            class="absolute right-0 z-20 mt-1 w-52 border border-[#e4eff8] bg-white shadow-[0_8px_24px_rgba(0,134,218,.12)]"
                                                            style="display: none;">
                                                            <button type="button"
                                                                class="flex w-full items-center gap-2.5 px-4 py-2.5 text-left text-[.78rem] font-semibold text-[#0086da] transition hover:bg-[#f0f8fe]"
                                                                wire:click="openProfile({{ $patient->id }})"
                                                                onclick="event.stopPropagation();">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-icon lucide-user"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                                                View Profile
                                                            </button>
                                                            <button type="button"
                                                                class="flex w-full items-center gap-2.5 px-4 py-2.5 text-left text-[.78rem] font-semibold text-[#3d5a6e] transition hover:bg-[#f0f8fe] hover:text-[#0086da]"
                                                                wire:click="$dispatch('editPatient', { id: {{ $patient->id }}, startStep: 1 })"
                                                                onclick="event.stopPropagation();">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-folder-open-icon lucide-folder-open"><path d="m6 14 1.5-2.9A2 2 0 0 1 9.24 10H20a2 2 0 0 1 1.94 2.5l-1.54 6a2 2 0 0 1-1.95 1.5H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3.9a2 2 0 0 1 1.69.9l.81 1.2a2 2 0 0 0 1.67.9H18a2 2 0 0 1 2 2v2"/></svg>
                                                                View Full Records
                                                            </button>
                                                            @if (!$isPatientUser)
                                                                <button type="button"
                                                                    class="flex w-full items-center gap-2.5 px-4 py-2.5 text-left text-[.78rem] font-semibold text-[#3d5a6e] transition hover:bg-[#f0f8fe] hover:text-[#0086da]"
                                                                    wire:click="$dispatch('editPatient', { id: {{ $patient->id }}, startStep: 2 })"
                                                                    onclick="event.stopPropagation();">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-heart-pulse-icon lucide-heart-pulse"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/><path d="M3.22 12H9.5l1.5-6 5 12 3-9h6.3"/></svg>
                                                                    Add Health History
                                                                </button>
                                                                <div class="border-t border-[#e4eff8]"></div>
                                                                <button type="button"
                                                                    class="flex w-full items-center gap-2.5 px-4 py-2.5 text-left text-[.78rem] font-semibold text-red-600 transition hover:bg-red-50"
                                                                    onclick="event.stopPropagation(); if (confirm('Delete this patient? This cannot be undone.')) { @this.deletePatient({{ $patient->id }}) }">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2-icon lucide-trash-2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                                                    Delete
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="p-8 text-center text-[.82rem] font-semibold text-[#7a9db5]">
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
                    <div class="grid grid-cols-1 gap-4 overflow-y-auto pb-4 pr-2 scrollbar-thin scrollbar-thumb-rounded-full scrollbar-track-[#f0f6fb] scrollbar-thumb-[#0086da] sm:grid-cols-2 lg:grid-cols-3" style="max-height:calc(100vh - 220px);">
                        @forelse($patients as $patient)
                            @php
                                $patientName = trim(($patient->first_name ?? '') . ' ' . ($patient->last_name ?? ''));
                                $patientCode = sprintf('PT%04d', $patient->id);
                                $patientInitials = strtoupper(
                                    substr($patient->first_name ?? 'P', 0, 1) . substr($patient->last_name ?? '', 0, 1),
                                );
                                $lastVisit = $patient->last_completed_at
                                    ? \Carbon\Carbon::parse($patient->last_completed_at)->format('m/d/Y')
                                    : 'No visits';
                                $cardAge = $patient->birth_date
                                    ? \Carbon\Carbon::parse($patient->birth_date)->age
                                    : null;
                                $locationLabel = $patient->home_address
                                    ? \Illuminate\Support\Str::limit($patient->home_address, 22)
                                    : '—';
                            @endphp

                            <div class="group border border-gray-200 bg-white shadow-sm transition hover:shadow-md">

                                {{-- Card top strip --}}
                                <div class="flex items-center justify-between border-b border-[#e4eff8] px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 text-[.58rem] font-bold uppercase tracking-[.14em]
                                        {{ $patient->patient_type === 'Active' ? 'bg-emerald-100 text-emerald-700' : 'bg-[#f0f6fb] text-[#7a9db5]' }}">
                                        {{ $patient->patient_type ?? 'Inactive' }}
                                    </span>
                                    @if (!$isPatientUser)
                                        <div class="relative" x-data="{ open: false }" @close-patient-menus.window="open = false">
                                            <button type="button"
                                                class="flex items-center justify-center w-7 h-7 bg-white text-black transition hover:border-[#0086da] hover:text-[#0086da]"
                                                @click.stop="$dispatch('close-patient-menus'); open = !open">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-ellipsis-vertical"><circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                                            </button>
                                            <div x-show="open" @click.away="open = false"
                                                class="absolute right-0 z-20 mt-1 w-48 border border-[#e4eff8] bg-white shadow-[0_8px_24px_rgba(0,134,218,.12)]"
                                                style="display:none;">

                                                <button type="button"
                                                    class="flex w-full items-center gap-2.5 px-4 py-2.5 text-left text-[.78rem] font-semibold text-red-600 transition hover:bg-red-50"
                                                    onclick="event.stopPropagation(); if (confirm('Delete this patient? This cannot be undone.')) { @this.deletePatient({{ $patient->id }}) }">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Avatar + Name --}}
                                <div class="flex flex-col items-center px-5 py-5 text-center">
                                    <div class="flex h-14 w-14 items-center justify-center bg-[#e8f4fc] text-gray-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    </div>
                                    <div class="mt-2 text-[.58rem] font-bold uppercase tracking-[.2em] text-[#0086da]">#{{ $patientCode }}</div>
                                    <div class="mt-0.5 text-[.92rem] font-extrabold text-[#1a2e3b] tracking-tight">
                                        {{ $patientName ?: 'Unnamed Patient' }}
                                    </div>
                                </div>

                                {{-- Stats strip --}}
                                <div class="grid grid-cols-3 border-t border-[#e4eff8]">
                                    <div class="border-r border-[#e4eff8] px-3 py-2.5 text-center">
                                        <div class="text-[.5rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Last Visit</div>
                                        <div class="mt-0.5 text-[.72rem] font-bold text-[#1a2e3b]">{{ $lastVisit }}</div>
                                    </div>
                                    <div class="border-r border-[#e4eff8] px-3 py-2.5 text-center">
                                        <div class="text-[.5rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Age</div>
                                        <div class="mt-0.5 text-[.72rem] font-bold text-[#1a2e3b]">{{ $cardAge ?? '—' }}</div>
                                    </div>
                                    <div class="px-3 py-2.5 text-center">
                                        <div class="text-[.5rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Gender</div>
                                        <div class="mt-0.5 text-[.72rem] font-bold text-[#1a2e3b]">{{ $patient->gender ?? '—' }}</div>
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="flex flex-col border-t border-[#e4eff8]">
                                    @if (!$isPatientUser)
                                        <a href="{{ route('appointment', ['patient_id' => $patient->id]) }}"
                                            class="flex items-center justify-center gap-1.5 border-b border-[#e4eff8] py-2.5 text-[.68rem] font-bold uppercase tracking-[.08em] text-[#0086da] transition hover:bg-[#f0f8fe]"
                                            onclick="event.stopPropagation();">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-plus"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h4"/></svg>
                                            Add Appointment
                                        </a>
                                    @endif
                                    <button type="button" wire:click="openProfile({{ $patient->id }})"
                                        onclick="event.stopPropagation();"
                                        class="flex items-center justify-center gap-1.5 bg-[#0086da] py-2.5 text-[.68rem] font-bold uppercase tracking-[.08em] text-white transition hover:bg-[#006ab0]">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
                                        View Record
                                    </button>
                                </div>
                            </div>

                        @empty
                            <div class="col-span-full border border-dashed border-[#d4e8f5] bg-[#f8fbfe] p-8 text-center">
                                <p class="text-[.82rem] font-semibold text-[#7a9db5]">No patients found for "{{ $search }}".</p>
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
