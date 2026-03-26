<div style="font-family:'Montserrat',sans-serif; -webkit-font-smoothing:antialiased;">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap');
        .appt-history-wrap * { font-family: 'Montserrat', sans-serif; }
    </style>

    <div class="appt-history-wrap space-y-5">

        {{-- ── Page Header ── --}}
        <div class="border border-[#b8d4e8] bg-white">
            <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-5 md:px-8">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-[#0086da] flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none"
                            stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="0" />
                            <path d="M16 2v4M8 2v4M3 10h18" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-[.6rem] font-bold uppercase tracking-[.22em] text-[#0086da]">Appointments</div>
                        <div class="text-[1.05rem] font-extrabold tracking-[-0.02em] text-[#1a2e3b]">Appointment History</div>
                    </div>
                </div>
                <div class="text-[.68rem] font-semibold uppercase tracking-[.12em] text-[#7a9db5]">
                    Completed &amp; cancelled records
                </div>
            </div>
        </div>

        {{-- ── Filter Bar ── --}}
        <div class="border border-[#b8d4e8] bg-white">
            <div class="flex items-center gap-3 px-5 py-3.5 border-b border-[#b8d4e8]">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
                    stroke="#0086da" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                </svg>
                <span class="text-[.6rem] font-bold uppercase tracking-[.2em] text-[#0086da]">Filters</span>
                <div wire:loading class="ml-auto text-[.62rem] font-bold uppercase tracking-[.14em] text-[#0086da] animate-pulse">
                    Updating...
                </div>
            </div>

            <div class="grid grid-cols-1 gap-0 md:grid-cols-2 xl:grid-cols-5 divide-y md:divide-y-0 md:divide-x divide-[#b8d4e8]">

                {{-- Search --}}
                <div class="px-5 py-4">
                    <label class="block text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5] mb-2">Search</label>
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
                            stroke="#9bbdd0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                        </svg>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Patient, service, or ID"
                            class="w-full border border-[#9bbdd0] bg-white py-2.5 pl-9 pr-3 text-[.8rem] text-[#1a2e3b] placeholder-[#9bbdd0] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#cce9f8]">
                    </div>
                </div>

                {{-- Status --}}
                <div class="px-5 py-4">
                    <label class="block text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5] mb-2">Status</label>
                    <div class="relative">
                        <select wire:model.live="status"
                            class="w-full appearance-none border border-[#9bbdd0] bg-white py-2.5 pl-4 pr-9 text-[.8rem] text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#cce9f8] cursor-pointer">
                            <option value="All">All</option>
                            <option value="Cancelled">Cancelled</option>
                            <option value="Completed">Completed</option>
                        </select>
                        <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#7a9db5" stroke-width="2.5" stroke-linecap="square">
                                <path d="M6 9l6 6 6-6"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Service --}}
                <div class="px-5 py-4">
                    <label class="block text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5] mb-2">Service</label>
                    <div class="relative">
                        <select wire:model.live="serviceId"
                            class="w-full appearance-none border border-[#9bbdd0] bg-white py-2.5 pl-4 pr-9 text-[.8rem] text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#cce9f8] cursor-pointer">
                            <option value="">All services</option>
                            @foreach ($serviceOptions as $id => $label)
                                <option value="{{ $id }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#7a9db5" stroke-width="2.5" stroke-linecap="square">
                                <path d="M6 9l6 6 6-6"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- From Date --}}
                <div class="px-5 py-4">
                    <label class="block text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5] mb-2">From</label>
                    <input type="date" wire:model.live="fromDate"
                        class="w-full border border-[#9bbdd0] bg-white py-2.5 px-4 text-[.8rem] text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#cce9f8]">
                </div>

                {{-- To Date + Reset --}}
                <div class="px-5 py-4">
                    <label class="block text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5] mb-2">To</label>
                    <div class="flex gap-2">
                        <input type="date" wire:model.live="toDate"
                            class="min-w-0 flex-1 border border-[#9bbdd0] bg-white py-2.5 px-4 text-[.8rem] text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#cce9f8]">
                        <button type="button" wire:click="resetFilters"
                            class="shrink-0 inline-flex items-center gap-1.5 border border-[#9bbdd0] bg-white px-3 py-2.5 text-[.62rem] font-bold uppercase tracking-[.14em] text-[#7a9db5] transition hover:border-[#0086da] hover:text-[#0086da]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                                <path d="M3 3v5h5"/>
                            </svg>
                            Reset
                        </button>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── Results Table ── --}}
        <div class="border border-[#b8d4e8] bg-white">

            {{-- Results header --}}
            <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-[#b8d4e8]">
                <div>
                    <div class="text-[.55rem] font-bold uppercase tracking-[.2em] text-[#7a9db5]">Results</div>
                    <div class="mt-0.5 text-[.82rem] font-bold text-[#1a2e3b]">
                        {{ $appointments->total() }}
                        <span class="font-semibold text-[#3d5a6e]">appointment {{ Str::plural('record', $appointments->total()) }} found</span>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm" style="font-family:'Montserrat',sans-serif;">
                    <thead class="border-b border-[#b8d4e8] bg-[#f6fafd]">
                        <tr>
                            <th class="px-5 py-3.5 text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5] whitespace-nowrap">Appointment</th>
                            <th class="px-5 py-3.5 text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5] whitespace-nowrap">Patient</th>
                            <th class="px-5 py-3.5 text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5] whitespace-nowrap">Service</th>
                            <th class="px-5 py-3.5 text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5] whitespace-nowrap">Status</th>
                            <th class="px-5 py-3.5 text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5] whitespace-nowrap">Cancellation Reason</th>
                            <th class="px-5 py-3.5 text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5] whitespace-nowrap">Status Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#dbe9f2]">
                        @forelse ($appointments as $appointment)
                            @php
                                $isCompleted = $appointment->status === 'Completed';
                                $isCancelled = $appointment->status === 'Cancelled';
                                $statusBadge = match($appointment->status) {
                                    'Cancelled'            => 'bg-rose-50 text-rose-700 border border-rose-200',
                                    'Completed'            => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                    'Scheduled'            => 'bg-[#eaf5fe] text-[#0086da] border border-[#cde8fb]',
                                    'Pending'              => 'bg-amber-50 text-amber-700 border border-amber-200',
                                    'Waiting', 'Ongoing'   => 'bg-indigo-50 text-indigo-700 border border-indigo-200',
                                    default                => 'bg-[#f6fafd] text-[#7a9db5] border border-[#e4eff8]',
                                };
                            @endphp
                            <tr class="align-top transition hover:bg-[#f8fbfe]">

                                {{-- Appointment ID + Date --}}
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="mt-0.5 text-[.7rem] font-semibold text-[#7a9db5]">
                                        {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}
                                    </div>
                                    <div class="text-[.68rem] text-[#9bbdd0]">
                                        {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('h:i A') }}
                                    </div>
                                </td>

                                {{-- Patient --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex h-7 w-7 flex-shrink-0 items-center justify-center bg-[#e8f4fc]">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                                                fill="none" stroke="#0086da" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                                <circle cx="12" cy="7" r="4"/>
                                            </svg>
                                        </div>
                                        <div class="text-[.82rem] font-bold text-[#1a2e3b]">{{ $appointment->patient_name }}</div>
                                    </div>
                                </td>

                                {{-- Service --}}
                                <td class="px-5 py-4">
                                    <div class="text-[.82rem] font-semibold text-[#1a2e3b]">
                                        {{ $appointment->service_name ?: '—' }}
                                    </div>
                                </td>

                                {{-- Status badge --}}
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 text-[.6rem] font-bold uppercase tracking-[.14em] {{ $statusBadge }}">
                                        @if ($isCompleted)
                                            <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="mr-1.5">
                                                <path d="M20 6 9 17l-5-5"/>
                                            </svg>
                                        @elseif ($isCancelled)
                                            <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="mr-1.5">
                                                <path d="M18 6 6 18M6 6l12 12"/>
                                            </svg>
                                        @endif
                                        {{ $appointment->status }}
                                    </span>
                                </td>

                                {{-- Cancellation Reason --}}
                                <td class="px-5 py-4 max-w-[220px]">
                                    @if ($isCancelled && $appointment->reason_label)
                                        <div class="border-l-2 border-rose-300 bg-rose-50 px-3 py-2">
                                            <div class="text-[.7rem] leading-relaxed text-rose-700">{{ $appointment->reason_label }}</div>
                                        </div>
                                    @else
                                        <span class="text-[.75rem] text-[#9bbdd0]">—</span>
                                    @endif
                                </td>

                                {{-- Status Time --}}
                                <td class="px-5 py-4 whitespace-nowrap">
                                    @if (in_array($appointment->status, ['Cancelled', 'Completed'], true) && $appointment->updated_at)
                                        <div class="text-[.78rem] font-bold text-[#1a2e3b]">
                                            {{ \Carbon\Carbon::parse($appointment->updated_at)->diffForHumans() }}
                                        </div>
                                        <div class="mt-0.5 text-[.68rem] text-[#7a9db5]">
                                            {{ \Carbon\Carbon::parse($appointment->updated_at)->format('M d, Y h:i A') }}
                                        </div>
                                    @else
                                        <span class="text-[.75rem] text-[#9bbdd0]">—</span>
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <div class="mx-auto max-w-sm border border-dashed border-[#9bbdd0] bg-[#f8fbfe] px-6 py-8">
                                        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center bg-[#e8f4fc]">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                                                stroke="#0086da" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="3" y="4" width="18" height="18" rx="0"/>
                                                <path d="M16 2v4M8 2v4M3 10h18"/>
                                                <path d="m9 14 2 2 4-4" stroke-linecap="round"/>
                                            </svg>
                                        </div>
                                        <p class="text-[.82rem] font-bold text-[#1a2e3b]">No records matched the current filters</p>
                                        <p class="mt-1.5 text-[.75rem] leading-relaxed text-[#7a9db5]">Try broadening the date range or switch the status filter to view more records.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($appointments->hasPages())
                <div class="border-t border-[#b8d4e8] px-5 py-4">
                    {{ $appointments->links() }}
                </div>
            @endif

        </div>

    </div>
</div>
