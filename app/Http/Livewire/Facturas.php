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
            $this->iva12 = $this->getIva12();
            $this->iva0 = $this->getIva0();
            $this->totalImpuesto12 = $this->getImpuesto12();
            $this->totalIce = $this->getIce();
            $this->totalDscto = $this->getDscto();
            //dd($totalImpuesto12);




        if(strlen($this->searchProduct) > 0)
            $this->products = Product::where('name','like',"%{$this->searchProduct}%")
            ->orderBy('name','asc')->get()->take(5);
        else
        $this->products =  Product::orderBy('name','asc')->get()->take(5); //primeros 5 clientes



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
          'itemsCart', 'productIdSelected', 'productChangesSelected', 'productNameSelected', 'changesProduct');
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


           // dd('verificacion de cliente ',  $customer->businame, $customer->typeidenti, $tipeIDenti);



            //dd($this->getContentCart());
        // //    dd(
        // //     $tipeidenti, $customer->businame,$customer->valueidenti,$customer->address,
        // //     $this->subtotsinimpuesto,$this->totaldscto, $this->iva12,
        // //     $this->totalimpuesto12,  $this->totalcart, $this->getcontentcart(),
        // //     $this->secuencial, $this->claveacceso
        // // );

            $factura  =  Factura::create([
                //dd($this->secuencial, $this->claveAcceso), hasta aqui llega bien secuencial y clave
                'secuencial' => $this->secuencial,
                'codDoc' => '01',
                'claveAcceso' =>   $this->claveAcceso,
                'customer_id' =>  $this->customer_id,
                'user_id' => Auth()->user()->id,
                'subtotal' => $this->subTotSinImpuesto,
                'subtotal0' => $this->iva0,
                'subtotal12' => $this->iva12,
                'ice' => $this->totalIce,
                'descuento' => $this->totalDscto,
                'iva12' => $this->totalImpuesto12,
                'total' => $this->totalCart,
                'formaPago' => '01'
            ]);
           // dd($factura);



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


            // ******para imprimir ticket**********
            //if ($print) $this->PrintTicket($sale->id);

            //*********aqui metodo de xml**********
           // $startXml = microtime(true);
           // $this->pdf();
        //    dd($tipeIDenti, $customer->businame,$customer->valueidenti,$customer->address,
        //    $this->subTotSinImpuesto,$this->totalDscto, $this->iva12,
        //    $this->totalImpuesto12,$this->totalCart, $this->getContentCart(),
        //    $this->secuencial, $this->claveAcceso);

            $factura->xmlFactura(
                                 $factura->id, $tipeIDenti, $customer->businame,$customer->valueidenti,$customer->address,
                                  $this->subTotSinImpuesto,$this->totalDscto, $this->iva12,
                                  $this->totalImpuesto12,$this->totalCart, $this->getContentCart(),
                                  $this->secuencial, $this->claveAcceso
                              );
                              //$ruta_no_firmados =  base_path('storage/app/comprobantes/no_firmados/'.$facturaId.'.xml');

            //*********aqui metodo factura firma***********

            //$factura->firmarUltimaFactura();



           //dd('tiempo del sri enviado y regresado: ', $timeFirma);
            //*********aqui metodo pdf***********
            //$razonSocial,$usuarioSistema,$direccionMatriz,$dirrecioSucursal,$rucCliente,$numeroFact,
            //$fechaAuto,$numeroAutori,$claveAccesoPDF,$customer,$fechaEmision,$customer_id
            //$this->pdfFactura($razonSocial,$usuarioSistema,$direccionMatriz,$dirrecioSucursal,
             //   $customer_id,$factura->id,$fechaEmision,'1','calceacceo',$customerSelected,$fechaEmision,$customer_id);
        }
        catch (\Throwable $e) {
            FacadesDB::rollback();
            $this->noty('Error al guardar el pedido: ' . $e->getMessage(), 'noty', 'error');
        }

       // dd('tiempo del sri enviado y regresado: ', $timeFirma);
      //$this->pdfController($timeFirma);
      //COONSTRUIT LA RUTA PARA EL DPDF
      //$param = $customer->businame;
        $factura->fechaAutorizacion =  Carbon::now();
        $factura->numeroAutorizacion =  $factura->claveAcceso;
        $factura->save();
        $this->clearCart();
        $this->resetUI();
        //refrescar el navegador aqui
        // enviar mail aqui
        //dd('vemaos');
        $url =  route('descargar-pdf', ['factura'=>$factura->id]);
        $this->noty('FACTURA GENERADA  CORRECTAMENTE !!!!!!');
      //dd($factura->id);
      // Redirigir al navegador para descargar/visualizar el PDF
        return redirect()->to($url);


      }

    //   public function pdfController($timeFirma)
    // {
    //     // Limpiar cualquier salida previa
    //     ob_clean();
    //     flush();

    //     // Crear una nueva instancia de FPDF
    //     $pdf = new Fpdf();

    //     // Agregar una página al documento
    //     $pdf->AddPage();

    //     // Establecer el tipo de fuente y tamaño
    //     $pdf->SetFont('Arial', 'B', 16);

    //     // Agregar contenido al PDF
    //     $pdf->Cell(40, 10, 'Factura');
    //     $pdf->Ln(); // Salto de línea
    //     $pdf->Cell(40, 10, 'Razón Social: ' . $timeFirma);
    //     $pdf->Ln();
    //     $pdf->Cell(40, 10, 'Fecha: ' . now()->format('Y-m-d H:i:s'));

    //     // Generar el PDF y enviarlo al navegador
    //     return response()->stream(function() use ($pdf) {
    //         echo $pdf->Output('S'); // 'S' envía el PDF como un string de salida
    //     }, 200, [
    //         'Content-Type' => 'application/pdf',
    //         'Content-Disposition' => 'inline; filename="factura-' . $timeFirma . '.pdf"',
    //     ]);
    // }




}
