<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Customer;
use App\Models\DetalleFactura;
use App\Models\Factura;
use App\Models\FacturaImpuesto;
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

    protected $listeners = ['cancelSale'];

     //traits
     use CartTrait, PrinterTrait, PdfTrait;

     // propiedades generales
    public  $cash, $searchCustomer, $searchProduct, $customer_id =null,
     $changes,  $customerSelected ="Seleccionar Cliente", $productSelected = "Buscar producto",$productNameSelected="",$productChangesSelected="",
     $claveAcceso ='', $secuencial ='', $fechaFactura ;

    // mostrar y activar panels
    public $showListProducts = false, $tabProducts =  true, $tabCategories = false;

     //collections
     public $productsList =[], $customers =[], $products = [];

     //info del carrito
    public $totalCart = 0, $itemsCart= 0, $contentCart=[];

    //totales
    public $subTotSinImpuesto =0;
    public $subtotalSinImpuestos = 0; // Total de productos sin impuestos


     // producto seleccionado
     public $productIdSelected;

     // impuestos
     public  $totalDscto=0;

     //dinamicos para la tabla
    // public $subtotal0 = 0, $subtotal15 = 0, $totalImpuesto15 =0;
      public  $impuestos = [];  // Ejemplo: ['IVA 15' => 5.00, 'ICE 3101' => 2.00]
     public $subtotales = []; // Ejemplo: ['IVA 15' => 30.00, 'ICE 3101' => 10.00]
     public $resumenImpuestos = [];     // Nuevo arreglo para el Blade: nombre, base, valor

     protected $paginationTheme = "bootstrap";

     public $estadoCaja;




     // carga al inicio

     public function mount()
     {

        $fact  = new Factura();
        $this->claveAcceso = $fact->claveAcceso();
        $this->secuencial = $fact->secuencial();
        //$this->recalcularTotales();


     }



    public function render()
    {


        $this->fechaFactura =  Carbon::now()->format('d-m-Y');

      // dd($this->secuencial , $this->claveAcceso);
      // dd($this->claveAcceso);
        $this->validaCaja();
        if(strlen($this->searchCustomer) > 0)
            $this->customers =  Customer::where('businame','like',"%{$this->searchCustomer}%")
             ->orderBy('businame','asc')->get()->take(5); //primeros 5 clientes
        else
            $this->customers =  Customer::orderBy('businame','asc')->get()->take(5); //primeros 5 clientes
            //$this->totalCart = $this->getTotalCart();
            $this->itemsCart = $this->getItemsCart();
           // $this->subTotSinImpuesto =  $this->getTotalSICart();
            $this->contentCart = $this->getContentCart();
            $this->recalcularTotales();
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
        $this->productsList = Product::where('category_id', $category_id)
                        ->where(function ($query) {
                            $query->where('stock', '>', 0)
                                ->orWhere('es_servicio', true);
                        })
                        ->get();
     }

    public function add2Cart(Product $product)
    {

       $this->addProductCart($product, 1, $this->changes);
       $this->changes = '';
       $this->recalcularTotales();
       //$this->subTotSinImpuesto = $this->subTotSinImpuesto + $product->price;
       //dd($this->subTotSinImpuesto);
    }

    public function increaseQty(Product $product, $cant=1)
    {
        $this->updateQtyCart($product, $cant);
        $this->recalcularTotales();
    }

    public function decreaseQty($productId)
    {
        $this->decreaseQtyCart($productId);
        $this->recalcularTotales();
    }

    public function removeFromCart($id)
    {
        $this->removeProductCart($id);
        $this->recalcularTotales();
    }

    public function updateQty(Product $product, $cant=1)
    {
        //para validar si hay las suficientes existencias en bbd y poder vender
        if($cant  + $this->countInCart($product->id) > $product->stock){
            $this->noty('STOCK INSUFICIENTE','noty','error');
            return;
        }
        if ($cant <= 0){

            $this->removeProductCart($product->id);
            $this->recalcularTotales();
        }
        else{
            $this->replaceQuantityCart($product->id, $cant);
            $this->recalcularTotales();
        }

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
        $this->reset('cash','searchCustomer','searchProduct','customer_id','changes','customerSelected','productSelected','claveAcceso','secuencial','fechaFactura',
        'showListProducts','tabProducts','tabCategories','productsList','customers','products','totalCart','itemsCart','contentCart',
        'subTotSinImpuesto','subtotalSinImpuestos','productIdSelected','totalDscto','impuestos','subtotales','resumenImpuestos');
      }


      public function recalcularTotales()
      {
          $this->subTotSinImpuesto = 0;  // Subtotal sin impuestos
          $this->totalDscto = 0;         // Total descuento
          //$this->totalCart = 0;          // Total general del carrito

          // Array dinámico para impuestos y subtotales por tipo de impuesto
          $this->impuestos = [];  // Ejemplo: ['IVA 15' => 5.00, 'ICE 3101' => 2.00]
          $this->subtotales = []; // Ejemplo: ['IVA 15' => 30.00, 'ICE 3101' => 10.00]

          $this->subtotalSinImpuestos = 0; // Total de productos sin impuestos
          $this->resumenImpuestos = [];     // Nuevo arreglo para el Blade: nombre, base, valor

          // Iterar los productos del carrito
          foreach ($this->contentCart as &$producto) { // Referencia con & para modificar directamente
              $subtotalProducto = $producto['price'] * $producto['qty'];
              $this->subTotSinImpuesto += $subtotalProducto;

              // Calcular descuento si existe (como porcentaje)
              $montoDescuento = isset($producto['descuento']) ? $subtotalProducto * ($producto['descuento'] / 100) : 0;
              $this->totalDscto += $montoDescuento;

              // Base imponible después de descuento
              $baseImponible = $subtotalProducto - $montoDescuento;

              // Inicializar el total de impuestos para este producto
              $producto['total_impuesto'] = 0;

              // Si el producto no tiene impuestos, acumular en subtotal sin impuestos
              if (empty($producto['impuestos'])) {
                  $this->subtotalSinImpuestos += $baseImponible;
              }

              // Iterar sobre los impuestos asignados al producto
              foreach ($producto['impuestos'] as $tax) {
                  $nombreImpuesto = $tax['nombre'] ; // Ejemplo: IVA 15 o ICE 3101
                  $porcentaje = $tax['porcentaje'];
                  $montoImpuesto = round($baseImponible * $porcentaje / 100, 2);

                  // Acumular montos de impuestos
                  $this->impuestos[$nombreImpuesto] = ($this->impuestos[$nombreImpuesto] ?? 0) + $montoImpuesto;

                  // Acumular base imponible por tipo de impuesto
                  $this->subtotales[$nombreImpuesto] = ($this->subtotales[$nombreImpuesto] ?? 0) + $baseImponible;


                  //sumamos en la nueva estructura
                  $encontrado =  false;
                  foreach ($this->resumenImpuestos  as $imp) {
                    if($imp['nombre'] === $nombreImpuesto){
                        $imp['base_imponible'] += $baseImponible;
                        $imp['valor_impuesto'] += $montoImpuesto;
                        $encontrado = true;
                        break;
                    }
                  }

                  if (!$encontrado) {
                    $this->resumenImpuestos[]=[
                        'nombre' => $nombreImpuesto,
                        'base_imponible' => $baseImponible,
                        'valor_impuesto' => $montoImpuesto
                    ];
                  }

                  // Sumar al total de impuestos del producto

                  $producto['total_impuesto'] += $montoImpuesto;
              }
          }

          // Calcular el total del carrito: subtotal - descuentos + impuestos totales
          $this->totalCart = round(
              $this->subTotSinImpuesto - $this->totalDscto + array_sum($this->impuestos),
              3
          );
          //dd($this->resumenImpuestos);

          // Debug con dd para revisar los resultados
        //    dd([
        //        'Subtotal sin impuestos' => $this->subTotSinImpuesto,
        //        'Descuento' => $this->totalDscto,
        //        'Subtotal sin impuestos específicos' => $this->subtotalSinImpuestos,
        //        'Subtotales por tipo de impuesto' => $this->subtotales,
        //        'Total impuestos' => $this->impuestos,
        //        'Total Carrito' => $this->totalCart,
        //        'Productos con total de impuesto' => $this->getContentCart(), // Aquí puedes ver el total de impuesto por producto
        //    ]);
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

           // $this->recalcularTotales();
        //    dd([
        //     'Subtotal sin impuestos' => $this->subTotSinImpuesto,
        //     'Descuento' => $this->totalDscto,
        //     'Subtotal sin impuestos específicos' => $this->subtotalSinImpuestos,
        //     'Subtotales por tipo de impuesto' => $this->subtotales,
        //     'Total impuestos' => $this->impuestos,
        //     'Total Carrito' => $this->totalCart,
        //     'Productos con total de impuesto' => $this->getContentCart(), // Aquí puedes ver el total de impuesto por producto
        // ]);



            $factura  =  Factura::create([
                //dd($this->secuencial, $this->claveAcceso), hasta aqui llega bien secuencial y clave
                'secuencial' => $this->secuencial,
                'codDoc' => '01',
                'claveAcceso' =>   $this->claveAcceso,
                'customer_id' =>  $this->customer_id,
                'user_id' => Auth()->user()->id,
                'subtotal' => $this->subTotSinImpuesto,  //ok
                'descuento' => $this->totalDscto,
                'total' => $this->totalCart,
                'formaPago' => '01'
            ]);
            //dd($factura);
            if ($factura) {
                $items =  $this->getContentCart();
               // dd($items);
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
                 // si producto es servicio no actualiza stock
                 $product = Product::find($item->id);
                 if ($product->es_servicio == 0){
                    $product->stock = $product->stock - $item->qty;
                    $product->save();
                 }


                 foreach($item['impuestos'] as $tax)
                 {
                    FacturaImpuesto::create([
                        'factura_id' => $factura->id,
                        'nombre_impuesto' => $tax['nombre'],
                        'codigo_impuesto' => $tax['codigo'],
                        'codigo_porcentaje' => $tax['codigo_porcentaje'],
                        'base_imponible' => ($item['price'] * $item['qty']) * (1 - ($item['descuento'] ?? 0) / 100),
                        'valor_impuesto' => round((($item['price'] * $item['qty']) * (1 - ($item['descuento'] ?? 0) / 100)) * ($tax['porcentaje'] / 100), 2),
                 ]);
                 }

               }
            }

            DB::commit();

            //dd($factura->impuestos);
            //ya no va : $this->subtotal15,   $this->totalImpuesto15,

            //********** crea xml , firma, envia y devuelve del sri  */
            $factura->xmlFactura(
                $factura->id, $tipeIDenti, $customer->businame, $customer->valueidenti, $customer->address,
                $this->subTotSinImpuesto, $this->totalDscto,
              $this->totalCart, $this->getContentCart(),
                $this->secuencial, $this->claveAcceso,$factura->impuestos
            );



        }
            catch (\Throwable $e) {
                FacadesDB::rollback();
                $this->noty('Error al guardar el pedido: ' . $e->getMessage(), 'noty', 'error');
            }

        // actualiza la factura con la fecha de autorizacion
        if (isset($factura)) {
            $factura->fechaAutorizacion = Carbon::now();
            $factura->numeroAutorizacion = $factura->claveAcceso;
            $factura->save();
        } else {
            $this->noty('Error: Factura no definida.', 'noty', 'error');
            return;
        }
        $this->clearCart();
        $this->resetUI();
        $url =  route('descargar-pdf', ['factura'=>$factura->id]);
        $this->noty('FACTURA GENERADA  CORRECTAMENTE !!!!!!');
        return redirect()->to($url);


      }


      public function cancelSale()
      {
        $this->clearCart();
        $this->resetUI();
        $this->noty('VENTA CANCELADA');
      }



}
