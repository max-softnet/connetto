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
        Schema::create('modelli_messaggio', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->enum('canale', ['email', 'sms']);
            $table->string('oggetto')->nullable();
            $table->text('corpo');
            $table->string('tipo_appuntamento')->nullable();
            $table->timestamps();

            $table->foreign('tipo_appuntamento')->references('nome')->on('tipi_appuntamento')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modelli_messaggio');
    }
};
