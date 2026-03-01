<?php

namespace App\Services;

use App\Repository\Interface\ShiftRepositoryInterface;
use App\Models\Shift;

class ShiftService
{
    public function __construct(
        private ShiftRepositoryInterface $repository
    ) {}

    public function openShift(int $branchId, int $userId, float $openingCash): Shift
    {
        // إغلاق أي وردية مفتوحة سابقة كإجراء وقائي (إضافة لاحقاً أو في المستودع)
        return $this->repository->create([
            'branch_id' => $branchId,
            'user_id' => $userId,
            'opening_cash' => $openingCash,
            'opened_at' => now(),
            'status' => 'open',
        ]);
    }

    public function closeShift(int $shiftId, float $closingCash): Shift
    {
        return $this->repository->update($shiftId, [
            'closing_cash' => $closingCash,
            'closed_at' => now(),
            'status' => 'closed',
        ]);
    }

    public function getCurrentShift(int $branchId, ?int $userId = null): ?Shift
    {
        return $this->repository->findOpenShift($branchId, $userId);
    }
}
