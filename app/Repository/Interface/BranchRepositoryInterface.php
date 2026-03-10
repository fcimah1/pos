<?php

declare(strict_types=1);

namespace App\Repository\Interface;

interface BranchRepositoryInterface
{
    /**
     * Get all branches.
     */
    public function all(): array;

    /**
     * Find a branch by ID.
     */
    public function find(int $id): ?array;

    /**
     * Create a new branch.
     */
    public function create(array $data): array;

    /**
     * Update an existing branch.
     */
    public function update(int $id, array $data): array;

    /**
     * Delete a branch.
     */
    public function delete(int $id): bool;
}
