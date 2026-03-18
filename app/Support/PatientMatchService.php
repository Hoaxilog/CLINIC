<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PatientMatchService
{
    private const MAX_MATCH_SCORE = 180;

    /**
     * Return ranked match suggestions for staff review.
     *
     * @param  array<string, mixed>  $requestData
     */
    public function suggestMatches(array $requestData, int $limit = 10): Collection
    {
        $firstName = $this->normalizeNameValue($requestData['first_name'] ?? null);
        $lastName = $this->normalizeNameValue($requestData['last_name'] ?? null);
        $fullName = trim($firstName.' '.$lastName);
        $mobile = $this->normalizeValue($requestData['mobile_number'] ?? null);
        $email = strtolower($this->normalizeValue($requestData['email_address'] ?? null));
        $birthDate = $this->normalizeValue($requestData['birth_date'] ?? null);

        if ($firstName === '' && $lastName === '' && $mobile === '' && $email === '') {
            return collect();
        }

        $fullNameExpression = $this->fullNameExpression();

        $candidates = DB::table('patients')
            ->select('id', 'first_name', 'last_name', 'mobile_number', 'email_address', 'birth_date')
            ->where(function ($query) use ($mobile, $email, $firstName, $lastName, $fullName, $birthDate, $fullNameExpression) {
                if ($mobile !== '') {
                    $query->orWhere('mobile_number', $mobile);
                }

                if ($email !== '') {
                    $query->orWhereRaw('LOWER(email_address) = ?', [$email]);
                }

                if ($firstName !== '' && $lastName !== '') {
                    $query->orWhere(function ($nameQuery) use ($firstName, $lastName) {
                        $nameQuery
                            ->whereRaw('LOWER(first_name) = ?', [strtolower($firstName)])
                            ->whereRaw('LOWER(last_name) = ?', [strtolower($lastName)]);
                    });
                }

                if ($fullName !== '') {
                    $query->orWhereRaw("LOWER({$fullNameExpression}) LIKE ?", ['%'.strtolower($fullName).'%']);
                }

                if ($birthDate !== '' && $firstName !== '' && $lastName !== '') {
                    $query->orWhere(function ($comboQuery) use ($firstName, $lastName, $birthDate) {
                        $comboQuery
                            ->whereRaw('LOWER(first_name) = ?', [strtolower($firstName)])
                            ->whereRaw('LOWER(last_name) = ?', [strtolower($lastName)])
                            ->whereDate('birth_date', $birthDate);
                    });
                }
            })
            ->limit(30)
            ->get();

        return $candidates
            ->map(function ($patient) use ($firstName, $lastName, $mobile, $email, $birthDate) {
                $score = 0;
                $reasons = [];

                $normalizedFirst = strtolower($firstName);
                $normalizedLast = strtolower($lastName);
                $patientFirst = strtolower((string) ($patient->first_name ?? ''));
                $patientLast = strtolower((string) ($patient->last_name ?? ''));
                $hasRequestedFullName = $firstName !== '' && $lastName !== '';
                $exactFullNameMatch = $hasRequestedFullName
                    && $patientFirst === $normalizedFirst
                    && $patientLast === $normalizedLast;

                $similarFullNameMatch = false;
                if ($hasRequestedFullName && ! $exactFullNameMatch) {
                    $combinedName = strtolower(trim($firstName.' '.$lastName));
                    $patientCombined = strtolower(trim(($patient->first_name ?? '').' '.($patient->last_name ?? '')));
                    $similarFullNameMatch = $combinedName !== '' && str_contains($patientCombined, $combinedName);
                }

                // Security gate: if full name is provided, reject candidates that only match
                // mobile/email but not the requested patient name.
                if ($hasRequestedFullName && ! $exactFullNameMatch && ! $similarFullNameMatch) {
                    return null;
                }

                if ($mobile !== '' && (string) $patient->mobile_number === $mobile) {
                    $score += 60;
                    $reasons[] = 'Exact mobile match';
                }

                if ($email !== '' && strtolower((string) ($patient->email_address ?? '')) === $email) {
                    $score += 50;
                    $reasons[] = 'Exact email match';
                }

                if ($exactFullNameMatch) {
                    $score += 30;
                    $reasons[] = 'Exact full name match';
                } elseif ($similarFullNameMatch) {
                    $score += 15;
                    $reasons[] = 'Similar full name';
                }

                if ($birthDate !== '' && ! empty($patient->birth_date) && (string) $patient->birth_date === $birthDate) {
                    if ($exactFullNameMatch) {
                        $score += 40;
                        $reasons[] = 'Full name + birth date match';
                    } else {
                        $score += 10;
                        $reasons[] = 'Birth date match';
                    }
                }

                $matchPercent = (int) round((max(0, min($score, self::MAX_MATCH_SCORE)) / self::MAX_MATCH_SCORE) * 100);
                $matchBand = $this->resolveMatchBand($matchPercent);

                $patient->match_score = $score;
                $patient->match_percent = $matchPercent;
                $patient->match_band = $matchBand;
                $patient->match_band_label = $this->resolveMatchBandLabel($matchBand);
                $patient->match_reasons = $reasons;

                return $patient;
            })
            ->filter()
            ->filter(fn ($patient) => (int) $patient->match_score > 0)
            ->sortByDesc('match_score')
            ->take($limit)
            ->values();
    }

    protected function resolveMatchBand(int $matchPercent): string
    {
        if ($matchPercent >= 75) {
            return 'strong';
        }

        if ($matchPercent >= 50) {
            return 'partial';
        }

        if ($matchPercent >= 25) {
            return 'weak';
        }

        return 'poor';
    }

    protected function resolveMatchBandLabel(string $matchBand): string
    {
        return match ($matchBand) {
            'strong' => 'Strong match',
            'partial' => 'Partial match',
            'weak' => 'Weak match',
            default => 'Poor match',
        };
    }

    /**
     * Return duplicate-risk warnings for create-new flow.
     *
     * @param  array<string, mixed>  $requestData
     * @return array<int, string>
     */
    public function duplicateWarnings(array $requestData): array
    {
        $warnings = [];

        $firstName = $this->normalizeNameValue($requestData['first_name'] ?? null);
        $lastName = $this->normalizeNameValue($requestData['last_name'] ?? null);
        $mobile = $this->normalizeValue($requestData['mobile_number'] ?? null);
        $email = strtolower($this->normalizeValue($requestData['email_address'] ?? null));
        $birthDate = $this->normalizeValue($requestData['birth_date'] ?? null);

        if ($mobile !== '' && DB::table('patients')->where('mobile_number', $mobile)->exists()) {
            $warnings[] = 'A patient with the same mobile number already exists.';
        }

        if ($email !== '' && DB::table('patients')->whereRaw('LOWER(email_address) = ?', [$email])->exists()) {
            $warnings[] = 'A patient with the same email already exists.';
        }

        if ($firstName !== '' && $lastName !== '' && $birthDate !== '') {
            $nameBirthExists = DB::table('patients')
                ->whereRaw('LOWER(first_name) = ?', [strtolower($firstName)])
                ->whereRaw('LOWER(last_name) = ?', [strtolower($lastName)])
                ->whereDate('birth_date', $birthDate)
                ->exists();

            if ($nameBirthExists) {
                $warnings[] = 'A patient with the same full name and birth date already exists.';
            }
        }

        return $warnings;
    }

    protected function normalizeValue(mixed $value): string
    {
        return trim((string) ($value ?? ''));
    }

    protected function normalizeNameValue(mixed $value): string
    {
        return preg_replace('/\s+/', ' ', $this->normalizeValue($value)) ?? '';
    }

    protected function fullNameExpression(): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')",
            'sqlsrv' => "COALESCE(first_name, '') + ' ' + COALESCE(last_name, '')",
            default => "CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, ''))",
        };
    }
}
