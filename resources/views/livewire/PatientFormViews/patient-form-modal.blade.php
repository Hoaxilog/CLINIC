<div>
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70" x-data="{}">
            
            <div class="bg-white rounded-lg shadow-xl w-full max-w-7xl mx-auto m-8">
                <!-- Modal Content -->
                <div class="flex flex-col max-h-[90vh]">
                    
                    <!-- Stepper Header -->
                    <div class="bg-white rounded-t-lg p-6 shadow-md">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4 {{ $currentStep == 1 ? 'text-blue-600' : 'text-gray-500' }}">
                                <span class="flex items-center justify-center h-8 w-8 rounded-full border-2 {{ $currentStep == 1 ? 'border-blue-600' : 'border-gray-500' }} text-sm font-bold">1</span>
                                <span class="text-lg font-semibold">Basic Information</span>
                            </div>
                            <div class="flex-1 h-px bg-gray-300 mx-8"></div>
                            <div class="flex items-center gap-4 {{ $currentStep == 2 ? 'text-blue-600' : 'text-gray-500' }}">
                                <span class="flex items-center justify-center h-8 w-8 rounded-full border-2 {{ $currentStep == 2 ? 'border-blue-600' : 'border-gray-500' }} text-sm font-bold">2</span>
                                <span class="text-lg font-semibold">Health History</span>
                            </div>
                            <div class="flex-1 h-px bg-gray-300 mx-8"></div>
                            <div class="flex items-center gap-4 {{ $currentStep == 3 ? 'text-blue-600' : 'text-gray-500' }}">
                                <span class="flex items-center justify-center h-8 w-8 rounded-full border-2 {{ $currentStep == 3 ? 'border-blue-600' : 'border-gray-500' }} text-sm font-bold">3</span>
                                <span class="text-lg font-semibold">Dental Chart</span>
                            </div>
                            <div class="flex-1 h-px bg-gray-300 mx-8"></div>
                            <div class="flex items-center gap-4 {{ $currentStep == 4 ? 'text-blue-600' : 'text-gray-500' }}">
                                <span class="flex items-center justify-center h-8 w-8 rounded-full border-2 {{ $currentStep == 4 ? 'border-blue-600' : 'border-gray-500' }} text-sm font-bold">4</span>
                                <span class="text-lg font-semibold">Treatment Record</span>
                            </div>
                        </div>
                    </div>

                    <!-- Scrollable Form Area -->
                    <div class="p-8 overflow-y-auto">
                        <!-- Step 1: Basic Information -->
                        <div @if($currentStep != 1) hidden @endif>
                            <livewire:PatientFormController.basic-info wire:key="basic-info" />
                        </div>

                        <div @if($currentStep != 2) hidden @endif>
                             <livewire:PatientFormController.health-history wire:key="health-history" />
                        </div>

                        <!-- Step 3: Dental Chart (Placeholder) -->
                        @if ($currentStep == 3)
                             <p>Dental Chart will go here...</p>
                        @endif
                        
                        <!-- Step 4: Treatment Record (Placeholder) -->
                        @if ($currentStep == 4)
                             <p>Treatment Record will go here...</p>
                        @endif
                    </div>

                    <!-- Footer / Buttons -->
                    <div class="bg-white rounded-b-lg p-6 flex justify-end items-center gap-4 shadow-[inset_0_4px_6px_-2px_rgba(0,0,0,0.1)]">                        
                        <button wire:click="closeModal" type="button" class="px-6 py-2.5 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">
                            Cancel
                        </button>
                        
                        @if ($currentStep > 1)
                            <button wire:click="previousStep" type="button" class="px-6 py-2.5 rounded-lg text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">
                                Back
                            </button>
                        @endif

                        @if ($currentStep == 1)
                            <button wire:click="nextStep" type="button" class="px-6 py-2.5 rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                Next
                            </button>
                        @endif
                        
                        {{-- Show Save button only on the last step (e.g., Step 4)
                           We are temporarily saving on Step 2
                        --}}
                         @if ($currentStep == 2) 
                            <button wire:click="save" type="button" class="px-6 py-2.5 rounded-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                Save Patient
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>