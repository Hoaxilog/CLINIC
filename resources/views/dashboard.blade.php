@extends('index')

@section('content')
    @php
        $isAdminDashboard = $isAdminDashboard ?? auth()->user()?->isAdmin();
        $isDentistDashboard = $isDentistDashboard ?? auth()->user()?->isDentist();
        $isStaffDashboard = $isStaffDashboard ?? auth()->user()?->isStaff();

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
                    'label' => 'Registered Patients',
                    'value' => $totalPatients ?? 0,
                    'meta' => 'Current patient records',
                    'href' => route('patient-records'),
                    'accent' => 'text-emerald-700',
                ],
                [
                    'label' => 'Today\'s Appointments',
                    'value' => $todayAppointmentsCount ?? 0,
                    'meta' => ($todayCompletedCount ?? 0).' completed today',
                    'href' => route('appointment.calendar'),
                    'accent' => 'text-sky-700',
                ],
                [
                    'label' => ($cancellationLabel ?? 'This Month').' Cancellation',
                    'value' => number_format($cancellationRate ?? 0, 1).'%',
                    'meta' => ($cancelledLast30 ?? 0).' of '.($bookedLast30 ?? 0).' booked',
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
                    'meta' => 'Waiting '.($waitingPatientsCount ?? 0).' · Arrived '.($arrivedPatientsCount ?? 0),
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

    <main id="mainContent"
        class="min-h-screen bg-[#f3f4f6] p-6 lg:p-8 ml-64 mt-14 transition-all duration-300 peer-[.collapsed]:ml-16">

        <section class="border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div
                        class="inline-flex items-center gap-2 border border-[#0086DA]/15 bg-[#0086DA]/5 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-[#0086DA]">
                        {{ $isAdminDashboard ? 'Management View' : ($isDentistDashboard ? 'Clinical View' : 'Operations View') }}
                    </div>
                    <h1 class="mt-3 text-3xl font-bold tracking-tight text-gray-900">{{ $roleTitle }}</h1>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-gray-600">{{ $roleSummary }}</p>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <a href="{{ $isAdminDashboard ? route('reports.index') : route('appointment.calendar') }}"
                        class="border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-800 transition hover:border-[#0086DA] hover:text-[#0086DA]">
                        {{ $isAdminDashboard ? 'Open Reports' : 'Open Schedule' }}
                    </a>
                    <a href="{{ $isAdminDashboard ? route('users.index') : ($isDentistDashboard ? route('queue') : route('appointment.requests')) }}"
                        class="border border-[#0086DA] bg-[#0086DA] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#006ab0]">
                        {{ $isAdminDashboard ? 'Open User Accounts' : ($isDentistDashboard ? 'Open Queue' : 'Open Appointment Requests') }}
                    </a>
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
                        <p class="text-xs font-medium text-gray-500">{{ $card['meta'] }}</p>
                        <span class="shrink-0 text-sm font-semibold {{ $card['accent'] }}">View</span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
            <div id="pending-approvals">
                @livewire('pending-approvals-widget')
            </div>

            <section id="today-schedule" class="border border-gray-200 bg-white p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Today's Appointment Schedule</h2>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ $isAdminDashboard ? 'Clinic-wide appointment visibility for scheduling and oversight.' : ($isDentistDashboard ? 'Today\'s booked patients and treatment flow.' : 'Today\'s booked patients and front-desk appointment flow.') }}
                        </p>
                    </div>
                    </div>

                <div class="max-h-[380px] overflow-auto border border-gray-100">
                    <table class="min-w-full text-sm">
                        <thead class="sticky top-0 bg-gray-50/95 text-xs uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-4 py-3 text-left">Time</th>
                                <th class="px-4 py-3 text-left">Patient Name</th>
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
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-gray-900">
                                        {{ \Carbon\Carbon::parse($appt->appointment_date)->format('h:i A') }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-800">{{ $appt->last_name }}, {{ $appt->first_name }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $appt->service_name }}</td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $badgeClass }}">{{ $appt->status }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('appointment.calendar', $viewSlotParams) }}"
                                            class="inline-flex whitespace-nowrap border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-gray-800 transition hover:border-[#0086DA] hover:text-[#0086DA]">
                                            View Slot
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
            @if ($isDentistDashboard)
                <section id="needs-attention" class="border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="mb-4">
                        <h2 class="text-lg font-bold text-gray-900">Clinical Priorities</h2>
                        <p class="mt-1 text-xs text-gray-500">Quick links to the patients and queues that need immediate attention.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <a href="{{ route('queue') }}"
                            class="block border border-amber-100 bg-amber-50 p-4 transition hover:border-amber-300">
                            <div class="text-xs font-semibold uppercase tracking-wide text-amber-700">Waiting + Arrived</div>
                            <div class="mt-2 text-3xl font-bold text-amber-800">{{ $queueLoadCount ?? 0 }}</div>
                            <p class="mt-1 text-xs text-amber-700/80">Waiting {{ $waitingPatientsCount ?? 0 }} · Arrived {{ $arrivedPatientsCount ?? 0 }}</p>
                        </a>

                        <a href="{{ route('appointment.calendar') }}"
                            class="block border border-sky-100 bg-sky-50 p-4 transition hover:border-sky-300">
                            <div class="text-xs font-semibold uppercase tracking-wide text-sky-700">Completed Today</div>
                            <div class="mt-2 text-3xl font-bold text-sky-800">{{ $todayCompletedCount ?? 0 }}</div>
                            <p class="mt-1 text-xs text-sky-700/80">Finished appointments for today.</p>
                        </a>

                        <a href="{{ route('patient-records') }}"
                            class="block border border-rose-100 bg-rose-50 p-4 transition hover:border-rose-300">
                            <div class="text-xs font-semibold uppercase tracking-wide text-rose-700">Patient Records</div>
                            <div class="mt-2 text-3xl font-bold text-rose-800">{{ $totalPatients ?? 0 }}</div>
                            <p class="mt-1 text-xs text-rose-700/80">Open charts and patient details quickly.</p>
                        </a>

                        <a href="{{ route('appointment.calendar') }}"
                            class="block border border-emerald-100 bg-emerald-50 p-4 transition hover:border-emerald-300">
                            <div class="text-xs font-semibold uppercase tracking-wide text-emerald-700">{{ $cancellationLabel ?? 'This Month' }} Cancellation</div>
                            <div class="mt-2 text-3xl font-bold text-emerald-800">{{ number_format($cancellationRate ?? 0, 1) }}%</div>
                            <p class="mt-1 text-xs text-emerald-700/80">{{ $cancelledLast30 ?? 0 }} of {{ $bookedLast30 ?? 0 }} booked appointments.</p>
                        </a>
                    </div>
                </section>
            @elseif ($isAdminDashboard)
                <section id="needs-attention" class="border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="mb-4">
                        <h2 class="text-lg font-bold text-gray-900">Management Snapshot</h2>
                        <p class="mt-1 text-xs text-gray-500">Track workload, pending requests, and clinic flow at a glance.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <a href="{{ route('users.index') }}"
                            class="block border border-indigo-100 bg-indigo-50 p-4 transition hover:border-indigo-300">
                            <div class="text-xs font-semibold uppercase tracking-wide text-indigo-700">User Accounts</div>
                            <div class="mt-2 text-3xl font-bold text-indigo-800">Manage</div>
                            <p class="mt-1 text-xs text-indigo-700/80">Review access across admin, dentist, and staff accounts.</p>
                        </a>

                        <a href="{{ route('reports.index') }}"
                            class="block border border-emerald-100 bg-emerald-50 p-4 transition hover:border-emerald-300">
                            <div class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Reports</div>
                            <div class="mt-2 text-3xl font-bold text-emerald-800">{{ number_format($monthProfit ?? 0, 0) }}</div>
                            <p class="mt-1 text-xs text-emerald-700/80">Current month profit overview.</p>
                        </a>

                        <a href="{{ route('activity-logs') }}"
                            class="block border border-sky-100 bg-sky-50 p-4 transition hover:border-sky-300">
                            <div class="text-xs font-semibold uppercase tracking-wide text-sky-700">Recent Activity</div>
                            <div class="mt-2 text-3xl font-bold text-sky-800">{{ count($recentActivities ?? []) }}</div>
                            <p class="mt-1 text-xs text-sky-700/80">Latest actions available for audit review.</p>
                        </a>

                        <a href="{{ route('appointment.requests') }}"
                            class="block border border-amber-100 bg-amber-50 p-4 transition hover:border-amber-300">
                            <div class="text-xs font-semibold uppercase tracking-wide text-amber-700">Pending Requests</div>
                            <div class="mt-2 text-3xl font-bold text-amber-800">{{ $pendingApprovalsCount ?? 0 }}</div>
                            <p class="mt-1 text-xs text-amber-700/80">Requests waiting for staff or admin review.</p>
                        </a>
                    </div>
                </section>
            @else
                @livewire('cancelled-appointments-widget')
            @endif

            <section class="border border-gray-200 bg-white p-6 shadow-sm">
                <div class="mb-4">
                    <h2 class="text-lg font-bold text-gray-900">{{ $isAdminDashboard ? 'Management Tools' : ($isDentistDashboard ? 'Quick Access' : 'Front Desk Support') }}</h2>
                    <p class="mt-1 text-xs text-gray-500">
                        {{ $isAdminDashboard ? 'Fast links for management, oversight, and approval work.' : ($isDentistDashboard ? 'Fast links for chairside and patient-care actions.' : 'Use these tools after reviewing cancelled appointments and follow-up needs.') }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-3">
                    @foreach ($quickLinks as $link)
                        <a href="{{ $link['href'] }}"
                            class="border border-gray-200 bg-gray-50 px-4 py-4 transition hover:border-[#0086DA] hover:bg-white">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $link['label'] }}</p>
                                    <p class="mt-1 text-xs leading-5 text-gray-500">{{ $link['description'] }}</p>
                                </div>
                                <span class="text-sm font-semibold text-[#0086DA]">Open</span>
                            </div>
                        </a>
                    @endforeach

                    @unless ($isAdminDashboard)
                        <button type="button" id="addPatientQuickAction"
                            class="border border-dashed border-gray-300 bg-white px-4 py-4 text-left transition hover:border-[#0086DA]">
                            <p class="text-sm font-semibold text-gray-900">Add Patient</p>
                            <p class="mt-1 text-xs leading-5 text-gray-500">Launch the patient form modal without leaving the dashboard.</p>
                        </button>
                    @endunless
                </div>
            </section>
        </div>

        @if ($isDentistDashboard || $isAdminDashboard)
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
        <livewire:patient-form-controller.patient-form-modal />
    </main>
@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addPatientQuickAction = document.getElementById('addPatientQuickAction');
            if (addPatientQuickAction) {
                addPatientQuickAction.addEventListener('click', function() {
                    if (window.Livewire && typeof window.Livewire.dispatch === 'function') {
                        window.Livewire.dispatch('openAddPatientModal');
                    }
                });
            }
        });
    </script>
@endpush
