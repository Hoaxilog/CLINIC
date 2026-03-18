<div>
    @if($showModal && $patient)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4 sm:p-8" x-data="{}">
            <div class="mx-auto flex max-h-[92vh] min-h-[80vh] w-full max-w-6xl flex-col overflow-hidden rounded-xl bg-white shadow-xl">
                <div class="flex-none border-b border-gray-200 p-6 sm:p-8">
                    <div class="mb-5 flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                                {{ $patient->first_name }} {{ $patient->last_name }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-500">Appointment history timeline</p>
                        </div>

                        <button
                            wire:click="closeModal"
                            class="inline-flex items-center gap-2 rounded-none bg-[#F06565] px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-[#e85959] active:outline-2 active:outline-dashed active:outline-offset-2 active:outline-black"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/>
                            </svg>
                            Go back
                        </button>
                    </div>

                    <div class="flex items-center gap-3">
                        <h3 class="text-lg font-semibold text-gray-900">Appointment history</h3>
                        <span class="inline-flex items-center rounded-none bg-[#eaf5fe] px-2 py-1 text-xs font-semibold uppercase tracking-wide text-[#0079c5]">
                            {{ count($appointmentHistory) }} record{{ count($appointmentHistory) === 1 ? '' : 's' }}
                        </span>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto bg-gray-50 p-4 sm:p-6">
                    @if(count($appointmentHistory) > 0)
                        <div class="space-y-3">
                            @foreach($appointmentHistory as $appt)
                                @php
                                    $appointmentAt = \Carbon\Carbon::parse($appt->appointment_date);
                                    $durationMinutes = (int) ($appt->duration_minutes ?? 0);
                                    $serviceLabel = $appt->service_name ?: 'N/A';
                                    $rawStatus = trim((string) ($appt->status ?? 'Unknown'));
                                    $normalizedStatus = strtolower($rawStatus);
                                    $displayStatus = $normalizedStatus === 'scheduled' ? 'Upcoming' : ($rawStatus ?: 'Unknown');
                                    $statusBadgeClass = match($normalizedStatus) {
                                        'completed' => 'border-blue-200 bg-blue-50 text-blue-700',
                                        'scheduled', 'upcoming' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                                        'cancelled', 'canceled' => 'border-red-200 bg-red-50 text-red-700',
                                        'no-show', 'noshow' => 'border-amber-200 bg-amber-50 text-amber-700',
                                        default => 'border-gray-200 bg-gray-100 text-gray-700',
                                    };
                                @endphp

                                <div wire:key="appointment-history-{{ $appt->id ?? $loop->index }}" class="rounded-none border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
                                    <div class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2 lg:grid-cols-4">
                                        <div>
                                            <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Date</p>
                                            <p class="mt-1 text-base font-semibold text-gray-900">{{ $appointmentAt->format('M d, Y') }}</p>
                                        </div>

                                        <div>
                                            <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Time</p>
                                            <p class="mt-1 text-base font-semibold text-gray-900">
                                                {{ $appointmentAt->format('h:i A') }}
                                                @if($durationMinutes > 0)
                                                    <span class="text-sm font-normal text-gray-500">({{ $durationMinutes }} min)</span>
                                                @endif
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Services</p>
                                            <p class="mt-1 truncate text-base font-semibold text-gray-900" title="{{ $serviceLabel }}">{{ $serviceLabel }}</p>
                                        </div>

                                        <div>
                                            <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Status</p>
                                            <div class="mt-1">
                                                <span class="inline-flex items-center rounded-none border px-2.5 py-1 text-xs font-semibold uppercase tracking-wide {{ $statusBadgeClass }}">
                                                    {{ $displayStatus }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex h-64 flex-col items-center justify-center rounded-none border border-dashed border-gray-300 bg-white text-gray-400">
                            <svg class="mb-3 h-14 w-14 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                            <p class="text-base font-medium text-gray-500">No appointment history found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
