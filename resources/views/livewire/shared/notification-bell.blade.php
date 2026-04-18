<div
    x-data="{
        open: false,
        pollTimer: null,
        init() {
            this.$watch('open', value => {
                document.body.classList.toggle('overflow-hidden', value);
            });

            this.pollTimer = window.setInterval(() => {
                this.pollNotifications();
            }, 5000);
        },
        destroy() {
            if (this.pollTimer !== null) {
                window.clearInterval(this.pollTimer);
            }

            document.body.classList.remove('overflow-hidden');
        },
        pollNotifications() {
            if (this.shouldPausePolling()) {
                return;
            }

            this.$wire.buildNotifications();
        },
        shouldPausePolling() {
            if (this.open) {
                return true;
            }

            return this.hasVisibleBlockingElement([
                {
                    selector: '[data-patient-form-modal]',
                },
                {
                    selector: '.fixed.inset-0, .absolute.inset-0',
                    predicate: (element) => this.hasWireLoadingAttribute(element),
                },
                {
                    selector: '.fixed.inset-0.z-50',
                },
                {
                    selector: '.fixed.inset-0.z-\\[60\\]',
                },
            ]);
        },
        hasWireLoadingAttribute(element) {
            return Array.from(element.attributes).some((attribute) => {
                return attribute.name === 'wire:loading'
                    || attribute.name.startsWith('wire:loading.');
            });
        },
        hasVisibleBlockingElement(selectors) {
            return selectors.some((entry) => {
                const selector = typeof entry === 'string' ? entry : entry.selector;
                const predicate = typeof entry === 'string'
                    ? null
                    : (entry.predicate ?? null);

                return Array.from(document.querySelectorAll(selector)).some((element) => {
                    if (predicate && !predicate(element)) {
                        return false;
                    }

                    if (this.$root.contains(element)) {
                        return false;
                    }

                    if (element.getAttribute('aria-hidden') === 'true') {
                        return false;
                    }

                    const style = window.getComputedStyle(element);

                    return style.display !== 'none'
                        && style.visibility !== 'hidden'
                        && style.pointerEvents !== 'none';
                });
            });
        },
    }"
    @keydown.escape.window="open = false"
    class="relative z-50"
