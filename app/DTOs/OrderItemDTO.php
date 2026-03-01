<?php

namespace App\DTOs;

readonly class OrderItemDTO
{
    public function __construct(
        public ?int $product_id,
        public string $product_name,
        public float $unit_price,
        public int $quantity,
        public float $subtotal
    ) {}
}
