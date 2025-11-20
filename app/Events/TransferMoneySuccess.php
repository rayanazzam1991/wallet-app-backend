<?php

namespace App\Events;

use App\Enums\TransactionStatus;
use App\Models\Transactions;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransferMoneySuccess implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Transactions $transaction,public string $requestId)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.'.$this->transaction->sender_id),
            new PrivateChannel('user.'.$this->transaction->receiver_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'request_id'  => $this->requestId,
            'transaction' => $this->transaction->toArray(),
            'status'      => TransactionStatus::SUCCESS->value,
        ];
    }

    /**
     * Event name (optional but recommended)
     */
    public function broadcastAs(): string
    {
        return 'money.transfer.success';
    }
}
