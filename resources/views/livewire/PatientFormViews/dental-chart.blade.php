<div class="flex flex-col items-center w-full">
    
    <div class="w-full overflow-x-auto pb-6">
        <!-- Min-width ensures chart doesn't crumble on small screens -->
        <div class="min-w-[1000px] flex flex-col items-center gap-8">
            
            <!-- UPPER ARCH (Teeth 18-28) -->
            <div class="flex flex-col items-center">
                <h3 class="text-gray-400 font-bold tracking-[0.2em] text-sm uppercase mb-4">Upper Arch</h3>
                
                {{-- CHANGED: items-start -> items-end (Aligns the Crowns/Bottoms) --}}
                <div class="flex items-end gap-1 p-4 border border-gray-200 rounded-xl bg-white shadow-sm">

                    <!-- Quadrant 1: 18 -> 11 -->
                    <div class="flex gap-1 border-r-2 border-gray-300 pr-3">
                        @foreach ([18, 17, 16, 15, 14, 13, 12, 11] as $tooth) 
                            @php $shape = in_array($tooth % 10, [1, 2, 3]) ? 'box' : 'circle'; @endphp
                            @include('livewire.PatientFormViews.partial.tooth', ['tooth' => $tooth, 'type' => $shape, 'isLower' => false])
                        @endforeach
                    </div>

                    <!-- Quadrant 2: 21 -> 28 -->
                    <div class="flex gap-1 pl-3">
                        @foreach ([21, 22, 23, 24, 25, 26, 27, 28] as $tooth)
                            @php $shape = in_array($tooth % 10, [1, 2, 3]) ? 'box' : 'circle'; @endphp
                            @include('livewire.PatientFormViews.partial.tooth', ['tooth' => $tooth, 'type' => $shape, 'isLower' => false])
                        @endforeach
                    </div>

                </div>
            </div>

            <!-- LOWER ARCH (Teeth 48-38) -->
            <div class="flex flex-col items-center">
                <h3 class="text-gray-400 font-bold tracking-[0.2em] text-sm uppercase mb-4">Lower Arch</h3>
                
                {{-- CHANGED: items-end -> items-start (Aligns the Crowns/Tops) --}}
                <div class="flex items-start gap-1 p-4 border border-gray-200 rounded-xl bg-white shadow-sm">
                    
                    <!-- Quadrant 4: 48 -> 41 -->
                    <div class="flex gap-1 border-r-2 border-gray-300 pr-3">
                        @foreach([48, 47, 46, 45, 44, 43, 42, 41] as $tooth)
                            @php $shape = in_array($tooth % 10, [1, 2, 3]) ? 'box' : 'circle'; @endphp
                            @include('livewire.PatientFormViews.partial.tooth', ['tooth' => $tooth, 'type' => $shape, 'isLower' => true])
                        @endforeach
                    </div>

                    <!-- Quadrant 3: 31 -> 38 -->
                    <div class="flex gap-1 pl-3">
                        @foreach([31, 32, 33, 34, 35, 36, 37, 38] as $tooth)
                            @php $shape = in_array($tooth % 10, [1, 2, 3]) ? 'box' : 'circle'; @endphp
                            @include('livewire.PatientFormViews.partial.tooth', ['tooth' => $tooth, 'type' => $shape, 'isLower' => true])
                        @endforeach
                    </div>

                </div>
            </div>
            
        </div> 
    </div>
</div>