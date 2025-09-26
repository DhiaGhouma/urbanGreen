<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('green_space_plants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('green_space_id')->constrained('green_spaces')->onDelete('cascade');
            $table->string('name');
            $table->string('species')->nullable();
            $table->integer('quantity')->default(1);
            $table->date('planted_at')->nullable();
            $table->string('maintenance')->nullable();
            $table->string('status')->default('en vie');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('green_space_plants');
    }
};
