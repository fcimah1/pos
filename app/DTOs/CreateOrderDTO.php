<?php

namespace App\DTOs;

readonly class CreateOrderDTO
{
    /**
     * @param OrderItemDTO[] $items
     */
    public function __construct(
        public int $branch_id,
        public int $user_id,
        public string $type,
        public ?string $table_number,
        public ?int $shift_id,
        public ?int $customer_id,
        public ?int $delivery_address_id,
        public ?int $delivery_person_id,
        public string $status,
        public string $payment_status,
        public float $subtotal,
        public float $tax_amount,
        public float $discount_amount,
        public float $delivery_charge,
        public float $total_amount,
        public ?string $notes,
        public array $items
    ) {}

    public static function fromArray(array $data): self
    {
        $items = [];
        foreach ($data['items'] ?? [] as $item) {
            $unitPrice = (float)($item['unit_price'] ?? 0);
            $qty = (int)($item['quantity'] ?? 1);
            $items[] = new OrderItemDTO(
                product_id: $item['product_id'] ?? null,
                product_name: $item['product_name'] ?? 'Unknown',
                unit_price: $unitPrice,
                quantity: $qty,
                subtotal: $unitPrice * $qty
            );
        }

        return new self(
            branch_id: $data['branch_id'] ?? 1,
            user_id: $data['user_id'] ?? 1,
            shift_id: $data['shift_id'] ?? null,
            type: $data['type'] ?? 'takeaway',
            table_number: $data['table_number'] ?? null,
            customer_id: $data['customer_id'] ?? null,
            delivery_address_id: $data['delivery_address_id'] ?? null,
            delivery_person_id: $data['delivery_person_id'] ?? null,
            status: $data['status'] ?? 'suspended',
            payment_status: $data['payment_status'] ?? 'unpaid',
            subtotal: (float)($data['subtotal'] ?? 0),
            tax_amount: (float)($data['tax_amount'] ?? 0),
            discount_amount: (float)($data['discount_amount'] ?? 0),
            delivery_charge: (float)($data['delivery_charge'] ?? 0),
            total_amount: (float)($data['total_amount'] ?? 0),
            notes: $data['notes'] ?? null,
            items: $items
        );
    }
}
