<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Table pour les messages du chat d'événement
        Schema::create('event_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('message');
            $table->string('type')->default('text'); // text, image, file, system
            $table->string('attachment_path')->nullable(); // pour images/fichiers
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_important')->default(false); // pour annonces
            $table->foreignId('reply_to_id')->nullable()->constrained('event_messages')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['event_id', 'created_at']);
        });

        // Table pour savoir qui a lu quoi
        Schema::create('event_message_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_message_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('read_at');
            $table->timestamps();

            $table->unique(['event_message_id', 'user_id']);
        });

        // Table pour les réactions aux messages
        Schema::create('event_message_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_message_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('emoji', 10); // 👍, ❤️, 😊, etc.
            $table->timestamps();

            $table->unique(['event_message_id', 'user_id', 'emoji']);
        });

        // Ajouter un champ pour le QR code unique de l'événement
        Schema::table('events', function (Blueprint $table) {
            $table->string('chat_token')->unique()->nullable()->after('image');
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_message_reactions');
        Schema::dropIfExists('event_message_reads');
        Schema::dropIfExists('event_messages');

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('chat_token');
        });
    }
};
