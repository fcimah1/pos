<?php

namespace App\DTOs;

class UserDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public int $branch_id,
        public int $role_id,
        public bool $is_active
    ) {}

    public static function fromModel($user): self
    {
        return new self(
            $user->id,
            $user->name,
            $user->email,
            $user->branch_id,
            $user->role_id,
            (bool)$user->is_active
        );
    }
}
