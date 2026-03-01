<?php

namespace App\Repository\Interface;

use App\Models\Table;
use Illuminate\Support\Collection;

interface TableRepositoryInterface
{
    public function getAll(int $branchId): Collection;
    public function find(int $id): ?Table;
    public function update(int $id, array $data): Table;
}
