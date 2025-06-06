<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moderations', function (Blueprint $table) {
            $table->id();
            $table->morphs('moderatable'); // Полиморфная связь для объявлений, пользователей и т.д.
            $table->string('type'); // text, image, user
            $table->string('status'); // pending, approved, rejected
            $table->json('check_results')->nullable(); // Результаты проверок
            $table->text('rejection_reason')->nullable(); // Причина отклонения
            $table->foreignId('moderator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('moderated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moderations');
    }
}; 