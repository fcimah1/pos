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
            ->with(['role', 'designation', 'department'])
            ->get();
    }

    public function all(): \Illuminate\Support\Collection
    {
        return User::with(['role', 'designation' => function($q) {
            $q->withCount('permissions');
        }, 'department'])->get();
    }

    public function update(int $id, array $data): ?User
    {
        $user = User::find($id);
        if ($user) {
            $user->update($data);
            return $user->refresh();
        }
        return null;
    }

    public function delete(int $id): bool
    {
        $user = User::find($id);
        if ($user) {
            return (bool)$user->delete();
        }
        return false;
    }
}
