@extends('index')

@section('content')
    <main id="mainContent" class="min-h-screen bg-gray-100 p-6 lg:p-8 ml-64 mt-14 transition-all duration-300 peer-[.collapsed]:ml-16">
        <h1 class="text-3xl lg:text-4xl font-bold text-gray-800">Dashboard</h1>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <section class="lg:col-span-1 bg-white p-6 rounded-lg shadow-md space-y-4 h-full">
            <div class="flex items-center justify-between">
                <p class="font-medium text-gray-700">Today's Appointment</p>
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0086da" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
            </div>
            <div>
                <h1 class="text-5xl font-semibold text-gray-900">{{$todayAppointmentsCount ?? 0}}</h1>
            </div>
            <p class="text-gray-600">
                {{ $todayCompletedCount ?? 0 }} completed,
                {{ $todayCancelledCount ?? 0 }} cancelled,
                {{ $todayUpcomingCount ?? 0 }} upcoming
            </p>
            </section>

            <section class="lg:col-span-1 bg-white p-6 rounded-lg shadow-md space-y-4 h-full">
            <div class="flex items-center justify-between">
                <p class="font-medium text-gray-700">Treatments Completed</p>
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0086da" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clipboard-check"><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="m9 14 2 2 4-4"/></svg>
            </div>
            <div>
                <h1 class="text-5xl font-semibold text-gray-900">{{ $weeklyCompletedCount ?? 0 }}</h1>
            </div>
            <p class="text-gray-600">+5 from last week</p>
            </section>

            <section class="lg:col-span-1 bg-[#0086da] text-white rounded-lg shadow-md p-6 flex flex-col justify-center items-center text-center h-full">
            <div class="text-2xl font-medium">Calendar</div>
            <div id="realtime-time" class="text-6xl lg:text-7xl font-extrabold my-2">
                Loading...
            </div>
            <div id="realtime-date" class="text-xl lg:text-2xl font-medium">
                </div>
            </section>

            <section class="lg:col-span-2 bg-white rounded-lg shadow-md flex flex-col">
                @livewire('today-schedule')
            </section>

            <section class="lg:col-span-1 relative flex flex-col bg-gray-100 rounded-lg shadow-md">
                @livewire('notes')
            </section>
            @php
                    $user = auth()->user();
                    $role = is_array($user) ? ($user['role'] ?? null) : ($user->role ?? null);
                    $isAdmin = $role === 1
                @endphp
            @auth
            @if($isAdmin)
                <section class="lg:col-span-3 bg-white rounded-lg shadow-md p-4">
                <h1 class="text-2xl font-semibold text-gray-800 mb-4">Quick Actions</h1>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">

                    {{-- <button class="bg-[#ccebff] text-gray-800 font-semibold p-4 rounded-lg flex flex-col items-center gap-2 hover:bg-blue-200 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0086da" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-plus"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
                        <span>Add Patient</span>
                    </button>

                    <button class="bg-[#ccebff] text-gray-800 font-semibold p-4 rounded-lg flex flex-col items-center gap-2 hover:bg-blue-200 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0086da" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clipboard-clock"><path d="M16 14v2.2l1.6 1"/><path d="M16 4h2a2 2 0 0 1 2 2v.832"/><path d="M8 4H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h2"/><circle cx="16" cy="16" r="6"/><rect x="8" y="2" width="8" height="4" rx="1"/></svg>
                        <span>Patient History</span>
                    </button>

                    <button class="bg-[#ccebff] text-gray-800 font-semibold p-4 rounded-lg flex flex-col items-center gap-2 hover:bg-blue-200 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0086da" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clipboard-plus"><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M9 14h6"/><path d="M12 17v-6"/></svg>
                        <span>Book Appointment</span>
                    </button> --}}

                    <a href="{{ route('admin.db.backup') }}" class="bg-[#ccebff] text-gray-800 font-semibold p-4 rounded-lg flex flex-col items-center gap-2 hover:bg-blue-200 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0086da" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-database-backup"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 12a9 3 0 0 0 5 2.69"/><path d="M21 9.3V5"/><path d="M3 5v14a9 3 0 0 0 6.47 2.88"/><path d="M12 12v4h4"/><path d="M13 20a5 5 0 0 0 9-3 4.5 4.5 0 0 0-4.5-4.5c-1.33 0-2.54.54-3.41 1.41L12 16"/></svg>
                        <span>Back up Data</span>
                    </a>
                </div>
                </section>
            @endif  
            @endauth
        </div> 
    </main>
@endsection

@push('script') 
    @vite('resources/js/time.js')
@endpush

