<div wire:key="tooth-{{ $tooth }}" class="flex flex-col items-center group w-16">
    
    @php
        $colors = [
            'red' => '#ef4444', 
            'blue' => '#3b82f6', 
            'green' => '#22c55e', 
            'white' => '#FFFFFF'
        ];
        
        $data = $teeth[$tooth] ?? [];
        
        $topColor    = $data['top']['color']    ?? 'white';
        $rightColor  = $data['right']['color']  ?? 'white';
        $bottomColor = $data['bottom']['color'] ?? 'white';
        $leftColor   = $data['left']['color']   ?? 'white';
        $centerColor = $data['center']['color'] ?? 'white';

        $toolLabels = $toolLabels ?? [];
        
        $getTooltip = function($partKey) use ($data, $toolLabels) {
            $code = $data[$partKey]['code'] ?? null;
            if (!$code) return '';
            $label = $toolLabels[$code] ?? '';
            return $label ? "$code - $label" : $code;
        };

        $s1 = $data['line_1'] ?? null;
        $s2 = $data['line_2'] ?? null;
        $s3 = $data['line_3'] ?? null;
        
        $getLineTooltip = function($status) use ($toolLabels) {
            if (!$status) return '';
            $code = $status['code'];
            $label = $toolLabels[$code] ?? '';
            return $label ? "$code - $label" : $code;
        };
    @endphp

    @if(isset($isLower) && $isLower)
        {{-- LOWER ARCH --}}
        {{-- SURFACE --}}
        <div class="relative w-14 h-14 z-10"> 
            <svg viewBox="0 0 100 100" class="w-full h-full drop-shadow-sm hover:scale-110 transition-transform duration-200 ">
                <path wire:click="updateSurface({{ $tooth }}, 'top')" d="M 50 50 L 20 22 C 28 10, 42 12, 50 18 C 58 12, 72 10, 80 22 L 50 50 Z" fill="{{ $colors[$topColor] }}" stroke="#7DC242" stroke-width="2.5" stroke-linejoin="round" class="hover:opacity-75 transition-opacity"><title>{{ $getTooltip('top') }}</title></path>
                <path wire:click="updateSurface({{ $tooth }}, 'right')" d="M 50 50 L 80 22 C 90 28, 88 42, 82 50 C 88 58, 90 72, 80 78 L 50 50 Z" fill="{{ $colors[$rightColor] }}" stroke="#7DC242" stroke-width="2.5" stroke-linejoin="round" class="hover:opacity-75 transition-opacity"><title>{{ $getTooltip('right') }}</title></path>
                <path wire:click="updateSurface({{ $tooth }}, 'bottom')" d="M 50 50 L 80 78 C 72 90, 58 88, 50 82 C 42 88, 28 90, 20 78 L 50 50 Z" fill="{{ $colors[$bottomColor] }}" stroke="#7DC242" stroke-width="2.5" stroke-linejoin="round" class="hover:opacity-75 transition-opacity"><title>{{ $getTooltip('bottom') }}</title></path>
                <path wire:click="updateSurface({{ $tooth }}, 'left')" d="M 50 50 L 20 78 C 10 72, 12 58, 18 50 C 12 42, 10 28, 20 22 L 50 50 Z" fill="{{ $colors[$leftColor] }}" stroke="#7DC242" stroke-width="2.5" stroke-linejoin="round" class="hover:opacity-75 transition-opacity"><title>{{ $getTooltip('left') }}</title></path>
                @if($type === 'circle')
                    <circle wire:click="updateSurface({{ $tooth }}, 'center')" cx="50" cy="50" r="14" fill="{{ $colors[$centerColor] }}" stroke="#7DC242" stroke-width="2.5" class="hover:opacity-75 transition-opacity"><title>{{ $getTooltip('center') }}</title></circle>
                @else
                    <rect wire:click="updateSurface({{ $tooth }}, 'center')" x="37" y="37" width="26" height="26" fill="{{ $colors[$centerColor] }}" stroke="#7DC242" stroke-width="2.5" stroke-linejoin="round" class="hover:opacity-75 transition-opacity"><title>{{ $getTooltip('center') }}</title></rect>
                @endif
            </svg>
        </div>

        {{-- ROOT IMAGE --}}
        <div class="h-18 w-full flex items-start justify-center -mt-1 z-0">
             <img src="{{ asset('images/teeth/'.$tooth.'.png') }}" alt="T{{ $tooth }}" class="w-10 h-auto object-contain opacity-80" onerror="this.style.display='none'" />
        </div>

        {{-- STATUS GRID (READ ONLY) --}}
        <div class="w-full border border-gray-400 bg-gray-50 mb-1 mt-6 shadow-sm flex flex-col text-[10px] font-bold text-center leading-none">
            {{-- [FIXED] Removed wire:click --}}
            <div title="{{ $getLineTooltip($s1) }}" class="h-6 border-b border-gray-300 flex items-center justify-center select-none {{ $s1 ? ($s1['color'] == 'red' ? 'text-red-600' : 'text-blue-600') : 'text-transparent' }}">
                {{ $s1['code'] ?? '-' }}
            </div>
            <div title="{{ $getLineTooltip($s2) }}" class="h-6 border-b border-gray-300 flex items-center justify-center select-none {{ $s2 ? ($s2['color'] == 'red' ? 'text-red-600' : 'text-blue-600') : 'text-transparent' }}">
                {{ $s2['code'] ?? '-' }}
            </div>
            <div title="{{ $getLineTooltip($s3) }}" class="h-6 flex items-center justify-center select-none {{ $s3 ? ($s3['color'] == 'red' ? 'text-red-600' : 'text-blue-600') : 'text-transparent' }}">
                {{ $s3['code'] ?? '-' }}
            </div>
        </div>

        <span class="text-sm font-bold text-gray-500 mt-1 select-none">{{ $tooth }}</span>

    @else
        {{-- UPPER ARCH --}}
        <span class="text-sm font-bold text-gray-500 mb-1 select-none">{{ $tooth }}</span>

        {{-- STATUS GRID (READ ONLY) --}}
        <div class="w-full border border-gray-400 bg-gray-50 mb-6 shadow-sm flex flex-col text-[10px] font-bold text-center leading-none">
            {{-- [FIXED] Removed wire:click --}}
            <div title="{{ $getLineTooltip($s1) }}" class="h-6 border-b border-gray-300 flex items-center justify-center select-none {{ $s1 ? ($s1['color'] == 'red' ? 'text-red-600' : 'text-blue-600') : 'text-transparent' }}">
                {{ $s1['code'] ?? '-' }}
            </div>
            <div title="{{ $getLineTooltip($s2) }}" class="h-6 border-b border-gray-300 flex items-center justify-center select-none {{ $s2 ? ($s2['color'] == 'red' ? 'text-red-600' : 'text-blue-600') : 'text-transparent' }}">
                {{ $s2['code'] ?? '-' }}
            </div>
            <div title="{{ $getLineTooltip($s3) }}" class="h-6 flex items-center justify-center select-none {{ $s3 ? ($s3['color'] == 'red' ? 'text-red-600' : 'text-blue-600') : 'text-transparent' }}">
                {{ $s3['code'] ?? '-' }}
            </div>
        </div>
        
        <div class="h-18 w-full flex items-end justify-center -mb-1 z-0">
             <img src="{{ asset('images/teeth/'.$tooth.'.png') }}" alt="T{{ $tooth }}" class="w-10 h-auto object-contain opacity-80" onerror="this.style.display='none'" />
        </div>

        <div class="relative w-14 h-14 z-10">
            <svg viewBox="0 0 100 100" class="w-full h-full drop-shadow-sm hover:scale-110 transition-transform duration-200">
                <path wire:click="updateSurface({{ $tooth }}, 'top')" d="M 50 50 L 20 22 C 28 10, 42 12, 50 18 C 58 12, 72 10, 80 22 L 50 50 Z" fill="{{ $colors[$topColor] }}" stroke="#7DC242" stroke-width="2.5" stroke-linejoin="round" class="hover:opacity-75 transition-opacity"><title>{{ $getTooltip('top') }}</title></path>
                <path wire:click="updateSurface({{ $tooth }}, 'right')" d="M 50 50 L 80 22 C 90 28, 88 42, 82 50 C 88 58, 90 72, 80 78 L 50 50 Z" fill="{{ $colors[$rightColor] }}" stroke="#7DC242" stroke-width="2.5" stroke-linejoin="round" class="hover:opacity-75 transition-opacity"><title>{{ $getTooltip('right') }}</title></path>
                <path wire:click="updateSurface({{ $tooth }}, 'bottom')" d="M 50 50 L 80 78 C 72 90, 58 88, 50 82 C 42 88, 28 90, 20 78 L 50 50 Z" fill="{{ $colors[$bottomColor] }}" stroke="#7DC242" stroke-width="2.5" stroke-linejoin="round" class="hover:opacity-75 transition-opacity"><title>{{ $getTooltip('bottom') }}</title></path>
                <path wire:click="updateSurface({{ $tooth }}, 'left')" d="M 50 50 L 20 78 C 10 72, 12 58, 18 50 C 12 42, 10 28, 20 22 L 50 50 Z" fill="{{ $colors[$leftColor] }}" stroke="#7DC242" stroke-width="2.5" stroke-linejoin="round" class="hover:opacity-75 transition-opacity"><title>{{ $getTooltip('left') }}</title></path>
                @if($type === 'circle')
                    <circle wire:click="updateSurface({{ $tooth }}, 'center')" cx="50" cy="50" r="14" fill="{{ $colors[$centerColor] }}" stroke="#7DC242" stroke-width="2.5" class="hover:opacity-75 transition-opacity"><title>{{ $getTooltip('center') }}</title></circle>
                @else
                    <rect wire:click="updateSurface({{ $tooth }}, 'center')" x="37" y="37" width="26" height="26" fill="{{ $colors[$centerColor] }}" stroke="#7DC242" stroke-width="2.5" stroke-linejoin="round" class="hover:opacity-75 transition-opacity"><title>{{ $getTooltip('center') }}</title></rect>
                @endif
            </svg>
        </div>
    @endif
</div>