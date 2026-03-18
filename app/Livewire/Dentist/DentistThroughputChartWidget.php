<?php

namespace App\Livewire\Dentist;

use Filament\Widgets\ChartWidget;

class DentistThroughputChartWidget extends ChartWidget
{
    protected ?string $heading = 'Dentist Throughput Chart Widget';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
