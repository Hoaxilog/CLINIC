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

    @include('components.homepage.header-section')

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
                        <a href="/book"
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
                            <div class="text-[2.6rem] leading-none font-extrabold tracking-[-.03em] text-white">5</div>
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
                <a href="/book"
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
                @foreach ($services as $idx => $s)
                    <div @class([
                        'service-card reveal relative overflow-hidden border-r border-b border-[#e4eff8] bg-white px-10 py-12 transition duration-300 hover:-translate-y-1 hover:shadow-[0_20px_48px_rgba(0,134,218,.1)] before:absolute before:top-0 before:right-0 before:left-0 before:h-[3px] before:bg-transparent hover:before:bg-[#0086da]',
                        'hidden' => $idx >= 4,
                    ])>
                        <div class="mb-8 flex items-start justify-between">
                            <span
                                class="select-none text-[2rem] leading-none font-extrabold tracking-[-.04em] text-[#d4e8f5]">{{ $s['num'] }}</span>
                        </div>
                        <h3 class="mb-3 text-[.98rem] font-bold tracking-[-.01em] text-[#1a2e3b]">{{ $s['title'] }}
                        </h3>
                        <p class="mb-7 text-[.81rem] leading-[1.8] text-[#3d5a6e]">{{ $s['summary'] }}</p>
                        <a href="{{ route('services.show', $s['slug']) }}"
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
            @if (count($services) > 4)
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
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-user-icon lucide-user">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
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
                    <a href="/book"
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
                                'Book, reschedule, or cancel anytime through our seamless online patient portal, quick and effortless.',
                            'icon' =>
                                '<rect x="3" y="4" width="18" height="18" stroke="#0086da" stroke-width="2" fill="none" stroke-linecap="square"/><path d="M16 2v4M8 2v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01" stroke="#0086da" stroke-width="2" stroke-linecap="square"/>',
                            'vb' => '0 0 24 24',
                        ],
                        [
                            'title' => 'Smart Reminders',
                            'desc' =>
                                'Automated email notifications keep you updated so you never miss a scheduled dental visit.',
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
                        We're Open<br><span class="font-light italic text-[#0086da]">5 Days a Week.</span>
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

    <section id="contact" class="border-t border-[#e4eff8] bg-[#f6fafd] px-6 py-[88px] md:px-12 xl:px-20">
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
                        <a href="/book"
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

    @include('components.homepage.footer-section')
    <script>
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

