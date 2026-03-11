<?php

namespace App\Filament\Widgets\Reports;

use App\Support\Reports\ReportDataService;
use Filament\Widgets\ChartWidget;

class TopServicesChart extends ChartWidget
{
    protected ?string $heading = 'Top Services';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $serviceData = ReportDataService::serviceData('30d');

        return [
            'datasets' => [
                [
                    'label' => 'Appointments',
                    'data' => $serviceData->pluck('total')->map(fn ($count) => (int) $count)->values()->all(),
                    'backgroundColor' => '#f59e0b',
                    'borderRadius' => 8,
                ],
            ],
            'labels' => $serviceData->pluck('service_name')->values()->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
