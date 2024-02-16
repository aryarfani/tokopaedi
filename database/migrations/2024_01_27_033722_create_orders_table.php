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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique();
            $table->foreignId('user_id')->constrained();
            $table->integer('total_price');
            $table->string('status');
            $table->string('midtrans_payment_type')->nullable();
            $table->string('midtrans_payment_url')->nullable();
            $table->string('midtrans_snap_token')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
