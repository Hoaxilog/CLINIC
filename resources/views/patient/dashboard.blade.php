@extends('index')

@section('content')
@php
    $hasUpcoming = (bool) $upcomingAppointment;
    $upcomingAt = $hasUpcoming ? \Carbon\Carbon::parse($upcomingAppointment->appointment_date) : null;
    $patientName = $patient->first_name ?? auth()->user()->username;
    $requestsCount = $appointmentRequests->count();
    $recordsCount = $treatmentRecords->count();
    $notificationCount = $appointmentRequests->take(5)->count();
@endphp

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

  .td-nav a:hover { color: #0082C3; }

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
    gap: 6px;
    border: 1px solid #9ecde8;
    background: #fff;
    color: #0a3f73;
    font-size: 0.85rem;
    font-weight: 700;
    height: 44px;
    padding: 0 12px 0 8px;
    border-radius: 12px;
    text-decoration: none;
    box-shadow: 0 3px 10px rgba(10, 63, 115, 0.08);
    transition: border-color 0.2s ease, color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
  }

  .td-header-login:hover {
    border-color: #0082C3;
    color: #0082C3;
    background: #eaf6fd;
    box-shadow: 0 6px 16px rgba(0, 130, 195, 0.18);
    transform: translateY(-1px);
  }

  .td-header-cta {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: linear-gradient(135deg, #0082C3 0%, #006aa4 100%);
    color: #fff;
    font-size: 0.88rem;
    font-weight: 700;
    padding: 10px 18px;
    border-radius: 12px;
    text-decoration: none;
    box-shadow: 0 8px 18px rgba(0, 130, 195, 0.3);
    transition: filter 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
  }

  .td-header-cta:hover {
    filter: saturate(1.08);
    box-shadow: 0 12px 24px rgba(0, 106, 164, 0.4);
    transform: translateY(-1px);
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

  .td-account-avatar {
    width: 30px;
    height: 30px;
    border-radius: 9999px;
    object-fit: cover;
    border: 1px solid #b7d9ee;
    background: #eaf6fd;
    display: block;
  }

  .td-account-name {
    max-width: 110px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    line-height: 1;
  }

  .td-notify-wrap {
    position: relative;
  }

  .td-notify-btn {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    border-radius: 12px;
    border: 1px solid #9ecde8;
    background: #fff;
    color: #0a3f73;
    box-shadow: 0 3px 10px rgba(10, 63, 115, 0.08);
    transition: border-color 0.2s ease, color 0.2s ease, background 0.2s ease;
  }

  .td-notify-btn:hover {
    border-color: #0082C3;
    color: #0082C3;
    background: #eaf6fd;
  }

  .td-notify-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    border-radius: 9999px;
    background: #ef4444;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    border: 2px solid #f4fbff;
  }

  .td-notify-dropdown {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    width: 320px;
    background: #fff;
    border: 1px solid #dbeefe;
    border-radius: 14px;
    box-shadow: 0 22px 40px -25px rgba(0, 130, 195, 0.45);
    padding: 12px;
    z-index: 120;
  }

  .td-notify-item {
    border: 1px solid #e6f2fb;
    border-radius: 10px;
    background: #fbfdff;
    padding: 10px;
  }

  @media (max-width: 1024px) {
    .td-header-inner { display: flex; justify-content: space-between; }
    .td-nav, .td-actions { display: none; }
    .td-mobile-btn { display: inline-flex; }
  }

  @media (max-width: 640px) {
    .td-header { height: 74px; }
    .td-header-inner { padding: 0 12px; }
    .td-notify-dropdown {
      width: 280px;
      right: -60px;
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
      <li><a href="{{ url('/patient/dashboard') }}">Dashboard</a></li>
      <li><a href="{{ url('/#about') }}">About</a></li>
      <li><a href="{{ url('/#services') }}">Services</a></li>
      <li><a href="{{ url('/#contact') }}">Contact</a></li>
    </ul>

    <div class="td-actions">
      <a class="td-header-cta" href="{{ url('/book') }}">Book Appointment</a>
      <div class="td-notify-wrap">
        <button type="button" id="notify-btn" class="td-notify-btn" aria-label="Notifications" aria-expanded="false">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 1 1-6 0m6 0H9"/>
          </svg>
          @if($notificationCount > 0)
            <span class="td-notify-badge">{{ $notificationCount > 9 ? '9+' : $notificationCount }}</span>
          @endif
        </button>
        <div id="notify-dropdown" class="td-notify-dropdown hidden">
          <p class="mb-2 text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Notifications</p>
          @if($appointmentRequests->count() > 0)
            <div class="space-y-2">
              @foreach($appointmentRequests->take(5) as $request)
                <div class="td-notify-item">
                  <p class="text-xs font-semibold text-[#0a2540]">{{ $request->service_name ?? 'Appointment update' }}</p>
                  <p class="mt-1 text-[11px] text-slate-500">
                    {{ \Carbon\Carbon::parse($request->appointment_date)->format('M d, Y h:i A') }}
                  </p>
                </div>
              @endforeach
            </div>
          @else
            <p class="text-sm text-slate-500">No notifications right now.</p>
          @endif
        </div>
      </div>
      <a class="td-header-login" href="{{ url('/profile') }}" aria-label="Account">
        <img
          src="https://ui-avatars.com/api/?name={{ urlencode($patientName) }}&background=0082C3&color=ffffff&size=64"
          alt="{{ $patientName }}"
          class="td-account-avatar"
        >
        <span class="td-account-name">{{ $patientName }}</span>
      </a>
    </div>

    <button id="menu-btn" class="td-mobile-btn" aria-label="Open menu">
      <svg id="menu-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>
  </div>

  <div id="mobile-menu" class="hidden border-t border-slate-200 bg-[#f4fbff] px-4 py-4 md:hidden">
    <div class="flex flex-col gap-3">
      <a href="{{ url('/patient/dashboard') }}" class="text-sm font-semibold text-[#4f7594]">Dashboard</a>
      <a href="{{ url('/#about') }}" class="text-sm font-semibold text-[#4f7594]">About</a>
      <a href="{{ url('/#services') }}" class="text-sm font-semibold text-[#4f7594]">Services</a>
      <a href="{{ url('/#contact') }}" class="text-sm font-semibold text-[#4f7594]">Contact</a>
      <a href="{{ url('/book') }}" class="rounded-md bg-[#0082C3] px-4 py-2 text-center text-sm font-semibold text-white">Book Appointment</a>
      <a href="{{ url('/patient/profile') }}" class="mt-1 rounded-md border border-[#b7d9ee] px-4 py-2 text-center text-sm font-semibold text-[#0a3f73]">Account</a>
    </div>
  </div>
</header>

<script>
  (function() {
    const menuBtn = document.getElementById('menu-btn');
    const menuIcon = document.getElementById('menu-icon');
    const mobileMenu = document.getElementById('mobile-menu');
    const notifyBtn = document.getElementById('notify-btn');
    const notifyDropdown = document.getElementById('notify-dropdown');
    if (!menuBtn || !menuIcon || !mobileMenu) return;

    menuBtn.addEventListener('click', () => {
      const isOpen = !mobileMenu.classList.contains('hidden');
      mobileMenu.classList.toggle('hidden');
      menuIcon.innerHTML = isOpen
        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />'
        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />';
    });

    if (notifyBtn && notifyDropdown) {
      notifyBtn.addEventListener('click', (event) => {
        event.stopPropagation();
        const willOpen = notifyDropdown.classList.contains('hidden');
        notifyDropdown.classList.toggle('hidden');
        notifyBtn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
      });

      document.addEventListener('click', (event) => {
        if (!notifyDropdown.contains(event.target) && !notifyBtn.contains(event.target)) {
          notifyDropdown.classList.add('hidden');
          notifyBtn.setAttribute('aria-expanded', 'false');
        }
      });
    }
  })();
</script>

<main id="mainContent" class="min-h-screen bg-[radial-gradient(circle_at_2px_2px,rgba(0,130,195,0.04)_1px,transparent_0),linear-gradient(180deg,rgba(240,249,255,0.5)_0%,rgba(255,255,255,0)_100%)] bg-[length:48px_48px,100%_100%] p-4 pb-10 pt-24 sm:p-6 sm:pt-28 lg:p-8 lg:pt-28">
    <section class="relative overflow-hidden rounded-3xl border border-[#dbeefe] bg-white px-5 py-6 shadow-[0_20px_45px_-25px_rgba(0,130,195,0.35)] sm:px-8 sm:py-8">
        <div class="absolute -right-16 -top-16 h-44 w-44 rounded-full bg-[#e0f2fe] blur-3xl"></div>
        <div class="absolute -left-10 -bottom-16 h-36 w-36 rounded-full bg-[#bae6fd] blur-3xl"></div>

        <div class="relative">
            <p class="text-[11px] font-black uppercase tracking-[0.35em] text-[#0082C3]">Patient Portal</p>
            <h1 class="mt-2 font-serif text-3xl font-bold leading-tight text-[#0a2540] sm:text-4xl">Welcome, {{ $patientName }}.</h1>
            <p class="mt-3 max-w-2xl text-sm leading-relaxed text-slate-600 sm:text-base">
                @if($hasUpcoming)
                    You are scheduled for {{ $upcomingAt->format('l, M d') }} at {{ $upcomingAt->format('h:i A') }}.
                @else
                    Keep your care on track. Book your next visit and monitor your treatment history here.
                @endif
            </p>
        </div>

        <div class="relative mt-6 grid gap-3 sm:grid-cols-3">
            <div class="rounded-2xl border border-[#dbeefe] bg-[#f7fcff] p-4">
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Upcoming</p>
                <p class="mt-2 text-2xl font-bold text-[#0a2540]">{{ $hasUpcoming ? '1' : '0' }}</p>
            </div>
            <div class="rounded-2xl border border-[#dbeefe] bg-[#f7fcff] p-4">
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Requests</p>
                <p class="mt-2 text-2xl font-bold text-[#0a2540]">{{ $requestsCount }}</p>
            </div>
            <div class="rounded-2xl border border-[#dbeefe] bg-[#f7fcff] p-4">
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Records</p>
                <p class="mt-2 text-2xl font-bold text-[#0a2540]">{{ $recordsCount }}</p>
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-4 md:grid-cols-2">
        <article class="rounded-2xl border border-[#dbeefe] bg-white p-5 shadow-[0_16px_40px_-25px_rgba(0,130,195,0.25)]">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Next Appointment</p>
            @if($hasUpcoming)
                <p class="mt-3 text-3xl font-bold text-[#0a2540]">{{ $upcomingAt->format('h:i A') }}</p>
                <p class="mt-1 text-sm text-slate-500">{{ $upcomingAt->format('l, M d Y') }}</p>
                <p class="mt-3 inline-flex rounded-full border border-[#bae6fd] bg-[#f0f9ff] px-3 py-1 text-xs font-semibold text-[#0071aa]">{{ $upcomingAppointment->service_name ?? 'Service' }}</p>
            @else
                <p class="mt-3 text-lg font-semibold text-[#0a2540]">No appointment yet</p>
                <p class="mt-1 text-sm text-slate-500">Choose your preferred date and time.</p>
            @endif
        </article>

        <article class="rounded-2xl border border-[#dbeefe] bg-white p-5 shadow-[0_16px_40px_-25px_rgba(0,130,195,0.25)]">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Appointment Requests</p>
                <span class="text-xs text-slate-400">Pending & upcoming</span>
            </div>
            @if($appointmentRequests->count() > 0)
                <div class="mt-4 space-y-3">
                    @foreach($appointmentRequests->take(4) as $request)
                        @php
                            $reqStatus = $request->status ?? 'Pending';
                            $reqBadge = match ($reqStatus) {
                                'Waiting' => 'bg-amber-100 text-amber-700 border-amber-200',
                                'Ongoing' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                                'Scheduled' => 'bg-blue-100 text-blue-700 border-blue-200',
                                default => 'bg-slate-100 text-slate-700 border-slate-200',
                            };
                        @endphp
                        <div class="rounded-xl border border-slate-100 bg-[#fbfdff] p-3">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-[#0a2540]">{{ $request->service_name ?? 'Service' }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">
                                        {{ \Carbon\Carbon::parse($request->appointment_date)->format('M d, Y') }}
                                        · {{ \Carbon\Carbon::parse($request->appointment_date)->format('h:i A') }}
                                    </p>
                                </div>
                                <span class="inline-flex rounded-full border px-2.5 py-1 text-[11px] font-semibold {{ $reqBadge }}">
                                    {{ $reqStatus }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="mt-4 text-sm text-slate-500">No appointment requests yet.</p>
            @endif
        </article>
    </section>

    <section class="mt-6 rounded-2xl border border-[#dbeefe] bg-white p-6 shadow-[0_16px_40px_-25px_rgba(0,130,195,0.25)]">
        <div class="flex items-center justify-between">
            <h2 class="font-serif text-2xl font-bold text-[#0a2540]">Treatment Timeline</h2>
            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Clinical Notes</span>
        </div>
        @if($treatmentRecords->count() > 0)
            <div class="mt-5 overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="border-b border-slate-100 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Date</th>
                            <th class="px-4 py-3 font-semibold">Procedure</th>
                            <th class="px-4 py-3 font-semibold">Dentist</th>
                            <th class="px-4 py-3 font-semibold">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($treatmentRecords as $record)
                            <tr class="transition-colors hover:bg-[#f7fcff]">
                                <td class="whitespace-nowrap px-4 py-3 font-medium text-[#0a2540]">{{ \Carbon\Carbon::parse($record->updated_at)->format('M d, Y') }}</td>
                                <td class="px-4 py-3">{{ $record->treatment ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $record->dmd ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-500">{{ $record->remarks ?? 'No notes provided' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="mt-4 text-sm text-slate-500">Treatment records will appear here after your first completed visit.</p>
        @endif
    </section>
</main>
@endsection
