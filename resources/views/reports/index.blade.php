@extends('index')

@section('content')
    <main id="mainContent"
        class="min-h-screen bg-gray-100 p-4 sm:p-6 lg:p-8 ml-0 md:ml-64 mt-14 transition-all duration-300 md:peer-[.collapsed]:ml-16">
        <style>
            .reports-wrap {
                display: grid;
                gap: 1rem;
            }
            .report-card {
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 14px;
                padding: 1rem;
                box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
            }
            .grid-4 {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 1rem;
            }
            .grid-2 {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 1rem;
            }
            .title {
                margin: 0 0 .5rem;
                font-size: 12px;
                font-weight: 700;
                color: #6b7280;
                text-transform: uppercase;
                letter-spacing: .08em;
            }
            .value {
                margin: 0;
                font-size: 2rem;
                line-height: 1.1;
                color: #111827;
                font-weight: 700;
            }
            .sub {
                margin: .45rem 0 0;
                color: #6b7280;
                font-size: .85rem;
            }
            .heading {
                margin: 0;
                font-size: 1.02rem;
                font-weight: 700;
                color: #111827;
            }
            .desc {
                margin: .2rem 0 0;
                color: #6b7280;
                font-size: .85rem;
            }
            .chart-300 {
                height: 300px;
                margin-top: .8rem;
            }
            .chart-340 {
                height: 340px;
                margin-top: .8rem;
            }
            .toolbar {
                display: grid;
                grid-template-columns: 1fr 1fr 1fr auto;
                gap: .6rem;
                align-items: end;
            }
            .input {
                width: 100%;
                border: 1px solid #d1d5db;
                border-radius: 10px;
                padding: .55rem .65rem;
                background: #fff;
                color: #111827;
            }
            .btn {
                border: 1px solid #0f766e;
                border-radius: 10px;
                color: #fff;
                background: #0f766e;
                padding: .56rem .9rem;
                font-weight: 600;
            }
            .table-wrap {
                overflow: auto;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                background: #fff;
            }
            .r-table {
                width: 100%;
                border-collapse: collapse;
                font-size: .86rem;
            }
            .r-table th, .r-table td {
                border-bottom: 1px solid #f1f5f9;
                padding: .65rem .75rem;
                text-align: left;
                white-space: nowrap;
            }
            .r-table th {
                background: #f8fafc;
                color: #334155;
                font-size: .75rem;
                text-transform: uppercase;
                letter-spacing: .06em;
            }
            @media (max-width: 1280px) {
                .grid-4 {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }
            @media (max-width: 768px) {
                .grid-2, .grid-4 {
                    grid-template-columns: 1fr;
                }
                .toolbar {
                    grid-template-columns: 1fr;
                }
                .value {
                    font-size: 1.6rem;
                }
                .chart-300 {
                    height: 250px;
                }
                .chart-340 {
                    height: 280px;
                }
            }
        </style>

        <div class="reports-wrap">
            <div class="report-card">
                <h1 class="heading">Clinic Reports & Analytics</h1>
                <p class="desc">Filter period: {{ $rangeLabel }}</p>
                <form method="GET" action="{{ route('reports.index') }}" class="toolbar" style="margin-top:.8rem;">
                    <div>
                        <label class="title" style="display:block;margin-bottom:.25rem;">Range</label>
                        <select name="range" class="input">
                            <option value="today" @selected($range === 'today')>Today</option>
                            <option value="week" @selected($range === 'week')>This Week</option>
                            <option value="month" @selected($range === 'month')>This Month</option>
                            <option value="year" @selected($range === 'year')>This Year</option>
                            <option value="custom" @selected($range === 'custom')>Custom Date Range</option>
                        </select>
                    </div>
                    <div>
                        <label class="title" style="display:block;margin-bottom:.25rem;">From</label>
                        <input type="date" name="from_date" value="{{ $fromDate }}" class="input">
                    </div>
                    <div>
                        <label class="title" style="display:block;margin-bottom:.25rem;">To</label>
                        <input type="date" name="to_date" value="{{ $toDate }}" class="input">
                    </div>
                    <button class="btn" type="submit">Apply</button>
                </form>
            </div>

            <div class="grid-4">
                <section class="report-card">
                    <p class="title">Total Patients</p>
                    <p class="value">{{ number_format($totalPatients) }}</p>
                    <p class="sub">Current patient base</p>
                </section>
                <section class="report-card">
                    <p class="title">Total Appointments</p>
                    <p class="value">{{ number_format($totalAppointments) }}</p>
                    <p class="sub">Within selected range</p>
                </section>
                <section class="report-card">
                    <p class="title">Completed Appointments</p>
                    <p class="value" style="color:#059669;">{{ number_format($completedCount) }}</p>
                    <p class="sub">Status = Completed</p>
                </section>
                <section class="report-card">
                    <p class="title">Cancelled Appointments</p>
                    <p class="value" style="color:#dc2626;">{{ number_format($cancelledCount) }}</p>
                    <p class="sub">Status = Cancelled</p>
                </section>
            </div>

            <div class="grid-2">
                <section class="report-card">
                    <h3 class="heading">Appointment Status Distribution</h3>
                    <p class="desc">Scheduled, Arrived, Ongoing, Completed, Cancelled</p>
                    <div class="chart-300"><canvas id="statusPieChart"></canvas></div>
                </section>
                <section class="report-card">
                    <h3 class="heading">Monthly Patient Registration Trend</h3>
                    <p class="desc">New patient registrations per month</p>
                    <div class="chart-300"><canvas id="patientRegLineChart"></canvas></div>
                </section>
            </div>

            <div class="grid-2">
                <section class="report-card">
                    <h3 class="heading">Appointment Trend</h3>
                    <p class="desc">Clinic volume by date buckets in selected range</p>
                    <div class="chart-300"><canvas id="appointmentTrendBarChart"></canvas></div>
                </section>
                <section class="report-card">
                    <h3 class="heading">Most Requested Services</h3>
                    <p class="desc">Based on appointment count</p>
                    <div class="chart-300"><canvas id="servicesBarChart"></canvas></div>
                </section>
            </div>

            <div class="grid-2">
                <section class="report-card">
                    <h3 class="heading">Walk-In vs Scheduled</h3>
                    <p class="desc">Behavior based on activity logs and appointments</p>
                    <div class="chart-300"><canvas id="walkinPieChart"></canvas></div>
                </section>
                <section class="report-card">
                    <h3 class="heading">Patient Gender Distribution</h3>
                    <p class="desc">Overall patient demographics</p>
                    <div class="chart-300"><canvas id="genderPieChart"></canvas></div>
                </section>
            </div>

            <section class="report-card">
                <h3 class="heading">Patient Registration Report</h3>
                <form method="GET" action="{{ route('reports.index') }}" class="toolbar" style="margin:.8rem 0;">
                    <input type="hidden" name="range" value="{{ $range }}">
                    <input type="hidden" name="from_date" value="{{ $fromDate }}">
                    <input type="hidden" name="to_date" value="{{ $toDate }}">
                    <div>
                        <label class="title" style="display:block;margin-bottom:.25rem;">From</label>
                        <input type="date" name="patient_from" value="{{ $patientRegFrom }}" class="input">
                    </div>
                    <div>
                        <label class="title" style="display:block;margin-bottom:.25rem;">To</label>
                        <input type="date" name="patient_to" value="{{ $patientRegTo }}" class="input">
                    </div>
                    <div>
                        <label class="title" style="display:block;margin-bottom:.25rem;">Gender</label>
                        <select name="patient_gender" class="input">
                            <option value="">All</option>
                            <option value="Male" @selected($patientGender === 'Male')>Male</option>
                            <option value="Female" @selected($patientGender === 'Female')>Female</option>
                            <option value="Other" @selected($patientGender === 'Other')>Other</option>
                        </select>
                    </div>
                    <button class="btn" type="submit">Filter</button>
                </form>
                <div class="table-wrap">
                    <table class="r-table">
                        <thead>
                            <tr>
                                <th>Patient ID</th>
                                <th>Full Name</th>
                                <th>Gender</th>
                                <th>Birth Date</th>
                                <th>Mobile Number</th>
                                <th>Date Registered</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($patientRows as $row)
                                <tr>
                                    <td>{{ $row->id }}</td>
                                    <td>{{ trim(($row->last_name ?? '') . ', ' . ($row->first_name ?? '') . ' ' . ($row->middle_name ?? '')) }}</td>
                                    <td>{{ $row->gender ?? 'Unspecified' }}</td>
                                    <td>{{ $row->birth_date ? \Carbon\Carbon::parse($row->birth_date)->format('M d, Y') : '-' }}</td>
                                    <td>{{ $row->mobile_number ?? '-' }}</td>
                                    <td>{{ $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('M d, Y h:i A') : '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6">No records found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="report-card">
                <h3 class="heading">Appointment Report</h3>
                <form method="GET" action="{{ route('reports.index') }}" class="toolbar" style="margin:.8rem 0;">
                    <input type="hidden" name="range" value="{{ $range }}">
                    <input type="hidden" name="from_date" value="{{ $fromDate }}">
                    <input type="hidden" name="to_date" value="{{ $toDate }}">
                    <div>
                        <label class="title" style="display:block;margin-bottom:.25rem;">From</label>
                        <input type="date" name="appointment_from" value="{{ $appointmentFrom }}" class="input">
                    </div>
                    <div>
                        <label class="title" style="display:block;margin-bottom:.25rem;">To</label>
                        <input type="date" name="appointment_to" value="{{ $appointmentTo }}" class="input">
                    </div>
                    <div>
                        <label class="title" style="display:block;margin-bottom:.25rem;">Status</label>
                        <select name="appointment_status" class="input">
                            <option value="">All</option>
                            @foreach (['Pending','Scheduled','Arrived','Ongoing','Completed','Cancelled','Waiting'] as $status)
                                <option value="{{ $status }}" @selected($appointmentStatus === $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="title" style="display:block;margin-bottom:.25rem;">Service</label>
                        <select name="appointment_service" class="input">
                            <option value="">All</option>
                            @foreach ($serviceOptions as $id => $name)
                                <option value="{{ $id }}" @selected((string) $appointmentService === (string) $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn" type="submit">Filter</button>
                </form>
                <div class="table-wrap">
                    <table class="r-table">
                        <thead>
                            <tr>
                                <th>Appointment ID</th>
                                <th>Patient Name</th>
                                <th>Service</th>
                                <th>Appointment Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($appointmentRows as $row)
                                <tr>
                                    <td>{{ $row->id }}</td>
                                    <td>{{ trim(($row->last_name ?? '') . ', ' . ($row->first_name ?? '') . ' ' . ($row->middle_name ?? '')) }}</td>
                                    <td>{{ $row->service_name ?? 'N/A' }}</td>
                                    <td>{{ $row->appointment_date ? \Carbon\Carbon::parse($row->appointment_date)->format('M d, Y h:i A') : '-' }}</td>
                                    <td>{{ $row->status }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5">No records found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="grid-2">
                <section class="report-card">
                    <h3 class="heading">Completed Appointment Report</h3>
                    <div class="table-wrap" style="margin-top:.8rem;">
                        <table class="r-table">
                            <thead>
                                <tr>
                                    <th>Appointment ID</th>
                                    <th>Patient Name</th>
                                    <th>Service</th>
                                    <th>Appointment Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($completedRows as $row)
                                    <tr>
                                        <td>{{ $row->id }}</td>
                                        <td>{{ trim(($row->last_name ?? '') . ', ' . ($row->first_name ?? '') . ' ' . ($row->middle_name ?? '')) }}</td>
                                        <td>{{ $row->service_name ?? 'N/A' }}</td>
                                        <td>{{ $row->appointment_date ? \Carbon\Carbon::parse($row->appointment_date)->format('M d, Y h:i A') : '-' }}</td>
                                        <td>{{ $row->status }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5">No records found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="report-card">
                    <h3 class="heading">Cancelled Appointment Report</h3>
                    <div class="table-wrap" style="margin-top:.8rem;">
                        <table class="r-table">
                            <thead>
                                <tr>
                                    <th>Appointment ID</th>
                                    <th>Patient Name</th>
                                    <th>Service</th>
                                    <th>Appointment Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($cancelledRows as $row)
                                    <tr>
                                        <td>{{ $row->id }}</td>
                                        <td>{{ trim(($row->last_name ?? '') . ', ' . ($row->first_name ?? '') . ' ' . ($row->middle_name ?? '')) }}</td>
                                        <td>{{ $row->service_name ?? 'N/A' }}</td>
                                        <td>{{ $row->appointment_date ? \Carbon\Carbon::parse($row->appointment_date)->format('M d, Y h:i A') : '-' }}</td>
                                        <td>{{ $row->status }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5">No records found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (() => {
            if (typeof Chart === 'undefined') return;
            const isMobile = window.innerWidth < 768;

            const charts = {};
            const build = (id, config) => {
                const ctx = document.getElementById(id);
                if (!ctx) return;
                charts[id] = new Chart(ctx.getContext('2d'), config);
            };

            build('statusPieChart', {
                type: 'pie',
                data: {
                    labels: @json($statusLabels),
                    datasets: [{
                        data: @json($statusCounts),
                        backgroundColor: ['#0284c7', '#16a34a', '#f59e0b', '#0ea5e9', '#ef4444'],
                    }],
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } },
            });

            build('patientRegLineChart', {
                type: 'line',
                data: {
                    labels: @json($patientRegMonths),
                    datasets: [{
                        label: 'Patients',
                        data: @json($patientRegCounts),
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, .12)',
                        fill: true,
                        tension: .3,
                    }],
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } },
            });

            build('appointmentTrendBarChart', {
                type: 'bar',
                data: {
                    labels: @json($dates),
                    datasets: [{
                        label: 'Appointments',
                        data: @json($totals),
                        backgroundColor: '#0891b2',
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { ticks: { maxTicksLimit: isMobile ? 6 : 14 } },
                        y: { beginAtZero: true, ticks: { precision: 0 } },
                    },
                },
            });

            build('servicesBarChart', {
                type: 'bar',
                data: {
                    labels: @json($serviceNames),
                    datasets: [{
                        label: 'Count',
                        data: @json($serviceCounts),
                        backgroundColor: '#d97706',
                    }],
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                },
            });

            build('walkinPieChart', {
                type: 'pie',
                data: {
                    labels: ['Walk-In', 'Scheduled'],
                    datasets: [{
                        data: [{{ $walkInCount }}, {{ $scheduledCount }}],
                        backgroundColor: ['#f97316', '#0ea5e9'],
                    }],
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } },
            });

            build('genderPieChart', {
                type: 'pie',
                data: {
                    labels: @json($genderLabels),
                    datasets: [{
                        data: @json($genderCounts),
                        backgroundColor: ['#3b82f6', '#ec4899', '#a855f7', '#94a3b8'],
                    }],
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } },
            });
        })();
    </script>
@endsection
