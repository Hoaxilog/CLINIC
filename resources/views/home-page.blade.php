<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tejada Clinic – Dental Care, Diliman QC</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400&display=swap"
        rel="stylesheet">
    @vite('resources/css/app.css')

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(26px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fu {
            animation: fadeUp .7s cubic-bezier(.22, 1, .36, 1) both;
        }

        .d1 {
            animation-delay: .10s;
        }

        .d2 {
            animation-delay: .22s;
        }

        .d3 {
            animation-delay: .36s;
        }

        .d4 {
            animation-delay: .50s;
        }

        .d5 {
            animation-delay: .64s;
        }

        .reveal {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity .6s cubic-bezier(.22, 1, .36, 1), transform .6s cubic-bezier(.22, 1, .36, 1);
        }

        .reveal.in {
            opacity: 1;
            transform: translateY(0);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: #0086da;
            transition: width .25s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        #bar1,
        #bar2,
        #bar3 {
            display: block;
            height: 2px;
            transform-origin: center;
            transition: transform .25s ease, opacity .2s ease, width .2s ease;
        }

        #bar1,
        #bar2 {
            width: 22px;
            background: #1a2e3b;
        }

        #bar3 {
            width: 14px;
            background: #0086da;
        }

        #ham-btn.active #bar1 {
            transform: translateY(7px) rotate(45deg);
        }

        #ham-btn.active #bar2 {
            opacity: 0;
            transform: scaleX(0);
        }

        #ham-btn.active #bar3 {
            transform: translateY(-7px) rotate(-45deg);
            width: 22px;
        }
    </style>
</head>

