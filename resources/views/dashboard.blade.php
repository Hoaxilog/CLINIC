@extends('index')

@section('content')
    @php
        $isAdmin = auth()->user()?->role === 1;
        $appointmentsQuickLink = route('appointment.calendar');
        $profitQuickLink = $isAdmin ? route('reports.index', ['section' => 'overview']) : '#needs-attention';
        $marginQuickLink = $isAdmin ? route('reports.index', ['section' => 'overview']) : '#needs-attention';
        $cancellationQuickLink = $isAdmin ? route('reports.index', ['section' => 'appointments']) : '#status-breakdown';
        $monthlyProfitQuickLink = $isAdmin ? route('reports.index', ['section' => 'overview']) : '#today-schedule';
    @endphp

    <main id="mainContent"
        class="min-h-screen bg-[#f3f4f6] p-6 lg:p-8 ml-64 mt-14 transition-all duration-300 peer-[.collapsed]:ml-16">

        <div class="grid items-start grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
            <a href="{{ route('patient-records') }}"
                class="block self-start h-fit group rounded-none border border-gray-100 bg-white p-6 shadow-sm transition hover:border-[#0086DA] hover:shadow-md">
                <p class="text-sm font-semibold text-gray-600">Total Patients</p>
                <div class="mt-3 text-4xl font-bold text-gray-900">{{ $totalPatients ?? 0 }}</div>
                <div class="mt-2 flex items-center justify-between gap-3">
                    <p class="text-xs font-medium text-gray-500">All registered patients</p>
                    <p class="text-xs font-semibold text-[#0086DA]">View</p>
                </div>
            </a>

            <a href="{{ $appointmentsQuickLink }}"
                class="block self-start h-fit group rounded-none border border-gray-100 bg-white p-6 shadow-sm transition hover:border-[#0086DA] hover:shadow-md">
                <p class="text-sm font-semibold text-gray-600">Today's Appointments</p>
                <div class="mt-3 text-4xl font-bold text-gray-900">{{ $todayAppointmentsCount ?? 0 }}</div>
                <div class="mt-2 flex items-center justify-between gap-3">
                    <p class="text-xs font-medium text-gray-500">
                        <span class="text-emerald-600">{{ $todayCompletedCount ?? 0 }} completed</span>
                        <span class="mx-1 text-gray-300">|</span>
                        <span class="text-rose-600">{{ $todayCancelledCount ?? 0 }} cancelled</span>
                    </p>
                    <p class="text-xs font-semibold text-[#0086DA]">View</p>
                </div>
            </a>

            <a href="{{ $profitQuickLink }}"
                class="block self-start h-fit group rounded-none border border-gray-100 bg-white p-6 shadow-sm transition hover:border-[#0086DA] hover:shadow-md">
                <p class="text-sm font-semibold text-gray-600">Today's Profit</p>
                <div class="mt-3 text-4xl font-bold {{ ($todayProfit ?? 0) >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                    PHP {{ number_format($todayProfit ?? 0, 2) }}
                </div>
                <div class="mt-2 flex items-center justify-between gap-3">
                    <p class="text-xs font-medium text-gray-500">
                        Revenue: PHP {{ number_format($todayRevenue ?? 0, 2) }}
                        <span class="mx-1 text-gray-300">|</span>
                        Cost: PHP {{ number_format($todayCost ?? 0, 2) }}
                    </p>
                    <p class="text-xs font-semibold text-[#0086DA]">View</p>
                </div>
            </a>

            <a href="{{ $marginQuickLink }}"
                class="block self-start h-fit group rounded-none border border-gray-100 bg-white p-6 shadow-sm transition hover:border-[#0086DA] hover:shadow-md">
                <p class="text-sm font-semibold text-gray-600">Today Margin</p>
                <div class="mt-3 text-4xl font-bold text-gray-900">
                    {{ $todayProfitMargin === null ? '--' : number_format($todayProfitMargin, 1) . '%' }}
                </div>
                <div class="mt-2 flex items-center justify-between gap-3">
                    <p class="text-xs font-medium text-gray-500">Profit as percentage of today's collected payment</p>
                    <p class="text-xs font-semibold text-[#0086DA]">View</p>
                </div>
            </a>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
            <div id="pending-approvals">
                @livewire('pending-approvals-widget')
            </div>

            <section id="today-schedule" class="rounded-none border border-gray-100 bg-white p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Today's Appointment Schedule</h2>
                        <p class="mt-0.5 text-xs text-gray-500">Today's booked appointments and status.</p>
                    </div>
                </div>

                <div class="max-h-[380px] overflow-auto rounded-none border border-gray-100">
                    <table class="min-w-full text-sm">
                        <thead class="sticky top-0 bg-gray-50/95 text-xs uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-4 py-3 text-left">Time</th>
                                <th class="px-4 py-3 text-left">Patient Name</th>
                                <th class="px-4 py-3 text-left">Service</th>
                                <th class="px-4 py-3 text-left">Status</th>
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
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-gray-900">
                                        {{ \Carbon\Carbon::parse($appt->appointment_date)->format('h:i A') }}</td>
                                    <td class="px-4 py-3 text-gray-800">{{ $appt->last_name }}, {{ $appt->first_name }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">{{ $appt->service_name }}</td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $badgeClass }}">{{ $appt->status }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">No appointments
                                        scheduled today.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
            <section id="needs-attention" class="rounded-none border border-gray-100 bg-white p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Needs Attention</h2>
                        <p class="mt-0.5 text-xs text-gray-500">Priority checks for operations and revenue.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <a href="#pending-approvals"
                        class="block rounded-none bg-rose-50 p-4 transition hover:ring-1 hover:ring-rose-300">
                        <div class="text-xs font-semibold uppercase tracking-wide text-rose-700">Pending Requests</div>
                        <div class="mt-2 text-3xl font-bold text-rose-800">{{ $pendingApprovalsCount ?? 0 }}</div>
                        <p class="mt-1 text-xs text-rose-700/80">Unapproved appointment requests.<span
                                class="float-right font-semibold text-rose-700">View</span></p>
                    </a>
                    <a href="{{ route('queue') }}"
                        class="block rounded-none bg-amber-50 p-4 transition hover:ring-1 hover:ring-amber-300">
                        <div class="text-xs font-semibold uppercase tracking-wide text-amber-700">Queue Load</div>
                        <div class="mt-2 text-3xl font-bold text-amber-800">
                            {{ ($waitingPatientsCount ?? 0) + ($arrivedPatientsCount ?? 0) }}</div>
                        <p class="mt-1 text-xs text-amber-700/80">
                            Waiting {{ $waitingPatientsCount ?? 0 }} · Arrived {{ $arrivedPatientsCount ?? 0 }}
                            <span class="float-right font-semibold text-amber-700">View</span>
                        </p>
                    </a>
                    <a href="{{ $cancellationQuickLink }}"
                        class="block rounded-none bg-sky-50 p-4 transition hover:ring-1 hover:ring-sky-300">
                        <div class="text-xs font-semibold uppercase tracking-wide text-sky-700">
                            {{ $cancellationLabel ?? 'This Month' }} Cancellation</div>
                        <div class="mt-2 text-3xl font-bold text-sky-800">{{ number_format($cancellationRate ?? 0, 1) }}%
                        </div>
                        <p class="mt-1 text-xs text-sky-700/80">{{ $cancelledLast30 ?? 0 }} of {{ $bookedLast30 ?? 0 }}
                            booked appointments.<span class="float-right font-semibold text-sky-700">View</span></p>
                    </a>
                    <a href="{{ $monthlyProfitQuickLink }}"
                        class="block rounded-none bg-emerald-50 p-4 transition hover:ring-1 hover:ring-emerald-300">
                        <div class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Monthly Profit</div>
                        <div class="mt-2 text-3xl font-bold text-emerald-800">PHP {{ number_format($monthProfit ?? 0, 2) }}
                        </div>
                        <p class="mt-1 text-xs text-emerald-700/80">
                            {{ $monthProfitPct === null ? 'No previous month baseline' : ($monthProfitPct >= 0 ? '+' : '') . $monthProfitPct . '% vs last month' }}
                            <span class="float-right font-semibold text-emerald-700">View</span>
                        </p>
                        <p class="mt-1 text-xs text-emerald-700/80">
                            Revenue PHP {{ number_format($monthRevenue ?? 0, 2) }} · Cost PHP
                            {{ number_format($monthCost ?? 0, 2) }}
                        </p>
                    </a>
                </div>
            </section>

            <section id="status-breakdown" class="rounded-none border border-gray-100 bg-white p-6 shadow-sm">
                <div class="mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Appointment Status Today</h2>
                    <p class="mt-0.5 text-xs text-gray-500">Pie or doughnut breakdown for today's statuses.</p>
                </div>
                <div id="statusChartWrap" class="relative min-h-[260px] w-full">
                    <canvas id="dashboardStatusTodayChart"></canvas>
                </div>
                <div id="statusChartEmpty"
                    class="hidden rounded-none border border-dashed border-gray-200 bg-gray-50 px-4 py-10 text-center text-sm font-medium text-gray-500">
                    No appointments recorded today
                </div>
            </section>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
            <section id="recent-activity" class="rounded-none border border-gray-100 bg-white p-6 shadow-sm">
                <div class="mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Recent Activity</h2>
                    <p class="mt-0.5 text-xs text-gray-500">Latest system actions (up to 5).</p>
                </div>

                <div class="max-h-[320px] overflow-auto rounded-none border border-gray-100">
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
                                    <td class="px-4 py-3 font-semibold text-gray-900">
                                        {{ $activity->causer_name ?? 'System' }}</td>
                                    <td class="px-4 py-3 text-gray-700">
                                        {{ $activity->description ?: $activity->event ?? 'Activity updated' }}</td>
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ \Carbon\Carbon::parse($activity->created_at)->format('M d, Y h:i A') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">No recent
                                        activity found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="rounded-none border border-gray-100 bg-white p-6 shadow-sm">
                <div class="mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Quick Actions</h2>
                    <p class="mt-0.5 text-xs text-gray-500">Run common tasks instantly.</p>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <button type="button" id="addPatientQuickAction"
                        class="flex items-center gap-2 rounded-none border border-gray-200 bg-white px-4 py-3 text-left text-sm font-semibold text-gray-800 hover:border-[#0086DA] hover:text-[#0086DA] transition">
                        <span>+</span>
                        <span>Add Patient</span>
                    </button>
                    <a href="{{ route('appointment') }}"
                        class="flex items-center gap-2 rounded-none border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-800 hover:border-[#0086DA] hover:text-[#0086DA] transition">
                        <span>??</span>
                        <span>Book Appointment</span>
                    </a>
                    <a href="{{ route('queue') }}"
                        class="flex items-center gap-2 rounded-none border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-800 hover:border-[#0086DA] hover:text-[#0086DA] transition">
                        <span>??</span>
                        <span>Register Walk-In Patient</span>
                    </a>
                    <a href="#today-schedule"
                        class="flex items-center gap-2 rounded-none border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-800 hover:border-[#0086DA] hover:text-[#0086DA] transition">
                        <span>??</span>
                        <span>View Today's Schedule</span>
                    </a>
                    @if (auth()->user()?->role === 1)
                        <a href="{{ route('reports.index') }}"
                            class="flex items-center gap-2 rounded-none border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-800 hover:border-[#0086DA] hover:text-[#0086DA] transition sm:col-span-2">
                            <span>??</span>
                            <span>Generate Reports</span>
                        </a>
                    @else
                        <a href="{{ route('appointment') }}"
                            class="flex items-center gap-2 rounded-none border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-800 hover:border-[#0086DA] hover:text-[#0086DA] transition sm:col-span-2">
                            <span>??</span>
                            <span>Open Appointments</span>
                        </a>
                    @endif
                </div>
            </section>
        </div>

        <livewire:patient-form-controller.patient-form-modal />

        <div id="dashboard-chart-data" data-status-labels='@json($statusLabels ?? [])'
            data-status-counts='@json($statusCounts ?? [])' class="hidden">
        </div>
    </main>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

            const dataEl = document.getElementById('dashboard-chart-data');
            const statusCtx = document.getElementById('dashboardStatusTodayChart');
            const statusChartWrap = document.getElementById('statusChartWrap');
            const statusChartEmpty = document.getElementById('statusChartEmpty');

            if (!dataEl || !statusCtx || typeof Chart === 'undefined') {
                return;
            }

            const statusLabels = JSON.parse(dataEl.dataset.statusLabels || '[]');
            const statusCounts = JSON.parse(dataEl.dataset.statusCounts || '[]').map((value) => Number(value || 0));
            const totalStatusCount = statusCounts.reduce((sum, value) => sum + value, 0);

            if (totalStatusCount === 0) {
                if (statusChartWrap) {
                    statusChartWrap.classList.add('hidden');
                }
                if (statusChartEmpty) {
                    statusChartEmpty.classList.remove('hidden');
                }
                return;
            }

            new Chart(statusCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusCounts,
                        backgroundColor: ['#3b82f6', '#f59e0b', '#06b6d4', '#10b981', '#16a34a',
                            '#ef4444'
                        ],
                        borderWidth: 0,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                                boxHeight: 10,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                padding: 14
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
