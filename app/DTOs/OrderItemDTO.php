<?php

namespace App\DTOs;

class OrderItemDTO
{
    public function __construct(
        public ?int $product_id,
        public int $quantity,
        public float $unit_price,
        public ?string $product_name = null
    ) {}
}
