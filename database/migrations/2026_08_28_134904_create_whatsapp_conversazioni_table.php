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
        Schema::create('whatsapp_conversazioni', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->string('nome_contatto')->nullable();
            $table->timestamp('ultimo_messaggio_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_conversazioni');
    }
};
