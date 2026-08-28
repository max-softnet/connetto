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
        Schema::table('modelli_messaggio', function (Blueprint $table) {
            $table->enum('whatsapp_formato_parametri', ['posizionale', 'nominale'])->default('posizionale')->after('whatsapp_template_lingua');
            $table->string('whatsapp_nome_parametro')->nullable()->after('whatsapp_formato_parametri');
            $table->boolean('whatsapp_header_parametro')->default(false)->after('whatsapp_nome_parametro');
        });

        Schema::table('messaggi', function (Blueprint $table) {
            $table->enum('whatsapp_formato_parametri', ['posizionale', 'nominale'])->default('posizionale')->after('whatsapp_template_lingua');
            $table->boolean('whatsapp_header_parametro')->default(false)->after('whatsapp_formato_parametri');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modelli_messaggio', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_formato_parametri', 'whatsapp_nome_parametro', 'whatsapp_header_parametro']);
        });

        Schema::table('messaggi', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_formato_parametri', 'whatsapp_header_parametro']);
        });
    }
};
