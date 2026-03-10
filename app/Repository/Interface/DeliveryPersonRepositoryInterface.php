<?php

declare(strict_types=1);

namespace App\Repository\Interface;

use App\Models\DeliveryPerson;
use Illuminate\Support\Collection;

interface DeliveryPersonRepositoryInterface
{
    /**
     * Get all delivery persons.
     */
    public function all(): Collection;

    /**
     * Find a delivery person by ID.
     */
    public function find(int $id): ?DeliveryPerson;

    /**
     * Create a new delivery person.
     */
    public function create(array $data): DeliveryPerson;

    /**
     * Update an existing delivery person.
     */
    public function update(int $id, array $data): ?DeliveryPerson;

    /**
     * Delete a delivery person.
     */
    public function delete(int $id): bool;

    /**
     * Get delivery persons by branch ID.
     */
    public function getByBranchId(int $branchId): Collection;
}
