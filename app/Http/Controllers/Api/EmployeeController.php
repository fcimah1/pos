<?php

namespace App\Http\Controllers\Api;

use App\Services\EmployeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Traits\ApiResponseTrait;
use App\Repository\Interface\UserRepositoryInterface;
use Throwable;

class EmployeeController
{
    use ApiResponseTrait;

    public function __construct(
        private EmployeeService $service,
        private UserRepositoryInterface $userRepository
    ) {}

    /**
     * قائمة الموظفين
     */
    public function index()
    {
        try {
            $users = $this->userRepository->all();

            return $this->successResponse($users);
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل جلب قائمة الموظفين');
        }
    }

    /**
     * إضافة موظف جديد
     */
    public function store(\App\Http\Requests\StoreEmployeeRequest $request)
    {
        try {
            $data = $request->validated();

            // Map designation to role if role_id is missing or to ensure consistency
            if (!isset($data['role_id']) || empty($data['role_id'])) {
                $designationId = $data['designation_id'] ?? 2; // Default to cashier designation
                $data['role_id'] = match ((int)$designationId) {
                    1 => 1, // Admin
                    2 => 2, // Cashier
                    3 => 2, // Delivery Boy (mapped to cashier role for now or custom role)
                    default => 2
                };
            }

            $data['branch_id'] = $data['branch_id'] ?? $request->user()->branch_id ?? 1;
            $data['is_active'] = true;

            $user = $this->userRepository->create($data);

            return $this->successResponse($user, 'تم إضافة الموظف بنجاح', 201);
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل إضافة الموظف');
        }
    }

    /**
     * تحديث بيانات موظف
     */
    public function update(\App\Http\Requests\UpdateEmployeeRequest $request, $id)
    {
        try {
            $user = $this->userRepository->find((int)$id);
            if (!$user) {
                return $this->errorResponse('الموظف غير موجود', 404);
            }

            $data = $request->validated();

            if (empty($data['password'])) {
                unset($data['password']);
            }

            $updatedUser = $this->userRepository->update((int)$id, $data);

            return $this->successResponse($updatedUser, 'تم تحديث بيانات الموظف بنجاح');
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل تحديث بيانات الموظف');
        }
    }

    /**
     * حذف موظف
     */
    public function destroy($id)
    {
        try {
            $success = $this->userRepository->delete((int)$id);
            if (!$success) {
                return $this->errorResponse('الموظف غير موجود أو لا يمكن حذفه', 404);
            }
            return $this->successResponse(null, 'تم حذف الموظف بنجاح');
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل حذف الموظف');
        }
    }

    /**
     * جلب الموظفين الذين يمكن للمستخدم الحالي رؤيتهم (Hierarchy Logic)
     */
    public function visible(Request $request)
    {
        try {
            $employees = $this->service->getVisibleEmployees($request->user());
            return $this->successResponse($employees, 'تم جلب الموظفين بنجاح');
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل جلب الموظفين');
        }
    }

    /**
     * جلب الموظفين البدلاء لنفس قسم الموظف المستهدف (Same Department)
     */
    public function backups(Request $request, $userId)
    {
        try {
            $targetUser = $this->userRepository->find((int)$userId);
            if (!$targetUser) {
                return $this->errorResponse('الموظف غير موجود', 404);
            }
            $backups = $this->service->getBackupEmployees($targetUser);
            return $this->successResponse($backups, 'تم جلب الموظفين البدلاء بنجاح');
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل جلب الموظفين البدلاء');
        }
    }
}
