<div class="w-full space-y-6">
    <?php
        $labelClass = 'mb-1.5 block text-sm font-semibold text-slate-700';
        $errorBag = session('errors');
        $inputClass =
            'w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100 disabled:bg-slate-100 disabled:text-slate-500';
        $fieldClass = fn(string $field) => ($errorBag && $errorBag->has($field))
            ? $inputClass . ' border-red-500 focus:border-red-500 focus:ring-red-200'
            : $inputClass;
    ?>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 md:p-6">
        <h2 class="text-lg font-semibold text-slate-900">Treatment Record</h2>
        <p class="mt-1 text-sm text-slate-500">Capture treatment details, billing, and attached images.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
        <div class="xl:col-span-7 rounded-2xl border border-slate-200 bg-white p-5 md:p-6">
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label for="dmd" class="<?php echo e($labelClass); ?>">DMD <span class="text-red-600">*</span></label>
                    <input wire:model.defer="dmd" type="text" id="dmd" class="<?php echo e($fieldClass('dmd')); ?>"
                        placeholder="e.g., Dr. Name" <?php if($isReadOnly): ?> disabled <?php endif; ?>>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['dmd'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span data-error-for="dmd" class="mt-1 block text-xs text-red-500"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div>
                    <label for="treatment" class="<?php echo e($labelClass); ?>">Treatment <span class="text-red-600">*</span></label>
                    <input wire:model.defer="treatment" type="text" id="treatment" class="<?php echo e($fieldClass('treatment')); ?>"
                        placeholder="e.g., Extraction" <?php if($isReadOnly): ?> disabled <?php endif; ?>>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['treatment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span data-error-for="treatment" class="mt-1 block text-xs text-red-500"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div>
                    <label for="cost_of_treatment" class="<?php echo e($labelClass); ?>">Estimated Cost <span class="text-red-600">*</span></label>
                    <input wire:model.defer="cost_of_treatment" type="number" id="cost_of_treatment"
                        class="<?php echo e($fieldClass('cost_of_treatment')); ?>" placeholder="0.00" <?php if($isReadOnly): ?> disabled <?php endif; ?>>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['cost_of_treatment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span data-error-for="cost_of_treatment"
                            class="mt-1 block text-xs text-red-500"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div>
                    <label for="amount_charged" class="<?php echo e($labelClass); ?>">Payment <span class="text-red-600">*</span></label>
                    <input wire:model.defer="amount_charged" type="number" id="amount_charged" class="<?php echo e($fieldClass('amount_charged')); ?>"
                        placeholder="0.00" <?php if($isReadOnly): ?> disabled <?php endif; ?>>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['amount_charged'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span data-error-for="amount_charged"
                            class="mt-1 block text-xs text-red-500"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="md:col-span-2">
                    <label for="remarks" class="<?php echo e($labelClass); ?>">Remarks</label>
                    <textarea wire:model.defer="remarks" id="remarks" rows="5" class="<?php echo e($fieldClass('remarks')); ?>"
                        placeholder="Enter notes here..." <?php if($isReadOnly): ?> disabled <?php endif; ?>></textarea>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['remarks'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span data-error-for="remarks" class="mt-1 block text-xs text-red-500"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        <div class="xl:col-span-5" x-data="{ showImage: false, activeImage: '', activeLabel: '' }">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 md:p-6">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-700">Treatment Images</h3>

                <div class="mt-4 space-y-5">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isReadOnly): ?>
                        <div>
                            <label for="beforeImages" class="<?php echo e($labelClass); ?>">Before</label>
                            <input wire:model="beforeImages" type="file" id="beforeImages" multiple
                                class="<?php echo e($fieldClass('beforeImages')); ?>" accept="image/*">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['beforeImages'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="mt-1 block text-xs text-red-500"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['beforeImages.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="mt-1 block text-xs text-red-500"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div>
                            <label for="afterImages" class="<?php echo e($labelClass); ?>">After</label>
                            <input wire:model="afterImages" type="file" id="afterImages" multiple
                                class="<?php echo e($fieldClass('afterImages')); ?>" accept="image/*">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['afterImages'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="mt-1 block text-xs text-red-500"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['afterImages.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="mt-1 block text-xs text-red-500"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php
                        $beforeList = collect($existingImages)
                            ->filter(fn($i) => ($i['image_type'] ?? '') === 'before')
                            ->values();
                        $afterList = collect($existingImages)
                            ->filter(fn($i) => ($i['image_type'] ?? '') === 'after')
                            ->values();
                    ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($beforeList->isNotEmpty()): ?>
                        <div>
                            <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Before Treatment
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $beforeList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <button type="button" class="overflow-hidden rounded-lg border border-slate-200 bg-white p-1"
                                        @click="activeImage = '<?php echo e(\Illuminate\Support\Facades\Storage::url($img['image_path'])); ?>'; activeLabel = 'before'; showImage = true">
                                        <img class="h-32 w-full rounded-md object-cover"
                                            src="<?php echo e(\Illuminate\Support\Facades\Storage::url($img['image_path'])); ?>"
                                            alt="Before image">
                                    </button>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($afterList->isNotEmpty()): ?>
                        <div>
                            <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">After Treatment
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $afterList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <button type="button" class="overflow-hidden rounded-lg border border-slate-200 bg-white p-1"
                                        @click="activeImage = '<?php echo e(\Illuminate\Support\Facades\Storage::url($img['image_path'])); ?>'; activeLabel = 'after'; showImage = true">
                                        <img class="h-32 w-full rounded-md object-cover"
                                            src="<?php echo e(\Illuminate\Support\Facades\Storage::url($img['image_path'])); ?>"
                                            alt="After image">
                                    </button>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($existingImages)): ?>
                        <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-5 text-center text-sm text-slate-500">
                            No treatment images yet.
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <div x-show="showImage" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-6"
                @click.self="showImage = false">
                <div class="w-full max-w-4xl rounded-xl bg-white p-4 shadow-xl">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-semibold uppercase tracking-wide text-slate-700" x-text="activeLabel"></div>
                        <button type="button" class="text-sm font-medium text-slate-500 hover:text-slate-800"
                            @click="showImage = false">Close</button>
                    </div>
                    <img class="mt-4 max-h-[75vh] w-full rounded-lg object-contain" :src="activeImage" alt="Full image">
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\JOELJOELJOELJOELJOEL\CLINIC\resources\views/livewire/patient/form/treatment-record.blade.php ENDPATH**/ ?>