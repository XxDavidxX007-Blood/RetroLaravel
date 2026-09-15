<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    use HasFactory;

    protected $table = 'reportes';

    protected $fillable = [
        'user_id',
        'generado_por',
        'filtro_usado',
        'formato',
        'total_productos',
        'valor_total',
        'datos_json',
    ];

    protected $casts = [
        'valor_total' => 'decimal:2',
        'datos_json' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
