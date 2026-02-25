<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Dashboard extends Controller
{
    protected function buildPatientStats(string $patientStatsRange): array
    {
        $today = Carbon::today();
        $firstAppointmentSub = DB::table('appointments')
            ->selectRaw('patient_id, DATE(MIN(appointment_date)) as first_date')
            ->groupBy('patient_id');

        if ($patientStatsRange === 'weekly') {
            $statsStart = $today->copy()->subDays(6)->startOfDay();
            $patientStatsLabel = 'Weekly new vs returning';
        } else {
            $statsStart = $today->copy()->startOfMonth()->startOfDay();
            $patientStatsLabel = 'Monthly new vs returning';
        }
        $statsEnd = $today->copy()->endOfDay();

        $patientStatsDates = [];
        $newPatientCounts = [];
        $returningPatientCounts = [];

        for ($cursor = $statsStart->copy(); $cursor->lte($statsEnd); $cursor->addDay()) {
            $date = $cursor->toDateString();
            $patientStatsDates[] = Carbon::parse($date)->format('d M');

            $newCount = DB::table(DB::raw("({$firstAppointmentSub->toSql()}) as firsts"))
                ->mergeBindings($firstAppointmentSub)
                ->where('first_date', $date)
                ->count();

            $returningCount = DB::table('appointments as a')
                ->join(DB::raw("({$firstAppointmentSub->toSql()}) as firsts"), 'firsts.patient_id', '=', 'a.patient_id')
                ->mergeBindings($firstAppointmentSub)
                ->whereDate('a.appointment_date', $date)
                ->where('firsts.first_date', '<', $date)
                ->distinct('a.patient_id')
                ->count('a.patient_id');

            $newPatientCounts[] = $newCount;
            $returningPatientCounts[] = $returningCount;
        }

        $patientStatsTotal = array_sum($newPatientCounts) + array_sum($returningPatientCounts);

        return [
            'patientStatsRange' => $patientStatsRange,
            'patientStatsLabel' => $patientStatsLabel,
            'patientStatsTotal' => $patientStatsTotal,
            'patientStatsDates' => $patientStatsDates,
            'newPatientCounts' => $newPatientCounts,
            'returningPatientCounts' => $returningPatientCounts,
        ];
    }

    public function patientStats(Request $request)
    {
        $patientStatsRange = $request->query('patient_stats_range', 'monthly');
        if (!in_array($patientStatsRange, ['weekly', 'monthly'], true)) {
            $patientStatsRange = 'monthly';
        }

        return response()->json($this->buildPatientStats($patientStatsRange));
    }

    public function index() {
        $today = Carbon::today();
        $range = request('range', '15d');
        $patientStatsRange = request('patient_stats_range', 'monthly');

        if (!in_array($patientStatsRange, ['weekly', 'monthly'], true)) {
            $patientStatsRange = 'monthly';
        }

        $rangeStart = $today->copy()->subDays(14)->startOfDay();
        $rangeLabel = 'Last 15 Days';
        $rangeDays = 15;

        if ($range === '30d') {
            $rangeStart = $today->copy()->subDays(29)->startOfDay();
            $rangeLabel = 'Last 30 Days';
            $rangeDays = 30;
        } elseif ($range === 'month') {
            $rangeStart = $today->copy()->startOfMonth();
            $rangeLabel = 'This Month';
            $rangeDays = $today->diffInDays($rangeStart) + 1;
        }

        $last30Start = $today->copy()->subDays(29)->startOfDay();
        $last30End = $today->copy()->endOfDay();

        $bookedLast30 = DB::table('appointments')
            ->whereBetween('appointment_date', [$last30Start, $last30End])
            ->count();

        $cancelledLast30 = DB::table('appointments')
            ->whereBetween('appointment_date', [$last30Start, $last30End])
            ->where('status', 'Cancelled')
            ->count();

        $todayAppointmentsCount = DB::table('appointments')
            ->whereDate('appointment_date', $today)
            ->count();

        $todayCompletedCount = DB::table('appointments')
            ->whereDate('appointment_date', $today)
            ->where('status', 'Completed')
            ->count();

        $todayCancelledCount = DB::table('appointments')
            ->whereDate('appointment_date', $today)
            ->where('status', 'Cancelled')
            ->count();

        $todayUpcomingCount = $todayAppointmentsCount - $todayCompletedCount - $todayCancelledCount;

        $rangeEnd = $today->copy()->endOfDay();
        $prevRangeEnd = $rangeStart->copy()->subDay()->endOfDay();
        $prevRangeStart = $prevRangeEnd->copy()->subDays($rangeDays - 1)->startOfDay();

        $rangeAppointmentsCount = DB::table('appointments')
            ->whereBetween('appointment_date', [$rangeStart, $rangeEnd])
            ->count();

        $prevRangeAppointmentsCount = DB::table('appointments')
            ->whereBetween('appointment_date', [$prevRangeStart, $prevRangeEnd])
            ->count();

        $rangeAppointmentsDiff = $rangeAppointmentsCount - $prevRangeAppointmentsCount;
        $rangeAppointmentsPct = $prevRangeAppointmentsCount > 0
            ? round(($rangeAppointmentsDiff / $prevRangeAppointmentsCount) * 100)
            : null;

        $todayProfit = (float) (DB::table('treatment_records')
            ->whereDate('created_at', $today)
            ->selectRaw('COALESCE(SUM(COALESCE(amount_charged,0) - COALESCE(cost_of_treatment,0)), 0) as profit')
            ->value('profit') ?? 0);

        $yesterday = $today->copy()->subDay();
        $yesterdayProfit = (float) (DB::table('treatment_records')
            ->whereDate('created_at', $yesterday)
            ->selectRaw('COALESCE(SUM(COALESCE(amount_charged,0) - COALESCE(cost_of_treatment,0)), 0) as profit')
            ->value('profit') ?? 0);

        $todayProfitPct = $yesterdayProfit > 0
            ? round((($todayProfit - $yesterdayProfit) / $yesterdayProfit) * 100)
            : null;

        $todayRevenue = (float) (DB::table('treatment_records')
            ->whereDate('created_at', $today)
            ->selectRaw('COALESCE(SUM(COALESCE(amount_charged,0)), 0) as revenue')
            ->value('revenue') ?? 0);

        $weekStart = $today->copy()->startOfWeek();
        $weekEnd = $today->copy()->endOfWeek();
        $prevWeekStart = $weekStart->copy()->subWeek();
        $prevWeekEnd = $weekEnd->copy()->subWeek();

        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();
        $prevMonthStart = $monthStart->copy()->subMonthNoOverflow();
        $prevMonthEnd = $monthEnd->copy()->subMonthNoOverflow();

        $weekProfit = (float) (DB::table('treatment_records')
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->selectRaw('COALESCE(SUM(COALESCE(amount_charged,0) - COALESCE(cost_of_treatment,0)), 0) as profit')
            ->value('profit') ?? 0);

        $prevWeekProfit = (float) (DB::table('treatment_records')
            ->whereBetween('created_at', [$prevWeekStart, $prevWeekEnd])
            ->selectRaw('COALESCE(SUM(COALESCE(amount_charged,0) - COALESCE(cost_of_treatment,0)), 0) as profit')
            ->value('profit') ?? 0);

        $weekProfitPct = $prevWeekProfit > 0
            ? round((($weekProfit - $prevWeekProfit) / $prevWeekProfit) * 100)
            : null;

        $monthProfit = (float) (DB::table('treatment_records')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->selectRaw('COALESCE(SUM(COALESCE(amount_charged,0) - COALESCE(cost_of_treatment,0)), 0) as profit')
            ->value('profit') ?? 0);

        $prevMonthProfit = (float) (DB::table('treatment_records')
            ->whereBetween('created_at', [$prevMonthStart, $prevMonthEnd])
            ->selectRaw('COALESCE(SUM(COALESCE(amount_charged,0) - COALESCE(cost_of_treatment,0)), 0) as profit')
            ->value('profit') ?? 0);

        $monthProfitPct = $prevMonthProfit > 0
            ? round((($monthProfit - $prevMonthProfit) / $prevMonthProfit) * 100)
            : null;

        $rangeProfit = (float) (DB::table('treatment_records')
            ->whereBetween('created_at', [$rangeStart, $rangeEnd])
            ->selectRaw('COALESCE(SUM(COALESCE(amount_charged,0) - COALESCE(cost_of_treatment,0)), 0) as profit')
            ->value('profit') ?? 0);

        $rangeRevenue = (float) (DB::table('treatment_records')
            ->whereBetween('created_at', [$rangeStart, $rangeEnd])
            ->selectRaw('COALESCE(SUM(COALESCE(amount_charged,0)), 0) as revenue')
            ->value('revenue') ?? 0);

        $prevRangeProfit = (float) (DB::table('treatment_records')
            ->whereBetween('created_at', [$prevRangeStart, $prevRangeEnd])
            ->selectRaw('COALESCE(SUM(COALESCE(amount_charged,0) - COALESCE(cost_of_treatment,0)), 0) as profit')
            ->value('profit') ?? 0);

        $prevRangeRevenue = (float) (DB::table('treatment_records')
            ->whereBetween('created_at', [$prevRangeStart, $prevRangeEnd])
            ->selectRaw('COALESCE(SUM(COALESCE(amount_charged,0)), 0) as revenue')
            ->value('revenue') ?? 0);

        $rangeProfitPct = $prevRangeProfit > 0
            ? round((($rangeProfit - $prevRangeProfit) / $prevRangeProfit) * 100)
            : null;

        $rangeRevenuePct = $prevRangeRevenue > 0
            ? round((($rangeRevenue - $prevRangeRevenue) / $prevRangeRevenue) * 100)
            : null;

        $trendDates = [];
        $trendAppointments = [];
        $trendProfit = [];
        for ($i = $rangeDays - 1; $i >= 0; $i--) {
            $date = $rangeEnd->copy()->subDays($i);
            $trendDates[] = $date->format('M d');

            $trendAppointments[] = DB::table('appointments')
                ->whereDate('appointment_date', $date)
                ->count();

            $dailyProfit = (float) (DB::table('treatment_records')
                ->whereDate('created_at', $date)
                ->selectRaw('COALESCE(SUM(COALESCE(amount_charged,0) - COALESCE(cost_of_treatment,0)), 0) as profit')
                ->value('profit') ?? 0);
            $trendProfit[] = $dailyProfit;
        }

        $statusCountsMap = DB::table('appointments')
            ->whereDate('appointment_date', $today)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $statusLabels = ['Scheduled', 'Waiting', 'Ongoing', 'Completed', 'Cancelled'];
        $statusCounts = [];
        foreach ($statusLabels as $label) {
            $statusCounts[] = $statusCountsMap[$label] ?? 0;
        }

        $nextAppointments = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->whereDate('appointments.appointment_date', $today)
            ->whereNotIn('appointments.status', ['Cancelled', 'Completed'])
            ->orderBy('appointments.appointment_date', 'asc')
            ->select(
                'appointments.appointment_date',
                'appointments.status',
                'patients.first_name',
                'patients.last_name',
                'services.service_name'
            )
            ->limit(3)
            ->get();

        $pendingApprovalsCount = DB::table('appointments')->where('status', 'Pending')->count();
        $totalPatients = DB::table('patients')->count();

        $patientStats = $this->buildPatientStats($patientStatsRange);

        return view('dashboard', [
            'todayAppointmentsCount' => $todayAppointmentsCount,
            'todayCompletedCount'    => $todayCompletedCount,
            'todayCancelledCount'    => $todayCancelledCount,
            'todayUpcomingCount'     => max(0, $todayUpcomingCount),
            'rangeAppointmentsCount'  => $rangeAppointmentsCount,
            'prevRangeAppointmentsCount' => $prevRangeAppointmentsCount,
            'rangeAppointmentsDiff'   => $rangeAppointmentsDiff,
            'rangeAppointmentsPct'    => $rangeAppointmentsPct,
            'todayProfit'            => $todayProfit,
            'todayRevenue'           => $todayRevenue,
            'yesterdayProfit'        => $yesterdayProfit,
            'todayProfitPct'         => $todayProfitPct,
            'weekProfit'             => $weekProfit,
            'weekProfitPct'          => $weekProfitPct,
            'monthProfit'            => $monthProfit,
            'monthProfitPct'         => $monthProfitPct,
            'rangeProfit'            => $rangeProfit,
            'rangeRevenue'           => $rangeRevenue,
            'prevRangeProfit'        => $prevRangeProfit,
            'prevRangeRevenue'       => $prevRangeRevenue,
            'rangeProfitPct'         => $rangeProfitPct,
            'rangeRevenuePct'        => $rangeRevenuePct,
            'trendDates'             => $trendDates,
            'trendAppointments'      => $trendAppointments,
            'trendProfit'            => $trendProfit,
            'statusLabels'           => $statusLabels,
            'statusCounts'           => $statusCounts,
            'bookedLast30'           => $bookedLast30,
            'cancelledLast30'        => $cancelledLast30,
            'nextAppointments'       => $nextAppointments,
            'pendingApprovalsCount'  => $pendingApprovalsCount,
            'totalPatients'          => $totalPatients,
            'patientStatsTotal'      => $patientStats['patientStatsTotal'],
            'patientStatsDates'      => $patientStats['patientStatsDates'],
            'newPatientCounts'       => $patientStats['newPatientCounts'],
            'returningPatientCounts' => $patientStats['returningPatientCounts'],
            'patientStatsRange'      => $patientStats['patientStatsRange'],
            'patientStatsLabel'      => $patientStats['patientStatsLabel'],
            'range'                  => $range,
            'rangeLabel'             => $rangeLabel,
        ]);
    }

}
