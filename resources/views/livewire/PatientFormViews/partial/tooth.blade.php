<div class="flex flex-col items-center group w-16">
    
    @php
        $colors = [
            'red' => '#ef4444', 
            'blue' => '#3b82f6', 
            'green' => '#22c55e', 
            'white' => '#FFFFFF'
        ];
    @endphp

    @if(isset($isLower) && $isLower)
    
    {{-- TOP SIDE --}}
        <div class="relative w-14 h-14 z-10"> 
            <svg viewBox="0 0 100 100" class="w-full h-full drop-shadow-sm hover:scale-110 transition-transform duration-200 cursor-pointer">
                {{-- REVERTED TO ORIGINAL "PETAL" PATHS --}}
                <path wire:click="toggleSurface({{ $tooth }}, 'top')"
                      d="M 50 50 L 20 22 C 28 10, 42 12, 50 18 C 58 12, 72 10, 80 22 L 50 50 Z" 
                      fill="{{ $colors[$teeth[$tooth]['top'] ?? 'white'] }}" 
                      stroke="#7DC242" stroke-width="2.5" stroke-linejoin="round"
                      class="hover:opacity-75 transition-opacity" />
                <path wire:click="toggleSurface({{ $tooth }}, 'right')"
                      d="M 50 50 L 80 22 C 90 28, 88 42, 82 50 C 88 58, 90 72, 80 78 L 50 50 Z" 
                      fill="{{ $colors[$teeth[$tooth]['right'] ?? 'white'] }}" 
                      stroke="#7DC242" stroke-width="2.5" stroke-linejoin="round"
                      class="hover:opacity-75 transition-opacity" />
                <path wire:click="toggleSurface({{ $tooth }}, 'bottom')"
                      d="M 50 50 L 80 78 C 72 90, 58 88, 50 82 C 42 88, 28 90, 20 78 L 50 50 Z" 
                      fill="{{ $colors[$teeth[$tooth]['bottom'] ?? 'white'] }}" 
                      stroke="#7DC242" stroke-width="2.5" stroke-linejoin="round"
                      class="hover:opacity-75 transition-opacity" />
                <path wire:click="toggleSurface({{ $tooth }}, 'left')"
                      d="M 50 50 L 20 78 C 10 72, 12 58, 18 50 C 12 42, 10 28, 20 22 L 50 50 Z" 
                      fill="{{ $colors[$teeth[$tooth]['left'] ?? 'white'] }}" 
                      stroke="#7DC242" stroke-width="2.5" stroke-linejoin="round"
                      class="hover:opacity-75 transition-opacity" />
                
                {{-- Center Shape Logic (Circle vs Rect) --}}
                @if($type === 'circle')
                    <circle wire:click="toggleSurface({{ $tooth }}, 'center')"
                            cx="50" cy="50" r="14" 
                            fill="{{ $colors[$teeth[$tooth]['center'] ?? 'white'] }}" 
                            stroke="#7DC242" stroke-width="2.5"
                            class="hover:opacity-75 transition-opacity" />
                @else
                    <rect wire:click="toggleSurface({{ $tooth }}, 'center')"
                          x="37" y="37" width="26" height="26" 
                          fill="{{ $colors[$teeth[$tooth]['center'] ?? 'white'] }}" 
                          stroke="#7DC242" stroke-width="2.5" stroke-linejoin="round"
                          class="hover:opacity-75 transition-opacity" />
                @endif
            </svg>
        </div>

        {{-- 2. FRONT VIEW IMAGE (Root - Bottom) --}}
        <div class="h-28 w-full flex items-start justify-center -mt-1 z-0">
             <img src="{{ asset('images/teeth/'.$tooth.'.png') }}" 
                  alt="T{{ $tooth }}" 
                  class="w-10 h-auto object-contain opacity-80"
                  onerror="this.style.display='none'" />
        </div>

        {{-- 3. STATUS GRID --}}
        {{-- INCREASED MARGIN mt-4 -> mt-6 to prevent overlap --}}
        <div class="w-full border border-gray-400 bg-gray-50 mb-1 mt-6 shadow-sm">
            <div class="h-3 border-b border-gray-300 hover:bg-blue-100 cursor-pointer transition-colors" title="Status 1"></div>
            <div class="h-3 border-b border-gray-300 hover:bg-blue-100 cursor-pointer transition-colors" title="Status 2"></div>
            <div class="h-3 hover:bg-blue-100 cursor-pointer transition-colors" title="Status 3"></div>
        </div>

        {{-- 4. TOOTH NUMBER --}}
        <span class="text-sm font-bold text-gray-500 mt-1 select-none group-hover:text-blue-600 transition-colors">
            {{ $tooth }}
        </span>

    @else
        {{-- ========================================== --}}
        {{-- UPPER ARCH: Number -> Grid -> Image -> Surface --}}
        {{-- ========================================== --}}

        {{-- 1. TOOTH NUMBER --}}
        <span class="text-sm font-bold text-gray-500 mb-1 select-none group-hover:text-blue-600 transition-colors">
            {{ $tooth }}
        </span>

        {{-- 2. STATUS GRID --}}
        {{-- INCREASED MARGIN mb-4 -> mb-6 to prevent overlap --}}
        <div class="w-full border border-gray-400 bg-gray-50 mb-6 shadow-sm">
            <div class="h-3 border-b border-gray-300 hover:bg-blue-100 cursor-pointer transition-colors" title="Status 1"></div>
            <div class="h-3 border-b border-gray-300 hover:bg-blue-100 cursor-pointer transition-colors" title="Status 2"></div>
            <div class="h-3 hover:bg-blue-100 cursor-pointer transition-colors" title="Status 3"></div>
        </div>
        
        {{-- 3. FRONT VIEW IMAGE (Root - Top) --}}
        {{-- INCREASED HEIGHT h-20 -> h-28 to accommodate tall roots --}}
        <div class="h-28 w-full flex items-end justify-center -mb-1 z-0">
             <img src="{{ asset('images/teeth/'.$tooth.'.png') }}" 
                  alt="T{{ $tooth }}" 
                  class="w-10 h-auto object-contain opacity-80" 
                  onerror="this.style.display='none'" />
        </div>

        {{-- 4. SURFACE (Crown - Bottom) --}}
        <div class="relative w-14 h-14 z-10">
            <svg viewBox="0 0 100 100" class="w-full h-full drop-shadow-sm hover:scale-110 transition-transform duration-200 cursor-pointer">
                {{-- REVERTED TO ORIGINAL "PETAL" PATHS --}}
                <path wire:click="toggleSurface({{ $tooth }}, 'top')"
                      d="M 50 50 L 20 22 C 28 10, 42 12, 50 18 C 58 12, 72 10, 80 22 L 50 50 Z" 
                      fill="{{ $colors[$teeth[$tooth]['top'] ?? 'white'] }}" 
                      stroke="#7DC242" stroke-width="2.5" stroke-linejoin="round"
                      class="hover:opacity-75 transition-opacity" />
                <path wire:click="toggleSurface({{ $tooth }}, 'right')"
                      d="M 50 50 L 80 22 C 90 28, 88 42, 82 50 C 88 58, 90 72, 80 78 L 50 50 Z" 
                      fill="{{ $colors[$teeth[$tooth]['right'] ?? 'white'] }}" 
                      stroke="#7DC242" stroke-width="2.5" stroke-linejoin="round"
                      class="hover:opacity-75 transition-opacity" />
                <path wire:click="toggleSurface({{ $tooth }}, 'bottom')"
                      d="M 50 50 L 80 78 C 72 90, 58 88, 50 82 C 42 88, 28 90, 20 78 L 50 50 Z" 
                      fill="{{ $colors[$teeth[$tooth]['bottom'] ?? 'white'] }}" 
                      stroke="#7DC242" stroke-width="2.5" stroke-linejoin="round"
                      class="hover:opacity-75 transition-opacity" />
                <path wire:click="toggleSurface({{ $tooth }}, 'left')"
                      d="M 50 50 L 20 78 C 10 72, 12 58, 18 50 C 12 42, 10 28, 20 22 L 50 50 Z" 
                      fill="{{ $colors[$teeth[$tooth]['left'] ?? 'white'] }}" 
                      stroke="#7DC242" stroke-width="2.5" stroke-linejoin="round"
                      class="hover:opacity-75 transition-opacity" />
                
                {{-- Center Shape Logic (Circle vs Rect) --}}
                @if($type === 'circle')
                    <circle wire:click="toggleSurface({{ $tooth }}, 'center')"
                            cx="50" cy="50" r="14" 
                            fill="{{ $colors[$teeth[$tooth]['center'] ?? 'white'] }}" 
                            stroke="#7DC242" stroke-width="2.5"
                            class="hover:opacity-75 transition-opacity" />
                @else
                    <rect wire:click="toggleSurface({{ $tooth }}, 'center')"
                          x="37" y="37" width="26" height="26" 
                          fill="{{ $colors[$teeth[$tooth]['center'] ?? 'white'] }}" 
                          stroke="#7DC242" stroke-width="2.5" stroke-linejoin="round"
                          class="hover:opacity-75 transition-opacity" />
                @endif
            </svg>
        </div>
    @endif
</div>