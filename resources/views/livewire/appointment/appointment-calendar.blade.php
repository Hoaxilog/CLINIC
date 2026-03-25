<div class="relative" x-data="{
    serverModalOpen: @entangle('showAppointmentModal').live,
    modalOpen: false,
    openingPatientForm: false,
    resumeAppointmentModalOnPatientFormClose: false,
    blockedToast: false,
    blockedMessage: '',
    blockedTimer: null,
    init() {
        this.modalOpen = this.serverModalOpen;
        this.$watch('serverModalOpen', value => {
            this.modalOpen = value;
        });
    },
    showBlocked(date, time) {
        const cleanTime = (time || '').toString().slice(0, 5);
        this.blockedMessage = `The ${date} ${cleanTime} slot is blocked and cannot be booked.`;
        this.blockedToast = true;
        if (this.blockedTimer) {
            clearTimeout(this.blockedTimer);
        }
        this.blockedTimer = setTimeout(() => {
            this.blockedToast = false;
        }, 1800);
    }
}"
    x-init="init()"
    x-on:patient-form-opened.window="openingPatientForm = false; modalOpen = false"
    x-on:patient-form-closed.window="openingPatientForm = false; if (resumeAppointmentModalOnPatientFormClose) { modalOpen = true; resumeAppointmentModalOnPatientFormClose = false }"
    x-on:patient-form-open-failed.window="openingPatientForm = false; resumeAppointmentModalOnPatientFormClose = false">
    @php
        $btnBase =
            'inline-flex items-center justify-center font-semibold transition focus:outline-none focus:ring-2 focus:ring-offset-1 disabled:opacity-60 disabled:cursor-not-allowed';
        $btnSm = $btnBase . ' px-3.5 py-2 text-xs';
        $btnMd = $btnBase . ' px-4 py-2 text-sm';
        $btnLg = $btnBase . ' px-6 py-2.5 text-sm';
        $btnIcon =
            'inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-[#587189] transition hover:bg-[#f6fafd] focus:outline-none';

        $btnPrimary = 'bg-[#0086da] text-white hover:bg-[#006ab0]';
        $btnSecondary = 'border border-[#e4eff8] bg-white text-[#587189] hover:bg-[#f6fafd]';
        $btnOutlinePrimary = 'border border-[#0086da] text-[#0086da] hover:bg-[#0086da] hover:text-white';
        $btnDanger = 'bg-rose-600 text-white hover:bg-rose-700';
        $btnDangerSoft = 'border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100';
        $btnSuccess = 'bg-[#0086da] text-white hover:bg-[#006ab0]';
        $btnWarning = 'bg-[#0086da] text-white hover:bg-[#006ab0]';
        $btnComplete = 'bg-[#0086da] text-white hover:bg-[#006ab0]';
        $btnInfo = 'bg-[#0086da] text-white hover:bg-[#006ab0]';
    @endphp

    <div class="w-full max-w-9xl mx-auto px-2 py-6 lg:px-8 bg-white">
        <div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-end">
            @if ($activeTab === 'calendar')
                <div class="flex flex-wrap items-center justify-end gap-2 md:flex-nowrap">
                    <button type="button" wire:click="previousWeek" class="{{ $btnMd }} {{ $btnSecondary }}"
                        aria-label="Previous week">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-arrow-big-right-icon lucide-arrow-big-right h-4 w-4 rotate-180">
                            <path
                                d="M11 9a1 1 0 0 0 1-1V5.061a1 1 0 0 1 1.811-.75l6.836 6.836a1.207 1.207 0 0 1 0 1.707l-6.836 6.835a1 1 0 0 1-1.811-.75V16a1 1 0 0 0-1-1H5a1 1 0 0 1-1-1v-4a1 1 0 0 1 1-1z" />
                        </svg>
                    </button>

                    <button type="button"
                        @click="$refs.calendarDatePicker.showPicker ? $refs.calendarDatePicker.showPicker() : $refs.calendarDatePicker.click()"
                        class="{{ $btnMd }} {{ $btnSecondary }} gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-calendar-days-icon lucide-calendar-days h-4 w-4">
                            <path d="M8 2v4" />
                            <path d="M16 2v4" />
                            <rect width="18" height="18" x="3" y="4" rx="2" />
                            <path d="M3 10h18" />
                            <path d="M8 14h.01" />
                            <path d="M12 14h.01" />
                            <path d="M16 14h.01" />
                            <path d="M8 18h.01" />
                            <path d="M12 18h.01" />
                            <path d="M16 18h.01" />
                        </svg>
                        <span>{{ \Carbon\Carbon::parse($selectedDate)->format('M d, Y') }}</span>
                    </button>

                    <input type="date" x-ref="calendarDatePicker" wire:model.live="selectedDate"
                        wire:change="goToDate" min="{{ now()->subYear()->format('Y-m-d') }}"
                        max="{{ now()->addYears(3)->format('Y-m-d') }}" class="sr-only">

                    <button type="button" wire:click="nextWeek" class="{{ $btnMd }} {{ $btnSecondary }}"
                        aria-label="Next week">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-arrow-big-right-icon lucide-arrow-big-right h-4 w-4">
                            <path
                                d="M11 9a1 1 0 0 0 1-1V5.061a1 1 0 0 1 1.811-.75l6.836 6.836a1.207 1.207 0 0 1 0 1.707l-6.836 6.835a1 1 0 0 1-1.811-.75V16a1 1 0 0 0-1-1H5a1 1 0 0 1-1-1v-4a1 1 0 0 1 1-1z" />
                        </svg>
                    </button>

                    <button type="button" wire:click="goToToday" class="{{ $btnMd }} {{ $btnSecondary }}">
                        Today
                    </button>

                    <button type="button" wire:click="toggleBlockMode"
                        class="{{ $btnMd }} {{ $isBlockMode ? 'border border-rose-700 bg-rose-700 text-white hover:bg-rose-800' : $btnDanger }}">
                        {{ $isBlockMode ? 'Cancel Block' : 'Block Time' }}
                    </button>
                </div>
            @endif
        </div>

        @if ($prefillPatientId && $prefillPatientLabel)
            <div
                class="mb-5 flex flex-wrap items-center gap-3 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                <span class="font-semibold">Adding appointment for:</span>
                <span class="font-medium text-blue-900">{{ $prefillPatientLabel }}</span>
                <button type="button" wire:click="clearPrefill"
                    class="ml-auto {{ $btnSm }} border border-blue-200 bg-white text-blue-700 hover:bg-blue-100">
                    Clear
                </button>
            </div>
        @endif

        @include('livewire.appointment.partials.pending-approvals')
        @include('livewire.appointment.partials.calendar-grid')
    </div>

    @include('livewire.appointment.partials.appointment-modal')

    <livewire:patient.form.patient-form-modal />

    <div x-cloak x-show="blockedToast" x-transition.opacity.duration.120ms
        class="fixed bottom-6 right-6 z-[75] w-full max-w-md rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800 shadow-lg">
        <span x-text="blockedMessage"></span>
    </div>

    {{-- Loading overlay for page-wide Livewire operations --}}
    <div wire:loading.flex wire:target="previousWeek,nextWeek,goToToday,goToDate,toggleBlockMode,blockSlot,unblockSlot,clearPrefill"
        class="fixed inset-0 z-[60] items-center justify-center bg-white/60 backdrop-blur-[2px]">
        <div class="flex flex-col items-center gap-3">
            <div class="h-10 w-10 animate-spin rounded-full border-4 border-blue-200 border-t-[#0086da]"></div>
            <div class="text-sm font-semibold text-gray-700">Loading...</div>
        </div>
    </div>
    @include('components.flash-toast')
</div>
