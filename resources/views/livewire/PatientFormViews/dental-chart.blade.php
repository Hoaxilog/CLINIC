<div class="relative w-full h-[68vh] bg-white border border-gray-200 rounded-lg overflow-hidden flex flex-col lg:flex-row">
    
    {{-- LOGIC: Show Chart Interface if History Exists OR We are Creating a New Chart --}}
    @if(count($history) > 0 || $isCreating)

        <input type="checkbox" id="sidebar-toggle" class="peer sr-only" checked>

        <label for="sidebar-toggle" 
               class="absolute top-20 right-10 z-50 p-2 bg-white shadow-md border border-gray-200 rounded-md text-gray-500 hover:text-blue-600 cursor-pointer peer-checked:hidden transition-opacity duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </label>

        <!-- LEFT PANEL: CHART & FORMS -->
        <div class="flex-1 h-full relative flex flex-col min-w-0 bg-gray-50 transition-all duration-300">
            
            <!-- HEADER -->
            <div class="flex items-center justify-between px-6 py-4 bg-gray-50 border-b border-gray-200 sticky top-0 z-20">
                
                <div class="flex items-center gap-4">
                    <h2 class="text-xl font-bold text-gray-800">Dental Chart</h2>
                    @if(count($history) > 0)
                        <div class="flex items-center gap-2">
                            <select 
                                wire:model.live="selectedHistoryId" 
                                class="px-3 py-2 border border-gray-300 rounded-md text-sm w-full focus:border-blue-500 focus:ring-blue-500 min-w-[200px] disabled:bg-gray-100 disabled:text-gray-400"
                                @if(!$isReadOnly) disabled @endif
                            >
                                <option value="" disabled>Select History Record...</option>
                                @foreach($history as $record)
                                    <option value="{{ $record['id'] }}">{{ $record['date'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
                
                <div class="flex items-center gap-3">
                    {{-- @if($selectedHistoryId)
                        <span class="ml-2 text-sm bg-yellow-100 text-yellow-800 px-3 py-1.5 rounded-full font-medium border border-yellow-200">Viewing History</span>
                    @else
                        @if(!$isReadOnly)
                            <span class="ml-2 text-sm bg-green-100 text-green-800 px-3 py-1.5 rounded-full font-medium border border-green-200">Editable</span>
                        @else
                             <span class="ml-2 text-sm bg-gray-100 text-gray-800 px-3 py-1.5 rounded-full font-medium border border-gray-200">Current View</span>
                        @endif
                    @endif --}}

                    <!-- Header New Chart Button (Only if history exists and we are viewing) -->
                    @if($isReadOnly && count($history) > 0)
                        <button 
                            wire:click="triggerNewChart" 
                            class="flex items-center gap-2 px-3 py-2 bg-[#0086da] text-white text-sm font-medium rounded hover:bg-blue-600 transition shadow-sm"
                            title="Start a fresh chart for today"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                            New Chart
                        </button>
                    @endif
                </div>
            </div>

            <!-- Main Scrollable Area -->
            <div class="flex-1 overflow-auto p-4 lg:p-10 scrollbar-thin scrollbar-thumb-rounded-full scrollbar-track-gray-100 scrollbar-thumb-gray-300">
                
                <!-- 1. DENTAL CHART (Teeth) -->
                <div class="min-w-[1000px] max-w-6xl flex flex-col items-center gap-12 mx-auto mb-16">
                    <!-- Upper Arch -->
                    <div class="flex flex-col items-center">
                        <h3 class="text-gray-400 font-bold tracking-[0.2em] text-sm uppercase mb-4">Upper Arch</h3>
                        <div class="flex items-end gap-1 p-4 border border-gray-200 rounded-xl bg-white shadow-sm">
                            <div class="flex gap-1 border-r-2 border-gray-300 pr-3">
                                @foreach ([18, 17, 16, 15, 14, 13, 12, 11] as $tooth) 
                                    @php $shape = in_array($tooth, [11, 12, 13]) ? 'box' : 'circle'; @endphp
                                    @include('livewire.PatientFormViews.partial.tooth', ['tooth' => $tooth, 'type' => $shape, 'isLower' => false, 'teeth' => $teeth, 'toolLabels' => $toolLabels])
                                @endforeach
                            </div>
                            <div class="flex gap-1 pl-3">
                                @foreach ([21, 22, 23, 24, 25, 26, 27, 28] as $tooth)
                                    @php $shape = in_array($tooth, [21, 22, 23]) ? 'box' : 'circle'; @endphp
                                    @include('livewire.PatientFormViews.partial.tooth', ['tooth' => $tooth, 'type' => $shape, 'isLower' => false, 'teeth' => $teeth, 'toolLabels' => $toolLabels])
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!-- Lower Arch -->
                    <div class="flex flex-col items-center">
                        <h3 class="text-gray-400 font-bold tracking-[0.2em] text-sm uppercase mb-4">Lower Arch</h3>
                        <div class="flex items-start gap-1 p-4 border border-gray-200 rounded-xl bg-white shadow-sm">
                            <div class="flex gap-1 border-r-2 border-gray-300 pr-3">
                                @foreach([48, 47, 46, 45, 44, 43, 42, 41] as $tooth)
                                    @php $shape = in_array($tooth, [41, 42, 43]) ? 'box' : 'circle'; @endphp
                                    @include('livewire.PatientFormViews.partial.tooth', ['tooth' => $tooth, 'type' => $shape, 'isLower' => true, 'teeth' => $teeth, 'toolLabels' => $toolLabels])
                                @endforeach
                            </div>
                            <div class="flex gap-1 pl-3">
                                @foreach([31, 32, 33, 34, 35, 36, 37, 38] as $tooth)
                                    @php $shape = in_array($tooth, [31, 32, 33]) ? 'box' : 'circle'; @endphp
                                    @include('livewire.PatientFormViews.partial.tooth', ['tooth' => $tooth, 'type' => $shape, 'isLower' => true, 'teeth' => $teeth, 'toolLabels' => $toolLabels])
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div> 

                <!-- 2. ORAL EXAM & COMMENTS -->
                <div class="max-w-6xl mx-auto flex flex-col gap-12">
                    <!-- Oral Exam Section -->
                    <section class="w-full">
                        <div class="bg-blue-100 border-l-4 border-blue-500 p-4 mb-6">
                            <h2 class="text-xl font-bold text-black">Oral Exam</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 bg-white p-8">
                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-2">Oral Hygiene Status</label>
                                <select wire:model="oralExam.oral_hygiene_status" @if($isReadOnly) disabled @endif class="w-full border rounded px-4 py-3 text-base bg-white focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-500">
                                    <option value="">Select...</option>
                                    <option value="Excellent">Excellent</option>
                                    <option value="Good">Good</option>
                                    <option value="Fair">Fair</option>
                                    <option value="Poor">Poor</option>
                                    <option value="Bad">Bad</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-2">Calcular Deposits</label>
                                <select wire:model="oralExam.calcular_deposits" @if($isReadOnly) disabled @endif class="w-full border rounded px-4 py-3 text-base bg-white focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-500">
                                    <option value="">Select...</option>
                                    <option value="None">None</option>
                                    <option value="Slight">Slight</option>
                                    <option value="Moderate">Moderate</option>
                                    <option value="Severe">Severe</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-2">Gingiva</label>
                                <select wire:model="oralExam.gingiva" @if($isReadOnly) disabled @endif class="w-full border rounded px-4 py-3 text-base bg-white focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-500">
                                    <option value="">Select...</option>
                                    <option value="Healthy">Healthy</option>
                                    <option value="Mildly Inflamed">Mildly Inflamed</option>
                                    <option value="Severe Inflamed">Severe Inflamed</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-2">Stains</label>
                                <select wire:model="oralExam.stains" @if($isReadOnly) disabled @endif class="w-full border rounded px-4 py-3 text-base bg-white focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-500">
                                    <option value="">Select...</option>
                                    <option value="None">None</option>
                                    <option value="Slight">Slight</option>
                                    <option value="Moderate">Moderate</option>
                                    <option value="Severe">Severe</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-2">Complete Denture</label>
                                <select wire:model="oralExam.complete_denture" @if($isReadOnly) disabled @endif class="w-full border rounded px-4 py-3 text-base bg-white focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-500">
                                    <option value="">Select...</option>
                                    <option value="None">None</option>
                                    <option value="Upper">Upper</option>
                                    <option value="Lower">Lower</option>
                                    <option value="Upper & Lower">Upper & Lower</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-2">Partial Denture</label>
                                <select wire:model="oralExam.partial_denture" @if($isReadOnly) disabled @endif class="w-full border rounded px-4 py-3 text-base bg-white focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-500">
                                    <option value="">Select...</option>
                                    <option value="None">None</option>
                                    <option value="Upper">Upper</option>
                                    <option value="Lower">Lower</option>
                                    <option value="Upper & Lower">Upper & Lower</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    <!-- Comments / Plan Section -->
                    <section class="w-full">
                        <div class="bg-blue-100 border-l-4 border-blue-500 p-4 mb-6">
                            <h2 class="text-xl font-bold text-black">Comments / Plan</h2>
                        </div>
                        <div class="space-y-8 bg-white p-8">
                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-2">Comments / Notes</label>
                                <textarea 
                                    wire:model="chartComments.notes" 
                                    rows="5" 
                                    class="w-full border rounded px-4 py-3 text-base focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-500"
                                    placeholder="Enter observation notes..."
                                    @if($isReadOnly) disabled @endif
                                ></textarea>
                            </div>
                            <div>
                                <label class="block text-lg font-medium text-gray-700 mb-2">Treatment Plan</label>
                                <textarea 
                                    wire:model="chartComments.treatment_plan" 
                                    rows="5" 
                                    class="w-full border rounded px-4 py-3 text-base focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-500"
                                    placeholder="Enter proposed treatment..."
                                    @if($isReadOnly) disabled @endif
                                ></textarea>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: SIDEBAR -->
        <div class="hidden peer-checked:flex w-full lg:w-72 bg-white border-l border-gray-200 flex-col z-30 shadow-2xl lg:shadow-none absolute lg:relative inset-y-0 right-0 h-full transition-all duration-300 transform">
            <div class="p-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between flex-shrink-0 h-16">
                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Legend & Tools</h3>
                <label for="sidebar-toggle" class="text-gray-400 hover:text-red-500 cursor-pointer p-1 transition-transform hover:scale-110">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </label>
            </div>
            <div class="flex-1 overflow-y-auto p-2 bg-white relative scrollbar-thin scrollbar-thumb-rounded-full scrollbar-track-gray-100 scrollbar-thumb-gray-300">
                <div class="space-y-1">
                    <button wire:click="selectTool(null)"
                            class="w-full flex items-center justify-between p-2.5 rounded-md border transition-all mb-4
                            {{ is_null($selectedTool) ? 'border-gray-800 bg-gray-800 text-white' : 'border-gray-200 hover:bg-gray-50 text-gray-600' }}">
                        <span class="font-bold text-xs">Cursor / No Tool</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path></svg>
                    </button>

                    @foreach($tools as $tool)
                        @php
                            $isSelected = $selectedTool === $tool['code'];
                            $isRed = $tool['color'] === 'red';
                            if ($isSelected) {
                                $containerClass = $isRed 
                                    ? 'bg-red-50 border-l-4 border-red-500 text-red-700 shadow-sm' 
                                    : 'bg-blue-50 border-l-4 border-blue-500 text-blue-700 shadow-sm';
                                $codeBadgeClass = $isRed ? 'bg-white text-red-600 border-red-200' : 'bg-white text-blue-600 border-blue-200';
                            } else {
                                $containerClass = 'border-l-4 border-transparent hover:bg-gray-50 text-gray-600';
                                $codeBadgeClass = $isRed ? 'bg-red-50 text-red-600 border-red-100' : 'bg-blue-50 text-blue-600 border-blue-100';
                            }
                        @endphp

                        <button wire:click="selectTool('{{ $tool['code'] }}')"
                                class="w-full flex items-center justify-between p-2 rounded-r-md transition-all duration-150 group {{ $containerClass }}">
                            <span class="text-xs font-semibold text-left leading-tight pr-2">
                                {{ $tool['label'] }}
                            </span>
                            <span class="font-bold font-mono text-[10px] min-w-[30px] text-center py-0.5 rounded border {{ $codeBadgeClass }}">
                                {{ $tool['code'] }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="p-3 bg-gray-50 border-t border-gray-200 text-center text-xs text-gray-500 flex-shrink-0">
                Select a condition
            </div>
        </div>
    
    @else
        <div class="w-full h-full flex flex-col items-center justify-center bg-gray-50 p-10 text-center space-y-6">
            <div class="bg-blue-50 p-6 rounded-full">
                <svg class="w-20 h-20 text-[#0086da]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-800">No Dental Chart History</h3>
                <p class="text-gray-500 mt-2 max-w-md mx-auto">This patient does not have any dental records yet. Click the button below to create the first chart.</p>
            </div>
            <button wire:click="triggerNewChart" class="flex items-center gap-2 px-6 py-3 bg-[#0086da] text-white text-lg font-bold rounded-lg shadow-lg hover:scale-105 transition-all transform">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                Add Dental Chart
            </button>
        </div>
    @endif
</div>