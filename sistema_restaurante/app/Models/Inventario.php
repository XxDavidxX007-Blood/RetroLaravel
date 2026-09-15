<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;

    protected $fillable = [
        'producto_id',
        'cantidad',
        'stock_minimo',
        'stock_maximo',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class);
    }
}