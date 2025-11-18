<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transactions extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionsFactory> */
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'commission_fees',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commission_fees' => 'decimal:2',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(related: User::class, foreignKey: 'sender_id', ownerKey: 'id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(related: User::class, foreignKey: 'receiver_id', ownerKey: 'id');
    }
}
