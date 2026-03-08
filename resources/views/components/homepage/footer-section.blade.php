<footer class="bg-[#FCFCFC] border-t-4 border-black pt-16 pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
            <div class="space-y-6">
                <a href="/"
                    class="inline-block border-2 border-black px-4 py-1 bg-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                    <span class="text-xl font-bold tracking-tighter text-black uppercase">Teja Dent</span>
                </a>
                <p class="text-sm font-medium text-gray-600 leading-relaxed">
                    Providing world-class dental care in Diliman, Quezon City. We combine modern technology with a
                    patient-first approach.
                </p>
            </div>
            <div>
                <h4 class="font-black uppercase text-sm tracking-widest mb-6 underline decoration-2 underline-offset-4">
                    Quick Links</h4>
                <ul class="space-y-4 text-sm font-bold">
                    <li><a href="/" onclick="navigateToHome('#about')" class="hover:text-[#0789da] transition-colors">About Us</a></li>
                    <li><a href="/" onclick="navigateToHome('#services')" class="hover:text-[#0789da] transition-colors">Our Services</a></li>
                    <li><a href="/" onclick="navigateToHome('#contact')" class="hover:text-[#0789da] transition-colors">Location</a></li>
                    <li><a href="/book" class="hover:text-[#0789da] transition-colors">Book Appointment</a>
                    </li>
                </ul>
            </div>
            <div>
                <h4 class="font-black uppercase text-sm tracking-widest mb-6 underline decoration-2 underline-offset-4">
                    Services</h4>
                <ul class="space-y-4 text-sm font-bold text-gray-600">
                    <li><a href="/services/general-checkup" class="hover:text-[#0789da] transition-colors">General
                            Dentistry</a></li>
                    <li><a href="/services/orthodontics" class="hover:text-[#0789da] transition-colors">Orthodontics</a>
                    </li>
                    <li><a href="/services/teeth-whitening" class="hover:text-[#0789da] transition-colors">Teeth
                            Whitening</a></li>
                    <li><a href="/services/oral-surgery" class="hover:text-[#0789da] transition-colors">Oral Surgery</a>
                    </li>
                </ul>
            </div>
            <div>
            </div>
        </div>
        <div class="border-t-2 border-black pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">
                &copy; 2026 TejaDent Clinic. All Rights Reserved.
            </p>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Developed by: </span>
                <div class="bg-black text-white px-2 py-1 text-[10px] font-bold border border-black italic">
                    DCSA Student
                </div>
            </div>
        </div>
    </div>
</footer>

<script>
    function navigateToHome(hash) {
        event.preventDefault();
        
        // Check if the section exists on the current page
        const sectionId = hash.substring(1); // Remove # symbol
        const section = document.getElementById(sectionId);
        const isOnHome = window.location.pathname === '/' || window.location.pathname === '/home';
        
        if (section && isOnHome) {
            // If on home page and section exists, scroll to it smoothly
            section.scrollIntoView({ behavior: 'smooth' });
        } else {
            // Navigate to home page with the hash
            window.location.href = '/' + hash;
        }
    }
</script>
