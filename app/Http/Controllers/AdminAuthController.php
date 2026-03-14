<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    /**
     * عرض صفحة مصادقة المدير
     */
    public function showAdminAuthForm()
    {
        return view('admin.auth');
    }

    /**
     * مصادقة المدير
     */
    public function authenticate(Request $request)
    {
        $request->validate([
            'admin_password' => 'required|string',
        ]);

        // البحث عن مدير نظام
        $adminUser = \App\Models\User::whereHas('designation', function ($query) {
            $query->where('name', 'مدير نظام');
        })->first();

        if (!$adminUser || !Hash::check($request->admin_password, $adminUser->password)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'كلمة مرور المدير غير صحيحة',
                    'errors' => ['admin_password' => ['كلمة مرور المدير غير صحيحة']]
                ], 422);
            }
            
            throw ValidationException::withMessages([
                'admin_password' => ['كلمة مرور المدير غير صحيحة'],
            ]);
        }

        // تخزين مصادقة المدير في الجلسة
        session(['admin_authenticated' => true, 'admin_authenticated_at' => now()]);
        
        // التوجيه للصفحة المطلوبة أصلاً
        $redirectUrl = session('admin_auth_redirect', route('Dashboard'));
        session()->forget('admin_auth_redirect');

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'تم المصادقة بنجاح',
                'redirect' => $redirectUrl
            ]);
        }

        return redirect($redirectUrl);
    }

    /**
     * تسجيل خروج المدير
     */
    public function logout(Request $request)
    {
        session()->forget(['admin_authenticated', 'admin_authenticated_at']);
        return redirect()->route('login');
    }
}
