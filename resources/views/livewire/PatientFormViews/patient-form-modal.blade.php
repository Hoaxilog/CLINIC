<div>
    @if ($showModal)
        <div data-patient-form-modal class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 p-3 sm:p-6" x-data="{}">

            <div class="w-full max-w-[108rem] overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 shadow-2xl">
                <!-- Modal Content -->
                <div class="flex max-h-[92vh] flex-col">
                    
                    <!-- Stepper Header -->
                    <div class="border-b border-slate-200 bg-white px-4 py-4 sm:px-6">
                        <div class="flex flex-wrap items-center justify-center gap-y-3">
                            
                            <!-- Step 1 -->
                            <div class="flex items-center gap-2.5 {{ $currentStep == 1 ? 'text-sky-600' : 'text-slate-500' }}">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full border-2 {{ $currentStep == 1 ? 'border-sky-600 bg-sky-50' : 'border-slate-400 bg-white' }} text-sm font-semibold">1</span>
                                <span class="whitespace-nowrap text-sm font-semibold sm:text-base">Basic Information</span>
                            </div>
                            
                            <!-- Connector -->
                            <div class="mx-3 h-px w-8 bg-slate-300 sm:w-12"></div>
                            
                            <!-- Step 2 -->
                            <div class="flex items-center gap-2.5 {{ $currentStep == 2 ? 'text-sky-600' : 'text-slate-500' }}">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full border-2 {{ $currentStep == 2 ? 'border-sky-600 bg-sky-50' : 'border-slate-400 bg-white' }} text-sm font-semibold">2</span>
                                <span class="whitespace-nowrap text-sm font-semibold sm:text-base">Health History</span>
                            </div>
                            
                            <!-- Step 3 & 4 (Admin Only AND Editing) -->
                            @if($isAdmin && $isEditing)
                                <div class="mx-3 h-px w-8 bg-slate-300 sm:w-12"></div>
                                
                                <div class="flex items-center gap-2.5 {{ $currentStep == 3 ? 'text-sky-600' : 'text-slate-500' }}">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full border-2 {{ $currentStep == 3 ? 'border-sky-600 bg-sky-50' : 'border-slate-400 bg-white' }} text-sm font-semibold">3</span>
                                    <span class="whitespace-nowrap text-sm font-semibold sm:text-base">Dental Chart</span>
                                </div>
                                
                                <div class="mx-3 h-px w-8 bg-slate-300 sm:w-12"></div>
                                
                                <div class="flex items-center gap-2.5 {{ $currentStep == 4 ? 'text-sky-600' : 'text-slate-500' }}">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full border-2 {{ $currentStep == 4 ? 'border-sky-600 bg-sky-50' : 'border-slate-400 bg-white' }} text-sm font-semibold">4</span>
                                    <span class="whitespace-nowrap text-sm font-semibold sm:text-base">Treatment Record</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Scrollable Form Area -->
                    <div data-form-scroll class="overflow-y-auto bg-slate-50 px-4 py-5 sm:px-6 sm:py-6">
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
                    <div class="border-t border-slate-200 bg-white px-4 py-4 sm:px-6">
                        @php
                            $showEditButton = false;
                            if ($isEditing && $isReadOnly) {
                                if ($currentStep != 3 && $currentStep != 4 && !($currentStep == 2 && empty($healthHistoryData['id']))) {
                                    $showEditButton = true;
                                }
                            }

                            $shouldShowSave = false;
                            if (!$isReadOnly) {
                                $shouldShowSave = true;
                                if (!$isEditing && $currentStep == 1) {
                                    $shouldShowSave = false;
                                }
                                if ($isEditing && $currentStep == 3) {
                                    $shouldShowSave = false;
                                }
                            }

                            $showNext = true;
                            $showBack = ($currentStep > 1);

                            if (!$isEditing) {
                                if ($currentStep >= 2) {
                                    $showNext = false;
                                }
                            } else {
                                if ($isAdmin) {
                                    if ($currentStep >= 4) {
                                        $showNext = false;
                                    }
                                } else {
                                    if ($currentStep >= 2) {
                                        $showNext = false;
                                    }
                                }

                                if (!$isReadOnly) {
                                    if ($currentStep != 3) {
                                        $showNext = false;
                                    }
                                    if ($currentStep == 2 || $currentStep == 3) {
                                        $showBack = false;
                                    }
                                }
                            }
                        @endphp

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="order-2 flex flex-wrap items-center gap-3 sm:order-1">
                                <button wire:click="cancelEdit" type="button" class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                    {{ ($isEditing && !$isReadOnly) ? 'Cancel' : 'Close' }}
                                </button>

                                @if ($showBack)
                                    <button data-trigger-scroll wire:click="previousStep" type="button" wire:loading.attr="disabled"
                                        wire:target="previousStep,nextStep,save"
                                        class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-60">
                                        <span wire:loading.remove wire:target="previousStep">Back</span>
                                        <span wire:loading wire:target="previousStep">Loading...</span>
                                    </button>
                                @endif
                            </div>

                            <div class="order-1 flex flex-wrap items-center justify-end gap-3 sm:order-2">
                                @if($showEditButton)
                                    <button wire:click="enableEditMode" type="button" class="active:outline-2 active:outline-offset-3 active:outline-dashed active:outline-black inline-flex items-center gap-2 rounded-lg bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                                        Edit Record
                                    </button>
                                @endif

                                @if($shouldShowSave)
                                    <button data-trigger-scroll wire:click="save" type="button"
                                        class="rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">
                                        {{ (!$isEditing && $currentStep == 2) ? 'Save Patient' : (($currentStep == 4) ? 'Save Record' : 'Update & Save') }}
                                    </button>
                                @endif

                                @if ($showNext)
                                    <button data-trigger-scroll wire:click="nextStep" type="button" wire:loading.attr="disabled"
                                        wire:target="nextStep,previousStep,save"
                                        class="rounded-lg bg-sky-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-sky-700 disabled:cursor-not-allowed disabled:opacity-60">
                                        <span wire:loading.remove wire:target="nextStep">Next</span>
                                        <span wire:loading wire:target="nextStep">Loading...</span>
                                    </button>
                                @endif
                            </div>
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
