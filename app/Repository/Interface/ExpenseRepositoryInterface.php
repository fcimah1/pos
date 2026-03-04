<?php

namespace App\Repository\Interface;

use App\Models\Expense;
use Illuminate\Support\Collection;

interface ExpenseRepositoryInterface
{
    public function create(array $data): Expense;
    public function getByShift(int $branchId, ?int $shiftId = null): Collection;
    public function sumByShift(int $branchId, ?int $shiftId = null): float;
    public function update(int $id, array $data): Expense;
    public function find(int $id): ?Expense;
}
