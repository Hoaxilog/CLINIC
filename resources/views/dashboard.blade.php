@extends('index')

@section('content')
    <main id="mainContent" class="min-h-screen bg-gray-100 p-6 lg:p-8 ml-64 mt-14 transition-all duration-300 peer-[.collapsed]:ml-16">
        <h1 class="text-3xl lg:text-4xl font-bold text-gray-800">Dashboard</h1>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <section class="lg:col-span-1 bg-white p-6 rounded-lg shadow-md space-y-4 h-full">
            <div class="flex items-center justify-between">
                <p class="font-medium text-gray-700">Today's Appointment</p>
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0086da" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
            </div>
            <div>
                <h1 class="text-5xl font-semibold text-gray-900">6</h1>
            </div>
            <p class="text-gray-600">2 completed, 4 upcoming</p>
            </section>

            <section class="lg:col-span-1 bg-white p-6 rounded-lg shadow-md space-y-4 h-full">
            <div class="flex items-center justify-between">
                <p class="font-medium text-gray-700">Treatments Completed</p>
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0086da" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clipboard-check"><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="m9 14 2 2 4-4"/></svg>
            </div>
            <div>
                <h1 class="text-5xl font-semibold text-gray-900">13</h1>
            </div>
            <p class="text-gray-600">+5 from last week</p>
            </section>

            <section class="lg:col-span-1 bg-[#0086da] text-white rounded-lg shadow-md p-6 flex flex-col justify-center items-center text-center h-full">
            <div class="text-2xl font-medium">Calendar</div>
            <div id="realtime-time" class="text-6xl lg:text-7xl font-extrabold my-2">
                Loading...
            </div>
            <div id="realtime-date" class="text-xl lg:text-2xl font-medium">
                </div>
            </section>

            <section class="lg:col-span-2 bg-white rounded-lg shadow-md flex flex-col">
                <div class="flex items-center justify-between p-4 border-b">
                    <h1 class="text-2xl font-semibold text-gray-800">Today's Schedule</h1>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0086da" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock"><path d="M12 6v6l4 2"/><circle cx="12" cy="12" r="10"/></svg>
                </div>
                
                <div class="space-y-3 p-4 h-96 overflow-y-auto scrollbar-thin scrollbar-thumb-rounded-full scrollbar-track-[#ccebff] scrollbar-thumb-[#0086da]  scrollbar-color-[#0086da] ">
                    <div class="bg-[#ccebff] p-3 rounded-lg ">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 mr-4">
                                <h2 class="text-lg font-semibold text-gray-900">Ace Morada</h2>
                                <p class="text-sm font-medium text-gray-700">Cleaning</p>
                            </div>
                            <p class="flex-shrink-0 text-md font-semibold text-[#0086da] bg-white border border-[#0086da] px-3 py-1 rounded-lg w-20 text-center">9:00</p>
                        </div>
                    </div>

                    <div class="bg-[#ccebff] p-3 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 mr-4">
                            <h2 class="text-lg font-semibold text-gray-900">Jhon Stephen Nicolas </h2>
                            <p class="text-sm font-medium text-gray-700">Cleaning</p>
                            </div>
                            <p class="flex-shrink-0 text-md font-semibold text-[#0086da] bg-white border border-[#0086da] px-3 py-1 rounded-lg w-20 text-center">10:00</p>
                        </div>
                    </div>

                    <div class="bg-[#ccebff] p-3 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 mr-4">
                            <h2 class="text-lg font-semibold text-gray-900">Clarenz Luigi</h2>
                            <p class="text-sm font-medium text-gray-700">Cleaning</p>
                            </div>
                            <p class="flex-shrink-0 text-md font-semibold text-[#0086da] bg-white border border-[#0086da] px-3 py-1 rounded-lg w-20 text-center">11:00</p>
                        </div>
                    </div>

                    <div class="bg-[#ccebff] p-3 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 mr-4">
                            <h2 class="text-lg font-semibold text-gray-900">Clarenz Luigi</h2>
                            <p class="text-sm font-medium text-gray-700">Cleaning</p>
                            </div>
                            <p class="flex-shrink-0 text-md font-semibold text-[#0086da] bg-white border border-[#0086da] px-3 py-1 rounded-lg w-20 text-center">10:00</p>
                        </div>
                    </div>
                    <div class="bg-[#ccebff] p-3 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 mr-4">
                            <h2 class="text-lg font-semibold text-gray-900">Clarenz Luigi</h2>
                            <p class="text-sm font-medium text-gray-700">Cleaning</p>
                            </div>
                            <p class="flex-shrink-0 text-md font-semibold text-[#0086da] bg-white border border-[#0086da] px-3 py-1 rounded-lg w-20 text-center">10:00</p>
                        </div>
                    </div>
                    
                </div>
            </section>

            <section class="lg:col-span-1 relative flex flex-col bg-gray-100 rounded-lg shadow-md">
                <div class="rounded-t-lg bg-[#ccebff] p-3 text-center">
                    <h3 class="text-lg font-semibold text-gray-800">Notes / Reminders</h3>
                </div>

                <div class="
                    space-y-3 overflow-y-auto p-4 h-96 
                    scrollbar-thin 
                    scrollbar-color-[#0086da]
                    scrollbar-track-[#ccebff]
                    scrollbar-thumb-[#0086da] 
                    scrollbar-thumb-rounded-full
                    ">
                    <div class="rounded-lg bg-white hover:bg-[#ccebff] hover:cursor-pointer transition delay-75  shadow-lg p-4">
                    <h4 class="font-medium text-gray-900">Sample notes title goes here</h4>
                    <p class="text-sm text-gray-700">October 26, 2025</p>
                    </div>
                    <div class="rounded-lg bg-white hover:bg-[#ccebff] hover:cursor-pointer transition delay-75  shadow-lg p-4">
                    <h4 class="font-medium text-gray-900">Another note about the project</h4>
                    <p class="text-sm text-gray-700">October 25, 2025</p>
                    </div>
                    <div class="rounded-lg bg-white hover:bg-[#ccebff] hover:cursor-pointer transition delay-75  shadow-lg p-4">
                    <h4 class="font-medium text-gray-900">Team meeting reminder</h4>
                    <p class="text-sm text-gray-700">October 24, 2025</p>
                    </div>
                    <div class="rounded-lg bg-white hover:bg-[#ccebff] hover:cursor-pointer transition delay-75  shadow-lg p-4">
                    <h4 class="font-medium text-gray-900">Deploy staging server</h4>
                    <p class="text-sm text-gray-700">October 23, 2025</p>
                    </div>
                    <div class="rounded-lg bg-white hover:bg-[#ccebff] hover:cursor-pointer transition delay-75 shadow-lg p-4">
                    <h4 class="font-medium text-gray-900">Update dependencies</h4>
                    <p class="text-sm text-gray-700">October 22, 2025</p>
                    </div>
                </div>

                <button id="add-notes" class="cursor-pointer absolute bottom-6 right-6 flex h-14 w-14 items-center justify-center rounded-full bg-[#ffac00] text-white shadow-lg transition hover:bg-yellow-500">
                    <span class="text-4xl font-light">+</span>
                </button>
            </section>

            <section class="lg:col-span-3 bg-white rounded-lg shadow-md p-4">
            <h1 class="text-2xl font-semibold text-gray-800 mb-4">Quick Actions</h1>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">

                <button class="bg-[#ccebff] text-gray-800 font-semibold p-4 rounded-lg flex flex-col items-center gap-2 hover:bg-blue-200 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0086da" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-plus"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
                    <span>Add Patient</span>
                </button>

                <button class="bg-[#ccebff] text-gray-800 font-semibold p-4 rounded-lg flex flex-col items-center gap-2 hover:bg-blue-200 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0086da" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clipboard-clock"><path d="M16 14v2.2l1.6 1"/><path d="M16 4h2a2 2 0 0 1 2 2v.832"/><path d="M8 4H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h2"/><circle cx="16" cy="16" r="6"/><rect x="8" y="2" width="8" height="4" rx="1"/></svg>
                    <span>Patient History</span>
                </button>

                <button class="bg-[#ccebff] text-gray-800 font-semibold p-4 rounded-lg flex flex-col items-center gap-2 hover:bg-blue-200 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0086da" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clipboard-plus"><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M9 14h6"/><path d="M12 17v-6"/></svg>
                    <span>Book Appointment</span>
                </button>

                <button class="bg-[#ccebff] text-gray-800 font-semibold p-4 rounded-lg flex flex-col items-center gap-2 hover:bg-blue-200 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0086da" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-database-backup"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 12a9 3 0 0 0 5 2.69"/><path d="M21 9.3V5"/><path d="M3 5v14a9 3 0 0 0 6.47 2.88"/><path d="M12 12v4h4"/><path d="M13 20a5 5 0 0 0 9-3 4.5 4.5 0 0 0-4.5-4.5c-1.33 0-2.54.54-3.41 1.41L12 16"/></svg>
                    <span>Back up Data</span>
                </button>
            </div>
            </section>

        </div> 
    </main>
@endsection

@push('script') 
    <script>
        function updateLiveTime() {
            const timeElement = document.getElementById('realtime-time');
            const dateElement = document.getElementById('realtime-date');
            
            if (!timeElement || !dateElement) return; // Failsafe
            
            const now = new Date();
            
            // Format Time
            let hours = now.getHours();
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12; // 0 hour is 12
            const formattedTime = `${hours.toString().padStart(2, '0')}:${minutes} ${ampm}`;
            
            // Format Date
            const formattedDate = now.toLocaleString('en-US', {
            month: 'long',
            day: 'numeric',
            year: 'numeric'
            });
            
            // Update HTML
            if (timeElement.innerText !== formattedTime) {
            timeElement.innerText = formattedTime;
            }
            if (dateElement.innerText !== formattedDate) {
            dateElement.innerText = formattedDate;
            }
        }

        updateLiveTime();
        setInterval(updateLiveTime, 1000);

        (function () {
            const openBtn = document.getElementById('add-notes');
            const notesList = document.getElementById('notes-list');

            // create modal element and append to body
            const modalHtml = `
            <div id="notes-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
            <!-- darker backdrop -->
            <div id="notes-modal-backdrop" class="absolute inset-0 bg-black opacity-60"></div>

            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 z-10 overflow-hidden">
                <!-- header: white background, larger blue close button -->
                <div class="p-6 flex items-center justify-between bg-white border-b">
                <h3 class="text-2xl font-semibold text-gray-900">Add Note</h3>
                <button id="notes-modal-close" class="text-[#0086da] text-5xl leading-none px-3 py-1 rounded-full hover:bg-[#e6f4ff] transition" aria-label="Close">&times;</button>
                </div>

                <form id="notes-form" class="p-6">
                <div class="mb-4">
                    <label class="block text-lg font-medium text-gray-700 mb-2">Title</label>
                    <input id="note-title" type="text" class="w-full border rounded px-4 py-3 text-base" placeholder="Note title" />
                </div>
                <div class="mb-4">
                    <label class="block text-lg font-medium text-gray-700 mb-2">Notes</label>
                    <textarea id="note-content" rows="6" class="w-full border rounded px-4 py-3 text-base" placeholder="Write something..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" id="notes-cancel" class="px-5 py-3 rounded bg-gray-200">Cancel</button>
                    <button type="submit" class="px-5 py-3 rounded bg-[#0086da] text-white text-lg">Save</button>
                </div>
                </form>
            </div>
            </div>`;

            document.body.insertAdjacentHTML('beforeend', modalHtml);

            const modal = document.getElementById('notes-modal');
            const backdrop = document.getElementById('notes-modal-backdrop');
            const closeBtn = document.getElementById('notes-modal-close');
            const cancelBtn = document.getElementById('notes-cancel');
            const form = document.getElementById('notes-form');
            const titleInput = document.getElementById('note-title');
            const contentInput = document.getElementById('note-content');

            function openModal() {
            modal.classList.remove('hidden');
            // reset fields
            titleInput.value = '';
            contentInput.value = '';
            setTimeout(() => titleInput.focus(), 50);
            }
            function closeModal() {
            modal.classList.add('hidden');
            }

            openBtn.addEventListener('click', openModal);
            closeBtn.addEventListener('click', closeModal);
            cancelBtn.addEventListener('click', closeModal);
            backdrop.addEventListener('click', closeModal);

            form.addEventListener('submit', function (e) {
            e.preventDefault();
            const title = (titleInput.value || 'Untitled note').trim();
            const content = (contentInput.value || '').trim();
            const now = new Date();
            const formattedDate = now.toLocaleString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });

            // build note node (matches existing styles)
            const noteNode = document.createElement('div');
            noteNode.className = 'rounded-lg bg-white hover:bg-[#ccebff] hover:cursor-pointer transition delay-75 shadow-lg p-4';
            const titleEl = document.createElement('h4');
            titleEl.className = 'font-medium text-gray-900';
            titleEl.textContent = title;
            noteNode.appendChild(titleEl);
            if (content) {
                const contentEl = document.createElement('p');
                contentEl.className = 'text-sm text-gray-700 mt-1';
                contentEl.textContent = content;
                noteNode.appendChild(contentEl);
            }
            const dateEl = document.createElement('p');
            dateEl.className = 'text-sm text-gray-700 mt-2';
            dateEl.textContent = formattedDate;
            noteNode.appendChild(dateEl);

            // insert new note at top
            if (notesList.firstChild) {
                notesList.insertBefore(noteNode, notesList.firstChild);
            } else {
                notesList.appendChild(noteNode);
            }

            closeModal();
            });
        })();
    </script>
@endpush

