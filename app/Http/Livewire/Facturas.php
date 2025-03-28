<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Customer;
use App\Models\DetalleFactura;
use App\Models\Factura;
use App\Models\Product;
use App\Models\User;
use App\Models\XmlFactura;
use Livewire\Component;
use App\Traits\CartTrait;
use App\Traits\PrinterTrait;
use App\Traits\PdfTrait;
use DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB as FacadesDB;
//use Codedge\Fpdf\Facades\Fpdf;
use Codedge\Fpdf\Fpdf\Fpdf;
//use Barryvdh\DomPDF\Facade as PDF;
//use Barryvdh\DomPDF\PDF as PDF;
use PhpParser\Node\Stmt\Return_;

class Facturas extends Component
{

    protected $listeners = ['archivoNoFirmadoNoGuardado'];

     //traits
     use CartTrait, PrinterTrait, PdfTrait;

     // propiedades generales
    public $search, $cash, $searchCustomer, $searchProduct, $customer_id =null,
     $changes,  $customerSelected ="Seleccionar Cliente", $productSelected = "Buscar producto",
     $claveAcceso ='', $secuencial ='', $fechaFactura ;

    // mostrar y activar panels
    public $showListProducts = false, $tabProducts =  true, $tabCategories = false;

     //collections
     public $productsList =[], $customers =[], $products = [];

     //info del carrito
    public $totalCart = 0, $itemsCart= 0, $contentCart=[];

    //totales
    public $subTotSinImpuesto =0;

     // producto seleccionado
     public $productIdSelected, $productChangesSelected, $productNameSelected, $changesProduct;

     // impuestos
     public $iva12 = 0, $iva0 =0, $totalImpuesto12 =0,  $totalIce=0, $totalDscto=0;

     //dinamicos para la tabla
     public $subtotal0 = 0, $subtotal15 = 0, $totalImpuesto15 =0;

     protected $paginationTheme = "bootstrap";

     public $estadoCaja;



     // carga al inicio

     public function mount()
     {



     }



    public function render()
    {


       // dd($jarPath);
        $fact  = new Factura();
        $this->fechaFactura =  Carbon::now()->format('d-m-Y');
       $this->claveAcceso = $fact->claveAcceso();
       $this->secuencial = $fact->secuencial();
       //dd($this->secuencial);
      // dd($this->claveAcceso);
        $this->validaCaja();
        if(strlen($this->searchCustomer) > 0)
            $this->customers =  Customer::where('businame','like',"%{$this->searchCustomer}%")
             ->orderBy('businame','asc')->get()->take(5); //primeros 5 clientes
        else
            $this->customers =  Customer::orderBy('businame','asc')->get()->take(5); //primeros 5 clientes
            $this->totalCart = $this->getTotalCart();
            $this->itemsCart = $this->getItemsCart();
            $this->subTotSinImpuesto =  $this->getTotalSICart();
            $this->contentCart = $this->getContentCart();
            //dd($this->contentCart);
            // $this->iva12 = $this->getIva12();
            // $this->iva0 = $this->getIva0();
            // $this->totalImpuesto12 = $this->getImpuesto12();
            // $this->totalIce = $this->getIce();
            // $this->totalDscto = $this->getDscto();
            //dd($totalImpuesto12);




            if(strlen($this->searchProduct) > 0) {
                $this->products = Product::where('name', 'like', "%{$this->searchProduct}%")
                    ->orderBy('created_at', 'desc') // Ordena por fecha de creación para mostrar los más recientes primero
                    ->limit(5) // Trae solo los primeros 5 resultados
                    ->get();
            } else {
                $this->products = Product::orderBy('created_at', 'desc') // Ordena los más recientes primero
                    ->limit(5) // Trae solo los primeros 5
                    ->get();
            }

            //dd($this->products);

        return view('livewire.facturas.component', [
            'categories' => Category::orderBy('name','asc')->get(),
        ])
        ->layout('layouts.theme.app');

    }



    public function  validaCaja()
    {
        $user_id  =  Auth()->user()->id;
        $usuario = User::find($user_id);
        $this->estadoCaja = $usuario->caja->status ?? 'nocajasasignadas' ;

    }

    public function setTabActive($tabName)
    {
        if ($tabName == 'tabProducts') {
            $this->tabProducts = true;
            $this->tabCategories = false;
        }
        else
        {
            $this->tabProducts = false;
            $this->showListProducts = false;
            $this->tabCategories = true;
        }
    }

    public function noty($msg, $eventName= 'noty', )
    {
        $this->dispatchBrowserEvent($eventName, ['msg' => $msg, 'type' => 'success']);
    }

    //operaciones con el carrito
    public function getProductsByCategory($category_id)
    {
        $this->showListProducts =  true;
        $this->productsList = Product::where('category_id', $category_id)->where('stock','>', 0)->get();
    }

