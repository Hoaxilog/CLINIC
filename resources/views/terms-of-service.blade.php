<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Terms of Service</title>
    @vite('resources/css/app.css')
</head>

<body>
    @include('components.homepage.header-section')

    <section class="bg-[#FCFCFC] py-10 sm:py-14 lg:py-16 my-10 sm:my-14">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8 sm:mb-12">
                <div
                    class="inline-block border-2 border-black px-3 py-1 mb-4 bg-white shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                    <span class="text-xs font-bold uppercase tracking-widest text-black">Terms of Service</span>
                </div>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-black uppercase tracking-tighter">
                    Terms of <span class="text-[#0789da]">Use</span>
                </h1>
            </div>

            <div
                class="bg-white border-2 border-black p-5 sm:p-6 lg:p-8 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] space-y-6">
                <p class="text-sm sm:text-base font-medium leading-relaxed">
                    These Terms of Service govern your use of the dental clinic system. By using the system, you agree
                    to follow these rules.
                </p>

                <div>
                    <h2 class="text-lg sm:text-xl font-black uppercase mb-2">Acceptable Use</h2>
                    <ul class="list-disc pl-5 space-y-1 text-sm sm:text-base">
                        <li>Provide accurate and truthful information.</li>
                        <li>Use the appointment system only for legitimate clinic services.</li>
                        <li>Do not attempt to access accounts or data that are not yours.</li>
                        <li>Do not abuse, harass, or threaten clinic staff or other users.</li>
                    </ul>
                </div>

                <div>
                    <h2 class="text-lg sm:text-xl font-black uppercase mb-2">Appointments</h2>
                    <ul class="list-disc pl-5 space-y-1 text-sm sm:text-base">
                        <li>Appointment slots are subject to availability.</li>
                        <li>Repeated no-shows or cancellations may result in scheduling limits.</li>
                        <li>The clinic may reschedule appointments when necessary.</li>
                    </ul>
                </div>

                <div>
                    <h2 class="text-lg sm:text-xl font-black uppercase mb-2">Account Security</h2>
                    <p class="text-sm sm:text-base font-medium leading-relaxed">
                        You are responsible for maintaining the confidentiality of your account credentials. Notify the
                        clinic immediately if you suspect unauthorized access.
                    </p>
                </div>

                <div>
                    <h2 class="text-lg sm:text-xl font-black uppercase mb-2">Data and Privacy</h2>
                    <p class="text-sm sm:text-base font-medium leading-relaxed">
                        Your use of the system is also governed by our Privacy Policy. Please review it to understand
                        how
                        your data is handled.
                    </p>
                </div>

                <div>
                    <h2 class="text-lg sm:text-xl font-black uppercase mb-2">Enforcement</h2>
                    <p class="text-sm sm:text-base font-medium leading-relaxed">
                        We may suspend or terminate access for violations of these terms or for activity that
                        compromises
                        the security and integrity of the system.
                    </p>
                </div>

                <div>
                    <h2 class="text-lg sm:text-xl font-black uppercase mb-2">Changes</h2>
                    <p class="text-sm sm:text-base font-medium leading-relaxed">
                        We may update these Terms from time to time. Continued use of the system means you accept the
                        updated terms.
                    </p>
                </div>

                <p class="text-xs sm:text-sm text-gray-600">
                    Last updated: {{ now()->format('F j, Y') }}
                </p>
            </div>
        </div>
    </section>

    @include('components.homepage.footer-section')
</body>

</html>
