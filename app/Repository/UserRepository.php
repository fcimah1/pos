<?php

namespace App\Repository;

use App\Repository\Interface\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function find(int $id): ?User
    {
        return User::find($id);
    }

    public function getByBranchId(int $branchId): \Illuminate\Support\Collection
    {
        return User::where('branch_id', $branchId)
            ->with(['designation', 'department'])
            ->get();
    }
}
