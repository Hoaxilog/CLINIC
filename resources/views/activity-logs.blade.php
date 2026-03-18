@extends('index')

@section('content')
<main id="mainContent" class="min-h-screen bg-gray-100 p-4 sm:p-6 lg:p-8 ml-64 mt-14 transition-all duration-300 peer-[.collapsed]:ml-16">
    <div class="flex w-full flex-col gap-6">
        <div class="flex flex-col gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">System Activity Logs</h1>
                <p class="mt-1 text-sm text-gray-500">Monitor actions performed by admins and staff in one timeline.</p>
            </div>

            <div class="rounded-none border border-gray-200 bg-white p-4 shadow-sm lg:p-5">
                <form id="activity-log-filter"
                    method="GET"
                    action="{{ route('activity-logs') }}"
                    class="flex w-full flex-wrap items-end gap-3 lg:flex-nowrap">
                    <div class="relative min-w-[18rem] flex-1 bg-white">
                        <div class="relative">
                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="User, action, details..."
                                class="w-full rounded-none border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-700 shadow-sm focus:border-[#0086da] focus:outline-none focus:ring-2 focus:ring-[#0086da]"
                                id="activity-log-search"
                            >

                            <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2.5 12C2.5 7.52166 2.5 5.28249 3.89124 3.89124C5.28249 2.5 7.52166 2.5 12 2.5C16.4783 2.5 18.7175 2.5 20.1088 3.89124C21.5 5.28249 21.5 7.52166 21.5 12C21.5 16.4783 21.5 18.7175 20.1088 20.1088C18.7175 21.5 16.4783 21.5 12 21.5C7.52166 21.5 5.28249 21.5 3.89124 20.1088C2.5 18.7175 2.5 16.4783 2.5 12Z" />
                                    <path d="M14.8284 14.8284L17 17M16 12C16 9.79086 14.2091 8 12 8C9.79086 8 8 9.79086 8 12C8 14.2091 9.79086 16 12 16C14.2091 16 16 14.2091 16 12Z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="relative w-full sm:w-44 lg:w-48">
                        <div class="relative">
                            <select name="action"
                                    id="activity-log-action"
                                    class="w-full rounded-none border border-gray-200 bg-white py-2.5 pl-10 pr-8 text-sm text-gray-700 shadow-sm focus:border-[#0086da] focus:outline-none focus:ring-2 focus:ring-[#0086da]">
                                <option value="">All Actions</option>
                                <option value="created" @selected(request('action') === 'created')>Created</option>
                                <option value="updated" @selected(request('action') === 'updated')>Updated</option>
                                <option value="deleted" @selected(request('action') === 'deleted')>Deleted</option>
                                <option value="cancelled" @selected(request('action') === 'cancelled')>Cancelled</option>
                            </select>

                            <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M8.85746 12.5061C6.36901 10.6456 4.59564 8.59915 3.62734 7.44867C3.3276 7.09253 3.22938 6.8319 3.17033 6.3728C2.96811 4.8008 2.86701 4.0148 3.32795 3.5074C3.7889 3 4.60404 3 6.23433 3H17.7657C19.396 3 20.2111 3 20.672 3.5074C21.133 4.0148 21.0319 4.8008 20.8297 6.37281C20.7706 6.83191 20.6724 7.09254 20.3726 7.44867C19.403 8.60062 17.6261 10.6507 15.1326 12.5135C14.907 12.6821 14.7583 12.9567 14.7307 13.2614C14.4837 15.992 14.2559 17.4876 14.1141 18.2442C13.8853 19.4657 12.1532 20.2006 11.226 20.8563C10.6741 21.2466 10.0043 20.782 9.93278 20.1778C9.79643 19.0261 9.53961 16.6864 9.25927 13.2614C9.23409 12.9539 9.08486 12.6761 8.85746 12.5061Z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="inline-flex h-[42px] items-center justify-center rounded-none border border-[#0086da] bg-[#0086da] px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-[#0073bb]">
                        Search
                    </button>

                    @if(request('search') || request('event') || request('action'))
                        <a href="{{ route('activity-logs') }}" class="inline-flex h-[42px] items-center justify-center rounded-none border border-gray-200 bg-white px-4 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                            Clear Filter
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <section class="overflow-hidden rounded-none border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="border-b border-gray-200 bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Date & Time</th>
                        <th class="px-6 py-4 font-semibold">Staff Member</th>
                        <th class="px-6 py-4 font-semibold">Action</th>
                        <th class="px-6 py-4 font-semibold">Subject</th>
                        <th class="px-6 py-4 font-semibold">Summary</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($activities as $activity)
                    <tr class="transition-colors duration-150 hover:bg-gray-50">
                        
                        {{-- DATE TIME --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col gap-0.5">
                                <span class="font-semibold text-gray-900">
                                    {{ $activity->created_at->format('M d, Y') }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ $activity->created_at->format('h:i A') }} 
                                    ({{ $activity->created_at->diffForHumans() }})
                                </span>
                            </div>
                        </td>
                        
                        {{-- STAFF MEMBER --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($activity->causer)
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-bold text-white shadow-sm
                                        {{ optional($activity->causer)->isAdmin() ? 'bg-emerald-500' : 'bg-[#0086da]' }}">
                                        {{ strtoupper(substr($activity->causer->username, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">
                                            {{ $activity->causer->username }}
                                        </div>
                                        <div class="text-[10px] font-semibold uppercase tracking-wide text-gray-400">
                                            {{ \App\Models\User::roleLabelFromId((int) $activity->causer->role) }}
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="flex items-center gap-2 text-xs italic text-rose-500">
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
                                    $colorClass = 'bg-emerald-100 text-emerald-700 border-emerald-200';
                                } elseif (str_contains($desc, 'updated')) {
                                    $colorClass = 'bg-blue-100 text-blue-700 border-blue-200';
                                } elseif (str_contains($desc, 'deleted')) {
                                    $colorClass = 'bg-rose-100 text-rose-700 border-rose-200';
                                }
                            @endphp
                            <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide {{ $colorClass }}">
                                {{ $activity->description }}
                            </span>
                        </td>

                        {{-- PERSON --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            <div class="flex flex-col gap-0.5">
                                <span class="inline-flex w-fit rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-gray-500">
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
                                            <span class="line-through decoration-rose-500">{{ $activity->properties['attributes']['username'] }}</span>
                                        @elseif(isset($activity->properties['attributes']['last_name']))
                                            <span class="line-through decoration-rose-500">{{ $activity->properties['attributes']['last_name'] }}</span>
                                        @elseif(isset($activity->properties['attributes']['patient_name']))
                                            <span class="line-through decoration-rose-500">{{ $activity->properties['attributes']['patient_name'] }}</span>
                                        @else
                                            ID: {{ $activity->subject_id }} <span class="text-xs text-rose-500">(Deleted)</span>
                                        @endif
                                    @endif
                                </span>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-700">
                            @php
                                $role = optional($activity->causer)->role;
                                $actor = \App\Models\User::roleLabelFromId($role !== null ? (int) $role : null);

                                $event = strtolower($activity->event ?? '');
                                $desc = strtolower($activity->description ?? '');
                                $subjectType = strtolower(class_basename($activity->subject_type ?? ''));

                                $attrs = $activity->properties['attributes'] ?? [];
                                $changedKeys = array_keys($attrs);

                                $fieldMap = [
                                    'mobile_number' => 'phone number',
                                    'contact' => 'phone number',
                                    'email' => 'email',
                                    'email_address' => 'email',
                                    'home_address' => 'address',
                                    'address' => 'address',
                                    'birth_date' => 'birth date',
                                    'gender' => 'gender',
                                    'status' => 'status',
                                ];

                                $basicInfoKeys = [
                                    'first_name','last_name','middle_name','nickname','occupation','birth_date','gender','civil_status',
                                    'address','home_address','office_address','home_number','office_number','mobile_number','contact',
                                    'email','email_address','referral','emergency_contact_name','emergency_contact_number','relationship',
                                    'who_answering','relationship_to_patient','father_name','father_number','mother_name','mother_number',
                                    'guardian_name','guardian_number',
                                ];

                                $humanFields = [];
                                foreach ($changedKeys as $key) {
                                    if (in_array($key, ['updated_at', 'created_at', 'id', 'modified_by', 'password', 'remember_token', 'email_verified_at'], true)) {
                                        continue;
                                    }
                                    $humanFields[] = $fieldMap[$key] ?? str_replace('_', ' ', $key);
                                }
                                $humanFields = array_values(array_unique($humanFields));
                                $fieldsText = count($humanFields) ? implode(' and ', $humanFields) : 'details';

                                $summary = null;

                                if (str_contains($event, 'created') || str_contains($desc, 'created')) {
                                    if (str_contains($event, 'health_history') || str_contains($desc, 'health history')) {
                                        $summary = "{$actor} added medical history.";
                                    } elseif (str_contains($event, 'patient') || $subjectType === 'patient') {
                                        $summary = "{$actor} added basic info.";
                                    } elseif (str_contains($event, 'appointment') || $subjectType === 'appointment') {
                                        $summary = "{$actor} created an appointment.";
                                    } elseif (str_contains($event, 'treatment_record') || str_contains($desc, 'treatment record')) {
                                        $summary = "{$actor} added a treatment record.";
                                    } elseif (str_contains($event, 'dental_chart') || str_contains($desc, 'dental chart')) {
                                        $summary = "{$actor} added a dental chart.";
                                    } else {
                                        $summary = "{$actor} created a record.";
                                    }
                                } elseif (str_contains($event, 'updated') || str_contains($desc, 'updated')) {
                                    if (str_contains($event, 'health_history') || str_contains($desc, 'health history')) {
                                        $summary = "{$actor} updated medical history.";
                                    } elseif (str_contains($event, 'patient') || $subjectType === 'patient') {
                                        $basicInfoChanged = count(array_intersect($changedKeys, $basicInfoKeys)) > 0;
                                        if ($basicInfoChanged) {
                                            $summary = "{$actor} updated patient basic information.";
                                        } else {
                                            $summary = "{$actor} updated patient {$fieldsText}.";
                                        }
                                    } elseif (str_contains($event, 'appointment') || $subjectType === 'appointment') {
                                        $oldStatus = $activity->properties['old']['status'] ?? null;
                                        $newStatus = $activity->properties['attributes']['status'] ?? null;
                                        if ($oldStatus && $newStatus && $oldStatus !== $newStatus) {
                                            $summary = "{$actor} updated appointment status from {$oldStatus} to {$newStatus}.";
                                        } else {
                                            $summary = "{$actor} updated appointment {$fieldsText}.";
                                        }
                                    } elseif (str_contains($event, 'treatment_record') || str_contains($desc, 'treatment record')) {
                                        $summary = "{$actor} updated treatment record.";
                                    } elseif (str_contains($event, 'dental_chart') || str_contains($desc, 'dental chart')) {
                                        $summary = "{$actor} updated dental chart.";
                                    } else {
                                        $summary = "{$actor} updated a record.";
                                    }
                                } elseif (str_contains($event, 'deleted') || str_contains($desc, 'deleted')) {
                                    if (str_contains($event, 'patient') || $subjectType === 'patient') {
                                        $summary = "{$actor} deleted a patient record.";
                                    } elseif (str_contains($event, 'appointment') || $subjectType === 'appointment') {
                                        $summary = "{$actor} deleted an appointment.";
                                    } else {
                                        $summary = "{$actor} deleted a record.";
                                    }
                                } elseif (str_contains($event, 'cancelled') || str_contains($desc, 'cancelled')) {
                                    $summary = "{$actor} cancelled an appointment.";
                                }
                            @endphp
                            <span class="leading-6 text-gray-700">
                                {{ $summary ?? 'Activity recorded.' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="bg-white px-6 py-16 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <p class="text-lg font-semibold text-gray-900">No logs found</p>
                                <p class="mt-1 text-sm text-gray-500">
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
            <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
                {{ $activities->links() }}
            </div>
            @endif
        </section>
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
