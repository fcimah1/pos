<?php

namespace App\DTOs;

readonly class ProductVariationDTO
{
    public function __construct(
        public ?int $id,
        public ?string $size_name,
        public float $price,
        public ?string $barcode
    ) {}
}
