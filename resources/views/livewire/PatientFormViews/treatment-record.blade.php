<div class="w-full">
    <!-- Header matched to Basic Info style -->
    <div class="bg-blue-100 border-l-4 border-blue-500 p-4 mb-6">
        <h2 class="text-xl font-bold text-black">Treatment Record</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 px-4 lg:px-0">
        
        <!-- Left Column: Form Fields (Compact Layout) -->
        <div class="lg:col-span-7 space-y-5">
            
            <!-- DMD -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <label for="dmd" class="sm:w-1/3 text-lg font-medium text-gray-700 sm:text-right pr-4">DMD :</label>
                <div class="flex-1 w-full">
                    <input wire:model="dmd" type="text" id="dmd" 
                        class="w-full border rounded px-4 py-3 text-base disabled:bg-gray-100 disabled:text-gray-500"
                        placeholder="e.g., Dr. Name"
                        @if($isReadOnly) disabled @endif
                    >
                    @error('dmd') <span class="text-red-500 text-sm block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Treatment / Procedure -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <label for="treatment" class="sm:w-1/3 text-lg font-medium text-gray-700 sm:text-right pr-4">Treatment :</label>
                <div class="flex-1 w-full">
                    <input wire:model="treatment" type="text" id="treatment" 
                        class="w-full border rounded px-4 py-3 text-base disabled:bg-gray-100 disabled:text-gray-500"
                        placeholder="e.g., Extraction"
                        @if($isReadOnly) disabled @endif
                    >
                    @error('treatment') <span class="text-red-500 text-sm block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Cost of Treatment -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <label for="cost_of_treatment" class="sm:w-1/3 text-lg font-medium text-gray-700 sm:text-right pr-4">Cost :</label>
                <div class="flex-1 w-full">
                    <input wire:model="cost_of_treatment" type="number" id="cost_of_treatment" 
                        class="w-full border rounded px-4 py-3 text-base disabled:bg-gray-100 disabled:text-gray-500"
                        placeholder="0.00"
                        @if($isReadOnly) disabled @endif
                    >
                </div>
            </div>

            <!-- Amount Charged -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <label for="amount_charged" class="sm:w-1/3 text-lg font-medium text-gray-700 sm:text-right pr-4">Charged :</label>
                <div class="flex-1 w-full">
                    <input wire:model="amount_charged" type="number" id="amount_charged" 
                        class="w-full border rounded px-4 py-3 text-base disabled:bg-gray-100 disabled:text-gray-500"
                        placeholder="0.00"
                        @if($isReadOnly) disabled @endif
                    >
                </div>
            </div>

            <!-- Remarks -->
            <div class="flex flex-col sm:flex-row sm:items-start gap-2">
                <label for="remarks" class="sm:w-1/3 text-lg font-medium text-gray-700 sm:text-right pr-4 pt-3">Remarks :</label>
                <div class="flex-1 w-full">
                    <textarea wire:model="remarks" id="remarks" rows="5" 
                        class="w-full border rounded px-4 py-3 text-base disabled:bg-gray-100 disabled:text-gray-500"
                        placeholder="Enter notes here..."
                        @if($isReadOnly) disabled @endif
                    ></textarea>
                </div>
            </div>

        </div>

        <!-- Right Column: Single File Upload -->
        <div class="lg:col-span-5">
            <div class="h-full min-h-[300px] border-2 border-dashed border-gray-400 bg-gray-50 rounded-lg flex flex-col items-center justify-center p-6 relative group hover:bg-gray-100 transition-colors">
                
                @if($isReadOnly)
                    @if($image)
                        <div class="w-full h-full flex items-center justify-center">
                            @if(is_string($image))
                                <img src="{{ $image }}" class="max-h-[300px] w-auto object-contain rounded shadow-sm">
                            @else
                                <p class="text-gray-500">Image not viewable.</p>
                            @endif
                        </div>
                    @else
                        <p class="text-gray-500 font-medium">No image available in view mode.</p>
                    @endif
                @else
                    {{-- <input type="file" wire:model="image" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"> --}}
                    
                    @if($image)
                        <!-- Preview for Single Image -->
                        <div class="text-center z-0 w-full h-full flex flex-col items-center justify-center">
                            <div class="w-40 h-40 mb-3 border border-gray-300 rounded shadow-sm overflow-hidden bg-white">
                                @if(is_string($image))
                                    <!-- Scenario A: Existing Image from DB (Base64 String) -->
                                    <img src="{{ $image }}" class="w-full h-full object-cover">
                                @elseif(is_object($image) && method_exists($image, 'temporaryUrl'))
                                    <!-- Scenario B: New Upload (Temporary File) -->
                                    <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <p class="text-gray-800 font-bold truncate max-w-[200px]">
                                {{ is_string($image) ? 'Image Attached' : $image->getClientOriginalName() }}
                            </p>
                            <p class="text-sm text-gray-500 mt-1">Click to change file</p>
                        </div>
                    @else
                        <div class="text-center z-0 pointer-events-none">
                            <!-- Paperclip Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-gray-600 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                            
                            <p class="text-lg font-medium text-gray-800 mb-6">Choose an image to upload</p>
                            
                            <span class="px-6 py-2 bg-transparent border border-gray-600 text-gray-700 rounded-full text-sm font-semibold hover:bg-gray-300 transition-colors">
                                Browse file
                            </span>
                        </div>
                    @endif

                    <div wire:loading wire:target="image" class="absolute inset-0 bg-white/80 flex items-center justify-center z-20">
                        <div class="flex flex-col items-center">
                            <svg class="animate-spin h-8 w-8 text-blue-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-sm font-bold text-blue-600">Uploading...</span>
                        </div>
                    </div>
                @endif
            </div>
            @error('image') <span class="text-red-500 text-sm block mt-2">{{ $message }}</span> @enderror
        </div>
    </div>
</div>