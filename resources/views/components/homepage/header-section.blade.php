<style>
  .td-header {
    position: fixed;
    inset: 0 0 auto 0;
    z-index: 100;
    height: 80px;
    background: #f4fbff;
    border-bottom: 1px solid #d3e6f3;
  }

  .td-header-inner {
    width: 100%;
    height: 100%;
    padding: 0 var(--page-gutter, clamp(20px, 3.2vw, 56px));
    display: grid;
    grid-template-columns: auto 1fr auto;
    align-items: center;
    gap: 20px;
  }

  .td-brand {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    flex-shrink: 0;
  }

  .td-nav {
    display: flex;
    align-items: center;
    justify-content: center;
    list-style: none;
    gap: clamp(18px, 2.2vw, 40px);
    margin: 0;
    padding: 0;
    width: 100%;
  }

  .td-nav a {
    font-size: 0.82rem;
    font-weight: 600;
    color: #4f7594;
    text-decoration: none;
    letter-spacing: 0.02em;
    transition: color 0.2s ease;
  }

  .td-nav a:hover {
    color: #0082C3;
  }

  .td-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
  }

  .td-header-login {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border: 1px solid #b7d9ee;
    background: #fff;
    color: #0a3f73;
    font-size: 0.9rem;
    font-weight: 700;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    transition: border-color 0.2s ease, color 0.2s ease, background 0.2s ease;
  }

  .td-header-login:hover {
    border-color: #0082C3;
    color: #0082C3;
    background: #eaf6fd;
  }

  .td-header-cta {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: linear-gradient(135deg, #0082C3 0%, #006aa4 100%);
    color: #fff;
    font-size: 0.9rem;
    font-weight: 700;
    padding: 11px 22px;
    border-radius: 8px;
    text-decoration: none;
    box-shadow: 0 4px 14px rgba(0, 130, 195, 0.28);
    transition: filter 0.2s ease, box-shadow 0.2s ease;
  }

  .td-header-cta:hover {
    filter: saturate(1.08);
    box-shadow: 0 8px 18px rgba(0, 106, 164, 0.35);
  }

  .td-mobile-btn {
    display: none;
    align-items: center;
    justify-content: center;
    border: 1px solid #b7d9ee;
    background: #fff;
    color: #0a3f73;
    border-radius: 8px;
    padding: 8px;
  }

  @media (max-width: 1024px) {
    .td-header-inner {
      display: flex;
      justify-content: space-between;
    }

    .td-nav,
    .td-actions {
      display: none;
    }

    .td-mobile-btn {
      display: inline-flex;
    }
  }

  @media (max-width: 640px) {
    .td-header {
      height: 74px;
    }

    .td-header-inner {
      padding: 0 12px;
    }
  }
</style>

<header class="td-header">
  <div class="td-header-inner">
    <a class="td-brand" href="{{ url('/') }}">
      <div class="relative flex h-11 w-11 shrink-0 items-center justify-center">
        <svg width="44" height="44" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="-translate-y-[1px]">
          <defs>
            <linearGradient id="headerLogoMain" x1="11" y1="11" x2="53" y2="55" gradientUnits="userSpaceOnUse">
              <stop offset="0" stop-color="#1389D4" />
              <stop offset="1" stop-color="#0B4F96" />
            </linearGradient>
            <linearGradient id="headerLogoShade" x1="42" y1="14" x2="50" y2="44" gradientUnits="userSpaceOnUse">
              <stop offset="0" stop-color="#0A5DA5" />
              <stop offset="1" stop-color="#0C2340" />
            </linearGradient>
            <linearGradient id="headerLogoBadge" x1="25" y1="27" x2="39" y2="41" gradientUnits="userSpaceOnUse">
              <stop offset="0" stop-color="#0F9ADD" />
              <stop offset="1" stop-color="#0B4D8E" />
            </linearGradient>
          </defs>
          <path fill="url(#headerLogoMain)" d="M32 58c-1.2 0-2.3-.4-3.2-1.2C16.5 47 8 39.7 8 28.8 8 19.7 14.6 13 23 13c3.7 0 7.2 1.4 9.9 4 2.7-2.6 6.2-4 9.9-4 8.4 0 15 6.7 15 15.8 0 10.9-8.5 18.2-20.8 28-.9.8-2 1.2-3.1 1.2z" />
          <path fill="url(#headerLogoShade)" d="M42.8 14.1c6 2.3 10.2 7.5 10.2 14.7 0 8.8-6.1 14.8-15.9 22.8 2.9-8.5-5.2-13.6-1.1-20 3.6-5.6 9-8.7 6.8-17.5z" opacity=".95" />
          <path fill="#FFFFFF" opacity=".22" d="M17 22c2.4-5.1 8.2-8.4 14.3-8.4 1.2 0 2.4.1 3.6.4-4.8 1-8.8 4.2-10.6 8.9-2.4 6.4-.6 12.6 3.7 17.4-6.8-3-13.7-10-11-18.3z" />
          <path fill="#EFF8FF" d="M31.4 58c2.4-3.3 3.3-6.8 2.7-10.4-.4-2.2.2-4.5 1.5-6.4l2.5-3.4c1.7-2.4 1.4-5.6-.7-7.6l-4.7-4.5-4.7 4.5c-2.1 2-2.4 5.3-.7 7.6l2.5 3.4c1.3 1.9 1.9 4.2 1.5 6.4-.6 3.6.3 7.1 2.7 10.4h-2.6z" />
          <circle cx="32" cy="34" r="11" fill="url(#headerLogoBadge)" stroke="#DFF3FF" stroke-width="2.2" />
          <circle cx="32" cy="34" r="8.4" fill="#FFFFFF" />
          <path d="M32 29v10M27 34h10" stroke="#0C3C71" stroke-width="3.2" stroke-linecap="round" />
        </svg>
      </div>
      <div class="flex -translate-y-[1px] flex-col justify-center leading-none">
        <p class="font-serif text-[17px] font-bold tracking-[0.01em] text-[#0f2340] sm:text-[19px]">Tejada Dental</p>
        <p class="mt-1 text-[8px] font-bold uppercase tracking-[0.28em] text-[#0082C3] sm:text-[9px] sm:tracking-[0.32em]">Clinic &amp; Oral Care</p>
      </div>
    </a>

    <ul class="td-nav">
      <li><a href="{{ url('/#about') }}">About</a></li>
      <li><a href="{{ url('/#services') }}">Services</a></li>
      <li><a href="{{ url('/#contact') }}">Contact</a></li>
    </ul>

    <div class="td-actions">
      <a class="td-header-login" href="{{ url('/login') }}">Login</a>
      <a class="td-header-cta" href="{{ url('/#register-section') }}">Book Appointment</a>
    </div>

    <button id="menu-btn" class="td-mobile-btn" aria-label="Open menu">
      <svg id="menu-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>
  </div>

  <div id="mobile-menu" class="hidden border-t border-slate-200 bg-[#f4fbff] px-4 py-4 md:hidden">
    <div class="flex flex-col gap-3">
      <a href="{{ url('/#about') }}" class="text-sm font-semibold text-[#4f7594]">About</a>
      <a href="{{ url('/#services') }}" class="text-sm font-semibold text-[#4f7594]">Services</a>
      <a href="{{ url('/#contact') }}" class="text-sm font-semibold text-[#4f7594]">Contact</a>
      <a href="{{ url('/login') }}" class="mt-1 rounded-md border border-[#b7d9ee] px-4 py-2 text-center text-sm font-semibold text-[#0a3f73]">Login</a>
      <a href="{{ url('/#register-section') }}" class="rounded-md bg-[#0082C3] px-4 py-2 text-center text-sm font-semibold text-white">Book Appointment</a>
    </div>
  </div>
</header>

<script>
  (function() {
    const menuBtn = document.getElementById('menu-btn');
    const menuIcon = document.getElementById('menu-icon');
    const mobileMenu = document.getElementById('mobile-menu');
    if (!menuBtn || !menuIcon || !mobileMenu) return;

    menuBtn.addEventListener('click', () => {
      const isOpen = !mobileMenu.classList.contains('hidden');
      mobileMenu.classList.toggle('hidden');
      menuIcon.innerHTML = isOpen
        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />'
        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />';
    });
  })();
</script>
