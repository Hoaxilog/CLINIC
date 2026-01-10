<section>
    <!-- Dental History -->
    <div class="bg-blue-100 border-l-4 border-blue-500 p-4 mb-6">
        <h2 class="text-xl font-bold text-black">Dental History</h2>
    </div>

    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Q1: Date -->
            <div>
                <label for="when_last_visit_q1" class="block text-lg font-medium text-gray-700 mb-2">1. Date of last dental visit:</label>
                <input wire:model="when_last_visit_q1" type="date" id="when_last_visit_q1" class="w-full border rounded px-4 py-3 text-base">
                @error('when_last_visit_q1') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <!-- Q1: Reason -->
            <div>
                <label for="what_last_visit_reason_q1" class="block text-lg font-medium text-gray-700 mb-2">What was done on your last dental visit?</label>
                <input wire:model="what_last_visit_reason_q1" type="text" id="what_last_visit_reason_q1" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., Cleaning, Filling, etc.">
                @error('what_last_visit_reason_q1') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Q2: Reason -->
        <div>
            <label for="what_seeing_dentist_reason_q2" class="block text-lg font-medium text-gray-700 mb-2">2. What is your reason for seeing the dentist today?</label>
            <input wire:model="what_seeing_dentist_reason_q2" type="text" id="what_seeing_dentist_reason_q2" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., Check-up, Toothache, etc.">
            @error('what_seeing_dentist_reason_q2') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Q3: Experienced -->
        <div>
            <label class="block text-lg font-medium text-gray-700 mb-2">3. Have you experienced:</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 pl-4">
                <!-- Q3a -->
                <div>
                    <label class="block text-base font-medium text-gray-700">A. Clicking of the Jaw?</label>
                    <div class="flex gap-x-6 mt-2">
                        <label class="flex items-center">
                            <input wire:model.live="is_clicking_jaw_q3a" type="radio" value="1" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="ml-2 text-base text-gray-700">YES</span>
                        </label>
                        <label class="flex items-center">
                            <input wire:model.live="is_clicking_jaw_q3a" type="radio" value="0" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="ml-2 text-base text-gray-700">NO</span>
                        </label>
                    </div>
                </div>
                <!-- Q3b -->
                <div>
                    <label class="block text-base font-medium text-gray-700">B. Pain below the ear / side of face?</label>
                    <div class="flex gap-x-6 mt-2">
                        <label class="flex items-center">
                            <input wire:model.live="is_pain_jaw_q3b" type="radio" value="1" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="ml-2 text-base text-gray-700">YES</span>
                        </label>
                        <label class="flex items-center">
                            <input wire:model.live="is_pain_jaw_q3b" type="radio" value="0" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="ml-2 text-base text-gray-700">NO</span>
                        </label>
                    </div>
                </div>
                <!-- Q3c -->
                <div>
                    <label class="block text-base font-medium text-gray-700">C. Difficulty in opening / closing of the mouth?</label>
                    <div class="flex gap-x-6 mt-2">
                        <label class="flex items-center">
                            <input wire:model.live="is_difficulty_opening_closing_q3c" type="radio" value="1" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="ml-2 text-base text-gray-700">YES</span>
                        </label>
                        <label class="flex items-center">
                            <input wire:model.live="is_difficulty_opening_closing_q3c" type="radio" value="0" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="ml-2 text-base text-gray-700">NO</span>
                        </label>
                    </div>
                </div>
                <!-- Q3d -->
                <div>
                    <label class="block text-base font-medium text-gray-700">D. Locking of the jaw?</label>
                    <div class="flex gap-x-6 mt-2">
                        <label class="flex items-center">
                            <input wire:model.live="is_locking_jaw_q3d" type="radio" value="1" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="ml-2 text-base text-gray-700">YES</span>
                        </label>
                        <label class="flex items-center">
                            <input wire:model.live="is_locking_jaw_q3d" type="radio" value="0" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="ml-2 text-base text-gray-700">NO</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Q4: Clench -->
        <div>
            <label class="block text-lg font-medium text-gray-700">4. Do you clench or grind your teeth while awake or asleep?</label>
            <div class="flex gap-x-6 mt-2">
                <label class="flex items-center">
                    <input wire:model.live="is_clench_grind_q4" type="radio" value="1" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span class="ml-2 text-base text-gray-700">YES</span>
                </label>
                <label class="flex items-center">
                    <input wire:model.live="is_clench_grind_q4" type="radio" value="0" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span class="ml-2 text-base text-gray-700">NO</span>
                </label>
            </div>
        </div>

        <!-- Q5: Bad Experience -->
        <div>
            <label class="block text-lg font-medium text-gray-700">5. Have you ever had a bad experience in the dental office?</label>
            <div class="flex gap-x-6 mt-2">
                <label class="flex items-center">
                    <input wire:model.live="is_bad_experience_q5" type="radio" value="1" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span class="ml-2 text-base text-gray-700">YES</span>
                </label>
                <label class="flex items-center">
                    <input wire:model.live="is_bad_experience_q5" type="radio" value="0" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span class="ml-2 text-base text-gray-700">NO</span>
                </label>
            </div>
        </div>

        <!-- Q6: Nervous -->
        <div>
            <label class="block text-lg font-medium text-gray-700">6. Do you feel nervous about having dental treatment?</label>
            <div class="flex gap-x-6 mt-2">
                <label class="flex items-center">
                    <input wire:model.live="is_nervous_q6" type="radio" value="1" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span class="ml-2 text-base text-gray-700">YES</span>
                </label>
                <label class="flex items-center">
                    <input wire:model.live="is_nervous_q6" type="radio" value="0" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span class="ml-2 text-base text-gray-700">NO</span>
                </label>
            </div>
        </div>

        <!-- Q6: Conditional Reason -->
        @if($is_nervous_q6)
            <div class="pl-6">
                <label for="what_nervous_concern_q6" class="block text-lg font-medium text-gray-700 mb-2">If YES, what is your concern?</label>
                <input wire:model="what_nervous_concern_q6" type="text" id="what_nervous_concern_q6" class="w-full border rounded px-4 py-3 text-base">
                @error('what_nervous_concern_q6') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        @endif
    </div>

    <!-- Medical History -->
    <div class="bg-blue-100 border-l-4 border-blue-500 p-4 mb-6 mt-10">
        <h2 class="text-xl font-bold text-black">Medical History</h2>
    </div>

    <div class="space-y-6">
        <!-- Q1: Medical Condition -->
        <div>
            <label class="block text-lg font-medium text-gray-700">1. Are you being treated for any medical condition at the present or have been treated within the past two years?</label>
            <div class="flex gap-x-6 mt-2">
                <label class="flex items-center">
                    <input wire:model.live="is_condition_q1" type="radio" value="1" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span class="ml-2 text-base text-gray-700">YES</span>
                </label>
                <label class="flex items-center">
                    <input wire:model.live="is_condition_q1" type="radio" value="0" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span class="ml-2 text-base text-gray-700">NO</span>
                </label>
            </div>
        </div>
        @if($is_condition_q1)
            <div class="pl-6">
                <label for="what_condition_reason_q1" class="block text-lg font-medium text-gray-700 mb-2">If YES, for what reason?</label>
                <input wire:model="what_condition_reason_q1" type="text" id="what_condition_reason_q1" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., High Blood Pressure, Diabetes, etc.">
                @error('what_condition_reason_q1') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        @endif

        <!-- Q2: Hospitalized -->
        <div>
            <label class="block text-lg font-medium text-gray-700">2. Have you ever been hospitalized?</label>
            <div class="flex gap-x-6 mt-2">
                <label class="flex items-center">
                    <input wire:model.live="is_hospitalized_q2" type="radio" value="1" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span class="ml-2 text-base text-gray-700">YES</span>
                </label>
                <label class="flex items-center">
                    <input wire:model.live="is_hospitalized_q2" type="radio" value="0" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span class="ml-2 text-base text-gray-700">NO</span>
                </label>
            </div>
        </div>
        @if($is_hospitalized_q2)
            <div class="pl-6">
                <label for="what_hospitalized_reason_q2" class="block text-lg font-medium text-gray-700 mb-2">If YES, for what reason?</label>
                <input wire:model="what_hospitalized_reason_q2" type="text" id="what_hospitalized_reason_q2" class="w-full border rounded px-4 py-3 text-base">
                @error('what_hospitalized_reason_q2') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        @endif

        <!-- Q3: Serious Illness/Operation -->
        <div>
            <label class="block text-lg font-medium text-gray-700">3. Have you ever had any serious illness or operation?</label>
            <div class="flex gap-x-6 mt-2">
                <label class="flex items-center">
                    <input wire:model.live="is_serious_illness_operation_q3" type="radio" value="1" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span class="ml-2 text-base text-gray-700">YES</span>
                </label>
                <label class="flex items-center">
                    <input wire:model.live="is_serious_illness_operation_q3" type="radio" value="0" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span class="ml-2 text-base text-gray-700">NO</span>
                </label>
            </div>
        </div>
        @if($is_serious_illness_operation_q3)
            <div class="pl-6">
                <label for="what_serious_illness_operation_reason_q3" class="block text-lg font-medium text-gray-700 mb-2">If YES, for what reason?</label>
                <input wire:model="what_serious_illness_operation_reason_q3" type="text" id="what_serious_illness_operation_reason_q3" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., Appendix, Heart Surgery, etc.">
                @error('what_serious_illness_operation_reason_q3') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        @endif

        <!-- Q4: Medications -->
        <div>
            <label class="block text-lg font-medium text-gray-700">4. Are you taking any medications prescribed by your doctor, non-prescription drugs, injections, or inhalers?</label>
            <div class="flex gap-x-6 mt-2">
                <label class="flex items-center">
                    <input wire:model.live="is_taking_medications_q4" type="radio" value="1" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span class="ml-2 text-base text-gray-700">YES</span>
                </label>
                <label class="flex items-center">
                    <input wire:model.live="is_taking_medications_q4" type="radio" value="0" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span class="ml-2 text-base text-gray-700">NO</span>
                </label>
            </div>
        </div>
        @if($is_taking_medications_q4)
            <div class="pl-6">
                <label for="what_medications_list_q4" class="block text-lg font-medium text-gray-700 mb-2">If YES, name of drug and dosage:</label>
                <input wire:model="what_medications_list_q4" type="text" id="what_medications_list_q4" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., Metformin 500mg, Advil, etc.">
                @error('what_medications_list_q4') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        @endif

        <!-- Q5: Allergic to Medications -->
        <div>
            <label class="block text-lg font-medium text-gray-700">5. Are you allergic to any medications or local anesthetics?</label>
            <div class="flex gap-x-6 mt-2">
                <label class="flex items-center">
                    <input wire:model.live="is_allergic_medications_q5" type="radio" value="1" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span class="ml-2 text-base text-gray-700">YES</span>
                </label>
                <label class="flex items-center">
                    <input wire:model.live="is_allergic_medications_q5" type="radio" value="0" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span class="ml-2 text-base text-gray-700">NO</span>
                </label>
            </div>
        </div>
        @if($is_allergic_medications_q5)
            <div class="pl-6">
                <label for="what_allergies_list_q5" class="block text-lg font-medium text-gray-700 mb-2">If YES, please list:</label>
                <input wire:model="what_allergies_list_q5" type="text" id="what_allergies_list_q5" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., Penicillin, Aspirin, etc.">
                @error('what_allergies_list_q5') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        @endif

        <!-- Q6: Allergic to Latex -->
        <div>
            <label class="block text-lg font-medium text-gray-700">6. Are you allergic to latex, rubber products, or metals?</label>
            <div class="flex gap-x-6 mt-2">
                <label class="flex items-center">
                    <input wire:model.live="is_allergic_latex_rubber_metals_q6" type="radio" value="1" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span class="ml-2 text-base text-gray-700">YES</span>
                </label>
                <label class="flex items-center">
                    <input wire:model.live="is_allergic_latex_rubber_metals_q6" type="radio" value="0" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span class="ml-2 text-base text-gray-700">NO</span>
                </label>
            </div>
        </div>
    </div>

    <!-- For Women Only -->
    @if($gender === 'Female')
        <div class="bg-blue-100 border-l-4 border-blue-500 p-4 mb-6 mt-10">
            <h2 class="text-xl font-bold text-black">For Women Only</h2>
        </div>

        <div class="space-y-6">
            <!-- Q7: Pregnant -->
            <div>
                <label class="block text-lg font-medium text-gray-700">7. Are you pregnant or think you're pregnant?</label>
                <div class="flex gap-x-6 mt-2">
                    <label class="flex items-center">
                        <input wire:model.live="is_pregnant_q7" type="radio" value="1" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="ml-2 text-base text-gray-700">YES</span>
                    </label>
                    <label class="flex items-center">
                        <input wire:model.live="is_pregnant_q7" type="radio" value="0" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="ml-2 text-base text-gray-700">NO</span>
                    </label>
                </div>
            </div>
            <!-- Q8: Breast feeding -->
            <div>
                <label class="block text-lg font-medium text-gray-700">8. Are you breast feeding?</label>
                <div class="flex gap-x-6 mt-2">
                    <label class="flex items-center">
                        <input wire:model.live="is_breast_feeding_q8" type="radio" value="1" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="ml-2 text-base text-gray-700">YES</span>
                    </label>
                    <label class="flex items-center">
                        <input wire:model.live="is_breast_feeding_q8" type="radio" value="0" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="ml-2 text-base text-gray-700">NO</span>
                    </label>
                </div>
            </div>
        </div>
    @endif
</section>