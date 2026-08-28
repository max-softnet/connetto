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
            $table->string('whatsapp_template_nome')->nullable()->after('corpo');
            $table->string('whatsapp_template_lingua')->nullable()->after('whatsapp_template_nome');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modelli_messaggio', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_template_nome', 'whatsapp_template_lingua']);
        });
    }
};
