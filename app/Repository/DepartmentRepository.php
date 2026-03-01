<?php

namespace App\Repository;

use App\Models\Department;
use App\Repository\Interface\DepartmentRepositoryInterface;
use Illuminate\Support\Collection;

class DepartmentRepository implements DepartmentRepositoryInterface
{
    public function getAll(): Collection
    {
        return Department::all();
    }

    public function find(int $id): ?Department
    {
        return Department::find($id);
    }
}
