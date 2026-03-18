<?php

namespace App\Livewire\Admin;

use Filament\Widgets\ChartWidget;

class AdminFinanceChartWidget extends ChartWidget
{
    protected ?string $heading = 'Admin Finance Chart Widget';

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
