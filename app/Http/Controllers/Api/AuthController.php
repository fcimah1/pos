<?php

namespace App\Http\Controllers\Api;

use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController
{
    public function __construct(private AuthService $service) {}

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $result = $this->service->login($credentials['email'], $credentials['password']);

        if (!$result) {
            return response()->json(['message' => 'بيانات الدخول غير صحيحة'], 401);
        }

        return response()->json($result);
    }

    public function logout(Request $request)
    {
        $this->service->logout($request->user());
        return response()->json(['message' => 'تم تسجيل الخروج بنجاح']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user()->load(['role', 'designation', 'department']));
    }
}
