<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Tejada Clinic</title>
    @vite('resources/css/app.css')
    @livewireStyles

</head>
<body class=" tracking-wide">
    <header class="bg-white border-b border-gray-200 h-14 flex items-center px-4 fixed top-0 left-0 right-0 z-10">
        <button id="toggleBtn" class="p-2 hover:bg-gray-100 rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <h2 class="ml-4 text-lg font-semibold text-gray-900">Tejada Dent</h2>
    </header>

    
    <aside id="sidebar"
        class="peer sidebar bg-white border-r border-gray-200 fixed left-0 top-14 bottom-0 overflow-hidden transition-all duration-300 w-64
            flex flex-col
            [&.collapsed]:w-16 group">
        <nav class="mt-10 w-full">
            <ul class="space-y-3 w-full">
                <li>
                    <a href="dashboard"
                        class="{{ request()->is('dashboard') ? 'active' : '' }}
                            nav-item flex items-center gap-5 px-3 py-2 relative w-full
                            transition-all duration-300
                            text-gray-700 hover:bg-gray-100 <!-- <== FIXED: Added default text color and hover -->
                            [&.active]:bg-[#0086DA] [&.active]:text-white
                            group-[.collapsed]:px-5 group-[.collapsed]:gap-0">

                        <span class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-calendar">
                                <path d="M8 2v4"/><path d="M16 2v4"/>
                                <rect width="18" height="18" x="3" y="4" rx="2"/>
                                <path d="M3 10h18"/>
                            </svg>
                        </span>

                        <span class="nav-text whitespace-nowrap text-xl overflow-hidden
                            transition-all duration-300
                            group-[.collapsed]:w-0 group-[.collapsed]:opacity-0">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="appointment"
                        class="{{ request()->is('appointment') ? 'active' : '' }}
                            nav-item flex items-center gap-5 px-3 py-2 relative w-full
                            transition-all duration-300
                            text-gray-700 hover:bg-gray-100 <!-- <== FIXED: Added default text color and hover -->
                            [&.active]:bg-[#0086DA] [&.active]:text-white
                            group-[.collapsed]:px-5 group-[.collapsed]:gap-0">
                        <span class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-clock">
                                <path d="M12 6v6l4 2"/><circle cx="12" cy="12" r="10"/>
                            </svg>
                        </span>
                        <span class="nav-text whitespace-nowrap text-xl overflow-hidden transition-all duration-300 group-[.collapsed]:w-0 group-[.collapsed]:opacity-0">Appointments</span>
                    </a>
                </li>
                <li>
                    <a href="#"
                        class="nav-item flex items-center gap-5 px-3 py-2 relative w-full
                            transition-all duration-300
                            text-gray-700 hover:bg-gray-100 <!-- <== FIXED: Added default text color and hover -->
                            group-[.collapsed]:px-5 group-[.collapsed]:gap-0">
                        <span class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-file-text">
                                <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/>
                            </svg>
                        </span>
                        <span class="nav-text whitespace-nowrap text-xl overflow-hidden transition-all duration-300 group-[.collapsed]:w-0 group-[.collapsed]:opacity-0">Patient Records</span>
                    </a>
                </li>
                <li>
                    <a href="#"
                        class="nav-item flex items-center gap-5 px-3 py-2 relative w-full
                            transition-all duration-300
                            text-gray-700 hover:bg-gray-100 <!-- <== FIXED: Added default text color and hover -->
                            group-[.collapsed]:px-5 group-[.collapsed]:gap-0">
                        <span class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-chart-line">
                                <path d="M3 3v16a2 2 0 0 0 2 2h16"/><path d="m19 9-5 5-4-4-3 3"/>
                            </svg>
                        </span>
                        <span class="nav-text whitespace-nowrap text-xl overflow-hidden transition-all duration-300 group-[.collapsed]:w-0 group-[.collapsed]:opacity-0">Reports</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    @yield("content")

    @stack('script')
    <script>
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
    </script>
    @livewireScripts
</body>
</html>