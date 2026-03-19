<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PreviousMonthCompletedSeeder extends Seeder
{
    private const MODIFIED_BY = 'previous-month-seeder';

    private const EXTRA_RECORDS = 12;

    public function run(): void
    {
        DB::transaction(function (): void {
            $services = DB::table('services')->orderBy('id')->get();
            if ($services->isEmpty()) {
                return;
            }

            $dentistId = $this->ensureDemoDentist();
            $patients = DB::table('patients')
                ->where('email_address', 'like', '%@demo.tejada.test')
                ->orderBy('id')
                ->limit(12)
                ->get();

            if ($patients->isEmpty()) {
                return;
            }

            $previousMonthStart = Carbon::now()->subMonthNoOverflow()->startOfMonth();
            $previousMonthEnd = Carbon::now()->subMonthNoOverflow()->endOfMonth();
            $hours = [9, 10, 11, 13, 14, 15];

            for ($i = 0; $i < self::EXTRA_RECORDS; $i++) {
                $patient = $patients[$i % $patients->count()];
                $service = $services[$i % $services->count()];
                $day = min(26, 2 + ($i * 2));
                $appointmentAt = $previousMonthStart->copy()
                    ->day($day)
                    ->setTime($hours[$i % count($hours)], 0, 0);

                if ($appointmentAt->gt($previousMonthEnd)) {
                    $appointmentAt = $previousMonthEnd->copy()->setTime(15, 0, 0);
                }

                $chartId = DB::table('dental_charts')->insertGetId([
                    'patient_id' => $patient->id,
                    'chart_data' => json_encode($this->buildChartData($i), JSON_THROW_ON_ERROR),
                    'modified_by' => self::MODIFIED_BY,
                    'created_at' => $appointmentAt,
                    'updated_at' => $appointmentAt,
                ]);

                [$treatment, $cost, $charged, $remarks] = $this->treatmentPayload((string) $service->service_name, $i);

                DB::table('appointments')->insert([
                    'appointment_date' => $appointmentAt,
                    'status' => 'Completed',
                    'service_id' => $service->id,
                    'patient_id' => $patient->id,
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
                    'created_at' => $appointmentAt->copy()->subDays(1),
                    'updated_at' => $appointmentAt,
                    'booking_type' => 'walk_in',
                    'cancellation_reason' => null,
                    'requester_middle_name' => null,
                    'requested_patient_middle_name' => null,
                ]);

                DB::table('treatment_records')->insert([
                    'patient_id' => $patient->id,
                    'dental_chart_id' => $chartId,
                    'dmd' => 'Dr. Demo Dentist, DMD',
                    'treatment' => $treatment,
                    'cost_of_treatment' => $cost,
                    'amount_charged' => $charged,
                    'remarks' => $remarks,
                    'image' => null,
                    'modified_by' => self::MODIFIED_BY,
                    'created_at' => $appointmentAt,
                    'updated_at' => $appointmentAt,
                ]);
            }
        });
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
                'notes' => 'Additional previous-month completed record.',
                'treatment_plan' => 'Continue follow-up care as needed.',
            ],
            'meta' => [
                'dentition_type' => 'adult',
                'numbering_system' => 'FDI',
            ],
        ];
    }

    private function treatmentPayload(string $serviceName, int $seed): array
    {
        $map = [
            'Cleaning' => ['Oral prophylaxis completed', 420, 980, 'Completed previous-month hygiene visit.'],
            'Tooth extractions' => ['Tooth extraction completed', 750, 1900, 'Completed extraction with post-op advice.'],
            'Full Consultation' => ['Comprehensive oral consultation', 220, 650, 'Consultation and treatment planning completed.'],
            'Dental Filling' => ['Composite filling completed', 540, 1280, 'Restoration completed successfully.'],
            'Oral Prophylaxis' => ['Prophylaxis session completed', 380, 900, 'Routine cleaning completed.'],
        ];

        [$treatment, $cost, $charged, $remarks] = $map[$serviceName] ?? [
            $serviceName.' completed',
            350,
            850,
            'Completed previous-month treatment.',
        ];

        return [$treatment, $cost + ($seed * 10), $charged + ($seed * 15), $remarks];
    }
}
