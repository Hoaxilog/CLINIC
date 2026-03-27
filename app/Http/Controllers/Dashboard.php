<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        if (! in_array($patientStatsRange, ['weekly', 'monthly'], true)) {
            $patientStatsRange = 'monthly';
        }

        return response()->json($this->buildPatientStats($patientStatsRange));
    }

    public function index()
    {
        $user = Auth::user();
        $isAdminDashboard = $user?->isAdmin() ?? false;
        $isDentistDashboard = $user?->isDentist() ?? false;
        $isStaffDashboard = $user?->isStaff() ?? false;
        $today = Carbon::today();
        $range = request('range', '15d');
        $patientStatsRange = request('patient_stats_range', 'monthly');
        $cancellationRange = request('cancellation_range', 'monthly');

        if (! in_array($patientStatsRange, ['weekly', 'monthly'], true)) {
            $patientStatsRange = 'monthly';
        }

        if (! in_array($cancellationRange, ['weekly', 'monthly'], true)) {
            $cancellationRange = 'monthly';
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
        $cancellationStart = $cancellationRange === 'weekly'
            ? $today->copy()->startOfWeek()->startOfDay()
            : $today->copy()->startOfMonth()->startOfDay();
        $cancellationEnd = $today->copy()->endOfDay();
        $cancellationLabel = $cancellationRange === 'weekly' ? 'This Week' : 'This Month';

        $bookedLast30 = DB::table('appointments')
            ->whereBetween('appointment_date', [$cancellationStart, $cancellationEnd])
            ->count();

        $cancelledLast30 = DB::table('appointments')
            ->whereBetween('appointment_date', [$cancellationStart, $cancellationEnd])
            ->where('status', 'Cancelled')
            ->count();
        $cancellationRate = $bookedLast30 > 0
            ? round(($cancelledLast30 / $bookedLast30) * 100, 1)
            : 0.0;

        $todayAppointmentsCount = DB::table('appointments')
            ->whereDate('appointment_date', $today)
            ->where('status', '!=', 'Pending')
            ->count();

        $todayCompletedCount = DB::table('appointments')
            ->whereDate('appointment_date', $today)
            ->where('status', 'Completed')
            ->count();

        $todayCancelledCount = DB::table('appointments')
            ->whereDate('appointment_date', $today)
            ->where('status', 'Cancelled')
            ->count();

        $rangeEnd = $today->copy()->endOfDay();
        $prevRangeEnd = $rangeStart->copy()->subDay()->endOfDay();
        $prevRangeStart = $prevRangeEnd->copy()->subDays($rangeDays - 1)->startOfDay();

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
        $todayCost = (float) (DB::table('treatment_records')
            ->whereDate('created_at', $today)
            ->selectRaw('COALESCE(SUM(COALESCE(cost_of_treatment,0)), 0) as total_cost')
            ->value('total_cost') ?? 0);
        $todayProfitMargin = $todayRevenue > 0
            ? round(($todayProfit / $todayRevenue) * 100, 1)
            : null;

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
        $monthRevenue = (float) (DB::table('treatment_records')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->selectRaw('COALESCE(SUM(COALESCE(amount_charged,0)), 0) as revenue')
            ->value('revenue') ?? 0);
        $monthCost = (float) (DB::table('treatment_records')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->selectRaw('COALESCE(SUM(COALESCE(cost_of_treatment,0)), 0) as total_cost')
            ->value('total_cost') ?? 0);

        $prevMonthProfit = (float) (DB::table('treatment_records')
            ->whereBetween('created_at', [$prevMonthStart, $prevMonthEnd])
            ->selectRaw('COALESCE(SUM(COALESCE(amount_charged,0) - COALESCE(cost_of_treatment,0)), 0) as profit')
            ->value('profit') ?? 0);

        $monthProfitPct = $prevMonthProfit > 0
            ? round((($monthProfit - $prevMonthProfit) / $prevMonthProfit) * 100)
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

        $appointmentTrendLabels = [];
        $appointmentTrendCurrentWeek = [];
        $appointmentTrendPreviousWeek = [];
        $appointmentTrendComparisonDates = [];
        $comparisonDayCount = $weekStart->diffInDays($today) + 1;

        for ($i = 0; $i < $comparisonDayCount; $i++) {
            $currentDate = $weekStart->copy()->addDays($i);
            $previousDate = $currentDate->copy()->subWeek();

            $appointmentTrendLabels[] = $currentDate->format('D');
            $appointmentTrendComparisonDates[] = $currentDate->format('M d');
            $appointmentTrendCurrentWeek[] = DB::table('appointments')
                ->whereDate('appointment_date', $currentDate)
                ->count();
            $appointmentTrendPreviousWeek[] = DB::table('appointments')
                ->whereDate('appointment_date', $previousDate)
                ->count();
        }

        $statusCountsMap = DB::table('appointments')
            ->whereDate('appointment_date', $today)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $statusLabels = ['Scheduled', 'Waiting', 'Arrived', 'Ongoing', 'Completed', 'Cancelled'];
        $statusCounts = [];
        foreach ($statusLabels as $label) {
            $statusCounts[] = $statusCountsMap[$label] ?? 0;
        }

        $todayScheduleAppointments = DB::table('appointments')
            ->leftJoin('patients', 'appointments.patient_id', '=', 'patients.id')
            ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
            ->whereDate('appointments.appointment_date', $today)
            ->where('appointments.status', '!=', 'Pending')
            ->orderBy('appointments.appointment_date', 'asc')
            ->select(
                'appointments.id',
                'appointments.appointment_date',
                'appointments.status',
                DB::raw("COALESCE(patients.first_name, appointments.requested_patient_first_name, appointments.requester_first_name, 'Unknown') as first_name"),
                DB::raw("COALESCE(patients.last_name, appointments.requested_patient_last_name, appointments.requester_last_name, 'Patient') as last_name"),
                DB::raw("COALESCE(services.service_name, 'General Consultation') as service_name")
            )
            ->get();

        $waitingPatientsCount = (int) ($statusCountsMap['Waiting'] ?? 0);
        $arrivedPatientsCount = (int) ($statusCountsMap['Arrived'] ?? 0);
        $ongoingPatientsCount = (int) ($statusCountsMap['Ongoing'] ?? 0);
        $completedPatientsCount = (int) ($statusCountsMap['Completed'] ?? 0);
        $recentActivities = DB::table('activity_log')
            ->leftJoin('users', function ($join) {
                $join->on('activity_log.causer_id', '=', 'users.id')
                    ->where('activity_log.causer_type', '=', 'App\\Models\\User');
            })
            ->select(
                'activity_log.description',
                'activity_log.event',
                'activity_log.created_at',
                'users.username as causer_name'
            )
            ->latest('activity_log.created_at')
            ->limit(5)
            ->get();

        $pendingApprovalsCount = DB::table('appointments')->where('status', 'Pending')->count();
        $totalPatients = DB::table('patients')->count();
        $revenueByTreatment = DB::table('treatment_records')
            ->selectRaw('COALESCE(NULLIF(TRIM(treatment), \'\'), \'Unspecified Treatment\') as treatment_name')
            ->selectRaw('COALESCE(SUM(COALESCE(amount_charged, 0)), 0) as total_revenue')
            ->whereBetween('created_at', [$last30Start, $last30End])
            ->groupBy('treatment_name')
            ->orderByDesc('total_revenue')
            ->limit(6)
            ->get();

        $topRevenueTreatmentNames = $revenueByTreatment->pluck('treatment_name')->values()->all();
        $topRevenueTreatmentAmounts = $revenueByTreatment
            ->pluck('total_revenue')
            ->map(fn ($amount) => (float) $amount)
            ->values()
            ->all();
        $topRevenueTotal = array_sum($topRevenueTreatmentAmounts);

        // Last 7 days finance chart data
        $last7Dates = [];
        $last7Revenue = [];
        $last7Cost = [];
        $last7Profit = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = $today->copy()->subDays($i);
            $last7Dates[] = $d->format('M d');
            $last7Revenue[] = (float) (DB::table('treatment_records')
                ->whereDate('created_at', $d)
                ->selectRaw('COALESCE(SUM(COALESCE(amount_charged,0)), 0) as revenue')
                ->value('revenue') ?? 0);
            $last7Cost[] = (float) (DB::table('treatment_records')
                ->whereDate('created_at', $d)
                ->selectRaw('COALESCE(SUM(COALESCE(cost_of_treatment,0)), 0) as total_cost')
                ->value('total_cost') ?? 0);
            $last7Profit[] = (float) (DB::table('treatment_records')
                ->whereDate('created_at', $d)
                ->selectRaw('COALESCE(SUM(COALESCE(amount_charged,0) - COALESCE(cost_of_treatment,0)), 0) as profit')
                ->value('profit') ?? 0);
        }

        // Weekly comparison chart data
        $thisWeekDates = [];
        $thisWeekAppointments = [];
        $lastWeekAppointments = [];
        for ($cursor = $weekStart->copy(); $cursor->lte($weekEnd); $cursor->addDay()) {
            $thisWeekDates[] = $cursor->format('D');
            $thisWeekAppointments[] = DB::table('appointments')
                ->whereDate('appointment_date', $cursor)
                ->where('status', '!=', 'Pending')
                ->count();
            $prevCursor = $cursor->copy()->subWeek();
            $lastWeekAppointments[] = DB::table('appointments')
                ->whereDate('appointment_date', $prevCursor)
                ->where('status', '!=', 'Pending')
                ->count();
        }

        // Hourly appointments today (8 AM – 6 PM)
        $hourlyLabels = [];
        $hourlyAppointments = [];
        for ($hour = 8; $hour <= 18; $hour++) {
            $hourlyLabels[] = Carbon::createFromTime($hour, 0)->format('g A');
            $hourlyAppointments[] = DB::table('appointments')
                ->whereDate('appointment_date', $today)
                ->whereRaw('HOUR(appointment_date) = ?', [$hour])
                ->where('status', '!=', 'Pending')
                ->count();
        }

        $patientStats = $this->buildPatientStats($patientStatsRange);

        return view('dashboard', [
            'isAdminDashboard' => $isAdminDashboard,
            'isDentistDashboard' => $isDentistDashboard,
            'isStaffDashboard' => $isStaffDashboard,
            'todayAppointmentsCount' => $todayAppointmentsCount,
            'todayCompletedCount' => $todayCompletedCount,
            'todayCancelledCount' => $todayCancelledCount,
            'todayProfit' => $todayProfit,
            'todayRevenue' => $todayRevenue,
            'todayCost' => $todayCost,
            'todayProfitMargin' => $todayProfitMargin,
            'yesterdayProfit' => $yesterdayProfit,
            'todayProfitPct' => $todayProfitPct,
            'weekProfit' => $weekProfit,
            'weekProfitPct' => $weekProfitPct,
            'monthProfit' => $monthProfit,
            'monthRevenue' => $monthRevenue,
            'monthCost' => $monthCost,
            'monthProfitPct' => $monthProfitPct,
            'trendDates' => $trendDates,
            'trendAppointments' => $trendAppointments,
            'trendProfit' => $trendProfit,
            'appointmentTrendLabels' => $appointmentTrendLabels,
            'appointmentTrendComparisonDates' => $appointmentTrendComparisonDates,
            'appointmentTrendCurrentWeek' => $appointmentTrendCurrentWeek,
            'appointmentTrendPreviousWeek' => $appointmentTrendPreviousWeek,
            'statusLabels' => $statusLabels,
            'statusCounts' => $statusCounts,
            'bookedLast30' => $bookedLast30,
            'cancelledLast30' => $cancelledLast30,
            'cancellationRate' => $cancellationRate,
            'todayScheduleAppointments' => $todayScheduleAppointments,
            'pendingApprovalsCount' => $pendingApprovalsCount,
            'totalPatients' => $totalPatients,
            'waitingPatientsCount' => $waitingPatientsCount,
            'arrivedPatientsCount' => $arrivedPatientsCount,
            'ongoingPatientsCount' => $ongoingPatientsCount,
            'completedPatientsCount' => $completedPatientsCount,
            'recentActivities' => $recentActivities,
            'patientStatsTotal' => $patientStats['patientStatsTotal'],
            'patientStatsDates' => $patientStats['patientStatsDates'],
            'newPatientCounts' => $patientStats['newPatientCounts'],
            'returningPatientCounts' => $patientStats['returningPatientCounts'],
            'patientStatsRange' => $patientStats['patientStatsRange'],
            'patientStatsLabel' => $patientStats['patientStatsLabel'],
            'topRevenueTreatmentNames' => $topRevenueTreatmentNames,
            'topRevenueTreatmentAmounts' => $topRevenueTreatmentAmounts,
            'topRevenueTotal' => $topRevenueTotal,
            'cancellationRange' => $cancellationRange,
            'cancellationLabel' => $cancellationLabel,
            'range' => $range,
            'rangeLabel' => $rangeLabel,
            'last7Dates' => $last7Dates,
            'last7Revenue' => $last7Revenue,
            'last7Cost' => $last7Cost,
            'last7Profit' => $last7Profit,
            'thisWeekDates' => $thisWeekDates,
            'thisWeekAppointments' => $thisWeekAppointments,
            'lastWeekAppointments' => $lastWeekAppointments,
            'hourlyLabels' => $hourlyLabels,
            'hourlyAppointments' => $hourlyAppointments,
        ]);
    }
}
