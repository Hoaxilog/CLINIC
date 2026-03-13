<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ReportController extends Controller
{
    private function resolveRange(Request $request): array
    {
        $range = $request->query('range', 'month');
        $allowed = ['today', 'week', 'month', 'year', 'custom'];
        if (!in_array($range, $allowed, true)) {
            $range = 'month';
        }

        $now = Carbon::now();
        switch ($range) {
            case 'today':
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                $rangeLabel = 'Today';
                break;
            case 'week':
                $startDate = $now->copy()->startOfWeek()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                $rangeLabel = 'This Week';
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
            case 'custom':
                $fromDate = $request->query('from_date');
                $toDate = $request->query('to_date');
                if ($fromDate && $toDate) {
                    $startDate = Carbon::parse($fromDate)->startOfDay();
                    $endDate = Carbon::parse($toDate)->endOfDay();
                    if ($startDate->gt($endDate)) {
                        [$startDate, $endDate] = [$endDate->copy()->startOfDay(), $startDate->copy()->endOfDay()];
                    }
                } else {
                    $startDate = $now->copy()->startOfMonth();
                    $endDate = $now->copy()->endOfDay();
                }
                $rangeLabel = 'Custom Range: ' . $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y');
                break;
            default:
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfDay();
                $rangeLabel = 'This Month';
                break;
        }

        $fromDate = $request->query('from_date') ?: $startDate->toDateString();
        $toDate = $request->query('to_date') ?: $endDate->toDateString();

        return [$range, $startDate, $endDate, $rangeLabel, $fromDate, $toDate];
    }

    public function index(Request $request)
    {
        [$range, $startDate, $endDate, $rangeLabel, $fromDate, $toDate] = $this->resolveRange($request);
        $section = $request->query('section', 'overview');
        if (!in_array($section, ['overview', 'patients', 'appointments', 'printable'], true)) {
            $section = 'overview';
        }

        $totalPatients = (int) DB::table('patients')->count();

        $driver = DB::getDriverName();
        $groupByMonth = $range === 'year' || $startDate->diffInDays($endDate) > 120;
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

        $appointmentTrendRaw = DB::table('appointments')
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
                $totals[] = (int) ($appointmentTrendRaw[$key] ?? 0);
            }
        } else {
            $period = CarbonPeriod::create($startDate, '1 day', $endDate);
            foreach ($period as $date) {
                $key = $date->format('Y-m-d');
                $dates[] = $date->format('M d');
                $totals[] = (int) ($appointmentTrendRaw[$key] ?? 0);
            }
        }

        $statusData = DB::table('appointments')
            ->select('status', DB::raw('count(*) as total'))
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->groupBy('status')
            ->get();

        $statusOrder = ['Scheduled', 'Arrived', 'Ongoing', 'Completed', 'Cancelled'];
        $statusLabels = collect($statusOrder);
        $statusCounts = $statusLabels
            ->map(fn ($status) => (int) ($statusData->firstWhere('status', $status)->total ?? 0))
            ->values();

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
        $completedCount = (int) ($statusData->firstWhere('status', 'Completed')->total ?? 0);
        $cancelledCount = (int) ($statusData->firstWhere('status', 'Cancelled')->total ?? 0);
        $completionRate = $totalAppointments > 0 ? round(($completedCount / $totalAppointments) * 100) : 0;
        $topServiceName = $serviceNames->first() ?? 'N/A';

        $walkInCount = (int) DB::table('activity_log')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where(function ($query) {
                $query->whereRaw('LOWER(event) LIKE ?', ['%walk%'])
                    ->orWhereRaw('LOWER(description) LIKE ?', ['%walk%'])
                    ->orWhereRaw('LOWER(log_name) LIKE ?', ['%walk%']);
            })
            ->count();
        $scheduledCount = max($totalAppointments - $walkInCount, 0);

        $genderData = DB::table('patients')
            ->select('gender', DB::raw('count(*) as total'))
            ->groupBy('gender')
            ->get();
        $genderLabels = collect(['Male', 'Female', 'Other', 'Unspecified']);
        $genderCounts = $genderLabels
            ->map(fn ($gender) => $gender === 'Unspecified'
                ? (int) ($genderData->firstWhere('gender', null)->total ?? 0)
                : (int) ($genderData->firstWhere('gender', $gender)->total ?? 0))
            ->values();

        $ageGroupLabels = collect(['0-12', '13-17', '18-29', '30-44', '45-59', '60+']);
        $ageGroupCounts = [0, 0, 0, 0, 0, 0];
        $birthDates = DB::table('patients')
            ->whereNotNull('birth_date')
            ->pluck('birth_date');
        foreach ($birthDates as $birthDate) {
            $age = Carbon::parse($birthDate)->age;
            if ($age <= 12) {
                $ageGroupCounts[0]++;
            } elseif ($age <= 17) {
                $ageGroupCounts[1]++;
            } elseif ($age <= 29) {
                $ageGroupCounts[2]++;
            } elseif ($age <= 44) {
                $ageGroupCounts[3]++;
            } elseif ($age <= 59) {
                $ageGroupCounts[4]++;
            } else {
                $ageGroupCounts[5]++;
            }
        }

        $patientRegistrationRaw = DB::table('patients')
            ->select(DB::raw(match ($driver) {
                'pgsql' => "TO_CHAR(created_at, 'YYYY-MM')",
                'sqlite' => "strftime('%Y-%m', created_at)",
                'sqlsrv' => "FORMAT(created_at, 'yyyy-MM')",
                default => "DATE_FORMAT(created_at, '%Y-%m')",
            } . ' as bucket'), DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->pluck('total', 'bucket');

        $patientRegMonths = [];
        $patientRegCounts = [];
        $regPeriod = CarbonPeriod::create($startDate->copy()->startOfMonth(), '1 month', $endDate);
        foreach ($regPeriod as $month) {
            $key = $month->format('Y-m');
            $patientRegMonths[] = $month->format('M Y');
            $patientRegCounts[] = (int) ($patientRegistrationRaw[$key] ?? 0);
        }

        $patientGender = $request->query('patient_gender');
        $patientRegFrom = $request->query('patient_from') ?: $startDate->toDateString();
        $patientRegTo = $request->query('patient_to') ?: $endDate->toDateString();
        $patientRows = DB::table('patients')
            ->select('id', 'first_name', 'middle_name', 'last_name', 'gender', 'birth_date', 'mobile_number', 'created_at')
            ->when($patientGender, fn ($query) => $query->where('gender', $patientGender))
            ->whereBetween('created_at', [
                Carbon::parse($patientRegFrom)->startOfDay(),
                Carbon::parse($patientRegTo)->endOfDay(),
            ])
            ->orderByDesc('created_at')
            ->limit(25)
            ->get();

        $appointmentFrom = $request->query('appointment_from') ?: $startDate->toDateString();
        $appointmentTo = $request->query('appointment_to') ?: $endDate->toDateString();
        $appointmentStatus = $request->query('appointment_status');
        $appointmentService = $request->query('appointment_service');

        $appointmentBase = DB::table('appointments')
            ->leftJoin('patients', 'appointments.patient_id', '=', 'patients.id')
            ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
            ->select(
                'appointments.id',
                'appointments.appointment_date',
                'appointments.status',
                'services.service_name',
                'patients.first_name',
                'patients.middle_name',
                'patients.last_name'
            )
            ->whereBetween('appointments.appointment_date', [
                Carbon::parse($appointmentFrom)->startOfDay(),
                Carbon::parse($appointmentTo)->endOfDay(),
            ]);

        $appointmentRows = (clone $appointmentBase)
            ->when($appointmentStatus, fn ($query) => $query->where('appointments.status', $appointmentStatus))
            ->when($appointmentService, fn ($query) => $query->where('appointments.service_id', $appointmentService))
            ->orderByDesc('appointments.appointment_date')
            ->limit(25)
            ->get();

        $completedRows = (clone $appointmentBase)
            ->where('appointments.status', 'Completed')
            ->orderByDesc('appointments.appointment_date')
            ->limit(25)
            ->get();

        $cancelledRows = (clone $appointmentBase)
            ->where('appointments.status', 'Cancelled')
            ->orderByDesc('appointments.appointment_date')
            ->limit(25)
            ->get();

        $serviceOptions = DB::table('services')
            ->orderBy('service_name')
            ->pluck('service_name', 'id');

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
            'totalPatients',
            'totalAppointments',
            'completedCount',
            'cancelledCount',
            'completionRate',
            'topServiceName',
            'walkInCount',
            'scheduledCount',
            'genderLabels',
            'genderCounts',
            'ageGroupLabels',
            'ageGroupCounts',
            'patientRegMonths',
            'patientRegCounts',
            'patientRows',
            'appointmentRows',
            'completedRows',
            'cancelledRows',
            'serviceOptions',
            'patientGender',
            'patientRegFrom',
            'patientRegTo',
            'appointmentFrom',
            'appointmentTo',
            'appointmentStatus',
            'appointmentService',
            'completionBadge',
            'range',
            'rangeLabel',
            'fromDate',
            'toDate',
            'section'
        ));
    }

    public function print(Request $request, string $reportType)
    {
        [$range, $startDate, $endDate, $rangeLabel, $fromDate, $toDate] = $this->resolveRange($request);

        $allowedTypes = ['patients', 'appointments', 'completed', 'cancelled', 'services', 'monthly-summary'];
        abort_unless(in_array($reportType, $allowedTypes, true), 404);

        $statusData = DB::table('appointments')
            ->select('status', DB::raw('count(*) as total'))
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->groupBy('status')
            ->get();

        $totalPatients = (int) DB::table('patients')->count();
        $totalAppointments = (int) DB::table('appointments')
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->count();
        $completedCount = (int) ($statusData->firstWhere('status', 'Completed')->total ?? 0);
        $cancelledCount = (int) ($statusData->firstWhere('status', 'Cancelled')->total ?? 0);

        $reportTitle = '';
        $columns = [];
        $rows = collect();

        if ($reportType === 'patients') {
            $patientGender = $request->query('patient_gender');
            $patientRegFrom = $request->query('patient_from') ?: $startDate->toDateString();
            $patientRegTo = $request->query('patient_to') ?: $endDate->toDateString();

            $rows = DB::table('patients')
                ->select('id', 'first_name', 'middle_name', 'last_name', 'gender', 'birth_date', 'mobile_number', 'created_at')
                ->when($patientGender, fn ($query) => $query->where('gender', $patientGender))
                ->whereBetween('created_at', [
                    Carbon::parse($patientRegFrom)->startOfDay(),
                    Carbon::parse($patientRegTo)->endOfDay(),
                ])
                ->orderByDesc('created_at')
                ->get();

            $reportTitle = 'Patient Registration Report';
            $columns = ['Patient ID', 'Full Name', 'Gender', 'Birth Date', 'Mobile Number', 'Date Registered'];
        } elseif ($reportType === 'appointments' || $reportType === 'completed' || $reportType === 'cancelled') {
            $appointmentFrom = $request->query('appointment_from') ?: $startDate->toDateString();
            $appointmentTo = $request->query('appointment_to') ?: $endDate->toDateString();
            $appointmentStatus = $request->query('appointment_status');
            $appointmentService = $request->query('appointment_service');

            $appointmentBase = DB::table('appointments')
                ->leftJoin('patients', 'appointments.patient_id', '=', 'patients.id')
                ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
                ->select(
                    'appointments.id',
                    'appointments.appointment_date',
                    'appointments.status',
                    'services.service_name',
                    'patients.first_name',
                    'patients.middle_name',
                    'patients.last_name'
                )
                ->whereBetween('appointments.appointment_date', [
                    Carbon::parse($appointmentFrom)->startOfDay(),
                    Carbon::parse($appointmentTo)->endOfDay(),
                ]);

            if ($reportType === 'appointments') {
                $rows = (clone $appointmentBase)
                    ->when($appointmentStatus, fn ($query) => $query->where('appointments.status', $appointmentStatus))
                    ->when($appointmentService, fn ($query) => $query->where('appointments.service_id', $appointmentService))
                    ->orderByDesc('appointments.appointment_date')
                    ->get();
                $reportTitle = 'Appointment Report';
            } elseif ($reportType === 'completed') {
                $rows = (clone $appointmentBase)
                    ->where('appointments.status', 'Completed')
                    ->orderByDesc('appointments.appointment_date')
                    ->get();
                $reportTitle = 'Completed Treatments Report';
            } else {
                $rows = (clone $appointmentBase)
                    ->where('appointments.status', 'Cancelled')
                    ->orderByDesc('appointments.appointment_date')
                    ->get();
                $reportTitle = 'Cancelled Appointment Report';
            }

            $columns = ['Appointment ID', 'Patient Name', 'Service', 'Appointment Date', 'Status'];
        } elseif ($reportType === 'services') {
            $rows = DB::table('appointments')
                ->join('services', 'appointments.service_id', '=', 'services.id')
                ->select('services.service_name', DB::raw('count(*) as total'))
                ->whereBetween('appointment_date', [$startDate, $endDate])
                ->groupBy('services.service_name')
                ->orderByDesc('total')
                ->get();

            $reportTitle = 'Service Utilization Summary';
            $columns = ['Service', 'Appointment Count'];
        } else {
            $reportTitle = 'Monthly Clinic Summary';
            $columns = ['Metric', 'Value'];
            $rows = collect([
                (object) ['metric' => 'Total Patients', 'value' => number_format($totalPatients)],
                (object) ['metric' => 'Total Appointments', 'value' => number_format($totalAppointments)],
                (object) ['metric' => 'Completed Appointments', 'value' => number_format($completedCount)],
                (object) ['metric' => 'Cancelled Appointments', 'value' => number_format($cancelledCount)],
                (object) ['metric' => 'Date Range', 'value' => Carbon::parse($fromDate)->format('M d, Y') . ' - ' . Carbon::parse($toDate)->format('M d, Y')],
            ]);
        }

        return view('reports.print.report', [
            'reportType' => $reportType,
            'reportTitle' => $reportTitle,
            'columns' => $columns,
            'rows' => $rows,
            'range' => $range,
            'rangeLabel' => $rangeLabel,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'generatedAt' => now(),
            'generatedBy' => auth()->user()?->name ?? 'Admin',
            'summary' => [
                'totalPatients' => $totalPatients,
                'totalAppointments' => $totalAppointments,
                'completedCount' => $completedCount,
                'cancelledCount' => $cancelledCount,
            ],
        ]);
    }
}
