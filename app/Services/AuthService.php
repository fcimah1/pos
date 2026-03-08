<?php

namespace App\Services;

use App\Models\User;
use App\Repository\Interface\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function login(string $email, string $password)
    {
        // DEBUG: log DB info to diagnose native:serve connection issue
        try {
            Log::info('[AuthService] DB driver: ' . DB::connection()->getDriverName());
            Log::info('[AuthService] DB name: ' . DB::connection()->getDatabaseName());
        } catch (\Throwable $e) {
            Log::error('[AuthService] DB check failed: ' . $e->getMessage());
        }

        $user = $this->userRepository->findByEmail($email);

        Log::info('[AuthService] User found: ' . ($user ? 'YES - ' . $user->email : 'NO'));

        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }

        $token = $user->createToken('pos-token')->plainTextToken;

        return [
            'user' => $user->load(['role', 'designation', 'department']),
            'token' => $token
        ];
    }

    public function logout(User $user)
    {
        $user->tokens()->delete();
        return true;
    }
}
