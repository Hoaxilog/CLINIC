<section class="space-y-8">
    @php
        $labelClass = 'mb-1.5 block text-sm font-semibold text-[#1a2e3b]';
        $errorBag = session('errors');
        $inputClass =
            'w-full rounded-md border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-[#1a2e3b] placeholder:text-gray-400 focus:border-[#0086da] focus:outline-none focus:ring-2 focus:ring-[#e8f4fc] transition';
        $fieldClass = fn(string $field) => ($errorBag && $errorBag->has($field))
            ? $inputClass . ' border-red-400 focus:border-red-500 focus:ring-red-100'
            : $inputClass;
        $readonlyClass =
            'w-full rounded-md border border-gray-200 bg-[#f6fafd] px-3.5 py-2.5 text-sm text-gray-500 cursor-not-allowed';
    @endphp

    <div class="rounded-sm border border-gray-200 bg-white p-5 md:p-6">
        <div class="mb-5 border-b border-gray-100 pb-4">
            <h2 class="text-base font-semibold text-[#1a2e3b]">Patient Information</h2>
            <p class="mt-1 text-xs text-gray-500">Fill in the patient's profile and contact details below.</p>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
            <div>
                <label for="last_name" class="{{ $labelClass }}">Last Name <span class="text-red-600">*</span></label>
                <input wire:model.defer="last_name" type="text" id="last_name" class="{{ $fieldClass('last_name') }}"
                    placeholder="e.g., Dela Cruz">
                @error('last_name')
                    <span data-error-for="last_name" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="first_name" class="{{ $labelClass }}">First Name <span class="text-red-600">*</span></label>
                <input wire:model.defer="first_name" type="text" id="first_name" class="{{ $fieldClass('first_name') }}"
                    placeholder="e.g., Juan">
                @error('first_name')
                    <span data-error-for="first_name" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="middle_name" class="{{ $labelClass }}">Middle Name</label>
                <input wire:model.defer="middle_name" type="text" id="middle_name" class="{{ $fieldClass('middle_name') }}"
                    placeholder="e.g., Reyes">
                @error('middle_name')
                    <span data-error-for="middle_name" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="suffix" class="{{ $labelClass }}">Suffix</label>
                <select wire:model.defer="suffix" id="suffix" class="{{ $fieldClass('suffix') }}">
                    <option value="">None</option>
                    <option value="Jr.">Jr.</option>
                    <option value="Sr.">Sr.</option>
                    <option value="II">II</option>
                    <option value="III">III</option>
                    <option value="IV">IV</option>
                    <option value="V">V</option>
                </select>
                @error('suffix')
                    <span data-error-for="suffix" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="nickname" class="{{ $labelClass }}">Nickname</label>
                <input wire:model.defer="nickname" type="text" id="nickname" class="{{ $fieldClass('nickname') }}"
                    placeholder="e.g., Jun">
                @error('nickname')
                    <span data-error-for="nickname" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="occupation" class="{{ $labelClass }}">Occupation <span class="text-red-600">*</span></label>
                <input wire:model.defer="occupation" type="text" id="occupation" class="{{ $fieldClass('occupation') }}"
                    placeholder="e.g., Engineer">
                @error('occupation')
                    <span data-error-for="occupation" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="birth_date" class="{{ $labelClass }}">Date of Birth <span class="text-red-600">*</span></label>
                <input wire:model.live="birth_date" type="date" id="birth_date" class="{{ $fieldClass('birth_date') }}">
                @error('birth_date')
                    <span data-error-for="birth_date" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="{{ $labelClass }}">Age</label>
                <input type="text" value="{{ $this->age }}" readonly class="{{ $readonlyClass }}" placeholder="Age">
            </div>

            <div>
                <label class="{{ $labelClass }}">Sex <span class="text-red-600">*</span></label>
                <select wire:model.defer="gender" class="{{ $fieldClass('gender') }}">
                    <option value="" >Select...</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
                @error('gender')
                    <span data-error-for="gender" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="civil_status" class="{{ $labelClass }}">Civil Status <span class="text-red-600">*</span></label>
                <input wire:model.defer="civil_status" type="text" id="civil_status" class="{{ $fieldClass('civil_status') }}"
                    placeholder="e.g., Single">
                @error('civil_status')
                    <span data-error-for="civil_status" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div class="md:col-span-2 xl:col-span-2">
                <label for="home_address" class="{{ $labelClass }}">Home Address <span class="text-red-600">*</span></label>
                <input wire:model.defer="home_address" type="text" id="home_address" class="{{ $fieldClass('home_address') }}"
                    placeholder="e.g., 123 Rizal St, Brgy. 1, Manila">
                @error('home_address')
                    <span data-error-for="home_address" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="home_number" class="{{ $labelClass }}">Home Phone Number</label>
                <input wire:model.defer="home_number" type="text" inputmode="numeric" pattern="[0-9]*"
                    oninput="this.value = this.value.replace(/\D/g, '')" id="home_number"
                    class="{{ $fieldClass('home_number') }}" placeholder="e.g., 0281234567">
                @error('home_number')
                    <span data-error-for="home_number" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div class="md:col-span-2 xl:col-span-2">
                <label for="office_address" class="{{ $labelClass }}">Office Address</label>
                <input wire:model.defer="office_address" type="text" id="office_address" class="{{ $fieldClass('office_address') }}"
                    placeholder="e.g., 456 Ayala Ave, Makati">
                @error('office_address')
                    <span data-error-for="office_address" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="office_number" class="{{ $labelClass }}">Office Phone Number</label>
                <input wire:model.defer="office_number" type="text" inputmode="numeric" pattern="[0-9]*"
                    oninput="this.value = this.value.replace(/\D/g, '')" id="office_number"
                    class="{{ $fieldClass('office_number') }}" placeholder="e.g., 0288888888">
                @error('office_number')
                    <span data-error-for="office_number" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="mobile_number" class="{{ $labelClass }}">Mobile Number <span class="text-red-600">*</span></label>
                <div class="flex min-w-0">
                    <span class="inline-flex shrink-0 items-center px-3 border border-r-0 border-gray-200 bg-gray-50 text-gray-500 text-sm font-semibold select-none">+63</span>
                    <input wire:model.defer="mobile_number" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="10"
                        oninput="this.value = this.value.replace(/\D/g, '').replace(/^0+/, '').slice(0, 10)" id="mobile_number" class="{{ $fieldClass('mobile_number') }} min-w-0 flex-1"
                        placeholder="9171234567">
                </div>
                @error('mobile_number')
                    <span data-error-for="mobile_number" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="email_address" class="{{ $labelClass }}">E-mail Address <span class="text-red-600">*</span></label>
                <input wire:model.defer="email_address" type="email" id="email_address" class="{{ $fieldClass('email_address') }}"
                    placeholder="e.g., juan.delacruz@gmail.com">
                @error('email_address')
                    <span data-error-for="email_address" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div class="md:col-span-2 xl:col-span-3">
                <label for="referral" class="{{ $labelClass }}">Whom may we thank for referring you?</label>
                <input wire:model.defer="referral" type="text" id="referral" class="{{ $fieldClass('referral') }}"
                    placeholder="e.g., Dr. Santos / Maria Lim">
                @error('referral')
                    <span data-error-for="referral" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div class="rounded-sm border border-gray-200 bg-white p-5 md:p-6">
        <div class="mb-5 border-b border-gray-100 pb-4">
            <h2 class="text-base font-semibold text-[#1a2e3b]">Emergency Contact</h2>
            <p class="mt-1 text-xs text-gray-500">Person to contact in case of emergency.</p>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
            <div>
                <label for="emergency_contact_name" class="{{ $labelClass }}">Name <span class="text-red-600">*</span></label>
                <input wire:model.defer="emergency_contact_name" type="text" id="emergency_contact_name"
                    class="{{ $fieldClass('emergency_contact_name') }}" placeholder="e.g., Maria Dela Cruz">
                @error('emergency_contact_name')
                    <span data-error-for="emergency_contact_name" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="emergency_contact_number" class="{{ $labelClass }}">Contact Number <span class="text-red-600">*</span></label>
                <div class="flex min-w-0">
                    <span class="inline-flex shrink-0 items-center px-3 border border-r-0 border-gray-200 bg-gray-50 text-gray-500 text-sm font-semibold select-none">+63</span>
                    <input wire:model.defer="emergency_contact_number" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="10"
                        oninput="this.value = this.value.replace(/\D/g, '').replace(/^0+/, '').slice(0, 10)" id="emergency_contact_number"
                        class="{{ $fieldClass('emergency_contact_number') }} min-w-0 flex-1" placeholder="9187654321">
                </div>
                @error('emergency_contact_number')
                    <span data-error-for="emergency_contact_number"
                        class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="relationship" class="{{ $labelClass }}">Relationship to Patient <span class="text-red-600">*</span></label>
                <input wire:model.defer="relationship" type="text" id="relationship" class="{{ $fieldClass('relationship') }}"
                    placeholder="e.g., Spouse">
                @error('relationship')
                    <span data-error-for="relationship" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    @if ($this->age !== null && $this->age < 18)
        <div class="rounded-sm border border-gray-200 bg-white p-5 md:p-6">
            <div class="mb-5 border-b border-gray-100 pb-4">
                <h2 class="text-base font-semibold text-[#1a2e3b]">For Patients Below 18</h2>
                <p class="mt-1 text-xs text-gray-500">Please complete guardian and parent details below.</p>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label for="who_answering" class="{{ $labelClass }}">Who is answering this form? <span class="text-red-600">*</span></label>
                    <input wire:model.defer="who_answering" type="text" id="who_answering" class="{{ $fieldClass('who_answering') }}"
                        placeholder="e.g., Maria Dela Cruz">
                    @error('who_answering')
                        <span data-error-for="who_answering" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="relationship_to_patient" class="{{ $labelClass }}">Relationship to Patient <span class="text-red-600">*</span></label>
                    <input wire:model.defer="relationship_to_patient" type="text" id="relationship_to_patient"
                        class="{{ $fieldClass('relationship_to_patient') }}" placeholder="e.g., Mother">
                    @error('relationship_to_patient')
                        <span data-error-for="relationship_to_patient"
                            class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="father_name" class="{{ $labelClass }}">Father's Name</label>
                    <input wire:model.defer="father_name" type="text" id="father_name" class="{{ $fieldClass('father_name') }}"
                        placeholder="e.g., Pedro Dela Cruz">
                    @error('father_name')
                        <span data-error-for="father_name" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="father_number" class="{{ $labelClass }}">Father's Contact Number</label>
                    <div class="flex min-w-0">
                        <span class="inline-flex shrink-0 items-center px-3 border border-r-0 border-gray-200 bg-gray-50 text-gray-500 text-sm font-semibold select-none">+63</span>
                        <input wire:model.defer="father_number" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="10"
                            oninput="this.value = this.value.replace(/\D/g, '').replace(/^0+/, '').slice(0, 10)" id="father_number" class="{{ $fieldClass('father_number') }} min-w-0 flex-1"
                            placeholder="9151112222">
                    </div>
                    @error('father_number')
                        <span data-error-for="father_number" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="mother_name" class="{{ $labelClass }}">Mother's Name</label>
                    <input wire:model.defer="mother_name" type="text" id="mother_name" class="{{ $fieldClass('mother_name') }}"
                        placeholder="e.g., Maria Dela Cruz">
                    @error('mother_name')
                        <span data-error-for="mother_name" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="mother_number" class="{{ $labelClass }}">Mother's Contact Number</label>
                    <div class="flex min-w-0">
                        <span class="inline-flex shrink-0 items-center px-3 border border-r-0 border-gray-200 bg-gray-50 text-gray-500 text-sm font-semibold select-none">+63</span>
                        <input wire:model.defer="mother_number" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="10"
                            oninput="this.value = this.value.replace(/\D/g, '').replace(/^0+/, '').slice(0, 10)" id="mother_number" class="{{ $fieldClass('mother_number') }} min-w-0 flex-1"
                            placeholder="9163334444">
                    </div>
                    @error('mother_number')
                        <span data-error-for="mother_number" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="guardian_name" class="{{ $labelClass }}">Guardian's Name</label>
                    <input wire:model.defer="guardian_name" type="text" id="guardian_name" class="{{ $fieldClass('guardian_name') }}"
                        placeholder="e.g., Jose Santos">
                    @error('guardian_name')
                        <span data-error-for="guardian_name" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="guardian_number" class="{{ $labelClass }}">Guardian's Contact Number</label>
                    <div class="flex min-w-0">
                        <span class="inline-flex shrink-0 items-center px-3 border border-r-0 border-gray-200 bg-gray-50 text-gray-500 text-sm font-semibold select-none">+63</span>
                        <input wire:model.defer="guardian_number" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="10"
                            oninput="this.value = this.value.replace(/\D/g, '').replace(/^0+/, '').slice(0, 10)" id="guardian_number" class="{{ $fieldClass('guardian_number') }} min-w-0 flex-1"
                            placeholder="9175556666">
                    </div>
                    @error('guardian_number')
                        <span data-error-for="guardian_number" class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
    @endif
</section>
