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
    <title>Tejada Clinic</title>
    @vite('resources/css/app.css')
</head>

<body class=" font-['Roboto']">
    @include('components.homepage.header-section')

    <main class="pt-20">
        <section class="bg-[#FCFCFC] py-16 lg:py-28">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:grid lg:grid-cols-2 lg:gap-16 items-center">

                    <div class="text-left">
                        <div class="inline-block border-2 border-black px-3 py-1 mb-8 bg-white">
                            <span class="text-xs font-bold uppercase tracking-widest text-black">World-Class
                                Dentistry</span>
                        </div>

                        <h1 class="text-5xl md:text-7xl font-bold text-black leading-[1.1] mb-8 tracking-tight">
                            Achieve Your <br>
                            <span class="underline decoration-4 underline-offset-8 decoration-black">Perfect
                                Smile</span>
                        </h1>

                        <p class="text-lg text-gray-700 max-w-lg mb-12 leading-relaxed font-medium">
                            Experience personalized dental care with cutting-edge technology and a compassionate team.
                            Your oral health is our passion.
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-sm lg:max-w-none lg:flex lg:flex-row">
                            <a href="/register"
                                class="inline-flex items-center justify-center min-w-[180px] px-8 py-4 bg-black text-white text-base font-bold border-2 border-black shadow-[5px_5px_0px_0px_rgba(0,0,0,0.3)] hover:shadow-none hover:translate-x-[3px] hover:translate-y-[3px] transition-all">
                                Join Our Platform <span class="ml-2">→</span>
                            </a>
                        </div>
                    </div>

                    <div class="hidden lg:block relative w-full h-[500px]">
                        <div id="stack-container" class="relative w-full h-full cursor-pointer" onclick="nextPage()">

                            <div
                                class="stack-card absolute inset-0 bg-white border-2 border-black shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] transition-all duration-500 ease-in-out z-10">
                                <img src="https://images.unsplash.com/photo-1629909613654-28e377c37b09?auto=format&fit=crop&q=80"
                                    class="w-full h-full object-cover" alt="Clinic 1">
                            </div>

                            <div
                                class="stack-card absolute inset-0 bg-white border-2 border-black shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] transition-all duration-500 ease-in-out z-20">
                                <img src="https://images.unsplash.com/photo-1598256989800-fe5f95da9787?auto=format&fit=crop&q=80"
                                    class="w-full h-full object-cover" alt="Clinic 2">
                            </div>

                            <div
                                class="stack-card absolute inset-0 bg-white border-2 border-black shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] transition-all duration-500 ease-in-out z-30">
                                <img src="https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?auto=format&fit=crop&q=80"
                                    class="w-full h-full object-cover hover:grayscale-0" alt="Clinic 3">
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-5">
                            <p class="text-xs font-black uppercase tracking-widest text-gray-400 font-mono">View our
                                facility</p>
                            <button onclick="nextPage()"
                                class="group flex items-center gap-2 text-sm font-black hover:text-[#0789da] transition-colors">
                                <span class="bg-black text-white p-1 group-hover:bg-[#0789da] transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 5l7 7-7 7" stroke-width="3" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <section id="about" class="bg-white py-16 lg:py-24 border-t-2 border-black">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-16 text-center lg:text-left">
                    <div
                        class="inline-block border-2 border-black px-3 py-1 mb-4 bg-white shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                        <span class="text-xs font-bold uppercase tracking-widest text-black">About Us</span>
                    </div>
                </div>
                <div class="lg:grid lg:grid-cols-2 lg:gap-16 items-start">
                    <div class="order-2 lg:order-1 text-left">
                        <h2
                            class="text-4xl md:text-7xl font-black text-black leading-none mb-8 uppercase tracking-tighter">
                            Redefining the <br>
                            <span class="text-[#0789da]">Dental Experience</span>
                        </h2>

                        <div class="space-y-6 text-lg text-gray-700 font-medium leading-relaxed">
                            <p>
                                At <span class="font-bold text-black underline decoration-2 underline-offset-4">Tejada
                                    Dental Clinic</span>, we believe that dental care should be transparent, accessible,
                                and completely stress-free. We've combined decades of clinical expertise with modern
                                technology to create a smarter clinic.
                            </p>
                            <p>
                                Whether you're here for a routine check-up or a complex restorative procedure, our team
                                is dedicated to providing personalized care that fits your lifestyle and your budget.
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-10">
                            <div class="p-6 bg-white border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                                <p class="text-3xl font-black text-[#0789da]">10+</p>
                                <p class="text-sm font-bold uppercase text-black">Years Experience</p>
                            </div>
                            <div class="p-6 bg-white border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                                <p class="text-3xl font-black text-[#0789da]">5k+</p>
                                <p class="text-sm font-bold uppercase text-black">Happy Patients</p>
                            </div>
                        </div>
                    </div>

                    <div class="order-1 lg:order-2 mb-12 lg:mb-0">
                        <div class="relative inline-block w-full">
                            <div
                                class="absolute inset-0 bg-[#0789da] border-2 border-black translate-x-4 translate-y-4">
                            </div>

                            <div class="relative bg-white border-2 border-black  md:p-20 p-10">
                                <h3 class="text-2xl font-black mb-4 uppercase tracking-tight">Why Choose Us?</h3>
                                <ul class="space-y-4">
                                    <li class="flex items-start gap-3">
                                        <span
                                            class="flex-shrink-0 w-6 h-6 bg-[#0789da] text-white flex items-center justify-center text-xs font-bold">1</span>
                                        <p class="font-bold text-black">Modern Equipment & Digital Records</p>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <span
                                            class="flex-shrink-0 w-6 h-6 bg-[#0789da] text-white flex items-center justify-center text-xs font-bold">2</span>
                                        <p class="font-bold text-black">Highly Experienced Dental Surgeons</p>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <span
                                            class="flex-shrink-0 w-6 h-6 bg-[#0789da] text-white flex items-center justify-center text-xs font-bold">3</span>
                                        <p class="font-bold text-black">Seamless Online Appointment System</p>
                                    </li>
                                </ul>

                                <div class="mt-8 border-t-2 border-black pt-6">
                                    <p class="italic font-medium text-gray-600">"We don't just treat teeth; we care for
                                        the people behind the smiles."</p>
                                    <p class="mt-2 font-black uppercase text-sm">— Dr. Shiela, Lead Dentist</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>


        <section id="services" class="bg-[#FCFCFC] py-16 lg:py-24 border-t-2 border-black">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="mb-16 text-center lg:text-left">
                    <div
                        class="inline-block border-2 border-black px-3 py-1 mb-4 bg-white shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                        <span class="text-xs font-bold uppercase tracking-widest text-black">What We Do</span>
                    </div>
                    <h2 class="text-5xl md:text-7xl font-black text-black uppercase tracking-tighter">
                        Our Dental <span class="text-[#0789da]">Services</span>
                    </h2>
                    <p class="mt-4 text-lg font-medium text-gray-600 max-w-2xl">
                        Comprehensive care designed for your comfort. From routine maintenance to your cosmetic
                        transformations.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">

                    <div
                        class="group bg-white border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all overflow-hidden flex flex-col">
                        <div class="h-48 border-b-4 border-black overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1606811841689-23dfddce3e95?auto=format&fit=crop&q=80"
                                alt="General Checkup" class="w-full h-full object-cover">
                        </div>
                        <div class="p-6 flex flex-col flex-grow">
                            <h3 class="text-2xl font-black text-black mb-3">General Checkup</h3>
                            <p class="text-gray-600 font-medium text-sm mb-6 flex-grow leading-relaxed">
                                Routine examination with a professional dentist to keep your oral health at peak
                                condition.
                            </p>
                            <div class="flex items-center justify-between mt-auto">
                                <a href="/services/general-checkup"
                                    class="font-black text-sm uppercase underline decoration-2 underline-offset-4 hover:text-[#0789da] transition-colors">Learn
                                    More</a>

                            </div>
                        </div>
                    </div>

                    <div
                        class="group bg-white border-2 border-black  shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all overflow-hidden flex flex-col">
                        <div class="h-48 border-b-4 border-black overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1598256989800-fe5f95da9787?auto=format&fit=crop&q=80"
                                alt="Orthodontics" class="w-full h-full object-cover">
                        </div>
                        <div class="p-6 flex flex-col flex-grow">
                            <h3 class="text-2xl font-black text-black mb-3">Orthodontics</h3>
                            <p class="text-gray-600 font-medium text-sm mb-6 flex-grow leading-relaxed">
                                Modern braces and aligners to correct your smile and improve long-term dental health.
                            </p>
                            <div class="flex items-center justify-between mt-auto">
                                <a href="/services/orthodontics"
                                    class="font-black text-sm uppercase underline decoration-2 underline-offset-4 hover:text-[#0789da] transition-colors">Learn
                                    More</a>
                            </div>
                        </div>
                    </div>

                    <div
                        class="group bg-white border-2 border-black  shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all overflow-hidden flex flex-col">
                        <div class="h-48 border-b-4 border-black overflow-hidden">
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSc_b0xAG8cB0EQbHARkpSNVrA9ZzmqX42kvw&s"
                                alt="Teeth Whitening" class="w-full h-full object-cover">
                        </div>
                        <div class="p-6 flex flex-col flex-grow">
                            <h3 class="text-2xl font-black text-black mb-3">Teeth Whitening</h3>
                            <p class="text-gray-600 font-medium text-sm mb-6 flex-grow leading-relaxed">
                                Advance teeth whitening to brighten your smile up to 8 shades in a single session.
                            </p>
                            <div class="flex items-center justify-between mt-auto">
                                <a href="/services/teeth-whitening"
                                    class="font-black text-sm uppercase underline decoration-2 underline-offset-4 hover:text-[#0789da] transition-colors">Learn
                                    More</a>
                            </div>
                        </div>
                    </div>

                    <div
                        class="group bg-white border-2 border-black  shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all overflow-hidden flex flex-col">
                        <div class="h-48 border-b-4 border-black overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?auto=format&fit=crop&q=80"
                                alt="Oral Surgery" class="w-full h-full object-cover">
                        </div>
                        <div class="p-6 flex flex-col flex-grow">
                            <h3 class="text-2xl font-black text-black mb-3">Oral Surgery</h3>
                            <p class="text-gray-600 font-medium text-sm mb-6 flex-grow leading-relaxed">
                                Safe and precise procedures for impacted teeth, severe decay, and advanced oral health concerns.
                            </p>
                            <div class="flex items-center justify-between mt-auto">
                                <a href="/services/oral-surgery"
                                    class="font-black text-sm uppercase underline decoration-2 underline-offset-4 hover:text-[#0789da] transition-colors">Learn
                                    More</a>
                            </div>
                        </div>
                    </div>


                </div>

                <div class="mt-20 relative">
                    <div class="absolute inset-0 bg-[#0789da] border-2 border-black translate-x-3 translate-y-3"></div>
                    <div class="relative bg-white border-2 border-black p-10 md:p-14 text-center">
                        <h3 class="text-3xl md:text-4xl font-black text-black mb-4 uppercase tracking-tighter">Need a
                            custom treatment plan?</h3>
                        <p class="text-gray-700 mb-10 font-medium text-lg max-w-2xl mx-auto">
                            Tell us your goals and we'll recommend the best options for your smile.
                        </p>
                        <a href="/book"
                            class="inline-flex items-center bg-[#0789da] text-white font-black px-10 py-4 border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[3px] hover:translate-y-[3px] transition-all uppercase tracking-widest text-sm">
                            Get a Consultation <span class="ml-3">→</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section id="contact" class="bg-[#FCFCFC] py-16 lg:py-24 border-t-2 border-black">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="mb-16 text-center lg:text-left">
                    <div
                        class="inline-block border-2 border-black px-3 py-1 mb-4 bg-white shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                        <span class="text-xs font-bold uppercase tracking-widest text-black">Get In Touch</span>
                    </div>
                    <h2 class="text-5xl md:text-7xl font-black text-black uppercase tracking-tighter">
                        Visit our <span class="text-[#0789da]">Clinic</span>
                    </h2>
                </div>

                <div class="lg:grid lg:grid-cols-2 lg:gap-16 items-start">

                    <div class="space-y-8">
                        <p class="text-xl font-bold text-gray-700 leading-relaxed max-w-md">
                            Ready to book your visit? Use our online system or reach out through any of these channels.
                        </p>

                        <div class="space-y-6">
                            <div
                                class="group flex items-start gap-4 p-6 bg-white border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[3px] hover:translate-y-[3px] transition-all">
                                <div
                                    class="bg-[#0789da] border-2 border-black p-2 text-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-black uppercase text-sm tracking-widest mb-1">Clinic Address</h4>
                                    <p class="font-bold text-gray-600">251 Commonwealth Ave, Diliman, Quezon City</p>
                                </div>
                            </div>

                            <a href="https://facebook.com/yourclinicpage" target="_blank"
                                class="group flex items-start gap-4 p-6 bg-white border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[3px] hover:translate-y-[3px] transition-all">
                                <div
                                    class="bg-[#0789da] border-2 border-black p-2 text-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-black uppercase text-sm tracking-widest mb-1">Facebook Page</h4>
                                    <p class="font-bold text-gray-600">facebook.com/TejaDentClinic</p>
                                </div>
                            </a>

                            <div
                                class="group flex items-start gap-4 p-6 bg-white border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[3px] hover:translate-y-[3px] transition-all">
                                <div
                                    class="bg-[#0789da] border-2 border-black p-2 text-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-black uppercase text-sm tracking-widest mb-1">Call / WhatsApp</h4>
                                    <p class="font-bold text-gray-600">+63 912 345 6789</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 lg:mt-0 relative">
                        <div class="absolute inset-0 bg-[#0789da] border-2 border-black translate-x-4 translate-y-4">
                        </div>
                        <div
                            class="relative bg-white border-2 border-black overflow-hidden aspect-square md:aspect-video lg:aspect-square">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3860.0526364003323!2d121.07357487578508!3d14.668435175138128!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b759ceee4d07%3A0x8927894f17a3b774!2sDiliman%20Doctors%20Hospital!5e0!3m2!1sen!2sph!4v1700000000000!5m2!1sen!2sph"
                                class="w-full h-full " style="border:0;" allowfullscreen="" loading="lazy">
                            </iframe>
                            <div
                                class="absolute top-4 right-4 bg-white border-2 border-black px-4 py-2 font-black text-sm shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                                FIND US HERE
                            </div>
                        </div>

                        <div
                            class="absolute -bottom-6 -left-6 bg-white border-2 border-black p-5 shadow-[4px_4px_0px_0px_rgba(7,137,218,1)]">
                            <h5 class="text-black font-black text-xs uppercase mb-3 border-b border-gray-700 pb-1">
                                Business Hours</h5>
                            <ul class="text-black text-[10px] font-bold space-y-1">
                                <li class="flex justify-between gap-4"><span>Mon-Fri:</span> <span>9:00 AM - 6:00
                                        PM</span></li>
                                <li class="flex justify-between gap-4"><span>Saturday:</span> <span>9:00 AM - 6:00
                                        PM</span></li>
                                <li class="flex justify-between gap-4 text-blue-400"><span>Sunday:</span>
                                    <span>Closed</span>
                                </li>
                                <li class="flex justify-between gap-4 text-blue-400"><span>Tuesday:</span>
                                    <span>Closed</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <section id="registration-info" class="bg-white py-16 lg:py-24 border-t-2 border-black">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="lg:grid lg:grid-cols-2 lg:gap-16 items-center">

                    <div class="text-left">
                        <div
                            class="inline-block border-2 border-black px-3 py-1 mb-6 bg-white shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                            <span class="text-xs font-bold uppercase tracking-widest text-black">Why Register?</span>
                        </div>

                        <h2
                            class="text-4xl md:text-6xl font-black text-black leading-tight mb-8 uppercase  tracking-tighter">
                            Manage your <br> <span
                                class="text-[#0789da] underline decoration-4 underline-offset-4">Digital
                                Smiles</span>
                        </h2>

                        <div class="space-y-8">
                            <div class="flex gap-6 group">
                                <div
                                    class="flex-shrink-0 w-12 h-12 bg-white border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex items-center justify-center group-hover:bg-[#0789da] group-hover:text-white transition-all">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-black uppercase text-lg">Track Your Records</h4>
                                    <p class="font-medium text-gray-600">Access your complete dental history, and
                                        treatment plans anytime, anywhere. No more physical folders.</p>
                                </div>
                            </div>

                            <div class="flex gap-6 group">
                                <div
                                    class="flex-shrink-0 w-12 h-12 bg-white border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex items-center justify-center group-hover:bg-[#0789da] group-hover:text-white transition-all">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-black uppercase text-lg">Smart Notifications</h4>
                                    <p class="font-medium text-gray-600">Receive automatic reminders via email or SMS.
                                        Never miss a check-up or follow-up appointment again.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-12">
                            <a href="/register"
                                class="inline-flex items-center justify-center px-10 py-5 bg-[#0789da] text-white text-lg font-black border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-[3px] hover:translate-y-[3px] transition-all uppercase tracking-widest">
                                Register Now →
                            </a>
                        </div>
                    </div>


                    <div class="hidden lg:block">
                        <div class="relative inline-block w-full">
                            <div
                                class="absolute inset-0 bg-[#0789da] border-2 border-black translate-x-4 translate-y-4">
                            </div>
                            <div
                                class="relative bg-white border-2 border-black p-10 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]">
                                <div class="space-y-6">
                                    <div class="flex items-center gap-4 border-b-2 border-black pb-4">
                                        <div
                                            class="w-12 h-12 bg-[#0789da] text-white flex items-center justify-center font-bold">
                                            1</div>
                                        <p class="font-black uppercase text-sm">Create an account in seconds</p>
                                    </div>
                                    <div class="flex items-center gap-4 border-b-2 border-black pb-4">
                                        <div
                                            class="w-12 h-12 bg-[#0789da] text-white flex items-center justify-center font-bold">
                                            2</div>
                                        <p class="font-black uppercase text-sm">Secure digital record storage</p>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-12 h-12 bg-[#0789da] text-white flex items-center justify-center font-bold">
                                            3</div>
                                        <p class="font-black uppercase text-sm">Instant appointment alerts</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        @include('components.homepage.footer-section')
    </main>

    <script>
        function nextPage() {
            const container = document.getElementById('stack-container');
            const cards = container.querySelectorAll('.stack-card');

            // Get the current top card (the last one in the DOM)
            const topCard = cards[cards.length - 1];

            // 1. Slide straight to the right (No rotation)
            topCard.style.transform = 'translateX(110%)';
            topCard.style.opacity = '0';

            setTimeout(() => {
                // 2. Move it to the bottom of the stack (beginning of the parent)
                container.prepend(topCard);

                // 3. Reset its position so it's ready to appear at the bottom
                topCard.style.transform = 'translateX(0)';
                topCard.style.opacity = '1';

                // 4. Update Z-indices so the order is always correct
                const currentCards = container.querySelectorAll('.stack-card');
                currentCards.forEach((card, index) => {
                    card.style.zIndex = (index + 1) * 10;
                });
            }, 500); // Duration matches Tailwind's transition-all duration-500
        }

        document.addEventListener('DOMContentLoaded', function() {
            const menuBtn = document.getElementById('menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');
            const menuIcon = document.getElementById('menu-icon');
            const menuPanel = document.getElementById('mobile-menu-panel');
            const menuBackdrop = document.getElementById('mobile-menu-backdrop');
            const menuClose = document.getElementById('menu-close');
            if (!menuBtn || !mobileMenu || !menuIcon || !menuPanel || !menuBackdrop || !menuClose) return;

            function openMenu() {
                mobileMenu.classList.remove('hidden');
                mobileMenu.setAttribute('aria-hidden', 'false');
                menuBtn.setAttribute('aria-expanded', 'true');
                menuIcon.innerHTML =
                    `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />`;
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        menuBackdrop.classList.remove('opacity-0');
                        menuBackdrop.classList.add('opacity-100');
                        menuPanel.classList.remove('translate-x-full');
                        menuClose.focus();
                    });
                });
            }

            function closeMenu() {
                if (mobileMenu.contains(document.activeElement)) {
                    menuBtn.focus();
                }
                menuPanel.classList.add('translate-x-full');
                menuBackdrop.classList.remove('opacity-100');
                menuBackdrop.classList.add('opacity-0');
                menuBtn.setAttribute('aria-expanded', 'false');
                menuIcon.innerHTML =
                    `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />`;
                setTimeout(() => {
                    mobileMenu.classList.add('hidden');
                    mobileMenu.setAttribute('aria-hidden', 'true');
                }, 300);
            }

            menuBtn.addEventListener('click', function() {
                if (mobileMenu.classList.contains('hidden')) {
                    openMenu();
                } else {
                    closeMenu();
                }
            });

            menuBackdrop.addEventListener('click', closeMenu);
            menuClose.addEventListener('click', closeMenu);
        });
    </script>
</body>

</html>
