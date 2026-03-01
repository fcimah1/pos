<?php

namespace App\Repository;

use App\Models\Customer;
use App\Repository\Interface\CustomerRepositoryInterface;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function getAll(int $branchId, array $filters = [])
    {
        return Customer::where('branch_id', $branchId)
            ->with('addresses')
            ->when(isset($filters['search']), function ($q) use ($filters) {
                $search = $filters['search'];
                $q->where(function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->paginate(50);
    }

    public function findById(int $id, int $branchId)
    {
        return Customer::where('branch_id', $branchId)->with('addresses')->findOrFail($id);
    }

    public function create(array $data)
    {
        return Customer::create($data);
    }

    public function update(int $id, array $data)
    {
        $customer = Customer::findOrFail($id);
        $customer->update($data);
        return $customer;
    }

    public function search(string $query, int $branchId)
    {
        return Customer::where('branch_id', $branchId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('phone', 'like', "%{$query}%")
                    ->orWhereHas('addresses', function ($addr) use ($query) {
                        $addr->where('address_line_1', 'like', "%{$query}%")
                            ->orWhere('address_line_2', 'like', "%{$query}%");
                    });
            })
            ->with('addresses')
            ->limit(10)
            ->get();
    }

    public function delete(int $id, int $branchId)
    {
        return Customer::where('branch_id', $branchId)->where('id', $id)->delete();
    }
}
