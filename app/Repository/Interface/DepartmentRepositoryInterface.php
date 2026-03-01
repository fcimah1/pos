<?php

namespace App\Repository\Interface;

use App\Models\Department;
use Illuminate\Support\Collection;

interface DepartmentRepositoryInterface
{
    public function getAll(): Collection;
    public function find(int $id): ?Department;
}
