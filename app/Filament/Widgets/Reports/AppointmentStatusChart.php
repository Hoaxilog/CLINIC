<?php

namespace App\Filament\Widgets\Reports;

use App\Support\Reports\ReportDataService;
use Filament\Widgets\ChartWidget;

class AppointmentStatusChart extends ChartWidget
{
    protected ?string $heading = 'Status Breakdown';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = [
        'default' => 12,
        'xl' => 4,
    ];

    protected function getData(): array
    {
        $statusData = ReportDataService::statusData('30d');

        return [
            'datasets' => [
                [
                    'data' => $statusData->pluck('total')->map(fn ($count) => (int) $count)->values()->all(),
                    'backgroundColor' => ['#0ea5e9', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6', '#64748b'],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $statusData->pluck('status')->values()->all(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
