<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationBell extends Component
{
    public $unreadCount = 0;
    public $notifications = [];

    public function mount()
    {
        $this->buildNotifications();
    }

    public function buildNotifications()
    {
        $now = Carbon::now();

        $user = Auth::user();
        if (!$user) {
            $this->notifications = [];
            $this->unreadCount = 0;
            return;
        }

        $notifications = collect();

        if ($user->role !== 3) {
            // Pending approvals count
            $pendingCount = DB::table('appointments')->where('status', 'Pending')->count();
            if ($pendingCount > 0) {
                $notifications->push((object) [
                    'id' => 'pending-count',
                    'title' => 'Pending Approvals',
                    'message' => "{$pendingCount} appointment request(s) waiting for approval.",
                    'created_at' => $now,
                    'status' => 'Pending',
                    'kind' => 'pending',
                    'meta' => 'Action required',
                    'is_read' => false,
                    'link' => url('/appointment'),
                ]);
            }

            // Daily summary
            $todayCount = DB::table('appointments')
                ->whereDate('appointment_date', $now->toDateString())
                ->whereNotIn('status', ['Cancelled'])
                ->count();
            $notifications->push((object) [
                'id' => 'daily-summary',
                'title' => 'Daily Summary',
                'message' => "You have {$todayCount} appointment(s) today.",
                'created_at' => $now,
                'status' => 'Info',
                'kind' => 'info',
                'meta' => 'Today',
                'is_read' => false,
                'link' => url('/appointment'),
            ]);

            // Next up reminder
            $next = DB::table('appointments')
                ->join('patients', 'appointments.patient_id', '=', 'patients.id')
                ->join('services', 'appointments.service_id', '=', 'services.id')
                ->whereDate('appointments.appointment_date', $now->toDateString())
                ->where('appointments.appointment_date', '>=', $now)
                ->whereNotIn('appointments.status', ['Cancelled', 'Completed'])
                ->orderBy('appointments.appointment_date', 'asc')
                ->select(
                    'appointments.appointment_date',
                    'patients.first_name',
                    'patients.last_name',
                    'services.service_name'
                )
                ->first();

            if ($next) {
                $nextTime = Carbon::parse($next->appointment_date)->format('h:i A');
                $notifications->push((object) [
                    'id' => 'next-up',
                    'title' => 'Next Up',
                    'message' => "{$next->last_name}, {$next->first_name} at {$nextTime} ({$next->service_name}).",
                    'created_at' => $next->appointment_date,
                    'status' => 'Scheduled',
                    'kind' => 'scheduled',
                    'meta' => "Today at {$nextTime}",
                    'appointment_at' => $next->appointment_date,
                    'is_read' => false,
                    'link' => url('/appointment'),
                ]);
            }

            // New booking notifications (last 24h)
            $newBookings = DB::table('appointments')
                ->join('patients', 'appointments.patient_id', '=', 'patients.id')
                ->join('services', 'appointments.service_id', '=', 'services.id')
                ->where('appointments.status', 'Pending')
                ->where('appointments.created_at', '>=', $now->copy()->subDay())
                ->orderBy('appointments.created_at', 'desc')
                ->select(
                    'appointments.id',
                    'appointments.created_at',
                    'patients.first_name',
                    'patients.last_name',
                    'services.service_name'
                )
                ->limit(5)
                ->get();

            foreach ($newBookings as $booking) {
                $notifications->push((object) [
                    'id' => "booking-{$booking->id}",
                    'title' => 'New Appointment Request',
                    'message' => "{$booking->last_name}, {$booking->first_name} requested {$booking->service_name}.",
                    'created_at' => $booking->created_at,
                    'status' => 'Pending',
                    'kind' => 'pending',
                    'meta' => 'Requested recently',
                    'is_read' => false,
                    'link' => url('/appointment'),
                ]);
            }
        } else {
            // Patient/User notifications
        $patientIds = collect();
        if ($user->email) {
            $patientIds = DB::table('patients')->where('email_address', $user->email)->pluck('id');
        }

            if ($patientIds->isNotEmpty()) {
                // Daily summary
                $todayCount = DB::table('appointments')
                    ->whereIn('patient_id', $patientIds)
                    ->whereDate('appointment_date', $now->toDateString())
                    ->whereNotIn('status', ['Cancelled'])
                    ->count();
                $notifications->push((object) [
                    'id' => 'daily-summary',
                    'title' => 'Daily Summary',
                    'message' => "You have {$todayCount} appointment(s) today.",
                    'created_at' => $now,
                    'status' => 'Info',
                    'kind' => 'info',
                    'meta' => 'Today',
                    'is_read' => false,
                    'link' => url('/appointment'),
                ]);

                // Next up reminder
                $next = DB::table('appointments')
                    ->join('services', 'appointments.service_id', '=', 'services.id')
                    ->whereIn('appointments.patient_id', $patientIds)
                    ->where('appointments.appointment_date', '>=', $now)
                    ->whereNotIn('appointments.status', ['Cancelled', 'Completed'])
                    ->orderBy('appointments.appointment_date', 'asc')
                    ->select('appointments.appointment_date', 'services.service_name')
                    ->first();

                if ($next) {
                    $nextTime = Carbon::parse($next->appointment_date)->format('M d, Y h:i A');
                    $notifications->push((object) [
                        'id' => 'next-up',
                        'title' => 'Next Appointment',
                        'message' => "{$next->service_name} on {$nextTime}.",
                        'created_at' => $next->appointment_date,
                        'status' => 'Scheduled',
                        'kind' => 'scheduled',
                        'meta' => $nextTime,
                        'appointment_at' => $next->appointment_date,
                        'is_read' => false,
                        'link' => url('/appointment'),
                    ]);
                }

                // Status changes (last 24h)
                $statusUpdates = DB::table('appointments')
                    ->join('services', 'appointments.service_id', '=', 'services.id')
                    ->whereIn('appointments.patient_id', $patientIds)
                    ->where('appointments.updated_at', '>=', $now->copy()->subDay())
                    ->whereIn('appointments.status', ['Scheduled', 'Cancelled'])
                    ->orderBy('appointments.updated_at', 'desc')
                    ->select(
                        'appointments.id',
                        'appointments.appointment_date',
                        'appointments.status',
                        'appointments.updated_at',
                        'services.service_name'
                    )
                    ->limit(5)
                    ->get();

                foreach ($statusUpdates as $update) {
                    $dateTime = Carbon::parse($update->appointment_date)->format('M d, Y h:i A');
                    $notifications->push((object) [
                        'id' => "status-{$update->id}",
                        'title' => 'Appointment Status Updated',
                        'message' => "Your {$update->service_name} on {$dateTime} is now {$update->status}.",
                        'created_at' => $update->updated_at,
                        'status' => $update->status,
                        'kind' => 'status',
                        'meta' => "Status: {$update->status}",
                        'appointment_at' => $update->appointment_date,
                        'is_read' => false,
                        'link' => url('/appointment'),
                    ]);
                }
            }
        }

        $this->notifications = $notifications;

        $this->unreadCount = $this->notifications->count();
    }

    public function render()
    {
        return view('livewire.components.notification-bell');
    }
}
