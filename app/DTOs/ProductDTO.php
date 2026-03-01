<?php

namespace App\DTOs;

readonly class ProductDTO
{
    /**
     * @param ProductVariationDTO[] $variations
     */
    public function __construct(
        public int $branch_id,
        public int $category_id,
        public string $name,
        public string $barcode,
        public float $price,
        public float $cost_price,
        public ?string $icon,
        public bool $is_active,
        public array $variations = []
    ) {}

    public static function fromArray(array $data): self
    {
        $variations = [];
        foreach ($data['variations'] ?? [] as $v) {
            $variations[] = new ProductVariationDTO(
                id: $v['id'] ?? null,
                size_name: $v['size_name'] ?? null,
                price: (float)($v['price'] ?? 0),
                barcode: $v['barcode'] ?? null
            );
        }

        return new self(
            branch_id: (int)($data['branch_id'] ?? 1),
            category_id: (int)($data['category_id'] ?? 1),
            name: $data['name'],
            barcode: $data['barcode'] ?? '',
            price: (float)($data['price'] ?? 0),
            // قبول 'cost' أو 'cost_price' من الـ Frontend
            cost_price: (float)($data['cost_price'] ?? $data['cost'] ?? 0),
            icon: $data['icon'] ?? null,
            is_active: (bool)($data['is_active'] ?? true),
            variations: $variations
        );
    }
}
