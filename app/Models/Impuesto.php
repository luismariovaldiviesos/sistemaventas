<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Impuesto extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'codigo', 'codigo_porcentaje', 'porcentaje'];


    public static function rules($id)
    {
        if ($id <= 0) {
            return [
                'nombre' => 'required',
                'codigo' => 'required',
                'codigo_porcentaje' => 'required',
                'porcentaje' => 'required'
            ];
        } else {
            return [
                'nombre' => "required",
                'codigo' => 'required',
                'codigo_porcentaje' => 'required',
                'porcentaje' => 'required'
            ];
        }
    }

    public static $messages = [
        'nombre.required' => 'Nombre impuesto requerido',
        'codigo.required' => 'Codigo impuesto requerido',
        'codigo_porcentaje.required' => 'Codigo porcentaje impuesto requerido',
        'porcentaje.required' => 'Porcentaje impuesto requerido',
    ];


     // Product.php
public function productos()
{
    return $this->belongsToMany(Product::class, 'impuesto_producto', 'impuesto_id', 'producto_id');
}

}
