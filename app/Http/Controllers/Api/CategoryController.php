<?php

namespace App\Http\Controllers\Api;

use App\Services\CategoryService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Throwable;

class CategoryController
{
    use ApiResponseTrait;

    public function __construct(private CategoryService $service) {}

    public function index(Request $request)
    {
        try {
            $branchId = $request->user()?->branch_id ?? 1;
            $categories = $this->service->getAll($branchId);
            return $this->successResponse($categories);
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل جلب التصنيفات');
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $data['branch_id'] = $request->user()?->branch_id ?? 1;
            $category = $this->service->create($data);
            return $this->successResponse($category, 'تم إنشاء التصنيف بنجاح', 201);
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل إنشاء التصنيف');
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $branchId = $request->user()?->branch_id ?? 1;
            $category = $this->service->findById((int)$id, $branchId);
            return $this->successResponse($category);
        } catch (Throwable $e) {
            return $this->handleException($e, 'التصنيف غير موجود');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $data = $request->all();
            $data['branch_id'] = $request->user()?->branch_id ?? 1;
            $category = $this->service->update((int)$id, $data);
            return $this->successResponse($category, 'تم تحديث التصنيف بنجاح');
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل تحديث التصنيف');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $branchId = $request->user()?->branch_id ?? 1;
            $this->service->delete((int)$id, $branchId);
            return $this->successResponse(null, 'تم حذف التصنيف بنجاح', 200);
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل حذف التصنيف');
        }
    }
}
