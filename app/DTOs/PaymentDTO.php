<?php

namespace App\DTOs;

class PaymentDTO
{
    public function __construct(
        public int $order_id,
        public int $shift_id,
        public string $method,
        public float $amount,
        public ?string $reference_number,
        public ?string $notes
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            (int)$data['order_id'],
            (int)($data['shift_id'] ?? 0),
            $data['method'],
            (float)$data['amount'],
            $data['reference_number'] ?? null,
            $data['notes'] ?? null
        );
    }
}
