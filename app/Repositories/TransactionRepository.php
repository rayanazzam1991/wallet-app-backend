<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Enums\TransactionType;

class TransactionRepository
{
    public function getUserTransactions(User $user, ?string $type): LengthAwarePaginator
    {
        $query = match ($type) {
            TransactionType::SENT->value => $user->sentTransactions(),
            TransactionType::RECEIVED->value => $user->receivedTransactions(),
            default => $user->allTransactions(),
        };

        return $query->orderBy('id', 'desc')->paginate(10);
    }
}
