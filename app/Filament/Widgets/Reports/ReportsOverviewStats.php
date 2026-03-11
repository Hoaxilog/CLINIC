<?php

namespace App\Filament\Widgets\Reports;

use App\Support\Reports\ReportDataService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReportsOverviewStats extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $trend = ReportDataService::appointmentsTrend('30d');
        $statusData = ReportDataService::statusData('30d');
        $serviceData = ReportDataService::serviceData('30d');

        $totalAppointments = array_sum($trend['totals']);
        $completedCount = (int) ($statusData->firstWhere('status', 'Completed')->total ?? 0);
        $completionRate = $totalAppointments > 0
            ? (int) round(($completedCount / $totalAppointments) * 100)
            : 0;

        $topServiceName = $serviceData->pluck('service_name')->first() ?? 'N/A';

        return [
            Stat::make('Total Appointments', number_format($totalAppointments))
                ->description('Last 30 days')
                ->color('gray'),
            Stat::make('Completed', number_format($completedCount))
                ->description('Status = Completed')
                ->color('success'),
            Stat::make('Completion Rate', $completionRate . '%')
                ->description('Completed / Total')
                ->color($completionRate >= 50 ? 'success' : 'warning'),
            Stat::make('Top Service', $topServiceName)
                ->description('Highest appointment volume')
                ->color('primary'),
        ];
    }
}
