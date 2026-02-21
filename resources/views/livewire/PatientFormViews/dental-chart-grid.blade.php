<div class="flex flex-col items-center justify-center gap-12 mx-auto mb-16"
    x-data
    x-on:request-dental-chart-teeth.window="
        console.log('[DentalChart] localTeeth before send', $store.dentalChart.localTeeth);
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
                const surfaces = ['top', 'bottom', 'left', 'right', 'center'];
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
            }
        };
        if (!existing) {
            Alpine.store('dentalChart', base);
        } else {
            Object.assign(existing, base);
        }
    ">
    <div class="flex flex-col items-center">
        <h3 class="text-gray-400 font-bold tracking-[0.2em] text-sm uppercase mb-4">Upper Arch</h3>
        <div class="p-4 border border-gray-200 rounded-xl bg-white shadow-sm w-full">
            {{-- <=510px: 2 teeth per row --}}
            <div class="flex flex-col items-center gap-3 max-[510px]:flex hidden">
                <div class="flex gap-1">
                    @foreach ([11, 12] as $tooth)
                        @php $shape = in_array($tooth, [11, 12, 13]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => false,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
                <div class="flex gap-1">
                    @foreach ([13, 14] as $tooth)
                        @php $shape = in_array($tooth, [11, 12, 13]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => false,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
                <div class="flex gap-1">
                    @foreach ([15, 16] as $tooth)
                        @php $shape = in_array($tooth, [11, 12, 13]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => false,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
                <div class="flex gap-1">
                    @foreach ([17, 18] as $tooth)
                        @php $shape = in_array($tooth, [11, 12, 13]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => false,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
                <div class="flex gap-1">
                    @foreach ([21, 22] as $tooth)
                        @php $shape = in_array($tooth, [21, 22, 23]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => false,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
                <div class="flex gap-1">
                    @foreach ([23, 24] as $tooth)
                        @php $shape = in_array($tooth, [21, 22, 23]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => false,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
                <div class="flex gap-1">
                    @foreach ([25, 26] as $tooth)
                        @php $shape = in_array($tooth, [21, 22, 23]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => false,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
                <div class="flex gap-1">
                    @foreach ([27, 28] as $tooth)
                        @php $shape = in_array($tooth, [21, 22, 23]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => false,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
            </div>

            {{-- <=730px: 4 rows per arch --}}
            <div class="hidden max-[730px]:flex max-[510px]:hidden flex-col items-center gap-5">
                <div class="flex gap-1">
                    @foreach ([11, 12, 13, 14] as $tooth)
                        @php $shape = in_array($tooth, [11, 12, 13]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => false,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
                <div class="flex gap-1">
                    @foreach ([15, 16, 17, 18] as $tooth)
                        @php $shape = in_array($tooth, [11, 12, 13]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => false,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
                <div class="flex gap-1">
                    @foreach ([21, 22, 23, 24] as $tooth)
                        @php $shape = in_array($tooth, [21, 22, 23]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => false,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
                <div class="flex gap-1">
                    @foreach ([25, 26, 27, 28] as $tooth)
                        @php $shape = in_array($tooth, [21, 22, 23]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => false,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
            </div>

            {{-- <1200px: 2 rows per arch --}}
            <div class="hidden max-[1199px]:flex max-[730px]:hidden flex-col items-center gap-5">
                <div class="flex gap-1">
                    @foreach ([18, 17, 16, 15, 14, 13, 12, 11] as $tooth)
                        @php $shape = in_array($tooth, [11, 12, 13]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => false,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
                <div class="flex gap-1">
                    @foreach ([21, 22, 23, 24, 25, 26, 27, 28] as $tooth)
                        @php $shape = in_array($tooth, [21, 22, 23]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => false,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
            </div>

            {{-- >=1200px: original 2 groups in a row --}}
            <div class="hidden min-[1200px]:flex items-end gap-1 justify-center">
                <div class="flex gap-1 border-r-2 border-gray-300 pr-3">
                    @foreach ([18, 17, 16, 15, 14, 13, 12, 11] as $tooth)
                        @php $shape = in_array($tooth, [11, 12, 13]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => false,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
                <div class="flex gap-1 pl-3">
                    @foreach ([21, 22, 23, 24, 25, 26, 27, 28] as $tooth)
                        @php $shape = in_array($tooth, [21, 22, 23]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => false,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="flex flex-col items-center">
        <h3 class="text-gray-400 font-bold tracking-[0.2em] text-sm uppercase mb-4">Lower Arch</h3>
        <div class="p-4 border border-gray-200 rounded-xl bg-white shadow-sm w-full">
            {{-- <=510px: 2 teeth per row --}}
            <div class="flex flex-col items-center gap-3 max-[510px]:flex hidden">
                <div class="flex gap-1">
                    @foreach ([41, 42] as $tooth)
                        @php $shape = in_array($tooth, [41, 42, 43]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => true,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
                <div class="flex gap-1">
                    @foreach ([43, 44] as $tooth)
                        @php $shape = in_array($tooth, [41, 42, 43]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => true,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
                <div class="flex gap-1">
                    @foreach ([45, 46] as $tooth)
                        @php $shape = in_array($tooth, [41, 42, 43]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => true,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
                <div class="flex gap-1">
                    @foreach ([47, 48] as $tooth)
                        @php $shape = in_array($tooth, [41, 42, 43]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => true,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
                <div class="flex gap-1">
                    @foreach ([31, 32] as $tooth)
                        @php $shape = in_array($tooth, [31, 32, 33]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => true,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
                <div class="flex gap-1">
                    @foreach ([33, 34] as $tooth)
                        @php $shape = in_array($tooth, [31, 32, 33]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => true,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
                <div class="flex gap-1">
                    @foreach ([35, 36] as $tooth)
                        @php $shape = in_array($tooth, [31, 32, 33]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => true,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
                <div class="flex gap-1">
                    @foreach ([37, 38] as $tooth)
                        @php $shape = in_array($tooth, [31, 32, 33]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => true,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
            </div>

            {{-- <=730px: 4 rows per arch --}}
            <div class="hidden max-[730px]:flex max-[510px]:hidden flex-col items-center gap-5">
                <div class="flex gap-1">
                    @foreach ([41, 42, 43, 44] as $tooth)
                        @php $shape = in_array($tooth, [41, 42, 43]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => true,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
                <div class="flex gap-1">
                    @foreach ([45, 46, 47, 48] as $tooth)
                        @php $shape = in_array($tooth, [41, 42, 43]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => true,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
                <div class="flex gap-1">
                    @foreach ([31, 32, 33, 34] as $tooth)
                        @php $shape = in_array($tooth, [31, 32, 33]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => true,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
                <div class="flex gap-1">
                    @foreach ([35, 36, 37, 38] as $tooth)
                        @php $shape = in_array($tooth, [31, 32, 33]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => true,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
            </div>

            {{-- <1200px: 2 rows per arch --}}
            <div class="hidden max-[1199px]:flex max-[730px]:hidden flex-col items-center gap-5">
                <div class="flex gap-1">
                    @foreach ([48, 47, 46, 45, 44, 43, 42, 41] as $tooth)
                        @php $shape = in_array($tooth, [41, 42, 43]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => true,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
                <div class="flex gap-1">
                    @foreach ([31, 32, 33, 34, 35, 36, 37, 38] as $tooth)
                        @php $shape = in_array($tooth, [31, 32, 33]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => true,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
            </div>

            {{-- >=1200px: original 2 groups in a row --}}
            <div class="hidden min-[1200px]:flex items-start gap-1 justify-center">
                <div class="flex gap-1 border-r-2 border-gray-300 pr-3">
                    @foreach ([48, 47, 46, 45, 44, 43, 42, 41] as $tooth)
                        @php $shape = in_array($tooth, [41, 42, 43]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => true,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
                <div class="flex gap-1 pl-3">
                    @foreach ([31, 32, 33, 34, 35, 36, 37, 38] as $tooth)
                        @php $shape = in_array($tooth, [31, 32, 33]) ? 'box' : 'circle'; @endphp
                        @include('livewire.PatientFormViews.partial.tooth', [
                            'tooth' => $tooth,
                            'type' => $shape,
                            'isLower' => true,
                            'teeth' => $teeth,
                            'toolLabels' => $toolLabels,
                            'quickTools' => $quickTools,
                            'picker' => $picker,
                            'tools' => $tools,
                        ])
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
