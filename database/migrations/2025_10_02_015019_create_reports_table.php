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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('green_space_id');

            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('photo')->nullable();

            $table->enum('category', ['dechets', 'plantes_mortes', 'vandalisme', 'equipement', 'autre']);
            $table->enum('priority', ['basse', 'normale', 'haute', 'urgente']);

            $table->enum('statut', ['en_attente', 'en_cours', 'resolu'])->default('en_attente');

            $table->dateTime('date_signalement');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->timestamps();

            // Clés étrangères
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('green_space_id')->references('id')->on('green_spaces')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
