<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE modelli_messaggio MODIFY canale ENUM('email', 'sms', 'whatsapp') NOT NULL");
        DB::statement("ALTER TABLE messaggi MODIFY canale ENUM('email', 'sms', 'whatsapp') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE modelli_messaggio MODIFY canale ENUM('email', 'sms') NOT NULL");
        DB::statement("ALTER TABLE messaggi MODIFY canale ENUM('email', 'sms') NOT NULL");
    }
};
