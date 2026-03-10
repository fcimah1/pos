<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\Branch;
use App\Repository\Interface\BranchRepositoryInterface;

class BranchRepository implements BranchRepositoryInterface
{
    /**
     * Get all branches.
     */
    public function all(): array
    {
        return Branch::all()->toArray();
    }

    /**
     * Find a branch by ID.
     */
    public function find(int $id): ?array
    {
        $branch = Branch::find($id);
        return $branch ? $branch->toArray() : null;
    }

    /**
     * Create a new branch.
     */
    public function create(array $data): array
    {
        return Branch::create($data)->toArray();
    }

    /**
     * Update an existing branch.
     */
    public function update(int $id, array $data): array
    {
        $branch = Branch::findOrFail($id);
        $branch->update($data);
        return $branch->toArray();
    }

    /**
     * Delete a branch.
     */
    public function delete(int $id): bool
    {
        $branch = Branch::findOrFail($id);
        return $branch->delete();
    }
}
