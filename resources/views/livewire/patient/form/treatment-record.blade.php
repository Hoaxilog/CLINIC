<div class="w-full space-y-6">
    @php
        $labelClass = 'mb-1.5 block text-sm font-semibold text-slate-700';
        $errorBag = session('errors');
        $inputClass =
            'w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100 disabled:bg-slate-100 disabled:text-slate-500';
        $fieldClass = fn(string $field) => ($errorBag && $errorBag->has($field))
            ? $inputClass . ' border-red-500 focus:border-red-500 focus:ring-red-200'
            : $inputClass;
    @endphp

    @php
        $hasTreatmentRecord = !empty($existingImages)
            || !empty($selectedTreatments)
            || filled($treatment)
            || filled($cost_of_treatment)
            || filled($amount_charged)
            || filled($remarks);
    @endphp

    <div class="rounded-2xl border border-slate-200 bg-white p-5 md:p-6">
        <h2 class="text-lg font-semibold text-slate-900">Treatment Record</h2>
        <p class="mt-1 text-sm text-slate-500">Capture treatment details, billing, and attached images.</p>
    </div>

    @if ($isReadOnly && ! $hasTreatmentRecord)
        <div class="flex min-h-[58vh] w-full flex-col items-center justify-center rounded-2xl border border-slate-200 bg-white p-10 text-center">
            <div class="rounded-full bg-blue-50 p-6">
                <svg class="h-20 w-20 text-[#0086da]" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z" />
                </svg>
            </div>
            <div class="mt-6">
                <h3 class="text-2xl font-bold text-gray-800">No Treatment Records</h3>
                <p class="mt-2 max-w-md text-gray-500">
                    This patient does not have any treatment records yet. Create the first record from the visit flow.
                </p>
            </div>
            @if (! $isReadOnly)
                <button type="button" wire:click="$dispatch('openNewVisitRecord')"
                    class="mt-6 inline-flex items-center rounded-lg bg-[#0086da] px-6 py-3 text-base font-bold text-white shadow-lg transition-all hover:scale-105 hover:bg-[#0073a8]">
                    + New Record
                </button>
            @endif
        </div>
    @else
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
        <div class="xl:col-span-7 rounded-2xl border border-slate-200 bg-white p-5 md:p-6">
            <div class="grid grid-cols-1 gap-5 xl:grid-cols-[minmax(0,0.92fr)_minmax(0,1.28fr)]">
                <div class="space-y-5">
                    <div>
                        <label for="dmd" class="{{ $labelClass }}">DMD <span class="text-red-600">*</span></label>
                        <input wire:model.defer="dmd" type="text" id="dmd" class="{{ $fieldClass('dmd') }}"
                            placeholder="Assigned practitioner" disabled readonly>
                        @error('dmd')
                            <span data-error-for="dmd" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="cost_of_treatment" class="{{ $labelClass }}">Estimated Cost <span class="text-red-600">*</span></label>
                        <input wire:model.defer="cost_of_treatment" type="text" inputmode="decimal"
                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')" id="cost_of_treatment"
                            class="{{ $fieldClass('cost_of_treatment') }}" placeholder="0.00" @if ($isReadOnly) disabled @endif>
                        @error('cost_of_treatment')
                            <span data-error-for="cost_of_treatment" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="amount_charged" class="{{ $labelClass }}">Payment <span class="text-red-600">*</span></label>
                        <input wire:model.defer="amount_charged" type="text" inputmode="decimal"
                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')" id="amount_charged"
                            class="{{ $fieldClass('amount_charged') }}" placeholder="0.00" @if ($isReadOnly) disabled @endif>
                        @error('amount_charged')
                            <span data-error-for="amount_charged" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div x-data="{ treatmentOpen: false }" class="flex h-full flex-col">
                    <div class="flex items-center justify-between gap-3">
                        <label for="treatment" class="{{ $labelClass }}">Treatment <span class="text-red-600">*</span></label>
                        @if (!$isReadOnly && !empty($selectedTreatments))
                            <span class="text-xs font-medium text-slate-500">{{ count($selectedTreatments) }} selected</span>
                        @endif
                    </div>
                    <input wire:model.defer="treatment" type="hidden" id="treatment">
                    <div class="relative flex-1">
                        <div class="{{ $fieldClass('treatment') }} flex h-full min-h-[13.5rem] flex-col px-3 py-3">
                            <div class="flex flex-wrap gap-2">
                                @if (!empty($selectedTreatments))
                                    @foreach ($selectedTreatments as $selectedTreatment)
                                        <span class="inline-flex items-center rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-sm font-medium text-sky-700">
                                            {{ $selectedTreatment }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-sm text-slate-400">Select treatment(s)...</span>
                                @endif
                            </div>

                            @if (!$isReadOnly)
                                <div class="mt-auto flex justify-end pt-4">
                                    <button type="button" @click="treatmentOpen = !treatmentOpen"
                                        class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-slate-50">
                                        {{ !empty($selectedTreatments) ? 'Select More' : 'Choose Treatments' }}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition" :class="treatmentOpen ? 'rotate-180' : ''"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        </div>

                        @if (!$isReadOnly)
                            <div x-cloak x-show="treatmentOpen" @click.outside="treatmentOpen = false"
                                class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-20 rounded-xl border border-slate-200 bg-white p-3 shadow-xl">
                                <div class="max-h-64 overflow-y-auto pr-1">
                                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                        @foreach ($treatmentOptions as $option)
                                            <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                                                <input type="checkbox" value="{{ $option }}" wire:model.live="selectedTreatments"
                                                    class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                                                <span>{{ $option }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    @error('treatment')
                        <span data-error-for="treatment" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div class="xl:col-span-2">
                    <label for="remarks" class="{{ $labelClass }}">Remarks</label>
                    <textarea wire:model.defer="remarks" id="remarks" rows="4" class="{{ $fieldClass('remarks') }}"
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
                                class="{{ $fieldClass('beforeImages') }}" accept="image/*">
                            <div wire:loading wire:target="beforeImages" class="mt-2 text-xs text-slate-500">
                                Uploading before images...
                            </div>
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
                                class="{{ $fieldClass('afterImages') }}" accept="image/*">
                            <div wire:loading wire:target="afterImages" class="mt-2 text-xs text-slate-500">
                                Uploading after images...
                            </div>
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

                    @if (!empty($beforeImages))
                        <div>
                            <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-sky-600">
                                Pending Before Images
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach ($beforeImages as $image)
                                    <button type="button"
                                        class="overflow-hidden rounded-lg border border-sky-200 bg-sky-50 p-1"
                                        @click="activeImage = '{{ $image->temporaryUrl() }}'; activeLabel = 'before preview'; showImage = true">
                                        <img class="h-32 w-full rounded-md object-cover"
                                            src="{{ $image->temporaryUrl() }}" alt="Before image preview">
                                    </button>
                                @endforeach
                            </div>
                            <p class="mt-2 text-xs text-slate-500">Preview only. These images will be saved when you save the record.</p>
                        </div>
                    @endif

                    @if (!empty($afterImages))
                        <div>
                            <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-emerald-600">
                                Pending After Images
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach ($afterImages as $image)
                                    <button type="button"
                                        class="overflow-hidden rounded-lg border border-emerald-200 bg-emerald-50 p-1"
                                        @click="activeImage = '{{ $image->temporaryUrl() }}'; activeLabel = 'after preview'; showImage = true">
                                        <img class="h-32 w-full rounded-md object-cover"
                                            src="{{ $image->temporaryUrl() }}" alt="After image preview">
                                    </button>
                                @endforeach
                            </div>
                            <p class="mt-2 text-xs text-slate-500">Preview only. These images will be saved when you save the record.</p>
                        </div>
                    @endif

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
    @endif
</div>
