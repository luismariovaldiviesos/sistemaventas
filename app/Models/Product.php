<?php

namespace App\Models;

use App\Traits\CartTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    use CartTrait;


    protected $fillable =  ['code','name','price','iva','ice','descuento','price2','changes','cost','stock','minstock','category_id'];


    //validaciones
    public static  function rules ($id)
    {
        if($id <= 0){
            return[
                'name' => 'required|min:3|max:100|string|unique:products',
                'code' => 'nullable|max:25',
                'category' => 'required|not_in:elegir',
                'price' => 'gt:0', // mayor a cero
                'iva' => 'required|not_in:elegir',
                'ice' => 'required|not_in:elegir',
                'cost' => 'gt:0', // mayor a cero
                'stock' => 'required',
                'minstock' => 'required',
            ];
        }
        else{
            return[
                'name' => "required|min:3|max:100|string|unique:products,name,{$id}",
                'code' => 'nullable|max:25',
                'category' => 'required|not_in:elegir',
                'price' => 'gt:0', // mayor a cero
                'iva' => 'required|not_in:elegir',
                'ice' => 'required|not_in:elegir',
                'cost' => 'gt:0', // mayor a cero
                'stock' => 'required',
                'minstock' => 'required',
            ];
        }

    }

    public static $messages = [
        'name.required' => 'Nombre del producto requerido',
        'name.min' => 'Nombre del producto debe tener al menos tres caracteres',
        'name.max' => 'Nombre del producto debe tener máximos 100 caracteres',
        'name.unique' => 'Nombre del producto ya existe en la base de datos',
        'code.max' => 'El código debe tener máximo 25 caracteres',
        'category.required' => 'La categoria es requerida',
        'category.not_in' => 'Elige una categoría válida',
        'cost.gt' => 'El costo debe ser mayor a cero',
        'price.gt' => 'El precio debe ser mayor a cero',
        'stock.required' => 'Ingresa el stock',
        'minstock.required' => 'Ingresa el stock mínimo',

        'iva.not_in' => 'Elige un valor para el  iva',
        'ice.not_in' => 'Elige un valor para el ice ',

        'iva.required' => 'El IVA es requerido',
        'ice.required' => 'El ICE es requerido',
    ];


    //relaciones
    public function sales()
    {
       return $this->hasMany(OrderDetail::class);
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


   }


