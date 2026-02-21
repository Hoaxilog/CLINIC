@extends('index')

@section('content')
    <main id="mainContent"
        class="min-h-screen bg-white p-6 lg:p-8 ml-64 mt-14 transition-all duration-300 peer-[.collapsed]:ml-16">
        <div class="mb-6">
            <h1 class="text-3xl lg:text-4xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-sm text-gray-500 mt-1">Operational overview for today and recent trends.</p>
        </div>

        <!-- TOP SECTION – TODAY FOCUS -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            <section
                class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 min-h-[170px] flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-gray-600">Today's Appointments</p>
                    <span class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M8 2v4" />
                            <path d="M16 2v4" />
                            <rect width="18" height="18" x="3" y="4" rx="2" />
                            <path d="M3 10h18" />
                        </svg>
                    </span>
                </div>
                <div>
                    <div class="text-4xl font-bold text-gray-900">{{ $todayAppointmentsCount ?? 0 }}</div>
                    <div class="text-xs text-gray-500 mt-1">
                        {{ $todayCompletedCount ?? 0 }} completed · {{ $todayCancelledCount ?? 0 }} cancelled ·
                        {{ $todayUpcomingCount ?? 0 }} upcoming
                    </div>
                </div>
            </section>

            <section
                class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 min-h-[170px] flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-gray-600">Profit</p>
                    <div class="flex items-center gap-2">
                        <select id="profitRangeSelect"
                            class="text-xs font-semibold text-gray-600 border border-gray-200 rounded-lg px-2 py-1">
                            <option value="today" selected>Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                        </select>
                    </div>
                </div>
                <div>
                    <div id="profitValue" class="text-4xl font-bold text-gray-900">PHP
                        {{ number_format($todayProfit ?? 0, 2) }}</div>
                    <div id="profitChange" class="text-xs mt-2 font-semibold text-gray-400">—</div>
                </div>
            </section>

            <section
                class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 min-h-[170px] flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-gray-600">Total Patients</p>
                    <span class="w-9 h-9 rounded-full bg-green-50 text-green-600 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                        </svg>
                    </span>
                </div>
                <div>
                    <div class="text-4xl font-bold text-gray-900">{{ $totalPatients ?? 0 }}</div>
                    <div class="text-xs text-gray-500 mt-1">All registered patients</div>
                </div>
            </section>

            <section
                class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 min-h-[170px] flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-gray-600">Pending Approvals</p>
                    <span class="w-9 h-9 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 8v4" />
                            <path d="M12 16h.01" />
                            <path
                                d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                        </svg>
                    </span>
                </div>
                <div class="text-4xl font-bold text-gray-900">{{ $pendingApprovalsCount ?? 0 }}</div>
                <div class="text-xs text-gray-500">Requests waiting for approval</div>
            </section>
        </div>

        <!-- SECOND SECTION – OPERATIONAL VIEW -->
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mt-8">
            <section class="lg:col-span-3 bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Appointments Trend ({{ $rangeLabel }})</h2>
                        <p class="text-xs text-gray-500 mt-1">Smooth daily trend</p>
                    </div>
                </div>
                <div class="relative h-48 w-full">
                    <canvas id="dashboardAppointmentsChart"></canvas>
                </div>
            </section>

            <section class="lg:col-span-2 bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Next 3 Upcoming</h2>
                    <span class="text-xs text-gray-500">Today</span>
                </div>
                <div class="space-y-3">
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
                        <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100">
                            <div>
                                <div class="text-sm font-semibold text-gray-900">{{ $appt->last_name }},
                                    {{ $appt->first_name }}</div>
                                <div class="text-xs text-gray-500">{{ $appt->service_name }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ \Carbon\Carbon::parse($appt->appointment_date)->format('h:i A') }}</div>
                                <span
                                    class="text-[10px] font-semibold px-2 py-1 rounded-full {{ $badgeClass }}">{{ $appt->status }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500">No upcoming appointments today.</div>
                    @endforelse
                </div>
            </section>
        </div>

        <!-- THIRD SECTION – TOP PROCEDURES -->
        <div class="grid grid-cols-1 gap-6 mt-8">
            <section class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Top Procedures (Last 30 Days)</h2>
                        <p class="text-xs text-gray-500 mt-1">Based on completed appointments only.</p>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-500">Total procedures</div>
                        <div class="text-lg font-bold text-gray-900">{{ $topProceduresTotal ?? 0 }}</div>
                    </div>
                </div>
                <div class="relative h-44 w-full">
                    <canvas id="dashboardTopProceduresChart"></canvas>
                </div>
            </section>
        </div>

        <!-- FOURTH SECTION – DAILY STATUS INSIGHT -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
            <section class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Today's Status Breakdown</h2>
                    <span class="text-xs text-gray-500">Appointments</span>
                </div>
                <div class="relative h-52 flex items-center justify-center">
                    <canvas id="dashboardStatusChart"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-3xl font-bold text-gray-900">{{ $todayAppointmentsCount ?? 0 }}</span>
                        <span class="text-[10px] uppercase tracking-widest text-gray-400">Total Today</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3 mt-4" id="dashboardStatusLegend"></div>
            </section>
        </div>

        <!-- FINAL SECTION – NOTES -->
        <div class="mt-8">
            <details class="bg-white border border-gray-100 rounded-2xl shadow-sm p-4">
                <summary class="cursor-pointer text-sm font-semibold text-gray-700">Notes / Reminders</summary>
                <div class="mt-4">
                    @livewire('notes')
                </div>
            </details>
        </div>

        <livewire:patient-form-controller.patient-form-modal />

        <div id="dashboard-chart-data" data-labels="{{ json_encode($trendDates) }}"
            data-appointments="{{ json_encode($trendAppointments) }}"
            data-status-labels="{{ json_encode($statusLabels) }}" data-status-counts="{{ json_encode($statusCounts) }}"
            data-top-procedure-names="{{ json_encode($topProcedureNames ?? []) }}"
            data-top-procedure-counts="{{ json_encode($topProcedureCounts ?? []) }}"
            data-today-profit="{{ $todayProfit }}" data-yesterday-profit="{{ $yesterdayProfit }}"
            data-today-profit-pct="{{ $todayProfitPct }}"
            data-week-profit="{{ $weekProfit }}" data-week-profit-pct="{{ $weekProfitPct }}"
            data-month-profit="{{ $monthProfit }}" data-month-profit-pct="{{ $monthProfitPct }}" class="hidden">
        </div>
    </main>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dataEl = document.getElementById('dashboard-chart-data');
        if (!dataEl) return;

        const labels = JSON.parse(dataEl.dataset.labels || '[]');
        const appointments = JSON.parse(dataEl.dataset.appointments || '[]');
        const statusLabels = JSON.parse(dataEl.dataset.statusLabels || '[]');
        const statusCounts = JSON.parse(dataEl.dataset.statusCounts || '[]');
        const topProcedureNames = JSON.parse(dataEl.dataset.topProcedureNames || '[]');
        const topProcedureCounts = JSON.parse(dataEl.dataset.topProcedureCounts || '[]');
        const todayProfit = Number(dataEl.dataset.todayProfit || 0);
        const todayProfitPct = dataEl.dataset.todayProfitPct;
        const weekProfit = Number(dataEl.dataset.weekProfit || 0);
        const weekProfitPct = dataEl.dataset.weekProfitPct;
        const monthProfit = Number(dataEl.dataset.monthProfit || 0);
        const monthProfitPct = dataEl.dataset.monthProfitPct;

        Chart.defaults.font.family = "ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif";
        Chart.defaults.color = '#64748b';

        const apptCtx = document.getElementById('dashboardAppointmentsChart');
        if (apptCtx) {
            new Chart(apptCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Appointments',
                        data: appointments,
                        borderColor: '#2f80ed',
                        backgroundColor: 'rgba(47, 128, 237, 0.12)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#2f80ed'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f1f5f9', borderDash: [4, 4] }, border: { display: false } },
                        x: { grid: { display: false }, border: { display: false } }
                    }
                }
            });
        }

        const statusCtx = document.getElementById('dashboardStatusChart');
        if (statusCtx) {
            const statusColors = ['#3b82f6', '#f59e0b', '#8b5cf6', '#10b981', '#ef4444'];
            new Chart(statusCtx.getContext('2d'), {
                type: 'doughnut',
                data: { labels: statusLabels, datasets: [{ data: statusCounts, backgroundColor: statusColors, borderWidth: 0, hoverOffset: 6, cutout: '75%' }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
            });

            const legend = document.getElementById('dashboardStatusLegend');
            if (legend) {
                legend.innerHTML = '';
                statusLabels.forEach((label, i) => {
                    const color = statusColors[i % statusColors.length];
                    const count = statusCounts[i] || 0;
                    legend.innerHTML += `<div class="flex items-center justify-between p-2.5 bg-gray-50 rounded-lg"><div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full" style="background-color: ${color}"></span><span class="text-xs font-semibold text-gray-600">${label}</span></div><span class="text-sm font-bold text-gray-900">${count}</span></div>`;
                });
            }
        }

        const topCtx = document.getElementById('dashboardTopProceduresChart');
        if (topCtx) {
            const labelPlugin = {
                id: 'valueLabels',
                afterDatasetsDraw(chart) {
                    const { ctx } = chart;
                    ctx.save();
                    ctx.font = '12px ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont';
                    ctx.fillStyle = '#334155';
                    ctx.textBaseline = 'middle';
                    chart.getDatasetMeta(0).data.forEach((bar, i) => {
                        const value = topProcedureCounts[i] ?? 0;
                        ctx.fillText(value, bar.x + 8, bar.y);
                    });
                    ctx.restore();
                }
            };

            new Chart(topCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: topProcedureNames,
                    datasets: [{
                        data: topProcedureCounts,
                        backgroundColor: '#5bbad5',
                        borderRadius: 10,
                        barThickness: 18,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { enabled: false } },
                    scales: {
                        x: { beginAtZero: true, grid: { display: false }, border: { display: false }, ticks: { display: false } },
                        y: { grid: { display: false }, border: { display: false }, ticks: { color: '#475569', font: { weight: '600', size: 12 } } }
                    }
                },
                plugins: [labelPlugin]
            });
        }

        const profitRangeSelect = document.getElementById('profitRangeSelect');
        const profitValue = document.getElementById('profitValue');
        const profitChange = document.getElementById('profitChange');

        function setProfitDisplay(mode) {
            let value = todayProfit;
            let pct = todayProfitPct;
            let compareLabel = 'vs yesterday';

            if (mode === 'week') {
                value = weekProfit;
                pct = weekProfitPct;
                compareLabel = 'vs last week';
            } else if (mode === 'month') {
                value = monthProfit;
                pct = monthProfitPct;
                compareLabel = 'vs last month';
            }

            if (profitValue) {
                profitValue.textContent = `PHP ${value.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            }

            if (profitChange) {
                let num = Number(pct);
                const hasPct = !(pct === null || pct === undefined || pct === '');
                if (!hasPct) {
                    num = 0;
                }

                const isUp = num >= 0;
                const trendIcon = isUp
                    ? '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-up-icon lucide-trending-up"><path d="M16 7h6v6"/><path d="m22 7-8.5 8.5-5-5L2 17"/></svg>'
                    : '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-down-icon lucide-trending-down"><path d="M16 17h6v-6"/><path d="m22 17-8.5-8.5-5 5L2 7"/></svg>';

                const pctText = hasPct ? `${Math.abs(num)}%` : '0%';
                profitChange.innerHTML = `${trendIcon}<span>${isUp ? 'Profit up' : 'Profit down'} ${pctText} (${compareLabel})</span>`;
                profitChange.className = `text-xs mt-2 font-semibold flex items-center gap-2 ${isUp ? 'text-emerald-600' : 'text-rose-600'}`;
            }
        }

        if (profitRangeSelect) {
            profitRangeSelect.addEventListener('change', (e) => setProfitDisplay(e.target.value));
            setProfitDisplay('today');
        }
    });
</script>
@endpush

