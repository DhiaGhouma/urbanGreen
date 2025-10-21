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
        Schema::create('report_updates', function (Blueprint $table) {
            $table->id();

            // Clé étrangère vers la table reports
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');

            $table->text('update_text'); // Texte de mise à jour
            $table->string('updated_by')->nullable(); // Qui a fait la mise à jour
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_updates');
    }
};
