<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Customer;
use App\Models\DetalleFactura;
use App\Models\Factura;
use App\Models\Product;
use App\Models\User;
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


class Facturas extends Component
{

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
        $fact  = new Factura();
        $this->fechaFactura =  Carbon::now()->format('d-m-Y');
       $this->claveAcceso = $fact->claveAcceso();
       $this->secuencial = $fact->secuencial();
       //dd($this->secuencial);
       //dd($this->claveAcceso);
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
            else{
                $tipeIDenti = '04';  //ruc
            }



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
            $this->noty('FACTURA GENERADA, INICIAMOS XML');

            // ******para imprimir ticket**********
            //if ($print) $this->PrintTicket($sale->id);

            //*********aqui metodo de xml**********
            $startXml = microtime(true);
           // $this->pdf();
            $factura->xmlFactura(
                                  $tipeIDenti, $customer->businame,$customer->valueidenti,$customer->address,
                                  $this->subTotSinImpuesto,$this->totalDscto, $this->iva12,
                                  $this->totalImpuesto12,$this->totalCart, $this->getContentCart(),
                                  $this->secuencial, $this->claveAcceso
                              );

            $endXml = microtime(true);
            $timeXml = $endXml - $startXml;

            //*********aqui metodo factura firma***********
            $startFirma = microtime(true);
            $factura->firmarUltimaFactura();
            $endFirma = microtime(true);
            $timeFirma = $endFirma - $startFirma;



