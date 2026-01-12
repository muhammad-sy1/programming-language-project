<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_id')->constrained()->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            $table->string('subject')->nullable();
            $table->boolean('is_sender_read')->default(true);
            $table->boolean('is_receiver_read')->default(true);
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
            
            $table->unique(['apartment_id', 'sender_id', 'receiver_id']);
            $table->index(['sender_id', 'last_message_at']);
            $table->index(['receiver_id', 'last_message_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};