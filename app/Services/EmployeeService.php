<?php

namespace App\Services;

use App\Models\User;
use App\Repository\Interface\UserRepositoryInterface;

class EmployeeService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    /**
     * تصفية الموظفين بناءً على السلم الهرمي
     * Rule: Level X see self + Level Y > X
     */
    public function getVisibleEmployees(User $authUser)
    {
        $myLevel = $authUser->designation?->hierarchy_level ?? 5;
        $branchId = $authUser->branch_id ?? 1;

        $allEmployees = $this->userRepository->getByBranchId($branchId);

        return $allEmployees->filter(function ($employee) use ($authUser, $myLevel) {
            // الشخص نفسه
            if ($employee->id === $authUser->id) {
                return true;
            }

            // الموظفون ذوو المستوى الأدنى (رقم أكبر)
            $empLevel = $employee->designation?->hierarchy_level ?? 5;
            return $empLevel > $myLevel;
        })->values();
    }

    /**
     * الحصول على موظفي نفس القسم للمناوبة (Backup)
     * Rule: Ignore hierarchy, must be same department
     */
    public function getBackupEmployees(User $targetUser)
    {
        if (!$targetUser->department_id) {
            return collect([]);
        }

        $branchId = $targetUser->branch_id ?? 1;
        $allEmployees = $this->userRepository->getByBranchId($branchId);

        return $allEmployees->filter(function ($employee) use ($targetUser) {
            return $employee->department_id === $targetUser->department_id &&
                $employee->id !== $targetUser->id;
        })->values();
    }
}
