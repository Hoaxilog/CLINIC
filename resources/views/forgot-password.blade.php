<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Tejadent</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center p-4">

    {{-- CARD CONTAINER --}}
    <div class="bg-white rounded-2xl shadow-xl flex max-w-4xl w-full overflow-hidden border border-gray-100 min-h-[660px]">

        {{-- LEFT SIDE: IMAGE --}}
        <div class="hidden md:block w-1/2 relative bg-cover bg-center bg-[url('https://images.unsplash.com/photo-1606811841689-23dfddce3e95?q=80&w=1974&auto=format&fit=crop')]">
            <div class="absolute inset-0 bg-[#0086DA] opacity-20"></div> 
        </div>

        {{-- RIGHT SIDE: FORM --}}
        {{-- Added pb-20 to push the centered content up, away from the absolute footer --}}
        <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center relative pb-20">
            
            {{-- LOGO SECTION --}}
            <div class="flex flex-col items-center mb-8">
                <div class="relative w-15 h-15 rounded-full flex items-center justify-center mb-2">
                    <svg width="56" height="45" viewBox="0 0 56 45" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <mask id="mask0_128_176" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="56" height="45">
                            <path d="M11.783 0.465134C6.04622 2.04593 1.64903 6.81758 0.396845 12.7602C-0.127324 15.307 -0.127324 16.9171 0.367724 19.3468C1.70727 25.6993 7.88082 33.5154 18.5972 42.444L21.0724 44.5225L21.3927 43.1173C22.1499 39.8972 23.402 37.9944 25.6152 36.7941C27.2751 35.8574 28.3525 35.9159 30.158 36.9698C31.5849 37.8187 33.2739 40.5412 33.7398 42.7367C33.9437 43.7321 34.1766 44.5225 34.264 44.5225C34.5261 44.5225 40.8161 39.0775 43.5243 36.5307C51.7363 28.7438 56.0461 20.9862 55.4637 15.0436C55.0269 10.711 53.2797 7.05178 50.2511 4.24147C44.2814 -1.37913 35.4579 -1.4084 29.5756 4.12438L27.7701 5.82227L25.9646 4.15365C22.9361 1.34335 19.4708 -0.00325012 15.3939 0.0552979C14.2 0.0552979 12.5692 0.260216 11.783 0.465134ZM32.7206 9.36442C38.7486 12.3504 41.1947 19.4932 38.1953 25.4066C37.2634 27.2801 34.7008 29.8269 32.808 30.7344C27.0712 33.4862 20.0532 31.0857 17.2867 25.4066C16.0054 22.7134 15.6851 20.6934 16.151 17.9417C16.7626 14.341 19.1796 11.0916 22.5284 9.42297C24.596 8.39838 25.4405 8.22274 28.2943 8.33983C30.4492 8.42765 31.119 8.57402 32.7206 9.36442Z" fill="black"/>
                            <path d="M24.0136 9.97903C21.0142 11.15 18.9757 13.2577 17.7235 16.4193C16.7917 18.7612 16.9664 22.3619 18.1021 24.616C19.2378 26.8116 21.2471 28.8022 23.3729 29.7975C24.9163 30.5001 25.4987 30.6172 27.7701 30.6172C29.9833 30.6172 30.653 30.5001 32.0217 29.8561C39.1271 26.4896 40.5831 17.5024 34.8755 12.2039C32.6624 10.1547 30.7113 9.39355 27.6828 9.39355C26.1976 9.42283 24.9745 9.59847 24.0136 9.97903ZM31.0316 13.4334C30.9151 14.0188 30.9151 15.0142 31.0025 15.5996L31.1772 16.6828L33.2739 16.7706L35.3414 16.8584V20.0493V23.2694L33.3613 23.2987C32.2838 23.328 31.3228 23.4451 31.2063 23.5329C31.119 23.65 31.0025 24.616 31.0025 25.6992L30.9734 27.6898L27.6828 27.7776L24.4213 27.8654V25.6699V23.5036L23.0526 23.328C22.2663 23.2401 21.3345 23.2109 20.9268 23.2401L20.1988 23.2987L20.1114 19.9907L20.0241 16.712H22.2372H24.4213V14.5165V12.321H27.7992H31.2063L31.0316 13.4334Z" fill="black"/>
                        </mask>
                        <g mask="url(#mask0_128_176)">
                            <rect x="-25.5311" y="-23.4609" width="106.265" height="91.7739" fill="#0086DA"/>
                        </g>
                    </svg>
                </div>
                <h1 class="text-3xl font-extrabold text-gray-800 tracking-wide">
                    TEJA<span class="text-[#0086DA]">DENT</span>
                </h1>
            </div>

            {{-- STEP PROGRESS BAR --}}
            <div class="mb-10">
                <div class="flex justify-between text-xs font-semibold text-gray-500 mb-2">
                    <span class="{{ $step >= 1 ? 'text-[#0086DA]' : '' }}">Identify</span>
                    <span class="{{ $step >= 2 ? 'text-[#0086DA]' : '' }}">Verify</span>
                    <span class="{{ $step >= 3 ? 'text-[#0086DA]' : '' }}">Reset</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-[#0086DA] h-2 rounded-full transition-all duration-500 ease-out {{ $step == 1 ? 'w-1/3' : ($step == 2 ? 'w-2/3' : 'w-full') }}"></div>
                </div>
            </div>

            {{-- GLOBAL ERROR MESSAGE --}}
            @if (session('error'))
                <div class="mb-6 p-3 rounded-lg bg-red-50 border-l-4 border-red-500 text-red-700 text-xs flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('password.process') }}" method="POST">
                @csrf
                @php $step = $step ?? 1; @endphp
                <input type="hidden" name="step" value="{{ $step }}">

                {{-- ================= STEP 1: FIND ACCOUNT ================= --}}
                @if($step == 1)
                    <div class="text-center mb-8">
                        <h2 class="text-xl font-bold text-gray-800">Find Account</h2>
                        <p class="text-gray-500 text-xs mt-1">Enter your username to search for your account.</p>
                    </div>

                    <div class="mb-5 relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </span>
                        <input type="text" name="username" placeholder="Enter Username" value="{{ old('username') }}" required 
                            class="w-full pl-10 pr-4 py-3 rounded-lg border-2 border-gray-300 focus:outline-none focus:border-[#0086DA] transition duration-200">
                    </div>
                    @error('username') <p class="text-red-500 text-xs mt-[-1rem] mb-4">{{ $message }}</p> @enderror

                    <button type="submit" class="w-full bg-[#0086DA] hover:bg-[#0073A8] text-white font-bold py-3 px-6 rounded-lg text-lg shadow-md transition duration-200 flex justify-center items-center mt-2">
                        Continue <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                @endif

                {{-- ================= STEP 2: SECURITY CHECK ================= --}}
                @if($step == 2)
                    <input type="hidden" name="user_id" value="{{ $user_id }}">

                    <div class="text-center mb-8">
                        <h2 class="text-xl font-bold text-gray-800">Security Check</h2>
                        <p class="text-gray-500 text-xs mt-1">Please answer your security question.</p>
                    </div>

                    <div class="bg-blue-50 border-l-4 border-[#0086DA] p-4 rounded mb-6">
                        <p class="text-xs text-gray-500 uppercase font-bold">Question:</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $question }}</p>
                    </div>

                    <div class="mb-5 relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                        </span>
                        <input type="text" name="answer" placeholder="Your Answer" required 
                            class="w-full pl-10 pr-4 py-3 rounded-lg border-2 border-gray-300 focus:outline-none focus:border-[#0086DA] transition duration-200">
                    </div>
                    @error('answer') <p class="text-red-500 text-xs mt-[-1rem] mb-4">{{ $message }}</p> @enderror
                    @isset($answer_error) <p class="text-red-500 text-xs mt-[-1rem] mb-4">{{ $answer_error }}</p> @endisset

                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg text-lg shadow-md transition duration-200 mt-2">
                        Verify Answer
                    </button>
                @endif

                {{-- ================= STEP 3: RESET PASSWORD ================= --}}
                @if($step == 3)
                    <input type="hidden" name="user_id" value="{{ $user_id }}">

                    <div class="text-center mb-8">
                        <h2 class="text-xl font-bold text-gray-800">Reset Password</h2>
                        <p class="text-gray-500 text-xs mt-1">Create a new, strong password.</p>
                    </div>

                    <div class="mb-5 relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </span>
                        <input type="password" name="password" placeholder="New Password" required 
                            class="w-full pl-10 pr-4 py-3 rounded-lg border-2 border-gray-300 focus:outline-none focus:border-[#0086DA] transition duration-200">
                    </div>
                    
                    <div class="mb-5 relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </span>
                        <input type="password" name="password_confirmation" placeholder="Confirm New Password" required 
                            class="w-full pl-10 pr-4 py-3 rounded-lg border-2 border-gray-300 focus:outline-none focus:border-[#0086DA] transition duration-200">
                    </div>
                    @error('password') <p class="text-red-500 text-xs mt-[-1rem] mb-4">{{ $message }}</p> @enderror

                    <button type="submit" class="w-full bg-[#0086DA] hover:bg-[#0073A8] text-white font-bold py-3 px-6 rounded-lg text-lg shadow-md transition duration-200 mt-2">
                        Reset Password
                    </button>
                @endif
            </form>

            <div class="absolute bottom-6 left-0 right-0 text-center">
                <p class="text-gray-500 text-xs">Remember your password?</p>
                <a href="{{ route('login') }}" class="font-bold text-[#0086DA] hover:underline text-sm">
                    Back to Login
                </a>
            </div>
        </div>
    </div>

</body>
</html>