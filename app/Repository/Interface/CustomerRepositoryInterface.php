<?php

namespace App\Repository\Interface;

interface CustomerRepositoryInterface
{
    public function getAll(int $branchId, array $filters = []);
    public function findById(int $id, int $branchId);
    public function create(array $data);
    public function update(int $id, array $data);
    public function search(string $query, int $branchId);
    public function delete(int $id, int $branchId);
}
