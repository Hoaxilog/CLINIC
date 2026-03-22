<div>
    @if ($showModal && $patient)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4 sm:p-8" x-data="{}">
            <div class="mx-auto flex max-h-[92vh] min-h-[60vh] w-full max-w-3xl flex-col overflow-hidden rounded-sm bg-white shadow-2xl"
                style="font-family:'Montserrat',sans-serif;">

                {{-- Header --}}
                <div class="flex shrink-0 items-start justify-between border-b border-gray-200 px-6 py-5 sm:px-8">
                    <div>
                        <div class="flex items-center gap-2 text-[.6rem] font-bold uppercase tracking-[.2em] text-[#0086da]">
                            <span class="block h-[2px] w-[18px] bg-[#0086da]"></span>
                            Appointment History
                        </div>
                        <h2 class="mt-1 text-lg font-extrabold leading-tight text-[#1a2e3b] sm:text-xl">
                            {{ $patient->first_name }} {{ $patient->last_name }}
                        </h2>
                        <div class="mt-2 flex items-center gap-2">
                            <span class="inline-flex items-center rounded-sm border border-[#d4e8f5] bg-[#e8f4fc] px-2.5 py-1 text-[10px] font-bold uppercase tracking-[.12em] text-[#0086da]">
                                {{ count($appointmentHistory) }} {{ count($appointmentHistory) === 1 ? 'record' : 'records' }}
                            </span>
                        </div>
                    </div>
                    <button wire:click="closeModal"
                        class="inline-flex items-center gap-2 rounded-sm border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-[#1a2e3b] transition hover:bg-[#f6fafd]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/>
                        </svg>
                        Go back
                    </button>
                </div>

                {{-- Body --}}
                <div class="flex-1 overflow-y-auto bg-[#f6fafd] px-4 py-5 sm:px-6">
                    @if (count($appointmentHistory) > 0)
                        <div class="relative space-y-0">
                            {{-- Timeline line --}}
                            <div class="absolute left-[19px] top-5 bottom-5 w-[2px] bg-[#d4e8f5]"></div>

                            @foreach ($appointmentHistory as $appt)
                                @php
                                    $appointmentAt = \Carbon\Carbon::parse($appt->appointment_date);
                                    $durationMinutes = (int) ($appt->duration_minutes ?? 0);
                                    $serviceLabel = $appt->service_name ?: 'N/A';
                                    $rawStatus = trim((string) ($appt->status ?? 'Unknown'));
                                    $normalizedStatus = strtolower($rawStatus);
                                    $displayStatus = $normalizedStatus === 'scheduled' ? 'Upcoming' : ($rawStatus ?: 'Unknown');
                                    $dotColor = match($normalizedStatus) {
                                        'completed'            => 'bg-[#0086da] border-[#0086da]',
                                        'scheduled', 'upcoming'=> 'bg-emerald-500 border-emerald-500',
                                        'cancelled', 'canceled'=> 'bg-red-400 border-red-400',
                                        'no-show', 'noshow'    => 'bg-amber-400 border-amber-400',
                                        default                => 'bg-gray-300 border-gray-300',
                                    };
                                    $statusBadge = match($normalizedStatus) {
                                        'completed'            => 'border-[#d4e8f5] bg-[#e8f4fc] text-[#0086da]',
                                        'scheduled', 'upcoming'=> 'border-emerald-200 bg-emerald-50 text-emerald-700',
                                        'cancelled', 'canceled'=> 'border-red-200 bg-red-50 text-red-600',
                                        'no-show', 'noshow'    => 'border-amber-200 bg-amber-50 text-amber-700',
                                        default                => 'border-gray-200 bg-gray-100 text-gray-600',
                                    };
                                @endphp

                                <div wire:key="appointment-history-{{ $appt->id ?? $loop->index }}"
                                    class="relative flex gap-4 pb-4 last:pb-0">
                                    {{-- Timeline dot --}}
                                    <div class="relative z-10 mt-4 flex h-10 w-10 shrink-0 items-center justify-center">
                                        <span class="flex h-4 w-4 items-center justify-center rounded-full border-2 bg-white {{ $dotColor }}">
                                            <span class="h-1.5 w-1.5 rounded-full {{ $dotColor }}"></span>
                                        </span>
                                    </div>

                                    {{-- Card --}}
                                    <div class="flex-1 rounded-sm border border-gray-200 bg-white p-4 shadow-sm">
                                        <div class="flex flex-wrap items-start justify-between gap-2">
                                            <div>
                                                <div class="text-sm font-bold text-[#1a2e3b]">
                                                    {{ $appointmentAt->format('M d, Y') }}
                                                    <span class="ml-1 font-normal text-[#587189]">·</span>
                                                    <span class="ml-1 font-semibold text-[#0086da]">{{ $appointmentAt->format('h:i A') }}</span>
                                                    @if ($durationMinutes > 0)
                                                        <span class="ml-1 text-xs font-normal text-gray-400">({{ $durationMinutes }} min)</span>
                                                    @endif
                                                </div>
                                                <div class="mt-1 text-xs text-[#587189]">{{ $serviceLabel }}</div>
                                            </div>
                                            <span class="inline-flex items-center rounded-sm border px-2.5 py-1 text-[10px] font-bold uppercase tracking-[.12em] {{ $statusBadge }}">
                                                {{ $displayStatus }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex h-48 flex-col items-center justify-center rounded-sm border border-dashed border-gray-300 bg-white text-center text-gray-400">
                            <svg class="mb-3 h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-sm font-medium text-gray-500">No appointment history found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
