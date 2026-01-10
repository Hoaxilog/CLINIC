@extends('index')

@section('content')
    <main id="mainContent" class="min-h-screen bg-gray-100 p-6 lg:p-8 ml-64 mt-14 transition-all duration-300 peer-[.collapsed]:ml-16">
        <h1 class="text-3xl lg:text-4xl font-bold text-gray-800">Dashboard</h1>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <section class="lg:col-span-1 bg-white p-6 rounded-lg shadow-md space-y-4 h-full">
            <div class="flex items-center justify-between">
                <p class="font-medium text-gray-700">Today's Appointment</p>
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0086da" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
            </div>
            <div>
                <h1 class="text-5xl font-semibold text-gray-900">{{$todayAppointmentsCount ?? 0}}</h1>
            </div>
            <p class="text-gray-600">
                {{ $todayCompletedCount ?? 0 }} completed,
                {{ $todayCancelledCount ?? 0 }} cancelled,
                {{ $todayUpcomingCount ?? 0 }} upcoming
            </p>
            </section>

            <section class="lg:col-span-1 bg-white p-6 rounded-lg shadow-md space-y-4 h-full">
            <div class="flex items-center justify-between">
                <p class="font-medium text-gray-700">Treatments Completed</p>
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0086da" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clipboard-check"><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="m9 14 2 2 4-4"/></svg>
            </div>
            <div>
                <h1 class="text-5xl font-semibold text-gray-900">{{ $weeklyCompletedCount ?? 0 }}</h1>
            </div>
            <p class="text-gray-600">+5 from last week</p>
            </section>

            <section class="lg:col-span-1 bg-[#0086da] text-white rounded-lg shadow-md p-6 flex flex-col justify-center items-center text-center h-full">
            <div class="text-2xl font-medium">Calendar</div>
            <div id="realtime-time" class="text-6xl lg:text-7xl font-extrabold my-2">
                Loading...
            </div>
            <div id="realtime-date" class="text-xl lg:text-2xl font-medium">
                </div>
            </section>

            <section class="lg:col-span-2 bg-white rounded-lg shadow-md flex flex-col">
                @livewire('today-schedule')
            </section>

            <section class="lg:col-span-1 relative flex flex-col bg-gray-100 rounded-lg shadow-md">
                @livewire('notes')
            </section>

            <!-- Weekly Appointments Chart -->
            <section class="lg:col-span-2 bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Weekly Appointments</h2>
                <div class="relative h-80">
                    <canvas id="weeklyAppointmentsChart"></canvas>
                </div>
            </section>

            <!-- Today's Status Distribution - Premium Analytics -->
            <section class="lg:col-span-1 bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-lg p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Today's Status</h2>
                        <p class="text-xs text-gray-500 mt-1">Real-time overview</p>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0086da" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pie-chart"><path d="M21.21 15.89H12V3.12a9 9 0 1 1-8.94 16.05A9.36 9.36 0 0 1 12 12v9.09"/></svg>
                    </div>
                </div>
                
                <div class="grid grid-cols-3 gap-2 mb-4">
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-3 border border-green-200">
                        <p class="text-xs font-medium text-green-700">Completed</p>
                        <p class="text-2xl font-bold text-green-900 mt-1" id="completedCount">0</p>
                        <p class="text-xs text-green-600" id="completedPercent">0%</p>
                    </div>
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-3 border border-blue-200">
                        <p class="text-xs font-medium text-blue-700">Scheduled</p>
                        <p class="text-2xl font-bold text-blue-900 mt-1" id="scheduledCount">0</p>
                        <p class="text-xs text-blue-600" id="scheduledPercent">0%</p>
                    </div>
                    <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg p-3 border border-red-200">
                        <p class="text-xs font-medium text-red-700">Cancelled</p>
                        <p class="text-2xl font-bold text-red-900 mt-1" id="cancelledCount">0</p>
                        <p class="text-xs text-red-600" id="cancelledPercent">0%</p>
                    </div>
                </div>

                <div class="relative h-64" data-completed="{{ $todayCompletedCount ?? 0 }}" data-scheduled="{{ $todayUpcomingCount ?? 0 }}" data-cancelled="{{ $todayCancelledCount ?? 0 }}">
                    <canvas id="appointmentStatusChart"></canvas>
                </div>
            </section>

            @php
                $user = auth()->user();
                $role = is_array($user) ? ($user['role'] ?? null) : ($user->role ?? null);
                $isAdmin = $role === 1
            @endphp
            @auth
                @if($isAdmin)
                    <section class="lg:col-span-3 bg-white rounded-lg shadow-md p-4">
                    <h1 class="text-2xl font-semibold text-gray-800 mb-4">Quick Actions</h1>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <a href="{{ route('admin.db.backup') }}" class="bg-[#ccebff] text-gray-800 font-semibold p-4 rounded-lg flex flex-col items-center gap-2 hover:bg-blue-200 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0086da" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-database-backup"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 12a9 3 0 0 0 5 2.69"/><path d="M21 9.3V5"/><path d="M3 5v14a9 3 0 0 0 6.47 2.88"/><path d="M12 12v4h4"/><path d="M13 20a5 5 0 0 0 9-3 4.5 4.5 0 0 0-4.5-4.5c-1.33 0-2.54.54-3.41 1.41L12 16"/></svg>
                            <span>Back up Data</span>
                        </a>
                    </div>
                    </section>
                @endif
            @endauth
        </div> 
    </main>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <script>
        // Real-time clock
        function updateLiveTime() {
            const timeElement = document.getElementById('realtime-time');
            const dateElement = document.getElementById('realtime-date');
            
            if (!timeElement || !dateElement) return;
            
            const now = new Date();
            
            let hours = now.getHours();
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12;
            const formattedTime = `${hours.toString().padStart(2, '0')}:${minutes} ${ampm}`;
            
            const formattedDate = now.toLocaleString('en-US', {
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            });
            
            timeElement.innerText = formattedTime;
            dateElement.innerText = formattedDate;
        }
        
        // Update immediately and then every second
        updateLiveTime();
        setInterval(updateLiveTime, 1000);
        
        // Wait for Chart.js to load
        document.addEventListener('DOMContentLoaded', function() {
            // Chart.js configuration with professional styling
            Chart.defaults.font.family = 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif';
            Chart.defaults.font.size = 12;
            Chart.defaults.color = '#6B7280';

            // Weekly Appointments Line Chart
            const weeklyCtx = document.getElementById('weeklyAppointmentsChart');
            if (weeklyCtx) {
                new Chart(weeklyCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                        datasets: [
                            {
                                label: 'Scheduled',
                                data: [12, 19, 15, 25, 22, 30, 10],
                                borderColor: '#0086da',
                                backgroundColor: 'rgba(0, 134, 218, 0.1)',
                                tension: 0.4,
                                fill: true,
                                borderWidth: 3,
                                pointRadius: 5,
                                pointBackgroundColor: '#0086da',
                                pointHoverRadius: 7
                            },
                            {
                                label: 'Completed',
                                data: [10, 18, 14, 23, 20, 28, 8],
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.4,
                                fill: true,
                                borderWidth: 3,
                                pointRadius: 5,
                                pointBackgroundColor: '#10b981',
                                pointHoverRadius: 7
                            },
                            {
                                label: 'Cancelled',
                                data: [2, 1, 1, 2, 2, 2, 2],
                                borderColor: '#f56565',
                                backgroundColor: 'rgba(245, 101, 101, 0.1)',
                                tension: 0.4,
                                fill: true,
                                borderWidth: 3,
                                pointRadius: 5,
                                pointBackgroundColor: '#f56565',
                                pointHoverRadius: 7
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    drawBorder: false,
                                    color: '#E5E7EB'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // Premium Today's Status Chart
            const statusCtx = document.getElementById('appointmentStatusChart');
            if (statusCtx) {
                const chartContainer = document.querySelector('[data-completed]');
                
                // Today's appointment data from Laravel backend
                const todayAppointmentData = {
                    completed: parseInt(chartContainer.getAttribute('data-completed')) || 0,
                    scheduled: parseInt(chartContainer.getAttribute('data-scheduled')) || 0,
                    cancelled: parseInt(chartContainer.getAttribute('data-cancelled')) || 0
                };

                // Calculate total and percentages
                const todayTotal = todayAppointmentData.completed + todayAppointmentData.scheduled + todayAppointmentData.cancelled;
                const todayCompletedPercent = todayTotal > 0 ? ((todayAppointmentData.completed / todayTotal) * 100).toFixed(1) : 0;
                const todayScheduledPercent = todayTotal > 0 ? ((todayAppointmentData.scheduled / todayTotal) * 100).toFixed(1) : 0;
                const todayCancelledPercent = todayTotal > 0 ? ((todayAppointmentData.cancelled / todayTotal) * 100).toFixed(1) : 0;

                // Update dashboard metrics with today's data
                const completedCountEl = document.getElementById('completedCount');
                const completedPercentEl = document.getElementById('completedPercent');
                const scheduledCountEl = document.getElementById('scheduledCount');
                const scheduledPercentEl = document.getElementById('scheduledPercent');
                const cancelledCountEl = document.getElementById('cancelledCount');
                const cancelledPercentEl = document.getElementById('cancelledPercent');

                if (completedCountEl) completedCountEl.textContent = todayAppointmentData.completed;
                if (completedPercentEl) completedPercentEl.textContent = todayCompletedPercent + '%';
                if (scheduledCountEl) scheduledCountEl.textContent = todayAppointmentData.scheduled;
                if (scheduledPercentEl) scheduledPercentEl.textContent = todayScheduledPercent + '%';
                if (cancelledCountEl) cancelledCountEl.textContent = todayAppointmentData.cancelled;
                if (cancelledPercentEl) cancelledPercentEl.textContent = todayCancelledPercent + '%';

                new Chart(statusCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Completed', 'Scheduled', 'Cancelled'],
                        datasets: [{
                            data: [todayAppointmentData.completed, todayAppointmentData.scheduled, todayAppointmentData.cancelled],
                            backgroundColor: [
                                '#10b981',
                                '#0086da',
                                '#f56565'
                            ],
                            borderColor: '#fff',
                            borderWidth: 3,
                            hoverBorderWidth: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                    font: {
                                        size: 13,
                                        weight: 'bold'
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                                        return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush

