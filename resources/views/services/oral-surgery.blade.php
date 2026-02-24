<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <title>{{ $service['title'] }} - Tejada Clinic</title>
    @vite('resources/css/app.css')
</head>

<body class="font-['Roboto']">
    @include('components.homepage.header-section')

    <main class="pt-20">
        <!-- Hero Section -->
        <section class="bg-[#FCFCFC] py-16 lg:py-28 border-b-2 border-black">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:grid lg:grid-cols-2 lg:gap-16 items-center">
                    <div class="text-left">
                        <div class="inline-block border-2 border-black px-3 py-1 mb-8 bg-white">
                            <span class="text-xs font-bold uppercase tracking-widest text-black">Our Services</span>
                        </div>

                        <h1 class="text-5xl md:text-7xl font-bold text-black leading-[1.1] mb-8 tracking-tight">
                            {{ $service['title'] }}
                        </h1>

                        <p class="text-lg text-gray-700 max-w-lg mb-12 leading-relaxed font-medium">
                            {{ $service['description'] }}
                        </p>

                        <div class="flex gap-4 flex-col sm:flex-row">
                            <a href="/book"
                                class="inline-flex items-center justify-center min-w-[200px] px-8 py-4 bg-[#0789da] text-white text-base font-bold border-2 border-black shadow-[5px_5px_0px_0px_rgba(0,0,0,0.3)] hover:shadow-none hover:translate-x-[3px] hover:translate-y-[3px] transition-all">
                                Book Now <span class="ml-2">→</span>
                            </a>
                            <a href="/#services"
                                class="inline-flex items-center justify-center min-w-[200px] px-8 py-4 bg-white text-black text-base font-bold border-2 border-black shadow-[5px_5px_0px_0px_rgba(0,0,0,0.3)] hover:shadow-none hover:translate-x-[3px] hover:translate-y-[3px] transition-all">
                                View More Services <span class="ml-2">←</span>
                            </a>
                        </div>
                    </div>

                    <div class="hidden lg:block relative w-full h-[500px] mt-10 lg:mt-0">
                        <img src="{{ $service['image'] }}" alt="{{ $service['title'] }}"
                            class="w-full h-full object-cover border-2 border-black shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]">
                    </div>
                </div>
            </div>
        </section>

        <!-- Service Details Section -->
        <section class="bg-white py-16 lg:py-24 border-b-2 border-black">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:grid lg:grid-cols-2 lg:gap-16 items-start">
                    <div class="order-2 lg:order-1">
                        <h2 class="text-4xl md:text-5xl font-black text-black leading-tight mb-8 uppercase tracking-tighter">
                            What's Included
                        </h2>

                        <ul class="space-y-4">
                            @foreach ($service['details'] as $detail)
                                <li class="flex items-start gap-4 p-6 bg-[#FCFCFC] border-2 border-black">
                                    <div
                                        class="flex-shrink-0 w-6 h-6 bg-[#0789da] text-white flex items-center justify-center rounded-full">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <p class="font-bold text-black">{{ $detail }}</p>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="order-1 lg:order-2 mb-12 lg:mb-0">
                        <div class="relative inline-block w-full">
                            <div class="absolute inset-0 bg-[#0789da] border-2 border-black translate-x-4 translate-y-4">
                            </div>

                            <div class="relative bg-white border-2 border-black p-10 md:p-14">
                                <h3 class="text-2xl font-black mb-6 uppercase tracking-tight">Key Benefits</h3>

                                <ul class="space-y-6">
                                    @foreach ($service['benefits'] as $benefit)
                                        <div class="flex items-start gap-4">
                                            <div
                                                class="flex-shrink-0 w-8 h-8 bg-[#0789da] text-white flex items-center justify-center font-bold text-sm">
                                                ✓
                                            </div>
                                            <p class="font-bold text-black">{{ $benefit }}</p>
                                        </div>
                                    @endforeach
                                </ul>

                                <div class="mt-10 border-t-2 border-black pt-8">
                                    <div class="space-y-4">
                                        <div>
                                            <p class="text-xs font-bold uppercase text-gray-500 mb-2">Estimated Duration</p>
                                            <p class="text-2xl font-black text-[#0789da]">{{ $service['duration'] }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold uppercase text-gray-500 mb-2">Price Range</p>
                                            <p class="text-2xl font-black text-[#0789da]">{{ $service['price'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="bg-[#FCFCFC] py-16 lg:py-24 border-b-2 border-black">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="relative">
                    <div class="absolute inset-0 bg-[#0789da] border-2 border-black translate-x-3 translate-y-3"></div>
                    <div class="relative bg-white border-2 border-black p-10 md:p-14 text-center">
                        <h3 class="text-3xl md:text-4xl font-black text-black mb-4 uppercase tracking-tighter">
                            Ready to Transform Your Smile?
                        </h3>
                        <p class="text-gray-700 mb-10 font-medium text-lg max-w-2xl mx-auto">
                            Schedule your appointment today and let our professional team help you achieve the smile you deserve.
                        </p>
                        <a href="/book"
                            class="inline-flex items-center bg-[#0789da] text-white font-black px-10 py-4 border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[3px] hover:translate-y-[3px] transition-all uppercase tracking-widest text-sm">
                            Book Your Appointment <span class="ml-3">→</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        @include('components.homepage.footer-section')
    </main>
</body>

</html>
