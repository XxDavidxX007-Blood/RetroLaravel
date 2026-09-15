<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'apellidos',
    'email',
    'telefono',
    'password',
    'foto',
    'role_id',
    'estado'
])]
#[Hidden([
    'password',
    'remember_token'
])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'apellidos',
        'email',
        'telefono',
        'password',
        'foto',
        'role_id',
        'estado'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'user_id');
    }

    public function mesero()
    {
        return $this->hasOne(Mesero::class, 'user_id');
    }

    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'user_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}