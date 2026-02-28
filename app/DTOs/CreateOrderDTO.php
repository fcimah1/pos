<?php

namespace App\DTOs;

class CreateOrderDTO
{
    public function __construct(
        public int $branch_id,
        public int $user_id,
        public string $type,
        public ?string $table_number,
        public ?int $customer_id,
        public ?int $delivery_address_id,
        public ?int $delivery_person_id,
        public float $discount_amount,
        public float $delivery_charge,
        public ?string $notes,
        public ?string $status,
        public ?string $payment_status,
        public array $items
    ) {}

    public static function fromArray(array $data): self
    {
        $items = array_map(
            fn ($i) => new OrderItemDTO(
                isset($i['product_id']) ? (int)$i['product_id'] : null, 
                (int)$i['quantity'], 
                (float)$i['unit_price'],
                $i['product_name'] ?? null
            ),
            $data['items'] ?? []
        );

        return new self(
            (int)$data['branch_id'],
            (int)$data['user_id'],
            $data['type'],
            $data['table_number'] ?? null,
            $data['customer_id'] ?? null,
            $data['delivery_address_id'] ?? null,
            $data['delivery_person_id'] ?? null,
            (float)($data['discount_amount'] ?? 0),
            (float)($data['delivery_charge'] ?? 0),
            $data['notes'] ?? null,
            $data['status'] ?? null,
            $data['payment_status'] ?? null,
            $items
        );
    }
}
