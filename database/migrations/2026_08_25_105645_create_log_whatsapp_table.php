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
        Schema::create('log_whatsapp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('messaggio_id')->nullable()->constrained('messaggi')->nullOnDelete();
            $table->string('endpoint');
            $table->json('richiesta');
            $table->unsignedSmallInteger('risposta_status')->nullable();
            $table->json('risposta')->nullable();
            $table->enum('esito', ['successo', 'fallito'])->default('fallito');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_whatsapp');
    }
};
