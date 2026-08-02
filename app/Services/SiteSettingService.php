<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SiteSettingService
{
    private const CACHE_KEY = 'site_settings.values';

    public function defaults(): array
    {
        return [
            'social_facebook_url' => '#',
            'social_instagram_url' => '#',
            'social_whatsapp_url' => '#',
            'social_tiktok_url' => '#',
            'topbar_newsletter_url' => '#',
            'topbar_faq_url' => '#',
            'footer_address' => 'Kilianstadterstr. 34 61137 Schoneck',
            'footer_phone' => '+49 6187 - 9959050',
            'footer_fax' => '+49 6187 - 9928178',
            'footer_email' => 'info@cupssy.com',
            'footer_service_help_url' => '#',
            'footer_service_contact_url' => '#',
            'footer_service_payment_url' => '#',
            'footer_service_shipping_url' => '#',
            'footer_legal_terms_url' => '#',
            'footer_legal_privacy_url' => '#',
            'footer_legal_imprint_url' => '#',
            'footer_legal_cancellation_url' => '#',
            'footer_legal_sitemap_url' => '#',
        ];
    }

    public function all(): Collection
    {
        $saved = SiteSetting::query()
            ->orderBy('setting_key')
            ->get()
            ->keyBy('setting_key');

        return collect($this->defaults())->map(function (?string $defaultValue, string $key) use ($saved): array {
            return [
                'key' => $key,
                'value' => old("settings.{$key}", $saved->get($key)?->setting_value ?? $defaultValue),
            ];
        });
    }

    public function values(): array
    {
        return Cache::remember(self::CACHE_KEY, now()->addHour(), function (): array {
            $saved = SiteSetting::query()
                ->pluck('setting_value', 'setting_key')
                ->all();

            return array_merge($this->defaults(), $saved);
        });
    }

    public function get(string $key, ?string $default = null): ?string
    {
        $values = $this->values();

        return $values[$key] ?? $default;
    }

    public function upsertMany(array $settings): void
    {
        foreach ($this->defaults() as $key => $defaultValue) {
            SiteSetting::updateOrCreate(
                ['setting_key' => $key],
                ['setting_value' => $this->blankToNull($settings[$key] ?? $defaultValue)]
            );
        }

        Cache::forget(self::CACHE_KEY);
    }

    private function blankToNull(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
