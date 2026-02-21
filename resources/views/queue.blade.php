@extends('index')

@section('style')
    :root {
        --queue-accent: #0b84d8;
        --queue-accent-dark: #0a6fb4;
        --queue-ink: #0f172a;
        --queue-muted: #64748b;
        --queue-bg: #f4f7fb;
        --queue-card: #ffffff;
    }
    .queue-shell {
        background: radial-gradient(1200px 600px at 20% -10%, rgba(11, 132, 216, 0.10), transparent 60%),
                    radial-gradient(900px 500px at 90% 0%, rgba(15, 118, 110, 0.10), transparent 60%),
                    var(--queue-bg);
    }
    .queue-header {
        background: linear-gradient(135deg, rgba(11, 132, 216, 0.14), rgba(11, 132, 216, 0.02));
        border: 1px solid rgba(11, 132, 216, 0.18);
    }
    .queue-chip {
        border: 1px solid rgba(11, 132, 216, 0.25);
        color: var(--queue-accent-dark);
        background: rgba(11, 132, 216, 0.08);
    }
    .queue-grid-card {
        background: var(--queue-card);
        border: 1px solid rgba(15, 23, 42, 0.06);
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
    }
@endsection

@section('content')
    <main id="mainContent" class="min-h-screen queue-shell p-6 lg:p-8 ml-64 mt-14 transition-all duration-300 peer-[.collapsed]:ml-16">
        <div class="queue-header rounded-2xl px-6 py-6 lg:px-8 lg:py-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-3xl lg:text-4xl font-extrabold text-[color:var(--queue-ink)] tracking-tight">Queue Control</h1>
                    <p class="text-sm lg:text-base text-[color:var(--queue-muted)] mt-1">Focus on Ready patients, call next, and open charts fast.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span class="queue-chip text-xs font-semibold px-3 py-1 rounded-full">Ready Queue</span>
                    <span class="queue-chip text-xs font-semibold px-3 py-1 rounded-full">Session</span>
                    <span class="queue-chip text-xs font-semibold px-3 py-1 rounded-full">Agenda</span>
                </div>
            </div>
        </div>

        <section class="mt-6 queue-grid-card rounded-2xl p-4 lg:p-6">
            @livewire('today-schedule')
        </section>

        <livewire:patient-form-controller.patient-form-modal />
    </main>
@endsection
