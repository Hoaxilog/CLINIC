@extends('index')

@section('content')
@php
    $hasUpcoming = (bool) $upcomingAppointment;
    $upcomingAt = $hasUpcoming ? \Carbon\Carbon::parse($upcomingAppointment->appointment_date) : null;
    $upcomingStatus = $upcomingAppointment->status ?? 'Scheduled';
    $upcomingBadge = 'bg-blue-100 text-blue-700 border-blue-200';

    if ($upcomingStatus === 'Waiting') {
        $upcomingBadge = 'bg-amber-100 text-amber-700 border-amber-200';
    } elseif ($upcomingStatus === 'Ongoing') {
        $upcomingBadge = 'bg-indigo-100 text-indigo-700 border-indigo-200';
    } elseif ($upcomingStatus === 'Completed') {
        $upcomingBadge = 'bg-emerald-100 text-emerald-700 border-emerald-200';
    } elseif ($upcomingStatus === 'Cancelled') {
        $upcomingBadge = 'bg-rose-100 text-rose-700 border-rose-200';
    }

    $completedCount = $appointmentHistory->where('status', 'Completed')->count();
    $cancelledCount = $appointmentHistory->where('status', 'Cancelled')->count();
    $patientName = $patient->first_name ?? auth()->user()->username;
@endphp

<main id="mainContent" class="min-h-screen bg-slate-50 p-4 pb-10 sm:p-6 lg:p-8">
    <section class="rounded-2xl bg-gradient-to-r from-sky-600 via-cyan-600 to-teal-600 px-5 py-6 sm:px-7 sm:py-7 text-white shadow-lg">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.25em] text-sky-100">Patient Portal</p>
                <h1 class="mt-2 text-2xl font-bold sm:text-3xl">Welcome back, {{ $patientName }}.</h1>
                <p class="mt-2 max-w-2xl text-sm text-cyan-50">
                    @if($hasUpcoming)
                        You are scheduled for {{ $upcomingAt->format('l, M d') }} at {{ $upcomingAt->format('h:i A') }}.
                    @else
                        Keep your care on track. Book your next visit and monitor your treatment history here.
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('book') }}" class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-sky-700 hover:bg-sky-50 transition-colors">
                    Book Appointment
                </a>
                <a href="{{ route('profile.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-white/40 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10 transition-colors">
                    My Profile
                </a>
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-4 md:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Next Appointment</p>
            @if($hasUpcoming)
                <p class="mt-3 text-2xl font-bold text-slate-900">{{ $upcomingAt->format('h:i A') }}</p>
                <p class="mt-1 text-sm text-slate-500">{{ $upcomingAt->format('l, M d Y') }}</p>
                <p class="mt-2 text-sm font-medium text-slate-700">{{ $upcomingAppointment->service_name ?? 'Service' }}</p>
            @else
                <p class="mt-3 text-lg font-semibold text-slate-900">No appointment yet</p>
                <p class="mt-1 text-sm text-slate-500">Choose your preferred date and time.</p>
            @endif
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Visit Summary</p>
            <p class="mt-3 text-2xl font-bold text-slate-900">{{ $appointmentHistory->count() }}</p>
            <p class="mt-1 text-sm text-slate-500">Recent finished or cancelled appointments.</p>
            <p class="mt-3 text-xs text-emerald-700 font-semibold">{{ $completedCount }} completed</p>
            <p class="mt-1 text-xs text-rose-700 font-semibold">{{ $cancelledCount }} cancelled</p>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Treatment Records</p>
            <p class="mt-3 text-2xl font-bold text-slate-900">{{ $treatmentRecords->count() }}</p>
            <p class="mt-1 text-sm text-slate-500">Latest clinical notes and procedures.</p>
            <a href="{{ route('book') }}" class="mt-4 inline-flex text-sm font-semibold text-sky-700 hover:text-sky-800">Need a follow-up? Book now.</a>
        </article>
    </section>

    <section class="mt-6 grid gap-6 lg:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900">Today and Next Steps</h2>
                @if($hasUpcoming)
                    <span class="rounded-full border px-3 py-1 text-xs font-semibold {{ $upcomingBadge }}">{{ $upcomingStatus }}</span>
                @endif
            </div>

            @if($hasUpcoming)
                <div class="mt-5 rounded-xl bg-slate-50 p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Upcoming Service</p>
                    <p class="mt-1 text-base font-semibold text-slate-900">{{ $upcomingAppointment->service_name ?? 'Service' }}</p>
                    <p class="mt-1 text-sm text-slate-600">{{ $upcomingAt->format('l, M d Y') }} at {{ $upcomingAt->format('h:i A') }}</p>
                </div>
            @else
                <div class="mt-5 rounded-xl bg-slate-50 p-4">
                    <p class="text-base font-semibold text-slate-900">No upcoming appointments</p>
                    <p class="mt-1 text-sm text-slate-600">Booking your next checkup helps keep your treatment on schedule.</p>
                </div>
            @endif

            <div class="mt-4 grid gap-3 sm:grid-cols-3">
                <a href="{{ route('book') }}" class="rounded-lg border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">Book a Visit</a>
                <a href="{{ route('book') }}" class="rounded-lg border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reschedule</a>
                <a href="{{ route('profile.index') }}" class="rounded-lg border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">Update Profile</a>
            </div>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900">Recent Visits</h2>
                <span class="text-xs text-slate-400">Latest</span>
            </div>
            @if($appointmentHistory->count() > 0)
                <div class="mt-4 space-y-3">
                    @foreach($appointmentHistory->take(5) as $item)
                        @php
                            $itemStatus = $item->status ?? 'Completed';
                            $itemBadge = $itemStatus === 'Completed'
                                ? 'bg-emerald-100 text-emerald-700 border-emerald-200'
                                : 'bg-rose-100 text-rose-700 border-rose-200';
                        @endphp
                        <div class="rounded-lg border border-slate-100 p-3">
                            <p class="text-sm font-semibold text-slate-900">{{ $item->service_name ?? 'Service' }}</p>
                            <p class="mt-0.5 text-xs text-slate-500">{{ \Carbon\Carbon::parse($item->appointment_date)->format('M d, Y') }}</p>
                            <span class="mt-2 inline-flex rounded-full border px-2.5 py-1 text-[11px] font-semibold {{ $itemBadge }}">{{ $itemStatus }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="mt-4 text-sm text-slate-500">No visit history yet.</p>
            @endif
        </article>
    </section>

    <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Treatment Timeline</h2>
            <span class="text-xs text-slate-400">Clinical Notes</span>
        </div>
        @if($treatmentRecords->count() > 0)
            <div class="mt-5 overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="border-b border-slate-100 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Date</th>
                            <th class="px-4 py-3 font-semibold">Procedure</th>
                            <th class="px-4 py-3 font-semibold">Dentist</th>
                            <th class="px-4 py-3 font-semibold">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($treatmentRecords as $record)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap font-medium text-slate-900">{{ \Carbon\Carbon::parse($record->updated_at)->format('M d, Y') }}</td>
                                <td class="px-4 py-3">{{ $record->treatment ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $record->dmd ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-500">{{ $record->remarks ?? 'No notes provided' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="mt-4 text-sm text-slate-500">Treatment records will appear here after your first completed visit.</p>
        @endif
    </section>
</main>
@endsection
