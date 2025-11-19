<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginUserAction
{
    /**
     * @throws ValidationException
     */
    public function execute(string $email, string $password): array
    {
        /** @var User|null $user */
        $user = User::query()->where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'credentials' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('token')->plainTextToken;

        return [
            'user'        => $user,
            'token'       => $token,
            'token_type'  => 'Bearer',
        ];
    }
}
