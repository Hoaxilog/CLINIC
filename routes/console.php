<?php

use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('notifications:prune', function () {
    if (! Schema::hasTable('notifications')) {
        $this->info('Notifications table does not exist yet.');

        return;
    }

    // This removes rows older than 30 days while leaving activity_log untouched.
    $cutoff = now()->subDays(30);
    $deleted = DB::table('notifications')
        ->where('created_at', '<', $cutoff)
        ->delete();

    if ($deleted === 0) {
        $this->info('No staff notifications to prune.');

        return;
    }

    $this->info("Pruned {$deleted} notifications older than 30 days.");
})->purpose('Delete notifications older than 30 days');

Artisan::command('patients:send-appointment-reminders {window}', function (string $window) {
    if (! Schema::hasTable('notifications') || ! Schema::hasTable('appointments')) {
        $this->info('Required tables are not available yet.');

        return;
    }

    $normalizedWindow = strtolower(trim($window));
    $supportedWindows = ['day-before', 'day-of'];

    if (! in_array($normalizedWindow, $supportedWindows, true)) {
        $this->error('Supported windows: day-before, day-of');

        return;
    }

    $targetDate = $normalizedWindow === 'day-before'
        ? now()->addDay()->toDateString()
        : now()->toDateString();

    $notificationType = $normalizedWindow === 'day-before'
        ? 'patient_appointment_reminder_day_before'
        : 'patient_appointment_reminder_day_of';

    $metaLabel = $normalizedWindow === 'day-before' ? 'Tomorrow' : 'Today';

    $appointments = DB::table('appointments')
        ->join('services', 'appointments.service_id', '=', 'services.id')
        ->join('users', 'users.id', '=', 'appointments.requester_user_id')
        ->whereDate('appointments.appointment_date', $targetDate)
        ->whereIn('appointments.status', ['Scheduled', 'Waiting'])
        ->whereNotNull('appointments.requester_user_id')
        ->select(
            'appointments.id',
            'appointments.appointment_date',
            'appointments.status',
            'appointments.requester_email',
            'appointments.requester_first_name',
            'appointments.requester_last_name',
            'appointments.requester_user_id',
            'services.service_name',
            'users.id as user_id',
            'users.email as user_email',
            'users.username'
        )
        ->get();

    if ($appointments->isEmpty()) {
        $this->info("No {$normalizedWindow} reminders to send.");

        return;
    }

    $createdCount = 0;
    $emailedCount = 0;

    foreach ($appointments as $appointment) {
        $alreadySentToday = DB::table('notifications')
            ->where('user_id', $appointment->user_id)
            ->where('appointment_id', $appointment->id)
            ->where('type', $notificationType)
            ->whereDate('created_at', now()->toDateString())
            ->exists();

        if ($alreadySentToday) {
            continue;
        }

        $appointmentAt = Carbon::parse($appointment->appointment_date);
        $displayName = trim(
            (string) ($appointment->requester_first_name ?? '').' '.
            (string) ($appointment->requester_last_name ?? '')
        );

        if ($displayName === '') {
            $displayName = (string) ($appointment->username ?: $appointment->user_email ?: 'Patient');
        }

        $title = $normalizedWindow === 'day-before'
            ? 'Appointment Reminder for Tomorrow'
            : 'Appointment Reminder for Today';

        $message = sprintf(
            '%s, this is a reminder for your %s appointment on %s at %s.',
            $displayName,
            $appointment->service_name ?? 'scheduled',
            $appointmentAt->format('F d, Y'),
            $appointmentAt->format('h:i A')
        );

        DB::table('notifications')->insert([
            'user_id' => $appointment->user_id,
            'type' => $notificationType,
            'appointment_id' => $appointment->id,
            'actor_user_id' => null,
            'title' => $title,
            'message' => $message,
            'link' => route('patient.dashboard'),
            'read_at' => null,
            'cleared_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $createdCount++;

        $recipientEmail = trim((string) ($appointment->requester_email ?: $appointment->user_email));

        if ($recipientEmail !== '') {
            try {
                Mail::send('appointment.emails.patient-reminder', [
                    'patientName' => $displayName,
                    'serviceName' => $appointment->service_name ?? 'Appointment',
                    'appointmentAt' => $appointmentAt,
                    'status' => $appointment->status,
                    'metaLabel' => $metaLabel,
                ], function ($mailMessage) use ($recipientEmail, $title) {
                    $mailMessage->to($recipientEmail);
                    $mailMessage->subject($title);
                });

                $emailedCount++;
            } catch (Throwable $exception) {
                report($exception);
            }
        }
    }

    $this->info("Created {$createdCount} reminder notification(s) and sent {$emailedCount} email reminder(s).");
})->purpose('Send patient appointment reminders and emails');

Schedule::command('notifications:prune')->daily();
Schedule::command('patients:send-appointment-reminders day-before')->dailyAt('08:00');
Schedule::command('patients:send-appointment-reminders day-of')->dailyAt('07:00');
