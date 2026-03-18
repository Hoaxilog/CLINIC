<?php

namespace App\Livewire\Admin;

use Filament\Widgets\ChartWidget;

class AdminWeeklyComparisonWidget extends ChartWidget
{
    protected ?string $heading = 'Admin Weekly Comparison Widget';

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
