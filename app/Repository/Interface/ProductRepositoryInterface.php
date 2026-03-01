<?php

namespace App\Repository\Interface;

use App\DTOs\ProductDTO;

interface ProductRepositoryInterface
{
    public function getAll(int $branchId);
    public function findById(int $id);
    public function create(ProductDTO $dto);
    public function update(int $id, ProductDTO $dto);
    public function delete(int $id, int $branchId);
}
