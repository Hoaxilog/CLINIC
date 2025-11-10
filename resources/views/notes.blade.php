<div>
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
        {{-- 
        why forelse and why not foreach?
        forelse let us display something if theres no data and at the same time loop like foreach
        while foreach cant do that but we can achieve that using ifelse but not convinient
         --}}
        @forelse($notes as $note) 
            <div class="rounded-lg bg-white hover:bg-[#ccebff] hover:cursor-pointer transition delay-75 space-y-2 shadow-lg p-4">
                <a href=""  >
                    <h4 class="font-medium text-xl text-gray-900"> {{ $note->title }}</h4>
                    <p class="text-lg text-gray-700">{{ \Carbon\Carbon::parse($note->created_at)->format('F j, Y')}}</p>
                </a>
            </div>
        @empty
            <div>
                <h1> No notes today </h1>
            </div>
        @endforelse
    </div>

    <button id="add-notes" class="cursor-pointer absolute bottom-6 right-6 flex h-14 w-14 items-center justify-center rounded-full bg-[#ffac00] text-white shadow-lg transition hover:bg-yellow-500">
        <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-badge-plus-icon lucide-badge-plus"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><line x1="12" x2="12" y1="8" y2="16"/><line x1="8" x2="16" y1="12" y2="12"/></svg>    
    </button>

    {{-- MODAL --}}
    <div id="notes-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
        <div id="notes-modal-backdrop" class="absolute inset-0 bg-black opacity-60"></div>
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 z-10 overflow-hidden">
            <div class="p-6 flex items-center justify-between bg-white border-b">
                <h3 class="text-2xl font-semibold text-gray-900">Add Note</h3>
                <button id="notes-modal-close" class="text-[#0086da] text-5xl flex items-center justify-center px-3 py-3 rounded-full hover:bg-[#e6f4ff] transition" aria-label="Close">
                    <!-- SVG icon -->
                </button>
            </div>  
            <form id="notes-form" class="p-6" method="POST" action="/notes">
                @csrf
                <div class="mb-4">
                    <label class="block text-lg font-medium text-gray-700 mb-2">Title</label>
                    <input id="note-title" name="title" type="text" class="w-full border rounded px-4 py-3 text-base" placeholder="Note title" />
                </div>
                <div class="mb-4">
                    <label class="block text-lg font-medium text-gray-700 mb-2">Notes</label>
                    <textarea id="note-content" name="notes" rows="6" class="w-full border rounded px-4 py-3 text-base" placeholder="Write something..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" id="notes-cancel" class="px-5 py-3 rounded bg-gray-200">Cancel</button>
                    <button type="submit" class="px-5 py-3 rounded bg-[#0086da] text-white text-lg">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