<body class="overflow-x-hidden bg-white text-[#1a2e3b] antialiased">

    <header class="sticky top-0 z-[100] border-b border-[#e4eff8] bg-white px-6 md:px-12 xl:px-20">
        <div class="relative mx-auto flex h-[70px] w-full max-w-[1400px] items-center justify-between">
            <a href="/" class="flex shrink-0 items-center gap-3 no-underline">
                <div class="flex h-[38px] w-[38px] shrink-0 items-center justify-center">
                    <svg width="56" height="45" viewBox="0 0 56 45" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <mask id="mask0_664_212" mask-type="alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="56"
                            height="45">
                            <path
                                d="M11.783 0.465134C6.04622 2.04593 1.64903 6.81758 0.396845 12.7602C-0.127324 15.307 -0.127324 16.9171 0.367724 19.3468C1.70727 25.6993 7.88082 33.5154 18.5972 42.444L21.0724 44.5225L21.3927 43.1173C22.1499 39.8972 23.402 37.9944 25.6152 36.7941C27.2751 35.8574 28.3525 35.9159 30.158 36.9698C31.5849 37.8187 33.2739 40.5412 33.7398 42.7367C33.9437 43.7321 34.1766 44.5225 34.264 44.5225C34.5261 44.5225 40.8161 39.0775 43.5243 36.5307C51.7363 28.7438 56.0461 20.9862 55.4637 15.0436C55.0269 10.711 53.2797 7.05178 50.2511 4.24147C44.2814 -1.37913 35.4579 -1.4084 29.5756 4.12438L27.7701 5.82227L25.9646 4.15365C22.9361 1.34335 19.4708 -0.00325012 15.3939 0.0552979C14.2 0.0552979 12.5692 0.260216 11.783 0.465134ZM32.7206 9.36442C38.7486 12.3504 41.1947 19.4932 38.1953 25.4066C37.2634 27.2801 34.7008 29.8269 32.808 30.7344C27.0712 33.4862 20.0532 31.0857 17.2867 25.4066C16.0054 22.7134 15.6851 20.6934 16.151 17.9417C16.7626 14.341 19.1796 11.0916 22.5284 9.42297C24.596 8.39838 25.4405 8.22274 28.2943 8.33983C30.4492 8.42765 31.119 8.57402 32.7206 9.36442Z"
                                fill="black" />
                            <path
                                d="M24.0136 9.97903C21.0142 11.15 18.9757 13.2577 17.7235 16.4193C16.7917 18.7612 16.9664 22.3619 18.1021 24.616C19.2378 26.8116 21.2471 28.8022 23.3729 29.7975C24.9163 30.5001 25.4987 30.6172 27.7701 30.6172C29.9833 30.6172 30.653 30.5001 32.0217 29.8561C39.1271 26.4896 40.5831 17.5024 34.8755 12.2039C32.6624 10.1547 30.7113 9.39355 27.6828 9.39355C26.1976 9.42283 24.9745 9.59847 24.0136 9.97903ZM31.0316 13.4334C30.9151 14.0188 30.9151 15.0142 31.0025 15.5996L31.1772 16.6828L33.2739 16.7706L35.3414 16.8584V20.0493V23.2694L33.3613 23.2987C32.2838 23.328 31.3228 23.4451 31.2063 23.5329C31.119 23.65 31.0025 24.616 31.0025 25.6992L30.9734 27.6898L27.6828 27.7776L24.4213 27.8654V25.6699V23.5036L23.0526 23.328C22.2663 23.2401 21.3345 23.2109 20.9268 23.2401L20.1988 23.2987L20.1114 19.9907L20.0241 16.712H22.2372H24.4213V14.5165V12.321H27.7992H31.2063L31.0316 13.4334Z"
                                fill="black" />
                        </mask>
                        <g mask="url(#mask0_664_212)">
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
                @foreach (['Services' => 'services', 'About' => 'about', 'Why Us' => 'why-us', 'Hours' => 'hours', 'Contact' => 'contact'] as $label => $id)
                    <a href="#{{ $id }}"
                        class="nav-link relative text-[.72rem] font-semibold uppercase tracking-[.07em] text-[#1a2e3b] transition-colors duration-200 hover:text-[#0086da]">{{ $label }}</a>
                @endforeach
            </nav>

            <div class="hidden items-center gap-3 lg:flex">
                <a href="{{ route('login') }}"
                    class="inline-flex items-center gap-[9px] whitespace-nowrap border border-[#0086da] px-6 py-3 text-[.7rem] font-bold uppercase tracking-[.1em] text-[#0086da] transition duration-200 hover:-translate-y-px hover:bg-[#0086da] hover:text-white">
                    Login
                </a>
                <a href="/book"
                    class="inline-flex items-center gap-[9px] whitespace-nowrap bg-[#0086da] px-6 py-3 text-[.7rem] font-bold uppercase tracking-[.1em] text-white transition duration-200 hover:-translate-y-px hover:bg-[#006ab0]">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="square">
                        <rect x="3" y="4" width="18" height="18" />
                        <path d="M16 2v4M8 2v4M3 10h18" />
                    </svg>
                    Book Now
                </a>
            </div>

            <button id="ham-btn" aria-label="Toggle menu"
                class="flex flex-col items-end gap-[5px] border-none bg-transparent p-2 lg:hidden">
                <span id="bar1"></span>
                <span id="bar2"></span>
                <span id="bar3"></span>
            </button>

            <div id="mob-menu"
                class="absolute top-full right-0 left-0 z-[200] hidden border-t border-[#e4eff8] bg-white shadow-[0_8px_32px_rgba(0,0,0,.08)]">
                @foreach (['Services' => 'services', 'About' => 'about', 'Why Us' => 'why-us', 'Hours' => 'hours', 'Contact' => 'contact'] as $label => $id)
                    <a href="#{{ $id }}"
                        class="block border-b border-[#e4eff8] px-7 py-[17px] text-[.75rem] font-semibold uppercase tracking-[.08em] text-[#1a2e3b] no-underline transition hover:bg-[#f0f8fe] hover:text-[#0086da]">{{ $label }}</a>
                @endforeach
                <a href="{{ route('login') }}"
                    class="block border-b border-[#e4eff8] px-7 py-[17px] text-[.75rem] font-semibold uppercase tracking-[.08em] text-[#1a2e3b] no-underline transition hover:bg-[#f0f8fe] hover:text-[#0086da]">Login</a>
                <div class="px-7 pt-5 pb-6">
                    <a href="#appointment"
                        class="inline-flex w-full items-center justify-center gap-[9px] whitespace-nowrap bg-[#0086da] px-8 py-[15px] text-[.72rem] font-bold uppercase tracking-[.1em] text-white transition duration-200 hover:bg-[#006ab0]">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="square">
                            <rect x="3" y="4" width="18" height="18" />
                            <path d="M16 2v4M8 2v4M3 10h18" />
                        </svg>
                        Book Appointment
                    </a>
                </div>
            </div>
        </div>
    </header>

    <section id="hero-wrap" class="bg-[#0086da] px-6 md:px-12 xl:px-20">
        <div class="mx-auto grid min-h-[calc(100vh-70px)] w-full max-w-[1400px] grid-cols-1 min-[1120px]:grid-cols-2">
            <div class="relative z-[2] flex flex-col justify-center py-24">
                <div class="max-w-[700px]">
                    <p class="fu d1 mb-6 text-[.66rem] font-bold uppercase tracking-[.26em] text-white/60">Diliman,
                        Quezon
                        City · Est. 2014</p>
                    <h1
                        class="fu d2 mb-[26px] text-[clamp(1.4rem,4.2vw,2.7rem)] leading-[1.1] font-extrabold tracking-[-.02em] text-white lg:text-[2.2rem] xl:text-[2.45rem] 2xl:text-[2.65rem]">
                        <span class="block whitespace-nowrap">Your Smile, Our Priority.</span>
                        <span class="block font-light italic text-white/75">Trusted Dental Care in Diliman.</span>
                    </h1>
                    <p class="fu d3 mb-11 max-w-[440px] text-[.92rem] leading-[1.9] text-white/68">
                        Led by Dr. Shiela and Dr, Alan, Tejada Clinic has been delivering trusted, patient-first dental
                        care to
                        families
                        in Diliman for over 10 years.
                    </p>
                    <div class="fu d4 mb-16 flex flex-wrap gap-[14px]">
                        <a href="#appointment"
                            class="inline-flex items-center gap-[9px] whitespace-nowrap bg-white px-8 py-[15px] text-[.72rem] font-bold uppercase tracking-[.1em] text-[#0086da] transition hover:bg-[#e8f4fc]">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="square">
                                <rect x="3" y="4" width="18" height="18" />
                                <path d="M16 2v4M8 2v4M3 10h18" />
                            </svg>
                            Book Appointment
                        </a>
                        <a href="#services"
                            class="inline-flex items-center gap-[9px] whitespace-nowrap border border-white/45 bg-transparent px-8 py-[13px] text-[.72rem] font-semibold uppercase tracking-[.1em] text-white transition hover:border-white hover:bg-white/10">
                            Our Services
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5" stroke-linecap="square">
                                <path d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                    <div class="fu d5 grid grid-cols-3 border-t border-white/15 pt-11">
                        <div class="border-r border-white/15 pr-4">
                            <div class="text-[2.6rem] leading-none font-extrabold tracking-[-.03em] text-white">10<span
                                    class="text-[1.4rem] font-semibold opacity-60">+</span></div>
                            <div class="mt-2 text-[.6rem] font-semibold uppercase tracking-[.18em] text-white/80">Years
                                Experience</div>
                        </div>
                        <div class="border-r border-white/15 px-4">
                            <div class="text-[2.6rem] leading-none font-extrabold tracking-[-.03em] text-white">5K<span
                                    class="text-[1.4rem] font-semibold opacity-60">+</span></div>
                            <div class="mt-2 text-[.6rem] font-semibold uppercase tracking-[.18em] text-white/80">Happy
                                Patients</div>
                        </div>
                        <div class="pl-4">
                            <div class="text-[2.6rem] leading-none font-extrabold tracking-[-.03em] text-white">6</div>
                            <div class="mt-2 text-[.6rem] font-semibold uppercase tracking-[.18em] text-white/80">Days
                                Open Weekly</div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="hero-photo"
                class="relative hidden min-h-[500px] overflow-hidden bg-[#005a96] min-[1120px]:block">
                <img src="{{ asset('images/dentist.png') }}" alt="Dr. Shiela"
                    class="block h-full w-full object-cover object-top">
                <div
                    class="absolute right-0 bottom-0 left-0 bg-gradient-to-t from-[rgba(0,74,124,.95)] to-transparent px-8 pt-14 pb-10 lg:px-10">
                    <div class="mb-[7px] text-[.58rem] font-bold uppercase tracking-[.22em] text-white/50">Lead Dentist
                    </div>
                    <div class="mb-2.5 text-[1.7rem] font-bold tracking-[-.01em] text-white">Dr. Shiela</div>
                    <div class="max-w-[300px] text-[.82rem] leading-[1.7] italic text-white/60">"We don't just treat
                        teeth; we care for the people behind the smiles."</div>
                </div>
            </div>
        </div>
    </section>

    <section id="services" class="border-t border-[#e4eff8] bg-white px-6 py-[88px] md:px-12 xl:px-20">
        <div class="mx-auto w-full max-w-[1400px]">
            <div class="mb-14 flex flex-wrap items-end justify-between gap-6 border-b border-[#e4eff8] pb-14">
                <div>
                    <div
                        class="reveal mb-4 inline-flex items-center gap-[10px] text-[.63rem] font-bold uppercase tracking-[.22em] text-[#0086da]">
                        <span class="block h-[2px] w-[22px] bg-[#0086da]"></span>What We Offer
                    </div>
                    <h2
                        class="reveal text-[clamp(1.9rem,3vw,2.9rem)] leading-[1.1] font-extrabold tracking-[-.025em] text-[#1a2e3b]">
                        Dental Services<br><span class="font-light italic text-[#0086da]">For Every Smile</span>
                    </h2>
                </div>
                <a href="#appointment"
                    class="reveal inline-flex items-center gap-[9px] whitespace-nowrap bg-[#0086da] px-8 py-[15px] text-[.72rem] font-bold uppercase tracking-[.1em] text-white transition hover:bg-[#006ab0]">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="square">
                        <rect x="3" y="4" width="18" height="18" />
                        <path d="M16 2v4M8 2v4M3 10h18" />
                    </svg>
                    Book Appointment
                </a>
            </div>

            <div class="grid grid-cols-1 border border-[#e4eff8] bg-[#e4eff8] sm:grid-cols-2 lg:grid-cols-4">
                @php
                    $svcs = [
                        [
                            'num' => '01',
                            'title' => 'General Checkup',
                            'desc' =>
                                'Comprehensive oral exams, professional cleaning, X-rays, and preventive care to keep your teeth healthy long-term.',
                            'icon' =>
                                '<path d="M10 1.5C7.6 1.5 5.7 2.8 4.1 4.5C2.5 6.2 1.5 8.2 1.5 10.5C1.5 13.2 2.5 15.8 3.5 17.9C4.5 20 5.6 22 7 22.3C8 22.5 8.5 21.6 9.1 20.1C9.6 18.8 9.9 17.6 10 17.6C10.1 17.6 10.4 18.8 10.9 20.1C11.5 21.6 12 22.5 13 22.3C14.4 22 15.5 20 16.5 17.9C17.5 15.8 18.5 13.2 18.5 10.5C18.5 8.2 17.5 6.2 15.9 4.5C14.3 2.8 12.4 1.5 10 1.5Z" fill="#0086da"/>',
                            'vb' => '0 0 20 23',
                        ],
                        [
                            'num' => '02',
                            'title' => 'Orthodontics',
                            'desc' =>
                                'Braces and modern clear aligners crafted to straighten teeth and correct bite issues for children, teens, and adults.',
                            'icon' =>
                                '<path d="M3 5h18M3 9h18M3 13h18M3 17h18" stroke="#0086da" stroke-width="2" stroke-linecap="square"/><circle cx="7" cy="5" r="2" stroke="#0086da" stroke-width="2" fill="none"/><circle cx="13" cy="9" r="2" stroke="#0086da" stroke-width="2" fill="none"/><circle cx="17" cy="13" r="2" stroke="#0086da" stroke-width="2" fill="none"/>',
                            'vb' => '0 0 24 24',
                        ],
                        [
                            'num' => '03',
                            'title' => 'Teeth Whitening',
                            'desc' =>
                                'Professional-grade whitening that safely lifts stubborn stains and restores the natural brightness of your smile.',
                            'icon' =>
                                '<circle cx="12" cy="12" r="4" stroke="#0086da" stroke-width="2" fill="none"/><path d="M12 2v3M12 19v3M4.22 4.22l2.12 2.12M17.66 17.66l2.12 2.12M2 12h3M19 12h3M4.22 19.78l2.12-2.12M17.66 6.34l2.12-2.12" stroke="#0086da" stroke-width="2" stroke-linecap="square"/>',
                            'vb' => '0 0 24 24',
                        ],
                        [
                            'num' => '04',
                            'title' => 'Oral Surgery',
                            'desc' =>
                                'Tooth extractions, implant placement, and minor surgical procedures performed by our experienced dental team.',
                            'icon' =>
                                '<path d="M9 3H5a2 2 0 00-2 2v4M9 3h6M9 3v18m6-18h4a2 2 0 012 2v4M15 3v18m0 0H9m6 0h4a2 2 0 002-2V9M3 9h18" stroke="#0086da" stroke-width="2" stroke-linecap="square" fill="none"/>',
                            'vb' => '0 0 24 24',
                        ],
                        [
                            'num' => '05',
                            'title' => 'Dental Fillings',
                            'desc' =>
                                'Tooth-colored composite fillings that repair cavities and restore strength while keeping a natural look.',
                            'icon' =>
                                '<path d="M4 10c0-4 3-7 8-7s8 3 8 7c0 6-4 11-8 11s-8-5-8-11Z" stroke="#0086da" stroke-width="2" fill="none"/><path d="M9 10h6" stroke="#0086da" stroke-width="2" stroke-linecap="square"/>',
                            'vb' => '0 0 24 24',
                        ],
                        [
                            'num' => '06',
                            'title' => 'Root Canal Therapy',
                            'desc' =>
                                'Pain-relieving root canal treatment that saves infected teeth and prevents further oral complications.',
                            'icon' =>
                                '<path d="M12 3v18M8 6v12M16 6v12" stroke="#0086da" stroke-width="2" stroke-linecap="square"/><path d="M6 4h12" stroke="#0086da" stroke-width="2" stroke-linecap="square"/>',
                            'vb' => '0 0 24 24',
                        ],
                        [
                            'num' => '07',
                            'title' => 'Dental Crowns',
                            'desc' =>
                                'Custom-made crowns that protect damaged teeth and restore full function and appearance.',
                            'icon' =>
                                '<path d="M5 8l2-4 5 3 5-3 2 4-2 10H7L5 8Z" stroke="#0086da" stroke-width="2" fill="none"/>',
                            'vb' => '0 0 24 24',
                        ],
                        [
                            'num' => '08',
                            'title' => 'Dental Bridges',
                            'desc' =>
                                'Fixed bridge solutions to replace missing teeth and bring back comfortable chewing and confidence.',
                            'icon' =>
                                '<circle cx="6" cy="12" r="3" stroke="#0086da" stroke-width="2" fill="none"/><circle cx="18" cy="12" r="3" stroke="#0086da" stroke-width="2" fill="none"/><rect x="9" y="10" width="6" height="4" stroke="#0086da" stroke-width="2" fill="none"/>',
                            'vb' => '0 0 24 24',
                        ],
                        [
                            'num' => '09',
                            'title' => 'Dentures',
                            'desc' =>
                                'Full and partial dentures designed for comfort, stability, and natural-looking smiles.',
                            'icon' =>
                                '<path d="M4 12c0 4 3.5 7 8 7s8-3 8-7" stroke="#0086da" stroke-width="2" fill="none"/><path d="M6 12h12" stroke="#0086da" stroke-width="2" stroke-linecap="square"/>',
                            'vb' => '0 0 24 24',
                        ],
                        [
                            'num' => '10',
                            'title' => 'Pediatric Dentistry',
                            'desc' =>
                                'Gentle dental care for children focused on prevention, comfort, and healthy oral habits.',
                            'icon' =>
                                '<circle cx="12" cy="10" r="5" stroke="#0086da" stroke-width="2" fill="none"/><path d="M7 20c1.2-2 2.8-3 5-3s3.8 1 5 3" stroke="#0086da" stroke-width="2" fill="none"/>',
                            'vb' => '0 0 24 24',
                        ],
                        [
                            'num' => '11',
                            'title' => 'Periodontal Care',
                            'desc' =>
                                'Specialized gum treatment to manage gingivitis and periodontitis and protect long-term oral health.',
                            'icon' =>
                                '<path d="M4 6h16M6 10h12M8 14h8M10 18h4" stroke="#0086da" stroke-width="2" stroke-linecap="square"/>',
                            'vb' => '0 0 24 24',
                        ],
                        [
                            'num' => '12',
                            'title' => 'Tooth Extraction',
                            'desc' =>
                                'Safe extractions for severely damaged or problematic teeth with careful aftercare support.',
                            'icon' =>
                                '<path d="M8 4l8 8M16 4l-8 8" stroke="#0086da" stroke-width="2" stroke-linecap="square"/><path d="M7 13c0 4 2 7 5 7s5-3 5-7" stroke="#0086da" stroke-width="2" fill="none"/>',
                            'vb' => '0 0 24 24',
                        ],
                        [
                            'num' => '13',
                            'title' => 'Dental Implants',
                            'desc' =>
                                'Durable implant restorations that replace missing teeth and restore bite stability.',
                            'icon' =>
                                '<path d="M12 3v14" stroke="#0086da" stroke-width="2" stroke-linecap="square"/><circle cx="12" cy="19" r="3" stroke="#0086da" stroke-width="2" fill="none"/>',
                            'vb' => '0 0 24 24',
                        ],
                        [
                            'num' => '14',
                            'title' => 'Veneers',
                            'desc' => 'Thin porcelain veneers to improve shape, color, and overall smile symmetry.',
                            'icon' =>
                                '<rect x="6" y="4" width="12" height="16" rx="3" stroke="#0086da" stroke-width="2" fill="none"/><path d="M9 8h6" stroke="#0086da" stroke-width="2" stroke-linecap="square"/>',
                            'vb' => '0 0 24 24',
                        ],
                        [
                            'num' => '15',
                            'title' => 'TMJ Management',
                            'desc' =>
                                'Assessment and treatment options for jaw pain, clenching, and bite-related discomfort.',
                            'icon' =>
                                '<path d="M4 8h16M6 12h12M8 16h8" stroke="#0086da" stroke-width="2" stroke-linecap="square"/><circle cx="7" cy="8" r="1" fill="#0086da"/><circle cx="17" cy="8" r="1" fill="#0086da"/>',
                            'vb' => '0 0 24 24',
                        ],
                        [
                            'num' => '16',
                            'title' => 'Emergency Dental Care',
                            'desc' =>
                                'Urgent dental treatment for sudden pain, trauma, and other immediate oral concerns.',
                            'icon' =>
                                '<path d="M13 2L6 13h5l-1 9 8-12h-5l0-8Z" stroke="#0086da" stroke-width="2" fill="none" stroke-linejoin="round"/>',
                            'vb' => '0 0 24 24',
                        ],
                    ];
                @endphp
                @foreach ($svcs as $idx => $s)
                    <div @class([
                        'service-card reveal relative overflow-hidden border-r border-b border-[#e4eff8] bg-white px-10 py-12 transition duration-300 hover:-translate-y-1 hover:shadow-[0_20px_48px_rgba(0,134,218,.1)] before:absolute before:top-0 before:right-0 before:left-0 before:h-[3px] before:bg-transparent hover:before:bg-[#0086da]',
                        'hidden' => $idx >= 4,
                    ])>
                        <div class="mb-8 flex items-start justify-between">
                            <div class="flex h-[52px] w-[52px] shrink-0 items-center justify-center bg-[#e8f4fc]">
                                <svg width="24" height="24" viewBox="{{ $s['vb'] }}"
                                    fill="none">{!! $s['icon'] !!}</svg>
                            </div>
                            <span
                                class="select-none text-[2rem] leading-none font-extrabold tracking-[-.04em] text-[#d4e8f5]">{{ $s['num'] }}</span>
                        </div>
                        <h3 class="mb-3 text-[.98rem] font-bold tracking-[-.01em] text-[#1a2e3b]">{{ $s['title'] }}
                        </h3>
                        <p class="mb-7 text-[.81rem] leading-[1.8] text-[#3d5a6e]">{{ $s['desc'] }}</p>
                        <a href="#appointment"
                            class="inline-flex items-center gap-[7px] text-[.67rem] font-bold uppercase tracking-[.1em] text-[#0086da] no-underline transition-[gap] hover:gap-[13px]">
                            Learn More
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5" stroke-linecap="square">
                                <path d="M5 12h14M12 5l7 7-7 7" />
                            </svg>

                        </a>
                    </div>
                @endforeach
            </div>
            @if (count($svcs) > 4)
                <div class="mt-8 flex justify-center">
                    <button id="view-more-services" type="button"
                        class="inline-flex items-center gap-[9px] whitespace-nowrap border border-[#0086da] bg-transparent px-8 py-[13px] text-[.72rem] font-bold uppercase tracking-[.1em] text-[#0086da] transition hover:bg-[#0086da] hover:text-white">
                        View More Services
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="square">
                            <path d="M5 12h14M12 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            @endif
        </div>
    </section>

    <section id="about" class="border-t border-[#e4eff8] bg-[#f6fafd] px-6 py-24 md:px-12 xl:px-20">
        <div class="mx-auto w-full max-w-[1400px]">
            <div class="mb-16 grid grid-cols-1 gap-12 md:grid-cols-2 md:items-end">
                <div>
                    <div
                        class="reveal mb-4 inline-flex items-center gap-[10px] text-[.63rem] font-bold uppercase tracking-[.22em] text-[#0086da]">
                        <span class="block h-[2px] w-[22px] bg-[#0086da]"></span>About the Clinic
                    </div>
                    <h2
                        class="reveal text-[clamp(1.9rem,3vw,2.9rem)] leading-[1.1] font-extrabold tracking-[-.025em] text-[#1a2e3b]">
                        A Clinic That Feels<br><span class="font-light italic text-[#0086da]">Like Home.</span>
                    </h2>
                </div>
                <div class="reveal flex flex-col justify-end">
                    <p class="text-[.88rem] leading-[1.9] text-[#3d5a6e]">
                        Since 2014, Tejada Clinic has served families in Diliman with gentle, thorough dental care. We
                        combine clinical precision with genuine warmth — because we believe dentistry should feel less
                        clinical and more human.
                    </p>
                </div>
            </div>

            <div class="mb-12 grid grid-cols-1 gap-[2px] bg-[#e4eff8] md:grid-cols-3">
                <div class="reveal bg-white px-9 py-10">
                    <div class="mb-5 flex h-11 w-11 items-center justify-center bg-[#e8f4fc]">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0086da"
                            stroke-width="2" stroke-linecap="square">
                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                    </div>
                    <div class="mb-2.5 text-[.72rem] font-bold uppercase tracking-[.14em] text-[#0086da]">Meet the
                        Doctor</div>
                    <div class="mb-2 text-[1.05rem] font-bold text-[#1a2e3b]">Dr. Shiela</div>
                    <p class="text-[.82rem] leading-[1.75] text-[#3d5a6e]">Lead dentist with over 10 years of clinical
                        experience in general dentistry, orthodontics, and oral surgery.</p>
                </div>

                <div class="reveal bg-white px-9 py-10">
                    <div class="mb-5 flex h-11 w-11 items-center justify-center bg-[#e8f4fc]">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0086da"
                            stroke-width="2" stroke-linecap="square">
                            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                            <polyline points="9 22 9 12 15 12 15 22" />
                        </svg>
                    </div>
                    <div class="mb-2.5 text-[.72rem] font-bold uppercase tracking-[.14em] text-[#0086da]">Our Clinic
                    </div>
                    <div class="mb-2 text-[1.05rem] font-bold text-[#1a2e3b]">Diliman, Since 2014</div>
                    <p class="text-[.82rem] leading-[1.75] text-[#3d5a6e]">Conveniently located at 251 Commonwealth
                        Ave,
                        Diliman — accessible to families across Quezon City and nearby areas.</p>
                </div>

                <div class="reveal bg-white px-9 py-10">
                    <div class="mb-5 flex h-11 w-11 items-center justify-center bg-[#e8f4fc]">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0086da"
                            stroke-width="2" stroke-linecap="square">
                            <path
                                d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z" />
                        </svg>
                    </div>
                    <div class="mb-2.5 text-[.72rem] font-bold uppercase tracking-[.14em] text-[#0086da]">Our Promise
                    </div>
                    <div class="mb-2 text-[1.05rem] font-bold text-[#1a2e3b]">Patient-First Care</div>
                    <p class="text-[.82rem] leading-[1.75] text-[#3d5a6e]">Every patient deserves to feel comfortable,
                        informed, and genuinely cared for — not just treated. That's our commitment at every visit.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 items-center gap-6 md:grid-cols-[1fr_auto]">
                <div class="reveal border-l-[3px] border-[#0086da] bg-white px-7 py-[22px]">
                    <p class="mb-[14px] text-[.9rem] leading-[1.75] italic text-[#3d5a6e]">"We don't just treat teeth;
                        we care for the people behind the smiles."</p>
                    <div class="flex items-center gap-3">
                        <div class="flex h-[34px] w-[34px] shrink-0 items-center justify-center bg-[#0086da]">
                            <svg width="16" height="19" viewBox="0 0 20 23" fill="none">
                                <path
                                    d="M10 1.5C7.6 1.5 5.7 2.8 4.1 4.5C2.5 6.2 1.5 8.2 1.5 10.5C1.5 13.2 2.5 15.8 3.5 17.9C4.5 20 5.6 22 7 22.3C8 22.5 8.5 21.6 9.1 20.1C9.6 18.8 9.9 17.6 10 17.6C10.1 17.6 10.4 18.8 10.9 20.1C11.5 21.6 12 22.5 13 22.3C14.4 22 15.5 20 16.5 17.9C17.5 15.8 18.5 13.2 18.5 10.5C18.5 8.2 17.5 6.2 15.9 4.5C14.3 2.8 12.4 1.5 10 1.5Z"
                                    fill="white" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-[.83rem] font-bold text-[#1a2e3b]">Dr. Shiela</div>
                            <div class="mt-0.5 text-[.63rem] font-semibold uppercase tracking-[.12em] text-[#0086da]">
                                Lead
                                Dentist</div>
                        </div>
                    </div>
                </div>
                <div class="reveal flex shrink-0 flex-col gap-3">
                    <a href="#appointment"
                        class="inline-flex items-center gap-[9px] whitespace-nowrap bg-[#0086da] px-8 py-[15px] text-[.72rem] font-bold uppercase tracking-[.1em] text-white transition hover:bg-[#006ab0]">
                        Schedule a Visit
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="square">
                            <path d="M5 12h14M12 5l7 7-7 7" />
                        </svg>
                    </a>
                    <a href="#services"
                        class="inline-flex items-center gap-[9px] whitespace-nowrap border border-[#0086da] bg-transparent px-8 py-[13px] text-[.72rem] font-bold uppercase tracking-[.1em] text-[#0086da] transition hover:bg-[#0086da] hover:text-white">
                        View Services
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="why-us" class="border-t border-[#e4eff8] bg-white px-6 py-[88px] md:px-12 xl:px-20">
        <div class="mx-auto w-full max-w-[1400px]">
            <div class="mb-14 border-b border-[#e4eff8] pb-14">
                <div
                    class="reveal mb-4 inline-flex items-center gap-[10px] text-[.63rem] font-bold uppercase tracking-[.22em] text-[#0086da]">
                    <span class="block h-[2px] w-[22px] bg-[#0086da]"></span>Why Choose Us
                </div>
                <h2
                    class="reveal text-[clamp(1.9rem,3vw,2.9rem)] leading-[1.1] font-extrabold tracking-[-.025em] text-[#1a2e3b]">
                    What
                    Sets Tejada Clinic Apart</h2>
            </div>

            <div class="grid grid-cols-1 border border-[#e4eff8] bg-[#e4eff8] sm:grid-cols-2 lg:grid-cols-4">
                @php
                    $feats = [
                        [
                            'title' => 'Modern Equipment',
                            'desc' =>
                                'Latest dental tools and fully digital, paperless patient records for precise and efficient care at every visit.',
                            'icon' =>
                                '<rect x="2" y="3" width="20" height="14" stroke="#0086da" stroke-width="2" fill="none" stroke-linecap="square"/><path d="M8 21h8M12 17v4" stroke="#0086da" stroke-width="2" stroke-linecap="square"/>',
                            'vb' => '0 0 24 24',
                        ],
                        [
                            'title' => 'Experienced Dentists',
                            'desc' =>
                                'Dr. Shiela and our team bring over a decade of hands-on expertise in general and surgical dentistry.',
                            'icon' =>
                                '<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" stroke="#0086da" stroke-width="2" stroke-linecap="square" fill="none"/><circle cx="9" cy="7" r="4" stroke="#0086da" stroke-width="2" fill="none"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" stroke="#0086da" stroke-width="2" stroke-linecap="square" fill="none"/>',
                            'vb' => '0 0 24 24',
                        ],
                        [
                            'title' => 'Online Appointments',
                            'desc' =>
                                'Book, reschedule, or cancel anytime through our seamless online patient portal — quick and effortless.',
                            'icon' =>
                                '<rect x="3" y="4" width="18" height="18" stroke="#0086da" stroke-width="2" fill="none" stroke-linecap="square"/><path d="M16 2v4M8 2v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01" stroke="#0086da" stroke-width="2" stroke-linecap="square"/>',
                            'vb' => '0 0 24 24',
                        ],
                        [
                            'title' => 'Smart Reminders',
                            'desc' =>
                                'Automated SMS and email notifications keep you updated so you never miss a scheduled dental visit.',
                            'icon' =>
                                '<path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9" stroke="#0086da" stroke-width="2" fill="none" stroke-linecap="square"/><path d="M13.73 21a2 2 0 01-3.46 0" stroke="#0086da" stroke-width="2" stroke-linecap="square" fill="none"/>',
                            'vb' => '0 0 24 24',
                        ],
                    ];
                @endphp
                @foreach ($feats as $f)
                    <div
                        class="reveal border-r border-b border-[#e4eff8] bg-white px-10 py-11 transition duration-300 hover:bg-[#f0f8fe] hover:shadow-[inset_0_0_0_1.5px_#0086da]">
                        <div class="mb-[26px] flex h-[50px] w-[50px] items-center justify-center bg-[#e8f4fc]">
                            <svg width="22" height="22" viewBox="{{ $f['vb'] }}"
                                fill="none">{!! $f['icon'] !!}</svg>
                        </div>
                        <h3 class="mb-3 text-[.93rem] font-bold tracking-[-.01em] text-[#1a2e3b]">{{ $f['title'] }}
                        </h3>
                        <p class="text-[.81rem] leading-[1.8] text-[#3d5a6e]">{{ $f['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="hours" class="border-t border-[#e4eff8] bg-[#f6fafd] px-6 py-[88px] md:px-12 xl:px-20">
        <div class="mx-auto w-full max-w-[1400px]">
            <div class="grid grid-cols-1 items-start gap-12 lg:grid-cols-[1fr_2fr]">
                <div>
                    <div
                        class="reveal mb-4 inline-flex items-center gap-[10px] text-[.63rem] font-bold uppercase tracking-[.22em] text-[#0086da]">
                        <span class="block h-[2px] w-[22px] bg-[#0086da]"></span>Clinic Hours
                    </div>
                    <h2
                        class="reveal mb-4 text-[clamp(1.8rem,2.6vw,2.6rem)] leading-[1.1] font-extrabold tracking-[-.025em] text-[#1a2e3b]">
                        We're Open<br><span class="font-light italic text-[#0086da]">6 Days a Week.</span>
                    </h2>
                    <p class="reveal mb-8 max-w-[300px] text-[.86rem] leading-[1.85] text-[#3d5a6e]">Walk-ins welcome.
                        Booking ahead is recommended for faster service and guaranteed slots.</p>
                    <a href="#appointment"
                        class="reveal inline-flex items-center gap-[9px] whitespace-nowrap bg-[#0086da] px-8 py-[15px] text-[.72rem] font-bold uppercase tracking-[.1em] text-white transition hover:bg-[#006ab0]">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="square">
                            <rect x="3" y="4" width="18" height="18" />
                            <path d="M16 2v4M8 2v4M3 10h18" />
                        </svg>
                        Book Online
                    </a>
                </div>

                <div class="flex flex-wrap gap-3">
                    @php
                        $hrs = [
                            ['d' => 'Monday', 'h' => '9:00 AM – 6:00 PM', 'o' => true],
                            ['d' => 'Tuesday', 'h' => 'Closed', 'o' => false],
                            ['d' => 'Wednesday', 'h' => '9:00 AM – 6:00 PM', 'o' => true],
                            ['d' => 'Thursday', 'h' => '9:00 AM – 6:00 PM', 'o' => true],
                            ['d' => 'Friday', 'h' => '9:00 AM – 6:00 PM', 'o' => true],
                            ['d' => 'Saturday', 'h' => '9:00 AM – 6:00 PM', 'o' => true],
                            ['d' => 'Sunday', 'h' => 'Closed', 'o' => false],
                        ];
                    @endphp
                    @foreach ($hrs as $h)
                        <div
                            class="reveal flex min-w-[150px] flex-1 flex-col gap-1.5 border border-t-[3px] px-6 py-5 {{ $h['o'] ? 'border-[#e4eff8] border-t-[#0086da] bg-white' : 'border-[#d4e8f5] border-t-[#cbd5e1] bg-transparent' }}">
                            <span
                                class="text-[.62rem] font-bold uppercase tracking-[.15em] text-[#7a9db5]">{{ $h['d'] }}</span>
                            <span
                                class="text-[.86rem] {{ $h['o'] ? 'font-semibold text-[#1a2e3b]' : 'font-medium text-[#94a3b8]' }}">{{ $h['h'] }}</span>
                            <span class="mt-0.5 inline-flex items-center gap-[5px]">
                                <span
                                    class="inline-block h-1.5 w-1.5 rounded-full {{ $h['o'] ? 'bg-[#0086da]' : 'bg-[#cbd5e1]' }}"></span>
                                <span
                                    class="text-[.6rem] font-semibold {{ $h['o'] ? 'text-[#0086da]' : 'text-[#94a3b8]' }}">{{ $h['o'] ? 'Open' : 'Closed' }}</span>
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="border-t border-[#e4eff8] bg-white px-6 py-[88px] md:px-12 xl:px-20">
        <div class="mx-auto w-full max-w-[1400px]">
            <div class="grid grid-cols-1 items-start gap-16 lg:grid-cols-2">
                <div>
                    <div
                        class="reveal mb-4 inline-flex items-center gap-[10px] text-[.63rem] font-bold uppercase tracking-[.22em] text-[#0086da]">
                        <span class="block h-[2px] w-[22px] bg-[#0086da]"></span>Book an Appointment
                    </div>
                    <h2
                        class="reveal mb-6 text-[clamp(1.9rem,3vw,2.9rem)] leading-[1.1] font-extrabold tracking-[-.025em] text-[#1a2e3b]">
                        Ready for Your<br><span class="font-light italic text-[#0086da]">Next Visit?</span>
                    </h2>
                    <p class="reveal mb-10 max-w-[420px] text-[.88rem] leading-[1.9] text-[#3d5a6e]">Fill in the form
                        and
                        we'll confirm your slot as soon as possible. You can also reach us via phone or Facebook if you
                        prefer a quicker response.</p>

                    <div class="reveal flex flex-col gap-4">
                        <a href="tel:+639123456789"
                            class="inline-flex items-center gap-[14px] border border-[#e4eff8] bg-[#f6fafd] px-5 py-4 no-underline transition hover:border-[#0086da]">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0086da"
                                stroke-width="2" stroke-linecap="square">
                                <path
                                    d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.8 19.79 19.79 0 01.1 1.18 2 2 0 012.11 0h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 14.92z" />
                            </svg>
                            <div>
                                <div class="text-[.6rem] font-bold uppercase tracking-[.14em] text-[#0086da]">Phone /
                                    WhatsApp</div>
                                <div class="mt-0.5 text-[.88rem] font-semibold text-[#1a2e3b]">+63 912 345 6789</div>
                            </div>
                        </a>
                        <a href="https://facebook.com/TejaDentClinic" target="_blank"
                            class="inline-flex items-center gap-[14px] border border-[#e4eff8] bg-[#f6fafd] px-5 py-4 no-underline transition hover:border-[#0086da]">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0086da"
                                stroke-width="2" stroke-linecap="square">
                                <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z" />
                            </svg>
                            <div>
                                <div class="text-[.6rem] font-bold uppercase tracking-[.14em] text-[#0086da]">Facebook
                                </div>
                                <div class="mt-0.5 text-[.88rem] font-semibold text-[#1a2e3b]">
                                    facebook.com/TejaDentClinic
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <div id="appointment" class="reveal relative border border-[#e4eff8] bg-[#f6fafd] px-10 py-11">
                    <div class="absolute top-0 left-8 h-[3px] w-12 bg-[#0086da]"></div>
                    <form action="" method="POST">
                        @csrf
                        <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label
                                    class="mb-2 block text-[.63rem] font-bold uppercase tracking-[.13em] text-[#3d5a6e]">Full
                                    Name</label>
                                <input type="text" name="name" placeholder="Juan dela Cruz"
                                    class="w-full border border-[#d4e8f5] bg-white px-4 py-[13px] text-[.83rem] text-[#1a2e3b] outline-none transition placeholder:font-normal placeholder:text-[#a8c8dc] focus:border-[#0086da]">
                            </div>
                            <div>
                                <label
                                    class="mb-2 block text-[.63rem] font-bold uppercase tracking-[.13em] text-[#3d5a6e]">Phone
                                    /
                                    WhatsApp</label>
                                <input type="tel" name="phone" placeholder="+63 9XX XXX XXXX"
                                    class="w-full border border-[#d4e8f5] bg-white px-4 py-[13px] text-[.83rem] text-[#1a2e3b] outline-none transition placeholder:font-normal placeholder:text-[#a8c8dc] focus:border-[#0086da]">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label
                                class="mb-2 block text-[.63rem] font-bold uppercase tracking-[.13em] text-[#3d5a6e]">Email
                                Address</label>
                            <input type="email" name="email" placeholder="juan@example.com"
                                class="w-full border border-[#d4e8f5] bg-white px-4 py-[13px] text-[.83rem] text-[#1a2e3b] outline-none transition placeholder:font-normal placeholder:text-[#a8c8dc] focus:border-[#0086da]">
                        </div>
                        <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label
                                    class="mb-2 block text-[.63rem] font-bold uppercase tracking-[.13em] text-[#3d5a6e]">Service</label>
                                <select name="service"
                                    class="w-full cursor-pointer appearance-none border border-[#d4e8f5] bg-white bg-[url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%230086da' stroke-width='1.5' fill='none' stroke-linecap='square'/%3E%3C/svg%3E\")] bg-[position:right_14px_center] bg-no-repeat px-4 py-[13px] pr-10 text-[.83rem] text-[#1a2e3b] outline-none transition focus:border-[#0086da]">
                                    <option value="">Select a service</option>
                                    <option>General Checkup</option>
                                    <option>Orthodontics</option>
                                    <option>Teeth Whitening</option>
                                    <option>Oral Surgery</option>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="mb-2 block text-[.63rem] font-bold uppercase tracking-[.13em] text-[#3d5a6e]">Preferred
                                    Date</label>
                                <input type="date" name="date"
                                    class="w-full border border-[#d4e8f5] bg-white px-4 py-[13px] text-[.83rem] text-[#1a2e3b] outline-none transition focus:border-[#0086da]">
                            </div>
                        </div>
                        <div class="mb-7">
                            <label
                                class="mb-2 block text-[.63rem] font-bold uppercase tracking-[.13em] text-[#3d5a6e]">Notes
                                (Optional)</label>
                            <textarea name="notes" rows="3" placeholder="Any concerns or questions?"
                                class="w-full resize-none border border-[#d4e8f5] bg-white px-4 py-[13px] text-[.83rem] text-[#1a2e3b] outline-none transition placeholder:font-normal placeholder:text-[#a8c8dc] focus:border-[#0086da]"></textarea>
                        </div>
                        <button type="submit"
                            class="inline-flex w-full items-center justify-center gap-[9px] whitespace-nowrap bg-[#0086da] px-8 py-[15px] text-[.72rem] font-bold uppercase tracking-[.1em] text-white transition hover:bg-[#006ab0]">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5" stroke-linecap="square">
                                <line x1="22" y1="2" x2="11" y2="13" />
                                <polygon points="22 2 15 22 11 13 2 9 22 2" fill="none" stroke="currentColor"
                                    stroke-width="2" />
                            </svg>
                            Submit Request
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="border-t border-[#e4eff8] bg-[#f6fafd] px-6 py-[88px] md:px-12 xl:px-20">
        <div class="mx-auto w-full max-w-[1400px]">
            <div class="grid grid-cols-1 border border-[#e4eff8] lg:grid-cols-[1fr_1.6fr]">
                <div class="flex flex-col justify-center border-r border-[#e4eff8] bg-white px-12 py-14">
                    <div
                        class="reveal mb-4 inline-flex items-center gap-[10px] text-[.63rem] font-bold uppercase tracking-[.22em] text-[#0086da]">
                        <span class="block h-[2px] w-[22px] bg-[#0086da]"></span>Find Us
                    </div>
                    <h3
                        class="reveal mb-8 text-[1.6rem] leading-[1.15] font-extrabold tracking-[-.02em] text-[#1a2e3b]">
                        Visit
                        the Clinic</h3>

                    <div class="flex flex-col border-t border-[#e4eff8]">
                        <div class="reveal flex items-start gap-4 border-b border-[#e4eff8] py-[22px]">
                            <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center bg-[#e8f4fc]">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none"
                                    stroke="#0086da" stroke-width="2" stroke-linecap="square">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>
                            </div>
                            <div>
                                <div class="mb-[5px] text-[.6rem] font-bold uppercase tracking-[.16em] text-[#0086da]">
                                    Address</div>
                                <div class="text-[.9rem] leading-[1.5] font-medium text-[#1a2e3b]">251 Commonwealth
                                    Ave,<br>Diliman,
                                    Quezon City</div>
                            </div>
                        </div>

                        <div class="reveal flex items-start gap-4 border-b border-[#e4eff8] py-[22px]">
                            <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center bg-[#e8f4fc]">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none"
                                    stroke="#0086da" stroke-width="2" stroke-linecap="square">
                                    <path
                                        d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.8 19.79 19.79 0 01.1 1.18 2 2 0 012.11 0h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 14.92z" />
                                </svg>
                            </div>
                            <div>
                                <div class="mb-[5px] text-[.6rem] font-bold uppercase tracking-[.16em] text-[#0086da]">
                                    Phone /
                                    WhatsApp</div>
                                <a href="tel:+639123456789"
                                    class="text-[.9rem] font-semibold text-[#1a2e3b] no-underline transition hover:text-[#0086da]">+63
                                    912 345 6789</a>
                            </div>
                        </div>

                        <div class="reveal flex items-start gap-4 border-b border-[#e4eff8] py-[22px]">
                            <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center bg-[#e8f4fc]">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none"
                                    stroke="#0086da" stroke-width="2" stroke-linecap="square">
                                    <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z" />
                                </svg>
                            </div>
                            <div>
                                <div class="mb-[5px] text-[.6rem] font-bold uppercase tracking-[.16em] text-[#0086da]">
                                    Facebook</div>
                                <a href="https://facebook.com/TejaDentClinic" target="_blank"
                                    class="text-[.9rem] font-semibold text-[#1a2e3b] no-underline transition hover:text-[#0086da]">facebook.com/TejaDentClinic</a>
                            </div>
                        </div>

                        <div class="reveal flex items-start gap-4 py-[22px]">
                            <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center bg-[#e8f4fc]">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none"
                                    stroke="#0086da" stroke-width="2" stroke-linecap="square">
                                    <circle cx="12" cy="12" r="10" />
                                    <polyline points="12 6 12 12 16 14" />
                                </svg>
                            </div>
                            <div>
                                <div class="mb-[5px] text-[.6rem] font-bold uppercase tracking-[.16em] text-[#0086da]">
                                    Hours</div>
                                <div class="text-[.9rem] leading-[1.6] font-medium text-[#1a2e3b]">
                                    Mon, Wed – Sat: 9:00 AM – 6:00 PM<br>
                                    <span class="text-[.82rem] text-[#94a3b8]">Tue &amp; Sun: Closed</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="reveal mt-8">
                        <a href="#appointment"
                            class="inline-flex items-center gap-[9px] whitespace-nowrap bg-[#0086da] px-8 py-[15px] text-[.72rem] font-bold uppercase tracking-[.1em] text-white transition hover:bg-[#006ab0]">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5" stroke-linecap="square">
                                <rect x="3" y="4" width="18" height="18" />
                                <path d="M16 2v4M8 2v4M3 10h18" />
                            </svg>
                            Book Appointment
                        </a>
                    </div>
                </div>

                <div class="relative min-h-[480px] overflow-hidden">
                    <iframe src="https://maps.google.com/maps?q=251+Commonwealth+Ave+Diliman+Quezon+City&output=embed"
                        class="block h-full min-h-[480px] w-full border-0" allowfullscreen loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </section>

    <footer class="border-t-[3px] border-t-[#006ab0] bg-[#0086da] px-6 md:px-12 xl:px-20">
        <div class="mx-auto w-full max-w-[1400px]">
            <div class="flex flex-wrap justify-between gap-12 border-b border-white/10 py-[68px] pb-12">
                <div class="max-w-[260px]">
                    <div class="mb-5 flex items-center gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center bg-white/15">
                            <svg width="56" height="45" viewBox="0 0 56 45" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <mask id="mask0_664_212" mask-type="alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                                    width="56" height="45">
                                    <path
                                        d="M11.783 0.465134C6.04622 2.04593 1.64903 6.81758 0.396845 12.7602C-0.127324 15.307 -0.127324 16.9171 0.367724 19.3468C1.70727 25.6993 7.88082 33.5154 18.5972 42.444L21.0724 44.5225L21.3927 43.1173C22.1499 39.8972 23.402 37.9944 25.6152 36.7941C27.2751 35.8574 28.3525 35.9159 30.158 36.9698C31.5849 37.8187 33.2739 40.5412 33.7398 42.7367C33.9437 43.7321 34.1766 44.5225 34.264 44.5225C34.5261 44.5225 40.8161 39.0775 43.5243 36.5307C51.7363 28.7438 56.0461 20.9862 55.4637 15.0436C55.0269 10.711 53.2797 7.05178 50.2511 4.24147C44.2814 -1.37913 35.4579 -1.4084 29.5756 4.12438L27.7701 5.82227L25.9646 4.15365C22.9361 1.34335 19.4708 -0.00325012 15.3939 0.0552979C14.2 0.0552979 12.5692 0.260216 11.783 0.465134ZM32.7206 9.36442C38.7486 12.3504 41.1947 19.4932 38.1953 25.4066C37.2634 27.2801 34.7008 29.8269 32.808 30.7344C27.0712 33.4862 20.0532 31.0857 17.2867 25.4066C16.0054 22.7134 15.6851 20.6934 16.151 17.9417C16.7626 14.341 19.1796 11.0916 22.5284 9.42297C24.596 8.39838 25.4405 8.22274 28.2943 8.33983C30.4492 8.42765 31.119 8.57402 32.7206 9.36442Z"
                                        fill="black" />
                                    <path
                                        d="M24.0136 9.97903C21.0142 11.15 18.9757 13.2577 17.7235 16.4193C16.7917 18.7612 16.9664 22.3619 18.1021 24.616C19.2378 26.8116 21.2471 28.8022 23.3729 29.7975C24.9163 30.5001 25.4987 30.6172 27.7701 30.6172C29.9833 30.6172 30.653 30.5001 32.0217 29.8561C39.1271 26.4896 40.5831 17.5024 34.8755 12.2039C32.6624 10.1547 30.7113 9.39355 27.6828 9.39355C26.1976 9.42283 24.9745 9.59847 24.0136 9.97903ZM31.0316 13.4334C30.9151 14.0188 30.9151 15.0142 31.0025 15.5996L31.1772 16.6828L33.2739 16.7706L35.3414 16.8584V20.0493V23.2694L33.3613 23.2987C32.2838 23.328 31.3228 23.4451 31.2063 23.5329C31.119 23.65 31.0025 24.616 31.0025 25.6992L30.9734 27.6898L27.6828 27.7776L24.4213 27.8654V25.6699V23.5036L23.0526 23.328C22.2663 23.2401 21.3345 23.2109 20.9268 23.2401L20.1988 23.2987L20.1114 19.9907L20.0241 16.712H22.2372H24.4213V14.5165V12.321H27.7992H31.2063L31.0316 13.4334Z"
                                        fill="black" />
                                </mask>
                                <g mask="url(#mask0_664_212)">
                                    <rect x="-25.5311" y="-23.4609" width="106.265" height="91.7739"
                                        fill="#0086DA" />
                                </g>
                            </svg>
                        </div>
                        <div>
                            <div class="text-[.88rem] font-extrabold tracking-[.05em] text-white">TEJADA CLINIC</div>
                            <div class="text-[.57rem] font-semibold uppercase tracking-[.2em] text-white/50">Dental
                                Care
                            </div>
                        </div>
                    </div>
                    <p class="text-[.8rem] leading-[1.75] italic text-white/52">"We don't just treat teeth; we care for
                        the people behind the smiles."</p>
                </div>

                <div>
                    <div class="mb-[18px] text-[.6rem] font-bold uppercase tracking-[.2em] text-white/35">Quick Links
                    </div>
                    <div class="flex flex-col gap-[11px]">
                        @foreach (['Services' => 'services', 'About' => 'about', 'Why Us' => 'why-us', 'Hours' => 'hours', 'Contact' => 'contact'] as $label => $id)
                            <a href="#{{ $id }}"
                                class="text-[.8rem] font-medium text-white/60 no-underline transition hover:text-white">{{ $label }}</a>
                        @endforeach
                    </div>
                </div>

                <div>
                    <div class="mb-[18px] text-[.6rem] font-bold uppercase tracking-[.2em] text-white/35">Contact</div>
                    <div class="flex flex-col gap-[14px]">
                        <div class="flex items-start gap-[11px]">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                stroke="rgba(255,255,255,.4)" stroke-width="2" stroke-linecap="square"
                                class="mt-0.5 shrink-0">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" />
                                <circle cx="12" cy="10" r="3" />
                            </svg>
                            <span class="text-[.8rem] leading-[1.6] text-white/60">251 Commonwealth Ave,<br>Diliman,
                                Quezon City</span>
                        </div>
                        <div class="flex items-center gap-[11px]">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                stroke="rgba(255,255,255,.4)" stroke-width="2" stroke-linecap="square">
                                <path
                                    d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.8 19.79 19.79 0 01.1 1.18 2 2 0 012.11 0h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 14.92z" />
                            </svg>
                            <a href="tel:+639123456789" class="text-[.8rem] text-white/60 no-underline">+63 912 345
                                6789</a>
                        </div>
                        <div class="flex items-center gap-[11px]">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                stroke="rgba(255,255,255,.4)" stroke-width="2" stroke-linecap="square">
                                <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z" />
                            </svg>
                            <a href="https://facebook.com/TejaDentClinic" target="_blank"
                                class="text-[.8rem] text-white/60 no-underline">TejaDentClinic</a>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="mb-[18px] text-[.6rem] font-bold uppercase tracking-[.2em] text-white/35">Ready to
                        Visit?
                    </div>
                    <a href="#appointment"
                        class="mb-[14px] inline-flex items-center gap-[9px] whitespace-nowrap bg-white px-6 py-[13px] text-[.7rem] font-bold uppercase tracking-[.1em] text-[#0086da] transition hover:bg-[#e8f4fc]">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="square">
                            <rect x="3" y="4" width="18" height="18" />
                            <path d="M16 2v4M8 2v4M3 10h18" />
                        </svg>
                        Book Appointment
                    </a>
                    <p class="mt-3 text-[.7rem] leading-[1.6] text-white/38">Mon – Sat · 9:00 AM – 6:00 PM</p>
                </div>
            </div>

            <div
                class="mx-auto flex max-w-[1400px] flex-wrap items-center justify-between gap-3 px-6 py-5 md:px-12 xl:px-20">
                <p class="text-[.7rem] text-white/28">&copy; {{ date('Y') }} Tejada Clinic. All rights reserved.
                </p>
                <p class="text-[.7rem] text-white/28">251 Commonwealth Ave, Diliman, Quezon City</p>
            </div>
        </div>
    </footer>

    <script>
        const hamBtn = document.getElementById('ham-btn');
        const mobMenu = document.getElementById('mob-menu');

        hamBtn.addEventListener('click', () => {
            const open = mobMenu.classList.toggle('hidden') === false;
            hamBtn.classList.toggle('active', open);
            hamBtn.setAttribute('aria-expanded', open);
        });

        mobMenu.querySelectorAll('a').forEach(a => {
            a.addEventListener('click', () => {
                mobMenu.classList.add('hidden');
                hamBtn.classList.remove('active');
                hamBtn.setAttribute('aria-expanded', 'false');
            });
        });

        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', e => {
                const target = document.querySelector(a.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        const io = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const delay = parseFloat(el.dataset.revealDelay || 0);
                    setTimeout(() => el.classList.add('in'), delay);
                    io.unobserve(el);
                }
            });
        }, {
            threshold: 0.1
        });

        document.querySelectorAll('.reveal').forEach(el => {
            const siblings = Array.from(el.parentElement.children).filter(c => c.classList.contains('reveal'));
            el.dataset.revealDelay = siblings.indexOf(el) * 80;
            io.observe(el);
        });

        const serviceCards = Array.from(document.querySelectorAll('.service-card'));
        const viewMoreServicesBtn = document.getElementById('view-more-services');
        if (serviceCards.length && viewMoreServicesBtn) {
            let shownCount = Math.min(4, serviceCards.length);
            const batchSize = 4;

            const syncButton = () => {
                viewMoreServicesBtn.hidden = shownCount >= serviceCards.length;
            };

            viewMoreServicesBtn.addEventListener('click', () => {
                const nextCount = Math.min(shownCount + batchSize, serviceCards.length);
                serviceCards.slice(shownCount, nextCount).forEach((card, i) => {
                    card.classList.remove('hidden');
                    setTimeout(() => {
                        card.classList.add('in');
                    }, i * 80);
                });
                shownCount = nextCount;
                syncButton();
            });

            syncButton();
        }
    </script>
</body>

</html>
