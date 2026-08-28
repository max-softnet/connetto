<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'ruolo', 'operatore_id', 'filemaker_persona_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function operatore()
    {
        return $this->belongsTo(Operatore::class);
    }

    public function isAdmin(): bool
    {
        return $this->ruolo === 'admin';
    }

    public function isOperatore(): bool
    {
        return $this->ruolo === 'operatore';
    }

    public function isPaziente(): bool
    {
        return $this->ruolo === 'paziente';
    }
}
