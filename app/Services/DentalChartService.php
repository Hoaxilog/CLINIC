<?php

namespace App\Services;

use App\Models\DentalChart;
use App\Models\TreatmentRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DentalChartService
{
    /**
     * Save (create or update today's) dental chart for a patient.
     * Returns the chart ID.
     */
    public function save(int $patientId, array $chartData, string $modifier, bool $forceNew = false): ?int
    {
        if (empty($chartData)) {
            return null;
        }

        $chartId = null;

        if (! $forceNew) {
            $existingToday = DB::table('dental_charts')
                ->where('patient_id', $patientId)
                ->whereDate('created_at', Carbon::today())
                ->orderBy('created_at', 'desc')
                ->first();

            if ($existingToday) {
                $old = DB::table('dental_charts')->where('id', $existingToday->id)->first();
                DB::table('dental_charts')->where('id', $existingToday->id)->update([
                    'chart_data'  => json_encode($chartData),
                    'modified_by' => $modifier,
                    'updated_at'  => now(),
                ]);
                $chartId = $existingToday->id;

                if ($old) {
                    $this->logChart('dental_chart_updated', $chartId, [
                        'old'        => ['chart_data' => $old->chart_data],
                        'attributes' => ['chart_data' => json_encode($chartData)],
                    ]);
                }
            }
        }

        if (! $chartId) {
            $chartId = DB::table('dental_charts')->insertGetId([
                'patient_id'  => $patientId,
                'chart_data'  => json_encode($chartData),
                'modified_by' => $modifier,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            $this->logChart('dental_chart_created', $chartId, [
                'attributes' => ['patient_id' => $patientId, 'chart_data' => json_encode($chartData)],
            ]);
        }

        return $chartId;
    }

    /**
     * Save a treatment record linked to a dental chart.
     */
    public function saveTreatmentRecord(int $chartId, int $patientId, array $data, string $modifier): void
    {
        if (empty($data)) {
            return;
        }

        $existing = DB::table('treatment_records')->where('dental_chart_id', $chartId)->first();

        $payload = [
            'patient_id'        => $patientId,
            'dmd'               => $data['dmd'] ?? null,
            'treatment'         => $data['treatment'] ?? null,
            'cost_of_treatment' => $data['cost_of_treatment'] ?? null,
            'amount_charged'    => $data['amount_charged'] ?? null,
            'remarks'           => $data['remarks'] ?? null,
            'modified_by'       => $modifier,
            'updated_at'        => now(),
        ];

        DB::table('treatment_records')->updateOrInsert(
            ['dental_chart_id' => $chartId],
            $payload
        );

        $saved = DB::table('treatment_records')->where('dental_chart_id', $chartId)->first();
        if ($saved) {
            $this->saveImages($saved->id, $data['image_payloads'] ?? []);

            $subject = new TreatmentRecord;
            $subject->id = $saved->id;

            if ($existing) {
                activity()->causedBy(Auth::user())->performedOn($subject)
                    ->event('treatment_record_updated')
                    ->withProperties(['old' => (array) $existing, 'attributes' => $payload])
                    ->log('Updated Treatment Record');
            } else {
                activity()->causedBy(Auth::user())->performedOn($subject)
                    ->event('treatment_record_created')
                    ->withProperties(['attributes' => $payload])
                    ->log('Created Treatment Record');
            }
        }
    }

    /**
     * Get chart history list for a patient (id + formatted date).
     */
    public function getHistory(int $patientId): array
    {
        return DB::table('dental_charts')
            ->where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->select('id', 'created_at')
            ->get()
            ->map(fn ($item) => [
                'id'   => $item->id,
                'date' => Carbon::parse($item->created_at)->format('F j, Y - h:i A'),
            ])->toArray();
    }

    /**
     * Get the latest dental chart + its treatment record for a patient.
     * Returns ['chartData', 'chartId', 'treatmentRecord']
     */
    public function getLatest(int $patientId): array
    {
        $latest = DB::table('dental_charts')
            ->where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($latest && ! empty($latest->chart_data)) {
            return [
                'chartData'       => json_decode($latest->chart_data, true),
                'chartId'         => $latest->id,
                'treatmentRecord' => $this->getTreatmentRecord($latest->id),
            ];
        }

        return ['chartData' => [], 'chartId' => null, 'treatmentRecord' => []];
    }

    /**
     * Get a treatment record (with images) for a given chart ID.
     */
    public function getTreatmentRecord(int $chartId): array
    {
        $record = DB::table('treatment_records')->where('dental_chart_id', $chartId)->first();
        if (! $record) {
            return [];
        }

        $result = (array) $record;
        $result['image_list'] = DB::table('treatment_record_images')
            ->where('treatment_record_id', $record->id)
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($img) => (array) $img)
            ->toArray();

        return $result;
    }

    private function saveImages(int $recordId, array $payloads): void
    {
        if (empty($payloads)) {
            return;
        }

        $currentMax = DB::table('treatment_record_images')
            ->where('treatment_record_id', $recordId)
            ->max('sort_order');

        $nextOrder = is_null($currentMax) ? 0 : ($currentMax + 1);
        $now = now();
        $rows = [];

        foreach ($payloads as $index => $payload) {
            $path = $payload['path'] ?? null;
            if (empty($path)) {
                continue;
            }
            $rows[] = [
                'treatment_record_id' => $recordId,
                'image_path'          => $path,
                'image_type'          => $payload['type'] ?? 'other',
                'sort_order'          => $nextOrder + $index,
                'created_at'          => $now,
                'updated_at'          => $now,
            ];
        }

        if (! empty($rows)) {
            DB::table('treatment_record_images')->insert($rows);
        }
    }

    private function logChart(string $event, int $chartId, array $properties): void
    {
        $subject = new DentalChart;
        $subject->id = $chartId;

        activity()
            ->causedBy(Auth::user())
            ->performedOn($subject)
            ->event($event)
            ->withProperties($properties)
            ->log(match ($event) {
                'dental_chart_created' => 'Created Dental Chart',
                'dental_chart_updated' => 'Updated Dental Chart',
                default => $event,
            });
    }
}
