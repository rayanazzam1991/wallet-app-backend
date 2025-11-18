<?php

namespace App\Jobs;

use App\Events\TransferMoneySuccess;
use App\Models\Transactions;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateTransactionJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;  // retry on deadlock

    public int $timeout = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $sender_id,
        public int $receiver_id,
        public float $amount,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function () {

            $senderId = $this->sender_id;
            $receiverId = $this->receiver_id;

            // total amount with fees
            $amount = $this->amount;

            // TODO refactor this to separated function
            $commissionFeesRatioPercentage = 1.5;
            $amountWithOutFees = (100 * $amount) / (100 + $commissionFeesRatioPercentage);
            $commissionFeesAmount = $amount - $amountWithOutFees;

            $transaction = Transactions::query()->create([
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
                'amount' => $amount,
                'commission_fees' => $commissionFeesAmount,
            ]);

            // doing those operation in one query will prevent the race condition and deadlock inside the transaction
            DB::table('users')
                ->where('id', $senderId)
                ->update(['balance' => DB::raw("balance - {$amount}")]);

            DB::table('users')
                ->where('id', $receiverId)
                ->update(['balance' => DB::raw("balance + {$amountWithOutFees}")]);


            // broadcast the transfer creating event to users
            event(new TransferMoneySuccess($transaction));
        }, 3);

    }
}
