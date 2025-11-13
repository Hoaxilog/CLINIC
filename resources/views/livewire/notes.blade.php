<div class="">
    <div class="rounded-t-lg bg-[#ccebff] p-3 text-center">
        <h3 class="text-lg font-semibold text-gray-800">Notes / Reminders</h3>
    </div>

    <div class="space-y-3 overflow-y-auto p-4 h-96 scrollbar-thin scrollbar-color-[#0086da] scrollbar-track-[#ccebff] scrollbar-thumb-[#0086da] scrollbar-thumb-rounded-full">
        @forelse($notes as $note)
            <div
                class="rounded-lg bg-white transition delay-75 space-y-2 shadow-lg p-4 flex items-center justify-between hover:bg-[#ccebff]"
            >
                <a href="#" wire:click.prevent="viewNotes({{ $note->id }})" class="flex-1">
                    <h4 class="font-medium text-xl text-gray-900">{{ $note->title }}</h4>
                    <p class="text-lg text-gray-700">{{ \Carbon\Carbon::parse($note->created_at)->format('F j, Y') }}</p>
                </a>
                <button
                    type="button"
                    class="active:outline-2 active:outline-offset-3 active:outline-dashed active:outline-black ml-4 p-2 rounded pointer-events-auto transition hover:bg-[#f56565] group"
                    onclick="if(confirm('Delete this note?')) { @this.delete({{ $note->id }}) }"
                    title="Delete"
                >
                    <svg xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" viewBox="0 0 24 24"
                        fill="none"
                        stroke="#f56565"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="lucide lucide-trash-icon lucide-trash transition-colors group-hover:stroke-white"
                    >
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/>
                        <path d="M3 6h18"/>
                        <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                    </svg>
                </button>
            </div>
        @empty
            <div>
                <h1>No notes today</h1>
            </div>
        @endforelse
    </div>

    <button wire:click="openModal" class="active:outline-2 active:outline-offset-3 active:outline-dashed active:outline-black cursor-pointer absolute bottom-6 right-6 flex h-14 w-14 items-center justify-center rounded-full bg-[#ffac00] text-white shadow-lg transition hover:bg-yellow-500">
        <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-badge-plus-icon lucide-badge-plus"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><line x1="12" x2="12" y1="8" y2="16"/><line x1="8" x2="16" y1="12" y2="12"/></svg>    
    </button>

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black opacity-60"></div>
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 z-10 overflow-hidden">
                <div class="px-6 py-4 flex items-center justify-between bg-white border-b">
                    <h3 class="text-2xl font-semibold text-gray-900 ">{{ $isEditing || !$noteId ? 'Add Note' : 'My Note' }}</h3>
                    <button class="active:outline-2 active:outline-offset-3 active:outline-dashed active:outline-black text-[#0086da] text-5xl flex items-center justify-center px-3 py-1 rounded-full hover:bg-[#e6f4ff] transition" wire:click="closeModal" aria-label="Close">×</button>
                </div>

                <form class="p-6" wire:submit.prevent="{{ $noteId && $isEditing ? 'update' : ($noteId ? 'update' : 'save') }}">
                    <div class="mb-4">
                        <label class="block text-lg font-medium text-gray-700 mb-2">Title</label>
                        <input wire:model.defer="title" type="text" class="w-full border rounded px-4 py-3 text-base" placeholder="Note title" @if(!$isEditing && $noteId) readonly @endif />
                        @error('title') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-lg font-medium text-gray-700 mb-2">Notes</label>
                        <textarea wire:model.defer="content" rows="6" class="w-full border rounded px-4 py-3 text-base" placeholder="Write something..." @if(!$isEditing && $noteId) readonly @endif></textarea>
                        @error('content') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3">
                        @if($noteId && !$isEditing)
                            <button type="button" wire:click="edit" class="active:outline-2 active:outline-offset-3 active:outline-dashed active:outline-black px-5 py-3 rounded bg-[#0086da] text-white text-lg">Edit</button>
                        @endif

                        <button type="button" wire:click="closeModal" class="active:outline-2 active:outline-offset-3 active:outline-dashed active:outline-black px-5 py-3 rounded bg-gray-200">Cancel</button>

                        @if(!$noteId || $isEditing)
                            <button type="submit" class="active:outline-2 active:outline-offset-3 active:outline-dashed active:outline-black px-5 py-3 rounded bg-[#0086da] text-white text-lg">
                                Save
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>


<script>
    window.addEventListener('notes:flash', e => {
        alert(e.detail.message ?? 'Saved');
    });
    // Livewire v3 browser events use 'browser:notes:flash'
    window.addEventListener('browser:notes:flash', e => {
        alert(e.detail.message ?? 'Saved');
    });
</script>