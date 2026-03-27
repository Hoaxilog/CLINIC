@extends('index')

@section('content')
    @php
        $isAdminDashboard = $isAdminDashboard ?? auth()->user()?->isAdmin();
        $isDentistDashboard = $isDentistDashboard ?? auth()->user()?->isDentist();
        $isStaffDashboard = $isStaffDashboard ?? auth()->user()?->isStaff();
        $trendingDownIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-down-icon lucide-trending-down"><path d="M16 17h6v-6"/><path d="m22 17-8.5-8.5-5 5L2 7"/></svg>';
        $trendingUpIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-up-icon lucide-trending-up"><path d="M16 7h6v6"/><path d="m22 7-8.5 8.5-5-5L2 17"/></svg>';

        $roleTitle = $isAdminDashboard ? 'Admin Dashboard' : ($isDentistDashboard ? 'Dentist Dashboard' : 'Staff Dashboard');
        $roleSummary = $isAdminDashboard
            ? 'Use this as a management hub for user oversight, reports, activity visibility, and clinic-wide operational snapshots.'
            : ($isDentistDashboard
                ? 'Use this as a chairside control center for queue flow, active patients, and fast clinical navigation.'
                : 'Use this as a front-desk quick-link hub for appointment flow, pending approvals, and patient coordination.');

        $topCards = $isAdminDashboard
            ? [
                [
                    'label' => 'Pending Requests',
                    'value' => $pendingApprovalsCount ?? 0,
                    'meta' => 'Awaiting clinic review',
                    'href' => route('appointment.requests'),
                    'accent' => 'text-rose-700',
                ],
                [
                    'label' => 'Monthly Profit',
                    'value' => 'PHP '.number_format($monthProfit ?? 0, 2),
                    'meta' => 'Revenue: '.number_format($monthRevenue ?? 0, 0)
                        .' | Cost: '.number_format($monthCost ?? 0, 0),
                    'href' => route('reports.index'),
                    'accent' => ($monthProfitPct ?? null) === null
                        ? 'text-slate-600'
                        : (($monthProfitPct ?? 0) >= 0 ? 'text-emerald-700' : 'text-rose-700'),
                    'meta_class' => ($monthProfit ?? 0) > 0
                        ? 'text-emerald-700'
                        : (($monthProfit ?? 0) < 0 ? 'text-rose-700' : 'text-slate-500'),
                    'action_label' => (($monthProfitPct ?? null) === null
                        ? 'View'
                        : number_format(abs($monthProfitPct ?? 0), 0).'%'),
                    'action_icon' => (($monthProfitPct ?? null) === null
                        ? null
                        : (($monthProfitPct ?? 0) >= 0 ? $trendingUpIcon : $trendingDownIcon)),
                ],
                [
                    'label' => 'Today\'s Appointments',
                    'value' => $todayAppointmentsCount ?? 0,
                    'meta' => ($todayCompletedCount ?? 0).' completed today',
                    'href' => route('appointment.history'),
                    'accent' => 'text-sky-700',
                ],
                [
                    'label' => ($cancellationLabel ?? 'This Month')."'s Cancellation Rate",
                    'value' => number_format($cancellationRate ?? 0, 1).'%',
                    'meta' => ($cancelledLast30 ?? 0).' cancelled appointments out of '.($bookedLast30 ?? 0).' bookings',
                    'href' => route('reports.index'),
                    'accent' => 'text-amber-700',
                ],
            ]
            : ($isDentistDashboard
            ? [
                [
                    'label' => 'Today\'s Appointments',
                    'value' => $todayAppointmentsCount ?? 0,
                    'meta' => ($todayCompletedCount ?? 0).' completed',
                    'href' => route('appointment.calendar'),
                    'accent' => 'text-sky-700',
                ],
                [
                    'label' => 'Ongoing Patients',
                    'value' => $ongoingPatientsCount ?? 0,
                    'meta' => 'Currently under treatment',
                    'href' => route('queue'),
                    'accent' => 'text-emerald-700',
                ],
                [
                    'label' => 'Completed Today',
                    'value' => $todayCompletedCount ?? 0,
                    'meta' => ($todayCancelledCount ?? 0).' cancelled',
                    'href' => route('appointment.calendar'),
                    'accent' => 'text-indigo-700',
                ],
                [
                    'label' => 'Queue Load',
                    'value' => $queueLoadCount ?? 0,
                    'meta' => 'Waiting '.($waitingPatientsCount ?? 0).' ┬╖ Arrived '.($arrivedPatientsCount ?? 0),
                    'href' => route('queue'),
                    'accent' => 'text-amber-700',
                ],
            ]
            : [
                [
                    'label' => 'Appointment Requests',
                    'value' => $pendingApprovalsCount ?? 0,
                    'meta' => 'Pending appointment requests',
                    'href' => route('appointment.requests'),
                    'accent' => 'text-rose-700',
                ],
                [
                    'label' => 'Waiting Patients',
                    'value' => $waitingPatientsCount ?? 0,
                    'meta' => 'Ready for queue handling',
                    'href' => route('queue'),
                    'accent' => 'text-amber-700',
                ],
                [
                    'label' => 'Arrived Patients',
                    'value' => $arrivedPatientsCount ?? 0,
                    'meta' => 'Checked in and on site',
                    'href' => route('queue'),
                    'accent' => 'text-cyan-700',
                ],
                [
                    'label' => 'Today\'s Appointments',
                    'value' => $todayAppointmentsCount ?? 0,
                    'meta' => ($todayCompletedCount ?? 0).' completed today',
                    'href' => route('appointment.calendar'),
                    'accent' => 'text-sky-700',
                ],
            ]);

        $quickLinks = $isAdminDashboard
            ? [
                ['label' => 'User Accounts', 'description' => 'Manage admin, dentist, and staff accounts.', 'href' => route('users.index')],
                ['label' => 'Reports', 'description' => 'Review clinic performance and printable reports.', 'href' => route('reports.index')],
                ['label' => 'Activity Logs', 'description' => 'Audit recent actions across the system.', 'href' => route('activity-logs')],
                ['label' => 'Appointment Requests', 'description' => 'Review incoming requests without leaving the dashboard.', 'href' => route('appointment.requests')],
            ]
            : ($isDentistDashboard
            ? [
                ['label' => 'Open Queue', 'description' => 'Manage waiting and ongoing chairside flow.', 'href' => route('queue')],
                ['label' => 'Patient Records', 'description' => 'Jump straight into patient charts and records.', 'href' => route('patient-records')],
                ['label' => 'Appointment Calendar', 'description' => 'See the day schedule and status changes.', 'href' => route('appointment.calendar')],
                ['label' => 'Appointment Requests', 'description' => 'Review pending requests needing clinic attention.', 'href' => route('appointment.requests')],
            ]
            : [
                ['label' => 'Open Appointment Requests', 'description' => 'Approve or reject incoming appointment requests.', 'href' => route('appointment.requests')],
                ['label' => 'Manage Queue', 'description' => 'Monitor waiting and arrived patients in one place.', 'href' => route('queue')],
                ['label' => 'Open Patient Records', 'description' => 'Search patient details quickly when assisting the clinic flow.', 'href' => route('patient-records')],
                ['label' => 'Book Appointment', 'description' => 'Create a booking or reschedule directly.', 'href' => route('appointment')],
            ]);
    @endphp

        <section class="border border-gray-200 bg-white p-6 shadow-sm">
            <div>
                <div>
                    <div
                        class="inline-flex items-center gap-2 border border-[#0086DA]/15 bg-[#0086DA]/5 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-[#0086DA]">
                        {{ $isAdminDashboard ? 'Management View' : ($isDentistDashboard ? 'Clinical View' : 'Operations View') }}
                    </div>
                    <h1 class="mt-3 text-3xl font-bold tracking-tight text-gray-900">{{ $roleTitle }}</h1>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-gray-600">{{ $roleSummary }}</p>
                </div>
            </div>
        </section>

        <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($topCards as $card)
                <a href="{{ $card['href'] }}"
                    class="group border border-gray-200 bg-white p-5 shadow-sm transition hover:border-[#0086DA] hover:shadow-md">
                    <div>
                        <p class="text-sm font-semibold text-gray-600">{{ $card['label'] }}</p>
                        <div class="mt-3 text-4xl font-bold text-gray-900">{{ $card['value'] }}</div>
                    </div>
                    <div class="mt-4 flex items-center justify-between gap-4">
                        <p class="text-xs font-medium {{ $card['meta_class'] ?? 'text-gray-500' }}">{{ $card['meta'] }}</p>
                        <span class="shrink-0 inline-flex items-center gap-1.5 text-sm font-semibold {{ $card['accent'] }}">
                            @if (!empty($card['action_icon']))
                                {!! $card['action_icon'] !!}
                            @endif
                            {{ $card['action_label'] ?? 'View' }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
            <div id="pending-approvals">
                @livewire('dashboard.pending-approvals-widget')
            </div>

            <section id="today-schedule" class="h-[420px] overflow-hidden border border-blue-200 bg-white shadow-sm shadow-blue-100/40">
                <div class="h-1 w-full bg-linear-to-r from-blue-600 via-sky-500 to-cyan-300"></div>

                <div class="mb-4 flex items-center justify-between gap-4 border-b border-blue-100 bg-blue-50/40 p-6">
                    <div>
                        <div class="flex items-center gap-3">
                            <h2 class="text-lg font-bold text-gray-900">Today's Appointment Schedule</h2>
                      
                        </div>
                        <p class="mt-1 text-xs text-gray-600">
                            {{ $isAdminDashboard ? 'Clinic-wide appointment visibility for scheduling and oversight.' : ($isDentistDashboard ? 'Today\'s booked patients and treatment flow.' : 'Today\'s booked patients and front-desk appointment flow.') }}
                        </p>
                    </div>
                </div>

                <div class="h-[336px] overflow-auto px-6 pb-6">
                    <table class="min-w-full text-sm">
                        <thead class="sticky top-0 bg-blue-50/80 text-xs uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-4 py-3 text-left">Patient Name</th>
                                <th class="px-4 py-3 text-left">Time</th>
                                <th class="px-4 py-3 text-left">Service</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-left">Quick Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($todayScheduleAppointments as $appt)
                                @php
                                    $status = strtolower($appt->status);
                                    $badgeClass = 'bg-gray-100 text-gray-700';
                                    if ($status === 'scheduled') {
                                        $badgeClass = 'bg-blue-50 text-blue-700';
                                    } elseif ($status === 'waiting') {
                                        $badgeClass = 'bg-amber-50 text-amber-700';
                                    } elseif ($status === 'arrived') {
                                        $badgeClass = 'bg-cyan-50 text-cyan-700';
                                    } elseif ($status === 'ongoing') {
                                        $badgeClass = 'bg-emerald-50 text-emerald-700';
                                    } elseif ($status === 'completed') {
                                        $badgeClass = 'bg-green-50 text-green-700';
                                    } elseif ($status === 'cancelled') {
                                        $badgeClass = 'bg-rose-50 text-rose-700';
                                    }

                                    $scheduleDate = \Carbon\Carbon::parse($appt->appointment_date)->toDateString();
                                    $viewSlotParams = ['date' => $scheduleDate];
                                    if (isset($appt->id)) {
                                        $viewSlotParams['appointment'] = $appt->id;
                                    }
                                @endphp
                                <tr class="border-l-2 border-transparent hover:border-blue-300 hover:bg-blue-50/40">
                                    <td class="px-4 py-3 text-gray-800">
                                        <div class="flex items-center gap-2">
                                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-700">
                                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M10 2a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-7 15a7 7 0 1 1 14 0H3Z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                            <span>{{ $appt->first_name }} {{ $appt->last_name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-gray-900">
                                        {{ \Carbon\Carbon::parse($appt->appointment_date)->format('h:i A') }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">{{ $appt->service_name }}</td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $badgeClass }}">{{ $appt->status }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('appointment.calendar', $viewSlotParams) }}"
                                            class="inline-flex whitespace-nowrap border border-blue-200 bg-blue-50 px-2.5 py-1.5 text-xs font-semibold text-blue-800 transition hover:bg-blue-100">
                                            View Appointment
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">No appointments scheduled today.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
            @if ($isAdminDashboard)
                @php
                    $appointmentTrendLabels = $appointmentTrendLabels ?? [];
                    $appointmentTrendComparisonDates = $appointmentTrendComparisonDates ?? [];
                    $appointmentTrendCurrentWeek = $appointmentTrendCurrentWeek ?? [];
                    $appointmentTrendPreviousWeek = $appointmentTrendPreviousWeek ?? [];
                    $trendChartMax = max(
                        ! empty($appointmentTrendCurrentWeek) ? max($appointmentTrendCurrentWeek) : 0,
                        ! empty($appointmentTrendPreviousWeek) ? max($appointmentTrendPreviousWeek) : 0,
                        1,
                    );
                    $trendCurrentTotal = array_sum($appointmentTrendCurrentWeek);
                    $trendPreviousTotal = array_sum($appointmentTrendPreviousWeek);
                    $trendWeekDelta = $trendCurrentTotal - $trendPreviousTotal;
                @endphp
                <section id="needs-attention" class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Appointment Trend</h2>
                            <p class="mt-1 text-xs text-gray-500">Current week vs previous week, compared day by day.</p>
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-400">Week Comparison</div>
                            <div class="mt-1 text-sm font-semibold {{ $trendWeekDelta >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $trendWeekDelta >= 0 ? '+' : '' }}{{ $trendWeekDelta }} appointments
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 flex items-center gap-5 text-xs text-gray-500">
                        <div class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-sm bg-cyan-600"></span>
                            <span>This Week</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-sm bg-cyan-200"></span>
                            <span>Previous Week</span>
                        </div>
                    </div>

                    @if (! empty($appointmentTrendCurrentWeek))
                        <div class="mt-6">
                            <div class="grid h-72 grid-cols-[48px_repeat(7,minmax(0,1fr))] grid-rows-[repeat(6,minmax(0,1fr))_auto] gap-x-3 gap-y-2">
                                @for ($tick = 0; $tick <= 5; $tick++)
                                    @php
                                        $gridRow = $tick + 1;
                                        $tickValue = (int) round(($trendChartMax / 5) * (6 - $gridRow));
                                    @endphp
                                    <div class="flex items-start justify-end pr-2 text-xs text-gray-400" style="grid-column: 1; grid-row: {{ $gridRow }};">
                                        {{ $tickValue }}
                                    </div>
                                    <div class="col-start-2 col-end-9 border-t border-gray-100" style="grid-row: {{ $gridRow }};"></div>
                                @endfor

                                @foreach ($appointmentTrendCurrentWeek as $index => $count)
                                    @php
                                        $previousCount = $appointmentTrendPreviousWeek[$index] ?? 0;
                                        $currentHeight = max(6, (int) round(($count / $trendChartMax) * 100));
                                        $previousHeight = max(6, (int) round(($previousCount / $trendChartMax) * 100));
                                        $trendDifference = $count - $previousCount;
                                    @endphp
                                    <div class="group relative flex min-w-0 flex-col items-center justify-end gap-3" style="grid-column: {{ $index + 2 }}; grid-row: 1 / 7;">
                                        <div class="pointer-events-none absolute -top-24 left-1/2 z-10 w-36 -translate-x-1/2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-left opacity-0 shadow-lg transition group-hover:opacity-100">
                                            <div class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">
                                                {{ $appointmentTrendLabels[$index] ?? 'N/A' }} {{ $appointmentTrendComparisonDates[$index] ?? '' }}
                                            </div>
                                            <div class="mt-2 flex items-center justify-between gap-3 text-xs">
                                                <span class="text-gray-500">This Week</span>
                                                <span class="font-semibold text-cyan-700">{{ $count }}</span>
                                            </div>
                                            <div class="mt-1 flex items-center justify-between gap-3 text-xs">
                                                <span class="text-gray-500">Previous</span>
                                                <span class="font-semibold text-cyan-300">{{ $previousCount }}</span>
                                            </div>
                                            <div class="mt-1 flex items-center justify-between gap-3 text-xs">
                                                <span class="text-gray-500">Difference</span>
                                                <span class="font-semibold {{ $trendDifference >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                                    {{ $trendDifference >= 0 ? '+' : '' }}{{ $trendDifference }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex h-full w-full items-end justify-center gap-2">
                                            <div class="w-4 rounded-t-sm bg-cyan-200" style="height: {{ $previousHeight }}%"></div>
                                            <div class="w-4 rounded-t-sm bg-cyan-600" style="height: {{ $currentHeight }}%"></div>
                                        </div>
                                        <div class="pb-1 text-center">
                                            <div class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">
                                                {{ $appointmentTrendLabels[$index] ?? 'N/A' }}
                                            </div>
                                            <div class="mt-1 text-[11px] text-gray-400">
                                                {{ $appointmentTrendComparisonDates[$index] ?? '' }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="mt-6 rounded-xl border border-dashed border-gray-200 bg-gray-50 px-4 py-10 text-center text-sm text-gray-500">
                            No appointment trend data available for this week yet.
                        </div>
                    @endif
                </section>
            @else
                @livewire('dashboard.cancelled-appointments-widget')
            @endif

            @if ($isAdminDashboard)
                @php
                    $profitTrendMax = max(
                        ! empty($trendProfit) ? max($trendProfit) : 0,
                        1,
                    );
                    $profitTrendMin = min(
                        ! empty($trendProfit) ? min($trendProfit) : 0,
                        0,
                    );
                    $profitTrendRange = max($profitTrendMax - $profitTrendMin, 1);
                    $latestTrendProfit = ! empty($trendProfit) ? (float) end($trendProfit) : 0;
                    $previousTrendProfit = count($trendProfit) > 1 ? (float) $trendProfit[count($trendProfit) - 2] : 0;
                    $profitTrendDelta = $latestTrendProfit - $previousTrendProfit;
                @endphp
                <section class="border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Profit Over Time</h2>
                            <p class="mt-1 text-xs text-gray-500">Daily clinic profit based on treatment records for {{ $rangeLabel ?? 'the selected range' }}.</p>
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-400">Latest Day</div>
                            <div class="mt-1 text-sm font-semibold {{ $profitTrendDelta >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $profitTrendDelta >= 0 ? '+' : '' }}PHP {{ number_format($profitTrendDelta, 2) }}
                            </div>
                        </div>
                    </div>

                    @if (! empty($trendProfit))
                        <div class="mt-6 overflow-x-auto pb-2">
                            <div class="grid h-72 min-w-[720px] grid-rows-[repeat(6,minmax(0,1fr))_auto] gap-x-3 gap-y-2" style="grid-template-columns: 56px repeat({{ max(count($trendDates), 1) }}, minmax(44px, 1fr));">
                                @for ($tick = 0; $tick <= 5; $tick++)
                                    @php
                                        $gridRow = $tick + 1;
                                        $tickValue = $profitTrendMax - (($profitTrendRange / 5) * $tick);
                                    @endphp
                                    <div class="flex items-start justify-end pr-2 text-[11px] text-gray-400" style="grid-column: 1; grid-row: {{ $gridRow }};">
                                        {{ number_format($tickValue, 0) }}
                                    </div>
                                    <div class="border-t border-gray-100" style="grid-column: 2 / {{ count($trendDates) + 2 }}; grid-row: {{ $gridRow }};"></div>
                                @endfor

                                @foreach ($trendProfit as $index => $amount)
                                    @php
                                        $normalizedHeight = (float) (($amount - $profitTrendMin) / $profitTrendRange) * 100;
                                        $barHeight = max(6, (int) round($normalizedHeight));
                                        $barClass = $amount >= 0 ? 'bg-emerald-500' : 'bg-rose-500';
                                    @endphp
                                    <div class="group relative flex min-w-0 flex-col items-center justify-end gap-2" style="grid-column: {{ $index + 2 }}; grid-row: 1 / 7;">
                                        <div class="pointer-events-none absolute -top-20 left-1/2 z-10 w-32 -translate-x-1/2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-left opacity-0 shadow-lg transition group-hover:opacity-100">
                                            <div class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">{{ $trendDates[$index] ?? 'N/A' }}</div>
                                            <div class="mt-2 text-xs font-semibold {{ $amount >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                                PHP {{ number_format($amount, 2) }}
                                            </div>
                                        </div>
                                        <div class="flex h-full w-full items-end justify-center">
                                            <div class="w-5 rounded-t-sm {{ $barClass }}" style="height: {{ $barHeight }}%"></div>
                                        </div>
                                        <div class="min-h-[2.25rem] pb-1 text-center">
                                            <div class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">
                                                {{ $trendDates[$index] ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="mt-6 rounded-xl border border-dashed border-gray-200 bg-gray-50 px-4 py-10 text-center text-sm text-gray-500">
                            No profit trend data available for this range yet.
                        </div>
                    @endif
                </section>
            @else
                <section class="relative overflow-hidden border border-gray-200 bg-white shadow-sm">
                    @livewire('dashboard.notes')
                </section>
            @endif
        </div>

        @if ($isAdminDashboard)
            <div class="mt-6">
                <section id="recent-activity" class="border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="mb-4">
                        <h2 class="text-lg font-bold text-gray-900">{{ $isAdminDashboard ? 'Recent Activity & Audit Trail' : 'Recent Activity' }}</h2>
                        <p class="mt-1 text-xs text-gray-500">{{ $isAdminDashboard ? 'Latest activity across the clinic for management review.' : 'Latest actions across the clinic system.' }}</p>
                    </div>

                    <div class="max-h-[320px] overflow-auto border border-gray-100">
                        <table class="min-w-full text-sm">
                            <thead class="sticky top-0 bg-gray-50/95 text-xs uppercase tracking-wide text-gray-500">
                                <tr>
                                    <th class="px-4 py-3 text-left">User</th>
                                    <th class="px-4 py-3 text-left">Activity</th>
                                    <th class="px-4 py-3 text-left">Date/Time</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse($recentActivities as $activity)
                                    <tr>
                                        <td class="px-4 py-3 font-semibold text-gray-900">{{ $activity->causer_name ?? 'System' }}</td>
                                        <td class="px-4 py-3 text-gray-700">
                                            {{ $activity->description ?: $activity->event ?? 'Activity updated' }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-600">
                                            {{ \Carbon\Carbon::parse($activity->created_at)->format('M d, Y h:i A') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">No recent activity found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        @endif
        <livewire:patient.form.patient-form-modal />
@endsection

