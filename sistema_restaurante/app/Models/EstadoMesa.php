<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoMesa extends Model
{
    use HasFactory;

    protected $table = 'estado_mesas';

    protected $fillable = [
        'nombre_estado',
    ];

    public function mesas()
    {
        return $this->hasMany(
            Mesa::class,
            'estado_mesa_id'
        );
    }
}