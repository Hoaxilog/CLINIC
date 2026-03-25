@extends('index')

@section('content')
    @php
        $baseQuery = request()->query();
        $navUrl = fn (string $target) => route('reports.index', array_merge($baseQuery, ['section' => $target]));
    @endphp

    <div class="space-y-4" style="font-family:'Montserrat',sans-serif;">

        {{-- ── HEADER CARD ── --}}
        <section class="border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 border border-[#0086da]/15 bg-[#0086da]/5 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-[#0086da]">
                        Analytics
                    </div>
                    <h1 class="mt-2 text-[1.1rem] font-extrabold tracking-tight text-[#1a2e3b]">Clinic Reports Module</h1>
                    <p class="mt-1 text-[.76rem] text-[#7a9db5]">Date range: {{ $rangeLabel }}</p>
                </div>
            </div>

            {{-- Toolbar --}}
            <form method="GET" action="{{ route('reports.index') }}" class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <input type="hidden" name="section" value="{{ $section }}">
                <div>
                    <label class="mb-1 block text-[.6rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Range</label>
                    <select name="range" class="w-full border border-gray-200 bg-white px-3 py-[9px] text-[.8rem] text-[#1a2e3b] focus:border-[#0086da] focus:outline-none">
                        <option value="today" @selected($range === 'today')>Today</option>
                        <option value="week" @selected($range === 'week')>This Week</option>
                        <option value="month" @selected($range === 'month')>This Month</option>
                        <option value="year" @selected($range === 'year')>This Year</option>
                        <option value="custom" @selected($range === 'custom')>Custom Date Range</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-[.6rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">From</label>
                    <input type="date" name="from_date" value="{{ $fromDate }}" class="w-full border border-gray-200 bg-white px-3 py-[9px] text-[.8rem] text-[#1a2e3b] focus:border-[#0086da] focus:outline-none">
                </div>
                <div>
                    <label class="mb-1 block text-[.6rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">To</label>
                    <input type="date" name="to_date" value="{{ $toDate }}" class="w-full border border-gray-200 bg-white px-3 py-[9px] text-[.8rem] text-[#1a2e3b] focus:border-[#0086da] focus:outline-none">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-[#0086da] px-4 py-[9px] text-[.7rem] font-bold uppercase tracking-[.1em] text-white transition hover:bg-[#006ab0]">Apply</button>
                </div>
            </form>

            {{-- Nav Tabs --}}
            <div class="mt-4 grid grid-cols-2 gap-2 sm:grid-cols-4">
                @foreach([['overview','Overview'],['patients','Patient Reports'],['appointments','Appointment Reports'],['printable','Printable Reports']] as [$tab,$label])
                    <a href="{{ $navUrl($tab) }}"
                        class="border px-3 py-2 text-center text-[.68rem] font-bold uppercase tracking-[.08em] transition
                            {{ $section === $tab
                                ? 'border-[#0086da] bg-[#f0f8fe] text-[#0086da]'
                                : 'border-gray-200 text-[#3d5a6e] hover:border-[#0086da] hover:text-[#0086da]' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </section>

        {{-- ══════════════════════════════════════════════════════════════
             OVERVIEW SECTION
        ══════════════════════════════════════════════════════════════ --}}
        @if ($section === 'overview')

            {{-- KPI Cards --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @php
                    $kpis = [
                        ['label' => 'New Patients',         'value' => number_format($newPatientsCount),        'color' => 'text-[#0086da]'],
                        ['label' => 'Completed Appointments',      'value' => number_format($completedCount),          'color' => 'text-emerald-600'],
                        ['label' => 'Cancelled Appointments',      'value' => number_format($cancelledCount),          'color' => 'text-rose-600'],
                        ['label' => 'Completion Rate',      'value' => number_format($completionRate) . '%',    'color' => $completionRate >= 50 ? 'text-emerald-600' : 'text-amber-600',
                         'sub' => number_format($totalAppointments) . ' total in range'],
                    ];
                @endphp
                @foreach ($kpis as $kpi)
                    <section class="border border-gray-200 bg-white p-5 shadow-sm">
                        <p class="text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">{{ $kpi['label'] }}</p>
                        <p class="mt-2 text-3xl font-bold {{ $kpi['color'] }}">{{ $kpi['value'] }}</p>
                        @if (!empty($kpi['sub']))
                            <p class="mt-1 text-[.72rem] text-[#7a9db5]">{{ $kpi['sub'] }}</p>
                        @endif
                    </section>
                @endforeach
            </div>

            {{-- Revenue Cards --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <section class="border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Revenue</p>
                    <p class="mt-2 text-2xl font-bold text-[#1a2e3b]">PHP {{ number_format($totalRevenue, 2) }}</p>
                    <p class="mt-1 text-[.72rem] text-[#7a9db5]">Collected payments in range</p>
                </section>
                <section class="border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Cost</p>
                    <p class="mt-2 text-2xl font-bold text-[#1a2e3b]">PHP {{ number_format($totalCost, 2) }}</p>
                    <p class="mt-1 text-[.72rem] text-[#7a9db5]">Treatment costs in range</p>
                </section>
                <section class="border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Profit</p>
                    <p class="mt-2 text-2xl font-bold {{ $totalProfit >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">PHP {{ number_format($totalProfit, 2) }}</p>
                    <p class="mt-1 text-[.72rem] text-[#7a9db5]">Margin: {{ $profitMargin === null ? '--' : number_format($profitMargin, 1) . '%' }}</p>
                </section>
            </div>


            {{-- Charts Row 1 --}}
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <section class="border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="text-[.55rem] font-bold uppercase tracking-[.2em] text-[#7a9db5]">Patient Mix</div>
                    <h3 class="text-[.88rem] font-extrabold text-[#1a2e3b]">New vs Returning Patients</h3>
                    <p class="mt-0.5 text-[.72rem] text-[#7a9db5]">Distinct patients per date bucket based on first appointment history</p>
                    <div style="height:280px;margin-top:.8rem;"><canvas id="newVsReturningChart"></canvas></div>
                </section>
                <section class="border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="text-[.55rem] font-bold uppercase tracking-[.2em] text-[#7a9db5]">Trend</div>
                    <h3 class="text-[.88rem] font-extrabold text-[#1a2e3b]">New Patient Records Over Time</h3>
                    <p class="mt-0.5 text-[.72rem] text-[#7a9db5]">Patient records created over the selected period</p>
                    <div style="height:280px;margin-top:.8rem;"><canvas id="patientRegLineChart"></canvas></div>
                </section>
            </div>

            {{-- Charts Row 2 --}}
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <section class="border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="text-[.55rem] font-bold uppercase tracking-[.2em] text-[#7a9db5]">Volume</div>
                    <h3 class="text-[.88rem] font-extrabold text-[#1a2e3b]">Appointment Trend</h3>
                    <p class="mt-0.5 text-[.72rem] text-[#7a9db5]">Clinic volume by date buckets</p>
                    <div style="height:280px;margin-top:.8rem;"><canvas id="appointmentTrendBarChart"></canvas></div>
                </section>
                <section class="border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="text-[.55rem] font-bold uppercase tracking-[.2em] text-[#7a9db5]">Output</div>
                    <h3 class="text-[.88rem] font-extrabold text-[#1a2e3b]">Most Performed Treatments</h3>
                    <p class="mt-0.5 text-[.72rem] text-[#7a9db5]">Based on completed treatment records in the selected range</p>
                    <div style="height:280px;margin-top:.8rem;"><canvas id="servicesBarChart"></canvas></div>
                </section>
            </div>
        @endif

        {{-- ══════════════════════════════════════════════════════════════
             PATIENTS SECTION
        ══════════════════════════════════════════════════════════════ --}}
        @if ($section === 'patients')
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                <section class="border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="text-[.55rem] font-bold uppercase tracking-[.2em] text-[#7a9db5]">Demographics</div>
                    <h3 class="text-[.88rem] font-extrabold text-[#1a2e3b]">Gender Distribution</h3>
                    <p class="mt-0.5 text-[.72rem] text-[#7a9db5]">Overall demographics</p>
                    <div style="height:260px;margin-top:.8rem;"><canvas id="genderPieChart"></canvas></div>
                </section>
                <section class="border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="text-[.55rem] font-bold uppercase tracking-[.2em] text-[#7a9db5]">Demographics</div>
                    <h3 class="text-[.88rem] font-extrabold text-[#1a2e3b]">Age Distribution</h3>
                    <p class="mt-0.5 text-[.72rem] text-[#7a9db5]">Grouped by age buckets</p>
                    <div style="height:260px;margin-top:.8rem;"><canvas id="ageBarChart"></canvas></div>
                </section>
                <section class="border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="text-[.55rem] font-bold uppercase tracking-[.2em] text-[#7a9db5]">Growth</div>
                    <h3 class="text-[.88rem] font-extrabold text-[#1a2e3b]">New Patient Records Over Time</h3>
                    <p class="mt-0.5 text-[.72rem] text-[#7a9db5]">Patient records created over the selected period</p>
                    <div style="height:260px;margin-top:.8rem;"><canvas id="patientRegLineChartPatients"></canvas></div>
                </section>
            </div>

            <section class="border border-gray-200 bg-white shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 px-5 py-4">
                    <div>
                        <div class="text-[.55rem] font-bold uppercase tracking-[.2em] text-[#7a9db5]">Table</div>
                        <h3 class="text-[.88rem] font-extrabold text-[#1a2e3b]">Patient Registration Report</h3>
                    </div>
                    <a class="inline-flex items-center border border-[#0086da] bg-white px-4 py-[8px] text-[.68rem] font-bold uppercase tracking-[.1em] text-[#0086da] transition hover:bg-[#f0f8fe]"
                        target="_blank"
                        href="{{ route('reports.print', ['reportType' => 'patients', 'range' => $range, 'from_date' => $fromDate, 'to_date' => $toDate, 'patient_from' => $patientRegFrom, 'patient_to' => $patientRegTo, 'patient_gender' => $patientGender]) }}">
                        Print Patient Report
                    </a>
                </div>

                <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 gap-3 border-b border-gray-200 px-5 py-4 sm:grid-cols-4">
                    <input type="hidden" name="section" value="patients">
                    <input type="hidden" name="range" value="{{ $range }}">
                    <input type="hidden" name="from_date" value="{{ $fromDate }}">
                    <input type="hidden" name="to_date" value="{{ $toDate }}">
                    <div>
                        <label class="mb-1 block text-[.6rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">From</label>
                        <input type="date" name="patient_from" value="{{ $patientRegFrom }}" class="w-full border border-gray-200 bg-white px-3 py-[9px] text-[.8rem] text-[#1a2e3b] focus:border-[#0086da] focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-[.6rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">To</label>
                        <input type="date" name="patient_to" value="{{ $patientRegTo }}" class="w-full border border-gray-200 bg-white px-3 py-[9px] text-[.8rem] text-[#1a2e3b] focus:border-[#0086da] focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-[.6rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Gender</label>
                        <select name="patient_gender" class="w-full border border-gray-200 bg-white px-3 py-[9px] text-[.8rem] text-[#1a2e3b] focus:border-[#0086da] focus:outline-none">
                            <option value="">All</option>
                            <option value="Male" @selected($patientGender === 'Male')>Male</option>
                            <option value="Female" @selected($patientGender === 'Female')>Female</option>
                            <option value="Other" @selected($patientGender === 'Other')>Other</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button class="w-full bg-[#0086da] px-4 py-[9px] text-[.7rem] font-bold uppercase tracking-[.1em] text-white transition hover:bg-[#006ab0]" type="submit">Filter</button>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[.82rem]">
                        <thead class="border-b border-gray-200 bg-[#f6fafd]">
                            <tr>
                                <th class="px-5 py-3 text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Patient ID</th>
                                <th class="px-5 py-3 text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Full Name</th>
                                <th class="px-5 py-3 text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Gender</th>
                                <th class="px-5 py-3 text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Birth Date</th>
                                <th class="px-5 py-3 text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Mobile</th>
                                <th class="px-5 py-3 text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Date Registered</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($patientRows as $row)
                                <tr class="hover:bg-[#f8fbfe]">
                                    <td class="px-5 py-3 text-[.78rem] font-bold text-[#0086da]">{{ $row->id }}</td>
                                    <td class="px-5 py-3 font-semibold text-[#1a2e3b]">{{ trim(($row->last_name ?? '') . ', ' . ($row->first_name ?? '') . ' ' . ($row->middle_name ?? '')) }}</td>
                                    <td class="px-5 py-3 text-[#3d5a6e]">{{ $row->gender ?? 'Unspecified' }}</td>
                                    <td class="px-5 py-3 text-[#7a9db5]">{{ $row->birth_date ? \Carbon\Carbon::parse($row->birth_date)->format('M d, Y') : '—' }}</td>
                                    <td class="px-5 py-3 text-[#3d5a6e]">{{ $row->mobile_number ?? '—' }}</td>
                                    <td class="px-5 py-3 text-[#7a9db5]">{{ $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('M d, Y h:i A') : '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-5 py-8 text-center text-[.82rem] text-[#7a9db5]">No records found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        {{-- ══════════════════════════════════════════════════════════════
             APPOINTMENTS SECTION
        ══════════════════════════════════════════════════════════════ --}}
        @if ($section === 'appointments')
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <section class="border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="text-[.55rem] font-bold uppercase tracking-[.2em] text-[#7a9db5]">Patient Mix</div>
                    <h3 class="text-[.88rem] font-extrabold text-[#1a2e3b]">New vs Returning Patients</h3>
                    <p class="mt-0.5 text-[.72rem] text-[#7a9db5]">Distinct patients per date bucket based on first appointment history</p>
                    <div style="height:280px;margin-top:.8rem;"><canvas id="newVsReturningChartAppointments"></canvas></div>
                </section>
                <section class="border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="text-[.55rem] font-bold uppercase tracking-[.2em] text-[#7a9db5]">Booking Type</div>
                    <h3 class="text-[.88rem] font-extrabold text-[#1a2e3b]">Walk-In vs Online Appointment</h3>
                    <p class="mt-0.5 text-[.72rem] text-[#7a9db5]">Booking behaviour</p>
                    <div style="height:280px;margin-top:.8rem;"><canvas id="walkinPieChart"></canvas></div>
                </section>
            </div>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <section class="border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="text-[.55rem] font-bold uppercase tracking-[.2em] text-[#7a9db5]">Volume</div>
                    <h3 class="text-[.88rem] font-extrabold text-[#1a2e3b]">Appointment Trend</h3>
                    <p class="mt-0.5 text-[.72rem] text-[#7a9db5]">Per day or month based on range</p>
                    <div style="height:280px;margin-top:.8rem;"><canvas id="appointmentTrendBarChartAppointments"></canvas></div>
                </section>
                <section class="border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="text-[.55rem] font-bold uppercase tracking-[.2em] text-[#7a9db5]">Output</div>
                    <h3 class="text-[.88rem] font-extrabold text-[#1a2e3b]">Most Performed Treatments</h3>
                    <p class="mt-0.5 text-[.72rem] text-[#7a9db5]">Based on completed treatment records in the selected range</p>
                    <div style="height:280px;margin-top:.8rem;"><canvas id="servicesBarChartAppointments"></canvas></div>
                </section>
            </div>

            <section class="border border-gray-200 bg-white shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3 border-b border-gray-200 px-5 py-4">
                    <div>
                        <div class="text-[.55rem] font-bold uppercase tracking-[.2em] text-[#7a9db5]">Table</div>
                        <h3 class="text-[.88rem] font-extrabold text-[#1a2e3b]">Appointment Report</h3>
                        @php
                            $appointmentCount      = ($appointmentRows ?? collect())->count();
                            $totalPaymentMade      = (float)(($appointmentRows ?? collect())->sum('payment_made') ?? 0);
                            $totalTreatmentCost    = (float)(($appointmentRows ?? collect())->sum('treatment_cost') ?? 0);
                            $netFromTreatment      = $totalPaymentMade - $totalTreatmentCost;
                        @endphp
                        <p class="mt-1 text-[.72rem] text-[#7a9db5]">
                            {{ number_format($appointmentCount) }} records ·
                            Paid PHP {{ number_format($totalPaymentMade, 2) }} ·
                            Cost PHP {{ number_format($totalTreatmentCost, 2) }} ·
                            Net PHP {{ number_format($netFromTreatment, 2) }}
                        </p>
                    </div>
                    <a class="inline-flex items-center border border-[#0086da] bg-white px-4 py-[8px] text-[.68rem] font-bold uppercase tracking-[.1em] text-[#0086da] transition hover:bg-[#f0f8fe]"
                        target="_blank"
                        href="{{ route('reports.print', ['reportType' => 'appointments', 'range' => $range, 'from_date' => $fromDate, 'to_date' => $toDate, 'appointment_from' => $appointmentFrom, 'appointment_to' => $appointmentTo, 'appointment_status' => $appointmentStatus, 'appointment_service' => $appointmentService]) }}">
                        Print Report
                    </a>
                </div>

                <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 gap-3 border-b border-gray-200 px-5 py-4 sm:grid-cols-2 lg:grid-cols-5">
                    <input type="hidden" name="section" value="appointments">
                    <input type="hidden" name="range" value="{{ $range }}">
                    <input type="hidden" name="from_date" value="{{ $fromDate }}">
                    <input type="hidden" name="to_date" value="{{ $toDate }}">
                    <div>
                        <label class="mb-1 block text-[.6rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">From</label>
                        <input type="date" name="appointment_from" value="{{ $appointmentFrom }}" class="w-full border border-gray-200 bg-white px-3 py-[9px] text-[.8rem] text-[#1a2e3b] focus:border-[#0086da] focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-[.6rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">To</label>
                        <input type="date" name="appointment_to" value="{{ $appointmentTo }}" class="w-full border border-gray-200 bg-white px-3 py-[9px] text-[.8rem] text-[#1a2e3b] focus:border-[#0086da] focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-[.6rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Status</label>
                        <select name="appointment_status" class="w-full border border-gray-200 bg-white px-3 py-[9px] text-[.8rem] text-[#1a2e3b] focus:border-[#0086da] focus:outline-none">
                            <option value="">All</option>
                            @foreach (['Pending','Scheduled','Arrived','Ongoing','Completed','Cancelled','Waiting'] as $st)
                                <option value="{{ $st }}" @selected($appointmentStatus === $st)>{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-[.6rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Service</label>
                        <select name="appointment_service" class="w-full border border-gray-200 bg-white px-3 py-[9px] text-[.8rem] text-[#1a2e3b] focus:border-[#0086da] focus:outline-none">
                            <option value="">All</option>
                            @foreach ($serviceOptions as $id => $name)
                                <option value="{{ $id }}" @selected((string) $appointmentService === (string) $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button class="w-full bg-[#0086da] px-4 py-[9px] text-[.7rem] font-bold uppercase tracking-[.1em] text-white transition hover:bg-[#006ab0]" type="submit">Filter</button>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[.82rem]">
                        <thead class="border-b border-gray-200 bg-[#f6fafd]">
                            <tr>
                                <th class="px-5 py-3 text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">ID</th>
                                <th class="px-5 py-3 text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Patient Name</th>
                                <th class="px-5 py-3 text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Service</th>
                                <th class="px-5 py-3 text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Treatment</th>
                                <th class="px-5 py-3 text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Date</th>
                                <th class="px-5 py-3 text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Status</th>
                                <th class="px-5 py-3 text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Payment</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($appointmentRows as $row)
                                <tr class="hover:bg-[#f8fbfe]">
                                    <td class="px-5 py-3 text-[.78rem] font-bold text-[#0086da]">{{ $row->id }}</td>
                                    <td class="px-5 py-3 font-semibold text-[#1a2e3b]">{{ trim(($row->last_name ?? '') . ', ' . ($row->first_name ?? '') . ' ' . ($row->middle_name ?? '')) }}</td>
                                    <td class="px-5 py-3 text-[#3d5a6e]">{{ $row->service_name ?? 'N/A' }}</td>
                                    <td class="px-5 py-3 text-[#7a9db5]">{{ $row->treatment_performed ?: 'N/A' }}</td>
                                    <td class="px-5 py-3 text-[#7a9db5]">{{ $row->appointment_date ? \Carbon\Carbon::parse($row->appointment_date)->format('M d, Y h:i A') : '—' }}</td>
                                    <td class="px-5 py-3">
                                        @php
                                            $stColor = match(strtolower($row->status)) {
                                                'completed'  => 'bg-emerald-100 text-emerald-700',
                                                'cancelled'  => 'bg-rose-100 text-rose-700',
                                                'ongoing'    => 'bg-sky-100 text-sky-700',
                                                'arrived'    => 'bg-cyan-100 text-cyan-700',
                                                'pending'    => 'bg-amber-100 text-amber-700',
                                                default      => 'bg-gray-100 text-gray-600',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 text-[.6rem] font-bold uppercase tracking-[.12em] {{ $stColor }}">{{ $row->status }}</span>
                                    </td>
                                    <td class="px-5 py-3 font-semibold text-[#1a2e3b]">PHP {{ number_format((float)($row->payment_made ?? 0), 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-5 py-8 text-center text-[.82rem] text-[#7a9db5]">No records found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        {{-- ══════════════════════════════════════════════════════════════
             PRINTABLE SECTION
        ══════════════════════════════════════════════════════════════ --}}
        @if ($section === 'printable')
            <section class="border border-gray-200 bg-white p-6 shadow-sm">
                <div class="text-[.55rem] font-bold uppercase tracking-[.2em] text-[#7a9db5]">Export</div>
                <h3 class="text-[.88rem] font-extrabold text-[#1a2e3b]">Printable Reports</h3>
                <p class="mt-1 text-[.76rem] text-[#7a9db5]">Generate report previews, then print or save as PDF.</p>
                <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <a class="flex items-center justify-center gap-2 bg-[#0086da] px-4 py-3 text-[.7rem] font-bold uppercase tracking-[.1em] text-white transition hover:bg-[#006ab0]"
                        target="_blank"
                        href="{{ route('reports.print', ['reportType' => 'patients', 'range' => $range, 'from_date' => $fromDate, 'to_date' => $toDate, 'patient_from' => $patientRegFrom, 'patient_to' => $patientRegTo, 'patient_gender' => $patientGender]) }}">
                        Print Patient Report
                    </a>
                    <a class="flex items-center justify-center gap-2 bg-[#0086da] px-4 py-3 text-[.7rem] font-bold uppercase tracking-[.1em] text-white transition hover:bg-[#006ab0]"
                        target="_blank"
                        href="{{ route('reports.print', ['reportType' => 'appointments', 'range' => $range, 'from_date' => $fromDate, 'to_date' => $toDate, 'appointment_from' => $appointmentFrom, 'appointment_to' => $appointmentTo, 'appointment_status' => $appointmentStatus, 'appointment_service' => $appointmentService]) }}">
                        Print Appointment Report
                    </a>
                    <a class="flex items-center justify-center gap-2 bg-[#0086da] px-4 py-3 text-[.7rem] font-bold uppercase tracking-[.1em] text-white transition hover:bg-[#006ab0]"
                        target="_blank"
                        href="{{ route('reports.print', ['reportType' => 'monthly-summary', 'range' => $range, 'from_date' => $fromDate, 'to_date' => $toDate]) }}">
                        Print Monthly Summary
                    </a>
                </div>
            </section>
        @endif

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (() => {
            if (typeof Chart === 'undefined') return;

            const isMobile = window.innerWidth < 768;

            // ── Vibrant multi-color palette ─────────────────────────────────
            const P = [
                '#0086da','#10b981','#f59e0b','#ef4444','#8b5cf6',
                '#06b6d4','#f97316','#ec4899','#84cc16','#6366f1',
            ];
            const palette = (n) => Array.from({ length: n }, (_, i) => P[i % P.length]);

            // Age-group gradient (cool → warm)
            const ageColors = ['#6366f1','#0086da','#06b6d4','#10b981','#f59e0b','#ef4444'];
            const appointmentTrendCompletedTotals = @json($completedTotals);
            const appointmentTrendCancelledTotals = @json($cancelledTotals);

            const build = (id, config) => {
                const ctx = document.getElementById(id);
                if (!ctx) return;
                new Chart(ctx.getContext('2d'), config);
            };

            const integerTicks = {
                precision: 0,
                callback: (value) => Number.isInteger(Number(value)) ? value : '',
            };

            const appointmentTrendTooltip = {
                callbacks: {
                    label: (ctx) => ` Total: ${ctx.parsed.y}`,
                    afterBody: (items) => {
                        const dataIndex = items[0]?.dataIndex ?? 0;
                        return [
                            `Completed: ${appointmentTrendCompletedTotals[dataIndex] ?? 0}`,
                            `Cancelled: ${appointmentTrendCancelledTotals[dataIndex] ?? 0}`,
                        ];
                    },
                },
            };

            // New vs Returning Patients (overview)
            build('newVsReturningChart', {
                type: 'bar',
                data: {
                    labels: @json($newVsReturningLabels),
                    datasets: [
                        {
                            label: 'New Patients',
                            data: @json($newPatientVisitTotals),
                            backgroundColor: '#0086da',
                            borderRadius: 3,
                        },
                        {
                            label: 'Returning Patients',
                            data: @json($returningPatientVisitTotals),
                            backgroundColor: '#10b981',
                            borderRadius: 3,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: {
                        x: { stacked: false, ticks: { maxTicksLimit: isMobile ? 6 : 12 } },
                        y: { beginAtZero: true, ticks: integerTicks },
                    },
                },
            });

            // New vs Returning Patients (appointments tab)
            build('newVsReturningChartAppointments', {
                type: 'bar',
                data: {
                    labels: @json($newVsReturningLabels),
                    datasets: [
                        {
                            label: 'New Patients',
                            data: @json($newPatientVisitTotals),
                            backgroundColor: '#0086da',
                            borderRadius: 3,
                        },
                        {
                            label: 'Returning Patients',
                            data: @json($returningPatientVisitTotals),
                            backgroundColor: '#10b981',
                            borderRadius: 3,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: {
                        x: { stacked: false, ticks: { maxTicksLimit: isMobile ? 6 : 12 } },
                        y: { beginAtZero: true, ticks: integerTicks },
                    },
                },
            });

            // Patient Registration Line (overview)
            build('patientRegLineChart', {
                type: 'line',
                data: {
                    labels: @json($patientRegMonths),
                    datasets: [{
                        label: 'Patients',
                        data: @json($patientRegCounts),
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139,92,246,.12)',
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#8b5cf6',
                        pointRadius: 3,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: integerTicks } },
                },
            });

            // Patient Registration Line (patients tab)
            build('patientRegLineChartPatients', {
                type: 'line',
                data: {
                    labels: @json($patientRegMonths),
                    datasets: [{
                        label: 'Patients',
                        data: @json($patientRegCounts),
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139,92,246,.12)',
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#8b5cf6',
                        pointRadius: 3,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: integerTicks } },
                },
            });

            // Appointment Trend Bar (overview) — multi-color per bar
            build('appointmentTrendBarChart', {
                type: 'bar',
                data: {
                    labels: @json($dates),
                    datasets: [{
                        label: 'Appointments',
                        data: @json($totals),
                        backgroundColor: palette(@json(count($totals))),
                        borderRadius: 3,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: appointmentTrendTooltip },
                    scales: {
                        x: { ticks: { maxTicksLimit: isMobile ? 6 : 14 } },
                        y: { beginAtZero: true, ticks: integerTicks },
                    },
                },
            });

            // Appointment Trend Bar (appointments tab)
            build('appointmentTrendBarChartAppointments', {
                type: 'bar',
                data: {
                    labels: @json($dates),
                    datasets: [{
                        label: 'Appointments',
                        data: @json($totals),
                        backgroundColor: palette(@json(count($totals))),
                        borderRadius: 3,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: appointmentTrendTooltip },
                    scales: {
                        x: { ticks: { maxTicksLimit: isMobile ? 6 : 14 } },
                        y: { beginAtZero: true, ticks: integerTicks },
                    },
                },
            });

            // Per-service profit data (from controller)
            const svcRevenues = [];
            const svcCosts    = [];
            const svcProfits  = [];

            const svcTooltip = {
                callbacks: {
                    label: (ctx) => [
                        ` Appointments : ${ctx.parsed.x ?? ctx.parsed.y}`,
                        ` Revenue      : PHP ${Number(svcRevenues[ctx.dataIndex]).toLocaleString('en-PH', {minimumFractionDigits:2})}`,
                        ` Cost         : PHP ${Number(svcCosts[ctx.dataIndex]).toLocaleString('en-PH', {minimumFractionDigits:2})}`,
                        ` Profit       : PHP ${Number(svcProfits[ctx.dataIndex]).toLocaleString('en-PH', {minimumFractionDigits:2})}`,
                    ],
                },
            };

            // Services Bar (overview) — each bar its own color
            build('servicesBarChart', {
                type: 'bar',
                data: {
                    labels: @json($serviceNames),
                    datasets: [{
                        label: 'Treatments',
                        data: @json($serviceCounts),
                        backgroundColor: palette(@json(count($serviceCounts))),
                        borderRadius: 3,
                    }],
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { x: { beginAtZero: true, ticks: integerTicks } },
                },
            });

            // Services Bar (appointments tab)
            build('servicesBarChartAppointments', {
                type: 'bar',
                data: {
                    labels: @json($serviceNames),
                    datasets: [{
                        label: 'Treatments',
                        data: @json($serviceCounts),
                        backgroundColor: palette(@json(count($serviceCounts))),
                        borderRadius: 3,
                    }],
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { x: { beginAtZero: true, ticks: integerTicks } },
                },
            });

            // Walk-In vs Online Pie
            build('walkinPieChart', {
                type: 'doughnut',
                data: {
                    labels: ['Walk-In', 'Online Appointment'],
                    datasets: [{ data: [{{ $walkInCount }}, {{ $scheduledCount }}], backgroundColor: ['#f97316','#0086da'], borderWidth: 2, borderColor: '#fff' }],
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } },
            });

            // Gender Pie — distinct gender colors
            build('genderPieChart', {
                type: 'doughnut',
                data: {
                    labels: @json($genderLabels),
                    datasets: [{ data: @json($genderCounts), backgroundColor: ['#0086da','#ec4899','#8b5cf6','#94a3b8'], borderWidth: 2, borderColor: '#fff' }],
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } },
            });

            // Age Group Bar — cool-to-warm gradient across groups
            build('ageBarChart', {
                type: 'bar',
                data: {
                    labels: @json($ageGroupLabels),
                    datasets: [{ label: 'Patients', data: @json($ageGroupCounts), backgroundColor: ageColors, borderRadius: 3 }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: integerTicks } },
                },
            });
        })();
    </script>
@endsection
