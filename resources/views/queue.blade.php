@extends('index')

@section('style')
    :root {
    --queue-bg: #f1f5f9;
    --queue-surface: #ffffff;
    --queue-surface-soft: #f8fafc;
    --queue-border: #dbe3ef;
    --queue-border-strong: #c5d1e2;
    --queue-text: #0f172a;
    --queue-muted: #475569;
    --queue-primary: #334155;
    --queue-primary-soft: #e2e8f0;
    }
    .queue-shell {
    background:
        radial-gradient(circle at 0% 0%, rgba(148, 163, 184, 0.16), transparent 38%),
        radial-gradient(circle at 100% 0%, rgba(148, 163, 184, 0.1), transparent 30%),
        var(--queue-bg);
    }
    .queue-panel {
    background: var(--queue-surface);
    border: 1px solid var(--queue-border);
    box-shadow: 0 14px 36px rgba(15, 23, 42, 0.07);
    }
    .queue-chip {
    border: 1px solid var(--queue-border-strong);
    color: var(--queue-primary);
    background: var(--queue-primary-soft);
    }
    .queue-stat {
    background: var(--queue-surface-soft);
    border: 1px solid var(--queue-border);
    }
@endsection

@section('content')
    <main id="mainContent"
        class="min-h-screen queue-shell p-6 lg:p-8 ml-64 mt-14 transition-all duration-300 peer-[.collapsed]:ml-16">
        <div class="mb-4">
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Lobby Flow</h1>
        </div>

        <section class="queue-panel rounded-none p-4 lg:p-6">
            @livewire('today-schedule')
        </section>

        <livewire:patient-form-controller.patient-form-modal />
    </main>
@endsection
