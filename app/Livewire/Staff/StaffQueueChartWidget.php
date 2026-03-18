<?php

namespace App\Livewire\Staff;

use Filament\Widgets\ChartWidget;

class StaffQueueChartWidget extends ChartWidget
{
    protected ?string $heading = 'Staff Queue Chart Widget';

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
