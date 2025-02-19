<?php

namespace App\Http\Livewire;

use App\Models\DeletedFactura;
use App\Models\Factura;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class DeletedList extends Component
{
    use WithPagination;
    use WithFileUploads;
    public $fact_id='', $secuencial ='', $customer='', $ruc_cliente='', $correo_cliente='', $estado;
    public $fecha_emision='',$clave_acceso='';
    public $action = 'Listado', $componentName='FACTURAS ANULADAS', $search, $form = false;
    private $pagination =20;
    protected $paginationTheme='tailwind';


    public function render()
    {
        //$facturas =  Factura::onlyTrashed()->orderBy('fechaAutorizacion', 'desc')->paginate($this->pagination);
        //dd($facturas);
        if (strlen($this->search) > 0) {
            $facturas = DeletedFactura::where(function ($query) {
                    $query->where('secuencial', 'like', "%{$this->search}%")
                        ->orWhere('cliente', 'like', "%{$this->search}%")
                        ->orWhereDate('fecha_emision', 'like', "%{$this->search}%");
                })
                ->orderBy('fecha_emision', 'desc')
                ->paginate($this->pagination);
        } else {
            // Si no hay búsqueda, traer todas las facturas eliminadas
            $facturas = DeletedFactura::orderBy('fecha_emision', 'desc')
                ->paginate($this->pagination);
        }
        return view('livewire.deletedlist.component', ['facturas' => $facturas])->layout('layouts.theme.app');;
    }

    public function noty($msg, $eventName= 'noty', )
{
    $this->dispatchBrowserEvent($eventName, ['msg' => $msg, 'type' => 'success']);
}



    public function deletesri(DeletedFactura $factura){

        $factura->estado = 'ANULADO';
        $factura->save();
        $this->noty('Factura marcada  eliminada correctamente');
    }
}
