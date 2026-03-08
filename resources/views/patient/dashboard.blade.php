@extends('index')

@section('content')
@php
    $hasUpcoming = (bool) $upcomingAppointment;
    $upcomingAt = $hasUpcoming ? \Carbon\Carbon::parse($upcomingAppointment->appointment_date) : null;
    $patientName = $patient->first_name ?? auth()->user()->username;
@endphp

<main id="mainContent" class="min-h-screen bg-slate-50 p-4 pb-10 sm:p-6 lg:p-8">
    <div class="mb-6">
        <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Patient Portal</p>
        <h1 class="mt-2 text-2xl font-bold text-slate-900 sm:text-3xl">Welcome, {{ $patientName }}.</h1>
        <p class="mt-2 max-w-2xl text-sm text-slate-600">
            @if($hasUpcoming)
                You are scheduled for {{ $upcomingAt->format('l, M d') }} at {{ $upcomingAt->format('h:i A') }}.
            @else
                Keep your care on track. Book your next visit and monitor your treatment history here.
            @endif
        </p>
    </div>

    <section class="mt-6 grid gap-4 md:grid-cols-2">
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
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Appointment Requests</p>
                <span class="text-xs text-slate-400">Pending & upcoming</span>
            </div>
            @if($appointmentRequests->count() > 0)
                <div class="mt-4 space-y-3">
                    @foreach($appointmentRequests->take(4) as $request)
                        @php
                            $reqStatus = $request->status ?? 'Pending';
                            $reqBadge = match ($reqStatus) {
                                'Waiting' => 'bg-amber-100 text-amber-700 border-amber-200',
                                'Ongoing' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                                'Scheduled' => 'bg-blue-100 text-blue-700 border-blue-200',
                                default => 'bg-slate-100 text-slate-700 border-slate-200',
                            };
                        @endphp
                        <div class="rounded-xl border border-slate-100 p-3">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $request->service_name ?? 'Service' }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">
                                        {{ \Carbon\Carbon::parse($request->appointment_date)->format('M d, Y') }}
                                        · {{ \Carbon\Carbon::parse($request->appointment_date)->format('h:i A') }}
                                    </p>
                                </div>
                                <span class="inline-flex rounded-full border px-2.5 py-1 text-[11px] font-semibold {{ $reqBadge }}">
                                    {{ $reqStatus }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="mt-4 text-sm text-slate-500">No appointment requests yet.</p>
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



