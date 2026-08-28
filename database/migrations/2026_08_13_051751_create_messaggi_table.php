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
        Schema::create('messaggi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appuntamento_id')->constrained('appuntamenti')->cascadeOnDelete();
            $table->foreignId('modello_id')->nullable()->constrained('modelli_messaggio')->nullOnDelete();
            $table->enum('canale', ['email', 'sms']);
            $table->string('destinatario');
            $table->string('oggetto')->nullable();
            $table->text('corpo');
            $table->enum('stato', ['bozza', 'inviato', 'fallito'])->default('bozza');
            $table->enum('origine', ['manuale', 'automatico'])->default('manuale');
            $table->timestamp('inviato_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messaggi');
    }
};
