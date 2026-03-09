<?php

namespace App\Repository\Interface;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;

    public function create(array $data): User;

    public function find(int $id): ?User;
    public function getByBranchId(int $branchId): \Illuminate\Support\Collection;
    public function all(): \Illuminate\Support\Collection;
    public function update(int $id, array $data): ?User;
    public function delete(int $id): bool;
}
