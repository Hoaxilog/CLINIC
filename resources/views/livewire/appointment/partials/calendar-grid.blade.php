@if ($activeTab === 'calendar')
    @if ($isBlockMode)
        <div
            class="mb-4 flex items-center gap-3 border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800"
            style="font-family:'Montserrat',sans-serif;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="shrink-0">
                <circle cx="12" cy="12" r="10" /><line x1="12" y1="8" x2="12" y2="12" /><line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
            Block mode active — select an available slot to block it. Only future blocked slots can be unblocked.
        </div>
    @endif

    <div class="relative" style="font-family:'Montserrat',sans-serif;">

        {{-- ── Day/Chair header ── --}}
        <div
            class="grid grid-cols-[90px_repeat(7,minmax(0,1fr))] lg:grid-cols-[110px_repeat(7,minmax(0,1fr))] sticky top-14 z-20 w-full border-b border-[#e4eff8] bg-white shadow-[0_2px_8px_0_rgba(0,134,218,0.06)]">
            {{-- Time label spacer --}}
            <div class="border-r border-[#e4eff8]"></div>

            @foreach ($weekDates as $date)
                <div wire:key="calendar-day-header-{{ $date->format('Y-m-d') }}"
                    class="border-r border-[#e4eff8] {{ $date->isToday() ? 'bg-[#0086da]' : 'bg-white' }}">
                    {{-- Date row --}}
                    <div class="flex flex-col items-center justify-center px-1 py-2.5">
                        <span
                            class="text-[10px] font-bold uppercase tracking-[.14em] {{ $date->isToday() ? 'text-white/80' : 'text-[#7a9db5]' }}">
                            {{ $date->format('D') }}
                        </span>
                        <span
                            class="mt-0.5 text-sm font-extrabold {{ $date->isToday() ? 'text-white' : 'text-[#1a2e3b]' }}">
                            {{ $date->format('M j') }}
                        </span>
                    </div>
                    {{-- Chair sub-headers --}}
                    <div
                        class="grid grid-cols-2 border-t {{ $date->isToday() ? 'border-white/20' : 'border-[#e4eff8]' }}">
                        <div
                            class="px-1 py-1 text-center text-[9px] font-bold uppercase tracking-[.12em] {{ $date->isToday() ? 'text-white/80' : 'text-[#7a9db5]' }}">
                            C1
                        </div>
                        <div
                            class="border-l px-1 py-1 text-center text-[9px] font-bold uppercase tracking-[.12em] {{ $date->isToday() ? 'border-white/20 text-white/80' : 'border-[#e4eff8] text-[#7a9db5]' }}">
                            C2
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── Mobile view ── --}}
        <div class="sm:hidden space-y-3 pt-3">
            @foreach ($weekDates as $date)
                @php
                    $mobileDayAppointments = $this->getAppointmentsForDay($date)->sortBy('start_time');
                @endphp
                <div wire:key="calendar-mobile-day-{{ $date->format('Y-m-d') }}"
                    class="border border-[#e4eff8] bg-white">
                    {{-- Mobile day header --}}
                    <div
                        class="flex items-center justify-between px-4 py-3 {{ $date->isToday() ? 'bg-[#0086da]' : 'bg-[#f6fafd]' }} border-b border-[#e4eff8]">
                        <div>
                            <p
                                class="text-[10px] font-bold uppercase tracking-[.14em] {{ $date->isToday() ? 'text-white/80' : 'text-[#7a9db5]' }}">
                                {{ $date->format('D') }}
                            </p>
                            <p class="text-sm font-extrabold {{ $date->isToday() ? 'text-white' : 'text-[#1a2e3b]' }}">
                                {{ $date->format('F j, Y') }}
                            </p>
                        </div>
                        @if ($date->isToday())
                            <span
                                class="bg-white/20 px-2.5 py-1 text-[10px] font-bold uppercase tracking-[.1em] text-white">Today</span>
                        @endif
                    </div>

                    <div class="p-3 space-y-2">
                        @forelse ($mobileDayAppointments as $appt)
                            @php
                                $statusColorMobile = match($appt->status) {
                                    'Ongoing'   => ['bg' => 'bg-amber-50',  'border' => 'border-amber-300',  'dot' => 'bg-amber-400',  'text' => 'text-amber-700'],
                                    'Scheduled' => ['bg' => 'bg-blue-50',   'border' => 'border-blue-200',   'dot' => 'bg-[#0086da]',  'text' => 'text-[#0086da]'],
                                    'Cancelled' => ['bg' => 'bg-red-50',    'border' => 'border-red-200',    'dot' => 'bg-red-500',    'text' => 'text-red-600'],
                                    'Waiting'   => ['bg' => 'bg-orange-50', 'border' => 'border-orange-200', 'dot' => 'bg-orange-400', 'text' => 'text-orange-600'],
                                    'Completed' => ['bg' => 'bg-green-50',  'border' => 'border-green-200',  'dot' => 'bg-green-500',  'text' => 'text-green-600'],
                                    default     => ['bg' => 'bg-slate-50',  'border' => 'border-slate-200',  'dot' => 'bg-slate-400',  'text' => 'text-slate-600'],
                                };
                                $displayStatusMobile = $appt->status === 'Waiting' ? 'Ready' : $appt->status;
                            @endphp
                            <button type="button" wire:key="mobile-appointment-{{ $appt->id }}"
                                @click="modalOpen = true" wire:click="viewAppointment({{ $appt->id }})"
                                class="w-full border {{ $statusColorMobile['border'] }} {{ $statusColorMobile['bg'] }} px-3.5 py-3 text-left transition hover:brightness-95">
                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <p class="text-sm font-bold text-[#1a2e3b] truncate">
                                        {{ $appt->last_name }}, {{ $appt->first_name }}
                                    </p>
                                    <span class="flex items-center gap-1 shrink-0">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $statusColorMobile['dot'] }}"></span>
                                        <span class="text-[10px] font-bold {{ $statusColorMobile['text'] }}">{{ $displayStatusMobile }}</span>
                                    </span>
                                </div>
                                <p class="text-xs text-[#3d5a6e] truncate">{{ $appt->service_name }}</p>
                                <p class="mt-1 text-[10px] font-semibold text-[#7a9db5]">
                                    {{ \Carbon\Carbon::parse($appt->start_time)->format('h:i A') }}
                                    – {{ \Carbon\Carbon::parse($appt->end_time)->format('h:i A') }}
                                </p>
                            </button>
                        @empty
                            <p class="border border-dashed border-[#d4e8f5] px-3 py-3 text-xs text-[#7a9db5] text-center">
                                No appointments yet.
                            </p>
                        @endforelse
                    </div>

                    <div class="px-3 pb-3">
                        <p class="mb-2 text-[10px] font-bold uppercase tracking-[.12em] text-[#7a9db5]">
                            {{ $isBlockMode ? 'Tap a slot to block / unblock' : 'Tap a slot to book' }}
                        </p>
                        <div class="grid grid-cols-3 gap-1.5">
                            @foreach ($timeSlots as $time)
                                @php
                                    $mobileBlockedSlot  = $this->getBlockedSlotAt($date->toDateString(), $time);
                                    $mobileIsBlocked    = $mobileBlockedSlot !== null;
                                    $mobileCanUnblock   = $this->canUnblockSlot($mobileBlockedSlot);
                                    $mobileIsOccupied   = $this->isSlotOccupied($date->toDateString(), $time);
                                    $mobileHasAppointments = $this->hasAppointmentsInSlot($date->toDateString(), $time);
                                @endphp
                                <button type="button"
                                    wire:key="mobile-slot-{{ $date->format('Y-m-d') }}-{{ str_replace(':', '-', $time) }}"
                                    @if ($isBlockMode) @if ($mobileIsBlocked && $mobileCanUnblock)
                                        @click="confirm('Unblock {{ $date->format('M d, Y') }} {{ \Carbon\Carbon::parse($mobileBlockedSlot->start_time)->format('h:i A') }} to {{ \Carbon\Carbon::parse($mobileBlockedSlot->end_time)->format('h:i A') }}?') && $wire.unblockSlot({{ $mobileBlockedSlot->id }})"
                                    @elseif(!$mobileHasAppointments && !$mobileIsBlocked)
                                        @click="confirm('Block {{ $date->format('M d, Y') }} {{ \Carbon\Carbon::parse($time)->format('h:i A') }} to {{ \Carbon\Carbon::parse($time)->addHour()->format('h:i A') }}?') && $wire.blockSlot('{{ $date->toDateString() }}', '{{ $time }}')" @endif
                                    @elseif($mobileIsBlocked)
                                        @click.prevent="showBlocked(
                                            @js($date->toDateString()),
                                            @js($time),
                                            @js($this->blockedByLabel($mobileBlockedSlot)),
                                            @js(trim((string) ($mobileBlockedSlot->reason ?? '')))
                                        )"
                                    @elseif(!$mobileIsOccupied) @click="modalOpen = true"
                                        wire:click="openAppointmentModal('{{ $date->toDateString() }}', '{{ $time }}')"
                                    @endif
                                    class="px-1 py-1.5 text-[10px] font-semibold transition
                                        {{ $mobileIsBlocked
                                            ? ($isBlockMode && !$mobileCanUnblock
                                                ? 'cursor-not-allowed border border-gray-200 bg-gray-100 text-gray-400'
                                                : 'border border-rose-200 bg-rose-50 text-rose-600 hover:bg-rose-100')
                                            : ($mobileIsOccupied
                                                ? 'cursor-not-allowed border border-[#e4eff8] bg-[#f6fafd] text-[#7a9db5]'
                                                : 'border border-[#d4e8f5] bg-white text-[#3d5a6e] hover:border-[#0086da] hover:bg-blue-50') }}">
                                    @if ($mobileIsBlocked)
                                        {{ $isBlockMode ? ($mobileCanUnblock ? 'Unblock' : 'Locked') : 'Blocked' }}
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

        {{-- ── Desktop grid ── --}}
        <div
            class="hidden sm:grid grid-cols-[90px_repeat(7,minmax(0,1fr))] lg:grid-cols-[110px_repeat(7,minmax(0,1fr))] w-full relative pt-4 z-10">

            @foreach ($timeSlots as $time)
                {{-- Time label cell --}}
                <div wire:key="calendar-time-label-{{ str_replace(':', '-', $time) }}"
                    class="relative h-28 border-t border-r border-gray-200 bg-white">
                    <span
                        class="absolute top-0 left-0 right-0 -mt-2.5 px-2 text-right text-[10px] lg:text-xs font-semibold text-[#7a9db5]">
                        {{ Carbon\Carbon::parse($time)->format('h:i A') }}
                    </span>
                    @if ($loop->last)
                        <span
                            class="absolute bottom-0 left-0 right-0 translate-y-1/2 px-2 text-right text-[10px] lg:text-xs font-semibold text-[#7a9db5]">
                            {{ Carbon\Carbon::parse($time)->addHour()->format('h:i A') }}
                        </span>
                    @endif
                </div>

                @foreach ($weekDates as $date)
                    @php
                        $slotDateString = $date->toDateString();
                    @endphp

                    <div wire:key="calendar-slot-{{ $date->format('Y-m-d') }}-{{ str_replace(':', '-', $time) }}"
                        class="relative h-28 border-t border-r border-gray-200 {{ $date->isToday() ? 'bg-[#f8fbfe]' : 'bg-white' }}">

                        <div class="absolute inset-0 grid grid-cols-2">
                            @foreach ([1, 2] as $chairId)
                                @php
                                    $chairBlockedSlot = $this->getBlockedSlotAt($slotDateString, $time, $chairId);
                                    $chairIsBlocked = $this->isChairBlocked($slotDateString, $time, $chairId);
                                    $chairCanUnblock = $this->canUnblockSlot($chairBlockedSlot);
                                    $chairHasAppointment = $this->hasAppointmentsInChairSlot($slotDateString, $time, $chairId);
                                    $laneIsAvailable = ! $chairIsBlocked && ! $chairHasAppointment;
                                @endphp
                                <button type="button"
                                    wire:key="calendar-chair-slot-{{ $slotDateString }}-{{ str_replace(':', '-', $time) }}-{{ $chairId }}"
                                    @if ($isBlockMode)
                                        @if ($chairIsBlocked && $chairCanUnblock && $chairBlockedSlot)
                                            @click="confirm('Unblock Chair {{ $chairId }} on {{ $date->format('M d, Y') }} {{ \Carbon\Carbon::parse($chairBlockedSlot->start_time)->format('h:i A') }} to {{ \Carbon\Carbon::parse($chairBlockedSlot->end_time)->format('h:i A') }}?') && $wire.unblockSlot({{ $chairBlockedSlot->id }})"
                                        @elseif(!$chairHasAppointment && !$chairIsBlocked)
                                            @click="confirm('Block Chair {{ $chairId }} on {{ $date->format('M d, Y') }} {{ \Carbon\Carbon::parse($time)->format('h:i A') }} to {{ \Carbon\Carbon::parse($time)->addHour()->format('h:i A') }}?') && $wire.blockSlot('{{ $slotDateString }}', '{{ $time }}', {{ $chairId }})"
                                        @endif
                                    @elseif($chairIsBlocked && $chairBlockedSlot)
                                        @click.prevent="showBlocked(
                                            @js($slotDateString . ' Chair ' . $chairId),
                                            @js($time),
                                            @js($this->blockedByLabel($chairBlockedSlot)),
                                            @js(trim((string) ($chairBlockedSlot->reason ?? '')))
                                        )"
                                    @elseif($laneIsAvailable)
                                        @click="modalOpen = true"
                                        wire:click="openAppointmentModal('{{ $slotDateString }}', '{{ $time }}')"
                                    @endif
                                    class="group/lane relative h-full w-full px-1 py-1 text-left transition
                                        {{ $chairId === 1 ? 'border-r border-gray-200' : '' }}
                                        @if ($chairIsBlocked && $isBlockMode && !$chairCanUnblock) cursor-not-allowed bg-gray-100
                                        @elseif ($chairIsBlocked) cursor-pointer bg-rose-50 hover:bg-rose-100
                                        @elseif ($laneIsAvailable) cursor-pointer hover:bg-[#f0f8ff]
                                        @else cursor-default @endif">
                                    @if ($chairIsBlocked && $chairBlockedSlot)
                                        <div class="pointer-events-none flex h-full flex-col justify-center px-1 py-1">
                                            <p class="text-[9px] font-bold uppercase tracking-[.1em] text-rose-600">
                                                {{ $isBlockMode ? ($chairCanUnblock ? 'Unblock' : 'Locked') : 'Blocked' }}
                                            </p>
                                            <p class="mt-0.5 text-[8px] text-rose-500">Chair {{ $chairId }}</p>
                                            @if (!empty($chairBlockedSlot->reason))
                                                <p class="hidden lg:block mt-0.5 text-[9px] text-rose-500 truncate">
                                                    {{ $chairBlockedSlot->reason }}
                                                </p>
                                            @endif
                                        </div>
                                    @elseif (!$chairHasAppointment)
                                        <div class="pointer-events-none absolute inset-0 flex items-center justify-center opacity-0 transition-opacity group-hover/lane:opacity-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                                fill="none" stroke="#0086da" stroke-width="2.5" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" />
                                            </svg>
                                        </div>
                                    @endif
                                </button>
                            @endforeach
                        </div>

                        {{-- Blocked overlay --}}
                        @if (false && $isBlocked)
                            <div class="pointer-events-none absolute inset-0 flex flex-col justify-center px-2 py-1.5">
                                <p class="text-[9px] lg:text-[10px] font-bold uppercase tracking-[.1em] text-rose-600">
                                    {{ $isBlockMode ? ($canUnblock ? '— Unblock —' : 'Locked') : 'Blocked' }}
                                </p>
                                @if (!empty($blockedSlot->reason))
                                    <p class="hidden lg:block mt-0.5 text-[9px] text-rose-500 truncate">
                                        {{ $blockedSlot->reason }}
                                    </p>
                                @endif
                                <p class="hidden lg:block mt-0.5 text-[9px] text-rose-400 truncate">
                                    {{ $this->blockedByLabel($blockedSlot) }}
                                </p>
                            </div>
                        @endif

                        {{-- Hover "+" hint for empty slots --}}
                        @if (false && !$isBlocked && !$isOccupied)
                            <div class="pointer-events-none absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="#0086da" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" />
                                </svg>
                            </div>
                        @endif
                    </div>
                @endforeach
            @endforeach

            {{-- ── Appointment overlay layer ── --}}
            <div
                class="absolute inset-x-0 bottom-0 top-4 grid grid-cols-[90px_repeat(7,minmax(0,1fr))] lg:grid-cols-[110px_repeat(7,minmax(0,1fr))] w-full pointer-events-none">
                <div class="h-full"></div>

                @foreach ($weekDates as $date)
                    <div wire:key="calendar-overlay-day-{{ $date->format('Y-m-d') }}"
                        class="relative h-full border-r border-transparent">
                        @php
                            $dayAppointments = $this->getAppointmentsForDay($date)->sortBy([
                                ['start_time', 'asc'],
                                ['duration_in_minutes', 'desc'],
                                ['id', 'asc'],
                            ]);
                            $dayStartHour        = 9;
                            $slotHeightRem       = 7; // matches h-28 (7rem)
                            $appointmentClusters = [];
                            $currentCluster      = null;

                            foreach ($dayAppointments as $appointment) {
                                $appointmentStart = Carbon\Carbon::parse($appointment->start_time)->seconds(0);
                                $appointmentEnd   = Carbon\Carbon::parse($appointment->end_time)->seconds(0);

                                if ($currentCluster === null || $appointmentStart->greaterThanOrEqualTo($currentCluster['end'])) {
                                    if ($currentCluster !== null) {
                                        $appointmentClusters[] = $currentCluster;
                                    }
                                    $currentCluster = [
                                        'items' => [$appointment],
                                        'end'   => $appointmentEnd,
                                    ];
                                } else {
                                    $currentCluster['items'][] = $appointment;
                                    if ($appointmentEnd->greaterThan($currentCluster['end'])) {
                                        $currentCluster['end'] = $appointmentEnd;
                                    }
                                }
                            }

                            if ($currentCluster !== null) {
                                $appointmentClusters[] = $currentCluster;
                            }

                            $positionedAppointments = collect();

                            foreach ($appointmentClusters as $cluster) {
                                $laneEnds            = [];
                                $clusterAppointments = [];

                                foreach ($cluster['items'] as $appointment) {
                                    $appointmentStart = Carbon\Carbon::parse($appointment->start_time)->seconds(0);
                                    $appointmentEnd   = Carbon\Carbon::parse($appointment->end_time)->seconds(0);
                                    $laneIndex        = null;

                                    foreach ($laneEnds as $index => $laneEnd) {
                                        if ($appointmentStart->greaterThanOrEqualTo($laneEnd)) {
                                            $laneIndex = $index;
                                            break;
                                        }
                                    }

                                    if ($laneIndex === null) {
                                        $laneIndex = count($laneEnds);
                                    }

                                    $laneEnds[$laneIndex]    = $appointmentEnd;
                                    $clusterAppointments[]   = [
                                        'appointment' => $appointment,
                                        'lane_index'  => $laneIndex,
                                    ];
                                }

                                $laneCount = max(1, count($laneEnds));

                                foreach ($clusterAppointments as $clusterAppointment) {
                                    $clusterAppointment['lane_count'] = $laneCount;
                                    $positionedAppointments->push((object) $clusterAppointment);
                                }
                            }
                        @endphp

                        @foreach ($positionedAppointments as $positionedAppointment)
                            @php
                                $appt           = $positionedAppointment->appointment;
                                $startCarbon    = Carbon\Carbon::parse($appt->start_time);
                                $topInMinutes   = ($startCarbon->hour - $dayStartHour) * 60 + $startCarbon->minute;
                                $topPositionRem = (max(0, $topInMinutes) / 60) * $slotHeightRem;
                                $heightInRem    = max(1.75, ((int) $appt->duration_in_minutes / 60) * $slotHeightRem - 0.25);
                                $laneIndex      = (int) $positionedAppointment->lane_index;
                                $laneCount      = max(1, (int) $positionedAppointment->lane_count);
                                $renderedLaneCount = max(2, $laneCount);
                                $laneGapRem     = 0.2;
                                $cardWidthStyle = $renderedLaneCount > 1
                                    ? "calc((100% - " . (($renderedLaneCount - 1) * $laneGapRem) . "rem) / {$renderedLaneCount})"
                                    : '100%';
                                $cardLeftStyle  = $renderedLaneCount > 1
                                    ? "calc({$laneIndex} * ({$cardWidthStyle} + {$laneGapRem}rem))"
                                    : '0px';

                                // Status-based card colors
                                $cardColors = match($appt->status) {
                                    'Ongoing'   => ['bg' => '#fffbeb', 'border' => '#fbbf24', 'accent' => '#d97706', 'label' => '#92400e', 'name' => '#1a2e3b'],
                                    'Cancelled' => ['bg' => '#fff1f2', 'border' => '#fca5a5', 'accent' => '#ef4444', 'label' => '#991b1b', 'name' => '#1a2e3b'],
                                    'Waiting'   => ['bg' => '#fff7ed', 'border' => '#fdba74', 'accent' => '#f97316', 'label' => '#9a3412', 'name' => '#1a2e3b'],
                                    'Completed' => ['bg' => '#f0fdf4', 'border' => '#86efac', 'accent' => '#22c55e', 'label' => '#166534', 'name' => '#1a2e3b'],
                                    default     => ['bg' => '#eff7ff', 'border' => '#93c5fd', 'accent' => '#0086da', 'label' => '#1e40af', 'name' => '#1a2e3b'],
                                };
                                $displayStatus = $appt->status === 'Waiting' ? 'Ready' : $appt->status;
                            @endphp

                            <div wire:key="calendar-appt-overlay-{{ $date->format('Y-m-d') }}-{{ $appt->id }}"
                                class="calendar-overlap-card absolute pointer-events-auto"
                                data-lane-index="{{ $laneIndex }}"
                                data-lane-count="{{ $renderedLaneCount }}"
                                style="top: {{ $topPositionRem }}rem; height: {{ $heightInRem }}rem; left: {{ $cardLeftStyle }}; width: {{ $cardWidthStyle }}; z-index: 10; padding: 0 2px;">

                                <button type="button" wire:key="appointment-stack-{{ $appt->id }}"
                                    @click="modalOpen = true"
                                    wire:click="viewAppointment({{ $appt->id }})"
                                    class="w-full h-full text-left overflow-hidden transition-all hover:brightness-95 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-[#0086da]"
                                    style="
                                        background-color: {{ $cardColors['bg'] }};
                                        border: 1.5px solid {{ $cardColors['border'] }};
                                        border-left: 3px solid {{ $cardColors['accent'] }};
                                        border-radius: 4px;
                                        box-shadow: 0 1px 3px 0 rgba(0,0,0,.06);
                                    ">
                                    <div class="flex flex-col h-full p-1.5 min-h-0">
                                        {{-- Chair label --}}
                                        <div class="flex items-center gap-1 mb-0.5 shrink-0">
                                            <span class="text-[8px] lg:text-[9px] font-black uppercase tracking-[.1em]"
                                                style="color: {{ $cardColors['accent'] }}">
                                                C{{ $laneIndex + 1 }}
                                            </span>
                                            <span class="h-px flex-1" style="background: {{ $cardColors['border'] }}"></span>
                                        </div>

                                        {{-- Patient name --}}
                                        <p class="text-[10px] lg:text-[11px] font-extrabold leading-tight truncate"
                                            style="color: {{ $cardColors['name'] }}">
                                            {{ $appt->last_name }},
                                            {{ $appt->first_name }}
                                        </p>

                                        {{-- Service name --}}
                                        <p class="mt-0.5 text-[9px] lg:text-[10px] leading-tight truncate"
                                            style="color: {{ $cardColors['accent'] }}; opacity: 0.85">
                                            {{ $appt->service_name }}
                                        </p>

                                        {{-- Time & status at bottom --}}
                                        <div class="mt-auto pt-1 shrink-0">
                                            <p class="text-[9px] font-semibold truncate"
                                                style="color: {{ $cardColors['accent'] }}">
                                                {{ $startCarbon->format('g:i A') }}–{{ Carbon\Carbon::parse($appt->end_time)->format('g:i A') }}
                                            </p>
                                            <div class="flex items-center gap-1 mt-0.5">
                                                <span class="h-1.5 w-1.5 rounded-full shrink-0"
                                                    style="background: {{ $cardColors['accent'] }}"></span>
                                                <span class="text-[9px] font-bold truncate"
                                                    style="color: {{ $cardColors['label'] }}">
                                                    {{ $displayStatus }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
