<?php

use App\Events\TransferMoneySuccess;
use App\Jobs\CreateTransactionJob;
use App\Models\Transactions;
use App\Models\User;

use Illuminate\Support\Facades\Event;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

it('should prevent sending money form non-authenticated user', function () {

    $response = postJson('/api/transactions', []);
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

    $response = actingAs($sender)->postJson('/api/transactions', []);

    $response->assertStatus(422);
});
it('should prevent sending money if sender has low balance', function () {

    $sender = User::factory()->create([
        'balance'=>100
    ]);
    $receiver = User::factory()->create();

    $response = actingAs($sender)->postJson('/api/transactions', [
        'receiver_id'=>$receiver->id,
        'amount'=>900
    ]);

    $response->assertStatus(422);
});
it('should sending money for authenticated sender to exited receiver with commission', function () {

    Event::fake();

    $sender = User::factory()->create(['balance' => 1000]);
    $receiver = User::factory()->create();

    // dispatch synchronously for the test
    CreateTransactionJob::dispatchSync(
        sender_id: $sender->id,
        receiver_id: $receiver->id,
        amount: 100
    );

    // DB assertions
    assertDatabaseHas('transactions', [
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'amount' => 100,
        'commission_fees' => 1.5,
    ]);

    // event assertion
    Event::assertDispatched(TransferMoneySuccess::class, function ($event) use ($sender, $receiver) {
        return $event->transaction->sender_id === $sender->id
            && $event->transaction->receiver_id === $receiver->id
            && $event->transaction->amount == 100;
    });

});
it('should debit the sender and credit the receiver when we make a money transfer', function () {

    Event::fake();

    $senderCurrentBalance = 1000;
    $receiverCurrentBalance = 1000;
    $transferredAmount = 100;
    $commissionFeesPercentage = 1.5;

    $sender = User::factory()->create([
        'balance' => 1000,
    ]);

    $receiver = User::factory()->create([
        'balance' => $receiverCurrentBalance,
    ]);

    // dispatch synchronously for the test
    CreateTransactionJob::dispatchSync(
        sender_id: $sender->id,
        receiver_id: $receiver->id,
        amount: 100
    );

    $response = actingAs($sender)->postJson('/api/transactions', [
        'receiver_id' => $receiver->id,
        'amount' => 100,
    ])->assertCreated();

    assertDatabaseHas(Transactions::class, [
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'amount' => 100,
        'commission_fees' => 1.5,
    ]);
    assertDatabaseHas(User::class, [
        'id' => $sender->id,
        'balance' => $senderCurrentBalance - (100 + $commissionFeesPercentage) * ($transferredAmount / 100),
    ]);

    assertDatabaseHas(User::class, [
        'id' => $receiver->id,
        'balance' => $receiverCurrentBalance + $transferredAmount,
    ]);
});
it('should get list of transactions', function () {

    $sender = User::factory()->create();

    Transactions::factory(10)->create([
        'sender_id' => $sender->id,
    ]);

    $response = actingAs($sender)->getJson('/api/transactions');

    $response->assertJsonStructure([
        'data' => [
            [
                'id',
                'sender',
                'receiver',
                'amount',
                'created_at',
            ],
        ],
    ]);
});
