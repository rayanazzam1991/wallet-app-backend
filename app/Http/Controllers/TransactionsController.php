<?php

namespace App\Http\Controllers;

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
        $dto = new CreateTransactionDTO(
            senderId: Auth::id(),
            receiverId: $request->validated('receiver_id'),
            amount: (float) $request->validated('amount')
        );

        $this->transactionService->createTransaction($dto);

        return response()->json([
            'message' => 'Transfer created Successfully!',
        ], 201);
    }
}
