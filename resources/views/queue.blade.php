@extends('index')

@section('style')
    :root {
    --queue-accent: #0b84d8;
    --queue-accent-dark: #0a6fb4;
    --queue-ink: #0f172a;
    --queue-muted: #64748b;
    --queue-bg: #f3f4f6;
    --queue-card: #ffffff;
    }
    .queue-shell {
    background: var(--queue-bg);
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
    <main id="mainContent"
        class="bg-[#f3f4f6] min-h-screen queue-shell p-6 lg:p-8 ml-64 mt-14 transition-all duration-300 peer-[.collapsed]:ml-16">
        <div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Live Patient Board</h1>
                <p class="text-sm text-gray-500 mt-1">Track arrivals, monitor lobby wait times, and manage active treatments.
                </p>

            </div>

            <div class="flex items-center gap-3">
                <div class="bg-white border border-gray-200 px-4 py-2 rounded-lg shadow-sm flex items-center gap-2"
                    title="Auto-refreshing every 5 seconds">
                    <span class="relative flex h-2.5 w-2.5">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">Live Sync On</span>
                </div>
            </div>
        </div>

        <section class="mt-6 queue-grid-card rounded-2xl p-4 lg:p-6">
            @livewire('today-schedule')
        </section>

        <livewire:patient-form-controller.patient-form-modal />
    </main>
@endsection
