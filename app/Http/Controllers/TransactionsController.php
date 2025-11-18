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

            /** @var User $sender */
            $sender = Auth::user();
            $receiver = User::query()->where('id', $request->receiver_id)->first();

            $amount = $request->amount;

            // TODO refactor this to separated function
            $commissionFeesRatioPercentage = 1.5;
            $amountWithFees = (100 * $amount) / (100 + $commissionFeesRatioPercentage);
            $commissionFeesAmount = $amount - $amountWithFees;

            Transactions::query()->create([
                'sender_id' => $sender->id,
                'receiver_id' => $request->receiver_id,
                'amount' => $amount,
                'commission_fees' => $commissionFeesAmount,
            ]);

            $sender->decrement('balance',$amount);
            $receiver->increment('balance',$amount - $commissionFeesAmount);

        });

        return response()->json([
            'message' => 'Transfer created Successfully!',
        ], 201);

    }
}
