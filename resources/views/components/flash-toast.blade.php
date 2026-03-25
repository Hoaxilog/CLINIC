{{--
    Unified Flash Toast Notification — Tejada Clinic
    ──────────────────────────────────────────────────
    Works two ways:

    1. SESSION FLASH (controller redirects):
       return redirect()->with('success' | 'error' | 'info', 'Your message here');

    2. LIVEWIRE DISPATCH (Livewire components, no page reload):
       $this->dispatch('flash-message', type: 'success'|'error'|'info', message: 'Your message here');

    Drop <x-flash-toast /> anywhere in a layout or page. It renders nothing if silent.
--}}

@php
    $fType = session('success') ? 'success' : (session('error') ? 'error' : (session('info') ? 'info' : null));
    $fMsg  = session('success') ?? session('error') ?? session('info') ?? null;
@endphp

{{-- ── 1. SESSION-BASED TOAST ─────────────────────────────────────── --}}
@if ($fType)
<div
    x-cloak
    x-data="{ visible: true }"
    x-show="visible"
    x-init="setTimeout(() => visible = false, 5000)"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 -translate-y-3"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 -translate-y-3"
    role="status"
    aria-live="polite"
    style="font-family:'Montserrat',sans-serif; position:fixed; top:24px; left:50%; transform:translateX(-50%); z-index:9999; width:100%; max-width:480px; padding:0 16px;"
>
    <div class="flex items-start gap-3 bg-white shadow-[0_8px_40px_rgba(0,0,0,0.13)] px-5 py-4
        @if($fType === 'success') border-l-4 border-[#0086da]
        @elseif($fType === 'error') border-l-4 border-red-500
        @else border-l-4 border-[#3d5a6e] @endif">

        {{-- Icon Badge --}}
        <div class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center
            @if($fType === 'success') bg-[#0086da]
            @elseif($fType === 'error') bg-red-500
            @else bg-[#3d5a6e] @endif">
            @if($fType === 'success')
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="square" d="M5 13l4 4L19 7"/></svg>
            @elseif($fType === 'error')
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="square" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @else
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="square" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @endif
        </div>

        {{-- Text --}}
        <div class="flex-1 min-w-0">
            <div class="text-[.68rem] font-black uppercase tracking-[.13em]
                @if($fType === 'success') text-[#0086da]
                @elseif($fType === 'error') text-red-500
                @else text-[#3d5a6e] @endif">
                @if($fType === 'success') Success @elseif($fType === 'error') Error @else Notice @endif
            </div>
            <div class="mt-0.5 text-[.82rem] font-medium leading-snug text-[#1a2e3b]">{{ $fMsg }}</div>
        </div>

        {{-- Dismiss --}}
        <button @click="visible = false" class="ml-1 shrink-0 p-1 text-[#7a9db5] hover:text-[#1a2e3b] transition">
            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
        </button>
    </div>

    {{-- Progress bar --}}
    <div class="h-[2px] w-full
        @if($fType === 'success') bg-[#0086da]/20
        @elseif($fType === 'error') bg-red-500/20
        @else bg-[#3d5a6e]/20 @endif">
        <div class="h-full animate-[shrink_5s_linear_forwards]
            @if($fType === 'success') bg-[#0086da]
            @elseif($fType === 'error') bg-red-500
            @else bg-[#3d5a6e] @endif"
            style="width:100%"></div>
    </div>
</div>
@endif

{{-- ── 2. LIVEWIRE / JS DISPATCH TOAST ─────────────────────────────── --}}
<div
    x-cloak
    x-data="{
        visible: false,
        type: 'success',
        message: '',
        _timer: null,
        show(detail) {
            this.type    = detail.type    || 'success';
            this.message = detail.message || '';
            this.visible = true;
            clearTimeout(this._timer);
            this._timer  = setTimeout(() => this.visible = false, 5000);
        }
    }"
    x-show="visible"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 -translate-y-3"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 -translate-y-3"
    @flash-message.window="show($event.detail)"
    role="status"
    aria-live="polite"
    style="font-family:'Montserrat',sans-serif; position:fixed; top:24px; left:50%; transform:translateX(-50%); z-index:9999; width:100%; max-width:480px; padding:0 16px;"
>
    {{-- Success --}}
    <div x-cloak x-show="type === 'success'" class="flex items-start gap-3 bg-white border-l-4 border-[#0086da] shadow-[0_8px_40px_rgba(0,0,0,0.13)] px-5 py-4">
        <div class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center bg-[#0086da]">
            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="square" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div class="flex-1 min-w-0">
            <div class="text-[.68rem] font-black uppercase tracking-[.13em] text-[#0086da]">Success</div>
            <div class="mt-0.5 text-[.82rem] font-medium leading-snug text-[#1a2e3b]" x-text="message"></div>
        </div>
        <button @click="visible = false" class="ml-1 shrink-0 p-1 text-[#7a9db5] hover:text-[#1a2e3b] transition">
            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
        </button>
    </div>

    {{-- Error --}}
    <div x-cloak x-show="type === 'error'" class="flex items-start gap-3 bg-white border-l-4 border-red-500 shadow-[0_8px_40px_rgba(0,0,0,0.13)] px-5 py-4">
        <div class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center bg-red-500">
            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="square" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="flex-1 min-w-0">
            <div class="text-[.68rem] font-black uppercase tracking-[.13em] text-red-500">Error</div>
            <div class="mt-0.5 text-[.82rem] font-medium leading-snug text-[#1a2e3b]" x-text="message"></div>
        </div>
        <button @click="visible = false" class="ml-1 shrink-0 p-1 text-[#7a9db5] hover:text-[#1a2e3b] transition">
            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
        </button>
    </div>

    {{-- Info --}}
    <div x-cloak x-show="type === 'info'" class="flex items-start gap-3 bg-white border-l-4 border-[#3d5a6e] shadow-[0_8px_40px_rgba(0,0,0,0.13)] px-5 py-4">
        <div class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center bg-[#3d5a6e]">
            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="square" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="flex-1 min-w-0">
            <div class="text-[.68rem] font-black uppercase tracking-[.13em] text-[#3d5a6e]">Notice</div>
            <div class="mt-0.5 text-[.82rem] font-medium leading-snug text-[#1a2e3b]" x-text="message"></div>
        </div>
        <button @click="visible = false" class="ml-1 shrink-0 p-1 text-[#7a9db5] hover:text-[#1a2e3b] transition">
            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
        </button>
    </div>

    {{-- Progress bar --}}
    <div class="h-[2px] w-full" :class="{
        'bg-[#0086da]/20': type === 'success',
        'bg-red-500/20':   type === 'error',
        'bg-[#3d5a6e]/20': type === 'info'
    }">
        <div class="h-full animate-[shrink_5s_linear_forwards]" :class="{
            'bg-[#0086da]': type === 'success',
            'bg-red-500':   type === 'error',
            'bg-[#3d5a6e]': type === 'info'
        }" style="width:100%"></div>
    </div>
</div>

<style>
@keyframes shrink {
    from { width: 100%; }
    to   { width: 0%; }
}
</style>
