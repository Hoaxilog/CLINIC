<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PatientTimelineDemoSeeder extends Seeder
{
    private const MODIFIED_BY = 'patient-timeline-demo-seeder';
    private const PATIENT_EMAIL = 'timeline.patient@demo.tejada.test';
    private const USER_EMAIL = 'timeline.portal@demo.tejada.test';

    public function run(): void
    {
        DB::transaction(function (): void {
            $this->cleanupExistingDemoPatient();
            $services = $this->ensureServices();
            $dentistId = $this->ensureDemoDentist();

            $patientId = $this->createPatient();
            $this->createPatientPortalUser($patientId);

            $yesterdayVisit = Carbon::yesterday()->setTime(10, 15, 0);
            $todayVisit = Carbon::today()->setTime(14, 30, 0);
            $todayAppointment = Carbon::today()->setTime(12, 0, 0);

            $this->createFullVisitRecord(
                patientId: $patientId,
                visitAt: $yesterdayVisit,
                healthReason: 'Follow-up cleaning and sensitivity check',
                chartSeed: 1,
                treatment: 'Oral prophylaxis with fluoride varnish',
                cost: 650,
                charged: 1200,
                remarks: 'Sensitivity improved. Advised desensitizing toothpaste.'
            );

            $this->createFullVisitRecord(
                patientId: $patientId,
                visitAt: $todayVisit,
                healthReason: 'Upper molar pain evaluation and restoration',
                chartSeed: 2,
                treatment: 'Composite restoration on upper molar',
                cost: 900,
                charged: 1800,
                remarks: 'Occlusion adjusted after restoration. Return if pain persists.'
            );

            $this->createAppointment(
                patientId: $patientId,
                dentistId: $dentistId,
                serviceId: $services[0]->id,
                appointmentAt: $yesterdayVisit,
                status: 'Completed',
                bookingType: 'walk_in'
            );

            $this->createAppointment(
                patientId: $patientId,
                dentistId: $dentistId,
                serviceId: $services[1]->id,
                appointmentAt: $todayAppointment,
                status: 'Scheduled',
                bookingType: 'online_appointment'
            );
        });
    }

    private function cleanupExistingDemoPatient(): void
    {
        $patientIds = DB::table('patients')
            ->where('email_address', self::PATIENT_EMAIL)
            ->pluck('id');

        if ($patientIds->isNotEmpty()) {
            $chartIds = DB::table('dental_charts')
                ->whereIn('patient_id', $patientIds)
                ->pluck('id');

            if ($chartIds->isNotEmpty()) {
                DB::table('treatment_record_images')->whereIn('treatment_record_id', function ($query) use ($chartIds) {
                    $query->select('id')
                        ->from('treatment_records')
                        ->whereIn('dental_chart_id', $chartIds);
                })->delete();

                DB::table('treatment_records')->whereIn('dental_chart_id', $chartIds)->delete();
            }

            DB::table('health_histories')->whereIn('patient_id', $patientIds)->delete();
            DB::table('dental_charts')->whereIn('patient_id', $patientIds)->delete();
            DB::table('appointments')->whereIn('patient_id', $patientIds)->delete();
            DB::table('users')->whereIn('patient_id', $patientIds)->delete();
            DB::table('patients')->whereIn('id', $patientIds)->delete();
        }

        DB::table('users')->where('email', self::USER_EMAIL)->delete();
    }

    private function createPatient(): int
    {
        return (int) DB::table('patients')->insertGetId([
            'last_name' => 'Timeline',
            'first_name' => 'Demo',
            'middle_name' => 'Visit',
            'nickname' => 'Demo',
            'occupation' => 'Office Staff',
            'birth_date' => Carbon::today()->subYears(29)->toDateString(),
            'gender' => 'Female',
            'civil_status' => 'Single',
            'home_address' => '123 Seeder Lane, Timeline City',
            'office_address' => 'Suite 5, Demo Building',
            'home_number' => null,
            'office_number' => null,
            'mobile_number' => '09171234567',
            'email_address' => self::PATIENT_EMAIL,
            'referral' => 'Patient Timeline Demo Seeder',
            'emergency_contact_name' => 'Rico Timeline',
            'emergency_contact_number' => '09179876543',
            'relationship' => 'Sibling',
            'who_answering' => null,
            'relationship_to_patient' => null,
            'father_name' => null,
            'father_number' => null,
            'mother_name' => null,
            'mother_number' => null,
            'guardian_name' => null,
            'guardian_number' => null,
            'modified_by' => self::MODIFIED_BY,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createPatientPortalUser(int $patientId): void
    {
        DB::table('users')->insert([
            'username' => 'timeline.patient.demo',
            'first_name' => 'Demo',
            'last_name' => 'Timeline',
            'email' => self::USER_EMAIL,
            'mobile_number' => '09170001122',
            'patient_id' => $patientId,
            'email_verified_at' => now(),
            'verification_token' => null,
            'password' => Hash::make('password'),
            'google_id' => null,
            'role' => User::ROLE_PATIENT,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function ensureServices(): array
    {
        $defaults = [
            ['service_name' => 'Cleaning', 'duration' => '01:00:00'],
            ['service_name' => 'Dental Filling', 'duration' => '01:00:00'],
        ];

        foreach ($defaults as $service) {
            DB::table('services')->updateOrInsert(
                ['service_name' => $service['service_name']],
                ['duration' => $service['duration']]
            );
        }

        return DB::table('services')->orderBy('id')->get()->all();
    }

    private function ensureDemoDentist(): int
    {
        $dentist = DB::table('users')->where('email', 'timeline.dentist@demo.tejada.test')->first();

        if ($dentist) {
            return (int) $dentist->id;
        }

        return (int) DB::table('users')->insertGetId([
            'username' => 'timeline.dentist.demo',
            'first_name' => 'Timeline',
            'last_name' => 'Dentist',
            'email' => 'timeline.dentist@demo.tejada.test',
            'mobile_number' => '09170009988',
            'patient_id' => null,
            'email_verified_at' => now(),
            'verification_token' => null,
            'password' => Hash::make('password'),
            'google_id' => null,
            'role' => User::ROLE_DENTIST,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createFullVisitRecord(
        int $patientId,
        Carbon $visitAt,
        string $healthReason,
        int $chartSeed,
        string $treatment,
        int $cost,
        int $charged,
        string $remarks
    ): void {
        DB::table('health_histories')->insert([
            'patient_id' => $patientId,
            'when_last_visit_q1' => $visitAt->copy()->subMonths(6)->toDateString(),
            'what_last_visit_reason_q1' => 'Routine oral prophylaxis',
            'what_seeing_dentist_reason_q2' => $healthReason,
            'is_clicking_jaw_q3a' => 0,
            'is_pain_jaw_q3b' => 0,
            'is_difficulty_opening_closing_q3c' => 0,
            'is_locking_jaw_q3d' => 0,
            'is_clench_grind_q4' => 1,
            'is_bad_experience_q5' => 0,
            'is_nervous_q6' => 0,
            'what_nervous_concern_q6' => null,
            'is_condition_q1' => 0,
            'what_condition_reason_q1' => null,
            'is_hospitalized_q2' => 0,
            'what_hospitalized_reason_q2' => null,
            'is_serious_illness_operation_q3' => 0,
            'what_serious_illness_operation_reason_q3' => null,
            'is_taking_medications_q4' => 1,
            'what_medications_list_q4' => 'Vitamin C once daily',
            'is_allergic_medications_q5' => 0,
            'what_allergies_list_q5' => null,
            'is_allergic_latex_rubber_metals_q6' => 0,
            'is_pregnant_q7' => 0,
            'is_breast_feeding_q8' => 0,
            'modified_by' => self::MODIFIED_BY,
            'created_at' => $visitAt,
            'updated_at' => $visitAt,
        ]);

        $chartId = DB::table('dental_charts')->insertGetId([
            'patient_id' => $patientId,
            'chart_data' => json_encode($this->buildChartData($chartSeed), JSON_THROW_ON_ERROR),
            'modified_by' => self::MODIFIED_BY,
            'created_at' => $visitAt,
            'updated_at' => $visitAt,
        ]);

        DB::table('treatment_records')->insert([
            'patient_id' => $patientId,
            'dental_chart_id' => $chartId,
            'dmd' => 'Dr. Seeder Demo, DMD',
            'treatment' => $treatment,
            'cost_of_treatment' => $cost,
            'amount_charged' => $charged,
            'remarks' => $remarks,
            'image' => null,
            'modified_by' => self::MODIFIED_BY,
            'created_at' => $visitAt,
            'updated_at' => $visitAt,
        ]);
    }

    private function createAppointment(
        int $patientId,
        int $dentistId,
        int $serviceId,
        Carbon $appointmentAt,
        string $status,
        string $bookingType
    ): void {
        DB::table('appointments')->insert([
            'appointment_date' => $appointmentAt,
            'status' => $status,
            'service_id' => $serviceId,
            'patient_id' => $patientId,
            'requester_user_id' => null,
            'requester_first_name' => null,
            'requester_last_name' => null,
            'requester_birth_date' => null,
            'requester_contact_number' => null,
            'requester_email' => null,
            'booking_for_other' => 0,
            'requested_patient_first_name' => null,
            'requested_patient_last_name' => null,
            'requested_patient_birth_date' => null,
            'requester_relationship_to_patient' => null,
            'dentist_id' => $dentistId,
            'modified_by' => self::MODIFIED_BY,
            'created_at' => $appointmentAt->copy()->subDay(),
            'updated_at' => $appointmentAt,
            'booking_type' => $bookingType,
            'cancellation_reason' => null,
            'requester_middle_name' => null,
            'requested_patient_middle_name' => null,
        ]);
    }

    private function buildChartData(int $seed): array
    {
        $adultTeeth = [11, 16, 21, 26, 31, 36, 41, 46];
        $selectedTooth = (string) $adultTeeth[$seed % count($adultTeeth)];

        return [
            'teeth' => [
                $selectedTooth => [
                    'top' => ['code' => 'C', 'color' => 'red'],
                    'line_1' => ['code' => 'LC', 'color' => 'blue'],
                ],
            ],
            'oral_exam' => [
                'oral_hygiene_status' => 'Fair',
                'gingiva' => 'Normal',
                'calcular_deposits' => 'Light',
                'stains' => 'Minimal',
                'complete_denture' => 'No',
                'partial_denture' => 'No',
            ],
            'comments' => [
                'notes' => 'Seeded timeline demo chart entry.',
                'treatment_plan' => 'Monitor symptoms and continue preventive care.',
            ],
            'meta' => [
                'dentition_type' => 'adult',
                'numbering_system' => 'FDI',
            ],
        ];
    }
}
