<?php

namespace App\Models;

use App\Traits\CartTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    use CartTrait;


    protected $fillable =  ['code','name','price','descuento','price2','changes','cost','stock','minstock','es_servicio','category_id'];


    //validaciones
    public static function rules($id, $requiereStock = false)
{
    $rules = [
        'name' => "required|min:3|max:100|string|unique:products,name,{$id}",
        'code' => 'required|max:25',
        'category' => 'required|not_in:elegir',
        'price' => 'gt:0',  // Mayor a cero

    ];

    if ($requiereStock) {
        $rules['stock'] = 'required|integer|min:0';
        $rules['minstock'] = 'required|integer|min:0';
    }

    return $rules;
}

    public static $messages = [
        'name.required' => 'Nombre del producto/servicio requerido',
        'name.min' => 'Nombre del producto/servicio debe tener al menos tres caracteres',
        'name.max' => 'Nombre del producto/servicio debe tener máximos 100 caracteres',
        'name.unique' => 'Nombre del producto/servicio ya existe en la base de datos',
        'code.max' => 'El código debe tener máximo 25 caracteres',
         'code.required' => 'Ingrese Codigo del producto/servicio',
        'category.required' => 'La categoria es requerida',
        'category.not_in' => 'Elige una categoría válida',
       'price.gt' => 'El precio debe ser mayor a cero',
        'stock.required' => 'Ingresa el stock',
        'minstock.required' => 'Ingresa el stock mínimo'
    ];


    //relaciones
    public function sales()
    {
       return $this->hasMany(DetalleFactura::class);
    }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'model');
    }

    // ultima imagen que se relaciono al producto
    public function lastestImage()
    {
        return $this->morphOne(Image::class, 'model')->latestOfMany();
    }

      // un producto puede tener varios impuestos
//    public function impuestos()
//    {
//         return $this->belongsToMany(Impuesto::class,'impuesto_producto');
//     }


    //accesors
    public function getImgAttribute()
    {
        if(count($this->images))
        {
            if (file_exists('storage/products/'. $this->images->last()->file))

                return "storage/products/". $this->images->last()->file;
                else
            return "storage/image-not-found.png";  // si el producto tiene imagen pero fisiscamente no se encuentra

        } else{
            return 'storage/noimg.png'; // si el producto no  tiene imagen relacionada

        }
    }

    public function getLiveStockAttribute()
    {
        $stock =0;
        $stockCart = $this->countInCart($this->id);
        if ($stockCart > 0) {
            $stock = $this->stock - $stockCart;
        }
        else{
            $stock = $this->stock;
        }

        return $stock;
    }


  // Product.php
public function impuestos()
{
    return $this->belongsToMany(Impuesto::class, 'impuesto_producto', 'producto_id', 'impuesto_id');
}


   }


