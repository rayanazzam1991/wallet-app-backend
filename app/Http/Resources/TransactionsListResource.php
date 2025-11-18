<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionsListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sender' => UserDetailsResource::make($this->sender),
            'receiver' => UserDetailsResource::make($this->receiver),
            'amount' => $this->amount,
            'created_at' => Carbon::parse($this->created_at)->toDateTimeString()
        ];
    }
}
