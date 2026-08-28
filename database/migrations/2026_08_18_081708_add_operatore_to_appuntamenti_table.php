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
        Schema::table('appuntamenti', function (Blueprint $table) {
            $table->string('operatore')->nullable()->after('tipo');
            $table->foreign('operatore')->references('nome')->on('operatori')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appuntamenti', function (Blueprint $table) {
            $table->dropForeign(['operatore']);
            $table->dropColumn('operatore');
        });
    }
};