>
    @php
        $footerLink = url('/appointment');
        $footerLabel = 'View all appointments';
    @endphp

    {{-- Bell Button --}}
    <button
        @click="open = !open"
        :class="open ? 'border-gray-400 bg-gray-100 text-black' : ''"
        class="relative flex h-9 w-9 items-center justify-center rounded-sm border border-gray-200 bg-white text-gray-600 shadow-sm transition hover:border-gray-400 hover:bg-gray-100 hover:text-black focus:outline-none focus:ring-2 focus:ring-gray-200"
    >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M15.5 18C15.5 19.933 13.933 21.5 12 21.5C10.067 21.5 8.5 19.933 8.5 18" />
            <path d="M19.2311 18H4.76887C3.79195 18 3 17.208 3 16.2311C3 15.762 3.18636 15.3121 3.51809 14.9803L4.12132 14.3771C4.68393 13.8145 5 13.0514 5 12.2558V9.5C5 5.63401 8.13401 2.5 12 2.5C15.866 2.5 19 5.634 19 9.5V12.2558C19 13.0514 19.3161 13.8145 19.8787 14.3771L20.4819 14.9803C20.8136 15.3121 21 15.762 21 16.2311C21 17.208 20.208 18 19.2311 18Z" />
        </svg>

        @if($unreadCount > 0)
            <span class="absolute -right-1 -top-1 inline-flex min-h-[18px] min-w-[18px] items-center justify-center rounded-sm bg-red-500 px-1 text-[9px] font-bold leading-none text-white ring-2 ring-white">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <template x-teleport="body">
        <div class="fixed inset-0 z-[5000] overflow-hidden pointer-events-none">
            {{-- Backdrop --}}
            <div
                x-show="open"
                @click="open = false"
                x-transition:enter="transition-opacity duration-300 ease-out"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-300 ease-in"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 bg-[#1a2e3b]/40 backdrop-blur-[1px] pointer-events-auto"
                style="display: none;"
            ></div>

            {{-- Drawer --}}
            <aside
                x-show="open"
                @click.outside="open = false"
                x-transition:enter="transform-gpu transition-transform duration-400 ease-[cubic-bezier(0.22,1,0.36,1)]"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform-gpu transition-transform duration-300 ease-[cubic-bezier(0.4,0,1,1)]"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="absolute right-0 top-0 h-screen w-[92vw] max-w-[400px] border-l border-gray-200 bg-[#f6fafd] shadow-xl will-change-transform pointer-events-auto"
                @click.stop
                style="display: none;"
            >
            <div class="flex h-full flex-col">

                {{-- Header --}}
                <div class="sticky top-0 z-10 border-b border-gray-200 bg-white px-4 py-3">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-bold text-[#1a2e3b]">Notifications</h3>
                                @if($unreadCount > 0)
                                    <span class="inline-flex items-center rounded-sm bg-[#e8f4fc] px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-[#0086da]">
                                        {{ $unreadCount }} unread
                                    </span>
                                @endif
                            </div>
                            <div class="mt-2 flex items-center gap-2">
                                <button
                                    type="button"
                                    wire:click="markAllAsRead"
                                    @disabled($unreadCount === 0)
                                    class="inline-flex items-center gap-1 rounded-sm border border-[#d4e8f5] bg-[#e8f4fc] px-2.5 py-1 text-[11px] font-semibold text-[#0086da] transition hover:bg-[#0086da] hover:text-white disabled:cursor-not-allowed disabled:opacity-40"
                                >
                                    Mark all read
                                </button>
                                <button
                                    type="button"
                                    wire:click="clearAllNotifications"
                                    @disabled(count($notifications) === 0)
                                    class="inline-flex items-center gap-1 rounded-sm border border-red-200 bg-white px-2.5 py-1 text-[11px] font-semibold text-red-700 transition hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-40"
                                >
                                    Clear all
                                </button>
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="open = false"
                            class="rounded-sm p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-[#1a2e3b]"
                            aria-label="Close notifications"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Notifications List --}}
                <div class="min-h-0 flex-1 overflow-y-auto custom-scrollbar">
                    @if(count($notifications) > 0)
                        <div class="divide-y divide-gray-100">
                            @foreach($notifications as $notification)
                                <div
                                    wire:key="notification-{{ $notification->id }}"
                                    class="group px-4 py-5 transition-colors hover:bg-white {{ !$notification->is_read ? 'bg-[#e8f4fc]/60 border-l-[3px] border-[#0086da]' : 'bg-transparent' }}"
                                >
                                    <div class="flex items-start gap-3">
                                        {{-- Icon --}}
                                        <div class="mt-0.5 shrink-0">
                                            <div class="rounded-sm p-1.5
                                                @if(($notification->kind ?? '') === 'pending') bg-amber-100 text-amber-700
                                                @elseif(($notification->kind ?? '') === 'scheduled') bg-[#e8f4fc] text-[#0086da]
                                                @elseif(($notification->kind ?? '') === 'status') bg-emerald-100 text-emerald-700
                                                @else bg-gray-100 text-gray-500
                                                @endif">
                                                @if(($notification->kind ?? '') === 'pending')
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                @elseif(($notification->kind ?? '') === 'scheduled')
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                @elseif(($notification->kind ?? '') === 'status')
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                @else
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Content --}}
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="truncate text-sm font-semibold text-[#1a2e3b]">
                                                    {{ $notification->title }}
                                                </p>
                                                <div class="flex shrink-0 items-center gap-1.5">
                                                    @if(!$notification->is_read)
                                                        <span class="h-1.5 w-1.5 rounded-sm bg-[#0086da]" title="Unread"></span>
                                                    @endif
                                                    @if(!empty($notification->meta))
                                                        <span class="rounded-sm border border-gray-200 bg-white px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-gray-500">
                                                            {{ $notification->meta }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <p class="mt-2 line-clamp-2 text-xs leading-relaxed text-[#3d5a6e]">
                                                {{ $notification->message }}
                                            </p>

                                            <div class="mt-2 flex items-center gap-1.5 text-[11px] text-gray-400">
                                                <span>{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</span>
                                                @if(!empty($notification->status))
                                                    <span>·</span>
                                                    <span class="uppercase tracking-wider text-gray-400">{{ $notification->status }}</span>
                                                @endif
                                            </div>

                                            <div class="mt-3 flex items-center gap-1.5 border-t border-gray-100 pt-2.5">
                                                <button
                                                    type="button"
                                                    wire:click="openNotification('{{ $notification->id }}', '{{ $notification->link }}')"
                                                    @click="open = false"
                                                    class="rounded-sm bg-[#0086da] px-2.5 py-1 text-[11px] font-semibold text-white transition hover:bg-[#0073a8]"
                                                >
                                                    View details
                                                </button>
                                                @if(!$notification->is_read)
                                                    <button
                                                        type="button"
                                                        wire:click="markAsRead('{{ $notification->id }}')"
                                                        class="rounded-sm border border-gray-200 bg-white px-2.5 py-1 text-[11px] font-semibold text-[#3d5a6e] transition hover:bg-[#f6fafd]"
                                                    >
                                                        Mark as read
                                                    </button>
                                                @endif
                                                <button
                                                    type="button"
                                                    wire:click="clearNotification('{{ $notification->id }}')"
                                                    class="rounded-sm px-2 py-1 text-[11px] font-semibold text-red-600 transition hover:bg-red-50"
                                                >
                                                    Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center px-4 py-16 text-center">
                            <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-sm bg-[#e8f4fc]">
                                <svg class="h-5 w-5 text-[#0086da]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-[#1a2e3b]">All caught up</p>
                            <p class="mt-0.5 text-xs text-gray-400">No notifications at the moment.</p>
                        </div>
                    @endif
                </div>

                {{-- Footer --}}
                <a
                    href="{{ $footerLink }}"
                    @click="open = false"
                    class="block border-t border-gray-200 bg-white px-4 py-3 text-center text-xs font-semibold text-[#0086da] transition hover:bg-[#e8f4fc]"
                >
                    {{ $footerLabel }} →
                </a>

            </div>
            </aside>
        </div>
    </template>
</div>
