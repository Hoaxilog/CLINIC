<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Teeth Whitening service at Tejada Dental Clinic." />
  <title>Teeth Whitening | Tejada Dental Clinic</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,500;1,600&display=swap" rel="stylesheet" />
  @vite('resources/css/app.css')
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script>
    tailwind.config = { theme: { extend: { fontFamily: { sans: ['Outfit', 'sans-serif'], serif: ['Playfair Display', 'serif'] }, colors: { brand: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 500: '#0082C3', 600: '#0071aa', deep: '#0a2540' } } } } };
  </script>
  <style>
    body { background-color:#fcfdfe; background-image:radial-gradient(circle at 2px 2px, rgba(0,130,195,.04) 1px, transparent 0), linear-gradient(180deg, rgba(240,249,255,.5) 0%, rgba(255,255,255,0) 100%); background-size:48px 48px,100% 100%; }
    .reveal { opacity:0; transform:translateY(30px); transition:all 1s cubic-bezier(.16,1,.3,1); }
    .reveal.active { opacity:1; transform:translateY(0); }
    .btn-premium { background:linear-gradient(135deg,#0082C3 0%,#005a88 100%); }
    .service-card { border:1px solid rgba(0,130,195,.12); transition:all .45s ease; }
    .service-card:hover { transform:translateY(-8px); box-shadow:0 24px 55px -20px rgba(0,130,195,.28); border-color:rgba(0,130,195,.3); }
  </style>
</head>
<body class="text-slate-800 antialiased overflow-x-hidden">
  @include('components.homepage.header-section')
  <main class="pt-36 lg:pt-44">
    <section class="relative overflow-hidden bg-white pb-20 pt-10 lg:pb-28">
      <div class="absolute right-0 -top-10 h-80 w-80 rounded-full bg-brand-100/40 blur-3xl"></div>
      <div class="mx-auto max-w-[1800px] px-4 sm:px-6 lg:px-10">
        <div class="grid items-center gap-12 lg:grid-cols-2">
          <div class="reveal">
            <p class="mb-5 text-[11px] font-black uppercase tracking-[0.35em] text-brand-500">Service 04</p>
            <h1 class="mb-6 font-serif text-4xl font-bold leading-tight text-brand-deep lg:text-6xl">Teeth Whitening</h1>
            <p class="mb-10 max-w-2xl text-lg font-light leading-relaxed text-slate-500">Clinical brightening protocols for visibly whiter teeth with safe enamel-conscious treatment.</p>
            <div class="flex flex-wrap gap-4">
              <a href="/book" class="btn-premium rounded-2xl px-8 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white transition">Book Now</a>
              <a href="/#services" class="rounded-2xl border border-brand-100 bg-white px-8 py-3 text-sm font-bold uppercase tracking-[0.2em] text-brand-deep transition hover:border-brand-500 hover:text-brand-500">View More Services</a>
            </div>
          </div>
          <div class="reveal overflow-hidden rounded-[2.75rem] border-[12px] border-white bg-slate-100 shadow-2xl">
            <img src="https://images.unsplash.com/photo-1606811971618-4486d14f3f99?auto=format&fit=crop&w=1200&q=85" alt="Teeth whitening service" class="h-[380px] w-full object-cover lg:h-[500px]">
          </div>
        </div>
      </div>
    </section>
    <section class="bg-[#f4fbff]/50 py-16 lg:py-24">
      <div class="mx-auto max-w-[1800px] px-4 sm:px-6 lg:px-10">
        <h2 class="reveal mb-8 font-serif text-3xl font-bold text-brand-deep">What’s Included</h2>
        <div class="grid gap-8 md:grid-cols-3">
          <div class="service-card reveal rounded-3xl bg-white p-8"><h3 class="mb-3 font-serif text-2xl font-bold text-brand-deep">Shade Mapping</h3><p class="text-sm text-slate-500">Baseline and target shade evaluation.</p></div>
          <div class="service-card reveal rounded-3xl bg-white p-8"><h3 class="mb-3 font-serif text-2xl font-bold text-brand-deep">Whitening Session</h3><p class="text-sm text-slate-500">Professional in-clinic brightening treatment.</p></div>
          <div class="service-card reveal rounded-3xl bg-white p-8"><h3 class="mb-3 font-serif text-2xl font-bold text-brand-deep">Aftercare Tips</h3><p class="text-sm text-slate-500">Guidance to preserve whitening results longer.</p></div>
        </div>
      </div>
    </section>
  </main>
  @include('components.homepage.footer-section')
  <script>
    lucide.createIcons();
    const io = new IntersectionObserver((entries) => entries.forEach((entry) => { if (entry.isIntersecting) entry.target.classList.add('active'); }), { threshold: 0.12 });
    document.querySelectorAll('.reveal').forEach((el) => io.observe(el));
  </script>
</body>
</html>
