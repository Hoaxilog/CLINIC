<div class="relative flex h-[68vh] w-full flex-col rounded-2xl border border-slate-200 bg-white lg:flex-row"
    x-data="{ chartLoading: {{ (count($history) > 0 || $isCreating) ? 'true' : 'false' }} }"
    x-on:show-dental-loading.window="chartLoading = true"
    x-on:dental-chart-ready.window="chartLoading = false">
    <div x-cloak x-show="chartLoading"
        class="absolute inset-0 z-30 flex items-center justify-center bg-white/70 backdrop-blur-sm text-center">
        <div class="flex flex-col items-center gap-3">
            <div class="h-10 w-10 animate-spin rounded-full border-4 border-blue-200 border-t-[#0086da]"></div>
            <div class="text-sm font-semibold text-gray-700">Loading dental chart...</div>
        </div>
    </div>

    @if (count($history) > 0 || $isCreating)

        <div class="relative flex h-full min-w-0 flex-1 flex-col bg-slate-50 transition-all duration-300">

            <div
                class="sticky top-0 z-20 flex items-center justify-between border-b border-slate-200 bg-white px-5 py-4">
                <div class="flex items-center gap-4">
                    <h2 class="text-lg font-semibold text-slate-900">Dental Chart</h2>
                    @if (count($history) > 0)
                        <div class="flex items-center gap-2">
                            <select wire:model.live="selectedHistoryId" x-on:change="$dispatch('show-dental-loading')"
                                class="px-3 py-2 border border-gray-300 rounded-md text-sm w-full focus:border-blue-500 focus:ring-blue-500 min-w-[200px] disabled:bg-gray-100 disabled:text-gray-400"
                                @if (!$isReadOnly) disabled @endif>
                                <option value="" disabled>Select History Record...</option>
                                @foreach ($history as $record)
                                    <option value="{{ $record['id'] }}">{{ $record['date'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    <div class="inline-flex items-center rounded-lg border border-slate-300 bg-white p-1">
                        <button type="button" wire:click="$set('dentitionType','adult')"
                            @if ($isReadOnly) disabled @endif
                            class="rounded-md px-3 py-1.5 text-xs font-semibold transition {{ $dentitionType === 'adult' ? 'bg-sky-600 text-white' : 'text-slate-600 hover:bg-slate-100' }} disabled:cursor-not-allowed disabled:opacity-50">
                            Adult
                        </button>
                        <button type="button" wire:click="$set('dentitionType','child')"
                            @if ($isReadOnly) disabled @endif
                            class="rounded-md px-3 py-1.5 text-xs font-semibold transition {{ $dentitionType === 'child' ? 'bg-sky-600 text-white' : 'text-slate-600 hover:bg-slate-100' }} disabled:cursor-not-allowed disabled:opacity-50">
                            Child
                        </button>
                    </div>
                    @if ($isReadOnly && count($history) > 0)
                        <button wire:click="triggerNewChart" x-on:click="$dispatch('show-dental-loading')"
                            class="inline-flex items-center gap-2 rounded-lg bg-sky-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-sky-700"
                            title="Start a fresh chart for today">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                            <span wire:loading.remove wire:target="triggerNewChart">New Chart</span>
                            <span wire:loading wire:target="triggerNewChart">Loading...</span>
                        </button>
                    @endif
                </div>
            </div>

            <div data-form-scroll
                class="flex-1 overflow-auto p-4 sm:px-6 lg:px-6 xl:p-8 scrollbar-thin scrollbar-thumb-rounded-full scrollbar-track-slate-100 scrollbar-thumb-slate-300">
                <livewire:patient.form.dental-chart-grid :teeth="$teeth" :isReadOnly="$isReadOnly" :dentitionType="$dentitionType" />
                <div class="max-w-6xl mx-auto flex flex-col gap-12">
                    <section class="w-full">
                        <div class="mb-6 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <h2 class="text-base font-semibold text-slate-900">Oral Exam</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 bg-white p-8">
                            @php
                                $errorBag = session('errors');
                                $selectClass = fn(string $field) => ($errorBag && $errorBag->has($field))
                                    ? 'w-full border border-red-500 rounded px-4 py-3 text-base bg-white focus:ring-red-200 focus:border-red-500 disabled:bg-gray-100 disabled:text-gray-500'
                                    : 'w-full border rounded px-4 py-3 text-base bg-white focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-500';
                            @endphp

                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-2">Oral Hygiene Status <span class="text-red-600">*</span></label>
                                <select wire:model.defer="oralExam.oral_hygiene_status"
                                    @if ($isReadOnly) disabled @endif
                                    class="{{ $selectClass('oralExam.oral_hygiene_status') }}">
                                    <option value="" disabled>Select...</option>
                                    <option value="Excellent">Excellent</option>
                                    <option value="Good">Good</option>
                                    <option value="Fair">Fair</option>
                                    <option value="Poor">Poor</option>
                                    <option value="Bad">Bad</option>
                                </select>
                                @error('oralExam.oral_hygiene_status')
                                    <span data-error-for="oralExam.oral_hygiene_status" class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-2">Calcular Deposits <span class="text-red-600">*</span></label>
                                <select wire:model.defer="oralExam.calcular_deposits"
                                    @if ($isReadOnly) disabled @endif
                                    class="{{ $selectClass('oralExam.calcular_deposits') }}">
                                    <option value="" disabled>Select...</option>
                                    <option value="None">None</option>
                                    <option value="Slight">Slight</option>
                                    <option value="Moderate">Moderate</option>
                                    <option value="Severe">Severe</option>
                                </select>
                                @error('oralExam.calcular_deposits')
                                    <span data-error-for="oralExam.calcular_deposits" class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-2">Gingiva <span class="text-red-600">*</span></label>
                                <select wire:model.defer="oralExam.gingiva" @if ($isReadOnly) disabled @endif
                                    class="{{ $selectClass('oralExam.gingiva') }}">
                                    <option value="" disabled>Select...</option>
                                    <option value="Healthy">Healthy</option>
                                    <option value="Mildly Inflamed">Mildly Inflamed</option>
                                    <option value="Severe Inflamed">Severe Inflamed</option>
                                </select>
                                @error('oralExam.gingiva')
                                    <span data-error-for="oralExam.gingiva" class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-2">Stains <span class="text-red-600">*</span></label>
                                <select wire:model.defer="oralExam.stains" @if ($isReadOnly) disabled @endif
                                    class="{{ $selectClass('oralExam.stains') }}">
                                    <option value="" disabled>Select...</option>
                                    <option value="None">None</option>
                                    <option value="Slight">Slight</option>
                                    <option value="Moderate">Moderate</option>
                                    <option value="Severe">Severe</option>
                                </select>
                                @error('oralExam.stains')
                                    <span data-error-for="oralExam.stains" class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-2">Complete Denture <span class="text-red-600">*</span></label>
                                <select wire:model.defer="oralExam.complete_denture"
                                    @if ($isReadOnly) disabled @endif
                                    class="{{ $selectClass('oralExam.complete_denture') }}">
                                    <option value="" disabled>Select...</option>
                                    <option value="None">None</option>
                                    <option value="Upper">Upper</option>
                                    <option value="Lower">Lower</option>
                                    <option value="Upper & Lower">Upper & Lower</option>
                                </select>
                                @error('oralExam.complete_denture')
                                    <span data-error-for="oralExam.complete_denture" class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-2">Partial Denture <span class="text-red-600">*</span></label>
                                <select wire:model.defer="oralExam.partial_denture"
                                    @if ($isReadOnly) disabled @endif
                                    class="{{ $selectClass('oralExam.partial_denture') }}">
                                    <option value="" disabled>Select...</option>
                                    <option value="None">None</option>
                                    <option value="Upper">Upper</option>
                                    <option value="Lower">Lower</option>
                                    <option value="Upper & Lower">Upper & Lower</option>
                                </select>
                                @error('oralExam.partial_denture')
                                    <span data-error-for="oralExam.partial_denture" class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <section class="w-full">
                        <div class="mb-6 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <h2 class="text-base font-semibold text-slate-900">Comments / Plan</h2>
                        </div>
                        <div class="space-y-8 bg-white p-8">
                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-2">Comments / Notes</label>
                                <textarea wire:model.defer="chartComments.notes" rows="5"
                                    class="w-full border rounded px-4 py-3 text-base focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-500"
                                    placeholder="Enter observation notes..." @if ($isReadOnly) disabled @endif></textarea>
                            </div>
                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-2">Treatment Plan</label>
                                <textarea wire:model.defer="chartComments.treatment_plan" rows="5"
                                    class="w-full border rounded px-4 py-3 text-base focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-500"
                                    placeholder="Enter proposed treatment..." @if ($isReadOnly) disabled @endif></textarea>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    @else
        <div class="w-full h-full flex flex-col items-center justify-center bg-gray-50 p-10 text-center space-y-6">
            <div class="bg-blue-50 p-6 rounded-full">
                <svg class="w-20 h-20 text-[#0086da]" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-800">No Dental Chart History</h3>
                <p class="text-gray-500 mt-2 max-w-md mx-auto">This patient does not have any dental records yet. Click
                    the button below to create the first chart.</p>
            </div>
            <button wire:click="triggerNewChart" x-on:click="$dispatch('show-dental-loading')"
                class="flex items-center gap-2 px-6 py-3 bg-[#0086da] text-white text-lg font-bold rounded-lg shadow-lg hover:scale-105 transition-all transform">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M12 5v14M5 12h14" />
                </svg>
                Add Dental Chart
            </button>
        </div>
    @endif
</div>
