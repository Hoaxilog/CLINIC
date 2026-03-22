@extends('index')

@section('page_shell_class', 'h-[calc(100vh-4rem)] flex flex-col overflow-hidden')

@section('content')
    <section class="flex-1 overflow-hidden">
        @livewire('patient.patient-records')
    </section>
    @livewire('patient.form.patient-form-modal')
    @livewire('appointment.appointment-history-modal')
@endsection
