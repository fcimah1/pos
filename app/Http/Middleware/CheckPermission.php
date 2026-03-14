<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $permission
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        try {
            // التحقق من وجود مستخدم مسجل الدخول
            if (!Auth::check()) {
                // إذا كان طلب API، إرجاع خطأ 401
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'غير مصرح لك بالوصول'], 401);
                }
                // إذا كان طلب web، توجيه لصفحة تسجيل الدخول
                return redirect()->guest(route('login'));
            }

            $user = Auth::user();

            if (!$user instanceof User) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'غير مصرح لك بالوصول'], 401);
                }
                return redirect()->guest(route('login'));
            }

            // التحقق من الصلاحيات
            if (!$user->hasPermission($permission)) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'ليس لديك صلاحية لتنفيذ هذا الإجراء'], 403);
                }
                
                // التحقق مما إذا كان المستخدم مصادق كمدير
                if (session('admin_authenticated')) {
                    return $next($request);
                }
                
                // تخزين الصفحة المطلوبة في الجلسة للعودة إليها بعد المصادقة
                session(['admin_auth_redirect' => $request->fullUrl()]);
                
                // توجيه لصفحة مصادقة المدير
                return redirect()->route('admin.auth');
            }

            return $next($request);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'CheckPermission Middleware Exception',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}
