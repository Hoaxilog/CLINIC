@extends('index') 

@section('content')
<main id="mainContent" class="min-h-screen bg-gray-100 p-6 lg:p-8 ml-64 mt-14 transition-all duration-300 peer-[.collapsed]:ml-16">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-8">Create New Account</h2>
        
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('users.store') }}">
            @csrf

            <div class="mb-6">
                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                    Username
                </label>
                <input type="text" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('username') border-red-500 @enderror" 
                       id="username" 
                       name="username" 
                       value="{{ old('username') }}" 
                       placeholder="Enter username"
                       required>
                @error('username')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="contact" class="block text-sm font-medium text-gray-700 mb-2">
                    Contact Number
                </label>
                <input type="tel" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('contact') border-red-500 @enderror" 
                       id="contact" 
                       name="contact" 
                       value="{{ old('contact') }}" 
                       placeholder="Enter 11-digit number (e.g., 0917...)"
                       maxlength="11"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                >
                @error('contact')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-6">
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                    Role
                </label>
                <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none @error('role') border-red-500 @enderror" 
                        id="role" 
                        name="role" 
                        required>
                    <option value="">Select Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role') == $role->id ? 'selected' : '' }}>
                            {{ ucfirst($role->role_name) }}
                        </option>
                    @endforeach
                </select>
                @error('role')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Password
                </label>
                <input type="password" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror" 
                       id="password" 
                       name="password"
                       placeholder="Enter password"
                       required>
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-8">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    Confirm Password
                </label>
                <input type="password" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       id="password_confirmation" 
                       name="password_confirmation"
                       placeholder="Confirm password"
                       required>
            </div>

            <div class="flex justify-between gap-4">
                <a href="{{ route('users.index') }}" class="w-full text-center px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg">
                    Cancel
                </a>
                <button type="submit" class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg">
                    Create Account
                </button>
            </div>
        </form>
    </div>
</main>
@endsection