<?php

namespace App\Support;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class ClientDataSecurity
{
    public const FIELDS = [
        'nombreCompleto',
        'telefono',
        'correo',
        'rfc',
    ];

    public static function encrypt(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        return Crypt::encryptString($value);
    }

    public static function decrypt(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        try {
            return Crypt::decryptString($value);
        } catch (DecryptException) {
            return $value;
        }
    }

    public static function prepareForStorage(string $field, ?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = preg_replace('/\s+/', ' ', trim($value));

        return match ($field) {
            'telefono' => preg_replace('/\D+/', '', $value),
            'correo' => strtolower($value),
            'rfc' => strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $value)),
            default => $value,
        };
    }

    public static function normalize(string $field, ?string $value): ?string
    {
        $value = self::prepareForStorage($field, self::decrypt($value));

        if ($value === null || $value === '') {
            return null;
        }

        return match ($field) {
            'telefono' => $value,
            'correo' => strtolower($value),
            'rfc' => strtoupper($value),
            default => Str::of($value)
                ->ascii()
                ->lower()
                ->replaceMatches('/\s+/', ' ')
                ->trim()
                ->toString(),
        };
    }

    public static function hash(string $field, ?string $value): ?string
    {
        $normalized = self::normalize($field, $value);

        if ($normalized === null || $normalized === '') {
            return null;
        }

        return hash_hmac('sha256', $normalized, (string) config('app.key'));
    }

    public static function hashColumn(string $field): string
    {
        return $field . '_hash';
    }

    public static function matchesSearch(array $values, string $search): bool
    {
        $terms = Str::of($search)
            ->ascii()
            ->lower()
            ->replaceMatches('/\s+/', ' ')
            ->trim()
            ->explode(' ')
            ->filter()
            ->values();

        if ($terms->isEmpty()) {
            return true;
        }

        $haystack = Str::of(implode(' ', $values))
            ->ascii()
            ->lower()
            ->replaceMatches('/\s+/', ' ')
            ->trim()
            ->toString();

        return $terms->every(fn ($term) => str_contains($haystack, $term));
    }
}
