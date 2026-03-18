@extends('layouts.app')

@section('content')
    @php
        $currentDate = \Carbon\Carbon::parse($appointment->appointment_date);
        $selectedSlot = old('selectedSlot', $currentSlotValue);
        $selectedDateValue = old('selectedDate', $selectedDate);
    @endphp

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400&display=swap');

        #patient-reschedule * {
            font-family: 'Montserrat', sans-serif;
        }
    </style>

    <main id="patient-reschedule" class="min-h-screen bg-[#f6fafd] px-6 py-8 md:px-12 xl:px-20">
        <div class="mx-auto flex w-full max-w-[980px] flex-col gap-6">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-[#e4eff8] pb-6">
                <div>
                    <p class="text-[.63rem] font-bold uppercase tracking-[.22em] text-[#0086da]">Pending Request</p>
                    <h1 class="mt-2 text-[1.8rem] font-extrabold leading-[1.1] tracking-[-.02em] text-[#1a2e3b]">
                        Reschedule Appointment
                    </h1>
                    <p class="mt-2 max-w-[620px] text-[.88rem] leading-[1.7] text-[#587189]">
                        Move your pending request to another available time.
                    </p>
                </div>
                <a href="{{ route('patient.dashboard') }}"
                    class="inline-flex items-center gap-[9px] border border-[#d4e8f5] bg-white px-6 py-[11px] text-[.72rem] font-bold uppercase tracking-[.1em] text-[#1a2e3b] transition hover:bg-[#f8fbfe]">
                    Back to Dashboard
                </a>
            </div>

            @if (session('failed'))
                <div class="border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    {{ session('failed') }}
                </div>
            @endif

            <section class="grid gap-6 lg:grid-cols-[.92fr_1.08fr]">
                <article class="border border-[#e4eff8] bg-white p-6">
                    <div
                        class="mb-4 inline-flex items-center gap-[10px] text-[.63rem] font-bold uppercase tracking-[.22em] text-[#0086da]">
                        Current Appointment Request
                    </div>

                    <div class="space-y-4">
                        <div class="border border-[#e4eff8] bg-[#f6fafd] p-4">
                            <p class="text-[.72rem] font-semibold uppercase tracking-[.12em] text-[#7a9db5]">Service</p>
                            <p class="mt-2 text-[1rem] font-semibold text-[#1a2e3b]">
                                {{ $service->service_name ?? 'Service' }}</p>
                        </div>
                        <div class="grid gap-[2px] bg-[#e4eff8]">
                            <div class="flex items-center justify-between bg-[#f6fafd] px-4 py-3">
                                <span class="text-[.72rem] font-semibold uppercase tracking-[.1em] text-[#7a9db5]">Current
                                    Date</span>
                                <span
                                    class="text-sm font-semibold text-[#1a2e3b]">{{ $currentDate->format('M d, Y') }}</span>
                            </div>
                            <div class="flex items-center justify-between bg-[#f6fafd] px-4 py-3">
                                <span class="text-[.72rem] font-semibold uppercase tracking-[.1em] text-[#7a9db5]">Current
                                    Time</span>
                                <span
                                    class="text-sm font-semibold text-[#1a2e3b]">{{ $currentDate->format('h:i A') }}</span>
                            </div>
                            <div class="flex items-center justify-between bg-[#f6fafd] px-4 py-3">
                                <span
                                    class="text-[.72rem] font-semibold uppercase tracking-[.1em] text-[#7a9db5]">Status</span>
                                <span
                                    class="inline-flex border border-amber-200 bg-amber-50 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-amber-700">Pending</span>
                            </div>
                        </div>

                        <div class="border border-[#d4e8f5] bg-[#f8fbfe] p-4 text-[.82rem] leading-[1.7] text-[#587189]">
                            Online rescheduling is limited to help prevent slot abuse. If you can no longer change this
                            request online,
                            please contact the clinic directly.
                        </div>
                    </div>
                </article>

                <article class="border border-[#e4eff8] bg-white p-6">
                    <div
                        class="mb-4 inline-flex items-center gap-[10px] text-[.63rem] font-bold uppercase tracking-[.22em] text-[#0086da]">
                        Choose New Schedule
                    </div>

                    <form method="GET" action="{{ route('patient.appointments.reschedule.edit', $appointment->id) }}"
                        class="mb-6">
                        <label class="block text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e] mb-2">
                            Select Date
                        </label>
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <input type="date" name="date" value="{{ $selectedDateValue }}"
                                min="{{ now()->toDateString() }}"
                                onchange="this.form.submit()"
                                class="w-full border border-[#d4e8f5] bg-white px-4 py-3 text-sm text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#cde8fb]">
                        </div>
                    </form>

                    <form method="POST" action="{{ route('patient.appointments.reschedule.update', $appointment->id) }}">
                        @csrf
                        <input type="hidden" name="selectedDate" value="{{ $selectedDateValue }}">

                        <div class="mb-4">
                            <p class="text-[.63rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e]">
                                Available Times for {{ \Carbon\Carbon::parse($selectedDateValue)->format('F d, Y') }}
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                            @foreach ($availableSlots as $slot)
                                @php
                                    $disabled =
                                        !empty($slot['is_full']) ||
                                        !empty($slot['is_past']) ||
                                        !empty($slot['is_blocked']);
                                @endphp
                                <label class="{{ $disabled ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }}">
                                    <input type="radio" name="selectedSlot" value="{{ $slot['value'] }}"
                                        @checked($selectedSlot === $slot['value']) @disabled($disabled) class="peer sr-only">
                                    <div
                                        class="border px-3 py-3 text-center text-[.74rem] font-semibold transition
                                        {{ $disabled
                                            ? 'border-[#e4eff8] bg-[#f8fbfe] text-[#b2c2cf]'
                                            : 'border-[#d4e8f5] bg-white text-[#1a2e3b] hover:border-[#0086da] hover:bg-[#f0f8fe] peer-checked:border-[#0086da] peer-checked:bg-[#0086da] peer-checked:text-white' }}">
                                        {{ $slot['time'] }}
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        @error('selectedDate')
                            <p class="mt-3 text-[.78rem] text-rose-600">{{ $message }}</p>
                        @enderror

                        @error('selectedSlot')
                            <p class="mt-3 text-[.78rem] text-rose-600">{{ $message }}</p>
                        @enderror

                        <div class="mt-6 flex flex-col gap-3 border-t border-[#e4eff8] pt-6 sm:flex-row">
                            <button type="submit"
                                class="inline-flex items-center justify-center bg-[#0086da] px-8 py-[14px] text-[.72rem] font-bold uppercase tracking-[.1em] text-white transition hover:bg-[#006ab0]">
                                Save New Schedule
                            </button>
                            <a href="{{ route('patient.dashboard') }}"
                                class="inline-flex items-center justify-center border border-[#d4e8f5] bg-white px-8 py-[14px] text-[.72rem] font-bold uppercase tracking-[.1em] text-[#1a2e3b] transition hover:bg-[#f8fbfe]">
                                Cancel
                            </a>
                        </div>
                    </form>
                </article>
            </section>
        </div>
    </main>
@endsection
