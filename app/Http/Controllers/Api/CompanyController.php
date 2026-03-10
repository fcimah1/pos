<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCompanySettingsRequest;
use App\Services\SettingService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class CompanyController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly SettingService $settingService
    ) {}

    /**
     * Get all company settings.
     */
    public function index(): JsonResponse
    {
        try {
            $settings = $this->settingService->getAllSettings();
            return $this->successResponse($settings, 'تم جلب إعدادات الشركة بنجاح');
        } catch (Throwable $e) {
            Log::error('Error fetching company settings: ' . $e->getMessage());
            return $this->errorResponse('فشل في جلب إعدادات الشركة');
        }
    }

    /**
     * Update company settings.
     */
    public function update(UpdateCompanySettingsRequest $request): JsonResponse
    {
        try {
            $this->settingService->updateSettings($request->validated());

            return $this->successResponse($this->settingService->getAllSettings(), 'تم تحديث إعدادات الشركة بنجاح');
        } catch (Throwable $e) {
            Log::error('Error updating company settings: ' . $e->getMessage());
            return $this->errorResponse('فشل في تحديث إعدادات الشركة: ' . $e->getMessage());
        }
    }
}
