<?php

namespace App\Services;

use App\Repository\Interface\CustomerRepositoryInterface;

class CustomerService
{
    public function __construct(private CustomerRepositoryInterface $repo) {}

    public function getAll(int $branchId, array $filters = [])
    {
        return $this->repo->getAll($branchId, $filters);
    }

    public function findById(int $id, int $branchId)
    {
        return $this->repo->findById($id, $branchId);
    }

    public function create(array $data)
    {
        return $this->repo->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->repo->update($id, $data);
    }

    public function search(string $query, int $branchId)
    {
        return $this->repo->search($query, $branchId);
    }

    public function delete(int $id, int $branchId)
    {
        return $this->repo->delete($id, $branchId);
    }
}
