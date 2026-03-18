<section class="bg-white border border-gray-100 rounded-none shadow-sm" wire:poll.15s="loadPendingApprovals">
    <div class="flex items-center justify-between p-5 border-b border-gray-100">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Pending Appointment Requests</h2>
            <p class="text-xs text-gray-500">Latest 5 requests awaiting approval.</p>
        </div>
        <a href="{{ route('appointment.requests') }}"
            class="px-3.5 py-1.5 text-xs font-semibold text-gray-700 bg-white border border-gray-200 rounded-none hover:bg-gray-50 transition">
            View All Requests
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50/80 text-xs uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-5 py-3 text-left">Patient</th>
                    <th class="px-5 py-3 text-left">Appointment Date</th>
                    <th class="px-5 py-3 text-left">Service</th>
                    <!-- <th class="px-5 py-3 text-left">Status</th> -->
                    <th class="px-5 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pendingApprovals as $pending)
                    <tr wire:key="pending-approval-{{ $pending->id }}" class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3">
                            <button type="button" wire:click="viewApproval({{ $pending->id }})"
                                class="text-left transition-colors hover:text-[#0086DA]">
                                <span class="block font-semibold text-gray-900">
                                    {{ $pending->last_name }}, {{ $pending->first_name }}
                                </span>
                                @if ($this->appointmentPatientBirthDateDisplay($pending))
                                    <span class="block text-xs text-gray-500">
                                        Birth date: {{ $this->appointmentPatientBirthDateDisplay($pending) }}
                                    </span>
                                @endif
                                @if ($this->appointmentHasSeparateRequester($pending))
                                    <span class="mt-1 block text-xs text-blue-700">
                                        Booked by {{ $this->appointmentRequesterDisplayName($pending) ?: 'Requester' }}
                                        @if ($this->appointmentRequesterRelationshipLabel($pending))
                                            ({{ $this->appointmentRequesterRelationshipLabel($pending) }})
                                        @endif
                                    </span>
                                @endif
                            </button>
                        </td>
                        <td class="px-5 py-3 text-gray-700">
                            {{ \Carbon\Carbon::parse($pending->appointment_date)->format('M d, Y h:i A') }}
                        </td>
                        <td class="px-5 py-3 text-gray-700">{{ $pending->service_name }}</td>
                        <!-- <td class="px-5 py-3">
                            <span
                                class="inline-flex rounded-none bg-amber-50 px-2.5 py-1 text-[11px] font-semibold text-amber-700">
                                {{ $pending->status }}
                            </span>
                        </td> -->
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-start gap-2">
                                <button type="button" wire:click="approveAppointment({{ $pending->id }})"
                                    wire:confirm="Approve this appointment request?"
                                    class="rounded-none border border-emerald-200 bg-emerald-50 px-2.5 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-100 transition">
                                    Approve
                                </button>
                                <a href="{{ route('appointment.requests', ['appointment' => $pending->id]) }}"
                                    class="rounded-none border border-blue-200 bg-blue-50 px-2.5 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100 transition">
                                    Review Request
                                </a>
                                <button type="button" wire:click="rejectAppointment({{ $pending->id }})"
                                    wire:confirm="Reject this appointment request?"
                                    class="rounded-none border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100 transition">
                                    Reject
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-sm text-gray-500">
                            No pending appointment requests.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($showDetails && $selectedApproval)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black opacity-60" wire:click="closeDetails"></div>
            <div class="relative bg-white rounded-none shadow-xl w-full max-w-4xl mx-4 z-10 overflow-hidden">
                <div class="px-6 py-4 flex items-center justify-between bg-white border-b">
                    <h3 class="text-2xl font-semibold text-gray-900">Appointment Details</h3>
                    <button
                        class="text-[#0086da] text-4xl flex items-center justify-center px-2 rounded-none hover:bg-[#e6f4ff] transition"
                        wire:click="closeDetails">&times;</button>
                </div>

                <div class="p-6 overflow-y-auto max-h-[85vh]">
                    <div class="mb-6 bg-gray-50 rounded-none p-5 border border-gray-100">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Date</label>
                                <input type="text"
                                    value="{{ \Carbon\Carbon::parse($selectedApproval->appointment_date)->format('F j, Y') }}"
                                    class="w-full border-0 bg-transparent p-0 text-xl font-bold text-gray-800"
                                    readonly />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Time</label>
                                <input type="text"
                                    value="{{ \Carbon\Carbon::parse($selectedApproval->appointment_date)->format('h:i A') }}"
                                    class="w-full border-0 bg-transparent p-0 text-xl font-bold text-gray-800"
                                    readonly />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Service</label>
                                <input type="text" value="{{ $selectedApproval->service_name }}"
                                    class="w-full border-0 bg-transparent p-0 text-xl font-bold text-gray-800"
                                    readonly />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                        <div>
                            <div class="rounded-none border border-gray-200 bg-white p-4">
                                <label class="block text-xs font-bold uppercase tracking-[0.2em] text-gray-400">Patient</label>
                                <div class="mt-2 text-lg font-semibold text-gray-900">
                                    {{ $selectedApproval->first_name }} {{ $selectedApproval->last_name }}
                                </div>
                                <div class="mt-1 text-sm text-gray-600">
                                    Birth date:
                                    {{ $this->appointmentPatientBirthDateDisplay($selectedApproval) ?: 'Not provided' }}
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="rounded-none border border-gray-200 bg-gray-50 p-4">
                                <label class="block text-xs font-bold uppercase tracking-[0.2em] text-gray-400">Booked By / Contact</label>
                                <div class="mt-2 text-lg font-semibold text-gray-900">
                                    {{ $this->appointmentRequesterDisplayName($selectedApproval) ?: 'Same as patient' }}
                                </div>
                                <div class="mt-1 text-sm text-gray-600">
                                    {{ $selectedApproval->requester_contact_number ?? $selectedApproval->mobile_number ?? 'N/A' }}
                                </div>
                                <div class="mt-1 text-sm text-gray-500">
                                    {{ $selectedApproval->requester_email ?? $selectedApproval->email_address ?? 'N/A' }}
                                </div>
                                @if ($this->appointmentRequesterRelationshipLabel($selectedApproval))
                                    <div class="mt-2 text-xs font-semibold uppercase tracking-wide text-blue-700">
                                        Relationship: {{ $this->appointmentRequesterRelationshipLabel($selectedApproval) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mb-8 rounded-none border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                        <div class="font-semibold text-blue-900">Approve first, link on arrival</div>
                        <p class="mt-1 text-xs">
                            Approval reserves the slot. If identity is still uncertain, staff can link or create the
                            patient record later when the patient arrives.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                        <button type="button" wire:click="closeDetails"
                            class="px-5 py-3 rounded-none bg-gray-200 hover:bg-gray-300 font-medium">Close</button>
                        @if ($selectedApproval->status === 'Pending')
                            <button type="button" wire:click="approveAppointment({{ $selectedApproval->id }})"
                                wire:confirm="Approve this appointment request?"
                                class="px-6 py-3 rounded-none bg-emerald-600 text-white text-lg font-bold shadow-md hover:bg-emerald-700 transition">
                                Approve Appointment
                            </button>
                        @endif
                        <button type="button" wire:click="rejectAppointment({{ $selectedApproval->id }})"
                            wire:confirm="Reject this appointment request?"
                            class="px-6 py-3 rounded-none bg-red-600 text-white text-lg font-bold shadow-md hover:bg-red-700 transition">
                            Reject Appointment
                        </button>
                        <a href="{{ route('appointment.requests', ['appointment' => $selectedApproval->id]) }}"
                            class="px-6 py-3 rounded-none bg-[#0086da] text-white text-lg font-bold shadow-md hover:bg-blue-600 transition text-center">
                            Review Request
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</section>
