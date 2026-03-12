<section class="bg-white border border-gray-100 rounded-2xl shadow-sm" wire:poll.15s="loadPendingApprovals">
    <div class="flex items-center justify-between p-5 border-b border-gray-100">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Pending Appointment Requests</h2>
            <p class="text-xs text-gray-500">Latest 6 requests awaiting approval.</p>
        </div>
        <a href="{{ route('appointment') }}"
            class="px-3.5 py-1.5 text-xs font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
            View All Requests
        </a>
    </div>

    <div class="divide-y divide-gray-100">
        @forelse($pendingApprovals as $pending)
            <div class="flex flex-col gap-4 p-5 transition hover:bg-gray-50 md:flex-row md:items-center md:justify-between">
                <div class="flex min-w-0 flex-1 cursor-pointer items-center gap-4"
                    wire:click="viewApproval({{ $pending->id }})" title="Click to view details">

                    <div class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-gray-200 bg-slate-100 text-sm font-bold text-slate-600">
                        @if (!empty($pending->profile_picture))
                            <img src="{{ asset('storage/' . $pending->profile_picture) . '?v=' . urlencode((string) strtotime((string) data_get($pending, 'profile_picture_updated_at'))) }}"
                                alt="{{ $pending->first_name }} {{ $pending->last_name }} profile picture"
                                class="h-full w-full object-cover">
                        @else
                            {{ strtoupper(substr($pending->first_name ?? 'P', 0, 1) . substr($pending->last_name ?? '', 0, 1)) }}
                        @endif
                    </div>

                    <div class="min-w-0">
                        <h4 class="truncate text-base font-semibold text-gray-900">{{ $pending->first_name }} {{ $pending->last_name }}</h4>

                        <div class="mt-0.5 flex flex-wrap items-center gap-2 text-[11px] text-gray-500">
                            <div class="flex items-center gap-1.5 whitespace-nowrap">
                                <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ \Carbon\Carbon::parse($pending->appointment_date)->format('d M Y') }}
                            </div>
                            <span class="text-gray-300">•</span>
                            <div class="flex items-center gap-1.5 whitespace-nowrap">
                                <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ \Carbon\Carbon::parse($pending->appointment_date)->format('h:i A') }}
                            </div>
                            <span class="text-gray-300">•</span>
                            <span class="truncate">{{ $pending->service_name }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" wire:click="approveAppointment({{ $pending->id }})"
                        class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100">
                        Approve
                    </button>
                    <button type="button" wire:click="rejectAppointment({{ $pending->id }})"
                        wire:confirm="Reject this appointment request?"
                        class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                        Reject
                    </button>
                </div>
            </div>
        @empty
            <div class="px-5 py-8 text-center text-sm text-gray-500">
                No pending appointment requests.
            </div>
        @endforelse
    </div>

    @if ($showDetails && $selectedApproval)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black opacity-60" wire:click="closeDetails"></div>
            <div class="relative z-10 mx-4 w-full max-w-4xl overflow-hidden rounded-lg bg-white shadow-xl">
                <div class="flex items-center justify-between border-b bg-white px-6 py-4">
                    <h3 class="text-2xl font-semibold text-gray-900">Appointment Details</h3>
                    <button
                        class="flex items-center justify-center rounded-full px-2 text-4xl text-[#0086da] transition hover:bg-[#e6f4ff]"
                        wire:click="closeDetails">&times;</button>
                </div>

                <div class="max-h-[85vh] overflow-y-auto p-6">
                    <div class="mb-6 rounded-xl border border-gray-100 bg-gray-50 p-5">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div>
                                <label class="mb-1 block text-xs font-bold uppercase text-gray-400">Date</label>
                                <input type="text"
                                    value="{{ \Carbon\Carbon::parse($selectedApproval->appointment_date)->format('F j, Y') }}"
                                    class="w-full border-0 bg-transparent p-0 text-xl font-bold text-gray-800"
                                    readonly />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-bold uppercase text-gray-400">Time</label>
                                <input type="text"
                                    value="{{ \Carbon\Carbon::parse($selectedApproval->appointment_date)->format('h:i A') }}"
                                    class="w-full border-0 bg-transparent p-0 text-xl font-bold text-gray-800"
                                    readonly />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-bold uppercase text-gray-400">Service</label>
                                <input type="text" value="{{ $selectedApproval->service_name }}"
                                    class="w-full border-0 bg-transparent p-0 text-xl font-bold text-gray-800"
                                    readonly />
                            </div>
                        </div>
                    </div>

                    <div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">First Name</label>
                            <input type="text" value="{{ $selectedApproval->first_name }}"
                                class="w-full rounded-lg border bg-gray-50 px-4 py-2.5 font-medium text-gray-800"
                                readonly />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Last Name</label>
                            <input type="text" value="{{ $selectedApproval->last_name }}"
                                class="w-full rounded-lg border bg-gray-50 px-4 py-2.5 font-medium text-gray-800"
                                readonly />
                        </div>
                    </div>

                    <div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Contact Number</label>
                            <input type="text" value="{{ $selectedApproval->mobile_number ?? 'N/A' }}"
                                class="w-full rounded-lg border bg-gray-50 px-4 py-2.5 font-medium text-gray-800"
                                readonly />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                            <input type="text" value="{{ $selectedApproval->email_address ?? 'N/A' }}"
                                class="w-full rounded-lg border bg-gray-50 px-4 py-2.5 font-medium text-gray-800"
                                readonly />
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col justify-end gap-3 border-t border-gray-100 pt-4 sm:flex-row">
                        <button type="button" wire:click="closeDetails"
                            class="rounded bg-gray-200 px-5 py-3 font-medium hover:bg-gray-300">Close</button>
                        <button type="button" wire:click="rejectAppointment({{ $selectedApproval->id }})"
                            wire:confirm="Reject this appointment request?"
                            class="rounded bg-red-600 px-6 py-3 text-lg font-bold text-white shadow-md transition hover:bg-red-700">
                            Reject Appointment
                        </button>
                        <button type="button" wire:click="approveAppointment({{ $selectedApproval->id }})"
                            class="rounded bg-[#0086da] px-6 py-3 text-lg font-bold text-white shadow-md transition hover:bg-blue-600">
                            Approve Appointment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</section>
