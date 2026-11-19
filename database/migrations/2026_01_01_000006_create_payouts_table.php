<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eo_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('event_id')->constrained('events')->restrictOnDelete();
            $table->unsignedBigInteger('gross_amount');
            $table->unsignedBigInteger('platform_commission');
            $table->unsignedBigInteger('net_amount');
            $table->string('bank_name', 50);
            $table->string('bank_account_number', 50);
            $table->string('bank_account_holder');
            $table->enum('status', ['pending', 'completed', 'rejected'])->default('pending');
            $table->string('proof_path')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};