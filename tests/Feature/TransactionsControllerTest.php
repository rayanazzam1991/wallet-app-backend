<?php

use App\Models\Transactions;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\getJson;

it('should prevent sending money form non-authenticated user', function () {

    $response = getJson('/api/transactions', []);
    $response->assertStatus(401);

});

it('should prevent sending money for a non-existing user', function () {

    $user = User::factory()->create();

    $response = actingAs($user)->postJson('/api/transactions', [
        'receiver_id' => 123,
    ]);

    $response->assertStatus(422);
});

it('should prevent sending money without amount or receiver_id', function () {

    $sender = User::factory()->create();
    $receiver = User::factory()->create();

    $response = actingAs($sender)->postJson('/api/transactions', [
        'receiver_id' => $receiver->id,
        'amount' => 12.54,
    ]);

    $response->assertStatus(200);
});

// Money Transfer
it('should sending money for authenticated sender to exited receiver with commission', function () {

    $sender = User::factory()->create([
        'balance'=>1000
    ]);
    $receiver = User::factory()->create();

    $response = actingAs($sender)->postJson('/api/transactions', [
        'receiver_id' => $receiver->id,
        'amount' => 101.5,
    ])->assertCreated();

    assertDatabaseHas(Transactions::class,[
        'sender_id'=>$sender->id,
        'receiver_id' => $receiver->id,
        'amount' => 101.5,
        'commission_fees'=>1.5
    ]);

});

it('should get list of transactions for a sender', function () {});

it('should get list of transactions for a receiver', function () {});
