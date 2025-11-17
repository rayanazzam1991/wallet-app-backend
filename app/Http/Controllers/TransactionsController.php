<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferMoneyRequest;
use App\Models\Transactions;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TransactionsController extends Controller
{
    public function index() {}

    public function create(TransferMoneyRequest $request): JsonResponse
    {
        /** @var User $sender */
        $sender = Auth::user();

        $amount = $request->amount;

        // TODO refactor this to separated function
        $commissionFeesRatioPercentage = 1.5;
        $amountWithFees = (100 * $amount)/(100 + $commissionFeesRatioPercentage);
        $commissionFeesAmount = $amount - $amountWithFees;

        Transactions::query()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $request->receiver_id,
            'amount' => $amount,
            'commission_fees' => $commissionFeesAmount,
        ]);

        return response()->json([
            'message' => 'Transfer created Successfully!',
        ], 201);

    }
}
