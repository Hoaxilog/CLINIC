<div class="h-full" x-data="{
    serverModalOpen: @entangle('showAppointmentModal').live,
    modalOpen: false,
    openingPatientForm: false,
    resumeAppointmentModalOnPatientFormClose: false,
    init() {
        this.modalOpen = this.serverModalOpen;
        this.$watch('serverModalOpen', value => {
            this.modalOpen = value;
        });
    }
}" x-init="init()"
    x-on:patient-form-opened.window="openingPatientForm = false; modalOpen = false"
    x-on:patient-form-closed.window="openingPatientForm = false; if (resumeAppointmentModalOnPatientFormClose) { modalOpen = true; resumeAppointmentModalOnPatientFormClose = false }"
    x-on:patient-form-open-failed.window="openingPatientForm = false; resumeAppointmentModalOnPatientFormClose = false">

    @php
        $cancelledAppointments = $todayAppointments->where('status', 'Cancelled');
        $scheduledAppointments = $todayAppointments->where('status', 'Scheduled');
    @endphp

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-4 h-[calc(100vh-280px)] min-h-[600px]">

        <div @if (!$showAppointmentModal && !$isPatientFormOpen) wire:poll.10s="refreshTodaySchedule" @endif
            class="flex flex-col min-h-0 overflow-hidden rounded-none border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-4 py-3.5">
                <div class="flex items-center gap-2">
                    <h1 class="text-sm font-bold uppercase tracking-wide text-slate-800">Scheduled Today</h1>
                </div>
                <div class="flex items-center gap-2">
                    <span
                        class="rounded-full border border-slate-200 bg-white px-2.5 py-1 text-[11px] font-bold text-slate-700">{{ count($scheduledAppointments) }}</span>
                </div>
            </div>

            <div
                class="flex-1 space-y-3 overflow-y-auto p-3.5 scrollbar-thin scrollbar-track-transparent scrollbar-thumb-slate-300">
                @if (count($scheduledAppointments) > 0)
                    @foreach ($scheduledAppointments as $app)
                        <div wire:key="today-{{ $app->id }}-{{ $app->status }}"
                            x-on:click="modalOpen = true"
                            wire:click="viewAppointment({{ $app->id }})"
                            class="group cursor-pointer rounded-none border border-slate-200 bg-slate-50 p-4 transition hover:-translate-y-0.5 hover:border-slate-300 hover:bg-white hover:shadow-md">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h2 class="truncate text-sm font-bold text-slate-900">{{ $app->first_name }}
                                        {{ $app->last_name }}</h2>
                                    <p class="mt-0.5 truncate text-xs text-slate-500">{{ $app->service_name }}</p>
                                </div>
                                <span
                                    class="rounded-full border border-slate-200 bg-white px-2.5 py-1 text-[11px] font-bold text-slate-700 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($app->appointment_date)->format('h:i A') }}
                                </span>
                            </div>

                            <div class="mt-3 flex items-center justify-between border-t border-slate-200 pt-3">
                                <span
                                    class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide bg-slate-200 text-slate-700">
                                    {{ $app->status }}
                                </span>
                                <span
                                    class="text-xs font-semibold text-slate-700 opacity-0 transition-opacity group-hover:opacity-100">View
                                    Details -&gt;</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div
                        class="flex h-full flex-col items-center justify-center text-center text-sm text-slate-400 px-6">
                        <svg class="mb-2 h-10 w-10 text-slate-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <p class="font-semibold text-slate-500">No scheduled appointments today.</p>
                        <p class="mt-1 text-xs">Scheduled entries will show here.</p>
                    </div>
                @endif
            </div>
        </div>

        <div @if (!$showAppointmentModal && !$isPatientFormOpen) wire:poll.10s="refreshWaitingQueue" @endif
            class="flex flex-col min-h-0 overflow-hidden rounded-none border border-amber-300 bg-amber-50/50 shadow-md">
            <div class="flex items-center justify-between border-b border-amber-200 bg-amber-100/70 px-4 py-3.5">
                <div class="flex items-center gap-2">
                    <h1 class="text-sm font-bold uppercase tracking-wide text-amber-900">Lobby Waiting</h1>
                </div>
                <span
                    class="rounded-full border border-amber-300 bg-white px-2.5 py-1 text-[11px] font-bold text-amber-700">{{ count($waitingQueue) }}</span>
            </div>

            <div
                class="flex-1 space-y-3 overflow-y-auto p-3.5 scrollbar-thin scrollbar-track-transparent scrollbar-thumb-amber-200">
                @if (count($waitingQueue) > 0)
                    @foreach ($waitingQueue as $wait)
                        <div wire:key="wait-{{ $wait->id }}-{{ $wait->status }}"
                            x-on:click="modalOpen = true"
                            wire:click="viewAppointment({{ $wait->id }})"
                            class="group cursor-pointer rounded-none border bg-white p-4 transition hover:-translate-y-0.5 hover:border-amber-300 hover:shadow-md {{ $loop->first ? 'border-amber-300 ring-1 ring-amber-100 shadow-md' : 'border-amber-200 shadow-sm' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h2 class="truncate text-sm font-bold text-slate-900">{{ $wait->first_name }}
                                            {{ $wait->last_name }}</h2>
                                        <span
                                            class="rounded-full px-2.5 py-1 text-[9px] font-bold uppercase tracking-wide {{ $loop->first ? 'bg-amber-200 text-amber-800' : 'bg-amber-100 text-amber-700' }}">{{ $loop->first ? 'Up Next' : 'Waiting' }}</span>
                                    </div>
                                    <p class="mt-0.5 truncate text-xs text-slate-500">
                                        {{ $wait->service_name ?? 'Procedure pending' }}</p>
                                </div>
                            </div>

                            <div
                                class="mt-3 rounded-none border border-amber-100 bg-amber-50 px-3 py-2 text-xs text-amber-900">
                                Waiting Time: <span
                                    class="font-semibold">{{ \Carbon\Carbon::parse($wait->waited_at)->diffForHumans(null, true) }}</span>
                            </div>

                            <div class="mt-3 flex items-center justify-between border-t border-slate-200 pt-3">
                                <span
                                    class="rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-amber-700">Ready</span>
                                <span
                                    class="text-xs font-semibold text-slate-700 opacity-0 transition-opacity group-hover:opacity-100">View
                                    Details -&gt;</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div
                        class="flex h-full flex-col items-center justify-center text-center text-sm text-amber-700 px-6">
                        <svg class="mb-2 h-10 w-10 text-amber-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <p class="font-semibold">No patients in lobby waiting.</p>
                        <p class="mt-1 text-xs text-amber-600">Arrived patients will appear here.</p>
                    </div>
                @endif
            </div>
        </div>

        <div @if (!$showAppointmentModal && !$isPatientFormOpen) wire:poll.5s="refreshOngoingAppointments" @endif
            class="flex flex-col min-h-0 overflow-hidden rounded-none border border-emerald-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-emerald-200 bg-emerald-50 px-4 py-3.5">
                <div class="flex items-center gap-2">
                    <h1 class="text-sm font-bold uppercase tracking-wide text-emerald-900">In Session</h1>
                </div>
                <span
                    class="rounded-full border border-emerald-200 bg-white px-2.5 py-1 text-[11px] font-bold text-emerald-700">{{ count($ongoingAppointments) }}</span>
            </div>

            <div
                class="flex-1 space-y-3 overflow-y-auto p-3.5 scrollbar-thin scrollbar-track-transparent scrollbar-thumb-emerald-200">
                @if (count($ongoingAppointments) > 0)
                    @foreach ($ongoingAppointments as $ongoing)
                        <div wire:key="ongoing-{{ $ongoing->id }}-{{ $ongoing->status }}"
                            x-on:click="modalOpen = true"
                            wire:click="viewAppointment({{ $ongoing->id }})"
                            class="group cursor-pointer rounded-none border border-emerald-200 bg-emerald-50/40 p-4 shadow-sm transition hover:-translate-y-0.5 hover:bg-white hover:shadow-md">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h2 class="truncate text-sm font-bold text-slate-900">{{ $ongoing->first_name }}
                                        {{ $ongoing->last_name }}</h2>
                                    <p class="mt-0.5 truncate text-xs text-slate-500">{{ $ongoing->service_name }}</p>
                                </div>
                                <span
                                    class="rounded-full border border-emerald-200 bg-white px-2.5 py-1 text-[9px] font-bold uppercase tracking-wide text-emerald-700">In
                                    Progress</span>
                            </div>

                            <div
                                class="mt-3 space-y-1 rounded-none border border-emerald-100 bg-white px-3 py-2 text-xs text-slate-600">
                                <p>Dentist: <span
                                        class="font-semibold text-slate-800">{{ ($ongoing->dentist_name ?? $ongoing->dentist_username) ? 'Dr. ' . ($ongoing->dentist_name ?? $ongoing->dentist_username) : 'Unassigned' }}</span>
                                </p>
                                <p>Started: <span
                                        class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($ongoing->appointment_date)->format('h:i A') }}</span>
                                </p>
                                <p>Elapsed: <span
                                        class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($ongoing->appointment_date)->diffForHumans(null, true) }}</span>
                                </p>
                            </div>

                            <div class="mt-3 flex items-center justify-between border-t border-slate-200 pt-3">
                                <span
                                    class="rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-emerald-700">Active
                                    Session</span>
                                <span
                                    class="text-xs font-semibold text-slate-700 opacity-0 transition-opacity group-hover:opacity-100">Open
                                    Chart -&gt;</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div
                        class="flex h-full flex-col items-center justify-center text-center text-sm text-emerald-700 px-6">
                        <svg class="mb-2 h-10 w-10 text-emerald-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                        <p class="font-semibold">No active sessions right now.</p>
                        <p class="mt-1 text-xs text-emerald-600">Patients will appear here once treatment starts.</p>
                    </div>
                @endif
            </div>
        </div>

        <div @if (!$showAppointmentModal && !$isPatientFormOpen) wire:poll.10s="refreshTodaySchedule" @endif
            class="flex flex-col min-h-0 overflow-hidden rounded-none border border-rose-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-rose-200 bg-rose-50 px-4 py-3.5">
                <div class="flex items-center gap-2">
                    <h1 class="text-sm font-bold uppercase tracking-wide text-rose-900">Cancelled Today</h1>
                </div>
                <span
                    class="rounded-full border border-rose-200 bg-white px-2.5 py-1 text-[11px] font-bold text-rose-700">{{ count($cancelledAppointments) }}</span>
            </div>

            <div
                class="flex-1 space-y-3 overflow-y-auto p-3.5 scrollbar-thin scrollbar-track-transparent scrollbar-thumb-rose-200">
                @if (count($cancelledAppointments) > 0)
                    @foreach ($cancelledAppointments as $app)
                        <div wire:key="cancelled-{{ $app->id }}-{{ $app->status }}"
                            x-on:click="modalOpen = true"
                            wire:click="viewAppointment({{ $app->id }})"
                            class="group cursor-pointer rounded-none border border-rose-200 bg-rose-50/60 p-4 transition hover:-translate-y-0.5 hover:border-rose-300 hover:bg-white hover:shadow-md">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h2 class="truncate text-sm font-bold text-slate-900">{{ $app->first_name }}
                                        {{ $app->last_name }}</h2>
                                    <p class="mt-0.5 truncate text-xs text-slate-500">{{ $app->service_name }}</p>
                                </div>
                                <span
                                    class="rounded-full border border-rose-200 bg-white px-2.5 py-1 text-[11px] font-bold text-rose-700 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($app->appointment_date)->format('h:i A') }}
                                </span>
                            </div>

                            <div class="mt-3 flex items-center justify-between border-t border-rose-200 pt-3">
                                <span
                                    class="rounded-full bg-rose-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-rose-700">
                                    {{ $app->status }}
                                </span>
                                <span
                                    class="text-xs font-semibold text-slate-700 opacity-0 transition-opacity group-hover:opacity-100">View
                                    Details -&gt;</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div
                        class="flex h-full flex-col items-center justify-center px-6 text-center text-sm text-rose-700">
                        <svg class="mb-2 h-10 w-10 text-rose-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <p class="font-semibold">No cancelled appointments for today.</p>
                        <p class="mt-1 text-xs text-rose-600">Cancelled entries will show here.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('livewire.appointment.partials.appointment-modal')

</div>
