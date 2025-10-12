<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        @vite('resources/css/app.css')

        <style>
            .sidebar { width: 16rem; transition: width 0.28s ease; }
            .sidebar.collapsed { width: 4rem; }

            /* Main content moves based on body class */
            #mainContent { margin-left: 16rem; padding-top: 3.5rem; transition: margin-left 0.28s ease; }
            body.sidebar-collapsed #mainContent { margin-left: 4rem; }

            /* small helpers for collapsed state */
            .nav-text { transition: opacity 0.18s ease; }
            .sidebar.collapsed .nav-text { opacity: 0; display: none; }
            .sidebar.collapsed .nav-item { justify-content: center; gap: 0; }
            .sidebar:not(.collapsed) .tooltip { display: none; }

            .sidebar.collapsed .tooltip { opacity: 0; transform: scale(0.95); transition: opacity 0.2s ease, transform 0.2s ease; }
            .sidebar.collapsed .group:hover .tooltip { opacity: 1; transform: scale(1); }
        </style>
    </head>
</html>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200 h-14 flex items-center px-4 fixed top-0 left-0 right-0 z-10">
        <button id="toggleBtn" class="p-2 hover:bg-gray-100 rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <h2 class="ml-4 text-lg font-semibold text-gray-900">Tejada Dent</h2>
    </header>

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar bg-white border-r border-gray-200 fixed left-0 top-14 bottom-0 overflow-hidden">
        <nav class="mt-10">
            <ul class="space-y-3">
                <li>
                    <a href="dashboard"
                        class="nav-item active flex items-center gap-5 px-3 py-2 rounded-md transition-colors relative group">
                        
                        <!-- Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" 
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                            class="flex-shrink-0 lucide lucide-calendar">
                            <path d="M8 2v4"/><path d="M16 2v4"/>
                            <rect width="18" height="18" x="3" y="4" rx="2"/>
                            <path d="M3 10h18"/>
                        </svg>
                        <span class="nav-text whitespace-nowrap">Dashboard</span>

                    </a>
                </li>
                <li>
                    <a href="#" class="nav-item flex items-center gap-5 px-3 py-2 rounded-md transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0 lucide lucide-clock">
                            <path d="M12 6v6l4 2"/><circle cx="12" cy="12" r="10"/>
                        </svg>
                        <span class="nav-text whitespace-nowrap">Appointments</span>
                        <span class="tooltip absolute left-full ml-3 top-1/2 -translate-y-1/2 bg-gray-800 text-white text-sm rounded px-2 py-1 z-20 shadow-md">
                            Appointments
                        </span>
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-item flex items-center gap-5 px-3 py-2 rounded-md transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0 lucide lucide-file-text">
                            <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/>
                        </svg>
                        <span class="nav-text whitespace-nowrap">Patient Records</span>
                        <span class="tooltip absolute left-full ml-3 top-1/2 -translate-y-1/2 bg-gray-800 text-white text-sm rounded px-2 py-1 z-20 shadow-md">
                            Patient Records
                        </span>
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-item flex items-center gap-5 px-3 py-2 rounded-md transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0 lucide lucide-chart-line">
                            <path d="M3 3v16a2 2 0 0 0 2 2h16"/><path d="m19 9-5 5-4-4-3 3"/>
                        </svg>
                        <span class="nav-text whitespace-nowrap">Charts</span>
                        <span class="tooltip absolute left-full ml-3 top-1/2 -translate-y-1/2 bg-gray-800 text-white text-sm rounded px-2 py-1 z-20 shadow-md">
                            Charts
                        </span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <script>
        (function(){
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggleBtn');

            if (!sidebar || !toggleBtn) return;

            // Set initial state consistency
            if (document.body.classList.contains('sidebar-collapsed')) {
                sidebar.classList.add('collapsed');
            }

            toggleBtn.addEventListener('click', function () {
                sidebar.classList.toggle('collapsed');
                document.body.classList.toggle('sidebar-collapsed');
            });

            // nav item active handling (defensive)
            const navItems = document.querySelectorAll('.nav-item');
            navItems.forEach(item => {
                item.addEventListener('click', function () {
                    navItems.forEach(nav => nav.classList.remove('active', 'bg-indigo-100', 'text-indigo-700'));
                    this.classList.add('active', 'bg-indigo-100', 'text-indigo-700');
                });
            });

            // set initial active styling if present
            const active = document.querySelector('.nav-item.active');
            if (active) active.classList.add('bg-indigo-100', 'text-indigo-700');
        })();
    </script>
</body>
</html>