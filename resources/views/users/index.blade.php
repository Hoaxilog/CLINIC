@extends('index')

@section('page_shell_class', 'bg-[#f6fafd]')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400&display=swap');
    #users-wrap * { font-family: 'Montserrat', sans-serif; }
</style>

<div id="users-wrap" style="font-family:'Montserrat',sans-serif; -webkit-font-smoothing:antialiased;">

    {{-- Page Banner --}}
    <div class="mb-6 border border-[#e4eff8] bg-white">
        <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-6 md:px-8">
            <div>
                <h1 class="text-[1.35rem] font-extrabold leading-[1.1] tracking-[-.02em] text-[#1a2e3b]">User Accounts</h1>
                <p class="mt-1 text-[.8rem] text-[#7a9db5]">Manage admin, dentist, and staff profiles, permissions, and lifecycle actions.</p>
            </div>
            <a href="{{ route('users.create') }}"
                class="inline-flex items-center gap-2 bg-[#0086da] hover:bg-[#006ab0] px-6 py-[11px] text-[.72rem] font-bold uppercase tracking-[.1em] text-white transition hover:-translate-y-px">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="square">
                    <path d="M5 12h14M12 5v14" />
                </svg>
                Add New User
            </a>
        </div>
    </div>


    {{-- ── Admins ── --}}
    <section class="mb-6 border border-[#e4eff8] bg-white shadow-[0_20px_48px_rgba(0,134,218,.07)]">
        <div class="flex items-center gap-3 border-b border-[#e4eff8] px-6 py-5 md:px-8">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center bg-[#0086da]">
                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/>
                    <path d="M6.376 18.91a6 6 0 0 1 11.249.003"/>
                    <circle cx="12" cy="11" r="4"/>
                </svg>
            </div>
            <div class="flex-1">
                <div class="text-[.95rem] font-extrabold tracking-[-0.01em] text-[#1a2e3b]">Admins</div>
            </div>
            <span class="border border-[#d4e8f5] bg-[#f0f8fe] px-3 py-1 text-[.65rem] font-bold uppercase tracking-[.14em] text-[#0086da]">
                {{ $admins->total() ?? $admins->count() }}
            </span>
        </div>

        <div class="p-6 md:p-8">
            <p class="mb-6 text-[.78rem] text-[#7a9db5]">Administrative users with full system control.</p>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @forelse($admins as $user)
                    <article class="relative border border-[#e4eff8] bg-white transition hover:shadow-[0_8px_24px_rgba(0,134,218,.1)] hover:-translate-y-0.5">
                        <div class="p-5">
                            <div class="mb-4 flex items-center gap-3">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center bg-[#0086da] text-base font-bold text-white">
                                    {{ strtoupper(substr($user->username, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <h3 class="truncate text-[.88rem] font-extrabold text-[#1a2e3b]">{{ $user->username }}</h3>
                                    <p class="truncate text-[.72rem] text-[#7a9db5]">ID: #{{ $user->id }}</p>
                                </div>
                            </div>
                            <div class="mb-1 text-[.72rem] text-[#7a9db5]">
                                <span class="font-semibold text-[#3d5a6e]">Joined</span> {{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}
                            </div>
                        </div>
                        <div class="flex items-center gap-[2px] border-t border-[#e4eff8] bg-[#f8fbfe]">
                            <a href="{{ route('users.edit', $user->id) }}"
                                class="flex-1 px-3 py-2.5 text-center text-[.68rem] font-bold uppercase tracking-[.1em] text-[#0086da] transition hover:bg-[#e8f4fc]">
                                Edit
                            </a>
                            @if ($user->id !== auth()->id())
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="flex-1 border-l border-[#e4eff8]"
                                    onsubmit="return confirm('Are you sure you want to delete this admin?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full px-3 py-2.5 text-[.68rem] font-bold uppercase tracking-[.1em] text-red-500 transition hover:bg-red-50">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="col-span-full border border-dashed border-[#d4e8f5] bg-[#f8fbfe] px-4 py-10 text-center text-[.82rem] text-[#7a9db5]">
                        No admins found.
                    </div>
                @endforelse
            </div>
            <div class="mt-5">{{ $admins->links() }}</div>
        </div>
    </section>

    {{-- ── Dentists ── --}}
    <section class="mb-6 border border-[#e4eff8] bg-white shadow-[0_20px_48px_rgba(0,134,218,.07)]">
        <div class="flex items-center gap-3 border-b border-[#e4eff8] px-6 py-5 md:px-8">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center bg-[#0086da]">
                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M13.5 8h-3"/>
                    <path d="m15 2-1 2h3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h3"/>
                    <path d="M16.899 22A5 5 0 0 0 7.1 22"/>
                    <path d="m9 2 3 6"/>
                    <circle cx="12" cy="15" r="3"/>
                </svg>
            </div>
            <div class="flex-1">
                <div class="text-[.95rem] font-extrabold tracking-[-0.01em] text-[#1a2e3b]">Dentists</div>
            </div>
            <span class="border border-[#d4e8f5] bg-[#f0f8fe] px-3 py-1 text-[.65rem] font-bold uppercase tracking-[.14em] text-[#0086da]">
                {{ $dentists->total() ?? $dentists->count() }}
            </span>
        </div>

        <div class="p-6 md:p-8">
            <p class="mb-6 text-[.78rem] text-[#7a9db5]">Clinical users focused on appointments, queue flow, and patient care.</p>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @forelse($dentists as $user)
                    <article class="relative border border-[#e4eff8] bg-white transition hover:shadow-[0_8px_24px_rgba(0,134,218,.1)] hover:-translate-y-0.5">
                        <div class="p-5">
                            <div class="mb-4 flex items-center gap-3">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center bg-[#1a2e3b] text-base font-bold text-white">
                                    {{ strtoupper(substr($user->username, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <h3 class="truncate text-[.88rem] font-extrabold text-[#1a2e3b]">{{ $user->username }}</h3>
                                    <p class="truncate text-[.72rem] text-[#7a9db5]">ID: #{{ $user->id }}</p>
                                </div>
                            </div>
                            <div class="mb-1 text-[.72rem] text-[#7a9db5]">
                                <span class="font-semibold text-[#3d5a6e]">Joined</span> {{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}
                            </div>
                        </div>
                        <div class="flex items-center gap-[2px] border-t border-[#e4eff8] bg-[#f8fbfe]">
                            <a href="{{ route('users.edit', $user->id) }}"
                                class="flex-1 px-3 py-2.5 text-center text-[.68rem] font-bold uppercase tracking-[.1em] text-[#0086da] transition hover:bg-[#e8f4fc]">
                                Edit
                            </a>
                            @if ($user->id !== auth()->id())
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="flex-1 border-l border-[#e4eff8]"
                                    onsubmit="return confirm('Are you sure you want to delete this dentist account?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full px-3 py-2.5 text-[.68rem] font-bold uppercase tracking-[.1em] text-red-500 transition hover:bg-red-50">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="col-span-full border border-dashed border-[#d4e8f5] bg-[#f8fbfe] px-4 py-10 text-center text-[.82rem] text-[#7a9db5]">
                        No dentists found.
                    </div>
                @endforelse
            </div>
            <div class="mt-5">{{ $dentists->links() }}</div>
        </div>
    </section>

    {{-- ── Staff ── --}}
    <section class="mb-6 border border-[#e4eff8] bg-white shadow-[0_20px_48px_rgba(0,134,218,.07)]">
        <div class="flex items-center gap-3 border-b border-[#e4eff8] px-6 py-5 md:px-8">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center bg-[#0086da]">
                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M13.5 8h-3"/>
                    <path d="m15 2-1 2h3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h3"/>
                    <path d="M16.899 22A5 5 0 0 0 7.1 22"/>
                    <path d="m9 2 3 6"/>
                    <circle cx="12" cy="15" r="3"/>
                </svg>
            </div>
            <div class="flex-1">
                <div class="text-[.95rem] font-extrabold tracking-[-0.01em] text-[#1a2e3b]">Staff</div>
            </div>
            <span class="border border-[#d4e8f5] bg-[#f0f8fe] px-3 py-1 text-[.65rem] font-bold uppercase tracking-[.14em] text-[#0086da]">
                {{ $staffs->total() ?? $staffs->count() }}
            </span>
        </div>

        <div class="p-6 md:p-8">
            <p class="mb-6 text-[.78rem] text-[#7a9db5]">Operational users supporting daily clinic workflow.</p>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @forelse($staffs as $user)
                    <article class="relative border border-[#e4eff8] bg-white transition hover:shadow-[0_8px_24px_rgba(0,134,218,.1)] hover:-translate-y-0.5">
                        <div class="p-5">
                            <div class="mb-4 flex items-center gap-3">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center bg-[#3d5a6e] text-base font-bold text-white">
                                    {{ strtoupper(substr($user->username, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <h3 class="truncate text-[.88rem] font-extrabold text-[#1a2e3b]">{{ $user->username }}</h3>
                                    <p class="truncate text-[.72rem] text-[#7a9db5]">ID: #{{ $user->id }}</p>
                                </div>
                            </div>
                            <div class="mb-1 text-[.72rem] text-[#7a9db5]">
                                <span class="font-semibold text-[#3d5a6e]">Joined</span> {{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}
                            </div>
                        </div>
                        <div class="flex items-center gap-[2px] border-t border-[#e4eff8] bg-[#f8fbfe]">
                            <a href="{{ route('users.edit', $user->id) }}"
                                class="flex-1 px-3 py-2.5 text-center text-[.68rem] font-bold uppercase tracking-[.1em] text-[#0086da] transition hover:bg-[#e8f4fc]">
                                Edit
                            </a>
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="flex-1 border-l border-[#e4eff8]"
                                onsubmit="return confirm('Are you sure you want to delete this staff member?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full px-3 py-2.5 text-[.68rem] font-bold uppercase tracking-[.1em] text-red-500 transition hover:bg-red-50">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full border border-dashed border-[#d4e8f5] bg-[#f8fbfe] px-4 py-10 text-center text-[.82rem] text-[#7a9db5]">
                        No staff found.
                    </div>
                @endforelse
            </div>
            <div class="mt-5">{{ $staffs->links() }}</div>
        </div>
    </section>

    {{-- ── Patient Accounts ── --}}
    <section class="border border-[#e4eff8] bg-white shadow-[0_20px_48px_rgba(0,134,218,.07)]">
        <div class="flex items-center gap-3 border-b border-[#e4eff8] px-6 py-5 md:px-8">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center bg-[#0086da]">
                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="square" d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                </svg>
            </div>
            <div class="flex-1">
                <div class="text-[.95rem] font-extrabold tracking-[-0.01em] text-[#1a2e3b]">Patient Accounts</div>
            </div>
            <span class="border border-[#d4e8f5] bg-[#f0f8fe] px-3 py-1 text-[.65rem] font-bold uppercase tracking-[.14em] text-[#0086da]">
                {{ $normalUsers->total() ?? $normalUsers->count() }}
            </span>
        </div>

        <div class="p-6 md:p-8">
            <p class="mb-6 text-[.78rem] text-[#7a9db5]">Registered patient users created through sign-up and booking flows.</p>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @forelse($normalUsers as $user)
                    <article class="relative border border-[#e4eff8] bg-white transition hover:shadow-[0_8px_24px_rgba(0,134,218,.1)] hover:-translate-y-0.5">
                        <div class="p-5">
                            <div class="mb-4 flex items-center gap-3">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center bg-[#0086da]/15 text-base font-bold text-[#0086da]">
                                    {{ strtoupper(substr($user->username, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <h3 class="truncate text-[.88rem] font-extrabold text-[#1a2e3b]">{{ $user->username }}</h3>
                                    <p class="truncate text-[.72rem] text-[#7a9db5]">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="mb-1 text-[.72rem] text-[#7a9db5]">
                                <span class="font-semibold text-[#3d5a6e]">Joined</span> {{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}
                            </div>
                        </div>
                        <div class="flex items-center gap-[2px] border-t border-[#e4eff8] bg-[#f8fbfe]">
                            <a href="{{ route('users.edit', $user->id) }}"
                                class="flex-1 px-3 py-2.5 text-center text-[.68rem] font-bold uppercase tracking-[.1em] text-[#0086da] transition hover:bg-[#e8f4fc]">
                                Edit
                            </a>
                            @if ($user->id !== auth()->id())
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="flex-1 border-l border-[#e4eff8]"
                                    onsubmit="return confirm('Are you sure you want to delete this patient account?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full px-3 py-2.5 text-[.68rem] font-bold uppercase tracking-[.1em] text-red-500 transition hover:bg-red-50">
                                        Delete
                                    </button>
                                </form>
                            @else
                                <span class="flex-1 border-l border-[#e4eff8] px-3 py-2.5 text-center text-[.68rem] font-semibold text-[#7a9db5]">
                                    Current
                                </span>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="col-span-full border border-dashed border-[#d4e8f5] bg-[#f8fbfe] px-4 py-10 text-center text-[.82rem] text-[#7a9db5]">
                        No patient accounts found.
                    </div>
                @endforelse
            </div>
            <div class="mt-5">{{ $normalUsers->links() }}</div>
        </div>
    </section>

</div>
@endsection
