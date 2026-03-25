@if ($activeTab === 'pending' && auth()->user()->role !== 3)
    <div class="border border-[#e4eff8] bg-white" style="font-family:'Montserrat',sans-serif;">

        {{-- ── Header ── --}}
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-[#e4eff8]">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-[#0086da] flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
                        stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="0" />
                        <path d="M16 2v4M8 2v4M3 10h18" />
                    </svg>
                </div>
                <div>
                    <div class="text-[.55rem] font-bold uppercase tracking-[.2em] text-[#7a9db5]">Pending</div>
                    <div class="text-[.95rem] font-extrabold text-[#1a2e3b] tracking-[-0.01em]">Appointment Requests</div>
                </div>
            </div>
            <div class="text-[.68rem] font-semibold uppercase tracking-[.12em] text-[#7a9db5]">
                Review and approve requests
            </div>
        </div>

        {{-- ── Column headers (desktop) ── --}}
        <div class="hidden md:grid grid-cols-5 gap-2 px-5 py-3 bg-[#f6fafd] border-b border-[#e4eff8]">
            <div class="text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Date &amp; Time</div>
            <div class="text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Patient</div>
            <div class="text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Service</div>
            <div class="text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Contact</div>
            <div class="text-right text-[.55rem] font-bold uppercase tracking-[.18em] text-[#7a9db5]">Action</div>
        </div>

        {{-- ── Rows ── --}}
        <div class="max-h-[calc(100vh-16rem)] overflow-y-auto divide-y divide-[#f0f6fb]">
            @forelse($this->getPendingApprovals() as $pending)
                <div wire:key="pending-appointment-{{ $pending->id }}"
                    class="grid grid-cols-1 md:grid-cols-5 gap-3 px-5 py-4 items-center transition hover:bg-[#f8fbfe]">

                    {{-- Date & Time --}}
                    <div>
                        <div class="text-[.82rem] font-bold text-[#1a2e3b]">
                            {{ \Carbon\Carbon::parse($pending->appointment_date)->format('M d, Y') }}
                        </div>
                        <div class="text-[.72rem] text-[#7a9db5] font-semibold">
                            {{ \Carbon\Carbon::parse($pending->appointment_date)->format('h:i A') }}
                        </div>
                    </div>

                    {{-- Patient --}}
                    <div>
                        <div class="flex items-center gap-2">
                            <div class="flex h-7 w-7 flex-shrink-0 items-center justify-center bg-[#f0f6fb]">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                                    fill="none" stroke="#1a2e3b" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-[.82rem] font-bold text-[#1a2e3b]">
                                    {{ $pending->last_name }}, {{ $pending->first_name }}
                                </div>
                                @if ($this->appointmentPatientBirthDateDisplay($pending))
                                    <div class="text-[.68rem] text-[#7a9db5]">
                                        {{ $this->appointmentPatientBirthDateDisplay($pending) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        @if ($this->appointmentHasSeparateRequester($pending))
                            <div class="mt-1.5 inline-flex items-center gap-1 px-2 py-0.5 bg-[#f0f6fb] border border-[#e4eff8]">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24"
                                    fill="none" stroke="#3d5a6e" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <line x1="19" x2="19" y1="8" y2="14" />
                                    <line x1="22" x2="16" y1="11" y2="11" />
                                </svg>
                                <span class="text-[.6rem] font-bold uppercase tracking-[.12em] text-[#3d5a6e]">
                                    Booked by {{ $this->appointmentRequesterDisplayName($pending) ?: 'Requester' }}
                                    @if ($this->appointmentRequesterRelationshipLabel($pending))
                                        ({{ $this->appointmentRequesterRelationshipLabel($pending) }})
                                    @endif
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Service --}}
                    <div>
                        <div class="text-[.8rem] font-semibold text-[#1a2e3b]">{{ $pending->service_name }}</div>
                    </div>

                    {{-- Contact --}}
                    <div>
                        <div class="text-[.8rem] font-semibold text-[#1a2e3b]">
                            {{ $pending->requester_contact_number ?? $pending->mobile_number ?? 'N/A' }}
                        </div>
                        <div class="text-[.68rem] text-[#7a9db5]">
                            {{ $pending->requester_email ?? $pending->email_address ?? 'N/A' }}
                        </div>
                    </div>

                    {{-- Action --}}
                    <div class="flex md:justify-end">
                        <button type="button"
                            @click="modalOpen = true"
                            wire:click="viewAppointment({{ $pending->id }})"
                            wire:loading.attr="disabled"
                            wire:target="viewAppointment({{ $pending->id }})"
                            class="inline-flex items-center gap-2 bg-[#0086da] px-4 py-[9px] text-[.68rem] font-bold uppercase tracking-[.1em] text-white transition hover:bg-[#006ab0] disabled:opacity-60">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            Review
                        </button>
                    </div>

                </div>
            @empty
                <div class="border border-dashed border-[#d4e8f5] bg-[#f8fbfe] m-5 p-8 text-center">
                    <div class="mx-auto mb-3 flex h-10 w-10 items-center justify-center bg-[#f0f6fb]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="#1a2e3b" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="0" />
                            <path d="M16 2v4M8 2v4M3 10h18" />
                        </svg>
                    </div>
                    <p class="text-[.78rem] font-semibold text-[#7a9db5]">No pending appointment requests.</p>
                    <p class="text-[.72rem] text-[#9bbdd0] mt-1">Requests will appear here once submitted.</p>
                </div>
            @endforelse
        </div>

    </div>
@endif
