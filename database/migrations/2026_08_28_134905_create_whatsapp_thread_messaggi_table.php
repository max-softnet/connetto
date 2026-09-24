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
        Schema::create('whatsapp_thread_messaggi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversazione_id')->constrained('whatsapp_conversazioni')->cascadeOnDelete();
            $table->enum('direzione', ['in', 'out']);
            $table->text('corpo');
            $table->string('wamid')->nullable()->unique();
            $table->string('stato')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_thread_messaggi');
    }
};
