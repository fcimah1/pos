<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ExpenseStoreRequest;
use App\Services\ExpenseService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Throwable;

class ExpenseController
{
    use ApiResponseTrait;

    public function __construct(private ExpenseService $service) {}

    public function store(ExpenseStoreRequest $request)
    {
        try {
            $data = $request->validated();
            $data['branch_id'] = (int)($request->user()?->branch_id ?? 1);
            $data['user_id'] = (int)($request->user()?->id ?? 1);
            $expense = $this->service->add($data);
            return $this->successResponse($expense, 'تم تسجيل المصروف بنجاح', 201);
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل تسجيل المصروف');
        }
    }

    public function index(Request $request)
    {
        try {
            $branchId = (int)($request->user()?->branch_id ?? 1);
            $shiftId = $request->query('shift_id') ? (int)$request->query('shift_id') : null;
            $items = $this->service->listByShift($branchId, $shiftId);
            return $this->successResponse($items);
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل جلب المصروفات');
        }
    }

    public function update(ExpenseStoreRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $data['branch_id'] = (int)($request->user()?->branch_id ?? 1);
            $data['user_id'] = (int)($request->user()?->id ?? 1);
            $expense = $this->service->update((int)$id, $data);
            return $this->successResponse($expense, 'تم تعديل المصروف بنجاح');
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل تعديل المصروف');
        }
    }
}
