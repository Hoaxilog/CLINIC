@extends('index')

@section('content')
<main id="mainContent" class="min-h-screen bg-[#F9FAFB] ml-64 mt-14 transition-all duration-300 peer-[.collapsed]:ml-16 flex flex-col font-sans">
    
    {{-- Modern Gradient Header Banner --}}
    <div class="h-48 bg-gradient-to-r from-[#007AB8] to-[#0099EE] w-full relative">
        {{-- Subtle Pattern Overlay --}}
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 24px 24px;"></div>
    </div>

    {{-- Floating Profile Header Card --}}
    <div class="px-6 lg:px-8 pb-10 -mt-20 relative z-10">
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6 mb-8">
            <div class="flex flex-col md:flex-row items-center md:items-end gap-6">
                
                {{-- Avatar Section --}}
                <div class="relative -mt-16 md:mt-0">
                    <div class="h-32 w-32 rounded-2xl bg-white p-1 shadow-xl ring-1 ring-gray-100">
                        <div class="h-full w-full rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 overflow-hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="absolute bottom-2 right-2 h-6 w-6 bg-emerald-500 rounded-full border-4 border-white shadow-sm flex items-center justify-center">
                         <div class="h-1.5 w-1.5 bg-white rounded-full animate-pulse"></div>
                    </div>
                </div>

                {{-- Profile Info Section --}}
                <div class="flex-1 text-center md:text-left pb-1">
                    <div class="flex flex-col md:flex-row md:items-center gap-3 mb-2">
                        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $user->username }}</h1>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-[#007AB8] border border-blue-100 uppercase tracking-wider self-center md:self-auto">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ ucfirst($roleName ?? 'Administrator') }}
                        </span>
                    </div>
                    <div class="flex flex-wrap justify-center md:justify-start items-center gap-y-2 gap-x-4 text-sm text-gray-500">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Member since {{ \Carbon\Carbon::parse($user->created_at)->format('M Y') }}
                        </span>
                        <span class="hidden md:block text-gray-300">|</span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Verified Account
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Grid Layout (Original Content Maintained) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            {{-- LEFT COLUMN: Personal Information + Security Settings --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- 1. Personal Information Card --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-3 mb-6 border-b border-gray-50 pb-4">
                        <div class="h-10 w-10 rounded-lg bg-blue-50 flex items-center justify-center text-[#0099EE]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Personal Information</h3>
                            <p class="text-xs text-gray-500">Update your profile details</p>
                        </div>
                    </div>

                    <form id="profile-form" action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">USERNAME</label>
                                <input type="text" name="username" value="{{ old('username', $user->username) }}"
                                    class="w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-md focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5 transition-all" 
                                    placeholder="Joel Cerineo">
                                @error('username') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Contact Number</label>
                                <input type="text" name="contact" value="{{ old('contact', $user->contact) }}"
                                    class="w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-md focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5 transition-all" 
                                    placeholder="e.g. +1 234 567 8900">
                                @error('contact') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1 md:col-span-2">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Position / Title</label>
                                <input type="text" name="position" value="{{ old('position', $user->position ?? '') }}"
                                    class="w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-md focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 block p-2.5 transition-all" 
                                    placeholder="e.g. Senior Physician, Nurse Practitioner">
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <button type="submit" class="flex items-center gap-2 px-6 py-2 bg-[#007AB8] hover:bg-[#006da3] text-white text-sm font-bold rounded-lg shadow-sm transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>

                {{-- 2. Security Settings Card --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-3 mb-6 border-b border-gray-50 pb-4">
                        <div class="h-10 w-10 rounded-lg bg-orange-50 flex items-center justify-center text-orange-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Security Settings</h3>
                            <p class="text-xs text-gray-500">Manage your password</p>
                        </div>
                    </div>

                    <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1 md:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Current Password</label>
                                <input type="password" name="current_password" placeholder="Enter current password"
                                    class="w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-md focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 block p-2.5">
                                @error('current_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">New Password</label>
                                <input type="password" name="password" placeholder="Enter new password"
                                    class="w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-md focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 block p-2.5">
                                @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Confirm New Password</label>
                                <input type="password" name="password_confirmation" placeholder="Confirm new password"
                                    class="w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-md focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 block p-2.5">
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit" class="px-6 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors flex items-center gap-2">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- RIGHT COLUMN: Account Information --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-20">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="h-10 w-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Account Information</h3>
                            <p class="text-xs text-gray-500">Your account details</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center gap-4 p-3 bg-white border border-gray-100 rounded-lg">
                            <div class="h-10 w-10 rounded bg-emerald-50 flex items-center justify-center text-emerald-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Account Role</p>
                                <p class="text-sm font-medium text-gray-900">{{ ucfirst($roleName ?? 'Administrator') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 p-3 bg-white border border-gray-100 rounded-lg">
                            <div class="h-10 w-10 rounded bg-purple-50 flex items-center justify-center text-purple-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Account Created</p>
                                <p class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($user->created_at)->format('F d, Y') }}</p>
                            </div>
                        </div>

                         <div class="flex items-center gap-4 p-3 bg-white border border-gray-100 rounded-lg">
                            <div class="h-10 w-10 rounded bg-orange-50 flex items-center justify-center text-orange-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Last Updated</p>
                                <p class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($user->updated_at)->format('F d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>
@endsection