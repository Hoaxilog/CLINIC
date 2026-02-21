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
<body class="tracking-wide">
    <header class="bg-white border-b border-gray-200 h-14 flex items-center justify-between px-4 fixed top-0 left-0 right-0 z-50">
    <div class="flex items-center">
        <button id="toggleBtn" class="p-2 hover:bg-gray-100 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M2 12C2 8.31087 2 6.4663 2.81382 5.15877C3.1149 4.67502 3.48891 4.25427 3.91891 3.91554C5.08116 3 6.72077 3 10 3H14C17.2792 3 18.9188 3 20.0811 3.91554C20.5111 4.25427 20.8851 4.67502 21.1862 5.15877C22 6.4663 22 8.31087 22 12C22 15.6891 22 17.5337 21.1862 18.8412C20.8851 19.325 20.5111 19.7457 20.0811 20.0845C18.9188 21 17.2792 21 14 21H10C6.72077 21 5.08116 21 3.91891 20.0845C3.48891 19.7457 3.1149 19.325 2.81382 18.8412C2 17.5337 2 15.6891 2 12Z" />
                <path d="M9.5 3L9.5 21" />
                <path d="M5 7H6M5 10H6" />
            </svg>        
        </button>
        <h2 class="ml-4 text-lg font-semibold text-gray-900">Tejada Dent</h2>
    </div>

    <div class="flex items-center gap-3 ">
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
                    {{ auth()->user()->role == 1 ? 'Admin' : (auth()->user()->role == 2 ? 'Staff' : 'Patient') }}
                </span>
            </div>
        </a>

    </div>
