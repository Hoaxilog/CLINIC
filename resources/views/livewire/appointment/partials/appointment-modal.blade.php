<div x-cloak x-show="modalOpen"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center p-4" wire:ignore.self>

    <div class="absolute inset-0 bg-black opacity-60"></div>

    <div x-show="modalOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative z-10 w-full max-w-6xl overflow-hidden rounded-2xl bg-white shadow-2xl"
        style="font-family:'Montserrat',sans-serif; max-height: calc(100vh - 2rem);">

        {{-- Patient form opening overlay --}}
        <div x-cloak x-show="openingPatientForm" x-transition.opacity.duration.120ms
            class="absolute inset-0 z-20 flex items-center justify-center bg-white/85 backdrop-blur-[1px]">
            <div class="flex flex-col items-center gap-3">
                <div class="h-10 w-10 animate-spin rounded-full border-4 border-blue-200 border-t-[#0086da]"></div>
                <div class="text-sm font-semibold text-gray-700">Opening patient form...</div>
            </div>
        </div>

        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-[#cfe2f1]/60 px-6 py-5">
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h3 class="text-lg font-bold text-[#1a2e3b]">
                        @if ($isViewing) Appointment Details @else Book Appointment @endif
                    </h3>
                    @if ($isViewing && $appointmentStatus)
                        @php
                            $statusLabel = match($appointmentStatus) {
                                'Pending'   => 'Awaiting Approval',
                                'Scheduled' => 'Approved & Scheduled',
                                'Waiting'   => 'Patient Arrived',
                                'Ongoing'   => 'In Progress',
                                'Completed' => 'Completed',
                                'Cancelled' => 'Cancelled',
                                default     => $appointmentStatus,
                            };
                            $statusBadge = match($appointmentStatus) {
                                'Pending'   => 'bg-amber-100 text-amber-800 border-amber-200',
                                'Scheduled' => 'bg-[#e8f4fc] text-[#0086da] border-[#c5dff2]',
                                'Waiting'   => 'bg-blue-100 text-blue-700 border-blue-200',
                                'Ongoing'   => 'bg-violet-100 text-violet-700 border-violet-200',
                                'Completed' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                'Cancelled' => 'bg-gray-100 text-gray-500 border-gray-200',
                                default     => 'bg-gray-100 text-gray-500 border-gray-200',
                            };
                        @endphp
                        <span class="inline-flex items-center rounded-full border px-3 py-1 text-[11px] font-bold uppercase tracking-wide {{ $statusBadge }}">
                            {{ $statusLabel }}
                        </span>
                    @endif
                </div>
            </div>
            <button wire:click="closeAppointmentModal(true)"
                wire:loading.attr="disabled" wire:target="closeAppointmentModal"
                class="inline-flex h-9 w-9 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-600">
                <svg wire:loading.remove wire:target="closeAppointmentModal" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
                <svg wire:loading wire:target="closeAppointmentModal" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
            </button>
        </div>

        @if (session()->has('error'))
            <div class="border-b border-red-200 bg-red-50 px-6 py-3 text-sm font-semibold text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if ($showAppointmentModal)

            {{-- Loading overlay while Livewire fetches --}}
            <div wire:loading.flex wire:target="openAppointmentModal,viewAppointment,saveAppointment,updateStatus,admitPatient"
                class="min-h-[400px] flex-col items-center justify-center bg-white/80">
                <div class="flex flex-col items-center gap-3">
                    <div class="h-10 w-10 animate-spin rounded-full border-4 border-blue-200 border-t-[#0086da]"></div>
                    <div class="text-sm font-semibold text-[#1a2e3b]">Loading...</div>
                </div>
            </div>

            <form wire:submit.prevent="saveAppointment"
                wire:loading.remove wire:target="openAppointmentModal,viewAppointment,saveAppointment,updateStatus,admitPatient"
                class="overflow-y-auto px-6 py-6" style="max-height: calc(100vh - 10rem);">

                @php
                    $inputBase    = 'w-full rounded-lg border border-[#cfe2f1] bg-white px-4 py-2.5 text-sm text-[#1a2e3b] placeholder:text-gray-400 focus:border-[#0086da] focus:outline-none focus:ring-2 focus:ring-[#0086da]/10 transition';
                    $inputErr     = 'w-full rounded-lg border border-red-400 bg-white px-4 py-2.5 text-sm text-[#1a2e3b] placeholder:text-gray-400 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-100 transition';
                    $readonlyInp  = 'w-full rounded-lg border border-[#cfe2f1] bg-[#f6fafd] px-4 py-2.5 text-sm text-[#1a2e3b] cursor-not-allowed';
                    $labelClass   = 'mb-1.5 block text-xs font-bold uppercase tracking-widest text-[#587189]';

                    $firstNameInputClass   = $errors->has('firstName')      ? $inputErr : $inputBase;
                    $middleNameInputClass  = $errors->has('middleName')     ? $inputErr : $inputBase;
                    $lastNameInputClass    = $errors->has('lastName')       ? $inputErr : $inputBase;
                    $contactInputClass     = $errors->has('contactNumber')  ? $inputErr : $inputBase;
                    $birthDateInputClass   = $errors->has('birthDate')      ? $inputErr : $inputBase;
                    $serviceSelectClass    = $errors->has('selectedService')? $inputErr : $inputBase;
                @endphp

                {{-- STATUS TIMELINE --}}
                @if ($isViewing)
                    @php
                        $statusSteps = ['Pending', 'Scheduled', 'Waiting', 'Ongoing', 'Completed'];
                        $currentIdx  = array_search($appointmentStatus, $statusSteps);
                    @endphp
                    <div class="mb-6">
                        <div class="mb-2 text-xs font-bold uppercase tracking-widest text-[#587189]">Appointment Status</div>
                        <div class="flex items-center gap-0">
                            @foreach ($statusSteps as $i => $step)
                                @php
                                    $isDone    = $currentIdx !== false && $i < $currentIdx;
                                    $isCurrent = $currentIdx !== false && $i === $currentIdx;
                                @endphp
                                <div class="flex flex-1 flex-col items-center">
                                    <div class="h-2.5 w-2.5 rounded-full
                                        {{ $isCurrent ? 'bg-[#0086da] ring-4 ring-[#0086da]/20' : ($isDone ? 'bg-[#0086da]' : 'bg-gray-200') }}">
                                    </div>
                                    <div class="mt-1.5 text-center text-[10px] font-semibold
                                        {{ $isCurrent ? 'text-[#0086da]' : ($isDone ? 'text-[#0086da]' : 'text-gray-400') }}">
                                        {{ $step }}
                                    </div>
                                </div>
                                @if (!$loop->last)
                                    <div class="mb-4 h-[2px] flex-1 {{ $isDone ? 'bg-[#0086da]' : 'bg-gray-200' }}"></div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- DATE / TIME --}}
                <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label class="{{ $labelClass }}">Date</label>
                        @if ($isViewing && $appointmentStatus === 'Pending' && $isRescheduling)
                            <input type="date" wire:model.live="selectedDate" min="{{ now()->toDateString() }}" class="{{ $inputBase }}" />
                            @error('selectedDate')<span class="mt-1 block text-xs text-red-500">{{ $message }}</span>@enderror
                        @else
                            <input type="date" wire:model.live="selectedDate" min="{{ now()->toDateString() }}"
                                class="{{ $isViewing ? $readonlyInp : $inputBase }}"
                                @if ($isViewing) readonly @endif />
                            @error('selectedDate')<span class="mt-1 block text-xs text-red-500">{{ $message }}</span>@enderror
                        @endif
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Start Time</label>
                        @if ($isViewing && $appointmentStatus === 'Pending' && $isRescheduling)
                            <select wire:model.live="selectedTime" class="{{ $inputBase }}">
                                <option value="">-- Select Time --</option>
                                @foreach ($timeSlots as $time)
                                    <option value="{{ $time }}">{{ \Carbon\Carbon::parse($time)->format('g:i A') }}</option>
                                @endforeach
                            </select>
                            @error('selectedTime')<span class="mt-1 block text-xs text-red-500">{{ $message }}</span>@enderror
                        @elseif($isViewing)
                            {{-- Plain text — timeSlots is empty in view mode so use a readonly input --}}
                            <input type="text"
                                value="{{ !empty($selectedTime) ? \Carbon\Carbon::parse($selectedTime)->format('g:i A') : '—' }}"
                                class="{{ $readonlyInp }}" readonly />
                        @else
                            <select wire:model.live="selectedTime" class="{{ $inputBase }}">
                                <option value="">-- Select Time --</option>
                                @foreach ($timeSlots as $time)
                                    <option value="{{ $time }}">{{ \Carbon\Carbon::parse($time)->format('g:i A') }}</option>
                                @endforeach
                            </select>
                            @error('selectedTime')<span class="mt-1 block text-xs text-red-500">{{ $message }}</span>@enderror
                        @endif
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">End Time</label>
                        <input type="text" value="{{ !empty($endTime) ? \Carbon\Carbon::parse($endTime)->format('h:i A') : '' }}"
                            class="{{ $readonlyInp }}" readonly />
                    </div>
                </div>

                {{-- REQUESTER INFO --}}
                @if ($isViewing && $viewingBookingForOther)
                <div class="mb-6 overflow-hidden rounded-xl border border-[#cfe2f1] bg-[#f6fafd]">
                    <div class="border-b border-[#cfe2f1] px-5 py-3">
                        <span class="text-xs font-bold uppercase tracking-widest text-[#587189]">Requester Info</span>
                    </div>
                    <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2">
                        <div>
                            <label class="{{ $labelClass }}">First Name</label>
                            <input type="text" value="{{ $viewingRequesterFirstName }}" class="{{ $readonlyInp }}" readonly />
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Last Name</label>
                            <input type="text" value="{{ $viewingRequesterLastName }}" class="{{ $readonlyInp }}" readonly />
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Contact Number</label>
                            <input type="text" value="{{ $viewingRequesterContactNumber }}" class="{{ $readonlyInp }}" readonly />
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Email</label>
                            <input type="text" value="{{ $viewingRequesterEmail }}" class="{{ $readonlyInp }}" readonly />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="{{ $labelClass }}">Relationship to Patient</label>
                            <input type="text" value="{{ $viewingRequesterRelationship }}" class="{{ $readonlyInp }}" readonly />
                        </div>
                    </div>
                </div>
                @endif

                {{-- PATIENT SEARCH (booking mode only) --}}
                @if (!$isViewing)
                    <div class="mb-6 relative">
                        <label class="{{ $labelClass }}">Search Existing Patient</label>
                        <div class="relative">
                            <input type="text" wire:model.live.debounce.300ms="searchQuery"
                                class="{{ $inputBase }} pl-10"
                                placeholder="Search by name or phone number..." />
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                                </svg>
                            </div>
                        </div>
                        @if (!empty($searchQuery) && count($patientSearchResults) > 0)
                            <div class="absolute z-50 mt-1 w-full rounded-xl border border-[#cfe2f1] bg-white py-1 shadow-xl max-h-56 overflow-auto text-sm">
                                @foreach ($patientSearchResults as $result)
                                    <button type="button"
                                        wire:key="psr-{{ $result->id }}"
                                        wire:click="selectPatient({{ $result->id }})"
                                        class="w-full border-b border-gray-100 px-4 py-2.5 text-left last:border-0 hover:bg-[#f6fafd] transition">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="font-semibold text-[#1a2e3b]">{{ $result->last_name }}, {{ $result->first_name }}</span>
                                            <span class="text-xs text-gray-400">{{ $result->mobile_number }}</span>
                                        </div>
                                        <div class="text-xs text-gray-400">{{ $result->birth_date ? \Carbon\Carbon::parse($result->birth_date)->format('M d, Y') : 'No birth date' }}</div>
                                    </button>
                                @endforeach
                            </div>
                        @elseif(!empty($searchQuery) && strlen($searchQuery) >= 2)
                            <div class="absolute z-50 mt-1 w-full rounded-xl border border-[#cfe2f1] bg-white px-4 py-3 text-sm text-gray-500 shadow-lg">
                                No patient found — fill in the form below to create a new record.
                            </div>
                        @endif
                    </div>
                @endif

                {{-- PATIENT NAME --}}
                <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label class="{{ $labelClass }}">First Name <span class="text-red-500 normal-case tracking-normal">*</span></label>
                        <input wire:model="firstName" type="text"
                            class="{{ $isViewing ? $readonlyInp : $firstNameInputClass }}"
                            @if ($isViewing) readonly @endif />
                        @error('firstName')<span class="mt-1 block text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Middle Name</label>
                        <input wire:model="middleName" type="text"
                            class="{{ $isViewing ? $readonlyInp : $middleNameInputClass }}"
                            @if ($isViewing) readonly @endif />
                        @error('middleName')<span class="mt-1 block text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Last Name <span class="text-red-500 normal-case tracking-normal">*</span></label>
                        <input wire:model="lastName" type="text"
                            class="{{ $isViewing ? $readonlyInp : $lastNameInputClass }}"
                            @if ($isViewing) readonly @endif />
                        @error('lastName')<span class="mt-1 block text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>
                </div>

                {{-- CONTACT / BIRTH DATE --}}
                <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="{{ $labelClass }}">Contact Number <span class="text-red-500 normal-case tracking-normal">*</span></label>
                        <input wire:model="contactNumber" type="text" inputmode="numeric" maxlength="11" pattern="[0-9]{11}"
                            class="{{ $isViewing ? $readonlyInp : $contactInputClass }}"
                            @if ($isViewing) readonly @endif />
                        @error('contactNumber')<span class="mt-1 block text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Birth Date <span class="text-red-500 normal-case tracking-normal">*</span></label>
                        <input wire:model.live="birthDate" type="date"
                            class="{{ $isViewing ? $readonlyInp : $birthDateInputClass }}"
                            @if ($isViewing) readonly @endif />
                        @error('birthDate')<span class="mt-1 block text-xs text-red-500">{{ $message }}</span>@enderror
                    </div>
                </div>

                {{-- SERVICE --}}
                <div class="mb-6">
                    <label class="{{ $labelClass }}">Service <span class="text-red-500 normal-case tracking-normal">*</span></label>
                    <select wire:model.live="selectedService"
                        class="{{ $isViewing ? $readonlyInp : $serviceSelectClass }}"
                        @if ($isViewing) disabled @endif>
                        <option value="" disabled>Select a service</option>
                        @foreach ($servicesList as $service)
                            <option value="{{ $service->id }}">
                                @php
                                    $dur = \Carbon\Carbon::parse($service->duration);
                                    $durH = (int) $dur->format('H');
                                    $durM = (int) $dur->format('i');
                                    $durLabel = ($durH > 0 ? $durH . 'h ' : '') . ($durM > 0 ? $durM . 'm' : '');
                                @endphp
                                {{ $service->service_name }} ({{ trim($durLabel) ?: '—' }})
                            </option>
                        @endforeach
                    </select>
                    @error('selectedService')<span class="mt-1 block text-xs text-red-500">{{ $message }}</span>@enderror
                </div>

                @if (false)
                    <div class="mb-6 rounded-xl border border-[#cfe2f1] bg-[#f6fafd] p-5">
                        <div class="mb-3 text-xs font-bold uppercase tracking-widest text-[#587189]">Appointment Summary</div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div>
                                <div class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Date</div>
                                <div class="mt-1 text-sm font-semibold text-[#1a2e3b]">{{ !empty($selectedDate) ? \Carbon\Carbon::parse($selectedDate)->format('M d, Y') : '—' }}</div>
                            </div>
                            <div>
                                <div class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Time</div>
                                <div class="mt-1 text-sm font-semibold text-[#1a2e3b]">
                                    {{ !empty($selectedTime) ? \Carbon\Carbon::parse($selectedTime)->format('g:i A') : '—' }}
                                    @if (!empty($endTime))
                                        to {{ \Carbon\Carbon::parse($endTime)->format('g:i A') }}
                                    @endif
                                </div>
                            </div>
                            <div>
                                <div class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Service</div>
                                <div class="mt-1 text-sm font-semibold text-[#1a2e3b]">{{ optional(collect($servicesList)->firstWhere('id', $selectedService))->service_name ?? '—' }}</div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- APPROVAL SAFETY CHECK (Pending only) --}}
                @if ($isViewing && $appointmentStatus === 'Pending' && !$isRescheduling)
                    @php
                        $safetyTone = $pendingApprovalSafety['tone'] ?? 'emerald';
                        $safetyBg = match ($safetyTone) {
                            'rose'  => 'border-red-200 bg-red-50',
                            'amber' => 'border-amber-200 bg-amber-50',
                            default => 'border-emerald-200 bg-emerald-50',
                        };
                        $safetyText = match ($safetyTone) {
                            'rose'  => 'text-red-800',
                            'amber' => 'text-amber-800',
                            default => 'text-emerald-800',
                        };
                        $safetyBadge = match ($safetyTone) {
                            'rose'  => 'bg-red-100 text-red-700',
                            'amber' => 'bg-amber-100 text-amber-700',
                            default => 'bg-emerald-100 text-emerald-700',
                        };
                    @endphp
                    <div x-data="{ showDetails: false }"
                        class="mb-6 overflow-hidden rounded-xl border {{ $safetyBg }}">
                        <div class="flex flex-wrap items-center gap-3 px-5 py-4">
                            <div class="flex-1 min-w-0">
                                <div class="mb-1 text-xs font-bold uppercase tracking-widest {{ $safetyText }} opacity-60">Approval Safety Check</div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex rounded-full {{ $safetyBadge }} px-3 py-1 text-xs font-semibold">
                                        {{ $pendingApprovalSafety['headline'] ?? 'Review before approving' }}
                                    </span>
                                    <span class="text-xs {{ $safetyText }}">
                                        {{ $pendingApprovalSafety['approved_overlap_count'] ?? 0 }}/2 approved in slot
                                    </span>
                                    @if (($pendingApprovalSafety['same_time_pending_count'] ?? 0) > 0)
                                        <span class="text-xs {{ $safetyText }}">
                                            · {{ $pendingApprovalSafety['same_time_pending_count'] }} other pending at same time
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <button type="button" @click="showDetails = !showDetails"
                                class="shrink-0 rounded-lg border border-current/20 bg-white/60 px-3 py-1.5 text-xs font-semibold {{ $safetyText }} hover:bg-white/80 transition">
                                <span x-show="!showDetails">Show Details</span>
                                <span x-show="showDetails" x-cloak>Hide</span>
                            </button>
                        </div>
                        <div x-cloak x-show="showDetails" x-transition.opacity.duration.150ms
                            class="space-y-2 border-t border-current/10 px-5 pb-4 pt-3">
                            @if (!empty($pendingApprovalSafety['overlapping_appointments']))
                                @foreach ($pendingApprovalSafety['overlapping_appointments'] as $overlap)
                                    <div class="flex items-center justify-between gap-3 rounded-lg border border-white/60 bg-white/70 px-4 py-2.5 text-sm">
                                        <div>
                                            <div class="font-semibold text-[#1a2e3b]">{{ $overlap['patient_name'] ?: 'Unnamed patient' }}</div>
                                            <div class="text-xs text-gray-500">{{ $overlap['service_name'] }} · {{ $overlap['time'] }}</div>
                                        </div>
                                        <span class="inline-flex rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-gray-500 shadow-sm">{{ $overlap['status'] }}</span>
                                    </div>
                                @endforeach
                            @endif
                            @if (!empty($pendingDuplicateWarnings))
                                <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-2.5 text-xs font-semibold text-amber-700 space-y-0.5">
                                    @foreach ($pendingDuplicateWarnings as $w)<div>⚠ {{ $w }}</div>@endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- PATIENT LINKING (Scheduled/Waiting, no patient linked) --}}
                @if ($isViewing && empty($viewingPatientId) && in_array($appointmentStatus, ['Waiting', 'Scheduled'], true) && auth()->user()->role !== 3)

                    <div class="mb-4 overflow-hidden rounded-xl border border-amber-200 bg-amber-50 px-5 py-4">
                        <div class="text-sm font-semibold text-amber-900">No patient record linked yet</div>
                        <p class="mt-1 text-xs text-amber-700">
                            Link an existing patient record or create a new one below to enable chart access, check-in, and admission.
                        </p>
                        @if (!empty($pendingDuplicateWarnings))
                            <div class="mt-2 space-y-0.5 text-xs font-semibold text-amber-700">
                                @foreach ($pendingDuplicateWarnings as $w)<div>⚠ {{ $w }}</div>@endforeach
                            </div>
                        @endif
                    </div>

                    {{-- MATCH CANDIDATES --}}
                    <div class="mb-6 overflow-hidden rounded-2xl border border-[#cfe2f1] bg-white/90">
                        <div class="flex items-center justify-between border-b border-[#cfe2f1] bg-[#f5f9fd] px-5 py-3">
                            <div>
                                <div class="text-xs font-bold uppercase tracking-widest text-[#0086da]">Possible Patient Matches</div>
                                <p class="mt-0.5 text-xs text-[#587189]">Click a candidate to review details before linking.</p>
                            </div>
                            @if (!empty($pendingMatchCandidates))
                                <span class="inline-flex rounded-full bg-[#e8f4fc] px-3 py-1 text-xs font-semibold text-[#0086da]">
                                    {{ count($pendingMatchCandidates) }} {{ \Illuminate\Support\Str::plural('match', count($pendingMatchCandidates)) }}
                                </span>
                            @endif
                        </div>

                        @if (!empty($pendingMatchCandidates))
                            @php
                                $initialMatchId = (int) ($selectedPendingPatientId ?: (collect($pendingMatchCandidates)->first()->id ?? 0));
                            @endphp

                            <div x-data="{ selectedMatch: @entangle('selectedPendingPatientId').live }"
                                wire:key="match-candidates-panel"
                                x-init="if (!selectedMatch) selectedMatch = {{ $initialMatchId }}"
                                class="grid gap-0 lg:grid-cols-[minmax(0,0.88fr)_minmax(0,1.12fr)]">

                                {{-- Left — candidate list --}}
                                <div class="border-b border-[#dbeaf7] bg-[#f5f9fd] p-4 lg:border-b-0 lg:border-r">
                                    <div class="space-y-2">
                                        @foreach ($pendingMatchCandidates as $candidate)
                                            @php
                                                $candidateId      = (int) $candidate->id;
                                                $candidateName    = trim(($candidate->first_name ?? '') . ' ' . ($candidate->last_name ?? '')) ?: 'Unnamed patient';
                                                $candidateBirth   = !empty($candidate->birth_date) ? \Carbon\Carbon::parse($candidate->birth_date)->format('M d, Y') : 'No birth date';
                                                $candidateBand    = (string) ($candidate->match_band ?? 'poor');
                                                $candidatePercent = (int) ($candidate->match_percent ?? 0);
                                                $dotColor = match ($candidateBand) {
                                                    'strong'  => 'bg-emerald-500',
                                                    'partial' => 'bg-amber-500',
                                                    'weak'    => 'bg-slate-500',
                                                    default   => 'bg-slate-400',
                                                };
                                            @endphp
                                            <button type="button"
                                                @click="selectedMatch = {{ $candidateId }}"
                                                class="w-full rounded-xl border px-4 py-3 text-left transition"
                                                :class="selectedMatch === {{ $candidateId }}
                                                    ? 'border-[#2e8dd0] bg-[#eaf5ff] shadow-[inset_0_0_0_1px_rgba(0,134,218,.18)]'
                                                    : 'border-[#d7e8f5] bg-white hover:border-[#8fc0e1]'">
                                                <div class="flex items-start gap-3">
                                                    <span class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full {{ $dotColor }}"></span>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="font-bold text-[#1a2e3b]">{{ $candidateName }}</p>
                                                        <p class="mt-0.5 text-xs text-[#6e8ea5]">{{ $candidateBirth }} · #{{ $candidateId }} · {{ $candidatePercent }}%</p>
                                                    </div>
                                                    <span aria-hidden="true"
                                                        class="mt-0.5 ml-auto inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full border transition"
                                                        :class="selectedMatch === {{ $candidateId }}
                                                            ? 'border-[#0086da] bg-[#0086da]'
                                                            : 'border-[#9fc8e3] bg-white'">
                                                        <span class="h-2 w-2 rounded-full bg-white"
                                                            :class="selectedMatch === {{ $candidateId }} ? 'opacity-100' : 'opacity-0'"></span>
                                                    </span>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Right — candidate detail panel --}}
                                <div class="relative bg-white p-4 md:p-5">

                                    {{-- Loading only on this right pane --}}
                                    <div wire:loading.flex wire:target="selectedPendingPatientId"
                                        class="absolute inset-0 z-10 items-center justify-center bg-white/80">
                                        <div class="flex items-center gap-2 rounded-xl border border-[#cfe2f1] bg-white px-3 py-2 shadow-sm">
                                            <div class="h-3.5 w-3.5 animate-spin rounded-full border-2 border-blue-200 border-t-[#0086da]"></div>
                                            <span class="text-xs font-semibold text-[#0086da]">Loading details...</span>
                                        </div>
                                    </div>

                                    @foreach ($pendingMatchCandidates as $candidate)
                                        @php
                                            $candidateId   = (int) $candidate->id;
                                            $candidateName = trim(($candidate->first_name ?? '') . ' ' . ($candidate->last_name ?? '')) ?: 'Unnamed patient';
                                            $matchBand     = (string) ($candidate->match_band ?? 'poor');
                                            $matchPercent  = (int) ($candidate->match_percent ?? 0);
                                            $matchLabel    = (string) ($candidate->match_band_label ?? 'Poor match');
                                            $matchBadge    = match ($matchBand) {
                                                'strong'  => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                                                'partial' => 'border-amber-200 bg-amber-50 text-amber-700',
                                                'weak'    => 'border-slate-300 bg-slate-100 text-slate-700',
                                                default   => 'border-slate-300 bg-slate-50 text-slate-600',
                                            };

                                            $requestName  = strtolower(trim(($firstName ?? '') . ' ' . ($lastName ?? '')));
                                            $cName        = strtolower(trim(($candidate->first_name ?? '') . ' ' . ($candidate->last_name ?? '')));
                                            $nameMatch    = $requestName !== '' && $cName === $requestName;

                                            $reqBirth  = !empty($birthDate) ? \Carbon\Carbon::parse($birthDate)->toDateString() : null;
                                            $cBirth    = !empty($candidate->birth_date) ? \Carbon\Carbon::parse($candidate->birth_date)->toDateString() : null;
                                            $birthMatch = $reqBirth && $cBirth && $reqBirth === $cBirth;

                                            $reqMobile = trim((string) ($viewingRequesterContactNumber ?: ($contactNumber ?? '')));
                                            $cMobile   = trim((string) ($candidate->mobile_number ?? ''));
                                            $mobileMatch = $reqMobile && $cMobile && $reqMobile === $cMobile;

                                            $reqEmail  = strtolower(trim((string) ($viewingRequesterEmail ?? '')));
                                            $cEmail    = strtolower(trim((string) ($candidate->email_address ?? '')));
                                            $emailMatch = $reqEmail && $cEmail && $reqEmail === $cEmail;
                                        @endphp
                                        <div x-cloak x-show="selectedMatch === {{ $candidateId }}" class="space-y-4">
                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                <p class="text-xs font-bold uppercase tracking-widest text-[#7a9db5]">Match Signals</p>
                                                <span class="inline-flex items-center rounded-full border px-3 py-1 text-[11px] font-bold uppercase tracking-wide {{ $matchBadge }}">
                                                    {{ $matchPercent }}% · {{ $matchLabel }}
                                                </span>
                                            </div>

                                            @if (!empty($candidate->match_reasons))
                                                <div class="flex flex-wrap gap-1.5">
                                                    @foreach ($candidate->match_reasons as $reason)
                                                        <span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">
                                                            ✓ {{ $reason }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif

                                            <div class="overflow-hidden rounded-xl border border-[#dbeaf7] bg-[#f8fbfe]">
                                                <table class="w-full text-xs">
                                                    <thead>
                                                        <tr class="border-b border-[#dbeaf7]">
                                                            <th class="px-3 py-2 text-left font-bold uppercase tracking-widest text-[#7a9db5]">Field</th>
                                                            <th class="px-3 py-2 text-left font-bold uppercase tracking-widest text-[#7a9db5]">Requested</th>
                                                            <th class="px-3 py-2 text-left font-bold uppercase tracking-widest text-[#7a9db5]">On Record</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-[#e5eff8]">
                                                        <tr>
                                                            <td class="px-3 py-2 font-semibold text-[#7a9db5]">Name</td>
                                                            <td class="px-3 py-2 text-[#1a2e3b]">{{ trim(($firstName ?? '') . ' ' . ($lastName ?? '')) ?: '—' }}</td>
                                                            <td class="px-3 py-2 {{ $nameMatch ? 'font-semibold text-emerald-700' : 'text-[#1a2e3b]' }}">{{ $nameMatch ? '✓ ' : '' }}{{ $candidateName }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="px-3 py-2 font-semibold text-[#7a9db5]">Birthday</td>
                                                            <td class="px-3 py-2 text-[#1a2e3b]">{{ $reqBirth ? \Carbon\Carbon::parse($reqBirth)->format('M d, Y') : '—' }}</td>
                                                            <td class="px-3 py-2 {{ $birthMatch ? 'font-semibold text-emerald-700' : 'text-[#1a2e3b]' }}">{{ $birthMatch ? '✓ ' : '' }}{{ !empty($candidate->birth_date) ? \Carbon\Carbon::parse($candidate->birth_date)->format('M d, Y') : '—' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="px-3 py-2 font-semibold text-[#7a9db5]">Mobile</td>
                                                            <td class="px-3 py-2 text-[#1a2e3b]">{{ $reqMobile ?: '—' }}</td>
                                                            <td class="px-3 py-2 {{ $mobileMatch ? 'font-semibold text-emerald-700' : 'text-[#1a2e3b]' }}">{{ $mobileMatch ? '✓ ' : '' }}{{ $candidate->mobile_number ?: '—' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="px-3 py-2 font-semibold text-[#7a9db5]">Email</td>
                                                            <td class="px-3 py-2 text-[#1a2e3b] break-all">{{ $reqEmail ?: '—' }}</td>
                                                            <td class="px-3 py-2 break-all {{ $emailMatch ? 'font-semibold text-emerald-700' : 'text-[#1a2e3b]' }}">{{ $emailMatch ? '✓ ' : '' }}{{ $candidate->email_address ?: '—' }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <button type="button"
                                                @click.stop="openingPatientForm = true; resumeAppointmentModalOnPatientFormClose = true"
                                                wire:click="previewPendingPatientRecord({{ $candidateId }})"
                                                class="w-full rounded-lg border border-[#cfe2f1] bg-white px-4 py-2 text-xs font-semibold text-[#1a2e3b] transition hover:bg-[#f6fafd]">
                                                View Full Patient Record
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Link / Create --}}
                            <div class="flex flex-wrap gap-2 border-t border-[#cfe2f1] bg-[#f5f9fd] px-5 py-4">
                                <button type="button" wire:click="linkPendingRequestToExistingPatient"
                                    wire:loading.attr="disabled" wire:target="linkPendingRequestToExistingPatient"
                                    class="inline-flex items-center gap-2 rounded-lg bg-[#0086da] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#0073a8]">
                                    Link to Selected Patient
                                </button>
                                <button type="button" wire:click="createPatientForPendingRequest"
                                    wire:loading.attr="disabled" wire:target="createPatientForPendingRequest"
                                    class="inline-flex items-center gap-2 rounded-lg border border-[#cfe2f1] bg-white px-4 py-2.5 text-sm font-semibold text-[#1a2e3b] shadow-sm transition hover:bg-[#f6fafd]">
                                    Create New Patient Record
                                </button>
                            </div>

                        @else
                            <div class="px-5 py-8 text-center">
                                <div class="text-sm font-semibold text-[#587189]">No matching records found</div>
                                <p class="mt-1 text-xs text-[#7a9db5]">No existing patient closely matches this request.</p>
                            </div>
                            <div class="border-t border-[#cfe2f1] bg-[#f5f9fd] px-5 py-4">
                                <button type="button" wire:click="createPatientForPendingRequest"
                                    wire:loading.attr="disabled" wire:target="createPatientForPendingRequest"
                                    class="inline-flex w-full items-center justify-center rounded-lg bg-[#0086da] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#0073a8]">
                                    Create New Patient Record
                                </button>
                            </div>
                        @endif
                    </div>

                @elseif ($isViewing && $viewingPatientId && in_array($appointmentStatus, ['Scheduled', 'Waiting', 'Completed'], true))
                    <div class="mb-6 flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4">
                        <div>
                            <div class="text-sm font-semibold text-emerald-900">Patient record linked ✓</div>
                        </div>
                        @if (auth()->user()->role !== 3 && in_array($appointmentStatus, ['Scheduled', 'Waiting'], true))
                            <button type="button" wire:click="unlinkAppointmentPatient"
                                wire:loading.attr="disabled" wire:target="unlinkAppointmentPatient"
                                wire:confirm="Remove the patient record link? You can re-link it right after."
                                class="shrink-0 rounded-lg border border-emerald-300 bg-white px-3 py-1.5 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100">
                                Change / Unlink
                            </button>
                        @endif
                    </div>
                @endif

                {{-- FOOTER ACTIONS --}}
                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-[#cfe2f1] pt-5">

                    @error('conflict')
                        <span class="w-full text-xs text-red-500">{{ $message }}</span>
                    @enderror

                    @if ($isViewing && !in_array($appointmentStatus, ['Cancelled', 'Completed']) && !$isRescheduling)
                        <button type="button" wire:click="updateStatus('Cancelled')"
                            wire:loading.attr="disabled" wire:target="updateStatus"
                            wire:confirm="Are you sure you want to cancel this appointment? This cannot be undone."
                            class="inline-flex items-center gap-2 rounded-lg border border-red-200 bg-white px-4 py-2.5 text-sm font-semibold text-red-700 shadow-sm transition hover:bg-red-50">
                            Cancel Appointment
                        </button>
                    @elseif(!$isViewing)
                        <button type="button" wire:click="closeAppointmentModal(true)"
                            wire:loading.attr="disabled" wire:target="closeAppointmentModal"
                            class="inline-flex items-center gap-2 rounded-lg border border-[#cfe2f1] bg-white px-4 py-2.5 text-sm font-semibold text-[#1a2e3b] shadow-sm transition hover:bg-[#f6fafd]">
                            Discard
                        </button>
                    @else
                        <div></div>
                    @endif

                    <div class="flex items-center gap-2">
                        @if ($isViewing)
                            @if ($appointmentStatus === 'Pending')
                                @if (auth()->user()->role !== 3)
                                    @if ($isRescheduling)
                                        <button type="button" wire:click="cancelPendingReschedule"
                                            wire:loading.attr="disabled" wire:target="cancelPendingReschedule"
                                            class="inline-flex items-center gap-2 rounded-lg border border-[#cfe2f1] bg-white px-4 py-2.5 text-sm font-semibold text-[#1a2e3b] shadow-sm transition hover:bg-[#f6fafd]">
                                            Cancel Reschedule
                                        </button>
                                        <button type="button" wire:click="savePendingReschedule"
                                            wire:loading.attr="disabled" wire:target="savePendingReschedule"
                                            wire:confirm="Save the new date and time for this request?"
                                            class="inline-flex items-center gap-2 rounded-lg bg-[#0086da] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#0073a8]">
                                            Save New Schedule
                                        </button>
                                    @else
                                        <button type="button" wire:click="beginPendingReschedule"
                                            wire:loading.attr="disabled" wire:target="beginPendingReschedule"
                                            class="inline-flex items-center gap-2 rounded-lg border border-[#cfe2f1] bg-white px-4 py-2.5 text-sm font-semibold text-[#1a2e3b] shadow-sm transition hover:bg-[#f6fafd]">
                                            Reschedule
                                        </button>
                                        <button type="button" wire:click="updateStatus('Scheduled')"
                                            wire:loading.attr="disabled" wire:target="updateStatus"
                                            wire:confirm="Approve this appointment request and add it to the schedule?"
                                            @disabled(!($pendingApprovalSafety['can_approve'] ?? true))
                                            class="inline-flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold shadow-sm transition
                                                {{ ($pendingApprovalSafety['can_approve'] ?? true) ? 'bg-[#0086da] text-white hover:bg-[#0073a8]' : 'border border-[#cfe2f1] bg-gray-100 text-gray-400 cursor-not-allowed' }}">
                                            {{ ($pendingApprovalSafety['can_approve'] ?? true) ? 'Approve & Schedule' : 'Cannot Approve — Slot Full' }}
                                        </button>
                                    @endif
                                @endif
                            @elseif($appointmentStatus === 'Scheduled')
                                <button type="button" @click="openingPatientForm = true" wire:click="dispatchPatientForm(1)"
                                    class="inline-flex items-center gap-2 rounded-lg border border-[#cfe2f1] bg-white px-4 py-2.5 text-sm font-semibold text-[#1a2e3b] shadow-sm transition hover:bg-[#f6fafd]">
                                    View Patient Info
                                </button>
                                <button type="button" wire:click="updateStatus('Waiting')"
                                    wire:loading.attr="disabled" wire:target="updateStatus"
                                    wire:confirm="Mark patient as arrived and ready to be seen?"
                                    class="inline-flex items-center gap-2 rounded-lg bg-[#0086da] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#0073a8]">
                                    Mark as Ready
                                </button>
                            @elseif($appointmentStatus === 'Waiting')
                                <button type="button" @click="openingPatientForm = true" wire:click="dispatchPatientForm(1)"
                                    class="inline-flex items-center gap-2 rounded-lg border border-[#cfe2f1] bg-white px-4 py-2.5 text-sm font-semibold text-[#1a2e3b] shadow-sm transition hover:bg-[#f6fafd]">
                                    View Patient Info
                                </button>
                                @if (auth()->user()?->canHandleChairsideFlow())
                                    <button type="button" wire:click="admitPatient"
                                        wire:loading.attr="disabled" wire:target="admitPatient"
                                        wire:confirm="Admit this patient to the chair now?"
                                        @if (!$viewingPatientId) disabled @endif
                                        class="inline-flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold shadow-sm transition
                                            {{ $viewingPatientId ? 'bg-[#0086da] text-white hover:bg-[#0073a8]' : 'border border-[#cfe2f1] bg-gray-100 text-gray-400 cursor-not-allowed' }}">
                                        ADMIT PATIENT
                                    </button>
                                @endif
                            @elseif($appointmentStatus === 'Ongoing')
                                @if ($this->canOpenViewingPatientChart())
                                    <button type="button" @click="openingPatientForm = true" wire:click="dispatchPatientForm(3)"
                                        class="inline-flex items-center gap-2 rounded-lg border border-[#cfe2f1] bg-white px-4 py-2.5 text-sm font-semibold text-[#1a2e3b] shadow-sm transition hover:bg-[#f6fafd]">
                                        View Dental Chart
                                    </button>
                                @endif
                                <button type="button" wire:click="updateStatus('Completed')"
                                    wire:loading.attr="disabled" wire:target="updateStatus"
                                    wire:confirm="Mark this appointment as done and completed?"
                                    class="inline-flex items-center gap-2 rounded-lg bg-[#0086da] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#0073a8]">
                                    Finish &amp; Complete
                                </button>
                            @elseif($appointmentStatus === 'Completed')
                                @if ($viewingPatientId)
                                    <button type="button" @click="openingPatientForm = true" wire:click="dispatchPatientForm(1)"
                                        class="inline-flex items-center gap-2 rounded-lg border border-[#cfe2f1] bg-white px-4 py-2.5 text-sm font-semibold text-[#1a2e3b] shadow-sm transition hover:bg-[#f6fafd]">
                                        View Patient Info
                                    </button>
                                @endif
                                <span class="inline-flex items-center rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-semibold text-emerald-700">
                                    ✓ Completed
                                </span>
                            @elseif($appointmentStatus === 'Cancelled')
                                <span class="inline-flex items-center rounded-lg border border-gray-200 bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-500">
                                    ✕ Cancelled
                                </span>
                            @endif
                        @else
                            <button type="submit" wire:loading.attr="disabled" wire:target="saveAppointment"
                                onclick="return confirm('Save this appointment and patient details?')"
                                class="inline-flex items-center gap-2 rounded-lg bg-[#0086da] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#0073a8]">
                                Save Appointment
                            </button>
                        @endif
                    </div>

                </div>

            </form>
        @else
            <div class="flex min-h-[400px] items-center justify-center bg-white">
                <div class="flex flex-col items-center gap-3">
                    <div class="h-10 w-10 animate-spin rounded-full border-4 border-blue-200 border-t-[#0086da]"></div>
                    <div class="text-sm font-semibold text-[#1a2e3b]">Loading...</div>
                </div>
            </div>
        @endif
    </div>
</div>
