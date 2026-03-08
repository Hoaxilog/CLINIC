<div>
    @if ($showModal)
        <div data-patient-form-modal class="fixed inset-0 z-50 flex items-center justify-center bg-black/70" x-data="{}">
            
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
                    <div data-form-scroll class="p-8 overflow-y-auto">
                        @if($currentStep == 1)
                            <div class="{{ $isReadOnly ? 'pointer-events-none' : '' }}">
                                <livewire:PatientFormController.basic-info wire:key="basic-info" :data="$basicInfoData" />
                            </div>
                        @endif

                        @if($currentStep == 2)
                            <div>
                                <livewire:PatientFormController.health-history 
                                    wire:key="health-history" 
                                    :data="$healthHistoryData" 
                                    :gender="$basicInfoData['gender'] ?? null" 
                                    :isReadOnly="$isReadOnly" 
                                />
                            </div>
                        @endif

                        @if($isAdmin && $isEditing)
                            @if($currentStep == 3)
                                <div class="relative">
                                    <div wire:loading.flex wire:target="startNewChartSession,switchChartHistory"
                                        class="absolute inset-0 z-30 items-center justify-center bg-white/70 backdrop-blur-sm text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="h-10 w-10 animate-spin rounded-full border-4 border-blue-200 border-t-[#0086da]"></div>
                                            <div class="text-sm font-semibold text-gray-700">Loading dental chart...</div>
                                        </div>
                                    </div>
                                    <livewire:PatientFormController.dental-chart 
                                        :wire:key="'dental-chart-'.$chartKey" 
                                        :data="$dentalChartData" 
                                        :isReadOnly="$isReadOnly"
                                        :history="$chartHistory"
                                        :selectedHistoryId="$selectedHistoryId" 
                                        :isCreating="$forceNewRecord"
                                    />
                                </div>
                            @endif
                            <!-- Step 4: Treatment Record -->
                            @if($currentStep == 4)
                                {{-- [UPDATED] Added chartKey to wire:key to force refresh when switching history --}}
                                <livewire:PatientFormController.treatment-record 
                                    :wire:key="'treatment-record-'.$chartKey" 
                                    :data="$treatmentRecordData ?? []" 
                                    :isReadOnly="$isReadOnly"
                                />
                            @endif
                        @endif
                    </div>

                    <!-- Footer / Buttons -->
                    <div class="bg-white rounded-b-lg p-6 flex justify-between items-center shadow-[inset_0_4px_6px_-2px_rgba(0,0,0,0.1)]">                        
                        
                        <!-- LEFT SIDE: ACTION BUTTONS (Edit or Save) -->
                        <div>
                            @if($isEditing && $isReadOnly)
                                @if($currentStep == 3 || $currentStep == 4)
                                @elseif($currentStep == 2 && empty($healthHistoryData['id']))
                                    @else 
                                    <button wire:click="enableEditMode" type="button" class="active:outline-2 active:outline-offset-3 active:outline-dashed active:outline-black px-6 py-2.5 rounded-lg text-sm font-bold text-white bg-[#ffac00] hover:bg-yellow-600 shadow-md flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                                        Edit Record
                                    </button>
                                @endif
                            @elseif(!$isReadOnly)
                                {{-- [UPDATED] Logic for Save Button Visibility --}}
                                @php
                                    $shouldShowSave = true;
                                    
                                    // Case 1: Adding New Patient -> Only show on Step 2
                                    if (!$isEditing && $currentStep == 1) {
                                        $shouldShowSave = false;
                                    }

                                    // Case 2: Editing (Admin) -> Hide on Step 3 (Dental Chart) to force flow to Step 4
                                    if ($isEditing && $currentStep == 3) {
                                        $shouldShowSave = false;
                                    }
                                @endphp

                                    @if($shouldShowSave)
                                    <button data-trigger-scroll wire:click="save" type="button"
                                        class="px-6 py-2.5 rounded-lg text-sm font-bold text-white bg-green-600 hover:bg-green-700 shadow-sm">
                                        {{ (!$isEditing && $currentStep == 2) ? 'Save Patient' : (($currentStep == 4) ? 'Save Record' : 'Update & Save') }}
                                    </button>
                                @endif
                            @endif
                        </div>

                        <!-- RIGHT SIDE: NAVIGATION -->
                        <div class="flex items-center gap-4">
                            
                            <button wire:click="cancelEdit" type="button" class="px-6 py-2.5 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">
                                {{ ($isEditing && !$isReadOnly) ? 'Cancel' : 'Close' }}
                            </button>

                            
                            
                            @php
                                $showNext = true;
                                $showBack = ($currentStep > 1);

                                // 1. Adding New Patient
                                if (!$isEditing) {
                                    if ($currentStep >= 2) $showNext = false;
                                } 
                                // 2. Editing Existing Patient
                                else {
                                    // Max Step Constraints
                                    if ($isAdmin) {
                                        if ($currentStep >= 4) $showNext = false;
                                    } else {
                                        if ($currentStep >= 2) $showNext = false;
                                    }

                                    // Active Edit Mode Restrictions
                                    if (!$isReadOnly) {
                                        // Only show 'Next' if we are on Step 3 (Dental Chart) to go to Step 4.
                                        // Steps 1 & 2 should strictly use 'Update & Save'.
                                        if ($currentStep != 3) {
                                            $showNext = false;
                                        }

                                        // Hide 'Back' on Step 2 and Step 3 during edit mode.
                                        // Step 2: Health History -> prevent back to Basic Info
                                        // Step 3: Dental Chart -> prevent back to Health History
                                        // Step 4: Treatment Record -> ALLOW Back to Dental Chart
                                        if ($currentStep == 2 || $currentStep == 3) {
                                            $showBack = false;
                                        }
                                    }
                                }
                            @endphp

                            @if ($showBack)
                                <button data-trigger-scroll wire:click="previousStep" type="button" wire:loading.attr="disabled"
                                    wire:target="previousStep,nextStep,save"
                                    class="px-6 py-2.5 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 disabled:opacity-60 disabled:cursor-not-allowed">
                                    <span wire:loading.remove wire:target="previousStep">Back</span>
                                    <span wire:loading wire:target="previousStep">Loading...</span>
                                </button>
                            @endif

                            @if ($showNext)
                                <button data-trigger-scroll wire:click="nextStep" type="button" wire:loading.attr="disabled"
                                    wire:target="nextStep,previousStep,save"
                                    class="px-6 py-2.5 rounded-lg text-sm font-medium text-white bg-[#0086da] hover:bg-blue-500 disabled:opacity-60 disabled:cursor-not-allowed">
                                    <span wire:loading.remove wire:target="nextStep">Next</span>
                                    <span wire:loading wire:target="nextStep">Loading...</span>
                                </button>
                            @endif

                        </div>

                    </div>
                </div>  
            </div>
        </div>
    @endif

    @include('components.flash-toast')

    @push('script')
        <script>
            (function () {
                const scrollToField = (field) => {
                    const modal = document.querySelector('[data-patient-form-modal]');
                    if (!modal) return;

                    const errorTarget = field
                        ? modal.querySelector('[data-error-for="' + field + '"]')
                        : null;

                    const inputSelector = field
                        ? [
                              '[wire\\:model="' + field + '"]',
                              '[wire\\:model\\.defer="' + field + '"]',
                              '[wire\\:model\\.live="' + field + '"]',
                              '[name="' + field + '"]',
                              '#' + CSS.escape(field)
                          ].join(',')
                        : null;

                    const inputTarget = inputSelector ? modal.querySelector(inputSelector) : null;
                    const target = errorTarget || inputTarget;

                    if (!target) return;

                    target.scrollIntoView({ behavior: 'smooth', block: 'center' });

                    const scrollContainer =
                        target.closest('[data-form-scroll]') || modal.querySelector('[data-form-scroll]');

                    if (scrollContainer) {
                        const containerRect = scrollContainer.getBoundingClientRect();
                        const targetRect = target.getBoundingClientRect();
                        const targetTop = targetRect.top - containerRect.top + scrollContainer.scrollTop - 20;
                        scrollContainer.scrollTo({ top: targetTop, behavior: 'smooth' });
                    }
                };

                document.addEventListener('scroll-to-error', (event) => {
                    const field = event?.detail?.field || null;
                    scrollToField(field);
                });
            })();
        </script>
    @endpush
</div>
