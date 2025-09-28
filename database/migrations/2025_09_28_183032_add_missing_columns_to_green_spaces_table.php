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
        Schema::table('green_spaces', function (Blueprint $table) {
            $table->integer('surface')->nullable()->after('type');
            $table->decimal('latitude', 10, 7)->nullable()->after('surface');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('status')->default('proposé')->after('longitude');
            $table->json('photos_before')->nullable()->after('status');
            $table->json('photos_after')->nullable()->after('photos_before');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('green_spaces', function (Blueprint $table) {
            $table->dropColumn([
                'surface',
                'latitude', 
                'longitude',
                'status',
                'photos_before',
                'photos_after'
            ]);
        });
    }
};
