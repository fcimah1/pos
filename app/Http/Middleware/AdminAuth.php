<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // التحقق من وجود مصادقة المدير في الجلسة
        if (!session('admin_authenticated')) {
            return redirect()->route('admin.auth');
        }

        // التحقق من انتهاء صلاحية المصادقة (30 دقيقة)
        $authenticatedAt = session('admin_authenticated_at');
        if ($authenticatedAt && now()->diffInMinutes($authenticatedAt) > 30) {
            session()->forget(['admin_authenticated', 'admin_authenticated_at']);
            return redirect()->route('admin.auth')->with('message', 'انتهت صلاحية المصادقة، يرجى إعادة المصادقة');
        }

        return $next($request);
    }
}
