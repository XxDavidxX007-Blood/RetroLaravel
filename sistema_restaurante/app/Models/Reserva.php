<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'mesa_id',
        'fecha_reserva',
        'hora_reserva',
        'cantidad_personas',
        'observaciones',
        'estado_reserva_id',
    ];

    protected $casts = [
        'fecha_reserva' => 'date',
        'hora_reserva' => 'datetime:H:i',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function mesa()
    {
        return $this->belongsTo(Mesa::class, 'mesa_id');
    }

    public function estadoReserva()
    {
        return $this->belongsTo(EstadoReserva::class, 'estado_reserva_id');
    }
}