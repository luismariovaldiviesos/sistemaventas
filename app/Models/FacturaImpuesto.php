<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaImpuesto extends Model
{
    use HasFactory;
    protected $fillable = [
        'factura_id', 'nombre_impuesto', 'codigo_impuesto', 'codigo_porcentaje',
        'base_imponible', 'valor_impuesto'
    ];


    public function factura(){
        return $this->belongsTo(Factura::class);
    }
}
