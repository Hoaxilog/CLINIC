<div x-data="{ modalOpen: @entangle('showAppointmentModal').live, detailsLoading: false }"
    x-on:appointment-details-loaded.window="detailsLoading = false"
    class="h-full">

    @php
        $completedAppointments = $todayAppointments->where('status', 'Completed');
        $cancelledAppointments = $todayAppointments->where('status', 'Cancelled');
        $scheduledAppointments = $todayAppointments->where('status', 'Scheduled');
    @endphp

    <section class="mb-4 grid grid-cols-2 gap-3 lg:grid-cols-5">
        <article class="rounded-xl border border-slate-200 bg-slate-50 p-3.5">
            <p class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-500">Scheduled Today</p>
            <p class="mt-1 text-lg font-bold text-slate-900">{{ count($scheduledAppointments) }}</p>
        </article>
        <article class="rounded-xl border border-amber-200 bg-amber-50 p-3.5">
            <p class="text-[10px] font-semibold uppercase tracking-[0.1em] text-amber-700">Patients Arrived</p>
            <p class="mt-1 text-lg font-bold text-amber-800">{{ count($waitingQueue) }}</p>
        </article>
        <article class="rounded-xl border border-emerald-200 bg-emerald-50 p-3.5">
            <p class="text-[10px] font-semibold uppercase tracking-[0.1em] text-emerald-700">In Session</p>
            <p class="mt-1 text-lg font-bold text-emerald-800">{{ count($ongoingAppointments) }}</p>
        </article>
        <article class="rounded-xl border border-teal-200 bg-teal-50 p-3.5">
            <p class="text-[10px] font-semibold uppercase tracking-[0.1em] text-teal-700">Completed Today</p>
            <p class="mt-1 text-lg font-bold text-teal-800">{{ count($completedAppointments) }}</p>
        </article>
        <article class="rounded-xl border border-rose-200 bg-rose-50 p-3.5">
            <p class="text-[10px] font-semibold uppercase tracking-[0.1em] text-rose-700">Cancelled</p>
            <p class="mt-1 text-lg font-bold text-rose-800">{{ count($cancelledAppointments) }}</p>
        </article>
    </section>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3 h-[calc(100vh-280px)] min-h-[600px]">

        <div @if(!$showAppointmentModal && !$isPatientFormOpen) wire:poll.60s="refreshTodaySchedule" @endif class="flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-4 py-3.5">
                <div class="flex items-center gap-2">
                    <div class="h-2.5 w-2.5 rounded-full bg-slate-500"></div>
                    <h1 class="text-sm font-bold uppercase tracking-wide text-slate-800">Today's Schedule</h1>
                </div>
                <span class="rounded-full border border-slate-200 bg-white px-2.5 py-1 text-[11px] font-bold text-slate-700">{{ count($scheduledAppointments) + count($cancelledAppointments) }}</span>
            </div>

            <div class="flex-1 space-y-3 overflow-y-auto p-3.5 scrollbar-thin scrollbar-track-transparent scrollbar-thumb-slate-300">
                @if(count($scheduledAppointments) + count($cancelledAppointments) > 0)
                    @foreach($todayAppointments->whereIn('status', ['Scheduled', 'Cancelled']) as $app)
                        <div wire:key="today-{{ $app->id }}-{{ $app->status }}" x-on:click="detailsLoading = true; modalOpen = true" wire:click="viewAppointment({{ $app->id }})" class="group cursor-pointer rounded-xl border border-slate-200 bg-slate-50 p-4 transition hover:-translate-y-0.5 hover:border-slate-300 hover:bg-white hover:shadow-md">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h2 class="truncate text-sm font-bold text-slate-900">{{ $app->first_name }} {{ $app->last_name }}</h2>
                                    <p class="mt-0.5 truncate text-xs text-slate-500">{{ $app->service_name }}</p>
                                </div>
                                <span class="rounded-full border border-slate-200 bg-white px-2.5 py-1 text-[11px] font-bold text-slate-700 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($app->appointment_date)->format('h:i A') }}
                                </span>
                            </div>

                            <div class="mt-3 flex items-center justify-between border-t border-slate-200 pt-3">
                                <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide {{ $app->status === 'Cancelled' ? 'bg-rose-100 text-rose-700' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $app->status }}
                                </span>
                                <span class="text-xs font-semibold text-slate-700 opacity-0 transition-opacity group-hover:opacity-100">View Details -&gt;</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="flex h-full flex-col items-center justify-center text-center text-sm text-slate-400 px-6">
                        <svg class="mb-2 h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <p class="font-semibold text-slate-500">No appointments in today's schedule.</p>
                        <p class="mt-1 text-xs">Scheduled and cancelled entries will appear here.</p>
                    </div>
                @endif
            </div>
        </div>

        <div @if(!$showAppointmentModal && !$isPatientFormOpen) wire:poll.10s="refreshWaitingQueue" @endif class="flex flex-col overflow-hidden rounded-xl border border-amber-300 bg-amber-50/50 shadow-md">
            <div class="flex items-center justify-between border-b border-amber-200 bg-amber-100/70 px-4 py-3.5">
                <div class="flex items-center gap-2">
                    <div class="h-2.5 w-2.5 animate-pulse rounded-full bg-amber-600"></div>
                    <h1 class="text-sm font-bold uppercase tracking-wide text-amber-900">Waiting in Lobby</h1>
                    <span class="rounded-full border border-amber-300 bg-white px-2.5 py-1 text-[11px] font-bold text-amber-700">{{ count($waitingQueue) }}</span>
                </div>
                @if(auth()->user()?->role === 1)
                    <button type="button" wire:click="callNextPatient" wire:loading.attr="disabled" wire:target="callNextPatient" class="rounded-lg border border-amber-700 bg-amber-600 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-amber-700 disabled:cursor-not-allowed disabled:opacity-60">
                        <span wire:loading.remove wire:target="callNextPatient">Call Next</span>
                        <span wire:loading wire:target="callNextPatient">Calling...</span>
                    </button>
                @endif
            </div>

            <div class="flex-1 space-y-3 overflow-y-auto p-3.5 scrollbar-thin scrollbar-track-transparent scrollbar-thumb-amber-200">
                @if(count($waitingQueue) > 0)
                    @foreach($waitingQueue as $wait)
                        <div wire:key="wait-{{ $wait->id }}-{{ $wait->status }}" x-on:click="detailsLoading = true; modalOpen = true" wire:click="viewAppointment({{ $wait->id }})" class="group cursor-pointer rounded-xl border bg-white p-4 transition hover:-translate-y-0.5 hover:border-amber-300 hover:shadow-md {{ $loop->first ? 'border-amber-300 ring-1 ring-amber-100 shadow-md' : 'border-amber-200 shadow-sm' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h2 class="truncate text-sm font-bold text-slate-900">{{ $wait->first_name }} {{ $wait->last_name }}</h2>
                                        <span class="rounded-full px-2.5 py-1 text-[9px] font-bold uppercase tracking-wide {{ $loop->first ? 'bg-amber-200 text-amber-800' : 'bg-amber-100 text-amber-700' }}">{{ $loop->first ? 'Up Next' : 'Waiting' }}</span>
                                    </div>
                                    <p class="mt-0.5 truncate text-xs text-slate-500">{{ $wait->service_name ?? 'Procedure pending' }}</p>
                                </div>
                            </div>

                            <div class="mt-3 rounded-lg border border-amber-100 bg-amber-50 px-3 py-2 text-xs text-amber-900">
                                Waiting Time: <span class="font-semibold">{{ \Carbon\Carbon::parse($wait->waited_at)->diffForHumans(null, true) }}</span>
                            </div>

                            <div class="mt-3 flex items-center justify-between border-t border-slate-200 pt-3">
                                <span class="rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-amber-700">Ready</span>
                                <span class="text-xs font-semibold text-slate-700 opacity-0 transition-opacity group-hover:opacity-100">View Details -&gt;</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="flex h-full flex-col items-center justify-center text-center text-sm text-amber-700 px-6">
                        <svg class="mb-2 h-10 w-10 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <p class="font-semibold">No patients waiting in lobby.</p>
                        <p class="mt-1 text-xs text-amber-600">Arrived patients will appear here for calling.</p>
                    </div>
                @endif
            </div>
        </div>

        <div @if(!$showAppointmentModal && !$isPatientFormOpen) wire:poll.5s="refreshOngoingAppointments" @endif class="flex flex-col overflow-hidden rounded-xl border border-emerald-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-emerald-200 bg-emerald-50 px-4 py-3.5">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-600"></span>
                    </span>
                    <h1 class="text-sm font-bold uppercase tracking-wide text-emerald-900">In Treatment</h1>
                </div>
                <span class="rounded-full border border-emerald-200 bg-white px-2.5 py-1 text-[11px] font-bold text-emerald-700">{{ count($ongoingAppointments) }}</span>
            </div>

            <div class="flex-1 space-y-3 overflow-y-auto p-3.5 scrollbar-thin scrollbar-track-transparent scrollbar-thumb-emerald-200">
                @if(count($ongoingAppointments) > 0)
                    @foreach($ongoingAppointments as $ongoing)
                        <div wire:key="ongoing-{{ $ongoing->id }}-{{ $ongoing->status }}" x-on:click="detailsLoading = true; modalOpen = true" wire:click="viewAppointment({{ $ongoing->id }})" class="group cursor-pointer rounded-xl border border-emerald-200 bg-emerald-50/40 p-4 shadow-sm transition hover:-translate-y-0.5 hover:bg-white hover:shadow-md">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h2 class="truncate text-sm font-bold text-slate-900">{{ $ongoing->first_name }} {{ $ongoing->last_name }}</h2>
                                    <p class="mt-0.5 truncate text-xs text-slate-500">{{ $ongoing->service_name }}</p>
                                </div>
                                <span class="rounded-full border border-emerald-200 bg-white px-2.5 py-1 text-[9px] font-bold uppercase tracking-wide text-emerald-700">In Progress</span>
                            </div>

                            <div class="mt-3 space-y-1 rounded-lg border border-emerald-100 bg-white px-3 py-2 text-xs text-slate-600">
                                <p>Dentist: <span class="font-semibold text-slate-800">{{ $ongoing->dentist_name ? 'Dr. ' . $ongoing->dentist_name : 'Unassigned' }}</span></p>
                                <p>Started: <span class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($ongoing->appointment_date)->format('h:i A') }}</span></p>
                                <p>Elapsed: <span class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($ongoing->appointment_date)->diffForHumans(null, true) }}</span></p>
                            </div>

                            <div class="mt-3 flex items-center justify-between border-t border-slate-200 pt-3">
                                <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-emerald-700">Active Session</span>
                                <span class="text-xs font-semibold text-slate-700 opacity-0 transition-opacity group-hover:opacity-100">Open Chart -&gt;</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="flex h-full flex-col items-center justify-center text-center text-sm text-emerald-700 px-6">
                        <svg class="mb-2 h-10 w-10 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <p class="font-semibold">No active treatments right now.</p>
                        <p class="mt-1 text-xs text-emerald-600">Patients will appear here when a session begins.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div x-cloak x-show="modalOpen" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black opacity-60" x-on:click="detailsLoading = false; modalOpen = false; $wire.closeAppointmentModal()"></div>
            <div class="relative z-10 mx-4 w-full max-w-4xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">

                <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-6 py-4">
                    <h3 class="text-xl font-bold text-slate-900">Appointment Details</h3>
                    <button class="rounded-full p-2 text-slate-400 transition hover:bg-white hover:text-slate-600" x-on:click="detailsLoading = false; modalOpen = false; $wire.closeAppointmentModal()">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                @if (session()->has('error'))
                    <div class="flex items-center gap-2 border-b border-rose-200 bg-rose-50 px-6 py-3 text-sm font-semibold text-rose-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ session('error') }}
                    </div>
                @endif

                <div x-cloak x-show="detailsLoading" class="min-h-[300px] items-center justify-center bg-slate-50 text-center flex">
                    <div class="flex flex-col items-center gap-3">
                        <div class="h-10 w-10 animate-spin rounded-full border-4 border-blue-200 border-t-[#0086da]"></div>
                        <div class="text-sm font-semibold text-gray-700">Loading appointment details...</div>
                    </div>
                </div>

                <div x-cloak x-show="!detailsLoading" class="max-h-[80vh] overflow-y-auto p-6">

                    <div class="mb-6 rounded-xl border border-slate-200 bg-slate-50 p-5">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div>
                                <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-500">Date</label>
                                <input type="text" value="{{ \Carbon\Carbon::parse($selectedDate)->format('F j, Y') }}" class="w-full border-0 bg-transparent p-0 text-lg font-bold text-slate-900 focus:ring-0" readonly />
                            </div>
                            <div>
                                <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-500">Start Time</label>
                                <input type="text" value="{{ $selectedTime }}" class="w-full border-0 bg-transparent p-0 text-lg font-bold text-slate-900 focus:ring-0" readonly />
                            </div>
                            <div>
                                <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-slate-500">End Time</label>
                                <input type="text" value="{{ $endTime }}" class="w-full border-0 bg-transparent p-0 text-lg font-bold text-slate-900 focus:ring-0" readonly />
                            </div>
                        </div>
                    </div>

                    @if($dentistName && ($appointmentStatus == 'Ongoing' || $appointmentStatus == 'Completed'))
                        <div class="mb-6 flex items-center gap-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode('Dr ' . $dentistName) }}&background=10b981&color=fff" class="h-10 w-10 rounded-full border-2 border-white shadow-sm">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Assigned Dentist</p>
                                <p class="text-base font-bold text-slate-900">Dr. {{ $dentistName }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="mb-5 grid grid-cols-1 gap-5 md:grid-cols-3">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">First Name</label>
                            <input wire:model="firstName" type="text" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 font-medium text-slate-900 outline-none" readonly/>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Middle Name</label>
                            <input wire:model="middleName" type="text" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 font-medium text-slate-900 outline-none" readonly/>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Last Name</label>
                            <input wire:model="lastName" type="text" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 font-medium text-slate-900 outline-none" readonly/>
                        </div>
                    </div>

                    <div class="mb-8 grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Contact Number</label>
                            <input wire:model="contactNumber" type="text" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 font-medium text-slate-900 outline-none" readonly/>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Service Required</label>
                            <select wire:model="selectedService" class="w-full rounded-lg border border-slate-200 bg-white px-4 py-2.5 font-medium text-slate-900 outline-none focus:ring-2 focus:ring-slate-200" {{ ($appointmentStatus != 'Waiting') ? 'disabled' : '' }}>
                                @foreach($servicesList as $service)
                                    <option value="{{ $service->id }}">
                                        {{ $service->service_name }} ({{ \Carbon\Carbon::parse($service->duration)->format('H:i') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-col justify-end gap-3 border-t border-slate-200 pt-6 sm:flex-row">

                        @if(!in_array($appointmentStatus, ['Cancelled', 'Completed']))
                            <button type="button" wire:click="updateStatus('Cancelled')" wire:loading.attr="disabled" wire:target="updateStatus" wire:confirm="Are you sure you want to cancel this appointment?" class="mr-auto rounded-lg px-5 py-2.5 font-semibold text-rose-600 transition hover:bg-rose-50 disabled:cursor-not-allowed disabled:opacity-60">
                                Cancel Appointment
                            </button>
                        @endif

                        @if($appointmentStatus === 'Scheduled')
                            <button type="button" wire:click="processPatient" wire:loading.attr="disabled" wire:target="processPatient" class="rounded-lg border border-slate-200 bg-white px-5 py-2.5 font-semibold text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-60">
                                Update Info
                            </button>
                            <button type="button" wire:click="updateStatus('Waiting')" wire:loading.attr="disabled" wire:target="updateStatus" class="flex items-center gap-2 rounded-lg bg-amber-500 px-5 py-2.5 font-semibold text-white shadow-sm transition hover:bg-amber-600 disabled:cursor-not-allowed disabled:opacity-60">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Mark as Arrived (Lobby)
                            </button>

                        @elseif($appointmentStatus === 'Waiting')
                            <button type="button" wire:click="processPatient" wire:loading.attr="disabled" wire:target="processPatient" class="rounded-lg border border-slate-200 bg-white px-5 py-2.5 font-semibold text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-60">
                                View Patient Info
                            </button>
                            <button type="button" wire:click="admitPatient" wire:loading.attr="disabled" wire:target="admitPatient" class="flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-2.5 font-semibold text-white shadow-sm transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                Call to Chair
                            </button>

                        @elseif($appointmentStatus === 'Ongoing')
                            <button type="button" wire:click="openPatientChart" wire:loading.attr="disabled" wire:target="openPatientChart" class="flex items-center gap-2 rounded-lg bg-slate-800 px-5 py-2.5 font-semibold text-white shadow-sm transition hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-60">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Open Chart
                            </button>
                            <button type="button" wire:click="updateStatus('Completed')" wire:loading.attr="disabled" wire:target="updateStatus" class="flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-2.5 font-semibold text-white shadow-sm transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Finish Session
                            </button>

                        @elseif($appointmentStatus === 'Completed')
                            <span class="flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-2.5 font-bold text-emerald-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Completed
                            </span>
                        @elseif($appointmentStatus === 'Cancelled')
                            <span class="flex items-center gap-2 rounded-lg border border-rose-200 bg-rose-50 px-5 py-2.5 font-bold text-rose-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Cancelled
                            </span>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    @include('components.flash-toast')
</div>
