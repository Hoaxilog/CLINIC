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

    </div>
</div>