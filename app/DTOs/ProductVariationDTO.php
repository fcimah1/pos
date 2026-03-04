<?php

namespace App\DTOs;

readonly class ProductVariationDTO
{
    public function __construct(
        public ?int $id,
        public ?string $size_name,
        public float $price,
        public ?float $cost_price,
        public ?string $barcode
    ) {}
}
