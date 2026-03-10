<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\DeliveryPerson;
use App\Repository\Interface\DeliveryPersonRepositoryInterface;
use Illuminate\Support\Collection;

class DeliveryPersonRepository implements DeliveryPersonRepositoryInterface
{
    /**
     * Get all delivery persons.
     */
    public function all(): Collection
    {
        return DeliveryPerson::all();
    }

    /**
     * Find a delivery person by ID.
     */
    public function find(int $id): ?DeliveryPerson
    {
        return DeliveryPerson::find($id);
    }

    /**
     * Create a new delivery person.
     */
    public function create(array $data): DeliveryPerson
    {
        return DeliveryPerson::create($data);
    }

    /**
     * Update an existing delivery person.
     */
    public function update(int $id, array $data): ?DeliveryPerson
    {
        $person = $this->find($id);
        if ($person) {
            $person->update($data);
            return $person->refresh();
        }
        return null;
    }

    /**
     * Delete a delivery person.
     */
    public function delete(int $id): bool
    {
        $person = $this->find($id);
        if ($person) {
            return (bool)$person->delete();
        }
        return false;
    }

    /**
     * Get delivery persons by branch ID.
     */
    public function getByBranchId(int $branchId): Collection
    {
        return DeliveryPerson::where('branch_id', $branchId)->get();
    }
}
