<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Account | Tejada Clinic</title>
    <meta name="theme-color" content="#0086DA">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400&display=swap"
        rel="stylesheet">
    @vite('resources/css/app.css')
</head>

<body class="min-h-screen bg-[#f6fafd] text-[#1a2e3b] antialiased" style="font-family: 'Montserrat', sans-serif;">
    @include('components.homepage.header-section', ['patientMinimalHeader' => true])

    <main class="overflow-x-hidden">
        <section class="border-b border-[#d9ecf9] bg-[#0086da] px-6 py-14 md:px-12 xl:px-20">
            <div class="mx-auto grid w-full max-w-[1400px] gap-8 lg:grid-cols-[1.1fr_.9fr] lg:items-end">
                <div class="max-w-[720px]">
                    <div
                        class="mb-4 inline-flex items-center gap-[10px] text-[.63rem] font-bold uppercase tracking-[.22em] text-white/75">
                        <span class="block h-[2px] w-[22px] bg-white/75"></span>
                        Google Account Setup
                    </div>
                    <h1 class="text-[clamp(1.8rem,4vw,3.1rem)] font-extrabold tracking-[-.03em] text-white">
                        Complete your account details before entering the dashboard.
                    </h1>
                    <p class="mt-5 max-w-[560px] text-[.92rem] leading-[1.9] text-white/78">
                        We only need the basic details stored on your user account: name, contact number, and birth
                        date. After this one-time step, you can move freely around your patient dashboard.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-[2px] border border-white/15 bg-white/15">
                    <div class="bg-white/10 px-5 py-5 backdrop-blur-sm">
                        <div class="text-[.58rem] font-bold uppercase tracking-[.18em] text-white/65">Login Email</div>
                        <div class="mt-2 text-[.85rem] font-semibold text-white">{{ $user->email }}</div>
                    </div>
                    <div class="bg-white/10 px-5 py-5 backdrop-blur-sm">
                        <div class="text-[.58rem] font-bold uppercase tracking-[.18em] text-white/65">Sign-in Method</div>
                        <div class="mt-2 text-[.85rem] font-semibold text-white">Google</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="px-6 py-10 md:px-12 md:py-14 xl:px-20">
            <div class="mx-auto grid w-full max-w-[1400px] gap-8 lg:grid-cols-[minmax(0,1.05fr)_340px]">
                <div class="border border-[#e4eff8] bg-white shadow-[0_20px_50px_rgba(0,134,218,.08)]">
                    <div class="border-b border-[#e4eff8] px-6 py-5 md:px-8">
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center bg-[#e8f4fc] text-[#0086da]">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-[.58rem] font-bold uppercase tracking-[.18em] text-[#0086da]">User Account</div>
                                <h2 class="text-[1rem] font-extrabold tracking-[-.02em] text-[#1a2e3b]">Account Details Form</h2>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('patient.complete-profile.store') }}" class="px-6 py-6 md:px-8 md:py-8">
                        @csrf

                        @if (session('failed'))
                            <div class="mb-6 border border-red-200 bg-red-50 px-4 py-3 text-[.8rem] font-semibold text-red-700">
                                {{ session('failed') }}
                            </div>
                        @endif

                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div class="min-w-0 md:col-span-2">
                                <label for="email_display" class="mb-2 block text-[.68rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e]">
                                    Email Address
                                </label>
                                <input id="email_display" type="text" value="{{ $user->email }}" readonly
                                    class="w-full cursor-not-allowed border border-[#e4eff8] bg-[#f6fafd] px-4 py-3 text-[.88rem] text-[#1a2e3b] outline-none">
                                <p class="mt-2 text-[.75rem] font-semibold text-[#7a9db5]">This login email is connected to your Google account and cannot be changed here.</p>
                            </div>

                            <div class="min-w-0">
                                <label for="first_name" class="mb-2 block text-[.68rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e]">
                                    First Name <span class="text-red-500">*</span>
                                </label>
                                <input id="first_name" name="first_name" type="text"
                                    value="{{ old('first_name', $user->first_name) }}" placeholder="Juan"
                                    class="w-full border border-[#d4e8f5] bg-white px-4 py-3 text-[.88rem] text-[#1a2e3b] placeholder:text-[#9bbdd0] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#cde8fb] @error('first_name') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror">
                                @error('first_name')
                                    <p class="mt-2 text-[.75rem] font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="min-w-0">
                                <label for="last_name" class="mb-2 block text-[.68rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e]">
                                    Last Name <span class="text-red-500">*</span>
                                </label>
                                <input id="last_name" name="last_name" type="text"
                                    value="{{ old('last_name', $user->last_name) }}" placeholder="Dela Cruz"
                                    class="w-full border border-[#d4e8f5] bg-white px-4 py-3 text-[.88rem] text-[#1a2e3b] placeholder:text-[#9bbdd0] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#cde8fb] @error('last_name') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror">
                                @error('last_name')
                                    <p class="mt-2 text-[.75rem] font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            @if ($hasMiddleNameColumn)
                                <div class="min-w-0">
                                    <label for="middle_name" class="mb-2 block text-[.68rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e]">
                                        Middle Name
                                    </label>
                                    <input id="middle_name" name="middle_name" type="text"
                                        value="{{ old('middle_name', $user->middle_name) }}" placeholder="Santos"
                                        class="w-full border border-[#d4e8f5] bg-white px-4 py-3 text-[.88rem] text-[#1a2e3b] placeholder:text-[#9bbdd0] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#cde8fb] @error('middle_name') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror">
                                    @error('middle_name')
                                        <p class="mt-2 text-[.75rem] font-semibold text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            @if ($hasBirthDateColumn)
                                <div class="min-w-0">
                                    <label for="birth_date" class="mb-2 block text-[.68rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e]">
                                        Birth Date <span class="text-red-500">*</span>
                                    </label>
                                    <input id="birth_date" name="birth_date" type="date"
                                        value="{{ old('birth_date', optional($user->birth_date)->format('Y-m-d')) }}"
                                        max="{{ now()->subDay()->format('Y-m-d') }}"
                                        class="w-full border border-[#d4e8f5] bg-white px-4 py-3 text-[.88rem] text-[#1a2e3b] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#cde8fb] @error('birth_date') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror">
                                    @error('birth_date')
                                        <p class="mt-2 text-[.75rem] font-semibold text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            <div class="min-w-0 md:col-span-2">
                                <label for="mobile_number" class="mb-2 block text-[.68rem] font-bold uppercase tracking-[.14em] text-[#3d5a6e]">
                                    Contact Number <span class="text-red-500">*</span>
                                </label>
                                <div class="flex">
                                    <span class="inline-flex items-center border border-r-0 border-[#d4e8f5] bg-[#f0f8fe] px-4 text-[.84rem] font-semibold text-[#3d5a6e]">
                                        +63
                                    </span>
                                    <input id="mobile_number" name="mobile_number" type="text" inputmode="numeric"
                                        maxlength="10" oninput="this.value = this.value.replace(/\D/g, '').replace(/^0+/, '').slice(0, 10)"
                                        value="{{ old('mobile_number', $user->mobile_number) }}" placeholder="9171234567"
                                        class="w-full min-w-0 border border-[#d4e8f5] bg-white px-4 py-3 text-[.88rem] text-[#1a2e3b] placeholder:text-[#9bbdd0] outline-none transition focus:border-[#0086da] focus:ring-2 focus:ring-[#cde8fb] @error('mobile_number') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror">
                                </div>
                                @error('mobile_number')
                                    <p class="mt-2 text-[.75rem] font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-8 flex flex-wrap items-center justify-between gap-4 border-t border-[#e4eff8] pt-6">
                            <p class="text-[.8rem] leading-[1.7] text-[#6b8799]">
                                This updates your `users` account record only. It does not edit clinic patient records.
                            </p>
                            <button type="submit"
                                class="inline-flex items-center gap-[9px] bg-[#0086da] px-7 py-[14px] text-[.72rem] font-bold uppercase tracking-[.1em] text-white transition hover:bg-[#006ab0]">
                                Save And Continue
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="square">
                                    <path d="M5 12h14M12 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                <aside class="space-y-6">
                    <div class="border border-[#e4eff8] bg-white">
                        <div class="border-b border-[#e4eff8] px-5 py-4">
                            <div class="text-[.58rem] font-bold uppercase tracking-[.18em] text-[#0086da]">Checklist</div>
                            <div class="mt-1 text-[.95rem] font-extrabold tracking-[-.02em] text-[#1a2e3b]">User account fields</div>
                        </div>
                        <div class="space-y-3 px-5 py-5">
                            @php
                                $accountFields = ['First name', 'Last name'];
                                if ($hasMiddleNameColumn) {
                                    $accountFields[] = 'Middle name if any';
                                }
                                if ($hasBirthDateColumn) {
                                    $accountFields[] = 'Birth date';
                                }
                                $accountFields[] = 'Contact number';
                            @endphp
                            @foreach ($accountFields as $item)
                                <div class="flex items-start gap-3 border border-[#e8f2fa] bg-[#f8fbfe] px-4 py-3">
                                    <span class="mt-1 inline-block h-2.5 w-2.5 shrink-0 bg-[#0086da]"></span>
                                    <span class="text-[.8rem] font-semibold text-[#1a2e3b]">{{ $item }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="border border-[#e4eff8] bg-white">
                        <div class="px-5 py-5">
                            <div class="mb-3 text-[.58rem] font-bold uppercase tracking-[.18em] text-[#0086da]">After This</div>
                            <p class="text-[.82rem] leading-[1.8] text-[#3d5a6e]">
                                Once submitted, your Google-linked user account will be marked complete and you will be
                                redirected straight to the patient dashboard.
                            </p>
                        </div>
                    </div>
                </aside>
            </div>
        </section>
    </main>

    @include('components.homepage.footer-section')
</body>

</html>
