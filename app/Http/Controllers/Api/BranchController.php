<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBranchRequest;
use App\Http\Requests\UpdateBranchRequest;
use App\Services\BranchService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class BranchController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly BranchService $branchService
    ) {}

    /**
     * Display a listing of branches.
     */
    public function index(): JsonResponse
    {
        try {
            return $this->successResponse($this->branchService->getAllBranches(), 'Branches retrieved successfully');
        } catch (Throwable $e) {
            Log::error('Error fetching branches: ' . $e->getMessage());
            return $this->errorResponse('فشل في جلب الفروع');
        }
    }

    /**
     * Store a newly created branch.
     */
    public function store(StoreBranchRequest $request): JsonResponse
    {
        try {
            $branch = $this->branchService->createBranch($request->validated());
            return $this->successResponse($branch, 'تم إنشاء الفرع بنجاح', 201);
        } catch (Throwable $e) {
            Log::error('Error creating branch: ' . $e->getMessage());
            return $this->errorResponse('فشل في إنشاء الفرع: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified branch.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $branch = $this->branchService->getBranchById($id);
            if (!$branch) {
                return $this->errorResponse('الفرع غير موجود', 404);
            }
            return $this->successResponse($branch, 'تم جلب الفرع بنجاح');
        } catch (Throwable $e) {
            Log::error('Error fetching branch: ' . $e->getMessage());
            return $this->errorResponse('فشل في جلب الفرع');
        }
    }

    /**
     * Update the specified branch.
     */
    public function update(UpdateBranchRequest $request, int $id): JsonResponse
    {
        try {
            $branch = $this->branchService->updateBranch($id, $request->validated());
            return $this->successResponse($branch, 'تم تحديث الفرع بنجاح');
        } catch (Throwable $e) {
            Log::error('Error updating branch: ' . $e->getMessage());
            return $this->errorResponse('فشل في تحديث الفرع: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified branch.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->branchService->deleteBranch($id);
            return $this->successResponse(null, 'تم حذف الفرع بنجاح');
        } catch (Throwable $e) {
            Log::error('Error deleting branch: ' . $e->getMessage());
            return $this->errorResponse('فشل في حذف الفرع: ' . $e->getMessage());
        }
    }
}
