<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Tejada Clinic</title>
    @vite('resources/css/app.css')
    <style>
        @yield("style");
    </style>
    @livewireStyles

</head>
<body class=" tracking-wide">
    <header class="bg-white border-b border-gray-200 h-14 flex items-center justify-between px-4 fixed top-0 left-0 right-0 z-50">
    <div class="flex items-center">
        <button id="toggleBtn" class="p-2 hover:bg-gray-100 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu-icon lucide-menu"><path d="M4 5h16"/><path d="M4 12h16"/><path d="M4 19h16"/></svg>        </button>
        <h2 class="ml-4 text-lg font-semibold text-gray-900">Tejada Dent</h2>
    </div>

    <div class="flex items-center gap-3">
        @livewire('components.notification-bell')
        
        <a href="{{ route('profile.index') }}" 
        class="{{ request()->routeIs('profile.index') ? 'bg-gray-100' : '' }} 
                flex items-center gap-2 px-2 py-1 hover:bg-gray-100 rounded-lg transition-all duration-300 group">
            
            <div class="flex-shrink-0 text-gray-400 group-hover:text-[#0086DA] transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-user-icon">
                    <circle cx="12" cy="12" r="10"/>
                    <circle cx="12" cy="10" r="3"/>
                    <path d="M7 20.662V19a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v1.662"/>
                </svg>
            </div>

            <div class="hidden md:flex flex-col items-start">
                <span class="text-sm font-medium text-gray-700 group-hover:text-[#0086DA] leading-tight">
                    {{ auth()->user()->username }}
                </span>
                <span class="text-[10px] text-gray-500 leading-tight">
                    {{ auth()->user()->role == 1 ? 'Admin' : (auth()->user()->role == 2 ? 'Staff' : 'User') }}
                </span>
            </div>
        </a>

        <div class="h-6 w-px bg-gray-200 mx-1"></div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="nav-item flex items-center gap-2 px-2 py-2 relative transition-all duration-300 text-[#f56565] hover:bg-red-50 hover:text-red-600 rounded-lg" title="Logout">  
                <span class="flex items-center justify-center w-5 h-5 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-out">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/>
                    </svg>
                </span>
                <span class="hidden md:inline text-sm font-medium">Logout</span>
            </button>
        </form>
    </div>
</header>

    
    <aside id="sidebar" class=" peer sidebar bg-white border-r border-gray-200 fixed left-0 top-12 bottom-0 overflow-hidden transition-all duration-300 w-64 flex flex-col [&.collapsed]:w-16 group">
        <nav class="w-full h-full flex flex-col py-10">
            <ul class="space-y-3 w-full h-full">
                <li>
                    <a href="{{route('dashboard')}}"
                        class="{{ request()->is('dashboard') ? 'active' : '' }}
                            nav-item flex items-center gap-5 px-3 py-2 relative w-full
                            transition-all duration-300
                            text-gray-700 hover:bg-gray-100
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
                            group-[.collapsed]:w-0 group-[.collapsed]:opacity-0">Dashboard
                        </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('appointment')}}"
                        class="{{ request()->is('appointment') ? 'active' : '' }}
                            nav-item flex items-center gap-5 px-3 py-2 relative w-full
                            transition-all duration-300
                            text-gray-700 hover:bg-gray-100
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
                    <a href="{{ route('patient-records') }}"
                        class="{{ request()->is('patient-records') ? 'active' : '' }} 
                            nav-item flex items-center gap-5 px-3 py-2 relative w-full
                            transition-all duration-300
                            text-gray-700 hover:bg-gray-100 
                            [&.active]:bg-[#0086DA] [&.active]:text-white
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
                @php
                    $user = auth()->user();
                    $role = is_array($user) ? ($user['role'] ?? null) : ($user->role ?? null);
                    $isAdmin = $role === 1
                @endphp
                @auth
                    @if($isAdmin)
                        <li>
                            <a href="{{ route('reports.index') }}"
                                class="{{ request()->routeIs('reports.*') ? 'active' : '' }}
                                    nav-item flex items-center gap-5 px-3 py-2 relative w-full
                                    transition-all duration-300
                                    text-gray-700 hover:bg-gray-100
                                    [&.active]:bg-[#0086DA] [&.active]:text-white
                                    group-[.collapsed]:px-5 group-[.collapsed]:gap-0">
                                <span class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-chart-line">
                                        <path d="M3 3v16a2 2 0 0 0 2 2h16"/><path d="m19 9-5 5-4-4-3 3"/>
                                    </svg>
                                </span>
                                <span class="nav-text whitespace-nowrap text-xl overflow-hidden transition-all duration-300 group-[.collapsed]:w-0 group-[.collapsed]:opacity-0">
                                    Reports
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('users.index') }}"
                                class="{{ request()->routeIs('users.*') ? 'active' : '' }}
                                    nav-item flex items-center gap-5 px-3 py-2 relative w-full
                                    transition-all duration-300
                                    text-gray-700 hover:bg-gray-100
                                    [&.active]:bg-[#0086DA] [&.active]:text-white
                                    group-[.collapsed]:px-5 group-[.collapsed]:gap-0">
                                <span class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-round-icon lucide-user-round"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>
                                </span>
                                <span class="nav-text whitespace-nowrap text-xl overflow-hidden transition-all duration-300 group-[.collapsed]:w-0 group-[.collapsed]:opacity-0">
                                    User Accounts
                                </span>
                            </a>
                        </li>
                    @endif
                @endauth
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