@extends('index')

@section('content')
    @php
        $patientCode = sprintf('PT%04d', $patient->id);
        $patientName = trim(($patient->first_name ?? '') . ' ' . ($patient->last_name ?? ''));
        $patientInitials = strtoupper(substr($patient->first_name ?? 'P', 0, 1) . substr($patient->last_name ?? '', 0, 1));
        $lastVisitLabel = $lastVisit?->appointment_date ? \Carbon\Carbon::parse($lastVisit->appointment_date)->format('M d, Y') : 'No completed visit yet';
        $latestAppointmentLabel = $latestAppointment?->appointment_date
            ? \Carbon\Carbon::parse($latestAppointment->appointment_date)->format('M d, Y')
            : 'No appointment on file';
    @endphp

    <main id="mainContent"
        class="min-h-screen bg-gradient-to-br from-slate-50 via-slate-50 to-sky-50/60 p-4 pb-10 sm:p-6 lg:p-8 ml-64 mt-14 transition-all duration-300 peer-[.collapsed]:ml-16">
        <div class="mx-auto max-w-7xl">
            <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Patient Records</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-900">
                        {{ $patientName ?: 'Unnamed patient' }}
                    </h1>
                    <p class="mt-2 text-sm text-slate-600">
                        Professional patient profile view for admins and staff.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('patient-records') }}"
                        class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                        Back to Patient Records
                    </a>
                    <a href="{{ route('appointment', ['patient_id' => $patient->id]) }}"
                        class="inline-flex items-center rounded-2xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700">
                        Create Appointment
                    </a>
                </div>
            </div>

            <section class="overflow-hidden rounded-[30px] border border-slate-200 bg-white shadow-xl">
                <div class="h-36 bg-gradient-to-r from-sky-600 via-cyan-500 to-blue-600"></div>

                <div class="px-6 pb-8 sm:px-8">
                    <div class="-mt-20 flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
                        <div class="flex flex-col gap-5 sm:flex-row sm:items-end">
                            <div class="h-36 w-36 overflow-hidden rounded-[28px] border-4 border-white bg-slate-100 shadow-xl">
                                @if (!empty($patient->profile_picture))
                                    <img src="{{ asset('storage/' . $patient->profile_picture) . '?v=' . urlencode((string) strtotime((string) data_get($patient, 'profile_picture_updated_at'))) }}"
                                        alt="{{ $patientName }} profile picture" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center bg-slate-200 text-4xl font-bold text-slate-600">
                                        {{ $patientInitials }}
                                    </div>
                                @endif
                            </div>

                            <div class="pb-1">
                                <div class="flex flex-wrap items-center gap-3">
                                    <span
                                        class="inline-flex items-center rounded-full bg-white/90 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.22em] text-sky-700 ring-1 ring-sky-100">
                                        {{ $patientCode }}
                                    </span>
                                    <span
                                        class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-bold uppercase tracking-[0.22em] {{ $patientType === 'Active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">
                                        {{ $patientType }}
                                    </span>
                                </div>

                                <h2 class="mt-4 text-3xl font-black tracking-tight text-slate-900">
                                    {{ $patientName ?: 'Unnamed patient' }}
                                </h2>
                                <p class="mt-2 text-sm text-slate-600">
                                    Portal email: {{ $patient->portal_email ?? 'Not linked' }}
                                </p>
                            </div>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-3 xl:min-w-[440px]">
                            <div class="rounded-2xl border border-slate-200 bg-white/90 px-4 py-3">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Last Visit</p>
                                <p class="mt-2 text-sm font-semibold text-slate-900">{{ $lastVisitLabel }}</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-white/90 px-4 py-3">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Latest Appointment</p>
                                <p class="mt-2 text-sm font-semibold text-slate-900">{{ $latestAppointmentLabel }}</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-white/90 px-4 py-3">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Portal Account</p>
                                <p class="mt-2 text-sm font-semibold text-slate-900">{{ $linkedUser ? 'Linked' : 'Not linked' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 grid gap-6 xl:grid-cols-[minmax(0,1.7fr)_320px]">
                        <div class="space-y-6">
                            <article class="rounded-[28px] border border-slate-200 bg-slate-50/80 p-6">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Basic Info</p>
                                        <h3 class="mt-2 text-xl font-black text-slate-900">Patient information</h3>
                                    </div>
                                </div>

                                <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">First Name</p>
                                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ $patient->first_name ?? 'N/A' }}</p>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Last Name</p>
                                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ $patient->last_name ?? 'N/A' }}</p>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Middle Name</p>
                                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ $patient->middle_name ?? 'N/A' }}</p>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Birth Date</p>
                                        <p class="mt-2 text-sm font-semibold text-slate-900">
                                            {{ $patient->birth_date ? \Carbon\Carbon::parse($patient->birth_date)->format('M d, Y') : 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Age</p>
                                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ $patientAge ?? 'N/A' }}</p>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Gender</p>
                                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ $patient->gender ?? 'N/A' }}</p>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Civil Status</p>
                                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ $patient->civil_status ?? 'N/A' }}</p>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Mobile Number</p>
                                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ $patient->mobile_number ?? 'N/A' }}</p>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Email Address</p>
                                        <p class="mt-2 break-all text-sm font-semibold text-slate-900">{{ $patient->email_address ?? 'N/A' }}</p>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4 md:col-span-2 xl:col-span-3">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Home Address</p>
                                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ $patient->home_address ?? 'N/A' }}</p>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4 md:col-span-2 xl:col-span-3">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Referral</p>
                                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ $patient->referral ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </article>

                            <article class="rounded-[28px] border border-slate-200 bg-slate-50/80 p-6">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Emergency</p>
                                <h3 class="mt-2 text-xl font-black text-slate-900">Emergency contact</h3>

                                <div class="mt-6 grid gap-4 md:grid-cols-3">
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Contact Name</p>
                                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ $patient->emergency_contact_name ?? 'N/A' }}</p>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Contact Number</p>
                                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ $patient->emergency_contact_number ?? 'N/A' }}</p>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Relationship</p>
                                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ $patient->relationship ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </article>

                            <article class="rounded-[28px] border border-slate-200 bg-slate-50/80 p-6">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">History</p>
                                <h3 class="mt-2 text-xl font-black text-slate-900">Recent treatment records</h3>

                                <div class="mt-6 space-y-3">
                                    @forelse ($treatmentRecords as $record)
                                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                                <div>
                                                    <p class="text-sm font-semibold text-slate-900">{{ $record->description ?? 'Treatment record' }}</p>
                                                    <p class="mt-1 text-xs text-slate-500">
                                                        {{ $record->created_at ? \Carbon\Carbon::parse($record->created_at)->format('M d, Y h:i A') : 'Date unavailable' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-6 text-sm text-slate-500">
                                            No treatment records available for this patient yet.
                                        </div>
                                    @endforelse
                                </div>
                            </article>
                        </div>

                        <aside class="space-y-6">
                            <article class="rounded-[28px] border border-slate-200 bg-slate-50/80 p-6">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Portal Access</p>
                                <h3 class="mt-2 text-xl font-black text-slate-900">Linked account</h3>

                                <div class="mt-6 space-y-3">
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Status</p>
                                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ $linkedUser ? 'Linked to patient portal account' : 'No linked portal account found' }}</p>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Username</p>
                                        <p class="mt-2 break-all text-sm font-semibold text-slate-900">{{ $patient->portal_username ?? 'N/A' }}</p>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Portal Email</p>
                                        <p class="mt-2 break-all text-sm font-semibold text-slate-900">{{ $patient->portal_email ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </article>

                            <article class="rounded-[28px] border border-slate-200 bg-slate-50/80 p-6">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Summary</p>
                                <h3 class="mt-2 text-xl font-black text-slate-900">Quick overview</h3>

                                <div class="mt-6 space-y-3">
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Occupation</p>
                                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ $patient->occupation ?? 'N/A' }}</p>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Nickname</p>
                                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ $patient->nickname ?? 'N/A' }}</p>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Office Number</p>
                                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ $patient->office_number ?? 'N/A' }}</p>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Home Number</p>
                                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ $patient->home_number ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </article>
                        </aside>
                    </div>
                </div>
            </section>
        </div>
    </main>
@endsection
