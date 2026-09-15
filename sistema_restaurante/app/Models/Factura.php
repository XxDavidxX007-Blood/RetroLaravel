<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id',
        'fecha_factura',
        'subtotal',
        'impuestos',
        'descuento',
        'total',
        'metodo_pago',
        'estado',
    ];

    protected $casts = [
        'fecha_factura' => 'date',
        'subtotal' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleFactura::class);
    }
}