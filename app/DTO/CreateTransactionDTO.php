<?php

namespace App\DTO;

readonly class CreateTransactionDTO
{
    public function __construct(
        public string   $tempUUID,
        public int   $senderId,
        public int   $receiverId,
        public float $amount,
    ) {}
}
