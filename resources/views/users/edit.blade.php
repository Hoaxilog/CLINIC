@extends('index') 

@section('content')
<main id="mainContent" class="min-h-screen bg-gray-50 p-6 lg:p-8 ml-64 mt-14 transition-all duration-300 peer-[.collapsed]:ml-16">
    
    <div class="max-w-3xl mx-auto">
        
        {{-- Form Card --}}
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden p-10 relative">
            
            {{-- HEADER --}}
            <div class="text-center mb-10">
                <h1 class="text-3xl font-extrabold text-gray-800 tracking-wide mb-2">
                    EDIT <span class="text-[#0086DA]">USER</span>
                </h1>
                <p class="text-sm text-gray-500 font-semibold">Update account details and permissions.</p>
            </div>
            
            @if(session('error'))
                <div class="mb-8 p-4 rounded-lg bg-red-50 border-l-4 border-red-500 text-red-700 text-sm flex items-center shadow-sm">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <span class="font-bold block">Error Encountered</span>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('users.update', $user->id) }}">
                @csrf
                @method('PUT')

                {{-- SECTION 1: ACCOUNT DETAILS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    {{-- Username --}}
                    <div>
                        <label class="block text-gray-700 font-bold mb-2 ml-1 text-sm uppercase tracking-wider">Username</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </span>
                            <input type="text" name="username" value="{{ old('username', $user->username) }}" placeholder="e.g. dr_smith" required
                                class="w-full pl-10 pr-4 py-3 rounded-lg border-2 border-gray-300 focus:outline-none focus:border-[#0086DA] transition duration-200 text-gray-800 font-medium placeholder-gray-400">
                        </div>
                        @error('username') <p class="text-red-500 text-xs mt-1 ml-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    {{-- Role --}}
                    <div>
                        <label class="block text-gray-700 font-bold mb-2 ml-1 text-sm uppercase tracking-wider">Role</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </span>
                            <select name="role" required class="w-full pl-10 pr-10 py-3 rounded-lg border-2 border-gray-300 focus:outline-none focus:border-[#0086DA] appearance-none bg-white transition duration-200 text-gray-800 font-medium">
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role', $user->role) == $role->id ? 'selected' : '' }}>
                                        {{ ucfirst($role->role_name) }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                            </div>
                        </div>
                        @error('role') <p class="text-red-500 text-xs mt-1 ml-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                </div>

                {{-- SECTION 2: PASSWORD CHANGE --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-gray-700 font-bold mb-2 ml-1 text-sm uppercase tracking-wider">New Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </span>
                            <input type="password" name="password" placeholder="Leave blank to keep current"
                                class="w-full pl-10 pr-4 py-3 rounded-lg border-2 border-gray-300 focus:outline-none focus:border-[#0086DA] transition duration-200 text-gray-800 font-medium placeholder-gray-400">
                        </div>
                        @error('password') <p class="text-red-500 text-xs mt-1 ml-1 font-bold">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-2 ml-1 text-sm uppercase tracking-wider">Confirm New Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </span>
                            <input type="password" name="password_confirmation" placeholder="Confirm new password"
                                class="w-full pl-10 pr-4 py-3 rounded-lg border-2 border-gray-300 focus:outline-none focus:border-[#0086DA] transition duration-200 text-gray-800 font-medium placeholder-gray-400">
                        </div>
                    </div>
                </div>

                {{-- FOOTER BUTTONS --}}
                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('users.index') }}" class="text-sm font-bold text-gray-500 hover:text-gray-700 transition duration-200">
                        Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 bg-[#0086DA] hover:bg-[#0073A8] text-white font-bold rounded-lg shadow-md transition duration-200 text-lg tracking-wide">
                        UPDATE ACCOUNT
                    </button>
                </div>

            </form>
        </div>
    </div>
</main>
@endsection
