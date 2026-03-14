@extends('index') 

@section('content')
<script>
    function toggleAdminCreatePassword(fieldId, eyeOpenId, eyeClosedId) {
        const passwordField = document.getElementById(fieldId);
        const eyeOpen = document.getElementById(eyeOpenId);
        const eyeClosed = document.getElementById(eyeClosedId);

        const isHidden = passwordField.type === 'password';
        passwordField.type = isHidden ? 'text' : 'password';

        eyeClosed.classList.toggle('hidden', isHidden);
        eyeOpen.classList.toggle('hidden', !isHidden);
    }
</script>
<main id="mainContent" class="min-h-screen bg-gray-100 p-6 lg:p-8 ml-64 mt-14 transition-all duration-300 peer-[.collapsed]:ml-16">
    
    <div class="max-w-3xl mx-auto">
        
        {{-- Card Container (Matches Login: rounded-none, shadow-xl, border) --}}
        <div class="bg-white rounded-none shadow-xl border border-gray-100 overflow-hidden p-10 relative">
            
            {{-- HEADER (Typography matches Login Page: ExtraBold & Tracking Wide) --}}
            <div class="text-center mb-10">
                <h1 class="text-3xl font-extrabold text-gray-800 tracking-wide mb-2">
                    CREATE <span class="text-[#4F46E5]">USER</span>
                </h1>
                <p class="text-sm text-gray-500 font-semibold">Add a new administrator or staff member.</p>
            </div>
            
            @if(session('error'))
                <div class="mb-8 p-4 rounded-none bg-red-50 border-l-4 border-red-500 text-red-700 text-sm flex items-center shadow-sm">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <span class="font-bold block">Error Encountered</span>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('users.store') }}">
                @csrf

                {{-- SECTION 1: ACCOUNT DETAILS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    {{-- Email --}}
                    <div>
                        <label class="block text-gray-700 font-bold mb-2 ml-1 text-sm uppercase tracking-wider">Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                {{-- User Icon --}}
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </span>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="e.g. staff@tejadent.com" required
                                class="w-full pl-10 pr-4 py-3 rounded-none border-2 border-gray-300 focus:outline-none focus:border-[#4F46E5] transition duration-200 text-gray-800 font-medium placeholder-gray-400">
                        </div>
                        @error('email') <p class="text-red-500 text-xs mt-1 ml-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    {{-- Role --}}
                    <div>
                        <label class="block text-gray-700 font-bold mb-2 ml-1 text-sm uppercase tracking-wider">Role</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                {{-- Badge Icon --}}
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </span>
                            <select name="role" required class="w-full pl-10 pr-10 py-3 rounded-none border-2 border-gray-300 focus:outline-none focus:border-[#4F46E5] appearance-none bg-white transition duration-200 text-gray-800 font-medium">
                                <option value="" selected disabled>Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role') == $role->id ? 'selected' : '' }}>{{ ucfirst($role->role_name) }}</option>
                                @endforeach
                            </select>
                            {{-- Dropdown Arrow --}}
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                            </div>
                        </div>
                        @error('role') <p class="text-red-500 text-xs mt-1 ml-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                </div>

                {{-- SECTION 2: PASSWORD --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-gray-700 font-bold mb-2 ml-1 text-sm uppercase tracking-wider">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </span>
                            <input type="password" id="admin-create-password" name="password" placeholder="Min. 8 characters" required
                                class="w-full pl-10 pr-4 py-3 rounded-none border-2 border-gray-300 focus:outline-none focus:border-[#4F46E5] transition duration-200 text-gray-800 font-medium placeholder-gray-400">
                            <button type="button"
                                onclick="toggleAdminCreatePassword('admin-create-password','admin-create-eye-open','admin-create-eye-closed')"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 transition"
                                aria-label="Toggle password visibility">
                                <svg id="admin-create-eye-closed" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg id="admin-create-eye-open" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.58 10.58a2 2 0 102.83 2.83"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.88 5.09A10.94 10.94 0 0112 5c5 0 9.27 3.11 11 7a12.62 12.62 0 01-3.04 4.19M6.1 6.1A12.84 12.84 0 001 12c1.73 3.89 6 7 11 7 1.65 0 3.23-.34 4.66-.95"></path>
                                </svg>
                            </button>
                        </div>
                        @error('password') <p class="text-red-500 text-xs mt-1 ml-1 font-bold">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-2 ml-1 text-sm uppercase tracking-wider">Confirm Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </span>
                            <input type="password" id="admin-create-password-confirmation" name="password_confirmation" placeholder="Re-enter password" required
                                class="w-full pl-10 pr-4 py-3 rounded-none border-2 border-gray-300 focus:outline-none focus:border-[#4F46E5] transition duration-200 text-gray-800 font-medium placeholder-gray-400">
                            <button type="button"
                                onclick="toggleAdminCreatePassword('admin-create-password-confirmation','admin-create-confirm-eye-open','admin-create-confirm-eye-closed')"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 transition"
                                aria-label="Toggle confirm password visibility">
                                <svg id="admin-create-confirm-eye-closed" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg id="admin-create-confirm-eye-open" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.58 10.58a2 2 0 102.83 2.83"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.88 5.09A10.94 10.94 0 0112 5c5 0 9.27 3.11 11 7a12.62 12.62 0 01-3.04 4.19M6.1 6.1A12.84 12.84 0 001 12c1.73 3.89 6 7 11 7 1.65 0 3.23-.34 4.66-.95"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- FOOTER BUTTONS --}}
                <div class="flex items-center justify-end gap-5 pt-2">
                    <a href="{{ route('users.index') }}" class="text-sm font-bold text-gray-500 hover:text-gray-700 transition duration-200">
                        Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 bg-[#4F46E5] hover:bg-[#4338CA] text-white font-bold rounded-none shadow-md transition duration-200 text-lg tracking-wide">
                        CREATE ACCOUNT
                    </button>
                </div>

            </form>
        </div>
    </div>
</main>
@endsection

