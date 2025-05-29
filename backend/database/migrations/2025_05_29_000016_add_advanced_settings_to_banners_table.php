<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->json('device_settings')->nullable()->after('position');
            $table->json('schedule_settings')->nullable()->after('device_settings');
            $table->integer('daily_impression_limit')->nullable()->after('schedule_settings');
            $table->json('browser_settings')->nullable()->after('daily_impression_limit');
            $table->boolean('is_adaptive')->default(false)->after('browser_settings');
            $table->json('adaptive_settings')->nullable()->after('is_adaptive');
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn([
                'device_settings',
                'schedule_settings',
                'daily_impression_limit',
                'browser_settings',
                'is_adaptive',
                'adaptive_settings'
            ]);
        });
    }
}; 