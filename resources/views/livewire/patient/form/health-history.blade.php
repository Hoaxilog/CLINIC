<div class="relative flex h-full w-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white"
    x-data="{
        historyLoading: {{ (count($historyList) > 0 || $isCreating) ? 'true' : 'false' }},
        nervous: @js((string) $is_nervous_q6),
        condition: @js((string) $is_condition_q1),
        hospitalized: @js((string) $is_hospitalized_q2),
        seriousIllness: @js((string) $is_serious_illness_operation_q3),
        medications: @js((string) $is_taking_medications_q4),
        allergies: @js((string) $is_allergic_medications_q5),
        otherCondition: @js((string) $is_other_disease_condition_problem)
    }"
    x-on:show-health-history-loading.window="historyLoading = true"
    x-on:sync-health-history-ui.window="
        nervous = $event.detail?.nervous ?? '';
        condition = $event.detail?.condition ?? '';
        hospitalized = $event.detail?.hospitalized ?? '';
        seriousIllness = $event.detail?.seriousIllness ?? '';
        medications = $event.detail?.medications ?? '';
        allergies = $event.detail?.allergies ?? '';
        otherCondition = $event.detail?.otherCondition ?? '';
    "
    x-on:health-history-ready.window="historyLoading = false">
    @php
        $canStartNewRecord = auth()->check() && (auth()->user()?->canHandleChairsideFlow() ?? false);
    @endphp
    <div x-cloak x-show="historyLoading"
        class="absolute inset-0 z-30 flex items-center justify-center bg-white/70 backdrop-blur-sm text-center">
        <div class="flex flex-col items-center gap-3">
            <div class="h-10 w-10 animate-spin rounded-full border-4 border-blue-200 border-t-[#0086da]"></div>
            <div class="text-sm font-semibold text-gray-700">Loading health history...</div>
        </div>
    </div>
    @if (count($historyList) > 0 || $isCreating)

        <div class="sticky top-0 z-20 flex items-center justify-between border-b border-slate-200 bg-white px-5 py-4"
            x-init="$nextTick(() => { $dispatch('health-history-ready'); })">
            <div class="flex items-center gap-4">
                <h2 class="text-lg font-semibold text-slate-900">Health History</h2>
            </div>
        </div>

        <div data-health-history-scroll data-form-scroll
            class="flex-1 overflow-y-auto bg-[linear-gradient(180deg,#f8fbff_0%,#ffffff_18rem)] p-4 lg:p-6 scrollbar-thin scrollbar-thumb-rounded-full scrollbar-track-slate-100 scrollbar-thumb-slate-300">

            @php
                $lastVisitDateClass = $errors->has('when_last_visit_q1')
                    ? 'w-full border border-red-500 rounded px-4 py-3 text-base focus:ring-red-200 focus:border-red-500'
                    : 'w-full border rounded px-4 py-3 text-base focus:ring-blue-500 focus:border-blue-500';
                $lastVisitReasonClass = $errors->has('what_last_visit_reason_q1')
                    ? 'w-full border border-red-500 rounded px-4 py-3 text-base focus:ring-red-200 focus:border-red-500'
                    : 'w-full border rounded px-4 py-3 text-base focus:ring-blue-500 focus:border-blue-500';
                $todayReasonClass = $errors->has('what_seeing_dentist_reason_q2')
                    ? 'w-full border border-red-500 rounded px-4 py-3 text-base focus:ring-red-200 focus:border-red-500'
                    : 'w-full border rounded px-4 py-3 text-base focus:ring-blue-500 focus:border-blue-500';
            @endphp

            <section class="mb-8 rounded-md border border-slate-200/80 bg-white/95 p-5 shadow-[0_18px_50px_-32px_rgba(15,23,42,0.22)] ring-1 ring-white">
                <div class="mb-6 rounded-md border border-sky-100 bg-[linear-gradient(135deg,#eef7ff_0%,#f8fbff_100%)] px-4 py-4">
                    <h2 class="text-lg font-semibold text-slate-900">Dental History</h2>
                </div>

                <div class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="rounded-md border border-slate-200 bg-slate-50/80 p-4">
                        <label class="mb-2 block text-base font-semibold text-slate-800">1. Date of last dental visit <span class="text-xs font-medium text-slate-500">(Optional)</span></label>
                        <input @if ($isReadOnly) disabled @endif wire:model.defer="when_last_visit_q1" type="date"
                            class="{{ $lastVisitDateClass }}">
                        @error('when_last_visit_q1')
                            <span data-error-for="when_last_visit_q1" class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="rounded-md border border-slate-200 bg-slate-50/80 p-4">
                        <label class="mb-2 block text-base font-semibold text-slate-800">What was done in your last dental visit <span class="text-xs font-medium text-slate-500">(Optional)</span></label>
                        <input @if ($isReadOnly) disabled @endif wire:model.defer="what_last_visit_reason_q1" type="text"
                            class="{{ $lastVisitReasonClass }}"
                            placeholder="e.g., Cleaning, Filling...">
                        @error('what_last_visit_reason_q1')
                            <span data-error-for="what_last_visit_reason_q1" class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="rounded-md border border-slate-200 bg-white p-4">
                    <label class="mb-2 block text-base font-semibold text-slate-800">2. Reason for seeing dentist
                        today? <span class="text-red-600">*</span></label>
                    <input @if ($isReadOnly) disabled @endif wire:model.defer="what_seeing_dentist_reason_q2" type="text"
                        class="{{ $todayReasonClass }}"
                        placeholder="e.g., Check-up...">
                    @error('what_seeing_dentist_reason_q2')
                        <span data-error-for="what_seeing_dentist_reason_q2" class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="rounded-md border border-slate-200 bg-white p-4">
                    <label class="mb-3 block text-base font-semibold text-slate-800">3. Have you experienced? <span class="text-red-600">*</span></label>
                    <div
                        class="grid grid-cols-1 gap-4 md:grid-cols-2 bg-slate-50/90 p-4 rounded-md border border-slate-200">

                        @foreach ([['label' => 'A. Clicking of the Jaw?', 'model' => 'is_clicking_jaw_q3a'], ['label' => 'B. Pain below the ear?', 'model' => 'is_pain_jaw_q3b'], ['label' => 'C. Difficulty opening/closing?', 'model' => 'is_difficulty_opening_closing_q3c'], ['label' => 'D. Locking of the jaw?', 'model' => 'is_locking_jaw_q3d']] as $q)
                            <div class="rounded-md border border-white bg-white px-4 py-3 shadow-sm">
                                <label class="block text-sm font-semibold text-slate-700">{{ $q['label'] }}</label>
                                <div class="mt-2 flex gap-x-6">
                                    <label class="flex items-center cursor-pointer">
                                        <input @if ($isReadOnly) disabled @endif wire:model.defer="{{ $q['model'] }}"
                                            type="radio" value="1"
                                            class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-600">YES</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input @if ($isReadOnly) disabled @endif wire:model.defer="{{ $q['model'] }}"
                                            type="radio" value="0"
                                            class="h-4 w-4 text-gray-400 border-gray-300 focus:ring-gray-500">
                                        <span class="ml-2 text-sm text-gray-600">NO</span>
                                    </label>
                                </div>
                                @error($q['model'])
                                    <span data-error-for="{{ $q['model'] }}" class="text-red-500 text-sm block mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>

                @foreach ([['q' => '4. Do you clench or grind your teeth?', 'model' => 'is_clench_grind_q4'], ['q' => '5. Bad experience in dental office?', 'model' => 'is_bad_experience_q5'], ['q' => '6. Feel nervous about treatment?', 'model' => 'is_nervous_q6', 'detail' => 'what_nervous_concern_q6', 'placeholder' => 'What is your concern?']] as $item)
                    <div class="rounded-md border border-slate-200 bg-white p-4">
                        <label class="block text-base font-semibold text-slate-800">{{ $item['q'] }} <span class="text-red-600">*</span></label>
                        <div class="flex gap-x-6 mt-2">
                            <label class="flex items-center cursor-pointer">
                                <input @if ($isReadOnly) disabled @endif wire:model.defer="{{ $item['model'] }}"
                                    @if (($item['model'] ?? null) === 'is_nervous_q6') x-model="nervous" @endif
                                    type="radio" value="1" class="h-4 w-4 text-blue-600">
                                <span class="ml-2 text-sm text-gray-600">YES</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input @if ($isReadOnly) disabled @endif wire:model.defer="{{ $item['model'] }}"
                                    @if (($item['model'] ?? null) === 'is_nervous_q6') x-model="nervous" @endif
                                    type="radio" value="0" class="h-4 w-4 text-gray-400">
                                <span class="ml-2 text-sm text-gray-600">NO</span>
                            </label>
                        </div>
                        
                        @error($item['model'])
                            <span data-error-for="{{ $item['model'] }}" class="text-red-500 text-sm block mt-1">{{ $message }}</span>
                        @enderror

                        @if (isset($item['detail']))
                            <div x-cloak x-show="nervous === '1'" class="mt-3 rounded-md border border-sky-100 bg-sky-50/70 p-3">
                                <input @if ($isReadOnly) disabled @endif wire:model.defer="{{ $item['detail'] }}" type="text"
                                    class="w-full border rounded px-3 py-2 text-sm @error($item['detail']) border-red-500 focus:border-red-500 focus:ring-red-200 @enderror"
                                    placeholder="{{ $item['placeholder'] }}">
                                
                                @error($item['detail'])
                                    <span data-error-for="{{ $item['detail'] }}" class="text-red-500 text-sm block mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                    </div>
                @endforeach
                </div>
            </section>

            <section class="rounded-md border border-slate-200/80 bg-white/95 p-5 shadow-[0_18px_50px_-32px_rgba(15,23,42,0.22)] ring-1 ring-white">
                <div class="mb-6 rounded-md border border-emerald-100 bg-[linear-gradient(135deg,#effcf6_0%,#f8fcfa_100%)] px-4 py-4">
                    <h2 class="text-lg font-semibold text-slate-900">Medical History</h2>
                </div>

            <div class="space-y-6">
                @foreach ([['q' => '1. Treated for medical condition (present/past 2 years)?', 'model' => 'is_condition_q1', 'detail' => 'what_condition_reason_q1'], ['q' => '2. Ever been hospitalized?', 'model' => 'is_hospitalized_q2', 'detail' => 'what_hospitalized_reason_q2'], ['q' => '3. Serious illness or operation?', 'model' => 'is_serious_illness_operation_q3', 'detail' => 'what_serious_illness_operation_reason_q3'], ['q' => '4. Taking any medications?', 'model' => 'is_taking_medications_q4', 'detail' => 'what_medications_list_q4'], ['q' => '5. Allergic to medications?', 'model' => 'is_allergic_medications_q5', 'detail' => 'what_allergies_list_q5'], ['q' => '6. Allergic to latex/rubber/metals?', 'model' => 'is_allergic_latex_rubber_metals_q6']] as $med)
                    <div class="rounded-md border border-slate-200 bg-white p-4">
                        <label class="block text-base font-semibold text-slate-800">{{ $med['q'] }} <span class="text-red-600">*</span></label>
                        <div class="flex gap-x-6 mt-2">
                            <label class="flex items-center cursor-pointer">
                                <input @if ($isReadOnly) disabled @endif wire:model.defer="{{ $med['model'] }}"
                                    @if (($med['model'] ?? null) === 'is_condition_q1') x-model="condition" @endif
                                    @if (($med['model'] ?? null) === 'is_hospitalized_q2') x-model="hospitalized" @endif
                                    @if (($med['model'] ?? null) === 'is_serious_illness_operation_q3') x-model="seriousIllness" @endif
                                    @if (($med['model'] ?? null) === 'is_taking_medications_q4') x-model="medications" @endif
                                    @if (($med['model'] ?? null) === 'is_allergic_medications_q5') x-model="allergies" @endif
                                    type="radio" value="1" class="h-4 w-4 text-blue-600">
                                <span class="ml-2 text-sm text-gray-600">YES</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input @if ($isReadOnly) disabled @endif wire:model.defer="{{ $med['model'] }}"
                                    @if (($med['model'] ?? null) === 'is_condition_q1') x-model="condition" @endif
                                    @if (($med['model'] ?? null) === 'is_hospitalized_q2') x-model="hospitalized" @endif
                                    @if (($med['model'] ?? null) === 'is_serious_illness_operation_q3') x-model="seriousIllness" @endif
                                    @if (($med['model'] ?? null) === 'is_taking_medications_q4') x-model="medications" @endif
                                    @if (($med['model'] ?? null) === 'is_allergic_medications_q5') x-model="allergies" @endif
                                    type="radio" value="0" class="h-4 w-4 text-gray-400">
                                <span class="ml-2 text-sm text-gray-600">NO</span>
                            </label>
                        </div>

                        @error($med['model'])
                            <span data-error-for="{{ $med['model'] }}" class="text-red-500 text-sm block mt-1">{{ $message }}</span>
                        @enderror

                        @if (isset($med['detail']))
                            <div
                                x-cloak
                                x-show="
                                    ('{{ $med['model'] }}' === 'is_condition_q1' && condition === '1') ||
                                    ('{{ $med['model'] }}' === 'is_hospitalized_q2' && hospitalized === '1') ||
                                    ('{{ $med['model'] }}' === 'is_serious_illness_operation_q3' && seriousIllness === '1') ||
                                    ('{{ $med['model'] }}' === 'is_taking_medications_q4' && medications === '1') ||
                                    ('{{ $med['model'] }}' === 'is_allergic_medications_q5' && allergies === '1')
                                "
                                class="mt-3 rounded-md border border-emerald-100 bg-emerald-50/70 p-3">
                                <input @if ($isReadOnly) disabled @endif wire:model.defer="{{ $med['detail'] }}" type="text"
                                    class="w-full border rounded px-3 py-2 text-sm @error($med['detail']) border-red-500 focus:border-red-500 focus:ring-red-200 @enderror"
                                    placeholder="Please specify details...">
                                
                                @error($med['detail'])
                                    <span data-error-for="{{ $med['detail'] }}" class="text-red-500 text-sm block mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                    </div>
                @endforeach

                <div class="rounded-md border border-slate-200 bg-slate-50/80 px-4 py-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h3 class="text-base font-semibold text-slate-900">Medical Conditions Checklist</h3>
                            <p class="mt-1 text-sm text-slate-600">Mark all listed conditions with Yes or No before continuing.</p>
                        </div>
                        @if (!$isReadOnly)
                            <button
                                type="button"
                                id="no-to-all-conditions-btn"
                                wire:click="setAllConditionsToNo"
                                wire:loading.attr="disabled"
                                wire:target="setAllConditionsToNo"
                                class="inline-flex shrink-0 items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm font-semibold text-slate-700 shadow-sm transition-all hover:bg-red-50 hover:border-red-300 hover:text-red-700 active:scale-95 disabled:opacity-60 disabled:cursor-not-allowed"
                                title="Set all medical condition questions to NO">
                                {{-- Loading state: spinner + Loading text --}}
                                <span wire:loading.flex wire:target="setAllConditionsToNo" class="items-center gap-1.5">
                                    <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    Loading...
                                </span>
                                {{-- Default state: icon + label --}}
                                <span wire:loading.remove wire:target="setAllConditionsToNo" class="inline-flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round`    `   `   `   `   `   `" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                    No to All
                                </span>
                            </button>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ([['label' => 'Chest Pain / Angina', 'model' => 'is_chest_pain_angina'], ['label' => 'Asthma', 'model' => 'is_asthma'], ['label' => 'Shortness of Breath', 'model' => 'is_shortness_of_breath'], ['label' => 'Tuberculosis', 'model' => 'is_tuberculosis'], ['label' => 'Heart Disease / Heart Attack', 'model' => 'is_heart_disease_heart_attack'], ['label' => 'Blood Disease', 'model' => 'is_blood_disease'], ['label' => 'Heart Surgery', 'model' => 'is_heart_surgery'], ['label' => 'Bleeding Problems / Bleeding Disorders', 'model' => 'is_bleeding_problems_disorders'], ['label' => 'Artificial Heart Valve / Pacemaker', 'model' => 'is_artificial_heart_valve_pacemaker'], ['label' => 'Diabetes', 'model' => 'is_diabetes'], ['label' => 'Rheumatic Fever / Rheumatic Heart Disease', 'model' => 'is_rheumatic_fever_heart_disease'], ['label' => 'Liver Problem / Jaundice / Hepatitis', 'model' => 'is_liver_problem_jaundice_hepatitis'], ['label' => 'Heart Murmur', 'model' => 'is_heart_murmur'], ['label' => 'Kidney Problem / Bladder Problem', 'model' => 'is_kidney_bladder_problem'], ['label' => 'Mitral Valve Prolapse', 'model' => 'is_mitral_valve_prolapse'], ['label' => 'Ulcers / Hyperacidity', 'model' => 'is_ulcers_hyperacidity'], ['label' => 'High Blood Pressure / Low Blood Pressure', 'model' => 'is_high_low_blood_pressure'], ['label' => 'Tumors / Cancer / Malignancies', 'model' => 'is_tumors_cancer_malignancies'], ['label' => 'Stroke', 'model' => 'is_stroke'], ['label' => 'AIDS / HIV Positive', 'model' => 'is_aids_hiv_positive'], ['label' => 'Respiratory Problem / Lung Problem', 'model' => 'is_respiratory_lung_problem'], ['label' => 'Fainting / Epilepsy / Seizures', 'model' => 'is_fainting_epilepsy_seizures'], ['label' => 'Emphysema', 'model' => 'is_emphysema'], ['label' => 'Mental Health Disorder', 'model' => 'is_mental_health_disorder']] as $condition)
                        <div class="rounded-md border border-slate-200 bg-white p-4">
                            <label class="block text-base font-semibold text-slate-800">{{ $condition['label'] }} <span class="text-red-600">*</span></label>
                            <div class="mt-3 flex gap-x-6">
                                <label class="flex items-center cursor-pointer">
                                    <input @if ($isReadOnly) disabled @endif wire:model.defer="{{ $condition['model'] }}"
                                        type="radio" value="1" class="h-4 w-4 text-blue-600">
                                    <span class="ml-2 text-sm text-gray-600">YES</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input @if ($isReadOnly) disabled @endif wire:model.defer="{{ $condition['model'] }}"
                                        type="radio" value="0" class="h-4 w-4 text-gray-400">
                                    <span class="ml-2 text-sm text-gray-600">NO</span>
                                </label>
                            </div>

                            @error($condition['model'])
                                <span data-error-for="{{ $condition['model'] }}" class="text-red-500 text-sm block mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    @endforeach
                </div>

                <div class="rounded-md border border-slate-200 bg-white p-4">
                    <label class="block text-base font-semibold leading-7 text-slate-800">10. Do you have, or have you had any disease, condition or problem not listed above? <span class="text-red-600">*</span></label>
                    <div class="mt-2 flex gap-x-6">
                        <label class="flex items-center cursor-pointer">
                            <input @if ($isReadOnly) disabled @endif wire:model.defer="is_other_disease_condition_problem"
                                x-model="otherCondition" type="radio" value="1" class="h-4 w-4 text-blue-600">
                            <span class="ml-2 text-sm text-gray-600">YES</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input @if ($isReadOnly) disabled @endif wire:model.defer="is_other_disease_condition_problem"
                                x-model="otherCondition" type="radio" value="0" class="h-4 w-4 text-gray-400">
                            <span class="ml-2 text-sm text-gray-600">NO</span>
                        </label>
                    </div>

                    @error('is_other_disease_condition_problem')
                        <span data-error-for="is_other_disease_condition_problem" class="text-red-500 text-sm block mt-1">{{ $message }}</span>
                    @enderror

                    <div x-cloak x-show="otherCondition === '1'" class="mt-3 rounded-md border border-emerald-100 bg-emerald-50/70 p-3">
                        <label class="mb-1 block text-sm font-medium text-gray-700">If yes, please list:</label>
                        <input @if ($isReadOnly) disabled @endif wire:model.defer="what_other_disease_condition_problem" type="text"
                            class="w-full border rounded px-3 py-2 text-sm @error('what_other_disease_condition_problem') border-red-500 focus:border-red-500 focus:ring-red-200 @enderror"
                            placeholder="Please specify details...">

                        @error('what_other_disease_condition_problem')
                            <span data-error-for="what_other_disease_condition_problem" class="text-red-500 text-sm block mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            </section>

            @if ($gender === 'Female')
                <div class="mb-6 mt-8 rounded-md border border-pink-200 bg-pink-50 px-4 py-4">
                    <h2 class="text-base font-semibold text-pink-800">For Women Only</h2>
                </div>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    @foreach ([['q' => '7. Are you pregnant?', 'model' => 'is_pregnant_q7'], ['q' => '8. Are you breast feeding?', 'model' => 'is_breast_feeding_q8']] as $fem)
                        <div class="rounded-md border border-pink-200 bg-white p-4">
                            <label class="block text-base font-semibold text-gray-700">{{ $fem['q'] }} <span class="text-red-600">*</span></label>
                            <div class="flex gap-x-6 mt-2">
                                <label class="flex items-center cursor-pointer">
                                    <input @if ($isReadOnly) disabled @endif wire:model.defer="{{ $fem['model'] }}"
                                        type="radio" value="1" class="h-4 w-4 text-pink-600">
                                    <span class="ml-2 text-sm text-gray-600">YES</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input @if ($isReadOnly) disabled @endif wire:model.defer="{{ $fem['model'] }}"
                                        type="radio" value="0" class="h-4 w-4 text-gray-400">
                                    <span class="ml-2 text-sm text-gray-600">NO</span>
                                </label>
                            </div>
                            
                            @error($fem['model'])
                                <span data-error-for="{{ $fem['model'] }}" class="text-red-500 text-sm block mt-1">{{ $message }}</span>
                            @enderror
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
            <button
                type="button"
                @if ($canStartNewRecord)
                    wire:click="$dispatch('openNewVisitRecord')"
                @else
                    disabled
                    aria-disabled="true"
                    title="Only admin or dentist can add new records."
                @endif
                class="flex items-center gap-2 rounded-lg px-6 py-3 text-lg font-bold text-white shadow-lg transition-all transform {{ $canStartNewRecord ? 'bg-[#0086da] hover:scale-105 cursor-pointer' : 'bg-slate-300 cursor-not-allowed opacity-70' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M12 5v14M5 12h14" />
                </svg>
                New Record
            </button>
        </div>
    @endif

</div>


