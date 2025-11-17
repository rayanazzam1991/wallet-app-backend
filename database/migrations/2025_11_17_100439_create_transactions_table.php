<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            // Foreign keys for sender and receiver (users)
            $table->foreignId('sender_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('receiver_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Amount and commission
            $table->decimal('amount', 15, 2);
            $table->decimal('commission_fees', 15, 2)->default(0);

            $table->timestamps();
        });
    }
};
