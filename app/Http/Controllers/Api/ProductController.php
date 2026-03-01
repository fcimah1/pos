<?php

namespace App\Http\Controllers\Api;

use App\Services\ProductService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Throwable;

class ProductController
{
    use ApiResponseTrait;

    public function __construct(private ProductService $service) {}

    public function index(Request $request)
    {
        try {
            $branchId = $request->user()?->branch_id ?? 1;
            $products = $this->service->getAll($branchId);
            return $this->successResponse($products);
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل جلب المنتجات');
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $data['branch_id'] = $request->user()?->branch_id ?? 1;
            $product = $this->service->create($data);
            return $this->successResponse($product, 'تم إنشاء المنتج بنجاح', 201);
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل إنشاء المنتج');
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $branchId = $request->user()?->branch_id ?? 1;
            $product = $this->service->findById((int)$id, $branchId);
            return $this->successResponse($product);
        } catch (Throwable $e) {
            return $this->handleException($e, 'المنتج غير موجود');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $data = $request->all();
            $data['branch_id'] = $request->user()?->branch_id ?? 1;
            $product = $this->service->update((int)$id, $data);
            return $this->successResponse($product, 'تم تعديل المنتج بنجاح');
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل تعديل المنتج');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $branchId = $request->user()?->branch_id ?? 1;
            $this->service->delete((int)$id, $branchId);
            return $this->successResponse(null, 'تم حذف المنتج بنجاح');
        } catch (Throwable $e) {
            return $this->handleException($e, 'فشل حذف المنتج');
        }
    }
}
