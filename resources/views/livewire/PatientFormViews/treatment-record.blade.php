<div class="w-full space-y-6">
    @php
        $labelClass = 'mb-1.5 block text-sm font-semibold text-slate-700';
        $inputClass =
            'w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100 disabled:bg-slate-100 disabled:text-slate-500';
    @endphp

    <div class="rounded-2xl border border-slate-200 bg-white p-5 md:p-6">
        <h2 class="text-lg font-semibold text-slate-900">Treatment Record</h2>
        <p class="mt-1 text-sm text-slate-500">Capture treatment details, billing, and attached images.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
        <div class="xl:col-span-7 rounded-2xl border border-slate-200 bg-white p-5 md:p-6">
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label for="dmd" class="{{ $labelClass }}">DMD <span class="text-red-600">*</span></label>
                    <input wire:model.defer="dmd" type="text" id="dmd" class="{{ $inputClass }}"
                        placeholder="e.g., Dr. Name" @if ($isReadOnly) disabled @endif>
                    @error('dmd')
                        <span data-error-for="dmd" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="treatment" class="{{ $labelClass }}">Treatment <span class="text-red-600">*</span></label>
                    <input wire:model.defer="treatment" type="text" id="treatment" class="{{ $inputClass }}"
                        placeholder="e.g., Extraction" @if ($isReadOnly) disabled @endif>
                    @error('treatment')
                        <span data-error-for="treatment" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="cost_of_treatment" class="{{ $labelClass }}">Estimated Cost <span class="text-red-600">*</span></label>
                    <input wire:model.defer="cost_of_treatment" type="number" id="cost_of_treatment"
                        class="{{ $inputClass }}" placeholder="0.00" @if ($isReadOnly) disabled @endif>
                    @error('cost_of_treatment')
                        <span data-error-for="cost_of_treatment"
                            class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="amount_charged" class="{{ $labelClass }}">Payment <span class="text-red-600">*</span></label>
                    <input wire:model.defer="amount_charged" type="number" id="amount_charged" class="{{ $inputClass }}"
                        placeholder="0.00" @if ($isReadOnly) disabled @endif>
                    @error('amount_charged')
                        <span data-error-for="amount_charged"
                            class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="remarks" class="{{ $labelClass }}">Remarks</label>
                    <textarea wire:model.defer="remarks" id="remarks" rows="5" class="{{ $inputClass }}"
                        placeholder="Enter notes here..." @if ($isReadOnly) disabled @endif></textarea>
                    @error('remarks')
                        <span data-error-for="remarks" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="xl:col-span-5" x-data="{ showImage: false, activeImage: '', activeLabel: '' }">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 md:p-6">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-700">Treatment Images</h3>

                <div class="mt-4 space-y-5">
                    @if (!$isReadOnly)
                        <div>
                            <label for="beforeImages" class="{{ $labelClass }}">Before</label>
                            <input wire:model="beforeImages" type="file" id="beforeImages" multiple
                                class="{{ $inputClass }}" accept="image/*">
                            @error('beforeImages')
                                <span class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                            @enderror
                            @error('beforeImages.*')
                                <span class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="afterImages" class="{{ $labelClass }}">After</label>
                            <input wire:model="afterImages" type="file" id="afterImages" multiple
                                class="{{ $inputClass }}" accept="image/*">
                            @error('afterImages')
                                <span class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                            @enderror
                            @error('afterImages.*')
                                <span class="mt-1 block text-xs text-red-500">{{ $message }}</span>
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
                            <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Before Treatment
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach ($beforeList as $img)
                                    <button type="button" class="overflow-hidden rounded-lg border border-slate-200 bg-white p-1"
                                        @click="activeImage = '{{ \Illuminate\Support\Facades\Storage::url($img['image_path']) }}'; activeLabel = 'before'; showImage = true">
                                        <img class="h-32 w-full rounded-md object-cover"
                                            src="{{ \Illuminate\Support\Facades\Storage::url($img['image_path']) }}"
                                            alt="Before image">
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($afterList->isNotEmpty())
                        <div>
                            <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">After Treatment
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach ($afterList as $img)
                                    <button type="button" class="overflow-hidden rounded-lg border border-slate-200 bg-white p-1"
                                        @click="activeImage = '{{ \Illuminate\Support\Facades\Storage::url($img['image_path']) }}'; activeLabel = 'after'; showImage = true">
                                        <img class="h-32 w-full rounded-md object-cover"
                                            src="{{ \Illuminate\Support\Facades\Storage::url($img['image_path']) }}"
                                            alt="After image">
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (empty($existingImages))
                        <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-5 text-center text-sm text-slate-500">
                            No treatment images yet.
                        </div>
                    @endif
                </div>
            </div>

            <div x-show="showImage" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-6"
                @click.self="showImage = false">
                <div class="w-full max-w-4xl rounded-xl bg-white p-4 shadow-xl">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-semibold uppercase tracking-wide text-slate-700" x-text="activeLabel"></div>
                        <button type="button" class="text-sm font-medium text-slate-500 hover:text-slate-800"
                            @click="showImage = false">Close</button>
                    </div>
                    <img class="mt-4 max-h-[75vh] w-full rounded-lg object-contain" :src="activeImage" alt="Full image">
                </div>
            </div>
        </div>
    </div>
</div>
