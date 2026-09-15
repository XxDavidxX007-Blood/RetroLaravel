<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'categoria_producto_id',
        'nombre',
        'descripcion',
        'precio',
        'imagen',
        'estado',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'estado' => 'boolean',
    ];

    public function categoria()
    {
        return $this->belongsTo(
            CategoriaProducto::class,
            'categoria_producto_id'
        );
    }

    public function inventario()
    {
        return $this->hasOne(Inventario::class);
    }
}