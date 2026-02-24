@extends('index')

@section('content')
    <main id="mainContent"
        class="min-h-screen bg-[#f3f4f6] p-6 lg:p-8 ml-64 mt-14 transition-all duration-300 peer-[.collapsed]:ml-16">

        <div class="mb-8">
            <h1 class="text-3xl lg:text-4xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-sm text-gray-500 mt-1">Operational overview for today and recent trends.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            <section
                class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 min-h-[170px] flex flex-col justify-between hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-gray-600">Today's Appointments</p>
                    <span class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
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
                    <div class="text-xs text-gray-500 mt-2 font-medium">
                        <span class="text-emerald-600">{{ $todayCompletedCount ?? 0 }} completed</span> &middot;
                        <span class="text-rose-500">{{ $todayCancelledCount ?? 0 }} cancelled</span> &middot;
                        <span>{{ $todayUpcomingCount ?? 0 }} upcoming</span>
                    </div>
                </div>
            </section>

            <section
                class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 min-h-[170px] flex flex-col justify-between hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-gray-600">Profit</p>
                    <div class="flex items-center gap-2">
                        <select id="profitRangeSelect"
                            class="text-xs font-semibold text-gray-600 border border-gray-200 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-blue-100 outline-none">
                            <option value="today" selected>Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                        </select>
                    </div>
                </div>
                <div>
                    <div id="profitValue" class="text-4xl font-bold text-gray-900">PHP
                        {{ number_format($todayProfit ?? 0, 2) }}</div>
                    <div id="profitChange" class="text-xs mt-2 font-semibold text-gray-400">&mdash;</div>
                </div>
            </section>

            <section
                class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 min-h-[170px] flex flex-col justify-between hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-gray-600">Total Patients</p>
                    <span class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                        </svg>
                    </span>
                </div>
                <div>
                    <div class="text-4xl font-bold text-gray-900">{{ $totalPatients ?? 0 }}</div>
                    <div class="text-xs text-gray-500 mt-2 font-medium">All registered patients</div>
                </div>
            </section>

            <section
                class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 min-h-[170px] flex flex-col justify-between hover:shadow-md transition-shadow">
                @php
                    $cancelledLast30 = $cancelledLast30 ?? 0;
                    $bookedLast30 = $bookedLast30 ?? 0;
                    $cancellationRate = $bookedLast30 > 0 ? ($cancelledLast30 / $bookedLast30) * 100 : 0;

                    $cancellationClass = 'text-gray-500';
                    if ($cancellationRate < 10) {
                        $cancellationClass = 'text-emerald-600';
                    } elseif ($cancellationRate >= 15) {
                        $cancellationClass = 'text-rose-600';
                    }
                @endphp
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-gray-600">30-Day Cancellation Rate</p>
                    <span class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2v6" />
                            <path d="M12 16v6" />
                            <path d="M6 12H2" />
                            <path d="M22 12h-4" />
                            <path d="M5 5l3 3" />
                            <path d="M19 19l-3-3" />
                            <path d="M19 5l-3 3" />
                            <path d="M5 19l3-3" />
                        </svg>
                    </span>
                </div>
                <div>
                    <div class="text-4xl font-bold {{ $cancellationClass }}">
                        {{ number_format($cancellationRate, 0) }}%
                    </div>
                    <div class="text-xs text-gray-500 mt-2 font-medium">
                        {{ $cancelledLast30 }} cancelled out of {{ $bookedLast30 }} booked
                    </div>
                </div>
            </section>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-6">
            <div>
                @livewire('pending-approvals-widget')
            </div>

            <section class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 flex flex-col">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Next Upcoming</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Today's queue</p>
                    </div>
                    <span
                        class="bg-blue-50 text-blue-600 text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-md">Live</span>
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
                            class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:border-blue-100 transition-colors">
                            <div>
                                <div class="text-sm font-bold text-gray-900">{{ $appt->last_name }},
                                    {{ $appt->first_name }}
                                </div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $appt->service_name }}</div>
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

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-6">
            <section class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 flex flex-col">
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Appointments Trend ({{ $rangeLabel ?? 'Last 15 Days' }})
                    </h2>
                </div>
                <div class="relative flex-1 w-full min-h-[250px]">
                    <canvas id="dashboardAppointmentsChart"></canvas>
                </div>
            </section>

            <section class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 flex flex-col">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Patients Statistics</h2>
                        <p id="patientStatsSubtitle" class="text-xs text-gray-500 mt-0.5">{{ $patientStatsLabel ?? 'Monthly new patient vs returning patient' }}</p>
                    </div>
                    <select id="patientStatsRangeSelect"
                        class="text-xs font-semibold text-gray-600 border border-gray-200 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-blue-100 outline-none">
                        <option value="weekly" {{ ($patientStatsRange ?? 'monthly') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ ($patientStatsRange ?? 'monthly') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                    </select>
                </div>
                <div class="flex items-center justify-end mb-4">
                    <div class="flex items-center gap-4 text-xs font-semibold text-gray-600">
                        <span class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-blue-600"></span>
                            New Patients
                        </span>
                        <span class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-blue-200"></span>
                            Returning Patients
                        </span>
                    </div>
                </div>
                <div class="relative flex-1 w-full min-h-[250px]">
                    <canvas id="dashboardPatientsStatsChart"></canvas>
                </div>
            </section>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mt-6">
            <section class="xl:col-span-2 bg-white border border-gray-100 rounded-2xl shadow-sm p-6 flex flex-col">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Top Procedures</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Last 30 days based on completed appointments</p>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-500 font-medium">Total procedures</div>
                        <div class="text-xl font-extrabold text-gray-900">{{ $topProceduresTotal ?? 0 }}</div>
                    </div>
                </div>
                <div class="relative flex-1 w-full min-h-[250px]">
                    <canvas id="dashboardTopProceduresChart"></canvas>
                </div>
            </section>

            <section class="xl:col-span-1 bg-white border border-gray-100 rounded-2xl shadow-sm p-6 h-full">
                <div class="mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Notes & Reminders</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Quick internal notes</p>
                </div>
                <div class="mt-4">
                    @livewire('notes')
                </div>
            </section>
        </div>
        <livewire:patient-form-controller.patient-form-modal />

        <div id="dashboard-chart-data" data-labels="{{ json_encode($trendDates) }}"
            data-appointments="{{ json_encode($trendAppointments) }}"
            data-patient-stats-dates="{{ json_encode($patientStatsDates ?? []) }}"
            data-new-patient-counts="{{ json_encode($newPatientCounts ?? []) }}"
            data-returning-patient-counts="{{ json_encode($returningPatientCounts ?? []) }}"
            data-patient-stats-endpoint="{{ route('dashboard.patient-stats') }}"
            data-top-procedure-names="{{ json_encode($topProcedureNames ?? []) }}"
            data-top-procedure-counts="{{ json_encode($topProcedureCounts ?? []) }}"
            data-today-profit="{{ $todayProfit }}" data-yesterday-profit="{{ $yesterdayProfit }}"
            data-today-profit-pct="{{ $todayProfitPct }}" data-week-profit="{{ $weekProfit }}"
            data-week-profit-pct="{{ $weekProfitPct }}" data-month-profit="{{ $monthProfit }}"
            data-month-profit-pct="{{ $monthProfitPct }}" class="hidden">
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
            const patientStatsDates = JSON.parse(dataEl.dataset.patientStatsDates || '[]');
            const newPatientCounts = JSON.parse(dataEl.dataset.newPatientCounts || '[]');
            const returningPatientCounts = JSON.parse(dataEl.dataset.returningPatientCounts || '[]');
            const patientStatsEndpoint = dataEl.dataset.patientStatsEndpoint || '';
            const topProcedureNames = JSON.parse(dataEl.dataset.topProcedureNames || '[]');
            const topProcedureCounts = JSON.parse(dataEl.dataset.topProcedureCounts || '[]');
            const todayProfit = Number(dataEl.dataset.todayProfit || 0);
            const todayProfitPct = dataEl.dataset.todayProfitPct;
            const weekProfit = Number(dataEl.dataset.weekProfit || 0);
            const weekProfitPct = dataEl.dataset.weekProfitPct;
            const monthProfit = Number(dataEl.dataset.monthProfit || 0);
            const monthProfitPct = dataEl.dataset.monthProfitPct;

            Chart.defaults.font.family =
                "ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif";
            Chart.defaults.color = '#64748b';

            // Position tooltip outward from the hovered doughnut slice direction.
            Chart.Tooltip.positioners.patientStatsRadial = function(elements) {
                const first = elements?.[0];
                if (!first) {
                    return false;
                }

                const chart = this.chart;
                const {
                    left,
                    right,
                    top,
                    bottom
                } = chart.chartArea;
                const centerX = (left + right) / 2;
                const centerY = (top + bottom) / 2;
                const anchor = first.element.tooltipPosition();

                const dx = anchor.x - centerX;
                const dy = anchor.y - centerY;
                const length = Math.hypot(dx, dy) || 1;
                const offset = 36;

                let x = anchor.x + (dx / length) * offset;
                let y = anchor.y + (dy / length) * offset;

                x = Math.max(left + 12, Math.min(right - 12, x));
                y = Math.max(top + 12, Math.min(bottom - 12, y));

                return {
                    x,
                    y,
                    xAlign: dx >= 0 ? 'left' : 'right',
                    yAlign: dy >= 0 ? 'top' : 'bottom'
                };
            };

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
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: false,
                                suggestedMin: 1,
                                suggestedMax: 7,
                                grid: {
                                    color: '#f1f5f9',
                                    borderDash: [4, 4]
                                },
                                ticks: {
                                    stepSize: 1,
                                    precision: 0
                                },
                                border: {
                                    display: false
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                border: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            const patientStatsCtx = document.getElementById('dashboardPatientsStatsChart');
            const patientStatsSubtitle = document.getElementById('patientStatsSubtitle');
            let patientStatsChart = null;
            if (patientStatsCtx) {
                const patientStatsCenterTextPlugin = {
                    id: 'patientStatsCenterText',
                    afterDraw(chart) {
                        const { ctx, chartArea: { left, right, top, bottom } } = chart;
                        const centerX = (left + right) / 2;
                        const centerY = (top + bottom) / 2;
                        const totalNewPatients = Number(chart.data.datasets?.[0]?.data?.[0] || 0);

                        ctx.save();
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';

                        ctx.fillStyle = '#0f172a';
                        ctx.font = '700 24px ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont';
                        ctx.fillText(totalNewPatients.toString(), centerX, centerY - 8);

                        ctx.fillStyle = '#64748b';
                        ctx.font = '600 11px ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont';
                        ctx.fillText('Total New Patient Acquire', centerX, centerY + 12);

                        ctx.restore();
                    }
                };

                const renderPatientStats = (newCounts, returningCounts) => {
                    const totalNewPatients = newCounts.reduce((sum, value) => sum + Number(value || 0), 0);
                    const totalReturningPatients = returningCounts.reduce((sum, value) => sum + Number(value || 0), 0);
                    const totals = [totalNewPatients, totalReturningPatients];

                    if (patientStatsChart) {
                        patientStatsChart.data.datasets[0].data = totals;
                        patientStatsChart.update();
                        return;
                    }

                    patientStatsChart = new Chart(patientStatsCtx.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: ['New Patients', 'Returning Patients'],
                            datasets: [{
                                data: totals,
                                backgroundColor: ['#2563eb', '#bfdbfe'],
                                borderColor: ['#2563eb', '#bfdbfe'],
                                borderWidth: 1,
                                hoverOffset: 8
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '58%',
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    position: 'patientStatsRadial',
                                    backgroundColor: '#ffffff',
                                    titleColor: '#0f172a',
                                    bodyColor: '#0f172a',
                                    borderColor: '#e2e8f0',
                                    borderWidth: 1,
                                    caretSize: 8,
                                    caretPadding: 8,
                                    padding: 10,
                                    usePointStyle: true,
                                    callbacks: {
                                        label: function(context) {
                                            const value = Number(context.parsed ?? 0);
                                            const values = (context.dataset.data || []).map((item) => Number(item || 0));
                                            const total = values.reduce((sum, item) => sum + item, 0);
                                            const pct = total > 0 ? Math.round((value / total) * 100) : 0;
                                            return `${context.label}: ${value} (${pct}%)`;
                                        }
                                    }
                                }
                            }
                        },
                        plugins: [patientStatsCenterTextPlugin]
                    });
                };

                renderPatientStats(newPatientCounts, returningPatientCounts);
            }

            const topCtx = document.getElementById('dashboardTopProceduresChart');
            if (topCtx) {
                const labelPlugin = {
                    id: 'valueLabels',
                    afterDatasetsDraw(chart) {
                        const {
                            ctx
                        } = chart;
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
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: false
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                grid: {
                                    display: false
                                },
                                border: {
                                    display: false
                                },
                                ticks: {
                                    display: false
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                },
                                border: {
                                    display: false
                                },
                                ticks: {
                                    color: '#475569',
                                    font: {
                                        weight: '600',
                                        size: 12
                                    }
                                }
                            }
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
                    profitValue.textContent =
                        `PHP ${value.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                }

                if (profitChange) {
                    let num = Number(pct);
                    const hasPct = !(pct === null || pct === undefined || pct === '');
                    if (!hasPct) {
                        num = 0;
                    }

                    const isUp = num >= 0;
                    const trendIcon = isUp ?
                        '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-up-icon lucide-trending-up"><path d="M16 7h6v6"/><path d="m22 7-8.5 8.5-5-5L2 17"/></svg>' :
                        '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-down-icon lucide-trending-down"><path d="M16 17h6v-6"/><path d="m22 17-8.5-8.5-5 5L2 7"/></svg>';

                    const pctText = hasPct ? `${Math.abs(num)}%` : '0%';
                    profitChange.innerHTML =
                        `${trendIcon}<span>${isUp ? 'Profit up' : 'Profit down'} ${pctText} (${compareLabel})</span>`;
                    profitChange.className =
                        `text-xs mt-2 font-semibold flex items-center gap-2 ${isUp ? 'text-emerald-600' : 'text-rose-600'}`;
                }
            }

            if (profitRangeSelect) {
                profitRangeSelect.addEventListener('change', (e) => setProfitDisplay(e.target.value));
                setProfitDisplay('today');
            }

            const patientStatsRangeSelect = document.getElementById('patientStatsRangeSelect');
            if (patientStatsRangeSelect && patientStatsEndpoint) {
                patientStatsRangeSelect.addEventListener('change', async function(e) {
                    const selectedRange = e.target.value;
                    patientStatsRangeSelect.disabled = true;

                    try {
                        const url = new URL(patientStatsEndpoint, window.location.origin);
                        url.searchParams.set('patient_stats_range', selectedRange);

                        const response = await fetch(url.toString(), {
                            headers: {
                                Accept: 'application/json'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Failed to load patient statistics');
                        }

                        const payload = await response.json();
                        if (patientStatsSubtitle && payload.patientStatsLabel) {
                            patientStatsSubtitle.textContent = payload.patientStatsLabel;
                        }
                        if (patientStatsChart) {
                            const freshNew = Array.isArray(payload.newPatientCounts) ? payload.newPatientCounts : [];
                            const freshReturning = Array.isArray(payload.returningPatientCounts) ? payload.returningPatientCounts : [];
                            const totalNewPatients = freshNew.reduce((sum, value) => sum + Number(value || 0), 0);
                            const totalReturningPatients = freshReturning.reduce((sum, value) => sum + Number(value || 0), 0);
                            patientStatsChart.data.datasets[0].data = [totalNewPatients, totalReturningPatients];
                            patientStatsChart.update();
                        }
                    } catch (error) {
                        console.error(error);
                    } finally {
                        patientStatsRangeSelect.disabled = false;
                    }
                });
            }
        });
    </script>
@endpush
