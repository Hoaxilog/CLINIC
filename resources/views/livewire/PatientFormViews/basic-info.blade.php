<section>
    <!-- Patient Information -->
    <div class="bg-blue-100 border-l-4 border-blue-500 p-4 mb-6">
        <h2 class="text-xl font-bold text-black">Patient Information</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        
        <!-- Last Name -->
        <div>
            <label for="last_name" class="block text-lg font-medium text-gray-700 mb-2">Last Name</label>
            <input wire:model="last_name" type="text" id="last_name" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., Dela Cruz">
            @error('last_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        
        <!-- First Name -->
        <div>
            <label for="first_name" class="block text-lg font-medium text-gray-700 mb-2">First Name</label>
            <input wire:model="first_name" type="text" id="first_name" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., Juan">
            @error('first_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Middle Name -->
        <div>
            <label for="middle_name" class="block text-lg font-medium text-gray-700 mb-2">Middle Name</label>
            <input wire:model="middle_name" type="text" id="middle_name" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., Reyes">
            @error('middle_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Nickname -->
        <div>
            <label for="nickname" class="block text-lg font-medium text-gray-700 mb-2">Nickname</label>
            <input wire:model="nickname" type="text" id="nickname" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., Juan">
            @error('nickname') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Occupation -->
        <div>
            <label for="occupation" class="block text-lg font-medium text-gray-700 mb-2">Occupation</label>
            <input wire:model="occupation" type="text" id="occupation" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., Engineer">
            @error('occupation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Date of Birth -->
        <div>
            <label for="birth_date" class="block text-lg font-medium text-gray-700 mb-2">Date of Birth</label>
            <input wire:model.live="birth_date" type="date" id="birth_date" class="w-full border rounded px-4 py-3 text-base">
            @error('birth_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        
        <!-- Age (Readonly) -->
        <div>
            <label class="block text-lg font-medium text-gray-700 mb-2">Age</label>
            <input wire:model="Age" type="text" value="{{ $this->age }}" readonly class="w-full border rounded px-4 py-3 text-base bg-gray-100" placeholder="Auto-calculated">
        </div>

        <!-- Sex (Select) -->
        <div>
            <label class="block text-lg font-medium text-gray-700 mb-2">Sex</label>
            <select wire:model="gender" class="w-full border rounded px-4 py-3 text-base bg-white">
                <option value="">Select...</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
            @error('gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

        </div>

        <!-- Civil Status -->
        <div>
            <label for="civil_status" class="block text-lg font-medium text-gray-700 mb-2">Civil Status</label>
            <input wire:model="civil_status" type="text" id="civil_status" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., Single">
            @error('civil_status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        
        <!-- Home Address -->
        <div class="col-span-1 md:col-span-2">
            <label for="home_address" class="block text-lg font-medium text-gray-700 mb-2">Home Address</label>
            <input wire:model="home_address" type="text" id="home_address" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., 123 Rizal St, Brgy. 1, Manila">
            @error('home_address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        
        <!-- Home Phone Number -->
        <div>
            <label for="home_number" class="block text-lg font-medium text-gray-700 mb-2">Home Phone Number</label>
            <input wire:model="home_number" type="text" id="home_number" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., (02) 8123 4567">
            @error('home_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        
        <!-- Office Address -->
        <div class="col-span-1 md:col-span-2">
            <label for="office_address" class="block text-lg font-medium text-gray-700 mb-2">Office Address</label>
            <input wire:model="office_address" type="text" id="office_address" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., 456 Ayala Ave, Makati">
            @error('office_address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        
        <!-- Office Phone Number -->
        <div>
            <label for="office_number" class="block text-lg font-medium text-gray-700 mb-2">Office Phone Number</label>
            <input wire:model="office_number" type="text" id="office_number" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., (02) 8888 8888">
            @error('office_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Mobile Number -->
        <div>
            <label for="mobile_number" class="block text-lg font-medium text-gray-700 mb-2">Mobile Number</label>
            <input wire:model="mobile_number" type="text" id="mobile_number" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., 0917 123 4567">
            @error('mobile_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- E-mail Address -->
        <div>
            <label for="email_address" class="block text-lg font-medium text-gray-700 mb-2">E-mail Address</label>
            <input wire:model="email_address" type="email" id="email_address" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., juan.delacruz@gmail.com">
            @error('email_address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Referral -->
        <div class="col-span-1 md:col-span-3">
            <label for="referral" class="block text-lg font-medium text-gray-700 mb-2">Whom may we thank for referring you?</label>
            <input wire:model="referral" type="text" id="referral" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., Dr. Santos / Maria Lim">
            @error('referral') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
    </div>

    <!-- Emergency Contact -->
    <div class="bg-blue-100 border-l-4 border-blue-500 p-4 mb-6">
        <h2 class="text-xl font-bold text-black">Person to Contact in Case of Emergency</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        
        <!-- Emergency Contact Name -->
        <div>
            <label for="emergency_contact_name" class="block text-lg font-medium text-gray-700 mb-2">Name</label>
            <input wire:model="emergency_contact_name" type="text" id="emergency_contact_name" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., Maria Dela Cruz">
            @error('emergency_contact_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Emergency Contact Number -->
        <div>
            <label for="emergency_contact_number" class="block text-lg font-medium text-gray-700 mb-2">Contact Number</label>
            <input wire:model="emergency_contact_number" type="text" id="emergency_contact_number" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., 0918 765 4321">
            @error('emergency_contact_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Relationship to Patient -->
        <div>
            <label for="relationship" class="block text-lg font-medium text-gray-700 mb-2">Relationship to Patient</label>
            <input wire:model="relationship" type="text" id="relationship" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., Spouse">
            @error('relationship') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

    </div>

    <!-- Below 18 -->
    @if($this->age < 18 && $this->age !== null)
        <div class="bg-blue-100 border-l-4 border-blue-500 p-4 mb-6">
            <h2 class="text-xl font-bold text-black">For Patient's Below 18 Years Old</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Who is Answering -->
            <div>
                <label for="who_answering" class="block text-lg font-medium text-gray-700 mb-2">Who is Answering this form on behalf of the patient?</label>
                <input wire:model="who_answering" type="text" id="who_answering" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., Maria Dela Cruz">
                @error('who_answering') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Relationship to Patient -->
            <div>
                <label for="relationship_to_patient" class="block text-lg font-medium text-gray-700 mb-2">Relationship to Patient</label>
                <input wire:model="relationship_to_patient" type="text" id="relationship_to_patient" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., Mother">
                @error('relationship_to_patient') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Father's Name -->
            <div>
                <label for="father_name" class="block text-lg font-medium text-gray-700 mb-2">Father's Name</label>
                <input wire:model="father_name" type="text" id="father_name" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., Pedro Dela Cruz">
                @error('father_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Father's Contact Number -->
            <div>
                <label for="father_number" class="block text-lg font-medium text-gray-700 mb-2">Father's Contact Number</label>
                <input wire:model="father_number" type="text" id="father_number" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., 0915 111 2222">
                @error('father_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Mother's Name -->
            <div>
                <label for="mother_name" class="block text-lg font-medium text-gray-700 mb-2">Mother's Name</label>
                <input wire:model="mother_name" type="text" id="mother_name" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., Maria Dela Cruz">
                @error('mother_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Mother's Contact Number -->
            <div>
                <label for="mother_number" class="block text-lg font-medium text-gray-700 mb-2">Mother's Contact Number</label>
                <input wire:model="mother_number" type="text" id="mother_number" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., 0916 333 4444">
                @error('mother_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Guardian's Name -->
            <div>
                <label for="guardian_name" class="block text-lg font-medium text-gray-700 mb-2">Guardian's Name</label>
                <input wire:model="guardian_name" type="text" id="guardian_name" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., Jose Santos">
                @error('guardian_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Guardian's Contact Number -->
            <div>
                <label for="guardian_number" class="block text-lg font-medium text-gray-700 mb-2">Guardian's Contact Number</label>
                <input wire:model="guardian_number" type="text" id="guardian_number" class="w-full border rounded px-4 py-3 text-base" placeholder="e.g., 0917 555 6666">
                @error('guardian_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>
    @endif
</section>