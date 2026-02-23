@extends('index')

@section('style')
    :root {
    --profile-ink: #0f172a;
    --profile-muted: #64748b;
    --profile-border: #e2e8f0;
    --profile-bg: #f6f8fb;
    --profile-accent: #0b84d8;
    }
    .profile-shell {
    background:
    radial-gradient(900px 500px at 10% -10%, rgba(11, 132, 216, 0.10), transparent 60%),
    radial-gradient(700px 500px at 90% 0%, rgba(14, 116, 144, 0.10), transparent 60%),
    var(--profile-bg);
    }
    .profile-card {
    background: #fff;
    border: 1px solid var(--profile-border);
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
    }
@endsection

@section('content')
    <main id="mainContent" class="min-h-screen profile-shell ml-64 mt-14 transition-all duration-300 peer-[.collapsed]:ml-16">
        <div class="max-w-6xl mx-auto px-6 lg:px-10 py-8">
            <div class="profile-card rounded-2xl p-6 lg:p-8 mb-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div class="flex items-center gap-5">
                        <div
                            class="h-16 w-16 rounded-2xl bg-white ring-1 ring-slate-200 flex items-center justify-center text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h1 class="text-2xl lg:text-3xl font-extrabold text-[color:var(--profile-ink)]">
                                    {{ $user->username }}</h1>
                                <span
                                    class="text-[11px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-blue-50 text-[color:var(--profile-accent)] border border-blue-100">
                                    {{ ucfirst($roleName ?? 'Administrator') }}
                                </span>
                            </div>
                            <p class="text-sm text-[color:var(--profile-muted)] mt-1">
                                Member since {{ \Carbon\Carbon::parse($user->created_at)->format('M Y') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <div class="px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-600">
                            Created {{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}
                        </div>
                        <div class="px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-600">
                            Updated {{ \Carbon\Carbon::parse($user->updated_at)->format('M d, Y') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="profile-card rounded-2xl p-6 lg:p-8">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-lg font-bold text-[color:var(--profile-ink)]">Profile Information</h2>
                                <p class="text-sm text-[color:var(--profile-muted)]">Update your basic details.</p>
                            </div>
                        </div>

                        <form id="profile-form" action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div class="space-y-1">
                                    <label
                                        class="text-xs font-bold text-slate-500 uppercase tracking-wider">Username</label>
                                    <input type="text" name="username" value="{{ old('username', $user->username) }}"
                                        class="w-full bg-white border border-slate-200 text-slate-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5 transition-all"
                                        placeholder="Jane Doe">
                                    @error('username')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- <div class="space-y-1">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Contact Number</label>
                                <input type="text" name="contact" value="{{ old('contact', $user->contact) }}"
                                    class="w-full bg-white border border-slate-200 text-slate-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5 transition-all"
                                    placeholder="e.g. +63 912 345 6789">
                                @error('contact') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div> --}}

                                <div class="space-y-1 md:col-span-2">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Position /
                                        Title</label>
                                    <input type="text" name="position"
                                        value="{{ old('position', $user->position ?? '') }}"
                                        class="w-full bg-white border border-slate-200 text-slate-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5 transition-all"
                                        placeholder="e.g. Dentist, Staff Nurse, Patient">
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[color:var(--profile-accent)] hover:bg-[#0a6fb4] text-white text-sm font-semibold transition">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="profile-card rounded-2xl p-6 lg:p-8">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-lg font-bold text-[color:var(--profile-ink)]">Security</h2>
                                <p class="text-sm text-[color:var(--profile-muted)]">Change your password.</p>
                            </div>
                        </div>

                        <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div class="space-y-1 md:col-span-2">
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Current
                                        Password</label>
                                    <input type="password" name="current_password" placeholder="Enter current password"
                                        class="w-full bg-white border border-slate-200 text-slate-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5">
                                    @error('current_password')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="space-y-1">
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">New
                                        Password</label>
                                    <input type="password" name="password" placeholder="Enter new password"
                                        class="w-full bg-white border border-slate-200 text-slate-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5">
                                    @error('password')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="space-y-1">
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Confirm
                                        Password</label>
                                    <input type="password" name="password_confirmation" placeholder="Confirm new password"
                                        class="w-full bg-white border border-slate-200 text-slate-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5">
                                </div>
                            </div>

                            <div class="flex justify-end pt-2">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold transition">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="profile-card rounded-2xl p-6 lg:p-8 sticky top-20">
                        <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider mb-4">Account Overview</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 rounded-xl border border-slate-200">
                                <span class="text-xs text-slate-500">Role</span>
                                <span
                                    class="text-sm font-semibold text-slate-900">{{ ucfirst($roleName ?? 'Administrator') }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 rounded-xl border border-slate-200">
                                <span class="text-xs text-slate-500">Username</span>
                                <span class="text-sm font-semibold text-slate-900">{{ $user->username }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 rounded-xl border border-slate-200">
                                <span class="text-xs text-slate-500">Contact</span>
                                <span class="text-sm font-semibold text-slate-900">{{ data_get($user, 'contact') ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 rounded-xl border border-slate-200">
                                <span class="text-xs text-slate-500">Position</span>
                                <span class="text-sm font-semibold text-slate-900">{{ $user->position ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
