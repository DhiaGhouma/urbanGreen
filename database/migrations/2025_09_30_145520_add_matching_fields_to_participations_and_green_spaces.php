<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     public function up()
    {
        Schema::table('participations', function (Blueprint $table) {
            $table->json('preferences')->nullable()->after('statut');
        });

        Schema::table('green_spaces', function (Blueprint $table) {
            $table->json('activities')->nullable()->after('description');
        });
    }

    public function down()
    {
        Schema::table('participations', function (Blueprint $table) {
            $table->dropColumn('preferences');
        });

        Schema::table('green_spaces', function (Blueprint $table) {
            $table->dropColumn('activities');
        });
    }
};
