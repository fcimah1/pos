<?php

namespace App\Repository;

use App\Models\Expense;
use App\Repository\Interface\ExpenseRepositoryInterface;
use Illuminate\Support\Collection;

class ExpenseRepository implements ExpenseRepositoryInterface
{
    public function create(array $data): Expense
    {
        return Expense::create($data);
    }

    public function getByShift(int $branchId, ?int $shiftId = null): Collection
    {
        return Expense::where('branch_id', $branchId)
            ->when($shiftId, fn($q) => $q->where('shift_id', $shiftId))
            ->latest()
            ->get();
    }

    public function sumByShift(int $branchId, ?int $shiftId = null): float
    {
        return (float) Expense::where('branch_id', $branchId)
            ->when($shiftId, fn($q) => $q->where('shift_id', $shiftId))
            ->sum('amount');
    }

    public function update(int $id, array $data): Expense
    {
        $expense = Expense::findOrFail($id);
        $expense->update($data);
        return $expense;
    }

    public function find(int $id): ?Expense
    {
        return Expense::find($id);
    }
}
