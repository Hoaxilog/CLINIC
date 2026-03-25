<div>
    @php
        $isPatientUser = auth()->check() && auth()->user()->role === 3;
        $patientName = $isPatientUser ? auth()->user()->username ?? 'Patient' : null;

        /* ── Shared input base ── */
        $inputBase =
            'w-full border bg-white px-4 py-3 text-sm text-[#1a2e3b] placeholder:text-[#9bbdd0] font-[Montserrat] outline-none transition focus:ring-2 focus:ring-[#cde8fb] focus:border-[#0086da] rounded-none';
        $inputError =
            'w-full border border-red-400 bg-white px-4 py-3 text-sm text-[#1a2e3b] placeholder:text-[#9bbdd0] font-[Montserrat] outline-none transition focus:ring-2 focus:ring-red-200 focus:border-red-500 rounded-none';
        $fieldClass = fn(string $f) => $errors->has($f) ? $inputError : $inputBase . ' border-[#d4e8f5]';

        $selectBase =
            'w-full border bg-white px-4 py-3 text-sm text-[#1a2e3b] font-[Montserrat] outline-none transition focus:ring-2 focus:ring-[#cde8fb] focus:border-[#0086da] rounded-none appearance-none cursor-pointer';
        $selectError =
            'w-full border border-red-400 bg-white px-4 py-3 text-sm text-[#1a2e3b] font-[Montserrat] outline-none transition focus:ring-2 focus:ring-red-200 focus:border-red-500 rounded-none appearance-none cursor-pointer';
        $selectClass = fn(string $f) => $errors->has($f) ? $selectError : $selectBase . ' border-[#d4e8f5]';

        $desktopDatePickerClass = $errors->has('selectedDate')
            ? 'hidden md:block border border-red-400 p-4 bg-white'
            : 'hidden md:block border border-[#e4eff8] p-4 bg-white';

        $slotGridClass = $errors->has('selectedSlot')
            ? 'grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-2 border border-red-400 p-1'
            : 'grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-2 p-1';
    @endphp

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400&display=swap');

        [style*="font-family"] {
            font-family: 'Montserrat', sans-serif !important;
        }

        .booking-wrap * {
            font-family: 'Montserrat', sans-serif;
        }
    </style>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <section
        class="booking-wrap min-h-screen bg-[#f6fafd] {{ $guestOtpStepActive ? 'flex items-center justify-center py-16 px-4' : '' }}"
        style="font-family:'Montserrat',sans-serif; -webkit-font-smoothing:antialiased;">

        {{-- ══════════════════════════════════
             HERO BANNER
        ══════════════════════════════════ --}}
        @if (!$guestOtpStepActive)
            <div class="px-6 pt-6 md:px-12 md:pt-8 xl:px-20">
                <div class="mx-auto w-full max-w-[1400px] border border-[#e4eff8] bg-white">
                    <div class="flex items-center gap-4 px-6 py-6 md:px-8">
                        <div>
                            @if ($isPatientUser)
                                <a href="{{ route('patient.dashboard') }}"
                                    class="mb-4 inline-flex items-center gap-[7px] text-[.68rem] font-bold uppercase tracking-[.12em] text-[#7a9db5] no-underline transition hover:text-[#0086da]">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5" stroke-linecap="square">
                                        <path d="M19 12H5M12 5l-7 7 7 7" />
                                    </svg>
                                    Back to Dashboard
                                </a>
                            @endif
                            <h1 class="text-[1.35rem] font-extrabold leading-[1.1] tracking-[-.02em] text-[#1a2e3b]">Book
                                an Appointment</h1>
                            <p class="mt-1 text-[.8rem] text-[#7a9db5]">Fill in your details, pick a service and time,
                                then confirm.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ══════════════════════════════════
             SUCCESS MODAL
        ══════════════════════════════════ --}}
        @if (session()->has('success'))
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
                <div
                    class="w-full max-w-md bg-white p-10 text-center shadow-[0_32px_80px_rgba(0,74,124,.18)] border border-[#dbeaf7]">
                    <div class="mx-auto mb-5 w-14 h-14 bg-[#e8f4fc] flex items-center justify-center">
                        <svg class="w-7 h-7 text-[#0086da]" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="square" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-[1.1rem] font-extrabold tracking-[-0.02em] text-[#1a2e3b] mb-2">Appointment Booked!
                    </h3>
                    <p class="text-[.85rem] text-[#3d5a6e] leading-relaxed">{{ session('success') }}</p>
                    <button type="button" onclick="this.closest('.fixed')?.remove()"
                        class="mt-7 inline-flex items-center justify-center gap-2 bg-[#0086da] px-8 py-[14px] text-[.72rem] font-bold uppercase tracking-[.1em] text-white transition hover:bg-[#006ab0]">
                        <svg class="w-[13px] h-[13px]" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="square" d="M5 13l4 4L19 7" />
                        </svg>
                        Done
                    </button>
                </div>
            </div>
        @endif

        {{-- ══════════════════════════════════
             MAIN FORM AREA
        ══════════════════════════════════ --}}
        <div class="px-6 md:px-12 xl:px-20 {{ $guestOtpStepActive ? '' : 'py-12 md:py-16' }}">
            <div class="mx-auto max-w-[1400px]">

                <form id="bookingForm">

                    {{-- ── Two-column form ── --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start {{ $guestOtpStepActive ? 'hidden' : '' }}">

                        {{-- ── CARD 1: Patient Details ── --}}
                        <div
                            class="bg-white border border-[#e4eff8] p-7 md:p-10 shadow-[0_20px_48px_rgba(0,134,218,.07)]">

                            {{-- Card header --}}
                            <div class="flex items-center gap-3 mb-8 pb-6 border-b border-[#e4eff8]">
                                <div class="w-8 h-8 bg-[#0086da] flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="square" d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                                        <circle cx="12" cy="7" r="4" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-[.6rem] font-bold uppercase tracking-[.2em] text-[#0086da]">Step 1
                                    </div>
                                    <div class="text-[.95rem] font-extrabold text-[#1a2e3b] tracking-[-0.01em]">Contact
                                        & Patient Details</div>
                                </div>
                            </div>

                            <div class="mb-7 border border-[#dcecf8] bg-[#f8fbfe] p-5">
                                <p class="text-[.62rem] font-bold uppercase tracking-[.18em] text-[#0086da]">Who is this
                                    appointment for?</p>
                                <div class="mt-4 grid gap-3 md:grid-cols-2">
                                    <label
                                        class="cursor-pointer border p-4 transition {{ $booking_for === 'self' ? 'border-[#0086da] bg-[#eef7ff] shadow-[inset_0_0_0_1px_rgba(0,134,218,.12)]' : 'border-[#d4e8f5] bg-white hover:border-[#0086da]' }}">
                                        <input type="radio" wire:model.live="booking_for" value="self" class="sr-only peer">
                                        <div class="flex items-start gap-3">
                                            <span
                                                class="mt-1 flex h-4 w-4 items-center justify-center rounded-full border {{ $booking_for === 'self' ? 'border-[#0086da]' : 'border-[#9fc8e3]' }}">
                                                <span
                                                    class="h-2 w-2 rounded-full {{ $booking_for === 'self' ? 'bg-[#0086da]' : 'bg-transparent' }}"></span>
                                            </span>
                                            <div>
                                                <p class="text-[.8rem] font-bold uppercase tracking-[.14em] text-[#1a2e3b]">For
                                                    Myself</p>
                                                <p class="mt-1 text-[.76rem] leading-relaxed text-[#5d7b8f]">Use my details as
                                                    the patient information for this request.</p>
                                            </div>
                                        </div>
                                    </label>
                                    <label
                                        class="cursor-pointer border p-4 transition {{ $booking_for === 'someone_else' ? 'border-[#0086da] bg-[#eef7ff] shadow-[inset_0_0_0_1px_rgba(0,134,218,.12)]' : 'border-[#d4e8f5] bg-white hover:border-[#0086da]' }}">
                                        <input type="radio" wire:model.live="booking_for" value="someone_else"
                                            class="sr-only peer">
                                        <div class="flex items-start gap-3">
                                            <span
                                                class="mt-1 flex h-4 w-4 items-center justify-center rounded-full border {{ $booking_for === 'someone_else' ? 'border-[#0086da]' : 'border-[#9fc8e3]' }}">
                                                <span
                                                    class="h-2 w-2 rounded-full {{ $booking_for === 'someone_else' ? 'bg-[#0086da]' : 'bg-transparent' }}"></span>
                                            </span>
                                            <div>
                                                <p class="text-[.8rem] font-bold uppercase tracking-[.14em] text-[#1a2e3b]">For
                                                    Someone Else</p>
                                                <p class="mt-1 text-[.76rem] leading-relaxed text-[#5d7b8f]">I am the contact
                                                    person, but the appointment is for another patient.</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                @error('booking_for')
                                    <p class="text-[.75rem] text-red-500 mt-2 validation-error" data-error-for="booking_for">
                                        {{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-5">
                                <p class="text-[.62rem] font-bold uppercase tracking-[.18em] text-[#0086da]">
                                    {{ $booking_for === 'someone_else' ? 'Contact Person' : 'Your Details' }}
                                </p>
                                <p class="mt-1 text-[.77rem] leading-relaxed text-[#6d899b]">
                                    {{ $booking_for === 'someone_else' ? 'We will contact this person about the request and verification.' : 'We will use these details to confirm and update the appointment.' }}
                                </p>
                            </div>

                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">

                                <div>
                                    <label
                                        class="block text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e] mb-2">
                                        {{ $booking_for === 'someone_else' ? 'Contact First Name' : 'First Name' }} <span
                                            class="text-red-500">*</span>
                                    </label>
                                    <input type="text" wire:model.defer="first_name" data-validate-field="first_name"
                                        placeholder="Renz" class="{{ $fieldClass('first_name') }}">
                                    @error('first_name')
                                        <p class="text-[.75rem] text-red-500 mt-1.5 validation-error"
                                            data-error-for="first_name">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label
                                        class="block text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e] mb-2">
                                        {{ $booking_for === 'someone_else' ? 'Contact Middle Name (Optional)' : 'Middle Name (Optional)' }}
                                    </label>
                                    <input type="text" wire:model.defer="middle_name" data-validate-field="middle_name"
                                        placeholder="Santos" class="{{ $fieldClass('middle_name') }}">
                                    @error('middle_name')
                                        <p class="text-[.75rem] text-red-500 mt-1.5 validation-error"
                                            data-error-for="middle_name">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label
                                        class="block text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e] mb-2">
                                        {{ $booking_for === 'someone_else' ? 'Contact Last Name' : 'Last Name' }} <span
                                            class="text-red-500">*</span>
                                    </label>
                                    <input type="text" wire:model.defer="last_name" data-validate-field="last_name"
                                        placeholder="Rosales" class="{{ $fieldClass('last_name') }}">
                                    @error('last_name')
                                        <p class="text-[.75rem] text-red-500 mt-1.5 validation-error"
                                            data-error-for="last_name">{{ $message }}</p>
                                    @enderror
                                </div>

                                @if ($booking_for !== 'someone_else')
                                    <div>
                                        <label
                                            class="block text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e] mb-2">
                                            Birth Date <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date" wire:model.defer="patient_birth_date"
                                            data-validate-field="patient_birth_date" max="{{ now()->toDateString() }}"
                                            class="{{ $fieldClass('patient_birth_date') }}">
                                        @error('patient_birth_date')
                                            <p class="text-[.75rem] text-red-500 mt-1.5 validation-error"
                                                data-error-for="patient_birth_date">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif

                                <div class="min-w-0" data-validate-group="contact_number">
                                    <label
                                        class="block text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e] mb-2">
                                        Contact Number <span class="text-red-500">*</span>
                                    </label>
                                    <div class="flex w-full min-w-0 items-stretch">
                                        <span class="inline-flex shrink-0 items-center px-3 border border-r-0 border-[#d4e8f5] bg-[#f0f8fe] text-[#3d5a6e] text-sm font-semibold select-none whitespace-nowrap {{ $errors->has('contact_number') ? 'border-red-400' : '' }}">+63</span>
                                        <input type="text" inputmode="numeric" maxlength="11" wire:model.defer="contact_number"
                                            data-validate-field="contact_number" placeholder="09XX XXX XXXX"
                                            class="{{ $fieldClass('contact_number') }} min-w-0 w-full flex-1">
                                    </div>
                                    @error('contact_number')
                                        <p class="block w-full text-[.75rem] text-red-500 mt-1.5 break-words validation-error"
                                            data-error-for="contact_number">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2 xl:col-span-3">
                                    <label
                                        class="block text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e] mb-2">
                                        Email Address <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" wire:model.blur="email" data-validate-field="email"
                                        placeholder="sample@gmail.com" class="{{ $fieldClass('email') }}">
                                    @error('email')
                                        <p class="text-[.75rem] text-red-500 mt-1.5 validation-error"
                                            data-error-for="email">{{ $message }}</p>
                                    @enderror
                                </div>

                            </div>

                            @if ($booking_for === 'someone_else')
                                <div class="mt-8 border-t border-[#e4eff8] pt-6">
                                <div class="mb-5">
                                    <p class="text-[.62rem] font-bold uppercase tracking-[.18em] text-[#0086da]">
                                        Patient Details
                                    </p>
                                    <p class="mt-1 text-[.77rem] leading-relaxed text-[#6d899b]">
                                        Tell us who the appointment is actually for so staff can match or create the
                                        correct patient record.
                                    </p>
                                </div>

                                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                                        <div>
                                            <label
                                                class="block text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e] mb-2">
                                                Patient First Name <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" wire:model.defer="patient_first_name"
                                                data-validate-field="patient_first_name" placeholder="Jamie"
                                                class="{{ $fieldClass('patient_first_name') }}">
                                            @error('patient_first_name')
                                                <p class="text-[.75rem] text-red-500 mt-1.5 validation-error"
                                                    data-error-for="patient_first_name">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label
                                                class="block text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e] mb-2">
                                                Patient Middle Name (Optional)
                                            </label>
                                            <input type="text" wire:model.defer="patient_middle_name"
                                                data-validate-field="patient_middle_name" placeholder="Mae"
                                                class="{{ $fieldClass('patient_middle_name') }}">
                                            @error('patient_middle_name')
                                                <p class="text-[.75rem] text-red-500 mt-1.5 validation-error"
                                                    data-error-for="patient_middle_name">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label
                                                class="block text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e] mb-2">
                                                Patient Last Name <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" wire:model.defer="patient_last_name"
                                                data-validate-field="patient_last_name" placeholder="Cruz"
                                                class="{{ $fieldClass('patient_last_name') }}">
                                            @error('patient_last_name')
                                                <p class="text-[.75rem] text-red-500 mt-1.5 validation-error"
                                                    data-error-for="patient_last_name">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label
                                                class="block text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e] mb-2">
                                                Patient Birth Date <span class="text-red-500">*</span>
                                            </label>
                                            <input type="date" wire:model.defer="patient_birth_date"
                                                data-validate-field="patient_birth_date" max="{{ now()->toDateString() }}"
                                                class="{{ $fieldClass('patient_birth_date') }}">
                                            @error('patient_birth_date')
                                                <p class="text-[.75rem] text-red-500 mt-1.5 validation-error"
                                                    data-error-for="patient_birth_date">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label
                                                class="block text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e] mb-2">
                                                Your Relationship to the Patient <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" wire:model.defer="relationship_to_patient"
                                                data-validate-field="relationship_to_patient" placeholder="Mother, spouse, etc"
                                                class="{{ $fieldClass('relationship_to_patient') }}">
                                            @error('relationship_to_patient')
                                                <p class="text-[.75rem] text-red-500 mt-1.5 validation-error"
                                                    data-error-for="relationship_to_patient">{{ $message }}</p>
                                            @enderror
                                        </div>
                                </div>
                                </div>
                            @endif

                            {{-- Footer note --}}
                            <div
                                class="mt-8 pt-6 border-t border-[#e4eff8] flex items-start gap-3 text-[.78rem] text-[#7a9db5] leading-relaxed">
                                <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-[#0086da]/50" fill="none"
                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <path stroke-linecap="square" d="M12 8v4M12 16h.01" />
                                </svg>
                                We only collect the details needed to reserve your slot, identify the patient correctly,
                                and send appointment updates.
                            </div>
                        </div>

                        {{-- ── CARD 2: Select Appointment ── --}}
                        <div
                            class="bg-white border border-[#e4eff8] p-7 md:p-10 shadow-[0_20px_48px_rgba(0,134,218,.07)]">

                            {{-- Card header --}}
                            <div class="flex items-center gap-3 mb-8 pb-6 border-b border-[#e4eff8]">
                                <div class="w-8 h-8 bg-[#0086da] flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        stroke-width="2.5" viewBox="0 0 24 24">
                                        <rect x="3" y="4" width="18" height="18" stroke-linecap="square" />
                                        <path stroke-linecap="square" d="M16 2v4M8 2v4M3 10h18" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-[.6rem] font-bold uppercase tracking-[.2em] text-[#0086da]">Step 2
                                    </div>
                                    <div class="text-[.95rem] font-extrabold text-[#1a2e3b] tracking-[-0.01em]">Select
                                        Appointment</div>
                                </div>
                            </div>

                            {{-- Service --}}
                            <div class="mb-6">
                                <label
                                    class="block text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e] mb-2">
                                    Service <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select wire:model.defer="service_id" data-validate-field="service_id"
                                        class="{{ $selectClass('service_id') }}">
                                        <option value="">Select a service</option>
                                        @foreach ($services as $service)
                                            <option value="{{ $service->id }}">{{ $service->service_name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2">
                                        <svg class="w-3.5 h-3.5 text-[#7a9db5]" fill="none" stroke="currentColor"
                                            stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="square" d="M6 9l6 6 6-6" />
                                        </svg>
                                    </div>
                                </div>
                                @error('service_id')
                                    <p class="text-[.75rem] text-red-500 mt-1.5 validation-error"
                                        data-error-for="service_id">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Date + Time grid --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                {{-- Date picker --}}
                                <div>
                                    <label
                                        class="block text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e] mb-3">
                                        Pick a Date <span class="text-red-500">*</span>
                                    </label>

                                    {{-- Mobile --}}
                                    <div class="md:hidden">
                                        <input type="date" wire:model.live="selectedDate"
                                            data-validate-field="selectedDate" min="{{ now()->toDateString() }}"
                                            class="{{ $fieldClass('selectedDate') }}">
                                        <p class="mt-1.5 text-[.72rem] text-[#7a9db5]">Tap to choose a date.</p>
                                    </div>

                                    {{-- Desktop calendar --}}
                                    <div class="{{ $desktopDatePickerClass }}" data-validate-field="selectedDate"
                                        wire:ignore>
                                        <div class="flex items-center justify-between mb-4">
                                            <button type="button" id="prevMonth"
                                                class="w-8 h-8 flex items-center justify-center border border-[#d4e8f5] text-[#1a2e3b] hover:bg-[#f0f8fe] transition text-sm">
                                                &#8249;
                                            </button>
                                            <span id="monthYear"
                                                class="text-[.72rem] font-bold uppercase tracking-[.18em] text-[#1a2e3b]"></span>
                                            <button type="button" id="nextMonth"
                                                class="w-8 h-8 flex items-center justify-center border border-[#d4e8f5] text-[#1a2e3b] hover:bg-[#f0f8fe] transition text-sm">
                                                &#8250;
                                            </button>
                                        </div>
                                        <div
                                            class="grid grid-cols-7 gap-1 mb-2 text-center text-[10px] font-bold uppercase tracking-wider text-[#7a9db5]">
                                            @foreach (['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'] as $d)
                                                <div>{{ $d }}</div>
                                            @endforeach
                                        </div>
                                        <div id="calendarDays" class="grid grid-cols-7 gap-1"></div>
                                    </div>

                                    @error('selectedDate')
                                        <p class="text-[.75rem] text-red-500 mt-1.5 validation-error"
                                            data-error-for="selectedDate">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Time slots --}}
                                <div>
                                    <label
                                        class="block text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e] mb-3">
                                        Select a Time <span class="text-red-500">*</span>
                                    </label>

                                    <div class="{{ $slotGridClass }}" data-validate-field="selectedSlot"
                                        wire:loading.remove wire:target="selectedDate">
                                        @forelse ($availableSlots as $slot)
                                            @php
                                                $isFull = !empty($slot['is_full']);
                                                $isPast = !empty($slot['is_past']);
                                                $isBlocked = !empty($slot['is_blocked']);
                                                $disabled = $isFull || $isPast || $isBlocked;
                                            @endphp
                                            <label
                                                class="{{ $disabled ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }}">
                                                <input type="radio" name="selectedSlot" wire:model="selectedSlot"
                                                    data-validate-field="selectedSlot" value="{{ $slot['value'] }}"
                                                    @disabled($disabled) class="peer sr-only">
                                                <div
                                                    class="text-center py-2.5 text-[.72rem] font-semibold border transition-all
                                                {{ $disabled
                                                    ? 'border-[#e4eff8] text-[#b2c2cf] bg-[#f8fbfe]'
                                                    : 'border-[#d4e8f5] text-[#1a2e3b] hover:border-[#0086da] hover:bg-[#f0f8fe] peer-checked:border-[#0086da] peer-checked:bg-[#0086da] peer-checked:text-white' }}">
                                                    {{ $slot['time'] }}{{ $isFull ? ' · Full' : '' }}
                                                </div>
                                            </label>
                                        @empty
                                            @php
                                                $placeholders = [
                                                    '09:00 AM',
                                                    '10:00 AM',
                                                    '11:00 AM',
                                                    '12:00 PM',
                                                    '01:00 PM',
                                                    '02:00 PM',
                                                    '03:00 PM',
                                                    '04:00 PM',
                                                    '05:00 PM',
                                                    '06:00 PM',
                                                ];
                                            @endphp
                                            @foreach ($placeholders as $p)
                                                <div
                                                    class="cursor-not-allowed text-center py-2.5 text-[.72rem] font-semibold border border-[#e4eff8] bg-[#f8fbfe] text-[#c5d7e4]">
                                                    {{ $p }}
                                                </div>
                                            @endforeach
                                        @endforelse
                                    </div>

                                    <div wire:loading.flex wire:target="selectedDate"
                                        class="mt-3 min-h-[160px] items-center justify-center">
                                        <svg class="h-6 w-6 animate-spin text-[#0086da]" viewBox="0 0 24 24"
                                            fill="none">
                                            <circle cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-opacity="0.2" stroke-width="4" />
                                            <path d="M22 12a10 10 0 0 1-10 10" stroke="currentColor" stroke-width="4"
                                                stroke-linecap="round" />
                                        </svg>
                                    </div>

                                    @error('selectedSlot')
                                        <p class="text-[.75rem] text-red-500 mt-1.5 validation-error"
                                            data-error-for="selectedSlot">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- reCAPTCHA (guests only) --}}
                            @guest
                                <div class="mt-7" wire:ignore>
                                    <div id="recaptcha-container" class="g-recaptcha"
                                        data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                                </div>
                                <input type="hidden" id="recaptchaToken" wire:ignore>
                                @error('recaptcha')
                                    <p class="text-[.75rem] text-red-500 mt-1.5 validation-error" data-error-for="recaptcha">
                                        {{ $message }}</p>
                                @enderror
                            @endguest

                            <div class="mt-7 border border-[#e4eff8] bg-[#f8fbfe] p-5" data-validate-group="booking_agreement">
                                <label class="flex items-start gap-3 cursor-pointer">
                                    <input type="checkbox" wire:model.defer="booking_agreement"
                                        data-validate-field="booking_agreement"
                                        class="mt-1 h-4 w-4 flex-shrink-0 rounded-none border-[#9fc8e3] text-[#0086da] focus:ring-[#9fc8e3]">
                                    <span class="text-[.8rem] leading-relaxed text-[#3d5a6e]">
                                        I confirm that the details I entered are accurate, and I agree that Tejada
                                        Clinic
                                        may use them to review, confirm, and contact me about this appointment request.
                                    </span>
                                </label>
                                @error('booking_agreement')
                                    <p class="text-[.75rem] text-red-500 mt-3 validation-error"
                                        data-error-for="booking_agreement">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Submit --}}
                            <button type="submit"
                                class="mt-8 inline-flex w-full items-center justify-center gap-2 bg-[#0086da] px-8 py-[15px] text-[.72rem] font-bold uppercase tracking-[.1em] text-white transition hover:-translate-y-px hover:bg-[#006ab0] disabled:cursor-not-allowed disabled:opacity-70"
                                wire:loading.attr="disabled" wire:target="bookAppointment">
                                <span wire:loading.remove wire:target="bookAppointment"
                                    class="inline-flex items-center gap-2">
                                    <svg class="w-[14px] h-[14px]" fill="none" stroke="currentColor"
                                        stroke-width="2.5" viewBox="0 0 24 24">
                                        <rect x="3" y="4" width="18" height="18" stroke-linecap="square" />
                                        <path stroke-linecap="square" d="M16 2v4M8 2v4M3 10h18" />
                                    </svg>
                                    Confirm Appointment
                                </span>
                                <span wire:loading wire:target="bookAppointment"
                                    class="inline-flex items-center justify-center">
                                    Processing...
                                </span>
                            </button>

                        </div>
                    </div>

                    {{-- ── OTP step (guests) ── --}}
                    @guest
                        @if ($guestOtpStepActive)
                            <div
                                class="w-full max-w-xl mx-auto bg-white border border-[#e4eff8] p-8 md:p-12 shadow-[0_20px_48px_rgba(0,134,218,.08)]">

                                {{-- Header --}}
                                <div class="flex items-center gap-3 mb-8 pb-6 border-b border-[#e4eff8]">
                                    <div class="w-8 h-8 bg-[#0086da] flex items-center justify-center flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail-icon lucide-mail"><path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"/><rect x="2" y="4" width="20" height="16" rx="2"/></svg>
                                    </div>
                                    <div>
                                        <div class="text-[.6rem] font-bold uppercase tracking-[.2em] text-[#0086da]">Step 3
                                        </div>
                                        <div class="text-[.95rem] font-extrabold text-[#1a2e3b] tracking-[-0.01em]">Verify
                                            Your Email</div>
                                    </div>
                                </div>

                                {{-- Back to form (top) --}}
                                <div class="mt-5 mb-6">
                                    <button type="button" wire:click="cancelGuestOtpStep" data-single-tap
                                        class="inline-flex items-center gap-[7px] text-[.68rem] font-bold uppercase tracking-[.12em] text-[#7a9db5] transition hover:text-[#0086da]">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="square">
                                            <path d="M19 12H5M12 5l-7 7 7 7" />
                                        </svg>
                                        Back to form
                                    </button>
                                </div>

                                <p class="text-[.88rem] leading-relaxed text-[#3d5a6e] mb-2">
                                    We sent a 6-digit code to <strong class="text-[#1a2e3b]">{{ $email }}</strong>.
                                    Enter it below to complete your booking.
                                </p>

                                @if (session()->has('otp_success'))
                                    <p class="mt-3 text-[.82rem] font-semibold text-emerald-600">
                                        {{ session('otp_success') }}</p>
                                @endif

                                {{-- Hidden data panel for JS countdown --}}
                                <div id="otpStatusPanel" class="hidden"
                                    data-expires-at="{{ $guestEmailOtpExpiresAt }}"
                                    data-cooldown-until="{{ $guestEmailOtpCooldownUntil }}"
                                    data-lock-until="{{ $guestEmailOtpResendLockedUntil }}"
                                    data-resends-remaining="{{ $this->guestOtpResendsRemaining }}">
                                </div>

                                {{-- OTP input + verify --}}
                                <div class="mt-6 flex flex-col sm:flex-row gap-4 sm:items-end">
                                    <div class="flex-1">
                                        <label
                                            class="block text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e] mb-2">One-Time
                                            Code</label>
                                        <input type="text" inputmode="numeric" maxlength="6"
                                            wire:model.defer="guestEmailOtp" data-validate-field="guestEmailOtp"
                                            placeholder="• • • • • •"
                                            class="{{ $fieldClass('guestEmailOtp') }} text-center text-lg tracking-[.4em] h-13">
                                    </div>
                                    <button type="button" wire:click="verifyGuestEmailOtp" wire:loading.attr="disabled"
                                        data-single-tap wire:target="verifyGuestEmailOtp"
                                        class="relative inline-flex h-[50px] items-center justify-center bg-[#0086da] px-7 text-[.72rem] font-bold uppercase tracking-[.1em] text-white transition hover:bg-[#006ab0] disabled:opacity-60 disabled:cursor-not-allowed">
                                        <span wire:loading.remove wire:target="verifyGuestEmailOtp">Verify & Book</span>
                                        <span wire:loading wire:target="verifyGuestEmailOtp"
                                            class="absolute inset-0 flex items-center justify-center gap-2 bg-[#0086da] px-7">
                                            <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                                <circle cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-opacity="0.2" stroke-width="4" />
                                                <path d="M22 12a10 10 0 0 1-10 10" stroke="currentColor" stroke-width="4"
                                                    stroke-linecap="round" />
                                            </svg>
                                            Verifying...
                                        </span>
                                    </button>
                                </div>

                                @error('guestEmailOtp')
                                    <p class="text-[.75rem] text-red-500 mt-2 validation-error"
                                        data-error-for="guestEmailOtp">{{ $message }}</p>
                                @enderror

                                {{-- Expiry line + Resend button (countdown embedded in label) --}}
                                <div class="mt-6">
                                    <p id="otpExpiryText" class="mb-3 text-[.75rem] text-[#7a9db5]">Code expires in --:--</p>
                                    <button type="button" id="otpResendButton" wire:click="resendGuestEmailOtp"
                                        wire:loading.attr="disabled" data-single-tap wire:target="resendGuestEmailOtp"
                                        class="inline-flex h-11 items-center justify-center border border-[#0086da] bg-white px-5 text-[.72rem] font-bold uppercase tracking-[.1em] text-[#0086da] transition hover:bg-[#0086da] hover:text-white disabled:opacity-60 disabled:cursor-not-allowed">
                                        <span wire:loading.remove wire:target="resendGuestEmailOtp" id="otpResendLabel">Resend OTP</span>
                                        <span wire:loading wire:target="resendGuestEmailOtp">Sending…</span>
                                    </button>
                                </div>

                            </div>
                        @endif
                    @endguest

                </form>
            </div>
        </div>

    </section>

    {{-- ══════════════════════════════════
         SCRIPTS (unchanged logic)
    ══════════════════════════════════ --}}
    <script>
        let currentDate = new Date();
        let selectedDate = @js($selectedDate);
        let uiTickerStarted = false;

        const selectedDayClass = 'bg-[#0086da] text-white border-[#0086da]';
        const activeDayClass = 'bg-white text-[#1a2e3b] border-[#e4eff8] hover:bg-[#f0f8fe] hover:border-[#0086da]';
        const disabledDayClass = 'bg-[#f8fbfe] text-[#c5d7e4] border-[#f0f4f8] cursor-not-allowed';

        function formatDateLocal(date) {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        }

        function syncCalendarToSelectedDate() {
            if (!selectedDate) return;
            const parsed = new Date(`${selectedDate}T00:00:00`);
            if (!Number.isNaN(parsed.getTime())) {
                currentDate = new Date(parsed.getFullYear(), parsed.getMonth(), 1);
            }
        }

        function renderCalendar() {
            const monthYearEl = document.getElementById('monthYear');
            const calendarDays = document.getElementById('calendarDays');
            if (!monthYearEl || !calendarDays) return;

            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            const today = new Date();
            const todayStr = formatDateLocal(today);
            const isPastMonth = year < today.getFullYear() || (year === today.getFullYear() && month < today.getMonth());
            const isFutureMonth = year > today.getFullYear() || (year === today.getFullYear() && month > today.getMonth());

            monthYearEl.textContent = currentDate.toLocaleDateString('en-US', {
                month: 'long',
                year: 'numeric'
            }).toUpperCase();

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            calendarDays.innerHTML = '';

            for (let i = 0; i < firstDay; i++) calendarDays.innerHTML += '<div></div>';

            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month, day);
                const dateStr = formatDateLocal(date);
                const isPast = isPastMonth || (!isFutureMonth && dateStr < todayStr);

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.textContent = day;
                btn.disabled = isPast;
                btn.className = `w-full aspect-square flex items-center justify-center text-[.72rem] font-semibold border transition
                    ${isPast ? disabledDayClass : selectedDate === dateStr ? selectedDayClass : activeDayClass}`;

                btn.addEventListener('click', () => {
                    if (isPast) return;
                    selectedDate = dateStr;
                    @this.set('selectedDate', dateStr);
                    dismissValidationFor('selectedDate');
                    renderCalendar();
                });
                calendarDays.appendChild(btn);
            }
        }

        function bindCalendarNavigation() {
            const prev = document.getElementById('prevMonth');
            const next = document.getElementById('nextMonth');
            if (prev && !prev.dataset.bound) {
                prev.dataset.bound = '1';
                prev.addEventListener('click', () => {
                    currentDate.setMonth(currentDate.getMonth() - 1);
                    renderCalendar();
                });
            }
            if (next && !next.dataset.bound) {
                next.dataset.bound = '1';
                next.addEventListener('click', () => {
                    currentDate.setMonth(currentDate.getMonth() + 1);
                    renderCalendar();
                });
            }
        }

        let recaptchaWidgetId = null;
        let bookingSubmitInFlight = false;

        async function syncRecaptchaToken(token = '') {
            const input = document.getElementById('recaptchaToken');
            if (input) input.value = token;
            await @this.set('recaptchaToken', token);
            if (token) dismissValidationFor('recaptcha');
        }

        function getRecaptchaApi() {
            if (typeof grecaptcha === 'undefined' || grecaptcha === null) return null;
            if (typeof grecaptcha.render === 'function' && typeof grecaptcha.getResponse === 'function') {
                return grecaptcha;
            }
            if (grecaptcha.enterprise && typeof grecaptcha.enterprise.render === 'function' && typeof grecaptcha
                .enterprise.getResponse === 'function') {
                return grecaptcha.enterprise;
            }
            return null;
        }

        function ensureRecaptcha() {
            const recaptchaApi = getRecaptchaApi();
            if (!recaptchaApi) return;
            const container = document.getElementById('recaptcha-container');
            if (!container || container.querySelector('iframe') || container.dataset.rendered) return;
            recaptchaWidgetId = recaptchaApi.render(container, {
                sitekey: container.getAttribute('data-sitekey'),
                callback: (t) => {
                    void syncRecaptchaToken(t);
                },
                'expired-callback': () => {
                    void syncRecaptchaToken('');
                },
                'error-callback': () => {
                    void syncRecaptchaToken('');
                },
            });
            container.dataset.rendered = 'true';
        }

        async function setRecaptchaToken() {
            const recaptchaApi = getRecaptchaApi();
            if (!recaptchaApi) return;
            const token = recaptchaWidgetId !== null ? recaptchaApi.getResponse(recaptchaWidgetId) : recaptchaApi
                .getResponse();
            await syncRecaptchaToken(token);
        }

        async function syncBookingFormStateToLivewire() {
            const syncFieldValue = async (key, selector, transform = (value) => value) => {
                const element = document.querySelector(selector);
                if (!element) return;
                await @this.set(key, transform(element.value));
            };

            await syncFieldValue('first_name', '[data-validate-field="first_name"]', value => value.trim());
            await syncFieldValue('last_name', '[data-validate-field="last_name"]', value => value.trim());
            await syncFieldValue('patient_birth_date', '[data-validate-field="patient_birth_date"]', value => value.trim());
            await syncFieldValue('contact_number', '[data-validate-field="contact_number"]', value => value.trim());
            await syncFieldValue('email', '[data-validate-field="email"]', value => value.trim());
            await syncFieldValue('patient_first_name', '[data-validate-field="patient_first_name"]', value => value.trim());
            await syncFieldValue('patient_last_name', '[data-validate-field="patient_last_name"]', value => value.trim());
            await syncFieldValue('relationship_to_patient', '[data-validate-field="relationship_to_patient"]', value => value
                .trim());
            await syncFieldValue('service_id', '[data-validate-field="service_id"]');

            const bookingForInput = document.querySelector('input[name="booking_for"]:checked');
            if (bookingForInput) {
                await @this.set('booking_for', bookingForInput.value);
            }

            const selectedSlotInput = document.querySelector('input[name="selectedSlot"]:checked');
            if (selectedSlotInput) {
                await @this.set('selectedSlot', selectedSlotInput.value);
            }

            const agreementInput = document.querySelector('input[data-validate-field="booking_agreement"]');
            if (agreementInput) {
                await @this.set('booking_agreement', !!agreementInput.checked);
            }
        }

        async function submitBookingForm() {
            if (bookingSubmitInFlight) return;
            bookingSubmitInFlight = true;
            try {
                if (document.getElementById('recaptcha-container')) await setRecaptchaToken();
                await syncBookingFormStateToLivewire();
                await @this.bookAppointment();
            } finally {
                bookingSubmitInFlight = false;
            }
        }

        function formatCountdown(secs) {
            const s = Math.max(0, Number(secs) || 0);
            return `${String(Math.floor(s / 60)).padStart(2,'0')}:${String(s % 60).padStart(2,'0')}`;
        }

        function secondsUntil(ts) {
            if (!ts) return 0;
            const end = new Date(ts.replace(' ', 'T'));
            return Number.isNaN(end.getTime()) ? 0 : Math.max(0, Math.ceil((end.getTime() - Date.now()) / 1000));
        }

        function updateOtpCountdownUi() {
            const panel = document.getElementById('otpStatusPanel');
            if (!panel) return;
            const expiryText = document.getElementById('otpExpiryText');
            const resendLabel = document.getElementById('otpResendLabel');
            const resendBtn = document.getElementById('otpResendButton');
            const expSecs = secondsUntil(panel.dataset.expiresAt || '');
            const coolSecs = secondsUntil(panel.dataset.cooldownUntil || '');
            const lockSecs = secondsUntil(panel.dataset.lockUntil || '');
            const remaining = Number(panel.dataset.resendsRemaining || 0);
            const effectiveRemaining = lockSecs <= 0 && remaining < 1 && panel.dataset.lockUntil ? 3 : remaining;

            if (expiryText) {
                expiryText.textContent = expSecs > 0
                    ? `Code expires in ${formatCountdown(expSecs)}`
                    : 'Code expired. Request a new OTP.';
            }
            if (resendLabel) {
                if (lockSecs > 0) {
                    resendLabel.textContent = `Resend OTP locked (${formatCountdown(lockSecs)})`;
                } else if (effectiveRemaining < 1) {
                    resendLabel.textContent = 'Resend OTP (0/3)';
                } else if (coolSecs > 0) {
                    resendLabel.textContent = `Resend OTP (${formatCountdown(coolSecs)})`;
                } else {
                    resendLabel.textContent = `Resend OTP (${effectiveRemaining}/3)`;
                }
            }
            if (resendBtn) resendBtn.disabled = lockSecs > 0 || coolSecs > 0 || effectiveRemaining < 1 || resendBtn.dataset.tapLocked === '1';
        }

        function bindSingleTapGuards() {
            document.querySelectorAll('[data-single-tap]').forEach(btn => {
                if (btn.dataset.singleTapBound === '1') return;
                btn.dataset.singleTapBound = '1';
                btn.addEventListener('click', e => {
                    if (btn.dataset.tapLocked === '1') {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        return;
                    }
                    btn.dataset.tapLocked = '1';
                    btn.disabled = true;
                    window.setTimeout(() => {
                        if (!document.body.contains(btn)) return;
                        btn.dataset.tapLocked = '0';
                        updateOtpCountdownUi();
                    }, 1200);
                });
            });
        }

        function startUiTicker() {
            if (uiTickerStarted) return;
            uiTickerStarted = true;
            window.setInterval(() => {
                bindSingleTapGuards();
                updateOtpCountdownUi();
            }, 1000);
        }

        function dismissValidationFor(key) {
            const err = document.querySelector(`.validation-error[data-error-for="${key}"]`);
            if (err) err.classList.add('hidden');
            document.querySelectorAll(`[data-validate-field="${key}"]`).forEach(el => {
                el.classList.remove('border-red-400', 'border-red-500', 'focus:border-red-500',
                    'focus:ring-red-200');
                el.classList.add('border-[#d4e8f5]', 'focus:border-[#0086da]', 'focus:ring-[#cde8fb]');
            });
            document.querySelectorAll(`[data-validate-group="${key}"]`).forEach(el => {
                el.classList.remove('border-red-400');
                el.classList.add('border-[#e4eff8]');
            });
        }

        function showValidationFor(key, message) {
            document.querySelectorAll(`[data-validate-field="${key}"]`).forEach(el => {
                el.classList.remove('border-[#d4e8f5]', 'focus:border-[#0086da]', 'focus:ring-[#cde8fb]');
                el.classList.add('border-red-400', 'focus:border-red-500', 'focus:ring-red-200');
            });
            document.querySelectorAll(`[data-validate-group="${key}"]`).forEach(el => {
                el.classList.remove('border-[#e4eff8]');
                el.classList.add('border-red-400');
            });

            let err = document.querySelector(`.validation-error[data-error-for="${key}"]`);
            if (!err) {
                const target = document.querySelector(`[data-validate-group="${key}"]`) || document.querySelector(
                    `[data-validate-field="${key}"]`);
                if (!target) return;
                err = document.createElement('p');
                err.className = 'block w-full mt-1.5 break-words text-[.75rem] text-red-500 validation-error';
                err.dataset.errorFor = key;
                // If target is a container div, append inside so error renders below children
                if (target.tagName === 'DIV') {
                    target.appendChild(err);
                } else {
                    target.insertAdjacentElement('afterend', err);
                }
            }

            err.textContent = message;
            err.classList.remove('hidden');
        }

        function isElementVisible(element) {
            if (!element) return false;
            return !!(element.offsetWidth || element.offsetHeight || element.getClientRects().length);
        }

        function getFieldElements(key) {
            return Array.from(document.querySelectorAll(`[data-validate-field="${key}"]`))
                .filter(isElementVisible);
        }

        function validateBookingFormBeforeSubmit() {
            const validators = [{
                    key: 'first_name',
                    message: 'Please enter the first name.',
                    validate: () => (document.querySelector('[data-validate-field="first_name"]')?.value || '').trim() !== '',
                },
                {
                    key: 'last_name',
                    message: 'Please enter the last name.',
                    validate: () => (document.querySelector('[data-validate-field="last_name"]')?.value || '').trim() !== '',
                },
                {
                    key: 'patient_birth_date',
                    message: 'Please provide the patient birth date.',
                    validate: () => {
                        const value = (document.querySelector('[data-validate-field="patient_birth_date"]')?.value || '').trim();
                        if (value === '') return false;
                        return value <= new Date().toISOString().slice(0, 10);
                    },
                },
                {
                    key: 'contact_number',
                    message: 'Contact number must be exactly 11 digits.',
                    validate: () => /^\d{11}$/.test((document.querySelector('[data-validate-field="contact_number"]')?.value || '').trim()),
                },
                {
                    key: 'email',
                    message: 'Please enter a valid email address.',
                    validate: () => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test((document.querySelector('[data-validate-field="email"]')?.value || '').trim()),
                },
                {
                    key: 'patient_first_name',
                    message: 'Please enter the patient first name.',
                    validate: () => (document.querySelector('[data-validate-field="patient_first_name"]')?.value || '').trim() !== '',
                },
                {
                    key: 'patient_last_name',
                    message: 'Please enter the patient last name.',
                    validate: () => (document.querySelector('[data-validate-field="patient_last_name"]')?.value || '').trim() !== '',
                },
                {
                    key: 'relationship_to_patient',
                    message: 'Please tell us your relationship to the patient.',
                    validate: () => (document.querySelector('[data-validate-field="relationship_to_patient"]')?.value || '').trim() !== '',
                },
                {
                    key: 'service_id',
                    message: 'Please select a service.',
                    validate: () => (document.querySelector('[data-validate-field="service_id"]')?.value || '').trim() !== '',
                },
                {
                    key: 'selectedDate',
                    message: 'Please pick a date.',
                    validate: () => (selectedDate || document.querySelector('[data-validate-field="selectedDate"]')?.value || '').trim() !== '',
                },
                {
                    key: 'selectedSlot',
                    message: 'Please select a time slot.',
                    validate: () => document.querySelector('input[name="selectedSlot"]:checked') !== null,
                },
                {
                    key: 'booking_agreement',
                    message: 'Please confirm the booking agreement before submitting your request.',
                    validate: () => !!document.querySelector('[data-validate-field="booking_agreement"]')?.checked,
                },
                {
                    key: 'recaptcha',
                    message: 'Please complete the CAPTCHA verification.',
                    validate: () => {
                        if (!document.getElementById('recaptcha-container')) return true;
                        const recaptchaApi = getRecaptchaApi();
                        const widgetResponse = recaptchaApi ? (recaptchaWidgetId !== null ? recaptchaApi.getResponse(
                            recaptchaWidgetId) : recaptchaApi.getResponse()) : '';
                        return ((document.getElementById('recaptchaToken')?.value || '').trim() !== '') || (
                            (widgetResponse || '').trim() !== '');
                    },
                }
            ];

            let firstInvalidKey = null;

            validators.forEach((validator) => {
                const fieldElements = getFieldElements(validator.key);
                if (fieldElements.length === 0 && validator.key !== 'selectedSlot' && validator.key !== 'recaptcha') {
                    return;
                }

                if (validator.validate()) {
                    dismissValidationFor(validator.key);
                    return;
                }

                showValidationFor(validator.key, validator.message);
                if (!firstInvalidKey) {
                    firstInvalidKey = validator.key;
                }
            });

            if (firstInvalidKey) {
                const target = document.querySelector(`[data-validate-field="${firstInvalidKey}"]`);
                target?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }

            return firstInvalidKey === null;
        }

        function bindValidationDismissal() {
            const form = document.getElementById('bookingForm');
            if (!form || form.dataset.validationBound === '1') return;
            form.dataset.validationBound = '1';
            form.addEventListener('input', e => {
                const k = e.target?.getAttribute?.('data-validate-field');
                if (k) dismissValidationFor(k);
            });
            form.addEventListener('change', e => {
                if (e.target instanceof HTMLInputElement && e.target.type === 'radio' && e.target.name ===
                    'selectedSlot') {
                    dismissValidationFor('selectedSlot');
                    return;
                }
                const k = e.target?.getAttribute?.('data-validate-field')
                    || e.target?.closest?.('[data-validate-field]')?.getAttribute?.('data-validate-field');
                if (k) dismissValidationFor(k);
            });
            form.addEventListener('submit', e => {
                e.preventDefault();
                if (!validateBookingFormBeforeSubmit()) return;
                void submitBookingForm();
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            syncCalendarToSelectedDate();
            bindCalendarNavigation();
            renderCalendar();
            ensureRecaptcha();
            bindValidationDismissal();
            bindSingleTapGuards();
            updateOtpCountdownUi();
            startUiTicker();
        });

        document.addEventListener('reset-recaptcha', () => {
            const recaptchaApi = getRecaptchaApi();
            if (typeof recaptchaApi?.reset === 'function') {
                try {
                    recaptchaWidgetId !== null ? recaptchaApi.reset(recaptchaWidgetId) : recaptchaApi.reset();
                } catch {}
            }
            void syncRecaptchaToken('');
        });

        document.addEventListener('book-calendar-refresh', e => {
            selectedDate = e.detail?.selectedDate || null;
            syncCalendarToSelectedDate();
            bindCalendarNavigation();
            renderCalendar();
            ensureRecaptcha();
            bindValidationDismissal();
            bindSingleTapGuards();
            updateOtpCountdownUi();
        });
    </script>
</div>
