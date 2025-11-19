<?php

namespace App\DTO;


readonly class TransactionFilterDTO
{
    public function __construct(
        public ?string $type,
    ) {}

    public function toArray(): array
    {
        return [
            'type' => $this->type,
        ];
    }
}
