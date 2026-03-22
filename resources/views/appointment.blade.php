@extends('index')

@section('content')
    <div class="mb-4">
        <h1 class="text-xl font-bold tracking-tight text-slate-900">Appointments</h1>
    </div>

    <section>
        @livewire(\App\Livewire\Appointment\AppointmentCalendar::class, ['initialTab' => $initialTab ?? null])
    </section>
@endsection
