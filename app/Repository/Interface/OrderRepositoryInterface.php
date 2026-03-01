<?php

namespace App\Repository\Interface;

use App\DTOs\CreateOrderDTO;

interface OrderRepositoryInterface
{
    public function getAll(int $branchId, array $filters = []);
    public function getSuspended(int $branchId);
    public function findById(int $id, int $branchId);
    public function create(CreateOrderDTO $dto);
    public function update(int $id, CreateOrderDTO $dto);
    public function updateStatus(int $id, string $status, int $branchId);
    public function updateTableAvailability(?string $tableNumber, int $branchId);
    public function settleDriverOrders(int $driverId, array $orderIds): bool;
}
