<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Dashboard extends Controller
{
    public function index() {
        $today = Carbon::today();

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

        $weeklyCompletedCount = DB::table('appointments')
            ->where('status', 'Completed')
            ->whereDate('appointment_date', '>=', Carbon::today()->subDays(6))
            ->count();

        return view('dashboard', [
            'todayAppointmentsCount' => $todayAppointmentsCount,
            'todayCompletedCount'    => $todayCompletedCount,
            'todayCancelledCount'    => $todayCancelledCount,
            'todayUpcomingCount'     => max(0, $todayUpcomingCount),
            'weeklyCompletedCount'   => $weeklyCompletedCount,
        ]);
    }

}
