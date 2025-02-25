<?php

namespace App\Http\Livewire;

use App\Http\Controllers\PdfController;
use App\Models\DeletedFactura;
use App\Models\Factura;
use App\Models\Product;
use App\Models\Setting;
use App\Models\XmlFile;
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

    public $fact_id='', $secuencial ='', $customer='', $directorio='', $estado, $annulmentDays;

    public  $factura_detalle ;
    public $action = 'Listado', $componentName='LISTADO DE FACTURAS', $search, $form = false;
    private $pagination =20;
    protected $paginationTheme='tailwind';


    public function mount()
    {
        $this->annulmentDays = Setting::first()?->annulment_days ?? 15;
    }

    public function render()
{
    // Si hay un término de búsqueda, se filtra por secuencial o cliente.
    if (strlen($this->search) > 0) {
        $info = Factura::whereNotNull('numeroAutorizacion') // Solo facturas aprobadas por el SRI
            ->where('codDoc', '01') // Filtrar solo facturas
            ->where(function ($query) {
                $query->where('secuencial', 'like', "%{$this->search}%")
                    ->orWhereHas('customer', function ($q) {
                        $q->where('businame', 'like', "%{$this->search}%");
                    })
                    ->orWhereDate('fechaAutorizacion', 'like', "%{$this->search}%");
            })
            ->orderBy('secuencial', 'desc')
            ->paginate($this->pagination);
    } else {
        // Si no hay término de búsqueda, solo traer facturas aprobadas
        $info = Factura::whereNotNull('numeroAutorizacion')
            ->where('codDoc', '01')
            ->orderBy('secuencial', 'desc')
            ->paginate($this->pagination);
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

    public  function confirmDelete(Factura $factura){
        //dd($factura->id);
        $this->dispatchBrowserEvent('swal:confirm',[
                'facturaId' => $factura->id
        ]);
    }

    public  function confirmNC(Factura $factura){
        //dd('vamos a emitir nc');
        $this->dispatchBrowserEvent('swal:nc',[
                'facturaId' => $factura->id
        ]);
    }

    protected $listeners =
        [
            'delete' => 'delete',
            'nc' => 'nc',

        ];

    function nc (Factura $factura){

        $factura->codDoc = '04'; //nota de credito
        $factura->save();
        $this->noty('Nota de Crédito emitida con éxito');
    }

    function delete(Factura $factura)
    {

        try {
            DB::transaction(function () use ($factura) {

                // Restaurar stock antes de eliminar la factura
               $this->restoreStockFromFacturas($factura);
                DeletedFactura::create([
                    'factura_id' => $factura->id,
                    'secuencial' => $factura->secuencial,
                    'cliente' => $factura->customer->businame,
                    'ruc_cliente' => $factura->customer->valueidenti,
                    'correo_cliente' => $factura->customer->email,
                    'fecha_emision' => $factura->created_at->toDateString(),
                    'clave_acceso' => $factura->claveAcceso,
                ]);

                // Soft delete de la factura
                $factura->delete();
                //eliminamos de xml_files
                XmlFile::where('factura_id', $factura->id)->delete();
            });

            // Notificar éxito
            $this->noty('Factura eliminada con éxito');
        } catch (\Throwable $th) {
            // Registrar error para depuración
            //\Log::error('Error al eliminar la factura: ' . $th->getMessage());
            // Notificar fallo
            $this->noty('No se pudo eliminar la factura. Revisa los logs para más detalles.' . $th->getMessage());
        }
    }


    public function resetUI()
    {
       $this->resetPage();
       $this->resetValidation();
       $this->reset('factura','search');

    }




   public  function show(Factura $factura)  {

        $this->factura_detalle = $factura->load('detalles');
        //dd($this->factura_detalle);

        $this->noty('','show_factura');
    }

}
