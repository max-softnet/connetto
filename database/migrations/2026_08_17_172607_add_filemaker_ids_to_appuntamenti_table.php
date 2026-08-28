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
            $table->unsignedBigInteger('filemaker_id')->nullable()->unique()->after('google_event_id');
            $table->unsignedBigInteger('filemaker_persona_id')->nullable()->index()->after('filemaker_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appuntamenti', function (Blueprint $table) {
            $table->dropColumn(['filemaker_id', 'filemaker_persona_id']);
        });
    }
};
