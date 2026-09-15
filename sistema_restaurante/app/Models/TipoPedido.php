<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPedido extends Model
{
    use HasFactory;

    protected $table = 'tipos_pedido';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
}