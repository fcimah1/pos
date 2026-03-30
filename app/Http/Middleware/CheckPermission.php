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
        error_log("[DEBUG] CheckPermission handle start");
        // التحقق من وجود مستخدم مسجل الدخول
        if (!Auth::check()) {
            error_log("[DEBUG] Auth check failed");
            if ($request->expectsJson()) {
                return response()->json(['message' => 'غير مصرح لك بالوصول'], 401);
            }

            return redirect()->guest(route('login'));
        }

        $user = Auth::user();

        if (!$user instanceof User) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'غير مصرح لك بالوصول'], 401);
            }

            return redirect()->guest(route('login'));
        }

        error_log("[DEBUG] user HAS check for $permission");
        if (!$user->hasPermission($permission)) {
            error_log("[DEBUG] user DOES NOT have permission");
            if ($request->expectsJson()) {
                error_log("[DEBUG] returning json response");
                return response()->json(['message' => 'ليس لديك صلاحية لتنفيذ هذا الإجراء'], 403);
            }

            if (session('admin_authenticated')) {
                return $next($request);
            }

            session(['admin_auth_redirect' => $request->fullUrl()]);

            return redirect()->route('admin.auth');
        }

        return $next($request);
    }
}
