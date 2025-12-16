<div x-data="{ open: false }" class="relative z-50">
    
    <button 
        @click="open = !open" 
        class="relative p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>

        @if(isset($unreadCount) && $unreadCount > 0)
            <span class="absolute top-0 right-0 flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
            </span>
        @endif
    </button>

    <div 
        x-show="open" 
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-3 w-80 md:w-96 bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden origin-top-right"
        style="display: none;"
    >
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-sm font-bold text-gray-800">Notifications</h3>
            @if(isset($unreadCount) && $unreadCount > 0)
                <a href="{{ url('/notifications/mark-all') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium hover:underline">
                    Mark all read
                </a>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto custom-scrollbar">
            @if(isset($notifications) && count($notifications) > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($notifications as $notification)
                        <div class="relative group block px-4 py-3 hover:bg-gray-50 transition-colors {{ $notification->is_read ? 'bg-white' : 'bg-blue-50/30' }}">
                            
                            <a href="{{ url('/notifications/read/' . $notification->id) }}" class="absolute inset-0 z-10"></a>

                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 mt-1">
                                    @if(str_contains(strtolower($notification->title), 'cancel'))
                                        <div class="p-1.5 bg-red-100 rounded-full text-red-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </div>
                                    @elseif(str_contains(strtolower($notification->title), 'completed'))
                                        <div class="p-1.5 bg-green-100 rounded-full text-green-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                    @else
                                        <div class="p-1.5 bg-blue-100 rounded-full text-blue-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">
                                        {{ $notification->title }}
                                    </p>
                                    <p class="text-sm text-gray-600 mt-0.5 line-clamp-2">
                                        {{ $notification->message }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="px-4 py-8 text-center">
                    <div class="mx-auto w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-500 font-medium">No new notifications</p>
                    <p class="text-xs text-gray-400 mt-1">We'll let you know when something arrives.</p>
                </div>
            @endif
        </div>

        <a href="{{ url('/appointments') }}" class="block bg-gray-50 border-t border-gray-100 px-4 py-3 text-center text-sm font-medium text-blue-600 hover:text-blue-700 hover:bg-gray-100 transition-colors">
            View All Appointments &rarr;
        </a>
    </div>
</div>