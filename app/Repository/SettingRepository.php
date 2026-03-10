<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\Setting;
use App\Repository\Interface\SettingRepositoryInterface;

class SettingRepository implements SettingRepositoryInterface
{
    /**
     * Get all settings as a key-value pair array.
     */
    public function all(): array
    {
        return Setting::pluck('value', 'key')->toArray();
    }

    /**
     * Get a setting by key.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $setting = Setting::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set/Update a setting.
     */
    public function set(string $key, mixed $value): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Update multiple settings at once.
     */
    public function updateMultiple(array $settings): void
    {
        foreach ($settings as $key => $value) {
            $this->set($key, $value);
        }
    }
}
