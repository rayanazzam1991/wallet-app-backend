<?php

namespace App\Services;

use App\DTO\CreateTransactionDTO;
use App\DTO\TransactionFilterDTO;
use App\Jobs\CreateTransactionJob;
use App\Repositories\TransactionRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\User;

readonly class TransactionService
{
    public function __construct(
        private TransactionRepository $transactionRepository
    ) {}

    public function listTransactions(User $user, TransactionFilterDTO $filter): LengthAwarePaginator
    {
        return $this->transactionRepository->getUserTransactions($user, $filter->type);
    }

    public function createTransaction(CreateTransactionDTO $dto): void
    {
        // dispatch async job
        CreateTransactionJob::dispatch(
            $dto->senderId,
            $dto->receiverId,
            $dto->amount
        );
    }
}
