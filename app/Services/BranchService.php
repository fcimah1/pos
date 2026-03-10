<?php

declare(strict_types=1);

namespace App\Services;

use App\Repository\Interface\BranchRepositoryInterface;

class BranchService
{
    public function __construct(
        private readonly BranchRepositoryInterface $branchRepository
    ) {}

    /**
     * Get all branches.
     */
    public function getAllBranches(): array
    {
        return $this->branchRepository->all();
    }

    /**
     * Get a specific branch by ID.
     */
    public function getBranchById(int $id): ?array
    {
        return $this->branchRepository->find($id);
    }

    /**
     * Create a new branch.
     */
    public function createBranch(array $data): array
    {
        return $this->branchRepository->create($data);
    }

    /**
     * Update an existing branch.
     */
    public function updateBranch(int $id, array $data): array
    {
        return $this->branchRepository->update($id, $data);
    }

    /**
     * Delete a branch.
     */
    public function deleteBranch(int $id): bool
    {
        return $this->branchRepository->delete($id);
    }
}
