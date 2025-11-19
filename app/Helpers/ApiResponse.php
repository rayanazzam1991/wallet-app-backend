<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiResponse
{
    public static function sendResponse(
        mixed $data,
        string $message = '',
        bool $isOk = true,
        int $code = 200,
        mixed $pagination = null
    ): JsonResponse {

        $response = [
            'success' => $isOk ?? true,
            'message' => $message ?? 'Success',
            'data' => $data ?? null,
            'pagination' => $pagination ?? null,
        ];
        //        if (env('APP_ENV') != 'production') {
        //            if ($result->exception != null) {
        //                $response['exception'] = $result->exception;
        //            }
        //        }

        return response()->json($response, (int) $code);
    }

    //    public static function sendErrorResponse(?Result $result = null): JsonResponse
    //    {
    //
    //        $response = [
    //            'success' => $result->isOk ?? false,
    //            'error_code' => $result?->code,
    //            'message' => $result->message ?? 'Error',
    //            'data' => $result->result ?? null,
    //            'pagination' => $result->paginate ?? null,
    //        ];
    //        if (env('APP_ENV') != 'production') {
    //            if ($result?->exception != null) {
    //                $response['exception'] = $result->exception;
    //            }
    //        }
    //
    //        return response()->json($response, $result?->code ?? Response::HTTP_BAD_REQUEST);
    //    }
    //
    //    public static function sendSuccessResponse(?SuccessResult $result = null): JsonResponse
    //    {
    //
    //        $response = [
    //            'success' => $result->isOk ?? true,
    //            'error_code' => null,
    //            'message' => $result->message ?? 'Success',
    //            'data' => $result->result ?? null,
    //            'pagination' => $result->paginate ?? null,
    //        ];
    //        if (env('APP_ENV') != 'production') {
    //            if ($result?->exception != null) {
    //                $response['exception'] = $result->exception;
    //            }
    //        }
    //
    //        return response()->json($response, $result?->code ?? Response::HTTP_OK);
    //    }
}
