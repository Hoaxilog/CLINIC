@if ($activeTab === 'calendar')
    @if ($isBlockMode)
        <div
            class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800">
            Block mode is active. Select an available slot to block, or tap a blocked slot to unblock it.
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

        {{-- Mobile view --}}
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
                            {{ $isBlockMode ? 'Tap a time to block or unblock' : 'Tap a time to book' }}
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
                                            wire:click="unblockSlot({{ $mobileBlockedSlot->id }})"
                                            wire:confirm="Unblock {{ $date->format('M d, Y') }} {{ \Carbon\Carbon::parse($mobileBlockedSlot->start_time)->format('h:i A') }} to {{ \Carbon\Carbon::parse($mobileBlockedSlot->end_time)->format('h:i A') }}?"
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
                                            {{ $isBlockMode ? 'Unblock' : 'Blocked' }}
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

        {{-- Desktop grid --}}
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
                                wire:click="unblockSlot({{ $blockedSlot->id }})"
                                wire:confirm="Unblock {{ $date->format('M d, Y') }} {{ \Carbon\Carbon::parse($blockedSlot->start_time)->format('h:i A') }} to {{ \Carbon\Carbon::parse($blockedSlot->end_time)->format('h:i A') }}?"
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
                                <p class="text-[10px] md:text-xs font-semibold uppercase tracking-wide">
                                    {{ $isBlockMode ? 'Unblock' : 'Blocked' }}
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

            {{-- Appointment overlays --}}
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
