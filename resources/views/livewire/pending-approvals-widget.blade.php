<section class="bg-white border border-gray-100 rounded-2xl shadow-sm" wire:poll.15s="loadPendingApprovals">
    <div class="flex items-center justify-between p-5 border-b border-gray-100">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Appointment Request</h2>
            <p class="text-xs text-gray-500">Pending requests awaiting approval.</p>
        </div>
        <button type="button"
            class="px-3.5 py-1.5 text-xs font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
            All Appointments
        </button>
    </div>

    <div class="divide-y divide-gray-100">
        @forelse($pendingApprovals as $pending)
            <div class="flex flex-col md:flex-row md:items-center justify-between p-5 hover:bg-gray-50 transition gap-4">
                
                <div class="flex items-center gap-4 flex-1 min-w-0 cursor-pointer"
                    wire:click="viewApproval({{ $pending->id }})" title="Click to view details">
                    
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($pending->first_name . ' ' . $pending->last_name) }}&background=f3f4f6&color=374151"
                        alt="Avatar" class="w-12 h-12 rounded-lg object-cover border border-gray-200 shrink-0">

                    <div class="min-w-0">
                        <h4 class="font-semibold text-gray-900 text-base truncate">{{ $pending->first_name }} {{ $pending->last_name }}</h4>
                        
                        <div class="flex items-center text-[11px] text-gray-500 mt-0.5 gap-2 flex-nowrap">
                            <div class="flex items-center gap-1.5 whitespace-nowrap">
                                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ \Carbon\Carbon::parse($pending->appointment_date)->format('d M Y') }}
                            </div>
                            
                            <span class="text-gray-300 shrink-0">|</span>
                            
                            <div class="flex items-center gap-1.5 whitespace-nowrap">
                                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ \Carbon\Carbon::parse($pending->appointment_date)->format('h:i A') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="shrink-0 flex items-center">
                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-blue-50 text-blue-600">
                        {{ $pending->service_name }}
                    </span>
                </div>

                <div class="flex items-center shrink-0 gap-2">
                    <button type="button" wire:click="viewApproval({{ $pending->id }})"
                        class="p-2.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition flex items-center justify-center"
                        title="View request">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>

                    <button type="button" wire:click="approveAppointment({{ $pending->id }})"
                        class="p-2.5 rounded-lg bg-gray-100 hover:bg-green-100 text-gray-600 hover:text-green-700 transition flex items-center justify-center"
                        title="Approve request">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </button>
                </div>

            </div>
        @empty
            <div class="p-8 text-center text-sm text-gray-500">
                No pending appointment requests.
            </div>
        @endforelse
    </div>

    @if ($showDetails && $selectedApproval)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black opacity-60" wire:click="closeDetails"></div>
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-4xl mx-4 z-10 overflow-hidden">
                <div class="px-6 py-4 flex items-center justify-between bg-white border-b">
                    <h3 class="text-2xl font-semibold text-gray-900">Appointment Details</h3>
                    <button
                        class="text-[#0086da] text-4xl flex items-center justify-center px-2 rounded-full hover:bg-[#e6f4ff] transition"
                        wire:click="closeDetails">&times;</button>
                </div>

                <div class="p-6 overflow-y-auto max-h-[85vh]">
                    <div class="mb-6 bg-gray-50 rounded-xl p-5 border border-gray-100">
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <input type="text" value="{{ $selectedApproval->first_name }}"
                                class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 text-gray-800 font-medium"
                                readonly />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <input type="text" value="{{ $selectedApproval->last_name }}"
                                class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 text-gray-800 font-medium"
                                readonly />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                            <input type="text" value="{{ $selectedApproval->mobile_number ?? 'N/A' }}"
                                class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 text-gray-800 font-medium"
                                readonly />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="text" value="{{ $selectedApproval->email_address ?? 'N/A' }}"
                                class="w-full border rounded-lg px-4 py-2.5 bg-gray-50 text-gray-800 font-medium"
                                readonly />
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                        <button type="button" wire:click="closeDetails"
                            class="px-5 py-3 rounded bg-gray-200 hover:bg-gray-300 font-medium">Close</button>
                        <button type="button" wire:click="approveAppointment({{ $selectedApproval->id }})"
                            class="px-6 py-3 rounded bg-[#0086da] text-white text-lg font-bold shadow-md hover:bg-blue-600 transition">
                            Approve Appointment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</section>
