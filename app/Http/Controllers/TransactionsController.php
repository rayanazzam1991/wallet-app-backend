<?php

namespace App\Http\Controllers;

use App\Enums\TransactionStatus;
use App\Helpers\ApiResponse;
use App\Helpers\PaginationResource;
use App\Http\Requests\GetTransactionsListRequest;
use App\Http\Requests\TransferMoneyRequest;
use App\Models\User;
use App\Services\TransactionService;
use App\DTO\TransactionFilterDTO;
use App\DTO\CreateTransactionDTO;
use App\Http\Resources\TransactionsListResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TransactionsController extends Controller
{
    public function __construct(
        private readonly TransactionService $transactionService
    ) {}

    public function index(GetTransactionsListRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $filterDto = new TransactionFilterDTO(
            type: $request->validated('type')
        );

        $transactions = $this->transactionService->listTransactions($user, $filterDto);

        return ApiResponse::sendResponse(
            data: TransactionsListResource::collection($transactions),
            pagination: PaginationResource::make($transactions)
        );
    }

    public function create(TransferMoneyRequest $request): JsonResponse
    {

        $requestId = (string) Str::uuid();

        $dto = new CreateTransactionDTO(
            tempUUID: $requestId,
            senderId: Auth::id(),
            receiverId: $request->validated('receiver_id'),
            amount: (float) $request->validated('amount')
        );

        $this->transactionService->createTransaction($dto);

        return ApiResponse::sendResponse(
            [
                'message' => 'Transfer started',
                'request_id' => $requestId,
                'status' => TransactionStatus::PENDING->value,
            ],code: 201
        );
//        return response()->json([
//            'message' => 'Transfer started',
//            'request_id' => $requestId,
//            'status' => TransactionStatus::PENDING->value,
//        ], 201);
    }
}
