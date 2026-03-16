@extends('layouts.app')

@section('content')
    @php
        $hasUpcoming = ($upcomingAppointments ?? collect())->count() > 0;
        $patientName = trim(($patient->first_name ?? '') . ' ' . ($patient->last_name ?? ''));
        $patientName = $patientName !== '' ? $patientName : auth()->user()->username ?? 'Patient';
        $profilePhone = data_get($patient, 'mobile_number') ?: auth()->user()->contact ?? 'N/A';

        $statusBadgeClass = static function (string $status): string {
            return match ($status) {
                'Scheduled' => 'bg-blue-100 text-blue-700 border-blue-200',
                'Pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                'Waiting' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                'Completed' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                'Cancelled' => 'bg-rose-100 text-rose-700 border-rose-200',
                default => 'bg-slate-100 text-slate-700 border-slate-200',
            };
        };
    @endphp

    <main id="mainContent" class="min-h-screen bg-gray-100 px-4 py-6 sm:px-6 lg:px-8">
        <div class="mx-auto w-full max-w-7xl">
            <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-teal-700">Patient Portal</p>
                <h1 class="mt-2 text-2xl font-bold text-slate-900 sm:text-3xl">Welcome back, {{ $patientName }}!</h1>
                <p class="mt-2 max-w-3xl text-sm text-slate-600">Manage your appointments and stay updated with clinic
                    notifications.</p>
            </div>

            @if (session('success'))
                <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('failed'))
                <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    {{ session('failed') }}
                </div>
            @endif

            <section class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-semibold uppercase tracking-[0.12em] text-teal-700">Quick Actions</h2>
                <div class="mt-4 flex flex-wrap gap-3">
                    <a href="{{ route('book') }}"
                        class="inline-flex items-center justify-center rounded-lg bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-teal-800">
                        Book Appointment
                    </a>
                    <a href="#upcoming-appointments"
                        class="inline-flex items-center justify-center rounded-lg border border-teal-200 bg-teal-50 px-4 py-2.5 text-sm font-semibold text-teal-800 transition hover:bg-teal-100">
                        View Appointments
                    </a>
                    <a href="{{ route('profile.index') }}"
                        class="inline-flex items-center justify-center rounded-lg border border-teal-200 bg-teal-50 px-4 py-2.5 text-sm font-semibold text-teal-800 transition hover:bg-teal-100">
                        Update Profile
                    </a>
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-3">
                <article id="upcoming-appointments"
                    class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm lg:col-span-2">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-slate-900">Upcoming Appointments</h2>
                        <span class="text-xs uppercase tracking-[0.12em] text-teal-700">Pending and Scheduled</span>
                    </div>

                    @if (($upcomingAppointments ?? collect())->count() > 0)
                        <div class="mt-4 space-y-3">
                            @foreach ($upcomingAppointments ?? collect() as $appointment)
                                @php
                                    $apptDate = \Carbon\Carbon::parse($appointment->appointment_date);
                                    $apptStatus = $appointment->status ?? 'Pending';
                                @endphp
                                <div class="rounded-xl border border-slate-100 p-4">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">
                                                {{ $appointment->service_name ?? 'Service' }}</p>
                                            <p class="mt-1 text-sm text-slate-500">
                                                {{ $apptDate->format('F d, Y') }} | {{ $apptDate->format('h:i A') }}
                                            </p>
                                        </div>
                                        <span
                                            class="inline-flex rounded-full border px-2.5 py-1 text-[11px] font-semibold {{ $statusBadgeClass($apptStatus) }}">
                                            {{ $apptStatus }}
                                        </span>
                                    </div>

                                    @if ($apptStatus === 'Scheduled')
                                        <form class="mt-3" method="POST"
                                            action="{{ route('patient.appointments.cancel', $appointment->id) }}"
                                            onsubmit="return confirm('Cancel this scheduled appointment? This will notify clinic staff immediately.');">
                                            @csrf
                                            <button type="submit"
                                                class="inline-flex items-center justify-center rounded-md border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-[11px] font-semibold uppercase tracking-wide text-rose-700 transition hover:bg-rose-100">
                                                Cancel Appointment
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-4 text-sm text-slate-500">No upcoming appointments yet. You can book your first
                            appointment now.</p>
                    @endif
                </article>

                <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">Notifications</h2>
                    <div class="mt-4 space-y-3 text-sm text-slate-600">
                        @forelse (($appointmentRequests ?? collect())->take(3) as $request)
                            @php
                                $reqDate = \Carbon\Carbon::parse($request->appointment_date);
                            @endphp
                            <div class="rounded-lg border border-teal-100 bg-teal-50 px-3 py-2">
                                @if (($request->status ?? '') === 'Scheduled')
                                    Your appointment for {{ $reqDate->format('M d') }} at {{ $reqDate->format('h:i A') }}
                                    has been confirmed.
                                @elseif (($request->status ?? '') === 'Pending')
                                    Your appointment request for {{ $reqDate->format('M d') }} is pending review.
                                @elseif (($request->status ?? '') === 'Waiting')
                                    Your appointment for {{ $reqDate->format('M d') }} is in waiting status.
                                @else
                                    Your appointment update: {{ $request->status ?? 'Updated' }}.
                                @endif
                            </div>
                        @empty
                            <div class="rounded-lg border border-teal-100 bg-teal-50 px-3 py-2">
                                No notifications yet. Appointment updates will appear here.
                            </div>
                        @endforelse
                    </div>
                </article>
            </section>

            <section class="mt-6 grid gap-6 lg:grid-cols-3">
                <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm lg:col-span-2">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-slate-900">Appointment History</h2>
                        <span class="text-xs uppercase tracking-[0.12em] text-teal-700">Portal Requests</span>
                    </div>

                    @if (($appointmentHistory ?? collect())->count() > 0)
                        <div class="mt-4 overflow-x-auto">
                            <table class="w-full min-w-[560px] text-left text-sm text-slate-600">
                                <thead class="border-b border-slate-100 text-xs uppercase text-slate-500">
                                    <tr>
                                        <th class="px-3 py-2 font-semibold">Date</th>
                                        <th class="px-3 py-2 font-semibold">Time</th>
                                        <th class="px-3 py-2 font-semibold">Service</th>
                                        <th class="px-3 py-2 font-semibold">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($appointmentHistory ?? collect() as $historyItem)
                                        @php
                                            $historyDate = \Carbon\Carbon::parse($historyItem->appointment_date);
                                            $historyStatus = $historyItem->status ?? 'Pending';
                                        @endphp
                                        <tr>
                                            <td class="px-3 py-3 font-medium text-slate-900">
                                                {{ $historyDate->format('M d, Y') }}</td>
                                            <td class="px-3 py-3">{{ $historyDate->format('h:i A') }}</td>
                                            <td class="px-3 py-3">{{ $historyItem->service_name ?? 'Service' }}</td>
                                            <td class="px-3 py-3">
                                                <span
                                                    class="inline-flex rounded-full border px-2.5 py-1 text-[11px] font-semibold {{ $statusBadgeClass($historyStatus) }}">
                                                    {{ $historyStatus }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="mt-4 text-sm text-slate-500">No previous appointment requests yet.</p>
                    @endif
                </article>

                <article class="space-y-6">
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <h2 class="text-lg font-semibold text-slate-900">Profile Information</h2>
                        <dl class="mt-4 space-y-2 text-sm text-slate-600">
                            <div>
                                <dt class="text-xs uppercase tracking-[0.12em] text-slate-400">Name</dt>
                                <dd class="mt-0.5 font-semibold text-slate-900">{{ $patientName }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-[0.12em] text-slate-400">Email</dt>
                                <dd class="mt-0.5 font-semibold text-slate-900">{{ auth()->user()->email ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-[0.12em] text-slate-400">Phone</dt>
                                <dd class="mt-0.5 font-semibold text-slate-900">{{ $profilePhone }}</dd>
                            </div>
                        </dl>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="{{ route('profile.index') }}"
                                class="inline-flex items-center justify-center rounded-lg border border-teal-200 bg-teal-50 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-teal-800 transition hover:bg-teal-100">
                                Edit Profile
                            </a>
                            <a href="{{ route('profile.index') }}"
                                class="inline-flex items-center justify-center rounded-lg border border-teal-200 bg-teal-50 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-teal-800 transition hover:bg-teal-100">
                                Change Password
                            </a>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <h2 class="text-lg font-semibold text-slate-900">Clinic Information</h2>
                        <div class="mt-4 space-y-2 text-sm text-slate-600">
                            <p><span class="font-semibold text-slate-800">Opening Hours:</span> Monday - Saturday | 9:00 AM
                                - 6:00 PM</p>
                            <p><span class="font-semibold text-slate-800">Phone:</span> +63 912 345 6789</p>
                            <p><span class="font-semibold text-slate-800">Address:</span> 251 Commonwealth Ave, Diliman,
                                Quezon City</p>
                        </div>
                    </div>
                </article>
            </section>
        </div>
    </main>
@endsection
