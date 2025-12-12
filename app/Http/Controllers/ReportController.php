<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
{
    // 1. Daily Appointments
    // CHANGED: limit(7) -> limit(30) to show the last month
    $dailyData = DB::table('appointments')
        ->select(DB::raw('DATE(appointment_date) as date'), DB::raw('count(*) as total'))
        ->groupBy('date')
        ->orderBy('date', 'ASC')
        ->limit(30)  // <--- INCREASED THIS NUMBER
        ->get();

    $dates = $dailyData->pluck('date');
    $totals = $dailyData->pluck('total');

    // 2. Appointment Status (No limit needed here usually)
    $statusData = DB::table('appointments')
        ->select('status', DB::raw('count(*) as total'))
        ->groupBy('status')
        ->get();

    $statusLabels = $statusData->pluck('status');
    $statusCounts = $statusData->pluck('total');

    // 3. Top Services
    // CHANGED: limit(5) -> limit(10) to show more service types
    $serviceData = DB::table('appointments')
        ->join('services', 'appointments.service_id', '=', 'services.id')
        ->select('services.service_name', DB::raw('count(*) as total'))
        ->groupBy('services.service_name')
        ->orderByDesc('total')
        ->limit(10) // <--- INCREASED THIS NUMBER
        ->get();

    $serviceNames = $serviceData->pluck('service_name');
    $serviceCounts = $serviceData->pluck('total');

    return view('reports.index', compact(
        'dates', 
        'totals', 
        'statusLabels', 
        'statusCounts', 
        'serviceNames', 
        'serviceCounts'
    ));
}
}