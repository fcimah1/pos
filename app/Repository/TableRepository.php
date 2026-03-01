<?php

namespace App\Repository;

use App\Models\Table;
use App\Repository\Interface\TableRepositoryInterface;
use Illuminate\Support\Collection;

class TableRepository implements TableRepositoryInterface
{
    public function getAll(int $branchId): Collection
    {
        return Table::where('branch_id', $branchId)->where('is_active', true)->get();
    }

    public function find(int $id): ?Table
    {
        return Table::find($id);
    }

    public function update(int $id, array $data): Table
    {
        $table = Table::findOrFail($id);
        $table->update($data);
        return $table;
    }
}
