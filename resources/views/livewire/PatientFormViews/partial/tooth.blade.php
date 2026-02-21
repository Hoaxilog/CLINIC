@php
    $colors = [
        'red' => '#ef4444',
        'blue' => '#3b82f6',
        'green' => '#22c55e',
        'white' => '#FFFFFF',
    ];

    $data = $teeth[$tooth] ?? [];

    $topColor = $data['top']['color'] ?? 'white';
    $rightColor = $data['right']['color'] ?? 'white';
    $bottomColor = $data['bottom']['color'] ?? 'white';
    $leftColor = $data['left']['color'] ?? 'white';
    $centerColor = $data['center']['color'] ?? 'white';

    $toolLabels = $toolLabels ?? [];

    $getTooltip = function ($partKey) use ($data, $toolLabels) {
        $code = $data[$partKey]['code'] ?? null;
        if (!$code) {
            return '';
        }
        $label = $toolLabels[$code] ?? '';
        return $label ? "$code - $label" : $code;
    };
 
    $s1 = $data['line_1'] ?? null;
    $s2 = $data['line_2'] ?? null;
    $s3 = $data['line_3'] ?? null;

    $getLineTooltip = function ($status) use ($toolLabels) {
        if (!$status) {
            return '';
        }
        $code = $status['code'];
        $label = $toolLabels[$code] ?? '';
        return $label ? "$code - $label" : $code;
    };

    $picker = $picker ?? ['open' => false, 'tooth' => null, 'part' => null, 'expanded' => false];
    $quickTools = $quickTools ?? [];
    $tools = $tools ?? [];
    $quickCodes = array_map(fn($tool) => $tool['code'], $quickTools);
    $outerTools = array_values(
        array_filter($tools, function ($tool) use ($quickCodes) {
            return !in_array($tool['code'], $quickCodes, true);
        }),
    );
    $hasMoreTools = count($outerTools) > 0;
@endphp

