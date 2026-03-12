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
        <section class="queue-panel rounded-2xl p-5 lg:p-6">
            <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-500">Clinic Queue Monitor</p>
                    <h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">Patient Flow Board</h1>
                    <p class="mt-2 text-sm text-slate-600">Track scheduled, waiting, and active treatments in real time.</p>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="queue-chip inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-semibold tracking-wide">
                        Live Status
                    </span>
                    <span class="queue-chip inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-semibold tracking-wide">
                        {{ now()->format('M d, Y') }}
                    </span>
                </div>
            </div>
        </section>

        <section class="mt-5 queue-panel rounded-2xl p-4 lg:p-6">
            <div class="mb-4 flex items-end justify-between border-b border-slate-200 pb-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.1em] text-slate-500">Queue Details</p>
                    <p class="mt-1 text-sm text-slate-600">Real-time patient flow and treatment progression.</p>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-2 lg:p-3">
                @livewire('today-schedule')
            </div>
        </section>

        <livewire:patient-form-controller.patient-form-modal />
    </main>
@endsection
