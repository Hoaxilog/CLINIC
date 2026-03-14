@extends('index')

@section('content')
    <main id="mainContent"
        class="min-h-screen bg-[#f3f4f6] p-6 lg:p-8 ml-64 mt-14 transition-all duration-300 peer-[.collapsed]:ml-16">
        <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900">User Accounts</h1>
                <p class="mt-1 text-sm text-gray-500">Manage admin and staff profiles, permissions, and lifecycle actions.</p>
            </div>
            <a href="{{ route('users.create') }}"
                class="inline-flex items-center gap-2 rounded-none bg-[#4F46E5] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#4338CA]">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M5 12h14" />
                    <path d="M12 5v14" />
                </svg>
                <span>Add New User</span>
            </a>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-none border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 shadow-sm" role="alert">
                <p class="font-semibold">Success</p>
                <p class="mt-0.5">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-none border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-700 shadow-sm" role="alert">
                <p class="font-semibold">Error</p>
                <p class="mt-0.5">{{ session('error') }}</p>
            </div>
        @endif

        <section class="rounded-none border border-gray-100 bg-white p-6 shadow-sm">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Admins</h2>
                    <p class="mt-0.5 text-xs text-gray-500">Administrative users with full system control.</p>
                </div>
                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                    {{ $admins->total() ?? $admins->count() }}
                </span>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
                @forelse($admins as $user)
                    <article class="relative rounded-none border border-gray-100 bg-white p-5 shadow-sm">
                        @if ($user->role_name)
                            <span class="absolute right-4 top-4 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">
                                {{ $user->role_name }}
                            </span>
                        @endif

                        <div class="mb-5 flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-500 text-base font-bold text-white">
                                {{ strtoupper(substr($user->username, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <h3 class="truncate text-base font-bold text-gray-900">{{ $user->username }}</h3>
                                <p class="truncate text-xs text-gray-500">ID: #{{ $user->id }}</p>
                            </div>
                        </div>

                        <div class="mb-5 text-sm text-gray-600">
                            Joined {{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}
                        </div>

                        <div class="flex items-center gap-2 border-t border-gray-100 pt-4">
                            <a href="{{ route('users.edit', $user->id) }}"
                                class="flex-1 rounded-none border border-amber-100 bg-amber-50 px-3 py-2 text-center text-sm font-semibold text-amber-700 transition hover:bg-amber-100">
                                Edit
                            </a>
                            @if ($user->id !== auth()->id())
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="flex-1"
                                    onsubmit="return confirm('Are you sure you want to delete this admin?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full rounded-none border border-rose-100 bg-rose-50 px-3 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-100">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-none border border-dashed border-gray-200 bg-gray-50 px-4 py-10 text-center text-sm text-gray-500">
                        No admins found.
                    </div>
                @endforelse
            </div>

            <div class="mt-5">{{ $admins->links() }}</div>
        </section>

        <section class="mt-6 rounded-none border border-gray-100 bg-white p-6 shadow-sm">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Staff</h2>
                    <p class="mt-0.5 text-xs text-gray-500">Operational users supporting daily clinic workflow.</p>
                </div>
                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                    {{ $staffs->total() ?? $staffs->count() }}
                </span>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
                @forelse($staffs as $user)
                    <article class="relative rounded-none border border-gray-100 bg-white p-5 shadow-sm">
                        @if ($user->role_name)
                            <span class="absolute right-4 top-4 rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-semibold text-blue-700">
                                {{ $user->role_name }}
                            </span>
                        @endif

                        <div class="mb-5 flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-500 text-base font-bold text-white">
                                {{ strtoupper(substr($user->username, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <h3 class="truncate text-base font-bold text-gray-900">{{ $user->username }}</h3>
                                <p class="truncate text-xs text-gray-500">ID: #{{ $user->id }}</p>
                            </div>
                        </div>

                        <div class="mb-5 text-sm text-gray-600">
                            Joined {{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}
                        </div>

                        <div class="flex items-center gap-2 border-t border-gray-100 pt-4">
                            <a href="{{ route('users.edit', $user->id) }}"
                                class="flex-1 rounded-none border border-amber-100 bg-amber-50 px-3 py-2 text-center text-sm font-semibold text-amber-700 transition hover:bg-amber-100">
                                Edit
                            </a>
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="flex-1"
                                onsubmit="return confirm('Are you sure you want to delete this staff member?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full rounded-none border border-rose-100 bg-rose-50 px-3 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-100">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-none border border-dashed border-gray-200 bg-gray-50 px-4 py-10 text-center text-sm text-gray-500">
                        No staff found.
                    </div>
                @endforelse
            </div>

            <div class="mt-5">{{ $staffs->links() }}</div>
        </section>

        <section class="mt-6 rounded-none border border-gray-100 bg-white p-6 shadow-sm">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Patient Accounts</h2>
                    <p class="mt-0.5 text-xs text-gray-500">Registered patient users created through sign-up and booking flows.</p>
                </div>
                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                    {{ $normalUsers->total() ?? $normalUsers->count() }}
                </span>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
                @forelse($normalUsers as $user)
                    <article class="relative rounded-none border border-gray-100 bg-white p-5 shadow-sm">
                        <span class="absolute right-4 top-4 rounded-full bg-sky-50 px-2.5 py-1 text-[11px] font-semibold text-sky-700">
                            {{ $user->role_name ?? 'patient' }}
                        </span>

                        <div class="mb-5 flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-sky-500 text-base font-bold text-white">
                                {{ strtoupper(substr($user->username, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <h3 class="truncate text-base font-bold text-gray-900">{{ $user->username }}</h3>
                                <p class="truncate text-xs text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>

                        <div class="mb-5 text-sm text-gray-600">
                            Joined {{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}
                        </div>

                        <div class="flex items-center gap-2 border-t border-gray-100 pt-4">
                            @if ($user->id !== auth()->id())
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="w-full"
                                    onsubmit="return confirm('Are you sure you want to delete this patient account?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full rounded-none border border-rose-100 bg-rose-50 px-3 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-100">
                                        Delete
                                    </button>
                                </form>
                            @else
                                <p class="w-full rounded-none border border-gray-200 bg-gray-50 px-3 py-2 text-center text-sm font-medium text-gray-500">
                                    Current account
                                </p>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-none border border-dashed border-gray-200 bg-gray-50 px-4 py-10 text-center text-sm text-gray-500">
                        No patient accounts found.
                    </div>
                @endforelse
            </div>

            <div class="mt-5">{{ $normalUsers->links() }}</div>
        </section>
    </main>
@endsection
