<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class WebLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Debug: log which DB is being used
        try {
            $dbName = DB::connection()->getDatabaseName();
            $user = \App\Models\User::where('email', $request->email)->first();
            Log::info('[WebLogin] DB in use: ' . $dbName);
            Log::info('[WebLogin] User found: ' . ($user ? 'YES - hash: ' . substr($user->password, 0, 15) : 'NO'));
        } catch (\Throwable $e) {
            Log::error('[WebLogin] DB check failed: ' . $e->getMessage());
        }

        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();
            return redirect('/');
        }

        throw ValidationException::withMessages([
            'email' => ['بيانات الدخول غير صحيحة'],
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
