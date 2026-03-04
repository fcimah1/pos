<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

trait ApiResponseTrait
{
    /**
     * Success Response
     */
    protected function successResponse(mixed $data, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Error Response
     */
    protected function errorResponse(string $message, int $code = 400, mixed $details = null): JsonResponse
    {
        $response = [
            'status' => 'error',
            'message' => $message
        ];

        if ($details) {
            $response['details'] = $details;
        }

        return response()->json($response, $code);
    }

    /**
     * Handle Exceptions automatically
     */
    protected function handleException(Throwable $e, string $customMessage = 'حدث خطأ غير متوقع'): JsonResponse
    {
        Log::error($e->getMessage(), [
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        $code = (int) $e->getCode();
        // تطبيع كود الحالة: أي كود غير HTTP يتم تحويله إلى 500
        if ($code < 100 || $code >= 600) {
            $code = 500;
        }

        return $this->errorResponse(
            $customMessage ?: $e->getMessage(),
            (int)$code,
            config('app.debug') ? $e->getMessage() : null
        );
    }
}
