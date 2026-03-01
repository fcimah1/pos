<?php

namespace App\Services;

use App\Repository\Interface\TableRepositoryInterface;

class TableService
{
    public function __construct(
        private TableRepositoryInterface $repository
    ) {}

    public function getAll(int $branchId)
    {
        return $this->repository->getAll($branchId);
    }

    public function getAvailable(int $branchId)
    {
        return $this->repository->getAll($branchId)->where('is_available', true);
    }

    public function updateStatus(int $tableId, bool $isAvailable)
    {
        return $this->repository->update($tableId, ['is_available' => $isAvailable]);
    }
}
