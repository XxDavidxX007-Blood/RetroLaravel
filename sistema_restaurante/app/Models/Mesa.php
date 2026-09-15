<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mesa extends Model
{
    use HasFactory;

    protected $table = 'mesas';

    protected $fillable = [
        'numero_mesa',
        'capacidad',
        'ubicacion',
        'estado_mesa_id',
    ];

    public function estadoMesa()
    {
        return $this->belongsTo(
            EstadoMesa::class,
            'estado_mesa_id'
        );
    }

    public function reservas()
    {
        return $this->hasMany(
            Reserva::class,
            'mesa_id'
        );
    }
}