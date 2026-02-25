@if (session()->has('success') || session()->has('error') || session()->has('info'))
    <div class="fixed inset-0 z-[60] flex items-center justify-center px-4 py-6 pointer-events-none">
        <div id="notification-toast" role="status" aria-live="polite"
            class="pointer-events-auto relative w-full max-w-2xl rounded-2xl border px-8 py-6 shadow-2xl backdrop-blur-sm transition-all duration-300 ease-in-out translate-y-0 opacity-100
        @if (session('success')) bg-green-50 border-green-200 text-green-900 
        @elseif(session('error')) bg-red-50 border-red-200 text-red-900 
        @else bg-blue-50 border-blue-200 text-blue-900 @endif">
            <div class="flex items-start gap-3">
                <div class="mt-0.5">
                    @if (session('success'))
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @elseif(session('error'))
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @else
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @endif
                </div>

                <div class="flex-1">
                    <div class="text-base font-semibold">
                        @if (session('success'))
                            Success
                        @elseif(session('error'))
                            Action Needed
                        @else
                            Notice
                        @endif
                    </div>
                    <div class="mt-2 text-base leading-relaxed text-gray-700">
                        {{ session('success') ?? (session('error') ?? session('info')) }}
                    </div>
                </div>

                <button onclick="document.getElementById('notification-toast').remove()"
                    class="ml-2 rounded-md p-1 text-gray-400 hover:text-gray-600 focus:outline-none">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
@endif
