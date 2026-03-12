@extends('index')

@section('content')
    <main id="mainContent"
        class="min-h-screen bg-[#f3f4f6] p-6 lg:p-8 ml-64 mt-14 transition-all duration-300 peer-[.collapsed]:ml-16">

        <div class="mb-8">
            <h1 class="text-3xl lg:text-4xl font-bold text-gray-900">Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500">Operational overview for today and quick actions.</p>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
            <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <p class="text-sm font-semibold text-gray-600">Total Patients</p>
                <div class="mt-3 text-4xl font-bold text-gray-900">{{ $totalPatients ?? 0 }}</div>
                <p class="mt-2 text-xs font-medium text-gray-500">All registered patients</p>
            </section>

            <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <p class="text-sm font-semibold text-gray-600">Today's Appointments</p>
                <div class="mt-3 text-4xl font-bold text-gray-900">{{ $todayAppointmentsCount ?? 0 }}</div>
                <p class="mt-2 text-xs font-medium text-gray-500">
                    <span class="text-emerald-600">{{ $todayCompletedCount ?? 0 }} completed</span>
                    <span class="mx-1 text-gray-300">|</span>
                    <span class="text-rose-600">{{ $todayCancelledCount ?? 0 }} cancelled</span>
                </p>
            </section>

            <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <p class="text-sm font-semibold text-gray-600">Waiting / Arrived</p>
                <div class="mt-3 text-4xl font-bold text-gray-900">{{ ($waitingPatientsCount ?? 0) + ($arrivedPatientsCount ?? 0) }}</div>
                <p class="mt-2 text-xs font-medium text-gray-500">
                    Waiting: {{ $waitingPatientsCount ?? 0 }}
                    <span class="mx-1 text-gray-300">|</span>
                    Arrived: {{ $arrivedPatientsCount ?? 0 }}
                </p>
            </section>

            <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <p class="text-sm font-semibold text-gray-600">Pending Requests</p>
                <div class="mt-3 text-4xl font-bold text-gray-900">{{ $pendingApprovalsCount ?? 0 }}</div>
                <p class="mt-2 text-xs font-medium text-gray-500">Appointment requests awaiting approval</p>
            </section>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
            <div>
                @livewire('pending-approvals-widget')
            </div>

            <section id="today-schedule" class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Today's Appointment Schedule</h2>
                        <p class="mt-0.5 text-xs text-gray-500">Today's booked appointments and status.</p>
                    </div>
                </div>

                <div class="space-y-4 flex-1">
                    @forelse($nextAppointments as $appt)
                        @php
                            $status = strtolower($appt->status);
                            $badgeClass = 'bg-gray-100 text-gray-700';
                            if ($status === 'scheduled') {
                                $badgeClass = 'bg-blue-50 text-blue-700';
                            } elseif ($status === 'waiting') {
                                $badgeClass = 'bg-amber-50 text-amber-700';
                            } elseif ($status === 'ongoing') {
                                $badgeClass = 'bg-emerald-50 text-emerald-700';
                            }
                        @endphp
                        <div
                            class="flex items-center justify-between gap-4 p-4 rounded-xl border border-gray-100 hover:border-blue-100 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="h-12 w-12 overflow-hidden rounded-xl bg-slate-100 ring-1 ring-slate-200 flex shrink-0 items-center justify-center text-sm font-bold text-slate-600">
                                    @if (!empty($appt->profile_picture))
                                        <img src="{{ asset('storage/' . $appt->profile_picture) . '?v=' . urlencode((string) strtotime((string) data_get($appt, 'profile_picture_updated_at'))) }}"
                                            alt="{{ $appt->first_name }} {{ $appt->last_name }} profile picture"
                                            class="h-full w-full object-cover">
                                    @else
                                        {{ strtoupper(substr($appt->first_name ?? 'P', 0, 1) . substr($appt->last_name ?? '', 0, 1)) }}
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-bold text-gray-900 truncate">{{ $appt->last_name }},
                                        {{ $appt->first_name }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-0.5 truncate">{{ $appt->service_name }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-bold text-gray-900">
                                    {{ \Carbon\Carbon::parse($appt->appointment_date)->format('h:i A') }}</div>
                                <span
                                    class="inline-block mt-1 text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $badgeClass }}">{{ $appt->status }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="flex items-center justify-center h-full text-sm text-gray-400 font-medium pb-8">
                            No upcoming appointments today.
                        </div>
                    @endforelse
                </div>
            </section>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
            <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Lobby Flow Snapshot</h2>
                        <p class="mt-0.5 text-xs text-gray-500">Current patient flow inside the clinic.</p>
                    </div>
                    <a href="{{ route('queue') }}" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                        View Full Lobby Flow
                    </a>
                </div>

                <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                    <div class="rounded-xl bg-amber-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-amber-700">Waiting</div>
                        <div class="mt-2 text-3xl font-bold text-amber-800">{{ $waitingPatientsCount ?? 0 }}</div>
                    </div>
                    <div class="rounded-xl bg-cyan-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Arrived</div>
                        <div class="mt-2 text-3xl font-bold text-cyan-800">{{ $arrivedPatientsCount ?? 0 }}</div>
                    </div>
                    <div class="rounded-xl bg-emerald-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Ongoing</div>
                        <div class="mt-2 text-3xl font-bold text-emerald-800">{{ $ongoingPatientsCount ?? 0 }}</div>
                    </div>
                    <div class="rounded-xl bg-blue-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-blue-700">Completed</div>
                        <div class="mt-2 text-3xl font-bold text-blue-800">{{ $completedPatientsCount ?? 0 }}</div>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Appointment Status Today</h2>
                    <p class="mt-0.5 text-xs text-gray-500">Pie or doughnut breakdown for today's statuses.</p>
                </div>
                <div id="statusChartWrap" class="relative min-h-[260px] w-full">
                    <canvas id="dashboardStatusTodayChart"></canvas>
                </div>
                <div id="statusChartEmpty" class="hidden rounded-xl border border-dashed border-gray-200 bg-gray-50 px-4 py-10 text-center text-sm font-medium text-gray-500">
                    No appointments recorded today
                </div>
            </section>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
            <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Recent Activity</h2>
                    <p class="mt-0.5 text-xs text-gray-500">Latest system actions (up to 5).</p>
                </div>

                <div class="max-h-[320px] overflow-auto rounded-xl border border-gray-100">
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
                                    <td class="px-4 py-3 text-gray-700">{{ $activity->description ?: ($activity->event ?? 'Activity updated') }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ \Carbon\Carbon::parse($activity->created_at)->format('M d, Y h:i A') }}</td>
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

            <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Quick Actions</h2>
                    <p class="mt-0.5 text-xs text-gray-500">Run common tasks instantly.</p>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <button type="button" id="addPatientQuickAction"
                        class="flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-3 text-left text-sm font-semibold text-gray-800 hover:border-[#0086DA] hover:text-[#0086DA] transition">
                        <span>+</span>
                        <span>Add Patient</span>
                    </button>
                    <a href="{{ route('appointment') }}"
                        class="flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-800 hover:border-[#0086DA] hover:text-[#0086DA] transition">
                        <span>??</span>
                        <span>Book Appointment</span>
                    </a>
                    <a href="{{ route('queue') }}"
                        class="flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-800 hover:border-[#0086DA] hover:text-[#0086DA] transition">
                        <span>??</span>
                        <span>Register Walk-In Patient</span>
                    </a>
                    <a href="#today-schedule"
                        class="flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-800 hover:border-[#0086DA] hover:text-[#0086DA] transition">
                        <span>??</span>
                        <span>View Today's Schedule</span>
                    </a>
                    @if (auth()->user()?->role === 1)
                        <a href="{{ route('reports.index') }}"
                            class="flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-800 hover:border-[#0086DA] hover:text-[#0086DA] transition sm:col-span-2">
                            <span>??</span>
                            <span>Generate Reports</span>
                        </a>
                    @else
                        <a href="{{ route('appointment') }}"
                            class="flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-800 hover:border-[#0086DA] hover:text-[#0086DA] transition sm:col-span-2">
                            <span>??</span>
                            <span>Open Appointments</span>
                        </a>
                    @endif
                </div>
            </section>
        </div>

        <livewire:patient-form-controller.patient-form-modal />

        <div id="dashboard-chart-data"
            data-status-labels='@json($statusLabels ?? [])'
            data-status-counts='@json($statusCounts ?? [])'
            class="hidden">
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
                        backgroundColor: ['#3b82f6', '#f59e0b', '#06b6d4', '#10b981', '#16a34a', '#ef4444'],
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
