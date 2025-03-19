<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XmlFile extends Model
{
    use HasFactory;
    protected $fillable = [
        'factura_id',
        'secuencial',
        'cliente',
        'directorio',
        'estado',
        'error'
    ];

    // Relación con la factura
    public function factura()
    {
        return $this->belongsTo(Factura::class, 'factura_id');
    }
}
