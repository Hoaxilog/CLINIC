<div x-data="{ open: false }" @keydown.escape.window="open = false" wire:poll.30s="buildNotifications" class="relative z-50">
    <button
        @click="open = !open"
        class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:bg-slate-50 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2"
        :class="open ? 'scale-105' : 'scale-100'"
    >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="#000000" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M15.5 18C15.5 19.933 13.933 21.5 12 21.5C10.067 21.5 8.5 19.933 8.5 18" />
            <path d="M19.2311 18H4.76887C3.79195 18 3 17.208 3 16.2311C3 15.762 3.18636 15.3121 3.51809 14.9803L4.12132 14.3771C4.68393 13.8145 5 13.0514 5 12.2558V9.5C5 5.63401 8.13401 2.5 12 2.5C15.866 2.5 19 5.634 19 9.5V12.2558C19 13.0514 19.3161 13.8145 19.8787 14.3771L20.4819 14.9803C20.8136 15.3121 21 15.762 21 16.2311C21 17.208 20.208 18 19.2311 18Z" />
        </svg>

        @if($unreadCount > 0)
            <span class="absolute -right-1 -top-1 inline-flex min-h-5 min-w-5 items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold leading-none text-white ring-2 ring-white">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div x-show="open" class="fixed inset-0 z-[70]" style="display: none;">
        <div
            @click="open = false"
            x-transition:enter="transition-opacity duration-300 ease-out"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity duration-300 ease-in"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-slate-900/40"
        ></div>

        <aside
            x-transition:enter="transform transition duration-300 ease-out"
            x-transition:enter-start="translate-x-full opacity-80"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition duration-250 ease-in"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full opacity-80"
            class="absolute right-0 top-0 h-screen w-[92vw] max-w-[420px] border-l border-slate-200 bg-slate-50 shadow-2xl will-change-transform"
            @click.stop
        >
            <div class="flex h-full flex-col">
                <div class="sticky top-0 z-10 border-b border-slate-200 bg-white/95 px-4 py-3 backdrop-blur">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Notifications <span class="text-slate-400">({{ $unreadCount }})</span></h3>
                            <div class="mt-2 flex items-center gap-2 text-[11px] font-semibold">
                                <button
                                    type="button"
                                    wire:click="markAllAsRead"
                                    @disabled($unreadCount === 0)
                                    class="rounded-md border border-sky-200 bg-sky-50 px-2 py-1 text-sky-700 hover:bg-sky-100 disabled:cursor-not-allowed disabled:opacity-40"
                                >
                                    Mark all as read
                                </button>
                                <button
                                    type="button"
                                    wire:click="clearAllNotifications"
                                    @disabled(count($notifications) === 0)
                                    class="rounded-md border border-rose-200 bg-rose-50 px-2 py-1 text-rose-700 hover:bg-rose-100 disabled:cursor-not-allowed disabled:opacity-40"
                                >
                                    Clear
                                </button>
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="open = false"
                            class="rounded-md p-1.5 text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                            aria-label="Close notifications"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="min-h-0 flex-1 overflow-y-auto custom-scrollbar">
                    @if(count($notifications) > 0)
                        <div class="space-y-2 p-2">
                            @foreach($notifications as $notification)
                                <div
                                    wire:key="notification-{{ $notification->id }}"
                                    x-show="open"
                                    x-transition:enter="transform transition duration-300 ease-out"
                                    x-transition:enter-start="translate-x-4 opacity-0"
                                    x-transition:enter-end="translate-x-0 opacity-100"
                                    x-transition:leave="transform transition duration-150 ease-in"
                                    x-transition:leave-start="translate-x-0 opacity-100"
                                    x-transition:leave-end="translate-x-2 opacity-0"
                                    style="transition-delay: {{ min($loop->index * 35, 220) }}ms;"
                                    class="group rounded-xl border border-slate-200 px-3 py-3 transition-colors hover:bg-slate-50 {{ !$notification->is_read ? 'bg-sky-50/60' : 'bg-white' }}"
                                >
                                    <div class="flex items-start gap-3">
                                        <div class="mt-1 shrink-0">
                                            <div class="rounded-full p-1.5
                                                @if(($notification->kind ?? '') === 'pending') bg-amber-100 text-amber-700
                                                @elseif(($notification->kind ?? '') === 'scheduled') bg-blue-100 text-blue-700
                                                @elseif(($notification->kind ?? '') === 'status') bg-emerald-100 text-emerald-700
                                                @else bg-slate-100 text-slate-600
                                                @endif">
                                                @if(($notification->kind ?? '') === 'pending')
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                @elseif(($notification->kind ?? '') === 'scheduled')
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                @elseif(($notification->kind ?? '') === 'status')
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                @else
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="truncate text-sm font-semibold text-slate-900">
                                                    {{ $notification->title }}
                                                </p>
                                                <div class="flex items-center gap-2">
                                                    @if(!$notification->is_read)
                                                        <span class="h-2 w-2 rounded-full bg-blue-500" title="Unread"></span>
                                                    @endif
                                                    @if(!empty($notification->meta))
                                                            <span class="rounded-full border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-semibold text-slate-600">
                                                                {{ $notification->meta }}
                                                            </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <p class="mt-0.5 line-clamp-2 text-sm text-slate-600">
                                                {{ $notification->message }}
                                            </p>

                                            <div class="mt-1 flex items-center gap-2 text-xs text-slate-400">
                                                <span>{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</span>
                                                @if(!empty($notification->status))
                                                    <span>|</span>
                                                    <span class="text-[10px] uppercase tracking-wider text-slate-500">{{ $notification->status }}</span>
                                                @endif
                                            </div>

                                            <div class="mt-2 flex items-center gap-2">
                                                <button
                                                    type="button"
                                                    wire:click="openNotification('{{ $notification->id }}', '{{ $notification->link }}')"
                                                    @click="open = false"
                                                    class="rounded-md bg-sky-50 px-2 py-1 text-xs font-semibold text-sky-700 hover:bg-sky-100"
                                                >
                                                    View
                                                </button>
                                                @if(!$notification->is_read)
                                                    <button
                                                        type="button"
                                                        wire:click="markAsRead('{{ $notification->id }}')"
                                                        class="rounded-md px-2 py-1 text-xs font-semibold text-slate-600 hover:bg-slate-100"
                                                    >
                                                        Mark as read
                                                    </button>
                                                @endif
                                                <button
                                                    type="button"
                                                    wire:click="clearNotification('{{ $notification->id }}')"
                                                    class="rounded-md px-2 py-1 text-xs font-semibold text-rose-600 hover:bg-rose-50"
                                                >
                                                    Clear
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="px-4 py-8 text-center">
                            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100">
                                <svg class="h-6 w-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-slate-500">No notifications</p>
                        </div>
                    @endif
                </div>

                <a href="{{ url('/appointment') }}" @click="open = false" class="block border-t border-slate-200 bg-white px-4 py-3 text-center text-sm font-semibold text-sky-700 transition-colors hover:bg-sky-50 hover:text-sky-800">
                    View All Appointments ->
                </a>
            </div>
        </aside>
    </div>
</div>
