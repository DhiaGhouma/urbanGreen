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
            // Add complexity level for experience matching
            // Note: latitude and longitude already exist in the table
            if (!Schema::hasColumn('green_spaces', 'complexity_level')) {
                $table->enum('complexity_level', ['débutant', 'intermédiaire', 'expert'])
                      ->default('débutant')
                      ->after('type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('green_spaces', function (Blueprint $table) {
            if (Schema::hasColumn('green_spaces', 'complexity_level')) {
                $table->dropColumn('complexity_level');
            }
        });
    }
};
