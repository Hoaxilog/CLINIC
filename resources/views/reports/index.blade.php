@extends('index')

@section('style')
    /* Page-specific styles */
    header { background-color: #ffffff !important; z-index: 999 !important; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
    aside { z-index: 998 !important; }
    
    @media print {
        header, aside, #dateDropdownBtn, button { display: none !important; }
        .p-6 { padding: 0 !important; margin: 0 !important; }
        .sm\:ml-64 { margin-left: 0 !important; }
        .mt-14 { margin-top: 0 !important; }
        .print\:block { display: block !important; }
        body { -webkit-print-color-adjust: exact; }
    }
@endsection

@section('content')
    
    {{--
        ========================================================
        MOCK DATA GENERATOR (For View Preview Only)
        This prevents the page from crashing if the Controller
        doesn't pass these variables yet.
        ========================================================
    --}}
    @php
        $dates = $dates ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $totals = $totals ?? [12, 19, 3, 5, 2, 3, 10];
        $statusLabels = $statusLabels ?? ['Completed', 'Pending', 'Cancelled'];
        $statusCounts = $statusCounts ?? [65, 20, 15];
        $serviceNames = $serviceNames ?? ['Cleaning', 'Whitening', 'Extraction', 'Braces'];
        $serviceCounts = $serviceCounts ?? [40, 25, 15, 20];
    @endphp
    <!-- ==================== MAIN CONTENT ==================== -->
    <main id="mainContent" class="mt-14 ml-64 transition-all duration-300 peer-[.collapsed]:ml-16 min-h-screen p-6 lg:p-8 bg-slate-50">
        
        <!-- Executive Report Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4 print:hidden">
            <div>
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-600 rounded-lg shadow-lg shadow-emerald-200">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tight">Executive Report</h1>
                </div>
                <p class="text-slate-500 mt-2 ml-14 text-sm font-medium">Live performance dashboard • {{ date('F Y') }}</p>
            </div>
            
            <div class="flex gap-3 relative z-10">
                <div class="relative">
                    <button id="dateDropdownBtn" onclick="toggleDropdown()" class="group flex items-center gap-3 bg-white border border-slate-200 text-slate-600 px-5 py-2.5 rounded-xl shadow-sm hover:border-emerald-500 hover:text-emerald-600 transition-all text-sm font-bold">
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span id="selectedDateRange">This Year</span>
                        <svg class="w-3 h-3 ml-1 text-slate-400 group-hover:text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div id="dateDropdownMenu" class="hidden absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden transform origin-top-right transition-all z-50">
                        <div class="p-2 space-y-1">
                            <a href="#" onclick="selectRange('Last 7 Days')" class="block px-4 py-2.5 text-sm text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 rounded-xl transition-colors font-medium">Last 7 Days</a>
                            <a href="#" onclick="selectRange('Last 30 Days')" class="block px-4 py-2.5 text-sm text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 rounded-xl transition-colors font-medium">Last 30 Days</a>
                            <a href="#" onclick="selectRange('This Month')" class="block px-4 py-2.5 text-sm text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 rounded-xl transition-colors font-medium">This Month</a>
                            <a href="#" onclick="selectRange('This Year')" class="block px-4 py-2.5 text-sm text-emerald-700 bg-emerald-50 rounded-xl transition-colors font-bold">This Year</a>
                        </div>
                    </div>
                </div>
                <button onclick="window.print()" class="flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-6 py-2.5 rounded-xl shadow-lg shadow-slate-300 hover:shadow-slate-400 transition-all text-sm font-bold tracking-wide">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Print Report
                </button>
            </div>
        </div>

        <div class="hidden print:block mb-8 border-b border-slate-200 pb-4">
            <h1 class="text-4xl font-bold text-slate-900">Tejada Dent Clinic Report</h1>
            <p class="text-slate-500 mt-2">Generated on {{ date('F j, Y') }}</p>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-[24px] shadow-sm border border-slate-100 hover:shadow-md transition-all group relative overflow-hidden">
                <div class="absolute right-0 top-0 h-24 w-24 bg-emerald-50 rounded-bl-[100px] -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-center mb-4">
                        <div class="p-3 bg-emerald-100 text-emerald-600 rounded-2xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <span class="text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 px-3 py-1 rounded-full">+12% vs last month</span>
                    </div>
                    <p class="text-slate-400 text-xs font-bold tracking-widest uppercase">Total Appointments</p>
                    <h2 class="text-4xl font-black text-slate-800 mt-1" id="total-appointments-display">0</h2>
                    <div class="mt-4 w-full bg-slate-100 rounded-full h-1.5 overflow-hidden"><div class="bg-emerald-500 h-1.5 rounded-full" style="width: 65%"></div></div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-[24px] shadow-sm border border-slate-100 hover:shadow-md transition-all group relative overflow-hidden">
                <div class="absolute right-0 top-0 h-24 w-24 bg-blue-50 rounded-bl-[100px] -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-center mb-4">
                        <div class="p-3 bg-blue-100 text-blue-600 rounded-2xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    <p class="text-slate-400 text-xs font-bold tracking-widest uppercase">Most Popular Service</p>
                    <h2 class="text-3xl font-black text-slate-800 mt-1 truncate" id="top-service-display">Loading...</h2>
                    <p class="text-xs text-slate-400 mt-2">Highest booking volume</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-[24px] shadow-sm border border-slate-100 hover:shadow-md transition-all group relative overflow-hidden">
                <div class="absolute right-0 top-0 h-24 w-24 bg-amber-50 rounded-bl-[100px] -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-center mb-4">
                        <div class="p-3 bg-amber-100 text-amber-600 rounded-2xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                        <span class="text-xs font-bold bg-slate-100 text-slate-600 px-3 py-1 rounded-full" id="completion-badge">Calculating...</span>
                    </div>
                    <p class="text-slate-400 text-xs font-bold tracking-widest uppercase">Completion Rate</p>
                    <h2 class="text-4xl font-black text-slate-800 mt-1" id="completion-rate-display">0%</h2>
                    <p class="text-xs text-slate-400 mt-2">Successful appointments</p>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <div class="lg:col-span-2 bg-white p-8 rounded-[32px] shadow-sm border border-slate-100">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Patient Statistics</h2>
                        <p class="text-sm text-slate-400 font-medium">Daily appointment trends</p>
                    </div>
                    <div class="flex gap-3"><div class="flex items-center gap-2 text-xs font-bold text-slate-500"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Patients</div></div>
                </div>
                <div class="relative h-80 w-full"><canvas id="dailyChart"></canvas></div>
            </div>
            <div class="bg-white p-8 rounded-[32px] shadow-sm border border-slate-100">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-slate-800">Status</h2>
                    <button class="text-slate-300 hover:text-slate-500 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg></button>
                </div>
                <div class="relative h-64 flex justify-center items-center my-2">
                    <canvas id="statusChart"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-4xl font-black text-slate-800 tracking-tighter" id="center-total">0</span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Total</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3 mt-4" id="status-legend-container"></div>
            </div>
        </div>
        
        <!-- Top Services -->
        <div class="bg-white p-8 rounded-[32px] shadow-sm border border-slate-100 mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Top Performing Services</h2>
                    <p class="text-sm text-slate-400 font-medium">Breakdown by appointment type</p>
                </div>
                <div class="p-2 bg-amber-50 rounded-lg text-amber-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg></div>
            </div>
            <div class="relative h-72"><canvas id="servicesChart"></canvas></div>
        </div>
    </main>

    <!-- Hidden Data Container for JS -->
    <div id="chart-data"
        data-dates="{{ json_encode($dates) }}"
        data-totals="{{ json_encode($totals) }}"
        data-status-labels="{{ json_encode($statusLabels) }}"
        data-status-counts="{{ json_encode($statusCounts) }}"
        data-service-names="{{ json_encode($serviceNames) }}"
        data-service-counts="{{ json_encode($serviceCounts) }}"
        class="hidden">
    </div>
@endsection

@push('script')
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // --- 1. Sidebar Logic ---
        (function(){
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggleBtn');
            if (!sidebar || !toggleBtn) return;
            if (localStorage.getItem('sidebar-collapsed') === 'true') {
                sidebar.classList.add('collapsed');
            }
            toggleBtn.addEventListener('click', function () {
                sidebar.classList.toggle('collapsed');
                localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
            });
        })();

        // --- 2. Dropdown Functions ---
        function toggleDropdown() { document.getElementById('dateDropdownMenu').classList.toggle('hidden'); }
        function selectRange(range) {
            document.getElementById('selectedDateRange').innerText = range;
            document.getElementById('dateDropdownMenu').classList.add('hidden');
        }
        window.addEventListener('click', function(e) {
            const btn = document.getElementById('dateDropdownBtn');
            const menu = document.getElementById('dateDropdownMenu');
            if (!btn.contains(e.target) && !menu.contains(e.target)) { menu.classList.add('hidden'); }
        });

        // --- 3. Chart Logic ---
        document.addEventListener('DOMContentLoaded', function() {
            // Parse Data
            const dataElement = document.getElementById('chart-data');
            const dailyDates = JSON.parse(dataElement.dataset.dates);
            const dailyTotals = JSON.parse(dataElement.dataset.totals);
            const statusLabels = JSON.parse(dataElement.dataset.statusLabels);
            const statusCounts = JSON.parse(dataElement.dataset.statusCounts);
            const serviceNames = JSON.parse(dataElement.dataset.serviceNames);
            const serviceCounts = JSON.parse(dataElement.dataset.serviceCounts);

            // KPI Calculations
            const totalAppts = dailyTotals.reduce((a, b) => a + b, 0);
            document.getElementById('total-appointments-display').innerText = totalAppts;
            document.getElementById('center-total').innerText = totalAppts;

            if (serviceNames.length > 0) {
                const maxCount = Math.max(...serviceCounts);
                const maxIndex = serviceCounts.indexOf(maxCount);
                document.getElementById('top-service-display').innerText = serviceNames[maxIndex];
            } else { document.getElementById('top-service-display').innerText = "N/A"; }

            const completedIndex = statusLabels.indexOf('Completed');
            if (completedIndex !== -1 && totalAppts > 0) {
                const rate = Math.round((statusCounts[completedIndex] / totalAppts) * 100);
                document.getElementById('completion-rate-display').innerText = rate + "%";
                const badge = document.getElementById('completion-badge');
                if(rate < 50) { badge.className = "text-xs font-bold bg-red-50 text-red-600 px-3 py-1 rounded-full"; badge.innerText = "Needs Attention"; }
                else { badge.className = "text-xs font-bold bg-emerald-50 text-emerald-600 px-3 py-1 rounded-full"; badge.innerText = "On Track"; }
            } else { document.getElementById('completion-badge').innerText = "No Data"; }

            // Chart Configurations
            Chart.defaults.font.family = "ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif";
            Chart.defaults.color = '#64748b';
            
            // 1. Bar Chart
            new Chart(document.getElementById('dailyChart').getContext('2d'), {
                type: 'bar',
                data: { labels: dailyDates, datasets: [{ label: 'Patients', data: dailyTotals, backgroundColor: '#10b981', borderRadius: 6, barThickness: 25, hoverBackgroundColor: '#059669' }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9', borderDash: [5, 5], drawBorder: false }, border: { display: false }, ticks: { padding: 15, font: { weight: '500' } } }, x: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 11, weight: '500' } } } } }
            });

            // 2. Doughnut Chart
            const statusColors = ['#10b981', '#f59e0b', '#ef4444', '#3b82f6']; 
            new Chart(document.getElementById('statusChart').getContext('2d'), {
                type: 'doughnut',
                data: { labels: statusLabels, datasets: [{ data: statusCounts, backgroundColor: statusColors, borderWidth: 0, hoverOffset: 8, cutout: '85%' }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, animation: { animateScale: true, animateRotate: true } }
            });

            // Legend Generation
            const legendContainer = document.getElementById('status-legend-container');
            statusLabels.forEach((label, index) => {
                const color = statusColors[index % statusColors.length];
                legendContainer.innerHTML += `<div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-xl"><div class="flex items-center gap-2.5"><span class="w-2.5 h-2.5 rounded-full" style="background-color: ${color}"></span><span class="text-xs font-bold text-slate-600 uppercase tracking-wide">${label}</span></div><span class="text-sm font-black text-slate-800">${statusCounts[index]}</span></div>`;
            });

            // 3. Horizontal Bar Chart
            new Chart(document.getElementById('servicesChart').getContext('2d'), {
                type: 'bar',
                data: { labels: serviceNames, datasets: [{ label: 'Bookings', data: serviceCounts, backgroundColor: '#f59e0b', borderRadius: 8, barPercentage: 0.6, categoryPercentage: 0.8, hoverBackgroundColor: '#d97706' }] },
                options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, grid: { color: '#f1f5f9', borderDash: [5, 5], drawBorder: false }, border: { display: false }, ticks: { stepSize: 1 } }, y: { grid: { display: false }, border: { display: false }, ticks: { font: { weight: '600', size: 12 } } } } }
            });
        });
    </script>
@endpush