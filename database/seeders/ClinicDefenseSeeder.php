<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ClinicDefenseSeeder extends Seeder
{
    private const MODIFIED_BY = 'defense-seeder';
    private const COMPLETED_RECORD_TARGET = 20;

    public function run(): void
    {
        DB::transaction(function (): void {
            $this->cleanupOldDemoData();
            $services = $this->ensureServices();
            $dentistId = $this->ensureDemoDentist();

            $patientIds = [];
            $baseToday = Carbon::today();
            $patientCount = 20;

            for ($i = 1; $i <= $patientCount; $i++) {
                $patientIds[] = $this->createDemoPatient($i, $baseToday);
            }

            $remainingCompletedRecords = self::COMPLETED_RECORD_TARGET;
            $patientTotal = count($patientIds);

            foreach ($patientIds as $index => $patientId) {
                if ($remainingCompletedRecords <= 0) {
                    break;
                }

                $patientsLeft = $patientTotal - $index;
                $minimumNeededForOthers = max(0, $patientsLeft - 1);
                $maxVisitsForThisPatient = min(3, $remainingCompletedRecords - $minimumNeededForOthers);
                $completedVisits = min($maxVisitsForThisPatient, 2 + ($index % 2));

                for ($visit = 0; $visit < $completedVisits; $visit++) {
                    $appointmentAt = $this->completedAppointmentDate($baseToday, $index, $visit);
                    $service = $services[($index + $visit) % count($services)];

                    $appointmentId = DB::table('appointments')->insertGetId([
                        'appointment_date' => $appointmentAt,
                        'status' => 'Completed',
                        'service_id' => $service->id,
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
                        'created_at' => $appointmentAt->copy()->subDays(2),
                        'updated_at' => $appointmentAt,
                        'booking_type' => 'walk_in',
                        'cancellation_reason' => null,
                        'requester_middle_name' => null,
                        'requested_patient_middle_name' => null,
                    ]);

                    $chartId = DB::table('dental_charts')->insertGetId([
                        'patient_id' => $patientId,
                        'chart_data' => json_encode($this->buildChartData($index + $visit), JSON_THROW_ON_ERROR),
                        'modified_by' => self::MODIFIED_BY,
                        'created_at' => $appointmentAt,
                        'updated_at' => $appointmentAt,
                    ]);

                    [$treatmentName, $cost, $charged, $remarks] = $this->buildTreatmentPayload(
                        $service->service_name,
                        $index,
                        $visit
                    );

                    DB::table('treatment_records')->insert([
                        'patient_id' => $patientId,
                        'dental_chart_id' => $chartId,
                        'dmd' => 'Dr. Demo Dentist, DMD',
                        'treatment' => $treatmentName,
                        'cost_of_treatment' => $cost,
                        'amount_charged' => $charged,
                        'remarks' => $remarks,
                        'image' => null,
                        'modified_by' => self::MODIFIED_BY,
                        'created_at' => $appointmentAt,
                        'updated_at' => $appointmentAt,
                    ]);

                    $remainingCompletedRecords--;
                }
            }

            $this->seedPendingAndUpcomingAppointments($patientIds, $services, $dentistId, $baseToday);
        });
    }

    private function cleanupOldDemoData(): void
    {
        $demoPatientIds = DB::table('patients')
            ->where('email_address', 'like', '%@demo.tejada.test')
            ->pluck('id');

        if ($demoPatientIds->isNotEmpty()) {
            $chartIds = DB::table('dental_charts')
                ->whereIn('patient_id', $demoPatientIds)
                ->pluck('id');

            if ($chartIds->isNotEmpty()) {
                DB::table('treatment_records')->whereIn('dental_chart_id', $chartIds)->delete();
            }

            DB::table('dental_charts')->whereIn('patient_id', $demoPatientIds)->delete();
            DB::table('appointments')->whereIn('patient_id', $demoPatientIds)->delete();
            DB::table('users')->whereIn('patient_id', $demoPatientIds)->delete();
            DB::table('patients')->whereIn('id', $demoPatientIds)->delete();
        }

        DB::table('users')->where('email', 'demo.dentist@tejada.test')->delete();
    }

    private function ensureServices(): array
    {
        $defaults = [
            ['service_name' => 'Cleaning', 'duration' => '01:00:00'],
            ['service_name' => 'Tooth extractions', 'duration' => '01:00:00'],
            ['service_name' => 'Full Consultation', 'duration' => '01:00:00'],
            ['service_name' => 'Dental Filling', 'duration' => '01:00:00'],
            ['service_name' => 'Oral Prophylaxis', 'duration' => '01:00:00'],
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
        $dentist = DB::table('users')->where('email', 'demo.dentist@tejada.test')->first();

        if ($dentist) {
            return (int) $dentist->id;
        }

        return (int) DB::table('users')->insertGetId([
            'username' => 'demo.dentist',
            'first_name' => 'Demo',
            'last_name' => 'Dentist',
            'email' => 'demo.dentist@tejada.test',
            'mobile_number' => '09170000001',
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

    private function createDemoPatient(int $index, Carbon $today): int
    {
        $firstNames = [
            'Renz', 'Mia', 'Paolo', 'Jessa', 'Carlo', 'Alyssa', 'Mark', 'Shane', 'Nicole',
            'Adrian', 'Bianca', 'Joshua', 'Andrea', 'Kyle', 'Patricia', 'Jomel', 'Kristine', 'Noel',
            'Janelle', 'Victor',
        ];
        $lastNames = [
            'Rosales', 'Santos', 'Dela Cruz', 'Garcia', 'Mendoza', 'Reyes', 'Navarro', 'Torres', 'Aquino',
            'Villanueva', 'Castillo', 'Ramos', 'Bautista', 'Domingo', 'Soriano', 'Manalang', 'Lopez', 'Fernandez',
            'Mercado', 'Salazar',
        ];

        $firstName = $firstNames[$index - 1];
        $lastName = $lastNames[$index - 1];
        $email = Str::slug($firstName.'.'.$lastName.'.'.$index).'@demo.tejada.test';

        $patientId = DB::table('patients')->insertGetId([
            'last_name' => $lastName,
            'first_name' => $firstName,
            'mobile_number' => '0917'.str_pad((string) (100000 + $index), 6, '0', STR_PAD_LEFT),
            'middle_name' => 'Demo',
            'nickname' => $firstName,
            'occupation' => ['Student', 'Teacher', 'Engineer', 'Cashier', 'OFW', 'Freelancer'][$index % 6],
            'birth_date' => $today->copy()->subYears(20 + $index)->subDays($index)->toDateString(),
            'gender' => $index % 2 === 0 ? 'Female' : 'Male',
            'civil_status' => $index % 4 === 0 ? 'Married' : 'Single',
            'home_address' => 'Demo Address '.$index.', Tejada City',
            'office_address' => null,
            'home_number' => null,
            'office_number' => null,
            'email_address' => $email,
            'referral' => 'Defense Demo Seed',
            'emergency_contact_name' => 'Emergency Contact '.$index,
            'emergency_contact_number' => '0918'.str_pad((string) (200000 + $index), 6, '0', STR_PAD_LEFT),
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

        DB::table('users')->insert([
            'username' => 'patient.demo.'.$index,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'mobile_number' => '0919'.str_pad((string) (300000 + $index), 6, '0', STR_PAD_LEFT),
            'patient_id' => $patientId,
            'email_verified_at' => now(),
            'verification_token' => null,
            'password' => Hash::make('password'),
            'google_id' => null,
            'role' => User::ROLE_PATIENT,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return (int) $patientId;
    }

    private function completedAppointmentDate(Carbon $today, int $patientIndex, int $visitIndex): Carbon
    {
        $weekOffsets = [0, 1, 2, 3, 4, 5];
        $daysInWeek = [0, 1, 2, 3, 4, 5];
        $hours = [9, 10, 11, 13, 14, 15, 16];

        $weeksBack = $weekOffsets[($patientIndex + $visitIndex) % count($weekOffsets)];
        $dayOffset = $daysInWeek[($patientIndex * 2 + $visitIndex) % count($daysInWeek)];
        $hour = $hours[($patientIndex + $visitIndex) % count($hours)];

        return $today->copy()
            ->startOfWeek()
            ->subWeeks($weeksBack)
            ->addDays($dayOffset)
            ->setTime($hour, 0, 0);
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
                'notes' => 'Auto-generated defense chart entry.',
                'treatment_plan' => 'Continue preventive care and scheduled treatment.',
            ],
            'meta' => [
                'dentition_type' => 'adult',
                'numbering_system' => 'FDI',
            ],
        ];
    }

    private function buildTreatmentPayload(string $serviceName, int $patientIndex, int $visitIndex): array
    {
        $treatments = [
            'Cleaning' => ['Oral prophylaxis completed', 400, 900, 'Completed with scaling and polishing.'],
            'Tooth extractions' => ['Tooth extraction completed', 700, 1800, 'Post-op instructions provided.'],
            'Full Consultation' => ['Comprehensive oral consultation', 200, 600, 'Treatment plan reviewed with patient.'],
            'Dental Filling' => ['Composite filling completed', 500, 1200, 'Occlusion adjusted after restoration.'],
            'Oral Prophylaxis' => ['Prophylaxis session completed', 350, 850, 'Plaque and stains removed successfully.'],
        ];

        if (isset($treatments[$serviceName])) {
            [$treatment, $cost, $charged, $remarks] = $treatments[$serviceName];

            return [
                $treatment,
                $cost + ($visitIndex * 25),
                $charged + ($patientIndex * 20),
                $remarks,
            ];
        }

        return [
            $serviceName.' completed',
            300 + ($visitIndex * 20),
            800 + ($patientIndex * 15),
            'Completed during defense demo seed run.',
        ];
    }

    private function seedPendingAndUpcomingAppointments(array $patientIds, array $services, int $dentistId, Carbon $today): void
    {
        foreach (array_slice($patientIds, 0, 5) as $index => $patientId) {
            $pendingAt = $today->copy()->addDays(1 + $index)->setTime(9 + $index, 0, 0);
            $service = $services[$index % count($services)];

            DB::table('appointments')->insert([
                'appointment_date' => $pendingAt,
                'status' => 'Pending',
                'service_id' => $service->id,
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
                'created_at' => now(),
                'updated_at' => now(),
                'booking_type' => 'online_appointment',
                'cancellation_reason' => null,
                'requester_middle_name' => null,
                'requested_patient_middle_name' => null,
            ]);
        }

        foreach (array_slice($patientIds, 5, 4) as $index => $patientId) {
            $scheduledAt = $today->copy()->setTime(10 + $index, 0, 0);
            $service = $services[($index + 1) % count($services)];

            DB::table('appointments')->insert([
                'appointment_date' => $scheduledAt,
                'status' => 'Scheduled',
                'service_id' => $service->id,
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
                'created_at' => now(),
                'updated_at' => now(),
                'booking_type' => 'online_appointment',
                'cancellation_reason' => null,
                'requester_middle_name' => null,
                'requested_patient_middle_name' => null,
            ]);
        }
    }
}
