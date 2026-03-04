<?php

namespace App\Services;

use App\Repository\Interface\ExpenseRepositoryInterface;
use App\Repository\Interface\ShiftRepositoryInterface;
use App\Models\Expense;
use Illuminate\Support\Collection;

class ExpenseService
{
    public function __construct(
        private ExpenseRepositoryInterface $repo,
        private ShiftRepositoryInterface $shiftRepo
    ) {}

    public function add(array $data): Expense
    {
        if (!isset($data['shift_id']) && isset($data['branch_id'])) {
            $open = $this->shiftRepo->findOpenShift($data['branch_id']);
            $data['shift_id'] = $open?->id;
        }
        return $this->repo->create($data);
    }

    public function listByShift(int $branchId, ?int $shiftId = null): Collection
    {
        return $this->repo->getByShift($branchId, $shiftId);
    }

    public function totalByShift(int $branchId, ?int $shiftId = null): float
    {
        return $this->repo->sumByShift($branchId, $shiftId);
    }

    public function update(int $id, array $data): Expense
    {
        return $this->repo->update($id, $data);
    }
}
