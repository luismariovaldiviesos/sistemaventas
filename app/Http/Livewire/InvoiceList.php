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
    // Si hay un término de búsqueda, se filtra por secuencial o cliente.
    if (strlen($this->search) > 0) {
        $info = Factura::where('secuencial', 'like', "%{$this->search}%")
            ->orWhereHas('customer', function ($query) {
                $query->where('businame', 'like', "%{$this->search}%"); // Filtrar por nombre del cliente
            })
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




    function retry(Factura $factura)  {

        dd('reenviar pdf de ', $factura->secuencial);

    }

    function downloadFiles(Factura $factura)  {

        dd('descargar archivos  de ', $factura->secuencial);

    }

    function delete(Factura $factura)  {

        dd('eliminar ', $factura->secuencial);

    }

    function show(Factura $factura)  {

        dd('ver ', $factura->secuencial);

    }



}
