@extends('index')

@section('style')
    :root {
        --color-primary: #0f7ae5;
        --color-primary-dark: #0b5fc4;
        --color-text-dark: #1a202c;
        --color-text-muted: #718096;
        --color-border: #e2e8f0;
        --color-bg-light: #f7fafc;
        --color-white: #ffffff;
        --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
        --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 16px 32px rgba(0, 0, 0, 0.12);
    }

    * {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Inter', 'Helvetica Neue', sans-serif;
    }

    body {
        background: linear-gradient(180deg, #f0f7ff 0%, #ffffff 60%);
    }

    .admin-profile-gradient {
        background: linear-gradient(135deg, #e6f2ff 0%, #f5faff 100%);
    }

    .admin-profile-card {
        background: var(--color-white);
        border: 1px solid var(--color-border);
        box-shadow: var(--shadow-md);
        border-radius: 16px;
        transition: all 0.3s ease;
    }

    .admin-profile-card:hover {
        box-shadow: var(--shadow-lg);
        border-color: #d6e4ff;
    }

    .admin-profile-header {
        background: linear-gradient(135deg, #0f7ae5 0%, #0b5fc4 100%);
        color: var(--color-white);
        padding: 40px;
        border-radius: 20px;
    }

    .input-field {
        border: 1px solid var(--color-border);
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 14px;
        color: var(--color-text-dark);
        transition: all 0.2s ease;
        background: var(--color-white);
    }

    .input-field:focus {
        outline: none;
        border-color: var(--color-primary);
        box-shadow: 0 0 0 3px rgba(15, 122, 229, 0.1);
    }

    .btn-primary {
        background: var(--color-primary);
        color: var(--color-white);
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-primary:hover {
        background: var(--color-primary-dark);
        box-shadow: var(--shadow-md);
    }

    .label-text {
        font-size: 13px;
        font-weight: 600;
        color: var(--color-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 8px;
    }

    .info-grid {
        display: grid;
        gap: 16px;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }

    .info-card {
        background: var(--color-bg-light);
        padding: 16px;
        border-radius: 12px;
        border: 1px solid var(--color-border);
    }

    .info-card-label {
        font-size: 12px;
        color: var(--color-text-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .info-card-value {
        font-size: 16px;
        font-weight: 600;
        color: var(--color-text-dark);
        word-break: break-word;
    }

    .section-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--color-text-dark);
        margin: 16px 0 8px;
        letter-spacing: -0.5px;
    }

    .section-subtitle {
        font-size: 14px;
        color: var(--color-text-muted);
        line-height: 1.6;
    }

@endsection

@section('content')
    @php
        $displayName = $user->username ?: $user->email;
        $roleLabel = ucfirst($roleName ?? 'Administrator');
        $memberSince = $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('M Y') : 'N/A';
        $createdDate = $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('M d, Y') : 'N/A';
        $updatedDate = $user->updated_at ? \Carbon\Carbon::parse($user->updated_at)->format('M d, Y') : 'N/A';
        $profilePictureUrl = !empty($user->profile_picture)
            ? asset('storage/' . $user->profile_picture) . '?v=' . urlencode((string) strtotime((string) $user->updated_at))
            : null;
    @endphp

    <main id="mainContent" class="admin-profile-gradient min-h-screen ml-64 mt-14 transition-all duration-300 peer-[.collapsed]:ml-16 py-8">
        <div class="mx-auto max-w-6xl px-6 lg:px-8">

            <!-- Header Section -->
            <section class="admin-profile-header mb-8 rounded-2xl">
                <div class="flex flex-col gap-8 lg:flex-row lg:items-start lg:justify-between">
                    <!-- Left: Profile Picture & Info -->
                    <div class="flex gap-6 flex-col sm:flex-row">
                        <!-- Profile Picture -->
                        <div class="flex flex-col gap-3">
                            <form id="profile-picture-form" action="{{ route('profile.picture.upload') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="file" id="profile-picture-input" name="profile_picture"
                                    accept="image/jpeg,image/png,image/gif,image/webp" class="hidden">

                                <button type="button" id="profile-picture-trigger"
                                    class="group relative h-28 w-28 overflow-hidden rounded-2xl border-4 border-white bg-slate-300 shadow-lg hover:shadow-xl transition">
                                    @if ($profilePictureUrl)
                                        <img id="profile-pic-preview" src="{{ $profilePictureUrl }}"
                                            alt="Profile picture"
                                            class="h-full w-full object-cover transition duration-200 group-hover:scale-105">
                                    @else
                                        <div id="profile-pic-empty"
                                            class="flex h-full w-full items-center justify-center bg-slate-400 text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                    @endif
                                </button>
                            </form>
                            <span class="text-xs text-white/80 text-center">Click to update photo</span>
                        </div>

                        <!-- User Info -->
                        <div class="flex flex-col justify-center flex-1 min-w-0">
                            <p class="text-xs font-semibold text-white/70 uppercase tracking-wider">Administrator</p>
                            <h1 class="text-4xl font-bold text-white mt-2 break-words">{{ $displayName }}</h1>
                            <p class="text-white/90 text-sm mt-2">{{ $user->email ?? 'No email found' }}</p>
                            <div class="mt-4 flex gap-2 flex-wrap">
                                <span class="inline-block px-3 py-1 rounded-full bg-white/20 text-white text-xs font-semibold">
                                    {{ $roleLabel }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Stats -->
                    <div class="info-grid text-white/90">
                        <div class="text-center sm:text-left">
                            <p class="text-xs font-semibold text-white/70 uppercase tracking-wider">Member Since</p>
                            <p class="text-xl font-bold mt-1">{{ $memberSince }}</p>
                        </div>
                        <div class="text-center sm:text-left">
                            <p class="text-xs font-semibold text-white/70 uppercase tracking-wider">Created</p>
                            <p class="text-xl font-bold mt-1">{{ $createdDate }}</p>
                        </div>
                        <div class="text-center sm:text-left">
                            <p class="text-xs font-semibold text-white/70 uppercase tracking-wider">Last Updated</p>
                            <p class="text-xl font-bold mt-1">{{ $updatedDate }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Messages -->
            @if (session('success'))
                <div class="mb-6 rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    ✓ {{ session('success') }}
                </div>
            @endif

            @if (session('failed'))
                <div class="mb-6 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800">
                    ✕ {{ session('failed') }}
                </div>
            @endif

            @if ($errors->has('profile_picture'))
                <div class="mb-6 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800">
                    ✕ {{ $errors->first('profile_picture') }}
                </div>
            @endif

            <!-- Main Content Grid -->
            <div class="grid gap-8 lg:grid-cols-3">

                <!-- Left Column: Profile Information -->
                <article class="admin-profile-card p-8 lg:col-span-2">
                    <div class="mb-8">
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">Account Details</p>
                        <h2 class="section-title">Profile Information</h2>
                        <p class="section-subtitle">Update your account details and professional information.</p>
                    </div>

                    <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <!-- Username & Contact -->
                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <label class="label-text">Username</label>
                                <input type="text" name="username" value="{{ old('username', $user->username) }}"
                                    class="input-field w-full"
                                    placeholder="Enter your username">
                                @error('username')
                                    <span class="mt-1 block text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="label-text">Contact Number</label>
                                <input type="text" name="contact" value="{{ old('contact', data_get($user, 'contact')) }}"
                                    class="input-field w-full"
                                    placeholder="0900000000 or office number">
                                @error('contact')
                                    <span class="mt-1 block text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Position -->
                        <div>
                            <label class="label-text">Position / Title</label>
                            <input type="text" name="position" value="{{ old('position', data_get($user, 'position')) }}"
                                class="input-field w-full"
                                placeholder="e.g. Administrator, Dentist, Clinic Staff">
                            @error('position')
                                <span class="mt-1 block text-xs text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Read-Only Info -->
                        <div class="info-grid pt-4 border-t border-slate-200">
                            <div class="info-card">
                                <div class="info-card-label">Account Email</div>
                                <div class="info-card-value">{{ $user->email ?? 'No email found' }}</div>
                            </div>
                            <div class="info-card">
                                <div class="info-card-label">Role</div>
                                <div class="info-card-value">{{ $roleLabel }}</div>
                            </div>
                            <div class="info-card">
                                <div class="info-card-label">Created</div>
                                <div class="info-card-value">{{ $createdDate }}</div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit" class="btn-primary">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </article>

                <!-- Right Column: Security Settings -->
                <article class="admin-profile-card p-8">
                    <div class="mb-8">
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">Security</p>
                        <h2 class="section-title">Security Settings</h2>
                        <p class="section-subtitle">Manage account access and password.</p>
                    </div>

                    @if (!empty($isGoogleUser))
                        <div class="rounded-lg border border-blue-300 bg-blue-50 p-4 mb-6">
                            <p class="text-sm leading-6 text-blue-900">
                                This account uses <strong>Google Login</strong>. Send a password setup link to your registered email when needed.
                            </p>
                            <form action="{{ route('profile.password.reset-link') }}" method="POST" class="mt-4">
                                @csrf
                                <button type="submit" class="btn-primary w-full text-center">
                                    Send Reset Link
                                </button>
                            </form>
                        </div>
                    @else
                        <form action="{{ route('profile.password') }}" method="POST" class="space-y-5">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="label-text">Current Password</label>
                                <input type="password" name="current_password"
                                    class="input-field w-full"
                                    placeholder="Enter current password">
                                @error('current_password')
                                    <span class="mt-1 block text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="label-text">New Password</label>
                                <input type="password" name="password"
                                    class="input-field w-full"
                                    placeholder="Enter new password">
                                @error('password')
                                    <span class="mt-1 block text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="label-text">Confirm New Password</label>
                                <input type="password" name="password_confirmation"
                                    class="input-field w-full"
                                    placeholder="Confirm new password">
                            </div>

                            <button type="submit" class="btn-primary w-full text-center">
                                Update Password
                            </button>
                        </form>
                    @endif
                </article>

            </div>

            <!-- Photo Upload Section -->
            <section class="admin-profile-card p-8 mt-8">
                <div class="mb-8">
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">Profile Picture</p>
                    <h2 class="section-title">Upload & Manage Photos</h2>
                    <p class="section-subtitle">Update or view your profile picture. Supported formats: JPEG, PNG, GIF, WebP. Maximum size: 10MB.</p>
                </div>

                <div class="grid gap-6 lg:grid-cols-3 items-center">
                    <label for="profile-picture-input" id="profile-dropzone"
                        class="flex h-28 items-center justify-center rounded-lg border-2 border-dashed border-slate-300 bg-slate-50 cursor-pointer hover:bg-blue-50 hover:border-blue-400 transition">
                        <div class="text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto text-slate-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span class="text-sm font-medium text-slate-600">Select or drag file</span>
                        </div>
                    </label>

                    <div id="file-selected-state" class="hidden text-center py-4">
                        <p class="text-sm font-medium text-slate-700">Selected: <span id="selected-file-name" class="font-bold text-blue-600"></span></p>
                    </div>

                    <div>
                        @if ($profilePictureUrl)
                            <a href="{{ $profilePictureUrl }}" target="_blank" rel="noopener noreferrer"
                                class="btn-primary w-full text-center block">
                                View Current Photo
                            </a>
                        @else
                            <button type="button" id="view-image-trigger" class="btn-primary w-full opacity-50 cursor-not-allowed">
                                No Photo
                            </button>
                        @endif
                    </div>
                </div>
            </section>

        </div>
    </main>

    <script>
        // Profile picture change handler
        document.getElementById('profile-picture-input').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                document.getElementById('selected-file-name').textContent = file.name;
                document.getElementById('file-selected-state').classList.remove('hidden');

                // Preview
                const reader = new FileReader();
                reader.onload = function(event) {
                    const picDisplay = document.getElementById('profile-pic-preview');
                    const picIcon = document.getElementById('profile-pic-empty');

                    if (picDisplay) {
                        picDisplay.src = event.target.result;
                    } else if (picIcon) {
                        const img = document.createElement('img');
                        img.id = 'profile-pic-preview';
                        img.src = event.target.result;
                        img.className = 'h-full w-full object-cover';
                        picIcon.parentElement.replaceChild(img, picIcon);
                    }
                };
                reader.readAsDataURL(file);

                // Submit form
                setTimeout(() => {
                    document.getElementById('profile-picture-form').submit();
                }, 500);
            }
        });

        // Trigger file input
        document.getElementById('profile-picture-trigger').addEventListener('click', function() {
            document.getElementById('profile-picture-input').click();
        });

        // Drag and drop
        const dropzone = document.getElementById('profile-dropzone');
        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('border-blue-400', 'bg-blue-50');
        });
        dropzone.addEventListener('dragleave', () => {
            dropzone.classList.remove('border-blue-400', 'bg-blue-50');
        });
    </script>
@endsection
