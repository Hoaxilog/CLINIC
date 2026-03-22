<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CalendarQueryService
{
    // Statuses that count against slot capacity
    public const APPROVED_STATUSES = ['Scheduled', 'Waiting', 'Ongoing'];

    /**
     * Fetch display appointments for the given week (excludes Cancelled + Pending).
     */
    public function weekAppointments(Carbon $startOfWeek, Carbon $endOfWeek): \Illuminate\Support\Collection
    {
        $select = $this->buildCalendarSelect();

        return DB::table('appointments')
            ->leftJoin('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->whereBetween('appointment_date', [$startOfWeek, $endOfWeek])
            ->where('appointments.status', '!=', 'Cancelled')
            ->where('appointments.status', '!=', 'Pending')
            ->select($select)
            ->get();
    }

    /**
     * Fetch the approved appointments for slot-capacity counting.
     */
    public function weekOccupied(Carbon $startOfWeek, Carbon $endOfWeek): \Illuminate\Support\Collection
    {
        return DB::table('appointments')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->whereBetween('appointment_date', [$startOfWeek, $endOfWeek])
            ->whereIn('appointments.status', self::APPROVED_STATUSES)
            ->select('appointments.appointment_date', 'services.duration')
            ->get();
    }

    /**
     * Fetch pending approvals for the pending tab.
     */
    public function pendingAppointments(?string $filterDate): \Illuminate\Support\Collection
    {
        return DB::table('appointments')
            ->leftJoin('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->where('appointments.status', 'Pending')
            ->when($filterDate, fn ($q) => $q->whereDate('appointments.appointment_date', $filterDate))
            ->orderBy('appointments.appointment_date', 'asc')
            ->select([
                'appointments.id',
                'appointments.patient_id',
                'appointments.appointment_date',
                'appointments.status',
                'appointments.booking_for_other',
                'appointments.requester_first_name',
                'appointments.requester_last_name',
                'appointments.requester_contact_number',
                'appointments.requester_email',
                'appointments.requester_relationship_to_patient',
                'appointments.requester_birth_date',
                'appointments.requested_patient_first_name',
                'appointments.requested_patient_last_name',
                'appointments.requested_patient_birth_date',
                DB::raw($this->firstNameExpr().' as first_name'),
                DB::raw($this->lastNameExpr().' as last_name'),
                DB::raw('COALESCE(patients.mobile_number, appointments.requester_contact_number) as mobile_number'),
                DB::raw('COALESCE(patients.email_address, appointments.requester_email) as email_address'),
                'services.service_name',
            ])
            ->get();
    }

    /**
     * Search patients by name or number.
     */
    public function searchPatients(string $query, int $limit = 10): \Illuminate\Support\Collection
    {
        return DB::table('patients')
            ->select('id', 'first_name', 'last_name', 'middle_name', 'mobile_number', 'birth_date')
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('mobile_number', 'like', "%{$query}%");
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->limit($limit)
            ->get();
    }

    /**
     * Count slot conflicts for a proposed time (used during appointment creation).
     */
    public function countConflicts(Carbon $proposedStart, Carbon $proposedEnd, array $approvedStatuses): int
    {
        return DB::table('appointments')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->where(function ($q) use ($proposedStart, $proposedEnd) {
                $q->where('appointments.appointment_date', '<', $proposedEnd->toDateTimeString())
                  ->where(
                      DB::raw('DATE_ADD(appointments.appointment_date, INTERVAL TIME_TO_SEC(services.duration) SECOND)'),
                      '>',
                      $proposedStart->toDateTimeString()
                  )
                  ->whereIn('appointments.status', ['Scheduled', 'Waiting', 'Ongoing']);
            })
            ->count();
    }

    /**
     * Fetch a single appointment with full patient + service data (for viewing).
     */
    public function findForView(int $appointmentId): ?object
    {
        return DB::table('appointments')
            ->leftJoin('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->select([
                'appointments.*',
                DB::raw($this->firstNameExpr().' as first_name'),
                DB::raw($this->lastNameExpr().' as last_name'),
                DB::raw($this->middleNameExpr().' as middle_name'),
                DB::raw('COALESCE(patients.mobile_number, appointments.requester_contact_number) as mobile_number'),
                DB::raw($this->birthDateExpr().' as birth_date'),
                'services.service_name',
                'services.duration',
            ])
            ->where('appointments.id', $appointmentId)
            ->first();
    }

    /**
     * Get overlapping approved appointments for the pending approval safety check.
     */
    public function overlappingApproved(int $excludeId, Carbon $start, Carbon $end): \Illuminate\Support\Collection
    {
        return DB::table('appointments')
            ->leftJoin('patients', 'appointments.patient_id', '=', 'patients.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->where('appointments.id', '!=', $excludeId)
            ->whereDate('appointments.appointment_date', $start->toDateString())
            ->whereIn('appointments.status', self::APPROVED_STATUSES)
            ->orderBy('appointments.appointment_date')
            ->select([
                'appointments.id',
                'appointments.appointment_date',
                'appointments.status',
                DB::raw($this->firstNameExpr().' as first_name'),
                DB::raw($this->lastNameExpr().' as last_name'),
                'services.service_name',
                'services.duration',
            ])
            ->get();
    }

    /**
     * Count pending requests targeting the exact same datetime slot (excluding given ID).
     */
    public function countSameTimePending(int $excludeId, string $dateTime): int
    {
        return DB::table('appointments')
            ->where('id', '!=', $excludeId)
            ->where('status', 'Pending')
            ->where('appointment_date', $dateTime)
            ->count();
    }

    /**
     * Count all active (non-Cancelled/Completed) appointments on given date.
     */
    public function countSameDayActive(Carbon $day, array $inactiveStatuses): int
    {
        return DB::table('appointments')
            ->whereDate('appointment_date', $day->toDateString())
            ->whereNotIn('status', $inactiveStatuses)
            ->count();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SQL expression helpers (COALESCE for guest bookings)
    // ─────────────────────────────────────────────────────────────────────────

    public function firstNameExpr(): string
    {
        if (Schema::hasColumn('appointments', 'requested_patient_first_name')) {
            return 'COALESCE(patients.first_name, appointments.requested_patient_first_name, appointments.requester_first_name)';
        }

        return 'COALESCE(patients.first_name, appointments.requester_first_name)';
    }

    public function lastNameExpr(): string
    {
        if (Schema::hasColumn('appointments', 'requested_patient_last_name')) {
            return 'COALESCE(patients.last_name, appointments.requested_patient_last_name, appointments.requester_last_name)';
        }

        return 'COALESCE(patients.last_name, appointments.requester_last_name)';
    }

    public function middleNameExpr(): string
    {
        if (Schema::hasColumn('appointments', 'requested_patient_middle_name') && Schema::hasColumn('appointments', 'requester_middle_name')) {
            return 'COALESCE(patients.middle_name, appointments.requested_patient_middle_name, appointments.requester_middle_name)';
        }

        if (Schema::hasColumn('appointments', 'requested_patient_middle_name')) {
            return 'COALESCE(patients.middle_name, appointments.requested_patient_middle_name)';
        }

        if (Schema::hasColumn('appointments', 'requester_middle_name')) {
            return 'COALESCE(patients.middle_name, appointments.requester_middle_name)';
        }

        return 'patients.middle_name';
    }

    public function birthDateExpr(): string
    {
        if (Schema::hasColumn('appointments', 'requested_patient_birth_date') && Schema::hasColumn('appointments', 'requester_birth_date')) {
            return 'COALESCE(patients.birth_date, appointments.requested_patient_birth_date, appointments.requester_birth_date)';
        }

        if (Schema::hasColumn('appointments', 'requested_patient_birth_date')) {
            return 'COALESCE(patients.birth_date, appointments.requested_patient_birth_date)';
        }

        if (Schema::hasColumn('appointments', 'requester_birth_date')) {
            return 'COALESCE(patients.birth_date, appointments.requester_birth_date)';
        }

        return 'patients.birth_date';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private
    // ─────────────────────────────────────────────────────────────────────────

    private function buildCalendarSelect(): array
    {
        $select = [
            'appointments.id',
            'appointments.patient_id',
            'appointments.service_id',
            'appointments.appointment_date',
            'appointments.status',
            DB::raw($this->firstNameExpr().' as first_name'),
            DB::raw($this->lastNameExpr().' as last_name'),
            DB::raw($this->middleNameExpr().' as middle_name'),
            DB::raw('COALESCE(patients.mobile_number, appointments.requester_contact_number) as mobile_number'),
            DB::raw($this->birthDateExpr().' as birth_date'),
            'services.service_name',
            'services.duration',
        ];

        foreach ([
            'booking_for_other',
            'requested_patient_first_name',
            'requested_patient_last_name',
            'requested_patient_birth_date',
            'requester_first_name',
            'requester_last_name',
            'requester_contact_number',
            'requester_email',
            'requester_relationship_to_patient',
            'requester_birth_date',
        ] as $column) {
            if (Schema::hasColumn('appointments', $column)) {
                $select[] = 'appointments.'.$column;
            }
        }

        return $select;
    }
}
