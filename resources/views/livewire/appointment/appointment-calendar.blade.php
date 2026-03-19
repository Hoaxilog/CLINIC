<div class="relative" x-data="{
    modalOpen: @entangle('showAppointmentModal').defer,
    openingPatientForm: false,
    resumeAppointmentModalOnPatientFormClose: false,
    blockedToast: false,
    blockedMessage: '',
    blockedTimer: null,
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
}" x-on:appointment-modal-closed.window="modalOpen = false"
    x-on:appointment-modal-opened.window="modalOpen = true"
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

        @if ($activeTab === 'pending' && auth()->user()->role !== 3)
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-[#f7fbff] to-white">
                    <div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Pending Approvals</h2>
                            <p class="text-xs text-gray-500">Review and approve appointment requests.</p>
                        </div>
                    </div>
                </div>

                <div
                    class="hidden md:grid grid-cols-5 gap-2 px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 bg-gray-50 border-b border-gray-100">
                    <div>Date & Time</div>
                    <div>Patient</div>
                    <div>Service</div>
                    <div>Contact</div>
                    <div class="text-right">Actions</div>
                </div>

                <div class="max-h-[calc(100vh-16rem)] overflow-y-auto divide-y divide-gray-100">
                    @forelse($this->getPendingApprovals() as $pending)
                        <div wire:key="pending-appointment-{{ $pending->id }}"
                            class="grid grid-cols-1 md:grid-cols-5 gap-3 px-5 py-4 text-sm items-center hover:bg-gray-50 transition">
                            <div>
                                <div class="font-semibold text-gray-900">
                                    {{ \Carbon\Carbon::parse($pending->appointment_date)->format('M d, Y') }}</div>
                                <div class="text-gray-500">
                                    {{ \Carbon\Carbon::parse($pending->appointment_date)->format('h:i A') }}</div>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">
                                    {{ $pending->last_name }}, {{ $pending->first_name }}
                                </div>
                                @if ($this->appointmentPatientBirthDateDisplay($pending))
                                    <div class="text-xs text-gray-500">
                                        Birth date: {{ $this->appointmentPatientBirthDateDisplay($pending) }}
                                    </div>
                                @endif
                                @if ($this->appointmentHasSeparateRequester($pending))
                                    <div class="mt-1 text-xs text-blue-700">
                                        Booked by {{ $this->appointmentRequesterDisplayName($pending) ?: 'Requester' }}
                                        @if ($this->appointmentRequesterRelationshipLabel($pending))
                                            ({{ $this->appointmentRequesterRelationshipLabel($pending) }})
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="text-gray-700">{{ $pending->service_name }}</div>
                            <div class="text-gray-600">
                                <div>{{ $pending->requester_contact_number ?? $pending->mobile_number ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-400">
                                    {{ $pending->requester_email ?? $pending->email_address ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="flex md:justify-end gap-2">
                                <button type="button" @click="modalOpen = true"
                                    wire:click="viewAppointment({{ $pending->id }})" wire:loading.attr="disabled"
                                    wire:target="viewAppointment({{ $pending->id }})"
                                    class="{{ $btnSm }} {{ $btnPrimary }}">
                                    Review
                                </button>
                                <button type="button" @click="modalOpen = true"
                                    wire:click="viewAppointment({{ $pending->id }})" wire:loading.attr="disabled"
                                    wire:target="viewAppointment({{ $pending->id }})"
                                    class="{{ $btnSm }} {{ $btnOutlinePrimary }}">
                                    Reschedule
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-sm text-gray-500">No pending approvals.</div>
                    @endforelse
                </div>
            </div>
        @endif

        @if ($activeTab === 'calendar')
            @if ($isBlockMode)
                <div
                    class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800">
                    Block mode is active. Select one available slot to block.
                </div>
            @endif
            <div class="relative">
                <div
                    class="grid grid-cols-[100px_repeat(7,minmax(0,1fr))] lg:grid-cols-[120px_repeat(7,minmax(0,1fr))] border-t border-gray-200 sticky top-14 w-full bg-white z-20 shadow-sm">
                    <div class="p-3.5 flex items-center justify-center text-sm font-medium text-gray-900">
                    </div>
                    @foreach ($weekDates as $date)
                        <div wire:key="calendar-day-header-{{ $date->format('Y-m-d') }}"
                            class="p-3.5 flex flex-col items-center justify-center border-r border-b border-gray-200  {{ $date->isToday() ? 'bg-[#0086da] text-white' : '' }}">
                            <span
                                class="text-sm lg:text-base font-medium {{ $date->isToday() ? ' text-white' : 'text-gray-500' }} mb-1">{{ $date->format('D') }}</span>
                            <span class="text-base lg:text-lg font-medium ">
                                {{ $date->format('M j') }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="sm:hidden space-y-4 pt-4">
                    @foreach ($weekDates as $date)
                        @php
                            $mobileDayAppointments = $this->getAppointmentsForDay($date)->sortBy('start_time');
                        @endphp
                        <div wire:key="calendar-mobile-day-{{ $date->format('Y-m-d') }}"
                            class="rounded-xl border border-gray-200 bg-white p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        {{ $date->format('D') }}
                                    </p>
                                    <p class="text-base font-bold text-gray-900">
                                        {{ $date->format('F j, Y') }}
                                    </p>
                                </div>
                                @if ($date->isToday())
                                    <span
                                        class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700">Today</span>
                                @endif
                            </div>

                            <div class="space-y-2">
                                @forelse ($mobileDayAppointments as $appt)
                                    <button type="button" wire:key="mobile-appointment-{{ $appt->id }}"
                                        @click="modalOpen = true" wire:click="viewAppointment({{ $appt->id }})"
                                        class="w-full rounded-xl border border-blue-100 bg-blue-50 px-4 py-3.5 text-left">
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="text-base font-semibold text-gray-900 truncate">
                                                {{ $appt->last_name }}, {{ $appt->first_name }}
                                            </p>
                                            <p class="text-sm font-semibold text-blue-700 whitespace-nowrap">
                                                {{ \Carbon\Carbon::parse($appt->start_time)->format('h:i A') }}
                                            </p>
                                        </div>
                                        <p class="text-sm text-gray-700 truncate mb-1">{{ $appt->service_name }}</p>
                                        <p
                                            class="text-sm font-semibold
                                            @if ($appt->status == 'Ongoing') text-yellow-700
                                            @elseif($appt->status == 'Scheduled') text-blue-700
                                            @elseif($appt->status == 'Cancelled') text-red-700
                                            @elseif($appt->status == 'Waiting') text-orange-700
                                            @elseif($appt->status == 'Completed') text-green-700
                                            @else text-gray-700 @endif">
                                            {{ $appt->status === 'Waiting' ? 'Ready' : $appt->status }}
                                        </p>
                                    </button>
                                @empty
                                    <p
                                        class="rounded-lg border border-dashed border-gray-200 px-3 py-2 text-xs text-gray-500">
                                        No appointments yet.
                                    </p>
                                @endforelse
                            </div>

                            <div class="mt-4">
                                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    {{ $isBlockMode ? 'Tap a time to block' : 'Tap a time to book' }}
                                </p>
                                <div class="grid grid-cols-3 gap-2">
                                    @foreach ($timeSlots as $time)
                                        @php
                                            $mobileBlockedSlot = $this->getBlockedSlotAt($date->toDateString(), $time);
                                            $mobileIsBlocked = $mobileBlockedSlot !== null;
                                            $mobileIsOccupied = $this->isSlotOccupied($date->toDateString(), $time);
                                            $mobileHasAppointments = $this->hasAppointmentsInSlot(
                                                $date->toDateString(),
                                                $time,
                                            );
                                        @endphp
                                        <button type="button"
                                            wire:key="mobile-slot-{{ $date->format('Y-m-d') }}-{{ str_replace(':', '-', $time) }}"
                                            @if ($isBlockMode) @if ($mobileIsBlocked)
                                                    @click.prevent="showBlocked('{{ $date->toDateString() }}', '{{ $time }}')"
                                                @elseif(!$mobileHasAppointments)
                                                    wire:click="blockSlot('{{ $date->toDateString() }}', '{{ $time }}')"
                                                    wire:confirm="Block {{ $date->format('M d, Y') }} {{ \Carbon\Carbon::parse($time)->format('h:i A') }} to {{ \Carbon\Carbon::parse($time)->addHour()->format('h:i A') }}?" @endif
                                        @elseif($mobileIsBlocked)
                                            @click.prevent="showBlocked('{{ $date->toDateString() }}', '{{ $time }}')"
                                        @elseif(!$mobileIsOccupied) @click="modalOpen = true"
                                            wire:click="openAppointmentModal('{{ $date->toDateString() }}', '{{ $time }}')"
                                            @endif
                                            class="rounded-md border px-2 py-1.5 text-xs font-medium transition
                                            {{ $mobileIsBlocked ? 'border-red-200 bg-red-100 text-red-700 hover:bg-red-200' : ($mobileIsOccupied ? 'cursor-not-allowed border-gray-200 bg-gray-100 text-gray-400' : 'border-gray-300 bg-white text-gray-700 hover:border-blue-300 hover:bg-blue-50') }}">
                                            @if ($mobileIsBlocked)
                                                Blocked
                                            @elseif($isBlockMode && !$mobileHasAppointments)
                                                Block
                                            @else
                                                {{ \Carbon\Carbon::parse($time)->format('h:i A') }}
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div
                    class="hidden sm:grid grid-cols-[100px_repeat(7,minmax(0,1fr))] lg:grid-cols-[120px_repeat(7,minmax(0,1fr))] w-full relative pt-4 z-10">
                    @foreach ($timeSlots as $time)
                        <div wire:key="calendar-time-label-{{ str_replace(':', '-', $time) }}"
                            class="relative h-32 border-t border-r border-gray-200">
                            <span
                                class="absolute top-0 left-2 -mt-2.5 bg-white px-1 text-xs lg:text-sm font-semibold text-gray-500">
                                {{ Carbon\Carbon::parse($time)->format('h:i A') }}
                            </span>
                            @if ($loop->last)
                                <span
                                    class="absolute bottom-0 left-2 translate-y-1/2 bg-white px-1 text-xs lg:text-sm font-semibold text-gray-500">
                                    {{ Carbon\Carbon::parse($time)->addHour()->format('h:i A') }}
                                </span>
                            @endif
                        </div>
                        @foreach ($weekDates as $date)
                            @php
                                $blockedSlot = $this->getBlockedSlotAt($date->toDateString(), $time);
                                $isBlocked = $blockedSlot !== null;
                                $isOccupied = $this->isSlotOccupied($date->toDateString(), $time);
                                $hasAppointments = $this->hasAppointmentsInSlot($date->toDateString(), $time);
                            @endphp

                            <div wire:key="calendar-slot-{{ $date->format('Y-m-d') }}-{{ str_replace(':', '-', $time) }}"
                                @if ($isBlockMode) @if ($isBlocked)
                                        @click.prevent="showBlocked('{{ $date->toDateString() }}', '{{ $time }}')"
                                    @elseif(!$hasAppointments)
                                        wire:click="blockSlot('{{ $date->toDateString() }}', '{{ $time }}')"
                                        wire:confirm="Block {{ $date->format('M d, Y') }} {{ \Carbon\Carbon::parse($time)->format('h:i A') }} to {{ \Carbon\Carbon::parse($time)->addHour()->format('h:i A') }}?" @endif
                            @elseif($isBlocked)
                                @click.prevent="showBlocked('{{ $date->toDateString() }}', '{{ $time }}')"
                            @elseif(!$isOccupied) @click="modalOpen = true"
                                wire:click="openAppointmentModal('{{ $date->toDateString() }}', '{{ $time }}')"
                                @endif
                                class="h-32 border-t border-r border-gray-200 transition-all 
                                @if ($isBlocked) bg-red-100 text-red-800 cursor-pointer hover:bg-red-200
                                @elseif(!$isOccupied)
                                    hover:bg-stone-100 cursor-pointer
                                @else
                                    bg-gray-100 @endif
                                ">
                                @if ($isBlocked)
                                    <div class="h-full w-full px-3 py-2">
                                        <p class="text-[10px] md:text-xs font-semibold uppercase tracking-wide">Blocked
                                        </p>
                                        @if (!empty($blockedSlot->reason))
                                            <p class="hidden md:block text-[10px] mt-1 truncate">
                                                {{ $blockedSlot->reason }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endforeach

                    <div
                        class="absolute inset-x-0 bottom-0 top-4 grid grid-cols-[100px_repeat(7,minmax(0,1fr))] lg:grid-cols-[120px_repeat(7,minmax(0,1fr))] w-full pointer-events-none">
                        <div class="h-full"></div>

                        @foreach ($weekDates as $date)
                            <div wire:key="calendar-overlay-day-{{ $date->format('Y-m-d') }}"
                                class="relative h-full border-r border-gray-200">
                                @php
                                    $dayAppointments = $this->getAppointmentsForDay($date);
                                    $dayStartHour = 9;
                                    $slotHeightRem = 8;
                                    $groupedByTime = $dayAppointments->groupBy('start_time');
                                @endphp

                                @foreach ($groupedByTime as $timeKey => $appointmentsAtTime)
                                    @php
                                        $firstAppt = $appointmentsAtTime->first();
                                        $startCarbon = Carbon\Carbon::parse($firstAppt->start_time);
                                        $topInMinutes =
                                            ($startCarbon->hour - $dayStartHour) * 60 + $startCarbon->minute;
                                        $slotTopIndex = intdiv(max(0, $topInMinutes), 60);
                                        $slotSpan = max(1, (int) ceil($firstAppt->duration_in_minutes / 60));
                                        $topPositionRem = $slotTopIndex * $slotHeightRem;
                                        $heightInRem = $slotSpan * $slotHeightRem;
                                        $countAtTime = $appointmentsAtTime->count();
                                    @endphp

                                    <div wire:key="calendar-group-{{ $date->format('Y-m-d') }}-{{ str_replace(':', '-', $timeKey) }}"
                                        class="absolute w-full pointer-events-auto"
                                        style="top: {{ $topPositionRem }}rem; height: {{ $heightInRem }}rem; z-index: 10;">
                                        <div class="h-full px-0.5 lg:px-1">

                                            @if ($countAtTime === 1)
                                                @php
                                                    $canAddSecondAtThisTime = !$this->isSlotBlocked(
                                                        $date->toDateString(),
                                                        $timeKey,
                                                    );
                                                @endphp
                                                <div class="h-full flex flex-col gap-1 pointer-events-auto">
                                                    @if ($canAddSecondAtThisTime)
                                                        <button type="button"
                                                            wire:key="add-second-appointment-{{ $date->format('Y-m-d') }}-{{ str_replace(':', '-', $timeKey) }}"
                                                            @click="modalOpen = true"
                                                            wire:click="openAppointmentModal('{{ $date->toDateString() }}', '{{ substr($timeKey, 0, 5) }}')"
                                                            class="w-full rounded-md border border-emerald-700 bg-emerald-600 px-2 py-1 text-[11px] font-semibold text-white hover:bg-emerald-700 text-center cursor-pointer shadow-sm">
                                                            + Add Patient
                                                        </button>
                                                    @endif

                                                    <div wire:key="appointment-{{ $firstAppt->id }}"
                                                        @click="modalOpen = true"
                                                        wire:click="viewAppointment({{ $firstAppt->id }})"
                                                        class="rounded-lg p-2.5 lg:p-3 border border-blue-600 bg-blue-100 flex-1 min-h-0 overflow-hidden cursor-pointer flex flex-col shadow-sm hover:bg-blue-200">
                                                        <p
                                                            class="text-xs lg:text-sm font-semibold text-slate-900 mb-1 leading-tight truncate">
                                                            {{ $firstAppt->last_name }},
                                                            {{ $firstAppt->first_name }}
                                                        </p>
                                                        <p
                                                            class="text-xs font-medium text-slate-700 leading-tight mb-1 truncate">
                                                            {{ $firstAppt->service_name }}
                                                        </p>
                                                        <p
                                                            class="text-xs font-normal text-blue-800 mb-1 whitespace-nowrap">
                                                            {{ $firstAppt->start_time }} -
                                                            {{ $firstAppt->end_time }}
                                                        </p>
                                                        <p
                                                            class="text-xs font-normal truncate
                                                    @if ($firstAppt->status == 'Ongoing') text-yellow-600
                                                    @elseif($firstAppt->status == 'Scheduled') text-blue-600
                                                    @elseif($firstAppt->status == 'Cancelled') text-red-600
                                                    @elseif($firstAppt->status == 'Waiting') text-orange-600
                                                    @elseif($firstAppt->status == 'Completed') text-green-600
                                                    @else text-gray-600 @endif">
                                                            {{ $firstAppt->status === 'Waiting' ? 'Ready' : $firstAppt->status }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="h-full flex flex-col gap-1 pointer-events-auto">
                                                    @foreach ($appointmentsAtTime->take(2) as $apptItem)
                                                        <button type="button"
                                                            wire:key="appointment-stack-{{ $apptItem->id }}"
                                                            @click="modalOpen = true"
                                                            wire:click="viewAppointment({{ $apptItem->id }})"
                                                            class="flex-1 min-h-0 rounded-lg border border-blue-600 bg-blue-100 px-2 py-1.5 text-left hover:bg-blue-200 shadow-sm">
                                                            <p class="text-xs font-semibold text-slate-900 truncate">
                                                                {{ $apptItem->last_name }},
                                                                {{ $apptItem->first_name }}
                                                            </p>
                                                            <p class="text-[11px] text-slate-700 truncate">
                                                                {{ $apptItem->service_name }}
                                                            </p>
                                                            <p class="text-[11px] text-blue-800 truncate">
                                                                {{ $apptItem->start_time }} -
                                                                {{ $apptItem->end_time }}
                                                            </p>
                                                        </button>
                                                    @endforeach

                                                    @if ($countAtTime > 2)
                                                        <div
                                                            class="rounded-md border border-gray-200 bg-gray-50 px-2 py-1 text-[11px] font-semibold text-gray-600">
                                                            +{{ $countAtTime - 2 }} more
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- --- APPOINTMENT MODAL --- --}}
    <div x-cloak x-show="modalOpen" x-transition.opacity.duration.150ms
        class="fixed inset-0 z-50 flex items-center justify-center p-4" wire:ignore.self>

        <div class="absolute inset-0 bg-black opacity-60"></div>
        <div class="relative bg-white border border-[#e4eff8] w-full max-w-6xl mx-4 z-10 overflow-hidden" style="font-family:'Montserrat',sans-serif;">

            <div x-cloak x-show="openingPatientForm" x-transition.opacity.duration.120ms
                class="absolute inset-0 z-20 flex items-center justify-center bg-white/85 backdrop-blur-[1px]">
                <div class="flex flex-col items-center gap-3">
                    <div class="h-10 w-10 animate-spin rounded-full border-4 border-blue-200 border-t-[#0086da]"></div>
                    <div class="text-sm font-semibold text-gray-700">Opening patient form...</div>
                </div>
            </div>

            <div class="px-6 py-5 sm:px-8 flex items-center justify-between border-b border-[#e4eff8] bg-white">
                <div>
                    <div class="mb-1 inline-flex items-center gap-[10px] text-[.63rem] font-bold uppercase tracking-[.22em] text-[#0086da]">
                        <span class="block h-[2px] w-[22px] bg-[#0086da]"></span>
                        Appointment
                    </div>
                    <h2 class="text-[1.3rem] font-extrabold leading-[1.15] tracking-[-.02em] text-[#1a2e3b]">Appointment Details</h2>
                </div>
                <button class="inline-flex h-9 w-9 items-center justify-center border border-[#e4eff8] text-[#587189] transition hover:bg-[#f6fafd] hover:text-[#0086da]"
                    @click="modalOpen = false; $wire.closeAppointmentModal(true)">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="square"><path d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </div>

            @if (session()->has('error'))
                <div class="bg-red-100 text-red-700 px-6 py-3 text-sm font-bold border-b border-red-200">
                    {{ session('error') }}
                </div>
            @endif

            @if ($showAppointmentModal)
                <div wire:loading.flex
                    wire:target="openAppointmentModal,viewAppointment,saveAppointment,updateStatus,admitPatient"
                    class="min-h-[300px] items-center justify-center bg-[#f6fafd] text-center">
                    <div class="flex flex-col items-center gap-3">
                        <div class="h-10 w-10 animate-spin rounded-full border-4 border-blue-200 border-t-[#0086da]">
                        </div>
                        <div class="text-sm font-semibold text-gray-700">Loading...</div>
                    </div>
                </div>

                {{-- [FIX] Changed from DIV to FORM so the submit button works --}}
                <form class="px-6 py-6 sm:px-8 overflow-y-auto max-h-[85vh]" wire:submit.prevent="saveAppointment"
                    wire:loading.remove
                    wire:target="openAppointmentModal,viewAppointment,saveAppointment,updateStatus,admitPatient">

                    @if ($isViewing && $appointmentStatus === 'Pending' && !$isRescheduling)
                        @php
                            $safetyTone = $pendingApprovalSafety['tone'] ?? 'emerald';
                            $safetyPalette = match ($safetyTone) {
                                'rose' => 'border-rose-200 bg-rose-50 text-rose-900',
                                'amber' => 'border-amber-200 bg-amber-50 text-amber-900',
                                default => 'border-emerald-200 bg-emerald-50 text-emerald-900',
                            };
                            $safetyBadge = match ($safetyTone) {
                                'rose' => 'border-rose-200 bg-rose-50 text-rose-700',
                                'amber' => 'border-amber-200 bg-amber-50 text-amber-700',
                                default => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                            };
                        @endphp
                        <div x-data="{ showSafetyDetails: false }" class="mb-4 border px-5 py-4 {{ $safetyPalette }}">
                            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                                <div class="flex flex-col items-start gap-2">
                                    <span class="text-[.62rem] font-bold uppercase tracking-[.18em] text-current/70">
                                        Approval Safety Check
                                    </span>
                                    <div class="inline-flex border px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] {{ $safetyBadge }}">
                                        {{ $pendingApprovalSafety['headline'] ?? 'Review before approval' }}
                                    </div>
                                    <div class="inline-flex border border-[#e4eff8] bg-white px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-[#1a2e3b]">
                                        {{ $pendingApprovalSafety['approved_overlap_count'] ?? 0 }}/2 Approved in this slot
                                    </div>
                                    <div class="inline-flex border border-[#e4eff8] bg-white px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-[#1a2e3b]">
                                        {{ $pendingApprovalSafety['same_time_pending_count'] ?? 0 }} Appointment Request at Same Time
                                    </div>
                                </div>
                                <button type="button" @click="showSafetyDetails = !showSafetyDetails"
                                    class="inline-flex border border-[#e4eff8] bg-white px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.14em] text-[#1a2e3b] transition hover:bg-[#f6fafd]">
                                    <span x-show="!showSafetyDetails">Show Other Appointment</span>
                                    <span x-show="showSafetyDetails" x-cloak>Hide</span>
                                </button>
                            </div>

                            <div x-cloak x-show="showSafetyDetails" x-transition.opacity.duration.150ms class="mt-3 space-y-3 border-t border-current/10 pt-3">
                                @if (!empty($pendingApprovalSafety['overlapping_appointments']))
                                    <div class="grid gap-2">
                                        @foreach ($pendingApprovalSafety['overlapping_appointments'] as $overlap)
                                            <div class="flex flex-col gap-1 border border-[#e4eff8] bg-white px-3 py-3 text-sm text-[#1a2e3b] sm:flex-row sm:items-center sm:justify-between">
                                                <div>
                                                    <div class="font-semibold">{{ $overlap['patient_name'] ?: 'Unnamed patient' }}</div>
                                                    <div class="text-xs text-[#587189]">{{ $overlap['service_name'] }} at {{ $overlap['time'] }}</div>
                                                </div>
                                                <span class="inline-flex border border-[#e4eff8] bg-[#f6fafd] px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-[#587189]">
                                                    {{ $overlap['status'] }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if (!empty($pendingDuplicateWarnings))
                                    <div class="border border-amber-200 bg-white px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.14em] text-amber-700">
                                        @foreach ($pendingDuplicateWarnings as $warning)
                                            <div>{{ $warning }}</div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- DATE & TIME HEADER --}}
                    <div class="mb-6 grid grid-cols-3 gap-[2px] bg-[#e4eff8]">
                        <div class="bg-white px-5 py-4">
                            <div class="text-[.6rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Date</div>
                            @if ($isViewing && $appointmentStatus === 'Pending' && $isRescheduling)
                                <input type="date" wire:model.live="selectedDate" min="{{ now()->toDateString() }}"
                                    class="mt-2 w-full border border-[#e4eff8] bg-white px-3 py-2 text-sm font-semibold text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#0086da]/10">
                                @error('selectedDate')
                                    <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span>
                                @enderror
                            @else
                                <div class="mt-2 text-base font-extrabold text-[#1a2e3b]">
                                    {{ \Carbon\Carbon::parse($selectedDate)->format('M j, Y') }}
                                </div>
                            @endif
                        </div>
                        <div class="bg-white px-5 py-4">
                            <div class="text-[.6rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Start Time</div>
                            @if ($isViewing && $appointmentStatus === 'Pending' && $isRescheduling)
                                <select wire:model.live="selectedTime"
                                    class="mt-2 w-full border border-[#e4eff8] bg-white px-3 py-2 text-sm font-semibold text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#0086da]/10">
                                    <option value="">Select time</option>
                                    @foreach ($timeSlots as $time)
                                        <option value="{{ $time }}">{{ \Carbon\Carbon::parse($time)->format('g:i A') }}</option>
                                    @endforeach
                                </select>
                                @error('selectedTime')
                                    <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span>
                                @enderror
                            @else
                                <div class="mt-2 text-base font-extrabold text-[#1a2e3b]">
                                    {{ !empty($selectedTime) ? \Carbon\Carbon::parse($selectedTime)->format('g:i A') : '—' }}
                                </div>
                            @endif
                        </div>
                        <div class="bg-white px-5 py-4">
                            <div class="text-[.6rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">End Time</div>
                            <div class="mt-2 text-base font-extrabold text-[#1a2e3b]">
                                {{ !empty($endTime) ? \Carbon\Carbon::parse($endTime)->format('g:i A') : '—' }}
                            </div>
                        </div>
                    </div>

                    @if ($isViewing)
                        <div class="mb-6 border border-[#e4eff8] bg-[#f6fafd] px-5 py-5">
                            <div class="mb-2 text-[.6rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Booked By:</div>
                            <div class="text-[1rem] font-extrabold leading-[1.2] text-[#1a2e3b]">
                                {{ trim($viewingRequesterFirstName . ' ' . $viewingRequesterLastName) ?: 'Same as patient' }}
                            </div>
                            <div class="mt-2 text-[.82rem] leading-[1.75] text-[#587189]">
                                {{ $viewingRequesterContactNumber ?: 'No contact number provided' }}
                            </div>
                            <div class="text-[.82rem] leading-[1.75] text-[#7a9db5]">
                                {{ $viewingRequesterEmail ?: 'No email provided' }}
                            </div>
                            @if ($viewingBookingForOther && $viewingRequesterRelationship)
                                <div class="mt-2 text-[.72rem] font-bold uppercase tracking-[.12em] text-[#0086da]">
                                    Relationship: {{ $viewingRequesterRelationship }}
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- SEARCH EXISTING PATIENT --}}
                    @if (!$isViewing)
                        <div class="mb-6 relative">
                        <label class="block text-[.72rem] font-bold uppercase tracking-[.12em] text-[#587189] mb-2">Search Existing Patient</label>
                            <div class="relative">
                                <input type="text" wire:model.live.debounce.300ms="searchQuery"
                                    class="w-full border border-[#e4eff8] px-4 py-3 pl-10 text-sm text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#0086da]/10"
                                    placeholder="Search by name or phone number..." />
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24"
                                        height="24" color="currentColor" fill="none" stroke="currentColor"
                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path
                                            d="M2.5 12C2.5 7.52166 2.5 5.28249 3.89124 3.89124C5.28249 2.5 7.52166 2.5 12 2.5C16.4783 2.5 18.7175 2.5 20.1088 3.89124C21.5 5.28249 21.5 7.52166 21.5 12C21.5 16.4783 21.5 18.7175 20.1088 20.1088C18.7175 21.5 16.4783 21.5 12 21.5C7.52166 21.5 5.28249 21.5 3.89124 20.1088C2.5 18.7175 2.5 16.4783 2.5 12Z" />
                                        <path
                                            d="M14.8284 14.8284L17 17M16 12C16 9.79086 14.2091 8 12 8C9.79086 8 8 9.79086 8 12C8 14.2091 9.79086 16 12 16C14.2091 16 16 14.2091 16 12Z" />
                                    </svg>
                                </div>

                                @if (!empty($searchQuery) && count($patientSearchResults) > 0)
                                    <div
                                        class="absolute z-50 mt-1 w-full bg-white shadow-xl max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                        @foreach ($patientSearchResults as $result)
                                            <button type="button"
                                                wire:key="patient-search-result-{{ $result->id }}"
                                                wire:click="selectPatient({{ $result->id }})"
                                                class="w-full text-left cursor-pointer select-none relative py-3 pl-3 pr-9 hover:bg-blue-50 transition text-gray-900 group border-b border-gray-100 last:border-0">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-semibold block truncate">
                                                        {{ $result->last_name }}, {{ $result->first_name }}
                                                        <span class="font-normal text-gray-500 text-xs ml-1">
                                                            ({{ $result->birth_date ? \Carbon\Carbon::parse($result->birth_date)->format('M d, Y') : 'No Bday' }})
                                                        </span>
                                                    </span>
                                                    <span
                                                        class="text-gray-500 text-xs bg-gray-100 px-2 py-1 rounded-full group-hover:bg-white">
                                                        {{ $result->mobile_number }}
                                                    </span>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                @elseif(!empty($searchQuery) && strlen($searchQuery) >= 2)
                                    <div
                                        class="absolute z-50 mt-1 w-full bg-white shadow-lg rounded-md py-2 px-4 text-sm text-gray-500 border border-gray-200">
                                        No patient found. Please fill in the details below.
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- FORM INPUTS --}}
                    @php
                        $inputBase = 'w-full border bg-white px-4 py-3 text-sm text-[#1a2e3b] outline-none transition focus:ring-2 focus:ring-[#0086da]/10';
                        $firstNameInputClass  = $errors->has('firstName')     ? $inputBase . ' border-rose-400' : $inputBase . ' border-[#e4eff8] focus:border-[#0086da]';
                        $middleNameInputClass  = $errors->has('middleName')    ? $inputBase . ' border-rose-400' : $inputBase . ' border-[#e4eff8] focus:border-[#0086da]';
                        $lastNameInputClass   = $errors->has('lastName')      ? $inputBase . ' border-rose-400' : $inputBase . ' border-[#e4eff8] focus:border-[#0086da]';
                        $contactInputClass    = $errors->has('contactNumber') ? $inputBase . ' border-rose-400' : $inputBase . ' border-[#e4eff8] focus:border-[#0086da]';
                        $birthDateInputClass  = $errors->has('birthDate')     ? $inputBase . ' border-rose-400' : $inputBase . ' border-[#e4eff8] focus:border-[#0086da]';
                        $serviceSelectClass   = $errors->has('selectedService')? $inputBase . ' border-rose-400' : $inputBase . ' border-[#e4eff8] focus:border-[#0086da]';
                    @endphp
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="">
                            <label class="block text-[.72rem] font-bold uppercase tracking-[.12em] text-[#587189] mb-2">First Name <span class="text-rose-600">*</span></label>
                            <input wire:model="firstName" type="text" class="{{ $firstNameInputClass }}"
                                @if ($isViewing) readonly class="w-full border border-[#e4eff8] bg-[#f6fafd] px-4 py-3 text-sm text-[#1a2e3b] cursor-not-allowed" @endif />
                            @error('firstName')
                                <span class="text-xs text-rose-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="">
                            <label class="block text-[.72rem] font-bold uppercase tracking-[.12em] text-[#587189] mb-2">Middle Name</label>
                            <input wire:model="middleName" type="text" class="{{ $middleNameInputClass }}"
                                @if ($isViewing) readonly class="w-full border border-[#e4eff8] bg-[#f6fafd] px-4 py-3 text-sm text-[#1a2e3b] cursor-not-allowed" @endif />
                            @error('middleName')
                                <span class="text-xs text-rose-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="">
                            <label class="block text-[.72rem] font-bold uppercase tracking-[.12em] text-[#587189] mb-2">Last Name <span class="text-rose-600">*</span></label>
                            <input wire:model="lastName" type="text" class="{{ $lastNameInputClass }}"
                                @if ($isViewing) readonly class="w-full border border-[#e4eff8] bg-[#f6fafd] px-4 py-3 text-sm text-[#1a2e3b] cursor-not-allowed" @endif />
                            @error('lastName')
                                <span class="text-xs text-rose-600">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[.72rem] font-bold uppercase tracking-[.12em] text-[#587189] mb-2">Contact Number <span class="text-rose-600">*</span></label>
                            <input wire:model="contactNumber" type="text" inputmode="numeric" maxlength="11"
                                pattern="[0-9]{11}" class="{{ $contactInputClass }}"
                                @if ($isViewing) readonly class="w-full border border-[#e4eff8] bg-[#f6fafd] px-4 py-3 text-sm text-[#1a2e3b] cursor-not-allowed" @endif />
                            @error('contactNumber')
                                <span class="text-xs text-rose-600">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- BIRTH DATE (Required for Saving) --}}
                        <div>
                            <label class="block text-[.72rem] font-bold uppercase tracking-[.12em] text-[#587189] mb-2">Birth Date <span class="text-rose-600">*</span></label>
                            <input wire:model.live="birthDate" type="date" class="{{ $birthDateInputClass }}"
                                @if ($isViewing) readonly class="w-full border border-[#e4eff8] bg-[#f6fafd] px-4 py-3 text-sm text-[#1a2e3b] cursor-not-allowed" @endif />
                            @error('birthDate')
                                <span class="text-xs text-rose-600">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[.72rem] font-bold uppercase tracking-[.12em] text-[#587189] mb-2">Service Required <span class="text-rose-600">*</span></label>
                            <select wire:model.live="selectedService" class="{{ $serviceSelectClass }}"
                                {{ $isViewing && $appointmentStatus != 'Waiting' ? 'disabled' : '' }}>
                                <option value="" disabled>Select service</option>
                                @foreach ($servicesList as $service)
                                    <option value="{{ $service->id }}">
                                        {{ $service->service_name }}
                                        ({{ \Carbon\Carbon::parse($service->duration)->format('H:i') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('selectedService')
                                <span class="text-xs text-rose-600">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    @if ($isViewing && empty($viewingPatientId) && in_array($appointmentStatus, ['Waiting', 'Scheduled'], true) && auth()->user()->role !== 3)
                        <div class="mb-4 border border-amber-200 bg-amber-50 px-4 py-3">
                            <div class="text-[.82rem] font-bold text-amber-900">Patient record not linked yet</div>
                            <p class="mt-1 text-[.78rem] leading-[1.7] text-amber-800">
                                Patient chart and admission actions stay blocked until staff links or creates the patient record.
                            </p>
                        </div>

                        <div class="mb-6 border border-[#e4eff8] bg-[#f6fafd] p-5">
                            <div class="mb-1 flex items-center justify-between gap-3">
                                <div class="text-[.6rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Patient Record Review</div>
                                @if (!empty($pendingDuplicateWarnings))
                                    <div class="relative group">
                                        <button type="button"
                                            class="inline-flex h-6 w-6 items-center justify-center rounded-full border border-amber-300 bg-amber-100 text-[13px] font-extrabold text-amber-700 transition hover:bg-amber-200 focus:outline-none focus:ring-2 focus:ring-amber-300"
                                            aria-label="Duplicate warnings available">
                                            !
                                        </button>
                                        <div
                                            class="pointer-events-none absolute right-0 top-8 z-20 hidden w-[320px] rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-900 shadow-[0_14px_36px_rgba(120,53,15,.18)] group-hover:block group-focus-within:block">
                                            <p class="font-semibold">Duplicate warnings before creating a new patient:</p>
                                            <ul class="mt-2 list-disc space-y-1 pl-4">
                                                @foreach ($pendingDuplicateWarnings as $warning)
                                                    <li>{{ $warning }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-4">
                                <div class="mb-3 flex items-center justify-between gap-3">
                                    <div>
                                        <div class="text-[.6rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Possible Patient Matches</div>
                                        <p class="mt-1 text-[.78rem] leading-[1.7] text-[#587189]">
                                            Review the request beside each patient record before linking.
                                        </p>
                                    </div>
                                    @if (!empty($pendingMatchCandidates))
                                        <span class="inline-flex items-center rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-blue-700">
                                            {{ count($pendingMatchCandidates) }} {{ \Illuminate\Support\Str::plural('match', count($pendingMatchCandidates)) }}
                                        </span>
                                    @endif
                                </div>

                                <div class="grid gap-3">
                                @if (!empty($pendingMatchCandidates))
                                    @php
                                        $initialMatchId = (int) ($selectedPendingPatientId ?: (collect($pendingMatchCandidates)->first()->id ?? 0));
                                    @endphp
                                    <div x-data="{ selectedMatch: {{ $initialMatchId }} }"
                                        class="overflow-hidden rounded-2xl border border-[#cfe2f1] bg-white/90">
                                        <div class="grid gap-0 lg:grid-cols-[minmax(0,0.88fr)_minmax(0,1.12fr)]">
                                            <div class="border-b border-[#dbeaf7] bg-[#f5f9fd] p-4 lg:border-b-0 lg:border-r">
                                                <div class="mb-4 rounded-xl border border-[#d7e8f5] bg-white px-4 py-3">
                                                    <p class="text-sm font-bold text-[#1a2e3b]">Search Results</p>
                                                    <p class="mt-1 text-xs text-[#6e8ea5]">Select one candidate to compare in detail.</p>
                                                </div>

                                                <div class="space-y-2">
                                                    @foreach ($pendingMatchCandidates as $candidate)
                                                        @php
                                                            $candidateId = (int) $candidate->id;
                                                            $candidateName = trim(($candidate->first_name ?? '') . ' ' . ($candidate->last_name ?? '')) ?: 'Unnamed patient';
                                                            $candidateBirth = !empty($candidate->birth_date) ? \Carbon\Carbon::parse($candidate->birth_date)->format('M d, Y') : 'No birth date';
                                                            $candidateBand = (string) ($candidate->match_band ?? 'poor');
                                                            $candidatePercent = (int) ($candidate->match_percent ?? 0);
                                                            $candidateColor = match ($candidateBand) {
                                                                'strong' => 'bg-emerald-500',
                                                                'partial' => 'bg-amber-500',
                                                                'weak' => 'bg-slate-500',
                                                                default => 'bg-slate-400',
                                                            };
                                                        @endphp
                                                        <button type="button"
                                                            @click="selectedMatch = {{ $candidateId }}; $wire.set('selectedPendingPatientId', {{ $candidateId }})"
                                                            class="w-full rounded-xl border px-4 py-3 text-left transition"
                                                            :class="selectedMatch === {{ $candidateId }}
                                                                ? 'border-[#2e8dd0] bg-[#eaf5ff] shadow-[inset_0_0_0_1px_rgba(0,134,218,.18)]'
                                                                : 'border-[#d7e8f5] bg-white hover:border-[#8fc0e1]'">
                                                            <div class="flex items-start justify-between gap-3">
                                                                <span class="mt-2 h-2.5 w-2.5 rounded-full {{ $candidateColor }}"></span>
                                                                <div>
                                                                    <p class="text-base font-bold text-[#1a2e3b]">{{ $candidateName }}</p>
                                                                    <p class="mt-0.5 text-sm text-[#6e8ea5]">{{ $candidateBirth }} · #{{ $candidateId }} · {{ $candidatePercent }}%</p>
                                                                </div>
                                                                <span aria-hidden="true"
                                                                    class="ml-auto mt-1 inline-flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full border transition"
                                                                    :class="selectedMatch === {{ $candidateId }}
                                                                        ? 'border-[#0086da] bg-[#0086da]'
                                                                        : 'border-[#9fc8e3] bg-white'">
                                                                    <span class="h-2.5 w-2.5 rounded-full bg-white"
                                                                        :class="selectedMatch === {{ $candidateId }} ? 'opacity-100' : 'opacity-0'"></span>
                                                                </span>
                                                            </div>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <div class="bg-white p-4 md:p-5">
                                                @foreach ($pendingMatchCandidates as $candidate)
                                                    @php
                                                        $candidateId = (int) $candidate->id;
                                                        $candidateName = trim(($candidate->first_name ?? '') . ' ' . ($candidate->last_name ?? '')) ?: 'Unnamed patient';
                                                    @endphp
                                                    <div x-cloak x-show="selectedMatch === {{ $candidateId }}" class="space-y-4">
                                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                                            <p class="text-[.7rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Match Signals</p>
                                                            @php
                                                                $matchBand = (string) ($candidate->match_band ?? 'poor');
                                                                $matchPercent = (int) ($candidate->match_percent ?? 0);
                                                                $matchLabel = (string) ($candidate->match_band_label ?? 'Poor match');
                                                                $matchBadge = match ($matchBand) {
                                                                    'strong' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                                                                    'partial' => 'border-amber-200 bg-amber-50 text-amber-700',
                                                                    'weak' => 'border-slate-300 bg-slate-100 text-slate-700',
                                                                    default => 'border-slate-300 bg-slate-50 text-slate-600',
                                                                };
                                                            @endphp
                                                            <span class="inline-flex items-center rounded-full border px-3 py-1 text-[11px] font-bold uppercase tracking-[0.14em] {{ $matchBadge }}">
                                                                {{ $matchPercent }}% · {{ $matchLabel }}
                                                            </span>
                                                        </div>

                                                        @if (!empty($candidate->match_reasons))
                                                            <div class="flex flex-wrap gap-2">
                                                                @foreach ($candidate->match_reasons as $reason)
                                                                    <span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-[12px] font-semibold text-emerald-700">
                                                                        {{ $reason }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        @endif

                                                        <div class="rounded-2xl border border-[#dbeaf7] bg-[#f8fbfe] p-4">
                                                            <div class="text-[.65rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Comparison</div>
                                                            @php
                                                                $requestName = strtolower(trim(($firstName ?? '') . ' ' . ($lastName ?? '')));
                                                                $candidateNameCompare = strtolower(trim(($candidate->first_name ?? '') . ' ' . ($candidate->last_name ?? '')));
                                                                $nameExactMatch = $requestName !== '' && $candidateNameCompare === $requestName;

                                                                $requestBirthDate = !empty($birthDate) ? \Carbon\Carbon::parse($birthDate)->toDateString() : null;
                                                                $candidateBirthDate = !empty($candidate->birth_date) ? \Carbon\Carbon::parse($candidate->birth_date)->toDateString() : null;
                                                                $birthDateMatch = !empty($requestBirthDate) && !empty($candidateBirthDate) && $requestBirthDate === $candidateBirthDate;

                                                                $requestMobile = trim((string) ($viewingRequesterContactNumber ?: ($contactNumber ?? '')));
                                                                $candidateMobile = trim((string) ($candidate->mobile_number ?? ''));
                                                                $mobileMatch = $requestMobile !== '' && $candidateMobile !== '' && $requestMobile === $candidateMobile;

                                                                $requestEmail = strtolower(trim((string) ($viewingRequesterEmail ?? '')));
                                                                $candidateEmail = strtolower(trim((string) ($candidate->email_address ?? '')));
                                                                $emailMatch = $requestEmail !== '' && $candidateEmail !== '' && $requestEmail === $candidateEmail;
                                                            @endphp
                                                            <div class="mt-3 grid gap-2 text-sm text-[#1a2e3b]">
                                                                <div class="grid grid-cols-[120px_minmax(0,1fr)] items-start gap-2 border-b border-[#e5eff8] pb-2">
                                                                    <span class="font-semibold text-[#5f7d93]">Name</span>
                                                                    <span class="{{ $nameExactMatch ? 'font-bold text-emerald-700' : 'font-bold text-[#1768a6]' }}">
                                                                        {{ $nameExactMatch ? '✓ ' : '' }}{{ $candidateName }}
                                                                    </span>
                                                                </div>
                                                                <div class="grid grid-cols-[120px_minmax(0,1fr)] items-start gap-2 border-b border-[#e5eff8] pb-2">
                                                                    <span class="font-semibold text-[#5f7d93]">Birth date</span>
                                                                    <span class="{{ $birthDateMatch ? 'font-semibold text-emerald-700' : '' }}">
                                                                        {{ $birthDateMatch ? '✓ ' : '' }}{{ !empty($candidate->birth_date) ? \Carbon\Carbon::parse($candidate->birth_date)->format('M d, Y') : 'Not provided' }}
                                                                    </span>
                                                                </div>
                                                                <div class="grid grid-cols-[120px_minmax(0,1fr)] items-start gap-2 border-b border-[#e5eff8] pb-2">
                                                                    <span class="font-semibold text-[#5f7d93]">Mobile</span>
                                                                    <span class="{{ $mobileMatch ? 'font-semibold text-emerald-700' : '' }}">
                                                                        {{ $mobileMatch ? '✓ ' : '' }}{{ $candidate->mobile_number ?: 'Not provided' }}
                                                                    </span>
                                                                </div>
                                                                <div class="grid grid-cols-[120px_minmax(0,1fr)] items-start gap-2">
                                                                    <span class="font-semibold text-[#5f7d93]">Email</span>
                                                                    <span class="break-all {{ $emailMatch ? 'font-semibold text-emerald-700' : '' }}">
                                                                        {{ $emailMatch ? '✓ ' : '' }}{{ $candidate->email_address ?: 'Not provided' }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_auto]">
                                                            <button type="button"
                                                                @click.stop="openingPatientForm = true; resumeAppointmentModalOnPatientFormClose = true"
                                                                wire:click="previewPendingPatientRecord({{ $candidateId }})"
                                                                class="{{ $btnSm }} {{ $btnSecondary }} w-full">
                                                                View Full Record
                                                            </button>
                                                            <button type="button"
                                                                @click="selectedMatch = {{ $candidateId }}; $wire.set('selectedPendingPatientId', {{ $candidateId }})"
                                                                class="{{ $btnSm }} {{ $btnPrimary }} whitespace-nowrap">
                                                                Select Match
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="border border-dashed border-[#d4e8f5] bg-white px-4 py-3 text-[.78rem] text-[#587189]">
                                        No strong match suggestions found. You can create a new patient record after
                                        staff verification at check-in.
                                    </div>
                                @endif
                                </div>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-2">
                                <button type="button" wire:click="linkPendingRequestToExistingPatient"
                                    wire:loading.attr="disabled" wire:target="linkPendingRequestToExistingPatient"
                                    class="{{ $btnSm }} {{ $btnPrimary }}">
                                    Link to Existing Patient
                                </button>
                                <button type="button" wire:click="createPatientForPendingRequest"
                                    wire:loading.attr="disabled" wire:target="createPatientForPendingRequest"
                                    class="{{ $btnSm }} {{ $btnOutlinePrimary }}">
                                    Create New Patient
                                </button>
                            </div>
                        </div>
                    @elseif ($isViewing && $viewingPatientId && in_array($appointmentStatus, ['Scheduled', 'Waiting'], true))
                        <div class="mb-6 border border-emerald-200 bg-emerald-50 px-4 py-3">
                            <div class="text-[.82rem] font-bold text-emerald-900">Patient record ready</div>
                            <p class="mt-1 text-[.78rem] leading-[1.7] text-emerald-800">
                                This appointment is already linked to patient record #{{ $viewingPatientId }}.
                            </p>
                            @if (auth()->user()->role !== 3)
                                <div class="mt-3">
                                    <button type="button" wire:click="unlinkAppointmentPatient"
                                        wire:loading.attr="disabled" wire:target="unlinkAppointmentPatient"
                                        wire:confirm="Unlink this appointment from the current patient record? You can relink it right after."
                                        class="{{ $btnSm }} {{ $btnOutlinePrimary }}">
                                        Unlink / Relink Patient
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="flex flex-col sm:flex-row justify-end gap-3 mt-6 pt-4 border-t border-[#e4eff8]">
                        @error('conflict')
                            <span class="text-rose-600 text-sm mr-auto">{{ $message }}</span>
                        @enderror

                        @if ($isViewing)
                            {{-- === VIEWING MODE (Flow Logic) === --}}
                            @if (!in_array($appointmentStatus, ['Cancelled', 'Completed']) && !$isRescheduling)
                                <button type="button" wire:click="updateStatus('Cancelled')"
                                    wire:loading.attr="disabled" wire:target="updateStatus"
                                    wire:confirm="Are you sure you want to cancel this appointment?"
                                    class="{{ $btnLg }} {{ $btnDangerSoft }} mr-auto">
                                    Cancel Appointment
                                </button>
                            @endif

                            @if ($appointmentStatus === 'Pending')
                                @if (auth()->user()->role !== 3)
                                    @if ($isRescheduling)
                                        <button type="button" wire:click="cancelPendingReschedule"
                                            wire:loading.attr="disabled" wire:target="cancelPendingReschedule"
                                            class="{{ $btnLg }} {{ $btnSecondary }} mr-auto">
                                            Cancel Reschedule
                                        </button>
                                        <button type="button" wire:click="savePendingReschedule"
                                            wire:loading.attr="disabled" wire:target="savePendingReschedule"
                                            wire:confirm="Save the new date and time for this request?"
                                            class="{{ $btnLg }} {{ $btnPrimary }}">
                                            Save New Schedule
                                        </button>
                                    @else
                                        <button type="button" wire:click="beginPendingReschedule"
                                            wire:loading.attr="disabled" wire:target="beginPendingReschedule"
                                            class="{{ $btnLg }} {{ $btnSecondary }}">
                                            Reschedule
                                        </button>
                                        <button type="button" wire:click="updateStatus('Scheduled')"
                                            wire:loading.attr="disabled" wire:target="updateStatus"
                                            wire:confirm="Approve this appointment request?"
                                            @disabled(!($pendingApprovalSafety['can_approve'] ?? true))
                                            class="{{ $btnLg }} {{ ($pendingApprovalSafety['can_approve'] ?? true) ? $btnPrimary : $btnSecondary }}">
                                            {{ ($pendingApprovalSafety['can_approve'] ?? true) ? 'Approve' : 'Reschedule Required' }}
                                        </button>
                                    @endif
                                @endif
                            @elseif($appointmentStatus === 'Scheduled')
                                <button type="button" @click="openingPatientForm = true"
                                    wire:click="dispatchPatientForm(1)"
                                    class="{{ $btnLg }} {{ $btnOutlinePrimary }}">
                                    Patient Info
                                </button>
                                <button type="button" wire:click="updateStatus('Waiting')"
                                    wire:loading.attr="disabled" wire:target="updateStatus"
                                    wire:confirm="Confirm patient is ready?"
                                    class="{{ $btnLg }} {{ $btnPrimary }}">
                                    Mark Ready
                                </button>
                            @elseif($appointmentStatus === 'Waiting')
                                <button type="button" @click="openingPatientForm = true"
                                    wire:click="dispatchPatientForm(1)"
                                    class="{{ $btnLg }} {{ $btnOutlinePrimary }}">
                                    Patient Info
                                </button>

                                @if (auth()->user()->canHandleChairsideFlow())
                                    <button type="button" wire:click="admitPatient" wire:loading.attr="disabled"
                                        wire:target="admitPatient" wire:confirm="Admit this patient to the chair now?"
                                        @if (!$viewingPatientId) disabled @endif
                                        class="{{ $btnLg }} {{ $btnPrimary }} {{ !$viewingPatientId ? 'opacity-50 cursor-not-allowed' : '' }}">
                                        ADMIT PATIENT
                                    </button>
                                @endif
                            @elseif($appointmentStatus === 'Ongoing')
                                @if (auth()->user()->canHandleChairsideFlow())
                                    <button type="button" @click="openingPatientForm = true"
                                        wire:click="dispatchPatientForm(3)"
                                        class="{{ $btnLg }} {{ $btnOutlinePrimary }}">
                                        View Dental Chart
                                    </button>
                                @endif
                                <button type="button" wire:click="updateStatus('Completed')"
                                    wire:loading.attr="disabled" wire:target="updateStatus"
                                    wire:confirm="Mark this appointment as completed?"
                                    class="{{ $btnLg }} {{ $btnPrimary }}">
                                    Finish & Complete
                                </button>
                            @elseif($appointmentStatus === 'Completed')
                                <span class="inline-flex border border-emerald-200 bg-emerald-50 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.14em] text-emerald-700">
                                    ✓ Completed
                                </span>
                            @elseif($appointmentStatus === 'Cancelled')
                                <span class="inline-flex border border-rose-200 bg-rose-50 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.14em] text-rose-700">
                                    ✕ Cancelled
                                </span>
                            @endif
                        @else
                            {{-- === BOOKING MODE === --}}
                            <button type="button" @click="modalOpen = false; $wire.closeAppointmentModal(true)"
                                class="{{ $btnLg }} {{ $btnOutlinePrimary }}">Cancel</button>
                            <button type="submit" wire:loading.attr="disabled" wire:target="saveAppointment"
                                onclick="return confirm('Save this appointment and patient details?')"
                                class="{{ $btnLg }} {{ $btnPrimary }}">
                                Save Appointment
                            </button>
                        @endif
                    </div>
                </form>
            @else
                <div class="min-h-[300px] flex items-center justify-center bg-[#f6fafd] text-center">
                    <div class="flex flex-col items-center gap-3">
                        <div class="h-10 w-10 animate-spin rounded-full border-4 border-blue-200 border-t-[#0086da]">
                        </div>
                        <div class="text-sm font-semibold text-gray-700">Loading...</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <livewire:patient-form-controller.patient-form-modal />

    <div x-cloak x-show="blockedToast" x-transition.opacity.duration.120ms
        class="fixed bottom-6 right-6 z-[75] w-full max-w-md rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800 shadow-lg">
        <span x-text="blockedMessage"></span>
    </div>

    <div wire:loading.flex
        wire:target="approveAppointment,rejectAppointment,previousWeek,nextWeek,goToDate,goToToday,toggleBlockMode,blockSlot,unblockSlot"
        class="fixed inset-0 z-[70] items-center justify-center bg-white/70 backdrop-blur-sm text-center">
        <div class="flex flex-col items-center gap-3">
            <div class="h-10 w-10 animate-spin rounded-full border-4 border-blue-200 border-t-[#0086da]"></div>
            <div class="text-sm font-semibold text-gray-700">Loading...</div>
        </div>
    </div>

    @include('components.flash-toast')
</div>
