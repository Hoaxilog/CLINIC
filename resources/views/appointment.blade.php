@extends('index')

@section('content')
    <main id="mainContent"
        class="min-h-screen bg-gray-100 p-6 lg:p-8 ml-64 mt-14 transition-all duration-300 peer-[.collapsed]:ml-16">
        <div class="mb-4">
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Appointments</h1>
        </div>

        <section>
            @livewire('appointment-calendar', ['initialTab' => $initialTab ?? null])
        </section>
    </main>
@endsection