</header>

    
    <aside id="sidebar" class=" peer sidebar bg-white border-r border-gray-200 fixed left-0 top-12 bottom-0 overflow-hidden transition-all duration-300 w-64 flex flex-col [&.collapsed]:w-16 group">
        <nav class="w-full h-full flex flex-col py-10">
            <ul class="space-y-3 w-full h-full">
                @if (auth()->user()->role === 3)
                    <li>
                        <a href="{{ route('patient.dashboard') }}"
                            class="{{ request()->routeIs('patient.dashboard') ? 'active' : '' }}
                                nav-item flex items-center gap-5 px-3 py-2 relative w-full
                                transition-all duration-300
                                text-gray-700 hover:bg-gray-100
                                [&.active]:bg-[#0086DA] [&.active]:text-white
                                group-[.collapsed]:px-5 group-[.collapsed]:gap-0">

                            <span class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round">
                                    <path d="M10.5 8.75V6.75C10.5 5.10626 10.5 4.28439 10.046 3.73121C9.96291 3.62995 9.87005 3.53709 9.76879 3.45398C9.21561 3 8.39374 3 6.75 3C5.10626 3 4.28439 3 3.73121 3.45398C3.62995 3.53709 3.53709 3.62995 3.45398 3.73121C3 4.28439 3 5.10626 3 6.75V8.75C3 10.3937 3 11.2156 3.45398 11.7688C3.53709 11.8701 3.62995 11.9629 3.73121 12.046C4.28439 12.5 5.10626 12.5 6.75 12.5C8.39374 12.5 9.21561 12.5 9.76879 12.046C9.87005 11.9629 9.96291 11.8701 10.046 11.7688C10.5 11.2156 10.5 10.3937 10.5 8.75Z" />
                                    <path d="M7.75 15.5H5.75C5.05222 15.5 4.70333 15.5 4.41943 15.5861C3.78023 15.78 3.28002 16.2802 3.08612 16.9194C3 17.2033 3 17.5522 3 18.25C3 18.9478 3 19.2967 3.08612 19.5806C3.28002 20.2198 3.78023 20.72 4.41943 20.9139C4.70333 21 5.05222 21 5.75 21H7.75C8.44778 21 8.79667 21 9.08057 20.9139C9.71977 20.72 10.22 20.2198 10.4139 19.5806C10.5 19.2967 10.5 18.9478 10.5 18.25C10.5 17.5522 10.5 17.2033 10.4139 16.9194C10.22 16.2802 9.71977 15.78 9.08057 15.5861C8.79667 15.5 8.44778 15.5 7.75 15.5Z" />
                                    <path d="M21 17.25V15.25C21 13.6063 21 12.7844 20.546 12.2312C20.4629 12.1299 20.3701 12.0371 20.2688 11.954C19.7156 11.5 18.8937 11.5 17.25 11.5C15.6063 11.5 14.7844 11.5 14.2312 11.954C14.1299 12.0371 14.0371 12.1299 13.954 12.2312C13.5 12.7844 13.5 13.6063 13.5 15.25V17.25C13.5 18.8937 13.5 19.7156 13.954 20.2688C14.0371 20.3701 14.1299 20.4629 14.2312 20.546C14.7844 21 15.6063 21 17.25 21C18.8937 21 19.7156 21 20.2688 20.546C20.3701 20.4629 20.4629 20.3701 20.546 20.2688C21 19.7156 21 18.8937 21 17.25Z" />
                                    <path d="M18.25 3H16.25C15.5522 3 15.2033 3 14.9194 3.08612C14.2802 3.28002 13.78 3.78023 13.5861 4.41943C13.5 4.70333 13.5 5.05222 13.5 5.75C13.5 6.44778 13.5 6.79667 13.5861 7.08057C13.78 7.71977 14.2802 8.21998 14.9194 8.41388C15.2033 8.5 15.5522 8.5 16.25 8.5H18.25C18.9478 8.5 19.2967 8.5 19.5806 8.41388C20.2198 8.21998 20.72 7.71977 20.9139 7.08057C21 6.79667 21 6.44778 21 5.75C21 5.05222 21 4.70333 20.9139 4.41943C20.72 3.78023 20.2198 3.28002 19.5806 3.08612C19.2967 3 18.9478 3 18.25 3Z" />
                                </svg>
                            </span>

                            <span class="nav-text whitespace-nowrap text-xl overflow-hidden
                                transition-all duration-300
                                group-[.collapsed]:w-0 group-[.collapsed]:opacity-0">My Dashboard
                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('book') }}"
                            class="{{ request()->routeIs('book') ? 'active' : '' }}
                                nav-item flex items-center gap-5 px-3 py-2 relative w-full
                                transition-all duration-300
                                text-gray-700 hover:bg-gray-100
                                [&.active]:bg-[#0086DA] [&.active]:text-white
                                group-[.collapsed]:px-5 group-[.collapsed]:gap-0">
                            <span class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 2V6M8 2V6" />
                                    <path d="M13 4H11C7.22876 4 5.34315 4 4.17157 5.17157C3 6.34315 3 8.22876 3 12V14C3 17.7712 3 19.6569 4.17157 20.8284C5.34315 22 7.22876 22 11 22H13C16.7712 22 18.6569 22 19.8284 20.8284C21 19.6569 21 17.7712 21 14V12C21 8.22876 21 6.34315 19.8284 5.17157C18.6569 4 16.7712 4 13 4Z" />
                                    <path d="M3 10H21" />
                                    <path d="M9 16.5C9 16.5 10.5 17 11 18.5C11 18.5 13.1765 14.5 16 13.5" />
                                </svg>
                            </span>
                            <span class="nav-text whitespace-nowrap text-xl overflow-hidden transition-all duration-300 group-[.collapsed]:w-0 group-[.collapsed]:opacity-0">Book Appointment</span>
                        </a>
                    </li>
                @else
                <li>
                    <a href="{{route('dashboard')}}"
                        class="{{ request()->is('dashboard') ? 'active' : '' }}
                            nav-item flex items-center gap-5 px-3 py-2 relative w-full
                            transition-all duration-300
                            text-gray-700 hover:bg-gray-100
                            [&.active]:bg-[#0086DA] [&.active]:text-white
                            group-[.collapsed]:px-5 group-[.collapsed]:gap-0">

                        <span class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round">
                                <path d="M10.5 8.75V6.75C10.5 5.10626 10.5 4.28439 10.046 3.73121C9.96291 3.62995 9.87005 3.53709 9.76879 3.45398C9.21561 3 8.39374 3 6.75 3C5.10626 3 4.28439 3 3.73121 3.45398C3.62995 3.53709 3.53709 3.62995 3.45398 3.73121C3 4.28439 3 5.10626 3 6.75V8.75C3 10.3937 3 11.2156 3.45398 11.7688C3.53709 11.8701 3.62995 11.9629 3.73121 12.046C4.28439 12.5 5.10626 12.5 6.75 12.5C8.39374 12.5 9.21561 12.5 9.76879 12.046C9.87005 11.9629 9.96291 11.8701 10.046 11.7688C10.5 11.2156 10.5 10.3937 10.5 8.75Z" />
                                <path d="M7.75 15.5H5.75C5.05222 15.5 4.70333 15.5 4.41943 15.5861C3.78023 15.78 3.28002 16.2802 3.08612 16.9194C3 17.2033 3 17.5522 3 18.25C3 18.9478 3 19.2967 3.08612 19.5806C3.28002 20.2198 3.78023 20.72 4.41943 20.9139C4.70333 21 5.05222 21 5.75 21H7.75C8.44778 21 8.79667 21 9.08057 20.9139C9.71977 20.72 10.22 20.2198 10.4139 19.5806C10.5 19.2967 10.5 18.9478 10.5 18.25C10.5 17.5522 10.5 17.2033 10.4139 16.9194C10.22 16.2802 9.71977 15.78 9.08057 15.5861C8.79667 15.5 8.44778 15.5 7.75 15.5Z" />
                                <path d="M21 17.25V15.25C21 13.6063 21 12.7844 20.546 12.2312C20.4629 12.1299 20.3701 12.0371 20.2688 11.954C19.7156 11.5 18.8937 11.5 17.25 11.5C15.6063 11.5 14.7844 11.5 14.2312 11.954C14.1299 12.0371 14.0371 12.1299 13.954 12.2312C13.5 12.7844 13.5 13.6063 13.5 15.25V17.25C13.5 18.8937 13.5 19.7156 13.954 20.2688C14.0371 20.3701 14.1299 20.4629 14.2312 20.546C14.7844 21 15.6063 21 17.25 21C18.8937 21 19.7156 21 20.2688 20.546C20.3701 20.4629 20.4629 20.3701 20.546 20.2688C21 19.7156 21 18.8937 21 17.25Z" />
                                <path d="M18.25 3H16.25C15.5522 3 15.2033 3 14.9194 3.08612C14.2802 3.28002 13.78 3.78023 13.5861 4.41943C13.5 4.70333 13.5 5.05222 13.5 5.75C13.5 6.44778 13.5 6.79667 13.5861 7.08057C13.78 7.71977 14.2802 8.21998 14.9194 8.41388C15.2033 8.5 15.5522 8.5 16.25 8.5H18.25C18.9478 8.5 19.2967 8.5 19.5806 8.41388C20.2198 8.21998 20.72 7.71977 20.9139 7.08057C21 6.79667 21 6.44778 21 5.75C21 5.05222 21 4.70333 20.9139 4.41943C20.72 3.78023 20.2198 3.28002 19.5806 3.08612C19.2967 3 18.9478 3 18.25 3Z" />
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
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 2V6M8 2V6" />
                                <path d="M13 4H11C7.22876 4 5.34315 4 4.17157 5.17157C3 6.34315 3 8.22876 3 12V14C3 17.7712 3 19.6569 4.17157 20.8284C5.34315 22 7.22876 22 11 22H13C16.7712 22 18.6569 22 19.8284 20.8284C21 19.6569 21 17.7712 21 14V12C21 8.22876 21 6.34315 19.8284 5.17157C18.6569 4 16.7712 4 13 4Z" />
                                <path d="M3 10H21" />
                                <path d="M9 16.5C9 16.5 10.5 17 11 18.5C11 18.5 13.1765 14.5 16 13.5" />
                            </svg>
                        </span>
                        <span class="nav-text whitespace-nowrap text-xl overflow-hidden transition-all duration-300 group-[.collapsed]:w-0 group-[.collapsed]:opacity-0">Appointments</span>
                    </a>
                </li>
                @if (auth()->user()->role !== 3)
                    <li>
                        <a href="{{ route('queue') }}"
                            class="{{ request()->is('queue') ? 'active' : '' }}
                                nav-item flex items-center gap-5 px-3 py-2 relative w-full
                                transition-all duration-300
                                text-gray-700 hover:bg-gray-100
                                [&.active]:bg-[#0086DA] [&.active]:text-white
                                group-[.collapsed]:px-5 group-[.collapsed]:gap-0">
                            <span class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 7H21" />
                                    <path d="M3 12H21" />
                                    <path d="M3 17H21" />
                                </svg>
                            </span>
                            <span class="nav-text whitespace-nowrap text-xl overflow-hidden transition-all duration-300 group-[.collapsed]:w-0 group-[.collapsed]:opacity-0">Queue</span>
                        </a>
                    </li>
                @endif
                <li>
                    <a href="{{ route('patient-records') }}"
                        class="{{ request()->is('patient-records') ? 'active' : '' }} 
                            nav-item flex items-center gap-5 px-3 py-2 relative w-full
                            transition-all duration-300
                            text-gray-700 hover:bg-gray-100 
                            [&.active]:bg-[#0086DA] [&.active]:text-white
                            group-[.collapsed]:px-5 group-[.collapsed]:gap-0">
                        <span class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M22 14V10C22 6.22876 22 4.34315 20.8284 3.17157C19.6569 2 17.7712 2 14 2H12C8.22876 2 6.34315 2 5.17157 3.17157C4 4.34315 4 6.22876 4 10V14C4 17.7712 4 19.6569 5.17157 20.8284C6.34315 22 8.22876 22 12 22H14C17.7712 22 19.6569 22 20.8284 20.8284C22 19.6569 22 17.7712 22 14Z" />
                                <path d="M5 6L2 6M5 12H2M5 18H2" />
                                <path d="M17.5 7L13.5 7M15.5 11H13.5" />
                                <path d="M9 22L9 2" />
                            </svg>
                        </span>
                        <span class="nav-text whitespace-nowrap text-xl overflow-hidden transition-all duration-300 group-[.collapsed]:w-0 group-[.collapsed]:opacity-0">Patient Records</span>
                    </a>
                </li>
                @if (auth()->user()->role === 1)
                    <li>
                        <a href="{{ route('reports.index') }}"
                            class="{{ request()->routeIs('reports.*') ? 'active' : '' }}
                                nav-item flex items-center gap-5 px-3 py-2 relative w-full
                                transition-all duration-300
                                text-gray-700 hover:bg-gray-100
                                [&.active]:bg-[#0086DA] [&.active]:text-white
                                group-[.collapsed]:px-5 group-[.collapsed]:gap-0">
                            <span class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M7 18V16M12 18V15M17 18V13M2.5 12C2.5 7.52166 2.5 5.28249 3.89124 3.89124C5.28249 2.5 7.52166 2.5 12 2.5C16.4783 2.5 18.7175 2.5 20.1088 3.89124C21.5 5.28249 21.5 7.52166 21.5 12C21.5 16.4783 21.5 18.7175 20.1088 20.1088C18.7175 21.5 16.4783 21.5 12 21.5C7.52166 21.5 5.28249 21.5 3.89124 20.1088C2.5 18.7175 2.5 16.4783 2.5 12Z" />
                                    <path d="M5.99219 11.4863C8.14729 11.5581 13.0341 11.2328 15.8137 6.82132M13.9923 6.28835L15.8678 5.98649C16.0964 5.95738 16.432 6.13785 16.5145 6.35298L17.0104 7.99142" />
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
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 12C2 8.22876 2 6.34315 3.17157 5.17157C4.34315 4 6.22876 4 10 4H14C17.7712 4 19.6569 4 20.8284 5.17157C22 6.34315 22 8.22876 22 12C22 15.7712 22 17.6569 20.8284 18.8284C19.6569 20 17.7712 20 14 20H10C6.22876 20 4.34315 20 3.17157 18.8284C2 17.6569 2 15.7712 2 12Z" />
                                    <path d="M9 12.5C7.61929 12.5 6.5 11.3807 6.5 10C6.5 8.61929 7.61929 7.5 9 7.5C10.3807 7.5 11.5 8.61929 11.5 10C11.5 11.3807 10.3807 12.5 9 12.5ZM9 12.5C11.2091 12.5 13 14.2909 13 16.5M9 12.5C6.79086 12.5 5 14.2909 5 16.5" />
                                    <path d="M15 9H19" />
                                    <path d="M15 12H19" />
                                </svg>                                
                            </span>
                            <span class="nav-text whitespace-nowrap text-xl overflow-hidden transition-all duration-300 group-[.collapsed]:w-0 group-[.collapsed]:opacity-0">
                                User Accounts
                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('activity-logs') }}"
                            class="{{ request()->is('activity-logs') ? 'active' : '' }} 
                                nav-item flex items-center gap-5 px-3 py-2 relative w-full
                            transition-all duration-300
                                text-gray-700 hover:bg-gray-100 
                                [&.active]:bg-[#0086DA] [&.active]:text-white 
                                group-[.collapsed]:px-5 group-[.collapsed]:gap-0">
                            <span class="flex items-center justify-center w-6 h-6 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 10.5V9.99995C19 6.22876 18.9999 4.34311 17.8284 3.17154C16.6568 2 14.7712 2 11 2C7.22889 2 5.34326 2.00006 4.17169 3.17159C3.00015 4.34315 3.00013 6.22872 3.0001 9.99988L3.00006 14.5C3.00003 17.7874 3.00002 19.4312 3.90794 20.5375C4.07418 20.7401 4.25992 20.9258 4.46249 21.0921C5.56883 22 7.21255 22 10.5 22" />
                                    <path d="M7 7H15M7 11H11" />
                                    <path d="M18 18.5L16.5 17.95V15.5M12 17.5C12 19.9853 14.0147 22 16.5 22C18.9853 22 21 19.9853 21 17.5C21 15.0147 18.9853 13 16.5 13C14.0147 13 12 15.0147 12 17.5Z" />
                                </svg>
                            </span>
                            <span class="nav-text whitespace-nowrap text-xl overflow-hidden transition-all duration-300 group-[.collapsed]:w-0 group-[.collapsed]:opacity-0">Activity Logs</span>
                        </a>
                    </li>
                @endif

                @endif
            </ul>
            <form action="{{ route('logout') }}" method="POST" class="px-3">
            @csrf

            <button
                type="submit"
                class="nav-item w-full flex items-center justify-center gap-5
                    px-3 py-2 rounded-lg
                    transition-all duration-300
                    text-[#f56565] hover:bg-red-50 hover:text-red-600
                    group-[.collapsed]:gap-0"
            >
                <span class="w-6 h-6 flex-shrink-0 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                        width="24" height="24" fill="none" stroke="currentColor"
                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 18C18 18.4644 18 18.6965 17.978 18.8918
                                C17.7952 20.5145 16.5145 21.7952 14.8919 21.978
                                C14.6965 22 14.4644 22 14 22H11
                                C7.70017 22 6.05025 22 5.02513 20.9749
                                C4 19.9498 4 18.2998 4 15V9
                                C4 5.70016 4 4.05024 5.02514 3.02512
                                C6.05027 2 7.70018 2 11 2H14" />
                        <path d="M8 12H20M20 12L16.5 8.5M20 12L16.5 15.5" />
                    </svg>
                </span>

                <span
                    class="nav-text whitespace-nowrap text-xl font-medium overflow-hidden
                        transition-all duration-300
                        group-[.collapsed]:w-0
                        group-[.collapsed]:opacity-0"
                >
                    Logout
                </span>
            </button>
        </form>

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
