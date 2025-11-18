<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Helpers\ApiResponse;
use App\Helpers\PaginationResource;
use App\Http\Requests\GetTransactionsListRequest;
use App\Http\Requests\TransferMoneyRequest;
use App\Http\Resources\TransactionsListResource;
use App\Jobs\CreateTransactionJob;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TransactionsController extends Controller
{
    public function index(GetTransactionsListRequest $request): JsonResponse
    {

        $transactionType = $request->type;
        /** @var User $user */
        $user = Auth::user();
        $transactions = match ($transactionType) {
            TransactionType::SENT->value => $user->sentTransactions()->orderBy('id', 'desc')->paginate(10),
            TransactionType::RECEIVED->value => $user->receivedTransactions()->orderBy('id', 'desc')->paginate(10),
            default => $user->allTransactions()->orderBy('id', 'desc')->paginate(10),
        };
        $pagination = PaginationResource::make($transactions);

        return ApiResponse::sendResponse(
            data: TransactionsListResource::collection($transactions), pagination: $pagination);

    }

    public function create(TransferMoneyRequest $request): JsonResponse
    {

        $senderId = Auth::id();
        $receiverId = $request->receiver_id;
        $amount = $request->amount;

        CreateTransactionJob::dispatch($senderId, $receiverId, $amount);

        return response()->json([
            'message' => 'Transfer created Successfully!',
        ], 201);

    }
}
