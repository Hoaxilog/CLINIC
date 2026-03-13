<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Tejada Dental Clinic - Premium, compassionate dental care in Quezon City." />
  <title>Tejada Dental Clinic | Excellence in Oral Care</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,500;1,600&display=swap" rel="stylesheet" />

  @vite('resources/css/app.css')
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>

  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Outfit', 'sans-serif'],
            serif: ['Playfair Display', 'serif'],
          },
          colors: {
            brand: {
              50: '#f0f9ff',
              100: '#e0f2fe',
              200: '#bae6fd',
              500: '#0082C3',
              600: '#0071aa',
              deep: '#0a2540',
            }
          },
          animation: {
            'float': 'float 8s ease-in-out infinite',
            'float-delayed': 'float 8s ease-in-out 4s infinite',
            'shimmer': 'shimmer 2.5s infinite',
          },
          keyframes: {
            float: {
              '0%, 100%': { transform: 'translateY(0px) rotate(0deg)' },
              '50%': { transform: 'translateY(-20px) rotate(1deg)' },
            },
            shimmer: {
              '0%': { backgroundPosition: '-200% 0' },
              '100%': { backgroundPosition: '200% 0' },
            }
          }
        }
      }
    }
  </script>

  <style>
    body {
      background-color: #fcfdfe;
      background-image:
        radial-gradient(circle at 2px 2px, rgba(0, 130, 195, 0.04) 1px, transparent 0),
        linear-gradient(180deg, rgba(240, 249, 255, 0.5) 0%, rgba(255, 255, 255, 0) 100%);
      background-size: 48px 48px, 100% 100%;
    }

    .glass-nav {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(20px) saturate(180%);
      -webkit-backdrop-filter: blur(20px) saturate(180%);
      transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .reveal {
      opacity: 0;
      transform: translateY(40px);
      transition: all 1.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .reveal.active {
      opacity: 1;
      transform: translateY(0);
    }

    .service-card {
      transition: all 0.7s cubic-bezier(0.16, 1, 0.3, 1);
      border: 1px solid rgba(0, 130, 195, 0.08);
      background: rgba(255, 255, 255, 0.9);
    }
    .service-card:hover {
      transform: translateY(-16px) scale(1.02);
      box-shadow: 0 40px 100px -20px rgba(0, 130, 195, 0.18);
      border-color: rgba(0, 130, 195, 0.3);
      background: white;
    }

    .btn-premium {
      background: linear-gradient(135deg, #0082C3 0%, #005a88 100%);
      position: relative;
      overflow: hidden;
      transition: all 0.4s ease;
    }
    .btn-premium::after {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
      transform: rotate(45deg);
      transition: 0.8s;
    }
    .btn-premium:hover::after {
      left: 100%;
    }
    .btn-premium:hover {
      transform: translateY(-2px);
      box-shadow: 0 15px 30px -5px rgba(0, 130, 195, 0.4);
    }

    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #f1f5f9; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; border: 2px solid #f1f5f9; }
    ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    /* Formal sizing for About section */
    #about h2 {
      font-size: clamp(2rem, 3.2vw, 3.25rem);
      line-height: 1.1;
      letter-spacing: -0.02em;
      margin-bottom: 2rem;
    }

    #about .space-y-8 > p {
      font-size: clamp(1rem, 1.15vw, 1.125rem);
      line-height: 1.8;
    }

    #about .group.relative.reveal {
      max-width: 34rem;
      width: 100%;
      margin-inline: auto;
    }

    #about .group.relative.reveal > div:nth-child(2) {
      aspect-ratio: 3 / 4;
      border-width: 8px;
      border-radius: 2.25rem;
    }

    /* Hide header Patient Portal/Login button */
    header nav a[href*="login"],
    header nav a[href*="portal"],
    nav a[href*="login"],
    nav a[href*="portal"] {
      display: none !important;
    }
  </style>
