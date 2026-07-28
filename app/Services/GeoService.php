<?php

namespace App\Services;

class GeoService
{
    public function normalize(array $data): array
    {
        return [
            'country_code' => $this->normalizeCountryCode($data['country_code'] ?? null),
            'region' => $this->blankToNull($data['region'] ?? null),
            'city' => $this->blankToNull($data['city'] ?? null),
            'latitude' => $this->normalizeDecimal($data['latitude'] ?? null),
            'longitude' => $this->normalizeDecimal($data['longitude'] ?? null),
            'timezone' => $this->blankToNull($data['timezone'] ?? null),
        ];
    }

    private function normalizeCountryCode(mixed $value): ?string
    {
        $value = strtoupper(trim((string) $value));

        return $value === '' ? null : substr($value, 0, 2);
    }

    private function normalizeDecimal(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (string) $value : null;
    }

    private function blankToNull(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
