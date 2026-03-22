@if ($activeTab === 'pending' && auth()->user()->role !== 3)
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-[#f7fbff] to-white">
            <div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Pending Approvals</h2>
                    <p class="text-xs text-gray-500">Review and approve appointment requests.</p>
                </div>
            </div>
        </div>

        <div
            class="hidden md:grid grid-cols-5 gap-2 px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 bg-gray-50 border-b border-gray-100">
            <div>Date &amp; Time</div>
            <div>Patient</div>
            <div>Service</div>
            <div>Contact</div>
            <div class="text-right">Actions</div>
        </div>

        <div class="max-h-[calc(100vh-16rem)] overflow-y-auto divide-y divide-gray-100">
            @forelse($this->getPendingApprovals() as $pending)
                <div wire:key="pending-appointment-{{ $pending->id }}"
                    class="grid grid-cols-1 md:grid-cols-5 gap-3 px-5 py-4 text-sm items-center hover:bg-gray-50 transition">
                    <div>
                        <div class="font-semibold text-gray-900">
                            {{ \Carbon\Carbon::parse($pending->appointment_date)->format('M d, Y') }}</div>
                        <div class="text-gray-500">
                            {{ \Carbon\Carbon::parse($pending->appointment_date)->format('h:i A') }}</div>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">
                            {{ $pending->last_name }}, {{ $pending->first_name }}
                        </div>
                        @if ($this->appointmentPatientBirthDateDisplay($pending))
                            <div class="text-xs text-gray-500">
                                Birth date: {{ $this->appointmentPatientBirthDateDisplay($pending) }}
                            </div>
                        @endif
                        @if ($this->appointmentHasSeparateRequester($pending))
                            <div class="mt-1 text-xs text-blue-700">
                                Booked by {{ $this->appointmentRequesterDisplayName($pending) ?: 'Requester' }}
                                @if ($this->appointmentRequesterRelationshipLabel($pending))
                                    ({{ $this->appointmentRequesterRelationshipLabel($pending) }})
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="text-gray-700">{{ $pending->service_name }}</div>
                    <div class="text-gray-600">
                        <div>{{ $pending->requester_contact_number ?? $pending->mobile_number ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-400">
                            {{ $pending->requester_email ?? $pending->email_address ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="flex md:justify-end gap-2">
                        <button type="button" @click="modalOpen = true"
                            wire:click="viewAppointment({{ $pending->id }})" wire:loading.attr="disabled"
                            wire:target="viewAppointment({{ $pending->id }})"
                            class="{{ $btnSm }} {{ $btnPrimary }}">
                            Review
                        </button>
                        <button type="button" @click="modalOpen = true"
                            wire:click="viewAppointment({{ $pending->id }})" wire:loading.attr="disabled"
                            wire:target="viewAppointment({{ $pending->id }})"
                            class="{{ $btnSm }} {{ $btnOutlinePrimary }}">
                            Reschedule
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-8 text-sm text-gray-500">No pending approvals.</div>
            @endforelse
        </div>
    </div>
@endif
