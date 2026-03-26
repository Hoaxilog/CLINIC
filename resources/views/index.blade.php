<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Tejada Clinic</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    @vite('resources/css/app.css')

    <style>
        @yield("style");
    </style>
    @livewireStyles

</head>

<body class="tracking-wide">
    @php
        $authUser = auth()->user();
        $isPatient = $authUser?->isPatient() ?? false;
        $isAdmin = $authUser?->isAdmin() ?? false;
        $isPatientBookingPage = request()->routeIs('book');
        $isPatientDashboardPage = request()->routeIs('patient.dashboard');
    @endphp

    @if ($isPatient)
        <header class="sticky top-0 z-[100] border-b border-[#e4eff8] bg-white px-4 sm:px-6 md:px-12 xl:px-20">
            <div class="relative mx-auto flex h-[70px] w-full max-w-[1400px] items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-3 no-underline">
                    <div class="flex h-[38px] w-[38px] shrink-0 items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-dashboard-icon lucide-layout-dashboard"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                    </div>
                    <div class="leading-[1.25]">
                        <div class="text-[.92rem] font-extrabold tracking-[.04em] text-[#1a2e3b]">TEJADA CLINIC</div>
                        <div class="text-[.57rem] font-semibold uppercase tracking-[.2em] text-[#0086da]">Dental Care</div>
                    </div>
                </a>

                <nav class="hidden items-center gap-9 lg:flex">
                    <a href="{{ route('home') }}"
                        class="group relative text-[.72rem] font-semibold uppercase tracking-[.07em] transition-colors duration-200 {{ $isPatientDashboardPage ? 'text-[#0086DA]' : 'text-[#1a2e3b] hover:text-[#0086da]' }}">
                        Home
                        <span class="absolute -bottom-1 left-0 h-0.5 bg-[#0086da] transition-all duration-200 {{ $isPatientDashboardPage ? 'w-full' : 'w-0 group-hover:w-full' }}"></span>
                    </a>
                </nav>

                <div class="flex items-center gap-2 sm:gap-3">
                    @if (!$isPatientBookingPage)
                        <a href="{{ route('book') }}"
                            class="hidden items-center gap-[9px] whitespace-nowrap bg-[#0086da] px-5 py-3 text-[.7rem] font-bold uppercase tracking-[.1em] text-white transition duration-200 hover:-translate-y-px hover:bg-[#006ab0] md:inline-flex">
                            Book Appointment
                        </a>
                    @endif

                    @livewire('shared.notification-bell')

                    <details class="relative">
                        <summary
                            class="list-none cursor-pointer inline-flex h-11 w-11 items-center justify-center rounded-full border border-[#cde8f9] bg-[#eff8fe] text-[#0086da] transition duration-200 hover:border-[#7ec4ef] hover:bg-[#dff0fc]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />
                                <circle cx="12" cy="10" r="3" />
                                <path d="M7 20.662V19a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v1.662" />
                            </svg>
                        </summary>
                        <div
                            class="absolute right-0 z-[220] mt-3 min-w-[220px] rounded-xl border border-[#d7ebf8] bg-white p-2 shadow-[0_18px_45px_rgba(13,60,91,.16)]">
                            <a href="{{ route('profile.index') }}"
                                class="flex w-full items-center gap-2 rounded-[10px] border border-transparent px-[.65rem] py-[.55rem] text-[.76rem] font-semibold uppercase tracking-[.06em] text-[#1a2e3b] transition hover:border-[#99d5f8] hover:bg-[#eff8fe] hover:text-[#0086da]">
                                Profile
                            </a>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="flex w-full items-center gap-2 rounded-[10px] border border-transparent px-[.65rem] py-[.55rem] text-left text-[.76rem] font-semibold uppercase tracking-[.06em] text-[#1a2e3b] transition hover:border-[#99d5f8] hover:bg-[#eff8fe] hover:text-[#0086da]">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </details>
                </div>
            </div>

            <div class="border-t border-[#e4eff8] py-3 lg:hidden">
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('home') }}"
                        class="inline-flex items-center rounded-full border px-4 py-2 text-[.72rem] font-semibold uppercase tracking-[.08em] transition {{ $isPatientDashboardPage ? 'border-[#0086da] bg-[#eff8fe] text-[#0086da]' : 'border-[#d7ebf8] text-[#1a2e3b] hover:border-[#99d5f8] hover:bg-[#eff8fe] hover:text-[#0086da]' }}">
                        Home
                    </a>
                    @if (!$isPatientBookingPage)
                        <a href="{{ route('book') }}"
                            class="inline-flex items-center rounded-full bg-[#0086da] px-4 py-2 text-[.72rem] font-bold uppercase tracking-[.08em] text-white transition hover:bg-[#006ab0]">
                            Book Appointment
                        </a>
                    @endif
                    <a href="{{ route('profile.index') }}"
                        class="inline-flex items-center rounded-full border border-[#d7ebf8] px-4 py-2 text-[.72rem] font-semibold uppercase tracking-[.08em] text-[#1a2e3b] transition hover:border-[#99d5f8] hover:bg-[#eff8fe] hover:text-[#0086da]">
                        Profile
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="inline-flex">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center rounded-full border border-[#d7ebf8] px-4 py-2 text-[.72rem] font-semibold uppercase tracking-[.08em] text-[#1a2e3b] transition hover:border-[#99d5f8] hover:bg-[#eff8fe] hover:text-[#0086da]">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </header>
    @else
        {{-- Admin Header: starts to the right of the sidebar --}}
        <header id="adminHeader"
            class="fixed top-0 right-0 z-40 flex h-16 items-center justify-between border-b border-slate-200/90 bg-white px-4 shadow-sm transition-all duration-300" style="left:16rem;">
            {{-- Left: Hamburger Toggle --}}
            <button id="toggleBtn" type="button" aria-label="Toggle sidebar"
                class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
            {{-- Right: Actions --}}
            <div class="flex items-center gap-2">
                @livewire('shared.notification-bell')
                <details class="relative">
                    <summary
                        class="list-none cursor-pointer inline-flex h-11 w-11 items-center justify-center rounded-full border border-[#cde8f9] bg-[#eff8fe] text-[#0086da] transition duration-200 hover:border-[#7ec4ef] hover:bg-[#dff0fc]">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"
                            fill="none" stroke="currentColor" stroke-width="1.7"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <circle cx="12" cy="10" r="3" />
                            <path d="M7 20.662V19a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v1.662" />
                        </svg>
                    </summary>
                    <div
                        class="absolute right-0 z-[220] mt-3 min-w-[220px] rounded-xl border border-[#d7ebf8] bg-white p-2 shadow-[0_18px_45px_rgba(13,60,91,.16)]">
                        <a href="{{ route('profile.index') }}"
                            class="flex w-full items-center gap-2 rounded-[10px] border border-transparent px-[.65rem] py-[.55rem] text-[.76rem] font-semibold uppercase tracking-[.06em] text-[#1a2e3b] transition hover:border-[#99d5f8] hover:bg-[#eff8fe] hover:text-[#0086da]">
                            Profile
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="flex w-full items-center gap-2 rounded-[10px] border border-transparent px-[.65rem] py-[.55rem] text-left text-[.76rem] font-semibold uppercase tracking-[.06em] text-[#1a2e3b] transition hover:border-[#99d5f8] hover:bg-[#eff8fe] hover:text-[#0086da]">
                                Logout
                            </button>
                        </form>
                    </div>
                </details>
            </div>
        </header>
    @endif

    @if (!$isPatient)
        <aside id="sidebar"
            class="sidebar fixed left-0 top-0 bottom-0 w-64 flex flex-col overflow-hidden bg-[#0f172a] shadow-[4px_0_20px_rgba(0,0,0,0.25)] transition-all duration-300 [&.collapsed]:w-20 group z-50">
            {{-- Sidebar Logo --}}
            <div class="flex h-16 shrink-0 items-center justify-center border-b border-white/10 px-4 group-[.collapsed]:px-2">
                <a href="{{ route('dashboard') }}"
                    class="flex w-full items-center gap-3 no-underline group-[.collapsed]:mx-auto group-[.collapsed]:w-12 group-[.collapsed]:justify-center group-[.collapsed]:px-1 group-[.collapsed]:gap-0">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center group-[.collapsed]:h-10 group-[.collapsed]:w-10">
                        <svg width="40" height="32" viewBox="0 0 56 45" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <mask id="sidebar-logo-mask" mask-type="alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="56" height="45">
                                <path d="M11.783 0.465134C6.04622 2.04593 1.64903 6.81758 0.396845 12.7602C-0.127324 15.307 -0.127324 16.9171 0.367724 19.3468C1.70727 25.6993 7.88082 33.5154 18.5972 42.444L21.0724 44.5225L21.3927 43.1173C22.1499 39.8972 23.402 37.9944 25.6152 36.7941C27.2751 35.8574 28.3525 35.9159 30.158 36.9698C31.5849 37.8187 33.2739 40.5412 33.7398 42.7367C33.9437 43.7321 34.1766 44.5225 34.264 44.5225C34.5261 44.5225 40.8161 39.0775 43.5243 36.5307C51.7363 28.7438 56.0461 20.9862 55.4637 15.0436C55.0269 10.711 53.2797 7.05178 50.2511 4.24147C44.2814 -1.37913 35.4579 -1.4084 29.5756 4.12438L27.7701 5.82227L25.9646 4.15365C22.9361 1.34335 19.4708 -0.00325012 15.3939 0.0552979C14.2 0.0552979 12.5692 0.260216 11.783 0.465134ZM32.7206 9.36442C38.7486 12.3504 41.1947 19.4932 38.1953 25.4066C37.2634 27.2801 34.7008 29.8269 32.808 30.7344C27.0712 33.4862 20.0532 31.0857 17.2867 25.4066C16.0054 22.7134 15.6851 20.6934 16.151 17.9417C16.7626 14.341 19.1796 11.0916 22.5284 9.42297C24.596 8.39838 25.4405 8.22274 28.2943 8.33983C30.4492 8.42765 31.119 8.57402 32.7206 9.36442Z" fill="white" />
                                <path d="M24.0136 9.97903C21.0142 11.15 18.9757 13.2577 17.7235 16.4193C16.7917 18.7612 16.9664 22.3619 18.1021 24.616C19.2378 26.8116 21.2471 28.8022 23.3729 29.7975C24.9163 30.5001 25.4987 30.6172 27.7701 30.6172C29.9833 30.6172 30.653 30.5001 32.0217 29.8561C39.1271 26.4896 40.5831 17.5024 34.8755 12.2039C32.6624 10.1547 30.7113 9.39355 27.6828 9.39355C26.1976 9.42283 24.9745 9.59847 24.0136 9.97903ZM31.0316 13.4334C30.9151 14.0188 30.9151 15.0142 31.0025 15.5996L31.1772 16.6828L33.2739 16.7706L35.3414 16.8584V20.0493V23.2694L33.3613 23.2987C32.2838 23.328 31.3228 23.4451 31.2063 23.5329C31.119 23.65 31.0025 24.616 31.0025 25.6992L30.9734 27.6898L27.6828 27.7776L24.4213 27.8654V25.6699V23.5036L23.0526 23.328C22.2663 23.2401 21.3345 23.2109 20.9268 23.2401L20.1988 23.2987L20.1114 19.9907L20.0241 16.712H22.2372H24.4213V14.5165V12.321H27.7992H31.2063L31.0316 13.4334Z" fill="white" />
                            </mask>
                            <g mask="url(#sidebar-logo-mask)">
                                <rect x="-25.5311" y="-23.4609" width="106.265" height="91.7739" fill="#38bdf8" />
                            </g>
                        </svg>
                    </div>
                    <div class="overflow-hidden whitespace-nowrap leading-tight transition-all duration-300 group-[.collapsed]:w-0 group-[.collapsed]:opacity-0">
                        <div class="text-[.8rem] font-extrabold tracking-[.06em] text-white">TEJADA CLINIC</div>
                        <div class="text-[.5rem] font-semibold uppercase tracking-[.2em] text-sky-400">Dental Care</div>
                    </div>
                </a>
            </div>
            <nav class="flex h-full w-full flex-col py-3 overflow-y-auto">
                <ul class="h-full w-full space-y-1 px-2">
                    @if (auth()->user()->role === 3)
                        <li
                            class="px-3 pb-2 pt-1 nav-text text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400 transition-all duration-300 group-[.collapsed]:opacity-0">
                            Care
                        </li>
                        <li>
                            <a href="{{ route('patient.dashboard') }}"
                                class="{{ request()->routeIs('patient.dashboard') ? 'active' : '' }}
                                nav-item group relative flex w-full items-center gap-3 rounded-lg px-3 py-2.5
                                transition-all duration-200
                                text-slate-300 hover:bg-white/10 hover:text-white
                                [&.active]:bg-[#0086DA] [&.active]:text-white [&.active]:shadow-[0_10px_24px_-16px_rgba(0,134,218,0.85)]
                                group-[.collapsed]:mx-auto group-[.collapsed]:w-12 group-[.collapsed]:justify-center group-[.collapsed]:px-1 group-[.collapsed]:gap-0
                                group-[.collapsed]:[&.active]:bg-white group-[.collapsed]:[&.active]:text-[#0086DA] group-[.collapsed]:[&.active]:shadow-[0_8px_18px_-14px_rgba(0,134,218,0.65)]">
                                <span
                                    class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg text-slate-400 transition-colors duration-200 group-[.active]:text-sky-300 group-[.collapsed]:h-10 group-[.collapsed]:w-10">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24"
                                        height="24" color="currentColor" fill="none" stroke="currentColor"
                                        stroke-width="1.5" stroke-linejoin="round">
                                        <path
                                            d="M3 10.5C3 6.52166 6.13401 3.25 10 3.25C13.866 3.25 17 6.52166 17 10.5C17 14.4783 13.866 17.75 10 17.75C6.13401 17.75 3 14.4783 3 10.5Z" />
                                        <path d="M14 14.5L20 20.5" />
                                    </svg>
                                </span>
                                <span
                                    class="nav-text whitespace-nowrap text-sm font-semibold tracking-wide overflow-hidden transition-all duration-300 group-[.collapsed]:w-0 group-[.collapsed]:opacity-0">
                                    My Dashboard
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('book') }}"
                                class="{{ request()->routeIs('book') ? 'active' : '' }}
                                nav-item group relative flex w-full items-center gap-3 rounded-lg px-3 py-2.5
                                transition-all duration-200
                                text-slate-300 hover:bg-white/10 hover:text-white
                                [&.active]:bg-[#0086DA] [&.active]:text-white [&.active]:shadow-[0_10px_24px_-16px_rgba(0,134,218,0.85)]
                                group-[.collapsed]:mx-auto group-[.collapsed]:w-12 group-[.collapsed]:justify-center group-[.collapsed]:px-1 group-[.collapsed]:gap-0">
                                <span
                                    class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg text-slate-400 transition-colors duration-200 group-[.active]:text-sky-300 group-[.collapsed]:h-10 group-[.collapsed]:w-10">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24"
                                        height="24" color="currentColor" fill="none" stroke="currentColor"
                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 2V6M8 2V6" />
                                        <path
                                            d="M13 4H11C7.22876 4 5.34315 4 4.17157 5.17157C3 6.34315 3 8.22876 3 12V14C3 17.7712 3 19.6569 4.17157 20.8284C5.34315 22 7.22876 22 11 22H13C16.7712 22 18.6569 22 19.8284 20.8284C21 19.6569 21 17.7712 21 14V12C21 8.22876 21 6.34315 19.8284 5.17157C18.6569 4 16.7712 4 13 4Z" />
                                        <path d="M3 10H21" />
                                        <path d="M9 16.5C9 16.5 10.5 17 11 18.5C11 18.5 13.1765 14.5 16 13.5" />
                                    </svg>
                                </span>
                                <span
                                    class="nav-text whitespace-nowrap text-sm font-semibold tracking-wide overflow-hidden transition-all duration-300 group-[.collapsed]:w-0 group-[.collapsed]:opacity-0">
                                    Appointment
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('profile.index') }}" title="My Profile"
                                class="{{ request()->routeIs('profile.index') ? 'active' : '' }}
                                nav-item group relative flex w-full items-center gap-3 rounded-lg px-3 py-2.5
                                transition-all duration-200
                                text-slate-300 hover:bg-white/10 hover:text-white
                                [&.active]:bg-[#0086DA] [&.active]:text-white [&.active]:shadow-[0_10px_24px_-16px_rgba(0,134,218,0.85)]
                                group-[.collapsed]:mx-auto group-[.collapsed]:w-12 group-[.collapsed]:justify-center group-[.collapsed]:px-1 group-[.collapsed]:gap-0">
                                <span
                                    class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg text-slate-400 transition-colors duration-200 group-[.active]:text-sky-300 group-[.collapsed]:h-10 group-[.collapsed]:w-10">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" color="currentColor" fill="none"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M20 21C20 18.7909 18.2091 17 16 17H8C5.79086 17 4 18.7909 4 21" />
                                        <circle cx="12" cy="9" r="4" />
                                    </svg>
                                </span>
                                <span
                                    class="nav-text whitespace-nowrap text-sm font-semibold tracking-wide overflow-hidden transition-all duration-300 group-[.collapsed]:w-0 group-[.collapsed]:opacity-0">
                                    My Profile
                                </span>
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('dashboard') }}" title="Dashboard"
                                class="{{ request()->is('dashboard') ? 'active' : '' }}
                            nav-item group relative flex w-full items-center gap-3 rounded-lg px-3 py-2.5
                            transition-all duration-200
                            text-slate-300 hover:bg-white/10 hover:text-white
                            [&.active]:bg-[#0086DA] [&.active]:text-white [&.active]:shadow-[0_10px_24px_-16px_rgba(0,134,218,0.85)]
                            group-[.collapsed]:mx-auto group-[.collapsed]:w-12 group-[.collapsed]:justify-center group-[.collapsed]:px-1 group-[.collapsed]:gap-0">

                                <span
                                    class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg text-slate-400 transition-colors duration-200 group-[.active]:text-sky-300 group-[.collapsed]:h-10 group-[.collapsed]:w-10">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24"
                                        height="24" color="currentColor" fill="none" stroke="currentColor"
                                        stroke-width="1.5" stroke-linejoin="round">
                                        <path
                                            d="M10.5 8.75V6.75C10.5 5.10626 10.5 4.28439 10.046 3.73121C9.96291 3.62995 9.87005 3.53709 9.76879 3.45398C9.21561 3 8.39374 3 6.75 3C5.10626 3 4.28439 3 3.73121 3.45398C3.62995 3.53709 3.53709 3.62995 3.45398 3.73121C3 4.28439 3 5.10626 3 6.75V8.75C3 10.3937 3 11.2156 3.45398 11.7688C3.53709 11.8701 3.62995 11.9629 3.73121 12.046C4.28439 12.5 5.10626 12.5 6.75 12.5C8.39374 12.5 9.21561 12.5 9.76879 12.046C9.87005 11.9629 9.96291 11.8701 10.046 11.7688C10.5 11.2156 10.5 10.3937 10.5 8.75Z" />
                                        <path
                                            d="M7.75 15.5H5.75C5.05222 15.5 4.70333 15.5 4.41943 15.5861C3.78023 15.78 3.28002 16.2802 3.08612 16.9194C3 17.2033 3 17.5522 3 18.25C3 18.9478 3 19.2967 3.08612 19.5806C3.28002 20.2198 3.78023 20.72 4.41943 20.9139C4.70333 21 5.05222 21 5.75 21H7.75C8.44778 21 8.79667 21 9.08057 20.9139C9.71977 20.72 10.22 20.2198 10.4139 19.5806C10.5 19.2967 10.5 18.9478 10.5 18.25C10.5 17.5522 10.5 17.2033 10.4139 16.9194C10.22 16.2802 9.71977 15.78 9.08057 15.5861C8.79667 15.5 8.44778 15.5 7.75 15.5Z" />
                                        <path
                                            d="M21 17.25V15.25C21 13.6063 21 12.7844 20.546 12.2312C20.4629 12.1299 20.3701 12.0371 20.2688 11.954C19.7156 11.5 18.8937 11.5 17.25 11.5C15.6063 11.5 14.7844 11.5 14.2312 11.954C14.1299 12.0371 14.0371 12.1299 13.954 12.2312C13.5 12.7844 13.5 13.6063 13.5 15.25V17.25C13.5 18.8937 13.5 19.7156 13.954 20.2688C14.0371 20.3701 14.1299 20.4629 14.2312 20.546C14.7844 21 15.6063 21 17.25 21C18.8937 21 19.7156 21 20.2688 20.546C20.3701 20.4629 20.4629 20.3701 20.546 20.2688C21 19.7156 21 18.8937 21 17.25Z" />
                                        <path
                                            d="M18.25 3H16.25C15.5522 3 15.2033 3 14.9194 3.08612C14.2802 3.28002 13.78 3.78023 13.5861 4.41943C13.5 4.70333 13.5 5.05222 13.5 5.75C13.5 6.44778 13.5 6.79667 13.5861 7.08057C13.78 7.71977 14.2802 8.21998 14.9194 8.41388C15.2033 8.5 15.5522 8.5 16.25 8.5H18.25C18.9478 8.5 19.2967 8.5 19.5806 8.41388C20.2198 8.21998 20.72 7.71977 20.9139 7.08057C21 6.79667 21 6.44778 21 5.75C21 5.05222 21 4.70333 20.9139 4.41943C20.72 3.78023 20.2198 3.28002 19.5806 3.08612C19.2967 3 18.9478 3 18.25 3Z" />
                                    </svg>
                                </span>

                                <span
                                    class="nav-text whitespace-nowrap text-sm font-semibold tracking-wide overflow-hidden transition-all duration-300 group-[.collapsed]:w-0 group-[.collapsed]:opacity-0">Dashboard
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('patient-records') }}" title="Patient Records"
                                class="{{ request()->is('patient-records') ? 'active' : '' }} 
                                nav-item group relative flex w-full items-center gap-3 rounded-lg px-3 py-2.5
                                transition-all duration-200
                                text-slate-300 hover:bg-white/10 hover:text-white
                                [&.active]:bg-[#0086DA] [&.active]:text-white [&.active]:shadow-[0_10px_24px_-16px_rgba(0,134,218,0.85)]
                                group-[.collapsed]:mx-auto group-[.collapsed]:w-12 group-[.collapsed]:justify-center group-[.collapsed]:px-1 group-[.collapsed]:gap-0">
                                <span
                                    class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg text-slate-400 transition-colors duration-200 group-[.active]:text-sky-300 group-[.collapsed]:h-10 group-[.collapsed]:w-10">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-folder-open-icon lucide-folder-open"><path d="m6 14 1.5-2.9A2 2 0 0 1 9.24 10H20a2 2 0 0 1 1.94 2.5l-1.54 6a2 2 0 0 1-1.95 1.5H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3.9a2 2 0 0 1 1.69.9l.81 1.2a2 2 0 0 0 1.67.9H18a2 2 0 0 1 2 2v2"/></svg>
                                </span>
                                <span
                                    class="nav-text whitespace-nowrap text-sm font-semibold tracking-wide overflow-hidden transition-all duration-300 group-[.collapsed]:w-0 group-[.collapsed]:opacity-0">Patient
                                    Records</span>
                            </a>
                        </li>
                        <li>
                            @php
                                $isAppointmentGroupActive =
                                    request()->routeIs('appointment') ||
                                    request()->routeIs('appointment.requests') ||
                                    request()->routeIs('appointment.calendar') ||
                                    request()->routeIs('appointment.history');
                                $isAppointmentMenuOpen = $isAppointmentGroupActive;
                            @endphp
                            <button type="button" data-appointment-toggle
                                title="Appointments"
                                aria-expanded="{{ $isAppointmentMenuOpen ? 'true' : 'false' }}"
                                class="{{ $isAppointmentGroupActive ? 'active' : '' }} nav-item group relative flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-slate-300 transition-all duration-200 hover:bg-white/10 hover:text-white group-[.collapsed]:mx-auto group-[.collapsed]:w-12 group-[.collapsed]:justify-center group-[.collapsed]:px-1 group-[.collapsed]:gap-0 [&.active]:bg-transparent [&.active]:text-[#0086DA] [&.active]:shadow-none">
                                <span
                                    class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg text-slate-400 transition-colors duration-200 group-[.active]:text-[#0086DA] group-[.collapsed]:h-10 group-[.collapsed]:w-10">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-icon lucide-calendar"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
                                </span>
                                <span
                                    class="nav-text whitespace-nowrap text-sm font-semibold tracking-wide overflow-hidden transition-all duration-300 group-[.collapsed]:w-0 group-[.collapsed]:opacity-0">Appointments</span>
                                <span
                                    class="ml-auto nav-text transition-transform duration-200 group-[.collapsed]:hidden {{ $isAppointmentMenuOpen ? 'rotate-180' : '' }}"
                                    data-appointment-chevron>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </span>
                            </button>
                            <div data-appointment-submenu
                                class="relative ml-2 mt-1 space-y-1 overflow-hidden transition-all duration-300 ease-in-out group-[.collapsed]:hidden {{ $isAppointmentMenuOpen ? 'max-h-80 opacity-100 pb-2' : 'max-h-0 opacity-0 pointer-events-none' }}">
                                <a href="{{ route('appointment.calendar') }}" title="Appointment Calendar"
                                    class="{{ request()->routeIs('appointment.calendar') || request()->routeIs('appointment') ? 'bg-[#0086DA] text-white shadow-[0_10px_24px_-16px_rgba(0,134,218,0.85)] before:bg-white/70' : 'text-slate-300 hover:bg-white/10 hover:text-white before:bg-slate-400/70' }} relative block mx-2 rounded-xl px-3 py-2 pl-[3.5rem] text-sm font-medium tracking-normal before:absolute before:left-[1.875rem] before:top-1/2 before:h-px before:w-4 before:-translate-y-1/2">                                    Appointment Calendar
                                </a>
                                <a href="{{ route('appointment.requests') }}" title="Appointment Request"
                                    class="{{ request()->routeIs('appointment.requests') ? 'bg-[#0086DA] text-white shadow-[0_10px_24px_-16px_rgba(0,134,218,0.85)] before:bg-white/70' : 'text-slate-300 hover:bg-white/10 hover:text-white before:bg-slate-400/70' }} relative block mx-2 rounded-xl px-3 py-2 pl-[3.5rem] text-sm font-medium tracking-normal before:absolute before:left-[1.875rem] before:top-1/2 before:h-px before:w-4 before:-translate-y-1/2">
                                    Appointment Request
                                </a>
                                <a href="{{ route('appointment.history') }}" title="Appointment History"
                                    class="{{ request()->routeIs('appointment.history') ? 'bg-[#0086DA] text-white shadow-[0_10px_24px_-16px_rgba(0,134,218,0.85)] before:bg-white/70' : 'text-slate-300 hover:bg-white/10 hover:text-white before:bg-slate-400/70' }} relative block mx-2 rounded-xl px-3 py-2 pl-[3.5rem] text-sm font-medium tracking-normal before:absolute before:left-[1.875rem] before:top-1/2 before:h-px before:w-4 before:-translate-y-1/2">
                                    Appointment History
                                </a>
                            </div>
                        </li>
                        @if (auth()->user()->role !== 3)
                            <li>
                                <a href="{{ route('queue') }}" title="Queue"
                                    class="{{ request()->is('queue') ? 'active' : '' }}
                                nav-item group relative flex w-full items-center gap-3 rounded-lg px-3 py-2.5
                                transition-all duration-200
                                text-slate-300 hover:bg-white/10 hover:text-white
                                [&.active]:bg-[#0086DA] [&.active]:text-white [&.active]:shadow-[0_10px_24px_-16px_rgba(0,134,218,0.85)]
                                group-[.collapsed]:mx-auto group-[.collapsed]:w-12 group-[.collapsed]:justify-center group-[.collapsed]:px-1 group-[.collapsed]:gap-0">
                                    <span
                                        class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg text-slate-400 transition-colors duration-200 group-[.active]:text-sky-300 group-[.collapsed]:h-10 group-[.collapsed]:w-10">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-copy-icon lucide-copy">
                                            <rect width="14" height="14" x="8" y="8" rx="2"
                                                ry="2" />
                                            <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2" />
                                        </svg>
                                    </span>
                                    <span
                                        class="nav-text whitespace-nowrap text-sm font-semibold tracking-wide overflow-hidden transition-all duration-300 group-[.collapsed]:w-0 group-[.collapsed]:opacity-0">Queue</span>
                                </a>
                            </li>
                        @endif
                        @if ($isAdmin)
                            <li>
                                <a href="{{ route('reports.index') }}" title="Reports"
                                    class="{{ request()->routeIs('reports.*') ? 'active' : '' }}
                                nav-item group relative flex w-full items-center gap-3 rounded-lg px-3 py-2.5
                                transition-all duration-200
                                text-slate-300 hover:bg-white/10 hover:text-white
                                [&.active]:bg-[#0086DA] [&.active]:text-white [&.active]:shadow-[0_10px_24px_-16px_rgba(0,134,218,0.85)]
                                group-[.collapsed]:mx-auto group-[.collapsed]:w-12 group-[.collapsed]:justify-center group-[.collapsed]:px-1 group-[.collapsed]:gap-0">
                                    <span
                                        class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg text-slate-400 transition-colors duration-200 group-[.active]:text-sky-300 group-[.collapsed]:h-10 group-[.collapsed]:w-10">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chart-pie-icon lucide-chart-pie"><path d="M21 12c.552 0 1.005-.449.95-.998a10 10 0 0 0-8.953-8.951c-.55-.055-.998.398-.998.95v8a1 1 0 0 0 1 1z"/><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/></svg>                                    </span>
                                    <span
                                        class="nav-text whitespace-nowrap text-sm font-semibold tracking-wide overflow-hidden transition-all duration-300 group-[.collapsed]:w-0 group-[.collapsed]:opacity-0">
                                        Analytics
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('users.index') }}" title="User Accounts"
                                    class="{{ request()->routeIs('users.*') ? 'active' : '' }}
                                nav-item group relative flex w-full items-center gap-3 rounded-lg px-3 py-2.5
                                transition-all duration-200
                                text-slate-300 hover:bg-white/10 hover:text-white
                                [&.active]:bg-[#0086DA] [&.active]:text-white [&.active]:shadow-[0_10px_24px_-16px_rgba(0,134,218,0.85)]
                                group-[.collapsed]:mx-auto group-[.collapsed]:w-12 group-[.collapsed]:justify-center group-[.collapsed]:px-1 group-[.collapsed]:gap-0">
                                    <span
                                        class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg text-slate-400 transition-colors duration-200 group-[.active]:text-sky-300 group-[.collapsed]:h-10 group-[.collapsed]:w-10">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-user-icon lucide-square-user"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="12" cy="10" r="3"/><path d="M7 21v-2a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v2"/></svg>
                                    </span>
                                    <span
                                        class="nav-text whitespace-nowrap text-sm font-semibold tracking-wide overflow-hidden transition-all duration-300 group-[.collapsed]:w-0 group-[.collapsed]:opacity-0">
                                        User Accounts
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('activity-logs') }}" title="Activity Logs"
                                class="{{ request()->is('activity-logs') ? 'active' : '' }} nav-item group relative flex w-full items-center gap-3 rounded-lg px-3 py-2.5 transition-all duration-200 text-slate-300 hover:bg-white/10 hover:text-white [&.active]:bg-[#0086DA] [&.active]:text-white [&.active]:shadow-[0_10px_24px_-16px_rgba(0,134,218,0.85)] group-[.collapsed]:mx-auto group-[.collapsed]:w-12 group-[.collapsed]:justify-center group-[.collapsed]:px-1 group-[.collapsed]:gap-0">
                                    <span
                                        class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg text-slate-400 transition-colors duration-200 group-[.active]:text-sky-300 group-[.collapsed]:h-10 group-[.collapsed]:w-10">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clipboard-clock-icon lucide-clipboard-clock"><path d="M16 14v2.2l1.6 1"/><path d="M16 4h2a2 2 0 0 1 2 2v.832"/><path d="M8 4H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h2"/><circle cx="16" cy="16" r="6"/><rect x="8" y="2" width="8" height="4" rx="1"/></svg>
                                    </span>
                                    <span
                                        class="nav-text whitespace-nowrap text-sm font-semibold tracking-wide overflow-hidden transition-all duration-300 group-[.collapsed]:w-0 group-[.collapsed]:opacity-0">Activity
                                        Logs</span>
                                </a>
                            </li>
                        @endif

                    @endif
                </ul>
            </nav>
        </aside>
    @endif

    @php
        $pageShellClasses = trim($__env->yieldContent('page_shell_class'));
        $adminShellBaseClasses = 'min-h-screen bg-[#f3f4f6] p-4 sm:p-6 lg:p-8 transition-all duration-300';
        $patientShellBaseClasses = 'min-h-[calc(100vh-73px)] bg-white';
    @endphp

    <main id="{{ $isPatient ? 'patientMain' : 'adminMain' }}"
        class="{{ $isPatient ? $patientShellBaseClasses : $adminShellBaseClasses }} {{ $pageShellClasses }}"
        @if (!$isPatient) style="margin-left:16rem; padding-top:5.5rem;" @endif>
        @yield('content')
    </main>


    @stack('script')
    <script>
        (function() {
            const sidebar = document.getElementById('sidebar');
            const adminHeader = document.getElementById('adminHeader');
            const toggleBtn = document.getElementById('toggleBtn');
            const adminMain = document.getElementById('adminMain');
            const appointmentToggle = document.querySelector('[data-appointment-toggle]');
            const appointmentSubmenu = document.querySelector('[data-appointment-submenu]');
            const appointmentChevron = document.querySelector('[data-appointment-chevron]');

            const EXPANDED_W = '16rem';
            const COLLAPSED_W = '5rem';

            const syncLayout = () => {
                if (!sidebar || !adminHeader) return;
                const sidebarWidth = sidebar.classList.contains('collapsed') ? COLLAPSED_W : EXPANDED_W;
                adminHeader.style.left = sidebarWidth;
                if (adminMain) {
                    adminMain.style.marginLeft = sidebarWidth;
                }
            };

            if (sidebar && toggleBtn) {
                if (localStorage.getItem('sidebar-collapsed') === 'true') {
                    sidebar.classList.add('collapsed');
                }
                syncLayout();

                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
                    syncLayout();
                });
            }

            if (appointmentToggle && appointmentSubmenu) {
                const storageKey = 'sidebar-appointment-open';
                const storedState = localStorage.getItem(storageKey);
                let isOpen = storedState !== null ?
                    storedState === 'true' :
                    appointmentToggle.getAttribute('aria-expanded') === 'true';

                const renderAppointmentState = () => {
                    appointmentSubmenu.classList.toggle('max-h-80', isOpen);
                    appointmentSubmenu.classList.toggle('opacity-100', isOpen);
                    appointmentSubmenu.classList.toggle('pb-2', isOpen);
                    appointmentSubmenu.classList.toggle('max-h-0', !isOpen);
                    appointmentSubmenu.classList.toggle('opacity-0', !isOpen);
                    appointmentSubmenu.classList.toggle('pointer-events-none', !isOpen);
                    appointmentToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                    if (appointmentChevron) {
                        appointmentChevron.classList.toggle('rotate-180', isOpen);
                    }
                };

                renderAppointmentState();

                appointmentToggle.addEventListener('click', function() {
                    if (sidebar && sidebar.classList.contains('collapsed')) {
                        sidebar.classList.remove('collapsed');
                        localStorage.setItem('sidebar-collapsed', 'false');
                        syncLayout();
                        isOpen = true;
                        localStorage.setItem(storageKey, 'true');
                        renderAppointmentState();
                        return;
                    }

                    isOpen = !isOpen;
                    localStorage.setItem(storageKey, String(isOpen));
                    renderAppointmentState();
                });
            }

        })();
    </script>

    <x-flash-toast />
    @livewireScripts
</body>

</html>
