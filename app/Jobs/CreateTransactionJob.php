<?php

namespace App\Jobs;

use App\Enums\TransactionStatus;
use App\Events\TransferMoneySuccess;
use App\Models\Transactions;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CreateTransactionJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;        // retry on deadlock
    public int $backoff = 2;      // small backoff for retries
    public int $timeout = 30;

    public function __construct(
        public string $temp_UUID,
        public int $sender_id,
        public int $receiver_id,
        public float $amount
    ) {}

    public function handle(): void
    {
        DB::transaction(function () {

            $senderId   = $this->sender_id;
            $receiverId = $this->receiver_id;
            $amount     = $this->amount;

            $commissionRate = 1.5;
            $amountWithFees = $amount + ($amount * $commissionRate)/100;
            $commissionFees = $amountWithFees - $amount;

            /**
             * STEP 1: Lock users in deterministic order (prevents DEADLOCKS)
             */
            $ids = [$senderId, $receiverId];
            sort($ids);

            $users = DB::table('users')
                ->whereIn('id', $ids)
                ->lockForUpdate() // SELECT ... FOR UPDATE
                ->get()
                ->keyBy('id');

            $sender   = $users[$senderId];
            $receiver = $users[$receiverId];

            /**
             * STEP 2: Double Check sender has enough balance ( we did this in validation layer)
             */
            if ($sender->balance < $amount) {
                throw new RuntimeException("Insufficient balance.");
            }

            /**
             * STEP 3: Debit & Credit atomically
             * Using DB::raw to avoid race conditions.
             */
            DB::table('users')
                ->where('id', $senderId)
                ->update([
                    'balance' => DB::raw("balance - {$amountWithFees}")
                ]);

            DB::table('users')
                ->where('id', $receiverId)
                ->update([
                    'balance' => DB::raw("balance + {$amount}")
                ]);

            /**
             * STEP 4: Create transaction record
             */
            $transaction = Transactions::query()->create([
                'sender_id'       => $senderId,
                'receiver_id'     => $receiverId,
                'amount'          => $amount,
                'commission_fees' => $commissionFees,
                'status'          => TransactionStatus::SUCCESS->value
            ]);

            /**
             * STEP 5: Fire event
             */
            event(new TransferMoneySuccess(transaction: $transaction,requestId: $this->temp_UUID,));

        }, 5); // DB transaction retry count on deadlocks
    }
}
