<div class="w-full">
    <div class="bg-blue-100 border-l-4 border-blue-500 p-4 mb-6">
        <h2 class="text-xl font-bold text-black">Treatment Record</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 px-4 lg:px-0">

        <div class="lg:col-span-7 space-y-5">

            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <label for="dmd" class="sm:w-1/3 text-lg font-medium text-gray-700 sm:text-right pr-4">DMD <span
                        class="text-red-600">*</span> :</label>
                <div class="flex-1 w-full">
                    <input wire:model="dmd" type="text" id="dmd"
                        class="w-full border rounded px-4 py-3 text-base disabled:bg-gray-100 disabled:text-gray-500"
                        placeholder="e.g., Dr. Name" @if ($isReadOnly) disabled @endif>
                    @error('dmd')
                        <span data-error-for="dmd" class="text-red-500 text-sm block mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <label for="treatment" class="sm:w-1/3 text-lg font-medium text-gray-700 sm:text-right pr-4">Treatment
                    <span class="text-red-600">*</span> :</label>
                <div class="flex-1 w-full">
                    <input wire:model="treatment" type="text" id="treatment"
                        class="w-full border rounded px-4 py-3 text-base disabled:bg-gray-100 disabled:text-gray-500"
                        placeholder="e.g., Extraction" @if ($isReadOnly) disabled @endif>
                    @error('treatment')
                        <span data-error-for="treatment" class="text-red-500 text-sm block mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <label for="cost_of_treatment"
                    class="sm:w-1/3 text-lg font-medium text-gray-700 sm:text-right pr-4">Estimated Cost <span
                        class="text-red-600">*</span> :</label>
                <div class="flex-1 w-full">
                    <input wire:model="cost_of_treatment" type="number" id="cost_of_treatment"
                        class="w-full border rounded px-4 py-3 text-base disabled:bg-gray-100 disabled:text-gray-500"
                        placeholder="0.00" @if ($isReadOnly) disabled @endif>
                    {{-- [ADDED] Error Message --}}
                    @error('cost_of_treatment')
                        <span data-error-for="cost_of_treatment"
                            class="text-red-500 text-sm block mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <label for="amount_charged"
                    class="sm:w-1/3 text-lg font-medium text-gray-700 sm:text-right pr-4">Payment <span
                        class="text-red-600">*</span> :</label>
                <div class="flex-1 w-full">
                    <input wire:model="amount_charged" type="number" id="amount_charged"
                        class="w-full border rounded px-4 py-3 text-base disabled:bg-gray-100 disabled:text-gray-500"
                        placeholder="0.00" @if ($isReadOnly) disabled @endif>
                    {{-- [ADDED] Error Message --}}
                    @error('amount_charged')
                        <span data-error-for="amount_charged"
                            class="text-red-500 text-sm block mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-start gap-2">
                <label for="remarks" class="sm:w-1/3 text-lg font-medium text-gray-700 sm:text-right pr-4 pt-3">Remarks
                    :</label>
                <div class="flex-1 w-full">
                    <textarea wire:model="remarks" id="remarks" rows="5"
                        class="w-full border rounded px-4 py-3 text-base disabled:bg-gray-100 disabled:text-gray-500"
                        placeholder="Enter notes here..." @if ($isReadOnly) disabled @endif></textarea>
                    {{-- [ADDED] Error Message --}}
                    @error('remarks')
                        <span data-error-for="remarks" class="text-red-500 text-sm block mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="lg:col-span-5 space-y-3" x-data="{ showImage: false, activeImage: '', activeLabel: '' }">
            <div class="rounded-lg border border-gray-200 bg-white p-4">
                <div class="text-sm font-semibold text-gray-800">Treatment Images</div>
                <div class="mt-3 space-y-3">
                    @if (!$isReadOnly)
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Before</div>
                            <input wire:model="beforeImages" type="file" id="beforeImages" multiple
                                class="mt-2 w-full border rounded px-4 py-3 text-base disabled:bg-gray-100 disabled:text-gray-500"
                                accept="image/*">
                            @error('beforeImages')
                                <span class="text-red-500 text-sm block mt-1">{{ $message }}</span>
                            @enderror
                            @error('beforeImages.*')
                                <span class="text-red-500 text-sm block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">After</div>
                            <input wire:model="afterImages" type="file" id="afterImages" multiple
                                class="mt-2 w-full border rounded px-4 py-3 text-base disabled:bg-gray-100 disabled:text-gray-500"
                                accept="image/*">
                            @error('afterImages')
                                <span class="text-red-500 text-sm block mt-1">{{ $message }}</span>
                            @enderror
                            @error('afterImages.*')
                                <span class="text-red-500 text-sm block mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    @php
                        $beforeList = collect($existingImages)
                            ->filter(fn($i) => ($i['image_type'] ?? '') === 'before')
                            ->values();
                        $afterList = collect($existingImages)
                            ->filter(fn($i) => ($i['image_type'] ?? '') === 'after')
                            ->values();
                    @endphp

                    @if ($beforeList->isNotEmpty())
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Before Treatment
                            </div>
                            <div class="mt-2 grid grid-cols-2 gap-3">
                                @foreach ($beforeList as $img)
                                    <div class="border rounded p-2 bg-white">
                                        <img class="w-full h-32 object-cover rounded"
                                            src="{{ \Illuminate\Support\Facades\Storage::url($img['image_path']) }}"
                                            alt="Before image"
                                            @click="activeImage = $el.src; activeLabel = 'before'; showImage = true">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($afterList->isNotEmpty())
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">After Treatment
                            </div>
                            <div class="mt-2 grid grid-cols-2 gap-3">
                                @foreach ($afterList as $img)
                                    <div class="border rounded p-2 bg-white">
                                        <img class="w-full h-32 object-cover rounded"
                                            src="{{ \Illuminate\Support\Facades\Storage::url($img['image_path']) }}"
                                            alt="After image"
                                            @click="activeImage = $el.src; activeLabel = 'after'; showImage = true">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (empty($existingImages))
                        <div class="text-xs text-gray-500">No Treatment Images yet.</div>
                    @endif
                </div>
            </div>

            <div x-show="showImage" x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-6"
                @click.self="showImage = false">
                <div class="w-full max-w-4xl rounded-lg bg-white p-4 shadow-xl">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-semibold text-gray-800" x-text="activeLabel"></div>
                        <button type="button" class="text-gray-500 hover:text-gray-800"
                            @click="showImage = false">Close</button>
                    </div>
                    <img class="mt-4 w-full max-h-[75vh] object-contain rounded" :src="activeImage"
                        alt="Full image">
                </div>
            </div>
        </div>

    </div>
</div>
