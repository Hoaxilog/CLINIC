<section class="min-h-[392px] border border-gray-200 bg-white p-6 shadow-sm" wire:poll.20s="loadCancelledAppointments">
    <div class="mb-4 flex items-center justify-between gap-4 border-b border-gray-100 pb-4">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Cancelled Appointments</h2>
            <p class="mt-1 text-xs text-gray-500">Review cancellation reasons and contact the patient if follow-up is needed.</p>
        </div>
        <a href="{{ route('appointment.calendar') }}"
            class="text-xs font-semibold uppercase tracking-[0.14em] text-[#0086DA]">
            Open Calendar
        </a>
    </div>

    <div class="grid grid-cols-1 gap-4">
        @forelse ($cancelledAppointments as $appointment)
            <article wire:key="cancelled-appointment-{{ $appointment->id }}"
                class="border border-rose-100 bg-rose-50/50 p-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $appointment->patient_name }}</p>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y h:i A') }}
                            · {{ $appointment->service_name }}
                        </p>
                    </div>
                    <span class="inline-flex self-start border border-rose-200 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-rose-700">
                        Cancelled
                    </span>
                </div>

                <div class="mt-3 border-l-2 border-rose-200 pl-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-gray-500">Appointment Info</p>
                    <p class="mt-1 text-sm leading-6 text-gray-700">
                        {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F j, Y g:i A') }} · {{ $appointment->service_name }}
                    </p>
                </div>

                <div class="mt-3 border-l-2 border-slate-200 pl-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-gray-500">Contact</p>
                    <p class="mt-1 text-sm leading-6 text-gray-700">
                        {{ $appointment->contact_number ?: 'No contact number provided' }}
                    </p>
                    <p class="text-sm leading-6 text-gray-700">
                        {{ $appointment->email_address ?: 'No email address provided' }}
                    </p>
                </div>

                <div class="mt-3 border-l-2 border-rose-200 pl-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-rose-700">Reason</p>
                    <p class="mt-1 text-sm leading-6 text-gray-700">{{ $appointment->reason_label }}</p>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    @if ($appointment->phone_href)
                        <a href="{{ $appointment->phone_href }}"
                            class="whitespace-nowrap border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-800 transition hover:border-[#0086DA] hover:text-[#0086DA]">
                            Call Patient
                        </a>
                    @endif

                    @if ($appointment->mail_href)
                        <a href="{{ $appointment->mail_href }}"
                            class="whitespace-nowrap border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-800 transition hover:border-[#0086DA] hover:text-[#0086DA]">
                            Email Patient
                        </a>
                    @endif

                    <a href="{{ route('appointment.calendar') }}"
                        class="whitespace-nowrap border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-800 transition hover:border-[#0086DA] hover:text-[#0086DA]">
                        Review Schedule
                    </a>
                </div>
            </article>
        @empty
            <div class="flex min-h-[260px] items-center justify-center border border-dashed border-gray-200 bg-gray-50 px-6 text-center text-sm text-gray-500">
                No cancelled appointments need follow-up right now.
            </div>
        @endforelse
    </div>
</section>
