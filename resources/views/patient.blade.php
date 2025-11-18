@extends('index')

@section('content')
    {{-- 
      MODIFIED: 
      1. Changed 'min-h-screen' to 'h-[calc(100vh-3.5rem)]' (3.5rem = 14 * 0.25rem for mt-14)
      2. Added 'flex flex-col' to make this a flex container.
    --}}
    <main id="mainContent" class="bg-gray-100 p-6 lg:p-8 ml-64 mt-14 transition-all duration-300 peer-[.collapsed]:ml-16 h-[calc(100vh-3.5rem)] flex flex-col">
        {{-- MODIFIED: Added 'flex-1 overflow-hidden' to make the section fill the available space --}}
        <section class="flex-1 overflow-hidden">
            @livewire('patient-records')
        </section>
        @livewire('PatientFormController.patient-form-modal')
        @livewire('appointment-history-modal')

    </main>
@endsection