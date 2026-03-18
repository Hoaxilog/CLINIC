<footer class="border-t-[3px] border-t-[#006ab0] bg-[#0086da] px-6 md:px-12 xl:px-20">
    <div class="mx-auto w-full max-w-[1400px]">
        <div class="flex flex-wrap justify-between gap-12 border-b border-white/10 py-[68px] pb-12">
            <div class="max-w-[260px]">
                <div class="mb-5">
                    <x-brand.logo :light="true" href="{{ route('home') }}" iconClass="flex h-9 w-9 shrink-0 items-center justify-center"
                        titleClass="text-[.88rem] font-extrabold tracking-[.05em] text-white"
                        subtitleClass="text-[.57rem] font-semibold uppercase tracking-[.2em] text-white/80" />
                </div>
                <p class="text-[.8rem] leading-[1.75] italic text-white/80">"We don't just treat teeth; we care for the
                    people behind the smiles."</p>
            </div>

            <div>
                <div class="mb-[18px] text-[.6rem] font-bold uppercase tracking-[.2em] text-white/60">Quick Links</div>
                <div class="flex flex-col gap-[11px]">
                    @foreach (['Services' => 'services', 'About' => 'about', 'Why Us' => 'why-us', 'Hours' => 'hours', 'Contact' => 'contact'] as $label => $id)
                        <a href="/home#{{ $id }}"
                            class="text-[.8rem] font-medium text-white no-underline transition hover:text-white/80">{{ $label }}</a>
                    @endforeach
                </div>
            </div>

            <div>
                <div class="mb-[18px] text-[.6rem] font-bold uppercase tracking-[.2em] text-white/60">Contact</div>
                <div class="flex flex-col gap-[14px]">
                    <div class="flex items-start gap-[11px]">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                            stroke="rgba(255,255,255,.8)" stroke-width="2" stroke-linecap="square"
                            class="mt-0.5 shrink-0">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        <span class="text-[.8rem] leading-[1.6] text-white">251 Commonwealth Ave,<br>Diliman, Quezon
                            City</span>
                    </div>
                    <div class="flex items-center gap-[11px]">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                            stroke="rgba(255,255,255,.8)" stroke-width="2" stroke-linecap="square">
                            <path
                                d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.8 19.79 19.79 0 01.1 1.18 2 2 0 012.11 0h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 14.92z" />
                        </svg>
                        <a href="tel:+639123456789" class="text-[.8rem] text-white no-underline">+63 912 345 6789</a>
                    </div>
                </div>
            </div>

            <div>
                <div class="mb-[18px] text-[.6rem] font-bold uppercase tracking-[.2em] text-white/60">Ready to Visit?
                </div>
                <a href="{{ route('book') }}"
                    class="mb-[14px] inline-flex items-center gap-[9px] whitespace-nowrap bg-white px-6 py-[13px] text-[.7rem] font-bold uppercase tracking-[.1em] text-[#0086da] transition hover:bg-[#e8f4fc]">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="square">
                        <rect x="3" y="4" width="18" height="18" />
                        <path d="M16 2v4M8 2v4M3 10h18" />
                    </svg>
                    Book Appointment
                </a>
                <p class="mt-3 text-[.7rem] leading-[1.6] text-white/70">Mon - Sat · 9:00 AM - 6:00 PM</p>
            </div>
        </div>

        <div
            class="mx-auto flex max-w-[1400px] flex-wrap items-center justify-between gap-3 px-6 py-5 md:px-12 xl:px-20">
            <p class="text-[.7rem] text-white/70">&copy; {{ date('Y') }} Tejada Clinic. All rights reserved.</p>
            <p class="text-[.7rem] text-white/70">251 Commonwealth Ave, Diliman, Quezon City</p>
        </div>
    </div>
</footer>
