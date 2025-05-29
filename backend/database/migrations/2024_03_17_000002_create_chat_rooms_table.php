<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['active', 'closed'])->default('active');
            $table->timestamp('closed_at')->nullable();
            $table->string('close_reason')->nullable();
            $table->foreignId('last_message_id')->nullable()->constrained('chat_messages')->onDelete('set null');
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_rooms');
    }
}; 