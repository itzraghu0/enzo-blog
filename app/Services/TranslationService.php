<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class TranslationService
{
    public function defaultLocale(): string
    {
        return config('blog.default_locale', 'en');
    }

    public function supportedLocales(): array
    {
        $locales = config('blog.supported_locales', [$this->defaultLocale()]);

        return array_values(array_filter($locales));
    }

    public function normalizeLocalePayloads(array $translations, array $fields): array
    {
        $defaultLocale = $this->defaultLocale();
        $supportedLocales = $this->supportedLocales();
        $normalized = [];

        $defaultPayload = $translations[$defaultLocale] ?? [];
        if ($defaultPayload === [] && $translations !== []) {
            $defaultLocale = array_key_first($translations);
            $defaultPayload = $translations[$defaultLocale] ?? [];
        }

        foreach ($supportedLocales as $locale) {
            $current = $translations[$locale] ?? [];
            $normalized[$locale] = [];

            foreach ($fields as $field) {
                $value = $current[$field] ?? null;

                if ($this->isBlank($value)) {
                    $value = $defaultPayload[$field] ?? null;
                }

                if ($field === 'slug' && $this->isBlank($value) && ! $this->isBlank($current['title'] ?? null)) {
                    $value = Str::slug((string) $current['title']);
                }

                $normalized[$locale][$field] = $value;
            }

            if ($this->isBlank($normalized[$locale]['slug'] ?? null) && ! $this->isBlank($normalized[$locale]['title'] ?? null)) {
                $normalized[$locale]['slug'] = Str::slug((string) $normalized[$locale]['title']);
            }
        }

        if (! isset($normalized[$defaultLocale])) {
            $normalized[$defaultLocale] = Arr::only($defaultPayload, $fields);
        }

        return $normalized;
    }

    private function isBlank(mixed $value): bool
    {
        if (is_array($value)) {
            return $value === [];
        }

        if (is_object($value)) {
            return false;
        }

        return $value === null || trim((string) $value) === '';
    }
}
