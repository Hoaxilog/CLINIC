@extends('layouts.app')

@section('content')
    @php
        $displayName = $requesterDisplayName ?? ($user->username ?? (auth()->user()->username ?? 'Patient'));
        $profilePhone = data_get($user, 'contact') ?? 'N/A';
        $hasPending = ($pendingRequests ?? collect())->count() > 0;
        $hasUpcoming = ($upcomingAppointments ?? collect())->count() > 0;
        $hasActiveRequest = $hasPending || $hasUpcoming;
        $profileReadyPercent = data_get($profileCompleteness ?? [], 'percentage', 0);
        $profileReadyLabel = data_get($profileCompleteness ?? [], 'label', 'Needs Update');
        $oldCancellationAppointmentId = (string) old('appointment_id', '');
        $oldCancellationReason = old('cancellation_reason', '');

        $statusBadgeClass = static function (string $status): string {
            return match ($status) {
                'Scheduled' => 'border-sky-200 bg-sky-50 text-sky-700',
                'Pending' => 'border-amber-200 bg-amber-50 text-amber-700',
                'Waiting' => 'border-indigo-200 bg-indigo-50 text-indigo-700',
                'Completed' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                'Cancelled' => 'border-rose-200 bg-rose-50 text-rose-700',
                default => 'border-slate-200 bg-slate-50 text-slate-700',
            };
        };

        $statusDotClass = static function (string $status): string {
            return match ($status) {
                'Scheduled' => 'bg-sky-500',
                'Pending' => 'bg-amber-500',
                'Waiting' => 'bg-indigo-500',
                'Completed' => 'bg-emerald-500',
                'Cancelled' => 'bg-rose-500',
                default => 'bg-slate-400',
            };
        };
    @endphp

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400&display=swap');

        #dashboard-wrap * {
            font-family: 'Montserrat', sans-serif;
        }

        .dash-reveal {
            opacity: 0;
            transform: translateY(16px);
            transition: opacity .5s cubic-bezier(.22, 1, .36, 1), transform .5s cubic-bezier(.22, 1, .36, 1);
        }

        .dash-reveal.in {
            opacity: 1;
            transform: translateY(0);
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

    <main id="dashboard-wrap" x-data="{
        cancelModalOpen: @js($oldCancellationAppointmentId !== ''),
        cancelAction: @js($oldCancellationAppointmentId !== '' ? route('patient.appointments.cancel', ['appointment' => $oldCancellationAppointmentId]) : ''),
        cancelAppointmentId: @js($oldCancellationAppointmentId),
        cancelReason: @js($oldCancellationReason),
        cancelTitle: 'Cancel Appointment',
        cancelPrompt: 'This will notify clinic staff immediately.',
        openCancelModal(config) {
            this.cancelAction = config.action;
            this.cancelAppointmentId = config.appointmentId;
            this.cancelReason = config.reason ?? '';
            this.cancelTitle = config.title;
            this.cancelPrompt = config.prompt;
            this.cancelModalOpen = true;
        },
        closeCancelModal() {
            this.cancelModalOpen = false;
        }
    }" class="min-h-screen bg-[#f6fafd] px-6 py-8 md:px-12 xl:px-20">
        <div class="mx-auto flex w-full max-w-[1400px] flex-col gap-7">

            {{-- Flash messages --}}
            @if (session('success'))
                <div class="rounded-sm border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('failed'))
                <div class="rounded-sm border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    {{ session('failed') }}
                </div>
            @endif

            {{-- ── Page heading ── --}}
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-[#e4eff8] pb-6">
                <div>
                    <h1 class="text-[1.7rem] font-extrabold leading-[1.1] tracking-[-.02em] text-[#1a2e3b]">
                        Welcome back, {{ $displayName }}.
                    </h1>
                </div>
                @unless ($hasActiveRequest)
                    <a href="{{ route('book') }}"
                        class="inline-flex shrink-0 items-center gap-[9px] whitespace-nowrap rounded-sm bg-[#0086da] px-8 py-[15px] text-[.72rem] font-bold uppercase tracking-[.1em] text-white transition hover:bg-[#006ab0]">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="square">
                            <rect x="3" y="4" width="18" height="18" />
                            <path d="M16 2v4M8 2v4M3 10h18" />
                        </svg>
                        Book Appointment
                    </a>
                @else
                    <p class="text-[.78rem] font-medium text-[#587189]">
                        You already have an active appointment request.
                    </p>
                @endunless
            </div>

            {{-- ── Main content + Sidebar ── --}}
            <section class="grid gap-6 lg:grid-cols-[1.6fr_.9fr]">

                {{-- ── Left column ── --}}
                <div class="space-y-6">

                    {{-- Pending Requests --}}
                    <article id="my-requests" class="dash-reveal rounded-sm border border-[#e4eff8] bg-white">
                        <div
                            class="flex flex-col gap-3 border-b border-[#e4eff8] px-6 py-6 sm:flex-row sm:items-end sm:justify-between sm:px-8">
                            <div>
                                <div
                                    class="mb-3 inline-flex items-center gap-[10px] text-[.63rem] font-bold uppercase tracking-[.22em] text-[#0086da]">
                                    Pending
                                </div>
                                <h2 class="text-[1.5rem] leading-[1.15] font-extrabold tracking-[-.02em] text-[#1a2e3b]">My Appointment Requests</h2>
                                {{-- <p class="mt-1 text-[.88rem] leading-[1.7] text-[#587189]">Requests awaiting clinic review.
                                    You can reschedule while pending. Once confirmed by the clinic, contact the clinic for
                                    changes.</p> --}}
                            </div>
                            @unless ($hasActiveRequest)
                                <a href="{{ route('book') }}"
                                    class="inline-flex shrink-0 items-center gap-[9px] whitespace-nowrap rounded-sm border border-[#0086da] px-6 py-[11px] text-[.7rem] font-bold uppercase tracking-[.1em] text-[#0086da] transition hover:bg-[#0086da] hover:text-white">
                                    Book Another
                                </a>
                            @endunless
                        </div>

                        @if ($hasPending)
                            <div class="space-y-[2px] bg-[#e4eff8]">
                                @foreach ($pendingRequests ?? collect() as $appointment)
                                    @php
                                        $apptDate = \Carbon\Carbon::parse($appointment->appointment_date);
                                        $apptStatus = $appointment->status ?? 'Pending';
                                    @endphp
                                    <div class="bg-white px-6 py-5 sm:px-8">
                                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                            <div>
                                                <span
                                                    class="mb-2 inline-flex rounded-sm border px-2.5 py-[3px] text-[10px] font-bold uppercase tracking-[0.16em] border-amber-200 bg-amber-50 text-amber-700">Pending</span>
                                                <p class="text-[1rem] font-semibold text-[#1a2e3b]">
                                                    {{ $appointment->service_name ?? 'Service' }}</p>
                                                <p class="mt-1 text-[.88rem] leading-[1.7] text-[#587189]">
                                                    {{ $apptDate->format('l, F d, Y') }} at {{ $apptDate->format('h:i A') }}
                                                </p>
                                                <p class="mt-0.5 text-[.8rem] text-[#7a9db5]">
                                                    {{ $apptDate->diffForHumans() }}</p>
                                            </div>
                                            <div class="flex w-32 flex-col items-center justify-center gap-2 text-center">
                                                <a href="{{ route('patient.appointments.reschedule.edit', $appointment->id) }}"
                                                    class="inline-flex w-full items-center justify-center rounded-sm border border-[#0086da] bg-[#e8f4fc] px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.14em] text-[#0086da] transition hover:bg-[#d7ecfb]">
                                                    Reschedule
                                                </a>
                                                <button type="button"
                                                    @click="openCancelModal({
                                                        action: @js(route('patient.appointments.cancel', $appointment->id)),
                                                        appointmentId: @js((string) $appointment->id),
                                                        reason: @js($oldCancellationAppointmentId === (string) $appointment->id ? $oldCancellationReason : ''),
                                                        title: 'Cancel Request',
                                                        prompt: 'Cancel this pending request? This cannot be undone.'
                                                    })"
                                                    class="inline-flex w-full items-center justify-center rounded-sm border border-rose-200 bg-rose-50 px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.14em] text-rose-700 transition hover:bg-rose-100">
                                                    Cancel Request
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="px-6 py-5 sm:px-8">
                                <p class="text-[.88rem] leading-[1.8] text-[#587189]">No pending requests right now.</p>
                            </div>
                        @endif
                    </article>

                    {{-- Upcoming Appointments (Scheduled / Waiting) --}}
                    <article class="dash-reveal rounded-sm border border-[#e4eff8] bg-white">
                        <div class="border-b border-[#e4eff8] px-6 py-6 sm:px-8">
                            <div
                                class="mb-3 inline-flex items-center gap-[10px] text-[.63rem] font-bold uppercase tracking-[.22em] text-[#0086da]">
                                Confirmed
                            </div>
                            <h2 class="text-[1.5rem] leading-[1.15] font-extrabold tracking-[-.02em] text-[#1a2e3b]">
                                Upcoming Appointments</h2>
                            <p class="mt-1 text-[.88rem] leading-[1.7] text-[#587189]">Clinic-confirmed appointments coming
                                up.</p>
                        </div>

                        @if ($hasUpcoming)
                            <div class="space-y-[2px] bg-[#e4eff8]">
                                @foreach ($upcomingAppointments ?? collect() as $appointment)
                                    @php
                                        $apptDate = \Carbon\Carbon::parse($appointment->appointment_date);
                                        $apptStatus = $appointment->status ?? 'Scheduled';
                                    @endphp
                                    <div class="bg-white px-6 py-5 sm:px-8">
                                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                            <div class="flex gap-4">
                                                <div class="mt-1 flex flex-col items-center">
                                                    <span
                                                        class="h-3 w-3 rounded-full {{ $statusDotClass($apptStatus) }}"></span>
                                                    @if (!$loop->last)
                                                        <span class="mt-2 h-16 w-px bg-slate-200"></span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <span
                                                        class="mb-2 inline-flex rounded-sm border px-2.5 py-[3px] text-[10px] font-bold uppercase tracking-[0.16em] {{ $statusBadgeClass($apptStatus) }}">{{ $apptStatus }}</span>
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <p class="text-[1rem] font-semibold text-[#1a2e3b]">
                                                            {{ $appointment->service_name ?? 'Service' }}</p>
                                                        @if ($loop->first)
                                                            <span
                                                                class="inline-flex rounded-sm border border-[#b8dcf3] bg-[#e8f4fc] px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-[#0086da]">Nearest
                                                                Visit</span>
                                                        @endif
                                                    </div>
                                                    <p class="mt-2 text-[.88rem] leading-[1.7] text-[#587189]">
                                                        {{ $apptDate->format('l, F d, Y') }} at
                                                        {{ $apptDate->format('h:i A') }}</p>
                                                    <p class="mt-1 text-[.8rem] text-[#7a9db5]">
                                                        {{ $apptDate->diffForHumans() }}</p>
                                                </div>
                                            </div>
                                            <div class="flex flex-col items-center justify-center gap-3 text-center">
                                                @if ($apptStatus === 'Scheduled')
                                                    <button type="button"
                                                        @click="openCancelModal({
                                                            action: @js(route('patient.appointments.cancel', $appointment->id)),
                                                            appointmentId: @js((string) $appointment->id),
                                                            reason: @js($oldCancellationAppointmentId === (string) $appointment->id ? $oldCancellationReason : ''),
                                                            title: 'Cancel Appointment',
                                                            prompt: 'Cancel this scheduled appointment? This will notify clinic staff immediately.'
                                                        })"
                                                        class="inline-flex items-center justify-center rounded-sm border border-rose-200 bg-rose-50 px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.14em] text-rose-700 transition hover:bg-rose-100">
                                                        Cancel
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="px-6 py-6 sm:px-8">
                                <div class="rounded-sm border border-dashed border-[#d4e8f5] bg-[#f6fafd] p-6">
                                    <h3 class="text-lg font-semibold text-[#1a2e3b]">No confirmed appointments yet</h3>
                                    <p class="mt-2 text-[.88rem] leading-[1.8] text-[#587189]">Once the clinic confirms your
                                        request it will appear here.</p>
                                </div>
                            </div>
                        @endif
                    </article>

                    {{-- Booking History --}}
                    <article class="dash-reveal rounded-sm border border-[#e4eff8] bg-white">
                        <div
                            class="flex flex-col gap-3 border-b border-[#e4eff8] px-6 py-6 sm:flex-row sm:items-end sm:justify-between sm:px-8">
                            <div>
                                <div
                                    class="mb-3 inline-flex items-center gap-[10px] text-[.63rem] font-bold uppercase tracking-[.22em] text-[#0086da]">
                                    History
                                </div>
                                <h2 class="text-[1.5rem] leading-[1.15] font-extrabold tracking-[-.02em] text-[#1a2e3b]">
                                    Booking History</h2>
                                <p class="mt-1 text-[.88rem] leading-[1.7] text-[#587189]">Completed, cancelled, and past
                                    appointment requests.</p>
                            </div>
                            <span class="text-[.62rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Latest 10
                                entries</span>
                        </div>

                        @if (($appointmentHistory ?? collect())->count() > 0)
                            <div class="overflow-x-auto px-6 py-6 sm:px-8">
                                <table class="w-full min-w-[580px] text-left text-sm text-[#587189]">
                                    <thead
                                        class="border-b border-[#e4eff8] text-[11px] uppercase tracking-[0.18em] text-[#7a9db5]">
                                        <tr>
                                            <th class="px-3 py-3 font-semibold">Date</th>
                                            <th class="px-3 py-3 font-semibold">Time</th>
                                            <th class="px-3 py-3 font-semibold">Service</th>
                                            <th class="px-3 py-3 font-semibold">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[#edf5fb]">
                                        @foreach ($appointmentHistory ?? collect() as $historyItem)
                                            @php
                                                $historyDate = \Carbon\Carbon::parse($historyItem->appointment_date);
                                                $historyStatus = $historyItem->status ?? 'Pending';
                                            @endphp
                                            <tr class="hover:bg-[#f8fbfe]">
                                                <td class="px-3 py-4 font-medium text-[#1a2e3b]">
                                                    {{ $historyDate->format('M d, Y') }}</td>
                                                <td class="px-3 py-4">{{ $historyDate->format('h:i A') }}</td>
                                                <td class="px-3 py-4">{{ $historyItem->service_name ?? 'Service' }}</td>
                                                <td class="px-3 py-4">
                                                    <span
                                                        class="inline-flex rounded-sm border px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] {{ $statusBadgeClass($historyStatus) }}">{{ $historyStatus }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="px-6 py-6 sm:px-8">
                                <p class="text-[.88rem] leading-[1.8] text-[#587189]">No previous appointment activity yet.
                                </p>
                            </div>
                        @endif
                    </article>
                </div>

                {{-- ── Right sidebar ── --}}
                <aside class="space-y-6">

                    {{-- Account --}}
                    <article class="dash-reveal rounded-sm border border-[#e4eff8] bg-white p-6">
                        <div
                            class="mb-4 inline-flex items-center gap-[10px] text-[.63rem] font-bold uppercase tracking-[.22em] text-[#0086da]">
                            My Account
                        </div>
                        <h2 class="text-[1.3rem] leading-[1.15] font-extrabold tracking-[-.02em] text-[#1a2e3b]">Account
                            Details</h2>

                        <div class="mt-4 space-y-3">
                            {{-- Readiness --}}
                            <div class="rounded-sm border border-[#e4eff8] bg-[#f6fafd] p-4">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-sm font-semibold text-[#1a2e3b]">Profile {{ $profileReadyPercent }}%
                                        ready</p>
                                    <span
                                        class="inline-flex shrink-0 rounded-sm border px-2.5 py-1 text-[11px] font-semibold {{ $profileReadyPercent === 100 ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-amber-200 bg-amber-50 text-amber-700' }}">{{ $profileReadyLabel }}</span>
                                </div>
                                <div class="mt-3 h-1.5 w-full bg-[#e4eff8]">
                                    <div class="h-full transition-all duration-700 {{ $profileReadyPercent === 100 ? 'bg-emerald-400' : 'bg-[#0086da]' }}"
                                        style="width: {{ $profileReadyPercent }}%"></div>
                                </div>
                                <a href="{{ route('profile.index') }}"
                                    class="mt-4 inline-flex items-center gap-[9px] whitespace-nowrap rounded-sm bg-[#0086da] px-6 py-[11px] text-[.7rem] font-bold uppercase tracking-[.1em] text-white transition hover:bg-[#006ab0]">
                                    Update Account
                                </a>
                            </div>

                            {{-- Details grid --}}
                            <div class="grid grid-cols-1 gap-[2px] bg-[#e4eff8]">
                                <div class="flex items-center justify-between bg-[#f6fafd] px-4 py-3">
                                    <span
                                        class="text-[.72rem] font-semibold uppercase tracking-[.1em] text-[#7a9db5]">Name</span>
                                    <span class="text-sm font-semibold text-[#1a2e3b]">{{ $displayName }}</span>
                                </div>
                                <div class="flex items-center justify-between bg-[#f6fafd] px-4 py-3">
                                    <span
                                        class="text-[.72rem] font-semibold uppercase tracking-[.1em] text-[#7a9db5]">Email</span>
                                    <span
                                        class="max-w-[160px] truncate text-sm font-semibold text-[#1a2e3b]">{{ $user->email ?? 'N/A' }}</span>
                                </div>
                                <div class="flex items-center justify-between bg-[#f6fafd] px-4 py-3">
                                    <span
                                        class="text-[.72rem] font-semibold uppercase tracking-[.1em] text-[#7a9db5]">Contact</span>
                                    <span class="text-sm font-semibold text-[#1a2e3b]">{{ $profilePhone }}</span>
                                </div>
                            </div>
                        </div>
                    </article>

                </aside>
            </section>
        </div>

        <div x-cloak x-show="cancelModalOpen" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-[#10283b]/55 px-4 py-6"
            @click.self="closeCancelModal()" @keydown.escape.window="closeCancelModal()">
            <div x-transition
                class="w-full max-w-lg rounded-sm border border-[#dbeaf7] bg-white p-6 shadow-[0_32px_80px_rgba(16,40,59,.22)] sm:p-8">
                <div class="flex items-start justify-between gap-4 border-b border-[#e4eff8] pb-5">
                    <div>
                        <p class="text-[.63rem] font-bold uppercase tracking-[.22em] text-rose-500">Cancellation</p>
                        <h2 class="mt-2 text-[1.35rem] font-extrabold leading-[1.1] tracking-[-.02em] text-[#1a2e3b]"
                            x-text="cancelTitle"></h2>
                        <p class="mt-2 text-[.88rem] leading-[1.7] text-[#587189]" x-text="cancelPrompt"></p>
                    </div>
                    <button type="button" @click="closeCancelModal()"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-sm border border-[#d4e8f5] text-[#587189] transition hover:bg-[#f8fbfe]">
                        <span class="sr-only">Close cancellation dialog</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.2" stroke-linecap="square">
                            <path d="M6 6l12 12M18 6L6 18" />
                        </svg>
                    </button>
                </div>

                <form method="POST" :action="cancelAction" class="mt-6 space-y-5">
                    @csrf
                    <input type="hidden" name="appointment_id" :value="cancelAppointmentId">

                    <label class="block text-left">
                        <span class="mb-2 block text-[.68rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e]">
                            Reason for cancellation
                        </span>
                        <textarea name="cancellation_reason" rows="4" maxlength="500"
                            placeholder="Tell the clinic why you need to cancel this appointment."
                            x-model="cancelReason"
                            class="w-full resize-none rounded-sm border border-[#d4e8f5] bg-white px-4 py-3 text-sm text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#cde8fb]"></textarea>
                    </label>

                    @if ($errors->has('cancellation_reason'))
                        <p class="text-sm text-rose-600">{{ $errors->first('cancellation_reason') }}</p>
                    @endif

                    <div class="flex pt-2 sm:justify-end">
                        <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-sm bg-rose-600 px-6 py-[13px] text-[.72rem] font-bold uppercase tracking-[.1em] text-white transition hover:bg-rose-700 sm:w-auto">
                            Confirm Cancellation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        (() => {
            const io = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const el = entry.target;
                        const delay = parseFloat(el.dataset.revealDelay || 0);
                        setTimeout(() => el.classList.add('in'), delay);
                        io.unobserve(el);
                    }
                });
            }, {
                threshold: 0.06
            });

            document.querySelectorAll('.dash-reveal').forEach((el, i) => {
                el.dataset.revealDelay = i * 70;
                io.observe(el);
            });
        })();
    </script>
@endsection
