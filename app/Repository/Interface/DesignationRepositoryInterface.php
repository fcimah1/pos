<?php

namespace App\Repository\Interface;

use App\Models\Designation;
use Illuminate\Support\Collection;

interface DesignationRepositoryInterface
{
    public function getAll(): Collection;
    public function find(int $id): ?Designation;
}
