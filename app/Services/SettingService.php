<?php

declare(strict_types=1);

namespace App\Services;

use App\Repository\Interface\SettingRepositoryInterface;

class SettingService
{
    public function __construct(
        private readonly SettingRepositoryInterface $settingRepository
    ) {}

    /**
     * Get all company settings.
     */
    public function getAllSettings(): array
    {
        return $this->settingRepository->all();
    }

    /**
     * Update multiple settings.
     */
    public function updateSettings(array $settings): void
    {
        $this->settingRepository->updateMultiple($settings);
    }

    /**
     * Get a specific setting.
     */
    public function getSetting(string $key, mixed $default = null): mixed
    {
        return $this->settingRepository->get($key, $default);
    }
}
