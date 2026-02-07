<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Privacy Policy</title>
    @vite('resources/css/app.css')
</head>

<body>
    @include('components.homepage.header-section')

    <section class="bg-[#FCFCFC] py-10 sm:py-14 lg:py-16 my-10 sm:my-14">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8 sm:mb-12">
                <div
                    class="inline-block border-2 border-black px-3 py-1 mb-4 bg-white shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                    <span class="text-xs font-bold uppercase tracking-widest text-black">Privacy Policy</span>
                </div>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-black uppercase tracking-tighter">
                    Your Privacy <span class="text-[#0789da]">Matters</span>
                </h1>
            </div>

            <div
                class="bg-white border-2 border-black p-5 sm:p-6 lg:p-8 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] space-y-6">
                <p class="text-sm sm:text-base font-medium leading-relaxed">
                    This Privacy Policy explains how our dental clinic system collects, uses, and protects your
                    personal information. By using the system, you agree to the practices described here.
                </p>

                <div>
                    <h2 class="text-lg sm:text-xl font-black uppercase mb-2">Information We Collect</h2>
                    <ul class="list-disc pl-5 space-y-1 text-sm sm:text-base">
                        <li>Identity details: name, age, gender, and contact information.</li>
                        <li>Account details: email address and login credentials.</li>
                        <li>Appointment data: preferred date/time, services requested, and visit history.</li>
                        <li>Clinical records: dental chart, treatment records, and related notes.</li>
                        <li>System logs: login activity and audit trails for security and compliance.</li>
                    </ul>
                </div>

                <div>
                    <h2 class="text-lg sm:text-xl font-black uppercase mb-2">Why We Collect It</h2>
                    <ul class="list-disc pl-5 space-y-1 text-sm sm:text-base">
                        <li>To schedule and manage appointments.</li>
                        <li>To maintain accurate dental records and provide safe care.</li>
                        <li>To communicate with you about your visits or account.</li>
                        <li>To secure the system and prevent misuse or fraud.</li>
                        <li>To comply with applicable laws and regulations.</li>
                    </ul>
                </div>

                <div>
                    <h2 class="text-lg sm:text-xl font-black uppercase mb-2">Who Can Access Your Data</h2>
                    <ul class="list-disc pl-5 space-y-1 text-sm sm:text-base">
                        <li>Authorized clinic staff involved in your care or administration.</li>
                        <li>System administrators for maintenance, security, and support.</li>
                        <li>Regulatory or legal authorities when required by law.</li>
                    </ul>
                </div>

                <div>
                    <h2 class="text-lg sm:text-xl font-black uppercase mb-2">Data Protection</h2>
                    <p class="text-sm sm:text-base font-medium leading-relaxed">
                        We use access controls, audit logs, and security measures to protect your data. You are
                        responsible for keeping your account credentials confidential.
                    </p>
                </div>

                <div>
                    <h2 class="text-lg sm:text-xl font-black uppercase mb-2">Your Rights</h2>
                    <ul class="list-disc pl-5 space-y-1 text-sm sm:text-base">
                        <li>Request access to or correction of your personal information.</li>
                        <li>Request deletion of your data, subject to legal and clinical retention requirements.</li>
                        <li>Withdraw consent where applicable.</li>
                    </ul>
                </div>

                <div>
                    <h2 class="text-lg sm:text-xl font-black uppercase mb-2">Contact</h2>
                    <p class="text-sm sm:text-base font-medium leading-relaxed">
                        If you have questions about this Privacy Policy, please contact the clinic directly.
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
