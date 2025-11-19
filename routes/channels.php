<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(['middleware' => 'auth:sanctum']);

Broadcast::channel('user.{id}', function ($user, $id) {
    \Log::info('Broadcast auth check', [
        'auth_user_id' => $user->id,
        'channel_id' => $id,
    ]);
    return (int) $user->id === (int) $id;
});
