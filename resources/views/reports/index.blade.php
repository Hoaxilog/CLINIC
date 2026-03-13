@extends('index')

@section('content')
    <main id="mainContent"
        class="min-h-screen bg-gray-100 p-4 sm:p-6 lg:p-8 ml-0 md:ml-64 mt-14 transition-all duration-300 md:peer-[.collapsed]:ml-16">
        <style>
            .reports-wrap { display: grid; gap: 1rem; }
            .report-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 1rem; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05); }
            .grid-4 { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem; }
            .grid-3 { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; }
            .grid-2 { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
            .title { margin: 0 0 .5rem; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .08em; }
            .value { margin: 0; font-size: 2rem; line-height: 1.1; color: #111827; font-weight: 700; }
            .sub { margin: .45rem 0 0; color: #6b7280; font-size: .85rem; }
            .heading { margin: 0; font-size: 1.02rem; font-weight: 700; color: #111827; }
            .heading-row { display: flex; align-items: center; justify-content: space-between; gap: .8rem; flex-wrap: wrap; }
            .desc { margin: .2rem 0 0; color: #6b7280; font-size: .85rem; }
            .chart-300 { height: 300px; margin-top: .8rem; }
            .toolbar { display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: .6rem; align-items: end; }
            .input { width: 100%; border: 1px solid #d1d5db; border-radius: 10px; padding: .55rem .65rem; background: #fff; color: #111827; }
            .btn { border: 1px solid #0f766e; border-radius: 10px; color: #fff; background: #0f766e; padding: .56rem .9rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
            .btn-outline { border: 1px solid #0f766e; border-radius: 10px; color: #0f766e; background: #f0fdfa; padding: .5rem .9rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
            .nav-pills { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: .5rem; margin-top: .9rem; }
            .pill { border: 1px solid #cbd5e1; border-radius: 10px; padding: .6rem .7rem; text-align: center; text-decoration: none; color: #334155; background: #fff; font-weight: 600; }
            .pill.active { border-color: #0f766e; color: #0f766e; background: #f0fdfa; }
            .table-wrap { overflow: auto; border: 1px solid #e5e7eb; border-radius: 12px; background: #fff; }
            .r-table { width: 100%; border-collapse: collapse; font-size: .86rem; }
            .r-table th, .r-table td { border-bottom: 1px solid #f1f5f9; padding: .65rem .75rem; text-align: left; white-space: nowrap; }
            .r-table th { background: #f8fafc; color: #334155; font-size: .75rem; text-transform: uppercase; letter-spacing: .06em; }
            .print-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .8rem; }
            @media (max-width: 1280px) { .grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
            @media (max-width: 768px) {
                .grid-2, .grid-3, .grid-4, .nav-pills, .print-grid, .toolbar { grid-template-columns: 1fr; }
                .value { font-size: 1.6rem; }
                .chart-300 { height: 250px; }
            }
        </style>

        @php
            $baseQuery = request()->query();
            $navUrl = fn (string $target) => route('reports.index', array_merge($baseQuery, ['section' => $target]));
        @endphp

        <div class="reports-wrap">
            <div class="report-card">
                <h1 class="heading">Clinic Reports Module</h1>
                <p class="desc">Date range: {{ $rangeLabel }}</p>

                <form method="GET" action="{{ route('reports.index') }}" class="toolbar" style="margin-top:.8rem;">
                    <input type="hidden" name="section" value="{{ $section }}">
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

                <nav class="nav-pills">
                    <a href="{{ $navUrl('overview') }}" class="pill @if($section === 'overview') active @endif">Overview</a>
                    <a href="{{ $navUrl('patients') }}" class="pill @if($section === 'patients') active @endif">Patient Reports</a>
                    <a href="{{ $navUrl('appointments') }}" class="pill @if($section === 'appointments') active @endif">Appointment Reports</a>
                    <a href="{{ $navUrl('printable') }}" class="pill @if($section === 'printable') active @endif">Printable Reports</a>
                </nav>
            </div>

            @if ($section === 'overview')
                <div class="grid-4">
                    <section class="report-card"><p class="title">Total Patients</p><p class="value">{{ number_format($totalPatients) }}</p><p class="sub">Current patient base</p></section>
                    <section class="report-card"><p class="title">Total Appointments</p><p class="value">{{ number_format($totalAppointments) }}</p><p class="sub">Within selected range</p></section>
                    <section class="report-card"><p class="title">Completed Appointments</p><p class="value" style="color:#059669;">{{ number_format($completedCount) }}</p><p class="sub">Status = Completed</p></section>
                    <section class="report-card"><p class="title">Cancelled Appointments</p><p class="value" style="color:#dc2626;">{{ number_format($cancelledCount) }}</p><p class="sub">Status = Cancelled</p></section>
                </div>

                <div class="grid-2">
                    <section class="report-card"><h3 class="heading">Appointment Status Distribution</h3><p class="desc">Scheduled, Arrived, Ongoing, Completed, Cancelled</p><div class="chart-300"><canvas id="statusPieChart"></canvas></div></section>
                    <section class="report-card"><h3 class="heading">Monthly Patient Registration Trend</h3><p class="desc">New patient registrations per month</p><div class="chart-300"><canvas id="patientRegLineChart"></canvas></div></section>
                </div>

                <div class="grid-2">
                    <section class="report-card"><h3 class="heading">Appointment Trend</h3><p class="desc">Clinic volume by date buckets</p><div class="chart-300"><canvas id="appointmentTrendBarChart"></canvas></div></section>
                    <section class="report-card"><h3 class="heading">Most Requested Services</h3><p class="desc">Service demand in selected range</p><div class="chart-300"><canvas id="servicesBarChart"></canvas></div></section>
                </div>
            @endif

            @if ($section === 'patients')
                <div class="grid-3">
                    <section class="report-card"><h3 class="heading">Patient Gender Distribution</h3><p class="desc">Overall demographics</p><div class="chart-300"><canvas id="genderPieChart"></canvas></div></section>
                    <section class="report-card"><h3 class="heading">Patient Age Distribution</h3><p class="desc">Grouped by age buckets</p><div class="chart-300"><canvas id="ageBarChart"></canvas></div></section>
                    <section class="report-card"><h3 class="heading">Patient Registration Trend</h3><p class="desc">Monthly registration growth</p><div class="chart-300"><canvas id="patientRegLineChartPatients"></canvas></div></section>
                </div>

                <section class="report-card">
                    <div class="heading-row">
                        <h3 class="heading">Patient Registration Report</h3>
                        <a class="btn-outline" target="_blank" href="{{ route('reports.print', ['reportType' => 'patients', 'range' => $range, 'from_date' => $fromDate, 'to_date' => $toDate, 'patient_from' => $patientRegFrom, 'patient_to' => $patientRegTo, 'patient_gender' => $patientGender]) }}">Print Patient Report</a>
                    </div>
                    <form method="GET" action="{{ route('reports.index') }}" class="toolbar" style="margin:.8rem 0;">
                        <input type="hidden" name="section" value="patients">
                        <input type="hidden" name="range" value="{{ $range }}">
                        <input type="hidden" name="from_date" value="{{ $fromDate }}">
                        <input type="hidden" name="to_date" value="{{ $toDate }}">
                        <div><label class="title" style="display:block;margin-bottom:.25rem;">From</label><input type="date" name="patient_from" value="{{ $patientRegFrom }}" class="input"></div>
                        <div><label class="title" style="display:block;margin-bottom:.25rem;">To</label><input type="date" name="patient_to" value="{{ $patientRegTo }}" class="input"></div>
                        <div><label class="title" style="display:block;margin-bottom:.25rem;">Gender</label><select name="patient_gender" class="input"><option value="">All</option><option value="Male" @selected($patientGender === 'Male')>Male</option><option value="Female" @selected($patientGender === 'Female')>Female</option><option value="Other" @selected($patientGender === 'Other')>Other</option></select></div>
                        <button class="btn" type="submit">Filter</button>
                    </form>
                    <div class="table-wrap">
                        <table class="r-table">
                            <thead><tr><th>Patient ID</th><th>Full Name</th><th>Gender</th><th>Birth Date</th><th>Mobile Number</th><th>Date Registered</th></tr></thead>
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
            @endif

            @if ($section === 'appointments')
                <div class="grid-2">
                    <section class="report-card"><h3 class="heading">Appointment Status Distribution</h3><p class="desc">Status outcome overview</p><div class="chart-300"><canvas id="statusPieChartAppointments"></canvas></div></section>
                    <section class="report-card"><h3 class="heading">Walk-In vs Scheduled</h3><p class="desc">Booking behavior</p><div class="chart-300"><canvas id="walkinPieChart"></canvas></div></section>
                </div>

                <div class="grid-2">
                    <section class="report-card"><h3 class="heading">Appointment Trend</h3><p class="desc">Per day or month based on range</p><div class="chart-300"><canvas id="appointmentTrendBarChartAppointments"></canvas></div></section>
                    <section class="report-card"><h3 class="heading">Most Requested Services</h3><p class="desc">Treatment demand in selected range</p><div class="chart-300"><canvas id="servicesBarChartAppointments"></canvas></div></section>
                </div>

                <section class="report-card">
                    <div class="heading-row">
                        <h3 class="heading">Appointment Report</h3>
                        <a class="btn-outline" target="_blank" href="{{ route('reports.print', ['reportType' => 'appointments', 'range' => $range, 'from_date' => $fromDate, 'to_date' => $toDate, 'appointment_from' => $appointmentFrom, 'appointment_to' => $appointmentTo, 'appointment_status' => $appointmentStatus, 'appointment_service' => $appointmentService]) }}">Print Appointment Report</a>
                    </div>
                    <form method="GET" action="{{ route('reports.index') }}" class="toolbar" style="margin:.8rem 0;">
                        <input type="hidden" name="section" value="appointments">
                        <input type="hidden" name="range" value="{{ $range }}">
                        <input type="hidden" name="from_date" value="{{ $fromDate }}">
                        <input type="hidden" name="to_date" value="{{ $toDate }}">
                        <div><label class="title" style="display:block;margin-bottom:.25rem;">From</label><input type="date" name="appointment_from" value="{{ $appointmentFrom }}" class="input"></div>
                        <div><label class="title" style="display:block;margin-bottom:.25rem;">To</label><input type="date" name="appointment_to" value="{{ $appointmentTo }}" class="input"></div>
                        <div><label class="title" style="display:block;margin-bottom:.25rem;">Status</label><select name="appointment_status" class="input"><option value="">All</option>@foreach (['Pending','Scheduled','Arrived','Ongoing','Completed','Cancelled','Waiting'] as $status)<option value="{{ $status }}" @selected($appointmentStatus === $status)>{{ $status }}</option>@endforeach</select></div>
                        <div><label class="title" style="display:block;margin-bottom:.25rem;">Service</label><select name="appointment_service" class="input"><option value="">All</option>@foreach ($serviceOptions as $id => $name)<option value="{{ $id }}" @selected((string) $appointmentService === (string) $id)>{{ $name }}</option>@endforeach</select></div>
                        <button class="btn" type="submit">Filter</button>
                    </form>
                    <div class="table-wrap">
                        <table class="r-table">
                            <thead><tr><th>Appointment ID</th><th>Patient Name</th><th>Service</th><th>Appointment Date</th><th>Status</th></tr></thead>
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
                        <div class="heading-row"><h3 class="heading">Completed Appointment Report</h3><a class="btn-outline" target="_blank" href="{{ route('reports.print', ['reportType' => 'completed', 'range' => $range, 'from_date' => $fromDate, 'to_date' => $toDate, 'appointment_from' => $appointmentFrom, 'appointment_to' => $appointmentTo]) }}">Print Completed Treatments</a></div>
                        <div class="table-wrap" style="margin-top:.8rem;">
                            <table class="r-table"><thead><tr><th>Appointment ID</th><th>Patient Name</th><th>Service</th><th>Appointment Date</th><th>Status</th></tr></thead><tbody>@forelse ($completedRows as $row)<tr><td>{{ $row->id }}</td><td>{{ trim(($row->last_name ?? '') . ', ' . ($row->first_name ?? '') . ' ' . ($row->middle_name ?? '')) }}</td><td>{{ $row->service_name ?? 'N/A' }}</td><td>{{ $row->appointment_date ? \Carbon\Carbon::parse($row->appointment_date)->format('M d, Y h:i A') : '-' }}</td><td>{{ $row->status }}</td></tr>@empty<tr><td colspan="5">No records found.</td></tr>@endforelse</tbody></table>
                        </div>
                    </section>

                    <section class="report-card">
                        <div class="heading-row"><h3 class="heading">Cancelled Appointment Report</h3><a class="btn-outline" target="_blank" href="{{ route('reports.print', ['reportType' => 'cancelled', 'range' => $range, 'from_date' => $fromDate, 'to_date' => $toDate, 'appointment_from' => $appointmentFrom, 'appointment_to' => $appointmentTo]) }}">Print Cancellation Report</a></div>
                        <div class="table-wrap" style="margin-top:.8rem;">
                            <table class="r-table"><thead><tr><th>Appointment ID</th><th>Patient Name</th><th>Service</th><th>Appointment Date</th><th>Status</th></tr></thead><tbody>@forelse ($cancelledRows as $row)<tr><td>{{ $row->id }}</td><td>{{ trim(($row->last_name ?? '') . ', ' . ($row->first_name ?? '') . ' ' . ($row->middle_name ?? '')) }}</td><td>{{ $row->service_name ?? 'N/A' }}</td><td>{{ $row->appointment_date ? \Carbon\Carbon::parse($row->appointment_date)->format('M d, Y h:i A') : '-' }}</td><td>{{ $row->status }}</td></tr>@empty<tr><td colspan="5">No records found.</td></tr>@endforelse</tbody></table>
                        </div>
                    </section>
                </div>
            @endif

            @if ($section === 'printable')
                <section class="report-card">
                    <h3 class="heading">Printable Reports</h3>
                    <p class="desc">Generate report previews then print or save as PDF.</p>
                    <div class="print-grid" style="margin-top:.9rem;">
                        <a class="btn" target="_blank" href="{{ route('reports.print', ['reportType' => 'patients', 'range' => $range, 'from_date' => $fromDate, 'to_date' => $toDate, 'patient_from' => $patientRegFrom, 'patient_to' => $patientRegTo, 'patient_gender' => $patientGender]) }}">Print Patient Report</a>
                        <a class="btn" target="_blank" href="{{ route('reports.print', ['reportType' => 'appointments', 'range' => $range, 'from_date' => $fromDate, 'to_date' => $toDate, 'appointment_from' => $appointmentFrom, 'appointment_to' => $appointmentTo, 'appointment_status' => $appointmentStatus, 'appointment_service' => $appointmentService]) }}">Print Appointment Report</a>
                        <a class="btn" target="_blank" href="{{ route('reports.print', ['reportType' => 'completed', 'range' => $range, 'from_date' => $fromDate, 'to_date' => $toDate, 'appointment_from' => $appointmentFrom, 'appointment_to' => $appointmentTo]) }}">Print Completed Treatments</a>
                        <a class="btn" target="_blank" href="{{ route('reports.print', ['reportType' => 'cancelled', 'range' => $range, 'from_date' => $fromDate, 'to_date' => $toDate, 'appointment_from' => $appointmentFrom, 'appointment_to' => $appointmentTo]) }}">Print Cancellation Report</a>
                        <a class="btn" target="_blank" href="{{ route('reports.print', ['reportType' => 'services', 'range' => $range, 'from_date' => $fromDate, 'to_date' => $toDate]) }}">Print Service Summary</a>
                        <a class="btn" target="_blank" href="{{ route('reports.print', ['reportType' => 'monthly-summary', 'range' => $range, 'from_date' => $fromDate, 'to_date' => $toDate]) }}">Print Monthly Clinic Summary</a>
                    </div>
                </section>
            @endif
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
                data: { labels: @json($statusLabels), datasets: [{ data: @json($statusCounts), backgroundColor: ['#0284c7', '#16a34a', '#f59e0b', '#0ea5e9', '#ef4444'] }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } },
            });
            build('statusPieChartAppointments', {
                type: 'pie',
                data: { labels: @json($statusLabels), datasets: [{ data: @json($statusCounts), backgroundColor: ['#0284c7', '#16a34a', '#f59e0b', '#0ea5e9', '#ef4444'] }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } },
            });
            build('patientRegLineChart', {
                type: 'line',
                data: { labels: @json($patientRegMonths), datasets: [{ label: 'Patients', data: @json($patientRegCounts), borderColor: '#2563eb', backgroundColor: 'rgba(37, 99, 235, .12)', fill: true, tension: .3 }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } },
            });
            build('patientRegLineChartPatients', {
                type: 'line',
                data: { labels: @json($patientRegMonths), datasets: [{ label: 'Patients', data: @json($patientRegCounts), borderColor: '#2563eb', backgroundColor: 'rgba(37, 99, 235, .12)', fill: true, tension: .3 }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } },
            });
            build('appointmentTrendBarChart', {
                type: 'bar',
                data: { labels: @json($dates), datasets: [{ label: 'Appointments', data: @json($totals), backgroundColor: '#0891b2' }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { ticks: { maxTicksLimit: isMobile ? 6 : 14 } }, y: { beginAtZero: true, ticks: { precision: 0 } } } },
            });
            build('appointmentTrendBarChartAppointments', {
                type: 'bar',
                data: { labels: @json($dates), datasets: [{ label: 'Appointments', data: @json($totals), backgroundColor: '#0891b2' }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { ticks: { maxTicksLimit: isMobile ? 6 : 14 } }, y: { beginAtZero: true, ticks: { precision: 0 } } } },
            });
            build('servicesBarChart', {
                type: 'bar',
                data: { labels: @json($serviceNames), datasets: [{ label: 'Count', data: @json($serviceCounts), backgroundColor: '#d97706' }] },
                options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } },
            });
            build('servicesBarChartAppointments', {
                type: 'bar',
                data: { labels: @json($serviceNames), datasets: [{ label: 'Count', data: @json($serviceCounts), backgroundColor: '#d97706' }] },
                options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } },
            });
            build('walkinPieChart', {
                type: 'pie',
                data: { labels: ['Walk-In', 'Scheduled'], datasets: [{ data: [{{ $walkInCount }}, {{ $scheduledCount }}], backgroundColor: ['#f97316', '#0ea5e9'] }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } },
            });
            build('genderPieChart', {
                type: 'pie',
                data: { labels: @json($genderLabels), datasets: [{ data: @json($genderCounts), backgroundColor: ['#3b82f6', '#ec4899', '#a855f7', '#94a3b8'] }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } },
            });
            build('ageBarChart', {
                type: 'bar',
                data: { labels: @json($ageGroupLabels), datasets: [{ label: 'Patients', data: @json($ageGroupCounts), backgroundColor: '#0f766e' }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } },
            });
        })();
    </script>
@endsection
