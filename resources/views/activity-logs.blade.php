@extends('index')

@section('content')
<main id="mainContent" class="min-h-screen bg-gray-100 p-6 lg:p-8 ml-64 mt-14 transition-all duration-300 peer-[.collapsed]:ml-16">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">System Activity Logs</h1>
            <p class="text-sm text-gray-500 mt-1">Monitor all actions performed by admins and staff.</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
            <form id="activity-log-filter"
                method="GET"
                action="{{ route('activity-logs') }}"
                class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto items-stretch">

                <!-- Search -->
                <div class="relative w-full sm:w-72">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search user, action, or details..."
                        class="w-full h-10 pl-10 pr-4 border border-black rounded-lg text-sm shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        id="activity-log-search"
                    >

                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none" stroke="#141B34" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2.5 12C2.5 7.52166 2.5 5.28249 3.89124 3.89124C5.28249 2.5 7.52166 2.5 12 2.5C16.4783 2.5 18.7175 2.5 20.1088 3.89124C21.5 5.28249 21.5 7.52166 21.5 12C21.5 16.4783 21.5 18.7175 20.1088 20.1088C18.7175 21.5 16.4783 21.5 12 21.5C7.52166 21.5 5.28249 21.5 3.89124 20.1088C2.5 18.7175 2.5 16.4783 2.5 12Z" />
                            <path d="M14.8284 14.8284L17 17M16 12C16 9.79086 14.2091 8 12 8C9.79086 8 8 9.79086 8 12C8 14.2091 9.79086 16 12 16C14.2091 16 16 14.2091 16 12Z" />
                        </svg>
                    </div>
                </div>

                <!-- Action Filter -->
                <div class="relative w-full sm:w-40">
                    <select name="action"
                            id="activity-log-action"
                            class="w-full h-10 pl-10 pr-4 border border-black rounded-lg text-sm shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Actions</option>
                        <option value="created" @selected(request('action') === 'created')>Created</option>
                        <option value="updated" @selected(request('action') === 'updated')>Updated</option>
                        <option value="deleted" @selected(request('action') === 'deleted')>Deleted</option>
                        <option value="cancelled" @selected(request('action') === 'cancelled')>Cancelled</option>
                    </select>

                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M8.85746 12.5061C6.36901 10.6456 4.59564 8.59915 3.62734 7.44867C3.3276 7.09253 3.22938 6.8319 3.17033 6.3728C2.96811 4.8008 2.86701 4.0148 3.32795 3.5074C3.7889 3 4.60404 3 6.23433 3H17.7657C19.396 3 20.2111 3 20.672 3.5074C21.133 4.0148 21.0319 4.8008 20.8297 6.37281C20.7706 6.83191 20.6724 7.09254 20.3726 7.44867C19.403 8.60062 17.6261 10.6507 15.1326 12.5135C14.907 12.6821 14.7583 12.9567 14.7307 13.2614C14.4837 15.992 14.2559 17.4876 14.1141 18.2442C13.8853 19.4657 12.1532 20.2006 11.226 20.8563C10.6741 21.2466 10.0043 20.782 9.93278 20.1778C9.79643 19.0261 9.53961 16.6864 9.25927 13.2614C9.23409 12.9539 9.08486 12.6761 8.85746 12.5061Z" />
                        </svg>
                    </div>
                </div>
            </form>


            @if(request('search') || request('event') || request('action'))
                <a href="{{ route('activity-logs') }}" class="flex items-center justify-center px-4 py-2 bg-white border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 text-sm font-medium transition-colors shadow-sm">
                    Clear Filter
                </a>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Date & Time</th>
                        <th class="px-6 py-4 font-semibold">Staff Member</th>
                        <th class="px-6 py-4 font-semibold">Action</th>
                        <th class="px-6 py-4 font-semibold">Subject (Who/What?)</th>
                        <th class="px-6 py-4 font-semibold">Changes / Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($activities as $activity)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        
                        {{-- DATE TIME --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-gray-900 font-medium">
                                    {{ $activity->created_at->format('M d, Y') }}
                                </span>
                                <span class="text-xs text-gray-400">
                                    {{ $activity->created_at->format('h:i A') }} 
                                    ({{ $activity->created_at->diffForHumans() }})
                                </span>
                            </div>
                        </td>
                        
                        {{-- STAFF MEMBER --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($activity->causer)
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-sm
                                        {{ optional($activity->causer)->role === 1 ? 'bg-green-500' : 'bg-blue-500' }}">
                                        {{ strtoupper(substr($activity->causer->username, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900">
                                            {{ $activity->causer->username }}
                                        </div>
                                        <div class="text-[10px] uppercase tracking-wide text-gray-400 font-semibold">
                                            @if($activity->causer->role === 1) Admin
                                            @elseif($activity->causer->role === 2) Staff
                                            @else User
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="flex items-center gap-2 text-red-500 italic text-xs">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a10 10 0 1 0 10 10 4 4 0 0 1-5-5 4 4 0 0 1-5-5c0-5.523 4.477-10 10-10z"></path></svg>
                                    System / Automated
                                </span>
                            @endif
                        </td>
                        
                        {{-- ACTION --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                // Check keywords inside your new descriptions ("Updated User Account", etc.)
                                $desc = strtolower($activity->description);
                                $colorClass = 'bg-gray-100 text-gray-700 border-gray-200';

                                if (str_contains($desc, 'created')) {
                                    $colorClass = 'bg-green-100 text-green-700 border-green-200';
                                } elseif (str_contains($desc, 'updated')) {
                                    $colorClass = 'bg-blue-100 text-blue-700 border-blue-200';
                                } elseif (str_contains($desc, 'deleted')) {
                                    $colorClass = 'bg-red-100 text-red-700 border-red-200';
                                }
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $colorClass }} uppercase tracking-wider">
                                {{ $activity->description }}
                            </span>
                        </td>

                        {{-- PERSON --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-400 text-[10px] uppercase tracking-wider mb-0.5">
                                    {{ class_basename($activity->subject_type) }}
                                </span>

                                <span class="font-medium text-gray-900">
                                    @if($activity->subject)
                                        @if($activity->subject_type == 'App\Models\User')
                                            {{ $activity->subject->username }}
                                        @elseif($activity->subject_type == 'App\Models\Patient')
                                            {{ $activity->subject->last_name }}, {{ $activity->subject->first_name }}
                                        @elseif($activity->subject_type == 'App\\Models\\Appointment')
                                            @if(optional($activity->subject->patient)->last_name)
                                                {{ $activity->subject->patient->last_name }}, {{ $activity->subject->patient->first_name }}
                                            @elseif(isset($activity->properties['attributes']['patient_name']))
                                                {{ $activity->properties['attributes']['patient_name'] }}
                                            @else
                                                ID: {{ $activity->subject_id }}
                                            @endif
                                        @else
                                            ID: {{ $activity->subject_id }}
                                        @endif
                                    @else
                                        @if(isset($activity->properties['attributes']['username']))
                                            <span class="line-through decoration-red-500">{{ $activity->properties['attributes']['username'] }}</span>
                                        @elseif(isset($activity->properties['attributes']['last_name']))
                                            <span class="line-through decoration-red-500">{{ $activity->properties['attributes']['last_name'] }}</span>
                                        @elseif(isset($activity->properties['attributes']['patient_name']))
                                            <span class="line-through decoration-red-500">{{ $activity->properties['attributes']['patient_name'] }}</span>
                                        @else
                                            ID: {{ $activity->subject_id }} <span class="text-xs text-red-500">(Deleted)</span>
                                        @endif
                                    @endif
                                </span>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-sm">
                            @if(str_contains(strtolower($activity->event), 'updated'))
                                <div class="space-y-1">
                                    @foreach($activity->properties['attributes'] ?? [] as $key => $newValue)
                                        @continue(in_array($key, ['updated_at', 'created_at', 'id', 'modified_by', 'password', 'remember_token', 'email_verified_at']))

                                        <div class="flex items-center gap-2 text-xs">
                                            <span class=" font-semibold text-gray-500 uppercase tracking-wide w-50 truncate" title="{{ str_replace('_', ' ', $key) }}">
                                                {{ str_replace('_', ' ', $key) }}
                                            </span>
                                            
                                            <div class="flex items-center gap-1.5 flex-1">
                                                @if(isset($activity->properties['old'][$key]))
                                                    <span class="text-red-400 line-through decoration-red-200">
                                                        {{ $activity->properties['old'][$key] }}
                                                    </span>
                                                    <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                                @endif
                                                
                                                <span class="text-gray-900 font-medium bg-yellow-50 px-1 rounded border border-yellow-100">
                                                    {{ Str::limit($newValue, 25) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif(str_contains(strtolower($activity->event), 'created'))
                                <span class="text-gray-400 italic text-xs flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    New Record Created
                                </span>
                            @elseif(str_contains(strtolower($activity->event), 'deleted'))
                                <span class="text-red-400 italic text-xs">Record permanently removed</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500 bg-white">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p class="text-lg font-medium text-gray-900">No logs found</p>
                                <p class="text-sm text-gray-400">
                                    @if(request('search'))
                                        No results match "<strong>{{ request('search') }}</strong>".
                                    @else
                                        Activity will show up here once users start performing actions.
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activities->hasPages())
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
            {{ $activities->links() }}
        </div>
        @endif
    </div>
</main>

<script>
    (function () {
        const form = document.getElementById('activity-log-filter');
        const search = document.getElementById('activity-log-search');
        const action = document.getElementById('activity-log-action');

        if (action && form) {
            action.addEventListener('change', () => form.submit());
        }

        if (search && form) {
            let timer;
            search.addEventListener('input', () => {
                clearTimeout(timer);
                timer = setTimeout(() => form.submit(), 400);
            });
        }
    })();
</script>
@endsection