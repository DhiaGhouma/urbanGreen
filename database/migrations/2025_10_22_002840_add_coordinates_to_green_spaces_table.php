<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('green_spaces', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('green_spaces', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('location');
            }
            if (!Schema::hasColumn('green_spaces', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
        });
    }

    public function down(): void
    {
        Schema::table('green_spaces', function (Blueprint $table) {
            if (Schema::hasColumn('green_spaces', 'latitude')) {
                $table->dropColumn('latitude');
            }
            if (Schema::hasColumn('green_spaces', 'longitude')) {
                $table->dropColumn('longitude');
            }
        });
    }
};