</head>
<body class="text-slate-800 antialiased overflow-x-hidden selection:bg-brand-500 selection:text-white">

  @include('components.homepage.header-section')

  <section class="relative overflow-hidden bg-white pb-24 pt-40 lg:pb-40 lg:pt-56">
    <div class="absolute -left-24 -top-24 -z-10 h-96 w-96 rounded-full bg-brand-100/30 blur-[100px]"></div>
    <div class="absolute right-0 top-0 -z-10 h-full w-1/2 rounded-l-[15rem] bg-brand-50/20"></div>

    <div class="mx-auto max-w-[1800px] px-4 sm:px-6 lg:px-10">
      <div class="grid items-center gap-14 lg:grid-cols-2">
        <div class="reveal max-w-2xl">
          <div class="mb-10 inline-flex items-center gap-3 rounded-full border border-brand-100 bg-brand-50 px-4 py-2 text-[10px] font-bold uppercase tracking-[0.3em] text-brand-500 shadow-sm">
            <span class="h-2 w-2 animate-pulse rounded-full bg-brand-500"></span>
            Ranked #1 in Patient Satisfaction
          </div>
          <h1 class="mb-10 font-serif text-5xl font-bold leading-[1.05] tracking-tight text-brand-deep lg:text-[5.4rem]">
            Premium care for a
            <span class="relative italic text-brand-500">
              confident
              <svg class="absolute -bottom-2 left-0 -z-10 h-3 w-full text-brand-200" viewBox="0 0 100 10" preserveAspectRatio="none"><path d="M0 5 Q 50 10 100 5" stroke="currentColor" stroke-width="4" fill="none"/></svg>
            </span>
            smile.
          </h1>
          <p class="mb-14 max-w-lg text-xl font-light leading-relaxed text-slate-500">
            Receive world-class clinical expertise within a sanctuary of modern luxury. We prioritize your comfort and safety above all.
          </p>
          <div class="flex flex-wrap items-center gap-8">
            <a href="#register" class="btn-premium rounded-2xl px-12 py-5 text-sm font-bold uppercase tracking-widest text-white shadow-2xl transition-all active:scale-95">
              Start Your Visit
            </a>
            <a href="#services" class="group flex items-center gap-3 text-sm font-black uppercase tracking-[0.2em] text-brand-deep transition-colors hover:text-brand-500">
              Our Services
              <i data-lucide="arrow-right" class="h-5 w-5 transition-transform group-hover:translate-x-3"></i>
            </a>
          </div>
        </div>

        <div class="reveal relative hidden lg:block">
          <div class="group relative z-10 aspect-square overflow-hidden rounded-[4rem] border-[16px] border-white bg-slate-50 shadow-[0_50px_120px_-20px_rgba(0,0,0,0.12)]">
            <img src="https://images.unsplash.com/photo-1629909613654-28e377c37b09?auto=format&fit=crop&w=1000&q=90" alt="Clinic Interior" class="h-full w-full object-cover transition-transform duration-[2.5s] group-hover:scale-105">
            <div class="absolute inset-0 bg-brand-deep/5 transition-colors duration-700 group-hover:bg-transparent"></div>
          </div>

          <div class="animate-float absolute -bottom-10 -left-10 z-20 flex cursor-default items-center gap-5 rounded-[2.5rem] border border-white/60 bg-white/35 p-8 shadow-2xl backdrop-blur-xl transition-transform hover:scale-105">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-500/85 text-white shadow-lg backdrop-blur-sm">
              <i data-lucide="award" class="h-8 w-8"></i>
            </div>
            <div>
              <p class="mb-1 text-[11px] font-black uppercase tracking-widest text-brand-500">Top Rated</p>
              <p class="text-xl font-bold text-brand-deep">10+ Expert Doctors</p>
            </div>
          </div>

          <div class="animate-float-delayed absolute -right-12 top-16 z-20 flex cursor-default items-center gap-5 rounded-[2.5rem] border border-white/35 bg-brand-deep/65 p-8 shadow-2xl backdrop-blur-xl transition-transform hover:scale-105">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl border border-white/35 bg-white/15 text-white backdrop-blur-sm">
              <i data-lucide="mail" class="h-8 w-8"></i>
            </div>
            <div class="text-white">
              <p class="text-xl font-bold">Smart Booking</p>
              <p class="mt-1 text-[11px] font-bold uppercase tracking-widest opacity-60">Gmail Notifications</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="about" class="relative overflow-hidden bg-white py-28 lg:py-32">
    <div class="mx-auto max-w-[1800px] px-4 sm:px-6 lg:px-10">
      <div class="grid items-center gap-20 lg:grid-cols-2">
        <div class="reveal">
          <div class="mb-10 text-[12px] font-black uppercase tracking-[0.5em] text-brand-500">Our Story</div>
          <h2 class="mb-10 font-serif text-4xl font-bold leading-tight tracking-tighter text-brand-deep lg:text-6xl">A Decade of Clinical Excellence.</h2>

          <div class="space-y-8">
            <p class="text-lg font-light leading-relaxed text-slate-500">
              Founded over 10 years ago in Quezon City, Tejada Dental Clinic delivers modern, evidence-based dentistry in a calm and professional setting. From prevention to advanced procedures, we focus on safe and precise care for every patient.
            </p>
            <p class="text-lg font-light leading-relaxed text-slate-500">
              Our team combines clinical experience, digital diagnostics, and patient-first service. Whether you need a routine checkup, orthodontic planning, whitening, or oral surgery, we make treatment clear, comfortable, and dependable.
            </p>

          </div>
        </div>

        <div class="group relative mx-auto w-full max-w-[560px] reveal">
          <div class="absolute -inset-4 -z-10 rounded-[4rem] bg-brand-50 transition-transform duration-700 group-hover:scale-105"></div>
          <div class="aspect-[4/4.8] overflow-hidden rounded-[3rem] border-[10px] border-white bg-slate-50 shadow-2xl">
            <img src="https://images.unsplash.com/photo-1588776814546-daab30f310ce?auto=format&fit=crop&w=1000&q=80" alt="Dental treatment session in clinic" class="h-full w-full object-cover">
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="services" class="relative overflow-hidden bg-[#f4fbff]/50 py-28 lg:py-32">
    <div class="mx-auto max-w-[1800px] px-4 sm:px-6 lg:px-10">
      <div class="reveal mx-auto mb-24 max-w-3xl text-center">
        <div class="mb-6 text-center text-[12px] font-black uppercase tracking-[0.5em] text-brand-500">Procedures</div>
        <h2 class="mb-8 font-serif text-5xl font-bold leading-tight tracking-tight text-brand-deep lg:text-7xl">
          Clinical <span class="font-light italic text-brand-500 underline decoration-brand-200 underline-offset-8">Excellence</span>
        </h2>
        <p class="text-xl font-light leading-relaxed text-slate-500">Combining medical precision with aesthetic artistry to deliver transformative results.</p>
      </div>

      <div class="grid gap-10 md:grid-cols-2 xl:grid-cols-4">
        <div class="service-card group reveal overflow-hidden rounded-[3rem] bg-white">
          <div class="relative h-64 overflow-hidden">
            <img src="https://images.unsplash.com/photo-1606811841689-23dfddce3e95?auto=format&fit=crop&w=600&q=80" alt="Checkup" class="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-110">
            <div class="absolute left-6 top-6 flex h-12 w-12 items-center justify-center rounded-full border border-slate-300 bg-white/80 text-xs font-black tracking-widest text-brand-deep shadow-sm backdrop-blur-sm">01</div>
          </div>
          <div class="p-10">
            <h3 class="mb-4 font-serif text-2xl font-bold text-brand-deep">General Checkup</h3>
            <p class="mb-10 text-sm font-light leading-relaxed text-slate-500">Advanced clinical examinations to ensure long-term oral health.</p>
            <a href="/services/general-checkup" class="flex items-center gap-3 text-[11px] font-black uppercase tracking-[0.3em] text-brand-500 transition-all group-hover:gap-5">
              Discover <i data-lucide="chevron-right" class="h-4 w-4"></i>
            </a>
          </div>
        </div>

        <div class="service-card group reveal overflow-hidden rounded-[3rem] bg-white">
          <div class="relative h-64 overflow-hidden">
            <img src="https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?auto=format&fit=crop&w=600&q=80" alt="Surgery" class="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-110">
            <div class="absolute left-6 top-6 flex h-12 w-12 items-center justify-center rounded-full border border-slate-300 bg-white/80 text-xs font-black tracking-widest text-brand-deep shadow-sm backdrop-blur-sm">02</div>
          </div>
          <div class="p-10">
            <h3 class="mb-4 font-serif text-2xl font-bold text-brand-deep">Oral Surgery</h3>
            <p class="mb-10 text-sm font-light leading-relaxed text-slate-500">Precision-guided procedures performed with absolute surgical mastery.</p>
            <a href="/services/oral-surgery" class="flex items-center gap-3 text-[11px] font-black uppercase tracking-[0.3em] text-brand-500 transition-all group-hover:gap-5">
              Discover <i data-lucide="chevron-right" class="h-4 w-4"></i>
            </a>
          </div>
        </div>

        <div class="service-card group reveal overflow-hidden rounded-[3rem] bg-white">
          <div class="relative h-64 overflow-hidden">
            <img src="https://images.unsplash.com/photo-1598256989800-fe5f95da9787?auto=format&fit=crop&w=600&q=80" alt="Orthodontics" class="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-110">
            <div class="absolute left-6 top-6 flex h-12 w-12 items-center justify-center rounded-full border border-slate-300 bg-white/80 text-xs font-black tracking-widest text-brand-deep shadow-sm backdrop-blur-sm">03</div>
          </div>
          <div class="p-10">
            <h3 class="mb-4 font-serif text-2xl font-bold text-brand-deep">Orthodontics</h3>
            <p class="mb-10 text-sm font-light leading-relaxed text-slate-500">Designing perfect alignment for both functional and aesthetic brilliance.</p>
            <a href="/services/orthodontics" class="flex items-center gap-3 text-[11px] font-black uppercase tracking-[0.3em] text-brand-500 transition-all group-hover:gap-5">
              Discover <i data-lucide="chevron-right" class="h-4 w-4"></i>
            </a>
          </div>
        </div>

        <div class="service-card group reveal overflow-hidden rounded-[3rem] bg-white">
          <div class="relative h-64 overflow-hidden">
            <img src="https://images.unsplash.com/photo-1606811971618-4486d14f3f99?auto=format&fit=crop&w=600&q=80" alt="Teeth Whitening Treatment" class="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-110">
            <div class="absolute left-6 top-6 flex h-12 w-12 items-center justify-center rounded-full border border-slate-300 bg-white/80 text-xs font-black tracking-widest text-brand-deep shadow-sm backdrop-blur-sm">04</div>
          </div>
          <div class="p-10">
            <h3 class="mb-4 font-serif text-2xl font-bold text-brand-deep">Teeth Whitening</h3>
            <p class="mb-10 text-sm font-light leading-relaxed text-slate-500">Advanced clinical brightening protocols for immediate, natural radiance.</p>
            <a href="/services/teeth-whitening" class="flex items-center gap-3 text-[11px] font-black uppercase tracking-[0.3em] text-brand-500 transition-all group-hover:gap-5">
              Discover <i data-lucide="chevron-right" class="h-4 w-4"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="contact" class="relative bg-white py-28 lg:py-32">
    <div class="mx-auto max-w-[1800px] px-4 sm:px-6 lg:px-10">
      <div class="grid items-center gap-20 lg:grid-cols-2">
        <div class="reveal">
          <div class="mb-10 text-[12px] font-black uppercase tracking-[0.5em] text-brand-500">Location</div>
          <h2 class="mb-14 font-serif text-5xl font-bold leading-tight tracking-tighter text-brand-deep lg:text-[5.1rem]">Clinical excellence in the heart of the city.</h2>

          <div class="space-y-10">
            <div class="group flex items-start gap-8">
              <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-[1.5rem] bg-brand-50 text-brand-500 shadow-sm transition-all duration-500 group-hover:bg-brand-500 group-hover:text-white">
                <i data-lucide="map-pin" class="h-7 w-7"></i>
              </div>
              <div>
                <p class="mb-2 text-[10px] font-black uppercase tracking-widest text-slate-400">Practice Address</p>
                <p class="text-2xl font-bold text-brand-deep">251 Commonwealth Ave, Diliman</p>
                <p class="mt-1 font-light text-slate-500">Quezon City, Metro Manila, Philippines</p>
              </div>
            </div>

            <div class="group flex items-start gap-8">
              <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-[1.5rem] bg-brand-50 text-brand-500 shadow-sm transition-all duration-500 group-hover:bg-brand-500 group-hover:text-white">
                <i data-lucide="phone" class="h-7 w-7"></i>
              </div>
              <div>
                <p class="mb-2 text-[10px] font-black uppercase tracking-widest text-slate-400">Contact Details</p>
                <p class="text-2xl font-bold text-brand-deep">+63 912 345 6789</p>
                <p class="mt-1 font-light italic text-slate-500">Mon - Sat: 9:00 AM - 6:00 PM</p>
              </div>
            </div>
          </div>
        </div>

        <div class="group relative h-[650px] overflow-hidden rounded-[4rem] border-[16px] border-slate-50 bg-slate-100 shadow-[0_60px_100px_-30px_rgba(0,130,195,0.2)] reveal">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3860.0526364003323!2d121.07357487578508!3d14.668435175138128!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b759ceee4d07%3A0x8927894f17a3b774!2sDiliman%20Doctors%20Hospital!5e0!3m2!1sen!2sph!4v1700000000000!5m2!1sen!2sph" class="absolute inset-0 h-full w-full border-0 transition-all duration-1000 group-hover:scale-[1.02]" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Tejada Dental Clinic Location"></iframe>
          <div class="absolute bottom-5 right-5 z-20 flex gap-3">
            <a href="https://maps.google.com/?q=251+Commonwealth+Ave,+Diliman,+Quezon+City" target="_blank" rel="noopener noreferrer" class="rounded-xl bg-white/90 px-4 py-2 text-xs font-bold uppercase tracking-wider text-brand-deep shadow-lg backdrop-blur-sm transition hover:bg-white">
              Open Map
            </a>
            <a href="https://www.google.com/maps/dir/?api=1&destination=251+Commonwealth+Ave,+Diliman,+Quezon+City" target="_blank" rel="noopener noreferrer" class="rounded-xl bg-brand-500 px-4 py-2 text-xs font-bold uppercase tracking-wider text-white shadow-lg transition hover:bg-brand-600">
              Directions
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <footer class="relative overflow-hidden border-t border-slate-100 bg-white pb-16 pt-28 lg:pt-32">
    <div class="absolute left-0 top-0 h-1 w-full bg-gradient-to-r from-transparent via-brand-500 to-transparent opacity-30"></div>

    <div class="mx-auto max-w-[1800px] px-4 sm:px-6 lg:px-10">
      <div class="mb-20 grid gap-16 md:grid-cols-3 lg:gap-24">
        <div class="reveal">
          <a class="td-brand mb-10 flex items-center gap-4" href="{{ url('/') }}">
            <div class="relative flex h-11 w-11 shrink-0 items-center justify-center">
              <svg width="44" height="44" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="-translate-y-[1px]">
                <defs>
                  <linearGradient id="footerLogoMain" x1="11" y1="11" x2="53" y2="55" gradientUnits="userSpaceOnUse">
                    <stop offset="0" stop-color="#1389D4" />
                    <stop offset="1" stop-color="#0B4F96" />
                  </linearGradient>
                  <linearGradient id="footerLogoShade" x1="42" y1="14" x2="50" y2="44" gradientUnits="userSpaceOnUse">
                    <stop offset="0" stop-color="#0A5DA5" />
                    <stop offset="1" stop-color="#0C2340" />
                  </linearGradient>
                  <linearGradient id="footerLogoBadge" x1="25" y1="27" x2="39" y2="41" gradientUnits="userSpaceOnUse">
                    <stop offset="0" stop-color="#0F9ADD" />
                    <stop offset="1" stop-color="#0B4D8E" />
                  </linearGradient>
                </defs>
                <path fill="url(#footerLogoMain)" d="M32 58c-1.2 0-2.3-.4-3.2-1.2C16.5 47 8 39.7 8 28.8 8 19.7 14.6 13 23 13c3.7 0 7.2 1.4 9.9 4 2.7-2.6 6.2-4 9.9-4 8.4 0 15 6.7 15 15.8 0 10.9-8.5 18.2-20.8 28-.9.8-2 1.2-3.1 1.2z" />
                <path fill="url(#footerLogoShade)" d="M42.8 14.1c6 2.3 10.2 7.5 10.2 14.7 0 8.8-6.1 14.8-15.9 22.8 2.9-8.5-5.2-13.6-1.1-20 3.6-5.6 9-8.7 6.8-17.5z" opacity=".95" />
                <path fill="#FFFFFF" opacity=".22" d="M17 22c2.4-5.1 8.2-8.4 14.3-8.4 1.2 0 2.4.1 3.6.4-4.8 1-8.8 4.2-10.6 8.9-2.4 6.4-.6 12.6 3.7 17.4-6.8-3-13.7-10-11-18.3z" />
                <path fill="#EFF8FF" d="M31.4 58c2.4-3.3 3.3-6.8 2.7-10.4-.4-2.2.2-4.5 1.5-6.4l2.5-3.4c1.7-2.4 1.4-5.6-.7-7.6l-4.7-4.5-4.7 4.5c-2.1 2-2.4 5.3-.7 7.6l2.5 3.4c1.3 1.9 1.9 4.2 1.5 6.4-.6 3.6.3 7.1 2.7 10.4h-2.6z" />
                <circle cx="32" cy="34" r="11" fill="url(#footerLogoBadge)" stroke="#DFF3FF" stroke-width="2.2" />
                <circle cx="32" cy="34" r="8.4" fill="#FFFFFF" />
                <path d="M32 29v10M27 34h10" stroke="#0C3C71" stroke-width="3.2" stroke-linecap="round" />
              </svg>
            </div>
            <div class="flex -translate-y-[1px] flex-col justify-center leading-none">
              <p class="font-serif text-[17px] font-bold tracking-[0.01em] text-[#0f2340] sm:text-[19px]">Tejada Dental</p>
              <p class="mt-1 text-[8px] font-bold uppercase tracking-[0.28em] text-[#0082C3] sm:text-[9px] sm:tracking-[0.32em]">Clinic &amp; Oral Care</p>
            </div>
          </a>
          <p class="max-w-sm text-[15px] font-light leading-relaxed text-slate-400">Excellence in oral healthcare and patient comfort for over a decade. Your smile is our greatest clinical masterpiece.</p>
        </div>
        <div class="reveal">
          <h4 class="mb-10 text-[10px] font-black uppercase tracking-[0.5em] text-brand-deep opacity-40">Navigation</h4>
          <ul class="space-y-5 text-[13px] font-bold uppercase tracking-[0.2em] text-slate-500">
            <li><a href="#about" class="transition-all hover:text-brand-500">About Clinic</a></li>
            <li><a href="#services" class="transition-all hover:text-brand-500">Our Services</a></li>
            <li><a href="#contact" class="transition-all hover:text-brand-500">Directions</a></li>
          </ul>
        </div>
        <div class="reveal">
          <h4 class="mb-10 text-[10px] font-black uppercase tracking-[0.5em] text-brand-deep opacity-40">Treatments</h4>
          <ul class="space-y-5 text-[13px] font-bold uppercase tracking-[0.2em] text-slate-500">
            <li><a href="/services/general-checkup" class="transition-all hover:text-brand-500">General Checkup</a></li>
            <li><a href="/services/orthodontics" class="transition-all hover:text-brand-500">Orthodontics</a></li>
            <li><a href="/services/oral-surgery" class="transition-all hover:text-brand-500">Oral Surgery</a></li>
            <li><a href="/services/teeth-whitening" class="transition-all hover:text-brand-500">Teeth Whitening</a></li>
          </ul>
        </div>
      </div>
      <div class="flex flex-col items-start justify-between gap-4 border-t border-slate-50 pt-12 text-[10px] font-black uppercase tracking-[0.28em] opacity-40 md:flex-row md:items-center">
        <p>&copy; 2026 Tejada Dental Clinic. All Rights Reserved.</p>
        <p>Quezon City, PH</p>
      </div>
    </div>
  </footer>

  <button id="backToTop" class="fixed bottom-8 right-8 z-[60] rounded-2xl bg-brand-deep p-4 text-white opacity-0 translate-y-10 shadow-2xl shadow-brand-deep/20 transition-all hover:-translate-y-2 hover:bg-brand-500 active:scale-90">
    <i data-lucide="arrow-up" class="h-6 w-6"></i>
  </button>

  <script>
    lucide.createIcons();

    const reveals = document.querySelectorAll('.reveal');
    const backToTop = document.getElementById('backToTop');

    window.addEventListener('scroll', () => {
      if (window.scrollY > 600) {
        backToTop.classList.add('opacity-100', 'translate-y-0');
        backToTop.classList.remove('opacity-0', 'translate-y-10');
      } else {
        backToTop.classList.add('opacity-0', 'translate-y-10');
        backToTop.classList.remove('opacity-100', 'translate-y-0');
      }

      reveals.forEach(el => {
        const top = el.getBoundingClientRect().top;
        if (top < window.innerHeight * 0.92) {
          el.classList.add('active');
        }
      });
    });

    backToTop.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    window.dispatchEvent(new Event('scroll'));
  </script>
</body>
</html>
