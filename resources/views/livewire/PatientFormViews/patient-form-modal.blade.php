<div>
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70" x-data="{}">
            
            <div class="bg-white rounded-lg shadow-xl w-full max-w-[105rem] mx-auto m-8">
                <!-- Modal Content -->
                <div class="flex flex-col max-h-[90vh]">
                    
                    <!-- Stepper Header -->
                    <div class="bg-white rounded-t-lg p-6 shadow-md">
                        <div class="flex items-center justify-center">
                            
                            <!-- Step 1 -->
                            <div class="flex items-center gap-3 {{ $currentStep == 1 ? 'text-[#0086da]' : 'text-gray-500' }}">
                                <span class="flex items-center justify-center h-8 w-8 rounded-full border-2 {{ $currentStep == 1 ? 'border-[#0086da]' : 'border-gray-500' }} text-sm font-bold">1</span>
                                <span class="text-lg font-semibold whitespace-nowrap">Basic Information</span>
                            </div>
                            
                            <!-- Connector -->
                            <div class="w-16 h-px bg-gray-300 mx-4"></div>
                            
                            <!-- Step 2 -->
                            <div class="flex items-center gap-3 {{ $currentStep == 2 ? 'text-[#0086da]' : 'text-gray-500' }}">
                                <span class="flex items-center justify-center h-8 w-8 rounded-full border-2 {{ $currentStep == 2 ? 'border-blue-600' : 'border-gray-500' }} text-sm font-bold">2</span>
                                <span class="text-lg font-semibold whitespace-nowrap">Health History</span>
                            </div>
                            
                            <!-- Step 3 & 4 (Admin Only AND Editing) -->
                            @if($isAdmin && $isEditing)
                                <div class="w-16 h-px bg-gray-300 mx-4"></div>
                                
                                <div class="flex items-center gap-3 {{ $currentStep == 3 ? 'text-[#0086da]' : 'text-gray-500' }}">
                                    <span class="flex items-center justify-center h-8 w-8 rounded-full border-2 {{ $currentStep == 3 ? 'border-blue-600' : 'border-gray-500' }} text-sm font-bold">3</span>
                                    <span class="text-lg font-semibold whitespace-nowrap">Dental Chart</span>
                                </div>
                                
                                <div class="w-16 h-px bg-gray-300 mx-4"></div>
                                
                                <div class="flex items-center gap-3 {{ $currentStep == 4 ? 'text-[#0086da]' : 'text-gray-500' }}">
                                    <span class="flex items-center justify-center h-8 w-8 rounded-full border-2 {{ $currentStep == 4 ? 'border-blue-600' : 'border-gray-500' }} text-sm font-bold">4</span>
                                    <span class="text-lg font-semibold whitespace-nowrap">Treatment Record</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Scrollable Form Area -->
                    <div class="p-8 overflow-y-auto">
                        <div @if($currentStep != 1) hidden @endif>
                            <div class="{{ $isReadOnly ? 'pointer-events-none' : '' }}">
                                <livewire:PatientFormController.basic-info wire:key="basic-info" :data="$basicInfoData" />
                            </div>
                        </div>

                        <div @if($currentStep != 2) hidden @endif>
                             <div class="{{ $isReadOnly ? 'pointer-events-none' : '' }}">
                                 <livewire:PatientFormController.health-history wire:key="health-history" :data="$healthHistoryData" :gender="$basicInfoData['gender'] ?? null" />
                             </div>
                        </div>

                        @if($isAdmin && $isEditing)
                            <div @if($currentStep != 3) hidden @endif>
                                <livewire:PatientFormController.dental-chart 
                                    :wire:key="'dental-chart-'.$chartKey" 
                                    :data="$dentalChartData" 
                                    :isReadOnly="$isReadOnly"
                                    :history="$chartHistory"
                                    :selectedHistoryId="$selectedHistoryId" 
                                    :isCreating="$forceNewRecord" {{-- [NEW] Pass creation status --}}
                                />
                            </div>
                            @if ($currentStep == 4)
                                 <p>Treatment Record will go here...</p>
                            @endif
                        @endif
                    </div>

                    <!-- Footer / Buttons -->
                        <div class="bg-white rounded-b-lg p-6 flex justify-between items-center shadow-[inset_0_4px_6px_-2px_rgba(0,0,0,0.1)]">                        
                            
                            <!-- LEFT SIDE: ACTION BUTTONS (Edit or Save) -->
                            <div>
                                @if($isEditing && $isReadOnly)
                                    {{-- 
                                        [UPDATED LOGIC] 
                                        Only show "Edit Record" if:
                                        1. We are viewing an existing patient (isEditing && isReadOnly).
                                        2. We are NOT viewing a historical record (selectedHistoryId is empty).
                                        3. AND (Specific for Step 3): Dental Chart history exists. 
                                        If no history, the 'Empty State' center button handles creation, so hide this one.
                                    --}}
                                    @if(empty($selectedHistoryId) && !($currentStep == 3 && count($chartHistory) == 0))
                                        <button wire:click="enableEditMode" type="button" class="active:outline-2 active:outline-offset-3 active:outline-dashed active:outline-black px-6 py-2.5 rounded-lg text-sm font-bold text-white bg-[#ffac00] hover:bg-yellow-600 shadow-md flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                                            Edit Record
                                        </button>
                                    @endif
                                @elseif(!$isReadOnly)
                                    <!-- EDIT/ADD MODE: Show Save Button -->
                                    <button wire:click="save" type="button" class="px-6 py-2.5 rounded-lg text-sm font-bold text-white bg-green-600 hover:bg-green-700 shadow-sm">
                                        {{ (!$isEditing && $currentStep == 2) ? 'Save Patient' : 'Update & Save' }}
                                    </button>
                                @endif
                            </div>

                            <!-- RIGHT SIDE: NAVIGATION -->
                            <div class="flex items-center gap-4">
                                
                                <button wire:click="cancelEdit" type="button" class="px-6 py-2.5 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">
                                    {{ ($isEditing && !$isReadOnly) ? 'Cancel Edit' : 'Close' }}
                                </button>
                                
                                @if($isReadOnly || !$isEditing)
                                    @if ($currentStep > 1)
                                        <button wire:click="previousStep" type="button" class="px-6 py-2.5 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">
                                            Back
                                        </button>
                                    @endif

                                    {{-- Max Step Logic for NEXT button --}}
                                    @php
                                        $showNext = true;
                                        if (!$isEditing && $currentStep >= 2) $showNext = false;
                                        if ($isEditing && $currentStep >= 4) $showNext = false;
                                    @endphp

                                    @if ($showNext)
                                        <button wire:click="nextStep" type="button" class="px-6 py-2.5 rounded-lg text-sm font-medium text-white bg-[#0086da] hover:bg-blue-500">
                                            Next
                                        </button>
                                    @endif
                                @endif
                            </div>

                        </div>
                </div>  
            </div>
        </div>
    @endif
</div>