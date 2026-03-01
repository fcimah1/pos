<?php

namespace App\DTOs;

readonly class CategoryDTO
{
    public function __construct(
        public int $branch_id,
        public string $name,
        public string $slug,
        public ?string $description,
        public ?string $icon,
        public bool $is_active
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            branch_id: (int)($data['branch_id'] ?? 1),
            name: $data['name'],
            slug: $data['slug'] ?? \Illuminate\Support\Str::slug($data['name']),
            description: $data['description'] ?? null,
            icon: $data['icon'] ?? null,
            is_active: (bool)($data['is_active'] ?? true)
        );
    }
}
