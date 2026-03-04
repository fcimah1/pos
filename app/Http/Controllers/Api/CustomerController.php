<?php

namespace App\Http\Controllers\Api;

use App\Services\CustomerService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Throwable;

class CustomerController
{
    use ApiResponseTrait;

    public function __construct(private CustomerService $service) {}

    public function index(Request $request)
    {
        try {
            $branchId = $request->user()?->branch_id ?? 1;
            return $this->successResponse($this->service->getAll($branchId));
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل جلب قائمة العملاء');
        }
    }

    public function search(Request $request)
    {
        try {
            $query = $request->query('q', '');
            $branchId = $request->user()?->branch_id ?? 1;
            return $this->successResponse($this->service->search($query, $branchId));
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل البحث عن العميل');
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $data['branch_id'] = $request->user()?->branch_id ?? 1;
            $customer = $this->service->create($data);
            return $this->successResponse($customer, 'تم إنشاء العميل بنجاح', 201);
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل إنشاء العميل');
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $branchId = $request->user()?->branch_id ?? 1;
            return $this->successResponse($this->service->findById((int)$id, $branchId));
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل جلب بيانات العميل');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $data = $request->all();
            $data['branch_id'] = $request->user()?->branch_id ?? 1;
            $customer = $this->service->update((int)$id, $data);
            return $this->successResponse($customer, 'تم تحديث بيانات العميل بنجاح');
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل تحديث بيانات العميل');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $branchId = $request->user()?->branch_id ?? 1;
            $this->service->delete((int)$id, $branchId);
            return $this->successResponse(null, 'تم حذف العميل بنجاح', 204);
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل حذف العميل');
        }
    }
}
