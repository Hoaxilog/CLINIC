<?php

namespace App\Livewire\PatientFormController;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;

class DentalChartGrid extends Component
{
    public $teeth = [];
    #[Reactive]
    public $isReadOnly = false;
    public $selectedTool = null;
    public $toolLabels = [];
    public $picker = ['open' => false, 'tooth' => null, 'part' => null, 'expanded' => false];
    public $quickToolCodes = ['C', 'CI', 'CD', 'CP', 'Am', 'LC', 'GIC', 'S', 'X', '---'];

    public $tools = [
        ['label' => 'Chief Complaint', 'code' => 'CC', 'color' => 'red'],
        ['label' => 'Caries', 'code' => 'C', 'color' => 'red'],
        ['label' => 'Incipient Caries', 'code' => 'CI', 'color' => 'red'],
        ['label' => 'Deep Caries', 'code' => 'CD', 'color' => 'red'],
        ['label' => 'Caries w/ Pulp Inv.', 'code' => 'CP', 'color' => 'red'],
        ['label' => 'Missing / Extracted', 'code' => '---', 'color' => 'red'],
        ['label' => 'Unerupted', 'code' => 'U', 'color' => 'red'],
        ['label' => 'Dislodged Restoration', 'code' => 'DR', 'color' => 'red'],
        ['label' => 'Abrasion', 'code' => 'Ab', 'color' => 'red'],
        ['label' => 'Fractured Tooth', 'code' => 'FT', 'color' => 'red'],
        ['label' => 'Root Fragment', 'code' => 'RF', 'color' => 'red'],
        ['label' => 'Impacted Tooth', 'code' => 'Imp', 'color' => 'red'],
        ['label' => 'For Extraction', 'code' => 'X', 'color' => 'red'],
        ['label' => 'Non-vital Tooth', 'code' => 'NV', 'color' => 'red'],
        ['label' => 'Temporary Filling', 'code' => 'TF', 'color' => 'red'],
        ['label' => '1st Degree Mobility', 'code' => '1Â°', 'color' => 'red'],
        ['label' => '2nd Degree Mobility', 'code' => '2Â°', 'color' => 'red'],
        ['label' => '3rd Degree Mobility', 'code' => '3Â°', 'color' => 'red'],
        ['label' => 'Amalgam Filling', 'code' => 'Am', 'color' => 'blue'],
        ['label' => 'Composite Filling', 'code' => 'LC', 'color' => 'blue'],
        ['label' => 'Glass Ionomer', 'code' => 'GIC', 'color' => 'blue'],
        ['label' => 'Inlay / Onlay', 'code' => 'In/On', 'color' => 'blue'],
        ['label' => 'Sealant', 'code' => 'S', 'color' => 'blue'],
        ['label' => 'Stainless Steel Crown', 'code' => 'SSC', 'color' => 'blue'],
        ['label' => 'Acrylic Crown/Bridge', 'code' => 'AC/AB', 'color' => 'blue'],
        ['label' => 'Full Porcelain', 'code' => 'FPC', 'color' => 'blue'],
        ['label' => 'Porcelain Fused Metal', 'code' => 'PFM', 'color' => 'blue'],
        ['label' => 'Abutment Tooth', 'code' => 'Abt', 'color' => 'blue'],
        ['label' => 'Pontic', 'code' => 'Po', 'color' => 'blue'],
        ['label' => 'Maryland Bridge', 'code' => 'MC', 'color' => 'blue'],
        ['label' => 'Veneer / Laminate', 'code' => 'V', 'color' => 'blue'],
        ['label' => 'Root Canal Treated', 'code' => 'RCT', 'color' => 'blue'],
        ['label' => 'Erupting Tooth', 'code' => 'â†‘/â†“', 'color' => 'blue'],
    ];

    public function mount($teeth = [], $isReadOnly = false)
    {
        $this->teeth = $teeth ?? [];
        $this->isReadOnly = $isReadOnly;

        foreach ($this->tools as $tool) {
            $this->toolLabels[$tool['code']] = $tool['label'];
        }
    }

