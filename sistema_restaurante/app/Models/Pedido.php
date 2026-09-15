<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'mesero_id',
        'tipo_pedido_id',
        'estado_pedido_id',
        'total',
        'observaciones',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    public function factura()
    {
    return $this->hasOne(Factura::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function mesero()
    {
        return $this->belongsTo(Mesero::class);
    }

    public function tipoPedido()
    {
        return $this->belongsTo(TipoPedido::class);
    }

    public function estadoPedido()
    {
        return $this->belongsTo(EstadoPedido::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetallePedido::class);
    }
}