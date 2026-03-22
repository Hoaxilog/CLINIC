<?php

namespace App\Livewire\Shared;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class NotificationBell extends Component
{
    protected const DB_NOTIFICATION_PREFIX = 'db-notification:';

    public int $unreadCount = 0;

    /** @var array<int, object> */
    public array $notifications = [];

    public function mount(): void
    {
        $this->buildNotifications();
    }

    public function buildNotifications(): void
    {
        $now = Carbon::now();
        $user = Auth::user();

        if (! $user) {
            $this->notifications = [];
            $this->unreadCount = 0;

            return;
        }

        $notifications = collect();
        $readIds = collect(session($this->readSessionKey($user->id), []));
        $clearedIds = collect(session($this->clearedSessionKey($user->id), []));

        if ($user->role !== 3) {
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
                    'link' => route('appointment.requests'),
                ]);
            }

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
                'link' => route('appointment.calendar'),
            ]);

            $next = DB::table('appointments')
                ->join('patients', 'appointments.patient_id', '=', 'patients.id')
                ->join('services', 'appointments.service_id', '=', 'services.id')
                ->whereDate('appointments.appointment_date', $now->toDateString())
                ->where('appointments.appointment_date', '>=', $now)
                ->whereNotIn('appointments.status', ['Cancelled', 'Completed'])
                ->orderBy('appointments.appointment_date', 'asc')
                ->select(
                    'appointments.id',
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
                    'link' => route('appointment.calendar', ['appointment' => $next->id]),
                ]);
            }

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
                    'link' => route('appointment.requests', ['appointment' => $booking->id]),
                ]);
            }

            foreach ($this->databaseNotificationsForUser($user->id) as $notification) {
                $notifications->push($notification);
            }
        } else {
            foreach ($this->databaseNotificationsForUser($user->id) as $notification) {
                $notifications->push($notification);
            }

            $recentAppointmentUpdates = DB::table('appointments')
                ->leftJoin('services', 'appointments.service_id', '=', 'services.id')
                ->where(function ($query) use ($user) {
                    $query->where('appointments.requester_user_id', $user->id);

                    if (! empty($user->email)) {
                        $query->orWhereRaw('LOWER(appointments.requester_email) = ?', [strtolower((string) $user->email)]);
                    }
                })
                ->select(
                    'appointments.id',
                    'appointments.status',
                    'appointments.appointment_date',
                    'appointments.updated_at',
                    'services.service_name'
                )
                ->orderByDesc('appointments.updated_at')
                ->limit(5)
                ->get();

            foreach ($recentAppointmentUpdates as $appointment) {
                $status = (string) ($appointment->status ?? 'Updated');
                $meta = Carbon::parse($appointment->appointment_date)->format('M d, h:i A');
                $notifications->push((object) [
                    'id' => 'patient-appt-'.$appointment->id.'-'.$status,
                    'title' => $appointment->service_name ?? 'Appointment update',
                    'message' => "Status: {$status}",
                    'created_at' => $appointment->updated_at ?? $appointment->appointment_date,
                    'status' => $status,
                    'kind' => in_array($status, ['Scheduled', 'Waiting'], true) ? 'scheduled' : 'status',
                    'meta' => $meta,
                    'is_read' => false,
                    'link' => route('patient.dashboard'),
                ]);
            }
        }

        $notifications = $notifications
            ->sortByDesc(fn ($notification) => Carbon::parse($notification->created_at)->timestamp)
            ->values();

        if ($clearedIds->isNotEmpty()) {
            $notifications = $notifications
                ->reject(fn ($notification) => ! $this->isDatabaseNotificationId((string) $notification->id) && $clearedIds->contains($notification->id))
                ->values();
        }

        $notifications = $notifications
            ->map(function ($notification) use ($readIds) {
                if (! $this->isDatabaseNotificationId((string) $notification->id)) {
                    $notification->is_read = $readIds->contains($notification->id);
                }

                return $notification;
            })
            ->values();

        $this->notifications = $notifications->all();
        $this->unreadCount = $notifications->where('is_read', false)->count();
    }

    public function render()
    {
        return view('livewire.shared.notification-bell');
    }

    public function markAsRead(string $notificationId): void
    {
        if ($notificationId === '') {
            return;
        }

        $user = Auth::user();
        if (! $user) {
            return;
        }

        if ($this->isDatabaseNotificationId($notificationId) && $this->staffNotificationsTableAvailable()) {
            $staffNotificationId = $this->extractDatabaseNotificationId($notificationId);

            if ($staffNotificationId !== null) {
                DB::table('notifications')
                    ->where('id', $staffNotificationId)
                    ->where('user_id', $user->id)
                    ->update([
                        'read_at' => now(),
                        'updated_at' => now(),
                    ]);
            }
        } else {
            $readIds = collect(session($this->readSessionKey($user->id), []));
            if (! $readIds->contains($notificationId)) {
                $readIds->push($notificationId);
                session([$this->readSessionKey($user->id) => $readIds->values()->all()]);
            }
        }

        $this->buildNotifications();
    }

    public function markAllAsRead(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        if ($this->staffNotificationsTableAvailable()) {
            DB::table('notifications')
                ->where('user_id', $user->id)
                ->whereNull('cleared_at')
                ->whereNull('read_at')
                ->update([
                    'read_at' => now(),
                    'updated_at' => now(),
                ]);
        }

        $currentIds = collect($this->notifications)
            ->pluck('id')
            ->filter()
            ->reject(fn ($id) => $this->isDatabaseNotificationId((string) $id))
            ->unique()
            ->values();

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
        if (! $user) {
            return;
        }

        if ($this->isDatabaseNotificationId($notificationId) && $this->staffNotificationsTableAvailable()) {
            $staffNotificationId = $this->extractDatabaseNotificationId($notificationId);

            if ($staffNotificationId !== null) {
                DB::table('notifications')
                    ->where('id', $staffNotificationId)
                    ->where('user_id', $user->id)
                    ->update([
                        'read_at' => now(),
                        'cleared_at' => now(),
                        'updated_at' => now(),
                    ]);
            }

            $this->buildNotifications();

            return;
        }

        $clearedIds = collect(session($this->clearedSessionKey($user->id), []));
        if (! $clearedIds->contains($notificationId)) {
            $clearedIds->push($notificationId);
            session([$this->clearedSessionKey($user->id) => $clearedIds->values()->all()]);
        }

        $this->markAsRead($notificationId);
    }

    public function clearAllNotifications(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        if ($this->staffNotificationsTableAvailable()) {
            DB::table('notifications')
                ->where('user_id', $user->id)
                ->whereNull('cleared_at')
                ->update([
                    'read_at' => now(),
                    'cleared_at' => now(),
                    'updated_at' => now(),
                ]);
        }

        $currentIds = collect($this->notifications)
            ->pluck('id')
            ->filter()
            ->reject(fn ($id) => $this->isDatabaseNotificationId((string) $id))
            ->unique()
            ->values();

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

    protected function databaseNotificationId(int $notificationId): string
    {
        return self::DB_NOTIFICATION_PREFIX.$notificationId;
    }

    protected function isDatabaseNotificationId(string $notificationId): bool
    {
        return str_starts_with($notificationId, self::DB_NOTIFICATION_PREFIX);
    }

    protected function extractDatabaseNotificationId(string $notificationId): ?int
    {
        if (! $this->isDatabaseNotificationId($notificationId)) {
            return null;
        }

        $id = (int) str_replace(self::DB_NOTIFICATION_PREFIX, '', $notificationId);

        return $id > 0 ? $id : null;
    }

    protected function databaseNotificationsForUser(int $userId)
    {
        if (! $this->staffNotificationsTableAvailable()) {
            return collect();
        }

        return DB::table('notifications')
            ->where('user_id', $userId)
            ->whereNull('cleared_at')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                return (object) [
                    'id' => $this->databaseNotificationId((int) $notification->id),
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'created_at' => $notification->created_at,
                    'status' => $this->databaseNotificationStatus($notification->type),
                    'kind' => $this->databaseNotificationKind($notification->type),
                    'meta' => $this->databaseNotificationMeta($notification->type),
                    'is_read' => ! empty($notification->read_at),
                    'link' => $notification->link ?: route('appointment.calendar'),
                ];
            });
    }

    protected function databaseNotificationStatus(string $type): string
    {
        return match ($type) {
            'patient_cancelled_request' => 'Cancelled',
            'patient_rescheduled_request' => 'Updated',
            'patient_appointment_reminder_day_before', 'patient_appointment_reminder_day_of' => 'Reminder',
            default => 'Updated',
        };
    }

    protected function databaseNotificationKind(string $type): string
    {
        return match ($type) {
            'patient_appointment_reminder_day_before', 'patient_appointment_reminder_day_of' => 'scheduled',
            'patient_cancelled_request', 'patient_rescheduled_request' => 'status',
            default => 'info',
        };
    }

    protected function databaseNotificationMeta(string $type): string
    {
        return match ($type) {
            'patient_appointment_reminder_day_before' => '1 day before',
            'patient_appointment_reminder_day_of' => 'Today',
            'patient_cancelled_request', 'patient_rescheduled_request' => 'Needs review',
            default => 'Update',
        };
    }

    protected function staffNotificationsTableAvailable(): bool
    {
        return Schema::hasTable('notifications');
    }
}
