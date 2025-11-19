<?php

namespace App\Http\Controllers;

use App\Actions\GetAuthenticatedUserAction;
use App\Actions\LoginUserAction;
use App\Helpers\ApiResponse;
use App\Http\Requests\LoginUserRequest;
use App\Http\Resources\UserDetailsResource;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function login(
        LoginUserRequest $request,
        LoginUserAction $action
    ): JsonResponse
    {
        $result = $action->execute(
            email: $request->validated('email'),
            password: $request->validated('password')
        );

        // Attach token info to the user resource
        $result['user']->token = $result['token'];
        $result['user']->token_type = $result['token_type'];

        return ApiResponse::sendResponse(
            data: $result['user']
        );
    }

    public function me(
        GetAuthenticatedUserAction $action
    ): JsonResponse
    {
        $user = $action->execute();

        return ApiResponse::sendResponse(
            data: UserDetailsResource::make($user)
        );
    }
}
