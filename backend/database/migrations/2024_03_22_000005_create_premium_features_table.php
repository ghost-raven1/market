<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('premium_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advertisement_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // boost, highlight, top_placement, vip
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->json('settings')->nullable(); // Дополнительные настройки (цвет, позиция и т.д.)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('premium_features');
    }
}; 