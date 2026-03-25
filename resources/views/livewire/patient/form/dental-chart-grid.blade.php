<div class="flex flex-col items-center justify-center gap-12 mx-auto mb-16"
    x-data
    x-on:request-dental-chart-teeth.window="
        $wire.provideTeeth($store.dentalChart.toPlain($store.dentalChart.localTeeth))
    "
    x-init="
        const existing = Alpine.store('dentalChart');
        const base = {
            localTeeth: @js($teeth) || {},
            tools: @js($tools),
            toolLabels: @js($toolLabels),
            isReadOnly: @js($isReadOnly),
            colors: { red: '#ef4444', blue: '#3b82f6', green: '#22c55e', white: '#FFFFFF' },
            ensureTeeth() {
                if (!this.localTeeth) this.localTeeth = {};
            },
            toPlain(value) {
                try {
                    return JSON.parse(JSON.stringify(value || {}));
                } catch (error) {
                    return value || {};
                }
            },
            getToolColor(code) {
                const tool = this.tools.find(t => t.code === code);
                return tool ? tool.color : null;
            },
            getPartFill(tooth, part) {
                const color = (this.localTeeth?.[tooth]?.[part]?.color) || 'white';
                return this.colors[color] || this.colors.white;
            },
            getPartTooltip(tooth, part) {
                const code = this.localTeeth?.[tooth]?.[part]?.code;
                if (!code) return '';
                const label = this.toolLabels?.[code] || '';
                return label ? `${code} - ${label}` : code;
            },
            hasChiefComplaint(tooth) {
                const wholeCode = this.localTeeth?.[tooth]?.whole_tooth?.code || null;
                if (wholeCode === 'CC') return true;
                const surfaces = ['top', 'bottom', 'left', 'right', 'center'];
                return surfaces.some((part) => (this.localTeeth?.[tooth]?.[part]?.code || null) === 'CC');
            },
            getStatusLine(tooth, index) {
                const key = `line_${index}`;
                return this.localTeeth?.[tooth]?.[key] || null;
            },
            getStatusCode(tooth, index) {
                return this.getStatusLine(tooth, index)?.code || '-';
            },
            getStatusClass(tooth, index) {
                const line = this.getStatusLine(tooth, index);
                if (!line) return 'text-transparent';
                return line.color === 'red' ? 'text-red-600' : 'text-blue-600';
            },
            getStatusTooltip(tooth, index) {
                const line = this.getStatusLine(tooth, index);
                if (!line) return '';
                const code = line.code;
                const label = this.toolLabels?.[code] || '';
                return label ? `${code} - ${label}` : code;
            },
            ensureStatusCode(tooth, code, color) {
                this.ensureTeeth();
                let firstEmptyKey = null;
                for (let i = 1; i <= 3; i++) {
                    const key = `line_${i}`;
                    const line = this.localTeeth?.[tooth]?.[key] || null;
                    if ((line?.code || null) === code) return;
                    if (!line && firstEmptyKey === null) firstEmptyKey = key;
                }
                if (firstEmptyKey) {
                    if (!this.localTeeth[tooth]) this.localTeeth[tooth] = {};
                    this.localTeeth[tooth][firstEmptyKey] = { code, color };
                }
            },
            removeStatusCode(tooth, code, ignoreSurface = null) {
                this.ensureTeeth();
                const surfaces = ['top', 'bottom', 'left', 'right', 'center', 'whole_tooth'];
                for (const surface of surfaces) {
                    if (surface === ignoreSurface) continue;
                    if ((this.localTeeth?.[tooth]?.[surface]?.code || null) === code) return;
                }
                for (let i = 1; i <= 3; i++) {
                    const key = `line_${i}`;
                    if ((this.localTeeth?.[tooth]?.[key]?.code || null) === code) {
                        delete this.localTeeth[tooth][key];
                        return;
                    }
                }
            },
            applyTool(code, tooth, part) {
                if (this.isReadOnly) return;
                if (!tooth || !part) return;
                this.ensureTeeth();
                const color = this.getToolColor(code);
                if (!color) return;
                if (!this.localTeeth[tooth]) this.localTeeth[tooth] = {};
                if (code === 'CC') {
                    const currentWhole = this.localTeeth?.[tooth]?.whole_tooth || null;
                    if ((currentWhole?.code || null) === 'CC') {
                        delete this.localTeeth[tooth].whole_tooth;
                        this.removeStatusCode(tooth, 'CC');
                        return;
                    }
                    ['top', 'bottom', 'left', 'right', 'center'].forEach((surface) => {
                        if ((this.localTeeth?.[tooth]?.[surface]?.code || null) === 'CC') {
                            delete this.localTeeth[tooth][surface];
                        }
                    });
                    this.localTeeth[tooth].whole_tooth = { color, code };
                    this.removeStatusCode(tooth, code);
                    return;
                }
                const currentData = this.localTeeth[tooth][part] || null;
                if (currentData?.code === code) {
                    delete this.localTeeth[tooth][part];
                    this.removeStatusCode(tooth, code);
                } else {
                    if (currentData?.code) {
                        this.removeStatusCode(tooth, currentData.code, part);
                    }
                    this.localTeeth[tooth][part] = { color, code };
                    this.ensureStatusCode(tooth, code, color);
                }
            },
            clearPart(tooth, part) {
                if (this.isReadOnly) return;
                if (!tooth || !part) return;
                this.ensureTeeth();
                if ((this.localTeeth?.[tooth]?.whole_tooth?.code || null) === 'CC') {
                    delete this.localTeeth[tooth].whole_tooth;
                    this.removeStatusCode(tooth, 'CC');
                }
                const currentData = this.localTeeth?.[tooth]?.[part] || null;
                if (!currentData?.code) return;
                delete this.localTeeth[tooth][part];
                this.removeStatusCode(tooth, currentData.code);
            }
        };
        if (!existing) {
            Alpine.store('dentalChart', base);
        } else {
            Object.assign(existing, base);
        }
        $nextTick(() => { $dispatch('dental-chart-ready'); });
    ">

    @php
        $shapeForTooth = function (int $tooth): string {
            $position = $tooth % 10;
            return $position <= 3 ? 'box' : 'circle';
        };

        $renderTooth = function (int $tooth, bool $isLower, string $instanceKey) use ($teeth, $toolLabels, $quickTools, $picker, $tools, $shapeForTooth) {
            return view('livewire.patient.form.partial.tooth', [
                'tooth' => $tooth,
                'type' => $shapeForTooth($tooth),
                'isLower' => $isLower,
                'instanceKey' => $instanceKey,
                'teeth' => $teeth,
                'toolLabels' => $toolLabels,
                'quickTools' => $quickTools,
                'picker' => $picker,
                'tools' => $tools,
            ])->render();
        };

        $chunk = fn(array $teethSet, int $size) => array_chunk($teethSet, $size);

        $upperLeft = $layout['upper']['left'] ?? [];
        $upperRight = $layout['upper']['right'] ?? [];
        $lowerLeft = $layout['lower']['left'] ?? [];
        $lowerRight = $layout['lower']['right'] ?? [];

        $upperAll = array_merge($upperLeft, $upperRight);
        $lowerAll = array_merge($lowerLeft, $lowerRight);

        $upperRows2 = $chunk($upperAll, 2);
        $lowerRows2 = $chunk($lowerAll, 2);
        $upperRows4 = $chunk($upperAll, 4);
        $lowerRows4 = $chunk($lowerAll, 4);
    @endphp

    <div class="flex flex-col items-center">
        <h3 class="text-gray-400 font-bold tracking-[0.2em] text-sm uppercase mb-4">Upper Arch</h3>
        <div class="p-4 border border-gray-200 rounded-xl bg-white shadow-sm w-full">
            <div class="flex flex-col items-center gap-3 max-[510px]:flex hidden">
                @foreach ($upperRows2 as $row)
                    <div class="flex gap-1">
                        @foreach ($row as $tooth)
                            {!! $renderTooth($tooth, false, 'upper-rows2-' . $loop->parent->index . '-' . $loop->index) !!}
                        @endforeach
                    </div>
                @endforeach
            </div>

            <div class="hidden max-[730px]:flex max-[510px]:hidden flex-col items-center gap-5">
                @foreach ($upperRows4 as $row)
                    <div class="flex gap-1">
                        @foreach ($row as $tooth)
                            {!! $renderTooth($tooth, false, 'upper-rows4-' . $loop->parent->index . '-' . $loop->index) !!}
                        @endforeach
                    </div>
                @endforeach
            </div>

            <div class="hidden max-[1199px]:flex max-[730px]:hidden flex-col items-center gap-5">
                <div class="flex gap-1">
                    @foreach ($upperLeft as $tooth)
                        {!! $renderTooth($tooth, false, 'upper-left-stacked-' . $loop->index) !!}
                    @endforeach
                </div>
                <div class="flex gap-1">
                    @foreach ($upperRight as $tooth)
                        {!! $renderTooth($tooth, false, 'upper-right-stacked-' . $loop->index) !!}
                    @endforeach
                </div>
            </div>

            <div class="hidden min-[1200px]:flex items-end gap-1 justify-center">
                <div class="flex gap-1 border-r-2 border-gray-300 pr-3">
                    @foreach ($upperLeft as $tooth)
                        {!! $renderTooth($tooth, false, 'upper-left-desktop-' . $loop->index) !!}
                    @endforeach
                </div>
                <div class="flex gap-1 pl-3">
                    @foreach ($upperRight as $tooth)
                        {!! $renderTooth($tooth, false, 'upper-right-desktop-' . $loop->index) !!}
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col items-center">
        <h3 class="text-gray-400 font-bold tracking-[0.2em] text-sm uppercase mb-4">Lower Arch</h3>
        <div class="p-4 border border-gray-200 rounded-xl bg-white shadow-sm w-full">
            <div class="flex flex-col items-center gap-3 max-[510px]:flex hidden">
                @foreach ($lowerRows2 as $row)
                    <div class="flex gap-1">
                        @foreach ($row as $tooth)
                            {!! $renderTooth($tooth, true, 'lower-rows2-' . $loop->parent->index . '-' . $loop->index) !!}
                        @endforeach
                    </div>
                @endforeach
            </div>

            <div class="hidden max-[730px]:flex max-[510px]:hidden flex-col items-center gap-5">
                @foreach ($lowerRows4 as $row)
                    <div class="flex gap-1">
                        @foreach ($row as $tooth)
                            {!! $renderTooth($tooth, true, 'lower-rows4-' . $loop->parent->index . '-' . $loop->index) !!}
                        @endforeach
                    </div>
                @endforeach
            </div>

            <div class="hidden max-[1199px]:flex max-[730px]:hidden flex-col items-center gap-5">
                <div class="flex gap-1">
                    @foreach ($lowerLeft as $tooth)
                        {!! $renderTooth($tooth, true, 'lower-left-stacked-' . $loop->index) !!}
                    @endforeach
                </div>
                <div class="flex gap-1">
                    @foreach ($lowerRight as $tooth)
                        {!! $renderTooth($tooth, true, 'lower-right-stacked-' . $loop->index) !!}
                    @endforeach
                </div>
            </div>

            <div class="hidden min-[1200px]:flex items-start gap-1 justify-center">
                <div class="flex gap-1 border-r-2 border-gray-300 pr-3">
                    @foreach ($lowerLeft as $tooth)
                        {!! $renderTooth($tooth, true, 'lower-left-desktop-' . $loop->index) !!}
                    @endforeach
                </div>
                <div class="flex gap-1 pl-3">
                    @foreach ($lowerRight as $tooth)
                        {!! $renderTooth($tooth, true, 'lower-right-desktop-' . $loop->index) !!}
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
