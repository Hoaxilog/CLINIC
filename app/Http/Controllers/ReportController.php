<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->query('range', '30d');
        $allowed = ['7d', '30d', 'month', 'year'];
        if (!in_array($range, $allowed, true)) {
            $range = '30d';
        }

        $now = Carbon::now();
        switch ($range) {
            case '7d':
                $startDate = $now->copy()->subDays(6)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                $rangeLabel = 'Last 7 Days';
                break;
            case 'month':
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfDay();
                $rangeLabel = 'This Month';
                break;
            case 'year':
                $startDate = $now->copy()->startOfYear();
                $endDate = $now->copy()->endOfDay();
                $rangeLabel = 'This Year';
                break;
            case '30d':
            default:
                $startDate = $now->copy()->subDays(29)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                $rangeLabel = 'Last 30 Days';
                break;
        }

        $driver = DB::getDriverName();
        $groupByMonth = $range === 'year';
        if ($groupByMonth) {
            switch ($driver) {
                case 'pgsql':
                    $bucketExpr = "TO_CHAR(appointment_date, 'YYYY-MM')";
                    break;
                case 'sqlite':
                    $bucketExpr = "strftime('%Y-%m', appointment_date)";
                    break;
                case 'sqlsrv':
                    $bucketExpr = "FORMAT(appointment_date, 'yyyy-MM')";
                    break;
                case 'mysql':
                default:
                    $bucketExpr = "DATE_FORMAT(appointment_date, '%Y-%m')";
                    break;
            }
        } else {
            switch ($driver) {
                case 'sqlsrv':
                    $bucketExpr = "CONVERT(date, appointment_date)";
                    break;
                case 'sqlite':
                    $bucketExpr = "date(appointment_date)";
                    break;
                case 'pgsql':
                case 'mysql':
                default:
                    $bucketExpr = "DATE(appointment_date)";
                    break;
            }
        }

        $dailyRaw = DB::table('appointments')
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
                $totals[] = (int) ($dailyRaw[$key] ?? 0);
            }
        } else {
            $period = CarbonPeriod::create($startDate, '1 day', $endDate);
            foreach ($period as $date) {
                $key = $date->format('Y-m-d');
                $dates[] = $date->format('M d');
                $totals[] = (int) ($dailyRaw[$key] ?? 0);
            }
        }

        $statusData = DB::table('appointments')
            ->select('status', DB::raw('count(*) as total'))
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->groupBy('status')
            ->get();

        $statusLabels = $statusData->pluck('status');
        $statusCounts = $statusData->pluck('total');

        $serviceData = DB::table('appointments')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->select('services.service_name', DB::raw('count(*) as total'))
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->groupBy('services.service_name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $serviceNames = $serviceData->pluck('service_name');
        $serviceCounts = $serviceData->pluck('total');

        $totalAppointments = array_sum($totals);
        $completedCount = $statusData->firstWhere('status', 'Completed')->total ?? 0;
        $completionRate = $totalAppointments > 0 ? round(($completedCount / $totalAppointments) * 100) : 0;
        $topServiceName = $serviceNames->first() ?? 'N/A';

        $completionBadge = [
            'text' => $totalAppointments === 0 ? 'No Data' : ($completionRate < 50 ? 'Needs Attention' : 'On Track'),
            'class' => $totalAppointments === 0
                ? 'text-xs font-bold bg-slate-100 text-slate-600 px-3 py-1 rounded-full'
                : ($completionRate < 50
                    ? 'text-xs font-bold bg-red-50 text-red-600 px-3 py-1 rounded-full'
                    : 'text-xs font-bold bg-emerald-50 text-emerald-600 px-3 py-1 rounded-full'),
        ];

        return view('reports.index', compact(
            'dates',
            'totals',
            'statusLabels',
            'statusCounts',
            'serviceNames',
            'serviceCounts',
            'totalAppointments',
            'completionRate',
            'topServiceName',
            'completionBadge',
            'range',
            'rangeLabel'
        ));
    }
}