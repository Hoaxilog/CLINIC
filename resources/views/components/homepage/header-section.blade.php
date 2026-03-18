@php
    $isPatientUser = auth()->check() && auth()->user()->role === 3;
    $guestActionMode = $guestActionMode ?? 'login';
    $guestSecondaryLabel = $guestActionMode === 'register' ? 'Sign Up' : 'Login';
    $guestSecondaryRoute = $guestActionMode === 'register' ? route('register') : route('login');
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
        <x-brand.logo href="{{ route('home') }}" />

        @if (!$isPatientUser)
            <nav class="hidden items-center gap-9 lg:flex">
                @foreach (['Services' => 'services', 'About' => 'about', 'Why Us' => 'why-us', 'Hours' => 'hours', 'Contact' => 'contact'] as $label => $id)
                    <a href="/home#{{ $id }}"
                        class="booking-nav-link relative text-[.72rem] font-semibold uppercase tracking-[.07em] text-[#1a2e3b] transition-colors duration-200 hover:text-[#0086da]">{{ $label }}</a>
                @endforeach
            </nav>
        @endif

        <div class="{{ $isPatientUser ? 'flex items-center gap-2' : 'hidden items-center gap-2 lg:flex' }}">
            @if ($isPatientUser)
                @livewire('components.notification-bell')

                <div class="relative" id="patient-profile-wrap">
                    <button id="patient-profile-btn" type="button" aria-haspopup="true" aria-expanded="false"
                        aria-controls="patient-profile-menu"
                        class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-[#cde8f9] bg-[#eff8fe] text-[#0086da] transition duration-200 hover:border-[#7ec4ef] hover:bg-[#dff0fc]">
                        <span class="sr-only">Open profile menu</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"
                            aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 2a4 4 0 100 8 4 4 0 000-8zM3 16a7 7 0 1114 0H3z"
                                clip-rule="evenodd" />
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
                <a href="{{ $guestSecondaryRoute }}"
                    class="inline-flex items-center gap-[9px] whitespace-nowrap border border-[#0086da] px-6 py-3 text-[.7rem] font-bold uppercase tracking-[.1em] text-[#0086da] transition duration-200 hover:-translate-y-px hover:bg-[#0086da] hover:text-white">
                    {{ $guestSecondaryLabel }}
                </a>
                <a href="{{ route('book') }}"
                    class="inline-flex items-center gap-[9px] whitespace-nowrap bg-[#0086da] px-6 py-3 text-[.7rem] font-bold uppercase tracking-[.1em] text-white transition duration-200 hover:-translate-y-px hover:bg-[#006ab0]">
                    Book Now
                </a>
            @endif
        </div>

        <button id="booking-ham-btn" aria-label="Toggle menu"
            class="{{ $isPatientUser ? 'hidden' : 'flex flex-col items-end gap-[5px] border-none bg-transparent p-2 lg:hidden' }}">
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
                    class="block border-b border-[#e4eff8] px-7 py-[17px] text-[.75rem] font-semibold uppercase tracking-[.08em] text-[#1a2e3b] no-underline transition hover:bg-[#f0f8fe] hover:text-[#0086da]">Book Appointment</a>
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
                <a href="{{ $guestSecondaryRoute }}"
                    class="block border-b border-[#e4eff8] px-7 py-[17px] text-[.75rem] font-semibold uppercase tracking-[.08em] text-[#1a2e3b] no-underline transition hover:bg-[#f0f8fe] hover:text-[#0086da]">{{ $guestSecondaryLabel }}</a>
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
        const profileWrap = document.getElementById('patient-profile-wrap');
        const profileBtn = document.getElementById('patient-profile-btn');
        const profileMenu = document.getElementById('patient-profile-menu');
        if (hamBtn && mobMenu) {
            hamBtn.addEventListener('click', () => {
                const open = mobMenu.classList.toggle('hidden') === false;
                hamBtn.classList.toggle('active', open);
                hamBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
            mobMenu.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    mobMenu.classList.add('hidden');
                    hamBtn.classList.remove('active');
                    hamBtn.setAttribute('aria-expanded', 'false');
                });
            });
        }

        const closeAll = () => {
            profileMenu?.classList.add('hidden');
            profileBtn?.setAttribute('aria-expanded', 'false');
        };

        if (profileBtn && profileMenu && profileWrap) {
            profileBtn.addEventListener('click', () => {
                const isOpen = !profileMenu.classList.contains('hidden');
                closeAll();
                if (!isOpen) {
                    profileMenu.classList.remove('hidden');
                    profileBtn.setAttribute('aria-expanded', 'true');
                }
            });
        }

        document.addEventListener('click', event => {
            if (profileWrap && !profileWrap.contains(event.target)) {
                closeAll();
            }
        });

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') {
                closeAll();
            }
        });
    });
</script>
