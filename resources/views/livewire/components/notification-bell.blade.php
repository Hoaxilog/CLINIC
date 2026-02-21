<div x-data="{ open: false }" class="relative z-50">
    
    <button 
        @click="open = !open" 
        class="relative p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
    >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="#000000" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M15.5 18C15.5 19.933 13.933 21.5 12 21.5C10.067 21.5 8.5 19.933 8.5 18" />
            <path d="M19.2311 18H4.76887C3.79195 18 3 17.208 3 16.2311C3 15.762 3.18636 15.3121 3.51809 14.9803L4.12132 14.3771C4.68393 13.8145 5 13.0514 5 12.2558V9.5C5 5.63401 8.13401 2.5 12 2.5C15.866 2.5 19 5.634 19 9.5V12.2558C19 13.0514 19.3161 13.8145 19.8787 14.3771L20.4819 14.9803C20.8136 15.3121 21 15.762 21 16.2311C21 17.208 20.208 18 19.2311 18Z" />
        </svg>

        @if($unreadCount > 0)
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
            @if($unreadCount > 0)
                <span class="text-[10px] font-semibold px-2 py-1 rounded-full bg-blue-100 text-blue-700">
                    {{ $unreadCount }} new
                </span>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto custom-scrollbar">
            @if(count($notifications) > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($notifications as $notification)
                        <div class="relative group block px-4 py-3 hover:bg-gray-50 transition-colors bg-white">
                            
                            <a href="{{ $notification->link }}" class="absolute inset-0 z-10"></a>

                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 mt-1">
                                    <div class="p-1.5 rounded-full
                                        @if(($notification->kind ?? '') === 'pending') bg-amber-100 text-amber-700
                                        @elseif(($notification->kind ?? '') === 'scheduled') bg-blue-100 text-blue-700
                                        @elseif(($notification->kind ?? '') === 'status') bg-emerald-100 text-emerald-700
                                        @else bg-slate-100 text-slate-600
                                        @endif">
                                        @if(($notification->kind ?? '') === 'pending')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        @elseif(($notification->kind ?? '') === 'scheduled')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        @elseif(($notification->kind ?? '') === 'status')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-sm font-semibold text-gray-900 truncate">
                                            {{ $notification->title }}
                                        </p>
                                        @if(!empty($notification->meta))
                                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full border border-gray-200 text-gray-600 bg-white">
                                                {{ $notification->meta }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 mt-0.5 line-clamp-2">
                                        {{ $notification->message }}
                                    </p>
                                    <div class="flex items-center gap-2 text-xs text-gray-400 mt-1">
                                        <span>{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</span>
                                        @if(!empty($notification->status))
                                            <span class="text-gray-300">•</span>
                                            <span class="uppercase tracking-wider text-[10px] text-gray-500">{{ $notification->status }}</span>
                                        @endif
                                    </div>
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
                    <p class="text-sm text-gray-500 font-medium">No notifications</p>
                </div>
            @endif
        </div>

        <a href="{{ url('/appointment') }}" class="block bg-gray-50 border-t border-gray-100 px-4 py-3 text-center text-sm font-medium text-blue-600 hover:text-blue-700 hover:bg-gray-100 transition-colors">
            View All Appointments &rarr;
        </a>
    </div>
</div>
