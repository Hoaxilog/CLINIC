@extends('index')

@section('content')
<main id="mainContent" class="min-h-screen bg-gray-100 p-6 lg:p-8 ml-64 mt-14 transition-all duration-300 peer-[.collapsed]:ml-16 flex flex-col font-sans">
    
    @if (session('success'))
        <div class="fixed top-24 right-6 z-50 bg-green-100 border-l-4 border-green-500 p-4 shadow-lg rounded-r animate-fade-in-down">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-bold text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="flex-grow w-full bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden flex flex-col">
        
        <div class="h-48 bg-gradient-to-b from-blue-400 to-white shrink-0"></div>

        <div class="px-10 pb-10 flex-grow flex flex-col">
            
            <div class="relative flex flex-col md:flex-row justify-between items-end -mt-16 mb-10 shrink-0">
                <div class="flex items-end gap-6">
                    <div class="h-32 w-32 rounded-full bg-white p-1.5 shadow-lg">
                        <div class="h-full w-full rounded-full bg-gray-100 text-gray-400 flex items-center justify-center overflow-hidden border border-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-full w-full object-cover" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <h1 class="text-3xl font-bold text-gray-900">{{ $user->username }}</h1>
                        <p class="text-gray-500 text-lg font-medium">{{ $user->contact }}</p>
                    </div>
                </div>

                <div class="mb-3">
                     <button type="submit" form="profile-form" 
                        class="px-8 py-3 bg-[#0086DA] hover:bg-blue-700 text-white text-base font-bold rounded-xl shadow-md transition-all hover:shadow-lg border border-transparent">
                        Save Changes
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 h-full">
                
                <div class="lg:col-span-2 space-y-8">
                    <div class="border-b border-gray-200 pb-4 mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Personal Information</h3>
                        <p class="text-gray-500 text-sm mt-1">Manage your basic account details.</p>
                    </div>

                    <form id="profile-form" action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 uppercase tracking-wide">Username</label>
                                <input type="text" name="username" value="{{ old('username', $user->username) }}"
                                    class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-base rounded-lg focus:ring-2 focus:ring-[#0086DA] focus:border-[#0086DA] block p-3 transition-all placeholder-gray-400" 
                                    placeholder="Enter username">
                                @error('username') <span class="text-red-600 text-sm font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 uppercase tracking-wide">Contact Number</label>
                                <input type="text" name="contact" value="{{ old('contact', $user->contact) }}"
                                    class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-base rounded-lg focus:ring-2 focus:ring-[#0086DA] focus:border-[#0086DA] block p-3 transition-all placeholder-gray-400" 
                                    placeholder="Enter contact number">
                                @error('contact') <span class="text-red-600 text-sm font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-500 uppercase tracking-wide">Account Role</label>
                                <input type="text" value="{{ ucfirst($roleName ?? 'User') }}" readonly
                                    class="w-full bg-gray-200 border border-gray-300 text-gray-600 text-base rounded-lg block p-3 cursor-not-allowed font-medium">
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-500 uppercase tracking-wide">Member Since</label>
                                <input type="text" value="{{ \Carbon\Carbon::parse($user->created_at)->format('F d, Y') }}" readonly
                                    class="w-full bg-gray-200 border border-gray-300 text-gray-600 text-base rounded-lg block p-3 cursor-not-allowed font-medium">
                            </div>
                        </div>
                    </form>

                    <div class="pt-8 mt-4 border-t border-gray-100">
                        <h4 class="text-lg font-bold text-gray-900 mb-4">Contact Overview</h4>
                        <div class="flex items-center gap-4 p-4 bg-blue-50 rounded-xl border border-blue-100 w-fit pr-10">
                            <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center text-[#0086DA] shadow-sm border border-blue-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-900 font-bold text-sm">Primary Contact</p>
                                <p class="text-gray-600 text-sm">{{ $user->contact }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-xl p-8 h-fit border border-gray-200 shadow-sm">
                    <h3 class="text-xl font-bold text-gray-900 mb-1">Security Settings</h3>
                    <p class="text-gray-500 text-sm mb-6">Update your password to keep your account safe.</p>

                    <form action="{{ route('profile.password') }}" method="POST" class="space-y-5">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Current Password</label>
                            <input type="password" name="current_password" 
                                class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-gray-800 p-3 shadow-sm transition-all">
                            @error('current_password') <span class="text-red-600 text-xs font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-2">New Password</label>
                            <input type="password" name="password" 
                                class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-gray-800 p-3 shadow-sm transition-all">
                            @error('password') <span class="text-red-600 text-xs font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Confirm Password</label>
                            <input type="password" name="password_confirmation" 
                                class="w-full bg-white border border-gray-300 text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-gray-800 p-3 shadow-sm transition-all">
                        </div>

                        <button type="submit" class="w-full mt-2 bg-gray-900 hover:bg-black text-white font-bold py-3 px-4 rounded-xl shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5 border border-transparent">
                            Update Password
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</main>
@endsection