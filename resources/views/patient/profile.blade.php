@extends('index')

@section('content')
@php
    $patientFirstName = data_get($patient, 'first_name', '');
    $patientLastName = data_get($patient, 'last_name', '');
    $patientName = trim($patientFirstName . ' ' . $patientLastName);
    $patientCode = $patient ? sprintf('PT%04d', $patient->id) : null;
    $patientInitials = $patient
        ? strtoupper(substr($patientFirstName ?: 'P', 0, 1) . substr($patientLastName ?: '', 0, 1))
        : strtoupper(substr($user->username ?? 'P', 0, 1));
    $birthDate = data_get($patient, 'birth_date') ? \Carbon\Carbon::parse($patient->birth_date)->format('M d, Y') : 'N/A';
@endphp

<main id="mainContent" class="min-h-screen bg-gradient-to-br from-slate-50 via-slate-50 to-slate-100 p-4 pb-10 sm:p-6 lg:p-8">
    <div class="mb-8">
        <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Patient Portal</p>
        <h1 class="mt-2 text-3xl font-bold text-slate-900 sm:text-4xl">My Profile</h1>
        <p class="mt-2 text-sm text-slate-600">
            Review your personal details and manage your profile picture.
        </p>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('failed'))
        <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-medium text-rose-800 shadow-sm">
            {{ session('failed') }}
        </div>
    @endif

    @if (!$patient)
        <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Patient profile not linked</h2>
            <p class="mt-2 text-sm text-slate-600">
                We couldn't find a patient record associated with your account. Please contact the clinic so we can link your profile.
            </p>
        </section>
    @endif

    <!-- Premium Profile Header Card with Picture Upload -->
    <section class="mt-8">
        <article class="rounded-3xl border border-slate-200 bg-white shadow-xl overflow-hidden">
            <!-- Gradient Background -->
            <div class="h-32 bg-gradient-to-r from-sky-500 via-sky-400 to-blue-500"></div>
            
            <!-- Profile Content -->
            <div class="px-6 sm:px-8 pb-8">
                <!-- Profile Picture + Info -->
                <div class="flex flex-col sm:flex-row items-start gap-6 -mt-16 mb-8">
                    <!-- Left: Profile Picture with Upload -->
                    <div class="relative flex-shrink-0">
                        <div class="h-40 w-40 rounded-full bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center flex-shrink-0 overflow-hidden ring-4 ring-white shadow-lg">
                            @if(data_get($user, 'profile_picture'))
                                <img id="profile-pic-display" src="{{ asset('storage/' . data_get($user, 'profile_picture')) }}" alt="{{ $patientName }}" class="h-40 w-40 object-cover">
                            @else
                                <svg id="profile-pic-icon" xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            @endif
                        </div>
                        
                        <!-- Upload Button -->
                        <label for="profile-picture-input" class="absolute bottom-0 right-0 h-14 w-14 rounded-full bg-sky-600 hover:bg-sky-700 flex items-center justify-center cursor-pointer ring-4 ring-white shadow-lg transition group" title="Upload image here">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </label>
                        
                        <!-- Instruction Text -->
                        <p class="text-xs text-slate-500 text-center mt-3">Upload image here</p>
                        
                        <!-- Hidden File Input -->
                        <form id="picture-form" action="{{ route('profile.picture.upload') }}" method="POST" enctype="multipart/form-data" class="hidden">
                            @csrf
                            <input type="file" id="profile-picture-input" name="profile_picture" accept="image/*" class="hidden">
                        </form>
                    </div>

                    <!-- Patient Info -->
                    <div class="pt-4 sm:pt-6 flex-1">
                        <div class="flex items-baseline gap-3 mb-2">
                            <h2 class="text-3xl font-bold text-slate-900">{{ $patientName ?: $user->username }}</h2>
                            <span class="inline-block px-3 py-1 rounded-full bg-blue-50 text-sky-700 text-xs font-semibold">{{ $patientCode ?? 'Not linked' }}</span>
                        </div>
                        <p class="text-slate-600 mb-4">Patient Account</p>
                        
                        <div class="flex gap-3 mb-6">
                            <button onclick="openProfileModal()" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                View Profile
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-400 font-semibold">Birth Date</p>
                                <p class="text-sm font-semibold text-slate-900 mt-1">{{ $birthDate }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-400 font-semibold">Gender</p>
                                <p class="text-sm font-semibold text-slate-900 mt-1">{{ $patient?->gender ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-400 font-semibold">Mobile</p>
                                <p class="text-sm font-semibold text-slate-900 mt-1">{{ $patient?->mobile_number ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-400 font-semibold">Email</p>
                                <p class="text-sm font-semibold text-slate-900 mt-1">{{ $patient?->email_address ?? $user->email ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address Card -->
                <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-50 to-slate-100 p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-400 font-semibold">Home Address</p>
                    <p class="text-sm font-semibold text-slate-900 mt-2">{{ $patient?->home_address ?? 'No address on file' }}</p>
                </div>
            </div>
        </article>
    </section>

    <!-- Emergency Contact Card -->
    <section class="mt-6">
        <article class="rounded-3xl border border-slate-200 bg-white p-8 shadow-lg">
            <h2 class="text-xl font-bold text-slate-900 mb-6">Emergency Contact</h2>
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-50 to-slate-100 p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-400 font-semibold">Contact Name</p>
                    <p class="text-sm font-semibold text-slate-900 mt-2">{{ $patient?->emergency_contact_name ?? 'N/A' }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-50 to-slate-100 p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-400 font-semibold">Contact Number</p>
                    <p class="text-sm font-semibold text-slate-900 mt-2">{{ $patient?->emergency_contact_number ?? 'N/A' }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-50 to-slate-100 p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-400 font-semibold">Relationship</p>
                    <p class="text-sm font-semibold text-slate-900 mt-2">{{ $patient?->relationship ?? 'N/A' }}</p>
                </div>
            </div>
        </article>
    </section>

    <!-- Account Settings & Security -->
    <section class="mt-8 grid gap-6 lg:grid-cols-2">
        <!-- Account Settings -->
        <article class="rounded-3xl border border-slate-200 bg-white p-8 shadow-lg">
            <h2 class="text-xl font-bold text-slate-900 mb-6">Account Settings</h2>

            <form action="{{ route('profile.update') }}" method="POST" class="space-y-5">
                @csrf
                @method('PATCH')

                <div>
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Username</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}"
                        class="w-full mt-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-sky-500 focus:ring-2 focus:ring-sky-100 transition">
                    @error('username')
                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Contact Number</label>
                    <input type="text" name="contact" value="{{ old('contact', data_get($user, 'contact')) }}"
                        class="w-full mt-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-sky-500 focus:ring-2 focus:ring-sky-100 transition">
                    @error('contact')
                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="w-full mt-6 px-4 py-2.5 rounded-lg bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold transition">
                    Save Changes
                </button>
            </form>
        </article>

        <!-- Security -->
        <article class="rounded-3xl border border-slate-200 bg-white p-8 shadow-lg">
            <h2 class="text-xl font-bold text-slate-900 mb-6">Security</h2>
            
            @if (!empty($isGoogleUser))
                <p class="text-slate-600 text-sm mb-6">Your account uses Google Login. We will email you a secure link to set a password.</p>
                <form action="{{ route('profile.password.reset-link') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold transition">
                        Send Reset Link
                    </button>
                </form>
            @else
                <form action="{{ route('profile.password') }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Current Password</label>
                        <input type="password" name="current_password"
                            class="w-full mt-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-sky-500 focus:ring-2 focus:ring-sky-100 transition"
                            placeholder="Enter current password">
                        @error('current_password')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">New Password</label>
                        <input type="password" name="password"
                            class="w-full mt-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-sky-500 focus:ring-2 focus:ring-sky-100 transition"
                            placeholder="Enter new password">
                        @error('password')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Confirm Password</label>
                        <input type="password" name="password_confirmation"
                            class="w-full mt-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-sky-500 focus:ring-2 focus:ring-sky-100 transition"
                            placeholder="Confirm new password">
                    </div>

                    <button type="submit" class="w-full mt-6 px-4 py-2.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold transition">
                        Update Password
                    </button>
                </form>
            @endif
        </article>
    </section>

    <!-- Privacy & Account Deletion -->
    <section class="mt-8">
        <article class="rounded-3xl border border-red-200 bg-white p-8 shadow-lg">
            <h2 class="text-xl font-bold text-slate-900 mb-2">Privacy & Account Deletion</h2>
            <p class="text-sm text-slate-600 mb-6">Before requesting permanent deletion, please review the conditions below. This process removes access to your Tejada Dent patient portal and cannot be reversed.</p>

            <div class="space-y-6 rounded-3xl border border-red-200 bg-[linear-gradient(180deg,#fff7f7_0%,#fffdfd_100%)] p-6">
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    Account deletion is permanent. Once completed, your portal access and linked profile information will no longer be recoverable from your account.
                </div>

                <div class="grid gap-4 lg:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 bg-white p-5">
                        <h3 class="text-sm font-bold uppercase tracking-[0.18em] text-slate-500">Deletion Terms</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-6 text-slate-700">
                            <li>You are requesting the permanent closure of your patient portal account.</li>
                            <li>Your login credentials, uploaded profile picture, and linked patient account access will be removed.</li>
                            <li>Clinic records required for legal, medical, billing, or regulatory compliance may be retained by Tejada Dent where applicable.</li>
                            <li>Appointments, treatment history, and other clinical records may no longer be viewable through your portal after deletion.</li>
                        </ul>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-5">
                        <h3 class="text-sm font-bold uppercase tracking-[0.18em] text-slate-500">Before You Continue</h3>
                        <ul class="mt-4 space-y-3 text-sm leading-6 text-slate-700">
                            <li>Ensure you have saved any information you may need from your account.</li>
                            <li>Contact the clinic first if you only need help updating personal details or changing login access.</li>
                            <li>If you have active or upcoming appointments, confirm with the clinic how they should be handled.</li>
                            <li>Proceed only if you fully understand that this action cannot be undone.</li>
                        </ul>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" id="accept-terms-checkbox" class="mt-1 h-4 w-4 rounded border-red-300 text-red-600 focus:ring-red-500">
                        <span class="text-sm leading-6 text-slate-700">
                            I have read and understood the account deletion terms above, and I accept responsibility for permanently deleting my Tejada Dent patient portal account.
                        </span>
                    </label>
                </div>

                <div class="rounded-2xl border border-red-200 bg-white p-5">
                    <label for="delete-confirmation-input" class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-600">Final Confirmation</label>
                    <input type="text" id="delete-confirmation-input" placeholder="Type DELETE ACCOUNT to confirm"
                        class="mt-3 w-full rounded-xl border border-red-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition"
                        autocomplete="off">
                    <p class="mt-2 text-xs text-slate-500">Type the confirmation phrase exactly as shown to enable account deletion.</p>
                </div>

                <div class="flex flex-col gap-3 border-t border-red-200 pt-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs leading-5 text-slate-500">If you are unsure, stop here and contact Tejada Dent for assistance instead of deleting your account.</p>
                    <button id="delete-account-btn" type="button" disabled
                        class="inline-flex items-center justify-center rounded-xl bg-red-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:bg-slate-300"
                        onclick="openDeleteModal()">
                        Delete Account Permanently
                    </button>
                </div>
            </div>
        </article>
    </section>
</main>

<script>
    // Single profile picture uploader via button
    document.getElementById('profile-picture-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Show upload feedback
            const picDisplay = document.getElementById('profile-pic-display');
            const picIcon = document.getElementById('profile-pic-icon');
            
            // Preview image before upload
            const reader = new FileReader();
            reader.onload = function(event) {
                if (picDisplay) {
                    picDisplay.src = event.target.result;
                } else if (picIcon) {
                    const img = document.createElement('img');
                    img.id = 'profile-pic-display';
                    img.src = event.target.result;
                    img.className = 'h-40 w-40 object-cover';
                    picIcon.parentElement.replaceChild(img, picIcon);
                }
            };
            reader.readAsDataURL(file);
            
            // Submit form after brief delay
            setTimeout(() => {
                document.getElementById('picture-form').submit();
            }, 300);
        }
    });

    // Account deletion validation
    const acceptCheckbox = document.getElementById('accept-terms-checkbox');
    const deleteInput = document.getElementById('delete-confirmation-input');
    const deleteBtn = document.getElementById('delete-account-btn');

    function validateDeleteButton() {
        const isChecked = acceptCheckbox.checked;
        const isTextCorrect = deleteInput.value === 'DELETE ACCOUNT';
        deleteBtn.disabled = !(isChecked && isTextCorrect);
    }

    acceptCheckbox.addEventListener('change', validateDeleteButton);
    deleteInput.addEventListener('input', validateDeleteButton);

    function deleteAccount() {
        if (!acceptCheckbox.checked) {
            alert('Please confirm that you understand the account deletion terms.');
            return;
        }
        if (deleteInput.value !== 'DELETE ACCOUNT') {
            alert('Please type "DELETE ACCOUNT" correctly to confirm.');
            return;
        }

        const form = document.getElementById('delete-account-form');
        if (form) {
            form.submit();
        }
    }

    function openDeleteModal() {
        if (!acceptCheckbox.checked) {
            alert('Please confirm that you understand the account deletion terms.');
            return;
        }
        if (deleteInput.value !== 'DELETE ACCOUNT') {
            alert('Please type "DELETE ACCOUNT" correctly to confirm.');
            return;
        }

        const modal = document.getElementById('delete-account-modal');
        modal.showModal();
    }

    function closeDeleteModal() {
        const modal = document.getElementById('delete-account-modal');
        modal.close();
    }

    // Profile picture modal
    function openProfileModal() {
        const modal = document.getElementById('profile-modal');
        modal.showModal();
    }

    function closeProfileModal() {
        const modal = document.getElementById('profile-modal');
        modal.close();
    }

    // Close modal when clicking outside the image
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('profile-modal');
        if (e.target === modal) {
            modal.close();
        }

        const deleteModal = document.getElementById('delete-account-modal');
        if (e.target === deleteModal) {
            deleteModal.close();
        }
    });
</script>

<!-- Profile Picture Modal -->
<dialog id="profile-modal" class="rounded-2xl shadow-2xl backdrop:bg-black/50 backdrop:backdrop-blur-sm p-0 m-auto max-w-2xl">
    <div class="flex items-center justify-center p-8">
        <div class="relative">
            @if(data_get($user, 'profile_picture'))
                <img src="{{ asset('storage/' . data_get($user, 'profile_picture')) }}" alt="{{ $patientName }}" class="max-w-xl max-h-[600px] rounded-xl object-cover">
            @else
                <div class="w-full h-96 rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center" style="width: 450px; height: 450px;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-40 w-40 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            @endif
            <button onclick="closeProfileModal()" class="absolute -top-4 -right-4 h-12 w-12 rounded-full bg-slate-900 hover:bg-slate-800 text-white flex items-center justify-center transition shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</dialog>

<!-- Delete Account Confirmation Modal -->
<dialog id="delete-account-modal" class="w-full max-w-lg rounded-3xl p-0 shadow-2xl backdrop:bg-slate-950/40 backdrop:backdrop-blur-sm">
    <div class="overflow-hidden rounded-3xl border border-red-200 bg-white">
        <div class="border-b border-red-100 bg-red-50 px-6 py-5">
            <h3 class="text-lg font-bold text-slate-900">Confirm Account Deletion</h3>
            <p class="mt-1 text-sm text-slate-600">This is your final confirmation before your patient portal account is permanently removed.</p>
        </div>

        <div class="space-y-4 px-6 py-5">
            <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm leading-6 text-red-900">
                By continuing, you confirm that you understand this action is irreversible and that your portal access, profile image, and associated account access will be deleted.
            </div>

            <ul class="space-y-2 text-sm text-slate-700">
                <li>Your Tejada Dent portal account will be deleted immediately.</li>
                <li>You will be signed out after deletion.</li>
                <li>Records that must be retained by the clinic for compliance purposes may still be preserved internally.</li>
            </ul>
        </div>

        <div class="flex flex-col-reverse gap-3 border-t border-slate-200 px-6 py-5 sm:flex-row sm:justify-end">
            <button type="button" onclick="closeDeleteModal()"
                class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                Cancel
            </button>
            <form id="delete-account-form" action="{{ route('profile.delete') }}" method="POST">
                @csrf
                <button type="button" onclick="deleteAccount()"
                    class="inline-flex items-center justify-center rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700">
                    Yes, Delete My Account
                </button>
            </form>
        </div>
    </div>
</dialog>

@endsection
