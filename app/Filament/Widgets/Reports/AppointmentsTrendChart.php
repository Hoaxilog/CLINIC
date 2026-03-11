<?php

namespace App\Filament\Widgets\Reports;

use App\Support\Reports\ReportDataService;
use Filament\Widgets\ChartWidget;

class AppointmentsTrendChart extends ChartWidget
{
    protected ?string $heading = 'Appointments Trend';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = [
        'default' => 12,
        'xl' => 8,
    ];

    protected function getData(): array
    {
        $trend = ReportDataService::appointmentsTrend('30d');

        return [
            'datasets' => [
                [
                    'label' => 'Appointments',
                    'data' => $trend['totals'],
                    'borderColor' => '#0ea5e9',
                    'backgroundColor' => 'rgba(14, 165, 233, 0.14)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $trend['dates'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
