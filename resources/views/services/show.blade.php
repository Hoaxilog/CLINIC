<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $service['title'] }} | Tejada Clinic</title>
    <x-brand.meta />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400&display=swap"
        rel="stylesheet">
    @vite('resources/css/app.css')

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }
    </style>
</head>

<body class="bg-[#f3f8fc] text-[#1a2e3b] antialiased">
    @include('components.homepage.header-section')

    <main class="px-5 py-8 md:px-10 md:py-10 xl:px-14 xl:py-12 2xl:px-20">
        <div class="mx-auto flex w-full max-w-[1640px] flex-col gap-8">
            <section class="border border-[#dbeaf5] bg-white shadow-[0_18px_45px_rgba(13,60,91,.06)]">
                <div class="grid gap-0 xl:grid-cols-[1.04fr_.96fr]">
                    <div class="border-b border-[#e3eef7] px-7 py-8 md:px-9 md:py-9 xl:border-r xl:border-b-0 xl:px-10 2xl:px-12">
                        <div class="inline-flex items-center gap-3 bg-[#eff7fd] px-4 py-2 text-[.68rem] font-bold uppercase tracking-[.18em] text-[#0086da]">
                            <span class="inline-flex h-6 w-6 items-center justify-center border border-[#dbeaf5] bg-white text-[.65rem] text-[#1a2e3b]">{{ $service['num'] }}</span>
                            Service Overview
                        </div>

                        <h1 class="mt-5 text-[clamp(2rem,4vw,3.35rem)] font-extrabold tracking-[-.04em] text-[#1a2e3b]">
                            {{ $service['title'] }}
                        </h1>
                        <p class="mt-4 max-w-4xl text-[1rem] leading-[1.9] text-[#567286]">
                            {{ $service['summary'] }}
                        </p>

                        <div class="mt-8 grid gap-4 lg:grid-cols-2">
                            <div class="border border-[#dbeaf5] bg-[#f8fbfe] px-5 py-5">
                                <p class="text-[.64rem] font-bold uppercase tracking-[.18em] text-[#0086da]">Estimated Duration</p>
                                <p class="mt-3 text-[1.05rem] font-semibold leading-[1.7] text-[#1a2e3b]">{{ $service['duration'] }}</p>
                            </div>
                            <div class="border border-[#dbeaf5] bg-[#f8fbfe] px-5 py-5">
                                <p class="text-[.64rem] font-bold uppercase tracking-[.18em] text-[#0086da]">Expected Investment</p>
                                <p class="mt-3 text-[1.05rem] font-semibold leading-[1.7] text-[#1a2e3b]">{{ $service['price'] }}</p>
                            </div>
                        </div>

                        @if (!empty($service['features']))
                            <div class="mt-8 border border-[#dbeaf5] bg-[#fbfdff] px-6 py-6">
                                <p class="text-[.64rem] font-bold uppercase tracking-[.18em] text-[#0086da]">Treatment Highlights</p>
                                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                    @foreach (array_slice($service['features'], 0, 4) as $feature)
                                        <div class="border border-[#e7f0f7] bg-white px-4 py-4">
                                            <p class="text-[.9rem] leading-[1.7] text-[#355064]">{{ $feature }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="bg-[#f8fbfe] px-7 py-8 md:px-9 md:py-9 xl:px-10 2xl:px-12">
                        <div>
                            <p class="text-[.66rem] font-bold uppercase tracking-[.18em] text-[#0086da]">Service Description</p>
                            <p class="mt-4 max-w-3xl text-[.98rem] leading-[1.9] text-[#4c6b7e]">
                                {{ $service['description'] }}
                            </p>
                        </div>

                        <div class="mt-8 border border-[#dbeaf5] bg-white px-6 py-6 shadow-[0_10px_30px_rgba(13,60,91,.04)]">
                            <p class="text-[.66rem] font-bold uppercase tracking-[.18em] text-[#0086da]">Key Information</p>
                            <div class="mt-4 space-y-4 text-[.95rem] leading-[1.85] text-[#4c6b7e]">
                                <p>After your assessment, our team will explain the findings, walk you through the recommended next steps, and help you understand what to expect from treatment and follow-up.</p>
                                <p>The exact approach may vary depending on your oral health condition, treatment goals, and the dentist's evaluation during your visit.</p>
                                <p>This service is planned with comfort, safety, and long-term oral health in mind, so recommendations may be tailored to your needs after the consultation.</p>
                            </div>
                        </div>

                        @if (!empty($service['expected_outputs']))
                            <div class="mt-8 border border-[#dbeaf5] bg-white px-6 py-6 shadow-[0_10px_30px_rgba(13,60,91,.04)]">
                                <p class="text-[.66rem] font-bold uppercase tracking-[.18em] text-[#0086da]">Expected Outcomes Preview</p>
                                <div class="mt-4 grid gap-3">
                                    @foreach (array_slice($service['expected_outputs'], 0, 3) as $output)
                                        <div class="border border-[#e5eff8] bg-[#f8fbfe] px-4 py-3">
                                            <p class="text-[.92rem] leading-[1.75] text-[#355064]">{{ $output }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            <div class="grid gap-8 2xl:grid-cols-[1.02fr_.98fr]">
                @if (!empty($service['features']))
                    <section class="border border-[#dbeaf5] bg-white px-7 py-8 shadow-[0_18px_45px_rgba(13,60,91,.05)] md:px-9 md:py-9 xl:px-10">
                        <div class="mb-7 flex items-start gap-4">
                            <div class="inline-flex h-12 w-12 shrink-0 items-center justify-center border border-[#dbeaf5] bg-[#eff7fd] text-[#0086da] shadow-sm">+</div>
                            <div>
                                <p class="text-[.68rem] font-bold uppercase tracking-[.18em] text-[#0086da]">Included Details</p>
                                <h2 class="mt-3 text-[1.8rem] font-extrabold tracking-[-.02em] text-[#1a2e3b]">What this service usually includes</h2>
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            @foreach ($service['features'] as $feature)
                                <div class="border border-[#e5eff8] bg-[#f8fbfe] px-5 py-4 transition hover:border-[#cfe3f2] hover:bg-white">
                                    <p class="text-[.95rem] leading-[1.8] text-[#355064]">{{ $feature }}</p>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if (!empty($service['expected_outputs']))
                    <section class="border border-[#dbeaf5] bg-white px-7 py-8 shadow-[0_18px_45px_rgba(13,60,91,.05)] md:px-9 md:py-9 xl:px-10">
                        <div class="mb-7 flex items-start gap-4">
                            <div class="inline-flex h-12 w-12 shrink-0 items-center justify-center border border-[#dbeaf5] bg-[#eff7fd] text-[#0086da] shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0l-3.25-3.25a1 1 0 111.414-1.414l2.543 2.543 6.543-6.543a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[.68rem] font-bold uppercase tracking-[.18em] text-[#0086da]">Expected Outputs</p>
                                <h2 class="mt-3 text-[1.8rem] font-extrabold tracking-[-.02em] text-[#1a2e3b]">Results you can expect</h2>
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2 2xl:grid-cols-1">
                            @foreach ($service['expected_outputs'] as $output)
                                <div class="border border-[#e5eff8] bg-[#f8fbfe] px-5 py-4 transition hover:border-[#cfe3f2] hover:bg-white">
                                    <p class="text-[.97rem] leading-[1.8] text-[#355064]">{{ $output }}</p>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>
        </div>
    </main>

    @include('components.homepage.footer-section')
</body>

</html>

