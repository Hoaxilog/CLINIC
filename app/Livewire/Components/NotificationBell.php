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
        $readIds = collect(session($this->readSessionKey($user->id), []));
        $clearedIds = collect(session($this->clearedSessionKey($user->id), []));

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

            $recentPatientCancellations = DB::table('activity_log')
                ->leftJoin('users', function ($join) {
                    $join->on('activity_log.causer_id', '=', 'users.id')
                        ->where('activity_log.causer_type', '=', 'App\\Models\\User');
                })
                ->where('activity_log.event', 'appointment_cancelled_by_patient')
                ->where('activity_log.created_at', '>=', $now->copy()->subDay())
                ->orderByDesc('activity_log.created_at')
                ->select(
                    'activity_log.id',
                    'activity_log.created_at',
                    DB::raw("COALESCE(users.username, users.email, 'Patient') as patient_name")
                )
                ->limit(5)
                ->get();

            foreach ($recentPatientCancellations as $cancellation) {
                $notifications->push((object) [
                    'id' => "patient-cancel-{$cancellation->id}",
                    'title' => 'Patient Cancelled Schedule',
                    'message' => "{$cancellation->patient_name} cancelled a scheduled appointment.",
                    'created_at' => $cancellation->created_at,
                    'status' => 'Cancelled',
                    'kind' => 'status',
                    'meta' => 'Needs review',
                    'is_read' => false,
                    'link' => url('/appointment'),
                ]);
            }
        } else {
            // Patient account notifications are intentionally decoupled
            // from medical records and patient rows.
        }

        $notifications = $notifications
            ->sortByDesc(fn ($notification) => Carbon::parse($notification->created_at)->timestamp)
            ->values();

        if ($clearedIds->isNotEmpty()) {
            $notifications = $notifications
                ->reject(fn ($notification) => $clearedIds->contains($notification->id))
                ->values();
        }

        $notifications = $notifications
            ->map(function ($notification) use ($readIds) {
                $notification->is_read = $readIds->contains($notification->id);
                return $notification;
            })
            ->values();

        $this->notifications = $notifications->all();
        $this->unreadCount = $notifications->where('is_read', false)->count();
    }

    public function render()
    {
        return view('livewire.components.notification-bell');
    }

    public function markAsRead(string $notificationId): void
    {
        if ($notificationId === '') {
            return;
        }

        $user = Auth::user();
        if (!$user) {
            return;
        }

        $readIds = collect(session($this->readSessionKey($user->id), []));
        if (!$readIds->contains($notificationId)) {
            $readIds->push($notificationId);
            session([$this->readSessionKey($user->id) => $readIds->values()->all()]);
        }

        $this->buildNotifications();
    }

    public function markAllAsRead(): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $currentIds = collect($this->notifications)->pluck('id')->filter()->unique()->values();
        $readIds = collect(session($this->readSessionKey($user->id), []))
            ->merge($currentIds)
            ->unique()
            ->values();
        session([$this->readSessionKey($user->id) => $readIds->all()]);

        $this->buildNotifications();
    }

    public function clearNotification(string $notificationId): void
    {
        if ($notificationId === '') {
            return;
        }

        $user = Auth::user();
        if (!$user) {
            return;
        }

        $clearedIds = collect(session($this->clearedSessionKey($user->id), []));
        if (!$clearedIds->contains($notificationId)) {
            $clearedIds->push($notificationId);
            session([$this->clearedSessionKey($user->id) => $clearedIds->values()->all()]);
        }

        $this->markAsRead($notificationId);
    }

    public function clearAllNotifications(): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $currentIds = collect($this->notifications)->pluck('id')->filter()->unique()->values();

        $clearedIds = collect(session($this->clearedSessionKey($user->id), []))
            ->merge($currentIds)
            ->unique()
            ->values();

        $readIds = collect(session($this->readSessionKey($user->id), []))
            ->merge($currentIds)
            ->unique()
            ->values();

        session([
            $this->clearedSessionKey($user->id) => $clearedIds->all(),
            $this->readSessionKey($user->id) => $readIds->all(),
        ]);

        $this->buildNotifications();
    }

    public function openNotification(string $notificationId, string $link)
    {
        $this->markAsRead($notificationId);
        return $this->redirect($link);
    }

    protected function readSessionKey(int $userId): string
    {
        return "notification_bell.user.{$userId}.read_ids";
    }

    protected function clearedSessionKey(int $userId): string
    {
        return "notification_bell.user.{$userId}.cleared_ids";
    }
}
