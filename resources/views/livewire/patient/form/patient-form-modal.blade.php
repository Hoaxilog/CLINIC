<div>
    @if ($showModal)
        <div data-patient-form-modal class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-3 sm:p-6"
            x-data="patientFormDraftManager({
                userId: @js((int) (auth()->id() ?? 0)),
                mode: @js($isEditing ? 'edit' : 'create'),
                patientId: @js($isEditing ? (int) ($newPatientId ?? 0) : 0),
                currentStep: @js((int) $currentStep)
            })" x-init="init()" x-on:patient-form-opened.window="handleModalOpened()"
            x-on:patient-form-closed.window="stopSyncTimer()"
            x-on:patient-form-draft-pause.window="pausePersist($event.detail?.ms || 2000)"
            x-on:patient-form-draft-cleared.window="handleDraftCleared($event.detail)"
            x-on:patient-form-navigation-started.window="navigationLoading = true; navigationStartedAt = Date.now()"
            x-on:patient-form-navigation-finished.window="handleNavigationFinished($event.detail)"
            x-on:patient-added.window="handleSuccessfulSave()" x-on:input.capture="markDirtyDebounced()"
            x-on:change.capture="markDirtyDebounced()" x-on:click.capture="handleClickCapture($event)">

            <div
                class="w-full max-w-[108rem] overflow-hidden rounded-md border border-gray-200 bg-[#f6fafd] shadow-2xl">
                <!-- Modal Content -->
                <div class="relative flex max-h-[92vh] flex-col">
                    <div x-cloak x-show="navigationLoading"
                        class="absolute inset-0 z-40 flex items-center justify-center bg-white/70 backdrop-blur-sm">
                        <div class="flex flex-col items-center gap-3 text-center">
                            <div class="h-10 w-10 animate-spin rounded-full border-4 border-blue-200 border-t-[#0086da]"></div>
                            <div class="text-sm font-semibold text-gray-700">Loading section...</div>
                        </div>
                    </div>

                    <div wire:loading.flex wire:target="selectVisitRecord,goToStep,startNewVisitRecord"
                        class="absolute inset-0 z-40 items-center justify-center bg-white/70 backdrop-blur-sm">
                        <div class="flex flex-col items-center gap-3 text-center">
                            <div class="h-10 w-10 animate-spin rounded-full border-4 border-blue-200 border-t-[#0086da]"></div>
                            <div class="text-sm font-semibold text-gray-700">Loading section...</div>
                        </div>
                    </div>


                    <!-- Stepper Header -->
                    <div class="border-b border-gray-200 bg-white px-4 py-4 sm:px-6">
                        @php
                            $basicInfoEditing = $isEditing && ! $isReadOnly && $currentStep == 1;
                            $editingLockedToSection = $isEditing && ! $isReadOnly && ! $forceNewRecord;
                            $canNavigateFromBasicInfo = ! $basicInfoEditing && ! $editingLockedToSection;
                        @endphp
                        <div class="flex flex-wrap items-center justify-center gap-y-3">

                                <!-- Tab 1: Basic Information -->
                                <div
                                    @if ($isEditing && !$editingLockedToSection) x-on:click.prevent="handleStepNavigation(1)" @endif
                                    class="flex items-center gap-2.5 transition {{ $currentStep == 1 ? 'text-[#0086da]' : 'text-gray-400' }} {{ $isEditing && $currentStep !== 1 && !$editingLockedToSection ? 'cursor-pointer hover:text-[#0086da]' : '' }} {{ $editingLockedToSection && $currentStep !== 1 ? 'cursor-not-allowed opacity-60' : '' }}">
                                    <span
                                        class="flex h-8 w-8 items-center justify-center rounded-sm border-2 {{ $currentStep == 1 ? 'border-[#0086da] bg-[#e8f4fc]' : 'border-gray-300 bg-white' }} text-sm font-semibold">1</span>
                                    <span class="whitespace-nowrap text-sm font-semibold sm:text-base">Basic Information</span>
                                </div>

                                <div class="mx-3 h-px w-8 bg-gray-200 sm:w-12"></div>

                                <!-- Tab 2: Health Records -->
                                <div
                                    @if ($isEditing && $canNavigateFromBasicInfo) x-on:click.prevent="handleStepNavigation(2)" @endif
                                    class="flex items-center gap-2.5 transition {{ $currentStep == 2 ? 'text-[#0086da]' : 'text-gray-400' }} {{ $isEditing && $currentStep !== 2 && $canNavigateFromBasicInfo ? 'cursor-pointer hover:text-[#0086da]' : '' }} {{ $basicInfoEditing ? 'cursor-not-allowed opacity-60' : '' }}">
                                    <span
                                        class="flex h-8 w-8 items-center justify-center rounded-sm border-2 {{ $currentStep == 2 ? 'border-[#0086da] bg-[#e8f4fc]' : 'border-gray-300 bg-white' }} text-sm font-semibold">2</span>
                                    <span class="whitespace-nowrap text-sm font-semibold sm:text-base">Health Records</span>
                                </div>

                                @if ($isAdmin)
                                    <div class="mx-3 h-px w-8 bg-gray-200 sm:w-12"></div>

                                    <!-- Tab 3: Dental Chart (admin only) -->
                                    <div
                                        @if ($isEditing && $canNavigateFromBasicInfo) x-on:click.prevent="handleStepNavigation(3)" @endif
                                        class="flex items-center gap-2.5 transition {{ $currentStep == 3 ? 'text-[#0086da]' : 'text-gray-400' }} {{ $isEditing && $currentStep !== 3 && $canNavigateFromBasicInfo ? 'cursor-pointer hover:text-[#0086da]' : '' }} {{ $basicInfoEditing ? 'cursor-not-allowed opacity-60' : '' }}">
                                        <span
                                            class="flex h-8 w-8 items-center justify-center rounded-sm border-2 {{ $currentStep == 3 ? 'border-[#0086da] bg-[#e8f4fc]' : 'border-gray-300 bg-white' }} text-sm font-semibold">3</span>
                                        <span class="whitespace-nowrap text-sm font-semibold sm:text-base">Dental Chart</span>
                                    </div>

                                    <div class="mx-3 h-px w-8 bg-gray-200 sm:w-12"></div>

                                    <!-- Tab 4: Treatment Record (admin only) -->
                                    <div
                                        @if ($isEditing && $canNavigateFromBasicInfo) x-on:click.prevent="handleStepNavigation(4)" @endif
                                        class="flex items-center gap-2.5 transition {{ $currentStep == 4 ? 'text-[#0086da]' : 'text-gray-400' }} {{ $isEditing && $currentStep !== 4 && $canNavigateFromBasicInfo ? 'cursor-pointer hover:text-[#0086da]' : '' }} {{ $basicInfoEditing ? 'cursor-not-allowed opacity-60' : '' }}">
                                        <span
                                            class="flex h-8 w-8 items-center justify-center rounded-sm border-2 {{ $currentStep == 4 ? 'border-[#0086da] bg-[#e8f4fc]' : 'border-gray-300 bg-white' }} text-sm font-semibold">4</span>
                                        <span class="whitespace-nowrap text-sm font-semibold sm:text-base">Treatment Record</span>
                                    </div>
                                @endif

                        </div>

                        <div x-cloak x-show="prompt.visible" x-transition:enter="transition ease-out duration-250"
                            x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-180"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                            :class="restoring ? 'animate-pulse' : ''"
                            class="mt-4 rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-900">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <div class="font-semibold text-red-800">Unsaved draft found</div>
                                    <div class="text-xs text-red-700">
                                        Last update:
                                        <span x-text="prompt.updatedAtLabel"></span>
                                        <span x-show="prompt.step"> | Step <span x-text="prompt.step"></span></span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" data-draft-ignore
                                        @click="$dispatch('patient-form-draft-pause', { ms: 2500 }); restoreDraft()"
                                        :disabled="restoring"
                                        class="rounded-sm bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-70">
                                        <span x-show="!restoring">Restore Draft</span>
                                        <span x-show="restoring" class="inline-flex items-center gap-1.5">
                                            <svg class="h-3.5 w-3.5 animate-spin" viewBox="0 0 24 24" fill="none">
                                                <circle cx="12" cy="12" r="9" stroke="currentColor"
                                                    stroke-width="2" class="opacity-30"></circle>
                                                <path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round"></path>
                                            </svg>
                                            Restoring...
                                        </span>
                                    </button>
                                    <button type="button" data-draft-ignore
                                        @click="$dispatch('patient-form-draft-pause', { ms: 2500 }); discardDraft()"
                                        :disabled="restoring"
                                        class="rounded-sm border border-red-200 bg-white px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-60">
                                        Discard Draft
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Scrollable Form Area -->
                    <div data-form-scroll class="overflow-y-auto bg-[#f6fafd] px-4 py-5 sm:px-6 sm:py-6">

                        {{-- Record Date bar — shown on steps 2-4 only --}}
                        @if ($isEditing && !empty($visitHistoryList) && $currentStep >= 2 && ($isReadOnly || ($isAdmin && in_array($currentStep, [3, 4], true))))
                            <div class="mb-5 flex items-center justify-between rounded-md border border-[#cce3f5] bg-white px-8 py-3 shadow-sm">
                                <div class="flex items-center gap-2 text-[#0086da]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                    <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Record Date</span>
                                </div>
                                <select
                                    x-on:change="handleVisitRecordSelection($event)"
                                    @disabled(!$isReadOnly)
                                    class="w-72 rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 focus:border-[#0086da] focus:outline-none focus:ring-1 focus:ring-[#0086da] sm:w-80">
                                    @foreach ($visitHistoryList as $visit)
                                        <option value="{{ $visit['value'] }}" @selected($selectedVisitDate === $visit['value'])>{{ $visit['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>

        @endif

                        {{-- Tab 1: Basic Info --}}
                        @if ($currentStep == 1)
                            <div class="{{ $isReadOnly ? 'pointer-events-none' : '' }}">
                                <livewire:patient.form.basic-info wire:key="basic-info" :data="$basicInfoData" />
                            </div>
                        @endif

                        {{-- Tab 2: Health Records --}}
                        @if ($currentStep == 2 && $isEditing)
                            <livewire:patient.form.health-history wire:key="health-history"
                                :data="$healthHistoryData" :gender="$basicInfoData['gender'] ?? null"
                                :isReadOnly="$selectedHealthHistoryId !== 'new'" :historyList="$healthHistoryList"
                                :selectedHistoryId="$selectedHealthHistoryId" />
                        @endif

                        {{-- Tab 3: Dental Chart (admin only) --}}
                        @if ($currentStep == 3 && $isEditing && $isAdmin)
                            <div wire:loading.flex wire:target="startNewChartSession,switchChartHistory"
                                class="absolute inset-0 z-30 items-center justify-center bg-white/70 backdrop-blur-sm text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="h-10 w-10 animate-spin rounded-full border-4 border-blue-200 border-t-[#0086da]"></div>
                                    <div class="text-sm font-semibold text-gray-700">Loading dental chart...</div>
                                </div>
                            </div>
                            <livewire:patient.form.dental-chart :wire:key="'dental-chart-'.$chartKey"
                                :data="$dentalChartData" :isReadOnly="$isReadOnly"
                                :history="$chartHistory" :selectedHistoryId="$selectedHistoryId"
                                :isCreating="$forceNewRecord" :patientAge="$patientAge" />
                        @endif

                        {{-- Tab 4: Treatment Record (admin only) --}}
                        @if ($currentStep == 4 && $isEditing && $isAdmin)
                            <livewire:patient.form.treatment-record
                                :wire:key="'treatment-record-'.$chartKey"
                                :data="$treatmentRecordData ?? []"
                                :isReadOnly="$isReadOnly" />
                        @endif





                    </div>

                    <!-- Footer / Buttons -->
                    <div class="border-t border-gray-200 bg-white px-4 py-4 sm:px-6">
                        @php
                            // ── Button visibility matrix ──────────────────────────────────────────
                            $hasHealthRecords = !empty($healthHistoryList);
                            $hasDentalRecords = !empty($chartHistory);
                            $hasTreatmentRecord = !empty($treatmentRecordData['id'] ?? null);
                            $hasExistingRecordForStep = match ($currentStep) {
                                2 => $hasHealthRecords,
                                3 => $hasDentalRecords,
                                4 => $hasTreatmentRecord,
                                default => false,
                            };

                            // Edit This Record: step 1 read-only only
                            $showEditButton = $isEditing && $isReadOnly && $currentStep == 1 && $isAdmin;

                            // Health history stays read-only for existing records.
                            $showEditVisitButton = false;
                            $showEditClinicalButton = $isEditing && $isReadOnly && $isAdmin && in_array($currentStep, [3, 4], true) && $hasExistingRecordForStep;
                            $showNewRecordButton = $isEditing && $isReadOnly && $isAdmin && in_array($currentStep, [2, 3, 4], true) && $hasExistingRecordForStep;

                            $finalEditableStep = $isEditing
                                ? ($currentStep === 1 ? 1 : ($isAdmin ? 4 : 1))
                                : ($isAdmin ? 4 : 2);
                            $shouldShowSave = ! $isReadOnly && (! $isEditing || $currentStep === $finalEditableStep);

                            $showNext = false; // tabs + save buttons replace Next
                            $showBack = false; // tabs replace Back
                        @endphp

                        @if ($isEditing && !$isReadOnly && $shouldShowSave)
                            <div class="mb-4 rounded-md border border-gray-200 bg-[#f6fafd] p-4">
                                <h3 class="text-sm font-semibold text-[#1a2e3b]">Patient Consent and Declaration</h3>
                                <p class="mt-2 text-xs leading-relaxed text-[#3d5a6e]">
                                    In compliance with Republic Act No. 10173 (Data Privacy Act of 2012), I voluntarily
                                    authorize this clinic and its authorized healthcare staff to collect, process, and
                                    securely store my personal and health information for diagnosis, treatment, billing,
                                    and related clinical operations.
                                </p>

                                <div class="mt-3 space-y-2">
                                    <label class="flex items-start gap-2 text-xs text-[#1a2e3b]">
                                        <input type="checkbox" wire:model="consentAuthorizationAccepted"
                                            class="mt-0.5 rounded-sm border-gray-300 text-[#0086da] focus:ring-[#0086da]">
                                        <span>
                                            I understand and authorize the processing of my information under the Data
                                            Privacy Act of 2012.
                                        </span>
                                    </label>
                                    @error('consentAuthorizationAccepted')
                                        <p class="text-xs text-red-600">{{ $message }}</p>
                                    @enderror

                                    <label class="flex items-start gap-2 text-xs text-[#1a2e3b]">
                                        <input type="checkbox" wire:model="consentTruthfulnessAccepted"
                                            class="mt-0.5 rounded-sm border-gray-300 text-[#0086da] focus:ring-[#0086da]">
                                        <span>
                                            I confirm that all information I have provided is true, complete, and
                                            correct to the best of my knowledge.
                                        </span>
                                    </label>
                                    @error('consentTruthfulnessAccepted')
                                        <p class="text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                            </div>
                        @endif

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="order-2 flex flex-wrap items-center gap-3 sm:order-1">
                                <button data-draft-ignore
                                    x-on:click="$dispatch('patient-form-draft-pause', { ms: 3000 })"
                                    wire:click="cancelEdit" type="button"
                                    class="rounded-sm border border-gray-200 bg-white px-5 py-2.5 text-sm font-semibold text-[#1a2e3b] hover:bg-[#f6fafd]">
                                    {{ $isEditing && !$isReadOnly ? 'Cancel' : 'Close' }}
                                </button>
                            </div>

                            <div class="order-1 flex flex-wrap items-center justify-end gap-3 sm:order-2">
                                @if ($showEditButton)
                                    <button wire:click="enableEditMode" type="button"
                                        class="inline-flex items-center gap-2 rounded-sm bg-[#0086da] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#0073a8]">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                        </svg>
                                        Edit Basic Info
                                    </button>
                                @endif

                                @if ($showEditVisitButton)
                                    <button wire:click="enableEditMode" type="button"
                                        class="inline-flex items-center gap-2 rounded-sm bg-[#0086da] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#0073a8]">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                        </svg>
                                        Edit Visit Record
                                    </button>
                                @endif

                                @if ($showEditClinicalButton)
                                    <button wire:click="enableEditMode" type="button"
                                        class="inline-flex items-center gap-2 rounded-sm bg-[#0086da] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#0073a8]">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                        </svg>
                                        Edit Clinical Record
                                    </button>
                                @endif

                                @if ($showNewRecordButton)
                                    <button data-draft-ignore wire:click="startNewVisitRecord" type="button"
                                        class="inline-flex items-center gap-2 rounded-sm bg-[#0086da] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#0073a8]">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 5v14M5 12h14"></path>
                                        </svg>
                                        New Record
                                    </button>
                                @endif

                                @if ($shouldShowSave)
                                    <button data-draft-ignore
                                        x-on:click="$dispatch('patient-form-draft-pause', { ms: 3000 })"
                                        data-trigger-scroll wire:click="save" type="button"
                                        class="rounded-sm bg-[#0086da] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#0073a8]">
                                        @if (!$isEditing)
                                            Register Patient
                                        @else
                                            Save All Changes
                                        @endif
                                    </button>
                                @endif

                                @if ($showNext)
                                    <button data-trigger-scroll wire:click="nextStep" type="button"
                                        wire:loading.attr="disabled" wire:target="nextStep,previousStep,save"
                                        class="rounded-sm bg-[#0086da] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#0073a8] disabled:cursor-not-allowed disabled:opacity-60">
                                        <span wire:loading.remove wire:target="nextStep">Continue to Health Records →</span>
                                        <span wire:loading wire:target="nextStep">Saving...</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif



    @push('script')
        <script>
            (function() {
                const clearDraftKeyByDetail = (detail) => {
                    if (!detail) return;

                    const userId = Number(detail.userId || 0);
                    if (!userId) return;

                    const mode = detail.mode === 'edit' ? 'edit' : 'create';
                    const patientId = Number(detail.patientId || 0);
                    const scope = mode === 'edit' ? patientId : 'new';
                    const key = `clinic:patient-form-draft:${userId}:${mode}:${scope}`;
                    localStorage.removeItem(key);
                };

                document.addEventListener('patient-form-draft-cleared', (event) => {
                    clearDraftKeyByDetail(event?.detail || null);
                });

                window.patientFormDraftManager = function(config) {
                    return {
                        userId: Number(config?.userId || 0),
                        mode: config?.mode === 'edit' ? 'edit' : 'create',
                        patientId: Number(config?.patientId || 0),
                        currentStep: Number(config?.currentStep || 1),
                        dirty: false,
                        persistPaused: false,
                        lastSnapshotHash: '',
                        lastSyncedHash: '',
                        debounceTimer: null,
                        syncTimer: null,
                        prompt: {
                            visible: false,
                            payload: null,
                            step: null,
                            updatedAtLabel: '',
                        },
                        restoring: false,
                        navigationLoading: false,
                        navigationStartedAt: 0,

                        init() {
                            this.startSyncTimer();
                            this.handleModalOpened();
                            window.addEventListener('beforeunload', () => {
                                if (this.dirty) {
                                    this.persistLocalDraft();
                                }
                            });
                        },

                        buildKey() {
                            const scope = this.mode === 'edit' ? this.patientId : 'new';
                            return `clinic:patient-form-draft:${this.userId}:${this.mode}:${scope}`;
                        },

                        markDirtyDebounced() {
                            if (this.persistPaused) {
                                return;
                            }

                            this.cancelPendingDebounce();

                            this.debounceTimer = setTimeout(() => {
                                if (this.persistPaused) {
                                    return;
                                }
                                this.currentStep = this.detectCurrentStep();
                                this.dirty = true;
                                this.persistLocalDraft();
                            }, 750);
                        },

                        persistLocalDraft() {
                            if (this.persistPaused) {
                                return;
                            }
                            this.currentStep = this.detectCurrentStep();
                            const snapshot = this.buildDraftPayload();
                            const json = JSON.stringify(snapshot);
                            this.lastSnapshotHash = json;
                            localStorage.setItem(this.buildKey(), json);
                        },

                        parseStoredLocalDraft() {
                            try {
                                const raw = localStorage.getItem(this.buildKey());
                                if (!raw) {
                                    return null;
                                }
                                const parsed = JSON.parse(raw);
                                if (!parsed || typeof parsed !== 'object') {
                                    return null;
                                }
                                return parsed;
                            } catch (error) {
                                return null;
                            }
                        },

                        async handleModalOpened() {
                            const localDraft = this.parseStoredLocalDraft();
                            let serverDraft = null;

                            if (this.userId > 0) {
                                try {
                                    serverDraft = await this.$wire.fetchServerDraft(this.mode, this.patientId);
                                } catch (error) {
                                    serverDraft = null;
                                }
                            }

                            const localCandidate = localDraft ?
                                {
                                    source: 'local',
                                    payload: localDraft,
                                    updatedAt: localDraft.updatedAt || null,
                                    step: localDraft.currentStep || null,
                                } :
                                null;

                            const serverCandidate = serverDraft && serverDraft.payload ?
                                {
                                    source: 'server',
                                    payload: serverDraft.payload,
                                    updatedAt: serverDraft.updatedAt || serverDraft.payload.updatedAt || null,
                                    step: serverDraft.step || serverDraft.payload.currentStep || null,
                                } :
                                null;

                            const chosen = this.pickNewestDraft(localCandidate, serverCandidate);
                            if (!chosen) {
                                this.prompt.visible = false;
                                return;
                            }

                            this.prompt.visible = true;
                            this.prompt.payload = chosen.payload;
                            this.prompt.step = chosen.step || chosen.payload.currentStep || null;
                            this.prompt.updatedAtLabel = this.formatTime(chosen.updatedAt);
                        },

                        pickNewestDraft(localCandidate, serverCandidate) {
                            if (!localCandidate && !serverCandidate) {
                                return null;
                            }
                            if (!localCandidate) {
                                return serverCandidate;
                            }
                            if (!serverCandidate) {
                                return localCandidate;
                            }

                            const localTime = Date.parse(localCandidate.updatedAt || '') || 0;
                            const serverTime = Date.parse(serverCandidate.updatedAt || '') || 0;
                            return serverTime > localTime ? serverCandidate : localCandidate;
                        },

                        async restoreDraft() {
                            if (!this.prompt.payload) {
                                return;
                            }

                            this.restoring = true;
                            const ok = await this.$wire.applyDraftPayload(this.prompt.payload);
                            if (ok) {
                                this.prompt.visible = false;
                                this.prompt.payload = null;
                                this.dirty = true;
                                this.markDirtyDebounced();
                            }
                            this.restoring = false;
                        },

                        async discardDraft() {
                            this.cancelPendingDebounce();
                            localStorage.removeItem(this.buildKey());
                            this.prompt.visible = false;
                            this.prompt.payload = null;
                            this.prompt.step = null;
                            this.prompt.updatedAtLabel = '';
                            this.restoring = false;
                            this.dirty = false;
                            this.lastSnapshotHash = '';
                            this.lastSyncedHash = '';

                            if (this.userId > 0) {
                                await this.$wire.discardDraft(this.mode, this.patientId);
                            }
                        },

                        handleDraftCleared(detail) {
                            if (!detail) {
                                return;
                            }
                            const mode = detail.mode === 'edit' ? 'edit' : 'create';
                            const patientId = Number(detail.patientId || 0);
                            if (mode !== this.mode || patientId !== this.patientId) {
                                return;
                            }
                            this.cancelPendingDebounce();
                            localStorage.removeItem(this.buildKey());
                            this.prompt.visible = false;
                            this.prompt.payload = null;
                            this.prompt.step = null;
                            this.prompt.updatedAtLabel = '';
                            this.restoring = false;
                            this.dirty = false;
                            this.lastSnapshotHash = '';
                            this.lastSyncedHash = '';
                        },

                        async handleSuccessfulSave() {
                            this.pausePersist(3500);
                            this.navigationLoading = false;
                            this.cancelPendingDebounce();
                            localStorage.removeItem(this.buildKey());
                            this.prompt.visible = false;
                            this.prompt.payload = null;
                            this.prompt.step = null;
                            this.prompt.updatedAtLabel = '';
                            this.restoring = false;
                            this.dirty = false;
                            this.lastSnapshotHash = '';
                            this.lastSyncedHash = '';

                            if (this.userId > 0) {
                                try {
                                    await this.$wire.discardDraft(this.mode, this.patientId);
                                } catch (error) {
                                    // no-op fallback: local is already cleared
                                }
                            }
                        },

                        handleNavigationFinished(detail) {
                            if (detail && detail.currentStep) {
                                this.currentStep = Number(detail.currentStep || this.currentStep);
                            }

                            const elapsed = this.navigationStartedAt ? Date.now() - this.navigationStartedAt : 0;
                            const remaining = Math.max(0, 350 - elapsed);

                            window.setTimeout(() => {
                                requestAnimationFrame(() => {
                                    this.navigationLoading = false;
                                    this.navigationStartedAt = 0;
                                });
                            }, remaining);
                        },

                        async handleVisitRecordSelection(event) {
                            const value = event?.target?.value;
                            if (!value) {
                                return;
                            }

                            await this.prepareNavigationLoading();

                            try {
                                await this.$wire.selectVisitRecord(value);
                                this.handleNavigationFinished({
                                    currentStep: this.currentStep,
                                    source: 'record-date-change',
                                    value,
                                });
                            } catch (error) {
                                this.navigationLoading = false;
                                this.navigationStartedAt = 0;
                            }
                        },

                        async handleStepNavigation(step) {
                            const targetStep = Number(step || 0);
                            if (!targetStep || targetStep === this.currentStep) {
                                return;
                            }

                            await this.prepareNavigationLoading();
                            await this.$wire.goToStep(targetStep);
                        },

                        async prepareNavigationLoading() {
                            if (!this.navigationLoading) {
                                this.navigationLoading = true;
                                this.navigationStartedAt = Date.now();
                            }

                            await new Promise((resolve) => requestAnimationFrame(resolve));
                            await new Promise((resolve) => requestAnimationFrame(resolve));
                        },

                        startSyncTimer() {
                            if (this.syncTimer) {
                                clearInterval(this.syncTimer);
                            }

                            this.syncTimer = setInterval(() => {
                                this.syncIfNeeded();
                            }, 15000);
                        },

                        stopSyncTimer() {
                            if (this.syncTimer) {
                                clearInterval(this.syncTimer);
                                this.syncTimer = null;
                            }
                            this.cancelPendingDebounce();
                        },

                        async syncIfNeeded() {
                            if (!this.dirty || this.userId <= 0 || this.persistPaused) {
                                return;
                            }

                            this.currentStep = this.detectCurrentStep();
                            const snapshot = this.buildDraftPayload();
                            const hash = JSON.stringify(snapshot);
                            if (!hash || hash === this.lastSyncedHash) {
                                return;
                            }

                            const response = await this.$wire.saveDraftFromClient(snapshot);
                            if (response && response.ok) {
                                this.lastSyncedHash = hash;
                                this.dirty = false;
                            }
                        },

                        buildDraftPayload() {
                            this.currentStep = this.detectCurrentStep();
                            const basicFields = [
                                'last_name', 'first_name', 'middle_name', 'nickname', 'occupation', 'birth_date',
                                'gender', 'civil_status',
                                'home_address', 'office_address', 'home_number', 'office_number', 'mobile_number',
                                'email_address', 'referral',
                                'emergency_contact_name', 'emergency_contact_number', 'relationship',
                                'who_answering', 'relationship_to_patient',
                                'father_name', 'father_number', 'mother_name', 'mother_number', 'guardian_name',
                                'guardian_number'
                            ];
                            const healthFields = [
                                'when_last_visit_q1', 'what_last_visit_reason_q1', 'what_seeing_dentist_reason_q2',
                                'is_clicking_jaw_q3a', 'is_pain_jaw_q3b', 'is_difficulty_opening_closing_q3c',
                                'is_locking_jaw_q3d',
                                'is_clench_grind_q4', 'is_bad_experience_q5', 'is_nervous_q6',
                                'what_nervous_concern_q6',
                                'is_condition_q1', 'what_condition_reason_q1', 'is_hospitalized_q2',
                                'what_hospitalized_reason_q2',
                                'is_serious_illness_operation_q3', 'what_serious_illness_operation_reason_q3',
                                'is_taking_medications_q4',
                                'what_medications_list_q4', 'is_allergic_medications_q5', 'what_allergies_list_q5',
                                'is_allergic_latex_rubber_metals_q6', 'is_pregnant_q7', 'is_breast_feeding_q8'
                            ];
                            const treatmentFields = ['dmd', 'treatment', 'cost_of_treatment', 'amount_charged',
                                'remarks'
                            ];

                            const dentalStore = Alpine.store('dentalChart');
                            const dentalTeeth = dentalStore && typeof dentalStore.toPlain === 'function' ?
                                dentalStore.toPlain(dentalStore.localTeeth || {}) :
                                {};

                            let dentitionType = 'adult';
                            const toggles = Array.from(document.querySelectorAll('[wire\\:click*="dentitionType"]'));
                            for (const btn of toggles) {
                                if (btn.classList.contains('bg-sky-600') && btn.textContent.trim().toLowerCase() ===
                                    'child') {
                                    dentitionType = 'child';
                                }
                            }

                            const oralExam = {};
                            [
                                'oral_hygiene_status',
                                'gingiva',
                                'calcular_deposits',
                                'stains',
                                'complete_denture',
                                'partial_denture'
                            ].forEach((field) => {
                                oralExam[field] = this.getFieldValue(`oralExam.${field}`);
                            });

                            const chartComments = {};
                            ['notes', 'treatment_plan'].forEach((field) => {
                                chartComments[field] = this.getFieldValue(`chartComments.${field}`);
                            });

                            return {
                                currentStep: this.currentStep,
                                basicInfo: this.getFieldValues(basicFields),
                                healthHistory: this.getFieldValues(healthFields),
                                dentalChart: {
                                    teeth: dentalTeeth,
                                    oralExam,
                                    chartComments,
                                    dentitionType,
                                    numberingSystem: 'FDI'
                                },
                                treatmentRecord: this.getFieldValues(treatmentFields),
                                updatedAt: new Date().toISOString(),
                                mode: this.mode,
                                patientId: this.patientId
                            };
                        },

                        detectCurrentStep() {
                            const modal = document.querySelector('[data-patient-form-modal]');
                            if (!modal) {
                                return Number(this.currentStep || 1);
                            }

                            // Step 4 marker (treatment record)
                            if (modal.querySelector(
                                '[wire\\:model\\.defer="dmd"], [wire\\:model\\.defer="treatment"]')) {
                                return 4;
                            }

                            // Step 3 marker (dental chart)
                            if (modal.querySelector(
                                    '[wire\\:model\\.defer="oralExam.gingiva"], [wire\\:model\\.defer="oralExam.oral_hygiene_status"]'
                                    )) {
                                return 3;
                            }

                            // Step 2 marker (health history)
                            if (modal.querySelector('[wire\\:model\\.defer="what_seeing_dentist_reason_q2"]')) {
                                return 2;
                            }

                            return 1;
                        },

                        getFieldValues(fields) {
                            const data = {};
                            fields.forEach((field) => {
                                data[field] = this.getFieldValue(field);
                            });
                            return data;
                        },

                        getFieldValue(model) {
                            const escapedModel = model.replace(/\./g, '\\\\.');
                            const selectors = [
                                `[wire\\:model\\.defer="${model}"]`,
                                `[wire\\:model\\.live="${model}"]`,
                                `[wire\\:model="${model}"]`,
                                `[wire\\:model\\.defer="${escapedModel}"]`,
                                `[wire\\:model\\.live="${escapedModel}"]`,
                                `[wire\\:model="${escapedModel}"]`,
                            ];

                            const nodes = Array.from(document.querySelectorAll(selectors.join(',')));
                            if (!nodes.length) {
                                return null;
                            }

                            if (nodes[0].type === 'radio') {
                                const checked = nodes.find((node) => node.checked);
                                return checked ? checked.value : null;
                            }

                            return nodes[0].value ?? null;
                        },

                        formatTime(value) {
                            if (!value) {
                                return 'Unknown';
                            }
                            const parsed = new Date(value);
                            if (Number.isNaN(parsed.getTime())) {
                                return 'Unknown';
                            }
                            return parsed.toLocaleString();
                        },

                        cancelPendingDebounce() {
                            if (this.debounceTimer) {
                                clearTimeout(this.debounceTimer);
                                this.debounceTimer = null;
                            }
                        },

                        pausePersist(ms = 2000) {
                            this.persistPaused = true;
                            this.cancelPendingDebounce();
                            window.setTimeout(() => {
                                this.persistPaused = false;
                            }, Number(ms) || 2000);
                        },

                        handleClickCapture(event) {
                            if (!event?.target || this.persistPaused) {
                                return;
                            }

                            const ignored = event.target.closest('[data-draft-ignore]');
                            if (ignored) {
                                return;
                            }

                            this.markDirtyDebounced();
                        }
                    };
                };

                const scrollToField = (field) => {
                    const modal = document.querySelector('[data-patient-form-modal]');
                    if (!modal) return;

                    const errorTarget = field ?
                        modal.querySelector('[data-error-for="' + field + '"]') :
                        null;

                    const inputSelector = field ?
                        [
                            '[wire\\:model="' + field + '"]',
                            '[wire\\:model\\.defer="' + field + '"]',
                            '[wire\\:model\\.live="' + field + '"]',
                            '[name="' + field + '"]',
                            '#' + CSS.escape(field)
                        ].join(',') :
                        null;

                    const inputTarget = inputSelector ? modal.querySelector(inputSelector) : null;
                    const target = errorTarget || inputTarget;

                    if (!target) return;

                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });

                    const scrollContainer =
                        target.closest('[data-form-scroll]') || modal.querySelector('[data-form-scroll]');

                    if (scrollContainer) {
                        const containerRect = scrollContainer.getBoundingClientRect();
                        const targetRect = target.getBoundingClientRect();
                        const targetTop = targetRect.top - containerRect.top + scrollContainer.scrollTop - 20;
                        scrollContainer.scrollTo({
                            top: targetTop,
                            behavior: 'smooth'
                        });
                    }
                };

                const findFieldKeyFromElement = (element) => {
                    if (!(element instanceof HTMLElement)) {
                        return null;
                    }

                    const explicit = element.getAttribute('data-validate-field');
                    if (explicit) {
                        return explicit;
                    }

                    for (const attrName of element.getAttributeNames()) {
                        if (attrName.startsWith('wire:model')) {
                            const model = element.getAttribute(attrName);
                            if (model) {
                                return model;
                            }
                        }
                    }

                    return null;
                };

                const clearFieldValidationUI = (fieldKey, sourceElement) => {
                    const modal = document.querySelector('[data-patient-form-modal]');
                    if (!modal || !fieldKey) return;

                    modal.querySelectorAll('[data-error-for="' + fieldKey + '"]').forEach((errorEl) => {
                        errorEl.classList.add('hidden');
                    });

                    if (sourceElement instanceof HTMLElement) {
                        sourceElement.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-200');
                    }
                };

                const bindValidationDismissal = () => {
                    const modal = document.querySelector('[data-patient-form-modal]');
                    if (!modal || modal.dataset.validationDismissBound === '1') return;
                    modal.dataset.validationDismissBound = '1';

                    const dismissHandler = (event) => {
                        const target = event.target;
                        if (!(target instanceof HTMLElement)) return;

                        const fieldKey = findFieldKeyFromElement(target);
                        if (!fieldKey) return;

                        clearFieldValidationUI(fieldKey, target);
                    };

                    modal.addEventListener('input', dismissHandler);
                    modal.addEventListener('change', dismissHandler);
                };

                document.addEventListener('scroll-to-error', (event) => {
                    const field = event?.detail?.field || null;
                    scrollToField(field);
                });

                document.addEventListener('patient-form-opened', () => {
                    bindValidationDismissal();
                });

                bindValidationDismissal();
            })();
        </script>
    @endpush
</div>
