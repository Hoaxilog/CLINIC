<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login </title>
    @vite('resources/css/app.css')
</head>
<body>
    <div class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl flex max-w-4xl w-full overflow-hidden">
            <div class="w-1/2 relative bg-cover bg-center rounded-l-2xl" style="background-image: url('https://i.imgur.com/your-image-url.jpg');">
                <img src={{ asset('login-image.png') }} alt="Dental Clinic" class="w-full h-full object-cover rounded-l-2xl">
            </div>

            <form method="POST" action="/login"
                class="w-1/2 p-12 flex flex-col justify-center items-center">
                @csrf
                <div class="flex items-center mb-4">
                    <div class="relative w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center mr-2">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        <svg class="absolute w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                
                <p class="text-xl text-[#0086DA] font-semibold mb-2">
                    "Your smile is our priority"
                </p>
                <h1 class="text-5xl font-extrabold text-gray-800 mb-8 tracking-wide">
                    TEJA<span class="text-[#0086DA]">DENT</span>
                </h1>
                
                <div class="relative w-full max-w-xs mb-6">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </span>
                    <input
                        value="{{ old('username') }}"
                        type="text"
                        placeholder="Username"
                        name="username"
                        class="w-full pl-10 pr-4 py-3 rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 transition duration-200"
                    />
                </div>
                @error('username')
                    <div style="color: red;">{{ $message }}</div>
                @enderror

                <div class="relative w-full max-w-xs">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </span>
                    <input
                        type="password"
                        placeholder="Password"
                        name="password"
                        class="w-full pl-10 pr-10 py-3 rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 transition duration-200"
                    />
                </div>
                @error('password')
                    <div style="color: red;">{{ $message }}</div>
                @enderror
                @if (session('failed'))
                    <div class="text-red-500">
                        {{ session('failed') }}
                    </div>
                @endif

                <button
                    class="w-full max-w-xs bg-[#0086DA] hover:bg-[#0073A8] text-white font-bold py-3 px-6 rounded-lg text-lg transition duration-200 shadow-md"
                >
                    SIGN IN
                </button>
            </form>
        </div>
    </div>
</body>
</html>