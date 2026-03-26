<section class="space-y-6">
    <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-[.65rem] font-bold uppercase tracking-[.22em] text-[#0086DA]">Appointments</p>
            <h1 class="mt-2 text-2xl font-extrabold tracking-[-0.02em] text-slate-900">Appointment History</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">
                Full appointment log for staff follow-up. This view starts focused on cancelled records so replacements and recovery work are easier to track.
            </p>
        </div>
        <div class="rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            Cancelled appointments are shown by default.
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            <label class="block">
                <span class="mb-2 block text-[.65rem] font-bold uppercase tracking-[.18em] text-slate-500">Search</span>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Patient, service, or ID"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#0086DA] focus:bg-white focus:ring-2 focus:ring-sky-100">
            </label>

            <label class="block">
                <span class="mb-2 block text-[.65rem] font-bold uppercase tracking-[.18em] text-slate-500">Status</span>
                <select wire:model.live="status"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#0086DA] focus:bg-white focus:ring-2 focus:ring-sky-100">
                    <option value="Cancelled">Cancelled</option>
                    <option value="Completed">Completed</option>
                </select>
            </label>

            <label class="block">
                <span class="mb-2 block text-[.65rem] font-bold uppercase tracking-[.18em] text-slate-500">Service</span>
                <select wire:model.live="serviceId"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#0086DA] focus:bg-white focus:ring-2 focus:ring-sky-100">
                    <option value="">All services</option>
                    @foreach ($serviceOptions as $id => $label)
                        <option value="{{ $id }}">{{ $label }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block">
                <span class="mb-2 block text-[.65rem] font-bold uppercase tracking-[.18em] text-slate-500">From</span>
                <input type="date" wire:model.live="fromDate"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#0086DA] focus:bg-white focus:ring-2 focus:ring-sky-100">
            </label>

            <label class="block">
                <span class="mb-2 block text-[.65rem] font-bold uppercase tracking-[.18em] text-slate-500">To</span>
                <div class="flex gap-2">
                    <input type="date" wire:model.live="toDate"
                        class="min-w-0 flex-1 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#0086DA] focus:bg-white focus:ring-2 focus:ring-sky-100">
                    <button type="button" wire:click="resetFilters"
                        class="shrink-0 rounded-xl border border-slate-200 px-4 py-3 text-[.68rem] font-bold uppercase tracking-[.14em] text-slate-600 transition hover:border-slate-300 hover:bg-slate-50">
                        Reset
                    </button>
                </div>
            </label>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
            <div>
                <p class="text-[.65rem] font-bold uppercase tracking-[.18em] text-slate-500">Results</p>
                <p class="mt-1 text-sm text-slate-600">{{ $appointments->total() }} appointment records found.</p>
            </div>
            <div wire:loading class="text-xs font-semibold uppercase tracking-[.14em] text-[#0086DA]">Updating...</div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-[.65rem] font-bold uppercase tracking-[.16em] text-slate-500">
                        <th class="px-6 py-4">Appointment</th>
                        <th class="px-6 py-4">Patient</th>
                        <th class="px-6 py-4">Service</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Cancellation Reason</th>
                        <th class="px-6 py-4">Status Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($appointments as $appointment)
                        @php
                            $statusClass = match($appointment->status) {
                                'Cancelled' => 'border-rose-200 bg-rose-50 text-rose-700',
                                'Completed' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                                'Scheduled' => 'border-sky-200 bg-sky-50 text-sky-700',
                                'Pending' => 'border-amber-200 bg-amber-50 text-amber-700',
                                'Waiting', 'Ongoing' => 'border-indigo-200 bg-indigo-50 text-indigo-700',
                                default => 'border-slate-200 bg-slate-50 text-slate-700',
                            };
                        @endphp
                        <tr class="align-top hover:bg-slate-50/70">
                            <td class="px-6 py-5">
                                <p class="font-semibold text-slate-900">#{{ $appointment->id }}</p>
                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y h:i A') }}
                                </p>
                            </td>
                            <td class="px-6 py-5">
                                <p class="font-semibold text-slate-900">{{ $appointment->patient_name }}</p>
                            </td>
                            <td class="px-6 py-5 text-slate-700">
                                {{ $appointment->service_name ?: 'No service assigned' }}
                            </td>
                            <td class="px-6 py-5">
                                <span class="inline-flex rounded-full border px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[.14em] {{ $statusClass }}">
                                    {{ $appointment->status }}
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <p class="max-w-md leading-6 text-slate-600">
                                    {{ $appointment->status === 'Cancelled' ? $appointment->reason_label : 'Not applicable.' }}
                                </p>
                            </td>
                            <td class="px-6 py-5 text-slate-500">
                                @if (in_array($appointment->status, ['Cancelled', 'Completed'], true) && $appointment->updated_at)
                                    <p class="font-medium text-slate-700">{{ $appointment->status }} {{ \Carbon\Carbon::parse($appointment->updated_at)->diffForHumans() }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ \Carbon\Carbon::parse($appointment->updated_at)->format('M d, Y h:i A') }}</p>
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="mx-auto max-w-md rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-6 py-8">
                                    <p class="text-sm font-semibold text-slate-700">No appointment history matched the current filters.</p>
                                    <p class="mt-2 text-sm leading-6 text-slate-500">Try broadening the date range or switch the status filter to view more records.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($appointments->hasPages())
            <div class="border-t border-slate-200 px-6 py-4">
                {{ $appointments->links() }}
            </div>
        @endif
    </div>
</section>
