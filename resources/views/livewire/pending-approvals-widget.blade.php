<section class="h-[420px] bg-white border border-gray-100 rounded-none shadow-sm" wire:poll.15s="loadPendingApprovals">
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

    <div class="h-[336px] overflow-auto">
        <table class="min-w-full text-sm">
            <thead class="sticky top-0 bg-gray-50/95 text-xs uppercase tracking-wide text-gray-500">
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
                            <div class="text-left">
                                <span class="block font-semibold text-gray-900">
                                    {{ $pending->last_name }}, {{ $pending->first_name }}
                                </span>
                                @if ($this->appointmentHasSeparateRequester($pending))
                                    <span class="mt-1 block text-xs text-blue-700">
                                        Booked by {{ $this->appointmentRequesterDisplayName($pending) ?: 'Requester' }}
                                        @if ($this->appointmentRequesterRelationshipLabel($pending))
                                            ({{ $this->appointmentRequesterRelationshipLabel($pending) }})
                                        @endif
                                    </span>
                                @endif
                            </div>
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
                            <div class="flex items-center justify-start">
                                <a href="{{ route('appointment.requests', ['appointment' => $pending->id]) }}"
                                    class="whitespace-nowrap rounded-none border border-blue-200 bg-blue-50 px-2.5 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100 transition">
                                    Review Request
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-20 text-center text-sm text-gray-500">
                            No pending appointment requests.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
