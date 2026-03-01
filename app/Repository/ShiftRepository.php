<?php

namespace App\Repository;

use App\Models\Shift;
use App\Repository\Interface\ShiftRepositoryInterface;

class ShiftRepository implements ShiftRepositoryInterface
{
    public function create(array $data): Shift
    {
        return Shift::create($data);
    }

    public function update(int $id, array $data): Shift
    {
        $shift = Shift::findOrFail($id);
        $shift->update($data);
        return $shift;
    }

    public function findOpenShift(int $branchId, ?int $userId = null): ?Shift
    {
        $query = Shift::where('branch_id', $branchId)
            ->where('status', 'open');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->latest()->first();
    }

    public function find(int $id): ?Shift
    {
        return Shift::find($id);
    }
}
