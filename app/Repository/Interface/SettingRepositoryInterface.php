<?php

declare(strict_types=1);

namespace App\Repository\Interface;

interface SettingRepositoryInterface
{
    /**
     * Get all settings as a key-value pair array.
     */
    public function all(): array;

    /**
     * Get a setting by key.
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Set/Update a setting.
     */
    public function set(string $key, mixed $value): void;

    /**
     * Update multiple settings at once.
     */
    public function updateMultiple(array $settings): void;
}
