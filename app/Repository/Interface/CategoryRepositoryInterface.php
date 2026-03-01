<?php

namespace App\Repository\Interface;

use App\DTOs\CategoryDTO;

interface CategoryRepositoryInterface
{
    public function getAll(int $branchId);
    public function findById(int $id, int $branchId);
    public function create(CategoryDTO $dto);
    public function update(int $id, CategoryDTO $dto, int $branchId);
    public function delete(int $id, int $branchId);
}
