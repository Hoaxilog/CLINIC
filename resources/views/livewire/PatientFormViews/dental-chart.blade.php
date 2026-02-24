<div class="relative w-full h-[68vh] bg-white border border-gray-200 rounded-lg flex flex-col lg:flex-row">

    @if (count($history) > 1 || $isCreating)

        <div class="flex-1 h-full relative flex flex-col min-w-0 bg-gray-50 transition-all duration-300">

            <div
                class="flex items-center justify-between px-6 py-4 bg-gray-50 border-b border-gray-200 sticky top-0 z-20">
                <div class="flex items-center gap-4">
                    <h2 class="text-xl font-bold text-gray-800">Dental Chart</h2>
                    @if (count($history) > 0)
                        <div class="flex items-center gap-2">
                            <select wire:model.live="selectedHistoryId"
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
                    @if ($isReadOnly && count($history) > 0)
                        <button wire:click="triggerNewChart"
                            class="flex items-center gap-2 px-3 py-2 bg-[#0086da] text-white text-sm font-medium rounded hover:bg-blue-600 transition shadow-sm"
                            title="Start a fresh chart for today">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                            New Chart
                        </button>
                    @endif
                </div>
            </div>

            <div
                class="flex-1 overflow-auto p-4 px-15 sm:px-8 lg:px-6 xl:p-10 scrollbar-thin scrollbar-thumb-rounded-full scrollbar-track-gray-100 scrollbar-thumb-gray-300">
                <livewire:PatientFormController.dental-chart-grid :teeth="$teeth" :isReadOnly="$isReadOnly" />
                <div class="max-w-6xl mx-auto flex flex-col gap-12">
                    <section class="w-full">
                        <div class="bg-blue-100 border-l-4 border-blue-500 p-4 mb-6">
                            <h2 class="text-xl font-bold text-black">Oral Exam</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 bg-white p-8">

                            {{-- ORAL HYGIENE --}}
                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-2">Oral Hygiene Status <span class="text-red-600">*</span></label>
                                <select wire:model="oralExam.oral_hygiene_status"
                                    @if ($isReadOnly) disabled @endif
                                    class="w-full border rounded px-4 py-3 text-base bg-white focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-500">
                                    <option value="" disabled>Select...</option>
                                    <option value="Excellent">Excellent</option>
                                    <option value="Good">Good</option>
                                    <option value="Fair">Fair</option>
                                    <option value="Poor">Poor</option>
                                    <option value="Bad">Bad</option>
                                </select>
                                @error('oralExam.oral_hygiene_status')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- CALCULAR DEPOSITS --}}
                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-2">Calcular Deposits <span class="text-red-600">*</span></label>
                                <select wire:model="oralExam.calcular_deposits"
                                    @if ($isReadOnly) disabled @endif
                                    class="w-full border rounded px-4 py-3 text-base bg-white focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-500">
                                    <option value="" disabled>Select...</option>
                                    <option value="None">None</option>
                                    <option value="Slight">Slight</option>
                                    <option value="Moderate">Moderate</option>
                                    <option value="Severe">Severe</option>
                                </select>
                                @error('oralExam.calcular_deposits')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- GINGIVA --}}
                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-2">Gingiva <span class="text-red-600">*</span></label>
                                <select wire:model="oralExam.gingiva" @if ($isReadOnly) disabled @endif
                                    class="w-full border rounded px-4 py-3 text-base bg-white focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-500">
                                    <option value="" disabled>Select...</option>
                                    <option value="Healthy">Healthy</option>
                                    <option value="Mildly Inflamed">Mildly Inflamed</option>
                                    <option value="Severe Inflamed">Severe Inflamed</option>
                                </select>
                                @error('oralExam.gingiva')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- STAINS --}}
                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-2">Stains <span class="text-red-600">*</span></label>
                                <select wire:model="oralExam.stains" @if ($isReadOnly) disabled @endif
                                    class="w-full border rounded px-4 py-3 text-base bg-white focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-500">
                                    <option value="" disabled>Select...</option>
                                    <option value="None">None</option>
                                    <option value="Slight">Slight</option>
                                    <option value="Moderate">Moderate</option>
                                    <option value="Severe">Severe</option>
                                </select>
                                @error('oralExam.stains')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- COMPLETE DENTURE --}}
                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-2">Complete Denture <span class="text-red-600">*</span></label>
                                <select wire:model="oralExam.complete_denture"
                                    @if ($isReadOnly) disabled @endif
                                    class="w-full border rounded px-4 py-3 text-base bg-white focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-500">
                                    <option value="" disabled>Select...</option>
                                    <option value="None">None</option>
                                    <option value="Upper">Upper</option>
                                    <option value="Lower">Lower</option>
                                    <option value="Upper & Lower">Upper & Lower</option>
                                </select>
                                @error('oralExam.complete_denture')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- PARTIAL DENTURE --}}
                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-2">Partial Denture <span class="text-red-600">*</span></label>
                                <select wire:model="oralExam.partial_denture"
                                    @if ($isReadOnly) disabled @endif
                                    class="w-full border rounded px-4 py-3 text-base bg-white focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-500">
                                    <option value="" disabled>Select...</option>
                                    <option value="None">None</option>
                                    <option value="Upper">Upper</option>
                                    <option value="Lower">Lower</option>
                                    <option value="Upper & Lower">Upper & Lower</option>
                                </select>
                                @error('oralExam.partial_denture')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <section class="w-full">
                        <div class="bg-blue-100 border-l-4 border-blue-500 p-4 mb-6">
                            <h2 class="text-xl font-bold text-black">Comments / Plan</h2>
                        </div>
                        <div class="space-y-8 bg-white p-8">
                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-2">Comments / Notes</label>
                                <textarea wire:model="chartComments.notes" rows="5"
                                    class="w-full border rounded px-4 py-3 text-base focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-500"
                                    placeholder="Enter observation notes..." @if ($isReadOnly) disabled @endif></textarea>
                            </div>
                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-2">Treatment Plan</label>
                                <textarea wire:model="chartComments.treatment_plan" rows="5"
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
            <button wire:click="triggerNewChart"
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
