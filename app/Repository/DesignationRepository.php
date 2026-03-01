<?php

namespace App\Repository;

use App\Models\Designation;
use App\Repository\Interface\DesignationRepositoryInterface;
use Illuminate\Support\Collection;

class DesignationRepository implements DesignationRepositoryInterface
{
    public function getAll(): Collection
    {
        return Designation::all();
    }

    public function find(int $id): ?Designation
    {
        return Designation::find($id);
    }
}
