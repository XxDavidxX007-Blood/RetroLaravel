<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    use HasFactory;

    protected $table = 'movimientos_inventario';

    protected $fillable = [
        'inventario_id',
        'tipo',
        'cantidad',
        'motivo',
        'user_id',
    ];

    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}