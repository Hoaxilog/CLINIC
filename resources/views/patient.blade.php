@extends('index')

@section('content')
    <main id="mainContent" class="bg-gray-100 p-6 lg:p-8 ml-64 mt-14 transition-all duration-300 peer-[.collapsed]:ml-16 h-[calc(100vh-3.5rem)] flex flex-col">
        <section class="flex-1 overflow-hidden">
            @livewire('patient-records')
        </section>
        @livewire('PatientFormController.patient-form-modal')
        @livewire('appointment-history-modal')
    </main>
@endsection