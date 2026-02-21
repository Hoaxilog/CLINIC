@extends('index')

@section('content')
<main id="mainContent" class="min-h-screen bg-gray-100 p-6 lg:p-8 ml-64 mt-14 transition-all duration-300 peer-[.collapsed]:ml-16">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">My Dashboard</h1>
            <p class="text-sm text-gray-500 mt-1">
                @if($patient)
                    Welcome back, {{ $patient->first_name }}. Here is your appointment overview.
                @else
                    Welcome. Book your first appointment to see your history here.
                @endif
            </p>
        </div>
        <a href="{{ route('book') }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#0086DA] text-white text-sm font-semibold shadow-sm hover:bg-[#0a74b8] transition-colors">
            Book Appointment
        </a>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Upcoming Appointment</h2>
                @if($upcomingAppointment)
                    <span class="text-xs text-gray-500">
                        {{ \Carbon\Carbon::parse($upcomingAppointment->appointment_date)->format('M d, Y') }}
                    </span>
                @endif
            </div>

            @if($upcomingAppointment)
                @php
                    $status = $upcomingAppointment->status ?? 'Scheduled';
                    $badgeClass = 'bg-blue-100 text-blue-700 border-blue-200';
                    if ($status === 'Waiting') {
                        $badgeClass = 'bg-yellow-100 text-yellow-700 border-yellow-200';
                    } elseif ($status === 'Ongoing') {
                        $badgeClass = 'bg-indigo-100 text-indigo-700 border-indigo-200';
                    } elseif ($status === 'Completed') {
                        $badgeClass = 'bg-green-100 text-green-700 border-green-200';
                    } elseif ($status === 'Cancelled') {
                        $badgeClass = 'bg-red-100 text-red-700 border-red-200';
                    }
                @endphp
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="text-2xl font-bold text-gray-900">
                            {{ \Carbon\Carbon::parse($upcomingAppointment->appointment_date)->format('h:i A') }}
                        </div>
                        <div class="text-sm text-gray-600 mt-1">
                            {{ $upcomingAppointment->service_name ?? 'Service' }}
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $badgeClass }}">
                            {{ $status }}
                        </span>
                    </div>
                </div>
            @else
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="text-lg font-semibold text-gray-900">No upcoming appointments</div>
                        <div class="text-sm text-gray-500 mt-1">Book a visit to reserve your preferred time.</div>
                    </div>
                    <a href="{{ route('book') }}"
                        class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                        Book now
                    </a>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Appointment History</h2>
                <span class="text-xs text-gray-400">Recent</span>
            </div>
            @if($appointmentHistory->count() > 0)
                <div class="space-y-3">
                    @foreach($appointmentHistory as $item)
                        @php
                            $status = $item->status ?? 'Completed';
                            $badgeClass = $status === 'Completed'
                                ? 'bg-green-100 text-green-700 border-green-200'
                                : 'bg-red-100 text-red-700 border-red-200';
                        @endphp
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ $item->service_name ?? 'Service' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($item->appointment_date)->format('M d, Y') }}
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $badgeClass }}">
                                {{ $status }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500">No past appointments yet.</p>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Treatment Records</h2>
            <span class="text-xs text-gray-400">Recent</span>
        </div>
        @if($treatmentRecords->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-500 uppercase border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Date</th>
                            <th class="px-4 py-3 font-semibold">Procedure</th>
                            <th class="px-4 py-3 font-semibold">Dentist</th>
                            <th class="px-4 py-3 font-semibold">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($treatmentRecords as $record)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap text-gray-900 font-medium">
                                    {{ \Carbon\Carbon::parse($record->updated_at)->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $record->treatment ?? '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $record->dmd ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-gray-500">
                                    {{ $record->remarks ?? 'No notes' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm text-gray-500">No treatment records yet.</p>
        @endif
    </div>
</main>
@endsection