    public function add2Cart(Product $product)
    {

       $this->addProductCart($product, 1, $this->changes);
       $this->changes = '';
       //$this->subTotSinImpuesto = $this->subTotSinImpuesto + $product->price;
       //dd($this->subTotSinImpuesto);
    }

    public function increaseQty(Product $product, $cant=1)
    {
        $this->updateQtyCart($product, $cant);
    }

    public function decreaseQty($productId)
    {
        $this->decreaseQtyCart($productId);
    }

    public function removeFromCart($id)
    {
        $this->removeProductCart($id);
    }

    public function updateQty(Product $product, $cant=1)
    {
        //para validar si hay las suficientes existencias en bbd y poder vender
        if($cant  + $this->countInCart($product->id) > $product->stock){
            $this->noty('STOCK INSUFICIENTE','noty','error');
            return;
        }
        if ($cant <= 0)
            $this->removeProductCart($product->id);
        else
            $this->replaceQuantityCart($product->id, $cant);
    }

      // para los cambios en el modal
      public function removeChanges()
      {
          $this->clearChanges($this->productIdSelected);
          $this->dispatchBrowserEvent('close-modal-changes'); // evento que va al front para cerrar el modal (a traves de JS)

      }

      public function addChanges($changes)
      {
          $this->addChanges2Product($this->productIdSelected, $changes);
          $this->dispatchBrowserEvent('close-modal-changes');
      }


      public function updatedCustomerSelected()
      {
          $this->dispatchBrowserEvent('close-customer-modal');
      }

      public function searchManualProduct(Product $product)
      {
          //dd($id);
          $this->add2Cart($product);
          $this->dispatchBrowserEvent('close-product-modal');
          $this->resetUI();

      }

      public function resetUI()
      {
          $this->reset('tabProducts', 'cash', 'showListProducts', 'tabCategories', 'search',
          'searchCustomer', 'searchProduct', 'customer_id', 'customerSelected', 'totalCart',
          'itemsCart', 'productIdSelected', 'productChangesSelected', 'productNameSelected', 'changesProduct','subTotSinImpuesto');
      }


      public function recalcularTotales()
    {
        $this->subTotSinImpuesto = 0;  // Subtotal sin impuestos
        $this->totalDscto = 0;         // Total descuento
        $this->subtotal0 = 0;          // Subtotal IVA 0%
        $this->subtotal15 = 0;         // Subtotal IVA 15%
        $this->totalIce = 0;           // Total ICE
        $this->totalImpuesto15 = 0;    // Total IVA 15%
        $this->totalCart = 0;          // Total general del carrito
         // $this->iva12 = $this->getIva12();
            // $this->iva0 = $this->getIva0();
            // $this->totalImpuesto12 = $this->getImpuesto12();
            // $this->totalIce = $this->getIce();
            // $this->totalDscto = $this->getDscto();

        // Inicializar array de impuestos dinámico
        $impuestos = [
            'IVA 0' => 0,
            'IVA 15' => 0,
            'ICE' => 0,
        ];

        // Iterar los productos del carrito
        foreach ($this->getContentCart() as $producto) {
            $subtotalProducto = $producto['price'] * $producto['qty'];
            $this->subTotSinImpuesto += $subtotalProducto;

            // Calcular descuento si existe (como porcentaje)
            $montoDescuento = isset($producto['descuento']) ? $subtotalProducto * ($producto['descuento'] / 100) : 0;
            $this->totalDscto += $montoDescuento;

            // Iterar sobre los impuestos asignados al producto
            foreach ($producto['impuestos'] as $tax) {
                $nombreImpuesto = $tax['nombre'] . ' ' . intval($tax['porcentaje']); // Ej. IVA 15 o ICE 5
                $porcentaje = $tax['porcentaje'];

                // Base imponible después de descuento
                $baseImponible = $subtotalProducto - $montoDescuento;
                $montoImpuesto = round($baseImponible * $porcentaje / 100, 2);

                // Acumular montos según el tipo de impuesto
                $impuestos[$nombreImpuesto] = ($impuestos[$nombreImpuesto] ?? 0) + $montoImpuesto;

                // Calcular subtotales según el tipo de impuesto
                if (intval($porcentaje) === 0) {
                    $this->subtotal0 += $subtotalProducto;
                } elseif (strpos($nombreImpuesto, 'IVA 15') !== false) {
                    $this->subtotal15 += $subtotalProducto;
                    $this->totalImpuesto15 += $montoImpuesto;
                } elseif (strpos($nombreImpuesto, 'ICE') !== false) {
                    $this->totalIce += $montoImpuesto;
                }
            }
        }

        // Calcular el total del carrito: subtotal - descuentos + impuestos totales
        $this->totalCart = round($this->subTotSinImpuesto - $this->totalDscto + array_sum($impuestos), 2);

        // Debug con dd para revisar los resultados
        // dd([
        //     'Subtotal sin impuestos' => $this->subTotSinImpuesto,
        //     'Descuento' => $this->totalDscto,
        //     'Subtotal IVA 0%' => $this->subtotal0,
        //     'Subtotal IVA 15%' => $this->subtotal15,
        //     'Total ICE' => $this->totalIce,
        //     'Total IVA 15%' => $this->totalImpuesto15,
        //     'Total Carrito' => $this->totalCart,
        // ]);
    }

