<?php

namespace App\Repository\Interface;

use App\Models\Shift;

interface ShiftRepositoryInterface
{
    public function create(array $data): Shift;
    public function update(int $id, array $data): Shift;
    public function findOpenShift(int $branchId, ?int $userId = null): ?Shift;
    public function find(int $id): ?Shift;
}
