<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Image;
use App\Models\Impuesto;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use DB;

class Products extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $name='', $code ='', $cost=0, $price=0, $price2=0, $pvp = 0, $descuento = 0,  $stock, $minstock, $category='elegir',$selected_id=0,$gallery = [];
    public $action = 'Listado', $componentName='CATALOGO DE PRODUCTOS', $search, $form = false;
    private $pagination =15;
    protected $paginationTheme='tailwind';

    public $impuestos ; // lista de impuestos desde la base de datos
    public $impuestosSeleccionados = [];
    public $totalImpuestos;
    public bool $isPhysical = false;

    // public $ivaporcentaje = 'elegir';
    // public $iceporcentaje = 'elegir';

    // public $iva = 0;
    // public $ice = 0;

   // public $selectedImpuestos =[];

   public function mount(){
        $this->impuestos  =  Impuesto::all();
   }


    public function render()
    {



        if(strlen($this->search) > 0)
            $info =  Product::join('categories as c','c.id','products.category_id')
                ->select('products.*','c.name as category')
                ->where('products.name','like',"%{$this->search}%")
                ->orWhere('products.code','like',"%{$this->search}%")
                ->orWhere('c.name','like',"%{$this->search}%")
                ->paginate($this->pagination);
            else

            $info =  Product::join('categories as c','c.id','products.category_id')
                ->select('products.*','c.name as category')
                ->paginate($this->pagination);


        return view('livewire.products.component', [
            'products' => $info,
            'categories' => Category::orderBy('name','asc')->get(),
            //'ivas' => Impuesto::where('nombre','IVA')->get()

        ])->layout('layouts.theme.app');
    }



    public $listeners = ['resetUI','Destroy'];


    public function noty($msg, $eventName='noty', $reset =  true, $action = '')
    {
        $this->dispatchBrowserEvent($eventName, ['msg' => $msg, 'type' => 'success', 'action' => $action]);
        if($reset) $this->resetUI();
    }

    public function AddNew()
    {
        $this->resetUI();
        $this->noty(null, 'open-modal');
    }

    public function resetUI()
    {
        $this->resetValidation();
        $this->resetPage();
        $this->reset(
            'name',
            'code',
            'cost',
            'price',
            'descuento',
            'price2',
             'stock',
            'minstock',
            'selected_id',
            'search',
            'action',
            'gallery',
            'isPhysical'
        );

    }

    public function CloseModal()
    {
        $this->resetUI();
        $this->noty(null, 'close-modal');
    }

    public function Edit (Product $product)
    {

        //dd($product);
        $this->selected_id = $product->id;
        $this->name = $product->name;
        $this->code = $product->code;
        $this->cost = number_format($product->cost,2) ;
        $this->price = number_format($product->price,2) ;
        // $this->iva = $product->iva;
        // $this->ice = $product->ice;
        $this->descuento = number_format($product->descuento,1);
        $this->price2 = number_format($product->price2,2) ;
        $this->stock = $product->stock;
        $this->minstock = $product->minstock;
        $this->category = $product->category_id;
        $this->impuestosSeleccionados = $product->impuestos->pluck('id')->toArray();
        //dd($this->impuestosSeleccionados);

        $this->noty('', 'open-modal', false);



    }


    public function Store()
    {


         sleep(1);

        $this->validate(Product::rules($this->selected_id,$this->isPhysical), Product::$messages);


        if($this->descuento > 0)
        {
            $totalDescuento  =  ($this->price * $this->descuento) /100;
            $precioConDescuento =   $this->price - $totalDescuento;

        }
        else{

            $precioConDescuento = $this->price;
       }

       $this->totalImpuestos = 0;
       foreach($this->impuestosSeleccionados as $impuestoId){
            $impuesto =  Impuesto::find($impuestoId); // treamoes el impuesto
            if ($impuesto) {
                $montoImpuesto = ($precioConDescuento * $impuesto->porcentaje) / 100;
                $this->totalImpuestos += $montoImpuesto;
            }
       }

       // Precio final considerando los impuestos
        $pvp = $precioConDescuento + $this->totalImpuestos;

      // dd($precioConDescuento, $pvp);

        $product = Product::updateOrCreate(

            ['id' => $this->selected_id ],
            [
                'name' => $this->name,
                'code' => $this->code,
                'cost' => $this->cost ?? 0,00,
                'price' => $this->price,
                'descuento' => $this->descuento,
                'price2' => $pvp,
                'stock' => $this->stock ?? null,
                'minstock' => $this->minstock ?? null,
                'category_id' => $this->category
            ]
        );
        // Sincronizar impuestos seleccionados con el producto
        $product->impuestos()->sync($this->impuestosSeleccionados);

        //fotos producto
        if(!empty($this->gallery)){
            if($this->selected_id > 0){ // si es mnayor a cero actualiza
                //eliminar todas las imagenes fisicamente
                $product->images()->each(function ($img){
                    if($img->file != null && file_exists('storage/products/'. $img->file))
                    {
                        unlink('storage/products/'. $img->file);
                    }
                });
                //eliminar relaciones de las imagenes en la bbdd
                $product->images()->delete();
            }

            foreach($this->gallery as $photo){
                $customFileName = uniqid() . '_.' .$photo->extension(); // nombre de la imagen
                $photo->storeAs('public/products', $customFileName);

                // crear la imagen
                $img = Image::create([
                    'model_id' => $product->id,
                    'model_type' => 'App\Models\Product',
                    'file' => $customFileName
                ]);

                //guardar relaciones
                $product->images()->save($img);
            }
        }
        $this->noty($this->selected_id > 0  ?  'Producto actualizado' : 'Producto registrado', 'noty', 'false', 'close-modal');
        $this->resetUI();
    }

    public  function calculaPVP(Product  $product)
    {

        $porcentaje = 0;
        foreach($product->impuestos as $impuesto)
        {
            $porcentaje = $porcentaje + $impuesto->porcentaje;
        }

        $impuestoProducto  =  ($product->price * $porcentaje) / 100;
        $precioFinal  = $product->price  + $impuestoProducto;
        //dd($porcentaje);
        //dd($impuestoProducto);
        //dd($precioFinal);
        return ($precioFinal);

    }


    public  function Destroy(Product $product)
    {
        // eliminar las imagenes fisicamente
        //dd($product);
        $product->images()->each( function($img){
            if($img->file != null && file_exists('storage/products' . $img->file))
            {
                unlink('storage/products' . $img->file);
            }
        });
        //eliminar las relaciones
        $product->images()->delete();
        $product->impuestos()->detach(); // Elimina relaciones en la tabla pivote
        //eliminar el producto
        $product->delete();
        $this->noty('Se eliminó el producto');

    }



}
