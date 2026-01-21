<div class="relative w-full h-full bg-white border border-gray-200 rounded-lg overflow-hidden flex flex-col">
    {{-- CONDITION: Show Form if History Exists OR We are Creating a New One --}}
    @if (count($historyList) > 0 || $isCreating)

        <div class="flex items-center justify-between px-6 py-4 bg-gray-50 border-b border-gray-200 sticky top-0 z-20">
            <div class="flex items-center gap-4">
                <h2 class="text-xl font-bold text-gray-800">Health History</h2>

                {{-- History Dropdown: DO NOT DISABLE (User needs to switch views) --}}
                @if (count($historyList) > 0 && $isReadOnly && !$isCreating)
                    <div class="flex items-center gap-2">
                        <select wire:model.live="selectedHistoryId"
                            class="px-3 py-2 border border-gray-300 rounded-md text-sm w-full focus:border-blue-500 focus:ring-blue-500 min-w-[200px]">
                            
                            @if($isCreating)
                                <option value="new" class="font-bold text-blue-600">New Record (Unsaved)</option>
                            @else
                                <option value="" disabled>Select History Record...</option>
                            @endif
                                                
                            <optgroup label="Past Records">
                                @foreach ($historyList as $history)
                                    <option value="{{ $history['id'] }}">
                                        📅 {{ $history['label'] }}
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-3">
                {{-- New Record Button: ALWAYS CLICKABLE --}}
                @if (count($historyList) > 0 && $isReadOnly && !$isCreating)
                    <button wire:click="triggerNewHistory"
                        class="flex items-center gap-2 px-3 py-2 bg-[#0086da] text-white text-sm font-medium rounded hover:bg-blue-600 transition shadow-sm"
                        title="Start a fresh health record">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                        New Record
                    </button>
                @endif
            </div>
        </div>

        <div
            class="flex-1 overflow-y-auto p-4 lg:p-8 scrollbar-thin scrollbar-thumb-rounded-full scrollbar-track-gray-100 scrollbar-thumb-gray-300">

            <div class="bg-blue-100 border-l-4 border-blue-500 p-4 mb-6">
                <h2 class="text-xl font-bold text-black">Dental History</h2>
            </div>

            <div class="space-y-6 mb-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-lg font-medium text-gray-700 mb-2">1. Date of last dental
                            visit:</label>
                        <input @if ($isReadOnly && !$isCreating) disabled @endif wire:model="when_last_visit_q1" type="date"
                            class="w-full border rounded px-4 py-3 text-base focus:ring-blue-500 focus:border-blue-500">
                        @error('when_last_visit_q1')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-lg font-medium text-gray-700 mb-2">What was done?</label>
                        <input @if ($isReadOnly && !$isCreating) disabled @endif wire:model="what_last_visit_reason_q1" type="text"
                            class="w-full border rounded px-4 py-3 text-base focus:ring-blue-500 focus:border-blue-500"
                            placeholder="e.g., Cleaning, Filling...">
                        @error('what_last_visit_reason_q1')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-lg font-medium text-gray-700 mb-2">2. Reason for seeing dentist
                        today?</label>
                    <input @if ($isReadOnly && !$isCreating) disabled @endif wire:model="what_seeing_dentist_reason_q2" type="text"
                        class="w-full border rounded px-4 py-3 text-base focus:ring-blue-500 focus:border-blue-500"
                        placeholder="e.g., Check-up...">
                    @error('what_seeing_dentist_reason_q2')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-lg font-medium text-gray-700 mb-2">3. Have you experienced:</label>
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 pl-4 bg-gray-50 p-4 rounded-lg border border-gray-100">

                        @foreach ([['label' => 'A. Clicking of the Jaw?', 'model' => 'is_clicking_jaw_q3a'], ['label' => 'B. Pain below the ear?', 'model' => 'is_pain_jaw_q3b'], ['label' => 'C. Difficulty opening/closing?', 'model' => 'is_difficulty_opening_closing_q3c'], ['label' => 'D. Locking of the jaw?', 'model' => 'is_locking_jaw_q3d']] as $q)
                            <div>
                                <label class="block text-base font-medium text-gray-700">{{ $q['label'] }}</label>
                                <div class="flex gap-x-6 mt-1">
                                    <label class="flex items-center cursor-pointer">
                                        <input @if ($isReadOnly && !$isCreating) disabled @endif wire:model.live="{{ $q['model'] }}"
                                            type="radio" value="1"
                                            class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <span class="ml-2 text-sm font-bold text-blue-700">YES</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input @if ($isReadOnly && !$isCreating) disabled @endif wire:model.live="{{ $q['model'] }}"
                                            type="radio" value="0"
                                            class="h-4 w-4 text-gray-400 border-gray-300 focus:ring-gray-500">
                                        <span class="ml-2 text-sm text-gray-600">NO</span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @foreach ([['q' => '4. Do you clench or grind your teeth?', 'model' => 'is_clench_grind_q4'], ['q' => '5. Bad experience in dental office?', 'model' => 'is_bad_experience_q5'], ['q' => '6. Feel nervous about treatment?', 'model' => 'is_nervous_q6', 'detail' => 'what_nervous_concern_q6', 'placeholder' => 'What is your concern?']] as $item)
                    <div class="border-b border-gray-100 pb-4">
                        <label class="block text-lg font-medium text-gray-700">{{ $item['q'] }}</label>
                        <div class="flex gap-x-6 mt-2">
                            <label class="flex items-center cursor-pointer">
                                <input @if ($isReadOnly && !$isCreating) disabled @endif wire:model.live="{{ $item['model'] }}"
                                    type="radio" value="1" class="h-4 w-4 text-blue-600">
                                <span class="ml-2 text-sm font-bold text-blue-700">YES</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input @if ($isReadOnly && !$isCreating) disabled @endif wire:model.live="{{ $item['model'] }}"
                                    type="radio" value="0" class="h-4 w-4 text-gray-400">
                                <span class="ml-2 text-sm text-gray-600">NO</span>
                            </label>
                        </div>
                        {{-- Conditional Detail Input --}}
                        @if (isset($item['detail']) && $this->{$item['model']})
                            <div class="mt-2 pl-4 border-l-2 border-blue-200">
                                <input @if ($isReadOnly && !$isCreating) disabled @endif wire:model="{{ $item['detail'] }}" type="text"
                                    class="w-full border rounded px-3 py-2 text-sm"
                                    placeholder="{{ $item['placeholder'] }}">
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="bg-blue-100 border-l-4 border-blue-500 p-4 mb-6">
                <h2 class="text-xl font-bold text-black">Medical History</h2>
            </div>

            <div class="space-y-6">
                @foreach ([['q' => '1. Treated for medical condition (present/past 2 years)?', 'model' => 'is_condition_q1', 'detail' => 'what_condition_reason_q1'], ['q' => '2. Ever been hospitalized?', 'model' => 'is_hospitalized_q2', 'detail' => 'what_hospitalized_reason_q2'], ['q' => '3. Serious illness or operation?', 'model' => 'is_serious_illness_operation_q3', 'detail' => 'what_serious_illness_operation_reason_q3'], ['q' => '4. Taking any medications?', 'model' => 'is_taking_medications_q4', 'detail' => 'what_medications_list_q4'], ['q' => '5. Allergic to medications?', 'model' => 'is_allergic_medications_q5', 'detail' => 'what_allergies_list_q5'], ['q' => '6. Allergic to latex/rubber/metals?', 'model' => 'is_allergic_latex_rubber_metals_q6']] as $med)
                    <div class="border-b border-gray-100 pb-4">
                        <label class="block text-lg font-medium text-gray-700">{{ $med['q'] }}</label>
                        <div class="flex gap-x-6 mt-2">
                            <label class="flex items-center cursor-pointer">
                                <input @if ($isReadOnly && !$isCreating) disabled @endif wire:model.live="{{ $med['model'] }}"
                                    type="radio" value="1" class="h-4 w-4 text-blue-600">
                                <span class="ml-2 text-sm font-bold text-blue-700">YES</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input @if ($isReadOnly && !$isCreating) disabled @endif wire:model.live="{{ $med['model'] }}"
                                    type="radio" value="0" class="h-4 w-4 text-gray-400">
                                <span class="ml-2 text-sm text-gray-600">NO</span>
                            </label>
                        </div>
                        @if (isset($med['detail']) && $this->{$med['model']})
                            <div class="mt-2 pl-4 border-l-2 border-blue-200">
                                <input @if ($isReadOnly && !$isCreating) disabled @endif wire:model="{{ $med['detail'] }}" type="text"
                                    class="w-full border rounded px-3 py-2 text-sm"
                                    placeholder="Please specify details...">
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            @if ($gender === 'Female')
                <div class="bg-pink-100 border-l-4 border-pink-500 p-4 mb-6 mt-10">
                    <h2 class="text-xl font-bold text-pink-800">For Women Only</h2>
                </div>
                <div class="space-y-4">
                    @foreach ([['q' => '7. Are you pregnant?', 'model' => 'is_pregnant_q7'], ['q' => '8. Are you breast feeding?', 'model' => 'is_breast_feeding_q8']] as $fem)
                        <div>
                            <label class="block text-lg font-medium text-gray-700">{{ $fem['q'] }}</label>
                            <div class="flex gap-x-6 mt-2">
                                <label class="flex items-center cursor-pointer">
                                    <input @if ($isReadOnly && !$isCreating) disabled @endif wire:model.live="{{ $fem['model'] }}"
                                        type="radio" value="1" class="h-4 w-4 text-pink-600">
                                    <span class="ml-2 text-sm font-bold text-pink-700">YES</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input @if ($isReadOnly && !$isCreating) disabled @endif wire:model.live="{{ $fem['model'] }}"
                                        type="radio" value="0" class="h-4 w-4 text-gray-400">
                                    <span class="ml-2 text-sm text-gray-600">NO</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @else
        <div class="w-full h-full flex flex-col items-center justify-center bg-gray-50 p-10 text-center space-y-6">
            <div class="bg-blue-50 p-6 rounded-full">
                <svg class="w-20 h-20 text-[#0086da]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                    </path>
                </svg>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-800">No Health History Record</h3>
                <p class="text-gray-500 mt-2 max-w-md mx-auto">This patient does not have any medical records yet.
                    Please add a record before proceeding.</p>
            </div>
            <button wire:click="triggerNewHistory"
                class="flex items-center gap-2 px-6 py-3 bg-[#0086da] text-white text-lg font-bold rounded-lg shadow-lg hover:scale-105 transition-all transform">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M12 5v14M5 12h14" />
                </svg>
                Add Health History
            </button>
        </div>
    @endif
</div>
