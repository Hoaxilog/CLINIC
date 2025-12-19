@extends('index')

@section('content')
<main id="mainContent" class="min-h-screen bg-[#F3F4F6] p-6 lg:p-8 ml-64 mt-14 transition-all duration-300 peer-[.collapsed]:ml-16 flex flex-col font-sans">
    
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
             class="fixed top-24 right-6 z-50 bg-white border border-gray-100 p-4 shadow-2xl rounded-xl flex items-center gap-3 animate-fade-in-down">
            <div class="h-8 w-8 rounded-full bg-green-50 flex items-center justify-center text-green-500 shadow-sm">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900">Success</p>
                <p class="text-xs text-gray-500">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="flex-grow w-full bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden flex flex-col">
        
        <div class="h-48 bg-slate-50 border-b border-gray-100 shrink-0 relative overflow-hidden">
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(#64748b 1px, transparent 1px); background-size: 24px 24px;"></div>
        </div>

        <div class="px-10 pb-10 flex-grow flex flex-col">
            
            <div class="relative flex flex-col md:flex-row justify-between items-end -mt-16 mb-12 shrink-0">
                <div class="flex items-end gap-6">
                    <div class="h-32 w-32 rounded-full bg-white p-1 shadow-lg ring-1 ring-gray-100 relative z-10">
                        <div class="h-full w-full rounded-full bg-gray-50 text-gray-300 flex items-center justify-center overflow-hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-full w-full object-cover" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <h1 class="text-3xl font-bold text-gray-900 tracking-tight drop-shadow-sm">{{ $user->username }}</h1>
                        <p class="text-gray-500 font-medium">{{ ucfirst($roleName ?? 'User') }}</p>
                    </div>
                </div>

                <div class="mb-3">
                     <button type="submit" form="profile-form" 
                        class="px-6 py-2.5 bg-[#0086DA] hover:bg-[#0073bd] text-white text-sm font-semibold rounded-lg shadow-md hover:shadow-lg transition-all focus:ring-2 focus:ring-offset-2 focus:ring-[#0086DA]">
                        Save Changes
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 h-full">
                
                <div class="lg:col-span-2 space-y-10">
                    
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                        <div class="h-8 w-1 bg-[#0086DA] rounded-full shadow-sm"></div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Personal Details</h3>
                            <p class="text-gray-500 text-sm">Update your official profile information.</p>
                        </div>
                    </div>

                    <form id="profile-form" action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide ml-1">Username</label>
                                <input type="text" name="username" value="{{ old('username', $user->username) }}"
                                    class="w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-lg shadow-sm focus:ring-2 focus:ring-[#0086DA]/10 focus:border-[#0086DA] block p-3 transition-all placeholder-gray-300" 
                                    placeholder="e.g. johndoe">
                                @error('username') <span class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide ml-1">Contact Number</label>
                                <input type="text" name="contact" value="{{ old('contact', $user->contact) }}"
                                    class="w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-lg shadow-sm focus:ring-2 focus:ring-[#0086DA]/10 focus:border-[#0086DA] block p-3 transition-all placeholder-gray-300" 
                                    placeholder="e.g. 0912 345 6789">
                                @error('contact') <span class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wide ml-1">Account Created</label>
                                <input type="text" value="{{ \Carbon\Carbon::parse($user->created_at)->format('F d, Y') }}" readonly
                                    class="w-full bg-gray-50 border border-gray-200 text-gray-500 text-sm rounded-lg shadow-sm block p-3 cursor-not-allowed">
                            </div>
                        </div>
                    </form>

                    <div class="pt-8">
                        <h4 class="text-sm font-bold text-gray-900 mb-4 ml-1">Contact Overview</h4>
                        
                        <div class="group flex items-center justify-between p-5 bg-white border border-gray-100 rounded-xl shadow-md hover:shadow-lg hover:border-blue-100 transition-all duration-300">
                            <div class="flex items-center gap-4">
                                <div class="h-12 w-12 rounded-full bg-blue-50 flex items-center justify-center text-[#0086DA] shadow-sm group-hover:scale-110 transition-transform duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-xs font-semibold uppercase tracking-wide">Primary Phone</p>
                                    <p class="text-gray-900 text-lg font-medium tracking-tight mt-0.5">{{ $user->contact }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50/50 rounded-2xl p-8 h-fit border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-300">
                    <div class="flex items-center gap-2 mb-6">
                        {{-- <div class="p-2 bg-white rounded-lg shadow-sm border border-gray-200 text-gray-700"> --}}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        {{-- </div> --}}
                        <h3 class="text-lg font-bold text-gray-900">Security</h3>
                    </div>

                    <form action="{{ route('profile.password') }}" method="POST" class="space-y-5">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5 ml-1">Current Password</label>
                            <input type="password" name="current_password" 
                                class="w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-lg shadow-sm focus:ring-2 focus:ring-gray-200 focus:border-gray-400 block p-2.5 transition-all">
                            @error('current_password') <span class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5 ml-1">New Password</label>
                            <input type="password" name="password" 
                                class="w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-lg shadow-sm focus:ring-2 focus:ring-gray-200 focus:border-gray-400 block p-2.5 transition-all">
                            @error('password') <span class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5 ml-1">Confirm Password</label>
                            <input type="password" name="password_confirmation" 
                                class="w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-lg shadow-sm focus:ring-2 focus:ring-gray-200 focus:border-gray-400 block p-2.5 transition-all">
                        </div>

                        <button type="submit" class="w-full mt-2 py-2.5 px-4 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg shadow-sm hover:bg-gray-50 hover:border-gray-400 hover:text-gray-900 transition-all duration-200">
                            Update Password
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</main>
@endsection