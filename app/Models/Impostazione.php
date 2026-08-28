<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Impostazione extends Model
{
    protected $table = 'impostazioni';

    protected $fillable = [
        'whatsapp_token',
        'whatsapp_phone_number_id',
    ];

    protected $casts = [
        'whatsapp_token' => 'encrypted',
    ];

    public static function corrente(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }
}
