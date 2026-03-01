<?php

namespace App\Services;

use App\Repository\Interface\CategoryRepositoryInterface;
use App\DTOs\CategoryDTO;

class CategoryService
{
    public function __construct(private CategoryRepositoryInterface $repo) {}

    public function getAll(int $branchId)
    {
        return $this->repo->getAll($branchId);
    }

    public function findById(int $id, int $branchId)
    {
        return $this->repo->findById($id, $branchId);
    }

    public function create(array $data)
    {
        $dto = CategoryDTO::fromArray($data);
        return $this->repo->create($dto);
    }

    public function update(int $id, array $data)
    {
        $dto = CategoryDTO::fromArray($data);
        $branchId = $data['branch_id'] ?? 1;
        return $this->repo->update($id, $dto, $branchId);
    }

    public function delete(int $id, int $branchId)
    {
        return $this->repo->delete($id, $branchId);
    }
}
