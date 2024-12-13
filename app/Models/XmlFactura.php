<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XmlFactura extends Model
{
    use HasFactory;

    protected $fillable = [
        'factura_id',
        'estado',
        'ruta_archivo',
    ];


    public function factura()  {
        return $this->belongsTo(Factura::class);
    }
}
