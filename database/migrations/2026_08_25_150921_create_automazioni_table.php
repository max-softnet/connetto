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
        Schema::create('automazioni', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->foreignId('modello_id')->constrained('modelli_messaggio')->cascadeOnDelete();
            $table->unsignedTinyInteger('giorni_prima')->default(1);
            $table->string('tipo_appuntamento')->nullable();
            $table->boolean('attiva')->default(true);
            $table->timestamps();

            $table->foreign('tipo_appuntamento')->references('nome')->on('tipi_appuntamento')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automazioni');
    }
};
