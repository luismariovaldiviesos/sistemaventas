<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeletedFactura extends Model
{
    use HasFactory;
    protected $fillable = [
        'factura_id',
        'secuencial',
        'cliente',
        'ruc_cliente',
        'correo_cliente',
        'fecha_emision',
        'clave_acceso',
        'estado',
    ];
}