    public function provideTeeth($teeth = null)
    {
        if (is_array($teeth)) {
            $this->teeth = $teeth;
        }
        $this->dispatch('dentalChartTeethProvided', teeth: $this->teeth)
            ->to('PatientFormController.dental-chart');
    }

    public function applyPickerTool($code, $tooth = null, $part = null)
    {
        if ($this->isReadOnly) return;
        if (!$tooth || !$part) return;

        $this->selectedTool = $code;
        $this->updateSurface($tooth, $part);
        $this->selectedTool = null;
    }

    private function getCurrentToolColor()
    {
        if (!$this->selectedTool) return null;
        foreach ($this->tools as $tool) {
            if ($tool['code'] === $this->selectedTool) {
                return $tool['color'];
            }
        }
        return 'white';
    }

    public function updateSurface($tooth_num, $part)
    {
        if ($this->isReadOnly) return;

        $toolColor = $this->getCurrentToolColor();
        if (!$toolColor) return;

        $currentData = $this->teeth[$tooth_num][$part] ?? null;

        if ($currentData && isset($currentData['code']) && $currentData['code'] === $this->selectedTool) {
            unset($this->teeth[$tooth_num][$part]);
            $this->removeStatusCode($tooth_num, $this->selectedTool);
        } else {
            if ($currentData && isset($currentData['code'])) {
                $this->removeStatusCode($tooth_num, $currentData['code'], $part);
            }
            $this->teeth[$tooth_num][$part] = [
                'color' => $toolColor,
                'code' => $this->selectedTool
            ];
            $this->ensureStatusCode($tooth_num, $this->selectedTool, $toolColor);
        }
    }

    private function ensureStatusCode($tooth_num, $code, $color)
    {
        $firstEmptyKey = null;
        for ($i = 1; $i <= 3; $i++) {
            $key = 'line_' . $i;
            $line = $this->teeth[$tooth_num][$key] ?? null;
            if (($line['code'] ?? null) === $code) return;
            if (!$line && $firstEmptyKey === null) $firstEmptyKey = $key;
        }
        if ($firstEmptyKey) {
            $this->teeth[$tooth_num][$firstEmptyKey] = ['code' => $code, 'color' => $color];
        }
    }

    private function removeStatusCode($tooth_num, $code, $ignoreSurface = null)
    {
        $surfaces = ['top', 'bottom', 'left', 'right', 'center'];
        foreach ($surfaces as $surface) {
            if ($surface === $ignoreSurface) continue;
            if (($this->teeth[$tooth_num][$surface]['code'] ?? null) === $code) return;
        }
        for ($i = 1; $i <= 3; $i++) {
            $key = 'line_' . $i;
            if (($this->teeth[$tooth_num][$key]['code'] ?? null) === $code) {
                unset($this->teeth[$tooth_num][$key]);
                return;
            }
        }
    }

    public function updateStatus($tooth_num, $line_index)
    {
        if ($this->isReadOnly) return;

        $toolColor = $this->getCurrentToolColor();
        if (!$toolColor) return;

        $key = 'line_' . $line_index;
        $currentLine = $this->teeth[$tooth_num][$key] ?? null;

        if (
            ($currentLine['code'] ?? null) === $this->selectedTool &&
            ($currentLine['color'] ?? null) === $toolColor
        ) {
            unset($this->teeth[$tooth_num][$key]);
        } else {
            $this->teeth[$tooth_num][$key] = [
                'code' => $this->selectedTool,
                'color' => $toolColor
            ];
        }
    }

    public function render()
    {
        $quickTools = array_values(array_filter($this->tools, function ($tool) {
            return in_array($tool['code'], $this->quickToolCodes, true);
        }));

        return view('livewire.PatientFormViews.dental-chart-grid', [
            'quickTools' => $quickTools,
        ]);
    }
}
