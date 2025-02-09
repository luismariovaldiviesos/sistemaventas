<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'product_id', 'changes', 'quantity', 'price'];

    // un detalle pertenece a una orden (a una venta a una cabecera factura ctm )
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // un detalle pertenece a un producto (a una venta a una cabecera factura ctm )
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
