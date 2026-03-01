<?php

namespace App\Repository;

use App\Models\Category;
use App\DTOs\CategoryDTO;
use App\Repository\Interface\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAll(int $branchId)
    {
        return Category::where('branch_id', $branchId)
            ->withCount('products')
            ->get();
    }

    public function findById(int $id, int $branchId)
    {
        return Category::where('branch_id', $branchId)->findOrFail($id);
    }

    public function create(CategoryDTO $dto)
    {
        return Category::create([
            'branch_id'   => $dto->branch_id,
            'name'        => $dto->name,
            'slug'        => $dto->slug,
            'description' => $dto->description,
            'icon'        => $dto->icon,
            'is_active'   => $dto->is_active,
        ]);
    }

    public function update(int $id, CategoryDTO $dto, int $branchId)
    {
        $category = $this->findById($id, $branchId);
        $category->update([
            'name'        => $dto->name,
            'slug'        => $dto->slug,
            'description' => $dto->description,
            'icon'        => $dto->icon,
            'is_active'   => $dto->is_active,
        ]);
        return $category;
    }

    public function delete(int $id, int $branchId)
    {
        $category = $this->findById($id, $branchId);
        return $category->delete();
    }
}
