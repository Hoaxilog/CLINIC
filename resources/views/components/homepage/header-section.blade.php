@php
    $isPatientUser = auth()->check() && auth()->user()->role === 3;
@endphp

<style>
    .booking-nav-link::after {
        content: '';
        position: absolute;
        bottom: -4px;
        left: 0;
        width: 0;
        height: 2px;
        background: #0086da;
        transition: width .25s ease;
    }

    .booking-nav-link:hover::after {
        width: 100%;
    }

    #book-bar1,
    #book-bar2,
    #book-bar3 {
        display: block;
        height: 2px;
        transform-origin: center;
        transition: transform .25s ease, opacity .2s ease, width .2s ease;
    }

    #book-bar1,
    #book-bar2 {
        width: 22px;
        background: #1a2e3b;
    }

    #book-bar3 {
        width: 14px;
        background: #0086da;
    }

    #booking-ham-btn.active #book-bar1 {
        transform: translateY(7px) rotate(45deg);
    }

    #booking-ham-btn.active #book-bar2 {
        opacity: 0;
        transform: scaleX(0);
    }

    #booking-ham-btn.active #book-bar3 {
        transform: translateY(-7px) rotate(-45deg);
        width: 22px;
    }

    .profile-menu-item {
        display: flex;
        width: 100%;
        align-items: center;
        gap: .5rem;
        border: 1px solid transparent;
        border-radius: 10px;
        padding: .55rem .65rem;
        color: #1a2e3b;
        font-size: .76rem;
        font-weight: 600;
        letter-spacing: .06em;
        text-transform: uppercase;
        text-decoration: none;
        transition: background-color .2s ease, color .2s ease, border-color .2s ease;
    }

    .profile-menu-item:hover {
        border-color: #99d5f8;
        background: #eff8fe;
        color: #0086da;
    }
</style>