      // guardar venta
      public function storeSale($print = false)
      {
        //dd($this->secuencial);
        //**********  validamos que haya productos  en la venta */
        if ($this->getTotalCart() <= 0) {
            $this->noty('AGREGA PRODUCTOS A LA VENTA', 'noty', 'error');
            return;
        }
        DB::beginTransaction();
        try
        {
            // si no se escige cliente , va consumidor final

            if ($this->customerSelected !=  'Seleccionar Cliente') {
                $this->customer_id = Customer::where('businame', $this->customerSelected)->first()->id;
            } else{
                $this->customer_id = Customer::where('businame', 'consumidor final')->first()->id;
            }

            // -----***********OJO REVISAR SI CONSUMIDAR FINAL SE FACTURA ********---------------
            // PARA EL CLEINTE EN EL XML
            $customer =  Customer::find($this->customer_id);
           //dd($customer);
            $tipo = $customer->typeidenti;
            if($tipo == 'ci')
            {
                $tipeIDenti = '05';   //cedula
            }
            if ($tipo == 'ruc') {
                $tipeIDenti = '04';  //ruc
            }

            $this->recalcularTotales();
            // dd([
            //     'Subtotal sin impuestos' => $this->subTotSinImpuesto,
            //     'Descuento' => $this->totalDscto,
            //     'Subtotal IVA 0%' => $this->subtotal0,
            //     'Subtotal IVA 15%' => $this->subtotal15,
            //     'Total ICE' => $this->totalIce,
            //     'Total IVA 15%' => $this->totalImpuesto15,
            //     'Total Carrito' => $this->totalCart,
            // ]);
            $factura  =  Factura::create([
                //dd($this->secuencial, $this->claveAcceso), hasta aqui llega bien secuencial y clave
                'secuencial' => $this->secuencial,
                'codDoc' => '01',
                'claveAcceso' =>   $this->claveAcceso,
                'customer_id' =>  $this->customer_id,
                'user_id' => Auth()->user()->id,
                'subtotal' => $this->subTotSinImpuesto,  //ok
                'subtotal0' => $this->subtotal0,   //iva0 borrar
                'subtotal12' => $this->subtotal15,     //iva12 borrar
                'ice' => $this->totalIce,    //ok
                'descuento' => $this->totalDscto,
                'iva12' => $this->totalImpuesto15,  //totalImpuesto12 borrar
                'total' => $this->totalCart,
                'formaPago' => '01'
            ]);
            //dd($factura);
            if ($factura) {
                $items =  $this->getContentCart();
                //dd($items);
               foreach($items  as $item)
               {
                DetalleFactura::create([
                    'factura_id' => $factura->id,
                    'product_id' => $item->id,
                    'cantidad' => $item->qty,
                    'descripcion' => $item->name,
                    'precioUnitario' => $item->price,
                    'descuento' =>$item->descuento,
                    'total' =>$item->price * $item->qty
                ]);

                 //**********  actualizamos stock */
                 $product = Product::find($item->id);
                 $product->stock = $product->stock - $item->qty;
                 $product->save();
               }
            }

            DB::commit();



            //********** crea xml , firma, envia y devuelve del sri  */
            $factura->xmlFactura(
                $factura->id, $tipeIDenti, $customer->businame, $customer->valueidenti, $customer->address,
                $this->subTotSinImpuesto, $this->totalDscto, $this->subtotal15,
                $this->totalImpuesto15, $this->totalCart, $this->getContentCart(),
                $this->secuencial, $this->claveAcceso
            );



        }
        catch (\Throwable $e) {
            FacadesDB::rollback();
            $this->noty('Error al guardar el pedido: ' . $e->getMessage(), 'noty', 'error');
        }

        // actualiza la factura con la fecha de autorizacion
        $factura->fechaAutorizacion =  Carbon::now();
        $factura->numeroAutorizacion =  $factura->claveAcceso;
        $factura->save();
        $this->clearCart();
        $this->resetUI();
        $url =  route('descargar-pdf', ['factura'=>$factura->id]);
        $this->noty('FACTURA GENERADA  CORRECTAMENTE !!!!!!');
        return redirect()->to($url);


      }



}
