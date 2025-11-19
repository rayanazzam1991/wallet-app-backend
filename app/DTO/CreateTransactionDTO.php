<?php

namespace App\DTO;

readonly class CreateTransactionDTO
{
    public function __construct(
        public int   $senderId,
        public int   $receiverId,
        public float $amount,
    ) {}
}
