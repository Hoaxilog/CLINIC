<?php

namespace App\Livewire\Patient\Form\Concerns;

trait SanitizesPatientFormInput
{
    protected function sanitizeDigitsOnly($value): string
    {
        return preg_replace('/\D+/u', '', (string) $value) ?? '';
    }

    protected function sanitizeDecimalValue($value): string
    {
        $sanitized = preg_replace('/[^0-9.]/u', '', (string) $value) ?? '';

        if ($sanitized === '') {
            return '';
        }

        $parts = explode('.', $sanitized, 3);

        if (count($parts) === 1) {
            return $parts[0];
        }

        return $parts[0].'.'.preg_replace('/\./u', '', $parts[1].($parts[2] ?? ''));
    }

    protected function sanitizeEmail($value): string
    {
        return $this->toLower($this->normalizeWhitespace((string) $value));
    }

    protected function sanitizeTitleCaseText($value, bool $allowDigits = false, string $extraAllowedChars = ''): string
    {
        $sanitized = $this->stripDisallowedCharacters($value, $allowDigits, $extraAllowedChars);

        if ($sanitized === '') {
            return '';
        }

        return $this->toTitleCase($this->toLower($sanitized));
    }

    protected function sanitizeSentenceCaseText($value, bool $allowDigits = true, string $extraAllowedChars = ".,!?()'\"#&/:;@%+-"): string
    {
        $sanitized = $this->stripDisallowedCharacters($value, $allowDigits, $extraAllowedChars);

        if ($sanitized === '') {
            return '';
        }

        return $this->applySentenceCase($sanitized);
    }

    protected function sanitizeEnumValue($value, array $allowed, string $default = ''): string
    {
        return in_array($value, $allowed, true) ? $value : $default;
    }

    protected function normalizeWhitespace($value): string
    {
        $value = trim((string) $value);

        return preg_replace('/\s+/u', ' ', $value) ?? '';
    }

    private function stripDisallowedCharacters($value, bool $allowDigits, string $extraAllowedChars): string
    {
        $normalized = $this->normalizeWhitespace($value);

        if ($normalized === '') {
            return '';
        }

        $escapedExtra = preg_quote($extraAllowedChars, '/');
        $digitClass = $allowDigits ? '\pN' : '';
        $sanitized = preg_replace("/[^\\pL\\pM{$digitClass}\\s{$escapedExtra}]/u", '', $normalized);

        return $this->normalizeWhitespace($sanitized ?? '');
    }

    private function applySentenceCase(string $value): string
    {
        $value = $this->toLower($this->normalizeWhitespace($value));

        return preg_replace_callback(
            '/(^|[.!?]\s+)([\pL\pM\pN])/u',
            fn(array $matches): string => $matches[1].$this->toUpper($matches[2]),
            $value
        ) ?? $value;
    }

    private function toLower(string $value): string
    {
        return function_exists('mb_strtolower')
            ? mb_strtolower($value, 'UTF-8')
            : strtolower($value);
    }

    private function toUpper(string $value): string
    {
        return function_exists('mb_strtoupper')
            ? mb_strtoupper($value, 'UTF-8')
            : strtoupper($value);
    }

    private function toTitleCase(string $value): string
    {
        if (function_exists('mb_convert_case')) {
            return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
        }

        return ucwords($value);
    }
}
