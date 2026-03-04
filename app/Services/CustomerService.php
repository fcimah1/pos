<?php

namespace App\Services;

use App\Repository\Interface\CustomerRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    public function __construct(private CustomerRepositoryInterface $repo) {}

    public function getAll(int $branchId, array $filters = [])
    {
        return $this->repo->getAll($branchId, $filters);
    }

    public function findById(int $id, int $branchId)
    {
        return $this->repo->findById($id, $branchId);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $customer = $this->repo->create($data);

            if (isset($data['addresses']) && is_array($data['addresses'])) {
                foreach ($data['addresses'] as $address) {
                    $customer->addresses()->create($address);
                }
            }

            return $customer->load('addresses');
        });
    }

    public function update(int $id, array $data)
    {
        return $this->repo->update($id, $data);
    }

    public function search(string $query, int $branchId)
    {
        return $this->repo->search($query, $branchId);
    }

    public function delete(int $id, int $branchId)
    {
        return $this->repo->delete($id, $branchId);
    }
}
