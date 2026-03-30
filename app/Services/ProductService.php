<?php

namespace App\Services;

use App\Repository\Interface\ProductRepositoryInterface;
use App\DTOs\ProductDTO;

class ProductService
{
    public function __construct(private ProductRepositoryInterface $repo) {}

    public function getAll(int $branchId, array $filters = [])
    {
        return $this->repo->getAll($branchId);
    }

    public function findById(int $id, int $branchId)
    {
        return $this->repo->findById($id, $branchId);
    }

    public function create(array $data)
    {
        $dto = ProductDTO::fromArray($data);
        return $this->repo->create($dto);
    }

    public function update(int $id, array $data)
    {
        $dto = ProductDTO::fromArray($data);
        return $this->repo->update($id, $dto);
    }

    public function delete(int $id, int $branchId)
    {
        return $this->repo->delete($id, $branchId);
    }
}
