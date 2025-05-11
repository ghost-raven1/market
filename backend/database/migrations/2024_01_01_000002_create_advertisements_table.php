<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // banner, category, email
            $table->string('title');
            $table->text('content');
            $table->string('image_url')->nullable();
            $table->string('target_url')->nullable();
            $table->json('targeting')->nullable(); // Настройки таргетинга
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->decimal('budget', 10, 2);
            $table->decimal('spent', 10, 2)->default(0);
            $table->string('status')->default('pending'); // pending, active, completed, rejected
            $table->json('statistics')->nullable(); // Статистика показов, кликов и т.д.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
}; 