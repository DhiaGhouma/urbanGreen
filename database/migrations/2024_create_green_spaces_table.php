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
        Schema::create('green_spaces', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('location');
            $table->text('description')->nullable();
            $table->enum('type', ['parc', 'jardin', 'square', 'autre']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('green_spaces');
    }
};