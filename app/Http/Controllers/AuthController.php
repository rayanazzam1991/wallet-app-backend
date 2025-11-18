<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\LoginUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function login(LoginUserRequest $request): JsonResponse
    {

        $user = User::query()->where('email', $request->email)->firstOrFail();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'credentials' => ['The provided credentials are incorrect.'],
            ]);
        }
        $token = $user->createToken('token')->plainTextToken;

        $user['token'] = $token;
        $user['token_type'] = 'Bearer';

        return ApiResponse::sendResponse(data: $user);

    }
}
