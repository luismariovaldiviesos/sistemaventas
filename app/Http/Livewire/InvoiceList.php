<?php

namespace App\Http\Livewire;

use App\Http\Controllers\PdfController;
use App\Models\DeletedFactura;
use App\Models\Factura;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Traits\CartTrait;
use Illuminate\Support\Facades\DB;

class InvoiceList extends Component
{
    use WithPagination;
    use WithFileUploads;
    use CartTrait;

    public $fact_id='', $secuencial ='', $customer='', $directorio='', $estado;
    public $action = 'Listado', $componentName='LISTADO DE FACTURAS', $search, $form = false;
    private $pagination =20;
    protected $paginationTheme='tailwind';

    public function render()
{
    // Si hay un término de búsqueda, se filtra por secuencial o cliente.
    if (strlen($this->search) > 0) {
        $info = Factura::where('secuencial', 'like', "%{$this->search}%")
            ->orWhereHas('customer', function ($query) {
                $query->where('businame', 'like', "%{$this->search}%"); // Filtrar por nombre del cliente
            })->orWhereDate('fechaAutorizacion','like', "%{$this->search}%" ) // Filtrar por fecha exacta
            ->where('numeroAutorizacion', '!=', null)
            ->orderBy('fechaAutorizacion', 'desc') // Ordenar por la fecha de autorización descendente
            ->paginate($this->pagination);  // Paginación
    } else {
        // Si no hay término de búsqueda, se cargan todas las facturas con número de autorización.
        $info = Factura::where('numeroAutorizacion', '!=', null)
            ->orderBy('fechaAutorizacion', 'desc') // Ordenar por la fecha de autorización descendente
            ->paginate($this->pagination); // Paginación
    }

    // Devuelve las facturas al componente de Livewire para la vista.
    return view('livewire.listadofacturas.component', ['facturas' => $info])
        ->layout('layouts.theme.app');
}

public function noty($msg, $eventName= 'noty', )
{
    $this->dispatchBrowserEvent($eventName, ['msg' => $msg, 'type' => 'success']);
}





    function retry(Factura $factura)  {

        //dd('reenviar pdf de ', $factura->secuencial);
        $pdfcontroller  =  New PdfController();
        $pdfcontroller->enviarFacturea($factura);
        $this->noty('PDF FACTURA REENVIADA  CORRECTAMENTE !!!!!!');

    }

    function downloadFiles(Factura $factura)  {

        $pdf_name =  $factura->customer->businame.'_'.$factura->secuencial;
        $pdfPath = base_path('storage/app/comprobantes/pdfs/'.$pdf_name.'.pdf');
        return response()->download($pdfPath);

    }

    function delete(Factura $factura)  {

        try {
            $this->restoreStockFromFacturas($factura);// metotdo que esta enn  el trait CartTrait
            DB::transaction(function () use ($factura) {
                DeletedFactura::create([
                    'factura_id' => $factura->id,
                'secuencial' => $factura->secuencial,
                'cliente'    => $factura->customer->businame,
                'ruc_cliente' => $factura->customer->valueidenti,
                'correo_cliente' => $factura->customer->email,
                'fecha_emision' => $factura->created_at,
                'clave_acceso' => $factura->claveAcceso
                ]);
                $factura->delete();  //soft cascade

            });
        } catch (\Throwable $th) {
            $this->noty('NO SE PUDO ELIMINAR LA FACTURA !!!!!!');
        }
        $this->noty('FACTURA ELIMINADO CON EXITO  !!!!!!');

    }

    function show(Factura $factura)  {

        dd('ver ', $factura->secuencial);

    }



}
