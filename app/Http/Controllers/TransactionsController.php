<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferMoneyRequest;
use App\Models\Transactions;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionsController extends Controller
{
    public function index() {}

    public function create(TransferMoneyRequest $request): JsonResponse
    {

        DB::transaction(function () use ($request) {

            $senderId = Auth::id();
            $receiverId =  $request->receiver_id;

            // total amount with fees
            $amount = $request->amount;

            // TODO refactor this to separated function
            $commissionFeesRatioPercentage = 1.5;
            $amountWithOutFees = (100 * $amount) / (100 + $commissionFeesRatioPercentage);
            $commissionFeesAmount = $amount - $amountWithOutFees;

            Transactions::query()->create([
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

        },3);

        return response()->json([
            'message' => 'Transfer created Successfully!',
        ], 201);

    }
}
