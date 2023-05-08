<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleFactura extends Model
{
    use HasFactory;

    protected $fillable =
    [      'factura_id','product_id','cantidad','descripcion',
           'precioUnitario','descuento','total'
   ];
}
