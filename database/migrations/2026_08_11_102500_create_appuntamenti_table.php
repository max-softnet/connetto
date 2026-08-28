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
        Schema::create('appuntamenti', function (Blueprint $table) {
            $table->id();
            $table->string('google_event_id')->nullable()->unique();
            $table->string('titolo');
            $table->text('descrizione')->nullable();
            $table->string('tipo');
            $table->date('data');
            $table->time('ora_inizio');
            $table->time('ora_fine');
            $table->enum('status', ['confermato', 'annullato'])->default('confermato');
            $table->timestamps();

            $table->foreign('tipo')->references('nome')->on('tipi_appuntamento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appuntamenti');
    }
};
