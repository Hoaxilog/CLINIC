<?php

namespace App\Support\Reports;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportDataService
{
    /**
     * @return array{start: Carbon, end: Carbon, label: string, group_by_month: bool}
     */
    public static function resolveRange(string $range = '30d'): array
    {
        $now = Carbon::now();

        return match ($range) {
            '7d' => [
                'start' => $now->copy()->subDays(6)->startOfDay(),
                'end' => $now->copy()->endOfDay(),
                'label' => 'Last 7 Days',
                'group_by_month' => false,
            ],
            'month' => [
                'start' => $now->copy()->startOfMonth(),
                'end' => $now->copy()->endOfDay(),
                'label' => 'This Month',
                'group_by_month' => false,
            ],
            'year' => [
                'start' => $now->copy()->startOfYear(),
                'end' => $now->copy()->endOfDay(),
                'label' => 'This Year',
                'group_by_month' => true,
            ],
            default => [
                'start' => $now->copy()->subDays(29)->startOfDay(),
                'end' => $now->copy()->endOfDay(),
                'label' => 'Last 30 Days',
                'group_by_month' => false,
            ],
        };
    }

    /**
     * @return array{dates: array<int, string>, totals: array<int, int>}
     */
    public static function appointmentsTrend(string $range = '30d'): array
    {
        $resolved = self::resolveRange($range);
        $startDate = $resolved['start'];
        $endDate = $resolved['end'];
        $groupByMonth = $resolved['group_by_month'];

        $driver = DB::getDriverName();

        if ($groupByMonth) {
            $bucketExpr = match ($driver) {
                'pgsql' => "TO_CHAR(appointment_date, 'YYYY-MM')",
                'sqlite' => "strftime('%Y-%m', appointment_date)",
                'sqlsrv' => "FORMAT(appointment_date, 'yyyy-MM')",
                default => "DATE_FORMAT(appointment_date, '%Y-%m')",
            };
        } else {
            $bucketExpr = match ($driver) {
                'sqlsrv' => "CONVERT(date, appointment_date)",
                'sqlite' => "date(appointment_date)",
                default => "DATE(appointment_date)",
            };
        }

        $raw = DB::table('appointments')
            ->select(DB::raw($bucketExpr . ' as bucket'), DB::raw('count(*) as total'))
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->groupBy('bucket')
            ->orderBy('bucket', 'ASC')
            ->pluck('total', 'bucket');

        $dates = [];
        $totals = [];

        if ($groupByMonth) {
            $period = CarbonPeriod::create($startDate->copy()->startOfMonth(), '1 month', $endDate);
            foreach ($period as $date) {
                $key = $date->format('Y-m');
                $dates[] = $date->format('M Y');
                $totals[] = (int) ($raw[$key] ?? 0);
            }
        } else {
            $period = CarbonPeriod::create($startDate, '1 day', $endDate);
            foreach ($period as $date) {
                $key = $date->format('Y-m-d');
                $dates[] = $date->format('M d');
                $totals[] = (int) ($raw[$key] ?? 0);
            }
        }

        return [
            'dates' => $dates,
            'totals' => $totals,
        ];
    }

    /**
     * @return Collection<int, object{status: string, total: int|string}>
     */
    public static function statusData(string $range = '30d'): Collection
    {
        $resolved = self::resolveRange($range);

        return DB::table('appointments')
            ->select('status', DB::raw('count(*) as total'))
            ->whereBetween('appointment_date', [$resolved['start'], $resolved['end']])
            ->groupBy('status')
            ->get();
    }

    /**
     * @return Collection<int, object{service_name: string, total: int|string}>
     */
    public static function serviceData(string $range = '30d', int $limit = 10): Collection
    {
        $resolved = self::resolveRange($range);

        return DB::table('appointments')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->select('services.service_name', DB::raw('count(*) as total'))
            ->whereBetween('appointment_date', [$resolved['start'], $resolved['end']])
            ->groupBy('services.service_name')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }
}
