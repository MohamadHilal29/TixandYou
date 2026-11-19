<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eo_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category', 100);
            $table->text('description');
            $table->json('lineup')->nullable();
            $table->date('event_date');
            $table->string('start_time', 20);
            $table->string('end_time', 20);
            $table->string('open_gate', 20)->nullable();
            $table->string('venue_name');
            $table->string('venue_address');
            $table->string('banner_path')->nullable();
            $table->enum('status', ['pending', 'published', 'rejected', 'completed', 'cancelled'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};