<?php

namespace App\Http\Controllers\Api;

use App\Services\EmployeeService;
use Illuminate\Http\Request;

class EmployeeController
{
    public function __construct(
        private EmployeeService $service,
        private \App\Repository\Interface\UserRepositoryInterface $userRepository
    ) {}

    /**
     * جلب الموظفين الذين يمكن للمستخدم الحالي رؤيتهم (Hierarchy Logic)
     */
    public function visible(Request $request)
    {
        $employees = $this->service->getVisibleEmployees($request->user());
        return response()->json($employees);
    }

    /**
     * جلب الموظفين البدلاء لنفس قسم الموظف المستهدف (Same Department)
     */
    public function backups(Request $request, $userId)
    {
        $targetUser = $this->userRepository->find((int)$userId);
        if (!$targetUser) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $backups = $this->service->getBackupEmployees($targetUser);
        return response()->json($backups);
    }
}
