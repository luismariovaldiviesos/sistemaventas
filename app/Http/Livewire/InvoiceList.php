<?php

namespace App\Http\Livewire;

use App\Models\Factura;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class InvoiceList extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $fact_id='', $secuencial ='', $customer='', $directorio='', $estado;
    public $action = 'Listado', $componentName='LISTADO DE FACTURAS', $search, $form = false;
    private $pagination =20;
    protected $paginationTheme='tailwind';

    public function render()
    {
        if (strlen($this->search) > 0) {
            $info = Factura::where('secuencial', 'like', "%{$this->search}%")
                ->orWhereHas('customer', function ($query) {
                    $query->where('businame', 'like', "%{$this->search}%"); // Filtrar por nombre del cliente
                })
                ->where('numeroAutorizacion', '!=', null)
                ->orderBy('fechaAutorizacion', 'desc') // Ordenar por la fecha de creación descendente
                ->paginate($this->pagination);
        } else {
            $info = Factura::where('numeroAutorizacion', '!=', null)
                ->orderBy('fechaAutorizacion', 'desc') // Ordenar por la fecha de creación descendente
                ->paginate($this->pagination);
        }



        return view('livewire.listadofacturas.component', ['facturas' => $info])
            ->layout('layouts.theme.app');
    }
}
