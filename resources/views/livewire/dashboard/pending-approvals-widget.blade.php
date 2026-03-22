@php
    $pendingCount = $pendingApprovals->count();
@endphp

<section class="h-[420px] overflow-hidden border border-amber-200 bg-white rounded-none shadow-sm shadow-amber-100/40" wire:poll.15s="loadPendingApprovals">
    <div class="h-1 w-full bg-linear-to-r from-amber-500 via-orange-400 to-amber-300"></div>

    <div class="flex items-center justify-between border-b border-amber-100 bg-amber-50/40 p-5">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-lg font-semibold text-gray-900">Pending Appointment Requests</h2>
                
            </div>
            <p class="text-xs text-gray-600">Latest requests appointment approval.</p>
        </div>
        <a href="{{ route('appointment.requests') }}"
            class="px-3.5 py-1.5 text-xs font-semibold text-amber-900 bg-white border border-amber-200 rounded-none hover:bg-amber-50 transition">
            View All Requests
        </a>
    </div>

    <div class="h-[336px] overflow-auto">
        <table class="min-w-full text-sm">
            <thead class="sticky top-0 bg-amber-50/80 text-xs uppercase tracking-wide text-gray-500">
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
                    <tr wire:key="pending-approval-{{ $pending->id }}" class="border-l-2 border-transparent hover:border-amber-300 hover:bg-amber-50/40 transition-colors">
                        <td class="px-5 py-3">
                            <div class="text-left">
                                <div class="flex items-center gap-2">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M10 2a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-7 15a7 7 0 1 1 14 0H3Z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                    <span class="block font-semibold text-gray-900">
                                        {{ $pending->first_name }} {{ $pending->last_name }}
                                    </span>
                                </div>
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
                            <div class="flex flex-col gap-0.5">
                                <span class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($pending->appointment_date)->format('M d, Y') }}</span>
                                <span class="text-xs text-amber-700">{{ \Carbon\Carbon::parse($pending->appointment_date)->format('h:i A') }}</span>
                            </div>
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
                                    class="whitespace-nowrap rounded-none border border-amber-200 bg-amber-50 px-2.5 py-1.5 text-xs font-semibold text-amber-800 hover:bg-amber-100 transition">
                                    Review Now
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-20 text-center text-sm text-gray-500">
                            No pending appointment requests.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
