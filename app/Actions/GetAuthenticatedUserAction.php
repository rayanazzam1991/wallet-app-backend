<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GetAuthenticatedUserAction
{
    public function execute(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }
}