<header class="sticky top-0 z-[100] border-b border-[#e4eff8] bg-white px-6 md:px-12 xl:px-20">
    <div class="relative mx-auto flex h-[70px] w-full max-w-[1400px] items-center justify-between">
        <a href="/" class="flex shrink-0 items-center gap-3 no-underline">
            <div class="flex h-[38px] w-[38px] shrink-0 items-center justify-center">
                <svg width="56" height="45" viewBox="0 0 56 45" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <mask id="booking-mask-logo" mask-type="alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="56"
                        height="45">
                        <path
                            d="M11.783 0.465134C6.04622 2.04593 1.64903 6.81758 0.396845 12.7602C-0.127324 15.307 -0.127324 16.9171 0.367724 19.3468C1.70727 25.6993 7.88082 33.5154 18.5972 42.444L21.0724 44.5225L21.3927 43.1173C22.1499 39.8972 23.402 37.9944 25.6152 36.7941C27.2751 35.8574 28.3525 35.9159 30.158 36.9698C31.5849 37.8187 33.2739 40.5412 33.7398 42.7367C33.9437 43.7321 34.1766 44.5225 34.264 44.5225C34.5261 44.5225 40.8161 39.0775 43.5243 36.5307C51.7363 28.7438 56.0461 20.9862 55.4637 15.0436C55.0269 10.711 53.2797 7.05178 50.2511 4.24147C44.2814 -1.37913 35.4579 -1.4084 29.5756 4.12438L27.7701 5.82227L25.9646 4.15365C22.9361 1.34335 19.4708 -0.00325012 15.3939 0.0552979C14.2 0.0552979 12.5692 0.260216 11.783 0.465134ZM32.7206 9.36442C38.7486 12.3504 41.1947 19.4932 38.1953 25.4066C37.2634 27.2801 34.7008 29.8269 32.808 30.7344C27.0712 33.4862 20.0532 31.0857 17.2867 25.4066C16.0054 22.7134 15.6851 20.6934 16.151 17.9417C16.7626 14.341 19.1796 11.0916 22.5284 9.42297C24.596 8.39838 25.4405 8.22274 28.2943 8.33983C30.4492 8.42765 31.119 8.57402 32.7206 9.36442Z"
                            fill="black" />
                        <path
                            d="M24.0136 9.97903C21.0142 11.15 18.9757 13.2577 17.7235 16.4193C16.7917 18.7612 16.9664 22.3619 18.1021 24.616C19.2378 26.8116 21.2471 28.8022 23.3729 29.7975C24.9163 30.5001 25.4987 30.6172 27.7701 30.6172C29.9833 30.6172 30.653 30.5001 32.0217 29.8561C39.1271 26.4896 40.5831 17.5024 34.8755 12.2039C32.6624 10.1547 30.7113 9.39355 27.6828 9.39355C26.1976 9.42283 24.9745 9.59847 24.0136 9.97903ZM31.0316 13.4334C30.9151 14.0188 30.9151 15.0142 31.0025 15.5996L31.1772 16.6828L33.2739 16.7706L35.3414 16.8584V20.0493V23.2694L33.3613 23.2987C32.2838 23.328 31.3228 23.4451 31.2063 23.5329C31.119 23.65 31.0025 24.616 31.0025 25.6992L30.9734 27.6898L27.6828 27.7776L24.4213 27.8654V25.6699V23.5036L23.0526 23.328C22.2663 23.2401 21.3345 23.2109 20.9268 23.2401L20.1988 23.2987L20.1114 19.9907L20.0241 16.712H22.2372H24.4213V14.5165V12.321H27.7992H31.2063L31.0316 13.4334Z"
                            fill="black" />
                    </mask>
                    <g mask="url(#booking-mask-logo)">
                        <rect x="-25.5311" y="-23.4609" width="106.265" height="91.7739" fill="#0086DA" />
                    </g>
                </svg>
            </div>
            <div class="leading-[1.25]">
                <div class="text-[.92rem] font-extrabold tracking-[.04em] text-[#1a2e3b]">TEJADA CLINIC</div>
                <div class="text-[.57rem] font-semibold uppercase tracking-[.2em] text-[#0086da]">Dental Care</div>
            </div>
        </a>

        <nav class="hidden items-center gap-9 lg:flex">
            @if ($isPatientUser)
                <a href="{{ route('patient.dashboard') }}"
                    class="booking-nav-link relative text-[.72rem] font-semibold uppercase tracking-[.07em] text-[#1a2e3b] transition-colors duration-200 hover:text-[#0086da]">Dashboard</a>
                <a href="{{ route('book') }}"
                    class="booking-nav-link relative text-[.72rem] font-semibold uppercase tracking-[.07em] text-[#1a2e3b] transition-colors duration-200 hover:text-[#0086da]">Book</a>
            @else
                @foreach (['Services' => 'services', 'About' => 'about', 'Why Us' => 'why-us', 'Hours' => 'hours', 'Contact' => 'contact'] as $label => $id)
                    <a href="/home#{{ $id }}"
                        class="booking-nav-link relative text-[.72rem] font-semibold uppercase tracking-[.07em] text-[#1a2e3b] transition-colors duration-200 hover:text-[#0086da]">{{ $label }}</a>
                @endforeach
            @endif
        </nav>

        <div class="hidden items-center gap-3 lg:flex">
            @if ($isPatientUser)
                <div class="relative" id="patient-profile-wrap">
                    <button id="patient-profile-btn" type="button" aria-haspopup="true" aria-expanded="false"
                        aria-controls="patient-profile-menu"
                        class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-[#cde8f9] bg-[#eff8fe] text-[#0086da] transition duration-200 hover:border-[#7ec4ef] hover:bg-[#dff0fc]">
                        <span class="sr-only">Open profile menu</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 2a4 4 0 100 8 4 4 0 000-8zM3 16a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div id="patient-profile-menu"
                        class="absolute right-0 z-[220] mt-3 hidden min-w-[220px] rounded-xl border border-[#d7ebf8] bg-white p-2 shadow-[0_18px_45px_rgba(13,60,91,.16)]">
                        <a href="{{ route('profile.index') }}" class="profile-menu-item">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="profile-menu-item">Logout</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}"
                    class="inline-flex items-center gap-[9px] whitespace-nowrap border border-[#0086da] px-6 py-3 text-[.7rem] font-bold uppercase tracking-[.1em] text-[#0086da] transition duration-200 hover:-translate-y-px hover:bg-[#0086da] hover:text-white">
                    Login
                </a>
                <a href="{{ route('book') }}"
                    class="inline-flex items-center gap-[9px] whitespace-nowrap bg-[#0086da] px-6 py-3 text-[.7rem] font-bold uppercase tracking-[.1em] text-white transition duration-200 hover:-translate-y-px hover:bg-[#006ab0]">
                    Book Now
                </a>
            @endif
        </div>

        <button id="booking-ham-btn" aria-label="Toggle menu"
            class="flex flex-col items-end gap-[5px] border-none bg-transparent p-2 lg:hidden">
            <span id="book-bar1"></span>
            <span id="book-bar2"></span>
            <span id="book-bar3"></span>
        </button>

        <div id="booking-mob-menu"
            class="absolute top-full right-0 left-0 z-[200] hidden border-t border-[#e4eff8] bg-white shadow-[0_8px_32px_rgba(0,0,0,.08)]">
            @if ($isPatientUser)
                <a href="{{ route('patient.dashboard') }}"
                    class="block border-b border-[#e4eff8] px-7 py-[17px] text-[.75rem] font-semibold uppercase tracking-[.08em] text-[#1a2e3b] no-underline transition hover:bg-[#f0f8fe] hover:text-[#0086da]">Dashboard</a>
                <a href="{{ route('book') }}"
                    class="block border-b border-[#e4eff8] px-7 py-[17px] text-[.75rem] font-semibold uppercase tracking-[.08em] text-[#1a2e3b] no-underline transition hover:bg-[#f0f8fe] hover:text-[#0086da]">Book</a>
                <a href="{{ route('profile.index') }}"
                    class="block border-b border-[#e4eff8] px-7 py-[17px] text-[.75rem] font-semibold uppercase tracking-[.08em] text-[#1a2e3b] no-underline transition hover:bg-[#f0f8fe] hover:text-[#0086da]">Profile</a>
                <div class="px-7 pt-5 pb-6">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="inline-flex w-full items-center justify-center gap-[9px] whitespace-nowrap border border-[#0086da] px-8 py-[15px] text-[.72rem] font-bold uppercase tracking-[.1em] text-[#0086da] transition duration-200 hover:bg-[#0086da] hover:text-white">
                            Logout
                        </button>
                    </form>
                </div>
            @else
                @foreach (['Services' => 'services', 'About' => 'about', 'Why Us' => 'why-us', 'Hours' => 'hours', 'Contact' => 'contact'] as $label => $id)
                    <a href="/home#{{ $id }}"
                        class="block border-b border-[#e4eff8] px-7 py-[17px] text-[.75rem] font-semibold uppercase tracking-[.08em] text-[#1a2e3b] no-underline transition hover:bg-[#f0f8fe] hover:text-[#0086da]">{{ $label }}</a>
                @endforeach
                <a href="{{ route('login') }}"
                    class="block border-b border-[#e4eff8] px-7 py-[17px] text-[.75rem] font-semibold uppercase tracking-[.08em] text-[#1a2e3b] no-underline transition hover:bg-[#f0f8fe] hover:text-[#0086da]">Login</a>
                <div class="px-7 pt-5 pb-6">
                    <a href="{{ route('book') }}"
                        class="inline-flex w-full items-center justify-center gap-[9px] whitespace-nowrap bg-[#0086da] px-8 py-[15px] text-[.72rem] font-bold uppercase tracking-[.1em] text-white transition duration-200 hover:bg-[#006ab0]">Book
                        Appointment</a>
                </div>
            @endif
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const hamBtn = document.getElementById('booking-ham-btn');
        const mobMenu = document.getElementById('booking-mob-menu');
        const patientProfileWrap = document.getElementById('patient-profile-wrap');
        const patientProfileBtn = document.getElementById('patient-profile-btn');
        const patientProfileMenu = document.getElementById('patient-profile-menu');
        if (!hamBtn || !mobMenu) return;

        hamBtn.addEventListener('click', () => {
            const open = mobMenu.classList.toggle('hidden') === false;
            hamBtn.classList.toggle('active', open);
            hamBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
        });

        mobMenu.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                mobMenu.classList.add('hidden');
                hamBtn.classList.remove('active');
                hamBtn.setAttribute('aria-expanded', 'false');
            });
        });

        if (patientProfileBtn && patientProfileMenu && patientProfileWrap) {
            const closeProfileMenu = () => {
                patientProfileMenu.classList.add('hidden');
                patientProfileBtn.setAttribute('aria-expanded', 'false');
            };

            patientProfileBtn.addEventListener('click', () => {
                const isOpen = !patientProfileMenu.classList.contains('hidden');
                patientProfileMenu.classList.toggle('hidden', isOpen);
                patientProfileBtn.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
            });

            document.addEventListener('click', (event) => {
                if (!patientProfileWrap.contains(event.target)) {
                    closeProfileMenu();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeProfileMenu();
                }
            });
        }
    });
</script>
