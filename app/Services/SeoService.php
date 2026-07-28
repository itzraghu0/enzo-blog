<?php

namespace App\Services;

class SeoService
{
    public function normalize(array $data, ?string $title = null): array
    {
        $title = trim((string) $title);

        return [
            'seo_title' => $this->blankToNull($data['seo_title'] ?? $title),
            'meta_description' => $this->blankToNull($data['meta_description'] ?? null),
            'og_title' => $this->blankToNull($data['og_title'] ?? $title),
            'og_description' => $this->blankToNull($data['og_description'] ?? null),
            'canonical_url' => $this->blankToNull($data['canonical_url'] ?? null),
        ];
    }

    private function blankToNull(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
