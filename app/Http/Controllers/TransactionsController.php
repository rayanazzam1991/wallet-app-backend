<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferMoneyRequest;
use App\Jobs\CreateTransactionJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TransactionsController extends Controller
{
    public function index() {}

    public function create(TransferMoneyRequest $request): JsonResponse
    {
        Log::info("here");
        $senderId = Auth::id();
        $receiverId = $request->receiver_id;
        $amount = $request->amount;

        CreateTransactionJob::dispatch($senderId, $receiverId, $amount);

        return response()->json([
            'message' => 'Transfer created Successfully!',
        ], 201);

    }
}
