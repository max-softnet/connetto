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
        Schema::create('log_automazioni', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automazione_id')->constrained('automazioni')->cascadeOnDelete();
            $table->dateTime('eseguita_at');
            $table->enum('origine', ['automatica', 'manuale'])->default('automatica');
            $table->enum('esito', ['successo', 'parziale', 'fallito', 'nessun_appuntamento'])->default('successo');
            $table->text('dettagli')->nullable();
            $table->timestamps();

            $table->index(['automazione_id', 'eseguita_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_automazioni');
    }
};
