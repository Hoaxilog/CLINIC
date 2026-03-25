<?php

namespace App\Support;

class InputSanitizer
{
    public static function normalizeWhitespace(mixed $value): string
    {
        $value = trim((string) $value);

        return preg_replace('/\s+/u', ' ', $value) ?? '';
    }

    public static function sanitizeEmail(mixed $value): string
    {
        return self::toLower(self::normalizeWhitespace($value));
    }

    public static function sanitizeDigitsOnly(mixed $value): string
    {
        return preg_replace('/\D+/u', '', (string) $value) ?? '';
    }

    public static function sanitizeCountryCodeLocalNumber(mixed $value, int $length = 10): string
    {
        $digits = self::sanitizeDigitsOnly($value);

        if (str_starts_with($digits, '63') && strlen($digits) >= ($length + 2)) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        return substr($digits, 0, $length);
    }

    public static function sanitizeTitleCase(mixed $value, bool $allowDigits = false, string $extraAllowedChars = ''): string
    {
        $sanitized = self::stripDisallowedCharacters($value, $allowDigits, $extraAllowedChars);

        if ($sanitized === '') {
            return '';
        }

        return self::toTitleCase(self::toLower($sanitized));
    }

    public static function sanitizeSentenceCase(mixed $value, bool $allowDigits = true, string $extraAllowedChars = ".,!?()'\"#&/:;@%+-"): string
    {
        $sanitized = self::stripDisallowedCharacters($value, $allowDigits, $extraAllowedChars);

        if ($sanitized === '') {
            return '';
        }

        $sanitized = self::toLower(self::normalizeWhitespace($sanitized));

        return preg_replace_callback(
            '/(^|[.!?]\s+)([\pL\pM\pN])/u',
            fn(array $matches): string => $matches[1].self::toUpper($matches[2]),
            $sanitized
        ) ?? $sanitized;
    }

    public static function sanitizeSearch(mixed $value): string
    {
        return self::sanitizeSentenceCase($value, true, '.,&()/-');
    }

    private static function stripDisallowedCharacters(mixed $value, bool $allowDigits, string $extraAllowedChars): string
    {
        $normalized = self::normalizeWhitespace($value);

        if ($normalized === '') {
            return '';
        }

        $escapedExtra = preg_quote($extraAllowedChars, '/');
        $digitClass = $allowDigits ? '\pN' : '';
        $sanitized = preg_replace("/[^\\pL\\pM{$digitClass}\\s{$escapedExtra}]/u", '', $normalized);

        return self::normalizeWhitespace($sanitized ?? '');
    }

    private static function toLower(string $value): string
    {
        return function_exists('mb_strtolower')
            ? mb_strtolower($value, 'UTF-8')
            : strtolower($value);
    }

    private static function toUpper(string $value): string
    {
        return function_exists('mb_strtoupper')
            ? mb_strtoupper($value, 'UTF-8')
            : strtoupper($value);
    }

    private static function toTitleCase(string $value): string
    {
        if (function_exists('mb_convert_case')) {
            return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
        }

        return ucwords($value);
    }
}
