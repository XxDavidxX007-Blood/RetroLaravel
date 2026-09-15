<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoReserva extends Model
{
    use HasFactory;

    protected $table = 'estado_reservas';

    protected $fillable = [
        'nombre_estado',
    ];

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'estado_reserva_id');
    }
}