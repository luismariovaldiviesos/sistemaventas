<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Impuesto extends Model
{
    use HasFactory;

    protected $fillable = ['nombre','porcentaje','codigo'];

    public static function rules($id)
    {

            return [
                'nombre' => 'required',
                'porcentaje' => 'required',
                'codigo' => 'required'
            ];

    }

    public static $messages = [
        'nombre.required' => 'Nombre impuesto requerido',
        'porcentaje.required' => 'porcentaje impuesto requerido',
        'codigo.required' => 'Código SRI requerido'

    ];

    // un impuesto pertenece a varios prodcutos
    public function productos(){
        return $this->belongsToMany(Product::class, 'impuesto_producto');
    }


}
