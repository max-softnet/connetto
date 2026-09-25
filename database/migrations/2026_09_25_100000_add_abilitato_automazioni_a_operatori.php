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
        Schema::table('operatori', function (Blueprint $table) {
            $table->boolean('abilitato_automazioni')->default(true)->after('colore');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operatori', function (Blueprint $table) {
            $table->dropColumn('abilitato_automazioni');
        });
    }
};
