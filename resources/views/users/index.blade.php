@extends('index') 

@section('content')
<main id="mainContent" class="min-h-screen bg-gray-100 p-6 lg:p-8 ml-64 mt-14 transition-all duration-300 peer-[.collapsed]:ml-16">

    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">User Accounts</h1>
        <a href="{{ route('users.create') }}" class="bg-[#0086DA] hover:scale-105 text-white px-4 py-2 rounded-lg flex items-center gap-2 shadow-sm  transition-transform duration-200 ">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
            Add New User
        </a>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r shadow-sm" role="alert">
            <p class="font-bold">Success</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r shadow-sm" role="alert">
            <p class="font-bold">Error</p>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- SECTION: Admins --}}
    <section class="mb-10">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">
                Admins 
                <span class="px-2 py-0.5 text-sm bg-gray-200 text-gray-700 rounded-full">{{ $admins->total() ?? $admins->count() }}</span>
            </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($admins as $user)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200 border border-gray-100 relative overflow-hidden">
                    {{-- Role Badge --}}
                    @if($user->role_name)
                        <span class="absolute top-4 right-4 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide rounded-full bg-green-100 text-[#10b981]">
                            {{ $user->role_name }}
                        </span>
                    @endif
                    
                    <div class="p-6">
                        <div class="flex items-center gap-4 mb-5">
                            <div class="flex-shrink-0 w-14 h-14 rounded-full flex items-center justify-center text-white text-xl font-bold bg-green-500 shadow-sm">
                                {{ strtoupper(substr($user->username, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-lg font-bold text-gray-900 truncate">{{ $user->username }}</h3>
                                <p class="text-xs text-gray-500 truncate">ID: #{{ $user->id }}</p>
                            </div>
                        </div>

                        <div class="space-y-3 mb-6">
                            @if(isset($user->contact))
                            <div class="flex items-center gap-3 text-sm text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="flex-shrink-0 text-gray-400">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                </svg>
                                <span class="truncate">{{ $user->contact }}</span>
                            </div>
                            @endif

                            <div class="flex items-center gap-3 text-sm text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="flex-shrink-0 text-gray-400">
                                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/>
                                </svg>
                                <span>Joined {{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}</span>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-4 flex items-center gap-2">
                            <a href="{{ route('users.edit', $user->id) }}" 
                               class="flex-1 py-2 text-sm font-medium text-center text-yellow-700 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition-colors">
                                Edit
                            </a>
                            @if($user->id !== auth()->id())
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure you want to delete this admin?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full py-2 text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-lg border border-dashed border-gray-300 p-8 text-center">
                    <p class="text-gray-500">No admins found.</p>
                </div>
            @endforelse
        </div>
        <div class="mt-4">
            {{ $admins->links() }}
        </div>
    </section>

    {{-- SECTION: Staff --}}
    <section class="mb-10">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">
                Staff
                <span class="px-2 py-0.5 text-sm bg-gray-200 text-gray-700 rounded-full">{{ $staffs->total() ?? $staffs->count() }}</span>
            </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($staffs as $user)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200 border border-gray-100 relative overflow-hidden">
                    @if($user->role_name)
                        <span class="absolute top-4 right-4 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide rounded-full bg-blue-100 text-blue-700">
                            {{ $user->role_name }}
                        </span>
                    @endif
                    
                    <div class="p-6">
                        <div class="flex items-center gap-4 mb-5">
                            <div class="flex-shrink-0 w-14 h-14 rounded-full flex items-center justify-center text-white text-xl font-bold bg-blue-500 shadow-sm">
                                {{ strtoupper(substr($user->username, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-lg font-bold text-gray-900 truncate">{{ $user->username }}</h3>
                                <p class="text-xs text-gray-500 truncate">ID: {{ $user->id }}</p>
                            </div>
                        </div>
                        
                        {{-- Contact & Date Info (Same as Admin) --}}
                        <div class="space-y-3 mb-6">
                            @if(isset($user->contact))
                            <div class="flex items-center gap-3 text-sm text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="flex-shrink-0 text-gray-400">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                </svg>
                                <span class="truncate">{{ $user->contact }}</span>
                            </div>
                            @endif
                            <div class="flex items-center gap-3 text-sm text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="flex-shrink-0 text-gray-400">
                                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/>
                                </svg>
                                <span>Joined {{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}</span>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-4 flex items-center gap-2">
                            <a href="{{ route('users.edit', $user->id) }}" class="flex-1 py-2 text-sm font-medium text-center text-yellow-700 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition-colors">Edit</a>
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure you want to delete this staff member?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-full py-2 text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-lg border border-dashed border-gray-300 p-8 text-center">
                    <p class="text-gray-500">No staff found.</p>
                </div>
            @endforelse
        </div>
        <div class="mt-4">{{ $staffs->links() }}</div>
    </section>

</main>
@endsection