<footer class="border-t-[3px] border-t-[#006ab0] bg-[#0086da] px-6 md:px-12 xl:px-20">
    <div class="mx-auto w-full max-w-[1400px]">
        <div class="flex flex-wrap justify-between gap-12 border-b border-white/10 py-[68px] pb-12">
            <div class="max-w-[260px]">
                <div class="mb-5 flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center bg-white/15">
                        <svg width="56" height="45" viewBox="0 0 56 45" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <mask id="footer-mask-logo" mask-type="alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                                width="56" height="45">
                                <path
                                    d="M11.783 0.465134C6.04622 2.04593 1.64903 6.81758 0.396845 12.7602C-0.127324 15.307 -0.127324 16.9171 0.367724 19.3468C1.70727 25.6993 7.88082 33.5154 18.5972 42.444L21.0724 44.5225L21.3927 43.1173C22.1499 39.8972 23.402 37.9944 25.6152 36.7941C27.2751 35.8574 28.3525 35.9159 30.158 36.9698C31.5849 37.8187 33.2739 40.5412 33.7398 42.7367C33.9437 43.7321 34.1766 44.5225 34.264 44.5225C34.5261 44.5225 40.8161 39.0775 43.5243 36.5307C51.7363 28.7438 56.0461 20.9862 55.4637 15.0436C55.0269 10.711 53.2797 7.05178 50.2511 4.24147C44.2814 -1.37913 35.4579 -1.4084 29.5756 4.12438L27.7701 5.82227L25.9646 4.15365C22.9361 1.34335 19.4708 -0.00325012 15.3939 0.0552979C14.2 0.0552979 12.5692 0.260216 11.783 0.465134ZM32.7206 9.36442C38.7486 12.3504 41.1947 19.4932 38.1953 25.4066C37.2634 27.2801 34.7008 29.8269 32.808 30.7344C27.0712 33.4862 20.0532 31.0857 17.2867 25.4066C16.0054 22.7134 15.6851 20.6934 16.151 17.9417C16.7626 14.341 19.1796 11.0916 22.5284 9.42297C24.596 8.39838 25.4405 8.22274 28.2943 8.33983C30.4492 8.42765 31.119 8.57402 32.7206 9.36442Z"
                                    fill="black" />
                                <path
                                    d="M24.0136 9.97903C21.0142 11.15 18.9757 13.2577 17.7235 16.4193C16.7917 18.7612 16.9664 22.3619 18.1021 24.616C19.2378 26.8116 21.2471 28.8022 23.3729 29.7975C24.9163 30.5001 25.4987 30.6172 27.7701 30.6172C29.9833 30.6172 30.653 30.5001 32.0217 29.8561C39.1271 26.4896 40.5831 17.5024 34.8755 12.2039C32.6624 10.1547 30.7113 9.39355 27.6828 9.39355C26.1976 9.42283 24.9745 9.59847 24.0136 9.97903ZM31.0316 13.4334C30.9151 14.0188 30.9151 15.0142 31.0025 15.5996L31.1772 16.6828L33.2739 16.7706L35.3414 16.8584V20.0493V23.2694L33.3613 23.2987C32.2838 23.328 31.3228 23.4451 31.2063 23.5329C31.119 23.65 31.0025 24.616 31.0025 25.6992L30.9734 27.6898L27.6828 27.7776L24.4213 27.8654V25.6699V23.5036L23.0526 23.328C22.2663 23.2401 21.3345 23.2109 20.9268 23.2401L20.1988 23.2987L20.1114 19.9907L20.0241 16.712H22.2372H24.4213V14.5165V12.321H27.7992H31.2063L31.0316 13.4334Z"
                                    fill="black" />
                            </mask>
                            <g mask="url(#footer-mask-logo)">
                                <rect x="-25.5311" y="-23.4609" width="106.265" height="91.7739" fill="#0086DA" />
                            </g>
                        </svg>
                    </div>
                    <div>
                        <div class="text-[.88rem] font-extrabold tracking-[.05em] text-white">TEJADA CLINIC</div>
                        <div class="text-[.57rem] font-semibold uppercase tracking-[.2em] text-white/50">Dental Care
                        </div>
                    </div>
                </div>
                <p class="text-[.8rem] leading-[1.75] italic text-white/52">"We don't just treat teeth; we care for the
                    people behind the smiles."</p>
            </div>

            <div>
                <div class="mb-[18px] text-[.6rem] font-bold uppercase tracking-[.2em] text-white/35">Quick Links</div>
                <div class="flex flex-col gap-[11px]">
                    @foreach (['Services' => 'services', 'About' => 'about', 'Why Us' => 'why-us', 'Hours' => 'hours', 'Contact' => 'contact'] as $label => $id)
                        <a href="/home#{{ $id }}"
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
                        <span class="text-[.8rem] leading-[1.6] text-white/60">251 Commonwealth Ave,<br>Diliman, Quezon
                            City</span>
                    </div>
                    <div class="flex items-center gap-[11px]">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                            stroke="rgba(255,255,255,.4)" stroke-width="2" stroke-linecap="square">
                            <path
                                d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.8 19.79 19.79 0 01.1 1.18 2 2 0 012.11 0h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 14.92z" />
                        </svg>
                        <a href="tel:+639123456789" class="text-[.8rem] text-white/60 no-underline">+63 912 345 6789</a>
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
                <div class="mb-[18px] text-[.6rem] font-bold uppercase tracking-[.2em] text-white/35">Ready to Visit?
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
                <p class="mt-3 text-[.7rem] leading-[1.6] text-white/38">Mon - Sat · 9:00 AM - 6:00 PM</p>
            </div>
        </div>

        <div
            class="mx-auto flex max-w-[1400px] flex-wrap items-center justify-between gap-3 px-6 py-5 md:px-12 xl:px-20">
            <p class="text-[.7rem] text-white/28">&copy; {{ date('Y') }} Tejada Clinic. All rights reserved.</p>
            <p class="text-[.7rem] text-white/28">251 Commonwealth Ave, Diliman, Quezon City</p>
        </div>
    </div>
</footer>