<div wire:key="tooth-{{ $tooth }}" x-data="{ tooth: {{ $tooth }}, open: false, part: null, expanded: false }"
    x-on:picker-open.window="if ($event.detail.tooth !== tooth) { open = false; expanded = false; }"
    x-on:keydown.escape.window="open = false; expanded = false" x-bind:class="open ? 'z-[1000]' : 'z-10'"
    class="flex flex-col items-center group w-20 sm:w-16 relative overflow-visible">
    @if (isset($isLower) && $isLower)
        {{-- LOWER ARCH --}}
        {{-- SURFACE --}}
        <div class="relative w-18 h-18 sm:w-14 sm:h-14 z-10 overflow-visible">
            <svg viewBox="0 0 100 100"
                class="w-full h-full drop-shadow-sm hover:scale-110 transition-transform duration-200 z-0 relative">
                <path x-on:click.stop="open = true; part = 'top'; expanded = false; $dispatch('picker-open', { tooth })"
                    d="M 50 50 L 20 22 C 28 10, 42 12, 50 18 C 58 12, 72 10, 80 22 L 50 50 Z"
                    x-bind:fill="$store.dentalChart.getPartFill(tooth, 'top')" stroke="#7DC242" stroke-width="2.5"
                    stroke-linejoin="round" class="hover:opacity-75 transition-opacity">
                    <title x-text="$store.dentalChart.getPartTooltip(tooth, 'top')">{{ $getTooltip('top') }}</title>
                </path>
                <path x-on:click.stop="open = true; part = 'right'; expanded = false; $dispatch('picker-open', { tooth })"
                    d="M 50 50 L 80 22 C 90 28, 88 42, 82 50 C 88 58, 90 72, 80 78 L 50 50 Z"
                    x-bind:fill="$store.dentalChart.getPartFill(tooth, 'right')" stroke="#7DC242" stroke-width="2.5"
                    stroke-linejoin="round" class="hover:opacity-75 transition-opacity">
                    <title x-text="$store.dentalChart.getPartTooltip(tooth, 'right')">{{ $getTooltip('right') }}</title>
                </path>
                <path x-on:click.stop="open = true; part = 'bottom'; expanded = false; $dispatch('picker-open', { tooth })"
                    d="M 50 50 L 80 78 C 72 90, 58 88, 50 82 C 42 88, 28 90, 20 78 L 50 50 Z"
                    x-bind:fill="$store.dentalChart.getPartFill(tooth, 'bottom')" stroke="#7DC242" stroke-width="2.5"
                    stroke-linejoin="round" class="hover:opacity-75 transition-opacity">
                    <title x-text="$store.dentalChart.getPartTooltip(tooth, 'bottom')">{{ $getTooltip('bottom') }}</title>
                </path>
                <path x-on:click.stop="open = true; part = 'left'; expanded = false; $dispatch('picker-open', { tooth })"
                    d="M 50 50 L 20 78 C 10 72, 12 58, 18 50 C 12 42, 10 28, 20 22 L 50 50 Z"
                    x-bind:fill="$store.dentalChart.getPartFill(tooth, 'left')" stroke="#7DC242" stroke-width="2.5"
                    stroke-linejoin="round" class="hover:opacity-75 transition-opacity">
                    <title x-text="$store.dentalChart.getPartTooltip(tooth, 'left')">{{ $getTooltip('left') }}</title>
                </path>
                @if ($type === 'circle')
                    <circle x-on:click.stop="open = true; part = 'center'; expanded = false; $dispatch('picker-open', { tooth })"
                        cx="50" cy="50" r="14" x-bind:fill="$store.dentalChart.getPartFill(tooth, 'center')"
                        stroke="#7DC242" stroke-width="2.5" class="hover:opacity-75 transition-opacity">
                        <title x-text="$store.dentalChart.getPartTooltip(tooth, 'center')">{{ $getTooltip('center') }}</title>
                    </circle>
                @else
                    <rect x-on:click.stop="open = true; part = 'center'; expanded = false; $dispatch('picker-open', { tooth })"
                        x="37" y="37" width="26" height="26" x-bind:fill="$store.dentalChart.getPartFill(tooth, 'center')"
                        stroke="#7DC242" stroke-width="2.5" stroke-linejoin="round"
                        class="hover:opacity-75 transition-opacity">
                        <title x-text="$store.dentalChart.getPartTooltip(tooth, 'center')">{{ $getTooltip('center') }}</title>
                    </rect>
                @endif
            </svg>

            @if (count($quickTools) > 0)
                <div class="absolute left-1/2 top-1/2 z-[100] -translate-x-1/2 -translate-y-1/2 pointer-events-auto"
                    x-cloak x-show="open" x-transition.opacity x-on:click.away="open = false; expanded = false">
                    @php
                        $innerItems = $quickTools;
                        $innerCount = count($innerItems) + ($hasMoreTools ? 1 : 0);
                        $outerCount = count($outerTools);

                        $cx = 175;
                        $cy = 175;
                        $innerOuterR = 110;
                        $innerInnerR = 60;
                        $outerOuterR = 170;
                        $outerInnerR = 120;

                        $polar = function ($radius, $angleDeg) use ($cx, $cy) {
                            $rad = deg2rad($angleDeg);
                            return [$cx + $radius * cos($rad), $cy + $radius * sin($rad)];
                        };
                        $arc = function ($rOuter, $rInner, $start, $end) use ($polar) {
                            $largeArc = abs($end - $start) > 180 ? 1 : 0;
                            [$x1, $y1] = $polar($rOuter, $start);
                            [$x2, $y2] = $polar($rOuter, $end);
                            [$x3, $y3] = $polar($rInner, $end);
                            [$x4, $y4] = $polar($rInner, $start);
                            return "M {$x1} {$y1} A {$rOuter} {$rOuter} 0 {$largeArc} 1 {$x2} {$y2} L {$x3} {$y3} A {$rInner} {$rInner} 0 {$largeArc} 0 {$x4} {$y4} Z";
                        };
                    @endphp
                    <svg viewBox="0 0 350 350" class="w-[18rem] h-[18rem]">
                        @foreach ($innerItems as $index => $tool)
                            @php
                                $start = $innerCount > 0 ? (360 * $index) / $innerCount - 90 : 0;
                                $end = $innerCount > 0 ? (360 * ($index + 1)) / $innerCount - 90 : 0;
                                $mid = ($start + $end) / 2;
                                [$tx, $ty] = $polar(($innerInnerR + $innerOuterR) / 2, $mid);
                                $isRed = $tool['color'] === 'red';
                                $fill = $isRed ? '#ef4444' : '#3b82f6';
                            @endphp
                            <path d="{{ $arc($innerOuterR, $innerInnerR, $start, $end) }}" fill="{{ $fill }}"
                                stroke="#e5e7eb" stroke-width="1.5" class="cursor-pointer hover:opacity-90"
                                x-on:click.stop="$store.dentalChart.applyTool('{{ $tool['code'] }}', tooth, part); open = false; expanded = false">
                                <title>{{ $tool['label'] }}</title>
                            </path>
                            <text x="{{ $tx }}" y="{{ $ty }}" text-anchor="middle"
                                dominant-baseline="middle"
                                class="fill-white text-[16px] font-bold select-none pointer-events-none">
                                {{ $tool['code'] }}
                            </text>
                        @endforeach
                        @if ($hasMoreTools)
                            @php
                                $moreIndex = $innerCount - 1;
                                $start = $innerCount > 0 ? (360 * $moreIndex) / $innerCount - 90 : 0;
                                $end = $innerCount > 0 ? (360 * ($moreIndex + 1)) / $innerCount - 90 : 0;
                                $mid = ($start + $end) / 2;
                                [$tx, $ty] = $polar(($innerInnerR + $innerOuterR) / 2, $mid);
                            @endphp
                            <path d="{{ $arc($innerOuterR, $innerInnerR, $start, $end) }}" fill="#374151"
                                stroke="#e5e7eb" stroke-width="1.5" class="cursor-pointer hover:opacity-90"
                                x-on:click.stop="expanded = !expanded">
                                <title x-text="expanded ? 'Hide more' : 'View more'">View more</title>
                            </path>
                            <text x="{{ $tx }}" y="{{ $ty }}" text-anchor="middle"
                                dominant-baseline="middle"
                                class="fill-white text-[16px] font-bold select-none pointer-events-none">
                                <tspan x-text="expanded ? 'Less' : 'More'">More</tspan>
                            </text>
                        @endif
                        @if ($outerCount > 0)
                            <g x-show="expanded">
                                @foreach ($outerTools as $index => $tool)
                                    @php
                                        $start = (360 * $index) / $outerCount - 90;
                                        $end = (360 * ($index + 1)) / $outerCount - 90;
                                        $mid = ($start + $end) / 2;
                                        [$tx, $ty] = $polar(($outerInnerR + $outerOuterR) / 2, $mid);
                                        $isRed = $tool['color'] === 'red';
                                        $fill = $isRed ? '#ef4444' : '#3b82f6';
                                    @endphp
                                    <path d="{{ $arc($outerOuterR, $outerInnerR, $start, $end) }}"
                                        fill="{{ $fill }}" stroke="#e5e7eb" stroke-width="1.5"
                                        class="cursor-pointer hover:opacity-90"
                                        x-on:click.stop="$store.dentalChart.applyTool('{{ $tool['code'] }}', tooth, part); open = false; expanded = false">
                                        <title>{{ $tool['label'] }}</title>
                                    </path>
                                    <text x="{{ $tx }}" y="{{ $ty }}" text-anchor="middle"
                                        dominant-baseline="middle"
                                        class="fill-white text-[14px] font-bold select-none pointer-events-none">
                                        {{ $tool['code'] }}
                                    </text>
                                @endforeach
                            </g>
                        @endif
                        <circle cx="{{ $cx }}" cy="{{ $cy }}" r="36" fill="#111827"
                            stroke="#e5e7eb" stroke-width="1.5" class="cursor-pointer hover:opacity-90"
                            x-on:click.stop="open = false; expanded = false" />
                        <text x="{{ $cx }}" y="{{ $cy }}" text-anchor="middle"
                            dominant-baseline="middle"
                            class="fill-white text-[18px] font-bold select-none pointer-events-none">
                            X
                        </text>
                    </svg>
                </div>
            @endif
        </div>

        {{-- ROOT IMAGE --}}
        <div class="h-20 sm:h-18 w-full flex items-start justify-center -mt-1 z-0">
            <img src="{{ asset('images/teeth/' . $tooth . '.png') }}" alt="T{{ $tooth }}"
                class="w-16 sm:w-10 h-auto object-contain opacity-80" onerror="this.style.display='none'" />
        </div>

        {{-- STATUS GRID (READ ONLY) --}}
        <div
            class="w-full border border-gray-400 bg-gray-50 mb-1 mt-6 max-md:mt-15 shadow-sm flex flex-col text-[12px] md:text-[10px] font-bold text-center leading-none">
            <div x-bind:title="$store.dentalChart.getStatusTooltip(tooth, 1)"
                class="h-7 md:h-6 border-b border-gray-300 flex items-center justify-center select-none"
                x-bind:class="$store.dentalChart.getStatusClass(tooth, 1)"
                x-text="$store.dentalChart.getStatusCode(tooth, 1)">{{ $s1['code'] ?? '-' }}</div>
            <div x-bind:title="$store.dentalChart.getStatusTooltip(tooth, 2)"
                class="h-7 md:h-6 border-b border-gray-300 flex items-center justify-center select-none"
                x-bind:class="$store.dentalChart.getStatusClass(tooth, 2)"
                x-text="$store.dentalChart.getStatusCode(tooth, 2)">{{ $s2['code'] ?? '-' }}</div>
            <div x-bind:title="$store.dentalChart.getStatusTooltip(tooth, 3)"
                class="h-7 md:h-6 flex items-center justify-center select-none"
                x-bind:class="$store.dentalChart.getStatusClass(tooth, 3)"
                x-text="$store.dentalChart.getStatusCode(tooth, 3)">{{ $s3['code'] ?? '-' }}</div>
        </div>

        <span class="text-sm font-bold text-gray-500 mt-1 select-none">{{ $tooth }}</span>
    @else
        {{-- UPPER ARCH --}}
        <span class="text-sm font-bold text-gray-500 mb-1 select-none">{{ $tooth }}</span>

        {{-- STATUS GRID (READ ONLY) --}}
        <div
            class="w-full border border-gray-400 bg-gray-50 mb-6 max-md:mb-15 shadow-sm flex flex-col text-[12px] md:text-[10px] font-bold text-center leading-none">
            <div x-bind:title="$store.dentalChart.getStatusTooltip(tooth, 1)"
                class="h-7 md:h-6 border-b border-gray-300 flex items-center justify-center select-none"
                x-bind:class="$store.dentalChart.getStatusClass(tooth, 1)"
                x-text="$store.dentalChart.getStatusCode(tooth, 1)">{{ $s1['code'] ?? '-' }}</div>
            <div x-bind:title="$store.dentalChart.getStatusTooltip(tooth, 2)"
                class="h-7 md:h-6 border-b border-gray-300 flex items-center justify-center select-none"
                x-bind:class="$store.dentalChart.getStatusClass(tooth, 2)"
                x-text="$store.dentalChart.getStatusCode(tooth, 2)">{{ $s2['code'] ?? '-' }}</div>
            <div x-bind:title="$store.dentalChart.getStatusTooltip(tooth, 3)"
                class="h-7 md:h-6 flex items-center justify-center select-none"
                x-bind:class="$store.dentalChart.getStatusClass(tooth, 3)"
                x-text="$store.dentalChart.getStatusCode(tooth, 3)">{{ $s3['code'] ?? '-' }}</div>
        </div>

        <div class="h-20 sm:h-18 w-full flex items-end justify-center -mb-1 z-0">
            <img src="{{ asset('images/teeth/' . $tooth . '.png') }}" alt="T{{ $tooth }}"
                class="w-16 sm:w-10 h-auto object-contain opacity-80" onerror="this.style.display='none'" />
        </div>

        <div class="relative w-18 h-18 sm:w-14 sm:h-14 z-10 overflow-visible">
            <svg viewBox="0 0 100 100"
                class="w-full h-full drop-shadow-sm hover:scale-110 transition-transform duration-200 relative z-0">
                <path x-on:click.stop="open = true; part = 'top'; expanded = false; $dispatch('picker-open', { tooth })"
                    d="M 50 50 L 20 22 C 28 10, 42 12, 50 18 C 58 12, 72 10, 80 22 L 50 50 Z"
                    x-bind:fill="$store.dentalChart.getPartFill(tooth, 'top')" stroke="#7DC242" stroke-width="2.5"
                    stroke-linejoin="round" class="hover:opacity-75 transition-opacity">
                    <title x-text="$store.dentalChart.getPartTooltip(tooth, 'top')">{{ $getTooltip('top') }}</title>
                </path>
                <path x-on:click.stop="open = true; part = 'right'; expanded = false; $dispatch('picker-open', { tooth })"
                    d="M 50 50 L 80 22 C 90 28, 88 42, 82 50 C 88 58, 90 72, 80 78 L 50 50 Z"
                    x-bind:fill="$store.dentalChart.getPartFill(tooth, 'right')" stroke="#7DC242" stroke-width="2.5"
                    stroke-linejoin="round" class="hover:opacity-75 transition-opacity">
                    <title x-text="$store.dentalChart.getPartTooltip(tooth, 'right')">{{ $getTooltip('right') }}</title>
                </path>
                <path x-on:click.stop="open = true; part = 'bottom'; expanded = false; $dispatch('picker-open', { tooth })"
                    d="M 50 50 L 80 78 C 72 90, 58 88, 50 82 C 42 88, 28 90, 20 78 L 50 50 Z"
                    x-bind:fill="$store.dentalChart.getPartFill(tooth, 'bottom')" stroke="#7DC242" stroke-width="2.5"
                    stroke-linejoin="round" class="hover:opacity-75 transition-opacity">
                    <title x-text="$store.dentalChart.getPartTooltip(tooth, 'bottom')">{{ $getTooltip('bottom') }}</title>
                </path>
                <path x-on:click.stop="open = true; part = 'left'; expanded = false; $dispatch('picker-open', { tooth })"
                    d="M 50 50 L 20 78 C 10 72, 12 58, 18 50 C 12 42, 10 28, 20 22 L 50 50 Z"
                    x-bind:fill="$store.dentalChart.getPartFill(tooth, 'left')" stroke="#7DC242" stroke-width="2.5"
                    stroke-linejoin="round" class="hover:opacity-75 transition-opacity">
                    <title x-text="$store.dentalChart.getPartTooltip(tooth, 'left')">{{ $getTooltip('left') }}</title>
                </path>
                @if ($type === 'circle')
                    <circle x-on:click.stop="open = true; part = 'center'; expanded = false; $dispatch('picker-open', { tooth })"
                        cx="50" cy="50" r="14" x-bind:fill="$store.dentalChart.getPartFill(tooth, 'center')"
                        stroke="#7DC242" stroke-width="2.5" class="hover:opacity-75 transition-opacity">
                        <title x-text="$store.dentalChart.getPartTooltip(tooth, 'center')">{{ $getTooltip('center') }}</title>
                    </circle>
                @else
                    <rect x-on:click.stop="open = true; part = 'center'; expanded = false; $dispatch('picker-open', { tooth })"
                        x="37" y="37" width="26" height="26" x-bind:fill="$store.dentalChart.getPartFill(tooth, 'center')"
                        stroke="#7DC242" stroke-width="2.5" stroke-linejoin="round"
                        class="hover:opacity-75 transition-opacity">
                        <title x-text="$store.dentalChart.getPartTooltip(tooth, 'center')">{{ $getTooltip('center') }}</title>
                    </rect>
                @endif
            </svg>

            @if (count($quickTools) > 0)
                <div class="absolute left-1/2 top-1/2 z-[100] -translate-x-1/2 -translate-y-1/2 pointer-events-auto"
                    x-cloak x-show="open" x-transition.opacity x-on:click.away="open = false; expanded = false">
                    @php
                        $innerItems = $quickTools;
                        $innerCount = count($innerItems) + ($hasMoreTools ? 1 : 0);
                        $outerCount = count($outerTools);

                        $cx = 175;
                        $cy = 175;
                        $innerOuterR = 110;
                        $innerInnerR = 60;
                        $outerOuterR = 170;
                        $outerInnerR = 120;

                        $polar = function ($radius, $angleDeg) use ($cx, $cy) {
                            $rad = deg2rad($angleDeg);
                            return [$cx + $radius * cos($rad), $cy + $radius * sin($rad)];
                        };
                        $arc = function ($rOuter, $rInner, $start, $end) use ($polar) {
                            $largeArc = abs($end - $start) > 180 ? 1 : 0;
                            [$x1, $y1] = $polar($rOuter, $start);
                            [$x2, $y2] = $polar($rOuter, $end);
                            [$x3, $y3] = $polar($rInner, $end);
                            [$x4, $y4] = $polar($rInner, $start);
                            return "M {$x1} {$y1} A {$rOuter} {$rOuter} 0 {$largeArc} 1 {$x2} {$y2} L {$x3} {$y3} A {$rInner} {$rInner} 0 {$largeArc} 0 {$x4} {$y4} Z";
                        };
                    @endphp
                    <svg viewBox="0 0 350 350" class="w-[18rem] h-[18rem]">
                        @foreach ($innerItems as $index => $tool)
                            @php
                                $start = $innerCount > 0 ? (360 * $index) / $innerCount - 90 : 0;
                                $end = $innerCount > 0 ? (360 * ($index + 1)) / $innerCount - 90 : 0;
                                $mid = ($start + $end) / 2;
                                [$tx, $ty] = $polar(($innerInnerR + $innerOuterR) / 2, $mid);
                                $isRed = $tool['color'] === 'red';
                                $fill = $isRed ? '#ef4444' : '#3b82f6';
                            @endphp
                            <path d="{{ $arc($innerOuterR, $innerInnerR, $start, $end) }}" fill="{{ $fill }}"
                                stroke="#e5e7eb" stroke-width="1.5" class="cursor-pointer hover:opacity-90"
                                x-on:click.stop="$store.dentalChart.applyTool('{{ $tool['code'] }}', tooth, part); open = false; expanded = false">
                                <title>{{ $tool['label'] }}</title>
                            </path>
                            <text x="{{ $tx }}" y="{{ $ty }}" text-anchor="middle"
                                dominant-baseline="middle"
                                class="fill-white text-[16px] font-bold select-none pointer-events-none">
                                {{ $tool['code'] }}
                            </text>
                        @endforeach
                        @if ($hasMoreTools)
                            @php
                                $moreIndex = $innerCount - 1;
                                $start = $innerCount > 0 ? (360 * $moreIndex) / $innerCount - 90 : 0;
                                $end = $innerCount > 0 ? (360 * ($moreIndex + 1)) / $innerCount - 90 : 0;
                                $mid = ($start + $end) / 2;
                                [$tx, $ty] = $polar(($innerInnerR + $innerOuterR) / 2, $mid);
                            @endphp
                            <path d="{{ $arc($innerOuterR, $innerInnerR, $start, $end) }}" fill="#374151"
                                stroke="#e5e7eb" stroke-width="1.5" class="cursor-pointer hover:opacity-90"
                                x-on:click.stop="expanded = !expanded">
                                <title x-text="expanded ? 'Hide more' : 'View more'">View more</title>
                            </path>
                            <text x="{{ $tx }}" y="{{ $ty }}" text-anchor="middle"
                                dominant-baseline="middle"
                                class="fill-white text-[16px] font-bold select-none pointer-events-none">
                                <tspan x-text="expanded ? 'Less' : 'More'">More</tspan>
                            </text>
                        @endif
                        @if ($outerCount > 0)
                            <g x-show="expanded">
                                @foreach ($outerTools as $index => $tool)
                                    @php
                                        $start = (360 * $index) / $outerCount - 90;
                                        $end = (360 * ($index + 1)) / $outerCount - 90;
                                        $mid = ($start + $end) / 2;
                                        [$tx, $ty] = $polar(($outerInnerR + $outerOuterR) / 2, $mid);
                                        $isRed = $tool['color'] === 'red';
                                        $fill = $isRed ? '#ef4444' : '#3b82f6';
                                    @endphp
                                    <path d="{{ $arc($outerOuterR, $outerInnerR, $start, $end) }}"
                                        fill="{{ $fill }}" stroke="#e5e7eb" stroke-width="1.5"
                                        class="cursor-pointer hover:opacity-90"
                                        x-on:click.stop="$store.dentalChart.applyTool('{{ $tool['code'] }}', tooth, part); open = false; expanded = false">
                                        <title>{{ $tool['label'] }}</title>
                                    </path>
                                    <text x="{{ $tx }}" y="{{ $ty }}" text-anchor="middle"
                                        dominant-baseline="middle"
                                        class="fill-white text-[14px] font-bold select-none pointer-events-none">
                                        {{ $tool['code'] }}
                                    </text>
                                @endforeach
                            </g>
                        @endif
                        <circle cx="{{ $cx }}" cy="{{ $cy }}" r="36" fill="#111827"
                            stroke="#e5e7eb" stroke-width="1.5" class="cursor-pointer hover:opacity-90"
                            x-on:click.stop="open = false; expanded = false" />
                        <text x="{{ $cx }}" y="{{ $cy }}" text-anchor="middle"
                            dominant-baseline="middle"
                            class="fill-white text-[18px] font-bold select-none pointer-events-none">
                            X
                        </text>
                    </svg>
                </div>
            @endif
        </div>
    @endif
</div>