           dd('tiempo del sri enviado y regresado: ', $timeFirma);
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
        return  $this->pdfController();


      }


     public  function pdfController()  {
        //dd('hola pdf');
        ob_start();
        $pdf = new Fpdf();
        $pdf->SetCreator('ESTEBAN BAHAMONDE');
		$pdf->SetAuthor('ESTEBAN BAHAMONDE');
		$pdf->SetTitle('factura');
		$pdf->SetSubject('PDF');
		$pdf->SetKeywords('FPDF, PDF, cheque, impresion, guia');
		$pdf->SetMargins('10', '10', '10');
		$pdf->SetAutoPageBreak(TRUE);
		$pdf->SetFont('Arial', '', 7);
		$pdf->AddPage();
		//$pdf->Image('../img/logo.jpg',35,15,34);
		$pdf->SetXY(107, 10);
		$pdf->Cell(93, 84, '', 1, 1);
		$pdf->SetXY(10, 54);
		$pdf->Cell(93, 40, '', 1, 1);
		$pdf->SetXY(10, 98);
		$pdf->Cell(190, 12, '', 1, 1);
		$pdf->SetXY(10, 114);
		$pdf->Cell(190, 173, '', 0, 1);
		$pdf->SetFont('Arial', 'B', 6);$pdf->SetXY(10, 54);$pdf->Cell(93, 10, 'MI EMPRESA COMERCIAL S.A', 0 , 1, 'C');
		$pdf->SetFont('Arial', '', 6);$pdf->SetXY(10, 59);$pdf->Cell(93, 10, ' QUITO-ECUADOR', 0 , 1, 'L');
		$pdf->SetFont('Arial', 'B', 7);$pdf->SetXY(10, 68);$pdf->MultiCell(15, 4, 'Direccion Matriz', 0 , 'C');
		$pdf->SetFont('Arial', '', 6);$pdf->SetXY(25, 68);$pdf->MultiCell(78, 4, 'VIA QUITO', 0 , 'L');
		$pdf->SetFont('Arial', 'B', 7);$pdf->SetXY(10, 80);$pdf->MultiCell(15, 4, 'Direccion Sucursal', 0 , 'C');
		$pdf->SetFont('Arial', '', 6);$pdf->SetXY(25, 80);$pdf->MultiCell(78, 4, 'VIA QUITO', 0 , 'L');
		$pdf->SetFont('Arial', 'B', 9);$pdf->SetXY(107, 10);$pdf->Cell(40, 8, 'RUC: 1791345444001', 0 , 1);
		$pdf->SetFont('Arial', '', 9);$pdf->SetXY(107, 18);$pdf->Cell(93, 8, 'FACTURA', 0 , 1);
		$pdf->SetFont('Arial', '', 9);$pdf->SetXY(107, 26);$pdf->Cell(40, 8, 'No: 001-001-000397201', 0 , 1);
		$pdf->SetFont('Arial', '', 9);$pdf->SetXY(107, 32);$pdf->Cell(40, 10, 'FECHA AUTORIZACION: 2020-09-20', 0 , 1);
		$pdf->SetFont('Arial', 'B', 7);$pdf->SetXY(107, 42);$pdf->Cell(93, 8, 'NUMERO DE AUTORIZACION', 0 , 1, 'C');
		$pdf->SetFont('Arial', '', 7);$pdf->SetXY(107, 50);$pdf->Cell(93, 10, '2009202001179134544400110010010003971781234567815', 0 , 1, 'C');
		$pdf->SetFont('Arial', 'B', 7);$pdf->SetXY(107, 66);$pdf->Cell(93, 4, 'CLAVE DE ACCESO', 0 , 1, 'C');
		//new barCodeGenrator('2009202001179134544400110010010003971781234567815', 1, 'barra.gif', 455, 60, false);
		//$pdf->Image('barra.gif', 108, 70, 90, 10);
		$pdf->SetFont('Arial', 'B', 7);
		$pdf->SetXY(107, 80);
		$pdf->Cell(93, 5, '2009202001179134544400110010010003971781234567815', 0 , 1, 'C');
		$pdf->SetFont('Arial', 'B', 6);$pdf->SetXY(10, 98);$pdf->Cell(30, 3, 'RAZON SOCIAL', 0 , 1, 'C');
		$pdf->SetXY(10, 101);$pdf->Cell(30, 3, 'NOMBRES Y APELLIDOS', 0 , 0, 'C');
		$pdf->SetFont('Arial', '', 7);$pdf->SetXY(40, 98);$pdf->MultiCell(160, 3, 'ESTEBAN BAHAMONDE',0,'L');
		$pdf->SetFont('Arial', 'B', 6);$pdf->SetXY(10, 104);$pdf->Cell(30, 6, 'FECHA DE EMISION', 0 , 1, 'C');
		$pdf->SetFont('Arial', '', 7);$pdf->SetXY(40, 104);$pdf->Cell(100, 6, '2020-09-20', 0 , 1);
		$pdf->SetFont('Arial', 'B', 7);$pdf->SetXY(140, 104);$pdf->Cell(30, 6, 'IDENTIFICACION', 0 , 1);
		$pdf->SetFont('Arial', '', 7);$pdf->SetXY(170, 104);$pdf->Cell(30, 6, '9999999999', 0 , 1);
		$pdf->SetFont('Arial', 'B', 7);
		$pdf->SetXY(10, 114);$pdf->Cell(13, 6, false, 1 , 1);
		$pdf->SetXY(10, 114);$pdf->Cell(13, 3, 'Cod.', 0 , 1, 'C');
		$pdf->SetXY(10, 117);$pdf->Cell(13, 3, 'Principal', 0 , 1, 'C');
		$pdf->SetXY(23, 114);$pdf->Cell(13, 6, false, 1 , 1);
		$pdf->SetXY(23, 114);$pdf->Cell(13, 3, 'Cod.', 0 , 1, 'C');
		$pdf->SetXY(23, 117);$pdf->Cell(13, 3, 'Auxiliar', 0 , 1, 'C');
		$pdf->SetXY(36, 114);$pdf->Cell(13, 6, 'Cant', 1 , 1, 'C');
		$pdf->SetXY(49, 114);$pdf->Cell(110, 6, 'DESCRIPCION', 1 , 1, 'C');
		$pdf->SetXY(159, 114);$pdf->Cell(13, 6, false, 1 , 1);
		$pdf->SetXY(159, 114);$pdf->Cell(13, 3, 'Precio', 0 , 1, 'C');
		$pdf->SetXY(159, 117);$pdf->Cell(13, 3, 'Unitario', 0 , 1, 'C');
		$pdf->SetXY(172, 114);$pdf->Cell(15, 6, 'Descuento', 1 , 1, 'C');
		$pdf->SetXY(187, 114);$pdf->Cell(13, 6, false, 1 , 1);
		$pdf->SetXY(187, 114);$pdf->Cell(13, 3, 'Precio', 0 , 1, 'C');
		$pdf->SetXY(187, 117);$pdf->Cell(13, 3, 'Total', 0 , 1, 'C');
		//CABECERA KARDEX TOTALES
		$ejey = 120;
		$pdf->SetXY(10, $ejey);$pdf->Cell(13, 10, 'ASDFQ', 1 , 1, 'C');
		$pdf->SetXY(23, $ejey);$pdf->Cell(13, 10, '', 1 , 1, 'C');
		$pdf->SetXY(36, $ejey);$pdf->Cell(13, 10, '1.00', 1 , 1, 'C');$pdf->SetFont('Arial', 'B', 5);
		$pdf->SetXY(49, $ejey);$pdf->Cell(110, 10, '', 1 , 0);
		$pdf->SetXY(49, $ejey);$pdf->MultiCell(110, 5,'MESA','L');$pdf->SetFont('Arial', 'B', 7);
		$pdf->SetXY(159, $ejey);$pdf->Cell(13, 10, '10.00', 1 , 1, 'C');
		$pdf->SetXY(172, $ejey);$pdf->Cell(15, 10, '0.00', 1 , 1, 'C');
		$pdf->SetXY(187, $ejey);$pdf->Cell(13, 10, '10.00', 1 , 1, 'C');
		$ejey += 10;
		$ejey += 4;
		//KARDEX TOTALES
		$pdf->SetFont('Arial', 'B', 7);
		$pdf->SetXY(120, $ejey);$pdf->Cell(50, 4, 'SUBTOTAL', 1 , 1, 'L');
		$pdf->SetXY(120, $ejey+4);$pdf->Cell(50, 4, 'IVA 0%', 1 , 1, 'L');
		$pdf->SetXY(120, $ejey+8);$pdf->Cell(50, 4, 'IVA 12%', 1 , 1, 'L');
		$pdf->SetXY(120, $ejey+12);$pdf->Cell(50, 4, 'DESCUENTO 0.00%', 1 , 1, 'L');
		$pdf->SetXY(120, $ejey+16);$pdf->Cell(50, 4, 'VALOR TOTAL', 1 , 1, 'L');
		$pdf->SetXY(170, $ejey);$pdf->Cell(30, 4, '10.00', 1 , 1, 'R');//SUBTOTAL
		$pdf->SetXY(170, $ejey+4);$pdf->Cell(30, 4, '10.00', 1 , 1, 'R');//IVA 0
		$pdf->SetXY(170, $ejey+8);$pdf->Cell(30, 4, '0.00', 1 , 1, 'R');//VALOR IVA
		$pdf->SetXY(170, $ejey+12);$pdf->Cell(30, 4, '0.00', 1 , 1, 'R');//VALOR DESCUENTO
		$pdf->SetXY(170, $ejey+16);$pdf->Cell(30, 4, '0.00', 1 , 1, 'R');//VALOR CON IVA
		//INFO ADICIONAL
		$pdf->SetFont('Arial', 'B', 8);
		$pdf->SetXY(10, $ejey);$pdf->Cell(105, 6, 'INFORMACION ADICIONAL', 1 , 1, 'C');
		$pdf->SetFont('Arial', '', 7);
		$pdf->SetXY(10, $ejey+6);$pdf->Cell(20, 6, 'Email empresa:', 'L' , 1, 'L');
		$pdf->SetXY(10, $ejey+12);$pdf->Cell(20, 6, 'Email cliente:', 'L' , 1, 'L');
		$pdf->SetXY(10, $ejey+18);$pdf->Cell(20, 6, 'Telefono cliente:', 'L' , 1, 'L');
		$pdf->SetXY(30, $ejey+6);$pdf->Cell(85, 6, 'emailempresa@gmail.com', 'R' , 1, 'L');
		$pdf->SetXY(30, $ejey+12);$pdf->Cell(85, 6, 'ebahamondet@gmail.com', 'R' , 1, 'L');
		$pdf->SetXY(30, $ejey+18);$pdf->Cell(85, 6, '2421558', 'R' , 1, 'L');
		$pdf->SetXY(10, $ejey+24);$pdf->MultiCell(105, 10, 'Direccion cliente: av 10 de agosto', 'LRB', 'L');
		//FORMA DE PAGO
		$pdf->SetFont('Arial', 'B', 7);$pdf->SetXY(10, $ejey+39);$pdf->Cell(75, 6, 'Forma de pago', 1 , 1, 'C');
		$pdf->SetFont('Arial', 'B', 7);$pdf->SetXY(85, $ejey+39);$pdf->Cell(30, 6, 'Valor', 1 , 1, 'C');
		$pdf->SetFont('Arial', '', 7);$pdf->SetXY(10, $ejey+45);$pdf->Cell(75, 6, 'SIN UTILIZACION DEL SISTEMA FINANCIERON', 'LRB' , 1, 'L');
		$pdf->SetFont('Arial', '', 7);$pdf->SetXY(85, $ejey+45);$pdf->Cell(30, 6, '152.00', 'RB' , 1, 'L');
        $pdf->Output();
        exit; // Usa die() o exit para evitar cualquier otra salida
        ob_end_flush();
      }


}
