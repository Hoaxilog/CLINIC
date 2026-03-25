<div class="relative flex h-full w-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white"
    x-data="{
        historyLoading: <?php echo e((count($historyList) > 0 || $isCreating) ? 'true' : 'false'); ?>,
        nervous: <?php echo \Illuminate\Support\Js::from((string) $is_nervous_q6)->toHtml() ?>,
        condition: <?php echo \Illuminate\Support\Js::from((string) $is_condition_q1)->toHtml() ?>,
        hospitalized: <?php echo \Illuminate\Support\Js::from((string) $is_hospitalized_q2)->toHtml() ?>,
        seriousIllness: <?php echo \Illuminate\Support\Js::from((string) $is_serious_illness_operation_q3)->toHtml() ?>,
        medications: <?php echo \Illuminate\Support\Js::from((string) $is_taking_medications_q4)->toHtml() ?>,
        allergies: <?php echo \Illuminate\Support\Js::from((string) $is_allergic_medications_q5)->toHtml() ?>
    }"
    x-on:show-health-history-loading.window="historyLoading = true"
    x-on:health-history-ready.window="historyLoading = false">
    <div x-cloak x-show="historyLoading"
        class="absolute inset-0 z-30 flex items-center justify-center bg-white/70 backdrop-blur-sm text-center">
        <div class="flex flex-col items-center gap-3">
            <div class="h-10 w-10 animate-spin rounded-full border-4 border-blue-200 border-t-[#0086da]"></div>
            <div class="text-sm font-semibold text-gray-700">Loading health history...</div>
        </div>
    </div>
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($historyList) > 0 || $isCreating): ?>

        <div class="sticky top-0 z-20 flex items-center justify-between border-b border-slate-200 bg-white px-5 py-4"
            x-init="$nextTick(() => { $dispatch('health-history-ready'); })">
            <div class="flex items-center gap-4">
                <h2 class="text-lg font-semibold text-slate-900">Health History</h2>
            </div>

            <div class="flex items-center gap-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isReadOnly && count($historyList) > 0 && ! $isCreating): ?>
                    <button type="button" wire:click="$dispatch('openNewVisitRecord')"
                        class="inline-flex items-center gap-2 rounded-lg bg-sky-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-sky-700"
                        title="Start a fresh connected record">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                        New Record
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <div data-health-history-scroll data-form-scroll
            class="flex-1 overflow-y-auto p-4 lg:p-6 scrollbar-thin scrollbar-thumb-rounded-full scrollbar-track-slate-100 scrollbar-thumb-slate-300">

            <?php
                $lastVisitDateClass = $errors->has('when_last_visit_q1')
                    ? 'w-full border border-red-500 rounded px-4 py-3 text-base focus:ring-red-200 focus:border-red-500'
                    : 'w-full border rounded px-4 py-3 text-base focus:ring-blue-500 focus:border-blue-500';
                $lastVisitReasonClass = $errors->has('what_last_visit_reason_q1')
                    ? 'w-full border border-red-500 rounded px-4 py-3 text-base focus:ring-red-200 focus:border-red-500'
                    : 'w-full border rounded px-4 py-3 text-base focus:ring-blue-500 focus:border-blue-500';
                $todayReasonClass = $errors->has('what_seeing_dentist_reason_q2')
                    ? 'w-full border border-red-500 rounded px-4 py-3 text-base focus:ring-red-200 focus:border-red-500'
                    : 'w-full border rounded px-4 py-3 text-base focus:ring-blue-500 focus:border-blue-500';
            ?>

            <div class="mb-6 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                <h2 class="text-base font-semibold text-slate-900">Dental History</h2>
            </div>

            <div class="space-y-6 mb-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-lg font-medium text-gray-700 mb-2">1. Date of last dental visit (Optional)</label>
                        <input <?php if($isReadOnly && !$isCreating): ?> disabled <?php endif; ?> wire:model.defer="when_last_visit_q1" type="date"
                            class="<?php echo e($lastVisitDateClass); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['when_last_visit_q1'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span data-error-for="when_last_visit_q1" class="text-red-500 text-sm"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div>
                        <label class="block text-lg font-medium text-gray-700 mb-2">What was done in your last dental (Optional)</label>
                        <input <?php if($isReadOnly && !$isCreating): ?> disabled <?php endif; ?> wire:model.defer="what_last_visit_reason_q1" type="text"
                            class="<?php echo e($lastVisitReasonClass); ?>"
                            placeholder="e.g., Cleaning, Filling...">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['what_last_visit_reason_q1'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span data-error-for="what_last_visit_reason_q1" class="text-red-500 text-sm"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <div>
                    <label class="block text-lg font-medium text-gray-700 mb-2">2. Reason for seeing dentist
                        today? <span class="text-red-600">*</span></label>
                    <input <?php if($isReadOnly && !$isCreating): ?> disabled <?php endif; ?> wire:model.defer="what_seeing_dentist_reason_q2" type="text"
                        class="<?php echo e($todayReasonClass); ?>"
                        placeholder="e.g., Check-up...">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['what_seeing_dentist_reason_q2'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span data-error-for="what_seeing_dentist_reason_q2" class="text-red-500 text-sm"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div>
                    <label class="block text-lg font-medium text-gray-700 mb-2">3. Have you experienced? <span class="text-red-600">*</span></label>
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 pl-4 bg-gray-50 p-4 rounded-lg border border-gray-100">

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [['label' => 'A. Clicking of the Jaw?', 'model' => 'is_clicking_jaw_q3a'], ['label' => 'B. Pain below the ear?', 'model' => 'is_pain_jaw_q3b'], ['label' => 'C. Difficulty opening/closing?', 'model' => 'is_difficulty_opening_closing_q3c'], ['label' => 'D. Locking of the jaw?', 'model' => 'is_locking_jaw_q3d']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <div>
                                <label class="block text-base font-medium text-gray-700"><?php echo e($q['label']); ?></label>
                                <div class="flex gap-x-6 mt-1">
                                    <label class="flex items-center cursor-pointer">
                                        <input <?php if($isReadOnly && !$isCreating): ?> disabled <?php endif; ?> wire:model.defer="<?php echo e($q['model']); ?>"
                                            type="radio" value="1"
                                            class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <span class="ml-2 text-sm font-bold text-blue-700">YES</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input <?php if($isReadOnly && !$isCreating): ?> disabled <?php endif; ?> wire:model.defer="<?php echo e($q['model']); ?>"
                                            type="radio" value="0"
                                            class="h-4 w-4 text-gray-400 border-gray-300 focus:ring-gray-500">
                                        <span class="ml-2 text-sm text-gray-600">NO</span>
                                    </label>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = [$q['model']];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span data-error-for="<?php echo e($q['model']); ?>" class="text-red-500 text-sm block mt-1"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [['q' => '4. Do you clench or grind your teeth?', 'model' => 'is_clench_grind_q4'], ['q' => '5. Bad experience in dental office?', 'model' => 'is_bad_experience_q5'], ['q' => '6. Feel nervous about treatment?', 'model' => 'is_nervous_q6', 'detail' => 'what_nervous_concern_q6', 'placeholder' => 'What is your concern?']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <div class="border-b border-gray-100 pb-4">
                        <label class="block text-lg font-medium text-gray-700"><?php echo e($item['q']); ?> <span class="text-red-600">*</span></label>
                        <div class="flex gap-x-6 mt-2">
                            <label class="flex items-center cursor-pointer">
                                <input <?php if($isReadOnly && !$isCreating): ?> disabled <?php endif; ?> wire:model.defer="<?php echo e($item['model']); ?>"
                                    <?php if(($item['model'] ?? null) === 'is_nervous_q6'): ?> x-model="nervous" <?php endif; ?>
                                    type="radio" value="1" class="h-4 w-4 text-blue-600">
                                <span class="ml-2 text-sm font-bold text-blue-700">YES</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input <?php if($isReadOnly && !$isCreating): ?> disabled <?php endif; ?> wire:model.defer="<?php echo e($item['model']); ?>"
                                    <?php if(($item['model'] ?? null) === 'is_nervous_q6'): ?> x-model="nervous" <?php endif; ?>
                                    type="radio" value="0" class="h-4 w-4 text-gray-400">
                                <span class="ml-2 text-sm text-gray-600">NO</span>
                            </label>
                        </div>
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = [$item['model']];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span data-error-for="<?php echo e($item['model']); ?>" class="text-red-500 text-sm block mt-1"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($item['detail'])): ?>
                            <div x-cloak x-show="nervous === '1'" class="mt-2 pl-4 border-l-2 border-blue-200">
                                <input <?php if($isReadOnly && !$isCreating): ?> disabled <?php endif; ?> wire:model.defer="<?php echo e($item['detail']); ?>" type="text"
                                    class="w-full border rounded px-3 py-2 text-sm <?php $__errorArgs = [$item['detail']];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 focus:border-red-500 focus:ring-red-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="<?php echo e($item['placeholder']); ?>">
                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = [$item['detail']];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span data-error-for="<?php echo e($item['detail']); ?>" class="text-red-500 text-sm block mt-1"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>

            <div class="mb-6 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                <h2 class="text-base font-semibold text-slate-900">Medical History</h2>
            </div>

            <div class="space-y-6">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [['q' => '1. Treated for medical condition (present/past 2 years)?', 'model' => 'is_condition_q1', 'detail' => 'what_condition_reason_q1'], ['q' => '2. Ever been hospitalized?', 'model' => 'is_hospitalized_q2', 'detail' => 'what_hospitalized_reason_q2'], ['q' => '3. Serious illness or operation?', 'model' => 'is_serious_illness_operation_q3', 'detail' => 'what_serious_illness_operation_reason_q3'], ['q' => '4. Taking any medications?', 'model' => 'is_taking_medications_q4', 'detail' => 'what_medications_list_q4'], ['q' => '5. Allergic to medications?', 'model' => 'is_allergic_medications_q5', 'detail' => 'what_allergies_list_q5'], ['q' => '6. Allergic to latex/rubber/metals?', 'model' => 'is_allergic_latex_rubber_metals_q6']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $med): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <div class="border-b border-gray-100 pb-4">
                        <label class="block text-lg font-medium text-gray-700"><?php echo e($med['q']); ?> <span class="text-red-600">*</span></label>
                        <div class="flex gap-x-6 mt-2">
                            <label class="flex items-center cursor-pointer">
                                <input <?php if($isReadOnly && !$isCreating): ?> disabled <?php endif; ?> wire:model.defer="<?php echo e($med['model']); ?>"
                                    <?php if(($med['model'] ?? null) === 'is_condition_q1'): ?> x-model="condition" <?php endif; ?>
                                    <?php if(($med['model'] ?? null) === 'is_hospitalized_q2'): ?> x-model="hospitalized" <?php endif; ?>
                                    <?php if(($med['model'] ?? null) === 'is_serious_illness_operation_q3'): ?> x-model="seriousIllness" <?php endif; ?>
                                    <?php if(($med['model'] ?? null) === 'is_taking_medications_q4'): ?> x-model="medications" <?php endif; ?>
                                    <?php if(($med['model'] ?? null) === 'is_allergic_medications_q5'): ?> x-model="allergies" <?php endif; ?>
                                    type="radio" value="1" class="h-4 w-4 text-blue-600">
                                <span class="ml-2 text-sm font-bold text-blue-700">YES</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input <?php if($isReadOnly && !$isCreating): ?> disabled <?php endif; ?> wire:model.defer="<?php echo e($med['model']); ?>"
                                    <?php if(($med['model'] ?? null) === 'is_condition_q1'): ?> x-model="condition" <?php endif; ?>
                                    <?php if(($med['model'] ?? null) === 'is_hospitalized_q2'): ?> x-model="hospitalized" <?php endif; ?>
                                    <?php if(($med['model'] ?? null) === 'is_serious_illness_operation_q3'): ?> x-model="seriousIllness" <?php endif; ?>
                                    <?php if(($med['model'] ?? null) === 'is_taking_medications_q4'): ?> x-model="medications" <?php endif; ?>
                                    <?php if(($med['model'] ?? null) === 'is_allergic_medications_q5'): ?> x-model="allergies" <?php endif; ?>
                                    type="radio" value="0" class="h-4 w-4 text-gray-400">
                                <span class="ml-2 text-sm text-gray-600">NO</span>
                            </label>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = [$med['model']];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span data-error-for="<?php echo e($med['model']); ?>" class="text-red-500 text-sm block mt-1"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($med['detail'])): ?>
                            <div
                                x-cloak
                                x-show="
                                    ('<?php echo e($med['model']); ?>' === 'is_condition_q1' && condition === '1') ||
                                    ('<?php echo e($med['model']); ?>' === 'is_hospitalized_q2' && hospitalized === '1') ||
                                    ('<?php echo e($med['model']); ?>' === 'is_serious_illness_operation_q3' && seriousIllness === '1') ||
                                    ('<?php echo e($med['model']); ?>' === 'is_taking_medications_q4' && medications === '1') ||
                                    ('<?php echo e($med['model']); ?>' === 'is_allergic_medications_q5' && allergies === '1')
                                "
                                class="mt-2 pl-4 border-l-2 border-blue-200">
                                <input <?php if($isReadOnly && !$isCreating): ?> disabled <?php endif; ?> wire:model.defer="<?php echo e($med['detail']); ?>" type="text"
                                    class="w-full border rounded px-3 py-2 text-sm <?php $__errorArgs = [$med['detail']];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 focus:border-red-500 focus:ring-red-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    placeholder="Please specify details...">
                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = [$med['detail']];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span data-error-for="<?php echo e($med['detail']); ?>" class="text-red-500 text-sm block mt-1"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($gender === 'Female'): ?>
                <div class="mb-6 mt-10 rounded-xl border border-pink-200 bg-pink-50 px-4 py-3">
                    <h2 class="text-base font-semibold text-pink-800">For Women Only</h2>
                </div>
                <div class="space-y-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [['q' => '7. Are you pregnant?', 'model' => 'is_pregnant_q7'], ['q' => '8. Are you breast feeding?', 'model' => 'is_breast_feeding_q8']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <div>
                            <label class="block text-lg font-medium text-gray-700"><?php echo e($fem['q']); ?> <span class="text-red-600">*</span></label>
                            <div class="flex gap-x-6 mt-2">
                                <label class="flex items-center cursor-pointer">
                                    <input <?php if($isReadOnly && !$isCreating): ?> disabled <?php endif; ?> wire:model.defer="<?php echo e($fem['model']); ?>"
                                        type="radio" value="1" class="h-4 w-4 text-pink-600">
                                    <span class="ml-2 text-sm font-bold text-pink-700">YES</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input <?php if($isReadOnly && !$isCreating): ?> disabled <?php endif; ?> wire:model.defer="<?php echo e($fem['model']); ?>"
                                        type="radio" value="0" class="h-4 w-4 text-gray-400">
                                    <span class="ml-2 text-sm text-gray-600">NO</span>
                                </label>
                            </div>
                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = [$fem['model']];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span data-error-for="<?php echo e($fem['model']); ?>" class="text-red-500 text-sm block mt-1"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php else: ?>
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
            <button type="button" wire:click="$dispatch('openNewVisitRecord')"
                class="flex items-center gap-2 px-6 py-3 bg-[#0086da] text-white text-lg font-bold rounded-lg shadow-lg hover:scale-105 transition-all transform">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M12 5v14M5 12h14" />
                </svg>
                New Record
            </button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

</div>


<?php /**PATH C:\JOELJOELJOELJOELJOEL\CLINIC\resources\views/livewire/patient/form/health-history.blade.php ENDPATH**/ ?>