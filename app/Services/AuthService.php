<?php

namespace App\Services;

use App\Models\User;
use App\Repository\Interface\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function login(string $email, string $password)
    {
        $user = $this->userRepository->findByEmail($email);

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
